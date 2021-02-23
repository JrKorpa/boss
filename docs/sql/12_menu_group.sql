-- 2014-12-17 by yangfuyou

--
-- 表的结构 `menu_group`
--

CREATE TABLE IF NOT EXISTS `menu_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `label` varchar(10) NOT NULL COMMENT '分组名称',
  `application_id` int(11) NOT NULL COMMENT '所属模块',
  `icon` int(11) DEFAULT NULL COMMENT '图标',
  `display_order` int(11) NOT NULL DEFAULT '1' COMMENT '显示顺序',
  `is_enabled` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否启用',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否系统内置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='菜单分组表' AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `menu_group`
--

INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES
(1, '系统配置', 1, 2, 99, 1, 0, 1),
(2, '资源管理', 1, 2, 98, 1, 0, 1),
(3, '用户管理', 1, 2, 97, 1, 0, 1),
(4, '日志管理', 1, 2, 96, 1, 0, 1),
(5, '回收站', 1, 2, 95, 1, 0, 0),
(6, '测试', 1, 199, 94, 1, 1, 0),
(7, '演示', 2, 2, 100, 1, 0, 0);

-- 2014-12-20 add yangfuyou
TRUNCATE TABLE `menu_group` ;

INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES
(1, '系统配置', 1, 2, 1419040533, 1, 0, 1),
(2, '菜单管理', 1, 2, 1419039873, 1, 0, 1),
(3, '用户管理', 1, 2, 1419040476, 1, 0, 1),
(4, '日志管理', 1, 2, 97, 1, 0, 1),
(5, '回收站', 1, 2, 96, 1, 0, 0),
(6, '按钮管理', 1, 406, 99, 1, 0, 0),
(7, '通用模块', 1, 65, 95, 1, 0, 0),
(8, '权限管理', 1, 72, 98, 1, 0, 0);


