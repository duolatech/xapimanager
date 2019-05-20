package controllers

import (
	"xapimanager/application/Services"
	"xapimanager/application/common"
	"xapimanager/application/models"
	"github.com/gin-gonic/gin"
	"html/template"
	"net/http"
	"regexp"
	"time"
)

//帮助中心页
func HelpList(c *gin.Context) {
	c.HTML(http.StatusOK, "help_list.html", gin.H{
		"website": Services.GetWebsite(),
	})
}

//帮组中心Api
func AjaxHelpList(c *gin.Context) {

	//用户自己的组织信息
	userInfo, _ := c.Get("user")
	oid := userInfo.(map[string]interface{})["oid"].(int)
	data := map[string]interface{}{
		"organize": oid,
	}
	page := common.StringToInt(c.DefaultQuery("page", "1"))
	start := (page - 1) * limit

	help := models.GetHelpList(data, start, limit)
	result := []map[string]interface{}{}
	for _, v := range help["list"].([]models.UserHelp) {
		temp := map[string]interface{}{
			"id":      v.Id,
			"author":  v.Username,
			"title":   v.Title,
			"content": common.SubString(common.StripTags(v.Content), 0, 100, true),
			"ctime":   time.Unix(int64(v.Ctime), 0).Format("2006-01-02"),
		}
		result = append(result, temp)
	}
	c.JSON(http.StatusOK, gin.H{
		"status":  200,
		"message": "成功",
		"data": map[string]interface{}{
			"totalCount": help["totalCount"],
			"list":       result,
		},
	})

}

//帮助中心详情
func HelpDetail(c *gin.Context) {

	//用户自己的组织信息
	userInfo, _ := c.Get("user")
	oid := userInfo.(map[string]interface{})["oid"].(int)
	hid := common.StringToInt(c.Param("hid"))

	detail := models.GetHelpDetail(oid, hid)

	c.HTML(http.StatusOK, "help_detail.html", gin.H{
		"website": Services.GetWebsite(),
		"data": map[string]interface{}{
			"id":      detail.Id,
			"author":  detail.Username,
			"title":   detail.Title,
			"content": template.HTML(detail.Content),
			"ctime":   time.Unix(int64(detail.Ctime), 0).Format("2006-01-02"),
		},
	})

}

//帮助中心操作
func HelpOperate(c *gin.Context) {

	//用户自己的组织信息
	userInfo, _ := c.Get("user")
	oid := userInfo.(map[string]interface{})["oid"].(int)
	hid := common.StringToInt(c.PostForm("hid"))

	if models.DeleteHelp(oid, hid) {
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

//新增帮助
func HelpInfo(c *gin.Context) {

	//用户自己的组织信息
	userInfo, _ := c.Get("user")
	oid := userInfo.(map[string]interface{})["oid"].(int)
	hid := common.StringToInt(c.Query("hid"))
	detail := models.GetHelpDetail(oid, hid)
	c.HTML(http.StatusOK, "help_info.html", gin.H{
		"website": Services.GetWebsite(),
		"data": map[string]interface{}{
			"id":      detail.Id,
			"title":   detail.Title,
			"content": template.HTML(detail.Content),
		},
	})
}

//新增帮助保存
func HelpStore(c *gin.Context) {

	//用户自己的组织信息
	userInfo, _ := c.Get("user")
	oid := userInfo.(map[string]interface{})["oid"].(int)
	uid := userInfo.(map[string]interface{})["uid"].(int)
	hid := common.StringToInt(c.PostForm("hid"))

	content := c.PostForm("content")

	//图片移动及替换
	re, _ := regexp.Compile("\\/upload\\/images.+?\\.\\w+")
	content = re.ReplaceAllStringFunc(content, common.ReplaceImage)

	//删除编辑器版权说明
	re1, _ := regexp.Compile("<p data-f-id=\"pbf\".+?<\\/p>")
	content = re1.ReplaceAllStringFunc(content, func(str string) string { return "" })

	data := map[string]interface{}{
		"author":  uid,
		"title":   c.PostForm("title"),
		"content": content,
	}
	if models.HelpStore(oid, hid, data) {
		c.JSON(http.StatusOK, gin.H{
			"status":  200,
			"message": "保存成功",
		})
	} else {
		c.JSON(http.StatusOK, gin.H{
			"status":  2010,
			"message": "保存失败，请稍后重试！",
		})
	}
}
