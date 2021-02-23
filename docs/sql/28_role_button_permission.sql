--
-- 表的结构 `role_button_permission`
--

CREATE TABLE IF NOT EXISTS `role_button_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色按钮权限表' AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `role_button_permission`
--

INSERT INTO `role_button_permission` (`id`, `role_id`, `permission_id`) VALUES
(4, 1, 44),
(6, 1, 42),
(7, 1, 43);

