package manager

import (
	"encoding/json"
	"github.com/gin-gonic/gin"
	"github.com/mozillazg/go-pinyin"
	"net/http"
	"strconv"
	"strings"
	"time"
	"xapimanager/application/Services"
	"xapimanager/application/common"
	"xapimanager/application/models"
)

var limit = 20

//请求类型(1json,2xml,3form,4raw)
var requestType = map[int]string{
	1: "JSON",
	2: "XML",
	3: "FORM",
	4: "RAW",
}

//响应类型(1json,2xml,3jsonp,4html)
var responseType = map[int]string{
	1: "JSON",
	2: "XML",
	3: "JSONP",
	4: "HTML",
}

//api状态
var apiStatus = map[int]string{
	1: "已审核",
	2: "待审核",
	3: "已废弃",
	4: "已删除",
	5: "审核不通过",
}

//请求方法
var method = map[int]string{
	1: "GET",
	2: "POST",
	3: "PUT",
	4: "DELETE",
}

//网络方式
var network = map[int]string{
	1: "内网",
	2: "外网",
}

//认证方式
var authentication = map[int]string{
	1: "session认证",
	2: "key/secret认证",
}

//Api 搜索
func ApiSearch(c *gin.Context) {

	//获取用户权限
	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"].(int)
	auth := userInfo.(map[string]interface{})["auth"].(map[string][]string)
	proid := common.StringToInt(c.Param("proid"))

	cls := models.GetClassify(proid, 0, auth["dataAuth"])
	classify, _ := json.Marshal(cls)

	//查询当前环境
	env := models.GetCurrentEnv(uid, proid)

	c.HTML(http.StatusOK, "manager_apisearch.html", gin.H{
		"website":  Services.GetWebsite(),
		"proid":    proid,
		"classify": string(classify),
		"env":      env,
	})
}

//Api 列表页
func GetApiList(c *gin.Context) {

	//获取用户权限
	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"].(int)
	auth := userInfo.(map[string]interface{})["auth"].(map[string][]string)
	proid := common.StringToInt(c.Param("proid"))

	cls := models.GetClassify(proid, 0, auth["dataAuth"])

	gather := map[string][]map[string]interface{}{}
	key := ""
	for _, vol := range cls {
		for _, v := range vol.Child {
			py := pinyin.Pinyin(v.Classifyname, pinyin.NewArgs())
			if len(py) > 0 {
				key = strings.ToUpper(
					common.SubString(
						py[0][0], 0, 1, false))
			} else {
				key = strings.ToUpper(
					common.SubString(
						v.Classifyname, 0, 1, false))
			}
			gather[key] = append(gather[key], map[string]interface{}{
				"id":           v.Id,
				"classifyname": v.Classifyname,
			})
		}
	}
	//查询当前环境
	env := models.GetCurrentEnv(uid, proid)
	c.HTML(http.StatusOK, "manager_apilist.html", gin.H{
		"website": Services.GetWebsite(),
		"proid":   proid,
		"gather":  gather,
		"env":     env,
	})
}

//Api 列表
func GetAjaxApilist(c *gin.Context) {

	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	proid, _ := strconv.Atoi(c.Param("proid"))
	//获取用户权限
	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"].(int)
	auth := userInfo.(map[string]interface{})["auth"].(map[string][]string)

	//查询当前环境
	env := models.GetCurrentEnv(uid, proid)
	//查询条件
	classify, _ := strconv.Atoi(c.DefaultQuery("classify", "0"))
	subClassify, _ := strconv.Atoi(c.DefaultQuery("subClassify", "0"))
	con := map[string]interface{}{
		"proid": proid,
		"envid": env.Envid,
	}
	start := (page - 1) * limit

	if c.Query("type") == "search" || c.PostForm("type") == "search" {
		con["apiname"] = c.Query("apiname")
		con["classify"] = classify
		con["subClassify"] = subClassify
		con["URI"] = c.Query("URI")
		con["author"] = c.Query("author")
	} else {
		subClassify, _ := strconv.Atoi(c.DefaultQuery("subClassify", "0"))
		if subClassify > 0 {
			con["subClassify"] = subClassify
		}
	}
	apistatus := strings.Split(c.Query("status"), ",")

	data := models.GetApilist(con, start, limit, apistatus, auth["dataAuth"])

	//功能节点权限检查
	data["auth"] = map[string]bool{
		"addVersion":   common.CheckAuth("addVersion", auth["operate"]),
		"discardApi":   common.CheckAuth("discardApi", auth["operate"]),
		"auditOperate": common.CheckAuth("auditOperate", auth["operate"]),
	}
	data["proid"] = proid
	c.JSON(http.StatusOK, gin.H{
		"status":  200,
		"message": "成功",
		"data":    data,
	})
}

