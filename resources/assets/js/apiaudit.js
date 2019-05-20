
//分页
function pagination(pageCount, status){
    $('.M-box').pagination({
        pageCount: pageCount,
        jump: true,
        callback:function(api){
            var data = {
                page: api.getCurrent(),
                status: status
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
        url: $("input[name='ajaxApiUrl']").val(),
        data: data,
        dataType: 'json',
        success: function(res) {
            if(res.status==200){
                $(".spiner-loading").hide();
                $(".totalCount").text(res.data.totalCount+'个Api');
                render("ApiAuditList", res.data, $(".tab-content tbody"));
                pageCount = Math.ceil(res.data.totalCount/20);
                if(first==1){
                    pagination(pageCount, data.status);
                }
            }
        },
        error: function() {
            swal("网络错误", "请稍后重试！","error")
        },
    });

}
//待审核Api - 通过
$('.tab-content').on('click', '.operate', function(){
    var did = $(this).attr('did');
    var apiname = $(this).attr('apiname');
    var version = $(this).attr('version');

    swal({
            title: "您确认（"+apiname+"V"+version+"版本）通过审核",
            text: "Api审核后才能对外提供服务",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonText: "通过",
            cancelButtonText: "取消",
        },
        function(){
            operate(did, 1, "通过审核")
        });
});
//项目环境修改
$('#myModal').on('show.bs.modal',
    function(event) {
        var obj = $(event.relatedTarget);
        var did = obj.attr("did");
        $(".modal-title").text("Api 审核操作");
        $("input[name='did']").val(did);
        $('textarea[name="refuse"]').val('')
    });
//待审核Api - 拒绝
$(".btn-info-submit").on("click", function () {
    var did = $("input[name='did']").val();
    var refuse = $('textarea[name="refuse"]').val();
    operate(did, 2, refuse)
});
//通过或拒绝接口
function operate(did, status, des){

    var ApiAuditUrl = $("input[name='ApiAuditUrl']").val();
    $.ajax({
        cache: false,
        type: "POST",
        url:   ApiAuditUrl,
        data:  {did:did,status:status,des:des},
        headers: {
            'X-CSRF-TOKEN': ""
        },
        dataType: 'json',
        success: function(res) {
            $(".btn-info-submit").attr('disabled',false);
            if(res.status==200){
                $('#myModal').modal('hide')
                swal("操作成功", "2s后将刷新本页面！","success");
                setTimeout(function(){
                    swal.close();
                    window.location.reload()
                }, 2000);
            }else{
                swal("请求出错", res.message, "error")
            }
        },
        error: function(request) {
            $(".btn-info-submit").attr('disabled',false);
            swal("网络错误", "请稍后重试！","error")
        },
    });
}
//搜索
$(".search").on("click",function () {
    var apiname = $('input[name="apiname"]').val();
    var author = $('input[name="author"]').val();
    var auditStatus = $('select[name="auditStatus"]').val();
    if (auditStatus==0){
        status = "2,5"
    }else{
        status = auditStatus;
    }
    var data = {
        type:"search",
        page:1,
        status:status,
        apiname:apiname,
        author:author
    };
    ajaxApiList(data, 1);
});