@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/chosen.css')}}">
<link rel="stylesheet" href="{{URL::asset('js/umeditor/themes/default/css/umeditor.css')}}">

<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
		<!-- main -->
		<div class="col">
			<!-- content -->
			<div class="wrapper-md ng-scope">
				<div class="panel panel-default">
					<div class="panel-heading font-bold">
						添加帮助中心
					</div>
					<div class="panel-body">
						<form id="myForm" method="post"
							class="form-horizontal ng-pristine ng-valid ng-valid-date ng-valid-required ng-valid-parse ng-valid-date-disabled">
							<div class="form-group">
								<label class="col-sm-2 control-label">标题</label>
								<div class="col-sm-6">
									<input name="title" type="text" class="form-control" value="{{$info['title'] or ''}}"> 
									<span class="help-block m-b-none" style="color:red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label">内容</label>
								<div class="col-sm-8">
									<!--style给定宽度可以影响编辑器的最终宽度-->
                                    <script type="text/plain" id="myEditor" style="height:240px;">@if(!empty($info['id'])){!!$info['content'] or ''!!}@endif</script>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							
							<div class="form-group">
                              <div class="col-sm-4 col-sm-offset-2">
                              	<input type="hidden" value="{{ csrf_token() }}" name="_token" />
                              	<input type="hidden" value="{{$info['id'] or ''}}" name="helpid" />
                                <button type="submit" class="btn btn-primary btn-info-submit">保存</button>
                              </div>
                            </div>
                            
                            <div class="form-group" style="margin-top:180px;"></div>
                            
						</form>
					</div>
				</div>
			</div>
			<!-- /content -->
			<script type="text/javascript" src="{{URL::asset('js/umeditor/third-party/template.min.js')}}"></script>
    		<script type="text/javascript" src="{{URL::asset('js/umeditor/umeditor.config.js')}}"></script>
   		 	<script type="text/javascript" src="{{URL::asset('js/umeditor/umeditor.min.js')}}"></script>
    		<script type="text/javascript" src="{{URL::asset('js/umeditor/lang/zh-cn/zh-cn.js')}}"></script>
			<script type="text/javascript" src="{{URL::asset('js/validation/jquery.validate.js')}}"></script>
			<script type="text/javascript" src="{{URL::asset('js/validation/messages_zh.js')}}"></script>
			<script type="text/javascript" charset="utf-8">

				//实例化编辑器
		    	var um = UM.getEditor('myEditor');
				//表单验证
				var validator = $("#myForm").validate({
					submitHandler: function(form) {
						var id = $("input[name='helpid']").val();
						var title = $("input[name='title']").val();
						var content  = um.getContent();
						var _token = $("input[name='_token']").val();

						$(".btn-info-submit").attr('disabled',true);
						$.ajax({
			                cache: false,
			                type: "POST",
			                url:"{{route('help.store')}}",
			                data:{
				                'id':id,
			                    'title':title,
			                    'content':content,
			                },
			                headers: {
			                    'X-CSRF-TOKEN': _token
			                },
			                dataType: 'json',
			                success: function(res) {
								$(".btn-info-submit").attr('disabled',false);
			                	if(res.status==200){
			                		layer.msg(res.message);
			                		setTimeout(function(){
										 window.location.href = "{{route('help.index')}}";;
									 }, 2000);
			                	}else{
			                		layer.msg(res.message);
			                	}
			                },
			                error: function(request) {
								$(".btn-info-submit").attr('disabled',false);
			                    layer.msg("网络错误，请稍后重试");
			                },
				            });
						
					},
					rules:{
						title:{
							required:true,
							maxlength:60,
							minlength:2
						}
					},
					messages:{
						title :{
							required:"标题不能为空",
							maxlength:"不能超过60个字符",
							minlength:"不能少于2个字符",
						}
					},
					errorElement: 'custom',
					errorClass:'error',
					errorPlacement: function(error, custom) {
						error.appendTo(custom.next('span'))
					},  
				})
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
