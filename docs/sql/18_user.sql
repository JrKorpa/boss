-- 2014-12-17 by yangfuyou

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`id`, `account`, `password`, `code`, `real_name`, `is_on_work`, `is_enabled`, `gender`, `birthday`, `mobile`, `phone`, `qq`, `email`, `address`, `join_date`, `user_type`, `up_pwd_date`, `uin`, `is_system`, `is_deleted`) VALUES
(1, 'admin', '6248883015fef52f23785ab26e187635', '10000', '', 1, 1, 0, '2014-11-11', '13800138000', '', '', 'yangfuyou@kela.cn', '', '2014-11-11', 1, 0, '', 1, 0);

