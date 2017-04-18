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
					<div class="pinyin">
    					@foreach($list['letter'] as $value)
    						<a class="btn btn-white btn-sm btn-primary classifyLetter">{{$value}}</a>
    					@endforeach
					</div>
					<div class="space-6"></div>
					<p id="classifyGather">
						
					</p>
				</form>
			</div>
			<div class="space-4"></div>
			<div class="row">
				<form id="form" method="post"
					action="#">
					<input type="hidden" value="{{ csrf_token() }}" name="_token" />
					<input type="hidden" value="0" name="classifyId" />
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

	//字母分类切换
	var gather = {!! $list['gather'] or '' !!};
	$('.classifyLetter').hover(function(){
		var element = ''
		var letter = $(this).text();
		if(typeof(gather[letter])!="undefined"){
			$.each(gather[letter], function(i,classify){
				element += '<a class="btn btn-white btn-info subClassify mgr10" subid='+classify.id+'>'+classify.classifyname+'</a>'
			});
		}
		$('#classifyGather').html(element);
	},function(){});

	//当前环境
	var env = $(".current_env span").attr("env");
	//当前选择分类
	var type = "{{$get['type'] or ''}}";
	var apiname = "{{$get['apiname'] or ''}}";
	var URI = "{{$get['URI'] or ''}}";
	var author = "{{$get['author'] or ''}}";
	var classify = "{{$get['classify'] or ''}}";
	var subClassify = "{{$get['subClassify'] or ''}}";
	$("#classifyGather").on('click', '.subClassify', function(){
		var subid = $(this).attr('subid');
		$("input[name='classifyId']").val(subid);
		window.location.href='/Api/list?page=1&envid='+env+'&subClassify='+subid;
	})
	ajaxApiList({
		page:1, 
		envid:env,
		type:type,
		apiname:apiname,
		URI:URI,
		author:author,
		classify:classify,
		subClassify:subClassify,
	},1);
	//分页
	function pagination(){
		$('.M-box').pagination({
			pageCount: pageCount,
		    jump: true,
		    callback:function(api){

		        var data = {
		            page: api.getCurrent(),
		            envid: env,
		            //classifyId: $("input[name='classifyId']").val(),
		            type:type,
					apiname:apiname,
					URI:URI,
					author:author,
					classify:classify,
					subClassify:subClassify,
		        };
		        ajaxApiList(data, 0);
		    }
		});
	}
	
	//获取接口列表信息, 第一页加载分页插件
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
					if(sub.status==1) btn = 'btn btn-xs disabled btn-success';
					if(sub.status==2) btn = 'btn btn-xs disabled btn-info';
					if(sub.status==3) btn = 'btn btn-xs disabled btn-warning';
					if(sub.status==5) btn = 'btn btn-xs disabled btn-inverse';
					if(key==0){
						element +=  '<td class="center align-middle" rowspan='+rowspan+'>'+item.apiname+'</td>';
					}
					element +=  '<td><a href="/Api/detail?did='+sub.id+'">'+sub.version+'</a></td>';
					element +=  '<td class="grouptd">'+sub.URI+'</td>';
					element +=  '<td>'+sub.username+'</td>';
					element +=  '<td>'+sub.ctime+'</td>';
					element += '<td><button class="'+btn+'">'+sub.apistatus+'</button></td>'
					element +=  '<td class="center">';
					element +=  	'<a href="/Api/info?version_type=add&lid='+sub.listid+'" target="_blank">添加版本</a>&nbsp;';
					element +=  	'<a href="/Api/detail?did='+sub.id+'">查看</a>&nbsp;';
					if(sub.status==1 || sub.status==2){
						element +=  	'<a href="javascript:void(0)" id='+sub.id+' class="discard">废弃</a>&nbsp;';
					}
					element +=  '</td>';
					element +=  '</tr>';
				});
			});
			$("tbody").html(element);
		}
	}

	//废弃接口
	$("#form").on("click", ".discard", function(){
		var did = $(this).attr('id');
		var _token = $("input[name='_token']").val();
		layer.confirm('您确认废弃该接口？', {
			  btn: ['确定', '取消'] //按钮
			}, function(){
				$.ajax({
		            cache: false,
		            type: "post",
		            url:"{{route('Api.discard')}}",
		            data: {'did':did},
		            headers: {
		                'X-CSRF-TOKEN': _token
		            },
		            dataType: 'json',
		            success: function(res) {
		            	if(res.status==200){
		            		layer.msg(res.message, {icon: 1});
		            		window.location.href='/Api/list';
		                }
		            },
		            error: function(request) {
		                layer.msg("网络错误，请稍后重试");
		            },
		        });
			}, function(){
		})
	})
})
	
</script>
@endsection
