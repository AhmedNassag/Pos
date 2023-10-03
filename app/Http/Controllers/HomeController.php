<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\SaleReturn;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\product_warehouse;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $top_five_clients             = $this->top_five_clients();
        $top_five_providers           = $this->top_five_providers();
        $last_five_sales              = $this->last_five_sales();
        $top_five_products_this_month = $this->top_five_products_this_month();
        $top_five_products_this_year  = $this->top_five_products_this_year();
        $today_sales                  = $this->today_sales();
        $today_return_sales           = $this->today_return_sales();
        $today_purchases              = $this->today_purchases();
        $today_return_purchases       = $this->today_return_purchases();
        $stock_alerts                 = $this->stock_alerts();
        $total_paid_sales             = $this->total_paid_sales();
        $total_unpaid_sales           = $this->total_unpaid_sales();
        $total_partial_sales          = $this->total_partial_sales();
        // return $last_five_sales;
        return view('home', compact(
                'top_five_clients',
                'top_five_providers',
                'last_five_sales',
                'top_five_products_this_year',
                'top_five_products_this_month',
                'today_sales',
                'today_return_sales',
                'today_purchases',
                'today_return_purchases',
                'stock_alerts',
                'total_paid_sales',
                'total_unpaid_sales',
                'total_partial_sales',
            )
        );
    }



    //top_five_clients
    public function top_five_clients()
    {
        try {
            $top_five_clients = Sale::where('sales.deleted_at', '=', null)
            // ->whereBetween('date', [
            //     Carbon::now()->startOfMonth(),
            //     Carbon::now()->endOfMonth(),
            // ])
            ->join('clients', 'sales.client_id', '=', 'clients.id')
            ->select(DB::raw('clients.name'), DB::raw("count(*) as value"))
            ->groupBy('clients.name')
            ->orderBy('value', 'desc')
            ->take(5)
            ->get();

            return $top_five_clients;
                
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    //top_five_providers
    public function top_five_providers()
    {
        try {
            $top_five_providers = Purchase::where('purchases.deleted_at', '=', null)
            // $top_five_providers = DB::table('purchases')->where('purchases.deleted_at', '=', null)
            // ->whereBetween('date', [
            //     Carbon::now()->startOfMonth(),
            //     Carbon::now()->endOfMonth(),
            // ])
            ->join('providers', 'purchases.provider_id', '=', 'providers.id')
            ->select(DB::raw('providers.name'), DB::raw('count(*) as count'))
            ->groupBy('providers.name')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();
            
            return $top_five_providers;

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    //last_five_sales
    public function last_five_sales()
    {
        try {
            $last_five_sales = Sale::with('details', 'client', 'warehouse', 'facture')->where('deleted_at', '=', null)
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

            return $last_five_sales;

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    //top_five_products_this_year
    public function top_five_products_this_year()
    {
        try {
            $top_five_products_this_year = SaleDetail::join('sales', 'sale_details.sale_id', '=', 'sales.id')
                ->join('products', 'sale_details.product_id', '=', 'products.id')
                ->whereBetween('sale_details.date', [
                    Carbon::now()->startOfYear(),
                    Carbon::now()->endOfYear(),
                ])
                ->select(
                    DB::raw('products.name as name'),
                    DB::raw('sum(sale_details.total) as value'),
                )
                ->groupBy('products.name')
                ->orderBy('value', 'desc')
                ->take(5)
                ->get();

            return $top_five_products_this_year;
            
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    //top_five_products_this_month
    public function top_five_products_this_month()
    {
        try {
            $top_five_products_this_month = SaleDetail::join('sales', 'sale_details.sale_id', '=', 'sales.id')
            ->join('products', 'sale_details.product_id', '=', 'products.id')
            ->join('units', 'products.unit_sale_id', '=', 'units.id')
            ->whereBetween('sale_details.date', [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ])
            ->select(
                DB::raw('products.name as name'),
                // DB::raw('units.ShortName as unit_product'),
                DB::raw('count(*) as count'),
                DB::raw('sum(total) as total'),
                DB::raw('sum(quantity) as quantity'),
            )
            ->groupBy('products.name')
            ->orderBy('count', 'desc')
            ->take(5)
            ->get();

            return $top_five_products_this_month;

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    //stock_alert
    public function stock_alerts()
    {
        try {
            $product_warehouse_data = product_warehouse::with('warehouse', 'product' ,'productVariant')
            ->join('products', 'product_warehouse.product_id', '=', 'products.id')
            ->whereRaw('qte <= stock_alert')
            ->where('product_warehouse.deleted_at', null)
            ->take('5')->get();

            $stock_alerts = [];
            if ($product_warehouse_data->isNotEmpty()) 
            {
                foreach ($product_warehouse_data as $product_warehouse) 
                {
                    if ($product_warehouse->qte <= $product_warehouse['product']->stock_alert) 
                    {
                        if ($product_warehouse->product_variant_id !== null) 
                        {
                            $item['code'] = $product_warehouse['productVariant']->name . '-' . $product_warehouse['product']->code;
                        } 
                        else {
                            $item['code'] = $product_warehouse['product']->code;
                        }
                        $item['quantity'] = $product_warehouse->qte;
                        $item['name'] = $product_warehouse['product']->name;
                        $item['warehouse'] = $product_warehouse['warehouse']->name;
                        $item['stock_alert'] = $product_warehouse['product']->stock_alert;
                        $stock_alerts[] = $item;
                    }
                }
            }

            return $stock_alerts;

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    //today_sales
    public function today_sales()
    {
        try {
            $today_sales_sum = Sale::where('deleted_at', '=', null)
            ->where('date', \Carbon\Carbon::today())
            ->get(DB::raw('SUM(GrandTotal)  As sum'))
            ->first()->sum;

            $today_sales_count = Sale::where('deleted_at', '=', null)
            ->where('date', \Carbon\Carbon::today())
            ->count();

            return ['today_sales_sum' => $today_sales_sum, 'today_sales_count' => $today_sales_count];

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    //today_return_sales
    public function today_return_sales()
    {
        try {
            $today_return_sales_sum = SaleReturn::where('deleted_at', '=', null)
            ->where('date', \Carbon\Carbon::today())
            ->get(DB::raw('SUM(GrandTotal)  As sum'))
            ->first()->sum;

            $today_return_sales_count = SaleReturn::where('deleted_at', '=', null)
            ->where('date', \Carbon\Carbon::today())
            ->count();

            return ['today_return_sales_sum' => $today_return_sales_sum , 'today_return_sales_count' => $today_return_sales_count];

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    //today_purchases
    public function today_purchases()
    {
        try {
            $today_purchases_sum = Purchase::where('deleted_at', '=', null)
            ->where('date', \Carbon\Carbon::today())
            ->get(DB::raw('SUM(GrandTotal)  As sum'))
            ->first()->sum;

            $today_purchases_count = Purchase::where('deleted_at', '=', null)
            ->where('date', \Carbon\Carbon::today())
            ->count();

            return ['today_purchases_sum' => $today_purchases_sum, 'today_purchases_count' => $today_purchases_count];

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    //today_purchases_return
    public function today_return_purchases()
    {
        try {
            $today_return_purchases_sum = PurchaseReturn::where('deleted_at', '=', null)
            ->where('date', \Carbon\Carbon::today())
            ->get(DB::raw('SUM(GrandTotal)  As sum'))
            ->first()->sum;

            $today_return_purchases_count = PurchaseReturn::where('deleted_at', '=', null)
            ->where('date', \Carbon\Carbon::today())
            ->count();

            return ['today_return_purchases_sum' => $today_return_purchases_sum , 'today_return_purchases_count' => $today_return_purchases_count];

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    //total_paid_sales
    public function total_paid_sales()
    {
        try {
            $total_paid_sales = Sale::where('deleted_at', '=', null)
            ->where('payment_status', 'paid')
            ->count();

            return $total_paid_sales;

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    //total_unpaid_sales
    public function total_unpaid_sales()
    {
        try {
            $total_unpaid_sales = Sale::where('deleted_at', '=', null)
            ->where('payment_status', 'unpaid')
            ->count();

            return $total_unpaid_sales;

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    //total_partial_sales
    public function total_partial_sales()
    {
        try {
            $total_partial_sales = Sale::where('deleted_at', '=', null)
            ->where('payment_status', 'partial')
            ->count();

            return $total_partial_sales;

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

}
