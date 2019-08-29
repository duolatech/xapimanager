
$(function(){

    $(".navbar-right li").eq(1).on("mouseleave", function () {
        $(this).removeClass("open")
    })
    //菜单点击
    $(".J_menuItem").on('click',function(){

        var reg = /^\/manager\/\d+$/g;
        var url = $(this).attr('href');
        if(!reg.test(url)){
            $("#J_iframe").attr('src',url);
        }
        return false;
    });

    //环境切换
    $(".all_env li").on('click', function(){
        var proid = $(this).find('a').attr('proid');
        var envid = $(this).find('a').attr('envid');
        var env_name = $(this).text();
        $(".current_env span").replaceWith('<span envid="'+envid+'">'+env_name+'</span>');
        $(".all_env li").show();
        $(this).hide();
        envChange(proid, envid)
    });

    function envChange(proid, envid) {
        $.ajax({
            cache: false,
            type: "POST",
            url:"/project/envchange",
            data:{
                proid:proid,
                envid:envid,
            },
            headers: {
                'X-CSRF-TOKEN': ""
            },
            dataType: 'json',
            success: function(res) {
                if(res.status!=200){
                    swal("请求出错", res.message, "error")
                }else{
                    refresh();
                }
            },
            error: function(request) {
                swal("网络错误", "请稍后重试！","error")
            }
        });
    }
    //全屏显示
    var btn = document.getElementById('fullscreen');
    var content = document.getElementById('wrapper-app');
    if(btn){
        btn.onclick = function() {
            if($(this).data.status=='on'){
                $(this).data.status = 'off';
                exitFullScreen(content);
            }else{
                $(this).data.status = 'on';
                fullScreen(content);
            }
        }
    }
    //获取未读消息,每五分钟获取一次
    GetUnread();
    window.setInterval(function(){
        GetUnread();
    }, 300000);

});
/*!
* 全屏展示、退出
*/
function fullScreen(el) {
    var rfs = el.requestFullScreen || el.webkitRequestFullScreen || el.mozRequestFullScreen || el.msRequestFullScreen,
        wscript;

    if(typeof rfs != "undefined" && rfs) {
        rfs.call(el);
        return;
    }

    if(typeof window.ActiveXObject != "undefined") {
        wscript = new ActiveXObject("WScript.Shell");
        if(wscript) {
            wscript.SendKeys("{F11}");
        }
    }
}

function exitFullScreen(el) {
    var el= document,
        cfs = el.cancelFullScreen || el.webkitCancelFullScreen || el.mozCancelFullScreen || el.exitFullScreen,
        wscript;

    if (typeof cfs != "undefined" && cfs) {
        cfs.call(el);
        return;
    }

    if (typeof window.ActiveXObject != "undefined") {
        wscript = new ActiveXObject("WScript.Shell");
        if (wscript != null) {
            wscript.SendKeys("{F11}");
        }
    }
}
//刷新当前页面
function refresh(){
    url = $("#J_iframe").attr('src');
    $("#J_iframe").attr('src',url);
}
//获取未读消息
function GetUnread(){
    $.get("/message/unread", function(res){
        var obj = $(".unreadMessage");
        if(res.status==200 && res.data.count>0){
            obj.text(res.data.count);
            obj.show();
        }
    });
}

//清除缓存
$(".clearCache").on("click",function(){
    $.getJSON("/cache/clear", function(data){
        if(data.status==200){
            alert("清除缓存成功");
        }
    })
});

//退出登录
$(".userlogout").on("click", function () {
    $.getJSON("/logout", function(data){
        if(data.status==200){
            window.location.href="/login"
        }
    })
});