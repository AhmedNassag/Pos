@extends('layouts.master')



@section('css')
    @section('title')
        {{ trans('main.Warehouse') }}
    @stop
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Warehouse') }}</span>
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
                                    <a class="btn btn-primary btn-ripple" href="{{ route('warehouses.index') }}">
                                        <i class="typcn typcn-arrow-right-outline"></i>&nbsp;{{ trans('main.Back') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body h-100">
                            <div class="row row-sm ">
                                @if($warehouse->photo)
                                    <div class=" col-xl-5 col-lg-12 col-md-12">
                                        <div class="preview-pic tab-content">
                                            <div class="tab-pane active" id="pic-1">
                                                <img src="{{ asset('attachments/warehouse/'.$warehouse->photo) }}" alt="{{ $warehouse->photo }}">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="details col-xl-7 col-lg-12 col-md-12 mt-4 mt-xl-0">
                                    <h4 class="product-title mb-1">{{ $warehouse->name }}</h4>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Name') }}
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $warehouse->name }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Phone') }}
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $warehouse->mobile }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Country') }}
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $warehouse->country }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.City') }}
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $warehouse->city }}</span>
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
                                                                    <th class="text-center">{{ trans('main.Product') }}</th>
                                                                    <th class="text-center">{{ trans('main.Quantity') }}</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($product_warehouses as $item)
                                                                    <tr>
                                                                        <td class="text-center">{{ $item->product->name }}</td>
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