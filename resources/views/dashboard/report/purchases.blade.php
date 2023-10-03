@extends('layouts.master')



@section('css')
    @section('title')
        {{ trans('main.Report') }} {{ trans('main.Purchases') }}
    @stop
    
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Report') }} {{ trans('main.Purchases') }}</span>
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
                                <h4 class="card-title mg-b-0">{{ trans('main.Report') }} {{ trans('main.Purchases') }}</h4>
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
                            <form action="{{ route('reports.purchases') }}" method="get">
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
                                        <label for="provider" class="">{{ trans('main.Provider') }} :</label>
                                        <select class="form-control text-center" name="provider_id" placeholder="search">
                                            <option value="" selected>{{ trans('main.All') }}</option>
                                            @foreach($providers as $provider)
                                                <option value="{{$provider->id}}" {{$provider->id == $provider_id ? 'selected' : ''}}>{{$provider->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div class="col-2 mr-2 notPrint">
                                        <label for="client" class="">{{ trans('main.Payment Status') }} :</label>
                                        <select class="form-control text-center" name="payment_status">
                                            <option value="" selected>{{ trans('main.All') }}</option>
                                            <option value="paid" {{$payment_status == 'paid' ? 'selected' : ''}}>{{ trans('main.Paid') }}</option>
                                            <option value="unpaid" {{$payment_status == 'unpaid' ? 'selected' : ''}}>{{ trans('main.Unpaid') }}</option>
                                            <option value="partial" {{$payment_status == 'partial' ? 'selected' : ''}}>{{ trans('main.Partial') }}</option>
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
                                            <th class="text-center border-bottom-0">{{ trans('main.Date') }}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Ref') }}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Provider') }}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Phone') }}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Address') }}</th>
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
                                        @foreach($purchases as $item)
                                            <?php $i++; ?>
                                            <tr>
                                                <td class="text-center">{{ $i }}</td>
                                                <td class="text-center">{{ $item->date }}</td>
                                                <td class="text-center">{{ $item->Ref }}</td>
                                                <td class="text-center">{{ $item->provider->name }}</td>
                                                <td class="text-center">{{ $item->provider->phone }}</td>
                                                <td class="text-center">{{ $item->provider->adresse }}</td>
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
