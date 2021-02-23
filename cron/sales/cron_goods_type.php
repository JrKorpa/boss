<?php
/*
*   功能用于实现当货品类型未标注时可以自动使用
*/
header("Content-type:text/html;charset=utf8;");
error_reporting(E_ALL ^ E_DEPRECATED);
define('ROOT_LOG_PATH',str_replace(basename(__FILE__), '', str_replace('\\', '/', __FILE__))); 
date_default_timezone_set("Asia/Shanghai");
$mysqli=new mysqli('192.168.1.59','cuteman','QW@W#RSS33#E#','app_order') or die("数据库连接失败！") ; 
//$mysqli=new mysqli('192.168.0.95','cuteman','QW@W#RSS33#E#','app_order') or die("数据库连接失败！") ; 
$starttime = date("Y-m-d H:i:s",strtotime("-5 days"));

//查看订单
$sql="
    SELECT 
        distinct boi.order_sn
    FROM 
        `app_order_details_20151124`  od
        inner join app_order.base_order_info boi on od.order_id = boi.id
        inner join warehouse_shipping.warehouse_goods wg on wg.order_goods_id = od.id AND wg.goods_id = od.goods_id 
        inner join front.app_cat_type as cat on cat.cat_type_name = wg.cat_type1
        inner join front.app_product_type as pro on pro.product_type_name = wg.product_type1
    WHERE 
        ( od.product_type = 0 or od.product_type is null )
        AND 
        ( od.cat_type = 0 or od.cat_type is null )
        AND od.is_zp = 0
        AND od.goods_type = 'style_goods'
        AND boi.create_time >= '$starttime'
        AND boi.order_status in (1,2)
        AND boi.order_sn = '20151120952415'
";
$str = '';
$result = $mysqli->query($sql);
if ($result) {
	if($result->num_rows>0){  
        $order_snList = array();
		while($row =$result->fetch_assoc() ){    
            $order_snList[] = $row['order_sn'];
        }
        $str = implode(',',$order_snList);
    }
}
recordLog('xianhuo', $str);
//修改订单商品产品线及款式分类
$sql="
    update 
        `app_order_details_20151124` od
        inner join app_order.base_order_info boi on od.order_id = boi.id
        inner join warehouse_shipping.warehouse_goods wg on wg.order_goods_id = od.id AND wg.goods_id = od.goods_id 
        inner join front.app_cat_type as cat on cat.cat_type_name = wg.cat_type1
        inner join front.app_product_type as pro on pro.product_type_name = wg.product_type1
    set 
        od.product_type = pro.product_type_id,
        od.cat_type = cat.cat_type_id
    where 
        ( od.product_type = 0 or od.product_type is null )
        AND 
        ( od.cat_type = 0 or od.cat_type is null )
        AND od.is_zp = 0
        AND od.goods_type = 'style_goods'
        AND boi.create_time >= '$starttime'
        AND boi.order_status in (1,2)
";
$mysqli->query($sql);


//查看订单
$sql="
    select 
        distinct boi.order_sn
    from 
        app_order.app_order_details_20151124 od
        inner join app_order.base_order_info boi on boi.id = od.order_id
    where
        (
            (
                (
                    (
                        ( od.product_type = 0 or od.product_type is null )
                        AND 
                        ( od.cat_type = 0 or od.cat_type is null )
                        AND od.goods_type = 'style_goods'
                    )
                    OR 
                    (
                        od.goods_type not in ('style_goods','caizuan_goods','lz','qiban','zp')
                        OR
                        od.goods_type is null
                    )
                )
                AND od.is_zp = 0
            )
            OR
            (
                od.is_zp not in ('0','1')
            )
        )
        AND od.goods_sn !=''
        AND boi.create_time >= '$starttime'
        AND boi.order_status in (1,2)
";
$str = '';
$result = $mysqli->query($sql);
if ($result) {
	if($result->num_rows>0){  
        $order_snList = array();
		while($row =$result->fetch_assoc() ){    
            $order_snList[] = $row['order_sn'];
        }
        $str = implode(',',$order_snList);
    }
}
recordLog('all', $str);

//修改订单商品产品线及款式分类
$sql="
    update 
        app_order.app_order_details_20151124 od
        inner join front.base_style_info bsi on od.goods_sn = bsi.style_sn
        inner join app_order.base_order_info boi on boi.id = od.order_id
    set 
        od.product_type = bsi.product_type,
        od.cat_type = bsi.style_type
    where 
        ( od.product_type = 0 or od.product_type is null )
        AND 
        ( od.cat_type = 0 or od.cat_type is null )
        AND od.goods_type = 'style_goods'
        AND od.goods_sn != ''
        AND boi.create_time >= '$starttime'
";
$mysqli->query($sql);

$mysqli->close();

/*------------------------------------------------------ */
//-- 记录日志信息
/*------------------------------------------------------ */
function recordLog($api, $str)
{
    $file_name = str_replace('.php','',basename(__FILE__));
	if (!file_exists(ROOT_LOG_PATH . 'logs'))
	{
		mkdir(ROOT_LOG_PATH . 'logs', 0777);
		chmod(ROOT_LOG_PATH . 'logs', 0777);
	}
	if (!file_exists(ROOT_LOG_PATH . 'logs/'.$file_name))
	{
		mkdir(ROOT_LOG_PATH . 'logs/'.$file_name, 0777);
		chmod(ROOT_LOG_PATH . 'logs/'.$file_name, 0777);
	}
	$content = $api."||".date("Y-m-d H:i:s")."||".$str."||"."\r\n";
	$file_path =  ROOT_LOG_PATH . 'logs/'.$file_name.'/'.date('Y')."_".date('m')."_".date('d')."$api.txt";
	file_put_contents($file_path, $content, FILE_APPEND );
}
