/*
Navicat MySQL Data Transfer

Source Server         : es 搜一搜
Source Server Version : 50722
Source Host           : 222.186.150.3:3306
Source Database       : so_seos

Target Server Type    : MYSQL
Target Server Version : 50722
File Encoding         : 65001

Date: 2020-07-08 10:26:51
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ad
-- ----------------------------
DROP TABLE IF EXISTS `ad`;
CREATE TABLE `ad` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '广告名称',
  `as_id` tinyint(5) NOT NULL COMMENT '所属位置',
  `pic` varchar(200) NOT NULL DEFAULT '' COMMENT '广告图片URL',
  `url` varchar(200) NOT NULL DEFAULT '' COMMENT '广告链接',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  `endtime` varchar(255) NOT NULL COMMENT '到期时间',
  `usermobile` char(11) NOT NULL COMMENT '用户手机号',
  `sort` int(11) NOT NULL COMMENT '排序',
  `open` tinyint(2) NOT NULL COMMENT '1=审核  0=未审核',
  `content` varchar(225) DEFAULT '' COMMENT '广告内容',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `plug_ad_adtypeid` (`as_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=74 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='广告表';

-- ----------------------------
-- Table structure for admin
-- ----------------------------
DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `admin_id` tinyint(4) NOT NULL AUTO_INCREMENT COMMENT '管理员ID',
  `username` varchar(20) NOT NULL COMMENT '管理员用户名',
  `pwd` varchar(70) NOT NULL COMMENT '管理员密码',
  `group_id` mediumint(8) DEFAULT NULL COMMENT '分组ID',
  `email` varchar(30) DEFAULT NULL COMMENT '邮箱',
  `realname` varchar(10) DEFAULT NULL COMMENT '真实姓名',
  `tel` varchar(30) DEFAULT NULL COMMENT '电话号码',
  `ip` varchar(20) DEFAULT NULL COMMENT 'IP地址',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `mdemail` varchar(50) DEFAULT '0' COMMENT '传递修改密码参数加密',
  `is_open` tinyint(2) DEFAULT '0' COMMENT '审核状态',
  `avatar` varchar(120) DEFAULT '' COMMENT '头像',
  PRIMARY KEY (`admin_id`) USING BTREE,
  KEY `admin_username` (`username`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='后台管理员';

-- ----------------------------
-- Table structure for adsense
-- ----------------------------
DROP TABLE IF EXISTS `adsense`;
CREATE TABLE `adsense` (
  `as_id` tinyint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '广告位名称',
  `sort` int(11) NOT NULL COMMENT '广告位排序',
  PRIMARY KEY (`as_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='广告分类';

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userid` int(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(40) NOT NULL DEFAULT '',
  `title` varchar(80) NOT NULL DEFAULT '',
  `keywords` varchar(120) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `content` text NOT NULL COMMENT '内容',
  `template` varchar(40) NOT NULL DEFAULT '',
  `posid` tinyint(2) unsigned DEFAULT '0' COMMENT '推荐位',
  `status` varchar(255) NOT NULL DEFAULT '1',
  `recommend` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `readgroup` varchar(100) NOT NULL DEFAULT '',
  `readpoint` smallint(5) NOT NULL DEFAULT '0',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
  `copyfrom` varchar(255) NOT NULL DEFAULT 'CLTPHP',
  `fromlink` varchar(255) NOT NULL DEFAULT 'http://www.cltphp.com/',
  `thumb` varchar(100) NOT NULL DEFAULT '',
  `title_style` varchar(100) NOT NULL DEFAULT '',
  `tags` varchar(255) NOT NULL DEFAULT '' COMMENT '标签',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `status` (`id`,`status`,`sort`) USING BTREE,
  KEY `catid` (`id`,`catid`,`status`) USING BTREE,
  KEY `listorder` (`id`,`catid`,`status`,`sort`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for article_tags
-- ----------------------------
DROP TABLE IF EXISTS `article_tags`;
CREATE TABLE `article_tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tag_id` int(11) DEFAULT NULL,
  `article_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for auth_group
-- ----------------------------
DROP TABLE IF EXISTS `auth_group`;
CREATE TABLE `auth_group` (
  `group_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '全新ID',
  `title` char(100) NOT NULL DEFAULT '' COMMENT '标题',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态',
  `rules` longtext COMMENT '规则',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`group_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='管理员分组';

-- ----------------------------
-- Table structure for auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `auth_rule`;
CREATE TABLE `auth_rule` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `href` char(80) NOT NULL DEFAULT '',
  `title` char(20) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `authopen` tinyint(2) NOT NULL DEFAULT '1',
  `icon` varchar(20) DEFAULT NULL COMMENT '样式',
  `condition` char(100) DEFAULT '',
  `pid` int(5) NOT NULL DEFAULT '0' COMMENT '父栏目ID',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `addtime` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `zt` int(1) DEFAULT NULL,
  `menustatus` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=302 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='权限节点';

-- ----------------------------
-- Table structure for ban
-- ----------------------------
DROP TABLE IF EXISTS `ban`;
CREATE TABLE `ban` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ban` varchar(255) NOT NULL,
  `addtime` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14583 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for category
-- ----------------------------
DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `catname` varchar(255) NOT NULL DEFAULT '',
  `catdir` varchar(30) NOT NULL DEFAULT '',
  `parentdir` varchar(50) NOT NULL DEFAULT '',
  `pid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `moduleid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `module` char(24) NOT NULL DEFAULT '',
  `arrparentid` varchar(255) NOT NULL DEFAULT '',
  `arrchildid` varchar(100) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `title` varchar(150) NOT NULL DEFAULT '',
  `keywords` varchar(200) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `sort` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ishtml` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ismenu` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `image` varchar(100) NOT NULL DEFAULT '',
  `child` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `url` varchar(100) NOT NULL DEFAULT '',
  `template_list` varchar(20) NOT NULL DEFAULT '',
  `template_show` varchar(20) NOT NULL DEFAULT '',
  `pagesize` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `readgroup` varchar(100) NOT NULL DEFAULT '',
  `listtype` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否预览',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `listorder` (`sort`) USING BTREE,
  KEY `pid` (`pid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for city
-- ----------------------------
DROP TABLE IF EXISTS `city`;
CREATE TABLE `city` (
  `city_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '城市id',
  `city_zh` varchar(255) NOT NULL DEFAULT '' COMMENT '中文名',
  `city_en` varchar(255) NOT NULL DEFAULT '' COMMENT '英文名',
  `city_short` varchar(255) NOT NULL COMMENT '简称',
  `city_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '1省 2市',
  `p_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`city_id`) USING BTREE,
  UNIQUE KEY `city_en` (`city_en`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=698 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for config
-- ----------------------------
DROP TABLE IF EXISTS `config`;
CREATE TABLE `config` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `name` varchar(50) DEFAULT NULL COMMENT '配置的key键名',
  `value` varchar(512) DEFAULT NULL COMMENT '配置的val值',
  `inc_type` varchar(64) DEFAULT NULL COMMENT '配置分组',
  `desc` varchar(50) DEFAULT NULL COMMENT '描述',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=90 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for debris
-- ----------------------------
DROP TABLE IF EXISTS `debris`;
CREATE TABLE `debris` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `type_id` int(6) DEFAULT NULL COMMENT '碎片分类ID',
  `title` varchar(120) DEFAULT NULL COMMENT '标题',
  `content` text COMMENT '内容',
  `addtime` int(13) DEFAULT NULL COMMENT '添加时间',
  `sort` int(11) DEFAULT '50' COMMENT '排序',
  `url` varchar(120) DEFAULT '' COMMENT '链接',
  `pic` varchar(120) DEFAULT '' COMMENT '图片',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for debris_type
-- ----------------------------
DROP TABLE IF EXISTS `debris_type`;
CREATE TABLE `debris_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(120) DEFAULT NULL,
  `sort` int(1) DEFAULT '50',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for donation
-- ----------------------------
DROP TABLE IF EXISTS `donation`;
CREATE TABLE `donation` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `name` varchar(120) NOT NULL DEFAULT '' COMMENT '用户名',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '捐赠金额',
  `addtime` varchar(15) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for download
-- ----------------------------
DROP TABLE IF EXISTS `download`;
CREATE TABLE `download` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userid` int(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(40) NOT NULL DEFAULT '',
  `title` varchar(120) NOT NULL DEFAULT '',
  `title_style` varchar(225) NOT NULL DEFAULT '',
  `thumb` varchar(225) NOT NULL DEFAULT '',
  `keywords` varchar(120) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `content` text NOT NULL,
  `template` varchar(40) NOT NULL DEFAULT '',
  `posid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `recommend` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `readgroup` varchar(100) NOT NULL DEFAULT '',
  `readpoint` smallint(5) NOT NULL DEFAULT '0',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
  `files` varchar(80) NOT NULL DEFAULT '',
  `ext` varchar(255) NOT NULL DEFAULT 'zip',
  `size` varchar(255) NOT NULL DEFAULT '',
  `downs` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `status` (`id`,`status`,`sort`) USING BTREE,
  KEY `catid` (`id`,`catid`,`status`) USING BTREE,
  KEY `listorder` (`id`,`catid`,`status`,`sort`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for feast
-- ----------------------------
DROP TABLE IF EXISTS `feast`;
CREATE TABLE `feast` (
  `id` int(4) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `title` varchar(120) DEFAULT '' COMMENT '标题',
  `open` int(1) DEFAULT '1' COMMENT '是否开启',
  `sort` int(4) DEFAULT '50' COMMENT '排序',
  `addtime` varchar(15) DEFAULT NULL COMMENT '添加时间',
  `feast_date` varchar(20) DEFAULT '' COMMENT '节日日期',
  `type` int(1) DEFAULT '1' COMMENT '1阳历 2农历',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='节日列表';

-- ----------------------------
-- Table structure for feast_element
-- ----------------------------
DROP TABLE IF EXISTS `feast_element`;
CREATE TABLE `feast_element` (
  `id` int(5) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `pid` int(4) DEFAULT NULL COMMENT '父级ID',
  `title` varchar(120) DEFAULT NULL COMMENT '标题',
  `css` text COMMENT 'CSS',
  `js` text COMMENT 'JS',
  `sort` int(5) DEFAULT '50' COMMENT '排序',
  `open` int(1) DEFAULT '1' COMMENT '是否开启',
  `addtime` varchar(15) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='节日元素表';

-- ----------------------------
-- Table structure for fenlei
-- ----------------------------
DROP TABLE IF EXISTS `fenlei`;
CREATE TABLE `fenlei` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fenlei` varchar(40) NOT NULL COMMENT '分类',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for field
-- ----------------------------
DROP TABLE IF EXISTS `field`;
CREATE TABLE `field` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `moduleid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `field` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(30) NOT NULL DEFAULT '',
  `tips` varchar(150) NOT NULL DEFAULT '',
  `required` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `minlength` int(10) unsigned NOT NULL DEFAULT '0',
  `maxlength` int(10) unsigned NOT NULL DEFAULT '0',
  `pattern` varchar(255) NOT NULL DEFAULT '',
  `errormsg` varchar(255) NOT NULL DEFAULT '',
  `class` varchar(20) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `setup` text,
  `ispost` tinyint(1) NOT NULL DEFAULT '0',
  `unpostgroup` varchar(60) NOT NULL DEFAULT '',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `issystem` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=161 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for hot_keyword
-- ----------------------------
DROP TABLE IF EXISTS `hot_keyword`;
CREATE TABLE `hot_keyword` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(32) NOT NULL COMMENT '关键词',
  `averagePv` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '周平均搜索量: 一周PC,移动端的关键词平均搜索量之和',
  `averagePvPc` int(11) unsigned NOT NULL COMMENT '周平均PC搜索量：一周PC端关键词搜索量之和',
  `averagePvMobile` int(11) unsigned NOT NULL COMMENT '周平均移动搜索：一周移动端关键词搜索量之和',
  `averageDayPv` int(11) unsigned NOT NULL COMMENT '日平均 搜索量 ：PC端和移动端的关键词搜索量；',
  `averageDayPvPc` int(11) unsigned NOT NULL COMMENT 'PC日均 搜索量 ： PC端每日关键词搜索量；',
  `averageDayPvMobile` int(11) unsigned NOT NULL COMMENT '移动日均搜索量： 移动端每日关键词搜索量',
  `competition` int(11) unsigned NOT NULL,
  `recommendPrice` decimal(10,2) unsigned NOT NULL,
  `recommendPricePc` decimal(10,2) unsigned NOT NULL,
  `recommendPriceMobile` decimal(10,2) unsigned NOT NULL,
  `showReasons` varchar(64) NOT NULL,
  `matchType` int(11) unsigned NOT NULL,
  `phraseType` int(11) unsigned NOT NULL,
  `campaignId` int(11) unsigned NOT NULL,
  `adgroupId` int(11) unsigned NOT NULL,
  `create_time` int(11) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyword` (`keyword`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=54228576 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for link
-- ----------------------------
DROP TABLE IF EXISTS `link`;
CREATE TABLE `link` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL COMMENT '链接名称',
  `url` varchar(200) NOT NULL COMMENT '链接URL',
  `sort` int(5) NOT NULL DEFAULT '50' COMMENT '排序',
  `addtime` int(11) NOT NULL COMMENT '添加时间',
  `open` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0禁用1启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for message
-- ----------------------------
DROP TABLE IF EXISTS `message`;
CREATE TABLE `message` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT '' COMMENT '留言标题',
  `tel` varchar(15) NOT NULL DEFAULT '' COMMENT '留言电话',
  `addtime` varchar(15) NOT NULL COMMENT '留言时间',
  `open` tinyint(2) NOT NULL DEFAULT '0' COMMENT '1=审核 0=不审核',
  `ip` varchar(50) DEFAULT '' COMMENT '留言者IP',
  `content` longtext NOT NULL COMMENT '留言内容',
  `name` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `email` varchar(50) NOT NULL COMMENT '留言邮箱',
  PRIMARY KEY (`message_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for module
-- ----------------------------
DROP TABLE IF EXISTS `module`;
CREATE TABLE `module` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `issystem` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `listfields` varchar(255) NOT NULL DEFAULT '',
  `sort` smallint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for oauth
-- ----------------------------
DROP TABLE IF EXISTS `oauth`;
CREATE TABLE `oauth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL COMMENT '用户id',
  `type` varchar(50) DEFAULT NULL COMMENT '账号类型',
  `openid` varchar(120) DEFAULT NULL COMMENT '第三方唯一标示',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for page
-- ----------------------------
DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(80) NOT NULL DEFAULT '',
  `title_style` varchar(225) NOT NULL DEFAULT '',
  `thumb` varchar(225) NOT NULL DEFAULT '',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `status` varchar(255) NOT NULL DEFAULT '1',
  `userid` int(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(40) NOT NULL DEFAULT '',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `content` text COMMENT '内容',
  `template` varchar(50) DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for picture
-- ----------------------------
DROP TABLE IF EXISTS `picture`;
CREATE TABLE `picture` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userid` int(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(40) NOT NULL DEFAULT '',
  `title` varchar(80) NOT NULL DEFAULT '',
  `keywords` varchar(120) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `content` text NOT NULL,
  `template` varchar(40) NOT NULL DEFAULT '',
  `posid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `recommend` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `readgroup` varchar(100) NOT NULL DEFAULT '',
  `readpoint` smallint(5) NOT NULL DEFAULT '0',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
  `pic` varchar(80) NOT NULL DEFAULT '',
  `group` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `status` (`id`,`status`,`sort`) USING BTREE,
  KEY `catid` (`id`,`catid`,`status`) USING BTREE,
  KEY `listorder` (`id`,`catid`,`status`,`sort`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for plugin
-- ----------------------------
DROP TABLE IF EXISTS `plugin`;
CREATE TABLE `plugin` (
  `code` varchar(13) DEFAULT NULL COMMENT '插件编码',
  `name` varchar(55) DEFAULT NULL COMMENT '中文名字',
  `version` varchar(255) DEFAULT NULL COMMENT '插件的版本',
  `author` varchar(30) DEFAULT NULL COMMENT '插件作者',
  `config` text COMMENT '配置信息',
  `config_value` text COMMENT '配置值信息',
  `desc` varchar(255) DEFAULT NULL COMMENT '插件描述',
  `status` tinyint(1) DEFAULT '0' COMMENT '是否启用',
  `type` varchar(50) DEFAULT NULL COMMENT '插件类型 payment支付 login 登陆 shipping物流',
  `icon` varchar(255) DEFAULT NULL COMMENT '图标',
  `bank_code` text COMMENT '网银配置信息',
  `scene` tinyint(1) DEFAULT '0' COMMENT '使用场景 0 PC+手机 1 手机 2 PC'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for posid
-- ----------------------------
DROP TABLE IF EXISTS `posid`;
CREATE TABLE `posid` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL DEFAULT '',
  `sort` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for product
-- ----------------------------
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userid` int(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(40) NOT NULL DEFAULT '',
  `title` varchar(120) NOT NULL DEFAULT '',
  `title_style` varchar(225) NOT NULL DEFAULT '',
  `thumb` varchar(225) NOT NULL DEFAULT '',
  `keywords` varchar(120) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `content` text NOT NULL,
  `template` varchar(40) NOT NULL DEFAULT '',
  `posid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `recommend` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `readgroup` varchar(100) NOT NULL DEFAULT '',
  `readpoint` smallint(5) NOT NULL DEFAULT '0',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `xinghao` varchar(255) NOT NULL DEFAULT '',
  `pics` mediumtext NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `status` (`id`,`status`,`sort`) USING BTREE,
  KEY `catid` (`id`,`catid`,`status`) USING BTREE,
  KEY `listorder` (`id`,`catid`,`status`,`sort`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for qq
-- ----------------------------
DROP TABLE IF EXISTS `qq`;
CREATE TABLE `qq` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `uid` int(11) DEFAULT NULL COMMENT '会员id',
  `qq` bigint(11) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `modify_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for region
-- ----------------------------
DROP TABLE IF EXISTS `region`;
CREATE TABLE `region` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `pid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `name` varchar(120) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3410 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for relevant_keyword
-- ----------------------------
DROP TABLE IF EXISTS `relevant_keyword`;
CREATE TABLE `relevant_keyword` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(32) NOT NULL COMMENT '关键词',
  `averagePv` int(11) unsigned NOT NULL,
  `averagePvPc` int(11) unsigned NOT NULL,
  `averagePvMobile` int(11) unsigned NOT NULL,
  `averageDayPv` int(11) unsigned NOT NULL,
  `averageDayPvPc` int(11) unsigned NOT NULL,
  `averageDayPvMobile` int(11) unsigned NOT NULL,
  `competition` int(11) unsigned NOT NULL,
  `recommendPrice` decimal(10,2) unsigned NOT NULL,
  `recommendPricePc` decimal(10,2) unsigned NOT NULL,
  `recommendPriceMobile` decimal(10,2) unsigned NOT NULL,
  `showReasons` varchar(64) NOT NULL,
  `matchType` int(11) unsigned NOT NULL,
  `phraseType` int(11) unsigned NOT NULL,
  `campaignId` int(11) unsigned NOT NULL,
  `adgroupId` int(11) unsigned NOT NULL,
  `create_time` int(11) unsigned NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `keyword` (`keyword`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=79377860 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `pid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sort` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for role_user
-- ----------------------------
DROP TABLE IF EXISTS `role_user`;
CREATE TABLE `role_user` (
  `role_id` mediumint(9) unsigned DEFAULT '0',
  `user_id` char(32) DEFAULT '0',
  KEY `group_id` (`role_id`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

-- ----------------------------
-- Table structure for seo_api
-- ----------------------------
DROP TABLE IF EXISTS `seo_api`;
CREATE TABLE `seo_api` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `api` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT 'api地址',
  `title` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `description` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0禁用  1启用',
  `num` int(10) NOT NULL DEFAULT '0' COMMENT '次数',
  `price` float(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `img` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `example` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for seo_api_log
-- ----------------------------
DROP TABLE IF EXISTS `seo_api_log`;
CREATE TABLE `seo_api_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '伪原创接口请求日志',
  `uid` int(10) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `api_id` int(10) NOT NULL,
  `state_code` int(10) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `param` varchar(255) NOT NULL,
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2164066 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for seo_api_order
-- ----------------------------
DROP TABLE IF EXISTS `seo_api_order`;
CREATE TABLE `seo_api_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL DEFAULT '0' COMMENT '会员id',
  `mobile` varchar(11) CHARACTER SET utf8 NOT NULL,
  `aid` int(10) NOT NULL DEFAULT '0' COMMENT 'api  id',
  `order_id` varchar(30) CHARACTER SET utf8 NOT NULL,
  `title` varchar(255) CHARACTER SET utf8 NOT NULL,
  `buy_num` int(10) NOT NULL,
  `total` float(11,2) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `addtime` int(11) NOT NULL,
  `paytime` int(11) DEFAULT NULL,
  `pay_type` varchar(20) DEFAULT 'alipay' COMMENT '支付方式',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for seo_api_times
-- ----------------------------
DROP TABLE IF EXISTS `seo_api_times`;
CREATE TABLE `seo_api_times` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `aid` int(10) NOT NULL,
  `total_num` int(10) NOT NULL,
  `num` int(10) NOT NULL,
  `create_time` int(11) NOT NULL,
  `modify_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for seo_goods
-- ----------------------------
DROP TABLE IF EXISTS `seo_goods`;
CREATE TABLE `seo_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goodname` varchar(255) DEFAULT NULL COMMENT '产品名称',
  `goodurl` varchar(255) DEFAULT NULL COMMENT '产品图片',
  `ticket` int(11) unsigned DEFAULT '0' COMMENT '所需积分',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '添加时间',
  `sort` int(10) unsigned DEFAULT NULL COMMENT '排序',
  `is_online` tinyint(1) unsigned DEFAULT '1' COMMENT '是否显示:1显示,0不显示',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for seo_goods_spend
-- ----------------------------
DROP TABLE IF EXISTS `seo_goods_spend`;
CREATE TABLE `seo_goods_spend` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `umobile` char(30) NOT NULL COMMENT '用户手机号',
  `gid` int(10) unsigned NOT NULL COMMENT '商品id',
  `spendcode` int(10) unsigned DEFAULT NULL COMMENT '扣除积分',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '兑换时间',
  `content` varchar(255) DEFAULT NULL COMMENT '备注,记录',
  `keywords` varchar(255) NOT NULL COMMENT '下载的关键字',
  `keytype` int(1) NOT NULL COMMENT '下载的关键字类别,1关键词挖掘,2相关词挖掘',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2151 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for seo_ipconvert
-- ----------------------------
DROP TABLE IF EXISTS `seo_ipconvert`;
CREATE TABLE `seo_ipconvert` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT NULL COMMENT '用户id',
  `ipsex` text COMMENT 'ip6地址',
  `ipfour` text COMMENT 'ip4地址',
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for seo_keyd_records
-- ----------------------------
DROP TABLE IF EXISTS `seo_keyd_records`;
CREATE TABLE `seo_keyd_records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `keywordhotdig` varchar(255) NOT NULL COMMENT '记录关键词热度',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '记录的时间',
  `ipaddress` varchar(255) NOT NULL COMMENT '记录用户ip地址',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `keywordhotdig` (`id`,`keywordhotdig`)
) ENGINE=MyISAM AUTO_INCREMENT=2663172 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for seo_keyh_records
-- ----------------------------
DROP TABLE IF EXISTS `seo_keyh_records`;
CREATE TABLE `seo_keyh_records` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `keywordhotdig` varchar(255) NOT NULL COMMENT '记录关键词热度',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '记录的时间',
  `ipaddress` varchar(255) NOT NULL COMMENT '记录用户ip地址',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=694032 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for seo_keyjj_records
-- ----------------------------
DROP TABLE IF EXISTS `seo_keyjj_records`;
CREATE TABLE `seo_keyjj_records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `keywordhotdig` varchar(255) NOT NULL COMMENT '记录关键词热度',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '记录的时间',
  `ipaddress` varchar(255) NOT NULL COMMENT '记录用户ip地址',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=179602 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for seo_keyword_hotdig
-- ----------------------------
DROP TABLE IF EXISTS `seo_keyword_hotdig`;
CREATE TABLE `seo_keyword_hotdig` (
  `id` char(32) NOT NULL,
  `id2` int(10) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(25) NOT NULL COMMENT '关键词',
  `averagePv` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '周平均搜索量: 一周PC,移动端的关键词平均搜索量之和',
  `averagePvPc` int(11) unsigned NOT NULL COMMENT '周平均PC搜索量：一周PC端关键词搜索量之和',
  `averagePvMobile` int(11) unsigned NOT NULL COMMENT '周平均移动搜索：一周移动端关键词搜索量之和',
  `averageDayPv` int(11) unsigned NOT NULL COMMENT '日平均 搜索量 ：PC端和移动端的关键词搜索量；',
  `averageDayPvPc` int(11) unsigned NOT NULL COMMENT 'PC日均 搜索量 ： PC端每日关键词搜索量；',
  `averageDayPvMobile` int(11) unsigned NOT NULL COMMENT '移动日均搜索量： 移动端每日关键词搜索量',
  `competition` int(11) unsigned NOT NULL,
  `recommendPrice` decimal(10,2) unsigned NOT NULL,
  `recommendPricePc` float(10,2) unsigned NOT NULL,
  `recommendPriceMobile` float(10,2) unsigned NOT NULL,
  `showReasons` varchar(255) NOT NULL,
  `matchType` int(11) unsigned NOT NULL,
  `phraseType` int(11) unsigned NOT NULL,
  `campaignId` int(11) unsigned NOT NULL,
  `adgroupId` int(11) unsigned NOT NULL,
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id2`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `keyword` (`keyword`) USING BTREE,
  KEY `create_time` (`create_time`),
  FULLTEXT KEY `testfulltext` (`keyword`) /*!50100 WITH PARSER `ngram` */ 
) ENGINE=MyISAM AUTO_INCREMENT=54333934 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for seo_monitor_keywords
-- ----------------------------
DROP TABLE IF EXISTS `seo_monitor_keywords`;
CREATE TABLE `seo_monitor_keywords` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `dmwebid` int(10) unsigned NOT NULL COMMENT '网址id',
  `enginetype` tinyint(1) unsigned NOT NULL COMMENT '引擎类型',
  `platform` tinyint(1) unsigned NOT NULL COMMENT '终端类型',
  `dmkeywords` varchar(255) NOT NULL COMMENT '关键词名称',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '添加时间',
  `keyrank` text NOT NULL COMMENT '关键词排名结果,每天的结果用","隔开',
  `update_time` int(11) unsigned NOT NULL COMMENT '每天的更新时间',
  `collectnum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收录量',
  `china_name` varchar(255) DEFAULT NULL COMMENT '熊掌号-监控使用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=28100 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for seo_monitor_website
-- ----------------------------
DROP TABLE IF EXISTS `seo_monitor_website`;
CREATE TABLE `seo_monitor_website` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `webname` varchar(255) NOT NULL COMMENT '网站名称',
  `weburl` varchar(255) DEFAULT NULL COMMENT '网站地址',
  `collectnum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '收录量',
  `flownum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '流量',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '网站添加时间',
  `update_time2` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=1050 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for seo_order
-- ----------------------------
DROP TABLE IF EXISTS `seo_order`;
CREATE TABLE `seo_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '//订单id',
  `uid` int(11) NOT NULL,
  `mobile` varchar(11) NOT NULL COMMENT '//用户名(手机号)',
  `order_id` varchar(30) NOT NULL COMMENT '//订单号',
  `title` varchar(255) NOT NULL COMMENT '订单标题',
  `buy_time` int(2) NOT NULL COMMENT '//购买时长',
  `buy_level` int(2) NOT NULL COMMENT '购买会员等级',
  `total` float(11,2) NOT NULL COMMENT '//总额',
  `status` tinyint(4) NOT NULL COMMENT '//支付状态：0表示未支付  1表示已支付待处理   2表示已交付',
  `ordertype` tinyint(4) unsigned DEFAULT '1' COMMENT '订单类别',
  `addtime` int(11) NOT NULL COMMENT '//购买时间',
  `paytime` int(11) DEFAULT NULL COMMENT '付款时间',
  `opentime` int(11) DEFAULT NULL COMMENT '服务器开通时间',
  `endtime` int(11) DEFAULT NULL COMMENT '服务器到期时间',
  `pay_type` varchar(20) DEFAULT 'alipay' COMMENT '支付方式',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=215 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for seo_pay
