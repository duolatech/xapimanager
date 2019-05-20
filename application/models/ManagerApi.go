package models

import (
	"time"
)

type QyApiDetail struct {
	Id             int
	Listid         int
	Proid          int
	Envid          int
	Apiname        string
	Subclassify    int
	Version        int
	Uri            string
	Gateway        string
	Local          string
	Network        int
	Authentication int
	Description    string
	Author         int
	Editor         string
	Method         int
	Requesttype    int
	Responsetype   int
	Header         string
	Request        string
	Response       string
	Statuscode     string
	Successgoback  string
	Failgoback     string
	Status         int
	Mtime          int
	Ctime          int
}
type Apilistinfo struct {
	Id       int
	Listid   int
	Apiname  string
	Version  string
	Uri      string
	Username string
	Mtime    int
	Status   int
	Method   int
}
type QyAudit struct {
	Id      int
	Auditor int
	Did     int
	Status  int
	Isdel   int
	Remark  string
	Ctime   int
}

var api_status = map[int]string{
	1: "已审核",
	2: "待审核",
	3: "已废弃",
	4: "已删除",
	5: "已拒绝",
}
var method = map[int]string{
	1: "GET",
	2: "POST",
	3: "PUT",
	4: "DELETE",
}

/**
 * 获取api列表
 * param  con     查询条件
 * param  start   查询开始位置
 * param  limit   条数
 * param  status  api状态
 * param  auth    分类权限限制
 * return result  api列表
 */
func GetApilist(con map[string]interface{}, start int, limit int, status []string, auth []string) (result map[string]interface{}) {

	defer Db.Close()
	Db = Connect()
	var totalCount int
	var apilist []Apilistinfo
	var listDetail = map[int][]map[string]interface{}{}
	var listInfo = map[int]map[string]interface{}{}
	var temp []map[string]interface{}

	obj := Db.Hander.Table("qy_apidetail as d").
		Joins("left join qy_user as u on u.uid = d.author").
		Where("d.proid = ? and d.envid= ?  and d.status in (?)",
			con["proid"], con["envid"], status)

	//分类数据权限
	if len(auth) > 0 {
		obj = obj.Where("subclassify in (?)", auth)
	}
	//api 名称查询
	if con["apiname"] != nil && len(con["apiname"].(string)) > 0 {
		obj = obj.Where("d.apiname like ?", "%"+con["apiname"].(string)+"%")
	}
	// 分类查询
	if con["subClassify"] != nil && con["subClassify"].(int) > 0 {
		obj = obj.Where("d.subclassify = ?", con["subClassify"].(int))
	} else if con["classify"] != nil && con["classify"].(int) > 0 {
		subIds := GetSubClassifyIds(con["proid"].(int), con["classify"].(int))
		obj = obj.Where("d.subclassify in (?)", subIds)
	}
	//URI
	if con["URI"] != nil && len(con["URI"].(string)) > 0 {
		obj = obj.Where("d.uri = ?", con["URI"].(string))
	}
	//开发人
	if con["author"] != nil && len(con["author"].(string)) > 0 {
		obj = obj.Where("u.username  like ?", "%"+con["author"].(string)+"%")
	}

	obj.Count(&totalCount)

	obj.Select("d.id as id, d.listid, d.apiname, d.version, d.uri, u.username, d.mtime, d.status, d.method").
		Order("d.mtime desc").
		Offset(start).Limit(limit).Find(&apilist)

	//数据处理
	for _, v := range apilist {

		listDetail[v.Listid] = append(listDetail[v.Listid],
			map[string]interface{}{
				"id":        v.Id,
				"listid":    v.Listid,
				"uri":       v.Uri,
				"version":   v.Version,
				"author":    v.Username,
				"status":    v.Status,
				"apistatus": api_status[v.Status],
				"mtime":     time.Unix(int64(v.Mtime), 0).Format("2006-01-02"),
				"method":    method[v.Method],
			})
		listInfo[v.Listid] = map[string]interface{}{
			"listid":  v.Listid,
			"apiname": v.Apiname,
		}
	}

	//数据聚合
	result = make(map[string]interface{})
	temp = []map[string]interface{}{}
	result["totalCount"] = totalCount
	for k, v := range listDetail {
		temp = append(
			temp,
			map[string]interface{}{
				"listid":  listInfo[k]["listid"],
				"apiname": listInfo[k]["apiname"],
				"info":    v,
			})
	}
	result["list"] = temp

	return
}

