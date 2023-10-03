<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\RoleAdded;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class RoleController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:عرض الصلاحيات', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة الصلاحيات', ['only' => ['create','store']]);
        $this->middleware('permission:تعديل الصلاحيات', ['only' => ['edit','update']]);
        $this->middleware('permission:حذف الصلاحيات', ['only' => ['destroy']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
    }
    


    public function index(Request $request)
    {
        try {
            $roles = Role::orderBy('id','DESC')->paginate(5);
            return view('dashboard.roles.index',compact('roles'))->with('i', ($request->input('page', 1) - 1) * 5);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    


    public function create()
    {
        try {
            $permission = Permission::get();
            return view('dashboard.roles.create',compact('permission'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    


    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name'       => 'required|unique:roles,name',
                'permission' => 'required',
            ]);

            $role = Role::create(['name' => $request->input('name')]);
            $role->syncPermissions($request->input('permission'));

            // send notification
            $users = User::where('id', '!=', Auth::user()->id)->get();
            Notification::send($users, new RoleAdded($role->id));

            return redirect()->route('roles.index')->with('success','Role created successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function show($id)
    {
        $role            = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")->where("role_has_permissions.role_id",$id)->get();
        return view('dashboard.roles.show',compact('role','rolePermissions'));
    }
    


    public function edit($id)
    {
        try {
            $role            = Role::find($id);
            $permission      = Permission::get();
            $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')->all();
            return view('dashboard.roles.edit',compact('role','permission','rolePermissions'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    


    public function update(Request $request, $id)
    {
        try {
            $this->validate($request, [
                'name'       => 'required',
                'permission' => 'required',
            ]);
            $role = Role::find($id);
            $role->name = $request->input('name');
            $role->save();
            $role->syncPermissions($request->input('permission'));
            return redirect()->route('roles.index')->with('success','Role updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    


    public function destroy($id)
    {
        try {
            DB::table("roles")->where('id',$id)->delete();
            return redirect()->route('roles.index')->with('success','Role deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function delete(Request $request)
    {
        try {
            $role = DB::table("roles")->where('id',$request->id)->delete();
            if (!$role) {
                session()->flash('error');
                return redirect()->back();
            }
            
            session()->flash('success');
            return redirect()->route('roles.index');
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
            $role            = Role::find($id);
            $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")->where("role_has_permissions.role_id",$id)->get();
            return view('dashboard.roles.show',compact('role','rolePermissions'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}