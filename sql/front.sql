/*
Navicat MySQL Data Transfer

Source Server         : mysql_192.168.0.95_3306
Source Server Version : 50626
Source Host           : 192.168.0.95:3306
Source Database       : front

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2019-09-09 10:22:31
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for app_apply_chaihuo
-- ----------------------------
DROP TABLE IF EXISTS `app_apply_chaihuo`;
CREATE TABLE `app_apply_chaihuo` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `style_id` int(10) unsigned NOT NULL COMMENT '款式自增ID',
  `create_user` varchar(20) NOT NULL COMMENT '制单人',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `check_user` varchar(20) NOT NULL COMMENT '审核人名字',
  `check_time` datetime NOT NULL COMMENT '拆货时间',
  `status` datetime NOT NULL COMMENT '状态 1申请2申请成功3申请失败',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_attribute
-- ----------------------------
DROP TABLE IF EXISTS `app_attribute`;
CREATE TABLE `app_attribute` (
  `attribute_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性id',
  `attribute_name` varchar(20) NOT NULL COMMENT '属性名称',
  `attribute_code` varchar(30) NOT NULL COMMENT '属性编码',
  `show_type` tinyint(1) NOT NULL COMMENT '展示方式：1文本框，2单选，3多选，4下拉',
  `attribute_status` tinyint(2) NOT NULL COMMENT '状态:1启用;0停用',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `create_user` varchar(10) NOT NULL COMMENT '创建人',
  `attribute_remark` varchar(50) DEFAULT NULL COMMENT '记录备注',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`attribute_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_attribute_ext
-- ----------------------------
DROP TABLE IF EXISTS `app_attribute_ext`;
CREATE TABLE `app_attribute_ext` (
  `attribute_id` int(10) unsigned NOT NULL COMMENT '属性ID',
  `attr_show_name` varchar(60) DEFAULT NULL COMMENT '前端显示名称',
  `is_diamond_attr` tinyint(1) unsigned DEFAULT '0' COMMENT '是否是钻石属性 1是，0否  默认值 0',
  `is_price_conbined` tinyint(1) unsigned DEFAULT '0' COMMENT '参与价格计算，1是，0否  默认0',
  `require_confirm` tinyint(1) unsigned DEFAULT '0' COMMENT '参与价格计算(需用户确认)，1是，0否  默认0',
  `attribute_unit` varchar(25) DEFAULT NULL COMMENT '属性单位',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `attribute_sort` int(4) DEFAULT '1000' COMMENT '排序',
  PRIMARY KEY (`attribute_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_attribute_value
-- ----------------------------
DROP TABLE IF EXISTS `app_attribute_value`;
CREATE TABLE `app_attribute_value` (
  `att_value_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '属性值ID',
  `attribute_id` int(10) NOT NULL COMMENT '属性id',
  `att_value_name` varchar(50) NOT NULL COMMENT '属性值名称',
  `att_value_status` tinyint(2) NOT NULL COMMENT '状态:1启用;0停用',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `create_user` varchar(10) NOT NULL COMMENT '创建人',
  `att_value_remark` varchar(50) DEFAULT NULL COMMENT '记录备注',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `att_value_code` varchar(10) DEFAULT NULL COMMENT '属性值编码',
  PRIMARY KEY (`att_value_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=716 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_bespoke_action_log
-- ----------------------------
DROP TABLE IF EXISTS `app_bespoke_action_log`;
CREATE TABLE `app_bespoke_action_log` (
  `action_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `bespoke_id` int(10) unsigned NOT NULL COMMENT '预约ID',
  `create_user` varchar(20) NOT NULL COMMENT '操作人  ',
  `create_time` datetime NOT NULL COMMENT '操作时间',
  `IP` char(15) NOT NULL COMMENT '操作IP',
  `bespoke_status` tinyint(1) unsigned NOT NULL COMMENT '预约状态',
  `remark` text COMMENT '备注',
  PRIMARY KEY (`action_id`)
) ENGINE=InnoDB AUTO_INCREMENT=149217 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_bespoke_info
-- ----------------------------
DROP TABLE IF EXISTS `app_bespoke_info`;
CREATE TABLE `app_bespoke_info` (
  `bespoke_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '预约ID',
  `bespoke_sn` varchar(20) DEFAULT NULL COMMENT '预约号',
  `department_id` int(10) DEFAULT NULL COMMENT '体验店ID',
  `mem_id` int(10) NOT NULL COMMENT '顾客ID',
  `member_sn` varchar(100) DEFAULT NULL COMMENT '预约人会员编号',
  `queue_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '服务状态，1 自由状态 2 待服务 3服务中 4服务结束',
  `sales_channels_id` int(10) DEFAULT NULL COMMENT 'sales_channels.id渠道',
  `customer_source_id` int(8) NOT NULL COMMENT '客户来源',
  `customer` varchar(20) DEFAULT NULL COMMENT '客户姓名',
  `customer_mobile` char(11) DEFAULT NULL COMMENT '客户手机',
  `customer_email` varchar(50) DEFAULT NULL COMMENT '客户email',
  `customer_address` varchar(100) DEFAULT NULL COMMENT '客户地址',
  `create_time` datetime DEFAULT NULL COMMENT '预约时间',
  `bespoke_inshop_time` date DEFAULT NULL COMMENT '预约到店时间',
  `real_inshop_time` datetime DEFAULT NULL COMMENT '实际到店时间',
  `make_order` varchar(20) DEFAULT NULL COMMENT '制单人',
  `accecipt_man` varchar(20) DEFAULT NULL COMMENT '接待人',
  `bespoke_status` tinyint(1) DEFAULT '1' COMMENT '预约状态 1保存 2已经审核3作废',
  `re_status` tinyint(1) DEFAULT '2' COMMENT '到店状态： 2未到店 1已到店',
  `re_lot_code` varchar(20) DEFAULT '' COMMENT '到店抽奖码',
  `deal_status` tinyint(1) NOT NULL DEFAULT '2' COMMENT '成交状态：1成交 2未成交',
  `withuserdo` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=无回访，1=回访',
  `salesstage` tinyint(1) NOT NULL DEFAULT '0' COMMENT '销售阶段:[1,了解;2,对比;3,决定]',
  `brandimage` tinyint(1) DEFAULT '0' COMMENT '品牌印象[1,弱;2,中;3,强]',
  `remark` text COMMENT '预约备注',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '取消预约 0未取消 1已取消',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `recommender_sn` varchar(20) DEFAULT NULL COMMENT '推荐人sn',
  `recommender_name` varchar(50) DEFAULT NULL COMMENT '推荐人',
  `recommender_mobile` char(11) DEFAULT NULL COMMENT '推荐人手机号',
  `recommend_accecipt_man` varchar(50) DEFAULT NULL COMMENT '推荐接待人',
  PRIMARY KEY (`bespoke_id`),
  KEY `bespoke_sn` (`bespoke_sn`),
  KEY `department_id` (`department_id`),
  KEY `customer` (`customer`),
  KEY `customer_mobile` (`customer_mobile`),
  KEY `bespoke_inshop_time` (`bespoke_inshop_time`),
  KEY `create_time` (`create_time`),
  KEY `real_inshop_time` (`real_inshop_time`),
  KEY `update_time` (`update_time`),
  KEY `accecipt_man` (`accecipt_man`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=44055 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_cat_type
-- ----------------------------
DROP TABLE IF EXISTS `app_cat_type`;
CREATE TABLE `app_cat_type` (
  `cat_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '部门id',
  `cat_type_name` char(50) NOT NULL COMMENT '部门名称',
  `cat_type_code` char(50) DEFAULT NULL COMMENT '部门编码',
  `note` varchar(255) DEFAULT NULL COMMENT '描述',
  `parent_id` int(11) NOT NULL COMMENT '上级部门id',
  `tree_path` varchar(512) NOT NULL COMMENT '全路径',
  `pids` varchar(200) DEFAULT NULL COMMENT '祖先分类',
  `childrens` int(11) NOT NULL DEFAULT '0' COMMENT '下级分类数',
  `display_order` int(11) NOT NULL COMMENT '显示顺序',
  `cat_type_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  `jiajialv` decimal(8,2) DEFAULT '0.00',
  PRIMARY KEY (`cat_type_id`),
  KEY `cat_type_name` (`cat_type_name`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='款式分类';

-- ----------------------------
-- Table structure for app_cat_type_new
-- ----------------------------
DROP TABLE IF EXISTS `app_cat_type_new`;
CREATE TABLE `app_cat_type_new` (
  `cat_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '部门id',
  `cat_type_name` char(50) NOT NULL COMMENT '部门名称',
  `cat_type_code` char(50) DEFAULT NULL COMMENT '部门编码',
  `note` varchar(255) DEFAULT NULL COMMENT '描述',
  `parent_id` int(11) NOT NULL COMMENT '上级部门id',
  `tree_path` varchar(512) NOT NULL COMMENT '全路径',
  `pids` varchar(200) DEFAULT NULL COMMENT '祖先分类',
  `childrens` int(11) NOT NULL DEFAULT '0' COMMENT '下级分类数',
  `display_order` int(11) NOT NULL COMMENT '显示顺序',
  `cat_type_staus` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  PRIMARY KEY (`cat_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='部门/组织架构';

-- ----------------------------
-- Table structure for app_cat_type_old
-- ----------------------------
DROP TABLE IF EXISTS `app_cat_type_old`;
CREATE TABLE `app_cat_type_old` (
  `cat_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品线ID',
  `cat_type_name` varchar(10) NOT NULL COMMENT '分类名称',
  `cat_type_status` tinyint(2) NOT NULL COMMENT '状态:1启用;0停用',
  `cat_type_code` varchar(20) NOT NULL COMMENT '属性类型编码',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `create_user` varchar(10) NOT NULL COMMENT '创建人',
  `cat_type_remark` varchar(50) DEFAULT NULL COMMENT '记录备注',
  PRIMARY KEY (`cat_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_coupon_log
-- ----------------------------
DROP TABLE IF EXISTS `app_coupon_log`;
CREATE TABLE `app_coupon_log` (
  `exchange_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '兑换ID',
  `exchange_coupon` varchar(50) NOT NULL COMMENT '兑换码',
  `exchange_status` tinyint(2) NOT NULL COMMENT '兑换状态',
  `exchange_time` datetime NOT NULL COMMENT '兑换时间',
  `exchange_name` varchar(20) NOT NULL COMMENT '兑换人',
  `exchange_remark` varchar(50) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`exchange_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_coupon_policy
-- ----------------------------
DROP TABLE IF EXISTS `app_coupon_policy`;
CREATE TABLE `app_coupon_policy` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `policy_name` varchar(30) NOT NULL COMMENT '优惠券政策名称',
  `policy_desc` text NOT NULL COMMENT '优惠券政策描述',
  `policy_price` decimal(10,2) NOT NULL COMMENT '优惠金额',
  `policy_status` tinyint(1) NOT NULL COMMENT '优惠政策状态；1，保存；2，提交申请；3，作废；4，审核通过；5，审核驳回；6，过期',
  `policy_type` tinyint(2) NOT NULL COMMENT '政策类型',
  `valid_time_start` datetime NOT NULL COMMENT '有效开始时间',
  `valid_time_end` datetime NOT NULL COMMENT '有效结束时间',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `create_user` varchar(30) NOT NULL COMMENT '创建人',
  `check_user` varchar(30) DEFAULT NULL COMMENT '审核人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='优惠券政策';

-- ----------------------------
-- Table structure for app_coupon_type
-- ----------------------------
DROP TABLE IF EXISTS `app_coupon_type`;
CREATE TABLE `app_coupon_type` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `type_name` varchar(30) NOT NULL COMMENT '优惠类型名称',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='优惠类型';

-- ----------------------------
-- Table structure for app_diamond_
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_`;
CREATE TABLE `app_diamond_` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` varchar(10) NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(20) DEFAULT NULL,
  `table_lv` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `pifajia` decimal(15,3) DEFAULT '0.000',
  `pifajia_mode` varchar(10) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL COMMENT '图片',
  `is_hot` tinyint(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_bluestar
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_bluestar`;
CREATE TABLE `app_diamond_bluestar` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(10) DEFAULT NULL,
  `table_lv` varchar(10) DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_color
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_color`;
CREATE TABLE `app_diamond_color` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_sn` varchar(20) DEFAULT NULL COMMENT '货品编号',
  `shape` varchar(20) DEFAULT NULL COMMENT '形状',
  `carat` double DEFAULT NULL COMMENT '钻重',
  `color` varchar(100) DEFAULT NULL COMMENT '颜色',
  `clarity` varchar(40) DEFAULT NULL COMMENT '净度',
  `polish` varchar(40) DEFAULT NULL COMMENT '抛光',
  `symmetry` varchar(40) DEFAULT NULL COMMENT '对称性',
  `fluorescence` varchar(40) DEFAULT NULL COMMENT '荧光',
  `measurements` varchar(40) DEFAULT NULL COMMENT '尺寸',
  `cert` varchar(40) DEFAULT NULL COMMENT '证书类型',
  `cert_id` varchar(40) DEFAULT NULL COMMENT '证书号',
  `price` decimal(10,2) DEFAULT NULL COMMENT '销售价',
  `image1` varchar(220) DEFAULT NULL COMMENT '图片1',
  `image2` varchar(220) DEFAULT NULL COMMENT '图片2',
  `image3` varchar(220) DEFAULT NULL COMMENT '图片3',
  `image4` varchar(220) DEFAULT NULL COMMENT '图片4',
  `image5` varchar(220) DEFAULT NULL COMMENT '图片5',
  `from_ad` varchar(50) DEFAULT NULL COMMENT '来源',
  `add_time` timestamp NULL DEFAULT NULL COMMENT '添加时间',
  `color_grade` varchar(50) DEFAULT NULL COMMENT '颜色分级',
  `warehouse` char(10) DEFAULT NULL COMMENT '库房',
  `cost_price` decimal(10,2) DEFAULT NULL COMMENT '成本价',
  `good_type` int(2) DEFAULT NULL COMMENT '货品类型',
  `mo_sn` char(10) DEFAULT NULL COMMENT '模号',
  `status` tinyint(2) DEFAULT NULL COMMENT '状态',
  `quantity` int(10) DEFAULT NULL COMMENT '数量',
  `goods_id` char(11) DEFAULT NULL COMMENT '丽比诗货品ID',
  `price_status` char(20) DEFAULT NULL COMMENT '价格状态',
  `secondary_hue` char(30) DEFAULT NULL COMMENT '主石副色',
  `white_color` char(5) DEFAULT NULL COMMENT '白钻颜色',
  `argyle_color` char(10) DEFAULT NULL COMMENT '阿盖尔颜色',
  `pair` char(5) DEFAULT NULL COMMENT '对',
  `jewerly_type` char(30) DEFAULT NULL COMMENT '珠宝类型',
  `jewerly_sub_type` char(30) DEFAULT NULL COMMENT '款式类型',
  `metal` char(20) DEFAULT NULL COMMENT '金属',
  `metal_color` char(40) DEFAULT NULL COMMENT '金属颜色',
  `metal_weight` char(5) DEFAULT NULL COMMENT '金重',
  `entery_type` char(20) DEFAULT NULL COMMENT '镶入珠宝类型',
  `earback_type` char(20) DEFAULT NULL COMMENT '耳环托类型',
  `ring_size` char(5) DEFAULT NULL COMMENT '戒圈',
  `side_weight` char(5) DEFAULT NULL COMMENT '副石重量',
  `jewelry_width` char(10) DEFAULT NULL COMMENT '珠宝直径',
  `chain_length` char(10) DEFAULT NULL COMMENT '链长',
  `length` char(10) DEFAULT NULL COMMENT '长度',
  `jewelry_stones_details` text COMMENT '石头详细',
  `price_per_karat` varchar(40) DEFAULT NULL COMMENT '每克拉价格',
  `image6` varchar(200) DEFAULT NULL COMMENT '图片6',
  `image7` varchar(200) DEFAULT NULL COMMENT '图片7',
  `image8` varchar(200) DEFAULT NULL COMMENT '图片8',
  `cert2` char(10) DEFAULT NULL COMMENT '证书类型2',
  `cert3` char(10) DEFAULT NULL COMMENT '证书类型3',
  `cert_id2` char(10) DEFAULT NULL COMMENT '证书号2',
  `cert_id3` char(10) DEFAULT NULL COMMENT '证书号3',
  `report_1` varchar(100) DEFAULT NULL COMMENT '证书报告1',
  `report_2` varchar(100) DEFAULT NULL COMMENT '证书报告2',
  `report_3` varchar(100) DEFAULT NULL COMMENT '证书报告3',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=563489 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_dharam
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_dharam`;
CREATE TABLE `app_diamond_dharam` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(10) DEFAULT NULL,
  `table_lv` varchar(10) DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_diamondbyhk
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_diamondbyhk`;
CREATE TABLE `app_diamond_diamondbyhk` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(10) DEFAULT NULL,
  `table_lv` varchar(10) DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_diarough
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_diarough`;
CREATE TABLE `app_diamond_diarough` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(10) DEFAULT NULL,
  `table_lv` varchar(10) DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_enjoy
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_enjoy`;
CREATE TABLE `app_diamond_enjoy` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(10) DEFAULT NULL,
  `table_lv` varchar(10) DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_fulong
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_fulong`;
CREATE TABLE `app_diamond_fulong` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` varchar(10) NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(20) DEFAULT NULL,
  `table_lv` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `pifajia` decimal(15,3) DEFAULT '0.000',
  `pifajia_mode` varchar(10) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL COMMENT '图片',
  `is_hot` tinyint(1) DEFAULT '0' COMMENT '是否热销'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_jb
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_jb`;
CREATE TABLE `app_diamond_jb` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(10) DEFAULT NULL,
  `table_lv` varchar(10) DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_jiajialv
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_jiajialv`;
CREATE TABLE `app_diamond_jiajialv` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `good_type` tinyint(1) DEFAULT NULL COMMENT '货品类型 1->现货  2->期货',
  `from_ad` tinyint(1) DEFAULT NULL COMMENT '来源',
  `cert` char(10) DEFAULT NULL COMMENT '证书类型',
  `cost_min` decimal(10,2) DEFAULT NULL COMMENT '最小成本',
  `cost_max` decimal(10,2) DEFAULT NULL COMMENT '最大成本',
  `jiajialv` decimal(10,2) DEFAULT NULL COMMENT '加价率',
  `status` tinyint(1) NOT NULL COMMENT '货品状态 1->上架  2->下架',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_kapu
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_kapu`;
CREATE TABLE `app_diamond_kapu` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(10) DEFAULT NULL,
  `table_lv` varchar(10) DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_karp
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_karp`;
CREATE TABLE `app_diamond_karp` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` varchar(10) NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(20) DEFAULT NULL,
  `table_lv` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `pifajia` decimal(15,3) DEFAULT '0.000',
  `pifajia_mode` varchar(10) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL COMMENT '图片',
  `is_hot` tinyint(1) DEFAULT '0' COMMENT '是否热销'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_kb
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_kb`;
CREATE TABLE `app_diamond_kb` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(10) DEFAULT NULL,
  `table_lv` varchar(10) DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_kbgems
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_kbgems`;
CREATE TABLE `app_diamond_kbgems` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` varchar(10) NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(20) DEFAULT NULL,
  `table_lv` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `pifajia` decimal(15,3) DEFAULT '0.000',
  `pifajia_mode` varchar(10) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL COMMENT '图片',
  `is_hot` tinyint(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_kg
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_kg`;
CREATE TABLE `app_diamond_kg` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(10) DEFAULT NULL,
  `table_lv` varchar(10) DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_kgk
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_kgk`;
CREATE TABLE `app_diamond_kgk` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` varchar(10) NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(20) DEFAULT NULL,
  `table_lv` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `pifajia` decimal(15,3) DEFAULT '0.000',
  `pifajia_mode` varchar(10) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL COMMENT '图片',
  `is_hot` tinyint(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_kiran
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_kiran`;
CREATE TABLE `app_diamond_kiran` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` varchar(10) NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(20) DEFAULT NULL,
  `table_lv` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `pifajia` decimal(15,3) DEFAULT '0.000',
  `pifajia_mode` varchar(10) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL COMMENT '图片',
  `is_hot` tinyint(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_log
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_log`;
CREATE TABLE `app_diamond_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `from_ad` tinyint(1) DEFAULT NULL COMMENT '来源',
  `operation_type` tinyint(1) DEFAULT NULL COMMENT '操作类型',
  `operation_content` varchar(200) DEFAULT NULL COMMENT '操作内容',
  `create_time` datetime DEFAULT NULL COMMENT '操作时间',
  `create_user` varchar(10) DEFAULT NULL COMMENT '操作人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_price
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_price`;
CREATE TABLE `app_diamond_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guige_a` int(4) NOT NULL COMMENT '主石规格起始',
  `guige_b` int(6) NOT NULL COMMENT '主石规格结束',
  `price` decimal(10,0) NOT NULL COMMENT '价格',
  `guige_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态1启用，2停用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_shawn
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_shawn`;
CREATE TABLE `app_diamond_shawn` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` varchar(10) NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(20) DEFAULT NULL,
  `table_lv` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `pifajia` decimal(15,3) DEFAULT '0.000',
  `pifajia_mode` varchar(10) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL COMMENT '图片',
  `is_hot` tinyint(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_slk
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_slk`;
CREATE TABLE `app_diamond_slk` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` varchar(10) NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(20) DEFAULT NULL,
  `table_lv` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `pifajia` decimal(15,3) DEFAULT '0.000',
  `pifajia_mode` varchar(10) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL COMMENT '图片',
  `is_hot` tinyint(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_starrays
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_starrays`;
CREATE TABLE `app_diamond_starrays` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` varchar(10) NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(20) DEFAULT NULL,
  `table_lv` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `pifajia` decimal(15,3) DEFAULT '0.000',
  `pifajia_mode` varchar(10) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL COMMENT '图片',
  `is_hot` tinyint(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_szh
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_szh`;
CREATE TABLE `app_diamond_szh` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` varchar(10) NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(20) DEFAULT NULL,
  `table_lv` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `pifajia` decimal(15,3) DEFAULT '0.000',
  `pifajia_mode` varchar(10) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL COMMENT '图片',
  `is_hot` tinyint(1) DEFAULT '0' COMMENT '是否热销'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_diamond_tiaojia
-- ----------------------------
DROP TABLE IF EXISTS `app_diamond_tiaojia`;
CREATE TABLE `app_diamond_tiaojia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `address1` text NOT NULL COMMENT '区域1',
  `address2` text NOT NULL COMMENT '区域2',
  `address3` text NOT NULL COMMENT '区域3',
  `dep_id1` text NOT NULL COMMENT '城市1',
  `dep_id2` text NOT NULL COMMENT '城市2',
  `dep_id3` text NOT NULL COMMENT '城市3',
  `zhekou_31` varchar(20) NOT NULL COMMENT '0.3-0.49',
  `zhekou_32` varchar(20) NOT NULL COMMENT '0.3-0.49',
  `zhekou_33` varchar(20) NOT NULL COMMENT '0.3-0.49',
  `zhekou_51` varchar(20) NOT NULL COMMENT '0.5-0.79',
  `zhekou_52` varchar(20) NOT NULL COMMENT '0.5-0.79',
  `zhekou_53` varchar(20) NOT NULL COMMENT '0.5-0.79',
  `zhekou_81` varchar(20) NOT NULL COMMENT '0.8-0.99',
  `zhekou_82` varchar(20) NOT NULL COMMENT '0.8-0.99',
  `zhekou_83` varchar(20) NOT NULL COMMENT '0.8-0.99',
  `zhekou_01` varchar(20) NOT NULL COMMENT '1-1.49',
  `zhekou_02` varchar(20) NOT NULL COMMENT '1-1.49',
  `zhekou_03` varchar(20) NOT NULL COMMENT '1-1.49',
  `zhekou_11` varchar(20) NOT NULL COMMENT '1.5-1.99',
  `zhekou_12` varchar(20) NOT NULL COMMENT '1.5-1.99',
  `zhekou_13` varchar(20) NOT NULL COMMENT '1.5-1.99',
  `zhekou_21` varchar(20) NOT NULL COMMENT '2以上',
  `zhekou_22` varchar(20) NOT NULL COMMENT '2以上',
  `zhekou_23` varchar(20) NOT NULL COMMENT '2以上',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='裸钻区域调价';

-- ----------------------------
-- Table structure for app_factory_apply
-- ----------------------------
DROP TABLE IF EXISTS `app_factory_apply`;
CREATE TABLE `app_factory_apply` (
  `apply_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `style_id` int(10) unsigned NOT NULL COMMENT '款式ID',
  `style_sn` varchar(20) DEFAULT NULL COMMENT '款号',
  `f_id` int(4) DEFAULT NULL COMMENT '更新ID',
  `factory_id` int(11) NOT NULL COMMENT '工厂id',
  `factory_name` varchar(30) DEFAULT NULL COMMENT '工厂名称',
  `factory_sn` varchar(30) NOT NULL COMMENT '工厂模号',
  `xiangkou` decimal(8,3) NOT NULL COMMENT '镶口',
  `factory_fee` decimal(10,2) NOT NULL COMMENT '工厂费用',
  `type` tinyint(1) NOT NULL COMMENT '操作:1 添加;2 删除;3 默认工厂',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态  1、待审核；2、审核通过；3、审核未通过；4、取消，5作废通过',
  `apply_num` smallint(5) NOT NULL DEFAULT '0' COMMENT '申请次数',
  `make_name` varchar(10) NOT NULL DEFAULT '0' COMMENT '创建人',
  `crete_time` datetime DEFAULT NULL COMMENT '创建时间',
  `check_name` varchar(10) DEFAULT NULL COMMENT '审核人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `info` varchar(100) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`apply_id`),
  KEY `style_sn` (`style_sn`),
  KEY `factory_sn` (`factory_sn`),
  KEY `factory_sn_2` (`factory_sn`),
  KEY `factory_sn_3` (`factory_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=5458 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_goodsprice_by_style
-- ----------------------------
DROP TABLE IF EXISTS `app_goodsprice_by_style`;
CREATE TABLE `app_goodsprice_by_style` (
  `id` varchar(60) NOT NULL,
  `style_sn` varchar(30) NOT NULL COMMENT '款号style_sn',
  `attr_select` text COMMENT '选中的属性（json格式）',
  `attr_keys` varchar(512) NOT NULL COMMENT '参与价格计算的属性组合keys',
  `attr_data` varchar(2048) DEFAULT NULL COMMENT '参与价格计算的属性组合',
  `market_price` varchar(30) DEFAULT '' COMMENT '市场价格',
  `kela_price` varchar(30) DEFAULT '' COMMENT 'BDD销售价格',
  `goods_type` tinyint(3) unsigned DEFAULT NULL COMMENT '商品类型 1现货 2 期货 3 起版',
  `goods_stock` int(8) unsigned DEFAULT '1' COMMENT '商品库存',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '商品状态：1上架，0下架',
  `create_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除 0未删除，1已删除',
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id` (`id`),
  KEY `style_sn` (`style_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='款式商品定价';

-- ----------------------------
-- Table structure for app_goodsprice_dingjia
-- ----------------------------
DROP TABLE IF EXISTS `app_goodsprice_dingjia`;
CREATE TABLE `app_goodsprice_dingjia` (
  `_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `_md5` varchar(60) DEFAULT NULL,
  `style_sn` varchar(255) DEFAULT NULL,
  `a_caizhi` varchar(255) DEFAULT NULL,
  `a_caizhiyanse` varchar(255) DEFAULT NULL,
  `a_zuanshijingdu` varchar(255) DEFAULT NULL,
  `a_yanse` varchar(255) DEFAULT NULL,
  `a_jinzhong` varchar(255) DEFAULT NULL,
  `a_zhushizhongliang` varchar(255) DEFAULT NULL,
  `kela_price` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`_id`),
  KEY `_md5` (`_md5`),
  KEY `style_sn` (`style_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=8983 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_goodsprice_salepolicy
-- ----------------------------
DROP TABLE IF EXISTS `app_goodsprice_salepolicy`;
CREATE TABLE `app_goodsprice_salepolicy` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `style_sn` varchar(30) NOT NULL COMMENT '款号',
  `style_id` varchar(60) NOT NULL COMMENT '按款定价Id',
  `channel_id` int(8) unsigned NOT NULL COMMENT '销售渠道',
  `jiajialv` decimal(8,2) DEFAULT NULL COMMENT '加价率',
  `sta_value` decimal(8,2) unsigned DEFAULT '0.00' COMMENT '固定值',
  `create_time` datetime DEFAULT NULL,
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_delete` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除 0未删除，1已删除',
  PRIMARY KEY (`id`),
  KEY `style_sn` (`style_id`,`channel_id`,`is_delete`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=129960 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_jinsun
-- ----------------------------
DROP TABLE IF EXISTS `app_jinsun`;
CREATE TABLE `app_jinsun` (
  `s_id` int(11) NOT NULL AUTO_INCREMENT,
  `price_type` varchar(10) NOT NULL COMMENT '1为男戒;2为女戒;3为情侣男戒;4为情侣女戒',
  `material_id` varchar(10) NOT NULL COMMENT '材质',
  `lv` decimal(8,2) NOT NULL COMMENT '金损率',
  `jinsun_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态1启用，2停用',
  PRIMARY KEY (`s_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_lz_discount_grant
-- ----------------------------
DROP TABLE IF EXISTS `app_lz_discount_grant`;
CREATE TABLE `app_lz_discount_grant` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户授权[user 表id]',
  `type` tinyint(1) unsigned NOT NULL COMMENT '类型范围 0未设定 1.小于50分 2.小于1克拉 3.大于1克拉',
  `zhekou` decimal(5,2) NOT NULL COMMENT '折扣',
  `mima` varchar(4) NOT NULL COMMENT '密码',
  `create_user_id` int(10) unsigned NOT NULL COMMENT '创建用户id',
  `create_user` varchar(20) NOT NULL COMMENT '创建用户',
  `createtime` datetime NOT NULL COMMENT '生成时间',
  `endtime` datetime NOT NULL COMMENT '结束时间',
  `use_user_id` int(10) unsigned DEFAULT '0' COMMENT '使用人id',
  `use_user` varchar(20) DEFAULT NULL COMMENT '使用人',
  `usetime` datetime DEFAULT NULL COMMENT '使用时间',
  `order_goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单商品id',
  `goods_sn` varchar(20) DEFAULT NULL COMMENT '货号',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '标签价',
  `real_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成交价',
  `cert` varchar(10) DEFAULT NULL COMMENT '证书类型',
  `cert_id` varchar(20) DEFAULT NULL COMMENT '证书号',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可用 1可用 2使用 3过期 4作废',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `user_id` (`user_id`,`status`)
) ENGINE=MyISAM AUTO_INCREMENT=31674 DEFAULT CHARSET=utf8 COMMENT='折扣密码授权表';

-- ----------------------------
-- Table structure for app_material_info
-- ----------------------------
DROP TABLE IF EXISTS `app_material_info`;
CREATE TABLE `app_material_info` (
  `material_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '材质ID',
  `material_name` varchar(10) NOT NULL COMMENT '材质名称',
  `price` decimal(8,2) NOT NULL,
  `tax_point` decimal(8,2) DEFAULT '0.00' COMMENT '税点',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `create_user` varchar(10) NOT NULL COMMENT '创建人',
  `material_status` tinyint(2) NOT NULL COMMENT '状态:1启用;0停用',
  `material_remark` varchar(50) DEFAULT NULL COMMENT '记录备注',
  PRIMARY KEY (`material_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8 COMMENT='材质信息表';

-- ----------------------------
-- Table structure for app_material_info170301
-- ----------------------------
DROP TABLE IF EXISTS `app_material_info170301`;
CREATE TABLE `app_material_info170301` (
  `material_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '材质ID',
  `material_name` varchar(10) NOT NULL COMMENT '材质名称',
  `price` decimal(8,2) NOT NULL,
  `tax_point` decimal(8,2) DEFAULT '0.00' COMMENT '税点',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `create_user` varchar(10) NOT NULL COMMENT '创建人',
  `material_status` tinyint(2) NOT NULL COMMENT '状态:1启用;0停用',
  `material_remark` varchar(50) DEFAULT NULL COMMENT '记录备注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_member_account
-- ----------------------------
DROP TABLE IF EXISTS `app_member_account`;
CREATE TABLE `app_member_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `memeber_id` int(11) unsigned NOT NULL COMMENT '会员ID',
  `current_money` double(12,2) DEFAULT NULL COMMENT '当前余额',
  `total_money` decimal(12,2) DEFAULT NULL COMMENT '总消费金额',
  `total_point` int(11) DEFAULT NULL COMMENT '总积分',
  `is_deleted` tinyint(4) DEFAULT '0' COMMENT '删除标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员账户信息\r\n';

-- ----------------------------
-- Table structure for app_member_address
-- ----------------------------
DROP TABLE IF EXISTS `app_member_address`;
CREATE TABLE `app_member_address` (
  `mem_address_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '地址ID',
  `member_id` int(10) NOT NULL COMMENT '会员id',
  `customer` varchar(50) NOT NULL COMMENT '顾客名',
  `mobile` char(11) NOT NULL COMMENT '手机号',
  `mem_country_id` int(10) NOT NULL COMMENT '会员国家',
  `mem_province_id` int(10) NOT NULL COMMENT '会员省',
  `mem_city_id` int(10) NOT NULL COMMENT '会员城市',
  `mem_district_id` int(10) NOT NULL COMMENT '会员区',
  `mem_address` varchar(255) NOT NULL COMMENT '会员详细地址',
  `mem_is_def` tinyint(1) DEFAULT '0' COMMENT '是否默认 1是默认0不是默认',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`mem_address_id`),
  KEY `update_time` (`update_time`)
) ENGINE=InnoDB AUTO_INCREMENT=3343 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_memeber_card
-- ----------------------------
DROP TABLE IF EXISTS `app_memeber_card`;
CREATE TABLE `app_memeber_card` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `mem_card_sn` varchar(20) NOT NULL COMMENT '会员卡号',
  `mem_card_level` tinyint(2) NOT NULL COMMENT '会员卡等级',
  `mem_card_uptime` int(11) NOT NULL COMMENT '会员卡升级时间',
  `men_card_type` tinyint(2) NOT NULL DEFAULT '0' COMMENT '会员卡类型',
  `mem_card_status` tinyint(4) DEFAULT '2' COMMENT '1有效,2无效,3挂失,4注销',
  `is_deleted` tinyint(2) DEFAULT '0' COMMENT '删除标识(删除前,卡必须注销)',
  `addby_id` int(11) DEFAULT NULL COMMENT '创建人',
  `add_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员卡信息表';

-- ----------------------------
-- Table structure for app_memeber_point
-- ----------------------------
DROP TABLE IF EXISTS `app_memeber_point`;
CREATE TABLE `app_memeber_point` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `memeber_id` int(11) DEFAULT NULL COMMENT '会员ID',
  `change_step` decimal(12,2) DEFAULT '0.00' COMMENT '本次调整',
  `chane_type` smallint(6) DEFAULT NULL COMMENT '调整类型',
  `change_status` tinyint(4) DEFAULT NULL COMMENT '调整状态',
  `happen_time` int(11) DEFAULT NULL COMMENT '发生时间',
  `pass_time` int(11) DEFAULT NULL COMMENT '通过时间',
  `pass_userid` int(11) DEFAULT NULL COMMENT '操作人',
  `is_deleted` tinyint(4) DEFAULT '0' COMMENT '删除标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员积分表';

-- ----------------------------
-- Table structure for app_order_favorable
-- ----------------------------
DROP TABLE IF EXISTS `app_order_favorable`;
CREATE TABLE `app_order_favorable` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `order_sn` varchar(30) NOT NULL COMMENT '订单号',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `detail_id` int(10) NOT NULL COMMENT '商品自增id',
  `goods_id` varchar(30) NOT NULL COMMENT '商品货号',
  `goods_sn` varchar(30) NOT NULL COMMENT '款号',
  `goods_name` varchar(30) NOT NULL COMMENT '商品名称',
  `goods_price` decimal(16,2) NOT NULL COMMENT '商品价格',
  `favorable_price` decimal(8,2) NOT NULL COMMENT '优惠价格（有符号）',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `check_user_id` int(8) DEFAULT NULL COMMENT '审核人id',
  `check_user` varchar(30) DEFAULT NULL COMMENT '审核人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `check_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '审核状态，1：未操作；2：审核通过；3审核驳回',
  `consignee` varchar(30) DEFAULT NULL COMMENT '顾客姓名',
  `create_user` varchar(30) DEFAULT NULL COMMENT '销售顾问',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=43078 DEFAULT CHARSET=utf8 COMMENT='订单货品优惠';

-- ----------------------------
-- Table structure for app_price_by_style
-- ----------------------------
DROP TABLE IF EXISTS `app_price_by_style`;
CREATE TABLE `app_price_by_style` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `style_id` int(10) unsigned NOT NULL COMMENT '款式id',
  `caizhi` varchar(10) NOT NULL COMMENT '材质',
  `stone_position` tinyint(1) unsigned NOT NULL COMMENT '石头位置类型 1主石2副石',
  `stone_cat` tinyint(3) unsigned DEFAULT NULL COMMENT '石头类型',
  `tuo_type` tinyint(1) unsigned NOT NULL COMMENT '金托类型',
  `zuan_min` decimal(8,3) unsigned DEFAULT NULL COMMENT '主石大小范围小',
  `zuan_max` decimal(8,3) unsigned DEFAULT NULL COMMENT '主石大小范围大',
  `zuan_yanse_min` varchar(10) DEFAULT NULL COMMENT '钻颜色始',
  `zuan_yanse_max` varchar(10) DEFAULT NULL COMMENT '钻颜色末',
  `zuan_jindu_min` varchar(10) DEFAULT NULL COMMENT '钻净度始',
  `zuan_jindu_max` varchar(10) DEFAULT NULL COMMENT '钻净度末',
  `cert` varchar(50) DEFAULT NULL COMMENT '证书类型',
  `zuan_shape` varchar(50) DEFAULT NULL COMMENT '石头形状',
  `price` decimal(12,2) unsigned NOT NULL COMMENT '按款定价',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '删除标志 0未删除1删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COMMENT='按款定价结构表';

-- ----------------------------
-- Table structure for app_product_type
-- ----------------------------
DROP TABLE IF EXISTS `app_product_type`;
CREATE TABLE `app_product_type` (
  `product_type_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '产品线id',
  `product_type_name` char(50) NOT NULL COMMENT '产品线名称',
  `product_type_code` char(50) DEFAULT NULL COMMENT '产品线编码',
  `note` varchar(255) DEFAULT NULL COMMENT '描述',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级部门id',
  `tree_path` varchar(512) NOT NULL COMMENT '全路径',
  `pids` varchar(200) DEFAULT NULL COMMENT '祖先分类',
  `childrens` int(11) NOT NULL DEFAULT '0' COMMENT '下级分类数',
  `display_order` int(11) NOT NULL COMMENT '显示顺序',
  `product_type_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否停用:1启用 0停用',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  PRIMARY KEY (`product_type_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COMMENT='产品线分类';

-- ----------------------------
-- Table structure for app_salepolicy_channel
-- ----------------------------
DROP TABLE IF EXISTS `app_salepolicy_channel`;
CREATE TABLE `app_salepolicy_channel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `policy_id` int(10) DEFAULT NULL COMMENT '销售策略id',
  `channel` int(10) DEFAULT NULL COMMENT '渠道id',
  `channel_level` int(10) DEFAULT '1' COMMENT '等级',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(100) DEFAULT NULL COMMENT '创建人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `check_user` varchar(100) DEFAULT NULL COMMENT '审核',
  `status` int(1) DEFAULT NULL COMMENT '状态:1保存2申请3审核通过4未通过5取消',
  `is_delete` int(1) DEFAULT NULL COMMENT '取消 1未删除 2已删除',
  PRIMARY KEY (`id`),
  KEY `policy_id` (`policy_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2014 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_salepolicy_channel_log
-- ----------------------------
DROP TABLE IF EXISTS `app_salepolicy_channel_log`;
CREATE TABLE `app_salepolicy_channel_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `policy_id` int(10) unsigned NOT NULL COMMENT '销售策略id',
  `create_user` varchar(20) NOT NULL COMMENT '操作人  ',
  `create_time` datetime NOT NULL COMMENT '操作时间',
  `IP` char(15) NOT NULL COMMENT '操作IP',
  `status` tinyint(1) unsigned DEFAULT NULL COMMENT '状态:1保存2申请3审核通过4未通过5取消',
  `remark` text NOT NULL COMMENT '备注',
  `is_delete` int(1) NOT NULL DEFAULT '1' COMMENT '删除状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1042 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_salepolicy_goods
-- ----------------------------
DROP TABLE IF EXISTS `app_salepolicy_goods`;
CREATE TABLE `app_salepolicy_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `policy_id` int(10) NOT NULL COMMENT '销售策略id',
  `goods_id` varchar(30) NOT NULL COMMENT '货号或款号',
  `chengben` decimal(8,2) unsigned NOT NULL COMMENT '成本价',
  `sale_price` decimal(12,2) unsigned DEFAULT NULL COMMENT '销售价',
  `jiajia` decimal(8,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '加价率',
  `sta_value` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '固定值',
  `chengben_compare` decimal(8,2) DEFAULT NULL COMMENT '当可销售商品表中的成本发生变化 向本字段写入改变的成本价格',
  `isXianhuo` tinyint(1) NOT NULL DEFAULT '1' COMMENT '现货状态0是期货1是现货',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(100) DEFAULT NULL COMMENT '创建人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `check_user` varchar(100) DEFAULT NULL COMMENT '审核',
  `status` int(1) DEFAULT '1' COMMENT '状态:1保存2申请3审核通过4未通过5取消',
  `is_delete` int(1) DEFAULT '1' COMMENT '删除 1未删除 2已删除',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`),
  KEY `policy_id` (`policy_id`,`goods_id`),
  KEY `status` (`status`),
  KEY `policy_id_2` (`policy_id`),
  KEY `is_delete` (`is_delete`),
  KEY `policy_id_3` (`policy_id`,`is_delete`)
) ENGINE=InnoDB AUTO_INCREMENT=384141 DEFAULT CHARSET=utf8 COMMENT='销售政策对应商品表';

-- ----------------------------
-- Table structure for app_salepolicy_goods_copy
-- ----------------------------
DROP TABLE IF EXISTS `app_salepolicy_goods_copy`;
CREATE TABLE `app_salepolicy_goods_copy` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `policy_id` int(10) NOT NULL COMMENT '销售策略id',
  `goods_id` varchar(30) NOT NULL COMMENT '货号或款号',
  `chengben` decimal(8,2) unsigned NOT NULL COMMENT '成本价',
  `sale_price` decimal(12,2) unsigned DEFAULT NULL COMMENT '销售价',
  `jiajia` decimal(8,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '加价率',
  `sta_value` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '固定值',
  `chengben_compare` decimal(8,2) DEFAULT NULL COMMENT '当可销售商品表中的成本发生变化 向本字段写入改变的成本价格',
  `isXianhuo` tinyint(1) NOT NULL DEFAULT '1' COMMENT '现货状态0是期货1是现货',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(100) DEFAULT NULL COMMENT '创建人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `update_time` datetime DEFAULT NULL COMMENT '更新时间',
  `check_user` varchar(100) DEFAULT NULL COMMENT '审核',
  `status` int(1) DEFAULT '1' COMMENT '状态:1保存2申请3审核通过4未通过5取消',
  `is_delete` int(1) DEFAULT '1' COMMENT '删除 1未删除 2已删除',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`),
  KEY `policy_id` (`policy_id`,`goods_id`),
  KEY `status` (`status`),
  KEY `policy_id_2` (`policy_id`),
  KEY `is_delete` (`is_delete`),
  KEY `policy_id_3` (`policy_id`,`is_delete`)
) ENGINE=InnoDB AUTO_INCREMENT=373047 DEFAULT CHARSET=utf8 COMMENT='销售政策对应商品表';

-- ----------------------------
-- Table structure for app_salepolicy_goods_my
-- ----------------------------
DROP TABLE IF EXISTS `app_salepolicy_goods_my`;
CREATE TABLE `app_salepolicy_goods_my` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `policy_id` int(10) NOT NULL COMMENT '销售策略id',
  `goods_id` varchar(30) NOT NULL COMMENT '货号或款号',
  `chengben` decimal(8,2) unsigned NOT NULL COMMENT '成本价',
  `sale_price` decimal(12,2) unsigned DEFAULT NULL COMMENT '销售价',
  `jiajia` decimal(8,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '加价率',
  `sta_value` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '固定值',
  `chengben_compare` decimal(8,2) DEFAULT NULL COMMENT '当可销售商品表中的成本发生变化 向本字段写入改变的成本价格',
  `isXianhuo` tinyint(1) NOT NULL DEFAULT '1' COMMENT '现货状态0是期货1是现货',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(100) DEFAULT NULL COMMENT '创建人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `check_user` varchar(100) DEFAULT NULL COMMENT '审核',
  `status` int(1) DEFAULT '1' COMMENT '状态:1保存2申请3审核通过4未通过5取消',
  `is_delete` int(1) DEFAULT '1' COMMENT '删除 1未删除 2已删除',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`),
  KEY `status` (`status`),
  KEY `policy_id_2` (`policy_id`),
  KEY `is_delete` (`is_delete`),
  KEY `policy_id_3` (`policy_id`,`is_delete`)
) ENGINE=MyISAM AUTO_INCREMENT=215677 DEFAULT CHARSET=utf8 COMMENT='销售政策对应商品表';

-- ----------------------------
-- Table structure for app_salepolicy_together_goods
-- ----------------------------
DROP TABLE IF EXISTS `app_salepolicy_together_goods`;
CREATE TABLE `app_salepolicy_together_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `together_name` varchar(30) NOT NULL COMMENT '打包策略名称',
  `create_user` varchar(30) NOT NULL COMMENT '创建人',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `is_split` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否可以拆分，1：否；2：是',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否有效，1：有效；2：无效',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='打包策略';

-- ----------------------------
-- Table structure for app_shop_config
-- ----------------------------
DROP TABLE IF EXISTS `app_shop_config`;
CREATE TABLE `app_shop_config` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '名称',
  `code` varchar(30) NOT NULL COMMENT '编码',
  `value` text NOT NULL COMMENT '值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_style_baoxianfee
-- ----------------------------
DROP TABLE IF EXISTS `app_style_baoxianfee`;
CREATE TABLE `app_style_baoxianfee` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `min` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '最小值',
  `max` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT '最大值',
  `price` decimal(10,0) NOT NULL COMMENT '价格',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态1启用，2停用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_style_fee
-- ----------------------------
DROP TABLE IF EXISTS `app_style_fee`;
CREATE TABLE `app_style_fee` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `style_id` int(10) DEFAULT NULL COMMENT '款id',
  `style_sn` varchar(50) DEFAULT NULL,
  `fee_type` tinyint(1) NOT NULL COMMENT '费用类型：1材质费，2超石工费，3表面工艺',
  `price` decimal(8,2) NOT NULL COMMENT '费用',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1启用2停用',
  `check_user` varchar(10) NOT NULL COMMENT '创建人',
  `check_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `cancel_time` datetime DEFAULT NULL COMMENT '停用时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fee_id` (`style_id`,`fee_type`),
  KEY `style_sn` (`style_sn`),
  KEY `style_id` (`style_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48795 DEFAULT CHARSET=utf8 COMMENT='费用';

-- ----------------------------
-- Table structure for app_style_for
-- ----------------------------
DROP TABLE IF EXISTS `app_style_for`;
CREATE TABLE `app_style_for` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `style_id` int(10) NOT NULL COMMENT '款式id',
  `style_for_who` varchar(20) NOT NULL COMMENT '适合对象(按年龄)1宝宝2青年3中年4老年',
  `style_for_use` varchar(20) NOT NULL COMMENT '适合场景(按用途)1求婚2纪念日3结婚',
  `style_for_when` varchar(20) NOT NULL COMMENT '销售类型1投资2装饰',
  `style_for_designer` varchar(20) NOT NULL COMMENT '设计师1无2POLO',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_style_gallery
-- ----------------------------
DROP TABLE IF EXISTS `app_style_gallery`;
CREATE TABLE `app_style_gallery` (
  `g_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `style_id` int(10) unsigned DEFAULT NULL COMMENT '款式id',
  `style_sn` varchar(30) DEFAULT NULL,
  `image_place` tinyint(3) unsigned NOT NULL COMMENT '图片位置，100=网络上架，6=表现工艺，5=证书图,1=正立45°图,2=正立图,3=爪头图,4=爪尾图,8=内臂图,7=质检专用图',
  `img_sort` int(10) unsigned NOT NULL COMMENT '图片排序',
  `img_ori` varchar(100) NOT NULL COMMENT '原图路径',
  `thumb_img` varchar(100) NOT NULL COMMENT '缩略图',
  `middle_img` varchar(100) NOT NULL COMMENT '中图',
  `big_img` varchar(100) NOT NULL COMMENT '大图',
  PRIMARY KEY (`g_id`) USING BTREE,
  KEY `style_sn` (`style_sn`) USING BTREE,
  KEY `img_ori` (`img_ori`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=117456 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_style_id
-- ----------------------------
DROP TABLE IF EXISTS `app_style_id`;
CREATE TABLE `app_style_id` (
  `g_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '自增ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_style_jxs
-- ----------------------------
DROP TABLE IF EXISTS `app_style_jxs`;
CREATE TABLE `app_style_jxs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `style_name` varchar(100) DEFAULT NULL COMMENT '款式名称',
  `style_sn` varchar(60) DEFAULT NULL COMMENT '款号',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态',
  `add_user` varchar(20) DEFAULT NULL COMMENT '添加用户',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `ban_user` varchar(20) DEFAULT NULL COMMENT '禁用用户',
  `ban_time` datetime DEFAULT NULL COMMENT '禁用时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_style_quickdiy
-- ----------------------------
DROP TABLE IF EXISTS `app_style_quickdiy`;
CREATE TABLE `app_style_quickdiy` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `goods_sn` varchar(50) NOT NULL COMMENT '商品编码',
  `style_sn` varchar(30) NOT NULL COMMENT '款号',
  `style_name` varchar(80) DEFAULT NULL COMMENT '款式名称',
  `caizhi` varchar(10) NOT NULL COMMENT '材质',
  `caizhiyanse` varchar(10) NOT NULL COMMENT '材质颜色',
  `xiangkou` varchar(10) DEFAULT NULL COMMENT '镶口',
  `zhiquan` varchar(10) DEFAULT NULL COMMENT '指圈',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态 style.quickdiy_status 字典 1启用 0禁用',
  `create_user` varchar(30) DEFAULT NULL COMMENT '添加人',
  `create_time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `goods_sn` (`goods_sn`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6464 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_style_tsyd_price
-- ----------------------------
DROP TABLE IF EXISTS `app_style_tsyd_price`;
CREATE TABLE `app_style_tsyd_price` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增',
  `style_sn` varchar(20) NOT NULL COMMENT '款号',
  `style_name` varchar(20) NOT NULL COMMENT '款式名称',
  `work` varchar(20) DEFAULT NULL COMMENT '工艺',
  `xiangkou_min` double(6,2) unsigned DEFAULT NULL COMMENT '镶口最小',
  `xiangkou_max` double(6,2) unsigned DEFAULT NULL COMMENT '镶口最大',
  `k_weight` double(7,3) unsigned DEFAULT NULL COMMENT '约18K金重',
  `pt_weight` double(7,3) DEFAULT NULL COMMENT '约Pt950金重',
  `carat` double(7,3) DEFAULT NULL COMMENT '钻石重量',
  `k_price` double(10,2) DEFAULT NULL COMMENT '18K一对托定制价',
  `pt_price` double(10,2) DEFAULT NULL COMMENT 'PT950一对定制托价',
  `jumpto` tinyint(3) NOT NULL DEFAULT '1' COMMENT '跳转地址 1裸钻 2成品',
  `pic` varchar(255) DEFAULT NULL COMMENT '图片地址',
  `group_sn` tinyint(3) DEFAULT NULL COMMENT '成组',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='天生一对定价';

-- ----------------------------
-- Table structure for app_style_xilie
-- ----------------------------
DROP TABLE IF EXISTS `app_style_xilie`;
CREATE TABLE `app_style_xilie` (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT '系列Id',
  `name` varchar(100) NOT NULL COMMENT '系列名称',
  `status` tinyint(1) unsigned NOT NULL COMMENT '是否启用',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8 COMMENT='款式系列表';

-- ----------------------------
-- Table structure for app_together_goods_related
-- ----------------------------
DROP TABLE IF EXISTS `app_together_goods_related`;
CREATE TABLE `app_together_goods_related` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `together_id` int(10) NOT NULL COMMENT '关联id',
  `goods_id` varchar(30) NOT NULL COMMENT '商品货号',
  `chengben` decimal(10,2) unsigned NOT NULL COMMENT '成本价',
  `sale_price` decimal(12,2) unsigned DEFAULT NULL COMMENT '销售价',
  `chengben_compare` decimal(8,2) DEFAULT NULL COMMENT '当可销售商品表中的成本发生变化 向本字段写入改变的陈本价格',
  `isXianhuo` tinyint(1) NOT NULL DEFAULT '1' COMMENT '现货状态0是期货1是现货',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(100) DEFAULT NULL COMMENT '创建人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `check_user` varchar(100) DEFAULT NULL COMMENT '审核人',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态:1保存2申请3审核通过4未通过5取消',
  `is_delete` tinyint(1) DEFAULT NULL COMMENT '删除 1未删除 2已删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_together_policey_related
-- ----------------------------
DROP TABLE IF EXISTS `app_together_policey_related`;
CREATE TABLE `app_together_policey_related` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `policy_id` int(10) NOT NULL COMMENT '销售策略id',
  `together_id` int(10) NOT NULL COMMENT '打包策略id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_tsyd_special
-- ----------------------------
DROP TABLE IF EXISTS `app_tsyd_special`;
CREATE TABLE `app_tsyd_special` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `style_name` varchar(100) DEFAULT NULL COMMENT '款式名称',
  `style_sn` varchar(60) DEFAULT NULL COMMENT '款号',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态',
  `add_user` varchar(20) DEFAULT NULL COMMENT '添加用户',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `ban_user` varchar(20) DEFAULT NULL COMMENT '禁用用户',
  `ban_time` datetime DEFAULT NULL COMMENT '禁用时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_user_bespoke_log
-- ----------------------------
DROP TABLE IF EXISTS `app_user_bespoke_log`;
CREATE TABLE `app_user_bespoke_log` (
  `log_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `bespoke_id` int(10) NOT NULL COMMENT '预约ID',
  `mem_id` int(10) NOT NULL COMMENT '用户ID',
  `create_user` varchar(20) DEFAULT NULL COMMENT '操作人  ',
  `create_time` datetime DEFAULT NULL COMMENT '操作时间',
  `IP` char(15) DEFAULT NULL COMMENT '操作IP',
  `remark` varchar(200) DEFAULT NULL COMMENT '备注信息',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=211 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_wsd_exchange
-- ----------------------------
DROP TABLE IF EXISTS `app_wsd_exchange`;
CREATE TABLE `app_wsd_exchange` (
  `wsd_id` int(4) NOT NULL AUTO_INCREMENT,
  `wsd_code` varchar(30) NOT NULL DEFAULT '万事达卡号',
  `wsd_name` varchar(20) NOT NULL DEFAULT '顾客姓名',
  `wsd_mobile` varchar(32) NOT NULL DEFAULT '顾客电话',
  `wsd_user` varchar(20) NOT NULL DEFAULT '顾问姓名',
  `wsd_department` varchar(20) NOT NULL DEFAULT '渠道部门',
  `wsd_department_name` varchar(100) NOT NULL DEFAULT '渠道名称',
  `wsd_is_bespoke` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否预约',
  `wsd_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '兑换时间',
  PRIMARY KEY (`wsd_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='万事达卡礼品兑换';

-- ----------------------------
-- Table structure for app_xiangkou
-- ----------------------------
DROP TABLE IF EXISTS `app_xiangkou`;
CREATE TABLE `app_xiangkou` (
  `x_id` int(11) NOT NULL AUTO_INCREMENT,
  `style_id` int(11) NOT NULL COMMENT '款式编码',
  `style_sn` varchar(20) NOT NULL COMMENT '款式编码',
  `stone` varchar(10) NOT NULL COMMENT '镶口',
  `finger` varchar(20) NOT NULL COMMENT '手寸',
  `main_stone_weight` decimal(8,3) NOT NULL COMMENT '主石重',
  `main_stone_num` int(4) NOT NULL COMMENT '主石数',
  `sec_stone_weight` decimal(8,3) NOT NULL COMMENT '副石1重',
  `sec_stone_num` int(4) NOT NULL COMMENT '副石1数',
  `sec_stone_weight3` decimal(8,3) NOT NULL COMMENT '副石3重',
  `sec_stone_num3` int(4) NOT NULL COMMENT '副石3数',
  `sec_stone_weight_other` decimal(8,3) NOT NULL COMMENT '副石2重',
  `sec_stone_num_other` int(5) NOT NULL COMMENT '副石2数',
  `g18_weight` decimal(8,3) NOT NULL COMMENT '18K金重',
  `g18_weight_more` decimal(8,3) NOT NULL COMMENT '18K金重上公差',
  `g18_weight_more2` decimal(8,3) NOT NULL COMMENT '18K金重下公差',
  `gpt_weight` decimal(8,3) NOT NULL COMMENT 'pt950金重',
  `gpt_weight_more` decimal(8,3) NOT NULL COMMENT 'pt950金重上公差',
  `gpt_weight_more2` decimal(8,3) NOT NULL COMMENT 'pt950金重下公差',
  `sec_stone_price_other` decimal(10,2) DEFAULT NULL COMMENT '其他副石成本价',
  `company_type` varchar(10) DEFAULT NULL COMMENT '可销售公司类型',
  PRIMARY KEY (`x_id`),
  KEY `style_sn` (`style_sn`),
  KEY `style_id` (`style_id`,`stone`,`finger`),
  KEY `finger` (`finger`),
  KEY `stone` (`stone`)
) ENGINE=MyISAM AUTO_INCREMENT=219085 DEFAULT CHARSET=utf8 COMMENT='新款式表产品属性表';

-- ----------------------------
-- Table structure for app_xilie_config
-- ----------------------------
DROP TABLE IF EXISTS `app_xilie_config`;
CREATE TABLE `app_xilie_config` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_name` varchar(20) NOT NULL COMMENT '用户名',
  `xilie` varchar(255) NOT NULL COMMENT '系列权限',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_yikoujia_goods
-- ----------------------------
DROP TABLE IF EXISTS `app_yikoujia_goods`;
CREATE TABLE `app_yikoujia_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` varchar(255) DEFAULT NULL,
  `goods_sn` varchar(255) DEFAULT NULL,
  `caizhi` varchar(255) DEFAULT NULL,
  `small` varchar(255) DEFAULT NULL,
  `tuo_type` tinyint(1) DEFAULT '0' COMMENT '金托类型',
  `color` varchar(20) DEFAULT NULL COMMENT '颜色',
  `clarity` varchar(20) DEFAULT NULL COMMENT '净度',
  `sbig` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `policy_id` varchar(255) DEFAULT NULL,
  `isXianhuo` varchar(255) DEFAULT NULL,
  `is_delete` tinyint(2) DEFAULT '0' COMMENT '是否删除0未删除 ，1已删除',
  `add_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `add_user` varchar(120) DEFAULT NULL COMMENT '添加人',
  `cert` varchar(255) DEFAULT NULL COMMENT '裸钻证书类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16224 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for auto_run_goods
-- ----------------------------
DROP TABLE IF EXISTS `auto_run_goods`;
CREATE TABLE `auto_run_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `goods_id` varchar(30) NOT NULL COMMENT '货号',
  `product_type` varchar(50) NOT NULL COMMENT '产品线',
  `cat_type` varchar(50) NOT NULL COMMENT '款式分类',
  `is_on_sale` int(3) NOT NULL DEFAULT '1' COMMENT '状态',
  `warehouse` varchar(30) NOT NULL COMMENT '所在仓库',
  `goods_sn` varchar(50) NOT NULL COMMENT '款号',
  `goods_name` varchar(100) DEFAULT NULL COMMENT '名称',
  `mingyichengben` decimal(10,2) DEFAULT '0.00' COMMENT '名义价',
  `tuo_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '金托类型',
  `zhushi` varchar(50) DEFAULT NULL COMMENT '主石',
  `zhushilishu` varchar(11) NOT NULL DEFAULT '0' COMMENT '主石粒数',
  `zuanshidaxiao` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '主石大小',
  `zhengshuhao` varchar(100) DEFAULT NULL COMMENT '证书号',
  `order_goods_id` varchar(10) NOT NULL DEFAULT '0' COMMENT '是否绑定',
  `box_sn` varchar(30) NOT NULL COMMENT '柜位',
  `bill_type` varchar(4) NOT NULL DEFAULT '1' COMMENT '单据类型',
  `bill_no` char(17) NOT NULL COMMENT '单据编号',
  `action_name` varchar(30) NOT NULL COMMENT '发生动作',
  `action_time` datetime NOT NULL COMMENT '发生时间',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for base_coupon
-- ----------------------------
DROP TABLE IF EXISTS `base_coupon`;
CREATE TABLE `base_coupon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '优惠券自增id',
  `coupon_code` varchar(30) NOT NULL COMMENT '优惠券码',
  `coupon_price` decimal(10,2) NOT NULL COMMENT '优惠券等价金额',
  `coupon_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '优惠券类型',
  `coupon_policy` int(8) NOT NULL DEFAULT '1' COMMENT '优惠券所属政策',
  `coupon_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '优惠券状态；1，有效；2，已使用；3，作废',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `create_user` varchar(20) NOT NULL COMMENT '创建人',
  `exchange_user` varchar(20) DEFAULT NULL COMMENT '兑换人',
  `order_sn` varchar(30) DEFAULT NULL COMMENT '绑定的订单号',
  `use_time` datetime DEFAULT NULL COMMENT '使用时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=211 DEFAULT CHARSET=utf8 COMMENT='优惠券';

-- ----------------------------
-- Table structure for base_cpdz_code
-- ----------------------------
DROP TABLE IF EXISTS `base_cpdz_code`;
CREATE TABLE `base_cpdz_code` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `code` varchar(11) DEFAULT NULL COMMENT '成品定制码',
  `price` decimal(10,2) unsigned NOT NULL COMMENT '定制码金额',
  `style_channel_id` varchar(255) NOT NULL COMMENT '款式来源渠道（销售渠道）',
  `style_channel` varchar(255) DEFAULT NULL COMMENT '款式来源渠道 名称（销售渠道）',
  `order_detail_id` int(10) unsigned DEFAULT NULL COMMENT '订单商品主键ID',
  `create_user` varchar(30) DEFAULT NULL COMMENT '添加人',
  `create_time` datetime DEFAULT NULL COMMENT '添加时间',
  `use_status` tinyint(255) unsigned NOT NULL DEFAULT '1' COMMENT '使用状态：1未使用 2使用中 3已使用 ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `order_detail_id` (`order_detail_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=100661 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for base_good_info
-- ----------------------------
DROP TABLE IF EXISTS `base_good_info`;
CREATE TABLE `base_good_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `style_id` int(10) unsigned NOT NULL COMMENT '款式id',
  `style_sn` varchar(20) NOT NULL COMMENT '款式编号',
  `style_name` varchar(60) NOT NULL COMMENT '款式名称',
  `product_type` decimal(8,3) NOT NULL COMMENT '产品线',
  `style_cat` tinyint(3) NOT NULL COMMENT '款式分类',
  `goods_sn` varchar(60) NOT NULL COMMENT '产品编号',
  `goods_name` varchar(50) DEFAULT NULL COMMENT '货品名称',
  `in_on_sale` int(1) NOT NULL DEFAULT '1' COMMENT '上架状态 0下架 1上架',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for base_lz_discount_config
-- ----------------------------
DROP TABLE IF EXISTS `base_lz_discount_config`;
CREATE TABLE `base_lz_discount_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `user_id` int(10) unsigned NOT NULL COMMENT '管理员ID[user表id]',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1.小于50分 ',
  `zhekou` decimal(5,2) NOT NULL DEFAULT '1.00' COMMENT '2.小于1克拉 ',
  `enabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '3.大于1克拉',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15875 DEFAULT CHARSET=utf8 COMMENT='折扣管理表';

-- ----------------------------
-- Table structure for base_lz_discount_config_copy
-- ----------------------------
DROP TABLE IF EXISTS `base_lz_discount_config_copy`;
CREATE TABLE `base_lz_discount_config_copy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `user_id` int(10) unsigned NOT NULL COMMENT '管理员ID[user表id]',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1.小于50分 ',
  `zhekou` decimal(5,2) NOT NULL DEFAULT '1.00' COMMENT '2.小于1克拉 ',
  `enabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '3.大于1克拉',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6834 DEFAULT CHARSET=utf8 COMMENT='折扣管理表';

-- ----------------------------
-- Table structure for base_member_info
-- ----------------------------
DROP TABLE IF EXISTS `base_member_info`;
CREATE TABLE `base_member_info` (
  `member_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '会员id',
  `country_id` int(10) DEFAULT NULL COMMENT '会员所在国家',
  `province_id` int(10) DEFAULT NULL COMMENT '会员所在省份',
  `city_id` int(10) DEFAULT NULL COMMENT '会员所在城市',
  `region_id` int(10) DEFAULT NULL COMMENT '会员所在区域',
  `source_id` int(10) DEFAULT NULL COMMENT 'sales_channels.id会员来源',
  `member_name` varchar(100) DEFAULT NULL COMMENT '会员名称',
  `department_id` int(10) unsigned DEFAULT NULL COMMENT '部门id',
  `customer_source_id` int(8) unsigned DEFAULT NULL COMMENT '客户来源',
  `mem_card_sn` varchar(50) DEFAULT NULL COMMENT '会员卡号',
  `member_phone` varchar(255) DEFAULT NULL COMMENT '会员电话',
  `member_age` tinyint(3) DEFAULT '0' COMMENT '会员年龄',
  `member_qq` char(255) DEFAULT NULL COMMENT '会员QQ',
  `member_email` char(255) DEFAULT NULL COMMENT '会员EMAIL',
  `member_aliww` char(255) DEFAULT NULL COMMENT '会员旺旺',
  `member_dudget` decimal(12,3) DEFAULT '0.000' COMMENT '会员预算',
  `member_maristatus` tinyint(2) DEFAULT '1' COMMENT '会员婚姻状况1单身2热恋3订婚4结婚5离婚',
  `member_address` varchar(255) DEFAULT NULL COMMENT '会员地址',
  `member_peference` tinyint(2) DEFAULT NULL COMMENT '会员喜好',
  `member_type` tinyint(2) DEFAULT NULL COMMENT '会员类型：0=无效会员,1=潜在会员,2=意向会员,3=订单会员;默认1',
  `member_truename` varchar(255) DEFAULT NULL COMMENT '会员真实姓名',
  `member_tel` varchar(255) DEFAULT NULL COMMENT '会员电话',
  `member_msn` varchar(255) DEFAULT NULL COMMENT '会员msn',
  `member_sex` tinyint(3) DEFAULT NULL COMMENT '会员性别',
  `member_birthday` date DEFAULT NULL COMMENT '会员生日',
  `member_wedding` date DEFAULT NULL COMMENT '结婚日期',
  `member_question` text,
  `member_answer` text,
  `reg_time` int(11) DEFAULT '0' COMMENT '申请时间',
  `last_login` int(11) DEFAULT '0' COMMENT '最后登录时间',
  `last_ip` varchar(30) DEFAULT '' COMMENT '会员最后登录ip',
  `visit_count` smallint(5) DEFAULT '0' COMMENT '会员登录次数',
  `head_img` varchar(100) DEFAULT '' COMMENT '会员头像',
  `make_order` varchar(30) DEFAULT '' COMMENT '登记人',
  `email_valid` tinyint(1) DEFAULT '1' COMMENT '邮箱是否验证1未验证2验证',
  `complete_info` tinyint(1) DEFAULT '1' COMMENT '信息是否完整1不完整2完整',
  `member_password` varchar(32) DEFAULT '' COMMENT '会员登录密码',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`member_id`),
  KEY `member_phone` (`member_phone`),
  KEY `reg_time` (`reg_time`),
  KEY `update_time` (`update_time`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB AUTO_INCREMENT=88145 DEFAULT CHARSET=utf8 COMMENT='会员信息表';

-- ----------------------------
-- Table structure for base_salepolicy_goods
-- ----------------------------
DROP TABLE IF EXISTS `base_salepolicy_goods`;
CREATE TABLE `base_salepolicy_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `goods_id` varchar(30) NOT NULL COMMENT '货号',
  `goods_sn` varchar(30) NOT NULL COMMENT '商品编码',
  `goods_name` varchar(60) NOT NULL COMMENT '商品名称',
  `isXianhuo` tinyint(1) NOT NULL DEFAULT '1' COMMENT '现货状态0是期货1是现货',
  `chengbenjia` decimal(10,2) NOT NULL COMMENT '成本价',
  `category` tinyint(3) NOT NULL COMMENT '分类',
  `product_type` tinyint(3) NOT NULL COMMENT '产品线',
  `add_time` datetime NOT NULL COMMENT '推送数据的时间',
  `is_sale` tinyint(1) NOT NULL DEFAULT '1' COMMENT '上架状态，1上架，0下架',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '销售策略类型，1：普通类型；2：打包类型',
  `is_base_style` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否是基本款：专门用于搜索可以匹配裸钻：1不是，2是',
  `xiangkou` varchar(10) DEFAULT NULL COMMENT '镶口',
  `is_valid` tinyint(1) NOT NULL DEFAULT '1' COMMENT '商品是否有效，1；有效；2：已销售；3：损益 4:款作废',
  `company` varchar(100) DEFAULT NULL COMMENT '公司',
  `warehouse` varchar(30) DEFAULT NULL COMMENT '仓库',
  `company_id` int(11) DEFAULT NULL COMMENT '公司ID',
  `warehouse_id` int(11) DEFAULT NULL COMMENT '仓库ID',
  `stone` decimal(8,2) DEFAULT NULL COMMENT '镶口',
  `finger` varchar(20) DEFAULT NULL COMMENT '手寸',
  `caizhi` int(2) DEFAULT NULL COMMENT '材质',
  `yanse` int(1) DEFAULT NULL COMMENT '颜色',
  `cate_g` tinyint(1) NOT NULL DEFAULT '0',
  `is_policy` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_id` (`goods_id`,`goods_sn`),
  KEY `product_type` (`product_type`),
  KEY `goods_id_2` (`goods_id`),
  KEY `goods_id_3` (`goods_id`,`is_sale`)
) ENGINE=InnoDB AUTO_INCREMENT=1253066 DEFAULT CHARSET=utf8 COMMENT='可以销售的商品';

-- ----------------------------
-- Table structure for base_salepolicy_goods_20161231
-- ----------------------------
DROP TABLE IF EXISTS `base_salepolicy_goods_20161231`;
CREATE TABLE `base_salepolicy_goods_20161231` (
  `id` int(10) NOT NULL DEFAULT '0' COMMENT '自增id',
  `goods_id` varchar(30) NOT NULL COMMENT '货号',
  `goods_sn` varchar(30) NOT NULL COMMENT '商品编码',
  `goods_name` varchar(60) NOT NULL COMMENT '商品名称',
  `isXianhuo` tinyint(1) NOT NULL DEFAULT '1' COMMENT '现货状态0是期货1是现货',
  `chengbenjia` decimal(10,2) NOT NULL COMMENT '成本价',
  `category` tinyint(3) NOT NULL COMMENT '分类',
  `product_type` tinyint(3) NOT NULL COMMENT '产品线',
  `add_time` datetime NOT NULL COMMENT '推送数据的时间',
  `is_sale` tinyint(1) NOT NULL DEFAULT '1' COMMENT '上架状态，1上架，0下架',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '销售策略类型，1：普通类型；2：打包类型',
  `is_base_style` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否是基本款：专门用于搜索可以匹配裸钻：1不是，2是',
  `xiangkou` varchar(10) DEFAULT NULL COMMENT '镶口',
  `is_valid` tinyint(1) NOT NULL DEFAULT '1' COMMENT '商品是否有效，1；有效；2：已销售；3：损益 4:款作废',
  `company` varchar(100) DEFAULT NULL COMMENT '公司',
  `warehouse` varchar(30) DEFAULT NULL COMMENT '仓库',
  `company_id` int(11) DEFAULT NULL COMMENT '公司ID',
  `warehouse_id` int(11) DEFAULT NULL COMMENT '仓库ID',
  `stone` decimal(8,2) DEFAULT NULL COMMENT '镶口',
  `finger` varchar(20) DEFAULT NULL COMMENT '手寸',
  `caizhi` int(2) DEFAULT NULL COMMENT '材质',
  `yanse` int(1) DEFAULT NULL COMMENT '颜色',
  `cate_g` tinyint(1) NOT NULL DEFAULT '0',
  `is_policy` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for base_salepolicy_info
-- ----------------------------
DROP TABLE IF EXISTS `base_salepolicy_info`;
CREATE TABLE `base_salepolicy_info` (
  `policy_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `policy_name` varchar(60) NOT NULL COMMENT '销售策略名称',
  `policy_start_time` date NOT NULL COMMENT '销售策略开始时间',
  `policy_end_time` date DEFAULT NULL COMMENT '销售策略结束时间',
  `create_time` datetime DEFAULT NULL COMMENT '记录创建时间',
  `create_user` varchar(20) DEFAULT NULL COMMENT '记录创建人',
  `create_remark` varchar(200) DEFAULT NULL COMMENT '记录创建备注',
  `check_user` varchar(20) DEFAULT NULL COMMENT '审核人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `zuofei_time` datetime DEFAULT NULL COMMENT '作废时间',
  `check_remark` varchar(200) DEFAULT NULL COMMENT '记录备注',
  `bsi_status` tinyint(1) DEFAULT NULL COMMENT '记录状态 1保存,2申请审核,3已审核,4取消',
  `is_delete` tinyint(1) DEFAULT '0' COMMENT '记录是否有效 0有效1无效',
  `is_together` tinyint(1) NOT NULL DEFAULT '1' COMMENT '策略类型：1，普通；2，打包',
  `jiajia` decimal(8,4) unsigned NOT NULL DEFAULT '1.0000' COMMENT '加价率',
  `sta_value` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '固定值',
  `is_default` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否为默认政策1为默认2位不是默认',
  `is_favourable` tinyint(11) NOT NULL DEFAULT '1',
  `product_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '产品线',
  `tuo_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '金托类型',
  `huopin_type` int(1) NOT NULL DEFAULT '2' COMMENT '货品类型',
  `cat_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '款式分类',
  `range_begin` char(25) NOT NULL DEFAULT '0' COMMENT '开始范围',
  `range_end` char(25) NOT NULL DEFAULT '0' COMMENT '结束范围',
  `zhushi_begin` char(25) NOT NULL DEFAULT '0' COMMENT '开始范围',
  `zhushi_end` char(25) NOT NULL DEFAULT '0' COMMENT '结束范围',
  `is_kuanprice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否按款定价 0不是1是',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_tsyd` tinyint(1) DEFAULT '0' COMMENT '是否天生一对',
  `product_type_id` int(10) DEFAULT '0' COMMENT '产品线id',
  `cat_type_id` int(10) DEFAULT '0' COMMENT '款式分类id',
  `xilie` text COMMENT '所属系列',
  `cert` text COMMENT '裸钻证书类型',
  `color` varchar(255) DEFAULT NULL,
  `clarity` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`policy_id`),
  KEY `policy_start_time` (`policy_start_time`,`policy_end_time`),
  KEY `policy_start_time_2` (`policy_start_time`,`policy_end_time`,`bsi_status`)
) ENGINE=InnoDB AUTO_INCREMENT=399 DEFAULT CHARSET=utf8 COMMENT='销售策略';

-- ----------------------------
-- Table structure for base_salepolicy_info_20161231
-- ----------------------------
DROP TABLE IF EXISTS `base_salepolicy_info_20161231`;
CREATE TABLE `base_salepolicy_info_20161231` (
  `policy_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '自增ID',
  `policy_name` varchar(60) NOT NULL COMMENT '销售策略名称',
  `policy_start_time` date NOT NULL COMMENT '销售策略开始时间',
  `policy_end_time` date DEFAULT NULL COMMENT '销售策略结束时间',
  `create_time` datetime DEFAULT NULL COMMENT '记录创建时间',
  `create_user` varchar(20) DEFAULT NULL COMMENT '记录创建人',
  `create_remark` varchar(200) DEFAULT NULL COMMENT '记录创建备注',
  `check_user` varchar(20) DEFAULT NULL COMMENT '审核人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `zuofei_time` datetime DEFAULT NULL COMMENT '作废时间',
  `check_remark` varchar(200) DEFAULT NULL COMMENT '记录备注',
  `bsi_status` tinyint(1) DEFAULT NULL COMMENT '记录状态 1保存,2申请审核,3已审核,4取消',
  `is_delete` tinyint(1) DEFAULT NULL COMMENT '记录是否有效 0有效1无效',
  `is_together` tinyint(1) NOT NULL DEFAULT '1' COMMENT '策略类型：1，普通；2，打包',
  `jiajia` decimal(8,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '加价率',
  `sta_value` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '固定值',
  `is_default` tinyint(1) NOT NULL DEFAULT '2' COMMENT '是否为默认政策1为默认2位不是默认',
  `is_favourable` tinyint(11) NOT NULL DEFAULT '1',
  `product_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '产品线',
  `tuo_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '金托类型',
  `huopin_type` int(1) NOT NULL DEFAULT '2' COMMENT '货品类型',
  `cat_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '款式分类',
  `range_begin` char(25) NOT NULL DEFAULT '0' COMMENT '开始范围',
  `range_end` char(25) NOT NULL DEFAULT '0' COMMENT '结束范围',
  `zhushi_begin` char(25) NOT NULL DEFAULT '0' COMMENT '开始范围',
  `zhushi_end` char(25) NOT NULL DEFAULT '0' COMMENT '结束范围',
  `is_kuanprice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否按款定价 0不是1是',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for base_salepolicy_info_20170317
-- ----------------------------
DROP TABLE IF EXISTS `base_salepolicy_info_20170317`;
CREATE TABLE `base_salepolicy_info_20170317` (
  `policy_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `policy_name` varchar(60) NOT NULL COMMENT '销售策略名称',
  `policy_start_time` date NOT NULL COMMENT '销售策略开始时间',
  `policy_end_time` date DEFAULT NULL COMMENT '销售策略结束时间',
  `create_time` datetime DEFAULT NULL COMMENT '记录创建时间',
  `create_user` varchar(20) DEFAULT NULL COMMENT '记录创建人',
  `create_remark` varchar(200) DEFAULT NULL COMMENT '记录创建备注',
  `check_user` varchar(20) DEFAULT NULL COMMENT '审核人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `zuofei_time` datetime DEFAULT NULL COMMENT '作废时间',
  `check_remark` varchar(200) DEFAULT NULL COMMENT '记录备注',
  `bsi_status` tinyint(1) DEFAULT NULL COMMENT '记录状态 1保存,2申请审核,3已审核,4取消',
  `is_delete` tinyint(1) DEFAULT NULL COMMENT '记录是否有效 0有效1无效',
  `is_together` tinyint(1) NOT NULL DEFAULT '1' COMMENT '策略类型：1，普通；2，打包',
  `jiajia` decimal(8,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '加价率',
  `sta_value` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '固定值',
  `is_default` tinyint(1) NOT NULL DEFAULT '2' COMMENT '是否为默认政策1为默认2位不是默认',
  `is_favourable` tinyint(11) NOT NULL DEFAULT '1',
  `product_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '产品线',
  `tuo_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '金托类型',
  `huopin_type` int(1) NOT NULL DEFAULT '2' COMMENT '货品类型',
  `cat_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '款式分类',
  `range_begin` char(25) NOT NULL DEFAULT '0' COMMENT '开始范围',
  `range_end` char(25) NOT NULL DEFAULT '0' COMMENT '结束范围',
  `zhushi_begin` char(25) NOT NULL DEFAULT '0' COMMENT '开始范围',
  `zhushi_end` char(25) NOT NULL DEFAULT '0' COMMENT '结束范围',
  `is_kuanprice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否按款定价 0不是1是',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_tsyd` int(11) DEFAULT '0' COMMENT '是否天生一对',
  `product_type_id` int(10) DEFAULT '0' COMMENT '产品线id',
  `cat_type_id` int(10) DEFAULT '0' COMMENT '款式分类id',
  `xilie` text COMMENT '所属系列',
  `cert` text COMMENT '裸钻证书类型',
  PRIMARY KEY (`policy_id`),
  KEY `policy_start_time` (`policy_start_time`,`policy_end_time`),
  KEY `policy_start_time_2` (`policy_start_time`,`policy_end_time`,`bsi_status`)
) ENGINE=InnoDB AUTO_INCREMENT=1718 DEFAULT CHARSET=utf8 COMMENT='销售策略';

-- ----------------------------
-- Table structure for base_salepolicy_info_bak_0428
-- ----------------------------
DROP TABLE IF EXISTS `base_salepolicy_info_bak_0428`;
CREATE TABLE `base_salepolicy_info_bak_0428` (
  `policy_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '自增ID',
  `policy_name` varchar(60) NOT NULL COMMENT '销售策略名称',
  `policy_start_time` date NOT NULL COMMENT '销售策略开始时间',
  `policy_end_time` date DEFAULT NULL COMMENT '销售策略结束时间',
  `create_time` datetime DEFAULT NULL COMMENT '记录创建时间',
  `create_user` varchar(20) DEFAULT NULL COMMENT '记录创建人',
  `create_remark` varchar(200) DEFAULT NULL COMMENT '记录创建备注',
  `check_user` varchar(20) DEFAULT NULL COMMENT '审核人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `zuofei_time` datetime DEFAULT NULL COMMENT '作废时间',
  `check_remark` varchar(200) DEFAULT NULL COMMENT '记录备注',
  `bsi_status` tinyint(1) DEFAULT NULL COMMENT '记录状态 1保存,2申请审核,3已审核,4取消',
  `is_delete` tinyint(1) DEFAULT NULL COMMENT '记录是否有效 0有效1无效',
  `is_together` tinyint(1) NOT NULL DEFAULT '1' COMMENT '策略类型：1，普通；2，打包',
  `jiajia` decimal(8,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '加价率',
  `sta_value` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '固定值',
  `is_default` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否为默认政策1为默认2位不是默认',
  `is_favourable` tinyint(11) NOT NULL DEFAULT '1',
  `product_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '产品线',
  `tuo_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '金托类型',
  `huopin_type` int(1) NOT NULL DEFAULT '2' COMMENT '货品类型',
  `cat_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '款式分类',
  `range_begin` char(25) NOT NULL DEFAULT '0' COMMENT '开始范围',
  `range_end` char(25) NOT NULL DEFAULT '0' COMMENT '结束范围',
  `zhushi_begin` char(25) NOT NULL DEFAULT '0' COMMENT '开始范围',
  `zhushi_end` char(25) NOT NULL DEFAULT '0' COMMENT '结束范围',
  `is_kuanprice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否按款定价 0不是1是',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_tsyd` int(11) DEFAULT '0' COMMENT '是否天生一对',
  `product_type_id` int(10) DEFAULT '0' COMMENT '产品线id',
  `cat_type_id` int(10) DEFAULT '0' COMMENT '款式分类id',
  `xilie` text COMMENT '所属系列',
  `cert` text COMMENT '裸钻证书类型',
  `color` varchar(255) DEFAULT NULL,
  `clarity` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for base_style_company_log
-- ----------------------------
DROP TABLE IF EXISTS `base_style_company_log`;
CREATE TABLE `base_style_company_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `newdo` varchar(20) DEFAULT NULL COMMENT '新值',
  `olddo` varchar(20) DEFAULT NULL COMMENT '老值',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `modifi_user` varchar(20) DEFAULT NULL COMMENT '操作人',
  `style_sn` varchar(20) DEFAULT NULL COMMENT '款式',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COMMENT='款式库公司类型修改记录表';

-- ----------------------------
-- Table structure for base_style_info
-- ----------------------------
DROP TABLE IF EXISTS `base_style_info`;
CREATE TABLE `base_style_info` (
  `style_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '款式ID',
  `style_sn` varchar(20) NOT NULL COMMENT '款式编号',
  `style_name` varchar(60) NOT NULL COMMENT '款式名称',
  `product_type` tinyint(2) DEFAULT NULL COMMENT '产品线:app_product_type',
  `style_type` tinyint(2) NOT NULL COMMENT '款式分类:app_cat_type',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `modify_time` datetime NOT NULL COMMENT '更新时间',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `cancel_time` datetime DEFAULT NULL COMMENT '作废时间',
  `check_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '审核状态:1保存2提交申请3审核4未通过5作废',
  `is_sales` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否销售，0：否，1：是',
  `is_made` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否定制，0：否，1：是',
  `dismantle_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否拆货:1=正常 2=允许拆货 3=已拆货',
  `style_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '记录状态',
  `style_remark` text COMMENT '记录备注',
  `dapei_goods_sn` varchar(60) DEFAULT NULL COMMENT '搭配套系名称',
  `changbei_sn` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否常备款;1,是；2,否',
  `style_sex` tinyint(1) NOT NULL DEFAULT '1' COMMENT '款式性别;1:男；2：女；3：中性',
  `xilie` varchar(50) DEFAULT NULL COMMENT '系列',
  `market_xifen` varchar(50) DEFAULT NULL COMMENT '市场细分',
  `is_zp` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否是赠品；1否，2是',
  `is_new` tinyint(4) DEFAULT NULL COMMENT '导数据用',
  `ori_goods_sn` varchar(20) DEFAULT NULL COMMENT '老款号',
  `sell_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '畅销度',
  `bang_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '绑定1：需要绑定，2：不需要绑定',
  `sale_way` char(2) NOT NULL DEFAULT '1' COMMENT '可销售渠道. 1线上，2线下',
  `is_xz` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否销账,2.是.1否',
  `zp_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '赠品售价',
  `is_allow_favorable` tinyint(1) unsigned NOT NULL COMMENT '是否允许改价',
  `is_gold` tinyint(3) unsigned NOT NULL COMMENT '是否是黄金 0:非黄金，1:瑞金 2:3D  3:一口价',
  `is_support_style` tinyint(3) unsigned DEFAULT NULL COMMENT '是否支持按款销售',
  `company_type_id` varchar(30) DEFAULT NULL,
  `is_auto` tinyint(1) DEFAULT NULL COMMENT '是否自动生成款号1是',
  `jiajialv` decimal(8,2) DEFAULT NULL,
  `is_wukong` tinyint(2) DEFAULT '0' COMMENT '是否物控款式 1 物控款  2 正常款',
  `goods_content` text CHARACTER SET utf16le COMMENT '款式商品详情',
  `goods_salenum` int(11) DEFAULT NULL COMMENT '商品销量',
  `goods_click` int(10) unsigned DEFAULT NULL COMMENT '商品点击数',
  `is_recommend` tinyint(1) unsigned DEFAULT '0' COMMENT '是否推荐 默认0  1推荐 0不推荐',
  PRIMARY KEY (`style_id`),
  UNIQUE KEY `style_sn` (`style_sn`) USING BTREE,
  KEY `product_type` (`product_type`),
  KEY `create_time` (`create_time`),
  KEY `is_made` (`is_made`),
  KEY `style_type` (`style_type`),
  KEY `product_type_2` (`product_type`,`style_type`)
) ENGINE=InnoDB AUTO_INCREMENT=34879 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for base_style_info20170502
-- ----------------------------
DROP TABLE IF EXISTS `base_style_info20170502`;
CREATE TABLE `base_style_info20170502` (
  `goods_sn` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for base_style_log
-- ----------------------------
DROP TABLE IF EXISTS `base_style_log`;
CREATE TABLE `base_style_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `style_id` int(10) unsigned NOT NULL COMMENT '款id',
  `create_user` varchar(30) NOT NULL DEFAULT '' COMMENT '操作人',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '操作时间',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '操作备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12535 DEFAULT CHARSET=utf8 COMMENT='款的日志';

-- ----------------------------
-- Table structure for base_style_log_beifen
-- ----------------------------
DROP TABLE IF EXISTS `base_style_log_beifen`;
CREATE TABLE `base_style_log_beifen` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `style_id` int(10) unsigned NOT NULL COMMENT '款id',
  `create_user` varchar(30) NOT NULL DEFAULT '' COMMENT '操作人',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '操作时间',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '操作备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3460 DEFAULT CHARSET=utf8 COMMENT='款的日志';

-- ----------------------------
-- Table structure for bespoke_info_bak
-- ----------------------------
DROP TABLE IF EXISTS `bespoke_info_bak`;
CREATE TABLE `bespoke_info_bak` (
  `create_time` datetime COMMENT '操作时间',
  `create_user` varchar(20) NOT NULL COMMENT '操作人  ',
  `remark` text COMMENT '备注',
  `bespoke_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '预约ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for business_scope
-- ----------------------------
DROP TABLE IF EXISTS `business_scope`;
CREATE TABLE `business_scope` (
  `id` int(11) NOT NULL COMMENT '序号',
  `business` varchar(50) DEFAULT NULL COMMENT '经营项目',
  `parent_id` int(11) DEFAULT NULL COMMENT '所属项目',
  `add_id` int(11) DEFAULT NULL COMMENT '创建人',
  `add_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `is_deleted` tinyint(4) DEFAULT NULL COMMENT '删除标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='经营范围';

-- ----------------------------
-- Table structure for crm_opportunity
-- ----------------------------
DROP TABLE IF EXISTS `crm_opportunity`;
CREATE TABLE `crm_opportunity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL COMMENT '会员ID',
  `user_name` varchar(50) DEFAULT NULL COMMENT '姓名',
  `gender` tinyint(2) DEFAULT '1' COMMENT '性别 ''1''-male, ''2''-female,''0''-unknown',
  `wechat` varchar(255) DEFAULT NULL COMMENT '微信号',
  `cellphone` varchar(20) NOT NULL COMMENT '手机号',
  `email` varchar(80) DEFAULT NULL COMMENT '邮箱',
  `qq` varchar(12) DEFAULT NULL COMMENT 'qq号',
  `action` varchar(255) DEFAULT NULL COMMENT '用户操作',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注信息',
  `is_public` tinyint(1) unsigned DEFAULT '0' COMMENT '公海客户',
  `last_track_time` datetime DEFAULT NULL COMMENT '最后跟踪时间',
  `last_remark` text COMMENT '最好跟踪日志',
  `creat_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '数据更新的时间戳',
  `record_type` tinyint(3) NOT NULL COMMENT '客户阶段',
  `from` int(11) NOT NULL COMMENT '客户来源,对应customer_sources表',
  `cs_account` char(20) DEFAULT NULL COMMENT '所属顾问账号',
  `shop_id` int(11) DEFAULT NULL COMMENT '所属店铺',
  `channel_id` int(11) DEFAULT NULL COMMENT '所属渠道ID',
  PRIMARY KEY (`id`),
  KEY `member_id` (`member_id`),
  KEY `cellphone` (`cellphone`),
  KEY `user_name` (`user_name`),
  KEY `channel_id` (`channel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商机';

-- ----------------------------
-- Table structure for dealer_customer_follow
-- ----------------------------
DROP TABLE IF EXISTS `dealer_customer_follow`;
CREATE TABLE `dealer_customer_follow` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID号',
  `did` int(10) unsigned NOT NULL COMMENT '经销商客户管理表ID',
  `content` text NOT NULL COMMENT '跟进情况',
  `created_time` datetime NOT NULL COMMENT '创建时间',
  `modified_time` datetime NOT NULL COMMENT '修改时间',
  `follow_name` varchar(20) NOT NULL COMMENT '添加人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3171 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for dealer_customer_follow_bak
-- ----------------------------
DROP TABLE IF EXISTS `dealer_customer_follow_bak`;
CREATE TABLE `dealer_customer_follow_bak` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID号',
  `did` int(10) unsigned NOT NULL COMMENT '经销商客户管理表ID',
  `content` text NOT NULL COMMENT '跟进情况',
  `created_time` datetime NOT NULL COMMENT '创建时间',
  `modified_time` datetime NOT NULL COMMENT '修改时间',
  `follow_name` varchar(20) NOT NULL COMMENT '添加人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3002 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for dealer_customer_manage
-- ----------------------------
DROP TABLE IF EXISTS `dealer_customer_manage`;
CREATE TABLE `dealer_customer_manage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID号',
  `customer_name` varchar(50) NOT NULL COMMENT '准客户姓名',
  `status` varchar(80) NOT NULL COMMENT '状态(文本)',
  `source` varchar(100) DEFAULT NULL,
  `source_channel` varchar(100) NOT NULL COMMENT '来源渠道',
  `tel` varchar(50) NOT NULL COMMENT '联系电话',
  `email` varchar(80) NOT NULL COMMENT '联系邮箱',
  `province` varchar(30) NOT NULL COMMENT '省',
  `city` varchar(30) NOT NULL COMMENT '市',
  `district` varchar(50) NOT NULL COMMENT '县区',
  `shop_nums` varchar(30) NOT NULL COMMENT '意向开店数',
  `investment_amount` varchar(50) NOT NULL COMMENT '投资金额',
  `info` text NOT NULL COMMENT '其他信息',
  `follow_upper_id` varchar(100) NOT NULL COMMENT '跟进员ID,逗号隔开',
  `created_time` datetime NOT NULL COMMENT '创建时间',
  `modified_time` datetime NOT NULL COMMENT '修改时间',
  `spread_id` varchar(100) DEFAULT NULL COMMENT '推广专员，逗号隔开',
  `text_item` varchar(100) DEFAULT NULL COMMENT '项目',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3376 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for dealer_customer_manage_bak
-- ----------------------------
DROP TABLE IF EXISTS `dealer_customer_manage_bak`;
CREATE TABLE `dealer_customer_manage_bak` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID号',
  `customer_name` varchar(50) NOT NULL COMMENT '准客户姓名',
  `status` varchar(80) NOT NULL COMMENT '状态(文本)',
  `source` varchar(100) DEFAULT NULL,
  `source_channel` varchar(100) NOT NULL COMMENT '来源渠道',
  `tel` varchar(50) NOT NULL COMMENT '联系电话',
  `email` varchar(80) NOT NULL COMMENT '联系邮箱',
  `province` varchar(30) NOT NULL COMMENT '省',
  `city` varchar(30) NOT NULL COMMENT '市',
  `district` varchar(50) NOT NULL COMMENT '县区',
  `shop_nums` varchar(30) NOT NULL COMMENT '意向开店数',
  `investment_amount` varchar(50) NOT NULL COMMENT '投资金额',
  `info` text NOT NULL COMMENT '其他信息',
  `follow_upper_id` varchar(100) NOT NULL COMMENT '跟进员ID,逗号隔开',
  `created_time` datetime NOT NULL COMMENT '创建时间',
  `modified_time` datetime NOT NULL COMMENT '修改时间',
  `spread_id` varchar(100) DEFAULT NULL COMMENT '推广专员，逗号隔开',
  `text_item` varchar(100) DEFAULT NULL COMMENT '项目',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3273 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_4c_tejia
-- ----------------------------
DROP TABLE IF EXISTS `diamond_4c_tejia`;
CREATE TABLE `diamond_4c_tejia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) DEFAULT NULL,
  `shape` varchar(60) DEFAULT NULL COMMENT '形状',
  `carat` varchar(60) DEFAULT NULL COMMENT '石重',
  `color` varchar(60) DEFAULT NULL COMMENT '颜色',
  `clarity` varchar(60) DEFAULT NULL COMMENT '净度',
  `cut` varchar(60) DEFAULT NULL COMMENT '切工',
  `symmetry` varchar(60) DEFAULT NULL COMMENT '对称',
  `polish` varchar(60) DEFAULT NULL COMMENT '抛光',
  `fluorescence` varchar(60) DEFAULT NULL COMMENT '荧光',
  `special_price` decimal(10,2) DEFAULT '0.00',
  `city` varchar(60) DEFAULT NULL COMMENT '地区城市',
  `carat_min` varchar(10) DEFAULT NULL,
  `carat_max` varchar(10) DEFAULT NULL,
  `end_time` datetime DEFAULT NULL COMMENT '活动结束时间(默认为null表示无期限)',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_channel_jiajialv
-- ----------------------------
DROP TABLE IF EXISTS `diamond_channel_jiajialv`;
CREATE TABLE `diamond_channel_jiajialv` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` int(10) unsigned NOT NULL COMMENT '销售渠道',
  `cert` varchar(10) DEFAULT NULL,
  `good_type` tinyint(1) unsigned NOT NULL COMMENT '货品类型1现货2期货',
  `carat_min` decimal(5,2) unsigned NOT NULL COMMENT '最小钻重',
  `carat_max` decimal(5,2) unsigned NOT NULL COMMENT '最大钻重',
  `jiajialv` decimal(4,3) DEFAULT '1.000' COMMENT '加价率',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：1启用 0停用',
  PRIMARY KEY (`id`),
  KEY `channel` (`channel_id`)
) ENGINE=MyISAM AUTO_INCREMENT=712 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_color_from_ad
-- ----------------------------
DROP TABLE IF EXISTS `diamond_color_from_ad`;
CREATE TABLE `diamond_color_from_ad` (
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `from_ad` tinyint(3) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_fourc_info
-- ----------------------------
DROP TABLE IF EXISTS `diamond_fourc_info`;
CREATE TABLE `diamond_fourc_info` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `shape` tinyint(2) DEFAULT NULL COMMENT '形状',
  `carat_min` double DEFAULT NULL COMMENT '最小石重',
  `carat_max` double DEFAULT NULL COMMENT '最大石重',
  `color` varchar(10) DEFAULT NULL COMMENT '颜色',
  `clarity` varchar(10) DEFAULT NULL COMMENT '净度',
  `cert` varchar(10) DEFAULT NULL COMMENT '证书类型',
  `price` decimal(10,2) DEFAULT NULL COMMENT '价格',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态 ：1启用，2禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for diamond_from_ad
-- ----------------------------
DROP TABLE IF EXISTS `diamond_from_ad`;
CREATE TABLE `diamond_from_ad` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `from_ad` tinyint(3) unsigned NOT NULL COMMENT '来源id',
  `title` varchar(50) DEFAULT NULL COMMENT 'name of ad',
  `enabled` tinyint(1) NOT NULL COMMENT '启用状态0关1开',
  `show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '供应商开关是否显示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='裸钻供应商期货开关';

-- ----------------------------
-- Table structure for diamond_info
-- ----------------------------
DROP TABLE IF EXISTS `diamond_info`;
CREATE TABLE `diamond_info` (
  `goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` varchar(10) NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(20) DEFAULT NULL,
  `table_lv` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `pifajia` decimal(15,3) DEFAULT '0.000',
  `pifajia_mode` varchar(10) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL COMMENT '图片',
  PRIMARY KEY (`goods_id`),
  UNIQUE KEY `cert_id` (`cert_id`),
  UNIQUE KEY `cert` (`cert`,`cert_id`) USING BTREE,
  KEY `kuan_sn` (`kuan_sn`),
  KEY `gemx_zhengshu` (`gemx_zhengshu`),
  KEY `clarity` (`clarity`),
  KEY `carat` (`carat`),
  KEY `cut` (`cut`),
  KEY `color` (`color`),
  KEY `shape` (`shape`),
  KEY `shop_price` (`shop_price`),
  KEY `good_type` (`good_type`),
  KEY `goods_sn` (`goods_sn`) USING BTREE,
  KEY `down_zk` (`cert`,`status`) USING BTREE,
  FULLTEXT KEY `cert_2` (`cert`)
) ENGINE=MyISAM AUTO_INCREMENT=505921519 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_info201803120
-- ----------------------------
DROP TABLE IF EXISTS `diamond_info201803120`;
CREATE TABLE `diamond_info201803120` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` varchar(10) NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(20) DEFAULT NULL,
  `table_lv` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `pifajia` decimal(15,3) DEFAULT '0.000',
  `pifajia_mode` varchar(10) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL COMMENT '图片'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_info_3d
-- ----------------------------
DROP TABLE IF EXISTS `diamond_info_3d`;
CREATE TABLE `diamond_info_3d` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `img` varchar(200) DEFAULT NULL COMMENT '图片',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `cert_id` (`cert_id`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_info_all
-- ----------------------------
DROP TABLE IF EXISTS `diamond_info_all`;
CREATE TABLE `diamond_info_all` (
  `goods_id` int(10) NOT NULL,
  `goods_sn` varchar(60) DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned DEFAULT '0' COMMENT '商品数量',
  `from_ad` varchar(10) DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double DEFAULT NULL COMMENT '石重',
  `clarity` varchar(40) DEFAULT NULL COMMENT '净度',
  `cut` varchar(10) DEFAULT NULL COMMENT '切工',
  `color` varchar(10) DEFAULT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(20) DEFAULT NULL,
  `table_lv` varchar(20) DEFAULT NULL,
  `symmetry` varchar(10) DEFAULT NULL COMMENT '对称',
  `polish` varchar(10) DEFAULT NULL COMMENT '抛光',
  `fluorescence` varchar(10) DEFAULT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) DEFAULT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) DEFAULT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `is_active` tinyint(1) DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `pifajia` decimal(15,3) DEFAULT '0.000',
  `pifajia_mode` varchar(10) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL COMMENT '图片',
  UNIQUE KEY `cert_id` (`cert_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_info_log
-- ----------------------------
DROP TABLE IF EXISTS `diamond_info_log`;
CREATE TABLE `diamond_info_log` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `from_ad` tinyint(1) NOT NULL COMMENT '1=51钻，2=BDD',
  `operation_type` tinyint(1) NOT NULL COMMENT '1添加，2修改，3上架，4下架',
  `operation_content` varchar(200) NOT NULL COMMENT '操作内容',
  `create_time` datetime NOT NULL COMMENT '操作时间',
  `create_user` varchar(10) NOT NULL COMMENT '操作人',
  `cert_id` varchar(30) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `pifajia` decimal(15,3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=588 DEFAULT CHARSET=utf8 COMMENT='操作记录表';

-- ----------------------------
-- Table structure for diamond_info_md
-- ----------------------------
DROP TABLE IF EXISTS `diamond_info_md`;
CREATE TABLE `diamond_info_md` (
  `goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` varchar(10) NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(20) DEFAULT NULL,
  `table_lv` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `pifajia` decimal(15,3) DEFAULT '0.000',
  `pifajia_mode` varchar(10) DEFAULT NULL,
  `img` varchar(200) DEFAULT NULL COMMENT '图片',
  `is_hot` tinyint(255) DEFAULT NULL,
  `carat_min` decimal(5,2) DEFAULT NULL,
  `carat_max` decimal(5,2) DEFAULT NULL,
  `sid` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`goods_id`) USING BTREE,
  UNIQUE KEY `cert_id` (`cert_id`) USING BTREE,
  UNIQUE KEY `cert` (`cert`,`cert_id`) USING BTREE,
  KEY `kuan_sn` (`kuan_sn`) USING BTREE,
  KEY `gemx_zhengshu` (`gemx_zhengshu`) USING BTREE,
  KEY `clarity` (`clarity`) USING BTREE,
  KEY `carat` (`carat`) USING BTREE,
  KEY `cut` (`cut`) USING BTREE,
  KEY `color` (`color`) USING BTREE,
  KEY `shape` (`shape`) USING BTREE,
  KEY `shop_price` (`shop_price`) USING BTREE,
  KEY `good_type` (`good_type`) USING BTREE,
  KEY `goods_sn` (`goods_sn`) USING BTREE,
  KEY `down_zk` (`cert`,`status`) USING BTREE,
  KEY `cert_2` (`cert`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=501011286 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_info_prevent
-- ----------------------------
DROP TABLE IF EXISTS `diamond_info_prevent`;
CREATE TABLE `diamond_info_prevent` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cert_id` varchar(30) NOT NULL COMMENT '过滤证书号 --取自diamond_info.cert_id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1044 DEFAULT CHARSET=utf8 COMMENT='裸钻过滤表';

-- ----------------------------
-- Table structure for diamond_info_temp
-- ----------------------------
DROP TABLE IF EXISTS `diamond_info_temp`;
CREATE TABLE `diamond_info_temp` (
  `goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '商品编码',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '商品数量',
  `from_ad` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '货品来源',
  `good_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '货品类型：1现货2期货',
  `market_price` decimal(10,2) unsigned DEFAULT NULL,
  `shop_price` decimal(10,2) DEFAULT NULL,
  `member_price` decimal(10,2) DEFAULT NULL,
  `chengben_jia` decimal(10,2) DEFAULT NULL,
  `carat` double NOT NULL COMMENT '石重',
  `clarity` varchar(40) NOT NULL COMMENT '净度',
  `cut` varchar(10) NOT NULL COMMENT '切工',
  `color` varchar(10) NOT NULL COMMENT '颜色',
  `shape` tinyint(3) DEFAULT NULL COMMENT '形状',
  `depth_lv` varchar(10) DEFAULT NULL,
  `table_lv` varchar(10) DEFAULT NULL,
  `symmetry` varchar(10) NOT NULL COMMENT '对称',
  `polish` varchar(10) NOT NULL COMMENT '抛光',
  `fluorescence` varchar(10) NOT NULL COMMENT '荧光',
  `warehouse` varchar(50) DEFAULT NULL COMMENT '库房',
  `guojibaojia` decimal(10,2) DEFAULT NULL,
  `cts` decimal(10,2) DEFAULT NULL,
  `us_price_source` decimal(10,2) DEFAULT NULL,
  `source_discount` decimal(10,4) DEFAULT NULL,
  `cert` varchar(10) NOT NULL COMMENT '证书号类型',
  `cert_id` varchar(30) NOT NULL COMMENT '证书号',
  `gemx_zhengshu` varchar(50) DEFAULT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1上架 2下架',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常 2活动',
  `kuan_sn` varchar(20) DEFAULT NULL COMMENT '天生一对款号',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  PRIMARY KEY (`goods_id`),
  UNIQUE KEY `cert_id` (`cert_id`),
  UNIQUE KEY `goods_sn` (`goods_sn`),
  KEY `cert` (`cert`,`cert_id`),
  KEY `kuan_sn` (`kuan_sn`),
  KEY `gemx_zhengshu` (`gemx_zhengshu`),
  KEY `clarity` (`clarity`),
  KEY `carat` (`carat`),
  KEY `cut` (`cut`),
  KEY `color` (`color`),
  KEY `shape` (`shape`),
  KEY `shop_price` (`shop_price`),
  KEY `good_type` (`good_type`),
  FULLTEXT KEY `cert_2` (`cert`)
) ENGINE=MyISAM AUTO_INCREMENT=1441 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_jiajialv
-- ----------------------------
DROP TABLE IF EXISTS `diamond_jiajialv`;
CREATE TABLE `diamond_jiajialv` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `good_type` tinyint(1) unsigned NOT NULL COMMENT '货品类型1现货2期货',
  `from_ad` tinyint(1) unsigned NOT NULL COMMENT '来源 1=51钻,2BDD',
  `cert` varchar(10) NOT NULL COMMENT '证书类型',
  `carat_min` decimal(5,2) unsigned NOT NULL COMMENT '最小钻重',
  `carat_max` decimal(5,2) unsigned NOT NULL COMMENT '最大钻重',
  `jiajialv` decimal(4,3) NOT NULL COMMENT '加价率',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：1启用 0停用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1039 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_jiajialv2
-- ----------------------------
DROP TABLE IF EXISTS `diamond_jiajialv2`;
CREATE TABLE `diamond_jiajialv2` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `good_type` tinyint(1) unsigned NOT NULL COMMENT '货品类型1现货2期货',
  `from_ad` tinyint(1) unsigned NOT NULL COMMENT '来源 1=51钻,2BDD',
  `cert` varchar(10) NOT NULL COMMENT '证书类型',
  `carat_min` decimal(5,2) unsigned NOT NULL COMMENT '最小钻重',
  `carat_max` decimal(5,2) unsigned NOT NULL COMMENT '最大钻重',
  `jiajialv` decimal(4,3) NOT NULL COMMENT '加价率',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：1启用 0停用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=924 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_pf_jiajialv
-- ----------------------------
DROP TABLE IF EXISTS `diamond_pf_jiajialv`;
CREATE TABLE `diamond_pf_jiajialv` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `good_type` tinyint(1) unsigned DEFAULT NULL COMMENT '货品类型1现货2期货',
  `from_ad` varchar(10) NOT NULL COMMENT '来源 1=51钻,2BDD',
  `cert` varchar(10) DEFAULT NULL COMMENT '证书类型',
  `carat_min` decimal(5,2) unsigned NOT NULL COMMENT '最小钻重',
  `carat_max` decimal(5,2) unsigned NOT NULL COMMENT '最大钻重',
  `color` varchar(10) DEFAULT NULL COMMENT '颜色',
  `clarity` varchar(10) DEFAULT NULL COMMENT '净度',
  `jiajialv` decimal(4,3) DEFAULT '1.000' COMMENT '加价率',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：1启用 0停用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=136 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_price
-- ----------------------------
DROP TABLE IF EXISTS `diamond_price`;
CREATE TABLE `diamond_price` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `shape` varchar(20) NOT NULL,
  `clarity` varchar(5) NOT NULL,
  `color` varchar(5) NOT NULL,
  `min` decimal(4,2) NOT NULL,
  `max` decimal(4,2) NOT NULL,
  `price` decimal(9,2) NOT NULL,
  `addtime` datetime NOT NULL,
  `version` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '上传版本',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30903 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_ssy_tejia
-- ----------------------------
DROP TABLE IF EXISTS `diamond_ssy_tejia`;
CREATE TABLE `diamond_ssy_tejia` (
  `goods_id` varchar(20) NOT NULL COMMENT '货号',
  `cert_id` varchar(20) NOT NULL COMMENT '证书号',
  `special_price` decimal(10,0) unsigned NOT NULL DEFAULT '0' COMMENT '特价(双十一价格)',
  KEY `cert_id` (`cert_id`) USING BTREE,
  KEY `goods_id` (`goods_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='双十一特价钻表';

-- ----------------------------
-- Table structure for diamond_vendor
-- ----------------------------
DROP TABLE IF EXISTS `diamond_vendor`;
CREATE TABLE `diamond_vendor` (
  `vendor_id` int(3) NOT NULL COMMENT '供应商id',
  `title` varchar(50) NOT NULL COMMENT '供应商简称',
  `activate` int(1) NOT NULL DEFAULT '1' COMMENT '是否激活',
  `show` int(1) NOT NULL DEFAULT '1' COMMENT '后台是否显示开关',
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`vendor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='裸钻供应商';

-- ----------------------------
-- Table structure for dia_qihuo_tmp
-- ----------------------------
DROP TABLE IF EXISTS `dia_qihuo_tmp`;
CREATE TABLE `dia_qihuo_tmp` (
  `lot_no` varchar(255) DEFAULT NULL,
  `shape` varchar(255) DEFAULT NULL,
  `carat` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `clarity` varchar(255) DEFAULT NULL,
  `cut` varchar(255) DEFAULT NULL,
  `polish` varchar(255) DEFAULT NULL,
  `sym` varchar(255) DEFAULT NULL,
  `fluor` varchar(255) DEFAULT NULL,
  `cert_type` varchar(255) DEFAULT NULL,
  `cert_no` varchar(255) DEFAULT NULL,
  `gj_price` varchar(255) DEFAULT NULL,
  `zk` varchar(255) DEFAULT NULL,
  `us_price` varchar(255) DEFAULT NULL,
  `ori_price` varchar(255) DEFAULT NULL,
  `sale_price` varchar(255) NOT NULL,
  UNIQUE KEY `ccc` (`cert_no`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diy_xiangkou_config
-- ----------------------------
DROP TABLE IF EXISTS `diy_xiangkou_config`;
CREATE TABLE `diy_xiangkou_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `style_sn` varchar(255) DEFAULT NULL,
  `xiangkou` varchar(255) DEFAULT NULL,
  `carat_lower_limit` float(255,4) DEFAULT NULL COMMENT '石重下限',
  `carat_upper_limit` float(255,4) DEFAULT NULL COMMENT '石重上限',
  PRIMARY KEY (`id`),
  KEY `style_sn` (`style_sn`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_diamond_jia
-- ----------------------------
DROP TABLE IF EXISTS `ecs_diamond_jia`;
CREATE TABLE `ecs_diamond_jia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `xian_jiajia3` varchar(5) NOT NULL COMMENT '现货加价比例30',
  `xian_jiajia4` varchar(5) NOT NULL,
  `xian_jiajia5` varchar(5) NOT NULL,
  `xian_jiajia6` varchar(5) NOT NULL,
  `xian_jiajia7` varchar(5) NOT NULL,
  `xian_jiajia8` varchar(5) NOT NULL,
  `xian_jiajia9` varchar(5) NOT NULL,
  `xian_jiajia1` varchar(5) NOT NULL,
  `xian_jiajia2` varchar(5) NOT NULL,
  `qi_jiajia3` varchar(5) NOT NULL COMMENT '期货加价30',
  `qi_jiajia4` varchar(5) NOT NULL,
  `qi_jiajia5` varchar(5) NOT NULL,
  `qi_jiajia6` varchar(5) NOT NULL,
  `qi_jiajia7` varchar(5) NOT NULL,
  `qi_jiajia8` varchar(5) NOT NULL,
  `qi_jiajia9` varchar(5) NOT NULL,
  `qi_jiajia1` varchar(5) NOT NULL,
  `qi_jiajia2` varchar(5) NOT NULL,
  `source` varchar(50) NOT NULL,
  `cert` char(10) NOT NULL COMMENT '证书',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='裸钻按钻重调价';

-- ----------------------------
-- Table structure for goods_boss
-- ----------------------------
DROP TABLE IF EXISTS `goods_boss`;
CREATE TABLE `goods_boss` (
  `style_sn` varchar(20) NOT NULL COMMENT '款式编号',
  `goods_sn` varchar(60) NOT NULL COMMENT '产品编号',
  `style_name` varchar(60) COMMENT '款式名称',
  `product_type_name` char(50) COMMENT '产品线名称',
  `cat_type_name` char(50) COMMENT '部门名称',
  `caizhi` int(2) NOT NULL COMMENT '材质',
  `yanse` int(1) NOT NULL COMMENT '颜色',
  `xiangkou` varchar(10) NOT NULL COMMENT '镶口',
  `zhiquan` int(2) NOT NULL COMMENT '手寸',
  `dingzhichengben` decimal(10,2) NOT NULL COMMENT '定制成本',
  `goods_image` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for list_cancle_reason
-- ----------------------------
DROP TABLE IF EXISTS `list_cancle_reason`;
CREATE TABLE `list_cancle_reason` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `style_id` int(10) unsigned NOT NULL COMMENT '款id',
  `create_user` varchar(30) NOT NULL DEFAULT '' COMMENT '操作人',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '操作时间',
  `type` varchar(20) NOT NULL COMMENT '作废原因类型',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '操作备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3372 DEFAULT CHARSET=utf8 COMMENT='款的申请作废的原因';

-- ----------------------------
-- Table structure for list_style_goods
-- ----------------------------
DROP TABLE IF EXISTS `list_style_goods`;
CREATE TABLE `list_style_goods` (
  `goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_type_id` int(10) DEFAULT NULL COMMENT '产品线id',
  `cat_type_id` int(10) NOT NULL COMMENT '款式分类id',
  `style_id` int(10) unsigned DEFAULT NULL COMMENT '款式id',
  `style_sn` varchar(20) NOT NULL COMMENT '款式编号',
  `style_name` varchar(60) NOT NULL COMMENT '款式名称',
  `goods_sn` varchar(60) NOT NULL COMMENT '产品编号',
  `shoucun` int(2) NOT NULL COMMENT '手寸',
  `xiangkou` varchar(10) NOT NULL COMMENT '镶口',
  `caizhi` int(2) NOT NULL COMMENT '材质',
  `yanse` int(1) NOT NULL COMMENT '颜色',
  `zhushizhong` decimal(8,3) NOT NULL COMMENT '主石重',
  `zhushi_num` int(3) NOT NULL COMMENT '主石数',
  `fushizhong1` decimal(8,3) NOT NULL COMMENT '副石1重',
  `fushi_num1` int(3) NOT NULL COMMENT '副石1数',
  `fushizhong2` decimal(8,3) NOT NULL COMMENT '副石2重',
  `fushi_num2` int(3) NOT NULL COMMENT '副石2数',
  `fushizhong3` decimal(8,3) NOT NULL COMMENT '副石3重',
  `fushi_num3` int(3) NOT NULL COMMENT '副石3数',
  `fushi_chengbenjia_other` decimal(10,2) DEFAULT NULL COMMENT '其他副石副石成本价',
  `weight` decimal(8,3) NOT NULL COMMENT '材质金重',
  `jincha_shang` decimal(8,3) NOT NULL COMMENT '金重上公差',
  `jincha_xia` decimal(8,3) NOT NULL COMMENT '金重下公差',
  `dingzhichengben` decimal(10,2) NOT NULL COMMENT '定制成本',
  `is_ok` int(1) NOT NULL DEFAULT '1' COMMENT '是否上架;0为下架;1为上架',
  `last_update` datetime NOT NULL COMMENT '最后更新时间',
  `is_base_style` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否基本款(0：否，1：是)',
  `is_quick_diy` tinyint(1) unsigned DEFAULT '0' COMMENT '是否快速定制 1是 0否',
  `xiangkou_company_type` varchar(10) DEFAULT NULL COMMENT '可销售公司类型',
  PRIMARY KEY (`goods_id`),
  KEY `style_id` (`style_id`),
  KEY `goods_sn` (`goods_sn`),
  KEY `style_sn` (`style_sn`),
  KEY `style_id_2` (`style_id`,`caizhi`,`xiangkou`),
  KEY `product_type_id` (`product_type_id`),
  KEY `cat_type_id` (`cat_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1786874 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for list_style_goods_0313
-- ----------------------------
DROP TABLE IF EXISTS `list_style_goods_0313`;
CREATE TABLE `list_style_goods_0313` (
  `goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_type_id` int(10) DEFAULT NULL COMMENT '产品线id',
  `cat_type_id` int(10) NOT NULL COMMENT '款式分类id',
  `style_id` int(10) unsigned DEFAULT NULL COMMENT '款式id',
  `style_sn` varchar(20) NOT NULL COMMENT '款式编号',
  `style_name` varchar(60) NOT NULL COMMENT '款式名称',
  `goods_sn` varchar(60) NOT NULL COMMENT '产品编号',
  `shoucun` int(2) NOT NULL COMMENT '手寸',
  `xiangkou` varchar(10) NOT NULL COMMENT '镶口',
  `caizhi` int(2) NOT NULL COMMENT '材质',
  `yanse` int(1) NOT NULL COMMENT '颜色',
  `zhushizhong` decimal(8,3) NOT NULL COMMENT '主石重',
  `zhushi_num` int(3) NOT NULL COMMENT '主石数',
  `fushizhong1` decimal(8,3) NOT NULL COMMENT '副石1重',
  `fushi_num1` int(3) NOT NULL COMMENT '副石1数',
  `fushizhong2` decimal(8,3) NOT NULL COMMENT '副石2重',
  `fushi_num2` int(3) NOT NULL COMMENT '副石2数',
  `fushizhong3` decimal(8,3) NOT NULL COMMENT '副石3重',
  `fushi_num3` int(3) NOT NULL COMMENT '副石3数',
  `fushi_chengbenjia_other` decimal(10,2) DEFAULT NULL COMMENT '其他副石副石成本价',
  `weight` decimal(8,3) NOT NULL COMMENT '材质金重',
  `jincha_shang` decimal(8,3) NOT NULL COMMENT '金重上公差',
  `jincha_xia` decimal(8,3) NOT NULL COMMENT '金重下公差',
  `dingzhichengben` decimal(10,2) NOT NULL COMMENT '定制成本',
  `is_ok` int(1) NOT NULL DEFAULT '1' COMMENT '是否上架;0为下架;1为上架',
  `last_update` datetime NOT NULL COMMENT '最后更新时间',
  `is_base_style` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否基本款(0：否，1：是)',
  PRIMARY KEY (`goods_id`),
  KEY `style_sn` (`style_sn`) USING BTREE,
  KEY `style_id` (`style_id`) USING BTREE,
  KEY `product_type_id` (`product_type_id`) USING BTREE,
  KEY `cat_type_id` (`cat_type_id`) USING BTREE,
  KEY `style_name` (`style_name`) USING BTREE,
  KEY `goods_sn` (`goods_sn`) USING BTREE,
  KEY `caizhi` (`caizhi`) USING BTREE,
  KEY `shoucun` (`shoucun`) USING BTREE,
  KEY `yanse` (`yanse`) USING BTREE,
  KEY `xiangkou` (`xiangkou`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=941212 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for list_style_goods_170509
-- ----------------------------
DROP TABLE IF EXISTS `list_style_goods_170509`;
CREATE TABLE `list_style_goods_170509` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_type_id` int(10) DEFAULT NULL COMMENT '产品线id',
  `cat_type_id` int(10) NOT NULL COMMENT '款式分类id',
  `style_id` int(10) unsigned DEFAULT NULL COMMENT '款式id',
  `style_sn` varchar(20) NOT NULL COMMENT '款式编号',
  `style_name` varchar(60) NOT NULL COMMENT '款式名称',
  `goods_sn` varchar(60) NOT NULL COMMENT '产品编号',
  `shoucun` int(2) NOT NULL COMMENT '手寸',
  `xiangkou` varchar(10) NOT NULL COMMENT '镶口',
  `caizhi` int(2) NOT NULL COMMENT '材质',
  `yanse` int(1) NOT NULL COMMENT '颜色',
  `zhushizhong` decimal(8,3) NOT NULL COMMENT '主石重',
  `zhushi_num` int(3) NOT NULL COMMENT '主石数',
  `fushizhong1` decimal(8,3) NOT NULL COMMENT '副石1重',
  `fushi_num1` int(3) NOT NULL COMMENT '副石1数',
  `fushizhong2` decimal(8,3) NOT NULL COMMENT '副石2重',
  `fushi_num2` int(3) NOT NULL COMMENT '副石2数',
  `fushizhong3` decimal(8,3) NOT NULL COMMENT '副石3重',
  `fushi_num3` int(3) NOT NULL COMMENT '副石3数',
  `fushi_chengbenjia_other` decimal(10,2) DEFAULT NULL COMMENT '其他副石副石成本价',
  `weight` decimal(8,3) NOT NULL COMMENT '材质金重',
  `jincha_shang` decimal(8,3) NOT NULL COMMENT '金重上公差',
  `jincha_xia` decimal(8,3) NOT NULL COMMENT '金重下公差',
  `dingzhichengben` decimal(10,2) NOT NULL COMMENT '定制成本',
  `is_ok` int(1) NOT NULL DEFAULT '1' COMMENT '是否上架;0为下架;1为上架',
  `last_update` datetime NOT NULL COMMENT '最后更新时间',
  `is_base_style` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否基本款(0：否，1：是)',
  `is_quick_diy` tinyint(1) unsigned DEFAULT '0' COMMENT '是否快速定制 1是 0否'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for list_style_goods_bak
-- ----------------------------
DROP TABLE IF EXISTS `list_style_goods_bak`;
CREATE TABLE `list_style_goods_bak` (
  `goods_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_type_id` int(10) DEFAULT NULL COMMENT '产品线id',
  `cat_type_id` int(10) NOT NULL COMMENT '款式分类id',
  `style_id` int(10) unsigned DEFAULT NULL COMMENT '款式id',
  `style_sn` varchar(20) NOT NULL COMMENT '款式编号',
  `style_name` varchar(60) NOT NULL COMMENT '款式名称',
  `goods_sn` varchar(60) NOT NULL COMMENT '产品编号',
  `shoucun` int(2) NOT NULL COMMENT '手寸',
  `xiangkou` varchar(10) NOT NULL COMMENT '镶口',
  `caizhi` int(2) NOT NULL COMMENT '材质',
  `yanse` int(1) NOT NULL COMMENT '颜色',
  `zhushizhong` decimal(8,3) NOT NULL COMMENT '主石重',
  `zhushi_num` int(3) NOT NULL COMMENT '主石数',
  `fushizhong1` decimal(8,3) NOT NULL COMMENT '副石1重',
  `fushi_num1` int(3) NOT NULL COMMENT '副石1数',
  `fushizhong2` decimal(8,3) NOT NULL COMMENT '副石2重',
  `fushi_num2` int(3) NOT NULL COMMENT '副石2数',
  `fushizhong3` decimal(8,3) NOT NULL COMMENT '副石3重',
  `fushi_num3` int(3) NOT NULL COMMENT '副石3数',
  `fushi_chengbenjia_other` decimal(10,2) DEFAULT NULL COMMENT '其他副石副石成本价',
  `weight` decimal(8,3) NOT NULL COMMENT '材质金重',
  `jincha_shang` decimal(8,3) NOT NULL COMMENT '金重上公差',
  `jincha_xia` decimal(8,3) NOT NULL COMMENT '金重下公差',
  `dingzhichengben` decimal(10,2) NOT NULL COMMENT '定制成本',
  `is_ok` int(1) NOT NULL DEFAULT '1' COMMENT '是否上架;0为下架;1为上架',
  `last_update` datetime NOT NULL COMMENT '最后更新时间',
  `is_base_style` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否基本款(0：否，1：是)',
  PRIMARY KEY (`goods_id`),
  KEY `style_id` (`style_id`),
  KEY `style_sn` (`style_sn`),
  KEY `style_id_2` (`style_id`,`caizhi`,`xiangkou`),
  KEY `goods_sn` (`goods_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=5008832 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for list_to
-- ----------------------------
DROP TABLE IF EXISTS `list_to`;
CREATE TABLE `list_to` (
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `product_type_id` int(10) DEFAULT NULL COMMENT '产品线id',
  `cat_type_id` int(10) NOT NULL COMMENT '款式分类id',
  `style_id` int(10) unsigned DEFAULT NULL COMMENT '款式id',
  `style_sn` varchar(20) NOT NULL COMMENT '款式编号',
  `style_name` varchar(60) NOT NULL COMMENT '款式名称',
  `goods_sn` varchar(60) NOT NULL COMMENT '产品编号',
  `shoucun` int(2) NOT NULL COMMENT '手寸',
  `xiangkou` varchar(10) NOT NULL COMMENT '镶口',
  `caizhi` int(2) NOT NULL COMMENT '材质',
  `yanse` int(1) NOT NULL COMMENT '颜色',
  `zhushizhong` decimal(8,3) NOT NULL COMMENT '主石重',
  `zhushi_num` int(3) NOT NULL COMMENT '主石数',
  `fushizhong1` decimal(8,3) NOT NULL COMMENT '副石1重',
  `fushi_num1` int(3) NOT NULL COMMENT '副石1数',
  `fushizhong2` decimal(8,3) NOT NULL COMMENT '副石2重',
  `fushi_num2` int(3) NOT NULL COMMENT '副石2数',
  `fushizhong3` decimal(8,3) NOT NULL COMMENT '副石3重',
  `fushi_num3` int(3) NOT NULL COMMENT '副石3数',
  `fushi_chengbenjia_other` decimal(10,2) DEFAULT NULL COMMENT '其他副石副石成本价',
  `weight` decimal(8,3) NOT NULL COMMENT '材质金重',
  `jincha_shang` decimal(8,3) NOT NULL COMMENT '金重上公差',
  `jincha_xia` decimal(8,3) NOT NULL COMMENT '金重下公差',
  `dingzhichengben` decimal(10,2) NOT NULL COMMENT '定制成本',
  `is_ok` int(1) NOT NULL DEFAULT '1' COMMENT '是否上架;0为下架;1为上架',
  `last_update` datetime NOT NULL COMMENT '最后更新时间',
  `is_base_style` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否基本款(0：否，1：是)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for office_discount_config
-- ----------------------------
DROP TABLE IF EXISTS `office_discount_config`;
CREATE TABLE `office_discount_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `channel_id` int(10) unsigned NOT NULL COMMENT '渠道ID[sales_channels表id]',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1 小于50分 2 50分-1.5克拉 3 1.5克拉以上 4 成品 5 空托 ',
  `zhekou` decimal(5,2) NOT NULL DEFAULT '1.00' COMMENT '折扣率',
  `enabled` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 启用 0 停用',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='官网折扣表';

-- ----------------------------
-- Table structure for old_style
-- ----------------------------
DROP TABLE IF EXISTS `old_style`;
CREATE TABLE `old_style` (
  `goods_sn` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for rel_cat_attribute
-- ----------------------------
DROP TABLE IF EXISTS `rel_cat_attribute`;
CREATE TABLE `rel_cat_attribute` (
  `rel_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_type_id` int(10) NOT NULL COMMENT '分类名称id',
  `product_type_id` smallint(4) NOT NULL COMMENT '产品线id',
  `attribute_id` int(10) NOT NULL COMMENT '属性id',
  `is_show` tinyint(1) NOT NULL COMMENT '是否显示：1是0否',
  `is_default` tinyint(1) NOT NULL COMMENT '是否默认：1是0否',
  `is_require` tinyint(1) NOT NULL COMMENT '是否必填：1是0否',
  `status` tinyint(1) NOT NULL COMMENT '状态:1启用;0停用',
  `attr_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '属性类型 1基本属性2销售属性3商品属性',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `create_user` varchar(10) NOT NULL COMMENT '创建人',
  `info` varchar(100) DEFAULT NULL COMMENT '备注',
  `default_val` varchar(20) DEFAULT NULL COMMENT '默认值是什么',
  PRIMARY KEY (`rel_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3491 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for rel_factory_boss_t100
-- ----------------------------
DROP TABLE IF EXISTS `rel_factory_boss_t100`;
CREATE TABLE `rel_factory_boss_t100` (
  `factory_id` varchar(60) DEFAULT NULL,
  `t100_id` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for rel_style_attribute
-- ----------------------------
DROP TABLE IF EXISTS `rel_style_attribute`;
CREATE TABLE `rel_style_attribute` (
  `rel_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cat_type_id` int(10) NOT NULL DEFAULT '1' COMMENT '分类名称id',
  `product_type_id` smallint(4) NOT NULL DEFAULT '1' COMMENT '产品线id',
  `style_sn` varchar(20) NOT NULL COMMENT '款号',
  `attribute_id` int(10) NOT NULL COMMENT '属性id',
  `attribute_value` varchar(200) DEFAULT NULL COMMENT '属性值',
  `show_type` tinyint(1) NOT NULL COMMENT '1=>''文本框'',2=>''单选'',3=>''多选'',4=>''下拉列表''',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `create_user` varchar(10) NOT NULL COMMENT '创建人',
  `info` varchar(100) DEFAULT NULL COMMENT '备注',
  `style_id` int(10) NOT NULL COMMENT '款式ID',
  `is_price_conbined` tinyint(1) unsigned DEFAULT '0' COMMENT '是否参与价格计算 1是 0否',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`rel_id`),
  KEY `style_sn` (`style_sn`),
  KEY `style_id` (`style_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `cat_type_id` (`cat_type_id`),
  KEY `product_type_id` (`product_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=806435 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for rel_style_attribute_node
-- ----------------------------
DROP TABLE IF EXISTS `rel_style_attribute_node`;
CREATE TABLE `rel_style_attribute_node` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=151 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for rel_style_factory
-- ----------------------------
DROP TABLE IF EXISTS `rel_style_factory`;
CREATE TABLE `rel_style_factory` (
  `f_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `style_id` int(10) unsigned DEFAULT NULL COMMENT '款式ID',
  `style_sn` varchar(20) DEFAULT NULL COMMENT '款号',
  `factory_id` int(11) NOT NULL COMMENT '工厂id',
  `factory_sn` varchar(30) NOT NULL COMMENT '工厂模号',
  `factory_fee` decimal(10,2) NOT NULL COMMENT '工厂费用',
  `xiangkou` decimal(8,3) NOT NULL COMMENT '镶口',
  `is_def` int(1) NOT NULL DEFAULT '0' COMMENT '是否默认;0为否;1为是;',
  `is_factory` int(1) NOT NULL DEFAULT '0' COMMENT '是否默认工厂；0为否 ；1为是',
  `is_cancel` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否作废，1正常，2作废',
  PRIMARY KEY (`f_id`),
  KEY `style_id` (`style_id`),
  KEY `style_sn` (`style_sn`),
  KEY `factory_id` (`factory_id`)
) ENGINE=InnoDB AUTO_INCREMENT=47737 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for rel_style_factory_old
-- ----------------------------
DROP TABLE IF EXISTS `rel_style_factory_old`;
CREATE TABLE `rel_style_factory_old` (
  `f_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `style_id` int(10) unsigned DEFAULT NULL COMMENT '款式ID',
  `style_sn` varchar(20) DEFAULT NULL COMMENT '款号',
  `factory_id` int(11) NOT NULL COMMENT '工厂id',
  `factory_sn` varchar(30) NOT NULL COMMENT '工厂模号',
  `factory_fee` decimal(10,2) NOT NULL COMMENT '工厂费用',
  `xiangkou` decimal(8,3) NOT NULL COMMENT '镶口',
  `is_def` int(1) NOT NULL DEFAULT '0' COMMENT '是否默认;0为否;1为是;',
  `is_factory` int(1) NOT NULL DEFAULT '0' COMMENT '是否默认工厂；0为否 ；1为是',
  `is_cancel` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否作废，1正常，2作废',
  PRIMARY KEY (`f_id`),
  KEY `style_sn` (`style_sn`),
  KEY `factory_sn` (`factory_sn`),
  KEY `factory_id` (`factory_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39102 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for rel_style_lovers
-- ----------------------------
DROP TABLE IF EXISTS `rel_style_lovers`;
CREATE TABLE `rel_style_lovers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `style_id1` int(10) DEFAULT '0' COMMENT '款式id',
  `style_id2` int(10) DEFAULT '0' COMMENT '款式id',
  `style_sn1` varchar(30) DEFAULT NULL COMMENT '款式编号',
  `style_sn2` varchar(30) DEFAULT NULL COMMENT '款式编号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1141 DEFAULT CHARSET=utf8 COMMENT='情侣表';

-- ----------------------------
-- Table structure for rel_style_stone
-- ----------------------------
DROP TABLE IF EXISTS `rel_style_stone`;
CREATE TABLE `rel_style_stone` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `style_id` int(10) unsigned NOT NULL COMMENT '款式信息自增id',
  `stone_position` tinyint(3) NOT NULL COMMENT '石头位置类型 1主石2副石',
  `stone_cat` tinyint(3) NOT NULL COMMENT '石头类型',
  `stone_attr` text COMMENT '属性',
  `add_time` datetime DEFAULT NULL COMMENT '导入数据用',
  `shape` tinyint(1) unsigned DEFAULT '0',
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `style_id` (`style_id`),
  KEY `stone_position` (`stone_position`)
) ENGINE=InnoDB AUTO_INCREMENT=59338 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for style_sn_now
-- ----------------------------
DROP TABLE IF EXISTS `style_sn_now`;
CREATE TABLE `style_sn_now` (
  `goods_sn` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for style_style
-- ----------------------------
DROP TABLE IF EXISTS `style_style`;
CREATE TABLE `style_style` (
  `style_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `style_sn` varchar(20) NOT NULL COMMENT '款式编号',
  `gene_sn` varchar(20) NOT NULL COMMENT '基因码,单品才有',
  `ori_goods_sn` varchar(20) NOT NULL COMMENT '原始款号',
  `style_name` varchar(60) NOT NULL COMMENT '款式名称',
  `style_dep` varchar(60) NOT NULL COMMENT '销售部门',
  `style_sex` tinyint(3) NOT NULL COMMENT '款式性别',
  `retail_num` decimal(10,3) NOT NULL COMMENT '建议零售价系数',
  `style_img` varchar(60) NOT NULL COMMENT '基础图',
  `thumb_img` varchar(60) NOT NULL COMMENT '缩略图',
  `middle_img` varchar(60) NOT NULL,
  `big_img` varchar(60) NOT NULL COMMENT '大图',
  `simple_desc` varchar(512) NOT NULL COMMENT '简要描述',
  `more_desc` text NOT NULL COMMENT '详细描述',
  `show_more_desc` int(3) NOT NULL COMMENT '不显示相册',
  `could_world` tinyint(3) NOT NULL COMMENT '是否支持刻字',
  `style_cat` int(11) NOT NULL COMMENT '款式分类',
  `style_cat_attr` text NOT NULL COMMENT '分类属性',
  `main_stone_cat` tinyint(3) NOT NULL COMMENT '主石类型',
  `main_stone_attr` text NOT NULL COMMENT '主石属性',
  `sec_stone_cat` tinyint(3) NOT NULL COMMENT '副石分类',
  `sec_stone_attr` text NOT NULL COMMENT '副石属性',
  `metal_info` text NOT NULL COMMENT '金料信息',
  `done_shell` tinyint(3) NOT NULL COMMENT '可以定制金托',
  `face_work` varchar(255) NOT NULL COMMENT '表面工艺',
  `style_style` varchar(20) NOT NULL COMMENT '款式风格',
  `style_for_who` varchar(20) NOT NULL COMMENT '适合对象(按年龄)',
  `style_for_use` varchar(20) NOT NULL COMMENT '适合场景(按用途)',
  `style_for_when` varchar(20) NOT NULL COMMENT '适合节庆',
  `style_for_cat` varchar(20) NOT NULL COMMENT '送礼人类型',
  `style_for_designer` int(10) NOT NULL COMMENT '设计师',
  `is_confirm` tinyint(3) NOT NULL COMMENT '是否审核',
  `is_betch` tinyint(3) NOT NULL DEFAULT '0' COMMENT '批量导入',
  `is_betch_bak` tinyint(3) NOT NULL COMMENT '导入备份',
  `is_manual_price` tinyint(3) NOT NULL DEFAULT '0' COMMENT '指定成本',
  `manual_price` decimal(10,2) NOT NULL COMMENT '成本价',
  `click_count` int(10) unsigned NOT NULL COMMENT '点击数量',
  `last_update` datetime NOT NULL COMMENT '最后更新时间',
  `confirm_time` datetime NOT NULL COMMENT '第一次审核时间',
  `beian` tinyint(3) NOT NULL,
  `chaihuo` tinyint(3) NOT NULL,
  `duibanzhuangtai` tinyint(3) NOT NULL COMMENT '对版状态,1.导出,2审核过,3不知道工厂,4暂时找不到版',
  `click_renqi` int(11) NOT NULL COMMENT '点击人气',
  `sell_num` int(10) NOT NULL COMMENT '销售量',
  `show_goods_gallery` tinyint(2) NOT NULL COMMENT '是否显示描述(1不显示，0显示)',
  `show_property` int(4) NOT NULL DEFAULT '0' COMMENT '是否显示属性',
  `pro_line` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0=其他 1=黄金等投资产品 2=素金饰品 3=黄金饰品 4=结婚钻石饰品 5=钻石饰品 6=珍珠饰品 7=彩宝及翡翠饰品 8=裸石',
  `merger_sn` varchar(20) NOT NULL COMMENT '合并后的款式编号',
  `merger_time` datetime NOT NULL,
  `kucun_num` int(5) NOT NULL COMMENT '总公司库存数',
  `sell_type` varchar(10) NOT NULL COMMENT '畅销度（新款、滞销款、平常款、畅销款、经典款）',
  `create_time` datetime NOT NULL COMMENT '首次更新时间',
  `kucun_num_all` int(6) NOT NULL COMMENT '全国库存',
  `sell_num_thr` int(6) NOT NULL COMMENT '3个月销量',
  `is_img` int(1) NOT NULL COMMENT '图片是否通过；1为通过；0为未通过；',
  `style_img_n` varchar(60) NOT NULL COMMENT '180度基础图',
  `thumb_img_n` varchar(60) NOT NULL COMMENT '180度缩略图',
  `big_img_n` varchar(60) NOT NULL COMMENT '180度大图',
  `middle_img_n` varchar(60) NOT NULL COMMENT '180度中图',
  `relation_sn` varchar(20) NOT NULL COMMENT '关联款号',
  `ring_type` int(1) NOT NULL COMMENT '新戒指类型;1为男戒；2为女戒；3为情侣戒；4为其他',
  `zuofei_time` datetime NOT NULL COMMENT '作废时间',
  `zuofei_type` int(1) NOT NULL DEFAULT '1' COMMENT '是否显示：1为正常；2为作废；',
  `zuhe_time` datetime NOT NULL COMMENT '情侣戒组合时间',
  `is_chaihuo` int(1) NOT NULL DEFAULT '0' COMMENT '是否拆货；0为正常；1为允许拆货；2为已拆货；',
  `history_chaihuo` int(1) NOT NULL DEFAULT '0' COMMENT '历史拆货记录；0为未拆过货；1为拆过货',
  `kucun_num_all_thr` int(10) NOT NULL COMMENT '近3个月全国库存数（近3月采量）',
  `is_3d` int(1) NOT NULL DEFAULT '0' COMMENT '是否3D；1为3D；2为普通；3为精品；',
  `pro_lines` int(2) NOT NULL DEFAULT '0' COMMENT '原“钻石饰品”产品线备注',
  `zhua_xingtai` int(1) NOT NULL DEFAULT '1' COMMENT '爪形态:1为直(默认);2为扭;3为花型;4为雪花;',
  `zhua_daizuan` int(1) NOT NULL COMMENT '爪带钻;1为钻',
  `bi_xingtai` int(1) NOT NULL DEFAULT '1' COMMENT '臂形态;1为直臂(默认);2为扭臂;3为错臂;4为高低臂;',
  `bi_daizuan` int(1) NOT NULL COMMENT '臂带钻;1为带副石',
  `jiebi_gongyi` varchar(20) NOT NULL COMMENT '戒臂表面工艺处理',
  `is_weizuan` int(1) NOT NULL DEFAULT '1' COMMENT '是否围钻;1为围',
  `zhua_xingzhuang` int(2) NOT NULL COMMENT '爪头形状',
  `zhua_num` int(5) NOT NULL COMMENT '爪头数量',
  `is_zhizhua` int(5) NOT NULL DEFAULT '0' COMMENT '是否直爪',
  `is_fushi_xingzhuang` varchar(20) NOT NULL COMMENT '副石形状',
  `style_xiangqian` int(2) NOT NULL COMMENT '镶嵌方式',
  `dapei_goods_sn` varchar(60) NOT NULL COMMENT '搭配套系名称',
  `zhushishu` int(5) NOT NULL COMMENT '主石数',
  `zhushi_xingzhuang` int(1) NOT NULL DEFAULT '1' COMMENT '主石形状；1为垫形；2为公主方；3祖母绿；4心形；5蛋形；6椭圆形；7橄榄形；8三角形；9水滴形；10长方形；11圆形；12梨形',
  `is_fushi` int(1) NOT NULL DEFAULT '0' COMMENT '是否有副石；0为是；1是否',
  `style_caizhi` int(2) NOT NULL COMMENT '可做材质；1为18K；2为PT950；3为18K&PT950',
  `is_kezi` int(1) NOT NULL DEFAULT '0' COMMENT '是否刻字；0为是；1为否；',
  `is_gaiquan` int(1) NOT NULL DEFAULT '0' COMMENT '是否支持改圈；0为是；1为否',
  `style_gaiquan` int(2) NOT NULL COMMENT '最大改圈范围；1为不支持；2为1个手寸；3为2个手寸；4为3个手寸；',
  `kezuo_yanse` varchar(20) NOT NULL COMMENT '18K可做颜色;',
  `18kw_gongfei` int(5) NOT NULL DEFAULT '0' COMMENT '18K白色基础工费',
  `18kr_gongfei` int(5) NOT NULL DEFAULT '0' COMMENT '18K红色基础工费',
  `18ky_gongfei` int(5) NOT NULL DEFAULT '0' COMMENT '18K黄色基础工费',
  `18kc_gongfei` int(5) NOT NULL DEFAULT '0' COMMENT '18K彩色基础工费',
  `ptw_gongfei` int(5) NOT NULL DEFAULT '0' COMMENT 'PT950白色基础工费',
  `is_new` int(1) NOT NULL DEFAULT '0' COMMENT '是否新款式；0为否；1为是；2为v3',
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '货品分类',
  `zhengshu` varchar(20) NOT NULL COMMENT '证书',
  `editor1` text NOT NULL COMMENT '卖点信息',
  `bmgy_gongfei` int(5) NOT NULL COMMENT '表面工艺工费',
  `csflj_gongfei` int(5) NOT NULL DEFAULT '0' COMMENT '超石费另计',
  `last_create_times` text NOT NULL COMMENT '上一次生成款式时间',
  `style_sn_v2` varchar(20) NOT NULL COMMENT 'v2版款号',
  PRIMARY KEY (`style_id`),
  KEY `style_sn` (`style_sn`),
  KEY `ori_goods_sn` (`ori_goods_sn`),
  KEY `style_cat` (`style_cat`),
  KEY `is_new` (`is_new`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=28670 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for style_style_temp
-- ----------------------------
DROP TABLE IF EXISTS `style_style_temp`;
CREATE TABLE `style_style_temp` (
  `style_id` int(10) DEFAULT NULL,
  `style_sn` varchar(20) NOT NULL COMMENT '款式编号'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for style_toindex
-- ----------------------------
DROP TABLE IF EXISTS `style_toindex`;
CREATE TABLE `style_toindex` (
  `style_sn` varchar(50) NOT NULL COMMENT '款号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for style_xilie_bak_0428
-- ----------------------------
DROP TABLE IF EXISTS `style_xilie_bak_0428`;
CREATE TABLE `style_xilie_bak_0428` (
  `style_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '款式ID',
  `style_sn` varchar(20) NOT NULL COMMENT '款式编号',
  `style_name` varchar(60) NOT NULL COMMENT '款式名称',
  `product_type` tinyint(2) DEFAULT NULL COMMENT '产品线:app_product_type',
  `style_type` tinyint(2) NOT NULL COMMENT '款式分类:app_cat_type',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `modify_time` datetime NOT NULL COMMENT '更新时间',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `cancel_time` datetime DEFAULT NULL COMMENT '作废时间',
  `check_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '审核状态:1保存2提交申请3审核4未通过5作废',
  `is_sales` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否销售，0：否，1：是',
  `is_made` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否定制，0：否，1：是',
  `dismantle_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否拆货:1=正常 2=允许拆货 3=已拆货',
  `style_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '记录状态',
  `style_remark` text COMMENT '记录备注',
  `dapei_goods_sn` varchar(60) DEFAULT NULL COMMENT '搭配套系名称',
  `changbei_sn` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否常备款;1,是；2,否',
  `style_sex` tinyint(1) NOT NULL DEFAULT '1' COMMENT '款式性别;1:男；2：女；3：中性',
  `xilie` varchar(50) DEFAULT NULL COMMENT '系列',
  `market_xifen` varchar(50) DEFAULT NULL COMMENT '市场细分',
  `is_zp` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否是赠品；1否，2是',
  `is_new` tinyint(4) DEFAULT NULL COMMENT '导数据用',
  `ori_goods_sn` varchar(20) DEFAULT NULL COMMENT '老款号',
  `sell_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '畅销度',
  `bang_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '绑定1：需要绑定，2：不需要绑定',
  `sale_way` char(2) NOT NULL DEFAULT '1' COMMENT '可销售渠道. 1线上，2线下',
  `is_xz` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否销账,2.是.1否',
  `zp_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '赠品售价',
  `is_allow_favorable` tinyint(1) unsigned NOT NULL COMMENT '是否允许改价',
  `is_gold` tinyint(3) unsigned NOT NULL COMMENT '是否是黄金 0:非黄金，1:瑞金 2:3D  3:一口价',
  `is_support_style` tinyint(3) unsigned DEFAULT NULL COMMENT '是否支持按款销售'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for temp_salepolicy_channel
-- ----------------------------
DROP TABLE IF EXISTS `temp_salepolicy_channel`;
CREATE TABLE `temp_salepolicy_channel` (
  `policy_name` varchar(255) DEFAULT NULL,
  `channel` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tmp_appyikoujia
-- ----------------------------
DROP TABLE IF EXISTS `tmp_appyikoujia`;
CREATE TABLE `tmp_appyikoujia` (
  `id` int(11) NOT NULL DEFAULT '0',
  `goods_id` varchar(255) DEFAULT NULL,
  `goods_sn` varchar(255) DEFAULT NULL,
  `caizhi` varchar(255) DEFAULT NULL,
  `small` varchar(255) DEFAULT NULL,
  `tuo_type` tinyint(1) DEFAULT '0' COMMENT '金托类型',
  `color` varchar(20) DEFAULT NULL COMMENT '颜色',
  `clarity` varchar(20) DEFAULT NULL COMMENT '净度',
  `sbig` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `policy_id` varchar(255) DEFAULT NULL,
  `isXianhuo` varchar(255) DEFAULT NULL,
  `is_delete` tinyint(2) DEFAULT '0' COMMENT '是否删除0未删除 ，1已删除',
  `add_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `add_user` varchar(120) DEFAULT NULL COMMENT '添加人',
  `cert` varchar(255) DEFAULT NULL COMMENT '裸钻证书类型',
  KEY `goods_id` (`goods_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tmp_appyikoujia_20170720
-- ----------------------------
DROP TABLE IF EXISTS `tmp_appyikoujia_20170720`;
CREATE TABLE `tmp_appyikoujia_20170720` (
  `id` int(11) DEFAULT '0',
  `goods_id` varchar(255) DEFAULT NULL,
  `goods_sn` varchar(255) DEFAULT NULL,
  `caizhi` varchar(255) DEFAULT NULL,
  `small` varchar(255) DEFAULT NULL,
  `tuo_type` tinyint(1) DEFAULT '0' COMMENT '金托类型',
  `color` varchar(20) DEFAULT NULL COMMENT '颜色',
  `clarity` varchar(20) DEFAULT NULL COMMENT '净度',
  `sbig` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `policy_id` varchar(255) DEFAULT NULL,
  `isXianhuo` varchar(255) DEFAULT NULL,
  `is_delete` tinyint(2) DEFAULT '0' COMMENT '是否删除0未删除 ，1已删除',
  `add_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `add_user` varchar(120) DEFAULT NULL COMMENT '添加人',
  `cert` varchar(255) DEFAULT NULL COMMENT '裸钻证书类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tmp_gangtai_channel_lly
-- ----------------------------
DROP TABLE IF EXISTS `tmp_gangtai_channel_lly`;
CREATE TABLE `tmp_gangtai_channel_lly` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `policy_id` int(10) DEFAULT NULL COMMENT '销售策略id',
  `channel` int(10) DEFAULT NULL COMMENT '渠道id',
  `channel_level` int(10) DEFAULT '1' COMMENT '等级',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(100) DEFAULT NULL COMMENT '创建人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `check_user` varchar(100) DEFAULT NULL COMMENT '审核',
  `status` int(1) DEFAULT NULL COMMENT '状态:1保存2申请3审核通过4未通过5取消',
  `is_delete` int(1) DEFAULT NULL COMMENT '取消 1未删除 2已删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tmp_gangtai_policy_lly
-- ----------------------------
DROP TABLE IF EXISTS `tmp_gangtai_policy_lly`;
CREATE TABLE `tmp_gangtai_policy_lly` (
  `policy_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '自增ID',
  `policy_name` varchar(60) NOT NULL COMMENT '销售策略名称',
  `policy_start_time` date NOT NULL COMMENT '销售策略开始时间',
  `policy_end_time` date DEFAULT NULL COMMENT '销售策略结束时间',
  `create_time` datetime DEFAULT NULL COMMENT '记录创建时间',
  `create_user` varchar(20) DEFAULT NULL COMMENT '记录创建人',
  `create_remark` varchar(200) DEFAULT NULL COMMENT '记录创建备注',
  `check_user` varchar(20) DEFAULT NULL COMMENT '审核人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `zuofei_time` datetime DEFAULT NULL COMMENT '作废时间',
  `check_remark` varchar(200) DEFAULT NULL COMMENT '记录备注',
  `bsi_status` tinyint(1) DEFAULT NULL COMMENT '记录状态 1保存,2申请审核,3已审核,4取消',
  `is_delete` tinyint(1) DEFAULT NULL COMMENT '记录是否有效 0有效1无效',
  `is_together` tinyint(1) NOT NULL DEFAULT '1' COMMENT '策略类型：1，普通；2，打包',
  `jiajia` decimal(8,4) unsigned NOT NULL DEFAULT '1.0000' COMMENT '加价率',
  `sta_value` decimal(8,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '固定值',
  `is_default` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否为默认政策1为默认2位不是默认',
  `is_favourable` tinyint(11) NOT NULL DEFAULT '1',
  `product_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '产品线',
  `tuo_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '金托类型',
  `huopin_type` int(1) NOT NULL DEFAULT '2' COMMENT '货品类型',
  `cat_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '款式分类',
  `range_begin` char(25) NOT NULL DEFAULT '0' COMMENT '开始范围',
  `range_end` char(25) NOT NULL DEFAULT '0' COMMENT '结束范围',
  `zhushi_begin` char(25) NOT NULL DEFAULT '0' COMMENT '开始范围',
  `zhushi_end` char(25) NOT NULL DEFAULT '0' COMMENT '结束范围',
  `is_kuanprice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否按款定价 0不是1是',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `is_tsyd` int(11) DEFAULT '0' COMMENT '是否天生一对',
  `product_type_id` int(10) DEFAULT '0' COMMENT '产品线id',
  `cat_type_id` int(10) DEFAULT '0' COMMENT '款式分类id',
  `xilie` text COMMENT '所属系列',
  `cert` text COMMENT '裸钻证书类型',
  `color` varchar(255) DEFAULT NULL,
  `clarity` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tmp_gangtai_yikoujia_lly
-- ----------------------------
DROP TABLE IF EXISTS `tmp_gangtai_yikoujia_lly`;
CREATE TABLE `tmp_gangtai_yikoujia_lly` (
  `id` int(11) NOT NULL DEFAULT '0',
  `goods_id` varchar(255) DEFAULT NULL,
  `goods_sn` varchar(255) DEFAULT NULL,
  `caizhi` varchar(255) DEFAULT NULL,
  `small` varchar(255) DEFAULT NULL,
  `tuo_type` tinyint(1) DEFAULT '0' COMMENT '金托类型',
  `color` varchar(20) DEFAULT NULL COMMENT '颜色',
  `clarity` varchar(20) DEFAULT NULL COMMENT '净度',
  `sbig` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `policy_id` varchar(255) DEFAULT NULL,
  `isXianhuo` varchar(255) DEFAULT NULL,
  `is_delete` tinyint(2) DEFAULT '0' COMMENT '是否删除0未删除 ，1已删除',
  `add_time` datetime DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `add_user` varchar(120) DEFAULT NULL COMMENT '添加人',
  `cert` varchar(255) DEFAULT NULL COMMENT '裸钻证书类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
