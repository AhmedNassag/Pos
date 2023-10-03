<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Models\Client;
use App\Models\Expense;
use App\Models\PaymentPurchase;
use App\Models\PaymentPurchaseReturns;
use App\Models\PaymentSale;
use App\Models\PaymentSaleReturns;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\product_warehouse;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturn;
use App\Models\Quotation;
use App\Models\Role;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SaleReturn;
use App\Models\User;
use App\Models\Warehouse;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Http\Controllers\Controller;

class ReportController extends BaseController
{
    public function salesPayments(Request $request)
    {
        try {
            $salesPayments = PaymentSale::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->when($request->warehouse_id != null,function ($q) use($request){
                return $q->whereRelation('sale','warehouse_id','like',$request->warehouse_id);
            })
            ->when($request->client_id != null,function ($q) use($request){
                return $q->whereRelation('sale','client_id',$request->client_id);
            })
            // ->orderBy('id', 'desc')
            ->get();

            $warehouses = Warehouse::where('deleted_at', null)->get(['id', 'name']);
            $clients    = Client::where('deleted_at', null)->get(['id', 'name']);

            return view('dashboard.report.salesPayments', compact('salesPayments', 'warehouses', 'clients'))
            ->with([
                'from_date'    => $request->from_date,
                'to_date'      => $request->to_date,
                'warehouse_id' => $request->warehouse_id,
                'client_id'    => $request->client_id,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    
    public function purchasesPayments(Request $request)
    {
        try {
            $purchasesPayments = PaymentPurchase::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->when($request->warehouse_id != null,function ($q) use($request){
                return $q->whereRelation('purchase','warehouse_id','like',$request->warehouse_id);
            })
            ->when($request->provider_id != null,function ($q) use($request){
                return $q->whereRelation('purchase','provider_id',$request->provider_id);
            })
            // ->orderBy('id', 'desc')
            ->get();

            $warehouses = Warehouse::where('deleted_at', null)->get(['id', 'name']);
            $providers  = Provider::where('deleted_at', null)->get(['id', 'name']);

            return view('dashboard.report.purchasesPayments', compact('purchasesPayments', 'warehouses', 'providers'))
            ->with([
                'from_date'    => $request->from_date,
                'to_date'      => $request->to_date,
                'warehouse_id' => $request->warehouse_id,
                'provider_id'  => $request->provider_id,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function salesReturnsPayments(Request $request)
    {
        try {
            $salesReturnsPayments = PaymentSaleReturns::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->when($request->warehouse_id != null,function ($q) use($request){
                return $q->whereRelation('SaleReturn','warehouse_id','like',$request->warehouse_id);
            })
            ->when($request->client_id != null,function ($q) use($request){
                return $q->whereRelation('SaleReturn','client_id',$request->client_id);
            })
            // ->orderBy('id', 'desc')
            ->get();

            $warehouses = Warehouse::where('deleted_at', null)->get(['id', 'name']);
            $clients    = Client::where('deleted_at', null)->get(['id', 'name']);

            return view('dashboard.report.salesReturnsPayments', compact('salesReturnsPayments', 'warehouses', 'clients'))
            ->with([
                'from_date'    => $request->from_date,
                'to_date'      => $request->to_date,
                'warehouse_id' => $request->warehouse_id,
                'client_id'    => $request->client_id,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }


    
    public function purchasesReturnsPayments(Request $request)
    {
        try {
            $purchasesReturnsPayments = PaymentPurchaseReturns::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->when($request->warehouse_id != null,function ($q) use($request){
                return $q->whereRelation('purchaseReturn','warehouse_id','like',$request->warehouse_id);
            })
            ->when($request->provider_id != null,function ($q) use($request){
                return $q->whereRelation('purchaseReturn','provider_id',$request->provider_id);
            })
            // ->orderBy('id', 'desc')
            ->get();

            $warehouses = Warehouse::where('deleted_at', null)->get(['id', 'name']);
            $providers  = Provider::where('deleted_at', null)->get(['id', 'name']);

            return view('dashboard.report.purchasesReturnsPayments', compact('purchasesReturnsPayments', 'warehouses', 'providers'))
            ->with([
                'from_date'    => $request->from_date,
                'to_date'      => $request->to_date,
                'warehouse_id' => $request->warehouse_id,
                'provider_id'  => $request->provider_id,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function statistics(Request $request)
    {
        try {
            $data['sales'] = Sale::where('deleted_at', '=', null)
            /*->whereBetween('date', array($request->from, $request->to))*/
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->select(DB::raw('SUM(GrandTotal) AS sum'), DB::raw("count(*) as nmbr"))
            ->first();

            $data['purchases'] = Purchase::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->select(DB::raw('SUM(GrandTotal) AS sum'), DB::raw("count(*) as nmbr"))
            ->first();

            $data['returns_sales'] = SaleReturn::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->select(DB::raw('SUM(GrandTotal) AS sum'), DB::raw("count(*) as nmbr"))
            ->first();

            $data['returns_purchases'] = PurchaseReturn::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->select(DB::raw('SUM(GrandTotal) AS sum'), DB::raw("count(*) as nmbr"))
            ->first();

            $data['paiement_sales'] = PaymentSale::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->select(DB::raw('SUM(montant) AS sum'))
            ->first();

            $data['PaymentSaleReturns'] = PaymentSaleReturns::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->select(DB::raw('SUM(montant) AS sum'))
            ->first();

            $data['PaymentPurchaseReturns'] = PaymentPurchaseReturns::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->select(DB::raw('SUM(montant) AS sum'))
            ->first();

            $data['paiement_purchases'] = PaymentPurchase::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->select(DB::raw('SUM(montant) AS sum'))
            ->first();

            $data['expenses'] = Expense::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->select(DB::raw('SUM(amount) AS sum'))
            ->first();

            $data['return_sales'] = SaleReturn::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->get(DB::raw('SUM(GrandTotal)  As sum'))
            ->first()->sum;
            
            $data['today_purchases'] = Purchase::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->get(DB::raw('SUM(GrandTotal)  As sum'))
            ->first()->sum;

            $data['purchases_return'] = PurchaseReturn::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->get(DB::raw('SUM(GrandTotal)  As sum'))
            ->first()->sum;
            

            //calcul profit
            $product_sale_data = Sale::join('sale_details' , 'sales.id', '=', 'sale_details.sale_id')
            ->select(DB::raw('sale_details.product_id , sum(sale_details.quantity) as sold_qty , sum(sale_details.total) as sold_amount'))
            ->where('sales.deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('sales.date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('sales.date','<=',$request->to_date);
            })
            ->groupBy('sale_details.product_id')
            ->get();

            $product_revenue = 0;
            $product_cost = 0;
            $profit = 0;

            foreach($product_sale_data as $key => $product_sale)
            {
                $product_purchase_data = PurchaseDetail::where('product_id' , $product_sale->product_id)->get();

                $purchased_qty = 0;
                $purchased_amount = 0;   
                $sold_qty = $product_sale->sold_qty;
                $product_revenue += $product_sale->sold_amount;

                foreach ($product_purchase_data as $key => $product_purchase)
                {
                    $purchased_qty += $product_purchase->quantity;
                    $purchased_amount += $product_purchase->total;
                    if($purchased_qty >= $sold_qty)
                    {
                        $qty_diff = $purchased_qty - $sold_qty;
                        $unit_cost = $product_purchase->total / $product_purchase->quantity;
                        $purchased_amount -= ($qty_diff * $unit_cost);
                        break;
                    }
                }

                $product_cost += $purchased_amount;
            }

            $data['revenue'] = $data['sales']['sum'] - $data['return_sales'];
            $data['profit'] = $data['revenue'] + $data['purchases_return'] - $product_cost;
            // - $item['expenses']['sum'];
            $data['payment_received'] = $data['paiement_sales']['sum'] + $data['PaymentPurchaseReturns']['sum'];
            $data['payment_sent'] = $data['paiement_purchases']['sum'] + $data['PaymentSaleReturns']['sum'] + $data['expenses']['sum'];
            $data['paiement_net'] = $data['payment_received'] - $data['payment_sent'];

            // return $data;
            return view('dashboard.report.statistics', compact('data'))
            ->with([
                'from_date'    => $request->from_date,
                'to_date'      => $request->to_date,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function stockAlert(request $request)
    {
        try {
            $products_alerts = product_warehouse::join('products', 'product_warehouse.product_id', '=', 'products.id')
            ->whereRaw('qte <= stock_alert')
            ->when($request->product_id != null,function ($q) use($request){
                return $q->where('product_id',$request->product_id);
            })
            ->when($request->warehouse_id != null,function ($q) use($request){
                return $q->where('warehouse_id',$request->warehouse_id);
            })
            ->when($request->code != null,function ($q) use($request){
                return $q->whereRelation('product', 'code','like',$request->code.'%');
            })
            ->get();

            $products   = Product::all();
            $warehouses = Warehouse::all();
            
            return view('dashboard.report.stockAlerts', compact('products_alerts', 'products', 'warehouses'))
            ->with([
                'warehouse_id' => $request->warehouse_id,
                'product_id'   => $request->product_id,
                'code'         => $request->code,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function Warehouses(Request $request)
    {
        try{
            $data['sales'] = Sale::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->when($request->warehouse_id != null,function ($q) use($request){
                return $q->where('warehouse_id',$request->warehouse_id);
            })
            ->get();

            $data['purchases'] = Purchase::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->when($request->warehouse_id != null,function ($q) use($request){
                return $q->where('warehouse_id',$request->warehouse_id);
            })
            ->get();

            $data['returnPurchases'] = PurchaseReturn::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->when($request->warehouse_id != null,function ($q) use($request){
                return $q->where('warehouse_id',$request->warehouse_id);
            })
            ->get();

            $data['returnSales'] = SaleReturn::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->when($request->warehouse_id != null,function ($q) use($request){
                return $q->where('warehouse_id',$request->warehouse_id);
            })
            ->get();

            $data['expenses'] = Expense::where('deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->when($request->warehouse_id != null,function ($q) use($request){
                return $q->where('warehouse_id',$request->warehouse_id);
            })
            ->get();

            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);

            return view('dashboard.report.warehouses', compact('data', 'warehouses'))
            ->with([
                'from_date'    => $request->from_date,
                'to_date'      => $request->to_date,
                'warehouse_id' => $request->warehouse_id,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function sales(Request $request)
    {
        try {
            $sales = Sale::select('sales.*')
            ->with('facture', 'client', 'warehouse')
            ->join('clients', 'sales.client_id', '=', 'clients.id')
            ->where('sales.deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->when($request->payment_status != null,function ($q) use($request){
                return $q->where('payment_status',$request->payment_status);
            })
            ->when($request->client_id != null,function ($q) use($request){
                return $q->where('client_id',$request->client_id);
            })
            ->get();
            
            $clients = client::where('deleted_at', '=', null)->get(['id', 'name']);

            return view('dashboard.report.sales', compact('sales', 'clients'))
            ->with([
                'from_date'      => $request->from_date,
                'to_date'        => $request->to_date,
                'payment_status' => $request->payment_status,
                'client_id'      => $request->client_id,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function purchases(Request $request)
    {
        try {
            $purchases = Purchase::select('purchases.*')
            ->with('facture', 'provider', 'warehouse')
            ->join('providers', 'purchases.provider_id', '=', 'providers.id')
            ->where('purchases.deleted_at', '=', null)
            ->when($request->from_date != null,function ($q) use($request){
                return $q->where('date','>=',$request->from_date);
            })
            ->when($request->to_date != null,function ($q) use($request){
                return $q->where('date','<=',$request->to_date);
            })
            ->when($request->payment_status != null,function ($q) use($request){
                return $q->where('payment_status',$request->payment_status);
            })
            ->when($request->provider_id != null,function ($q) use($request){
                return $q->where('provider_id',$request->provider_id);
            })
            ->get();

            $providers = provider::where('deleted_at', '=', null)->get(['id', 'name']);

            return view('dashboard.report.purchases', compact('purchases', 'providers'))
            ->with([
                'from_date'      => $request->from_date,
                'to_date'        => $request->to_date,
                'payment_status' => $request->payment_status,
                'provider_id'    => $request->provider_id,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function clients(Request $request)
    {
        try {
            $clientsData = Client::where('deleted_at', '=', null)
            ->when($request->client_id != null,function ($q) use($request){
                return $q->where('id',$request->client_id);
            })
            ->get();

            foreach ($clientsData as $client) 
            {
                $item['total_sales'] = DB::table('sales')
                ->where('deleted_at', '=', null)
                ->where('client_id', $client->id)
                ->count();

                $item['total_amount'] = DB::table('sales')
                ->where('deleted_at', '=', null)
                ->where('client_id', $client->id)
                ->sum('GrandTotal');

                $item['total_paid'] = DB::table('sales')
                ->leftjoin('payment_sales', 'sales.id', '=', 'payment_sales.sale_id')
                ->where('sales.deleted_at', '=', null)
                ->where('sales.client_id', $client->id)
                ->sum('payment_sales.montant');

                $item['due']        = $item['total_amount'] - $item['total_paid'];
                $item['name']       = $client->name;
                $item['phone']      = $client->phone;
                $item['address']    = $client->adresse;
                $item['created_at'] = $client->created_at;
                $item['code']       = $client->code;
                $item['id']         = $client->id;
                
                $data[] = $item;
            }

            $clients = Client::where('deleted_at', '=', null)->get();

            return view('dashboard.report.clients', compact('data', 'clients'))
            ->with([
                'client_id' => $request->client_id,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }

    }



    public function clientDetails(Request $request, $id)
    {
        try {

            $client = Client::where('deleted_at', '=', null)->findOrFail($id);

            $data['id'] = $id;

            $data['total_sales'] = DB::table('sales')
            ->where('deleted_at', '=', null)
            ->where('client_id', $id)
            ->count();

            $data['total_amount'] = DB::table('sales')
            ->where('deleted_at', '=', null)
            ->where('client_id', $id)
            ->sum('GrandTotal');

            $data['total_paid'] = DB::table('sales')
            ->where('sales.deleted_at', '=', null)
            ->join('payment_sales', 'sales.id', '=', 'payment_sales.sale_id')
            ->where('payment_sales.deleted_at', '=', null)
            ->where('sales.client_id', $client->id)
            ->sum('payment_sales.montant');

            $data['due'] = $data['total_amount'] - $data['total_paid'];

            //client sales
            $data['sales'] = Sale::where('deleted_at', '=', null)
            ->where('client_id', $id)
            ->get();

            //client sales payments
            $data['salesPayments'] = DB::table('payment_sales')
            ->where('payment_sales.deleted_at', '=', null)
            ->join('sales', 'payment_sales.sale_id', '=', 'sales.id')
            ->where('sales.client_id', $id)
            ->select(
                'payment_sales.date', 'payment_sales.Ref AS Ref', 'sales.Ref AS Sale_Ref',
                'payment_sales.Reglement', 'payment_sales.montant'
            )->get();

            //client salesReturns
            $data['salesReturns'] = SaleReturn::where('deleted_at', '=', null)
            ->where('client_id', $request->id)
            ->get();

            //client salesReturns payments
            $data['salesReturnsPayments'] = DB::table('payment_sale_returns')
            ->where('payment_sale_returns.deleted_at', '=', null)
            ->join('sale_returns', 'payment_sale_returns.sale_return_id', '=', 'sale_returns.id')
            ->where('sale_returns.client_id', $id)
            ->select(
                'payment_sale_returns.date', 'payment_sale_returns.Ref AS Ref', 'sale_returns.Ref AS SaleReturn_Ref',
                'payment_sale_returns.Reglement', 'payment_sale_returns.montant'
            )
            ->get();

            return view('dashboard.report.clientDetails', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function providers(Request $request)
    {
        try {
            $providersData = Provider::where('deleted_at', '=', null)
            ->when($request->provider_id != null,function ($q) use($request){
                return $q->where('id',$request->provider_id);
            })
            ->get();

            foreach ($providersData as $provider) 
            {
                $item['total_purchase'] = DB::table('purchases')
                ->where('deleted_at', '=', null)
                ->where('provider_id', $provider->id)
                ->count();

                $item['total_amount'] = DB::table('purchases')
                ->where('deleted_at', '=', null)
                ->where('provider_id', $provider->id)
                ->sum('GrandTotal');

                $item['total_paid'] = DB::table('purchases')
                ->leftjoin('payment_purchases', 'purchases.id', '=', 'payment_purchases.purchase_id')
                ->where('purchases.provider_id', $provider->id)
                ->where('purchases.deleted_at', '=', null)
                ->sum('payment_purchases.montant');

                $item['due']        = $item['total_amount'] - $item['total_paid'];
                $item['name']       = $provider->name;
                $item['phone']      = $provider->phone;
                $item['address']    = $provider->adresse;
                $item['created_at'] = $provider->created_at;
                $item['code']       = $provider->code;
                $item['id']         = $provider->id;
                
                $data[] = $item;
            }

            $providers = Provider::where('deleted_at', '=', null)->get();

            return view('dashboard.report.providers', compact('data', 'providers'))
            ->with([
                'provider_id' => $request->provider_id,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function providerDetails(Request $request, $id)
    {
        try {

            $provider = Provider::where('deleted_at', '=', null)->findOrFail($id);

            $data['id'] = $id;


            $data['total_purchases'] = DB::table('purchases')
            ->where('deleted_at', '=', null)
            ->where('provider_id', $id)
            ->count();

            $data['total_amount'] = DB::table('purchases')
            ->where('deleted_at', '=', null)
            ->where('provider_id', $id)
            ->sum('GrandTotal');
            
            $data['total_paid'] = DB::table('purchases')
            ->where('purchases.deleted_at', '=', null)
            ->join('payment_purchases', 'purchases.id', '=', 'payment_purchases.purchase_id')
            ->where('payment_purchases.deleted_at', '=', null)
            ->where('purchases.provider_id', $id)
            ->sum('payment_purchases.montant');
            
            $data['due'] = $data['total_amount'] - $data['total_paid'];
            
            //provider purchases
            $data['purchases'] = Purchase::where('deleted_at', '=', null)
            ->where('provider_id', $id)
            ->get();
            
            //provider purchases payments
            $data['purchasesPayments'] = DB::table('payment_purchases')
            ->where('payment_purchases.deleted_at', '=', null)
            ->join('purchases', 'payment_purchases.purchase_id', '=', 'purchases.id')
            ->where('purchases.provider_id', $id)
            ->select(
                'payment_purchases.date', 'payment_purchases.Ref AS Ref', 'purchases.Ref AS Purchase_Ref',
                'payment_purchases.Reglement', 'payment_purchases.montant'
            )->get();
            
            //provider purchasesReturns
            $data['purchasesReturns'] = PurchaseReturn::where('deleted_at', '=', null)
            ->where('provider_id', $request->id)
            ->get();
            
            //provider purchasesReturns payments
            $data['purchasesReturnsPayments'] = DB::table('payment_purchase_returns')
            ->where('payment_purchase_returns.deleted_at', '=', null)
            ->join('purchase_returns', 'payment_purchase_returns.purchase_return_id', '=', 'purchase_returns.id')
            ->where('purchase_returns.provider_id', $id)
            ->select(
                'payment_purchase_returns.date', 'payment_purchase_returns.Ref AS Ref', 'purchase_returns.Ref AS PurchaseReturn_Ref',
                'payment_purchase_returns.Reglement', 'payment_purchase_returns.montant'
            )
            ->get();

            return view('dashboard.report.providerDetails', compact('data'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    














    

    

    



    //-------------------- Backup Databse -------------\\

    public function GetBackup(Request $request)
    {

        $this->authorizeForUser($request->user('api'), 'backup', User::class);

        $data = [];
        $id = 0;
        foreach (glob(storage_path() . '/app/public/backup/*') as $filename) {
            $item['id'] = $id += 1;
            $item['date'] = basename($filename);
            $size = $this->formatSizeUnits(filesize($filename));
            $item['size'] = $size;

            $data[] = $item;
        }
        $totalRows = sizeof($data);

        return response()->json([
            'backups' => $data,
            'totalRows' => $totalRows,
        ]);

    }

    //-------------------- Generate Databse -------------\\

    public function GenerateBackup(Request $request)
    {

        $this->authorizeForUser($request->user('api'), 'backup', User::class);

        Artisan::call('database:backup');

        return response()->json('Generate complete success');
    }

    //-------------------- Delete Databse -------------\\

    public function DeleteBackup(Request $request, $name)
    {

        $this->authorizeForUser($request->user('api'), 'backup', User::class);

        foreach (glob(storage_path() . '/app/public/backup/*') as $filename) {
            $path = storage_path() . '/app/public/backup/' . basename($name);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

}
