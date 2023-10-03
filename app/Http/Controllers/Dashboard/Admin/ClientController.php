<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Exports\ClientsExport;
use App\Models\Client;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use DB;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ClientAdded;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class ClientController extends BaseController
{
    function __construct()
    {
        $this->middleware('permission:عرض العملاء', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة العملاء', ['only' => ['store']]);
        $this->middleware('permission:تعديل العملاء', ['only' => ['update']]);
        $this->middleware('permission:حذف العملاء', ['only' => ['destroy','deleteSelected','forceDelete']]);
        $this->middleware('permission:إستعادة العملاء', ['only' => ['restore']]);
        $this->middleware('permission:أرشيف العملاء', ['only' => ['archived']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
    }
    


    public function index(Request $request)
    {
        try {
            $clients = Client::where('deleted_at', '=', null)
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

            return view('dashboard.client.index', compact('clients', 'trashed'))
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
            $client = Client::findOrFail($id);
            return view('dashboard.client.show', compact('client'));
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

            $client = Client::create([
                'name'    => $request['name'],
                'code'    => $this->getNumberOrder(),
                'adresse' => $request['adresse'],
                'phone'   => $request['phone'],
                'country' => $request['country'],
                'city'    => $request['city'],
            ]);

            // send notification
            $users = User::where('id', '!=', Auth::user()->id)->get();
            Notification::send($users, new ClientAdded($client->id));

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

            $client = Client::whereId($request->id)->update([
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
            $related_table   = Sale::where('client_id', $request->id)->pluck('client_id');
            $related_table_2 = SaleReturn::where('client_id', $request->id)->pluck('client_id');
            if($related_table->count() == 0 && $related_table_2->count() == 0) { 
                $client = Client::whereId($request->id)->update([
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
                $related_table   = Sale::where('client_id', $request->id)->pluck('client_id');
                $related_table_2 = SaleReturn::where('client_id', $request->id)->pluck('client_id');
                if($related_table->count() == 0 && $related_table_2->count() == 0) { 
                    $clients = Client::whereIn('id', $delete_selected_id)->delete();
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
            $client = Client::withTrashed()->where('id', $request->id)->first();
            if (!$client) {
                session()->flash('error');
                return redirect()->back();
            }
            $client->forceDelete();
            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function restore(Request $request)
    {
        try {
            $client = Client::withTrashed()->where('id', $request->id)->restore();
            if (!$client) {
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
            $clients = Client::onlyTrashed()->get();
            $trashed = true;
            return view('dashboard.client.index', compact('clients', 'trashed'));
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
            $client = Client::findOrFail($id);
            return view('dashboard.client.show', compact('client'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    


    public function getNumberOrder()
    {
        $last = DB::table('clients')->latest('id')->first();
        if ($last) {
            $code = $last->code + 1;
        } else {
            $code = 1;
        }
        return $code;
    }
}
