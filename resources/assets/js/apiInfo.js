
var JSON_VALUE_TYPES = ['string','object', 'array','number', 'boolean', 'null'];

var trs = "";
//导入数据
$('#myModal').on('show.bs.modal',
    function(event) {
        var obj = $(event.relatedTarget);
        var loadingType = obj.attr("loadingType");
        var operateTable = obj.attr("operateTable");
        $(".modal-title").text("导入"+loadingType);
        $("input[name='loadingType']").val(loadingType);
        $("input[name='operateTable']").val(operateTable);
    });
$(".btn-leadingData").on("click", function () {
    var dataStr = $('textarea[name="leadingData"]').val();
    var operateTable = $("input[name='operateTable']").val();
    var tbody = $("."+operateTable).find("tbody");
    trs = "";
    var loadingType = $("input[name='loadingType']").val();

    switch(loadingType){
        case "Json":
            try {
                var jsonData = JSON.parse(dataStr);
            }
            catch(e) {
                swal("处理出错", "请核对输入json字符串是否正确", "error")
                return
            }
            break;
        case "Xml":
            var xotree = new XML.ObjTree();
            var jsonData = xotree.parseXML(dataStr);
            try {
                jsonData.html.body.parsererror
                swal("处理出错", "请核对输入的xml字符串是否正确", "error")
                return
            }catch(e) {}

            break;
    }
    $("."+operateTable).parents(".tab-content").find(".valueType").val(dataType(jsonData));
    $("#sel option[value='xx']").prop("selected",true);
    eachJsonData(jsonData, "1", 0);
    $('#myModal').modal('hide');
    tbody.html('');
    tbody.html(trs);
});

//读取json数据
function eachJsonData(jsonData, dataid, paddingleft ){

    dataid += "-";
    paddingleft += 15;
    basetype = dataType(jsonData);
    //数组只循环一次
    if(basetype=='array'){
        if(jsonData[0] != undefined && jsonData[0].length != 0){
            oneType = dataType(jsonData[0]);
            if(in_array(oneType,['array','object'])){
                earchData(jsonData[0], dataid, paddingleft)
            }
        }
    }else if(basetype=='object'){
        earchData(jsonData, dataid, paddingleft)
    }

}

//获取数据类型
function dataType(data){
    valueType = Object.prototype.toString.call(data).match(/\s(.+)]/)[1].toLowerCase();
    return valueType;
}
//元素是否在数组中
function in_array(search,array){
    for(var i in array){
        if(array[i]==search){
            return true;
        }
    }
    return false;
}
//遍历数据
function earchData(data, dataid, paddingleft){

    num = 0;
    $.each(data,function(i,value){
        newDataId =  dataid + String(num);
        valueType = dataType(value);
        if (valueType == 'object'){
            trs += getFieldApi(newDataId, paddingleft, num, valueType, i, '')
            eachJsonData(value, newDataId, paddingleft)
        }else if (valueType == 'array'){
            trs += getFieldApi(newDataId, paddingleft, num, valueType, i, '')
            eachJsonData(value, newDataId, paddingleft)
        }else{
            if(in_array(valueType, JSON_VALUE_TYPES)){
                trs += getFieldApi(newDataId, paddingleft, num, valueType, i, value)
            }
        }
        num ++
    });
}

