/*
销售渠道表

Date: 2015-01-11 22:07:34
*/

-- ----------------------------
-- Table structure for sales_channels
-- ----------------------------
DROP TABLE IF EXISTS `sales_channels`;
CREATE TABLE `sales_channels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `channel_name` varchar(50) DEFAULT NULL COMMENT '渠道名称',
  `channel_code` varchar(20) DEFAULT NULL COMMENT '渠道编码',
  `channel_class` tinyint(4) unsigned DEFAULT NULL COMMENT '1线上，2线下',
  `channel_type` tinyint(4) unsigned DEFAULT NULL COMMENT '1部门，2体验店，3公司',
  `channel_own_id` int(8) unsigned DEFAULT NULL COMMENT '所属ID',
  `channel_own` varchar(50) DEFAULT NULL COMMENT '渠道归属',
  `addby_id` int(11) unsigned DEFAULT NULL COMMENT '创建人',
  `addby_time` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `updateby_id` int(11) unsigned DEFAULT NULL COMMENT '更新人',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '修改时间',
  `is_deleted` tinyint(1) unsigned DEFAULT '1' COMMENT '删除标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;