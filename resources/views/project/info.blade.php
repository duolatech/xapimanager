@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/chosen.css')}}">

<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
		<!-- main -->
		<div class="col">
			<!-- main header -->
			<div class="bg-light lter b-b wrapper-md">
				<div class="row">
					<div class="col-sm-6 col-xs-12">
						<h1 class="m-n font-thin h3 text-black">Dashboard</h1>
					</div>
					<div class="col-sm-6 text-right hidden-xs">
						<div class="inline m-r text-left">
							<div class="m-b-xs">
								1 <span class="text-muted">items</span>
							</div>
						</div>
						<div class="inline text-left">
							<div class="m-b-xs">
								<span class="text-muted"><i class="glyphicon glyphicon-th"></i></span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- / main header -->
			<!-- content -->
			<div class="wrapper-md ng-scope">
				<div class="panel panel-default">
					<div class="panel-heading font-bold">@if(!empty($data['id'])) 编辑项目 @else 添加项目 @endif</div>
					<div class="panel-body">
						<form id="myForm" method="post"
							class="form-horizontal ng-pristine ng-valid ng-valid-date ng-valid-required ng-valid-parse ng-valid-date-disabled">
							<div class="form-group">
								<label class="col-sm-2 control-label">项目名称</label>
								<div class="col-sm-6">
									<input name="proname" type="text" class="form-control" value="{{$data['proname'] or ''}}"> 
									<span class="help-block m-b-none" style="color:red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label">项目描述</label>
								<div class="col-sm-6">
									<textarea name="desc" class="form-control" rows="6" placeholder="">{{$data['desc'] or ''}}</textarea>
									<span class="help-block m-b-none" style="color:red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label">项目开放</label>
								<div class="col-sm-6">
									<div class="radio">
										<label class="i-checks"> <input type="radio" name="attribute"
											class="ng-valid ng-dirty ng-valid-parse ng-touched" value=1 @if(empty($data['attribute']) || $data['attribute']==1) checked @endif> <i></i>
											对所有权限组开放
										</label>
									</div>
									<div class="radio">
										<label class="i-checks"> <input type="radio" name="attribute" value=2 @if(empty($data['attribute']) || $data['attribute']==2) checked @endif
											class="ng-valid ng-dirty ng-touched"> <i></i> 仅对指定权限组开放
										</label>
									</div>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group project-member" style="display:none;">
								<label class="col-sm-2 control-label">项目成员</label>
								<div class="col-sm-6">
									<div
										class="ui-select-multiple ui-select-bootstrap dropdown form-control ng-valid ng-dirty open"
										multiple="multiple" theme="bootstrap" ng-disabled="disabled">
										<div>
											<span class="ui-select-match" placeholder="Select person...">
												@if(!empty($data['groups']))
													@foreach($data['groups'] as $value)
														<span class="ng-scope">
                                                        	<span style="margin-right: 3px;" class="ui-select-match-item btn btn-default btn-xs" tabindex="-1" type="button">
                                                        		<span class="close ui-select-match-close">&nbsp;×</span>
                                                        		<span uis-transclude-append="">
                                                        			<span class="ng-binding ng-scope">
                                                        					{{$value['groupname'] or ''}}
                                                        			</span> 
                                                        		</span> 
                                                        		<input type="hidden" value="{{$value['id'] or ''}}" name="groups[]" />
                                                        	</span>
                                                        </span>
													@endforeach
												@endif
											</span>
											<input type="text" autocomplete="off"
												autocorrect="off" autocapitalize="off" spellcheck="false"
												class="ui-select-search input-xs ng-pristine ng-valid ng-touched"
												placeholder="" style="width: 503px;">
										</div>
										<ul
											class="ui-select-choices ui-select-choices-content dropdown-menu ng-scope"
											role="menu" aria-labelledby="dLabel" group-by="someGroupFn" style="display:none;">
											<li class="ui-select-choices-group ng-scope"><div
													class="divider ng-hide"></div>
										</ul>
									</div>

								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							
							<div class="form-group">
                              <div class="col-sm-4 col-sm-offset-2">
                              	<input type="hidden" value="{{ csrf_token() }}" name="_token" />
                              	<input type="hidden" value="{{$data['id'] or ''}}" name="id" />
                                <button type="submit" class="btn btn-primary btn-info-submit">保存项目</button>
                              </div>
                            </div>
                            
                            <div class="form-group" style="margin-top:180px;"></div>
                            
						</form>
					</div>
				</div>
			</div>
			<!-- /content -->
			<script type="text/javascript" src="{{URL::asset('js/validation/jquery.validate.js')}}"></script>
			<script type="text/javascript" src="{{URL::asset('js/validation/messages_zh.js')}}"></script>
			<script type="text/javascript" charset="utf-8">
				//项目开放性
				var pmember = $(".project-member");
				var open = $('input[name="attribute"]:checked').val();
				if(open==2) pmember.show();
				$('input[name="attribute"]').click(function(){
					var val = $(this).val();
					if(val==2){
						pmember.show();
					}else if(val==1){
						pmember.hide();
					}
				})
				//选择成员
				$(".ui-select-search").focus(function(){
					$(".ui-select-choices-content").show();
					sendKeyWord('');
				})
				$(".ui-select-choices-content").on('mouseover', '.ui-select-choices-row', function(){
					$(this).addClass('active');
				}).on('mouseout', '.ui-select-choices-row', function(){
					$(this).removeClass('active');
				})
				$(".wrapper-md").on('click', '.ui-select-choices-row', function(){
					var groupname = $(this).attr('groupname');
					var gid = $(this).attr('gid');
					var _element = '';
					_element += '<span class="ng-scope">';
					_element += '	<span style="margin-right: 3px;" class="ui-select-match-item btn btn-default btn-xs" tabindex="-1" type="button">';
					_element += '		<span class="close ui-select-match-close">&nbsp;×</span>';
					_element += '		<span uis-transclude-append="">';
					_element += '			<span class="ng-binding ng-scope">';
					_element += 				groupname;
					_element += '			</span> ';
					_element += '		</span> ';
					_element += '		<input type="hidden" value="'+gid+'" name="groups[]" />';
					_element += '	</span>';
					_element += '</span>';
					
					$(".ui-select-match").append(_element);
					$(".ui-select-search").val('');
				})
				$(".wrapper-md").on('click', '.ui-select-match-close', function(){
					$(this).parents('.ui-select-match-item').remove();
				})
				$(".ui-select-choices-content").mouseleave(function() {
					$(this).hide();
					$(".ui-select-search").blur();
        		})
        		//自动联想
        		$(".wrapper-md").on('keyup', '.ui-select-search', function(){
        			keyword = $(this).val();
        			sendKeyWord(keyword);
            	}).on('paste', '.ui-select-search', function(){
            		var el = $(this); 
            		setTimeout(function() { 
            			keyword = $(el).val(); 
            			sendKeyWord(keyword);
            		}, 100);  
                })
				function sendKeyWord(keyword){
						$.ajax({
			                cache: false,
			                type: "GET",
			                url:"{{route('ajaxGroup')}}?&keyword="+keyword,
			                dataType: 'json',
			                success: function(res) {
				                var _element = '';
			                	if(res.status==200){
			                		$.each(res.data,function(key, group){
			                			_element +='<div class="ui-select-choices-row ng-scope" groupname="'+group.groupname+'" gid="'+group.id+'">';
			                			_element +='	<a href="javascript:void(0)"';
			                			_element +='		class="ui-select-choices-row-inner"';
			                			_element +='		uis-transclude-append="">';
			                			_element +='		<div class="ng-binding ng-scope">'+group.groupname+'</div>';
			                			_element +='	</a>';
			                			_element +='</div>';
				                	})
				                	$(".ui-select-choices-group").html(_element);
				                }
			                },
			                error: function(request) {
			                	layer.msg("网络错误，请稍后重试");
			                },
			            });
				}
				//表单验证
				var validator = $("#myForm").validate({
					submitHandler: function(form) {
						$(".btn-info-submit").attr('disabled',true);
						$.ajax({
			                cache: false,
			                type: "POST",
			                url:"{{route('project.store')}}",
			                data:$('#myForm').serialize(),
			                headers: {
			                    'X-CSRF-TOKEN': $("input[name='_token']").val()
			                },
			                dataType: 'json',
			                success: function(res) {
			                	$(".btn-info-submit").attr('disabled',false);
			                	layer.msg(res.message)
			                	if(res.status==200){
			                		setTimeout(function(){
										 window.location.href="/";
									 }, 2000);
			                	}
			                },
			                error: function(request) {
			                    layer.msg("网络错误，请稍后重试");
			                    $(".btn-info-submit").attr('disabled',false);
			                },
			            });
					},
					rules:{
						proname:{
							required:true,
							maxlength:20,
							minlength:2
						},
						desc:{
							required:true,
							minlength:6
						}
					},
					messages:{
						proname :{
							required:"项目名称不能为空",
							maxlength:"不能超过20个字符",
							minlength:"不能少于2个字符",
						},
						desc :{
							required:"项目描述不能为空",
							minlength:"不能少于6个字符",
						}
				        
					},
					errorElement: 'custom',
					errorClass:'error',
					errorPlacement: function(error, custom) {
						error.appendTo(custom.next('span'))
					},  
				})
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
