package models

import (
	"strconv"
	"time"
)

type QyClassify struct {
	Id           int `gorm:"primary_key"`
	Proid        int
	Classifyname string
	Pid          int
	Description  string
	Addtime      int
	Creator      int
	Leader       string
	Status       int
}
type XClassify struct {
	Id           int
	Proid        int
	Classifyname string
	Addtime      int
	Status       int
}
type AllClassify struct {
	XClassify
	Child []AllClassify
}

/**
 * 递归获取分类
 * @param proid    项目id
 * @param pid      父级id
 * @result result    所有分类
 */
func GetClassify(proid int, pid int, scope []string) (classifys []AllClassify) {

	defer Db.Close()
	Db = Connect()
	var xClassify []XClassify
	var temp AllClassify

	obj := Db.Hander.Table("qy_classify").
		Where("proid=? and pid = ? and status=1", proid, pid)
	if len(scope) > 0 {
		obj = obj.Where("id in (?)", scope)
	}
	obj.Find(&xClassify)

	if len(xClassify) > 0 {
		for _, value := range xClassify {
			temp.XClassify = value
			temp.Child = GetClassify(proid, value.Id, scope)
			classifys = append(classifys, temp)
		}
	}

	return

}

/**
 * 获取指定分类的子分类信息
 * @param  proid 项目id
 * @param  classify 父级分类id
 * @result subclassify    子分类信息
 */
func GetSubClassify(proid int, classify int) (subclassify []QyClassify) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_classify").
		Where("proid=? and pid = ? and status=1", proid, classify).
		Find(&subclassify)
	return
}

/**
 * 获取指定分类的子分类ids
 * @param  proid 项目id
 * @param  classify 父级分类id
 * @result subclassify    子分类信息
 */
func GetSubClassifyIds(proid int, classify int) (result []int) {

	defer Db.Close()
	Db = Connect()
	var subclassify []QyClassify
	Db.Hander.Table("qy_classify").
		Where("proid=? and pid = ? and status=1", proid, classify).
		Find(&subclassify)
	for _, v := range subclassify {
		result = append(result, v.Id)
	}

	return
}

//获取分类信息
func GetClassifyInfo(proid int, classify int) (result QyClassify) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_classify").
		Where("proid=? and id = ? and status=1", proid, classify).
		Find(&result)
	return
}

/**
 * 保存分类
 * @param  proid  项目id
 * @param  cateId 分类id
 * @param  uid    用户id
 * @param  pid    当前分类的父级id
 * @param  data   数据
 * @param  bool,int
 */
func CategorySave(proid int, cateId int, uid int, pid int, data map[string]interface{}) (bool, int) {

	defer Db.Close()
	Db = Connect()
	if cateId > 0 {
		err := Db.Hander.Table("qy_classify").
			Where("id = ? and proid=? ", cateId, data["proid"].(int)).
			Updates(data).Error
		if err != nil {
			return false, 0
		}
		return true, 0
	} else {
		time := time.Now().Unix()
		info := &QyClassify{
			0,
			proid,
			data["classifyname"].(string),
			pid,
			data["description"].(string),
			int(time),
			uid,
			strconv.Itoa(uid),
			1,
		}
		obj := Db.Hander.Table("qy_classify").Create(info).Value

		cateId = obj.(*QyClassify).Id

		return true, cateId

	}

}

//更新分类数据
func UpdateClassifyInfo(proid int, cateId int, data map[string]interface{}) bool {

	defer Db.Close()
	Db = Connect()
	err := Db.Hander.Table("qy_classify").
		Where("id = ? and proid=? ", cateId, proid).
		Updates(data).Error
	if err != nil {
		return false
	}
	return true
}
