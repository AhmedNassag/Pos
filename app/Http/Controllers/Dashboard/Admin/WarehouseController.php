<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\product_warehouse;
use App\Models\Warehouse;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\Adjustment;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\WarehouseAdded;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class WarehouseController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:عرض المخازن', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة المخازن', ['only' => ['store']]);
        $this->middleware('permission:تعديل المخازن', ['only' => ['update']]);
        $this->middleware('permission:حذف المخازن', ['only' => ['destroy','deleteSelected','forceDelete']]);
        $this->middleware('permission:إستعادة المخازن', ['only' => ['restore']]);
        $this->middleware('permission:أرشيف المخازن', ['only' => ['archived']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
    }
    


    public function index(Request $request)
    {
        try {
            $warehouses = Warehouse::where('deleted_at', '=', null)
            ->when($request->name != null,function ($q) use($request){
                return $q->where('name','like','%'.$request->name.'%');
            })
            ->when($request->mobile != null,function ($q) use($request){
                return $q->where('mobile','like','%'.$request->mobile.'%');
            })
            ->when($request->country != null,function ($q) use($request){
                return $q->where('country','like','%'.$request->country.'%');
            })
            ->when($request->city != null,function ($q) use($request){
                return $q->where('city','like','%'.$request->city.'%');
            })
            // ->orderBy('id', 'desc')
            ->get();
                
            $trashed = false;

            return view('dashboard.warehouse.index', compact('warehouses', 'trashed'))
            ->with([
                'name'    => $request->name,
                'mobile'  => $request->mobile,
                'country' => $request->country,
                'city'    => $request->city
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function show($id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            $product_warehouses = product_warehouse::where('warehouse_id', $warehouse->id)->where('deleted_at', '=', null)->get();
            $total_qty = 0;
            if($product_warehouses->count() > 0) {
                foreach ($product_warehouses as $product_warehouse) {
                    $total_qty += $product_warehouse->qte;
                    $product['quantity'] = $total_qty;
                }
            } else {
                $product['quantity'] = $total_qty;
            }
            return view('dashboard.warehouse.show', compact('warehouse', 'product_warehouses'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }  
    }

    

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name'    => 'required',
                'mobile'  => 'required',
                'country' => 'required',
                'city'    => 'required',
            ]);

            $warehouse = Warehouse::create([
                'name'    => $request['name'],
                'mobile'  => $request['mobile'],
                'country' => $request['country'],
                'city'    => $request['city'],
            ]);

            // send notification
            $users = User::where('id', '!=', Auth::user()->id)->get();
            Notification::send($users, new WarehouseAdded($warehouse->id));

            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function update(Request $request)
    {
        try {
            $this->validate($request, [
                'name'    => 'required',
                'mobile'  => 'required',
                'country' => 'required',
                'city'    => 'required',
            ]);

            $warehouse = Warehouse::whereId($request->id)->update([
                'name'    => $request['name'],
                'mobile'  => $request['mobile'],
                'country' => $request['country'],
                'city'    => $request['city'],
            ]);

            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function destroy(Request $request)
    {
        try {
            $related_table   = Expense::where('warehouse_id', $request->id)->pluck('warehouse_id');
            $related_table_2 = Purchase::where('warehouse_id', $request->id)->pluck('warehouse_id');
            $related_table_3 = PurchaseReturn::where('warehouse_id', $request->id)->pluck('warehouse_id');
            $related_table_4 = Sale::where('warehouse_id', $request->id)->pluck('warehouse_id');
            $related_table_5 = SaleReturn::where('warehouse_id', $request->id)->pluck('warehouse_id');
            $related_table_6 = Adjustment::where('warehouse_id', $request->id)->pluck('warehouse_id');
            if(
                $related_table->count() == 0
                && $related_table_2->count() == 0 
                && $related_table_3->count() == 0
                && $related_table_4->count() == 0
                && $related_table_5->count() == 0
                && $related_table_6->count() == 0
            )
            { 
                $warehouse         = Warehouse::whereId($request->id)->delete();
                $product_warehouse = product_warehouse::where('warehouse_id', $request->id)->delete();
                session()->flash('success');
                return redirect()->back();
            } else {
                session()->flash('canNotDeleted');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function deleteSelected(Request $request)
    {
        try {
            $delete_selected_id = explode(",", $request->delete_selected_id);
            foreach($delete_selected_id as $selected_id) {
                $related_table   = Expense::where('warehouse_id', $selected_id)->pluck('warehouse_id');
                $related_table_2 = Purchase::where('warehouse_id', $request->id)->pluck('warehouse_id');
                $related_table_3 = PurchaseReturn::where('warehouse_id', $request->id)->pluck('warehouse_id');
                $related_table_4 = Sale::where('warehouse_id', $request->id)->pluck('warehouse_id');
                $related_table_5 = SaleReturn::where('warehouse_id', $request->id)->pluck('warehouse_id');
                $related_table_6 = Adjustment::where('warehouse_id', $request->id)->pluck('warehouse_id');
                if(
                    $related_table->count() == 0
                    && $related_table_2->count() == 0 
                    && $related_table_3->count() == 0
                    && $related_table_4->count() == 0
                    && $related_table_5->count() == 0
                    && $related_table_6->count() == 0
                )
                {
                    $warehouses        = Warehouse::whereIn('id', $delete_selected_id)->delete();
                    $product_warehouse = product_warehouse::whereIn('warehouse_id', $delete_selected_id)->delete();
                    session()->flash('success');
                    return redirect()->back();
                } else {
                    session()->flash('canNotDeleted');
                    return redirect()->back();
                }
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function forceDelete(Request $request)
    {
        try {
            $warehouse = Warehouse::withTrashed()->where('id', $request->id)->first();
            if (!$warehouse) {
                session()->flash('error');
                return redirect()->back();
            }
            $warehouse->forceDelete();
            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function restore(Request $request)
    {
        try {
            $warehouse = Warehouse::withTrashed()->where('id', $request->id)->restore();
            if (!$warehouse) {
                session()->flash('error');
                return redirect()->back();
            }
            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function archived()
    {
        try {
            $warehouses = Warehouse::onlyTrashed()->get();
            $trashed = true;
            return view('dashboard.warehouse.index', compact('warehouses', 'trashed'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function showNotification($id,$notification_id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            $notification = NotificationModel::findOrFail($notification_id);
            $notification->update([
                'read_at' => now(),
            ]);
            $product_warehouses = product_warehouse::where('warehouse_id', $warehouse->id)->where('deleted_at', '=', null)->get();
            $total_qty = 0;
            if($product_warehouses->count() > 0) {
                foreach ($product_warehouses as $product_warehouse) {
                    $total_qty += $product_warehouse->qte;
                    $product['quantity'] = $total_qty;
                }
            } else {
                $product['quantity'] = $total_qty;
            }
            return view('dashboard.warehouse.show', compact('warehouse', 'product_warehouses'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
