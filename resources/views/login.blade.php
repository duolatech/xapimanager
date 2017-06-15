<!DOCTYPE html>
<!--[if lt IE 7 ]> <html lang="en" class="ie6 ielt8"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="ie7 ielt8"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html lang="zh-cn"> <!--<![endif]-->
<head>
<meta charset="utf-8">
<title>{{$sys['Website']['title']}}</title>
<meta name="keywords" content="{{$sys['Website']['keywords']}}" />
<meta name="description" content="{{$sys['Website']['description']}}" />
<link rel="stylesheet" type="text/css" href="{{URL::asset('css/login.css')}}" />
</head>
<body>
<div class="container">
	<section id="content">
		<form action="{{route('login.index')}}" method="post">
			<h1>{{$sys['Website']['title']}}</h1>
			<div>
				<input type="text" placeholder="邮箱"  name="email" id="username" />
			</div>
			<div>
				<input type="password" placeholder="密码" name="password" id="password" />
			</div>
			<div>
					<input type="text"  placeholder="请输入验证码" name="verifycode"  id="password" >
					<img width="96%" height="50" src="{{url('captcha/mews')}}" id="verifycode" title="点击刷新" >
			</div>
			<div>
				<input type="hidden" value="{{ csrf_token() }}" name="_token" />
				<input id="submit" type="button" value="登录" class="loginbtn"/>
			</div>
		</form><!-- form -->
	</section><!-- content -->
</div><!-- container -->
<script src="{{URL::asset('js/jquery-1.9.1.min.js')}}"></script>
<script src="{{URL::asset('js/jquery.md5.js')}}"></script>
<script src="{{URL::asset('js/layer/layer.js')}}"></script>
<script>
	$(function(){
		//登陆
		$("body").bind("keyup",function(event) {  
		    if(event.keyCode==13){  
		    	ajaxLogin();  
		    }     
		}); 
		$("#submit").on("click",function(){
			ajaxLogin();
		 })
		function ajaxLogin(){
			
			 var user = $("input[name='email']").val();
			 var pass = $("input[name='password']").val();
			 var verifycode = $("input[name='verifycode']").val();
			 var _Token = $("input[name='_token']").val();
			 $.ajax({
				 type:"post",
				 cache:false,
				 dataType:"json",
				 url:"{{route('login.index')}}",
				 data:{'user':user,'pass':$.md5(pass),'captcha':verifycode, '_token':_Token},
				 headers: {
                      'X-CSRF-TOKEN': _Token
                  },
				 success:function(res){
					 if(res.status==200){
						 window.location.href="/Api/list";
					 }else{
						 layer.alert(res.message, {'icon':5,'skin':'layer-ext-moon'});
						 $("#verifycode").attr("src", "{{url('captcha/mews')}}"+'?random='+Math.random()); 
					 }
				 },
				 error:function(msg){
					 layer.alert('网络错误，请稍后重试', 8); 
				 }
			 })
		}
	});
	$("#verifycode").click(function(){
		$(this).attr("src", "{{url('captcha/mews')}}"+'?random='+Math.random()); 
	})
</script>
</body>
</html>