//Api 详情
func GetApiDetail(c *gin.Context) {

	proid := common.StringToInt(c.Param("proid"))
	did := common.StringToInt(c.Query("did"))

	//获取api信息
	detail := models.GetApiInfo(did)
	//获取分类信息
	subClassify := models.GetClassifyInfo(proid, detail.Subclassify)
	Classify := models.GetClassifyInfo(proid, subClassify.Pid)
	classifyName := Classify.Classifyname + " >> " + subClassify.Classifyname
	//获取权限及环境信息
	var env []string
	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"].(int)
	auth := userInfo.(map[string]interface{})["auth"].(map[string][]string)
	envInfo := models.GetProjectValidEnv(proid, "desc")
	for _, v := range envInfo {
		env = append(env, v.Envname)
	}
	basic := map[string]interface{}{
		"Id":             detail.Id,
		"Apiname":        detail.Apiname,
		"Version":        detail.Version,
		"ClassifyName":   classifyName,
		"Gateway":        detail.Gateway,
		"Local":          detail.Local,
		"Method":         method[detail.Method],
		"Network":        network[detail.Network],
		"Authentication": authentication[detail.Network],
		"Description":    detail.Description,
		"Mtime":          detail.Mtime,
		"Ctime":          detail.Ctime,
		"Author":         Services.GetUserName(detail.Author),
		"Editor":         strings.Join(Services.GetBatchUserName(detail.Editor), ","),
		"Envinfo":        models.GetUserDomain(uid, proid),
		"Status":         detail.Status,
		"ApiStatus":      apiStatus[detail.Status],
		"PublishEnv":     strings.Join(env, " >> "),
		"AuditInfo":      models.GetApiAuditInfo(detail.Id),
	}

	if detail.Id > 0 {
		c.HTML(http.StatusOK, "manager_apidetail.html", gin.H{
			"website":       Services.GetWebsite(),
			"proid":         proid,
			"basic":         basic,
			"Header":        common.JsonDecodetoMap(detail.Header),
			"Request":       common.JsonDecodetoMap(detail.Request),
			"Response":      common.JsonDecodetoMap(detail.Response),
			"Statuscode":    common.JsonDecodetoMap(detail.Statuscode),
			"SuccessGoback": common.JsonDecodetoMap(detail.Successgoback),
			"FailGoback":    common.JsonDecodetoMap(detail.Failgoback),
			"auth": map[string]bool{
				"publishApi": common.CheckAuth("publishApi", auth["operate"]),
				"modifyApi":  common.CheckAuth("modifyApi", auth["operate"]),
				"delApi":     common.CheckAuth("delApi", auth["operate"]),
			},
		})
	} else {
		c.HTML(http.StatusOK, "404.html", gin.H{
			"status":  400,
			"message": "未找到该Api详情",
		})
	}

}