-- ----------------------------
DROP TABLE IF EXISTS `seo_pay`;
CREATE TABLE `seo_pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '收款订单id',
  `notify_time` varchar(20) NOT NULL COMMENT '通知时间',
  `notify_type` varchar(30) NOT NULL COMMENT '通知类型',
  `notify_id` varchar(40) NOT NULL COMMENT '通知校验ID',
  `sign_type` varchar(5) NOT NULL COMMENT '签名方式',
  `sign` varchar(70) NOT NULL COMMENT '签名',
  `out_trade_no` char(50) NOT NULL COMMENT '商户网站唯一订单号',
  `subject` varchar(32) NOT NULL COMMENT '商品名称',
  `trade_no` char(64) NOT NULL COMMENT '支付交易号',
  `trade_status` varchar(15) NOT NULL COMMENT '交易状态',
  `gmt_create` varchar(20) NOT NULL COMMENT '交易创建时间',
  `gmt_payment` varchar(20) NOT NULL COMMENT '交易付款时间',
  `buyer_email` varchar(100) NOT NULL COMMENT '买家支付宝账号',
  `zonghe` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=141 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for seo_points_log
-- ----------------------------
DROP TABLE IF EXISTS `seo_points_log`;
CREATE TABLE `seo_points_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `umobile` varchar(255) DEFAULT NULL COMMENT '用户手机号',
  `remarks` varchar(255) DEFAULT NULL COMMENT '操作备注',
  `points` int(255) unsigned DEFAULT '2' COMMENT '扣除的积分',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '扣除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for seo_regist_times
