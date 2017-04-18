@extends('base') @section('page-content')
<div class="page-content">
	<!-- #section:settings.box -->
	<div class="alert alert-block alert-success">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>
		<!--i class="ace-icon fa fa-check green"></i-->
		Api环境选择，从上至下分别为测试、线上环境。状态说明：ON 已开启 OFF 已关闭
	</div>
	@include('public/setbox')
	<div class="row">
		<div class="col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<form action="" class="form-horizontal" id="myForm" method="post">
			
				@foreach($data as $env)
    				<div class="space-4"></div>
    				<div class="form-group">
    					<label class="col-sm-1 control-label no-padding-right" for="form-field-2"> 开启环境 </label>
                            <div class="control-label no-padding-left col-sm-1">
                                <label>
                                	<input name="apienv[id][]"  type="hidden" value="{{$env['id']}}" />
                                    <input name="apienv[status][{{$env['id']}}]" 
                                    	@if ((!empty($env['status']) && $env['status'] == 1) || empty($env['status'])) checked="checked" @endif
                                        class="ace ace-switch ace-switch-1" type="checkbox" >
                                    <span class="lbl"></span>
                                </label>
                            </div>
    					<div class="col-sm-9">
    						<input type="text" name="apienv[name][]" class="col-xs-10 col-sm-5" value=" {{$env['envname']}}">
    						<span class="help-inline col-xs-12 col-sm-7"> </span>
    					</div>
    				</div>
				@endforeach
				 <div class="col-md-offset-2 col-md-9">
                    <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                    <button class="btn btn-info submit" type="submit">
                        <i class="icon-ok bigger-110"></i>
                        	提交
                    </button>
                    
                    &nbsp; &nbsp; &nbsp;
                    <button class="btn" type="reset">
                        <i class="icon-undo bigger-110"></i>
                        	重置
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
	var validator = $("#myForm").validate({
		submitHandler: function(form) {	
			$.ajax({
                cache: false,
                type: "POST",
                url:"{{route('env.store')}}",
                data:$('#myForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $("input[name='_token']").val()
                },
                dataType: 'json',
                success: function(res) {
                	if(res.status==200){
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
		errorElement: 'custom',
		errorClass:'error',
		errorPlacement: function(error, custom) {
			error.appendTo( custom.next('span') ); 
		},  
	})
})
		
</script>
@endsection
