package requests

import (
	"github.com/gin-gonic/gin"
	"gopkg.in/go-playground/validator.v9"
	"net/http"
)

type CategoryRequest struct {
	Classify string `form:"classify" binding:"required" validate:"required,max=60,min=2"`
}

func CategoryVerify() gin.HandlerFunc {
	return func(c *gin.Context) {

		var data CategoryRequest
		//绑定数据
		errA := c.ShouldBind(&data)

		//校验请求数据
		validate := validator.New()
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
