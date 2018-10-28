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
					<div class="panel-heading font-bold">Api 搜索</div>
					<div class="panel-body">
						<form id="myForm" method="post"
							class="form-horizontal ng-pristine ng-valid ng-valid-date ng-valid-required ng-valid-parse ng-valid-date-disabled">
							<div class="form-group">
								<label class="col-sm-2 control-label">资源名称</label>
								<div class="col-sm-6">
									<input name="apiname" type="text" class="form-control"
										value="" placeholder="接口名称" @if(!empty($data['lid']) && !empty($data['version_type']) && $data['version_type']=='add') readonly @endif> <span
										class="help-block m-b-none" style="color: red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Api 分类</label>
								<div class="col-sm-6">
									<div class="row selectList">
										<div class="col-sm-6">
											<select name="classify" class="form-control m-b-xs classify">
												<option value="0">请选择</option>
											</select>
										</div>
										<div class="col-sm-6">
											<select name="subClassify" class="form-control m-b-xs subClassify">
												<option value="0">请选择</option>
											</select>
										</div>
									</div>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label">URI</label>
								<div class="col-sm-6">
									<input name="gateway" type="text" class="form-control"
										value="" placeholder="URL路径部分，示例：/Api/v1/info"> <span
										class="help-block m-b-none" style="color: red;"></span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="form-group">
								<label class="col-sm-2 control-label">Api 维护人</label>
								<div class="col-sm-6">
										<input type="text" name="author" class="form-control ui-select-search" > <span
                							class="help-inline col-xs-12 col-sm-7"><span
										class="help-block m-b-none" style="color: red;"></span>
                						</span>
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>


							<div class="form-group">
								<div class="col-sm-4 col-sm-offset-2">
									<input type="hidden" value="{{ csrf_token() }}" name="_token" />
									<input type="hidden" value="{{$data['id'] or ''}}" name="id" />
									<a type="submit" class="btn btn-primary btn-search">搜索</a>
								</div>
							</div>

							<div class="form-group" style="margin-top: 180px;"></div>

						</form>
					</div>
				</div>
			</div>
			<!-- /content -->
			<script type="text/javascript"
				src="{{URL::asset('js/validation/jquery.validate.js')}}"></script>
			<script type="text/javascript"
				src="{{URL::asset('js/validation/messages_zh.js')}}"></script>
			<script type="text/javascript" charset="utf-8">

    			$(function(){

      			  	//分类多级联动
        		    $(".selectList").each(function(){
        		        var areaJson;
        		        var temp_html;
        		        var oclassify = $(this).find(".classify");
        		        var osubClassify = $(this).find(".subClassify");
        		        //初始化一级分类
        		        var classify = function(){
        		        	temp_html ="";
        		            $.each(areaJson,function(i,classify){
        		                temp_html+="<option value='"+classify.id+"'>"+classify.name+"</option>";
        		            });
        		            oclassify.html(temp_html);
        		            subClassify();
        		        };
        		        //赋值子分类
        		        var subClassify = function(){
        		        	temp_html ="";
        		            var n = oclassify.get(0).selectedIndex;
        		            if(!areaJson[n].child && n>0){
        		            	layer.alert('该分类下无子分类，<br/>请在分类信息中添加子分类');
        		            }else{
        		            	$.each(areaJson[n].child, function(i,subClassify){
        		                    temp_html+="<option value='"+subClassify.id+"'>"+subClassify.ct+"</option>";
        		                });
        		            }
        		            default_html ="<option value=0 selected>请选择</option>";
        		            osubClassify.html(default_html+temp_html);
        		        };
        		        //选择分类改变子分类
        		        oclassify.change(function(){
        		            subClassify();
        		        });
        		        //获取json数据
        		            areaJson = {!! $data['classify'] or '' !!};
        		            classify();
        		    });

        		    
    			    //搜索
    			    $(".btn-search").click(function(){
    				    
    			    	var apiname = $('input[name="apiname"]').val();
    			    	var classify = $('select[name="classify"]').val();
    			    	var subClassify = $('select[name="subClassify"]').val();
    			    	var URI = $('input[name="gateway"]').val();
    			    	var author = $('input[name="author"]').val();
    
    			    	var url = "/Api/list?type=search";
    			    	url+="&apiname="+apiname;
    			    	if(classify!=0) url+="&classify="+classify;
    			    	if(subClassify!=0) url+="&subClassify="+subClassify;
    			    	url+="&URI="+URI;
    			    	url+="&author="+author;
    			    	
    			    	window.location.href= encodeURI(url);
    				})
    			})
      		  	
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
