/*
快递公司表
*/

SET FOREIGN_KEY_CHECKS=0;

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='快递公司';
