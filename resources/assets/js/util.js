String.prototype.startWith=function(str){
    if(str==null||str==""||this.length==0||str.length>this.length)
        return false;
    if(this.substr(0,str.length)==str)
        return true;
    else
        return false;
    return true;
}
String.prototype.endWith=function(str){
    if(str==null||str==""||this.length==0||str.length>this.length)
        return false;
    if(this.substring(this.length-str.length)==str)
        return true;
    else
        return false;
    return true;
}

//日期格式化
Date.prototype.format = function(format){
    if(!format){
        format = 'yyyy-MM-dd';// 默认1997-01-01这样的格式
    }
    var o = {
        "M+" : this.getMonth()+1, // month
        "d+" : this.getDate(), // day
        "h+" : this.getHours(), // hour
        "m+" : this.getMinutes(), // minute
        "s+" : this.getSeconds(), // second
        "q+" : Math.floor((this.getMonth()+3)/3), // quarter
        "S" : this.getMilliseconds() // millisecond
    }

    if(/(y+)/.test(format)) {
        format = format.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
    }

    for(var k in o) {
        if(new RegExp("("+ k +")").test(format)) {
            format = format.replace(RegExp.$1, RegExp.$1.length==1 ? o[k] : ("00"+ o[k]).substr((""+ o[k]).length));
        }
    }
    return format;
}


var config={
    serverUrl:"localhost:3000"
}

function Core(){

}
Core.prototype.numformat=function(){
    var num = (num || 0).toString(), result = '';
    var suffix="";
    if(num.indexOf(".")>-1){
        var t = num.split(".");
        num=t[0];
        suffix = "." + t[1];

    }

    while (num.length > 3) {
        result = ',' + num.slice(-3) + result;
        num = num.slice(0, num.length - 3);
    }
    if (num) { result = num +""+ result; }

    return result+suffix;
}
Core.prototype.token=function(d){
    d.createAt = new Date();
    return this.data("token",d);
}

Core.prototype.error=function(d){
    alert(d)
}
//用于存储信息
Core.prototype.data=function(k,d){
    //console.log("data",k,d)
    if(typeof d=="undefined"){
        var o = localStorage.getItem(k);
        if(o==null){
            return null;
        }else{
            o = JSON.parse(o)
            return o[k]
        }


    }else if(null==d){
        return localStorage.removeItem(k)
    }else{
        var o = {}
        o[k] = d;
        return localStorage.setItem(k,JSON.stringify(o))
    }
}
Core.prototype.post=function(uri,data){
    if(!data){
        data ={};
    }


    var that = this;
    var _opts = {
        "type":"post",
        data:data,
        timeout:60000,
        url:config.serverUrl+"/"+uri,
        dataType:"json"
    }
    var token = that.data("token");

    if(null!=token){

        _opts.headers={"x-request-token":token};
    }
    if(typeof cb=="function"){
        _opts.success=function(a,b,c){
            cb(a,b,c)
        }
    }

    _opts.error=function(a,b,c){
        if(a.status==401){

            restgo.error("请先登录");
        }else{

            restgo.error("服务器繁忙");
        }

    }
    return $.ajax(_opts);

}

Core.prototype.postJson=function(uri,data){

    var that = this;
    var _opts = {
        "type":"post",
        data:JSON.stringify(data),
        timeout:60000,
        url:config.serverUrl+uri,
        contentType:"application/json;charset=utf-8",
        dataType:"json"
    }
    var token = that.data("token");

    if(null!=token){
        _opts.headers={"Authorization":token.token_type+token.access_token};
    }
    var deferred = Q.defer();
    _opts.error=function(a,b,c){
        error("服务器繁忙,请稍后再试")
    }

    return $.ajax(_opts)
}

/***
 * filedom jquery 对象
 *
 */
