@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/table/dataTables.bootstrap.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/table/footable.core.css')}}">
<link rel="stylesheet" href="{{URL::asset('js/pagination/pagination.css')}}" />

<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
		
			<!-- content -->
			<div class="wrapper-md ng-scope">
				<div class="panel panel-default">
					<div class="panel-heading">Api 列表</div>
    					<div class="pinyin m-t-sm">
							<a class="btn btn-sm btn-primary m-l-xs classifyALL">ALL</a>
        					@foreach($list['letter'] as $value)
        						<a class="btn btn-sm btn-primary m-l-xs classifyLetter">{{$value}}</a>
        					@endforeach
    					</div>
    					<div class="line line-dashed b-b pull-in"></div>
    					<p id="classifyGather">
    						@if(!empty($list['gather']))
        						@foreach(current($list['gather']) as $value)
        							<a class="btn btn-sm btn-default btn-rounded subClassify" subid={{$value['id'] or ''}}>{{$value['classifyname'] or ''}}</a>
        						@endforeach
    						@endif
    						@foreach($list['gather'] as $key=>$gather)
    							@foreach($gather as $vol)
    								<a class="btn btn-sm btn-default btn-rounded sub{{$key}} subClassify" subid={{$vol['id'] or ''}} style="display: none;">{{$vol['classifyname'] or ''}}</a>
    							@endforeach
    						@endforeach
    					</p>
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
						<input type="hidden" value="{{ csrf_token() }}" name="_token" />
					</div>
				</div>
			</div>
			<!-- /content -->
			<script type="text/javascript" src="{{URL::asset('js/pagination/jquery.pagination.min.js')}}"></script>
			<script type="text/javascript" charset="utf-8">
				$(function() {
					var subcon = {};
					//查询条件
					var con = {
						'type' : getQueryString('type'),
						'apiname' : getQueryString('apiname'),
						'URI' : getQueryString('URI'),
						'author' : getQueryString('author'),
						'classify' : getQueryString('classify'),
						'subClassify' : getQueryString('subClassify'),
						'proid':getQueryString('proid'),
					}
					//字母分类切换
					$('.classifyLetter').on({
						mouseover : function(){  
							var element = ''
							var letter = $(this).text();
							$('#classifyGather a').hide();
							$(".sub"+letter).show(); 
						} ,
					})
					//子分类选择
					$("#classifyGather").on('click', '.subClassify', function(){
						var subid = $(this).attr('subid');
						$('#classifyGather a').removeClass('btn-info');
						$(this).removeClass('btn-default').addClass('btn-info');
						subcon = {
							'subClassify' : subid
						}
						var data = $.extend({page:1}, subcon);
						ajaxApiList(data,1);
					})
					//全部分类
					$(".pinyin").on('click', '.classifyALL', function(){
						//第一页数据
						var data = $.extend({page:1}, con);
						ajaxApiList(data,1);
					})
					//第一页数据
					var data = $.extend({page:1}, con);
					ajaxApiList(data,1);
					
					//分页
					function pagination(pageCount){
						$('.M-box').pagination({
							pageCount: pageCount,
							jump: true,
							callback:function(api){
								var data = {
									page: api.getCurrent()
								};
								if(subcon.subClassify){
									data = $.extend(data, map, subcon);
								}else{
									data = $.extend(data, map, con);
								}
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
					/* //获取get参数
					function getQueryString(name) { 
						var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "iu");
						var r = window.location.search.substr(1).match(reg);
						if (r != null)
							return decodeURI(r[2]);
						return null;
					} */
					//渲染页面
					function render(res){

						var element = "";
						var step = 0;
						if(res){
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
									element +=  '	<td class="footable-visible"><a href="/Api/detail?did='+sub.id+'" target="_blank">'+sub.version+'</a></td>';
									element +=  '	<td class="footable-visible" >【'+sub.type+"】"+sub.URI+'</td>';
									element +=  '	<td class="footable-visible">'+sub.username+'</td>';
									element +=  '	<td class="footable-visible">'+sub.ctime+'</td>';
									element +=  '	<td class="footable-visible footable-last-column">';
									element +=  '		<span class="label '+bg_status+'">'+sub.apistatus+'</span>';
									element +=  '	</td>';
									element +=  '	<td class="footable-visible" >';
									element +=  '		<a href="/Api/info?version_type=add&lid='+sub.listid+'" target="_blank">添加版本</a>&nbsp;';
									element +=  '		<a href="/Api/detail?did='+sub.id+'" target="_blank">查看</a>&nbsp;';
									if(sub.status==1 || sub.status==2){
										element +=  	'<a href="javascript:void(0)" id='+sub.id+' class="discard">废弃</a>&nbsp;';
									}
									element +=  '	</td>';
									element +=  '</tr>';
									step++;
								});
							});
							$("tbody").html(element);
						}
					}
					//废弃接口
					$("table").on("click", ".discard", function(){
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
		</div>
	</div>
<!-- /.page-content -->
@endsection
