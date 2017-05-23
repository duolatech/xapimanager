@extends('base') @section('page-content')
<script type="text/javascript" src="{{URL::asset('js/jquery-1.9.1.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/layer/layer.js')}}"></script>
<div class="page-content">
	<!-- #section:settings.box -->
	<div class="alert alert-block alert-success">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>
		<!--i class="ace-icon fa fa-check green"></i--> 
		友情提示：添加子分类请在菜单 '分类信息>>接口分类' 中选择子分类进行添加
	</div>
	@include('public/setbox')
	<div class="row">
		<div class="col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<form class="form-horizontal"
				action="#" method="post" id="myForm">
				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-1"> 分类名称 </label>
					<div class="col-sm-9">
						<input type="text" name="classify" class="rcol-xs-10 col-sm-5"
							value="{{$info['classifyname'] or ''}}"> 
							<span class="help-inline col-xs-12 col-sm-7"> 
								<span class="middle"></span>
							</span>
					</div>
				</div>

				<div class="space-4"></div>
				
				<div class="form-group">
                    <label class="col-sm-1 control-label no-padding-right" for="form-field-2"> 分类描述 </label>
                    <div class="col-sm-9">
                        <textarea name="description" class="col-xs-10 col-sm-5" rows="5">{{$info['description'] or ''}}</textarea>
                        <span class="help-inline col-xs-12 col-sm-7">
									<span class="middle"></span>
								</span>
                    </div>
                </div>
				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-1"> 分类负责人 </label>
					<div class="col-sm-9">
						<input type="text" name="Duty" class="rcol-xs-10 col-sm-5 duty"
							value="" placeholder="输入后回车"> 
							<span class="help-inline col-xs-12 col-sm-7"> 
								<span class="middle classDuty"></span>
							</span>
					</div>
				</div>

				<div class="space-1"></div>
				
				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-1">  </label>
					<div class="col-sm-9">
						<div class="duty_person">
						@if(!empty($info['user']))
							@foreach($info['user'] as $value)
								<a class="btn btn-white btn-sm btn-primary ux" style="margin-left:3px" csrf_user="{{$value['uid']}}" >{{$value['username']}}</a>
							@endforeach
						@endif
						</div>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="col-md-offset-2 col-md-9">
					<input type="hidden" value="{{$info['id'] or ''}}" name="classifyId" />
					<input type="hidden" value="{{ csrf_token() }}" name="_token" />
					<button class="btn btn-info" type="submit">
						<i class="icon-ok bigger-110"></i> 保存
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

	$(".duty_person").on('click', '.ux', function(){
		$(this).remove();
	})
	$(".duty").on('keypress', function(event){
		if(event.keyCode ==13){
			var obj = $(this);
			var dutyname = obj.val();
			var _token = $("input[name='_token']").val();
			if(!dutyname){
				layer.msg('负责人不能为空', {icon: 2});
				return false;
			}
			$.ajax({
	            cache: false,
	            type: "POST",
	            url:"{{route('user.check')}}",
	            data: {'dutyname':dutyname},
	            headers: {
	                'X-CSRF-TOKEN': _token
	            },
	            dataType: 'json',
	            success: function(res) {
	            	if(res.status==200){ 
	            		var element = '<a class="btn btn-white btn-sm btn-primary ux" style="margin-left:3px"';
	            		element += 'csrf_user="'+res.csrf_user+'" >'+dutyname+'</a>';
	        			$('.duty_person').append(element);
	        			obj.val(''); 
	                }else{
	                	layer.msg(res.message, {icon: 2});
	                }
	            },
	            error: function(request) {
	                layer.msg("网络错误，请稍后重试");
	            },
	        });
			return false;
		}
	});
	var validator = $("#myForm").validate({
		submitHandler: function(form) {	
			var classify = $("input[name='classify']").val();
			var classifyId = $("input[name='classifyId']").val();
			var description  = $('textarea[name="description"]').val();
			var _token = $("input[name='_token']").val();
			csrf_user = '';
			$('.ux').each(function(){
				csrf_user += ","+$(this).attr('csrf_user');
			})
			if(csrf_user==''){
				$(".classDuty").html("输入用户名后请回车");
				return false;
			}
			$.ajax({
                cache: false,
                type: "POST",
                url:"{{route('Category.store')}}",
                data:{
                    'classify':classify,
                    'pid':0,
                    'classifyId':classifyId,
                    'description':description,
                    'csrf_user':csrf_user,
                },
                headers: {
                    'X-CSRF-TOKEN': _token
                },
                dataType: 'json',
                success: function(res) {
                	if(res.status==200){
                		layer.msg(res.message, {icon: 1});
                		setTimeout(function(){
							 window.location.href = "{{route('Category.index')}}";;
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
			classify:{
				required:true,
				minlength:2,
			},
			description:{
				required:true,
				minlength:5,
			}
		},
		messages:{
			classify:{
				required:"分类名称不能为空",
				minlength:'至少为2个字符',
			},
			description:{
				required:"分类描述不能为空",
				minlength:'至少为5个字符',
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
