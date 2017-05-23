@extends('base') @section('page-content')
<div class="page-content">
	<!-- #section:settings.box -->
	<div class="alert alert-block alert-success">
		<button type="button" class="close" data-dismiss="alert">
			<i class="ace-icon fa fa-times"></i>
		</button>
		<!--i class="ace-icon fa fa-check green"></i-->
		友情提示：项目接口子分类信息
	</div>
	@include('public/setbox')
	<div class="row">
		<div class="col-xs-12">
			<!-- PAGE CONTENT BEGINS -->
			<div class="col-sm-6">
				<h3 class="row header smaller lighter blue">
					<span class="col-xs-6"> {{$info['classify']['classifyname'] or ''}}子分类列表 </span>
					<!-- /.col -->

					<span class="col-xs-6"> <span class="pull-right inline"> <span
							class="grey smaller-80 bolder"><a href="{{route('Category.infoSub')}}?classify={{$info['classify']['id']}}">新增子分类</a></span>
					</span>
					</span>
					<!-- /.col -->
				</h3>

				<!-- #section:elements.accordion -->
				<div id="accordion" class="accordion-style1 panel-group">
					@if(!empty($info['sub']))
					@foreach($info['sub'] as $subinfo)
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title clearfix">
								<a class="accordion-toggle collapsed toggleleft" data-toggle="collapse"
									data-parent="#accordion" href="#collapse{{$subinfo['id'] or ''}}"
									aria-expanded="false"> <i
									class="bigger-110 ace-icon fa fa-angle-right"
									data-icon-hide="ace-icon fa fa-angle-down"
									data-icon-show="ace-icon fa fa-angle-right"></i> &nbsp;{{$subinfo['classifyname'] or ''}}
								</a>
								<a class="btn btn-sm btn-primary toggleright"  href="/Category/infoSub?subClassifyId={{$subinfo['id']}}"><i class="ace-icon fa fa-pencil-square-o"></i>编辑</a>
							</h4>
						</div>
						<div class="panel-collapse collapse" id="collapse{{$subinfo['id'] or ''}}"
							aria-expanded="false" style="height: 0px;">
							<div class="panel-body"> {{$subinfo['description'] or ''}}</div>
						</div>
					</div>
					@endforeach
					@endif
				</div>

				<!-- /section:elements.accordion -->
			</div>

		</div>
		<!-- PAGE CONTENT ENDS -->
	</div>
	<!-- /.col -->
</div>
<!-- /.row -->
</div>
<!-- /.page-content -->
@endsection
