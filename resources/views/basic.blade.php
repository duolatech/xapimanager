<!DOCTYPE html>
<html lang="zh-CN">
@include('public/header')
<body>
	<!-- uiView:  -->
	<div class="app ng-scope app-header-fixed app-aside-fixed" id="app"
		ui-view="">
		<!-- navbar -->
		@include('public/navbar2')
		<!-- / navbar -->
		
		<!-- content -->
			<!-- page-content -->
			@yield('page-content')
			<!-- page-content -->
		<!-- /content -->
		

	</div>
	@include('public/footerjs')
</body>
</html>