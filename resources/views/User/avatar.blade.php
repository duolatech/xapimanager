<link rel="stylesheet" type="text/css" href="{{URL::asset('js/uploadifive/uploadifive.css')}}">
<script type="text/javascript" src="{{URL::asset('js/uploadifive/jquery.uploadifive.js')}}"></script>

<div class="mg-t20">
	<div class="q-headerup">
		<div class="headerbox clearfix">
			<div class="headerbig" id="queue">
				<span class="up">点击上传头像</span>
				<span class="up1">仅支持2MB以下的图片文件</span>
				<div class="filebox">
					<input type="file" class="file" id="file_upload">
					<input type="hidden" value="{{ csrf_token() }}" name="_token" />
				</div>
				<div class="headerimg">
					<img src="{{$info['user']['avatar'] or ''}}" class="uploadPic img-responsive q-hide" id="cropbox">
				</div>
			</div>
		</div>
	</div>
</div>
<div>
	<span class="help-inline"> 
		<span class="againUpload">重新上传</span>
	</span>
</div>
<!-- 第一部分 End-->
<script type="text/javascript">
		
		$(function() {
			
			//图片上传
			$('#file_upload').uploadifive({
				'auto'             : true,
				'checkScript'      : false,
				'fileSizeLimit'    : '2MB',
				'fileType'         : 'image/*',
				'formData'         : {
									   'classify'  : 'avatar',
									   'classifyName' : '头像',
									   'uploadType'   : 'picture',
									   '_token'   : $("input[name='_token']").val(),
				                     },
				'buttonText'       : '上传图片',
				'queueID'          : 'queue',
				'uploadScript'     : '{{route("upload")}}',
				'itemTemplate'     : false, 
				'multi'			   : false,
				'width'            : 198,
				'height'           : 198,
				'onProgress'   : function(file, event) {
					$(".uploadPic").attr("src", "/images/loading.gif?"+Math.random())
				},
				'onError'          : function(errorType) {
					if(errorType=='FORBIDDEN_FILE_TYPE'){
						layer.alert('不支持该文件类型的上传');
					}else if(errorType=='FILE_SIZE_LIMIT_EXCEEDED'){
						layer.alert('文件超过限制大小，最大可上传2M');
					}else{
						layer.alert(errorType);
					}
				}, 
				'onUploadComplete' : function(file, data) { 
					//上传后载图
					var dataJson = JSON.parse(data);
					$(".uploadPic").attr("src",dataJson.info.avatar+'?'+Math.random()).removeClass('q-hide').addClass('q-show2');
					
					$(".up").addClass('q-hide');
					$(".uploadPic").show();
					
				}
			});
			
		});
		
		$(".againUpload").click(function(){
			
			$(".up").show();
			$(".uploadPic").hide();
		})
	</script>