@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('js/pagination/pagination.css')}}" />

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
			<div ui-view="" class="ng-scope">
				<div ng-controller="MailListCtrl" class="ng-scope">
					<!-- header -->
					<div class="wrapper bg-light lter b-b">
						<span class="text-muted help-author">消息列表</span> 
					</div>
					<!-- / header -->

					<!-- list -->
					<ul class="list-group list-group-lg no-radius m-b-none m-t-n-xxs">
						
						@foreach($mes['info'] as $value)
						<li class="list-group-item clearfix b-l-3x ng-scope b-l-info"><a
							class="avatar thumb pull-left m-r"
							href="javascript:;"> 
							<img  src="{{$value['sender']['avatar'] or ''}}" title="{{$value['sender']['username'] or ''}}">
						</a>
							<div class="pull-right text-sm text-muted">
								<span class="hidden-xs ng-binding m-r-sm">{{$value['time'] or ''}}</span> 
								<i class="ace-icon fa fa-trash-o pull-right bigger-120 grey m-r-sm delMessage" id="{{$value['id'] or ''}}" subject="{{$value['subject'] or ''}}"></i>
							</div>
							<div class="clear">
								<div>
									<a class="text-md ng-binding" href="/Message/detail?id={{$value['id'] or ''}}&type={{$mes['type']}}">
										{{$value['subject'] or ''}}
										</a><span class="label bg-light m-l-sm ng-binding">
											@if($value['isread']==1)
												已读
											@else
												未读
											@endif</span>
								</div>
								<div class="text-ellipsis m-t-xs ng-binding">{{$value['content'] or ''}}</div>
							</div>
						</li>
						@endforeach
					</ul>
					@if(!empty($mes['info']))
					<div class="padder">
						<div class="list-group">
							<div class="M-box"></div>
						</div>
					</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript" src="{{URL::asset('js/pagination/jquery.pagination.min.js')}}"></script>
<script type="text/javascript" charset="utf-8">

	var helpInfo;
	$('.M-box').pagination({
	    pageCount: {{$mes['pageCount'] or ''}},
	    jump: true,
	    current: {{$mes['page'] or 1}},
	    callback:function(api){
	        var page=api.getCurrent();
	        var type = getQueryString('type');
	        if(type){
	        	window.location.href = '/Message/index?type='+type+'&page='+page
		    }else{
		    	window.location.href = '/Message/index?page='+page
			};
	    }
	});
	//获取get参数
	function getQueryString(name) { 
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "iu");
        var r = window.location.search.substr(1).match(reg);
        if (r != null)
            return decodeURI(r[2]);
        return null;
    }
	//删除消息
	$(".app-content-body").on('click', '.delMessage', function(){
		var subject = $(this).attr('subject');
		var id = $(this).attr('id');
		layer.confirm('确认删除 '+subject+'？', {
			  btn: ['确定','取消']
		}, function(){
			$.ajax({
	            cache: false,
	            type: "post",
	            url:"{{route('message.del')}}",
	            data: {'id':id},
	            headers: {
	                'X-CSRF-TOKEN': $("input[name='_token']").val()
	            },
	            dataType: 'json',
	            success: function(res) {
	            	if(res.status==200){
	            		layer.msg(res.message);
                		setTimeout(function(){
							 window.location.reload();
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
<!-- /.page-content -->
@endsection
