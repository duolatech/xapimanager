//分页
function pagination(pageCount){
    $('.M-box').pagination({
        pageCount: pageCount,
        jump: true,
        callback:function(api){
            var data = {
                page: api.getCurrent(),
            };
            ajaxMessageList(data, 0);
        }
    });
}
//获取接口列表信息, 第一页加载分页插件
function ajaxMessageList(data, first){

    $(".spiner-loading").show();
    $.ajax({
        cache: false,
        type: "GET",
        url: "/message",
        data: data,
        dataType: 'json',
        success: function(res) {
            if(res.status==200){
                $(".spiner-loading").hide();
                $(".totalCount").text(res.data.totalCount);
                render("MessageList", res.data, $(".tab-content tbody"));
                pageCount = Math.ceil(res.data.totalCount/20);
                if(first==1){
                    pagination(pageCount);
                }
            }
            $('.i-checks').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
            });
        },
        error: function() {
            swal("网络错误", "请稍后重试！","error")
        }
    });
}

//刷新
$(".refresh").on("click", function () {
    ajaxMessageList({page:1}, 1);
});

//标记为已读
$(".read").on("click",function(){
    var mids = [];
    $('input[name="portion"]:checked').each(function (i, item) {
        obj = $(this);
        mids.push(obj.attr("mid"))
    });
    if (mids.length==0){
        swal("请选择要标记为已读的内容", "","info")
    }else{
        data = {"status":1,"mids":mids.toString()}
        operate(data)
    }

});
//批量删除
$(".delete").on("click",function(){
    var mids = [];
    $('input[name="portion"]:checked').each(function (i, item) {
        obj = $(this);
        mids.push(obj.attr("mid"))
    });
    if (mids.length==0){
        swal("请选择要删除的内容", "","info")
    }else{
        swal({
                title: "您确认要删除选中的消息",
                text: "",
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
                confirmButtonText: "确认",
                cancelButtonText: "取消"
            },
            function(){
                data = {"status":2,"mids":mids.toString()}
                operate(data)
            });
    }
});

//操作请求
function operate(data) {
    $.ajax({
        cache: false,
        type: "POST",
        url: "/message/operate",
        data: data,
        dataType: 'json',
        success: function(res) {
            if(res.status==200){
                swal(res.message, "2s后将刷新本页面！","success");
                setTimeout(function(){
                    window.location.reload()
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