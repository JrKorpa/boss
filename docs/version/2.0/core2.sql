-- phpMyAdmin SQL Dump
-- version 4.1.13
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2015-01-23 21:55:53
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `core_frame`
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='项目模块表' AUTO_INCREMENT=12 ;

--
-- 转存表中的数据 `application`
--

INSERT INTO `application` (`id`, `label`, `code`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(1, '管理中心', 'management', 31, 11, 1, 0, 1);
INSERT INTO `application` (`id`, `label`, `code`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(2, '演示', 'demo', 5, 1, 1, 0, 1);
INSERT INTO `application` (`id`, `label`, `code`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(3, '采购管理', 'purchase', 31, 9, 1, 0, 0);
INSERT INTO `application` (`id`, `label`, `code`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(4, '款式库管理', 'style', 31, 8, 1, 0, 0);
INSERT INTO `application` (`id`, `label`, `code`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(5, '会员管理', 'bespoke', 99, 7, 1, 0, 0);
INSERT INTO `application` (`id`, `label`, `code`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(6, '供应商管理', 'processor', 17, 6, 1, 0, 0);
INSERT INTO `application` (`id`, `label`, `code`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(7, '销售管理', 'sales', 10, 5, 1, 0, 0);
INSERT INTO `application` (`id`, `label`, `code`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(8, '销售政策管理', 'salepolicy', 1, 4, 1, 0, 0);
INSERT INTO `application` (`id`, `label`, `code`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(9, '仓储管理', 'warehouse', 31, 10, 1, 0, 0);
INSERT INTO `application` (`id`, `label`, `code`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(10, '裸钻管理', 'diamond', 31, 3, 1, 0, 0);
INSERT INTO `application` (`id`, `label`, `code`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(11, '财务管理', 'finance', 21, 2, 1, 0, 0);

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
  `type` tinyint(4) DEFAULT '1' COMMENT '按钮类型；1为列表页面,2为查看页面',
  `tips` varchar(20) NOT NULL COMMENT '按钮提示',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否系统内置',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `data_title` varchar(10) NOT NULL COMMENT '页签标题',
  `a_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属模块',
  `c_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属文件',
  `o_id` int(11) NOT NULL DEFAULT '0' COMMENT '请求方法',
  `display_order` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='按钮表' AUTO_INCREMENT=236 ;

--
-- 转存表中的数据 `button`
--

INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(1, '同步', 4, 2, '', 16, '', 1, '同步当前页数据', 0, 0, '', 0, 0, 0, 3);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(2, '重置', 6, 3, '', 5, '', 1, '重置搜索列表', 0, 0, '', 0, 0, 0, 2);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(3, '离开', 8, 1, '', 157, '', 3, '关闭当前页签', 0, 0, '', 0, 0, 0, 1);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(4, '刷新', 6, 4, '', 5, '', 2, '刷新查看页签', 0, 0, '', 0, 0, 0, 2);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(101, '添加', 1, 31, '', 1, 'index.php?mod=management&con=ButtonIcon&act=add', 1, '添加图标', 0, 0, '', 1, 1, 3, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(102, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=ButtonIcon&act=edit', 1, '编辑图标', 0, 0, '', 1, 1, 4, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(103, '删除', 3, 34, '', 3, 'index.php?mod=management&con=ButtonIcon&act=delete', 1, '删除图标', 0, 0, '', 1, 1, 7, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(104, '添加', 1, 31, '', 1, 'index.php?mod=management&con=ButtonClass&act=add', 1, '添加样式', 0, 0, '', 1, 2, 10, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(105, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=ButtonClass&act=edit', 1, '编辑样式', 0, 0, '', 1, 2, 11, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(106, '删除', 3, 34, '', 3, 'index.php?mod=management&con=ButtonClass&act=delete', 1, '删除样式', 0, 0, '', 1, 2, 15, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(107, '添加', 1, 31, '', 1, 'index.php?mod=management&con=ButtonFunction&act=add', 1, '添加事件', 0, 0, '', 1, 3, 18, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(108, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=ButtonFunction&act=edit', 1, '编辑事件', 0, 0, '', 1, 3, 19, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(109, '删除', 3, 34, '', 3, 'index.php?mod=management&con=ButtonFunction&act=delete', 1, '删除事件', 0, 0, '', 1, 3, 22, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(110, '添加', 1, 31, '', 1, 'index.php?mod=management&con=User&act=add', 1, '添加用户', 0, 0, '', 1, 4, 25, 29);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(111, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=User&act=edit', 1, '编辑用户', 0, 0, '', 1, 4, 26, 28);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(112, '删除', 3, 34, '', 3, 'index.php?mod=management&con=User&act=delete', 1, '删除用户', 0, 0, '', 1, 4, 35, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(113, '详情', 5, 53, '', 23, 'index.php?mod=management&con=User&act=show', 1, '查看用户详情', 0, 0, '', 1, 4, 36, 27);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(114, '重置密码', 11, 32, '', 32, 'index.php?mod=management&con=User&act=modify', 1, '重置密码', 0, 0, '', 1, 4, 27, 26);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(115, '启用', 1, 36, '', 30, 'index.php?mod=management&con=User&act=setEnabled', 1, '启用帐户', 0, 0, '', 1, 4, 31, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(116, '停用', 8, 36, '', 61, 'index.php?mod=management&con=User&act=setDisabled', 1, '停用帐户，停用后不能登录系统', 0, 0, '', 1, 4, 32, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(117, '入职', 2, 36, '', 19, 'index.php?mod=management&con=User&act=setOnWork', 1, '员工入职，设置为在职状态', 0, 0, '', 1, 4, 34, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(118, '离职', 3, 36, '', 21, 'index.php?mod=management&con=User&act=setLeave', 1, '员工离职，离职后不能登录系统', 0, 0, '', 1, 4, 33, 25);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(119, '添加', 1, 31, '', 1, 'index.php?mod=management&con=Application&act=add', 1, '添加项目', 0, 0, '', 1, 6, 41, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(120, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=Application&act=edit', 1, '编辑项目', 0, 0, '', 1, 6, 42, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(121, '删除', 3, 34, '', 3, 'index.php?mod=management&con=Application&act=delete', 1, '删除项目', 0, 0, '', 1, 6, 46, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(122, '详情', 5, 53, '', 23, 'index.php?mod=management&con=Application&act=show', 1, '查看项目详情', 0, 0, '', 1, 6, 43, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(123, '添加', 1, 91, '', 1, 'index.php?mod=management&con=MenuGroup&act=add', 1, '添加菜单组', 0, 0, '', 1, 7, 50, 25);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(124, '编辑', 2, 92, '', 2, 'index.php?mod=management&con=MenuGroup&act=edit', 1, '编辑菜单组', 0, 0, '', 1, 7, 51, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(125, '删除', 3, 93, '', 3, 'index.php?mod=management&con=MenuGroup&act=delete', 1, '删除菜单组', 0, 0, '', 1, 7, 54, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(126, '排序', 9, 95, '', 210, 'index.php?mod=management&con=MenuGroup&act=ListMenuGroup', 1, '菜单组排序', 0, 0, '', 1, 7, 55, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(127, '添加', 1, 31, '', 1, 'index.php?mod=management&con=Control&act=add', 1, '添加控制器', 0, 0, '', 1, 8, 59, 27);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(128, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=Control&act=edit', 1, '编辑控制器', 0, 0, '', 1, 8, 60, 26);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(129, '删除', 3, 34, '', 3, 'index.php?mod=management&con=Control&act=delete', 1, '删除控制器', 0, 0, '', 1, 8, 64, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(130, '详情', 5, 53, '', 23, 'index.php?mod=management&con=Control&act=show', 1, '查看控制器详情', 0, 0, '', 1, 8, 61, 25);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(131, '列表按钮排序', 9, 38, '', 210, 'index.php?mod=management&con=Control&act=listButton', 1, '列表按钮排序', 0, 0, '', 1, 8, 66, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(132, '详情页按钮排序', 10, 38, '', 211, 'index.php?mod=management&con=Control&act=listButtons', 1, '详情页按钮排序', 0, 0, '', 1, 8, 67, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(133, '关联明细查看', 8, 21, 'view_link_obj', 11, 'index.php?mod=management&con=Control&act=linkObj', 1, '查看对象的明细对象', 0, 0, '控制器', 1, 8, 65, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(134, '添加', 1, 31, '', 1, 'index.php?mod=management&con=Menu&act=add', 1, '添加菜单', 0, 0, '', 1, 9, 71, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(135, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=Menu&act=edit', 1, '编辑菜单', 0, 0, '', 1, 9, 72, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(136, '删除', 3, 34, '', 3, 'index.php?mod=management&con=Menu&act=delete', 1, '删除菜单', 0, 0, '', 1, 9, 75, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(137, '刷新', 6, 5, '', 5, 'index.php?mod=management&con=MenuGroup&act=search', 1, '刷新菜单组', 0, 0, '', 1, 7, 49, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(138, '添加', 1, 31, '', 1, 'index.php?mod=management&con=Department&act=add', 1, '添加部门', 0, 0, '', 1, 10, 78, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(139, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=Department&act=edit', 1, '编辑部门', 0, 0, '', 1, 10, 79, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(140, '删除', 3, 34, '', 3, 'index.php?mod=management&con=Department&act=delete', 1, '删除部门', 0, 0, '', 1, 10, 83, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(141, '详情', 5, 53, '', 23, 'index.php?mod=management&con=Department&act=show', 1, '查看部门详情', 0, 0, '', 1, 10, 80, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(142, '添加', 1, 21, 'organization_add', 1, 'index.php?mod=management&con=Organization&act=add', 1, '添加岗位', 0, 0, '岗位', 1, 11, 86, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(143, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=Organization&act=edit', 1, '编辑岗位', 0, 0, '', 1, 11, 87, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(144, '删除', 3, 34, '', 3, 'index.php?mod=management&con=Organization&act=delete', 1, '删除岗位', 0, 0, '', 1, 11, 90, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(145, '添加', 1, 31, '', 1, 'index.php?mod=demo&con=GuestBook&act=add', 1, '添加留言', 0, 0, '', 2, 13, 94, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(146, '编辑', 2, 32, '', 2, 'index.php?mod=demo&con=GuestBook&act=edit', 1, '编辑留言', 0, 0, '', 2, 13, 95, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(147, '删除', 3, 34, '', 3, 'index.php?mod=demo&con=GuestBook&act=delete', 1, '删除留言', 0, 0, '', 2, 13, 98, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(148, '详情', 5, 53, '', 23, 'index.php?mod=demo&con=GuestBook&act=show', 1, '查看留言详情', 0, 0, '', 2, 13, 288, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(149, '锁定', 8, 73, '', 153, 'index.php?mod=demo&con=GuestBook&act=setLocked', 2, '模拟没有页面的特殊操作', 0, 0, '', 2, 13, 99, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(150, '添加', 1, 51, '', 1, 'index.php?mod=demo&con=Message&act=add', 1, '新页签中添加留言', 0, 0, '', 2, 14, 102, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(151, '编辑', 2, 52, '', 2, 'index.php?mod=demo&con=Message&act=edit', 1, '在新页签中编辑留言', 0, 0, '', 2, 14, 103, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(152, '删除', 3, 34, '', 3, 'index.php?mod=demo&con=Message&act=delete', 1, '删除留言', 0, 0, '', 2, 14, 107, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(153, '详情', 5, 53, '', 23, 'index.php?mod=demo&con=Message&act=show', 1, '详情', 0, 0, '', 2, 14, 104, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(154, '添加', 1, 91, '', 1, 'index.php?mod=demo&con=Reply&act=add', 1, '添加留言回复', 0, 0, '', 2, 15, 109, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(155, '编辑', 2, 92, '', 2, 'index.php?mod=demo&con=Reply&act=edit', 1, '编辑留言回复', 0, 0, '', 2, 15, 110, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(156, '删除', 3, 93, '', 3, 'index.php?mod=demo&con=Reply&act=delete', 1, '删除留言回复', 0, 0, '', 2, 15, 113, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(157, '刷新', 6, 5, '', 5, 'index.php?mod=demo&con=Reply&act=search', 1, '刷新', 0, 0, '', 2, 15, 108, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(158, '添加', 1, 91, '', 1, 'index.php?mod=demo&con=MessageReply&act=add', 1, '添加信息回复', 0, 0, '', 2, 16, 115, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(159, '编辑', 2, 92, '', 2, 'index.php?mod=demo&con=MessageReply&act=edit', 1, '编辑信息回复', 0, 0, '', 2, 16, 116, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(160, '删除', 3, 93, '', 3, 'index.php?mod=demo&con=MessageReply&act=delete', 1, '删除信息回复', 0, 0, '', 2, 16, 119, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(161, '刷新', 6, 5, '', 5, 'index.php?mod=demo&con=MessageReply&act=search', 1, '刷新', 0, 0, '', 2, 16, 114, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(162, '添加', 1, 31, '', 1, 'index.php?mod=management&con=Dict&act=add', 1, '添加字典', 0, 0, '', 1, 18, 123, 25);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(163, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=Dict&act=edit', 1, '编辑字典', 0, 0, '', 1, 18, 124, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(164, '禁用', 3, 34, '', 61, 'index.php?mod=management&con=Dict&act=delete', 1, '禁用字典', 0, 0, '', 1, 18, 128, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(165, '启用', 1, 36, '', 72, 'index.php?mod=management&con=Dict&act=recover', 1, '启用字典', 0, 0, '', 1, 18, 129, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(166, '详情', 5, 53, '', 23, 'index.php?mod=management&con=Dict&act=show', 1, '查看详情', 0, 0, '', 1, 18, 125, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(167, '添加', 1, 31, '', 1, 'index.php?mod=management&con=Group&act=add', 1, '添加工作组', 0, 0, '', 1, 19, 132, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(168, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=Group&act=edit', 1, '编辑工作组', 0, 0, '', 1, 19, 133, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(169, '删除', 3, 34, '', 3, 'index.php?mod=management&con=Group&act=delete', 1, '删除工作组', 0, 0, '', 1, 19, 137, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(170, '详情', 5, 53, '', 23, 'index.php?mod=management&con=Group&act=show', 1, '工作组详情', 0, 0, '', 1, 19, 134, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(171, '添加', 1, 91, '', 1, 'index.php?mod=management&con=Operation&act=add', 1, '添加操作', 0, 0, '', 1, 20, 139, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(172, '编辑', 2, 92, '', 2, 'index.php?mod=management&con=Operation&act=edit', 1, '编辑操作', 0, 0, '', 1, 20, 140, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(173, '删除', 3, 93, '', 3, 'index.php?mod=management&con=Operation&act=delete', 1, '删除操作', 0, 0, '', 1, 20, 143, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(174, '刷新', 6, 5, '', 5, 'index.php?mod=management&con=Operation&act=search', 1, '刷新', 0, 0, '', 1, 20, 138, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(175, '添加', 1, 31, '', 1, 'index.php?mod=management&con=Button&act=add', 1, '添加按钮', 0, 0, '', 1, 21, 146, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(176, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=Button&act=edit', 1, '编辑按钮', 0, 0, '', 1, 21, 147, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(177, '删除', 3, 34, '', 3, 'index.php?mod=management&con=Button&act=delete', 1, '删除按钮', 0, 0, '', 1, 21, 150, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(178, '添加', 1, 31, '', 1, 'index.php?mod=management&con=Role&act=add', 1, '添加角色', 0, 0, '', 1, 22, 153, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(179, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=Role&act=edit', 1, '编辑角色', 0, 0, '', 1, 22, 154, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(180, '删除', 3, 34, '', 3, 'index.php?mod=management&con=Role&act=delete', 1, '删除角色', 0, 0, '', 1, 22, 157, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(181, '添加', 1, 21, 'group_user_add', 1, 'index.php?mod=management&con=GroupUser&act=add', 1, '添加组用户', 0, 0, '组用户', 1, 24, 163, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(182, '删除', 3, 34, '', 3, 'index.php?mod=management&con=GroupUser&act=delete', 1, '删除组用户', 0, 0, '', 1, 24, 165, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(183, '添加', 1, 21, 'group_role_add', 1, 'index.php?mod=management&con=GroupRole&act=add', 1, '添加组角色', 0, 0, '组角色', 1, 25, 168, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(184, '删除', 3, 34, '', 3, 'index.php?mod=management&con=GroupRole&act=delete', 1, '删除组角色', 0, 0, '', 1, 25, 170, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(185, '添加', 1, 21, 'roleuser_add', 1, 'index.php?mod=management&con=RoleUser&act=add', 1, '添加角色用户', 0, 0, '角色用户', 1, 26, 173, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(186, '删除', 3, 34, '', 3, 'index.php?mod=management&con=RoleUser&act=delete', 1, '删除角色用户', 0, 0, '', 1, 26, 175, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(187, '添加', 1, 31, '', 1, 'index.php?mod=management&con=ConfItem&act=add', 1, '添加配置', 0, 0, '', 1, 29, 180, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(188, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=ConfItem&act=edit', 1, '编辑配置', 0, 0, '', 1, 29, 181, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(189, '删除', 3, 34, '', 3, 'index.php?mod=management&con=ConfItem&act=delete', 1, '删除配置', 0, 0, '', 1, 29, 184, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(190, '下载', 10, 21, 'getdoc', 14, 'index.php?mod=management&con=ConfItem&act=getDoc', 1, '下载数据库配置文件', 0, 0, '数据库配置', 1, 29, 185, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(191, '恢复', 1, 36, '', 304, 'index.php?mod=management&con=UserRecycle&act=recover', 1, '恢复用户', 0, 0, '', 1, 30, 188, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(192, '添加', 1, 31, '', 1, 'index.php?mod=management&con=Region&act=add', 1, '添加省市区', 0, 0, '', 1, 31, 191, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(193, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=Region&act=edit', 1, '编辑省市区', 0, 0, '', 1, 31, 192, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(194, '删除', 3, 34, '', 3, 'index.php?mod=management&con=Region&act=delete', 1, '删除省市区', 0, 0, '', 1, 31, 195, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(195, '恢复', 1, 36, '', 304, 'index.php?mod=management&con=ApplicationRecycle&act=recover', 1, '恢复项目', 0, 0, '', 1, 32, 198, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(196, '恢复', 1, 36, '', 304, 'index.php?mod=management&con=ButtonFunctionRecycle&act=recover', 1, '恢复事件', 0, 0, '', 1, 33, 201, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(197, '恢复', 1, 36, '', 304, 'index.php?mod=management&con=ButtonRecycle&act=recover', 1, '恢复按钮', 0, 0, '', 1, 34, 204, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(198, '恢复', 1, 36, '', 304, 'index.php?mod=management&con=CompanyRecycle&act=recover', 1, '恢复分公司', 0, 0, '', 1, 35, 207, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(199, '恢复', 1, 36, '', 304, 'index.php?mod=management&con=ControlRecycle&act=recover', 1, '恢复控制器', 0, 0, '', 1, 36, 210, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(200, '恢复', 1, 94, '', 5, 'index.php?mod=management&con=OperationRecycle&act=recover', 1, '恢复操作', 0, 0, '', 1, 37, 212, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(201, '刷新', 6, 5, '', 5, 'index.php?mod=management&con=OperationRecycle&act=search', 1, '刷新已删操作列表', 0, 0, '', 1, 37, 211, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(202, '添加', 1, 91, '', 1, 'index.php?mod=management&con=DictItem&act=add', 1, '添加字典明细', 0, 0, '', 1, 38, 214, 25);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(203, '编辑', 2, 92, '', 2, 'index.php?mod=management&con=DictItem&act=edit', 1, '编辑字典明细', 0, 0, '', 1, 38, 215, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(204, '禁用', 3, 93, '', 304, 'index.php?mod=management&con=DictItem&act=delete', 1, '禁用字典明细', 0, 0, '', 1, 38, 218, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(205, '启用', 1, 94, '', 304, 'index.php?mod=management&con=DictItem&act=recover', 1, '启用数据字典明细', 0, 0, '', 1, 38, 219, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(206, '刷新', 6, 5, '', 5, 'index.php?mod=management&con=DictItem&act=search', 1, '刷新字典明细', 0, 0, '', 1, 38, 213, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(207, '添加', 1, 31, '', 1, 'index.php?mod=management&con=Payment&act=add', 1, '添加支付方式', 0, 0, '', 1, 101, 222, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(208, '编辑', 2, 31, '', 2, 'index.php?mod=management&con=Payment&act=edit', 1, '编辑支付方式', 0, 0, '', 1, 101, 223, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(209, '删除', 3, 34, '', 3, 'index.php?mod=management&con=Payment&act=delete', 1, '删除支付方式', 0, 0, '', 1, 101, 226, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(210, '排序', 9, 35, '', 210, 'index.php?mod=management&con=Payment&act=listAll', 1, '支付方式排序', 0, 0, '', 1, 101, 227, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(211, '添加', 1, 31, '', 1, 'index.php?mod=management&con=ForumLinks&act=add', 1, '添加友情链接', 0, 0, '', 1, 102, 231, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(212, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=ForumLinks&act=edit', 1, '编辑友情链接', 0, 0, '', 1, 102, 232, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(213, '删除', 3, 34, '', 3, 'index.php?mod=management&con=ForumLinks&act=delete', 1, '删除友情链接', 0, 0, '', 1, 102, 235, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(214, '排序', 9, 35, '', 210, 'index.php?mod=management&con=ForumLinks&act=listAll', 1, '排序友情链接', 0, 0, '', 1, 102, 236, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(215, '添加', 1, 31, '', 1, 'index.php?mod=management&con=Express&act=add', 1, '添加快递公司', 0, 0, '', 1, 103, 240, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(216, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=Express&act=edit', 1, '编辑快递公司', 0, 0, '', 1, 103, 241, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(217, '删除', 3, 34, '', 3, 'index.php?mod=management&con=Express&act=delete', 1, '删除快递公司', 0, 0, '', 1, 103, 244, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(218, '添加', 1, 31, '', 1, 'index.php?mod=management&con=Company&act=add', 1, '添加分公司', 0, 0, '', 1, 104, 247, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(219, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=Company&act=edit', 1, '编辑分公司', 0, 0, '', 1, 104, 248, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(220, '删除', 3, 34, '', 3, 'index.php?mod=management&con=Company&act=delete', 1, '删除分公司', 0, 0, '', 1, 104, 252, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(221, '添加', 1, 21, 'department_compay_add', 1, 'index.php?mod=management&con=CompanyDepartment&act=add', 1, '添加部门', 0, 0, '分公司归属', 1, 105, 255, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(222, '删除', 3, 34, '', 3, 'index.php?mod=management&con=CompanyDepartment&act=delete', 1, '删除部门', 0, 0, '', 1, 105, 257, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(223, '添加', 1, 21, 'company_department_add', 1, 'index.php?mod=management&con=DepartmentCompany&act=add', 1, '添加公司', 0, 0, '分公司', 1, 107, 267, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(224, '删除', 3, 34, '', 3, 'index.php?mod=management&con=DepartmentCompany&act=delete', 1, '删除分公司', 0, 0, '', 1, 107, 269, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(225, '添加', 1, 31, '', 1, 'index.php?mod=management&con=LargeArea&act=add', 1, '添加大区', 0, 0, '', 1, 106, 260, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(226, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=LargeArea&act=edit', 1, '编辑大区', 0, 0, '', 1, 106, 261, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(227, '删除', 3, 34, '', 3, 'index.php?mod=management&con=LargeArea&act=delete', 1, '删除大区', 0, 0, '', 1, 106, 264, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(228, '添加', 1, 31, '', 1, 'index.php?mod=management&con=ShopCfg&act=add', 1, '添加体验店', 0, 0, '', 1, 108, 272, 24);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(229, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=ShopCfg&act=edit', 1, '编辑体验店', 0, 0, '', 1, 108, 273, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(230, '删除', 3, 34, '', 3, 'index.php?mod=management&con=ShopCfg&act=delete', 1, '删除体验店', 0, 0, '', 1, 108, 277, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(231, '详情', 5, 53, '', 23, 'index.php?mod=management&con=ShopCfg&act=show', 1, '查看体验店信息', 0, 0, '', 1, 108, 274, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(232, '添加', 1, 31, '', 1, 'index.php?mod=management&con=SalesChannels&act=add', 1, '添加销售渠道', 0, 0, '', 1, 109, 280, 23);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(233, '编辑', 2, 32, '', 2, 'index.php?mod=management&con=SalesChannels&act=edit', 1, '编辑销售渠道', 0, 0, '', 1, 109, 281, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(234, '删除', 3, 34, '', 3, 'index.php?mod=management&con=SalesChannels&act=delete', 1, '删除销售渠道', 0, 0, '', 1, 109, 284, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(235, '恢复', 1, 36, '', 304, 'index.php?mod=management&con=ShopCfgRecycle&act=recover', 1, '恢复体验店', 0, 0, '', 1, 110, 287, 21);

-- --------------------------------------------------------

--
-- 表的结构 `button_class`
--

CREATE TABLE IF NOT EXISTS `button_class` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '样式id',
  `classname` varchar(20) NOT NULL COMMENT '样式名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='按钮样式表' AUTO_INCREMENT=12 ;

--
-- 转存表中的数据 `button_class`
--

INSERT INTO `button_class` (`id`, `classname`) VALUES(1, 'green');
INSERT INTO `button_class` (`id`, `classname`) VALUES(2, 'blue');
INSERT INTO `button_class` (`id`, `classname`) VALUES(3, 'red');
INSERT INTO `button_class` (`id`, `classname`) VALUES(4, 'grey');
INSERT INTO `button_class` (`id`, `classname`) VALUES(5, 'btn-info');
INSERT INTO `button_class` (`id`, `classname`) VALUES(6, 'dark');
INSERT INTO `button_class` (`id`, `classname`) VALUES(7, 'default');
INSERT INTO `button_class` (`id`, `classname`) VALUES(8, 'yellow');
INSERT INTO `button_class` (`id`, `classname`) VALUES(9, 'purple');
INSERT INTO `button_class` (`id`, `classname`) VALUES(10, 'btn-primary');
INSERT INTO `button_class` (`id`, `classname`) VALUES(11, 'btn-success');

-- --------------------------------------------------------

--
-- 表的结构 `button_function`
--

CREATE TABLE IF NOT EXISTS `button_function` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'js方法id',
  `name` varchar(30) NOT NULL COMMENT 'js组件名',
  `label` varchar(10) NOT NULL COMMENT '中文显示',
  `tips` varchar(200) NOT NULL COMMENT '使用提示',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '事件类型：1为列表页，2为查看页，3为列表和查看通用',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='按钮事件表' AUTO_INCREMENT=96 ;

--
-- 转存表中的数据 `button_function`
--

INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(1, 'closeTab', '离开', '关闭当前页签', 1, 0, 3);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(2, 'sync', '刷新列表', '保留搜索条件，刷新当前页，同步数据变更。', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(3, 'reload', '重置列表', '清除搜索条件，回到首页。', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(4, 'retrieveReload', '刷新当前页', '刷新查看页。', 1, 0, 2);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(5, 'relReload', '刷新明细', '刷新明细分页，重置到首页', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(21, 'cust', '自定义按钮操作', '自定义函数实现特殊的功能', 1, 0, 3);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(31, 'add', '弹框添加', '', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(32, 'edit', '弹框编辑', '', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(33, 'retrieve', '弹框查看', '', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(34, 'delete', '弹框删除', '', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(35, 'sort', '弹框排序', '', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(36, 'confirm', '列表特殊处理', '', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(37, 'relList', '相关列表', '选中行，跳转相关菜单列表', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(38, 'pop2', '弹框', '选中行，弹框', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(51, 'addNew', '新页签添加', '', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(52, 'editNew', '新页签编辑', '', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(53, 'view', '新页签查看', '', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(71, 'retrieveEdit', '查看页编辑', '', 1, 0, 2);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(72, 'retrieveDelete', '查看页删除', '', 1, 0, 2);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(73, 'retrieveConfirm', '查看页特殊处理', '', 1, 0, 2);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(91, 'addRel', '弹窗添加明细', '', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(92, 'editRel', '弹窗编辑明细', '', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(93, 'deleteRel', '弹窗删除明细', '', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(94, 'relConfirm', '明细特殊处理', '', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(95, 'relSort', '明细排序', '', 1, 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `button_icon`
--

CREATE TABLE IF NOT EXISTS `button_icon` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '图标id',
  `name` varchar(40) NOT NULL COMMENT '图标名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='按钮图标表' AUTO_INCREMENT=410 ;

--
-- 转存表中的数据 `button_icon`
--

INSERT INTO `button_icon` (`id`, `name`) VALUES(1, 'fa-plus');
INSERT INTO `button_icon` (`id`, `name`) VALUES(2, 'fa-edit');
INSERT INTO `button_icon` (`id`, `name`) VALUES(3, 'fa-trash-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(4, 'fa-random');
INSERT INTO `button_icon` (`id`, `name`) VALUES(5, 'fa-refresh');
INSERT INTO `button_icon` (`id`, `name`) VALUES(6, 'fa-angle-double-left');
INSERT INTO `button_icon` (`id`, `name`) VALUES(7, 'fa-search');
INSERT INTO `button_icon` (`id`, `name`) VALUES(8, 'fa-arrow-circle-o-right');
INSERT INTO `button_icon` (`id`, `name`) VALUES(9, 'fa-arrow-circle-o-left');
INSERT INTO `button_icon` (`id`, `name`) VALUES(10, 'fa-asterisk');
INSERT INTO `button_icon` (`id`, `name`) VALUES(11, 'fa-bar-chart-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(12, 'fa-cloud-download');
INSERT INTO `button_icon` (`id`, `name`) VALUES(13, 'fa-cloud-upload');
INSERT INTO `button_icon` (`id`, `name`) VALUES(14, 'fa-download');
INSERT INTO `button_icon` (`id`, `name`) VALUES(15, 'fa-female');
INSERT INTO `button_icon` (`id`, `name`) VALUES(16, 'fa-exchange');
INSERT INTO `button_icon` (`id`, `name`) VALUES(17, 'fa-eye');
INSERT INTO `button_icon` (`id`, `name`) VALUES(18, 'fa-gear');
INSERT INTO `button_icon` (`id`, `name`) VALUES(19, 'fa-gears');
INSERT INTO `button_icon` (`id`, `name`) VALUES(20, 'fa-glass');
INSERT INTO `button_icon` (`id`, `name`) VALUES(21, 'fa-group');
INSERT INTO `button_icon` (`id`, `name`) VALUES(22, 'fa-heart');
INSERT INTO `button_icon` (`id`, `name`) VALUES(23, 'fa-info');
INSERT INTO `button_icon` (`id`, `name`) VALUES(24, 'fa-info-circle');
INSERT INTO `button_icon` (`id`, `name`) VALUES(25, 'fa-key');
INSERT INTO `button_icon` (`id`, `name`) VALUES(26, 'fa-male');
INSERT INTO `button_icon` (`id`, `name`) VALUES(27, 'fa-money');
INSERT INTO `button_icon` (`id`, `name`) VALUES(28, 'fa-tag');
INSERT INTO `button_icon` (`id`, `name`) VALUES(29, 'fa-tags');
INSERT INTO `button_icon` (`id`, `name`) VALUES(30, 'fa-unlock');
INSERT INTO `button_icon` (`id`, `name`) VALUES(31, 'fa-user');
INSERT INTO `button_icon` (`id`, `name`) VALUES(32, 'fa-wrench');
INSERT INTO `button_icon` (`id`, `name`) VALUES(33, 'fa-chain');
INSERT INTO `button_icon` (`id`, `name`) VALUES(34, 'fa-angle-double-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(35, 'fa-save');
INSERT INTO `button_icon` (`id`, `name`) VALUES(36, 'fa-undo');
INSERT INTO `button_icon` (`id`, `name`) VALUES(37, 'fa-angle-double-right');
INSERT INTO `button_icon` (`id`, `name`) VALUES(38, 'fa-arrow-left');
INSERT INTO `button_icon` (`id`, `name`) VALUES(39, 'fa-arrow-right');
INSERT INTO `button_icon` (`id`, `name`) VALUES(40, 'fa-arrow-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(41, 'fa-arrow-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(42, 'fa-rub');
INSERT INTO `button_icon` (`id`, `name`) VALUES(43, 'fa-ruble');
INSERT INTO `button_icon` (`id`, `name`) VALUES(44, 'fa-rouble');
INSERT INTO `button_icon` (`id`, `name`) VALUES(45, 'fa-pagelines');
INSERT INTO `button_icon` (`id`, `name`) VALUES(46, 'fa-stack-exchange');
INSERT INTO `button_icon` (`id`, `name`) VALUES(47, 'fa-caret-square-o-left');
INSERT INTO `button_icon` (`id`, `name`) VALUES(48, 'fa-toggle-left');
INSERT INTO `button_icon` (`id`, `name`) VALUES(49, 'fa-dot-circle-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(50, 'fa-wheelchair');
INSERT INTO `button_icon` (`id`, `name`) VALUES(51, 'fa-vimeo-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(52, 'fa-try');
INSERT INTO `button_icon` (`id`, `name`) VALUES(53, 'fa-turkish-lira');
INSERT INTO `button_icon` (`id`, `name`) VALUES(54, 'fa-plus-square-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(55, 'fa-adjust');
INSERT INTO `button_icon` (`id`, `name`) VALUES(56, 'fa-anchor');
INSERT INTO `button_icon` (`id`, `name`) VALUES(57, 'fa-archive');
INSERT INTO `button_icon` (`id`, `name`) VALUES(58, 'fa-arrows');
INSERT INTO `button_icon` (`id`, `name`) VALUES(59, 'fa-arrows-h');
INSERT INTO `button_icon` (`id`, `name`) VALUES(60, 'fa-arrows-v');
INSERT INTO `button_icon` (`id`, `name`) VALUES(61, 'fa-ban');
INSERT INTO `button_icon` (`id`, `name`) VALUES(62, 'fa-barcode');
INSERT INTO `button_icon` (`id`, `name`) VALUES(63, 'fa-bars');
INSERT INTO `button_icon` (`id`, `name`) VALUES(64, 'fa-beer');
INSERT INTO `button_icon` (`id`, `name`) VALUES(65, 'fa-bell');
INSERT INTO `button_icon` (`id`, `name`) VALUES(66, 'fa-bell-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(67, 'fa-bolt');
INSERT INTO `button_icon` (`id`, `name`) VALUES(68, 'fa-book');
INSERT INTO `button_icon` (`id`, `name`) VALUES(69, 'fa-bookmark');
INSERT INTO `button_icon` (`id`, `name`) VALUES(70, 'fa-bookmark-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(71, 'fa-briefcase');
INSERT INTO `button_icon` (`id`, `name`) VALUES(72, 'fa-bug');
INSERT INTO `button_icon` (`id`, `name`) VALUES(73, 'fa-building-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(74, 'fa-bullhorn');
INSERT INTO `button_icon` (`id`, `name`) VALUES(75, 'fa-bullseye');
INSERT INTO `button_icon` (`id`, `name`) VALUES(76, 'fa-calendar');
INSERT INTO `button_icon` (`id`, `name`) VALUES(77, 'fa-calendar-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(78, 'fa-camera');
INSERT INTO `button_icon` (`id`, `name`) VALUES(79, 'fa-camera-retro');
INSERT INTO `button_icon` (`id`, `name`) VALUES(80, 'fa-caret-square-o-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(81, 'fa-caret-square-o-right');
INSERT INTO `button_icon` (`id`, `name`) VALUES(82, 'fa-caret-square-o-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(83, 'fa-certificate');
INSERT INTO `button_icon` (`id`, `name`) VALUES(84, 'fa-check');
INSERT INTO `button_icon` (`id`, `name`) VALUES(85, 'fa-check-circle');
INSERT INTO `button_icon` (`id`, `name`) VALUES(86, 'fa-check-circle-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(87, 'fa-check-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(88, 'fa-check-square-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(89, 'fa-circle');
INSERT INTO `button_icon` (`id`, `name`) VALUES(90, 'fa-circle-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(91, 'fa-clock-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(92, 'fa-cloud');
INSERT INTO `button_icon` (`id`, `name`) VALUES(93, 'fa-code');
INSERT INTO `button_icon` (`id`, `name`) VALUES(94, 'fa-code-fork');
INSERT INTO `button_icon` (`id`, `name`) VALUES(95, 'fa-coffee');
INSERT INTO `button_icon` (`id`, `name`) VALUES(96, 'fa-cog');
INSERT INTO `button_icon` (`id`, `name`) VALUES(97, 'fa-cogs');
INSERT INTO `button_icon` (`id`, `name`) VALUES(98, 'fa-comment');
INSERT INTO `button_icon` (`id`, `name`) VALUES(99, 'fa-comment-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(100, 'fa-comments');
INSERT INTO `button_icon` (`id`, `name`) VALUES(101, 'fa-comments-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(102, 'fa-compass');
INSERT INTO `button_icon` (`id`, `name`) VALUES(103, 'fa-credit-card');
INSERT INTO `button_icon` (`id`, `name`) VALUES(104, 'fa-crop');
INSERT INTO `button_icon` (`id`, `name`) VALUES(105, 'fa-crosshairs');
INSERT INTO `button_icon` (`id`, `name`) VALUES(106, 'fa-cutlery');
INSERT INTO `button_icon` (`id`, `name`) VALUES(107, 'fa-dashboard');
INSERT INTO `button_icon` (`id`, `name`) VALUES(108, 'fa-desktop');
INSERT INTO `button_icon` (`id`, `name`) VALUES(109, 'fa-ellipsis-h');
INSERT INTO `button_icon` (`id`, `name`) VALUES(110, 'fa-ellipsis-v');
INSERT INTO `button_icon` (`id`, `name`) VALUES(111, 'fa-envelope');
INSERT INTO `button_icon` (`id`, `name`) VALUES(112, 'fa-envelope-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(113, 'fa-eraser');
INSERT INTO `button_icon` (`id`, `name`) VALUES(114, 'fa-exclamation');
INSERT INTO `button_icon` (`id`, `name`) VALUES(115, 'fa-exclamation-circle');
INSERT INTO `button_icon` (`id`, `name`) VALUES(116, 'fa-exclamation-triangle');
INSERT INTO `button_icon` (`id`, `name`) VALUES(117, 'fa-external-link');
INSERT INTO `button_icon` (`id`, `name`) VALUES(118, 'fa-external-link-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(119, 'fa-eye-slash');
INSERT INTO `button_icon` (`id`, `name`) VALUES(120, 'fa-fighter-jet');
INSERT INTO `button_icon` (`id`, `name`) VALUES(121, 'fa-film');
INSERT INTO `button_icon` (`id`, `name`) VALUES(122, 'fa-filter');
INSERT INTO `button_icon` (`id`, `name`) VALUES(123, 'fa-fire');
INSERT INTO `button_icon` (`id`, `name`) VALUES(124, 'fa-fire-extinguisher');
INSERT INTO `button_icon` (`id`, `name`) VALUES(125, 'fa-flag');
INSERT INTO `button_icon` (`id`, `name`) VALUES(126, 'fa-flag-checkered');
INSERT INTO `button_icon` (`id`, `name`) VALUES(127, 'fa-flag-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(128, 'fa-flash');
INSERT INTO `button_icon` (`id`, `name`) VALUES(129, 'fa-flask');
INSERT INTO `button_icon` (`id`, `name`) VALUES(130, 'fa-folder');
INSERT INTO `button_icon` (`id`, `name`) VALUES(131, 'fa-folder-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(132, 'fa-folder-open');
INSERT INTO `button_icon` (`id`, `name`) VALUES(133, 'fa-folder-open-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(134, 'fa-frown-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(135, 'fa-gamepad');
INSERT INTO `button_icon` (`id`, `name`) VALUES(136, 'fa-gavel');
INSERT INTO `button_icon` (`id`, `name`) VALUES(137, 'fa-gift');
INSERT INTO `button_icon` (`id`, `name`) VALUES(138, 'fa-globe');
INSERT INTO `button_icon` (`id`, `name`) VALUES(139, 'fa-hdd-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(140, 'fa-headphones');
INSERT INTO `button_icon` (`id`, `name`) VALUES(141, 'fa-heart-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(142, 'fa-home');
INSERT INTO `button_icon` (`id`, `name`) VALUES(143, 'fa-inbox');
INSERT INTO `button_icon` (`id`, `name`) VALUES(144, 'fa-keyboard-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(145, 'fa-laptop');
INSERT INTO `button_icon` (`id`, `name`) VALUES(146, 'fa-leaf');
INSERT INTO `button_icon` (`id`, `name`) VALUES(147, 'fa-legal');
INSERT INTO `button_icon` (`id`, `name`) VALUES(148, 'fa-lemon-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(149, 'fa-level-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(150, 'fa-level-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(151, 'fa-lightbulb-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(152, 'fa-location-arrow');
INSERT INTO `button_icon` (`id`, `name`) VALUES(153, 'fa-lock');
INSERT INTO `button_icon` (`id`, `name`) VALUES(154, 'fa-magic');
INSERT INTO `button_icon` (`id`, `name`) VALUES(155, 'fa-magnet');
INSERT INTO `button_icon` (`id`, `name`) VALUES(156, 'fa-mail-forward');
INSERT INTO `button_icon` (`id`, `name`) VALUES(157, 'fa-mail-reply');
INSERT INTO `button_icon` (`id`, `name`) VALUES(158, 'fa-mail-reply-all');
INSERT INTO `button_icon` (`id`, `name`) VALUES(159, 'fa-map-marker');
INSERT INTO `button_icon` (`id`, `name`) VALUES(160, 'fa-meh-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(161, 'fa-microphone');
INSERT INTO `button_icon` (`id`, `name`) VALUES(162, 'fa-microphone-slash');
INSERT INTO `button_icon` (`id`, `name`) VALUES(163, 'fa-minus');
INSERT INTO `button_icon` (`id`, `name`) VALUES(164, 'fa-minus-circle');
INSERT INTO `button_icon` (`id`, `name`) VALUES(165, 'fa-minus-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(166, 'fa-minus-square-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(167, 'fa-mobile');
INSERT INTO `button_icon` (`id`, `name`) VALUES(168, 'fa-mobile-phone');
INSERT INTO `button_icon` (`id`, `name`) VALUES(169, 'fa-moon-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(170, 'fa-music');
INSERT INTO `button_icon` (`id`, `name`) VALUES(171, 'fa-pencil');
INSERT INTO `button_icon` (`id`, `name`) VALUES(172, 'fa-pencil-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(173, 'fa-pencil-square-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(174, 'fa-phone');
INSERT INTO `button_icon` (`id`, `name`) VALUES(175, 'fa-phone-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(176, 'fa-picture-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(177, 'fa-plane');
INSERT INTO `button_icon` (`id`, `name`) VALUES(178, 'fa-plus-circle');
INSERT INTO `button_icon` (`id`, `name`) VALUES(179, 'fa-plus-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(180, 'fa-power-off');
INSERT INTO `button_icon` (`id`, `name`) VALUES(181, 'fa-print');
INSERT INTO `button_icon` (`id`, `name`) VALUES(182, 'fa-puzzle-piece');
INSERT INTO `button_icon` (`id`, `name`) VALUES(183, 'fa-qrcode');
INSERT INTO `button_icon` (`id`, `name`) VALUES(184, 'fa-question');
INSERT INTO `button_icon` (`id`, `name`) VALUES(185, 'fa-question-circle');
INSERT INTO `button_icon` (`id`, `name`) VALUES(186, 'fa-quote-left');
INSERT INTO `button_icon` (`id`, `name`) VALUES(187, 'fa-quote-right');
INSERT INTO `button_icon` (`id`, `name`) VALUES(188, 'fa-reply');
INSERT INTO `button_icon` (`id`, `name`) VALUES(189, 'fa-reply-all');
INSERT INTO `button_icon` (`id`, `name`) VALUES(190, 'fa-retweet');
INSERT INTO `button_icon` (`id`, `name`) VALUES(191, 'fa-road');
INSERT INTO `button_icon` (`id`, `name`) VALUES(192, 'fa-rocket');
INSERT INTO `button_icon` (`id`, `name`) VALUES(193, 'fa-rss');
INSERT INTO `button_icon` (`id`, `name`) VALUES(194, 'fa-rss-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(195, 'fa-search-minus');
INSERT INTO `button_icon` (`id`, `name`) VALUES(196, 'fa-search-plus');
INSERT INTO `button_icon` (`id`, `name`) VALUES(197, 'fa-share');
INSERT INTO `button_icon` (`id`, `name`) VALUES(198, 'fa-share-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(199, 'fa-share-square-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(200, 'fa-shield');
INSERT INTO `button_icon` (`id`, `name`) VALUES(201, 'fa-shopping-cart');
INSERT INTO `button_icon` (`id`, `name`) VALUES(202, 'fa-sign-in');
INSERT INTO `button_icon` (`id`, `name`) VALUES(203, 'fa-sign-out');
INSERT INTO `button_icon` (`id`, `name`) VALUES(204, 'fa-signal');
INSERT INTO `button_icon` (`id`, `name`) VALUES(205, 'fa-sitemap');
INSERT INTO `button_icon` (`id`, `name`) VALUES(206, 'fa-smile-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(207, 'fa-sort');
INSERT INTO `button_icon` (`id`, `name`) VALUES(208, 'fa-sort-alpha-asc');
INSERT INTO `button_icon` (`id`, `name`) VALUES(209, 'fa-sort-alpha-desc');
INSERT INTO `button_icon` (`id`, `name`) VALUES(210, 'fa-sort-amount-asc');
INSERT INTO `button_icon` (`id`, `name`) VALUES(211, 'fa-sort-amount-desc');
INSERT INTO `button_icon` (`id`, `name`) VALUES(212, 'fa-sort-asc');
INSERT INTO `button_icon` (`id`, `name`) VALUES(213, 'fa-sort-desc');
INSERT INTO `button_icon` (`id`, `name`) VALUES(214, 'fa-sort-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(215, 'fa-sort-numeric-asc');
INSERT INTO `button_icon` (`id`, `name`) VALUES(216, 'fa-sort-numeric-desc');
INSERT INTO `button_icon` (`id`, `name`) VALUES(217, 'fa-sort-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(218, 'fa-spinner');
INSERT INTO `button_icon` (`id`, `name`) VALUES(219, 'fa-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(220, 'fa-square-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(221, 'fa-star');
INSERT INTO `button_icon` (`id`, `name`) VALUES(222, 'fa-star-half');
INSERT INTO `button_icon` (`id`, `name`) VALUES(223, 'fa-star-half-empty');
INSERT INTO `button_icon` (`id`, `name`) VALUES(224, 'fa-star-half-full');
INSERT INTO `button_icon` (`id`, `name`) VALUES(225, 'fa-star-half-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(226, 'fa-star-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(227, 'fa-subscript');
INSERT INTO `button_icon` (`id`, `name`) VALUES(228, 'fa-suitcase');
INSERT INTO `button_icon` (`id`, `name`) VALUES(229, 'fa-sun-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(230, 'fa-superscript');
INSERT INTO `button_icon` (`id`, `name`) VALUES(231, 'fa-tablet');
INSERT INTO `button_icon` (`id`, `name`) VALUES(232, 'fa-tachometer');
INSERT INTO `button_icon` (`id`, `name`) VALUES(233, 'fa-tasks');
INSERT INTO `button_icon` (`id`, `name`) VALUES(234, 'fa-terminal');
INSERT INTO `button_icon` (`id`, `name`) VALUES(235, 'fa-thumb-tack');
INSERT INTO `button_icon` (`id`, `name`) VALUES(236, 'fa-thumbs-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(237, 'fa-thumbs-o-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(238, 'fa-thumbs-o-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(239, 'fa-thumbs-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(240, 'fa-ticket');
INSERT INTO `button_icon` (`id`, `name`) VALUES(241, 'fa-times');
INSERT INTO `button_icon` (`id`, `name`) VALUES(242, 'fa-times-circle');
INSERT INTO `button_icon` (`id`, `name`) VALUES(243, 'fa-times-circle-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(244, 'fa-tint');
INSERT INTO `button_icon` (`id`, `name`) VALUES(245, 'fa-toggle-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(246, 'fa-toggle-right');
INSERT INTO `button_icon` (`id`, `name`) VALUES(247, 'fa-toggle-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(248, 'fa-trophy');
INSERT INTO `button_icon` (`id`, `name`) VALUES(249, 'fa-truck');
INSERT INTO `button_icon` (`id`, `name`) VALUES(250, 'fa-umbrella');
INSERT INTO `button_icon` (`id`, `name`) VALUES(251, 'fa-unlock-alt');
INSERT INTO `button_icon` (`id`, `name`) VALUES(252, 'fa-unsorted');
INSERT INTO `button_icon` (`id`, `name`) VALUES(253, 'fa-upload');
INSERT INTO `button_icon` (`id`, `name`) VALUES(254, 'fa-users');
INSERT INTO `button_icon` (`id`, `name`) VALUES(255, 'fa-video-camera');
INSERT INTO `button_icon` (`id`, `name`) VALUES(256, 'fa-volume-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(257, 'fa-volume-off');
INSERT INTO `button_icon` (`id`, `name`) VALUES(258, 'fa-volume-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(259, 'fa-warning');
INSERT INTO `button_icon` (`id`, `name`) VALUES(260, 'fa-bitcoin');
INSERT INTO `button_icon` (`id`, `name`) VALUES(261, 'fa-btc');
INSERT INTO `button_icon` (`id`, `name`) VALUES(262, 'fa-cny');
INSERT INTO `button_icon` (`id`, `name`) VALUES(263, 'fa-dollar');
INSERT INTO `button_icon` (`id`, `name`) VALUES(264, 'fa-eur');
INSERT INTO `button_icon` (`id`, `name`) VALUES(265, 'fa-euro');
INSERT INTO `button_icon` (`id`, `name`) VALUES(266, 'fa-gbp');
INSERT INTO `button_icon` (`id`, `name`) VALUES(267, 'fa-inr');
INSERT INTO `button_icon` (`id`, `name`) VALUES(268, 'fa-jpy');
INSERT INTO `button_icon` (`id`, `name`) VALUES(269, 'fa-krw');
INSERT INTO `button_icon` (`id`, `name`) VALUES(270, 'fa-rmb');
INSERT INTO `button_icon` (`id`, `name`) VALUES(271, 'fa-rupee');
INSERT INTO `button_icon` (`id`, `name`) VALUES(272, 'fa-usd');
INSERT INTO `button_icon` (`id`, `name`) VALUES(273, 'fa-won');
INSERT INTO `button_icon` (`id`, `name`) VALUES(274, 'fa-yen');
INSERT INTO `button_icon` (`id`, `name`) VALUES(275, 'fa-stethoscope');
INSERT INTO `button_icon` (`id`, `name`) VALUES(276, 'fa-align-center');
INSERT INTO `button_icon` (`id`, `name`) VALUES(277, 'fa-align-justify');
INSERT INTO `button_icon` (`id`, `name`) VALUES(278, 'fa-align-left');
INSERT INTO `button_icon` (`id`, `name`) VALUES(279, 'fa-align-right');
INSERT INTO `button_icon` (`id`, `name`) VALUES(280, 'fa-bold');
INSERT INTO `button_icon` (`id`, `name`) VALUES(281, 'fa-chain-broken');
INSERT INTO `button_icon` (`id`, `name`) VALUES(282, 'fa-clipboard');
INSERT INTO `button_icon` (`id`, `name`) VALUES(283, 'fa-columns');
INSERT INTO `button_icon` (`id`, `name`) VALUES(284, 'fa-copy');
INSERT INTO `button_icon` (`id`, `name`) VALUES(285, 'fa-cut');
INSERT INTO `button_icon` (`id`, `name`) VALUES(286, 'fa-dedent');
INSERT INTO `button_icon` (`id`, `name`) VALUES(287, 'fa-file');
INSERT INTO `button_icon` (`id`, `name`) VALUES(288, 'fa-file-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(289, 'fa-file-text');
INSERT INTO `button_icon` (`id`, `name`) VALUES(290, 'fa-file-text-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(291, 'fa-files-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(292, 'fa-floppy-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(293, 'fa-font');
INSERT INTO `button_icon` (`id`, `name`) VALUES(294, 'fa-indent');
INSERT INTO `button_icon` (`id`, `name`) VALUES(295, 'fa-italic');
INSERT INTO `button_icon` (`id`, `name`) VALUES(296, 'fa-link');
INSERT INTO `button_icon` (`id`, `name`) VALUES(297, 'fa-list');
INSERT INTO `button_icon` (`id`, `name`) VALUES(298, 'fa-list-alt');
INSERT INTO `button_icon` (`id`, `name`) VALUES(299, 'fa-list-ol');
INSERT INTO `button_icon` (`id`, `name`) VALUES(300, 'fa-list-ul');
INSERT INTO `button_icon` (`id`, `name`) VALUES(301, 'fa-outdent');
INSERT INTO `button_icon` (`id`, `name`) VALUES(302, 'fa-paperclip');
INSERT INTO `button_icon` (`id`, `name`) VALUES(303, 'fa-paste');
INSERT INTO `button_icon` (`id`, `name`) VALUES(304, 'fa-repeat');
INSERT INTO `button_icon` (`id`, `name`) VALUES(305, 'fa-rotate-left');
INSERT INTO `button_icon` (`id`, `name`) VALUES(306, 'fa-rotate-right');
INSERT INTO `button_icon` (`id`, `name`) VALUES(307, 'fa-scissors');
INSERT INTO `button_icon` (`id`, `name`) VALUES(308, 'fa-strikethrough');
INSERT INTO `button_icon` (`id`, `name`) VALUES(309, 'fa-table');
INSERT INTO `button_icon` (`id`, `name`) VALUES(310, 'fa-text-height');
INSERT INTO `button_icon` (`id`, `name`) VALUES(311, 'fa-text-width');
INSERT INTO `button_icon` (`id`, `name`) VALUES(312, 'fa-th');
INSERT INTO `button_icon` (`id`, `name`) VALUES(313, 'fa-th-large');
INSERT INTO `button_icon` (`id`, `name`) VALUES(314, 'fa-th-list');
INSERT INTO `button_icon` (`id`, `name`) VALUES(315, 'fa-underline');
INSERT INTO `button_icon` (`id`, `name`) VALUES(316, 'fa-unlink');
INSERT INTO `button_icon` (`id`, `name`) VALUES(317, 'fa-user-md');
INSERT INTO `button_icon` (`id`, `name`) VALUES(318, 'fa-angle-double-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(319, 'fa-angle-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(320, 'fa-angle-left');
INSERT INTO `button_icon` (`id`, `name`) VALUES(321, 'fa-angle-right');
INSERT INTO `button_icon` (`id`, `name`) VALUES(322, 'fa-angle-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(323, 'fa-arrow-circle-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(324, 'fa-arrow-circle-left');
INSERT INTO `button_icon` (`id`, `name`) VALUES(325, 'fa-arrow-circle-o-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(326, 'fa-arrow-circle-o-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(327, 'fa-arrow-circle-right');
INSERT INTO `button_icon` (`id`, `name`) VALUES(328, 'fa-arrow-circle-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(329, 'fa-arrows-alt');
INSERT INTO `button_icon` (`id`, `name`) VALUES(330, 'fa-caret-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(331, 'fa-caret-left');
INSERT INTO `button_icon` (`id`, `name`) VALUES(332, 'fa-caret-right');
INSERT INTO `button_icon` (`id`, `name`) VALUES(333, 'fa-caret-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(334, 'fa-chevron-circle-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(335, 'fa-chevron-circle-left');
INSERT INTO `button_icon` (`id`, `name`) VALUES(336, 'fa-chevron-circle-right');
INSERT INTO `button_icon` (`id`, `name`) VALUES(337, 'fa-chevron-circle-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(338, 'fa-chevron-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(339, 'fa-chevron-left');
INSERT INTO `button_icon` (`id`, `name`) VALUES(340, 'fa-chevron-right');
INSERT INTO `button_icon` (`id`, `name`) VALUES(341, 'fa-chevron-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(342, 'fa-hand-o-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(343, 'fa-hand-o-left');
INSERT INTO `button_icon` (`id`, `name`) VALUES(344, 'fa-hand-o-right');
INSERT INTO `button_icon` (`id`, `name`) VALUES(345, 'fa-hand-o-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(346, 'fa-long-arrow-down');
INSERT INTO `button_icon` (`id`, `name`) VALUES(347, 'fa-long-arrow-left');
INSERT INTO `button_icon` (`id`, `name`) VALUES(348, 'fa-long-arrow-right');
INSERT INTO `button_icon` (`id`, `name`) VALUES(349, 'fa-long-arrow-up');
INSERT INTO `button_icon` (`id`, `name`) VALUES(350, 'fa-backward');
INSERT INTO `button_icon` (`id`, `name`) VALUES(351, 'fa-compress');
INSERT INTO `button_icon` (`id`, `name`) VALUES(352, 'fa-eject');
INSERT INTO `button_icon` (`id`, `name`) VALUES(353, 'fa-expand');
INSERT INTO `button_icon` (`id`, `name`) VALUES(354, 'fa-fast-backward');
INSERT INTO `button_icon` (`id`, `name`) VALUES(355, 'fa-fast-forward');
INSERT INTO `button_icon` (`id`, `name`) VALUES(356, 'fa-forward');
INSERT INTO `button_icon` (`id`, `name`) VALUES(357, 'fa-pause');
INSERT INTO `button_icon` (`id`, `name`) VALUES(358, 'fa-play');
INSERT INTO `button_icon` (`id`, `name`) VALUES(359, 'fa-play-circle');
INSERT INTO `button_icon` (`id`, `name`) VALUES(360, 'fa-play-circle-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(361, 'fa-step-backward');
INSERT INTO `button_icon` (`id`, `name`) VALUES(362, 'fa-step-forward');
INSERT INTO `button_icon` (`id`, `name`) VALUES(363, 'fa-stop');
INSERT INTO `button_icon` (`id`, `name`) VALUES(364, 'fa-youtube-play');
INSERT INTO `button_icon` (`id`, `name`) VALUES(365, 'fa-adn');
INSERT INTO `button_icon` (`id`, `name`) VALUES(366, 'fa-android');
INSERT INTO `button_icon` (`id`, `name`) VALUES(367, 'fa-apple');
INSERT INTO `button_icon` (`id`, `name`) VALUES(368, 'fa-bitbucket');
INSERT INTO `button_icon` (`id`, `name`) VALUES(369, 'fa-bitbucket-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(370, 'fa-css3');
INSERT INTO `button_icon` (`id`, `name`) VALUES(371, 'fa-dribbble');
INSERT INTO `button_icon` (`id`, `name`) VALUES(372, 'fa-dropbox');
INSERT INTO `button_icon` (`id`, `name`) VALUES(373, 'fa-facebook');
INSERT INTO `button_icon` (`id`, `name`) VALUES(374, 'fa-facebook-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(375, 'fa-flickr');
INSERT INTO `button_icon` (`id`, `name`) VALUES(376, 'fa-foursquare');
INSERT INTO `button_icon` (`id`, `name`) VALUES(377, 'fa-github');
INSERT INTO `button_icon` (`id`, `name`) VALUES(378, 'fa-github-alt');
INSERT INTO `button_icon` (`id`, `name`) VALUES(379, 'fa-github-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(380, 'fa-gittip');
INSERT INTO `button_icon` (`id`, `name`) VALUES(381, 'fa-google-plus');
INSERT INTO `button_icon` (`id`, `name`) VALUES(382, 'fa-google-plus-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(383, 'fa-html5');
INSERT INTO `button_icon` (`id`, `name`) VALUES(384, 'fa-instagram');
INSERT INTO `button_icon` (`id`, `name`) VALUES(385, 'fa-linkedin');
INSERT INTO `button_icon` (`id`, `name`) VALUES(386, 'fa-linkedin-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(387, 'fa-linux');
INSERT INTO `button_icon` (`id`, `name`) VALUES(388, 'fa-maxcdn');
INSERT INTO `button_icon` (`id`, `name`) VALUES(389, 'fa-pinterest');
INSERT INTO `button_icon` (`id`, `name`) VALUES(390, 'fa-pinterest-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(391, 'fa-renren');
INSERT INTO `button_icon` (`id`, `name`) VALUES(392, 'fa-skype');
INSERT INTO `button_icon` (`id`, `name`) VALUES(393, 'fa-stack-overflow');
INSERT INTO `button_icon` (`id`, `name`) VALUES(394, 'fa-trello');
INSERT INTO `button_icon` (`id`, `name`) VALUES(395, 'fa-tumblr');
INSERT INTO `button_icon` (`id`, `name`) VALUES(396, 'fa-tumblr-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(397, 'fa-twitter');
INSERT INTO `button_icon` (`id`, `name`) VALUES(398, 'fa-twitter-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(399, 'fa-vk');
INSERT INTO `button_icon` (`id`, `name`) VALUES(400, 'fa-weibo');
INSERT INTO `button_icon` (`id`, `name`) VALUES(401, 'fa-windows');
INSERT INTO `button_icon` (`id`, `name`) VALUES(402, 'fa-xing');
INSERT INTO `button_icon` (`id`, `name`) VALUES(403, 'fa-xing-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(404, 'fa-youtube');
INSERT INTO `button_icon` (`id`, `name`) VALUES(405, 'fa-youtube-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(406, 'fa-ambulance');
INSERT INTO `button_icon` (`id`, `name`) VALUES(407, 'fa-h-square');
INSERT INTO `button_icon` (`id`, `name`) VALUES(408, 'fa-hospital-o');
INSERT INTO `button_icon` (`id`, `name`) VALUES(409, 'fa-medkit');

-- --------------------------------------------------------

--
-- 表的结构 `company`
--

CREATE TABLE IF NOT EXISTS `company` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `company_sn` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '编码',
  `company_name` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '名称',
  `parent_id` int(11) NOT NULL DEFAULT '1' COMMENT 'PID',
  `contact` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '联系人',
  `phone` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '联系电话',
  `address` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '公司地址',
  `bank_of_deposit` varchar(200) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '开户银行',
  `account` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '开户银行账户',
  `receipt` int(11) NOT NULL DEFAULT '0' COMMENT '是否能力开发票 0=不开1=有',
  `is_sign` int(11) NOT NULL DEFAULT '0' COMMENT '是否财务签字；0为否；1为是',
  `remark` text COLLATE utf8_bin COMMENT '备注',
  `create_user` int(11) DEFAULT NULL COMMENT '创建人',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `is_deleted` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '是否有效 0有效1无效',
  `is_system` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='公司信息表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `company`
--

INSERT INTO `company` (`id`, `company_sn`, `company_name`, `parent_id`, `contact`, `phone`, `address`, `bank_of_deposit`, `account`, `receipt`, `is_sign`, `remark`, `create_user`, `create_time`, `is_deleted`, `is_system`) VALUES(1, 'KELA', '', 0, '预留联系人', '13800138000', '上海', '开户银行', '开户银行账户', 0, 0, NULL, NULL, NULL, 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `company_department`
--

CREATE TABLE IF NOT EXISTS `company_department` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int(10) unsigned NOT NULL,
  `dep_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=42 ;

--
-- 转存表中的数据 `company_department`
--

INSERT INTO `company_department` (`id`, `company_id`, `dep_id`) VALUES(29, 1, 2);
INSERT INTO `company_department` (`id`, `company_id`, `dep_id`) VALUES(31, 3, 2);
INSERT INTO `company_department` (`id`, `company_id`, `dep_id`) VALUES(32, 2, 2);
INSERT INTO `company_department` (`id`, `company_id`, `dep_id`) VALUES(33, 1, 4);
INSERT INTO `company_department` (`id`, `company_id`, `dep_id`) VALUES(34, 3, 1);
INSERT INTO `company_department` (`id`, `company_id`, `dep_id`) VALUES(35, 3, 3);
INSERT INTO `company_department` (`id`, `company_id`, `dep_id`) VALUES(36, 3, 4);
INSERT INTO `company_department` (`id`, `company_id`, `dep_id`) VALUES(37, 2, 1);
INSERT INTO `company_department` (`id`, `company_id`, `dep_id`) VALUES(38, 2, 3);
INSERT INTO `company_department` (`id`, `company_id`, `dep_id`) VALUES(39, 2, 4);
INSERT INTO `company_department` (`id`, `company_id`, `dep_id`) VALUES(40, 1, 1);
INSERT INTO `company_department` (`id`, `company_id`, `dep_id`) VALUES(41, 1, 3);

-- --------------------------------------------------------

--
-- 表的结构 `control`
--

CREATE TABLE IF NOT EXISTS `control` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `label` varchar(20) NOT NULL COMMENT '显示名称',
  `code` varchar(40) NOT NULL COMMENT '控制器名',
  `type` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1为独立对象2为主对象3为明细对象',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '明细对象和主对象的关联字段0为不是明细对象',
  `application_id` int(11) NOT NULL COMMENT '所属项目',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '系统内置',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='文件表' AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `control`
--

INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1, '图标', 'ButtonIcon', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(2, '样式', 'ButtonClass', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(3, '事件', 'ButtonFunction', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(4, '用户', 'User', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(5, '访问日志', 'SystemAccessLog', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(6, '项目', 'Application', 2, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(7, '菜单组', 'MenuGroup', 3, 6, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(8, '控制器', 'Control', 2, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(9, '菜单', 'Menu', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(10, '部门', 'Department', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(11, '岗位', 'Organization', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(12, '开发工具', 'Developer', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(13, '留言簿', 'GuestBook', 2, 0, 2, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(14, '留言本', 'Message', 1, 0, 2, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(15, '留言回复', 'Reply', 3, 13, 2, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(16, '信息回复', 'MessageReply', 3, 13, 2, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(17, '资源类型', 'ResourceType', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(18, '数据字典', 'Dict', 2, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(19, '工作组', 'Group', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(20, '操作', 'Operation', 3, 8, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(21, '按钮', 'Button', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(22, '角色', 'Role', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(23, '权限', 'Permission', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(24, '组用户', 'GroupUser', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(25, '组角色', 'GroupRole', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(26, '角色用户', 'RoleUser', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(27, '角色授权', 'RolePermission', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(28, '用户授权', 'UserPermission', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(29, '数据库配置', 'ConfItem', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(30, '用户回收站', 'UserRecycle', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(31, '地理信息', 'Region', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(32, '项目回收站', 'ApplicationRecycle', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(33, '事件回收站', 'ButtonFunctionRecycle', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(34, '按钮回收站', 'ButtonRecycle', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(35, '分公司回收', 'CompanyRecycle', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(36, '控制器回收', 'ControlRecycle', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(37, '已删操作', 'OperationRecycle', 3, 8, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(38, '字典明细', 'DictItem', 3, 18, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(101, '支付方式', 'Payment', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(102, '友情链接', 'ForumLinks', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(103, '快递公司', 'Express', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(104, '分公司', 'Company', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(105, '分公司归属', 'CompanyDepartment', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(106, '大区管理', 'LargeArea', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(107, '分公司归属一', 'DepartmentCompany', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(108, '体验店', 'ShopCfg', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(109, '销售渠道', 'SalesChannels', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(110, '体验店回收站', 'ShopCfgRecycle', 1, 0, 1, 0, 1);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1001, '仓库管理', 'Warehouse', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1002, '采购分类', 'PurchaseType', 1, 0, 3, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1003, '货架管理', 'WarehouseCabinet', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1004, '材质', 'AppMaterialInfo', 1, 0, 4, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1005, '顾客管理', 'BaseMemberInfo', 1, 0, 5, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1006, '货位管理', 'CargoSpace', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1007, '供应商信息', 'AppProcessorInfo', 1, 0, 6, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1008, '属性名称', 'AppAttribute', 1, 0, 4, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1009, '供应商分类', 'AppProcessorType', 1, 0, 6, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1010, '会员卡', 'AppMemeberCard', 1, 0, 5, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1011, '款式信息', 'BaseStyleInfo', 1, 0, 4, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1012, '会员地址管理', 'AppMemberAddress', 1, 0, 5, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1013, '属性值管理', 'AppAttributeValue', 1, 0, 4, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1014, '仓库公司关联', 'Warehouserel', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1015, '采购单列表', 'PurchaseInfo', 1, 0, 3, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1016, '仓库区域', 'Warehousearea', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1017, '分类类型', 'AppCatType', 1, 0, 4, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1018, '预约管理', 'AppBespokeInfo', 1, 0, 5, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1019, '货架层管理', 'WarehouseLess', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1020, '会员账户', 'AppMemberAccount', 1, 0, 5, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1021, '相册管理', 'AppStyleGallery', 1, 0, 4, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1022, '入库单', 'WarehouseInOrder', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1023, '布产列表', 'ProductInfo', 1, 0, 6, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1025, '产品线', 'AppProductType', 1, 0, 4, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1026, '会员积分', 'AppMemeberPoint', 1, 0, 5, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1027, '金损', 'AppJinsun', 1, 0, 4, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1028, '石头信息', 'RelStyleStone', 1, 0, 4, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1029, '订单', 'Order', 1, 0, 7, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1030, '工厂信息', 'RelStyleFactory', 1, 0, 4, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1031, '申请工厂信息', 'AppFactoryApply', 1, 0, 4, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1032, '钻石规格单价', 'AppDiamondPrice', 1, 0, 4, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1033, '添加工费', 'AppProcessorFee', 1, 0, 6, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1034, '出库单', 'WarehouseOutVoucher', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1035, '货品', 'AppOrderDetails', 1, 0, 7, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1036, '分类属性', 'RelCatAttribute', 1, 0, 4, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1037, '销售政策', 'BaseSalepolicyInfo', 1, 0, 8, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1038, '货品上架', 'GoodsWarehouse', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1039, '收货地址', 'OrderAddress', 1, 0, 7, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1041, '预约操作日志', 'AppBespokeActionLog', 1, 0, 5, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1042, '出库单', 'OutVoucher', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1043, '工厂操作', 'ProductFactoryOpra', 1, 0, 6, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1044, '工厂出货', 'ProductShipment', 1, 0, 6, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1045, '工厂质检', 'ProductOqcOpra', 1, 0, 6, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1046, '仓储单据', 'WarehouseBill', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1047, '仓储单据类型', 'WarehouseBillType', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1048, '裸钻国际报价', 'DiamondPrice', 1, 0, 10, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1049, '调拨单', 'WarehouseBillInfoM', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1050, '裸钻信息', 'DiamondInfo', 1, 0, 10, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1051, '商品信息', 'WarehouseGoods', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1052, '不良品返厂单', 'DefectiveProduct', 2, 0, 3, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1053, '采购收货单', 'PurchaseReceipt', 2, 0, 3, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1054, '损益单', 'WarehouseBillInfoE', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1055, '供应商申请信息', 'AppProcessorRecord', 1, 0, 6, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1056, '供应商日志', 'AppProcessorOperation', 1, 0, 6, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1057, '商品列表', 'ListStyleGoods', 1, 0, 4, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1058, '裸钻加价率', 'DiamondJiajialv', 1, 0, 10, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1059, '质检列表', 'PurchaseReceiptDetail', 1, 0, 3, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1060, '配置管理', 'AppShopConfig', 1, 0, 10, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1061, '财务收银', 'AppOrderPayAction', 1, 0, 11, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1062, '退货单', 'WarehouseBillInfoB', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1063, '其他出货单', 'WarehouseBillInfoC', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1064, '质检操作', 'PurchaseIqcOpra', 1, 0, 3, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1065, '裸钻加价率日志', 'DiamondInfoLog', 1, 0, 10, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1066, '款式属性', 'RelStyleAttribute', 1, 0, 4, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1067, '定金收据列表', 'AppReceiptDeposit', 1, 0, 11, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1068, '供应商审批流程', 'AppProcessorProcess', 2, 0, 6, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1069, '仓库盘点', 'WarehouseBillInfoW', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1070, '其他收货单', 'WarehouseBillInfoT', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1072, '采购收货货品日志', 'PurchaseLog', 1, 0, 3, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1073, '收货单', 'WarehouseBillInfoL', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1074, '应付申请单', 'AppPayApply', 2, 0, 11, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1075, '结账账期管理', 'AppJiezhang', 1, 0, 11, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1076, '点款收据列表', 'AppReceiptPay', 1, 0, 11, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1077, '收货明细', 'WarehouseBillGoods', 1, 0, 9, 0, 0);
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1078, '付款确认', 'AppOrderPayActionList', 1, 0, 11, 0, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='部门/组织架构' AUTO_INCREMENT=11 ;

--
-- 转存表中的数据 `department`
--

INSERT INTO `department` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES(1, '总部部', 'K01', '总部123', 0, '0', '', 10, 1, 0, 1);
INSERT INTO `department` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES(2, '线上事业部', 'K002', '热我认为', 1, '0-1', '1', 1, 1418437719, 0, 0);
INSERT INTO `department` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES(3, '线下事业部', 'K003', 'fdsfs', 1, '0-1', '1', 1, 1418455327, 0, 0);
INSERT INTO `department` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES(4, '北京西单体验店', 'BJXDTYD', '', 3, '0-1-3', '1,3', 0, 0, 0, 0);
INSERT INTO `department` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES(5, '上海南京东路体验店', 'SHNJDLTYD', '', 3, '0-1-3', '1,3', 0, 1421741299, 0, 0);
INSERT INTO `department` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES(6, '淘宝部门', 'TB', '', 2, '0-1-2', '1,2', -1, 1421741318, 0, 0);
INSERT INTO `department` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES(7, '京东部', 'JD', '', 2, '0-1-2', '1,2', 0, 1421741342, 0, 0);
INSERT INTO `department` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES(8, '测试一级', 'test1', '', 1, '0-1', '1,2,6', 2, 1421924105, 0, 0);
INSERT INTO `department` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES(9, '测试', 'test2', '', 8, '0-1-8', '1,8', 0, 1421924123, 0, 0);
INSERT INTO `department` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES(10, '测试二', 'test22', '', 8, '0-1-8', '1,8', 0, 1421924182, 0, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='数据字典' AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `dict`
--

INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1, 'user.user_type', '用户类型', 1, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(2, 'is_enabled', '启用禁用', 1, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(3, 'user.is_on_work', '员工状态', 1, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(4, 'gender', '性别', 1, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(5, 'confirm', '是否', 1, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(6, 'login_status', '登录状态', 1, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(7, 'position_level', '职级', 1, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(8, 'position', '职位', 1, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(9, 'guest_book.education', '留言本学历', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1009, 'member.member_type', '顾客类型', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1010, 'card_status', '会员卡状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1011, 'purchase_status', '采购单状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1012, 'warehouse_in_status', '单据状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1013, 'buchan_status', '布产单状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1014, 'warehouse.put_in_type', '入库方式', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1015, 'warehouse.order_type', '入库单据类型', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1017, 'factory_req', '工厂需求', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1018, 'OQC_reason', 'OQC未过原因', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1020, 'order.order_status', '订单状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1021, 'order.check_status', '订单审核状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1022, 'order.order_pay_status', '订单支付状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1023, 'order.order_pay_type', '订单付款方式', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1024, 'sales_channels_class', '渠道一级分类', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1025, 'sales_channels_type', '渠道二级分类', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1026, 'warehouse.voucher_type', '出库单类型', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1027, 'buchan_fac_opra', '布产单工厂操作', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1028, 'ordergoods.status', '商品状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1029, 'address.distribution', '订单配送方式', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1031, 'app_jinsun.price_type', '款式类别', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1032, 'app_salepolicy_channel.status', '状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1033, 'app_salepolicy_channel.is_delete', '删除状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1034, 'warehouse.bill_status', '仓储单据状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1035, 'warehouse.goods_status', '货品状态-仓储管理', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1036, 'receipt.detail_status', '采购收货货品状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1037, 'iqc_opra', 'IQC质检操作', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1038, 'bl_status', '不良品返厂单状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1039, 'in_warehouse_type', '退货返厂入库方式', 0, 1);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1040, 'DefectiveProductStatus', '采购收货单状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1041, 'is_active', '是否活动', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1042, 'warehouse.ruku_type', '入库类型', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1043, 'style_good_status', '款式商品上下架', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1044, 'warehouse.bill_pay_content', '收货支付内容', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1045, 'warehouse.pay_method', '结算方式', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1046, 'bespoke_status', '预约状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1047, 'goshop_status', '预约到店状态', 0, 0);
INSERT INTO `dict` (`id`, `name`, `label`, `is_system`, `is_deleted`) VALUES(1048, 'region.type', '省市区', 0, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='字典明细表' AUTO_INCREMENT=1168 ;

--
-- 转存表中的数据 `dict_item`
--

INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1, 1, 1, '超级管理员', '超级管理员', 1434567890, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(2, 1, 2, '权限管理员', NULL, 1435677779, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(3, 1, 3, '系统用户', NULL, 1435677778, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(4, 2, 1, '启用', '启用', 1417954105, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(5, 2, 0, '停用', '停用', 1417954167, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(6, 3, 1, '在职', '在职', 1417954865, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(7, 3, 0, '离职', '离职', 1417955381, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(8, 4, 0, '男', '男', 1418010228, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(9, 4, 1, '女', '女', 1418010445, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(10, 5, 0, '否', '', 1418104095, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(11, 5, 1, '是', '', 1418104105, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(12, 6, 1, '用户不存在', NULL, 1, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(13, 6, 2, '账户未启用', NULL, 2, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(14, 6, 3, '密码不正确', NULL, 3, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(15, 6, 4, '登陆成功', NULL, 4, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(16, 6, 5, '退出成功', NULL, 5, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(17, 6, 6, '修改密码', NULL, 6, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(18, 6, 7, '邮箱不正确', NULL, 7, 1, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(19, 7, 1, '初级', '', 1418366339, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(20, 7, 2, '助理', '', 1418366356, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(21, 7, 3, '中级', '', 1418366369, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(22, 7, 4, '副高级', '', 1418371308, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(23, 7, 5, '高级', '', 1418371322, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(24, 8, 1, '董事长', '', 1418374417, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(25, 8, 2, '总经理', '', 1418374694, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(26, 8, 3, '副总经理', '', 1418375288, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(27, 8, 4, '总监', '', 1418375392, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(28, 8, 5, '经理', '', 1418375556, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(29, 8, 6, '专员', '', 1418375564, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(30, 8, 7, '主管', '', 1418375571, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(31, 8, 8, '收银', '', 1418375604, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(32, 8, 9, '出纳', '', 1418375611, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(33, 8, 10, '前台文员', '', 1418375649, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(34, 8, 11, '秘书', '', 1418375654, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(35, 8, 12, '顾问', '', 1418375669, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(36, 8, 13, '咨询师', '', 1418375719, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(37, 8, 14, '工程师', '', 1418375727, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(39, 9, 1, '小学', '', 1421049725, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(40, 9, 2, '烈士', '', 1421049732, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1038, 1009, 1, '普通用户', '普通用户', 1420636324, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1039, 1009, 2, '高级用户', '高级用户', 1420636336, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1040, 1010, 1, '有效', '会员卡有效', 1420712526, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1041, 1010, 2, '新卡', '新会员卡', 1420712541, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1042, 1010, 3, '挂失', '会员卡挂失', 1420712555, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1043, 1010, 4, '注销', '会员卡注销', 1420712563, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1044, 1011, 1, '已保存', '采购单状态-初始状态', 1420727196, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1045, 1011, 2, '待审核', '采购单状态-待审核', 1420727207, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1046, 1011, 3, '已审核', '采购单状态-已审核', 1420727218, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1047, 1011, 4, '已作废', '采购单状态-作废', 1420727228, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1048, 1012, 1, '待审核', '入库单状态－初始状态', 1420772809, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1049, 1013, 1, '初始化', '采购单已审核，自动生成布产单，布产单初始化。', 1420772861, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1050, 1012, 2, '已取消', '入库单状态－审核未通过', 1420772875, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1051, 1013, 2, '待分配', '布产单分配了跟单人，等待分配工厂', 1420772894, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1052, 1012, 3, '已审核', '入库单状态－审核通过', 1420772925, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1053, 1013, 3, '已分配', '布产单已经分配到工厂（属于四大工厂的自动分配）', 1420772967, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1054, 1013, 4, '生产中', '工厂点击开始生产，布产单为生产中。', 1420773008, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1055, 1013, 5, '质检中', 'BDD接货，进入质检流程。', 1420773043, 0, 1);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1056, 1013, 6, '质检完成', '质检完成', 1420773067, 0, 1);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1057, 1013, 7, '部分出厂', '布产单，BDD接单后，部分数量质检完成为部分出厂。', 1420773169, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1058, 1013, 8, '作废', '整个布产单都作废掉，不继续生产。', 1420773230, 0, 1);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1059, 1013, 9, '已出厂', '布产单，接单质检通过数量和布产单数量相同为全部出厂，布产单状态为已出厂。', 1420773797, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1060, 1014, 1, '购买', '入库方式：购买', 1420801555, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1061, 1014, 2, '加工', '入库方式：加工', 1420801568, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1062, 1014, 3, '代销', '入库方式：代销', 1420801581, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1063, 1014, 4, '借入', '入库方式：借入', 1420801593, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1064, 1015, 1, '购买', '购买入库', 1420802134, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1065, 1015, 2, '转仓', '转仓入库', 1420802150, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1066, 1017, 1, '工厂镶钻', '', 1420812882, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1067, 1017, 2, '不需工厂配钻', '', 1420812896, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1068, 1018, 1, '指圈不对', '', 1420813016, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1069, 1018, 2, '镶钻不稳', '', 1420813067, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1075, 1020, 1, '有效', '订单状态有效默认值', 1420871486, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1076, 1020, 2, '无效', '订单状态无效', 1420871514, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1077, 1020, 3, '记录删除', '', 1420871531, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1078, 1020, 4, '申请关闭', '', 1420871557, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1079, 1021, 1, '待审核', '', 1420871804, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1080, 1021, 2, '已审核', '', 1420871814, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1081, 1021, 3, '审核未通过', '', 1420871829, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1082, 1021, 4, '退货', '', 1420871842, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1083, 1021, 5, '关闭', '', 1420871849, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1084, 1021, 6, '无效', '默认值', 1420871865, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1085, 1022, 1, '未付款', '', 1420873199, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1086, 1022, 2, '部分付款', '', 1420873210, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1087, 1022, 3, '已付款', '', 1420873222, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1088, 1023, 1, '网络付款', '', 1420873261, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1089, 1023, 2, '财务备案', '', 1420873273, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1090, 1024, 1, '线上', '线上——销售渠道', 1420876372, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1091, 1024, 2, '线下', '线下——销售渠道', 1420876391, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1092, 1025, 1, '部门', '部门——销售渠道', 1420876458, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1093, 1025, 2, '体验店', '体验店——销售渠道', 1420876472, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1094, 1025, 3, '公司', '公司——销售渠道', 1420876487, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1095, 1026, 1, '外部', '外部-出库单', 1420942864, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1096, 1026, 2, '内部', '内部-出库单', 1420942878, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1097, 1027, 1, '倒模', '', 1420965754, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1098, 1027, 2, '抛光', '', 1420965761, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1099, 1027, 3, '等钻', '', 1420965779, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1100, 1028, 1, '有效', '商品默认值', 1420967165, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1101, 1028, 2, '无效', '', 1420967174, 0, 1);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1102, 1028, 3, '记录删除', '', 1420967185, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1103, 1029, 1, '总公司发客户', '', 1421031098, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1104, 1029, 2, '配送体验店', '', 1421031111, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1105, 1029, 3, '经销商', '', 1421031124, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1108, 1031, 1, '男戒', '男戒', 1421146740, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1109, 1031, 2, '女戒', '女戒', 1421146757, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1110, 1031, 3, '情侣男戒', '情侣男戒', 1421146831, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1111, 1031, 4, '情侣女戒', '情侣女戒', 1421146846, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1112, 1032, 1, '保存', '保存', 1421154401, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1113, 1032, 2, '申请', '申请', 1421154416, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1114, 1032, 3, '审核通过', '审核通过', 1421154439, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1115, 1032, 4, '未通过', '未通过', 1421154456, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1116, 1032, 5, '取消', '取消', 1421154470, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1117, 1033, 1, '未删除', '未删除', 1421154583, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1118, 1033, 2, '已删除', '已删除', 1421154595, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1119, 1034, 1, '已保存', '新建单据已经保存', 1421204489, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1120, 1034, 2, '已审核', '审核通过', 1421204502, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1121, 1034, 3, '已取消', '单据取消', 1421204516, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1122, 1035, 1, '收货中', '', 1421246369, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1123, 1035, 2, '库存', '', 1421246377, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1124, 1035, 3, '已销售', '', 1421246396, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1125, 1036, 1, '初始化', '采购收货单刚刚收货，收货单状态还是初始化的时候，货品明细也为初始化', 1421566165, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1126, 1036, 2, '已报废', '质检操作报废', 1421566237, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1127, 1036, 3, '待质检', '采购收货单审核通过，明细货品等待质检。', 1421566273, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1128, 1036, 4, '已质检', '货品质检通过', 1421566319, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1129, 1036, 5, 'IQC未过', '质检不通过，等待生成不良品返厂单', 1421566348, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1130, 1036, 6, '待返厂', '已生成不良品返厂单，等待审核返厂单。', 1421566430, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1131, 1036, 7, '已返厂', '不良品返厂单已审核，货品为已返厂。', 1421566465, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1132, 1037, 1, '质检通过', 'IQC质检通过操作', 1421577417, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1133, 1037, 2, '报废', 'IQC质检 货品报废', 1421577437, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1134, 1037, 3, 'IQC未过', 'IQC质检未通过', 1421577458, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1135, 1036, 8, '已取消', '采购收货单取消后，对应货品也为作废状态', 1421587207, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1136, 1038, 1, '已保存', '初始化状态', 1421595860, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1137, 1038, 2, '已审核', '审核不良品返厂单，返厂。', 1421595882, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1138, 1040, 1, '已保存', '采购收货单保存状态', 1421651835, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1139, 1040, 2, '已审核', '采购收货单审核，确认收到货。', 1421651859, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1140, 1038, 3, '已取消', '取消不良品返厂单，单据内容恢复为IQC未过', 1421726428, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1141, 1041, 1, '正常', '正常', 1421737088, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1142, 1041, 2, '活动', '活动', 1421737108, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1143, 1042, 1, '换系统', '', 1421738181, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1144, 1042, 2, '拆货', '', 1421738188, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1145, 1042, 3, '货品组装', '', 1421738207, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1146, 1042, 4, '盘盈', '', 1421738224, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1147, 1040, 3, '已取消', '采购单取消，此次收货无效。', 1421738376, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1148, 1043, 1, '上架', '上架', 1421739096, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1149, 1043, 2, '下架', '下架', 1421739111, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1150, 1044, 1, '金料', '', 1421739375, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1151, 1044, 2, '石料', '', 1421739384, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1152, 1044, 3, '成品', '', 1421739393, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1153, 1044, 4, '证书费', '', 1421739408, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1154, 1044, 5, '加工费', '', 1421739415, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1155, 1045, 1, '记账', '', 1421739459, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1156, 1045, 2, '已付款', '', 1421739499, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1157, 1046, 1, '保存', '保存', 1421754262, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1158, 1046, 2, '已经审核', '已经审核', 1421754277, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1159, 1046, 3, '作废', '作废', 1421754296, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1160, 1047, 1, '到店', '到店', 1421759616, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1161, 1047, 2, '未到店', '未到店', 1421759632, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1162, 1035, 4, '盘点中', '盘点中', 1421939863, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1163, 1035, 5, '调拨中', '调拨中', 1421939863, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1164, 1048, 0, '国家', '', 1422016501, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1165, 1048, 1, '省', '省、直辖市、自治区', 1422016565, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1166, 1048, 2, '市', '', 1422016578, 0, 0);
INSERT INTO `dict_item` (`id`, `dict_id`, `name`, `label`, `note`, `display_order`, `is_system`, `is_deleted`) VALUES(1167, 1048, 3, '区县', '', 1422016587, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `express`
--

CREATE TABLE IF NOT EXISTS `express` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `exp_name` varchar(100) DEFAULT NULL COMMENT '快递名称',
  `exp_code` varchar(50) DEFAULT NULL COMMENT '快递编码',
  `exp_areas` varchar(100) DEFAULT NULL COMMENT '配送区域',
  `exp_tel` char(20) DEFAULT NULL COMMENT '联系电话',
  `exp_note` varchar(200) DEFAULT NULL COMMENT '备注说明',
  `is_deleted` tinyint(4) DEFAULT '0' COMMENT '删除标识',
  `addby_id` int(11) DEFAULT NULL COMMENT '创建人',
  `add_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='快递公司' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `express`
--

INSERT INTO `express` (`id`, `exp_name`, `exp_code`, `exp_areas`, `exp_tel`, `exp_note`, `is_deleted`, `addby_id`, `add_time`) VALUES(1, '顺丰', 'SHUNFENG', '全国', '4008111111', NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- 表的结构 `forum_links`
--

CREATE TABLE IF NOT EXISTS `forum_links` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `title` varchar(40) NOT NULL COMMENT '显示文字',
  `url_img` varchar(200) DEFAULT NULL,
  `url_addr` varchar(200) NOT NULL COMMENT '链接地址',
  `display_order` tinyint(4) NOT NULL DEFAULT '0' COMMENT '显示顺序',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='友情链接表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `forum_links`
--

INSERT INTO `forum_links` (`id`, `title`, `url_img`, `url_addr`, `display_order`) VALUES(1, 'BDD商贸', '/upload/image/20150113/LFn35AypHR_170718.png', 'www.kela.cn', 0);

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

INSERT INTO `group` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES(1, '张三', '123', '方的身份的身份放到沙发上', 0, '0', '', 3, 1418718419, 0, 0);
INSERT INTO `group` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES(2, '李四', '111', '放到沙发上订单111', 1, '0-1', '1', 0, 1418718474, 0, 0);
INSERT INTO `group` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES(3, '王五', '222', '范德萨范德萨', 1, '0-1', '1', 0, 1418718447, 0, 0);
INSERT INTO `group` (`id`, `name`, `code`, `note`, `parent_id`, `tree_path`, `pids`, `childrens`, `display_order`, `is_deleted`, `is_system`) VALUES(4, '放到沙发上', '12345', '', 1, '0-1', '1', 0, 1418718698, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `group_role`
--

CREATE TABLE IF NOT EXISTS `group_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `group_id` int(11) NOT NULL COMMENT '组id',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='组角色表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `group_role`
--

INSERT INTO `group_role` (`id`, `group_id`, `role_id`) VALUES(1, 1, 2);

-- --------------------------------------------------------

--
-- 表的结构 `group_user`
--

CREATE TABLE IF NOT EXISTS `group_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(11) NOT NULL COMMENT '用户',
  `group_id` int(11) NOT NULL COMMENT '组别',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='组用户管理' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `group_user`
--

INSERT INTO `group_user` (`id`, `user_id`, `group_id`) VALUES(1, 2, 2);

-- --------------------------------------------------------

--
-- 表的结构 `guest_book`
--

CREATE TABLE IF NOT EXISTS `guest_book` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `content` text NOT NULL,
  `gender` tinyint(4) DEFAULT '0',
  `education` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=18 ;

--
-- 转存表中的数据 `guest_book`
--

INSERT INTO `guest_book` (`id`, `title`, `content`, `gender`, `education`) VALUES(6, '测试', 'aaa', 0, 1);
INSERT INTO `guest_book` (`id`, `title`, `content`, `gender`, `education`) VALUES(17, '测试', 'aaa', 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `large_area`
--

CREATE TABLE IF NOT EXISTS `large_area` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(30) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '名称',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT 'PID',
  `tree_path` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '全路径',
  `pids` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '祖先分类数',
  `childrens` int(11) NOT NULL DEFAULT '0' COMMENT '下级分类数',
  `create_user` int(11) NOT NULL COMMENT '创建人',
  `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `is_enable` int(11) NOT NULL COMMENT '是否有效 0有效1无效',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='大区表' AUTO_INCREMENT=10 ;

--
-- 转存表中的数据 `large_area`
--

INSERT INTO `large_area` (`id`, `name`, `parent_id`, `tree_path`, `pids`, `childrens`, `create_user`, `create_time`, `is_enable`) VALUES(1, '一级', 0, '', NULL, 0, 1, 1, 0);
INSERT INTO `large_area` (`id`, `name`, `parent_id`, `tree_path`, `pids`, `childrens`, `create_user`, `create_time`, `is_enable`) VALUES(2, '二级', 1, '', NULL, 0, 1, 1, 0);
INSERT INTO `large_area` (`id`, `name`, `parent_id`, `tree_path`, `pids`, `childrens`, `create_user`, `create_time`, `is_enable`) VALUES(3, '三级', 5, '', NULL, 0, 1, 1, 0);
INSERT INTO `large_area` (`id`, `name`, `parent_id`, `tree_path`, `pids`, `childrens`, `create_user`, `create_time`, `is_enable`) VALUES(4, '大', 0, '', NULL, 0, 1, 1, 0);
INSERT INTO `large_area` (`id`, `name`, `parent_id`, `tree_path`, `pids`, `childrens`, `create_user`, `create_time`, `is_enable`) VALUES(5, '四级', 3, '', NULL, 0, 1, 0, 0);
INSERT INTO `large_area` (`id`, `name`, `parent_id`, `tree_path`, `pids`, `childrens`, `create_user`, `create_time`, `is_enable`) VALUES(6, '阿萨德', 0, '', NULL, 0, 0, 0, 0);
INSERT INTO `large_area` (`id`, `name`, `parent_id`, `tree_path`, `pids`, `childrens`, `create_user`, `create_time`, `is_enable`) VALUES(7, '是', 0, '', NULL, 0, 0, 0, 0);
INSERT INTO `large_area` (`id`, `name`, `parent_id`, `tree_path`, `pids`, `childrens`, `create_user`, `create_time`, `is_enable`) VALUES(8, '是', 0, '', NULL, 0, 0, 0, 0);
INSERT INTO `large_area` (`id`, `name`, `parent_id`, `tree_path`, `pids`, `childrens`, `create_user`, `create_time`, `is_enable`) VALUES(9, '终身大事', 0, '', NULL, 0, 1, 1419846429, 1);

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
  `icon` int(11) NOT NULL DEFAULT '0' COMMENT '图标',
  `group_id` int(11) NOT NULL COMMENT '所属菜单组',
  `application_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属项目',
  `display_order` int(11) NOT NULL DEFAULT '0' COMMENT '显示顺序',
  `is_enabled` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用',
  `is_system` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否系统内置',
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='系统菜单表' AUTO_INCREMENT=114 ;

--
-- 转存表中的数据 `menu`
--

INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(1, 1, 1, '图标管理', 'BUTTON_ICON', 'index.php?mod=management&con=ButtonIcon&act=index', 1, 1, 1, 1, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(2, 2, 8, '样式管理', 'BUTTON_CLASS', 'index.php?mod=management&con=ButtonClass&act=index', 1, 1, 1, 2, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(3, 3, 16, '事件管理', 'BUTTON_FUNCTION', 'index.php?mod=management&con=ButtonFunction&act=index', 1, 1, 1, 3, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(4, 4, 23, '用户管理', 'USER', 'index.php?mod=management&con=User&act=index', 1, 3, 1, 10, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(5, 5, 37, '访问日志', 'SYSTEM_ACCESS_LOG', 'index.php?mod=management&con=SystemAccessLog&act=index', 1, 8, 1, 5, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(6, 6, 39, '项目管理', 'APPLICATION', 'index.php?mod=management&con=Application&act=index', 1, 7, 1, 15, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(7, 8, 57, '控制器管理', 'CONTROL', 'index.php?mod=management&con=Control&act=index', 1, 7, 1, 8, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(8, 9, 69, '菜单管理', 'MENU', 'index.php?mod=management&con=Menu&act=index', 1, 7, 1, 7, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(9, 10, 76, '部门管理', 'DEPARTMENT', 'index.php?mod=management&con=Department&act=index', 1, 3, 1, 9, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(10, 11, 84, '岗位设置', 'ORGANIZATION', 'index.php?mod=management&con=Organization&act=index', 1, 3, 1, 4, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(11, 12, 91, '开发工具', 'DEVELOPER', 'index.php?mod=management&con=Developer&act=index', 1, 1, 1, 30, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(12, 13, 92, '留言簿', 'GUEST_BOOK', 'index.php?mod=demo&con=GuestBook&act=index', 1, 2, 2, 12, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(13, 14, 100, '留言本', 'MESSAGE', 'index.php?mod=demo&con=Message&act=index', 1, 2, 2, 13, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(14, 17, 120, '资源类型', 'RESOURCE_TYPE', 'index.php?mod=management&con=ResourceType&act=index', 1, 1, 1, 11, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(15, 18, 121, '数据字典', 'DICT', 'index.php?mod=management&con=Dict&act=index', 1, 7, 1, 17, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(16, 19, 130, '工作组', 'GROUP', 'index.php?mod=management&con=Group&act=index', 1, 4, 1, 24, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(17, 21, 144, '按钮管理', 'BUTTON', 'index.php?mod=management&con=Button&act=index', 1, 7, 1, 6, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(18, 22, 151, '角色管理', 'ROLE', 'index.php?mod=management&con=Role&act=index', 1, 4, 1, 23, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(19, 23, 158, '权限管理', 'PERMISSION', 'index.php?mod=management&con=Permission&act=index', 1, 4, 1, 19, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(20, 24, 161, '组用户管理', 'GROUP_USER', 'index.php?mod=management&con=GroupUser&act=index', 1, 4, 1, 21, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(21, 25, 166, '组角色管理', 'GROUP_ROLE', 'index.php?mod=management&con=GroupRole&act=index', 1, 4, 1, 22, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(22, 26, 171, '角色用户', 'ROLE_USER', 'index.php?mod=management&con=RoleUser&act=index', 1, 4, 1, 20, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(23, 27, 176, '角色授权', 'ROLE_PERMISSION', 'index.php?mod=management&con=RolePermission&act=index', 1, 4, 1, 18, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(24, 28, 177, '用户授权', 'USER_PERMISSION', 'index.php?mod=management&con=UserPermission&act=index', 1, 4, 1, 16, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(25, 101, 220, '支付方式', 'PAYMENT', 'index.php?mod=management&con=Payment&act=index', 1, 6, 1, 34, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(26, 102, 229, '友情链接', 'FORUM_LINKS', 'index.php?mod=management&con=ForumLinks&act=index', 1, 6, 1, 36, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(27, 103, 238, '快递公司', 'EXPRESS', 'index.php?mod=management&con=Express&act=index', 1, 6, 1, 33, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(28, 104, 245, '分公司信息维护', 'COMPANY', 'index.php?mod=management&con=Company&act=index', 1, 6, 1, 27, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(29, 105, 253, '分公司归属', 'COMPANY_DEPARTMENT', 'index.php?mod=management&con=CompanyDepartment&act=index', 1, 6, 1, 26, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(30, 29, 178, '数据库配置', 'CONF_ITEM', 'index.php?mod=management&con=ConfItem&act=index', 1, 1, 1, 14, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(31, 106, 258, '大区管理', 'LARGE_AREA', 'index.php?mod=management&con=LargeArea&act=index', 1, 6, 1, 28, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(32, 30, 186, '用户回收站', 'USER_RECYCLE', 'index.php?mod=management&con=UserRecycle&act=index', 1, 5, 1, 42, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(33, 107, 265, '分公司归属一', 'DEPARTMENT_COMPANY', 'index.php?mod=management&con=DepartmentCompany&act=index', 1, 6, 1, 25, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(34, 108, 270, '体验店管理', 'SHOP_CFG', 'index.php?mod=management&con=ShopCfg&act=index', 1, 6, 1, 29, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(35, 109, 278, '销售渠道', 'SALES_CHANNELS', 'index.php?mod=management&con=SalesChannels&act=index', 1, 6, 1, 31, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(36, 31, 189, '地理信息', 'REGION', 'index.php?mod=management&con=Region&act=index', 1, 6, 1, 35, 1, 1, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(37, 32, 196, '项目回收站', 'APPLICATION_RECYCLE', 'index.php?mod=management&con=ApplicationRecycle&act=index', 30, 5, 1, 39, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(38, 33, 199, '事件回收站', 'BUTTON_FUNCTION_RECYCLE', 'index.php?mod=management&con=ButtonFunctionRecycle&act=index', 30, 5, 1, 41, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(39, 34, 202, '按钮回收站', 'BUTTON_RECYCLE', 'index.php?mod=management&con=ButtonRecycle&act=index', 30, 5, 1, 38, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(40, 35, 205, '分公司回收站', 'COMPANY_RECYCLE', 'index.php?mod=management&con=CompanyRecycle&act=index', 32, 5, 1, 32, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(41, 36, 208, '控制器回收站', 'CONTROL_RECYCLE', 'index.php?mod=management&con=ControlRecycle&act=index', 30, 5, 1, 40, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(42, 110, 285, '体验店回收站', 'SHOP_CFG_RECYCLE', 'index.php?mod=management&con=ShopCfgRecycle&act=index', 30, 5, 1, 37, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(44, 26, 217, '仓库管理', 'WAREHOUSE', 'index.php?mod=warehouse&con=Warehouse&act=index', 4, 9, 9, 1420945850, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(45, 27, 218, '采购分类', 'PURCHASE_TYPE', 'index.php?mod=purchase&con=PurchaseType&act=index', 206, 11, 3, 1420618030, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(46, 30, 230, '顾客管理', 'BASE_MEMBER_INFO', 'index.php?mod=bespoke&con=BaseMemberInfo&act=index', 99, 13, 5, 1420620966, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(47, 28, 221, '货架管理', 'WAREHOUSE_CABINET', 'index.php?mod=warehouse&con=WarehouseCabinet&act=index', 28, 9, 9, 1420769538, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(48, 31, 233, '筐位管理', 'CARGO_SPACE', 'index.php?mod=warehouse&con=CargoSpace&act=index', 7, 9, 9, 1420623126, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(49, 32, 237, '供应商管理', 'APP_PROCESSOR_INFO', 'index.php?mod=processor&con=AppProcessorInfo&act=index', 7, 16, 6, 1420685001, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(50, 29, 241, '材质管理', 'APP_MATERIAL_INFO', 'index.php?mod=style&con=AppMaterialInfo&act=index', 8, 23, 4, 1420624836, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(51, 34, 248, '供应商分类', 'APP_PROCESSOR_TYPE', 'index.php?mod=processor&con=AppProcessorType&act=index', 68, 16, 6, 1420895251, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(52, 33, 252, '属性信息', 'APP_ATTRIBUTE', 'index.php?mod=style&con=AppAttribute&act=index', 23, 12, 4, 1420901276, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(53, 35, 259, '会员卡管理', 'APP_MEMEBER_CARD', 'index.php?mod=bespoke&con=AppMemeberCard&act=index', 104, 19, 5, 1420691465, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(54, 36, 260, '款式信息', 'BASE_STYLE_INFO', 'index.php?mod=style&con=BaseStyleInfo&act=index', 50, 18, 4, 1420858525, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(55, 38, 267, '属性值管理', 'APP_ATTRIBUTE_VALUE', 'index.php?mod=style&con=AppAttributeValue&act=index', 8, 12, 4, 1420780534, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(56, 37, 271, '会员地址管理', 'APP_MEMBER_ADDRESS', 'index.php?mod=bespoke&con=AppMemberAddress&act=index', 99, 19, 5, 1420704269, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(57, 40, 279, '仓库公司关联列表', 'WAREHOUSEREL_LIST', 'index.php?mod=warehouse&con=Warehouserel&act=index', 5, 9, 9, 1420606473, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(58, 41, 284, '采购列表', 'PURCHASE_INFO', 'index.php?mod=purchase&con=PurchaseInfo&act=index', 5, 17, 3, 1420778359, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(59, 42, 289, '区域管理', 'WAREHOUSEAREA', 'index.php?mod=warehouse&con=Warehousearea&act=index', 4, 9, 9, 1420772181, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(61, 45, 292, '预约管理', 'APP_BESPOKE_INFO', 'index.php?mod=bespoke&con=AppBespokeInfo&act=index', 5, 20, 5, 1420771298, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(62, 46, 294, '架层管理', 'WAREHOUSE_LESS', 'index.php?mod=warehouse&con=WarehouseLess&act=index', 236, 9, 9, 1420713910, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(63, 47, 301, '会员账户', 'APP_MEMBER_ACCOUNT', 'index.php?mod=bespoke&con=AppMemberAccount&act=index', 27, 19, 5, 1420773444, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(64, 48, 306, '相册管理', 'APP_STYLE_GALLERY', 'index.php?mod=style&con=AppStyleGallery&act=index', 8, 18, 4, 1420793944, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(65, 49, 307, '入库单', 'WAREHOUSE_IN_ORDER', 'index.php?mod=warehouse&con=WarehouseInOrder&act=index', 1, 10, 9, 1420775048, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(66, 50, 316, '布产列表', 'PRODUCT_INFO', 'index.php?mod=processor&con=ProductInfo&act=index', 39, 24, 6, 1420728318, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(67, 51, 319, '分类类型', 'APP_CAT_TYPE', 'index.php?mod=style&con=AppCatType&act=index', 1, 12, 4, 1420702161, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(68, 52, 323, '产品线管理', 'APP_PRODUCT_TYPE', 'index.php?mod=style&con=AppProductType&act=index', 1, 12, 4, 1420780498, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(69, 53, 327, '会员积分管理', 'APP_MEMEBER_POINT', 'index.php?mod=bespoke&con=AppMemeberPoint&act=index', 414, 19, 5, 1420781903, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(70, 54, 335, '金损', 'APP_JINSUN', 'index.php?mod=style&con=AppJinsun&act=index', 4, 23, 4, 1420792271, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(71, 55, 336, '石头信息', 'REL_STYLE_STONE', 'index.php?mod=style&con=RelStyleStone&act=index', 17, 18, 4, 1420858436, 0, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(72, 57, 346, '订单', 'ORDER', 'index.php?mod=sales&con=Order&act=index', 5, 21, 7, 1420856954, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(73, 58, 347, '工厂信息', 'REL_STYLE_FACTORY', 'index.php?mod=style&con=RelStyleFactory&act=index', 18, 18, 4, 1420774629, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(74, 59, 348, '申请工厂信息', 'APP_FACTORY_APPLY', 'index.php?mod=style&con=AppFactoryApply&act=index', 18, 18, 4, 1420692819, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(75, 60, 354, '钻石规格单价', 'APP_DIAMOND_PRICE', 'index.php?mod=style&con=AppDiamondPrice&act=index', 4, 23, 4, 1420859118, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(76, 63, 379, '工费管理', 'APP_PROCESSOR_FEE', 'index.php?mod=processor&con=AppProcessorFee&act=index', 5, 16, 6, 1420623724, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(77, 64, 380, '出库单', 'WAREHOUSE_OUT_VOUCHER', 'index.php?mod=warehouse&con=WarehouseOutVoucher&act=index', 448, 10, 9, 1420895570, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(78, 65, 384, '货品', 'APPORDERDETAILS', 'index.php?mod=sales&con=AppOrderDetails&act=index', 10, 21, 7, 1420895820, 0, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(79, 66, 388, '类型属性', 'REL_CAT_ATTRIBUTE', 'index.php?mod=style&con=RelCatAttribute&act=index', 1, 12, 4, 1420686508, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(80, 67, 395, '销售政策列表', 'BASE_SALEPOLICY_INFO', 'index.php?mod=salepolicy&con=BaseSalepolicyInfo&act=index', 1, 22, 8, 1420944235, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(81, 68, 396, '货品上架', 'GOODS_WAREHOUSE', 'index.php?mod=warehouse&con=GoodsWarehouse&act=index', 204, 9, 9, 1420623015, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(82, 69, 418, '收货地址', 'ORDERADDRESS', 'index.php?mod=sales&con=OrderAddress&act=index', 1, 21, 7, 1421032045, 0, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(83, 70, 419, '款式属性', 'REL_STYLE_ATTRIBUTE', 'index.php?mod=style&con=RelStyleAttribute&act=index', 7, 12, 4, 1421038394, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(84, 80, 420, '单据列表', 'WAREHOUSE_BILL', 'index.php?mod=warehouse&con=WarehouseBill&act=index', 8, 10, 9, 1421041433, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(85, 76, 431, '出库单测试', 'OUT_VOUCHER', 'index.php?mod=demo&con=OutVoucher&act=index', 332, 2, 9, 1421056851, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(86, 87, 441, '仓储单据类型', 'WAREHOUSE_BILL_TYPE', 'index.php?mod=warehouse&con=WarehouseBillType&act=index', 10, 9, 9, 1421202158, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(87, 91, 445, '裸钻国际报价', 'DIAMOND_PRICE', 'index.php?mod=diamond&con=DiamondPrice&act=index', 29, 29, 10, 1421580055, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(88, 92, 447, '调拨单', 'WAREHOUSE_BILL_INFO_M', 'index.php?mod=warehouse&con=WarehouseBillInfoM&act=index', 154, 10, 9, 1421728460, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(89, 94, 456, '裸钻信息', 'DIAMOND_INFO', 'index.php?mod=diamond&con=DiamondInfo&act=index', 19, 25, 10, 1421230301, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(90, 95, 460, '商品列表', 'WAREHOUSE_GOODS', 'index.php?mod=warehouse&con=WarehouseGoods&act=index', 37, 26, 9, 1421245938, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(91, 96, 461, '不良品返厂', 'DEFECTIVE_PRODUCT', 'index.php?mod=purchase&con=DefectiveProduct&act=index', 4, 27, 3, 1421291415, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(92, 97, 462, '采购收货单', 'PURCHASE_RECEIPT', 'index.php?mod=purchase&con=PurchaseReceipt&act=index', 19, 27, 3, 1421292241, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(93, 98, 463, '损益单', 'WAREHOUSE_BILL_INFO_E', 'index.php?mod=warehouse&con=WarehouseBillInfoE&act=index', 20, 10, 9, 1421721993, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(94, 100, 469, '操作日志', 'APP_PROCESSOR_OPERATION', 'index.php?mod=processor&con=AppProcessorOperation&act=index', 5, 16, 6, 1421313674, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(95, 99, 468, '申请管理', 'APP_PROCESSOR_RECORD', 'index.php?mod=processor&con=AppProcessorRecord&act=index', 5, 33, 6, 1421313728, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(96, 101, 470, '商品信息', 'LIST_STYLE_GOODS', 'index.php?mod=style&con=ListStyleGoods&act=index', 4, 18, 4, 1421317153, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(97, 102, 471, '裸钻加价率', 'DIAMOND_JIAJIALV', 'index.php?mod=diamond&con=DiamondJiajialv&act=index', 25, 29, 10, 1421321136, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(98, 103, 477, '质检列表', 'PURCHASE_RECEIPT_DETAIL', 'index.php?mod=purchase&con=PurchaseReceiptDetail&act=index', 7, 17, 3, 1421561745, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(99, 104, 479, '配置管理', 'APP_SHOP_CONFIG', 'index.php?mod=diamond&con=AppShopConfig&act=index', 1, 29, 10, 1421563495, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(100, 105, 483, '财务收银', 'APP_ORDER_PAY_ACTION', 'index.php?mod=finance&con=AppOrderPayAction&act=index', 22, 28, 11, 1421572299, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(101, 106, 451, '退货返厂单', 'WAREHOUSE_BILL_INFO_B', 'index.php?mod=warehouse&con=WarehouseBillInfoB&act=add', 34, 10, 9, 1421219976, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(102, 107, 484, '其他出库单', 'WAREHOUSE_BILL_INFO_C', 'index.php?mod=warehouse&con=WarehouseBillInfoC&act=index', 8, 10, 9, 1421304926, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(103, 109, 490, '裸钻加价率日志', 'DIAMOND_INFO_LOG', 'index.php?mod=diamond&con=DiamondInfoLog&act=index', 31, 29, 10, 1421206468, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(104, 111, 495, '定金收据列表', 'APP_RECEIPT_DEPOSIT', 'index.php?mod=finance&con=AppReceiptDeposit&act=index', 16, 28, 11, 1421649586, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(105, 113, 499, '审批流程', 'APP_PROCESSOR_PROCESS', 'index.php?mod=processor&con=AppProcessorProcess&act=index', 19, 30, 6, 1421675821, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(106, 114, 503, '盘点', 'WAREHOUSEBILLINFOW', 'index.php?mod=warehouse&con=WarehouseBillInfoW&act=index', 186, 10, 9, 1421573514, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(107, 115, 504, '其他收货单', 'WAREHOUSEBILLINFOT', 'index.php?mod=warehouse&con=WarehouseBillInfoT&act=add', 6, 10, 9, 1421572591, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(108, 118, 515, '收货单', 'WAREHOUSEBILLINFOL', 'index.php?mod=warehouse&con=WarehouseBillInfoL&act=add', 205, 10, 9, 1421831248, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(109, 119, 516, '应付申请单', 'APP_PAY_APPLY', 'index.php?mod=finance&con=AppPayApply&act=index', 349, 31, 11, 1421838793, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(110, 120, 520, '结账账期管理', 'APP_JIEZHANG', 'index.php?mod=finance&con=AppJiezhang&act=index', 1, 34, 11, 1421839240, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(111, 121, 526, '点款收据列表', 'APP_RECEIPT_PAY', 'index.php?mod=finance&con=AppReceiptPay&act=index', 399, 28, 11, 1421892549, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(112, 122, 527, '收货明细', 'WARE_HOUSE_BILL_GOODS', 'index.php?mod=demo&con=WarehouseBillGoods&act=index', 8, 2, 9, 1421913052, 1, 0, 0);
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(113, 0, 0, '付款确认', 'APP_ORDER_PAY_ACTION_LIST', 'index.php?mod=finance&con=AppOrderPayActionList&act=index', 0, 40, 11, 0, 1, 0, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='菜单分组表' AUTO_INCREMENT=41 ;

--
-- 转存表中的数据 `menu_group`
--

INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(1, '系统配置', 1, 1, 7, 1, 0, 1);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(2, '开发示例', 2, 1, 2, 1, 0, 1);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(3, '用户管理', 1, 1, 5, 1, 0, 1);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(4, '权限管理', 1, 1, 4, 1, 0, 1);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(5, '回收站', 1, 1, 2, 1, 0, 1);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(6, '通用模块', 1, 1, 1, 1, 0, 1);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(7, '资源管理', 1, 1, 6, 1, 0, 1);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(8, '日志管理', 1, 1, 3, 1, 0, 1);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(9, '仓储配置', 9, 2, 1420605849, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(10, '仓储单据', 9, 4, 1420605860, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(11, '采购配置', 3, 18, 1420690148, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(12, '基本属性', 4, 148, 1420947858, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(13, '顾客管理', 5, 99, 1420620425, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(14, '货架管理', 9, 204, 1420622394, 1, 1, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(15, '柜位管理', 9, 0, 1420622796, 1, 1, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(16, '供应商信息', 6, 17, 1420623149, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(17, '采购信息', 3, 4, 1420617856, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(18, '款式管理', 4, 22, 1420692451, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(19, '会员中心', 5, 104, 1420704129, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(20, '预约管理', 5, 5, 1420771219, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(21, '订单管理', 7, 15, 1420856277, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(22, '销售政策', 8, 340, 1420943990, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(23, '配置管理', 4, 2, 1420619379, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(24, '供应商订单', 6, 8, 1420956526, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(25, '裸钻信息', 10, 4, 1421589956, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(26, '商品管理', 9, 7, 1421245736, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(27, '单据管理', 3, 2, 1421290973, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(28, '店面财务收银', 11, 37, 1421751576, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(29, '裸钻配置管理', 10, 265, 1421205190, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(30, '流程管理', 6, 340, 1421675763, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(31, '应收管理', 11, 372, 1421751527, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(32, '应付管理', 11, 375, 1421570039, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(33, '供应商申请', 6, 448, 1421824439, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(34, '配置管理', 11, 1, 1421838860, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(35, '定金收据列表', 11, 332, 1421935185, 1, 0, 0);
INSERT INTO `menu_group` (`id`, `label`, `application_id`, `icon`, `display_order`, `is_enabled`, `is_deleted`, `is_system`) VALUES(40, '付款确认', 11, 1, 1421990553, 1, 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `message`
--

CREATE TABLE IF NOT EXISTS `message` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `title` varchar(60) NOT NULL COMMENT '留言主题',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='留言表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `message`
--

INSERT INTO `message` (`id`, `title`) VALUES(1, '操蛋的生活');

-- --------------------------------------------------------

--
-- 表的结构 `message_reply`
--

CREATE TABLE IF NOT EXISTS `message_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `message_id` int(11) NOT NULL COMMENT '留言id',
  `title` varchar(30) NOT NULL COMMENT '回复主题',
  `content` varchar(200) NOT NULL COMMENT '回复内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `message_reply`
--

INSERT INTO `message_reply` (`id`, `message_id`, `title`, `content`) VALUES(1, 6, '王帅', '放到沙发上');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='操作表' AUTO_INCREMENT=289 ;

--
-- 转存表中的数据 `operation`
--

INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(1, 'index', '默认页', 1, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(2, 'search', '数据分页', 1, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(3, 'add', '添加', 1, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(4, 'edit', '编辑', 1, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(5, 'insert', '保存', 1, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(6, 'update', '更新', 1, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(7, 'delete', '删除', 1, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(8, 'index', '默认页', 2, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(9, 'search', '数据分页', 2, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(10, 'add', '添加', 2, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(11, 'edit', '编辑', 2, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(12, 'show', '详情', 2, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(13, 'insert', '保存', 2, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(14, 'update', '更新', 2, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(15, 'delete', '删除', 2, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(16, 'index', '默认页', 3, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(17, 'search', '数据分页', 3, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(18, 'add', '添加', 3, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(19, 'edit', '编辑', 3, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(20, 'insert', '保存', 3, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(21, 'update', '更新', 3, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(22, 'delete', '删除', 3, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(23, 'index', '默认页', 4, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(24, 'search', '数据分页', 4, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(25, 'add', '添加', 4, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(26, 'edit', '编辑', 4, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(27, 'modify', '重置密码', 4, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(28, 'insert', '保存', 4, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(29, 'update', '更新', 4, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(30, 'setModify', '更新密码', 4, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(31, 'setEnabled', '启用', 4, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(32, 'setDisabled', '禁用', 4, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(33, 'setLeave', '离职', 4, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(34, 'setOnWork', '入职', 4, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(35, 'delete', '删除', 4, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(36, 'show', '详情', 4, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(37, 'index', '默认页', 5, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(38, 'search', '数据分页', 5, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(39, 'index', '默认页', 6, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(40, 'search', '数据分页', 6, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(41, 'add', '添加', 6, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(42, 'edit', '编辑', 6, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(43, 'show', '详情', 6, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(44, 'insert', '保存', 6, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(45, 'update', '更新', 6, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(46, 'delete', '删除', 6, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(47, 'listAll', '排序', 6, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(48, 'saveSort', '保存排序', 6, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(49, 'search', '数据分页', 7, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(50, 'add', '添加', 7, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(51, 'edit', '编辑', 7, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(52, 'insert', '保存', 7, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(53, 'update', '更新', 7, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(54, 'delete', '删除', 7, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(55, 'ListMenuGroup', '排序', 7, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(56, 'saveSort', '保存排序', 7, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(57, 'index', '默认页', 8, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(58, 'search', '数据分页', 8, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(59, 'add', '添加', 8, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(60, 'edit', '编辑', 8, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(61, 'show', '详情', 8, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(62, 'insert', '保存', 8, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(63, 'update', '更新', 8, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(64, 'delete', '删除', 8, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(65, 'linkObj', '明细对象', 8, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(66, 'listButton', '列表按钮排序', 8, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(67, 'listButtons', '查看按钮排序', 8, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(68, 'saveSort', '保存排序', 8, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(69, 'index', '默认页', 9, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(70, 'search', '数据分页', 9, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(71, 'add', '添加', 9, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(72, 'edit', '编辑', 9, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(73, 'insert', '保存', 9, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(74, 'update', '更新', 9, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(75, 'delete', '删除', 9, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(76, 'index', '默认页', 10, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(77, 'search', '数据列表', 10, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(78, 'add', '添加', 10, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(79, 'edit', '编辑', 10, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(80, 'show', '详情', 10, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(81, 'insert', '保存', 10, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(82, 'update', '更新', 10, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(83, 'delete', '删除', 10, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(84, 'index', '默认页', 11, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(85, 'search', '数据分页', 11, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(86, 'add', '添加', 11, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(87, 'edit', '编辑', 11, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(88, 'insert', '保存', 11, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(89, 'update', '更新', 11, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(90, 'delete', '删除', 11, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(91, 'index', '默认页', 12, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(92, 'index', '默认页', 13, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(93, 'search', '数据分页', 13, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(94, 'add', '添加', 13, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(95, 'edit', '编辑', 13, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(96, 'insert', '保存', 13, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(97, 'update', '更新', 13, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(98, 'delete', '删除', 13, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(99, 'setLocked', '锁定', 13, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(100, 'index', '默认页', 14, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(101, 'search', '数据分页', 14, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(102, 'add', '添加', 14, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(103, 'edit', '编辑', 14, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(104, 'show', '详情', 14, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(105, 'insert', '保存', 14, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(106, 'update', '更新', 14, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(107, 'delete', '删除', 14, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(108, 'search', '数据分页', 15, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(109, 'add', '添加', 15, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(110, 'edit', '编辑', 15, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(111, 'insert', '保存', 15, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(112, 'update', '更新', 15, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(113, 'delete', '删除', 15, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(114, 'search', '数据分页', 16, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(115, 'add', '添加', 16, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(116, 'edit', '编辑', 16, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(117, 'insert', '保存', 16, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(118, 'update', '更新', 16, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(119, 'delete', '删除', 16, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(120, 'index', '默认页', 17, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(121, 'index', '默认页', 18, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(122, 'search', '数据分页', 18, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(123, 'add', '添加', 18, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(124, 'edit', '编辑', 18, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(125, 'show', '详情', 18, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(126, 'insert', '保存', 18, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(127, 'update', '更新', 18, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(128, 'delete', '禁用', 18, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(129, 'recover', '启用', 18, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(130, 'index', '默认页', 19, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(131, 'search', '数据列表', 19, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(132, 'add', '添加', 19, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(133, 'edit', '编辑', 19, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(134, 'show', '详情', 19, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(135, 'insert', '保存', 19, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(136, 'update', '更新', 19, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(137, 'delete', '删除', 19, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(138, 'search', '数据分页', 20, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(139, 'add', '添加', 20, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(140, 'edit', '编辑', 20, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(141, 'insert', '保存', 20, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(142, 'update', '更新', 20, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(143, 'delete', '删除', 20, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(144, 'index', '默认页', 21, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(145, 'search', '数据分页', 21, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(146, 'add', '添加', 21, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(147, 'edit', '编辑', 21, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(148, 'insert', '保存', 21, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(149, 'update', '更新', 21, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(150, 'delete', '删除', 21, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(151, 'index', '默认页', 22, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(152, 'search', '数据分页', 22, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(153, 'add', '添加', 22, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(154, 'edit', '编辑', 22, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(155, 'insert', '保存', 22, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(156, 'update', '更新', 22, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(157, 'delete', '删除', 22, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(158, 'index', '默认页', 23, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(159, 'search', '数据分页', 17, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(160, 'search', '数据分页', 23, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(161, 'index', '默认页', 24, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(162, 'search', '数据分页', 24, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(163, 'add', '添加', 24, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(164, 'insert', '添加', 24, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(165, 'delete', '删除', 24, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(166, 'index', '默认页', 25, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(167, 'search', '数据分页', 25, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(168, 'add', '添加', 25, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(169, 'insert', '保存', 25, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(170, 'delete', '删除', 25, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(171, 'index', '默认页', 26, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(172, 'search', '数据分页', 26, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(173, 'add', '添加', 26, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(174, 'insert', '保存', 26, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(175, 'delete', '删除', 26, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(176, 'index', '默认页', 27, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(177, 'index', '默认页', 28, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(178, 'index', '默认页', 29, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(179, 'search', '数据列表', 29, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(180, 'add', '添加', 29, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(181, 'edit', '编辑', 29, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(182, 'insert', '保存', 29, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(183, 'update', '更新', 29, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(184, 'delete', '删除', 29, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(185, 'getDoc', '下载', 29, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(186, 'index', '默认页', 30, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(187, 'search', '数据分页', 30, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(188, 'recover', '恢复', 30, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(189, 'index', '默认页', 31, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(190, 'search', '数据分页', 31, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(191, 'add', '添加', 31, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(192, 'edit', '编辑', 31, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(193, 'insert', '保存', 31, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(194, 'update', '更新', 31, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(195, 'delete', '删除', 31, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(196, 'index', '默认页', 32, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(197, 'search', '数据分页', 32, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(198, 'recover', '恢复', 32, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(199, 'index', '默认页', 33, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(200, 'search', '数据分页', 33, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(201, 'recover', '恢复', 33, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(202, 'index', '默认页', 34, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(203, 'search', '数据分页', 34, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(204, 'recover', '恢复', 34, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(205, 'index', '默认页', 35, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(206, 'search', '数据分页', 35, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(207, 'recover', '恢复', 35, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(208, 'index', '默认页', 36, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(209, 'search', '数据分页', 36, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(210, 'recover', '恢复', 36, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(211, 'search', '数据分页', 37, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(212, 'recover', '恢复', 37, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(213, 'search', '数据分页', 38, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(214, 'add', '添加', 38, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(215, 'edit', '编辑', 38, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(216, 'insert', '保存', 38, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(217, 'update', '更新', 38, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(218, 'delete', '禁用', 38, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(219, 'recover', '启用', 38, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(220, 'index', '默认页', 101, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(221, 'search', '数据分页', 101, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(222, 'add', '添加', 101, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(223, 'edit', '编辑', 101, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(224, 'insert', '保存', 101, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(225, 'update', '更新', 101, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(226, 'delete', '删除', 101, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(227, 'listAll', '排序', 101, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(228, 'saveSort', '保存排序', 101, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(229, 'index', '默认页', 102, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(230, 'search', '数据分页', 102, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(231, 'add', '添加', 102, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(232, 'edit', '编辑', 102, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(233, 'insert', '保存', 102, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(234, 'update', '更新', 102, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(235, 'delete', '删除', 102, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(236, 'listAll', '排序', 102, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(237, 'saveSort', '保存排序', 102, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(238, 'index', '默认页', 103, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(239, 'search', '数据分页', 103, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(240, 'add', '添加', 103, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(241, 'edit', '编辑', 103, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(242, 'insert', '保存', 103, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(243, 'update', '更新', 103, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(244, 'delete', '删除', 103, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(245, 'index', '默认页', 104, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(246, 'search', '数据分页', 104, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(247, 'add', '添加', 104, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(248, 'edit', '编辑', 104, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(249, 'show', '详情', 104, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(250, 'insert', '保存', 104, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(251, 'update', '更新', 104, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(252, 'delete', '删除', 104, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(253, 'index', '默认页', 105, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(254, 'search', '数据分页', 105, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(255, 'add', '添加', 105, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(256, 'insert', '保存', 105, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(257, 'delete', '删除', 105, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(258, 'index', '默认页', 106, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(259, 'search', '数据列表', 106, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(260, 'add', '添加', 106, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(261, 'edit', '编辑', 106, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(262, 'insert', '保存', 106, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(263, 'update', '更新', 106, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(264, 'delete', '删除', 106, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(265, 'index', '默认页', 107, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(266, 'search', '数据分页', 107, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(267, 'add', '添加', 107, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(268, 'insert', '保存', 107, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(269, 'delete', '删除', 107, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(270, 'index', '默认页', 108, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(271, 'search', '数据分页', 108, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(272, 'add', '添加', 108, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(273, 'edit', '编辑', 108, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(274, 'show', '详情', 108, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(275, 'insert', '保存', 108, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(276, 'update', '更新', 108, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(277, 'delete', '删除', 108, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(278, 'index', '默认页', 109, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(279, 'search', '数据分页', 109, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(280, 'add', '添加', 109, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(281, 'edit', '编辑', 109, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(282, 'insert', '保存', 109, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(283, 'update', '更新', 109, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(284, 'delete', '删除', 109, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(285, 'index', '默认页', 110, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(286, 'search', '数据分页', 110, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(287, 'recover', '恢复', 110, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(288, 'show', '详情', 13, 0, 0);

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
  `pay_name` varchar(20) NOT NULL COMMENT '支付方式中文名',
  `pay_code` varchar(20) NOT NULL COMMENT '支付方式拼音',
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
  `is_online` tinyint(1) unsigned DEFAULT '0' COMMENT '是否线上',
  `is_offline` tinyint(1) unsigned DEFAULT '0' COMMENT '是否线下',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='支付方式表' AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `payment`
--

INSERT INTO `payment` (`id`, `pay_name`, `pay_code`, `pay_fee`, `pay_desc`, `pay_order`, `pay_config`, `is_enabled`, `is_cod`, `is_display`, `is_web`, `addby_id`, `add_time`, `is_deleted`, `is_online`, `is_offline`) VALUES(1, '支付宝', 'ZHIFUBAO', '0.20', '', 0, '', 1, 0, 1, 1, NULL, 1421810192, 0, 1, 1);
INSERT INTO `payment` (`id`, `pay_name`, `pay_code`, `pay_fee`, `pay_desc`, `pay_order`, `pay_config`, `is_enabled`, `is_cod`, `is_display`, `is_web`, `addby_id`, `add_time`, `is_deleted`, `is_online`, `is_offline`) VALUES(2, '微支付', 'WEIZHIFU', '0.10', '', 0, '', 1, 0, 1, 1, NULL, 1421810977, 0, 1, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='权限表' AUTO_INCREMENT=518 ;

--
-- 转存表中的数据 `permission`
--

INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(1, 1, 1, '图标管理-菜单权限', 'BUTTON_ICON_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(2, 1, 2, '样式管理-菜单权限', 'BUTTON_CLASS_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(3, 1, 3, '事件管理-菜单权限', 'BUTTON_FUNCTION_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(4, 1, 4, '用户管理-菜单权限', 'USER_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(5, 1, 5, '访问日志-菜单权限', 'SYSTEM_ACCESS_LOG_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(6, 1, 6, '项目管理-菜单权限', 'APPLICATION_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(7, 1, 7, '控制器管理-菜单权限', 'CONTROL_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(8, 1, 8, '菜单管理-菜单权限', 'MENU_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(9, 1, 9, '部门管理-菜单权限', 'DEPARTMENT_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(10, 1, 10, '岗位设置-菜单权限', 'ORGANIZATION_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(11, 1, 11, '开发工具-菜单权限', 'DEVELOPER_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(12, 1, 12, '留言簿-菜单权限', 'GUEST_BOOK_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(13, 1, 13, '留言本-菜单权限', 'MESSAGE_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(14, 1, 14, '资源类型-菜单权限', 'RESOURCE_TYPE_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(15, 1, 15, '数据字典-菜单权限', 'DICT_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(16, 1, 16, '工作组-菜单权限', 'GROUP_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(17, 1, 17, '按钮管理-菜单权限', 'BUTTON_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(18, 1, 18, '角色管理-菜单权限', 'ROLE_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(19, 1, 19, '权限管理-菜单权限', 'PERMISSION_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(20, 1, 20, '组用户管理-菜单权限', 'GROUP_USER_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(21, 1, 21, '组角色管理-菜单权限', 'GROUP_ROLE_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(22, 1, 22, '角色用户-菜单权限', 'ROLE_USER_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(23, 1, 23, '角色授权-菜单权限', 'ROLE_PERMISSION_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(24, 1, 24, '用户授权-菜单权限', 'USER_PERMISSION_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(25, 1, 25, '支付方式-菜单权限', 'PAYMENT_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(26, 1, 26, '友情链接-菜单权限', 'FORUM_LINKS_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(27, 1, 27, '快递公司-菜单权限', 'EXPRESS_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(28, 1, 28, '分公司信息维护-菜单权限', 'COMPANY_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(29, 1, 29, '分公司归属-菜单权限', 'COMPANY_DEPARTMENT_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(30, 1, 30, '数据库配置-菜单权限', 'CONF_ITEM_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(31, 1, 31, '大区管理-菜单权限', 'LARGE_AREA_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(32, 1, 32, '用户回收站-菜单权限', 'USER_RECYCLE_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(33, 1, 33, '分公司归属一-菜单权限', 'DEPARTMENT_COMPANY_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(34, 1, 34, '体验店管理-菜单权限', 'SHOP_CFG_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(35, 1, 35, '销售渠道-菜单权限', 'SALES_CHANNELS_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(36, 1, 36, '地理信息-菜单权限', 'REGION_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(37, 1, 37, '项目回收站-菜单权限', 'APPLICATION_RECYCLE_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(38, 1, 38, '事件回收站-菜单权限', 'BUTTON_FUNCTION_RECYCLE_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(39, 1, 39, '按钮回收站-菜单权限', 'BUTTON_RECYCLE_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(40, 1, 40, '分公司回收站-菜单权限', 'COMPANY_RECYCLE_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(41, 1, 41, '控制器回收站-菜单权限', 'CONTROL_RECYCLE_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(42, 1, 42, '体验店回收站-菜单权限', 'SHOP_CFG_RECYCLE_M', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(43, 4, 1, '图标-数据权限', 'OBJ_BUTTON_ICON', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(44, 4, 2, '样式-数据权限', 'OBJ_BUTTON_CLASS', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(45, 4, 3, '事件-数据权限', 'OBJ_BUTTON_FUNCTION', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(46, 4, 4, '用户-数据权限', 'OBJ_USER', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(47, 4, 5, '访问日志-数据权限', 'OBJ_SYSTEM_ACCESS_LOG', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(48, 4, 6, '项目-数据权限', 'OBJ_APPLICATION', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(49, 4, 7, '菜单组-数据权限', 'OBJ_MENU_GROUP', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(50, 4, 8, '控制器-数据权限', 'OBJ_CONTROL', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(51, 4, 9, '菜单-数据权限', 'OBJ_MENU', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(52, 4, 10, '部门-数据权限', 'OBJ_DEPARTMENT', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(53, 4, 11, '岗位-数据权限', 'OBJ_ORGANIZATION', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(54, 4, 12, '开发工具-数据权限', 'OBJ_DEVELOPER', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(55, 4, 13, '留言簿-数据权限', 'OBJ_GUEST_BOOK', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(56, 4, 14, '留言本-数据权限', 'OBJ_MESSAGE', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(57, 4, 15, '留言回复-数据权限', 'OBJ_REPLY', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(58, 4, 16, '信息回复-数据权限', 'OBJ_MESSAGE_REPLY', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(59, 4, 17, '资源类型-数据权限', 'OBJ_RESOURCE_TYPE', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(60, 4, 18, '数据字典-数据权限', 'OBJ_DICT', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(61, 4, 19, '工作组-数据权限', 'OBJ_GROUP', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(62, 4, 20, '操作-数据权限', 'OBJ_OPERATION', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(63, 4, 21, '按钮-数据权限', 'OBJ_BUTTON', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(64, 4, 22, '角色-数据权限', 'OBJ_ROLE', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(65, 4, 23, '权限-数据权限', 'OBJ_PERMISSION', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(66, 4, 24, '组用户-数据权限', 'OBJ_GROUP_USER', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(67, 4, 25, '组角色-数据权限', 'OBJ_GROUP_ROLE', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(68, 4, 26, '角色用户-数据权限', 'OBJ_ROLE_USER', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(69, 4, 27, '角色授权-数据权限', 'OBJ_ROLE_PERMISSION', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(70, 4, 28, '用户授权-数据权限', 'OBJ_USER_PERMISSION', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(71, 4, 29, '数据库配置-数据权限', 'OBJ_CONF_ITEM', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(72, 4, 30, '用户回收站-数据权限', 'OBJ_USER_RECYCLE', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(73, 4, 31, '地理信息-数据权限', 'OBJ_REGION', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(74, 4, 32, '项目回收站-数据权限', 'OBJ_APPLICATION_RECYCLE', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(75, 4, 33, '事件回收站-数据权限', 'OBJ_BUTTON_FUNCTION_RECYCLE', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(76, 4, 34, '按钮回收站-数据权限', 'OBJ_BUTTON_RECYCLE', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(77, 4, 35, '分公司回收-数据权限', 'OBJ_COMPANY_RECYCLE', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(78, 4, 36, '控制器回收-数据权限', 'OBJ_CONTROL_RECYCLE', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(79, 4, 37, '已删操作-数据权限', 'OBJ_OPERATION_RECYCLE', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(80, 4, 38, '字典明细-数据权限', 'OBJ_DICT_ITEM', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(81, 4, 101, '支付方式-数据权限', 'OBJ_PAYMENT', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(82, 4, 102, '友情链接-数据权限', 'OBJ_FORUM_LINKS', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(83, 4, 103, '快递公司-数据权限', 'OBJ_EXPRESS', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(84, 4, 104, '分公司-数据权限', 'OBJ_COMPANY', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(85, 4, 105, '分公司归属-数据权限', 'OBJ_COMPANY_DEPARTMENT', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(86, 4, 106, '大区管理-数据权限', 'OBJ_LARGE_AREA', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(87, 4, 107, '分公司归属一-数据权限', 'OBJ_DEPARTMENT_COMPANY', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(88, 4, 108, '体验店-数据权限', 'OBJ_SHOP_CFG', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(89, 4, 109, '销售渠道-数据权限', 'OBJ_SALES_CHANNELS', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(90, 4, 110, '体验店回收站-数据权限', 'OBJ_SHOP_CFG_RECYCLE', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(91, 3, 1, '图标-默认页-操作权限', 'BUTTON_ICON_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(92, 3, 2, '图标-数据分页-操作权限', 'BUTTON_ICON_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(93, 3, 3, '图标-添加-操作权限', 'BUTTON_ICON_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(94, 3, 4, '图标-编辑-操作权限', 'BUTTON_ICON_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(95, 3, 5, '图标-保存-操作权限', 'BUTTON_ICON_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(96, 3, 6, '图标-更新-操作权限', 'BUTTON_ICON_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(97, 3, 7, '图标-删除-操作权限', 'BUTTON_ICON_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(98, 3, 8, '样式-默认页-操作权限', 'BUTTON_CLASS_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(99, 3, 9, '样式-数据分页-操作权限', 'BUTTON_CLASS_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(100, 3, 10, '样式-添加-操作权限', 'BUTTON_CLASS_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(101, 3, 11, '样式-编辑-操作权限', 'BUTTON_CLASS_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(102, 3, 12, '样式-详情-操作权限', 'BUTTON_CLASS_SHOW_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(103, 3, 13, '样式-保存-操作权限', 'BUTTON_CLASS_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(104, 3, 14, '样式-更新-操作权限', 'BUTTON_CLASS_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(105, 3, 15, '样式-删除-操作权限', 'BUTTON_CLASS_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(106, 3, 16, '事件-默认页-操作权限', 'BUTTON_FUNCTION_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(107, 3, 17, '事件-数据分页-操作权限', 'BUTTON_FUNCTION_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(108, 3, 18, '事件-添加-操作权限', 'BUTTON_FUNCTION_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(109, 3, 19, '事件-编辑-操作权限', 'BUTTON_FUNCTION_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(110, 3, 20, '事件-保存-操作权限', 'BUTTON_FUNCTION_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(111, 3, 21, '事件-更新-操作权限', 'BUTTON_FUNCTION_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(112, 3, 22, '事件-删除-操作权限', 'BUTTON_FUNCTION_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(113, 3, 23, '用户-默认页-操作权限', 'USER_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(114, 3, 24, '用户-数据分页-操作权限', 'USER_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(115, 3, 25, '用户-添加-操作权限', 'USER_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(116, 3, 26, '用户-编辑-操作权限', 'USER_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(117, 3, 27, '用户-重置密码-操作权限', 'USER_MODIFY_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(118, 3, 28, '用户-保存-操作权限', 'USER_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(119, 3, 29, '用户-更新-操作权限', 'USER_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(120, 3, 30, '用户-更新密码-操作权限', 'USER_SET_MODIFY_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(121, 3, 31, '用户-启用-操作权限', 'USER_SET_ENABLED_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(122, 3, 32, '用户-禁用-操作权限', 'USER_SET_DISABLED_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(123, 3, 33, '用户-离职-操作权限', 'USER_SET_LEAVE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(124, 3, 34, '用户-入职-操作权限', 'USER_SET_ON_WORK_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(125, 3, 35, '用户-删除-操作权限', 'USER_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(126, 3, 36, '用户-详情-操作权限', 'USER_SHOW_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(127, 3, 37, '访问日志-默认页-操作权限', 'SYSTEM_ACCESS_LOG_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(128, 3, 38, '访问日志-数据分页-操作权限', 'SYSTEM_ACCESS_LOG_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(129, 3, 39, '项目-默认页-操作权限', 'APPLICATION_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(130, 3, 40, '项目-数据分页-操作权限', 'APPLICATION_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(131, 3, 41, '项目-添加-操作权限', 'APPLICATION_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(132, 3, 42, '项目-编辑-操作权限', 'APPLICATION_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(133, 3, 43, '项目-详情-操作权限', 'APPLICATION_SHOW_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(134, 3, 44, '项目-保存-操作权限', 'APPLICATION_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(135, 3, 45, '项目-更新-操作权限', 'APPLICATION_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(136, 3, 46, '项目-删除-操作权限', 'APPLICATION_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(137, 3, 47, '项目-排序-操作权限', 'APPLICATION_LIST_ALL_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(138, 3, 48, '项目-保存排序-操作权限', 'APPLICATION_SAVE_SORT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(139, 3, 49, '菜单组-数据分页-操作权限', 'MENU_GROUP_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(140, 3, 50, '菜单组-添加-操作权限', 'MENU_GROUP_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(141, 3, 51, '菜单组-编辑-操作权限', 'MENU_GROUP_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(142, 3, 52, '菜单组-保存-操作权限', 'MENU_GROUP_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(143, 3, 53, '菜单组-更新-操作权限', 'MENU_GROUP_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(144, 3, 54, '菜单组-删除-操作权限', 'MENU_GROUP_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(145, 3, 55, '菜单组-排序-操作权限', 'MENU_GROUP_LIST_MENU_GROUP_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(146, 3, 56, '菜单组-保存排序-操作权限', 'MENU_GROUP_SAVE_SORT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(147, 3, 57, '控制器-默认页-操作权限', 'CONTROL_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(148, 3, 58, '控制器-数据分页-操作权限', 'CONTROL_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(149, 3, 59, '控制器-添加-操作权限', 'CONTROL_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(150, 3, 60, '控制器-编辑-操作权限', 'CONTROL_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(151, 3, 61, '控制器-详情-操作权限', 'CONTROL_SHOW_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(152, 3, 62, '控制器-保存-操作权限', 'CONTROL_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(153, 3, 63, '控制器-更新-操作权限', 'CONTROL_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(154, 3, 64, '控制器-删除-操作权限', 'CONTROL_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(155, 3, 65, '控制器-明细对象-操作权限', 'CONTROL_LINK_OBJ_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(156, 3, 66, '控制器-列表按钮排序-操作权限', 'CONTROL_LIST_BUTTON_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(157, 3, 67, '控制器-查看按钮排序-操作权限', 'CONTROL_LIST_BUTTONS_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(158, 3, 68, '控制器-保存排序-操作权限', 'CONTROL_SAVE_SORT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(159, 3, 69, '菜单-默认页-操作权限', 'MENU_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(160, 3, 70, '菜单-数据分页-操作权限', 'MENU_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(161, 3, 71, '菜单-添加-操作权限', 'MENU_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(162, 3, 72, '菜单-编辑-操作权限', 'MENU_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(163, 3, 73, '菜单-保存-操作权限', 'MENU_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(164, 3, 74, '菜单-更新-操作权限', 'MENU_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(165, 3, 75, '菜单-删除-操作权限', 'MENU_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(166, 3, 76, '部门-默认页-操作权限', 'DEPARTMENT_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(167, 3, 77, '部门-数据列表-操作权限', 'DEPARTMENT_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(168, 3, 78, '部门-添加-操作权限', 'DEPARTMENT_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(169, 3, 79, '部门-编辑-操作权限', 'DEPARTMENT_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(170, 3, 80, '部门-详情-操作权限', 'DEPARTMENT_SHOW_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(171, 3, 81, '部门-保存-操作权限', 'DEPARTMENT_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(172, 3, 82, '部门-更新-操作权限', 'DEPARTMENT_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(173, 3, 83, '部门-删除-操作权限', 'DEPARTMENT_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(174, 3, 84, '岗位-默认页-操作权限', 'ORGANIZATION_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(175, 3, 85, '岗位-数据分页-操作权限', 'ORGANIZATION_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(176, 3, 86, '岗位-添加-操作权限', 'ORGANIZATION_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(177, 3, 87, '岗位-编辑-操作权限', 'ORGANIZATION_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(178, 3, 88, '岗位-保存-操作权限', 'ORGANIZATION_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(179, 3, 89, '岗位-更新-操作权限', 'ORGANIZATION_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(180, 3, 90, '岗位-删除-操作权限', 'ORGANIZATION_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(181, 3, 91, '开发工具-默认页-操作权限', 'DEVELOPER_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(182, 3, 92, '留言簿-默认页-操作权限', 'GUEST_BOOK_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(183, 3, 93, '留言簿-数据分页-操作权限', 'GUEST_BOOK_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(184, 3, 94, '留言簿-添加-操作权限', 'GUEST_BOOK_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(185, 3, 95, '留言簿-编辑-操作权限', 'GUEST_BOOK_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(186, 3, 96, '留言簿-保存-操作权限', 'GUEST_BOOK_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(187, 3, 97, '留言簿-更新-操作权限', 'GUEST_BOOK_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(188, 3, 98, '留言簿-删除-操作权限', 'GUEST_BOOK_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(189, 3, 99, '留言簿-锁定-操作权限', 'GUEST_BOOK_SET_LOCKED_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(190, 3, 100, '留言本-默认页-操作权限', 'MESSAGE_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(191, 3, 101, '留言本-数据分页-操作权限', 'MESSAGE_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(192, 3, 102, '留言本-添加-操作权限', 'MESSAGE_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(193, 3, 103, '留言本-编辑-操作权限', 'MESSAGE_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(194, 3, 104, '留言本-详情-操作权限', 'MESSAGE_SHOW_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(195, 3, 105, '留言本-保存-操作权限', 'MESSAGE_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(196, 3, 106, '留言本-更新-操作权限', 'MESSAGE_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(197, 3, 107, '留言本-删除-操作权限', 'MESSAGE_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(198, 3, 108, '留言回复-数据分页-操作权限', 'REPLY_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(199, 3, 109, '留言回复-添加-操作权限', 'REPLY_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(200, 3, 110, '留言回复-编辑-操作权限', 'REPLY_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(201, 3, 111, '留言回复-保存-操作权限', 'REPLY_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(202, 3, 112, '留言回复-更新-操作权限', 'REPLY_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(203, 3, 113, '留言回复-删除-操作权限', 'REPLY_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(204, 3, 114, '信息回复-数据分页-操作权限', 'MESSAGE_REPLY_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(205, 3, 115, '信息回复-添加-操作权限', 'MESSAGE_REPLY_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(206, 3, 116, '信息回复-编辑-操作权限', 'MESSAGE_REPLY_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(207, 3, 117, '信息回复-保存-操作权限', 'MESSAGE_REPLY_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(208, 3, 118, '信息回复-更新-操作权限', 'MESSAGE_REPLY_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(209, 3, 119, '信息回复-删除-操作权限', 'MESSAGE_REPLY_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(210, 3, 120, '资源类型-默认页-操作权限', 'RESOURCE_TYPE_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(211, 3, 159, '资源类型-数据分页-操作权限', 'RESOURCE_TYPE_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(212, 3, 121, '数据字典-默认页-操作权限', 'DICT_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(213, 3, 122, '数据字典-数据分页-操作权限', 'DICT_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(214, 3, 123, '数据字典-添加-操作权限', 'DICT_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(215, 3, 124, '数据字典-编辑-操作权限', 'DICT_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(216, 3, 125, '数据字典-详情-操作权限', 'DICT_SHOW_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(217, 3, 126, '数据字典-保存-操作权限', 'DICT_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(218, 3, 127, '数据字典-更新-操作权限', 'DICT_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(219, 3, 128, '数据字典-禁用-操作权限', 'DICT_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(220, 3, 129, '数据字典-启用-操作权限', 'DICT_RECOVER_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(221, 3, 130, '工作组-默认页-操作权限', 'GROUP_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(222, 3, 131, '工作组-数据列表-操作权限', 'GROUP_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(223, 3, 132, '工作组-添加-操作权限', 'GROUP_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(224, 3, 133, '工作组-编辑-操作权限', 'GROUP_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(225, 3, 134, '工作组-详情-操作权限', 'GROUP_SHOW_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(226, 3, 135, '工作组-保存-操作权限', 'GROUP_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(227, 3, 136, '工作组-更新-操作权限', 'GROUP_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(228, 3, 137, '工作组-删除-操作权限', 'GROUP_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(229, 3, 138, '操作-数据分页-操作权限', 'OPERATION_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(230, 3, 139, '操作-添加-操作权限', 'OPERATION_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(231, 3, 140, '操作-编辑-操作权限', 'OPERATION_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(232, 3, 141, '操作-保存-操作权限', 'OPERATION_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(233, 3, 142, '操作-更新-操作权限', 'OPERATION_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(234, 3, 143, '操作-删除-操作权限', 'OPERATION_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(235, 3, 144, '按钮-默认页-操作权限', 'BUTTON_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(236, 3, 145, '按钮-数据分页-操作权限', 'BUTTON_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(237, 3, 146, '按钮-添加-操作权限', 'BUTTON_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(238, 3, 147, '按钮-编辑-操作权限', 'BUTTON_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(239, 3, 148, '按钮-保存-操作权限', 'BUTTON_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(240, 3, 149, '按钮-更新-操作权限', 'BUTTON_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(241, 3, 150, '按钮-删除-操作权限', 'BUTTON_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(242, 3, 151, '角色-默认页-操作权限', 'ROLE_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(243, 3, 152, '角色-数据分页-操作权限', 'ROLE_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(244, 3, 153, '角色-添加-操作权限', 'ROLE_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(245, 3, 154, '角色-编辑-操作权限', 'ROLE_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(246, 3, 155, '角色-保存-操作权限', 'ROLE_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(247, 3, 156, '角色-更新-操作权限', 'ROLE_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(248, 3, 157, '角色-删除-操作权限', 'ROLE_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(249, 3, 158, '权限-默认页-操作权限', 'PERMISSION_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(250, 3, 160, '权限-数据分页-操作权限', 'PERMISSION_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(251, 3, 161, '组用户-默认页-操作权限', 'GROUP_USER_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(252, 3, 162, '组用户-数据分页-操作权限', 'GROUP_USER_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(253, 3, 163, '组用户-添加-操作权限', 'GROUP_USER_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(254, 3, 164, '组用户-添加-操作权限', 'GROUP_USER_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(255, 3, 165, '组用户-删除-操作权限', 'GROUP_USER_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(256, 3, 166, '组角色-默认页-操作权限', 'GROUP_ROLE_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(257, 3, 167, '组角色-数据分页-操作权限', 'GROUP_ROLE_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(258, 3, 168, '组角色-添加-操作权限', 'GROUP_ROLE_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(259, 3, 169, '组角色-保存-操作权限', 'GROUP_ROLE_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(260, 3, 170, '组角色-删除-操作权限', 'GROUP_ROLE_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(261, 3, 171, '角色用户-默认页-操作权限', 'ROLE_USER_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(262, 3, 172, '角色用户-数据分页-操作权限', 'ROLE_USER_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(263, 3, 173, '角色用户-添加-操作权限', 'ROLE_USER_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(264, 3, 174, '角色用户-保存-操作权限', 'ROLE_USER_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(265, 3, 175, '角色用户-删除-操作权限', 'ROLE_USER_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(266, 3, 176, '角色授权-默认页-操作权限', 'ROLE_PERMISSION_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(267, 3, 177, '用户授权-默认页-操作权限', 'USER_PERMISSION_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(268, 3, 178, '数据库配置-默认页-操作权限', 'CONF_ITEM_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(269, 3, 179, '数据库配置-数据列表-操作权限', 'CONF_ITEM_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(270, 3, 180, '数据库配置-添加-操作权限', 'CONF_ITEM_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(271, 3, 181, '数据库配置-编辑-操作权限', 'CONF_ITEM_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(272, 3, 182, '数据库配置-保存-操作权限', 'CONF_ITEM_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(273, 3, 183, '数据库配置-更新-操作权限', 'CONF_ITEM_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(274, 3, 184, '数据库配置-删除-操作权限', 'CONF_ITEM_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(275, 3, 185, '数据库配置-下载-操作权限', 'CONF_ITEM_GET_DOC_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(276, 3, 186, '用户回收站-默认页-操作权限', 'USER_RECYCLE_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(277, 3, 187, '用户回收站-数据分页-操作权限', 'USER_RECYCLE_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(278, 3, 188, '用户回收站-恢复-操作权限', 'USER_RECYCLE_RECOVER_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(279, 3, 189, '地理信息-默认页-操作权限', 'REGION_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(280, 3, 190, '地理信息-数据分页-操作权限', 'REGION_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(281, 3, 191, '地理信息-添加-操作权限', 'REGION_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(282, 3, 192, '地理信息-编辑-操作权限', 'REGION_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(283, 3, 193, '地理信息-保存-操作权限', 'REGION_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(284, 3, 194, '地理信息-更新-操作权限', 'REGION_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(285, 3, 195, '地理信息-删除-操作权限', 'REGION_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(286, 3, 196, '项目回收站-默认页-操作权限', 'APPLICATION_RECYCLE_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(287, 3, 197, '项目回收站-数据分页-操作权限', 'APPLICATION_RECYCLE_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(288, 3, 198, '项目回收站-恢复-操作权限', 'APPLICATION_RECYCLE_RECOVER_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(289, 3, 199, '事件回收站-默认页-操作权限', 'BUTTON_FUNCTION_RECYCLE_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(290, 3, 200, '事件回收站-数据分页-操作权限', 'BUTTON_FUNCTION_RECYCLE_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(291, 3, 201, '事件回收站-恢复-操作权限', 'BUTTON_FUNCTION_RECYCLE_RECOVER_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(292, 3, 202, '按钮回收站-默认页-操作权限', 'BUTTON_RECYCLE_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(293, 3, 203, '按钮回收站-数据分页-操作权限', 'BUTTON_RECYCLE_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(294, 3, 204, '按钮回收站-恢复-操作权限', 'BUTTON_RECYCLE_RECOVER_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(295, 3, 205, '分公司回收-默认页-操作权限', 'COMPANY_RECYCLE_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(296, 3, 206, '分公司回收-数据分页-操作权限', 'COMPANY_RECYCLE_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(297, 3, 207, '分公司回收-恢复-操作权限', 'COMPANY_RECYCLE_RECOVER_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(298, 3, 208, '控制器回收-默认页-操作权限', 'CONTROL_RECYCLE_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(299, 3, 209, '控制器回收-数据分页-操作权限', 'CONTROL_RECYCLE_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(300, 3, 210, '控制器回收-恢复-操作权限', 'CONTROL_RECYCLE_RECOVER_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(301, 3, 211, '已删操作-数据分页-操作权限', 'OPERATION_RECYCLE_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(302, 3, 212, '已删操作-恢复-操作权限', 'OPERATION_RECYCLE_RECOVER_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(303, 3, 213, '字典明细-数据分页-操作权限', 'DICT_ITEM_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(304, 3, 214, '字典明细-添加-操作权限', 'DICT_ITEM_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(305, 3, 215, '字典明细-编辑-操作权限', 'DICT_ITEM_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(306, 3, 216, '字典明细-保存-操作权限', 'DICT_ITEM_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(307, 3, 217, '字典明细-更新-操作权限', 'DICT_ITEM_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(308, 3, 218, '字典明细-禁用-操作权限', 'DICT_ITEM_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(309, 3, 219, '字典明细-启用-操作权限', 'DICT_ITEM_RECOVER_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(310, 3, 220, '支付方式-默认页-操作权限', 'PAYMENT_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(311, 3, 221, '支付方式-数据分页-操作权限', 'PAYMENT_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(312, 3, 222, '支付方式-添加-操作权限', 'PAYMENT_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(313, 3, 223, '支付方式-编辑-操作权限', 'PAYMENT_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(314, 3, 224, '支付方式-保存-操作权限', 'PAYMENT_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(315, 3, 225, '支付方式-更新-操作权限', 'PAYMENT_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(316, 3, 226, '支付方式-删除-操作权限', 'PAYMENT_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(317, 3, 227, '支付方式-排序-操作权限', 'PAYMENT_LIST_ALL_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(318, 3, 228, '支付方式-保存排序-操作权限', 'PAYMENT_SAVE_SORT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(319, 3, 229, '友情链接-默认页-操作权限', 'FORUM_LINKS_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(320, 3, 230, '友情链接-数据分页-操作权限', 'FORUM_LINKS_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(321, 3, 231, '友情链接-添加-操作权限', 'FORUM_LINKS_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(322, 3, 232, '友情链接-编辑-操作权限', 'FORUM_LINKS_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(323, 3, 233, '友情链接-保存-操作权限', 'FORUM_LINKS_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(324, 3, 234, '友情链接-更新-操作权限', 'FORUM_LINKS_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(325, 3, 235, '友情链接-删除-操作权限', 'FORUM_LINKS_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(326, 3, 236, '友情链接-排序-操作权限', 'FORUM_LINKS_LIST_ALL_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(327, 3, 237, '友情链接-保存排序-操作权限', 'FORUM_LINKS_SAVE_SORT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(328, 3, 238, '快递公司-默认页-操作权限', 'EXPRESS_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(329, 3, 239, '快递公司-数据分页-操作权限', 'EXPRESS_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(330, 3, 240, '快递公司-添加-操作权限', 'EXPRESS_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(331, 3, 241, '快递公司-编辑-操作权限', 'EXPRESS_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(332, 3, 242, '快递公司-保存-操作权限', 'EXPRESS_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(333, 3, 243, '快递公司-更新-操作权限', 'EXPRESS_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(334, 3, 244, '快递公司-删除-操作权限', 'EXPRESS_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(335, 3, 245, '分公司-默认页-操作权限', 'COMPANY_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(336, 3, 246, '分公司-数据分页-操作权限', 'COMPANY_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(337, 3, 247, '分公司-添加-操作权限', 'COMPANY_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(338, 3, 248, '分公司-编辑-操作权限', 'COMPANY_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(339, 3, 249, '分公司-详情-操作权限', 'COMPANY_SHOW_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(340, 3, 250, '分公司-保存-操作权限', 'COMPANY_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(341, 3, 251, '分公司-更新-操作权限', 'COMPANY_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(342, 3, 252, '分公司-删除-操作权限', 'COMPANY_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(343, 3, 253, '分公司归属-默认页-操作权限', 'COMPANY_DEPARTMENT_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(344, 3, 254, '分公司归属-数据分页-操作权限', 'COMPANY_DEPARTMENT_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(345, 3, 255, '分公司归属-添加-操作权限', 'COMPANY_DEPARTMENT_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(346, 3, 256, '分公司归属-保存-操作权限', 'COMPANY_DEPARTMENT_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(347, 3, 257, '分公司归属-删除-操作权限', 'COMPANY_DEPARTMENT_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(348, 3, 258, '大区管理-默认页-操作权限', 'LARGE_AREA_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(349, 3, 259, '大区管理-数据列表-操作权限', 'LARGE_AREA_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(350, 3, 260, '大区管理-添加-操作权限', 'LARGE_AREA_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(351, 3, 261, '大区管理-编辑-操作权限', 'LARGE_AREA_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(352, 3, 262, '大区管理-保存-操作权限', 'LARGE_AREA_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(353, 3, 263, '大区管理-更新-操作权限', 'LARGE_AREA_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(354, 3, 264, '大区管理-删除-操作权限', 'LARGE_AREA_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(355, 3, 265, '分公司归属一-默认页-操作权限', 'DEPARTMENT_COMPANY_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(356, 3, 266, '分公司归属一-数据分页-操作权限', 'DEPARTMENT_COMPANY_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(357, 3, 267, '分公司归属一-添加-操作权限', 'DEPARTMENT_COMPANY_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(358, 3, 268, '分公司归属一-保存-操作权限', 'DEPARTMENT_COMPANY_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(359, 3, 269, '分公司归属一-删除-操作权限', 'DEPARTMENT_COMPANY_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(360, 3, 270, '体验店-默认页-操作权限', 'SHOP_CFG_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(361, 3, 271, '体验店-数据分页-操作权限', 'SHOP_CFG_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(362, 3, 272, '体验店-添加-操作权限', 'SHOP_CFG_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(363, 3, 273, '体验店-编辑-操作权限', 'SHOP_CFG_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(364, 3, 274, '体验店-详情-操作权限', 'SHOP_CFG_SHOW_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(365, 3, 275, '体验店-保存-操作权限', 'SHOP_CFG_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(366, 3, 276, '体验店-更新-操作权限', 'SHOP_CFG_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(367, 3, 277, '体验店-删除-操作权限', 'SHOP_CFG_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(368, 3, 278, '销售渠道-默认页-操作权限', 'SALES_CHANNELS_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(369, 3, 279, '销售渠道-数据分页-操作权限', 'SALES_CHANNELS_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(370, 3, 280, '销售渠道-添加-操作权限', 'SALES_CHANNELS_ADD_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(371, 3, 281, '销售渠道-编辑-操作权限', 'SALES_CHANNELS_EDIT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(372, 3, 282, '销售渠道-保存-操作权限', 'SALES_CHANNELS_INSERT_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(373, 3, 283, '销售渠道-更新-操作权限', 'SALES_CHANNELS_UPDATE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(374, 3, 284, '销售渠道-删除-操作权限', 'SALES_CHANNELS_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(375, 3, 285, '体验店回收站-默认页-操作权限', 'SHOP_CFG_RECYCLE_INDEX_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(376, 3, 286, '体验店回收站-数据分页-操作权限', 'SHOP_CFG_RECYCLE_SEARCH_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(377, 3, 287, '体验店回收站-恢复-操作权限', 'SHOP_CFG_RECYCLE_RECOVER_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(378, 2, 1, '-同步-按钮权限', 'BUTTON1', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(379, 2, 2, '-重置-按钮权限', 'BUTTON2', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(380, 2, 3, '-离开-按钮权限', 'BUTTON3', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(381, 2, 4, '-刷新-按钮权限', 'BUTTON4', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(382, 2, 101, '图标-添加-按钮权限', 'BUTTON101', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(383, 2, 102, '图标-编辑-按钮权限', 'BUTTON102', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(384, 2, 103, '图标-删除-按钮权限', 'BUTTON103', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(385, 2, 104, '样式-添加-按钮权限', 'BUTTON104', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(386, 2, 105, '样式-编辑-按钮权限', 'BUTTON105', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(387, 2, 106, '样式-删除-按钮权限', 'BUTTON106', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(388, 2, 107, '事件-添加-按钮权限', 'BUTTON107', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(389, 2, 108, '事件-编辑-按钮权限', 'BUTTON108', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(390, 2, 109, '事件-删除-按钮权限', 'BUTTON109', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(391, 2, 110, '用户-添加-按钮权限', 'BUTTON110', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(392, 2, 111, '用户-编辑-按钮权限', 'BUTTON111', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(393, 2, 112, '用户-删除-按钮权限', 'BUTTON112', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(394, 2, 113, '用户-详情-按钮权限', 'BUTTON113', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(395, 2, 114, '用户-重置密码-按钮权限', 'BUTTON114', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(396, 2, 115, '用户-启用-按钮权限', 'BUTTON115', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(397, 2, 116, '用户-停用-按钮权限', 'BUTTON116', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(398, 2, 117, '用户-入职-按钮权限', 'BUTTON117', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(399, 2, 118, '用户-离职-按钮权限', 'BUTTON118', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(400, 2, 119, '项目-添加-按钮权限', 'BUTTON119', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(401, 2, 120, '项目-编辑-按钮权限', 'BUTTON120', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(402, 2, 121, '项目-删除-按钮权限', 'BUTTON121', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(403, 2, 122, '项目-详情-按钮权限', 'BUTTON122', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(404, 2, 123, '菜单组-添加-按钮权限', 'BUTTON123', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(405, 2, 124, '菜单组-编辑-按钮权限', 'BUTTON124', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(406, 2, 125, '菜单组-删除-按钮权限', 'BUTTON125', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(407, 2, 126, '菜单组-排序-按钮权限', 'BUTTON126', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(408, 2, 127, '控制器-添加-按钮权限', 'BUTTON127', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(409, 2, 128, '控制器-编辑-按钮权限', 'BUTTON128', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(410, 2, 129, '控制器-删除-按钮权限', 'BUTTON129', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(411, 2, 130, '控制器-详情-按钮权限', 'BUTTON130', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(412, 2, 131, '控制器-列表按钮排序-按钮权限', 'BUTTON131', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(413, 2, 132, '控制器-详情页按钮排序-按钮权限', 'BUTTON132', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(414, 2, 133, '控制器-关联明细查看-按钮权限', 'BUTTON133', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(415, 2, 134, '菜单-添加-按钮权限', 'BUTTON134', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(416, 2, 135, '菜单-编辑-按钮权限', 'BUTTON135', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(417, 2, 136, '菜单-删除-按钮权限', 'BUTTON136', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(418, 2, 137, '菜单组-刷新-按钮权限', 'BUTTON137', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(419, 2, 138, '部门-添加-按钮权限', 'BUTTON138', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(420, 2, 139, '部门-编辑-按钮权限', 'BUTTON139', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(421, 2, 140, '部门-删除-按钮权限', 'BUTTON140', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(422, 2, 141, '部门-详情-按钮权限', 'BUTTON141', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(423, 2, 142, '岗位-添加-按钮权限', 'BUTTON142', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(424, 2, 143, '岗位-编辑-按钮权限', 'BUTTON143', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(425, 2, 144, '岗位-删除-按钮权限', 'BUTTON144', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(426, 2, 145, '留言簿-添加-按钮权限', 'BUTTON145', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(427, 2, 146, '留言簿-编辑-按钮权限', 'BUTTON146', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(428, 2, 147, '留言簿-删除-按钮权限', 'BUTTON147', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(429, 3, 288, '留言簿-详情-操作权限', 'GUEST_BOOK_SHOW_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(430, 2, 148, '留言簿-详情-按钮权限', 'BUTTON148', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(431, 2, 149, '留言簿-锁定-按钮权限', 'BUTTON149', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(432, 2, 150, '留言本-添加-按钮权限', 'BUTTON150', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(433, 2, 151, '留言本-编辑-按钮权限', 'BUTTON151', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(434, 2, 152, '留言本-删除-按钮权限', 'BUTTON152', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(435, 2, 153, '留言本-详情-按钮权限', 'BUTTON153', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(436, 2, 154, '留言回复-添加-按钮权限', 'BUTTON154', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(437, 2, 155, '留言回复-编辑-按钮权限', 'BUTTON155', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(438, 2, 156, '留言回复-删除-按钮权限', 'BUTTON156', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(439, 2, 157, '留言回复-刷新-按钮权限', 'BUTTON157', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(440, 2, 158, '信息回复-添加-按钮权限', 'BUTTON158', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(441, 2, 159, '信息回复-编辑-按钮权限', 'BUTTON159', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(442, 2, 160, '信息回复-删除-按钮权限', 'BUTTON160', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(443, 2, 161, '信息回复-刷新-按钮权限', 'BUTTON161', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(444, 2, 162, '数据字典-添加-按钮权限', 'BUTTON162', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(445, 2, 163, '数据字典-编辑-按钮权限', 'BUTTON163', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(446, 2, 164, '数据字典-禁用-按钮权限', 'BUTTON164', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(447, 2, 165, '数据字典-启用-按钮权限', 'BUTTON165', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(448, 2, 166, '数据字典-详情-按钮权限', 'BUTTON166', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(449, 2, 167, '工作组-添加-按钮权限', 'BUTTON167', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(450, 2, 168, '工作组-编辑-按钮权限', 'BUTTON168', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(451, 2, 169, '工作组-删除-按钮权限', 'BUTTON169', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(452, 2, 170, '工作组-详情-按钮权限', 'BUTTON170', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(453, 2, 171, '操作-添加-按钮权限', 'BUTTON171', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(454, 2, 172, '操作-编辑-按钮权限', 'BUTTON172', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(455, 2, 173, '操作-删除-按钮权限', 'BUTTON173', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(456, 2, 174, '操作-刷新-按钮权限', 'BUTTON174', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(457, 2, 175, '按钮-添加-按钮权限', 'BUTTON175', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(458, 2, 176, '按钮-编辑-按钮权限', 'BUTTON176', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(459, 2, 177, '按钮-删除-按钮权限', 'BUTTON177', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(460, 2, 178, '角色-添加-按钮权限', 'BUTTON178', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(461, 2, 179, '角色-编辑-按钮权限', 'BUTTON179', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(462, 2, 180, '角色-删除-按钮权限', 'BUTTON180', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(463, 2, 181, '组用户-添加-按钮权限', 'BUTTON181', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(464, 2, 182, '组用户-删除-按钮权限', 'BUTTON182', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(465, 2, 183, '组角色-添加-按钮权限', 'BUTTON183', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(466, 2, 184, '组角色-删除-按钮权限', 'BUTTON184', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(467, 2, 185, '角色用户-添加-按钮权限', 'BUTTON185', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(468, 2, 186, '角色用户-删除-按钮权限', 'BUTTON186', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(469, 2, 187, '数据库配置-添加-按钮权限', 'BUTTON187', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(470, 2, 188, '数据库配置-编辑-按钮权限', 'BUTTON188', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(471, 2, 189, '数据库配置-删除-按钮权限', 'BUTTON189', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(472, 2, 190, '数据库配置-下载-按钮权限', 'BUTTON190', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(473, 2, 191, '用户回收站-恢复-按钮权限', 'BUTTON191', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(474, 2, 192, '地理信息-添加-按钮权限', 'BUTTON192', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(475, 2, 193, '地理信息-编辑-按钮权限', 'BUTTON193', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(476, 2, 194, '地理信息-删除-按钮权限', 'BUTTON194', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(477, 2, 195, '项目回收站-恢复-按钮权限', 'BUTTON195', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(478, 2, 196, '事件回收站-恢复-按钮权限', 'BUTTON196', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(479, 2, 197, '按钮回收站-恢复-按钮权限', 'BUTTON197', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(480, 2, 198, '分公司回收-恢复-按钮权限', 'BUTTON198', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(481, 2, 199, '控制器回收-恢复-按钮权限', 'BUTTON199', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(482, 2, 200, '已删操作-恢复-按钮权限', 'BUTTON200', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(483, 2, 201, '已删操作-刷新-按钮权限', 'BUTTON201', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(484, 2, 202, '字典明细-添加-按钮权限', 'BUTTON202', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(485, 2, 203, '字典明细-编辑-按钮权限', 'BUTTON203', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(486, 2, 204, '字典明细-禁用-按钮权限', 'BUTTON204', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(487, 2, 205, '字典明细-启用-按钮权限', 'BUTTON205', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(488, 2, 206, '字典明细-刷新-按钮权限', 'BUTTON206', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(489, 2, 207, '支付方式-添加-按钮权限', 'BUTTON207', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(490, 2, 208, '支付方式-编辑-按钮权限', 'BUTTON208', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(491, 2, 209, '支付方式-删除-按钮权限', 'BUTTON209', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(492, 2, 210, '支付方式-排序-按钮权限', 'BUTTON210', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(493, 2, 211, '友情链接-添加-按钮权限', 'BUTTON211', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(494, 2, 212, '友情链接-编辑-按钮权限', 'BUTTON212', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(495, 2, 213, '友情链接-删除-按钮权限', 'BUTTON213', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(496, 2, 214, '友情链接-排序-按钮权限', 'BUTTON214', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(497, 2, 215, '快递公司-添加-按钮权限', 'BUTTON215', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(498, 2, 216, '快递公司-编辑-按钮权限', 'BUTTON216', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(499, 2, 217, '快递公司-删除-按钮权限', 'BUTTON217', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(500, 2, 218, '分公司-添加-按钮权限', 'BUTTON218', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(501, 2, 219, '分公司-编辑-按钮权限', 'BUTTON219', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(502, 2, 220, '分公司-删除-按钮权限', 'BUTTON220', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(503, 2, 221, '分公司归属-添加-按钮权限', 'BUTTON221', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(504, 2, 222, '分公司归属-删除-按钮权限', 'BUTTON222', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(505, 2, 223, '分公司归属一-添加-按钮权限', 'BUTTON223', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(506, 2, 224, '分公司归属一-删除-按钮权限', 'BUTTON224', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(507, 2, 225, '大区管理-添加-按钮权限', 'BUTTON225', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(508, 2, 226, '大区管理-编辑-按钮权限', 'BUTTON226', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(509, 2, 227, '大区管理-删除-按钮权限', 'BUTTON227', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(510, 2, 228, '体验店-添加-按钮权限', 'BUTTON228', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(511, 2, 229, '体验店-编辑-按钮权限', 'BUTTON229', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(512, 2, 230, '体验店-删除-按钮权限', 'BUTTON230', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(513, 2, 231, '体验店-详情-按钮权限', 'BUTTON231', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(514, 2, 232, '销售渠道-添加-按钮权限', 'BUTTON232', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(515, 2, 233, '销售渠道-编辑-按钮权限', 'BUTTON233', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(516, 2, 234, '销售渠道-删除-按钮权限', 'BUTTON234', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(517, 2, 235, '体验店回收站-恢复-按钮权限', 'BUTTON235', '', 0, 0);

-- --------------------------------------------------------

--
-- 表的结构 `region`
--

CREATE TABLE IF NOT EXISTS `region` (
  `region_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '父级地区',
  `region_name` varchar(120) NOT NULL DEFAULT '' COMMENT '地区名称',
  `region_type` tinyint(1) NOT NULL DEFAULT '2' COMMENT '属于第几层从0层开始',
  PRIMARY KEY (`region_id`),
  KEY `parent_id` (`parent_id`),
  KEY `region_type` (`region_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3409 ;

--
-- 转存表中的数据 `region`
--

INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1, 0, '中国', 0);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2, 1, '北京', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3, 1, '安徽', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(4, 1, '福建', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(5, 1, '甘肃', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(6, 1, '广东', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(7, 1, '广西', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(8, 1, '贵州', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(9, 1, '海南', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(10, 1, '河北', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(11, 1, '河南', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(12, 1, '黑龙江', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(13, 1, '湖北', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(14, 1, '湖南', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(15, 1, '吉林', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(16, 1, '江苏', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(17, 1, '江西', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(18, 1, '辽宁', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(19, 1, '内蒙古', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(20, 1, '宁夏', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(21, 1, '青海', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(22, 1, '山东', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(23, 1, '山西', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(24, 1, '陕西', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(25, 1, '上海', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(26, 1, '四川', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(27, 1, '天津', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(28, 1, '西藏', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(29, 1, '新疆', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(30, 1, '云南', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(31, 1, '浙江', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(32, 1, '重庆', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(33, 1, '香港', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(34, 1, '澳门', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(35, 1, '台湾', 1);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(36, 3, '安庆', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(37, 3, '蚌埠', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(38, 3, '巢湖', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(39, 3, '池州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(40, 3, '滁州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(41, 3, '阜阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(42, 3, '淮北', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(43, 3, '淮南', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(44, 3, '黄山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(45, 3, '六安', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(46, 3, '马鞍山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(47, 3, '宿州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(48, 3, '铜陵', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(49, 3, '芜湖', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(50, 3, '宣城', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(51, 3, '亳州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(52, 2, '北京', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(53, 4, '福州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(54, 4, '龙岩', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(55, 4, '南平', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(56, 4, '宁德', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(57, 4, '莆田', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(58, 4, '泉州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(59, 4, '三明', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(60, 4, '厦门', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(61, 4, '漳州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(62, 5, '兰州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(63, 5, '白银', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(64, 5, '定西', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(65, 5, '甘南', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(66, 5, '嘉峪关', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(67, 5, '金昌', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(68, 5, '酒泉', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(69, 5, '临夏', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(70, 5, '陇南', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(71, 5, '平凉', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(72, 5, '庆阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(73, 5, '天水', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(74, 5, '武威', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(75, 5, '张掖', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(76, 6, '广州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(77, 6, '深圳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(78, 6, '潮州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(79, 6, '东莞', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(80, 6, '佛山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(81, 6, '河源', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(82, 6, '惠州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(83, 6, '江门', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(84, 6, '揭阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(85, 6, '茂名', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(86, 6, '梅州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(87, 6, '清远', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(88, 6, '汕头', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(89, 6, '汕尾', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(90, 6, '韶关', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(91, 6, '阳江', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(92, 6, '云浮', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(93, 6, '湛江', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(94, 6, '肇庆', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(95, 6, '中山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(96, 6, '珠海', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(97, 7, '南宁', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(98, 7, '桂林', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(99, 7, '百色', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(100, 7, '北海', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(101, 7, '崇左', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(102, 7, '防城港', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(103, 7, '贵港', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(104, 7, '河池', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(105, 7, '贺州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(106, 7, '来宾', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(107, 7, '柳州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(108, 7, '钦州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(109, 7, '梧州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(110, 7, '玉林', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(111, 8, '贵阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(112, 8, '安顺', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(113, 8, '毕节', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(114, 8, '六盘水', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(115, 8, '黔东南', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(116, 8, '黔南', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(117, 8, '黔西南', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(118, 8, '铜仁', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(119, 8, '遵义', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(120, 9, '海口', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(121, 9, '三亚', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(122, 9, '白沙', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(123, 9, '保亭', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(124, 9, '昌江', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(125, 9, '澄迈县', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(126, 9, '定安县', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(127, 9, '东方', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(128, 9, '乐东', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(129, 9, '临高县', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(130, 9, '陵水', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(131, 9, '琼海', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(132, 9, '琼中', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(133, 9, '屯昌县', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(134, 9, '万宁', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(135, 9, '文昌', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(136, 9, '五指山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(137, 9, '儋州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(138, 10, '石家庄', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(139, 10, '保定', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(140, 10, '沧州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(141, 10, '承德', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(142, 10, '邯郸', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(143, 10, '衡水', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(144, 10, '廊坊', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(145, 10, '秦皇岛', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(146, 10, '唐山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(147, 10, '邢台', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(148, 10, '张家口', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(149, 11, '郑州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(150, 11, '洛阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(151, 11, '开封', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(152, 11, '安阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(153, 11, '鹤壁', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(154, 11, '济源', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(155, 11, '焦作', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(156, 11, '南阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(157, 11, '平顶山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(158, 11, '三门峡', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(159, 11, '商丘', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(160, 11, '新乡', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(161, 11, '信阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(162, 11, '许昌', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(163, 11, '周口', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(164, 11, '驻马店', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(165, 11, '漯河', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(166, 11, '濮阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(167, 12, '哈尔滨', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(168, 12, '大庆', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(169, 12, '大兴安岭', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(170, 12, '鹤岗', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(171, 12, '黑河', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(172, 12, '鸡西', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(173, 12, '佳木斯', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(174, 12, '牡丹江', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(175, 12, '七台河', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(176, 12, '齐齐哈尔', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(177, 12, '双鸭山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(178, 12, '绥化', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(179, 12, '伊春', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(180, 13, '武汉', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(181, 13, '仙桃', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(182, 13, '鄂州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(183, 13, '黄冈', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(184, 13, '黄石', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(185, 13, '荆门', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(186, 13, '荆州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(187, 13, '潜江', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(188, 13, '神农架林区', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(189, 13, '十堰', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(190, 13, '随州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(191, 13, '天门', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(192, 13, '咸宁', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(193, 13, '襄樊', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(194, 13, '孝感', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(195, 13, '宜昌', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(196, 13, '恩施', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(197, 14, '长沙', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(198, 14, '张家界', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(199, 14, '常德', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(200, 14, '郴州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(201, 14, '衡阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(202, 14, '怀化', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(203, 14, '娄底', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(204, 14, '邵阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(205, 14, '湘潭', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(206, 14, '湘西', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(207, 14, '益阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(208, 14, '永州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(209, 14, '岳阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(210, 14, '株洲', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(211, 15, '长春', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(212, 15, '吉林', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(213, 15, '白城', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(214, 15, '白山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(215, 15, '辽源', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(216, 15, '四平', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(217, 15, '松原', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(218, 15, '通化', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(219, 15, '延边', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(220, 16, '南京', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(221, 16, '苏州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(222, 16, '无锡', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(223, 16, '常州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(224, 16, '淮安', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(225, 16, '连云港', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(226, 16, '南通', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(227, 16, '宿迁', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(228, 16, '泰州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(229, 16, '徐州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(230, 16, '盐城', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(231, 16, '扬州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(232, 16, '镇江', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(233, 17, '南昌', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(234, 17, '抚州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(235, 17, '赣州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(236, 17, '吉安', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(237, 17, '景德镇', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(238, 17, '九江', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(239, 17, '萍乡', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(240, 17, '上饶', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(241, 17, '新余', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(242, 17, '宜春', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(243, 17, '鹰潭', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(244, 18, '沈阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(245, 18, '大连', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(246, 18, '鞍山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(247, 18, '本溪', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(248, 18, '朝阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(249, 18, '丹东', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(250, 18, '抚顺', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(251, 18, '阜新', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(252, 18, '葫芦岛', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(253, 18, '锦州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(254, 18, '辽阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(255, 18, '盘锦', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(256, 18, '铁岭', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(257, 18, '营口', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(258, 19, '呼和浩特', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(259, 19, '阿拉善盟', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(260, 19, '巴彦淖尔盟', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(261, 19, '包头', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(262, 19, '赤峰', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(263, 19, '鄂尔多斯', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(264, 19, '呼伦贝尔', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(265, 19, '通辽', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(266, 19, '乌海', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(267, 19, '乌兰察布市', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(268, 19, '锡林郭勒盟', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(269, 19, '兴安盟', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(270, 20, '银川', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(271, 20, '固原', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(272, 20, '石嘴山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(273, 20, '吴忠', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(274, 20, '中卫', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(275, 21, '西宁', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(276, 21, '果洛', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(277, 21, '海北', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(278, 21, '海东', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(279, 21, '海南', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(280, 21, '海西', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(281, 21, '黄南', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(282, 21, '玉树', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(283, 22, '济南', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(284, 22, '青岛', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(285, 22, '滨州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(286, 22, '德州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(287, 22, '东营', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(288, 22, '菏泽', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(289, 22, '济宁', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(290, 22, '莱芜', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(291, 22, '聊城', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(292, 22, '临沂', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(293, 22, '日照', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(294, 22, '泰安', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(295, 22, '威海', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(296, 22, '潍坊', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(297, 22, '烟台', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(298, 22, '枣庄', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(299, 22, '淄博', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(300, 23, '太原', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(301, 23, '长治', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(302, 23, '大同', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(303, 23, '晋城', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(304, 23, '晋中', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(305, 23, '临汾', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(306, 23, '吕梁', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(307, 23, '朔州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(308, 23, '忻州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(309, 23, '阳泉', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(310, 23, '运城', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(311, 24, '西安', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(312, 24, '安康', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(313, 24, '宝鸡', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(314, 24, '汉中', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(315, 24, '商洛', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(316, 24, '铜川', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(317, 24, '渭南', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(318, 24, '咸阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(319, 24, '延安', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(320, 24, '榆林', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(321, 25, '上海', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(322, 26, '成都', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(323, 26, '绵阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(324, 26, '阿坝', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(325, 26, '巴中', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(326, 26, '达州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(327, 26, '德阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(328, 26, '甘孜', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(329, 26, '广安', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(330, 26, '广元', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(331, 26, '乐山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(332, 26, '凉山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(333, 26, '眉山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(334, 26, '南充', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(335, 26, '内江', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(336, 26, '攀枝花', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(337, 26, '遂宁', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(338, 26, '雅安', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(339, 26, '宜宾', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(340, 26, '资阳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(341, 26, '自贡', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(342, 26, '泸州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(343, 27, '天津', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(344, 28, '拉萨', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(345, 28, '阿里', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(346, 28, '昌都', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(347, 28, '林芝', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(348, 28, '那曲', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(349, 28, '日喀则', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(350, 28, '山南', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(351, 29, '乌鲁木齐', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(352, 29, '阿克苏', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(353, 29, '阿拉尔', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(354, 29, '巴音郭楞', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(355, 29, '博尔塔拉', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(356, 29, '昌吉', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(357, 29, '哈密', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(358, 29, '和田', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(359, 29, '喀什', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(360, 29, '克拉玛依', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(361, 29, '克孜勒苏', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(362, 29, '石河子', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(363, 29, '图木舒克', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(364, 29, '吐鲁番', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(365, 29, '五家渠', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(366, 29, '伊犁', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(367, 30, '昆明', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(368, 30, '怒江', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(369, 30, '普洱', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(370, 30, '丽江', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(371, 30, '保山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(372, 30, '楚雄', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(373, 30, '大理', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(374, 30, '德宏', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(375, 30, '迪庆', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(376, 30, '红河', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(377, 30, '临沧', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(378, 30, '曲靖', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(379, 30, '文山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(380, 30, '西双版纳', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(381, 30, '玉溪', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(382, 30, '昭通', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(383, 31, '杭州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(384, 31, '湖州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(385, 31, '嘉兴', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(386, 31, '金华', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(387, 31, '丽水', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(388, 31, '宁波', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(389, 31, '绍兴', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(390, 31, '台州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(391, 31, '温州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(392, 31, '舟山', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(393, 31, '衢州', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(394, 32, '重庆', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(395, 33, '香港', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(396, 34, '澳门', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(397, 35, '台湾', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(398, 36, '迎江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(399, 36, '大观区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(400, 36, '宜秀区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(401, 36, '桐城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(402, 36, '怀宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(403, 36, '枞阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(404, 36, '潜山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(405, 36, '太湖县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(406, 36, '宿松县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(407, 36, '望江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(408, 36, '岳西县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(409, 37, '中市区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(410, 37, '东市区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(411, 37, '西市区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(412, 37, '郊区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(413, 37, '怀远县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(414, 37, '五河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(415, 37, '固镇县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(416, 38, '居巢区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(417, 38, '庐江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(418, 38, '无为县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(419, 38, '含山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(420, 38, '和县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(421, 39, '贵池区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(422, 39, '东至县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(423, 39, '石台县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(424, 39, '青阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(425, 40, '琅琊区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(426, 40, '南谯区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(427, 40, '天长市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(428, 40, '明光市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(429, 40, '来安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(430, 40, '全椒县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(431, 40, '定远县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(432, 40, '凤阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(433, 41, '蚌山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(434, 41, '龙子湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(435, 41, '禹会区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(436, 41, '淮上区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(437, 41, '颍州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(438, 41, '颍东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(439, 41, '颍泉区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(440, 41, '界首市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(441, 41, '临泉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(442, 41, '太和县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(443, 41, '阜南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(444, 41, '颖上县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(445, 42, '相山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(446, 42, '杜集区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(447, 42, '烈山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(448, 42, '濉溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(449, 43, '田家庵区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(450, 43, '大通区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(451, 43, '谢家集区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(452, 43, '八公山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(453, 43, '潘集区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(454, 43, '凤台县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(455, 44, '屯溪区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(456, 44, '黄山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(457, 44, '徽州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(458, 44, '歙县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(459, 44, '休宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(460, 44, '黟县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(461, 44, '祁门县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(462, 45, '金安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(463, 45, '裕安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(464, 45, '寿县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(465, 45, '霍邱县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(466, 45, '舒城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(467, 45, '金寨县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(468, 45, '霍山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(469, 46, '雨山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(470, 46, '花山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(471, 46, '金家庄区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(472, 46, '当涂县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(473, 47, '埇桥区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(474, 47, '砀山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(475, 47, '萧县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(476, 47, '灵璧县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(477, 47, '泗县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(478, 48, '铜官山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(479, 48, '狮子山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(480, 48, '郊区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(481, 48, '铜陵县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(482, 49, '镜湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(483, 49, '弋江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(484, 49, '鸠江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(485, 49, '三山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(486, 49, '芜湖县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(487, 49, '繁昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(488, 49, '南陵县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(489, 50, '宣州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(490, 50, '宁国市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(491, 50, '郎溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(492, 50, '广德县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(493, 50, '泾县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(494, 50, '绩溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(495, 50, '旌德县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(496, 51, '涡阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(497, 51, '蒙城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(498, 51, '利辛县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(499, 51, '谯城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(500, 52, '东城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(501, 52, '西城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(502, 52, '海淀区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(503, 52, '朝阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(504, 52, '崇文区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(505, 52, '宣武区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(506, 52, '丰台区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(507, 52, '石景山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(508, 52, '房山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(509, 52, '门头沟区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(510, 52, '通州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(511, 52, '顺义区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(512, 52, '昌平区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(513, 52, '怀柔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(514, 52, '平谷区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(515, 52, '大兴区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(516, 52, '密云县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(517, 52, '延庆县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(518, 53, '鼓楼区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(519, 53, '台江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(520, 53, '仓山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(521, 53, '马尾区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(522, 53, '晋安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(523, 53, '福清市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(524, 53, '长乐市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(525, 53, '闽侯县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(526, 53, '连江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(527, 53, '罗源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(528, 53, '闽清县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(529, 53, '永泰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(530, 53, '平潭县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(531, 54, '新罗区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(532, 54, '漳平市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(533, 54, '长汀县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(534, 54, '永定县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(535, 54, '上杭县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(536, 54, '武平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(537, 54, '连城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(538, 55, '延平区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(539, 55, '邵武市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(540, 55, '武夷山市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(541, 55, '建瓯市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(542, 55, '建阳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(543, 55, '顺昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(544, 55, '浦城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(545, 55, '光泽县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(546, 55, '松溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(547, 55, '政和县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(548, 56, '蕉城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(549, 56, '福安市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(550, 56, '福鼎市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(551, 56, '霞浦县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(552, 56, '古田县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(553, 56, '屏南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(554, 56, '寿宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(555, 56, '周宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(556, 56, '柘荣县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(557, 57, '城厢区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(558, 57, '涵江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(559, 57, '荔城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(560, 57, '秀屿区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(561, 57, '仙游县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(562, 58, '鲤城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(563, 58, '丰泽区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(564, 58, '洛江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(565, 58, '清濛开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(566, 58, '泉港区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(567, 58, '石狮市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(568, 58, '晋江市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(569, 58, '南安市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(570, 58, '惠安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(571, 58, '安溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(572, 58, '永春县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(573, 58, '德化县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(574, 58, '金门县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(575, 59, '梅列区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(576, 59, '三元区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(577, 59, '永安市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(578, 59, '明溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(579, 59, '清流县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(580, 59, '宁化县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(581, 59, '大田县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(582, 59, '尤溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(583, 59, '沙县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(584, 59, '将乐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(585, 59, '泰宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(586, 59, '建宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(587, 60, '思明区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(588, 60, '海沧区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(589, 60, '湖里区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(590, 60, '集美区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(591, 60, '同安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(592, 60, '翔安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(593, 61, '芗城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(594, 61, '龙文区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(595, 61, '龙海市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(596, 61, '云霄县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(597, 61, '漳浦县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(598, 61, '诏安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(599, 61, '长泰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(600, 61, '东山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(601, 61, '南靖县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(602, 61, '平和县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(603, 61, '华安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(604, 62, '皋兰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(605, 62, '城关区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(606, 62, '七里河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(607, 62, '西固区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(608, 62, '安宁区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(609, 62, '红古区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(610, 62, '永登县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(611, 62, '榆中县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(612, 63, '白银区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(613, 63, '平川区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(614, 63, '会宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(615, 63, '景泰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(616, 63, '靖远县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(617, 64, '临洮县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(618, 64, '陇西县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(619, 64, '通渭县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(620, 64, '渭源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(621, 64, '漳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(622, 64, '岷县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(623, 64, '安定区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(624, 64, '安定区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(625, 65, '合作市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(626, 65, '临潭县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(627, 65, '卓尼县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(628, 65, '舟曲县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(629, 65, '迭部县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(630, 65, '玛曲县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(631, 65, '碌曲县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(632, 65, '夏河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(633, 66, '嘉峪关市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(634, 67, '金川区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(635, 67, '永昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(636, 68, '肃州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(637, 68, '玉门市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(638, 68, '敦煌市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(639, 68, '金塔县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(640, 68, '瓜州县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(641, 68, '肃北', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(642, 68, '阿克塞', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(643, 69, '临夏市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(644, 69, '临夏县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(645, 69, '康乐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(646, 69, '永靖县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(647, 69, '广河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(648, 69, '和政县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(649, 69, '东乡族自治县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(650, 69, '积石山', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(651, 70, '成县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(652, 70, '徽县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(653, 70, '康县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(654, 70, '礼县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(655, 70, '两当县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(656, 70, '文县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(657, 70, '西和县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(658, 70, '宕昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(659, 70, '武都区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(660, 71, '崇信县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(661, 71, '华亭县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(662, 71, '静宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(663, 71, '灵台县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(664, 71, '崆峒区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(665, 71, '庄浪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(666, 71, '泾川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(667, 72, '合水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(668, 72, '华池县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(669, 72, '环县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(670, 72, '宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(671, 72, '庆城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(672, 72, '西峰区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(673, 72, '镇原县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(674, 72, '正宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(675, 73, '甘谷县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(676, 73, '秦安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(677, 73, '清水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(678, 73, '秦州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(679, 73, '麦积区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(680, 73, '武山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(681, 73, '张家川', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(682, 74, '古浪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(683, 74, '民勤县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(684, 74, '天祝', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(685, 74, '凉州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(686, 75, '高台县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(687, 75, '临泽县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(688, 75, '民乐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(689, 75, '山丹县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(690, 75, '肃南', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(691, 75, '甘州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(692, 76, '从化市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(693, 76, '天河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(694, 76, '东山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(695, 76, '白云区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(696, 76, '海珠区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(697, 76, '荔湾区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(698, 76, '越秀区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(699, 76, '黄埔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(700, 76, '番禺区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(701, 76, '花都区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(702, 76, '增城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(703, 76, '从化区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(704, 76, '市郊', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(705, 77, '福田区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(706, 77, '罗湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(707, 77, '南山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(708, 77, '宝安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(709, 77, '龙岗区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(710, 77, '盐田区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(711, 78, '湘桥区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(712, 78, '潮安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(713, 78, '饶平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(714, 79, '南城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(715, 79, '东城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(716, 79, '万江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(717, 79, '莞城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(718, 79, '石龙镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(719, 79, '虎门镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(720, 79, '麻涌镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(721, 79, '道滘镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(722, 79, '石碣镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(723, 79, '沙田镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(724, 79, '望牛墩镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(725, 79, '洪梅镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(726, 79, '茶山镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(727, 79, '寮步镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(728, 79, '大岭山镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(729, 79, '大朗镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(730, 79, '黄江镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(731, 79, '樟木头', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(732, 79, '凤岗镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(733, 79, '塘厦镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(734, 79, '谢岗镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(735, 79, '厚街镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(736, 79, '清溪镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(737, 79, '常平镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(738, 79, '桥头镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(739, 79, '横沥镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(740, 79, '东坑镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(741, 79, '企石镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(742, 79, '石排镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(743, 79, '长安镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(744, 79, '中堂镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(745, 79, '高埗镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(746, 80, '禅城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(747, 80, '南海区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(748, 80, '顺德区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(749, 80, '三水区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(750, 80, '高明区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(751, 81, '东源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(752, 81, '和平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(753, 81, '源城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(754, 81, '连平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(755, 81, '龙川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(756, 81, '紫金县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(757, 82, '惠阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(758, 82, '惠城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(759, 82, '大亚湾', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(760, 82, '博罗县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(761, 82, '惠东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(762, 82, '龙门县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(763, 83, '江海区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(764, 83, '蓬江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(765, 83, '新会区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(766, 83, '台山市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(767, 83, '开平市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(768, 83, '鹤山市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(769, 83, '恩平市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(770, 84, '榕城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(771, 84, '普宁市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(772, 84, '揭东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(773, 84, '揭西县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(774, 84, '惠来县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(775, 85, '茂南区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(776, 85, '茂港区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(777, 85, '高州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(778, 85, '化州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(779, 85, '信宜市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(780, 85, '电白县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(781, 86, '梅县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(782, 86, '梅江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(783, 86, '兴宁市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(784, 86, '大埔县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(785, 86, '丰顺县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(786, 86, '五华县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(787, 86, '平远县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(788, 86, '蕉岭县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(789, 87, '清城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(790, 87, '英德市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(791, 87, '连州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(792, 87, '佛冈县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(793, 87, '阳山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(794, 87, '清新县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(795, 87, '连山', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(796, 87, '连南', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(797, 88, '南澳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(798, 88, '潮阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(799, 88, '澄海区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(800, 88, '龙湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(801, 88, '金平区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(802, 88, '濠江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(803, 88, '潮南区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(804, 89, '城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(805, 89, '陆丰市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(806, 89, '海丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(807, 89, '陆河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(808, 90, '曲江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(809, 90, '浈江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(810, 90, '武江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(811, 90, '曲江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(812, 90, '乐昌市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(813, 90, '南雄市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(814, 90, '始兴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(815, 90, '仁化县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(816, 90, '翁源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(817, 90, '新丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(818, 90, '乳源', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(819, 91, '江城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(820, 91, '阳春市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(821, 91, '阳西县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(822, 91, '阳东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(823, 92, '云城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(824, 92, '罗定市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(825, 92, '新兴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(826, 92, '郁南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(827, 92, '云安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(828, 93, '赤坎区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(829, 93, '霞山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(830, 93, '坡头区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(831, 93, '麻章区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(832, 93, '廉江市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(833, 93, '雷州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(834, 93, '吴川市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(835, 93, '遂溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(836, 93, '徐闻县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(837, 94, '肇庆市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(838, 94, '高要市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(839, 94, '四会市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(840, 94, '广宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(841, 94, '怀集县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(842, 94, '封开县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(843, 94, '德庆县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(844, 95, '石岐街道', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(845, 95, '东区街道', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(846, 95, '西区街道', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(847, 95, '环城街道', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(848, 95, '中山港街道', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(849, 95, '五桂山街道', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(850, 96, '香洲区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(851, 96, '斗门区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(852, 96, '金湾区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(853, 97, '邕宁区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(854, 97, '青秀区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(855, 97, '兴宁区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(856, 97, '良庆区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(857, 97, '西乡塘区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(858, 97, '江南区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(859, 97, '武鸣县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(860, 97, '隆安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(861, 97, '马山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(862, 97, '上林县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(863, 97, '宾阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(864, 97, '横县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(865, 98, '秀峰区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(866, 98, '叠彩区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(867, 98, '象山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(868, 98, '七星区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(869, 98, '雁山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(870, 98, '阳朔县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(871, 98, '临桂县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(872, 98, '灵川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(873, 98, '全州县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(874, 98, '平乐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(875, 98, '兴安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(876, 98, '灌阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(877, 98, '荔浦县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(878, 98, '资源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(879, 98, '永福县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(880, 98, '龙胜', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(881, 98, '恭城', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(882, 99, '右江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(883, 99, '凌云县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(884, 99, '平果县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(885, 99, '西林县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(886, 99, '乐业县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(887, 99, '德保县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(888, 99, '田林县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(889, 99, '田阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(890, 99, '靖西县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(891, 99, '田东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(892, 99, '那坡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(893, 99, '隆林', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(894, 100, '海城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(895, 100, '银海区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(896, 100, '铁山港区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(897, 100, '合浦县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(898, 101, '江州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(899, 101, '凭祥市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(900, 101, '宁明县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(901, 101, '扶绥县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(902, 101, '龙州县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(903, 101, '大新县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(904, 101, '天等县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(905, 102, '港口区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(906, 102, '防城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(907, 102, '东兴市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(908, 102, '上思县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(909, 103, '港北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(910, 103, '港南区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(911, 103, '覃塘区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(912, 103, '桂平市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(913, 103, '平南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(914, 104, '金城江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(915, 104, '宜州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(916, 104, '天峨县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(917, 104, '凤山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(918, 104, '南丹县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(919, 104, '东兰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(920, 104, '都安', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(921, 104, '罗城', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(922, 104, '巴马', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(923, 104, '环江', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(924, 104, '大化', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(925, 105, '八步区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(926, 105, '钟山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(927, 105, '昭平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(928, 105, '富川', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(929, 106, '兴宾区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(930, 106, '合山市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(931, 106, '象州县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(932, 106, '武宣县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(933, 106, '忻城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(934, 106, '金秀', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(935, 107, '城中区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(936, 107, '鱼峰区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(937, 107, '柳北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(938, 107, '柳南区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(939, 107, '柳江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(940, 107, '柳城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(941, 107, '鹿寨县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(942, 107, '融安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(943, 107, '融水', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(944, 107, '三江', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(945, 108, '钦南区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(946, 108, '钦北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(947, 108, '灵山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(948, 108, '浦北县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(949, 109, '万秀区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(950, 109, '蝶山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(951, 109, '长洲区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(952, 109, '岑溪市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(953, 109, '苍梧县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(954, 109, '藤县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(955, 109, '蒙山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(956, 110, '玉州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(957, 110, '北流市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(958, 110, '容县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(959, 110, '陆川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(960, 110, '博白县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(961, 110, '兴业县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(962, 111, '南明区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(963, 111, '云岩区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(964, 111, '花溪区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(965, 111, '乌当区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(966, 111, '白云区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(967, 111, '小河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(968, 111, '金阳新区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(969, 111, '新天园区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(970, 111, '清镇市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(971, 111, '开阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(972, 111, '修文县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(973, 111, '息烽县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(974, 112, '西秀区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(975, 112, '关岭', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(976, 112, '镇宁', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(977, 112, '紫云', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(978, 112, '平坝县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(979, 112, '普定县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(980, 113, '毕节市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(981, 113, '大方县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(982, 113, '黔西县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(983, 113, '金沙县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(984, 113, '织金县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(985, 113, '纳雍县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(986, 113, '赫章县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(987, 113, '威宁', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(988, 114, '钟山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(989, 114, '六枝特区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(990, 114, '水城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(991, 114, '盘县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(992, 115, '凯里市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(993, 115, '黄平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(994, 115, '施秉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(995, 115, '三穗县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(996, 115, '镇远县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(997, 115, '岑巩县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(998, 115, '天柱县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(999, 115, '锦屏县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1000, 115, '剑河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1001, 115, '台江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1002, 115, '黎平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1003, 115, '榕江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1004, 115, '从江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1005, 115, '雷山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1006, 115, '麻江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1007, 115, '丹寨县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1008, 116, '都匀市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1009, 116, '福泉市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1010, 116, '荔波县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1011, 116, '贵定县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1012, 116, '瓮安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1013, 116, '独山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1014, 116, '平塘县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1015, 116, '罗甸县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1016, 116, '长顺县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1017, 116, '龙里县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1018, 116, '惠水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1019, 116, '三都', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1020, 117, '兴义市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1021, 117, '兴仁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1022, 117, '普安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1023, 117, '晴隆县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1024, 117, '贞丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1025, 117, '望谟县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1026, 117, '册亨县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1027, 117, '安龙县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1028, 118, '铜仁市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1029, 118, '江口县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1030, 118, '石阡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1031, 118, '思南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1032, 118, '德江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1033, 118, '玉屏', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1034, 118, '印江', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1035, 118, '沿河', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1036, 118, '松桃', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1037, 118, '万山特区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1038, 119, '红花岗区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1039, 119, '务川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1040, 119, '道真县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1041, 119, '汇川区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1042, 119, '赤水市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1043, 119, '仁怀市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1044, 119, '遵义县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1045, 119, '桐梓县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1046, 119, '绥阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1047, 119, '正安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1048, 119, '凤冈县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1049, 119, '湄潭县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1050, 119, '余庆县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1051, 119, '习水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1052, 119, '道真', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1053, 119, '务川', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1054, 120, '秀英区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1055, 120, '龙华区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1056, 120, '琼山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1057, 120, '美兰区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1058, 137, '市区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1059, 137, '洋浦开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1060, 137, '那大镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1061, 137, '王五镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1062, 137, '雅星镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1063, 137, '大成镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1064, 137, '中和镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1065, 137, '峨蔓镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1066, 137, '南丰镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1067, 137, '白马井镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1068, 137, '兰洋镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1069, 137, '和庆镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1070, 137, '海头镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1071, 137, '排浦镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1072, 137, '东成镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1073, 137, '光村镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1074, 137, '木棠镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1075, 137, '新州镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1076, 137, '三都镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1077, 137, '其他', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1078, 138, '长安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1079, 138, '桥东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1080, 138, '桥西区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1081, 138, '新华区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1082, 138, '裕华区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1083, 138, '井陉矿区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1084, 138, '高新区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1085, 138, '辛集市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1086, 138, '藁城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1087, 138, '晋州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1088, 138, '新乐市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1089, 138, '鹿泉市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1090, 138, '井陉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1091, 138, '正定县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1092, 138, '栾城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1093, 138, '行唐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1094, 138, '灵寿县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1095, 138, '高邑县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1096, 138, '深泽县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1097, 138, '赞皇县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1098, 138, '无极县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1099, 138, '平山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1100, 138, '元氏县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1101, 138, '赵县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1102, 139, '新市区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1103, 139, '南市区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1104, 139, '北市区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1105, 139, '涿州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1106, 139, '定州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1107, 139, '安国市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1108, 139, '高碑店市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1109, 139, '满城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1110, 139, '清苑县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1111, 139, '涞水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1112, 139, '阜平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1113, 139, '徐水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1114, 139, '定兴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1115, 139, '唐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1116, 139, '高阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1117, 139, '容城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1118, 139, '涞源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1119, 139, '望都县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1120, 139, '安新县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1121, 139, '易县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1122, 139, '曲阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1123, 139, '蠡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1124, 139, '顺平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1125, 139, '博野县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1126, 139, '雄县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1127, 140, '运河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1128, 140, '新华区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1129, 140, '泊头市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1130, 140, '任丘市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1131, 140, '黄骅市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1132, 140, '河间市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1133, 140, '沧县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1134, 140, '青县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1135, 140, '东光县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1136, 140, '海兴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1137, 140, '盐山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1138, 140, '肃宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1139, 140, '南皮县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1140, 140, '吴桥县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1141, 140, '献县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1142, 140, '孟村', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1143, 141, '双桥区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1144, 141, '双滦区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1145, 141, '鹰手营子矿区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1146, 141, '承德县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1147, 141, '兴隆县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1148, 141, '平泉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1149, 141, '滦平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1150, 141, '隆化县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1151, 141, '丰宁', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1152, 141, '宽城', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1153, 141, '围场', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1154, 142, '从台区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1155, 142, '复兴区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1156, 142, '邯山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1157, 142, '峰峰矿区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1158, 142, '武安市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1159, 142, '邯郸县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1160, 142, '临漳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1161, 142, '成安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1162, 142, '大名县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1163, 142, '涉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1164, 142, '磁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1165, 142, '肥乡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1166, 142, '永年县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1167, 142, '邱县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1168, 142, '鸡泽县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1169, 142, '广平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1170, 142, '馆陶县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1171, 142, '魏县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1172, 142, '曲周县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1173, 143, '桃城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1174, 143, '冀州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1175, 143, '深州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1176, 143, '枣强县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1177, 143, '武邑县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1178, 143, '武强县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1179, 143, '饶阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1180, 143, '安平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1181, 143, '故城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1182, 143, '景县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1183, 143, '阜城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1184, 144, '安次区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1185, 144, '广阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1186, 144, '霸州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1187, 144, '三河市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1188, 144, '固安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1189, 144, '永清县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1190, 144, '香河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1191, 144, '大城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1192, 144, '文安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1193, 144, '大厂', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1194, 145, '海港区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1195, 145, '山海关区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1196, 145, '北戴河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1197, 145, '昌黎县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1198, 145, '抚宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1199, 145, '卢龙县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1200, 145, '青龙', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1201, 146, '路北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1202, 146, '路南区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1203, 146, '古冶区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1204, 146, '开平区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1205, 146, '丰南区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1206, 146, '丰润区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1207, 146, '遵化市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1208, 146, '迁安市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1209, 146, '滦县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1210, 146, '滦南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1211, 146, '乐亭县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1212, 146, '迁西县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1213, 146, '玉田县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1214, 146, '唐海县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1215, 147, '桥东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1216, 147, '桥西区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1217, 147, '南宫市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1218, 147, '沙河市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1219, 147, '邢台县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1220, 147, '临城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1221, 147, '内丘县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1222, 147, '柏乡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1223, 147, '隆尧县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1224, 147, '任县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1225, 147, '南和县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1226, 147, '宁晋县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1227, 147, '巨鹿县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1228, 147, '新河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1229, 147, '广宗县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1230, 147, '平乡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1231, 147, '威县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1232, 147, '清河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1233, 147, '临西县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1234, 148, '桥西区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1235, 148, '桥东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1236, 148, '宣化区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1237, 148, '下花园区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1238, 148, '宣化县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1239, 148, '张北县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1240, 148, '康保县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1241, 148, '沽源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1242, 148, '尚义县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1243, 148, '蔚县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1244, 148, '阳原县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1245, 148, '怀安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1246, 148, '万全县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1247, 148, '怀来县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1248, 148, '涿鹿县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1249, 148, '赤城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1250, 148, '崇礼县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1251, 149, '金水区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1252, 149, '邙山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1253, 149, '二七区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1254, 149, '管城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1255, 149, '中原区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1256, 149, '上街区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1257, 149, '惠济区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1258, 149, '郑东新区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1259, 149, '经济技术开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1260, 149, '高新开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1261, 149, '出口加工区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1262, 149, '巩义市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1263, 149, '荥阳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1264, 149, '新密市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1265, 149, '新郑市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1266, 149, '登封市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1267, 149, '中牟县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1268, 150, '西工区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1269, 150, '老城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1270, 150, '涧西区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1271, 150, '瀍河回族区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1272, 150, '洛龙区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1273, 150, '吉利区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1274, 150, '偃师市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1275, 150, '孟津县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1276, 150, '新安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1277, 150, '栾川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1278, 150, '嵩县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1279, 150, '汝阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1280, 150, '宜阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1281, 150, '洛宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1282, 150, '伊川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1283, 151, '鼓楼区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1284, 151, '龙亭区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1285, 151, '顺河回族区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1286, 151, '金明区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1287, 151, '禹王台区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1288, 151, '杞县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1289, 151, '通许县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1290, 151, '尉氏县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1291, 151, '开封县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1292, 151, '兰考县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1293, 152, '北关区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1294, 152, '文峰区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1295, 152, '殷都区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1296, 152, '龙安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1297, 152, '林州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1298, 152, '安阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1299, 152, '汤阴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1300, 152, '滑县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1301, 152, '内黄县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1302, 153, '淇滨区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1303, 153, '山城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1304, 153, '鹤山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1305, 153, '浚县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1306, 153, '淇县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1307, 154, '济源市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1308, 155, '解放区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1309, 155, '中站区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1310, 155, '马村区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1311, 155, '山阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1312, 155, '沁阳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1313, 155, '孟州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1314, 155, '修武县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1315, 155, '博爱县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1316, 155, '武陟县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1317, 155, '温县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1318, 156, '卧龙区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1319, 156, '宛城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1320, 156, '邓州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1321, 156, '南召县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1322, 156, '方城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1323, 156, '西峡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1324, 156, '镇平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1325, 156, '内乡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1326, 156, '淅川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1327, 156, '社旗县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1328, 156, '唐河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1329, 156, '新野县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1330, 156, '桐柏县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1331, 157, '新华区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1332, 157, '卫东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1333, 157, '湛河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1334, 157, '石龙区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1335, 157, '舞钢市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1336, 157, '汝州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1337, 157, '宝丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1338, 157, '叶县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1339, 157, '鲁山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1340, 157, '郏县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1341, 158, '湖滨区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1342, 158, '义马市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1343, 158, '灵宝市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1344, 158, '渑池县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1345, 158, '陕县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1346, 158, '卢氏县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1347, 159, '梁园区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1348, 159, '睢阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1349, 159, '永城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1350, 159, '民权县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1351, 159, '睢县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1352, 159, '宁陵县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1353, 159, '虞城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1354, 159, '柘城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1355, 159, '夏邑县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1356, 160, '卫滨区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1357, 160, '红旗区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1358, 160, '凤泉区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1359, 160, '牧野区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1360, 160, '卫辉市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1361, 160, '辉县市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1362, 160, '新乡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1363, 160, '获嘉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1364, 160, '原阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1365, 160, '延津县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1366, 160, '封丘县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1367, 160, '长垣县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1368, 161, '浉河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1369, 161, '平桥区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1370, 161, '罗山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1371, 161, '光山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1372, 161, '新县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1373, 161, '商城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1374, 161, '固始县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1375, 161, '潢川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1376, 161, '淮滨县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1377, 161, '息县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1378, 162, '魏都区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1379, 162, '禹州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1380, 162, '长葛市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1381, 162, '许昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1382, 162, '鄢陵县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1383, 162, '襄城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1384, 163, '川汇区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1385, 163, '项城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1386, 163, '扶沟县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1387, 163, '西华县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1388, 163, '商水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1389, 163, '沈丘县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1390, 163, '郸城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1391, 163, '淮阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1392, 163, '太康县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1393, 163, '鹿邑县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1394, 164, '驿城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1395, 164, '西平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1396, 164, '上蔡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1397, 164, '平舆县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1398, 164, '正阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1399, 164, '确山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1400, 164, '泌阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1401, 164, '汝南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1402, 164, '遂平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1403, 164, '新蔡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1404, 165, '郾城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1405, 165, '源汇区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1406, 165, '召陵区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1407, 165, '舞阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1408, 165, '临颍县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1409, 166, '华龙区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1410, 166, '清丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1411, 166, '南乐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1412, 166, '范县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1413, 166, '台前县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1414, 166, '濮阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1415, 167, '道里区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1416, 167, '南岗区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1417, 167, '动力区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1418, 167, '平房区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1419, 167, '香坊区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1420, 167, '太平区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1421, 167, '道外区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1422, 167, '阿城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1423, 167, '呼兰区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1424, 167, '松北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1425, 167, '尚志市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1426, 167, '双城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1427, 167, '五常市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1428, 167, '方正县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1429, 167, '宾县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1430, 167, '依兰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1431, 167, '巴彦县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1432, 167, '通河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1433, 167, '木兰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1434, 167, '延寿县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1435, 168, '萨尔图区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1436, 168, '红岗区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1437, 168, '龙凤区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1438, 168, '让胡路区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1439, 168, '大同区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1440, 168, '肇州县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1441, 168, '肇源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1442, 168, '林甸县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1443, 168, '杜尔伯特', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1444, 169, '呼玛县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1445, 169, '漠河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1446, 169, '塔河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1447, 170, '兴山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1448, 170, '工农区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1449, 170, '南山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1450, 170, '兴安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1451, 170, '向阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1452, 170, '东山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1453, 170, '萝北县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1454, 170, '绥滨县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1455, 171, '爱辉区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1456, 171, '五大连池市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1457, 171, '北安市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1458, 171, '嫩江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1459, 171, '逊克县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1460, 171, '孙吴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1461, 172, '鸡冠区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1462, 172, '恒山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1463, 172, '城子河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1464, 172, '滴道区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1465, 172, '梨树区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1466, 172, '虎林市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1467, 172, '密山市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1468, 172, '鸡东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1469, 173, '前进区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1470, 173, '郊区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1471, 173, '向阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1472, 173, '东风区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1473, 173, '同江市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1474, 173, '富锦市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1475, 173, '桦南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1476, 173, '桦川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1477, 173, '汤原县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1478, 173, '抚远县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1479, 174, '爱民区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1480, 174, '东安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1481, 174, '阳明区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1482, 174, '西安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1483, 174, '绥芬河市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1484, 174, '海林市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1485, 174, '宁安市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1486, 174, '穆棱市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1487, 174, '东宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1488, 174, '林口县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1489, 175, '桃山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1490, 175, '新兴区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1491, 175, '茄子河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1492, 175, '勃利县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1493, 176, '龙沙区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1494, 176, '昂昂溪区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1495, 176, '铁峰区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1496, 176, '建华区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1497, 176, '富拉尔基区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1498, 176, '碾子山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1499, 176, '梅里斯达斡尔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1500, 176, '讷河市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1501, 176, '龙江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1502, 176, '依安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1503, 176, '泰来县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1504, 176, '甘南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1505, 176, '富裕县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1506, 176, '克山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1507, 176, '克东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1508, 176, '拜泉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1509, 177, '尖山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1510, 177, '岭东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1511, 177, '四方台区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1512, 177, '宝山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1513, 177, '集贤县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1514, 177, '友谊县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1515, 177, '宝清县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1516, 177, '饶河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1517, 178, '北林区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1518, 178, '安达市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1519, 178, '肇东市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1520, 178, '海伦市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1521, 178, '望奎县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1522, 178, '兰西县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1523, 178, '青冈县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1524, 178, '庆安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1525, 178, '明水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1526, 178, '绥棱县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1527, 179, '伊春区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1528, 179, '带岭区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1529, 179, '南岔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1530, 179, '金山屯区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1531, 179, '西林区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1532, 179, '美溪区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1533, 179, '乌马河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1534, 179, '翠峦区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1535, 179, '友好区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1536, 179, '上甘岭区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1537, 179, '五营区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1538, 179, '红星区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1539, 179, '新青区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1540, 179, '汤旺河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1541, 179, '乌伊岭区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1542, 179, '铁力市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1543, 179, '嘉荫县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1544, 180, '江岸区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1545, 180, '武昌区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1546, 180, '江汉区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1547, 180, '硚口区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1548, 180, '汉阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1549, 180, '青山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1550, 180, '洪山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1551, 180, '东西湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1552, 180, '汉南区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1553, 180, '蔡甸区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1554, 180, '江夏区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1555, 180, '黄陂区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1556, 180, '新洲区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1557, 180, '经济开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1558, 181, '仙桃市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1559, 182, '鄂城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1560, 182, '华容区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1561, 182, '梁子湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1562, 183, '黄州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1563, 183, '麻城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1564, 183, '武穴市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1565, 183, '团风县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1566, 183, '红安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1567, 183, '罗田县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1568, 183, '英山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1569, 183, '浠水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1570, 183, '蕲春县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1571, 183, '黄梅县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1572, 184, '黄石港区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1573, 184, '西塞山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1574, 184, '下陆区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1575, 184, '铁山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1576, 184, '大冶市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1577, 184, '阳新县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1578, 185, '东宝区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1579, 185, '掇刀区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1580, 185, '钟祥市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1581, 185, '京山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1582, 185, '沙洋县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1583, 186, '沙市区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1584, 186, '荆州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1585, 186, '石首市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1586, 186, '洪湖市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1587, 186, '松滋市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1588, 186, '公安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1589, 186, '监利县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1590, 186, '江陵县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1591, 187, '潜江市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1592, 188, '神农架林区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1593, 189, '张湾区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1594, 189, '茅箭区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1595, 189, '丹江口市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1596, 189, '郧县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1597, 189, '郧西县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1598, 189, '竹山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1599, 189, '竹溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1600, 189, '房县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1601, 190, '曾都区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1602, 190, '广水市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1603, 191, '天门市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1604, 192, '咸安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1605, 192, '赤壁市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1606, 192, '嘉鱼县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1607, 192, '通城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1608, 192, '崇阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1609, 192, '通山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1610, 193, '襄城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1611, 193, '樊城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1612, 193, '襄阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1613, 193, '老河口市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1614, 193, '枣阳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1615, 193, '宜城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1616, 193, '南漳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1617, 193, '谷城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1618, 193, '保康县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1619, 194, '孝南区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1620, 194, '应城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1621, 194, '安陆市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1622, 194, '汉川市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1623, 194, '孝昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1624, 194, '大悟县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1625, 194, '云梦县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1626, 195, '长阳', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1627, 195, '五峰', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1628, 195, '西陵区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1629, 195, '伍家岗区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1630, 195, '点军区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1631, 195, '猇亭区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1632, 195, '夷陵区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1633, 195, '宜都市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1634, 195, '当阳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1635, 195, '枝江市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1636, 195, '远安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1637, 195, '兴山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1638, 195, '秭归县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1639, 196, '恩施市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1640, 196, '利川市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1641, 196, '建始县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1642, 196, '巴东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1643, 196, '宣恩县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1644, 196, '咸丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1645, 196, '来凤县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1646, 196, '鹤峰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1647, 197, '岳麓区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1648, 197, '芙蓉区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1649, 197, '天心区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1650, 197, '开福区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1651, 197, '雨花区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1652, 197, '开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1653, 197, '浏阳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1654, 197, '长沙县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1655, 197, '望城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1656, 197, '宁乡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1657, 198, '永定区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1658, 198, '武陵源区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1659, 198, '慈利县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1660, 198, '桑植县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1661, 199, '武陵区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1662, 199, '鼎城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1663, 199, '津市市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1664, 199, '安乡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1665, 199, '汉寿县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1666, 199, '澧县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1667, 199, '临澧县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1668, 199, '桃源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1669, 199, '石门县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1670, 200, '北湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1671, 200, '苏仙区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1672, 200, '资兴市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1673, 200, '桂阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1674, 200, '宜章县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1675, 200, '永兴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1676, 200, '嘉禾县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1677, 200, '临武县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1678, 200, '汝城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1679, 200, '桂东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1680, 200, '安仁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1681, 201, '雁峰区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1682, 201, '珠晖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1683, 201, '石鼓区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1684, 201, '蒸湘区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1685, 201, '南岳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1686, 201, '耒阳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1687, 201, '常宁市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1688, 201, '衡阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1689, 201, '衡南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1690, 201, '衡山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1691, 201, '衡东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1692, 201, '祁东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1693, 202, '鹤城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1694, 202, '靖州', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1695, 202, '麻阳', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1696, 202, '通道', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1697, 202, '新晃', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1698, 202, '芷江', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1699, 202, '沅陵县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1700, 202, '辰溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1701, 202, '溆浦县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1702, 202, '中方县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1703, 202, '会同县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1704, 202, '洪江市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1705, 203, '娄星区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1706, 203, '冷水江市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1707, 203, '涟源市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1708, 203, '双峰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1709, 203, '新化县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1710, 204, '城步', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1711, 204, '双清区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1712, 204, '大祥区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1713, 204, '北塔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1714, 204, '武冈市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1715, 204, '邵东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1716, 204, '新邵县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1717, 204, '邵阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1718, 204, '隆回县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1719, 204, '洞口县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1720, 204, '绥宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1721, 204, '新宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1722, 205, '岳塘区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1723, 205, '雨湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1724, 205, '湘乡市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1725, 205, '韶山市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1726, 205, '湘潭县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1727, 206, '吉首市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1728, 206, '泸溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1729, 206, '凤凰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1730, 206, '花垣县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1731, 206, '保靖县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1732, 206, '古丈县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1733, 206, '永顺县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1734, 206, '龙山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1735, 207, '赫山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1736, 207, '资阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1737, 207, '沅江市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1738, 207, '南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1739, 207, '桃江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1740, 207, '安化县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1741, 208, '江华', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1742, 208, '冷水滩区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1743, 208, '零陵区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1744, 208, '祁阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1745, 208, '东安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1746, 208, '双牌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1747, 208, '道县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1748, 208, '江永县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1749, 208, '宁远县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1750, 208, '蓝山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1751, 208, '新田县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1752, 209, '岳阳楼区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1753, 209, '君山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1754, 209, '云溪区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1755, 209, '汨罗市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1756, 209, '临湘市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1757, 209, '岳阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1758, 209, '华容县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1759, 209, '湘阴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1760, 209, '平江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1761, 210, '天元区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1762, 210, '荷塘区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1763, 210, '芦淞区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1764, 210, '石峰区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1765, 210, '醴陵市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1766, 210, '株洲县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1767, 210, '攸县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1768, 210, '茶陵县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1769, 210, '炎陵县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1770, 211, '朝阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1771, 211, '宽城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1772, 211, '二道区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1773, 211, '南关区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1774, 211, '绿园区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1775, 211, '双阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1776, 211, '净月潭开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1777, 211, '高新技术开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1778, 211, '经济技术开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1779, 211, '汽车产业开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1780, 211, '德惠市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1781, 211, '九台市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1782, 211, '榆树市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1783, 211, '农安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1784, 212, '船营区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1785, 212, '昌邑区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1786, 212, '龙潭区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1787, 212, '丰满区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1788, 212, '蛟河市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1789, 212, '桦甸市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1790, 212, '舒兰市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1791, 212, '磐石市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1792, 212, '永吉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1793, 213, '洮北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1794, 213, '洮南市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1795, 213, '大安市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1796, 213, '镇赉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1797, 213, '通榆县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1798, 214, '江源区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1799, 214, '八道江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1800, 214, '长白', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1801, 214, '临江市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1802, 214, '抚松县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1803, 214, '靖宇县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1804, 215, '龙山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1805, 215, '西安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1806, 215, '东丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1807, 215, '东辽县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1808, 216, '铁西区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1809, 216, '铁东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1810, 216, '伊通', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1811, 216, '公主岭市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1812, 216, '双辽市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1813, 216, '梨树县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1814, 217, '前郭尔罗斯', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1815, 217, '宁江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1816, 217, '长岭县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1817, 217, '乾安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1818, 217, '扶余县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1819, 218, '东昌区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1820, 218, '二道江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1821, 218, '梅河口市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1822, 218, '集安市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1823, 218, '通化县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1824, 218, '辉南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1825, 218, '柳河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1826, 219, '延吉市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1827, 219, '图们市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1828, 219, '敦化市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1829, 219, '珲春市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1830, 219, '龙井市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1831, 219, '和龙市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1832, 219, '安图县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1833, 219, '汪清县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1834, 220, '玄武区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1835, 220, '鼓楼区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1836, 220, '白下区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1837, 220, '建邺区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1838, 220, '秦淮区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1839, 220, '雨花台区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1840, 220, '下关区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1841, 220, '栖霞区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1842, 220, '浦口区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1843, 220, '江宁区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1844, 220, '六合区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1845, 220, '溧水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1846, 220, '高淳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1847, 221, '沧浪区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1848, 221, '金阊区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1849, 221, '平江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1850, 221, '虎丘区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1851, 221, '吴中区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1852, 221, '相城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1853, 221, '园区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1854, 221, '新区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1855, 221, '常熟市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1856, 221, '张家港市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1857, 221, '玉山镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1858, 221, '巴城镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1859, 221, '周市镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1860, 221, '陆家镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1861, 221, '花桥镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1862, 221, '淀山湖镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1863, 221, '张浦镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1864, 221, '周庄镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1865, 221, '千灯镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1866, 221, '锦溪镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1867, 221, '开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1868, 221, '吴江市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1869, 221, '太仓市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1870, 222, '崇安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1871, 222, '北塘区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1872, 222, '南长区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1873, 222, '锡山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1874, 222, '惠山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1875, 222, '滨湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1876, 222, '新区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1877, 222, '江阴市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1878, 222, '宜兴市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1879, 223, '天宁区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1880, 223, '钟楼区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1881, 223, '戚墅堰区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1882, 223, '郊区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1883, 223, '新北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1884, 223, '武进区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1885, 223, '溧阳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1886, 223, '金坛市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1887, 224, '清河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1888, 224, '清浦区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1889, 224, '楚州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1890, 224, '淮阴区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1891, 224, '涟水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1892, 224, '洪泽县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1893, 224, '盱眙县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1894, 224, '金湖县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1895, 225, '新浦区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1896, 225, '连云区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1897, 225, '海州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1898, 225, '赣榆县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1899, 225, '东海县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1900, 225, '灌云县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1901, 225, '灌南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1902, 226, '崇川区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1903, 226, '港闸区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1904, 226, '经济开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1905, 226, '启东市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1906, 226, '如皋市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1907, 226, '通州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1908, 226, '海门市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1909, 226, '海安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1910, 226, '如东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1911, 227, '宿城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1912, 227, '宿豫区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1913, 227, '宿豫县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1914, 227, '沭阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1915, 227, '泗阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1916, 227, '泗洪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1917, 228, '海陵区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1918, 228, '高港区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1919, 228, '兴化市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1920, 228, '靖江市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1921, 228, '泰兴市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1922, 228, '姜堰市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1923, 229, '云龙区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1924, 229, '鼓楼区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1925, 229, '九里区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1926, 229, '贾汪区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1927, 229, '泉山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1928, 229, '新沂市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1929, 229, '邳州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1930, 229, '丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1931, 229, '沛县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1932, 229, '铜山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1933, 229, '睢宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1934, 230, '城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1935, 230, '亭湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1936, 230, '盐都区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1937, 230, '盐都县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1938, 230, '东台市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1939, 230, '大丰市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1940, 230, '响水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1941, 230, '滨海县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1942, 230, '阜宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1943, 230, '射阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1944, 230, '建湖县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1945, 231, '广陵区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1946, 231, '维扬区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1947, 231, '邗江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1948, 231, '仪征市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1949, 231, '高邮市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1950, 231, '江都市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1951, 231, '宝应县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1952, 232, '京口区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1953, 232, '润州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1954, 232, '丹徒区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1955, 232, '丹阳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1956, 232, '扬中市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1957, 232, '句容市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1958, 233, '东湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1959, 233, '西湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1960, 233, '青云谱区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1961, 233, '湾里区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1962, 233, '青山湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1963, 233, '红谷滩新区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1964, 233, '昌北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1965, 233, '高新区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1966, 233, '南昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1967, 233, '新建县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1968, 233, '安义县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1969, 233, '进贤县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1970, 234, '临川区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1971, 234, '南城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1972, 234, '黎川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1973, 234, '南丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1974, 234, '崇仁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1975, 234, '乐安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1976, 234, '宜黄县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1977, 234, '金溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1978, 234, '资溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1979, 234, '东乡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1980, 234, '广昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1981, 235, '章贡区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1982, 235, '于都县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1983, 235, '瑞金市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1984, 235, '南康市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1985, 235, '赣县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1986, 235, '信丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1987, 235, '大余县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1988, 235, '上犹县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1989, 235, '崇义县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1990, 235, '安远县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1991, 235, '龙南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1992, 235, '定南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1993, 235, '全南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1994, 235, '宁都县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1995, 235, '兴国县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1996, 235, '会昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1997, 235, '寻乌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1998, 235, '石城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(1999, 236, '安福县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2000, 236, '吉州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2001, 236, '青原区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2002, 236, '井冈山市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2003, 236, '吉安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2004, 236, '吉水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2005, 236, '峡江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2006, 236, '新干县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2007, 236, '永丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2008, 236, '泰和县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2009, 236, '遂川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2010, 236, '万安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2011, 236, '永新县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2012, 237, '珠山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2013, 237, '昌江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2014, 237, '乐平市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2015, 237, '浮梁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2016, 238, '浔阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2017, 238, '庐山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2018, 238, '瑞昌市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2019, 238, '九江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2020, 238, '武宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2021, 238, '修水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2022, 238, '永修县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2023, 238, '德安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2024, 238, '星子县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2025, 238, '都昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2026, 238, '湖口县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2027, 238, '彭泽县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2028, 239, '安源区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2029, 239, '湘东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2030, 239, '莲花县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2031, 239, '芦溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2032, 239, '上栗县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2033, 240, '信州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2034, 240, '德兴市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2035, 240, '上饶县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2036, 240, '广丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2037, 240, '玉山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2038, 240, '铅山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2039, 240, '横峰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2040, 240, '弋阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2041, 240, '余干县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2042, 240, '波阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2043, 240, '万年县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2044, 240, '婺源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2045, 241, '渝水区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2046, 241, '分宜县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2047, 242, '袁州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2048, 242, '丰城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2049, 242, '樟树市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2050, 242, '高安市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2051, 242, '奉新县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2052, 242, '万载县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2053, 242, '上高县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2054, 242, '宜丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2055, 242, '靖安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2056, 242, '铜鼓县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2057, 243, '月湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2058, 243, '贵溪市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2059, 243, '余江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2060, 244, '沈河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2061, 244, '皇姑区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2062, 244, '和平区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2063, 244, '大东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2064, 244, '铁西区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2065, 244, '苏家屯区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2066, 244, '东陵区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2067, 244, '沈北新区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2068, 244, '于洪区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2069, 244, '浑南新区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2070, 244, '新民市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2071, 244, '辽中县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2072, 244, '康平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2073, 244, '法库县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2074, 245, '西岗区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2075, 245, '中山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2076, 245, '沙河口区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2077, 245, '甘井子区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2078, 245, '旅顺口区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2079, 245, '金州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2080, 245, '开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2081, 245, '瓦房店市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2082, 245, '普兰店市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2083, 245, '庄河市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2084, 245, '长海县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2085, 246, '铁东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2086, 246, '铁西区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2087, 246, '立山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2088, 246, '千山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2089, 246, '岫岩', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2090, 246, '海城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2091, 246, '台安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2092, 247, '本溪', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2093, 247, '平山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2094, 247, '明山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2095, 247, '溪湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2096, 247, '南芬区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2097, 247, '桓仁', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2098, 248, '双塔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2099, 248, '龙城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2100, 248, '喀喇沁左翼蒙古族自治县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2101, 248, '北票市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2102, 248, '凌源市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2103, 248, '朝阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2104, 248, '建平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2105, 249, '振兴区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2106, 249, '元宝区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2107, 249, '振安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2108, 249, '宽甸', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2109, 249, '东港市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2110, 249, '凤城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2111, 250, '顺城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2112, 250, '新抚区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2113, 250, '东洲区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2114, 250, '望花区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2115, 250, '清原', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2116, 250, '新宾', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2117, 250, '抚顺县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2118, 251, '阜新', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2119, 251, '海州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2120, 251, '新邱区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2121, 251, '太平区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2122, 251, '清河门区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2123, 251, '细河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2124, 251, '彰武县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2125, 252, '龙港区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2126, 252, '南票区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2127, 252, '连山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2128, 252, '兴城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2129, 252, '绥中县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2130, 252, '建昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2131, 253, '太和区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2132, 253, '古塔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2133, 253, '凌河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2134, 253, '凌海市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2135, 253, '北镇市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2136, 253, '黑山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2137, 253, '义县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2138, 254, '白塔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2139, 254, '文圣区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2140, 254, '宏伟区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2141, 254, '太子河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2142, 254, '弓长岭区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2143, 254, '灯塔市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2144, 254, '辽阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2145, 255, '双台子区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2146, 255, '兴隆台区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2147, 255, '大洼县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2148, 255, '盘山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2149, 256, '银州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2150, 256, '清河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2151, 256, '调兵山市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2152, 256, '开原市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2153, 256, '铁岭县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2154, 256, '西丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2155, 256, '昌图县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2156, 257, '站前区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2157, 257, '西市区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2158, 257, '鲅鱼圈区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2159, 257, '老边区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2160, 257, '盖州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2161, 257, '大石桥市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2162, 258, '回民区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2163, 258, '玉泉区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2164, 258, '新城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2165, 258, '赛罕区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2166, 258, '清水河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2167, 258, '土默特左旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2168, 258, '托克托县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2169, 258, '和林格尔县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2170, 258, '武川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2171, 259, '阿拉善左旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2172, 259, '阿拉善右旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2173, 259, '额济纳旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2174, 260, '临河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2175, 260, '五原县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2176, 260, '磴口县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2177, 260, '乌拉特前旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2178, 260, '乌拉特中旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2179, 260, '乌拉特后旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2180, 260, '杭锦后旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2181, 261, '昆都仑区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2182, 261, '青山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2183, 261, '东河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2184, 261, '九原区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2185, 261, '石拐区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2186, 261, '白云矿区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2187, 261, '土默特右旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2188, 261, '固阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2189, 261, '达尔罕茂明安联合旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2190, 262, '红山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2191, 262, '元宝山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2192, 262, '松山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2193, 262, '阿鲁科尔沁旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2194, 262, '巴林左旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2195, 262, '巴林右旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2196, 262, '林西县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2197, 262, '克什克腾旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2198, 262, '翁牛特旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2199, 262, '喀喇沁旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2200, 262, '宁城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2201, 262, '敖汉旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2202, 263, '东胜区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2203, 263, '达拉特旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2204, 263, '准格尔旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2205, 263, '鄂托克前旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2206, 263, '鄂托克旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2207, 263, '杭锦旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2208, 263, '乌审旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2209, 263, '伊金霍洛旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2210, 264, '海拉尔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2211, 264, '莫力达瓦', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2212, 264, '满洲里市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2213, 264, '牙克石市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2214, 264, '扎兰屯市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2215, 264, '额尔古纳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2216, 264, '根河市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2217, 264, '阿荣旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2218, 264, '鄂伦春自治旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2219, 264, '鄂温克族自治旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2220, 264, '陈巴尔虎旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2221, 264, '新巴尔虎左旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2222, 264, '新巴尔虎右旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2223, 265, '科尔沁区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2224, 265, '霍林郭勒市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2225, 265, '科尔沁左翼中旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2226, 265, '科尔沁左翼后旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2227, 265, '开鲁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2228, 265, '库伦旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2229, 265, '奈曼旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2230, 265, '扎鲁特旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2231, 266, '海勃湾区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2232, 266, '乌达区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2233, 266, '海南区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2234, 267, '化德县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2235, 267, '集宁区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2236, 267, '丰镇市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2237, 267, '卓资县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2238, 267, '商都县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2239, 267, '兴和县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2240, 267, '凉城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2241, 267, '察哈尔右翼前旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2242, 267, '察哈尔右翼中旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2243, 267, '察哈尔右翼后旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2244, 267, '四子王旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2245, 268, '二连浩特市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2246, 268, '锡林浩特市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2247, 268, '阿巴嘎旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2248, 268, '苏尼特左旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2249, 268, '苏尼特右旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2250, 268, '东乌珠穆沁旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2251, 268, '西乌珠穆沁旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2252, 268, '太仆寺旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2253, 268, '镶黄旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2254, 268, '正镶白旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2255, 268, '正蓝旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2256, 268, '多伦县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2257, 269, '乌兰浩特市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2258, 269, '阿尔山市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2259, 269, '科尔沁右翼前旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2260, 269, '科尔沁右翼中旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2261, 269, '扎赉特旗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2262, 269, '突泉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2263, 270, '西夏区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2264, 270, '金凤区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2265, 270, '兴庆区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2266, 270, '灵武市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2267, 270, '永宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2268, 270, '贺兰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2269, 271, '原州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2270, 271, '海原县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2271, 271, '西吉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2272, 271, '隆德县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2273, 271, '泾源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2274, 271, '彭阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2275, 272, '惠农县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2276, 272, '大武口区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2277, 272, '惠农区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2278, 272, '陶乐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2279, 272, '平罗县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2280, 273, '利通区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2281, 273, '中卫县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2282, 273, '青铜峡市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2283, 273, '中宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2284, 273, '盐池县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2285, 273, '同心县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2286, 274, '沙坡头区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2287, 274, '海原县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2288, 274, '中宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2289, 275, '城中区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2290, 275, '城东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2291, 275, '城西区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2292, 275, '城北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2293, 275, '湟中县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2294, 275, '湟源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2295, 275, '大通', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2296, 276, '玛沁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2297, 276, '班玛县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2298, 276, '甘德县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2299, 276, '达日县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2300, 276, '久治县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2301, 276, '玛多县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2302, 277, '海晏县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2303, 277, '祁连县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2304, 277, '刚察县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2305, 277, '门源', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2306, 278, '平安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2307, 278, '乐都县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2308, 278, '民和', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2309, 278, '互助', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2310, 278, '化隆', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2311, 278, '循化', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2312, 279, '共和县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2313, 279, '同德县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2314, 279, '贵德县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2315, 279, '兴海县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2316, 279, '贵南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2317, 280, '德令哈市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2318, 280, '格尔木市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2319, 280, '乌兰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2320, 280, '都兰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2321, 280, '天峻县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2322, 281, '同仁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2323, 281, '尖扎县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2324, 281, '泽库县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2325, 281, '河南蒙古族自治县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2326, 282, '玉树县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2327, 282, '杂多县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2328, 282, '称多县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2329, 282, '治多县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2330, 282, '囊谦县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2331, 282, '曲麻莱县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2332, 283, '市中区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2333, 283, '历下区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2334, 283, '天桥区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2335, 283, '槐荫区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2336, 283, '历城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2337, 283, '长清区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2338, 283, '章丘市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2339, 283, '平阴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2340, 283, '济阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2341, 283, '商河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2342, 284, '市南区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2343, 284, '市北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2344, 284, '城阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2345, 284, '四方区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2346, 284, '李沧区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2347, 284, '黄岛区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2348, 284, '崂山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2349, 284, '胶州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2350, 284, '即墨市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2351, 284, '平度市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2352, 284, '胶南市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2353, 284, '莱西市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2354, 285, '滨城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2355, 285, '惠民县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2356, 285, '阳信县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2357, 285, '无棣县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2358, 285, '沾化县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2359, 285, '博兴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2360, 285, '邹平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2361, 286, '德城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2362, 286, '陵县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2363, 286, '乐陵市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2364, 286, '禹城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2365, 286, '宁津县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2366, 286, '庆云县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2367, 286, '临邑县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2368, 286, '齐河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2369, 286, '平原县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2370, 286, '夏津县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2371, 286, '武城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2372, 287, '东营区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2373, 287, '河口区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2374, 287, '垦利县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2375, 287, '利津县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2376, 287, '广饶县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2377, 288, '牡丹区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2378, 288, '曹县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2379, 288, '单县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2380, 288, '成武县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2381, 288, '巨野县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2382, 288, '郓城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2383, 288, '鄄城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2384, 288, '定陶县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2385, 288, '东明县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2386, 289, '市中区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2387, 289, '任城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2388, 289, '曲阜市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2389, 289, '兖州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2390, 289, '邹城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2391, 289, '微山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2392, 289, '鱼台县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2393, 289, '金乡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2394, 289, '嘉祥县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2395, 289, '汶上县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2396, 289, '泗水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2397, 289, '梁山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2398, 290, '莱城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2399, 290, '钢城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2400, 291, '东昌府区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2401, 291, '临清市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2402, 291, '阳谷县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2403, 291, '莘县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2404, 291, '茌平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2405, 291, '东阿县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2406, 291, '冠县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2407, 291, '高唐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2408, 292, '兰山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2409, 292, '罗庄区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2410, 292, '河东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2411, 292, '沂南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2412, 292, '郯城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2413, 292, '沂水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2414, 292, '苍山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2415, 292, '费县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2416, 292, '平邑县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2417, 292, '莒南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2418, 292, '蒙阴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2419, 292, '临沭县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2420, 293, '东港区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2421, 293, '岚山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2422, 293, '五莲县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2423, 293, '莒县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2424, 294, '泰山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2425, 294, '岱岳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2426, 294, '新泰市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2427, 294, '肥城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2428, 294, '宁阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2429, 294, '东平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2430, 295, '荣成市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2431, 295, '乳山市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2432, 295, '环翠区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2433, 295, '文登市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2434, 296, '潍城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2435, 296, '寒亭区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2436, 296, '坊子区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2437, 296, '奎文区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2438, 296, '青州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2439, 296, '诸城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2440, 296, '寿光市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2441, 296, '安丘市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2442, 296, '高密市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2443, 296, '昌邑市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2444, 296, '临朐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2445, 296, '昌乐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2446, 297, '芝罘区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2447, 297, '福山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2448, 297, '牟平区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2449, 297, '莱山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2450, 297, '开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2451, 297, '龙口市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2452, 297, '莱阳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2453, 297, '莱州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2454, 297, '蓬莱市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2455, 297, '招远市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2456, 297, '栖霞市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2457, 297, '海阳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2458, 297, '长岛县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2459, 298, '市中区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2460, 298, '山亭区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2461, 298, '峄城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2462, 298, '台儿庄区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2463, 298, '薛城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2464, 298, '滕州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2465, 299, '张店区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2466, 299, '临淄区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2467, 299, '淄川区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2468, 299, '博山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2469, 299, '周村区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2470, 299, '桓台县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2471, 299, '高青县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2472, 299, '沂源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2473, 300, '杏花岭区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2474, 300, '小店区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2475, 300, '迎泽区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2476, 300, '尖草坪区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2477, 300, '万柏林区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2478, 300, '晋源区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2479, 300, '高新开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2480, 300, '民营经济开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2481, 300, '经济技术开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2482, 300, '清徐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2483, 300, '阳曲县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2484, 300, '娄烦县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2485, 300, '古交市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2486, 301, '城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2487, 301, '郊区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2488, 301, '沁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2489, 301, '潞城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2490, 301, '长治县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2491, 301, '襄垣县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2492, 301, '屯留县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2493, 301, '平顺县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2494, 301, '黎城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2495, 301, '壶关县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2496, 301, '长子县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2497, 301, '武乡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2498, 301, '沁源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2499, 302, '城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2500, 302, '矿区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2501, 302, '南郊区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2502, 302, '新荣区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2503, 302, '阳高县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2504, 302, '天镇县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2505, 302, '广灵县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2506, 302, '灵丘县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2507, 302, '浑源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2508, 302, '左云县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2509, 302, '大同县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2510, 303, '城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2511, 303, '高平市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2512, 303, '沁水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2513, 303, '阳城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2514, 303, '陵川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2515, 303, '泽州县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2516, 304, '榆次区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2517, 304, '介休市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2518, 304, '榆社县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2519, 304, '左权县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2520, 304, '和顺县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2521, 304, '昔阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2522, 304, '寿阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2523, 304, '太谷县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2524, 304, '祁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2525, 304, '平遥县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2526, 304, '灵石县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2527, 305, '尧都区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2528, 305, '侯马市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2529, 305, '霍州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2530, 305, '曲沃县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2531, 305, '翼城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2532, 305, '襄汾县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2533, 305, '洪洞县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2534, 305, '吉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2535, 305, '安泽县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2536, 305, '浮山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2537, 305, '古县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2538, 305, '乡宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2539, 305, '大宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2540, 305, '隰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2541, 305, '永和县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2542, 305, '蒲县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2543, 305, '汾西县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2544, 306, '离石市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2545, 306, '离石区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2546, 306, '孝义市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2547, 306, '汾阳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2548, 306, '文水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2549, 306, '交城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2550, 306, '兴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2551, 306, '临县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2552, 306, '柳林县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2553, 306, '石楼县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2554, 306, '岚县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2555, 306, '方山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2556, 306, '中阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2557, 306, '交口县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2558, 307, '朔城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2559, 307, '平鲁区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2560, 307, '山阴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2561, 307, '应县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2562, 307, '右玉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2563, 307, '怀仁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2564, 308, '忻府区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2565, 308, '原平市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2566, 308, '定襄县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2567, 308, '五台县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2568, 308, '代县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2569, 308, '繁峙县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2570, 308, '宁武县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2571, 308, '静乐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2572, 308, '神池县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2573, 308, '五寨县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2574, 308, '岢岚县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2575, 308, '河曲县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2576, 308, '保德县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2577, 308, '偏关县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2578, 309, '城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2579, 309, '矿区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2580, 309, '郊区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2581, 309, '平定县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2582, 309, '盂县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2583, 310, '盐湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2584, 310, '永济市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2585, 310, '河津市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2586, 310, '临猗县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2587, 310, '万荣县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2588, 310, '闻喜县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2589, 310, '稷山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2590, 310, '新绛县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2591, 310, '绛县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2592, 310, '垣曲县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2593, 310, '夏县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2594, 310, '平陆县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2595, 310, '芮城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2596, 311, '莲湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2597, 311, '新城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2598, 311, '碑林区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2599, 311, '雁塔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2600, 311, '灞桥区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2601, 311, '未央区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2602, 311, '阎良区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2603, 311, '临潼区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2604, 311, '长安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2605, 311, '蓝田县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2606, 311, '周至县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2607, 311, '户县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2608, 311, '高陵县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2609, 312, '汉滨区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2610, 312, '汉阴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2611, 312, '石泉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2612, 312, '宁陕县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2613, 312, '紫阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2614, 312, '岚皋县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2615, 312, '平利县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2616, 312, '镇坪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2617, 312, '旬阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2618, 312, '白河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2619, 313, '陈仓区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2620, 313, '渭滨区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2621, 313, '金台区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2622, 313, '凤翔县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2623, 313, '岐山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2624, 313, '扶风县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2625, 313, '眉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2626, 313, '陇县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2627, 313, '千阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2628, 313, '麟游县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2629, 313, '凤县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2630, 313, '太白县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2631, 314, '汉台区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2632, 314, '南郑县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2633, 314, '城固县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2634, 314, '洋县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2635, 314, '西乡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2636, 314, '勉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2637, 314, '宁强县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2638, 314, '略阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2639, 314, '镇巴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2640, 314, '留坝县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2641, 314, '佛坪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2642, 315, '商州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2643, 315, '洛南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2644, 315, '丹凤县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2645, 315, '商南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2646, 315, '山阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2647, 315, '镇安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2648, 315, '柞水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2649, 316, '耀州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2650, 316, '王益区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2651, 316, '印台区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2652, 316, '宜君县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2653, 317, '临渭区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2654, 317, '韩城市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2655, 317, '华阴市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2656, 317, '华县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2657, 317, '潼关县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2658, 317, '大荔县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2659, 317, '合阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2660, 317, '澄城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2661, 317, '蒲城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2662, 317, '白水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2663, 317, '富平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2664, 318, '秦都区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2665, 318, '渭城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2666, 318, '杨陵区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2667, 318, '兴平市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2668, 318, '三原县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2669, 318, '泾阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2670, 318, '乾县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2671, 318, '礼泉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2672, 318, '永寿县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2673, 318, '彬县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2674, 318, '长武县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2675, 318, '旬邑县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2676, 318, '淳化县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2677, 318, '武功县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2678, 319, '吴起县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2679, 319, '宝塔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2680, 319, '延长县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2681, 319, '延川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2682, 319, '子长县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2683, 319, '安塞县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2684, 319, '志丹县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2685, 319, '甘泉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2686, 319, '富县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2687, 319, '洛川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2688, 319, '宜川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2689, 319, '黄龙县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2690, 319, '黄陵县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2691, 320, '榆阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2692, 320, '神木县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2693, 320, '府谷县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2694, 320, '横山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2695, 320, '靖边县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2696, 320, '定边县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2697, 320, '绥德县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2698, 320, '米脂县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2699, 320, '佳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2700, 320, '吴堡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2701, 320, '清涧县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2702, 320, '子洲县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2703, 321, '长宁区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2704, 321, '闸北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2705, 321, '闵行区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2706, 321, '徐汇区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2707, 321, '浦东新区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2708, 321, '杨浦区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2709, 321, '普陀区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2710, 321, '静安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2711, 321, '卢湾区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2712, 321, '虹口区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2713, 321, '黄浦区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2714, 321, '南汇区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2715, 321, '松江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2716, 321, '嘉定区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2717, 321, '宝山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2718, 321, '青浦区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2719, 321, '金山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2720, 321, '奉贤区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2721, 321, '崇明县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2722, 322, '青羊区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2723, 322, '锦江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2724, 322, '金牛区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2725, 322, '武侯区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2726, 322, '成华区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2727, 322, '龙泉驿区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2728, 322, '青白江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2729, 322, '新都区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2730, 322, '温江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2731, 322, '高新区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2732, 322, '高新西区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2733, 322, '都江堰市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2734, 322, '彭州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2735, 322, '邛崃市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2736, 322, '崇州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2737, 322, '金堂县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2738, 322, '双流县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2739, 322, '郫县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2740, 322, '大邑县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2741, 322, '蒲江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2742, 322, '新津县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2743, 322, '都江堰市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2744, 322, '彭州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2745, 322, '邛崃市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2746, 322, '崇州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2747, 322, '金堂县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2748, 322, '双流县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2749, 322, '郫县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2750, 322, '大邑县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2751, 322, '蒲江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2752, 322, '新津县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2753, 323, '涪城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2754, 323, '游仙区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2755, 323, '江油市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2756, 323, '盐亭县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2757, 323, '三台县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2758, 323, '平武县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2759, 323, '安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2760, 323, '梓潼县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2761, 323, '北川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2762, 324, '马尔康县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2763, 324, '汶川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2764, 324, '理县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2765, 324, '茂县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2766, 324, '松潘县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2767, 324, '九寨沟县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2768, 324, '金川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2769, 324, '小金县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2770, 324, '黑水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2771, 324, '壤塘县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2772, 324, '阿坝县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2773, 324, '若尔盖县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2774, 324, '红原县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2775, 325, '巴州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2776, 325, '通江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2777, 325, '南江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2778, 325, '平昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2779, 326, '通川区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2780, 326, '万源市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2781, 326, '达县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2782, 326, '宣汉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2783, 326, '开江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2784, 326, '大竹县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2785, 326, '渠县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2786, 327, '旌阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2787, 327, '广汉市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2788, 327, '什邡市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2789, 327, '绵竹市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2790, 327, '罗江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2791, 327, '中江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2792, 328, '康定县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2793, 328, '丹巴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2794, 328, '泸定县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2795, 328, '炉霍县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2796, 328, '九龙县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2797, 328, '甘孜县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2798, 328, '雅江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2799, 328, '新龙县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2800, 328, '道孚县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2801, 328, '白玉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2802, 328, '理塘县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2803, 328, '德格县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2804, 328, '乡城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2805, 328, '石渠县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2806, 328, '稻城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2807, 328, '色达县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2808, 328, '巴塘县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2809, 328, '得荣县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2810, 329, '广安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2811, 329, '华蓥市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2812, 329, '岳池县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2813, 329, '武胜县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2814, 329, '邻水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2815, 330, '利州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2816, 330, '元坝区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2817, 330, '朝天区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2818, 330, '旺苍县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2819, 330, '青川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2820, 330, '剑阁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2821, 330, '苍溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2822, 331, '峨眉山市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2823, 331, '乐山市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2824, 331, '犍为县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2825, 331, '井研县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2826, 331, '夹江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2827, 331, '沐川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2828, 331, '峨边', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2829, 331, '马边', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2830, 332, '西昌市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2831, 332, '盐源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2832, 332, '德昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2833, 332, '会理县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2834, 332, '会东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2835, 332, '宁南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2836, 332, '普格县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2837, 332, '布拖县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2838, 332, '金阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2839, 332, '昭觉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2840, 332, '喜德县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2841, 332, '冕宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2842, 332, '越西县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2843, 332, '甘洛县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2844, 332, '美姑县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2845, 332, '雷波县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2846, 332, '木里', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2847, 333, '东坡区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2848, 333, '仁寿县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2849, 333, '彭山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2850, 333, '洪雅县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2851, 333, '丹棱县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2852, 333, '青神县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2853, 334, '阆中市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2854, 334, '南部县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2855, 334, '营山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2856, 334, '蓬安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2857, 334, '仪陇县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2858, 334, '顺庆区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2859, 334, '高坪区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2860, 334, '嘉陵区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2861, 334, '西充县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2862, 335, '市中区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2863, 335, '东兴区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2864, 335, '威远县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2865, 335, '资中县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2866, 335, '隆昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2867, 336, '东  区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2868, 336, '西  区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2869, 336, '仁和区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2870, 336, '米易县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2871, 336, '盐边县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2872, 337, '船山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2873, 337, '安居区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2874, 337, '蓬溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2875, 337, '射洪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2876, 337, '大英县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2877, 338, '雨城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2878, 338, '名山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2879, 338, '荥经县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2880, 338, '汉源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2881, 338, '石棉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2882, 338, '天全县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2883, 338, '芦山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2884, 338, '宝兴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2885, 339, '翠屏区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2886, 339, '宜宾县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2887, 339, '南溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2888, 339, '江安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2889, 339, '长宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2890, 339, '高县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2891, 339, '珙县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2892, 339, '筠连县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2893, 339, '兴文县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2894, 339, '屏山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2895, 340, '雁江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2896, 340, '简阳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2897, 340, '安岳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2898, 340, '乐至县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2899, 341, '大安区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2900, 341, '自流井区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2901, 341, '贡井区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2902, 341, '沿滩区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2903, 341, '荣县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2904, 341, '富顺县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2905, 342, '江阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2906, 342, '纳溪区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2907, 342, '龙马潭区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2908, 342, '泸县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2909, 342, '合江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2910, 342, '叙永县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2911, 342, '古蔺县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2912, 343, '和平区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2913, 343, '河西区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2914, 343, '南开区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2915, 343, '河北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2916, 343, '河东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2917, 343, '红桥区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2918, 343, '东丽区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2919, 343, '津南区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2920, 343, '西青区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2921, 343, '北辰区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2922, 343, '塘沽区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2923, 343, '汉沽区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2924, 343, '大港区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2925, 343, '武清区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2926, 343, '宝坻区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2927, 343, '经济开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2928, 343, '宁河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2929, 343, '静海县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2930, 343, '蓟县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2931, 344, '城关区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2932, 344, '林周县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2933, 344, '当雄县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2934, 344, '尼木县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2935, 344, '曲水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2936, 344, '堆龙德庆县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2937, 344, '达孜县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2938, 344, '墨竹工卡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2939, 345, '噶尔县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2940, 345, '普兰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2941, 345, '札达县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2942, 345, '日土县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2943, 345, '革吉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2944, 345, '改则县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2945, 345, '措勤县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2946, 346, '昌都县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2947, 346, '江达县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2948, 346, '贡觉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2949, 346, '类乌齐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2950, 346, '丁青县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2951, 346, '察雅县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2952, 346, '八宿县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2953, 346, '左贡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2954, 346, '芒康县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2955, 346, '洛隆县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2956, 346, '边坝县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2957, 347, '林芝县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2958, 347, '工布江达县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2959, 347, '米林县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2960, 347, '墨脱县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2961, 347, '波密县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2962, 347, '察隅县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2963, 347, '朗县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2964, 348, '那曲县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2965, 348, '嘉黎县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2966, 348, '比如县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2967, 348, '聂荣县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2968, 348, '安多县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2969, 348, '申扎县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2970, 348, '索县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2971, 348, '班戈县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2972, 348, '巴青县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2973, 348, '尼玛县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2974, 349, '日喀则市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2975, 349, '南木林县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2976, 349, '江孜县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2977, 349, '定日县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2978, 349, '萨迦县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2979, 349, '拉孜县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2980, 349, '昂仁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2981, 349, '谢通门县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2982, 349, '白朗县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2983, 349, '仁布县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2984, 349, '康马县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2985, 349, '定结县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2986, 349, '仲巴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2987, 349, '亚东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2988, 349, '吉隆县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2989, 349, '聂拉木县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2990, 349, '萨嘎县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2991, 349, '岗巴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2992, 350, '乃东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2993, 350, '扎囊县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2994, 350, '贡嘎县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2995, 350, '桑日县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2996, 350, '琼结县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2997, 350, '曲松县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2998, 350, '措美县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(2999, 350, '洛扎县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3000, 350, '加查县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3001, 350, '隆子县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3002, 350, '错那县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3003, 350, '浪卡子县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3004, 351, '天山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3005, 351, '沙依巴克区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3006, 351, '新市区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3007, 351, '水磨沟区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3008, 351, '头屯河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3009, 351, '达坂城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3010, 351, '米东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3011, 351, '乌鲁木齐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3012, 352, '阿克苏市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3013, 352, '温宿县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3014, 352, '库车县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3015, 352, '沙雅县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3016, 352, '新和县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3017, 352, '拜城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3018, 352, '乌什县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3019, 352, '阿瓦提县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3020, 352, '柯坪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3021, 353, '阿拉尔市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3022, 354, '库尔勒市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3023, 354, '轮台县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3024, 354, '尉犁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3025, 354, '若羌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3026, 354, '且末县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3027, 354, '焉耆', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3028, 354, '和静县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3029, 354, '和硕县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3030, 354, '博湖县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3031, 355, '博乐市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3032, 355, '精河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3033, 355, '温泉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3034, 356, '呼图壁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3035, 356, '米泉市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3036, 356, '昌吉市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3037, 356, '阜康市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3038, 356, '玛纳斯县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3039, 356, '奇台县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3040, 356, '吉木萨尔县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3041, 356, '木垒', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3042, 357, '哈密市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3043, 357, '伊吾县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3044, 357, '巴里坤', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3045, 358, '和田市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3046, 358, '和田县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3047, 358, '墨玉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3048, 358, '皮山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3049, 358, '洛浦县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3050, 358, '策勒县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3051, 358, '于田县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3052, 358, '民丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3053, 359, '喀什市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3054, 359, '疏附县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3055, 359, '疏勒县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3056, 359, '英吉沙县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3057, 359, '泽普县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3058, 359, '莎车县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3059, 359, '叶城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3060, 359, '麦盖提县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3061, 359, '岳普湖县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3062, 359, '伽师县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3063, 359, '巴楚县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3064, 359, '塔什库尔干', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3065, 360, '克拉玛依市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3066, 361, '阿图什市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3067, 361, '阿克陶县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3068, 361, '阿合奇县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3069, 361, '乌恰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3070, 362, '石河子市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3071, 363, '图木舒克市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3072, 364, '吐鲁番市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3073, 364, '鄯善县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3074, 364, '托克逊县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3075, 365, '五家渠市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3076, 366, '阿勒泰市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3077, 366, '布克赛尔', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3078, 366, '伊宁市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3079, 366, '布尔津县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3080, 366, '奎屯市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3081, 366, '乌苏市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3082, 366, '额敏县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3083, 366, '富蕴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3084, 366, '伊宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3085, 366, '福海县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3086, 366, '霍城县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3087, 366, '沙湾县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3088, 366, '巩留县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3089, 366, '哈巴河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3090, 366, '托里县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3091, 366, '青河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3092, 366, '新源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3093, 366, '裕民县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3094, 366, '和布克赛尔', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3095, 366, '吉木乃县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3096, 366, '昭苏县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3097, 366, '特克斯县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3098, 366, '尼勒克县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3099, 366, '察布查尔', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3100, 367, '盘龙区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3101, 367, '五华区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3102, 367, '官渡区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3103, 367, '西山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3104, 367, '东川区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3105, 367, '安宁市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3106, 367, '呈贡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3107, 367, '晋宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3108, 367, '富民县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3109, 367, '宜良县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3110, 367, '嵩明县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3111, 367, '石林县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3112, 367, '禄劝', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3113, 367, '寻甸', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3114, 368, '兰坪', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3115, 368, '泸水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3116, 368, '福贡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3117, 368, '贡山', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3118, 369, '宁洱', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3119, 369, '思茅区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3120, 369, '墨江', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3121, 369, '景东', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3122, 369, '景谷', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3123, 369, '镇沅', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3124, 369, '江城', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3125, 369, '孟连', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3126, 369, '澜沧', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3127, 369, '西盟', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3128, 370, '古城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3129, 370, '宁蒗', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3130, 370, '玉龙', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3131, 370, '永胜县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3132, 370, '华坪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3133, 371, '隆阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3134, 371, '施甸县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3135, 371, '腾冲县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3136, 371, '龙陵县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3137, 371, '昌宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3138, 372, '楚雄市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3139, 372, '双柏县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3140, 372, '牟定县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3141, 372, '南华县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3142, 372, '姚安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3143, 372, '大姚县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3144, 372, '永仁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3145, 372, '元谋县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3146, 372, '武定县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3147, 372, '禄丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3148, 373, '大理市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3149, 373, '祥云县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3150, 373, '宾川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3151, 373, '弥渡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3152, 373, '永平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3153, 373, '云龙县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3154, 373, '洱源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3155, 373, '剑川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3156, 373, '鹤庆县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3157, 373, '漾濞', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3158, 373, '南涧', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3159, 373, '巍山', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3160, 374, '潞西市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3161, 374, '瑞丽市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3162, 374, '梁河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3163, 374, '盈江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3164, 374, '陇川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3165, 375, '香格里拉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3166, 375, '德钦县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3167, 375, '维西', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3168, 376, '泸西县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3169, 376, '蒙自县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3170, 376, '个旧市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3171, 376, '开远市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3172, 376, '绿春县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3173, 376, '建水县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3174, 376, '石屏县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3175, 376, '弥勒县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3176, 376, '元阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3177, 376, '红河县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3178, 376, '金平', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3179, 376, '河口', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3180, 376, '屏边', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3181, 377, '临翔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3182, 377, '凤庆县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3183, 377, '云县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3184, 377, '永德县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3185, 377, '镇康县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3186, 377, '双江', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3187, 377, '耿马', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3188, 377, '沧源', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3189, 378, '麒麟区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3190, 378, '宣威市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3191, 378, '马龙县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3192, 378, '陆良县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3193, 378, '师宗县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3194, 378, '罗平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3195, 378, '富源县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3196, 378, '会泽县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3197, 378, '沾益县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3198, 379, '文山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3199, 379, '砚山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3200, 379, '西畴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3201, 379, '麻栗坡县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3202, 379, '马关县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3203, 379, '丘北县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3204, 379, '广南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3205, 379, '富宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3206, 380, '景洪市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3207, 380, '勐海县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3208, 380, '勐腊县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3209, 381, '红塔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3210, 381, '江川县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3211, 381, '澄江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3212, 381, '通海县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3213, 381, '华宁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3214, 381, '易门县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3215, 381, '峨山', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3216, 381, '新平', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3217, 381, '元江', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3218, 382, '昭阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3219, 382, '鲁甸县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3220, 382, '巧家县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3221, 382, '盐津县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3222, 382, '大关县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3223, 382, '永善县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3224, 382, '绥江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3225, 382, '镇雄县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3226, 382, '彝良县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3227, 382, '威信县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3228, 382, '水富县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3229, 383, '西湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3230, 383, '上城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3231, 383, '下城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3232, 383, '拱墅区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3233, 383, '滨江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3234, 383, '江干区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3235, 383, '萧山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3236, 383, '余杭区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3237, 383, '市郊', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3238, 383, '建德市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3239, 383, '富阳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3240, 383, '临安市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3241, 383, '桐庐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3242, 383, '淳安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3243, 384, '吴兴区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3244, 384, '南浔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3245, 384, '德清县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3246, 384, '长兴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3247, 384, '安吉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3248, 385, '南湖区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3249, 385, '秀洲区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3250, 385, '海宁市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3251, 385, '嘉善县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3252, 385, '平湖市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3253, 385, '桐乡市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3254, 385, '海盐县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3255, 386, '婺城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3256, 386, '金东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3257, 386, '兰溪市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3258, 386, '市区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3259, 386, '佛堂镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3260, 386, '上溪镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3261, 386, '义亭镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3262, 386, '大陈镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3263, 386, '苏溪镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3264, 386, '赤岸镇', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3265, 386, '东阳市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3266, 386, '永康市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3267, 386, '武义县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3268, 386, '浦江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3269, 386, '磐安县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3270, 387, '莲都区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3271, 387, '龙泉市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3272, 387, '青田县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3273, 387, '缙云县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3274, 387, '遂昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3275, 387, '松阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3276, 387, '云和县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3277, 387, '庆元县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3278, 387, '景宁', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3279, 388, '海曙区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3280, 388, '江东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3281, 388, '江北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3282, 388, '镇海区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3283, 388, '北仑区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3284, 388, '鄞州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3285, 388, '余姚市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3286, 388, '慈溪市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3287, 388, '奉化市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3288, 388, '象山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3289, 388, '宁海县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3290, 389, '越城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3291, 389, '上虞市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3292, 389, '嵊州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3293, 389, '绍兴县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3294, 389, '新昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3295, 389, '诸暨市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3296, 390, '椒江区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3297, 390, '黄岩区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3298, 390, '路桥区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3299, 390, '温岭市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3300, 390, '临海市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3301, 390, '玉环县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3302, 390, '三门县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3303, 390, '天台县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3304, 390, '仙居县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3305, 391, '鹿城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3306, 391, '龙湾区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3307, 391, '瓯海区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3308, 391, '瑞安市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3309, 391, '乐清市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3310, 391, '洞头县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3311, 391, '永嘉县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3312, 391, '平阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3313, 391, '苍南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3314, 391, '文成县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3315, 391, '泰顺县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3316, 392, '定海区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3317, 392, '普陀区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3318, 392, '岱山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3319, 392, '嵊泗县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3320, 393, '衢州市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3321, 393, '江山市', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3322, 393, '常山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3323, 393, '开化县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3324, 393, '龙游县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3325, 394, '合川区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3326, 394, '江津区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3327, 394, '南川区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3328, 394, '永川区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3329, 394, '南岸区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3330, 394, '渝北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3331, 394, '万盛区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3332, 394, '大渡口区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3333, 394, '万州区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3334, 394, '北碚区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3335, 394, '沙坪坝区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3336, 394, '巴南区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3337, 394, '涪陵区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3338, 394, '江北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3339, 394, '九龙坡区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3340, 394, '渝中区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3341, 394, '黔江开发区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3342, 394, '长寿区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3343, 394, '双桥区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3344, 394, '綦江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3345, 394, '潼南县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3346, 394, '铜梁县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3347, 394, '大足县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3348, 394, '荣昌县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3349, 394, '璧山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3350, 394, '垫江县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3351, 394, '武隆县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3352, 394, '丰都县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3353, 394, '城口县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3354, 394, '梁平县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3355, 394, '开县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3356, 394, '巫溪县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3357, 394, '巫山县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3358, 394, '奉节县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3359, 394, '云阳县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3360, 394, '忠县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3361, 394, '石柱', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3362, 394, '彭水', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3363, 394, '酉阳', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3364, 394, '秀山', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3365, 395, '沙田区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3366, 395, '东区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3367, 395, '观塘区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3368, 395, '黄大仙区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3369, 395, '九龙城区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3370, 395, '屯门区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3371, 395, '葵青区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3372, 395, '元朗区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3373, 395, '深水埗区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3374, 395, '西贡区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3375, 395, '大埔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3376, 395, '湾仔区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3377, 395, '油尖旺区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3378, 395, '北区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3379, 395, '南区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3380, 395, '荃湾区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3381, 395, '中西区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3382, 395, '离岛区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3383, 396, '澳门', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3384, 397, '台北', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3385, 397, '高雄', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3386, 397, '基隆', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3387, 397, '台中', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3388, 397, '台南', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3389, 397, '新竹', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3390, 397, '嘉义', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3391, 397, '宜兰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3392, 397, '桃园县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3393, 397, '苗栗县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3394, 397, '彰化县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3395, 397, '南投县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3396, 397, '云林县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3397, 397, '屏东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3398, 397, '台东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3399, 397, '花莲县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3400, 397, '澎湖县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3401, 3, '合肥', 2);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3402, 3401, '庐阳区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3403, 3401, '瑶海区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3404, 3401, '蜀山区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3405, 3401, '包河区', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3406, 3401, '长丰县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3407, 3401, '肥东县', 3);
INSERT INTO `region` (`region_id`, `parent_id`, `region_name`, `region_type`) VALUES(3408, 3401, '肥西县', 3);

-- --------------------------------------------------------

--
-- 表的结构 `reply`
--

CREATE TABLE IF NOT EXISTS `reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '留言回复主键',
  `message_id` int(10) unsigned NOT NULL COMMENT '留言id',
  `title` varchar(40) NOT NULL COMMENT '回复内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='留言回复' AUTO_INCREMENT=22 ;

--
-- 转存表中的数据 `reply`
--

INSERT INTO `reply` (`id`, `message_id`, `title`) VALUES(6, 6, 'asd0');
INSERT INTO `reply` (`id`, `message_id`, `title`) VALUES(7, 6, 'aseeeeeeee');
INSERT INTO `reply` (`id`, `message_id`, `title`) VALUES(8, 6, 'aseeeeeeee');
INSERT INTO `reply` (`id`, `message_id`, `title`) VALUES(19, 17, 'asd0');
INSERT INTO `reply` (`id`, `message_id`, `title`) VALUES(20, 17, 'asd1');
INSERT INTO `reply` (`id`, `message_id`, `title`) VALUES(21, 17, 'asd2');

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

INSERT INTO `resource_type` (`id`, `label`, `code`, `main_table`, `user_table`, `fields`, `foreigh_key`, `is_system`, `is_enabled`, `is_deleted`, `note`) VALUES(1, '菜单', 'MENU', 'menu', 'rel_user_menu', 'id', 'menu_id', 1, 1, 0, '');
INSERT INTO `resource_type` (`id`, `label`, `code`, `main_table`, `user_table`, `fields`, `foreigh_key`, `is_system`, `is_enabled`, `is_deleted`, `note`) VALUES(2, '按钮', 'BUTTON', 'button', 'rel_user_button', 'id', 'button_id', 1, 1, 0, '');
INSERT INTO `resource_type` (`id`, `label`, `code`, `main_table`, `user_table`, `fields`, `foreigh_key`, `is_system`, `is_enabled`, `is_deleted`, `note`) VALUES(3, '操作', 'OPERATION', 'operation', 'rel_user_operation', 'id', 'operation_id', 1, 1, 0, '');
INSERT INTO `resource_type` (`id`, `label`, `code`, `main_table`, `user_table`, `fields`, `foreigh_key`, `is_system`, `is_enabled`, `is_deleted`, `note`) VALUES(4, '数据', 'DATA', 'control', 'rel_user_control', 'id', 'c_id', 1, 1, 0, '');

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

INSERT INTO `role` (`id`, `label`, `code`, `note`, `is_deleted`, `is_system`) VALUES(1, '授权管理员', '123', '授权管理员', 0, 1);
INSERT INTO `role` (`id`, `label`, `code`, `note`, `is_deleted`, `is_system`) VALUES(2, '普通用户', '124', '普通用户', 0, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色按钮权限表' AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- 表的结构 `role_menu_permission`
--

CREATE TABLE IF NOT EXISTS `role_menu_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色权限表' AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='操作权限表' AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- 表的结构 `role_subdetail_button_permission`
--

CREATE TABLE IF NOT EXISTS `role_subdetail_button_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `parent_id` int(11) NOT NULL COMMENT '所属权限',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色明细按钮权限表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `role_subdetail_operation_permission`
--

CREATE TABLE IF NOT EXISTS `role_subdetail_operation_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `parent_id` int(11) NOT NULL COMMENT '所属权限',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色明细操作权限表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `role_subdetail_permission`
--

CREATE TABLE IF NOT EXISTS `role_subdetail_permission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `parent_id` int(11) NOT NULL COMMENT '主对象权限id',
  `permission_id` int(11) NOT NULL COMMENT '明细对象权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色明细对象权限表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `role_user`
--

CREATE TABLE IF NOT EXISTS `role_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '序号',
  `role_id` int(11) DEFAULT NULL COMMENT '角色ID',
  `user_id` int(11) DEFAULT NULL COMMENT '用户ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色用户表' AUTO_INCREMENT=3 ;

--
-- 转存表中的数据 `role_user`
--

INSERT INTO `role_user` (`id`, `role_id`, `user_id`) VALUES(1, 1, 2);
INSERT INTO `role_user` (`id`, `role_id`, `user_id`) VALUES(2, 2, 3);

-- --------------------------------------------------------

--
-- 表的结构 `role_view_button_permission`
--

CREATE TABLE IF NOT EXISTS `role_view_button_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `parent_id` int(11) NOT NULL COMMENT '所属权限菜单',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='角色查看按钮权限表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `sales_channels`
--

CREATE TABLE IF NOT EXISTS `sales_channels` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '序号',
  `channel_name` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT '渠道名称',
  `channel_code` varchar(20) COLLATE utf8_bin DEFAULT NULL COMMENT '渠道编码',
  `channel_class` tinyint(4) unsigned DEFAULT NULL COMMENT '1线上，2线下',
  `channel_type` tinyint(4) unsigned DEFAULT NULL COMMENT '1部门，2体验店，3公司',
  `channel_own_id` int(8) DEFAULT NULL COMMENT '所属ID',
  `channel_own` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT '渠道所属',
  `addby_id` int(11) unsigned DEFAULT NULL COMMENT '创建人',
  `addby_time` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `updateby_id` int(11) unsigned DEFAULT NULL COMMENT '更新人',
  `update_time` int(11) unsigned DEFAULT NULL COMMENT '修改时间',
  `is_deleted` tinyint(1) unsigned DEFAULT '0' COMMENT '删除标识',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='销售渠道表' AUTO_INCREMENT=13 ;

--
-- 转存表中的数据 `sales_channels`
--

INSERT INTO `sales_channels` (`id`, `channel_name`, `channel_code`, `channel_class`, `channel_type`, `channel_own_id`, `channel_own`, `addby_id`, `addby_time`, `updateby_id`, `update_time`, `is_deleted`) VALUES(9, '京东商城', 'JINGDONGSHANGCHENG', 1, 1, 2, '线上事业部', NULL, NULL, NULL, NULL, 0);
INSERT INTO `sales_channels` (`id`, `channel_name`, `channel_code`, `channel_class`, `channel_type`, `channel_own_id`, `channel_own`, `addby_id`, `addby_time`, `updateby_id`, `update_time`, `is_deleted`) VALUES(10, '天猫商城', 'TIANMAOSHANGCHENG', 1, 3, 1, '', NULL, NULL, NULL, NULL, 0);
INSERT INTO `sales_channels` (`id`, `channel_name`, `channel_code`, `channel_class`, `channel_type`, `channel_own_id`, `channel_own`, `addby_id`, `addby_time`, `updateby_id`, `update_time`, `is_deleted`) VALUES(11, '北京店', 'BEIJINGDIAN', 2, 2, 8, '体验店3', NULL, NULL, NULL, NULL, 0);
INSERT INTO `sales_channels` (`id`, `channel_name`, `channel_code`, `channel_class`, `channel_type`, `channel_own_id`, `channel_own`, `addby_id`, `addby_time`, `updateby_id`, `update_time`, `is_deleted`) VALUES(12, '淘宝一店', 'TAOBAO1DIAN', 1, 3, 1, '', NULL, NULL, NULL, NULL, 0);

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
-- 表的结构 `shop_cfg`
--

CREATE TABLE IF NOT EXISTS `shop_cfg` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `shop_name` varchar(128) NOT NULL DEFAULT '' COMMENT '名称',
  `short_name` varchar(64) NOT NULL DEFAULT '' COMMENT '简称(拼音)',
  `shop_address` varchar(64) NOT NULL DEFAULT '' COMMENT '体验店地址',
  `shop_phone` varchar(128) NOT NULL DEFAULT '' COMMENT '电话',
  `shop_time` varchar(30) NOT NULL DEFAULT '' COMMENT '营业时间',
  `shop_traffic` text NOT NULL COMMENT '交通情况',
  `shop_dec` text NOT NULL COMMENT '体验店描述',
  `second_url` varchar(30) NOT NULL DEFAULT '' COMMENT '体验店二级域名',
  `order` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `create_user` int(11) DEFAULT NULL COMMENT '创建人',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `is_delete` int(11) DEFAULT '0' COMMENT '是否有效 0有效1无效',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='体验店表' AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='系统访问日志' AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户表' AUTO_INCREMENT=7 ;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `account`, `password`, `code`, `real_name`, `is_on_work`, `is_enabled`, `gender`, `birthday`, `mobile`, `phone`, `qq`, `email`, `address`, `join_date`, `user_type`, `up_pwd_date`, `uin`, `is_system`, `is_deleted`) VALUES(1, 'admin', '6248883015fef52f23785ab26e187635', '10000', '', 1, 1, 0, '2014-11-11', '13800138000', '', '', 'yangfuyou@kela.cn', '', '2014-11-11', 1, 0, '', 1, 0);
INSERT INTO `user` (`id`, `account`, `password`, `code`, `real_name`, `is_on_work`, `is_enabled`, `gender`, `birthday`, `mobile`, `phone`, `qq`, `email`, `address`, `join_date`, `user_type`, `up_pwd_date`, `uin`, `is_system`, `is_deleted`) VALUES(2, 'yangfuyou', '6248883015fef52f23785ab26e187635', '10001', '杨福友', 1, 1, 0, '', '13800138000', '', '', 'yangfuyou@kela.cn', '', '', 2, 0, NULL, 0, 0);
INSERT INTO `user` (`id`, `account`, `password`, `code`, `real_name`, `is_on_work`, `is_enabled`, `gender`, `birthday`, `mobile`, `phone`, `qq`, `email`, `address`, `join_date`, `user_type`, `up_pwd_date`, `uin`, `is_system`, `is_deleted`) VALUES(3, 'demo', '6248883015fef52f23785ab26e187635', '10003', '测试', 1, 1, 0, '', '13810363671', '', '123456', 'yangfuyou@kela.cn', '', '', 3, 0, NULL, 0, 0);
INSERT INTO `user` (`id`, `account`, `password`, `code`, `real_name`, `is_on_work`, `is_enabled`, `gender`, `birthday`, `mobile`, `phone`, `qq`, `email`, `address`, `join_date`, `user_type`, `up_pwd_date`, `uin`, `is_system`, `is_deleted`) VALUES(4, 'wangshuai', 'O0N9ExgEzvi3p2F68qG24D8qTkJZZL6W', '123', '王帅', 1, 1, 0, '2015-01-16', '13811111000', '123', '', '1@1.cn', '', '', 2, 0, NULL, 0, 0);
INSERT INTO `user` (`id`, `account`, `password`, `code`, `real_name`, `is_on_work`, `is_enabled`, `gender`, `birthday`, `mobile`, `phone`, `qq`, `email`, `address`, `join_date`, `user_type`, `up_pwd_date`, `uin`, `is_system`, `is_deleted`) VALUES(5, 'kela', '6248883015fef52f23785ab26e187635', '10005', '', 1, 1, 0, '', '13800138000', '', '', '1@1.cn', '', '', 3, 0, NULL, 0, 0);
INSERT INTO `user` (`id`, `account`, `password`, `code`, `real_name`, `is_on_work`, `is_enabled`, `gender`, `birthday`, `mobile`, `phone`, `qq`, `email`, `address`, `join_date`, `user_type`, `up_pwd_date`, `uin`, `is_system`, `is_deleted`) VALUES(6, 'changmark', '27c28fe3b722b09b9a7f79255cd45e29', '201', '张园园', 1, 1, 0, '2015-01-18', '13522424414', '', '1041522879', 'zhangyuanyuan@kela.cn', 'AAAAAAAAAAAAAAA', '2015-01-18', 1, 0, NULL, 0, 0);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户按钮权限表' AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- 表的结构 `user_menu_permission`
--

CREATE TABLE IF NOT EXISTS `user_menu_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户菜单权限表' AUTO_INCREMENT=1 ;


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户操作权限表' AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- 表的结构 `user_subdetail_button_permission`
--

CREATE TABLE IF NOT EXISTS `user_subdetail_button_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `parent_id` int(11) NOT NULL COMMENT '所属权限',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户明细按钮权限表' AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- 表的结构 `user_subdetail_operation_permission`
--

CREATE TABLE IF NOT EXISTS `user_subdetail_operation_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `parent_id` int(11) NOT NULL COMMENT '所属权限',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户明细操作权限表' AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- 表的结构 `user_subdetail_permission`
--

CREATE TABLE IF NOT EXISTS `user_subdetail_permission` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `parent_id` int(11) NOT NULL COMMENT '主对象权限id',
  `permission_id` int(11) NOT NULL COMMENT '明细对象权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户明细对象权限表' AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- 表的结构 `user_view_button_permission`
--

CREATE TABLE IF NOT EXISTS `user_view_button_permission` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  `parent_id` int(11) NOT NULL COMMENT '所属权限菜单',
  `permission_id` int(11) NOT NULL COMMENT '权限id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户查看按钮权限表' AUTO_INCREMENT=1 ;


--初始id值
-- control id
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1000, 'x', 'x', 0, 0, 0, 1, 0);

-- operation id
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(10000, '', '', 0, 1, 1);

-- button id
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(10000, 'x', 0, 0, '', 0, '', 0, 'x', 0, 1, '', 0, 0, 0, 0);

-- menu id
INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(100, 0, 0, '', '', '', 0, 0, 0, 0, 0, 1, 1);

-- permission id
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(10000, 0, 0, '', '', '', 1, 1);




-- 2015-01-24 add yangfuyou 项目排序
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES
(236, '排序', 9, 35, '', 210, 'index.php?mod=management&con=Application&act=listAll', 1, '项目排序', 0, 0, '', 1, 6, 47, 1422072712);

INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES
(519, 2, 236, '项目-排序-按钮权限', 'BUTTON236', '', 0, 0);

-- 调整排序

UPDATE `application` SET `display_order`='5' WHERE `id`='6'; 
UPDATE `application` SET `display_order`='6' WHERE `id`='7'; 

UPDATE `button` SET `display_order`='25' WHERE `id`='119';
UPDATE `button` SET `display_order`='24' WHERE `id`='120';
UPDATE `button` SET `display_order`='22' WHERE `id`='121';
UPDATE `button` SET `display_order`='23' WHERE `id`='122';

-- 订单修改为主对象

UPDATE `control` SET `type`='2' WHERE `id`='1029';

-- 采购收货货品日志 删除
UPDATE `control` SET `is_deleted`=1 WHERE `id`='1072';

-- 增加
INSERT INTO `control` (`id`, `label`, `code`, `type`, `parent_id`, `application_id`, `is_deleted`, `is_system`) VALUES(1079, '会计审核', 'AppOrderPayActionCheck', 1, 0, 11, 0, 0);

-- 修改枚举值
UPDATE `dict_item` SET `label`='驳回' WHERE `id`='1081';

UPDATE `menu` SET `c_id`='1002',`o_id`='10024',`icon`='199' WHERE `id`='45'; 
UPDATE `menu` SET `c_id`='1005',`o_id`='10119' WHERE `id`='46'; 
UPDATE `menu` SET `c_id`='1008',`o_id`='10109' WHERE `id`='52'; 
UPDATE `menu` SET `c_id`='1010',`o_id`='10040' WHERE `id`='53'; 
UPDATE `menu` SET `c_id`='1011',`o_id`='10041' WHERE `id`='54'; 


UPDATE `menu` SET `c_id`='1015',`o_id`='10027' WHERE `id`='58'; 
UPDATE `menu` SET `c_id`='1018',`o_id`='10080' WHERE `id`='61'; 
UPDATE `menu` SET `c_id`='1020',`o_id`='10066' WHERE `id`='63';

UPDATE `menu` SET `c_id`='1026',`o_id`='10072',`icon`='110' WHERE `id`='69'; 
UPDATE `menu` SET `c_id`='1029',`o_id`='10029' WHERE `id`='72'; 
UPDATE `menu` SET `c_id`='1034',`o_id`='10112',`icon`='119' WHERE `id`='77'; 

UPDATE `menu` SET `c_id`='1048',`o_id`='10068' WHERE `id`='87'; 
UPDATE `menu` SET `c_id`='1050',`o_id`='10104' WHERE `id`='89'; 
UPDATE `menu` SET `c_id`='1052',`o_id`='10036',`icon`='6' WHERE `id`='91'; 
UPDATE `menu` SET `c_id`='1053',`o_id`='10031' WHERE `id`='92'; 
UPDATE `menu` SET `c_id`='1055',`o_id`='10023',`icon`='21' WHERE `id`='95'; 

UPDATE `menu` SET `c_id`='1058',`o_id`='10085' WHERE `id`='97'; 
UPDATE `menu` SET `c_id`='1059',`o_id`='10032',`icon`='5' WHERE `id`='98'; 
UPDATE `menu` SET `c_id`='1060',`o_id`='10075' WHERE `id`='99'; 
UPDATE `menu` SET `c_id`='1061',`o_id`='10038' WHERE `id`='100'; 

UPDATE `menu` SET `c_id`='1065',`o_id`='10100' WHERE `id`='103'; 
UPDATE `menu` SET `c_id`='1067',`o_id`='10056' WHERE `id`='104'; 
UPDATE `menu` SET `c_id`='1068',`o_id`='10001' WHERE `id`='105'; 

UPDATE `menu` SET `c_id`='1076',`o_id`='10045' WHERE `id`='111'; 
UPDATE `menu` SET `c_id`='1078',`o_id`='293',`icon`='7' WHERE `id`='113'; 

INSERT INTO `menu` (`id`, `c_id`, `o_id`, `label`, `code`, `url`, `icon`, `group_id`, `application_id`, `display_order`, `is_enabled`, `is_system`, `is_deleted`) VALUES(114, 1079, 292, '会计审核', 'APP_ORDER_PAY_ACTION_CHECK', 'index.php?mod=finance&con=AppOrderPayActionCheck&act=index', 7, 40, 11, 1422065263, 1, 0, 0);

UPDATE `menu_group` SET `icon`='286' WHERE `id`='30';
UPDATE `menu_group` SET `icon`='191' WHERE `id`='33';

-- add 20150126

INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES
(237, '菜单排序', 9, 38, '', 211, 'index.php?mod=management&con=MenuGroup&act=ListMenu', 1, '菜单排序', 0, 0, '', 1, 7, 289, 22);


INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES
(290, 'saveMenuSort', '保存菜单排序', 7, 0, 0),
(289, 'ListMenu', '菜单排序', 7, 0, 0);

INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES
(522, 2, 237, '菜单组-菜单排序-按钮权限', 'BUTTON237', '', 0, 0),
(521, 3, 290, '菜单组-保存菜单排序-操作权限', 'MENU_GROUP_SAVE_MENU_SORT_O', '', 0, 0),
(520, 3, 289, '菜单组-菜单排序-操作权限', 'MENU_GROUP_LIST_MENU_O', '', 0, 0);

ALTER TABLE `user` CHANGE `mobile` `mobile` CHAR(11) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '手机';

INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(39, 'pop3', '批量选取并弹框', '主对象列表批量选取，将选中的记录编号传递给弹出页面', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(40, 'batchConfirm', '批量操作', '列表页批量选中，并弹框提示是否对选中的记录进行操作', 1, 0, 1);
INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`, `type`) VALUES(41, 'pop4', '打开新窗口', '批量选中记录，并传递到新的浏览器窗口', 1, 0, 1);

INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(293, 'batchOpen', '新窗口弹出', 14, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(292, 'batchDelete', '批量删除', 14, 0, 0);
INSERT INTO `operation` (`id`, `method_name`, `label`, `c_id`, `is_system`, `is_deleted`) VALUES(291, 'batchPop', '批量选取弹框', 14, 0, 0);

INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(240, '批量操作', 7, 41, '', 15, 'index.php?mod=demo&con=Message&act=batchOpen', 1, '批量操作', 0, 0, '金正日', 2, 14, 293, 21);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(239, '批量删除', 5, 40, '', 13, 'index.php?mod=demo&con=Message&act=batchDelete', 1, '批量删除', 0, 0, '本拉登', 2, 14, 292, 22);
INSERT INTO `button` (`id`, `label`, `class_id`, `function_id`, `cust_function`, `icon_id`, `data_url`, `type`, `tips`, `is_system`, `is_deleted`, `data_title`, `a_id`, `c_id`, `o_id`, `display_order`) VALUES(238, '批量选取', 2, 39, '', 10, 'index.php?mod=demo&con=Message&act=batchPop', 1, '批量选取', 0, 0, '萨达姆', 2, 14, 291, 23);

INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(528, 2, 240, '留言本-批量操作-按钮权限', 'BUTTON240', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(527, 2, 239, '留言本-批量删除-按钮权限', 'BUTTON239', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(526, 2, 238, '留言本-批量选取-按钮权限', 'BUTTON238', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(525, 3, 293, '留言本-新窗口弹出-操作权限', 'MESSAGE_BATCH_OPEN_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(524, 3, 292, '留言本-批量删除-操作权限', 'MESSAGE_BATCH_DELETE_O', '', 0, 0);
INSERT INTO `permission` (`id`, `type`, `resource_id`, `name`, `code`, `note`, `is_deleted`, `is_system`) VALUES(523, 3, 291, '留言本-批量选取弹框-操作权限', 'MESSAGE_BATCH_POP_O', '', 0, 0);

ALTER TABLE `user` ADD `is_warehouse_keeper` TINYINT NOT NULL DEFAULT '0' COMMENT '是否库管';
ALTER TABLE `user` ADD `is_channel_keeper` TINYINT NOT NULL DEFAULT '0' COMMENT '是否渠道操作员';
ALTER TABLE `user` ADD `icd` CHAR(18) NULL COMMENT '身份证号';

ALTER TABLE `menu` ADD `type` TINYINT NOT NULL DEFAULT '1' COMMENT '菜单类型：1、通用，2、库管可见，3、渠道操作员可见';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
