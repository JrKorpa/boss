/*
Navicat MySQL Data Transfer

Source Server         : mysql_192.168.0.95_3306
Source Server Version : 50626
Source Host           : 192.168.0.95:3306
Source Database       : finance

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2019-09-09 10:22:01
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for app_apply_balance
-- ----------------------------
DROP TABLE IF EXISTS `app_apply_balance`;
CREATE TABLE `app_apply_balance` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `balance_no` char(16) NOT NULL COMMENT '应付单号',
  `apply_array` varchar(200) DEFAULT NULL COMMENT '对应申请单[数组]',
  `supplier_id` int(10) unsigned DEFAULT NULL COMMENT '结算商ID',
  `supplier_name` varchar(100) DEFAULT NULL COMMENT '供应商名称',
  `total_sys` decimal(12,2) DEFAULT NULL COMMENT '系统金额',
  `total_dev` decimal(12,2) DEFAULT NULL COMMENT '调整金额',
  `total_real` decimal(12,2) DEFAULT NULL COMMENT '应付金额',
  `pay_total` decimal(12,2) DEFAULT '0.00' COMMENT '已付金额',
  `pay_type` varchar(20) DEFAULT NULL COMMENT '应付类型',
  `balance_status` tinyint(4) DEFAULT NULL COMMENT '单据状态:1=待审核,2=已审核,3=已取消',
  `pay_status` tinyint(4) DEFAULT NULL COMMENT '付款状态:1=未付款,2=部分付款,3=已付款',
  `create_id` int(10) unsigned DEFAULT NULL COMMENT '制单人ID',
  `create_name` varchar(20) DEFAULT NULL COMMENT '制单人',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '制单时间',
  `check_id` int(10) unsigned DEFAULT NULL,
  `check_name` varchar(20) DEFAULT NULL,
  `check_time` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='财务结算单';

-- ----------------------------
-- Table structure for app_apply_bills
-- ----------------------------
DROP TABLE IF EXISTS `app_apply_bills`;
CREATE TABLE `app_apply_bills` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `apply_no` char(16) DEFAULT NULL COMMENT '申请单号',
  `pay_number` varchar(20) DEFAULT NULL COMMENT '应付单号',
  `detail_array` varchar(200) DEFAULT NULL COMMENT '对应明细:明细ID用，隔开',
  `pay_type` tinyint(4) DEFAULT NULL COMMENT '应付类型:1=代销,2=成本采购,3=石包采购',
  `bills_type` tinyint(4) DEFAULT NULL COMMENT '单据状态:1/新增2/待审核3/已驳回4/已取消5/待生成6/已生成',
  `supplier_id` int(11) DEFAULT NULL COMMENT '结算商ID',
  `pay_total` decimal(12,2) DEFAULT NULL COMMENT '应付金额(系统统计)',
  `adjust_total` decimal(10,2) DEFAULT NULL COMMENT '调整金额',
  `apply_total` decimal(12,2) DEFAULT NULL COMMENT '申请金额 (系统t统计-调整)',
  `invoice_no` varchar(30) DEFAULT NULL COMMENT '发票号码',
  `create_id` int(11) DEFAULT NULL COMMENT '制单人ID',
  `create_name` varchar(20) DEFAULT NULL COMMENT '制单人',
  `create_time` int(11) DEFAULT NULL COMMENT '制单时间',
  `check_id` int(11) DEFAULT NULL COMMENT '审核人ID',
  `check_name` varchar(20) DEFAULT NULL COMMENT '审核人',
  `check_time` int(11) DEFAULT NULL COMMENT '审核时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应付申请单';

-- ----------------------------
-- Table structure for app_apply_pay_log
-- ----------------------------
DROP TABLE IF EXISTS `app_apply_pay_log`;
CREATE TABLE `app_apply_pay_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `order_type` tinyint(4) DEFAULT NULL COMMENT '单据类型',
  `order_id` int(11) DEFAULT NULL COMMENT '单据ID',
  `order_no` char(16) DEFAULT NULL COMMENT '单据号码',
  `handle_type` tinyint(4) DEFAULT NULL COMMENT '1=调整,2审批,3=付款',
  `content` varchar(200) DEFAULT NULL COMMENT '操作内容',
  `create_id` int(11) DEFAULT NULL COMMENT '操作人ID',
  `create_name` varchar(20) DEFAULT NULL COMMENT '操作人',
  `create_time` int(11) DEFAULT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应付操作日志';

-- ----------------------------
-- Table structure for app_apply_real_pay
-- ----------------------------
DROP TABLE IF EXISTS `app_apply_real_pay`;
CREATE TABLE `app_apply_real_pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `real_number` char(16) DEFAULT NULL COMMENT '实付单号',
  `apply_no` char(16) DEFAULT NULL COMMENT '应付单ID',
  `bank_name` varchar(50) DEFAULT NULL COMMENT '银行名称',
  `bank_serial` varchar(50) DEFAULT NULL COMMENT '银行流水号',
  `account_name` varchar(20) DEFAULT NULL COMMENT '收款方户名',
  `bank_account` varchar(50) DEFAULT NULL COMMENT '收款方账户',
  `pay_time` char(10) DEFAULT NULL COMMENT '付款时间',
  `supplier_id` int(11) DEFAULT NULL COMMENT '结算商ID',
  `supplier_name` varchar(50) DEFAULT NULL COMMENT '供应商名称',
  `pay_total` decimal(12,2) DEFAULT NULL COMMENT '实付金额',
  `create_id` int(11) DEFAULT NULL COMMENT '创建人ID',
  `create_name` varchar(20) DEFAULT NULL COMMENT '创建人',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='财务实付表';

-- ----------------------------
-- Table structure for app_deal_detail
-- ----------------------------
DROP TABLE IF EXISTS `app_deal_detail`;
CREATE TABLE `app_deal_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `detail_type` tinyint(4) NOT NULL COMMENT '明细类型：1=代销,2=成品,3=石包',
  `serial_number` char(16) DEFAULT NULL COMMENT '流水号',
  `goods_no` varchar(50) DEFAULT NULL COMMENT '货号/单据编号',
  `goods_type` varchar(30) DEFAULT NULL COMMENT '货品分类/单据分类',
  `goods_status` smallint(6) unsigned DEFAULT NULL COMMENT '货品状态',
  `certficate_no` varchar(50) DEFAULT NULL COMMENT '证书号',
  `put_in_type` tinyint(4) unsigned DEFAULT NULL COMMENT '入库方式',
  `make_time` datetime DEFAULT NULL COMMENT '入库制单时间',
  `check_time` datetime DEFAULT NULL COMMENT '入库审核时间',
  `supplier_id` varchar(50) DEFAULT NULL COMMENT '供货商ID',
  `supplier_name` varchar(50) DEFAULT NULL COMMENT '供应商名称',
  `supplier_order` varchar(50) DEFAULT NULL COMMENT '供货商单号',
  `company_id` int(10) unsigned DEFAULT NULL COMMENT '所属公司',
  `pay_cont` varchar(100) DEFAULT NULL COMMENT '支付内容',
  `amount_total` decimal(12,2) DEFAULT NULL COMMENT '单据金额',
  `apply_status` tinyint(4) DEFAULT '1' COMMENT '应付申请状态',
  `apply_id` int(10) unsigned DEFAULT NULL COMMENT '申请单ID',
  `apply_number` varchar(50) DEFAULT NULL COMMENT '应付申请单号',
  `oldsys_id` int(10) unsigned DEFAULT NULL COMMENT '旧系统主键',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应付明细表';

-- ----------------------------
-- Table structure for app_jiezhang
-- ----------------------------
DROP TABLE IF EXISTS `app_jiezhang`;
CREATE TABLE `app_jiezhang` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `qihao` tinyint(3) NOT NULL DEFAULT '0' COMMENT '期号（1-12）',
  `start_time` date NOT NULL COMMENT '开始日期',
  `end_time` date NOT NULL COMMENT '结束日期',
  `year` char(4) NOT NULL COMMENT '会计年度',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_order_pay_action
-- ----------------------------
DROP TABLE IF EXISTS `app_order_pay_action`;
CREATE TABLE `app_order_pay_action` (
  `pay_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) NOT NULL COMMENT '订单Id',
  `order_sn` varchar(20) NOT NULL COMMENT '订单号',
  `order_time` datetime NOT NULL COMMENT '下单时间',
  `order_amount` decimal(10,2) NOT NULL COMMENT '订单金额',
  `deposit` decimal(10,2) NOT NULL COMMENT '提报金额',
  `balance` decimal(10,2) NOT NULL COMMENT '剩余金额',
  `attach_sn` varchar(100) DEFAULT NULL COMMENT '淘宝代付款流水号',
  `remark` text COMMENT '备注',
  `pay_time` datetime NOT NULL COMMENT '支付时间',
  `pay_type` varchar(100) DEFAULT NULL COMMENT '支付方式',
  `order_consignee` varchar(60) NOT NULL COMMENT '顾客',
  `pay_account` varchar(100) DEFAULT NULL COMMENT '银行卡账户',
  `pay_sn` varchar(100) DEFAULT NULL,
  `proof_sn` varchar(100) DEFAULT NULL COMMENT '凭证号',
  `leader` varchar(20) DEFAULT NULL COMMENT '审批人',
  `leader_check` datetime DEFAULT NULL COMMENT '审核时间',
  `opter_name` varchar(20) DEFAULT NULL COMMENT '操作人',
  `department` int(5) NOT NULL COMMENT '支付地点',
  `status` tinyint(3) NOT NULL COMMENT '1=未提报，2=已提报，3=已审核，4=有问题',
  `pay_checker` varchar(20) DEFAULT NULL COMMENT '财务审核人',
  `pay_check_time` date DEFAULT NULL COMMENT '财务审核时间',
  `system_flg` tinyint(3) DEFAULT '0' COMMENT '系统识别,0展厅,1ecshop后台',
  `zhuandan_sn` varchar(30) DEFAULT NULL COMMENT '转单流水号',
  `create_date` varchar(30) DEFAULT NULL COMMENT '验证收银提报重复',
  `is_type` tinyint(1) DEFAULT '1' COMMENT '记录是收款还是退款：1收款，2退款',
  `out_order_sn` varchar(30) DEFAULT '0' COMMENT '外部订单',
  PRIMARY KEY (`pay_id`),
  KEY `order_sn` (`order_sn`),
  KEY `order_id` (`order_id`),
  KEY `pay_time` (`pay_time`)
) ENGINE=InnoDB AUTO_INCREMENT=199921 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_pay_apply
-- ----------------------------
DROP TABLE IF EXISTS `app_pay_apply`;
CREATE TABLE `app_pay_apply` (
  `apply_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '申请单ID',
  `pay_apply_number` varchar(20) NOT NULL DEFAULT 'YFSQ' COMMENT '应付申请单号',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态1/新增2/待审核3/已驳回4/已取消5/待生成应付单6/已生成应付单',
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
  `record_type` int(2) NOT NULL COMMENT '单据类型(1、应付申请单；2、应付调整单)',
  `overrule_reason` varchar(100) DEFAULT '' COMMENT '调整单的驳回原因',
  `fapiao` varchar(40) DEFAULT NULL COMMENT '发票',
  PRIMARY KEY (`apply_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='付款申请单据表';

-- ----------------------------
-- Table structure for app_pay_apply_goods
-- ----------------------------
DROP TABLE IF EXISTS `app_pay_apply_goods`;
CREATE TABLE `app_pay_apply_goods` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应付申请商品详情';

-- ----------------------------
-- Table structure for app_pay_operat_log
-- ----------------------------
DROP TABLE IF EXISTS `app_pay_operat_log`;
CREATE TABLE `app_pay_operat_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `related_id` int(10) NOT NULL COMMENT '关联ID',
  `type` int(2) NOT NULL DEFAULT '1' COMMENT '分类( 1、应付申请单)',
  `operat_name` varchar(20) NOT NULL COMMENT '操作人',
  `operat_time` datetime NOT NULL COMMENT '操作时间',
  `operat_content` varchar(100) NOT NULL COMMENT '操作内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应付操作日志表';

-- ----------------------------
-- Table structure for app_pay_real
-- ----------------------------
DROP TABLE IF EXISTS `app_pay_real`;
CREATE TABLE `app_pay_real` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='实付表';

-- ----------------------------
-- Table structure for app_pay_should
-- ----------------------------
DROP TABLE IF EXISTS `app_pay_should`;
CREATE TABLE `app_pay_should` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应付表';

-- ----------------------------
-- Table structure for app_pay_should_detail
-- ----------------------------
DROP TABLE IF EXISTS `app_pay_should_detail`;
CREATE TABLE `app_pay_should_detail` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pay_number` int(10) NOT NULL COMMENT '财务应付单ID',
  `pay_apply_number` varchar(20) NOT NULL COMMENT '应付申请单单号',
  `total_cope` decimal(10,2) NOT NULL COMMENT '应付金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='应付详情表';

-- ----------------------------
-- Table structure for app_receipt_deposit
-- ----------------------------
DROP TABLE IF EXISTS `app_receipt_deposit`;
CREATE TABLE `app_receipt_deposit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL COMMENT '订单号',
  `receipt_sn` varchar(50) NOT NULL COMMENT '定金收据号',
  `customer` varchar(30) NOT NULL COMMENT '客户名称',
  `department` int(4) NOT NULL COMMENT '部门id',
  `pay_fee` decimal(10,2) NOT NULL COMMENT '支付金额',
  `pay_type` varchar(20) NOT NULL COMMENT '支付类型',
  `card_no` varchar(30) DEFAULT NULL COMMENT '卡号',
  `card_voucher` varchar(30) DEFAULT NULL COMMENT '刷卡凭证',
  `pay_time` date NOT NULL COMMENT '收款时间',
  `status` tinyint(1) NOT NULL COMMENT '状态：1有效，2点款，3作废',
  `print_num` tinyint(2) NOT NULL COMMENT '打印次数',
  `pay_user` varchar(50) NOT NULL COMMENT '收款人',
  `remark` varchar(50) NOT NULL COMMENT '备注',
  `add_time` datetime NOT NULL,
  `add_user` varchar(20) NOT NULL COMMENT '操作人',
  `zuofei_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_sn` (`receipt_sn`),
  KEY `order_sn` (`order_sn`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=395 DEFAULT CHARSET=utf8 COMMENT='定金收据信息';

-- ----------------------------
-- Table structure for app_receipt_deposit_log
-- ----------------------------
DROP TABLE IF EXISTS `app_receipt_deposit_log`;
CREATE TABLE `app_receipt_deposit_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `receipt_id` int(10) NOT NULL COMMENT '定金收据id',
  `receipt_action` varchar(20) NOT NULL COMMENT '操作',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `add_user` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=964 DEFAULT CHARSET=utf8 COMMENT='定金收据操作日志';

-- ----------------------------
-- Table structure for app_receipt_pay
-- ----------------------------
DROP TABLE IF EXISTS `app_receipt_pay`;
CREATE TABLE `app_receipt_pay` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL COMMENT '订单号',
  `receipt_sn` varchar(50) NOT NULL COMMENT '收据号',
  `customer` varchar(30) NOT NULL COMMENT '客户名称',
  `department` int(4) NOT NULL COMMENT '部门id',
  `pay_fee` decimal(9,2) NOT NULL COMMENT '支付金额',
  `pay_type` varchar(20) NOT NULL COMMENT '支付类型',
  `card_no` varchar(30) NOT NULL COMMENT '卡号',
  `card_voucher` varchar(30) NOT NULL COMMENT '刷卡凭证',
  `pay_time` date NOT NULL COMMENT '收款时间',
  `status` tinyint(1) NOT NULL COMMENT '状态：1有效，2作废',
  `print_num` tinyint(2) NOT NULL COMMENT '打印次数',
  `pay_user` varchar(30) NOT NULL COMMENT '收款人',
  `remark` text NOT NULL COMMENT '备注',
  `add_time` datetime NOT NULL,
  `add_user` varchar(30) NOT NULL COMMENT '操作人',
  `zuofei_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `receipt_sn` (`receipt_sn`),
  KEY `order_sn` (`order_sn`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=173086 DEFAULT CHARSET=utf8 COMMENT='收据信息';

-- ----------------------------
-- Table structure for app_receipt_pay_log
-- ----------------------------
DROP TABLE IF EXISTS `app_receipt_pay_log`;
CREATE TABLE `app_receipt_pay_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `receipt_id` int(10) NOT NULL COMMENT '收据id',
  `receipt_action` varchar(20) NOT NULL COMMENT '操作',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  `add_user` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=267767 DEFAULT CHARSET=utf8 COMMENT='收据操作日志';

-- ----------------------------
-- Table structure for app_receive_apply
-- ----------------------------
DROP TABLE IF EXISTS `app_receive_apply`;
CREATE TABLE `app_receive_apply` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '应收申请ID',
  `apply_number` varchar(20) NOT NULL COMMENT '应收申请单号',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '应收申请状态：1、新增，2、待审核、3、已驳回、4、已取消、5、待生成应收单，6、已生成应收单',
  `should_number` varchar(20) NOT NULL COMMENT '财务应收单单号',
  `from_ad` varchar(20) NOT NULL COMMENT '订单来源',
  `cash_type` int(2) NOT NULL DEFAULT '1' COMMENT '收款类型：1、销售收款，2、退货退款',
  `make_time` datetime NOT NULL COMMENT '制单时间',
  `make_name` varchar(20) NOT NULL COMMENT '制单人',
  `check_time` datetime NOT NULL COMMENT '审核时间',
  `check_name` varchar(20) NOT NULL COMMENT '审核人',
  `amount` int(10) NOT NULL COMMENT '总数量',
  `total` decimal(10,2) NOT NULL COMMENT '应收总金额',
  `external_total_all` decimal(10,2) NOT NULL COMMENT '外部总金额',
  `kela_total_all` decimal(10,2) NOT NULL COMMENT 'BDD金额',
  `jxc_total_all` decimal(10,2) NOT NULL COMMENT '销账金额',
  `check_sale_number` varchar(20) NOT NULL COMMENT '核销单单号',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应收申请单单据表';

-- ----------------------------
-- Table structure for app_receive_apply_detail
-- ----------------------------
DROP TABLE IF EXISTS `app_receive_apply_detail`;
CREATE TABLE `app_receive_apply_detail` (
  `detail_id` int(10) NOT NULL AUTO_INCREMENT,
  `apply_id` int(10) NOT NULL COMMENT '申请单ID',
  `replytime` date NOT NULL COMMENT '对账日期',
  `external_sn` varchar(50) NOT NULL COMMENT '外部订单号',
  `kela_sn` varchar(20) NOT NULL COMMENT 'BDD订单号',
  `external_total` decimal(10,2) NOT NULL COMMENT '订单金额/退款金额(外部金额)',
  `pay_xj` decimal(10,2) NOT NULL COMMENT '客户支付信息--现金支付',
  `pay_jf` decimal(10,2) NOT NULL COMMENT '客户支付信息--平台积分支付',
  `pay_pt_yhq` decimal(10,2) NOT NULL COMMENT '客户支付信息--平台优惠券支付',
  `pay_kela_yhq` decimal(10,2) NOT NULL COMMENT '客户支付信息--BDD优惠券支付',
  `f_koudian` decimal(10,2) NOT NULL COMMENT '费用--扣点',
  `f_yongjin` decimal(10,2) NOT NULL COMMENT '费用--佣金',
  `f_jingdong` decimal(10,2) NOT NULL COMMENT '费用--京豆、京券',
  `f_yunfei` decimal(10,2) NOT NULL COMMENT '费用--运费',
  `f_peifu` decimal(10,2) NOT NULL COMMENT '费用--卖家赔付',
  `f_chajia` decimal(10,2) NOT NULL COMMENT '费用--退差价',
  `f_youhui` decimal(10,2) NOT NULL COMMENT '费用--活动优惠',
  `f_weiyue` decimal(10,2) NOT NULL COMMENT '费用--违约罚款',
  `f_qita` decimal(10,2) NOT NULL COMMENT '费用--其他',
  `sy_fanyou` decimal(10,2) NOT NULL COMMENT '收益--反邮',
  `sy_qita` decimal(10,2) NOT NULL COMMENT '收益--其他',
  `total` decimal(10,2) NOT NULL COMMENT '应收现金',
  `reoverrule_reason` varchar(100) NOT NULL COMMENT '驳回原因',
  PRIMARY KEY (`detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_receive_operat_log
-- ----------------------------
DROP TABLE IF EXISTS `app_receive_operat_log`;
CREATE TABLE `app_receive_operat_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `related_id` int(10) NOT NULL COMMENT '关联ID',
  `type` int(2) NOT NULL DEFAULT '1' COMMENT '分类( 1、应付申请单)',
  `operat_name` varchar(20) NOT NULL COMMENT '操作人',
  `operat_time` datetime NOT NULL COMMENT '操作时间',
  `operat_content` varchar(100) NOT NULL COMMENT '操作内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='应付操作日志表';

-- ----------------------------
-- Table structure for app_receive_real
-- ----------------------------
DROP TABLE IF EXISTS `app_receive_real`;
CREATE TABLE `app_receive_real` (
  `real_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '实收单ID',
  `real_number` varchar(20) NOT NULL COMMENT '实收单单号',
  `from_ad` varchar(20) NOT NULL COMMENT '订单来源',
  `should_number` varchar(20) NOT NULL COMMENT '应收单单号',
  `bank_name` varchar(200) NOT NULL COMMENT '银行名称',
  `bank_serial_number` varchar(30) NOT NULL COMMENT '银行交易流水号',
  `total` decimal(10,2) NOT NULL COMMENT '实收金额',
  `pay_tiime` datetime NOT NULL COMMENT '财务收款时间',
  `maketime` datetime NOT NULL COMMENT '制单时间',
  `makename` varchar(30) NOT NULL COMMENT '制单人',
  PRIMARY KEY (`real_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_receive_should
-- ----------------------------
DROP TABLE IF EXISTS `app_receive_should`;
CREATE TABLE `app_receive_should` (
  `should_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '应收单ID',
  `should_number` varchar(20) NOT NULL COMMENT '应收单单号',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '应收单状态：1、待审核，2、已审核，3、已取消',
  `total_status` int(2) NOT NULL DEFAULT '1' COMMENT '收款状态：1、未付款，2、部分付款，3、已付款',
  `from_ad` varchar(15) NOT NULL COMMENT '订单来源',
  `total_cope` decimal(10,2) NOT NULL COMMENT '应收金额',
  `total_real` decimal(10,2) NOT NULL COMMENT '实收金额',
  `maketime` datetime NOT NULL COMMENT '制单时间',
  `makename` varchar(20) NOT NULL COMMENT '制单人',
  `checktime` datetime NOT NULL COMMENT '审核时间',
  `checkname` varchar(20) NOT NULL COMMENT '审核人',
  PRIMARY KEY (`should_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_receive_should_detail
-- ----------------------------
DROP TABLE IF EXISTS `app_receive_should_detail`;
CREATE TABLE `app_receive_should_detail` (
  `detail_id` int(10) NOT NULL AUTO_INCREMENT,
  `should_id` int(10) NOT NULL COMMENT '应收单对应ID',
  `apply_number` varchar(20) NOT NULL COMMENT '应收申请单单号',
  `total_cope` decimal(10,2) NOT NULL COMMENT '应收金额',
  PRIMARY KEY (`detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for base_invoice_info
-- ----------------------------
DROP TABLE IF EXISTS `base_invoice_info`;
CREATE TABLE `base_invoice_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `invoice_num` varchar(35) NOT NULL COMMENT '发票号',
  `price` decimal(10,2) NOT NULL COMMENT '发票价格',
  `title` varchar(100) NOT NULL COMMENT '抬头',
  `content` text NOT NULL COMMENT '内容',
  `status` tinyint(1) NOT NULL COMMENT '状态:1未使用2已使用3已作废',
  `create_user` varchar(50) NOT NULL COMMENT '创建人',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `use_user` varchar(50) DEFAULT NULL COMMENT '使用人',
  `use_time` datetime DEFAULT NULL COMMENT '使用时间',
  `cancel_user` varchar(50) DEFAULT NULL COMMENT '作废人',
  `cancel_time` datetime DEFAULT NULL COMMENT '作废时间',
  `order_sn` varchar(20) DEFAULT NULL COMMENT '订单号',
  `type` tinyint(1) NOT NULL COMMENT '类型 1客订单',
  PRIMARY KEY (`id`),
  KEY `invoice_num` (`invoice_num`),
  KEY `order_sn` (`order_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=104851 DEFAULT CHARSET=utf8 COMMENT='发票表';

-- ----------------------------
-- Table structure for express
-- ----------------------------
DROP TABLE IF EXISTS `express`;
CREATE TABLE `express` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `exp_name` varchar(100) DEFAULT NULL COMMENT '快递名称',
  `exp_code` varchar(50) DEFAULT NULL COMMENT '快递编码',
  `exp_areas` varchar(100) DEFAULT NULL COMMENT '配送区域',
  `exp_tel` char(20) DEFAULT NULL COMMENT '联系电话',
  `exp_note` varchar(200) DEFAULT NULL COMMENT '备注说明',
  `is_deleted` tinyint(4) DEFAULT '0' COMMENT '删除标识',
  `addby_id` int(11) DEFAULT NULL COMMENT '创建人',
  `add_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `oldsys_id` int(10) unsigned DEFAULT NULL COMMENT '旧系统Id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COMMENT='快递公司';

-- ----------------------------
-- Table structure for goods
-- ----------------------------
DROP TABLE IF EXISTS `goods`;
CREATE TABLE `goods` (
  `serial_number` int(10) NOT NULL AUTO_INCREMENT COMMENT '流水号',
  `item_id` varchar(20) NOT NULL COMMENT '货号/单号',
  `order_id` int(11) DEFAULT NULL COMMENT '订单ID',
  `zhengshuhao` varchar(100) NOT NULL DEFAULT '' COMMENT '证书号（代销借货用）',
  `goods_status` int(4) NOT NULL DEFAULT '1' COMMENT '0=初始化，1=库存，2=已销售，3=转仓中，4=盘点中，5=销售中，6=冻结，7=已返厂,8=退货中，9=返厂中, 10=作废, 11=损益中,12=已报损',
  `item_type` varchar(20) NOT NULL COMMENT '分类/单据类型',
  `company` int(10) DEFAULT NULL COMMENT '所属公司',
  `prc_id` int(4) NOT NULL COMMENT '供货商ID',
  `prc_name` varchar(30) NOT NULL COMMENT '供货商',
  `prc_num` varchar(30) NOT NULL DEFAULT '' COMMENT '供货商单号',
  `type` int(2) NOT NULL DEFAULT '1' COMMENT '1代销、2成品、3石包',
  `pay_content` varchar(20) NOT NULL DEFAULT '' COMMENT '支付内容(收货单)',
  `storage_mode` int(2) NOT NULL COMMENT '入库方式 0=默认 1=购买 2=委托加工 3=代销 4=借入',
  `make_time` datetime NOT NULL COMMENT '入库制单时间',
  `check_time` datetime NOT NULL COMMENT '入库审核时间',
  `total` decimal(10,2) NOT NULL COMMENT '采购成本/单据金额',
  `pay_apply_status` int(4) NOT NULL DEFAULT '1' COMMENT '应付申请状态（1=>待申请，2=>待审核，3=>已驳回，4=>已审核）',
  `pay_apply_number` varchar(20) NOT NULL DEFAULT '' COMMENT '应付申请单号',
  `add_time` datetime NOT NULL COMMENT '添加时间',
  PRIMARY KEY (`serial_number`),
  KEY `item_id` (`item_id`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=69477 DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for jxs_area_scope
-- ----------------------------
DROP TABLE IF EXISTS `jxs_area_scope`;
CREATE TABLE `jxs_area_scope` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `jxs_id` int(5) NOT NULL COMMENT '经销商id',
  `country_id` int(5) NOT NULL,
  `province_id` int(5) NOT NULL COMMENT '0=全部',
  `city_id` int(5) NOT NULL COMMENT '0=全部',
  `region_id` int(5) NOT NULL COMMENT '0=全部',
  `create_time` datetime NOT NULL,
  `create_user` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='经销商所辖区域';

-- ----------------------------
-- Table structure for jxs_order
-- ----------------------------
DROP TABLE IF EXISTS `jxs_order`;
CREATE TABLE `jxs_order` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jxs_id` int(10) NOT NULL,
  `batch_id` int(10) NOT NULL COMMENT '导入的批次',
  `order_id` int(10) NOT NULL COMMENT '订单号',
  `order_sn` varchar(20) NOT NULL COMMENT '订单编号',
  `department_id` varchar(20) DEFAULT NULL COMMENT '订单部门',
  `create_time` datetime NOT NULL COMMENT '制单时间',
  `send_goods_time` datetime DEFAULT NULL COMMENT '发货时间',
  `item_count` int(10) DEFAULT NULL COMMENT '订单商品数量',
  `order_amount` decimal(10,2) DEFAULT NULL COMMENT '订单金额',
  `order_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单审核状态1无效2已审核3取消4关闭',
  `address` varchar(1024) DEFAULT NULL COMMENT '收货地址',
  `country_id` int(10) DEFAULT '0' COMMENT '国家id',
  `province_id` int(10) DEFAULT NULL COMMENT '省份id',
  `city_id` int(10) DEFAULT NULL COMMENT '城市id',
  `region_id` int(10) DEFAULT NULL COMMENT '区域id',
  `calc_profit` decimal(10,2) DEFAULT NULL COMMENT '公式计算的让利额',
  `real_profit` decimal(10,2) DEFAULT NULL COMMENT '实际让利额',
  `calc_status` tinyint(4) DEFAULT '0' COMMENT '订单结算状态,0未结算，1结算',
  `calc_date` datetime DEFAULT NULL COMMENT '结算时间',
  `profit_id` int(10) DEFAULT NULL COMMENT '结算单id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx-order_id` (`order_id`) USING HASH
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for jxs_order_copy
-- ----------------------------
DROP TABLE IF EXISTS `jxs_order_copy`;
CREATE TABLE `jxs_order_copy` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jxs_id` int(10) NOT NULL,
  `batch_id` int(10) NOT NULL COMMENT '导入的批次',
  `order_id` int(10) NOT NULL COMMENT '订单号',
  `order_sn` varchar(20) NOT NULL COMMENT '订单编号',
  `department_id` varchar(20) DEFAULT NULL COMMENT '订单部门',
  `create_time` datetime NOT NULL COMMENT '制单时间',
  `send_goods_time` datetime DEFAULT NULL COMMENT '发货时间',
  `item_count` int(10) DEFAULT NULL COMMENT '订单商品数量',
  `order_amount` decimal(10,2) DEFAULT NULL COMMENT '订单金额',
  `order_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单审核状态1无效2已审核3取消4关闭',
  `address` varchar(1024) DEFAULT NULL COMMENT '收货地址',
  `country_id` int(10) DEFAULT '0' COMMENT '国家id',
  `province_id` int(10) DEFAULT NULL COMMENT '省份id',
  `city_id` int(10) DEFAULT NULL COMMENT '城市id',
  `region_id` int(10) DEFAULT NULL COMMENT '区域id',
  `calc_profit` decimal(10,2) DEFAULT NULL COMMENT '公式计算的让利额',
  `real_profit` decimal(10,2) DEFAULT NULL COMMENT '实际让利额',
  `calc_status` tinyint(4) DEFAULT '0' COMMENT '订单结算状态,0未结算，1结算',
  `calc_date` datetime DEFAULT NULL COMMENT '结算时间',
  `profit_id` int(10) DEFAULT NULL COMMENT '结算单id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx-order_id` (`order_id`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=33621 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for jxs_order_detail
-- ----------------------------
DROP TABLE IF EXISTS `jxs_order_detail`;
CREATE TABLE `jxs_order_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键',
  `batch_id` int(10) DEFAULT NULL COMMENT '批次',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单号',
  `goods_id` varchar(30) NOT NULL DEFAULT '' COMMENT '货号',
  `trading_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品最终成交价格',
  `cost_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品成本价格',
  `cart` varchar(60) DEFAULT NULL COMMENT '石重',
  `cut` varchar(60) DEFAULT NULL COMMENT '切工',
  `clarity` varchar(60) DEFAULT NULL COMMENT '净度',
  `color` varchar(60) DEFAULT NULL COMMENT '颜色',
  `jinzhong` decimal(10,3) DEFAULT NULL COMMENT '金重',
  `jinse` varchar(60) DEFAULT NULL COMMENT '金色',
  `caizhi` varchar(60) DEFAULT NULL COMMENT '材质',
  `goods_type` varchar(20) DEFAULT NULL COMMENT '商品类型lz:裸钻',
  `cat_type` smallint(4) DEFAULT NULL COMMENT '款式分类',
  `product_type` smallint(4) DEFAULT NULL COMMENT '产品线',
  `xiangkou` varchar(20) DEFAULT NULL COMMENT '镶口',
  `profit_type` tinyint(4) DEFAULT NULL COMMENT '利润类型',
  `calc_profit` decimal(10,2) DEFAULT NULL COMMENT '利润额',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for jxs_profit
-- ----------------------------
DROP TABLE IF EXISTS `jxs_profit`;
CREATE TABLE `jxs_profit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jxs_id` int(10) NOT NULL COMMENT '经销商',
  `created_date` datetime NOT NULL COMMENT '申请时间',
  `created_by` varchar(16) DEFAULT NULL COMMENT '申请人',
  `calc_profit` decimal(10,2) DEFAULT NULL COMMENT '结算金额',
  `calc_date` datetime DEFAULT NULL COMMENT '结算时间',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态,1已经结算 2取消结算',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=221 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
  `total_cope` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应付金额',
  `total_dev` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '偏差金额',
  `adj_reason` text COMMENT '调整原因（调整单的调整原因）',
  `record_type` int(2) NOT NULL DEFAULT '1' COMMENT '单据类型(1、应付申请单；2、应付调整单)',
  `overrule_reason` varchar(200) DEFAULT NULL COMMENT '调整单的驳回原因',
  `fapiao` varchar(100) DEFAULT NULL COMMENT '发票',
  `style_type` tinyint(2) DEFAULT NULL COMMENT '款式分类',
  `hidden` tinyint(1) DEFAULT '0' COMMENT '是否隐藏栏位',
  PRIMARY KEY (`apply_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1091 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pay_apply_goods
-- ----------------------------
DROP TABLE IF EXISTS `pay_apply_goods`;
CREATE TABLE `pay_apply_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `apply_id` int(10) NOT NULL COMMENT '应付申请ID',
  `serial_number` int(10) NOT NULL COMMENT '货流水号',
  `goods_id` varchar(30) NOT NULL COMMENT '货号/单号',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '系统金额',
  `total_cope` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应付金额',
  `total_dev` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '偏差金额',
  `dev_direction` varchar(50) DEFAULT NULL COMMENT '偏差说明',
  `overrule_reason` varchar(50) DEFAULT NULL COMMENT '驳回原因',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=24094 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pay_hexiao
-- ----------------------------
DROP TABLE IF EXISTS `pay_hexiao`;
CREATE TABLE `pay_hexiao` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '核销ID',
  `check_sale_number` varchar(20) NOT NULL COMMENT '核销单单号',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '1、新增，2、待审核，3、已审核，4、已驳回，5、已取消',
  `from_ad` varchar(20) NOT NULL COMMENT '订单来源',
  `order_num` int(6) NOT NULL COMMENT '单据数量',
  `goods_num` int(6) NOT NULL COMMENT '货品总数',
  `chengben` decimal(10,2) NOT NULL COMMENT '成本价',
  `shijia` decimal(10,2) NOT NULL COMMENT '销售价',
  `maketime` datetime NOT NULL COMMENT '制单时间',
  `makename` varchar(20) NOT NULL COMMENT '制单人',
  `checktime` datetime NOT NULL COMMENT '审核时间',
  `checkname` varchar(20) NOT NULL COMMENT '审核人',
  `apply_number` varchar(15) NOT NULL COMMENT '应收申请单单号',
  `cash_type` int(2) NOT NULL DEFAULT '1' COMMENT '收款类型：1、销售收款，2、退货退款',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pay_hexiao_detail
-- ----------------------------
DROP TABLE IF EXISTS `pay_hexiao_detail`;
CREATE TABLE `pay_hexiao_detail` (
  `detail_id` int(10) NOT NULL AUTO_INCREMENT,
  `hx_id` int(10) NOT NULL COMMENT '核销单ID',
  `jxc_order` varchar(12) NOT NULL,
  `type` varchar(6) NOT NULL COMMENT '单据类型：S、销售单，B、销售退货单',
  `goods_num` int(6) NOT NULL COMMENT '货品总数',
  `chengben` decimal(10,2) NOT NULL COMMENT '成本价',
  `shijia` decimal(10,2) NOT NULL COMMENT '销售价',
  `overrule_reason` varchar(50) DEFAULT NULL COMMENT '驳回原因 ',
  PRIMARY KEY (`detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pay_jxc_order
-- ----------------------------
DROP TABLE IF EXISTS `pay_jxc_order`;
CREATE TABLE `pay_jxc_order` (
  `order_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '销售出入库ID',
  `jxc_order` varchar(12) NOT NULL COMMENT '进销存单号',
  `kela_sn` varchar(20) NOT NULL COMMENT 'BDD订单号',
  `type` varchar(6) NOT NULL COMMENT '单据类型：S、销售单，B、销售退货单',
  `status` int(4) NOT NULL DEFAULT '1' COMMENT '状态：1、待核销，2、待审核，3、已审核，4、已驳回',
  `goods_num` int(6) NOT NULL COMMENT '单据货品数量',
  `chengben` decimal(10,2) NOT NULL COMMENT '成本价',
  `shijia` decimal(10,2) NOT NULL COMMENT '销售价',
  `addtime` datetime NOT NULL COMMENT '单据下单时间',
  `checktime` datetime NOT NULL COMMENT '单据审核时间',
  `hexiaotime` datetime NOT NULL COMMENT '核销时间',
  `hexiao_number` varchar(10) NOT NULL COMMENT '核销单号',
  `is_return` int(2) NOT NULL DEFAULT '0' COMMENT '是否回款：0、否，1、是',
  `returntime` datetime NOT NULL COMMENT '回款时间',
  `oldsys_id` int(10) unsigned DEFAULT NULL COMMENT '旧系统主键',
  PRIMARY KEY (`order_id`),
  KEY `kela_sn` (`kela_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pay_order_detail
-- ----------------------------
DROP TABLE IF EXISTS `pay_order_detail`;
CREATE TABLE `pay_order_detail` (
  `detail_id` int(10) NOT NULL AUTO_INCREMENT,
  `order_info_id` int(10) NOT NULL COMMENT '订单表的相关ID',
  `kela_sn` varchar(20) NOT NULL COMMENT 'BDD订单号（关联字段）',
  `detail_type` int(2) NOT NULL DEFAULT '1' COMMENT '1、销售记录，2、退款记录',
  `detail_total` decimal(10,2) NOT NULL COMMENT '金额',
  `detail_time` datetime NOT NULL COMMENT '下单时间、退款时间',
  PRIMARY KEY (`detail_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='记录BDD订单销售以及退款明细';

-- ----------------------------
-- Table structure for pay_order_info
-- ----------------------------
DROP TABLE IF EXISTS `pay_order_info`;
CREATE TABLE `pay_order_info` (
  `order_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '销售明细ID',
  `kela_sn` varchar(20) NOT NULL COMMENT 'BDD订单号',
  `external_sn` varchar(50) NOT NULL COMMENT '外部订单号',
  `make_order` varchar(30) NOT NULL COMMENT '制单人',
  `order_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '下单时间',
  `shipping_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '发货时间',
  `pay_id` int(6) NOT NULL COMMENT '支付方式ID',
  `pay_name` varchar(30) NOT NULL COMMENT '支付方式',
  `department` int(4) NOT NULL COMMENT '来源部门',
  `from_ad` varchar(20) NOT NULL COMMENT '订单来源',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '1、待申请，2、待审核、3已审核、4、已驳回、5、待提交',
  `apply_number` varchar(10) NOT NULL COMMENT '应收申请单',
  `addtime` datetime NOT NULL COMMENT '数据创建时间',
  `kela_total_all` decimal(10,2) NOT NULL COMMENT 'BDD金额',
  `jxc_total_all` decimal(10,2) NOT NULL COMMENT '销账金额',
  `external_total_all` decimal(10,2) NOT NULL COMMENT '外部金额',
  `oldsys_id` int(10) unsigned DEFAULT NULL COMMENT '旧系统主键',
  `pass_status` tinyint(1) unsigned DEFAULT '0' COMMENT '是否导入新系统',
  PRIMARY KEY (`order_id`),
  KEY `from_ad` (`from_ad`),
  KEY `department` (`department`),
  KEY `kela_sn` (`kela_sn`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pay_should
-- ----------------------------
DROP TABLE IF EXISTS `pay_should`;
CREATE TABLE `pay_should` (
  `pay_number_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '财务应付ID',
  `pay_type` int(1) NOT NULL COMMENT '应付类型',
  `prc_id` int(10) NOT NULL COMMENT '供货商ID',
  `prc_name` varchar(30) NOT NULL COMMENT '供货商',
  `settle_mode` int(4) NOT NULL COMMENT '结算方式 见数据字典',
  `company` int(10) NOT NULL COMMENT '所属公司',
  `make_time` datetime NOT NULL COMMENT '制单时间',
  `make_name` varchar(20) DEFAULT NULL COMMENT '制单人',
  `check_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '审核时间',
  `check_name` varchar(20) DEFAULT NULL COMMENT '审核人',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '单据状态(1、待审核；2、已审核；3、已取消',
  `pay_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '付款状态：1、未付款；2、部分付款；3、已付款)',
  `total_cope` decimal(10,2) NOT NULL COMMENT '应付金额',
  `total_real` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '财务实付金额',
  `pay_should_all_name` varchar(20) NOT NULL DEFAULT 'CWYF' COMMENT '财务应付单号',
  `hidden` tinyint(1) DEFAULT '0' COMMENT '是否隐藏栏位',
  PRIMARY KEY (`pay_number_id`)
) ENGINE=MyISAM AUTO_INCREMENT=796 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for pay_should_detail
-- ----------------------------
DROP TABLE IF EXISTS `pay_should_detail`;
CREATE TABLE `pay_should_detail` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pay_number` int(10) NOT NULL COMMENT '财务应付单ID',
  `pay_apply_number` varchar(20) NOT NULL COMMENT '应付申请单单号',
  `total_cope` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '应付金额',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1007 DEFAULT CHARSET=utf8;

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
  `bank_account` varchar(50) NOT NULL COMMENT '收款方帐号',
  `pay_time` date NOT NULL COMMENT '财务付款时间',
  `total` decimal(10,2) NOT NULL COMMENT '实付金额',
  `make_time` datetime NOT NULL COMMENT '操作时间',
  `make_name` varchar(20) NOT NULL COMMENT '制单人',
  `hidden` tinyint(1) DEFAULT '0' COMMENT '是否隐藏栏位',
  PRIMARY KEY (`pay_real_number`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Procedure structure for add_test
-- ----------------------------
DROP PROCEDURE IF EXISTS `add_test`;
DELIMITER ;;
CREATE DEFINER=`cuteman`@`%` PROCEDURE `add_test`()
BEGIN  
               DECLARE bill_no VARCHAR(50);  
               DECLARE pro_id VARCHAR(50);  
               DECLARE pro_name VARCHAR(50);  
               DECLARE send_goods_sn VARCHAR(50);  
               DECLARE pay_content VARCHAR(50);  
               DECLARE put_in_type VARCHAR(50);  
               DECLARE create_time VARCHAR(50);  
               DECLARE check_time VARCHAR(50);  
               DECLARE amount VARCHAR(50);  
               DECLARE str VARCHAR(300);  
               DECLARE x int;  
               DECLARE s int default 0;  
               DECLARE cursor_name CURSOR FOR 
select b.bill_no,p.pro_id,p.pro_name,b.send_goods_sn,p.pay_content,b.put_in_type,b.create_time,b.check_time,p.amount from warehouse_shipping.warehouse_bill_pay p inner JOIN warehouse_shipping.warehouse_bill b on p.bill_id = b.id and b.pro_id = p.pro_id 
where b.bill_type='L' AND b.bill_status = 2 AND p.pay_content not in (4,6) AND b.put_in_type IN(1,2) AND
NOT EXISTS(select * from goods g where g.prc_id = p.pro_id AND g.prc_name = p.pro_name and b.bill_no = g.item_id and p.pay_content = g.pay_content and g.type=2);  
               DECLARE CONTINUE HANDLER FOR SQLSTATE '02000' SET s=1;  
                set str = "--";  
                    OPEN cursor_name;  
                        fetch  cursor_name into bill_no,pro_id,pro_name,send_goods_sn,pay_content,put_in_type,create_time,check_time,amount;  
                        while s <> 1 do  
                                set str =  concat(str,x);  
                                INSERT INTO goods (`item_id`, `order_id`, `zhengshuhao`, `goods_status`, `item_type`, `company`, `prc_id`, `prc_name`, `prc_num`, `type`, `pay_content`, `storage_mode`, `make_time`, `check_time`, `total`, `pay_apply_status`, `pay_apply_number`, `add_time`) 
VALUES (bill_no, '0', '', '2', '1', '58', pro_id, pro_name, send_goods_sn, '2', pay_content, put_in_type, create_time, check_time, amount, '1', '', check_time);

                                fetch  cursor_name into bill_no,pro_id,pro_name,send_goods_sn,pay_content,put_in_type,create_time,check_time,amount;  
                          
                        end while;  
                     CLOSE cursor_name ;  
                select str;  
        END
;;
DELIMITER ;