//同级及子级字段添加
function getFieldApi(dataid, paddingleft, subnodenum, vType, fieldName, fieldValue){

    var fieldApi = '<tr class="gradeX footable-even" style="display: table-row;" data-id="'+dataid+'" data-padding-left="'+paddingleft+'"  data-subnodenum="'+subnodenum+'">\n' +
        '              <td style="padding-left: '+paddingleft+'px;">\n' +
        '                    <input type="text" class="form-control input-sm fieldname" placeholder="字段名" value="'+fieldName+'">\n' +
        '              </td>\n' +
        '              <td>\n' +
        '                   <select name="valueType" class="form-control valueType">';
    $.each(JSON_VALUE_TYPES, function(i, type){
        if(vType == type){
            fieldApi +='            <option value="'+type+'" selected>'+type+'</option>';
        }else{
            fieldApi +='            <option value="'+type+'" >'+type+'</option>';
        }

    });
    fieldApi +='                   </select>\n' +
        '              </td>\n' +
        '              <td>\n' +
        '                   <select name="valueMust" class="form-control valueMust">\n' +
        '                         <option value="1" >是</option>\n' +
        '                         <option value="2" >否</option>\n' +
        '                   </select>\n' +
        '              </td>\n' +
        '              <td><input type="text" name="desc" class="form-control input-sm desc" placeholder="字段说明"> </td>\n' +
        '              <td><input type="text" name="default" class="form-control input-sm default" placeholder="123" value="'+fieldValue+'"> </td>\n' +
        '              <td class="center">\n' +
        '                   <a class="btn btn-default btn-xs btn-rounded sibling" href="javascript:;" style="padding: 3px 6px;">\n' +
        '                       <i class="fa fa-plus"></i>\n' +
        '                       同级</a>\n' +
        '                   <a class="btn btn-default btn-xs btn-rounded child" href="javascript:;" style="padding: 3px 6px;">\n' +
        '                        <i class="fa fa-plus"></i>\n' +
        '                        子级</a>\n' +
        '                   <a class="btn btn-default btn-xs btn-rounded delete" href="javascript:;" style="padding: 3px 6px;">\n' +
        '                        <i class="fa fa-trash-o" style="font-size: 16px;"></i>\n' +
        '                   </a>\n' +
        '              </td>\n' +
        '         </tr>';
    return fieldApi
}
//添加状态码字段
function addStatusCodeField(){
    field = '<tr class="gradeX footable-even" style="display: table-row;">\n' +
        '      <td><input type="text" name="name" class="form-control input-sm fieldname" placeholder="200"> </td>\n' +
        '      <td><input type="text" name="desc" class="form-control input-sm desc" placeholder="成功"></td>\n' +
        '      <td class="center"><a><i class="fa fa-trash-o m-l-xs delete" style="font-size: 16px;"></i></a></td>\n' +
        '   </tr>';
    return field
}
//添加header
function addHeaderField(){
    field = '<tr class="gradeX footable-even" style="display: table-row;">\n' +
        '       <td><input type="text" name="name" class="form-control input-sm fieldname" placeholder="Content-Type"> </td>\n' +
        '       <td><input type="text" name="value" class="form-control input-sm value" placeholder="application/x-www-form-urlencoded"></td>\n' +
        '       <td><input type="text" name="desc" class="form-control input-sm desc" placeholder="字段说明"></td>\n' +
        '       <td class="center delete"><a><i class="fa fa-trash-o m-l-xs" style="font-size: 16px;"></i></a></td>\n' +
        '    </tr>';
    return field
}

//添加同级字段
$(".footable").on("click", ".sibling", function(){
    var obj = $(this);
    var trNum = parseInt(obj.parents("tbody").find("tr").length);
    var dataleft = parseInt(obj.parents("tr").attr("data-padding-left"));
    var dataid = obj.parents("tr").attr("data-id");
    var arr = dataid.split("-");
    var last = parseInt(arr.pop()) + trNum;
    arr.push(last);
    var newDataid = arr.join("-");
    fieldStr = getFieldApi(newDataid, dataleft, 0, '','','');
    obj.parents("tr").after(fieldStr)

});
//添加字段，这是为了防止用户将所有字段删除
$(".footable").on("click", ".addsibling", function(){
    var obj = $(this);
    var trNum = parseInt(obj.parents(".footable").find("tbody tr").length);
    var newDataid = trNum+1;
    newDataid = "1-"+newDataid;
    var dataleft = 0;
    fieldStr = getFieldApi(newDataid, dataleft, 0, '','','');
    obj.parents(".footable").find("tbody").append(fieldStr)

});

//添加子级字段
$(".footable").on("click", ".child", function(){
    var obj = $(this);
    var subnodenum = parseInt(obj.parents("tr").attr("data-subnodenum"));
    var newDataid = obj.parents("tr").attr("data-id") + "-" + subnodenum;
    var level = newDataid.split("-").length;
    fieldStr = getFieldApi(newDataid,  (level - 1)*15, 0,'','','');
    obj.parents("tr").after(fieldStr);
    obj.parents("tr").attr("data-subnodenum", subnodenum + 1)
});
//删除字段
$(".footable").on("click", ".delete", function(){
    var obj = $(this);
    obj.parents("tr").remove();
});
//添加状态码
$(".footable").on("click", ".addStatusCode", function () {
    var obj = $(this);
    var field = addStatusCodeField();
    obj.parents("table").find("tbody").append(field)
});
//添加header
$(".footable").on("click", ".addHeader", function () {
    var obj = $(this);
    var field = addHeaderField();
    obj.parents("table").find("tbody").append(field)
});

//请求数据类型选择
$('input[name="request_data_type"]').on("ifChecked",function(){

    var data_type = $(this).val();
    $(".btn_request_data_type").hide();
    if(data_type != "RAW"){
        $(".request-table").show();
        $(".request-table-RAW").hide();
    }else{
        $(".request-table").hide();
        $(".request-table-RAW").show();
    }
    $(".request_data_type_"+data_type).show();
});
//响应数据类型选择
$('input[name="response_data_type"]').on("ifChecked",function(){

    var data_type = $(this).val();
    $(".btn_response_data_type").hide();
    $(".response_data_type_"+data_type).show();
});

