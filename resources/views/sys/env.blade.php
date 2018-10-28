@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/chosen.css')}}">

<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
		<!-- main -->
		<div class="col">
		<div class="bg-light lter b-b wrapper-md">
				<div class="row">
					<div class="col-sm-6 col-xs-12">
						<h1 class="m-n font-thin h3 text-black">{{$proname or '默认项目'}}</h1>
					</div>
				</div>
			</div>
			<!-- content -->
			<div class="wrapper-md ng-scope">
					<div class="row">
						@foreach($data as $value)
						<form class="myForm{{$value['id'] or ''}}" method="post">
                            <div class="col-sm-4">
                              <div class="panel panel-default">
                                <div class="panel-heading font-bold">{{$value['envname'] or ''}}</div>
                                <div class="panel-body">
                                  <form class="bs-example form-horizontal ng-pristine ng-valid">
                                    <div class="form-group">
                                      <label class="col-lg-3 control-label">环境域名</label>
                                      <div class="col-lg-9">
                                        <input type="text" name="domain" class="form-control domain" placeholder="示例 http://api.smaty.net/" value="{{$value['domain'] or ''}}">
                                        <span class="help-block m-b-none" style="color:red;"></span>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="col-lg-3 control-label">环境名称</label>
                                      <div class="col-lg-9">
                                        <input type="text" name="envname" class="form-control envname" value="{{$value['envname'] or ''}}">
                                        <span class="help-block m-b-none" style="color:red;"></span>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="col-lg-3 control-label">是否启用</label>
                                      <div class="col-lg-9">
                                        <label class="i-switch m-t-xs m-r">
                                          <input type="checkbox" name="isopen" class="isopen" @if(empty($value['status']) || (!empty($value['status']) && $value['status']==1)) checked @endif>
                                          <i></i>
                                        </label>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                      <div class="col-lg-offset-3 col-lg-9">
                                      	<input type="hidden" value="{{ csrf_token() }}" name="_token" />
                                      	<input type="hidden" name="envid" class="envid" value="{{$value['id'] or ''}}">
                                        <button type="submit" class="btn btn-sm btn-info">保 存</button>
                                      </div>
                                    </div>
                                  </form>
                                </div>
                              </div>
                            </div>
                        </form>
                        @endforeach
                      </div>
			</div>
			<!-- /content -->
			<script type="text/javascript" src="{{URL::asset('js/validation/jquery.validate.js')}}"></script>
			<script type="text/javascript" src="{{URL::asset('js/validation/messages_zh.js')}}"></script>
			<script type="text/javascript" charset="utf-8">
    			$(function(){
        			$('form').each(function(){
						var obj = $(this);
						var validation = {
		    					submitHandler: function(form) {	

		    						$.ajax({
		    			                cache: false,
		    			                type: "POST",
		    			                url:"{{route('env.store')}}",
		    			                data:obj.serialize(),
		    			                headers: {
		    			                    'X-CSRF-TOKEN': $("input[name='_token']").val()
		    			                },
		    			                dataType: 'json',
		    			                success: function(res) {
		    			                	if(res.status==200){
		    			                		layer.msg(res.message)
		    			                	}else{
		    			                		layer.msg(res.message);
		    			                	}
		    			                },
		    			                error: function(request) {
		    			                    layer.msg("网络错误，请稍后重试");
		    			                },
		    			            });
		    					},
		    					rules:{
		    						domain:{
		    							required:true,
		    							UrlCheck:true,
		    						},
		    						envname:{
		    							required:true,
		    						}
		    					},
		    					messages:{
		    						domain:{
		    							required:"环境域名不能为空",
		    							UrlCheck:"请输入域名，以'http/https'开头",
		    						},
		    						envname:{
		    							required:"环境名称不能为空",
		    						}
		    					},
		    					errorElement: 'custom',
		    					errorClass:'error',
		    					errorPlacement: function(error, custom) {
		    						error.appendTo( custom.next('span') ); 
		    					},  
		    				};
						//表单验证
		      			jQuery.validator.addMethod("UrlCheck", function(value, element) {       
		      				return this.optional(element) || /^((https|http)?:\/\/)+[A-Za-z0-9]+(\.[A-Za-z0-9]+)+((:)+[0-9]{1,5})?(\/)?$/.test(value);    
		      			}, "请输入正确的域名地址");
						var validator = obj.validate(validation);
            		});
    			})
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
