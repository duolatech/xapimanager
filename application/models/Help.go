package models

import (
	"time"
)

type QyHelp struct {
	Id       int
	Organize int
	Author   int
	Title    string
	Content  string
	Status   int
	Ctime    int
}
type UserHelp struct {
	QyHelp
	Username string
}

//获取帮助中心列表
func GetHelpList(data map[string]interface{}, start int, limit int) (result map[string]interface{}) {

	defer Db.Close()
	Db = Connect()
	var count int
	var help []UserHelp
	obj := Db.Hander.Table("qy_help as h").
		Joins("join qy_user as u on u.uid = h.author").
		Where("h.organize = ? and h.status = ?", data["organize"], 1)
	obj.Count(&count)
	obj.Select("h.*, u.username").Offset(start).Limit(limit).
		Order("h.ctime desc").Find(&help)

	result = map[string]interface{}{
		"totalCount": count,
		"list":       help,
	}
	return
}

//帮助详情
func GetHelpDetail(organize int, hid int) (help UserHelp) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_help as h").
		Joins("join qy_user as u on u.uid = h.author").
		Where("h.organize = ? and h.id = ? and h.status = ?", organize, hid, 1).
		Select("h.*, u.username").
		Find(&help)

	return
}

//删除帮助中心
func DeleteHelp(organize int, hid int) bool {
	defer Db.Close()
	Db = Connect()
	err := Db.Hander.Table("qy_help").
		Where("organize = ? and id= ?", organize, hid).
		Update("status", 2).Error
	if err != nil {
		return false
	}
	return true

}

//帮助中心保存
func HelpStore(organize int, hid int, data map[string]interface{}) bool {
	defer Db.Close()
	Db = Connect()
	if hid > 0 {
		delete(data, "author")
		err := Db.Hander.Table("qy_help").
			Where("organize = ? and id=? ", organize, hid).
			Updates(data).Error
		if err != nil {
			return false
		}
		return true
	} else {
		time := time.Now().Unix()
		info := &QyHelp{
			0,
			organize,
			data["author"].(int),
			data["title"].(string),
			data["content"].(string),
			1,
			int(time),
		}
		if err := Db.Hander.Table("qy_help").Create(info).Error; err != nil {
			return false
		}
		return true
	}
}
