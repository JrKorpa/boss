/*
Navicat MySQL Data Transfer

Source Server         : mysql_192.168.0.95_3306
Source Server Version : 50626
Source Host           : 192.168.0.95:3306
Source Database       : shibao

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2019-09-09 10:24:11
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for dia
-- ----------------------------
DROP TABLE IF EXISTS `dia`;
CREATE TABLE `dia` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `shibao` varchar(30) NOT NULL,
  `addtime` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `kucun_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '库存数量',
  `MS_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '买入数量',
  `fenbaoru_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '分包转入数量',
  `SS_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '送出数量',
  `fenbaochu_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '分包转出数量',
  `HS_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '还回数量-镶嵌',
  `TS_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '退石数量',
  `YS_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '遗失数量',
  `SY_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '损坏数量',
  `TH_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '退货数',
  `RK_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '其他入库数量',
  `CK_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '其他出库数量',
  `kucun_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `MS_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `fenbaoru_zhong` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '分包转入重量',
  `SS_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `fenbaochu_zhong` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '分包转出重量',
  `HS_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `TS_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `YS_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `SY_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `TH_zhong` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '退货重',
  `RK_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `CK_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `yuanshicaigouchengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原始采购成本',
  `caigouchengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '每卡采购价格',
  `xiaoshouchengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '每卡销售价格',
  PRIMARY KEY (`id`),
  UNIQUE KEY `shibao_2` (`shibao`),
  KEY `shibao` (`shibao`)
) ENGINE=InnoDB AUTO_INCREMENT=3288 DEFAULT CHARSET=utf8 COMMENT='石包信息';

-- ----------------------------
-- Table structure for dia_20151117
-- ----------------------------
DROP TABLE IF EXISTS `dia_20151117`;
CREATE TABLE `dia_20151117` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `shibao` varchar(30) NOT NULL,
  `addtime` datetime NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `kucun_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '库存数量',
  `MS_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '买入数量',
  `fenbaoru_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '分包转入数量',
  `SS_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '送出数量',
  `fenbaochu_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '分包转出数量',
  `HS_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '还回数量-镶嵌',
  `TS_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '退石数量',
  `YS_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '遗失数量',
  `SY_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '损坏数量',
  `TH_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '退货数',
  `RK_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '其他入库数量',
  `CK_cnt` int(8) NOT NULL DEFAULT '0' COMMENT '其他出库数量',
  `kucun_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `MS_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `fenbaoru_zhong` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '分包转入重量',
  `SS_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `fenbaochu_zhong` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '分包转出重量',
  `HS_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `TS_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `YS_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `SY_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `TH_zhong` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '退货重',
  `RK_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `CK_zhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `yuanshicaigouchengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原始采购成本',
  `caigouchengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '每卡采购价格',
  `xiaoshouchengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '每卡销售价格',
  PRIMARY KEY (`id`),
  UNIQUE KEY `shibao_2` (`shibao`),
  KEY `shibao` (`shibao`)
) ENGINE=InnoDB AUTO_INCREMENT=3284 DEFAULT CHARSET=utf8 COMMENT='石包信息';

-- ----------------------------
-- Table structure for dia_order
-- ----------------------------
DROP TABLE IF EXISTS `dia_order`;
CREATE TABLE `dia_order` (
  `order_id` int(8) NOT NULL AUTO_INCREMENT,
  `type` varchar(10) NOT NULL COMMENT '''MS''=>''买石单'',''SS''=>''送石单'',''HS''=>''还石单'',''TS''=>''退石单'',''YS''=>''遗失单'',''SY''=>''损益单'',''RK''=>''其他入库单'',''CK''=>''其他出库单''',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0=新增，1=已保存，2=已审核，3=已取消',
  `fin_status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '0=未审核，1=已审核',
  `order_time` date NOT NULL,
  `in_warehouse_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '参照数据字典',
  `account_type` tinyint(3) NOT NULL DEFAULT '0' COMMENT '参照数据字典',
  `adjust_type` int(4) NOT NULL DEFAULT '0' COMMENT '调整类型0-扣减,1-增加',
  `send_goods_sn` varchar(60) NOT NULL COMMENT '送货单号',
  `prc_id` int(4) NOT NULL DEFAULT '0',
  `prc_name` varchar(30) NOT NULL,
  `goods_num` int(8) NOT NULL DEFAULT '0' COMMENT '石包总数',
  `goods_zhong` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '石包总重量',
  `goods_total` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '石包总价值',
  `shijia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '采购支付金额',
  `make_order` varchar(30) NOT NULL,
  `addtime` datetime NOT NULL,
  `check_order` varchar(30) DEFAULT NULL,
  `checktime` datetime DEFAULT NULL,
  `fin_check` varchar(30) DEFAULT NULL,
  `fin_check_time` datetime DEFAULT NULL,
  `info` text NOT NULL,
  `times` varchar(40) NOT NULL COMMENT '时间戳',
  `pro_sn` char(14) DEFAULT NULL,
  PRIMARY KEY (`order_id`),
  KEY `checktime` (`checktime`),
  KEY `type` (`type`),
  KEY `status` (`status`),
  KEY `prc_id` (`prc_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10005989 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='石包单据';

-- ----------------------------
-- Table structure for dia_order_goods
-- ----------------------------
DROP TABLE IF EXISTS `dia_order_goods`;
CREATE TABLE `dia_order_goods` (
  `og_id` int(10) NOT NULL AUTO_INCREMENT,
  `order_id` int(8) NOT NULL,
  `order_type` varchar(10) NOT NULL,
  `shibao` varchar(20) NOT NULL,
  `zhengshuhao` varchar(20) DEFAULT NULL,
  `zhong` varchar(20) DEFAULT NULL,
  `yanse` varchar(20) DEFAULT NULL,
  `jingdu` varchar(20) DEFAULT NULL,
  `qiegong` varchar(20) DEFAULT NULL,
  `duichen` varchar(20) DEFAULT NULL,
  `paoguang` varchar(20) DEFAULT NULL,
  `yingguang` varchar(20) DEFAULT NULL,
  `num` int(8) NOT NULL DEFAULT '1' COMMENT '石包总数',
  `zongzhong` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '石包总重量',
  `caigouchengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '每卡采购价格',
  `xiaoshouchengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '每卡销售价格',
  `add_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`og_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=42610 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for stone
-- ----------------------------
DROP TABLE IF EXISTS `stone`;
CREATE TABLE `stone` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `dia_package` varchar(30) DEFAULT NULL COMMENT '石包',
  `purchase_price` decimal(8,2) DEFAULT NULL COMMENT '每卡采购价格(元)',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态',
  `sup_id` int(10) DEFAULT NULL,
  `sup_name` varchar(150) DEFAULT NULL COMMENT '供应商',
  `specification` varchar(255) DEFAULT NULL COMMENT '规格',
  `color` varchar(20) DEFAULT NULL COMMENT '颜色',
  `neatness` varchar(20) DEFAULT NULL COMMENT '净度',
  `cut` varchar(20) DEFAULT NULL COMMENT '切工',
  `symmetry` varchar(20) DEFAULT NULL COMMENT '对称',
  `polishing` varchar(20) DEFAULT NULL COMMENT '抛光',
  `fluorescence` varchar(20) DEFAULT NULL COMMENT '荧光',
  `lose_efficacy_time` datetime DEFAULT NULL COMMENT '失效时间',
  `lose_efficacy_cause` text COMMENT '失效原因',
  `lose_efficacy_user` varchar(30) DEFAULT NULL COMMENT '失效操作人',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=159 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for stone_bill
-- ----------------------------
DROP TABLE IF EXISTS `stone_bill`;
CREATE TABLE `stone_bill` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `bill_no` varchar(30) DEFAULT NULL COMMENT '单号',
  `bill_type` varchar(30) DEFAULT NULL COMMENT '类型',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态',
  `processors_id` int(10) DEFAULT NULL COMMENT '供应商ID',
  `processors_name` varchar(100) DEFAULT NULL COMMENT '供应商',
  `factory_id` int(10) DEFAULT NULL COMMENT '工厂id',
  `factory_name` varchar(100) DEFAULT NULL COMMENT '工厂',
  `source` tinyint(1) DEFAULT NULL COMMENT '单据来源：收货单/手工入',
  `source_no` varchar(20) DEFAULT NULL COMMENT '来源收货单号',
  `price_total` decimal(8,2) DEFAULT '0.00' COMMENT '价格总计',
  `create_user` varchar(30) DEFAULT NULL COMMENT '制单人',
  `check_user` varchar(30) DEFAULT NULL COMMENT '审核人',
  `num` int(8) DEFAULT '0' COMMENT '总数量',
  `weight` decimal(8,3) DEFAULT '0.000' COMMENT '总重量',
  `paper_no` varchar(30) DEFAULT NULL COMMENT '纸质单号',
  `create_time` datetime DEFAULT NULL COMMENT '制单时间',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `remark` text COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `processors_id` (`processors_id`),
  KEY `factory_id` (`factory_id`),
  KEY `source_no` (`source_no`)
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for stone_bill_details
-- ----------------------------
DROP TABLE IF EXISTS `stone_bill_details`;
CREATE TABLE `stone_bill_details` (
  `id` bigint(30) NOT NULL AUTO_INCREMENT,
  `bill_id` int(10) NOT NULL,
  `dia_package` varchar(100) DEFAULT NULL,
  `purchase_price` decimal(8,2) DEFAULT NULL,
  `specification` varchar(255) DEFAULT NULL COMMENT '规格',
  `color` varchar(20) DEFAULT NULL,
  `neatness` varchar(20) DEFAULT NULL,
  `cut` varchar(20) DEFAULT NULL,
  `symmetry` varchar(20) DEFAULT NULL,
  `polishing` varchar(20) DEFAULT NULL,
  `fluorescence` varchar(20) DEFAULT NULL,
  `num` int(10) DEFAULT '0' COMMENT '数量',
  `weight` decimal(8,3) DEFAULT '0.000' COMMENT '重量',
  `price` decimal(8,2) DEFAULT '0.00' COMMENT '价格',
  PRIMARY KEY (`id`),
  KEY `dia_package` (`dia_package`),
  KEY `bill_id` (`bill_id`)
) ENGINE=InnoDB AUTO_INCREMENT=400 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for stone_check_log
-- ----------------------------
DROP TABLE IF EXISTS `stone_check_log`;
CREATE TABLE `stone_check_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `pro_id` int(11) NOT NULL COMMENT '采购单ID',
  `check_id` int(11) NOT NULL COMMENT '操作人ID',
  `check_user` varchar(50) DEFAULT NULL COMMENT '操作人',
  `check_time` datetime DEFAULT NULL COMMENT '操作时间',
  `is_check` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否审核',
  `check_info` varchar(255) DEFAULT NULL COMMENT '审核信息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='石包审批记录';

-- ----------------------------
-- Table structure for stone_procure
-- ----------------------------
DROP TABLE IF EXISTS `stone_procure`;
CREATE TABLE `stone_procure` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `pro_sn` char(14) DEFAULT NULL COMMENT '采购单号',
  `pro_type` tinyint(4) DEFAULT NULL COMMENT '采购方式',
  `pro_ct` float(12,3) NOT NULL DEFAULT '0.000' COMMENT '采购重量',
  `pro_total` decimal(12,2) DEFAULT '0.00' COMMENT '采购总金额',
  `is_batch` tinyint(1) unsigned DEFAULT NULL COMMENT '分批收货：1=是，0=否',
  `check_plan` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '已审批人数',
  `check_status` tinyint(4) DEFAULT '0' COMMENT '审核状态:0未审核,1=审核中,2=已驳回,3=审核通过',
  `refuse_cause` varchar(255) DEFAULT NULL COMMENT '拒绝原因',
  `create_id` int(11) DEFAULT NULL COMMENT '制单人ID',
  `create_user` varchar(50) DEFAULT NULL COMMENT '制单人',
  `create_time` datetime DEFAULT NULL COMMENT '制单时间',
  `note` varchar(200) DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='石包采购单';

-- ----------------------------
-- Table structure for stone_procure_details
-- ----------------------------
DROP TABLE IF EXISTS `stone_procure_details`;
CREATE TABLE `stone_procure_details` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `pro_id` int(11) DEFAULT NULL COMMENT '采购单ID',
  `keep_ct` float(12,3) DEFAULT NULL COMMENT '库存重量',
  `keep_num` int(11) DEFAULT NULL COMMENT '库存数量',
  `pro_ct` float(12,3) DEFAULT NULL COMMENT '采购重量',
  `ct_norms` smallint(6) DEFAULT NULL COMMENT '重量规格',
  `color_norms` smallint(6) DEFAULT NULL COMMENT '颜色规格',
  `clarity_norms` smallint(6) DEFAULT NULL COMMENT '净度规格',
  `pro_budget` decimal(12,2) DEFAULT NULL COMMENT '采购预算',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='石包采购明细';
