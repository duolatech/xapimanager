$(function(){
    //修改团队信息
    $(".wrapper").on("click", ".organize_modify", function(){
        var oid = $(this).attr("oid");
        window.location.href="/organize/detail/"+oid;
    })
    //邀请分享
    $(".wrapper").on("click", ".invite", function(){
        var invite = $(this).attr("invite")
        swal({
                title: "",
                imageUrl: "/assets/img/team.png",
                imageSize: "280x260",
                text: "分享链接邀请成员加入您的团队，享受协同工作的乐趣！",
                type: "input",
                showCancelButton: true,
                closeOnConfirm: false,
                animation: "slide-from-top",
                inputValue: invite,
                showCancelButton:true,
                closeOnConfirm:false,
                confirmButtonText: "复制",
                cancelButtonText:  "取消",
            },
            function(){

                $(".sweet-alert").find("input").attr("id", "id_text");
                $(".confirm").attr("data-clipboard-target","#id_text");
                $(".confirm").attr("data-clipboard-action","copy");

                //实例化 ClipboardJS对象;
                var copyBtn = new ClipboardJS('.confirm');

                copyBtn.on("success",function(e){
                    swal("复制成功！", "", "success");
                });
                copyBtn.on("error",function(e){
                    swal("复制失败", "", "error");
                });

            });
    })
    //团队搜索
    $(".osearch button").on("click",function (){

        identify = $.trim($(".osearch input").val());
        if(identify.length<=0){
            return
        }
        $.ajax({
            cache: false,
            type: "GET",
            url:"/organize/search/"+identify,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': ''
            },
            success: function(res) {
                if(res.status==200){
                    render("organizeSearch", res.data, $("#seachresult"));
                }else{
                    swal("请求出错", "请稍后重试！","error")
                }
            },
            error: function(request) {
                swal("网络错误", "请稍后重试！","error")
            },
        });
        return false;
    })
    //加入团队
    $(".wrapper").on("click", ".organizeJoin", function(){

        var identify = $(this).attr("identify");
        $.ajax({
            cache: false,
            type: "POST",
            url:"/organize/addition/"+identify,
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': ''
            },
            success: function(res) {
                if(res.status==200){
                    swal("加入成功", "2s后将刷新当前页面","success")
                    setTimeout(function(){
                        window.location.reload();
                    }, 2000);
                }else{
                    swal("请求出错", res.message, "error")
                }
            },
            error: function(request) {
                swal("网络错误", "请稍后重试！","error")
            },
        });
        return false;
    });
    //退出团队
    $(".wrapper").on("click", ".orgquit", function () {

        var oid = $(this).attr("oid");
        swal({
                title: "您确认要退出该团队？",
                text: "退出后该团队下的项目，您将无权查看",
                type: "info",
                showCancelButton: true,
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
                confirmButtonText: "确定",
                cancelButtonText:  "取消",
            },
            function(){
                $.ajax({
                    cache: false,
                    type: "POST",
                    url:"/organize/quit/"+oid,
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': ''
                    },
                    success: function(res) {
                        if(res.status==200){
                            swal("退出成功", "2s后将刷新当前页面！","success")
                            setTimeout(function(){
                                window.location.reload();
                            }, 2000);
                        }else{
                            swal("请求出错", res.message, "error")
                        }
                    },
                    error: function(request) {
                        swal("网络错误", "请稍后重试！","error")
                    },
                });
            });

        return false;
    });
    if($("#myForm").length>0){
        //编辑团队信息
        var validator = $("#myForm").validate({
            submitHandler: function(form) {
                $(".btn-info-submit").attr('disabled',true);
                var oid = $(".btn-info-submit").attr("oid");
                $.ajax({
                    cache: false,
                    type: "POST",
                    url:"/organize/detail/"+oid,
                    data:$('#myForm').serialize(),
                    headers: {
                        'X-CSRF-TOKEN': ""
                    },
                    dataType: 'json',
                    success: function(res) {
                        $(".btn-info-submit").attr('disabled',false);
                        if(res.status==200){
                            swal("保存成功", "2s后将返回团队页面！","success")
                            setTimeout(function(){
                                window.location.href="/organize";
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
                organize_name:{
                    required:true,
                    maxlength:20,
                    minlength:2
                },
                organize_desc:{
                    required:true,
                    maxlength:100,
                    minlength:2
                }
            },
            messages:{
                organize_name :{
                    required:"团队名不能为空",
                    maxlength:"不能超过20个字符",
                    minlength:"不能少于2个字符"
                },
                organize_desc :{
                    required:"团队口号不能为空",
                    maxlength:"不能超过100个字符",
                    minlength:"不能少于2个字符"
                }
            },
            errorElement: 'custom',
            errorClass:'error',
            errorPlacement: function(error, element) {
                error.appendTo(element.next("span"))
            }
        })
    }

});