package models

import (
	"fmt"
	"github.com/jinzhu/gorm"
	_ "github.com/jinzhu/gorm/dialects/mysql"
	"time"
	"xapimanager/application/utils"
	"xapimanager/config"
)

type DB struct {
	Hander *gorm.DB
	cfg
}
type cfg struct {
	host   string
	port   string
	user   string
	pass   string
	dbname string
	prefix string
}

//连接mysql
func Connect() (Dbase *DB) {
	//创建连接
	Dbase = Singleton()
	if err := Dbase.Open(); err != nil {
		//日志记录示例
		data := map[string]interface{}{
			"filename": "database",
			"size":     10,
		}
		utils.Log.WithFields(data).Info(err)
	}
	return
}

//创建单例模式
func Singleton() *DB {

	sysc := config.GetGlobal()
	var dbconfig = cfg{
		sysc.MYSQL_IP,
		sysc.MYSQL_PORT,
		sysc.MYSQL_USERNAME,
		sysc.MYSQL_PASSWORD,
		sysc.MYSQL_DBNAME,
		sysc.MYSQL_PREFIX,
	}
	return &DB{
		cfg: dbconfig,
	}
}

//mysql 连接
func (db *DB) Open() error {

	sysc := config.GetGlobal()
	connect := fmt.Sprintf("%s:%s@(%s:%s)/%s?charset=utf8&parseTime=True&loc=Local",
		db.user, db.pass, db.host, db.port, db.dbname)
	obj, err := gorm.Open("mysql", connect)
	if err != nil {
		return err
	}
	//转换名称时不加s
	obj.SingularTable(true)
	//打印详细日志 //TODO 调试完成后需删除
	obj.LogMode(sysc.DbLogMode)
	//设置连接池
	obj.DB().SetConnMaxLifetime(time.Second * time.Duration(sysc.DbConnMaxLifetime))
	obj.DB().SetMaxIdleConns(sysc.DbMaxIdleConns)
	obj.DB().SetMaxOpenConns(sysc.DbMaxOpenConns)
	db.Hander = obj
	return nil
}

//mysql 关闭连接
func (db *DB) Close() {
	if db != nil && db.Hander != nil {
		db.Hander.Close()
	}
}
