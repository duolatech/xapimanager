package models

var Db *DB

type Allmenu struct {
	QyAuthRule
	Child []Allmenu
}

/**
 * 递归获取菜单栏
 * @param identify 标识(1主控制台,2接口平台)
 * @param pid      父级id
 * @result result    所有菜单
 */
func GetMenu(identify int, pid int) (menu []Allmenu) {

	defer Db.Close()
	Db = Connect()
	var authRule []QyAuthRule
	var temp Allmenu

	Db.Hander.Where("identify=? and pid = ? and status=1 and isdel=2", identify, pid).Order("sort asc").Find(&authRule)

	if len(authRule) > 0 {
		for _, value := range authRule {
			temp.QyAuthRule = value
			temp.Child = GetMenu(identify, value.Id)
			menu = append(menu, temp)
		}
	}

	return

}

/**
 * 递归获取api菜单栏
 * @param identify 标识(1主控制台,2接口平台)
 * @param pid      父级id
 * @param rules	   路由切片
 * @result result    所有菜单
 */
func GetManagerMenu(identify int, pid int, rules []string) (menu []Allmenu) {

	defer Db.Close()
	Db = Connect()
	var authRule []QyAuthRule
	var temp Allmenu

	obj := Db.Hander.Where("identify=? and pid = ? and status=1 and isdel=2", identify, pid)

	if pid > 0 {
		obj = obj.Where("id in (?)", rules)
	}
	obj.Order("sort asc").Find(&authRule)

	if len(authRule) > 0 {
		for _, value := range authRule {
			temp.QyAuthRule = value
			temp.Child = GetManagerMenu(identify, value.Id, rules)
			menu = append(menu, temp)
		}
	}

	return

}
