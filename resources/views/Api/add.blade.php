@extends('base') @section('page-content')
<div class="page-content">
	<!-- #section:settings.box -->
	<div class="alert alert-block alert-success">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>
		<!--i class="ace-icon fa fa-check green"></i--> 
		不论当前Api环境是什么，添加Api时，统一保存在 {{$sys['ApiEnv'][0]['envname']}} 下
	</div>
	@include('public/setbox')
	<div class="row">
		<div class="col-xs-11 col-xs-offset-1">
			<!-- PAGE CONTENT BEGINS -->
			<form class="form-horizontal" action="#"
				method="post" id="myForm">
				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-1"> 资源名称 </label>
					<div class="col-sm-9">
						<input type="text" name="apiname" class="rcol-xs-8 col-sm-8" placeholder="接口名称"
							value="{{$data['apiname'] or ''}}" @if(!empty($data['lid']) && !empty($data['version_type']) && $data['version_type']=='add') readonly @endif> <span class="help-inline col-xs-12 col-sm-7"> <span
							class="middle"></span>
						</span>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-10"> 接口分类 </label>
					<div class="col-sm-9 selectList">
						<select name="classify" class="rcol-xs-4 col-sm-4 classify">
							<option value="0">请选择</option>
						</select> <select name="subClassify"
							class="rcol-xs-4 col-sm-4 subClassify">
							<option value="0">请选择</option>
						</select>
						<span class="help-inline col-xs-12 col-sm-7"> <span class="middle"></span>
						</span>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-2"> 接口版本 </label>
					<div class="col-sm-9">
						<input type="text" name="version" id="version" placeholder="v1、v2……"
							class="rcol-xs-8 col-sm-8" value="{{$data['detail']['version'] or ''}}"> <span
							class="help-inline col-xs-12 col-sm-7"> <span class="middle"></span>
						</span>
					</div>
				</div>
				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-1"> gateway地址 </label>
					<div class="col-sm-9">
						<input type="text" name="gateway" class="rcol-xs-8 col-sm-8" placeholder="示例：http://api.smaty.net/Api/v1/info"
							value="{{$data['detail']['gateway'] or ''}}"> <span class="help-inline col-xs-12 col-sm-7"> <span
							class="middle"></span>
						</span>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-1"> 本地接口地址 </label>
					<div class="col-sm-9">
						<input type="text" name="local" class="rcol-xs-8 col-sm-8" placeholder="示例：http://api.smaty.net/Api/v1/info"
							value="{{$data['detail']['local'] or ''}}"> <span class="help-inline col-xs-12 col-sm-7"> <span
							class="middle"></span>
						</span>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-2"> 接口描述 </label>
					<div class="col-sm-9">
						<textarea name="description" id="description"
							class="col-xs-8 col-sm-8" rows="5">{{$data['detail']['description'] or ''}}</textarea>
						<span class="help-inline col-xs-12 col-sm-7"> <span class="middle"></span>
						</span>
					</div>
				</div>
				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-4"> 网络权限 </label>
					<div class="col-sm-9">
						<label class="col-xs-2" style="width: 160px;"> 
							<input name="network[]" class="ace ace-checkbox-2 children"
							type="radio" value="1"  @if(!empty($data['detail']) && $data['detail']['network']==1) checked @endif > <div class="lbl"> 内网</div>
						</label> 
						<label class="col-xs-2" style="width: 160px;"> 
						<input name="network[]" class="ace ace-checkbox-2 children"
							type="radio" value="2" @if(empty($data['detail']) || (!empty($data['detail']) && $data['detail']['network']==2)) checked @endif > <div class="lbl"> 外网</div>
						</label>
						<span class="help-inline col-xs-12 col-sm-7"> <span class="middle"></span>
						</span>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-4"> 请求方式 </label>
					<div class="col-sm-9">
						<label class="col-xs-2" style="width: 120px;"> 
							<input name="request_type[]" class="ace ace-checkbox-2 children request_type"
							type="checkbox" value="1" method="get" @if(!empty($data['param']) && in_array(1,$data['param']['type'])) checked @endif> <div class="lbl"> GET</div>
						</label> 
						<label class="col-xs-2" style="width: 120px;"> 
							<input name="request_type[]" class="ace ace-checkbox-2 children request_type"
							type="checkbox" value="2" method="post" @if(!empty($data['param']) && in_array(2,$data['param']['type'])) checked @endif> <div class="lbl"> POST</div>
						</label> 
						<label class="col-xs-2" style="width: 120px;"> 
							<input name="request_type[]" class="ace ace-checkbox-2 children request_type"
							type="checkbox" value="3" method="put" @if(!empty($data['param']) && in_array(3,$data['param']['type'])) checked @endif> <div class="lbl"> PUT</div>
						</label> 
						<label class="col-xs-2" style="width: 120px;"> 
							<input name="request_type[]" class="ace ace-checkbox-2 children request_type"
							type="checkbox" value="4" method="del" @if(!empty($data['param']) && in_array(4,$data['param']['type'])) checked @endif> <div class="lbl"> DELETE</div>
						</label>
						<span class="help-inline col-xs-12 col-sm-7"> 
							<span class="middle reqtype"></span>
						</span>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-4"> 请求及响应 </label>
					<div class="col-sm-9">
						<div class="tabbable">
							<ul class="nav nav-tabs" id="myTab">
								<li class="active"><a data-toggle="tab" href="#header"
									aria-expanded="true">Header头 </a></li>

								<li class="" id="tab-get"  style="display:none;"><a data-toggle="tab" href="#tab-content-get"
									aria-expanded="false"> GET请求 </a></li>

								<li class="" id="tab-post" style="display:none;"><a data-toggle="tab" href="#tab-content-post"
									aria-expanded="false"> POST请求 </a></li>
									
								<li class="" id="tab-put" style="display:none;"><a data-toggle="tab" href="#tab-content-put"
									aria-expanded="false"> PUT请求 </a></li>
									
								<li class="" id="tab-del" style="display:none;"><a data-toggle="tab" href="#tab-content-del"
									aria-expanded="false"> DELETE请求 </a></li>


							</ul>

							<div class="tab-content">
							
								<div id="header" class="tab-pane fade active in">
									@each('Api.param', $data['param']['HEADER'], 'param')
								</div>

								<div id="tab-content-get" class="tab-pane fade">
									@each('Api.param', $data['param']['GET'], 'param')
								</div>
								
								<div id="tab-content-post" class="tab-pane fade">
									@each('Api.param', $data['param']['POST'], 'param')
								</div>
								
								<div id="tab-content-put" class="tab-pane fade">
									@each('Api.param', $data['param']['PUT'], 'param')
								</div>
								
								<div id="tab-content-del" class="tab-pane fade">
									@each('Api.param', $data['param']['DELETE'], 'param')
								</div>
								
							</div>
						</div>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group clearfix">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-2"> 返回示例 </label>
					<div class="col-sm-9">
						<textarea name="goback" id="goback" class="col-xs-8 col-sm-8"
							rows="5">{{$data['detail']['goback'] or ''}}</textarea>
						<span class="help-inline col-xs-12 col-sm-7"> <span class="middle"></span>
						</span>
					</div>
				</div>
				<div class="space-4"></div>

				<div class="form-group clearfix statusCode">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-10"> 状态码 </label>
					<div class="col-sm-9">
						<input type="hidden" value="{{ csrf_token() }}" name="_token" />
						<table class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th class="center">操作</th>
									<th>状态码</th>
									<th>描述</th>
								</tr>
							</thead>
							<tbody>
								@foreach($data['statuscode'] as $value)
								<tr>
									<td class="center"><a class="red delNode" href="javascript:void(0);"> <i
											class="ace-icon fa fa-trash-o bigger-130"></i>
									</a></td>
									<td><input type="text" name="scode[status][]"
										class="rcol-xs-12 col-sm-12" value="{{$value['status'] or ''}}"></td>
									<td><input type="text" name="scode[des][]"
										class="rcol-xs-12 col-sm-12" value="{{$value['des'] or ''}}"></td>
								</tr>
								@endforeach
							</tbody>
						</table>
						<span class="btn btn-minier btn-info add-button" type="statusCode">
							<i class="glyphicon-plus fa "></i> 增加
						</span>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="col-md-offset-2 col-md-9">
					<input type="hidden" value="{{ csrf_token() }}" name="_token" />
					<input type="hidden" value="{{$data['detail']['id'] or ''}}" name="did" />
					<input type="hidden" value="{{$data['detail']['listid'] or ''}}" name="lid" />
					<input type="hidden" value="{{$data['version_type'] or ''}}" name="version_type" />
					<input type="hidden" value="{{$data['lid'] or ''}}" name="version_lid" />
					<button class="btn btn-info btn-info-submit" type="submit">
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
	
	//分类多级联动
    $(".selectList").each(function(){
        var areaJson;
        var temp_html;
        var oclassify = $(this).find(".classify");
        var osubClassify = $(this).find(".subClassify");
        //初始化一级分类
        var classifyId =  {{ $data['currentClassify']['classifyId'] or '1' }};
        var subClassifyId =  {{ $data['currentClassify']['subClassifyId'] or '1' }};
        var classify = function(){
        	temp_html = '';
            $.each(areaJson,function(i,classify){
                if(classifyId==classify.id){
                	temp_html+="<option value='"+classify.id+"' selected>"+classify.name+"</option>";
                }else{
                	temp_html+="<option value='"+classify.id+"'>"+classify.name+"</option>";
                }
                
            });
            oclassify.html(temp_html);
            subClassify();
        };
        //赋值子分类
        var subClassify = function(){
            temp_html = ""; 
            var n = oclassify.get(0).selectedIndex;
            if(!areaJson[n].child){
            	layer.alert('该分类下无子分类，<br/>请在分类信息中添加子分类');
            }else{
            	$.each(areaJson[n].child, function(i,subClassify){
                    if(subClassifyId==subClassify.id){
                    	temp_html+="<option value='"+subClassify.id+"' selected>"+subClassify.ct+"</option>";
                    }else{
                    	temp_html+="<option value='"+subClassify.id+"'>"+subClassify.ct+"</option>";
                    }
                });
            }
            osubClassify.html(temp_html);
        };
        //选择分类改变子分类
        oclassify.change(function(){
            subClassify();
        });
        //获取json数据
            areaJson = {!! $data['classify'] or '' !!};
            classify();
    });
    //选择请求方式
    var method = '';
    var checked = false;
    $('.request_type').each(function(){
    	method = $(this).attr('method');
		checked = $(this).is(':checked');
		if(checked){
			$("#tab-"+method).show();
		} 
    });
	$('.request_type').click(function(){
		method = $(this).attr('method');
		checked = $(this).is(':checked');
		//获取当前激活节点
		$(".nav-tabs li").removeClass('active');
		$(".tab-pane").removeClass('active in');
		if(checked){
			$("#tab-"+method).show().addClass('active');
			$("#tab-content-"+method).addClass('active in');
		}else{
			$("#tab-"+method).hide();
			//获取当前激活的节点的上一节点
			$(".nav-tabs li:first").addClass('active');
			$(".tab-pane:first").addClass('active in');
		}
	});
    //参数和状态码添加
    $(".form-group").on("click", ".add-button", function(){
		var type = $(this).attr("type");
		var method = $(this).attr("method");
		var element = '';
		if(type=='request' || type=='response'){
			element += '<tr>';
			element += '<td class="center"><a class="red delNode" href="javascript:void(0);" > ';
			element += '	<i class="ace-icon fa fa-trash-o bigger-130"></i>';
			element += '</a></td>';
			element += '<td><input type="text" name=' + 'param['+method+']['+type+'][field][]';
			element += '	class="rcol-xs-12 col-sm-12" value=""></td>';
			element += '<td><select class="rcol-xs-12 col-sm-12 valid" name=' + 'param['+method+']['+type+'][must][]';
			element += '	aria-invalid="false">';
			element += '		<option value="0" selected="selected">是</option>';
			element += '		<option value="1">否</option>';
			element += '</select></td>';
			element += '<td><input type="text" name=' + 'param['+method+']['+type+'][des][]';
			element += '	class="rcol-xs-12 col-sm-12" value=""></td>';
			element += '<td><input type="text" name=' + 'param['+method+']['+type+'][default][]';
			element += '	class="rcol-xs-12 col-sm-12" value=""></td>';
			element += '	</tr>';
		}else if(type=='statusCode'){
			element += '<tr>';
			element += '	<td class="center"><a class="red delNode" href="javascript:void(0);"> ';
			element += '			<i class="ace-icon fa fa-trash-o bigger-130"></i>';
			element += '	</a></td>';
			element += '	<td><input type="text" name="scode[status][]"';
			element += '		class="rcol-xs-12 col-sm-12" value=""></td>';
			element += '	<td><input type="text" name="scode[des][]"';
			element += '		class="rcol-xs-12 col-sm-12" value=""></td>';
			element += '	</tr>';
		}
		$(this).parents("."+type).find("tbody").append(element);
    });
    //删除节点
    $(".form-group").on("click", ".delNode", function(){
		$(this).parents("tr").remove();
    });
    //表单验证
	jQuery.validator.addMethod("UrlCheck", function(value, element) {       
		return this.optional(element) || /^((https|http)?:\/\/)+[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/.test(value);       
	}, "请输入正确的接口地址");
	var validator = $("#myForm").validate({
		submitHandler: function(form) {
			$(".btn-info-submit").attr('disabled',true);
			$.ajax({
                cache: false,
                type: "POST",
                url:"{{route('Api.store')}}",
                data:$('#myForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': $("input[name='_token']").val()
                },
                dataType: 'json',
                success: function(res) {
                	$(".btn-info-submit").attr('disabled',false);
                	if(res.status==200){
                		layer.msg(res.message, {icon: 1})
                		setTimeout(function(){
							 window.location.href="{{route('Api.list')}}";
						 }, 2000);
                	}else{
                		layer.msg(res.message, {icon: 2});
                	}
                },
                error: function(request) {
                    layer.msg("网络错误，请稍后重试");
                    $(".btn-info-submit").attr('disabled',false);
                },
            });
		},
		rules:{
			apiname:{
				required:true,
				maxlength:20,
				minlength:2
			},
			version:{
				required:true,
				maxlength:4,
				minlength:2
			},
			gateway:{
				required:true,
				UrlCheck:true,
			},
			local:{
				required:true,
				UrlCheck:true,
			},
			subClassify:{
				required:true,
			},
			description:{
				required:true,
			},
			'request_type[]':{
				required:true,
			}
		},
		messages:{
			apiname :{
				required:"资源名不能为空",
				maxlength:"不能超过20个字符",
				minlength:"不能少于2个字符",
			},
			version :{
				required:"接口版本不能为空",
				maxlength:"不能超过4个字符",
				minlength:"不能少于2个字符",
			},
			gateway:{
				required:"接口地址不能为空",
				UrlCheck:"请输入正确的接口地址",
			},
			local:{
				required:"接口地址不能为空",
				UrlCheck:"请输入正确的接口地址",
			},
			subClassify:{
				required:"子分类信息不能为空",
			},
			description:{
				required:"接口描述不能为空",
			},
			'request_type[]':{
				required:"请求方式不能为空",
			}
	        
		},
		errorElement: 'custom',
		errorClass:'error',
		errorPlacement: function(error, custom) {
			if(custom.context.name=='request_type[]'){
				error.appendTo($(".reqtype")); 
			}else{
				error.appendTo(custom.next('span'))
			}
		},  
	})
})
		
</script>
@endsection
