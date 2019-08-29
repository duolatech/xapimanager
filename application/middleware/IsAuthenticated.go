package middleware

import (
	"github.com/gin-gonic/gin"
	"net/http"
)

func Auth() gin.HandlerFunc {
	return func(c *gin.Context) {

		//var code int
		//var data interface{}ls
		//
		//code = utils.SUCCESS
		//token := c.Query("token")
		//if token == "" {
		//	code = utils.INVALID_PARAMS
		//} else {
		//	claims, err := utils.ParseToken(token)
		//	if err != nil {
		//		code = utils.ERROR_AUTH_CHECK_TOKEN_FAIL
		//	} else if time.Now().Unix() > claims.ExpiresAt {
		//		code = utils.ERROR_AUTH_CHECK_TOKEN_TIMEOUT
		//	}
		//}
		//
		//if code != utils.SUCCESS {
		//	c.JSON(http.StatusUnauthorized, gin.H{
		//		"code": code,
		//		"msg":  utils.GetMsg(code),
		//		"data": data,
		//	})
		//
		//	c.Abort()
		//	return
		//}

		//获取用户信息
		userInfo, _ := c.Get("user")
		uid := userInfo.(map[string]interface{})["uid"].(int)
		username := userInfo.(map[string]interface{})["username"].(string)

		//未登录用户直接跳转到登录页
		if uid == 0 || username == "" {
			c.Redirect(http.StatusFound, "/login")
			c.Abort()
			return
		} else {
			c.Set("user", map[string]interface{}{
				"uid":      uid,
				"username": username,
				"oid":      1,
			})
			c.Next()
		}
	}
}