//Api 添加/编辑
func ApiInfo(c *gin.Context) {

	proid := common.StringToInt(c.Param("proid"))
	did := common.StringToInt(c.Query("did"))

	var vInfo = map[string]interface{}{}
	var subclassify int
	lid := common.StringToInt(c.Query("lid"))
	vInfo["status"] = 0
	vInfo["lid"] = lid
	if c.Query("version_type") == "add" && lid > 0 {
		versionInfo := models.GetApiDetail(map[string]interface{}{
			"proid":  proid,
			"listid": lid,
		})
		if versionInfo.Id > 0 {
			vInfo["status"] = 1
			vInfo["apiname"] = versionInfo.Apiname
			subclassify = versionInfo.Subclassify
		}
	}
	//获取用户权限
	userInfo, _ := c.Get("user")
	auth := userInfo.(map[string]interface{})["auth"].(map[string][]string)

	cls := models.GetClassify(proid, 0, auth["dataAuth"])
	classify, _ := json.Marshal(cls)
	//获取api信息
	detail := models.GetApiInfo(did)
	if vInfo["status"] == 0 {
		subclassify = detail.Subclassify
	}

	if did > 0 {
		c.HTML(http.StatusOK, "manager_apiinfo_modify.html", gin.H{
			"website":       Services.GetWebsite(),
			"proid":         proid,
			"classify":      string(classify),
			"subClassify":   models.GetClassifyInfo(proid, subclassify),
			"detail":        detail,
			"Header":        common.JsonDecodetoMap(detail.Header),
			"Request":       common.JsonDecodetoMap(detail.Request),
			"Response":      common.JsonDecodetoMap(detail.Response),
			"Statuscode":    common.JsonDecodetoMap(detail.Statuscode),
			"SuccessGoback": common.JsonDecodetoMap(detail.Successgoback),
			"FailGoback":    common.JsonDecodetoMap(detail.Failgoback),
		})
	} else {
		c.HTML(http.StatusOK, "manager_apiinfo.html", gin.H{
			"website":     Services.GetWebsite(),
			"proid":       proid,
			"classify":    string(classify),
			"subClassify": models.GetClassifyInfo(proid, subclassify),
			"detail":      detail,
			"vInfo":       vInfo,
		})
	}
}

//Api保存
func ApiStore(c *gin.Context) {

	var author int
	var editor []string
	var requesttype int
	var responsetype int
	id := common.StringToInt(c.PostForm("did"))
	lid := common.StringToInt(c.PostForm("lid"))
	proid := common.StringToInt(c.Param("proid"))
	env := models.GetProjectLowEnv(proid)
	envid := env.Id
	if envid == 0 {
		c.JSON(http.StatusOK, gin.H{
			"status":  2010,
			"message": "请先设置项目的环境信息",
		})
		return
	}
	//获取用户信息
	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"].(int)
	//获取编辑人
	if id > 0 {
		detail := models.GetApiInfo(id)
		editor = common.RemoveRepeatedElement(
			append(
				strings.Split(detail.Editor, ","),
				strconv.Itoa(uid),
			))
		author = detail.Author
	} else {
		author = uid
		editor = []string{strconv.Itoa(uid)}
	}
	for k, v := range requestType {
		if v == c.PostForm("requesttype") {
			requesttype = k
		}
	}
	for k, v := range responseType {
		if v == c.PostForm("responsetype") {
			responsetype = k
		}
	}

	time := time.Now().Unix()
	data := map[string]interface{}{
		"apiname":        c.PostForm("apiname"),
		"subclassify":    common.StringToInt(c.PostForm("subClassify")),
		"version":        common.StringToInt(c.PostForm("version")),
		"uri":            c.PostForm("gateway"),
		"gateway":        c.PostForm("gateway"),
		"local":          c.PostForm("local"),
		"network":        common.StringToInt(c.PostForm("network")),
		"authentication": common.StringToInt(c.PostForm("authentication")),
		"description":    c.PostForm("description"),
		"author":         author,
		"editor":         strings.Join(editor, ","),
		"method":         common.StringToInt(c.PostForm("method")),
		"requesttype":    requesttype,
		"responsetype":   responsetype,
		"header":         c.PostForm("header"),
		"request":        c.PostForm("request"),
		"response":       c.PostForm("response"),
		"statuscode":     c.PostForm("statusCode"),
		"successgoback":  c.PostForm("successGoback"),
		"failgoback":     c.PostForm("failGoback"),
		"status":         2,
		"mtime":          int(time),
	}

	if id == 0 {
		if lid > 0 { //添加版本时
			data["listid"] = lid
		} else { //非添加版本时
			count := models.GetMaxApilist(proid)
			data["listid"] = count + 1
		}
	}
	models.OperateLog("Api添加/编辑", 2, c)
	if models.ApiDetailStore(id, proid, env.Id, data) {
		c.JSON(http.StatusOK, gin.H{
			"status":  200,
			"message": "保存成功",
		})
	} else {
		c.JSON(http.StatusOK, gin.H{
			"status":  2011,
			"message": "保存失败",
		})
	}

}

//Api 待审核页面
func ApiAudit(c *gin.Context) {

	proid, _ := strconv.Atoi(c.Param("proid"))

	c.HTML(http.StatusOK, "manager_apiaudit.html", gin.H{
		"website": Services.GetWebsite(),
		"proid":   proid,
	})
}

