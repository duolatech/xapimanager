@extends('base') @section('page-content')
<div class="page-content">
	<link rel="stylesheet" href="{{URL::asset('js/jsonview/jquery.jsonview.min.css')}}" />
	<script type="text/javascript" src="{{URL::asset('js/jsonview/jquery.jsonview.min.js')}}"></script>
	<!-- #section:settings.box -->
	@include('public/setbox')
	<div class="row urllistbox">
		<div class="col-xs-2 gurllist widget-box">
			<div class="widget-header ">
				<h4 class="widget-title">历史记录</h4>
			</div>
			<ul class="history">
			</ul>
		</div>
		<div class="col-xs-9 gurlrighttext">
			<!-- PAGE CONTENT BEGINS -->
			<form class="form-horizontal" action="#"
				method="post" id="myForm">
			<div class="widget-box">
				<div class="widget-header">
					<h4 class="widget-title apiname">接口信息</h4>
				</div>

				<div class="widget-body">
					<div class="widget-main  clearfix">
						<div class="input-group col-xs-12">
							<select class="gsel form-control request-type" name="type">
								@foreach($data['type'] as $value)
									<option value="{{$value}}">{{$value}}</option>
								@endforeach
							</select> 
							<input class="form-control input-mask-date ginput" name="apiurl" type="text" value="">
							<input type="hidden" value="{{ csrf_token() }}" name="_token" />
							<input type="hidden" value="" name="apiname" />
							<button class="btn btn-primary api-send" type="button">发送</button>
							<button class="btn btn-warning api-clear" type="button">清空</button>
						</div>
						<div class="tabbable">
							<ul class="nav nav-tabs" id="myTab">
								<li class="active"><a data-toggle="tab" href="#tab-request"> 请求参数 </a>
								</li>

								<li><a data-toggle="tab" href="#tab-header"> header头信息 </a></li>
							</ul>

							<div class="tab-content">
								<div id="tab-request" class="form-group request tab-pane fade in active row">
									<table class="table  table-bordered table-hover">
										<thead>
											<tr>
												<td class="center">参数名</td>
												<td class="center">参数值</td>
												<td class="center">操作</td>
											</tr>
										</thead>
										<tbody></tbody>
									</table>
									<span class="btn btn-minier btn-info add-button"
										xtype="request"> <i class="glyphicon-plus fa "></i> 增加
									</span>
								</div>

								<div id="tab-header" class="form-group header tab-pane fade">
									<table class="table  table-bordered table-hover">
										<thead>
											<tr>
												<td class="center">Header名称</td>
												<td class="center">Header值</td>
												<td class="center">操作</td>
											</tr>
										</thead>
										<tbody></tbody>
									</table>
									<span class="btn btn-minier btn-info add-button"
										xtype="header"> <i class="glyphicon-plus fa "></i> 增加
									</span>
								</div>
							</div>
						</div>
						<div class="tabbable">
							<ul class="nav nav-tabs" id="myTab2">
								<li class="active"><a data-toggle="tab" href="#tab-response-body-highlight" aria-expanded="true"> Response Body(Highlight) </a></li>
								<li><a data-toggle="tab" href="#tab-response-body-raw" aria-expanded="false"> Response Body(Raw) </a></li>
								<li><a data-toggle="tab" href="#tab-response-headers" aria-expanded="false"> Response Headers </a></li>
							</ul>
							<div class="tab-content">
								<div id="tab-response-body-highlight" class="form-group request tab-pane fade in active row">
									<div id="goback-highlight">
										
									</div>
								</div>
								<div id="tab-response-body-raw" class="form-group request tab-pane fade">
									<div id="goback-raw">
										
									</div>
								</div>
								<div id="tab-response-headers" class="form-group header tab-pane fade">
									
								</div>
							</div>
						</div>
						<!-- /section:plugins/date-time.datetimepicker -->
					</div>
				</div>
			</div>
			</form>
			<!-- PAGE CONTENT ENDS -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
</div>
<!-- /.page-content -->
<script type="text/javascript" charset="utf-8">

