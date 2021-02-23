/*
Navicat MySQL Data Transfer

Source Server         : mysql_192.168.0.95_3306
Source Server Version : 50626
Source Host           : 192.168.0.95:3306
Source Database       : repair_order

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2019-09-09 10:24:00
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for app_order_weixiu
-- ----------------------------
DROP TABLE IF EXISTS `app_order_weixiu`;
CREATE TABLE `app_order_weixiu` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(20) NOT NULL,
  `order_sn` varchar(20) NOT NULL,
  `rec_id` varchar(20) NOT NULL COMMENT '布产号',
  `re_type` int(10) NOT NULL DEFAULT '1' COMMENT '维修单类型',
  `old_goods_id` varchar(20) NOT NULL COMMENT '原货号',
  `goods_id` varchar(20) NOT NULL COMMENT '货号',
  `consignee` varchar(20) NOT NULL COMMENT '客户姓名',
  `repair_act` varchar(60) NOT NULL COMMENT '维修动作',
  `repair_man` int(10) NOT NULL COMMENT '跟单人id',
  `repair_factory` int(10) NOT NULL DEFAULT '0' COMMENT '工厂',
  `repair_make_order` varchar(20) NOT NULL COMMENT '维修制单人',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  `status` int(10) NOT NULL COMMENT '状态',
  `order_time` datetime NOT NULL COMMENT '制单时间',
  `confirm_time` datetime DEFAULT NULL COMMENT '确认时间',
  `factory_time` datetime DEFAULT NULL COMMENT '下单时间',
  `end_time` datetime DEFAULT NULL COMMENT '预计出厂时间',
  `re_end_time` datetime DEFAULT NULL COMMENT '完成时间',
  `receiving_time` datetime DEFAULT NULL COMMENT '收货时间',
  `frequency` int(10) NOT NULL DEFAULT '1' COMMENT '维修次数 默认1次',
  `after_sale` tinyint(2) DEFAULT '0' COMMENT '是否是售后维修 0不是，1是',
  `change_sn` varchar(20) DEFAULT '' COMMENT '转仓单号',
  `qc_status` tinyint(1) DEFAULT '3' COMMENT '质检状态，3:未质检，1：质检通过，2：质检未过',
  `qc_times` smallint(4) DEFAULT '0' COMMENT '质检次数',
  `qc_nopass_dt` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '最新质检未通过时间',
  `order_class` tinyint(4) DEFAULT '0',
  `weixiu_price` decimal(8,2) DEFAULT NULL COMMENT '维修费用',
  `hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏栏位',
  PRIMARY KEY (`id`),
  FULLTEXT KEY `order_sn` (`order_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=198 DEFAULT CHARSET=utf8 COMMENT='订单维修表';

-- ----------------------------
-- Table structure for app_order_weixiu_log
-- ----------------------------
DROP TABLE IF EXISTS `app_order_weixiu_log`;
CREATE TABLE `app_order_weixiu_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `do_id` int(10) unsigned NOT NULL COMMENT '操作id',
  `date_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作时间',
  `user_name` varchar(20) DEFAULT NULL COMMENT '操作人',
  `do_type` varchar(30) DEFAULT NULL COMMENT '操作类型',
  `content` varchar(100) DEFAULT NULL COMMENT '操作内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=491 DEFAULT CHARSET=utf8 COMMENT='维修日志表';
