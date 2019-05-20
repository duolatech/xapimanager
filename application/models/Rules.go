package models

import (
	"strconv"
	"strings"
	"time"
)

type QyAuthRule struct {
	Id       int `gorm:"primary_key"`
	Identify int
	Pid      int
	Path     string
	Title    string
	Icon     string
	Status   int
	Sort     int
	Isdel    int
}
type QyAuthOperate struct {
	Id       int
	Title    string
	Identify string
	Rid      int
	Path     string
	Status   int
}
type AuthData struct {
	Groupid int
	Type    int
	Record  string
	Ctime   int
}

//获取功能节点权限
func GetFeatureAuth() (auth []QyAuthOperate) {
	defer Db.Close()
	Db = Connect()
	Db.Hander.Find(&auth)
	return
}

//保存项目数据权限(根据权限组选择多个项目)
func ProjectDataSave(gid int, str []string) bool {
	defer Db.Close()
	Db = Connect()
	if len(str) > 0 {
		//插入时先删除历史数据
		Db.Hander.Table("qy_auth_data").Where("groupid = ? and type=1", gid).Delete(struct{}{})
		sql := "insert into qy_auth_data (groupid, type, record, ctime) VALUES" + strings.Join(str, ",")
		if err := Db.Hander.Exec(sql).Error; err != nil {
			return false
		}
		return true
	}
	return false
}

//保存项目数据权限(根据项目选择多个权限组)
func ProjectGroupSave(proid int, gids []string) bool {

	defer Db.Close()
	Db = Connect()
	var auth AuthData
	time := time.Now().Unix()

	//删除历史数据权限
	Db.Hander.Table("qy_auth_data").
		Where("type = ? and record = ?", 1, proid).Delete(struct{}{})
	//保存新的数据权限
	for _, id := range gids {
		id, _ := strconv.Atoi(id)
		auth = AuthData{
			id,
			1,
			strconv.Itoa(proid),
			int(time),
		}
		Db.Hander.Table("qy_auth_data").Create(&auth)
	}
	return true
}

//保存接口分类数据权限
func ClassifyDataSave(gid int, data AuthData) bool {
	defer Db.Close()
	Db = Connect()
	//插入时先删除历史数据
	Db.Hander.Table("qy_auth_data").Where("groupid =? and type=2", gid).Delete(struct{}{})
	err := Db.Hander.Table("qy_auth_data").Create(&data).Error
	if err != nil {
		return false
	}
	return true
}

//新增Api分类时保存用户组的分类数据权限
func NewClassifyDataSave(gid int, classify string) bool {

	defer Db.Close()
	Db = Connect()
	var count int
	var authData AuthData
	var record string
	obj := Db.Hander.Table("qy_auth_data").
		Where("groupid= ? and type = ?", gid, 2)
	obj.Count(&count)
	if count > 0 {
		obj.Find(&authData)
		if len(authData.Record) > 0 {
			record = authData.Record + "," + classify
		} else {
			record = classify
		}
		obj.Update("record", record)
	} else {
		time := time.Now().Unix()
		authData = AuthData{
			gid,
			2,
			classify,
			int(time),
		}

		err := Db.Hander.Table("qy_auth_data").Create(&authData).Error
		if err != nil {
			return false
		}
	}

	return true
}

//获取用户菜单权限
func GetUserMenuAuth(ids []string) (rules []QyAuthRule) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_auth_rule").
		Where("id in (?) and status=1 and isdel=2", ids).
		Find(&rules)
	return
}

//获取用户功能节点权限
func GetUserOperateAuth(ids []string) (operate []QyAuthOperate) {

	defer Db.Close()
	Db = Connect()
	obj := Db.Hander.Table("qy_auth_operate").
		Where("status=?", 1)
	if len(ids) > 0 {
		obj = obj.Where("id in (?)", ids).Find(&operate)
	} else {
		obj.Find(&operate)
	}

	return
}

//获取用户数据权限
func GetUserDataAuth(gid int, xtype []int) (auth []AuthData) {

	defer Db.Close()
	Db = Connect()
	//插入时先删除历史数据
	Db.Hander.Table("qy_auth_data").
		Where("groupid=? and type in (?)", gid, xtype).
		Find(&auth)
	return
}
