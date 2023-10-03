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
                <form method="POST" action="{{ route('adjustments.store') }}" enctype="multipart/form-data">
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
                        <!-- product_id -->
                        <div class="col-6">
                            <label for="product_id" class="mr-sm-2">{{ trans('main.Product') }} :</label>
                            <select class="form-control select2" name="product_id" required>

                            </select>
                        </div>
                        <!-- type -->
                        <div class="col-6">
                            <label for="type" class="mr-sm-2">{{ trans('main.Type') }} :</label>
                            <select class="form-control" name="type" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                <option value="add">{{ trans('main.Add') }}</option>
                                <option value="subtract">{{ trans('main.Subtract') }}</option>
                            </select>
                        </div>
                        <!--quantity-->
                        <div class="col-6">
                            <label for="quantity">{{ trans('main.Quantity') }}</label>
                            <input id="quantity" name="quantity" type="number" class="form-control" value="{{ 1, old('quantity') }}" required oninput="checkQuantity()">
                        </div>
                        <!--notes-->
                        <div class="col-6">
                            <label for="note">{{ trans('main.Notes') }}</label>
                            <textarea id="note" name="notes" type="text" class="form-control" value="{{ old('notes') }}"></textarea>
                        </div>
                    </div>

                    <div class="modal-footer mt-3">
                        @can('إضافة تعديل المخزون')
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