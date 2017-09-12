@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">

<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope"
		ng-controller="MailCtrl">
		<div class="col w-md bg-light dk b-r bg-auto">
			<div class="wrapper b-b bg">
				<button
					class="btn btn-sm btn-default pull-right visible-sm visible-xs"
					ui-toggle-class="show" target="/Message/info">
					<i class="fa fa-bars"></i>
				</button>
				<a ui-sref="app.mail.compose"
					class="btn btn-sm btn-danger w-xs font-bold"
					href="/Message/info">发消息</a>
			</div>
			<div class="wrapper hidden-sm hidden-xs" id="email-menu">
				<ul class="nav nav-pills nav-stacked nav-sm">
					<li class="ng-scope @if(!empty($_GET['type']) && $_GET['type']=='unread') active @endif"><a class="ng-binding" href="/Message/index?type=unread"> 未读消息 </a></li>
					<li class="ng-scope @if(empty($_GET['type'])) active @endif"><a class="ng-binding" href="/Message/index"> 已接收消息 </a></li>
					<li class="ng-scope @if(!empty($_GET['type']) && $_GET['type']=='send') active @endif"><a class="ng-binding" href="/Message/index?type=send"> 已发送消息 </a></li>
				</ul>
			</div>
		</div>
		<div class="col">
			<!-- uiView:  -->
			<div class="ng-scope">
				<div  class="ng-scope">
					<!-- header -->
					<div class="wrapper bg-light lter b-b">
						<a class="btn btn-sm btn-default w-xxs m-r-sm" href="/Message/index"><i
							class="fa fa-long-arrow-left"></i></a>
					</div>
					<!-- / header -->
					<div class="wrapper b-b">
						<h2 class="font-thin m-n ng-binding">{{$info['detail']['mes']['subject'] or ''}}</h2>
					</div>
					<div class="wrapper b-b ng-binding">
						<img class="img-circle thumb-xs m-r-sm"
							src="{{$info['detail']['mes']['sender']['avatar'] or ''}}"> 
							@if($info['uid']==$info['detail']['mes']['sender']['uid'])
								to <a href="" class="ng-binding">{{$info['detail']['mes']['receiver']['username'] or ''}}</a>
							@else
								from <a href="" class="ng-binding">{{$info['detail']['mes']['sender']['username'] or ''}}</a>
							@endif
						{{$info['detail']['mes']['time'] or ''}}
					</div>
					<div class="wrapper ng-binding">
						<div class="m-l-lg">
                            <div class="m-b">
                              <div>{{$info['detail']['mes']['content'] or ''}}</div>
                            </div>
                          </div>
                          
                        @if(!empty($info['detail']['reply']))
                        	@foreach($info['detail']['reply'] as $reply)
        						<div class="m-l-lg">
                                  <a class="pull-left thumb-sm avatar">
                                    <img src="{{$reply['sender']['avatar'] or ''}}" title="{{$reply['sender']['username'] or ''}}">
                                  </a>          
                                  <div class="m-l-sm panel b-a m-b-sm">
                                    <div class="panel-heading pos-rlt">
                                      <span class="arrow left pull-up"></span>
                                      <span class="text-muted m-l-sm pull-right">
                                        {{$reply['time'] or ''}}
                                      </span>
                                      <a href="javascript:;">{{$reply['sender']['username'] or ''}}：</a>
                                      	{{$reply['content'] or ''}}                     
                                    </div>
                                  </div>
                                </div>
                        	@endforeach
                        @endif
                        
					</div>
					<div class="wrapper">
						<div class="panel b-a">
							<div class="ng-hide" ng-show="reply">
								<div class="panel-heading b-b b-light ng-binding">
									@if($info['uid']==$info['detail']['mes']['sender']['uid'])
        								{{$info['detail']['receiver']['username'] or ''}}
        								<input type="hidden" value="{{$info['detail']['mes']['receiver']['uid'] or ''}}" name="member" />
        							@else
        								{{$info['detail']['sender']['mes']['username'] or ''}}
        								<input type="hidden" value="{{$info['detail']['mes']['sender']['uid'] or ''}}" name="member" />
        							@endif
									</div>
								<div class="wrapper content" contenteditable="true"
									style="min-height: 100px"></div>
								<div class="panel-footer bg-light lt">
									<button class="btn btn-link pull-right">
									</button>
									<input type="hidden" value="{{ csrf_token() }}" name="_token" />
									<input type="hidden" value="{{$info['detail']['mes']['id'] or ''}}" name="pid" />
									<input type="hidden" value="{{$info['detail']['mes']['subject'] or ''}}" name="subject">
									<button class="btn btn-info w-xs font-bold btn-info-submit">回复</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" charset="utf-8">

	$(".btn-info-submit").click(function(){
		var subject = $("input[name='subject']").val();
		var pid = $("input[name='pid']").val();
		var content = $(".content").html();
		var member = $("input[name='member']").val();
		var _token = $("input[name='_token']").val();
		
		if(content.length<6){
			layer.msg("内容不能少于6个字符");
		}else{
			$(".btn-info-submit").attr('disabled',true);
			$.ajax({
	            cache: false,
	            type: "POST",
	            url:"{{route('message.store')}}",
	            data:{
	                'subject':'[回复]'+subject,
	                'pid':pid,
	                'content':content,
	                'members':member,
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
							 window.location.reload();
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
	})
	            
</script>			            
<!-- /.page-content -->
@endsection
