package controllers

import (
	"encoding/json"
	"github.com/gin-gonic/gin"
	"net/http"
	"strconv"
	"time"
	"xapimanager/application/Services"
	"xapimanager/application/models"
	"xapimanager/config"
)

//获取项目列表
func ProjectList(c *gin.Context) {

	//获取用户信息
	var data string
	var err error
	var projects []models.QyProject
	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"].(int)
	oid := userInfo.(map[string]interface{})["oid"].(int)
	Cache := models.CacheConnect()
	key := "qy_user_project_list#" + strconv.Itoa(uid)

	if Cache.Hander != nil {
		data, err = Cache.Hander.Get(key).Result()
		if err == nil {
			json.Unmarshal([]byte(data), &projects)
		}
	}
	if Cache.Hander == nil || err != nil {
		organizeIds := models.GetOrganizeIds(uid)
		//查询用户组私有项目id
		groupIds := models.GetGroupIds(uid)
		proids := models.GetGroupProject(groupIds)
		//查询用户的项目
		projects = models.GetUserProject(organizeIds, proids)
		data, _ := json.Marshal(projects)
		if Cache.Hander != nil {
			Cache.Hander.Set(key, data,
				time.Second*time.Duration(config.GetGlobal().User_Cache))
		}

	}

	//检查用户是否创建权限组
	authNum := models.GetOrganizeGroup(oid)
	c.HTML(http.StatusOK, "project.html", gin.H{
		"website":  Services.GetWebsite(),
		"organize": oid,
		"project":  projects,
		"authNum":  len(authNum),
		"uid":      uid,
	})

}

//创建项目
func ProjectCreate(c *gin.Context) {

	c.HTML(http.StatusOK, "project_info.html", gin.H{
		"website": Services.GetWebsite(),
		"project": models.GetProjectInfo(0),
	})
}

//保存项目
func ProjectSave(c *gin.Context) {

	var gids []string
	var InsertId int
	req := c.Request
	req.ParseForm()

	//获取用户信息
	userInfo, _ := c.Get("user")
	proid, _ := strconv.Atoi(c.DefaultPostForm("proid", "0"))

	for k, param := range req.PostForm {
		if k == "groups[]" {
			gids = param
		}
	}
	attribute, _ := strconv.Atoi(c.DefaultPostForm("attribute", "1"))
	data := map[string]interface{}{
		"organize":  userInfo.(map[string]interface{})["oid"].(int),
		"proname":   c.PostForm("project"),
		"desc":      c.PostForm("desc"),
		"attribute": attribute,
	}
	if proid > 0 {
		//更新项目信息
		if flag, _ := models.ProjectSave(proid, data); flag {
			models.ProjectGroupSave(proid, gids)
			models.OperateLog("编辑项目", 2, c)
		} else {
			c.JSON(http.StatusOK, gin.H{
				"status":  2010,
				"message": "保存失败",
			})
			return
		}
	} else {
		//新增项目
		_, InsertId = models.ProjectSave(proid, data)
		if InsertId > 0 {
			models.ProjectGroupSave(InsertId, gids)
		}
		models.OperateLog("新增项目", 1, c)
	}
	uid := userInfo.(map[string]interface{})["uid"].(int)
	key := "qy_user_project_list#" + strconv.Itoa(uid)
	Services.ClearCache(key)
	c.JSON(http.StatusOK, gin.H{
		"status":  200,
		"message": "保存成功",
		"data":    InsertId,
	})

}

//修改项目
func ProjectModify(c *gin.Context) {

	//查询项目信息
	proid, _ := strconv.Atoi(c.Param("proid"))
	project := models.GetProjectInfo(proid)

	//查询项目下的权限组
	group := models.GetProjectGroupAuth(proid)

	c.HTML(http.StatusOK, "project_info.html", gin.H{
		"website": Services.GetWebsite(),
		"project": project,
		"group":   group,
	})
}

//项目环境
func ProjectEnv(c *gin.Context) {

	//查询项目信息
	proid, _ := strconv.Atoi(c.Param("proid"))
	project := models.GetProjectInfo(proid)

	//查询项目下的环境
	c.HTML(http.StatusOK, "project_env.html", gin.H{
		"website": Services.GetWebsite(),
		"proid":   proid,
		"project": project,
		"env":     models.GetProjectEnv(proid),
	})
}

//项目环境保存
func ProjectEnvSave(c *gin.Context) {

	//查询项目信息
	proid, _ := strconv.Atoi(c.Param("proid"))
	envid, _ := strconv.Atoi(c.PostForm("envid"))
	sort, _ := strconv.Atoi(c.PostForm("sort"))

	var status int
	if c.PostForm("isopen") == "on" {
		status = 1
	} else {
		status = 2
	}
	data := map[string]interface{}{
		"proid":   proid,
		"envname": c.PostForm("envname"),
		"domain":  c.PostForm("domain"),
		"sort":    sort,
		"status":  status,
	}

	if models.ProjectEnvSave(envid, data) {
		c.JSON(http.StatusOK, gin.H{
			"status":  200,
			"message": "保存成功",
		})
	} else {
		c.JSON(http.StatusOK, gin.H{
			"status":  2010,
			"message": "保存失败",
		})
	}

}

//切换项目环境
func ProjectEnvChange(c *gin.Context) {

	//获取用户信息
	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"]

	proid, _ := strconv.Atoi(c.DefaultPostForm("proid", "0"))
	envid, _ := strconv.Atoi(c.DefaultPostForm("envid", "0"))

	if models.ProjectEnvChange(uid.(int), proid, envid) {
		c.JSON(http.StatusOK, gin.H{
			"status":  200,
			"message": "切换成功",
		})
	} else {
		c.JSON(http.StatusOK, gin.H{
			"status":  2010,
			"message": "切换失败",
		})
	}
}