Core.prototype.uploadonefile=function(filedom,data){
    var formData = new FormData();
    var size = 0
    for(var oo in filedom[0].files){
        var file = filedom[0].files[oo];
        console.log()
        if(file.size>1*1024*1024-1){
            var deferred = $.Deferred();
            return deferred.resolve( {"status":400,"msg":"文件太大,单个文件不能超过1Mb","data":[]} )
        }
        size += file.size
        formData.append(file.name,file);
    }
    if(size >10*1024*1024-1){
        var deferred = $.Deferred();
        return deferred.resolve( {"status":400,"msg":"文件太大,每次上传总数不能超过10Mb","data":[]} )
    }


    if(!!data){
        for(var i in data){
            formData.append(i, data[i]);
        }
    }
    var that = this;
    var _opts = {
        "type":"post",
        data:formData,
        timeout:6000,
        url:that.api("attach/upload"),
        dataType:"json",
        contentType: false,
        processData: false
    }

    var token = that.data("token");
    if(null!=token){
        _opts.headers={"Authorization":token.token_type+token.access_token};
    }
    _opts.error=function(a,b,c){
        error("服务器繁忙,请稍后再试")
    }
    return $.ajax(_opts)

}
Core.prototype.compressanduploadonefile=function(filedom,data){

    // 压缩图片需要的一些元素和对象
    var reader = new FileReader(),
        img = new Image();
    var that = this;
    if(typeof pickfile=="undefined"){
        function pickfile(f){

        }
    }

    // 选择的文件对象
    var file = filedom[0].files[0];;
    var deferred = $.Deferred();
    if(file.type.indexOf("image")==-1){
        deferred.resolve({"status":400,"msg":"当前文件类型暂不支持"})

        return deferred;
    }
    console.log(file)
    if(file.size<500*1024){
        return that.uploadonefile(filedom,data)
    }


    // 缩放图片需要的canvas
    var canvas = document.createElement('canvas');
    var context = canvas.getContext('2d');

    // base64地址图片加载完毕后
    img.onload = function () {
        // 图片原始尺寸
        var originWidth = this.width;
        var originHeight = this.height;


        // 最大尺寸限制
        var maxWidth = filedom.data("maxwidth")||400, maxHeight = filedom.data("maxheight")||400;
        // 目标尺寸
        var targetWidth = originWidth, targetHeight = originHeight;
        // 图片尺寸超过400x400的限制
        if (originWidth > maxWidth || originHeight > maxHeight) {
            if (originWidth / originHeight > maxWidth / maxHeight) {
                // 更宽，按照宽度限定尺寸
                targetWidth = maxWidth;
                targetHeight = Math.round(maxWidth * (originHeight / originWidth));
            } else {
                targetHeight = maxHeight;
                targetWidth = Math.round(maxHeight * (originWidth / originHeight));
            }
        }

        // canvas对图片进行缩放
        canvas.width = targetWidth;
        canvas.height = targetHeight;
        // 清除画布
        context.clearRect(0, 0, targetWidth, targetHeight);
        // 图片压缩
        context.drawImage(img, 0, 0, targetWidth, targetHeight);
        // canvas转为blob并上传
        var dataURL = canvas.toDataURL("image/jpeg");
        console.log("tourl success")
        that.post("attach/upload",{"base64data":dataURL}).then(function(resp){
            deferred.resolve( resp)
        })

    };

    // 文件base64化，以便获知图片原始尺寸
    reader.onload = function(e) {
        img.src = e.target.result;
    };
    if (file.type.indexOf("image") == 0) {
        reader.readAsDataURL(file);
    }else{
        deferred.resolve({"status":400,"msg":"当前文件类型暂不支持"})
    }
    return deferred;

}

Core.prototype.parseUri = function(url){
    if(typeof url=="undefined"){
        url= location.href;
    }
    var query = url.substr(url.indexOf("?"));
    query=query.substr(1);
    var reg = /([^=&\s]+)[=\s]*([^=&\s]*)/g;
    var obj = {};
    while(reg.exec(query)){
        obj[RegExp.$1] = decodeURI(RegExp.$2);
    }
    return obj;
}
Core.prototype.parseQuery = function(name){

    var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
    var r = window.location.search.substr(1).match(reg);  //匹配目标参数
    if (r != null) return decodeURI(unescape(r[2]));
    return null; //返回参数值
}

Core.prototype.testEmail = function(email){
    return /^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+[\.][a-zA-Z0-9_-]+$/.test(email)
}
Core.prototype.testMobile = function(mobile){
    return /^[1][34578][0-9]{9}$/.test(mobile)
}

Core.prototype.testReg = function(reg,data){
    var reg = new RegExp(reg); //构造一个含有目标参数的正则表达式对象
    return reg.test(data)
}
window.xapi = new Core();



