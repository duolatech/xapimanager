package models

import "time"

type QyMessage struct {
	Id       int
	Sender   int
	Receiver int
	Pid      int
	Subject  string
	Content  string
	Sendtime int
	Isread   int
}
type UserMessage struct {
	QyMessage
	Username string
}

//发送消息
func SendMessage(data map[string]interface{}) bool {

	defer Db.Close()
	Db = Connect()
	time := time.Now().Unix()
	info := &QyMessage{
		0,
		data["sender"].(int),
		data["recevier"].(int),
		data["pid"].(int),
		data["subject"].(string),
		data["content"].(string),
		int(time),
		2,
	}
	err := Db.Hander.Table("qy_message").Create(info).Error
	if err != nil {
		return false
	}
	return true
}

//获取消息列表
func GetMessageList(data map[string]interface{}, start int, limit int) (result map[string]interface{}) {

	defer Db.Close()
	Db = Connect()
	var count int
	var message []UserMessage
	obj := Db.Hander.Table("qy_message as m").
		Joins("join qy_user as u on u.uid = m.sender").
		Where("m.receiver = ?", data["receiver"])
	obj.Count(&count)
	obj.Select("m.*, u.username").Offset(start).Limit(limit).
		Order("m.sendtime desc").Find(&message)

	result = map[string]interface{}{
		"totalCount": count,
		"list":       message,
	}
	return
}

//消息详情
func GetMessageDetail(uid int, mid int) (message UserMessage) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_message as m").
		Joins("join qy_user as u on u.uid = m.sender").
		Where("m.receiver = ? and m.id=?", uid, mid).
		Select("m.*, u.username").
		Find(&message)

	return
}

//更新为已读
func ReadUpdate(receiver int, ids []string) bool {

	defer Db.Close()
	Db = Connect()
	err := Db.Hander.Table("qy_message").
		Where("receiver =? and id in (?)", receiver, ids).
		Update("isread", 1).Error
	if err != nil {
		return false
	}
	return true
}

//批量删除
func DeleteMessage(receiver int, ids []string) bool {

	defer Db.Close()
	Db = Connect()
	err := Db.Hander.Table("qy_message").
		Where("receiver =? and id in (?)", receiver, ids).
		Delete(struct{}{}).Error
	if err != nil {
		return false
	}
	return true
}

//获取未读消息
func GetUnreadMessage(receiver int) (count int) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_message").
		Where("receiver =? and isread = ?", receiver, 2).
		Count(&count)
	return
}
