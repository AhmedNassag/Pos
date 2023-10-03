<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Exports\ProvidersExport;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\PurchaseReturn;
use App\utils\helpers;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ProviderAdded;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class ProvidersController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:عرض الموردين', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة الموردين', ['only' => ['store']]);
        $this->middleware('permission:تعديل الموردين', ['only' => ['update']]);
        $this->middleware('permission:حذف الموردين', ['only' => ['destroy','deleteSelected','forceDelete']]);
        $this->middleware('permission:إستعادة الموردين', ['only' => ['restore']]);
        $this->middleware('permission:أرشيف الموردين', ['only' => ['archived']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
    }
    


    public function index(Request $request)
    {
        try {
            $providers = Provider::where('deleted_at', '=', null)
                ->when($request->name != null,function ($q) use($request){
                    return $q->where('name','like','%'.$request->name.'%');
                })
                ->when($request->code != null,function ($q) use($request){
                    return $q->where('code','like','%'.$request->code.'%');
                })
                ->when($request->phone != null,function ($q) use($request){
                    return $q->where('phone','like','%'.$request->phone.'%');
                })
                ->when($request->country != null,function ($q) use($request){
                    return $q->where('country','like','%'.$request->country.'%');
                })
                ->when($request->city != null,function ($q) use($request){
                    return $q->where('city','like','%'.$request->city.'%');
                })
                ->when($request->adresse != null,function ($q) use($request){
                    return $q->where('adresse','like','%'.$request->adresse.'%');
                })
                // ->orderBy('id', 'desc')
                ->get();
                
            $trashed = false;

            return view('dashboard.provider.index', compact('providers', 'trashed'))
            ->with([
                'name'    => $request->name,
                'code'    => $request->code,
                'phone'   => $request->phone,
                'country' => $request->country,
                'city'    => $request->city,
                'adresse' => $request->adresse,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function show($id)
    {
        try {
            $provider = Provider::findOrFail($id);
            return view('dashboard.provider.show', compact('provider'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name'    => 'required',
                'adresse' => 'required',
                'phone'   => 'required',
                'country' => 'required',
                'city'    => 'required',
            ]);

            $provider = Provider::create([
                'name'    => $request['name'],
                'code'    => $this->getNumberOrder(),
                'adresse' => $request['adresse'],
                'phone'   => $request['phone'],
                'country' => $request['country'],
                'city'    => $request['city'],
            ]);

            // send notification
            $users = User::where('id', '!=', Auth::user()->id)->get();
            Notification::send($users, new ProviderAdded($provider->id));

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
                'adresse' => 'required',
                'phone'   => 'required',
                'country' => 'required',
                'city'    => 'required',
            ]);

            $provider = Provider::whereId($request->id)->update([
                'name'    => $request['name'],
                'adresse' => $request['adresse'],
                'phone'   => $request['phone'],
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
            $related_table   = Purchase::where('provider_id', $request->id)->pluck('provider_id');
            $related_table_2 = PurchaseReturn::where('provider_id', $request->id)->pluck('provider_id');
            if($related_table->count() == 0 && $related_table_2->count() == 0) { 
                $provider = Provider::whereId($request->id)->update([
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
                $related_table   = Purchase::where('provider_id', $selected_id)->pluck('provider_id');
                $related_table_2 = PurchaseReturn::where('provider_id', $selected_id)->pluck('provider_id');
                if($related_table->count() == 0 && $related_table_2->count() == 0) {
                    $providers = Provider::whereIn('id', $delete_selected_id)->delete();
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
            $provider = Provider::withTrashed()->where('id', $request->id)->first();
            if (!$provider) {
                session()->flash('error');
                return redirect()->back();
            }
            $provider->forceDelete();
            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function restore(Request $request)
    {
        try {
            $provider = Provider::withTrashed()->where('id', $request->id)->restore();
            if (!$provider) {
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
            $providers = Provider::onlyTrashed()->get();
            $trashed   = true;
            return view('dashboard.provider.index', compact('providers', 'trashed'));
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
            $provider = Provider::findOrFail($id);
            return view('dashboard.provider.show', compact('provider'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function getNumberOrder()
    {
        $last = DB::table('providers')->latest('id')->first();
        if ($last) {
            $code = $last->code + 1;
        } else {
            $code = 1;
        }
        return $code;
    }
}
