@extends('base') @section('page-content')
<div class="page-content">
	<!-- #section:settings.box -->
	@include('public/setbox')
	<link rel="stylesheet"
		href="{{URL::asset('js/pagination/pagination.css')}}" />
	<div class="row">
		<div class="col-xs-11 col-xs-offset-1">
			<!-- PAGE CONTENT BEGINS -->
			<form class="form-horizontal" action="#" method="post" id="myForm">
				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-1"> 资源名称 </label>
					<div class="col-sm-9">
						<input type="text" name="apiname" class="rcol-xs-8 col-sm-8"
							placeholder="接口名称" value="{{$data['detail']['apiname'] or ''}}">
						<span class="help-inline col-xs-12 col-sm-7"> <span class="middle"></span>
						</span>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-10"> 接口分类 </label>
					<div class="col-sm-9 selectList">
						<select name="classify" class="rcol-xs-4 col-sm-4 classify">
							<option value="0">请选择</option>
						</select> <select name="subClassify"
							class="rcol-xs-4 col-sm-4 subClassify">
							<option value="0">请选择</option>
						</select>
					</div>
				</div>

				<div class="space-4"></div>

				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-2"> URI </label>
					<div class="col-sm-9">
						<input type="text" name="URI"
							placeholder="/Api/info" class="rcol-xs-8 col-sm-8"
							value=""> <span
							class="help-inline col-xs-12 col-sm-7"> <span class="middle"></span>
						</span>
					</div>
				</div>
				<div class="space-4"></div>
				<div class="form-group">
					<label class="col-sm-1 control-label no-padding-right"
						for="form-field-2"> 开发者 </label>
					<div class="col-sm-9">
						<input type="text" name="author"
							placeholder="小明" class="rcol-xs-8 col-sm-8"
							value=""> <span
							class="help-inline col-xs-12 col-sm-7"> <span class="middle"></span>
						</span>
					</div>
				</div>
				<div class="space-4"></div>

				<div class="col-md-offset-2 col-md-9">
					
					<button class="btn btn-info btn-info-submit btn-search" type="button">
						<i class="icon-ok bigger-110"></i> 搜索
					</button>

					&nbsp; &nbsp; &nbsp;
					<button class="btn" type="reset">
						<i class="icon-undo bigger-110"></i> 重置
					</button>
				</div>
			</form>
			<!-- PAGE CONTENT ENDS -->
		</div>
		<!-- /.col -->
	</div>
	<!-- /.row -->
</div>
<!-- /.page-content -->
<script type="text/javascript"
	src="{{URL::asset('js/pagination/jquery.pagination.min.js')}}"></script>
<script type="text/javascript" charset="utf-8">

	$(function(){
		//分类多级联动
	    $(".selectList").each(function(){
	        var areaJson;
	        var temp_html;
	        var oclassify = $(this).find(".classify");
	        var osubClassify = $(this).find(".subClassify");
	        var classify = function(){
		        temp_html+="<option value=0>请选择</option>";
	            $.each(areaJson,function(i,classify){
	                temp_html+="<option value='"+classify.id+"'>"+classify.name+"</option>";
	            });
	            oclassify.html(temp_html);
	            subClassify();
	        };
	        //赋值子分类
	        var subClassify = function(){
	            temp_html = ""; 
	            var n = oclassify.get(0).selectedIndex;
	            temp_html+="<option value=0>请选择</option>";
	            $.each(areaJson[n].child,function(i,subClassify){
	                temp_html+="<option value='"+subClassify.id+"'>"+subClassify.ct+"</option>";
	            });
	            osubClassify.html(temp_html);
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
		    
	    	var env = $(".current_env span").attr("env");
	    	var apiname = $('input[name="apiname"]').val();
	    	var classify = $('select[name="classify"]').val();
	    	var subClassify = $('select[name="subClassify"]').val();
	    	var URI = $('input[name="URI"]').val();
	    	var author = $('input[name="author"]').val();

	    	var url = "/Api/list?type=search";
	    	url+="&envid="+env;
	    	url+="&apiname="+apiname;
	    	if(classify!=0) url+="&classify="+classify;
	    	if(subClassify!=0) url+="&subClassify="+subClassify;
	    	url+="&URI="+URI;
	    	url+="&author="+author;
	    	
	    	window.location.href= url;
		})
	})
		
</script>
@endsection
