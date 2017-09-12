@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/chosen.css')}}">
<link rel="stylesheet" href="{{URL::asset('js/umeditor/themes/default/css/umeditor.css')}}">

<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
		<!-- main -->
		<div class="col">
			<!-- content -->
			<div class="wrapper-md ng-scope">
				<div class="panel panel-default">
					<div class="panel-heading font-bold">
						@if(!empty($info['id'])) 编辑子分类 @else 添加子分类 @endif
					</div>
					<div class="panel-body">
						<form id="myForm" method="post"
							class="form-horizontal ng-pristine ng-valid ng-valid-date ng-valid-required ng-valid-parse ng-valid-date-disabled">
							<div class="form-group">
								<label class="col-sm-2 control-label">上级分类</label>
								<div class="col-sm-6">
    								<select name="pid" class="form-control m-b-xs valid" aria-invalid="false">
    									@foreach($info['classify'] as $value)
            								<option value="{{$value['id']}}" 
            									@if(!empty($info['pid']) && $value['id']==$info['pid']) selected="selected" @endif
            									@if(!empty($info['currentClassify']) && $value['id']== $info['currentClassify']) selected="selected" @endif
            								>{{$value['classifyname']}}</option>
            							@endforeach
    								</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-2 control-label">子分类名称</label>
								<div class="col-sm-6">
									<input name="classify" type="text" class="form-control" value="{{$info['classifyname'] or ''}}"> 
									<span class="help-block m-b-none" style="color:red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group project-member">
								<label class="col-sm-2 control-label">分类负责人</label>
								<div class="col-sm-6">
									<div
										class="ui-select-multiple ui-select-bootstrap dropdown form-control ng-valid ng-dirty open"
										multiple="multiple" theme="bootstrap" ng-disabled="disabled">
										<div>
											<span class="ui-select-match" placeholder="Select person...">
												@if(!empty($info['user']))
													@foreach($info['user'] as $value)
														<span class="ng-scope">
                                                        	<span style="margin-right: 3px;" class="ui-select-match-item btn btn-default btn-xs" tabindex="-1" type="button">
                                                        		<span class="close ui-select-match-close">&nbsp;×</span>
                                                        		<span uis-transclude-append="">
                                                        			<span class="ng-binding ng-scope">
                                                        					{{$value['username'] or ''}}&lt;{{$value['email'] or ''}}&gt;;
                                                        			</span> 
                                                        		</span> 
                                                        		<input type="hidden" value="{{$value['uid'] or ''}}" class="classifymanager" name="members[]" />
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
								<label class="col-sm-2 control-label">分类描述</label>
								<div class="col-sm-8">
									<!--style给定宽度可以影响编辑器的最终宽度-->
                                    <script type="text/plain" id="myEditor" style="height:240px;">
                                          @if(!empty($info['id']))
                                                {!!$info['description'] or ''!!}
                                          @endif
                                    </script>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							
							<div class="form-group">
                              <div class="col-sm-4 col-sm-offset-2">
                              	<input type="hidden" value="{{ csrf_token() }}" name="_token" />
                              	<input type="hidden" value="{{$info['id'] or ''}}" name="classifyId" />
                                <button type="submit" class="btn btn-primary btn-info-submit">保存分类</button>
                              </div>
                            </div>
                            
                            <div class="form-group" style="margin-top:180px;"></div>
                            
						</form>
					</div>
				</div>
			</div>
			<!-- /content -->
			<script type="text/javascript" src="{{URL::asset('js/umeditor/third-party/template.min.js')}}"></script>
    		<script type="text/javascript" src="{{URL::asset('js/umeditor/umeditor.config.js')}}"></script>
   		 	<script type="text/javascript" src="{{URL::asset('js/umeditor/umeditor.min.js')}}"></script>
    		<script type="text/javascript" src="{{URL::asset('js/umeditor/lang/zh-cn/zh-cn.js')}}"></script>
			<script type="text/javascript" src="{{URL::asset('js/validation/jquery.validate.js')}}"></script>
			<script type="text/javascript" src="{{URL::asset('js/validation/messages_zh.js')}}"></script>
			<script type="text/javascript" charset="utf-8">

				//实例化编辑器
		    	var um = UM.getEditor('myEditor');
				//项目开放性
				var pmember = $(".project-member");
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
					var username = $(this).attr('username');
					var email = $(this).attr('email');
					var uid = $(this).attr('uid');
					var _element = '';
					_element += '<span class="ng-scope">';
					_element += '	<span style="margin-right: 3px;" class="ui-select-match-item btn btn-default btn-xs" tabindex="-1" type="button">';
					_element += '		<span class="close ui-select-match-close">&nbsp;×</span>';
					_element += '		<span uis-transclude-append="">';
					_element += '			<span class="ng-binding ng-scope">';
					_element += 				username + "&lt;"+ email +"&gt;";
					_element += '			</span> ';
					_element += '		</span> ';
					_element += '		<input type="hidden" value="'+uid+'" class="classifymanager" name="members[]" />';
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
			                url:"{{route('ajaxUser')}}?search=1&field=email&keyword="+keyword,
			                dataType: 'json',
			                success: function(res) {
				                var _element = '';
			                	if(res.status==200){
			                		$.each(res.data.info,function(key, user){
			                			_element +='<div class="ui-select-choices-row ng-scope" username="'+user.username+'" email="'+user.email+'" uid="'+user.uid+'">';
			                			_element +='	<a href="javascript:void(0)"';
			                			_element +='		class="ui-select-choices-row-inner"';
			                			_element +='		uis-transclude-append="">';
			                			_element +='		<div class="ng-binding ng-scope">'+user.username+' '+user.email+'</div>';
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
						
						var classify = $("input[name='classify']").val();
						var classifyId = $("input[name='classifyId']").val();
						var description  = um.getContent();
						var pid = $('select[name="pid"]').val();
						var _token = $("input[name='_token']").val();

						var members = '';  //分类负责人
						$(".classifymanager").each(function(){
							 obj = $(this);
							 members += obj.val()+',';
						});
						if(!members){
							layer.msg("请输入分类负责人");
						}else{
							$(".btn-info-submit").attr('disabled',true);
							$.ajax({
				                cache: false,
				                type: "POST",
				                url:"{{route('Category.store')}}",
				                data:{
				                    'classify':classify,
				                    'pid':pid,
				                    'classifyId':classifyId,
				                    'description':description,
				                    'members':members,
				                },
				                headers: {
				                    'X-CSRF-TOKEN': _token
				                },
				                dataType: 'json',
				                success: function(res) {
									$(".btn-info-submit").attr('disabled',false);
				                	if(res.status==200){
				                		layer.msg(res.message);
				                		setTimeout(function(){
											 window.location.href = "{{route('Category.index')}}";;
										 }, 2000);
				                	}else{
				                		layer.msg(res.message);
				                	}
				                },
				                error: function(request) {
									$(".btn-info-submit").attr('disabled',false);
				                    layer.msg("网络错误，请稍后重试");
				                },
				            });
						}
					},
					rules:{
						classify:{
							required:true,
							maxlength:60,
							minlength:2
						}
					},
					messages:{
						classify :{
							required:"分类名称不能为空",
							maxlength:"不能超过60个字符",
							minlength:"不能少于2个字符",
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
