-- phpMyAdmin SQL Dump
-- version 4.1.13
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2015-01-04 16:25:53
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `cuteframe`
--

-- --------------------------------------------------------

--
-- 表的结构 `application`
--

CREATE TABLE IF NOT EXISTS `application` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `label` varchar(10) NOT NULL COMMENT '项目名称',
  `code` varchar(40) NOT NULL COMMENT '项目文件夹',
  `icon` tinyint(4) NOT NULL COMMENT '图标',
  `display_order` int(11) NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `is_enabled` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否系统内置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='项目模块表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `application`
--

INSERT INTO `application` (`id`, `label`, `code`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES
(1, '管理中心', 'management', 31, 2, 1, 0, 1);

-- --------------------------------------------------------

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
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `data_title` varchar(10) NOT NULL COMMENT '页签标题',
  `a_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属模块',
  `c_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属文件',
  `o_id` int(11) NOT NULL DEFAULT '0' COMMENT '请求方法',
  `display_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='按钮表' AUTO_INCREMENT=92 ;

--
-- 转存表中的数据 `button`
--

INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES
(1, '同步', 4, 1, '', 1, '', '同步', 1, 0, '', 0, 0, 0, 3),
(2, '刷新', 5, 2, '', 1, '', '刷新', 1, 0, '', 0, 0, 0, 2),
(3, '关闭', 13, 3, '', 6, '', '关闭页签', 1, 0, '', 0, 0, 0, 1),
(4, '添加', 1, 6, '', 1, 'index.php?mod=management&con=Application&act=add', '添加项目', 1, 0, '', 1, 1, 16, 7),
(5, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=Application&act=edit', '编辑', 1, 0, '', 1, 1, 20, 6),
(6, '删除', 3, 8, '', 3, 'index.php?mod=management&con=Application&act=delete', '删除', 1, 0, '', 1, 1, 24, 5),
(7, '详情', 5, 10, '', 1, 'index.php?mod=management&con=Application&act=show', '详情', 1, 0, '', 1, 1, 21, 4),
(8, '添加', 1, 6, '', 1, 'index.php?mod=management&con=ButtonClass&act=add', '添加样式', 1, 0, '', 1, 5, 28, 6),
(9, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=ButtonClass&act=edit', '编辑样式', 1, 0, '', 1, 5, 29, 5),
(10, '删除', 3, 8, '', 3, 'index.php?mod=management&con=ButtonClass&act=delete', '删除样式', 1, 0, '', 1, 5, 33, 4),
(11, '添加', 1, 6, '', 1, 'index.php?mod=management&con=Control&act=add', '添加文件', 1, 0, '', 1, 3, 36, 7),
(12, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=Control&act=edit', '编辑文件', 1, 0, '', 1, 3, 37, 6),
(13, '删除', 3, 8, '', 3, 'index.php?mod=management&con=Control&act=delete', '删除文件', 1, 0, '', 1, 3, 40, 5),
(14, '详情', 5, 10, '', 7, 'index.php?mod=management&con=MenuGroup&act=show', '查看详情', 1, 0, '', 1, 4, 51, 4),
(15, '添加', 1, 6, '', 1, 'index.php?mod=management&con=ButtonIcon&act=add', '添加图标', 1, 0, '', 1, 2, 42, 6),
(16, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=ButtonIcon&act=edit', '编辑图标', 1, 0, '', 1, 2, 43, 5),
(17, '删除', 3, 8, '', 3, 'index.php?mod=management&con=ButtonIcon&act=delete', '删除图标', 1, 0, '', 1, 2, 46, 4),
(18, '添加', 1, 6, '', 1, 'index.php?mod=management&con=Button&act=add', '添加按钮', 1, 0, '', 1, 11, 113, 7),
(19, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=Button&act=edit', '编辑按钮', 1, 0, '', 1, 11, 114, 6),
(20, '删除', 3, 8, '', 3, 'index.php?mod=management&con=Button&act=delete', '删除按钮', 1, 0, '', 1, 11, 115, 5),
(22, '添加', 1, 6, '', 1, 'index.php?mod=management&con=MenuGroup&act=add', '添加菜单组', 1, 0, '', 1, 4, 49, 7),
(23, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=MenuGroup&act=edit', '编辑菜单组', 1, 0, '', 1, 4, 50, 6),
(24, '删除', 3, 8, '', 3, 'index.php?mod=management&con=MenuGroup&act=delete', '删除菜单组', 1, 0, '', 1, 4, 54, 5),
(25, '添加', 1, 6, '', 1, 'index.php?mod=management&con=Dict&act=add', '添加字典', 1, 0, '', 1, 6, 56, 9),
(26, '编辑', 2, 17, 'dict_edit', 2, 'index.php?mod=management&con=Dict&act=edit', '编辑字典', 1, 0, '编辑', 1, 6, 57, 8),
(27, '删除', 3, 17, 'dict_delete', 3, 'index.php?mod=management&con=Dict&act=delete', '删除字典', 1, 0, '删除', 1, 6, 58, 7),
(28, '添加', 1, 17, 'dict_item_add', 1, 'index.php?mod=management&con=Dict&act=addItem', '添加字典明细', 1, 0, '添加', 1, 6, 62, 6),
(29, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=Dict&act=editItem', '编辑字典明细', 1, 0, '', 1, 6, 63, 5),
(30, '删除', 3, 8, '', 3, 'index.php?mod=management&con=Dict&act=deleteItem', '删除字典明细', 1, 0, '', 1, 6, 64, 4),
(31, '添加', 1, 6, '', 1, 'index.php?mod=management&con=Operation&act=add', '添加操作', 1, 0, '', 1, 8, 82, 7),
(32, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=Operation&act=edit', '编辑操作', 1, 0, '', 1, 8, 83, 6),
(33, '删除', 3, 8, '', 3, 'index.php?mod=management&con=Operation&act=delete', '删除操作', 1, 0, '', 1, 8, 84, 5),
(34, '详情', 5, 10, '', 7, 'index.php?mod=management&con=Operation&act=show', '查看详情', 1, 0, '', 1, 8, 85, 4),
(35, '添加', 1, 6, '', 1, 'index.php?mod=management&con=Department&act=add', '添加部门', 1, 0, '', 1, 9, 89, 7),
(36, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=Department&act=edit', '编辑部门', 1, 0, '', 1, 9, 90, 6),
(37, '删除', 3, 8, '', 3, 'index.php?mod=management&con=Department&act=delete', '删除部门', 1, 0, '', 1, 9, 91, 5),
(38, '详情', 5, 10, '', 7, 'index.php?mod=management&con=Department&act=show', '查看详情', 1, 0, '', 1, 9, 92, 4),
(39, '添加', 1, 6, '', 1, 'index.php?mod=management&con=User&act=add', '添加用户', 1, 0, '', 1, 10, 98, 13),
(40, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=User&act=edit', '编辑用户', 1, 0, '', 1, 10, 99, 12),
(41, '删除', 3, 8, '', 3, 'index.php?mod=management&con=User&act=delete', '删除用户', 1, 0, '', 1, 10, 109, 11),
(42, '详情', 5, 10, '', 7, 'index.php?mod=management&con=User&act=show', '查看详情', 1, 0, '', 1, 10, 100, 10),
(43, '重置密码', 12, 7, '', 18, 'index.php?mod=management&con=User&act=modify', '重置密码', 1, 0, '', 1, 10, 101, 9),
(44, '启用', 1, 15, '', 30, 'index.php?mod=management&con=User&act=setEnabled', '启用帐户', 1, 0, '', 1, 10, 105, 8),
(45, '停用', 8, 15, '', 61, 'index.php?mod=management&con=User&act=setDisabled', '停用帐户', 1, 0, '', 1, 10, 106, 7),
(46, '入职', 5, 15, '', 21, 'index.php?mod=management&con=User&act=setOnWork', '入职', 1, 0, '', 1, 10, 108, 6),
(47, '离职', 7, 15, '', 33, 'index.php?mod=management&con=User&act=setLeave', '离职', 1, 0, '', 1, 10, 107, 5),
(48, '恢复', 1, 15, '', 16, 'index.php?mod=management&con=User&act=recover', '恢复', 1, 0, '', 1, 10, 110, 4),
(49, '添加', 1, 6, '', 1, 'index.php?mod=management&con=ButtonFunction&act=add', '添加事件', 1, 0, '', 1, 12, 119, 6),
(50, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=ButtonFunction&act=edit', '编辑事件', 1, 0, '', 1, 12, 120, 5),
(51, '删除', 3, 8, '', 3, 'index.php?mod=management&con=ButtonFunction&act=delete', '删除事件', 1, 0, '', 1, 12, 121, 4),
(52, '添加', 1, 17, 'organization_add', 1, 'index.php?mod=management&con=Organization&act=add', '添加', 1, 0, '添加', 1, 14, 126, 6),
(53, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=Organization&act=edit', '编辑', 1, 0, '', 1, 14, 127, 5),
(54, '删除', 3, 8, '', 3, 'index.php?mod=management&con=Organization&act=delete', '删除', 1, 0, '', 1, 14, 128, 4),
(55, '添加', 1, 6, '', 1, 'index.php?mod=management&con=Role&act=add', '添加角色', 1, 0, '', 1, 16, 132, 6),
(56, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=Role&act=edit', '编辑角色', 1, 0, '', 1, 16, 133, 5),
(57, '删除', 3, 8, '', 3, 'index.php?mod=management&con=Role&act=delete', '删除角色', 1, 0, '', 1, 16, 134, 4),
(59, '添加', 1, 6, '', 1, 'index.php?mod=management&con=Payment&act=add', '添加支付方式', 1, 0, '', 1, 17, 141, 6),
(60, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=Payment&act=edit', '编辑支付方式', 1, 0, '', 1, 17, 142, 5),
(61, '删除', 3, 8, '', 3, 'index.php?mod=management&con=Payment&act=delete', '删除支付方式', 1, 0, '', 1, 17, 143, 4),
(62, '添加', 1, 6, '', 1, 'index.php?mod=management&con=ForumLinks&act=add', '添加友情链接', 1, 0, '', 1, 18, 148, 7),
(63, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=ForumLinks&act=edit', '编辑友情链接', 1, 0, '', 1, 18, 149, 6),
(64, '删除', 3, 8, '', 3, 'index.php?mod=management&con=ForumLinks&act=delete', '删除友情链接', 1, 0, '', 1, 18, 150, 5),
(65, '排序', 5, 4, '', 6, 'index.php?mod=management&con=Payment&act=listAll', '支付方式显示顺序', 1, 0, '', 1, 17, 207, 4),
(66, '添加', 1, 6, '', 1, 'index.php?mod=management&con=Permission&act=add', '添加权限', 1, 0, '', 1, 19, 157, 6),
(67, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=Permission&act=edit', '编辑权限', 1, 0, '', 1, 19, 158, 5),
(68, '删除', 3, 8, '', 3, 'index.php?mod=management&con=Permission&act=delete', '删除权限', 1, 0, '', 1, 19, 159, 4),
(69, '排序', 5, 4, '', 6, 'index.php?mod=management&con=ForumLinks&act=listAll', '变更显示顺序', 1, 0, '', 1, 18, 151, 4),
(70, '添加', 1, 6, '', 1, 'index.php?mod=management&con=Menu&act=add', '添加菜单', 1, 0, '', 1, 7, 70, 8),
(71, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=Menu&act=edit', '编辑菜单', 1, 0, '', 1, 7, 71, 7),
(72, '删除', 3, 8, '', 3, 'index.php?mod=management&con=Menu&act=delete', '删除菜单', 1, 0, '', 1, 7, 74, 6),
(73, '分配按钮', 5, 5, '', 7, 'index.php?mod=management&con=Menu&act=layoutButton', '显示待分配按钮以及分配情况', 1, 0, '', 1, 7, 77, 5),
(74, '按钮排序', 5, 5, '', 7, 'index.php?mod=management&con=Menu&act=listButton', '控制按钮在页面的显示顺序', 1, 0, '', 1, 7, 79, 4),
(75, '添加', 1, 6, '', 1, 'index.php?mod=management&con=ResourceType&act=add', '添加资源类型', 1, 0, '', 1, 20, 166, 6),
(76, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=ResourceType&act=edit', '编辑资源类型', 1, 0, '', 1, 20, 167, 5),
(77, '删除', 3, 8, '', 3, 'index.php?mod=management&con=ResourceType&act=delete', '删除资源类型', 1, 0, '', 1, 20, 168, 4),
(78, '添加', 1, 6, '', 1, 'index.php?mod=management&con=GroupRole&act=add', '添加角色', 1, 0, '', 1, 21, 173, 5),
(80, '删除', 3, 8, '', 3, 'index.php?mod=management&con=GroupRole&act=delete', '删除角色', 1, 0, '', 1, 21, 175, 4),
(81, '相关操作', 5, 14, '', 18, 'index.php?mod=management&con=operation&act=index', '当前文件包含的相关操作', 1, 0, '', 1, 3, 0, 4),
(84, '添加', 1, 6, '', 1, 'index.php?mod=management&con=Group&act=add', '添加组', 1, 0, '', 1, 15, 199, 7),
(85, '编辑', 2, 7, '', 2, 'index.php?mod=management&con=Group&act=edit', '编辑组', 1, 0, '', 1, 15, 200, 6),
(86, '删除', 3, 8, '', 3, 'index.php?mod=management&con=Group&act=delete', '删除组', 1, 0, '', 1, 15, 201, 5),
(87, '详情', 5, 10, '', 7, 'index.php?mod=management&con=Group&act=show', '查看详情', 1, 0, '', 1, 15, 202, 4),
(88, '添加', 1, 17, 'group_user_add', 1, 'index.php?mod=management&con=GroupUser&act=add', '添加组用户', 1, 0, '添加', 1, 24, 187, 5),
(89, '删除', 3, 8, '', 3, 'index.php?mod=management&con=GroupUser&act=delete', '删除组用户', 1, 0, '', 1, 24, 189, 4),
(90, '添加', 1, 17, 'roleuser_add', 1, 'index.php?mod=management&con=RoleUser&act=add', '添加角色用户', 1, 0, '添加', 1, 23, 212, 5),
(91, '删除', 3, 8, '', 3, 'index.php?mod=management&con=RoleUser&act=delete', '删除角色用户', 1, 0, '', 1, 23, 214, 4);

-- --------------------------------------------------------

--
-- 表的结构 `button_class`
--

CREATE TABLE IF NOT EXISTS `button_class` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '样式id',
  `classname` varchar(20) NOT NULL COMMENT '样式名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='按钮样式表' AUTO_INCREMENT=15 ;

--
-- 转存表中的数据 `button_class`
--

INSERT INTO `button_class` (`id`, `classname`) VALUES
(1, 'green'),
(2, 'blue'),
(3, 'red'),
(4, 'grey'),
(5, 'btn-info'),
(6, 'dark'),
(7, 'default'),
(8, 'yellow'),
(9, 'purple'),
(10, 'btn-primary'),
(11, 'btn-default'),
(12, 'btn-success'),
(13, 'btn-warning'),
(14, 'btn-danger');

-- --------------------------------------------------------

--
-- 表的结构 `button_function`
--

CREATE TABLE IF NOT EXISTS `button_function` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'js方法id',
  `name` varchar(30) NOT NULL COMMENT '方法名',
  `label` varchar(10) NOT NULL COMMENT '显示值',
  `tips` varchar(200) NOT NULL COMMENT '使用提示',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='按钮事件表' AUTO_INCREMENT=18 ;

--
-- 转存表中的数据 `button_function`
--

INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`) VALUES
(1, 'sync', '同步', '保留搜索条件，刷新当前页，同步数据变更', 1, 0),
(2, 'reload', '刷新', '清除搜索条件，回到首页', 1, 0),
(3, 'closeTab', '离开', '关闭当前页签', 1, 0),
(4, 'pop', '弹窗', '无须post参数', 1, 0),
(5, 'pop2', '弹窗2', '需要传递行id', 1, 0),
(6, 'add', '新增(弹框)', '同pop', 1, 0),
(7, 'edit', '编辑(弹框)', '同pop2', 1, 0),
(8, 'delete', '删除(弹框)', '弹窗删除', 1, 0),
(9, 'retrieve', '详情(弹框)', '同pop2', 1, 0),
(10, 'view', '详情(页签)', '同newTab', 1, 0),
(11, 'retrieveEdit', '编辑(详情页编辑弹窗', '查看页的编辑按钮', 1, 0),
(12, 'retrieveDelete', '删除(详情页删除)', '查看页的删除按钮', 1, 0),
(13, 'sort', '排序(弹框)', '同pop', 1, 0),
(14, 'relList', '相关列表', '跳转到相应菜单列表，并将记录id带过去作为条件', 1, 0),
(15, 'confirm', '特殊处理', '', 1, 0),
(16, 'newTab', '新页签', '打开新页签，需要传递行id', 1, 0),
(17, 'cust', '自定义', '用户自定义函数处理', 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `button_icon`
--

CREATE TABLE IF NOT EXISTS `button_icon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '图标id',
  `name` varchar(40) NOT NULL COMMENT '图标名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='按钮图标表' AUTO_INCREMENT=453 ;

--
-- 转存表中的数据 `button_icon`
--

INSERT INTO `button_icon` (`id`, `name`) VALUES
(1, 'fa-plus'),
(2, 'fa-edit'),
(3, 'fa-trash-o'),
(4, 'fa-random'),
(5, 'fa-refresh'),
(6, 'fa-angle-double-left'),
(7, 'fa-search'),
(8, 'fa-arrow-circle-o-right'),
(9, 'fa-arrow-circle-o-left'),
(10, 'fa-asterisk'),
(11, 'fa-bar-chart-o'),
(12, 'fa-cloud-download'),
(13, 'fa-cloud-upload'),
(14, 'fa-download'),
(15, 'fa-female'),
(16, 'fa-exchange'),
(17, 'fa-eye'),
(18, 'fa-gear'),
(19, 'fa-gears'),
(20, 'fa-glass'),
(21, 'fa-group'),
(22, 'fa-heart'),
(23, 'fa-info'),
(24, 'fa-info-circle'),
(25, 'fa-key'),
(26, 'fa-male'),
(27, 'fa-money'),
(28, 'fa-tag'),
(29, 'fa-tags'),
(30, 'fa-unlock'),
(31, 'fa-user'),
(32, 'fa-wrench'),
(33, 'fa-chain'),
(34, 'fa-angle-double-up'),
(35, 'fa-save'),
(36, 'fa-undo'),
(37, 'fa-angle-double-right'),
(38, 'fa-arrow-left'),
(39, 'fa-arrow-right'),
(40, 'fa-arrow-up'),
(41, 'fa-arrow-down'),
(42, 'fa-rub'),
(43, 'fa-ruble'),
(44, 'fa-rouble'),
(45, 'fa-pagelines'),
(46, 'fa-stack-exchange'),
(47, 'fa-caret-square-o-left'),
(48, 'fa-toggle-left'),
(49, 'fa-dot-circle-o'),
(50, 'fa-wheelchair'),
(51, 'fa-vimeo-square'),
(52, 'fa-try'),
(53, 'fa-turkish-lira'),
(54, 'fa-plus-square-o'),
(55, 'fa-adjust'),
(56, 'fa-anchor'),
(57, 'fa-archive'),
(58, 'fa-arrows'),
(59, 'fa-arrows-h'),
(60, 'fa-arrows-v'),
(61, 'fa-ban'),
(62, 'fa-barcode'),
(63, 'fa-bars'),
(64, 'fa-beer'),
(65, 'fa-bell'),
(66, 'fa-bell-o'),
(67, 'fa-bolt'),
(68, 'fa-book'),
(69, 'fa-bookmark'),
(70, 'fa-bookmark-o'),
(71, 'fa-briefcase'),
(72, 'fa-bug'),
(73, 'fa-building-o'),
(74, 'fa-bullhorn'),
(75, 'fa-bullseye'),
(76, 'fa-calendar'),
(77, 'fa-calendar-o'),
(78, 'fa-camera'),
(79, 'fa-camera-retro'),
(80, 'fa-caret-square-o-down'),
(81, 'fa-caret-square-o-left'),
(82, 'fa-caret-square-o-right'),
(83, 'fa-caret-square-o-up'),
(84, 'fa-certificate'),
(85, 'fa-check'),
(86, 'fa-check-circle'),
(87, 'fa-check-circle-o'),
(88, 'fa-check-square'),
(89, 'fa-check-square-o'),
(90, 'fa-circle'),
(91, 'fa-circle-o'),
(92, 'fa-clock-o'),
(93, 'fa-cloud'),
(94, 'fa-code'),
(95, 'fa-code-fork'),
(96, 'fa-coffee'),
(97, 'fa-cog'),
(98, 'fa-cogs'),
(99, 'fa-comment'),
(100, 'fa-comment-o'),
(101, 'fa-comments'),
(102, 'fa-comments-o'),
(103, 'fa-compass'),
(104, 'fa-credit-card'),
(105, 'fa-crop'),
(106, 'fa-crosshairs'),
(107, 'fa-cutlery'),
(108, 'fa-dashboard'),
(109, 'fa-desktop'),
(110, 'fa-dot-circle-o'),
(111, 'fa-ellipsis-h'),
(112, 'fa-ellipsis-v'),
(113, 'fa-envelope'),
(114, 'fa-envelope-o'),
(115, 'fa-eraser'),
(116, 'fa-exclamation'),
(117, 'fa-exclamation-circle'),
(118, 'fa-exclamation-triangle'),
(119, 'fa-external-link'),
(120, 'fa-external-link-square'),
(121, 'fa-eye-slash'),
(122, 'fa-fighter-jet'),
(123, 'fa-film'),
(124, 'fa-filter'),
(125, 'fa-fire'),
(126, 'fa-fire-extinguisher'),
(127, 'fa-flag'),
(128, 'fa-flag-checkered'),
(129, 'fa-flag-o'),
(130, 'fa-flash'),
(131, 'fa-flask'),
(132, 'fa-folder'),
(133, 'fa-folder-o'),
(134, 'fa-folder-open'),
(135, 'fa-folder-open-o'),
(136, 'fa-frown-o'),
(137, 'fa-gamepad'),
(138, 'fa-gavel'),
(139, 'fa-gift'),
(140, 'fa-globe'),
(141, 'fa-hdd-o'),
(142, 'fa-headphones'),
(143, 'fa-heart-o'),
(144, 'fa-home'),
(145, 'fa-inbox'),
(146, 'fa-keyboard-o'),
(147, 'fa-laptop'),
(148, 'fa-leaf'),
(149, 'fa-legal'),
(150, 'fa-lemon-o'),
(151, 'fa-level-down'),
(152, 'fa-level-up'),
(153, 'fa-lightbulb-o'),
(154, 'fa-location-arrow'),
(155, 'fa-lock'),
(156, 'fa-magic'),
(157, 'fa-magnet'),
(158, 'fa-mail-forward'),
(159, 'fa-mail-reply'),
(160, 'fa-mail-reply-all'),
(161, 'fa-map-marker'),
(162, 'fa-meh-o'),
(163, 'fa-microphone'),
(164, 'fa-microphone-slash'),
(165, 'fa-minus'),
(166, 'fa-minus-circle'),
(167, 'fa-minus-square'),
(168, 'fa-minus-square-o'),
(169, 'fa-mobile'),
(170, 'fa-mobile-phone'),
(171, 'fa-moon-o'),
(172, 'fa-music'),
(173, 'fa-pencil'),
(174, 'fa-pencil-square'),
(175, 'fa-pencil-square-o'),
(176, 'fa-phone'),
(177, 'fa-phone-square'),
(178, 'fa-picture-o'),
(179, 'fa-plane'),
(180, 'fa-plus-circle'),
(181, 'fa-plus-square'),
(182, 'fa-plus-square-o'),
(183, 'fa-power-off'),
(184, 'fa-print'),
(185, 'fa-puzzle-piece'),
(186, 'fa-qrcode'),
(187, 'fa-question'),
(188, 'fa-question-circle'),
(189, 'fa-quote-left'),
(190, 'fa-quote-right'),
(191, 'fa-reply'),
(192, 'fa-reply-all'),
(193, 'fa-retweet'),
(194, 'fa-road'),
(195, 'fa-rocket'),
(196, 'fa-rss'),
(197, 'fa-rss-square'),
(198, 'fa-search-minus'),
(199, 'fa-search-plus'),
(200, 'fa-share'),
(201, 'fa-share-square'),
(202, 'fa-share-square-o'),
(203, 'fa-shield'),
(204, 'fa-shopping-cart'),
(205, 'fa-sign-in'),
(206, 'fa-sign-out'),
(207, 'fa-signal'),
(208, 'fa-sitemap'),
(209, 'fa-smile-o'),
(210, 'fa-sort'),
(211, 'fa-sort-alpha-asc'),
(212, 'fa-sort-alpha-desc'),
(213, 'fa-sort-amount-asc'),
(214, 'fa-sort-amount-desc'),
(215, 'fa-sort-asc'),
(216, 'fa-sort-desc'),
(217, 'fa-sort-down'),
(218, 'fa-sort-numeric-asc'),
(219, 'fa-sort-numeric-desc'),
(220, 'fa-sort-up'),
(221, 'fa-spinner'),
(222, 'fa-square'),
(223, 'fa-square-o'),
(224, 'fa-star'),
(225, 'fa-star-half'),
(226, 'fa-star-half-empty'),
(227, 'fa-star-half-full'),
(228, 'fa-star-half-o'),
(229, 'fa-star-o'),
(230, 'fa-subscript'),
(231, 'fa-suitcase'),
(232, 'fa-sun-o'),
(233, 'fa-superscript'),
(234, 'fa-tablet'),
(235, 'fa-tachometer'),
(236, 'fa-tasks'),
(237, 'fa-terminal'),
(238, 'fa-thumb-tack'),
(239, 'fa-thumbs-down'),
(240, 'fa-thumbs-o-down'),
(241, 'fa-thumbs-o-up'),
(242, 'fa-thumbs-up'),
(243, 'fa-ticket'),
(244, 'fa-times'),
(245, 'fa-times-circle'),
(246, 'fa-times-circle-o'),
(247, 'fa-tint'),
(248, 'fa-toggle-down'),
(249, 'fa-toggle-left'),
(250, 'fa-toggle-right'),
(251, 'fa-toggle-up'),
(252, 'fa-trophy'),
(253, 'fa-truck'),
(254, 'fa-umbrella'),
(255, 'fa-unlock-alt'),
(256, 'fa-unsorted'),
(257, 'fa-upload'),
(258, 'fa-users'),
(259, 'fa-video-camera'),
(260, 'fa-volume-down'),
(261, 'fa-volume-off'),
(262, 'fa-volume-up'),
(263, 'fa-warning'),
(264, 'fa-wheelchair'),
(265, 'fa-wrench'),
(266, 'fa-check-square'),
(267, 'fa-check-square-o'),
(268, 'fa-circle'),
(269, 'fa-circle-o'),
(270, 'fa-dot-circle-o'),
(271, 'fa-minus-square'),
(272, 'fa-minus-square-o'),
(273, 'fa-plus-square'),
(274, 'fa-plus-square-o'),
(275, 'fa-square'),
(276, 'fa-square-o'),
(277, 'fa-bitcoin'),
(278, 'fa-btc'),
(279, 'fa-cny'),
(280, 'fa-dollar'),
(281, 'fa-eur'),
(282, 'fa-euro'),
(283, 'fa-gbp'),
(284, 'fa-inr'),
(285, 'fa-jpy'),
(286, 'fa-krw'),
(287, 'fa-rmb'),
(288, 'fa-rouble'),
(289, 'fa-rub'),
(290, 'fa-ruble'),
(291, 'fa-rupee'),
(292, 'fa-try'),
(293, 'fa-turkish-lira'),
(294, 'fa-usd'),
(295, 'fa-won'),
(296, 'fa-yen'),
(297, 'fa-stethoscope'),
(298, 'fa-align-center'),
(299, 'fa-align-justify'),
(300, 'fa-align-left'),
(301, 'fa-align-right'),
(302, 'fa-bold'),
(303, 'fa-chain-broken'),
(304, 'fa-clipboard'),
(305, 'fa-columns'),
(306, 'fa-copy'),
(307, 'fa-cut'),
(308, 'fa-dedent'),
(309, 'fa-eraser'),
(310, 'fa-file'),
(311, 'fa-file-o'),
(312, 'fa-file-text'),
(313, 'fa-file-text-o'),
(314, 'fa-files-o'),
(315, 'fa-floppy-o'),
(316, 'fa-font'),
(317, 'fa-indent'),
(318, 'fa-italic'),
(319, 'fa-link'),
(320, 'fa-list'),
(321, 'fa-list-alt'),
(322, 'fa-list-ol'),
(323, 'fa-list-ul'),
(324, 'fa-outdent'),
(325, 'fa-paperclip'),
(326, 'fa-paste'),
(327, 'fa-repeat'),
(328, 'fa-rotate-left'),
(329, 'fa-rotate-right'),
(330, 'fa-scissors'),
(331, 'fa-strikethrough'),
(332, 'fa-table'),
(333, 'fa-text-height'),
(334, 'fa-text-width'),
(335, 'fa-th'),
(336, 'fa-th-large'),
(337, 'fa-th-list'),
(338, 'fa-underline'),
(339, 'fa-unlink'),
(340, 'fa-user-md'),
(341, 'fa-angle-double-down'),
(342, 'fa-angle-down'),
(343, 'fa-angle-left'),
(344, 'fa-angle-right'),
(345, 'fa-angle-up'),
(346, 'fa-arrow-circle-down'),
(347, 'fa-arrow-circle-left'),
(348, 'fa-arrow-circle-o-down'),
(349, 'fa-arrow-circle-o-up'),
(350, 'fa-arrow-circle-right'),
(351, 'fa-arrow-circle-up'),
(352, 'fa-arrows'),
(353, 'fa-arrows-alt'),
(354, 'fa-arrows-h'),
(355, 'fa-arrows-v'),
(356, 'fa-caret-down'),
(357, 'fa-caret-left'),
(358, 'fa-caret-right'),
(359, 'fa-caret-square-o-down'),
(360, 'fa-caret-square-o-left'),
(361, 'fa-caret-square-o-right'),
(362, 'fa-caret-square-o-up'),
(363, 'fa-caret-up'),
(364, 'fa-chevron-circle-down'),
(365, 'fa-chevron-circle-left'),
(366, 'fa-chevron-circle-right'),
(367, 'fa-chevron-circle-up'),
(368, 'fa-chevron-down'),
(369, 'fa-chevron-left'),
(370, 'fa-chevron-right'),
(371, 'fa-chevron-up'),
(372, 'fa-hand-o-down'),
(373, 'fa-hand-o-left'),
(374, 'fa-hand-o-right'),
(375, 'fa-hand-o-up'),
(376, 'fa-long-arrow-down'),
(377, 'fa-long-arrow-left'),
(378, 'fa-long-arrow-right'),
(379, 'fa-long-arrow-up'),
(380, 'fa-toggle-down'),
(381, 'fa-toggle-left'),
(382, 'fa-toggle-right'),
(383, 'fa-toggle-up'),
(384, 'fa-wheelchair'),
(385, 'fa-arrows-alt'),
(386, 'fa-backward'),
(387, 'fa-compress'),
(388, 'fa-eject'),
(389, 'fa-expand'),
(390, 'fa-fast-backward'),
(391, 'fa-fast-forward'),
(392, 'fa-forward'),
(393, 'fa-pause'),
(394, 'fa-play'),
(395, 'fa-play-circle'),
(396, 'fa-play-circle-o'),
(397, 'fa-step-backward'),
(398, 'fa-step-forward'),
(399, 'fa-stop'),
(400, 'fa-youtube-play'),
(401, 'fa-adn'),
(402, 'fa-android'),
(403, 'fa-apple'),
(404, 'fa-bitbucket'),
(405, 'fa-bitbucket-square'),
(406, 'fa-bitcoin'),
(407, 'fa-btc'),
(408, 'fa-css3'),
(409, 'fa-dribbble'),
(410, 'fa-dropbox'),
(411, 'fa-facebook'),
(412, 'fa-facebook-square'),
(413, 'fa-flickr'),
(414, 'fa-foursquare'),
(415, 'fa-github'),
(416, 'fa-github-alt'),
(417, 'fa-github-square'),
(418, 'fa-gittip'),
(419, 'fa-google-plus'),
(420, 'fa-google-plus-square'),
(421, 'fa-html5'),
(422, 'fa-instagram'),
(423, 'fa-linkedin'),
(424, 'fa-linkedin-square'),
(425, 'fa-linux'),
(426, 'fa-maxcdn'),
(427, 'fa-pagelines'),
(428, 'fa-pinterest'),
(429, 'fa-pinterest-square'),
(430, 'fa-renren'),
(431, 'fa-skype'),
(432, 'fa-stack-exchange'),
(433, 'fa-stack-overflow'),
(434, 'fa-trello'),
(435, 'fa-tumblr'),
(436, 'fa-tumblr-square'),
(437, 'fa-twitter'),
(438, 'fa-twitter-square'),
(439, 'fa-vimeo-square'),
(440, 'fa-vk'),
(441, 'fa-weibo'),
(442, 'fa-windows'),
(443, 'fa-xing'),
(444, 'fa-xing-square'),
(445, 'fa-youtube'),
(446, 'fa-youtube-play'),
(447, 'fa-youtube-square'),
(448, 'fa-ambulance'),
(449, 'fa-h-square'),
(450, 'fa-hospital-o'),
(451, 'fa-medkit'),
(452, 'fa-plus-square');

-- --------------------------------------------------------

--
-- 表的结构 `control`
--

CREATE TABLE IF NOT EXISTS `control` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `label` varchar(10) NOT NULL COMMENT '显示名称',
  `code` varchar(20) NOT NULL COMMENT '控制器名',
  `application_id` int(11) NOT NULL COMMENT '所属项目',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='文件表' AUTO_INCREMENT=26 ;

--
-- 转存表中的数据 `control`
--

INSERT INTO `control` (`id`, `label`, `code`, `application_id`, `is_deleted`, `is_system`) VALUES
(1, '项目', 'Application', 1, 0, 1),
(2, '图标', 'ButtonIcon', 1, 0, 1),
(3, '文件', 'Control', 1, 0, 1),
(4, '菜单组', 'MenuGroup', 1, 0, 1),
(5, '样式', 'ButtonClass', 1, 0, 1),
(6, '数据字典', 'Dict', 1, 0, 1),
(7, '菜单', 'Menu', 1, 0, 1),
(8, '操作', 'Operation', 1, 0, 1),
(9, '部门', 'Department', 1, 0, 1),
(10, '用户', 'User', 1, 0, 1),
(11, '按钮', 'Button', 1, 0, 1),
(12, '按钮事件', 'ButtonFunction', 1, 0, 1),
(13, '系统访问日志', 'SystemAccessLog', 1, 0, 1),
(14, '岗位设置', 'Organization', 1, 0, 1),
(15, '工作组', 'Group', 1, 0, 1),
(16, '角色管理', 'Role', 1, 0, 1),
(17, '支付方式', 'Payment', 1, 0, 1),
(18, '友情链接', 'ForumLinks', 1, 0, 1),
(19, '权限管理', 'Permission', 1, 0, 1),
(20, '资源类型', 'ResourceType', 1, 0, 1),
(21, '组角色管理', 'GroupRole', 1, 0, 1),
(22, '角色授权', 'RolePermission', 1, 0, 1),
(23, '角色用户', 'RoleUser', 1, 0, 1),
(24, '组用户管理', 'GroupUser', 1, 0, 1),
(25, '用户授权', 'UserPermission', 1, 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `department`
--

CREATE TABLE IF NOT EXISTS `department` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '部门id',
  `name` char(50) NOT NULL COMMENT '部门名称',
  `code` char(50) DEFAULT NULL COMMENT '部门编码',
  `note` varchar(255) DEFAULT NULL COMMENT '描述',
  `parent_id` int(11) NOT NULL COMMENT '上级部门id',
  `tree_path` varchar(512) NOT NULL COMMENT '全路径',
  `pids` varchar(200) DEFAULT NULL COMMENT '祖先分类',
  `childrens` int(11) NOT NULL DEFAULT '0' COMMENT '下级分类数',
  `display_order` int(11) NOT NULL COMMENT '显示顺序',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='部门/组织架构' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `department`
--

INSERT INTO `department` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES
(1, '总部', 'K01', '总部123', 0, '0', '', 4, 1, 0, 1),
(2, '线上事业部', 'K002', '热我认为', 1, '0-1', '1', 0, 1418437719, 0, 0),
(3, '线下事业部', 'K003', 'fdsfs', 1, '0-1', '1', 0, 1418455327, 0, 0),
(4, '第一事业部', NULL, NULL, 3, '0-1-3', '1,3', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `dict`
--

CREATE TABLE IF NOT EXISTS `dict` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(40) NOT NULL COMMENT '属性',
  `label` varchar(20) NOT NULL COMMENT '标识',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否内置',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='数据字典' AUTO_INCREMENT=9 ;

--
-- 转存表中的数据 `dict`
--

INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES
(1, 'user.user_type', '用户类型', 1, 0),
(2, 'is_enabled', '启用禁用', 1, 0),
(3, 'user.is_on_work', '员工状态', 1, 0),
(4, 'gender', '性别', 1, 0),
(5, 'confirm', '是否', 1, 0),
(6, 'login_status', '登录状态', 1, 0),
(7, 'position_level', '职级', 1, 0),
(8, 'position', '职位', 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `dict_item`
--

CREATE TABLE IF NOT EXISTS `dict_item` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '明细主键',
  `dict_id` int(11) NOT NULL COMMENT '字典id',
  `name` tinyint(4) NOT NULL COMMENT '枚举key',
  `label` varchar(10) NOT NULL COMMENT '枚举显示标识',
  `note` varchar(200) DEFAULT NULL COMMENT '描述',
  `display_order` int(11) NOT NULL COMMENT '顺序号',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='字典明细表' AUTO_INCREMENT=38 ;

--
-- 转存表中的数据 `dict_item`
--

INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES
(1, 1, 1, '超级管理员', '超级管理员', 1434567890, 1, 0),
(2, 1, 2, '权限管理员', NULL, 1435677779, 1, 0),
(3, 1, 3, '系统用户', NULL, 1435677778, 1, 0),
(4, 2, 1, '启用', '启用', 1417954105, 1, 0),
(5, 2, 0, '停用', '停用', 1417954167, 1, 0),
(6, 3, 1, '在职', '在职', 1417954865, 1, 0),
(7, 3, 0, '离职', '离职', 1417955381, 1, 0),
(8, 4, 0, '男', '男', 1418010228, 1, 0),
(9, 4, 1, '女', '女', 1418010445, 1, 0),
(10, 5, 0, '否', '', 1418104095, 1, 0),
(11, 5, 1, '是', '', 1418104105, 1, 0),
(12, 6, 1, '用户不存在', NULL, 1, 1, 0),
(13, 6, 2, '账户未启用', NULL, 2, 1, 0),
(14, 6, 3, '密码不正确', NULL, 3, 1, 0),
(15, 6, 4, '登陆成功', NULL, 4, 1, 0),
(16, 6, 5, '退出成功', NULL, 5, 1, 0),
(17, 6, 6, '修改密码', NULL, 6, 1, 0),
(18, 6, 7, '邮箱不正确', NULL, 7, 1, 0),
(19, 7, 1, '初级', '', 1418366339, 0, 0),
(20, 7, 2, '助理', '', 1418366356, 0, 0),
(21, 7, 3, '中级', '', 1418366369, 0, 0),
(22, 7, 4, '副高级', '', 1418371308, 0, 0),
(23, 7, 5, '高级', '', 1418371322, 0, 0),
(24, 8, 1, '董事长', '', 1418374417, 0, 0),
(25, 8, 2, '总经理', '', 1418374694, 0, 0),
(26, 8, 3, '副总经理', '', 1418375288, 0, 0),
(27, 8, 4, '总监', '', 1418375392, 0, 0),
(28, 8, 5, '经理', '', 1418375556, 0, 0),
(29, 8, 6, '专员', '', 1418375564, 0, 0),
(30, 8, 7, '主管', '', 1418375571, 0, 0),
(31, 8, 8, '收银', '', 1418375604, 0, 0),
(32, 8, 9, '出纳', '', 1418375611, 0, 0),
(33, 8, 10, '前台文员', '', 1418375649, 0, 0),
(34, 8, 11, '秘书', '', 1418375654, 0, 0),
(35, 8, 12, '顾问', '', 1418375669, 0, 0),
(36, 8, 13, '咨询师', '', 1418375719, 0, 0),
(37, 8, 14, '工程师', '', 1418375727, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `forum_links`
--

CREATE TABLE IF NOT EXISTS `forum_links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(40) NOT NULL COMMENT '显示文字',
  `url_addr` varchar(200) NOT NULL COMMENT '链接地址',
  `display_order` tinyint(4) NOT NULL DEFAULT '0' COMMENT '显示顺序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='友情链接表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `group`
--

CREATE TABLE IF NOT EXISTS `group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '组id',
  `name` char(50) NOT NULL COMMENT '组名称',
  `code` char(50) DEFAULT NULL COMMENT '组编码',
  `note` varchar(255) DEFAULT NULL COMMENT '描述',
  `parent_id` int(11) NOT NULL COMMENT '上级组id',
  `tree_path` varchar(512) NOT NULL COMMENT '全路径',
  `pids` varchar(200) DEFAULT NULL COMMENT '祖先分类',
  `childrens` int(11) NOT NULL DEFAULT '0' COMMENT '下级分类数',
  `display_order` int(11) NOT NULL COMMENT '显示顺序',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='工作组表' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `group`
--

INSERT INTO `group` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES
(1, '张三', '123', '方的身份的身份放到沙发上', 0, '0', '', 3, 1418718419, 0, 0),
(2, '李四', '111', '放到沙发上订单111', 1, '0-1', '1', 0, 1418718447, 0, 0),
(3, '王五', '222', '范德萨范德萨', 1, '0-1', '1', 0, 1418718474, 0, 0),
(4, '放到沙发上', '12345', '', 1, '0-1', '1', 0, 1418718698, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `group_role`
--

CREATE TABLE IF NOT EXISTS `group_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `group_id` int(11) NOT NULL COMMENT '组id',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组角色表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `group_user`
--

CREATE TABLE IF NOT EXISTS `group_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(11) NOT NULL COMMENT '用户',
  `group_id` int(11) NOT NULL COMMENT '组别',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='组用户管理' AUTO_INCREMENT=15 ;

--
-- 转存表中的数据 `group_user`
--

INSERT INTO `group_user` (`id`, `user_id`, `group_id`) VALUES
(14, 1, 2);

-- --------------------------------------------------------

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='系统菜单表' AUTO_INCREMENT=27 ;

--
-- 转存表中的数据 `menu`
--

INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES
(1, 6, 3, '数据字典', 'DICT', 'index.php?mod=management&con=dict&act=index', 1, 1, 1, 1419079055, 1, 1, 0),
(2, 1, 1, '项目管理', 'APPLICATION', 'index.php?mod=management&con=application&act=index', 1, 1, 1, 100, 1, 1, 0),
(3, 7, 7, '菜单管理', 'MENU', 'index.php?mod=management&con=menu&act=index', 1, 2, 1, 91, 1, 1, 0),
(4, 4, 5, '菜单分组', 'MENU_GROUP', 'index.php?mod=management&con=menuGroup&act=index', 1, 1, 1, 99, 1, 1, 0),
(5, 3, 8, '文件管理', 'CONTROL', 'index.php?mod=management&con=control&act=index', 1, 2, 1, 98, 1, 1, 0),
(6, 5, 6, '按钮样式', 'BUTTON_CLASS', 'index.php?mod=management&con=buttonClass&act=index', 1, 6, 1, 94, 1, 1, 0),
(7, 2, 4, '按钮图标', 'BUTTON_ICON', 'index.php?mod=management&con=buttonIcon&act=index', 1, 6, 1, 95, 1, 1, 0),
(8, 9, 10, '部门管理', 'DEPARTMENT', 'index.php?mod=management&con=department&act=index', 1, 3, 1, 93, 1, 1, 0),
(9, 10, 11, '用户管理', 'USER', 'index.php?mod=management&con=user&act=index', 1, 3, 1, 86, 1, 1, 0),
(10, 8, 9, '操作管理', 'OPERATION', 'index.php?mod=management&con=operation&act=index', 1, 2, 1, 96, 1, 1, 0),
(11, 10, 12, '已删用户', 'USER_RECYCLE', 'index.php?mod=management&con=user&act=recycle', 1, 5, 1, 90, 1, 1, 0),
(12, 11, 13, '按钮管理', 'BUTTON', 'index.php?mod=management&con=button&act=index', 2, 6, 1, 88, 1, 1, 0),
(13, 12, 14, '按钮事件', 'BUTTON_FUNCTION', 'index.php?mod=management&con=buttonFunction&act=index', 5, 6, 1, 89, 1, 1, 0),
(14, 13, 15, '访问日志', 'SYSTEM_ACCESS_LOG', 'index.php?mod=management&con=systemAccessLog&act=index', 2, 4, 1, 87, 1, 1, 0),
(15, 14, 17, '岗位设置', 'ORGANIZATION', 'index.php?mod=management&con=organization&act=index', 204, 3, 1, 84, 1, 1, 0),
(16, 24, 184, '组用户管理', 'GROUP_USER', 'index.php?mod=management&con=GroupUser&act=index', 33, 8, 1, 1419299580, 1, 1, 0),
(17, 15, 18, '工作组', 'GROUP', 'index.php?mod=management&con=group&act=index', 5, 3, 1, 92, 1, 1, 0),
(18, 16, 19, '角色管理', 'ROLE', 'index.php?mod=management&con=Role&act=index', 1, 8, 1, 1419821775, 1, 1, 0),
(19, 17, 34, '支付方式', 'PAYMENT', 'index.php?mod=management&con=Payment&act=index', 204, 7, 1, 1418961357, 1, 1, 0),
(20, 18, 146, '友情链接', 'FORUM_LINKS', 'index.php?mod=management&con=ForumLinks&act=index', 204, 7, 1, 1418970438, 1, 1, 0),
(21, 19, 155, '权限管理', 'PERMISSION', 'index.php?mod=management&con=Permission&act=index', 20, 8, 1, 1419491411, 1, 1, 0),
(22, 20, 164, '资源类型', 'RESOURCE_TYPE', 'index.php?mod=management&con=ResourceType&act=index', 23, 1, 1, 97, 1, 1, 0),
(23, 21, 171, '组角色管理', 'GROUP_ROLE', 'index.php?mod=management&con=GroupRole&act=index', 17, 8, 1, 1419301219, 1, 1, 0),
(24, 22, 178, '角色授权', 'ROLE_PERMISSION', 'index.php?mod=management&con=RolePermission&act=index', 32, 8, 1, 1419043097, 1, 1, 0),
(25, 23, 183, '角色用户', 'ROLE_USER', 'index.php?mod=management&con=RoleUser&act=index', 30, 8, 1, 1419217981, 1, 1, 0),
(26, 25, 185, '用户授权', 'USER_PERMISSION', 'index.php?mod=management&con=UserPermission&act=index', 29, 8, 1, 1418812163, 1, 1, 0);

-- --------------------------------------------------------

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='菜单分组表' AUTO_INCREMENT=9 ;

--
-- 转存表中的数据 `menu_group`
--

INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES
(1, '系统配置', 1, 2, 1419040533, 1, 0, 1),
(2, '菜单管理', 1, 2, 1419039873, 1, 0, 1),
(3, '用户管理', 1, 2, 1419040476, 1, 0, 1),
(4, '日志管理', 1, 2, 97, 1, 0, 1),
(5, '回收站', 1, 2, 96, 1, 0, 1),
(6, '按钮管理', 1, 406, 99, 1, 0, 1),
(7, '通用模块', 1, 65, 95, 1, 0, 1),
(8, '权限管理', 1, 72, 98, 1, 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `operation`
--

CREATE TABLE IF NOT EXISTS `operation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `method_name` varchar(30) NOT NULL COMMENT '方法名称',
  `label` varchar(30) NOT NULL COMMENT '显示标识',
  `c_id` int(11) NOT NULL COMMENT '所属控制器',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='操作表' AUTO_INCREMENT=217 ;

--
-- 转存表中的数据 `operation`
--

INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES
(1, 'index', '默认页', 1, 0, 0),
(2, 'search', '数据分页', 1, 0, 0),
(3, 'index', '默认页', 6, 0, 0),
(4, 'index', '默认页', 2, 0, 0),
(5, 'index', '默认页', 4, 0, 0),
(6, 'index', '默认页', 5, 0, 0),
(7, 'index', '默认页', 7, 0, 0),
(8, 'index', '默认页', 3, 0, 0),
(9, 'index', '默认页', 8, 0, 0),
(10, 'index', '默认页', 9, 0, 0),
(11, 'index', '默认页', 10, 0, 0),
(12, 'recycle', '用户回收站默认页', 10, 0, 0),
(13, 'index', '默认页', 11, 0, 0),
(14, 'index', '默认页', 12, 0, 0),
(15, 'index', '默认页', 13, 0, 0),
(16, 'add', '添加项目', 1, 0, 0),
(17, 'index', '默认页', 14, 0, 0),
(18, 'index', '默认页', 15, 0, 0),
(19, 'index', '默认页', 16, 0, 0),
(20, 'edit', '编辑', 1, 0, 0),
(21, 'show', '详情', 1, 0, 0),
(22, 'insert', '保存', 1, 0, 0),
(23, 'update', '更新', 1, 0, 0),
(24, 'delete', '删除', 1, 0, 0),
(25, 'moveup', '上移', 1, 0, 0),
(26, 'movedown', '下移', 1, 0, 0),
(27, 'search', '数据分页', 5, 0, 0),
(28, 'add', '添加', 5, 0, 0),
(29, 'edit', '编辑', 5, 0, 0),
(30, 'show', '详情', 5, 0, 0),
(31, 'insert', '保存', 5, 0, 0),
(32, 'update', '更新', 5, 0, 0),
(33, 'delete', '删除', 5, 0, 0),
(34, 'index', '默认页', 17, 0, 0),
(35, 'search', '数据分页', 3, 0, 0),
(36, 'add', '添加', 3, 0, 0),
(37, 'edit', '编辑', 3, 0, 0),
(38, 'save', '保存', 3, 0, 0),
(39, 'update', '更新', 3, 0, 0),
(40, 'delete', '删除', 3, 0, 0),
(41, 'search', '数据分页', 2, 0, 0),
(42, 'add', '添加', 2, 0, 0),
(43, 'edit', '编辑', 2, 0, 0),
(44, 'insert', '保存', 2, 0, 0),
(45, 'update', '更新', 2, 0, 0),
(46, 'delete', '删除', 2, 0, 0),
(47, 'show', '详情', 3, 0, 0),
(48, 'search', '数据分页', 4, 0, 0),
(49, 'add', '添加', 4, 0, 0),
(50, 'edit', '编辑', 4, 0, 0),
(51, 'show', '详情', 4, 0, 0),
(52, 'insert', '保存', 4, 0, 0),
(53, 'update', '更新', 4, 0, 0),
(54, 'delete', '删除', 4, 0, 0),
(55, 'dictList', '字典列表', 6, 0, 0),
(56, 'add', '字典添加', 6, 0, 0),
(57, 'edit', '字典编辑', 6, 0, 0),
(58, 'delete', '字典删除', 6, 0, 0),
(59, 'insert', '保存字典', 6, 0, 0),
(60, 'update', '更新字典', 6, 0, 0),
(61, 'search', '字典明细', 6, 0, 0),
(62, 'addItem', '添加字典明细', 6, 0, 0),
(63, 'editItem', '编辑字典明细', 6, 0, 0),
(64, 'deleteItem', '删除字典明细', 6, 0, 0),
(65, 'insertItem', '保存字典明细', 6, 0, 0),
(66, 'updateItem', '更新字典明细', 6, 0, 0),
(67, 'moveup', '上移', 4, 0, 0),
(68, 'movedown', '下移', 4, 0, 0),
(69, 'search', '数据分页', 7, 0, 0),
(70, 'add', '添加', 7, 0, 0),
(71, 'edit', '编辑', 7, 0, 0),
(72, 'insert', '保存', 7, 0, 0),
(73, 'update', '更新', 7, 0, 0),
(74, 'delete', '删除', 7, 0, 0),
(75, 'moveup', '上移', 7, 0, 0),
(76, 'movedown', '下移', 7, 0, 0),
(77, 'layoutButton', '分配按钮', 7, 0, 0),
(78, 'saveButton', '分配按钮保存', 7, 0, 0),
(79, 'listButton', '按钮排序页面', 7, 0, 0),
(80, 'saveSort', '保存排序', 7, 0, 0),
(81, 'search', '数据分页', 8, 0, 0),
(82, 'add', '添加', 8, 0, 0),
(83, 'edit', '编辑', 8, 0, 0),
(84, 'delete', '删除', 8, 0, 0),
(85, 'show', '详情', 8, 0, 0),
(86, 'insert', '保存', 8, 0, 0),
(87, 'update', '更新', 8, 0, 0),
(88, 'search', '数据列表', 9, 0, 0),
(89, 'add', '添加', 9, 0, 0),
(90, 'edit', '编辑', 9, 0, 0),
(91, 'delete', '删除', 9, 0, 0),
(92, 'show', '详情', 9, 0, 0),
(93, 'insert', '保存', 9, 0, 0),
(94, 'update', '更新', 9, 0, 0),
(95, 'moveup', '上移', 9, 0, 0),
(96, 'movedown', '下移', 9, 0, 0),
(97, 'search', '数据分页', 10, 0, 0),
(98, 'add', '添加', 10, 0, 0),
(99, 'edit', '编辑', 10, 0, 0),
(100, 'show', '详情', 10, 0, 0),
(101, 'modify', '重置密码', 10, 0, 0),
(102, 'setModify', '保存密码', 10, 0, 0),
(103, 'insert', '保存', 10, 0, 0),
(104, 'update', '更新', 10, 0, 0),
(105, 'setEnabled', '生效', 10, 0, 0),
(106, 'setDisabled', '失效', 10, 0, 0),
(107, 'setLeave', '离职', 10, 0, 0),
(108, 'setOnWork', '入职', 10, 0, 0),
(109, 'delete', '删除', 10, 0, 0),
(110, 'recover', '恢复', 10, 0, 0),
(111, 'recycleList', '用户回收站分页', 10, 0, 0),
(112, 'search', '数据分页', 11, 0, 0),
(113, 'add', '添加', 11, 0, 0),
(114, 'edit', '编辑', 11, 0, 0),
(115, 'delete', '删除', 11, 0, 0),
(116, 'insert', '保存', 11, 0, 0),
(117, 'update', '更新', 11, 0, 0),
(118, 'search', '数据列表', 12, 0, 0),
(119, 'add', '添加', 12, 0, 0),
(120, 'edit', '编辑', 12, 0, 0),
(121, 'delete', '删除', 12, 0, 0),
(122, 'insert', '保存', 12, 0, 0),
(123, 'update', '更新', 12, 0, 0),
(124, 'search', '数据分页', 13, 0, 0),
(125, 'search', '数据分页', 14, 0, 0),
(126, 'add', '添加', 14, 0, 0),
(127, 'edit', '编辑', 14, 0, 0),
(128, 'delete', '删除', 14, 0, 0),
(129, 'insert', '保存', 14, 0, 0),
(130, 'update', '更新', 14, 0, 0),
(131, 'search', '数据列表', 16, 0, 0),
(132, 'add', '添加', 16, 0, 0),
(133, 'edit', '编辑', 16, 0, 0),
(134, 'delete', '删除', 16, 0, 0),
(136, 'insert', '保存', 16, 0, 0),
(137, 'update', '更新', 16, 0, 0),
(140, 'search', '数据分页', 17, 0, 0),
(141, 'add', '添加', 17, 0, 0),
(142, 'edit', '编辑', 17, 0, 0),
(143, 'delete', '删除', 17, 0, 0),
(144, 'insert', '保存', 17, 0, 0),
(145, 'update', '更新', 17, 0, 0),
(146, 'index', '默认页', 18, 0, 0),
(147, 'search', '数据分页', 18, 0, 0),
(148, 'add', '添加', 18, 0, 0),
(149, 'edit', '编辑', 18, 0, 0),
(150, 'delete', '删除', 18, 0, 0),
(151, 'listAll', '排序', 18, 0, 0),
(152, 'insert', '保存', 18, 0, 0),
(153, 'update', '更新', 18, 0, 0),
(154, 'saveSort', '保存排序', 18, 0, 0),
(155, 'index', '默认页', 19, 0, 0),
(156, 'search', '数据分页', 19, 0, 0),
(157, 'add', '添加', 19, 0, 0),
(158, 'edit', '编辑', 19, 0, 0),
(159, 'delete', '删除', 19, 0, 0),
(160, 'insert', '保存', 19, 0, 0),
(161, 'update', '更新', 19, 0, 0),
(164, 'index', '默认页', 20, 0, 0),
(165, 'search', '数据分页', 20, 0, 0),
(166, 'add', '添加', 20, 0, 0),
(167, 'edit', '编辑', 20, 0, 0),
(168, 'delete', '删除', 20, 0, 0),
(169, 'insert', '保存', 20, 0, 0),
(170, 'update', '更新', 20, 0, 0),
(171, 'index', '默认页', 21, 0, 0),
(172, 'search', '数据列表', 21, 0, 0),
(173, 'add', '添加角色', 21, 0, 0),
(175, 'delete', '删除角色', 21, 0, 0),
(176, 'insert', '保存角色', 21, 0, 0),
(178, 'index', '默认页', 22, 0, 0),
(179, 'listMenu', '菜单列表', 22, 0, 0),
(180, 'saveMenu', '保存菜单授权', 22, 0, 0),
(181, 'listButton', '按钮菜单列表', 22, 0, 0),
(182, 'saveButton', '保存按钮授权', 22, 0, 0),
(183, 'index', '默认页', 23, 0, 0),
(184, 'index', '默认页', 24, 0, 0),
(185, 'index', '默认页', 25, 0, 0),
(186, 'search', '数据分页', 24, 0, 0),
(187, 'add', '组用户添加', 24, 0, 0),
(188, 'insert', '保存组用户', 24, 0, 0),
(189, 'delete', '删除组用户', 24, 0, 0),
(190, 'listMenu', '菜单权限', 25, 0, 0),
(191, 'saveMenu', '菜单授权', 25, 0, 0),
(192, 'listButton', '按钮列表菜单', 25, 0, 0),
(193, 'buttonList', '按钮授权', 25, 0, 0),
(194, 'saveButton', '保存按钮授权', 25, 0, 0),
(195, 'listOpr', '操作菜单列表', 25, 0, 0),
(196, 'oprList', '操作授权', 25, 0, 0),
(197, 'saveOpr', '保存操作授权', 25, 0, 0),
(198, 'search', '数据分页', 15, 0, 0),
(199, 'add', '添加组', 15, 0, 0),
(200, 'edit', '编辑组', 15, 0, 0),
(201, 'delete', '删除组', 15, 0, 0),
(202, 'show', '查看组', 15, 0, 0),
(203, 'insert', '保存组', 15, 0, 0),
(204, 'update', '更新组', 15, 0, 0),
(205, 'moveup', '上移', 15, 0, 0),
(206, 'movedown', '下移', 15, 0, 0),
(207, 'listAll', '排序', 17, 0, 0),
(208, 'saveSort', '保存排序', 17, 0, 0),
(209, 'listOpr', '操作权限菜单列表', 22, 0, 0),
(210, 'saveOpr', '保存操作授权', 22, 0, 0),
(211, 'search', '数据分页', 23, 0, 0),
(212, 'add', '添加用户', 23, 0, 0),
(213, 'insert', '保存', 23, 0, 0),
(214, 'delete', '删除用户', 23, 0, 0),
(215, 'buttonList', '按钮授权', 22, 0, 0),
(216, 'oprList', '操作授权', 22, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `organization`
--

CREATE TABLE IF NOT EXISTS `organization` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `dept_id` int(11) NOT NULL DEFAULT '0' COMMENT '部门id',
  `position` tinyint(4) NOT NULL DEFAULT '0' COMMENT '职位',
  `level` tinyint(4) NOT NULL DEFAULT '0' COMMENT '职级',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='岗位设置表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `payment`
--

CREATE TABLE IF NOT EXISTS `payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pay_code` varchar(20) NOT NULL COMMENT '支付方式拼音',
  `pay_name` varchar(20) NOT NULL COMMENT '支付方式中文名',
  `pay_fee` decimal(10,2) NOT NULL COMMENT '支付手续费',
  `pay_desc` text NOT NULL COMMENT '描述',
  `pay_order` tinyint(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `pay_config` text NOT NULL COMMENT '配置项',
  `is_enabled` tinyint(4) NOT NULL COMMENT '是否启用',
  `is_cod` tinyint(4) NOT NULL COMMENT '是否货到付款',
  `is_display` tinyint(4) NOT NULL COMMENT '是否显示',
  `is_web` tinyint(4) NOT NULL COMMENT '是否网络付款可用',
  `addby_id` int(11) DEFAULT NULL COMMENT '创建人',
  `add_time` int(11) NOT NULL COMMENT '创建时间',
  `is_deleted` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付方式表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `permission`
--

CREATE TABLE IF NOT EXISTS `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '权限id',
  `type` int(11) NOT NULL COMMENT '资源类型id',
  `resource_id` int(11) NOT NULL DEFAULT '0' COMMENT '资源id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `code` varchar(50) DEFAULT NULL COMMENT '编码',
  `note` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='权限表' AUTO_INCREMENT=347 ;

--
-- 转存表中的数据 `permission`
--

INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES
(1, 1, 15, '岗位设置-菜单权限', 'ORGANIZATION_M', '', 0, 1),
(2, 1, 9, '用户管理-菜单权限', 'USER_M', '', 0, 1),
(3, 1, 14, '访问日志-菜单权限', 'SYSTEM_ACCESS_LOG_M', '', 0, 1),
(4, 1, 12, '按钮管理-菜单权限', 'BUTTON_M', '', 0, 1),
(5, 1, 13, '按钮事件-菜单权限', 'BUTTON_FUNCTION_M', '', 0, 1),
(6, 1, 11, '已删用户-菜单权限', 'USER_RECYCLE_M', '', 0, 1),
(7, 1, 3, '菜单管理-菜单权限', 'MENU_M', '', 0, 1),
(8, 1, 17, '工作组-菜单权限', 'GROUP_M', '', 0, 1),
(9, 1, 8, '部门管理-菜单权限', 'DEPARTMENT_M', '', 0, 1),
(10, 1, 6, '按钮样式-菜单权限', 'BUTTON_CLASS_M', '', 0, 1),
(11, 1, 7, '按钮图标-菜单权限', 'BUTTON_ICON_M', '', 0, 1),
(12, 1, 10, '操作管理-菜单权限', 'OPERATION_M', '', 0, 1),
(13, 1, 4, '菜单分组-菜单权限', 'MENU_GROUP_M', '', 0, 1),
(14, 1, 5, '文件管理-菜单权限', 'CONTROL_M', '', 0, 1),
(15, 1, 2, '项目管理-菜单权限', 'APPLICATION_M', '', 0, 1),
(16, 1, 1, '数据字典-菜单权限', 'DICT_M', '', 0, 1),
(17, 1, 18, '角色管理-菜单权限', 'ROLE_M', '', 0, 1),
(18, 1, 19, '支付方式-菜单权限', 'PAYMENT_M', '', 0, 1),
(19, 1, 20, '友情链接-菜单权限', 'FORUM_LINKS_M', '', 0, 1),
(20, 1, 21, '权限管理-菜单权限', 'PERMISSION_M', '', 0, 1),
(21, 1, 22, '资源类型-菜单权限', 'RESOURCE_TYPE_M', '', 0, 1),
(22, 1, 23, '组角色管理-菜单权限', 'GROUP_ROLE_M', '', 0, 1),
(23, 1, 24, '角色授权-菜单权限', 'ROLE_PERMISSION_M', '', 0, 1),
(24, 1, 25, '角色用户-菜单权限', 'ROLE_USER_M', '', 0, 1),
(25, 1, 26, '用户授权-菜单权限', 'USER_PERMISSION_M', '', 0, 1),
(26, 1, 16, '组用户管理-菜单权限', 'GROUP_USER_M', '', 0, 1),
(27, 4, 1, '项目-数据权限', 'OBJ_APPLICATION', '', 0, 1),
(28, 4, 2, '图标-数据权限', 'OBJ_BUTTON_ICON', '', 0, 1),
(29, 4, 3, '文件-数据权限', 'OBJ_CONTROL', '', 0, 1),
(30, 4, 4, '菜单组-数据权限', 'OBJ_MENU_GROUP', '', 0, 1),
(31, 4, 5, '样式-数据权限', 'OBJ_BUTTON_CLASS', '', 0, 1),
(32, 4, 6, '数据字典-数据权限', 'OBJ_DICT', '', 0, 1),
(33, 4, 7, '菜单-数据权限', 'OBJ_MENU', '', 0, 1),
(34, 4, 8, '操作-数据权限', 'OBJ_OPERATION', '', 0, 1),
(35, 4, 9, '部门-数据权限', 'OBJ_DEPARTMENT', '', 0, 1),
(36, 4, 10, '用户-数据权限', 'OBJ_USER', '', 0, 1),
(37, 4, 11, '按钮-数据权限', 'OBJ_BUTTON', '', 0, 1),
(38, 4, 12, '按钮事件-数据权限', 'OBJ_BUTTON_FUNCTION', '', 0, 1),
(39, 4, 13, '系统访问日志-数据权限', 'OBJ_SYSTEM_ACCESS_LOG', '', 0, 1),
(40, 4, 14, '岗位设置-数据权限', 'OBJ_ORGANIZATION', '', 0, 1),
(41, 4, 15, '工作组-数据权限', 'OBJ_GROUP', '', 0, 1),
(42, 4, 16, '角色管理-数据权限', 'OBJ_ROLE', '', 0, 1),
(43, 4, 17, '支付方式-数据权限', 'OBJ_PAYMENT', '', 0, 1),
(44, 4, 18, '友情链接-数据权限', 'OBJ_FORUM_LINKS', '', 0, 1),
(45, 4, 19, '权限管理-数据权限', 'OBJ_PERMISSION', '', 0, 1),
(46, 4, 20, '资源类型-数据权限', 'OBJ_RESOURCE_TYPE', '', 0, 1),
(47, 4, 21, '组角色管理-数据权限', 'OBJ_GROUP_ROLE', '', 0, 1),
(48, 4, 22, '角色授权-数据权限', 'OBJ_ROLE_PERMISSION', '', 0, 1),
(49, 4, 23, '角色用户-数据权限', 'OBJ_ROLE_USER', '', 0, 1),
(50, 4, 24, '组用户管理-数据权限', 'OBJ_GROUP_USER', '', 0, 1),
(51, 4, 25, '用户授权-数据权限', 'OBJ_USER_PERMISSION', '', 0, 1),
(52, 3, 1, '项目-默认页-操作权限', 'APPLICATION_INDEX_O', '', 0, 1),
(53, 3, 2, '项目-数据分页-操作权限', 'APPLICATION_SEARCH_O', '', 0, 1),
(54, 3, 3, '数据字典-默认页-操作权限', 'DICT_INDEX_O', '', 0, 1),
(55, 3, 4, '图标-默认页-操作权限', 'BUTTON_ICON_INDEX_O', '', 0, 1),
(56, 3, 5, '菜单组-默认页-操作权限', 'MENU_GROUP_INDEX_O', '', 0, 1),
(57, 3, 6, '样式-默认页-操作权限', 'BUTTON_CLASS_INDEX_O', '', 0, 1),
(58, 3, 7, '菜单-默认页-操作权限', 'MENU_INDEX_O', '', 0, 1),
(59, 3, 8, '文件-默认页-操作权限', 'CONTROL_INDEX_O', '', 0, 1),
(60, 3, 9, '操作-默认页-操作权限', 'OPERATION_INDEX_O', '', 0, 1),
(61, 3, 10, '部门-默认页-操作权限', 'DEPARTMENT_INDEX_O', '', 0, 1),
(62, 3, 11, '用户-默认页-操作权限', 'USER_INDEX_O', '', 0, 1),
(63, 3, 12, '用户-用户回收站默认页-操作权限', 'USER_RECYCLE_O', '', 0, 1),
(64, 3, 13, '按钮-默认页-操作权限', 'BUTTON_INDEX_O', '', 0, 1),
(65, 3, 14, '按钮事件-默认页-操作权限', 'BUTTON_FUNCTION_INDEX_O', '', 0, 1),
(66, 3, 15, '系统访问日志-默认页-操作权限', 'SYSTEM_ACCESS_LOG_INDEX_O', '', 0, 1),
(67, 3, 16, '项目-添加项目-操作权限', 'APPLICATION_ADD_O', '', 0, 1),
(68, 3, 17, '岗位设置-默认页-操作权限', 'ORGANIZATION_INDEX_O', '', 0, 1),
(69, 3, 18, '工作组-默认页-操作权限', 'GROUP_INDEX_O', '', 0, 1),
(70, 3, 19, '角色管理-默认页-操作权限', 'ROLE_INDEX_O', '', 0, 1),
(71, 3, 20, '项目-编辑-操作权限', 'APPLICATION_EDIT_O', '', 0, 1),
(72, 3, 21, '项目-详情-操作权限', 'APPLICATION_SHOW_O', '', 0, 1),
(73, 3, 22, '项目-保存-操作权限', 'APPLICATION_INSERT_O', '', 0, 1),
(74, 3, 23, '项目-更新-操作权限', 'APPLICATION_UPDATE_O', '', 0, 1),
(75, 3, 24, '项目-删除-操作权限', 'APPLICATION_DELETE_O', '', 0, 1),
(76, 3, 25, '项目-上移-操作权限', 'APPLICATION_MOVEUP_O', '', 0, 1),
(77, 3, 26, '项目-下移-操作权限', 'APPLICATION_MOVEDOWN_O', '', 0, 1),
(78, 3, 27, '样式-数据分页-操作权限', 'BUTTON_CLASS_SEARCH_O', '', 0, 1),
(79, 3, 28, '样式-添加-操作权限', 'BUTTON_CLASS_ADD_O', '', 0, 1),
(80, 3, 29, '样式-编辑-操作权限', 'BUTTON_CLASS_EDIT_O', '', 0, 1),
(81, 3, 30, '样式-详情-操作权限', 'BUTTON_CLASS_SHOW_O', '', 0, 1),
(82, 3, 31, '样式-保存-操作权限', 'BUTTON_CLASS_INSERT_O', '', 0, 1),
(83, 3, 32, '样式-更新-操作权限', 'BUTTON_CLASS_UPDATE_O', '', 0, 1),
(84, 3, 33, '样式-删除-操作权限', 'BUTTON_CLASS_DELETE_O', '', 0, 1),
(85, 3, 34, '支付方式-默认页-操作权限', 'PAYMENT_INDEX_O', '', 0, 1),
(86, 3, 35, '文件-数据分页-操作权限', 'CONTROL_SEARCH_O', '', 0, 1),
(87, 3, 36, '文件-添加-操作权限', 'CONTROL_ADD_O', '', 0, 1),
(88, 3, 37, '文件-编辑-操作权限', 'CONTROL_EDIT_O', '', 0, 1),
(89, 3, 38, '文件-保存-操作权限', 'CONTROL_SAVE_O', '', 0, 1),
(90, 3, 39, '文件-更新-操作权限', 'CONTROL_UPDATE_O', '', 0, 1),
(91, 3, 40, '文件-删除-操作权限', 'CONTROL_DELETE_O', '', 0, 1),
(92, 3, 41, '图标-数据分页-操作权限', 'BUTTON_ICON_SEARCH_O', '', 0, 1),
(93, 3, 42, '图标-添加-操作权限', 'BUTTON_ICON_ADD_O', '', 0, 1),
(94, 3, 43, '图标-编辑-操作权限', 'BUTTON_ICON_EDIT_O', '', 0, 1),
(95, 3, 44, '图标-保存-操作权限', 'BUTTON_ICON_INSERT_O', '', 0, 1),
(96, 3, 45, '图标-更新-操作权限', 'BUTTON_ICON_UPDATE_O', '', 0, 1),
(97, 3, 46, '图标-删除-操作权限', 'BUTTON_ICON_DELETE_O', '', 0, 1),
(98, 3, 47, '文件-详情-操作权限', 'CONTROL_SHOW_O', '', 0, 1),
(99, 3, 48, '菜单组-数据分页-操作权限', 'MENU_GROUP_SEARCH_O', '', 0, 1),
(100, 3, 49, '菜单组-添加-操作权限', 'MENU_GROUP_ADD_O', '', 0, 1),
(101, 3, 50, '菜单组-编辑-操作权限', 'MENU_GROUP_EDIT_O', '', 0, 1),
(102, 3, 51, '菜单组-详情-操作权限', 'MENU_GROUP_SHOW_O', '', 0, 1),
(103, 3, 52, '菜单组-保存-操作权限', 'MENU_GROUP_INSERT_O', '', 0, 1),
(104, 3, 53, '菜单组-更新-操作权限', 'MENU_GROUP_UPDATE_O', '', 0, 1),
(105, 3, 54, '菜单组-删除-操作权限', 'MENU_GROUP_DELETE_O', '', 0, 1),
(106, 3, 55, '数据字典-字典列表-操作权限', 'DICT_DICT_LIST_O', '', 0, 1),
(107, 3, 56, '数据字典-字典添加-操作权限', 'DICT_ADD_O', '', 0, 1),
(108, 3, 57, '数据字典-字典编辑-操作权限', 'DICT_EDIT_O', '', 0, 1),
(109, 3, 58, '数据字典-字典删除-操作权限', 'DICT_DELETE_O', '', 0, 1),
(110, 3, 59, '数据字典-保存字典-操作权限', 'DICT_INSERT_O', '', 0, 1),
(111, 3, 60, '数据字典-更新字典-操作权限', 'DICT_UPDATE_O', '', 0, 1),
(112, 3, 61, '数据字典-字典明细-操作权限', 'DICT_SEARCH_O', '', 0, 1),
(113, 3, 62, '数据字典-添加字典明细-操作权限', 'DICT_ADD_ITEM_O', '', 0, 1),
(114, 3, 63, '数据字典-编辑字典明细-操作权限', 'DICT_EDIT_ITEM_O', '', 0, 1),
(115, 3, 64, '数据字典-删除字典明细-操作权限', 'DICT_DELETE_ITEM_O', '', 0, 1),
(116, 3, 65, '数据字典-保存字典明细-操作权限', 'DICT_INSERT_ITEM_O', '', 0, 1),
(117, 3, 66, '数据字典-更新字典明细-操作权限', 'DICT_UPDATE_ITEM_O', '', 0, 1),
(118, 3, 67, '菜单组-上移-操作权限', 'MENU_GROUP_MOVEUP_O', '', 0, 1),
(119, 3, 68, '菜单组-下移-操作权限', 'MENU_GROUP_MOVEDOWN_O', '', 0, 1),
(120, 3, 69, '菜单-数据分页-操作权限', 'MENU_SEARCH_O', '', 0, 1),
(121, 3, 70, '菜单-添加-操作权限', 'MENU_ADD_O', '', 0, 1),
(122, 3, 71, '菜单-编辑-操作权限', 'MENU_EDIT_O', '', 0, 1),
(123, 3, 72, '菜单-保存-操作权限', 'MENU_INSERT_O', '', 0, 1),
(124, 3, 73, '菜单-更新-操作权限', 'MENU_UPDATE_O', '', 0, 1),
(125, 3, 74, '菜单-删除-操作权限', 'MENU_DELETE_O', '', 0, 1),
(126, 3, 75, '菜单-上移-操作权限', 'MENU_MOVEUP_O', '', 0, 1),
(127, 3, 76, '菜单-下移-操作权限', 'MENU_MOVEDOWN_O', '', 0, 1),
(128, 3, 77, '菜单-分配按钮-操作权限', 'MENU_LAYOUT_BUTTON_O', '', 0, 1),
(129, 3, 78, '菜单-分配按钮保存-操作权限', 'MENU_SAVE_BUTTON_O', '', 0, 1),
(130, 3, 79, '菜单-按钮排序页面-操作权限', 'MENU_LIST_BUTTON_O', '', 0, 1),
(131, 3, 80, '菜单-保存排序-操作权限', 'MENU_SAVE_SORT_O', '', 0, 1),
(132, 3, 81, '操作-数据分页-操作权限', 'OPERATION_SEARCH_O', '', 0, 1),
(133, 3, 82, '操作-添加-操作权限', 'OPERATION_ADD_O', '', 0, 1),
(134, 3, 83, '操作-编辑-操作权限', 'OPERATION_EDIT_O', '', 0, 1),
(135, 3, 84, '操作-删除-操作权限', 'OPERATION_DELETE_O', '', 0, 1),
(136, 3, 85, '操作-详情-操作权限', 'OPERATION_SHOW_O', '', 0, 1),
(137, 3, 86, '操作-保存-操作权限', 'OPERATION_INSERT_O', '', 0, 1),
(138, 3, 87, '操作-更新-操作权限', 'OPERATION_UPDATE_O', '', 0, 1),
(139, 3, 88, '部门-数据列表-操作权限', 'DEPARTMENT_SEARCH_O', '', 0, 1),
(140, 3, 89, '部门-添加-操作权限', 'DEPARTMENT_ADD_O', '', 0, 1),
(141, 3, 90, '部门-编辑-操作权限', 'DEPARTMENT_EDIT_O', '', 0, 1),
(142, 3, 91, '部门-删除-操作权限', 'DEPARTMENT_DELETE_O', '', 0, 1),
(143, 3, 92, '部门-详情-操作权限', 'DEPARTMENT_SHOW_O', '', 0, 1),
(144, 3, 93, '部门-保存-操作权限', 'DEPARTMENT_INSERT_O', '', 0, 1),
(145, 3, 94, '部门-更新-操作权限', 'DEPARTMENT_UPDATE_O', '', 0, 1),
(146, 3, 95, '部门-上移-操作权限', 'DEPARTMENT_MOVEUP_O', '', 0, 1),
(147, 3, 96, '部门-下移-操作权限', 'DEPARTMENT_MOVEDOWN_O', '', 0, 1),
(148, 3, 97, '用户-数据分页-操作权限', 'USER_SEARCH_O', '', 0, 1),
(149, 3, 98, '用户-添加-操作权限', 'USER_ADD_O', '', 0, 1),
(150, 3, 99, '用户-编辑-操作权限', 'USER_EDIT_O', '', 0, 1),
(151, 3, 100, '用户-详情-操作权限', 'USER_SHOW_O', '', 0, 1),
(152, 3, 101, '用户-重置密码-操作权限', 'USER_MODIFY_O', '', 0, 1),
(153, 3, 102, '用户-保存密码-操作权限', 'USER_SET_MODIFY_O', '', 0, 1),
(154, 3, 103, '用户-保存-操作权限', 'USER_INSERT_O', '', 0, 1),
(155, 3, 104, '用户-更新-操作权限', 'USER_UPDATE_O', '', 0, 1),
(156, 3, 105, '用户-生效-操作权限', 'USER_SET_ENABLED_O', '', 0, 1),
(157, 3, 106, '用户-失效-操作权限', 'USER_SET_DISABLED_O', '', 0, 1),
(158, 3, 107, '用户-离职-操作权限', 'USER_SET_LEAVE_O', '', 0, 1),
(159, 3, 108, '用户-入职-操作权限', 'USER_SET_ON_WORK_O', '', 0, 1),
(160, 3, 109, '用户-删除-操作权限', 'USER_DELETE_O', '', 0, 1),
(161, 3, 110, '用户-恢复-操作权限', 'USER_RECOVER_O', '', 0, 1),
(162, 3, 111, '用户-用户回收站分页-操作权限', 'USER_RECYCLE_LIST_O', '', 0, 1),
(163, 3, 112, '按钮-数据分页-操作权限', 'BUTTON_SEARCH_O', '', 0, 1),
(164, 3, 113, '按钮-添加-操作权限', 'BUTTON_ADD_O', '', 0, 1),
(165, 3, 114, '按钮-编辑-操作权限', 'BUTTON_EDIT_O', '', 0, 1),
(166, 3, 115, '按钮-删除-操作权限', 'BUTTON_DELETE_O', '', 0, 1),
(167, 3, 116, '按钮-保存-操作权限', 'BUTTON_INSERT_O', '', 0, 1),
(168, 3, 117, '按钮-更新-操作权限', 'BUTTON_UPDATE_O', '', 0, 1),
(169, 3, 118, '按钮事件-数据列表-操作权限', 'BUTTON_FUNCTION_SEARCH_O', '', 0, 1),
(170, 3, 119, '按钮事件-添加-操作权限', 'BUTTON_FUNCTION_ADD_O', '', 0, 1),
(171, 3, 120, '按钮事件-编辑-操作权限', 'BUTTON_FUNCTION_EDIT_O', '', 0, 1),
(172, 3, 121, '按钮事件-删除-操作权限', 'BUTTON_FUNCTION_DELETE_O', '', 0, 1),
(173, 3, 122, '按钮事件-保存-操作权限', 'BUTTON_FUNCTION_INSERT_O', '', 0, 1),
(174, 3, 123, '按钮事件-更新-操作权限', 'BUTTON_FUNCTION_UPDATE_O', '', 0, 1),
(175, 3, 124, '系统访问日志-数据分页-操作权限', 'SYSTEM_ACCESS_LOG_SEARCH_O', '', 0, 1),
(176, 3, 125, '岗位设置-数据分页-操作权限', 'ORGANIZATION_SEARCH_O', '', 0, 1),
(177, 3, 126, '岗位设置-添加-操作权限', 'ORGANIZATION_ADD_O', '', 0, 1),
(178, 3, 127, '岗位设置-编辑-操作权限', 'ORGANIZATION_EDIT_O', '', 0, 1),
(179, 3, 128, '岗位设置-删除-操作权限', 'ORGANIZATION_DELETE_O', '', 0, 1),
(180, 3, 129, '岗位设置-保存-操作权限', 'ORGANIZATION_INSERT_O', '', 0, 1),
(181, 3, 130, '岗位设置-更新-操作权限', 'ORGANIZATION_UPDATE_O', '', 0, 1),
(182, 3, 131, '角色管理-数据列表-操作权限', 'ROLE_SEARCH_O', '', 0, 1),
(183, 3, 132, '角色管理-添加-操作权限', 'ROLE_ADD_O', '', 0, 1),
(184, 3, 133, '角色管理-编辑-操作权限', 'ROLE_EDIT_O', '', 0, 1),
(185, 3, 134, '角色管理-删除-操作权限', 'ROLE_DELETE_O', '', 0, 1),
(186, 3, 136, '角色管理-保存-操作权限', 'ROLE_INSERT_O', '', 0, 1),
(187, 3, 137, '角色管理-更新-操作权限', 'ROLE_UPDATE_O', '', 0, 1),
(188, 3, 140, '支付方式-数据分页-操作权限', 'PAYMENT_SEARCH_O', '', 0, 1),
(189, 3, 141, '支付方式-添加-操作权限', 'PAYMENT_ADD_O', '', 0, 1),
(190, 3, 142, '支付方式-编辑-操作权限', 'PAYMENT_EDIT_O', '', 0, 1),
(191, 3, 143, '支付方式-删除-操作权限', 'PAYMENT_DELETE_O', '', 0, 1),
(192, 3, 144, '支付方式-保存-操作权限', 'PAYMENT_INSERT_O', '', 0, 1),
(193, 3, 145, '支付方式-更新-操作权限', 'PAYMENT_UPDATE_O', '', 0, 1),
(194, 3, 146, '友情链接-默认页-操作权限', 'FORUM_LINKS_INDEX_O', '', 0, 1),
(195, 3, 147, '友情链接-数据分页-操作权限', 'FORUM_LINKS_SEARCH_O', '', 0, 1),
(196, 3, 148, '友情链接-添加-操作权限', 'FORUM_LINKS_ADD_O', '', 0, 1),
(197, 3, 149, '友情链接-编辑-操作权限', 'FORUM_LINKS_EDIT_O', '', 0, 1),
(198, 3, 150, '友情链接-删除-操作权限', 'FORUM_LINKS_DELETE_O', '', 0, 1),
(199, 3, 151, '友情链接-排序-操作权限', 'FORUM_LINKS_LIST_ALL_O', '', 0, 1),
(200, 3, 152, '友情链接-保存-操作权限', 'FORUM_LINKS_INSERT_O', '', 0, 1),
(201, 3, 153, '友情链接-更新-操作权限', 'FORUM_LINKS_UPDATE_O', '', 0, 1),
(202, 3, 154, '友情链接-保存排序-操作权限', 'FORUM_LINKS_SAVE_SORT_O', '', 0, 1),
(203, 3, 155, '权限管理-默认页-操作权限', 'PERMISSION_INDEX_O', '', 0, 1),
(204, 3, 156, '权限管理-数据分页-操作权限', 'PERMISSION_SEARCH_O', '', 0, 1),
(205, 3, 157, '权限管理-添加-操作权限', 'PERMISSION_ADD_O', '', 0, 1),
(206, 3, 158, '权限管理-编辑-操作权限', 'PERMISSION_EDIT_O', '', 0, 1),
(207, 3, 159, '权限管理-删除-操作权限', 'PERMISSION_DELETE_O', '', 0, 1),
(208, 3, 160, '权限管理-保存-操作权限', 'PERMISSION_INSERT_O', '', 0, 1),
(209, 3, 161, '权限管理-更新-操作权限', 'PERMISSION_UPDATE_O', '', 0, 1),
(210, 3, 164, '资源类型-默认页-操作权限', 'RESOURCE_TYPE_INDEX_O', '', 0, 1),
(211, 3, 165, '资源类型-数据分页-操作权限', 'RESOURCE_TYPE_SEARCH_O', '', 0, 1),
(212, 3, 166, '资源类型-添加-操作权限', 'RESOURCE_TYPE_ADD_O', '', 0, 1),
(213, 3, 167, '资源类型-编辑-操作权限', 'RESOURCE_TYPE_EDIT_O', '', 0, 1),
(214, 3, 168, '资源类型-删除-操作权限', 'RESOURCE_TYPE_DELETE_O', '', 0, 1),
(215, 3, 169, '资源类型-保存-操作权限', 'RESOURCE_TYPE_INSERT_O', '', 0, 1),
(216, 3, 170, '资源类型-更新-操作权限', 'RESOURCE_TYPE_UPDATE_O', '', 0, 1),
(217, 3, 171, '组角色管理-默认页-操作权限', 'GROUP_ROLE_INDEX_O', '', 0, 1),
(218, 3, 172, '组角色管理-数据列表-操作权限', 'GROUP_ROLE_SEARCH_O', '', 0, 1),
(219, 3, 173, '组角色管理-添加角色-操作权限', 'GROUP_ROLE_ADD_O', '', 0, 1),
(220, 3, 175, '组角色管理-删除角色-操作权限', 'GROUP_ROLE_DELETE_O', '', 0, 1),
(221, 3, 176, '组角色管理-保存角色-操作权限', 'GROUP_ROLE_INSERT_O', '', 0, 1),
(222, 3, 178, '角色授权-默认页-操作权限', 'ROLE_PERMISSION_INDEX_O', '', 0, 1),
(223, 3, 179, '角色授权-菜单列表-操作权限', 'ROLE_PERMISSION_LIST_MENU_O', '', 0, 1),
(224, 3, 180, '角色授权-保存菜单授权-操作权限', 'ROLE_PERMISSION_SAVE_MENU_O', '', 0, 1),
(225, 3, 181, '角色授权-按钮菜单列表-操作权限', 'ROLE_PERMISSION_LIST_BUTTON_O', '', 0, 1),
(226, 3, 182, '角色授权-保存按钮授权-操作权限', 'ROLE_PERMISSION_SAVE_BUTTON_O', '', 0, 1),
(227, 3, 183, '角色用户-默认页-操作权限', 'ROLE_USER_INDEX_O', '', 0, 1),
(228, 3, 184, '组用户管理-默认页-操作权限', 'GROUP_USER_INDEX_O', '', 0, 1),
(229, 3, 185, '用户授权-默认页-操作权限', 'USER_PERMISSION_INDEX_O', '', 0, 1),
(230, 3, 186, '组用户管理-数据分页-操作权限', 'GROUP_USER_SEARCH_O', '', 0, 1),
(231, 3, 187, '组用户管理-组用户添加-操作权限', 'GROUP_USER_ADD_O', '', 0, 1),
(232, 3, 188, '组用户管理-保存组用户-操作权限', 'GROUP_USER_INSERT_O', '', 0, 1),
(233, 3, 189, '组用户管理-删除组用户-操作权限', 'GROUP_USER_DELETE_O', '', 0, 1),
(234, 3, 190, '用户授权-菜单权限-操作权限', 'USER_PERMISSION_LIST_MENU_O', '', 0, 1),
(235, 3, 191, '用户授权-菜单授权-操作权限', 'USER_PERMISSION_SAVE_MENU_O', '', 0, 1),
(236, 3, 192, '用户授权-按钮列表菜单-操作权限', 'USER_PERMISSION_LIST_BUTTON_O', '', 0, 1),
(237, 3, 193, '用户授权-按钮授权-操作权限', 'USER_PERMISSION_BUTTON_LIST_O', '', 0, 1),
(238, 3, 194, '用户授权-保存按钮授权-操作权限', 'USER_PERMISSION_SAVE_BUTTON_O', '', 0, 1),
(239, 3, 195, '用户授权-操作菜单列表-操作权限', 'USER_PERMISSION_LIST_OPR_O', '', 0, 1),
(240, 3, 196, '用户授权-操作授权-操作权限', 'USER_PERMISSION_OPR_LIST_O', '', 0, 1),
(241, 3, 197, '用户授权-保存操作授权-操作权限', 'USER_PERMISSION_SAVE_OPR_O', '', 0, 1),
(242, 3, 198, '工作组-数据分页-操作权限', 'GROUP_SEARCH_O', '', 0, 1),
(243, 3, 199, '工作组-添加组-操作权限', 'GROUP_ADD_O', '', 0, 1),
(244, 3, 200, '工作组-编辑组-操作权限', 'GROUP_EDIT_O', '', 0, 1),
(245, 3, 201, '工作组-删除组-操作权限', 'GROUP_DELETE_O', '', 0, 1),
(246, 3, 202, '工作组-查看组-操作权限', 'GROUP_SHOW_O', '', 0, 1),
(247, 3, 203, '工作组-保存组-操作权限', 'GROUP_INSERT_O', '', 0, 1),
(248, 3, 204, '工作组-更新组-操作权限', 'GROUP_UPDATE_O', '', 0, 1),
(249, 3, 205, '工作组-上移-操作权限', 'GROUP_MOVEUP_O', '', 0, 1),
(250, 3, 206, '工作组-下移-操作权限', 'GROUP_MOVEDOWN_O', '', 0, 1),
(251, 3, 207, '支付方式-排序-操作权限', 'PAYMENT_LIST_ALL_O', '', 0, 1),
(252, 3, 208, '支付方式-保存排序-操作权限', 'PAYMENT_SAVE_SORT_O', '', 0, 1),
(253, 3, 209, '角色授权-操作权限菜单列表-操作权限', 'ROLE_PERMISSION_LIST_OPR_O', '', 0, 1),
(254, 3, 210, '角色授权-保存操作授权-操作权限', 'ROLE_PERMISSION_SAVE_OPR_O', '', 0, 1),
(255, 3, 211, '角色用户-数据分页-操作权限', 'ROLE_USER_SEARCH_O', '', 0, 1),
(256, 3, 212, '角色用户-添加用户-操作权限', 'ROLE_USER_ADD_O', '', 0, 1),
(257, 3, 213, '角色用户-保存-操作权限', 'ROLE_USER_INSERT_O', '', 0, 1),
(258, 3, 214, '角色用户-删除用户-操作权限', 'ROLE_USER_DELETE_O', '', 0, 1),
(259, 3, 215, '角色授权-按钮授权-操作权限', 'ROLE_PERMISSION_BUTTON_LIST_O', '', 0, 1),
(260, 3, 216, '角色授权-操作授权-操作权限', 'ROLE_PERMISSION_OPR_LIST_O', '', 0, 1),
(261, 2, 1, '-同步-按钮权限', 'BUTTON1', '', 0, 1),
(262, 2, 2, '-刷新-按钮权限', 'BUTTON2', '', 0, 1),
(263, 2, 3, '-关闭-按钮权限', 'BUTTON3', '', 0, 1),
(264, 2, 4, '项目-添加-按钮权限', 'BUTTON4', '', 0, 1),
(265, 2, 5, '项目-编辑-按钮权限', 'BUTTON5', '', 0, 1),
(266, 2, 6, '项目-删除-按钮权限', 'BUTTON6', '', 0, 1),
(267, 2, 7, '项目-详情-按钮权限', 'BUTTON7', '', 0, 1),
(268, 2, 8, '样式-添加-按钮权限', 'BUTTON8', '', 0, 1),
(269, 2, 9, '样式-编辑-按钮权限', 'BUTTON9', '', 0, 1),
(270, 2, 10, '样式-删除-按钮权限', 'BUTTON10', '', 0, 1),
(271, 2, 11, '文件-添加-按钮权限', 'BUTTON11', '', 0, 1),
(272, 2, 12, '文件-编辑-按钮权限', 'BUTTON12', '', 0, 1),
(273, 2, 13, '文件-删除-按钮权限', 'BUTTON13', '', 0, 1),
(274, 2, 14, '菜单组-详情-按钮权限', 'BUTTON14', '', 0, 1),
(275, 2, 15, '图标-添加-按钮权限', 'BUTTON15', '', 0, 1),
(276, 2, 16, '图标-编辑-按钮权限', 'BUTTON16', '', 0, 1),
(277, 2, 17, '图标-删除-按钮权限', 'BUTTON17', '', 0, 1),
(278, 2, 18, '按钮-添加-按钮权限', 'BUTTON18', '', 0, 1),
(279, 2, 19, '按钮-编辑-按钮权限', 'BUTTON19', '', 0, 1),
(280, 2, 20, '按钮-删除-按钮权限', 'BUTTON20', '', 0, 1),
(281, 2, 22, '菜单组-添加-按钮权限', 'BUTTON22', '', 0, 1),
(282, 2, 23, '菜单组-编辑-按钮权限', 'BUTTON23', '', 0, 1),
(283, 2, 24, '菜单组-删除-按钮权限', 'BUTTON24', '', 0, 1),
(284, 2, 25, '数据字典-添加-按钮权限', 'BUTTON25', '', 0, 1),
(285, 2, 26, '数据字典-编辑-按钮权限', 'BUTTON26', '', 0, 1),
(286, 2, 27, '数据字典-删除-按钮权限', 'BUTTON27', '', 0, 1),
(287, 2, 28, '数据字典-添加-按钮权限', 'BUTTON28', '', 0, 1),
(288, 2, 29, '数据字典-编辑-按钮权限', 'BUTTON29', '', 0, 1),
(289, 2, 30, '数据字典-删除-按钮权限', 'BUTTON30', '', 0, 1),
(290, 2, 31, '操作-添加-按钮权限', 'BUTTON31', '', 0, 1),
(291, 2, 32, '操作-编辑-按钮权限', 'BUTTON32', '', 0, 1),
(292, 2, 33, '操作-删除-按钮权限', 'BUTTON33', '', 0, 1),
(293, 2, 34, '操作-详情-按钮权限', 'BUTTON34', '', 0, 1),
(294, 2, 35, '部门-添加-按钮权限', 'BUTTON35', '', 0, 1),
(295, 2, 36, '部门-编辑-按钮权限', 'BUTTON36', '', 0, 1),
(296, 2, 37, '部门-删除-按钮权限', 'BUTTON37', '', 0, 1),
(297, 2, 38, '部门-详情-按钮权限', 'BUTTON38', '', 0, 1),
(298, 2, 39, '用户-添加-按钮权限', 'BUTTON39', '', 0, 1),
(299, 2, 40, '用户-编辑-按钮权限', 'BUTTON40', '', 0, 1),
(300, 2, 41, '用户-删除-按钮权限', 'BUTTON41', '', 0, 1),
(301, 2, 42, '用户-详情-按钮权限', 'BUTTON42', '', 0, 1),
(302, 2, 43, '用户-重置密码-按钮权限', 'BUTTON43', '', 0, 1),
(303, 2, 44, '用户-启用-按钮权限', 'BUTTON44', '', 0, 1),
(304, 2, 45, '用户-停用-按钮权限', 'BUTTON45', '', 0, 1),
(305, 2, 46, '用户-入职-按钮权限', 'BUTTON46', '', 0, 1),
(306, 2, 47, '用户-离职-按钮权限', 'BUTTON47', '', 0, 1),
(307, 2, 48, '用户-恢复-按钮权限', 'BUTTON48', '', 0, 1),
(308, 2, 49, '按钮事件-添加-按钮权限', 'BUTTON49', '', 0, 1),
(309, 2, 50, '按钮事件-编辑-按钮权限', 'BUTTON50', '', 0, 1),
(310, 2, 51, '按钮事件-删除-按钮权限', 'BUTTON51', '', 0, 1),
(311, 2, 52, '岗位设置-添加-按钮权限', 'BUTTON52', '', 0, 1),
(312, 2, 53, '岗位设置-编辑-按钮权限', 'BUTTON53', '', 0, 1),
(313, 2, 54, '岗位设置-删除-按钮权限', 'BUTTON54', '', 0, 1),
(314, 2, 55, '角色管理-添加-按钮权限', 'BUTTON55', '', 0, 1),
(315, 2, 56, '角色管理-编辑-按钮权限', 'BUTTON56', '', 0, 1),
(316, 2, 57, '角色管理-删除-按钮权限', 'BUTTON57', '', 0, 1),
(317, 2, 59, '支付方式-添加-按钮权限', 'BUTTON59', '', 0, 1),
(318, 2, 60, '支付方式-编辑-按钮权限', 'BUTTON60', '', 0, 1),
(319, 2, 61, '支付方式-删除-按钮权限', 'BUTTON61', '', 0, 1),
(320, 2, 62, '友情链接-添加-按钮权限', 'BUTTON62', '', 0, 1),
(321, 2, 63, '友情链接-编辑-按钮权限', 'BUTTON63', '', 0, 1),
(322, 2, 64, '友情链接-删除-按钮权限', 'BUTTON64', '', 0, 1),
(323, 2, 65, '支付方式-排序-按钮权限', 'BUTTON65', '', 0, 1),
(324, 2, 66, '权限管理-添加-按钮权限', 'BUTTON66', '', 0, 1),
(325, 2, 67, '权限管理-编辑-按钮权限', 'BUTTON67', '', 0, 1),
(326, 2, 68, '权限管理-删除-按钮权限', 'BUTTON68', '', 0, 1),
(327, 2, 69, '友情链接-排序-按钮权限', 'BUTTON69', '', 0, 1),
(328, 2, 70, '菜单-添加-按钮权限', 'BUTTON70', '', 0, 1),
(329, 2, 71, '菜单-编辑-按钮权限', 'BUTTON71', '', 0, 1),
(330, 2, 72, '菜单-删除-按钮权限', 'BUTTON72', '', 0, 1),
(331, 2, 73, '菜单-分配按钮-按钮权限', 'BUTTON73', '', 0, 1),
(332, 2, 74, '菜单-按钮排序-按钮权限', 'BUTTON74', '', 0, 1),
(333, 2, 75, '资源类型-添加-按钮权限', 'BUTTON75', '', 0, 1),
(334, 2, 76, '资源类型-编辑-按钮权限', 'BUTTON76', '', 0, 1),
(335, 2, 77, '资源类型-删除-按钮权限', 'BUTTON77', '', 0, 1),
(336, 2, 78, '组角色管理-添加-按钮权限', 'BUTTON78', '', 0, 1),
(337, 2, 80, '组角色管理-删除-按钮权限', 'BUTTON80', '', 0, 1),
(338, 2, 81, '文件-相关操作-按钮权限', 'BUTTON81', '', 0, 1),
(339, 2, 84, '工作组-添加-按钮权限', 'BUTTON84', '', 0, 1),
(340, 2, 85, '工作组-编辑-按钮权限', 'BUTTON85', '', 0, 1),
(341, 2, 86, '工作组-删除-按钮权限', 'BUTTON86', '', 0, 1),
(342, 2, 87, '工作组-详情-按钮权限', 'BUTTON87', '', 0, 1),
(343, 2, 88, '组用户管理-添加-按钮权限', 'BUTTON88', '', 0, 1),
(344, 2, 89, '组用户管理-删除-按钮权限', 'BUTTON89', '', 0, 1),
(345, 2, 90, '角色用户-添加-按钮权限', 'BUTTON90', '', 0, 1),
(346, 2, 91, '角色用户-删除-按钮权限', 'BUTTON91', '', 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `rel_menu_button`
--

CREATE TABLE IF NOT EXISTS `rel_menu_button` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `menu_id` int(11) NOT NULL COMMENT '菜单id',
  `button_id` int(11) NOT NULL COMMENT '按钮id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='菜单分配按钮相关表' AUTO_INCREMENT=185 ;

--
-- 转存表中的数据 `rel_menu_button`
--

INSERT INTO `rel_menu_button` (`id`, `menu_id`, `button_id`) VALUES
(1, 3, 70),
(2, 3, 71),
(3, 3, 72),
(4, 3, 73),
(5, 3, 74),
(6, 3, 1),
(7, 3, 2),
(8, 3, 3),
(9, 15, 52),
(10, 15, 53),
(11, 15, 54),
(12, 15, 1),
(13, 15, 2),
(14, 15, 3),
(15, 9, 39),
(16, 9, 40),
(17, 9, 41),
(18, 9, 42),
(19, 9, 43),
(20, 9, 44),
(21, 9, 45),
(22, 9, 46),
(23, 9, 47),
(24, 9, 1),
(25, 9, 2),
(26, 9, 3),
(27, 14, 2),
(28, 14, 3),
(29, 12, 18),
(30, 12, 19),
(31, 12, 20),
(32, 12, 21),
(33, 12, 1),
(34, 12, 2),
(35, 12, 3),
(36, 13, 49),
(37, 13, 50),
(38, 13, 51),
(39, 13, 1),
(40, 13, 2),
(41, 13, 3),
(42, 11, 42),
(43, 11, 48),
(44, 11, 1),
(45, 11, 2),
(46, 11, 3),
(51, 17, 1),
(52, 17, 2),
(53, 17, 3),
(54, 8, 35),
(55, 8, 36),
(56, 8, 37),
(57, 8, 38),
(58, 8, 1),
(59, 8, 2),
(60, 8, 3),
(61, 6, 8),
(62, 6, 9),
(63, 6, 10),
(64, 6, 1),
(65, 6, 2),
(66, 6, 3),
(67, 7, 15),
(68, 7, 16),
(69, 7, 17),
(70, 7, 1),
(71, 7, 2),
(72, 7, 3),
(73, 10, 31),
(74, 10, 32),
(75, 10, 33),
(76, 10, 34),
(77, 10, 1),
(78, 10, 2),
(79, 10, 3),
(80, 4, 22),
(81, 4, 23),
(82, 4, 24),
(83, 4, 14),
(84, 4, 1),
(85, 4, 2),
(86, 4, 3),
(87, 5, 11),
(88, 5, 12),
(89, 5, 13),
(90, 5, 81),
(91, 5, 1),
(92, 5, 2),
(93, 5, 3),
(94, 2, 4),
(95, 2, 5),
(96, 2, 6),
(97, 2, 7),
(98, 2, 1),
(99, 2, 2),
(100, 2, 3),
(101, 1, 25),
(102, 1, 26),
(103, 1, 27),
(104, 1, 28),
(105, 1, 29),
(106, 1, 30),
(107, 1, 1),
(109, 1, 3),
(113, 18, 1),
(114, 18, 2),
(115, 18, 3),
(119, 19, 65),
(120, 19, 1),
(121, 19, 2),
(122, 19, 3),
(126, 20, 69),
(127, 20, 1),
(128, 20, 2),
(129, 20, 3),
(133, 21, 1),
(134, 21, 2),
(135, 21, 3),
(139, 22, 1),
(140, 22, 2),
(141, 22, 3),
(144, 23, 1),
(145, 23, 2),
(146, 23, 3),
(150, 25, 1),
(151, 25, 2),
(152, 25, 3),
(156, 16, 1),
(157, 16, 2),
(158, 16, 3),
(159, 1, 2),
(160, 17, 87),
(161, 17, 86),
(162, 17, 85),
(163, 17, 84),
(164, 16, 89),
(165, 16, 88),
(166, 25, 91),
(167, 25, 90),
(168, 18, 55),
(169, 18, 56),
(170, 18, 57),
(171, 23, 78),
(172, 23, 80),
(173, 21, 66),
(174, 21, 67),
(175, 21, 68),
(176, 22, 75),
(177, 22, 76),
(178, 22, 77),
(179, 19, 59),
(180, 19, 60),
(181, 19, 61),
(182, 20, 62),
(183, 20, 63),
(184, 20, 64);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='资源类型表' AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `resource_type`
--

INSERT INTO `resource_type` (`id`, `label`, `code`, `main_table`, `user_table`, `fields`, `foreigh_key`, `is_system`, `is_enabled`, `is_deleted`, `note`) VALUES
(1, '菜单', 'MENU', 'menu', 'rel_user_menu', 'id', 'menu_id', 1, 1, 0, ''),
(2, '按钮', 'BUTTON', 'button', 'rel_user_button', 'id', 'button_id', 1, 1, 0, ''),
(3, '操作', 'OPERATION', 'operation', 'rel_user_operation', 'id', 'operation_id', 1, 1, 0, ''),
(4, '数据', 'DATA', 'control', 'rel_user_control', 'id', 'c_id', 1, 1, 0, '');

-- --------------------------------------------------------

--
-- 表的结构 `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `label` varchar(40) NOT NULL COMMENT '角色名称',
  `code` varchar(40) NOT NULL COMMENT '编码',
  `note` varchar(250) NOT NULL COMMENT '描述',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '删除标记',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色表' AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `role`
--

INSERT INTO `role` (`id`, `label`, `code`, `note`, `is_deleted`, `is_system`) VALUES
(1, '授权管理员', '123', '授权管理员', 0, 1),
(2, '普通用户', '124', '普通用户', 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `role_button_permission`
--

CREATE TABLE IF NOT EXISTS `role_button_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `parent_id` int(11) NOT NULL COMMENT '所属权限菜单',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色按钮权限表' AUTO_INCREMENT=75 ;

--
-- 转存表中的数据 `role_button_permission`
--

INSERT INTO `role_button_permission` (`id`, `role_id`, `parent_id`, `permission_id`) VALUES
(27, 1, 2, 261),
(28, 1, 2, 262),
(29, 1, 2, 263),
(30, 1, 2, 301),
(31, 1, 2, 302),
(32, 1, 2, 303),
(33, 1, 2, 304),
(34, 1, 6, 261),
(35, 1, 6, 262),
(36, 1, 6, 263),
(37, 1, 6, 301),
(38, 1, 17, 261),
(39, 1, 17, 262),
(40, 1, 17, 263),
(41, 1, 17, 314),
(42, 1, 17, 315),
(43, 1, 20, 261),
(44, 1, 20, 262),
(45, 1, 20, 263),
(46, 1, 22, 261),
(47, 1, 22, 262),
(48, 1, 22, 263),
(49, 1, 22, 336),
(50, 1, 22, 337),
(51, 1, 24, 261),
(52, 1, 24, 262),
(53, 1, 24, 263),
(54, 1, 24, 345),
(55, 1, 24, 346),
(56, 1, 26, 261),
(57, 1, 26, 262),
(58, 1, 26, 263),
(59, 1, 26, 343),
(60, 1, 26, 344),
(61, 2, 18, 261),
(62, 2, 18, 262),
(63, 2, 18, 263),
(67, 2, 18, 323),
(68, 2, 19, 261),
(69, 2, 19, 262),
(70, 2, 19, 263);

-- --------------------------------------------------------

--
-- 表的结构 `role_menu_permission`
--

CREATE TABLE IF NOT EXISTS `role_menu_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色权限表' AUTO_INCREMENT=32 ;

--
-- 转存表中的数据 `role_menu_permission`
--

INSERT INTO `role_menu_permission` (`id`, `role_id`, `permission_id`) VALUES
(8, 1, 2),
(12, 1, 6),
(19, 1, 17),
(20, 1, 20),
(22, 1, 22),
(23, 1, 23),
(24, 1, 24),
(25, 1, 25),
(26, 1, 26),
(28, 2, 18),
(29, 2, 19);

-- --------------------------------------------------------

--
-- 表的结构 `role_operation_permission`
--

CREATE TABLE IF NOT EXISTS `role_operation_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `parent_id` int(11) NOT NULL COMMENT '权限菜单id',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='操作权限表' AUTO_INCREMENT=58 ;

--
-- 转存表中的数据 `role_operation_permission`
--

INSERT INTO `role_operation_permission` (`id`, `role_id`, `parent_id`, `permission_id`) VALUES
(1, 1, 2, 62),
(3, 1, 2, 148),
(4, 1, 2, 151),
(5, 1, 2, 152),
(6, 1, 2, 153),
(7, 1, 2, 156),
(8, 1, 2, 157),
(10, 1, 6, 63),
(11, 1, 6, 162),
(12, 1, 17, 70),
(13, 1, 17, 182),
(14, 1, 17, 183),
(15, 1, 17, 184),
(16, 1, 17, 186),
(17, 1, 17, 187),
(18, 1, 26, 228),
(19, 1, 26, 230),
(20, 1, 26, 231),
(21, 1, 26, 232),
(22, 1, 26, 233),
(23, 1, 25, 229),
(24, 1, 25, 234),
(25, 1, 25, 235),
(26, 1, 25, 236),
(27, 1, 25, 237),
(28, 1, 25, 238),
(29, 1, 25, 239),
(30, 1, 25, 240),
(31, 1, 25, 241),
(32, 1, 24, 227),
(33, 1, 24, 255),
(34, 1, 24, 256),
(35, 1, 24, 257),
(36, 1, 24, 258),
(37, 1, 23, 222),
(38, 1, 23, 223),
(39, 1, 23, 224),
(40, 1, 23, 225),
(41, 1, 23, 226),
(42, 1, 23, 253),
(43, 1, 23, 254),
(44, 1, 23, 259),
(45, 1, 23, 260),
(46, 1, 22, 217),
(47, 1, 22, 218),
(48, 1, 22, 219),
(49, 1, 22, 220),
(50, 1, 22, 221),
(51, 1, 20, 203),
(52, 1, 20, 204),
(53, 1, 20, 209),
(54, 2, 18, 85),
(55, 2, 18, 188),
(56, 2, 19, 194),
(57, 2, 19, 195);

-- --------------------------------------------------------

--
-- 表的结构 `role_user`
--

CREATE TABLE IF NOT EXISTS `role_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `role_id` int(11) DEFAULT NULL COMMENT '角色ID',
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色用户表' AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `role_user`
--

INSERT INTO `role_user` (`id`, `role_id`, `user_id`) VALUES
(4, 1, 2),
(5, 2, 3);

-- --------------------------------------------------------

--
-- 表的结构 `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `sesskey` varchar(50) NOT NULL DEFAULT '',
  `expiry` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `adminid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `data` longtext NOT NULL,
  PRIMARY KEY (`sesskey`),
  KEY `expiry` (`expiry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `system_access_log`
--

CREATE TABLE IF NOT EXISTS `system_access_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '日志主键',
  `create_time` int(11) NOT NULL COMMENT '操作时间',
  `user_id` int(11) DEFAULT NULL COMMENT '操作员id',
  `data` varchar(120) DEFAULT NULL COMMENT '数据',
  `status` tinyint(4) DEFAULT NULL COMMENT '登录状态',
  `ip` varchar(15) NOT NULL COMMENT '操作者ip',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='系统访问日志' AUTO_INCREMENT=62 ;

--
-- 转存表中的数据 `system_access_log`
--

INSERT INTO `system_access_log` (`id`, `create_time`, `user_id`, `data`, `status`, `ip`) VALUES
(1, 1420000134, 1, '', 4, '127.0.0.1'),
(2, 1420021617, 1, '', 5, '127.0.0.1'),
(3, 1420021627, 2, '', 4, '127.0.0.1'),
(4, 1420021660, 2, '', 5, '127.0.0.1'),
(5, 1420021667, 1, '', 4, '127.0.0.1'),
(6, 1420021832, 1, '', 5, '127.0.0.1'),
(7, 1420021839, 2, '', 4, '127.0.0.1'),
(8, 1420022060, 2, '', 5, '127.0.0.1'),
(9, 1420022067, 1, '', 4, '127.0.0.1'),
(10, 1420022174, 1, '', 5, '127.0.0.1'),
(11, 1420022182, 2, '', 4, '127.0.0.1'),
(12, 1420335250, 1, '', 4, '127.0.0.1'),
(13, 1420343223, 1, '', 4, '127.0.0.1'),
(14, 1420351524, 1, '', 5, '127.0.0.1'),
(15, 1420351534, 2, '', 4, '127.0.0.1'),
(16, 1420351542, 2, '', 4, '127.0.0.1'),
(17, 1420351562, 2, '', 4, '127.0.0.1'),
(18, 1420351654, 2, '', 4, '127.0.0.1'),
(19, 1420351711, 2, '', 5, '127.0.0.1'),
(20, 1420351718, 1, '', 4, '127.0.0.1'),
(21, 1420351837, 1, '', 5, '127.0.0.1'),
(22, 1420351845, 2, '', 4, '127.0.0.1'),
(23, 1420352150, 2, '', 5, '127.0.0.1'),
(24, 1420352154, 1, '', 4, '127.0.0.1'),
(25, 1420353557, 1, '', 5, '127.0.0.1'),
(26, 1420353565, 3, '', 4, '127.0.0.1'),
(27, 1420353652, 3, '', 5, '127.0.0.1'),
(28, 1420353654, 1, '', 4, '127.0.0.1'),
(29, 1420356706, 1, '', 4, '127.0.0.1'),
(30, 1420357587, 1, '', 5, '127.0.0.1'),
(31, 1420357589, 1, '', 3, '127.0.0.1'),
(32, 1420357595, 1, '', 4, '127.0.0.1'),
(33, 1420357857, 1, '', 5, '127.0.0.1'),
(34, 1420357859, 1, '', 3, '127.0.0.1'),
(35, 1420357864, 1, '', 3, '127.0.0.1'),
(36, 1420357869, 1, '', 4, '127.0.0.1'),
(37, 1420358136, 1, '', 5, '127.0.0.1'),
(38, 1420358145, 3, '', 4, '127.0.0.1'),
(39, 1420358500, 3, '', 5, '127.0.0.1'),
(40, 1420358503, 1, '', 4, '127.0.0.1'),
(41, 1420358538, 1, '', 5, '127.0.0.1'),
(42, 1420358545, 3, '', 4, '127.0.0.1'),
(43, 1420358700, 3, '', 5, '127.0.0.1'),
(44, 1420358743, 2, '', 4, '127.0.0.1'),
(45, 1420358761, 2, '', 5, '127.0.0.1'),
(46, 1420358763, 1, '', 4, '127.0.0.1'),
(47, 1420358844, 1, '', 5, '127.0.0.1'),
(48, 1420358852, 2, '', 4, '127.0.0.1'),
(49, 1420358878, 2, '', 5, '127.0.0.1'),
(50, 1420358880, 1, '', 4, '127.0.0.1'),
(51, 1420358971, 1, '', 5, '127.0.0.1'),
(52, 1420358988, 2, '', 4, '127.0.0.1'),
(53, 1420359380, 2, '', 5, '127.0.0.1'),
(54, 1420359382, 1, '', 4, '127.0.0.1'),
(55, 1420359418, 1, '', 5, '127.0.0.1'),
(56, 1420359429, 2, '', 4, '127.0.0.1'),
(57, 1420359475, 1, '', 3, '127.0.0.1'),
(58, 1420359480, 1, '', 4, '127.0.0.1'),
(59, 1420359902, 2, '', 5, '127.0.0.1'),
(60, 1420359913, 2, '', 4, '127.0.0.1'),
(61, 1420359942, 2, '', 5, '127.0.0.1');

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `account` varchar(20) NOT NULL COMMENT '登录帐号',
  `password` varchar(50) NOT NULL COMMENT '登录密码',
  `code` varchar(10) NOT NULL COMMENT '员工编码',
  `real_name` varchar(20) NOT NULL COMMENT '姓名',
  `is_on_work` tinyint(4) NOT NULL DEFAULT '1' COMMENT '员工状态。1在职，0离职。离职后无法登录系统，但账户信息保留。',
  `is_enabled` tinyint(4) NOT NULL DEFAULT '0' COMMENT '账户状态。1启用，0停用。停用则无法登录系统。',
  `gender` tinyint(4) NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` varchar(10) NOT NULL COMMENT '生日',
  `mobile` varchar(11) NOT NULL COMMENT '手机',
  `phone` varchar(20) NOT NULL COMMENT '电话',
  `qq` varchar(20) NOT NULL COMMENT 'QQ号码',
  `email` varchar(60) NOT NULL COMMENT '邮箱',
  `address` varchar(100) NOT NULL COMMENT '住址',
  `join_date` char(10) NOT NULL COMMENT '入职时间',
  `user_type` tinyint(4) NOT NULL COMMENT '用户类型',
  `up_pwd_date` int(11) NOT NULL DEFAULT '0' COMMENT '最后修改密码日期',
  `uin` varchar(20) DEFAULT NULL COMMENT '微信账号',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除。',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `account` (`account`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户表' AUTO_INCREMENT=4 ;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `account`, `password`, `code`, `real_name`, `is_on_work`, `is_enabled`, `gender`, `birthday`, `mobile`, `phone`, `qq`, `email`, `address`, `join_date`, `user_type`, `up_pwd_date`, `uin`, `is_system`, `is_deleted`) VALUES
(1, 'admin', '6248883015fef52f23785ab26e187635', '10000', '', 1, 1, 0, '2014-11-11', '13800138000', '', '', 'yangfuyou@kela.cn', '', '2014-11-11', 1, 0, '', 1, 0),
(2, 'yangfuyou', '6248883015fef52f23785ab26e187635', '10001', '杨福友', 1, 1, 0, '', '13800138000', '', '', 'yangfuyou@kela.cn', '', '', 2, 0, NULL, 0, 0),
(3, 'demo', '6248883015fef52f23785ab26e187635', '10003', '测试', 1, 1, 0, '', '13810363671', '', '123456', 'yangfuyou@kela.cn', '', '', 3, 0, NULL, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `user_button_permission`
--

CREATE TABLE IF NOT EXISTS `user_button_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `parent_id` int(11) NOT NULL COMMENT '所属权限菜单',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户按钮权限表' AUTO_INCREMENT=83 ;

--
-- 转存表中的数据 `user_button_permission`
--

INSERT INTO `user_button_permission` (`id`, `user_id`, `parent_id`, `permission_id`) VALUES
(42, 3, 18, 261),
(43, 3, 18, 262),
(44, 3, 18, 263),
(45, 3, 19, 261),
(46, 3, 19, 262),
(47, 3, 19, 263),
(49, 2, 2, 261),
(50, 2, 2, 262),
(51, 2, 2, 263),
(52, 2, 2, 301),
(53, 2, 2, 302),
(54, 2, 2, 303),
(55, 2, 2, 304),
(56, 2, 6, 261),
(57, 2, 6, 262),
(58, 2, 6, 263),
(59, 2, 6, 301),
(60, 2, 17, 261),
(61, 2, 17, 262),
(62, 2, 17, 263),
(63, 2, 17, 314),
(64, 2, 17, 315),
(65, 2, 20, 261),
(66, 2, 20, 262),
(67, 2, 20, 263),
(68, 2, 22, 261),
(69, 2, 22, 262),
(70, 2, 22, 263),
(71, 2, 22, 336),
(72, 2, 22, 337),
(73, 2, 24, 261),
(74, 2, 24, 262),
(75, 2, 24, 263),
(76, 2, 24, 345),
(77, 2, 24, 346),
(78, 2, 26, 261),
(79, 2, 26, 262),
(80, 2, 26, 263),
(81, 2, 26, 343),
(82, 2, 26, 344);

-- --------------------------------------------------------

--
-- 表的结构 `user_menu_permission`
--

CREATE TABLE IF NOT EXISTS `user_menu_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户菜单权限表' AUTO_INCREMENT=30 ;

--
-- 转存表中的数据 `user_menu_permission`
--

INSERT INTO `user_menu_permission` (`id`, `user_id`, `permission_id`) VALUES
(1, 2, 17),
(2, 2, 20),
(3, 2, 22),
(4, 2, 23),
(5, 2, 24),
(6, 2, 25),
(7, 2, 26),
(8, 2, 2),
(9, 2, 6),
(10, 3, 18),
(11, 3, 19);

-- --------------------------------------------------------

--
-- 表的结构 `user_operation_permission`
--

CREATE TABLE IF NOT EXISTS `user_operation_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `parent_id` int(11) NOT NULL COMMENT '所属权限菜单',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户操作权限表' AUTO_INCREMENT=112 ;

--
-- 转存表中的数据 `user_operation_permission`
--

INSERT INTO `user_operation_permission` (`id`, `user_id`, `parent_id`, `permission_id`) VALUES
(56, 3, 18, 85),
(57, 3, 18, 188),
(58, 3, 19, 194),
(60, 3, 19, 195),
(61, 2, 2, 62),
(62, 2, 2, 148),
(63, 2, 2, 151),
(64, 2, 2, 152),
(65, 2, 2, 153),
(66, 2, 2, 156),
(67, 2, 2, 157),
(68, 2, 6, 63),
(69, 2, 6, 162),
(70, 2, 23, 222),
(71, 2, 23, 223),
(72, 2, 23, 224),
(73, 2, 23, 225),
(74, 2, 23, 226),
(75, 2, 23, 253),
(76, 2, 23, 254),
(77, 2, 23, 259),
(78, 2, 23, 260),
(79, 2, 17, 70),
(80, 2, 17, 182),
(81, 2, 17, 183),
(82, 2, 17, 184),
(83, 2, 17, 186),
(84, 2, 17, 187),
(85, 2, 20, 203),
(86, 2, 20, 204),
(87, 2, 20, 209),
(88, 2, 22, 217),
(89, 2, 22, 218),
(90, 2, 22, 219),
(91, 2, 22, 220),
(92, 2, 22, 221),
(93, 2, 24, 227),
(94, 2, 24, 255),
(95, 2, 24, 256),
(96, 2, 24, 257),
(97, 2, 24, 258),
(98, 2, 25, 229),
(99, 2, 25, 234),
(100, 2, 25, 235),
(101, 2, 25, 236),
(102, 2, 25, 237),
(103, 2, 25, 238),
(104, 2, 25, 239),
(105, 2, 25, 240),
(106, 2, 25, 241),
(107, 2, 26, 228),
(108, 2, 26, 230),
(109, 2, 26, 231),
(110, 2, 26, 232),
(111, 2, 26, 233);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
