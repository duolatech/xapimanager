package manager

import (
	"xapimanager/application/Services"
	"xapimanager/application/common"
	"xapimanager/application/models"
	"github.com/gin-gonic/gin"
	"html/template"
	"net/http"
	"regexp"
	"strconv"
)

//Api 分类列表
func Category(c *gin.Context) {

	//获取用户权限
	userInfo, _ := c.Get("user")
	auth := userInfo.(map[string]interface{})["auth"].(map[string][]string)
	proid, _ := strconv.Atoi(c.Param("proid"))

	classify := models.GetClassify(proid, 0, auth["dataAuth"])

	c.HTML(http.StatusOK, "manager_category.html", gin.H{
		"website":  Services.GetWebsite(),
		"proid":    proid,
		"classify": classify,
		"auth": map[string]bool{
			"modifyCategory": common.CheckAuth("modifyCategory", auth["operate"]),
			"delCategory":    common.CheckAuth("delCategory", auth["operate"]),
		},
	})
}

//Api 子分类列表
func CategorySub(c *gin.Context) {

	//获取用户权限
	userInfo, _ := c.Get("user")
	auth := userInfo.(map[string]interface{})["auth"].(map[string][]string)
	proid, _ := strconv.Atoi(c.Param("proid"))
	cateId, _ := strconv.Atoi(c.DefaultQuery("cateId", "0"))

	info := models.GetClassifyInfo(proid, cateId)
	subClassify := models.GetSubClassify(proid, cateId)

	c.HTML(http.StatusOK, "manager_category_sub.html", gin.H{
		"website":     Services.GetWebsite(),
		"proid":       proid,
		"classify":    info,
		"subClassify": subClassify,
		"auth": map[string]bool{
			"modifyCategory": common.CheckAuth("modifyCategory", auth["operate"]),
			"delCategory":    common.CheckAuth("delCategory", auth["operate"]),
		},
	})
}

//Api 分类添加
func CategoryInfo(c *gin.Context) {

	var cateListUrl string
	var info models.QyClassify

	proid := common.StringToInt(c.Param("proid"))
	cateId := common.StringToInt(c.DefaultQuery("cateId", "0"))
	subcateId := common.StringToInt(c.DefaultQuery("subcateId", "0"))

	if subcateId > 0 {
		info = models.GetClassifyInfo(proid, subcateId)
		cateListUrl = "/manager/" + c.Param("proid") + "/category/sub?cateId=" +
			strconv.Itoa(info.Pid)
	} else {
		cateListUrl = "/manager/" + c.Param("proid") + "/category/sub"
		info = models.GetClassifyInfo(proid, cateId)
	}

	c.HTML(http.StatusOK, "manager_category_info.html", gin.H{
		"website":     Services.GetWebsite(),
		"proid":       proid,
		"classify":    info,
		"desc":        template.HTML(info.Description),
		"cateListUrl": cateListUrl,
	})
}

//Api 子分类添加
func CategoryInfoSub(c *gin.Context) {

	proid := common.StringToInt(c.Param("proid"))
	cateId := common.StringToInt(c.DefaultQuery("cateId", "0"))

	cateListUrl := "/manager/" + c.Param("proid") + "/category/sub?cateId=" +
		strconv.Itoa(cateId)

	c.HTML(http.StatusOK, "manager_category_infoSub.html", gin.H{
		"website":     Services.GetWebsite(),
		"proid":       proid,
		"classify":    models.GetClassifyInfo(proid, cateId),
		"cateListUrl": cateListUrl,
	})
}

