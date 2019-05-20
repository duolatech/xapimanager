package manager

import (
	"xapimanager/application/Services"
	"xapimanager/application/common"
	"xapimanager/application/models"
	"github.com/gin-gonic/gin"
	"net/http"
	"strconv"
)

//企业秘钥列表页
func Company(c *gin.Context) {

	proid, _ := strconv.Atoi(c.Param("proid"))
	c.HTML(http.StatusOK, "manager_company.html", gin.H{
		"website": Services.GetWebsite(),
		"proid":   proid,
	})
}

//企业密钥列表
func GetAjaxCompany(c *gin.Context) {

	//获取用户权限
	userInfo, _ := c.Get("user")
	auth := userInfo.(map[string]interface{})["auth"].(map[string][]string)

	proid, _ := strconv.Atoi(c.Param("proid"))
	page, _ := strconv.Atoi(c.DefaultQuery("page", "1"))
	company := c.Query("company")
	start := (page - 1) * (limit)

	data := models.CompanyList(proid, company, start, limit)

	//功能节点权限检查
	data["auth"] = map[string]bool{
		"modifyCompany": common.CheckAuth("modifyCompany", auth["operate"]),
		"delCompany":    common.CheckAuth("delCompany", auth["operate"]),
	}
	data["proid"] = proid
	c.JSON(http.StatusOK, gin.H{
		"status":  200,
		"message": "成功",
		"data":    data,
	})

}

//企业秘钥编辑页面
func CompanyInfo(c *gin.Context) {

	proid, _ := strconv.Atoi(c.Param("proid"))
	id, _ := strconv.Atoi(c.DefaultQuery("id", "0"))

	c.HTML(http.StatusOK, "manager_company_info.html", gin.H{
		"website": Services.GetWebsite(),
		"proid":   proid,
		"company": models.GetCompany(id),
	})
}

//企业密钥保存
func CompanySave(c *gin.Context) {

	proid, _ := strconv.Atoi(c.Param("proid"))
	comid, _ := strconv.Atoi(c.DefaultPostForm("comid", "0"))
	status, _ := strconv.Atoi(c.DefaultPostForm("status", "1"))
	models.OperateLog("编辑企业密钥", 2, c)
	if models.CompanySave(comid, map[string]interface{}{
		"proid":     proid,
		"company":   c.PostForm("company"),
		"appid":     c.PostForm("appId"),
		"appsecret": c.PostForm("appSecret"),
		"status":    status,
	}) {
		c.JSON(http.StatusOK, gin.H{
			"status":  200,
			"message": "成功",
			"data":    map[string]int{"proid": proid},
		})
	} else {
		c.JSON(http.StatusOK, gin.H{
			"status":  2010,
			"message": "保存失败",
		})
	}

}

//企业密钥操作
func CompanyOperate(c *gin.Context) {

	proid, _ := strconv.Atoi(c.Param("proid"))
	id, _ := strconv.Atoi(c.DefaultPostForm("id", "0"))
	if models.CompanyOperate(id, proid) {
		c.JSON(http.StatusOK, gin.H{
			"status":  200,
			"message": "删除成功",
		})
	} else {
		c.JSON(http.StatusOK, gin.H{
			"status":  2010,
			"message": "删除失败",
		})
	}

}
