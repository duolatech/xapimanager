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
					<div class="panel-heading">公司密钥列表</div>
					<div class="panel-body b-b b-light">
						公司名称: <input name="company" type="text"
							class="input-sm form-control w-sm inline v-middle">
							<input type="hidden" value="{{ csrf_token() }}" name="_token" />
                                <button class="btn btn-sm btn-default search">搜索</button> 
					</div>
					<div>
						<table
							class="table m-b-none footable-loaded footable tablet breakpoint"
							ui-jq="footable" data-filter="#filter" data-page-size="5">
							<thead>
								<tr>
									<th data-toggle="true"
										class="footable-visible footable-sortable footable-first-column">
										公司名称
									</th>
									<th class="footable-visible footable-sortable">appId</th>
									<th class="footable-sortable">appSecret </th>
									<th class="footable-visible footable-sortable footable-last-column">状态</th>
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
			ajaxApiList({
				page:1,
			},1);
			//分页
			function pagination(pageCount){

				var company = $('input[name="company"]').val();
				$('.M-box').pagination({
					pageCount: pageCount,
				    jump: true,
				    callback:function(api){
				        var data = {
				            page: api.getCurrent(),
				            company: company
				        };
				        ajaxApiList(data, 0);
				    }
				});
			}
			
			//获取接口列表信息, 第一页加载分页插件
			function ajaxApiList(data, first){

				layer.load(0, {shade: false});
				var _token = $("input[name='_token']").val();
				$.ajax({
		            cache: false,
		            type: "GET",
		            url:"{{route('secret.ajaxList')}}",
		            data: data,
		            dataType: 'json',
		            success: function(res) {
		            	layer.closeAll();
		            	if(res.status==200){
		            		render(res.data.list);
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
						var footable = (step%2==0) ? 'footable-even' : 'footable-odd';
						element +=  '<tr class="'+footable+'" style="display: table-row;" >';
						element +=  '	<td class="footable-visible footable-first-column">'+item.company+'</td>';
						element +=  '	<td class="footable-visible"><a href="">'+item.appId+'</a></td>';
						element +=  '	<td class="" >'+item.appSecret+'</td>';
						element +=  '	<td class="footable-visible footable-last-column">';
						if(item.status==1){
							element +=  '		<span class="label bg-success" >正常</span>';
						}else if(item.status==2){
							element +=  '	 	<span class="label bg-warning" >冻结</span>';
						}
						element +=  '	</td>';
						element +=  '	<td class="footable-visible" >';
						element +=  	'<a href="/Company/secret/info?id='+item.id+'">编辑</a>&nbsp;';
						element +=  	'<a href="javascript:void(0)" class="operate" id='+item.id+' company='+item.company+'>删除</a>&nbsp;';
						element +=  '	</td>';
						element +=  '</tr>';
						step++;
					});
					$("tbody").html(element);
				}
			}
			//搜索接口
        	$('.search').click(function(){
            	var company = $('input[name="company"]').val();
            	ajaxApiList({ 
                	page:1, 
                	company:company,
                }, 1);
        	})
        	//操作
			$('table').on('click', '.operate', function(){

				var id = $(this).attr('id');
				var company = $(this).attr('company');

				layer.confirm('确认删除 '+company+'的公司密钥？', {
					  btn: ['确定','取消']
				}, function(){
					operate(id, company);
				});
			})
        	//删除公司密钥信息
        	function operate(id, company){
        
        		var _token = $("input[name='_token']").val();
        		$.ajax({
                    cache: false,
                    type: "POST",
                    url:"{{route('secret.operate')}}",
                    data: {'id':id, 'company':company},
                    headers: {
                        'X-CSRF-TOKEN': _token
                    },
                    dataType: 'json',
                    success: function(res) {
        
                    	layer.closeAll();
                    	if(res.status==200){
                    		layer.msg(res.message)
                    		setTimeout(function(){
        						 window.location.reload();
        					 }, 2000);
                        }else{
                        	layer.msg(res.message)
                         }
                    },
                    error: function(request) {
                        layer.msg("网络错误，请稍后重试");
                    },
                });
        	}
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
