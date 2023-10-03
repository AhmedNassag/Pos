<!-- start edit modal -->
<div class="modal fade" id="edit{{ $item['id'] }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                <form action="{{ route('products.update', 'test') }}" method="post" enctype="multipart/form-data">
                    {{ method_field('patch') }}
                    @csrf
                    <div class="row">

                        <!-- name -->
                        <div class="col-6">
                            <label for="name" class="mr-sm-2">{{ trans('main.Name') }} :</label>
                            <input id="name" type="text" class="form-control" name="name" value="{{ $item['name'] }}">
                        </div>
                        <!-- code -->
                        <div class="col-6">
                            <label for="code" class="mr-sm-2">{{ trans('main.Code') }} :</label>
                            <input id="code" type="text" class="form-control" name="code" value="{{ $item['code'] }}">
                        </div>
                        <!-- category_id -->
                        <div class="col-6">
                            <label for="category_id" class="mr-sm-2">{{ trans('main.Category') }} :</label>
                            <select class="form-control select2" name="category_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($categories as $category)
                                    <option value="{{$category->id}}"  {{ $category->id == $item['category_id'] ? 'selected' : ''}}>{{$category->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- brand_id -->
                        <div class="col-6">
                            <label for="brand_id" class="mr-sm-2">{{ trans('main.Brand') }} :</label>
                            <select class="form-control select2" name="brand_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($brands as $brand)
                                    <option value="{{$brand->id}}" {{ $brand->id == $item['brand_id'] ? 'selected' : ''}}>{{$brand->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- cost -->
                        <div class="col-6">
                            <label for="cost" class="mr-sm-2">{{ trans('main.Cost') }} :</label>
                            <input id="cost" type="text" class="form-control" name="cost" value="{{ $item['cost'] }}">
                        </div>
                        <!-- price -->
                        <div class="col-6">
                            <label for="price" class="mr-sm-2">{{ trans('main.Price') }} :</label>
                            <input id="price" type="text" class="form-control" name="price" value="{{ $item['price'] }}">
                        </div>
                        <!-- unit_id -->
                        <div class="col-6">
                            <label for="unit_id" class="mr-sm-2">{{ trans('main.Unit') }} :</label>
                            <select class="form-control select2" name="unit_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($units as $unit)
                                    <option value="{{$unit->id}}" {{ $unit->id == $item['unit_id'] ? 'selected' : ''}}>{{$unit->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- unit_purchase_id -->
                        <div class="col-6">
                            <label for="unit_purchase_id" class="mr-sm-2">{{ trans('main.Purchase Unit') }} :</label>
                            <select class="form-control select2" name="unit_purchase_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($units as $unit)
                                    <option value="{{$unit->id}}" {{ $unit->id == $item['unit_purchase_id'] ? 'selected' : ''}}>{{$unit->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- unit_sale_id -->
                        <div class="col-6">
                            <label for="unit_sale_id" class="mr-sm-2">{{ trans('main.Sale Unit') }} :</label>
                            <select class="form-control select2" name="unit_sale_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($units as $unit)
                                    <option value="{{$unit->id}}" {{ $unit->id == $item['unit_sale_id'] ? 'selected' : ''}}>{{$unit->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- stock_alert -->
                        <div class="col-6">
                            <label for="stock_alert" class="mr-sm-2">{{ trans('main.Stock Alert') }} :</label>
                            <input id="stock_alert" type="number" class="form-control" name="stock_alert" value="{{ $item['stock_alert'] }}">
                        </div>
                        <!--note-->
                        <div class="col-6">
                            <label for="note">{{ trans('main.Note') }}</label>
                            <textarea id="note" name="note" type="text" class="form-control" value="{{ $item['note'] }}">{{ $item['note'] }}</textarea>
                        </div>

                        <!-- photo -->
                        <div class="col-6">
                            <label for="photo" class="mr-sm-2">{{ trans('main.Photo') }} :</label>
                            <input type="file" name="photo" class="form-control" accept="image/*" data-height="70" value="{{ $item['image'] }}"/>
                            @if($item['image'])
                                <div class="row">
                                    <div class="col-12">
                                        <img class="img-thumbnail m-1" src="{{ asset('attachments/product/'.$item['image']) }}" alt="{{ $item['image'] }}" height="100" width="100">
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- id -->
                        <div class="col-6">
                            <input id="id" type="hidden" name="id" class="form-control" value="{{ $item['id'] }}">
                        </div>
                    </div>

                    <div class="modal-footer mt-3">
                        @can('تعديل المنتجات')
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
