@extends('layouts.master')



@section('css')
    @section('title')
        {{ trans('main.Report') }} {{ trans('main.Statistics') }}
    @stop
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Report') }} {{ trans('main.Statistics') }}</span>
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
                                <h4 class="card-title mg-b-0">{{ trans('main.Report') }} {{ trans('main.Statistics') }}</h4>
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
                            <form action="{{ route('reports.statistics') }}" method="get">
                                <div class="row mt-5">
                                    <div class="col-2 mr-2 notPrint">
                                        <label for="from_date" class="">{{ trans('main.From Date') }} :</label>
                                        <input id="from_date" type="date" class="form-control" name="from_date" value="{{ $from_date, date('Y-m-d') }}">
                                    </div>
                                    <div class="col-2 mr-2 notPrint">
                                        <label for="to_date" class="">{{ trans('main.To Date') }} :</label>
                                        <input id="to_date" type="date" class="form-control" name="to_date" value="{{ $to_date, date('Y-m-d') }}">
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
                            <div class="row mt-5">

                                <!-- Sales -->
                                <div class="col-4">
                                    <div class="card overflow-hidden sales-card bg-primary-gradient">
                                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                            <div class="">
                                                <h1 class="mb-3 text-white">{{ trans('main.Sales') }}</h1>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 font-weight-bold mb-1 text-white">$&nbsp;{{ $data['sales']['sum'] ? $data['sales']['sum'] : 0 }}</h4>
                                                        <br>
                                                    </div>
                                                    <span class="float-right my-auto mr-auto">
                                                        <i class="fas fa-arrow-circle-up text-white"></i>
                                                        <span class="text-white op-7">{{ $data['sales']['nmbr'] ? $data['sales']['nmbr'] : 0 }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <span id="compositeline">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
                                    </div>
                                </div>

                                <!-- Sales Payments -->
                                <div class="col-4">
                                    <div class="card overflow-hidden sales-card bg-primary-gradient">
                                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                            <div class="">
                                                <h1 class="mb-3 text-white">{{ trans('main.payments')}}  {{ trans('main.Sales')}}</h1>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 font-weight-bold mb-1 text-white">$&nbsp;{{ $data['paiement_sales']['sum'] ? $data['paiement_sales']['sum'] : 0 }}</h4>
                                                        <br>
                                                        <br>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>

                                <!-- Expenses -->
                                <div class="col-4">
                                    <div class="card overflow-hidden sales-card bg-danger-gradient">
                                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                            <div class="">
                                                <h1 class="mb-3 text-white">{{ trans('main.Expenses') }}</h1>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 font-weight-bold mb-1 text-white">$&nbsp;{{ $data['expenses']['sum'] ? $data['expenses']['sum'] : 0 }}</h4>
                                                        <br>
                                                        <br>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>

                                <!-- Purchases -->
                                <div class="col-4">
                                    <div class="card overflow-hidden sales-card bg-success-gradient">
                                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                            <div class="">
                                                <h1 class="mb-3 text-white">{{ trans('main.Purchases') }}</h1>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 font-weight-bold mb-1 text-white">$&nbsp;{{ $data['purchases']['sum'] ? $data['purchases']['sum'] : 0 }}</h4>
                                                        <br>
                                                    </div>
                                                    <span class="float-right my-auto mr-auto">
                                                        <i class="fas fa-arrow-circle-up text-white"></i>
                                                        <span class="text-white op-7">{{ $data['purchases']['nmbr'] ? $data['purchases']['nmbr'] : 0 }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <span id="compositeline2">3,2,4,6,12,14,8,7,14,16,12,7,8,4,3,2,2,5,6,7</span>
                                    </div>    
                                </div>

                                <!-- Purchases Payments -->
                                <div class="col-4">
                                    <div class="card overflow-hidden sales-card bg-success-gradient">
                                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                            <div class="">
                                                <h1 class="mb-3 text-white">{{ trans('main.payments')}}  {{ trans('main.Purchases') }}</h1>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 font-weight-bold mb-1 text-white">$&nbsp;{{ $data['paiement_purchases']['sum'] ? $data['paiement_purchases']['sum'] : 0 }}</h4>
                                                        <br>
                                                        <br>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>

                                <!-- Profit -->
                                <div class="col-4">
                                    <div class="card overflow-hidden sales-card bg-danger-gradient">
                                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                            <div class="">
                                                <h1 class="mb-3 text-white">{{ trans('main.Profit') }}</h1>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 font-weight-bold mb-1 text-white">$&nbsp;{{ $data['profit'] ? $data['profit'] : 0 }}</h4>
                                                        <br>
                                                        <br>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                                
                                <!-- Sales Returns -->
                                <div class="col-4">
                                    <div class="card overflow-hidden sales-card bg-primary-gradient">
                                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                            <div class="">
                                                <h1 class="mb-3 text-white">{{ trans('main.Returns') }} {{ trans('main.Sales') }}</h1>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 font-weight-bold mb-1 text-white">$&nbsp;{{ $data['returns_sales']['sum'] ? $data['returns_sales']['sum'] : 0 }}</h4>
                                                        <br>
                                                    </div>
                                                    <span class="float-right my-auto mr-auto">
                                                        <i class="fas fa-arrow-circle-up text-white"></i>
                                                        <span class="text-white op-7">{{ $data['returns_sales']['nmbr'] ? $data['returns_sales']['nmbr'] : 0 }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <span id="compositeline3">5,10,5,20,22,12,15,18,20,15,8,12,22,5,10,12,22,15,16,10</span>
						            </div>    
                                </div>

                                <!-- Sales Returns Payments -->
                                <div class="col-4">
                                    <div class="card overflow-hidden sales-card bg-primary-gradient">
                                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                            <div class="">
                                                <h1 class="mb-3 text-white">{{ trans('main.payments')}} {{ trans('main.Returns') }} {{ trans('main.Sales') }}</h1>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 font-weight-bold mb-1 text-white">$&nbsp;{{ $data['PaymentSaleReturns']['sum'] ? $data['PaymentSaleReturns']['sum'] : 0 }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>

                                <!-- Received Payments -->
                                <div class="col-4">
                                    <div class="card overflow-hidden sales-card bg-danger-gradient">
                                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                            <div class="">
                                                <h1 class="mb-3 text-white">{{ trans('main.Received Payments') }}</h1>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 font-weight-bold mb-1 text-white">$&nbsp;{{ $data['payment_received'] ? $data['payment_received'] : 0 }}</h4>
                                                        <br>
                                                        <p class="mb-1 tx-12 text-white op-7">( {{trans('main.payments')}} {{trans('main.Sales')}} + {{trans('main.payments')}} {{trans('main.Returns')}} {{trans('main.Purchases')}} )</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>

                                <!-- Purchases Returns -->
                                <div class="col-4">
                                    <div class="card overflow-hidden sales-card bg-success-gradient">
                                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                            <div class="">
                                                <h1 class="mb-3 text-white">{{ trans('main.Returns') }} {{ trans('main.Purchases') }}</h1>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 font-weight-bold mb-1 text-white">$&nbsp;{{ $data['returns_purchases']['sum'] ? $data['returns_purchases']['sum'] : 0 }}</h4>
                                                        <br>
                                                    </div>
                                                    <span class="float-right my-auto mr-auto">
                                                        <i class="fas fa-arrow-circle-up text-white"></i>
                                                        <span class="text-white op-7">{{ $data['returns_purchases']['nmbr'] ? $data['returns_purchases']['nmbr'] : 0 }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
							            <span id="compositeline4">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
                                    </div>    
                                </div>

                                <!-- Purchases Returns Payments -->
                                <div class="col-4">
                                    <div class="card overflow-hidden sales-card bg-success-gradient">
                                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                            <div class="">
                                                <h1 class="mb-3 text-white">{{ trans('main.payments') }} {{ trans('main.Returns') }} {{trans('main.Purchases') }}</h1>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 font-weight-bold mb-1 text-white">$&nbsp;{{ $data['PaymentPurchaseReturns']['sum'] ? $data['PaymentPurchaseReturns']['sum'] : 0 }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>

                                <!-- Sent Payments -->
                                <div class="col-4">
                                    <div class="card overflow-hidden sales-card bg-danger-gradient">
                                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                            <div class="">
                                                <h1 class="mb-3 text-white">{{ trans('main.Sent Payments') }}</h1>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 font-weight-bold mb-1 text-white">$&nbsp;{{ $data['payment_sent'] ? $data['payment_sent'] : 0 }}</h4>
                                                        <br>
                                                        <p class="mb-1 tx-12 text-white op-7">( {{trans('main.payments')}} {{trans('main.Purchases')}} + {{trans('main.payments')}} {{trans('main.Returns')}} {{trans('main.Sales')}} + {{trans('main.Expenses')}} )</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>

                                <div class="col-8"></div>

                                <!-- Total Payments -->
                                <div class="col-4">
                                    <div class="card overflow-hidden sales-card bg-danger-gradient">
                                        <div class="pl-3 pt-3 pr-3 pb-2 pt-0">
                                            <div class="">
                                                <h1 class="mb-3 text-white">{{ trans('main.Total Payments') }}</h1>
                                            </div>
                                            <div class="pb-0 mt-0">
                                                <div class="d-flex">
                                                    <div class="">
                                                        <h4 class="tx-20 font-weight-bold mb-1 text-white">$&nbsp;{{ $data['paiement_net'] ? $data['paiement_net'] : 0 }}</h4>
                                                        <br>
                                                        <p class="mb-1 tx-12 text-white op-7">( {{trans('main.Received Payments')}} - {{trans('main.Sent Payments')}} )</p>
                                                    </div>
                                                </div>
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
