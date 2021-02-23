-- 2014-12-17 by yangfuyou
--
-- 表的结构 `application`
--

CREATE TABLE IF NOT EXISTS `application` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `label` varchar(10) NOT NULL COMMENT '项目名称',
  `code` varchar(40) NOT NULL COMMENT '项目文件夹',
  `icon` tinyint(4) NOT NULL COMMENT '图标',
  `display_order` int(11) NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `is_enabled` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否系统内置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='项目模块表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `application`
--

INSERT INTO `application` (`id`, `label`, `code`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES
(1, '管理中心', 'management', 31, 2, 1, 0, 1);