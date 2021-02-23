/*
Navicat MySQL Data Transfer

Source Server         : mysql_192.168.0.95_3306
Source Server Version : 50626
Source Host           : 192.168.0.95:3306
Source Database       : shipping

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2019-09-09 10:24:20
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for express_check
-- ----------------------------
DROP TABLE IF EXISTS `express_check`;
CREATE TABLE `express_check` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `option` tinyint(1) NOT NULL DEFAULT '0' COMMENT '(0:未对账 1：已对账)',
  `name` varchar(30) NOT NULL,
  `oldname` varchar(30) NOT NULL,
  `path` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='(快递对账表)';

-- ----------------------------
-- Table structure for express_file
-- ----------------------------
DROP TABLE IF EXISTS `express_file`;
CREATE TABLE `express_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `express_id` int(10) unsigned DEFAULT NULL COMMENT '快递公司ID',
  `file_md5` varchar(60) DEFAULT NULL COMMENT '文件Md5唯一标识（防止重复提交）',
  `filename` varchar(255) DEFAULT NULL COMMENT '文件名',
  `detail_num` smallint(5) unsigned DEFAULT NULL COMMENT '文件内的快递单数目',
  `is_print` tinyint(1) unsigned DEFAULT '0' COMMENT '打印状态(0未打印，1已打印)',
  `is_register` tinyint(3) unsigned DEFAULT '0' COMMENT '登记状态(0未登记，1已登记)',
  `print_time` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '打印时间',
  `register_time` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '登记时间',
  `create_user` varchar(50) DEFAULT NULL COMMENT '创建人(上传人)用户名',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间（上传时间）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8 COMMENT='上传快递单文件表';

-- ----------------------------
-- Table structure for express_file--
-- ----------------------------
DROP TABLE IF EXISTS `express_file--`;
CREATE TABLE `express_file--` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `express_id` int(10) unsigned DEFAULT NULL COMMENT '快递公司ID',
  `file_md5` varchar(60) DEFAULT NULL COMMENT '文件Md5唯一标识（防止重复提交）',
  `filename` varchar(255) DEFAULT NULL COMMENT '文件名',
  `detail_num` smallint(5) unsigned DEFAULT NULL COMMENT '文件内的快递单数目',
  `is_print` tinyint(1) unsigned DEFAULT '0' COMMENT '打印状态(0未打印，1已打印)',
  `is_register` tinyint(3) unsigned DEFAULT '0' COMMENT '登记状态(0未登记，1已登记)',
  `print_time` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '打印时间',
  `register_time` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '登记时间',
  `create_user` varchar(50) DEFAULT NULL COMMENT '创建人(上传人)用户名',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间（上传时间）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8 COMMENT='上传快递单文件表';

-- ----------------------------
-- Table structure for express_file_detail
-- ----------------------------
DROP TABLE IF EXISTS `express_file_detail`;
CREATE TABLE `express_file_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned DEFAULT NULL COMMENT '文件ID',
  `freight_no` varchar(60) DEFAULT NULL COMMENT '快递单编号',
  `sender` varchar(20) DEFAULT NULL COMMENT '寄件人',
  `department` varchar(40) DEFAULT NULL COMMENT '寄件部门',
  `remark` varchar(255) DEFAULT NULL COMMENT '发件缘由',
  `consignee` varchar(20) DEFAULT NULL COMMENT '收件人',
  `cons_address` varchar(120) DEFAULT NULL COMMENT '收件人地址',
  `cons_tel` varchar(20) DEFAULT NULL COMMENT '收件人联系电话',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4735 DEFAULT CHARSET=utf8 COMMENT='快递单文件明细';

-- ----------------------------
-- Table structure for express_file_detail--
-- ----------------------------
DROP TABLE IF EXISTS `express_file_detail--`;
CREATE TABLE `express_file_detail--` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` int(10) unsigned DEFAULT NULL COMMENT '文件ID',
  `freight_no` varchar(60) DEFAULT NULL COMMENT '快递单编号',
  `sender` varchar(20) DEFAULT NULL COMMENT '寄件人',
  `department` varchar(40) DEFAULT NULL COMMENT '寄件部门',
  `remark` varchar(255) DEFAULT NULL COMMENT '发件缘由',
  `consignee` varchar(20) DEFAULT NULL COMMENT '收件人',
  `cons_address` varchar(120) DEFAULT NULL COMMENT '收件人地址',
  `cons_tel` varchar(20) DEFAULT NULL COMMENT '收件人联系电话',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3664 DEFAULT CHARSET=utf8 COMMENT='快递单文件明细';

-- ----------------------------
-- Table structure for express_list
-- ----------------------------
DROP TABLE IF EXISTS `express_list`;
CREATE TABLE `express_list` (
  `id` bigint(30) NOT NULL AUTO_INCREMENT,
  `express_id` int(10) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `create_user` varchar(60) DEFAULT NULL,
  `province` varchar(60) DEFAULT NULL,
  `city` varchar(60) DEFAULT NULL,
  `district` varchar(60) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `d_tel` varchar(60) DEFAULT NULL,
  `d_contact` varchar(60) DEFAULT NULL,
  `express_no` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50000602 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for express_list20170726
-- ----------------------------
DROP TABLE IF EXISTS `express_list20170726`;
CREATE TABLE `express_list20170726` (
  `id` bigint(30) NOT NULL DEFAULT '0',
  `express_id` int(10) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `create_user` varchar(60) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `d_tel` varchar(60) DEFAULT NULL,
  `d_contact` varchar(60) DEFAULT NULL,
  `express_no` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ship_freight
-- ----------------------------
DROP TABLE IF EXISTS `ship_freight`;
CREATE TABLE `ship_freight` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `order_no` varchar(150) DEFAULT NULL,
  `freight_no` varchar(30) DEFAULT NULL COMMENT '快递单号',
  `express_id` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '快递公司ID',
  `consignee` varchar(20) DEFAULT NULL COMMENT '收件人',
  `cons_address` varchar(150) DEFAULT NULL COMMENT '收货地址',
  `cons_mobile` varchar(32) DEFAULT NULL COMMENT '收件人手机',
  `cons_tel` varchar(32) DEFAULT NULL COMMENT '收件人电话',
  `order_mount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单总金额',
  `remark` varchar(100) DEFAULT NULL COMMENT '发货缘由(订单发货 展厅发货)',
  `is_print` tinyint(1) NOT NULL DEFAULT '1' COMMENT '打印状态（1，打印 2.未打印）',
  `print_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '打印时间',
  `sender` varchar(20) DEFAULT NULL COMMENT '寄件人',
  `department` varchar(40) DEFAULT NULL COMMENT '寄件部门',
  `note` varchar(200) DEFAULT NULL COMMENT '操作备注',
  `create_id` int(10) unsigned DEFAULT NULL COMMENT '操作人ID',
  `create_name` varchar(20) DEFAULT NULL COMMENT '操作人',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '操作时间',
  `is_deleted` tinyint(1) unsigned DEFAULT '0' COMMENT '删除标识',
  `channel_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '渠道',
  `out_order_id` varchar(300) NOT NULL DEFAULT '' COMMENT '外部订单号',
  `is_tsyd` tinyint(1) DEFAULT '0' COMMENT '是否经销商天生一对订单:0 不是;1 是',
  PRIMARY KEY (`id`),
  KEY `order_no` (`order_no`),
  KEY `freight_no` (`freight_no`),
  KEY `createtime` (`create_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=363081 DEFAULT CHARSET=utf8 COMMENT='快递单列表';

-- ----------------------------
-- Table structure for ship_freight_1107
-- ----------------------------
DROP TABLE IF EXISTS `ship_freight_1107`;
CREATE TABLE `ship_freight_1107` (
  `id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '序号',
  `order_no` varchar(30) DEFAULT NULL COMMENT '订单号',
  `freight_no` varchar(30) DEFAULT NULL COMMENT '快递单号',
  `express_id` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '快递公司ID',
  `consignee` varchar(20) DEFAULT NULL COMMENT '收件人',
  `cons_address` varchar(150) DEFAULT NULL COMMENT '收货地址',
  `cons_mobile` varchar(20) DEFAULT NULL COMMENT '收件人手机',
  `cons_tel` varchar(20) DEFAULT NULL COMMENT '收件人电话',
  `order_mount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单总金额',
  `remark` varchar(100) DEFAULT NULL COMMENT '发货缘由(订单发货 展厅发货)',
  `is_print` tinyint(1) NOT NULL DEFAULT '1' COMMENT '打印状态（1，打印 2.未打印）',
  `print_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '打印时间',
  `sender` varchar(20) DEFAULT NULL COMMENT '寄件人',
  `department` varchar(40) DEFAULT NULL COMMENT '寄件部门',
  `note` varchar(200) DEFAULT NULL COMMENT '操作备注',
  `create_id` int(10) unsigned DEFAULT NULL COMMENT '操作人ID',
  `create_name` varchar(20) DEFAULT NULL COMMENT '操作人',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '操作时间',
  `is_deleted` tinyint(1) unsigned DEFAULT '0' COMMENT '删除标识',
  `channel_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '渠道',
  `out_order_id` varchar(50) NOT NULL DEFAULT '' COMMENT '外部订单号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ship_freight_51
-- ----------------------------
DROP TABLE IF EXISTS `ship_freight_51`;
CREATE TABLE `ship_freight_51` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `order_no` varchar(150) DEFAULT NULL,
  `freight_no` varchar(30) DEFAULT NULL COMMENT '快递单号',
  `express_id` smallint(10) unsigned NOT NULL DEFAULT '0' COMMENT '快递公司ID',
  `consignee` varchar(20) DEFAULT NULL COMMENT '收件人',
  `cons_address` varchar(150) DEFAULT NULL COMMENT '收货地址',
  `cons_mobile` varchar(32) DEFAULT NULL COMMENT '收件人手机',
  `cons_tel` varchar(32) DEFAULT NULL COMMENT '收件人电话',
  `order_mount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单总金额',
  `remark` varchar(100) DEFAULT NULL COMMENT '发货缘由(订单发货 展厅发货)',
  `is_print` tinyint(1) NOT NULL DEFAULT '1' COMMENT '打印状态（1，打印 2.未打印）',
  `print_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '打印时间',
  `sender` varchar(20) DEFAULT NULL COMMENT '寄件人',
  `department` varchar(40) DEFAULT NULL COMMENT '寄件部门',
  `note` varchar(200) DEFAULT NULL COMMENT '操作备注',
  `create_id` int(10) unsigned DEFAULT NULL COMMENT '操作人ID',
  `create_name` varchar(20) DEFAULT NULL COMMENT '操作人',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '操作时间',
  `is_deleted` tinyint(1) unsigned DEFAULT '0' COMMENT '删除标识',
  `channel_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '渠道',
  `out_order_id` varchar(50) NOT NULL DEFAULT '' COMMENT '外部订单号',
  `is_tsyd` tinyint(1) DEFAULT '0' COMMENT '是否经销商天生一对订单:0 不是;1 是',
  PRIMARY KEY (`id`),
  KEY `order_no` (`order_no`),
  KEY `freight_no` (`freight_no`),
  KEY `createtime` (`create_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=419 DEFAULT CHARSET=utf8 COMMENT='快递单列表';

-- ----------------------------
-- Table structure for ship_parcel
-- ----------------------------
DROP TABLE IF EXISTS `ship_parcel`;
CREATE TABLE `ship_parcel` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `express_id` int(10) DEFAULT '0' COMMENT '快递公司ID',
  `express_sn` varchar(25) DEFAULT NULL COMMENT '快递单号',
  `amount` decimal(10,2) DEFAULT '0.00' COMMENT '单据总金额',
  `num` int(10) DEFAULT '0' COMMENT '货品数量',
  `shouhuoren` varchar(45) DEFAULT NULL COMMENT '包裹收货方',
  `company_id` int(10) DEFAULT '0' COMMENT '公司ID （目标展厅）',
  `sales_channels` int(10) DEFAULT '0' COMMENT '渠道类别（数字字典）',
  `create_time` datetime DEFAULT NULL COMMENT '制单时间',
  `send_status` tinyint(1) DEFAULT '1' COMMENT '发货状态(数字字典order.send_good_status)',
  `send_time` datetime DEFAULT NULL COMMENT '发货时间',
  `is_print` tinyint(1) DEFAULT '0' COMMENT '是否打印(数字字典confirm)',
  `create_user` varchar(25) DEFAULT NULL COMMENT '制单人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5563 DEFAULT CHARSET=utf8 COMMENT='包裹单数据表';

-- ----------------------------
-- Table structure for ship_parcel_detail
-- ----------------------------
DROP TABLE IF EXISTS `ship_parcel_detail`;
CREATE TABLE `ship_parcel_detail` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parcel_id` int(10) NOT NULL COMMENT '包裹id 关联ship_parcel 主键ID',
  `zhuancang_sn` varchar(35) DEFAULT NULL COMMENT '调拨单单号',
  `from_place_id` int(10) DEFAULT NULL COMMENT '出货地ID',
  `to_warehouse_id` int(10) DEFAULT NULL COMMENT '入货仓ID',
  `shouhuoren` varchar(35) DEFAULT NULL COMMENT '收货人',
  `num` int(10) DEFAULT NULL COMMENT '货品数量',
  `num_danwei` varchar(10) NOT NULL DEFAULT '件' COMMENT '数量单位',
  `amount` varchar(255) DEFAULT NULL COMMENT '货品金额',
  `goods_sn` text COMMENT '全部款号',
  `goods_name` text COMMENT '全部商品名称',
  `create_user` varchar(35) DEFAULT NULL COMMENT '添加人',
  `create_time` datetime DEFAULT NULL COMMENT '添加时间',
  `order_sn` varchar(45) DEFAULT NULL COMMENT 'BDD订单号',
  PRIMARY KEY (`id`),
  KEY `parcel_id` (`parcel_id`),
  CONSTRAINT `ship_parcel_detail_ibfk_1` FOREIGN KEY (`parcel_id`) REFERENCES `ship_parcel` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38191 DEFAULT CHARSET=utf8 COMMENT='包裹单详情表';

-- ----------------------------
-- Table structure for ship_parcel_log
-- ----------------------------
DROP TABLE IF EXISTS `ship_parcel_log`;
CREATE TABLE `ship_parcel_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `parcel_id` int(10) NOT NULL DEFAULT '0' COMMENT '包裹ID 关联ship_parcel 表主键',
  `operate_content` varchar(255) DEFAULT NULL COMMENT '操作内容/备注',
  `operate_time` datetime DEFAULT NULL COMMENT '操作时间',
  `operate_user` varchar(35) DEFAULT NULL COMMENT '操作人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14299 DEFAULT CHARSET=utf8 COMMENT='包裹日志表';
