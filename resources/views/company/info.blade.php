@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/chosen.css')}}">

<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
		<!-- main -->
		<div class="col">
			<!-- content -->
			<div class="wrapper-md ng-scope">
				<div class="panel panel-default">
					<div class="panel-heading font-bold">@if(!empty($data['id'])) 编辑密钥 @else 添加密钥 @endif</div>
					<div class="panel-body">
						<form id="myForm" method="post"
							class="form-horizontal ng-pristine ng-valid ng-valid-date ng-valid-required ng-valid-parse ng-valid-date-disabled">
							<div class="form-group">
								<label class="col-sm-2 control-label">公司名称</label>
								<div class="col-sm-6">
									<input name="company" type="text" class="form-control" value="{{$data['company'] or ''}}"> 
									<span class="help-block m-b-none" style="color:red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label">appId</label>
								<div class="col-sm-6">
									<input name="appId" type="text" class="form-control" value="{{$data['appId'] or ''}}"> 
									<span class="help-block m-b-none" style="color:red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label">appSecret</label>
								<div class="col-sm-6">
									<input name="appSecret" type="text" class="form-control" value="{{$data['appSecret'] or ''}}"> 
									<span class="help-block m-b-none" style="color:red;"></span>
								</div>
								<div class="col-sm-2">
									<label class="btn btn-sm btn-info random-button">随机获取</label>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label">状态</label>
								<div class="col-sm-6">
									<label class="i-switch m-t-xs m-r">
                                      <input name="status" type="checkbox" @if(empty($data['status']) || (!empty($data['status']) && $data['status']==1)) checked @endif>
                                      <i></i>
                                    </label>
                                    <label>
                                    	@if(empty($data['status']) || (!empty($data['status']) && $data['status']==1)) 已开启  @else 已冻结  @endif
                                    </label>
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
					//随机获取
					$(".random-button").click(function(){
						var secret = randomWord(true, 32,48);
						$('input[name="appSecret"]').val(secret);
					})
					//表单验证
    				var validator = $("#myForm").validate({
    					submitHandler: function(form) {
    						$(".btn-info-submit").attr('disabled',true);
    						$.ajax({
    			                cache: false,
    			                type: "POST",
    			                url:"{{route('secret.store')}}",
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
    										 window.location.href="{{route('secret.index')}}";
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
    						company:{
    							required:true,
    							maxlength:60,
    							minlength:2
    						},
    						appId:{
    							required:true,
    						},
    						appSecret:{
    							required:true,
    							maxlength:48,
    							minlength:6
    						}
    					},
    					messages:{
    						company :{
    							required:"公司名称不能为空",
    							maxlength:"不能超过60个字符",
    							minlength:"不能少于2个字符",
    						},
    						appId :{
    							required:"appId不能为空",
    						},
    						appSecret :{
    							required:"appSecret不能为空",
    							maxlength:"不能超过48个字符",
    							minlength:"不能少于6个字符",
    						}
    				        
    					},
    					errorElement: 'custom',
    					errorClass:'error',
    					errorPlacement: function(error, custom) {
    						error.appendTo(custom.next('span'))
    					},  
    				})
					/*
                    ** randomWord 产生任意长度随机字母数字组合
                    ** randomFlag-是否任意长度 min-任意长度最小位[固定位数] max-任意长度最大位
                    ** xuanfeng 2014-08-28
                    */
                    function randomWord(randomFlag, min, max){
                        var str = "",
                            range = min,
                            arr = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
                     
                        // 随机产生
                        if(randomFlag){
                            range = Math.round(Math.random() * (max-min)) + min;
                        }
                        for(var i=0; i<range; i++){
                            pos = Math.round(Math.random() * (arr.length-1));
                            str += arr[pos];
                        }
                        return str;
                    }
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
