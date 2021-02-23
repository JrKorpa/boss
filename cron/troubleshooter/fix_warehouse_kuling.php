<?php


include_once dirname(__FILE__).'/template.php';

// 清洗仓库库龄问题
$sql = "UPDATE warehouse_shipping.`warehouse_goods_age` SET `total_age` = `total_age` + 1 WHERE `goods_id` IN(SELECT `goods_id` FROM warehouse_shipping.`warehouse_goods` WHERE `is_on_sale` IN(2,4,5,6,8,10));
UPDATE warehouse_shipping.`warehouse_goods_age` SET `self_age` = `self_age` + 1 WHERE `goods_id` IN(SELECT `goods_id` FROM warehouse_shipping.`warehouse_goods` WHERE `is_on_sale` IN(2,4,5,6,8,10))"; 

$fixer = new Template();
$fixer->exec($sql);
		
?>