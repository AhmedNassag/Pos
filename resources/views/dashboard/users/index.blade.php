@extends('layouts.master')



@section('css')
    @section('title')
        {{ trans('main.Users') }}
    @stop
@endsection



@section('page-header')
    <!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Users') }}</span>
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

            <!-- row opened -->
            <div class="row row-sm">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="row row-xs wd-xl-60p">
                                <div class="col-sm-6 col-md-3">
                                    @can('إضافة المستخدمين')
                                        <a class=" btn btn-md btn-primary ripple" href="{{ route('users.create') }}">
                                            <i class="typcn typcn-plus"></i>&nbsp; {{ trans('main.Add') }}
                                        </a>
                                    @endcan
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive hoverable-table">
                                <table class="table table-striped" id="example1" data-page-length='50' style=" text-align: center;">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">{{ trans('main.Name') }}</th>
                                            <th class="text-center">{{ trans('main.Email') }}</th>
                                            <th class="text-center">{{ trans('main.Mobile') }}</th>
                                            <th class="text-center">{{ trans('main.Status') }}</th>
                                            <th class="text-center">{{ trans('main.Role') }}</th>
                                            <th class="text-center">{{ trans('main.Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($data as $key => $user)
                                            <tr>
                                                <td class="text-center">{{ ++$i }}</td>
                                                <td class="text-center">{{ $user->name }}</td>
                                                <td class="text-center">{{ $user->email }}</td>
                                                <td class="text-center">{{ $user->mobile }}</td>
                                                <td class="text-center">
                                                    @if ($user->status == 1)
                                                        <span class="label text-success text-center">
                                                            <div class="dot-label bg-success mr-3"></div>
                                                            {{ app()->getLocale() == 'ar' ? 'مفعل' : 'Active' }}
                                                        </span>
                                                    @else
                                                        <span class="label text-danger text-center">
                                                            <div class="dot-label bg-danger mr-3"></div>
                                                            {{ app()->getLocale() == 'ar' ? 'غير مفعل' : 'InActive' }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if (!empty($user->getRoleNames()))
                                                        @foreach ($user->getRoleNames() as $v)
                                                            <label class="badge badge-success">{{ $v }}</label>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @can('تعديل المستخدمين')
                                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-info" title="{{ trans('main.Edit') }}"><i class="las la-pen"></i></a>
                                                    @endcan

                                                    @can('حذف المستخدمين')
                                                        <a class="modal-effect btn btn-sm btn-danger" data-effect="effect-scale" data-user_id="{{ $user->id }}" data-username="{{ $user->name }}" data-toggle="modal" href="#modaldemo8" title="{{ trans('main.Delete') }}"><i class="las la-trash"></i></a>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/div-->

                <!-- Modal effects -->
                <div class="modal" id="modaldemo8">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content modal-content-demo">
                            <div class="modal-header">
                                <h6 class="modal-title">{{ trans('main.Delete') }} {{ trans('main.User') }}</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <form action="{{ route('users.destroy', 'test') }}" method="post">
                                {{ method_field('delete') }}
                                {{ csrf_field() }}
                                <div class="modal-body">
                                    <p>{{ trans('main.Are You Sure Of Deleting..??') }}</p><br>
                                    <input type="hidden" name="user_id" id="user_id" value="">
                                    <input class="form-control" name="username" id="username" type="text" readonly>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('main.Close') }}</button>
                                    @can('حذف المستخدمين')
                                        <button type="submit" class="btn btn-danger">{{ trans('main.Confirm') }}</button>
                                    @endcan
                                </div>
                            </form>
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

@endsection
