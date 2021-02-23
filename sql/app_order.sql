/*
Navicat MySQL Data Transfer

Source Server         : mysql_192.168.0.95_3306
Source Server Version : 50626
Source Host           : 192.168.0.95:3306
Source Database       : app_order

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2019-09-09 10:20:23
*/

SET FOREIGN_KEY_CHECKS=0;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_gold_jiajialv
-- ----------------------------
DROP TABLE IF EXISTS `app_gold_jiajialv`;
CREATE TABLE `app_gold_jiajialv` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `gold_price` decimal(10,2) DEFAULT NULL COMMENT '黄金价格',
  `jiajialv` decimal(10,2) DEFAULT NULL COMMENT '加价率',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(20) DEFAULT NULL COMMENT '创建用户',
  `is_usable` tinyint(1) DEFAULT '1' COMMENT '是否可用（1 启用，0 禁用）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='普通黄金价格加价率';

-- ----------------------------
-- Table structure for app_order_account
-- ----------------------------
DROP TABLE IF EXISTS `app_order_account`;
CREATE TABLE `app_order_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单id',
  `order_amount` decimal(10,2) DEFAULT '0.00' COMMENT '订单总金额',
  `money_paid` decimal(10,2) DEFAULT '0.00' COMMENT '已付',
  `money_unpaid` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '未付',
  `goods_return_price` decimal(10,2) DEFAULT '0.00' COMMENT '商品实际退款',
  `real_return_price` decimal(10,2) DEFAULT '0.00' COMMENT '实退金额',
  `shipping_fee` decimal(10,2) DEFAULT '0.00' COMMENT '快递费',
  `goods_amount` decimal(10,2) DEFAULT '0.00' COMMENT '商品总额',
  `coupon_price` decimal(10,2) DEFAULT '0.00' COMMENT '订单优惠券金额',
  `favorable_price` decimal(10,2) DEFAULT '0.00' COMMENT '订单商品优惠金额',
  `card_fee` decimal(10,2) DEFAULT '0.00',
  `pack_fee` decimal(10,2) DEFAULT '0.00',
  `pay_fee` decimal(10,2) DEFAULT '0.00',
  `insure_fee` decimal(10,2) DEFAULT '0.00',
  `current_point` decimal(10,2) DEFAULT '0.00' COMMENT '订单积分',
  `old_point` decimal(10,2) DEFAULT '0.00' COMMENT '老订单积分',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2110147 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_order_account_bak
-- ----------------------------
DROP TABLE IF EXISTS `app_order_account_bak`;
CREATE TABLE `app_order_account_bak` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单id',
  `order_amount` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '订单总金额',
  `money_paid` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '已付',
  `money_unpaid` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '未付',
  `goods_return_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品实际退款',
  `real_return_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实退金额',
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '快递费',
  `goods_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品总额',
  `coupon_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单优惠券金额',
  `favorable_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单商品优惠金额',
  `card_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pack_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pay_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `insure_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id_2` (`order_id`),
  KEY `order_id` (`order_id`),
  KEY `money_paid` (`money_paid`,`money_unpaid`),
  KEY `order_amount` (`order_amount`)
) ENGINE=InnoDB AUTO_INCREMENT=127298 DEFAULT CHARSET=utf8 COMMENT='订单账户金额表';

-- ----------------------------
-- Table structure for app_order_action
-- ----------------------------
DROP TABLE IF EXISTS `app_order_action`;
CREATE TABLE `app_order_action` (
  `action_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单审核状态1无效2已审核3取消4关闭',
  `shipping_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发货状态',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态:1未付款2部分付款3已付款',
  `create_user` varchar(30) NOT NULL DEFAULT '' COMMENT '操作人',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '操作时间',
  `remark` text NOT NULL COMMENT '操作备注',
  `hidden` tinyint(1) DEFAULT '0' COMMENT '是否隐藏栏位',
  PRIMARY KEY (`action_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=789405 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_order_action_jxc
