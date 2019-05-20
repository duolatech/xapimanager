package controllers

import (
	"xapimanager/application/Services"
	"xapimanager/application/models"
	"github.com/gin-gonic/gin"
	"net/http"
)

//获取网站设置页
func Website(c *gin.Context) {

	c.HTML(http.StatusOK, "website.html", gin.H{
		"website": Services.GetWebsite(),
		"site":    models.GetWebsite(),
	})

}

//保存网站信息
func WebsiteInfo(c *gin.Context) {

	var data = []string{"sitename", "title", "keywords", "description", "copyright"}
	for _, v := range data {
		models.WebsiteSave(v, c.PostForm(v))
	}
	Services.ClearCache("qy_website")
	c.JSON(http.StatusOK, gin.H{
		"status":  200,
		"message": "保存成功",
	})
}