//表单提交
jQuery.validator.addMethod("UrlPathCheck", function(value, element) {
    return this.optional(element) || /^\/[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/.test(value);
}, "请输入正确的gateway Api地址");
jQuery.validator.addMethod("UrlCheck", function(value, element) {
    return this.optional(element) || /^((https|http)?:\/\/)+[A-Za-z0-9\-]+\.[A-Za-z0-9\-]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/.test(value);
}, "请输入正确的local Api地址");
var validator = $("#myForm").validate({
    submitHandler: function(form) {
        $(".btn-info-submit").attr('disabled',true);
        var did = $(".btn-info-submit").attr('did');
        var lid =  $(".btn-info-submit").attr('lid');
        var proid = $(".btn-info-submit").attr('proid');
        var data = {
            "did":             did,
            "lid":            lid,
            "apiname" :       $('input[name="apiname"]').val(),
            "subClassify" :   $('select[name="subClassify"]').val(),
            "version" :       $('select[name="version"]').val(),
            "method" :        $('select[name="method"]').val(),
            "gateway" :       $('input[name="gateway"]').val(),
            "local" :         $('input[name="local"]').val(),
            "network" :       $('input[name="network"]:checked').val(),
            "authentication": $('input[name="authentication"]:checked').val(),
            "description" :   $('.description').val(),
            "header":         JSON.stringify(getHeaderInfo()),
            "request":        JSON.stringify(getRequestInfo()),
            "response":       JSON.stringify(getResponseInfo()),
            "requesttype":   $('input[name="request_data_type"]').val(),
            "responsetype":  $('input[name="response_data_type"]').val(),
            "statusCode":     JSON.stringify(getStatusCodeInfo()),
            "successGoback":  JSON.stringify(getSuccessGobackInfo()),
            "failGoback":     JSON.stringify(getFailGobackInfo()),
        };
        $.ajax({
            cache: false,
            type: "POST",
            url:"/manager/"+proid+"/Api/store",
            data:data,
            headers: {
                'X-CSRF-TOKEN': ''
            },
            dataType: 'json',
            success: function(res) {
                $(".btn-info-submit").attr('disabled',false);
                if(res.status==200){
                    swal("保存成功,等待审核", "2s后将返回Api列表！","success")
                    setTimeout(function(){
                        window.location.href="/manager/"+proid+"/Api/list";
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
        apiname:{
            required:true,
            maxlength:20,
            minlength:2
        },
        version:{
            required:true,
        },
        gateway:{
            required:true,
            UrlPathCheck:true,
        },
        local:{
            required:true,
            UrlCheck:true,
        },
        subClassify:{
            required:true,
        },
        description:{
            required:true,
        }
    },
    messages:{
        apiname :{
            required:"资源名不能为空",
            maxlength:"不能超过20个字符",
            minlength:"不能少于2个字符",
        },
        version :{
            required:"接口版本不能为空",
        },
        gateway:{
            required:"，gateway api地址不能为空",
            UrlCheck:"，请输入URL的路径部分，以'/'开头",
        },
        local:{
            required:"本地Api地址不能为空",
            UrlCheck:"本地完整Url，含http/https",
        },
        subClassify:{
            required:"子分类信息不能为空",
        },
        description:{
            required:"Api描述不能为空",
        }
    },
    errorElement: 'custom',
    errorClass:'text-danger',
    errorPlacement: function(error, element) {
        error.appendTo(element.next("span"))
    }
});

//获取header信息
function getHeaderInfo(){
    var gather = $(".header-table tbody tr");
    var header = {};
    var content = [];
    gather.each(function(){
        var strobj = $(this);
        content.push({
            "name": strobj.find('.fieldname').val(),
            "value": strobj.find('.value').val(),
            "desc": strobj.find('.desc').val()
        });
    });
    header.content = content;
    return header
}
//获取请求信息
function getRequestInfo(){

    var data_type;
    var request = {};
    data_type = $('input[name="request_data_type"]:checked').val();
    request.data_type = data_type
    if(data_type!="RAW"){
        request.Raw = "";
    }else{
        request.Raw = $(".request-table-RAW").val();
    }
    request.valueType = $('select[name="request-valueType"]').val();

    var gather = $(".request-table tbody tr");
    var content = [];
    gather.each(function(){
        var strobj = $(this);
        content.push({
            "name": strobj.find('.fieldname').val(),
            "valueType": strobj.find('.valueType').val(),
            "valueMust": strobj.find('.valueMust').val(),
            "desc": strobj.find('.desc').val(),
            "default": strobj.find('.default').val(),
            "other":{
                "dataId":strobj.attr("data-id"),
                "dataLeft":strobj.attr("data-padding-left"),
                "dataNum":strobj.attr("data-subnodenum")

            }
        });
    });
    request.content = content;
    return request

}
//获取响应数据
function getResponseInfo(){

    var response = {};
    response.data_type = $('input[name="response_data_type"]:checked').val();
    response.valueType = $('select[name="responese-valueType"]').val();

    var gather = $(".response-table tbody tr");
    var content = [];
    gather.each(function(){
        var strobj = $(this);
        content.push({
            "name": strobj.find('.fieldname').val(),
            "valueType": strobj.find('.valueType').val(),
            "valueMust": strobj.find('.valueMust').val(),
            "desc": strobj.find('.desc').val(),
            "default": strobj.find('.default').val(),
            "other":{
                "dataId":strobj.attr("data-id"),
                "dataLeft":strobj.attr("data-padding-left"),
                "dataNum":strobj.attr("data-subnodenum")

            }
        });
    });
    response.content = content;
    json =createJson(response.valueType, "1-(\\d+)", gather);
    response.json = JSON.stringify(json);

    console.log(response.json);

    return response
}
//获取所有状态码
function getStatusCodeInfo(){

    var content = [];
    var statusCode = {};
    var gather = $(".statusCode-table tbody tr");

    gather.each(function(){
        var strobj = $(this);
        content.push({
            "name": strobj.find('.fieldname').val(),
            "desc": strobj.find('.desc').val()
        });
    });
    statusCode.content = content
    return statusCode
}
//获取成功示例
function getSuccessGobackInfo(){

    var Goback = {};
        Goback.data_type = $('input[name="response_success_type"]:checked').val();
        Goback.content = $(".successgoback").val()
    return Goback
}
//获取失败示例
function getFailGobackInfo(){
    var Goback = {};
        Goback.data_type = $('input[name="response_fail_type"]:checked').val();
        Goback.content = $(".failgoback").val()
    return Goback
}

//生成json数据
function createJson(valueType, regstr,  gather) {

    if (valueType == "object"){
        var content = {};
        gather.each(function(){
            var strobj = $(this);
            var isdeal = strobj.attr("isdeal");
            var data_id = strobj.attr("data-id");
            var reg = new RegExp("^" + regstr + "$","g");
            if(data_id.match(reg)){
                var field = strobj.find('.fieldname').val();
                var value = strobj.find('.default').val();
                var subValueType = strobj.find('.valueType').val();
                if (subValueType == "object" || subValueType == "array"){
                    content[field] = createJson(subValueType, data_id.match(reg) + "-(\\d+)", gather);
                }else{
                    if (isdeal!=1){
                        content[field] = value;
                        strobj.attr("isdeal",1)
                    }
                }
            }
        });
        return content;
    }else if(valueType == "array"){
        var content = [];
        var subcontent = {};
        gather.each(function(){
            var strobj = $(this);
            var isdeal = strobj.attr("isdeal");
            var data_id = strobj.attr("data-id");
            var reg = new RegExp("^" + regstr + "$","g");
            if(data_id.match(reg)){
                var field = strobj.find('.fieldname').val();
                var value = strobj.find('.default').val();
                var subValueType = strobj.find('.valueType').val();
                if (subValueType == "object" || subValueType == "array" ){
                    subcontent[field] = createJson(subValueType, data_id.match(reg) + "-(\\d+)", gather);
                }else{
                    if (isdeal!=1){
                        subcontent[field] = value;
                        strobj.attr("isdeal",1)
                    }
                }
            }
        });
        if (Object.keys(subcontent).length != 0) {
            content.push(subcontent);
        }

        return content

    }else if(valueType=="string" || valueType=="number" || valueType=="boolean" || valueType=="null"){
        var content = {};
        gather.each(function(){
            var strobj = $(this);
            var isdeal = strobj.attr("isdeal");
            var data_id = strobj.attr("data-id");
            var reg = new RegExp("^" + regstr + "$","g");
            if(data_id.match(reg)){
                field = strobj.find('.fieldname').val();
                value = strobj.find('.default').val();
                if (isdeal!=1){
                    switch (valueType) {
                        case "number":content[field] = parseInt(value);break;
                        case "boolean": content[field] = new Boolean(value);break;
                        case "null":content[field]=null;break;
                        default:content[field] = value;break;
                    }
                    strobj.attr("isdeal",1)
                }
            }
        });
        return content

    }
}
