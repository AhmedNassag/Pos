<?php

namespace App\Http\Controllers\Dashboard\Admin;
use Twilio\Rest\Client as Client_Twilio;
use App\Exports\SalesExport;
use App\Mail\SaleMail;
use App\Models\Client;
use App\Models\Currency;
use App\Models\Unit;
use App\Models\PaymentSale;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\product_warehouse;
use App\Models\Quotation;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Setting;
use App\Models\PosSetting;
use App\Models\Warehouse;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Stripe;
use App\Models\PaymentWithCreditCard;
use DB;
use PDF;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\StockAlert;
use App\Notifications\SaleAdded;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class SalesController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:عرض المبيعات', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة المبيعات', ['only' => ['store']]);
        $this->middleware('permission:تعديل المبيعات', ['only' => ['update']]);
        $this->middleware('permission:حذف المبيعات', ['only' => ['destroy','deleteSelected']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
    }
    


    public function index(request $request)
    {
        try {
            $sales = Sale::with('facture', 'client', 'warehouse')
            ->where('deleted_at', '=', null)
            ->when($request->Ref != null,function ($q) use($request){
                return $q->where('Ref',$request->Ref);
            })
            ->when($request->date != null,function ($q) use($request){
                return $q->where('date',$request->date);
            })
            ->when($request->client_id != null,function ($q) use($request){
                return $q->where('client_id',$request->client_id);
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

            foreach ($sales as $sale) {
                $sale['due'] = $sale->GrandTotal - $sale->paid_amount;
            }
            
            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
            $clients    = client::where('deleted_at', '=', null)->get(['id', 'name']);

            $trashed = false;

            return view('dashboard.sale.index', compact('sales', 'warehouses', 'clients', 'trashed'))
            ->with([
                'Ref'            => $request->Ref,
                'date'           => $request->date,
                'client_id'      => $request->client_id,
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
            $sale     = Sale::findOrFail($id);
            $currency = Currency::first();
            return view('dashboard.sale.show', compact('sale','currency'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'date'         => 'required',
                'client_id'    => 'required',
                'warehouse_id' => 'required',
                'tax_rate'     => 'required',
                'discount'     => 'required',
                'shipping'     => 'required',
                'product_id'   => 'required',
                'quantity'     => 'required',
            ]);
            
            //check if quantity is available
            $i = 0;
            foreach($request->product_id as $product_id)
            {
                $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                ->where('warehouse_id', $request->warehouse_id)
                ->where('product_id', $product_id)
                ->first();

                if($request->quantity[$i] > $product_warehouse->qte)
                {
                    session()->flash('notAvailableQuantity');
                    return redirect()->back();
                }
                $i++;
            }

            $sale = Sale::create([
                'Ref'            => $this->getNumberOrder(),
                'date'           => $request->date,
                'client_id'      => $request->client_id,
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
                $unit    = Unit::where('id', $product->unit_sale_id)->first();
                $saleDetails = SaleDetail::create([
                    'sale_id'            => $sale->id,
                    'quantity'           => $request->quantity[$i],
                    'price'              => $product->price,
                    'sale_unit_id'       => $product->unit_sale_id,
                    'TaxNet'             => $product->TaxNet,
                    'tax_method'         => $product->tax_method,
                    'discount'           => 0,
                    'discount_method'    => 2,
                    'product_id'         => $product->id,
                    'product_variant_id' => null,
                    'date'               => now(),
                    'total'              => $product->price * $request->quantity[$i],
                ]);
                if ($sale->status == "received") 
                {
                    $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                    ->where('warehouse_id', $sale->warehouse_id)
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

                        //send notification
                        if($product_warehouse->qte <= $product->stock_alert)
                        {
                            $users = User::get();
                            Notification::send($users, new StockAlert());
                        }
                    }
                }
                $totalAmount += ($product->price * $request->quantity[$i]);
                $i++;
            }
            
            $subTotal            = $totalAmount; //إجمالى سعر المنتجات
            $totalAfterDiscount  = $subTotal - $sale->discount; //إجمالى السعر بعد الخصم
            $taxNet              = $totalAfterDiscount * ($sale->tax_rate / 100); //إجمالى سعر الضريبة
            $totalAfterTax       = $totalAfterDiscount + $taxNet; //إجمالى السعر بعد الضريبة
            $grandTotal          = $totalAfterTax + $sale->shipping; //إجمالى السعر النهائى بعد إضافة الشحن
            
            $sale->update([
                'TaxNet'     => $taxNet,
                'GrandTotal' => $grandTotal,
            ]);

            // send notification
            $users = User::where('id', '!=', Auth::user()->id)->get();
            Notification::send($users, new SaleAdded($sale->id));

            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    //------------- UPDATE SALE -----------

    public function update(Request $request, $id)
    {
        $this->authorizeForUser($request->user('api'), 'update', Sale::class);

        request()->validate([
            'warehouse_id' => 'required',
            'client_id' => 'required',
        ]);

        \DB::transaction(function () use ($request, $id) {

            $role = Auth::user()->roles()->first();
            $view_records = Role::findOrFail($role->id)->inRole('record_view');
            $current_Sale = Sale::findOrFail($id);

            // Check If User Has Permission view All Records
            if (!$view_records) {
                // Check If User->id === Sale->id
                $this->authorizeForUser($request->user('api'), 'check_record', $current_Sale);
            }
            $old_sale_details = SaleDetail::where('sale_id', $id)->get();
            $new_sale_details = $request['details'];
            $length = sizeof($new_sale_details);

            // Get Ids for new Details
            $new_products_id = [];
            foreach ($new_sale_details as $new_detail) {
                $new_products_id[] = $new_detail['id'];
            }

            // Init Data with old Parametre
            $old_products_id = [];
            foreach ($old_sale_details as $key => $value) {
                $old_products_id[] = $value->id;
                
                //check if detail has sale_unit_id Or Null
                if($value['sale_unit_id'] !== null){
                    $old_unit = Unit::where('id', $value['sale_unit_id'])->first();
                }else{
                    $product_unit_sale_id = Product::with('unitSale')
                    ->where('id', $value['product_id'])
                    ->first();
                    $old_unit = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
                }

                if($value['sale_unit_id'] !== null){
                    if ($current_Sale->statut == "completed") {

                        if ($value['product_variant_id'] !== null) {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Sale->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->where('product_variant_id', $value['product_variant_id'])
                                ->first();

                            if ($product_warehouse) {
                                if ($old_unit->operator == '/') {
                                    $product_warehouse->qte += $value['quantity'] / $old_unit->operator_value;
                                } else {
                                    $product_warehouse->qte += $value['quantity'] * $old_unit->operator_value;
                                }
                                $product_warehouse->save();
                            }

                        } else {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $current_Sale->warehouse_id)
                                ->where('product_id', $value['product_id'])
                                ->first();
                            if ($product_warehouse) {
                                if ($old_unit->operator == '/') {
                                    $product_warehouse->qte += $value['quantity'] / $old_unit->operator_value;
                                } else {
                                    $product_warehouse->qte += $value['quantity'] * $old_unit->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        }
                    }
                    // Delete Detail
                    if (!in_array($old_products_id[$key], $new_products_id)) {
                        $SaleDetail = SaleDetail::findOrFail($value->id);
                        $SaleDetail->delete();
                    }
                }
            }

            // Update Data with New request
            foreach ($new_sale_details as $prd => $prod_detail) {
                
                if($prod_detail['no_unit'] !== 0){
                    $unit_prod = Unit::where('id', $prod_detail['sale_unit_id'])->first();

                    if ($request['statut'] == "completed") {

                        if ($prod_detail['product_variant_id'] !== null) {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $request->warehouse_id)
                                ->where('product_id', $prod_detail['product_id'])
                                ->where('product_variant_id', $prod_detail['product_variant_id'])
                                ->first();

                            if ($product_warehouse) {
                                if ($unit_prod->operator == '/') {
                                    $product_warehouse->qte -= $prod_detail['quantity'] / $unit_prod->operator_value;
                                } else {
                                    $product_warehouse->qte -= $prod_detail['quantity'] * $unit_prod->operator_value;
                                }
                                $product_warehouse->save();
                            }

                        } else {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                                ->where('warehouse_id', $request->warehouse_id)
                                ->where('product_id', $prod_detail['product_id'])
                                ->first();

                            if ($product_warehouse) {
                                if ($unit_prod->operator == '/') {
                                    $product_warehouse->qte -= $prod_detail['quantity'] / $unit_prod->operator_value;
                                } else {
                                    $product_warehouse->qte -= $prod_detail['quantity'] * $unit_prod->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        }

                    }

                    $orderDetails['sale_id'] = $id;
                    $orderDetails['price'] = $prod_detail['Unit_price'];
                    $orderDetails['sale_unit_id'] = $prod_detail['sale_unit_id'];
                    $orderDetails['TaxNet'] = $prod_detail['tax_percent'];
                    $orderDetails['tax_method'] = $prod_detail['tax_method'];
                    $orderDetails['discount'] = $prod_detail['discount'];
                    $orderDetails['discount_method'] = $prod_detail['discount_Method'];
                    $orderDetails['quantity'] = $prod_detail['quantity'];
                    $orderDetails['product_id'] = $prod_detail['product_id'];
                    $orderDetails['product_variant_id'] = $prod_detail['product_variant_id'];
                    $orderDetails['total'] = $prod_detail['subtotal'];
                    
                    if (!in_array($prod_detail['id'], $old_products_id)) {
                        $orderDetails['date'] = Carbon::now();
                        $orderDetails['sale_unit_id'] = $unit_prod ? $unit_prod->id : Null;
                        SaleDetail::Create($orderDetails);
                    } else {
                        SaleDetail::where('id', $prod_detail['id'])->update($orderDetails);
                    }
                }
            }

            $due = $request['GrandTotal'] - $current_Sale->paid_amount;
            if ($due === 0.0 || $due < 0.0) {
                $payment_statut = 'paid';
            } else if ($due != $request['GrandTotal']) {
                $payment_statut = 'partial';
            } else if ($due == $request['GrandTotal']) {
                $payment_statut = 'unpaid';
            }

            $current_Sale->update([
                'date' => $request['date'],
                'client_id' => $request['client_id'],
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
            $current_Sale     = Sale::findOrFail($request->id);
            $old_sale_details = SaleDetail::where('sale_id', $request->id)->get();
 
            foreach ($old_sale_details as $key => $value) 
            {    
                //check if detail has sale_unit_id Or Null
                if($value['sale_unit_id'] !== null)
                {
                    $old_unit = Unit::where('id', $value['sale_unit_id'])->first();
                }
                else
                {
                    $product_unit_sale_id = Product::with('unitSale')->where('id', $value['product_id'])->first();
                    $old_unit             = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
                }

                if ($current_Sale->status == "received") 
                {
                    $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                    ->where('warehouse_id', $current_Sale->warehouse_id)
                    ->where('product_id', $value['product_id'])
                    ->first();

                    if ($product_warehouse) 
                    {
                        if ($old_unit->operator == '/') 
                        {
                            $product_warehouse->qte += $value['quantity'] / $old_unit->operator_value;
                        } 
                        else 
                        {
                            $product_warehouse->qte += $value['quantity'] * $old_unit->operator_value;
                        }
                        $product_warehouse->save();
                    }
                }
                  
            }

            $current_Sale->details()->delete();

            $current_Sale->update([
                'deleted_at' => Carbon::now(),
            ]);

            $Payment_Sale_data = PaymentSale::where('sale_id', $request->id)->update([
                'deleted_at' => Carbon::now(),
            ]);

            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function deleteSelected(Request $request)
    {
        try {
            $delete_selected_id = explode(",", $request->delete_selected_id);
            foreach ($delete_selected_id as $sale_id) 
            {
                // $related_table = realed_model::where('purchase_id', $selected_id)->pluck('purchase_id');
                // if($related_table->count() == 0)
                // {
                    $current_Sale = Sale::findOrFail($sale_id);
                    $old_sale_details = SaleDetail::where('sale_id', $sale_id)->get();
                    
                    foreach ($old_sale_details as $key => $value) 
                    {
                        //check if detail has sale_unit_id Or Null
                        if($value['sale_unit_id'] !== null)
                        {
                            $old_unit = Unit::where('id', $value['sale_unit_id'])->first();
                        }
                        else
                        {
                            $product_unit_sale_id = Product::with('unitSale')->where('id', $value['product_id'])->first();
                            $old_unit             = Unit::where('id', $product_unit_sale_id['unitSale']->id)->first();
                        }
        
                        if ($current_Sale->status == "received") 
                        {
                            $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                            ->where('warehouse_id', $current_Sale->warehouse_id)
                            ->where('product_id', $value['product_id'])
                            ->first();
                            if ($product_warehouse) 
                            {
                                if ($old_unit->operator == '/') 
                                {
                                    $product_warehouse->qte += $value['quantity'] / $old_unit->operator_value;
                                }
                                else
                                {
                                    $product_warehouse->qte += $value['quantity'] * $old_unit->operator_value;
                                }
                                $product_warehouse->save();
                            }
                        }
                    }
                    $current_Sale->details()->delete();

                    $current_Sale->update([
                        'deleted_at' => Carbon::now(),
                    ]);
    
                    $Payment_Sale_data = PaymentSale::where('sale_id', $sale_id)->update([
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
            $sale     = Sale::findOrFail($id);
            $currency = Currency::first();
            return view('dashboard.sale.show', compact('sale','currency'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function stockAlert($notification_id)
    {
        try {
            $notification = NotificationModel::findOrFail($notification_id);
            $notification->update([
                'read_at' => now(),
            ]);
            return redirect()->route('reports.stockAlert');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function getNumberOrder()
    {
        try {
            $last = DB::table('sales')->latest('id')->first();
            if ($last)
            {
                $item  = $last->Ref;
                $nwMsg = explode("_", $item);
                $inMsg = $nwMsg[1] + 1;
                $code  = $nwMsg[0] . '_' . $inMsg;
            }
            else
            {
                $code = 'SL_1111';
            }
            return $code;
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
