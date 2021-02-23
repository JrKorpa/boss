-- 2014-12-18

-- --------------------------------------------------------

--
-- 表的结构 `payment`
--

CREATE TABLE IF NOT EXISTS `payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pay_code` varchar(20) NOT NULL COMMENT '支付方式拼音',
  `pay_name` varchar(20) NOT NULL COMMENT '支付方式中文名',
  `pay_fee` decimal(10,2) NOT NULL COMMENT '支付手续费',
  `pay_desc` text NOT NULL COMMENT '描述',
  `pay_order` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `pay_config` text NOT NULL COMMENT '配置项',
  `is_enabled` tinyint(4) NOT NULL COMMENT '是否启用',
  `is_cod` tinyint(4) NOT NULL COMMENT '是否货到付款',
  `is_display` tinyint(4) NOT NULL COMMENT '是否显示',
  `is_web` tinyint(4) NOT NULL COMMENT '是否网络付款可用',
  `addby_id`  int(11) NULL DEFAULT NULL COMMENT '创建人' ,
  `add_time`  int(11) NOT NULL COMMENT '创建时间' ,
  `is_deleted` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='支付方式表' AUTO_INCREMENT=1 ;

