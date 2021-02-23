<?php

function zhanting_on_styles_synced($data, $db) {
   $sql = "UPDATE front.list_style_goods a
LEFT JOIN front.app_style_quickdiy b ON a.goods_sn = b.goods_sn
SET a.is_quick_diy = ifnull(b.`STATUS`, 0)
WHERE  (  a.is_quick_diy = 1  AND b.goods_sn IS NULL) OR (b.goods_sn IS NOT NULL AND a.is_quick_diy <> b.`status`);";
   $db->exec($sql);	
   echo 'finish updating list_style_goods.'.PHP_EOL;
}


?>