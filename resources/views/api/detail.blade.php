@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/chosen.css')}}">
<link rel="stylesheet" href="{{URL::asset('js/jsonview/jquery.jsonview.min.css')}}" />
<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
		<!-- main -->
		<div class="col">
			<!-- content -->
			<div class="wrapper-md ng-scope">
				<div class="panel panel-default">
					<div class="panel-heading font-bold">
						<a href="javascript:void(0);">Api 详情</a>
                		<a href="/Debug?did={{$data['detail']['id']}}" class="text-primary m-l-xl pull-right" target="_blank">调试</a>
					</div>
					<div class="panel-body">
						
						<form id="myForm" method="post"
							class="form-horizontal ng-pristine ng-valid ng-valid-date ng-valid-required ng-valid-parse ng-valid-date-disabled">
							<div class="m-l font-bold">
                              <span class="col-lg-2 control-label">
                              		<a href="{{route('Api.info')}}?did={{$data['detail']['id']}}" class="btn m-b-xs w-xs btn-primary">修 改</a> 
							  </span>
                              <span class="col-lg-6 form-control-static">
                              		@if($data['detail']['status'] == 1) 
            							<a href="javascript:void(0);" did={{$data['detail']['id']}} class="btn m-b-xs w-xs btn-success operate" type="2"> 发布 </a>
                					@elseif($data['detail']['status'] == 2)
                						<a href="javascript:void(0);"  class="btn m-b-xs w-xs btn-info"> 审核中 </a>
                					@elseif($data['detail']['status'] == 3)
                						<a href="javascript:void(0);"  class="m-l-xl btn m-b-xs w-xs btn-danger"> 已废弃 </a>
                				 	@endif
                				 	<a href="javascript:void(0);" did={{$data['detail']['id']}} class="m-l-lg btn m-b-xs w-xs btn-dark operate " type="1">删除</a>
                              </span>
                            </div>
            				<div class="line line-dashed b-b pull-in"></div>
							<div class="m-l">
                              <span class="col-lg-2 control-label">资源名称：</span>
                              <span class="col-lg-6 form-control-static">{{$data['apiname'] or ''}}</span>
                            </div>
							<div class="line line-dashed b-b pull-in"></div>
							
							@if(!empty($data['audit']))
							<div class="m-l">
                              <span class="col-lg-2 control-label ">审核信息：</span>
                              <div class="col-lg-6 form-control-static">
                              		<label>审核人:{{$data['audit']['auditor']}} &nbsp;&nbsp;状态: @if($data['audit']['status']==1) 已审核 @elseif($data['audit']['status']==2) 已拒绝 @endif</label>
                              		@if($data['audit']['status']==2) <pre> 备注： {{$data['audit']['remark']}} </pre>@endif
                              </div>
                            </div>
							<div class="line line-dashed b-b pull-in"></div>
							@endif
							
							<div class="m-l">
                              <span class="col-lg-2 control-label">维护人：</span>
                              <span class="col-lg-6 form-control-static">
                              		<label>{{$data['editor']['username'] or ''}} &nbsp;&nbsp;最近改动时间: {{$data['editor']['mtime'] or ''}}</label>
                              </span>
                            </div>
							<div class="line line-dashed b-b pull-in"></div>
							
							<div class="m-l">
                              <span class="col-lg-2 control-label">Api 分类：</span>
                              <span class="col-lg-6 form-control-static">
                              		@if(!empty($data['currentClassify']))
                						<span>{{$data['currentClassify']['classifyName'] or ''}} >> {{$data['currentClassify']['subClassifyName'] or ''}}</span>
                					@endif
                              </span>
                            </div>
							<div class="line line-dashed b-b pull-in"></div>
							
							<div class="m-l">
                              <span class="col-lg-2 control-label">Api 版本：</span>
                              <span class="col-lg-6 form-control-static">
                              		<label>{{$data['detail']['version'] or ''}}</label>
                              </span>
                            </div>
							<div class="line line-dashed b-b pull-in"></div>
							
							<div class="m-l">
                              <span class="col-lg-2 control-label">gateway 地址：</span>
                              <span class="col-lg-6 form-control-static">
                              		<label class="gatewayApi">{{$sys['Project']['env']['domain']}}{{$data['detail']['gateway'] or ''}}</label>
                              </span>
                            </div>
							<div class="line line-dashed b-b pull-in"></div>
							
							<div class="m-l">
                              <span class="col-lg-2 control-label">local 地址：</span>
                              <span class="col-lg-6 form-control-static">
                              		<label>{{$data['detail']['local'] or ''}}</label>
                              </span>
                            </div>
							
							<div class="line line-dashed b-b pull-in"></div>
							<div class="m-l">
                              <span class="col-lg-2 control-label">mock 地址：</span>
                              <span class="col-lg-6 form-control-static">
                              		<label>{{$data['detail']['mockUrl'] or ''}}</label>
									<label class="m-l-md">
										<i class="icon-question mockdes"></i>
									</label>
									<label class="m-l-md">
										<a href="{{$data['detail']['mockUrl'] or ''}}" target="_blank"  class="text-primary m-b-sm m-t-sm block">前往</a>
									</label>
                              </span>
                            </div>

							<div class="line line-dashed b-b pull-in"></div>
							
							<div class="m-l">
                              <span class="col-lg-2 control-label">Api 描述：</span>
                              <span class="col-lg-6 form-control-static">
                              		<pre>{{$data['detail']['description'] or ''}}</pre>
                              </span>
                            </div>
							<div class="line line-dashed b-b pull-in"></div>
							
							<div class="m-l">
                              <span class="col-lg-2 control-label">请求方式：</span>
                              <span class="col-lg-6 form-control-static">
                              		@if(!empty($data['detail']))
        								<label> 
        									@if($data['detail']['type']==1) GET @endif
        									@if($data['detail']['type']==2) POST @endif
        									@if($data['detail']['type']==3) PUT @endif
        									@if($data['detail']['type']==4) DELETE @endif
        								</label> 
            						@endif
                              </span>
                            </div>
							<div class="line line-dashed b-b pull-in"></div>
							
							<div class="m-l">
                              <span class="col-lg-2 control-label">网络权限：</span>
                              <span class="col-lg-6 form-control-static">
                              		@if(!empty($data['detail']))
            							@if($data['detail']['network']==1)
                							<label>内网</label> 
            							@endif
            							@if($data['detail']['network']==2)
                							<label>外网</label> 
            							@endif
            						@endif
                              </span>
                            </div>
							<div class="line line-dashed b-b pull-in"></div>
							
							@if($data['detail']['isheader'] && !empty($data['detail']['header']))
							<div class="m-l">
								<span class="col-lg-2 control-label">Header 头信息：</span>
								<div class="col-sm-8">
									<div class="panel panel-default m-b-none p-header">
										<table class="table table-striped m-b-none">
											<thead>
												<tr>
													<th class="text-center">字段名</th>
													<th>类型</th>
													<th>必填</th>
													<th>描述</th>
												</tr>
											</thead>
											<tbody>
                                        		@foreach($data['detail']['header'] as $value)
    												<tr>
    													<td class="text-center">{{$value['field'] or ''}}</td>
    													<td>
    													@if(!empty($value['fieldType']))
    														@if($value['fieldType']==1) string @endif
    														@if($value['fieldType']==2) number @endif
    														@if($value['fieldType']==3) object @endif
    														@if($value['fieldType']==4) array @endif
    														@if($value['fieldType']==5) file @endif
    														@if($value['fieldType']==6) bool @endif
    													@else
        														string
    													@endif
    													</td>
    													<td>
    														@if($value['must']==1) 是 @endif
    														@if($value['must']==2) 否 @endif
    													</td>
    													<td>{{$value['des'] or ''}}</td>
    												</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>
							@endif
							
							<div class="m-l">
								<label class="col-lg-2 control-label">请求及响应：</label>
								<div class="col-sm-8">
									<div class="panel panel-default m-b-none p-request">
										<div class="panel-heading">请求参数</div>
										<table class="table table-striped m-b-none">
											<thead>
												<tr>
													<th class="text-center">字段名</th>
													<th>类型</th>
													<th>必填</th>
													<th>描述</th>
												</tr>
											</thead>
											<tbody>
												@foreach($data['detail']['request'] as $value)
													<tr>
    													<td class="text-center">{{$value['field'] or ''}}</td>
    													<td>
    														@if(!empty($value['fieldType']))
        														@if($value['fieldType']==1) string @endif
        														@if($value['fieldType']==2) number @endif
        														@if($value['fieldType']==3) object @endif
        														@if($value['fieldType']==4) array @endif
        														@if($value['fieldType']==5) file @endif
        														@if($value['fieldType']==6) bool @endif
        													@else
        														string
    														@endif
    													</td>
    													<td>
    														@if($value['must']==1) 是 @endif
    														@if($value['must']==2) 否 @endif
    													</td>
    													<td>{{$value['des'] or ''}}</td>
    												</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>
							<div class="m-l">
								<label class="col-lg-2 control-label">&nbsp;&nbsp;</label>
								<div class="col-sm-8">
									<div class="panel panel-default m-b-none p-response">
										<div class="panel-heading">响应参数</div>
										<table class="table table-striped m-b-none">
											<thead>
												<tr>
													<th class="text-center">字段名</th>
													<th>类型</th>
													<th>必填</th>
													<th>描述</th>
												</tr>
											</thead>
											<tbody>
												@foreach($data['detail']['response'] as $value)
													<tr>
    													<td class="text-center">{{$value['field'] or ''}}</td>
    													<td>
    														@if(!empty($value['fieldType']))
        														@if($value['fieldType']==1) string @endif
        														@if($value['fieldType']==2) number @endif
        														@if($value['fieldType']==3) object @endif
        														@if($value['fieldType']==4) array @endif
        														@if($value['fieldType']==5) file @endif
        														@if($value['fieldType']==6) bool @endif
    														@else
        														string
    														@endif
    													</td>
    													<td>
    														@if($value['must']==1) 是 @endif
    														@if($value['must']==2) 否 @endif
    													</td>
    													<td>{{$value['des'] or ''}}</td>
    												</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>
							
							<div class="m-l">
                              <span class="col-lg-2 control-label">响应数据类型：</span>
                              <span class="col-lg-6 form-control-static">
                              		@if(!empty($data['detail']))
            							@if($data['detail']['response_type']==1)
                							<label>JSON</label> 
            							@elseif($data['detail']['response_type']==2)
                							<label>XML</label> 
            							@elseif($data['detail']['response_type']==3)
                							<label>JSONP</label> 
            							@elseif($data['detail']['response_type']==4)
                							<label>HTML</label> 
            							@endif
            						@endif

            						<div class="m-t-sm" name="goback" id="goback-highlight" >
            							
									</div>
                              </span>
                            </div>
							<div class="line line-dashed b-b pull-in"></div>
							
							@if(!empty($data['detail']['statuscode']))
							<div class="m-l">
								<span class="col-lg-2 control-label">状态码：</span>
								<div class="col-sm-8">
									<div class="panel panel-default m-b-none p-header">
										<table class="table table-striped m-b-none">
											<tbody>
                                        		@foreach($data['detail']['statuscode'] as $value)
    												<tr>
    													<td class="text-center">{{$value['status'] or ''}}</td>
    													<td>{{$value['des'] or ''}}</td>
    												</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>
							@endif
						</form>
					</div>
				</div>
			</div>
			<!-- /content -->
			<script type="text/javascript" src="{{URL::asset('js/jsonview/jquery.jsonview.min.js')}}"></script>
			<script type="text/javascript" src="{{URL::asset('js/format/jsonp.js')}}"></script>
			<script type="text/javascript" src="{{URL::asset('js/format/xml.js')}}"></script>
			<script type="text/javascript" charset="utf-8">
				$(function(){
					var contentType = {{$data['detail']['response_type'] or ''}};
					var goback = '{!!$data["detail"]["example"]!!}';
					if(contentType==1 || contentType==3){
	            		try {
	                        var param=JSON.parse(goback);
	                        if(goback.indexOf('{')>-1){
	                        	$("#goback-highlight").JSONView(goback, { collapsed: false });
	                        }
	                    } catch(e) {
	                    	$("#goback-highlight").html("<pre>"+formatJsonp(goback)+"</pre>");
	                    }
		            }else if(contentType==2){
		            	$("#goback-highlight").html('<pre>'+htmlspecialchars(formatXml(goback))+'</pre>');
	            	}else if(contentType==4){
	            		var resultHtml =  htmlspecialchars(goback);
		            	$("#goback-highlight").html(goback);
			        }
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
				                'envid':"{{$sys['Project']['env']['id']}}",
				                'type':type
				            },
				            headers: {
				                'X-CSRF-TOKEN': $("input[name='_token']").val()
				            },
				            dataType: 'json',
				            success: function(res) {
				            	if(res.status){
				            		layer.msg(res.message)
				            		if(type==1){
				            			window.location.href='/Api/list';
				                	}
				            	}else{
				            		layer.msg(res.message);
				            	}
				            },
				            error: function(request) {
				                layer.msg("网络错误，请稍后重试");
				            },
				        });
					}
					//mock地址说明
					$(".mockdes").click(function(){
						layer.tips('添加或编辑Api时，选择响应数据类型和添加响应数据后，可直接通过该接口进行mock测试', '.mockdes', {
							tips: [1, '#3595CC'],
							time: 5000
						});
					})
				})
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
