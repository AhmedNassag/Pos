@extends('layouts.master')



@section('css')
    @section('title')
        {{ trans('main.Adjustment') }}
    @stop
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Adjustment') }}</span>
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
                                    <a class="btn btn-primary btn-ripple" href="{{ route('adjustments.index') }}">
                                        <i class="typcn typcn-arrow-right-outline"></i>&nbsp;{{ trans('main.Back') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body h-100">
                            <div class="row row-sm ">
                                @if($adjustment->image)
                                    <div class=" col-xl-5 col-lg-12 col-md-12">
                                        <div class="preview-pic tab-content">
                                            <div class="tab-pane active" id="pic-1">
                                                <img src="{{ asset('attachments/adjustment/'.$adjustment->image) }}" alt="{{ $adjustment->image }}">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="details col-xl-7 col-lg-12 col-md-12 mt-4 mt-xl-0">
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Date') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $adjustment->date }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Warehouse') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $adjustment->warehouse->name }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Product') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $adjustment->details[0]->product->name }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Quantity') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $adjustment->details[0]->quantity }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Type') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $adjustment->details[0]->type == 'add' ? 'إضافة' : 'نقص' }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    @if($adjustment->notes)
                                        <div class="sizes d-flex mt-5">
                                            {{ trans('main.Notes') }} :
                                            <span class="size d-flex" data-toggle="tooltip" title="small">
                                                <label class="mb-0">
                                                    <span class="font-weight-bold">{{ $adjustment->notes }}</span>
                                                </label>
                                            </span>
                                        </div>
                                    @endif
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Added By') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $adjustment->user->name }}</span>
                                            </label>
                                        </span>
                                    </div>
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