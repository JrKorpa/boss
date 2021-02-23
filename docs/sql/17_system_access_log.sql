
-- 2014-12-17 by yangfuyou

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

