DROP TABLE IF EXISTS `configs`;

CREATE TABLE `configs` (
  `id` BIGINT(20) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `pid` BIGINT(20) NOT NULL DEFAULT '0' COMMENT '父级id',
  `key` VARCHAR(255) NOT NULL COMMENT '配置key',
  `value` TEXT NOT NULL COMMENT '配置key值',
  `remark` TEXT NOT NULL COMMENT '描述',
  `create_time` INT(11) NOT NULL COMMENT '创建时间',
  `update_time` INT(11) NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_pid_key_uniq` (`pid`,`key`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT  INTO `configs`(`id`,`pid`,`key`,`value`,`remark`,`create_time`,`update_time`) VALUES (1,0,'ver','1','',0,0);

DROP TABLE IF EXISTS `tags`;

CREATE TABLE `tags` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tag` VARCHAR(255) NOT NULL,
  `create_time` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `update_time` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `status` TINYINT(4) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_tag` (`tag`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT  INTO `tags`(`id`,`tag`,`create_time`,`update_time`,`status`) VALUES (1,'全部',0,0,0),(2,'软件',0,0,0),(3,'游戏',0,0,0),(4,'工具',0,0,0),(5,'网站',0,0,0);

DROP TABLE IF EXISTS `url_menus`;

CREATE TABLE `url_menus` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` VARCHAR(64) NOT NULL COMMENT '名称',
  `icon` VARCHAR(128) NOT NULL DEFAULT '' COMMENT '图标',
  `url` VARCHAR(128) NOT NULL COMMENT '地址',
  `pid` INT(11) NOT NULL DEFAULT '0' COMMENT '父级id 0 为根菜单',
  `has_child` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '有无子菜单 0 无 1 有',
  `px` INT(11) NOT NULL DEFAULT '0' COMMENT '排序值',
  `create_time` INT(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` SMALLINT(4) NOT NULL DEFAULT '0' COMMENT '状态 0 正常 -1 删除',
  PRIMARY KEY (`id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT  INTO `url_menus`(`id`,`name`,`icon`,`url`,`pid`,`has_child`,`px`,`create_time`,`update_time`,`status`) VALUES (1,'优选','','#',0,1,1,0,0,0),(2,'分类','','#',0,1,2,0,0,0),(3,'工具','','#',0,1,3,0,0,0),(4,'关于','','#',0,1,5,0,0,0),(5,'推荐','','/index?t=5',1,0,1,0,0,0),(6,'应用','','/index?t=6',2,0,1,0,0,0),(7,'搜索引擎','','/index?t=7',3,0,1,0,0,0),(8,'作者','','/about',4,0,1,0,0,0),(9,'发现','','/',1,0,2,0,0,0),(10,'更新菜单栏','','/menus/refreshHtml',3,0,2,0,0,0),(11,'标签','','/tags?t=11',3,0,4,0,0,0);

DROP TABLE IF EXISTS `url_tags`;

CREATE TABLE `url_tags` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `url_id` INT(11) UNSIGNED NOT NULL COMMENT '链接ID',
  `tag_id` INT(11) UNSIGNED NOT NULL COMMENT '标签ID',
  `create_time` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  `update_time` INT(11) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_url_tag` (`url_id`,`tag_id`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

INSERT  INTO `url_tags`(`id`,`url_id`,`tag_id`,`create_time`,`update_time`) VALUES (1,1,1,0,0),(2,1,2,0,0),(3,1,3,0,0),(4,1,4,0,0),(5,1,5,0,0);

DROP TABLE IF EXISTS `urls`;

CREATE TABLE `urls` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` VARCHAR(255) NOT NULL COMMENT '标题名称',
  `url` VARCHAR(255) NOT NULL COMMENT '链接地址',
  `icon` VARCHAR(255) NOT NULL COMMENT '图标地址',
  `remark` TEXT NOT NULL COMMENT '简介描述',
  `create_time` INT(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` INT(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `rec` SMALLINT(4) NOT NULL DEFAULT '0' COMMENT '推荐分类 0 默认 1 rec 2 导航类 3-16 具体类别 99 优秀合集',
  `weight` INT(11) NOT NULL DEFAULT '0' COMMENT '权重 排序倒序',
  `status` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '状态 0 正常 -1 删除',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_url_uniq` (`url`)
) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

INSERT  INTO `urls`(`id`,`title`,`url`,`icon`,`remark`,`create_time`,`update_time`,`rec`,`weight`,`status`) VALUES (1,'百度一下','https://www.baidu.com','https://www.baidu.com/favicon.ico','百度一下，百度搜索引擎',0,0,5,1,0),(2,'必应搜索','https://cn.bing.com/','https://cn.bing.com/sa/simg/bing_p_rr_teal_min.ico','必应搜索引擎',0,0,6,0,0),(3,'梦想导航','https://nav.dreamthere.com/','https://nav.dreamthere.com/res/favicon.png','梦想导航-有梦的地方',0,0,7,0,0);

