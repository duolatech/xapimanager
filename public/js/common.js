/**
 * js 公共函数 
 * author feng
 */


//判断字符串是否在数组中
function in_array(stringToSearch, arrayToSearch) {
	for (s = 0; s < arrayToSearch.length; s++) {
		thisEntry = arrayToSearch[s].toString();
		if (thisEntry == stringToSearch) {
			return true;
		}
	}
	return false;
}
//获取get参数
function getQueryString(name) { 
	var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
    var r = window.location.search.substr(1).match(reg);
    if (r != null)
        return decodeURI(r[2]);
    return null;
}
//json字符串特殊字符处理（避免JSON.parse报错）
function dealJson(string){
	
	string = string.replace('\"',"\\\"");
	string = string.replace("\'", "\\\'");
	string = string.replace('\\',"\\\\");
	string = string.replace('\&',"\\\&");
	string = string.replace('/',"\\/");
	string = string.replace('\b',"\\b");
	string = string.replace('\f',"\\f");
	string = string.replace('\n',"\\n");
	string = string.replace('\r',"\\r");
	string = string.replace('\t',"\\t");
	
	return string;
}
//清除所有cookie
function clearAllCookie() {  
    var keys = document.cookie.match(/[^ =;]+(?=\=)/g);  
    if(keys) {  
        for(var i = keys.length; i--;)  
            document.cookie = keys[i] + '=0;expires=' + new Date(0).toUTCString()  
    }  
} 