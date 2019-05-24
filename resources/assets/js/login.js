var sliderNum = "";
$(function () {
    if ($("#slider").length>0){
        var slider = new SliderUnlock("#slider",{
            successLabelTip : "验证成功"
        },function(){
            sliderNum = $("#slider_label").width();
            $(".sliderValide").text("");
            //以下四行设置恢复初始，不需要可以删除
            setTimeout(function(){
                resetSlider()
            },120000);
            slider.init();
        });
        slider.init();
    }
});
//重置验证码
function resetSlider() {
    $("#slider_labelTip").html("请重新拖动滑块验证");
    $("#slider_labelTip").css("color","#787878");
    sliderNum = ""
}
//登录
if($("#loginForm").length>0){
    var validator = $("#loginForm").validate({
        submitHandler: function(form) {
            $(".login-submit").attr('disabled',true);
            var user = $('input[name="user"]').val();
            var pass = $('input[name="pass"]').val();
            $.ajax({
                cache: false,
                type: "POST",
                url: "/userlogin",
                data:{
                    user: user,
                    pass: md5(pass),
                    sliderNum: sliderNum
                },
                headers: {
                    'X-CSRF-TOKEN': ''
                },
                dataType: 'json',
                success: function(res) {
                    $(".login-submit").attr('disabled',false);
                    if(res.status==200){
                        //swal("登录成功", "即将跳转到首页","success");
                        setTimeout(function(){
                            window.location.href="/main";
                        }, 500);
                    }else{
                        swal("请求出错", res.message, "error")
                    }
                },
                error: function(request) {
                    $(".login-submit").attr('disabled',false);
                    swal("网络错误", "请稍后重试！","error")
                }
            });
        },
        rules:{
            user:{
                required:true,
                maxlength:60,
                minlength:2
            },
            pass:{
                required:true,
                maxlength:32,
                minlength:6
            },
        },
        messages:{
            user :{
                required:"用户名/手机号/邮箱不能为空",
                maxlength:"不能超过60个字符",
                minlength:"不能少于2个字符",
            },
            pass :{
                required:"密码不能为空",
                maxlength:"不能超过32个字符",
                minlength:"不能少于6个字符",
            },

        },
        errorElement: 'custom',
        errorClass:'error',
        errorPlacement: function(error, element) {
            error.appendTo(element.next("span"))
        },
    });
}
//找回密码
if($("#forgetForm").length>0){
    var validator = $("#forgetForm").validate({
        submitHandler: function(form) {

            $(".forget-submit").attr('disabled',true);
            swal({
                title: "发送中，请稍等……！",
                text: "",
                timer: 2000,
                showConfirmButton: false
            });
            var email = $('input[name="email"]').val();
            $.ajax({
                cache: false,
                type: "POST",
                url: "/forget",
                data:{
                    email: email,
                    sliderNum: sliderNum
                },
                headers: {
                    'X-CSRF-TOKEN': ''
                },
                dataType: 'json',
                success: function(res) {
                    $(".forget-submit").attr('disabled',false);
                    if(res.status==200){
                        swal("发送成功", res.message, "success")
                    }else{
                        swal("请求出错", res.message, "error")
                    }
                },
                error: function(request) {
                    $(".forget-submit").attr('disabled',false);
                    swal("网络错误", "请稍后重试！","error")
                }
            });
        },
        rules:{
            email:{
                required:true,
                email:true
            }
        },
        messages:{
            email :{
                required : "请输入邮箱",
                email : "请输入正确的邮箱"
            }

        },
        errorElement: 'custom',
        errorClass:'error',
        errorPlacement: function(error, element) {
            error.appendTo(element.next("span"))
        }
    });
}
//找回密码
if($("#resetForm").length>0){
    var validator = $("#resetForm").validate({
        submitHandler: function(form) {
            var obj = $(".reset-submit");
            obj.attr('disabled',true);
            var pass = $('input[name="pass"]').val();
            var repass = $('input[name="repass"]').val();
            var resetinfo = $('input[name="resetinfo"]').val();
            $.ajax({
                cache: false,
                type: "POST",
                url: "/forget/reset",
                data:{
                    pass: md5(pass),
                    repass:md5(repass),
                    resetinfo:resetinfo,
                    sliderNum: sliderNum
                },
                headers: {
                    'X-CSRF-TOKEN': ''
                },
                dataType: 'json',
                success: function(res) {
                    obj.attr('disabled',false);
                    if(res.status==200){
                        swal("密码修改成功", "2s后将跳转到登录页", "success")
                        setTimeout(function(){
                            window.location.href="/login"
                        }, 2000);

                    }else{
                        swal({
                                title: "请求出错",
                                text: res.message,
                                type: "error",
                                showCancelButton: false,
                                closeOnConfirm: false,
                                showLoaderOnConfirm: true,
                                confirmButtonText: "重新获取"
                            },
                            function(){
                                window.location.href="/forget"
                            });
                    }
                },
                error: function(request) {
                    obj.attr('disabled',false);
                    swal("网络错误", "请稍后重试！","error")
                }
            });
        },
        rules:{
            pass:{
                required:true,
                maxlength:32,
                minlength:6
            },
            repass:{
                equalTo: "#pass"
            }
        },
        messages:{
            pass :{
                required : "请输入密码",
                maxlength : "不能超过32个字符",
                minlength: "不能少于6个字符"
            },
            repass:{
                equalTo:"两次密码不一致"
            }

        },
        errorElement: 'custom',
        errorClass:'error',
        errorPlacement: function(error, element) {
            error.appendTo(element.next("span"))
        }
    });
}
//用户注册
if($("#registerForm").length>0){

    var validator = $("#registerForm").validate({
        submitHandler: function(form) {

            var obj = $(".register-submit");
            obj.attr('disabled',true);
            var username = $('input[name="username"]').val();
            var password = $('input[name="password"]').val();
            var phone = $('input[name="phone"]').val();
            var email = $('input[name="email"]').val();
            var identify = $('input[name="identify"]').val();
            $.ajax({
                cache: false,
                type: "POST",
                url: "/register",
                data:{
                    username:username,
                    password:md5(password),
                    phone:phone,
                    email: email,
                    identify:identify,
                    sliderNum: sliderNum
                },
                headers: {
                    'X-CSRF-TOKEN': ''
                },
                dataType: 'json',
                success: function(res) {
                    obj.attr('disabled',false);
                    if(res.status==200){
                        swal("好帅的操作，恭喜您注册成功", "2s后将跳转到登录页", "success")
                        setTimeout(function(){
                            window.location.href="/login"
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
                maxlength:32,
                minlength:2,
                remote: {
                    url: "/register/check",
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
                    url: "/register/check",
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
                    url: "/register/check",
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
                maxlength:"不能超过32个字符",
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