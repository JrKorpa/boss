/*
SQLyog Ultimate v11.24 (32 bit)
MySQL - 5.6.26-log 
*********************************************************************
*/
/*!40101 SET NAMES utf8 */;

create table `s11_order_info` (
	`id` int (10),
	`out_order_sn` varchar (60),
	`order_id` int (10),
	`order_sn` varchar (48),
	`res` tinyint (1),
	`reason` varchar (765),
	`order_status` varchar (375),
	`add_time` datetime 
); 
