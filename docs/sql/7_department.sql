-- 2014-12-17 by yangfuyou

-- --------------------------------------------------------

--
-- 表的结构 `department`
--

CREATE TABLE IF NOT EXISTS `department` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '部门id',
  `name` char(50) NOT NULL COMMENT '部门名称',
  `code` char(50) DEFAULT NULL COMMENT '部门编码',
  `note` varchar(255) DEFAULT NULL COMMENT '描述',
  `parent_id` int(11) NOT NULL COMMENT '上级部门id',
  `tree_path` varchar(512) NOT NULL COMMENT '全路径',
  `pids` varchar(200) DEFAULT NULL COMMENT '祖先分类',
  `childrens` int(11) NOT NULL DEFAULT '0' COMMENT '下级分类数',
  `display_order` int(11) NOT NULL COMMENT '显示顺序',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='部门/组织架构' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `department`
--

INSERT INTO `department` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES
(1, '总部', 'K01', '总部123', 0, '0', '', 4, 1, 0, 1),
(2, '线上事业部', 'K002', '热我认为', 1, '0-1', '1', 0, 1418437719, 0, 0),
(3, '线下事业部', 'K003', 'fdsfs', 1, '0-1', '1', 0, 1418455327, 0, 0),
(4, '第一事业部', NULL, NULL, 3, '0-1-3', '1,3', 0, 0, 0, 0);

