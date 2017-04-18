@extends('base') @section('page-content')
<div class="page-content">
	<!-- #section:settings.box -->
	@include('public/setbox')
	<link rel="stylesheet" href="{{URL::asset('js/pagination/pagination.css')}}" />
	<div class="row">
		<div class="col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<div class="row">
				<form class="form-inline" action="#" method="get">
					<label class="inline">资源名</label> 
					<input type="text" name="apiname" value="" class="form-control"> 
					<label class="inline">URI</label> 
					<input type="text" name="URI" value="" class="form-control"> 
					<label class="inline">开发者</label> 
					<input type="text" name="author" value="" class="form-control"> 
					<label class="inline">审核状态</label> 
					<select name="auditStatus" class="form-control">
						<option value="0">全部</option>
						<option value="2">待审核</option>
						<option value="5">已拒绝</option>
					</select>
					<button type="button" class="btn btn-purple btn-sm search">
						<span class="ace-icon fa fa-search icon-on-right bigger-110"></span>
						Search
					</button>
				</form>
			</div>
			<div class="space-4"></div>
			<div class="row">
				<form id="form" method="post"
					action="#">
					<input type="hidden" value="{{ csrf_token() }}" name="_token" />
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th class="center">资源名</th>
								<th>版本</th>
								<th>URI</th>
								<th class="center">开发者</th>
								<th>时间</th>
								<th>状态</th>
								<th class="center">操作</th>
							</tr>
						</thead>
						<tbody> </tbody>
					</table>
				</form>
				<div>
					<div class="M-box"></div>
				</div>
			</div>
			<!-- PAGE CONTENT ENDS -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
</div>
<!-- /.page-content -->
<script type="text/javascript" src="{{URL::asset('js/pagination/jquery.pagination.min.js')}}"></script>
<script type="text/javascript" charset="utf-8">

$(function(){

	//当前环境
	var env = $(".current_env span").attr("env");
	ajaxApiList({
		type:'search', 
		page:1, 
		envid:env, 
		status: '2,5'
	}, 1);
	//分页
	function pagination(pageCount){

		var auditStatus = $('select[name="auditStatus"]').val();
		var status = (auditStatus==0) ? '2,5' : auditStatus;
		$('.M-box').pagination({
			pageCount: pageCount,
		    jump: true,
		    callback:function(api){
		        var data = {
		        	type:'search',
		            page: api.getCurrent(),
		            envid: env,
		            apiname: $("input[name='apiname']").val(),
		            URI: $("input[name='URI']").val(),
		            status: status
		        };
		        ajaxApiList(data, 0);
		    }
		});
	}
	//获取接口列表信息
	function ajaxApiList(data, first){

		var _token = $("input[name='_token']").val();
		$.ajax({
            cache: false,
            type: "GET",
            url:"{{route('Api.ajaxList')}}",
            data: data,
            headers: {
                'X-CSRF-TOKEN': _token
            },
            dataType: 'json',
            success: function(res) {
            	if(res.status==200){
            		render(res.data.list);
            		pageCount = res.data.pageCount;
            		if(first==1){
            			pagination(pageCount);
                	}
                }
            },
            error: function(request) {
                layer.msg("网络错误，请稍后重试");
            },
        });

	}
	//渲染页面
	function render(res){

		var element = "";
		if(res){
			$.each(res,function(i, item){
				var rowspan = item.info.length;
				$.each(item.info,function(key, sub){
					element +=  '<tr>';
					if(sub.status==2) btn = 'btn btn-xs disabled btn-info';
					if(sub.status==5) btn = 'btn btn-xs disabled btn-inverse';
					if(key==0){
						element +=  '<td class="center" rowspan='+rowspan+'>'+item.apiname+'</td>';
					}
					element +=  '<td><a href="/Api/detail?did='+sub.id+'" target="_blank">'+sub.version+'</a></td>';
					element +=  '<td class="grouptd">'+sub.URI+'</td>';
					element +=  '<td>'+sub.username+'</td>';
					element +=  '<td>'+sub.ctime+'</td>';
					element += 	'<td><button class="'+btn+'">'+sub.apistatus+'</button></td>'
					element +=  '<td class="center">';
					element +=  	'<a href="javascript:void(0)" class="operate" did='+sub.id+' uri='+sub.URI+' status=1 >通过</a>&nbsp;';
					if(sub.status==2){
						element +=  	'<a href="javascript:void(0)" class="operate" did='+sub.id+' uri='+sub.URI+' status=2 >拒绝</a>&nbsp;';
					}
					element +=  '</td>';
					element +=  '</tr>';
				});
			});
			$("tbody").html(element);
		}
	}
	//操作
	$('table').on('click', '.operate', function(){

		var did = $(this).attr('did');
		var uri = $(this).attr('uri');
		var status = $(this).attr('status');

		if(status==1){
			layer.confirm('确认接口: '+uri+'通过审核？', {
				  btn: ['确定','取消']
			}, function(){
				operate(did, status, '审核通过');
			});
		}else if(status==2){
			layer.open({
				  type: 1,
				  skin: 'layui-layer-rim',
				  area: ['420px', '280px'],
				  content: '<span class="greason">拒绝原因:</span> <textarea name="reject" class="gresontext" rows="5"></textarea>',
				  btn: ['提交'],
				  yes: function(index, layero){
					  var reject = $('textarea[name="reject"]').val();
					  operate(did, status, reject)
				  }
			});
		}
	})
	//通过或拒绝接口
	function operate(did, status, des){

		var _token = $("input[name='_token']").val();
		$.ajax({
            cache: false,
            type: "POST",
            url:"{{route('Api.audit')}}",
            data: {did:did,status:status,des:des},
            headers: {
                'X-CSRF-TOKEN': _token
            },
            dataType: 'json',
            success: function(res) {

            	layer.closeAll();
            	if(res.status==200){
            		layer.msg(res.message, {icon: 1})
            		setTimeout(function(){
						 window.location.reload();
					 }, 2000);
                }else{
                	layer.msg(res.message, {icon: 2})
                 }
            },
            error: function(request) {
                layer.msg("网络错误，请稍后重试");
            },
        });
	}
	//搜索接口
	$('.search').click(function(){
		
		var env = $(".current_env span").attr("env");
    	var apiname = $('input[name="apiname"]').val();
    	var URI = $('input[name="URI"]').val();
    	var author = $('input[name="author"]').val();
    	var auditStatus = $('select[name="auditStatus"]').val();
		var status = (auditStatus==0) ? '2,5' : auditStatus;

    	ajaxApiList({
        	type:'search', 
        	page:1, 
        	envid:env, 
        	apiname:apiname,
        	URI:URI,
        	author:author,
        	status: status
        }, 1);
	})
})
		
</script>
@endsection
