@extends('layouts.master')



@section('css')
    @section('title')
        {{ trans('main.Product') }}
    @stop
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Product') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection



@section('content')
            <!-- row -->
            <div class="row row-sm">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="row row-xs wd-xl-60p">
                                <div class="col-sm-6 col-md-12">
                                    <a class="btn btn-primary btn-ripple" href="{{ route('products.index') }}">
                                        <i class="typcn typcn-arrow-right-outline"></i>&nbsp;{{ trans('main.Back') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body h-100">
                            <div class="row row-sm ">
                                @if($product->image)
                                    <div class=" col-xl-5 col-lg-12 col-md-12">
                                        <div class="preview-pic tab-content">
                                            <div class="tab-pane active" id="pic-1">
                                                <img src="{{ asset('attachments/product/'.$product->image) }}" alt="{{ $product->image }}">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="details col-xl-7 col-lg-12 col-md-12 mt-4 mt-xl-0">
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Name') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $product->name }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Code') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $product->code }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Category') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $product->category->name }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Brand') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $product->brand->name }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Cost') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $product->cost }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Price') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $product->price }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Unit') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $product->unit->name }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Purchase Unit') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $product->unitPurchase->name }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Sale Unit') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $product->unitSale->name }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Stock Alert') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $product->stock_alert }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Note') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $product->note }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Total Quantity') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $product->quantity }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    @if($product_warehouses->count() > 0)
                                        <div class="sizes d-flex mt-5">
                                            <span class="size d-flex" data-toggle="tooltip" title="small">
                                                <label class="mb-0">
                                                    <div class="table-responsive">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th class="text-center">{{ trans('main.Warehouse') }}</th>
                                                                    <th class="text-center">{{ trans('main.Quantity') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($product_warehouses as $item)
                                                                    <tr>
                                                                        <td class="text-center">{{ $item->warehouse->name }}</td>
                                                                        <td class="text-center">{{ $item->qte == null ? 0 : $item->qte }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </label>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /row -->

        </div>
        <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection



@section('js')
    
@endsection