-- ----------------------------
DROP TABLE IF EXISTS `app_order_action_jxc`;
CREATE TABLE `app_order_action_jxc` (
  `action_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '自增',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单审核状态1无效2已审核3取消4关闭',
  `shipping_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发货状态',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态:1未付款2部分付款3已付款',
  `create_user` varchar(30) NOT NULL DEFAULT '' COMMENT '操作人',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '操作时间',
  `remark` text NOT NULL COMMENT '操作备注',
  KEY `id` (`action_id`),
  KEY `order_id` (`order_id`),
  KEY `create_time` (`create_time`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_order_action_wrong
-- ----------------------------
DROP TABLE IF EXISTS `app_order_action_wrong`;
CREATE TABLE `app_order_action_wrong` (
  `action_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订单审核状态1无效2已审核3取消4关闭',
  `shipping_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '发货状态',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态:1未付款2部分付款3已付款',
  `create_user` varchar(30) NOT NULL DEFAULT '' COMMENT '操作人',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '操作时间',
  `remark` text NOT NULL COMMENT '操作备注',
  PRIMARY KEY (`action_id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_order_address
-- ----------------------------
DROP TABLE IF EXISTS `app_order_address`;
CREATE TABLE `app_order_address` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `consignee` varchar(45) NOT NULL COMMENT '收货人',
  `distribution_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '配送方式',
  `express_id` smallint(6) DEFAULT NULL COMMENT '快递公司ID',
  `freight_no` varchar(50) DEFAULT NULL COMMENT '快递号',
  `country_id` int(10) DEFAULT '0' COMMENT '国家id',
  `province_id` int(10) DEFAULT '0' COMMENT '省份id',
  `city_id` int(10) DEFAULT '0' COMMENT '城市id',
  `regional_id` int(10) DEFAULT '0' COMMENT '区域id',
  `shop_type` int(10) DEFAULT '0' COMMENT '体验店类型',
  `shop_name` varchar(128) DEFAULT NULL COMMENT '体验店名称',
  `address` varchar(100) DEFAULT NULL COMMENT '详细地址',
  `tel` varchar(30) DEFAULT NULL COMMENT '电话',
  `email` varchar(50) DEFAULT NULL COMMENT 'email',
  `zipcode` varchar(20) DEFAULT NULL COMMENT '邮编',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品id',
  `wholesale_id` int(10) DEFAULT NULL COMMENT '批发客户:仓储管理-仓储配置-批发客户管理调用资料',
  `consignee2` varchar(45) DEFAULT NULL,
  `address2` varchar(160) DEFAULT NULL,
  `tel2` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=128472 DEFAULT CHARSET=utf8 COMMENT='订单地址';

-- ----------------------------
-- Table structure for app_order_cart
-- ----------------------------
DROP TABLE IF EXISTS `app_order_cart`;
CREATE TABLE `app_order_cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `session_id` varchar(50) NOT NULL COMMENT 'session的id',
  `goods_id` varchar(30) NOT NULL COMMENT '货号',
  `goods_sn` varchar(15) NOT NULL COMMENT '款号',
  `product_type` smallint(4) DEFAULT NULL COMMENT '产品线',
  `cat_type` tinyint(4) DEFAULT '1' COMMENT '款式分类',
  `goods_type` varchar(20) NOT NULL COMMENT '商品类型：lz裸钻',
  `goods_name` varchar(50) NOT NULL COMMENT '名称',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原价格',
  `favorable_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '优惠价',
  `goods_count` int(8) NOT NULL COMMENT '数量',
  `is_stock_goods` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否是现货：1现货 0期货',
  `cart` varchar(10) DEFAULT NULL COMMENT '石重',
  `cut` varchar(10) DEFAULT NULL COMMENT '切工',
  `clarity` varchar(10) DEFAULT NULL COMMENT '净度',
  `color` varchar(10) DEFAULT NULL COMMENT '颜色',
  `zhengshuhao` varchar(20) DEFAULT NULL COMMENT '证书号',
  `caizhi` varchar(20) DEFAULT NULL COMMENT '材质',
  `jinse` varchar(10) DEFAULT NULL COMMENT '金色',
  `jinzhong` varchar(10) DEFAULT NULL COMMENT '金重',
  `zhiquan` varchar(10) DEFAULT NULL COMMENT '指圈',
  `kezi` varchar(50) DEFAULT NULL COMMENT '刻字',
  `face_work` varchar(20) DEFAULT NULL COMMENT '表面工艺',
  `xiangqian` varchar(20) DEFAULT NULL COMMENT '镶嵌方式',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `modify_time` datetime DEFAULT NULL COMMENT '修改时间',
  `create_user` varchar(10) DEFAULT NULL COMMENT '添加人',
  `department_id` int(10) DEFAULT NULL COMMENT '渠道id',
  `policy_goods_id` int(10) DEFAULT NULL COMMENT '政策对应商品的id',
  `type` tinyint(1) DEFAULT NULL COMMENT '1是普通政策 2打包政策',
  `kuan_sn` varchar(30) DEFAULT NULL COMMENT '天生一对的款号',
  `xiangkou` varchar(50) DEFAULT NULL COMMENT '镶口',
  `is_4c` tinyint(1) unsigned DEFAULT '0' COMMENT '是否为4c 1是 0否',
  `filter_data` text COMMENT '加入购物车前4C搜索条件',
  `tuo_type` varchar(10) DEFAULT NULL,
  `cert` varchar(10) DEFAULT NULL,
  `goods_key` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=383 DEFAULT CHARSET=utf8 COMMENT='购物车表';

-- ----------------------------
-- Table structure for app_order_complaint
-- ----------------------------
DROP TABLE IF EXISTS `app_order_complaint`;
CREATE TABLE `app_order_complaint` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `order_id` int(10) NOT NULL COMMENT '订单ID',
  `cl_feedback_id` tinyint(3) NOT NULL COMMENT '客诉选项',
  `cl_other` varchar(255) DEFAULT NULL COMMENT '客诉备注',
  `cl_user` varchar(60) NOT NULL COMMENT '添加人',
  `cl_time` datetime NOT NULL COMMENT '添加时间',
  `cl_url` varchar(255) DEFAULT NULL COMMENT '图片地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客诉记录表';

-- ----------------------------
-- Table structure for app_order_details
-- ----------------------------
DROP TABLE IF EXISTS `app_order_details`;
CREATE TABLE `app_order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单号',
  `goods_id` varchar(30) NOT NULL DEFAULT '' COMMENT '货号',
  `goods_sn` varchar(60) DEFAULT NULL,
  `ext_goods_sn` varchar(50) DEFAULT ' ' COMMENT '原始款号',
  `goods_name` varchar(200) NOT NULL COMMENT '商品名称',
  `goods_price` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `favorable_price` decimal(10,2) DEFAULT NULL COMMENT '优惠价格:正数代表减钱，负数代表加钱',
  `goods_count` int(8) NOT NULL COMMENT '商品个数',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `modify_time` datetime NOT NULL COMMENT '修改时间',
  `create_user` varchar(100) DEFAULT '' COMMENT '创建人',
  `details_status` tinyint(2) NOT NULL DEFAULT '1',
  `send_good_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1未到货2已发货3到货未检验4到货已检验5返厂',
  `buchan_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '布产状态:1初始化2待分配3已分配4生产中7部分出厂9已出厂10已取消',
  `is_stock_goods` tinyint(1) NOT NULL COMMENT '是否是现货：1现货 0期货',
  `is_return` tinyint(3) NOT NULL DEFAULT '0' COMMENT '退货产品 0未退货1已退货',
  `details_remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `cart` varchar(60) DEFAULT NULL COMMENT '石重',
  `cut` varchar(60) DEFAULT NULL COMMENT '切工',
  `clarity` varchar(60) DEFAULT NULL COMMENT '净度',
  `color` varchar(60) DEFAULT NULL COMMENT '颜色',
  `cert` varchar(10) DEFAULT NULL COMMENT '证书类型',
  `zhengshuhao` varchar(60) DEFAULT NULL COMMENT '证书号',
  `caizhi` varchar(60) DEFAULT NULL COMMENT '材质',
  `jinse` varchar(60) DEFAULT NULL COMMENT '金色',
  `jinzhong` varchar(60) DEFAULT NULL COMMENT '金重',
  `zhiquan` varchar(60) DEFAULT NULL COMMENT '指圈',
  `kezi` varchar(60) DEFAULT NULL COMMENT '刻字',
  `face_work` varchar(60) DEFAULT NULL COMMENT '表面工艺',
  `xiangqian` varchar(60) DEFAULT NULL COMMENT '镶嵌要求',
  `goods_type` varchar(20) DEFAULT NULL COMMENT '商品类型lz:裸钻',
  `favorable_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '优惠审核状态；1：保存；2：提交申请；3：审核通过；4：审核驳回',
  `cat_type` smallint(4) DEFAULT NULL COMMENT '款式分类',
  `product_type` smallint(4) DEFAULT NULL COMMENT '产品线',
  `kuan_sn` varchar(30) DEFAULT NULL COMMENT '天生一对的款号',
  `xiangkou` varchar(50) DEFAULT NULL COMMENT '镶口',
  `chengbenjia` decimal(10,2) DEFAULT '0.00' COMMENT '成本价',
  `bc_id` int(10) DEFAULT '0',
  `policy_id` int(10) DEFAULT NULL COMMENT '销售政策商品',
  `is_peishi` tinyint(1) unsigned DEFAULT '0' COMMENT '是否支持4C配钻，0不支持，1裸钻支持，2空托支持',
  `is_zp` char(2) NOT NULL DEFAULT '0' COMMENT '是否赠品1.是0.否',
  `is_finance` tinyint(2) NOT NULL DEFAULT '2' COMMENT '是否销账,2.是.1否',
  `weixiu_status` tinyint(2) DEFAULT NULL,
  `allow_favorable` tinyint(2) DEFAULT '1' COMMENT '是否允许申请优惠',
  `qiban_type` tinyint(1) unsigned DEFAULT '2' COMMENT '起版类型：见数据字典qiban_type',
  `delivery_status` tinyint(1) DEFAULT '1' COMMENT '1：未配货；2允许配货；5已配货',
  `retail_price` decimal(16,2) DEFAULT '0.00' COMMENT '原始零售价',
  `ds_xiangci` varchar(60) DEFAULT NULL COMMENT '单身-项次（鼎捷）',
  `pinhao` varchar(60) DEFAULT NULL COMMENT '品号（鼎捷）',
  `dia_type` tinyint(1) DEFAULT '0' COMMENT '钻石类型（1、现货钻，2、期货钻）',
  `is_cpdz` tinyint(1) DEFAULT '0',
  `tuo_type` varchar(10) DEFAULT NULL,
  `zhushi_num` smallint(5) unsigned DEFAULT NULL COMMENT '主石粒数',
  `cpdzcode` varchar(11) DEFAULT NULL,
  `discount_point` decimal(10,2) DEFAULT '0.00' COMMENT '折扣积分',
  `reward_point` decimal(10,2) DEFAULT '0.00' COMMENT '奖励积分',
  `daijinquan_code` varchar(30) DEFAULT NULL COMMENT '代金券兑换码',
  `daijinquan_price` decimal(10,2) DEFAULT '0.00' COMMENT '代金券优惠金额',
  `daijinquan_addtime` datetime DEFAULT NULL COMMENT '代金券兑换码使用时间',
  `jifenma_code` varchar(30) DEFAULT NULL COMMENT '积分码',
  `jifenma_point` decimal(8,2) DEFAULT '0.00' COMMENT '积分码赠送积分',
  `zhuandan_cash` decimal(10,2) DEFAULT '0.00',
  `goods_from` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3314392 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_order_details_bak
-- ----------------------------
DROP TABLE IF EXISTS `app_order_details_bak`;
CREATE TABLE `app_order_details_bak` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单号',
  `goods_id` varchar(30) NOT NULL DEFAULT '' COMMENT '货号',
  `goods_sn` varchar(60) DEFAULT NULL,
  `ext_goods_sn` varchar(50) DEFAULT ' ' COMMENT '原始款号',
  `goods_name` varchar(200) NOT NULL COMMENT '商品名称',
  `goods_price` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `favorable_price` decimal(10,2) DEFAULT NULL COMMENT '优惠价格:正数代表减钱，负数代表加钱',
  `goods_count` int(8) NOT NULL COMMENT '商品个数',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `modify_time` datetime NOT NULL COMMENT '修改时间',
  `create_user` varchar(10) DEFAULT '' COMMENT '创建人',
  `details_status` tinyint(2) NOT NULL DEFAULT '1',
  `send_good_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1未发货2已发货3收货确认4允许发货5已到店',
  `buchan_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '布产状态:1初始化2待分配3已分配4生产中7部分出厂9已出厂10已取消',
  `is_stock_goods` tinyint(1) NOT NULL COMMENT '是否是现货：1现货 0期货',
  `is_return` tinyint(3) NOT NULL DEFAULT '0' COMMENT '退货产品 0未退货1已退货',
  `details_remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `cart` varchar(60) DEFAULT NULL COMMENT '石重',
  `cut` varchar(60) DEFAULT NULL COMMENT '切工',
  `clarity` varchar(60) DEFAULT NULL COMMENT '净度',
  `color` varchar(60) DEFAULT NULL COMMENT '颜色',
  `cert` varchar(10) DEFAULT NULL COMMENT '证书类型',
  `zhengshuhao` varchar(60) DEFAULT NULL COMMENT '证书号',
  `caizhi` varchar(60) DEFAULT NULL COMMENT '材质',
  `jinse` varchar(60) DEFAULT NULL COMMENT '金色',
  `jinzhong` varchar(60) DEFAULT NULL COMMENT '金重',
  `zhiquan` varchar(60) DEFAULT NULL COMMENT '指圈',
  `kezi` varchar(60) DEFAULT NULL COMMENT '刻字',
  `face_work` varchar(60) DEFAULT NULL COMMENT '表面工艺',
  `xiangqian` varchar(60) NOT NULL COMMENT '镶嵌要求',
  `goods_type` varchar(20) DEFAULT NULL COMMENT '商品类型lz:裸钻',
  `favorable_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '优惠审核状态；1：保存；2：提交申请；3：审核通过；4：审核驳回',
  `cat_type` smallint(4) DEFAULT NULL COMMENT '款式分类',
  `product_type` smallint(4) DEFAULT NULL COMMENT '产品线',
  `kuan_sn` varchar(30) DEFAULT NULL COMMENT '天生一对的款号',
  `xiangkou` varchar(50) DEFAULT NULL COMMENT '镶口',
  `chengbenjia` decimal(10,2) DEFAULT '0.00' COMMENT '成本价',
  `bc_id` int(10) DEFAULT '0',
  `policy_id` int(10) DEFAULT NULL COMMENT '销售政策商品',
  `is_peishi` tinyint(1) unsigned DEFAULT '0' COMMENT '是否支持4C配钻，0不支持，1裸钻支持，2空托支持',
  `is_zp` char(2) NOT NULL DEFAULT '0' COMMENT '是否赠品1.是0.否',
  `is_finance` tinyint(2) NOT NULL DEFAULT '2' COMMENT '是否销账,2.是.1否',
  `weixiu_status` tinyint(2) DEFAULT NULL,
  `allow_favorable` tinyint(2) DEFAULT '1' COMMENT '是否允许申请优惠',
  `qiban_type` tinyint(1) unsigned DEFAULT '2' COMMENT '起版类型：见数据字典qiban_type',
  `delivery_status` tinyint(1) DEFAULT '1' COMMENT '1：未配货；2允许配货；5已配货',
  `retail_price` decimal(16,2) DEFAULT '0.00' COMMENT '原始零售价',
  `jxc_in_price` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '金多宝进价',
  `jxc_out_price` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '金多宝出价',
  `ds_xiangci` varchar(60) DEFAULT NULL COMMENT '单身-项次（鼎捷）',
  `pinhao` varchar(50) DEFAULT NULL COMMENT '品号（鼎捷）',
  `dia_type` tinyint(1) DEFAULT '0',
  `is_cpdz` tinyint(1) DEFAULT '0',
  `tuo_type` varchar(10) DEFAULT NULL,
  `zhushi_num` smallint(5) unsigned DEFAULT NULL COMMENT '主石粒数',
  `cpdzcode` varchar(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `goods_id` (`goods_id`),
  KEY `goods_sn` (`goods_sn`),
  KEY `goods_name` (`goods_name`),
  KEY `zhengshuhao` (`zhengshuhao`),
  KEY `cart` (`cart`),
  KEY `bc_id` (`bc_id`),
  KEY `zhengshu_peishi` (`zhengshuhao`,`is_peishi`) USING BTREE,
  KEY `goods_type` (`goods_type`),
  KEY `product_type` (`product_type`),
  KEY `cat_type` (`cat_type`),
  KEY `ext_goods_sn` (`ext_goods_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=250084 DEFAULT CHARSET=utf8 COMMENT='订单商品表';

-- ----------------------------
-- Table structure for app_order_feedback
-- ----------------------------
DROP TABLE IF EXISTS `app_order_feedback`;
CREATE TABLE `app_order_feedback` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ks_option` varchar(255) NOT NULL COMMENT '客诉选项',
  `ks_user` varchar(60) NOT NULL COMMENT '添加人',
  `ks_time` datetime NOT NULL COMMENT '添加时间',
  `ks_status` tinyint(1) NOT NULL COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客诉总档表';

-- ----------------------------
-- Table structure for app_order_invoice
-- ----------------------------
DROP TABLE IF EXISTS `app_order_invoice`;
CREATE TABLE `app_order_invoice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL COMMENT '订单id',
  `is_invoice` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否需要发票 1:需要 0：不需要',
  `invoice_title` varchar(60) NOT NULL DEFAULT '个人' COMMENT '发票抬头',
  `invoice_content` text COMMENT '发票内容',
  `invoice_status` tinyint(1) DEFAULT NULL COMMENT '1未开发票2已开发票3发票作废',
  `invoice_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '发票金额',
  `invoice_address` varchar(255) DEFAULT NULL COMMENT '发票邮寄地址',
  `invoice_num` varchar(35) DEFAULT NULL COMMENT '发票号',
  `create_user` varchar(50) DEFAULT '' COMMENT '创建人',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `use_user` varchar(50) DEFAULT '' COMMENT '使用人',
  `use_time` datetime DEFAULT NULL COMMENT '使用时间',
  `open_sn` varchar(50) DEFAULT NULL COMMENT '外部发票流水号',
  `invoice_type` tinyint(1) unsigned DEFAULT '1' COMMENT '发票类型 1普通发票 2电子发票',
  `taxpayer_sn` varchar(50) DEFAULT NULL COMMENT '纳税人识别号',
  `title_type` tinyint(1) unsigned DEFAULT '1' COMMENT '抬头类型 1个人 2公司',
  `invoice_email` varchar(80) DEFAULT '' COMMENT '发票发送邮箱地址',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `is_invoice` (`is_invoice`),
  KEY `invoice_num` (`invoice_num`)
) ENGINE=InnoDB AUTO_INCREMENT=128201 DEFAULT CHARSET=utf8 COMMENT='订单发票信息';

-- ----------------------------
-- Table structure for app_order_invoice20181111
-- ----------------------------
DROP TABLE IF EXISTS `app_order_invoice20181111`;
CREATE TABLE `app_order_invoice20181111` (
  `outid` varchar(60) DEFAULT NULL,
  `order_id` int(10) DEFAULT NULL,
  `order_sn` varchar(48) DEFAULT NULL,
  `create_user` varchar(30) NOT NULL COMMENT '制单人',
  `invoice_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '发票金额'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_order_invoice2018111102
-- ----------------------------
DROP TABLE IF EXISTS `app_order_invoice2018111102`;
CREATE TABLE `app_order_invoice2018111102` (
  `outid` varchar(60) DEFAULT NULL,
  `order_id` int(10) DEFAULT NULL,
  `order_sn` varchar(48) DEFAULT NULL,
  `create_user` varchar(30) NOT NULL COMMENT '制单人',
  `invoice_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '发票金额'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_order_sn_list
-- ----------------------------
DROP TABLE IF EXISTS `app_order_sn_list`;
CREATE TABLE `app_order_sn_list` (
  `id` int(10) NOT NULL,
  `order_sn` varchar(50) NOT NULL COMMENT '订单号',
  `add_time` date NOT NULL COMMENT '添加日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单号列表';

-- ----------------------------
-- Table structure for app_order_time
-- ----------------------------
DROP TABLE IF EXISTS `app_order_time`;
CREATE TABLE `app_order_time` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单id',
  `allow_shop_time` datetime NOT NULL COMMENT '允许到店时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单时间统计表';

-- ----------------------------
-- Table structure for app_return_check
-- ----------------------------
DROP TABLE IF EXISTS `app_return_check`;
CREATE TABLE `app_return_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `return_id` int(10) unsigned NOT NULL COMMENT '退款单id',
  `leader_id` int(10) DEFAULT NULL COMMENT '部门主管',
  `leader_res` varchar(255) DEFAULT NULL COMMENT '业务负责人意见',
  `leader_status` tinyint(3) DEFAULT NULL COMMENT '主管审核状态',
  `leader_time` datetime DEFAULT NULL COMMENT '主管审核时间',
  `goods_comfirm_id` int(10) DEFAULT NULL COMMENT '库管ID',
  `goods_res` varchar(255) DEFAULT NULL COMMENT '库管部门意见',
  `goods_status` tinyint(3) DEFAULT '0' COMMENT '产品状态,0,未确认,1,审核通过,2审核驳回',
  `goods_time` datetime DEFAULT NULL COMMENT '产品状态操作时间',
  `cto_id` int(10) DEFAULT NULL COMMENT 'CTOID',
  `cto_res` varchar(255) DEFAULT NULL COMMENT 'CTO意见',
  `cto_status` tinyint(3) DEFAULT NULL COMMENT 'CTO状态：0为操作，1批准',
  `cto_time` datetime DEFAULT NULL COMMENT 'CTO操作时间',
  `deparment_finance_id` int(10) DEFAULT NULL COMMENT '部门财务id',
  `deparment_finance_status` tinyint(1) DEFAULT NULL COMMENT '部门财务审核状态0,未操作,1已审核',
  `deparment_finance_res` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '部门财务备注',
  `deparment_finance_time` datetime DEFAULT NULL COMMENT '部门财务操作时间',
  `finance_id` int(10) DEFAULT NULL COMMENT '财务操作人ID',
  `bak_fee` decimal(10,2) DEFAULT NULL COMMENT '支付手续费',
  `finance_res` varchar(255) DEFAULT NULL COMMENT '财务意见',
  `finance_status` tinyint(3) DEFAULT NULL COMMENT '财务状态,0未操作,1,已确认',
  `finance_time` datetime DEFAULT NULL COMMENT '财务操作时间',
  `pay_id` int(10) DEFAULT NULL COMMENT '实际付款人id',
  `pay_res` varchar(255) DEFAULT NULL COMMENT '付款备注',
  `pay_status` tinyint(1) DEFAULT NULL COMMENT '支付状态',
  `pay_attach` varchar(255) DEFAULT NULL COMMENT '付款附件',
  `old_goods_status` tinyint(3) DEFAULT '0' COMMENT '备份以前库管审核状态：0,未确认,1,留库存,2未出库',
  PRIMARY KEY (`id`),
  KEY `return_id` (`return_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=36141 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_return_check_bak0522
-- ----------------------------
DROP TABLE IF EXISTS `app_return_check_bak0522`;
CREATE TABLE `app_return_check_bak0522` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `return_id` int(10) unsigned NOT NULL COMMENT '退款单id',
  `leader_id` int(10) NOT NULL COMMENT '部门主管',
  `leader_res` varchar(255) NOT NULL COMMENT '业务负责人意见',
  `leader_status` tinyint(3) NOT NULL COMMENT '主管审核状态',
  `leader_time` datetime NOT NULL COMMENT '主管审核时间',
  `goods_comfirm_id` int(10) DEFAULT NULL COMMENT '库管ID',
  `goods_res` varchar(255) DEFAULT NULL COMMENT '库管部门意见',
  `goods_status` tinyint(3) DEFAULT NULL COMMENT '产品状态,0,未确认,1,留库存,2未出库',
  `goods_time` datetime DEFAULT NULL COMMENT '产品状态操作时间',
  `cto_id` int(10) DEFAULT NULL COMMENT 'CTOID',
  `cto_res` varchar(255) DEFAULT NULL COMMENT 'CTO意见',
  `cto_status` tinyint(3) DEFAULT NULL COMMENT 'CTO状态：0为操作，1批准',
  `cto_time` datetime DEFAULT NULL COMMENT 'CTO操作时间',
  `deparment_finance_id` int(10) DEFAULT NULL COMMENT '部门财务id',
  `deparment_finance_status` tinyint(1) DEFAULT NULL COMMENT '部门财务审核状态0,未操作,1已审核',
  `deparment_finance_res` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '部门财务备注',
  `deparment_finance_time` datetime DEFAULT NULL COMMENT '部门财务操作时间',
  `finance_id` int(10) DEFAULT NULL COMMENT '财务操作人ID',
  `bak_fee` decimal(10,2) DEFAULT NULL COMMENT '支付手续费',
  `finance_res` varchar(255) DEFAULT NULL COMMENT '财务意见',
  `finance_status` tinyint(3) DEFAULT NULL COMMENT '财务状态,0未操作,1,已确认',
  `finance_time` datetime DEFAULT NULL COMMENT '财务操作时间',
  `pay_id` int(10) DEFAULT NULL COMMENT '实际付款人id',
  `pay_res` varchar(255) DEFAULT NULL COMMENT '付款备注',
  `pay_status` tinyint(1) DEFAULT NULL COMMENT '支付状态',
  `pay_attach` varchar(255) DEFAULT NULL COMMENT '付款附件'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_return_goods
-- ----------------------------
DROP TABLE IF EXISTS `app_return_goods`;
CREATE TABLE `app_return_goods` (
  `return_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '退款单id',
  `department` int(11) NOT NULL COMMENT '所属部门',
  `apply_user_id` int(10) NOT NULL COMMENT '申请人',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `order_sn` varchar(20) NOT NULL COMMENT '订单编号',
  `order_goods_id` int(10) NOT NULL,
  `should_return_amount` decimal(10,2) NOT NULL COMMENT '应退金额',
  `apply_return_amount` decimal(10,2) NOT NULL COMMENT '申请金额',
  `real_return_amount` decimal(10,2) NOT NULL COMMENT '实退金额',
  `confirm_price` decimal(10,2) DEFAULT NULL COMMENT '审核金额',
  `return_res` varchar(255) NOT NULL COMMENT '退款原因',
  `return_by` tinyint(1) unsigned DEFAULT NULL COMMENT '退款方式 1退商品，2不退商品',
  `return_type` tinyint(3) NOT NULL COMMENT '退款类型,1转单,2打卡,3现金',
  `return_card` varchar(60) NOT NULL COMMENT '退款账户',
  `consignee` varchar(60) NOT NULL COMMENT '退款人',
  `mobile` varchar(32) NOT NULL COMMENT '联系电话',
  `bank_name` varchar(60) NOT NULL COMMENT '开户银行',
  `apply_time` datetime NOT NULL COMMENT '申请时间',
  `pay_id` int(10) DEFAULT NULL COMMENT '实际付款人',
  `pay_res` varchar(255) DEFAULT NULL COMMENT '付款备注',
  `pay_status` tinyint(3) DEFAULT NULL COMMENT '支付状态',
  `pay_attach` varchar(255) DEFAULT NULL COMMENT '付款附件',
  `pay_order_sn` varchar(20) DEFAULT NULL COMMENT '支付的订单号',
  `jxc_order` varchar(30) DEFAULT NULL COMMENT '进销存退货单',
  `zhuandan_amount` decimal(10,2) DEFAULT NULL,
  `check_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未操作1主管审核通过2库管审核通过3事业部通过4现场财务通过5财务通过',
  `return_goods_id` varchar(255) DEFAULT NULL COMMENT '退货商品ID',
  PRIMARY KEY (`return_id`),
  KEY `apply_user_id` (`apply_user_id`),
  KEY `order_goods_id` (`order_goods_id`),
  KEY `order_id` (`order_id`),
  KEY `order_sn` (`order_sn`),
  KEY `pay_order_sn` (`pay_order_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=36011 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_return_log
-- ----------------------------
DROP TABLE IF EXISTS `app_return_log`;
CREATE TABLE `app_return_log` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `return_id` mediumint(8) unsigned NOT NULL COMMENT '退款单id',
  `even_time` datetime NOT NULL COMMENT '操作时间',
  `even_user` varchar(20) NOT NULL COMMENT '操作人',
  `even_content` varchar(255) NOT NULL COMMENT '操作内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3646 DEFAULT CHARSET=utf8 COMMENT='退款单操作日志';

-- ----------------------------
-- Table structure for app_return_point
-- ----------------------------
DROP TABLE IF EXISTS `app_return_point`;
CREATE TABLE `app_return_point` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(30) NOT NULL DEFAULT '0' COMMENT '订单号',
  `order_detail_id` int(10) NOT NULL DEFAULT '0' COMMENT '订单明细ID',
  `return_id` int(10) NOT NULL DEFAULT '0' COMMENT '退货明细ID',
  `return_by` tinyint(1) DEFAULT NULL,
  `return_type` tinyint(1) NOT NULL COMMENT '退款类型,1转单,2打卡,3现金',
  `goods_id` varchar(30) DEFAULT NULL,
  `goods_sn` varchar(30) DEFAULT NULL,
  `return_cash` float(10,2) DEFAULT NULL COMMENT '实际退款金额s',
  `discount_point` int(10) NOT NULL DEFAULT '0' COMMENT '退货扣除折扣积分',
  `reward_point` int(10) NOT NULL DEFAULT '0' COMMENT '退货扣除奖励积分',
  `jifenma_point` int(10) NOT NULL DEFAULT '0' COMMENT '退货扣除积分码积分',
  `create_user` varchar(30) DEFAULT NULL COMMENT '创建人',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for base_order_info
-- ----------------------------
DROP TABLE IF EXISTS `base_order_info`;
CREATE TABLE `base_order_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL COMMENT '订单编号',
  `old_order_id` int(10) NOT NULL DEFAULT '0' COMMENT '老订单号',
  `bespoke_id` int(10) NOT NULL DEFAULT '0' COMMENT '预约号',
  `old_bespoke_id` int(10) DEFAULT '0' COMMENT '老预约号',
  `user_id` int(8) NOT NULL COMMENT '会员id',
  `consignee` varchar(30) DEFAULT NULL COMMENT '名字',
  `mobile` varchar(32) DEFAULT NULL COMMENT '手机号',
  `order_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单审核状态: 1（默认待审核）2已审核 3取消 4关闭5审核未通过',
  `order_pay_status` tinyint(2) DEFAULT '1' COMMENT '支付状态:1未付款2部分付款3已付款4财务备案',
  `order_pay_type` smallint(1) DEFAULT '0' COMMENT '支付类型;0:默认，1:展厅订购,2:货到付款',
  `delivery_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '[参考数字字典：配送状态(sales.delivery_status)]',
  `send_good_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1未发货2已发货3收货确认4允许发货5已到店',
  `buchan_status` tinyint(1) DEFAULT NULL COMMENT '布产状态:1未操作,2已布产,3生产中,4已出厂,5不需布产',
  `customer_source_id` int(8) NOT NULL COMMENT '客户来源',
  `department_id` varchar(20) DEFAULT NULL COMMENT '订单部门',
  `create_time` datetime NOT NULL COMMENT '制单时间',
  `create_user` varchar(30) NOT NULL COMMENT '制单人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `check_user` varchar(45) DEFAULT NULL COMMENT '审核人',
  `genzong` varchar(20) DEFAULT NULL COMMENT '跟单人',
  `recommended` varchar(50) DEFAULT NULL COMMENT '推荐人',
  `recommender_sn` varchar(50) DEFAULT NULL COMMENT '推荐人会员编号',
  `modify_time` datetime NOT NULL COMMENT '修改时间',
  `order_remark` varchar(255) DEFAULT NULL COMMENT '备注信息',
  `referer` varchar(20) NOT NULL DEFAULT '未知' COMMENT '录单来源',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态0有效1删除',
  `apply_close` tinyint(1) NOT NULL DEFAULT '0' COMMENT '申请关闭:0=未申请，1=申请关闭',
  `is_xianhuo` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否是现货：1现货 0定制',
  `is_print_tihuo` tinyint(1) DEFAULT '0' COMMENT '是否打印提货单（数字字典confirm）',
  `effect_date` datetime DEFAULT NULL COMMENT '订单生效时间(确定布产)',
  `is_zp` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单是否是赠品订单 1是0不是',
  `pay_date` datetime DEFAULT NULL COMMENT '第一次点款时间',
  `apply_return` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1未操作2正在退款',
  `weixiu_status` tinyint(2) DEFAULT '0' COMMENT '维修状态',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `shipfreight_time` datetime DEFAULT '0000-00-00 00:00:00',
  `is_real_invoice` tinyint(255) NOT NULL DEFAULT '1' COMMENT '是否需要开发票',
  `out_company` int(255) DEFAULT '0',
  `discount_point` decimal(10,2) DEFAULT '0.00' COMMENT '折扣积分',
  `reward_point` decimal(10,2) DEFAULT '0.00' COMMENT '奖励积分',
  `jifenma_point` decimal(10,2) DEFAULT '0.00' COMMENT '积分码积分',
  `zhuandan_cash` decimal(10,2) DEFAULT '0.00' COMMENT '转单金额',
  `hidden` tinyint(1) DEFAULT '0' COMMENT '是否隐藏栏位',
  `birthday` date DEFAULT NULL COMMENT '会员生日',
  `profile_id` int(10) DEFAULT NULL COMMENT 'crm会员信息id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_sn` (`order_sn`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2822641 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for base_order_info_auth
-- ----------------------------
DROP TABLE IF EXISTS `base_order_info_auth`;
CREATE TABLE `base_order_info_auth` (
  `id` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for base_order_info_bak
-- ----------------------------
DROP TABLE IF EXISTS `base_order_info_bak`;
CREATE TABLE `base_order_info_bak` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL COMMENT '订单编号',
  `old_order_id` int(10) NOT NULL DEFAULT '0' COMMENT '老订单号',
  `bespoke_id` int(10) NOT NULL DEFAULT '0' COMMENT '预约号',
  `old_bespoke_id` int(10) DEFAULT '0' COMMENT '老预约号',
  `user_id` int(8) NOT NULL COMMENT '会员id',
  `consignee` varchar(30) DEFAULT NULL COMMENT '名字',
  `mobile` varchar(32) DEFAULT NULL COMMENT '手机号',
  `order_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单审核状态: 1（默认待审核）2已审核 3取消 4关闭5审核未通过',
  `order_pay_status` tinyint(2) DEFAULT '1' COMMENT '支付状态:1未付款2部分付款3已付款4财务备案',
  `order_pay_type` smallint(1) DEFAULT '0' COMMENT '支付类型;0:默认，1:展厅订购,2:货到付款',
  `delivery_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '[参考数字字典：配送状态(sales.delivery_status)]',
  `send_good_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1未发货2已发货3收货确认4允许发货5已到店',
  `buchan_status` tinyint(1) DEFAULT NULL COMMENT '布产状态:1未操作,2已布产,3生产中,4已出厂,5不需布产',
  `customer_source_id` int(8) NOT NULL COMMENT '客户来源',
  `department_id` varchar(20) DEFAULT NULL COMMENT '订单部门',
  `create_time` datetime NOT NULL COMMENT '制单时间',
  `create_user` varchar(10) NOT NULL COMMENT '制单人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `check_user` varchar(45) DEFAULT NULL COMMENT '审核人',
  `genzong` varchar(20) DEFAULT NULL COMMENT '跟单人',
  `recommended` varchar(50) DEFAULT NULL COMMENT '推荐人',
  `modify_time` datetime NOT NULL COMMENT '修改时间',
  `order_remark` varchar(255) DEFAULT NULL COMMENT '备注信息',
  `referer` varchar(20) NOT NULL DEFAULT '未知' COMMENT '录单来源',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态0有效1删除',
  `apply_close` tinyint(1) NOT NULL DEFAULT '0' COMMENT '申请关闭:0=未申请，1=申请关闭',
  `is_xianhuo` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否是现货：1现货 0定制',
  `is_print_tihuo` tinyint(1) DEFAULT '0' COMMENT '是否打印提货单（数字字典confirm）',
  `effect_date` datetime DEFAULT NULL COMMENT '订单生效时间(确定布产)',
  `is_zp` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单是否是赠品订单 1是0不是',
  `pay_date` datetime DEFAULT NULL COMMENT '第一次点款时间',
  `apply_return` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1未操作2正在退款',
  `weixiu_status` tinyint(2) DEFAULT '0' COMMENT '维修状态',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `shipfreight_time` datetime DEFAULT '0000-00-00 00:00:00',
  `is_real_invoice` tinyint(255) NOT NULL DEFAULT '1' COMMENT '是否需要开发票',
  `out_company` int(255) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_sn_2` (`order_sn`),
  KEY `old_order_id` (`old_order_id`),
  KEY `user_id` (`user_id`),
  KEY `consignee` (`consignee`),
  KEY `mobile` (`mobile`),
  KEY `customer_source_id` (`customer_source_id`),
  KEY `department_id` (`department_id`),
  KEY `create_user` (`create_user`),
  KEY `referer` (`referer`),
  KEY `is_zp` (`is_zp`),
  KEY `order_status` (`order_status`),
  KEY `pay_date` (`pay_date`),
  KEY `update_time` (`update_time`),
  KEY `create_time` (`create_time`),
  KEY `bespoke_id` (`bespoke_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=130388 DEFAULT CHARSET=utf8 COMMENT='订单信息表';

-- ----------------------------
-- Table structure for base_order_info_重要勿删
-- ----------------------------
DROP TABLE IF EXISTS `base_order_info_重要勿删`;
CREATE TABLE `base_order_info_重要勿删` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL COMMENT '订单编号',
  `old_order_id` int(10) NOT NULL DEFAULT '0' COMMENT '老订单号',
  `bespoke_id` int(10) NOT NULL DEFAULT '0' COMMENT '预约号',
  `old_bespoke_id` int(10) DEFAULT '0' COMMENT '老预约号',
  `user_id` int(8) NOT NULL COMMENT '会员id',
  `consignee` varchar(30) DEFAULT NULL COMMENT '名字',
  `mobile` varchar(32) DEFAULT NULL COMMENT '手机号',
  `order_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单审核状态: 1（默认待审核）2已审核 3取消 4关闭5审核未通过',
  `order_pay_status` tinyint(2) DEFAULT '1' COMMENT '支付状态:1未付款2部分付款3已付款4财务备案',
  `order_pay_type` smallint(1) DEFAULT '0' COMMENT '支付类型;0:默认，1:展厅订购,2:货到付款',
  `delivery_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '[参考数字字典：配送状态(sales.delivery_status)]',
  `send_good_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1未发货2已发货3收货确认4允许发货5已到店',
  `buchan_status` tinyint(1) DEFAULT NULL COMMENT '布产状态:1未操作,2已布产,3生产中,4已出厂,5不需布产',
  `customer_source_id` int(8) NOT NULL COMMENT '客户来源',
  `department_id` varchar(20) DEFAULT NULL COMMENT '订单部门',
  `create_time` datetime NOT NULL COMMENT '制单时间',
  `create_user` varchar(10) NOT NULL COMMENT '制单人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `check_user` varchar(45) DEFAULT NULL COMMENT '审核人',
  `genzong` varchar(20) DEFAULT NULL COMMENT '跟单人',
  `recommended` varchar(50) DEFAULT NULL COMMENT '推荐人',
  `modify_time` datetime NOT NULL COMMENT '修改时间',
  `order_remark` varchar(255) DEFAULT NULL COMMENT '备注信息',
  `referer` varchar(20) NOT NULL DEFAULT '未知' COMMENT '录单来源',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态0有效1删除',
  `apply_close` tinyint(1) NOT NULL DEFAULT '0' COMMENT '申请关闭:0=未申请，1=申请关闭',
  `is_xianhuo` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否是现货：1现货 0定制',
  `is_print_tihuo` tinyint(1) DEFAULT '0' COMMENT '是否打印提货单（数字字典confirm）',
  `effect_date` datetime DEFAULT NULL COMMENT '订单生效时间(确定布产)',
  `is_zp` tinyint(1) NOT NULL DEFAULT '1' COMMENT '订单是否是赠品订单 1是0不是',
  `pay_date` datetime DEFAULT NULL COMMENT '第一次点款时间',
  `apply_return` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1未操作2正在退款',
  `weixiu_status` tinyint(2) DEFAULT '0' COMMENT '维修状态',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `shipfreight_time` datetime DEFAULT '0000-00-00 00:00:00',
  `is_real_invoice` tinyint(255) NOT NULL DEFAULT '1' COMMENT '是否需要开发票',
  `out_company` int(255) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_sn_2` (`order_sn`),
  KEY `old_order_id` (`old_order_id`),
  KEY `user_id` (`user_id`),
  KEY `consignee` (`consignee`),
  KEY `mobile` (`mobile`),
  KEY `customer_source_id` (`customer_source_id`),
  KEY `department_id` (`department_id`),
  KEY `create_user` (`create_user`),
  KEY `referer` (`referer`),
  KEY `is_zp` (`is_zp`),
  KEY `order_status` (`order_status`),
  KEY `pay_date` (`pay_date`),
  KEY `update_time` (`update_time`),
  KEY `create_time` (`create_time`),
  KEY `bespoke_id` (`bespoke_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=35473 DEFAULT CHARSET=utf8 COMMENT='订单信息表';

-- ----------------------------
-- Table structure for base_order_info20170111
-- ----------------------------
DROP TABLE IF EXISTS `base_order_info20170111`;
CREATE TABLE `base_order_info20170111` (
  `order_sn` varchar(20) NOT NULL COMMENT '订单编号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for base_order_info20170118
-- ----------------------------
DROP TABLE IF EXISTS `base_order_info20170118`;
CREATE TABLE `base_order_info20170118` (
  `order_sn` varchar(20) NOT NULL COMMENT '订单编号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for base_order_info20170602
-- ----------------------------
DROP TABLE IF EXISTS `base_order_info20170602`;
CREATE TABLE `base_order_info20170602` (
  `order_sn` varchar(20) NOT NULL COMMENT '订单编号',
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'id',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `consignee` varchar(45) NOT NULL COMMENT '收货人',
  `distribution_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '配送方式',
  `express_id` smallint(6) DEFAULT NULL COMMENT '快递公司ID',
  `freight_no` varchar(50) DEFAULT NULL COMMENT '快递号',
  `country_id` int(10) DEFAULT '0' COMMENT '国家id',
  `province_id` int(10) DEFAULT '0' COMMENT '省份id',
  `city_id` int(10) DEFAULT '0' COMMENT '城市id',
  `regional_id` int(10) DEFAULT '0' COMMENT '区域id',
  `shop_type` int(10) DEFAULT '0' COMMENT '体验店类型',
  `shop_name` varchar(128) DEFAULT NULL COMMENT '体验店名称',
  `address` varchar(100) DEFAULT NULL COMMENT '详细地址',
  `tel` varchar(30) DEFAULT NULL COMMENT '电话',
  `email` varchar(50) DEFAULT NULL COMMENT 'email',
  `zipcode` varchar(20) DEFAULT NULL COMMENT '邮编',
  `goods_id` int(10) DEFAULT NULL COMMENT '商品id',
  `wholesale_id` int(10) DEFAULT NULL COMMENT '批发客户:仓储管理-仓储配置-批发客户管理调用资料'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for bills_remark
-- ----------------------------
DROP TABLE IF EXISTS `bills_remark`;
CREATE TABLE `bills_remark` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '单据备注',
  `bill_no` varchar(30) DEFAULT NULL COMMENT '单据sn',
  `bill_type` int(11) DEFAULT NULL COMMENT '单据类型 1、线上订单布产 2、线下订单布产 3、配钻 4、工厂出厂 5、网络配货 6、线下配货 7、P单未及时审核',
  `create_user` varchar(30) DEFAULT NULL COMMENT '用户姓名',
  `remark` varchar(100) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`),
  UNIQUE KEY `bill_no` (`bill_no`,`bill_type`)
) ENGINE=InnoDB AUTO_INCREMENT=6717 DEFAULT CHARSET=utf8 COMMENT='单据备注表';

-- ----------------------------
-- Table structure for boss_order_goods
-- ----------------------------
DROP TABLE IF EXISTS `boss_order_goods`;
CREATE TABLE `boss_order_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `goods_id` varchar(11) NOT NULL DEFAULT '',
  `cert_id` varchar(60) DEFAULT NULL COMMENT '证书号',
  `color` varchar(60) DEFAULT NULL COMMENT '颜色',
  `carat` varchar(60) DEFAULT NULL COMMENT '石重',
  `cut` varchar(60) DEFAULT NULL COMMENT '切工',
  `clarity` varchar(60) DEFAULT NULL COMMENT '净度',
  `consignee` varchar(30) DEFAULT NULL COMMENT '名字',
  `order_status` int(1) NOT NULL DEFAULT '0',
  `order_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `cert` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for deduct_percentage_money
-- ----------------------------
DROP TABLE IF EXISTS `deduct_percentage_money`;
CREATE TABLE `deduct_percentage_money` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `search_date` varchar(20) DEFAULT NULL COMMENT '日期',
  `department_id` int(8) DEFAULT NULL COMMENT '渠道ID',
  `department_name` varchar(150) DEFAULT NULL COMMENT '渠道名称',
  `sales_name` varchar(20) DEFAULT NULL COMMENT '销售顾问',
  `should_ticheng_price` decimal(8,2) DEFAULT NULL COMMENT '应发提成',
  `baodi_price` decimal(8,2) DEFAULT NULL COMMENT '保底任务',
  `real_add_price` decimal(8,2) DEFAULT NULL COMMENT '实际总新增',
  `hbh_add_price` decimal(8,2) DEFAULT NULL COMMENT '婚博会新增金额	',
  `undiscount_add_price` decimal(8,2) DEFAULT NULL COMMENT '低于折扣下限新增核算金额',
  `cp_add_price` decimal(8,2) DEFAULT NULL COMMENT '成品新增核算金额（非婚博会&高于折扣下限）',
  `lzxy_add_price` decimal(8,2) DEFAULT NULL COMMENT '裸钻星耀新增核算金额（非婚博会&高于折扣下限）',
  `lzfxy_add_price` decimal(8,2) DEFAULT NULL COMMENT '裸钻非星耀新增核算金额（非婚博会&高于折扣下限）',
  `tejia_add_price` decimal(8,2) DEFAULT NULL COMMENT '特价商品新增金额',
  `total_add_price` decimal(8,2) DEFAULT NULL COMMENT '总新增核算金额',
  `real_return_price` decimal(8,2) DEFAULT NULL COMMENT '实际总转退',
  `hbh_return_price` decimal(8,2) DEFAULT NULL COMMENT '婚博会转退金额',
  `undiscount_return_price` decimal(8,2) DEFAULT NULL COMMENT '低于折扣下限转退核算金额',
  `cp_return_price` decimal(8,2) DEFAULT NULL COMMENT '成品转退核算金额（非婚博会&高于折扣下限）',
  `lzxy_return_price` decimal(8,2) DEFAULT NULL COMMENT '裸钻星耀转退核算金额（非婚博会&高于折扣下限）',
  `lzfxy_return_price` decimal(8,2) DEFAULT NULL COMMENT '裸钻非星耀转退核算金额（非婚博会&高于折扣下限）',
  `tejia_return_price` decimal(8,2) DEFAULT NULL COMMENT '特价商品转退金额',
  `total_return_price` decimal(8,2) DEFAULT NULL COMMENT '总转退核算金额',
  `real_deduct_price` decimal(8,2) DEFAULT NULL COMMENT '实际总新增扣除转退',
  `hbh_deduct_price` decimal(8,2) DEFAULT NULL COMMENT '婚博会新增扣除转退金额',
  `undiscount_deduct_price` decimal(8,2) DEFAULT NULL COMMENT '低于折扣下限核算新增扣除转退金额（非婚博会）',
  `cp_deduct_price` decimal(8,2) DEFAULT NULL COMMENT '成品核算新增扣除转退金额（非婚博会&高于折扣下限）',
  `lzxy_deduct_price` decimal(8,2) DEFAULT NULL COMMENT '裸钻星耀核算新增扣除转退金额（非婚博会&高于折扣下限）',
  `lzfxy_deduct_price` decimal(8,2) DEFAULT NULL COMMENT '裸钻非星耀核算新增扣除转退金额（非婚博会&高于折扣下限）',
  `tejia_deduct_price` decimal(8,2) DEFAULT NULL COMMENT '特价商品新增扣除转退金额',
  `total_deduct_price` decimal(8,2) DEFAULT NULL COMMENT '总新增扣除转退核算金额	',
  `is_dabiao` varchar(10) DEFAULT NULL COMMENT '是否完成新增保底任务',
  `bonus_gears` tinyint(2) DEFAULT NULL COMMENT '新增完成业绩所属档级',
  `dabiao_price` decimal(8,2) DEFAULT NULL COMMENT '达标新增奖',
  `cp_shipments_price` decimal(8,2) DEFAULT NULL COMMENT '成品发货总金额',
  `lzxy_shipments_price` decimal(8,2) DEFAULT NULL COMMENT '裸钻星耀发货总金额',
  `lzfxy_shipments_price` decimal(8,2) DEFAULT NULL COMMENT '裸钻非星耀发货总金额',
  `tejia_shipments_price` decimal(8,2) DEFAULT NULL COMMENT '特价商品发货总金额',
  `shipments_total_price` decimal(8,2) DEFAULT NULL COMMENT '发货总金额',
  `cp_jiti_price` decimal(8,2) DEFAULT NULL COMMENT '成品发货计提总金额',
  `lzxy_jiti_price` decimal(8,2) DEFAULT NULL COMMENT '裸钻星耀发货计提总金额',
  `lzfxy_jiti_price` decimal(8,2) DEFAULT NULL COMMENT '裸钻非星耀发货计提总金额',
  `tejia_jiti_price` decimal(8,2) DEFAULT NULL COMMENT '特价商品发货计提总金额',
  `jiti_total_price` decimal(8,2) DEFAULT NULL COMMENT '发货计提总金额',
  `ticheng_factor` decimal(3,2) DEFAULT NULL COMMENT '档位提成系数',
  `ticheng_price` decimal(8,2) DEFAULT NULL COMMENT '提成',
  `tejia_ticheng_price` decimal(8,2) DEFAULT NULL COMMENT '特价商品提成',
  `tsyd_award_price` decimal(8,2) DEFAULT NULL COMMENT '天生一对奖励',
  `tsyd_punish_price` decimal(8,2) DEFAULT NULL COMMENT '天生一对惩罚',
  `real_should_price` decimal(8,2) DEFAULT NULL COMMENT '实际应发',
  `xy_award_price` decimal(8,2) DEFAULT NULL COMMENT '星耀奖励',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_color_from_ad
-- ----------------------------
DROP TABLE IF EXISTS `diamond_color_from_ad`;
CREATE TABLE `diamond_color_from_ad` (
  `id` tinyint(3) NOT NULL AUTO_INCREMENT,
  `from_ad` tinyint(3) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for diamond_jiajialv
-- ----------------------------
DROP TABLE IF EXISTS `diamond_jiajialv`;
CREATE TABLE `diamond_jiajialv` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `channel_id` int(10) unsigned NOT NULL COMMENT '销售渠道',
  `cert` varchar(10) DEFAULT NULL,
  `good_type` tinyint(1) unsigned NOT NULL COMMENT '货品类型1现货2期货',
  `carat_min` decimal(5,2) unsigned NOT NULL COMMENT '最小钻重',
  `carat_max` decimal(5,2) unsigned NOT NULL COMMENT '最大钻重',
  `jiajialv` decimal(4,3) DEFAULT '1.000' COMMENT '加价率',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态：1启用 0停用',
  `sid` varchar(60) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `channel` (`channel_id`),
  KEY `cert` (`cert`),
  KEY `carat_min` (`carat_min`),
  KEY `carat_max` (`carat_max`),
  KEY `sid` (`sid`)
) ENGINE=MyISAM AUTO_INCREMENT=2637 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_department_channel
-- ----------------------------
DROP TABLE IF EXISTS `ecs_department_channel`;
CREATE TABLE `ecs_department_channel` (
  `dc_id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `dep_name` varchar(20) NOT NULL,
  `dp_leader` int(10) unsigned NOT NULL COMMENT '部门领导',
  `assistant` varchar(255) NOT NULL COMMENT '部门助理',
  `system_type` int(3) NOT NULL COMMENT '权限类型0默认1报表2体验店',
  `is_sec` tinyint(3) NOT NULL,
  `parent_id` int(8) NOT NULL,
  `zhekou` decimal(10,2) NOT NULL DEFAULT '0.80' COMMENT '部门折扣底线',
  `is_huifang` tinyint(3) NOT NULL,
  `toubu_is_huifang` tinyint(3) NOT NULL,
  `sort` int(3) NOT NULL DEFAULT '255' COMMENT '排序',
  `kuaidi_sort` int(3) NOT NULL DEFAULT '255' COMMENT '快递排序',
  `is_dealer` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=经销商，0=默认',
  PRIMARY KEY (`dc_id`),
  KEY `is_sec` (`is_sec`)
) ENGINE=MyISAM AUTO_INCREMENT=154 DEFAULT CHARSET=gbk;

-- ----------------------------
-- Table structure for egl_display
-- ----------------------------
DROP TABLE IF EXISTS `egl_display`;
CREATE TABLE `egl_display` (
  `order_sn` varchar(255) NOT NULL COMMENT 'EGL订单号',
  PRIMARY KEY (`order_sn`),
  UNIQUE KEY `order_sn` (`order_sn`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for extra_category_ratio
-- ----------------------------
DROP TABLE IF EXISTS `extra_category_ratio`;
CREATE TABLE `extra_category_ratio` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `dep_id` int(6) DEFAULT NULL COMMENT '体验店ID',
  `dep_name` varchar(100) DEFAULT NULL COMMENT '体验店名称',
  `goods_type` varchar(20) DEFAULT NULL COMMENT '商品类型',
  `discount` decimal(6,2) DEFAULT NULL COMMENT '折扣',
  `pull_ratio_a` decimal(6,2) DEFAULT NULL COMMENT '提成比例A（裸钻小于0.5克拉）',
  `pull_ratio_b` decimal(6,2) DEFAULT NULL COMMENT '提成比例B（裸钻0.5克拉（含0.5克拉）-1克拉）',
  `pull_ratio_c` decimal(6,2) DEFAULT NULL COMMENT '提成比例C（裸钻1克拉（含1克拉）-1.5克拉）',
  `pull_ratio_d` decimal(6,2) DEFAULT NULL COMMENT '提成比例D（裸钻1.5克拉及以上）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=416 DEFAULT CHARSET=utf8 COMMENT='品类折扣提成比例';

-- ----------------------------
-- Table structure for extra_discount_scope
-- ----------------------------
DROP TABLE IF EXISTS `extra_discount_scope`;
CREATE TABLE `extra_discount_scope` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `dep_id` int(6) DEFAULT NULL COMMENT '体验店id',
  `dep_name` varchar(100) DEFAULT NULL COMMENT '体验店名称',
  `style_channel_id` int(11) DEFAULT NULL COMMENT '款式渠道来源ID',
  `style_channel_name` varchar(100) DEFAULT NULL COMMENT '款式渠道来源名称',
  `goods_type` varchar(20) DEFAULT NULL COMMENT '商品类型',
  `discount_upper` decimal(3,2) DEFAULT NULL COMMENT '折扣上限',
  `discount_floor` decimal(3,2) DEFAULT NULL COMMENT '折扣下限',
  `push_money` decimal(8,2) DEFAULT NULL COMMENT '提成',
  `priority` int(6) DEFAULT NULL COMMENT '优先级',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='特价商品规则';

-- ----------------------------
-- Table structure for extra_gemx_award
-- ----------------------------
DROP TABLE IF EXISTS `extra_gemx_award`;
CREATE TABLE `extra_gemx_award` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `gemx_max` decimal(6,2) DEFAULT NULL COMMENT '最小值（单位：分）',
  `gemx_min` decimal(6,2) DEFAULT NULL COMMENT '最大者（单位：分）',
  `award` decimal(8,2) DEFAULT NULL COMMENT '奖励',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='星耀奖励';

-- ----------------------------
-- Table structure for extra_mintsyd_mission
-- ----------------------------
DROP TABLE IF EXISTS `extra_mintsyd_mission`;
CREATE TABLE `extra_mintsyd_mission` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `dep_id` int(6) DEFAULT NULL COMMENT '销售渠道ID',
  `dep_name` varchar(100) DEFAULT NULL COMMENT '体验店渠道名称',
  `sale_name` varchar(20) DEFAULT NULL COMMENT '销售顾问',
  `minimum_price` decimal(8,2) DEFAULT NULL COMMENT '保底金额（单位：万元）',
  `tsyd_mission` int(6) DEFAULT NULL COMMENT '天生一对任务（单位：个）',
  `task_date` varchar(20) DEFAULT NULL COMMENT '任务时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=239 DEFAULT CHARSET=utf8 COMMENT='保底任务和天生一对任务';

-- ----------------------------
-- Table structure for extra_push_coefficient
-- ----------------------------
DROP TABLE IF EXISTS `extra_push_coefficient`;
CREATE TABLE `extra_push_coefficient` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dep_id` int(6) DEFAULT NULL COMMENT '体验店ID',
  `dep_name` varchar(100) DEFAULT NULL COMMENT '体验店名称',
  `station` varchar(20) DEFAULT NULL COMMENT '岗位',
  `bonus_gears` varchar(20) DEFAULT NULL COMMENT '差额奖金档位',
  `add_performance_standard` decimal(8,2) DEFAULT NULL COMMENT '新增业绩标准（万）',
  `excess_price` decimal(8,2) DEFAULT NULL COMMENT '超额奖金（元）',
  `push_money_coefficient` decimal(6,2) DEFAULT NULL COMMENT '档位提成系数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=321 DEFAULT CHARSET=utf8 COMMENT='岗位档位提成系数';

-- ----------------------------
-- Table structure for gift_goods
-- ----------------------------
DROP TABLE IF EXISTS `gift_goods`;
CREATE TABLE `gift_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL COMMENT '名称',
  `num` int(11) NOT NULL COMMENT '数量',
  `min_num` int(10) NOT NULL DEFAULT '0' COMMENT '最低数量',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '均价',
  `sell_sprice` decimal(10,2) NOT NULL COMMENT '售价',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态-1删除1正常',
  `goods_number` varchar(30) DEFAULT NULL COMMENT '赠品货号',
  `sell_type` int(4) NOT NULL DEFAULT '1' COMMENT '店面销售 1=开启  2=关闭',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_randring` tinyint(3) NOT NULL DEFAULT '2' COMMENT '是否活圈 1是2否',
  `sale_way` char(2) NOT NULL DEFAULT '1' COMMENT '可销售渠道. 1线上，2线下',
  `is_xz` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否销账,2.是.1否',
  `is_zp` char(2) NOT NULL DEFAULT '1' COMMENT '是否显示在外部订单1.是.0.否',
  PRIMARY KEY (`id`),
  KEY `goods_number` (`goods_number`)
) ENGINE=MyISAM AUTO_INCREMENT=452 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gift_goods_copy
-- ----------------------------
DROP TABLE IF EXISTS `gift_goods_copy`;
CREATE TABLE `gift_goods_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL COMMENT '名称',
  `num` int(11) NOT NULL COMMENT '数量',
  `min_num` int(10) NOT NULL DEFAULT '0' COMMENT '最低数量',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '均价',
  `sell_sprice` decimal(10,2) NOT NULL COMMENT '售价',
  `status` tinyint(3) NOT NULL DEFAULT '1' COMMENT '状态-1删除1正常',
  `goods_number` varchar(30) DEFAULT NULL COMMENT '赠品货号',
  `sell_type` int(4) NOT NULL DEFAULT '1' COMMENT '店面销售 1=开启  2=关闭',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_randring` tinyint(3) NOT NULL DEFAULT '2' COMMENT '是否活圈 1是2否',
  `sale_way` char(2) NOT NULL DEFAULT '1' COMMENT '可销售渠道. 1线上，2线下',
  `is_xz` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否销账,2.是.1否',
  `is_zp` char(2) NOT NULL DEFAULT '1' COMMENT '是否显示在外部订单1.是.0.否',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=369 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gift_goods_log
-- ----------------------------
DROP TABLE IF EXISTS `gift_goods_log`;
CREATE TABLE `gift_goods_log` (
  `id` mediumint(50) NOT NULL AUTO_INCREMENT COMMENT '日志id',
  `content` varchar(100) NOT NULL COMMENT '内容',
  `zp_sn` varchar(50) NOT NULL COMMENT '赠品id',
  `create_name` varchar(50) NOT NULL COMMENT '操作人',
  `create_time` datetime NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for huangou_goods
-- ----------------------------
DROP TABLE IF EXISTS `huangou_goods`;
CREATE TABLE `huangou_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `style_sn` varchar(30) NOT NULL COMMENT '款号',
  `goods_name` varchar(60) DEFAULT NULL,
  `channel_id` int(8) NOT NULL DEFAULT '0' COMMENT '渠道ID',
  `label_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '标签价',
  `sale_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '换购价',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1启用/2禁用',
  `create_user` varchar(30) NOT NULL,
  `create_time` datetime(6) NOT NULL,
  `update_user` varchar(30) DEFAULT NULL,
  `update_time` datetime(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for important_order_stats
-- ----------------------------
DROP TABLE IF EXISTS `important_order_stats`;
CREATE TABLE `important_order_stats` (
  `order_id` int(10) NOT NULL,
  `order_sn` varchar(255) NOT NULL,
  `goods_price` decimal(10,2) DEFAULT NULL,
  `goods_amount` decimal(10,2) DEFAULT NULL,
  `goods_favor` decimal(10,2) DEFAULT NULL,
  `favorable_price` decimal(10,2) DEFAULT NULL,
  `deposit` decimal(10,2) DEFAULT NULL,
  `money_paid` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for jitx_app_order_details
-- ----------------------------
DROP TABLE IF EXISTS `jitx_app_order_details`;
CREATE TABLE `jitx_app_order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单号',
  `goods_id` varchar(30) NOT NULL DEFAULT '' COMMENT '货号',
  `goods_sn` varchar(60) DEFAULT NULL,
  `ext_goods_sn` varchar(50) DEFAULT ' ' COMMENT '原始款号',
  `goods_name` varchar(200) NOT NULL COMMENT '商品名称',
  `goods_price` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `favorable_price` decimal(10,2) DEFAULT NULL COMMENT '优惠价格:正数代表减钱，负数代表加钱',
  `goods_count` int(8) NOT NULL COMMENT '商品个数',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `modify_time` datetime NOT NULL COMMENT '修改时间',
  `create_user` varchar(100) DEFAULT '' COMMENT '创建人',
  `details_status` tinyint(2) NOT NULL DEFAULT '1',
  `send_good_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1未到货2已发货3到货未检验4到货已检验5返厂',
  `buchan_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '布产状态:1初始化2待分配3已分配4生产中7部分出厂9已出厂10已取消',
  `is_stock_goods` tinyint(1) NOT NULL COMMENT '是否是现货：1现货 0期货',
  `is_return` tinyint(3) NOT NULL DEFAULT '0' COMMENT '退货产品 0未退货1已退货',
  `details_remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `cart` varchar(60) DEFAULT NULL COMMENT '石重',
  `cut` varchar(60) DEFAULT NULL COMMENT '切工',
  `clarity` varchar(60) DEFAULT NULL COMMENT '净度',
  `color` varchar(60) DEFAULT NULL COMMENT '颜色',
  `cert` varchar(10) DEFAULT NULL COMMENT '证书类型',
  `zhengshuhao` varchar(60) DEFAULT NULL COMMENT '证书号',
  `caizhi` varchar(60) DEFAULT NULL COMMENT '材质',
  `jinse` varchar(60) DEFAULT NULL COMMENT '金色',
  `jinzhong` varchar(60) DEFAULT NULL COMMENT '金重',
  `zhiquan` varchar(60) DEFAULT NULL COMMENT '指圈',
  `kezi` varchar(60) DEFAULT NULL COMMENT '刻字',
  `face_work` varchar(60) DEFAULT NULL COMMENT '表面工艺',
  `xiangqian` varchar(60) DEFAULT NULL COMMENT '镶嵌要求',
  `goods_type` varchar(20) DEFAULT NULL COMMENT '商品类型lz:裸钻',
  `favorable_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '优惠审核状态；1：保存；2：提交申请；3：审核通过；4：审核驳回',
  `cat_type` smallint(4) DEFAULT NULL COMMENT '款式分类',
  `product_type` smallint(4) DEFAULT NULL COMMENT '产品线',
  `kuan_sn` varchar(30) DEFAULT NULL COMMENT '天生一对的款号',
  `xiangkou` varchar(50) DEFAULT NULL COMMENT '镶口',
  `chengbenjia` decimal(10,2) DEFAULT '0.00' COMMENT '成本价',
  `bc_id` int(10) DEFAULT '0',
  `policy_id` int(10) DEFAULT NULL COMMENT '销售政策商品',
  `is_peishi` tinyint(1) unsigned DEFAULT '0' COMMENT '是否支持4C配钻，0不支持，1裸钻支持，2空托支持',
  `is_zp` char(2) NOT NULL DEFAULT '0' COMMENT '是否赠品1.是0.否',
  `is_finance` tinyint(2) NOT NULL DEFAULT '2' COMMENT '是否销账,2.是.1否',
  `weixiu_status` tinyint(2) DEFAULT NULL,
  `allow_favorable` tinyint(2) DEFAULT '1' COMMENT '是否允许申请优惠',
  `qiban_type` tinyint(1) unsigned DEFAULT '2' COMMENT '起版类型：见数据字典qiban_type',
  `delivery_status` tinyint(1) DEFAULT '1' COMMENT '1：未配货；2允许配货；5已配货',
  `retail_price` decimal(16,2) DEFAULT '0.00' COMMENT '原始零售价',
  `ds_xiangci` varchar(60) DEFAULT NULL COMMENT '单身-项次（鼎捷）',
  `pinhao` varchar(60) DEFAULT NULL COMMENT '品号（鼎捷）',
  `dia_type` tinyint(1) DEFAULT '0' COMMENT '钻石类型（1、现货钻，2、期货钻）',
  `is_cpdz` tinyint(1) DEFAULT '0',
  `tuo_type` varchar(10) DEFAULT NULL,
  `zhushi_num` smallint(5) unsigned DEFAULT NULL COMMENT '主石粒数',
  `cpdzcode` varchar(11) DEFAULT NULL,
  `discount_point` decimal(10,2) DEFAULT '0.00' COMMENT '折扣积分',
  `reward_point` decimal(10,2) DEFAULT '0.00' COMMENT '奖励积分',
  `daijinquan_code` varchar(30) DEFAULT NULL COMMENT '代金券兑换码',
  `daijinquan_price` decimal(10,2) DEFAULT '0.00' COMMENT '代金券优惠金额',
  `daijinquan_addtime` datetime DEFAULT NULL COMMENT '代金券兑换码使用时间',
  `jifenma_code` varchar(30) DEFAULT NULL COMMENT '积分码',
  `jifenma_point` decimal(8,2) DEFAULT '0.00' COMMENT '积分码赠送积分',
  `zhuandan_cash` decimal(10,2) DEFAULT '0.00',
  `goods_from` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for main_site
-- ----------------------------
DROP TABLE IF EXISTS `main_site`;
CREATE TABLE `main_site` (
  `order_sn` varchar(255) DEFAULT NULL,
  `cs` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `tel` varchar(255) DEFAULT NULL,
  KEY `order_sn` (`order_sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for member20170405
-- ----------------------------
DROP TABLE IF EXISTS `member20170405`;
CREATE TABLE `member20170405` (
  `id` int(11) unsigned NOT NULL DEFAULT '0',
  `cell_phone` char(11) DEFAULT NULL COMMENT '客户电话',
  `member_sn` varchar(20) DEFAULT NULL COMMENT '会员编号',
  KEY `cell_phone` (`cell_phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for member2017040502
-- ----------------------------
DROP TABLE IF EXISTS `member2017040502`;
CREATE TABLE `member2017040502` (
  `id` int(11) unsigned NOT NULL DEFAULT '0',
  `cell_phone` char(11) DEFAULT NULL COMMENT '客户电话',
  `member_sn` varchar(20) DEFAULT NULL COMMENT '会员编号',
  `department_id` varchar(20) DEFAULT NULL COMMENT '订单部门',
  `customer_source_id` int(8) NOT NULL COMMENT '客户来源',
  `order_sn` varchar(20) NOT NULL COMMENT '订单编号',
  `source_code` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '来源编码'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for order_deliver_fix
-- ----------------------------
DROP TABLE IF EXISTS `order_deliver_fix`;
CREATE TABLE `order_deliver_fix` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `delivery_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '[参考数字字典：配送状态(sales.delivery_status)]'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for order_goods_type_bak
-- ----------------------------
DROP TABLE IF EXISTS `order_goods_type_bak`;
CREATE TABLE `order_goods_type_bak` (
  `order_sn` varchar(20) COMMENT '订单编号',
  `id` int(11) NOT NULL DEFAULT '0' COMMENT '主键',
  `goods_id` bigint(30) DEFAULT '0' COMMENT '货号',
  `cat_type1` varchar(50) DEFAULT NULL COMMENT '新款式分类',
  `product_type1` varchar(50) DEFAULT NULL COMMENT '新产品线',
  `cat_type` smallint(4) DEFAULT NULL COMMENT '款式分类',
  `product_type` smallint(4) DEFAULT NULL COMMENT '产品线',
  `goods_type` varchar(20) DEFAULT NULL COMMENT '商品类型lz:裸钻',
  `real_goods_type` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for purchase_order_info
-- ----------------------------
DROP TABLE IF EXISTS `purchase_order_info`;
CREATE TABLE `purchase_order_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `purchase_id` int(11) DEFAULT NULL COMMENT '采购单明细ID',
  `detail_id` int(11) DEFAULT NULL COMMENT '订单明细ID',
  `order_sn` varchar(25) DEFAULT NULL COMMENT '订单号',
  `dep_name` varchar(50) DEFAULT NULL COMMENT '销售渠道',
  `bd_goods_id` bigint(30) DEFAULT NULL COMMENT '绑定货号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for rel
-- ----------------------------
DROP TABLE IF EXISTS `rel`;
CREATE TABLE `rel` (
  `sn` varchar(20) NOT NULL,
  `bjsn` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for rel_gift_order
-- ----------------------------
DROP TABLE IF EXISTS `rel_gift_order`;
CREATE TABLE `rel_gift_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` int(11) NOT NULL,
  `gift_id` varchar(255) DEFAULT NULL,
  `gift_num` varchar(50) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for rel_out_order
-- ----------------------------
DROP TABLE IF EXISTS `rel_out_order`;
CREATE TABLE `rel_out_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `out_order_sn` varchar(50) NOT NULL COMMENT '外部订单编号',
  `goods_detail_id` int(11) DEFAULT NULL COMMENT '商品明细id',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `order_id_2` (`order_id`),
  KEY `out_order_sn` (`out_order_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=57128 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for rel_out_order_finance
-- ----------------------------
DROP TABLE IF EXISTS `rel_out_order_finance`;
CREATE TABLE `rel_out_order_finance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `out_order_sn` varchar(50) NOT NULL COMMENT '外部订单编号',
  `goods_detail_id` int(11) DEFAULT NULL COMMENT '商品明细id',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for s11_order_info
-- ----------------------------
DROP TABLE IF EXISTS `s11_order_info`;
CREATE TABLE `s11_order_info` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `out_order_sn` varchar(60) DEFAULT NULL,
  `order_id` int(10) DEFAULT NULL,
  `order_sn` varchar(48) DEFAULT NULL,
  `res` tinyint(1) DEFAULT NULL,
  `reason` varchar(765) DEFAULT NULL,
  `ispreorder` int(2) DEFAULT '0',
  `isreturn` int(2) DEFAULT '0' COMMENT '是否已经回写备注到淘宝',
  `order_status` varchar(375) DEFAULT NULL,
  `add_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13440 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for sale_quota
-- ----------------------------
DROP TABLE IF EXISTS `sale_quota`;
CREATE TABLE `sale_quota` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `quota_name` varchar(60) DEFAULT NULL COMMENT '指标名称',
  `cate_type1` varchar(30) DEFAULT NULL COMMENT '指标分类：渠道一部 渠道二部',
  `cate_type2` varchar(30) DEFAULT NULL COMMENT '指标明细分类：天猫 京东 自营',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2801 DEFAULT CHARSET=utf8 COMMENT='网销日经指标';

-- ----------------------------
-- Table structure for sale_quota_item
-- ----------------------------
DROP TABLE IF EXISTS `sale_quota_item`;
CREATE TABLE `sale_quota_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `item_id` int(10) unsigned NOT NULL COMMENT '指标ID',
  `pdate` date NOT NULL COMMENT '指标日期',
  `plan_value` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '指标计划值',
  `actual_value` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '指标实际值',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `modify_time` datetime NOT NULL COMMENT '修改时间',
  `create_user` varchar(10) DEFAULT '' COMMENT '创建人',
  `update_user` varchar(10) DEFAULT '' COMMENT '编辑人',
  `date_number` int(10) NOT NULL DEFAULT '0' COMMENT '日期编码',
  PRIMARY KEY (`id`),
  UNIQUE KEY `item_id` (`item_id`,`pdate`)
) ENGINE=InnoDB AUTO_INCREMENT=44158 DEFAULT CHARSET=utf8 COMMENT='网销日经营指标计划';

-- ----------------------------
-- Table structure for sale_refund_item
-- ----------------------------
DROP TABLE IF EXISTS `sale_refund_item`;
CREATE TABLE `sale_refund_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `sup_id` int(10) unsigned NOT NULL COMMENT '指标id',
  `grade` int(10) NOT NULL COMMENT '排序等级',
  `value` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '指标计划值',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `modify_time` datetime NOT NULL COMMENT '修改时间',
  `create_user` varchar(20) DEFAULT '' COMMENT '创建人',
  `update_user` varchar(20) DEFAULT '' COMMENT '编辑人',
  `date_number` int(10) NOT NULL DEFAULT '0' COMMENT '日期编码',
  `pdate` date DEFAULT NULL COMMENT '指标日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='销售退款报表';

-- ----------------------------
-- Table structure for tmp_fin_order_info
-- ----------------------------
DROP TABLE IF EXISTS `tmp_fin_order_info`;
CREATE TABLE `tmp_fin_order_info` (
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `order_sn` varchar(20) NOT NULL DEFAULT '',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `CooMainId` int(8) NOT NULL,
  `bespoke_id` int(11) NOT NULL DEFAULT '0',
  `department` int(4) NOT NULL DEFAULT '0',
  `sec_department` varchar(20) NOT NULL DEFAULT '',
  `is_special` int(1) NOT NULL,
  `is_hidden` tinyint(3) NOT NULL,
  `warehouse` varchar(60) NOT NULL DEFAULT 'SZ',
  `order_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `order_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `apply_close` tinyint(3) NOT NULL COMMENT '申请关闭',
  `shipping_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `buchan_status` int(1) NOT NULL DEFAULT '0' COMMENT '布产状态:0=现货        8=定制未操作, 1定制已布产,2定制已出厂,（非0状态均为定制）',
  `order_factory_status` tinyint(3) NOT NULL,
  `consignee` varchar(60) NOT NULL DEFAULT '',
  `country` smallint(5) unsigned NOT NULL DEFAULT '0',
  `province` smallint(5) unsigned NOT NULL DEFAULT '0',
  `city` smallint(5) unsigned NOT NULL DEFAULT '0',
  `district` smallint(5) unsigned NOT NULL DEFAULT '0',
  `address` varchar(255) NOT NULL DEFAULT '',
  `zipcode` varchar(60) NOT NULL DEFAULT '',
  `consignee_msg` varchar(255) NOT NULL COMMENT '留言',
  `gift` varchar(255) NOT NULL,
  `tel` varchar(60) NOT NULL DEFAULT '',
  `mobile` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `best_time` varchar(120) NOT NULL DEFAULT '',
  `sign_building` varchar(120) NOT NULL DEFAULT '',
  `postscript` varchar(255) NOT NULL DEFAULT '',
  `shipping_id` tinyint(3) NOT NULL DEFAULT '0',
  `shipping_name` varchar(120) NOT NULL DEFAULT '',
  `shipping_target` varchar(128) NOT NULL COMMENT '配送目标（展厅、个人）',
  `second_ship` varchar(255) NOT NULL COMMENT '补寄信息',
  `pay_id` int(5) NOT NULL DEFAULT '0',
  `pay_name` varchar(120) NOT NULL DEFAULT '',
  `how_oos` varchar(120) NOT NULL DEFAULT '',
  `how_surplus` varchar(120) NOT NULL DEFAULT '',
  `pack_name` varchar(120) NOT NULL DEFAULT '',
  `card_name` varchar(120) NOT NULL DEFAULT '',
  `card_message` varchar(255) NOT NULL DEFAULT '',
  `need_inv` tinyint(3) NOT NULL DEFAULT '0',
  `inv_payee` varchar(120) NOT NULL DEFAULT '',
  `inv_content` varchar(120) NOT NULL DEFAULT '',
  `inv_post_address` varchar(255) NOT NULL DEFAULT '' COMMENT '发票邮寄地址',
  `goods_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `insure_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pay_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pack_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `card_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `order_attach_price` decimal(8,2) NOT NULL COMMENT '订单挂件金额',
  `order_goods_attach_price` decimal(8,2) NOT NULL COMMENT '订单商品挂件金额',
  `goods_return_price` decimal(10,2) NOT NULL COMMENT '产品退款挂件',
  `real_return_price` decimal(10,2) NOT NULL COMMENT '实退金额',
  `money_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `surplus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `integral` int(10) unsigned NOT NULL DEFAULT '0',
  `integral_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bonus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `from_ad` varchar(20) NOT NULL DEFAULT '000000260000',
  `referer` varchar(255) NOT NULL DEFAULT '',
  `confirm_time` int(10) unsigned NOT NULL DEFAULT '0',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0',
  `first_pay_time` datetime NOT NULL COMMENT '第一次付款时间',
  `last_goods_out` datetime NOT NULL COMMENT '第一次打印提货单时间',
  `first_thd_time` datetime NOT NULL COMMENT '第一次打印提货单时间',
  `shipping_time` int(10) unsigned NOT NULL DEFAULT '0',
  `receive_time` int(10) NOT NULL DEFAULT '0',
  `pack_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `card_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bonus_id` int(8) unsigned NOT NULL DEFAULT '0',
  `invoice_no` varchar(50) NOT NULL DEFAULT '',
  `extension_code` varchar(30) NOT NULL DEFAULT '',
  `extension_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `to_buyer` varchar(255) NOT NULL DEFAULT '',
  `pay_note` varchar(255) NOT NULL DEFAULT '',
  `kela_coder` varchar(20) NOT NULL,
  `make_order` varchar(60) NOT NULL DEFAULT '0',
  `make_order_proportion` decimal(10,4) NOT NULL DEFAULT '0.5000' COMMENT '制单人分成比例',
  `user_belong` varchar(20) NOT NULL COMMENT '用户所属人',
  `user_belong_proportion` decimal(10,4) NOT NULL DEFAULT '0.5000' COMMENT '用户所属人比例',
  `make_user` mediumint(8) NOT NULL DEFAULT '0',
  `is_change` tinyint(3) NOT NULL DEFAULT '0',
  `last_done_time` int(10) NOT NULL DEFAULT '0',
  `taobao_order_id` varchar(800) NOT NULL,
  `is_zp` tinyint(3) NOT NULL COMMENT '是否含有赠品0=不含1=含有',
  `print_thd` tinyint(3) NOT NULL DEFAULT '0' COMMENT '打印提货单',
  `print_shqd` tinyint(3) NOT NULL DEFAULT '0' COMMENT '打印随货清单',
  `effect_date` datetime NOT NULL COMMENT '订单生效时间(确定布产)',
  `order_into_status` tinyint(3) NOT NULL COMMENT '提报状态,0未提报,1已提报',
  `into_status` int(3) NOT NULL COMMENT '订单提报审核,0未提报,1已提报,2提报未通过',
  `genzong` varchar(20) NOT NULL COMMENT '跟踪',
  `is_comment` tinyint(3) NOT NULL COMMENT '是否评论',
  `union_status` tinyint(2) NOT NULL DEFAULT '0',
  `source` varchar(128) NOT NULL,
  `tel_status` tinyint(3) NOT NULL COMMENT '电话状态,0未打电话,1电话通知',
  `order_type` int(1) NOT NULL DEFAULT '0' COMMENT '0=零售，1=备货,2=备货回款，3=团购',
  `qq_yongjin` int(11) NOT NULL COMMENT 'qq推单佣金',
  `dianmian` varchar(100) NOT NULL COMMENT '店面销售',
  `tuijianren` varchar(32) NOT NULL COMMENT '设计图推荐人',
  `shejishi` varchar(32) NOT NULL COMMENT '设计师',
  `chengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '销货成本',
  `recommended` varchar(50) NOT NULL COMMENT '异业合作推荐人',
  `inv_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '发票金额',
  `jxc_sale` int(4) NOT NULL DEFAULT '0' COMMENT '进销存自动销货状态0-未销，1-已销',
  `cps_source` varchar(150) NOT NULL COMMENT '对接参数-cps用',
  `is_caibei` int(4) NOT NULL DEFAULT '0' COMMENT '是否推单',
  `peihuo_status` int(1) NOT NULL DEFAULT '0' COMMENT '配货状态',
  `kehu_birthday` varchar(10) NOT NULL COMMENT '淘宝订单记录客户生日',
  `peihuo_time` datetime NOT NULL COMMENT '配货时间',
  `xiaozhang_time` datetime NOT NULL COMMENT '销帐时间',
  `fqc_time` datetime NOT NULL COMMENT 'fqc时间',
  `is_rax` int(1) NOT NULL DEFAULT '0' COMMENT 'rax活动；是否抽奖0为否；1为是；',
  `list_price` decimal(10,2) NOT NULL COMMENT '原始价格',
  `is_update` int(1) NOT NULL COMMENT 'list_price是否插入',
  `is_config` tinyint(3) NOT NULL DEFAULT '0' COMMENT '售价报警系统 1是通过 0是待处理',
  `fast_sc` tinyint(3) NOT NULL COMMENT '快速定制生产',
  `distribution_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '配送类型1:配送到体验店2：总公司发客户3：配送到经销商事业部'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tmp_jxs_qixi
-- ----------------------------
DROP TABLE IF EXISTS `tmp_jxs_qixi`;
CREATE TABLE `tmp_jxs_qixi` (
  `id` varchar(255) DEFAULT NULL,
  `日期` varchar(255) DEFAULT NULL,
  `参与者姓名` varchar(255) DEFAULT NULL,
  `参与者手机` varchar(255) DEFAULT NULL,
  `参与者地址` varchar(255) DEFAULT NULL,
  `gid` varchar(255) DEFAULT NULL,
  `被邀请参与日期` varchar(255) DEFAULT NULL,
  `被邀请参与者姓名` varchar(255) DEFAULT NULL,
  `被邀请参与者手机` varchar(255) DEFAULT NULL,
  `被邀请参与者地址` varchar(255) DEFAULT NULL,
  `pcode` varchar(255) DEFAULT NULL,
  `icode` varchar(255) DEFAULT NULL,
  `vcode` varchar(255) DEFAULT NULL,
  `类型` varchar(255) DEFAULT NULL,
  `体验店` varchar(255) DEFAULT NULL,
  `是否存在` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tmp_order_action
-- ----------------------------
DROP TABLE IF EXISTS `tmp_order_action`;
CREATE TABLE `tmp_order_action` (
  `action_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `buchan_sn` int(10) NOT NULL COMMENT '布产号',
  `action_user` varchar(30) NOT NULL DEFAULT '',
  `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shipping_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `action_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `action_note` varchar(255) NOT NULL DEFAULT '',
  `action_type` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tmp_order_channel
-- ----------------------------
DROP TABLE IF EXISTS `tmp_order_channel`;
CREATE TABLE `tmp_order_channel` (
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `order_sn` varchar(20) NOT NULL DEFAULT '',
  `department` int(4) NOT NULL DEFAULT '0',
  `dc_id` int(8) unsigned NOT NULL DEFAULT '0',
  `dep_name` varchar(20) CHARACTER SET gbk NOT NULL,
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '序号',
  `channel_name` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '渠道名称',
  KEY `id` (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tmp_order_goods
-- ----------------------------
DROP TABLE IF EXISTS `tmp_order_goods`;
CREATE TABLE `tmp_order_goods` (
  `rec_id` bigint(30) unsigned NOT NULL DEFAULT '0',
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` varchar(30) NOT NULL DEFAULT '0',
  `goods_name` varchar(120) NOT NULL DEFAULT '',
  `goods_sn` varchar(60) NOT NULL DEFAULT '',
  `ori_goods_sn` varchar(60) NOT NULL COMMENT '原产品编号',
  `ext_goods_sn` varchar(60) NOT NULL COMMENT '产品外部编号',
  `goods_number` bigint(10) unsigned NOT NULL DEFAULT '1',
  `market_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `rui_price` varchar(60) NOT NULL COMMENT '瑞金结算单价',
  `goods_attr` text NOT NULL,
  `send_number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `is_real` int(1) unsigned NOT NULL DEFAULT '0',
  `extension_code` varchar(30) NOT NULL DEFAULT '',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `is_gift` smallint(5) unsigned NOT NULL DEFAULT '0',
  `is_dia` tinyint(3) NOT NULL,
  `goods_size` varchar(10) NOT NULL,
  `brand` varchar(60) NOT NULL DEFAULT 'BDD',
  `saller` varchar(60) NOT NULL DEFAULT 'BDD',
  `stone` varchar(60) NOT NULL,
  `jietuoxiangkou` decimal(10,3) NOT NULL COMMENT '戒托镶口',
  `stone_color` varchar(20) NOT NULL,
  `stone_clear` varchar(20) NOT NULL,
  `factory` varchar(20) NOT NULL,
  `factory_num` varchar(20) NOT NULL,
  `factory_sn` varchar(60) NOT NULL,
  `gold` varchar(20) NOT NULL,
  `gold_weight` varchar(20) NOT NULL,
  `gold_color` varchar(20) NOT NULL,
  `face_work` varchar(20) NOT NULL,
  `certid` varchar(20) NOT NULL COMMENT '证书号',
  `fushi` varchar(2) NOT NULL COMMENT '是否含有副石',
  `chengpin` varchar(10) NOT NULL COMMENT '成品或半成品',
  `yanshi` varchar(50) NOT NULL COMMENT '验石需求',
  `finger` varchar(20) NOT NULL,
  `word` varchar(100) NOT NULL,
  `send_time` int(10) NOT NULL,
  `get_time` int(10) NOT NULL COMMENT '生产接单时间',
  `normal_time` int(10) NOT NULL COMMENT '标准出厂时间',
  `out_time` int(10) NOT NULL COMMENT '出场时间',
  `end_time` int(10) NOT NULL DEFAULT '0',
  `bind_time` int(10) NOT NULL DEFAULT '0' COMMENT '绑定进销存货号时间',
  `recv_time` int(10) NOT NULL DEFAULT '0' COMMENT '店内收货时间',
  `buchan_img` varchar(60) NOT NULL,
  `gdlist` int(8) NOT NULL DEFAULT '0',
  `goods_status` int(1) NOT NULL COMMENT '0=未操作,1=未跟单, 2=正在生产, 3=已出厂, 4=不需布产,5=维修中, 8=待审核',
  `remark` varchar(255) NOT NULL,
  `arrival_status` int(1) NOT NULL DEFAULT '0' COMMENT '到货状态',
  `into_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '分成类型',
  `into_status` int(1) NOT NULL COMMENT '审核类型0未提交1已提报2财务已审3审核未通过',
  `is_return` tinyint(3) NOT NULL COMMENT '退货产品',
  `union_property` tinyint(2) NOT NULL DEFAULT '0',
  `picking_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '配货类型,0按产品号,1按款式',
  `is_hidden` int(1) NOT NULL DEFAULT '0' COMMENT '跟单人员认为是问题订单',
  `bc_goods_type` int(2) NOT NULL DEFAULT '0',
  `goods_type` varchar(20) NOT NULL COMMENT '添加类型:cp成品、lz裸钻、zp赠品、xp新品、dz定制、js素金饰品、xg黄金千足金、ig投资金条、sp特殊类型、ob欧版戒指、tg团购、sj设计',
  `goods_data` varchar(10) NOT NULL COMMENT '产品分类类型',
  `cut` varchar(20) NOT NULL COMMENT '切工',
  `caigou_chengben` int(10) NOT NULL COMMENT '预计采购成本',
  `chengbenjia` decimal(10,2) NOT NULL COMMENT '采购成本',
  `is_qingsuan` tinyint(3) NOT NULL DEFAULT '0' COMMENT '瑞金清算0 未清算,1已清算',
  `buchan_group` int(2) NOT NULL DEFAULT '0' COMMENT '生产分组',
  `chuchang_num` int(10) NOT NULL DEFAULT '0' COMMENT '实际出厂数量',
  `waste_num` int(10) NOT NULL DEFAULT '0' COMMENT '报废数量',
  `dia_is_buy` int(1) NOT NULL DEFAULT '0' COMMENT '内部是否购买裸钻 默认未购买=0 购买=1',
  `gold_price` varchar(8) NOT NULL DEFAULT '' COMMENT '克单价',
  `gold_price_history` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `gold_jiejia` int(3) NOT NULL DEFAULT '0' COMMENT '瑞金是否结价1=未结价，2=已结价,0=旧数据',
  `is_comment` int(2) NOT NULL COMMENT '0-未评价 1-已评价',
  `abctype` tinyint(3) NOT NULL DEFAULT '2' COMMENT '0=A,1=B,2=C',
  `hidden_code` varchar(10) NOT NULL COMMENT 'AB货内码',
  `is_qihuozuan` tinyint(3) NOT NULL DEFAULT '1' COMMENT '是否期货钻；1为否；2为是；',
  `qiban_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '''0未操作1现货2定制3有款起版4无款起版',
  `g_update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tmp_order_info
-- ----------------------------
DROP TABLE IF EXISTS `tmp_order_info`;
CREATE TABLE `tmp_order_info` (
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `order_sn` varchar(20) NOT NULL DEFAULT '',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `CooMainId` int(8) NOT NULL,
  `bespoke_id` int(11) NOT NULL DEFAULT '0',
  `department` int(4) NOT NULL DEFAULT '0',
  `sec_department` varchar(20) NOT NULL DEFAULT '',
  `is_special` int(1) NOT NULL,
  `is_hidden` tinyint(3) NOT NULL,
  `warehouse` varchar(60) NOT NULL DEFAULT 'SZ',
  `order_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `order_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `apply_close` tinyint(3) NOT NULL COMMENT '申请关闭',
  `shipping_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `buchan_status` int(1) NOT NULL DEFAULT '0' COMMENT '布产状态:0=现货        8=定制未操作, 1定制已布产,2定制已出厂,（非0状态均为定制）',
  `order_factory_status` tinyint(3) NOT NULL,
  `consignee` varchar(60) NOT NULL DEFAULT '',
  `country` smallint(5) unsigned NOT NULL DEFAULT '0',
  `province` smallint(5) unsigned NOT NULL DEFAULT '0',
  `city` smallint(5) unsigned NOT NULL DEFAULT '0',
  `district` smallint(5) unsigned NOT NULL DEFAULT '0',
  `address` varchar(255) NOT NULL DEFAULT '',
  `zipcode` varchar(60) NOT NULL DEFAULT '',
  `consignee_msg` varchar(255) NOT NULL COMMENT '留言',
  `gift` varchar(255) NOT NULL,
  `tel` varchar(60) NOT NULL DEFAULT '',
  `mobile` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `best_time` varchar(120) NOT NULL DEFAULT '',
  `sign_building` varchar(120) NOT NULL DEFAULT '',
  `postscript` varchar(255) NOT NULL DEFAULT '',
  `shipping_id` tinyint(3) NOT NULL DEFAULT '0',
  `shipping_name` varchar(120) NOT NULL DEFAULT '',
  `shipping_target` varchar(128) NOT NULL COMMENT '配送目标（展厅、个人）',
  `second_ship` varchar(255) NOT NULL COMMENT '补寄信息',
  `pay_id` int(5) NOT NULL DEFAULT '0',
  `pay_name` varchar(120) NOT NULL DEFAULT '',
  `how_oos` varchar(120) NOT NULL DEFAULT '',
  `how_surplus` varchar(120) NOT NULL DEFAULT '',
  `pack_name` varchar(120) NOT NULL DEFAULT '',
  `card_name` varchar(120) NOT NULL DEFAULT '',
  `card_message` varchar(255) NOT NULL DEFAULT '',
  `need_inv` tinyint(3) NOT NULL DEFAULT '0',
  `inv_payee` varchar(120) NOT NULL DEFAULT '',
  `inv_content` varchar(120) NOT NULL DEFAULT '',
  `inv_post_address` varchar(255) NOT NULL DEFAULT '' COMMENT '发票邮寄地址',
  `goods_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `insure_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pay_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pack_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `card_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `order_attach_price` decimal(8,2) NOT NULL COMMENT '订单挂件金额',
  `order_goods_attach_price` decimal(8,2) NOT NULL COMMENT '订单商品挂件金额',
  `goods_return_price` decimal(10,2) NOT NULL COMMENT '产品退款挂件',
  `real_return_price` decimal(10,2) NOT NULL COMMENT '实退金额',
  `money_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `surplus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `integral` int(10) unsigned NOT NULL DEFAULT '0',
  `integral_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bonus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `from_ad` varchar(20) NOT NULL DEFAULT '000000260000',
  `referer` varchar(255) NOT NULL DEFAULT '',
  `confirm_time` int(10) unsigned NOT NULL DEFAULT '0',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0',
  `first_pay_time` datetime NOT NULL COMMENT '第一次付款时间',
  `last_goods_out` datetime NOT NULL COMMENT '第一次打印提货单时间',
  `first_thd_time` datetime NOT NULL COMMENT '第一次打印提货单时间',
  `shipping_time` int(10) unsigned NOT NULL DEFAULT '0',
  `receive_time` int(10) NOT NULL DEFAULT '0',
  `pack_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `card_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bonus_id` int(8) unsigned NOT NULL DEFAULT '0',
  `invoice_no` varchar(50) NOT NULL DEFAULT '',
  `extension_code` varchar(30) NOT NULL DEFAULT '',
  `extension_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `to_buyer` varchar(255) NOT NULL DEFAULT '',
  `pay_note` varchar(255) NOT NULL DEFAULT '',
  `kela_coder` varchar(20) NOT NULL,
  `make_order` varchar(60) NOT NULL DEFAULT '0',
  `make_order_proportion` decimal(10,4) NOT NULL DEFAULT '0.5000' COMMENT '制单人分成比例',
  `user_belong` varchar(20) NOT NULL COMMENT '用户所属人',
  `user_belong_proportion` decimal(10,4) NOT NULL DEFAULT '0.5000' COMMENT '用户所属人比例',
  `make_user` mediumint(8) NOT NULL DEFAULT '0',
  `is_change` tinyint(3) NOT NULL DEFAULT '0',
  `last_done_time` int(10) NOT NULL DEFAULT '0',
  `taobao_order_id` varchar(800) NOT NULL,
  `is_zp` tinyint(3) NOT NULL COMMENT '是否含有赠品0=不含1=含有',
  `print_thd` tinyint(3) NOT NULL DEFAULT '0' COMMENT '打印提货单',
  `print_shqd` tinyint(3) NOT NULL DEFAULT '0' COMMENT '打印随货清单',
  `effect_date` datetime NOT NULL COMMENT '订单生效时间(确定布产)',
  `order_into_status` tinyint(3) NOT NULL COMMENT '提报状态,0未提报,1已提报',
  `into_status` int(3) NOT NULL COMMENT '订单提报审核,0未提报,1已提报,2提报未通过',
  `genzong` varchar(20) NOT NULL COMMENT '跟踪',
  `is_comment` tinyint(3) NOT NULL COMMENT '是否评论',
  `union_status` tinyint(2) NOT NULL DEFAULT '0',
  `source` varchar(128) NOT NULL,
  `tel_status` tinyint(3) NOT NULL COMMENT '电话状态,0未打电话,1电话通知',
  `order_type` int(1) NOT NULL DEFAULT '0' COMMENT '0=零售，1=备货,2=备货回款，3=团购',
  `qq_yongjin` int(11) NOT NULL COMMENT 'qq推单佣金',
  `dianmian` varchar(100) NOT NULL COMMENT '店面销售',
  `tuijianren` varchar(32) NOT NULL COMMENT '设计图推荐人',
  `shejishi` varchar(32) NOT NULL COMMENT '设计师',
  `chengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '销货成本',
  `recommended` varchar(50) NOT NULL COMMENT '异业合作推荐人',
  `inv_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '发票金额',
  `jxc_sale` int(4) NOT NULL DEFAULT '0' COMMENT '进销存自动销货状态0-未销，1-已销',
  `cps_source` varchar(150) NOT NULL COMMENT '对接参数-cps用',
  `is_caibei` int(4) NOT NULL DEFAULT '0' COMMENT '是否推单',
  `peihuo_status` int(1) NOT NULL DEFAULT '0' COMMENT '配货状态',
  `kehu_birthday` varchar(10) NOT NULL COMMENT '淘宝订单记录客户生日',
  `peihuo_time` datetime NOT NULL COMMENT '配货时间',
  `xiaozhang_time` datetime NOT NULL COMMENT '销帐时间',
  `fqc_time` datetime NOT NULL COMMENT 'fqc时间',
  `is_rax` int(1) NOT NULL DEFAULT '0' COMMENT 'rax活动；是否抽奖0为否；1为是；',
  `list_price` decimal(10,2) NOT NULL COMMENT '原始价格',
  `is_update` int(1) NOT NULL COMMENT 'list_price是否插入',
  `is_config` tinyint(3) NOT NULL DEFAULT '0' COMMENT '售价报警系统 1是通过 0是待处理',
  `fast_sc` tinyint(3) NOT NULL COMMENT '快速定制生产',
  `distribution_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '配送类型1:配送到体验店2：总公司发客户3：配送到经销商事业部',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tmp_qh_xiankou
-- ----------------------------
DROP TABLE IF EXISTS `tmp_qh_xiankou`;
CREATE TABLE `tmp_qh_xiankou` (
  `goods_id` varchar(30) NOT NULL COMMENT '货号',
  `finger` varchar(30) DEFAULT NULL,
  `xiankou` varchar(63) DEFAULT NULL,
  UNIQUE KEY `goods_id` (`goods_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tt_app_return_check_bk0717
-- ----------------------------
DROP TABLE IF EXISTS `tt_app_return_check_bk0717`;
CREATE TABLE `tt_app_return_check_bk0717` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `return_id` int(10) unsigned NOT NULL COMMENT '退款单id',
  `leader_id` int(10) DEFAULT NULL COMMENT '部门主管',
  `leader_res` varchar(255) DEFAULT NULL COMMENT '业务负责人意见',
  `leader_status` tinyint(3) DEFAULT NULL COMMENT '主管审核状态',
  `leader_time` datetime DEFAULT NULL COMMENT '主管审核时间',
  `goods_comfirm_id` int(10) DEFAULT NULL COMMENT '库管ID',
  `goods_res` varchar(255) DEFAULT NULL COMMENT '库管部门意见',
  `goods_status` tinyint(3) DEFAULT '0' COMMENT '产品状态,0,未确认,1,审核通过,2审核驳回',
  `goods_time` datetime DEFAULT NULL COMMENT '产品状态操作时间',
  `cto_id` int(10) DEFAULT NULL COMMENT 'CTOID',
  `cto_res` varchar(255) DEFAULT NULL COMMENT 'CTO意见',
  `cto_status` tinyint(3) DEFAULT NULL COMMENT 'CTO状态：0为操作，1批准',
  `cto_time` datetime DEFAULT NULL COMMENT 'CTO操作时间',
  `deparment_finance_id` int(10) DEFAULT NULL COMMENT '部门财务id',
  `deparment_finance_status` tinyint(1) DEFAULT NULL COMMENT '部门财务审核状态0,未操作,1已审核',
  `deparment_finance_res` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '部门财务备注',
  `deparment_finance_time` datetime DEFAULT NULL COMMENT '部门财务操作时间',
  `finance_id` int(10) DEFAULT NULL COMMENT '财务操作人ID',
  `bak_fee` decimal(10,2) DEFAULT NULL COMMENT '支付手续费',
  `finance_res` varchar(255) DEFAULT NULL COMMENT '财务意见',
  `finance_status` tinyint(3) DEFAULT NULL COMMENT '财务状态,0未操作,1,已确认',
  `finance_time` datetime DEFAULT NULL COMMENT '财务操作时间',
  `pay_id` int(10) DEFAULT NULL COMMENT '实际付款人id',
  `pay_res` varchar(255) DEFAULT NULL COMMENT '付款备注',
  `pay_status` tinyint(1) DEFAULT NULL COMMENT '支付状态',
  `pay_attach` varchar(255) DEFAULT NULL COMMENT '付款附件',
  `old_goods_status` tinyint(3) DEFAULT '0' COMMENT '备份以前库管审核状态：0,未确认,1,留库存,2未出库'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tt_app_return_goods_bk0717
-- ----------------------------
DROP TABLE IF EXISTS `tt_app_return_goods_bk0717`;
CREATE TABLE `tt_app_return_goods_bk0717` (
  `return_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退款单id',
  `department` int(11) NOT NULL COMMENT '所属部门',
  `apply_user_id` int(10) NOT NULL COMMENT '申请人',
  `order_id` int(10) NOT NULL COMMENT '订单id',
  `order_sn` varchar(20) NOT NULL COMMENT '订单编号',
  `order_goods_id` int(10) NOT NULL,
  `should_return_amount` decimal(10,2) NOT NULL COMMENT '应退金额',
  `apply_return_amount` decimal(10,2) NOT NULL COMMENT '申请金额',
  `real_return_amount` decimal(10,2) NOT NULL COMMENT '实退金额',
  `confirm_price` decimal(10,2) DEFAULT NULL COMMENT '审核金额',
  `return_res` varchar(255) NOT NULL COMMENT '退款原因',
  `return_by` tinyint(1) unsigned DEFAULT NULL COMMENT '退款方式 1退商品，2不退商品',
  `return_type` tinyint(3) NOT NULL COMMENT '退款类型,1转单,2打卡,3现金',
  `return_card` varchar(60) NOT NULL COMMENT '退款账户',
  `consignee` varchar(60) NOT NULL COMMENT '退款人',
  `mobile` varchar(20) NOT NULL COMMENT '联系电话',
  `bank_name` varchar(60) NOT NULL COMMENT '开户银行',
  `apply_time` datetime NOT NULL COMMENT '申请时间',
  `pay_id` int(10) DEFAULT NULL COMMENT '实际付款人',
  `pay_res` varchar(255) DEFAULT NULL COMMENT '付款备注',
  `pay_status` tinyint(3) DEFAULT NULL COMMENT '支付状态',
  `pay_attach` varchar(255) DEFAULT NULL COMMENT '付款附件',
  `pay_order_sn` varchar(20) DEFAULT NULL COMMENT '支付的订单号',
  `jxc_order` varchar(30) DEFAULT NULL COMMENT '进销存退货单',
  `zhuandan_amount` decimal(10,2) DEFAULT NULL,
  `check_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未操作1主管审核通过2库管审核通过3事业部通过4现场财务通过5财务通过',
  `return_goods_id` varchar(255) DEFAULT NULL COMMENT '退货商品ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tt_order_sn_for_crm
-- ----------------------------
DROP TABLE IF EXISTS `tt_order_sn_for_crm`;
CREATE TABLE `tt_order_sn_for_crm` (
  `order_sn` varchar(20) NOT NULL COMMENT '订单编号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tt_order_sn_for_crm_1
-- ----------------------------
DROP TABLE IF EXISTS `tt_order_sn_for_crm_1`;
CREATE TABLE `tt_order_sn_for_crm_1` (
  `order_sn` varchar(20) NOT NULL COMMENT '订单编号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tt_order_sn_for_crm_2
-- ----------------------------
DROP TABLE IF EXISTS `tt_order_sn_for_crm_2`;
CREATE TABLE `tt_order_sn_for_crm_2` (
  `order_sn` varchar(20) NOT NULL COMMENT '订单编号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tt_warehouse_bill_goods0112
-- ----------------------------
DROP TABLE IF EXISTS `tt_warehouse_bill_goods0112`;
CREATE TABLE `tt_warehouse_bill_goods0112` (
  `id` int(11) unsigned NOT NULL DEFAULT '0',
  `pinhao` varchar(30) DEFAULT NULL COMMENT '品号（鼎捷字段）',
  `xiangci` varchar(30) DEFAULT NULL COMMENT '项次(鼎捷字段)',
  `p_sn_out` varchar(50) DEFAULT NULL COMMENT '外部订单号（鼎捷）',
  `bill_id` int(10) NOT NULL DEFAULT '0' COMMENT '单据id',
  `bill_no` char(17) NOT NULL COMMENT '单据编号',
  `bill_type` varchar(4) NOT NULL DEFAULT '1' COMMENT '单据类型',
  `goods_id` bigint(30) NOT NULL COMMENT '货号',
  `goods_sn` varchar(30) NOT NULL COMMENT '款号',
  `goods_name` varchar(150) NOT NULL COMMENT '商品名称',
  `num` int(6) NOT NULL DEFAULT '0' COMMENT '数量',
  `warehouse_id` int(10) unsigned DEFAULT NULL COMMENT '所属仓库ID(如果是盘点单的明细，则表示：盘点时,盘盈的货品需要记录)',
  `caizhi` varchar(12) DEFAULT NULL COMMENT '材质',
  `jinzhong` decimal(10,3) DEFAULT '0.000' COMMENT '金重',
  `jingdu` varchar(10) DEFAULT NULL COMMENT '净度',
  `jinhao` float(6,3) NOT NULL DEFAULT '0.000' COMMENT '金耗',
  `yanse` varchar(35) DEFAULT NULL COMMENT '颜色',
  `zhengshuhao` varchar(50) DEFAULT NULL COMMENT '证书号',
  `zuanshidaxiao` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '钻石大小',
  `chengbenjia` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `mingyijia` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '名义价',
  `xiaoshoujia` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '销售价（损益单中的退货价） （在退货单中，该字段作为退货价）',
  `in_warehouse_type` tinyint(2) DEFAULT '0' COMMENT '入库方式 0、默认无。1.购买。2、委托加工。3、代销。4、借入',
  `account` tinyint(2) DEFAULT '0' COMMENT '是否结价0、默认无。1、未结价。2、已结价',
  `addtime` datetime DEFAULT NULL COMMENT '添加时间',
  `pandian_status` tinyint(2) DEFAULT '0' COMMENT '盘点状态 参考数字字典',
  `guiwei` varchar(35) DEFAULT NULL COMMENT '货品所在柜位号',
  `detail_id` int(11) DEFAULT NULL COMMENT '销售单和退后单存订单的detail_id所用',
  `pandian_guiwei` varchar(35) DEFAULT '0-00-0-0' COMMENT '盘点柜位',
  `pandian_user` varchar(35) DEFAULT NULL COMMENT '盘点人',
  `pifajia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '批发价格（批发单-名义价存放，批发退货-批发价存放）',
  `sale_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `shijia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际价格',
  `yuanshichengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原始采购成本',
  `bill_y_id` char(17) DEFAULT NULL COMMENT 'Y单号',
  `jiajialv` decimal(4,2) DEFAULT NULL COMMENT '加价率（适用于Y单）',
  `order_sn` varchar(30) DEFAULT NULL COMMENT '订单号',
  `dep_settlement_type` tinyint(1) DEFAULT NULL COMMENT 'æ•°æ®å­—å…¸warehouse.dep_settlement_type',
  `settlement_time` datetime DEFAULT NULL COMMENT 'ç»“ç®—æ“ä½œæ—¶é—´',
  `management_fee` decimal(8,3) DEFAULT '0.000' COMMENT 'ç®¡ç†è´¹'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for tt_warehouse_bill0112
-- ----------------------------
DROP TABLE IF EXISTS `tt_warehouse_bill0112`;
CREATE TABLE `tt_warehouse_bill0112` (
  `id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '序号',
  `bill_no` char(17) NOT NULL COMMENT '单据编号',
  `bill_type` varchar(14) DEFAULT '1' COMMENT '单据类型',
  `bill_status` tinyint(4) DEFAULT '1' COMMENT '数据字典：仓储单据状态（warehouse_in_status）/ 盘点单状态（warehouse.pandian_plan）',
  `order_sn` varchar(20) DEFAULT NULL COMMENT '订单号',
  `goods_num` int(8) DEFAULT NULL COMMENT '货品总数',
  `put_in_type` tinyint(1) DEFAULT '1' COMMENT '入库方式',
  `jiejia` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否结价',
  `tuihuoyuanyin` int(2) NOT NULL DEFAULT '0' COMMENT '退货原因',
  `send_goods_sn` varchar(25) DEFAULT NULL COMMENT '送货单号',
  `pro_id` int(10) DEFAULT NULL COMMENT '供应商ID',
  `pro_name` varchar(30) DEFAULT NULL COMMENT '供应商名称',
  `goods_total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '货总金额',
  `goods_total_jiajia` decimal(10,2) DEFAULT '0.00' COMMENT '加价之后的货总金额',
  `shijia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际销售价格',
  `to_warehouse_id` int(8) DEFAULT NULL COMMENT '入货仓ID (盘点单，该列存盘点的仓库,退货返厂单时，该字段记录出库仓)',
  `to_warehouse_name` varchar(100) DEFAULT NULL COMMENT '入货仓名称 (盘点单，该列存盘点的仓库)',
  `to_company_id` int(8) DEFAULT NULL COMMENT '入货公司ID',
  `to_company_name` varchar(100) DEFAULT NULL COMMENT '入货公司名称',
  `from_company_id` int(10) DEFAULT NULL COMMENT '出货公司id',
  `from_company_name` varchar(100) DEFAULT NULL COMMENT '出货公司名称',
  `bill_note` text COMMENT '备注',
  `yuanshichengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原始成本',
  `check_user` varchar(25) DEFAULT NULL COMMENT '审核人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `create_user` varchar(25) NOT NULL COMMENT '制单人',
  `create_time` datetime NOT NULL COMMENT '制单时间',
  `fin_check_status` tinyint(4) DEFAULT '1' COMMENT '财务审核状态:见数据字典',
  `fin_check_time` datetime DEFAULT NULL COMMENT '财务审核时间',
  `to_customer_id` int(10) NOT NULL DEFAULT '0' COMMENT '配送公司id',
  `pifajia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '批发价格（批发单-名义价存放，批发退货-批发价存放）',
  `company_id_from` int(11) DEFAULT NULL COMMENT 'ä¸šåŠ¡ç»„ç»‡å…¬å¸ID',
  `company_from` varchar(100) DEFAULT NULL COMMENT '业务组织公司名称',
  `from_bill_id` int(10) DEFAULT NULL COMMENT '来源单据id',
  `confirm_delivery` tinyint(1) DEFAULT '0' COMMENT '0 未确认;1已确认',
  `is_tsyd` tinyint(1) DEFAULT '0' COMMENT '是否经销商天生一对订单:0 不是;1 是',
  `production_manager_name` varchar(30) DEFAULT '0.00' COMMENT '生产跟单人',
  `sign_user` varchar(255) DEFAULT NULL COMMENT '签收人',
  `sign_time` datetime DEFAULT NULL COMMENT '签收日期',
  `out_warehouse_type` tinyint(1) DEFAULT NULL COMMENT 'æ•°æ®å­—å…¸warehouse.out_warehouse_type'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for vop_app_order_details
-- ----------------------------
DROP TABLE IF EXISTS `vop_app_order_details`;
CREATE TABLE `vop_app_order_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单号',
  `goods_id` varchar(30) NOT NULL DEFAULT '' COMMENT '货号',
  `goods_sn` varchar(60) DEFAULT NULL,
  `ext_goods_sn` varchar(50) DEFAULT ' ' COMMENT '原始款号',
  `goods_name` varchar(200) NOT NULL COMMENT '商品名称',
  `goods_price` decimal(16,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `favorable_price` decimal(10,2) DEFAULT NULL COMMENT '优惠价格:正数代表减钱，负数代表加钱',
  `goods_count` int(8) NOT NULL COMMENT '商品个数',
  `create_time` datetime NOT NULL COMMENT '添加时间',
  `modify_time` datetime NOT NULL COMMENT '修改时间',
  `create_user` varchar(100) DEFAULT '' COMMENT '创建人',
  `details_status` tinyint(2) NOT NULL DEFAULT '1',
  `send_good_status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '1未到货2已发货3到货未检验4到货已检验5返厂',
  `buchan_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '布产状态:1初始化2待分配3已分配4生产中7部分出厂9已出厂10已取消',
  `is_stock_goods` tinyint(1) NOT NULL COMMENT '是否是现货：1现货 0期货',
  `is_return` tinyint(3) NOT NULL DEFAULT '0' COMMENT '退货产品 0未退货1已退货',
  `details_remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `cart` varchar(60) DEFAULT NULL COMMENT '石重',
  `cut` varchar(60) DEFAULT NULL COMMENT '切工',
  `clarity` varchar(60) DEFAULT NULL COMMENT '净度',
  `color` varchar(60) DEFAULT NULL COMMENT '颜色',
  `cert` varchar(10) DEFAULT NULL COMMENT '证书类型',
  `zhengshuhao` varchar(60) DEFAULT NULL COMMENT '证书号',
  `caizhi` varchar(60) DEFAULT NULL COMMENT '材质',
  `jinse` varchar(60) DEFAULT NULL COMMENT '金色',
  `jinzhong` varchar(60) DEFAULT NULL COMMENT '金重',
  `zhiquan` varchar(60) DEFAULT NULL COMMENT '指圈',
  `kezi` varchar(60) DEFAULT NULL COMMENT '刻字',
  `face_work` varchar(60) DEFAULT NULL COMMENT '表面工艺',
  `xiangqian` varchar(60) DEFAULT NULL COMMENT '镶嵌要求',
  `goods_type` varchar(20) DEFAULT NULL COMMENT '商品类型lz:裸钻',
  `favorable_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '优惠审核状态；1：保存；2：提交申请；3：审核通过；4：审核驳回',
  `cat_type` smallint(4) DEFAULT NULL COMMENT '款式分类',
  `product_type` smallint(4) DEFAULT NULL COMMENT '产品线',
  `kuan_sn` varchar(30) DEFAULT NULL COMMENT '天生一对的款号',
  `xiangkou` varchar(50) DEFAULT NULL COMMENT '镶口',
  `chengbenjia` decimal(10,2) DEFAULT '0.00' COMMENT '成本价',
  `bc_id` int(10) DEFAULT '0',
  `policy_id` int(10) DEFAULT NULL COMMENT '销售政策商品',
  `is_peishi` tinyint(1) unsigned DEFAULT '0' COMMENT '是否支持4C配钻，0不支持，1裸钻支持，2空托支持',
  `is_zp` char(2) NOT NULL DEFAULT '0' COMMENT '是否赠品1.是0.否',
  `is_finance` tinyint(2) NOT NULL DEFAULT '2' COMMENT '是否销账,2.是.1否',
  `weixiu_status` tinyint(2) DEFAULT NULL,
  `allow_favorable` tinyint(2) DEFAULT '1' COMMENT '是否允许申请优惠',
  `qiban_type` tinyint(1) unsigned DEFAULT '2' COMMENT '起版类型：见数据字典qiban_type',
  `delivery_status` tinyint(1) DEFAULT '1' COMMENT '1：未配货；2允许配货；5已配货',
  `retail_price` decimal(16,2) DEFAULT '0.00' COMMENT '原始零售价',
  `ds_xiangci` varchar(60) DEFAULT NULL COMMENT '单身-项次（鼎捷）',
  `pinhao` varchar(60) DEFAULT NULL COMMENT '品号（鼎捷）',
  `dia_type` tinyint(1) DEFAULT '0' COMMENT '钻石类型（1、现货钻，2、期货钻）',
  `is_cpdz` tinyint(1) DEFAULT '0',
  `tuo_type` varchar(10) DEFAULT NULL,
  `zhushi_num` smallint(5) unsigned DEFAULT NULL COMMENT '主石粒数',
  `cpdzcode` varchar(11) DEFAULT NULL,
  `discount_point` decimal(10,2) DEFAULT '0.00' COMMENT '折扣积分',
  `reward_point` decimal(10,2) DEFAULT '0.00' COMMENT '奖励积分',
  `daijinquan_code` varchar(30) DEFAULT NULL COMMENT '代金券兑换码',
  `daijinquan_price` decimal(10,2) DEFAULT '0.00' COMMENT '代金券优惠金额',
  `daijinquan_addtime` datetime DEFAULT NULL COMMENT '代金券兑换码使用时间',
  `jifenma_code` varchar(30) DEFAULT NULL COMMENT '积分码',
  `jifenma_point` decimal(8,2) DEFAULT '0.00' COMMENT '积分码赠送积分',
  `zhuandan_cash` decimal(10,2) DEFAULT '0.00',
  `goods_from` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
