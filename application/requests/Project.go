package requests

import (
	"github.com/gin-gonic/gin"
	"gopkg.in/go-playground/validator.v9"
	"net/http"
)

type ProjectRequest struct {
	Project string `form:"project" binding:"required" validate:"required,max=20,min=2"`
	Desc    string `form:"desc" binding:"required" validate:"required,max=300,min=2"`
}

func ProjectVerify() gin.HandlerFunc {
	return func(c *gin.Context) {

		var proR ProjectRequest
		//绑定数据
		errA := c.ShouldBind(&proR)

		//校验请求数据
		validate := validator.New()
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
