<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Exports\ProductsExport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\product_warehouse;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Models\PurchaseDetail;
use App\Models\PurchaseReturnDetails;
use App\Models\SaleDetail;
use App\Models\SaleReturnDetails;
use App\utils\helpers;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use \Gumlet\ImageResize;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ProductAdded;
use Illuminate\Support\Facades\Notification;
use App\Models\Notification as NotificationModel;

class ProductsController extends BaseController
{
    use ImageTrait;

    function __construct()
    {
        $this->middleware('permission:عرض المنتجات', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة المنتجات', ['only' => ['store']]);
        $this->middleware('permission:تعديل المنتجات', ['only' => ['update']]);
        $this->middleware('permission:حذف المنتجات', ['only' => ['destroy','deleteSelected','forceDelete']]);
        $this->middleware('permission:إستعادة المنتجات', ['only' => ['restore']]);
        $this->middleware('permission:أرشيف المنتجات', ['only' => ['archived']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
    }
    


    public function index(request $request)
    {
        try {
            $products = [];
            $data = Product::with('category', 'brand', 'unit','unitPurchase', 'unitSale')->where('deleted_at', '=', null)
            ->when($request->name != null,function ($q) use($request){
                return $q->where('name','like','%'.$request->name.'%');
            })
            ->when($request->code != null,function ($q) use($request){
                return $q->where('code','like',$request->code.'%');
            })
            ->when($request->price != null,function ($q) use($request){
                return $q->where('price',$request->price);
            })
            ->when($request->category_id != null,function ($q) use($request){
                return $q->where('category_id',$request->category_id);
            })
            ->when($request->brand_id != null,function ($q) use($request){
                return $q->where('brand_id',$request->brand_id);
            })
            ->when($request->unit_id != null,function ($q) use($request){
                return $q->where('unit_id',$request->unit_id);
            })
            ->when($request->photo != null,function ($q) use($request){
                return $q->where('image','like','%'.$request->photo.'%');
            })
            // ->orderBy('id', 'desc')
            ->get();

            foreach ($data as $product) {
                $item['id']               = $product->id;
                $item['code']             = $product->code;
                $item['name']             = $product->name;
                $item['cost']             = $product->cost;
                $item['price']            = $product->price;
                $item['stock_alert']      = $product->stock_alert;
                $item['note']             = $product->note;
                $item['category']         = $product['category']->name;
                $item['brand']            = $product['brand']->name;
                $item['unit']             = $product['unit']->ShortName;
                $item['category_id']      = $product['category']->id;
                $item['brand_id']         = $product['brand']->id;
                $item['unit_id']          = $product['unit']->id;
                $item['unit_purchase_id'] = $product['unitPurchase']->id;
                $item['unit_sale_id']     = $product['unitSale']->id;
                $item['image']            = $product->image;
                $product_warehouses = product_warehouse::where('product_id', $product->id)->where('deleted_at', '=', null)->get();
                $total_qty = 0;
                if($product_warehouses->count() > 0) {
                    foreach ($product_warehouses as $product_warehouse) {
                        $total_qty += $product_warehouse->qte;
                        $item['quantity'] = $total_qty;
                    }
                } else {
                    $item['quantity'] = $total_qty;
                }
                
    
                // $firstImage = explode(',', $product->image);
                // $item['image'] = $firstImage[0];

                $products[] = $item;
            }

            $categories = Category::where('deleted_at', null)->get(['id', 'name']);
            $brands     = Brand::where('deleted_at', null)->get(['id', 'name']);
            $units      = Unit::where('deleted_at', null)->get(['id', 'name']);

            $trashed = false;

            return view('dashboard.product.index', compact('products', 'units', 'categories', 'brands', 'trashed'))
            ->with([
                'name'        => $request->name,
                'code'        => $request->code,
                'price'       => $request->price,
                'category_id' => $request->category_id,
                'brand_id'    => $request->brand_id,
                'unit_id'     => $request->unit_id,
                'photo'       => $request->photo,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function show($id)
    {
        try {
            $product            = Product::with('category', 'brand', 'unit','unitPurchase', 'unitSale')->findOrFail($id);
            $product_warehouses = product_warehouse::where('product_id', $product->id)->where('deleted_at', '=', null)->get();
            $total_qty = 0;
            if($product_warehouses->count() > 0) {
                foreach ($product_warehouses as $product_warehouse) {
                    $total_qty += $product_warehouse->qte;
                    $product['quantity'] = $total_qty;
                }
            } else {
                $product['quantity'] = $total_qty;
            }
            return view('dashboard.product.show', compact('product', 'product_warehouses'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    

    public function store(Request $request)
    {
        try {
            $this->validate($request, [
                'name'             => 'required|unique:products',
                'code'             => 'required|unique:products',
                'cost'             => 'required',
                'price'            => 'required',
                'category_id'      => 'required',
                'brand_id'         => 'required',
                'unit_id'          => 'required',
                'unit_sale_id'     => 'required',
                'unit_purchase_id' => 'required',
                'image'            => 'nullable|file|mimes:png,jpg,jpeg',
            ]);
            
            //upload image
            if ($request->photo) {
                $photo_name = $this->uploadImage($request->photo, 'attachments/product');
            }

            $product = Product::create([
                'name'             => $request['name'],
                'code'             => $request['code'],
                'cost'             => $request['cost'],
                'price'            => $request['price'],
                'note'             => $request['note'],
                'category_id'      => $request['category_id'],
                'brand_id'         => $request['brand_id'],
                'unit_id'          => $request['unit_id'],
                'unit_sale_id'     => $request['unit_sale_id'],
                'unit_purchase_id' => $request['unit_purchase_id'],
                'stock_alert'      => $request['stock_alert'] ? $request['stock_alert'] : 0,
                'is_variant'       => $request['is_variant'] == 'true' ? 1 : 0,
                'image'            => $request['photo'] ? $photo_name : null,
            ]);

            //--store product warehouse
            $warehouses = Warehouse::where('deleted_at', null)->pluck('id')->toArray();
            if ($warehouses) {
                $product_variants = ProductVariant::where('product_id', $product->id)->where('deleted_at', null)->get();
                foreach ($warehouses as $warehouse) {
                    if ($request['is_variant'] == 'true') {
                        foreach ($product_variants as $product_variant) {
                            $product_warehouse[] = [
                                'product_id' => $product->id,
                                'warehouse_id' => $warehouse,
                                'product_variant_id' => $product_variant->id,
                            ];
                        }
                    } else {
                        $product_warehouse[] = [
                            'product_id' => $product->id,
                            'warehouse_id' => $warehouse,
                        ];
                    }
                }
                product_warehouse::insert($product_warehouse);
            }

            // send notification
            $users = User::where('id', '!=', Auth::user()->id)->get();
            Notification::send($users, new ProductAdded($product->id));

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
                'name'             => 'required|unique:products,name,'.$request->id,
                'code'             => 'required|unique:products,code,'.$request->id,
                'cost'             => 'required',
                'price'            => 'required',
                'category_id'      => 'required',
                'brand_id'         => 'required',
                'unit_id'          => 'required',
                'unit_sale_id'     => 'required',
                'unit_purchase_id' => 'required',
                'image'            => 'nullable|file|mimes:png,jpg,jpeg',
            ]);

            $product = Product::findOrFail($request->id);
            
            //upload image
            if ($request->photo) {
                //remove old photo
                Storage::disk('attachments')->delete('product/' . $product->image);
                $photo_name = $this->uploadImage($request->photo, 'attachments/product');
            }

            $product = Product::whereId($request->id)->update([
                'name'             => $request['name'],
                'code'             => $request['code'],
                'cost'             => $request['cost'],
                'price'            => $request['price'],
                'note'             => $request['note'],
                'category_id'      => $request['category_id'],
                'brand_id'         => $request['brand_id'],
                'unit_id'          => $request['unit_id'],
                'unit_sale_id'     => $request['unit_sale_id'],
                'unit_purchase_id' => $request['unit_purchase_id'],
                'stock_alert'      => $request['stock_alert'] ? $request['stock_alert'] : 0,
                'is_variant'       => $request['is_variant'] == 'true' ? 1 : 0,
                'image'            => $request['photo'] ? $photo_name : $product->image,
            ]);


            // Store Variants Product
            $oldVariants = ProductVariant::where('product_id', $request->id)->where('deleted_at', null)->get();
            $warehouses  = Warehouse::where('deleted_at', null)->pluck('id')->toArray();

            if ($request['is_variant'] == 'true') 
            {
                if ($oldVariants->isNotEmpty()) 
                {
                    $new_variants_id = [];
                    $var = 'id';
                    foreach ($request['variants'] as $new_id) 
                    {
                        if (array_key_exists($var, $new_id)) 
                        {
                            $new_variants_id[] = $new_id['id'];
                        } 
                        else 
                        {
                            $new_variants_id[] = 0;
                        }
                    }

                    foreach ($oldVariants as $key => $value) 
                    {
                        $old_variants_id[] = $value->id;
                        // Delete Variant
                        if (!in_array($old_variants_id[$key], $new_variants_id)) 
                        {
                            $ProductVariant = ProductVariant::findOrFail($value->id);
                            $ProductVariant->deleted_at = Carbon::now();
                            $ProductVariant->save();
                            $ProductWarehouse = product_warehouse::where('product_variant_id', $value->id)->update(['deleted_at' => Carbon::now()]);
                        }
                    }

                    foreach ($request['variants'] as $key => $variant) 
                    {
                        if (array_key_exists($var, $variant)) 
                        {
                            $ProductVariantDT = new ProductVariant;
                            //-- Field Required
                            $ProductVariantDT->product_id = $variant['product_id'];
                            $ProductVariantDT->name = $variant['text'];
                            $ProductVariantDT->qty = $variant['qty'];
                            $ProductVariantUP['product_id'] = $variant['product_id'];
                            $ProductVariantUP['name'] = $variant['text'];
                            $ProductVariantUP['qty'] = $variant['qty'];
                        } 
                        else 
                        {
                            $ProductVariantDT = new ProductVariant;
                            //-- Field Required
                            $ProductVariantDT->product_id = $request->id;
                            $ProductVariantDT->name = $variant['text'];
                            $ProductVariantDT->qty = 0.00;
                            $ProductVariantUP['product_id'] = $request->id;
                            $ProductVariantUP['name'] = $variant['text'];
                            $ProductVariantUP['qty'] = 0.00;
                        }
                        if (!in_array($new_variants_id[$key], $old_variants_id)) 
                        {
                            $ProductVariantDT->save();
                            //--Store Product warehouse
                            if ($warehouses) 
                            {
                                $product_warehouse= [];
                                foreach ($warehouses as $warehouse)
                                {
                                    $product_warehouse[] = [
                                        'product_id' => $request->id,
                                        'warehouse_id' => $warehouse,
                                        'product_variant_id' => $ProductVariantDT->id,
                                    ];
                                }
                                product_warehouse::insert($product_warehouse);
                            }
                        } 
                        else 
                        {
                            ProductVariant::where('id', $variant['id'])->update($ProductVariantUP);
                        }
                    }

                } 
                else 
                {
                    $producttWarehouse = product_warehouse::where('product_id', $request->id)->update(['deleted_at' => Carbon::now(),]);
                    foreach ($request['variants'] as $variant) 
                    {
                        $product_warehouse_DT = [];
                        $ProductVarDT = new ProductVariant;
                        //-- Field Required
                        $ProductVarDT->product_id = $request->id;
                        $ProductVarDT->name = $variant['text'];
                        $ProductVarDT->save();
                        //-- Store Product warehouse
                        if ($warehouses) 
                        {
                            foreach ($warehouses as $warehouse) 
                            {
                                $product_warehouse_DT[] = [
                                    'product_id'         => $request->id,
                                    'warehouse_id'       => $warehouse,
                                    'product_variant_id' => $ProductVarDT->id,
                                ];
                            }
                            product_warehouse::insert($product_warehouse_DT);
                        }
                    }
                }
            } 
            else 
            {
                if ($oldVariants->isNotEmpty()) 
                {
                    foreach ($oldVariants as $old_var) 
                    {
                        $var_old = ProductVariant::where('product_id', $old_var['product_id'])->where('deleted_at', null)->first();
                        $var_old->deleted_at = Carbon::now();
                        $var_old->save();
                        $ProducttWarehouse = product_warehouse::where('product_variant_id', $old_var['id'])->update(['deleted_at' => Carbon::now(),]);
                    }
                    if ($warehouses) 
                    {
                        foreach ($warehouses as $warehouse) 
                        {
                            $product_warehouse[] = [
                                'product_id'         => $request->id,
                                'warehouse_id'       => $warehouse,
                                'product_variant_id' => null,
                            ];
                        }
                        product_warehouse::insert($product_warehouse);
                    }
                }
            }

            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
    
    

    public function destroy(Request $request)
    {
        try {
            $related_table   = PurchaseDetail::where('product_id', $request->id)->pluck('product_id');
            $related_table_2 = PurchaseReturnDetails::where('product_id', $request->id)->pluck('product_id');
            $related_table_3 = SaleDetail::where('product_id', $request->id)->pluck('product_id');
            $related_table_4 = SaleReturnDetails::where('product_id', $request->id)->pluck('product_id');
            $related_table_5 = AdjustmentDetail::where('product_id', $request->id)->pluck('product_id');
            if(
                $related_table->count() == 0
                && $related_table_2->count() == 0
                && $related_table_3->count() == 0
                && $related_table_4->count() == 0
                && $related_table_5->count() == 0
            )
            { 
                $product           = Product::whereId($request->id)->delete();
                $product_warehouse = product_warehouse::where('product_id', $request->id)->delete();
                $product_variant   = ProductVariant::where('product_id', $request->id)->delete();
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
                $related_table   = PurchaseDetail::where('product_id', $selected_id)->pluck('product_id');
                $related_table_2 = PurchaseReturnDetails::where('product_id', $request->id)->pluck('product_id');
                $related_table_3 = SaleDetail::where('product_id', $request->id)->pluck('product_id');
                $related_table_4 = SaleReturnDetails::where('product_id', $request->id)->pluck('product_id');
                $related_table_5 = AdjustmentDetail::where('product_id', $request->id)->pluck('product_id');
                if(
                    $related_table->count() == 0 
                    && $related_table_2->count() == 0
                    && $related_table_3->count() == 0
                    && $related_table_4->count() == 0
                    && $related_table_5->count() == 0
                )
                {
                    $products          = Product::whereIn('id', $delete_selected_id)->delete();
                    $product_warehouse = product_warehouse::whereIn('product_id', $delete_selected_id)->delete();
                    $product_variant   = ProductVariant::whereIn('product_id', $delete_selected_id)->delete();
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
            $product            = Product::withTrashed()->where('id', $request->id)->first();
            $product_warehouses = product_warehouse::withTrashed()->where('product_id', $request->id)->forceDelete();
            $product_variants   = ProductVariant::withTrashed()->where('product_id', $request->id)->forceDelete();
            if (!$product) {
                session()->flash('error');
                return redirect()->back();
            }
            Storage::disk('attachments')->delete('product/' . $product->image);
            $product->forceDelete();
            session()->flash('success');
            return redirect()->back();
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }



    public function restore(Request $request)
    {
        try {
            $product = Product::withTrashed()->where('id', $request->id)->restore();
            if (!$product) {
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
            $products = [];
            $data   = Product::onlyTrashed()->get();
            foreach ($data as $product) {
                $item['id']               = $product->id;
                $item['code']             = $product->code;
                $item['name']             = $product->name;
                $item['cost']             = $product->cost;
                $item['price']            = $product->price;
                $item['stock_alert']      = $product->stock_alert;
                $item['note']             = $product->note;
                $item['category']         = $product['category']->name;
                $item['brand']            = $product['brand']->name;
                $item['unit']             = $product['unit']->ShortName;
                $item['category_id']      = $product['category']->id;
                $item['brand_id']         = $product['brand']->id;
                $item['unit_id']          = $product['unit']->id;
                $item['unit_purchase_id'] = $product['unitPurchase']->id;
                $item['unit_sale_id']     = $product['unitSale']->id;
                $item['image']            = $product->image;
                $product_warehouses = product_warehouse::where('product_id', $product->id)->where('deleted_at', '=', null)->get();
                $total_qty = 0;
                if($product_warehouses->count() > 0) {
                    foreach ($product_warehouses as $product_warehouse) {
                        $total_qty += $product_warehouse->qte;
                        $item['quantity'] = $total_qty;
                    }
                } else {
                    $item['quantity'] = $total_qty;
                }
                
    
                // $firstImage = explode(',', $product->image);
                // $item['image'] = $firstImage[0];

                $products[] = $item;
            }

            $categories = Category::where('deleted_at', null)->get(['id', 'name']);
            $brands     = Brand::where('deleted_at', null)->get(['id', 'name']);
            $units      = Unit::where('deleted_at', null)->get(['id', 'name']);
            $trashed = true;
            return view('dashboard.product.index', compact('products', 'categories', 'brands', 'units', 'trashed'));
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
            $product            = Product::with('category', 'brand', 'unit','unitPurchase', 'unitSale')->findOrFail($id);
            $product_warehouses = product_warehouse::where('product_id', $product->id)->where('deleted_at', '=', null)->get();
            $total_qty = 0;
            if($product_warehouses->count() > 0) {
                foreach ($product_warehouses as $product_warehouse) {
                    $total_qty += $product_warehouse->qte;
                    $product['quantity'] = $total_qty;
                }
            } else {
                $product['quantity'] = $total_qty;
            }
            return view('dashboard.product.show', compact('product', 'product_warehouses'));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
