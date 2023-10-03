@extends('layouts.master')
@section('css')
<!--  Owl-carousel css-->
<link href="{{URL::asset('assets/plugins/owl-carousel/owl.carousel.css')}}" rel="stylesheet" />
<!-- Maps css -->
<link href="{{URL::asset('assets/plugins/jqvmap/jqvmap.min.css')}}" rel="stylesheet">
@endsection
@section('page-header')
				<!-- breadcrumb -->
				<div class="breadcrumb-header justify-content-between">
					<div class="left-content">
						<div>
						  <h2 class="main-content-title tx-24 mg-b-1 mg-b-lg-1">Hi, {{ Auth::user()->name }}</h2>
						  <p class="mg-b-0">Sales monitoring dashboard template.</p>
						</div>
					</div>
					<div class="main-dashboard-header-right"></div>
				</div>
				<!-- /breadcrumb -->
@endsection
@section('content')
				<!-- row -->
				<div class="row row-sm">
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-primary-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-3 tx-20 text-white">{{ trans('main.Sales') }} {{ trans('main.Today') }}</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">${{ $today_sales['today_sales_sum'] ? $today_sales['today_sales_sum'] : 0 }}</h4>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-up text-white"></i>
											<span class="text-white op-7"> + {{ $today_sales['today_sales_count'] ? $today_sales['today_sales_count'] : 0 }}</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-danger-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-3 tx-20 text-white">{{ trans('main.Returns') }} {{ trans('main.Sales') }} {{ trans('main.Today') }}</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">${{ $today_return_sales['today_return_sales_sum'] ? $today_return_sales['today_return_sales_sum'] : 0 }}</h4>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-down text-white"></i>
											<span class="text-white op-7"> + {{ $today_return_sales['today_return_sales_count'] ? $today_return_sales['today_return_sales_count'] : 0 }}</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline2" class="pt-1">3,2,4,6,12,14,8,7,14,16,12,7,8,4,3,2,2,5,6,7</span>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-success-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-3 tx-20 text-white">{{ trans('main.Purchases') }} {{ trans('main.Today') }}</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">${{ $today_purchases['today_purchases_sum'] ? $today_purchases['today_purchases_sum'] : 0 }}</h4>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-up text-white"></i>
											<span class="text-white op-7"> + {{ $today_purchases['today_purchases_count'] ? $today_purchases['today_purchases_count'] : 0 }}</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline3" class="pt-1">5,10,5,20,22,12,15,18,20,15,8,12,22,5,10,12,22,15,16,10</span>
						</div>
					</div>
					<div class="col-xl-3 col-lg-6 col-md-6 col-xm-12">
						<div class="card overflow-hidden sales-card bg-warning-gradient">
							<div class="pl-3 pt-3 pr-3 pb-2 pt-0">
								<div class="">
									<h6 class="mb-3 tx-20 text-white">{{ trans('main.Returns') }} {{ trans('main.Purchases') }} {{ trans('main.Today') }}</h6>
								</div>
								<div class="pb-0 mt-0">
									<div class="d-flex">
										<div class="">
											<h4 class="tx-20 font-weight-bold mb-1 text-white">${{ $today_return_purchases['today_return_purchases_sum'] ? $today_return_purchases['today_return_purchases_sum'] : 0 }}</h4>
										</div>
										<span class="float-right my-auto mr-auto">
											<i class="fas fa-arrow-circle-down text-white"></i>
											<span class="text-white op-7"> + {{ $today_return_purchases['today_return_purchases_count'] ? $today_return_purchases['today_return_purchases_count'] : 0 }}</span>
										</span>
									</div>
								</div>
							</div>
							<span id="compositeline4" class="pt-1">5,9,5,6,4,12,18,14,10,15,12,5,8,5,12,5,12,10,16,12</span>
						</div>
					</div>
				</div>
				<!-- row closed -->

				<!-- row opened -->
				<div class="row row-sm">
					<div class="col-md-12 col-lg-12 col-xl-7">
						<div class="card">
							<div class="card-header bg-transparent pd-b-0 pd-t-20 bd-b-0">
								<div class="d-flex justify-content-between">
									<h4 class="card-title mb-0">{{ trans('main.Sales') }}</h4>
									<i class="mdi mdi-dots-horizontal text-gray"></i>
								</div>
								<p class="tx-12 text-muted mb-0"></p>
							</div>
							<div class="card-body">
								<div class="total-revenue">
									<div>
									  <h4>{{ $total_paid_sales }}</h4>
									  <label><span class="bg-success"></span>{{ trans('main.Paid') }}</label>
									</div>
									<div>
									  <h4>{{ $total_partial_sales }}</h4>
									  <label><span class="bg-primary"></span>{{ trans('main.Partial') }}</label>
									</div>
									<div>
									  <h4>{{ $total_unpaid_sales }}</h4>
									  <label><span class="bg-danger"></span>{{ trans('main.Unpaid') }}</label>
									</div>
								  </div>
								<div id="bar" class="sales-bar mt-4"></div>
							</div>
						</div>
					</div>
					<div class="col-lg-12 col-xl-5">
						<div class="card card-dashboard-map-one">
							<label class="main-content-label">{{ trans('main.Stock Alert') }}</label>
							<span class="d-block mg-b-20 text-muted tx-12"></span>
							<div class="table-responsive country-table">
								<table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
									<thead>
										<tr>
											<th class="wd-lg-25p tx-center">{{ trans('main.Product') }}</th>
											<th class="wd-lg-25p tx-center">{{ trans('main.Warehouse') }}</th>
											<th class="wd-lg-25p tx-center">{{ trans('main.Quantity') }}</th>
										</tr>
									</thead>
									<tbody>
										@foreach($stock_alerts as $item)
											<tr>
												<td class="tx-center tx-medium tx-inverse">{{ $item['name'] }}</td>
												<td class="tx-center tx-medium tx-inverse">{{ $item['warehouse'] }}</td>
												<td class="tx-center tx-medium">
													<span class="badge badge-danger p-2">{{ $item['quantity'] }}</span>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<!-- row closed -->

				<!-- row opened -->
				<div class="row row-sm">
					<div class="col-xl-4 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header pb-1">
								<h3 class="card-title mb-2">{{ trans('main.Top Five Clients') }}</h3>
								<p class="tx-12 mb-0 text-muted"></p>
							</div>
							<div class="card-body p-0 customers mt-1">
								<div class="list-group list-lg-group list-group-flush">
									@foreach($top_five_clients as $item)
										<div class="list-group-item list-group-item-action" href="#">
											<div class="media mt-0">
												<img class="avatar-lg rounded-circle ml-3 my-auto" src="{{URL::asset('assets/img/faces/user.png')}}" alt="Image description">
												<div class="media-body">
													<div class="d-flex align-items-center">
														<div class="mt-0">
															<h5 class="mb-1 tx-15">{{ $item->name}}</h5>
														</div>
														<span class="mr-auto wd-45p fs-16 mt-2">
															<p class="mb-0 tx-13 text-muted">{{ trans('main.Sales') }} <span class="text-success ml-2">{{ $item->value }}</span></p>
														</span>
													</div>
												</div>
											</div>
										</div>
									@endforeach
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-md-12 col-lg-12">
						<div class="card">
							<div class="card-header pb-1">
								<h3 class="card-title mb-2">{{ trans('main.Top Five Providers') }}</h3>
								<p class="tx-12 mb-0 text-muted"></p>
							</div>
							<div class="card-body p-0 customers mt-1">
								<div class="list-group list-lg-group list-group-flush">
									@foreach($top_five_providers as $item)
										<div class="list-group-item list-group-item-action" href="#">
											<div class="media mt-0">
												<img class="avatar-lg rounded-circle ml-3 my-auto" src="{{URL::asset('assets/img/faces/user.png')}}" alt="Image description">
												<div class="media-body">
													<div class="d-flex align-items-center">
														<div class="mt-0">
															<h5 class="mb-1 tx-15">{{ $item->name}}</h5>
														</div>
														<span class="mr-auto wd-45p fs-16 mt-2">
															<p class="mb-0 tx-13 text-muted">{{ trans('main.Purchases') }} <span class="text-success ml-2">{{ $item->count }}</span></p>
														</span>
													</div>
												</div>
											</div>
										</div>
									@endforeach
								</div>
							</div>
						</div>
					</div>
					<div class="col-xl-4 col-md-12 col-lg-6">
						<div class="card">
							<div class="card-header pb-1">
								<h3 class="card-title mb-2">{{ trans('main.Top Five Products This Year') }}</h3>
								<p class="tx-12 mb-0 text-muted"></p>
							</div>
							<div class="product-timeline card-body pt-2 mt-1">
								<div class="table-responsive country-table">
									<table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
										<thead>
											<tr>
												<th class="wd-lg-25p tx-center">{{ trans('main.Product') }}</th>
												<th class="wd-lg-25p tx-center">{{ trans('main.Amount') }}</th>
											</tr>
										</thead>
										<tbody>
											@foreach($top_five_products_this_year as $item)
												<tr>
													<td class="tx-center tx-medium tx-inverse">{{ $item['name'] }}</td>
													<td class="tx-center tx-medium tx-inverse">{{ $item['value'] }}</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- row close -->

				<!-- row opened -->
				<div class="row row-sm row-deck">
					<div class="col-md-12 col-lg-4 col-xl-4">
						<div class="card card-dashboard-eight pb-2">
							<h6 class="card-title">{{ trans('main.Top Five Products This Month') }}</h6><span class="d-block mg-b-10 text-muted tx-12"></span>
							<div class="list-group">
								<div class="table-responsive country-table">
									<table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
										<thead>
											<tr>
												<th class="wd-lg-25p tx-center">{{ trans('main.Product') }}</th>
												<th class="wd-lg-25p tx-center">{{ trans('main.Sales') }}</th>
												<th class="wd-lg-25p tx-center">{{ trans('main.Quantity') }}</th>
												<th class="wd-lg-25p tx-center">{{ trans('main.Amount') }}</th>
											</tr>
										</thead>
										<tbody>
											@foreach($top_five_products_this_month as $item)
												<tr>
													<td class="tx-center tx-medium tx-inverse">{{ $item['name'] }}</td>
													<td class="tx-center tx-medium tx-inverse">{{ $item['count'] }}</td>
													<td class="tx-center tx-medium tx-inverse">{{ $item['quantity'] }}</td>
													<td class="tx-center tx-medium tx-inverse">{{ $item['total'] }}</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12 col-lg-8 col-xl-8">
						<div class="card card-table-two">
							<div class="d-flex justify-content-between">
								<h4 class="card-title mb-1">{{ trans('main.Last Five Sales') }}</h4>
								<i class="mdi mdi-dots-horizontal text-gray"></i>
							</div>
							<span class="tx-12 tx-muted mb-3 "></span>
							<div class="table-responsive country-table">
								<table class="table table-striped table-bordered mb-0 text-sm-nowrap text-lg-nowrap text-xl-nowrap">
									<thead>
										<tr>
											<th class="wd-lg-25p tx-right">{{ trans('main.Date') }}</th>
											<th class="wd-lg-25p tx-right">{{ trans('main.Client') }}</th>
											<th class="wd-lg-25p tx-right">{{ trans('main.Warehouse') }}</th>
											<th class="wd-lg-25p tx-right">{{ trans('main.Grand Total') }}</th>
											<th class="wd-lg-25p tx-right">{{ trans('main.Paid Amount') }}</th>
											<th class="wd-lg-25p tx-right">{{ trans('main.Due') }}</th>
											<th class="wd-lg-25p tx-right">{{ trans('main.Payment Status') }}</th>
										</tr>
									</thead>
									<tbody>
										@foreach($last_five_sales as $item)
											<tr>
												<td class="tx-right tx-medium tx-inverse">{{ $item->date }}</td>
												<td class="tx-right tx-medium tx-inverse">{{ $item->client->name }}</td>
												<td class="tx-right tx-medium tx-inverse">{{ $item->warehouse->name }}</td>
												<td class="tx-right tx-medium tx-danger">{{ $item->GrandTotal }}</td>
												<td class="tx-right tx-medium tx-inverse">{{ $item->paid_amount }}</td>
												<td class="tx-right tx-medium tx-inverse">{{ $item->GrandTotal - $item->paid_amount }}</td>
												<td class="tx-right tx-medium tx-danger">
													@if($item->payment_status == 'paid')
                                                        <p class="badge badge-success p-2">{{ trans('main.Paid') }}</p>
                                                    @elseif ($item->payment_status == 'unpaid')
                                                        <p class="badge badge-danger p-2">{{ trans('main.Unpaid') }}</p>
                                                    @else
                                                        <p class="badge badge-warning p-2">{{ trans('main.Partial') }}</p>
                                                    @endif 
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
				<!-- /row -->
			</div>
		</div>
		<!-- Container closed -->
