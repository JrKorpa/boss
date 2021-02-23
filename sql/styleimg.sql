/*
Navicat MySQL Data Transfer

Source Server         : mysql_192.168.0.95_3306
Source Server Version : 50626
Source Host           : 192.168.0.95:3306
Source Database       : styleimg

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2019-09-09 10:24:31
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for app_style_gallery
-- ----------------------------
DROP TABLE IF EXISTS `app_style_gallery`;
CREATE TABLE `app_style_gallery` (
  `g_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `style_id` int(10) unsigned NOT NULL COMMENT '款式id',
  `style_sn` varchar(30) DEFAULT NULL,
  `image_place` tinyint(3) unsigned NOT NULL COMMENT '图片位置，100=网络上架，6=表现工艺，5=证书图,1=正立45°图,2=正立图,3=爪头图,4=爪尾图,8=内臂图,7=质检专用图',
  `img_sort` int(10) unsigned NOT NULL COMMENT '图片排序',
  `img_ori` varchar(100) NOT NULL COMMENT '原图路径',
  `thumb_img` varchar(100) NOT NULL COMMENT '缩略图',
  `middle_img` varchar(100) NOT NULL COMMENT '中图',
  `big_img` varchar(100) NOT NULL COMMENT '大图',
  `new_gid` int(11) DEFAULT NULL COMMENT '新系统相册自增id',
  PRIMARY KEY (`g_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_style_gallery150803
-- ----------------------------
DROP TABLE IF EXISTS `app_style_gallery150803`;
CREATE TABLE `app_style_gallery150803` (
  `g_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `style_id` int(10) unsigned NOT NULL COMMENT '款式id',
  `style_sn` varchar(30) DEFAULT NULL,
  `image_place` tinyint(3) unsigned NOT NULL COMMENT '图片位置，100=网络上架，6=表现工艺，5=证书图,1=正立45°图,2=正立图,3=爪头图,4=爪尾图,8=内臂图,7=质检专用图',
  `img_sort` int(10) unsigned NOT NULL COMMENT '图片排序',
  `img_ori` varchar(100) NOT NULL COMMENT '原图路径',
  `thumb_img` varchar(100) NOT NULL COMMENT '缩略图',
  `middle_img` varchar(100) NOT NULL COMMENT '中图',
  `big_img` varchar(100) NOT NULL COMMENT '大图',
  `new_gid` int(11) DEFAULT NULL COMMENT '新系统相册自增id',
  PRIMARY KEY (`g_id`)
) ENGINE=MyISAM AUTO_INCREMENT=82645 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for bangding_order_goods
-- ----------------------------
DROP TABLE IF EXISTS `bangding_order_goods`;
CREATE TABLE `bangding_order_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL COMMENT '订单号',
  `goods_id` varchar(20) NOT NULL COMMENT '货号',
  `wearhouse` varchar(20) NOT NULL COMMENT '库房',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='绑定订单商品';

-- ----------------------------
-- Table structure for ecs_sessions
-- ----------------------------
DROP TABLE IF EXISTS `ecs_sessions`;
CREATE TABLE `ecs_sessions` (
  `sesskey` varchar(32) NOT NULL DEFAULT '',
  `expiry` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `adminid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `data` longtext NOT NULL,
  PRIMARY KEY (`sesskey`),
  KEY `expiry` (`expiry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
