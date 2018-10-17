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
				<div class="row row-sm">
					@foreach($data as $value)
                   	<div class="col-lg-3 col-md-4 col-sm-6">
                      <div class="panel b-a">
                        <div class="panel-heading wrapper-xs bg-primary no-border"></div>
                        <div class="wrapper text-center">
                          <h2 class="m-t-none">
                            <span class="text-lt">{{$value['proname'] or ''}}</span>
                          </h2>
                        </div>
                        <ul class="list-group">
                          <li class="list-group-item">
                            <a href="/Sys/env?proid={{$value['id'] or ''}}">
                            	<i class="icon-pencil text-success m-r-xs" target="_blank"></i> 系统环境设置
                            </a>
                          </li>
                          <li class="list-group-item">
                            <a href="/Project/edit?id={{$value['id'] or ''}}" target="_blank">
                            	<i class="icon-pencil text-success m-r-xs"></i> 项目设置
                            </a>
                          </li>
                        </ul>
                        <div class="panel-footer text-center">
                          <a href="/Api/list?proid={{$value['id'] or ''}}" target="_blank" class="btn bg-primary font-bold m">查看该项目接口</a>
                        </div>
                      </div>
                    </div>
                    @endforeach
                </div>
			</div>
			<!-- /content -->
			<script type="text/javascript" charset="utf-8">
    			
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
