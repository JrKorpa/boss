-- 2014-12-17 by yangfuyou

--
-- 表的结构 `button`
--

CREATE TABLE IF NOT EXISTS `button` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '按钮id',
  `label` varchar(10) NOT NULL COMMENT '显示名',
  `class_id` int(11) NOT NULL COMMENT '按钮样式id',
  `function_id` int(11) NOT NULL COMMENT '按钮图标id',
  `cust_function` varchar(50) DEFAULT NULL COMMENT '自定义处理函数',
  `icon_id` int(11) NOT NULL COMMENT '按钮事件id',
  `data_url` varchar(100) NOT NULL COMMENT '按钮请求地址',
  `tips` varchar(20) NOT NULL COMMENT '按钮提示',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否系统内置',
  `data_title` varchar(10) NOT NULL COMMENT '页签标题',
  `a_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属模块',
  `c_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属文件',
  `o_id` int(11) NOT NULL DEFAULT '0' COMMENT '请求方法',
  `display_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='按钮表' AUTO_INCREMENT=14 ;

--
-- 转存表中的数据 `button`
--

INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `tips`, `is_system`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES
(1, '同步', 4, 1, '', 1, '', '同步', 0, '', 1, 1, 0, 3),
(2, '刷新', 5, 2, '', 1, '', '刷新', 0, '', 1, 1, 0, 2),
(3, '关闭', 13, 3, '', 6, '', '关闭页签', 0, '', 1, 1, 0, 1),
(4, '添加', 1, 6, '', 1, 'index.php?mod=management&con=application&act=add', '添加项目', 1, '', 1, 1, 16, 7),
(5, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=application&act=edit', '编辑', 0, '', 1, 1, 20, 6),
(6, '删除', 3, 8, '', 3, 'index.php?mod=management&con=application&act=delete', '删除', 0, '', 1, 1, 24, 5),
(7, '详情', 5, 9, '', 1, 'index.php?mod=management&con=application&act=show', '详情', 0, '', 1, 1, 21, 4),
(8, '添加', 1, 6, '', 1, 'index.php?mod=management&con=buttonClass&act=add', '添加样式', 0, '', 1, 5, 28, 0),
(9, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=buttonClass&act=edit', '编辑样式', 0, '', 1, 5, 29, 0),
(10, '删除', 3, 8, '', 3, 'index.php?mod=management&con=buttonClass&act=delete', '删除样式', 0, '', 1, 5, 33, 0),
(11, '同步', 4, 1, '', 4, '', '同步', 0, '', 1, 5, 0, 0),
(12, '刷新', 5, 2, '', 5, '', '刷新', 0, '', 1, 5, 0, 0),
(13, '关闭', 8, 3, '', 6, '', '关闭页签', 0, '', 1, 5, 0, 0);

-- 2014-12-19 add yangfuyou

TRUNCATE TABLE `button` ;
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `tips`, `is_system`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES
(1, '同步', 4, 1, '', 1, '', '同步', 0, '', 0, 0, 0, 3),
(2, '刷新', 5, 2, '', 1, '', '刷新', 0, '', 0, 0, 0, 2),
(3, '关闭', 13, 3, '', 6, '', '关闭页签', 0, '', 0, 0, 0, 1),
(4, '添加', 1, 6, '', 1, 'index.php?mod=management&con=application&act=add', '添加项目', 1, '', 1, 1, 16, 7),
(5, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=application&act=edit', '编辑', 0, '', 1, 1, 20, 6),
(6, '删除', 3, 8, '', 3, 'index.php?mod=management&con=application&act=delete', '删除', 0, '', 1, 1, 24, 5),
(7, '详情', 5, 4, '', 1, 'index.php?mod=management&con=application&act=show', '详情', 0, '详情', 1, 1, 21, 4),
(8, '添加', 1, 6, '', 1, 'index.php?mod=management&con=buttonClass&act=add', '添加样式', 0, '', 1, 5, 28, 6),
(9, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=buttonClass&act=edit', '编辑样式', 0, '', 1, 5, 29, 5),
(10, '删除', 3, 8, '', 3, 'index.php?mod=management&con=buttonClass&act=delete', '删除样式', 0, '', 1, 5, 33, 4),
(11, '添加', 1, 6, '', 1, 'index.php?mod=management&con=control&act=add', '添加文件', 0, '', 1, 3, 36, 6),
(12, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=control&act=edit', '编辑文件', 0, '', 1, 3, 37, 5),
(13, '删除', 3, 8, '', 3, 'index.php?mod=management&con=control&act=delete', '删除文件', 0, '', 1, 3, 40, 4),
(14, '详情', 5, 4, '', 7, 'index.php?mod=management&con=menuGroup&act=show', '查看详情', 0, '详情', 1, 4, 51, 4),
(15, '添加', 1, 6, '', 1, 'index.php?mod=management&con=buttonIcon&act=add', '添加图标', 0, '', 1, 2, 42, 6),
(16, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=buttonIcon&act=edit', '编辑图标', 0, '', 1, 2, 43, 5),
(17, '删除', 3, 8, '', 3, 'index.php?mod=management&con=buttonIcon&act=delete', '删除图标', 0, '', 1, 2, 46, 4),
(18, '添加', 1, 6, '', 1, 'index.php?mod=management&con=button&act=add', '添加按钮', 0, '', 1, 11, 113, 7),
(19, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=button&act=edit', '编辑按钮', 0, '', 1, 11, 114, 6),
(20, '删除', 3, 8, '', 3, 'index.php?mod=management&con=button&act=delete', '删除按钮', 0, '', 1, 11, 115, 5),
(21, '详情', 5, 4, '', 14, 'index.php?mod=management&con=button&act=index', '查看详情', 0, '详情', 1, 11, 13, 4),
(22, '添加', 1, 6, '', 1, 'index.php?mod=management&con=menuGroup&act=add', '添加菜单组', 0, '', 1, 4, 49, 7),
(23, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=menuGroup&act=edit', '编辑菜单组', 0, '', 1, 4, 50, 6),
(24, '删除', 3, 8, '', 3, 'index.php?mod=management&con=menuGroup&act=delete', '删除菜单组', 0, '', 1, 4, 54, 5),
(25, '添加', 1, 6, '', 1, 'index.php?mod=management&con=dict&act=add', '添加字典', 0, '', 1, 6, 56, 9),
(26, '编辑', 2, 5, 'dict_edit', 2, 'index.php?mod=management&con=dict&act=edit', '编辑字典', 0, '编辑', 1, 6, 57, 8),
(27, '删除', 3, 5, 'dict_delete', 3, 'index.php?mod=management&con=dict&act=delete', '删除字典', 0, '删除', 1, 6, 58, 7),
(28, '添加', 1, 5, 'dict_item_add', 1, 'index.php?mod=management&con=dict&act=addItem', '添加字典明细', 0, '添加', 1, 6, 62, 6),
(29, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=dict&act=editItem', '编辑字典明细', 0, '', 1, 6, 63, 5),
(30, '删除', 3, 8, '', 3, 'index.php?mod=management&con=dict&act=deleteItem', '删除字典明细', 0, '', 1, 6, 64, 4),
(31, '添加', 1, 6, '', 1, 'index.php?mod=management&con=operation&act=add', '添加操作', 0, '', 1, 8, 82, 7),
(32, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=operation&act=edit', '编辑操作', 0, '', 1, 8, 83, 6),
(33, '删除', 3, 8, '', 3, 'index.php?mod=management&con=operation&act=delete', '删除操作', 0, '', 1, 8, 84, 5),
(34, '详情', 5, 4, '', 7, 'index.php?mod=management&con=operation&act=show', '查看详情', 0, '详情', 1, 8, 85, 4),
(35, '添加', 1, 6, '', 1, 'index.php?mod=management&con=department&act=add', '添加部门', 0, '', 1, 9, 89, 7),
(36, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=department&act=edit', '编辑部门', 0, '', 1, 9, 90, 6),
(37, '删除', 3, 8, '', 3, 'index.php?mod=management&con=department&act=delete', '删除部门', 0, '', 1, 9, 91, 5),
(38, '详情', 5, 4, '', 7, 'index.php?mod=management&con=department&act=show', '查看详情', 0, '详情', 1, 9, 92, 4),
(39, '添加', 1, 6, '', 1, 'index.php?mod=management&con=user&act=add', '添加用户', 0, '', 1, 10, 98, 13),
(40, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=user&act=edit', '编辑用户', 0, '', 1, 10, 99, 12),
(41, '删除', 3, 8, '', 3, 'index.php?mod=management&con=user&act=delete', '删除用户', 0, '', 1, 10, 109, 11),
(42, '详情', 5, 4, '', 7, 'index.php?mod=management&con=user&act=show', '查看详情', 0, '详情', 1, 10, 100, 10),
(43, '重置密码', 12, 7, '', 18, 'index.php?mod=management&con=user&act=modify', '重置密码', 0, '', 1, 10, 101, 9),
(44, '启用', 1, 12, '', 30, 'index.php?mod=management&con=user&act=setEnabled', '启用帐户', 0, '', 1, 10, 105, 8),
(45, '停用', 8, 12, '', 61, 'index.php?mod=management&con=user&act=setDisabled', '停用帐户', 0, '', 1, 10, 106, 7),
(46, '入职', 5, 12, '', 21, 'index.php?mod=management&con=user&act=setOnWork', '入职', 0, '', 1, 10, 108, 6),
(47, '离职', 7, 12, '', 33, 'index.php?mod=management&con=user&act=setLeave', '离职', 0, '', 1, 10, 107, 5),
(48, '恢复', 1, 12, '', 16, 'index.php?mod=management&con=user&act=recover', '恢复', 0, '', 1, 10, 110, 4),
(49, '添加', 1, 6, '', 1, 'index.php?mod=management&con=buttonFunction&act=add', '添加事件', 0, '', 1, 12, 119, 6),
(50, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=buttonFunction&act=edit', '编辑事件', 0, '', 1, 12, 120, 5),
(51, '删除', 3, 8, '', 3, 'index.php?mod=management&con=buttonFunction&act=delete', '删除事件', 0, '', 1, 12, 121, 4),
(52, '添加', 1, 5, 'organization_add', 1, 'index.php?mod=management&con=organization&act=add', '添加', 0, '添加', 1, 14, 126, 6),
(53, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=organization&act=edit', '编辑', 0, '', 1, 14, 127, 5),
(54, '删除', 3, 8, '', 3, 'index.php?mod=management&con=organization&act=delete', '删除', 0, '', 1, 14, 128, 4),
(55, '添加', 1, 6, '', 1, 'index.php?mod=management&con=group&act=add', '添加工作组', 0, '', 1, 16, 132, 7),
(56, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=group&act=edit', '编辑工作组', 0, '', 1, 16, 133, 6),
(57, '删除', 3, 8, '', 3, 'index.php?mod=management&con=group&act=delete', '删除工作组', 0, '', 1, 16, 134, 5),
(58, '详情', 5, 4, '', 7, 'index.php?mod=management&con=group&act=show', '详情', 0, '详情', 1, 16, 135, 4),
(59, '添加', 1, 6, '', 1, 'index.php?mod=management&con=Role&act=add', '添加角色', 0, '', 1, 17, 141, 6),
(60, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=Role&act=edit', '编辑角色', 0, '', 1, 17, 142, 5),
(61, '删除', 3, 8, '', 3, 'index.php?mod=management&con=Role&act=delete', '删除角色', 0, '', 1, 17, 143, 4),
(62, '添加', 1, 6, '', 1, 'index.php?mod=management&con=Payment&act=add', '添加支付方式', 0, '', 1, 18, 148, 7),
(63, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=Payment&act=edit', '编辑支付方式', 0, '', 1, 18, 149, 6),
(64, '删除', 3, 8, '', 3, 'index.php?mod=management&con=Payment&act=delete', '删除支付方式', 0, '', 1, 18, 150, 5),
(65, '排序', 5, 13, '', 6, 'index.php?mod=management&con=Payment&act=listAll', '支付方式显示顺序', 0, '', 1, 18, 151, 4),
(66, '添加', 1, 6, '', 1, 'index.php?mod=management&con=ForumLinks&act=add', '添加友情链接', 0, '', 1, 19, 157, 7),
(67, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=ForumLinks&act=edit', '编辑友情链接', 0, '', 1, 19, 158, 6),
(68, '删除', 3, 8, '', 3, 'index.php?mod=management&con=ForumLinks&act=delete', '删除友情链接', 0, '', 1, 19, 159, 5),
(69, '排序', 5, 13, '', 6, 'index.php?mod=management&con=ForumLinks&act=listAll', '变更显示顺序', 0, '', 1, 19, 162, 4),
(70, '添加', 1, 6, '', 1, 'index.php?mod=management&con=menu&act=add', '添加菜单', 0, '', 1, 7, 70, 8),
(71, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=menu&act=edit', '编辑菜单', 0, '', 1, 7, 71, 7),
(72, '删除', 3, 8, '', 3, 'index.php?mod=management&con=menu&act=delete', '删除菜单', 0, '', 1, 7, 74, 6),
(73, '分配按钮', 5, 13, '', 7, 'index.php?mod=management&con=menu&act=layoutButton', '显示待分配按钮以及分配情况', 0, '', 1, 7, 77, 5),
(74, '按钮排序', 5, 13, '', 7, 'index.php?mod=management&con=menu&act=listButton', '控制按钮在页面的显示顺序', 0, '', 1, 7, 79, 4);

-- 2014-12-20 add yangfuyou

UPDATE `button` SET `function_id`=14 WHERE `id` IN (73,74);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `tips`, `is_system`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES
(75, '添加', 1, 6, '', 1, 'index.php?mod=management&con=Permission&act=add', '添加权限', 0, '', 1, 20, 166, 6),
(76, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=Permission&act=edit', '编辑权限', 0, '', 1, 20, 167, 5),
(77, '删除', 3, 8, '', 3, 'index.php?mod=management&con=Permission&act=delete', '删除权限', 0, '', 1, 20, 168, 4);

-- 2014-12-21 add yangfuyou
update button set display_order=6 where id=11;
update button set display_order=5 where id=12;
update button set display_order=4 where id=13;

INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `tips`, `is_system`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES
(78, '添加', 1, 6, '', 1, 'index.php?mod=management&con=ResourceType&act=add', '添加资源类型', 0, '', 1, 21, 173, 6),
(79, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=ResourceType&act=edit', '编辑资源类型', 0, '', 1, 21, 174, 5),
(80, '删除', 3, 8, '', 3, 'index.php?mod=management&con=ResourceType&act=delete', '删除资源类型', 0, '', 1, 21, 175, 4),
(81, '相关操作', 5, 16, '', 18, 'index.php?mod=management&con=operation&act=index', '当前文件包含的相关操作', 0, '', 1, 3, 0, 4);

-- 2014-12-22 add yangfuyou

INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `tips`, `is_system`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES
(82, '添加', 1, 5, 'group_role_add', 1, 'index.php?mod=management&con=GroupRole&act=add', '添加组角色', 0, '添加', 1, 22, 179, 5),
(83, '删除', 3, 8, '', 3, 'index.php?mod=management&con=GroupRole&act=delete', '删除组角色', 0, '', 1, 22, 181, 4);
