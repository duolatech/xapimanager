@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">

<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
		<div class="col w-md bg-light dk b-r bg-auto">
			<div class="wrapper b-b bg">
				<button
					class="btn btn-sm btn-default pull-right visible-sm visible-xs">
					<i class="fa fa-bars"></i>
				</button>
				<a ui-sref="app.mail.compose"
					class="btn btn-sm btn-danger w-xs font-bold"
					href="#/app/mail/compose">发消息</a>
			</div>
			<div class="wrapper hidden-sm hidden-xs" id="email-menu">
				<ul class="nav nav-pills nav-stacked nav-sm">
					<li
						class="ng-scope"><a
						class="ng-binding" href="/Message/index?type=unread"> 未读消息 </a></li>
					<li class="ng-scope"><a
						class="ng-binding" href="/Message/index"> 已接收消息 </a></li>
					<li
						class="ng-scope"><a
						class="ng-binding" href="/Message/index?type=send"> 已发送消息 </a></li>
				</ul>
			</div>
		</div>
		<div class="wrapper bg-light lter b-b">
						<a class="btn btn-sm btn-default w-xxs m-r-sm" href="/Message/index"><i
							class="fa fa-long-arrow-left"></i></a>
					</div>
		<div class="wrapper wrapper-md">
			<form name="newMail" id="myForm"
				class="form-horizontal m-t-lg ng-pristine ng-valid">
				<div class="form-group">
					<label class="col-lg-2 control-label">接收人:</label>
					<div class="col-lg-8">
						<div class="ui-select-multiple ui-select-bootstrap dropdown form-control ng-valid ng-dirty open"
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
				<div class="form-group">
					<label class="col-lg-2 control-label">消息标题:</label>
					<div class="col-lg-8">
						<input type="text" name="subject"
							class="form-control ng-pristine ng-untouched ng-valid">
							<span class="help-block m-b-none" style="color:red;"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">消息内容:</label>
					<div class="col-sm-8">
						<textarea class="form-control" rows="6" name="content"></textarea>
						<span class="help-block m-b-none" style="color:red;"></span>
					</div>
				</div>
				<div class="form-group">
					<div class="col-lg-8 col-lg-offset-2">
					<input type="hidden" value="{{ csrf_token() }}" name="_token" />
						<button class="btn btn-info w-xs btn-info-submit">发送</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript" src="{{URL::asset('js/validation/jquery.validate.js')}}"></script>
			<script type="text/javascript" src="{{URL::asset('js/validation/messages_zh.js')}}"></script>
			<script type="text/javascript" charset="utf-8">

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
						
						var subject = $("input[name='subject']").val();
						var content = $("textarea[name='content']").val();
						var _token = $("input[name='_token']").val();

						var members = '';  //分类负责人
						$(".classifymanager").each(function(){
							 obj = $(this);
							 members += obj.val()+',';
						});
						if(!members){
							layer.msg("请输入接收人");
						}else{
							$(".btn-info-submit").attr('disabled',true);
							$.ajax({
				                cache: false,
				                type: "POST",
				                url:"{{route('message.store')}}",
				                data:{
				                    'subject':subject,
				                    'pid':0,
				                    'content':content,
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
											 window.location.href = "{{route('message.index')}}?type=unread";;
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
						subject:{
							required:true,
							maxlength:60,
							minlength:2
						},
						content:{
							required:true,
							minlength:6
						}
					},
					messages:{
						subject :{
							required:"消息标题不能为空",
							maxlength:"不能超过60个字符",
							minlength:"不能少于2个字符",
						},
						content :{
							required:"消息内容不能为空",
							minlength:"不能少于6个字符",
						},
					},
					errorElement: 'custom',
					errorClass:'error',
					errorPlacement: function(error, custom) {
						error.appendTo(custom.next('span'))
					},  
				})
			</script>
<!-- /.page-content -->
@endsection
