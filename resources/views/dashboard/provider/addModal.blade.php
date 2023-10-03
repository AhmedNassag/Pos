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
                <form method="POST" action="{{ route('providers.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- name -->
                        <div class="col-6">
                            <label for="name" class="mr-sm-2">{{ trans('main.Name') }} :</label>
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}">
                        </div>
                        <!-- phone -->
                        <div class="col-6">
                            <label for="phone" class="mr-sm-2">{{ trans('main.Phone') }} :</label>
                            <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                        </div>
                        <!-- country -->
                        <div class="col-6">
                            <label for="country" class="mr-sm-2">{{ trans('main.Country') }} :</label>
                            <input id="country" type="text" class="form-control" name="country" value="{{ old('country') }}">
                        </div>
                        <!-- city -->
                        <div class="col-6">
                            <label for="city" class="mr-sm-2">{{ trans('main.City') }} :</label>
                            <input id="city" type="text" class="form-control" name="city" value="{{ old('city') }}">
                        </div>
                        <!-- adress -->
                        <div class="col-6">
                            <label for="adresse" class="mr-sm-2">{{ trans('main.Address') }} :</label>
                            <input id="adresse" type="text" class="form-control" name="adresse" value="{{ old('adresse') }}">
                        </div>
                    </div>

                    <div class="modal-footer mt-3">
                        @can('إضافة الموردين')
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