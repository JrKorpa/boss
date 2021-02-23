--
-- 表的结构 `role_operation_permission`
--

CREATE TABLE IF NOT EXISTS `role_operation_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='操作权限表' AUTO_INCREMENT=9 ;

--
-- 转存表中的数据 `role_operation_permission`
--

INSERT INTO `role_operation_permission` (`id`, `role_id`, `permission_id`) VALUES
(6, 1, 23),
(7, 1, 26),
(8, 1, 27);

