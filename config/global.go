package config

import (
	"encoding/json"
	"io/ioutil"
)

type Global struct {
	DEBUG          bool   `json:"debug"`
	SERVER_PORT    string `json:"server_port"`
	SERVER_WEBSITE string `json:"server_website"`

	MYSQL_IP       string `json:"mysql_ip"`
	MYSQL_PORT     string `json:"mysql_port"`
	MYSQL_USERNAME string `json:"mysql_username"`
	MYSQL_PASSWORD string `json:"mysql_password"`
	MYSQL_DBNAME   string `json:"mysql_dbname"`
	MYSQL_PREFIX   string `json:"mysql_prefix"`

	REDIS_IP       string `json:"redis_ip"`
	REDIS_PORT     string `json:"redis_port"`
	REDIS_PASSWORD string `json:"redis_password"`
	REDIS_DB       int    `json:"redis_db"`

	LOG_PATH      string `json:"log_path"`
	TEMPLATE_PATH string `json:"template_pate"`
	ASSETS_PATH   string `json:"assets_path"`
	UPLOAD_PATH   string `json:"upload_path"`

	DbLogMode         bool `json:"DbLogMode"`         //数据库日志模式，开启true, 关闭false
	DbMaxIdleConns    int  `json:"DbMaxIdleConns"`    //最大空闲连接数
	DbMaxOpenConns    int  `json:"DbMaxOpenConns"`    //最大连接数
	DbConnMaxLifetime int  `json:"DbConnMaxLifetime"` //mysql超时时间

	User_Cache int `json:"user_cache"` //用户缓存，单位s
	Sys_Cache  int `json:"sys_cache"`  //系统缓存，单位s

}

func GetGlobal() *Global {

	conf := "./config/config.json"
	data, _ := ioutil.ReadFile(conf)

	global := &Global{}
	json.Unmarshal(data, &global)

	return global
}
