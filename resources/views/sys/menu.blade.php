@extends('base') @section('page-content')
<div class="page-content">
	<!-- #section:settings.box -->
	@include('public/setbox')
	<div class="row">
		<div class="col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<div class="row">
				<div class="space-4"></div>
				<form id="myForm" method="post"
					action="#">
					<table class="table table-striped table-bordered table-hover">
						<thead>
							<tr>
								<th class="center"></th>
								<th>菜单名称</th>
								<th>链接</th>
								<th>ICON</th>
								<th class="center">状态</th>
								<th>排序</th>
								<th class="center">操作</th>
							</tr>
						</thead>
						<tbody>
							@foreach($sys['ValidMenu'] as $menu)
							<tr>
								<td class="center"><input class="ids" type="checkbox"
									name="mids"  value="{{$menu['id']}}"></td>
								<td>{{$menu['title']}}</td>
								<td>{{$menu['path']}}</td>
								<td><i class="{{$menu['icon']}}"></i></td>
								<td class="center">@if($menu['status'] == 1) 显示 @else 隐藏  @endif</td>
								<td>{{$menu['sort']}}</td>
								<td class="center"><a
									href="/Sys/Menu/info?id={{$menu['id']}}">修改</a>&nbsp;<a
									class="del" href="javascript:;"
									val="/Sys/Menu/del" title="删除" mid="{{$menu['id']}}">删除</a></td>
							</tr>
								@foreach($menu['child'] as $submenu)
									<tr>
        								<td class="center"><input class="ids" type="checkbox"
        									name="mids"  value="{{$submenu['id']}}"></td>
        								<td>——{{$submenu['title']}}</td>
        								<td>{{$submenu['path']}}</td>
        								<td><i class="{{$submenu['icon']}}"></i></td>
        								<td class="center">@if($submenu['status'] == 1) 显示 @else 隐藏  @endif</td>
        								<td>{{$submenu['sort']}}</td>
        								<td class="center"><a
        									href="/Sys/Menu/info?id={{$submenu['id']}}">修改</a>&nbsp;<a
        									class="del" href="javascript:;"
        									val="/Sys/Menu/del" title="删除" mid="{{$submenu['id']}}">删除</a></td>
        							</tr>
								@endforeach
							@endforeach
						</tbody>
					</table>
				</form>
				<div class="cf">
					<input type="hidden" value="{{ csrf_token() }}" name="_token" />
					<a class="btn btn-info" href="/Sys/Menu/add" value="">新增</a>
					<input id="submit" class="btn btn-info" type="button" value="删除">
				</div>
			</div>
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

	$("#submit").click(function(){
		var mids = "";
		$('input[type="checkbox"][name="mids"]:checked').each(function(){
			mids += $(this).val() + "#";
		})
		delNode(mids);
	})
	
	$(".del").click(function(){

		var mids = $(this).attr('mid');
		delNode(mids);
	})
	function delNode(mids){
		if(!mids){
			return false;
		}
		var _token = $("input[name='_token']").val();
		$.ajax({
            cache: false,
            type: "POST",
            url:"{{route('menu.del')}}",
            data:{
            	'mids':mids,
            },
            headers: {
                'X-CSRF-TOKEN': _token
            },
            dataType: 'json',
            success: function(res) {
            	if(res.status){
            		layer.msg(res.message, {icon: 1})
            		setTimeout(function(){
						 window.location.reload();
					 }, 2000);
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
