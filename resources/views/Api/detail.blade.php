@extends('base') @section('page-content')
<div class="page-content">
	<link rel="stylesheet" href="{{URL::asset('js/jsonview/jquery.jsonview.min.css')}}" />
	<script type="text/javascript" src="{{URL::asset('js/jsonview/jquery.jsonview.min.js')}}"></script>
	<style>
        .form-group{
	       margin-bottom:7px;
        }
        .col-sm-2{
	       font-size:16px;
        }
        .col-sm-8{
	       font-size:14px;
        }
    </style>
	<!-- #section:settings.box -->
	@include('public/setbox')
	<div class="row">
		<div class="col-xs-11">
			<!-- PAGE CONTENT BEGINS -->
			<div class="col-xs-11 col-xs-offset- gnav ">
				<a href="{{route('Api.info')}}?did={{$data['detail']['id']}}">修改</a> 
				<a href="javascript:void(0);" did={{$data['detail']['id']}} class="operate" type="1">删除</a> 
					@if($data['detail']['status'] == 1) 
						<a href="javascript:void(0);" did={{$data['detail']['id']}} class="operate" type="2"> 发布 </a>
					@elseif($data['detail']['status'] == 2)
						<a href="javascript:void(0);"> 审核中 </a>
					@elseif($data['detail']['status'] == 3)
						<a href="javascript:void(0);"> 已废弃 </a>
				 	@endif
				<a href="/Api/debug?did={{$data['detail']['id']}}" class="pull-right" target="_blank">调试</a>
			</div>
			<form class="form-horizontal" action="#"
				method="post" id="myForm">
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right"
						for="form-field-1"> 资源名称: </label>
					<div class="col-sm-8 margin710">
						<span>{{$data['apiname'] or ''}}</span>
					</div>
				</div>
				
				@if(!empty($data['audit']))
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right"
						for="form-field-1"> 审核信息: </label>
					<div class="col-sm-8 margin710">
						<div>审核人:{{$data['audit']['auditor']}} 状态: @if($data['audit']['status']==1) 已审核 @elseif($data['audit']['status']==2) 已拒绝 @endif</div>
						<div>@if($data['audit']['status']==2) 备注： {{$data['audit']['remark']}} @endif</div>
					</div>
				</div>
				@endif
				
				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right"
						for="form-field-2"> 开发负责人: </label>
					<div class="col-sm-8 margin710">
						<div>负责人:{{$data['editor']['username'] or ''}}</div>
						<div>最近改动时间: {{$data['editor']['mtime'] or ''}}</div>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right"
						for="form-field-10"> 接口分类: </label>
					@if(!empty($data['currentClassify']))
						<div class="col-sm-8 margin710">
							<span>{{$data['currentClassify']['classifyName'] or ''}}>>{{$data['currentClassify']['subClassifyName'] or ''}}</span>
						</div>
					@endif
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right"
						for="form-field-2"> 接口版本: </label>
					<div class="col-sm-8 margin710">
						<span>{{$data['detail']['version'] or ''}}</span>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right"
						for="form-field-1"> gateway地址: </label>
					<div class="col-sm-8 margin710">
						<span>{{$data['detail']['gateway'] or ''}}</span>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right"
						for="form-field-1"> 本地接口地址: </label>
					<div class="col-sm-8 margin710">
						<span>{{$data['detail']['local'] or ''}}</span>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right"
						for="form-field-2"> 接口描述: </label>
					<div class="col-sm-8 margin710">
						<span>{{$data['detail']['description'] or ''}}</span>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right"
						for="form-field-4"> 网络权限: </label>
					<div class="col-sm-8 margin710">
						@if(!empty($data['detail']))
							@if($data['detail']['network']==1)
    							<label class="col-xs-2" style="width: 160px;">内网</label> 
							@endif
							@if($data['detail']['network']==2)
    							<label class="col-xs-2" style="width: 160px;">外网</label> 
							@endif
						@endif
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right"
						for="form-field-4"> 请求方式: </label>
					<div class="col-sm-8">
						@if(!empty($data['param']))
							@foreach($data['param']['type'] as $value)
								<label class="col-xs-2" style="width: 120px;"> 
									@if($value==1) GET @endif
									@if($value==2) POST @endif
									@if($value==3) PUT @endif
									@if($value==4) DELETE @endif
								</label> 
							@endforeach
						@endif
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label no-padding-right"
						for="form-field-4"> 请求及响应: </label>
					<div class="col-sm-8">
						<div class="tabbable">
							<ul class="nav nav-tabs" id="myTab">
								<li class="active"><a data-toggle="tab" href="#header"
									aria-expanded="true">Header头 </a></li>
								@if(!empty($data['param']))
        							@foreach($data['param']['type'] as $value)
        								<li class="" id="tab-{{$value}}"> 
        									<a data-toggle="tab" href="#tab-content-{{$value}}">
            									@if($value==1) GET请求 @endif
            									@if($value==2) POST请求 @endif
            									@if($value==3) PUT请求 @endif
            									@if($value==4) DELETE请求 @endif
        									</a>
        								</li>
        							@endforeach
        						@endif
							</ul>

							<div class="tab-content">
							
								<div id="header" class="tab-pane fade active in">
									@each('Api.detail_param', $data['param']['HEADER'], 'param')
								</div>

								<div id="tab-content-1" class="tab-pane fade">
									@each('Api.detail_param', $data['param']['GET'], 'param')
								</div>
								
								<div id="tab-content-2" class="tab-pane fade">
									@each('Api.detail_param', $data['param']['POST'], 'param')
								</div>
								
								<div id="tab-content-3" class="tab-pane fade">
									@each('Api.detail_param', $data['param']['PUT'], 'param')
								</div>
								
								<div id="tab-content-4" class="tab-pane fade">
									@each('Api.detail_param', $data['param']['DELETE'], 'param')
								</div>
								
							</div>
						</div>
					</div>
				</div>

				<div class="form-group clearfix">
					<label class="col-sm-2 control-label no-padding-right"
						for="form-field-2"> 返回示例 </label>
					<div class="col-sm-8  margin710">
						<span id="goback">{{$data['detail']['goback'] or ''}}</span>
					</div>
				</div>
				<div class="space-4"></div>

				<div class="form-group clearfix statusCode">
					<label class="col-sm-2 control-label no-padding-right"
						for="form-field-10"> 状态码 </label>
					<div class="col-sm-8 margin710">
						<input type="hidden" value="{{ csrf_token() }}" name="_token" />
						<table class="table table-striped table-bordered table-hover">
							<thead>
								<tr>
									<th>状态码</th>
									<th>描述</th>
								</tr>
							</thead>
							<tbody>
								@foreach($data['statuscode'] as $value)
								<tr>
									<td>{{$value['status'] or ''}}</td>
									<td>{{$value['des'] or ''}}</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>

				<div class="space-4"></div>

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

	//返回示例格式化
	try{
		$("#goback").JSONView($("#goback").html(), { collapsed: false });
	}catch(err){}

	//接口操作
	$(".operate").click(function(){
		var did = $(this).attr('did');
		var type = $(this).attr('type');
		if(type==1){
			$prompt = '您确认要删除该接口？';
		}else if(type==2){
			$prompt = '发布Api时，Api将同步到当前环境的上一级环境，<br/>Api同步的顺序依次是:<br/>{{$data["envinfo"]}}';
		}
		layer.confirm($prompt, {
			  btn: ['确定', '取消'] //按钮
			}, function(){
				operate(did, type);
			}, function(){
		})
	})
	//接口操作
	function operate(did, type){

		$.ajax({
            cache: false,
            type: "POST",
            url:"{{route('Api.operate')}}",
            data:{
                'did':did, 
                'envid':$(".current_env span").attr("env"),
                'type':type
            },
            headers: {
                'X-CSRF-TOKEN': $("input[name='_token']").val()
            },
            dataType: 'json',
            success: function(res) {
            	if(res.status){
            		layer.msg(res.message, {icon: 1})
            		if(type==1){
            			window.location.href='/Api/list';
                	}
            	}else{
            		layer.msg(res.message, {icon: 2});
            	}
            },
            error: function(request) {
                layer.msg("网络错误，请稍后重试");
            },
        });
	}
    
})
		
</script>
@endsection
