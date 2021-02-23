/*
Navicat MySQL Data Transfer

Source Server         : mysql_192.168.0.95_3306
Source Server Version : 50626
Source Host           : 192.168.0.95:3306
Source Database       : bossreport

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2019-09-09 10:21:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ecs_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `ecs_admin_user`;
CREATE TABLE `ecs_admin_user` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `bid` int(10) DEFAULT '0',
  `code` varchar(16) DEFAULT NULL COMMENT '工牌号',
  `realname` varchar(60) DEFAULT NULL,
  `user_name` varchar(60) DEFAULT '',
  `cid` char(20) DEFAULT NULL,
  `mobile` char(11) DEFAULT NULL,
  `saler_type` varchar(3) DEFAULT '2',
  `email` varchar(60) DEFAULT '',
  `password` varchar(32) DEFAULT '',
  `join_time` datetime DEFAULT '0000-00-00 00:00:00',
  `last_time` datetime DEFAULT '0000-00-00 00:00:00',
  `last_ip` varchar(15) DEFAULT '',
  `action_list` text,
  `role_list` char(255) DEFAULT NULL COMMENT '角色列表',
  `department` varchar(255) DEFAULT NULL,
  `warehouse` varchar(60) DEFAULT NULL,
  `user_belong_area` char(10) DEFAULT NULL COMMENT '所属展区',
  `user_station` char(10) DEFAULT NULL COMMENT '关联柜台',
  `nav_list` text,
  `lang_type` varchar(50) DEFAULT '',
  `is_work` tinyint(1) DEFAULT '1',
  `im` varchar(15) DEFAULT NULL,
  `group` int(5) DEFAULT NULL,
  `department_id` int(5) DEFAULT NULL,
  `haveorder` int(1) DEFAULT NULL,
  `padName` char(20) DEFAULT NULL COMMENT 'PAD编号',
  `user_jc_name` varchar(20) DEFAULT NULL COMMENT '用户简称',
  `for_goods` varchar(20) DEFAULT NULL COMMENT '负责商品',
  `is_daozhu` tinyint(1) DEFAULT '0',
  `is_daoqu` char(20) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_name` (`user_name`)
) ENGINE=MyISAM AUTO_INCREMENT=205 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_login_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_login_log`;
CREATE TABLE `ecs_login_log` (
  `id` int(20) NOT NULL AUTO_INCREMENT COMMENT '登录Id',
  `login_time` datetime NOT NULL COMMENT '登录时间',
  `login_ip` varchar(20) NOT NULL COMMENT '登录IP',
  `user_name` varchar(50) NOT NULL COMMENT '登录用户',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

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

-- ----------------------------
-- Table structure for ecs_shop_config
-- ----------------------------
DROP TABLE IF EXISTS `ecs_shop_config`;
CREATE TABLE `ecs_shop_config` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `code` varchar(30) NOT NULL DEFAULT '',
  `name` char(255) NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT '',
  `store_range` varchar(255) NOT NULL DEFAULT '',
  `store_dir` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for --goods
-- ----------------------------
DROP TABLE IF EXISTS `--goods`;
CREATE TABLE `--goods` (
  `id` bigint(16) NOT NULL AUTO_INCREMENT,
  `platform_id` int(10) DEFAULT '1' COMMENT '平台id 1拼多多 2京东 3淘宝',
  `platform_sub_id` int(10) unsigned DEFAULT '0' COMMENT '平台子ID',
  `goods_id` bigint(16) DEFAULT NULL COMMENT '商品ID',
  `goods_name` varchar(255) DEFAULT NULL COMMENT '商品名称',
  `goods_desc` varchar(800) DEFAULT NULL COMMENT '商品描述',
  `goods_thumbnail_url` varchar(255) DEFAULT NULL COMMENT '商品小图',
  `goods_image_url` varchar(255) DEFAULT NULL COMMENT '商品大图',
  `goods_gallery_urls` varchar(1600) DEFAULT NULL COMMENT '商品图集',
  `sold_quantity` int(11) DEFAULT '0' COMMENT '销量',
  `goods_mark_price` decimal(10,2) DEFAULT NULL COMMENT '市场价',
  `goods_fact_price` decimal(10,2) DEFAULT NULL COMMENT '券后价',
  `min_group_price` decimal(10,2) DEFAULT NULL COMMENT '最小拼购价',
  `min_normal_price` decimal(10,2) DEFAULT NULL COMMENT '正常价格',
  `mall_id` bigint(20) DEFAULT NULL COMMENT '店铺ID',
  `mall_name` varchar(50) DEFAULT NULL COMMENT '店铺名称',
  `class_id` int(11) DEFAULT '0' COMMENT '今日抢分类ID',
  `class_name` varchar(50) DEFAULT NULL COMMENT '今日抢分类名称',
  `category_id` int(11) DEFAULT NULL COMMENT '分类ID',
  `category_name` varchar(30) DEFAULT NULL COMMENT '分类名称',
  `cat_id1` int(10) DEFAULT NULL COMMENT '一级分类',
  `cat_id2` int(10) DEFAULT NULL COMMENT '二级分类',
  `cat_id3` int(10) DEFAULT NULL COMMENT '三级分类',
  `has_coupon` smallint(11) DEFAULT NULL COMMENT '是否有券',
  `click_url` varchar(1024) DEFAULT NULL COMMENT '推广地址',
  `coupon_url` varchar(255) DEFAULT NULL COMMENT '优惠券地址',
  `coupon_min_order_amount` smallint(11) DEFAULT NULL COMMENT '券最低订单金额',
  `coupon_discount` decimal(11,2) DEFAULT '0.00' COMMENT '优惠券金额',
  `coupon_total_quantity` int(11) DEFAULT NULL COMMENT '优惠券总数',
  `coupon_remain_quantity` int(11) DEFAULT NULL COMMENT '优惠券剩余总数',
  `coupon_start_time` int(11) DEFAULT NULL COMMENT '优惠券开始时间',
  `coupon_end_time` int(11) DEFAULT NULL COMMENT '优惠券结束时间',
  `promotion_rate` int(11) DEFAULT NULL COMMENT '佣金比',
  `promotion_amount` decimal(10,2) DEFAULT NULL COMMENT '佣金金额',
  `goods_eval_score` decimal(10,2) DEFAULT NULL COMMENT '商品评分',
  `goods_eval_count` int(11) DEFAULT NULL COMMENT '商品评论总数',
  `avg_desc` int(11) DEFAULT NULL COMMENT '描述评分',
  `avg_lgst` int(11) DEFAULT NULL COMMENT '物流评分',
  `avg_serv` int(11) DEFAULT NULL COMMENT '服务评分',
  `desc_pct` double(11,5) DEFAULT NULL COMMENT '描述评分击败同类店铺百分比',
  `lgst_pct` double(10,5) DEFAULT NULL COMMENT '物流评分击败同类店铺百分比',
  `serv_pct` double(10,5) DEFAULT NULL COMMENT '服务评分击败同类店铺百分比',
  `sale_num24` int(11) DEFAULT NULL,
  `sale_num_today` int(11) DEFAULT NULL,
  `rank` decimal(10,2) DEFAULT '0.00' COMMENT '排序热度',
  `week_day` tinyint(1) unsigned DEFAULT '0' COMMENT '商品按天分组  周1-周日 0-6',
  `is_onsale` tinyint(2) DEFAULT '1' COMMENT '是否上架  1是 0否',
  `goods_type` tinyint(1) DEFAULT '1' COMMENT '商品类型 1:今日抢货 2高佣金  3: 0元购',
  `last_updated` int(11) DEFAULT NULL COMMENT '最后更新时间',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `created_by` varchar(45) DEFAULT NULL COMMENT '创建人',
  `updated_by` varchar(45) DEFAULT NULL COMMENT '上下架更新人',
  `remark` varchar(255) DEFAULT NULL COMMENT '商品备注',
  `updated_at` int(10) DEFAULT NULL COMMENT '更新时间',
  `batch_no` int(11) DEFAULT '0' COMMENT '同步批次号',
  `brand_name` varchar(150) DEFAULT NULL COMMENT '品牌名称',
  `promotion_type` int(1) DEFAULT '0' COMMENT '爆品：0：无含义；1：今日爆品；',
  `keyword_id` int(10) unsigned DEFAULT '0' COMMENT '关键词ID',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `goods_id` (`goods_id`,`platform_id`) USING BTREE,
  KEY `goods_type` (`goods_type`),
  KEY `cat_id1` (`cat_id1`) USING BTREE,
  KEY `cat_id2` (`cat_id2`) USING BTREE,
  KEY `is_onsale` (`is_onsale`),
  KEY `sort_1` (`rank`) USING BTREE COMMENT '排序组合索引',
  KEY `keyword_id` (`keyword_id`),
  KEY `platform_sub_id` (`platform_sub_id`,`batch_no`) USING BTREE,
  FULLTEXT KEY `fultext_index` (`goods_name`,`class_name`)
) ENGINE=InnoDB AUTO_INCREMENT=15294557 DEFAULT CHARSET=utf8 COMMENT='拼多多商品表';

-- ----------------------------
-- Table structure for --goods_cats
-- ----------------------------
DROP TABLE IF EXISTS `--goods_cats`;
CREATE TABLE `--goods_cats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` int(10) unsigned DEFAULT NULL COMMENT '分类id',
  `cat_name` varchar(255) DEFAULT NULL COMMENT '分类名称',
  `parent_cat_id` int(10) DEFAULT NULL COMMENT '上级分类id',
  `level` int(10) DEFAULT NULL COMMENT '层级数目',
  `last_updated` int(11) DEFAULT NULL COMMENT '最后更新时间',
  `is_deleted` tinyint(1) unsigned DEFAULT '0' COMMENT '是否删除 1是 0否',
  `platform_id` int(10) unsigned DEFAULT NULL COMMENT '平台id  1拼多多 2京东',
  PRIMARY KEY (`id`),
  UNIQUE KEY `cat_id` (`cat_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13775 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for --goods_order
-- ----------------------------
DROP TABLE IF EXISTS `--goods_order`;
CREATE TABLE `--goods_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `platform_id` int(10) NOT NULL COMMENT '平台类型',
  `order_sn` varchar(45) NOT NULL COMMENT '订单编号',
  `goods_id` bigint(16) NOT NULL COMMENT '商品id',
  `goods_name` varchar(255) DEFAULT NULL COMMENT '商品名称',
  `goods_price` decimal(10,2) DEFAULT NULL COMMENT '商品价格',
  `goods_thumbnail_url` varchar(255) DEFAULT NULL COMMENT '商品缩略图',
  `goods_quantity` int(10) DEFAULT NULL COMMENT '商品数量',
  `p_id` varchar(30) DEFAULT NULL COMMENT '推广位id',
  `promotion_rate` int(10) DEFAULT NULL COMMENT '佣金比例（千分比）',
  `promotion_amount` decimal(10,2) DEFAULT NULL COMMENT '总佣金',
  `order_amount` decimal(10,2) DEFAULT NULL COMMENT '订单金额',
  `order_status` tinyint(2) DEFAULT NULL COMMENT '订单状态',
  `order_status_desc` varchar(100) DEFAULT NULL COMMENT '订单状态',
  `order_create_time` int(10) DEFAULT NULL COMMENT '订单创建时间',
  `order_pay_time` int(10) DEFAULT NULL COMMENT '订单支付时间',
  `order_group_success_time` int(10) DEFAULT NULL COMMENT '订单成团时间',
  `order_verify_time` int(10) DEFAULT NULL COMMENT '订单确认时间',
  `order_modify_at` int(10) DEFAULT NULL COMMENT '订单最后更新时间',
  `custom_parameters` varchar(30) DEFAULT NULL COMMENT '自定义参数',
  `promotion_status` tinyint(1) unsigned DEFAULT '0' COMMENT '结算状态 1已结算 0未结算 2无效',
  `buyer_id` int(10) unsigned DEFAULT NULL COMMENT '买家ID',
  `seller_id` int(10) DEFAULT NULL COMMENT '卖家ID',
  `created_at` int(10) DEFAULT NULL COMMENT '创建时间',
  `last_updated` int(10) DEFAULT NULL COMMENT '最后更新时间',
  `free_order_status` tinyint(1) DEFAULT '0' COMMENT '零元购状态(0普通 1零元购 2转普通)',
  `org_promotion_rate` int(11) DEFAULT NULL COMMENT '原始商品佣金比例',
  `org_promotion_amount` decimal(10,2) DEFAULT NULL COMMENT '原始商品佣金金额',
  `org_goods_fact_price` decimal(10,2) DEFAULT NULL COMMENT '原始商品券后价',
  `goods_data` text COMMENT '原始商品信息，json存储',
  `level1_member_id` int(11) DEFAULT '0' COMMENT '一级用户',
  `level1_p_rate` decimal(10,2) DEFAULT '0.00' COMMENT '一级佣金比例',
  `level1_p_amount` decimal(10,2) DEFAULT '0.00' COMMENT '一级佣金',
  `level2_member_id` int(11) DEFAULT '0' COMMENT '一级用户',
  `level2_p_rate` decimal(10,2) DEFAULT '0.00' COMMENT '一级佣金比例',
  `level2_p_amount` decimal(10,2) DEFAULT '0.00' COMMENT '一级佣金',
  `buyer_p_rate` decimal(10,2) DEFAULT '0.00' COMMENT '买家佣金比例',
  `buyer_p_amount` decimal(10,2) DEFAULT '0.00' COMMENT '买家佣金',
  `audit_status` tinyint(1) DEFAULT '0' COMMENT '审批状态，0待审批1已审批',
  `audit_by` varchar(60) DEFAULT NULL COMMENT '审批人',
  `audit_time` int(11) DEFAULT '0' COMMENT '审批时间',
  `api_data` text COMMENT '订单api数据',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_id` (`order_sn`,`platform_id`,`goods_id`) USING BTREE COMMENT '唯一键',
  KEY `buyer_id` (`buyer_id`),
  KEY `goods_id` (`goods_id`,`platform_id`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8 COMMENT='订单列表';

-- ----------------------------
-- Table structure for hunbo_role
-- ----------------------------
DROP TABLE IF EXISTS `hunbo_role`;
CREATE TABLE `hunbo_role` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` char(255) NOT NULL COMMENT '角色名称',
  `role_desc` char(255) NOT NULL COMMENT '角色描述',
  `role_permiss` text NOT NULL COMMENT '角色权限',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COMMENT='角色管理';
