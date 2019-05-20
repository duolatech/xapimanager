package controllers

import (
	"xapimanager/application/models"
	"github.com/gin-gonic/gin"
	"net/http"
	"strconv"
)

/**
 * 清除用户缓存，不检查是否成功
 * 缓存分为两类，用户缓存(以qy_user_开头)、系统缓存(以qy_sys_开头)
 */
func ClearCache(c *gin.Context) {

	//获取用户权限
	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"].(int)

	Cache := models.CacheConnect()
	CacheList := []string{
		"qy_user_userAuth#" + strconv.Itoa(uid),
		"qy_user_project_group#" + strconv.Itoa(uid),
		"qy_user_project_list#" + strconv.Itoa(uid),
	}
	for _, key := range CacheList {
		if Cache.Hander != nil {
			Cache.Hander.Del(key).Result()
		}
	}

	c.JSON(http.StatusOK, gin.H{
		"status":  200,
		"message": "缓存清理成功",
	})
}
