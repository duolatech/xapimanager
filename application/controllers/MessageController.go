package controllers

import (
	"xapimanager/application/Services"
	"xapimanager/application/common"
	"xapimanager/application/models"
	"github.com/gin-gonic/gin"
	"net/http"
	"strings"
	"time"
)

var limit = 20

//消息列表
func MessageList(c *gin.Context) {

	c.HTML(http.StatusOK, "message_list.html", gin.H{
		"website": Services.GetWebsite(),
	})
}

//消息列表 Api
func GetAjaxMessageList(c *gin.Context) {
	//获取用户信息
	var sender string
	userInfo, _ := c.Get("user")
	data := map[string]interface{}{
		"receiver": userInfo.(map[string]interface{})["uid"].(int),
	}
	//查询消息列表
	page := common.StringToInt(c.DefaultQuery("page", "1"))
	start := (page - 1) * limit
	Message := models.GetMessageList(data, start, limit)
	result := []map[string]interface{}{}
	for _, v := range Message["list"].([]models.UserMessage) {
		sender = v.Username
		if v.Sender == 1 {
			sender = "管理员"
		}
		temp := map[string]interface{}{
			"id":       v.Id,
			"sender":   sender,
			"subject":  v.Subject,
			"isread":   v.Isread,
			"sendtime": time.Unix(int64(v.Sendtime), 0).Format("2006-01-02 03:04:05"),
		}
		result = append(result, temp)
	}

	c.JSON(http.StatusOK, gin.H{
		"status":  200,
		"message": "成功",
		"data": map[string]interface{}{
			"totalCount": Message["totalCount"],
			"list":       result,
		},
	})
}

//更新已读和删除，
func MessageOperate(c *gin.Context) {

	var flag bool
	//获取用户信息
	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"].(int)
	mids := strings.Split(c.PostForm("mids"), ",")
	status := common.StringToInt(c.PostForm("status"))

	switch status {
	case 1: //更新为已读
		flag = models.ReadUpdate(uid, mids)
	case 2: //删除
		flag = models.DeleteMessage(uid, mids)
	}
	if flag {
		c.JSON(http.StatusOK, gin.H{
			"status":  200,
			"message": "操作成功",
		})
	} else {
		c.JSON(http.StatusOK, gin.H{
			"status":  2010,
			"message": "操作失败，请稍后重试",
		})
	}

}

//消息详情
func MessageDetail(c *gin.Context) {

	var sender string
	//获取用户信息及详细详情
	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"].(int)
	mid := common.StringToInt(c.Param("mid"))
	detail := models.GetMessageDetail(uid, mid)
	sender = detail.Username
	if detail.Sender == 1 {
		sender = "管理员"
	}
	data := map[string]interface{}{
		"id":       detail.Id,
		"sender":   sender,
		"subject":  detail.Subject,
		"isread":   detail.Isread,
		"content":  detail.Content,
		"sendtime": time.Unix(int64(detail.Sendtime), 0).Format("2006-01-02 03:04:05"),
	}

	c.HTML(http.StatusOK, "message_detail.html", gin.H{
		"website": Services.GetWebsite(),
		"data":    data,
	})
}

//获取未读消息数
func GetUnreadMessage(c *gin.Context) {

	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"].(int)
	count := models.GetUnreadMessage(uid)
	c.JSON(http.StatusOK, gin.H{
		"status":  200,
		"message": "成功",
		"data":    map[string]int{"count": count},
	})
}
