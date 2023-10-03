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
                <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- name -->
                        <div class="col-6">
                            <label for="name" class="mr-sm-2">{{ trans('main.Name') }} :</label>
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}">
                        </div>
                        <!-- code -->
                        <div class="col-6">
                            <label for="code" class="mr-sm-2">{{ trans('main.Code') }} :</label>
                            <input id="code" type="text" class="form-control" name="code" value="{{ old('code') }}">
                        </div>
                        <!-- category_id -->
                        <div class="col-6">
                            <label for="category_id" class="mr-sm-2">{{ trans('main.Category') }} :</label>
                            <select class="form-control select2" name="category_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}">{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- brand_id -->
                        <div class="col-6">
                            <label for="brand_id" class="mr-sm-2">{{ trans('main.Brand') }} :</label>
                            <select class="form-control select2" name="brand_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($brands as $brand)
                                    <option value="{{$brand->id}}">{{$brand->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- cost -->
                        <div class="col-6">
                            <label for="cost" class="mr-sm-2">{{ trans('main.Cost') }} :</label>
                            <input id="cost" type="text" class="form-control" name="cost" value="{{ old('cost') }}">
                        </div>
                        <!-- price -->
                        <div class="col-6">
                            <label for="price" class="mr-sm-2">{{ trans('main.Price') }} :</label>
                            <input id="price" type="text" class="form-control" name="price" value="{{ old('price') }}">
                        </div>
                        <!-- unit_id -->
                        <div class="col-6">
                            <label for="unit_id" class="mr-sm-2">{{ trans('main.Unit') }} :</label>
                            <select class="form-control select2" name="unit_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($units as $unit)
                                    <option value="{{$unit->id}}">{{$unit->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- unit_purchase_id -->
                        <div class="col-6">
                            <label for="unit_purchase_id" class="mr-sm-2">{{ trans('main.Purchase Unit') }} :</label>
                            <select class="form-control select2" name="unit_purchase_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($units as $unit)
                                    <option value="{{$unit->id}}">{{$unit->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- unit_sale_id -->
                        <div class="col-6">
                            <label for="unit_sale_id" class="mr-sm-2">{{ trans('main.Sale Unit') }} :</label>
                            <select class="form-control select2" name="unit_sale_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($units as $unit)
                                    <option value="{{$unit->id}}">{{$unit->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- stock_alert -->
                        <div class="col-6">
                            <label for="stock_alert" class="mr-sm-2">{{ trans('main.Stock Alert') }} :</label>
                            <input id="stock_alert" type="number" class="form-control" name="stock_alert" value="{{ old('stock_alert') }}">
                        </div>
                        <!--note-->
                        <div class="col-6">
                            <label for="note">{{ trans('main.Note') }}</label>
                            <textarea id="note" name="note" type="text" class="form-control" value="{{ old('note') }}"></textarea>
                        </div>
                        <!-- photo -->
                        <div class="col-6">
                            <label for="photo" class="mr-sm-2">{{ trans('main.Photo') }} :</label>
                            <input type="file" name="photo" class="dropify" accept="image/*" data-height="70" />
                        </div>
                    </div>

                    <div class="modal-footer mt-3">
                        @can('إضافة المنتجات')
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