//Api 审核
func ApiAuditOpearate(did int, data map[string]interface{}) bool {

	defer Db.Close()
	Db = Connect()
	var count int
	Db.Hander.Table("qy_audit").
		Where("did = ?", did).
		Count(&count)

	if count > 0 {
		err := Db.Hander.Table("qy_audit").
			Where("did = ?", did).
			Updates(data).Error
		if err != nil {
			return false
		}
	} else {
		time := time.Now().Unix()
		info := &QyAudit{
			0,
			data["auditor"].(int),
			did,
			data["status"].(int),
			data["isdel"].(int),
			data["remark"].(string),
			int(time),
		}
		err := Db.Hander.Table("qy_audit").Create(info).Error
		if err != nil {
			return false
		}
	}

	return true
}

// 更新Api详情 状态
func UpdateAuditStatus(id int, status int) bool {

	defer Db.Close()
	Db = Connect()
	if status == 1 {
		status = 1
	} else {
		status = 5
	}
	err := Db.Hander.Table("qy_apidetail").
		Where("id = ?", id).
		Update("status", status).Error
	if err != nil {
		return false
	}
	return true
}

// 获取Api信息
func GetApiInfo(did int) (data QyApiDetail) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_apidetail").
		Where("id = ?", did).
		Find(&data)

	return
}

//多条件下获取Api详情
func GetApiDetail(data map[string]interface{}) (detail QyApiDetail) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_apidetail").
		Where(data).
		Find(&detail)

	return
}

//获取项目下Apilist 最大值
func GetMaxApilist(proid int) (count int) {

	defer Db.Close()
	Db = Connect()
	type ListApi struct {
		Listid int
	}
	var listApi ListApi
	Db.Hander.Table("qy_apidetail").
		Where("proid = ?", proid).
		Select("max(listid) as listid").Find(&listApi)

	return listApi.Listid
}

// 获取Api审核信息
func GetApiAuditInfo(did int) (data QyAudit) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_audit").
		Where("did = ?", did).
		Find(&data)

	return
}

/*
 *保存api信息
 *@param id 详情id
 *@param proid 项目id
 *@param envid 环境id
 *@param data  数据
 *@param bool
 */
func ApiDetailStore(id int, proid int, envid int, data map[string]interface{}) bool {

	defer Db.Close()
	Db = Connect()
	if id > 0 {
		err := Db.Hander.Table("qy_apidetail").
			Where("id = ? and proid = ?", id, proid).
			Updates(data).Error
		if err != nil {
			return false
		}
	} else {
		time := time.Now().Unix()
		info := &QyApiDetail{
			0,
			data["listid"].(int),
			proid,
			envid,
			data["apiname"].(string),
			data["subclassify"].(int),
			data["version"].(int),
			data["uri"].(string),
			data["gateway"].(string),
			data["local"].(string),
			data["network"].(int),
			data["authentication"].(int),
			data["description"].(string),
			data["author"].(int),
			data["editor"].(string),
			data["method"].(int),
			data["requesttype"].(int),
			data["responsetype"].(int),
			data["header"].(string),
			data["request"].(string),
			data["response"].(string),
			data["statuscode"].(string),
			data["successgoback"].(string),
			data["failgoback"].(string),
			data["status"].(int),
			data["mtime"].(int),
			int(time),
		}
		err := Db.Hander.Table("qy_apidetail").Create(info).Error
		if err != nil {
			return false
		}
	}

	return true
}

/*
 *更新api信息
 *@param did 详情id
 *@param proid 项目id
 *@param bool
 */
func UpdateApiInfo(did int, proid int, data map[string]interface{}) bool {
	defer Db.Close()
	Db = Connect()
	err := Db.Hander.Table("qy_apidetail").
		Where("id = ? and proid = ?", did, proid).
		Updates(data).Error
	if err != nil {
		return false
	}
	return true
}
