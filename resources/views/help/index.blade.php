@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('js/pagination/pagination.css')}}" />
<div
	class="app-content-body app-content-full fade-in-up ng-scope h-full"
	ng-class="{'h-full': app.hideFooter }" ui-view="">
	<!-- hbox layout -->
	<div class="hbox hbox-auto-xs bg-light  ng-scope">
		<!-- column -->
		<div class="col w-lg lt b-r">
			<div class="vbox">
				<div class="wrapper">
					<a href="/Help/info" class="pull-right btn btn-sm btn-info m-t-n-xs" >添加</a>
					<div class="h4">帮助中心</div>
				</div>
				<div class="row-row">
					<div class="cell scrollable hover">
						<div class="cell-inner">
							<div class="padder">
								<div class="list-group help-title">
									
								</div>
								<div class="list-group">
									<div class="M-box"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /column -->

		<!-- column -->
		<div class="col">
			<div class="vbox">
				<div class="wrapper bg-light lt b-b overview">
					
				</div>
				<div class="row-row">
					<div class="cell">
						<div class="cell-inner">
							<div
								class="form-control help-content no-radius no-border no-bg wrapper-lg text-md ng-pristine ng-valid ng-touched"
								style="height: 100%;"> 
								           
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<input type="hidden" value="{{ csrf_token() }}" name="_token" />
		<!-- /column -->
	</div>
	<!-- /hbox layout -->
	<script type="text/javascript" src="{{URL::asset('js/pagination/jquery.pagination.min.js')}}"></script>
			<script type="text/javascript" charset="utf-8">

				var helpInfo;
				ajaxHelp({page:1},1);
    			//分页
    			function pagination(){
    				$('.M-box').pagination({
    				    pageCount: pageCount,
    				    jump: true,
    				    callback:function(api){
    				        var data = {
    				            page: api.getCurrent(),
    				        };
    				        ajaxHelp(data,0);
    				    }
    				});
    			}
    			
    			//获取接口列表信息, 第一页加载分页插件
    			function ajaxHelp(data, first){
    
    				$.ajax({
    		            cache: false,
    		            type: "get",
    		            url:"{{route('ajaxHelp')}}",
    		            data: data,
    		            headers: {
    		                'X-CSRF-TOKEN': ''
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
    					helpInfo = res;
    					$(".help-title").html(element);
    					//插入内容
    					if(res[0]){
    						$(".help-author").html(res[0]['username']+': ');
        					$(".help-content-title").html(res[0]['title']);
        					$(".help-content").html(res[0]['content']);
        				}
    					$.each(res,function(i, item){
    						var flag = (step%2==0) ? 'b-l-success' : 'b-l-primary';
    						element +=  '<a class="list-group-item '+flag+' b-l-3x hover-anchor hover help-tile-link" help-i="'+i+'" > ';
    						element +=  '	<span class="pull-right text-muted hover-action"></span>';
    						element +=  '	<span class="block text-ellipsis ng-binding">'+item.title+'</span> ';
    						element +=  '	<small class="text-muted ng-binding">'+item.time+'</small>';
    						element +=  '</a>';
    						step++;
    					});
    					$(".help-title").html(element);
    				}
    			}
    			//内容标题、操作
    			$(".app-content-body").on('click', '.help-tile-link', function(){
					var i = $(this).attr('help-i');
					var element = "";
						element +=  '<span class="text-muted help-author">'+helpInfo[i]["username"]+'：</span>';
						element +=  '<span class="ng-binding help-content-title">'+helpInfo[i]["title"]+'</span>';
						element +=  '<a href="javascript:;" class="delHelp" helpname="'+helpInfo[i]["title"]+'" id="'+helpInfo[i]["id"]+'">';
						element +=  '	<i class="ace-icon fa fa-trash-o pull-right bigger-120 grey m-r-sm "></i>';
						element +=  '</a>';
						element +=  '<a href="/Help/info?id='+helpInfo[i]["id"]+'"><i class="icon-pencil text-success m-r pull-right"></i></a>';
					$(".overview").html(element);
					$(".help-content").html(helpInfo[i]['content']);
        		});
        		//删除帮助
        		$(".app-content-body").on('click', '.delHelp', function(){
            		var helpname = $(this).attr('helpname');
            		var id = $(this).attr('id');
        			layer.confirm('确认删除 '+helpname+'？', {
    					  btn: ['确定','取消']
    				}, function(){
    					$.ajax({
        		            cache: false,
        		            type: "post",
        		            url:"{{route('help.del')}}",
        		            data: {'id':id},
        		            headers: {
        		                'X-CSRF-TOKEN': $("input[name='_token']").val()
        		            },
        		            dataType: 'json',
        		            success: function(res) {
        		            	if(res.status==200){
        		            		layer.msg(res.message);
			                		setTimeout(function(){
										 window.location.href = "{{route('help.index')}}";;
									 }, 2000);
        		                }
        		            },
        		            error: function(request) {
        		                layer.msg("网络错误，请稍后重试");
        		            },
        		        });
    				});
            	})
			</script>
</div>
<!-- /.page-content -->
@endsection
