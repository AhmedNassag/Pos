@extends('layouts.master')



@section('css')
    @section('title')
        {{ trans('main.Report') }} {{ trans('main.Warehouses') }}
    @stop
    
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Report') }} {{ trans('main.Warehouses') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection



@section('content')

        <!-- row -->
        <div class="row">
                
            <div class="col-xl-12">
                <div class="card mg-b-20">
                    <div id="print">
                        <div class="card-header pb-0">
                            <!--start title-->
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-0">{{ trans('main.Report') }} {{ trans('main.Warehouses') }}</h4>
                                <i class="mdi mdi-dots-horizontal text-gray"></i>
                            </div>
                            <p class="tx-12 tx-gray-500 mb-2"></p>
                            <!--end title-->
                            <div class="row row-xs">
                                <div class="col-sm-6 col-md-12 notPrint">
                                    <button class="btn btn-dark ripple" id="print_Button" onclick="printDiv()">
                                        <i class="mdi mdi-printer"></i>&nbsp;{{ trans('main.Print') }}
                                    </button>
                                </div>
                            </div>
                            <!-- start search -->
                            <form action="{{ route('reports.warehouses') }}" method="get">
                                <div class="row mt-5">
                                    <div class="col-2 mr-2 notPrint">
                                        <label for="from_date" class="">{{ trans('main.From Date') }} :</label>
                                        <input id="from_date" type="date" class="form-control" name="from_date" value="{{ $from_date, date('Y-m-d') }}">
                                    </div>
                                    <div class="col-2 mr-2 notPrint">
                                        <label for="to_date" class="">{{ trans('main.To Date') }} :</label>
                                        <input id="to_date" type="date" class="form-control" name="to_date" value="{{ $to_date, date('Y-m-d') }}">
                                    </div>
                                    <div class="col-2 mr-2 notPrint">
                                        <label for="warehouse_id">{{ trans('main.Warehouse') }} :</label>
                                        <select class="form-control text-center" name="warehouse_id" placeholder="search">
                                            <option value="" selected>{{ trans('main.All') }}</option>
                                            @foreach($warehouses as $warehouse)
                                                <option value="{{$warehouse->id}}" {{$warehouse->id == $warehouse_id ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 notPrint">
                                        <label for="button" class=""></label><br>
                                        <button type="button" class="btn btn-info btn-md text-center mt-2" title="{{ trans('main.Search') }}" onclick="this.form.submit()">
                                            <i class="far fa-eye-slash"></i>&nbsp;{{ trans('main.Search') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <!-- end serch -->
                        </div>
                        <div class="card-body">                 
                            <div class="panel panel-primary tabs-style-3">
                                <div class="row">
                                    <div class="col-lg-3 col-md-6">
                                        <div class="card  bg-success-gradient">
                                            <div class="card-body">
                                                <div class="counter-status d-flex md-mb-0">
                                                    <div class="counter-icon">
                                                        <i class="fa fa-shopping-cart"></i>
                                                    </div>
                                                    <div class="mr-auto">
                                                        <h5 class="tx-white-8 mb-3">{{ trans('main.Sales') }}</h5>
                                                        <h2 class="counter mb-0 text-white">{{ $data['sales']->count() }}</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="card  bg-danger-gradient">
                                            <div class="card-body">
                                                <div class="counter-status d-flex md-mb-0">
                                                    <div class="counter-icon text-warning">
                                                        <i class="fa fa-share"></i>
                                                    </div>
                                                    <div class="mr-auto">
                                                        <h5 class="tx-white-8 mb-3">{{ trans('main.Returns') }} {{ trans('main.Sales') }}</h5>
                                                        <h2 class="counter mb-0 text-white">{{ $data['returnSales']->count() }}</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="card  bg-primary-gradient">
                                            <div class="card-body">
                                                <div class="counter-status d-flex md-mb-0">
                                                    <div class="counter-icon text-primary">
                                                        <i class="fa fa-hands"></i>
                                                    </div>
                                                    <div class="mr-auto">
                                                        <h5 class="tx-white-8 mb-3">{{ trans('main.Purchases') }}</h5>
                                                        <h2 class="counter mb-0 text-white">{{ $data['purchases']->count() }}</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <div class="card  bg-warning-gradient">
                                            <div class="card-body">
                                                <div class="counter-status d-flex md-mb-0">
                                                    <div class="counter-icon text-success">
                                                        <i class="fa fa-exchange-alt"></i>
                                                    </div>
                                                    <div class="mr-auto">
                                                        <h5 class="tx-white-8 mb-3">{{ trans('main.Returns') }} {{ trans('main.Purchases') }}</h5>
                                                        <h2 class="counter mb-0 text-white">{{ $data['returnPurchases']->count() }}</h2>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-menu-heading">
                                    <div class="tabs-menu ">
                                        <!-- Tabs -->
                                        <ul class="nav panel-tabs">
                                            <li class=""><a href="#tab1" class="active" data-toggle="tab"><i class="fa fa-shopping-cart"></i> {{ trans('main.Sales') }}</a></li>
                                            <li><a href="#tab2" data-toggle="tab"><i class="fa fa-share"></i> {{ trans('main.Returns') }} {{ trans('main.Sales') }}</a></li>
                                            <li><a href="#tab3" data-toggle="tab"><i class="fa fa-hands"></i> {{ trans('main.Purchases') }}</a></li>
                                            <li><a href="#tab4" data-toggle="tab"><i class="fa fa-exchange-alt"></i> {{ trans('main.Returns') }} {{ trans('main.Purchases') }}</a></li>
                                            <li><a href="#tab5" data-toggle="tab"><i class="fa fa-coins"></i> {{ trans('main.Expenses') }}</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="panel-body tabs-menu-body">
                                    <div class="tab-content">

                                        <!-- Sales -->
                                        <div class="tab-pane active" id="tab1">
                                            <div class="table-responsive hoverable-table">
                                                <table class="table table-striped" id="example1" data-page-length='50' style="text-align: center;">      
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center border-bottom-0">#</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Ref') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Date') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Client') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Warehouse') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Grand Total') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Paid Amount') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Due') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Payment Status') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Added By') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $i = 0; ?>
                                                        @foreach ($data['sales'] as $item)
                                                            <?php $i++; ?>
                                                            <tr>
                                                                <td class="text-center">{{ $i }}</td>
                                                                <td class="text-center">{{ $item->Ref }}</td>
                                                                <td class="text-center">{{ $item->date }}</td>
                                                                <td class="text-center">{{ $item->client->name }}</td>
                                                                <td class="text-center">{{ $item->warehouse->name }}</td>
                                                                <td class="text-center">{{ $item->GrandTotal }}</td>
                                                                <td class="text-center">{{ $item->paid_amount }}</td>
                                                                <td class="text-center">{{ $item->GrandTotal - $item->paid_amount }}</td>
                                                                <td class="text-center">
                                                                    @if($item->payment_status == 'paid')
                                                                        <p class="badge badge-success p-2">{{ trans('main.Paid') }}</p>
                                                                    @elseif ($item->payment_status == 'unpaid')
                                                                        <p class="badge badge-danger p-2">{{ trans('main.Unpaid') }}</p>
                                                                    @else
                                                                        <p class="badge badge-warning p-2">{{ trans('main.Partial') }}</p>
                                                                    @endif                                                    
                                                                </td>
                                                                <td class="text-center">{{ $item->user->name }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>


                                        <!-- Sales Returns -->
                                        <div class="tab-pane" id="tab2">
                                            <div class="table-responsive hoverable-table">
                                                <table class="table table-striped" id="example1" data-page-length='50' style="text-align: center;">      
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center border-bottom-0">#</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Ref') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Date') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Client') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Warehouse') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Grand Total') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Paid Amount') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Due') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Payment Status') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Added By') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $i = 0; ?>
                                                        @foreach ($data['returnSales'] as $item)
                                                            <?php $i++; ?>
                                                            <tr>
                                                                <td class="text-center">{{ $i }}</td>
                                                                <td class="text-center">{{ $item->Ref }}</td>
                                                                <td class="text-center">{{ $item->date }}</td>
                                                                <td class="text-center">{{ $item->client->name }}</td>
                                                                <td class="text-center">{{ $item->warehouse->name }}</td>
                                                                <td class="text-center">{{ $item->GrandTotal }}</td>
                                                                <td class="text-center">{{ $item->paid_amount }}</td>
                                                                <td class="text-center">{{ $item->GrandTotal - $item->paid_amount }}</td>
                                                                <td class="text-center">
                                                                    @if($item->payment_status == 'paid')
                                                                        <p class="badge badge-success p-2">{{ trans('main.Paid') }}</p>
                                                                    @elseif ($item->payment_status == 'unpaid')
                                                                        <p class="badge badge-danger p-2">{{ trans('main.Unpaid') }}</p>
                                                                    @else
                                                                        <p class="badge badge-warning p-2">{{ trans('main.Partial') }}</p>
                                                                    @endif                                                    
                                                                </td>
                                                                <td class="text-center">{{ $item->user->name }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>


                                        <!-- Purchases -->
                                        <div class="tab-pane" id="tab3">
                                            <div class="table-responsive hoverable-table">
                                                <table class="table table-striped" id="example1" data-page-length='50' style="text-align: center;">      
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center border-bottom-0">#</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Ref') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Date') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Provider') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Warehouse') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Grand Total') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Paid Amount') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Due') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Payment Status') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Added By') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $i = 0; ?>
                                                        @foreach ($data['purchases'] as $item)
                                                            <?php $i++; ?>
                                                            <tr>
                                                                <td class="text-center">{{ $i }}</td>
                                                                <td class="text-center">{{ $item->Ref }}</td>
                                                                <td class="text-center">{{ $item->date }}</td>
                                                                <td class="text-center">{{ $item->provider->name }}</td>
                                                                <td class="text-center">{{ $item->warehouse->name }}</td>
                                                                <td class="text-center">{{ $item->GrandTotal }}</td>
                                                                <td class="text-center">{{ $item->paid_amount }}</td>
                                                                <td class="text-center">{{ $item->GrandTotal - $item->paid_amount }}</td>
                                                                <td class="text-center">
                                                                    @if($item->payment_status == 'paid')
                                                                        <p class="badge badge-success p-2">{{ trans('main.Paid') }}</p>
                                                                    @elseif ($item->payment_status == 'unpaid')
                                                                        <p class="badge badge-danger p-2">{{ trans('main.Unpaid') }}</p>
                                                                    @else
                                                                        <p class="badge badge-warning p-2">{{ trans('main.Partial') }}</p>
                                                                    @endif                                                    
                                                                </td>
                                                                <td class="text-center">{{ $item->user->name }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>


                                        <!-- Purchases Returns -->
                                        <div class="tab-pane" id="tab4">
                                            <div class="table-responsive hoverable-table">
                                                <table class="table table-striped" id="example1" data-page-length='50' style="text-align: center;">      
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center border-bottom-0">#</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Ref') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Date') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Provider') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Warehouse') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Grand Total') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Paid Amount') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Due') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Payment Status') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Added By') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $i = 0; ?>
                                                        @foreach ($data['returnPurchases'] as $item)
                                                            <?php $i++; ?>
                                                            <tr>
                                                                <td class="text-center">{{ $i }}</td>
                                                                <td class="text-center">{{ $item->Ref }}</td>
                                                                <td class="text-center">{{ $item->date }}</td>
                                                                <td class="text-center">{{ $item->provider->name }}</td>
                                                                <td class="text-center">{{ $item->warehouse->name }}</td>
                                                                <td class="text-center">{{ $item->GrandTotal }}</td>
                                                                <td class="text-center">{{ $item->paid_amount }}</td>
                                                                <td class="text-center">{{ $item->GrandTotal - $item->paid_amount }}</td>
                                                                <td class="text-center">
                                                                    @if($item->payment_status == 'paid')
                                                                        <p class="badge badge-success p-2">{{ trans('main.Paid') }}</p>
                                                                    @elseif ($item->payment_status == 'unpaid')
                                                                        <p class="badge badge-danger p-2">{{ trans('main.Unpaid') }}</p>
                                                                    @else
                                                                        <p class="badge badge-warning p-2">{{ trans('main.Partial') }}</p>
                                                                    @endif                                                    
                                                                </td>
                                                                <td class="text-center">{{ $item->user->name }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>


                                        <!-- Expenses -->
                                        <div class="tab-pane" id="tab5">
                                            <div class="table-responsive hoverable-table">
                                                <table class="table table-striped" id="example1" data-page-length='50' style=" text-align: center;">      
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center border-bottom-0">#</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Date') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Amount') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Details') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Expense Category') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Warehouse') }}</th>
                                                            <th class="text-center border-bottom-0">{{ trans('main.Added By') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php $i = 0; ?>
                                                        @foreach ($data['expenses'] as $item)
                                                            <?php $i++; ?>
                                                            <tr>
                                                                <td class="text-center">{{ $i }}</td>
                                                                <td class="text-center">{{ $item->date }}</td>
                                                                <td class="text-center">{{ $item->amount }}</td>
                                                                <td class="text-center">{{ $item->details }}</td>
                                                                <td class="text-center">{{ $item->expense_category ? $item->expense_category->name : '' }}</td>
                                                                <td class="text-center">{{ $item->warehouse ? $item->warehouse->name : '' }}</td>
                                                                <td class="text-center">{{ $item->user->name }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
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
@endsection



@section('js')

@endsection
