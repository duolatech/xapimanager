package Services

import (
	"encoding/json"
	"strconv"
	"strings"
	"time"
	"xapimanager/application/models"
	"xapimanager/config"
)

//获取用户权限并保存在cache中
func GetUserAuth(uid int, proid int) (userAuth map[string][]string) {

	var data string
	var err error
	Cache := models.CacheConnect()

	key := "qy_user_userAuth#" + strconv.Itoa(uid) + "#" + strconv.Itoa(proid)

	userAuth = make(map[string][]string)
	if Cache.Hander != nil {
		data, err = Cache.Hander.Get(key).Result()
		if err == nil {
			json.Unmarshal([]byte(data), &userAuth)
		}
	}
	if Cache.Hander == nil || err != nil {
		//获取用户在该项目中的权限组
		group := models.GetProjectGroup(uid, proid)
		//获取菜单权限
		rules := models.GetUserMenuAuth(strings.Split(group.Rules, ","))
		//获取操作节点权限
		operate := models.GetUserOperateAuth(strings.Split(group.Operate, ","))
		//获取用户数据权限(查询分类权限)
		auth := models.GetUserDataAuth(group.Id, []int{2})

		var temp []string
		temp = []string{}
		for _, v := range rules {
			if len(v.Path) > 0 {
				temp = append(temp, v.Path)
			}
		}
		userAuth["rules"] = temp

		temp = []string{}
		for _, v := range operate {
			if len(v.Identify) > 0 {
				temp = append(temp, v.Identify)
			}
		}
		userAuth["operate"] = temp
		if len(auth) > 0 {
			userAuth["dataAuth"] = strings.Split(auth[0].Record, ",")
		} else {
			userAuth["dataAuth"] = []string{}
		}

		data, _ := json.Marshal(userAuth)
		if Cache.Hander != nil {
			Cache.Hander.Set(key, data,
				time.Second*time.Duration(config.GetGlobal().User_Cache))
		}

	}

	return
}

//获取所有可用节点操作
func GetNodeAuth() (nodeAuth map[string]string) {

	var data string
	var err error
	Cache := models.CacheConnect()
	key := "qy_sys_NodeAuth"

	nodeAuth = make(map[string]string)
	if Cache.Hander != nil {
		data, err = Cache.Hander.Get(key).Result()
		if err == nil {
			json.Unmarshal([]byte(data), &nodeAuth)
		}
	}
	if Cache.Hander == nil || err != nil {
		auth := models.GetUserOperateAuth([]string{})
		for _, v := range auth {
			nodeAuth[v.Path] = v.Identify
		}
		data, _ := json.Marshal(nodeAuth)
		if Cache.Hander != nil {
			Cache.Hander.Set(key, data,
				time.Second*time.Duration(config.GetGlobal().User_Cache))
		}
	}

	return
}
