<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Exports\Purchase_Return;
use App\Mail\ReturnMail;
use App\Models\Currency;
use App\Models\Unit;
use App\Models\PaymentPurchaseReturns;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\product_warehouse;
use App\Models\Provider;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetails;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Warehouse;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Twilio\Rest\Client as Client_Twilio;
use DB;
use PDF;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PurchaseReturnAdded;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class PurchasesReturnController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:عرض مرتجع المشتريات', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة مرتجع المشتريات', ['only' => ['store']]);
        $this->middleware('permission:تعديل مرتجع المشتريات', ['only' => ['update']]);
        $this->middleware('permission:حذف مرتجع المشتريات', ['only' => ['destroy','deleteSelected']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
    }
    


    public function index(request $request)
    {
        try {
            $purchasesReturns = PurchaseReturn::with('facture', 'provider', 'warehouse')
            ->where('deleted_at', '=', null)
            ->when($request->Ref != null,function ($q) use($request){
                return $q->where('Ref',$request->Ref);
            })
            ->when($request->date != null,function ($q) use($request){
                return $q->where('date',$request->date);
            })
            ->when($request->provider_id != null,function ($q) use($request){
                return $q->where('provider_id',$request->provider_id);
            })
            ->when($request->warehouse_id != null,function ($q) use($request){
                return $q->where('warehouse_id',$request->warehouse_id);
            })
            ->when($request->GrandTotal != null,function ($q) use($request){
                return $q->where('GrandTotal',$request->GrandTotal);
            })
            ->when($request->paid_amount != null,function ($q) use($request){
                return $q->where('paid_amount',$request->paid_amount);
            })
            ->when($request->payment_status != null,function ($q) use($request){
                return $q->where('payment_status',$request->payment_status);
            })
            ->orderBy('id', 'desc')
            ->get();

            foreach ($purchasesReturns as $purchaseReturn) {
                $purchaseReturn['due'] = $purchaseReturn->GrandTotal - $purchaseReturn->paid_amount;
            }
            
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
            $providers  = provider::where('deleted_at', '=', null)->get(['id', 'name']);
            $products   = product::where('deleted_at', '=', null)->get(['id', 'name']);

            $trashed = false;

            return view('dashboard.purchaseReturn.index', compact('purchasesReturns',  'warehouses', 'providers', 'products', 'trashed'))
            ->with([
                'Ref'            => $request->Ref,
                'date'           => $request->date,
                'provider_id'    => $request->provider_id,
                'warehouse_id'   => $request->warehouse_id,
                'GrandTotal'     => $request->GrandTotal,
                'paid_amount'    => $request->paid_amount,
                'payment_status' => $request->payment_status,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function show($id)
    {
        try {
            $purchaseReturn = PurchaseReturn::findOrFail($id);
            $currency       = Currency::first();
            return view('dashboard.purchaseReturn.show', compact('purchaseReturn','currency'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'date'         => 'required',
                'provider_id'  => 'required',
                'warehouse_id' => 'required',
                'tax_rate'     => 'required',
                'discount'     => 'required',
                'shipping'     => 'required',
                'product_id'   => 'required',
                'quantity'     => 'required',
            ]);
            
            $purchaseReturn = PurchaseReturn::create([
                'Ref'            => $this->getNumberOrder(),
                'date'           => $request->date,
                'provider_id'    => $request->provider_id,
                'warehouse_id'   => $request->warehouse_id,
                'tax_rate'       => $request->tax_rate,
                'discount'       => $request->discount,
                'shipping'       => $request->shipping,
                'notes'          => $request->notes,
                'status'         => 'received',
                'payment_status' => 'unpaid',
                'user_id'        => Auth::user()->id,
            ]);

            $i = 0;
            $totalAmount = 0;
            foreach($request->product_id as $product_id)
            {
                $product = Product::findOrFail($product_id);
                $unit    = Unit::where('id', $product->unit_purchase_id)->first();
                $purchaseReturnDetails = PurchaseReturnDetails::create([
                    'purchase_return_id' => $purchaseReturn->id,
                    'quantity'           => $request->quantity[$i],
                    'cost'               => $product->cost,
                    'purchase_unit_id'   => $product->unit_purchase_id,
                    'TaxNet'             => $product->TaxNet,
                    'tax_method'         => $product->tax_method,
                    'discount'           => 0,
                    'discount_method'    => 2,
                    'product_id'         => $product->id,
                    'product_variant_id' => null,
                    'total'              => $product->cost * $request->quantity[$i],
                ]);
                if ($purchaseReturn->status == "received") 
                {
                    $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                    ->where('warehouse_id', $purchaseReturn->warehouse_id)
                    ->where('product_id', $product->id)
                    ->first();
                    if ($unit && $product_warehouse)
                    {
                        if ($unit->operator == '/') 
                        {
                            $product_warehouse->qte -= $request->quantity[$i] / $unit->operator_value;
                        } 
                        else 
                        {
                            $product_warehouse->qte -= $request->quantity[$i] * $unit->operator_value;
                        }
                        $product_warehouse->save();
                    }
                    else
                    {
                        $product_warehouse = product_warehouse::create([
                            'warehouse_id' => $purchase->warehouse_id,
                            'product_id'   => $product->id,
                        ]);
                        if ($unit->operator == '/') 
                        {
                            $product_warehouse->qte -= $request->quantity[$i] / $unit->operator_value;
                        } 
                        else 
                        {
                            $product_warehouse->qte -= $request->quantity[$i] * $unit->operator_value;
                        }
                        $product_warehouse->save();
                    }
                }
                $totalAmount += ($product->cost * $request->quantity[$i]);
                $i++;
            }

            $subTotal            = $totalAmount; //إجمالى سعر المنتجات
            $totalAfterDiscount  = $subTotal - $purchaseReturn->discount; //إجمالى السعر بعد الخصم
            $taxNet              = $totalAfterDiscount * ($purchaseReturn->tax_rate / 100); //إجمالى سعر الضريبة
            $totalAfterTax       = $totalAfterDiscount + $taxNet; //إجمالى السعر بعد الضريبة
            $grandTotal          = $totalAfterTax + $purchaseReturn->shipping; //إجمالى السعر النهائى بعد إضافة الشحن
            
            $purchaseReturn->update([
                'TaxNet'     => $taxNet,
                'GrandTotal' => $grandTotal,
            ]);

            // send notification
            $users = User::where('id', '!=', Auth::user()->id)->get();
            Notification::send($users, new PurchaseReturnAdded($purchaseReturn->id));

            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    //------------ Update Purchase Return --------------\\

    public function update(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'update', PurchaseReturn::class);

        request()->validate([
            'warehouse_id' => 'required',
            'supplier_id' => 'required',
        ]);

        \DB::transaction(function () use ($request, $id) {
            $role = Auth::user()->roles()->first();
            $view_records = Role::findOrFail($role->id)->inRole('record_view');
            $current_PurchaseReturn = PurchaseReturn::findOrFail($id);

            // Check If User Has Permission view All Records
            if (!$view_records) {
                // Check If User->id === PurchaseReturn->id
                $this->authorizeForUser($request->user('api'), 'check_record', $current_PurchaseReturn);
            }

            $old_Return_Details = PurchaseReturnDetails::where('purchase_return_id', $id)->get();
            $New_Return_Details = $request['details'];
            $length = sizeof($New_Return_Details);

            // Get Ids details
            $new_products_id = [];
            foreach ($New_Return_Details as $new_detail) {
                $new_products_id[] = $new_detail['id'];
            }

            // Init Data with old Parametre
            $old_products_id = [];
            foreach ($old_Return_Details as $key => $value) {
                $old_products_id[] = $value->id;

                 //check if detail has purchase_unit_id Or Null
                 if($value['purchase_unit_id'] !== null){
                    $unit = Unit::where('id', $value['purchase_unit_id'])->first();
                }else{
                    $product_unit_purchase_id = Product::with('unitPurchase')
                    ->where('id', $value['product_id'])
                    ->first();
                    $unit = Unit::where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
                }

                if($value['purchase_unit_id'] !== null){
                    if ($current_PurchaseReturn->statut == "completed") {
                        if ($value['product_variant_id'] !== null) {

                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_PurchaseReturn->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->where('product_variant_id', $value['product_variant_id'])
                                ->first();

                            if ($unit && $product_warehouse) {
                                if ($unit->operator == '/') {
                                    $product_warehouse->qte += $value['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse->qte += $value['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse->save();
                            }

                        } else {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_PurchaseReturn->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->first();

                            if ($unit && $product_warehouse) {
                                if ($unit->operator == '/') {
                                    $product_warehouse->qte += $value['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse->qte += $value['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        }
                    }

                    // Delete Detail
                    if (!in_array($old_products_id[$key], $new_products_id)) {
                        $PurchaseReturnDetails = PurchaseReturnDetails::findOrFail($value->id);
                        $PurchaseReturnDetails->delete();
                    }
                }

            }

            // Update Data with New request
            foreach ($New_Return_Details as $key => $product_detail) {

                if($product_detail['no_unit'] !== 0){
                    $unit_prod = Unit::where('id', $product_detail['purchase_unit_id'])->first();

                    if ($request->statut == "completed") {
                        if ($product_detail['product_variant_id'] !== null) {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $request->warehouse_id)
                                ->where('product_id', $product_detail['product_id'])
                                ->where('product_variant_id', $product_detail['product_variant_id'])
                                ->first();

                            if ($unit_prod && $product_warehouse) {
                                if ($unit_prod->operator == '/') {
                                    $product_warehouse->qte -= $product_detail['quantity'] / $unit_prod->operator_value;
                                } else {
                                    $product_warehouse->qte -= $product_detail['quantity'] * $unit_prod->operator_value;
                                }
                                $product_warehouse->save();
                            }

                        } else {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $request->warehouse_id)
                                ->where('product_id', $product_detail['product_id'])
                                ->first();

                            if ($unit_prod && $product_warehouse) {
                                if ($unit_prod->operator == '/') {
                                    $product_warehouse->qte -= $product_detail['quantity'] / $unit_prod->operator_value;
                                } else {
                                    $product_warehouse->qte -= $product_detail['quantity'] * $unit_prod->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        }
                    }

                    $orderDetails['purchase_return_id'] = $id;
                    $orderDetails['cost'] = $product_detail['Unit_cost'];
                    $orderDetails['purchase_unit_id'] = $product_detail['purchase_unit_id'];
                    $orderDetails['TaxNet'] = $product_detail['tax_percent'];
                    $orderDetails['tax_method'] = $product_detail['tax_method'];
                    $orderDetails['discount'] = $product_detail['discount'];
                    $orderDetails['discount_method'] = $product_detail['discount_Method'];
                    $orderDetails['quantity'] = $product_detail['quantity'];
                    $orderDetails['product_id'] = $product_detail['product_id'];
                    $orderDetails['product_variant_id'] = $product_detail['product_variant_id'];
                    $orderDetails['total'] = $product_detail['subtotal'];

                    if (!in_array($product_detail['id'], $old_products_id)) {
                        PurchaseReturnDetails::Create($orderDetails);
                    } else {
                        PurchaseReturnDetails::where('id', $product_detail['id'])->update($orderDetails);
                    }
                }

            }

            $due = $request['GrandTotal'] - $current_PurchaseReturn->paid_amount;
            if ($due === 0.0 || $due < 0.0) {
                $payment_statut = 'paid';
            } else if ($due != $request['GrandTotal']) {
                $payment_statut = 'partial';
            } else if ($due == $request['GrandTotal']) {
                $payment_statut = 'unpaid';
            }

            $current_PurchaseReturn->update([
                'date' => $request['date'],
                'provider_id' => $request['supplier_id'],
                'warehouse_id' => $request['warehouse_id'],
                'notes' => $request['notes'],
                'statut' => $request['statut'],
                'tax_rate' => $request['tax_rate'],
                'TaxNet' => $request['TaxNet'],
                'discount' => $request['discount'],
                'shipping' => $request['shipping'],
                'GrandTotal' => $request['GrandTotal'],
                'payment_statut' => $payment_statut,
            ]);

        }, 10);

        return response()->json(['success' => true]);
    }

    

    public function destroy(Request $request)
    {
        try {
            // $related_table = realed_model::where('category_id', $request->id)->pluck('category_id');
            // if($related_table->count() == 0) 
            // { 

                $current_PurchaseReturn     = PurchaseReturn::findOrFail($request->id);
                $old_purchaseReturn_details = PurchaseReturnDetails::where('purchase_return_id', $request->id)->get();

                foreach ($old_purchaseReturn_details as $key => $value) 
                {
                    //check if detail has purchase_unit_id Or Null
                    if($value['purchase_unit_id'] !== null)
                    {
                        $unit = Unit::where('id', $value['purchase_unit_id'])->first();
                    }
                    else
                    {
                        $product_unit_purchase_id = Product::with('unitPurchase')
                        ->where('id', $value['product_id'])
                        ->first();
                        $unit = Unit::where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
                    }

                    if ($current_PurchaseReturn->status == "received") 
                    {
                        if ($value['product_variant_id'] !== null) 
                        {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $current_PurchaseReturn->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();

                            if ($unit && $product_warehouse) 
                            {
                                if ($unit->operator == '/') 
                                {
                                    $product_warehouse->qte += $value['quantity'] / $unit->operator_value;
                                } 
                                else 
                                {
                                    $product_warehouse->qte += $value['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        }
                        else 
                        {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $current_PurchaseReturn->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->first();

                            if ($unit && $product_warehouse) 
                            {
                                if ($unit->operator == '/') 
                                {
                                    $product_warehouse->qte += $value['quantity'] / $unit->operator_value;
                                } 
                                else 
                                {
                                    $product_warehouse->qte += $value['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        }
                    }
                }

                $current_PurchaseReturn->details()->delete();

                $current_PurchaseReturn->update([
                    'deleted_at' => Carbon::now(),
                ]);

                $payment_purchase_return = PaymentPurchaseReturns::where('purchase_return_id', $request->id)->update([
                    'deleted_at' => Carbon::now(),
                ]);

                session()->flash('success');
                return redirect()->back();
            // } else {
                // session()->flash('canNotDeleted');
                // return redirect()->back();
            // }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function deleteSelected(Request $request)
    {
        try {
            $delete_selected_id = explode(",", $request->delete_selected_id);
            foreach ($delete_selected_id as $purchaseReturn_id) 
            {
                // $related_table = realed_model::where('purchase_id', $selected_id)->pluck('purchase_id');
                // if($related_table->count() == 0)
                // {
                $current_purchaseReturn = PurchaseReturn::findOrFail($purchaseReturn_id);
                $old_Return_Details     = PurchaseReturnDetails::where('purchase_return_id', $purchaseReturn_id)->get();

                foreach ($old_Return_Details as $key => $value) 
                {
                    //check if detail has purchase_unit_id Or Null
                    if($value['purchase_unit_id'] !== null)
                    {
                       $unit = Unit::where('id', $value['purchase_unit_id'])->first();
                    }
                    else
                    {
                       $product_unit_purchase_id = Product::with('unitPurchase')
                       ->where('id', $value['product_id'])
                       ->first();
                       $unit = Unit::where('id', $product_unit_purchase_id['unitPurchase']->id)->first();
                    }
   
                    if ($current_purchaseReturn->status == "received") 
                    {
                        if ($value['product_variant_id'] !== null) 
                        {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $current_purchaseReturn->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();
   
                            if ($unit && $product_warehouse) 
                            {
                                if ($unit->operator == '/') 
                                {
                                   $product_warehouse->qte += $value['quantity'] / $unit->operator_value;
                                } 
                                else 
                                {
                                   $product_warehouse->qte += $value['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        } 
                        else 
                        {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $current_purchaseReturn->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->first();
   
                            if ($unit && $product_warehouse) 
                            {
                                if ($unit->operator == '/') 
                                {
                                   $product_warehouse->qte += $value['quantity'] / $unit->operator_value;
                                }
                                else 
                                {
                                    $product_warehouse->qte += $value['quantity'] * $unit->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        }
                    }   
                }

                $current_purchaseReturn->details()->delete();
                
                $current_purchaseReturn->update([
                   'deleted_at' => Carbon::now(),
                ]);

                $payment_purchase_return = PaymentPurchaseReturns::where('purchase_return_id', $purchaseReturn_id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
            // } 
                // else 
                // {
                //     session()->flash('canNotDeleted');
                //     return redirect()->back();
                // }
            }
                
            session()->flash('success');
            return redirect()->back();

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function showNotification($id,$notification_id)
    {
        try {
            $notification = NotificationModel::findOrFail($notification_id);
            $notification->update([
                'read_at' => now(),
            ]);
            $purchaseReturn = PurchaseReturn::findOrFail($id);
            $currency       = Currency::first();
            return view('dashboard.purchaseReturn.show', compact('purchaseReturn','currency'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function getNumberOrder()
    {
        try {
            $last = DB::table('purchase_returns')->latest('id')->first();
            if ($last) 
            {
                $item  = $last->Ref;
                $nwMsg = explode("_", $item);
                $inMsg = $nwMsg[1] + 1;
                $code  = $nwMsg[0] . '_' . $inMsg;
            } 
            else 
            {
                $code = 'RT_1111';
            }
            return $code;
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