-- ----------------------------
DROP TABLE IF EXISTS `seo_regist_times`;
CREATE TABLE `seo_regist_times` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `times` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Table structure for seo_relevant_records
-- ----------------------------
DROP TABLE IF EXISTS `seo_relevant_records`;
CREATE TABLE `seo_relevant_records` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `relevantkey` varchar(255) NOT NULL COMMENT '记录相关词',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '记录的时间',
  `ipaddress` varchar(255) NOT NULL COMMENT '记录用户ip地址',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2732198 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for seo_relevant_word
-- ----------------------------
DROP TABLE IF EXISTS `seo_relevant_word`;
CREATE TABLE `seo_relevant_word` (
  `id` char(32) DEFAULT NULL,
  `id2` int(10) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(25) DEFAULT NULL COMMENT '关键词',
  `averagePv` int(11) unsigned DEFAULT NULL,
  `averagePvPc` int(11) unsigned DEFAULT NULL,
  `averagePvMobile` int(11) unsigned DEFAULT NULL,
  `averageDayPv` int(11) unsigned DEFAULT NULL,
  `averageDayPvPc` int(11) unsigned DEFAULT NULL,
  `averageDayPvMobile` int(11) unsigned DEFAULT NULL,
  `competition` int(11) unsigned DEFAULT NULL,
  `recommendPrice` float(10,2) unsigned DEFAULT NULL,
  `recommendPricePc` float(10,2) unsigned DEFAULT NULL,
  `recommendPriceMobile` float(10,2) unsigned DEFAULT NULL,
  `showReasons` varchar(255) DEFAULT NULL,
  `matchType` int(11) unsigned DEFAULT NULL,
  `phraseType` int(11) unsigned DEFAULT NULL,
  `campaignId` int(11) unsigned DEFAULT NULL,
  `adgroupId` int(11) unsigned DEFAULT NULL,
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id2`),
  UNIQUE KEY `id` (`id`) USING BTREE,
  KEY `keyword` (`keyword`) USING BTREE,
  FULLTEXT KEY `testfulltext` (`keyword`) /*!50100 WITH PARSER `ngram` */ 
) ENGINE=MyISAM AUTO_INCREMENT=79717861 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for seo_sitemap
-- ----------------------------
DROP TABLE IF EXISTS `seo_sitemap`;
CREATE TABLE `seo_sitemap` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loc` varchar(255) DEFAULT NULL COMMENT '网址',
  `priority` varchar(255) DEFAULT NULL COMMENT '优先级',
  `lastmod` datetime DEFAULT NULL COMMENT '时间',
  `changefreq` varchar(255) DEFAULT NULL COMMENT '转换频率',
  `remarks` varchar(255) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for seo_website
-- ----------------------------
DROP TABLE IF EXISTS `seo_website`;
CREATE TABLE `seo_website` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `website_url` varchar(255) DEFAULT NULL COMMENT '网站域名',
  `start_time` datetime DEFAULT NULL COMMENT '域名注册时间',
  `end_time` datetime DEFAULT NULL COMMENT '域名到期时间',
  `status_time` datetime DEFAULT NULL COMMENT '域名备案审核时间',
  `record_num` varchar(255) DEFAULT NULL COMMENT '备案号',
  `nature` varchar(255) DEFAULT NULL COMMENT '备案性质',
  `name` varchar(255) DEFAULT NULL COMMENT '备案名称',
  `create_time` datetime DEFAULT NULL COMMENT '网站添加时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `website_url` (`website_url`),
  KEY `status_time` (`status_time`),
  KEY `record_num` (`record_num`)
) ENGINE=MyISAM AUTO_INCREMENT=5159413 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for seo_website_info
-- ----------------------------
DROP TABLE IF EXISTS `seo_website_info`;
CREATE TABLE `seo_website_info` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `website_url` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `baidu_include` varchar(10) DEFAULT '0' COMMENT '收录',
  `jj` tinyint(1) DEFAULT '0' COMMENT '是否竞价网站',
  `pic_addr` varchar(128) DEFAULT '0' COMMENT '图片地址',
  `fenlei_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `website_url` (`website_url`),
  KEY `fenlei` (`fenlei_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3437655 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sph_counter
