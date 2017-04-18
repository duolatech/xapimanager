@extends('base') @section('page-content')
<div class="page-content">
	<!-- #section:settings.box -->
	<div class="alert alert-block alert-success">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>
		<!--i class="ace-icon fa fa-check green"></i-->
		这是网站设置的提示。
	</div>
	@include('public/setbox')
	<div class="row">
		<div class="col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<form action="" class="form-horizontal" id="myForm" method="post">
				<div class="space-4"></div>
				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-1"> 网站名称 </label>
					<div class="col-sm-9">
						<input type="text" name="sitename"
							placeholder="网站名称" class="col-xs-10 col-sm-5" value=" {{$sys['Website']['sitename']}}">
						<span class="help-inline col-xs-12 col-sm-7"> </span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-1"> 网站标题 </label>
					<div class="col-sm-9">
						<input type="text" name="title" placeholder="xApi Manager"
							class="col-xs-10 col-sm-5" value=" {{$sys['Website']['title']}}">
							<span class="help-inline col-xs-12 col-sm-7"> </span>
					</div>
				</div>
				<div class="space-4"></div>


				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-2">关键词 </label>
					<div class="col-sm-9">
						<input type="text" name="keywords" placeholder="关键词"
							class="col-xs-10 col-sm-5" value=" {{$sys['Website']['keywords']}}"> 
							<span class="help-inline col-xs-12 col-sm-7"> </span>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-2"> 网站描述 </label>
					<div class="col-sm-9">
						<textarea name="description" placeholder="网站描述"
							class="col-xs-10 col-sm-5" rows="5">{{$sys['Website']['description']}}</textarea>
						<span class="help-inline col-xs-12 col-sm-7"> </span>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-2"> 底部版权 </label>
					<div class="col-sm-9">
						<textarea name="copyright" placeholder="底部版权"
							class="col-xs-10 col-sm-5" rows="5">{{$sys['Website']['copyright']}}</textarea>
						<span class="help-inline col-xs-12 col-sm-7"> </span>
					</div>
				</div>
				<div class="space-4"></div>
				<input type="hidden" value="{{ csrf_token() }}" name="_token" />
				<div class="col-md-offset-2 col-md-9">
					<input type="submit"  value="提交" class="btn btn-info submit">
					
					&nbsp; &nbsp; &nbsp;
					<input type="reset"  value="重置" class="btn">
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
                		layer.msg(res.message, {icon: 1})
                	}else{
                		layer.msg(res.message, {icon: 2});
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
@endsection
