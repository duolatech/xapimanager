<!-- basic scripts -->

<!--[if !IE]> -->
<script type="text/javascript">
    window.jQuery || document.write("<script src='{{URL::asset('js/jquery.js')}}'>" + "<" + "/script>");
</script>

<!-- <![endif]-->

<!--[if IE]>
<script type="text/javascript">
    window.jQuery || document.write("<script src='{{URL::asset('js/jquery1x.js')}}'>" + "<" + "/script>");
</script>
<![endif]-->
<script type="text/javascript">
    if ('ontouchstart' in document.documentElement) document.write("<script src='{{URL::asset('js/jquery.mobile.custom.js')}}'>" + "<" + "/script>");
</script>
<script src="{{URL::asset('js/bootstrap.js')}}"></script>

<!-- page specific plugin scripts -->
<script charset="utf-8"
	src="{{URL::asset('js/kindeditor/kindeditor-min.js')}}"></script>
<script charset="utf-8"
	src="{{URL::asset('js/kindeditor/lang/zh_CN.js')}}"></script>
<script src="{{URL::asset('js/bootbox.js')}}"></script>
<!-- ace scripts -->
<script src="{{URL::asset('js/ace/elements.scroller.js')}}"></script>
<script src="{{URL::asset('js/ace/elements.colorpicker.js')}}"></script>
<script src="{{URL::asset('js/ace/elements.fileinput.js')}}"></script>
<script src="{{URL::asset('js/ace/elements.typeahead.js')}}"></script>
<script src="{{URL::asset('js/ace/elements.wysiwyg.js')}}"></script>
<script src="{{URL::asset('js/ace/elements.spinner.js')}}"></script>
<script src="{{URL::asset('js/ace/elements.treeview.js')}}"></script>
<script src="{{URL::asset('js/ace/elements.wizard.js')}}"></script>
<script src="{{URL::asset('js/ace/elements.aside.js')}}"></script>
<script src="{{URL::asset('js/ace/ace.js')}}"></script>
<script src="{{URL::asset('js/ace/ace.ajax-content.js')}}"></script>
<script src="{{URL::asset('js/ace/ace.touch-drag.js')}}"></script>
<script src="{{URL::asset('js/ace/ace.sidebar.js')}}"></script>
<script src="{{URL::asset('js/ace/ace.sidebar-scroll-1.js')}}"></script>
<script src="{{URL::asset('js/ace/ace.submenu-hover.js')}}"></script>
<script src="{{URL::asset('js/ace/ace.widget-box.js')}}"></script>
<script src="{{URL::asset('js/ace/ace.settings.js')}}"></script>
<script src="{{URL::asset('js/ace/ace.settings-rtl.js')}}"></script>
<script src="{{URL::asset('js/ace/ace.settings-skin.js')}}"></script>
<script src="{{URL::asset('js/ace/ace.widget-on-reload.js')}}"></script>
<script src="{{URL::asset('js/ace/ace.searchbox-autocomplete.js')}}"></script>
<script src="{{URL::asset('js/jquery-ui.js')}}"></script>
<!-- inline scripts related to this page -->
<script type="text/javascript">
    $(function () {

        $("#officialnews ul").html('<div class="ace-icon fa fa-spinner fa-spin orange"></div>');
        
        //清除缓存
		 $(".clearcache").on("click",function(){
			 $.ajax({
				 type:"get",
				 cache:false,
				 dataType:"json",
				 url:"{{route('cache.index')}}",
				 success:function(res){
					 if(res.status==200){
						 layer.msg(res.message); 
						 setTimeout(function(){
							 window.location.reload();
						 }, 2000);
					 }else{
						 layer.alert(res.message, {'icon':5,'skin':'layer-ext-moon'}); 
					 }
				 },
				 error:function(msg){
					 layer.alert('网络错误，请稍后重试', 8); 
				 }
			 })
		 })
    })


</script>