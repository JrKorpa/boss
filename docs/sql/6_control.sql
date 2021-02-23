-- 2014-12-17 by yangfuyou

--
-- 表的结构 `control`
--

CREATE TABLE IF NOT EXISTS `control` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `label` varchar(10) NOT NULL COMMENT '显示名称',
  `code` varchar(20) NOT NULL COMMENT '控制器名',
  `application_id` int(11) NOT NULL COMMENT '所属项目',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='文件表' AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `control`
--

INSERT INTO `control` (`id`, `label`, `code`, `application_id`) VALUES
(1, '项目', 'application', 1),
(2, '图标', 'buttonIcon', 1),
(3, '文件', 'control', 1),
(4, '菜单组', 'menuGroup', 1),
(5, '样式', 'buttonClass', 1),
(6, '数据字典', 'dict', 1),
(7, '菜单', 'menu', 1),
(8, '操作', 'operation', 1),
(9, '部门', 'department', 1),
(10, '用户', 'user', 1),
(11, '按钮', 'button', 1),
(12, '按钮事件', 'buttonFunction', 1),
(13, '系统访问日志', 'systemAccessLog', 1),
(14, '岗位设置', 'organization', 1),
(15, '工作组', 'group', 1),
(16, '角色管理', 'Role', 1);

-- 2014-12-19 add yangfuyou

INSERT INTO `control` (`id`, `label`, `code`, `application_id`) VALUES
(17, '支付方式', 'Payment', 1),
(18, '友情链接', 'ForumLinks', 1);

-- 2014-12-20 add yangfuyou

INSERT INTO `control` (`id`, `label`, `code`, `application_id`) VALUES
(19, '权限管理', 'Permission', 1);

-- 2014-12-21 add yangfuyou

INSERT INTO `control` (`id`, `label`, `code`, `application_id`) VALUES
(20, '资源类型', 'ResourceType', 1);

-- 2014-12-22 add yangfuyou

INSERT INTO `control` (`id`, `label`, `code`, `application_id`) VALUES
(21, '组角色管理', 'GroupRole', 1);

-- 2014-12-25 add yangfuyou

INSERT INTO `control` (`id`, `label`, `code`, `application_id`) VALUES
(22, '角色授权', 'RolePermission', 1),
(23, '角色用户', 'RoleUser', 1);

