-- 2014-12-17 by yangfuyou
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='按钮事件表' AUTO_INCREMENT=1 ;

--
-- 转存表中的数据 `button_function`
--

INSERT INTO `button_function` (`id`, `name`, `label`, `tips`, `is_system`, `is_deleted`) VALUES
(1, 'sync', '同步', '保留搜索条件，刷新当前页，同步数据变更', 1, 0),
(2, 'reload', '刷新', '清除搜索条件，回到首页', 1, 0),
(3, 'closeTab', '离开', '关闭当前页签', 1, 0),
(4, 'pop', '弹窗', ',无须post参数', 1, 0),
(5, 'pop2', '弹窗2', '需要传递行id', 1, 0),
(6, 'add', '新增(弹框)', '同pop', 1, 0),
(7, 'edit', '编辑(弹框)', '同pop2', 1, 0),
(8, 'delete', '删除(弹框)', '弹窗删除', 1, 0),
(9, 'retrieve', '详情(弹框)', '同pop2', 1, 0),
(10, 'view', '详情(页签)', '同newTab', 1, 0),
(11, 'retrieveEdit', '编辑(详情页编辑弹窗)', '查看页的编辑按钮', 1, 0),
(12, 'retrieveDelete', '删除(详情页删除)', '查看页的删除按钮', 1, 0),
(13, 'sort', '排序(弹框)', '同pop', 1, 0),
(14, 'relList', '相关列表', '跳转到相应菜单列表，并将记录id带过去作为条件', 1, 0),
(15, 'confirm', '特殊处理', '', 1, 0),
(16, 'newTab', '新页签', '打开新页签，需要传递行id', 1, 0),
(17, 'cust', '自定义', '用户自定义函数处理', 1, 0);
