<!-- start add modal -->
<div class="modal" id="addPayment{{ $item->id }}">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content modal-content-demo">
            <div class="modal-header">
                <h6 class="modal-title">{{ trans('main.Add') }}</h6>
                <button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <!-- Form -->
                <form method="POST" action="{{ route('paymentSale.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <!-- date -->
                        <div class="col-4">
                            <label for="date" class="mr-sm-2">{{ trans('main.Date') }} :</label>
                            <input id="date" type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <!-- montant -->
                        <div class="col-4">
                            <label for="montant" class="mr-sm-2">{{ trans('main.Montant') }} :</label>
                            <input id="montant" type="number" class="form-control" name="montant" value="1" value="{{ old('montant') }}" required>
                        </div>
                        <!--notes-->
                        <div class="col-8">
                            <label for="note">{{ trans('main.Notes') }}</label>
                            <textarea id="note" name="notes" type="text" class="form-control" value="{{ old('notes') }}"></textarea>
                        </div>
                    </div>
                        
                    <!-- id -->
                    <input id="id" type="hidden" name="id" class="form-control" value="{{ $item->id }}">
                                            
                    <div class="modal-footer mt-3">
                        @can('إضافة دفع المبيعات')
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