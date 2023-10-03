<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Exports\ExpenseExport;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Role;
use App\Models\Warehouse;
use App\utils\helpers;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ExpenseAdded;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class ExpensesController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:عرض المصروفات', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة المصروفات', ['only' => ['store']]);
        $this->middleware('permission:تعديل المصروفات', ['only' => ['update']]);
        $this->middleware('permission:حذف المصروفات', ['only' => ['destroy','deleteSelected','forceDelete']]);
        $this->middleware('permission:إستعادة المصروفات', ['only' => ['restore']]);
        $this->middleware('permission:أرشيف المصروفات', ['only' => ['archived']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
    }
    


    public function index(request $request)
    {
        try {
            $expenses = Expense::with('expense_category', 'warehouse')->where('deleted_at', '=', null)
            ->when($request->date != null,function ($q) use($request){
                return $q->where('date',$request->date);
            })
            ->when($request->amount != null,function ($q) use($request){
                return $q->where('amount',$request->amount);
            })
            ->when($request->details != null,function ($q) use($request){
                return $q->where('details','like','%'.$request->details.'%');
            })
            ->when($request->warehouse_id != null,function ($q) use($request){
                return $q->where('warehouse_id',$request->warehouse_id);
            })
            ->when($request->expense_category_id != null,function ($q) use($request){
                return $q->where('expense_category_id',$request->expense_category_id);
            })
            // ->orderBy('id', 'desc')
            ->get();

            $warehouses        = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
            $expenses_categories = ExpenseCategory::where('deleted_at', '=', null)->get(['id', 'name']);
                
            $trashed = false;

            return view('dashboard.expense.index', compact('expenses', 'expenses_categories', 'warehouses', 'trashed'))
            ->with([
                'date'                => $request->date,
                'amount'              => $request->amount,
                'details'             => $request->details,
                'warehouse_id'        => $request->warehouse_id,
                'expense_category_id' => $request->expense_category_id,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function show($id)
    {
        try {
            $expense = Expense::findOrFail($id);
            return view('dashboard.expense.show', compact('expense'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'date'                => 'required',
                'amount'              => 'required',
                'details'             => 'required',
                'warehouse_id'        => 'required',
                'expense_category_id' => 'required',
            ]);

            $expense = Expense::create([
                'date'                => $request['date'],
                'amount'              => $request['amount'],
                'details'             => $request['details'],
                'warehouse_id'        => $request['warehouse_id'],
                'expense_category_id' => $request['expense_category_id'],
                'user_id'             => Auth::user()->id,
                'Ref'                 => $this->getNumberOrder(),
            ]);

            // send notification
            $users = User::where('id', '!=', Auth::user()->id)->get();
            Notification::send($users, new ExpenseAdded($expense->id));

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
                'date'                => 'required',
                'amount'              => 'required',
                'details'             => 'required',
                'warehouse_id'        => 'required',
                'expense_category_id' => 'required',
            ]);
            $expense = Expense::findOrFail($request->id);

            $expense = Expense::whereId($request->id)->update([
                'date'                => $request['date'],
                'amount'              => $request['amount'],
                'details'             => $request['details'],
                'warehouse_id'        => $request['warehouse_id'],
                'expense_category_id' => $request['expense_category_id'],
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
            // $related_table = realed_model::where('category_id', $request->id)->pluck('category_id');
            // if($related_table->count() == 0) { 
                $expense = Expense::whereId($request->id)->update([
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
            // foreach($delete_selected_id as $selected_id) {
            //     $related_table = realed_model::where('category_id', $selected_id)->pluck('category_id');
            //     if($related_table->count() == 0) {
                    $expense = Expense::whereIn('id', $delete_selected_id)->delete();
                    session()->flash('success');
                    return redirect()->back();
            //     } else {
            //         session()->flash('canNotDeleted');
            //         return redirect()->back();
            //     }
            // }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function forceDelete(Request $request)
    {
        try {
            $expense = Expense::withTrashed()->where('id', $request->id)->first();
            if (!$expense) {
                session()->flash('error');
                return redirect()->back();
            }
            $expense->forceDelete();
            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function restore(Request $request)
    {
        try {
            $expense = Expense::withTrashed()->where('id', $request->id)->restore();
            if (!$expense) {
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
            $expenses            = Expense::onlyTrashed()->get();
            $warehouses          = Warehouse::where('deleted_at', '=', null)->get(['id', 'name']);
            $expenses_categories = ExpenseCategory::where('deleted_at', '=', null)->get(['id', 'name']);
            $trashed = true;
            return view('dashboard.expense.index', compact('expenses', 'warehouses', 'expenses_categories', 'trashed'));
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
            return $this->show($id);
            // $expense = Expense::findOrFail($id);
            // return view('dashboard.expense.show', compact('expense'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function getNumberOrder()
    {
        try {
            $last = DB::table('expenses')->latest('id')->first();
            if ($last) {
                $item  = $last->Ref;
                $nwMsg = explode("_", $item);
                $inMsg = $nwMsg[1] + 1;
                $code  = $nwMsg[0] . '_' . $inMsg;
            } else {
                $code = 'EXP_1111';
            }
            return $code;
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
