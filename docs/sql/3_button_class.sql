-- 2014-12-17 by yangfuyou

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
