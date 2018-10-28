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
				<div class="col-sm-10">
					@foreach($info as $value)
					<div class="panel panel-default ng-isolate-scope"
						heading="Dynamic body content">
						<div class="panel-heading">
							<h4 class="panel-title">
								<div href="" class="accordion-toggle" accordion-transclude="heading">
									<span class="ng-binding">
										{{$value['classifyname']}}
									</span> 
									<span class="pull-right"> 
										<a href="/Category/info?classifyId={{$value['id']}}" title="编辑"><i class="fa fa-pencil fa-fw m-r-xs"></i></a>&nbsp;&nbsp;
										<a href="/Category/infoSub?classifyId={{$value['id']}}" title="添加子分类"><i class="fa fa-plus fa-fw m-r-xs"></i></a>&nbsp;&nbsp;
										<a href="/Category/detail?classifyId={{$value['id']}}" title="查看详情"><i class="fa fa-angle-right fa-fw m-r-xs"></i></a>
									</span>
								</div>
							</h4>
						</div>
						<div class="panel-collapse collapse in" collapse="!isOpen"
							style="height: auto;">
							<div class="panel-body" ng-transclude="">
								@foreach($value['child'] as $vol)
									<a href="javascript:void(0);" class="btn btn-default btn-sm ng-scope subClassify" id="{{$vol['id']}}">{{$vol['classifyname']}}</a>
								@endforeach
								<div class="list-group m-t ng-scope">
									@foreach($value['child'] as $vol)
    									<div  class="list-group-item ng-binding ng-scope">
    										{{$vol['classifyname']}} 
    										<span class="pull-right">
    											<a href="/Category/detailSub?subClassifyId={{$vol['id']}}" class="btn btn-sm btn-success">查看</a>
    											<a href="javascript:;" cid="{{$vol['id']}}" class="btn btn-sm btn-primary btn-export">导出</a>
    											<a href="/Category/infoSub?subClassifyId={{$vol['id']}}" class="btn btn-sm btn-info">编辑</a>
    										</span>
    									</div>
									@endforeach
								</div>
							</div>
						</div>
					</div>
					@endforeach
				</div>
			</div>
			<!-- /content -->
			<script type="text/javascript" charset="utf-8">
				$(".subClassify").click(function(){
					var id = $(this).attr('id');
					window.location.href='/Api/list?type=search&subClassify='+id;
				})
				//导出word文档
				$(".btn-export").click(function(){
					var cid = $(this).attr('cid');
					window.location.href='/Export/v1/subClassify/'+cid;
				})
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
