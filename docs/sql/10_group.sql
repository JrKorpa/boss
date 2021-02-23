-- 2014-12-17 by yangfuyou

--
-- 表的结构 `group`
--

CREATE TABLE IF NOT EXISTS `group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '组id',
  `name` char(50) NOT NULL COMMENT '组名称',
  `code` char(50) DEFAULT NULL COMMENT '组编码',
  `note` varchar(255) DEFAULT NULL COMMENT '描述',
  `parent_id` int(11) NOT NULL COMMENT '上级组id',
  `tree_path` varchar(512) NOT NULL COMMENT '全路径',
  `pids` varchar(200) DEFAULT NULL COMMENT '祖先分类',
  `childrens` int(11) NOT NULL DEFAULT '0' COMMENT '下级分类数',
  `display_order` int(11) NOT NULL COMMENT '显示顺序',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='工作组表' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `group`
--

INSERT INTO `group` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES
(1, '张三', '123', '方的身份的身份放到沙发上', 0, '0', '', 3, 1418718419, 0, 1),
(2, '李四', '111', '放到沙发上订单111', 1, '0-1', '1', 0, 1418718447, 0, 1),
(3, '王五', '222', '范德萨范德萨', 1, '0-1', '1', 0, 1418718474, 0, 1),
(4, '放到沙发上', '12345', '', 1, '0-1', '1', 0, 1418718698, 1, 0);

