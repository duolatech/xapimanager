//动态渲染函数
function render(type, data, obj){
    switch (type) {
        case "organizeSearch":organizeSearch(data, obj);break;
        case "userList":userList(data, obj);break;
        case "OperateLog":OperateLog(data, obj);break;
        case "ApiList":ApiList(data, obj);break;
        case "ApiAuditList":ApiAuditList(data, obj);break;
        case "companyList":companyList(data, obj);break;
        case "MessageList":MessageList(data, obj);break;
        case "helpList":helpList(data, obj);break;
    }
}
//团队搜索
function organizeSearch(data, obj){

    obj.html('');
    if(data.length==0){
        return
    }
    element = '<div class="ibox-content forum-container m-b-sm">'+
            '<div class="forum-item">'+
                '<div class="row">'+
                    '<div class="col-sm-7">'+
                        '<div class="forum-icon">'+
                            '<i class="fa fa-users"></i>'+
                        '</div>'+
                        '<a href="forum_post.html" class="forum-item-title">'+data.Name+'</a>'+
                        '<div class="forum-sub-title">口号：'+data.Desc+'</div>'+
                    '</div>'+
                    '<div class="col-sm-5">'+
                '<span class="input-group-btn">'+
                    '<button type="button" class="btn btn-success organizeJoin" identify="'+data.Identify+'">'+
                        '<i class="fa fa-sign-in"></i> 加入该团队'+
                    '</button>'+
                '</span>'+
                '</div>'+
                '</div>'+
            '</div>'+
        '</div>';
    obj.append(element);

}

//用户列表渲染
function userList(data, obj){

    obj.html('');
    if(data.length==0){
        return
    }
    element = "";
    $.each(data, function (i, item) {
        switch(item.Status) {
            case '1': classname = "label-primary", status = "已激活";break;
            case '2': classname = "label-warning", status = "待激活";break;
            case '3': classname = "label-danger", status = "失效";break;
            default: classname = "label-danger", status = "未知";break;
        }
        element += '<tr>\n' +
            '          <td>'+item.Username+'</td>\n' +
            '          <td>'+item.Groupname+'</td>\n' +
            '          <td>'+item.Phone+'</td>\n' +
            '          <td>'+item.Email+'</td>\n' +
            '          <td class="client-status"><span class="label '+classname+'">'+status+'</span></td>\n' +
            '          <td class="client-status">\n' +
            '              <a href="/users/detail/'+item.Uid+'" class="m-r-sm">编辑</a>\n';
            if(item.Status==2){
                element += '<a href="javascript:;" class="activate" uid="'+item.Uid+'">激活</a>\n';
            }
            element += '          </td>\n' +
            '      </tr>';
    });
    obj.append(element);
}

//操作日志列表
function OperateLog(data, obj){
    obj.html('');
    if(data.length==0){
        return
    }
    element = "";
    $.each(data, function (i, item) {

        th = '<th data-toggle="true">操作人</th>\n' +
            '   <th>操作对象</th>\n' +
            '   <th>类型</th>\n' +
            '<th>时间</th>';
        str = "";
        $.each($.parseJSON(item.Desc), function(k, v){
            th += '<th data-hide="all">'+k+'</th>';
            str += '<td>'+JSON.stringify(v)+'</td>\n';
        });
        $("thead tr").html(th);
        element += '<tr>\n' +
            '         <td>'+item.Operator+'</td>\n' +
            '         <td>'+item.Object+'</td>\n' +
            '         <td>'+item.Logtype+'</td>\n' +
            '         <td>'+item.Addtime+'</td>\n' +
                      str +
            '       </tr>'
    });
    obj.append(element);
}

