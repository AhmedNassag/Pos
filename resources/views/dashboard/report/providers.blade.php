@extends('layouts.master')



@section('css')
    @section('title')
        {{ trans('main.Report') }} {{ trans('main.Providers') }}
    @stop
    
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Report') }} {{ trans('main.Providers') }}</span>
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
                                <h4 class="card-title mg-b-0">{{ trans('main.Report') }} {{ trans('main.Providers') }}</h4>
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
                            <form action="{{ route('reports.providers') }}" method="get">
                                <div class="row mt-5">
                                    <div class="col-3 mr-2 notPrint">
                                        <label for="provider" class="">{{ trans('main.Provider') }} :</label>
                                        <select class="form-control text-center" name="provider_id" placeholder="search">
                                            <option value="" selected>{{ trans('main.All') }}</option>
                                            @foreach($providers as $provider)
                                                <option value="{{$provider->id}}" {{$provider->id == $provider_id ? 'selected' : ''}}>{{$provider->name}}</option>
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
                                            <th class="text-center border-bottom-0">{{ trans('main.Code') }}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Provider') }}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Phone') }}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Address') }}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.total') }} {{ trans('main.Sales') }}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.total') }} {{ trans('main.Amount') }}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.total') }} {{ trans('main.Payments') }}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.total') }} {{ trans('main.Due') }}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Actions') }}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.date') }} {{ trans('main.The') }}{{ trans('main.Add') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 0; ?>
                                        @foreach($data as $item)
                                            <?php $i++; ?>
                                            <tr>
                                                <td class="text-center">{{ $i }}</td>
                                                <td class="text-center">{{ $item['code'] }}</td>
                                                <td class="text-center">{{ $item['name'] }}</td>
                                                <td class="text-center">{{ $item['phone'] }}</td>
                                                <td class="text-center">{{ $item['address'] }}</td>
                                                <td class="text-center">{{ $item['total_purchase'] }}</td>
                                                <td class="text-center">{{ $item['total_amount'] }}</td>
                                                <td class="text-center">{{ $item['total_paid'] }}</td>
                                                <td class="text-center">{{ $item['due'] }}</td>
                                                <td class="text-center">
                                                    <a class="btn btn-primary btn-sm" href="{{ route('reports.providerDetails',[$item['id']]) }}" title="{{ trans('main.Reports') }}">
                                                        <i class="text-white fas fa-file-alt"></i>&nbsp;&nbsp;{{ trans('main.Reports') }}
                                                    </a>
                                                </td>
                                                <td class="text-center">{{ $item['created_at']->format('Y-m-d') }}</td>
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
