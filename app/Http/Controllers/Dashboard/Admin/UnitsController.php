<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Models\Unit;
use App\Models\Product;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturnDetails;
use App\Models\SaleDetail;
use App\Models\SaleReturnDetails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UnitAdded;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class UnitsController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:عرض الوحدات', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة الوحدات', ['only' => ['store']]);
        $this->middleware('permission:تعديل الوحدات', ['only' => ['update']]);
        $this->middleware('permission:حذف الوحدات', ['only' => ['destroy','deleteSelected','forceDelete']]);
        $this->middleware('permission:إستعادة الوحدات', ['only' => ['restore']]);
        $this->middleware('permission:أرشيف الوحدات', ['only' => ['archived']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
    }
    


    public function index(Request $request)
    {
        try {
            $units = Unit::where('deleted_at', '=', null)
            ->when($request->name != null,function ($q) use($request){
                return $q->where('name','like','%'.$request->name.'%');
            })
            ->when($request->ShortName != null,function ($q) use($request){
                return $q->where('ShortName','like','%'.$request->ShortName.'%');
            })
            ->when($request->operator_value != null,function ($q) use($request){
                return $q->where('operator_value','like','%'.$request->operator_value.'%');
            })
            ->when($request->base_unit != null,function ($q) use($request){
                return $q->where('base_unit','like',$request->base_unit);
            })
            // ->orderBy('id', 'desc')
            ->get();

            $base_units = Unit::where('base_unit', null)->where('deleted_at', null)->orderBy('id', 'DESC')->get(['id', 'name']); 
            
            $trashed = false;

            return view('dashboard.unit.index', compact('units', 'base_units', 'trashed'))
            ->with([
                'name'           => $request->name,
                'ShortName'      => $request->ShortName,
                'operator_value' => $request->operator_value,
                'base_unit'      => $request->base_unit,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function show($id)
    {
        try {
            $unit = Unit::findOrFail($id);
            return view('dashboard.unit.show', compact('unit'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    


    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name'      => 'required',
                'ShortName' => 'required',
            ]);

            if ($request->base_unit == '' || $request->base_unit == 0) {
                $operator       = '*';
                $operator_value = 1;
            } else {
                $operator       = '/';
                $operator_value = $request->operator_value;
            }

            $unit = Unit::create([
                'name'           => $request['name'],
                'ShortName'      => $request['ShortName'],
                'base_unit'      => $request['base_unit'],
                'operator'       => $operator,
                'operator_value' => $operator_value,
            ]);

            // send notification
            $users = User::where('id', '!=', Auth::user()->id)->get();
            Notification::send($users, new UnitAdded($unit->id));

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
                'name'      => 'required',
                'ShortName' => 'required',
            ]);

            if ($request->base_unit == '' || $request->base_unit == 0 || $request->base_unit == $request->id) {
                $operator       = '*';
                $operator_value = 1;
                $base_unit      = null;
            } else {
                $operator       = $request->operator;
                $operator_value = $request->operator_value;
                $base_unit      = $request['base_unit'];
            }

            $unit = Unit::whereId($request->id)->update([
                'name'           => $request['name'],
                'ShortName'      => $request['ShortName'],
                'base_unit'      => $base_unit,
                'operator'       => $operator,
                'operator_value' => $operator_value,
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
            $related_table   = Unit::where('base_unit', $request->id)->pluck('base_unit');
            $related_table_2 = Product::where('unit_id', $request->id)->pluck('unit_id');
            $related_table_3 = Product::where('unit_sale_id', $request->id)->pluck('unit_sale_id');
            $related_table_4 = Product::where('unit_purchase_id', $request->id)->pluck('unit_purchase_id');
            $related_table_5 = PurchaseDetail::where('purchase_unit_id', $request->id)->pluck('purchase_unit_id');
            $related_table_6 = PurchaseReturnDetails::where('purchase_unit_id', $request->id)->pluck('purchase_unit_id');
            $related_table_7 = SaleDetail::where('sale_unit_id', $request->id)->pluck('sale_unit_id');
            $related_table_8 = SaleReturnDetails::where('sale_unit_id', $request->id)->pluck('sale_unit_id');
            if(
                $related_table->count() == 0
                && $related_table_2->count() == 0
                && $related_table_3->count() == 0
                && $related_table_4->count() == 0
                && $related_table_5->count() == 0
                && $related_table_6->count() == 0
                && $related_table_7->count() == 0
                && $related_table_8->count() == 0
            )
            {
                $unit = Unit::whereId($request->id)->delete();
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
                $related_table = Unit::where('base_unit', $selected_id)->pluck('base_unit');
                $related_table_2 = Product::where('unit_id', $request->id)->pluck('unit_id');
                $related_table_3 = Product::where('unit_sale_id', $request->id)->pluck('unit_sale_id');
                $related_table_4 = Product::where('unit_purchase_id', $request->id)->pluck('unit_purchase_id');
                $related_table_5 = PurchaseDetail::where('purchase_unit_id', $request->id)->pluck('purchase_unit_id');
                $related_table_6 = PurchaseReturnDetails::where('purchase_unit_id', $request->id)->pluck('purchase_unit_id');
                $related_table_7 = SaleDetail::where('sale_unit_id', $request->id)->pluck('sale_unit_id');
                $related_table_8 = SaleReturnDetails::where('sale_unit_id', $request->id)->pluck('sale_unit_id');
                if(
                    $related_table->count() == 0
                    && $related_table_2->count() == 0
                    && $related_table_3->count() == 0
                    && $related_table_4->count() == 0
                    && $related_table_5->count() == 0
                    && $related_table_6->count() == 0
                    && $related_table_7->count() == 0
                    && $related_table_8->count() == 0
                )
                { 
                    $units = Unit::whereIn('id', $delete_selected_id)->delete();
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
            $unit = Unit::withTrashed()->where('id', $request->id)->first();
            if (!$unit) {
                session()->flash('error');
                return redirect()->back();
            }
            $unit->forceDelete();
            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function restore(Request $request)
    {
        try {
            $unit = Unit::withTrashed()->where('id', $request->id)->restore();
            if (!$unit) {
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
            $units = Unit::onlyTrashed()->get();
            $base_units = Unit::where('base_unit', null)->where('deleted_at', null)->orderBy('id', 'DESC')->get(['id', 'name']); 
            $trashed = true;
            return view('dashboard.unit.index', compact('units', 'base_units', 'trashed'));
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
            $unit = Unit::findOrFail($id);
            return view('dashboard.unit.show', compact('unit'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
