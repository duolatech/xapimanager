package models

type AuthGroup struct {
	Id          int `gorm:"primary_key"`
	Organize    int
	Groupname   string
	Description string
	Status      int
	Rules       string
	Operate     string
}
type QyAuthAccess struct {
	Id      int `gorm:"primary_key"`
	Uid     int
	GroupId int
}

//获取用户信息
func GetGroupInfo(gid int) (result AuthGroup) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_auth_group").Where("id = ?", gid).Find(&result)

	return
}

//获取用户权限组id
func GetUserGroup(uid int) (gid int) {

	defer Db.Close()
	Db = Connect()
	var authAccess QyAuthAccess
	Db.Hander.Table("qy_auth_access").
		Where("uid = ?", uid).Find(&authAccess)

	gid = authAccess.GroupId

	return

}

//权限组保存
func GroupSave(gid int, data map[string]interface{}) (result int) {

	defer Db.Close()
	Db = Connect()
	if gid > 0 {
		err := Db.Hander.Table("qy_auth_group").Where("id = ?", gid).Updates(data).Error
		if err != nil {
			return 0
		} else {
			return gid
		}
	} else {
		info := &AuthGroup{
			0,
			data["organize"].(int),
			data["groupname"].(string),
			data["description"].(string),
			data["status"].(int),
			data["rules"].(string),
			data["operate"].(string),
		}
		obj := Db.Hander.Table("qy_auth_group").Create(info).Value

		insertId := obj.(*AuthGroup).Id

		return insertId
	}

}

//更新权限组
func GroupFeatureUpdate(gid int, data map[string]interface{}) (result bool) {
	defer Db.Close()
	Db = Connect()
	if gid > 0 {
		err := Db.Hander.Table("qy_auth_group").Where("id = ?", gid).Updates(data).Error
		if err != nil {
			return false
		}
		return true
	}
	return false
}

//获取组织下的权限组
func GetOrganizeGroup(oid int) (group []AuthGroup) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_auth_group").
		Where("organize=?", oid).
		Find(&group)

	return
}

//获取组织下的权限组数量
func GetOrganizeGroupCount(oid int) (count int) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_auth_group").
		Where("organize=?", oid).
		Count(&count)

	return
}

//权限组操作
func GroupOperate(otype int, gid int) (result bool) {

	defer Db.Close()
	Db = Connect()
	switch {
	case otype == 1:
		Db.Hander.Table("qy_auth_group").
			Where("id=?", gid).
			Delete(struct{}{})
		break
	}
	return true
}

//查询权限组下是否有用户
func GetGroupUserNum(gid int) (count int) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_auth_access").
		Where("group_id=?", gid).
		Count(&count)
	return
}

//查询权限组项目及分类权限
func GetGroupDataAuth(gid int) (data []QyAuthData) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_auth_data").
		Where("groupid=? and type in (1,2)", gid).
		Find(&data)
	return
}

//获取用户在指定项目的权限组
func GetProjectGroup(uid int, proid int) (group AuthGroup) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_auth_group as g").
		Joins("join qy_project as p on g.organize = p.organize").
		Joins("join qy_auth_access as a on a.group_id = g.id").
		Where("a.uid = ? and p.id =?", uid, proid).
		Select("g.id, g.organize, g.groupname, g.description, g.status, g.rules, g.operate").
		Find(&group)
	return
}
