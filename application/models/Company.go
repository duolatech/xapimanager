package models

import "time"

type QySecret struct {
	Id        int
	Proid     int
	Company   string
	Appid     string
	Appsecret string
	Status    int
	Ctime     int
}

/**
 * 获取企业秘钥列表
 * param  proid     项目id
 * param  company   公司名
 * param  start     开始位置
 * param  limit     条数
 * return result    秘钥列表
 */
func CompanyList(proid int, company string, start int, limit int) (result map[string]interface{}) {

	defer Db.Close()
	Db = Connect()
	var count int
	var secret []QySecret

	obj := Db.Hander.Table("qy_secret").Where("proid =? and status in (?)", proid, []int{1, 2})
	if len(company) > 0 {
		obj = obj.Where("company like ?", "%"+company+"%")
	}
	obj.Count(&count)
	obj.Offset(start).Limit(limit).Find(&secret)

	result = make(map[string]interface{})
	result["totalCount"] = count
	result["list"] = secret

	return
}

/**
 * 获取企业秘钥
 * param  id   密钥id
 */
func GetCompany(id int) (result QySecret) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_secret").Where("status in (?)", []int{1, 2}).
		Where("id = ?", id).Find(&result)
	return
}

/**
 * 保存企业秘钥
 * param  id   密钥id
 * param  data 密钥数据
 */
func CompanySave(id int, data map[string]interface{}) bool {

	defer Db.Close()
	Db = Connect()
	if id > 0 {
		err := Db.Hander.Table("qy_secret").
			Where("id = ? and proid=? ", id, data["proid"].(int)).
			Updates(data).Error
		if err != nil {
			return false
		}
	} else {
		time := time.Now().Unix()
		info := &QySecret{
			0,
			data["proid"].(int),
			data["company"].(string),
			data["appid"].(string),
			data["appsecret"].(string),
			data["status"].(int),
			int(time),
		}
		err := Db.Hander.Table("qy_secret").Create(info).Error
		if err != nil {
			return false
		}
	}
	return true
}

/**
 * 删除企业秘钥
 * param  id    密钥id
 * param  proid 项目id
 */
func CompanyOperate(id int, proid int) bool {
	defer Db.Close()
	Db = Connect()
	if id > 0 {
		err := Db.Hander.Table("qy_secret").
			Where("id = ? and proid = ?", id, proid).
			Update("status", 3).Error
		if err != nil {
			return false
		}
		return true
	}
	return false
}
