@extends('layouts.master')



@section('css')
    <!--Internal  Font Awesome -->
    <link href="{{URL::asset('assets/plugins/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
    <!--Internal  treeview -->
    <link href="{{URL::asset('assets/plugins/treeview/treeview-rtl.css')}}" rel="stylesheet" type="text/css" />
    @section('title')
        {{ trans('main.Add') }} {{ trans('main.Role') }}
    @stop
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Roles') }}</h4><span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Add') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection



@section('content')

            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <button aria-label="Close" class="close" data-dismiss="alert" type="button">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>{{ trans('main.Error')}}</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {!! Form::open(array('route' => 'roles.store','method'=>'POST')) !!}
            <!-- row -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card mg-b-20">
                        <div class="card-body">
                            <div class="main-content-label mg-b-5">
                                <div class="pull-right">
                                    <a class="btn btn-primary ripple" href="{{ route('roles.index') }}">
                                        <i class="typcn typcn-arrow-right-outline"></i>&nbsp;{{ trans('main.Back') }}
                                    </a>
                                </div>
                            </div>
                            <div class="main-content-label mg-b-5 mt-3">
                                <div class="col-xs-7 col-sm-7 col-md-7">
                                    <div class="form-group">
                                        <p>{{ trans('main.Name')}} :</p>
                                        {!! Form::text('name', null, array('class' => 'form-control')) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- col -->
                                <div class="col-lg-12">
                                    <ul id="treeview1">
                                        <li>
                                            <a href="#">{{ trans('main.Roles')}}</a>
                                            <ul>
                                                <li class="row">
                                                    @foreach($permission as $value)
                                                        <label class="col-3" style="font-size: 16px; margin:5px;">
                                                            {{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }} {{ $value->name }}
                                                        </label>
                                                        <br>
                                                    @endforeach
                                                </li>
                                            </ul>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                                    <button type="submit" class="btn btn-main-primary">{{ trans('main.Confirm')}}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- row closed -->
        </div>
        <!-- Container closed -->
    </div>
    <!-- main-content closed -->
    
    {!! Form::close() !!}
@endsection



@section('js')
    <!-- Internal Treeview js -->
    <script src="{{URL::asset('assets/plugins/treeview/treeview.js')}}"></script>
@endsection