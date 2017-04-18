@extends('base') @section('page-content')
<script type="text/javascript" src="{{URL::asset('js/jquery-1.9.1.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/layer/layer.js')}}"></script>
<div class="page-content">
	<!-- #section:settings.box -->
	@include('public/setbox')
	<div class="row">
		<div class="col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<form class="form-horizontal"
				action="{{route('user.store')}}" method="post" id="myForm">
				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-1"> 用户名 </label>
					<div class="col-sm-9">
						<input type="text" name="username" class="rcol-xs-10 col-sm-5"
							value="{{$info['user']['username'] or ''}}"> 
							<span class="help-inline col-xs-12 col-sm-7"> 
								<span class="middle"></span>
							</span>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-10"> 用户组 </label>
					<div class="col-sm-9">
						<select id="group_id" name="group_id" class="rcol-xs-10 col-sm-5">
							@foreach ($info['group'] as $key=>$groupname)
								<option value="{{$key}}" @if(!empty($info['userGroup']) && $info['userGroup']['group_id']==$key) selected @endif>{{$groupname}}</option>
							@endforeach
						</select> 
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-2"> 用户密码 </label>
					<div class="col-sm-9">
						<input type="password" name="password" id="password"
							placeholder="用户密码" class="col-xs-10 col-sm-5" value="@if(!empty($info['user']['uid'])) security @endif"> 
						<span class="help-inline col-xs-12 col-sm-7"> 
							<span class="middle"></span>
						</span>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-4"> 头像 </label>
					<div class="col-sm-9">
						<div>
							@include('User/avatar')
						</div>
					</div>

				</div>
				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-4"> 性别 </label>
					<div class="col-sm-9 margin710">
						<input type="radio" name="sex" value="1" @if(empty($info['user']['sex']) || (!empty($info['user']['sex']) && $info['user']['sex']==1)) checked @endif>男 
						<input type="radio" name="sex" value="2" @if(!empty($info['user']['sex']) && $info['user']['sex']==2) checked @endif>女
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-6"> 手机号码 </label>
					<div class="col-sm-9">
						<input type="text" name="phone" id="phone" placeholder="电话号码"
							class="col-xs-10 col-sm-5" value="{{$info['user']['phone'] or ''}}"> <span
							class="help-inline col-xs-12 col-sm-7"> <span class="middle"></span>
						</span>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-8"> E-mail </label>
					<div class="col-sm-9">
						<input type="email" name="email" id="email" placeholder="E-mail"
							class="col-xs-10 col-sm-5" value="{{$info['user']['email'] or ''}}"> <span
							class="help-inline col-xs-12 col-sm-7"> <span class="middle"></span>
						</span>
						
					</div>
				</div>
				
				<div class="space-4"></div>
				
				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-10"> 状态 </label>
					<div class="col-sm-9">
						<select id="status_id" name="status_id" class="rcol-xs-10 col-sm-5">
							<option value="1" @if(empty($info['user']['status']) || (!empty($info['user']['status']) && $info['user']['status']==1)) selected @endif>在职</option>
							<option value="2" @if(!empty($info['user']['status']) && $info['user']['status']==2) selected @endif>离职</option>
						</select> 
					</div>
				</div>

				<div class="space-4"></div>

				<div class="col-md-offset-2 col-md-9 mgt20">
					<input type="hidden" value="{{ csrf_token() }}" name="_token" />
					<input type="hidden" value="{{$info['user']['uid'] or ''}}" name="userid" />
					<button class="btn btn-info" type="submit">
						<i class="icon-ok bigger-110"></i> 提交
					</button>

					&nbsp; &nbsp; &nbsp;
					<button class="btn" type="reset">
						<i class="icon-undo bigger-110"></i> 重置
					</button>
				</div>
			</form>
			<!-- PAGE CONTENT ENDS -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
</div>
<!-- /.page-content -->

<script type="text/javascript" src="{{URL::asset('js/validation/jquery.validate.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validation/messages_zh.js')}}"></script>
<script type="text/javascript" charset="utf-8">

$(function(){
	
	//手机号验证
	jQuery.validator.addMethod("phoneCheck", function(value, element) {       
		return this.optional(element) || /^1\d{10}$/.test(value);       
		}, "请填写正确的手机号");
	var validator = $("#myForm").validate({
		submitHandler: function(form) {
			
			//获取用户信息
			var username = $('input[name="username"]').val();
			var group_id = $('select[name="group_id"]').val();
			var sex = $("input[name='sex']:checked").val();
			var password    = $('input[name="password"]').val();
			var phone     = $('input[name="phone"]').val();
			var email     = $('input[name="email"]').val();
			var status_id = $('select[name="status_id"]').val();
			var avatar     = $('.uploadPic').attr('src');
			var _token = $("input[name='_token']").val();
			var userid = $('input[name="userid"]').val();
			$.ajax({
                cache: false,
                type: "POST",
                url:"{{route('user.store')}}",
                data:{
                	'username':username,
                	'group_id':group_id,
                	'sex':sex,
                	'password':password,
                	'phone':phone,
                	'email':email,
                	'status_id':status_id,
                	'avatar':avatar,
                	'userid':userid
                },
                headers: {
                    'X-CSRF-TOKEN': _token
                },
                dataType: 'json',
                success: function(res) {
                	if(res.status==200){
                		layer.msg(res.message, {icon: 1})
                	}else{
                		layer.alert(res.message, {icon: 2})
                	}
                },
                error: function(request) {
                    layer.msg("网络错误，请稍后重试");
                },
            });
		},
		rules:{
			username:{
				required:true,
				maxlength:20,
				minlength:2
			},
			password:{
    			required:true,
    			maxlength:20,
    			minlength:6
    		},
    		phone:{
    			required:true,
    			phoneCheck:true
    		},
    		email:{
    			required:true,
    			email:true
    		},
		},
		messages:{
			username :{
				required:"用户名不能为空",
				maxlength:"不能超过20个字符",
				minlength:"不能少于2个字符",
			},
			password:{
    			required:true,
    			maxlength:"不能超过20个字符",
				minlength:"不能少于6个字符",
    		},
			phone : {
	            required : "请填写手机号",
	        },
	        email : {
	            required : "请输入邮箱",
	            email : "请正确填写您的邮箱"
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
@endsection
