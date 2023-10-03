<div class="modal fade" id="delete_selected" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title" id="exampleModalLabel">
                    {{ trans('main.Delete Selected') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="{{ route('currencies.deleteSelected') }}" method="POST">
                {{ csrf_field() }}
                <div class="modal-body">
                    {{ trans('main.Are You Sure Of Deleting..??') }}
                    <input class="text" type="hidden" id="delete_selected_id" name="delete_selected_id" value=''>
                </div>

                <div class="modal-footer mt-3">
                    @can('حذف العملات')
                        <button type="submit" class="btn btn-success ripple">{{ trans('main.Confirm') }}</button>
                    @endcan
                    <button type="button" class="btn btn-danger ripple" data-dismiss="modal">{{ trans('main.Close') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>