@endsection
@section('js')
<!--Internal  Chart.bundle js -->
<script src="{{URL::asset('assets/plugins/chart.js/Chart.bundle.min.js')}}"></script>
<!-- Moment js -->
<script src="{{URL::asset('assets/plugins/raphael/raphael.min.js')}}"></script>
<!--Internal  Flot js-->
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.pie.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.resize.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jquery.flot/jquery.flot.categories.js')}}"></script>
<script src="{{URL::asset('assets/js/dashboard.sampledata.js')}}"></script>
<script src="{{URL::asset('assets/js/chart.flot.sampledata.js')}}"></script>
<!--Internal Apexchart js-->
<script src="{{URL::asset('assets/js/apexcharts.js')}}"></script>
<!-- Internal Map -->
<script src="{{URL::asset('assets/plugins/jqvmap/jquery.vmap.min.js')}}"></script>
<script src="{{URL::asset('assets/plugins/jqvmap/maps/jquery.vmap.usa.js')}}"></script>
<script src="{{URL::asset('assets/js/modal-popup.js')}}"></script>
<!--Internal  index js -->
<script src="{{URL::asset('assets/js/index.js')}}"></script>
<script src="{{URL::asset('assets/js/jquery.vmap.sampledata.js')}}"></script>	
@endsection