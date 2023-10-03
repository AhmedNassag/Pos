@extends('layouts.master')



@section('css')
    @section('title')
        {{ trans('main.Stocks') }}
    @stop
    
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Stocks') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection



@section('content')
        <!-- validationNotify -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- success Notify -->
        @if (session()->has('success'))
            <script id="successNotify">
                window.onload = function() {
                    notif({
                        msg: "تمت العملية بنجاح",
                        type: "success"
                    })
                }
            </script>
        @endif

        <!-- error Notify -->
        @if (session()->has('error'))
            <script id="errorNotify">
                window.onload = function() {
                    notif({
                        msg: "لقد حدث خطأ.. برجاء المحاولة مرة أخرى!",
                        type: "error"
                    })
                }
            </script>
        @endif

        <!-- canNotDeleted Notify -->
        @if (session()->has('canNotDeleted'))
            <script id="canNotDeleted">
                window.onload = function() {
                    notif({
                        msg: "لا يمكن الحذف لوجود بيانات أخرى مرتبطة بها..!",
                        type: "error"
                    })
                }
            </script>
        @endif


        <!-- row -->
        <div class="row">
                
            <div class="col-xl-12">
                <div class="card mg-b-20">
                    <div id="print">
                        <div class="card-header pb-0">
                            <!--start title-->
                            <div class="d-flex justify-content-between">
                                <h4 class="card-title mg-b-0">{{ trans('main.Stocks') }}</h4>
                                <i class="mdi mdi-dots-horizontal text-gray"></i>
                            </div>
                            <p class="tx-12 tx-gray-500 mb-2"></p>
                            <!--end title-->
                            <div class="row row-xs">
                                <div class="col-sm-6 col-md-12 notPrint">
                                    @can('طباعة تعديل المخزون')
                                        <button class="btn btn-dark ripple" id="print_Button" onclick="printDiv()">
                                            <i class="mdi mdi-printer"></i>&nbsp;{{ trans('main.Print') }}
                                        </button>
                                    @endcan
                                </div>
                            </div>
                            <!-- start search -->
                            <form action="{{ route('adjustments.stock') }}" method="get">
                                <div class="row mt-5">
                                    <div class="col-3 mr-2 notPrint">
                                        <label for="warehouse" class="">{{ trans('main.Warehouse') }} :</label>
                                        <select class="form-control text-center" name="warehouse_id" placeholder="search">
                                            <option value="" selected>{{ trans('main.All') }}</option>
                                            @foreach($warehouses as $warehouse)
                                                <option value="{{$warehouse->id}}" {{$warehouse->id == $warehouse_id ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-3 notPrint">
                                        <label for="warehouse" class="">{{ trans('main.Product') }} :</label>
                                        <select class="form-control text-center" name="product_id">
                                            <option value="" selected>{{ trans('main.All') }}</option>
                                            @foreach($products as $product)
                                                <option value="{{$product->id}}" {{$product->id == $product_id ? 'selected' : ''}}>{{$product->name}}</option>
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
                                <table class="table table-striped" id="example1" data-page-length='50' style=" text-align: center;">      
                                    <thead>
                                        <tr>
                                            <th class="text-center border-bottom-0">#</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Warehouse')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Product')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Quantity')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 0; ?>
                                        @foreach ($stocks as $item)
                                            <?php $i++; ?>
                                            <tr>
                                                <td class="text-center">{{ $i }}</td>
                                                <td class="text-center">{{ $item->warehouse ? $item->warehouse->name : '' }}</td>
                                                <td class="text-center">{{ $item->product ? $item->product->name : '' }}</td>
                                                <td class="text-center">
                                                    @if($item->qte > $item->product->stock_alert)
                                                        <p class="badge badge-success p-2">{{ $item->qte }}</p> ({{$item->product->unit->name}})
                                                    @else
                                                        <p class="badge badge-danger p-2">{{ $item->qte }}</p> ({{$item->product->unit->name}})
                                                    @endif
                                                </td>
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
