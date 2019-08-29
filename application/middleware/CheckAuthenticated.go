package middleware

import (
	"github.com/gin-gonic/gin"
	"net/http"
	"regexp"
	"strconv"
	"xapimanager/application/Services"
	"xapimanager/application/common"
)

//需排除权限限制的页面
var exclude = []string{
	"/users",            //用户列表
	"/group",            //权限管理
	"/category/detail",  //分类详情
	"/category/sub",     //子分类列表
	"/category/infoSub", //子分类添加页
	"/Api/detail",       //Api 详情页
}

//页面权限检查, 检查用户是否用该页面的访问权限
func AuthCheck() gin.HandlerFunc {
	return func(c *gin.Context) {

		//获取用户信息
		userInfo, _ := c.Get("user")
		uid := userInfo.(map[string]interface{})["uid"].(int)
		username := userInfo.(map[string]interface{})["username"].(string)

		//未登录用户直接跳转到登录页
		if uid == 0 || username == "" {
			c.Redirect(http.StatusFound, "/login")
			c.Abort()
			return
		}

		proid, _ := strconv.Atoi(c.Param("proid"))
		c.Set("user", map[string]interface{}{
			"uid":      uid,
			"username": username,
			"oid":      1,
			"auth":     Services.GetUserAuth(uid, proid),
		})
		//项目权限检查
		project := Services.GetUserProject(uid)
		flag := false
		for _, v := range project {
			if proid == v.Id {
				flag = true
			}
		}
		if !flag {
			c.HTML(http.StatusOK, "404.html", gin.H{
				"status":  510,
				"message": "您没有权限访问该项目，请联系管理员",
			})
			//终止
			c.Abort()
		}
		//用户权限检查
		auth := Services.GetUserAuth(uid, proid)

		//匹配当前路由
		uri := c.Request.RequestURI
		reg := regexp.MustCompile(`\/manager\/\d+(\/[0-9A-Za-z\/]+)`)
		match := reg.FindStringSubmatch(uri)
		authflag := false

		if len(match) > 0 && len(match[1]) > 0 {
			if common.CheckAuth(match[1], exclude) {
				authflag = true
			} else {
				authflag = common.CheckAuth(match[1], auth["rules"])
			}
		}

		if !authflag {
			c.HTML(http.StatusOK, "404.html", gin.H{
				"status":  513,
				"message": "您没有权限访问该页面，请联系管理员",
			})
			c.Abort()
		}

		c.Next()
	}
}
