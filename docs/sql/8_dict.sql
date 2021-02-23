-- 2014-12-17 by yangfuyou

-- --------------------------------------------------------

--
-- 表的结构 `dict`
--

CREATE TABLE IF NOT EXISTS `dict` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '属性',
  `label` varchar(20) NOT NULL COMMENT '标识',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否内置',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='数据字典' AUTO_INCREMENT=9 ;

--
-- 转存表中的数据 `dict`
--

INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES
(1, 'user.user_type', '用户类型', 1, 0),
(2, 'is_enabled', '启用禁用', 1, 0),
(3, 'user.is_on_work', '员工状态', 1, 0),
(4, 'gender', '性别', 1, 0),
(5, 'confirm', '是否', 1, 0),
(6, 'login_status', '登录状态', 1, 0),
(7, 'position_level', '职级', 1, 0),
(8, 'position', '职位', 1, 0);


