-- 2014-12-17 by yangfuyou

--
-- 表的结构 `menu`
--

CREATE TABLE IF NOT EXISTS `menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '菜单id',
  `c_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属文件',
  `o_id` int(11) NOT NULL DEFAULT '0' COMMENT '请求操作',
  `label` varchar(20) NOT NULL COMMENT '菜单名称',
  `code` varchar(40) NOT NULL COMMENT '编码',
  `url` varchar(100) NOT NULL COMMENT '地址',
  `icon` int(11) NOT NULL COMMENT '图标',
  `group_id` int(11) NOT NULL COMMENT '所属菜单组',
  `application_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属项目',
  `display_order` int(11) NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `is_enabled` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否系统内置',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='系统菜单表' AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `menu`
--

INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES
(1, 6, 3, '数据字典', 'DICT', 'index.php?mod=management&con=dict&act=index', 1, 1, 1, 100, 1, 1, 0),
(2, 1, 1, '项目管理', 'APPLICATION', 'index.php?mod=management&con=application&act=index', 1, 1, 1, 99, 1, 1, 0),
(3, 7, 7, '菜单管理', 'MENU', 'index.php?mod=management&con=menu&act=index', 1, 2, 1, 91, 1, 1, 0),
(4, 4, 5, '菜单分组', 'MENU_GROUP', 'index.php?mod=management&con=menuGroup&act=index', 1, 1, 1, 97, 1, 1, 0),
(5, 3, 8, '文件管理', 'CONTROL', 'index.php?mod=management&con=control&act=index', 1, 2, 1, 98, 1, 1, 0),
(6, 5, 6, '按钮样式', 'BUTTON_CLASS', 'index.php?mod=management&con=buttonClass&act=index', 1, 1, 1, 94, 1, 1, 0),
(7, 2, 4, '按钮图标', 'BUTTON_ICON', 'index.php?mod=management&con=buttonIcon&act=index', 1, 1, 1, 95, 1, 1, 0),
(8, 9, 10, '部门管理', 'DEPARTMENT', 'index.php?mod=management&con=department&act=index', 1, 3, 1, 93, 1, 1, 0),
(9, 10, 11, '用户管理', 'USER', 'index.php?mod=management&con=user&act=index', 1, 3, 1, 86, 1, 1, 0),
(10, 8, 9, '操作管理', 'OPERATION', 'index.php?mod=management&con=operation&act=index', 1, 2, 1, 96, 1, 1, 0),
(11, 10, 12, '已删用户', 'USER_RECYCLE', 'index.php?mod=management&con=user&act=recycle', 1, 5, 1, 90, 1, 1, 0),
(12, 11, 13, '按钮管理', 'BUTTON', 'index.php?mod=management&con=button&act=index', 2, 2, 1, 89, 1, 0, 0),
(13, 12, 14, '按钮事件', 'BUTTON_FUNCTION', 'index.php?mod=management&con=buttonFunction&act=index', 5, 1, 1, 88, 1, 0, 0),
(14, 13, 15, '访问日志', 'SYSTEM_ACCESS_LOG', 'index.php?mod=management&con=systemAccessLog&act=index', 2, 4, 1, 87, 1, 0, 0),
(15, 14, 17, '岗位设置', 'ORGANIZATION', 'index.php?mod=management&con=organization&act=index', 204, 3, 1, 84, 1, 0, 0),
(16, 16, 19, '工作组', 'GROUP', 'index.php?mod=management&con=group&act=index', 5, 3, 1, 92, 1, 0, 0),
(17, 17, 34, '角色管理', 'ROLE', 'index.php?mod=management&con=Role&act=index', 1, 3, 1, 1418812163, 1, 0, 0);

-- 2014-12-19 add yangfuyou


INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES
(18, 18, 146, '支付方式', 'PAYMENT', 'index.php?mod=management&con=Payment&act=index', 204, 2, 1, 1418961357, 1, 0, 0),
(19, 19, 155, '友情链接', 'FORUM_LINKS', 'index.php?mod=management&con=ForumLinks&act=index', 204, 1, 1, 1418970438, 1, 0, 0);

-- 2014-12-20 add yangfuyou

INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES
(20, 20, 164, '权限管理', 'PERMISSION', 'index.php?mod=management&con=Permission&act=index', 20, 10, 1, 1419043097, 1, 0, 0);

-- 2014-12-21 add yangfuyou

INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES
(21, 21, 171, '资源类型', 'RESOURCE_TYPE', 'index.php?mod=management&con=ResourceType&act=index', 23, 10, 1, 1419079055, 1, 0, 0);

-- 2014-12-22 add yangfuyou

INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES
(22, 22, 178, '组角色管理', 'GROUP_ROLE', 'index.php?mod=management&con=GroupRole&act=index', 17, 10, 1, 1419217981, 1, 0, 0);

-- 2014-12-25 add yangfuyou

INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES
(23, 23, 183, '角色授权', 'ROLE_PERMISSION', 'index.php?mod=management&con=RolePermission&act=index', 32, 10, 1, 1419299580, 1, 0, 0),
(24, 24, 184, '角色用户', 'ROLE_USER', 'index.php?mod=management&con=RoleUser&act=index', 30, 10, 1, 1419301219, 1, 0, 0);
