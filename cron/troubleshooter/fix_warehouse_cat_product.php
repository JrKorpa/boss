<?php


include_once dirname(__FILE__).'/template.php';

// 清洗仓库商品产品线和款式分类问题
$sql = "update warehouse_shipping.warehouse_goods g inner join (
	SELECT i.style_sn, p.product_type_name, c.cat_type_name from front.base_style_info i left JOIN front.app_product_type p on p.product_type_id = i.product_type
	left join front.app_cat_type c on c.cat_type_id = i.style_type
) d on d.style_sn = g.goods_sn
set g.product_type1 = d.product_type_name, g.cat_type1 = d.cat_type_name"; 

$fixer = new Template();
$fixer->exec($sql);
		
?>





