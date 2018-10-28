<div class="app-header navbar ng-scope">
	<!-- navbar header -->
	<div class="navbar-header bg-black">
		<button class="pull-right visible-xs dk">
			<i class="glyphicon glyphicon-cog"></i>
		</button>
		<button class="pull-right visible-xs self-adaption-button">
			<i class="glyphicon glyphicon-align-justify"></i>
		</button>
		<!-- brand -->
		<a href="/" class="navbar-brand text-lt"> <span
			class="hidden-folded m-l-xs ng-binding">xApi Manager</span>
		</a>
		<!-- / brand -->
	</div>
	<!-- / navbar header -->

	<!-- navbar collapse -->
	<div class="collapse pos-rlt navbar-collapse box-shadow bg-white-only">
		<!-- buttons -->
		<div class="nav navbar-nav hidden-xs">
			<a href="javascript:void(0);" class="btn no-shadow navbar-btn"> <i
				class="fa fa-dedent fa-fw toggle-nav"></i>
			</a>
			<script type="text/javascript" charset="utf-8">
    			var indent = $.cookie('fa-indent');
        		if(indent==1){
        			$(".toggle-nav").removeClass('fa-dedent').addClass('fa-indent');
        		}
    		</script>
		</div>
		<!-- / buttons -->
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
			<li class="dropdown hidden-sm" is-open="lang.isopen" dropdown=""
				id="select_env"><a href="javascript:void(0);"
				class="dropdown-toggle ng-binding current_env" dropdown-toggle=""
				aria-haspopup="true" aria-expanded="false"><span env="0"> 请选择环境</span><b class="caret"></b>
			</a>
				<ul class="dropdown-menu animated fadeInRight w all_env">
					@foreach($sys['ApiEnv'] as $env)
					<li class="ng-scope">
						<a href="javascript:void(0);" class="ng-binding"><span env="{{$env['id']}}" domain="{{$env['domain'] or ''}}">{{$env['envname']}}</span></a>
					</li>
					@endforeach
				</ul>
			</li>
			<input type="hidden" value="{{ csrf_token() }}" name="_token" />
			<script type="text/javascript" charset="utf-8">
				$(function() {
					$("#select_env").click(function() {
						var cenv = $(".current_env");
						if (cenv.attr('aria-expanded') == 'true') {
							$(this).removeClass('open');
							cenv.attr('aria-expanded', false);
						} else {
							$(this).addClass('open');
							cenv.attr('aria-expanded', true);
						}
					})
					var env,env_name;
					env        = "{{$sys['Project']['env']['id']}}";
					env_name   = "{{$sys['Project']['env']['envname']}}";
					env_domain = "{{$sys['Project']['env']['domain']}}";
					//初始化判断
					if(parseInt(env)>0){
						$(".current_env span").replaceWith("<span env='"+env+"' domain='"+env_domain+"'>"+env_name+"</span>");
						$(".all_env li").each(function(){
							if($(this).find('a').attr('env') == env){
								$(this).hide();
							}
						})
					}else{
						var startEnv = $(".all_env li:first a span");
						$(".current_env span").replaceWith($(".all_env li:first a").html());
						$(".all_env li:first").hide();
						envToggle(startEnv.attr('env'));
					}
					//环境切换
					$(".all_env li").click(function(){
						var env = $(this).find('span').attr('env');
						var env_name = $(this).text();
						var env_domain = $(this).find('span').attr('domain');
						$(".current_env span").replaceWith($(this).find('a').html());
						$(".all_env li").show();
						$(this).hide();
						envToggle(env);
					});
					//环境切换
					function envToggle(envid){
						$.ajax({
				                cache: false,
				                type: "POST",
				                url:"{{route('project.envToggle')}}",
				                data:{'envid':envid},
				                headers: {
				                    'X-CSRF-TOKEN': $("input[name='_token']").val()
				                },
				                dataType: 'json',
				                success: function(res) {
				                	layer.msg(res.message)
									setTimeout(function(){
				                		window.location.reload();
									}, 100);
				                },
				                error: function(request) {
				                    layer.msg("网络错误，请稍后重试");
				                },
				            });
					}
				})
			</script>
			<li class="hidden-xs"><a id="fullscreen"><i
					class="fa fa-expand fa-fw text"></i><i
					class="fa fa-compress fa-fw text-active"></i></a></li>
			<li class="dropdown"><a href="/Message/index?type=unread" class="dropdown-toggle"> 
				<i class="icon-bell fa-fw"></i>
					@if(!empty($sys['UnreadMessage']) && $sys['UnreadMessage']>0)
					<span class="badge badge-sm up bg-danger pull-right-xs">{{$sys['UnreadMessage'] or ''}}</span>
					@endif
			</a> 
			</li>
			<li class="dropdown" dropdown="" id="personal"><a
				href="javascript:void(0);"
				class="dropdown-toggle clear personalinfo" dropdown-toggle=""
				aria-haspopup="true" aria-expanded="false"> <span
					class="thumb-sm avatar pull-right m-t-n-sm m-b-n-sm m-l-sm"> <img
						src="{{Session::get('avatar')}}" alt="..."> <i class="on md b-white bottom"></i>
				</span> <span class="hidden-sm hidden-md">{{Session::get('username')}}</span> <b
					class="caret"></b>
			</a> <!-- dropdown -->
				<ul class="dropdown-menu animated fadeInRight w">
					<li><a href="/User/detail?uid={{Session::get('uid')}}">个人信息</a></li>
					<li class="divider"></li>
					<li><a href="javascript:;" class="clearCache">清除缓存</a></li>
					<li class="divider"></li>
					<li><a href="/Login/logout">退出</a></li>
				</ul> <script type="text/javascript" charset="utf-8">
							$(function() {
								$("#personal").click(function() {
									var cenv = $(".personalinfo");
									if (cenv.attr('aria-expanded') == 'true') {
										$(this).removeClass('open');
										cenv.attr('aria-expanded', false);
									} else {
										$(this).addClass('open');
										cenv.attr('aria-expanded', true);
									}
								})

								$(document)
										.mouseup(
												function(e) {

													if ($(e.target).parent(
															"#select_env").length == 0) {
														$("#select_env")
																.removeClass(
																		'open');
														$(".current_env")
																.attr(
																		'aria-expanded',
																		false);
													}
													if ($(e.target).parent(
															"#personal").length == 0) {
														$("#personal")
																.removeClass(
																		'open');
														$(".personalinfo")
																.attr(
																		'aria-expanded',
																		false);
													}

												})
							})
							//清除缓存
							$(".clearCache").click(function(){
								$.getJSON("{{route('cache.index')}}", function(data){
									if(data.status==200){
										layer.msg(data.message);
										window.location.reload();
									}
								})
							})
						</script> <!-- / dropdown --></li>
		</ul>
		<!-- / navbar right -->

	</div>
	<!-- / navbar collapse -->
</div>
