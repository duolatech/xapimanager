package models

import (
	"encoding/json"
	"github.com/gin-gonic/gin"
	"time"
)

type QyLog struct {
	Organize int
	Object   string
	Logtype  int
	Operator int
	Desc     string
	Addtime  int
}

//保存操作日志
func OperateLog(object string, longtype int, c *gin.Context) bool {

	defer Db.Close()
	Db = Connect()
	//获取用户信息
	req := c.Request
	req.ParseForm()
	userInfo, _ := c.Get("user")
	info, _ := json.Marshal(
		map[string]interface{}{
			"URI":    req.URL.Path,
			"Method": req.Method,
			"Param":  req.PostForm,
		})

	var log QyLog
	log = QyLog{
		userInfo.(map[string]interface{})["oid"].(int),
		object,
		longtype,
		userInfo.(map[string]interface{})["uid"].(int),
		string(info),
		int(time.Now().Unix()),
	}
	err := Db.Hander.Create(&log).Error
	if err != nil {
		return false
	}
	return true
}

//获取分页操作日志
func GetOperateLog(oid int, start int, limit int) (result map[string]interface{}) {

	defer Db.Close()
	Db = Connect()
	var count int
	var log []QyLog

	info := Db.Hander.Table("qy_log").
		Where("organize=?", oid)

	//查询信息
	info.Count(&count)
	info.Offset(start).Limit(limit).Find(&log)

	result = map[string]interface{}{
		"totalCount": count,
		"list":       log,
	}

	return

}
