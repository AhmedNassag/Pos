<?php

namespace App\Http\Controllers\Dashboard;

use DB;
use Hash;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\UserAdded;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:عرض المستخدمين', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة المستخدمين', ['only' => ['create','store']]);
        $this->middleware('permission:تعديل المستخدمين', ['only' => ['edit','update']]);
        $this->middleware('permission:حذف المستخدمين', ['only' => ['destroy']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
    }
    


    public function index(Request $request)
    {
        try {
            $data = User::orderBy('id','DESC')->paginate(5);
            return view('dashboard.users.index',compact('data'))->with('i', ($request->input('page', 1) - 1) * 5);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    


    public function show($id)
    {
        try {
            $user = User::find($id);
            return view('dashboard.users.show',compact('user'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    


    public function create()
    {
        try {
            $roles = Role::pluck('name','name')->all();
            return view('dashboard.users.create',compact('roles'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    


    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name'       => 'required',
                'email'      => 'required|email|unique:users,email',
                'mobile'     => 'required|unique:users,mobile',
                // 'password'   => 'required|same:confirm-password',
                'roles_name' => 'required'
            ]);
            $input = $request->all();
            $input['password'] = Hash::make($input['password']);
            $user = User::create($input);
            $user->assignRole($request->input('roles_name'));

            // send notification
            $users = User::where('id', '!=', Auth::user()->id)->get();
            Notification::send($users, new UserAdded($user->id));

            session()->flash('success');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    


    public function edit($id)
    {
        try {
            $user     = User::find($id);
            $roles    = Role::pluck('name','name')->all();
            $userRole = $user->roles->pluck('name','name')->all();
            return view('dashboard.users.edit',compact('user','roles','userRole'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    

        
    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'name'       => 'required',
                'email'      => 'required|email|unique:users,email,'.$id,
                'mobile'     => 'required|unique:users,mobile,'.$id,
                // 'password' => 'same:confirm-password',
                'roles_name' => 'required'
            ]);
            $user  = User::find($id);
            $input = $request->all();
            if(!empty($input['password'])) {
                $input['password'] = Hash::make($input['password']);
            } else {
                $input['password'] = $user->password;
            }
            $user->update($input);
            DB::table('model_has_roles')->where('model_id',$id)->delete();
            $user->assignRole($request->input('roles_name'));

            session()->flash('success');
            return redirect()->route('users.index');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    


    public function destroy(Request $request)
    {
        try {
            $related_table   = ExpenseCategory::where('user_id', $request->user_id)->pluck('user_id');
            $related_table_2 = Expense::where('user_id', $request->user_id)->pluck('user_id');
            if($related_table->count() == 0 || $related_table_2->count() == 0) { 
                $user = User::whereId($request->user_id)->delete();
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



    public function showNotification($id,$notification_id)
    {
        try {
            $notification = NotificationModel::findOrFail($notification_id);
            $notification->update([
                'read_at' => now(),
            ]);
            $user = User::findOrFail($id);
            return view('dashboard.users.show',compact('user'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}