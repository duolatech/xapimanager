//分页
function pagination(pageCount){
    var userGroup    = $('select[name="usergroup"]').val();
    var userStatus   = $('select[name="userstatus"]').val();
    var keyword = $('input[name="keyword"]').val();
    $('.M-box').pagination({
        pageCount: pageCount,
        jump: true,
        callback:function(api){
            var data = {
                page: api.getCurrent(),
                userGroup:userGroup,
                userStatus:userStatus,
                keyword:keyword,
            };
            ajaxUser(data,0);
        }
    });
}

//获取接口列表信息, 第一页加载分页插件
function ajaxUser(data, first){

    $(".spiner-loading").show();
    $.ajax({
        cache: false,
        type: "get",
        url:"/users/all",
        data: data,
        headers: {
            'X-CSRF-TOKEN': ''
        },
        dataType: 'json',
        success: function(res) {
            if(res.status==200){
                $(".spiner-loading").hide();
                render("userList", res.data.list, $(".tab-content tbody"));
                pageCount = Math.ceil(res.data.totalCount/20);
                $(".totalCount").text(res.data.totalCount + "个用户")
                if(first==1){
                    pagination(pageCount);
                }
            }else{
                swal("请求出错", "请稍后重试！","error")
            }
        },
        error: function(request) {
            swal("网络错误", "请稍后重试！","error")
        },
    });
}
//搜索接口
$('.search').click(function(){

    var userGroup    = $('select[name="usergroup"]').val();
    var userStatus   = $('select[name="userstatus"]').val();
    var keyword = $('input[name="keyword"]').val();
    ajaxUser({
        search:1,
        userGroup:userGroup,
        userStatus:userStatus,
        keyword:keyword,
    }, 1);
});

//激活用户
$(".table").on("click", ".activate", function(){
    var uid = $(this).attr("uid");
    swal({
            title: "用户激活",
            text: "激活后该用户能访问您的团队下的项目",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonText: "确定",
            cancelButtonText:  "取消"
        },
        function(){
            window.location.href="/users/detail/"+uid;
        });
});

//编辑用户信息
$(".btn-info-submit").on("click", function () {

    var obj = $(this);
    obj.attr('disabled',true);
    var userid = obj.attr("userid");
    var auth = $("select[name='auth']").val();
    var status = $("input[name='status']:checked").val();
    var password = $("input[name='password']").val();

    $(".btn-info-submit").attr('disabled',true);
    $.ajax({
        cache: false,
        type: "POST",
        url:"/users/detail/"+userid,
        data:{
            auth:auth,
            status:status,
            password:md5(password)
        },
        headers: {
            'X-CSRF-TOKEN': ""
        },
        dataType: 'json',
        success: function(res) {
            obj.attr('disabled',false);
            if(res.status==200){
                swal("保存成功", "2s后将返回团队成员列表！","success");
                setTimeout(function(){
                    window.location.href="/users";
                }, 2000);
            }else{
                swal("请求出错", res.message, "error")
            }
        },
        error: function(request) {
            obj.attr('disabled',false);
            swal("网络错误", "请稍后重试！","error")
        }
    });
});

//操作日志分页
function LogPagination(pageCount){

    $('.M-box').pagination({
        pageCount: pageCount,
        jump: true,
        callback:function(api){
            var data = {
                page: api.getCurrent(),
            };
            ajaxOperateLog(data,0);
        }
    });
}

//获取操作日历列表信息, 第一页加载分页插件
function ajaxOperateLog(data, first){

    $.ajax({
        cache: false,
        type: "get",
        url:"/log/all",
        data: data,
        headers: {
            'X-CSRF-TOKEN': ''
        },
        dataType: 'json',
        success: function(res) {
            $(".spiner-loading").show();
            if(res.status==200){
                $(".spiner-loading").hide();
                render("OperateLog", res.data.list, $(".ibox-content tbody"));
                pageCount = Math.ceil(res.data.totalCount/20);
                if(first==1){
                    LogPagination(pageCount);
                }
            }else{
                swal("请求出错", "请稍后重试！","error")
            }
            $('.footable').footable();
            $(".footable-sort-indicator").remove();
            $("th").attr('onclick','').unbind('click');
        },
        error: function(request) {
            swal("网络错误", "请稍后重试！","error")
        },
    });
}
//个人资料修改
if($("#personForm").length>0){

    var validator = $("#personForm").validate({
        submitHandler: function(form) {

            var obj = $(".person-submit");
            obj.attr('disabled',true);
            var username = $('input[name="username"]').val();
            var password = $('input[name="password"]').val();
            var phone = $('input[name="phone"]').val();
            var email = $('input[name="email"]').val();
            var intro = $('textarea[name="intro"]').val();
            $.ajax({
                cache: false,
                type: "POST",
                url: "/users/person/store",
                data:{
                    username:username,
                    password:md5(password),
                    phone:phone,
                    email: email,
                    intro:intro,
                },
                headers: {
                    'X-CSRF-TOKEN': ''
                },
                dataType: 'json',
                success: function(res) {
                    obj.attr('disabled',false);
                    if(res.status==200){
                        swal("修改成功", "2s后将刷新当前页面", "success")
                        setTimeout(function(){
                            window.location.reload()
                        }, 2000);
                    }else{
                        swal("请求出错", res.message, "error")
                    }
                },
                error: function(request) {
                    obj.attr('disabled',false);
                    swal("网络错误", "请稍后重试！","error")
                }
            });
        },
        rules:{
            username:{
                required:true,
                maxlength:20,
                minlength:2,
                remote: {
                    url: "/users/person/check",
                    type: "post",
                    dataType: "json",
                    data: {
                        username: function(){
                            return $('input[name="username"]').val();
                        }
                    },
                    headers: {
                        'X-CSRF-TOKEN': ""
                    },
                }
            },
            password:{
                required:true,
                maxlength:20,
                minlength:6
            },
            phone:{
                required:true,
                phoneCheck:true,
                remote: {
                    url: "/users/person/check",
                    type: "post",
                    dataType: "json",
                    data: {
                        phone: function(){
                            return $('input[name="phone"]').val();
                        }
                    },
                    headers: {
                        'X-CSRF-TOKEN': ""
                    },
                }
            },
            email:{
                required:true,
                email:true,
                remote: {
                    url: "/users/person/check",
                    type: "post",
                    dataType: "json",
                    data: {
                        email: function(){
                            return $('input[name="email"]').val();
                        }
                    },
                    headers: {
                        'X-CSRF-TOKEN': ""
                    }
                }
            }
        },
        messages:{
            username :{
                required:"用户名不能为空",
                maxlength:"不能超过20个字符",
                minlength:"不能少于2个字符",
                remote:"该用户名已被使用",
            },
            password :{
                required:"密码不能为空",
                maxlength:"不能超过20个字符",
                minlength:"不能少于6个字符",
            },
            phone :{
                required : "请输入手机号",
                remote:"该手机号已被使用",
            },
            email :{
                required : "请输入邮箱",
                email : "请输入正确的邮箱",
                remote:"该邮箱已被使用",
            }
        },
        errorElement: 'custom',
        errorClass:'error',
        errorPlacement: function(error, element) {
            error.appendTo(element.next("span"))
        }
    });
    //手机号验证
    jQuery.validator.addMethod("phoneCheck", function(value, element) {
        return this.optional(element) || /^1\d{10}$/.test(value);
    }, "请输入正确的手机号");
}