
$(function(){

    //响应数据格式化
    var gather = $(".goback");
    gather.each(function(){
        var obj = $(this);
        var type = obj.attr("data-type");
        var goback = obj.html();
        switch (type) {
            case "JSON":
                obj.JSONView(goback);
                break;
            case "XML":
                obj.html('<pre style="background-color:#ffffff">'+htmlSpecialChars(vkbeautify.xml(HtmlDecode(goback),4))+'</pre>');
                break;
            case "JSONP":
                obj.html('<pre style="background-color:#ffffff">'+formatJsonp(goback, 4)+'</pre>');
                break;
            case "HTML":
                obj.html('<pre style="background-color:#ffffff">'+goback+'</pre>');
                break;
        }

    });
    //请求raw
    setTimeout(function(){
        if($(".request-table-RAW").length>0){
            $(".request-table-RAW").textareaAutoHeight();
        }
    }, 500);
});
//删除API
$(".deleteApi").on("click",function(){

    var proid = $(this).attr("proid");
    var did = $(this).attr("did");

    swal({
            title: "您确认删除该Api",
            text: "请谨慎操作！",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonText: "确认",
            cancelButtonText: "取消"
        },
        function(){
            $.ajax({
                cache: false,
                type: "POST",
                url:"/manager/"+proid+"/Api/operate",
                data:{did:did},
                headers: {
                    'X-CSRF-TOKEN': ''
                },
                dataType: 'json',
                success: function(res) {
                    if(res.status==200){
                        swal(res.message, "2s后将返回Api列表！","success")
                        setTimeout(function(){
                            window.location.href="/manager/"+proid+"/Api/list";
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
// 发布Api
$(".publish").on("click",function () {
    var proid = $(this).attr("proid");
    var did = $(this).attr("did");
    var publishEnv = $(this).attr("publishEnv");

    swal({
            title: "您确认发布该Api",
            text: "发布Api时，Api将同步到当前环境的上一级环境<br/>Api同步的顺序依次是:<br/>"+publishEnv,
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonText: "发布",
            cancelButtonText: "取消",
            html: true
        },
        function(){
            $.ajax({
                cache: false,
                type: "POST",
                url:"/manager/"+proid+"/Api/publish",
                data:{did:did},
                headers: {
                    'X-CSRF-TOKEN': ''
                },
                dataType: 'json',
                success: function(res) {
                    if(res.status==200){
                        swal(res.message, "请切换到该环境后查看Api列表","success");
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