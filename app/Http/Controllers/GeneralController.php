<?php

namespace App\Http\Controllers;

use App\Models\product_warehouse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;


class GeneralController extends Controller
{
    public function index($id)
    {
        if (view()->exists($id)) {
            return view($id);
        }
        else {
            return view('404');
        }
    }


    //show_file
    public function show_file($folder_name, $photo_name)
    {
        $show_file = Storage::disk('attachments')->getDriver()->getAdapter()->applyPathPrefix($folder_name.'/'.$photo_name);
        return response()->file($show_file);
    }


    //download_file
    public function download_file($folder_name,$photo_name)
    {
        $download_file= Storage::disk('attachments')->getDriver()->getAdapter()->applyPathPrefix($folder_name.'/'.$photo_name);
        return response()->download($download_file);
    }


    //allNotifications
    public function allNotifications()
    {
        return view('dashboard.notification.index');
    }


    //markAllAsRead
    public function markAllAsRead(Request $request)
    {
        $userUnreadNotification= auth()->user()->unreadNotifications;
        if ($userUnreadNotification) {
            $userUnreadNotification->markAsRead();
            return back();
        }
    }



    /***** javascripts events *****/
    public function warehouseProducts($warehouse_id)
    {
        $product_warehouses = product_warehouse::where('warehouse_id', $warehouse_id)->where('qte', '!=', null)->where('qte', '>', 0)->where('deleted_at', '=', null)->get();
        foreach($product_warehouses as $product_warehouse) {
            $product_warehouse['product_name'] = $product_warehouse->product->name;
        }

        return response()->json($product_warehouses);
    }
}
