package main

import (
	"github.com/gin-gonic/gin"
	"runtime"
	"xapimanager/application/common"
	"xapimanager/config"
	"xapimanager/routes"
)

func main() {

	runtime.GOMAXPROCS(runtime.NumCPU())

	sysconfig := config.GetGlobal()

	if sysconfig.DEBUG {
		gin.SetMode(gin.DebugMode)
	} else {
		gin.SetMode(gin.ReleaseMode)
	}

	router := routes.InitRouter()

	router.Run(":" + sysconfig.SERVER_PORT)

	common.Open(sysconfig.SERVER_WEBSITE)

}
