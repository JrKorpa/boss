--
-- 表的结构 `group_role`
--

CREATE TABLE IF NOT EXISTS `group_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `group_id` int(11) NOT NULL COMMENT '组id',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='组角色表' AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `group_role`
--
