//分页
function pagination(pageCount){
    $('.M-box').pagination({
        pageCount: pageCount,
        jump: true,
        callback:function(api){
            var data = {
                page: api.getCurrent(),
            };
            ajaxHelpList(data, 0);
        }
    });
}
//获取接口列表信息, 第一页加载分页插件
function ajaxHelpList(data, first){

    $(".spiner-loading").show();
    $.ajax({
        cache: false,
        type: "GET",
        url: "/help",
        data: data,
        dataType: 'json',
        success: function(res) {
            if(res.status==200){
                $(".spiner-loading").hide();
                render("helpList", res.data, $(".helpcenter"));
                console.log(res.data.totalCount);
                pageCount = Math.ceil(res.data.totalCount/20);
                if(first==1){
                    pagination(pageCount);
                }
            }
        },
        error: function() {
            swal("网络错误", "请稍后重试！","error")
        }
    });
}

//批量删除
$(".delete").on("click",function(){
    var obj = $(this);
    var hid = obj.attr("hid");

    swal({
            title: "您确认要删除该文章",
            text: "请谨慎操作！",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonText: "确认",
            cancelButtonText: "取消"
        },
        function(){
            operate({hid:hid})
        });

});

//操作请求
function operate(data) {
    $.ajax({
        cache: false,
        type: "POST",
        url: "/help/operate",
        data: data,
        dataType: 'json',
        success: function(res) {
            if(res.status==200){
                swal(res.message, "2s后将返回帮助中心！","success");
                setTimeout(function(){
                    window.location.href="/help/list"
                }, 2000);
            }else{
                swal("请求出错", res.message, "error")
            }
        },
        error: function() {
            swal("网络错误", "请稍后重试！","error")
        }
    });
}

if($("#HelpForm").length>0){
    var validator = $("#HelpForm").validate({
        submitHandler: function(form) {
            var hid = $("input[name='hid']").val();
            $(".btn-info-submit").attr('disabled',true);
            $.ajax({
                cache: false,
                type: "POST",
                url: "/help/store",
                data:$('#HelpForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': ''
                },
                dataType: 'json',
                success: function(res) {
                    $(".btn-info-submit").attr('disabled',false);
                    if(res.status==200){
                        swal("保存成功", "2s后将返回帮助中心！","success")
                        setTimeout(function(){
                            window.location.href= "/help/list";
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
            title:{
                required:true,
                maxlength:60,
                minlength:2
            },
        },
        messages:{
            title :{
                required:"标题不能为空",
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