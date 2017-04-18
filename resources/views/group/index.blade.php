@extends('base') @section('page-content')
<div class="page-content">
    <!-- #section:settings.box -->
	<div class="alert alert-block alert-success">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>
		<!--i class="ace-icon fa fa-check green"></i-->
		友情提示：请慎重操作用户组权限
	</div>
	@include('public/setbox')
	<div class="row">
		<div class="col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<form id="export-form" method="post"
				action="#">
				<table class="table table-striped table-bordered table-hover">
					<thead>
						<tr>
							<th class="center"></th>
							<th>用户组</th>
							<th>状态</th>
							<th class="center">操作</th>
						</tr>
					</thead>
					<tbody>
						@foreach($group as $key=>$value)
							<tr gid="{{$value['id']}}" groupname="{{$value['groupname']}}">
    							<td class="center">{{$key+1}}</td>
    							<td>{{$value['groupname']}}</td>
    							<td>
    								@if($value['id']!=1)
        								@if($value['status']==2) 
        								<a href="javascript:void(0);" type=1>启用</a> 
        								@else 
        								<a href="javascript:void(0);" type=2>禁用</a>
        								@endif
    								@endif
    							</td>
    							<td class="center">
    								@if($value['id']!=1)
    								<a href="{{route('group.add')}}?gid={{$value['id']}}">修改</a>
    								<a href="javascript:void(0);" type=3>删除</a>
    								@endif
    							</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				<input type="hidden" value="{{ csrf_token() }}" name="_token" />
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

	$('table').on('click', 'a', function(){
		var gid = $(this).parents('tr').attr('gid');
		var groupname = $(this).parents('tr').attr('groupname');
		var type = $(this).attr('type');
		var _token = $("input[name='_token']").val();
		if(type==1){
			info = '启用'+groupname+'后<br/>将导致关联的用户获得操作权限!';
		}else if(type==2){
			info = '禁用'+groupname+'后<br/>将导致关联的用户失去操作权限!';
		}else if(type==3){
			info = '需删除'+groupname+'关联的所有用户后，<br/>才能删除该用户组!';
		}
		layer.confirm(info, {
			  btn: ['确定','取消']
		}, function(){
			operate(type, gid, _token)
		});
	})
	/**
     * 用户组操作
     * @param type 操作类型
     * @param id 用户组id
     * @param _token 用户token
     */
	function operate(type, gid, _token){

		$.ajax({
            cache: false,
            type: "POST",
            url:"{{route('group.operate')}}",
            data:{
                'type':type,
                'gid':gid,
            },
            headers: {
                'X-CSRF-TOKEN': _token
            },
            dataType: 'json',
            success: function(res) {
            	if(res.status==200){
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
