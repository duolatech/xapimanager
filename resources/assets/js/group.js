//编辑团队信息
if($("#myForm").length>0){
    var validator = $("#myForm").validate({
        submitHandler: function(form) {
            $(".btn-info-submit").attr('disabled',true);
            var oid = $(".btn-info-submit").attr("oid");
            $.ajax({
                cache: false,
                type: "POST",
                url:"/group/info",
                data:$('#myForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': ""
                },
                dataType: 'json',
                success: function(res) {
                    $(".btn-info-submit").attr('disabled',false);
                    if(res.status==200){
                        swal("保存成功", "2s后进入功能权限设置页面！","success");
                        setTimeout(function(){
                            window.location.href="/group/featureAuth/"+res.data.group_id;
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
            groupname:{
                required:true,
                maxlength:20,
                minlength:2
            },
            description:{
                required:true,
                maxlength:300,
                minlength:2
            }
        },
        messages:{
            groupname :{
                required:"权限组名不能为空",
                maxlength:"不能超过20个字符",
                minlength:"不能少于2个字符"
            },
            description :{
                required:"权限组描述不能为空",
                maxlength:"不能超过300个字符",
                minlength:"不能少于2个字符"
            }
        },
        errorElement: 'custom',
        errorClass:'error',
        errorPlacement: function(error, element) {
            error.appendTo(element.next("span"))
        }
    });
}

$(".groupDelete").on("click",function(){
    var gid = $(this).attr("gid");
    console.log(gid)
    swal({
            title: "您确定要删除该权限组？",
            text: "仅能删除未与用户绑定的权限组",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
        },
        function(){
            $.ajax({
                cache: false,
                type: "POST",
                url:"/group/operate/"+gid,
                dataType: 'json',
                headers: {
                    'X-CSRF-TOKEN': ''
                },
                success: function(res) {
                    if(res.status==200){
                        swal("删除成功", " ","success")
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
});
//功能权限节点选择
//选中一级checkbox后，子checkbox也全部选中
$(".oneCheck").click(function(){
    var obj = $(this);
    var checked = obj.is(':checked');
    var node = obj.closest('li').find('input[type="checkbox"]');
    node.each(function(){
        $(this).prop('checked', checked);
    })
})
$(".twoCheck").click(function(){
    //下一级处理
    var obj = $(this);
    var checked = obj.is(':checked');
    var node = obj.closest('li').find('input[type="checkbox"]');
    node.each(function(){
        $(this).prop('checked', checked);
    })
    //上级处理(当twoCheck都没有选中时，oneCheck设置为未选中)
    nodeFather(obj)
})
// $(".thirdCheck").click(function(){
//     var obj = $(this);
//     var son = obj.closest('ol').find('input[type="checkbox"]');
//     var flag = false;
//     son.each(function(){
//         if($(this).is(':checked')){
//             flag = true
//         };
//     })
//     var father = obj.parents('.father').find('.twoCheck');
//     father.prop("checked", flag);
//     nodeFather(father);
// })
$(".fourCheck").click(function(){
    var obj = $(this);
    var son = obj.closest('ol').find('input[type="checkbox"]');
    var flag = false;
    son.each(function(){
        if($(this).is(':checked')){
            flag = true
        };
    })
    console.log(flag);
    var father = obj.parents('.father').find('.oneCheck');
    father.prop("checked", flag);
    nodeFather(father);
})
//父级节点处理
function nodeFather(obj){
    var flag = false;
    var brother = obj.closest('ol').find('li');
    brother.each(function(){
        var check = $(this).find('.twoCheck').is(':checked');
        if(check){
            flag = true
        }
    })
    var grandfather = obj.parents('.grandfather').find('.oneCheck');
    grandfather.prop("checked", flag);
}
//功能权限保存
$(".btn-submit-feature").on("click", function () {
    obj = $(this);
    obj.attr('disabled',true);
    gid = $('input[name="gid"]').val()
    $.ajax({
        cache: false,
        type: "POST",
        url:"/group/featureAuth/"+gid,
        data:$('#feature').serialize(),
        headers: {
            'X-CSRF-TOKEN': ""
        },
        dataType: 'json',
        success: function(res) {
            obj.attr('disabled',false);
            if(res.status==200){
                swal("保存成功", "2s后将返回权限管理！","success")
                setTimeout(function(){
                    window.location.href="/group";
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

//数据权限保存
$(".btn-submit-dataAuth").on("click", function () {
    var type = $(this).attr("element");
    var gid  = $(this).attr("gid");
    var obj  = $(this);
    var proids = "";
    var classifyids = "";
    if(type=="xProject"){
        $('input[name="projectAuth"]:checked').each(function(){
            proids += $(this).val() + ","
        })
    }else if(type=="xClassify"){
        $('input[name="classifyAuth"]:checked').each(function(){
            classifyids += $(this).val() + ","
        })
    }
    $.ajax({
        cache: false,
        type: "POST",
        url:"/group/dataAuth/"+gid,
        data:{
            authType :type,
            proids:proids,
            classifyids:classifyids,
        },
        headers: {
            'X-CSRF-TOKEN': ""
        },
        dataType: 'json',
        success: function(res) {
            obj.attr('disabled',false);
            if(res.status==200){
                swal("保存成功", "2s后将返回权限管理！","success")
                setTimeout(function(){
                    window.location.href="/group";
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