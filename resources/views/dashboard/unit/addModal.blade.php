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
                <form method="POST" action="{{ route('units.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- name -->
                        <div class="col-6">
                            <label for="name" class="mr-sm-2">{{ trans('main.Name') }} :</label>
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required>
                        </div>

                        <!-- ShortName -->
                        <div class="col-6">
                            <label for="ShortName" class="mr-sm-2">{{ trans('main.Short Name') }} :</label>
                            <input id="ShortName" type="text" class="form-control" name="ShortName" value="{{ old('ShortName') }}" required>
                        </div>

                        <!-- is_base_unit -->
                        <div class="col-6" style="margin-top: 30px; margin-bottom: 30px;">
                            <div class="row">
                                <div class="col-4">
                                    <label for="is_base_unit" class="mr-sm-2">{{ trans('main.Is Base Unit') }} </label>
                                </div>
                                <div class="col-2">
                                    <input type="radio" name="is_base_unit" value="1" checked onclick="hideBaseUnit()"> {{ trans('main.yes') }}
                                </div>
                                <div class="col-2">
                                    <input type="radio" name="is_base_unit" value="0" onclick="showBaseUnit()"> {{ trans('main.no') }}
                                </div>
                                <div class="col-4"></div>
                            </div>
                        </div>

                        <!-- base_unit -->
                        <div id="base_unit_div" class="col-12" style="display:none;">
                            <div class="row">
                                <!-- base_unit -->
                                <div class="col-6">
                                    <label for="base_unit" class="mr-sm-2">{{ trans('main.Base Unit') }} :</label>
                                    <select class="form-control select2" name="base_unit">
                                        <option label="{{ trans('main.Choose') }}"></option>
                                        @foreach($base_units as $base_unit)
                                            <option value="{{$base_unit->id}}">{{$base_unit->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- operator_value -->
                                <div class="col-6">
                                    <label for="operator_value" class="mr-sm-2">{{ trans('main.Operator Value') }} :</label>
                                    <input id="operator_value" type="text" class="form-control" name="operator_value" value="{{ old('operator_value') }}">
                                </div>
                            <div>
                        </div>
                    </div>
                    </div></div>
                    <div class="modal-footer mt-3">
                        @can('إضافة الوحدات')
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