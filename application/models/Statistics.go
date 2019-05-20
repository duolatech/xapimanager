package models

type QyArea struct {
	Id        int
	AreaId    int
	AreaName  string
	Pid       int
	Longitude float64
	Latitude  float64
	Sort      int
}

func GetAreaData() (area []QyArea) {

	defer Db.Close()
	Db = Connect()
	Db.Hander.Table("qy_area").
		Where("pid !=? ", 0).
		Find(&area)

	return
}
