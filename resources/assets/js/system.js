
//保存项目
if($("#myForm").length>0){
    var validator = $("#myForm").validate({
        submitHandler: function(form) {
            $(".btn-info-submit").attr('disabled',true);
            var oid = $(".btn-info-submit").attr("oid");
            $.ajax({
                cache: false,
                type: "POST",
                url:"/website/info",
                data:$('#myForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': ""
                },
                dataType: 'json',
                success: function(res) {
                    $(".btn-info-submit").attr('disabled',false);
                    if(res.status==200){
                        swal("保存成功", "","success")
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
            sitename:{
                required:true,
                minlength:2,
            },
            title:{
                required:true,
            }
        },
        messages:{
            sitename:{
                required:"至少为两个字符",
            },
            title:{
                required:"网站标题不能为空",
            }
        },
        errorElement: 'custom',
        errorClass:'error',
        errorPlacement: function(error, element) {
            error.appendTo(element.next("span"))
        }
    });
}
