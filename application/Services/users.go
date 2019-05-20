package Services

import (
	"strings"
	"xapimanager/application/common"
	"xapimanager/application/models"
)

type Sendlist struct {
	Name    string
	Address string
}

/**
 * 批量获取用户姓名
 * param userids = 1,2,3
 */
func GetBatchUserName(userids string) (data []string) {

	userIds := strings.Split(userids, ",")
	uids := []int{}
	for _, v := range userIds {
		uids = append(uids, common.StringToInt(v))
	}
	userInfo := models.BatchUsers(uids)

	for _, v := range userInfo {
		data = append(data, v.Username)
	}
	return
}

/**
 * 获取用户姓名
 * param uid 用户id
 */
func GetUserName(uid int) (username string) {

	info := models.GetUserInfo(uid)

	return info.Username
}
