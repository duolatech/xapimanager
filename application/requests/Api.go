package requests

import (
	"github.com/gin-gonic/gin"
	"gopkg.in/go-playground/validator.v9"
	"net/http"
	"regexp"
)

type ApiRequest struct {
	Apiname        string `form:"apiname" binding:"required" validate:"required,max=20,min=2"`
	Subclassify    string `form:"subClassify" binding:"required" validate:"required,max=10,min=1"`
	Version        string `form:"version" binding:"required" validate:"required,max=10,min=1"`
	Requesttype    string `form:"requesttype" binding:"required" validate:"required,max=10,min=1"`
	Gateway        string `form:"gateway" binding:"required" validate:"gatewayURl"`
	Local          string `form:"local" binding:"required" validate:"localURL"`
	Network        string `form:"network" binding:"required" validate:"required,max=2,min=1"`
	Authentication string `form:"authentication" binding:"required" validate:"required,max=2,min=1"`
	Description    string `form:"description" binding:"required" validate:"required,min=1"`
}

func ApiVerify() gin.HandlerFunc {
	return func(c *gin.Context) {

		var data ApiRequest
		//绑定数据
		errA := c.ShouldBind(&data)

		//校验请求数据
		validate := validator.New()
		//自己定义tag标签以及与之对应的处理逻辑
		validate.RegisterValidation("gatewayURl", gatewayURl)
		validate.RegisterValidation("localURL", localURL)
		errB := validate.Struct(&data)

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

//gateway uri验证
func gatewayURl(fl validator.FieldLevel) bool {

	domain := fl.Field().String()
	matched, _ := regexp.MatchString(`^\/[A-Za-z0-9\-]+[\/=\?%\-&_~@[\]\':+!]*([^<>\"\"])*$`, domain)

	return matched
}

//local url校验
func localURL(fl validator.FieldLevel) bool {

	sort := fl.Field().String()
	matched, _ := regexp.MatchString(`^((https|http)?:\/\/)+[A-Za-z0-9\-]+\.[A-Za-z0-9\-]+[\/=\?%\-&_~@[\]\':+!]*([^<>\"\"])*`, sort)

	return matched
}
