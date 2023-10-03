<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Models\Currency;
use App\utils\helpers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\CurrencyAdded;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class CurrencyController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:عرض العملات', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة العملات', ['only' => ['store']]);
        $this->middleware('permission:تعديل العملات', ['only' => ['update']]);
        $this->middleware('permission:حذف العملات', ['only' => ['destroy','deleteSelected','forceDelete']]);
        $this->middleware('permission:إستعادة العملات', ['only' => ['restore']]);
        $this->middleware('permission:أرشيف العملات', ['only' => ['archived']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
    }
    


    public function index(Request $request)
    {
        try {
            $currencies = Currency::where('deleted_at', '=', null)
            ->when($request->name != null,function ($q) use($request){
                return $q->where('name','like','%'.$request->name.'%');
            })
            ->when($request->symbol != null,function ($q) use($request){
                return $q->where('symbol','like','%'.$request->symbol.'%');
            })
            // ->orderBy('id', 'desc')
            ->get();
                
            $trashed = false;

            return view('dashboard.currency.index', compact('currencies', 'trashed'))
            ->with([
                'name'   => $request->name,
                'symbol' => $request->symbol,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function show($id)
    {
        try {
            $currency = Currency::findOrFail($id);
            return view('dashboard.currency.show', compact('currency'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name'   => 'required',
                'symbol' => 'required',
            ]);

            $currency = Currency::create([
                'name'   => $request['name'],
                'symbol' => $request['symbol'],
            ]);

            // send notification
            $users = User::where('id', '!=', Auth::user()->id)->get();
            Notification::send($users, new CurrencyAdded($currency->id));

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
                'symbol' => 'required',
            ]);
            $currency = Currency::findOrFail($request->id);

            $currency = Currency::whereId($request->id)->update([
                'name'   => $request['name'],
                'symbol' => $request['symbol'],
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
                $currency = Currency::whereId($request->id)->update([
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
                    $currencies = Currency::whereIn('id', $delete_selected_id)->delete();
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
            $currency = Currency::withTrashed()->where('id', $request->id)->first();
            if (!$currency) {
                session()->flash('error');
                return redirect()->back();
            }
            $currency->forceDelete();
            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function restore(Request $request)
    {
        try {
            $currency = Currency::withTrashed()->where('id', $request->id)->restore();
            if (!$currency) {
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
            $currencies = Currency::onlyTrashed()->get();
            $trashed = true;
            return view('dashboard.currency.index', compact('currencies', 'trashed'));
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
            $currency = Currency::findOrFail($id);
            return view('dashboard.currency.show', compact('currency'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
