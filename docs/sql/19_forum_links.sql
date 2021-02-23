-- 2014-12-17 by yangxiaotong

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='友情链接表' AUTO_INCREMENT=1 ;