//Api 待审核
func ApiAuditOpearate(c *gin.Context) {

	//获取用户权限
	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"].(int)
	did, _ := strconv.Atoi(c.DefaultPostForm("did", "0"))
	status, _ := strconv.Atoi(c.PostForm("status"))
	data := map[string]interface{}{
		"auditor": uid,
		"status":  status,
		"isdel":   2,
		"remark":  c.PostForm("des"),
	}
	models.OperateLog("Api 待审核操作", 2, c)
	if models.ApiAuditOpearate(did, data) {

		//更新Api详情中的状态
		models.UpdateAuditStatus(did, status)
		//获取Api信息
		detail := models.GetApiInfo(did)
		//发送审核不通过通知
		var subject string
		if status == 1 {
			subject = "Api(" + detail.Gateway + ") 审核通过"
		} else if status == 2 {
			subject = "Api(" + detail.Gateway + ") 审核不通过"
		}
		models.SendMessage(map[string]interface{}{
			"sender":   1,
			"recevier": detail.Author,
			"pid":      0,
			"subject":  subject,
			"content":  c.PostForm("des"),
		})
		c.JSON(http.StatusOK, gin.H{
			"status":  200,
			"message": "操作成功",
		})
	} else {
		c.JSON(http.StatusOK, gin.H{
			"status":  2010,
			"message": "操作失败",
		})
	}
}

//Api 操作（删除）
func ApiOpearate(c *gin.Context) {

	proid := common.StringToInt(c.Param("proid"))
	did := common.StringToInt(c.PostForm("did"))

	data := map[string]interface{}{
		"status": 4,
	}
	models.OperateLog("Api删除", 3, c)
	if models.UpdateApiInfo(did, proid, data) {
		c.JSON(http.StatusOK, gin.H{
			"status":  200,
			"message": "删除成功",
		})
	} else {
		c.JSON(http.StatusOK, gin.H{
			"status":  2010,
			"message": "删除失败，请稍后重试",
		})
	}

}

//Api 发布
func ApiPublish(c *gin.Context) {

	var nextId int
	proid := common.StringToInt(c.Param("proid"))
	did := common.StringToInt(c.PostForm("did"))

	//获取用户当前环境
	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"].(int)
	current := models.GetCurrentEnv(uid, proid)

	envInfo := models.GetProjectValidEnv(proid, "desc")
	for k, v := range envInfo {
		if v.Id == current.Envid {
			nextId = k + 1
		}
	}
	if nextId+1 <= len(envInfo) {
		if Services.SyncApiDetail(did, proid, envInfo[nextId].Id) {
			c.JSON(http.StatusOK, gin.H{
				"status":  200,
				"message": "已成功发布到" + envInfo[nextId].Envname + "",
			})
		} else {
			c.JSON(http.StatusOK, gin.H{
				"status":  2011,
				"message": "发布失败，请稍后重试",
			})
		}
	} else {
		c.JSON(http.StatusOK, gin.H{
			"status":  2010,
			"message": "下级环境不存在，请确认后重试",
		})
	}

}

//Api 废弃
func ApiDiscard(c *gin.Context) {
	proid := common.StringToInt(c.Param("proid"))
	did := common.StringToInt(c.PostForm("did"))

	data := map[string]interface{}{
		"status": 3,
	}
	if models.UpdateApiInfo(did, proid, data) {
		c.JSON(http.StatusOK, gin.H{
			"status":  200,
			"message": "废弃成功",
		})
	} else {
		c.JSON(http.StatusOK, gin.H{
			"status":  2010,
			"message": "废弃失败，请稍后重试",
		})
	}
}

//mock测试
func MockTest(c *gin.Context) {

	data := map[string]interface{}{
		"id":      c.Query("did"),
		"gateway": c.Param("action"),
	}
	detail := models.GetApiDetail(data)

	response := map[string]interface{}{}

	json.Unmarshal([]byte(detail.Response), &response)

	jsonResponse := map[string]interface{}{}

	if response["json"] != nil {
		jsondata := response["json"].(string)
		json.Unmarshal([]byte(jsondata), &jsonResponse)
	}
	c.JSON(http.StatusOK, jsonResponse)

}
