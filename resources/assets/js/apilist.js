
var subcon;
//查询条件
var con = {
    'type' : getQueryString('type'),
    'apiname' : getQueryString('apiname'),
    'URI' : getQueryString('URI'),
    'author' : getQueryString('author'),
    'classify' : getQueryString('classify'),
    'subClassify' : getQueryString('subClassify')
};
//第一页数据
var data = $.extend({page:1, status:"1,2,3,5"}, con);
ajaxApiList(data,1);

//字母分类切换
$('.classifyLetter').on({
    mouseover : function(){
        var letter = $(this).text();
        $('.classifyGather a').hide();
        $(".sub"+letter).show();
    }
});

$(".classifyALL").on("click",function(){
    window.location.reload();
});

//子分类选择
$(".classifyGather").on('click', '.subClassify', function(){
    var subid = $(this).attr('subid');
    $('#classifyGather a').removeClass('btn-info');
    $(this).removeClass('btn-default').addClass('btn-info');
    subcon = {
        'subClassify' : subid
    }
    var data = $.extend({page:1,status:"1,2,3,5"}, subcon);
    ajaxApiList(data,1);
});

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
            if(subcon != undefined && subcon.subClassify){
                data = $.extend(data, subcon);
            }else{
                data = $.extend(data, con);
            }
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
                render("ApiList", res.data, $(".tab-content tbody"));
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
//废弃Api
$(".tab-content").on("click", ".discard", function(){
    var proid = $(this).attr("proid");
    var did = $(this).attr("did");

    swal({
            title: "您确认废弃该Api",
            text: "请谨慎操作！",
            type: "info",
            showCancelButton: true,
            closeOnConfirm: false,
            showLoaderOnConfirm: true,
            confirmButtonText: "废弃",
            cancelButtonText: "取消"
        },
        function(){
            $.ajax({
                cache: false,
                type: "POST",
                url:"/manager/"+proid+"/Api/discard",
                data:{did:did},
                headers: {
                    'X-CSRF-TOKEN': ''
                },
                dataType: 'json',
                success: function(res) {
                    if(res.status==200){
                        swal(res.message, "2s后刷新当前页面","success");
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