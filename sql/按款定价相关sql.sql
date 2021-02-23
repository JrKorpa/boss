/*
Navicat MySQL Data Transfer

Source Server         : 192.168.0.131
Source Server Version : 50626
Source Host           : 192.168.0.131:3306
Source Database       : front

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2016-01-27 16:59:19
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for app_attribute_ext
-- ----------------------------
DROP TABLE IF EXISTS `app_attribute_ext`;
CREATE TABLE `app_attribute_ext` (
  `attribute_id` int(10) unsigned NOT NULL COMMENT '属性ID',
  `attr_show_name` varchar(60) DEFAULT NULL COMMENT '前端显示名称',
  `is_diamond_attr` tinyint(1) unsigned DEFAULT '0' COMMENT '是否是钻石属性 1是，0否  默认值 0',
  `require_confirm` tinyint(1) unsigned DEFAULT '0' COMMENT '参与价格计算(需用户确认)，1是，0否  默认0',
  `attribute_unit` varchar(25) DEFAULT NULL COMMENT '属性单位',
  `update_time`  timestamp NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `app_goodsprice_by_style`;
CREATE TABLE `app_goodsprice_by_style` (
  `id` varchar(60) NOT NULL,
  `style_sn` varchar(30) NOT NULL COMMENT '款号style_sn',
  `attr_select` text COMMENT '选中的属性（json格式）',
  `attr_keys` varchar(512) NOT NULL COMMENT '参与价格计算的属性组合keys',
  `attr_data` varchar(2048) DEFAULT NULL COMMENT '参与价格计算的属性组合',
  `market_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '市场价格',
  `kela_price` decimal(10,2) unsigned DEFAULT NULL COMMENT 'BDD销售价格',
  `goods_type` tinyint(3) unsigned DEFAULT NULL COMMENT '商品类型 1现货 2 期货 3 起版',
  `goods_stock` int(8) unsigned DEFAULT '1' COMMENT '商品库存',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '商品状态：1上架，0下架',
  `create_time` datetime DEFAULT NULL COMMENT '添加时间',
  `modify_time` datetime DEFAULT NULL COMMENT '更新时间',
  `update_time`  timestamp NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '数据变更时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='款式商品定价';

-- ----------------------------
-- Table structure for app_goodsprice_salepolicy
-- ----------------------------
DROP TABLE IF EXISTS `app_goodsprice_salepolicy`;
CREATE TABLE `app_goodsprice_salepolicy` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `style_sn` varchar(30) NOT NULL COMMENT '款号',
  `channel_id` int(8) unsigned NOT NULL COMMENT '销售渠道',
  `jiajialv` decimal(8,2) DEFAULT NULL COMMENT '加价率',
  `sta_value` decimal(8,2) unsigned DEFAULT '0.00' COMMENT '固定值',
  `create_time` datetime DEFAULT NULL,
  `update_time`  timestamp NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


ALTER TABLE `base_style_info`
ADD COLUMN `is_allow_favorable`  tinyint(1) UNSIGNED NOT NULL COMMENT '是否允许改价',
ADD COLUMN `is_gold`  tinyint UNSIGNED NOT NULL COMMENT '是否是黄金 0:非黄金，1:瑞金 2:3D  3:一口价' AFTER `is_allow_favorable`,
ADD COLUMN `is_support_style`  tinyint UNSIGNED NULL COMMENT '是否支持按款销售' AFTER `is_gold`;


ALTER TABLE `app_attribute`
ADD COLUMN `update_time`  timestamp NULL ON UPDATE CURRENT_TIMESTAMP AFTER `attribute_remark`;
UPDATE app_attribute SET update_time = create_time;
UPDATE app_attribute_ext ae SET update_time = (SELECT create_time FROM app_attribute a WHERE a.attribute_id = ae.attribute_id);

ALTER TABLE `app_attribute_value`
ADD COLUMN `update_time`  timestamp NULL ON UPDATE CURRENT_TIMESTAMP AFTER `att_value_remark`;
UPDATE app_attribute_value SET update_time = create_time;

ALTER TABLE `app_cat_type`
ADD COLUMN `update_time`  timestamp NULL ON UPDATE CURRENT_TIMESTAMP AFTER `is_system`;
UPDATE app_cat_type SET update_time = '2016-01-01 00:00:00';

ALTER TABLE `rel_style_attribute`
ADD COLUMN `update_time`  timestamp NULL ON UPDATE CURRENT_TIMESTAMP AFTER `is_price_conbined`;
UPDATE rel_style_attribute SET update_time = create_time;

ALTER TABLE `base_salepolicy_info`
ADD COLUMN `update_time`  timestamp NULL ON UPDATE CURRENT_TIMESTAMP AFTER `is_kuanprice`;
UPDATE base_salepolicy_info SET update_time = create_time;

ALTER TABLE `warehouse_goods`
ADD COLUMN `update_time`  timestamp NULL ON UPDATE CURRENT_TIMESTAMP AFTER `companyA_id`;
UPDATE warehouse_goods SET update_time = addtime;