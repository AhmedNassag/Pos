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
                <form method="POST" action="{{ route('category.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- name -->
                        <div class="col-6">
                            <label for="name" class="mr-sm-2">{{ trans('main.Name') }} :</label>
                            <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}">
                        </div>
                        <!-- photo -->
                        <div class="col-6">
                            <label for="photo" class="mr-sm-2">{{ trans('main.Photo') }} :</label>
                            <input type="file" name="photo" class="dropify" accept="image/*" data-height="70" />
                        </div>
                    </div>

                    <div class="modal-footer mt-3">
                        @can('إضافة الفئات')
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