@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/nestable.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/chosen.css')}}">
<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
			<!-- content -->
			<form id="myForm" method="post"
							class="form-horizontal ng-pristine ng-valid ng-valid-date ng-valid-required ng-valid-parse ng-valid-date-disabled">
			<div class="wrapper-md ng-scope">
				<div class="row">
					<div class="col-sm-4">
						<div class="list-group">
							<a href="javascript:;" class="list-group-item"> <span
								class="badge badge-empty">4项</span> 数据权限
							</a>
							 <a href="javascript:;" class="list-group-item data-item" type="1" name="项目选择"> <i
								class="fa fa-chevron-right text-muted"></i> <span
								class="badge bg-success">{{count($data['project'])}}个项目</span> <i
								class="glyphicon glyphicon-tags fa-fw m-r-xs"></i> 项目选择
							</a> 
							<a href="javascript:;" class="list-group-item data-item" type="2" name="分类接口"> <i
								class="fa fa-chevron-right text-muted"></i> <span
								class="badge bg-info">{{$data['cateNum']['father'] or 0}}大类{{$data['cateNum']['son'] or 0}}子类</span> <i
								class="glyphicon glyphicon-th-large icon text-successfa-fw m-r-xs"></i>
								分类接口
							</a> 
							<a href="javascript:;" class="list-group-item data-item" type="3" name="用户选择"> <i
								class="fa fa-chevron-right text-muted"></i> <span
								class="badge bg-warning">{{$data['userNum'] or 0}}人</span> <i
								class="icon-users icon text-muted fa-fw m-r-xs"></i> 用户选择
							</a> 
							<a href="javascript:;" class="list-group-item data-item" type="4" name="公司密钥"> <i
								class="fa fa-chevron-right text-muted"></i> <span
								class="badge bg-danger">{{$data['secret'] or 0}}个</span> <i
								class="glyphicon glyphicon-link icon text-danger fa-fw m-r-xs"></i>
								公司密钥
							</a>
						</div>
						<div class="panel panel-default suboProject" style="display: none;">
                        	<div class="panel-heading font-bold">所属项目</div>
                        	<div class="panel-body">
                        		<div class="form-group">
                        			<div class="m-l-md">	
                        				<ol class="dd-list">
                        					<li class="dd-item" data-id="5">
                        						
                        					</li>
                        				</ol>
                        			</div>
                        		</div>
                        	</div>
                        </div>
						<div class="panel panel-default suboClassify" style="display: none;">
							<div class="panel-heading font-bold">所属分类</div>
							<div class="panel-body">
								<div class="form-group">
									<div>
										<ol class="dd-list dd-info">
											
										</ol>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-8">
						<div class="panel panel-default data-range">
							<div class="panel-heading font-bold">数据选择</div>
							<div class="panel-body">
								<p class="text-muted">请为该管理组选择允许查看的『<span class="infoName text-info">分类接口</span>』范围</p>
								<div class="form-group-classify" style="display:none;">
									<select name="env" class="pull-left m-r-sm">
										@foreach($sys['ApiEnv'] as $env)
											<option value="{{$env['id']}}">{{$env['envname']}}</option>
										@endforeach			
									</select>
									<div class="selectList pull-left">
										<select name="oneClassify" class="fatherClassify">
											
										</select>
										<select name="twoClassify" class="subClassify">
											
										</select>
									</div>
    							</div>
    							<div class="line line-dashed b-b line-lg pull-in"></div>
								<div>
									<table
										class="table m-b-none footable-loaded footable tablet breakpoint">
										<thead>
											<tr>
												<th data-toggle="true"
													class="footable-sortable">
													<label class="i-checks"> 
													<input type="checkbox" value="option3"><i></i></label>
												</th>
												<th class="footable-sortable">appId</th>
												<th class="footable-sortable">appSecret</th>
												<th class="footable-sortable">状态</th>
												
											</tr>
										</thead>
										<tbody>
											<tr class="footable-even" style="display: table-row;">
												<td class="footable-visible footable-first-column">
													<label class="i-checks"> <input type="checkbox" value="option3"><i></i></label>
												</td>
												<td class="footable-visible"><a href="">Boudreaux</a></td>
												<td class="">Traffic Court Referee</td>
												<td class="footable-visible footable-last-column"><span
													class="label bg-success">Active</span></td>
											</tr>
											<tr class="footable-odd" style="display: table-row;">
												<td class="footable-visible footable-first-column">
													<label class="i-checks"> <input type="checkbox" value="option3"><i></i></label>
												</td>
												<td class="footable-visible">Woldt</td>
												<td class=""><a href="">Airline Transport Pilot</a></td>
												<td data-value="2"
													class="footable-visible footable-last-column"><span
													class="label bg-light" title="Disabled">Disabled</span></td>
											</tr>
											<tr class="footable-even" style="display: table-row;">
												<td class="footable-visible footable-first-column">
													<label class="i-checks"> <input type="checkbox" value="option3"><i></i></label>
												</td>
												<td class="footable-visible">Leonardo</td>
												<td class="">Business Services Sales Representative</td>
												<td data-value="3"
													class="footable-visible footable-last-column"><span
													class="label bg-warning" title="Suspended">Suspended</span></td>
											</tr>
											<tr class="footable-odd" style="display: table-row;">
												<td class="footable-visible footable-first-column">
													<label class="i-checks"> <input type="checkbox" value="option3"><i></i></label>
												</td>
												<td class="footable-visible">Dragoo</td>
												<td class="">Drywall Stripper</td>
												<td data-value="1"
													class="footable-visible footable-last-column"><span
													class="label bg-success" title="Active">Active</span></td>
											</tr>
											<tr class="footable-even" style="display: table-row;">
												<td class="footable-visible footable-first-column">
													<label class="i-checks"> <input type="checkbox" value="option3"><i></i></label>
												</td>
												<td class="footable-visible">Halladay</td>
												<td class="">Aviation Tactical Readiness Officer</td>
												<td data-value="3"
													class="footable-visible footable-last-column"><span
													class="label bg-warning" title="Suspended">Suspended</span></td>
											</tr>

										</tbody>
										<!-- <tfoot class="hide-if-no-paging">
											<tr>
												<td colspan="5" class="text-center footable-visible">
													<div class="load-more">
														<button class="btn btn-sm btn-default"
															ui-toggle-class="show" target="#moreless">
															<i class="fa fa-plus text"></i> <span class="text">加载更多……</span>
															<i class="fa fa-minus text-active"></i> <span
																class="text-active">Less</span>
														</button>
													</div>
												</td>
											</tr>
										</tfoot> -->
									</table>
								</div>
								<div class="form-group">
                                  <div class="">
                                  	<input type="hidden" value="{{ csrf_token() }}" name="_token" />
                                  	<input type="hidden" name="auth_type" value="0"/>
                                  	<input type="hidden" name="gid" value="{{$data['gid'] or 0}}"/>
                                    <a type="submit" class="btn btn-primary btn-info-submit">保存数据权限</a>
                                  </div>
                                </div>
							</div>
						</div>
					</div>
			</div>
			</form>
			<!-- /content -->
			<script type="text/javascript" charset="utf-8">
				//当前选中分类
				var oClassify= {};
				//所有分类
				var allProject = {};
				//项目选择
				$(".data-item").click(function(){
					var type = $(this).attr('type');
					var name = $(this).attr('name');
					$('.form-group-classify').hide();
					//数据范围名称
					$(".data-range .infoName").text(name);
					layer.load(0, {shade: false});
					$.ajax({
		                cache: false,
		                type: "GET",
		                url:"{{route('group.ajaxDataRange')}}",
		                data:{
			                'gid':{{$data['gid'] or 0}},
			                'type':type,
			                },
		                dataType: 'json',
		                success: function(res) {
		                	layer.closeAll();
		                	if(res.status==200){
			                	$('input[name="auth_type"]').val(res.data.type);
		                		render(res.data);
		                	}
		                	if($.isEmptyObject(res.data.subordinate.project)){
		                		$(".suboProject").hide();
			                }else{
			                	$(".suboProject").show();
				            }
		                	if($.isEmptyObject(res.data.subordinate.classify)){
		                		$(".suboClassify").hide();
			                }else{
			                	$(".suboClassify").show();
				            }
		                },
		                error: function(request) {
		                	layer.closeAll();
		                    layer.msg("网络错误，请稍后重试");
		                },
		            });
				})
				//页面渲染
				function render(item){
					var thead = $(".data-range table thead");
					var tbody = $(".data-range table tbody");
					//表头
					thead.find('tr').html('');
					var element_thead = '<th data-toggle="true"'+
						'class="footable-sortable">'+
						'<label class="i-checks">'+ 
						'<input type="checkbox" class="checks-all"><i></i></label>'+
					'</th>';
					$.each(item.field, function(key, val){
						element_thead += '<th class="footable-sortable">'+val+'</th>';
					});
					thead.find('tr').append(element_thead);
					//表内容
					var element_tbody = '';
					tbody.html('');
					$.each(item.data, function(key, val){
						var checked = val.selected==1 ? 'checked' : '';
						element_tbody += '<tr class="footable-even" style="display: table-row;">';
						element_tbody += '	<td class="footable-visible footable-first-column">';
						element_tbody += '		<label class="i-checks"> <input type="checkbox" name="range[]" class="checks-single" value="'+val.id+'" '+checked+'><i></i></label>';
						element_tbody += '	</td>';
							$.each(val.fieldValue, function(ko, vol){
								element_tbody += '<td class="footable-visible">'+vol+'</td>';
							});
    					element_tbody += '</tr>';
					});
					tbody.append(element_tbody);
					//所属项目、所属分组
					allProject = item.subordinate.project;
					project(allProject, item.type);
				}
				//所属项目
				function project(project, type){
					var suboProject = $(".suboProject li");
					var element = '';
					suboProject.html('');
					if(type==2 || type==4){
						$.each(project, function(key, val){
							var checked = '';
							if(val.selected && val.selected==1){
								classify(val.classify, type);
								checked = 'checked';
							}
							element += '<label class="i-checks">';
							element += '	<input type="radio" name="proid" value="'+val.id+'" '+checked+' xtype='+type+'><i></i>'+val.proname;
							element += '</label>';
						});
					}
					suboProject.append(element);
				}
				//项目切换
				$(".suboProject").on('click', 'input[name="proid"]', function(){
					var proid = $(this).val();
					var type = $(this).attr('xtype');
					if(type==2){
						classify(allProject[proid].classify, type);
					}
					if(type==4){
						var proid = $('input[name="proid"]:checked').val();
						layer.load(0, {shade: false});
						$.ajax({
	    		            cache: false,
	    		            type: "GET",
	    		            url:"{{route('secret.ajaxList')}}",
	    		            data: {
	        		            'proid':proid,
	        		            'page':1,
	        		            'limit':200
	        		        },
	    		            dataType: 'json',
	    		            success: function(res) {
	    		            	layer.closeAll();
	    		            	if(res.status==200){
	    		            		renderCompay(res.data.list);
	    		                }
	    		                
	    		            },
	    		            error: function(request) {
	    		            	layer.closeAll();
	    		                layer.msg("网络错误，请稍后重试");
	    		            },
	    		        });
					}
				})
				//所属分类
				function classify(classify, type){
					var suboClassify = $(".suboClassify .dd-info");
					var element = '';
					suboClassify.html('');
					if(type==2){
						$.each(classify.allClassify, function(ka,val){
							var onecheck = in_array(val.id, classify.classifyIds) ? 'checked' : '';
							element += '<li class="dd-item firstLi" data-id="2">';
							element += '	<div class="dd-handle">';
							element += '		<label class="checkbox i-checks m-l-md m-b-md"> ';
							element += '	    	<input type="checkbox" name="classify[]" class="ng-pristine ng-untouched ng-valid nodeClassify" '+onecheck+' value="'+val.id+'" classifyname="'+val.classifyname+'">';
							element += '			<i></i>'+val.classifyname;
							element += '		</label>';
							element += '	</div>';
							element += '	<ol class="dd-list">';
							element += '		<li class="dd-item" data-id="5">';
							$.each(val.child,function(ko,vol){
								var twocheck = in_array(vol.id, classify.classifyIds) ? 'checked' : '';
								element += '		<label class="i-checks"> <input type="checkbox" name="subClassify['+val.id+'][]" class="subNodeClassify" value="'+vol.id+'" '+twocheck+' classifyname="'+vol.classifyname+'"><i></i> '+vol.classifyname+'</label> ';	
							})
							element += '		</li>';
							element += '	</ol>';
							element += '</li>';
						})
						$(".suboClassify .dd-info").append(element);
						//下拉分类
						$('.form-group-classify').show();
						oClassify= {};
						getCheckedClassify();
						SelectBox(0);
					}
				}
				//选中所属分类
				$(".suboClassify").on("click", 'input[type="checkbox"]', function(){
					//分类选中联动
					var obj = $(this);
					var name = obj.prop('name');
					var checked = obj.is(':checked');
					if(obj.hasClass('nodeClassify')){
						var children = obj.closest('li').find('.subNodeClassify');
						children.each(function(){
							$(this).prop("checked", checked);
						})
					}else{
						var flag = false;
						var children = obj.closest('li').find('.subNodeClassify');
						children.each(function(){
							subchecked = $(this).is(':checked');
							if(subchecked) flag = true;
						})
						var father = obj.closest('.firstLi').find('.nodeClassify');
						father.prop("checked", flag);
					}
					//生成下拉列表
					oClassify= {};
					getCheckedClassify();
					SelectBox(0);
					
				})
				//生成已选中的分类信息对象
				function getCheckedClassify(){
					var nodeClassify = $(".suboClassify").find('.nodeClassify');
					nodeClassify.each(function(key, node){
						var obj = $(this);
						if(obj.is(':checked')){
							i = obj.val();
							//获取子节点选中情况
							var sub = obj.closest('li').find('.subNodeClassify');
							var child = {};
							sub.each(function(ko, subNode){
								var subobj = $(this);
								if(subobj.is(':checked')){
									j = subobj.val();
									child[j] = {
											'id':j,
											'classifyname':subobj.attr('classifyname')

										};
								}
							});
							oClassify[i] = {
										"id":i,
										'classifyname':obj.attr('classifyname'),
										'child':child
									}; 
						}
					})
					return oClassify;
				}
				//生成下拉列表框
				function SelectBox(cid){
					var fatherClassify = $(".selectList .fatherClassify");
					var fatherElement = '<option value="0">请选择</option>';
					var subClassify = $(".selectList .subClassify");
					var subELement = '<option value="0">请选择</option>';
					if(cid>0){
						$.each(oClassify[cid].child, function(ko, vol){
							subELement += '<option value="'+vol.id+'">'+vol.classifyname+'</option>'
						})
					}else{
						$.each(oClassify, function(key, val){
							fatherElement += '<option value="'+val.id+'">'+val.classifyname+'</option>'
						})
						fatherClassify.html(fatherElement);
					}
					subClassify.html(subELement);
				}
				//下拉列表单击
				$(".data-range").on('change', '.fatherClassify', function(){
					var cid = $(this).val();
					SelectBox(cid);
				})
				//选中子分类时请求数据
				$(".data-range").on('change', '.subClassify', function(){
					var subid = $(this).val();
					var env = $('select[name="env"]').val();
					var proid = $('input[name="proid"]:checked').val();
					layer.load(0, {shade: false});
					$.ajax({
    		            cache: false,
    		            type: "GET",
    		            url:"{{route('Api.ajaxList')}}",
    		            data: {
        		            'type':'search',
        		            'proid':proid,
        		            'env':env,
        		            'subClassify':subid,
        		            'page':1,
        		            'limit':500
        		        },
    		            dataType: 'json',
    		            success: function(res) {
    		            	layer.closeAll();
    		            	if(res.status==200){
    		            		renderApi(res.data.list);
    		                }
    		                
    		            },
    		            error: function(request) {
    		            	layer.closeAll();
    		                layer.msg("网络错误，请稍后重试");
    		            },
    		        });
				})
				//数据范围-全选
				$(".data-range").on('click', '.checks-all', function(){
					var checked = $(this).is(':checked');
					var children = $('.checks-single');
					children.each(function(){
						$(this).prop("checked", checked);
					})
				})
				//分类Api页面渲染
				function renderApi(res){
					var tbody = $(".data-range table tbody");
					//表内容
					var element = '';
					var step = 0;
					tbody.html('');
					$.each(res, function(i, item){
						var rowspan = item.info.length;
						$.each(item.info,function(key, sub){
							var checked = '';
							if(sub.status==1) bg_status = 'bg-success';
							if(sub.status==2) bg_status = 'bg-info';
							if(sub.status==3) bg_status = 'bg-warning';
							if(sub.status==5) bg_status = 'bg-light';
							var footable = (step%2==0) ? 'footable-even' : 'footable-odd';
							element +=  '<tr class="'+footable+'" style="display: table-row;" >';
							if(key==0){
								element +=  '	<td class="footable-visible" rowspan='+rowspan+'>';
								element += '		<label class="i-checks"> <input type="checkbox" name="range[]" class="checks-single" value="'+sub.id+'" '+checked+'><i></i></label>';
								element +=  '	</td>';
								element +=  '	<td class="footable-visible footable-first-column v-middle" rowspan='+rowspan+'>'+item.apiname+'</td>';
							}
							element +=  '	<td class="footable-visible"><a href="/Api/detail?did='+sub.id+'">'+sub.version+'</a></td>';
							element +=  '	<td class="footable-visible" >【'+sub.type+"】"+sub.URI+'</td>';
							element +=  '	<td class="footable-visible">'+sub.username+'</td>';
							element +=  '	<td class="footable-visible footable-last-column">';
							element +=  '		<span class="label '+bg_status+'">'+sub.apistatus+'</span>';
							element +=  '	</td>';
							element +=  '</tr>';
							step++;
						});
					});
					tbody.append(element);
				}
				//公司密钥
				function renderCompay(res){

					var tbody = $(".data-range table tbody");
					var element = "";
					if(res){
						$("tbody").html(element);
						$.each(res,function(i, item){
							var checked = '';
							element +=  '<tr class="footable-even" style="display: table-row;" >';
							element +=  '	<td class="footable-visible">';
							element += '		<label class="i-checks"> <input type="checkbox" class="checks-single" value="'+item.id+'" '+checked+'><i></i></label>';
							element +=  '	</td>';
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
							element +=  '</tr>';
						});
						$("tbody").html(element);
					}
				}
				//保存
				$(".btn-info-submit").click(function(){
					$.ajax({
  		                cache: false,
  		                type: "POST",
  		                url:"{{route('group.dataStore')}}",
  		                data:$('#myForm').serialize(),
  		                headers: {
  		                    'X-CSRF-TOKEN': $("input[name='_token']").val()
  		                },
  		                dataType: 'json',
  		                success: function(res) {
  		                	$(".btn-info-submit").attr('disabled',false);
  		                	if(res.status==200){
  		                		layer.msg(res.message);
  		                	}else{
  		                		layer.msg(res.message);
  		                	}
  		                },
  		                error: function(request) {
  		                    layer.msg("网络错误，请稍后重试");
  		                    $(".btn-info-submit").attr('disabled',false);
  		                },
  		            });
				})
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