//Api 列表
function ApiList(data, obj){
    obj.html('');
    if(data.length==0){
        return
    }
    element = "";
    $.each(data.list, function (i, item) {
        var rowspan = item.info.length;
        $.each(item.info,function(key, sub){
            if(sub.status==1) label_status = 'label-primary';
            if(sub.status==2) label_status = 'label-warning';
            if(sub.status==3) label_status = 'label-default';
            if(sub.status==5) label_status = 'label-danger';

            element += '<tr>';
            if(key==0){
                element += '<td rowspan='+rowspan+'>'+item.apiname+'</td>';
            }
            element += '<td><a href="/manager/'+data.proid+'/Api/detail?did='+sub.id+'">V'+sub.version+'</a></td>';
            element +=     '<td>【'+sub.method+"】"+sub.uri+'</td>';
            element +=     '<td>'+sub.author+'</td>';
            element +=     '<td class="footable-visible">'+sub.mtime+'</td>';
            element +=     '<td class="client-status"><span class="label '+label_status+'">'+sub.apistatus+'</span></td>';
            element +=     '<td class="client-status">';
            element +=          '<a href="/manager/'+data.proid+'/Api/detail?did='+sub.id+'" class="m-r-sm">查看</a>&nbsp;';
            if (data.auth.addVersion){
                element +=          '<a href="/manager/'+data.proid+'/Api/info?version_type=add&lid='+sub.listid+'" class="m-r-sm">添加版本</a>&nbsp;';
            }
            if(data.auth.discardApi && (sub.status==1 || sub.status==2)){
                element +=          '<a href="javascript:;" class="discard" did="'+sub.id+'" proid="'+data.proid+'">废弃</a>';
            }
            element +=     '</td>';
            element += '</tr>';
        });

    });
    obj.append(element);
}
//Api 待审核列表
function ApiAuditList(data, obj) {
    obj.html('');
    if(data.length==0){
        return
    }
    element = "";
    $.each(data.list, function (i, item) {
        var rowspan = item.info.length;
        $.each(item.info,function(key, sub){
            if(sub.status==1) label_status = 'label-primary';
            if(sub.status==2) label_status = 'label-warning';
            if(sub.status==3) label_status = 'label-default';
            if(sub.status==5) label_status = 'label-danger';

            element += '<tr>';
            if(key==0){
                element += '<td rowspan='+rowspan+'>'+item.apiname+'</td>';
            }
            element += '<td><a href="/Api/detail?did='+sub.id+'">V'+sub.version+'</a></td>';
            element +=     '<td>【'+sub.method+"】"+sub.uri+'</td>';
            element +=     '<td>'+sub.author+'</td>';
            element +=     '<td class="footable-visible">'+sub.mtime+'</td>';
            element +=     '<td class="client-status"><span class="label '+label_status+'">'+sub.apistatus+'</span></td>';
            element +=     '<td class="client-status">';
            element +=          '<a href="/manager/'+data.proid+'/Api/detail?did='+sub.id+'" class="m-r-sm">查看</a>&nbsp;';
            if (data.auth.auditOperate){
                element +=       '<a href="javascript:;" class="m-r-sm operate"  did="'+sub.id+'" apiname="'+item.apiname+'" version="'+sub.version+'">通过</a>&nbsp;';
                element +=       '<a href="#"  did="'+sub.id+'" apiname="'+item.apiname+'" version="'+sub.version+'" data-toggle="modal" data-target="#myModal">拒绝</a>';
            }
            element +=     '</td>';
            element += '</tr>';
        });

    });
    obj.append(element);
}
//企业密钥
function companyList(data, obj){
    obj.html('');
    if(data.length==0){
        return
    }
    element = "";
    $.each(data.list, function (i, item) {
        if(item.Status==1){
            label_status = 'label-primary';
            status = "已开启";
        }
        if(item.Status==2){
            label_status = 'label-warning';
            status = "已关闭";
        }

        element += '<tr>';
        element +=    '<td>'+item.Company+'</td>';
        element +=    '<td>'+item.Appid+'</td>';
        element +=    '<td>'+item.Appsecret+'</td>';
        element +=    '<td class="client-status"><span class="label '+label_status+'">'+status+'</span></td>';
        element +=    '<td class="client-status">';
        if (data.auth.modifyCompany){
            element +=    '     <a href="/manager/'+data.proid+'/company/info?id='+item.Id+'" class="m-r-sm">编辑</a>';
        }
        if (data.auth.delCompany){
            element +=    '     <a href="javascript:;" class="delCompany" id="'+item.Id+'" proid="'+data.proid+'">删除</a>';
        }
        element +=    '</td>';
        element +='</tr>';
    });

    obj.append(element);
}
//消息列表
function MessageList(data, obj){
    obj.html('');
    if(data.length==0){
        return
    }
    element = "";
    $.each(data.list, function (i, item) {
        if(item.isread==1){
            trClass = "read";
        }else{
            trClass = "unread";
        }
        element += '<tr class="'+trClass+'">\n' +
            '           <td class="check-mail">\n' +
            '                <div class="icheckbox_square-green" style="position: relative;">\n' +
            '                   <input type="checkbox" name="portion" class="i-checks" mid="'+item.id+'" style="position: absolute; opacity: 0;">\n' +
            '                </div>\n' +
            '           </td>\n' +
            '           <td class="mail-ontact">'+item.sender+'</td>\n' +
            '           <td class="mail-subject"><a href="/message/detail/'+item.id+'">'+item.subject+'</a></td>\n' +
            '           <td class=""></td>\n' +
            '           <td class="text-right mail-date">'+item.sendtime+'</td>\n' +
            '       </tr>';
    });

    obj.append(element);
}
//帮助中心列表
function helpList(data, obj){
    obj.html('');
    if(data.length==0){
        return
    }
    element = "";
    $.each(data.list, function (i, item) {
        element += '<div class="col-sm-12">\n' +
            '                <div class="ibox" style="margin-bottom: 10px;">\n' +
            '                    <div class="ibox-content" style="padding: 10px 20px;">\n' +
            '                        <a href="/help/detail/'+item.id+'" class="btn-link">\n' +
            '                            <h2>\n' +
                                            item.title+
            '                            </h2>\n' +
            '                        </a>\n' +
            '                        <div class="small m-b-xs">\n' +
            '                            <strong>'+item.author+'</strong> <span class="text-muted m-l-sm"><i class="fa fa-clock-o"></i> '+item.ctime+'</span>\n' +
            '                        </div>\n' +
            '                        <p>\n' +
                                           item.content+
            '                        </p>\n' +
            '                    </div>\n' +
            '                </div>\n' +
            '            </div>';
    });

    obj.append(element);
}