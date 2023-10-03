<!-- start edit modal -->
<div class="modal fade" id="edit{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title" id="exampleModalLabel">
                    {{ trans('main.Edit') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Form -->
                <form action="{{ route('expenses.update', 'test') }}" method="post" enctype="multipart/form-data">
                    {{ method_field('patch') }}
                    @csrf
                    <div class="row">

                        <!-- date -->
                        <div class="col-6">
                            <label for="date" class="mr-sm-2">{{ trans('main.Date') }} :</label>
                            <input id="date" type="date" class="form-control" name="date" value="{{ $item->date }}" required>
                        </div>
                        <!-- warehouse_id -->
                        <div class="col-6">
                            <label for="warehouse" class="mr-sm-2">{{ trans('main.Warehouse') }} :</label>
                            <select class="form-control select2" name="warehouse_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{$warehouse->id}}" {{ $warehouse->id == $item->warehouse_id ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- expenses_category_id -->
                        <div class="col-6">
                            <label for="expense_category_id" class="mr-sm-2">{{ trans('main.Expense Category') }} :</label>
                            <select class="form-control select2" name="expense_category_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($expenses_categories as $expenses_category)
                                    <option value="{{$expenses_category->id}}" {{ $expenses_category->id == $item->expense_category_id ? 'selected' : ''}}>{{$expenses_category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- amount -->
                        <div class="col-6">
                            <label for="edit_amount" class="mr-sm-2">{{ trans('main.Amount') }} :</label>
                            <input id="edit_amount" type="text" class="form-control" name="amount" value="{{ $item->amount }}" required oninput="checkAmount()">
                        </div>
                        <!-- details -->
                        <div class="col-12">
                            <label for="details" class="mr-sm-2">{{ trans('main.Details') }} :</label>
                            <textarea id="details" type="text" class="form-control" name="details" value="{{ $item->details }}" required>{{ $item->details }}</textarea>
                        </div>

                        <!-- id -->
                        <div class="col-6">
                            <input id="id" type="hidden" name="id" class="form-control" value="{{ $item->id }}">
                        </div>
                    </div>

                    <div class="modal-footer mt-3">
                        @can('تعديل المصروفات')
                            <button type="submit" class="btn btn-success ripple">{{ trans('main.Confirm') }}</button>
                        @endcan
                        <button type="button" class="btn btn-danger ripple" data-dismiss="modal">{{ trans('main.Close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- end edit modal -->
