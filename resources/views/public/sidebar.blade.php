<div class="app-aside hidden-xs bg-black" id="self-adaption-menu">
	<div class="aside-wrap ng-scope">
		<div class="navi-wrap">

			<!-- nav -->
			<nav ui-nav="" class="navi ng-scope">
				<!-- list -->
				<ul class="nav ng-scope navbarmenu">
					<script type="text/javascript" charset="utf-8">
                		var indent = $.cookie('fa-indent');
                		if(indent==1){
                			$(".navbarmenu").addClass('navbarmenufold');
                		}
            		</script>
					<li class="hidden-folded padder m-t m-b-sm text-muted text-xs"><span
						class="ng-scope">导航栏</span>
					</li>
					@if(!empty($sys['ValidMenu'][1]))
						@foreach($sys['ValidMenu'][1]['child'] as $navbar)	
							@if($navbar['status']==1)
							<li><a href="@if(!empty($navbar['child'])) javascript:; @else {{$navbar['path'] or ''}} @endif" class="auto"> 
								<span class="pull-right text-muted">
									@if(!empty($navbar['child']))
										<i class="fa fa-fw fa-angle-right text"></i> 
										<i class="fa fa-fw fa-angle-down text-active"></i>
									@endif
								</span> 
								<i class="{{$navbar['icon'] or ''}}"></i> 
								<span class="font-bold ng-scope">{{$navbar['title'] or ''}}</span>
        					</a>
        					@if(!empty($navbar['child']))
        					<ul class="nav nav-sub dk" style="top: auto; bottom: auto;">
        						@foreach($navbar['child'] as $subNavbar)
        						@if($subNavbar['status']==1)
    							<li ui-sref-active="active"><a ui-sref="app.dashboard-v1"
    								href="{{$subNavbar['path'] or ''}}"> <span>{{$subNavbar['title'] or ''}}</span>
    							</a></li>
    							@endif
    							@endforeach
    						</ul>
        					@endif
        					</li>
        					@endif
						@endforeach
					@endif	
					
					<li class="line dk"></li>

					<li class="hidden-folded padder m-t m-b-sm text-muted text-xs"><span
						translate="aside.nav.components.COMPONENTS" class="ng-scope">项目选择</span>
					</li>
					<li ui-sref-active="active"><a ui-sref="app.chart"
						href="/Project/create"> <i class="glyphicon glyphicon-plus"></i> 
						<span class="ng-scope">添加项目</span>
					</a></li>
					@if(!empty($sys['Project']['info']))
					@foreach($sys['Project']['info'] as $pro)
					<li><a href="javascript:void(0);" class='activeproject' proid={{$pro['id'] or ''}}> 
							<i class="glyphicon glyphicon-tags"></i> 
							<span class="ng-scope">{{$pro['proname'] or ''}}<label>@if($pro['active'])√ @endif</label></span>
						</a>
						<input type="hidden" value="{{ csrf_token() }}" name="_token" />
					</li>
					@endforeach
					@endif
					<script type="text/javascript" charset="utf-8">
						$(".activeproject").click(function(){
							var proid = $(this).attr('proid');
							$.ajax({
				                cache: false,
				                type: "POST",
				                url:"{{route('project.toggle')}}",
				                data:{'proid':proid},
				                headers: {
				                    'X-CSRF-TOKEN': $("input[name='_token']").val()
				                },
				                dataType: 'json',
				                success: function(res) {
				                	layer.msg(res.message)
				                	if(res.status==200){
										$.cookie('env', res.data.envid, {expires:30,path:'/'});
				                		setTimeout(function(){
				                			window.location.reload();
										 }, 2000);
				                	}
				                },
				                error: function(request) {
				                    layer.msg("网络错误，请稍后重试");
				                },
				            });
						})
					</script>
					<li class="line dk hidden-folded"></li>

					<li class="hidden-folded padder m-t m-b-sm text-muted text-xs">
						<span class="ng-scope">用户中心</span>
					</li>
					@if(!empty($sys['ValidMenu'][3]))
						@foreach($sys['ValidMenu'][3]['child'] as $navbar)	
							@if($navbar['status']==1)
							<li><a href="@if(!empty($navbar['child'])) javascript:; @else {{$navbar['path'] or ''}} @endif" class="auto"> 
								<span class="pull-right text-muted">
									@if(!empty($navbar['child']))
										<i class="fa fa-fw fa-angle-right text"></i> 
										<i class="fa fa-fw fa-angle-down text-active"></i>
									@endif
								</span> 
								<i class="{{$navbar['icon'] or ''}}"></i> 
								<span class="font-bold ng-scope">{{$navbar['title'] or ''}}</span>
        					</a>
        					@if(!empty($navbar['child']))
        					<ul class="nav nav-sub dk" style="top: auto; bottom: auto;">
        						@foreach($navbar['child'] as $subNavbar)
        						@if($subNavbar['status']==1)
    							<li ui-sref-active="active"><a ui-sref="app.dashboard-v1"
    								href="{{$subNavbar['path'] or ''}}"> <span>{{$subNavbar['title'] or ''}}</span>
    							</a></li>
    							@endif
    							@endforeach
    						</ul>
        					@endif
        					</li>
        					@endif
						@endforeach
					@endif
				</ul>
				<!-- / list -->
			</nav>
			<!-- nav -->

			<!-- aside footer -->
			<div class="wrapper m-t">
				<div class="text-center-folded">
				</div>
			</div>
			<!-- / aside footer -->
		</div>
	</div>


</div>