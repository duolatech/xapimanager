package Services

import (
	"encoding/json"
	"time"
	"xapimanager/application/models"
	"xapimanager/config"
)

/**
 * 获得站点信息
 * @result 站点信息
 */
func GetWebsite() (site map[string]string) {

	var data string
	var err error
	site = map[string]string{}
	Cache := models.CacheConnect()
	key := "qy_website"
	if Cache.Hander != nil {
		data, err = Cache.Hander.Get(key).Result()
		if err == nil {
			json.Unmarshal([]byte(data), &site)
		}
	}
	if Cache.Hander == nil || err != nil {
		site = models.GetWebsite()
		jsonStr, _ := json.Marshal(site)
		if Cache.Hander != nil {
			Cache.Hander.Set(key, jsonStr,
				time.Second*time.Duration(config.GetGlobal().Sys_Cache))
		}

	}

	return
}

/**
 * 清除指定key的缓存
 */
func ClearCache(key string) {
	// 清除分类缓存
	Cache := models.CacheConnect()
	if Cache.Hander != nil {
		Cache.Hander.Del(key).Result()
	}
}
