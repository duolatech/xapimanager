
<style>
.mytable tr {
	border: 1px solid
}

.mytable td {
	border: 1px solid
}
</style>
<p style="line-height: 13px">&nbsp;</p>

<p style="margin-top: 160px; text-align: center;">
	<span style="font-size: 24px;"><span style="font-family: 微软雅黑;">{{$data['classify']['classifyname']
			or ''}}</span></span>
</p>
<p>&nbsp;</p>
<p style="margin-top: 420px; text-align: center; line-height: 150%">
	<strong><span
		style="font-size: 16px; line-height: 150%; font-family: 黑体">{{$data['site']['sitename']
			or ''}}</span></strong>
</p>
<p style="margin-top: 60px; text-align: center; text-indent: 28px">
	<strong><span style="font-family: 黑体">{{$data['time'] or ''}}</span></strong>
</p>
<pagebreak orientation="portrait" type="NEXT-ODD" />
<p>
	<br />
</p>
@foreach($data['info'] as $item)
<p style="margin-left: 20px">
	&nbsp;<span style="font-size: 21px; font-family: 微软雅黑, sans-serif;">1.{{$loop->index
		+ 1}} {{$item['apiname'] or ''}}</span>
</p>
<table width="660" class="mytable" style="margin-left: 30px">
	<tbody>
		<tr class="firstRow">
			<td width="79" valign="top"
				style="border-color: rgb(127, 127, 127); border-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>Api URL</strong>
				</p>
			</td>
			<td width="510" colspan="4" valign="top"
				style="border-top-color: rgb(127, 127, 127); border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-style: none; padding: 0px 7px;">
				<p>{{$item['gateway'] or ''}}</p>
			</td>
		</tr>
		<tr>
			<td width="79" valign="top"
				style="border-top-style: none; border-left-color: rgb(127, 127, 127); border-left-width: 1px; border-bottom-style: none; border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>网络权限</strong>
				</p>
			</td>
			<td width="510" colspan="4" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px;">
				<p>@if($item['network']==2) 外网 @else 内网 @endif</p>
			</td>
		</tr>
		<tr>
			<td width="79" valign="top"
				style="border-color: rgb(127, 127, 127); border-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>接口描述</strong>
				</p>
			</td>
			<td width="510" colspan="4" valign="top"
				style="border-top-color: rgb(127, 127, 127); border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-style: none; padding: 0px 7px;">
				<p>{{$item['description'] or ''}}</p>
			</td>
		</tr>
		@if(!empty($item['param']['HEADER']['request']))
		<tr>
			<td width="79" rowspan="<?php echo count($item['param']['HEADER']['request'])+1;?>" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-left-color: rgb(127, 127, 127); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-top-style: none; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>Header</strong>
				</p>
				<p>
					<strong>&nbsp;</strong>
				</p>
			</td>
			<td width="131" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>名称</strong>
				</p>
			</td>
			<td width="55" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>必选</strong>
				</p>
			</td>
			<td width="97" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>类型及范围</strong>
				</p>
			</td>
			<td width="228" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>说明</strong>
				</p>
			</td>
		</tr>
		@endif
		@foreach($item['param']['HEADER']['request'] as $key=>$value)
		<tr style="height: 35px">
			<td width="131" valign="top"
				style="border-top-color: rgb(127, 127, 127); border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-style: none; padding: 0px 7px;">
				<p>{{$value['field'] or ''}}</p>
			</td>
			<td width="55" valign="top"
				style="border-top-color: rgb(127, 127, 127); border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-style: none; padding: 0px 7px;">
				<p>@if($value['must']==1) 必选 @else 可选 @endif</p>
			</td>
			<td width="97" valign="top"
				style="border-top-color: rgb(127, 127, 127); border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-style: none; padding: 0px 7px;">
				<p>默认值</p>
			</td>
			<td width="228" valign="top"
				style="border-top-color: rgb(127, 127, 127); border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-style: none; padding: 0px 7px;">
				<p>{{$value['des'] or ''}}</p>
			</td>
		</tr>
		@endforeach 
		@foreach($item['param'] as $key=>$myparam)
		@if(in_array($key, array('GET', 'POST', 'PUT', 'DELETE')))
		<tr>
			<td width="79" valign="top"
				style="border-top-style: none; border-left-color: rgb(127, 127, 127); border-left-width: 1px; border-bottom-style: none; border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>请求方式</strong>
				</p>
			</td>
			<td width="510" colspan="4" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px;">
				<p>{{$key or ''}}</p>
			</td>
		</tr>
		<tr>
			<td width="79" rowspan="<?php echo count($myparam['request'])+1;?>" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-left-color: rgb(127, 127, 127); border-right-width: 1px; border-bottom-width: 1px; border-left-width: 1px; border-top-style: none; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>请求参数</strong>
				</p>
				<p>
					<strong>&nbsp;</strong>
				</p>
			</td>
			<td width="131" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>名称</strong>
				</p>
			</td>
			<td width="55" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>必选</strong>
				</p>
			</td>
			<td width="97" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>默认值</strong>
				</p>
			</td>
			<td width="228" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>说明</strong>
				</p>
			</td>
		</tr>
		@foreach($myparam['request'] as $arr)
		
		<tr style="height: 35px">
			<td width="131" valign="top"
				style="border-top-color: rgb(127, 127, 127); border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-style: none; padding: 0px 7px;">
				<p>{{$arr['field'] or ''}}</p>
			</td>
			<td width="55" valign="top"
				style="border-top-color: rgb(127, 127, 127); border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-style: none; padding: 0px 7px;">
				<p>@if($arr['must']==1) 必选 @else 可选 @endif</p>
			</td>
			<td width="97" valign="top"
				style="border-top-color: rgb(127, 127, 127); border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-style: none; padding: 0px 7px;">
				<p>{{$arr['default'] or ''}}</p>
			</td>
			<td width="228" valign="top"
				style="border-top-color: rgb(127, 127, 127); border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-style: none; padding: 0px 7px;">
				<p>{{$arr['des'] or ''}}</p>
			</td>
		</tr>
		@endforeach
		
		
		<tr>
			<td width="79" rowspan="<?php echo count($myparam['response'])+1;?>" valign="top"
				style="border-top-style: none; border-left-color: rgb(127, 127, 127); border-left-width: 1px; border-bottom-style: none; border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>返回结果</strong>
				</p>
			</td>
			<td width="131" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>名称</strong>
				</p>
			</td>
			<td width="55" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>必选</strong>
				</p>
			</td>
			<td width="97" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>类型及范围</strong>
				</p>
			</td>
			<td width="228" valign="top"
				style="border-right-color: rgb(127, 127, 127); border-right-width: 1px; padding: 0px 7px; background: rgb(222, 234, 246);">
				<p>
					<strong>说明</strong>
				</p>
			</td>
		</tr>
		
		@foreach($myparam['response'] as $arr)
		<tr style="height: 35px">
			<td width="131" valign="top"
				style="border-top-color: rgb(127, 127, 127); border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-style: none; padding: 0px 7px;">
				<p>{{$arr['field'] or ''}}</p>
			</td>
			<td width="55" valign="top"
				style="border-top-color: rgb(127, 127, 127); border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-style: none; padding: 0px 7px;">
				<p>@if($arr['must']==1) 必选 @else 可选 @endif</p>
			</td>
			<td width="97" valign="top"
				style="border-top-color: rgb(127, 127, 127); border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-style: none; padding: 0px 7px;">
				<p>{{$arr['default'] or ''}}</p>
			</td>
			<td width="228" valign="top"
				style="border-top-color: rgb(127, 127, 127); border-right-color: rgb(127, 127, 127); border-bottom-color: rgb(127, 127, 127); border-top-width: 1px; border-right-width: 1px; border-bottom-width: 1px; border-left-style: none; padding: 0px 7px;">
				<p>{{$arr['des'] or ''}}</p>
			</td>
		</tr>
		@endforeach
		@endif 
		@endforeach
	</tbody>
</table>
<p class="MsoListParagraph" style="margin-left: 25px; text-indent: 0">
	&nbsp;</p>
<p>
	<br />
</p>
@endforeach
