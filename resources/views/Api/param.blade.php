<div class="request">
<h4>{{$param['type']}}请求参数</h4>
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th class="center">操作</th>
			<th>字段名称</th>
			<th>是否必须</th>
			<th>描述</th>
			<th class="center">默认值</th>
		</tr>
	</thead>
	<tbody>
		@foreach($param['data']['request'] as $value)
    		<tr>
    			<td class="center"><a class="red delNode" href="javascript:void(0);">
    					<i class="ace-icon fa fa-trash-o bigger-130"></i>
    			</a></td>
    			<td><input type="text" name="param[{{$param['type']}}][request][field][]" class="rcol-xs-12 col-sm-12"
    				value="{{$value['field'] or ''}}"></td>
    			<td><select class="rcol-xs-12 col-sm-12 valid" aria-invalid="false" name="param[{{$param['type']}}][request][must][]">
    					<option value="1" @if($value['must']!=2) selected="selected" @endif >是</option>
    					<option value="2" @if($value['must']==2) selected="selected" @endif >否</option>
    			</select></td>
    
    			<td><input type="text" name="param[{{$param['type']}}][request][des][]" class="rcol-xs-12 col-sm-12"
    				value="{{$value['des'] or ''}}"></td>
    			<td><input type="text" name="param[{{$param['type']}}][request][default][]" class="rcol-xs-12 col-sm-12"
    				value="{{$value['default'] or ''}}"></td>
    		</tr>
		@endforeach
	</tbody>
</table>
<span class="btn btn-minier btn-info add-button" type="request"  method="{{$param['type']}}"> <i
	class="glyphicon-plus fa "></i> 增加
</span>
</div>
<div class="response" @if($param['type'] == 'HEADER') style="display:none;" @endif>
<h4>响应参数</h4>
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th class="center">操作</th>
			<th>字段名称</th>
			<th>是否必须</th>
			<th>描述</th>
			<th class="center">默认值</th>
		</tr>
	</thead>
	<tbody>
		@foreach($param['data']['response'] as $value)
    		<tr>
    			<td class="center"><a class="red delNode" href="javascript:void(0);">
    					<i class="ace-icon fa fa-trash-o bigger-130"></i>
    			</a></td>
    			<td><input type="text" name="param[{{$param['type']}}][response][field][]" class="rcol-xs-12 col-sm-12"
    				value="{{$value['field'] or ''}}"></td>
    			<td><select class="rcol-xs-12 col-sm-12 valid" aria-invalid="false" name="param[{{$param['type']}}][response][must][]">
    					<option value="0" @if($value['must']!=2) selected="selected" @endif >是</option>
    					<option value="1" @if($value['must']==2) selected="selected" @endif>否</option>
    			</select></td>
    
    			<td><input type="text" name="param[{{$param['type']}}][response][des][]" class="rcol-xs-12 col-sm-12"
    				value="{{$value['des'] or ''}}"></td>
    			<td><input type="text" name="param[{{$param['type']}}][response][default][]" class="rcol-xs-12 col-sm-12"
    				value="{{$value['default'] or ''}}"></td>
    		</tr>
		@endforeach
	</tbody>
</table>
<span class="btn btn-minier btn-info add-button" type="response" method="{{$param['type']}}"> <i
	class="glyphicon-plus fa "></i> 增加
</span>
</div>