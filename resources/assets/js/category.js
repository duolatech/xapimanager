
if($("#categoryForm").length>0){
    var validator = $("#categoryForm").validate({
        submitHandler: function(form) {
            var proid = $("input[name='proid']").val();
            var url = "/manager/"+proid+"/category/store";
            $(".btn-info-submit").attr('disabled',true);
            $.ajax({
                cache: false,
                type: "POST",
                url: url,
                data:$('#categoryForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': ''
                },
                dataType: 'json',
                success: function(res) {
                    $(".btn-info-submit").attr('disabled',false);
                    if(res.status==200){
                        swal("保存成功", "2s后将返回Api分类列表！","success")
                        setTimeout(function(){
                            window.location.href= res.data.cateListUrl;
                        }, 2000);
                    }else{
                        swal("请求出错", res.message, "error")
                    }
                },
                error: function(request) {
                    $(".btn-info-submit").attr('disabled',false);
                    swal("网络错误", "请稍后重试！","error")
                }
            });
        },
        rules:{
            classify:{
                required:true,
                maxlength:60,
                minlength:2
            },
        },
        messages:{
            classify :{
                required:"分类名称不能为空",
                maxlength:"不能超过60个字符",
                minlength:"不能少于2个字符",
            },
        },
        errorElement: 'custom',
        errorClass:'error',
        errorPlacement: function(error, element) {
            error.appendTo(element.next("span"))
        },
    });
}
//删除分类
$(".delCategory").on("click", function(){
    var cateId = $(this).attr("cateId");
    var proid = $(this).attr("proid");
    swal({
            title: "您确认要删除该分类？",
            text: "请确保该分类下无子分类",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonText: "确定",
            cancelButtonText: "取消"
        },
        function(){
            $.ajax({
                cache: false,
                type: "POST",
                url: "/manager/"+proid+"/category/operate",
                data:{cateId:cateId},
                headers: {
                    'X-CSRF-TOKEN': ''
                },
                dataType: 'json',
                success: function(res) {
                    if(res.status==200){
                        swal(res.message, "2s后将刷新当前页面！","success")
                        setTimeout(function(){
                            window.location.reload();
                        }, 2000);
                    }else{
                        swal("请求出错", res.message, "error")
                    }
                },
                error: function(request) {
                    swal("网络错误", "请稍后重试！","error")
                }
            });
        });
});