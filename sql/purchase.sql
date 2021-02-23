/*
Navicat MySQL Data Transfer

Source Server         : mysql_192.168.0.95_3306
Source Server Version : 50626
Source Host           : 192.168.0.95:3306
Source Database       : purchase

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2019-09-09 10:23:51
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for defective_product
-- ----------------------------
DROP TABLE IF EXISTS `defective_product`;
CREATE TABLE `defective_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态',
  `prc_id` int(11) NOT NULL COMMENT '供应商ID',
  `prc_name` varchar(50) NOT NULL COMMENT '供应商名称',
  `ship_num` varchar(30) NOT NULL COMMENT '出货单号',
  `num` int(11) NOT NULL COMMENT '总数量',
  `total` decimal(10,2) NOT NULL COMMENT '总金额',
  `make_name` varchar(20) NOT NULL COMMENT '制单人',
  `make_time` datetime NOT NULL COMMENT '制单时间',
  `check_name` varchar(20) NOT NULL COMMENT '审核人',
  `check_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '审核时间',
  `hidden` tinyint(1) DEFAULT '0' COMMENT '是否隐藏栏位',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3497 DEFAULT CHARSET=utf8 COMMENT='不良品返厂单';

-- ----------------------------
-- Table structure for defective_product_detail
-- ----------------------------
DROP TABLE IF EXISTS `defective_product_detail`;
CREATE TABLE `defective_product_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `info_id` int(11) NOT NULL COMMENT '主表关联ID',
  `xuhao` int(11) NOT NULL COMMENT '质检关联序号',
  `rece_detail_id` int(11) NOT NULL COMMENT '关联purchase_receipt_detail表ID',
  `factory_sn` varchar(20) NOT NULL COMMENT '工厂模号',
  `bc_sn` varchar(20) NOT NULL COMMENT '布产号',
  `customer_name` varchar(50) NOT NULL COMMENT '客户名',
  `cat_type` varchar(20) NOT NULL COMMENT '款式分类',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `info` varchar(100) NOT NULL COMMENT '备注：IQC未过原因',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7226 DEFAULT CHARSET=utf8 COMMENT='不良品返厂单明细表';

-- ----------------------------
-- Table structure for purchase_goods
-- ----------------------------
DROP TABLE IF EXISTS `purchase_goods`;
CREATE TABLE `purchase_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '采购商品ID',
  `pinfo_id` int(11) NOT NULL COMMENT '采购单ID',
  `style_sn` varchar(20) NOT NULL COMMENT '款号',
  `g_name` varchar(100) DEFAULT NULL COMMENT '货品名称',
  `product_type_id` int(11) NOT NULL DEFAULT '0' COMMENT '产品线ID',
  `cat_type_id` int(11) NOT NULL DEFAULT '0' COMMENT '款式分类ID',
  `num` int(11) NOT NULL DEFAULT '1' COMMENT '数量',
  `is_urgent` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否加急',
  `info` text COMMENT '备注',
  `xiangqian` varchar(60) NOT NULL DEFAULT '' COMMENT '镶嵌方式',
  `consignee` varchar(60) NOT NULL DEFAULT '' COMMENT '客户姓名',
  `is_apply` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否审请修改',
  `apply_info` text COMMENT '审请原因',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=33234 DEFAULT CHARSET=utf8 COMMENT='采购单商品表';

-- ----------------------------
-- Table structure for purchase_goods_attr
-- ----------------------------
DROP TABLE IF EXISTS `purchase_goods_attr`;
CREATE TABLE `purchase_goods_attr` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `g_id` int(11) NOT NULL COMMENT '采购商品ID',
  `code` varchar(100) NOT NULL COMMENT '属性code',
  `name` varchar(100) NOT NULL COMMENT '属性name',
  `value` varchar(255) NOT NULL COMMENT '属性value值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=387907 DEFAULT CHARSET=utf8 COMMENT='采购单货品属性表';

-- ----------------------------
-- Table structure for purchase_info
-- ----------------------------
DROP TABLE IF EXISTS `purchase_info`;
CREATE TABLE `purchase_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `p_sn` varchar(20) NOT NULL COMMENT '采购单单号',
  `t_id` int(11) NOT NULL COMMENT '采购单分类ID',
  `is_tofactory` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否去工厂生产',
  `is_style` tinyint(2) NOT NULL COMMENT '是否有款采购',
  `p_sum` int(5) NOT NULL COMMENT '采购数量',
  `purchase_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购申请费用',
  `put_in_type` int(11) NOT NULL COMMENT '采购方式（数据字典入库方式）',
  `make_uname` varchar(10) NOT NULL COMMENT '制单人姓名',
  `make_time` datetime NOT NULL COMMENT '制单时间',
  `check_uname` varchar(10) NOT NULL COMMENT '审核人姓名',
  `check_time` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '审核时间',
  `p_status` int(11) NOT NULL DEFAULT '1' COMMENT '采购单状态：1=新增，2=待审核，3=审核，4=作废',
  `p_info` text NOT NULL COMMENT '采购单备注',
  `prc_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '工厂ID',
  `prc_name` varchar(100) NOT NULL DEFAULT '' COMMENT '工厂名称',
  `to_factory_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '采购单分配工厂时间',
  `channel_ids` varchar(255) DEFAULT NULL COMMENT '采购——销售渠道',
  `is_zhanyong` tinyint(1) DEFAULT '0' COMMENT '是否占用备货',
  `hidden` tinyint(1) DEFAULT '0' COMMENT '是否隐藏栏位',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=992553 DEFAULT CHARSET=utf8 COMMENT='采购单基础表';

-- ----------------------------
-- Table structure for purchase_iqc_opra
-- ----------------------------
DROP TABLE IF EXISTS `purchase_iqc_opra`;
CREATE TABLE `purchase_iqc_opra` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `rece_detail_id` int(11) NOT NULL COMMENT '操作的序号',
  `opra_code` int(3) NOT NULL COMMENT '见数据字典（IQC质检操作）',
  `opra_uname` varchar(20) NOT NULL COMMENT '操作人',
  `opra_time` datetime NOT NULL COMMENT '操作时间',
  `opra_info` varchar(100) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=824 DEFAULT CHARSET=utf8 COMMENT='IQC质检表';

-- ----------------------------
-- Table structure for purchase_log
-- ----------------------------
DROP TABLE IF EXISTS `purchase_log`;
CREATE TABLE `purchase_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `rece_detail_id` int(11) NOT NULL COMMENT '关联序号ID',
  `status` int(11) NOT NULL COMMENT '当前状态',
  `remark` varchar(1024) NOT NULL COMMENT '备注',
  `uid` int(11) NOT NULL COMMENT '操作人ID',
  `uname` varchar(20) NOT NULL COMMENT '操作人姓名',
  `time` datetime NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=169001 DEFAULT CHARSET=utf8 COMMENT='采购货物流水日志';

-- ----------------------------
-- Table structure for purchase_qiban_goods
-- ----------------------------
DROP TABLE IF EXISTS `purchase_qiban_goods`;
CREATE TABLE `purchase_qiban_goods` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `info` varchar(255) NOT NULL COMMENT '备注',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `addtime` int(10) NOT NULL DEFAULT '0' COMMENT '起版码',
  `order_sn` varchar(25) NOT NULL DEFAULT '' COMMENT '订单',
  `opt` varchar(30) NOT NULL DEFAULT '' COMMENT '录单人',
  `customer` varchar(30) NOT NULL DEFAULT '' COMMENT '顾客',
  `xiangkou` varchar(30) NOT NULL DEFAULT '' COMMENT '镶口',
  `shoucun` varchar(20) NOT NULL DEFAULT '' COMMENT '手寸',
  `specifi` varchar(30) NOT NULL DEFAULT '' COMMENT '规格',
  `fuzhu` varchar(20) NOT NULL DEFAULT '' COMMENT '辅助版号',
  `qibanfei` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '起版费',
  `jinliao` tinyint(1) NOT NULL DEFAULT '0' COMMENT '金料 数字字典 purchase.qiban_jinliao',
  `jinse` tinyint(1) NOT NULL DEFAULT '0' COMMENT '金色 数字字典 purchase.qiban_jinse',
  `gongyi` tinyint(1) NOT NULL DEFAULT '0' COMMENT '表面工艺 数字字典 purchase.qiban_gongyi',
  `is_shenhe` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否审核2=未审核 1=已审核',
  `is_fukuan` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否付款 2未付款 1=已付款',
  `gongchang_id` int(10) NOT NULL DEFAULT '0' COMMENT '供应商id',
  `gongchang` varchar(30) NOT NULL DEFAULT '' COMMENT '工厂',
  `kuanhao` varchar(20) NOT NULL DEFAULT '' COMMENT '款号',
  `zhengshu` varchar(20) NOT NULL DEFAULT '' COMMENT '证书号',
  `xuqiu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '产品需求 数字字典 purchase.qiban_xuqiu',
  `pic` varchar(100) NOT NULL DEFAULT '' COMMENT '起版图片',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 数字字典 purchase.qiban_status',
  `kuan_type` tinyint(1) NOT NULL COMMENT '款式类型',
  `qiban_type` varchar(20) NOT NULL COMMENT '起版类型',
  `zhushi_num` smallint(5) unsigned DEFAULT '0' COMMENT '主石粒数',
  `cert` varchar(10) DEFAULT NULL COMMENT '证书类型',
  `jinzhong_min` decimal(10,3) DEFAULT NULL,
  `jinzhong_max` decimal(10,3) DEFAULT NULL,
  `hidden` tinyint(1) DEFAULT '0' COMMENT '是否隐藏栏位',
  `jingdu` varchar(60) DEFAULT NULL,
  `yanse` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `addtime` (`addtime`),
  KEY `opt` (`opt`),
  KEY `customer` (`customer`),
  KEY `kuanhao` (`kuanhao`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=13183 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for purchase_qiban_goods_bak
-- ----------------------------
DROP TABLE IF EXISTS `purchase_qiban_goods_bak`;
CREATE TABLE `purchase_qiban_goods_bak` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `info` varchar(255) NOT NULL COMMENT '备注',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `addtime` int(10) NOT NULL DEFAULT '0' COMMENT '起版码',
  `order_sn` varchar(25) NOT NULL DEFAULT '' COMMENT '订单',
  `opt` varchar(30) NOT NULL DEFAULT '' COMMENT '录单人',
  `customer` varchar(30) NOT NULL DEFAULT '' COMMENT '顾客',
  `xiangkou` varchar(30) NOT NULL DEFAULT '' COMMENT '镶口',
  `shoucun` varchar(20) NOT NULL DEFAULT '' COMMENT '手寸',
  `specifi` varchar(30) NOT NULL DEFAULT '' COMMENT '规格',
  `fuzhu` varchar(20) NOT NULL DEFAULT '' COMMENT '辅助版号',
  `qibanfei` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '起版费',
  `jinliao` tinyint(1) NOT NULL DEFAULT '0' COMMENT '金料 数字字典 purchase.qiban_jinliao',
  `jinse` tinyint(1) NOT NULL DEFAULT '0' COMMENT '金色 数字字典 purchase.qiban_jinse',
  `gongyi` tinyint(1) NOT NULL DEFAULT '0' COMMENT '表面工艺 数字字典 purchase.qiban_gongyi',
  `is_shenhe` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否审核2=未审核 1=已审核',
  `is_fukuan` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否付款 2未付款 1=已付款',
  `gongchang_id` int(10) NOT NULL DEFAULT '0' COMMENT '供应商id',
  `gongchang` varchar(30) NOT NULL DEFAULT '' COMMENT '工厂',
  `kuanhao` varchar(20) NOT NULL DEFAULT '' COMMENT '款号',
  `zhengshu` varchar(20) NOT NULL DEFAULT '' COMMENT '证书号',
  `xuqiu` tinyint(1) NOT NULL DEFAULT '0' COMMENT '产品需求 数字字典 purchase.qiban_xuqiu',
  `pic` varchar(100) NOT NULL DEFAULT '' COMMENT '起版图片',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 数字字典 purchase.qiban_status',
  `kuan_type` tinyint(1) NOT NULL COMMENT '款式类型',
  `qiban_type` varchar(20) NOT NULL COMMENT '起版类型',
  PRIMARY KEY (`id`),
  KEY `addtime` (`addtime`),
  KEY `opt` (`opt`),
  KEY `customer` (`customer`),
  KEY `kuanhao` (`kuanhao`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=7343 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for purchase_receipt
-- ----------------------------
DROP TABLE IF EXISTS `purchase_receipt`;
CREATE TABLE `purchase_receipt` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '见数据字典：采购收货单状态',
  `prc_id` int(10) NOT NULL COMMENT '供应商id	',
  `prc_name` varchar(50) NOT NULL COMMENT '加工商名称',
  `ship_num` varchar(25) NOT NULL COMMENT '出货单号',
  `chengbenjia` decimal(10,2) NOT NULL COMMENT '成本价',
  `remark` varchar(60) DEFAULT NULL COMMENT '备注',
  `num` int(10) NOT NULL COMMENT '数量',
  `all_amount` decimal(10,2) NOT NULL COMMENT '总金额',
  `user_id` int(10) NOT NULL COMMENT '操作人id',
  `user_name` varchar(10) NOT NULL COMMENT '操作人',
  `create_time` datetime NOT NULL COMMENT '操作时间',
  `edit_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改人ID',
  `edit_user_name` varchar(20) NOT NULL DEFAULT '' COMMENT '创建人',
  `edit_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `hidden` tinyint(1) DEFAULT '0' COMMENT '是否隐藏栏位',
  PRIMARY KEY (`id`),
  KEY `edit_time` (`edit_time`)
) ENGINE=InnoDB AUTO_INCREMENT=3316 DEFAULT CHARSET=utf8 COMMENT='采购工厂出库表';

-- ----------------------------
-- Table structure for purchase_receipt_detail
-- ----------------------------
DROP TABLE IF EXISTS `purchase_receipt_detail`;
CREATE TABLE `purchase_receipt_detail` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `xuhao` int(11) NOT NULL COMMENT '序号',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '状态（见数据字典，采购收货货品状态）',
  `purchase_receipt_id` int(10) NOT NULL COMMENT '采购收货流水id',
  `purchase_sn` varchar(20) NOT NULL COMMENT '采购单单号',
  `customer_name` varchar(50) NOT NULL COMMENT '客户名',
  `bc_sn` varchar(20) NOT NULL COMMENT '布产号（有款布产必填）',
  `style_sn` varchar(25) NOT NULL COMMENT '款号（有款必填）',
  `factory_sn` varchar(20) DEFAULT NULL COMMENT '模号（无款、有款都需添）',
  `ring_mouth` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '戒托镶口',
  `is_cp_kt` varchar(10) NOT NULL COMMENT '成品/空托',
  `cat_type` varchar(20) NOT NULL COMMENT '款式分类',
  `hand_inch` float(10,3) NOT NULL DEFAULT '0.000' COMMENT '手寸',
  `material` varchar(10) NOT NULL COMMENT '主成色',
  `gross_weight` decimal(10,3) NOT NULL COMMENT '毛重	',
  `net_gold_weight` decimal(10,3) NOT NULL COMMENT '主成色重(净金重)',
  `gold_loss` decimal(10,3) NOT NULL COMMENT '金耗',
  `gold_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '主成色买入单价(金价)',
  `main_stone` varchar(20) NOT NULL COMMENT '主石',
  `main_stone_weight` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '主石重',
  `main_stone_num` int(10) NOT NULL DEFAULT '0' COMMENT '主石数量',
  `work_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '工费	',
  `extra_stone_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '超石费',
  `other_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '其他费用',
  `fittings_cost_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '配件成本',
  `tax_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '税费',
  `customer_info_stone` varchar(40) NOT NULL COMMENT '客来石信息',
  `chengbenjia` decimal(10,2) DEFAULT '0.00' COMMENT '成本价',
  `zhushiyanse` varchar(50) DEFAULT NULL COMMENT '主石颜色',
  `zhushijingdu` varchar(50) DEFAULT NULL COMMENT '主石净度',
  `zhushidanjia` decimal(10,2) DEFAULT NULL COMMENT '主石买入单价',
  `fushi` varchar(50) DEFAULT NULL COMMENT '副石(副石类别)',
  `fushilishu` int(10) NOT NULL DEFAULT '0' COMMENT '副石粒数',
  `fushizhong` decimal(8,3) DEFAULT NULL COMMENT '副石重',
  `fushidanjia` decimal(10,2) DEFAULT NULL COMMENT '副石买入单价',
  `zhengshuhao` varchar(100) DEFAULT NULL COMMENT '证书号',
  `shi2` varchar(50) DEFAULT NULL COMMENT '石2(石2类别)',
  `shi2lishu` int(10) DEFAULT '0' COMMENT '石2粒数',
  `shi2zhong` decimal(8,3) DEFAULT NULL COMMENT '石2重',
  `shi2danjia` decimal(8,2) DEFAULT NULL COMMENT '石2买入单价',
  `shi3` varchar(30) DEFAULT NULL COMMENT '石3(石3类别)',
  `shi3lishu` int(10) DEFAULT '0' COMMENT '石3粒数',
  `shi3zhong` decimal(8,3) DEFAULT NULL COMMENT '石3重',
  `shi3danjia` decimal(8,2) DEFAULT NULL COMMENT '石3买入单价',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36626 DEFAULT CHARSET=utf8 COMMENT='采购工厂出库单明细';

-- ----------------------------
-- Table structure for purchase_type
-- ----------------------------
DROP TABLE IF EXISTS `purchase_type`;
CREATE TABLE `purchase_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '采购类型ID',
  `t_name` varchar(20) NOT NULL COMMENT '采购类型名称',
  `is_auto` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否支持系统自动匹配 1：匹配 0：不匹配',
  `is_enabled` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否开启：1=开启，0=关闭',
  `is_deleted` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否是系统内置 0：否 / 1：是',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='采购类型表';
