package controllers

import (
	"xapimanager/application/Services"
	"xapimanager/application/models"
	"github.com/gin-gonic/gin"
	"net/http"
	"strconv"
)

type UserOrganize struct {
	Uid      int
	Leader   int
	Organize int
	Name     string
	Desc     string
	Identify string
	Icon     string
	Invite   string
}

//获取项目列表
func OrganizeList(c *gin.Context) {

	//获取用户信息
	userInfo, _ := c.Get("user")

	//查询用户的组织及用户组
	uid := userInfo.(map[string]interface{})["uid"].(int)
	//查询用户自己的团队信息
	self := models.GetUserOrganize(uid)
	if self.Id == 0 {
		models.OrganizeJoin(uid, 1, 1)
	}

	organize := models.GetOrganize(uid)

	data := map[string][]interface{}{}
	var OrganizeNo string
	var tmp UserOrganize
	for _, v := range organize {
		tmp = UserOrganize{
			v.Uid,
			v.Leader,
			v.Organize,
			v.Name,
			v.Desc,
			v.Identify,
			v.Icon,
			"http://" + c.Request.Host + "/register?invite=" + v.Identify,
		}
		if v.Uid == v.Leader {
			data["self"] = append(data["self"], tmp)
			OrganizeNo = v.Identify
		} else {
			data["everyone"] = append(data["everyone"], tmp)
		}
	}

	c.HTML(http.StatusOK, "organize.html", gin.H{
		"website":    Services.GetWebsite(),
		"OrganizeNo": OrganizeNo,
		"data":       data,
	})

}

//组织搜索
func Search(c *gin.Context) {

	var orginfo models.QyOrganize
	identify := c.Param("identify")

	org := models.GetOrganizeInfo(identify)
	if len(org) > 0 {
		orginfo = org[0]
	} else {
		orginfo = models.QyOrganize{}
	}
	c.JSON(http.StatusOK, gin.H{
		"status":  200,
		"message": "成功",
		"data":    orginfo,
	})

}

//加入组织
func OrganizeJoin(c *gin.Context) {

	identify := c.Param("identify")
	org := models.GetOrganizeOne(identify)

	//获取用户信息
	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"].(int)
	err := models.OrganizeJoin(uid, org.Id, 2)

	if err == nil {
		c.JSON(http.StatusOK, gin.H{
			"status":  200,
			"message": "成功",
		})
	} else {
		c.JSON(http.StatusOK, gin.H{
			"status":  4010,
			"message": err.Error(),
		})
	}

}

//退出组织
func OrganizeQuit(c *gin.Context) {

	//获取用户信息
	userInfo, _ := c.Get("user")
	uid := userInfo.(map[string]interface{})["uid"].(int)

	//组织id
	oid := c.Param("oid")
	organizeId, _ := strconv.Atoi(oid)
	err := models.OrganizeQuit(uid, organizeId)

	if err == nil {
		c.JSON(http.StatusOK, gin.H{
			"status":  200,
			"message": "退出成功",
		})
	} else {
		c.JSON(http.StatusOK, gin.H{
			"status":  4010,
			"message": "退出失败",
		})
	}
}

//组织详情
func OrganizeDetail(c *gin.Context) {

	//组织id
	var orginfo models.QyOrganize
	oid := c.Param("oid")

	organizeId, _ := strconv.Atoi(oid)
	org := models.GetOrganizeDetail(organizeId)
	if len(org) > 0 {
		orginfo = org[0]
	} else {
		orginfo = models.QyOrganize{}
	}
	c.HTML(http.StatusOK, "organize_detail.html", gin.H{
		"website": Services.GetWebsite(),
		"org":     orginfo,
	})
}

//保存修改信息
func OrganizeSave(c *gin.Context) {

	//组织id
	oid := c.Param("oid")
	organizeId, _ := strconv.Atoi(oid)

	data := map[string]interface{}{
		"name": c.PostForm("organize_name"),
		"desc": c.PostForm("organize_desc"),
	}
	err := models.OrganizeSave(organizeId, data)
	if err == nil {
		c.JSON(http.StatusOK, gin.H{
			"status":  200,
			"message": "保存成功",
		})
	} else {
		c.JSON(http.StatusOK, gin.H{
			"status":  4010,
			"message": "保存失败",
		})
	}
}
