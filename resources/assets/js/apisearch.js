
//Api 搜索
$(".btn-Api-search").on("click", function(){

    var apiname = $('input[name="apiname"]').val();
    var classify = $('select[name="classify"]').val();
    var subClassify = $('select[name="subClassify"]').val();
    var URI = $('input[name="gateway"]').val();
    var author = $('input[name="author"]').val();
    var proid = $(this).attr("proid");

    var url = "/manager/"+proid+"/Api/list?type=search&apiname="+apiname;
    if(classify!=0) url+="&classify="+classify;
    if(subClassify!=0) url+="&subClassify="+subClassify;
    url+="&URI="+URI;
    url+="&author="+author;

    window.location.href= url;
});