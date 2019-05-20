 var con = {};
    //分页
    function pagination(pageCount){
        $('.M-box').pagination({
            pageCount: pageCount,
            jump: true,
            callback:function(api){
                var data = {
                    page: api.getCurrent()
                };
                data = $.extend(data, con);
                ajaxApiList(data, 0);
            }
        });
    }
    //获取接口列表信息, 第一页加载分页插件
    function ajaxApiList(data, first){

        $(".spiner-loading").show();
        $.ajax({
            cache: false,
            type: "GET",
            url: $("input[name='ajaxCompanyUrl']").val(),
            data: data,
            dataType: 'json',
            success: function(res) {
                if(res.status==200){
                    $(".spiner-loading").hide();
                    $(".totalCount").text(res.data.totalCount+'个密钥');
                    render("companyList", res.data, $(".tab-content tbody"));
                    pageCount = Math.ceil(res.data.totalCount/20);
                    if(first==1){
                        pagination(pageCount);
                    }
                }
            },
            error: function() {
                swal("网络错误", "请稍后重试！","error")
            },
        });
    }
    //搜索
    $(".btn-search").on("click", function(){
        con = {
            "company":$("input[name='comapany']").val()
        };
        var data = $.extend({page:1}, con);
        ajaxApiList(data,1);
    });
    //随机获取appId、appSecret
    $(".random-button").click(function(){
        $('input[name="appId"]').val(
            randomWord(true, 6,12)
        );
        $('input[name="appSecret"]').val(
            randomWord(true, 32,48)
        );
    });
    //创建企业秘钥表单验证
    if($("#myForm").length>0){
        var validator = $("#myForm").validate({
            submitHandler: function(form) {
                var url = $(".btn-info-submit").attr('url');
                $(".btn-info-submit").attr('disabled',true);
                $.ajax({
                    cache: false,
                    type: "POST",
                    url: url,
                    data:$('#myForm').serialize(),
                    headers: {
                        'X-CSRF-TOKEN': ''
                    },
                    dataType: 'json',
                    success: function(res) {
                        $(".btn-info-submit").attr('disabled',false);
                        if(res.status==200){
                            swal("保存成功", "2s后将返回企业密钥列表！","success")
                            setTimeout(function(){
                                window.location.href="/manager/"+res.data.proid+"/company";
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
                company:{
                    required:true,
                    maxlength:60,
                    minlength:2
                },
                appId:{
                    required:true,
                },
                appSecret:{
                    required:true,
                    maxlength:48,
                    minlength:6
                }
            },
            messages:{
                company :{
                    required:"公司名称不能为空",
                    maxlength:"不能超过60个字符",
                    minlength:"不能少于2个字符",
                },
                appId :{
                    required:"appId不能为空",
                },
                appSecret :{
                    required:"appSecret不能为空",
                    maxlength:"不能超过48个字符",
                    minlength:"不能少于6个字符",
                }

            },
            errorElement: 'custom',
            errorClass:'error',
            errorPlacement: function(error, element) {
                error.appendTo(element.next("span"))
            },
        });
    }
    //删除企业密钥
    $(".tab-content").on("click", ".delCompany",function(){
        var id = $(this).attr("id");
        var proid = $(this).attr("proid");
        swal({
                title: "您确认要删除该企业密钥？",
                text: " ",
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
                    url: "/manager/"+proid+"/company/operate",
                    data:{id:id},
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
