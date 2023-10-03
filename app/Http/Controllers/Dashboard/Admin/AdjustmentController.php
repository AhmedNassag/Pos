<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Exports\AdjustmentsExport;
use App\Models\Adjustment;
use App\Models\AdjustmentDetail;
use App\Models\ProductVariant;
use App\Models\product_warehouse;
use App\Models\Role;
use App\Models\Warehouse;
use App\Models\Product;
use App\utils\helpers;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdjustmentAdded;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class AdjustmentController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:عرض تعديل المخزون', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة تعديل المخزون', ['only' => ['store']]);
        $this->middleware('permission:حذف تعديل المخزون', ['only' => ['destroy','deleteSelected']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
        $this->middleware('permission:عرض المخزون', ['only' => ['stock']]);
    }
    


    public function index(request $request)
    {
        try {
            $adjustments = Adjustment::with('details', 'warehouse')->where('deleted_at', '=', null)
            ->when($request->date != null,function ($q) use($request){
                return $q->where('date',$request->date);
            })
            ->when($request->warehouse_id != null,function ($q) use($request){
                return $q->where('warehouse_id',$request->warehouse_id);
            })
            ->when($request->product_id != null,function ($q) use($request){
                return $q->whereRelation('details' ,'product_id',$request->product_id);
            })
            ->when($request->quantity != null,function ($q) use($request){
                return $q->whereRelation('details' ,'quantity',$request->quantity);
            })
            ->when($request->type != null,function ($q) use($request){
                return $q->whereRelation('details' ,'type',$request->type);
            })
            // ->orderBy('id', 'desc')
            ->get();

            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
            $products   = Product::where('deleted_at', '=', null)->get(['id', 'name']);
                
            $trashed = false;

            return view('dashboard.adjustment.index', compact('adjustments', 'warehouses', 'products', 'trashed'))
            ->with([
                'warehouse_id' => $request->warehouse_id,
                'product_id'   => $request->product_id,
                'quantity'     => $request->quantity,
                'type'         => $request->type,
                'date'         => $request->date,
                'notes'        => $request->notes,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function show($id)
    {
        try {
            $adjustment = Adjustment::with('details', 'warehouse')->findOrFail($id);
            return view('dashboard.adjustment.show', compact('adjustment'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'warehouse_id' => 'required',
                'product_id'   => 'required',
                'quantity'     => 'required',
                'type'         => 'required',
                'date'         => 'required',
            ]);

            $adjustment = Adjustment::create([
                'date'         => $request['date'],
                'warehouse_id' => $request['warehouse_id'],
                'notes'        => $request['notes'],
                'items'        => 1.0,
                'user_id'      => Auth::user()->id,
                'Ref'          => $this->getNumberOrder(),
            ]);

            $adjustmentDetail = AdjustmentDetail::create([
                'adjustment_id'      => $adjustment->id,
                'quantity'           => $request['quantity'],
                'product_id'         => $request['product_id'],
                'product_variant_id' => $request['product_variant_id'],
                'type'               => $request['type'],
            ]);

            if ($request['type'] == "add") 
            {
                $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                ->where('warehouse_id', $request['warehouse_id'])
                ->where('product_id', $request['product_id'])
                ->first();

                if ($product_warehouse) 
                {
                    $product_warehouse->qte += $request['quantity'];
                    $product_warehouse->save();
                }
            } 
            else 
            {
                $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                ->where('warehouse_id', $request['warehouse_id'])
                ->where('product_id', $request['product_id'])
                ->first();

                if ($product_warehouse) 
                {
                    $product_warehouse->qte -= $request['quantity'];
                    $product_warehouse->save();
                }
            }

            // send notification
            $users = User::where('id', '!=', Auth::user()->id)->get();
            Notification::send($users, new AdjustmentAdded($adjustment->id));

            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function destroy(Request $request)
    {
        try {
            $current_adjustment     = Adjustment::findOrFail($request->id);
            $old_adjustment_details = AdjustmentDetail::where('adjustment_id', $request->id)->get();

            // Init Data with old Parametre
            foreach ($old_adjustment_details as $key => $value) 
            {
                if ($value['type'] == "add") 
                {
                    $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                    ->where('warehouse_id', $current_adjustment->warehouse_id)
                    ->where('product_id', $value['product_id'])
                    ->first();

                    if ($product_warehouse) 
                    {
                        $product_warehouse->qte -= $value['quantity'];
                        $product_warehouse->save();
                    }
                } 
                else 
                {
                    $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                    ->where('warehouse_id', $current_adjustment->warehouse_id)
                    ->where('product_id', $value['product_id'])
                    ->first();

                    if ($product_warehouse) 
                    {
                        $product_warehouse->qte += $value['quantity'];
                        $product_warehouse->save();
                    }
                }
            }

            $current_adjustment->details()->delete();
            $current_adjustment->delete();

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
            foreach ($delete_selected_id as $adjustment_id)
            {
                $current_adjustment     = Adjustment::findOrFail($adjustment_id);
                $old_adjustment_details = AdjustmentDetail::where('adjustment_id', $adjustment_id)->get();

                // Init Data with old Parametre
                foreach ($old_adjustment_details as $key => $value)
                {
                    if ($value['type'] == "add")
                    {
                        $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                        ->where('warehouse_id', $current_adjustment->warehouse_id)
                        ->where('product_id', $value['product_id'])
                        ->first();

                        if ($product_warehouse)
                        {
                            $product_warehouse->qte -= $value['quantity'];
                            $product_warehouse->save();
                        }
                    }
                    else
                    {
                        $product_warehouse = product_warehouse::where('deleted_at', '=', null)
                        ->where('warehouse_id', $current_adjustment->warehouse_id)
                        ->where('product_id', $value['product_id'])
                        ->first();

                        if ($product_warehouse)
                        {
                            $product_warehouse->qte += $value['quantity'];
                            $product_warehouse->save();
                        }
                    }

                }

                $current_adjustment->details()->delete();
                $current_adjustment->delete();

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
            $adjustment = Adjustment::with('details', 'warehouse')->findOrFail($id);
            return view('dashboard.adjustment.show', compact('adjustment'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function stock(Request $request)
    {
        try {
            $stocks = product_warehouse::with('product', 'warehouse')->where('qte', '!=', null)->where('deleted_at', '=', null)
            ->when($request->warehouse_id != null,function ($q) use($request){
                return $q->where('warehouse_id',$request->warehouse_id);
            })
            ->when($request->product_id != null,function ($q) use($request){
                return $q->where('product_id',$request->product_id);
            })
            ->when($request->quantity != null,function ($q) use($request){
                return $q->where('qte',$request->quantity);
            })
            // ->orderBy('id', 'desc')
            ->get();

            $warehouses = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
            $products   = Product::where('deleted_at', '=', null)->get(['id', 'name']);
                
            $trashed = false;

            return view('dashboard.adjustment.stock', compact('stocks', 'warehouses', 'products', 'trashed'))
            ->with([
                'warehouse_id' => $request->warehouse_id,
                'product_id'   => $request->product_id,
                'quantity'     => $request->quantity,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function getNumberOrder()
    {
        try {
            $last = DB::table('adjustments')->latest('id')->first();
            if ($last) 
            {
                $item  = $last->Ref;
                $nwMsg = explode("_", $item);
                $inMsg = $nwMsg[1] + 1;
                $code  = $nwMsg[0] . '_' . $inMsg;
            }
            else
            {
                $code = 'AD_1111';
            }
            return $code;
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
