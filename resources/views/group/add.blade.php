@extends('base') @section('page-content')
<div class="page-content">
	<!-- #section:settings.box -->
	<div class="alert alert-block alert-success">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>
		<!--i class="ace-icon fa fa-check green"></i-->
		操作节点权限选择
	</div>
	@include('public/setbox')
	<div class="row">
		<div class="col-xs-12">
                        <!-- PAGE CONTENT BEGINS -->
                        <form class="form-horizontal" action="#" method="post" id="myForm">
                            <div class="form-group">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-1"> 用户组名 </label>
                                <div class="col-sm-9">
                                    <input type="text" name="groupname"  placeholder="用户组名" class="col-xs-10 col-sm-5" value="{{$data['group']['groupname'] or ''}}">
                                    <input type="hidden" name="gid"  value="{{$data['group']['id'] or ''}}">
                                    <span class="help-inline col-xs-12 col-sm-7">
												<span class="middle"></span>
											</span>
                                </div>
                            </div>
                            <div class="space-4"></div>
                            <div class="form-group">
                                <label class="col-sm-1 control-label no-padding-right" for="form-field-2"> 是否启用 </label>
                                <div class="control-label no-padding-left col-sm-1">
                                    <label>
                                        <input name="status" id="status" class="ace ace-switch ace-switch-2" type="checkbox"
                                        @if ((!empty($data['group']['status']) && $data['group']['status'] == 1) || empty($data['group']['status'])) checked="checked" @endif >
                                        <span class="lbl"></span>
                                    </label>
                                </div>
                                <span class="help-inline col-xs-12 col-sm-7">
												<span class="middle">YES，启用；NO，禁用</span>
										</span>
                            </div>
                                                         <div class="space-4"></div>
                            <div class="form-group">
                                    <label class="col-sm-1 control-label no-padding-right" for="form-field-1">
                                        权限选择 </label>
                                    <div class="col-sm-9">
                                        <div class="col-sm-10">
                                        
                             @foreach($data['node'] as $node)          
                             <div class="row">
								<div class="widget-box">
									<div class="widget-header">
										<h4 class="widget-title">
											<label> <input name="rules[]"
												class="ace ace-checkbox-2 father" type="checkbox" value="{{$node['id']}}"
												@if(!empty($node['id']) && !empty($data['group']['rules']) &&  in_array($node['id'], explode(',', $data['group']['rules'])))
													checked = 'checked'
												@endif
												>
												<span class="lbl"> {{$node['title'] or ''}}</span>
											</label>
										</h4>
										<div class="widget-toolbar">
											@if(!empty($node['child']))
											<a href="#" data-action="collapse">
                                                <i class="ace-icon fa fa-chevron-up"></i>
                                            </a> 
											@endif
										</div>
									</div>
									@if(!empty($node['child']))
									<div class="widget-body">
                                          <div class="widget-main row">
                                          		@foreach($node['child'] as $sub)
                                                    <label class="col-xs-2" style="width:160px;">
                                                            <input name="rules[]" class="ace ace-checkbox-2 children" type="checkbox" value="{{$sub['id']}}"
                                                            @if(!empty($sub['id']) && !empty($data['group']['rules']) &&  in_array($sub['id'], explode(',', $data['group']['rules'])))
																checked = 'checked'
															@endif
                                                            >
                                                            <span class="lbl"> {{$sub['title'] or ''}}</span>
                                                	</label>
                                                @endforeach
                                           </div>
                                     </div>
                                     @endif
								</div>
							</div>           
                            @endforeach
                            
                            <div class="col-md-offset-2 col-md-9 mgt20">
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

	$(".father").click(function(){

		var obj = $(this);
		var checked = obj.is(':checked');
		var children = obj.parents('.widget-box').find('.children');
		children.each(function(){
			$(this).prop("checked", checked);
		})
	})
	$(".children").click(function(){
		
		var obj = $(this);
		var flag = false;
		var children = obj.parents('.widget-main').find('.children');
		children.each(function(){
			subchecked = $(this).is(':checked');
			if(subchecked) flag = true;
		})
		var father = obj.parents('.widget-box').find('.father');
		father.prop("checked", flag);
		
	})
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
                		layer.msg(res.message, {icon: 1});
                		setTimeout(function(){
							 window.location.href="{{route('group.index')}}";
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
			groupname:{
				required:true,
				minlength:2,
			}
		},
		messages:{
			groupname:{
				required:"用户组名称，不能为空",
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
