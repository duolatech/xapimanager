@extends('base') @section('page-content')
<link rel="stylesheet" href="{{URL::asset('css/nestable.css')}}">

<div class="app-content-body fade-in-up ng-scope" ui-view="">
	<div class="hbox hbox-auto-xs hbox-auto-sm ng-scope">
		<!-- main -->
		<div class="col">
			<!-- content -->
			<div class="wrapper-md ng-scope">
				<div class="panel panel-default">
					<div class="panel-heading font-bold">{{$info['groupname'] or ''}} 功能权限</div>
					<div class="panel-body">
						<form id="myForm" method="post"
							class="form-horizontal ng-pristine ng-valid ng-valid-date ng-valid-required ng-valid-parse ng-valid-date-disabled">
							
							@foreach($sys['ValidMenu'] as $nav)
								@foreach($nav['child'] as $menu)
        							<div class="form-group">
        								<div class="col-sm-10">
                                        	<div ui-jq="nestable" class="dd">
                                        		<ol class="dd-list">
                                        			<li class="dd-item grandfather">
                                        				<div class="dd-handle">
                                        					<label class="checkbox i-checks m-l-xl">
                  												<input type="checkbox" name="rules[]" value="{{$menu['id'] or ''}}" class="ng-pristine ng-untouched ng-valid oneCheck" @if(in_array($menu['id'], $info['rules'])) checked @endif><i></i>
                  												{{$menu['title'] or ''}}
                											</label>
                                        				</div>
                                        				<ol class="dd-list">
                                        					@foreach($menu['child'] as $submenu)
                                        					<li class="dd-item father">
                                        						<div class="dd-handle">
                                        							<label class="checkbox i-checks m-l-xl">
                          												<input type="checkbox" name="rules[]" value="{{$submenu['id'] or ''}}" class="ng-pristine ng-untouched ng-valid twoCheck" @if(in_array($submenu['id'], $info['rules'])) checked @endif><i></i>
                          												{{$submenu['title'] or ''}}
                        											</label>
                                        						</div>
                                        						<ol class="dd-list">
                                        							<li class="dd-item son">
                                        								<div class="dd-handle">
                                        									@if(!empty($sys['Operate'][$submenu['id']]))
                                            									 @foreach($sys['Operate'][$submenu['id']] as $opt)
                                                                                    <label class="checkbox-inline i-checks  m-l-xl">
                                                                                      <input type="checkbox" name="operate[]" value="{{$opt['id'] or ''}}" class="thirdCheck"  @if(in_array($opt['id'], $info['operate'])) checked @endif><i></i> {{$opt['title'] or ''}}
                                                                                    </label>
                                                                                 @endforeach
                                                                            @endif
                                        								</div>
                                        							</li>
                                        						</ol>
                                        					</li>
                                        					@endforeach
                                        				</ol></li>
                                        		</ol>
                                        	</div>
                                        </div>
        							</div>
        							<div class="line line-dashed b-b line-lg pull-in"></div>
								@endforeach
							@endforeach
							<div class="form-group">
                              <div class="col-sm-4 col-sm-offset-2">
                              	<input type="hidden" value="{{ csrf_token() }}" name="_token" />
                              	<input type="hidden" value="{{$info['id'] or ''}}" name="id" />
                                <a href="javascript:;" class="btn btn-primary btn-info-submit">保存权限</a>
                              </div>
                            </div>
                            
                            <div class="form-group" style="margin-top:180px;"></div>
                            
						</form>
					</div>
				</div>
			</div>
			<!-- /content -->
			<script type="text/javascript" charset="utf-8">
				//选中一级checkbox后，子checkbox也全部选中
				$(".oneCheck").click(function(){
					var obj = $(this);
					var checked = obj.is(':checked');
					var node = obj.closest('ol').find('input[type="checkbox"]');
					node.each(function(){
						$(this).prop('checked', checked);
					})
				})
				$(".twoCheck").click(function(){
					//下一级处理
					var obj = $(this);
					var checked = obj.is(':checked');
					var node = obj.closest('li').find('input[type="checkbox"]');
					node.each(function(){
						$(this).prop('checked', checked);
					})
					//上级处理(当twoCheck都没有选中时，oneCheck设置为未选中)
					nodeFather(obj)
				})
				$(".thirdCheck").click(function(){
					var obj = $(this);
					var son = obj.closest('ol').find('input[type="checkbox"]');
					var flag = false;
					son.each(function(){
						if($(this).is(':checked')){
							flag = true
						};
					})
					var father = obj.parents('.father').find('.twoCheck');
					father.prop("checked", flag);
					nodeFather(father);
				})
				//父级节点处理
				function nodeFather(obj){
					var flag = false;
					var brother = obj.closest('ol').find('li');
					brother.each(function(){
						var check = $(this).find('.twoCheck').is(':checked');
						if(check){
							flag = true
						}
					})
					var grandfather = obj.parents('.grandfather').find('.oneCheck');
					grandfather.prop("checked", flag);
				}
				//保存功能权限
				$(".btn-info-submit").click(function(){
					$.ajax({
		                cache: false,
		                type: "POST",
		                url:"{{route('group.featureStore')}}",
		                data:$('#myForm').serialize(),
		                headers: {
		                    'X-CSRF-TOKEN':$("input[name='_token']").val()
		                },
		                dataType: 'json',
		                success: function(res) {
		                	if(res.status==200){
		                		setTimeout(function(){
									 window.location.href="{{route('group.index')}}";
								 }, 2000);
		                	}
		                	layer.msg(res.message);
		                },
		                error: function(request) {
		                    layer.msg("网络错误，请稍后重试");
		                },
		            });
				})
			</script>
		</div>
	</div>
</div>
<!-- /.page-content -->
@endsection
