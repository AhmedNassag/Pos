<!-- main-sidebar -->
<div class="app-sidebar__overlay" data-toggle="sidebar"></div>
<aside class="app-sidebar sidebar-scroll">
	<div class="main-sidebar-header active">
		<a class="desktop-logo logo-light active" href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/logo.png')}}" class="main-logo" alt="logo"></a>
		<a class="desktop-logo logo-dark active" href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/logo-white.png')}}" class="main-logo dark-theme" alt="logo"></a>
		<a class="logo-icon mobile-logo icon-light active" href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/favicon.png')}}" class="logo-icon" alt="logo"></a>
		<a class="logo-icon mobile-logo icon-dark active" href="{{ url('/' . $page='index') }}"><img src="{{URL::asset('assets/img/brand/favicon-white.png')}}" class="logo-icon dark-theme" alt="logo"></a>
	</div>
	<div class="main-sidemenu">
		<div class="app-sidebar__user clearfix">
			<div class="dropdown user-pro-body">
				<div class="">
					@if(Auth::user()->avatar)
						<img alt="user-img" class="avatar avatar-xl brround" src="{{ asset('attachments/user/'.Auth::user()->avatar) }}"><span class="avatar-status profile-status bg-green"></span>
					@else
						<img alt="user-img" class="avatar avatar-xl brround" src="{{ asset('attachments/user/user.png') }}"><span class="avatar-status profile-status bg-green"></span>
					@endif
				</div>
				<div class="user-info">
					<h4 class="font-weight-semibold mt-3 mb-0">{{ Auth::user()->name }}</h4>
					<span class="mb-0 text-muted">{{ Auth::user()->email }}</span>
				</div>
			</div>
		</div>

		<ul class="side-menu">
			


			<!-- Dashboard -->
			<li class="side-item side-item-category">{{ trans('main.Dashboard') }}</li>
			<li class="slide">
				<a class="side-menu__item" href="{{ route('home') }}">
					<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" >
						<path d="M0 0h24v24H0V0z" fill="none"/>
						<path d="M5 5h4v6H5zm10 8h4v6h-4zM5 17h4v2H5zM15 5h4v2h-4z" opacity=".3"/>
						<path d="M3 13h8V3H3v10zm2-8h4v6H5V5zm8 16h8V11h-8v10zm2-8h4v6h-4v-6zM13 3v6h8V3h-8zm6 4h-4V5h4v2zM3 21h8v-6H3v6zm2-4h4v2H5v-2z"/>
					</svg>
					<span class="side-menu__label">{{ trans('main.Index') }}</span>
				</a>
			</li>



			<!-- Users And Permissions -->
			@can('المستخدمين والصلاحيات')
				<li class="side-item side-item-category">{{ trans('main.Users And Permissions') }}</li>
				<li class="slide">
					<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
							<path d="M0 0h24v24H0V0z" fill="none" />
							<path d="M15 11V4H4v8.17l.59-.58.58-.59H6z" opacity=".3" />
							<path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-5 7c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10zM4.59 11.59l-.59.58V4h11v7H5.17l-.58.59z" />
						</svg>
						<span class="side-menu__label">{{ trans('main.Users And Permissions') }}</span>
						<i class="angle fe fe-chevron-down"></i>
					</a>
					<ul class="slide-menu">
						@can('عرض المستخدمين')
							<li>
								<a class="slide-item" href="{{ url('/' . ($page = 'users')) }}">{{ trans('main.Users') }}</a>
							</li>
						@endcan

						@can('عرض الصلاحيات')
							<li>
								<a class="slide-item" href="{{ url('/' . ($page = 'roles')) }}">{{ trans('main.Permissions') }}</a>
							</li>
						@endcan
					</ul>
				</li>
			@endcan



			<!-- People -->
			@can('الأشخاص')
				<li class="side-item side-item-category">{{ trans('main.People') }}</li>
				<li class="slide">
					<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
							<path d="M0 0h24v24H0V0z" fill="none" />
							<path d="M15 11V4H4v8.17l.59-.58.58-.59H6z" opacity=".3" />
							<path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-5 7c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10zM4.59 11.59l-.59.58V4h11v7H5.17l-.58.59z" />
						</svg>
						<span class="side-menu__label">{{ trans('main.People') }}</span>
						<i class="angle fe fe-chevron-down"></i>
					</a>
					<ul class="slide-menu">
						@can('عرض العملاء')
							<li>
								<a class="slide-item" href="{{ route('clients.index') }}">{{ trans('main.Clients') }}</a>
							</li>
						@endcan

						@can('عرض الموردين')
							<li>
								<a class="slide-item" href="{{ route('providers.index') }}">{{ trans('main.Providers') }}</a>
							</li>
						@endcan
					</ul>
				</li>
			@endcan



			<!-- General Data -->
			@can('الأشخاص')
				<li class="side-item side-item-category">{{ trans('main.General Data') }}</li>
				<li class="slide">
					<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
							<path d="M0 0h24v24H0V0z" fill="none" />
							<path d="M15 11V4H4v8.17l.59-.58.58-.59H6z" opacity=".3" />
							<path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-5 7c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10zM4.59 11.59l-.59.58V4h11v7H5.17l-.58.59z" />
						</svg>
						<span class="side-menu__label">{{ trans('main.General Data') }}</span>
						<i class="angle fe fe-chevron-down"></i>
					</a>
					<ul class="slide-menu">
						@can('عرض الفئات')
							<li>
								<a class="slide-item" href="{{ route('category.index') }}">{{ trans('main.Categories') }}</a>
							</li>
						@endcan
						@can('عرض الماركات')
							<li>
								<a class="slide-item" href="{{ route('brands.index') }}">{{ trans('main.Brands') }}</a>
							</li>
						@endcan
						@can('عرض العملات')
							<li>
								<a class="slide-item" href="{{ route('currencies.index') }}">{{ trans('main.Currencies') }}</a>
							</li>
						@endcan
						@can('عرض الوحدات')
							<li>
								<a class="slide-item" href="{{ route('units.index') }}">{{ trans('main.Units') }}</a>
							</li>
						@endcan
						@can('عرض المنتجات')
							<li>
								<a class="slide-item" href="{{ route('products.index') }}">{{ trans('main.Products') }}</a>
							</li>
						@endcan
					</ul>
				</li>
			@endcan



			<!-- Purchases -->
			@can('المشتريات')
				<li class="side-item side-item-category">{{ trans('main.Purchases') }}</li>
				<li class="slide">
					<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
							<path d="M0 0h24v24H0V0z" fill="none" />
							<path d="M15 11V4H4v8.17l.59-.58.58-.59H6z" opacity=".3" />
							<path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-5 7c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10zM4.59 11.59l-.59.58V4h11v7H5.17l-.58.59z" />
						</svg>
						<span class="side-menu__label">{{ trans('main.Purchases') }}</span>
						<i class="angle fe fe-chevron-down"></i>
					</a>
					<ul class="slide-menu">
					@can('عرض المشتريات')
						<li>
							<a class="slide-item" href="{{ route('purchases.index') }}">{{ trans('main.List') }} {{ trans('main.Purchases') }}</a>
						</li>
					@endcan
					@can('عرض مرتجع المشتريات')
						<li>
							<a class="slide-item" href="{{ route('purchasesReturns.index') }}">{{ trans('main.Returns') }} {{ trans('main.Purchases') }}</a>
						</li>
					@endcan
					</ul>
				</li>
			@endcan



			<!-- Sales -->
			@can('المبيعات')
				<li class="side-item side-item-category">{{ trans('main.Sales') }}</li>
				<li class="slide">
					<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
							<path d="M0 0h24v24H0V0z" fill="none" />
							<path d="M15 11V4H4v8.17l.59-.58.58-.59H6z" opacity=".3" />
							<path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-5 7c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10zM4.59 11.59l-.59.58V4h11v7H5.17l-.58.59z" />
						</svg>
						<span class="side-menu__label">{{ trans('main.Sales') }}</span>
						<i class="angle fe fe-chevron-down"></i>
					</a>
					<ul class="slide-menu">
						@can('عرض المبيعات')
							<li>
								<a class="slide-item" href="{{ route('sales.index') }}">{{ trans('main.List') }} {{ trans('main.Sales') }}</a>
							</li>
						@endcan
						@can('عرض مرتجع المبيعات')
							<li>
								<a class="slide-item" href="{{ route('salesReturns.index') }}">{{ trans('main.Returns') }} {{ trans('main.Sales') }}</a>
							</li>
						@endcan
					</ul>
				</li>
			@endcan



			<!-- Warehouses -->
			@can('المخازن')
				<li class="side-item side-item-category">{{ trans('main.Warehouses') }}</li>
				<li class="slide">
					<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
							<path d="M0 0h24v24H0V0z" fill="none" />
							<path d="M15 11V4H4v8.17l.59-.58.58-.59H6z" opacity=".3" />
							<path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-5 7c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10zM4.59 11.59l-.59.58V4h11v7H5.17l-.58.59z" />
						</svg>
						<span class="side-menu__label">{{ trans('main.Warehouses') }}</span>
						<i class="angle fe fe-chevron-down"></i>
					</a>
					<ul class="slide-menu">
						@can('عرض المخازن')
							<li>
								<a class="slide-item" href="{{ route('warehouses.index') }}">{{ trans('main.List') }} {{ trans('main.Warehouses') }}</a>
							</li>
						@endcan
						@can('عرض المخزون')
							<li>
								<a class="slide-item" href="{{ route('adjustments.stock') }}">{{ trans('main.List') }} {{ trans('main.Stocks') }}</a>
							</li>
						@endcan
						@can('عرض تعديل المخزون')
							<li>
								<a class="slide-item" href="{{ route('adjustments.index') }}">{{ trans('main.Adjustments') }}</a>
							</li>
						@endcan
					</ul>
				</li>
			@endcan



			<!-- Expenses -->
			@can('المصروفات')
				<li class="side-item side-item-category">{{ trans('main.Expenses') }}</li>
				<li class="slide">
					<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . ($page = '#')) }}">
						<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24">
							<path d="M0 0h24v24H0V0z" fill="none" />
							<path d="M15 11V4H4v8.17l.59-.58.58-.59H6z" opacity=".3" />
							<path d="M21 6h-2v9H6v2c0 .55.45 1 1 1h11l4 4V7c0-.55-.45-1-1-1zm-5 7c.55 0 1-.45 1-1V3c0-.55-.45-1-1-1H3c-.55 0-1 .45-1 1v14l4-4h10zM4.59 11.59l-.59.58V4h11v7H5.17l-.58.59z" />
						</svg>
						<span class="side-menu__label">{{ trans('main.Expenses') }}</span>
						<i class="angle fe fe-chevron-down"></i>
					</a>
					<ul class="slide-menu">
						@can('عرض فئات المصروفات')
							<li>
								<a class="slide-item" href="{{ route('expensescategory.index') }}">{{ trans('main.Expenses Categories') }}</a>
							</li>
						@endcan
						@can('عرض المصروفات')
							<li>
								<a class="slide-item" href="{{ route('expenses.index') }}">{{ trans('main.List') }} {{ trans('main.Expenses') }}</a>
							</li>
						@endcan
					</ul>
				</li>
			@endcan



			<!-- Reports -->
			@can('التقارير')
			<li class="side-item side-item-category">{{ trans('main.Reports') }}</li>
			<li class="slide ">
				<a class="side-menu__item" data-toggle="slide" href="{{ url('/' . $page='#') }}">
					<svg xmlns="http://www.w3.org/2000/svg" class="side-menu__icon" viewBox="0 0 24 24" >
						<path d="M0 0h24v24H0V0z" fill="none"/>
						<path d="M5 9h14V5H5v4zm2-3.5c.83 0 1.5.67 1.5 1.5S7.83 8.5 7 8.5 5.5 7.83 5.5 7 6.17 5.5 7 5.5zM5 19h14v-4H5v4zm2-3.5c.83 0 1.5.67 1.5 1.5s-.67 1.5-1.5 1.5-1.5-.67-1.5-1.5.67-1.5 1.5-1.5z" opacity=".3"/>
						<path d="M20 13H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1v-6c0-.55-.45-1-1-1zm-1 6H5v-4h14v4zm-12-.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5-1.5.67-1.5 1.5.67 1.5 1.5 1.5zM20 3H4c-.55 0-1 .45-1 1v6c0 .55.45 1 1 1h16c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1zm-1 6H5V5h14v4zM7 8.5c.83 0 1.5-.67 1.5-1.5S7.83 5.5 7 5.5 5.5 6.17 5.5 7 6.17 8.5 7 8.5z"/>
					</svg>
					<span class="side-menu__label">{{ trans('main.Reports') }}</span>
					<i class="angle fe fe-chevron-down"></i>
				</a>
				<ul class="slide-menu">
					<li class="sub-slide">
						@can('عرض تقرير المدفوعات')
							<a class="sub-side-menu__item" data-toggle="sub-slide" href="{{ url('/' . $page='#') }}">
								<span class="sub-side-menu__label">{{ trans('main.Report') }} {{ trans('main.Payments') }}</span>
								<i class="sub-angle fe fe-chevron-down"></i>
							</a>
						@endcan
						<ul class="sub-slide-menu">
							@can('عرض تقرير مدفوعات المشتريات')
								<li>
									<a class="sub-slide-item" href="{{ route('reports.purchasesPayments') }}">{{ trans('main.Purchases') }}</a>
								</li>
							@endcan
							@can('عرض تقرير مدفوعات المبيعات')
								<li>
									<a class="sub-slide-item" href="{{ route('reports.salesPayments') }}">{{ trans('main.Sales') }}</a>
								</li>
							@endcan
							@can('عرض تقرير مدفوعات مرتجع المشتريات')
								<li>
									<a class="sub-slide-item" href="{{ route('reports.purchasesReturnsPayments') }}">{{ trans('main.Returns') }} {{ trans('main.Purchases') }}</a>
								</li>
							@endcan
							@can('عرض تقرير مدفوعات مرتجع المبيعات')
								<li>
									<a class="sub-slide-item" href="{{ route('reports.salesReturnsPayments') }}">{{ trans('main.Returns') }} {{ trans('main.Sales') }}</a>
								</li>
							@endcan
						</ul>
					</li>
					
					@can('عرض تقرير المخازن')
						<li>
							<a class="slide-item" href="{{ route('reports.warehouses') }}">{{ trans('main.Report') }} {{ trans('main.Warehouses') }}</a>
						</li>
					@endcan
					@can('عرض تقرير المبيعات')
						<li>
							<a class="slide-item" href="{{ route('reports.sales') }}">{{ trans('main.Report') }} {{ trans('main.Sales') }}</a>
						</li>
					@endcan
					@can('عرض تقرير المشتريات')
						<li>
							<a class="slide-item" href="{{ route('reports.purchases') }}">{{ trans('main.Report') }} {{ trans('main.Purchases') }}</a>
						</li>
					@endcan
					@can('عرض تقرير العملاء')
						<li>
							<a class="slide-item" href="{{ route('reports.clients') }}">{{ trans('main.Report') }} {{ trans('main.Clients') }}</a>
						</li>
					@endcan
					@can('عرض تقرير الموردين')
						<li>
							<a class="slide-item" href="{{ route('reports.providers') }}">{{ trans('main.Report') }} {{ trans('main.Providers') }}</a>
						</li>
					@endcan
					@can('عرض تقرير تنبيه المخزون')
						<li>
							<a class="slide-item" href="{{ route('reports.stockAlert') }}">{{ trans('main.Report') }} {{ trans('main.Stock Alert') }}</a>
						</li>
					@endcan
					@can('عرض تقرير الإحصائيات')
						<li>
							<a class="slide-item" href="{{ route('reports.statistics') }}">{{ trans('main.Report') }} {{ trans('main.Statistics') }}</a>
						</li>
					@endcan
				</ul>
			</li>
			@endcan



		</ul>
	</div>
</aside>
<!-- main-sidebar -->