-- ----------------------------
DROP TABLE IF EXISTS `sph_counter`;
CREATE TABLE `sph_counter` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `max_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`c_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for system
-- ----------------------------
DROP TABLE IF EXISTS `system`;
CREATE TABLE `system` (
  `id` int(36) unsigned NOT NULL,
  `name` char(36) NOT NULL DEFAULT '' COMMENT '网站名称',
  `domain` varchar(36) NOT NULL DEFAULT '' COMMENT '网址',
  `title` varchar(200) NOT NULL COMMENT '标题',
  `key` varchar(200) NOT NULL COMMENT '关键字',
  `des` varchar(200) NOT NULL COMMENT '描述',
  `bah` varchar(50) DEFAULT NULL COMMENT '备案号',
  `copyright` varchar(30) DEFAULT NULL COMMENT 'copyright',
  `ads` varchar(120) DEFAULT NULL COMMENT '公司地址',
  `tel` varchar(15) DEFAULT NULL COMMENT '公司电话',
  `email` varchar(50) DEFAULT NULL COMMENT '公司邮箱',
  `logo` varchar(120) DEFAULT NULL COMMENT 'logo',
  `mobile` varchar(10) DEFAULT 'open' COMMENT '是否开启手机端 open 开启 close 关闭',
  `code` varchar(10) DEFAULT 'close' COMMENT '是否开启验证码',
  `cnzz` text COMMENT '统计代码',
  `permission` varchar(255) DEFAULT NULL COMMENT '登录地区限制',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for tags
-- ----------------------------
DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT '' COMMENT '标签名称',
  `nums` int(11) DEFAULT '0' COMMENT '适配数',
  `hits` int(11) DEFAULT '0' COMMENT '点击次数',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for team
-- ----------------------------
DROP TABLE IF EXISTS `team`;
CREATE TABLE `team` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL DEFAULT '',
  `title_style` varchar(225) NOT NULL DEFAULT '',
  `thumb` varchar(225) NOT NULL DEFAULT '',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `userid` int(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(40) NOT NULL DEFAULT '',
  `sort` int(10) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `info` text NOT NULL,
  `template` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for user_level
-- ----------------------------
DROP TABLE IF EXISTS `user_level`;
CREATE TABLE `user_level` (
  `level_id` smallint(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `level_name` varchar(30) DEFAULT NULL COMMENT '头衔名称',
  `sort` int(3) DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `bomlimit` int(5) DEFAULT '0' COMMENT '积分下限',
  `toplimit` int(5) DEFAULT '0' COMMENT '积分上限',
  `price` decimal(11,2) unsigned NOT NULL COMMENT '价格',
  `description` varchar(200) NOT NULL COMMENT '权限描述',
  `selectnum` int(11) unsigned DEFAULT '0' COMMENT '查询数量',
  `downnum` int(11) unsigned DEFAULT '0' COMMENT '下载数量',
  `keywordnum` tinyint(1) unsigned DEFAULT '0' COMMENT '关键词指数',
  `keywordretrieval` tinyint(1) unsigned DEFAULT '0' COMMENT '关键词检索量',
  `batchselect` int(11) unsigned DEFAULT '0' COMMENT '批量查询次数',
  `batchselectnum` int(10) unsigned DEFAULT '0' COMMENT '批量查询条数',
  `notoriginal` tinyint(1) unsigned DEFAULT '0' COMMENT '伪原创查询',
  PRIMARY KEY (`level_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `yaoqing_id` int(10) unsigned DEFAULT '0' COMMENT '邀请用户id',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号码',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '注册时间',
  `avatar` varchar(255) DEFAULT NULL COMMENT '头像',
  `point` int(11) unsigned DEFAULT '10' COMMENT '点数(积分)',
  `group` tinyint(1) unsigned DEFAULT '1' COMMENT '会员等级虚',
  `level` tinyint(1) unsigned DEFAULT '1' COMMENT '会员等级实',
  `opentime` int(10) unsigned DEFAULT NULL COMMENT '开通时间',
  `endtime` int(10) unsigned DEFAULT NULL COMMENT '到期时间',
  `is_lock` tinyint(1) unsigned DEFAULT '1' COMMENT '是否被锁定冻结',
  `money` decimal(11,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '金额',
  `email` varchar(60) NOT NULL DEFAULT '' COMMENT '邮箱账号',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0未知 1 男 2 女',
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '微信头像',
  `paypwd` varchar(32) DEFAULT NULL COMMENT '支付密码',
  `birthday` int(11) NOT NULL DEFAULT '0' COMMENT '生日',
  `last_login` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(15) NOT NULL DEFAULT '' COMMENT '最后登录ip',
  `qq` varchar(20) NOT NULL DEFAULT '' COMMENT 'QQ',
  `mobile_validated` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否验证手机',
  `oauth` varchar(10) DEFAULT '' COMMENT '第三方来源 wx weibo alipay',
  `openid` varchar(100) DEFAULT NULL COMMENT '第三方唯一标示',
  `unionid` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT '0' COMMENT '省份',
  `city` varchar(100) DEFAULT '0' COMMENT '市区',
  `district` int(6) DEFAULT '0' COMMENT '县',
  `country` varchar(255) DEFAULT NULL COMMENT '国家',
  `language` varchar(255) DEFAULT NULL COMMENT '语言',
  `email_validated` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否验证电子邮箱',
  `username` varchar(50) DEFAULT NULL COMMENT '第三方返回昵称',
  `token` varchar(64) DEFAULT '' COMMENT '用于app 授权类似于session_id',
  `sign` varchar(255) DEFAULT '' COMMENT '签名',
  `status` varchar(20) DEFAULT 'hide' COMMENT '登录状态',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `email` (`email`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1722 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for users_group
-- ----------------------------
DROP TABLE IF EXISTS `users_group`;
CREATE TABLE `users_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '会员组id',
  `title` char(100) NOT NULL DEFAULT '' COMMENT '标题',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态',
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='管理员分组';

-- ----------------------------
-- Table structure for users_num
-- ----------------------------
DROP TABLE IF EXISTS `users_num`;
CREATE TABLE `users_num` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(10) unsigned NOT NULL COMMENT '用户id',
  `keyword_querynum` int(10) unsigned DEFAULT '0' COMMENT '关键词查询次数',
  `keyword_exportnum` int(10) unsigned DEFAULT '0' COMMENT '关键词导出次数',
  `keyword_plquerynum` int(10) unsigned DEFAULT '0' COMMENT '关键词批量查询次数',
  `qz_querynum` int(10) unsigned DEFAULT '0' COMMENT '词频查询次数',
  `beian_querynum` int(10) unsigned DEFAULT '0' COMMENT '备案单个查询次数',
  `beian_plquerynum` int(10) unsigned DEFAULT '0' COMMENT '备案批量查询次数',
  `beian_exportnum` int(10) unsigned DEFAULT '0' COMMENT '备案导出次数',
  `includ_querynum` int(10) unsigned DEFAULT '0' COMMENT '收录查询次数 ',
  `includ_exportnum` int(10) unsigned DEFAULT '0' COMMENT '收录导出次数',
  `rank_querynum` int(10) unsigned DEFAULT '0' COMMENT '排名查询次数',
  `rank_plquerynum` int(10) unsigned DEFAULT '0' COMMENT '排名批量查询次数',
  `rank_exportnum` int(10) unsigned DEFAULT '0' COMMENT '排名导出次数',
  `wyc_querynum` int(10) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1695 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for users_rule
-- ----------------------------
DROP TABLE IF EXISTS `users_rule`;
CREATE TABLE `users_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '表id',
  `groupid` int(11) DEFAULT NULL COMMENT '会员组id',
  `sort` int(3) DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '是否显示',
  `price` decimal(11,2) unsigned DEFAULT NULL COMMENT '价格',
  `description` varchar(200) DEFAULT NULL COMMENT '权限描述',
  `keyword_querynum` int(10) unsigned DEFAULT '0' COMMENT '关键词查询次数',
  `keyword_exportnum` int(10) unsigned DEFAULT '0' COMMENT '关键词导出次数',
  `keyword_shownum` int(10) unsigned DEFAULT '0' COMMENT '关键词展示条数',
  `keyword_plquerynum` int(10) unsigned DEFAULT '0' COMMENT '关键词批量查询次数',
  `keyword_plsubmit` int(11) unsigned DEFAULT '50' COMMENT '关键词批量查询提交数量',
  `qz_querynum` int(10) unsigned DEFAULT '0' COMMENT '词频查询次数',
  `beian_querynum` int(10) unsigned DEFAULT '0' COMMENT '备案单个查询次数',
  `beian_plquerynum` int(10) unsigned DEFAULT '0' COMMENT '备案批量查询次数',
  `beian_exportnum` int(10) unsigned DEFAULT '0' COMMENT '备案导出次数',
  `beian_plsubmit` int(10) unsigned DEFAULT '50' COMMENT '备案批量查询提交数量',
  `includ_querynum` int(10) unsigned DEFAULT '0' COMMENT '收录查询次数 ',
  `includ_plsubmit` int(10) unsigned DEFAULT '50' COMMENT '收录提交查询个数',
  `includ_exportnum` int(10) unsigned DEFAULT '0' COMMENT '收录导出次数',
  `webmonitorl_urlnum` int(10) unsigned DEFAULT '0' COMMENT '网站监控网址数量',
  `webmonitor_keynum` int(10) unsigned DEFAULT '0' COMMENT '网站监控关键词数量',
  `rank_querynum` int(10) unsigned DEFAULT '0' COMMENT '排名查询次数',
  `rank_plquerynum` int(10) unsigned DEFAULT '0' COMMENT '排名批量查询次数',
  `rank_plsubmit` int(10) unsigned DEFAULT '50' COMMENT '排名批量查询提交数量',
  `rank_exportnum` int(10) unsigned DEFAULT '0' COMMENT '排名导出次数',
  `order_beian` int(1) unsigned DEFAULT '0' COMMENT '备案排序，1:有，0：无',
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '添加时间',
  `order_includ` int(1) unsigned DEFAULT '0' COMMENT '收录排序，1:有，0：无',
  `order_relate` int(1) unsigned DEFAULT '0' COMMENT '相关词排序，1:有，0：无',
  `order_keydig` int(1) unsigned DEFAULT '0' COMMENT '关键词挖掘排序，1：有，0：无',
  `order_rank` int(1) unsigned DEFAULT '0' COMMENT '排名排序，1：有，0：无',
  `wyc_num` int(10) DEFAULT '1000' COMMENT '伪原创字数限制',
  `wyc_times` int(10) DEFAULT '0',
  `beian_maxpage` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wx_auth
-- ----------------------------
DROP TABLE IF EXISTS `wx_auth`;
CREATE TABLE `wx_auth` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `instance_id` int(11) NOT NULL DEFAULT '0' COMMENT '店铺id',
  `authorizer_appid` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺的appid  授权之后不用刷新',
  `authorizer_refresh_token` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺授权之后的刷新token，每月刷新',
  `authorizer_access_token` varchar(255) NOT NULL DEFAULT '' COMMENT '店铺的公众号token，只有2小时',
  `func_info` varchar(1000) NOT NULL DEFAULT '' COMMENT '授权项目',
  `nick_name` varchar(50) NOT NULL DEFAULT '' COMMENT '公众号昵称',
  `head_img` varchar(255) NOT NULL DEFAULT '' COMMENT '公众号头像url',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '公众号原始账号',
  `alias` varchar(255) NOT NULL DEFAULT '' COMMENT '公众号原始名称',
  `qrcode_url` varchar(255) NOT NULL DEFAULT '' COMMENT '公众号二维码url',
  `auth_time` int(11) DEFAULT '0' COMMENT '授权时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=8192 ROW_FORMAT=COMPACT COMMENT='店铺(实例)微信公众账号授权';

-- ----------------------------
-- Table structure for wx_default_replay
-- ----------------------------
DROP TABLE IF EXISTS `wx_default_replay`;
CREATE TABLE `wx_default_replay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `instance_id` int(11) NOT NULL COMMENT '店铺id',
  `reply_media_id` int(11) NOT NULL COMMENT '回复媒体内容id',
  `sort` int(11) NOT NULL,
  `create_time` int(11) DEFAULT '0',
  `modify_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=16384 ROW_FORMAT=COMPACT COMMENT='关注时回复';

-- ----------------------------
-- Table structure for wx_fans
-- ----------------------------
DROP TABLE IF EXISTS `wx_fans`;
CREATE TABLE `wx_fans` (
  `fans_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '粉丝ID',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '会员编号ID',
  `source_uid` int(11) NOT NULL DEFAULT '0' COMMENT '推广人uid',
  `instance_id` int(11) NOT NULL COMMENT '店铺ID',
  `nickname` varchar(255) NOT NULL COMMENT '昵称',
  `nickname_decode` varchar(255) DEFAULT '',
  `headimgurl` varchar(500) NOT NULL DEFAULT '' COMMENT '头像',
  `sex` smallint(6) NOT NULL DEFAULT '1' COMMENT '性别',
  `language` varchar(20) NOT NULL DEFAULT '' COMMENT '用户语言',
  `country` varchar(60) NOT NULL DEFAULT '' COMMENT '国家',
  `province` varchar(255) NOT NULL DEFAULT '' COMMENT '省',
  `city` varchar(255) NOT NULL DEFAULT '' COMMENT '城市',
  `district` varchar(255) NOT NULL DEFAULT '' COMMENT '行政区/县',
  `openid` varchar(255) NOT NULL DEFAULT '' COMMENT '用户的标识，对当前公众号唯一     用户的唯一身份ID',
  `unionid` varchar(255) NOT NULL DEFAULT '' COMMENT '粉丝unionid',
  `groupid` int(11) NOT NULL DEFAULT '0' COMMENT '粉丝所在组id',
  `is_subscribe` bigint(1) NOT NULL DEFAULT '1' COMMENT '是否订阅',
  `memo` varchar(255) NOT NULL COMMENT '备注',
  `subscribe_date` int(11) DEFAULT '0' COMMENT '订阅时间',
  `unsubscribe_date` int(11) DEFAULT '0' COMMENT '解订阅时间',
  `update_date` int(11) DEFAULT '0' COMMENT '粉丝信息最后更新时间',
  PRIMARY KEY (`fans_id`) USING BTREE,
  KEY `IDX_sys_weixin_fans_openid` (`openid`) USING BTREE,
  KEY `IDX_sys_weixin_fans_unionid` (`unionid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1638 ROW_FORMAT=COMPACT COMMENT='微信公众号获取粉丝列表';

-- ----------------------------
-- Table structure for wx_follow_replay
-- ----------------------------
DROP TABLE IF EXISTS `wx_follow_replay`;
CREATE TABLE `wx_follow_replay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `instance_id` int(11) NOT NULL COMMENT '店铺id',
  `reply_media_id` int(11) NOT NULL COMMENT '回复媒体内容id',
  `sort` int(11) NOT NULL,
  `create_time` int(11) DEFAULT '0',
  `modify_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=16384 ROW_FORMAT=COMPACT COMMENT='关注时回复';

-- ----------------------------
-- Table structure for wx_key_replay
-- ----------------------------
DROP TABLE IF EXISTS `wx_key_replay`;
CREATE TABLE `wx_key_replay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `instance_id` int(11) NOT NULL COMMENT '店铺id',
  `key` varchar(255) NOT NULL COMMENT '关键词',
  `match_type` tinyint(4) NOT NULL COMMENT '匹配类型1模糊匹配2全部匹配',
  `reply_media_id` int(11) NOT NULL COMMENT '回复媒体内容id',
  `sort` int(11) NOT NULL,
  `create_time` int(11) DEFAULT '0',
  `modify_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=16384 ROW_FORMAT=COMPACT COMMENT='关键词回复';

-- ----------------------------
-- Table structure for wx_media
-- ----------------------------
DROP TABLE IF EXISTS `wx_media`;
CREATE TABLE `wx_media` (
  `media_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '图文消息id',
  `title` varchar(100) DEFAULT NULL,
  `instance_id` int(11) NOT NULL DEFAULT '0' COMMENT '实例id店铺id',
  `type` varchar(255) NOT NULL DEFAULT '1' COMMENT '类型1文本(项表无内容) 2单图文 3多图文',
  `sort` int(11) NOT NULL DEFAULT '0',
  `create_time` int(11) DEFAULT '0' COMMENT '创建日期',
  `modify_time` int(11) DEFAULT '0' COMMENT '修改日期',
  PRIMARY KEY (`media_id`) USING BTREE,
  UNIQUE KEY `id` (`media_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1170 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for wx_media_item
-- ----------------------------
DROP TABLE IF EXISTS `wx_media_item`;
CREATE TABLE `wx_media_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `media_id` int(11) NOT NULL COMMENT '图文消息id',
  `title` varchar(100) DEFAULT NULL,
  `author` varchar(50) NOT NULL COMMENT '作者',
  `cover` varchar(200) NOT NULL COMMENT '图文消息封面',
  `show_cover_pic` tinyint(4) NOT NULL DEFAULT '1' COMMENT '封面图片显示在正文中',
  `summary` text,
  `content` text NOT NULL COMMENT '正文',
  `content_source_url` varchar(200) NOT NULL DEFAULT '' COMMENT '图文消息的原文地址，即点击“阅读原文”后的URL',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序号',
  `hits` int(11) NOT NULL DEFAULT '0' COMMENT '阅读次数',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `id` (`media_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=712 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for wx_menu
-- ----------------------------
DROP TABLE IF EXISTS `wx_menu`;
CREATE TABLE `wx_menu` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `instance_id` int(11) NOT NULL DEFAULT '0' COMMENT '店铺id',
  `menu_name` varchar(50) NOT NULL DEFAULT '' COMMENT '菜单名称',
  `ico` varchar(32) NOT NULL DEFAULT '' COMMENT '菜图标单',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父菜单',
  `menu_event_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1普通url 2 图文素材 3 功能',
  `media_id` int(11) NOT NULL DEFAULT '0' COMMENT '图文消息ID',
  `menu_event_url` varchar(255) NOT NULL DEFAULT '' COMMENT '菜单url',
  `hits` int(11) NOT NULL DEFAULT '0' COMMENT '触发数',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `create_date` int(11) DEFAULT '0' COMMENT '创建日期',
  `modify_date` int(11) DEFAULT '0' COMMENT '修改日期',
  PRIMARY KEY (`menu_id`) USING BTREE,
  KEY `IDX_biz_shop_menu_orders` (`sort`) USING BTREE,
  KEY `IDX_biz_shop_menu_shopId` (`instance_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=1638 ROW_FORMAT=COMPACT COMMENT='微设置->微店菜单';

-- ----------------------------
-- Table structure for wx_mp
-- ----------------------------
DROP TABLE IF EXISTS `wx_mp`;
CREATE TABLE `wx_mp` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `instance_id` int(11) NOT NULL DEFAULT '1' COMMENT '实例ID',
  `key` varchar(255) NOT NULL DEFAULT '' COMMENT '配置项WCHAT,QQ,WPAY,ALIPAY...',
  `value` varchar(1000) NOT NULL DEFAULT '' COMMENT '配置值json',
  `desc` varchar(1000) NOT NULL DEFAULT '' COMMENT '描述',
  `is_use` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用 1启用 0不启用',
  `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
  `modify_time` int(11) DEFAULT '0' COMMENT '修改时间',
  `qrcode` varchar(120) DEFAULT '' COMMENT '二维码',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='第三方配置表';

-- ----------------------------
-- Table structure for wx_user
-- ----------------------------
DROP TABLE IF EXISTS `wx_user`;
CREATE TABLE `wx_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '表id',
  `uid` int(11) NOT NULL COMMENT 'uid',
  `wxname` varchar(60) NOT NULL COMMENT 'nicheng',
  `openid` varchar(255) DEFAULT NULL COMMENT '微信openid',
  `gender` tinyint(1) DEFAULT NULL COMMENT '性别1男2女',
  `language` varchar(255) DEFAULT NULL COMMENT '语言',
  `city` varchar(255) DEFAULT NULL COMMENT '城市',
  `province` varchar(255) DEFAULT NULL COMMENT '省',
  `country` varchar(255) DEFAULT NULL COMMENT '国家',
  `headimgurl` varchar(255) DEFAULT NULL COMMENT '微信头像',
  `privilege` varchar(255) DEFAULT NULL,
  `unionid` varchar(255) DEFAULT NULL COMMENT '微信标识',
  `aeskey` varchar(256) NOT NULL DEFAULT '' COMMENT 'aeskey',
  `encode` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'encode',
  `appid` varchar(50) NOT NULL DEFAULT '' COMMENT 'appid',
  `appsecret` varchar(50) NOT NULL DEFAULT '' COMMENT 'appsecret',
  `wxid` varchar(64) NOT NULL COMMENT '公众号原始ID',
  `weixin` char(64) NOT NULL COMMENT '微信号',
  `token` char(255) NOT NULL COMMENT 'token',
  `w_token` varchar(150) NOT NULL DEFAULT '' COMMENT '微信对接token',
  `create_time` int(11) NOT NULL COMMENT 'create_time',
  `updatetime` int(11) NOT NULL COMMENT 'updatetime',
  `tplcontentid` varchar(2) NOT NULL COMMENT '内容模版ID',
  `share_ticket` varchar(150) NOT NULL COMMENT '分享ticket',
  `share_dated` char(15) NOT NULL COMMENT 'share_dated',
  `authorizer_access_token` varchar(200) NOT NULL COMMENT 'authorizer_access_token',
  `authorizer_refresh_token` varchar(200) NOT NULL COMMENT 'authorizer_refresh_token',
  `authorizer_expires` char(10) NOT NULL COMMENT 'authorizer_expires',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型',
  `web_access_token` varchar(200) NOT NULL COMMENT '网页授权token',
  `web_refresh_token` varchar(200) NOT NULL COMMENT 'web_refresh_token',
  `web_expires` int(11) NOT NULL COMMENT '过期时间',
  `menu_config` text COMMENT '菜单',
  `wait_access` tinyint(1) DEFAULT '0' COMMENT '微信接入状态,0待接入1已接入',
  `concern` varchar(225) DEFAULT '' COMMENT '关注回复',
  `default` varchar(225) DEFAULT '' COMMENT '默认回复',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `uid_2` (`uid`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='微信公共帐号';

-- ----------------------------
-- Table structure for wx_user_msg
-- ----------------------------
DROP TABLE IF EXISTS `wx_user_msg`;
CREATE TABLE `wx_user_msg` (
  `msg_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `msg_type` varchar(255) NOT NULL,
  `content` text,
  `is_replay` int(11) NOT NULL DEFAULT '0' COMMENT '是否回复',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`msg_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='微信用户消息表';

-- ----------------------------
-- Table structure for wx_user_msg_replay
-- ----------------------------
DROP TABLE IF EXISTS `wx_user_msg_replay`;
CREATE TABLE `wx_user_msg_replay` (
  `replay_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `msg_id` int(11) NOT NULL,
  `replay_uid` int(11) NOT NULL COMMENT '当前客服uid',
  `replay_type` varchar(255) NOT NULL,
  `content` text,
  `replay_time` int(11) DEFAULT '0',
  PRIMARY KEY (`replay_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='微信用户消息回复表';
