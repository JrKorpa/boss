/*
Navicat MySQL Data Transfer

Source Server         : mysql_192.168.0.95_3306
Source Server Version : 50626
Source Host           : 192.168.0.95:3306
Source Database       : warehouse_shipping

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2019-09-09 10:24:44
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for biaoqianjia
-- ----------------------------
DROP TABLE IF EXISTS `biaoqianjia`;
CREATE TABLE `biaoqianjia` (
  `goods_id` bigint(30) DEFAULT NULL,
  `bprice` decimal(10,2) DEFAULT NULL,
  UNIQUE KEY `goods_id` (`goods_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for biaoqianjia002
-- ----------------------------
DROP TABLE IF EXISTS `biaoqianjia002`;
CREATE TABLE `biaoqianjia002` (
  `goods_id` bigint(30) DEFAULT NULL,
  `bprice` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for biaoqianjia01
-- ----------------------------
DROP TABLE IF EXISTS `biaoqianjia01`;
CREATE TABLE `biaoqianjia01` (
  `goods_id` bigint(30) DEFAULT NULL,
  `bprice` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for biaoqianjia03
-- ----------------------------
DROP TABLE IF EXISTS `biaoqianjia03`;
CREATE TABLE `biaoqianjia03` (
  `goods_id` bigint(30) DEFAULT NULL,
  `bprice` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for biaoqianjia_yancheng
-- ----------------------------
DROP TABLE IF EXISTS `biaoqianjia_yancheng`;
CREATE TABLE `biaoqianjia_yancheng` (
  `goods_id` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for bill_new
-- ----------------------------
DROP TABLE IF EXISTS `bill_new`;
CREATE TABLE `bill_new` (
  `bill_no` char(17) DEFAULT NULL,
  `bill_type` varchar(14) DEFAULT NULL,
  `goods_id` bigint(30) DEFAULT NULL,
  `goods_sn` varchar(30) DEFAULT NULL,
  `goods_name` varchar(150) DEFAULT NULL,
  `prc_name` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for bill_sync
-- ----------------------------
DROP TABLE IF EXISTS `bill_sync`;
CREATE TABLE `bill_sync` (
  `sync_id` int(11) NOT NULL AUTO_INCREMENT,
  `bill_id` int(11) NOT NULL,
  `bill_no` varchar(20) NOT NULL,
  `latest_push_time` datetime DEFAULT NULL,
  `latest_pull_time` datetime DEFAULT NULL,
  PRIMARY KEY (`sync_id`),
  UNIQUE KEY `id_no` (`bill_id`,`bill_no`) USING HASH
) ENGINE=InnoDB AUTO_INCREMENT=393 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for bill_total
-- ----------------------------
DROP TABLE IF EXISTS `bill_total`;
CREATE TABLE `bill_total` (
  `bill_no` char(17) DEFAULT NULL,
  `bill_type` varchar(14) DEFAULT NULL,
  `goods_id` bigint(30) DEFAULT NULL,
  `goods_sn` varchar(30) DEFAULT NULL,
  `goods_name` varchar(150) DEFAULT NULL,
  `prc_name` varchar(30) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for boss_goods
-- ----------------------------
DROP TABLE IF EXISTS `boss_goods`;
CREATE TABLE `boss_goods` (
  `goods_id` bigint(30) NOT NULL DEFAULT '0' COMMENT '货号',
  `goods_sn` varchar(50) DEFAULT NULL COMMENT '款号',
  `product_type` varchar(50) DEFAULT NULL COMMENT '新产品线',
  `cat_type` varchar(50) DEFAULT NULL COMMENT '新款式分类',
  `is_on_sale` int(3) NOT NULL DEFAULT '1' COMMENT '见数据字典',
  `prc_id` int(4) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `prc_name` varchar(100) DEFAULT NULL COMMENT '供货商名称',
  `put_in_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '入库方式 见数据字典',
  `goods_name` varchar(100) DEFAULT NULL COMMENT '商品名称',
  `company` varchar(100) NOT NULL COMMENT '公司',
  `warehouse` varchar(30) NOT NULL COMMENT '仓库',
  `company_id` int(11) NOT NULL COMMENT '公司ID',
  `warehouse_id` int(11) NOT NULL COMMENT '仓库D',
  `caizhi` varchar(20) DEFAULT NULL COMMENT '材质',
  `jinzhong` decimal(8,3) DEFAULT NULL COMMENT '金重',
  `jinhao` varchar(50) DEFAULT '0' COMMENT '金耗',
  `zongzhong` varchar(50) DEFAULT NULL,
  `shoucun` varchar(50) DEFAULT NULL COMMENT '手寸',
  `kela_order_sn` varchar(40) DEFAULT NULL COMMENT '订单号',
  `buchan_sn` varchar(20) DEFAULT NULL COMMENT '布产号',
  `order_detail_id` varchar(10) DEFAULT NULL,
  `pinpai` varchar(100) DEFAULT NULL COMMENT '品牌',
  `changdu` varchar(100) DEFAULT NULL COMMENT '长度',
  `zhengshuhao` varchar(100) DEFAULT NULL COMMENT '证书号',
  `zhengshuhao2` varchar(30) DEFAULT NULL COMMENT '证书号2',
  `peijianshuliang` varchar(50) DEFAULT NULL COMMENT '配件数量',
  `guojizhengshu` varchar(50) DEFAULT NULL COMMENT '国际证书',
  `zhengshuleibie` varchar(50) DEFAULT NULL COMMENT '证书类别',
  `gemx_zhengshu` varchar(64) DEFAULT NULL,
  `num` int(6) NOT NULL DEFAULT '1',
  `addtime` datetime DEFAULT NULL,
  `yanse` varchar(50) DEFAULT NULL COMMENT '颜色',
  `jingdu` varchar(50) DEFAULT NULL COMMENT '净度',
  `qiegong` varchar(10) DEFAULT NULL COMMENT '切工',
  `paoguang` varchar(10) DEFAULT NULL COMMENT '抛光',
  `duichen` varchar(10) DEFAULT NULL COMMENT '对称',
  `yingguang` varchar(10) DEFAULT NULL COMMENT '荧光',
  `zuanshizhekou` varchar(11) DEFAULT NULL,
  `jinse` varchar(3) NOT NULL DEFAULT '',
  `guojibaojia` varchar(20) DEFAULT NULL COMMENT '裸钻国际报价',
  `luozuanzhengshu` varchar(100) DEFAULT NULL COMMENT '裸钻证书类型',
  `tuo_type` tinyint(3) NOT NULL DEFAULT '1',
  `huopin_type` int(1) NOT NULL DEFAULT '2' COMMENT '货品类型；0为A类；1为B类；2为C类；',
  `dia_sn` varchar(4) DEFAULT NULL COMMENT '钻石代码（色阶+净度）',
  `zhushipipeichengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改主石匹配的成本-AB货',
  `biaoqianjia` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '标签价',
  `jietuoxiangkou` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '戒托镶口',
  `box_sn` varchar(30) DEFAULT '0-00-0-0' COMMENT '默认柜位',
  `jiejia` tinyint(1) DEFAULT NULL COMMENT '是否结价：1=是,0=否',
  `weixiu_status` tinyint(2) DEFAULT '0' COMMENT '货品维修状态 数字字典warehouse.weixiu_status',
  `change_time` datetime DEFAULT NULL COMMENT '最后一次转仓时间',
  `weixiu_company_id` int(10) NOT NULL DEFAULT '0' COMMENT '维修入库公司id',
  `weixiu_company_name` varchar(30) NOT NULL DEFAULT '' COMMENT '维修入库公司名称',
  `weixiu_warehouse_id` int(10) NOT NULL DEFAULT '0' COMMENT '维修入库仓库id',
  `weixiu_warehouse_name` varchar(30) NOT NULL DEFAULT '' COMMENT '维修入库仓库名称',
  `chuku_time` datetime DEFAULT NULL COMMENT '出库时间',
  `color_grade` varchar(50) DEFAULT NULL COMMENT '颜色等级',
  `xinyaozhanshi` tinyint(1) DEFAULT '0' COMMENT '星耀钻石 1:否；2:是',
  `peijianjinzhong` decimal(8,3) DEFAULT NULL COMMENT '配件金重',
  `zhushi` varchar(50) DEFAULT NULL COMMENT '主石',
  `zhushilishu` varchar(11) NOT NULL DEFAULT '0' COMMENT '主石粒数',
  `zuanshidaxiao` decimal(10,3) DEFAULT '0.000' COMMENT '主石大小',
  `zhushizhongjijia` varchar(50) DEFAULT NULL COMMENT '主石总计价',
  `zhushiyanse` varchar(50) DEFAULT NULL COMMENT '主石颜色',
  `zhushijingdu` varchar(50) DEFAULT NULL COMMENT '主石净度',
  `zhushiqiegong` varchar(50) DEFAULT NULL COMMENT '主石切工',
  `zhushixingzhuang` varchar(50) DEFAULT NULL COMMENT '主石形状',
  `zhushibaohao` varchar(60) DEFAULT NULL COMMENT '主石包号',
  `zhushiguige` varchar(60) DEFAULT NULL COMMENT '主石规格',
  `zhushitiaoma` varchar(100) DEFAULT NULL COMMENT '主石条码',
  `fushi` varchar(50) DEFAULT NULL COMMENT '副石',
  `fushilishu` varchar(50) DEFAULT NULL COMMENT '副石粒数',
  `fushizhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `fushizhongjijia` varchar(50) DEFAULT NULL,
  `fushibaohao` varchar(50) DEFAULT NULL,
  `fushiguige` varchar(50) DEFAULT NULL,
  `fushiyanse` varchar(50) DEFAULT NULL,
  `fushijingdu` varchar(50) DEFAULT NULL,
  `fushixingzhuang` varchar(50) DEFAULT NULL,
  `shi2` varchar(40) DEFAULT NULL COMMENT '副石2',
  `shi2lishu` varchar(40) DEFAULT NULL COMMENT '副石2粒数',
  `shi2zhong` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '副石2重',
  `shi2zhongjijia` varchar(40) DEFAULT NULL COMMENT '副石2总计价',
  `shi2baohao` varchar(60) DEFAULT NULL COMMENT '石2包号',
  `shi3` varchar(40) DEFAULT NULL COMMENT '副石3',
  `shi3lishu` varchar(40) DEFAULT NULL COMMENT '副石3粒数',
  `shi3zhong` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '副石3重',
  `shi3zhongjijia` varchar(40) DEFAULT NULL COMMENT '副石3总计价',
  `shi3baohao` varchar(60) DEFAULT NULL COMMENT '石3包号',
  `yuanshichengbenjia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原始成本价',
  `mingyichengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '现在成本',
  `jijiachengben` varchar(50) DEFAULT NULL COMMENT '计价成本',
  `update_time` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `store_id` binary(0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for boss_warehouse
-- ----------------------------
DROP TABLE IF EXISTS `boss_warehouse`;
CREATE TABLE `boss_warehouse` (
  `id` int(4) unsigned NOT NULL DEFAULT '0',
  `name` varchar(20) NOT NULL COMMENT '仓库名称',
  `code` varchar(20) NOT NULL,
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `create_time` datetime DEFAULT NULL,
  `create_user` varchar(20) DEFAULT NULL,
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否有效；1为有效，0为无效',
  `lock` tinyint(1) NOT NULL DEFAULT '0' COMMENT '锁定状态 0未锁定/1锁定',
  `type` int(2) NOT NULL DEFAULT '0' COMMENT '仓库类型',
  `diamond_warehouse` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否裸钻库 0否,1是',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '默认上架 0 否,1 是',
  `company_id` int(10) NOT NULL COMMENT '公司关联id',
  `company_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '名称',
  `store_id` int(1) NOT NULL DEFAULT '0',
  `store_name` varchar(41) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for box_goods_log
-- ----------------------------
DROP TABLE IF EXISTS `box_goods_log`;
CREATE TABLE `box_goods_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `goods_id` varchar(35) NOT NULL DEFAULT '' COMMENT '货号',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类型（上架，下架） 参考数字字典（box_goods_type）',
  `create_time` datetime DEFAULT NULL COMMENT '操作时间',
  `create_user` varchar(35) NOT NULL DEFAULT '' COMMENT '操作人',
  `warehouse` varchar(65) NOT NULL DEFAULT '' COMMENT '仓库',
  `box_sn` varchar(25) NOT NULL DEFAULT '' COMMENT '柜位',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2824700 DEFAULT CHARSET=utf8 COMMENT='上下架日志';

-- ----------------------------
-- Table structure for ckg
-- ----------------------------
DROP TABLE IF EXISTS `ckg`;
CREATE TABLE `ckg` (
  `goods_id` varchar(255) DEFAULT NULL,
  `boss_kc` varchar(255) DEFAULT NULL,
  `boss_bill_no` varchar(255) DEFAULT NULL,
  `zt_kc` varchar(255) DEFAULT NULL,
  `zt_bill_no` varchar(255) DEFAULT NULL,
  `zt_tiaoma_goods_id` varchar(255) DEFAULT NULL,
  `zt_tiaoma_kc` varchar(255) DEFAULT NULL,
  `zt_tiaoma_no` varchar(255) DEFAULT NULL,
  `zhengshuhao` varchar(255) DEFAULT NULL,
  `zt_zhengshuhao_goods_id` varchar(255) DEFAULT NULL,
  `zt_zhengshuhao_bill_no` varchar(255) DEFAULT NULL,
  `zt_zhengshuhao_goods_kc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for company_ec_rel2
-- ----------------------------
DROP TABLE IF EXISTS `company_ec_rel2`;
CREATE TABLE `company_ec_rel2` (
  `ec_id` varchar(60) DEFAULT NULL,
  `company_id` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ddd
-- ----------------------------
DROP TABLE IF EXISTS `ddd`;
CREATE TABLE `ddd` (
  `minid` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='(快递对账表)';

-- ----------------------------
-- Table structure for fix_pfj
-- ----------------------------
DROP TABLE IF EXISTS `fix_pfj`;
CREATE TABLE `fix_pfj` (
  `goods_id` bigint(30) NOT NULL DEFAULT '0' COMMENT '货号',
  `company_id` int(11) NOT NULL COMMENT '公司ID',
  `price` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for goods_io
-- ----------------------------
DROP TABLE IF EXISTS `goods_io`;
CREATE TABLE `goods_io` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` bigint(30) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `in_time` datetime DEFAULT NULL COMMENT '入库时间',
  `out_time` datetime DEFAULT NULL COMMENT '出库时间',
  `birth_time` datetime DEFAULT NULL COMMENT '货号进入kela时间',
  `in_bill_no` varchar(255) DEFAULT NULL,
  `out_bill_no` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `io` (`goods_id`,`warehouse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=408230 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for goods_list_new
-- ----------------------------
DROP TABLE IF EXISTS `goods_list_new`;
CREATE TABLE `goods_list_new` (
  `goods_id` bigint(30) NOT NULL DEFAULT '0' COMMENT '货号',
  `goods_sn` varchar(50) DEFAULT NULL COMMENT '款号',
  `goods_name` varchar(100) DEFAULT NULL COMMENT '名称',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `caizhi` varchar(20) DEFAULT NULL COMMENT '材质',
  `bcaizhi` varchar(20) DEFAULT NULL COMMENT 'b材质',
  `jinzhong` decimal(8,3) DEFAULT NULL COMMENT '金重',
  `shoucun` varchar(50) DEFAULT NULL COMMENT '手寸',
  `chengbenjia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `zhengshuhao` varchar(100) DEFAULT NULL COMMENT '证书号',
  `type` char(1) NOT NULL COMMENT '出入库类型',
  `jietuoxiangkou` decimal(10,3) NOT NULL COMMENT '镶口',
  `saleStatus` varchar(20) NOT NULL COMMENT '状态',
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='仓库货品表';

-- ----------------------------
-- Table structure for goods_list_total
-- ----------------------------
DROP TABLE IF EXISTS `goods_list_total`;
CREATE TABLE `goods_list_total` (
  `goods_id` bigint(30) NOT NULL DEFAULT '0' COMMENT '货号',
  `goods_sn` varchar(50) DEFAULT NULL COMMENT '款号',
  `goods_name` varchar(100) DEFAULT NULL COMMENT '名称',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `caizhi` varchar(20) DEFAULT NULL COMMENT '材质',
  `bcaizhi` varchar(20) DEFAULT NULL COMMENT 'b材质',
  `jinzhong` decimal(8,3) DEFAULT NULL COMMENT '金重',
  `shoucun` varchar(50) DEFAULT NULL COMMENT '手寸',
  `chengbenjia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `zhengshuhao` varchar(100) DEFAULT NULL COMMENT '证书号',
  `type` char(1) NOT NULL COMMENT '出入库类型',
  `jietuoxiangkou` decimal(10,3) NOT NULL COMMENT '镶口',
  `saleStatus` varchar(20) NOT NULL COMMENT '状态',
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='仓库货品表';

-- ----------------------------
-- Table structure for goods_warehouse
-- ----------------------------
DROP TABLE IF EXISTS `goods_warehouse`;
CREATE TABLE `goods_warehouse` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `good_id` bigint(30) NOT NULL COMMENT '货号',
  `warehouse_id` int(10) NOT NULL DEFAULT '0' COMMENT '仓库ID 关联warehouse表ID',
  `box_id` int(10) NOT NULL DEFAULT '0' COMMENT '柜位ID 关联warehouse_box表主键',
  `add_time` datetime DEFAULT NULL COMMENT '入库时间 ',
  `create_time` datetime DEFAULT NULL COMMENT '上架时间',
  `create_user` varchar(35) NOT NULL COMMENT '上架操作人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `good_id_2` (`good_id`),
  KEY `good_id` (`good_id`) USING BTREE,
  KEY `warehouse_id` (`warehouse_id`) USING BTREE,
  KEY `box_id` (`box_id`) USING BTREE,
  KEY `add_time` (`add_time`)
) ENGINE=InnoDB AUTO_INCREMENT=938137 DEFAULT CHARSET=utf8 COMMENT='货品与仓库的关系表';

-- ----------------------------
-- Table structure for goods_warehouse_170424
-- ----------------------------
DROP TABLE IF EXISTS `goods_warehouse_170424`;
CREATE TABLE `goods_warehouse_170424` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `good_id` bigint(30) NOT NULL COMMENT '货号',
  `warehouse_id` int(10) NOT NULL DEFAULT '0' COMMENT '仓库ID 关联warehouse表ID',
  `box_id` int(10) NOT NULL DEFAULT '0' COMMENT '柜位ID 关联warehouse_box表主键',
  `add_time` datetime DEFAULT NULL COMMENT '入库时间 ',
  `create_time` datetime DEFAULT NULL COMMENT '上架时间',
  `create_user` varchar(35) NOT NULL COMMENT '上架操作人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `good_id_2` (`good_id`),
  KEY `good_id` (`good_id`) USING BTREE,
  KEY `warehouse_id` (`warehouse_id`) USING BTREE,
  KEY `box_id` (`box_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=848850 DEFAULT CHARSET=utf8 COMMENT='货品与仓库的关系表';

-- ----------------------------
-- Table structure for hbh_lly
-- ----------------------------
DROP TABLE IF EXISTS `hbh_lly`;
CREATE TABLE `hbh_lly` (
  `goods_id` varchar(255) DEFAULT NULL,
  `gt` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hrd_goods_id
-- ----------------------------
DROP TABLE IF EXISTS `hrd_goods_id`;
CREATE TABLE `hrd_goods_id` (
  `goods_id` bigint(30) NOT NULL DEFAULT '0' COMMENT '货号',
  `pinpai` varchar(100) DEFAULT NULL COMMENT '品牌',
  KEY `pinpai` (`pinpai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for hrd_warehouse_goods
-- ----------------------------
DROP TABLE IF EXISTS `hrd_warehouse_goods`;
CREATE TABLE `hrd_warehouse_goods` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_id` bigint(30) NOT NULL DEFAULT '0' COMMENT '货号',
  `goods_sn` varchar(50) DEFAULT NULL COMMENT '款号',
  `buchan_sn` varchar(20) DEFAULT NULL COMMENT '布产号',
  `order_goods_id` varchar(10) NOT NULL DEFAULT '0' COMMENT '订单商品detail_id',
  `product_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '产品线',
  `cat_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '款式分类',
  `is_on_sale` int(3) NOT NULL DEFAULT '1' COMMENT '见数据字典',
  `prc_id` int(4) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `prc_name` varchar(100) DEFAULT NULL COMMENT '供货商名称',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `put_in_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '入库方式 见数据字典',
  `goods_name` varchar(100) DEFAULT NULL COMMENT '商品名称',
  `company` varchar(100) NOT NULL COMMENT '公司',
  `warehouse` varchar(30) NOT NULL COMMENT '仓库',
  `company_id` int(11) NOT NULL COMMENT '公司ID',
  `warehouse_id` int(11) NOT NULL COMMENT '仓库D',
  `caizhi` varchar(20) DEFAULT NULL COMMENT '材质',
  `jinzhong` decimal(8,3) DEFAULT NULL COMMENT '金重',
  `jinhao` varchar(50) DEFAULT '0' COMMENT '金耗',
  `zhushi` varchar(50) DEFAULT NULL COMMENT '主石',
  `zhuchengsezhongjijia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '主成色总计价',
  `zhuchengsemairudanjia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '主成色买入单价',
  `zhuchengsemairuchengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '主成色买入成本',
  `zhuchengsejijiadanjia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '主成色计价单价',
  `zhushilishu` varchar(11) NOT NULL DEFAULT '0' COMMENT '主石粒数',
  `zuanshidaxiao` decimal(10,3) DEFAULT '0.000' COMMENT '主石大小',
  `zhushizhongjijia` varchar(50) DEFAULT NULL COMMENT '主石总计价',
  `zhushiyanse` varchar(50) DEFAULT NULL COMMENT '主石颜色',
  `zhushijingdu` varchar(50) DEFAULT NULL COMMENT '主石净度',
  `zhushimairudanjia` decimal(10,2) DEFAULT '0.00' COMMENT '主石买入单价',
  `zhushimairuchengben` decimal(10,2) DEFAULT '0.00' COMMENT '主石买入成本',
  `zhushijijiadanjia` decimal(10,2) DEFAULT '0.00' COMMENT '主石计价单价',
  `zhushiqiegong` varchar(50) DEFAULT NULL COMMENT '主石切工',
  `zhushixingzhuang` varchar(50) DEFAULT NULL COMMENT '主石形状',
  `zhushibaohao` varchar(60) DEFAULT NULL COMMENT '主石包号',
  `zhushiguige` varchar(60) DEFAULT NULL COMMENT '主石规格',
  `fushi` varchar(50) DEFAULT NULL COMMENT '副石',
  `fushilishu` varchar(50) DEFAULT NULL COMMENT '副石粒数',
  `fushizhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `fushizhongjijia` varchar(50) DEFAULT NULL,
  `fushiyanse` varchar(50) DEFAULT NULL,
  `fushijingdu` varchar(50) DEFAULT NULL,
  `fushimairuchengben` decimal(8,2) DEFAULT '0.00',
  `fushimairudanjia` decimal(8,2) NOT NULL DEFAULT '0.00',
  `fushijijiadanjia` decimal(8,2) NOT NULL DEFAULT '0.00',
  `fushixingzhuang` varchar(50) DEFAULT NULL,
  `fushibaohao` varchar(50) DEFAULT NULL,
  `fushiguige` varchar(50) DEFAULT NULL,
  `zongzhong` varchar(50) DEFAULT NULL,
  `mairugongfeidanjia` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '买入工费单价',
  `mairugongfei` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '买入工费',
  `jijiagongfei` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '计价工费',
  `shoucun` varchar(50) DEFAULT NULL COMMENT '手寸',
  `ziyin` varchar(50) DEFAULT NULL COMMENT '字印',
  `danjianchengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '单价成本',
  `peijianchengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '配件成本',
  `qitachengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '其他成本',
  `yuanshichengbenjia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原始成本价',
  `chengbenjia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `jijiachengben` varchar(50) DEFAULT NULL COMMENT '计价成本',
  `jiajialv` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '加价率',
  `kela_order_sn` varchar(40) DEFAULT NULL COMMENT '订单号',
  `zuixinlingshoujia` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pinpai` varchar(100) DEFAULT NULL COMMENT '品牌',
  `changdu` varchar(100) DEFAULT NULL COMMENT '长度',
  `zhengshuhao` varchar(100) DEFAULT NULL COMMENT '证书号',
  `zhengshuhao2` varchar(30) DEFAULT NULL COMMENT '证书号2',
  `yanse` varchar(50) DEFAULT NULL COMMENT '颜色',
  `jingdu` varchar(50) DEFAULT NULL COMMENT '净度',
  `peijianshuliang` varchar(50) DEFAULT NULL COMMENT '配件数量',
  `guojizhengshu` varchar(50) DEFAULT NULL COMMENT '国际证书',
  `zhengshuleibie` varchar(50) DEFAULT NULL COMMENT '证书类别',
  `gemx_zhengshu` varchar(64) DEFAULT NULL,
  `num` int(6) NOT NULL DEFAULT '1',
  `addtime` datetime DEFAULT NULL,
  `shi2` varchar(40) DEFAULT NULL COMMENT '副石2',
  `shi2lishu` varchar(40) DEFAULT NULL COMMENT '副石2粒数',
  `shi2zhong` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '副石2重',
  `shi2zhongjijia` varchar(40) DEFAULT NULL COMMENT '副石2总计价',
  `shi2mairudanjia` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '副石2买入单价',
  `shi2mairuchengben` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '副石2买入成本',
  `shi2jijiadanjia` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '副石2计价单价',
  `qiegong` varchar(10) DEFAULT NULL COMMENT '切工',
  `paoguang` varchar(10) DEFAULT NULL COMMENT '抛光',
  `duichen` varchar(10) DEFAULT NULL COMMENT '对称',
  `yingguang` varchar(10) DEFAULT NULL COMMENT '荧光',
  `mingyichengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '现在成本',
  `xianzaixiaoshou` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '现在销售',
  `zuanshizhekou` varchar(11) DEFAULT NULL,
  `guojibaojia` varchar(20) DEFAULT NULL COMMENT '裸钻国际报价',
  `gongchangchengben` varchar(20) DEFAULT NULL COMMENT '工厂成本',
  `account` tinyint(3) NOT NULL DEFAULT '0',
  `account_time` datetime DEFAULT NULL,
  `tuo_type` tinyint(3) NOT NULL DEFAULT '1',
  `att1` varchar(30) DEFAULT NULL,
  `att2` varchar(30) DEFAULT NULL,
  `huopin_type` int(1) NOT NULL DEFAULT '2' COMMENT '货品类型；0为A类；1为B类；2为C类；',
  `dia_sn` varchar(4) DEFAULT NULL COMMENT '钻石代码（色阶+净度）',
  `zhushipipeichengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改主石匹配的成本-AB货',
  `biaoqianjia` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '标签价',
  `jietuoxiangkou` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '戒托镶口',
  `caigou_chengbenjia` decimal(10,2) NOT NULL DEFAULT '0.00',
  `box_sn` varchar(30) DEFAULT '0-00-0-0' COMMENT '默认柜位',
  `oldsys_id` int(10) unsigned DEFAULT '0',
  `pass_sale` tinyint(1) DEFAULT '0' COMMENT '可销售商品推送',
  `old_set_w` tinyint(1) unsigned DEFAULT '0' COMMENT '导数据专用',
  `weixiu_status` tinyint(2) DEFAULT '0' COMMENT '货品维修状态 数字字典warehouse.weixiu_status',
  `jiejia` tinyint(1) DEFAULT NULL COMMENT '是否结价：1=是,0=否',
  `change_time` datetime DEFAULT NULL COMMENT '最后一次转仓时间',
  `weixiu_company_id` int(10) NOT NULL DEFAULT '0' COMMENT '维修入库公司id',
  `weixiu_company_name` varchar(30) NOT NULL DEFAULT '' COMMENT '维修入库公司名称',
  `weixiu_warehouse_id` int(10) NOT NULL DEFAULT '0' COMMENT '维修入库仓库id',
  `weixiu_warehouse_name` varchar(30) NOT NULL DEFAULT '' COMMENT '维修入库仓库名称',
  `zhushitiaoma` varchar(30) DEFAULT NULL COMMENT '主石条码',
  KEY `pinpai` (`pinpai`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for imp_1022
-- ----------------------------
DROP TABLE IF EXISTS `imp_1022`;
CREATE TABLE `imp_1022` (
  `产品线` varchar(255) DEFAULT NULL,
  `新产品线` varchar(255) DEFAULT NULL,
  `款式分类` varchar(255) DEFAULT NULL,
  `新款式分类` varchar(255) DEFAULT NULL,
  `货号` varchar(255) DEFAULT NULL,
  `供应商` varchar(255) DEFAULT NULL,
  `入库方式` varchar(255) DEFAULT NULL,
  `状态` varchar(255) DEFAULT NULL,
  `所在仓库` varchar(255) DEFAULT NULL,
  `款号` varchar(255) DEFAULT NULL,
  `模号` varchar(255) DEFAULT NULL,
  `名称` varchar(255) DEFAULT NULL,
  `名义价` varchar(255) DEFAULT NULL,
  `原始采购价` varchar(255) DEFAULT NULL,
  `最新采购价` varchar(255) DEFAULT NULL,
  `材质` varchar(255) DEFAULT NULL,
  `金重` varchar(255) DEFAULT NULL,
  `手寸` varchar(255) DEFAULT NULL,
  `金托类型` varchar(255) DEFAULT NULL,
  `主石` varchar(255) DEFAULT NULL,
  `主石粒数` varchar(255) DEFAULT NULL,
  `主石形状` varchar(255) DEFAULT NULL,
  `主石大小` varchar(255) DEFAULT NULL,
  `主石颜色` varchar(255) DEFAULT NULL,
  `主石净度` varchar(255) DEFAULT NULL,
  `主石切工` varchar(255) DEFAULT NULL,
  `抛光` varchar(255) DEFAULT NULL,
  `对称` varchar(255) DEFAULT NULL,
  `荧光` varchar(255) DEFAULT NULL,
  `主石规格` varchar(255) DEFAULT NULL,
  `副石1` varchar(255) DEFAULT NULL,
  `副石1粒数` varchar(255) DEFAULT NULL,
  `副石1重` varchar(255) DEFAULT NULL,
  `副石2` varchar(255) DEFAULT NULL,
  `副石2粒数` varchar(255) DEFAULT NULL,
  `副石2重` varchar(255) DEFAULT NULL,
  `证书号` varchar(255) DEFAULT NULL,
  `证书类型` varchar(255) DEFAULT NULL,
  `金饰类型` varchar(255) DEFAULT NULL,
  `数量` varchar(255) DEFAULT NULL,
  `是否结价` varchar(255) DEFAULT NULL,
  `是否绑定` varchar(255) DEFAULT NULL,
  `所在公司` varchar(255) DEFAULT NULL,
  `戒托实际镶口` varchar(255) DEFAULT NULL,
  `维修状态` varchar(255) DEFAULT NULL,
  `维修公司` varchar(255) DEFAULT NULL,
  `维修仓库` varchar(255) DEFAULT NULL,
  `金耗` varchar(255) DEFAULT NULL,
  `最后销售时间` varchar(255) DEFAULT NULL,
  `本库库龄` varchar(255) DEFAULT NULL,
  `库龄` varchar(255) DEFAULT NULL,
  `国际报价` varchar(255) DEFAULT NULL,
  `折扣` varchar(255) DEFAULT NULL,
  `品牌` varchar(255) DEFAULT NULL,
  `裸钻证书类型` varchar(255) DEFAULT NULL,
  `供应商货品条码` varchar(255) DEFAULT NULL,
  `系列及款式归属` varchar(255) DEFAULT NULL,
  `柜位` varchar(255) DEFAULT NULL,
  `主石买入单价` varchar(255) DEFAULT NULL,
  `主石买入成本` varchar(255) DEFAULT NULL,
  `主石计价单价` varchar(255) DEFAULT NULL,
  `副石买入单价` varchar(255) DEFAULT NULL,
  `副石买入成本` varchar(255) DEFAULT NULL,
  `副石计价单价` varchar(255) DEFAULT NULL,
  `石2买入单价` varchar(255) DEFAULT NULL,
  `石2买入成本` varchar(255) DEFAULT NULL,
  `石2计价单价` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for innodb_lock_monitor
-- ----------------------------
DROP TABLE IF EXISTS `innodb_lock_monitor`;
CREATE TABLE `innodb_lock_monitor` (
  `x` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for innodb_monitor
-- ----------------------------
DROP TABLE IF EXISTS `innodb_monitor`;
CREATE TABLE `innodb_monitor` (
  `a` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for jxc_goods
-- ----------------------------
DROP TABLE IF EXISTS `jxc_goods`;
CREATE TABLE `jxc_goods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` varchar(30) DEFAULT NULL COMMENT '货号',
  `goods_sn` varchar(50) DEFAULT NULL COMMENT '款号',
  `buchan_sn` varchar(20) DEFAULT NULL COMMENT '布产号',
  `order_goods_id` varchar(10) NOT NULL DEFAULT '0' COMMENT '订单商品detail_id',
  `product_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '产品线',
  `cat_type` varchar(50) NOT NULL DEFAULT '0' COMMENT '款式分类',
  `is_on_sale` int(3) NOT NULL DEFAULT '1' COMMENT '见数据字典',
  `prc_id` int(4) NOT NULL DEFAULT '0' COMMENT '供应商ID',
  `prc_name` varchar(100) DEFAULT NULL COMMENT '供货商名称',
  `mo_sn` varchar(50) DEFAULT NULL COMMENT '模号',
  `put_in_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '入库方式 见数据字典',
  `goods_name` varchar(100) DEFAULT NULL COMMENT '商品名称',
  `company` varchar(100) NOT NULL COMMENT '公司',
  `warehouse` varchar(30) NOT NULL COMMENT '仓库',
  `company_id` int(11) NOT NULL COMMENT '公司ID',
  `warehouse_id` int(11) NOT NULL COMMENT '仓库D',
  `caizhi` varchar(20) DEFAULT NULL COMMENT '材质',
  `jinzhong` decimal(8,3) DEFAULT NULL COMMENT '金重',
  `jinhao` varchar(50) DEFAULT NULL COMMENT '金耗',
  `zhushi` varchar(50) DEFAULT NULL COMMENT '主石',
  `zhuchengsezhongjijia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '主成色总计价',
  `zhuchengsemairudanjia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '主成色买入单价',
  `zhuchengsemairuchengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '主成色买入成本',
  `zhuchengsejijiadanjia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '主成色计价单价',
  `zhushilishu` varchar(11) NOT NULL DEFAULT '0' COMMENT '主石粒数',
  `zuanshidaxiao` decimal(10,3) DEFAULT '0.000' COMMENT '主石大小',
  `zhushizhongjijia` varchar(50) DEFAULT NULL COMMENT '主石总计价',
  `zhushiyanse` varchar(50) DEFAULT NULL COMMENT '主石颜色',
  `zhushijingdu` varchar(50) DEFAULT NULL COMMENT '主石净度',
  `zhushimairudanjia` decimal(10,2) DEFAULT '0.00' COMMENT '主石买入单价',
  `zhushimairuchengben` decimal(10,2) DEFAULT '0.00' COMMENT '主石买入成本',
  `zhushijijiadanjia` decimal(10,2) DEFAULT '0.00' COMMENT '主石计价单价',
  `zhushiqiegong` varchar(50) DEFAULT NULL COMMENT '主石切工',
  `zhushixingzhuang` varchar(50) DEFAULT NULL COMMENT '主石形状',
  `zhushibaohao` varchar(60) DEFAULT NULL COMMENT '主石包号',
  `zhushiguige` varchar(60) DEFAULT NULL COMMENT '主石规格',
  `fushi` varchar(50) DEFAULT NULL COMMENT '副石',
  `fushilishu` varchar(50) DEFAULT NULL COMMENT '副石粒数',
  `fushizhong` decimal(8,3) NOT NULL DEFAULT '0.000',
  `fushizhongjijia` varchar(50) DEFAULT NULL,
  `fushiyanse` varchar(50) DEFAULT NULL,
  `fushijingdu` varchar(50) DEFAULT NULL,
  `fushimairuchengben` decimal(8,2) DEFAULT '0.00',
  `fushimairudanjia` decimal(8,2) NOT NULL DEFAULT '0.00',
  `fushijijiadanjia` decimal(8,2) NOT NULL DEFAULT '0.00',
  `fushixingzhuang` varchar(50) DEFAULT NULL,
  `fushibaohao` varchar(50) DEFAULT NULL,
  `fushiguige` varchar(50) DEFAULT NULL,
  `zongzhong` varchar(50) DEFAULT NULL,
  `mairugongfeidanjia` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '买入工费单价',
  `mairugongfei` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '买入工费',
  `jijiagongfei` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '计价工费',
  `shoucun` varchar(50) DEFAULT NULL COMMENT '手寸',
  `ziyin` varchar(50) DEFAULT NULL COMMENT '字印',
  `danjianchengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '单价成本',
  `peijianchengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '配件成本',
  `qitachengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '其他成本',
  `yuanshichengbenjia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '原始成本价',
  `chengbenjia` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `jijiachengben` varchar(50) DEFAULT NULL COMMENT '计价成本',
  `jiajialv` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '加价率',
  `kela_order_sn` varchar(40) DEFAULT NULL COMMENT '订单号',
  `zuixinlingshoujia` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pinpai` varchar(100) DEFAULT NULL COMMENT '品牌',
  `changdu` varchar(100) DEFAULT NULL COMMENT '长度',
  `zhengshuhao` varchar(100) DEFAULT NULL COMMENT '证书号',
  `zhengshuhao2` varchar(30) DEFAULT NULL COMMENT '证书号2',
  `yanse` varchar(50) DEFAULT NULL COMMENT '颜色',
  `jingdu` varchar(50) DEFAULT NULL COMMENT '净度',
  `peijianshuliang` varchar(50) DEFAULT NULL COMMENT '配件数量',
  `guojizhengshu` varchar(50) DEFAULT NULL COMMENT '国际证书',
  `zhengshuleibie` varchar(50) DEFAULT NULL COMMENT '证书类别',
  `gemx_zhengshu` varchar(64) DEFAULT NULL,
  `num` int(6) NOT NULL DEFAULT '1',
  `addtime` datetime DEFAULT NULL,
  `shi2` varchar(40) DEFAULT NULL COMMENT '副石2',
  `shi2lishu` varchar(40) DEFAULT NULL COMMENT '副石2粒数',
  `shi2zhong` decimal(8,3) NOT NULL DEFAULT '0.000' COMMENT '副石2重',
  `shi2zhongjijia` varchar(40) DEFAULT NULL COMMENT '副石2总计价',
  `shi2mairudanjia` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '副石2买入单价',
  `shi2mairuchengben` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '副石2买入成本',
  `shi2jijiadanjia` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '副石2计价单价',
  `qiegong` varchar(10) DEFAULT NULL COMMENT '切工',
  `paoguang` varchar(10) DEFAULT NULL COMMENT '抛光',
  `duichen` varchar(10) DEFAULT NULL COMMENT '对称',
  `yingguang` varchar(10) DEFAULT NULL COMMENT '荧光',
  `mingyichengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '现在成本',
  `xianzaixiaoshou` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '现在销售',
  `zuanshizhekou` varchar(11) DEFAULT NULL,
  `guojibaojia` varchar(20) DEFAULT NULL COMMENT '裸钻国际报价',
  `gongchangchengben` varchar(20) DEFAULT NULL COMMENT '工厂成本',
  `account` tinyint(3) NOT NULL DEFAULT '0',
  `account_time` datetime DEFAULT NULL,
  `tuo_type` tinyint(3) NOT NULL DEFAULT '1',
  `att1` varchar(30) DEFAULT NULL,
  `att2` varchar(30) DEFAULT NULL,
  `huopin_type` int(1) NOT NULL DEFAULT '2' COMMENT '货品类型；0为A类；1为B类；2为C类；',
  `dia_sn` varchar(4) DEFAULT NULL COMMENT '钻石代码（色阶+净度）',
  `zhushipipeichengben` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '修改主石匹配的成本-AB货',
  `biaoqianjia` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '标签价',
  `jietuoxiangkou` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '戒托镶口',
  `caigou_chengbenjia` decimal(10,2) NOT NULL DEFAULT '0.00',
  `box_sn` varchar(30) DEFAULT '0-00-0-0' COMMENT '默认柜位',
  `oldsys_id` int(10) unsigned DEFAULT '0',
  `pass_sale` tinyint(1) DEFAULT '0' COMMENT '可销售商品推送[导数据]',
  `old_set_w` tinyint(1) unsigned DEFAULT '0' COMMENT '导数据专用',
  `weixiu_status` tinyint(2) DEFAULT '0' COMMENT '货品维修状态 数字字典warehouse.weixiu_status',
  `jiejia` tinyint(1) DEFAULT NULL COMMENT '是否结价：1=是,0=否',
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2018 DEFAULT CHARSET=utf8 COMMENT='仓库货品表';

-- ----------------------------
-- Table structure for jxc_goods20160229
-- ----------------------------
DROP TABLE IF EXISTS `jxc_goods20160229`;
CREATE TABLE `jxc_goods20160229` (
  `goods_id` bigint(30) NOT NULL COMMENT '货号',
  `is_on_sale` int(3) NOT NULL DEFAULT '0' COMMENT '0=初始化，1=库存，2=已销售，3=转仓中，4=盘点中，5=销售中，6=冻结，7=已返厂,8=退货中，9=返厂中, 10=作废, 11=损益中,12=已报损',
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for jxc_warehouse
-- ----------------------------
DROP TABLE IF EXISTS `jxc_warehouse`;
CREATE TABLE `jxc_warehouse` (
  `wh_id` int(4) NOT NULL AUTO_INCREMENT,
  `p_id` int(4) NOT NULL DEFAULT '0' COMMENT '公司的ID(jxc_processors表)',
  `wh_sn` varchar(30) NOT NULL,
  `type` int(2) NOT NULL DEFAULT '0' COMMENT '0=其他，1=柜面，2=后柜，3=待取，4=冻结，5=赠品，6=活动，7=裸钻， 8=拆货， 9=退货',
  `level` int(4) NOT NULL DEFAULT '1',
  `status` int(2) NOT NULL DEFAULT '1' COMMENT '0=废弃，1=使用，2=盘点',
  `addtime` datetime NOT NULL,
  `wh_name` varchar(30) NOT NULL,
  `admin` varchar(30) DEFAULT NULL,
  `use_cab` int(1) NOT NULL DEFAULT '0' COMMENT '使用保险柜系统',
  PRIMARY KEY (`wh_id`)
) ENGINE=MyISAM AUTO_INCREMENT=659 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for jxc_wholesale
-- ----------------------------
DROP TABLE IF EXISTS `jxc_wholesale`;
CREATE TABLE `jxc_wholesale` (
  `wholesale_id` int(10) NOT NULL AUTO_INCREMENT,
  `wholesale_sn` varchar(10) DEFAULT NULL COMMENT '批发客户编号',
  `wholesale_name` varchar(30) DEFAULT NULL COMMENT '批发客户名称',
  `wholesale_credit` decimal(10,2) DEFAULT NULL COMMENT '授信额度',
  `wholesale_status` int(2) DEFAULT '1' COMMENT '开启状态  1=开启，0=关闭',
  `add_name` varchar(20) DEFAULT NULL COMMENT '添加人',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `sign_required` smallint(2) DEFAULT '0' COMMENT '是否需求签收',
  `sign_company` int(255) DEFAULT NULL COMMENT '签收公司',
  PRIMARY KEY (`wholesale_id`) USING BTREE,
  UNIQUE KEY `sn` (`wholesale_sn`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=253 DEFAULT CHARSET=utf8 COMMENT='批发客户管理表';

-- ----------------------------
-- Table structure for jxc_wholesale20160524
-- ----------------------------
DROP TABLE IF EXISTS `jxc_wholesale20160524`;
CREATE TABLE `jxc_wholesale20160524` (
  `wholesale_id` int(10) NOT NULL AUTO_INCREMENT,
  `wholesale_sn` varchar(10) DEFAULT NULL COMMENT '批发客户编号',
  `wholesale_name` varchar(30) DEFAULT NULL COMMENT '批发客户名称',
  `wholesale_credit` decimal(10,2) DEFAULT NULL COMMENT '授信额度',
  `wholesale_status` int(2) DEFAULT '1' COMMENT '开启状态  1=开启，0=关闭',
  `add_name` varchar(20) DEFAULT NULL COMMENT '添加人',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`wholesale_id`)
) ENGINE=MyISAM AUTO_INCREMENT=84 DEFAULT CHARSET=utf8 COMMENT='批发客户管理表';

-- ----------------------------
-- Table structure for jxc_wholesale20160615
-- ----------------------------
DROP TABLE IF EXISTS `jxc_wholesale20160615`;
CREATE TABLE `jxc_wholesale20160615` (
  `wholesale_id` int(10) NOT NULL AUTO_INCREMENT,
  `wholesale_sn` varchar(10) DEFAULT NULL COMMENT '批发客户编号',
  `wholesale_name` varchar(30) DEFAULT NULL COMMENT '批发客户名称',
  `wholesale_credit` decimal(10,2) DEFAULT NULL COMMENT '授信额度',
  `wholesale_status` int(2) DEFAULT '1' COMMENT '开启状态  1=开启，0=关闭',
  `add_name` varchar(20) DEFAULT NULL COMMENT '添加人',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`wholesale_id`)
) ENGINE=MyISAM AUTO_INCREMENT=90 DEFAULT CHARSET=utf8 COMMENT='批发客户管理表';

-- ----------------------------
-- Table structure for jxc_wholesale20160715
-- ----------------------------
DROP TABLE IF EXISTS `jxc_wholesale20160715`;
CREATE TABLE `jxc_wholesale20160715` (
  `wholesale_id` int(10) NOT NULL AUTO_INCREMENT,
  `wholesale_sn` varchar(10) DEFAULT NULL COMMENT '批发客户编号',
  `wholesale_name` varchar(30) DEFAULT NULL COMMENT '批发客户名称',
  `wholesale_credit` decimal(10,2) DEFAULT NULL COMMENT '授信额度',
  `wholesale_status` int(2) DEFAULT '1' COMMENT '开启状态  1=开启，0=关闭',
  `add_name` varchar(20) DEFAULT NULL COMMENT '添加人',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`wholesale_id`)
) ENGINE=MyISAM AUTO_INCREMENT=96 DEFAULT CHARSET=utf8 COMMENT='批发客户管理表';

-- ----------------------------
-- Table structure for jxc_wholesale_1230
-- ----------------------------
DROP TABLE IF EXISTS `jxc_wholesale_1230`;
CREATE TABLE `jxc_wholesale_1230` (
  `wholesale_id` int(10) NOT NULL AUTO_INCREMENT,
  `wholesale_sn` varchar(10) DEFAULT NULL COMMENT '批发客户编号',
  `wholesale_name` varchar(30) DEFAULT NULL COMMENT '批发客户名称',
  `wholesale_credit` decimal(10,2) DEFAULT NULL COMMENT '授信额度',
  `wholesale_status` int(2) DEFAULT '1' COMMENT '开启状态  1=开启，0=关闭',
  `add_name` varchar(20) DEFAULT NULL COMMENT '添加人',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `sign_required` smallint(2) DEFAULT '0' COMMENT '是否需求签收',
  `sign_company` int(255) DEFAULT NULL COMMENT '签收公司',
  PRIMARY KEY (`wholesale_id`)
) ENGINE=MyISAM AUTO_INCREMENT=135 DEFAULT CHARSET=utf8 COMMENT='批发客户管理表';

-- ----------------------------
-- Table structure for jxc_wholesale_170516
-- ----------------------------
DROP TABLE IF EXISTS `jxc_wholesale_170516`;
CREATE TABLE `jxc_wholesale_170516` (
  `wholesale_id` int(10) NOT NULL AUTO_INCREMENT,
  `wholesale_sn` varchar(10) DEFAULT NULL COMMENT '批发客户编号',
  `wholesale_name` varchar(30) DEFAULT NULL COMMENT '批发客户名称',
  `wholesale_credit` decimal(10,2) DEFAULT NULL COMMENT '授信额度',
  `wholesale_status` int(2) DEFAULT '1' COMMENT '开启状态  1=开启，0=关闭',
  `add_name` varchar(20) DEFAULT NULL COMMENT '添加人',
  `add_time` datetime DEFAULT NULL COMMENT '添加时间',
  `sign_required` smallint(2) DEFAULT '0' COMMENT '是否需求签收',
  `sign_company` int(255) DEFAULT NULL COMMENT '签收公司',
  PRIMARY KEY (`wholesale_id`)
) ENGINE=MyISAM AUTO_INCREMENT=161 DEFAULT CHARSET=utf8 COMMENT='批发客户管理表';

-- ----------------------------
-- Table structure for map
-- ----------------------------
DROP TABLE IF EXISTS `map`;
CREATE TABLE `map` (
  `EC` varchar(255) DEFAULT NULL,
  `BOSS` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for material_bill
-- ----------------------------
DROP TABLE IF EXISTS `material_bill`;
CREATE TABLE `material_bill` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `bill_no` char(20) NOT NULL COMMENT '单据编号',
  `bill_type` varchar(14) NOT NULL DEFAULT '1' COMMENT '单据类型',
  `supplier_id` int(8) DEFAULT NULL COMMENT '出入库供应商ID',
  `warehouse_id` int(8) DEFAULT NULL COMMENT '出入库仓库ID',
  `bill_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '数据字典：仓储单据状态（warehouse_in_status）/ 盘点单状态（warehouse.pandian_plan）',
  `department_id` int(10) DEFAULT '0' COMMENT '销售渠道',
  `bill_note` text COMMENT '备注',
  `create_user` varchar(25) NOT NULL COMMENT '制单人',
  `create_time` datetime NOT NULL COMMENT '制单时间',
  `check_user` varchar(25) DEFAULT NULL COMMENT '审核人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `from_bill_no` char(20) DEFAULT NULL COMMENT '入库单据编号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `bill_no` (`bill_no`)
) ENGINE=InnoDB AUTO_INCREMENT=916 DEFAULT CHARSET=utf8 COMMENT='物控单据表';

-- ----------------------------
-- Table structure for material_bill_goods
-- ----------------------------
DROP TABLE IF EXISTS `material_bill_goods`;
CREATE TABLE `material_bill_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bill_id` int(10) NOT NULL DEFAULT '0' COMMENT '单据id',
  `goods_sn` varchar(30) NOT NULL COMMENT '货品编号号',
  `supplier_id` int(10) unsigned DEFAULT NULL COMMENT '供应商库ID',
  `inventory_id` int(9) unsigned DEFAULT NULL COMMENT '库存ID',
  `in_warehouse_id` int(10) unsigned DEFAULT NULL COMMENT '入库仓库ID',
  `out_warehouse_id` int(10) unsigned DEFAULT NULL COMMENT '出库仓库ID',
  `batch_sn` varchar(30) NOT NULL COMMENT '批次号',
  `num` int(6) NOT NULL DEFAULT '0' COMMENT '出入库数量',
  `cost` decimal(10,2) DEFAULT NULL COMMENT '成本单价',
  `shijia` decimal(10,2) DEFAULT NULL COMMENT '销售单价',
  `goods_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bill_id` (`bill_id`),
  KEY `goods_sn` (`goods_sn`),
  KEY `supplier_id` (`supplier_id`),
  KEY `in_warehouse_id` (`in_warehouse_id`),
  KEY `out_warehouse_id` (`out_warehouse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8723 DEFAULT CHARSET=utf8 COMMENT='物控单据明细表';

-- ----------------------------
-- Table structure for material_bill_goods_jin
-- ----------------------------
DROP TABLE IF EXISTS `material_bill_goods_jin`;
CREATE TABLE `material_bill_goods_jin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bill_id` int(10) NOT NULL DEFAULT '0' COMMENT '单据id',
  `goods_sn` varchar(30) NOT NULL COMMENT '货品编号号',
  `supplier_id` int(10) unsigned DEFAULT NULL COMMENT '供应商库ID',
  `inventory_id` int(9) unsigned DEFAULT NULL COMMENT '库存ID',
  `in_warehouse_id` int(10) unsigned DEFAULT NULL COMMENT '入库仓库ID',
  `out_warehouse_id` int(10) unsigned DEFAULT NULL COMMENT '出库仓库ID',
  `batch_sn` varchar(30) NOT NULL COMMENT '批次号',
  `num` decimal(11,5) NOT NULL DEFAULT '0.00000' COMMENT '出入库数量',
  `cost` decimal(13,5) DEFAULT NULL COMMENT '成本单价',
  `shijia` decimal(13,5) DEFAULT NULL COMMENT '销售单价',
  `goods_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `bill_id` (`bill_id`),
  KEY `goods_sn` (`goods_sn`),
  KEY `supplier_id` (`supplier_id`),
  KEY `in_warehouse_id` (`in_warehouse_id`),
  KEY `out_warehouse_id` (`out_warehouse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=178 DEFAULT CHARSET=utf8 COMMENT='物控单据明细表';

-- ----------------------------
-- Table structure for material_bill_jin
-- ----------------------------
DROP TABLE IF EXISTS `material_bill_jin`;
CREATE TABLE `material_bill_jin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `bill_no` char(20) NOT NULL COMMENT '单据编号',
  `bill_type` varchar(14) NOT NULL DEFAULT '1' COMMENT '单据类型',
  `supplier_id` int(8) DEFAULT NULL COMMENT '出入库供应商ID',
  `warehouse_id` int(8) DEFAULT NULL COMMENT '出入库仓库ID',
  `bill_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '数据字典：仓储单据状态（warehouse_in_status）/ 盘点单状态（warehouse.pandian_plan）',
  `department_id` int(10) DEFAULT '0' COMMENT '销售渠道',
  `bill_note` text COMMENT '备注',
  `create_user` varchar(25) NOT NULL COMMENT '制单人',
  `create_time` datetime NOT NULL COMMENT '制单时间',
  `check_user` varchar(25) DEFAULT NULL COMMENT '审核人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  `from_bill_no` char(20) DEFAULT NULL COMMENT '入库单据编号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `bill_no` (`bill_no`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8 COMMENT='物控单据表';

-- ----------------------------
-- Table structure for material_goods
-- ----------------------------
DROP TABLE IF EXISTS `material_goods`;
CREATE TABLE `material_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `style_sn` varchar(30) NOT NULL COMMENT '款号',
  `style_name` varchar(30) NOT NULL COMMENT '款式名称',
  `goods_sn` varchar(30) NOT NULL COMMENT '货品编号',
  `goods_name` varchar(60) NOT NULL COMMENT '货品名称',
  `goods_spec` varchar(60) DEFAULT NULL COMMENT '货品规格',
  `catetory1` varchar(30) DEFAULT NULL COMMENT '分类1',
  `catetory2` varchar(30) DEFAULT NULL COMMENT '分类2',
  `catetory3` varchar(30) DEFAULT NULL COMMENT '分类3',
  `cost` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '成本价',
  `goods_sale_price` decimal(8,2) DEFAULT '0.00',
  `goods_jiajialv` decimal(8,2) DEFAULT '0.00',
  `unit` varchar(10) NOT NULL COMMENT '单位',
  `create_user` varchar(30) DEFAULT NULL COMMENT '添加人',
  `create_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_user` varchar(30) DEFAULT NULL COMMENT '修改人',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `goods_type` tinyint(1) unsigned DEFAULT '1' COMMENT '货品类型：1物料 2赠品',
  `caizhi` varchar(25) DEFAULT NULL,
  `min_qty` int(8) unsigned DEFAULT '1' COMMENT '起订量',
  `pack_qty` int(8) unsigned DEFAULT '1' COMMENT '装箱量',
  `remark` varchar(500) DEFAULT NULL,
  `goods_status` tinyint(1) unsigned DEFAULT '1' COMMENT '商品状态 1上架 2下架',
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_sn` (`goods_sn`),
  KEY `style_sn` (`style_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=666 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for material_goods_jin
-- ----------------------------
DROP TABLE IF EXISTS `material_goods_jin`;
CREATE TABLE `material_goods_jin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `style_sn` varchar(30) NOT NULL COMMENT '款号',
  `style_name` varchar(30) NOT NULL COMMENT '款式名称',
  `goods_sn` varchar(30) NOT NULL COMMENT '货品编号',
  `goods_name` varchar(60) NOT NULL COMMENT '货品名称',
  `goods_spec` varchar(60) DEFAULT NULL COMMENT '货品规格',
  `catetory1` varchar(30) DEFAULT NULL COMMENT '分类1',
  `catetory2` varchar(30) DEFAULT NULL COMMENT '分类2',
  `catetory3` varchar(30) DEFAULT NULL COMMENT '分类3',
  `cost` decimal(11,5) NOT NULL DEFAULT '0.00000' COMMENT '成本价',
  `goods_sale_price` decimal(11,5) DEFAULT '0.00000',
  `goods_jiajialv` decimal(8,2) DEFAULT '0.00',
  `unit` varchar(10) NOT NULL COMMENT '单位',
  `create_user` varchar(30) DEFAULT NULL COMMENT '添加人',
  `create_time` datetime DEFAULT NULL COMMENT '添加时间',
  `update_user` varchar(30) DEFAULT NULL COMMENT '修改人',
  `update_time` datetime DEFAULT NULL COMMENT '修改时间',
  `goods_type` tinyint(1) unsigned DEFAULT '1' COMMENT '货品类型：1物料 2赠品',
  `caizhi` varchar(25) DEFAULT NULL,
  `min_qty` int(8) unsigned DEFAULT '1' COMMENT '起订量',
  `pack_qty` int(8) unsigned DEFAULT '1' COMMENT '装箱量',
  `remark` varchar(500) DEFAULT NULL,
  `goods_status` tinyint(1) unsigned DEFAULT '1' COMMENT '商品状态 1上架 2下架',
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_sn` (`goods_sn`),
  KEY `style_sn` (`style_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for material_inventory
-- ----------------------------
DROP TABLE IF EXISTS `material_inventory`;
CREATE TABLE `material_inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_sn` varchar(30) NOT NULL COMMENT '货品编号',
  `supplier_id` int(10) NOT NULL COMMENT '供应商ID',
  `warehouse_id` int(10) NOT NULL COMMENT '库存仓库ID',
  `batch_sn` varchar(30) NOT NULL COMMENT '批次号',
  `inventory_qty` decimal(8,2) NOT NULL COMMENT '库存数量',
  `cost` decimal(8,2) NOT NULL COMMENT '成本单价',
  `version` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `goods_id` int(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_sn` (`goods_sn`,`supplier_id`,`warehouse_id`,`batch_sn`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2295 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for material_inventory_jin
-- ----------------------------
DROP TABLE IF EXISTS `material_inventory_jin`;
CREATE TABLE `material_inventory_jin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_sn` varchar(30) NOT NULL COMMENT '货品编号',
  `supplier_id` int(10) NOT NULL COMMENT '供应商ID',
  `warehouse_id` int(10) NOT NULL COMMENT '库存仓库ID',
  `batch_sn` varchar(30) NOT NULL COMMENT '批次号',
  `inventory_qty` decimal(11,5) NOT NULL COMMENT '库存数量',
  `cost` decimal(11,5) NOT NULL COMMENT '成本单价',
  `version` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `goods_id` int(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_sn` (`goods_sn`,`supplier_id`,`warehouse_id`,`batch_sn`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for material_order
-- ----------------------------
DROP TABLE IF EXISTS `material_order`;
CREATE TABLE `material_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `bill_no` char(20) NOT NULL COMMENT '单据编号',
  `bill_status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '数据字典：仓储单据状态（warehouse_in_status）/ 盘点单状态（warehouse.pandian_plan）',
  `department_id` int(10) DEFAULT '0' COMMENT '销售渠道',
  `bill_note` text COMMENT '备注',
  `address` varchar(150) DEFAULT NULL COMMENT '邮寄地址',
  `create_user` varchar(25) NOT NULL COMMENT '制单人',
  `create_time` datetime NOT NULL COMMENT '制单时间',
  `check_user` varchar(25) DEFAULT NULL COMMENT '审核人',
  `check_time` datetime DEFAULT NULL COMMENT '审核时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `bill_no` (`bill_no`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8 COMMENT='物控订单表';

-- ----------------------------
-- Table structure for material_order_goods
-- ----------------------------
DROP TABLE IF EXISTS `material_order_goods`;
CREATE TABLE `material_order_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bill_id` int(10) NOT NULL DEFAULT '0' COMMENT '单据id',
  `goods_sn` varchar(30) NOT NULL COMMENT '货品编号',
  `goods_num` int(6) NOT NULL DEFAULT '0' COMMENT '订单数量',
  `goods_price` decimal(10,2) DEFAULT '0.00' COMMENT '销售单价',
  PRIMARY KEY (`id`),
  UNIQUE KEY `bill_id_2` (`bill_id`,`goods_sn`),
  KEY `bill_id` (`bill_id`),
  KEY `goods_sn` (`goods_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8 COMMENT='物控订单明细表';
