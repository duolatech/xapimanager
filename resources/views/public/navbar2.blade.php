<div class="app-header navbar ng-scope">
	<!-- navbar header -->
	<div class="navbar-header bg-black">
		<!-- brand -->
		<a href="/" class="navbar-brand text-lt"> <span
			class="hidden-folded m-l-xs ng-binding">xApi Manager</span>
		</a>
		<!-- / brand -->
	</div>
	<!-- / navbar header -->

	<!-- navbar collapse -->
	<div class="collapse pos-rlt navbar-collapse box-shadow bg-white-only">
		<ul class="nav navbar-nav hidden-sm">
			<li class="dropdown pos-stc" dropdown=""><a href="/Debug" target="_blank"
				class="dropdown-toggle" dropdown-toggle="" aria-haspopup="true"
				aria-expanded="false"> <span>在线调试</span>
			</a></li>
		</ul>

		<!-- / link and dropdown -->

		<!-- search form -->

		<!-- / search form -->

		<!-- nabar right -->
		<ul class="nav navbar-nav navbar-right">
			<li class="dropdown" dropdown="" id="personal">
			@if(Session::get('uid'))
			<a href="javascrpit:void(0);"
				class="dropdown-toggle clear personalinfo" dropdown-toggle=""
				aria-haspopup="true" aria-expanded="false"> <span
					class="thumb-sm avatar pull-right m-t-n-sm m-b-n-sm m-l-sm"> <img
						src="{{Session::get('avatar')}}">
				</span> <span class="hidden-sm hidden-md">{{Session::get('username')}}</span>
			</a> 
			@endif
			</li>
		</ul>
		<!-- / navbar right -->

	</div>
	<!-- / navbar collapse -->
</div>