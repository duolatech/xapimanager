@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/chosen.css')}}">

<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
		<!-- main -->
		<div class="col">
			<!-- content -->
			<div class="wrapper-md ng-scope">
				<div class="panel panel-default">
					<div class="panel-heading font-bold">@if(!empty($data['group']['id'])) 编辑权限组 @else 添加权限组 @endif</div>
					<div class="panel-body">
						<form id="myForm" method="post"
							class="form-horizontal ng-pristine ng-valid ng-valid-date ng-valid-required ng-valid-parse ng-valid-date-disabled">
							<div class="form-group">
								<label class="col-sm-2 control-label">权限组名称</label>
								<div class="col-sm-6">
									<input type="text" name="groupname"  placeholder="用户组名" class="form-control" value="{{$data['group']['groupname'] or ''}}">
									<span class="help-block m-b-none" style="color:red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label">权限组描述</label>
								<div class="col-sm-6">
									<textarea name="description" class="form-control" rows="6" placeholder="">{{$data['group']['description'] or ''}}</textarea>
									<span class="help-block m-b-none" style="color:red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label">状态</label>
								<div class="col-sm-6">
									<label class="i-switch m-t-xs m-r">
                                      <input name="status" type="checkbox" @if ((!empty($data['group']['status']) && $data['group']['status'] == 1) || empty($data['group']['status'])) checked="checked" @endif>
                                      <i></i>
                                    </label>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
                              <div class="col-sm-4 col-sm-offset-2">
                              	<input type="hidden" value="{{ csrf_token() }}" name="_token" />
                              	<input type="hidden" name="gid"  value="{{$data['group']['id'] or ''}}">
                                <button type="submit" class="btn btn-primary btn-info-submit">保存权限组</button>
                              </div>
                            </div>
                            
                            <div class="form-group" style="margin-top:180px;"></div>
                            
						</form>
					</div>
				</div>
			</div>
			<!-- /content -->
			<script type="text/javascript" src="{{URL::asset('js/validation/jquery.validate.js')}}"></script>
			<script type="text/javascript" src="{{URL::asset('js/validation/messages_zh.js')}}"></script>
			<script type="text/javascript" charset="utf-8">
			var validator = $("#myForm").validate({
				submitHandler: function(form) {	
					$.ajax({
		                cache: false,
		                type: "POST",
		                url:"{{route('group.store')}}",
		                data:$('#myForm').serialize(),
		                headers: {
		                    'X-CSRF-TOKEN':$("input[name='_token']").val()
		                },
		                dataType: 'json',
		                success: function(res) {
		                	if(res.status==200){
		                		layer.msg(res.message);
		                		setTimeout(function(){
									window.location.href="{{route('group.index')}}";
								}, 2000);
		                	}else{
		                		layer.msg(res.message);
		                	}
		                },
		                error: function(request) {
		                    layer.msg("网络错误，请稍后重试");
		                },
		            });
				},
				rules:{
					groupname:{
						required:true,
						minlength:2,
					},
					description:{
						required:true,
						minlength:6,
					}
				},
				messages:{
					groupname:{
						required:"用户组名称，不能为空",
					},
					description:{
						required:"描述不能为空",
						minlength:"至少6个字符",
					}
				},
				errorElement: 'custom',
				errorClass:'error',
				errorPlacement: function(error, custom) {
					error.appendTo( custom.next('span') ); 
				},  
			})
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
