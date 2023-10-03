@extends('layouts.master')

@section('css')
@endsection

@section('page-header')
	<!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Profile') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
				<!-- row -->
				<div class="row row-sm">
					<div class="col-lg-12">
						<div class="row row-sm">
							<div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
								<div class="card ">
									<div class="card-body">
										<div class="counter-status d-flex md-mb-0">
											<div class="counter-icon bg-primary-transparent">
												<i class="icon-layers text-primary"></i>
											</div>
											<div class="mr-auto">
												<h5 class="tx-13">Orders</h5>
												<h2 class="mb-0 tx-22 mb-1 mt-1">1,587</h2>
												<p class="text-muted mb-0 tx-11"><i class="si si-arrow-up-circle text-success mr-1"></i>increase</p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
								<div class="card ">
									<div class="card-body">
										<div class="counter-status d-flex md-mb-0">
											<div class="counter-icon bg-danger-transparent">
												<i class="icon-paypal text-danger"></i>
											</div>
											<div class="mr-auto">
												<h5 class="tx-13">Revenue</h5>
												<h2 class="mb-0 tx-22 mb-1 mt-1">46,782</h2>
												<p class="text-muted mb-0 tx-11"><i class="si si-arrow-up-circle text-success mr-1"></i>increase</p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-sm-12 col-xl-4 col-lg-12 col-md-12">
								<div class="card ">
									<div class="card-body">
										<div class="counter-status d-flex md-mb-0">
											<div class="counter-icon bg-success-transparent">
												<i class="icon-rocket text-success"></i>
											</div>
											<div class="mr-auto">
												<h5 class="tx-13">Product sold</h5>
												<h2 class="mb-0 tx-22 mb-1 mt-1">1,890</h2>
												<p class="text-muted mb-0 tx-11"><i class="si si-arrow-up-circle text-success mr-1"></i>increase</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-12">
						<div class="card mg-b-20">
							<div class="card-body">
								<div class="pl-0">
									<div class="main-profile-overview">
										<div class="main-img-user profile-user">
											@if (Auth::user()->avatar)
												<img alt="" src="{{ asset('attachments/user/'.Auth::user()->avatar) }}"><a class="fas fa-camera profile-edit" href="JavaScript:void(0);"></a>
											@else
												<img alt="" src="{{ asset('attachments/user/user.png') }}"><a class="fas fa-camera profile-edit" href="JavaScript:void(0);"></a>
											@endif
										</div>
										<div class="d-flex justify-content-between mg-b-20">
											<div>
												<h5 class="main-profile-name">{{ Auth::user()->name }}</h5>
												<p class="main-profile-name-text">{{ Auth::user()->email }}</p>
											</div>
										</div><!-- main-profile-bio -->
										<div class="row">
											<div class="col-12">
												<form role="form">
													<div class="form-group">
														<label for="FullName">{{ trans('main.Name') }}</label>
														<input type="text" value="{{ Auth::user()->name }}" id="FullName" class="form-control" readonly>
													</div>
													<div class="form-group">
														<label for="Email">{{ trans('main.Email') }}</label>
														<input type="email" value="{{ Auth::user()->email }}" id="Email" class="form-control" readonly>
													</div>
													<div class="form-group">
														<label for="Mobile">{{ trans('main.Mobile') }}</label>
														<input type="text" value="{{ Auth::user()->mobile }}" id="Phone" class="form-control" readonly>
													</div>
													<div class="form-group">
														<label for="Status">{{ trans('main.Status') }}</label>
														@if (Auth::user()->status == 1)
															<span class="label text-success form-control" readonly>
																<label class="badge badge-success">
																	{{ app()->getLocale() == 'ar' ? 'مفعل' : 'Active' }}
																</label>
															</span>
														@else
															<span class="label text-danger text-center" readonly>
																<label class="badge badge-danger">
																	{{ app()->getLocale() == 'ar' ? 'غير مفعل' : 'InActive' }}
																</label>
															</span>
														@endif
													</div>
													<div class="form-group">
														<label for="Role">{{ trans('main.Role') }}</label>
														<span class="form-control" readonly>
															@if (!empty(Auth::user()->getRoleNames()))
																@foreach (Auth::user()->getRoleNames() as $v)
																	<label class="badge badge-primary">{{ $v }}</label>
																@endforeach
															@endif
														</span>
													</div>
												</form>
											</div>
										</div>
									</div><!-- main-profile-overview -->
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- row closed -->
			</div>
			<!-- Container closed -->
		</div>
		<!-- main-content closed -->
@endsection
@section('js')
@endsection