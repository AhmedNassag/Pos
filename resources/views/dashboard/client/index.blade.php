@extends('layouts.master')



@section('css')
    @section('title')
        {{ trans('main.Clients') }}
    @stop
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Clients') }}</span>
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
                                <h4 class="card-title mg-b-0">{{ trans('main.Clients') }}</h4>
                                <i class="mdi mdi-dots-horizontal text-gray"></i>
                            </div>
                            <p class="tx-12 tx-gray-500 mb-2"></p>
                            <!--end title-->
                            <div class="row row-xs">
                                <div class="col-sm-6 col-md-12 notPrint">
                                    @if($trashed == false)
                                        @can('إضافة العملاء')    
                                            <a class="modal-effect btn btn-primary ripple" data-effect="effect-newspaper" data-toggle="modal" href="#modalClient">
                                                <i class="mdi mdi-plus"></i>&nbsp;{{ trans('main.Add') }}
                                            </a>
                                        @endcan
                                        @can('أرشيف العملاء')
                                            <a class="btn btn-danger ripple float-left" href="{{ route('clients.archived') }}">
                                                <i class="fe fe-alert-triangle"></i>&nbsp;{{ trans('main.Archive') }} {{ trans('main.Clients') }}
                                            </a>
                                        @endcan
                                    @else
                                        @can('عرض العملاء')
                                            <a class="btn btn-danger ripple float-left" href="{{ route('clients.index') }}">
                                                {{ trans('main.Back') }} <i class="typcn typcn-arrow-left-outline"></i>
                                            </a>
                                        @endcan
                                    @endif
                                    @can('طباعة العملاء')
                                        <button class="btn btn-dark ripple" id="print_Button" onclick="printDiv()">
                                            <i class="mdi mdi-printer"></i>&nbsp;{{ trans('main.Print') }}
                                        </button>
                                    @endcan
                                    @can('حذف العملاء')
                                        <button type="button" class="btn btn-danger ripple" id="btn_delete_selected" title="{{ trans('main.Delete Selected') }}" style="display:none">
                                            <i class="fas fa-trash-alt"></i>&nbsp;{{ trans('main.Delete Selected') }}
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive hoverable-table">
                                <table class="table table-striped" id="example1" data-page-length='50' style=" text-align: center;">      
                                    <thead>
                                        <tr>
                                            @if($trashed == false)
                                                <th class="text-center border-bottom-0 notPrint">
                                                    <input name="select_all" id="example-select-all" type="checkbox" onclick="CheckAll('box1', this)"  oninput="showBtnDeleteSelected()">
                                                </th>
                                            @endif
                                            <th class="text-center border-bottom-0">#</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Name')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Code')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Phone')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Country')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.City')}}</th>
                                            <th class="text-center border-bottom-0">{{ trans('main.Address')}}</th>
                                            <th class="text-center border-bottom-0 notPrint">{{ trans('main.Actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- start search -->
                                        @if($trashed == false)
                                            <tr class="notPrint">
                                                <td class="text-center"></td>
                                                <td class="text-center"></td>
                                                <form action="{{ route('clients.index') }}" method="get">
                                                    <td class="text-center">
                                                        <input type="text" class="form-control text-center" name="name" placeholder="{{ trans('main.Enter Search Key Of') }} {{ trans('main.Name') }}" value="{{ $name }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="text" class="form-control text-center" name="code" placeholder="{{ trans('main.Enter Search Key Of') }} {{ trans('main.Code') }}" value="{{ $code }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="text" class="form-control text-center" name="phone" placeholder="{{ trans('main.Enter Search Key Of') }} {{ trans('main.Phone') }}" value="{{ $phone }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="text" class="form-control text-center" name="country" placeholder="{{ trans('main.Enter Search Key Of') }} {{ trans('main.Country') }}" value="{{ $country }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="text" class="form-control text-center" name="city" placeholder="{{ trans('main.Enter Search Key Of') }} {{ trans('main.City') }}" value="{{ $city }}">
                                                    </td>
                                                    <td class="text-center">
                                                        <input type="text" class="form-control text-center" name="adresse" placeholder="{{ trans('main.Enter Search Key Of') }} {{ trans('main.Address') }}" value="{{ $adresse }}">
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
                                        @foreach ($clients as $item)
                                            <?php $i++; ?>
                                            <tr>
                                                @if($trashed == false)
                                                    <td class="text-center notPrint">
                                                        <input id="delete_selected_input" type="checkbox" value="{{ $item->id }}" class="box1 mr-3" oninput="showBtnDeleteSelected()">
                                                    </td>
                                                @endif
                                                <td class="text-center">{{ $i }}</td>
                                                <td class="text-center">{{ $item->name }}</td>
                                                <td class="text-center">{{ $item->code }}</td>
                                                <td class="text-center">{{ $item->phone }}</td>
                                                <td class="text-center">{{ $item->country }}</td>
                                                <td class="text-center">{{ $item->city }}</td>
                                                <td class="text-center">{{ $item->adresse }}</td>
                                                <td class="text-center notPrint">
                                                    <div class="dropdown">
                                                        <button aria-expanded="false" aria-haspopup="true" class="btn ripple btn-primary btn-sm" data-toggle="dropdown" type="button"><i class="fas fa-caret-down ml-1"></i>{{ trans('main.Actions') }}</button>
                                                        <div class="dropdown-menu tx-13 bd-primary rounded-5">
                                                            @if($trashed == false)
                                                                @can('عرض العملاء')
                                                                    <a class="dropdown-item" href="{{ route('clients.show',[$item->id]) }}" title="{{ trans('main.Show') }}">
                                                                        <i class="text-success fas fa-eye"></i>&nbsp;&nbsp;{{ trans('main.Show') }}
                                                                    </a>
                                                                @endcan
                                                                @can('تعديل العملاء')
                                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#edit{{ $item->id }}" title="{{ trans('main.Edit') }}">
                                                                        <i class="text-info fas fa-pencil-alt"></i>&nbsp;&nbsp;{{ trans('main.Edit') }}
                                                                    </a>
                                                                @endcan
                                                                @can('حذف العملاء')
                                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#delete{{ $item->id }}" title="{{ trans('main.Delete') }}">
                                                                        <i class="text-danger icon ion-md-paper-plane"></i>&nbsp;&nbsp;{{ trans('main.Archive') }}
                                                                    </a>
                                                                @endcan
                                                            @else
                                                                @can('إستعادة العملاء')
                                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#restore{{ $item->id }}" title="{{ trans('main.Restore') }}">
                                                                        <i class="text-success fas fa-exchange-alt"></i>&nbsp;&nbsp;{{ trans('main.Restore') }}
                                                                    </a>
                                                                @endcan
                                                                @can('حذف العملاء')
                                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#forceDelete{{ $item->id }}" title="{{ trans('main.Delete') }}">
                                                                        <i class="text-danger fas fa-trash-alt"></i>&nbsp;&nbsp;{{ trans('main.Delete') }}
                                                                    </a>
                                                                @endcan
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                            @include('dashboard.client.editModal')
                                            @include('dashboard.client.deleteModal')
                                            @include('dashboard.client.forceDeleteModal')
                                            @include('dashboard.client.restoreModal')

                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('dashboard.client.addModal')
            @include('dashboard.client.deleteSelectedModal')

            <!-- row closed -->
        </div>
        <!-- Container closed -->
    </div>
    <!-- main-content closed -->
@endsection



@section('js')

@endsection
