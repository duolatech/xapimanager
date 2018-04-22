/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*数据权限*/
DROP TABLE IF EXISTS `mx_auth_data`;
CREATE TABLE `mx_auth_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` int(11) DEFAULT NULL COMMENT '组id',
  `type` tinyint(2) DEFAULT NULL COMMENT '类型(1项目选择,2分类选择,3用户选择,4企业密钥)',
  `record` text COMMENT '类型对应的记录',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*调试表，v1未用到*/
DROP TABLE IF EXISTS `mx_debug`;
CREATE TABLE `mx_debug` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` int(11) NOT NULL COMMENT '用户uid',
  `type` tinyint(1) DEFAULT '1' COMMENT '请求方式(1GET,2POST,3PUT,4DELETE)',
  `apiurl` varchar(200) DEFAULT NULL COMMENT '接口url',
  `param` varchar(500) DEFAULT NULL COMMENT '接口参数',
  `header` varchar(500) DEFAULT NULL COMMENT 'header头',
  `addtime` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

/*域名绑定表*/
DROP TABLE IF EXISTS `mx_domain`;
CREATE TABLE `mx_domain` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '用户id',
  `domain` varchar(50) DEFAULT NULL COMMENT '域名',
  `iplong` varchar(300) DEFAULT NULL COMMENT 'ip地址',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*日志表*/
DROP TABLE IF EXISTS `mx_log`;

CREATE TABLE `mx_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project` int(11) DEFAULT NULL COMMENT '项目id',
  `envid` tinyint(1) DEFAULT NULL COMMENT '接口环境id',
  `operator` int(11) DEFAULT NULL COMMENT '操作人用户id',
  `desc` varchar(500) DEFAULT NULL COMMENT '操作描述',
  `addtime` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*项目表*/
DROP TABLE IF EXISTS `mx_project`;

CREATE TABLE `mx_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proname` varchar(50) DEFAULT NULL COMMENT '项目名称',
  `desc` text COMMENT '项目描述',
  `attribute` tinyint(1) DEFAULT NULL COMMENT '项目属性(1公有,2私有)',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态(1正常,2弃用)',
  `ctime` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

insert  into `mx_project`(`id`,`proname`,`desc`,`attribute`,`status`,`ctime`) values (1,'默认项目','系统默认项目',1,1,1504970909);

/*项目切换表*/
DROP TABLE IF EXISTS `mx_project_toggle`;

CREATE TABLE `mx_project_toggle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '用户id',
  `proid` int(11) DEFAULT NULL COMMENT '当前激活的项目id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

insert  into `mx_project_toggle`(`id`,`uid`,`proid`) values (1,1,1);

