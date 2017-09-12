@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/chosen.css')}}">

<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
		<!-- main -->
		<div class="col">
			<!-- main header -->
			<div class="bg-light lter b-b wrapper-md">
				<div class="row">
					<div class="col-sm-6 col-xs-12">
						<h1 class="m-n font-thin h3 text-black">网站信息设置</h1>
					</div>
					<div class="col-sm-6 text-right hidden-xs">
						<div class="inline m-r text-left">
							<div class="m-b-xs">
								1290 <span class="text-muted">items</span>
							</div>
						</div>
						<div class="inline text-left">
							<div class="m-b-xs">
								<span class="text-muted"><i class="glyphicon glyphicon-th"></i></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- / main header -->
			<!-- content -->
			<div class="wrapper-md ng-scope">
				<div class="panel panel-default">
					<div class="panel-heading font-bold">网站设置</div>
					<div class="panel-body">
						<form id="myForm" method="post"
							class="form-horizontal ng-pristine ng-valid ng-valid-date ng-valid-required ng-valid-parse ng-valid-date-disabled">
							<div class="form-group">
								<label class="col-sm-2 control-label">网站名称</label>
								<div class="col-sm-6">
									<input name="sitename" type="text" class="form-control" placeholder="网站名称"  value="{{$sys['Website']['sitename']}}"> 
									<span class="help-block m-b-none" style="color:red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label">网站标题</label>
								<div class="col-sm-6">
									<input name="title" type="text" class="form-control" placeholder="xApi Manager" value="{{$sys['Website']['title']}}"> 
									<span class="help-block m-b-none" style="color:red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label">关键词</label>
								<div class="col-sm-6">
									<input name="keywords" type="text" class="form-control" placeholder="关键词" value="{{$sys['Website']['keywords'] or ''}}"> 
									<span class="help-block m-b-none" style="color:red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label">网站描述</label>
								<div class="col-sm-6">
									<textarea name="description" class="form-control" rows="6" placeholder="网站描述">{{$sys['Website']['description'] or ''}}</textarea>
									<span class="help-block m-b-none" style="color:red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label">底部版权</label>
								<div class="col-sm-6">
									<textarea name="copyright" class="form-control" rows="6" placeholder="底部版权">{{$sys['Website']['copyright'] or ''}}</textarea>
									<span class="help-block m-b-none" style="color:red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
                              <div class="col-sm-4 col-sm-offset-2">
                              	<input type="hidden" value="{{ csrf_token() }}" name="_token" />
                              	<input type="hidden" value="{{$data['id'] or ''}}" name="id" />
                                <button type="submit" class="btn btn-primary btn-info-submit">保存网站信息</button>
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
    			$(function(){
    				var validator = $("#myForm").validate({
    					submitHandler: function(form) {	
    						//获取站点信息
    						var sitename = $('input[name="sitename"]').val();
    						var title    = $('input[name="title"]').val();
    						var keywords     = $('input[name="keywords"]').val();
    						var description  = $('textarea[name="description"]').val();
    						var copyright  = $('textarea[name="copyright"]').val();
    						var _token = $("input[name='_token']").val();
    						$.ajax({
    			                cache: false,
    			                type: "POST",
    			                url:"{{route('site.store')}}",
    			                data:{
    			                	'sitename':sitename,
    			                	'title':title,
    			                	'keywords':keywords,
    			                	'description':description,
    			                	'copyright':copyright,
    			                },
    			                headers: {
    			                    'X-CSRF-TOKEN': _token
    			                },
    			                dataType: 'json',
    			                success: function(res) {
    			                	if(res.status){
    			                		layer.msg(res.message)
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
    						sitename:{
    							required:true,
    							minlength:2,
    						},
    						title:{
    							required:true,
    						}
    					},
    					messages:{
    						sitename:{
    							required:"至少为两个字符",
    						},
    						title:{
    							required:"网站标题不能为空",
    						}
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
	</div>
</div>
<!-- /.page-content -->
@endsection
