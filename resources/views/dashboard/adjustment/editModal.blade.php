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
                <form action="{{ route('adjustments.update', 'test') }}" method="post" enctype="multipart/form-data">
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
                        <!-- product_id -->
                        <div class="col-6">
                            <label for="product_id" class="mr-sm-2">{{ trans('main.Product') }} :</label>
                            <select class="form-control select2" name="product_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($products as $product)
                                    <option value="{{$product->id}}" {{ $product->id == $item->details[0]->product_id ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- type -->
                        <div class="col-6">
                            <label for="type" class="mr-sm-2">{{ trans('main.Type') }} :</label>
                            <select class="form-control" name="type" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                <option value="add" {{$item->details[0]->type == 'add' ? 'selected' : ''}}>{{ trans('main.Add') }}</option>
                                <option value="subtract" {{$item->details[0]->type == 'subtract' ? 'selected' : ''}}>{{ trans('main.Subtract') }}</option>
                            </select>
                        </div>
                        <!--quantity-->
                        <div class="col-6">
                            <label for="quantity">{{ trans('main.Quantity') }}</label>
                            <input id="quantity" name="quantity" type="number" class="form-control" value="{{ $item->details[0]->quantity, old('quantity') }}" required oninput="checkQuantity()">
                        </div>
                        <!--notes-->
                        <div class="col-6">
                            <label for="note">{{ trans('main.Notes') }}</label>
                            <textarea id="note" name="notes" type="text" class="form-control" value="{{ $item->notes }}"></textarea>
                        </div>

                        <!-- id -->
                        <div class="col-6">
                            <input id="id" type="hidden" name="id" class="form-control" value="{{ $item->id }}">
                        </div>
                    </div>

                    <div class="modal-footer mt-3">
                        @can('تعديل تعديل المخزون')
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
