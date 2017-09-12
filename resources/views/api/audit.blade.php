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
					<div class="panel-heading">待审核接口列表</div>
					<div class="panel-body b-b b-light">
						<form class="form-inline ng-pristine ng-valid" role="form">
                            <div class="form-group">
                              <input type="text" name="apiname" class="form-control" id="exampleInputEmail2" placeholder="Api 名称">
                            </div>
                            <div class="form-group">
                              <input type="text" name="author" class="form-control" id="exampleInputPassword2" placeholder="维护人">
                            </div>
                            <div class="form-group">
                            <select name="auditStatus" class="form-control">
								<option value="0">全部</option>
								<option value="2">待审核</option>
								<option value="5">已拒绝</option>
							</select>
                            </div>
                            <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                            <a href="javascript:void(0);" class="btn btn-default search">搜索</a>
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
										Api 名称
									</th>
									<th class="footable-visible footable-sortable">版本</th>
									<th class="footable-sortable">URI</th>
									<th class="footable-sortable">维护人</th>
									<th class="footable-sortable">时间</th>
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

    			var env = $.cookie('env');
    			ajaxApiList({
    				type:'search', 
    				page:1, 
    				envid:env, 
    				status: '2,5'
    			},1);
    			//分页
    			function pagination(){
    
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
    					            author: $("input[name='author']").val(),
    					            status: status
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
    		            url:"{{route('Api.ajaxList')}}",
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
    						var rowspan = item.info.length;
    						$.each(item.info,function(key, sub){
    							if(sub.status==1) bg_status = 'bg-success';
    							if(sub.status==2) bg_status = 'bg-info';
    							if(sub.status==3) bg_status = 'bg-warning';
    							if(sub.status==5) bg_status = 'bg-light';
    							var footable = (step%2==0) ? 'footable-even' : 'footable-odd';
    							element +=  '<tr class="'+footable+'" style="display: table-row;" >';
    							if(key==0){
    								element +=  '	<td class="footable-visible footable-first-column v-middle" rowspan='+rowspan+'>'+item.apiname+'</td>';
    							}
    							element +=  '	<td class="footable-visible"><a href="/Api/detail?did='+sub.id+'">'+sub.version+'</a></td>';
    							element +=  '	<td class="footable-visible" >【'+sub.type+"】"+sub.URI+'</td>';
    							element +=  '	<td class="footable-visible">'+sub.username+'</td>';
    							element +=  '	<td class="footable-visible">'+sub.ctime+'</td>';
    							element +=  '	<td class="footable-visible footable-last-column">';
    							element +=  '		<span class="label '+bg_status+'">'+sub.apistatus+'</span>';
    							element +=  '	</td>';
    							element +=  '	<td class="footable-visible" >';
    							element +=  	'<a href="javascript:void(0)" class="operate" did='+sub.id+' uri='+sub.URI+' status=1 >通过</a>&nbsp;';
    							if(sub.status==2){
    								element +=  	'<a href="javascript:void(0)" class="operate" did='+sub.id+' uri='+sub.URI+' status=2 >拒绝</a>&nbsp;';
    							}
    							element +=  '	</td>';
    							element +=  '</tr>';
    							step++;
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
    				var content = '';
    
    				if(status==1){
    					layer.confirm('确认接口: '+uri+'通过审核？', {
    						  btn: ['确定','取消']
    					}, function(){
    						operate(did, status, '审核通过');
    					});
    				}else if(status==2){
    					content += '<div class="form-group m">';
    					content += '	<label class="control-label">拒绝原因:</label>';
    					content += '	<div class="">';
    					content += '		<textarea name="reject" class="form-control" rows="5"></textarea>';
    					content += '	</div>';
    					content += '</div>';
    					layer.open({
    						  type: 1,
    						  skin: 'layui-layer-rim',
    						  area: ['420px', '280px'],
    						  content: content,
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
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
