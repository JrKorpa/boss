/*
Navicat MySQL Data Transfer

Source Server         : mysql_192.168.0.95_3306
Source Server Version : 50626
Source Host           : 192.168.0.95:3306
Source Database       : kela_supplier

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2019-09-09 10:22:42
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for app_processor_audit
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_audit`;
CREATE TABLE `app_processor_audit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `record_id` int(10) unsigned DEFAULT NULL COMMENT '申请ID',
  `process_id` int(10) unsigned DEFAULT NULL COMMENT '流程ID',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '审批人ID',
  `audit_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未审，1通过，2驳回',
  `audit_time` int(11) NOT NULL DEFAULT '0' COMMENT '审批时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=284 DEFAULT CHARSET=utf8 COMMENT='供应商审核表';

-- ----------------------------
-- Table structure for app_processor_audit20160928
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_audit20160928`;
CREATE TABLE `app_processor_audit20160928` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `record_id` int(10) unsigned DEFAULT NULL COMMENT '申请ID',
  `process_id` int(10) unsigned DEFAULT NULL COMMENT '流程ID',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '审批人ID',
  `audit_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未审，1通过，2驳回',
  `audit_time` int(11) NOT NULL DEFAULT '0' COMMENT '审批时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=211 DEFAULT CHARSET=utf8 COMMENT='供应商审核表';

-- ----------------------------
-- Table structure for app_processor_audit_bak
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_audit_bak`;
CREATE TABLE `app_processor_audit_bak` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `record_id` int(10) unsigned DEFAULT NULL COMMENT '申请ID',
  `process_id` int(10) unsigned DEFAULT NULL COMMENT '流程ID',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT '审批人ID',
  `audit_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0未审，1通过，2驳回',
  `audit_time` int(11) NOT NULL DEFAULT '0' COMMENT '审批时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=168 DEFAULT CHARSET=utf8 COMMENT='供应商审核表';

-- ----------------------------
-- Table structure for app_processor_buyer
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_buyer`;
CREATE TABLE `app_processor_buyer` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `supplier_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `buyer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '采购人ID',
  `buyer_account` varchar(20) DEFAULT NULL COMMENT ' 采购人账户',
  `buyer_name` varchar(20) DEFAULT NULL COMMENT '姓名',
  `buyer_tel` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `buyer_papers` char(18) DEFAULT NULL COMMENT '身份证号',
  `create_id` int(11) unsigned DEFAULT NULL COMMENT '添加人',
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '添加时间',
  `is_deleted` tinyint(1) unsigned DEFAULT '0' COMMENT '删除标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COMMENT='供应商采购人';

-- ----------------------------
-- Table structure for app_processor_buyer_bak
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_buyer_bak`;
CREATE TABLE `app_processor_buyer_bak` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `supplier_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `buyer_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '采购人ID',
  `buyer_account` varchar(20) DEFAULT NULL COMMENT ' 采购人账户',
  `buyer_name` varchar(20) DEFAULT NULL COMMENT '姓名',
  `buyer_tel` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `buyer_papers` char(18) DEFAULT NULL COMMENT '身份证号',
  `create_id` int(11) unsigned DEFAULT NULL COMMENT '添加人',
  `create_time` int(11) unsigned DEFAULT NULL COMMENT '添加时间',
  `is_deleted` tinyint(1) unsigned DEFAULT '0' COMMENT '删除标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='供应商采购人';

-- ----------------------------
-- Table structure for app_processor_fee
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_fee`;
CREATE TABLE `app_processor_fee` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `processor_id` int(10) NOT NULL COMMENT '供应商id',
  `fee_type` tinyint(1) NOT NULL COMMENT '费用类型：1材质费，2超石工费，3表面工艺',
  `price` decimal(8,2) NOT NULL COMMENT '费用',
  `status` tinyint(1) NOT NULL COMMENT '状态：1启用2停用',
  `check_user` varchar(10) NOT NULL COMMENT '创建人',
  `check_time` datetime DEFAULT NULL COMMENT '创建时间',
  `cancel_time` datetime DEFAULT NULL COMMENT '停用时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='工厂费用';

-- ----------------------------
-- Table structure for app_processor_fee_bak
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_fee_bak`;
CREATE TABLE `app_processor_fee_bak` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `processor_id` int(10) NOT NULL COMMENT '供应商id',
  `fee_type` tinyint(1) NOT NULL COMMENT '费用类型：1材质费，2超石工费，3表面工艺',
  `price` decimal(8,2) NOT NULL COMMENT '费用',
  `status` tinyint(1) NOT NULL COMMENT '状态：1启用2停用',
  `check_user` varchar(10) NOT NULL COMMENT '创建人',
  `check_time` datetime DEFAULT NULL COMMENT '创建时间',
  `cancel_time` datetime DEFAULT NULL COMMENT '停用时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='工厂费用';

-- ----------------------------
-- Table structure for app_processor_group
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_group`;
CREATE TABLE `app_processor_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `supplier_id` int(10) DEFAULT NULL COMMENT '供应商ID',
  `group_id` int(10) NOT NULL COMMENT '分组ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8 COMMENT='供应商关联';

-- ----------------------------
-- Table structure for app_processor_group_bak
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_group_bak`;
CREATE TABLE `app_processor_group_bak` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `supplier_id` int(10) DEFAULT NULL COMMENT '供应商ID',
  `group_id` int(10) NOT NULL COMMENT '分组ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8 COMMENT='供应商关联';

-- ----------------------------
-- Table structure for app_processor_info
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_info`;
CREATE TABLE `app_processor_info` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL COMMENT '供应商编码',
  `name` varchar(100) NOT NULL COMMENT '供应商名称',
  `business_scope` varchar(200) NOT NULL DEFAULT '' COMMENT '经营范围：1:黄金,2:K金素金,3:PT素金,4:K金钻石镶嵌品,5:PT钻石镶嵌品,6:成品钻,7:彩宝饰品,8:银饰品,9:其他',
  `is_open` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开通系统:1是0否',
  `password` varchar(50) DEFAULT NULL COMMENT '密码',
  `business_license` varchar(50) DEFAULT '' COMMENT '营业执照号码',
  `tax_registry_no` varchar(50) DEFAULT '' COMMENT '税务登记证号',
  `business_license_region` varchar(200) DEFAULT '' COMMENT '营业执照地址:省，市，区',
  `business_license_address` varchar(200) DEFAULT NULL COMMENT '营业执照地址',
  `pro_region` varchar(200) DEFAULT '' COMMENT '取货地址:省，市，区',
  `pro_address` varchar(200) DEFAULT NULL COMMENT '取货地址',
  `cycle` varchar(30) DEFAULT '' COMMENT '出货周期',
  `pay_type` varchar(30) DEFAULT '0' COMMENT '结算方式：1现金,2转账,3支票',
  `tax_invoice` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT '增值税发票',
  `tax_point` varchar(255) DEFAULT NULL COMMENT '税点',
  `balance_type` tinyint(1) unsigned DEFAULT NULL COMMENT '付款周期：supplier_pay.method',
  `balance_day` varchar(20) DEFAULT NULL COMMENT '周期结算天数',
  `purchase_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购额度',
  `pro_contact` varchar(50) DEFAULT NULL COMMENT '公司联系人',
  `pro_phone` varchar(50) DEFAULT NULL COMMENT '公司联系电话',
  `pro_qq` varchar(50) DEFAULT '' COMMENT '公司联系qq',
  `contact` varchar(50) DEFAULT '' COMMENT 'BDD紧急联系人',
  `kela_phone` varchar(50) DEFAULT '' COMMENT 'BDD紧急联系电话',
  `kela_qq` varchar(50) DEFAULT NULL COMMENT 'BDD紧急联系qq',
  `bank_name` varchar(50) DEFAULT '' COMMENT '开户银行',
  `account_name` varchar(50) DEFAULT NULL COMMENT '户名',
  `account` varchar(50) DEFAULT '' COMMENT '银行账户',
  `is_invoice` tinyint(1) DEFAULT '0' COMMENT '此供应商是否有能力开发票:1开，0不开',
  `pro_email` varchar(50) DEFAULT NULL COMMENT '供货商邮箱',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1启用2停用',
  `create_id` int(10) unsigned DEFAULT NULL COMMENT '创建人ID',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(50) DEFAULT NULL COMMENT '创建人',
  `info` text COMMENT '备注',
  `pact_doc` varchar(255) DEFAULT NULL COMMENT '合同附件',
  `license_jpg` varchar(255) DEFAULT NULL COMMENT '营业执照附件',
  `tax_jpg` varchar(255) DEFAULT NULL COMMENT '税务登记证',
  `is_A_company` varchar(10) DEFAULT NULL COMMENT '是否A公司',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=640 DEFAULT CHARSET=utf8 COMMENT='供应商信息';

-- ----------------------------
-- Table structure for app_processor_info_bak
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_info_bak`;
CREATE TABLE `app_processor_info_bak` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(30) NOT NULL COMMENT '供应商编码',
  `name` varchar(100) NOT NULL COMMENT '供应商名称',
  `business_scope` varchar(200) NOT NULL DEFAULT '' COMMENT '经营范围：1:黄金,2:K金素金,3:PT素金,4:K金钻石镶嵌品,5:PT钻石镶嵌品,6:成品钻,7:彩宝饰品,8:银饰品,9:其他',
  `is_open` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开通系统:1是0否',
  `password` varchar(50) DEFAULT NULL COMMENT '密码',
  `business_license` varchar(50) DEFAULT '' COMMENT '营业执照号码',
  `tax_registry_no` varchar(50) DEFAULT '' COMMENT '税务登记证号',
  `business_license_region` varchar(200) DEFAULT '' COMMENT '营业执照地址:省，市，区',
  `business_license_address` varchar(200) DEFAULT NULL COMMENT '营业执照地址',
  `pro_region` varchar(200) DEFAULT '' COMMENT '取货地址:省，市，区',
  `pro_address` varchar(200) DEFAULT NULL COMMENT '取货地址',
  `cycle` varchar(30) DEFAULT '' COMMENT '出货周期',
  `pay_type` varchar(30) DEFAULT '0' COMMENT '结算方式：1现金,2转账,3支票',
  `tax_invoice` decimal(4,2) NOT NULL DEFAULT '0.00' COMMENT '增值税发票',
  `tax_point` varchar(255) DEFAULT NULL COMMENT '税点',
  `balance_type` tinyint(1) unsigned DEFAULT NULL COMMENT '付款周期：supplier_pay.method',
  `balance_day` varchar(20) DEFAULT NULL COMMENT '周期结算天数',
  `purchase_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购额度',
  `pro_contact` varchar(50) DEFAULT NULL COMMENT '公司联系人',
  `pro_phone` varchar(50) DEFAULT NULL COMMENT '公司联系电话',
  `pro_qq` varchar(50) DEFAULT '' COMMENT '公司联系qq',
  `contact` varchar(50) DEFAULT '' COMMENT 'BDD紧急联系人',
  `kela_phone` varchar(50) DEFAULT '' COMMENT 'BDD紧急联系电话',
  `kela_qq` varchar(50) DEFAULT NULL COMMENT 'BDD紧急联系qq',
  `bank_name` varchar(50) DEFAULT '' COMMENT '开户银行',
  `account_name` varchar(50) DEFAULT NULL COMMENT '户名',
  `account` varchar(50) DEFAULT '' COMMENT '银行账户',
  `is_invoice` tinyint(1) DEFAULT '0' COMMENT '此供应商是否有能力开发票:1开，0不开',
  `pro_email` varchar(50) DEFAULT NULL COMMENT '供货商邮箱',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1启用2停用',
  `create_id` int(10) unsigned DEFAULT NULL COMMENT '创建人ID',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `create_user` varchar(50) DEFAULT NULL COMMENT '创建人',
  `info` text COMMENT '备注',
  `pact_doc` varchar(255) DEFAULT NULL COMMENT '合同附件',
  `license_jpg` varchar(255) DEFAULT NULL COMMENT '营业执照附件',
  `tax_jpg` varchar(255) DEFAULT NULL COMMENT '税务登记证',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=568 DEFAULT CHARSET=utf8 COMMENT='供应商信息';

-- ----------------------------
-- Table structure for app_processor_operation
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_operation`;
CREATE TABLE `app_processor_operation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `processor_id` int(10) unsigned DEFAULT NULL COMMENT '供应商ID',
  `name` varchar(30) NOT NULL COMMENT '供应商名称',
  `operation_type` tinyint(1) NOT NULL COMMENT '操作类型：1保存，2提交,3审批，4拒绝,5修改，6删除，',
  `operation_content` varchar(200) NOT NULL COMMENT '操作内容',
  `create_time` datetime NOT NULL COMMENT '操作时间',
  `create_user_id` int(10) unsigned DEFAULT NULL,
  `create_user` varchar(50) NOT NULL COMMENT '操作人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=686 DEFAULT CHARSET=utf8 COMMENT='操作记录表';

-- ----------------------------
-- Table structure for app_processor_operation_bak
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_operation_bak`;
CREATE TABLE `app_processor_operation_bak` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `processor_id` int(10) unsigned DEFAULT NULL COMMENT '供应商ID',
  `name` varchar(30) NOT NULL COMMENT '供应商名称',
  `operation_type` tinyint(1) NOT NULL COMMENT '操作类型：1保存，2提交,3审批，4拒绝,5修改，6删除，',
  `operation_content` varchar(200) NOT NULL COMMENT '操作内容',
  `create_time` datetime NOT NULL COMMENT '操作时间',
  `create_user_id` int(10) unsigned DEFAULT NULL,
  `create_user` varchar(50) NOT NULL COMMENT '操作人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=410 DEFAULT CHARSET=utf8 COMMENT='操作记录表';

-- ----------------------------
-- Table structure for app_processor_process
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_process`;
CREATE TABLE `app_processor_process` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `process_name` varchar(100) DEFAULT NULL COMMENT '流程名称',
  `business_scope` varchar(50) DEFAULT NULL COMMENT '经营范围：1,2,3...',
  `business_type` varchar(20) DEFAULT NULL COMMENT '经营范围[中文]',
  `department_id` int(10) unsigned NOT NULL COMMENT '申请人部门id',
  `is_enabled` tinyint(1) unsigned DEFAULT '1' COMMENT '是否启用',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT '删除标识',
  `create_user_id` int(10) unsigned NOT NULL COMMENT '创建人id',
  `create_user` varchar(30) DEFAULT NULL COMMENT '创建人',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COMMENT='供应商审批流程表';

-- ----------------------------
-- Table structure for app_processor_process_bak
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_process_bak`;
CREATE TABLE `app_processor_process_bak` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `process_name` varchar(100) DEFAULT NULL COMMENT '流程名称',
  `business_scope` varchar(50) DEFAULT NULL COMMENT '经营范围：1,2,3...',
  `business_type` varchar(20) DEFAULT NULL COMMENT '经营范围[中文]',
  `department_id` int(10) unsigned NOT NULL COMMENT '申请人部门id',
  `is_enabled` tinyint(1) unsigned DEFAULT '1' COMMENT '是否启用',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT '删除标识',
  `create_user_id` int(10) unsigned NOT NULL COMMENT '创建人id',
  `create_user` varchar(30) DEFAULT NULL COMMENT '创建人',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='供应商审批流程表';

-- ----------------------------
-- Table structure for app_processor_record
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_record`;
CREATE TABLE `app_processor_record` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '申请ID',
  `info_id` int(10) unsigned DEFAULT NULL COMMENT '供应商ID',
  `name` varchar(50) NOT NULL COMMENT '供应商名称',
  `code` varchar(30) NOT NULL COMMENT '供应商编码',
  `business_scope` varchar(100) NOT NULL DEFAULT '' COMMENT '经营范围：1:黄金,2:K金素金,3:PT素金,4:K金钻石镶嵌品,5:PT钻石镶嵌品,6:成品钻,7:彩宝饰品,8:银饰品,9:其他',
  `is_open` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开通系统:1是0否',
  `password` varchar(50) DEFAULT NULL COMMENT '密码',
  `business_license` varchar(50) NOT NULL DEFAULT '' COMMENT '营业执照号码',
  `tax_registry_no` varchar(50) NOT NULL DEFAULT '' COMMENT '税务登记证号',
  `business_license_region` varchar(200) NOT NULL DEFAULT '' COMMENT '营业执照地址:省，市，区',
  `business_license_address` varchar(200) NOT NULL DEFAULT '' COMMENT '营业执照地址',
  `pro_region` varchar(200) NOT NULL DEFAULT '' COMMENT '取货地址:省，市，区',
  `pro_address` varchar(200) NOT NULL DEFAULT '' COMMENT '取货地址',
  `cycle` varchar(30) NOT NULL DEFAULT '' COMMENT '出货周期',
  `pay_type` varchar(30) NOT NULL DEFAULT '0' COMMENT '结算方式：1现金,2转账,3支票',
  `tax_invoice` decimal(4,2) DEFAULT '0.00' COMMENT '增值税发票',
  `tax_point` varchar(255) DEFAULT NULL COMMENT '税点',
  `balance_type` tinyint(1) unsigned DEFAULT NULL COMMENT '付款周期：supplier_pay.method',
  `balance_day` varchar(20) DEFAULT NULL COMMENT '周期结算天数',
  `purchase_amount` decimal(10,2) DEFAULT '0.00' COMMENT '采购额度',
  `pro_contact` varchar(50) NOT NULL DEFAULT '' COMMENT '公司联系人',
  `pro_phone` varchar(50) NOT NULL DEFAULT '' COMMENT '公司联系电话',
  `pro_qq` varchar(50) DEFAULT NULL COMMENT '公司联系qq',
  `contact` varchar(50) NOT NULL DEFAULT '' COMMENT 'BDD紧急联系人',
  `kela_phone` varchar(50) NOT NULL DEFAULT '' COMMENT 'BDD紧急联系电话',
  `kela_qq` varchar(50) DEFAULT NULL COMMENT 'BDD紧急联系qq',
  `bank_name` varchar(50) NOT NULL DEFAULT '' COMMENT '开户银行',
  `account_name` varchar(50) NOT NULL DEFAULT '' COMMENT '户名',
  `account` varchar(50) NOT NULL DEFAULT '' COMMENT '银行账户',
  `is_invoice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '此供应商是否开发票:1开，0不开',
  `pro_email` varchar(50) DEFAULT NULL COMMENT '供货商邮箱',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1启用2停用',
  `check_user` int(10) DEFAULT NULL COMMENT '当前审核人ID',
  `check_status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '审核状态;1=保存,2=提交,3=审批中,4=驳回,5=修改,6=删除,7=通过',
  `info` text COMMENT '备注',
  `audit_plan` tinyint(4) unsigned DEFAULT '0' COMMENT '审批进度：0--100',
  `create_id` int(10) unsigned DEFAULT NULL,
  `create_user` varchar(50) DEFAULT NULL COMMENT '创建人',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_id` int(10) unsigned DEFAULT NULL,
  `update_user` varchar(50) DEFAULT NULL COMMENT '提交人/更新人',
  `update_time` datetime DEFAULT NULL COMMENT '最后一次提交/更新时间',
  `department_id` int(10) DEFAULT '0' COMMENT '申请部门id',
  `pact_doc` varchar(255) DEFAULT NULL COMMENT '合同附件',
  `license_jpg` varchar(255) DEFAULT NULL COMMENT '营业执照附件',
  `tax_jpg` varchar(255) DEFAULT NULL COMMENT '税务登记证',
  `is_A_company` varchar(10) DEFAULT NULL COMMENT '是否A公司',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=962 DEFAULT CHARSET=utf8 COMMENT='供应商申请记录';

-- ----------------------------
-- Table structure for app_processor_record_bak
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_record_bak`;
CREATE TABLE `app_processor_record_bak` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '申请ID',
  `info_id` int(10) unsigned DEFAULT NULL COMMENT '供应商ID',
  `name` varchar(50) NOT NULL COMMENT '供应商名称',
  `code` varchar(30) NOT NULL COMMENT '供应商编码',
  `business_scope` varchar(100) NOT NULL DEFAULT '' COMMENT '经营范围：1:黄金,2:K金素金,3:PT素金,4:K金钻石镶嵌品,5:PT钻石镶嵌品,6:成品钻,7:彩宝饰品,8:银饰品,9:其他',
  `is_open` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开通系统:1是0否',
  `password` varchar(50) DEFAULT NULL COMMENT '密码',
  `business_license` varchar(50) NOT NULL DEFAULT '' COMMENT '营业执照号码',
  `tax_registry_no` varchar(50) NOT NULL DEFAULT '' COMMENT '税务登记证号',
  `business_license_region` varchar(200) NOT NULL DEFAULT '' COMMENT '营业执照地址:省，市，区',
  `business_license_address` varchar(200) NOT NULL DEFAULT '' COMMENT '营业执照地址',
  `pro_region` varchar(200) NOT NULL DEFAULT '' COMMENT '取货地址:省，市，区',
  `pro_address` varchar(200) NOT NULL DEFAULT '' COMMENT '取货地址',
  `cycle` varchar(30) NOT NULL DEFAULT '' COMMENT '出货周期',
  `pay_type` varchar(30) NOT NULL DEFAULT '0' COMMENT '结算方式：1现金,2转账,3支票',
  `tax_invoice` decimal(4,2) DEFAULT '0.00' COMMENT '增值税发票',
  `tax_point` varchar(255) DEFAULT NULL COMMENT '税点',
  `balance_type` tinyint(1) unsigned DEFAULT NULL COMMENT '付款周期：supplier_pay.method',
  `balance_day` varchar(20) DEFAULT NULL COMMENT '周期结算天数',
  `purchase_amount` decimal(10,2) DEFAULT '0.00' COMMENT '采购额度',
  `pro_contact` varchar(50) NOT NULL DEFAULT '' COMMENT '公司联系人',
  `pro_phone` varchar(50) NOT NULL DEFAULT '' COMMENT '公司联系电话',
  `pro_qq` varchar(50) DEFAULT NULL COMMENT '公司联系qq',
  `contact` varchar(50) NOT NULL DEFAULT '' COMMENT 'BDD紧急联系人',
  `kela_phone` varchar(50) NOT NULL DEFAULT '' COMMENT 'BDD紧急联系电话',
  `kela_qq` varchar(50) DEFAULT NULL COMMENT 'BDD紧急联系qq',
  `bank_name` varchar(50) NOT NULL DEFAULT '' COMMENT '开户银行',
  `account_name` varchar(50) NOT NULL DEFAULT '' COMMENT '户名',
  `account` varchar(50) NOT NULL DEFAULT '' COMMENT '银行账户',
  `is_invoice` tinyint(1) NOT NULL DEFAULT '0' COMMENT '此供应商是否开发票:1开，0不开',
  `pro_email` varchar(50) DEFAULT NULL COMMENT '供货商邮箱',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1启用2停用',
  `check_user` int(10) DEFAULT NULL COMMENT '当前审核人ID',
  `check_status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '审核状态;1=保存,2=提交,3=审批中,4=驳回,5=修改,6=删除,7=通过',
  `info` text COMMENT '备注',
  `audit_plan` tinyint(4) unsigned DEFAULT '0' COMMENT '审批进度：0--100',
  `create_id` int(10) unsigned DEFAULT NULL,
  `create_user` varchar(50) DEFAULT NULL COMMENT '创建人',
  `create_time` datetime DEFAULT NULL COMMENT '创建时间',
  `update_id` int(10) unsigned DEFAULT NULL,
  `update_user` varchar(50) DEFAULT NULL COMMENT '提交人/更新人',
  `update_time` datetime DEFAULT NULL COMMENT '最后一次提交/更新时间',
  `department_id` int(10) DEFAULT '0' COMMENT '申请部门id',
  `pact_doc` varchar(255) DEFAULT NULL COMMENT '合同附件',
  `license_jpg` varchar(255) DEFAULT NULL COMMENT '营业执照附件',
  `tax_jpg` varchar(255) DEFAULT NULL COMMENT '税务登记证',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=909 DEFAULT CHARSET=utf8 COMMENT='供应商申请记录';

-- ----------------------------
-- Table structure for app_processor_taker
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_taker`;
CREATE TABLE `app_processor_taker` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `supplier_id` int(10) unsigned NOT NULL COMMENT '供应商ID',
  `taker_id` int(10) unsigned NOT NULL COMMENT '取货人ID',
  `taker_account` varchar(20) DEFAULT NULL COMMENT '取货人账户',
  `taker_name` varchar(20) DEFAULT NULL COMMENT '姓名',
  `taker_gender` tinyint(1) DEFAULT NULL COMMENT '性别:0=男,1=女',
  `taker_tel` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `taker_papers` char(18) DEFAULT NULL COMMENT '身份证号',
  `create_id` int(11) DEFAULT NULL COMMENT '添加人',
  `create_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `is_deleted` tinyint(1) DEFAULT NULL COMMENT '删除标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='委托取货人';

-- ----------------------------
-- Table structure for app_processor_taker_bak
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_taker_bak`;
CREATE TABLE `app_processor_taker_bak` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `supplier_id` int(10) unsigned NOT NULL COMMENT '供应商ID',
  `taker_id` int(10) unsigned NOT NULL COMMENT '取货人ID',
  `taker_account` varchar(20) DEFAULT NULL COMMENT '取货人账户',
  `taker_name` varchar(20) DEFAULT NULL COMMENT '姓名',
  `taker_gender` tinyint(1) DEFAULT NULL COMMENT '性别:0=男,1=女',
  `taker_tel` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `taker_papers` char(18) DEFAULT NULL COMMENT '身份证号',
  `create_id` int(11) DEFAULT NULL COMMENT '添加人',
  `create_time` int(11) DEFAULT NULL COMMENT '添加时间',
  `is_deleted` tinyint(1) DEFAULT NULL COMMENT '删除标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COMMENT='委托取货人';

-- ----------------------------
-- Table structure for app_processor_user
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_user`;
CREATE TABLE `app_processor_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `process_id` int(11) unsigned NOT NULL COMMENT '流程ID',
  `user_id` int(11) DEFAULT NULL COMMENT '审批人',
  `user_order` tinyint(4) unsigned DEFAULT NULL COMMENT '审批顺序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=118 DEFAULT CHARSET=utf8 COMMENT='供应商审批人';

-- ----------------------------
-- Table structure for app_processor_user_bak
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_user_bak`;
CREATE TABLE `app_processor_user_bak` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `process_id` int(11) unsigned NOT NULL COMMENT '流程ID',
  `user_id` int(11) DEFAULT NULL COMMENT '审批人',
  `user_order` tinyint(4) unsigned DEFAULT NULL COMMENT '审批顺序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101 DEFAULT CHARSET=utf8 COMMENT='供应商审批人';

-- ----------------------------
-- Table structure for app_processor_worktime
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_worktime`;
CREATE TABLE `app_processor_worktime` (
  `pw_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `processor_id` int(10) NOT NULL DEFAULT '0' COMMENT '供应商id',
  `normal_day` tinyint(3) NOT NULL DEFAULT '7' COMMENT '标准出货时间',
  `wait_dia` tinyint(3) NOT NULL DEFAULT '25' COMMENT '等钻加时',
  `behind_wait_dia` tinyint(3) NOT NULL DEFAULT '5' COMMENT '等钻后加时',
  `ykqbzq` tinyint(3) NOT NULL COMMENT '有款起版周期',
  `is_rest` int(1) NOT NULL DEFAULT '1' COMMENT '工作休息；1为不休；2为单休；3为双休',
  `order_problem` int(3) NOT NULL DEFAULT '0' COMMENT '订单问题加时',
  `wkqbzq` tinyint(3) DEFAULT NULL COMMENT '无款起版周期',
  `order_type` char(3) DEFAULT NULL COMMENT '订单类型 1->客订单 2->采购单',
  `now_wait_dia` tinyint(3) DEFAULT NULL COMMENT '现货等钻加时',
  `is_work` text COMMENT '周末上班 上班日期',
  `holiday_time` text COMMENT '放假日期',
  PRIMARY KEY (`pw_id`),
  KEY `user_id` (`processor_id`)
) ENGINE=MyISAM AUTO_INCREMENT=491 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for app_processor_worktime_bak
-- ----------------------------
DROP TABLE IF EXISTS `app_processor_worktime_bak`;
CREATE TABLE `app_processor_worktime_bak` (
  `pw_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `processor_id` int(10) NOT NULL DEFAULT '0' COMMENT '供应商id',
  `normal_day` tinyint(3) NOT NULL DEFAULT '7' COMMENT '标准出货时间',
  `wait_dia` tinyint(3) NOT NULL DEFAULT '25' COMMENT '等钻加时',
  `behind_wait_dia` tinyint(3) NOT NULL DEFAULT '5' COMMENT '等钻后加时',
  `ykqbzq` tinyint(3) NOT NULL COMMENT '有款起版周期',
  `is_rest` int(1) NOT NULL DEFAULT '1' COMMENT '工作休息；1为不休；2为单休；3为双休',
  `order_problem` int(3) NOT NULL DEFAULT '0' COMMENT '订单问题加时',
  `wkqbzq` tinyint(3) DEFAULT NULL COMMENT '无款起版周期',
  `order_type` char(3) DEFAULT NULL COMMENT '订单类型 1->客订单 2->采购单',
  `now_wait_dia` tinyint(3) DEFAULT NULL COMMENT '现货等钻加时',
  `is_work` text COMMENT '周末上班 上班日期',
  `holiday_time` text COMMENT '放假日期',
  PRIMARY KEY (`pw_id`),
  KEY `user_id` (`processor_id`)
) ENGINE=MyISAM AUTO_INCREMENT=452 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for factory_opra_dict
-- ----------------------------
DROP TABLE IF EXISTS `factory_opra_dict`;
CREATE TABLE `factory_opra_dict` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(60) NOT NULL DEFAULT '''''' COMMENT '名称',
  `create_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '创建时间',
  `edit_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '修改时间',
  `status` int(1) NOT NULL DEFAULT '1' COMMENT '0为禁用1为正常',
  `dict_id` int(10) NOT NULL DEFAULT '0' COMMENT '对应数字字典的id',
  `dict_value` tinyint(4) NOT NULL DEFAULT '0' COMMENT '对应数字字典的枚举key',
  `display_order` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  KEY `order_sn` (`display_order`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COMMENT='工厂操作维护列表';

-- ----------------------------
-- Table structure for fix_diy
-- ----------------------------
DROP TABLE IF EXISTS `fix_diy`;
CREATE TABLE `fix_diy` (
  `bc_id` varchar(12) DEFAULT NULL,
  `s_val` smallint(255) DEFAULT NULL,
  `dt` varchar(255) DEFAULT NULL,
  `c_val` smallint(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for peishi_list
-- ----------------------------
DROP TABLE IF EXISTS `peishi_list`;
CREATE TABLE `peishi_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id主键',
  `order_sn` varchar(30) DEFAULT NULL COMMENT '订单号',
  `rec_id` int(10) NOT NULL DEFAULT '0' COMMENT '布产id',
  `peishi_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '配石状态（需建数据字典）',
  `add_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `last_time` datetime NOT NULL,
  `add_user` varchar(30) DEFAULT NULL COMMENT '添加人',
  `peishi_remark` varchar(255) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL COMMENT '钻石颜色',
  `clarity` varchar(50) DEFAULT NULL COMMENT '钻石净度',
  `shape` varchar(50) DEFAULT NULL COMMENT '钻石形状',
  `cert` varchar(50) DEFAULT NULL COMMENT '证书类型',
  `zhengshuhao` varchar(50) DEFAULT NULL COMMENT '证书号',
  `carat` varchar(50) DEFAULT NULL COMMENT '钻石大小',
  `stone_num` smallint(5) unsigned DEFAULT '1' COMMENT '钻石数量(布产商品数量*钻石粒数)',
  `stone_cat` varchar(50) DEFAULT NULL COMMENT '钻石类型',
  `stone_position` tinyint(3) unsigned DEFAULT NULL COMMENT '钻石位置 0：主石 ，1：副石1，2：副石2，3：副石3',
  `caigou_time` datetime DEFAULT NULL COMMENT '采购时间（记录最新的一次采购时间）',
  `songshi_time` datetime DEFAULT NULL COMMENT '已送生产部时间(已送生产部的最新一次时间)',
  `peishi_time` datetime DEFAULT NULL COMMENT '配石中时间（操作配石中的最新时间）',
  `caigou_user` varchar(30) DEFAULT NULL COMMENT '采购人（操作采购中的人员）',
  `songshi_user` varchar(30) DEFAULT NULL COMMENT '送石人（已送生产部操作人员）',
  `peishi_user` varchar(30) DEFAULT NULL COMMENT '配石人（配石中操作人员）',
  PRIMARY KEY (`id`),
  KEY `rec_id` (`rec_id`,`stone_position`,`peishi_status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=28955 DEFAULT CHARSET=utf8 COMMENT='配石列表';

-- ----------------------------
-- Table structure for peishi_list_170523
-- ----------------------------
DROP TABLE IF EXISTS `peishi_list_170523`;
CREATE TABLE `peishi_list_170523` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id主键',
  `order_sn` varchar(30) DEFAULT NULL COMMENT '订单号',
  `rec_id` int(10) NOT NULL DEFAULT '0' COMMENT '布产id',
  `peishi_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '配石状态（需建数据字典）',
  `add_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `last_time` datetime NOT NULL,
  `admins` varchar(30) DEFAULT NULL COMMENT '添加人',
  `peishi_remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rec_id` (`rec_id`,`peishi_status`)
) ENGINE=InnoDB AUTO_INCREMENT=8617 DEFAULT CHARSET=utf8 COMMENT='配石列表';

-- ----------------------------
-- Table structure for peishi_list_goods
-- ----------------------------
DROP TABLE IF EXISTS `peishi_list_goods`;
CREATE TABLE `peishi_list_goods` (
  `peishi_id` int(10) unsigned NOT NULL COMMENT '配石ID',
  `goods_id` varchar(50) DEFAULT NULL COMMENT '商品ID',
  KEY `peishi_id` (`peishi_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for peishi_list_log
-- ----------------------------
DROP TABLE IF EXISTS `peishi_list_log`;
CREATE TABLE `peishi_list_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `peishi_id` int(11) NOT NULL COMMENT '配石列表主键',
  `add_time` datetime NOT NULL COMMENT '生成时间',
  `action_name` varchar(20) NOT NULL COMMENT '操作人',
  `remark` varchar(255) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67003 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_apply_info
-- ----------------------------
DROP TABLE IF EXISTS `product_apply_info`;
CREATE TABLE `product_apply_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(200) NOT NULL,
  `detail_id` int(10) unsigned DEFAULT NULL COMMENT '订单明细ID',
  `style_sn` varchar(100) DEFAULT '' COMMENT '商品款号',
  `apply_info` longtext NOT NULL COMMENT '申请信息',
  `old_info` longtext,
  `apply_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '申请状态:0=未操作,1=同意,2=拒绝',
  `factory_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '工厂接受状态：1=未接受,2=已接受',
  `factory_time` datetime DEFAULT NULL COMMENT '工厂接受时间',
  `goods_status` tinyint(1) DEFAULT '1' COMMENT '生产状态：数据字典=buchan_status',
  `apply_id` int(10) unsigned NOT NULL COMMENT '申请人ID',
  `apply_name` varchar(50) DEFAULT NULL COMMENT '申请人姓名',
  `apply_time` datetime DEFAULT NULL COMMENT '申请时间',
  `check_id` int(10) unsigned DEFAULT NULL COMMENT '审核人ID',
  `check_name` varchar(50) DEFAULT NULL COMMENT '审核人姓名',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `refuse_remark` varchar(255) DEFAULT NULL COMMENT '拒绝理由',
  `special` varchar(255) DEFAULT NULL COMMENT '特别要求',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11481 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_factory_opra
-- ----------------------------
DROP TABLE IF EXISTS `product_factory_opra`;
CREATE TABLE `product_factory_opra` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `bc_id` int(11) NOT NULL COMMENT '布产号',
  `opra_action` int(11) NOT NULL COMMENT '工厂操作动作',
  `opra_uid` int(11) NOT NULL COMMENT '操作人ID',
  `opra_uname` varchar(20) NOT NULL COMMENT '操作人',
  `opra_time` datetime NOT NULL COMMENT '操作时间',
  `opra_info` varchar(50) NOT NULL COMMENT '操作备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=183421 DEFAULT CHARSET=utf8 COMMENT='工厂操作流水表';

-- ----------------------------
-- Table structure for product_factory_oprauser
-- ----------------------------
DROP TABLE IF EXISTS `product_factory_oprauser`;
CREATE TABLE `product_factory_oprauser` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `prc_id` int(10) NOT NULL DEFAULT '0' COMMENT '工厂id',
  `opra_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '跟单人ID',
  `opra_uname` varchar(30) NOT NULL DEFAULT '' COMMENT '跟单人姓名',
  `add_user` varchar(30) DEFAULT '' COMMENT '添加人',
  `add_time` datetime DEFAULT '0000-00-00 00:00:00' COMMENT '添加时间',
  `production_manager_id` int(10) DEFAULT NULL COMMENT '生产经理Id',
  `production_manager_name` varchar(30) DEFAULT NULL COMMENT '生产经理名称',
  PRIMARY KEY (`id`),
  KEY `prc_id` (`prc_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=221 DEFAULT CHARSET=utf8 COMMENT='工厂 - 跟单人关系表';

-- ----------------------------
-- Table structure for product_fqc_conf
-- ----------------------------
DROP TABLE IF EXISTS `product_fqc_conf`;
CREATE TABLE `product_fqc_conf` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `cat_name` varchar(20) NOT NULL COMMENT '分类名字',
  `parent_id` int(11) NOT NULL COMMENT '分类父id',
  `tree_path` varchar(512) NOT NULL COMMENT '全路径',
  `pids` varchar(200) NOT NULL COMMENT '祖先分类',
  `childrens` int(11) NOT NULL COMMENT '下级分类',
  `display_order` int(11) NOT NULL COMMENT '显示顺序',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_goods_rel
-- ----------------------------
DROP TABLE IF EXISTS `product_goods_rel`;
CREATE TABLE `product_goods_rel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `goods_id` int(11) NOT NULL COMMENT '货品ID',
  `bc_id` int(11) NOT NULL COMMENT '布产ID',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`) USING BTREE,
  KEY `bc_id` (`bc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26271 DEFAULT CHARSET=utf8 COMMENT='订单货品和布产单关系表';

-- ----------------------------
-- Table structure for product_info
-- ----------------------------
DROP TABLE IF EXISTS `product_info`;
CREATE TABLE `product_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `bc_sn` varchar(20) NOT NULL COMMENT '布产号',
  `p_id` int(11) NOT NULL COMMENT '采购单明细ID/订单商品ID',
  `p_sn` varchar(20) NOT NULL COMMENT '采购单号/订单号',
  `style_sn` varchar(30) NOT NULL COMMENT '款号',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态:buchan_status',
  `buchan_fac_opra` int(2) NOT NULL DEFAULT '1' COMMENT '生产状态：见数据字典buchan_fac_opra',
  `num` int(11) NOT NULL DEFAULT '1' COMMENT '数量',
  `prc_id` int(11) NOT NULL COMMENT '工厂ID',
  `prc_name` varchar(30) NOT NULL COMMENT '工厂名称',
  `opra_uname` varchar(20) NOT NULL COMMENT '跟单人',
  `add_time` datetime NOT NULL COMMENT '单据添加时间',
  `esmt_time` date NOT NULL DEFAULT '0000-00-00' COMMENT '标准出厂时间',
  `rece_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '工厂交货时间',
  `info` varchar(255) NOT NULL COMMENT '备注',
  `from_type` int(2) NOT NULL DEFAULT '1' COMMENT '来源类型：1=>采购单 2=>订单',
  `consignee` varchar(50) DEFAULT NULL COMMENT '客户姓名',
  `edit_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '最后操作时间',
  `order_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '接单时间',
  `remark` varchar(255) DEFAULT NULL COMMENT '最后操作备注',
  `bc_style` varchar(20) NOT NULL DEFAULT '' COMMENT '类型普通，加急',
  `goods_name` varchar(200) NOT NULL COMMENT '品名',
  `xiangqian` varchar(60) NOT NULL DEFAULT '' COMMENT '镶嵌要求',
  `factory_opra_status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '工厂操作（读取工厂操作维护）',
  `customer_source_id` int(10) DEFAULT '0' COMMENT '客户来源',
  `channel_id` int(10) DEFAULT '0' COMMENT '渠道id',
  `caigou_info` varchar(250) DEFAULT NULL COMMENT '采购单备注',
  `create_user` varchar(20) NOT NULL DEFAULT '' COMMENT '创建人',
  `weixiu_status` tinyint(1) DEFAULT '0',
  `is_peishi` tinyint(1) unsigned DEFAULT '0' COMMENT '是否支持4C配钻，0不支持，1裸钻支持，2空托支持',
  `buchan_times` smallint(2) DEFAULT '1' COMMENT '布产次数',
  `is_alone` tinyint(1) DEFAULT '0',
  `diamond_type` tinyint(2) DEFAULT '0' COMMENT '钻石类型：0-默认 1-现货钻 2-期货钻',
  `origin_dia_type` tinyint(2) DEFAULT '0' COMMENT '原钻石类型：0-默认 1-现货 2-期货',
  `qiban_type` tinyint(1) unsigned DEFAULT '2' COMMENT '起版类型：见数据字典qiban_type',
  `to_factory_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '分配工厂时间',
  `wait_dia_starttime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '等钻开始时间',
  `wait_dia_endtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '实际等钻结束时间',
  `wait_dia_finishtime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '预计等钻完成时间',
  `oqc_pass_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'OQC质检通过时间',
  `peishi_goods_id` bigint(30) DEFAULT '0' COMMENT '配石货号',
  `production_manager_name` varchar(30) DEFAULT NULL COMMENT '生产经理',
  `def_factory_sn` varchar(30) DEFAULT NULL COMMENT '默认工厂模号',
  `def_factory_name` varchar(120) DEFAULT NULL COMMENT '默认工厂名称',
  `is_quick_diy` tinyint(1) unsigned DEFAULT '0' COMMENT '是否快速定制 1是 0否',
  `combine_goods_id` varchar(30) DEFAULT NULL COMMENT '组合镶嵌空托商品ID',
  `is_combine` tinyint(1) unsigned DEFAULT '0' COMMENT '是否组合镶嵌 1是 0否',
  `biaozhun_jinzhong_min` decimal(8,3) DEFAULT NULL COMMENT '标准金重下限',
  `biaozhun_jinzhong_max` decimal(8,3) DEFAULT NULL COMMENT '标准金重上限',
  `lishi_jinzhong_min` decimal(8,3) DEFAULT NULL COMMENT '历史金重下限',
  `lishi_jinzhong_max` decimal(8,3) DEFAULT NULL COMMENT '历史金重上限',
  `hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏栏位',
  PRIMARY KEY (`id`),
  KEY `p_sn` (`p_sn`),
  KEY `bc_sn` (`bc_sn`),
  KEY `style_sn` (`style_sn`),
  KEY `hidden` (`hidden`)
) ENGINE=InnoDB AUTO_INCREMENT=9944428 DEFAULT CHARSET=utf8 COMMENT='布产单';

-- ----------------------------
-- Table structure for product_info20170424
-- ----------------------------
DROP TABLE IF EXISTS `product_info20170424`;
CREATE TABLE `product_info20170424` (
  `bcid` varchar(255) DEFAULT NULL,
  `zhengshuleibie` varchar(255) DEFAULT NULL,
  `goods_id` varchar(255) DEFAULT NULL,
  `zhengshuhao` varchar(255) DEFAULT NULL,
  `cart` varchar(255) DEFAULT NULL,
  `yanse` varchar(255) DEFAULT NULL,
  `jingdu` varchar(255) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  `F12` varchar(255) DEFAULT NULL,
  `F13` varchar(255) DEFAULT NULL,
  `F14` varchar(255) DEFAULT NULL,
  KEY `bcid` (`bcid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_info20170426
-- ----------------------------
DROP TABLE IF EXISTS `product_info20170426`;
CREATE TABLE `product_info20170426` (
  `goods_id` varchar(255) DEFAULT NULL,
  `zhengshuleibie` varchar(255) DEFAULT NULL,
  `zhengshuhao` varchar(255) DEFAULT NULL,
  `cart` varchar(255) DEFAULT NULL,
  `yanse` varchar(255) DEFAULT NULL,
  `jingdu` varchar(255) DEFAULT NULL,
  `bc_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for product_info_4c
-- ----------------------------
DROP TABLE IF EXISTS `product_info_4c`;
CREATE TABLE `product_info_4c` (
  `id` int(11) unsigned NOT NULL COMMENT '主键id(裸钻布产ID)',
  `order_sn` varchar(125) DEFAULT NULL COMMENT '订单编号',
  `kt_order_detail_id` int(10) unsigned DEFAULT NULL COMMENT '空托的order_detail_id',
  `kt_bc_sn` varchar(20) DEFAULT NULL COMMENT '空托布产号',
  `zhengshuhao` varchar(125) DEFAULT NULL COMMENT '新证书号',
  `zhengshuhao_org` varchar(125) NOT NULL COMMENT '原证书号',
  `price_org` decimal(10,2) DEFAULT NULL COMMENT '原采购价格',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '采购价格',
  `discount_org` decimal(10,2) DEFAULT NULL COMMENT '原采购折扣',
  `discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '新采购折扣',
  `color` varchar(60) DEFAULT NULL COMMENT '新颜色',
  `carat` varchar(60) DEFAULT NULL COMMENT '新石重',
  `shape` varchar(60) DEFAULT NULL COMMENT '新形状',
  `clarity` varchar(60) DEFAULT NULL COMMENT '新净度',
  `cut` varchar(60) DEFAULT NULL COMMENT '新切工',
  `peishi_status` tinyint(1) unsigned DEFAULT '0' COMMENT '4C配钻状态 0未完成 1已完成',
  `create_user` varchar(60) DEFAULT NULL COMMENT '操作人',
  `create_time` datetime DEFAULT NULL COMMENT '配钻时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='4C配钻记录表';

-- ----------------------------
-- Table structure for product_info_attr
-- ----------------------------
DROP TABLE IF EXISTS `product_info_attr`;
CREATE TABLE `product_info_attr` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `g_id` int(11) NOT NULL COMMENT '布产ID',
  `code` varchar(50) NOT NULL COMMENT '属性code',
  `name` varchar(50) NOT NULL COMMENT '属性name',
  `value` tinytext NOT NULL COMMENT '属性value值',
  PRIMARY KEY (`id`),
  KEY `g_id` (`g_id`),
  KEY `g_id_code` (`g_id`,`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1755816 DEFAULT CHARSET=utf8 COMMENT='采购单货品属性表';

-- ----------------------------
-- Table structure for product_info_attr_1016
-- ----------------------------
DROP TABLE IF EXISTS `product_info_attr_1016`;
CREATE TABLE `product_info_attr_1016` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `g_id` int(11) NOT NULL COMMENT '布产ID',
  `code` varchar(50) NOT NULL COMMENT '属性code',
  `name` varchar(50) NOT NULL COMMENT '属性name',
  `value` tinytext NOT NULL COMMENT '属性value值',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购单货品属性表';

-- ----------------------------
-- Table structure for product_info_attr_170523
-- ----------------------------
DROP TABLE IF EXISTS `product_info_attr_170523`;
CREATE TABLE `product_info_attr_170523` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `g_id` int(11) NOT NULL COMMENT '布产ID',
  `code` varchar(50) NOT NULL COMMENT '属性code',
  `name` varchar(50) NOT NULL COMMENT '属性name',
  `value` tinytext NOT NULL COMMENT '属性value值',
  PRIMARY KEY (`id`),
  KEY `g_id` (`g_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1027356 DEFAULT CHARSET=utf8 COMMENT='采购单货品属性表';

-- ----------------------------
-- Table structure for product_info_attr_copy
-- ----------------------------
DROP TABLE IF EXISTS `product_info_attr_copy`;
CREATE TABLE `product_info_attr_copy` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `g_id` int(11) NOT NULL COMMENT '布产ID',
  `code` varchar(50) NOT NULL COMMENT '属性code',
  `name` varchar(50) NOT NULL COMMENT '属性name',
  `value` tinytext NOT NULL COMMENT '属性value值',
  PRIMARY KEY (`id`),
  KEY `g_id` (`g_id`),
  KEY `g_id_code` (`g_id`,`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1140129 DEFAULT CHARSET=utf8 COMMENT='采购单货品属性表';

-- ----------------------------
-- Table structure for product_info_attr_old
-- ----------------------------
DROP TABLE IF EXISTS `product_info_attr_old`;
CREATE TABLE `product_info_attr_old` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `g_id` int(11) NOT NULL COMMENT '布产ID',
  `code` varchar(50) NOT NULL COMMENT '属性code',
  `name` varchar(50) NOT NULL COMMENT '属性name',
  `value` tinytext NOT NULL COMMENT '属性value值',
  PRIMARY KEY (`id`),
  KEY `g_id` (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='采购单货品属性表';

-- ----------------------------
-- Table structure for product_opra_log
-- ----------------------------
DROP TABLE IF EXISTS `product_opra_log`;
CREATE TABLE `product_opra_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bc_id` int(11) NOT NULL COMMENT '布产号ID',
  `status` int(2) NOT NULL COMMENT '当前状态',
  `remark` text NOT NULL COMMENT '备注',
  `uid` int(11) NOT NULL COMMENT '操作人ID',
  `uname` varchar(30) NOT NULL COMMENT '操作人姓名',
  `time` datetime NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`),
  KEY `bc_id` (`bc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1163773 DEFAULT CHARSET=utf8 COMMENT='布产单操作日志表';

-- ----------------------------
-- Table structure for product_oqc_opra
-- ----------------------------
DROP TABLE IF EXISTS `product_oqc_opra`;
CREATE TABLE `product_oqc_opra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bc_id` int(11) NOT NULL COMMENT '布产号',
  `oqc_num` int(11) DEFAULT NULL COMMENT '质检通过数量',
  `oqc_bf_num` int(11) DEFAULT NULL COMMENT '质检报废数量',
  `oqc_no_num` int(11) DEFAULT NULL COMMENT '质检未过数量',
  `reason_scrapped` varchar(255) DEFAULT NULL COMMENT '报废原因',
  `oqc_result` tinyint(2) NOT NULL COMMENT 'OQC结果',
  `oqc_reason` tinyint(2) NOT NULL DEFAULT '0' COMMENT 'OQC未过原因',
  `oqc_info` varchar(50) NOT NULL COMMENT '操作备注',
  `opra_uid` int(11) NOT NULL COMMENT '操作人ID',
  `opra_uname` varchar(20) NOT NULL COMMENT '操作人',
  `opra_time` datetime NOT NULL COMMENT '操作时间',
  `buchan_times` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20735 DEFAULT CHARSET=utf8 COMMENT='OQC操作记录表';

-- ----------------------------
-- Table structure for product_shipment
-- ----------------------------
DROP TABLE IF EXISTS `product_shipment`;
CREATE TABLE `product_shipment` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `bc_id` int(11) NOT NULL COMMENT '布产单ID',
  `shipment_number` varchar(20) NOT NULL COMMENT '出货单号',
  `num` int(11) NOT NULL COMMENT '出货数量',
  `bf_num` int(11) NOT NULL DEFAULT '0' COMMENT '报废数量',
  `info` varchar(255) NOT NULL COMMENT '备注',
  `opra_uid` int(11) NOT NULL COMMENT '操作人ID',
  `opra_uname` varchar(20) NOT NULL COMMENT '操作人姓名',
  `opra_time` datetime NOT NULL COMMENT '操作时间',
  `oqc_result` tinyint(1) DEFAULT '1' COMMENT '质检结果,1:通过，0：未通过',
  `oqc_no_num` int(11) DEFAULT NULL COMMENT '质检未过数量',
  `reason_scrapped` varchar(255) DEFAULT NULL COMMENT '报废原因',
  `oqc_no_type` tinyint(4) DEFAULT NULL COMMENT '质检未过类型:供应商管理-流程管理-质检未通过原因',
  `oqc_no_reason` tinyint(4) DEFAULT NULL COMMENT '未过原因:供应商管理-流程管理-质检未通过原因',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=43373 DEFAULT CHARSET=utf8 COMMENT='工厂出货表';

-- ----------------------------
-- Table structure for rel_img_product_info
-- ----------------------------
DROP TABLE IF EXISTS `rel_img_product_info`;
CREATE TABLE `rel_img_product_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `product_info_id` int(10) unsigned NOT NULL COMMENT '布产单ID',
  `save_name` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '图片名称',
  `save_path` varchar(60) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '图片路径',
  `create_user_id` int(10) NOT NULL COMMENT '添加人ID',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '添加时间',
  `create_user_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '添加人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=692 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- ----------------------------
-- Table structure for stone_feed_config
-- ----------------------------
DROP TABLE IF EXISTS `stone_feed_config`;
CREATE TABLE `stone_feed_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `color` varchar(25) DEFAULT NULL COMMENT '颜色',
  `clarity` varchar(25) DEFAULT NULL COMMENT '净度',
  `cert` varchar(25) DEFAULT NULL COMMENT '证书类型',
  `carat_min` double unsigned DEFAULT NULL COMMENT '石重下限（最小值）',
  `carat_max` double DEFAULT NULL COMMENT '石重上限(最大值)',
  `factory_id` int(10) unsigned DEFAULT NULL COMMENT '工厂ID',
  `factory_name` varchar(100) DEFAULT NULL COMMENT '工厂名称',
  `feed_type` tinyint(3) unsigned DEFAULT NULL COMMENT '供料类型 ，详情见 stone.feed_type',
  `prority_sort` int(5) unsigned DEFAULT NULL COMMENT '优先级排序，数字越小优先级越高，同一个工厂下优先级不能重复',
  `create_time` datetime DEFAULT NULL COMMENT '添加时间',
  `create_user` varchar(50) DEFAULT NULL COMMENT '添加人',
  `is_enable` tinyint(1) unsigned DEFAULT '1' COMMENT '是否可用 1可用 0禁用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 COMMENT='裸石供料配置管理';
