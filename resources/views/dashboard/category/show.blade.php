@extends('layouts.master')



@section('css')
    <!--Internal  Nice-select css  -->
    <!-- <link href="{{URL::asset('assets/plugins/jquery-nice-select/css/nice-select.css')}}" rel="stylesheet"/> -->
    <!-- Internal Select2 css -->
    <!-- <link href="{{URL::asset('assets/plugins/select2/css/select2.min.css')}}" rel="stylesheet"> -->
    @section('title')
        {{ trans('main.Category') }}
    @stop
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Category') }}</span>
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
                                    <a class="btn btn-primary btn-ripple" href="{{ route('category.index') }}">
                                        <i class="typcn typcn-arrow-right-outline"></i>&nbsp;{{ trans('main.Back') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body h-100">
                            <div class="row row-sm ">
                                @if($category->photo)
                                    <div class=" col-xl-5 col-lg-12 col-md-12">
                                        <div class="preview-pic tab-content">
                                            <div class="tab-pane active" id="pic-1">
                                                <img src="{{ asset('attachments/category/'.$category->photo) }}" alt="{{ $category->photo }}">
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="details col-xl-7 col-lg-12 col-md-12 mt-4 mt-xl-0">
                                    <h4 class="product-title mb-1">{{ app()->getLocale() == 'ar' ? $category->name_ar : $category->name_en }}</h4>
                                    <div class="sizes d-flex mt-5">
                                        @if(app()->getLocale() == 'ar')
                                            {{ trans('main.Name') }} {{ trans('main.In English') }} :
                                        @else
                                            {{ trans('main.Name') }} {{ trans('main.In Arabic') }} :
                                        @endif
                                        <span class="size d-flex" data-toggle="tooltip" title="small">
                                            <label class="mb-0">
                                                <span class="font-weight-bold">{{ app()->getLocale() == 'ar' ? $category->name_en : $category->name_ar }}</span>
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
    <!-- Internal Select2.min js -->
    <!-- <script src="{{URL::asset('assets/plugins/select2/js/select2.min.js')}}"></script> -->
    <!-- <script src="{{URL::asset('assets/js/select2.js')}}"></script> -->
    <!-- Internal Nice-select js-->
    <!-- <script src="{{URL::asset('assets/plugins/jquery-nice-select/js/jquery.nice-select.js')}}"></script> -->
    <!-- <script src="{{URL::asset('assets/plugins/jquery-nice-select/js/nice-select.js')}}"></script> -->
@endsection