$(function(){

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
		element += '	<td class="center delNode">';
		element += '	<i class="ace-icon fa fa-trash-o bigger-120 grey"></i>';
		element += '</td>';
		element += '</tr>';

		return element;
	}
    //参数和header头添加
    $(".form-group").on("click", ".add-button", function(){
		var xtype = $(this).attr("xtype");
		var element = '';
		if(xtype=='request' || xtype=='header'){
			field = '';
			element = nodePram(xtype, field);
		}
		$(this).parents("."+xtype).find("tbody").append(element);
    });
    //删除节点
    $(".form-group").on("click", ".delNode", function(){
		$(this).parents("tr").remove();
    });
    //请求方式
    var type = 'GET';
    var json = {!! $data['info'] !!};     
    render(type, json);                                                                                                                                                                                 
    $(".request-type").change(function(){
		type = $(this).val();
		render(type, json);
    });
    //渲染页面
    function render(type, data){

        if(!data){
        	element1 = nodePram('request');
        	element2 = nodePram('header');
			$("#tab-request tbody").html(element1);
			$("#tab-header tbody").html(element2);
			return ;
        }
		//接口名称/地址
		if(data.apiname){
			$(".apiname").html(data.apiname);
			$('input[name="apiname"]').val(data.apiname);
		}
		if(data.detail.gateway){
			$('input[name="apiurl"]').val(data.detail.gateway);
		}
		//请求参数
		var param = data.param[type].request;
		if(param){
			var element = '';
			$.each(param, function(i, info){
				element += nodePram('request', info.field, info.value);
			})
			element += nodePram('request');
			$("#tab-request tbody").html(element);
		}
		//header头信息
		var header = data.param.HEADER.request;
		if(header){
			var element = '';
			$.each(header,function(i, info){
				element += nodePram('header', info.field, info.value);
			})
			element += nodePram('header');
			$("#tab-header tbody").html(element);
		}
    }
    //发送
    $(".api-send").click(function(){

    	var apiurl = $('input[name="apiurl"]').val();

		var reg = /^((https|http)?:\/\/)+[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/;
		if(!reg.test(apiurl)){
			layer.msg("请输入正确的Api地址");	
		}else{
			apiCache = $.cookie('apiCache');        	
			$.ajax({
	            cache: false,
	            type: "POST",
	            url:"{{route('Api.test')}}",
	            data:$('#myForm').serialize(),
	            headers: {
	                'X-CSRF-TOKEN': $("input[name='_token']").val()
	            },
	            dataType: 'json',
	            success: function(res) {

	            	//Response body格式化
	            	var goback = res.data.content;
	            	var contentType = res.data.ContentType;
	            	if(contentType=='application/json'){
	            		$("#goback-highlight").JSONView(goback, { collapsed: false });
		            }else{
		            	$("#goback-highlight").html(goback);
			        }
	            	$("#goback-raw").html(goback);
	            	//header
	            	var element = '<ul>';
	            	$.each(res.data.header,function(i,vol){
						element +='<li class="urlli"><span>'+i+': </span>'+vol+'</li>'
		            });
		            element += '</ul>';
	            	$("#tab-response-headers").html(element);
					//历史记录存取	            	
	            	storeHistory(res.data.info);
	            	//更新历史记录
	            	updateHistory();
	            	
	            },
	            error: function(request) {
	                layer.msg("网络错误，请稍后重试");
	            },
	        });
		}
    	
    })
    //清空
    $(".api-clear").click(function(){
    	$(".apiname").html('接口信息');
    	$('input[name="apiurl"]').val('');
    	element1 = nodePram('request');
    	element2 = nodePram('header');
    	$("#tab-request tbody").html(element1);
		$("#tab-header tbody").html(element2);
		$("#goback-highlight").html("")
		$("#goback-raw").html("");
		$("#tab-response-headers").html("");
    })
    //加载历史记录接口
    $(".page-content").on('click', '.history a', function(){
			var pos = $(this).attr('pos');
			apiCache = $.cookie('apiCache');
	        apiCache = JSON.parse(apiCache);
	        var apiInfo = apiCache[pos];
	        if(apiInfo){
	        	render(apiInfo['type'], apiInfo);
		    }
    });
    //删除历史记录
    $(".page-content").on('click', '.history i', function(){
		var pos = $(this).attr('pos');
		apiCache = $.cookie('apiCache');
        apiCache = JSON.parse(apiCache);
        apiCache.splice(pos, 1);
        if(!apiCache){
        	apiCache = [];
        }
        //更新cookie
        apiCache = JSON.stringify(apiCache);
    	$.cookie('apiCache', apiCache, { expires: 7 });
    	//移除节点
    	$(this).parent('li').remove();
	});
    updateHistory();
    //更新历史记录
    function updateHistory(){
    	//历史记录
        apiCache = $.cookie('apiCache');
        if(apiCache){
        	apiCache = JSON.parse(apiCache);
            var node = '';
    		$.each(apiCache, function(i, info){
    			node += '<li><span class="leftspan"pos='+i+' title="'+info.detail.gateway+'">'+info.name+'</span><i class="fa fa-trash-o bigger-120 pull-right"></i></li>'
    		});
        }
        $(".history").html(node);
    }
    //存储历史记录
    function storeHistory(data){

    	apiCache = $.cookie('apiCache');

    	if(!apiCache){
        	apiCache = [];
    	}else{
    		apiCache = JSON.parse(apiCache);
        }
    	apiCache.push(data);
		apiCache.reverse();
    	apiCache.slice(0,10);

    	
    	apiCache = JSON.stringify(apiCache);
    	$.cookie('apiCache', apiCache, { expires: 7 });
    }
    
    
});
</script>
@endsection
