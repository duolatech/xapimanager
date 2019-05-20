package models

import (
	"time"
	"xapimanager/application/common"
)

type QyAuthData struct {
	Id      int `gorm:"primary_key"`
	Groupid int
	Type    int
	Record  string
	Ctime   int
}
type QyProject struct {
	Id        int    `gorm:"primary_key" json:"id"`
	Organize  int    `json:"organize"`
	Proname   string `json:"proname"`
	Desc      string `json:"desc"`
	Attribute int    `json:"attribute"`
	Status    int    `json:"status"`
	Ctime     int    `json:"ctime"`
}
type ProjectGroup struct {
	Id        int
	Organize  int
	Groupname string
	Status    int
}
type QyApienv struct {
	Id      int
	Proid   int
	Envname string
	Domain  string
	Sort    int
	Status  int
}
type QyUserEnv struct {
	Id    int
	Uid   int
	Proid int
	Envid int
}

/**
 *获取用户组下的项目
 *param groupIds 用户组id
 *return 项目id
 */
func GetGroupProject(groupIds []int) (result []string) {

	defer Db.Close()
	Db = Connect()
	var authData []QyAuthData
	Db.Hander.Where("groupid in (?) and type=?", groupIds, 1).Find(&authData)
	for _, v := range authData {

		result = append(result, v.Record)
	}

	return
}

/**
 *获取用户的项目，组织下共有项目和私有项目
 *param organizeIds 用户组织id
 *param proids	 用户组下的项目
 *return 项目信息
 */
func GetUserProject(organizeIds []int, proids []string) (projects []QyProject) {

	defer Db.Close()
	Db = Connect()
	var data []QyProject
	Db.Hander.
		Where("organize in (?) and attribute = ? and status = ?", organizeIds, 1, 1).
		Or("id in (?)", proids).Find(&data)

	for _, v := range data {
		temp := QyProject{
			v.Id,
			v.Organize,
			v.Proname,
			common.SubString(v.Desc, 0, 20, true),
			v.Attribute,
			v.Status,
			v.Ctime,
		}
		projects = append(projects, temp)
	}
	return

}

//获取组织下的所有项目
func GerOrganizeProject(oid int) (projects []QyProject) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.
		Where("organize=? and status = ?", oid, 1).Find(&projects)

	return
}

//保存项目
func ProjectSave(proid int, data map[string]interface{}) (bool, int) {

	defer Db.Close()
	Db = Connect()
	var project QyProject
	if proid > 0 {
		if err := Db.Hander.Table("qy_project").Where("id =?", proid).Updates(data).Error; err != nil {
			return false, 0
		}
		return true, 0
	} else {
		time := time.Now().Unix()
		project = QyProject{
			0,
			data["organize"].(int),
			data["proname"].(string),
			data["desc"].(string),
			data["attribute"].(int),
			1,
			int(time),
		}
		obj := Db.Hander.Create(&project).Value

		insertId := obj.(*QyProject).Id

		return true, insertId
	}
}

//查询项目信息
func GetProjectInfo(proid int) (result QyProject) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Where("id = ?", proid).Find(&result)
	return
}

//查询项目下的权限组
func GetProjectGroupAuth(proid int) (group []ProjectGroup) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_auth_data as a").
		Joins("join qy_auth_group as g on g.id = a.groupid").
		Select("g.id, g.organize,g.groupname,g.status").
		Where("a.record=? and status=?", proid, 1).
		Find(&group)
	return
}

//获取项目下的所有环境
func GetProjectEnv(proid int) (env []QyApienv) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Where("proid = ?", proid).Order("sort asc").Find(&env)

	return
}

/*
 *获取项目下的启动的环境
 *param proid 项目id
 *param sort  排序 asc 升序，desc 降序
 */
func GetProjectValidEnv(proid int, sort string) (env []QyApienv) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Where("proid = ? and status = 1", proid).Order("sort " + sort).Find(&env)

	return
}

//获取项目下的最低等级环境
func GetProjectLowEnv(proid int) (env QyApienv) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Where("proid = ? and status = 1", proid).
		Order("sort desc").Limit(1).Find(&env)

	return
}

//保存项目环境
func ProjectEnvSave(envid int, data map[string]interface{}) bool {

	defer Db.Close()
	Db = Connect()
	if envid > 0 {
		//修改环境
		if err := Db.Hander.Table("qy_apienv").Where("id = ?", envid).Updates(data).Error; err != nil {
			return false
		}
		return true
	} else {
		//添加环境
		env := QyApienv{
			0,
			data["proid"].(int),
			data["envname"].(string),
			data["domain"].(string),
			data["sort"].(int),
			data["status"].(int),
		}
		if err := Db.Hander.Create(&env).Error; err != nil {
			return false
		}
		return true
	}
}

//环境切换
func ProjectEnvChange(uid int, proid int, envid int) bool {

	defer Db.Close()
	Db = Connect()
	var count int
	Db.Hander.Table("qy_user_env").
		Where("uid = ? and proid =? ", uid, proid).
		Count(&count)
	if count > 0 {
		if err := Db.Hander.Table("qy_user_env").
			Where("uid = ? and proid =? ", uid, proid).
			Update("envid", envid).Error; err != nil {
			return false
		} else {
			return true
		}
	} else {
		var userEnv = QyUserEnv{
			0,
			uid,
			proid,
			envid,
		}
		if err := Db.Hander.Table("qy_user_env").Create(&userEnv).Error; err != nil {
			return false
		}
		return true
	}
}

//获取用户当前环境
func GetCurrentEnv(uid int, proid int) (data QyUserEnv) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_user_env").
		Where("uid = ? and proid =? ", uid, proid).
		Find(&data)
	return
}

//获取当前环境信息
func GetUserDomain(uid int, proid int) (info QyApienv) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_user_env as u").
		Joins("join qy_apienv as a on u.envid = a.id").
		Where("u.uid = ? and u.proid =? ", uid, proid).
		Select("a.id, a.proid, a.envname, a.domain, a.sort, a.status").
		Find(&info)
	return
}
