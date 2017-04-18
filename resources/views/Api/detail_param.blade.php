<div class="request">
<h4>{{$param['type']}}请求参数</h4>
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>字段名称</th>
			<th>是否必须</th>
			<th>描述</th>
			<th class="center">默认值</th>
		</tr>
	</thead>
	<tbody>
		@foreach($param['data']['request'] as $value)
    		<tr>
    			<td>{{$value['field'] or ''}}</td>
    			<td>
    					@if($value['must']!=2) 是 @endif
    					@if($value['must']==2) 否 @endif
    			</td>
    			<td>{{$value['des'] or ''}}</td>
    			<td>{{$value['default'] or ''}}</td>
    		</tr>
		@endforeach
	</tbody>
</table>
</div>
<div class="response" @if($param['type'] == 'HEADER') style="display:none;" @endif>
<h4>响应参数</h4>
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>字段名称</th>
			<th>是否必须</th>
			<th>描述</th>
			<th class="center">默认值</th>
		</tr>
	</thead>
	<tbody>
		@foreach($param['data']['response'] as $value)
    		<tr>
    			<td>{{$value['field'] or ''}}</td>
    			<td>
    					@if($value['must']!=2) 是 @endif
    					@if($value['must']==2) 否 @endif
    			</td>
    			<td>{{$value['des'] or ''}}</td>
    			<td>{{$value['default'] or ''}}</td>
    		</tr>
		@endforeach
	</tbody>
</table>
</div>