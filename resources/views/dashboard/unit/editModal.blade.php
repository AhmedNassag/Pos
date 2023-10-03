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
                <form action="{{ route('units.update', 'test') }}" method="post" enctype="multipart/form-data">
                    {{ method_field('patch') }}
                    @csrf
                    <div class="row">

                        <!-- name -->
                        <div class="col-6">
                            <label for="name" class="mr-sm-2">{{ trans('main.Name') }} :</label>
                            <input id="name" type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                        </div>

                        <!-- ShortName -->
                        <div class="col-6">
                            <label for="ShortName" class="mr-sm-2">{{ trans('main.Short Name') }} :</label>
                            <input id="ShortName" type="text" class="form-control" name="ShortName" value="{{ $item->ShortName }}">
                        </div>
                        
                        <!-- base_unit -->
                        <div class="col-6">
                            <label for="base_unit" class="mr-sm-2">{{ trans('main.Base Unit') }} :</label>
                            <select class="form-control select2" name="base_unit">
                                <option value="0">{{trans('main.None')}}</option>
                                @foreach($base_units as $base_unit)
                                    <option value="{{$base_unit->id}}" {{ $base_unit->id == $item->base_unit ? 'selected' : '' }}>{{$base_unit->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- operator_value -->
                        <div class="col-6">
                            <label for="operator_value" class="mr-sm-2">{{ trans('main.Operator Value') }} :</label>
                            <input id="operator_value" type="text" class="form-control" name="operator_value" value="{{ $item->operator_value }}">
                        </div>

                        <!-- id -->
                        <div class="col-6">
                            <input id="id" type="hidden" name="id" class="form-control" value="{{ $item->id }}">
                        </div>
                    </div>

                    <div class="modal-footer mt-3">
                        @can('تعديل الوحدات')
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
