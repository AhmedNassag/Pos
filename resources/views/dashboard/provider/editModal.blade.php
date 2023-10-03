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
                <form action="{{ route('providers.update', 'test') }}" method="post" enctype="multipart/form-data">
                    {{ method_field('patch') }}
                    @csrf
                    <div class="row">
                        <!-- name -->
                        <div class="col-6">
                            <label for="name" class="mr-sm-2">{{ trans('main.Name') }} :</label>
                            <input id="name" type="text" class="form-control" name="name" value="{{ $item->name }}">
                        </div>
                        <!-- phone -->
                        <div class="col-6">
                            <label for="phone" class="mr-sm-2">{{ trans('main.Phone') }} :</label>
                            <input id="phone" type="text" class="form-control" name="phone" value="{{ $item->phone }}">
                        </div>
                        <!-- country -->
                        <div class="col-6">
                            <label for="country" class="mr-sm-2">{{ trans('main.Country') }} :</label>
                            <input id="country" type="text" class="form-control" name="country" value="{{ $item->country }}">
                        </div>
                        <!-- city -->
                        <div class="col-6">
                            <label for="city" class="mr-sm-2">{{ trans('main.City') }} :</label>
                            <input id="city" type="text" class="form-control" name="city" value="{{ $item->city }}">
                        </div>
                        <!-- adresse -->
                        <div class="col-6">
                            <label for="adresse" class="mr-sm-2">{{ trans('main.Address') }} :</label>
                            <input id="adresse" type="text" class="form-control" name="adresse" value="{{ $item->adresse }}">
                        </div>

                        <!-- id -->
                        <div class="col-6">
                            <input id="id" type="hidden" name="id" class="form-control" value="{{ $item->id }}">
                        </div>
                    </div>

                    <div class="modal-footer mt-3">
                        @can('تعديل الموردين')
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
