@extends('layouts.master')



@section('css')
    @section('title')
        {{ trans('main.Purchases') }}
    @stop
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Purchases') }}</span>
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

        <!-- paymentGreaterThanDue Notify -->
        @if (session()->has('paymentGreaterThanDue'))
            <script id="paymentGreaterThanDue">
                window.onload = function() {
                    notif({
                        msg: "المبلغ المدفوغ أكبر من المستحق..!",
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
                                <h4 class="card-title mg-b-0">{{ trans('main.Purchases') }}</h4>
                                <i class="mdi mdi-dots-horizontal text-gray"></i>
                            </div>
                            <p class="tx-12 tx-gray-500 mb-2"></p>
                            <!--end title-->
                            <div class="row row-xs">
                                <div class="col-sm-6 col-md-12 notPrint">
                                    @if($trashed == false)
                                        @can('إضافة المشتريات')
                                            <a class="modal-effect btn btn-primary ripple" data-effect="effect-newspaper" data-toggle="modal" href="#modaldemo8">
                                                <i class="mdi mdi-plus"></i>&nbsp;{{ trans('main.Add') }}
                                            </a>
                                        @endcan
                                        <!-- <a class="btn btn-danger ripple float-left" href="{{ route('purchases.archived') }}">
                                            <i class="fe fe-alert-triangle"></i>&nbsp;{{ trans('main.Archive') }} {{ trans('main.Products') }}
                                        </a> -->
                                    @else
                                        @can('عرض المشتريات')
                                            <a class="btn btn-danger ripple float-left" href="{{ route('purchases.index') }}">
                                                {{ trans('main.Back') }} <i class="typcn typcn-arrow-left-outline"></i>
                                            </a>
                                        @endcan
                                    @endif
                                    @can('طباعة المشتريات')
                                        <button class="btn btn-dark ripple" id="print_Button" onclick="printDiv()">
                                            <i class="mdi mdi-printer"></i>&nbsp;{{ trans('main.Print') }}
                                        </button>
                                    @endcan
                                    @can('حذف المشتريات')
                                        <button type="button" class="btn btn-danger ripple" id="btn_delete_selected" title="{{ trans('main.Delete Selected') }}" style="display:none">
                                            <i class="fas fa-trash-alt"></i>&nbsp;{{ trans('main.Delete Selected') }}
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive hoverable-table">
                                <table class="table table-striped" id="example1" data-page-length='50' style="text-align: center;">      
                                    <thead>
                                        <tr>
                                            @if($trashed == false)
                                                <th class="text-center border-bottom-0 notPrint">
                                                    <!-- <input name="select_all" id="example-select-all" type="checkbox" onclick="CheckAll('box1', this)"  oninput="showBtnDeleteSelected()"> -->
                                                </th>
                                            @endif
                                            <th class="text-center border-bottom-0">#</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Ref')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Date')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Provider')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Warehouse')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Grand Total')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Paid Amount')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Due')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Payment Status')}}</th>
                                            <th class="text-center border-bottom-0 notPrint">{{ trans('main.Actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- start search -->
                                        @if($trashed == false)
                                            <tr class="notPrint">
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <form action="{{ route('purchases.index') }}" method="get">
                                                    <td class="text-center">
                                                        <input type="text" class="form-control text-center" name="Ref" placeholder="{{ trans('main.Enter Search Key Of') }} {{ trans('main.Code') }}"  value="{{ $Ref }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="date" class="form-control text-center" name="date" placeholder="{{ trans('main.Enter Search Key Of') }} {{ trans('main.Date') }}"  value="{{ $date }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <select class="form-control text-center" name="provider_id">
                                                            <option value="" selected>{{ trans('main.All') }}</option>
                                                            @foreach($providers as $provider)
                                                                <option value="{{$provider->id}}" {{$provider->id == $provider_id ? 'selected' : ''}}>{{$provider->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <select class="form-control text-center" name="warehouse_id">
                                                            <option value="" selected>{{ trans('main.All') }}</option>
                                                            @foreach($warehouses as $warehouse)
                                                                <option value="{{$warehouse->id}}" {{$warehouse->id == $warehouse_id ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="text" class="form-control text-center" name="GrandTotal" placeholder="{{ trans('main.Enter Search Key Of') }} {{ trans('main.Grand Total') }}" value="{{ $GrandTotal }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="text" class="form-control text-center" name="paid_amount" placeholder="{{ trans('main.Enter Search Key Of') }} {{ trans('main.Paid Amount') }}" value="{{ $paid_amount }}">
                                                    </td>
                                                    <td class="text-center">

                                                    </td>
                                                    <td class="text-center">
                                                        <select class="form-control text-center" name="payment_status">
                                                            <option value="" selected>{{ trans('main.All') }}</option>
                                                            <option value="paid" {{$payment_status == 'paid' ? 'selected' : ''}}>{{ trans('main.Paid') }}</option>
                                                            <option value="unpaid" {{$payment_status == 'unpaid' ? 'selected' : ''}}>{{ trans('main.Unpaid') }}</option>
                                                            <option value="partial" {{$payment_status == 'partial' ? 'selected' : ''}}>{{ trans('main.Partial') }}</option>
                                                        </select>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-info btn-sm pr-3 pl-3 pt-1 pb-1 mt-2 text-center" title="{{ trans('main.Search') }}" onclick="this.form.submit()">
                                                            <i class="far fa-eye-slash"></i>&nbsp;{{ trans('main.Search') }}
                                                        </button>
                                                    </td>
                                                </form>
                                            </tr>
                                        @endif
                                        <!-- end serch -->
                                        <?php $i = 0; ?>
                                        @foreach ($purchases as $item)
                                            <?php $i++; ?>
                                            <tr>
                                                @if($trashed == false)
                                                    <td class="text-center notPrint">
                                                        <input id="delete_selected_input" type="checkbox" value="{{ $item->id }}" class="box1 mr-3" oninput="showBtnDeleteSelected()">
                                                    </td>
                                                @endif
                                                <td class="text-center">{{ $i }}</td>
                                                <td class="text-center">{{ $item->Ref }}</td>
                                                <td class="text-center">{{ $item->date }}</td>
                                                <td class="text-center">{{ $item->provider->name }}</td>
                                                <td class="text-center">{{ $item->warehouse->name }}</td>
                                                <td class="text-center">{{ $item->GrandTotal }}</td>
                                                <td class="text-center">{{ $item->paid_amount }}</td>
                                                <td class="text-center">{{ $item->due }}</td>
                                                <td class="text-center">
                                                    @if($item->payment_status == 'paid')
                                                        <p class="badge badge-success p-2">{{ trans('main.Paid') }}</p>
                                                    @elseif ($item->payment_status == 'unpaid')
                                                        <p class="badge badge-danger p-2">{{ trans('main.Unpaid') }}</p>
                                                    @else
                                                        <p class="badge badge-warning p-2">{{ trans('main.Partial') }}</p>
                                                    @endif                                                    
                                                </td>
                                                <td class="text-center notPrint">
                                                    <div class="dropdown">
                                                        <button aria-expanded="false" aria-haspopup="true" class="btn ripple btn-primary btn-sm" data-toggle="dropdown" type="button"><i class="fas fa-caret-down ml-1"></i>{{ trans('main.Actions') }}</button>
                                                        <div class="dropdown-menu tx-13 bd-primary rounded-5">
                                                            @if($trashed == false)
                                                                @can('عرض المشتريات')
                                                                    <a class="dropdown-item" href="{{ route('purchases.show',[$item->id]) }}" title="{{ trans('main.Show') }}">
                                                                        <i class="text-success fas fa-eye"></i>&nbsp;&nbsp;{{ trans('main.Show') }}
                                                                    </a>
                                                                @endcan
                                                                <!-- <a class="dropdown-item" href="#" data-toggle="modal" data-target="#edit{{ $item->id }}" title="{{ trans('main.Edit') }}">
                                                                    <i class="text-info fas fa-pencil-alt"></i>&nbsp;&nbsp;{{ trans('main.Edit') }}
                                                                </a> -->
                                                                @can('عرض دفع المشتريات')
                                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#showPayments{{ $item->id }}" title="{{ trans('main.Show') }} {{ trans('main.Payments') }}">
                                                                        <i class="text-info fas fa-coins"></i>&nbsp;&nbsp;{{ trans('main.Show') }} {{ trans('main.Payments') }}
                                                                    </a>
                                                                @endcan
                                                                @if($item->due > 0)
                                                                    @can('إضافة دفع المشتريات')
                                                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#addPayment{{ $item->id }}" title="{{ trans('main.Add') }} {{ trans('main.Payment') }}">
                                                                            <i class="text-dark cf cf-zec"></i>&nbsp;&nbsp;{{ trans('main.Add') }} {{ trans('main.Payment') }}
                                                                        </a>
                                                                    @endcan
                                                                @endif
                                                                @can('حذف المشتريات')
                                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete{{ $item->id }}" title="{{ trans('main.Delete') }}">
                                                                        <i class="text-danger fas fa-trash-alt"></i>&nbsp;&nbsp;{{ trans('main.Delete') }}
                                                                    </a>
                                                                @endcan
                                                            @else
                                                                @can('إستعادة المشتريات')
                                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#restore{{ $item->id }}" title="{{ trans('main.Restore') }}">
                                                                        <i class="text-success fas fa-exchange-alt"></i>&nbsp;&nbsp;{{ trans('main.Restore') }}
                                                                    </a>
                                                                @endcan
                                                                @can('حذف المشتريات')
                                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#forceDelete{{ $item->id }}" title="{{ trans('main.Delete') }}">
                                                                        <i class="text-danger fas fa-trash-alt"></i>&nbsp;&nbsp;{{ trans('main.Delete') }}
                                                                    </a>
                                                                @endcan
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                            @include('dashboard.purchase.deleteModal')
                                            @include('dashboard.purchase.forceDeleteModal')
                                            @include('dashboard.purchase.restoreModal')
                                            @include('dashboard.purchase.addPaymentModal')
                                            @include('dashboard.purchase.showPaymentsModal')

                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('dashboard.purchase.addModal')
            @include('dashboard.purchase.deleteSelectedModal')

            <!-- row closed -->
        </div>
        <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection



@section('js')
    <script type="text/javascript">
        //get products for this warehouse
        $(document).ready(function(){
            $('select[name="warehouse_idd"]').on('change',function(){
                var warehouse_id =$(this).val();
                if (warehouse_id) {
                    $.ajax({
                        url:"{{URL::to('warehouseProducts')}}/" + warehouse_id,
                        type:"GET",
                        dataType:"json",
                        success:function(data){
                            $('select[name="product_id[]"]').empty();
                            $('select[name="product_id[]"]').append('<option class="form-control" value="">اختر</option>');
                            $.each(data,function(key,value) {
                                $('select[name="product_id[]"]').append('<option class="form-control" value="'+ value["product_id"] +'">' + value["product_name"] + '</option>');
                            });
                        }
                    });
                } else {
                    console.log('not work')
                }
            });
        });
    </script>
    <script type="text/javascript">
        function addRow()
        {
            $('table[id="myTable"]').append('<tr><td style="width:48%;"><label for="product_id" class="mr-sm-2">{{ trans('main.Product') }} :</label><select class="form-control select2" name="product_id[]" required><option label="{{ trans('main.Choose') }}"></option>@foreach($products as $product)<option value="{{$product->id}}">{{$product->name}}</option>@endforeach</select></td><td style="width:3%;"></td><td style="width:48%;"><label for="quantity" class="mr-sm-2">{{ trans('main.Quantity') }} :</label><input id="quantity" type="number" class="form-control" name="quantity[]" value="0" value="{{ old('quantity') }}" required></td><td style="width:1%;"></td></tr>');
        }
    </script>
    <script type="text/javascript">
        function removeRow()
        {
            var myDiv = document.getElementById("myTable").deleteRow(0);
        }
    </script>
    <script type="text/javascript">
        function checkTaxRate()
        {
            var tax_rate = parseFloat(document.getElementById('tax_rate').value);
            if(tax_rate > 100)
            {
                alert('يجب أن تكون نسبة الضريبة أقل من 100');
                document.getElementById('tax_rate').value = 0;
            }
            if(tax_rate < 0)
            {
                alert('يجب أن تكون نسبة الضريبة 0 أو أكبر من 0');
                document.getElementById('tax_rate').value = 0;
            }
        }
    </script>
    <script type="text/javascript">
        function checkDiscount()
        {
            var discount = parseFloat(document.getElementById('discount').value);
            if(discount < 0)
            {
                alert('يجب أن تكون قيمة الخصم 0 أو أكبر من 0');
                document.getElementById('discount').value = 0;
            }
        }
    </script>
    <script type="text/javascript">
        function checkShipping()
        {
            var shipping = parseFloat(document.getElementById('shipping').value);
            if(shipping < 0)
            {
                alert('يجب أن تكون قيمة الشحن 0 أو أكبر من 0');
                document.getElementById('shipping').value = 0;
            }
        }
    </script>
    <script type="text/javascript">
        function checkQuantity()
        {
            var quantity = parseFloat(document.getElementById('quantity').value);
            if(quantity < 0)
            {
                alert('يجب أن تكون الكميةأكبر من 0');
                document.getElementById('quantity').value = 1;
            }
            if(quantity == 0)
            {
                alert('يجب أن تكون الكميةأكبر من 0');
                document.getElementById('quantity').value = 1;
            }
        }
    </script>
@endsection
