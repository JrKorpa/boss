/*
Date: 2014-12-31 09:52:26
*/


-- ----------------------------
-- Table structure for conf_item
-- ----------------------------
DROP TABLE IF EXISTS `conf_item`;
CREATE TABLE `conf_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `item` varchar(50) DEFAULT NULL COMMENT '配置项',
  `note` varchar(100) DEFAULT NULL COMMENT '备注',
  `is_deleted` tinyint(4) DEFAULT '0' COMMENT '删除标识',
  `addby_id` int(11) DEFAULT NULL COMMENT '创建人',
  `add_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for conf_param
-- ----------------------------
DROP TABLE IF EXISTS `conf_param`;
CREATE TABLE `conf_param` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `param_key` varchar(50) DEFAULT NULL COMMENT '参数',
  `param_value` varchar(50) DEFAULT NULL COMMENT '值',
  `item_id` int(11) DEFAULT NULL COMMENT '项目序号',
  `is_deleted`  tinyint(4) NULL DEFAULT 0 COMMENT '删除标识' ,
  `addby_id` int(11) DEFAULT NULL COMMENT '创建人',
  `add_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='配置参数';
