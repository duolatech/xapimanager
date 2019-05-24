
//项目开放性
var pmember = $(".project-member");
var open = $('input[name="attribute"]:checked').val();
if(open==2) pmember.show();
$('input[name="attribute"]').on("ifClicked", function(){
    var val = $(this).val();
    if(val==2){
        pmember.show();
    }else if(val==1){
        pmember.hide();
    }
})
//选择成员
$(".ui-select-search").focus(function(){
    $(".ui-select-choices-content").show();
    sendKeyWord('');
})
$(".ui-select-choices-content").on('mouseover', '.ui-select-choices-row', function(){
    $(this).addClass('active');
}).on('mouseout', '.ui-select-choices-row', function(){
    $(this).removeClass('active');
})
$(".wrapper-content").on('click', '.ui-select-choices-row', function(){
    var groupname = $(this).attr('groupname');
    var gid = $(this).attr('gid');
    var _element = '';
    _element += '<span class="ng-scope">';
    _element += '	<span style="margin-right: 3px;" class="ui-select-match-item btn btn-default btn-xs" tabindex="-1" type="button">';
    _element += '		<span class="close ui-select-match-close">&nbsp;×</span>';
    _element += '		<span uis-transclude-append="">';
    _element += '			<span class="ng-binding ng-scope">';
    _element += 				groupname;
    _element += '			</span> ';
    _element += '		</span> ';
    _element += '		<input type="hidden" value="'+gid+'" name="groups[]" />';
    _element += '	</span>';
    _element += '</span>';

    $(".ui-select-match").append(_element);
    $(".ui-select-search").val('');
})
$(".wrapper-content").on('click', '.ui-select-match-close', function(){
    $(this).parents('.ui-select-match-item').remove();
})
$(".ui-select-choices-content").mouseleave(function() {
    $(this).hide();
    $(".ui-select-search").blur();
})
//自动联想
$(".wrapper-content").on('keyup', '.ui-select-search', function(){
    keyword = $(this).val();
    sendKeyWord(keyword);
}).on('paste', '.ui-select-search', function(){
    var el = $(this);
    setTimeout(function() {
        keyword = $(el).val();
        sendKeyWord(keyword);
    }, 100);
})
function sendKeyWord(keyword){
    $.ajax({
        cache: false,
        type: "GET",
        url:"/group/all?&keyword="+keyword,
        dataType: 'json',
        success: function(res) {
            var _element = '';
            if(res.status==200){
                $.each(res.data,function(key, group){
                    _element +='<div class="ui-select-choices-row ng-scope" groupname="'+group.Groupname+'" gid="'+group.Id+'">';
                    _element +='	<a href="javascript:void(0)"';
                    _element +='		class="ui-select-choices-row-inner"';
                    _element +='		uis-transclude-append="">';
                    _element +='		<div class="ng-binding ng-scope">'+group.Groupname+'</div>';
                    _element +='	</a>';
                    _element +='</div>';
                })
                $(".ui-select-choices-group").html(_element);
            }
        },
        error: function(request) {
            swal("网络错误，请稍后重试", "","error")
        },
    });
}
//保存项目
if($("#myForm").length>0){
    var validator = $("#myForm").validate({
        submitHandler: function(form) {
            $(".btn-info-submit").attr('disabled',true);
            var oid = $(".btn-info-submit").attr("oid");
            var proid = $('input[name="proid"]').val();
            $.ajax({
                cache: false,
                type: "POST",
                url:"/project/info",
                data:$('#myForm').serialize(),
                headers: {
                    'X-CSRF-TOKEN': ""
                },
                dataType: 'json',
                success: function(res) {
                    $(".btn-info-submit").attr('disabled',false);
                    if(res.status==200){
                        if (proid>0){
                            swal("保存成功", "2s后将返回权限管理！","success");
                            setTimeout(function(){
                                window.location.href="/project";
                            }, 2000);
                        }else{
                            proid = res.data
                            swal("创建成功", "即将跳转到项目环境设置页","success");
                            setTimeout(function(){
                                window.location.href="/project/env/"+proid;
                            }, 2000);
                        }
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
            project:{
                required:true,
                maxlength:20,
                minlength:2
            },
            desc:{
                required:true,
                maxlength:300,
                minlength:2
            }
        },
        messages:{
            project :{
                required:"项目名不能为空",
                maxlength:"不能超过20个字符",
                minlength:"不能少于2个字符"
            },
            desc :{
                required:"项目描述不能为空",
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

//项目环境修改
$('#myModal').on('show.bs.modal',
    function(event) {
        var obj = $(event.relatedTarget);
        var status = obj.attr("status");
        var checked = status==1 ? true : false;
        if (status){
            $(".modal-title").text("修改项目环境");
        }else{
            $(".modal-title").text("添加项目环境");
        }
        $('input[name="envname"]').val(obj.attr("envname"));
        $('input[name="domain"]').val(obj.attr("domain"));
        $('input[name="sort"]').val(obj.attr("sort"));
        $('.i-switch-isopen').prop("checked", checked);
        $('input[name="envid"]').val(obj.attr("envid"))

});

//创建环境
if($("#envFrom").length>0){
    var validator = $("#envFrom").validate({
        submitHandler: function(form) {
            $(".btn-info-submit").attr('disabled',true);
            var proid = $(".btn-info-submit").attr("proid");
            $.ajax({
                cache: false,
                type: "POST",
                url:"/project/env/"+proid,
                data:$('#envFrom').serialize(),
                headers: {
                    'X-CSRF-TOKEN': ""
                },
                dataType: 'json',
                success: function(res) {
                    $(".btn-info-submit").attr('disabled',false);
                    if(res.status==200){
                        swal("保存成功，请勿轻易修改", "2s后将返回项目环境列表！ ","success");
                        setTimeout(function(){
                            window.location.href="/project/env/"+proid;
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
            domain:{
                required:true,
                UrlCheck:true,
            },
            envname:{
                required:true,
            },
            sort:{
                required:true,
                NumberCheck:true,
            }
        },
        messages:{
            domain:{
                required:"环境域名不能为空",
                UrlCheck:"请输入域名，以'http/https'开头",
            },
            envname:{
                required:"环境名称不能为空",
            },
            sort:{
                required:"排序不能为空",
            }
        },
        errorElement: 'custom',
        errorClass:'error',
        errorPlacement: function(error, element) {
            error.appendTo(element.next("span"))
        }
    });
    //域名网站
    jQuery.validator.addMethod("UrlCheck", function(value, element) {
        return this.optional(element) || /^((https|http)?:\/\/)+[A-Za-z0-9\-]+(\.[A-Za-z0-9\-]+)+((:)+[0-9]{1,5})?(\/)?$/.test(value);
    }, "请输入正确的域名地址");
    //排序数字检查
    jQuery.validator.addMethod("NumberCheck", function(value, element) {
        return this.optional(element) || /^\d+$/.test(value);
    }, "请输入0-9的数字");
}