-- 2014-12-19

--
-- 表的结构 `permission`
--

CREATE TABLE IF NOT EXISTS `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限id',
  `type` int(11) NOT NULL COMMENT '资源类型id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `is_deleted` bit(1) NOT NULL DEFAULT b'0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='权限表' AUTO_INCREMENT=1 ;

-- 2012-12-20 add yangfuyou

ALTER TABLE `permission` ADD `resource_id` INT NOT NULL DEFAULT '0' COMMENT '资源id' AFTER `type`;
ALTER TABLE `permission` CHANGE `is_deleted` `is_deleted` TINYINT NOT NULL DEFAULT '0' COMMENT '是否删除';
ALTER TABLE `permission` ADD `code` VARCHAR(50) NULL COMMENT '编码' AFTER `name`;
ALTER TABLE `permission` CHANGE `desc` `note` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '描述';


-- 2012-12-21 add yangfuyou

INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`) VALUES
(1, 1, 1, '数据字典-菜单权限', 'DICT_M', '', 0),
(2, 1, 22, '资源类型-菜单权限', 'RESOURCE_TYPE_M', '', 0),
(3, 1, 2, '项目管理-菜单权限', 'APPLICATION_M', '', 0),
(4, 1, 23, '热热-菜单权限', 'MENU23', '', 0),
(5, 1, 15, '岗位设置-菜单权限', 'ORGANIZATION_M', '', 0),
(6, 1, 9, '用户管理-菜单权限', 'USER_M', '', 0),
(7, 1, 14, '访问日志-菜单权限', 'SYSTEM_ACCESS_LOG_M', '', 0),
(8, 1, 12, '按钮管理-菜单权限', 'BUTTON_M', '', 0),
(9, 1, 13, '按钮事件-菜单权限', 'BUTTON_FUNCTION_M', '', 0),
(10, 1, 11, '已删用户-菜单权限', 'MENU11', '', 1),
(11, 1, 3, '菜单管理-菜单权限', 'MENU_M', '', 0),
(12, 1, 17, '工作组-菜单权限', 'GROUP_M', '', 0),
(13, 1, 8, '部门管理-菜单权限', 'DEPARTMENT_M', '', 0),
(14, 1, 6, '按钮样式-菜单权限', 'BUTTON_CLASS_M', '', 0),
(15, 1, 7, '按钮图标-菜单权限', 'BUTTON_ICON_M', '', 0),
(16, 1, 10, '操作管理-菜单权限', 'OPERATION_M', '', 0),
(17, 1, 4, '菜单分组-菜单权限', 'MENU_GROUP_M', '', 0),
(18, 1, 5, '文件管理-菜单权限', 'CONTROL_M', '', 0),
(19, 1, 18, '角色管理-菜单权限', 'ROLE_M', '', 0),
(20, 1, 19, '支付方式-菜单权限', 'PAYMENT_M', '', 0),
(21, 1, 20, '友情链接-菜单权限', 'FORUM_LINKS_M', '', 0),
(22, 1, 21, '权限管理-菜单权限', 'PERMISSION_M', '', 0),
(23, 3, 1, '项目-默认页-操作权限', 'APPLICATION_INDEX_O', '', 0),
(24, 2, 4, '项目-添加-按钮权限', 'BUTTON4', '', 0),
(25, 3, 171, '资源类型-默认页-操作权限', 'RESOURCE_TYPE_INDEX_O', '', 0),
(26, 3, 2, '项目-数据分页-操作权限', 'APPLICATION_SEARCH_O', '', 0),
(27, 3, 16, '项目-添加项目-操作权限', 'APPLICATION_ADD_O', '', 0);

-- 2012-12-21 add yangfuyou

update `permission` set name='组角色管理-菜单权限',code='GROUP_ROLE_M' where id=4;

INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`) VALUES
(28, 4, 22, '组角色管理-数据权限', 'OBJ_GROUP_ROLE', '', 0),
(29, 3, 178, '组角色管理-默认页-操作权限', 'GROUP_ROLE_INDEX_O', '', 0),
(30, 3, 179, '组角色管理-添加-操作权限', 'GROUP_ROLE_ADD_O', '', 0),
(31, 3, 180, '组角色管理-数据分页-操作权限', 'GROUP_ROLE_SEARCH_O', '', 0),
(32, 3, 181, '组角色管理-删除-操作权限', 'GROUP_ROLE_DELETE_O', '', 0),
(33, 3, 182, '组角色管理-保存-操作权限', 'GROUP_ROLE_INSERT_O', '', 0),
(34, 2, 82, '组角色管理-添加-按钮权限', 'BUTTON82', '', 0),
(35, 2, 83, '组角色管理-删除-按钮权限', 'BUTTON83', '', 0);

-- 2012-12-25 add yangfuyou

INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`) VALUES
(36, 4, 23, '角色授权-数据权限', 'OBJ_ROLE_PERMISSION', '', 0),
(37, 3, 183, '角色授权-默认页-操作权限', 'ROLE_PERMISSION_INDEX_O', '', 0),
(38, 1, 24, '角色授权-菜单权限', 'ROLE_PERMISSION_M', '', 0),
(39, 4, 24, '角色用户-数据权限', 'OBJ_ROLE_USER', '', 0),
(40, 3, 184, '角色用户-默认页-操作权限', 'ROLE_USER_INDEX_O', '', 0),
(41, 1, 25, '角色用户-菜单权限', 'ROLE_USER_M', '', 0),
(42, 2, 1, '同步-按钮权限', 'BUTTON1', '', 0),
(43, 2, 2, '刷新-按钮权限', 'BUTTON2', '', 0),
(44, 2, 3, '关闭-按钮权限', 'BUTTON3', '', 0);