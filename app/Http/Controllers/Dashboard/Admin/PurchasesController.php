<?php

namespace App\Http\Controllers\Dashboard\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PurchasesExport;
use App\Mail\PurchaseMail;
use App\Models\PaymentPurchase;
use App\Models\Product;
use App\Models\Currency;
use App\Models\Unit;
use App\Models\ProductVariant;
use App\Models\product_warehouse;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Warehouse;
use App\utils\helpers;
use Carbon\Carbon;
use Twilio\Rest\Client as Client_Twilio;
use DB;
use PDF;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\PurchaseAdded;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class PurchasesController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:عرض المشتريات', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة المشتريات', ['only' => ['store']]);
        $this->middleware('permission:تعديل المشتريات', ['only' => ['update']]);
        $this->middleware('permission:حذف المشتريات', ['only' => ['destroy','deleteSelected']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
    }
    


    public function index(request $request)
    {
        try {
            $purchases = Purchase::with('facture', 'provider', 'warehouse')
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

            foreach ($purchases as $purchase) {
                $purchase['due'] = $purchase->GrandTotal - $purchase->paid_amount;
            }
            
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
            $providers  = provider::where('deleted_at', '=', null)->get(['id', 'name']);
            $products   = product::where('deleted_at', '=', null)->get(['id', 'name']);

            $trashed = false;

            return view('dashboard.purchase.index', compact('purchases', 'warehouses', 'providers', 'products', 'trashed'))
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
            $purchase = Purchase::findOrFail($id);
            $currency = Currency::first();
            return view('dashboard.purchase.show', compact('purchase','currency'));
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
            
            $purchase = Purchase::create([
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
                $purchaseDetails = PurchaseDetail::create([
                    'purchase_id'        => $purchase->id,
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
                if ($purchase->status == "received") 
                {
                    $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                    ->where('warehouse_id', $purchase->warehouse_id)
                    ->where('product_id', $product->id)
                    ->first();
                    if ($unit && $product_warehouse)
                    {
                        if ($unit->operator == '/') 
                        {
                            $product_warehouse->qte += $request->quantity[$i] / $unit->operator_value;
                        } 
                        else 
                        {
                            $product_warehouse->qte += $request->quantity[$i] * $unit->operator_value;
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
                            $product_warehouse->qte += $request->quantity[$i] / $unit->operator_value;
                        } 
                        else 
                        {
                            $product_warehouse->qte += $request->quantity[$i] * $unit->operator_value;
                        }
                        $product_warehouse->save();
                    }
                }
                $totalAmount += ($product->cost * $request->quantity[$i]);
                $i++;
            }
            
            $subTotal            = $totalAmount; //إجمالى سعر المنتجات
            $totalAfterDiscount  = $subTotal - $purchase->discount; //إجمالى السعر بعد الخصم
            $taxNet              = $totalAfterDiscount * ($purchase->tax_rate / 100); //إجمالى سعر الضريبة
            $totalAfterTax       = $totalAfterDiscount + $taxNet; //إجمالى السعر بعد الضريبة
            $grandTotal          = $totalAfterTax + $purchase->shipping; //إجمالى السعر النهائى بعد إضافة الشحن
            
            $purchase->update([
                'TaxNet'     => $taxNet,
                'GrandTotal' => $grandTotal,
            ]);

            // send notification
            $users = User::where('id', '!=', Auth::user()->id)->get();
            Notification::send($users, new PurchaseAdded($purchase->id));

            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function update(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'update', Purchase::class);

        request()->validate([
            'warehouse_id' => 'required',
            'supplier_id' => 'required',
        ]);

        \DB::transaction(function () use ($request, $id) {
            $role = Auth::user()->roles()->first();
            $view_records = Role::findOrFail($role->id)->inRole('record_view');
            $current_Purchase = Purchase::findOrFail($id);

            // Check If User Has Permission view All Records
            if (!$view_records) {
                // Check If User->id === Purchase->id
                $this->authorizeForUser($request->user('api'), 'check_record', $current_Purchase);
            }

            $old_purchase_details = PurchaseDetail::where('purchase_id', $id)->get();
            $new_purchase_details = $request['details'];
            $length = sizeof($new_purchase_details);

            // Get Ids for new Details
            $new_products_id = [];
            foreach ($new_purchase_details as $new_detail) {
                $new_products_id[] = $new_detail['id'];
            }

            // Init Data with old Parametre
            $old_products_id = [];
            foreach ($old_purchase_details as $key => $value) {
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
                    if ($current_Purchase->statut == "received") {

                        if ($value['product_variant_id'] !== null) {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Purchase->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->where('product_variant_id', $value['product_variant_id'])
                                ->first();

                            if ($unit && $product_warehouse) {
                                if ($unit->operator == '/') {
                                    $product_warehouse->qte -= $value['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse->qte -= $value['quantity'] * $unit->operator_value;
                                }

                                $product_warehouse->save();
                            }

                        } else {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Purchase->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->first();

                            if ($unit && $product_warehouse) {
                                if ($unit->operator == '/') {
                                    $product_warehouse->qte -= $value['quantity'] / $unit->operator_value;
                                } else {
                                    $product_warehouse->qte -= $value['quantity'] * $unit->operator_value;
                                }

                                $product_warehouse->save();
                            }
                        }
                    }

                    // Delete Detail
                    if (!in_array($old_products_id[$key], $new_products_id)) {
                        $PurchaseDetail = PurchaseDetail::findOrFail($value->id);
                        $PurchaseDetail->delete();
                    }
                }

            }

            // Update Data with New request
            foreach ($new_purchase_details as $key => $prod_detail) {
                
                if($prod_detail['no_unit'] !== 0){
                    $unit_prod = Unit::where('id', $prod_detail['purchase_unit_id'])->first();

                    if ($request['statut'] == "received") {

                        if ($prod_detail['product_variant_id'] !== null) {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $request->warehouse_id)
                                ->where('product_id', $prod_detail['product_id'])
                                ->where('product_variant_id', $prod_detail['product_variant_id'])
                                ->first();

                            if ($unit_prod && $product_warehouse) {
                                if ($unit_prod->operator == '/') {
                                    $product_warehouse->qte += $prod_detail['quantity'] / $unit_prod->operator_value;
                                } else {
                                    $product_warehouse->qte += $prod_detail['quantity'] * $unit_prod->operator_value;
                                }

                                $product_warehouse->save();
                            }

                        } else {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $request->warehouse_id)
                                ->where('product_id', $prod_detail['product_id'])
                                ->first();

                            if ($unit_prod && $product_warehouse) {
                                if ($unit_prod->operator == '/') {
                                    $product_warehouse->qte += $prod_detail['quantity'] / $unit_prod->operator_value;
                                } else {
                                    $product_warehouse->qte += $prod_detail['quantity'] * $unit_prod->operator_value;
                                }

                                $product_warehouse->save();
                            }
                        }

                    }

                    $orderDetails['purchase_id'] = $id;
                    $orderDetails['cost'] = $prod_detail['Unit_cost'];
                    $orderDetails['purchase_unit_id'] = $prod_detail['purchase_unit_id'];
                    $orderDetails['TaxNet'] = $prod_detail['tax_percent'];
                    $orderDetails['tax_method'] = $prod_detail['tax_method'];
                    $orderDetails['discount'] = $prod_detail['discount'];
                    $orderDetails['discount_method'] = $prod_detail['discount_Method'];
                    $orderDetails['quantity'] = $prod_detail['quantity'];
                    $orderDetails['product_id'] = $prod_detail['product_id'];
                    $orderDetails['product_variant_id'] = $prod_detail['product_variant_id'];
                    $orderDetails['total'] = $prod_detail['subtotal'];

                    if (!in_array($prod_detail['id'], $old_products_id)) {
                        PurchaseDetail::Create($orderDetails);
                    } else {
                        PurchaseDetail::where('id', $prod_detail['id'])->update($orderDetails);
                    }
                }
            }

            $due = $request['GrandTotal'] - $current_Purchase->paid_amount;
            if ($due === 0.0 || $due < 0.0) {
                $payment_statut = 'paid';
            } else if ($due != $request['GrandTotal']) {
                $payment_statut = 'partial';
            } else if ($due == $request['GrandTotal']) {
                $payment_statut = 'unpaid';
            }

            $current_Purchase->update([
                'date' => $request['date'],
                'provider_id' => $request['supplier_id'],
                'warehouse_id' => $request['warehouse_id'],
                'notes' => $request['notes'],
                'tax_rate' => $request['tax_rate'],
                'TaxNet' => $request['TaxNet'],
                'discount' => $request['discount'],
                'shipping' => $request['shipping'],
                'statut' => $request['statut'],
                'GrandTotal' => $request['GrandTotal'],
                'payment_statut' => $payment_statut,
            ]);

        }, 10);

        return response()->json(['success' => true, 'message' => 'Purchase Updated !!']);

    }

    

    public function destroy(Request $request)
    {
        try {
            // $related_table = realed_model::where('category_id', $request->id)->pluck('category_id');
            // if($related_table->count() == 0) 
            // { 

                $current_purchase     = Purchase::findOrFail($request->id);
                $old_purchase_details = PurchaseDetail::where('purchase_id', $request->id)->get();

                foreach ($old_purchase_details as $key => $value) 
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
                    
                    if ($current_purchase->status == "received") 
                    {
                        if ($value['product_variant_id'] !== null) 
                        {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $current_Purchase->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->where('product_variant_id', $value['product_variant_id'])
                            ->first();

                            if ($unit && $product_warehouse) 
                            {
                                if ($unit->operator == '/') 
                                {
                                    $product_warehouse->qte -= $value['quantity'] / $unit->operator_value;
                                } 
                                else 
                                {
                                    $product_warehouse->qte -= $value['quantity'] * $unit->operator_value;
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
                        else 
                        {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $current_purchase->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->first();
                            
                            if ($unit && $product_warehouse) 
                            {
                                if ($unit->operator == '/') 
                                {
                                    $product_warehouse->qte -= $value['quantity'] / $unit->operator_value;
                                } 
                                else 
                                {
                                    $product_warehouse->qte -= $value['quantity'] * $unit->operator_value;
                                }

                                $product_warehouse->save();
                            }
                        }
                    }
                }

                $current_purchase->purchase_details()->delete();

                $current_purchase->update([
                    'deleted_at' => Carbon::now(),
                ]);

                $payment_purchase = PaymentPurchase::where('purchase_id', $request->id)->update([
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
            foreach ($delete_selected_id as $purchase_id) 
            {
                // $related_table = realed_model::where('purchase_id', $selected_id)->pluck('purchase_id');
                // if($related_table->count() == 0)
                // {
                    $current_purchase     = Purchase::findOrFail($purchase_id);
                    $old_purchase_details = PurchaseDetail::where('purchase_id', $purchase_id)->get();
                    
                    foreach ($old_purchase_details as $key => $value) 
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
                        
                        if ($current_purchase->status == "received")
                        {
                            if ($value['product_variant_id'] !== null) 
                            {
                                $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_purchase->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->where('product_variant_id', $value['product_variant_id'])
                                ->first();
        
                                if ($unit && $product_warehouse) 
                                {
                                    if ($unit->operator == '/') 
                                    {
                                        $product_warehouse->qte -= $value['quantity'] / $unit->operator_value;
                                    } 
                                    else 
                                    {
                                        $product_warehouse->qte -= $value['quantity'] * $unit->operator_value;
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
                            else 
                            {
                                $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_purchase->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->first();
        
                                if ($unit && $product_warehouse)
                                {
                                    if ($unit->operator == '/')
                                    {
                                        $product_warehouse->qte -= $value['quantity'] / $unit->operator_value;
                                    }
                                    else
                                    {
                                        $product_warehouse->qte -= $value['quantity'] * $unit->operator_value;
                                    }
                                    
                                    $product_warehouse->save();
                                }
                            }
                        }
                    }
        
                    $current_purchase->purchase_details()->delete();  

                    $current_purchase->update([
                        'deleted_at' => Carbon::now(),
                    ]);
                    
                    $payment_purchase = PaymentPurchase::where('purchase_id', $purchase_id)->update([
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
            $purchase = Purchase::findOrFail($id);
            $currency = Currency::first();
            return view('dashboard.purchase.show', compact('purchase','currency'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function getNumberOrder()
    {
        try {
            $last = DB::table('purchases')->latest('id')->first();
            if ($last) {
                $item  = $last->Ref;
                $nwMsg = explode("_", $item);
                $inMsg = $nwMsg[1] + 1;
                $code  = $nwMsg[0] . '_' . $inMsg;
            } else {
                $code = 'PR_1111';
            }
            return $code;
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
