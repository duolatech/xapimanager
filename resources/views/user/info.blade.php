@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('js/cropper/cropper.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('js/cropper/sitelogo.css')}}">
<script type="text/javascript" src="{{URL::asset('js/cropper/cropper.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/cropper/sitelogo.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/cropper/bootstrap-3.3.4.js')}}"></script>

<div class="app-content-body app-content-full fade-in-up ng-scope h-full">
<div class="hbox hbox-auto-xs hbox-auto-sm bg-light  ng-scope" >

  <div class="col bg-white-only">
      <div class="wrapper-sm b-b">
        <div class="m-t-n-xxs m-b-n-xxs m-l-xs">
          <a class="btn btn-xs btn-default ng-hide">编辑</a>
        </div>
      </div>
      <div class="row-row">
        <div class="cell">
          <div class="cell-inner">
          <form action="{{route('user.store')}}" method="post" id="myForm">
            <div class="wrapper-lg">
              <div class="hbox h-auto m-b-lg">
                <div class="col text-center w-sm">
                  <div class="thumb-lg avatar inline">
                    <img class="userAvatar" src="{{$info['user']['avatar'] or '/img/avatar.jpg'}}">
                  </div>
                  <label class="btn btn-default" data-toggle="modal" data-target="#avatar-modal" style="margin: 10px;">
                      <i class="fa fa-cloud-upload text"></i>
                      <span class="text">上传</span>
                    </label>
                </div>
              </div>
              <!-- fields -->
              <div class="form-horizontal">
                <div class="form-group m-b-sm">
                  <label class="col-sm-3 control-label">用户名</label>
                  <div class="col-sm-6">
                    <input type="text" name="username" class="form-control ng-pristine ng-valid ng-touched"
                    	value="{{$info['user']['username'] or ''}}">
                    	<span class="help-block m-b-none" style="color: red;"></span>
                  </div>
                </div>
                <div class="form-group m-b-sm">
                  <label class="col-sm-3 control-label">用户组</label>
                  <div class="col-sm-6">
                    <select name="group_id"class="form-control m-b-xs valid">
						@foreach ($info['group'] as $key=>$groupname)
							<option value="{{$key}}" @if(!empty($info['userGroup']) && $info['userGroup']['group_id']==$key) selected @endif>{{$groupname}}</option>
						@endforeach
					</select>
                  </div>
                </div>
                <div class="form-group m-b-sm">
                  <label class="col-sm-3 control-label">用户密码</label>
                  <div class="col-sm-6">
                    <input type="password" name="password" class="form-control ng-pristine ng-valid ng-touched"
                    	value="@if(!empty($info['user']['uid']) && !empty($info['user']['password'])) security @endif">
                    	<span class="help-block m-b-none" style="color: red;"></span>
                  </div>
                </div>
                <div class="form-group m-b-sm">
                  <label class="col-sm-3 control-label">手机号</label>
                  <div class="col-sm-6">
                    <input type="text" name="phone" class="form-control ng-pristine ng-valid ng-touched"
                    	value="{{$info['user']['phone'] or ''}}">
                    	<span class="help-block m-b-none" style="color: red;"></span>
                  </div>
                </div>
                <div class="form-group m-b-sm">
                  <label class="col-sm-3 control-label">E-mail</label>
                  <div class="col-sm-6">
                    <input type="text" name="email" class="form-control ng-pristine ng-valid ng-touched"
                    	value="{{$info['user']['email'] or ''}}">
                    	<span class="help-block m-b-none" style="color: red;"></span>
                  </div>
                </div>
                <div class="form-group m-b-sm">
                  <label class="col-sm-3 control-label">状态</label>
                  <div class="col-sm-6">
                  	  <select name="status_id" class="form-control m-b-xs valid">
					  		<option value="1" @if(empty($info['user']['status']) || (!empty($info['user']['status']) && $info['user']['status']==1)) selected @endif>在职</option>
							<option value="2" @if(!empty($info['user']['status']) && $info['user']['status']==2) selected @endif>离职</option>
						</select> 
                  </div>
                </div>
                <div class="form-group m-b-sm">
                  <label class="col-sm-3 control-label">  </label>
                  <div class="col-sm-6">
                  	  <input type="hidden" value="{{ csrf_token() }}" name="_token" />
					  <input type="hidden" value="{{$info['user']['uid'] or ''}}" name="userid" />
                  	  <button type="submit" class="btn btn-info btn-info-submit">保 存</button>
                  </div>
                </div>
                <div class="form-group m-b-lg">
                </div>
              </div>
              <!-- / fields -->
            </div>
            </form>
          </div>
        </div>
      </div>
      
        <!-- 头像上传 -->
		<div class="modal fade" id="avatar-modal" aria-hidden="true" aria-labelledby="avatar-modal-label" role="dialog" tabindex="-1">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<!--<form class="avatar-form" action="upload-logo.php" enctype="multipart/form-data" method="post">-->
					<form class="avatar-form">
						<div class="modal-header">
							<button class="close" data-dismiss="modal" type="button">&times;</button>
							<h4 class="modal-title" id="avatar-modal-label">上传图片</h4>
						</div>
						<div class="modal-body">
							<div class="avatar-body">
								<div class="avatar-upload">
									<input class="avatar-src" name="avatar_src" type="hidden">
									<input class="avatar-data" name="avatar_data" type="hidden">
									<label for="avatarInput" style="line-height: 35px;">图片上传</label>
									<button class="btn btn-danger"  type="button" style="height: 35px;" onclick="$('input[id=avatarInput]').click();">请选择图片</button>
									<span id="avatar-name"></span>
									<input class="avatar-input hide" id="avatarInput" name="avatar_file" type="file"></div>
								<div class="row">
									<div class="col-md-9">
										<div class="avatar-wrapper"></div>
									</div>
									<div class="col-md-3">
										<div class="avatar-preview preview-lg" id="imageHead"></div>
										<!--<div class="avatar-preview preview-md"></div>
								<div class="avatar-preview preview-sm"></div>-->
									</div>
								</div>
								<div class="row avatar-btns">
									<div class="col-md-4">
										<div class="btn-group">
											<button class="btn btn-danger fa fa-undo" data-method="rotate" data-option="-90" type="button" title="Rotate -90 degrees"> 向左旋转</button>
										</div>
										<div class="btn-group">
											<button class="btn  btn-danger fa fa-repeat" data-method="rotate" data-option="90" type="button" title="Rotate 90 degrees"> 向右旋转</button>
										</div>
									</div>
									<div class="col-md-5" style="text-align: right;">								
										<button class="btn btn-danger fa fa-arrows" data-method="setDragMode" data-option="move" type="button" title="移动">
							            <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="$().cropper(&quot;setDragMode&quot;, &quot;move&quot;)">
							            </span>
							          </button>
							          <button type="button" class="btn btn-danger fa fa-search-plus" data-method="zoom" data-option="0.1" title="放大图片">
							            <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="$().cropper(&quot;zoom&quot;, 0.1)">
							              <!--<span class="fa fa-search-plus"></span>-->
							            </span>
							          </button>
							          <button type="button" class="btn btn-danger fa fa-search-minus" data-method="zoom" data-option="-0.1" title="缩小图片">
							            <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="$().cropper(&quot;zoom&quot;, -0.1)">
							              <!--<span class="fa fa-search-minus"></span>-->
							            </span>
							          </button>
							          <button type="button" class="btn btn-danger fa fa-refresh" data-method="reset" title="重置图片">
								            <span class="docs-tooltip" data-toggle="tooltip" title="" data-original-title="$().cropper(&quot;reset&quot;)" aria-describedby="tooltip866214">
								       </button>
							        </div>
									<div class="col-md-3">
										<button class="btn btn-danger btn-block avatar-save fa fa-save" type="button" data-dismiss="modal"> 保存修改</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>

		<div class="loading" aria-label="Loading" role="img" tabindex="-1"></div>
<!-- /头像上传 -->
      
  </div>
  <!-- /column -->
</div>
</div>

<script type="text/javascript" src="{{URL::asset('js/validation/jquery.validate.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validation/messages_zh.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/cropper/html2canvas.min.js')}}"></script>
<script type="text/javascript">

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
			var password    = $('input[name="password"]').val();
			var phone     = $('input[name="phone"]').val();
			var email     = $('input[name="email"]').val();
			var status_id = $('select[name="status_id"]').val();
			var _token = $("input[name='_token']").val();
			var userid = $('input[name="userid"]').val();
			var avatar = $(".userAvatar").attr('src');
			layer.load(0, {shade: false});
			$.ajax({
                cache: false,
                type: "POST",
                url:"{{route('user.store')}}",
                data:{
                	'username':username,
                	'group_id':group_id,
                	'password':password,
                	'phone':phone,
                	'email':email,
                	'status_id':status_id,
                	'userid':userid,
                	'avatar':avatar
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
							 window.location.href="{{route('user.index')}}";
						 }, 2000);
                	}else{
                		layer.msg(res.message)
                	}
                },
                error: function(request) {
                	layer.closeAll();
                    layer.msg("网络错误，请稍后重试");
                },
            });
		},
		rules:{
			username:{
				required:true,
				maxlength:20,
				minlength:2,
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
    			required:"密码不能为空",
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
	//头像验证
	$('#avatarInput').on('change', function(e) {
		var filemaxsize = 1024 * 2;//2M
		var target = $(e.target);
		var Size = target[0].files[0].size / 1024;
		if(Size > filemaxsize) {
			layer.msg('图片不能超过2M，请重新选择!');
			$(".avatar-wrapper").childre().remove;
			return false;
		}
		if(!this.files[0].type.match(/image.*/)) {
			layer.msg('请选择正确的图片!')
		} else {
			var filename = document.querySelector("#avatar-name");
			var texts = document.querySelector("#avatarInput").value;
			var teststr = texts; //你这里的路径写错了
			testend = teststr.match(/[^\\]+\.[^\(]+/i); //直接完整文件名的
			filename.innerHTML = testend;
		}
	
	});

	$(".avatar-save").on("click", function() {
		var img_lg = document.getElementById('imageHead');
		// 截图小的显示框内的内容
		html2canvas(img_lg, {
			allowTaint: true,
			taintTest: false,
			onrendered: function(canvas) {
				canvas.id = "mycanvas";
				//生成base64图片数据
				var dataUrl = canvas.toDataURL("image/jpeg");
				var newImg = document.createElement("img");
				newImg.src = dataUrl;
				imagesAjax(dataUrl)
			}
		});
	})
	
	function imagesAjax(src) {
		var data = {};
		data.img = src;
		data.jid = $('#jid').val();
		data.uid = "{{$info['user']['uid'] or ''}}"
		var _token = $("input[name='_token']").val();
		$.ajax({
			url: "{{route('upload.avatar')}}",
			data: data,
            headers: {
                'X-CSRF-TOKEN': _token
            },
			type: "POST",
			dataType: 'json',
			success: function(re) {
				if(re.status == '200') {
					$('.userAvatar').attr('src',src );
				}
			}
		});
	}
</script>
<!-- /.page-content -->
@endsection
