package routes

import (
	"xapimanager/application/controllers"
	"xapimanager/application/controllers/manager"
	"xapimanager/application/middleware"
	"github.com/gin-gonic/gin"
)

func webRouter(router *gin.Engine) {

	basicRouter := router.Group("")
	{
		//页面路由
		basicRouter.GET("/", middleware.Auth(), controllers.Index)
		basicRouter.GET("/main", middleware.Auth(), controllers.Index)
		basicRouter.GET("/login", controllers.Login)
		basicRouter.GET("/register", controllers.Register)

		//manager 主页
		basicRouter.GET("/manager/:proid", middleware.Auth(), controllers.Manager)
		//mock 测试
		basicRouter.Any("/mock/*action", manager.MockTest)
	}

	apiRouter := router.Group("/manager/:proid", middleware.AuthCheck())
	{
		//控制台
		apiRouter.GET("/dash", manager.Dashboard)
		//Api 搜索
		apiRouter.GET("/Api/search", manager.ApiSearch)
		//Api 详情
		apiRouter.GET("/Api/detail", manager.GetApiDetail)
		//Api 列表
		apiRouter.GET("/Api/list", manager.GetApiList)
		//Api 添加
		apiRouter.GET("/Api/info", manager.ApiInfo)
		//Api 待审核
		apiRouter.GET("/Api/audit", manager.ApiAudit)
		//Api 分类
		apiRouter.GET("/category", manager.Category)
		//Api 子分类
		apiRouter.GET("/category/sub", manager.CategorySub)
		//Api 分类添加
		apiRouter.GET("/category/info", manager.CategoryInfo)
		//Api 子分类添加
		apiRouter.GET("/category/infoSub", manager.CategoryInfoSub)
		//Api分类详情
		apiRouter.GET("/category/detail", manager.CategoryDetail)
		//Api 企业密钥
		apiRouter.GET("/company", manager.Company)
		//Api 企业密钥编辑
		apiRouter.GET("/company/info", manager.CompanyInfo)
		//消息列表
		apiRouter.GET("/message/list", controllers.MessageList)
		//帮助中心
		apiRouter.GET("/help/list", controllers.HelpList)
		//新增帮助
		apiRouter.GET("/help/info", controllers.HelpInfo)
	}
	projectRouter := router.Group("/project", middleware.Auth())
	{
		//项目列表页
		projectRouter.GET("", controllers.ProjectList)
		//创建项目
		projectRouter.GET("/create", controllers.ProjectCreate)
		//项目修改
		projectRouter.GET("/info/:proid", controllers.ProjectModify)
		//项目环境
		projectRouter.GET("/env/:proid", controllers.ProjectEnv)
	}
	applicationRouter := router.Group("/apps")
	{
		//应用列表
		applicationRouter.GET("", controllers.AppsList)
		//json\xml转换
		applicationRouter.GET("/trans", controllers.AppsTransform)
		//json格式化、压缩
		applicationRouter.GET("/json", controllers.AppsJson)
		//时间戳转化
		applicationRouter.GET("/timestamp", controllers.AppsTimestamp)
	}

	organizeRouter := router.Group("/organize", middleware.Auth())
	{
		//组织信息
		organizeRouter.GET("", controllers.OrganizeList)
		//组织详情
		organizeRouter.GET("/detail/:oid", controllers.OrganizeDetail)
	}
	userRouter := router.Group("/users", middleware.Auth())
	{
		//用户列表
		userRouter.GET("", controllers.UserList)
		//用户详情
		userRouter.GET("/detail/:userid", controllers.UserDetail)
		//个人中心
		userRouter.GET("/person", controllers.UserPerson)
	}
	groupRouter := router.Group("/group", middleware.Auth())
	{
		//权限组列表
		groupRouter.GET("", controllers.GroupList)
		//编辑权限组
		groupRouter.GET("/info", controllers.GroupInfo)
		//功能权限
		groupRouter.GET("/featureAuth/:gid", controllers.GroupfeatureAuth)
		//数据权限
		groupRouter.GET("/dataAuth/:gid", controllers.GroupdataAuth)
	}
	logRouter := router.Group("/log", middleware.Auth())
	{
		//日志列表
		logRouter.GET("", controllers.OperateLog)
	}
	websiteRouter := router.Group("/website", middleware.Auth())
	{
		//网站设置
		websiteRouter.GET("", controllers.Website)
	}
	messageRouter := router.Group("/message", middleware.Auth())
	{
		//消息列表
		messageRouter.GET("/list", controllers.MessageList)
		//消息详情
		messageRouter.GET("/detail/:mid", controllers.MessageDetail)

	}
	helpRouter := router.Group("/help", middleware.Auth())
	{
		//帮助中心
		helpRouter.GET("/list", controllers.HelpList)
		//消息详情
		helpRouter.GET("/detail/:hid", controllers.HelpDetail)
		//新增帮助
		helpRouter.GET("/info", controllers.HelpInfo)
	}

}
