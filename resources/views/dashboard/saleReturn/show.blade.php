@extends('layouts.master')



@section('css')
    <style>
        @media print {
            #print_Button {
                display: none;
            }
        }
    </style>
    @section('title')
        {{ trans('main.Invoice') }}
    @stop
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Invoice') }} {{trans('main.Returns') }} {{ trans('main.Sales') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection



@section('content')
            <!-- row -->
            <div class="row row-xs wd-xl-60p">
                <div class="col-sm-6 col-md-12">
                    <a class="btn btn-primary btn-ripple mb-3 notPrint" href="{{ route('salesReturns.index') }}">
                        <i class="typcn typcn-arrow-right-outline"></i>&nbsp;{{ trans('main.Back') }}
                    </a>
                </div>
            </div>
            <div class="row row-sm">
                <div class="col-xl-12">
                    <div class=" main-content-body-invoice" id="print">
                        <div class="card card-invoice">
                            <div class="card-body">
                                <div class="invoice-header">
                                    <h1 class="invoice-title">{{ trans('main.Invoice') }} {{ trans('main.Returns') }} {{ trans('main.Sales') }}</h1>
                                    <div class="billed-from">
                                        <h6>{{ $saleReturn->warehouse->name }}</h6>
                                        <p>
                                            {{ $saleReturn->warehouse->city }}, {{ $saleReturn->warehouse->country }}<br>
                                            {{ $saleReturn->warehouse->mobile }}<br>
                                        </p>
                                    </div><!-- billed-from -->
                                </div><!-- invoice-header -->
                                <div class="row mg-t-20">
                                    <div class="col-md">
                                        <label class="tx-gray-600">Billed From</label>
                                        <div class="billed-to">
                                            <h6>{{ $saleReturn->client->name }}</h6>
                                            <p>
                                                {{ $saleReturn->client->adresse }}, {{ $saleReturn->client->city }}, {{ $saleReturn->client->country }}<br>
                                                {{ $saleReturn->client->phone }}<br>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <label class="tx-gray-600">{{ trans('main.Informations') }} {{ trans('main.The') }}{{ trans('main.Invoice') }}</label>
                                        <p class="invoice-info-row">
                                            <span>{{ trans('main.Number')}} {{ trans('main.The') }}{{ trans('main.Invoice') }}</span>
                                            <span>{{ $saleReturn->Ref }}</span>
                                        </p>
                                        <p class="invoice-info-row">
                                            <span>{{ trans('main.date') }} {{ trans('main.The') }}{{ trans('main.Invoice') }}</span>
                                            <span>{{ $saleReturn->date }}</span>
                                        </p>
                                        @if($saleReturn->payment_status != 'paid')
                                            <p class="invoice-info-row">
                                                <span>{{ trans('main.Due') }}</span>
                                                <span>{{ $saleReturn->GrandTotal - $saleReturn->paid_amount }}</span>
                                            </p>
                                        @endif
                                        <p class="invoice-info-row">
                                            <span>{{ trans('main.Payment Status') }}</span>
                                            @if($saleReturn->payment_status == 'paid')
                                                <span class="badge badge-success p-2">{{ trans('main.Paid') }}</span>
                                            @elseif ($saleReturn->payment_status == 'unpaid')
                                                <span class="badge badge-danger p-2">{{ trans('main.Unpaid') }}</span>
                                            @else
                                                <span class="badge badge-warning p-2">{{ trans('main.Partial') }}</span>
                                            @endif
                                        </p>
                                        <p class="invoice-info-row">
                                            <span>{{ trans('main.Added By') }}</span>
                                            <span>{{ $saleReturn->user->name }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="table-responsive mg-t-40">
                                    <table class="table table-invoice border text-md-nowrap mb-0">
                                        <thead>
                                            <tr>
                                                <th class="wd-20p">#</th>
                                                <th class="wd-40p">{{ trans('main.Product') }}</th>
                                                <th class="tx-center">{{ trans('main.Price') }}</th>
                                                <th class="tx-center">{{ trans('main.Quantity') }}</th>
                                                <th class="tx-center">{{ trans('main.Total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $i = 0; ?>
                                            @foreach($saleReturn->details as $details)
                                                <?php $i++; ?>
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td class="tx-12">{{ $details->product->name }}</td>
                                                    <td class="tx-center">{{ $details->product->price }}</td>
                                                    <td class="tx-center">{{ $details->quantity }} ({{ $details->product->unitSale->name }})</td>
                                                    <td class="tx-center">{{ $details->total }}</td>
                                                </tr>
                                            @endforeach
                                            <tr>
                                                <td class="valign-middle" colspan="2" rowspan="4">
                                                    @if($saleReturn->notes)
                                                        <div class="invoice-notes">
                                                            <label class="main-content-label tx-13">{{ trans('main.Notes') }}</label>
                                                            <br>
                                                            <span>{{ $saleReturn->notes }}</span>
                                                        </div><!-- invoice-notes -->
                                                    @endif
                                                </td>
                                                <td class="tx-right">{{ trans('main.Tax') }} ({{ $saleReturn->tax_rate }} %)</td>
                                                <td class="tx-right" colspan="2">{{ $saleReturn->TaxNet }}</td>
                                            </tr>
                                            <tr>
                                                <td class="tx-right">{{ trans('main.Discount') }}</td>
                                                <td class="tx-right" colspan="2">{{ $saleReturn->discount }}</td>
                                            </tr>
                                            <tr>
                                                <td class="tx-right">{{ trans('main.Shipping') }}</td>
                                                <td class="tx-right" colspan="2">{{ $saleReturn->shipping }}</td>
                                            </tr>
                                            <tr>
                                                <td class="tx-right tx-uppercase tx-bold tx-inverse">{{ trans('main.Grand Total') }}</td>
                                                <td class="tx-right" colspan="2">
                                                    <h4 class="tx-primary tx-bold">{{ $saleReturn->GrandTotal }} ({{$currency->symbol}})</h4>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <hr class="mg-b-40">
                                <button class="btn btn-danger  float-left mt-3 mr-2" id="print_Button" onclick="printDiv()"> <i class="mdi mdi-printer ml-1"></i>{{ trans('main.Print') }}</button>
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
    <script type="text/javascript">
        function printDiv() {
            var printContents       = document.getElementById('print').innerHTML;
            var originalContents    = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            location.reload();
        }
    </script>
@endsection