<!DOCTYPE html>
<html lang="zh-CN">
@include('public/header')
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/chosen.css')}}">
<link rel="stylesheet" href="{{URL::asset('js/rangeSlider/css/ion.rangeSlider.css')}}">
<link rel="stylesheet" href="{{URL::asset('js/rangeSlider/css/ion.rangeSlider.skinHTML5.css')}}">
<script type="text/javascript" src="{{URL::asset('js/rangeSlider/js/ion.rangeSlider.min.js')}}"></script>
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

			<div ui-view="" class="fade-in-right-big smooth ng-scope">
				<div class="container w-xxl w-auto-xs ng-scope">
					<div class="m-b-lg">
						<form name="form" id="myForm"
							class="form-validation ng-pristine ng-invalid ng-invalid-required ng-valid-email">
							<div class="text-danger wrapper text-center ng-binding ng-hide"></div>
							<div class="list-group-item">
								<div class="navbar-brand block m-t ng-binding">xApi Manager</div>
							</div>
							<div class="list-group-item">
								<input type="text" name="username" class="form-control" placeholder="用户名"> 
								<span class="help-block m-b-none text-danger"></span>
							</div>
							<div class="list-group-item">
								<input type="text" name="phone" class="form-control" placeholder="手机"> 
								<span class="help-block m-b-none text-danger"></span>
							</div>
							<div class="list-group-item">
								<input type="text" name="email" class="form-control" placeholder="邮箱"> 
								<span class="help-block m-b-none text-danger"></span>
							</div>
							<div class="list-group-item">
								<input type="password" name="password" class="form-control" placeholder="密码">
								<span class="help-block m-b-none text-danger"></span>
							</div>
							<div class="list-group-item">
								<input type="text" class="range-slider"  value="" />
								<span class="help-block m-b-none">拖动圆圈，重叠后即可解锁<lable class="valtip"></lable></span>
							</div>
							<div class="list-group-item">
								<div class="m-t-xs">
    								<input type="hidden" value="{{ csrf_token() }}" name="_token" />
    								<button class="btn m-b-xs w-xs btn-info">注册</button>
    								<a href="/Login/index" class="btn m-b-xs w-xs btn-primary">登录</a>
								</div>
							</div>
						</form>
					</div>
					<div class="text-center ng-scope" >
						<p class="ng-scope">
							<small>Copyright © 2017 <a href="http://xapi.smaty.net?type"
								class="text-success" target="_blank">xApi Manager</a> All Rights
								Reserved.
							</small>
						</p>
					</div>
				</div>
			</div>


			<!-- page-content -->
		</div>
		<!-- /content -->
		<script type="text/javascript" src="{{URL::asset('js/validation/jquery.validate.js')}}"></script>
		<script type="text/javascript" src="{{URL::asset('js/validation/messages_zh.js')}}"></script>
		<script type="text/javascript" src="{{URL::asset('js/md5.min.js')}}"></script>
		<script type="text/javascript" charset="utf-8">

			
    		var $range = $(".range-slider");
    		var valtip = $(".valtip");
    		var from = {{$data['from'] or  '20'}};
    		var to = {{$data['to'] or '80'}};
    		var rangeslider = md5(from+"#"+to);
    		var rand = 0;
    		$range.ionRangeSlider({
    		    type: "double",
    		    min: 0,
    		    max: 100,
    		    from: from,
    		    to: to
    		});
    		$range.on("change", function () {
    		    var $this = $(this),
    		    from = $this.data("from"),
    		    to = $this.data("to");
		        $(".irs-from").remove();
		        $(".irs-to").remove();
		        $(".irs-single").remove();
    		    if(from==to){
    		    	rand = to;
    		    	valtip.removeClass('text-danger').addClass('text-success').text(' 解锁成功 ');
        		}
        		if(rand && from!=to){
        			rand = 0;
        			valtip.removeClass('text-success').addClass('text-danger').text(' 解锁失败 ');
            	}
    		});

    		$(function(){

    			//手机号验证
    			jQuery.validator.addMethod("phoneCheck", function(value, element) {       
    				return this.optional(element) || /^1\d{10}$/.test(value);       
    				}, "请输入正确的手机号");
    			var _token = $("input[name='_token']").val();
    			var validator = $("#myForm").validate({
    				submitHandler: function(form) {
    					//获取用户信息
    					var username = $('input[name="username"]').val();
    					var password = $('input[name="password"]').val();
    					var phone    = $('input[name="phone"]').val();
    					var email    = $('input[name="email"]').val();
    					var _token   = $("input[name='_token']").val();
    					if(rand>0){
    						var index = layer.load(0, {shade: false});
    						$.ajax({
        		                cache: false,
        		                type: "POST",
        		                url:"{{route('Register.store')}}",
        		                data:{
        		                	'username':username,
        		                	'password':password,
        		                	'phone':phone,
        		                	'email':email,
        		                	'rand':rand,
        		                	'rangeslider':rangeslider
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
        				}else{
        					valtip.removeClass('text-success').addClass('text-danger').text(' 解锁失败 ')
            			};
    					
    					
    				},
    				rules:{
    					username:{
    						required:true,
    						maxlength:20,
    						minlength:2,
    						remote: {
    							url: "{{route('Register.check')}}",
    		                    type: "post",
    		                    dataType: "json",  
    		                    data: {   
    		                     	username: function(){
    		                     		return $('input[name="username"]').val();
    		                     	}
    		                	},
    		                	headers: {
    		                        'X-CSRF-TOKEN': _token
    		                    },
    		               }
    					},
    					password:{
    		    			required:true,
    		    			maxlength:20,
    		    			minlength:6
    		    		},
    		    		phone:{
    		    			required:true,
    		    			phoneCheck:true,
    		    			remote: {
    							url: "{{route('Register.check')}}",
    		                    type: "post",
    		                    dataType: "json",  
    		                    data: {   
    		                     	phone: function(){
    		                     		return $('input[name="phone"]').val();
    		                     	}
    		                	},
    		                	headers: {
    		                        'X-CSRF-TOKEN': _token
    		                    },
    		               }
    		    		},
    		    		email:{
    		    			required:true,
    		    			email:true,
    		    			remote: {
    							url: "{{route('Register.check')}}",
    		                    type: "post",
    		                    dataType: "json",  
    		                    data: {   
    		                     	email: function(){
    		                     		return $('input[name="email"]').val();
    		                     	}
    		                	},
    		                	headers: {
    		                        'X-CSRF-TOKEN': _token
    		                    },
    		               }
    		    		},
    				},
    				messages:{
    					username :{
    						required:"用户名不能为空",
    						maxlength:"不能超过20个字符",
    						minlength:"不能少于2个字符",
    						remote:"该用户名已被使用",
    					},
    					password:{
    		    			required:"密码不能为空",
    		    			maxlength:"不能超过20个字符",
    						minlength:"不能少于6个字符",
    		    		},
    		    		phone : {
    			            required : "请输入手机号",
    			            remote:"该手机号已被使用",
    			        },
    			        email : {
    			            required : "请输入邮箱",
    			            email : "请输入正确的邮箱",
    			            remote:"该邮箱已被使用",
    			        },
    			        
    				},
    				errorElement: 'custom',
    				errorClass:'error',
    				errorPlacement: function(error, custom) {
    					error.appendTo( custom.next('span') ); 
    				},  
    			})
    		})
    				
		</script>
	</div>
	<div id="flotTip" style="display: none; position: absolute;"></div>
	@include('public/footerjs')
</body>
</html>