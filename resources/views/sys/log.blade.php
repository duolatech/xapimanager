@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/table/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/table/footable.core.css')}}">
<link rel="stylesheet" href="{{URL::asset('js/pagination/pagination.css')}}" />
<link rel="stylesheet" href="{{URL::asset('js/Ecalendar/Ecalendar.css')}}" />
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
								<label value="username">用户名：</label>
                            </div>
                            <div class="form-group m-r-sm">
                              <input type="text" name="username" class="form-control">
                            </div>
                            <div class="form-group">
								<label value="date">日期：</label>
                            </div>
                            <div class="form-group">
                              <input type="text" name="startDate" class="form-control"> -
                              <input type="text" name="endDate" class="form-control">
                            </div>
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                            <a href="javascript:void(0);"  class="btn btn-default search">搜索</a>
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
										项目
									</th>
									<th class="footable-visible footable-sortable">环境</th>
									<th class="footable-sortable">操作人</th>
									<th class="footable-sortable">描述</th>
									<th class="footable-sortable">时间</th>
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
			<script type="text/javascript" src="{{URL::asset('js/Ecalendar/Ecalendar.min.js')}}"></script>
			<script type="text/javascript" src="{{URL::asset('js/pagination/jquery.pagination.min.js')}}"></script>
			<script type="text/javascript" charset="utf-8">
				ajaxUser({page:1},1);
    			//分页
    			function pagination(){
    				var username = $('input[name="username"]').val();
    				var startDate = $("input[name='startDate']").val();
    				var endDate = $("input[name='endDate']").val();
    				$('.M-box').pagination({
    				    pageCount: pageCount,
    				    jump: true,
    				    callback:function(api){
    				        var data = {
    				            page: api.getCurrent(),
    				            username : username,
    				            startDate : startDate,
    				            endDate : endDate,
    				        };
    				        ajaxUser(data,0);
    				    }
    				});
    			}
    			
    			//获取接口列表信息, 第一页加载分页插件
    			function ajaxUser(data, first){
    
    				var _token = $("input[name='_token']").val();
    				$.ajax({
    		            cache: false,
    		            type: "get",
    		            url:"{{route('ajaxLog')}}",
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
    				var step = 0;
    				if(res){
    					$("tbody").html(element);
    					$.each(res,function(i, item){
    						
    						var footable = (step%2==0) ? 'footable-even' : 'footable-odd';
    						element +=  '<tr class="'+footable+'" style="display: table-row;" >';
							element +=  '	<td class="footable-visible">'+item.proname+'</a></td>';
							element +=  '	<td class="footable-visible">'+item.envname+'</td>';
							element +=  '	<td class="footable-visible">'+item.username+'</td>';
							element +=  '	<td class="footable-visible">'+item.desc+'</td>';
							element +=  '	<td class="footable-visible">'+item.time+'</td>';
							element +=  '	<td class="footable-visible" >';
							element +=  '		<a href="/Sys/log/detail?id='+item.id+'">查看</a>&nbsp;';
							element +=  '	</td>';
							element +=  '</tr>';
							step++;
    					});
    					$("tbody").html(element);
    				}
    			}
    			//搜索接口
            	$('.search').click(function(){
            		var username = $('input[name="username"]').val();
    				var startDate = $("input[name='startDate']").val();
    				var endDate = $("input[name='endDate']").val();
    				ajaxUser({
    					username : username,
			            startDate : startDate,
			            endDate : endDate,
        			},1);
            	})
            	$("input[name='startDate']").ECalendar({
        			 type:"time",   //模式，time: 带时间选择; date: 不带时间选择;
        			 stamp : false,   //是否转成时间戳，默认true;
        			 offset:[0,2],   //弹框手动偏移量;
        			 format:"yyyy年mm月dd日 hh:ii",   //时间格式 默认 yyyy-mm-dd hh:ii;
        			 skin:3,   //皮肤颜色，默认随机，可选值：0-8,或者直接标注颜色值;
        			 step:10,   //选择时间分钟的精确度;
        			 callback:function(v,e){} //回调函数
        		});
            	$("input[name='endDate']").ECalendar({
          			 type:"time",   //模式，time: 带时间选择; date: 不带时间选择;
          			 stamp : false,   //是否转成时间戳，默认true;
          			 offset:[0,2],   //弹框手动偏移量;
          			 format:"yyyy年mm月dd日 hh:ii",   //时间格式 默认 yyyy-mm-dd hh:ii;
          			 skin:3,   //皮肤颜色，默认随机，可选值：0-8,或者直接标注颜色值;
          			 step:10,   //选择时间分钟的精确度;
          			 callback:function(v,e){} //回调函数
          		});
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
