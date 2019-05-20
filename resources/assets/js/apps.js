
//json 样例
$(".jsonExample").on("click",function () {
    var jsonStr = '{"data":{"name":"xApi Manager","url":"http://www.smaty.net","keywords":["xapi","restfull"],"author":["feng","long"],"description":"专业实用的开源接口管理平台"}}';
    var jsonData = JSON.parse(jsonStr);
    //json/xml 互转
    if($("#import").length>0){
        $("#import").val(JSON.stringify(jsonData, null, 4))
    }
    //json 格式化
    if($("#format-import").length>0){
        $("#format-import").val(JSON.stringify(jsonData, null, 4))
    }

});
//xml 样例
$(".xmlExample").on("click",function () {
    var xmlStr = '<?xml version="1.0" encoding="UTF-8" ?><data><name>xApi Manager</name><url>http://www.smaty.net</url><keywords>xapi</keywords><keywords>restfull</keywords><author>feng</author><author>long</author><description>专业实用的开源接口管理平台</description></data>';
    $("#import").val(vkbeautify.xml(xmlStr,4))
});
//xml转json
$(".xmltrans").on("click", function () {
    var xmlText = $("#import").val();
    var xotree = new XML.ObjTree();
    var jsonData = xotree.parseXML(xmlText);
    if(jsonData.html){
        swal("处理出错", "xml格式错误", "error");
    }
    $("#export").val(JSON.stringify(jsonData, null, 4))
});
//json转xml
$(".jsontrans").on("click", function () {

    var xotree = new XML.ObjTree();
    var dataStr = $("#import").val();
    try {
        var jsonData = JSON.parse(dataStr);
        var xml = xotree.writeXML(jsonData);
        var xmlText = vkbeautify.xml(xml,4);
        $("#export").val(xmlText)
    }
    catch(e) {
        swal("处理出错", "请核对输入json字符串是否正确", "error");
        return
    }

});
//json 格式化
$(".JsonFormat").on("click", function(){
    var jsonstr = $("#format-import").val();
    try {
        var jsonData = JSON.parse(jsonstr);
        $("#format-export").JSONView(jsonData);
    }
    catch(e) {
        swal("处理出错", "请核对输入json字符串是否正确", "error");
        return
    }
});
//json 压缩
$(".JsonCompress").on("click", function(){
    var jsonstr = $("#format-import").val();
    try {
        var jsonData = JSON.parse(jsonstr);
        $("#format-export").html(JSON.stringify(jsonData, null, 0))
    }
    catch(e) {
        swal("处理出错", "请核对输入json字符串是否正确", "error");
        return
    }
});