@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/table/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/table/footable.core.css')}}">
<link rel="stylesheet" href="{{URL::asset('js/pagination/pagination.css')}}" />

<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
			<!-- content -->
			<div class="wrapper-md ng-scope">
				<div class="panel panel-default">
					<div class="panel-heading">权限组</div>
					<div>
						<table
							class="table m-b-none footable-loaded footable tablet breakpoint"
							ui-jq="footable" data-filter="#filter" data-page-size="5">
							<thead>
								<tr>
									<th class="footable-sortable">序号 </th>
									<th data-toggle="true"
										class="footable-visible footable-sortable footable-first-column">
										权限组名
									</th>
									<th class="footable-visible footable-sortable">权限</th>
									<th class="footable-sortable">状态 </th>
									<th class="footable-visible footable-sortable footable-last-column">操作</th>
								</tr>
							</thead>
							<tbody>
								@foreach($group as $key=>$value)
									
    								<tr class="@if($key%2==0) footable-even @else footable-odd @endif" 
    									style="display: table-row;" gid="{{$value['id']}}" groupname="{{$value['groupname']}}">
    									<td class="footable-visible">{{$key+1}}</td>
    									<td class="footable-visible footable-first-column">{{$value['groupname']}}</td>
    									<td class="footable-visible">
    										<a href="/Group/featureAuth?id={{$value['id'] or ''}}" class="label btn-primary" title="Active">功能权限</a>
    									</td>
    									<td class="footable-visible" >
    										@if($value['id']!=1)
                								@if($value['status']==2) 
                								<a href="javascript:void(0);" type=1 class="label operate btn-warning">启用</a> 
                								@else 
                								<a href="javascript:void(0);" type=2 class="label operate bg-success">禁用</a>
                								@endif
            								@endif
    									</td>
    									<td class="footable-visible footable-last-column">
    										@if($value['id']!=1)
                								<a href="{{route('group.info')}}?gid={{$value['id']}}">修改</a>
                								<a href="javascript:void(0);" type=3 class="operate">删除</a>
                							@endif
    									</td>
    								</tr>
								@endforeach
							</tbody>
						</table>
						<input type="hidden" value="{{ csrf_token() }}" name="_token" />
					</div>
				</div>
			</div>
			<!-- /content -->
			<script type="text/javascript" src="{{URL::asset('js/pagination/jquery.pagination.min.js')}}"></script>
			<script type="text/javascript" charset="utf-8">
				$(function(){
        			$('table').on('click', '.operate', function(){
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
        		            		layer.msg(res.message);
        		            		setTimeout(function(){
        								 window.location.reload();
        							 }, 2000);
        		            	}else{
        		            		layer.msg(res.message);
        		            	}
        		            },
        		            error: function(request) {
        		                layer.msg("网络错误，请稍后重试");
        		            },
        		        });
        			}
        				
        		})
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
