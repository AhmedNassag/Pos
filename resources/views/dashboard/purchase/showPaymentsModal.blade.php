<!--start delete modal -->
<div class="modal fade" id="showPayments{{ $item['id'] }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 style="font-family: 'Cairo', sans-serif;" class="modal-title" id="exampleModalLabel">
                        {{ trans('main.Show') }} {{ trans('main.Payments') }}
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- <form action="{{ route('purchases.destroy', 'test') }}" method="post">
                    {{ method_field('Delete') }}
                    @csrf
                    
                    {{ trans('main.Are You Sure Of Archiving..??') }}
                    
                    <input id="id" type="hidden" name="id" class="form-control" value="{{ $item->id }}">
                    
                </form> -->
                <div class="row">
					<div class="col-12">
						<div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <div class="row" style="max-width:100%;">
                                        <div class="col-1 text-center" style="border: 1px solid gray; padding:10px">#</div>
                                        <div class="col-2 text-center" style="border: 1px solid gray; padding:10px">{{ trans('main.Date') }}</div>
                                        <div class="col-2 text-center" style="border: 1px solid gray; padding:10px">{{ trans('main.Montant') }}</div>
                                        <div class="col-2 text-center" style="border: 1px solid gray; padding:10px">{{ trans('main.Added By') }}</div>
                                        <div class="col-3 text-center" style="border: 1px solid gray; padding:10px">{{ trans('main.Note') }}</div>
                                        <div class="col-2 text-center" style="border: 1px solid gray; padding:10px">{{ trans('main.Actions') }}</div>
                                    </div>
                                    <div class="row" style="max-width:100%;">
                                        <?php $i = 0; ?>
                                        @foreach ($item->facture as $item)
                                            <?php $i++; ?>
                                            <div class="col-1 text-center" style="border: 1px solid gray; padding:10px">{{ $i }}</div>
                                            <div class="col-2 text-center" style="border: 1px solid gray; padding:10px">{{ $item->date }}</div>
                                            <div class="col-2 text-center" style="border: 1px solid gray; padding:10px">{{ $item->montant }}</div>
                                            <div class="col-2 text-center" style="border: 1px solid gray; padding:10px">{{ $item->user->name }}</div>
                                            <div class="col-3 text-center" style="border: 1px solid gray; padding:10px">{{ $item->note }}</div>
                                            <div class="col-2 text-center" style="border: 1px solid gray; padding:10px">
                                                @can('تعديل دفع المشتريات')
                                                    <a href="#" data-toggle="modal" data-target="#editPayment{{ $item->id }}" title="{{ trans('main.Edit') }}">
                                                        <i class="text-info fas fa-pencil-alt"></i> {{ trans('main.Edit') }}
                                                    </a>
                                                @endcan
                                                |
                                                @can('حذف دفع المشتريات')
                                                    <a href="#" data-toggle="modal" data-target="#deletePayment{{ $item->id }}" title="{{ trans('main.Delete') }}">
                                                        <i class="text-danger fas fa-trash-alt"></i> {{ trans('main.Delete') }}
                                                    </a>
                                                @endcan
                                            </div>

                                            @include('dashboard.purchase.editPaymentModal')
                                            @include('dashboard.purchase.deletePaymentModal')
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                        
                <div class="modal-footer mt-3">
                    <button type="button" class="btn btn-danger ripple" data-dismiss="modal">{{ trans('main.Close') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- end delete modal -->