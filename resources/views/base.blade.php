<!DOCTYPE html>
<html lang="zh-CN">
@include('public/header')
<body>
	<!-- uiView:  -->
	<div class="app ng-scope app-header-fixed app-aside-fixed" id="app">
		<script type="text/javascript" charset="utf-8">
    		var indent = $.cookie('fa-indent');
    		if(indent==1){
    			$("#app").addClass('app-aside-folded');
    		}
		</script>
		<!-- navbar -->
		@include('public/navbar')
		<!-- / navbar -->

		<!-- menu -->
		@include('public/sidebar')
		<!-- / menu -->
		
		<!-- content -->
		<div class="app-content ng-scope">
			<div ui-butterbar="" class="butterbar hide">
				<span class="bar"></span>
			</div>
			<a href="" class="off-screen-toggle hide" ></a>
			<!-- page-content -->
			@yield('page-content')
			<!-- page-content -->
		</div>
		<!-- /content -->
		
		<!-- footer -->
		@include('public/footer')
		<!-- / footer -->

	</div>
	<div id="flotTip" style="display: none; position: absolute;"></div>
	@include('public/footerjs')
</body>
</html>