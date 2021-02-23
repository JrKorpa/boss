/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50617
Source Host           : localhost:3306
Source Database       : cuteframe

Target Server Type    : MYSQL
Target Server Version : 50617
File Encoding         : 65001

Date: 2014-12-19 18:35:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `area`
-- ----------------------------
DROP TABLE IF EXISTS `area`;
CREATE TABLE `area` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `contury` smallint(5) unsigned NOT NULL COMMENT '国家',
  `province` smallint(5) unsigned NOT NULL COMMENT '省',
  `city` smallint(5) unsigned NOT NULL COMMENT '城市',
  `district` smallint(5) unsigned NOT NULL COMMENT '地区',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '会员区域',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='会员区域表';


