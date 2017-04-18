@extends('base') @section('page-content')
<div class="page-content">
	<!-- #section:settings.box -->
	<div></div>
	@include('public/setbox')
	<div class="row">
		<div class="col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<form class="form-horizontal"
				action="{{route('menu.store')}}" method="post" id="myForm">
				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-10"> 上级菜单 </label> <input name="id" value=""
						type="hidden">
					<div class="col-sm-9">
						<select id="pid" name="pid" class="rcol-xs-10 col-sm-5">
							<option value="0" selected="selected">顶级菜单</option>
							@foreach($sys['ValidMenu'] as $menu)
								<option value="{{$menu['id']}}" @if(!empty($cmenu['pid']) && $menu['id'] == $cmenu['pid']) selected="selected" @endif>{{$menu['title']}}</option>
							@endforeach
						</select> 
						<span class="help-inline col-xs-12 col-sm-7"> 
						<span class="middle"></span>
						</span>
					</div>
				</div>
				<div class="space-4"></div>
				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-1"> 菜单名称 </label>
					<div class="col-sm-9">
						<input type="text" name="title" id="title"
							class="rcol-xs-10 col-sm-5" value="{{$cmenu['title'] or ''}}"> <span
							class="help-inline col-xs-12 col-sm-7"> <span class="middle"></span>
						</span>
					</div>
				</div>

				<div class="space-4"></div>
				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-2"> 链接 </label>
					<div class="col-sm-9">
						<input type="text" name="path" id="name"
							placeholder="链接，如：/Index/index" class="col-xs-10 col-sm-5"
							value="{{$cmenu['path'] or ''}}"> <span class="help-inline col-xs-12 col-sm-7"> <span
							class="middle"></span>
						</span>
					</div>
				</div>

				<div class="space-4"></div>
				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-2"> ICON图标 </label>
					<div class="col-sm-9">
						<input type="text" name="icon" id="icon" placeholder="ICON图标"
							class="col-xs-10 col-sm-5" value="{{$cmenu['icon'] or ''}}"> <span
							class="help-inline col-xs-12 col-sm-7"> <span class="middle"></span>
						</span>
					</div>
				</div>
				<div class="space-4"></div>
				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-2"> 显示状态 </label>
					<div class="control-label no-padding-left col-sm-1">
						<label> <input name="status" id="status" value="1"
							@if ((!empty($cmenu['status']) && $cmenu['status'] == 1) || empty($cmenu['status'])) checked="checked" @endif 
							class="ace ace-switch ace-switch-2" type="checkbox"> <span
							class="lbl"></span>
						</label>
					</div>
					<span class="help-inline col-xs-12 col-sm-7"> <span class="middle"></span>
					</span>
				</div>
				<div class="space-4"></div>
				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-2"> 排序 </label>
					<div class="col-sm-9">
						<input type="text" name="sort" id="o" placeholder=""
							class="col-xs-10 col-sm-5" value="{{$cmenu['sort'] or ''}}"> <span
							class="help-inline col-xs-12 col-sm-7"> <span class="middle">越小越靠前</span>
						</span>
					</div>
				</div>
				<div class="space-4"></div>
				<input type="hidden" value="{{ csrf_token() }}" name="_token" />
				<input type="hidden" value="{{$cmenu['id'] or ''}}" name="mid" />
				<div class="col-md-offset-2 col-md-9">
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
<script type="text/javascript"
	src="{{URL::asset('js/validation/jquery.validate.js')}}"></script>
<script type="text/javascript"
	src="{{URL::asset('js/validation/messages_zh.js')}}"></script>
<script type="text/javascript" charset="utf-8">

$(function(){
	var validator = $("#myForm").validate({
		submitHandler: function(form) {	
			//获取菜单信息
			var mid = $("input[name='mid']").val();
			var pid = $('select[name="pid"]').val();
			var title    = $('input[name="title"]').val();
			var path     = $('input[name="path"]').val();
			var icon     = $('input[name="icon"]').val();
			var sort     = $('input[name="sort"]').val();
			var status   = $('input[name="status"]').prop('checked');
			var _token = $("input[name='_token']").val();
			
			$.ajax({
                cache: false,
                type: "POST",
                url:"{{route('menu.store')}}",
                data:{
                    'id':mid,
                	'pid':pid,
                	'title':title,
                	'path':path,
                	'icon':icon,
                	'sort':sort,
                	'status':status,
                },
                headers: {
                    'X-CSRF-TOKEN': _token
                },
                dataType: 'json',
                success: function(res) {
                	if(res.status){
                		layer.msg(res.message, {icon: 1})
                		setTimeout(function(){
							 window.location.reload();
						 }, 2000);
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
			path:{
				required:true,
				minlength:1,
			},
			title:{
				required:true,
				minlength:2,
			}
		},
		messages:{
			path:{
				required:"链接不能为空",
				minlength:'至少为1个字符',
			},
			title:{
				required:"菜单名称不能为空",
				minlength:'至少为2个字符',
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
