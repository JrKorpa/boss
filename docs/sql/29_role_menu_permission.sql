--
-- 表的结构 `role_menu_permission`
--

CREATE TABLE IF NOT EXISTS `role_menu_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色权限表' AUTO_INCREMENT=48 ;

--
-- 转存表中的数据 `role_menu_permission`
--

INSERT INTO `role_menu_permission` (`id`, `role_id`, `permission_id`) VALUES
(25, 1, 1),
(26, 1, 3),
(27, 1, 17),
(28, 1, 11),
(29, 1, 16),
(30, 1, 18),
(31, 1, 5),
(32, 1, 6),
(33, 1, 12),
(34, 1, 13),
(35, 1, 7),
(36, 1, 8),
(37, 1, 9),
(38, 1, 14),
(39, 1, 15),
(40, 1, 20),
(41, 1, 21),
(42, 1, 2),
(43, 1, 4),
(44, 1, 19),
(45, 1, 22),
(46, 1, 38),
(47, 1, 41);

