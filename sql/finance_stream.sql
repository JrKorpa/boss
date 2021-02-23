/*
Navicat MySQL Data Transfer

Source Server         : mysql_192.168.0.95_3306
Source Server Version : 50626
Source Host           : 192.168.0.95:3306
Source Database       : finance_stream

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2019-09-09 10:22:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for goods
-- ----------------------------
DROP TABLE IF EXISTS `goods`;
CREATE TABLE `goods` (
  `serial_number` int(10) NOT NULL AUTO_INCREMENT COMMENT '流水号',
  `item_id` varchar(20) NOT NULL COMMENT '货号/单号',
  `order_id` int(11) NOT NULL COMMENT '订单ID',
  `zhengshuhao` varchar(100) NOT NULL COMMENT '证书号（代销借货用）',
  `goods_status` int(4) NOT NULL DEFAULT '1' COMMENT '0=初始化，1=库存，2=已销售，3=转仓中，4=盘点中，5=销售中，6=冻结，7=已返厂,8=退货中，9=返厂中, 10=作废, 11=损益中,12=已报损',
  `item_type` varchar(20) NOT NULL COMMENT '分类/单据类型',
  `company` int(10) NOT NULL COMMENT '所属公司',
  `prc_id` int(4) NOT NULL COMMENT '供货商ID',
  `prc_name` varchar(30) NOT NULL COMMENT '供货商',
  `prc_num` varchar(30) NOT NULL COMMENT '供货商单号',
  `type` int(2) NOT NULL DEFAULT '1' COMMENT '1代销、2成品、3石包',
  `pay_content` varchar(20) NOT NULL COMMENT '支付内容(收货单)',
  `storage_mode` int(2) NOT NULL COMMENT '入库方式 0=默认 1=购买 2=委托加工 3=代销 4=借入',
  `make_time` datetime NOT NULL COMMENT '入库制单时间',
  `check_time` datetime NOT NULL COMMENT '入库审核时间',
  `total` decimal(10,2) NOT NULL COMMENT '采购成本/单据金额',
  `pay_apply_status` int(4) NOT NULL DEFAULT '1' COMMENT '应付申请状态（1=>待申请，2=>待审核，3=>已驳回，4=>已审核）',
  `pay_apply_number` varchar(20) NOT NULL COMMENT '应付申请单号',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`serial_number`)
) ENGINE=MyISAM AUTO_INCREMENT=2501412 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for jxc_jiezhang
-- ----------------------------
DROP TABLE IF EXISTS `jxc_jiezhang`;
CREATE TABLE `jxc_jiezhang` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `qihao` tinyint(3) NOT NULL DEFAULT '0' COMMENT '期号（1-12）',
  `start_time` date NOT NULL COMMENT '开始日期',
  `end_time` date NOT NULL COMMENT '结束日期',
  `year` char(4) NOT NULL COMMENT '会计年度',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for jxc_processors
-- ----------------------------
DROP TABLE IF EXISTS `jxc_processors`;
CREATE TABLE `jxc_processors` (
  `p_id` int(4) NOT NULL AUTO_INCREMENT,
  `suoshu` varchar(40) NOT NULL COMMENT '加工商所属公司',
  `p_sn` varchar(30) NOT NULL,
  `p_name` varchar(30) NOT NULL,
  `parent_name` varchar(60) NOT NULL,
  `email` varchar(32) DEFAULT NULL COMMENT '供货商邮箱',
  `password` varchar(128) DEFAULT NULL COMMENT '密码',
  `pt_id` int(4) NOT NULL,
  `level` tinyint(3) NOT NULL DEFAULT '0',
  `contact` varchar(30) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `address` varchar(30) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '1',
  `addtime` datetime NOT NULL,
  `account` decimal(10,2) NOT NULL DEFAULT '0.00',
  `add_rate` float NOT NULL DEFAULT '1' COMMENT '加价率',
  `dia_add_rate` float NOT NULL DEFAULT '1',
  `zhenzhu_add_rate` float NOT NULL DEFAULT '1',
  `fin_own` varchar(255) NOT NULL COMMENT '财务负责人',
  `kaihuhang` varchar(255) NOT NULL,
  `fapiao` int(1) NOT NULL DEFAULT '0' COMMENT '0=不开',
  `info` text NOT NULL,
  `is_qianzi` int(1) NOT NULL DEFAULT '0' COMMENT '是否财务签字；0为否；1为是',
  `company_type` int(1) NOT NULL DEFAULT '0' COMMENT '公司类型；0为A类；1为B类；',
  `payment` tinyint(1) NOT NULL COMMENT '结算方式1自然日,2月结,3货到付款,',
  `pay_day` smallint(6) NOT NULL COMMENT '结算方式对应的日期',
  `kaihuzhanghao` varchar(20) NOT NULL COMMENT '开户账户',
  PRIMARY KEY (`p_id`),
  UNIQUE KEY `p_sn` (`p_sn`)
) ENGINE=MyISAM AUTO_INCREMENT=507 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for operat_log
-- ----------------------------
DROP TABLE IF EXISTS `operat_log`;
CREATE TABLE `operat_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `related_id` int(10) NOT NULL COMMENT '关联ID',
  `type` int(2) NOT NULL DEFAULT '1' COMMENT '分类( 1、应付申请单)',
  `operat_name` varchar(20) NOT NULL COMMENT '操作人',
  `operat_time` datetime NOT NULL COMMENT '操作时间',
  `operat_content` varchar(100) NOT NULL COMMENT '操作内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=213 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pay_apply
-- ----------------------------
DROP TABLE IF EXISTS `pay_apply`;
CREATE TABLE `pay_apply` (
  `apply_id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `pay_apply_number` varchar(20) NOT NULL DEFAULT 'YFSQ' COMMENT '应付申请单号',
  `status` int(2) NOT NULL DEFAULT '0' COMMENT '状态(0、新增；1、待审核；2、已驳回；3、已取消；4、待生成应付单；5、已生成应付单)',
  `pay_number` varchar(20) NOT NULL DEFAULT '' COMMENT '财务应付单单号',
  `make_time` datetime NOT NULL COMMENT '制单时间',
  `make_name` varchar(20) NOT NULL COMMENT '制单人',
  `check_time` datetime NOT NULL COMMENT '审核时间',
  `check_name` varchar(20) NOT NULL COMMENT '审核人',
  `company` int(10) NOT NULL COMMENT '所属公司',
  `prc_id` int(10) NOT NULL COMMENT '供货商ID',
  `prc_name` varchar(30) NOT NULL COMMENT '供货商名称',
  `pay_type` tinyint(3) NOT NULL COMMENT '应付类型',
  `amount` int(30) NOT NULL DEFAULT '0' COMMENT '总数量',
  `total_cope` decimal(10,2) NOT NULL COMMENT '应付金额',
  `total_dev` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '偏差金额',
  `adj_reason` varchar(100) DEFAULT NULL COMMENT '调整原因（调整单的调整原因）',
  `record_type` int(2) NOT NULL DEFAULT '1' COMMENT '单据类型(1、应付申请单；2、应付调整单)',
  `overrule_reason` varchar(100) DEFAULT '' COMMENT '调整单的驳回原因',
  `fapiao` varchar(40) DEFAULT NULL COMMENT '发票',
  PRIMARY KEY (`apply_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10048 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pay_apply_goods
-- ----------------------------
DROP TABLE IF EXISTS `pay_apply_goods`;
CREATE TABLE `pay_apply_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `apply_id` int(10) NOT NULL COMMENT '应付申请ID',
  `serial_number` int(10) NOT NULL COMMENT '货流水号',
  `goods_id` varchar(30) NOT NULL COMMENT '货号/单号',
  `total` decimal(10,2) NOT NULL COMMENT '系统金额',
  `total_cope` decimal(10,2) NOT NULL COMMENT '应付金额',
  `total_dev` decimal(10,2) NOT NULL COMMENT '偏差金额',
  `dev_direction` varchar(50) DEFAULT NULL COMMENT '偏差说明',
  `overrule_reason` varchar(50) DEFAULT NULL COMMENT '驳回原因',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=209 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pay_real
-- ----------------------------
DROP TABLE IF EXISTS `pay_real`;
CREATE TABLE `pay_real` (
  `pay_real_number` int(10) NOT NULL AUTO_INCREMENT COMMENT '财务实付单号',
  `pay_real_all_name` varchar(20) NOT NULL COMMENT '全名',
  `pay_number` varchar(20) NOT NULL COMMENT '财务应付单号',
  `pay_type` tinyint(3) NOT NULL COMMENT '实付类型;1为代销借货；2为成品采购；3石包采购；',
  `prc_id` int(10) NOT NULL COMMENT '供货商ID',
  `prc_name` varchar(30) NOT NULL COMMENT '供货商',
  `company` int(10) NOT NULL COMMENT '所属公司',
  `bank_name` varchar(150) NOT NULL COMMENT '银行名称',
  `bank_serial_number` varchar(30) NOT NULL COMMENT '银行交易流水',
  `bank_account` varchar(20) NOT NULL COMMENT '收款方帐号',
  `pay_time` date NOT NULL COMMENT '财务付款时间',
  `total` decimal(10,2) NOT NULL COMMENT '实付金额',
  `make_time` datetime NOT NULL COMMENT '操作时间',
  `make_name` varchar(20) NOT NULL COMMENT '制单人',
  PRIMARY KEY (`pay_real_number`)
) ENGINE=MyISAM AUTO_INCREMENT=1000073 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pay_should
-- ----------------------------
DROP TABLE IF EXISTS `pay_should`;
CREATE TABLE `pay_should` (
  `pay_number_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '财务应付ID',
  `pay_type` int(1) NOT NULL COMMENT '应付类型',
  `prc_id` int(10) NOT NULL COMMENT '供货商ID',
  `prc_name` varchar(30) NOT NULL COMMENT '供货商',
  `settle_mode` varchar(30) NOT NULL COMMENT '结算方式',
  `company` int(10) NOT NULL COMMENT '所属公司',
  `make_time` datetime NOT NULL COMMENT '制单时间',
  `make_name` varchar(20) NOT NULL COMMENT '制单人',
  `check_time` datetime NOT NULL COMMENT '审核时间',
  `check_name` varchar(20) NOT NULL COMMENT '审核人',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '单据状态(1、待审核；2、已审核；3、已取消',
  `pay_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '付款状态：1、未付款；2、部分付款；3、已付款)',
  `total_cope` decimal(10,2) NOT NULL COMMENT '应付金额',
  `total_real` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '财务实付金额',
  `pay_should_all_name` varchar(20) NOT NULL DEFAULT 'CWYF' COMMENT '财务应付单号',
  PRIMARY KEY (`pay_number_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10025 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pay_should_detail
-- ----------------------------
DROP TABLE IF EXISTS `pay_should_detail`;
CREATE TABLE `pay_should_detail` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pay_number` int(10) NOT NULL COMMENT '财务应付单ID',
  `pay_apply_number` varchar(20) NOT NULL COMMENT '应付申请单单号',
  `total_cope` decimal(10,2) NOT NULL COMMENT '应付金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pay_yf_real
-- ----------------------------
DROP TABLE IF EXISTS `pay_yf_real`;
CREATE TABLE `pay_yf_real` (
  `pay_real_number` int(10) NOT NULL AUTO_INCREMENT COMMENT '财务实付单号',
  `pay_real_all_name` varchar(20) DEFAULT NULL COMMENT '全名',
  `pay_number` varchar(20) NOT NULL COMMENT '财务应付单号',
  `pay_type` tinyint(3) NOT NULL COMMENT '实付类型;1为代销借货；2为成品采购；3石包采购；',
  `prc_id` int(10) NOT NULL COMMENT '供货商ID',
  `prc_name` varchar(30) NOT NULL COMMENT '供货商',
  `company` int(10) NOT NULL COMMENT '所属公司',
  `bank_name` varchar(150) NOT NULL COMMENT '银行名称',
  `bank_serial_number` varchar(30) NOT NULL COMMENT '银行交易流水',
  `bank_account` varchar(20) NOT NULL COMMENT '收款方帐号',
  `pay_time` date NOT NULL COMMENT '财务付款时间',
  `total` decimal(10,2) NOT NULL COMMENT '实付金额',
  `make_time` datetime NOT NULL COMMENT '操作时间',
  `make_name` varchar(20) NOT NULL COMMENT '制单人',
  PRIMARY KEY (`pay_real_number`)
) ENGINE=MyISAM AUTO_INCREMENT=1000075 DEFAULT CHARSET=utf8;
