@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/select.min.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/slider.css')}}">
<link rel="stylesheet" href="{{URL::asset('css/chosen.css')}}">
<link rel="stylesheet" href="{{URL::asset('js/umeditor/themes/default/css/umeditor.css')}}">

<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
		<!-- main -->
		<div class="col">
			<!-- content -->
			<div class="wrapper-md ng-scope">
				<div class="panel panel-default">
					<div class="panel-heading font-bold">分类信息</div>
					<div class="panel-body">
						<form id="myForm" method="post"
							class="form-horizontal ng-pristine ng-valid ng-valid-date ng-valid-required ng-valid-parse ng-valid-date-disabled">
							<div class="m-l">
								<label class="col-sm-2 control-label">项目名称</label>
								<div class="col-sm-6 form-control-static">
									{{$info['proname'] or ''}}
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="m-l">
								<label class="col-sm-2 control-label">环境名称</label>
								<div class="col-sm-6 form-control-static">
									{{$info['envname'] or ''}}
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
							<div class="m-l">
								<label class="col-sm-2 control-label">操作人</label>
								<div class="col-sm-8 form-control-static">
                                     {!!$info['username'] or ''!!}
								</div>
							</div>
							<div class="line line-dashed b-b line-lg pull-in"></div>
                            <div class="m-l">
								<label class="col-sm-2 control-label">操作描述</label>
								<div class="col-sm-8 form-control-static">
                                     {!!$info['desc'] or ''!!}
								</div>
							</div>
                            <div class="form-group" style="margin-top:180px;"></div>
                            
						</form>
					</div>
				</div>
			</div>
			<!-- /content -->
			
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
