<?php

namespace App\Http\Controllers\Dashboard\Admin;

use DB;
use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Product;
use App\utils\helpers;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\BrandAdded;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class BrandsController extends Controller
{
    use ImageTrait;

    function __construct()
    {
        $this->middleware('permission:عرض الماركات', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة الماركات', ['only' => ['store']]);
        $this->middleware('permission:تعديل الماركات', ['only' => ['update']]);
        $this->middleware('permission:حذف الماركات', ['only' => ['destroy','deleteSelected','forceDelete']]);
        $this->middleware('permission:إستعادة الماركات', ['only' => ['restore']]);
        $this->middleware('permission:أرشيف الماركات', ['only' => ['archived']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
    }
    


    public function index(Request $request)
    {
        try {
            $brands = Brand::where('deleted_at', '=', null)
            ->when($request->name != null,function ($q) use($request){
                return $q->where('name','like','%'.$request->name.'%');
            })
            ->when($request->photo != null,function ($q) use($request){
                return $q->where('image','like','%'.$request->photo.'%');
            })
            // ->orderBy('id', 'desc')
            ->get();
                
            $trashed = false;

            return view('dashboard.brand.index', compact('brands', 'trashed'))
            ->with([
                'name'  => $request->name,
                'photo' => $request->photo,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function show($id)
    {
        try {
            $brand = Brand::findOrFail($id);
            return view('dashboard.brand.show', compact('brand'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name'  => 'required',
                'image' => 'nullable|file|mimes:png,jpg,jpeg',
            ]);

            //upload image
            if ($request->photo) {
                $photo_name = $this->uploadImage($request->photo, 'attachments/brand');
            }

            $brand = Brand::create([
                'name'  => $request['name'],
                'image' => $request['photo'] ? $photo_name : null,
            ]);

            // send notification
            $users = User::where('id', '!=', Auth::user()->id)->get();
            Notification::send($users, new BrandAdded($brand->id));

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
                'name'  => 'required',
                'image' => 'nullable|file|mimes:png,jpg,jpeg',
            ]);
            $brand = Brand::findOrFail($request->id);
            
            //upload image
            if ($request->photo) {
                //remove old photo
                Storage::disk('attachments')->delete('brand/' . $brand->image);
                $photo_name = $this->uploadImage($request->photo, 'attachments/brand');
            }

            $brand = Brand::whereId($request->id)->update([
                'name'  => $request['name'],
                'image' => $request['photo'] ? $photo_name : $brand->image,
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
            $related_table = Product::where('brand_id', $request->id)->pluck('brand_id');
            if($related_table->count() == 0) { 
                $brand = Brand::whereId($request->id)->update([
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
                $related_table = Product::where('brand_id', $request->id)->pluck('brand_id');
                if($related_table->count() == 0) {
                    $brands = Brand::whereIn('id', $delete_selected_id)->delete();
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
            $brand = Brand::withTrashed()->where('id', $request->id)->first();
            if (!$brand) {
                session()->flash('error');
                return redirect()->back();
            }
            Storage::disk('attachments')->delete('brand/' . $brand->image);
            $brand->forceDelete();
            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function restore(Request $request)
    {
        try {
            $brand = Brand::withTrashed()->where('id', $request->id)->restore();
            if (!$brand) {
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
            $brands = Brand::onlyTrashed()->get();
            $trashed = true;
            return view('dashboard.brand.index', compact('brands', 'trashed'));
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
            $brand = Brand::findOrFail($id);
            return view('dashboard.brand.show', compact('brand'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

}
