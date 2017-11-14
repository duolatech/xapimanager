@extends('basic') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/chosen.css')}}">
<link rel="stylesheet" href="{{URL::asset('js/jsonview/jquery.jsonview.min.css')}}" />
<div class="app-content-body fade-in-up ng-scope">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
		<!-- content -->
		<div class="wrapper-md ng-scope">
			<div class="row">
				<div class="col-sm-3">
					<div class="panel panel-default">
						<div class="panel-heading font-bold">
							服务器IP绑定
							@if(Session::get('uid'))
							<i class="icon icon-plus pull-right grey binding"></i>
							@endif
						</div>
						<div class="panel-body">
							@if(Session::get('uid'))
								@foreach($data['domain'] as $dom)
								<div class="form-group">
									<div>
										<label class="text-lg">{{$dom['domain'] or ''}}</label>
										<i class="icon icon-settings pull-right grey binding" domid="{{$dom['id'] or ''}}" domain="{{$dom['domain'] or ''}}"  ips="{{$dom['ips'] or ''}}" ></i>
									</div>
									@foreach($dom['iplong'] as $long)
										<div class="iplist" iplong="{{$long['iplong'] or ''}}">
											@if($long['status']==1)<i class="ace-icon fa fa-check grey "></i>@endif
											<label class="padder">{{ long2ip($long['iplong']) }}</label>
										</div>
									@endforeach
								</div>
								<div class="line line-dashed b-b line-lg pull-in"></div>
								@endforeach
							@else
								<a href="/Login/index" class="btn btn-sm btn-primary">登录后查看</a>
							@endif
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading font-bold">保存记录</div>
						<div class="panel-body store-record">
							@foreach($data['record'] as $value)
								<div class="form-group">
									<label ><a href="/Debug?sid={{$value['id'] or ''}}">【{{$value['type'] or ''}}】{{$value['path'] or ''}}</a></label>
									<i class="ace-icon fa fa-trash-o pull-right bigger-120 grey del-store" debugid="{{$value['id'] or ''}}"></i>
								</div>
								<div class="line line-dashed b-b line-lg pull-in"></div>
							@endforeach	
						</div>
					</div>
				</div>
				<div class="col-sm-9">
					<div class="panel panel-default">
						<div class="panel-heading font-bold">Api 请求</div>
						<div class="panel-body">
							<form id="apiForm" class="bs-example form-horizontal ng-pristine ng-valid" method="post" action="#">
								<div class="form-group">
									<div class="col-sm-8">
										<div class="input-group m-b">
											<div class="input-group-btn dropdown" dropdown="">
												<button type="button" class="btn btn-default"
													dropdown-toggle="" aria-haspopup="true"
													aria-expanded="false">
													<label2 class="btn-request-type">{{$data['param']['requestType'] or 'GET'}}</label2> <span class="caret"></span>
												</button>
												<ul class="dropdown-menu">
												@foreach($data['type'] as $value)
                									<li><a href="javascript:void(0);">{{$value}}</a></li>
                								@endforeach
												</ul>
												<input type="hidden" name="type" value="{{$data['param']['requestType'] or 'GET'}}">
											</div>
											<!-- /btn-group -->
											<input type="text" name="apiurl" class="form-control" placeholder="请带上http/https" value="{{$data['apiurl'] or ''}}">
											<script type="text/javascript" charset="utf-8">
                                              		var env_domain = $.cookie('env_domain');
                                              		if(env_domain){
                										$("input[name='apiurl']").val(env_domain+"{{$data['apiurl'] or ''}}")
                                                  	}
                                            </script>
										</div>
									</div>
									<div class="col-sm-4">
										<input type="hidden" value="{{ csrf_token() }}" name="_token" />
										<a href="javascript:;" class="btn m-b-xs w-xs btn-info api-send">发送</a>
										@if(Session::get('uid'))
											<a href="javascript:;" class="btn m-b-xs w-xs btn-warning api-store">保存</a>
										@else
											<a href="/Login/index" class="btn m-b-xs w-xs btn-warning api-store">登录后保存</a>
										@endif
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-12">
										<ul class="nav nav-tabs" id="myTab">
											<li class="active"><a data-toggle="tab" href="javascript:void(0);" tab-href="#tab-request-body"> Body参数 </a></li>
											<li><a data-toggle="tab" href="javascript:void(0);" tab-href="#tab-request-header"> Header参数 </a></li>
										</ul>
										<div class="tab-content">
											<div id="tab-request-body"
												class="form-group request tab-request fade row active in">
												<table class="table  table-bordered table-hover">
													<thead>
														<tr>
															<td class="center">Body参数名</td>
															<td class="center">参数值</td>
															<td class="center">操作</td>
														</tr>
													</thead>
													<tbody>
														@foreach($data['param']['request'] as $value)
														<tr>
															<td class="center"><input type="text"
																name="param[request][field][]"
																class="col-xs-12 col-sm-12" value="{{$value['field'] or ''}}"></td>
															<td class="center"><input type="text"
																name="param[request][value][]"
																class="col-xs-12 col-sm-12" value="{{$value['value'] or ''}}"></td>
															<td class="center"><i
																class="ace-icon fa fa-trash-o bigger-120 grey delNode"></i></td>
														</tr>
														@endforeach
													</tbody>
												</table>
												<span class="btn btn-sm btn-primary add-button" xtype="request">
													<i class="fa fa-plus text"></i> <span class="text">添加</span>
												</span>
												<span class="btn btn-sm btn-default batch" xtype="request">
													<span class="text">批量参数</span>
												</span>
											</div>
											<div id="tab-request-header" style="display: none;"
												class="form-group header tab-request fade row active in">
												<table class="table  table-bordered table-hover">
													<thead>
														<tr>
															<td class="center">Header参数名</td>
															<td class="center">参数值</td>
															<td class="center">操作</td>
														</tr>
													</thead>
													<tbody>
														@foreach($data['param']['header'] as $value)
    														<tr>
    															<td class="center"><input type="text"
    																name="param[header][field][]"
    																class="col-xs-12 col-sm-12" value="{{$value['field'] or ''}}"></td>
    															<td class="center"><input type="text"
    																name="param[header][value][]"
    																class="col-xs-12 col-sm-12" value="{{$value['value'] or ''}}"></td>
    															<td class="center delNode"><i
    																class="ace-icon fa fa-trash-o bigger-120 grey"></i></td>
    														</tr>
    													@endforeach
													</tbody>
												</table>
												<span class="btn btn-sm btn-primary add-button" xtype="header">
													<i class="fa fa-plus text"></i> <span class="text">添加</span>
												</span>
												<span class="btn btn-sm btn-default batch" xtype="header">
													<span class="text">批量参数</span>
												</span>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-12">
										<div class="tab-container">
											<ul class="nav nav-tabs">
												<li class="ng-isolate-scope active"><a href="javascript:void(0);" tab-href="#response-body-hight"
													class="ng-binding"><tab-heading class="ng-scope">  Response Body(Highlight)  </tab-heading></a>
												</li>
												<li class="ng-isolate-scope"><a href="javascript:void(0);" tab-href="#response-body-raw" class="ng-binding"><tab-heading
															class="ng-scope">  Response Body(Raw)  </tab-heading></a></li>
												<li class="ng-isolate-scope"><a href="javascript:void(0);" tab-href="#response-body-header" class="ng-binding"><tab-heading
															class="ng-scope">  Response Headers  </tab-heading></a></li>
											</ul>
											<div class="tab-content">
												<div id="response-body-hight" class="response-body tab-pane ng-scope active">
													<div id="goback-highlight">
										
													</div>
												</div>
												<div id="response-body-raw" class="response-body tab-pane ng-scope">
													<div id="goback-raw">
										
													</div>
												</div>
												<div id="response-body-header" class="response-body tab-pane ng-scope">
													<div id="goback-header">
										
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-12">
										<div>1. 接口请求时，会自动记录cookie，以便下一次请求时带上该cookie信息</div>
										<div>2. 支持绑定服务器IP，无需修改host文件</div>
										<div>2. 响应的json/jsonp数据，建议设置Content-Type: application/json; charset=utf-8<div>
										<div>3. 请求Api时，默认10s超时<div>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /content -->
    		<div id="addBind" style="display: none;"> 
    			<form  action="#" method="post" id="myForm">
    			<div class="wrapper-md ng-scope">
        			<div class="row" >
                		<div class="btn-block">
                			<p class="text-muted">支持单个域名绑定多个IP，无需修改系统host文件</p>
                			<div class="form-group">
                			  <label>域名(不带http://)</label>
                			  <input type="text"  name="domain" class="form-control domain">
                			  <span class="help-block m-b-none text-danger"></span>
                			</div>
                			<div class="form-group">
                			  <label>IP地址(多个IP请用英文,隔开)</label>
                			  <textarea class="form-control ips" rows="6" name="ips"></textarea>
                			  <span class="help-block m-b-none text-danger"></span>
                			</div>
                			<div class="form-group">
                			  <label> </label>
                			  <input type="hidden" value="" name="domid"  class="domid"/>
                			  <button class="btn btn-primary btn-info-submit domainbind">保存</button>
                			</div>
                		</div>
                	</div>
            	</div>
            	</form>
            	
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
</div>
<script type="text/javascript" src="{{URL::asset('js/validation/jquery.validate.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/validation/messages_zh.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/jsonview/jquery.jsonview.min.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/format/jsonp.js')}}"></script>
<script type="text/javascript" src="{{URL::asset('js/format/xml.js')}}"></script>
<script type="text/javascript" charset="utf-8">

	var _token = $("input[name='_token']").val();
	$(".input-group-btn").click(function(){
		if($(this).data('status')==1){
			$(this).data('status',2);
			$(this).removeClass('open');
			$('.btn-request').attr('aria-expanded', false);
		}else{
			$(this).data('status',1);
			$(this).addClass('open');
			$('.btn-request').attr('aria-expanded', true);
		}
	})
	$('.dropdown-menu li').click(function(){
		var type = $(this).find('a').html();
		$('.btn-request-type').html(type);
		$("input[name='type']").val(type);
	})
	$('.iplist').on({
		mouseover : function(){  
            $(this).addClass('alert-info');  
        } ,  
        mouseout : function(){  
        	$(this).removeClass('alert-info');  
        },
        click:function(){
        	var form_group = $(this).parents('.form-group');
        	form_group.find('.ace-icon').remove();
            $(this).prepend('<i class="ace-icon fa fa-check grey"></i>');
            var domid = form_group.find('.binding').attr('domid');
        	var iplong = $(this).attr('iplong');
        	$.ajax({
                cache: false,
                type: "POST",
                url:"{{route('Debug.isBind')}}",
                data:{
                	'domid':domid,
                	'iplong':iplong,
                },
                headers: {
                    'X-CSRF-TOKEN': _token
                },
                dataType: 'json',
                success: function(res) {
                	
                },
                error: function(request) {
                },
            });
        }
	})
	
	//弹出绑定窗口
	$(".binding").click(function(){
		var content = $("#addBind").html();
		var obj = $(this);
		layer.open({
			  type: 1,
			  title: '服务器IP绑定',
			  skin: 'layui-layer-demo', //样式类名
			  closeBtn: 1, //不显示关闭按钮
			  anim: 2,
			  shadeClose: true, //开启遮罩关闭
			  content: content
		});
		$(".domain").val(obj.attr('domain'));
		$(".domid").val(obj.attr('domid'));
		$(".ips").val(obj.attr('ips'));
	})
	//弹出批量导入窗口
	$(".batch").click(function(){
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
		var tbody = $("."+xtype).find("tbody");
		string = $.trim(string);
		try {
            var jsonData = JSON.parse(string);
            var theRequest = getJsonparse(jsonData, {});
            if(string.indexOf('{')>-1){
            	tbody.html('');
            	$.each(theRequest,function(field,value){
            		var element = '';
            		if(xtype=='request' || xtype=='header'){
            			element = nodePram(xtype, field, value);
            		}
            		tbody.append(element);
                })
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
      			if(key){
      				theRequest[key]=unescape(val); 
      			}
      		}
      		tbody.html('');
      		$.each(theRequest,function(field,value){
        		var element = '';
        		if(xtype=='request' || xtype=='header'){
        			element = nodePram(xtype, field, value);
        		}
        		tbody.append(element);
            })
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
	$("#myTab li").click(function(){
		$("#myTab li").removeClass('active');
		$(this).addClass('active');
		var obj = $(this).find('a');
		$(".tab-request").hide();
		$(obj.attr('tab-href')).show();
		
	})
	$(".ng-isolate-scope").click(function(){
		$(".ng-isolate-scope").removeClass('active');
		$(this).addClass('active');
		var obj = $(this).find('a');
		$(".response-body").hide().removeClass('tab-pane');
		$(obj.attr('tab-href')).show();
	})
	//单击绑定保存按钮
	$("body").on('click','.domainbind',function(){
		var obj = $(this);
		var myForm = obj.parents('form');
		domainSet(myForm)
	})
	
	//保存绑定信息
 	function domainSet(myForm){
	 	
		var validator = myForm.validate({
			submitHandler: function(form) {
				//获取信息
				var domain = myForm.find(".domain").val();
				var domid = myForm.find(".domid").val();
				var ips = myForm.find(".ips").val();
					$.ajax({
		                cache: false,
		                type: "POST",
		                url:"{{route('Debug.domain')}}",
		                data:{
		                	'domain':domain,
		                	'domid':domid,
		                	'ips':ips,
		                },
		                headers: {
		                    'X-CSRF-TOKEN': _token
		                },
		                dataType: 'json',
		                success: function(res) {
		                	if(res.status==200){
		                		layer.msg(res.message)
		                		setTimeout(function(){
									 window.location.reload();
								 }, 2000);
		                	}else{
		                		layer.alert(res.message)
		                	}
		                },
		                error: function(request) {
		                    layer.msg("网络错误，请稍后重试");
		                },
		            });
			},
			rules:{
				domain:{
					required:true,
					maxlength:50,
					minlength:2
				},
				ips:{
	    			required:true,
	    			minlength:7
	    		}
			},
			messages:{
				domain :{
					required:"域名不能为空",
					maxlength:"不能超过50个字符",
					minlength:"不能少于2个字符",
				},
				ips:{
	    			required:"IP不能为空",
					minlength:"不能少于7个字符",
	    		}
		        
			},
			errorElement: 'custom',
			errorClass:'error',
			errorPlacement: function(error, custom) {
				error.appendTo( custom.next('span') ); 
			},  
		})
	}
	//节点参数
	function nodePram(xtype, field, value){

		if(!field || typeof(field)=='undifined') field = '';
		if(!value || typeof(value)=='undifined') value = '';
		element  = '<tr>';
		element += '<td class="center">';
		element += '	<input type="text" name="param['+xtype+'][field][]" class="col-xs-12 col-sm-12" value="'+field+'">';
		element += '</td>';
		element += '<td class="center">';
		element += '<input type="text" name="param['+xtype+'][value][]" class="col-xs-12 col-sm-12" value="'+value+'">';
		element += '</td>';
		element += '	<td class="center">';
		element += '	<i class="ace-icon fa fa-trash-o bigger-120 grey delNode"></i>';
		element += '</td>';
		element += '</tr>';

		return element;
	}
    //参数和header头添加
    $(".tab-content").on("click", ".add-button", function(){
		var xtype = $(this).attr("xtype");
		var element = '';
		if(xtype=='request' || xtype=='header'){
			field = '';
			element = nodePram(xtype, field);
		}
		$(this).parents("."+xtype).find("tbody").append(element);
    });
    //删除节点
    $(".tab-content").on("click", ".delNode", function(){
		$(this).parents("tr").remove();
    });
    //转换成html实体
    function htmlspecialchars(str)  
    {  
            str = str.replace(/&/g, '&amp;');
			str = str.replace(/</g, '&lt;');
			str = str.replace(/>/g, '&gt;');
			str = str.replace(/"/g, '&quot;');
			str = str.replace(/'/g, '&#039;');
			
			return str;
    }
    //发送
    $(".api-send").click(function(){

    	var apiurl = $('input[name="apiurl"]').val();
		var reg = /^((https|http)?:\/\/)+[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/;
		if(!reg.test(apiurl)){
			layer.msg("请输入正确的Api地址,需带上http/https");	
		}else{
			var index = layer.load(0, {shade: false});
			$.ajax({
	            cache: false,
	            type: "POST",
	            url:"{{route('Debug.test')}}",
	            data:$('#apiForm').serialize(),
	            headers: {
	                'X-CSRF-TOKEN': _token
	            },
	            dataType: 'json',
	            success: function(res) {
	            	layer.close(index);
	            	//Response body格式化
	            	var goback = res.data.content;
	            	var rawGoback = res.data.rawContent;
	            	var contentType = res.data.ContentType;
	            	$("#goback-highlight").html('');
	            	$("#goback-raw").html('');
	            	$("#goback-header").html('');
	            	$("#goback-raw").html(rawGoback);
	            	if(contentType=='application/json'){
	            		try {
	                        var param=JSON.parse(goback);
	                        if(goback.indexOf('{')>-1){
	                        	$("#goback-highlight").JSONView(goback, { collapsed: false });
	                        }
	                    } catch(e) {
	                    	$("#goback-highlight").html("<pre>"+formatJsonp(goback)+"</pre>");
	                    }
		            }else if(contentType=='application/xml' || contentType=='text/xml'){
		            	$("#goback-highlight").html('<pre>'+htmlspecialchars(formatXml(goback))+'</pre>');
	            	}else if(contentType=='text/html'){
		            	try{
		            		var param=JSON.parse(goback);
	                        if(goback.indexOf('{')>-1){
		                        console.log(136);
	                        	$("#goback-highlight").JSONView(goback, { collapsed: false });
	                        }
			            } catch(e){
			            	//var resultHtml =  htmlspecialchars(goback);
			            	var resultHtml = goback.replace(/<script.*?>(.|\n|\r|\t)+?<\/script>/gim,"");
			            	$("#goback-highlight").html(resultHtml);
			            }
			        }
	            	//header
	            	var element = '<ul class="list-group list-group-alt no-borders pull-in m-b-none ng-scope">';
	            	$.each(res.data.header,function(i,vol){
						element +='<li class="list-group-item"><span>'+i+': </span>'+vol+'</li>'
		            });
		            element += '</ul>';
	            	$("#goback-header").html(element);
	            	
	            },
	            error: function(request) {
	            	layer.close(index);
	                layer.msg("网络错误，请稍后重试");
	            },
	        });
		}
    })
    //Api保存
    $(".api-store").click(function(){
    	var apiurl = $('input[name="apiurl"]').val();
		var reg = /^((https|http)?:\/\/)+[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/;
		if(!reg.test(apiurl)){
			layer.msg("请输入正确的Api地址,需带上http/https");	
		}else{
			var index = layer.load(2, {shade: false});
			$.ajax({
	            cache: false,
	            type: "POST",
	            url:"{{route('Debug.store')}}",
	            data:$('#apiForm').serialize(),
	            headers: {
	                'X-CSRF-TOKEN': _token
	            },
	            dataType: 'json',
	            success: function(res) {
	            	layer.msg(res.message);
	            	var element = '';
	            	if(res.status==200){
	            		element +='<div class="form-group">';
	            		element +='<label><a href="/Debug?sid='+res.data.id+'">【'+res.data.type+'】'+res.data.path+'</a></label>';
	            		element +='<i class="ace-icon fa fa-trash-o pull-right bigger-120 grey del-store" debugid='+res.data.id+'></i>';
	            		element +='</div>';
	            		element +='<div class="line line-dashed b-b line-lg pull-in"></div>';
	            		$(".store-record").append(element);
		            }
	            	layer.close(index);
	            },
	            error: function(request) {
	            	layer.close(index);
	                layer.msg("网络错误，请稍后重试");
	            },
	        });
		}
    })
    //删除保存记录
    $(".panel-default").on('click', '.del-store', function(){
        
		var obj = $(this);
		$.ajax({
            cache: false,
            type: "POST",
            url:"{{route('Debug.del')}}",
            data:{'id':obj.attr('debugid')},
            headers: {
                'X-CSRF-TOKEN': _token
            },
            dataType: 'json',
            success: function(res) {
            	layer.msg(res.message);
            	obj.parents('.form-group').remove();
            },
            error: function(request) {
                layer.msg("网络错误，请稍后重试");
            },
        });
    })
	
</script>

<!-- /.page-content -->
@endsection
