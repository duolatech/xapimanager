package Services

import (
	"reflect"
	"strings"
	"time"
	"xapimanager/application/common"
	"xapimanager/application/models"
)

func SyncApiDetail(did int, proid int, envid int) bool {

	var id int
	//同步信息检查
	detail := models.GetApiInfo(did)
	con := map[string]interface{}{
		"listid":  detail.Listid,
		"proid":   proid,
		"envid":   envid,
		"version": detail.Version,
	}
	apidetail := models.GetApiDetail(con)
	//获取api信息
	time := time.Now().Unix()
	var data = map[string]interface{}{
		"envid":  envid,
		"ctime":  int(time),
		"mtime":  int(time),
		"status": 1,
	}
	if detail.Id > 0 {
		t := reflect.TypeOf(detail)
		v := reflect.ValueOf(detail)
		for k := 0; k < t.NumField(); k++ {
			key := strings.ToLower(t.Field(k).Name)
			if !common.CheckAuth(key, []string{"id", "envid", "mtime", "ctime", "status"}) {
				data[strings.ToLower(t.Field(k).Name)] = v.Field(k).Interface()
			}
		}
	}
	if apidetail.Id > 0 {
		id = apidetail.Id
	} else {
		id = 0
	}
	result := models.ApiDetailStore(id, proid, envid, data)

	return result
}
