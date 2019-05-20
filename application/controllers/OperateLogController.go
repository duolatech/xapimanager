package controllers

import (
	"xapimanager/application/Services"
	"xapimanager/application/common"
	"xapimanager/application/models"
	"github.com/gin-gonic/gin"
	"net/http"
	"strconv"
	"time"
)

//操作日志页面
func OperateLog(c *gin.Context) {

	c.HTML(http.StatusOK, "operate_log.html", gin.H{
		"website": Services.GetWebsite(),
	})
}

//操作日志数据
func AjaxOperateLog(c *gin.Context) {

	var userIds []int
	var list []interface{}
	//获取用户信息
	userInfo, _ := c.Get("user")
	oid := userInfo.(map[string]interface{})["oid"].(int)

	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	limit := 20
	start := (page - 1) * limit

	log := models.GetOperateLog(oid, start, limit)

	for _, v := range log["list"].([]models.QyLog) {
		userIds = append(userIds, v.Operator)
	}
	ltype := map[int]string{
		1: "增加",
		2: "修改",
		3: "删除",
	}
	if len(userIds) > 0 {
		userIds = common.RemoveRepByMap(userIds)
		users := models.BatchUsers(userIds)
		for _, v := range log["list"].([]models.QyLog) {
			temp := map[string]interface{}{
				"Organize": v.Organize,
				"Object":   v.Object,
				"Logtype":  ltype[v.Logtype],
				"Operator": users[v.Operator].Username,
				"Desc":     v.Desc,
				"Addtime":  time.Unix(int64(v.Addtime), 0).Format("2006-01-02"),
			}
			list = append(list, temp)
		}
	}

	c.JSON(http.StatusOK, gin.H{
		"status":  200,
		"message": "成功",
		"data": map[string]interface{}{
			"list":       list,
			"totalCount": log["totalCount"],
		},
	})

}
