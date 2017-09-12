@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/table/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/table/footable.core.css')}}">
<link rel="stylesheet" href="{{URL::asset('js/pagination/pagination.css')}}" />

<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
		<!-- main -->
		<div class="col">
			<!-- content -->
			<div class="wrapper-md ng-scope">
				<div class="panel panel-default">
					<div class="panel-heading">用户列表</div>
					<div class="panel-body b-b b-light">
						<form class="form-inline ng-pristine ng-valid" role="form">
							<div class="form-group">
                            <select name="field" class="form-control">
								<option value="username">用户名</option>
								<option value="phone">手机</option>
								<option value="email">邮箱</option>
							</select>
                            </div>
                            <div class="form-group">
                              <input type="text" name="keyword" class="form-control">
                            </div>
                            <div class="form-group">
                            <select name="group_id" class="form-control">
								<option value="0">全部</option>
        						@foreach($list['group'] as $value)
        							<option value="{{$value['id']}}" >{{$value['groupname']}}</option>
        						@endforeach
							</select>
                            </div>
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                            <a href="javascript:void(0);"  class="btn btn-default search">搜索</a>
                            <a href="/User/info" class="btn btn-info">新增用户</a>
                            <div class="m-t-xs">注意：新注册的用户选择所属分组保存后，即可完成激活</div>
                          </form>
					</div>
					<div>
						<table
							class="table m-b-none footable-loaded footable tablet breakpoint"
							ui-jq="footable" data-filter="#filter" data-page-size="5">
							<thead>
								<tr>
									<th data-toggle="true"
										class="footable-visible footable-sortable footable-first-column">
										用户名
									</th>
									<th class="footable-visible footable-sortable">用户组</th>
									<th class="footable-sortable">性别</th>
									<th class="footable-sortable">电话</th>
									<th class="footable-sortable">邮箱</th>
									<th class="footable-visible footable-sortable footable-last-column">在职状态</th>
									<th class="footable-sortable">操作</th>
								</tr>
							</thead>
							<tbody>
								
							</tbody>
							<tfoot class="hide-if-no-paging">
								<tr>
									<td colspan="5" class="text-center footable-visible">
										<div class="M-box"></div>
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
				</div>
			</div>
			<!-- /content -->
			<script type="text/javascript" src="{{URL::asset('js/pagination/jquery.pagination.min.js')}}"></script>
			<script type="text/javascript" charset="utf-8">
				ajaxUser({page:1},1);
    			//分页
    			function pagination(){
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
    			
    			//获取接口列表信息, 第一页加载分页插件
    			function ajaxUser(data, first){
    				layer.load(0, {shade: false});
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
    		            	layer.closeAll();
    		            	if(res.status==200){
    		            		render(res.data.info);
    		            		pageCount = res.data.pageCount;
    		            		if(first==1){
    		            			pagination(pageCount);
    		                	}
    		                }
    		            },
    		            error: function(request) {
    		            	layer.closeAll();
    		                layer.msg("网络错误，请稍后重试");
    		            },
    		        });
    
    			}
    			//渲染页面
    			function render(res){
    
    				var element = "";
    				var step = 0;
    				if(res){
    					$("tbody").html(element);
    					$.each(res,function(i, item){
        					item.operate = '修改';
    						if(item.status==1){
    							item.jobStatus = '<span class="label bg-success">在职</span>';
    						}else if(item.status==2){
    							item.jobStatus = '<span class="label bg-light">离职</span>';
    						}else{
    							item.operate = '激活'
    							item.jobStatus = '<span class="label bg-danger">待激活</span>';
        					}
    						var footable = (step%2==0) ? 'footable-even' : 'footable-odd';
    						element +=  '<tr class="'+footable+'" style="display: table-row;" >';
							element +=  '	<td class="footable-visible">'+item.username+'</a></td>';
							element +=  '	<td class="footable-visible">'+item.groupname+'</td>';
							element +=  '	<td class="footable-visible">'+item.sex+'</td>';
							element +=  '	<td class="footable-visible">'+item.phone+'</td>';
							element +=  '	<td class="footable-visible">'+item.email+'</td>';
							element +=  '	<td class="footable-visible">'+item.jobStatus+'</td>';
							element +=  '	<td class="footable-visible" >';
							element +=  	'<a href="/User/detail?uid='+item.uid+'">查看</a>&nbsp;';
							element +=  	'<a href="/User/info?uid='+item.uid+'">'+item.operate+'</a>&nbsp;';
							element +=  '	</td>';
							element +=  '</tr>';
							step++;
    					});
    					$("tbody").html(element);
    				}
    			}
    			//搜索接口
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
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
