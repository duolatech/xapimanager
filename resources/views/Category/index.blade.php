@extends('base') @section('page-content')
<div class="page-content">
	<!-- #section:settings.box -->
	<div class="alert alert-block alert-success">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>
		<!--i class="ace-icon fa fa-check green"></i-->
		友情提示：项目接口分类
	</div>
	@include('public/setbox')
	<div class="row">
		<div class="col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
				<div class="col-xs-12 p0 ng-scope" ng-if="list.length>0">
				<form class="form-horizontal" action="#"
				method="post" id="myForm">
					@foreach($info as $value)
					<div class="col-xs-2 gimgtextbox">
						<div>
							<a href="/Api/list?type=search&classify={{$value['id']}}" class="gimgbox">
								<img src="/images/classify.png" class="img-responsive">
							</a>
							<div class="center">
								<a  href="/Api/list?type=search&classify={{$value['id']}}">{{$value['classifyname']}}</a>
							</div>
							<div class="center gtx">
								<a href="/Category/sub?classifyId={{$value['id']}}"> <i class="ace-icon fa fa-xing-square"></i>子分类</a>
								<a href="/Export/v1/classify/{{$value['id']}}?envid=" target="__blank" class="export"> <i class="ace-icon fa fa-file-pdf-o"></i>PDF</a>
								<a href="/Category/info?classifyId={{$value['id']}}"> <i class="ace-icon fa fa-cog"></i>编辑</a>
							</div>
						</div>
					</div>
					@endforeach
				</form>
				</div>

		</div>
		<!-- PAGE CONTENT ENDS -->
	</div>
	<!-- /.col -->
</div>
<!-- /.row -->
</div>
<script type="text/javascript" charset="utf-8">

	var env = $(".current_env span").attr("env");
	$(".export").each(function(i,vol){
		var href = $(this).attr('href');
		href += env;
		$(this).attr('href', href);
	})
</script>
<!-- /.page-content -->
@endsection
