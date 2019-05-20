package models

import (
	"xapimanager/application/common"
	"github.com/pkg/errors"
	"strconv"
	"time"
)

type QyOrganize struct {
	Id       int `gorm:"primary_key"`
	Leader   int
	Name     string
	Identify string
	Icon     string
	Desc     string
	Ctime    int
}
type QyUserOrganize struct {
	Uid      int
	Organize int
	Status   int
	Ctime    int
}
type PermissionGroup struct {
	Groupid     int
	Organize    int
	Groupname   string
	Description string
	Status      int
	Operate     string
}

//根据标识获取组织信息
func GetOrganizeInfo(identify string) (organize []QyOrganize) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Where("identify = ?", identify).Find(&organize)

	return

}

//获取单个组织信息
func GetOrganizeOne(identify string) (organize QyOrganize) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Where("identify = ?", identify).Find(&organize)

	return

}

//根据id获取组织信息
func GetOrganizeDetail(id int) (organize []QyOrganize) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Where("id = ?", id).Find(&organize)

	return

}

//获取用户自己的组织
func GetUserOrganize(uid int) (organize QyOrganize) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Where("leader = ?", uid).Find(&organize)

	return
}

//获取用户自己组织的权限组
func GetUserPermissionGroup() (group []PermissionGroup) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_auth_group as g").
		Joins("join qy_organize as o on g.organize = o.id").
		Select("g.id as groupid, g.organize, g.groupname, g.description, g.status, g.rules, g.operate").
		Where("g.status=?", 1).
		Find(&group)

	return
}

//为用户自己创建团队
func OrganizeCreate(uid int, data map[string]interface{}) (orgId int) {

	defer Db.Close()
	Db = Connect()
	time := time.Now().Unix()
	org := QyOrganize{
		0,
		uid,
		"梦之队" + strconv.Itoa(uid),
		common.MD5(strconv.Itoa(uid) + string(time)),
		"",
		"我就是我，不一样的烟火！",
		int(time),
	}
	organize := Db.Hander.Create(&org).Value
	return organize.(*QyOrganize).Id

}

//加入团队
func OrganizeJoin(uid int, organize int, status int) (err error) {

	defer Db.Close()
	Db = Connect()
	var num int
	//查询用户是否在该组织
	Db.Hander.Table("qy_user_organize").
		Where(map[string]interface{}{"uid": uid, "organize": organize}).
		Count(&num)
	if num > 0 {
		return errors.New("主人，您已在该团队,无需重复加入")
	}
	time := time.Now().Unix()
	org := QyUserOrganize{
		Uid:      uid,
		Organize: organize,
		Status:   status, //1已激活、2待激活
		Ctime:    int(time),
	}
	if err := Db.Hander.Create(&org).Error; err != nil {
		return err
	}
	return
}

//退出组织
func OrganizeQuit(uid int, organize int) (err error) {

	defer Db.Close()
	Db = Connect()
	err = Db.Hander.Where("uid = ? and organize =?", uid, organize).Delete(QyUserOrganize{}).Error
	if err != nil {
		return err
	}
	return
}

//修改组织信息
func OrganizeSave(organizeId int, data map[string]interface{}) (err error) {

	defer Db.Close()
	Db = Connect()
	var Qyorg QyOrganize
	err = Db.Hander.Model(&Qyorg).Where("id=?", organizeId).Updates(data).Error

	if err != nil {
		return err
	}
	return

}

//修改用户组织
func UpdateUserOrganize(uid int, data map[string]interface{}) bool {

	defer Db.Close()
	Db = Connect()
	err := Db.Hander.Table("qy_user_organize").Where("uid = ?", uid).Updates(data).Error
	if err != nil {
		return false
	}
	return true
}

//获取组织下的用户信息
//获取用户信息
func GetOrganizeUserInfo(oid int, uid int) (result UserInfo) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_user as u").
		Joins("left join qy_user_organize as o on u.uid=o.uid").
		Select("u.uid,u.username,u.email,u.phone,u.intro,o.status").
		Where("u.uid = ? and o.organize=?", uid, oid).Find(&result)

	return
}
