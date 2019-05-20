package Services

import (
	"encoding/json"
	"strconv"
	"time"
	"xapimanager/application/models"
	"xapimanager/config"
)

//获取用户在指定项目的权限组
func GetProjectGroup(uid int, proid int) (group models.AuthGroup) {

	var data string
	var err error
	group = models.AuthGroup{}
	Cache := models.CacheConnect()
	key := "qy_user_project_group#" + strconv.Itoa(uid)

	if Cache.Hander != nil {
		data, err = Cache.Hander.Get(key).Result()
		if err == nil {
			json.Unmarshal([]byte(data), &group)
		}
	}
	if Cache.Hander == nil || err != nil {
		group = models.GetProjectGroup(uid, proid)
		jsonStr, _ := json.Marshal(group)
		if Cache.Hander != nil {
			Cache.Hander.Set(key, jsonStr,
				time.Second*time.Duration(config.GetGlobal().User_Cache))
		}

	}

	return
}

//获取用户所有的项目
func GetUserProject(uid int) (projects []models.QyProject) {

	//获取用户信息
	var data string
	var err error

	Cache := models.CacheConnect()
	key := "qy_user_project_list#" + strconv.Itoa(uid)

	if Cache.Hander != nil {
		data, err = Cache.Hander.Get(key).Result()
		if err == nil {
			json.Unmarshal([]byte(data), &projects)
		}
	}
	if Cache.Hander == nil || err != nil {
		organizeIds := models.GetOrganizeIds(uid)
		//查询用户组私有项目id
		groupIds := models.GetGroupIds(uid)
		proids := models.GetGroupProject(groupIds)
		//查询用户的项目
		projects = models.GetUserProject(organizeIds, proids)
		data, _ := json.Marshal(projects)
		if Cache.Hander != nil {
			Cache.Hander.Set(key, data,
				time.Second*time.Duration(config.GetGlobal().User_Cache))
		}

	}
	return
}
