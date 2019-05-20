package middleware

import (
	"github.com/gin-contrib/sessions"
	"github.com/gin-gonic/gin"
)

func Handler() gin.HandlerFunc {
	return func(c *gin.Context) {

		session := sessions.Default(c)
		//获取用户信息
		uid := session.Get("uid")
		username := session.Get("username")
		avatar := session.Get("avatar")

		//获取用户信息
		if uid == nil || username == nil {
			c.Set("user", map[string]interface{}{
				"uid":      0,
				"username": "",
				"avatar":   "/assets/images/avatar.png",
			})
		} else {
			c.Set("user", map[string]interface{}{
				"uid":      uid.(int),
				"username": username.(string),
				"avatar":   avatar,
			})
		}
		c.Next()
	}
}
