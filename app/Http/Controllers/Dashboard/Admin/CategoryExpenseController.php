<?php

namespace App\Http\Controllers\Dashboard\Admin;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ExpenseCategoryAdded;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class CategoryExpenseController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:عرض فئات المصروفات', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة فئات المصروفات', ['only' => ['store']]);
        $this->middleware('permission:تعديل فئات المصروفات', ['only' => ['update']]);
        $this->middleware('permission:حذف فئات المصروفات', ['only' => ['destroy','deleteSelected','forceDelete']]);
        $this->middleware('permission:إستعادة فئات المصروفات', ['only' => ['restore']]);
        $this->middleware('permission:أرشيف فئات المصروفات', ['only' => ['archived']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
    }
    


    public function index(Request $request)
    {
        try {
            $expensescategories = ExpenseCategory::where('deleted_at', '=', null)
            ->when($request->name != null,function ($q) use($request){
                return $q->where('name','like','%'.$request->name.'%');
            })
            ->when($request->description != null,function ($q) use($request){
                return $q->where('description','like','%'.$request->description.'%');
            })
            // ->orderBy('id', 'desc')
            ->get();
                
            $trashed = false;

            return view('dashboard.expensescategory.index', compact('expensescategories', 'trashed'))
            ->with([
                'name'        => $request->name,
                'description' => $request->description,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function show($id)
    {
        try {
            $expensescategory = ExpenseCategory::findOrFail($id);
            return view('dashboard.expensescategory.show', compact('expensescategory'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name'   => 'required',
            ]);

            $expensesCategory = ExpenseCategory::create([
                'name'        => $request['name'],
                'description' => $request['description'],
                'user_id'     => Auth::user()->id,
            ]);

            // send notification
            $users = User::where('id', '!=', Auth::user()->id)->get();
            Notification::send($users, new ExpenseCategoryAdded($expensesCategory->id));

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
                'name'   => 'required',
            ]);
            $expensescategory = ExpenseCategory::findOrFail($request->id);

            $expensescategory = ExpenseCategory::whereId($request->id)->update([
                'name'        => $request['name'],
                'description' => $request['description'],
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
            $related_table = Expense::where('expense_category_id', $request->id)->pluck('expense_category_id');
            if($related_table->count() == 0) { 
                $expensescategory = ExpenseCategory::whereId($request->id)->update([
                    'deleted_at' => Carbon::now(),
                ]);
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
                $related_table = Expense::where('expense_category_id', $selected_id)->pluck('expense_category_id');
                if($related_table->count() == 0) {
                    $expensescategories = ExpenseCategory::whereIn('id', $delete_selected_id)->delete();
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
            $expensescategory = ExpenseCategory::withTrashed()->where('id', $request->id)->first();
            if (!$expensescategory) {
                session()->flash('error');
                return redirect()->back();
            }
            $expensescategory->forceDelete();
            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function restore(Request $request)
    {
        try {
            $expensescategory = ExpenseCategory::withTrashed()->where('id', $request->id)->restore();
            if (!$expensescategory) {
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
            $expensescategories = ExpenseCategory::onlyTrashed()->get();
            $trashed = true;
            return view('dashboard.expensescategory.index', compact('expensescategories', 'trashed'));
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
            $expensescategory = ExpenseCategory::findOrFail($id);
            return view('dashboard.expensescategory.show', compact('expensescategory'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

}
