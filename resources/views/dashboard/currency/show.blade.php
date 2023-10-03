@extends('layouts.master')



@section('css')
    @section('title')
        {{ trans('main.Currency') }}
    @stop
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Currency') }}</span>
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
                                    <a class="btn btn-primary btn-ripple" href="{{ route('currencies.index') }}">
                                        <i class="typcn typcn-arrow-right-outline"></i>&nbsp;{{ trans('main.Back') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body h-100">
                            <div class="row row-sm ">
                                @if($currency->image)
                                    <div class=" col-xl-5 col-lg-12 col-md-12">
                                        <div class="preview-pic tab-content">
                                            <div class="tab-pane active" id="pic-1">
                                                <img src="{{ asset('attachments/currency/'.$currency->image) }}" alt="{{ $currency->image }}">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="details col-xl-7 col-lg-12 col-md-12 mt-4 mt-xl-0">
                                    <h4 class="product-title mb-1">{{ $currency->name }}</h4>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Name') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $currency->name }}</span>
                                            </label>
                                        </span>
                                    </div>
                                    <div class="sizes d-flex mt-5">
                                        {{ trans('main.Symbol') }} :
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ $currency->symbol }}</span>
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