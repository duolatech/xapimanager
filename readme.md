项目介绍
========
### xApi Manager-哆啦接口管理平台
XAPI MANAGER -专业实用的开源接口管理平台，为程序开发者提供一个灵活，方便，快捷的API管理工具，让API管理变的更加清晰、明朗
### 特点
* 全站基于 Laravel 5.4 + Ace Admin + Bootstrap + jQuery +layer3.0开发
* 开放源代码，开发者可以根据自己的需求自由使用和定制
* 让开发者更加专注于项目业务和逻辑的实现，及协同开发
* 根据Api接口分类，灵活导出分类Api文档，便于开发人员阅读开发

项目部署
========
* 部署代码前，请确保php已开启curl，mbstring这两个扩展，建议在php7下部署
* 在MySQL中新建api数据库，并执行 /sql/xapi.sql文件。如果你是在命令行操作则可以像下面这样：
```sql
source /tmp/xapi.sql;
```
* 复制根目录下的.env.example重命名为.env文件，修改.env文件的数据库配置信息
```php
//数据库连接配置
DB_CONNECTION=mysql
DB_HOST=127.0.0.1          	//数据库地址
DB_PORT=3306				//端口号
DB_DATABASE=xapimanager		//数据库名
DB_PREFIX=mx_				//表前缀
DB_USERNAME=root			//帐号
DB_PASSWORD=123456			//密码
```
* 把项目部署到Apache或Nginx中即可

使用说明
========
1. 当前版本(v1.0)版本添加了用户权限控制，分类Api导出，代码审核，Api发表，多环境切换等。
2. 您下载源码后可以根据自己的需求进行二次开发和定制。
3. 已添加了一个超级管理员账号(用户名：admin，邮箱：admin@admin.com 密码：admin123)。
4. 详情操作及二次开发说明，请访问官方技术社区。

联系我们
==========
* 官方网站：	http://xapi.smaty.net
* 哆啦技术社区：http://www.smaty.net
* qq交流群:		623709829

最后
====
非常欢迎大家贡献代码，让这个项目成长的更好。
