package routes

import (
	"github.com/gin-contrib/sessions"
	"github.com/gin-contrib/sessions/cookie"
	"github.com/gin-gonic/gin"
	"html/template"
	"net/http"
	"time"
	"xapimanager/application/common"
	"xapimanager/application/middleware"
	"xapimanager/config"
)

func InitRouter() *gin.Engine {

	router := gin.New()

	router.StaticFS("/assets", http.Dir(config.GetGlobal().ASSETS_PATH))
	router.StaticFS("/upload", http.Dir(config.GetGlobal().UPLOAD_PATH))

	router.SetFuncMap(template.FuncMap{
		"inArray":    common.InArray,
		"FormatTime": common.FormatTime,
	})
	router.LoadHTMLGlob(config.GetGlobal().TEMPLATE_PATH + "/*") // html模板

	store := cookie.NewStore([]byte("secret"))
	store.Options(sessions.Options{
		MaxAge: int(24 * time.Hour),
		Path:   "/",
	})
	router.Use(sessions.Sessions("xapi", store))

	router.Use(middleware.Handler())
	router.Use(gin.Logger())

	router.NoRoute(func(c *gin.Context) {
		c.HTML(http.StatusOK, "404.html", gin.H{
			"status":  404,
			"message": "找不到你要的内容",
		})
		return
	})

	router.NoMethod(func(c *gin.Context) {
		c.JSON(http.StatusNotFound, gin.H{
			"code": 404,
			"msg":  "找不到该方法",
		})
		return
	})

	//加载路由文件
	webRouter(router)
	projectRouter(router)

	return router
}
