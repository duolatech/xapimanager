@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/chosen.css')}}">

<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
		<!-- main -->
		<div class="col">
			<!-- content -->
			<div class="wrapper-md ng-scope">
				<div class="panel panel-default">
					<div class="panel-heading font-bold">Api 添加（不论当前Api环境是什么，添加Api时，统一保存在{{$sys['ApiEnv'][0]['envname']}}下）</div>
					<div class="panel-body">
						<form id="myForm" method="post"
							class="form-horizontal ng-pristine ng-valid ng-valid-date ng-valid-required ng-valid-parse ng-valid-date-disabled">
							<div class="form-group m-b-none">
								<label class="col-sm-2 control-label">资源名称</label>
								<div class="col-sm-6">
									<input name="apiname" type="text" class="form-control"
										value="{{$data['apiname'] or ''}}" placeholder="接口名称" @if(!empty($data['lid']) && !empty($data['version_type']) && $data['version_type']=='add') readonly @endif> <span
										class="help-block m-b-none" style="color: red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>
							<div class="form-group m-b-none">
								<label class="col-sm-2 control-label">项目选择</label>
								<div class="col-sm-6">
									<select name="project" class="form-control m-b-xs">
										@foreach($sys['Project']['info'] as $pro)
											<option value="{{$pro['id'] or ''}}" 
												@if($sys['Project']['proid']==$pro['id']) selected @endif
											>{{$pro['proname'] or ''}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>
							<div class="form-group m-b-none">
								<label class="col-sm-2 control-label">接口分类</label>
								<div class="col-sm-6">
									<div class="row selectList">
										<div class="col-sm-6">
											<select name="classify" class="form-control m-b-xs classify">
												<option value="0">请选择</option>
											</select>
										</div>
										<div class="col-sm-6">
											<select name="subClassify" class="form-control m-b-xs subClassify">
												<option value="0">请选择</option>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>
							<div class="form-group m-b-none">
								<label class="col-sm-2 control-label">接口版本</label>
								<div class="col-sm-6">
									<input name="version" type="text" class="form-control"
										value="{{$data['detail']['version'] or ''}}" placeholder="v1、v2……"> <span
										class="help-block m-b-none" style="color: red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>
							<div class="form-group m-b-none">
								<label class="col-sm-2 control-label">gateway api 地址</label>
								<div class="col-sm-6">
									<input name="gateway" type="text" class="form-control"
										value="{{$data['detail']['gateway'] or ''}}" placeholder="URL路径部分，示例：/Api/v1/info"> 
										<span class="help-block m-b-none" style="color: red;"></span>
										<label2 class="help-block m-b-none">备注：Url的前缀部分由当前环境的域名自动生成</label2>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>
							<div class="form-group m-b-none">
								<label class="col-sm-2 control-label">本地 api 地址</label>
								<div class="col-sm-6">
									<input name="local" type="text" class="form-control"
										value="{{$data['detail']['local'] or ''}}" placeholder="本地完整Url，含http/https"> <span
										class="help-block m-b-none" style="color: red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>
							<div class="form-group m-b-none">
								<label class="col-sm-2 control-label">接口描述</label>
								<div class="col-sm-6">
									<textarea name="description" class="form-control" rows="6"
										placeholder="">{{$data['detail']['description'] or ''}}</textarea>
									<span class="help-block m-b-none" style="color: red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>
							<div class="form-group m-b-none">
								<label class="col-sm-2 control-label">网络权限</label>
								<div class="col-sm-6">
									<label class="checkbox-inline i-checks"> <input type="radio" name="network"
										value="1"  @if(!empty($data['detail']['network']) && $data['detail']['network']==1) checked @endif ><i></i> 内网
									</label> <label class="checkbox-inline i-checks"> <input type="radio" name="network" 
										value="2"  @if(empty($data['detail']['network']) || (!empty($data['detail']['network']) && $data['detail']['network']==2)) checked @endif><i></i> 外网
									</label>
								</div>
							</div>

							<div class="line line-dashed b-b pull-in"></div>
							<div class="form-group m-b-none">
								<label class="col-sm-2 control-label">是否有header参数</label>
								<div class="col-sm-6">
									<label class="checkbox-inline i-checks"> <input type="radio" name="isheader" value="1" 
										@if(!empty($data['detail']['isheader']) && $data['detail']['isheader']==1) checked @endif><i></i> 是
									</label>
									<label class="checkbox-inline i-checks"> <input type="radio" name="isheader" value="2"
										@if(empty($data['detail']['isheader']) || (!empty($data['detail']['isheader']) && $data['detail']['isheader']==2)) checked @endif ><i></i> 否
									</label>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>

							<div class="form-group m-b-none">
								<label class="col-sm-2 control-label">请求方式</label>
								<div class="col-sm-6">
									<label class="checkbox-inline i-checks"> <input type="radio" name="request_type"
										value="1" checked><i></i> GET
									</label> <label class="checkbox-inline i-checks"> <input
										type="radio" name="request_type"  value="2"><i></i> POST
									</label> <label class="checkbox-inline i-checks"> <input
										type="radio" name="request_type" value="3"><i></i> PUT
									</label> <label class="checkbox-inline i-checks"> <input
										type="radio" name="request_type" value="4"><i></i> DELETE
									</label>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>
							<div class="header-info" @if(empty($data['detail']['isheader'])) style="display: none;" @endif>
							<div class="form-group m-b-none">
								<label class="col-sm-2 control-label">Header 头信息</label>
								<div class="col-sm-8">
									<div class="panel panel-default m-b-none p-header">
										<table class="table table-striped m-b-none">
											<thead>
												<tr>
													<th class="text-center">字段名</th>
													<th>类型</th>
													<th>必填</th>
													<th>描述</th>
													<th>操作</th>
												</tr>
											</thead>
											<tbody>
                                        		@foreach($data['detail']['header'] as $value)
    												<tr>
    													<td><input name="param[header][field][]" type="text" class="form-control"
    														value="{{$value['field'] or ''}}"></td>
    													<td><select name="param[header][fieldType][]" class="form-control">
    														@if(!empty($value['fieldType'])){
    															<option value="1" @if($value['fieldType']==1) selected="selected" @endif>string</option>
    															<option value="2" @if($value['fieldType']==2) selected="selected" @endif>number</option>
    															<option value="3" @if($value['fieldType']==3) selected="selected" @endif>object</option>
    															<option value="4" @if($value['fieldType']==4) selected="selected" @endif>array</option>
    															<option value="5" @if($value['fieldType']==5) selected="selected" @endif>file</option>
    															<option value="6" @if($value['fieldType']==6) selected="selected" @endif>bool</option>
    														@else
        														<option value="1" selected="selected">string</option>
    														@endif
    													</select></td>
    													<td class="text-success"><select name="param[header][must][]"
    														class="form-control ">
    															<option value="1" @if($value['must']==1) selected="selected" @endif>是</option>
    															<option value="2" @if($value['must']==2) selected="selected" @endif>否</option>
    													</select></td>
    													<td><input name="param[header][des][]" type="text" class="form-control"
    														value="{{$value['des'] or ''}}"></td>
    													<td class="text-center m-t-xs"><i
    														class="ace-icon fa fa-trash-o  bigger-240 grey delNode"></i></td>
    												</tr>
												@endforeach
												<tr>
													<td>
														<a class="btn btn-sm btn-primary add-button"  type="header" >
															<i class="fa fa-plus text"></i> <span class="text">添加</span>
														</a>
														<a class="btn btn-sm btn-default batch" xtype="header">
															<span class="text">批量参数</span>
														</a>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>
							</div>
							<div class="form-group m-t-none">
								<label class="col-sm-2 control-label">请求及响应</label>
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
													<th>操作</th>
												</tr>
											</thead>
											<tbody>
											@foreach($data['detail']['request'] as $value)
												<tr>
    													<td><input name="param[request][field][]" type="text" class="form-control"
    														value="{{$value['field'] or ''}}"></td>
    													<td><select name="param[request][fieldType][]" class="form-control">
    														@if(!empty($value['fieldType'])){
    															<option value="1" @if($value['fieldType']==1) selected="selected" @endif>string</option>
    															<option value="2" @if($value['fieldType']==2) selected="selected" @endif>number</option>
    															<option value="3" @if($value['fieldType']==3) selected="selected" @endif>object</option>
    															<option value="4" @if($value['fieldType']==4) selected="selected" @endif>array</option>
    															<option value="5" @if($value['fieldType']==5) selected="selected" @endif>file</option>
    															<option value="6" @if($value['fieldType']==6) selected="selected" @endif>bool</option>
    														@else
        														<option value="1" selected="selected">string</option>
    														@endif
    													</select></td>
    													<td class="text-success"><select name="param[request][must][]"
    														class="form-control ">
    															<option value="1" @if($value['must']==1) selected="selected" @endif>是</option>
    															<option value="2" @if($value['must']==2) selected="selected" @endif>否</option>
    													</select></td>
    													<td><input name="param[request][des][]" type="text" class="form-control"
    														value="{{$value['des'] or ''}}"></td>
    													<td class="text-center m-t-xs"><i
    														class="ace-icon fa fa-trash-o  bigger-240 grey delNode"></i></td>
    												</tr>
												@endforeach
												<tr>
													<td>
														<a class="btn btn-sm btn-primary add-button" type="request">
															<i class="fa fa-plus text"></i> <span class="text">添加</span>
														</a>
														<a class="btn btn-sm btn-default batch" xtype="request">
															<span class="text">批量参数</span>
														</a>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="form-group m-t-none">
								<label class="col-sm-2 control-label"> </label>
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
													<th>操作</th>
												</tr>
											</thead>
											<tbody>
												@foreach($data['detail']['response'] as $value)
													<tr>
    													<td><input name="param[response][field][]" type="text" class="form-control"
    														value="{{$value['field'] or ''}}"></td>
    													<td><select name="param[response][fieldType][]" class="form-control">
    															@if(!empty($value['fieldType'])){
        															<option value="1" @if($value['fieldType']==1) selected="selected" @endif>string</option>
        															<option value="2" @if($value['fieldType']==2) selected="selected" @endif>number</option>
        															<option value="3" @if($value['fieldType']==3) selected="selected" @endif>object</option>
        															<option value="4" @if($value['fieldType']==4) selected="selected" @endif>array</option>
        															<option value="5" @if($value['fieldType']==5) selected="selected" @endif>file</option>
        															<option value="6" @if($value['fieldType']==6) selected="selected" @endif>bool</option>
        														@else
            														<option value="1" selected="selected">string</option>
        														@endif
    													</select></td>
    													<td class="text-success"><select name="param[response][must][]"
    														class="form-control ">
    															<option value="1" @if($value['must']==1) selected="selected" @endif>是</option>
    															<option value="2" @if($value['must']==2) selected="selected" @endif>否</option>
    													</select></td>
    													<td><input name="param[response][des][]" type="text" class="form-control"
    														value="{{$value['des'] or ''}}"></td>
    													<td class="text-center m-t-xs"><i
    														class="ace-icon fa fa-trash-o  bigger-240 grey delNode"></i></td>
    												</tr>
												@endforeach
												<tr>
													<td>
														<a class="btn btn-sm btn-primary add-button"  type="response">
															<i class="fa fa-plus text"></i> <span class="text">添加</span>
														</a>
														<a class="btn btn-sm btn-default batch" xtype="response">
															<span class="text">批量参数</span>
														</a>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>
							<div class="form-group m-b-none">
								<label class="col-sm-2 control-label">响应数据类型</label>
								<div class="col-sm-8">
									<label class="checkbox-inline i-checks"> <input type="radio" name="response_type"
										value="1" @if(empty($data['detail']['response_type']) || (!empty($data['detail']['response_type']) && $data['detail']['response_type']==1)) checked @endif><i></i> JSON
									</label> <label class="checkbox-inline i-checks"> <input name="response_type"
										type="radio" value="2" @if(!empty($data['detail']['response_type']) && $data['detail']['response_type']==2) checked @endif><i></i> XML
									</label> <label class="checkbox-inline i-checks"> <input name="response_type"
										type="radio" value="3" @if(!empty($data['detail']['response_type']) && $data['detail']['response_type']==3) checked @endif><i></i> JSONP
									</label> <label class="checkbox-inline i-checks"> <input name="response_type"
										type="radio" value="4" @if(!empty($data['detail']['response_type']) && $data['detail']['response_type']==4) checked @endif><i></i> HTML
									</label>
									<div class="m-t-sm">
										<textarea name="goback" class="form-control" rows="6"
											placeholder="">{{$data['detail']['goback'] or ''}}</textarea>
									</div>
								</div>

							</div>
							<div class="line line-dashed b-b pull-in"></div>

							<div class="form-group m-b-none">
								<label class="col-sm-2 control-label">状态码</label>
								<div class="col-sm-8">
									<div class="panel panel-default statusCode">
										<table class="table table-striped m-b-none">
											<thead>
												<tr>
													<th class="text-center">状态码</th>
													<th>描述</th>
													<th>操作</th>
												</tr>
											</thead>
											<tbody>
												@foreach($data['detail']['statuscode'] as $value)
                    								<tr>
    													<td><input name="scode[status][]" type="text" class="form-control"
    														value="{{$value['status'] or ''}}"></td>
    													<td><input name="scode[des][]" type="text" class="form-control"
    														value="{{$value['des'] or ''}}"></td>
    													<td class="text-center m-t-xs"><i
    														class="ace-icon fa fa-trash-o  bigger-240 grey delNode"></i></td>
													</tr>
                    							@endforeach
												<tr>
													<td>
														<a class="btn btn-sm btn-primary add-button" type="statusCode">
															<i class="fa fa-plus text"></i> <span class="text">添加</span>
														</a>
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>
							<div class="line line-dashed b-b pull-in"></div>
							<div class="form-group m-b-none">
								<div class="col-sm-4 col-sm-offset-2">
									<input type="hidden" value="{{ csrf_token() }}" name="_token" />
                					<input type="hidden" value="{{$data['detail']['id'] or ''}}" name="did" />
                					<input type="hidden" value="{{$data['detail']['listid'] or ''}}" name="lid" />
                					<input type="hidden" value="{{$data['version_type'] or ''}}" name="version_type" />
                					<input type="hidden" value="{{$data['lid'] or ''}}" name="version_lid" />
									<button type="submit" class="btn btn-info btn-info-submit">保存Api</button>
								</div>
							</div>
							<div class="form-group" style="margin-top: 180px;"></div>

						</form>
					</div>
				</div>
				<div id="addBatch" style="display: none;"> 
        			<div class="wrapper-md ng-scope">
            			<div class="row batch-info" >
                    		<div class="btn-block">
                    			<p class="text-muted">支持两种方式导入：</p>
                    			<p class="text-muted">1.输入Raw参数，例如：id=123&name=ming&status=0</p>
                    			<p class="text-muted">2.输入JSON数据，例如：{"status":200,"message":"成功"}</p>
                    			<div class="form-group">
                    			  <label>批量参数</label>
                    			  <textarea class="form-control batchparam" rows="6" name="batchparam" ></textarea>
                    			</div>
                    			<div class="form-group">
                    			  <label> </label>
                    			  <span class="btn btn-primary btn-info-submit loadbatch">导入</span>
                    			</div>
                    		</div>
                    	</div>
                	</div>
        		</div>
			</div>
			<!-- /content -->
			<script type="text/javascript"
				src="{{URL::asset('js/validation/jquery.validate.js')}}"></script>
			<script type="text/javascript"
				src="{{URL::asset('js/validation/messages_zh.js')}}"></script>
			<script type="text/javascript" charset="utf-8">

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
    		    //是否有header信息
		        $('input[name="isheader"]').click(function(){
		        	var isheader = $(this).val();
		        	var header_info = $('.header-info');
		        	if(isheader==1){
		        		header_info.show();
                	}else{
                		header_info.hide();
                    }
    		    })
    		    //参数和状态码添加
				$(".form-group").on("click", ".add-button", function(){
					var type = $(this).attr("type");
					var element = nodePram(type);
					$(this).parents("tbody").find("tr:last").before(element);
				})
				//参数节点生成函数
				function nodePram(type, field, value){
		        	var element = '';
		        	if(!field || typeof(field)=='undifined') field = '';
		    		if(!value || typeof(value)=='undifined') value = '';
					if(type=='header' || type=='request' || type=='response'){
						element += '<tr>';
						element += '	<td><input name="param['+type+'][field][]" type="text" class="form-control"';
						element += '		value="'+field+'"></td>';
						element += '	<td><select name="param['+type+'][fieldType][]" class="form-control ">';
						element += '		<option value="1">string</option>';
						element += '		<option value="2">number</option>';
						element += '		<option value="3">object</option>';
						element += '		<option value="4">array</option>';
						element += '		<option value="5">file</option>';
						element += '		<option value="6">bool</option>';
						element += '	</select></td>';
						element += '	<td class="text-success"><select name="param['+type+'][must][]"';
						element += '		class="form-control ">';
						element += '			<option value="1">是</option>';
						element += '			<option value="2">否</option>';
						element += '		</select></td>';
						element += '	<td><input name="param['+type+'][des][]" type="text" class="form-control"';
						element += '		value="'+value+'"></td>';
						element += '	<td class="text-center m-t-xs"><i';
						element += '		class="ace-icon fa fa-trash-o  bigger-240 grey delNode"></i></td>';
						element += '</tr>';
					}else if(type=='statusCode'){
						element += '<tr>';
						element += '	<td><input name="scode[status][]" type="text" class="form-control"';
						element += '		value="{{$value['status'] or ''}}"></td>';
						element += '	<td><input name="scode[des][]" type="text" class="form-control"';
						element += '		value="{{$value['des'] or ''}}"></td>';
						element += '	<td class="text-center m-t-xs">';
						element += '		<i class="ace-icon fa fa-trash-o  bigger-240 grey delNode"></i>';
						element += '	</td>';
						element += '</tr>';
					}
					return element;
				}
				//添加、批量导入节点生成函数
		        function buttonNode(type){
		        	var tabTr = '<tr>';
                		tabTr += '	<td>';
                		tabTr += '		<a class="btn btn-sm btn-primary add-button"  type="response">';
                		tabTr += '			<i class="fa fa-plus text"></i> <span class="text">添加</span>';
                		tabTr += '		</a>';
                		tabTr += '		<a class="btn btn-sm btn-default batch" xtype="'+type+'">';
                		tabTr += '			<span class="text">批量参数</span>';
                		tabTr += '		</a>';
                		tabTr += '	</td>';
                		tabTr += '</tr>';
            		return tabTr;
			    }
				//删除节点
			    $(".form-group").on("click", ".delNode", function(){
					$(this).parents("tr").remove();
			    });
      		  	//弹出批量导入窗口
      		  	$(".form-group").on("click", ".batch", function(){
        				var content = $("#addBatch").html();
        				var obj = $(this);
        				var xtype = obj.attr('xtype');
        				layer.open({
        					  type: 1,
        					  title: '批量参数',
        					  skin: 'layui-layer-demo', //样式类名
        					  closeBtn: 1, //不显示关闭按钮
        					  anim: 2,
        					  area: ['420px','400px'],
        					  shadeClose: true, //开启遮罩关闭
        					  content: content
        					});
        				$(".loadbatch").attr('xtype', xtype);
          		})
    			//参数导入
            	$("body").on('click', '.loadbatch',function(){
            		var obj = $(this);
            		var xtype = obj.attr("xtype");
            		var string = obj.parents('.batch-info').find('.batchparam').val();
            		var tbody = $(".p-"+xtype).find("tbody");
            		string = $.trim(string);
            		try {
                        var jsonData = JSON.parse(string);
                        var theRequest = getJsonparse(jsonData, {});
                        if(string.indexOf('{')>-1){
                        	tbody.html('');
                        	$.each(theRequest,function(field,value){
                        		var element = '';
                        		if(xtype=='request' || xtype=='header' || xtype=="response"){
                        			element = nodePram(xtype, field, value);
                        		}
                        		tbody.append(element);
                            })
                            tbody.append(buttonNode(xtype));
                        }else{
                            return false;
                        }
                    } catch(e) {
                    	var theRequest = new Object(); 
                  	  	var strs = string.split('&');
                  		for(var i = 0; i < strs.length; i ++) { 
                  			var key = strs[i].split("=")[0];
                  			var val = strs[i].split("=")[1];
                  			key = key.replace(/\?/g, "");
                  			val = typeof(val)!='undefined' ? val : '';
                  			if(key){
                  				theRequest[key]=unescape(val); 
                  			}
                  		}
                  		tbody.html('');
                  		$.each(theRequest,function(field,value){
                    		var element = '';
                    		if(xtype=='request' || xtype=='header' || xtype=="response"){
                    			element = nodePram(xtype, field, value);
                    		}
                    		tbody.append(element);
                        })
                		tbody.append(buttonNode(xtype));
                    }
                    layer.closeAll();
            	})
            	//json数据解析
            	function getJsonparse(jsonData, obj){
              		var theRequest = new Object(); 
              		$.each(jsonData,function(field,value){
            			if(typeof(value)=='object'){
            				obj = getJsonparse(value, {});
            			}else{
            				theRequest[field]=unescape(value); 
            			}		
            	    });
            	    newOjb = $.extend(obj, theRequest);
            	    return newOjb;
               }
          		//表单验证
      			jQuery.validator.addMethod("UrlPathCheck", function(value, element) {       
      				return this.optional(element) || /^\/[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/.test(value);       
      			}, "请输入正确的gateway api地址");
      			jQuery.validator.addMethod("UrlCheck", function(value, element) {       
      				return this.optional(element) || /^((https|http)?:\/\/)+[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/.test(value);       
      			}, "请输入正确的本地Api地址");
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
      		                		layer.msg(res.message)
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
      						maxlength:12,
      						minlength:2
      					},
      					gateway:{
      						required:true,
      						UrlPathCheck:true,
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
      						maxlength:"不能超过12个字符",
      						minlength:"不能少于2个字符",
      					},
      					gateway:{
      						required:"gateway api地址不能为空",
      						UrlCheck:"请输入URL的路径部分，以'/'开头",
      					},
      					local:{
      						required:"本地Api地址不能为空",
      						UrlCheck:"本地完整Url，含http/https",
      					},
      					subClassify:{
      						required:"子分类信息不能为空",
      					},
      					description:{
      						required:"接口描述不能为空",
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
