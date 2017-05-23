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
					<a class="btn btn-info" href="/User/info"
						value="">新增</a> <label class="inline">用户搜索</label> 
					<select name="field" class="form-control">
						<option value="username">用户名</option>
						<option value="phone">手机</option>
						<option value="email">邮箱</option>
					</select> 
					<input type="text" name="keyword" value="" class="form-control"> 
					<label class="inline">用户组</label> 
					<select name="group_id" class="form-control">
						<option value="0">全部</option>
						@foreach($list['group'] as $value)
							<option value="{{$value['id']}}" >{{$value['groupname']}}</option>
						@endforeach
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
								<th class="center"><input class="check-all" type="checkbox"
									value=""></th>
								<th>用户名</th>
								<th>用户组</th>
								<th class="center">性别</th>
								<th>电话</th>
								<th>邮箱</th>
								<th>注册时间</th>
								<th>在职状态</th>
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

	ajaxUser({page:1},1);
	
	//分页
	function pagination(pageCount){
		var field   = $('select[name="field"]').val();
		var keyword = $('input[name="keyword"]').val();
		var group_id = $('select[name="group_id"]').val();
		$('.M-box').pagination({
		    pageCount: pageCount,
		    jump: true,
		    callback:function(api){
		        var data = {
		            page: api.getCurrent(),
		            field:field,
					keyword:keyword,
					group_id:group_id
		        };
		        ajaxUser(data,0);
		    }
		});
	}
	//获取用户信息
	function ajaxUser(data, first){

		var _token = $("input[name='_token']").val();
		$.ajax({
            cache: false,
            type: "get",
            url:"{{route('ajaxUser')}}",
            data: data,
            headers: {
                'X-CSRF-TOKEN': _token
            },
            dataType: 'json',
            success: function(res) {
            	if(res.status==200){
            		render(res.data.info);
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
				if(item.status==1){
					item.jobStatus = '在职';
				}else{
					item.jobStatus = '离职';
				}
				element +=  '<tr>';
				element +=  '<td class="center"><input class="ids" type="checkbox" name="mids" value="1"></td>';
				element +=  '<td>'+item.username+'</td>';
				element +=  '<td class="grouptd">'+item.groupname+'</td>';
				element +=  '<td class="center">'+item.sex+'</td>';
				element +=  '<td>'+item.phone+'</td>';
				element +=  '<td>'+item.email+'</td>';
				element +=  '<td>'+item.ctime+'</td>';
				element +=  '<td>'+item.jobStatus+'</td>';
				element +=  '<td class="center"><a href="/User/info?uid='+item.uid+'">修改</a>&nbsp;</td>';
				element +=  '</tr>';
			});
			$("tbody").html(element);
		}
	}

	$('.search').click(function(){
		
		var field   = $('select[name="field"]').val();
		var keyword = $('input[name="keyword"]').val();
		var group_id = $('select[name="group_id"]').val();
		if(keyword || group_id){
			ajaxUser({
				'search':1,
				field:field,
				keyword:keyword,
				group_id:group_id
			}, 1);
		}
		
	})
	
})
		
</script>
@endsection
