package requests

import (
	"github.com/gin-gonic/gin"
	"gopkg.in/go-playground/validator.v9"
	"net/http"
	"regexp"
)

type ProjectEnvRequest struct {
	Envname string `form:"envname" binding:"required" validate:"required,max=20,min=2"`
	Domain  string `form:"domain" binding:"required" validate:"envDomain"`
	Sort    string `form:"sort" binding:"required" validate:"envSort"`
}

func ProjectEnv() gin.HandlerFunc {
	return func(c *gin.Context) {

		var proR ProjectEnvRequest
		//绑定数据
		errA := c.ShouldBind(&proR)

		//校验请求数据
		validate := validator.New()
		//自己定义tag标签以及与之对应的处理逻辑
		validate.RegisterValidation("envDomain", envDomain)
		validate.RegisterValidation("envSort", envSort)
		errB := validate.Struct(&proR)

		if errA != nil || errB != nil {
			c.JSON(http.StatusInternalServerError, gin.H{
				"status":  5010,
				"message": "请求参数不合法",
			})
			//终止
			c.Abort()
		} else {
			//该句可以省略，写出来只是表明可以进行验证下一步中间件，不写，也是内置会继续访问下一个中间件的
			c.Next()
		}

	}
}

//环境域名验证
func envDomain(fl validator.FieldLevel) bool {

	domain := fl.Field().String()
	matched, _ := regexp.MatchString(`^((https|http)?:\/\/)+[A-Za-z0-9\-]+(\.[A-Za-z0-9\-]+)+((:)+[0-9]{1,5})?(\/)?$`, domain)

	return matched
}

//环境排序验证
func envSort(fl validator.FieldLevel) bool {

	sort := fl.Field().String()
	matched, _ := regexp.MatchString(`^\d+?$`, sort)

	return matched
}
