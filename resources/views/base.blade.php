<!DOCTYPE html>
<html lang="zh-CN">
@include('public/header')
<body class="no-skin">
	<!-- #section:basics/navbar.layout -->
	@include('public/navbar')
	<!-- /section:basics/navbar.layout -->
	<div class="main-container" id="main-container">
		<script type="text/javascript">
            try {
                ace.settings.check('main-container', 'fixed')
            } catch (e) {
            }
        </script>
		@include('public/sidebar')
		<div class="main-content">
			<div class="main-content-inner">
				<!-- #section:basics/content.breadcrumbs -->
				<div class="breadcrumbs" id="breadcrumbs">
					<script type="text/javascript">
                        try {
                            ace.settings.check('breadcrumbs', 'fixed')
                        } catch (e) {
                        }
                    </script>

					<ul class="breadcrumb">
						<li><i class="ace-icon fa fa-home home-icon"></i> <a
							href="/">首页</a></li>
						<li class="active">{{$sys['Router'][Request::path()]['title'] or '控制台'}}</li>
					</ul>

					<div class="navbar-buttons navbar-header pull-right" role="navigation">
						
					</div>
					
					<!-- /.breadcrumb -->
				</div>

				<!-- /section:basics/content.breadcrumbs -->
				<!-- page-content -->
				@if($sys['AuthRule']['status']!=1)
				<script type="text/javascript">
					layer.alert('您所在的用户组权限，已经被禁用，请联系管理员', {
						skin: 'layui-layer-lan',
					    closeBtn: 0,
					    btn: []
					});
				</script>
				@endif @yield('page-content')

				<!-- /page-content -->
			</div>
		</div>
		<!-- /.main-content -->

		@include('public/footer') <a href="#" id="btn-scroll-up"
			class="btn-scroll-up btn btn-sm btn-inverse"> <i
			class="ace-icon fa fa-angle-double-up icon-only bigger-110"></i>
		</a>

	</div>
	<!-- /.main-container -->

	@include('public/footerjs')
</body>
</html>