package controllers

import (
	"xapimanager/application/Services"
	"github.com/gin-gonic/gin"
	"net/http"
)

//应用列表
func AppsList(c *gin.Context) {

	c.HTML(http.StatusOK, "apps.html", gin.H{
		"website": Services.GetWebsite(),
	})
}

//json/xml转换
func AppsTransform(c *gin.Context) {
	c.HTML(http.StatusOK, "apps_transform.html", gin.H{
		"website": Services.GetWebsite(),
	})
}

//json格式化、压缩
func AppsJson(c *gin.Context) {
	c.HTML(http.StatusOK, "apps_json.html", gin.H{
		"website": Services.GetWebsite(),
	})
}

//时间戳转换
func AppsTimestamp(c *gin.Context) {
	c.HTML(http.StatusOK, "apps_timestamp.html", gin.H{
		"website": Services.GetWebsite(),
	})
}
