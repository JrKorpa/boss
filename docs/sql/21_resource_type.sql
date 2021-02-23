-- 2014-12-18

-- --------------------------------------------------------

--
-- 表的结构 `resource_type`
--

CREATE TABLE IF NOT EXISTS `resource_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `label` varchar(10) NOT NULL COMMENT '显示标识',
  `code` varchar(20) NOT NULL COMMENT '编码',
  `main_table` varchar(40) NOT NULL COMMENT '主表',
  `user_table` varchar(40) NOT NULL COMMENT '相关表',
  `fields` varchar(200) NOT NULL COMMENT '字段',
  `foreigh_key` varchar(20) NOT NULL COMMENT '外键',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否系统内置',
  `is_enabled` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `note` varchar(250) NOT NULL COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='资源类型表' AUTO_INCREMENT=1 ;

-- 2014-12-21 add yangfuyou

TRUNCATE TABLE `resource_type`;
INSERT INTO `resource_type` (`id`, `label`, `code`, `main_table`, `user_table`, `fields`, `foreigh_key`, `is_system`, `is_enabled`, `is_deleted`, `note`) VALUES
(1, '菜单', 'MENU', 'menu', 'rel_user_menu', 'id', 'menu_id', 1, 1, 0, ''),
(2, '按钮', 'BUTTON', 'button', 'rel_user_button', 'id', 'button_id', 1, 1, 0, ''),
(3, '操作', 'OPERATION', 'operation', 'rel_user_operation', 'id', 'operation_id', 1, 1, 0, ''),
(4, '数据', 'DATA', 'control', 'rel_user_control', 'id', 'c_id', 1, 1, 0, '');

