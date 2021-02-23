-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2015-08-31 09:15:40
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `front`
--

-- --------------------------------------------------------

--
-- 表的结构 `auto_run_goods`
--

CREATE TABLE IF NOT EXISTS `auto_run_goods` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `goods_id` varchar(30) NOT NULL COMMENT '货号',
  `product_type` varchar(50) NOT NULL COMMENT '产品线',
  `cat_type` varchar(50) NOT NULL COMMENT '款式分类',
  `is_on_sale` int(3) NOT NULL DEFAULT '1' COMMENT '状态',
  `warehouse` varchar(30) NOT NULL COMMENT '所在仓库',
  `goods_sn` varchar(50) NOT NULL COMMENT '款号',
  `goods_name` varchar(100) DEFAULT NULL COMMENT '名称',
  `mingyichengben` decimal(10,2) DEFAULT '0.00' COMMENT '名义价',
  `tuo_type` tinyint(3) NOT NULL DEFAULT '1' COMMENT '金托类型',
  `zhushi` varchar(50) DEFAULT NULL COMMENT '主石',
  `zhushilishu` varchar(11) NOT NULL DEFAULT '0' COMMENT '主石粒数',
  `zuanshidaxiao` decimal(10,3) NOT NULL DEFAULT '0.000' COMMENT '主石大小',
  `zhengshuhao` varchar(100) DEFAULT NULL COMMENT '证书号',
  `order_goods_id` varchar(10) NOT NULL DEFAULT '0' COMMENT '是否绑定',
  `box_sn` varchar(30) NOT NULL COMMENT '柜位',
  `bill_type` varchar(4) NOT NULL DEFAULT '1' COMMENT '单据类型',
  `bill_no` char(17) NOT NULL COMMENT '单据编号',
  `action_name` varchar(30) NOT NULL COMMENT '发生动作',
  `action_time` datetime NOT NULL COMMENT '发生时间',
  PRIMARY KEY (`id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
