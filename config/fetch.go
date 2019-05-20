package config

/*
 * 该变量用于记录页面与调用Api的映射关系
 * 在权限控制中如果页面不能访问的话，页面调用的接口也不能访问
 * 所谓页面调用的接口，其实是页面的ajax请求
 */

var fetch = map[string][]string{
	"/dash": []string{ //控制台页面
		"/dash/area"},
	"/Api/audit": []string{ //待审核Api页面
		"/Api/audit"},
	"/Api/list": []string{ //Api 列表页面
		"/Api"},
	"/Api/info": []string{ //Api添加、编辑、发布、删除
		"/Api/store",
		"/Api/operate",
		"/Api/publish",
		"/Api/discard"},
	"/company": []string{ //企业密钥页
		"/company/list",
		"/company/operate"},
	"/company/info": []string{ //企业密钥创建/编辑页
		"/company/store"},
	"/category/info": []string{ //分类添加/编辑
		"/category/store",
		"/category/operate"},
}

func GetApiFetch() (Apifetch map[string]string) {

	Apifetch = map[string]string{}
	for ko, vol := range fetch {
		for _, v := range vol {
			Apifetch[v] = ko
		}
	}
	return
}
