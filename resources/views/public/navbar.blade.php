<div id="navbar" class="navbar navbar-default">
	<script type="text/javascript">
            try {
                ace.settings.check('navbar', 'fixed')
            } catch (e) {
            }
        </script>

	<div class="navbar-container" id="navbar-container">
		<!-- #section:basics/sidebar.mobile.toggle -->
		<button type="button" class="navbar-toggle menu-toggler pull-left"
			id="menu-toggler" data-target="#sidebar">
			<span class="sr-only">Toggle sidebar</span> <span class="icon-bar"></span>

			<span class="icon-bar"></span> <span class="icon-bar"></span>
		</button>

		<!-- /section:basics/sidebar.mobile.toggle -->
		<div class="navbar-header pull-left">
			<!-- #section:basics/navbar.layout.brand -->
			<a href="{{route('home')}}" class="navbar-brand"> <small>
					<i class="fa fa-home">{{$sys['Website']['sitename']}}</i> 
			</small>
			</a>

			<!-- /section:basics/navbar.layout.brand -->

			<!-- #section:basics/navbar.toggle -->

			<!-- /section:basics/navbar.toggle -->
		</div>

		<!-- #section:basics/navbar.dropdown -->
		<div class="navbar-buttons navbar-header pull-right" role="navigation">
			<ul class="nav ace-nav">
				<!-- #section:basics/navbar.user_menu -->
				<li class="purple"><a href="javascript:void(0)" class="clearcache"
					title="清除缓存" target="_self"> <i class="ace-icon fa glyphicon-trash"></i>
				</a></li>
				<li>
					<ul class="nav ace-nav">
							<li class="green">
								<a data-toggle="dropdown" href="javascript:void(0);" class="dropdown-toggle current_env">
									<span env="0"> 请选择环境</span> <i class="ace-icon fa fa-caret-down"></i>
								</a>
								<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close all_env">
									@foreach($sys['ApiEnv'] as $env)
										<li><a href="javascript:void(0);"><span env="{{$env['id']}}">{{$env['envname']}}</span></a></li>
									@endforeach
								</ul>
							</li>
						</ul>
						<script type="text/javascript" charset="utf-8">
    						var env,env_name;
    						env = $.cookie('env');
    						env_name = $.cookie('env_name');
    						//初始化判断
    						if(env && env_name){
    							$(".current_env span").replaceWith("<span env='"+env+"'>"+env_name+"</span>");
    							$(".all_env li").each(function(){
    								if($(this).find('span').attr('env') == env){
    									$(this).hide();
    								}
    							})
    						}else{
    							$(".current_env span").replaceWith($(".all_env li:first a").html());
    							$(".all_env li:first").hide();
    						}
    						//环境切换
    						$(".all_env li").click(function(){
    							var env = $(this).find('span').attr('env');
    							var env_name = $(this).text();
    							$(".current_env span").replaceWith($(this).find('a').html());
    							$(".all_env li").show();
    							$(this).hide();
    							$.cookie('env', env, {expires:30,path:'/'});
    							$.cookie('env_name', env_name, {expires:30,path:'/'});
    							window.location.href='/Api/list';
    						});
						</script>
				</li>
				<li class="light-blue"><a data-toggle="dropdown" href="#"
					class="dropdown-toggle"> <img class="nav-user-photo"
						src="{{Session::get('avatar')}}" alt="admin" />
						<span class="user-info"> <small>欢迎光临，</small> {{
							Session::get('username') }}
					</span> <i class="ace-icon fa fa-caret-down"></i>
				</a>

					<ul
						class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
						<li><a href="/Personal/profile"> <i
								class="ace-icon fa fa-cog"></i> 设置
						</a></li>

						<li><a href="/Personal/profile"> <i
								class="ace-icon fa fa-user"></i> 个人资料
						</a></li>

						<li class="divider"></li>

						<li><a href="/Login/logout"> <i
								class="ace-icon fa fa-power-off"></i> 退出
						</a></li>
					</ul></li>

				<!-- /section:basics/navbar.user_menu -->
			</ul>
		</div>

		<!-- /section:basics/navbar.dropdown -->
	</div>
	<!-- /.navbar-container -->
</div>