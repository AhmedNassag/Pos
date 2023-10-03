@extends('layouts.master')

@section('css')

@endsection

@section('page-header')
	<!-- breadcrumb -->
    <div class="breadcrumb-header justify-content-between">
        <div class="my-auto">
            <div class="d-flex">
                <h4 class="content-title mb-0 my-auto">{{ trans('main.Dashboard') }}</h4>
                <span class="text-muted mt-1 tx-13 mr-2 mb-0">/ {{ trans('main.Change Password') }}</span>
            </div>
        </div>
    </div>
    <!-- breadcrumb -->
@endsection

@section('content')
				<!-- validationNotify -->
				@if ($errors->any())
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				<!-- success Notify -->
				@if (session()->has('success'))
					<script id="successNotify">
						window.onload = function() {
							notif({
								msg: "تمت العملية بنجاح",
								type: "success"
							})
						}
					</script>
				@endif

				<!-- error Notify -->
				@if (session()->has('error'))
					<script id="errorNotify">
						window.onload = function() {
							notif({
								msg: "لقد حدث خطأ.. برجاء المحاولة مرة أخرى!",
								type: "error"
							})
						}
					</script>
				@endif

				<!-- errorOldPassword Notify -->
				@if (session()->has('errorOldPassword'))
					<script id="errorOldPasswordNotify">
						window.onload = function() {
							notif({
								msg: "كلمة المرور القديمة التى أدخلتها غير صحيحة",
								type: "error"
							})
						}
					</script>
				@endif
				
				<!-- row -->
				<div class="row row-sm">
					<div class="col-lg-12">
						<div class="card mg-b-20">
							<div class="card-body">
								<div class="pl-0">
									<div class="main-profile-overview">
										<div class="main-img-user profile-user">
											@if (Auth::user()->avatar)
												<img alt="" src="{{ asset('attachments/user/'.Auth::user()->avatar) }}">
												<a class="fas fa-camera profile-edit" href="JavaScript:void(0);"></a>
											@else
												<img alt="" src="{{ asset('attachments/user/user.png') }}">
												<a class="fas fa-camera profile-edit" href="JavaScript:void(0);"></a>
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
												<form class="form-horizontal"action="{{ route('settings.updatePassword') }}" method="post" enctype="multipart/form-data">
													@csrf
													<div class="form-group">
														<label for="old_password">{{ trans('main.Old Password') }}</label>
														<input type="password" name="old_password" id="old_password" class="form-control" placeholder="{{ trans('main.Old Password') }}" required>
													</div>
													<div class="form-group">
														<label for="password">{{ trans('main.New Password') }}</label>
														<input type="password" name="password" id="password" class="form-control" placeholder="{{ trans('main.New Password') }}" required>
													</div>
													<div class="card-footer text-left">
														<button type="submit" class="btn btn-primary waves-effect waves-light">{{ trans('main.Confirm') }}</button>
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