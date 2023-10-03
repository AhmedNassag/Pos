<!-- start add modal -->
<div class="modal" id="modaldemo8">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">{{ trans('main.Add') }}</h6>
                <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <!-- Form -->
                <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- date -->
                        <div class="col-6">
                            <label for="date" class="mr-sm-2">{{ trans('main.Date') }} :</label>
                            <input id="date" type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <!-- warehouse_id -->
                        <div class="col-6">
                            <label for="warehouse" class="mr-sm-2">{{ trans('main.Warehouse') }} :</label>
                            <select class="form-control select2" name="warehouse_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- expenses_category_id -->
                        <div class="col-6">
                            <label for="expense_category_id" class="mr-sm-2">{{ trans('main.Expense Category') }} :</label>
                            <select class="form-control select2" name="expense_category_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($expenses_categories as $expenses_category)
                                    <option value="{{$expenses_category->id}}">{{$expenses_category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- amount -->
                        <div class="col-6">
                            <label for="amount" class="mr-sm-2">{{ trans('main.Amount') }} :</label>
                            <input id="amount" type="text" class="form-control" name="amount" value="{{ 1, old('amount') }}" required oninput="checkAmount()">
                        </div>
                        <!-- details -->
                        <div class="col-12">
                            <label for="details" class="mr-sm-2">{{ trans('main.Details') }} :</label>
                            <textarea id="details" type="text" class="form-control" name="details" value="{{ old('details') }}" required></textarea>
                        </div>
                    </div>

                    <div class="modal-footer mt-3">
                        @can('إضافة المصروفات')
                            <button type="submit" class="btn btn-success ripple">{{ trans('main.Confirm') }}</button>
                        @endcan
                        <button type="button" class="btn btn-danger ripple" data-dismiss="modal">{{ trans('main.Close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- end add modal -->