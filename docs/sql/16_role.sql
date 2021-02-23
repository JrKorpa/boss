-- 2014-12-17 by wangshuai

--
-- Database: `cuteframe`
--

-- --------------------------------------------------------

--
-- 表的结构 `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `label` varchar(40) NOT NULL COMMENT '角色名称',
  `code` varchar(40) NOT NULL COMMENT '编码',
  `note` varchar(250) NOT NULL COMMENT '描述',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `role`
--

INSERT INTO `role` (`id`, `label`, `code`, `note`, `is_deleted`, `is_system`) VALUES
(1, '超级管理员', 'ASD', 'asd', 0, 0);


