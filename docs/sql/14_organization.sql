-- 2014-12-17 by yangfuyou

--
-- 表的结构 `organization`
--

CREATE TABLE IF NOT EXISTS `organization` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `dept_id` int(11) NOT NULL DEFAULT '0' COMMENT '部门id',
  `position` tinyint(4) NOT NULL DEFAULT '0' COMMENT '职位',
  `level` tinyint(4) NOT NULL DEFAULT '0' COMMENT '职级',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='岗位设置表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `organization`
--

INSERT INTO `organization` (`id`, `dept_id`, `position`, `level`, `user_id`) VALUES
(1, 1, 3, 2, 1);

