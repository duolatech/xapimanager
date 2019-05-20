package models

type QySite struct {
	Id    int `gorm:"primary_key"`
	Key   string
	Value string
	Type  int
	Des   string
}

/**
 * 获得站点信息
 * @result 站点信息
 */
func GetWebsite() (result map[string]string) {

	defer Db.Close()
	Db = Connect()
	var site []QySite
	result = make(map[string]string)
	Db.Hander.Find(&site)
	for _, v := range site {
		result[v.Key] = v.Value
	}

	return

}

/**
 * 保存站点信息
 * @result bool
 */
func WebsiteSave(key string, value string) bool {

	defer Db.Close()
	Db = Connect()
	if err := Db.Hander.Table("qy_site").Where("`key` =?", key).Update("value", value).Error; err != nil {
		return false
	}
	return true
}
