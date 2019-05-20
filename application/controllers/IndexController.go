package controllers

import (
	"xapimanager/application/Services"
	"xapimanager/application/models"
	_ "xapimanager/application/utils"
	"github.com/gin-contrib/sessions"
	"github.com/gin-gonic/gin"
	"net/http"
	"strconv"
	"strings"
)

func Index(c *gin.Context) {

	//日志记录示例
	//data := map[string]interface{}{
	//	"filename": "routes",
	//	"size":     10,
	//	"username": c.PostForm("username"),
	//	"passwd":   c.PostForm("passwd"),
	//	"token":    c.Request.Header.Get("Authorization"),
	//}
	//token := c.Request.Header.Get("Authorization")
	//utils.Log.WithFields(data).Info("路由文件记录")
	//获取用户信息
	userInfo, _ := c.Get("user")
	//查询用户组及该组的功能权限
	uid := userInfo.(map[string]interface{})["uid"].(int)
	gid := models.GetUserGroup(uid)

	var menu []models.Allmenu
	if gid == 1 {
		menu = models.GetMenu(1, 0)
	} else {
		rules := []string{"1", "2", "3", "4", "5", "7", "8", "9", "10", "14", "15", "16", "17", "18"}
		menu = models.GetManagerMenu(1, 0, rules)
	}

	session := sessions.Default(c)
	c.HTML(http.StatusOK, "index.html", gin.H{
		"website": Services.GetWebsite(),
		"menu":    menu,
		"userinfo": map[string]interface{}{
			"username": session.Get("username"),
			"avatar":   session.Get("avatar"),
		},
	})
}

func Manager(c *gin.Context) {

	proid, _ := strconv.Atoi(c.Param("proid"))
	env := models.GetProjectValidEnv(proid, "asc")

	//获取用户信息
	userInfo, _ := c.Get("user")
	//查询用户组及该组的功能权限
	uid := userInfo.(map[string]interface{})["uid"]
	group := Services.GetProjectGroup(uid.(int), proid)
	rules := strings.Split(group.Rules, ",")
	menu := models.GetManagerMenu(2, 0, rules)

	//用户当前环境
	cenv := models.GetCurrentEnv(uid.(int), proid)
	currentEnv := map[int]string{
		0: "请选择环境",
	}
	for _, v := range env {
		if cenv.Envid == v.Id {
			delete(currentEnv, 0)
			currentEnv[v.Id] = v.Envname
		}
	}
	session := sessions.Default(c)

	c.HTML(http.StatusOK, "index_api.html", gin.H{
		"website":    Services.GetWebsite(),
		"apimenu":    menu,
		"projectEnv": env,
		"proid":      proid,
		"currentEnv": currentEnv,
		"userinfo": map[string]interface{}{
			"username": session.Get("username"),
			"avatar":   session.Get("avatar"),
		},
	})
}
