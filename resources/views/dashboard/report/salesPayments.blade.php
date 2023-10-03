@extends('layouts.master')



@section('css')
    @section('title')
    {{ trans('main.Report') }} {{ trans('main.payments') }} {{ trans('main.Sales') }}
    @stop
    
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Report') }} {{ trans('main.payments') }} {{ trans('main.Sales') }}</span>
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
                                <h4 class="card-title mg-b-0">{{ trans('main.Report') }} {{ trans('main.payments') }} {{ trans('main.Sales') }}</h4>
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
                            <form action="{{ route('reports.salesPayments') }}" method="get">
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
                                        <label for="warehouse" class="">{{ trans('main.Warehouse') }} :</label>
                                        <select class="form-control text-center" name="warehouse_id" placeholder="search">
                                            <option value="" selected>{{ trans('main.All') }}</option>
                                            @foreach($warehouses as $warehouse)
                                                <option value="{{$warehouse->id}}" {{$warehouse->id == $warehouse_id ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-2 notPrint">
                                        <label for="warehouse" class="">{{ trans('main.Client') }} :</label>
                                        <select class="form-control text-center" name="client_id">
                                            <option value="" selected>{{ trans('main.All') }}</option>
                                            @foreach($clients as $client)
                                                <option value="{{$client->id}}" {{$client->id == $client_id ? 'selected' : ''}}>{{$client->name}}</option>
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
                            <div class="table-responsive hoverable-table">
                                <table class="table table-striped" id="example" data-page-length='50' style=" text-align: center;">      
                                    <thead>
                                        <tr>
                                            <th class="text-center border-bottom-0">#</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Date')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Ref')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Sale')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Client')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Amount')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Warehouse')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Added By')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 0; ?>
                                        @foreach($salesPayments as $item)
                                            <?php $i++; ?>
                                            <tr>
                                                <td class="text-center">{{ $i }}</td>
                                                <td class="text-center">{{ $item->date }}</td>
                                                <td class="text-center">{{ $item->Ref }}</td>
                                                <td class="text-center">{{ $item->sale->Ref }}</td>
                                                <td class="text-center">{{ $item->sale->client->name }}</td>
                                                <td class="text-center">{{ $item->montant }}</td>
                                                <td class="text-center">{{ $item->sale->warehouse->name }}</td>
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

            <!-- row closed -->
        </div>
        <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection



@section('js')

@endsection
