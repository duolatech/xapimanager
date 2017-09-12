<div class="app-footer wrapper b-t bg-light ng-scope">
	<span class="pull-right ng-binding versionInfo"><a href=""
		class="m-l-sm text-muted" target="_blank"><label class="text-success">v2.0 </label><i class="fa fa-long-arrow-up" style="display: none;"></i></a></span>
	Copyright © 2017 
	<a href="http://xapi.smaty.net" target="_blank" style="color: #428bca;text-decoration:underline;">
		xApi Manager
	</a> All Rights Reserved.
</div>
<script type="text/javascript" charset="utf-8">
//新版本检查
$.get("/Sys/update",function(res,status){
	var checkUpdate = $.cookie('checkUpdate',1,{expires:7,path:'/'});
	if(status=='success' && res.status==200){
		var version = $(".versionInfo");
		version.find('label').text(res.message);
		version.find('a').prop('href', res.data.url);
		version.find('i').show();
	}
});
</script>