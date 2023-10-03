<!-- start edit modal -->
<div class="modal fade" id="editPayment{{ $item['id'] }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                <form action="{{ route('paymentPurchase.update', 'test') }}" method="post" enctype="multipart/form-data">
                    {{ method_field('patch') }}
                    @csrf
                    <div class="row">
                        <!-- date -->
                        <div class="col-4">
                            <label for="date" class="mr-sm-2">{{ trans('main.Date') }} :</label>
                            <input id="date" type="date" class="form-control" name="date" value="{{ $item->date }}" required>
                        </div>
                        <!-- montant -->
                        <div class="col-4">
                            <label for="montant" class="mr-sm-2">{{ trans('main.Montant') }} :</label>
                            <input id="montant" type="number" class="form-control" name="montant"  value="{{ $item->montant }}" required>
                        </div>
                        <!--notes-->
                        <div class="col-8">
                            <label for="note">{{ trans('main.Notes') }}</label>
                            <textarea id="note" name="notes" type="text" class="form-control" value="{{ $item->notes }}"></textarea>
                        </div>

                        <!-- id -->
                        <div class="col-6">
                            <input id="id" type="hidden" name="id" class="form-control" value="{{ $item->id }}">
                        </div>
                    </div>

                    <div class="modal-footer mt-3">
                        @can('تعديل دفع المشتريات')
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