//Api 分类保存
func CategorySave(c *gin.Context) {

	var flag bool
	var id int
	var info models.QyClassify
	models.OperateLog("Api分类保存", 2, c)
	//获取用户权限
	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"].(int)
	proid := common.StringToInt(c.Param("proid"))
	cateId := common.StringToInt(c.DefaultPostForm("cateId", "0"))

	content := c.PostForm("content")

	//图片移动及替换
	re, _ := regexp.Compile("\\/upload\\/images.+?\\.\\w+")
	content = re.ReplaceAllStringFunc(content, common.ReplaceImage)

	//删除编辑器版权说明
	re1, _ := regexp.Compile("<p data-f-id=\"pbf\".+?<\\/p>")
	content = re1.ReplaceAllStringFunc(content, func(str string) string { return "" })

	data := map[string]interface{}{
		"proid":        proid,
		"classifyname": c.PostForm("classify"),
		"description":  content,
	}

	if "addSub" == c.PostForm("opttype") { //新增子分类
		info.Pid = cateId
		flag, id = models.CategorySave(proid, 0, uid, cateId, data)
	} else {
		info = models.GetClassifyInfo(proid, cateId) //分类添加及编辑(主要是子分类编辑时用到)
		flag, id = models.CategorySave(proid, cateId, uid, info.Pid, data)
	}

	//获取用户在该项目中的权限组
	group := models.GetProjectGroup(uid, proid)
	//新增分类后为用户组添加该分类的权限
	if flag && id > 0 {
		classifyId := strconv.Itoa(id)
		models.NewClassifyDataSave(group.Id, classifyId)
	}
	// 清除分类缓存
	key := "qy_UserAuth#" + strconv.Itoa(uid) + "#" + strconv.Itoa(proid)
	Services.ClearCache(key)

	if flag {
		var cateListUrl string
		if info.Pid > 0 {
			cateListUrl = "/manager/" + strconv.Itoa(proid) + "/category/sub?cateId=" +
				strconv.Itoa(info.Pid)
		} else {
			cateListUrl = "/manager/" + strconv.Itoa(proid) + "/category"
		}
		c.JSON(http.StatusOK, gin.H{
			"status":  200,
			"message": "保存成功",
			"data": map[string]interface{}{
				"proid":       proid,
				"cateListUrl": cateListUrl,
			},
		})
	} else {
		c.JSON(http.StatusOK, gin.H{
			"status":  2010,
			"message": "保存失败",
		})
	}
}

//分类详情
func CategoryDetail(c *gin.Context) {

	var cateListUrl string
	var info models.QyClassify
	proid, _ := strconv.Atoi(c.Param("proid"))
	cateId, _ := strconv.Atoi(c.DefaultQuery("cateId", "0"))
	subcateId, _ := strconv.Atoi(c.DefaultQuery("subcateId", "0"))

	if subcateId > 0 {
		info = models.GetClassifyInfo(proid, subcateId)
		cateListUrl = "/manager/" + c.Param("proid") + "/category/sub?cateId=" +
			strconv.Itoa(info.Pid)
	} else {
		info = models.GetClassifyInfo(proid, cateId)
		cateListUrl = "/manager/" + c.Param("proid") + "/category"
	}
	c.HTML(http.StatusOK, "manager_category_detail.html", gin.H{
		"website":     Services.GetWebsite(),
		"proid":       proid,
		"classify":    info,
		"desc":        template.HTML(info.Description),
		"cateListUrl": cateListUrl,
	})
}

//删除分类
func CategoryOperate(c *gin.Context) {

	proid, _ := strconv.Atoi(c.Param("proid"))
	cateId, _ := strconv.Atoi(c.DefaultPostForm("cateId", "0"))
	models.OperateLog("删除Api分类", 3, c)
	//检查分类下是否有子分类，没有的话允许删除
	subids := models.GetSubClassifyIds(proid, cateId)
	if len(subids) > 0 {
		c.JSON(http.StatusOK, gin.H{
			"status":  2012,
			"message": "该分类下有子分类，不允许删除",
		})
	} else {
		if models.UpdateClassifyInfo(proid, cateId, map[string]interface{}{
			"status": 2,
		}) {
			c.JSON(http.StatusOK, gin.H{
				"status":  200,
				"message": "删除成功",
			})
		} else {
			c.JSON(http.StatusOK, gin.H{
				"status":  2010,
				"message": "删除失败",
			})
		}
	}

}
