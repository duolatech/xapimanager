<!DOCTYPE html>
<html lang="zh-CN">
@include('public/header')
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/chosen.css')}}">
<body>
	<!-- uiView:  -->
	<div class="app ng-scope app-header-fixed app-aside-fixed" id="app"
		ui-view="">

		<!-- content -->
		<div class="ng-scope">
			<div ui-butterbar="" class="butterbar hide">
				<span class="bar"></span>
			</div>
			<a href="" class="off-screen-toggle hide"
				ui-toggle-class="off-screen" data-target=".app-aside"></a>
			<!-- page-content -->
			<div class="wrapper-md ng-scope">
				<div class="row">
					<div class="col-sm-3">
						<div class="panel b-a">
							<h4 class="font-thin padder">使用说明</h4>
							<ul class="list-group">
								<li class="list-group-item">
									<p>
										1.xApi Manager 为开源项目，您可以根据自己的需求自由使用和二次开发，但不得用于商业用途
									</p>
								</li>
								<li class="list-group-item">
									<p>
										2.如果您在安装或是使用过程中遇到什么问题，欢迎跟我们联系
										<a target="_blank" href="//shang.qq.com/wpa/qunwpa?idkey=d83394f78622527ea525d1d1bc5dca2e6aa8292ca2f05a04c46d172ac2180f29"><img border="0" src="//pub.idqqimg.com/wpa/images/group.png" alt="xApi Manager" title="xApi Manager"></a>
									</p>
								</li>
								<li class="list-group-item">
									<p>
										3.我们致力于提供一个简单实用、体验上乘的接口管理平台，如果您感兴趣欢迎与我们一同开发
									</p>
								</li>
								<li class="list-group-item">
									<p>
										网站：<a href="http://xapi.smaty.net/" target="_blank" style="text-decoration:underline">http://xapi.smaty.net/</a>
									</p>
									<p>
										操作手册：<br/><a href="http://www.smaty.net/t/xapi-manager" target="_blank" style="text-decoration:underline">http://www.smaty.net/t/xapi-manager</a>
									</p>
									
								</li>
							</ul>
						</div>
					</div>
					<div class="col-sm-7">
						<div class="m-b-lg m-r-lg">
							<form name="form" id="myForm" method="post"
								class="form-validation ng-pristine ng-invalid ng-invalid-required ng-valid-email">
								<div class="list-group-item">
									<div class="navbar-brand block m-t ng-binding">xApi Manager
										安装向导</div>
								</div>
								@if(!empty($info['data']))
    								<div class="list-group-item">
    									 @if(!empty($info['data']['dirauth']))
    									 <article class="media">
                                            <div class="pull-left">
                                              <span class="">
                                                <i class="fa fa-circle text-danger"></i>
                                              </span>
                                            </div>
                                            <div class="media-body">                        
                                              <a href="" class="h4">目录权限检查</a>
                                              <small class="block m-t-xs">项目根目录无写权限，建议设置为755</small>
                                            </div>
                                          </article>
                                          @endif
                                          @if(!empty($info['data']['ext']))
                                          <article class="media">
                                            <div class="pull-left">
                                              <span class="">
                                                <i class="fa fa-circle text-danger"></i>
                                              </span>
                                            </div>
                                            <div class="media-body">                        
                                              <a href="" class="h4">扩展检查</a>
                                              @foreach($info['data']['ext'] as $value)
                                              	<small class="block m-t-xs">未检查到{{$value or ''}}扩展</small>
                                              @endforeach
                                            </div>
                                          </article>
                                          @endif
    								</div>
    							@endif
    								<div class="list-group-item">
    									<input type="text" name="address" class="form-control"
    										placeholder="数据库地址"> <span
    										class="help-block m-b-none text-danger"></span>
    								</div>
    								<div class="list-group-item">
    									<input type="text" name="port" class="form-control"
    										placeholder="端口号，默认为3306"> <span
    										class="help-block m-b-none text-danger"></span>
    								</div>
    								<div class="list-group-item">
    									<input type="text" name="database" class="form-control"
    										placeholder="数据库名，默认为xapimanager"> <span
    										class="help-block m-b-none text-danger"></span>
    								</div>
    								<div class="list-group-item">
    									<input type="text" name="username" class="form-control"
    										placeholder="数据库用户名"> <span
    										class="help-block m-b-none text-danger"></span>
    								</div>
    								<div class="list-group-item">
    									<input type="text" name="password" class="form-control"
    										placeholder="数据库密码"> <span
    										class="help-block m-b-none text-danger"></span>
    								</div>
    								<div class="list-group-item">
    									<div class="m-t-xs">
    										<input type="hidden" value="{{ csrf_token() }}" name="_token" />
    										@if(!empty($info['data']))
    											<a href="/Install" class="btn m-b-xs btn-default">请处理后，刷新当前页面</a>
    										@else
    											<button type="submit" class="btn m-b-xs w-xs btn-info">安装</button>
    										@endif
    										<a href="javascript:;" class="btn m-b-xs btn-default onlineUpdate" version="{{$info['version']}}">在线升级</a>
    									</div>
    								</div>
							</form>
						</div>
						<div class="text-center ng-scope">
							<p class="ng-scope">
								<small>Copyright © 2017 <a href="http://xapi.smaty.net?type"
									class="text-success" target="_blank">xApi Manager</a> All
									Rights Reserved.
								</small>
							</p>
						</div>
				</div>
				</div>
				


				<!-- page-content -->
			</div>
			<!-- /content -->
			<script type="text/javascript"
				src="{{URL::asset('js/validation/jquery.validate.js')}}"></script>
			<script type="text/javascript"
				src="{{URL::asset('js/validation/messages_zh.js')}}"></script>
			<script type="text/javascript" charset="utf-8">

    		$(function(){
    			
    			var validator = $("#myForm").validate({
    				submitHandler: function(form) {
    					var address = $('input[name="address"]').val();
    					var port = $('input[name="port"]').val();
    					var database = $('input[name="database"]').val();
    					var username = $('input[name="username"]').val();
    					var password = $('input[name="password"]').val();
    					var _token = $("input[name='_token']").val();
    					layer.load(0, {shade: false});
						$.ajax({
    		                cache: false,
    		                type: "POST",
    		                url:"{{route('install.info')}}",
    		                data:{
    		                	'address':address,
    		                	'port':port,
    		                	'database':database,
    		                	'username':username,
    		                	'password':password
    		                },
    		                headers: {
    		                    'X-CSRF-TOKEN': _token
    		                },
    		                dataType: 'json',
    		                success: function(res) {
    		                	layer.closeAll();
    		                	if(res.status==200){
    		                		layer.msg(res.message)
    		                		setTimeout(function(){
    									 window.location.href="{{route('login.index')}}";
    								 }, 2000);
    		                	}else{
    		                		layer.alert(res.message)
    		                	}
    		                },
    		                error: function(request) {
    		                	layer.closeAll();
    		                    layer.msg("网络错误，请稍后重试");
    		                },
    		            });
    				},
    				rules:{
    					address:{
    						required:true,
    					},
    					database:{
    		    			required:true,
    		    		},
    					username:{
    		    			required:true,
    		    		},
    					password:{
    		    			required:true,
    		    		},
    				},
    				messages:{
    					address:{
    						required:'数据库地址不能为空',
    					},
    					database:{
    		    			required:'数据库名不能为空',
    		    		},
    					username:{
    		    			required:'用户名不能为空',
    		    		},
    					password:{
    		    			required:'密码不能为空',
    		    		}
    				},
    				errorElement: 'custom',
    				errorClass:'error',
    				errorPlacement: function(error, custom) {
    					error.appendTo( custom.next('span') ); 
    				},  
    			})
    		})
    		//升级
    		$(".onlineUpdate").click(function(){
    			layer.confirm('升级前请备份当前数据库！！！', {
  				  btn: ['确认','取消']
  				}, function(){
  					layer.load(0, {shade: false});
  					$.ajax({
		                cache: false,
		                type: "POST",
		                url:"{{route('install.update')}}",
		                data:{},
		                headers: {
		                    'X-CSRF-TOKEN': $("input[name='_token']").val()
		                },
		                dataType: 'json',
		                success: function(res) {
		                	layer.closeAll();
		                	if(res.status==200){
		                		layer.msg(res.message)
		                		setTimeout(function(){
									 window.location.href="{{route('login.index')}}";
								 }, 2000);
		                	}else{
		                		layer.alert(res.message)
		                	}
		                },
		                error: function(request) {
		                	layer.closeAll();
		                    layer.msg("网络错误，请稍后重试");
		                },
		            });
  					
  				}, function(){
  				});
  				//清除所有cookie
    			clearAllCookie();
        	})	
		</script>
		</div>
		<div id="flotTip" style="display: none; position: absolute;"></div>
		@include('public/footerjs')

</body>
</html>