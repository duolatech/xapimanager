package manager

import (
	"xapimanager/application/Services"
	"xapimanager/application/models"
	"github.com/gin-gonic/gin"
	"math/rand"
	"net/http"
	"strconv"
)

//控制台
func Dashboard(c *gin.Context) {

	proid, _ := strconv.Atoi(c.Param("proid"))

	c.HTML(http.StatusOK, "manager_dash.html", gin.H{
		"website": Services.GetWebsite(),
		"proid":   proid,
	})
}

//各地区api调用量
func DashboardArea(c *gin.Context) {

	var result []map[string]interface{}
	data := models.GetAreaData()

	for _, v := range data {
		temp := map[string]interface{}{
			"area_id":   v.AreaId,
			"name":      v.AreaName,
			"value":     rand.Intn(200),
			"longitude": v.Longitude,
			"latitude":  v.Latitude,
		}
		result = append(result, temp)
	}

	c.JSON(http.StatusOK, gin.H{
		"status":  200,
		"message": "成功",
		"data":    result,
	})
}
