package requests

import (
	"github.com/gin-gonic/gin"
	"gopkg.in/go-playground/validator.v9"
	"net/http"
	"regexp"
)

type RegisterRequest struct {
	Username string `form:"username" binding:"required" validate:"required,max=20,min=2"`
	Password string `form:"password" binding:"required" validate:"required,max=32,min=16"`
	Phone    string `form:"phone" binding:"required" validate:"required,phonecheck"`
	Email    string `form:"email" binding:"required" validate:"required,email"`
}

func RegisterVerify() gin.HandlerFunc {
	return func(c *gin.Context) {

		var data RegisterRequest
		//绑定数据
		errA := c.ShouldBind(&data)

		//校验请求数据
		validate := validator.New()

		//自己定义tag标签以及与之对应的处理逻辑
		validate.RegisterValidation("phonecheck", phonecheck)

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

//手机号 验证
func phonecheck(fl validator.FieldLevel) bool {

	phone := fl.Field().String()
	matched, _ := regexp.MatchString(`^1\d{10}$`, phone)

	return matched
}
