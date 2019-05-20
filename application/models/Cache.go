package models

import (
	"github.com/go-redis/redis"
	"xapimanager/application/utils"
	"xapimanager/config"
)

type Cache struct {
	Hander *redis.Client
	Scheme int
	rediscfg
}
type rediscfg struct {
	host string
	port string
	pass string
	db   int
}

//连接
func CacheConnect() (Cc *Cache) {

	Cc = CacheSingleton()
	Cc.Open()
	return
}

//创建单例模式
func CacheSingleton() *Cache {

	sysc := config.GetGlobal()
	return &Cache{
		rediscfg: rediscfg{
			sysc.REDIS_IP,
			sysc.REDIS_PORT,
			sysc.REDIS_PASSWORD,
			sysc.REDIS_DB,
		},
	}
}

//打开
func (Cc *Cache) Open() error {

	var err error
	client := redis.NewClient(&redis.Options{
		Addr:     Cc.host + ":" + Cc.port,
		Password: Cc.pass,
		DB:       Cc.db,
	})
	//判断redis连接是否可用
	if _, err := client.Ping().Result(); err != nil {
		//日志记录示例
		data := map[string]interface{}{
			"filename": "redisconnet",
			"size":     10,
		}
		utils.Log.WithFields(data).Info(err)
	} else {
		Cc.Hander = client
	}

	return err

}

//关闭
func (Cc *Cache) Close() {

	Cc.Hander.Close()

}
