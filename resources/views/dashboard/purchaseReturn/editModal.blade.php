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
                <form action="{{ route('purchasesReturns.update', 'test') }}" method="post" enctype="multipart/form-data">
                    {{ method_field('patch') }}
                    @csrf
                    <div class="row">
                        <!-- date -->
                        <div class="col-4">
                            <label for="date" class="mr-sm-2">{{ trans('main.Date') }} :</label>
                            <input id="date" type="date" class="form-control" name="date" value="{{ $item->date }}" required>
                        </div>
                        <!-- warehouse_id -->
                        <div class="col-4">
                            <label for="warehouse" class="mr-sm-2">{{ trans('main.Warehouse') }} :</label>
                            <select class="form-control select2" name="warehouse_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{$warehouse->id}}" {{$warehouse->id == $item->warehouse_id ? 'selected' : ''}}>{{$warehouse->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- provider_id -->
                        <div class="col-4">
                            <label for="provider_id" class="mr-sm-2">{{ trans('main.Provider') }} :</label>
                            <select class="form-control select2" name="provider_id" required>
                                <option label="{{ trans('main.Choose') }}"></option>
                                @foreach($providers as $provider)
                                    <option value="{{$provider->id}}" {{$provider->id == $item->provider_id ? 'selected' : ''}}>{{$provider->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- tax_rate -->
                        <div class="col-4">
                            <label for="tax_rate" class="mr-sm-2">{{ trans('main.Tax Rate') }} (%) :</label>
                            <input id="tax_rate" type="number" class="form-control" name="tax_rate" value="0" value="{{ $item->tax_rate }}" required>
                        </div>
                        <!-- discount -->
                        <div class="col-4">
                            <label for="discount" class="mr-sm-2">{{ trans('main.Discount') }} :</label>
                            <input id="discount" type="number" class="form-control" name="discount" value="0" value="{{ $item->discount }}" value="0" required>
                        </div>
                        <!-- shipping -->
                        <div class="col-4">
                            <label for="shipping" class="mr-sm-2">{{ trans('main.Shipping') }} :</label>
                            <input id="shipping" type="number" class="form-control" name="shipping" value="0" value="{{ $item->shipping }}" value="0" required>
                        </div>
                        <!-- status -->
                        <div class="col-4">
                            <label for="status" class="mr-sm-2">{{ trans('main.Status') }} :</label>
                            <select class="form-control" name="status" required>
                                <option value="received" {{ $item->status == "received" ? 'selected' : ''}}>{{ trans('main.Received') }}</option>
                                <option value="ordered" {{ $item->status == "ordered" ? 'selected' : ''}}>{{ trans('main.Ordered') }}</option>
                                <option value="pending" {{ $item->status == "pending" ? 'selected' : ''}}>{{ trans('main.Pending') }}</option>
                            </select>
                        </div>
                        <!--notes-->
                        <div class="col-8">
                            <label for="note">{{ trans('main.Notes') }}</label>
                            <textarea id="note" name="notes" type="text" class="form-control" value="{{ $item->notes }}">{{ $item->notes }}</textarea>
                        </div>
                        <!--details-->
                        <div class="col-12 mt-3" style="border-top: 1px solid grey;">
                            <div id="purchase_details" name="purchase_details">
                                <div class="row mt-3">
                                    <div class="col-4">
                                        <h4>{{ trans('main.Details') }}</h4>
                                    </div>
                                    <div class="col-4"></div>
                                    <div class="col-4">
                                        <!--add_row-->
                                        <button type="button" class="btn btn-primary ripple" onclick="addRow()">{{ trans('main.Add') }} {{ trans('main.Item') }}</button>
                                        <!--remove_row-->
                                        <button type="button" class="btn btn-dark ripple" onclick="removeRow()">{{ trans('main.Delete') }} {{ trans('main.Item') }}</button>
                                    </div>
                                </div>
                                <table id="myTable" class="col-12">
                                    @foreach($item->purchase_details as $purchase_detail)
                                    <tr>
                                        <td style="width:48%;">
                                            <label for="product_id" class="mr-sm-2">{{ trans('main.Product') }} :</label>
                                            <select class="form-control select2" name="product_id[]" required>
                                                <option label="{{ trans('main.Choose') }}"></option>
                                                <option value="{{$purchase_detail->product_id}}">{{$purchase_detail->product_id}}</option>
                                            </select>
                                        </td>
                                        <td style="width:3%;"></td>
                                        <td style="width:48%;">
                                            <label for="quantity" class="mr-sm-2">{{ trans('main.Quantity') }} :</label>
                                            <input id="quantity" type="number" class="form-control" name="quantity[]" value="{{ $purchase_detail->quantity }}" required>
                                        </td>
                                        <td style="width:1%;"></td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>

                        <!-- id -->
                        <div class="col-6">
                            <input id="id" type="hidden" name="id" class="form-control" value="{{ $item->id }}">
                        </div>
                    </div>

                    <div class="modal-footer mt-3">
                        @can('نعديل مرتجع المشتريات')
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