DROP TABLE IF EXISTS `mx_secret`;
/*公司密钥*/
CREATE TABLE `mx_secret` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proid` int(11) DEFAULT NULL COMMENT '项目id',
  `company` varchar(100) DEFAULT NULL COMMENT '公司名称',
  `appId` varchar(100) DEFAULT NULL COMMENT '应用id',
  `appSecret` varchar(100) NOT NULL COMMENT '应用密钥',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态(1正常,2冻结,3删除)',
  `ctime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `apiSecret` (`appSecret`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `mx_help`;

CREATE TABLE `mx_help` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `author` int(11) DEFAULT NULL COMMENT '发布人',
  `title` varchar(100) NOT NULL COMMENT '标题',
  `content` text COMMENT '内容',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态(1正常,2删除)',
  `ctime` int(11) DEFAULT NULL COMMENT '发布时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `mx_message`;

CREATE TABLE `mx_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender` int(11) DEFAULT NULL COMMENT '发件人uid',
  `receiver` int(11) DEFAULT NULL COMMENT '收件人uid',
  `pid` int(11) DEFAULT '0' COMMENT '父id',
  `subject` varchar(100) DEFAULT NULL COMMENT '消息主题',
  `content` varchar(900) DEFAULT NULL COMMENT '消息内容',
  `sendtime` int(11) DEFAULT NULL COMMENT '发件时间',
  `isread` tinyint(1) DEFAULT '2' COMMENT '状态(1已读,2未读)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `mx_auth_operate`;

CREATE TABLE `mx_auth_operate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL COMMENT '名称',
  `rid` int(11) DEFAULT NULL COMMENT '对应auth_rule的id',
  `path` varchar(100) DEFAULT NULL COMMENT '路径',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态(1可用,2不可用)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `mx_auth_access`;

CREATE TABLE `mx_auth_access` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) unsigned NOT NULL COMMENT '用户id',
  `group_id` mediumint(8) unsigned NOT NULL COMMENT '组id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `mx_auth_access` */

insert  into `mx_auth_access`(`id`,`uid`,`group_id`) values (1,1,1);

DROP TABLE IF EXISTS `mx_auth_group`;
CREATE TABLE `mx_auth_group` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(100) NOT NULL DEFAULT '' COMMENT '组名称',
  `description` varchar(300) NOT NULL COMMENT '组权限描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态(1已启用,2已禁用)',
  `rules` varchar(255) NOT NULL DEFAULT '' COMMENT '规则',
  `operate` varchar(100) NOT NULL COMMENT '操作',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

insert  into `mx_auth_group`(`id`,`groupname`,`description`,`status`,`rules`,`operate`) values (1,'超级管理员','',1,'1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34','1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34'),(2,'Api开发人员','',1,'4,5,6,15,16,17,30,7,18,19,20,8,21,22,9,34,10,23,11,12,13','1,2,5,6,3,4,7,8,9,10,11,12,13,14,15,16'),(3,'Api审核人员','',1,'4,5,6,15,16,17,30,7,18,19,20,8,21,22,9,34,10,23,11,12,13','1,2,5,6,3,4,7,8,9,10,11,12,13,14,15,16'),(4,'app开发组','用于开发人员，可以编辑、修改、查看api',2,'4,5,6,15,16,17,30,7,18,19,20,8,21,22,9,34,10,23,11,12,13','1,2,5,6,3,4,7,8,9,10,11,12,13,14,15,16');

insert  into `mx_auth_operate`(`id`,`title`,`rid`,`path`,`status`) values (1,'添加版本',15,'Api/info',1),(2,'废弃',15,'Api/discard',1),(3,'修改',30,'Api/info',1),(4,'发布/删除',30,'Api/operate',1),(5,'保存',16,'Api/store',1),(6,'通过/拒绝',17,'Api/audit',1),(7,'导出',18,'Export/v1/classify',1),(8,'添加/编辑分类',18,'Category/info',1),(9,'添加/编辑子分类',18,'Category/infoSub',1),(10,'保存',19,'Category/store',1),(11,'保存',20,'Category/store',1),(12,'编辑',21,'Company/secret/info',1),(13,'删除',21,'Company/secret/operate',1),(14,'保存',22,'Company/secret/store',1),(15,'新增',23,'User/info',1),(16,'修改',23,'User/info',1),(17,'保存',24,'User/store',1),(18,'功能权限',25,'Group/featureAuth',1),(19,'数据权限',25,'Group/dataAuth',1),(20,'修改/删除',25,'Group/operate',1),(21,'保存',31,'Group/featureStore',1),(22,'保存',32,'Group/dataStore',1),(23,'保存',27,'Sys/siteStore',1),(24,'保存',33,'Sys/env/store',1),(25,'保存',34,'Project/store',1),(26,'保存',26,'Group/store',1);

DROP TABLE IF EXISTS `mx_auth_rule`;
CREATE TABLE `mx_auth_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '父级id',
  `path` varchar(80) NOT NULL DEFAULT '' COMMENT '路径',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '名称',
  `icon` varchar(100) DEFAULT NULL COMMENT 'icon图标',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态(0隐藏,1显示)',
  `sort` int(11) NOT NULL COMMENT '排序',
  `isdel` tinyint(1) DEFAULT '2' COMMENT '是否删除(1已删除,2未删除)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
insert  into `mx_auth_rule`(`id`,`pid`,`path`,`title`,`icon`,`status`,`sort`,`isdel`) values (1,0,'','导航栏',NULL,1,1,2),(2,0,'','项目选择',NULL,1,2,2),(3,0,'','用户中心',NULL,1,3,2),(4,1,'Index/index','控制台','glyphicon glyphicon-stats icon text-primary-dker',1,1,2),(5,1,'Api/search','接口搜索','glyphicon glyphicon-search icon text-info',1,2,2),(6,1,'','接口信息','glyphicon glyphicon-tasks icon',1,3,2),(7,1,'','分类信息','glyphicon glyphicon-th-large icon text-success',1,4,2),(8,1,'','公司密钥','glyphicon glyphicon-link icon text-danger',1,5,2),(9,2,'','添加项目','glyphicon glyphicon-plus',1,1,2),(10,3,'','用户及组','icon-users icon text-muted\"',1,1,2),(11,3,'Message/index','消息通知','glyphicon glyphicon-envelope icon text-info-lter',1,2,2),(12,3,'','个人信息','icon-user icon text-success-lter',0,3,2),(13,3,'Help/index','帮助中心','icon-question icon',1,4,2),(14,3,'','系统设置','glyphicon glyphicon-cog icon text-#000000',1,5,2),(15,6,'Api/list','接口列表',NULL,1,1,2),(16,6,'Api/info','接口添加',NULL,1,2,2),(17,6,'Api/audit','待审核接口',NULL,1,3,2),(18,7,'Category/index','接口分类',NULL,1,1,2),(19,7,'Category/info','添加分类',NULL,1,2,2),(20,7,'Category/infoSub','添加子分类',NULL,0,3,2),(21,8,'Company/index','公司列表',NULL,1,1,2),(22,8,'Company/secret/info','创建密钥',NULL,1,2,2),(23,10,'User/index','用户管理',NULL,1,1,2),(24,10,'User/info','新增用户',NULL,1,2,2),(25,10,'Group/index','用户组管理',NULL,1,3,2),(26,10,'Group/info','新增用户组',NULL,1,4,2),(27,14,'Sys/site','网站设置',NULL,1,1,2),(28,14,'Sys/project','项目设置',NULL,1,2,2),(29,14,'Sys/log','操作日志',NULL,0,3,2),(30,6,'Api/detail','接口详情',NULL,0,4,2),(31,10,'Group/featureAuth','功能权限',NULL,0,5,2),(32,10,'Group/dataAuth','数据权限',NULL,0,6,2),(33,14,'Sys/env','环境设置',NULL,0,4,2),(34,2,'Project/edit','项目编辑',NULL,0,2,2);

/*api 列表、分类*/
ALTER TABLE `mx_apilist` ADD `proid` INT(11) NOT NULL DEFAULT 1 COMMENT '项目id' AFTER `id`;
ALTER TABLE `mx_classify` ADD `proid` INT(11) NOT NULL DEFAULT 1 COMMENT '项目id' AFTER `id`;

/*api 详情添加*/
ALTER TABLE `mx_apidetail` ADD `type` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '请求方式(1GET,2POST,3PUT,4DELETE)' AFTER `network`;
ALTER TABLE `mx_apidetail` ADD `header` LONGTEXT NULL COMMENT 'header参数' AFTER `type`;

ALTER TABLE `mx_apidetail` ADD `request` LONGTEXT NULL COMMENT '请求参数' AFTER `header`;
ALTER TABLE `mx_apidetail` ADD `response` LONGTEXT NULL COMMENT '响应参数' AFTER `request`;
ALTER TABLE `mx_apidetail` ADD `statuscode` LONGTEXT NULL COMMENT '状态码' AFTER `response`;
ALTER TABLE `mx_apidetail` ADD `isheader` TINYINT(1) NOT NULL DEFAULT '2' COMMENT '是否有header参数(1是,2否)' AFTER `type`;
ALTER TABLE `mx_apidetail` ADD `response_type` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '响应类型(1json,2xml,3jsonp,4html)' AFTER `type`;

ALTER TABLE `mx_classify` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '分类描述';
ALTER TABLE `mx_apienv` ADD `domain` VARCHAR(200) NOT NULL COMMENT '环境域名' AFTER `envname`;
ALTER TABLE `mx_apienv` ADD proid int(11) default '1' comment '项目id' after id ;
ALTER TABLE `mx_user` CHANGE `avatar` `avatar` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '用户头像';
ALTER TABLE `mx_auth_group` ADD `description` VARCHAR(300) NOT NULL COMMENT '组权限描述' AFTER `groupname`;
ALTER TABLE `mx_auth_group` ADD `operate` VARCHAR(100) NOT NULL COMMENT '操作' AFTER `rules`;
