<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\categoryRequest;
use App\Repositories\Category\CategoryInterface;

class CategoryController extends Controller
{
    protected $category;

    public function __construct(CategoryInterface $category)
    {
        $this->category = $category;
        $this->middleware('permission:عرض الفئات', ['only' => ['index','show']]);
        $this->middleware('permission:إضافة الفئات', ['only' => ['store']]);
        $this->middleware('permission:تعديل الفئات', ['only' => ['update']]);
        $this->middleware('permission:حذف الفئات', ['only' => ['destroy','deleteSelected','forceDelete']]);
        $this->middleware('permission:إستعادة الفئات', ['only' => ['restore']]);
        $this->middleware('permission:أرشيف الفئات', ['only' => ['archived']]);
        $this->middleware('permission:الإشعارات', ['only' => ['showNotification']]);
    }



    public function index(Request $request)
    {
        $categories = $this->category->index($request);
        $trashed    = false;
        return view('dashboard.category.index', compact('categories', 'trashed'))
        ->with([
            'name'  => $request->name,
            'photo' => $request->photo,
        ]);
    }



    public function show($id)
    {
        $category = $this->category->show($id);
        return view('dashboard.category.show', compact('category'));
    }



    public function store(categoryRequest $request)
    {
        return $this->category->store($request);
    }



    public function update(categoryRequest $request)
    {
        return $this->category->update($request);
    }



    public function destroy(Request $request)
    {
        return $this->category->destroy($request);
    }



    public function deleteSelected(Request $request)
    {
        return $this->category->deleteSelected($request);
    }



    public function forceDelete(Request $request)
    {
        return $this->category->forceDelete($request);
    }



    public function restore(Request $request)
    {
        return $this->category->restore($request);
    }



    public function archived()
    {
        $categories = $this->category->archived();
        $trashed    = true;
        return view('dashboard.category.index', compact('categories', 'trashed'));
    }



    public function showNotification($route_id,$notification_id)
    {
        $category = $this->category->showNotification($route_id,$notification_id);
        return view('dashboard.category.show', compact('category'));
    }
}
