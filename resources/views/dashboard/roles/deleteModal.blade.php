<!--start delete modal -->
<div class="modal fade" id="delete{{ $role->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title" id="exampleModalLabel">
                    {{ trans('main.Delete') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('roles.delete', 'test') }}" method="post">
                    @csrf
                    
                    {{ trans('main.Are You Sure Of Deleting..??') }}
                    <!-- id -->
                    <input id="id" type="hidden" name="id" class="form-control" value="{{ $role->id }}">
                    
                    <div class="modal-footer mt-3">
                        <button type="submit" class="btn btn-success ripple">{{ trans('main.Confirm') }}</button>
                        <button type="button" class="btn btn-danger ripple" data-dismiss="modal">{{ trans('main.Close') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- end delete modal -->