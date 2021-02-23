<?php
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once('MysqlDB.class.php');
set_time_limit(0);
ini_set('memory_limit','2000M');
exit();
$new_conf = [
		'dsn'=>"mysql:host=172.18.150.131;dbname=app_order",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
];

$new_conf = [
    'dsn'=>"mysql:host=192.168.0.95;dbname=app_order",
    'user'=>"cuteman",
    'password'=>"QW@W#RSS33#E#",
    'charset' => 'utf8'
];

$db = new MysqlDB($new_conf);
$db->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
$db->db()->beginTransaction();//开启事务
try{
        echo "开始".PHP_EOL;
        //删除订单相关
        $sql = "SELECT id,order_sn FROM `app_order`.`base_order_info` where hidden = 1";
        $order_info = $db->getAll($sql);
        if($order_info){
            $order_ids = array_column($order_info, 'id');
            $order_ids = implode(',',$order_ids);

            $order_sns = array_column($order_info, 'order_sn');
            $order_sns = implode("','",$order_sns);

            //删除订单明细
            $sql="DELETE from `app_order`.app_order_details where order_id in({$order_ids})";
            $db->query($sql);

            //删除订单金额
            $sql="DELETE from `app_order`.app_order_account where order_id in({$order_ids})";
            $db->query($sql);

            //删除订单赠品
            $sql="DELETE from `app_order`.rel_gift_order where order_id in({$order_ids})";
            $db->query($sql);

            //删除订单发票
            $sql="DELETE from `app_order`.app_order_invoice where order_id in({$order_ids})";
            $db->query($sql);

            //删除订单地址
            $sql="DELETE from `app_order`.app_order_address where order_id in({$order_ids})";
            $db->query($sql);

            //删除订单日志
            $sql="DELETE from `app_order`.app_order_action where order_id in({$order_ids})";
            $db->query($sql);

            //删除支付方式
            $sql="DELETE from finance.app_order_pay_action where order_id in({$order_ids})";
            $db->query($sql);

            //删除定金收据
            $sql="DELETE from finance.app_receipt_deposit where order_sn in('{$order_sns}')";
            $db->query($sql);

            $sql="DELETE from finance.app_receipt_pay where order_sn in('{$order_sns}')";
            $db->query($sql);

            //最后删除订单主表信息
            $sql="DELETE FROM `app_order`.`base_order_info` where hidden = 1";
            $db->query($sql);
            echo "订单相关删除完成".PHP_EOL;
        }

        //删除货品相关
        $sql="select goods_id from warehouse_shipping.warehouse_goods where hidden = 1";
        $goods_info = $db->getAll($sql);
        if($goods_info){
            $goods_ids = array_column($goods_info, 'goods_id');
            $goods_ids = implode(',',$goods_ids);

            //删除库龄信息
            $sql="DELETE from warehouse_shipping.warehouse_goods_age where goods_id in({$goods_ids})";
            $db->query($sql);

            //删除上下架信息
            $sql="DELETE from warehouse_shipping.goods_warehouse where good_id in({$goods_ids})";
            $db->query($sql);

            //删除货品
            $sql="DELETE from warehouse_shipping.warehouse_goods where hidden = 1";
            $db->query($sql);
            echo "货品相关删除完成".PHP_EOL;
        }

        //删除仓储单据相关
        $sql="select id from warehouse_shipping.warehouse_bill where hidden = 1";
        $bill_info = $db->getAll($sql);
        if($bill_info){
            $bill_ids = array_column($bill_info, 'id');
            $bill_ids = implode(',',$bill_ids);

            //删除单据明细
            $sql="DELETE from warehouse_shipping.warehouse_bill_goods where bill_id in({$bill_ids})";
            $db->query($sql);

            //删除结算信息
            $sql="DELETE from warehouse_shipping.warehouse_bill_pay where bill_id in({$bill_ids})";
            $db->query($sql);

            //删除单据状态信息
            $sql="DELETE from warehouse_shipping.warehouse_bill_status where bill_id in({$bill_ids})";
            $db->query($sql);

            //删除单据打印
            $sql="DELETE from warehouse_shipping.warehouse_bill_print where bill_id in({$bill_ids})";
            $db->query($sql);

            //删除单据
            $sql="DELETE from warehouse_shipping.warehouse_bill where hidden = 1";
            $db->query($sql);
            echo "单据相关删除完成".PHP_EOL;
        }

        //删除布产信息相关
        $sql="select id from kela_supplier.product_info where hidden = 1";
        $bc_info = $db->getAll($sql);
        if($bc_info){
            $bc_ids = array_column($bc_info, 'id');
            $bc_ids = implode(',',$bc_ids);

            //删除布产货品属性信息
            $sql="DELETE from kela_supplier.product_info_attr where g_id in({$bc_ids})";
            $db->query($sql);

            //删除工厂流水信息
            $sql="DELETE from kela_supplier.product_factory_opra where bc_id in({$bc_ids})";
            $db->query($sql);

            //删除布产单操作日志
            $sql="DELETE from kela_supplier.product_opra_log where bc_id in({$bc_ids})";
            $db->query($sql);

            //删除OQC操作记录
            $sql="DELETE from kela_supplier.product_oqc_opra where bc_id in({$bc_ids})";
            $db->query($sql);

            //删除工厂出货信息
            $sql="DELETE from kela_supplier.product_shipment where bc_id in({$bc_ids})";
            $db->query($sql);

            //删除布产信息
            $sql="DELETE from kela_supplier.product_info where hidden = 1";
            $db->query($sql);
            echo "布产相关删除完成".PHP_EOL;
        }

        //删除维修订单
        $sql="select id from repair_order.app_order_weixiu where hidden = 1";
        $weixiu = $db->getAll($sql);
        if($weixiu){
            $weixiu_ids = array_column($weixiu, 'id');
            $weixiu_ids = implode(',',$weixiu_ids);

            //删除维修日志
            $sql="DELETE from repair_order.app_order_weixiu_log where do_id in({$weixiu_ids})";
            $db->query($sql);

            //删除维修订单
            $sql="DELETE from repair_order.app_order_weixiu where hidden = 1";
            $db->query($sql);
            echo "维修相关删除完成".PHP_EOL;
        }

        //删除无账单据
        $sql="DELETE from warehouse_shipping.virtual_return_bill where hidden = 1";
        $db->query($sql);

        //删除财务相关
        $sql="DELETE from finance.pay_yf_real where hidden = 1";
        $db->query($sql);

        $sql="DELETE from finance.pay_should where hidden = 1";
        $db->query($sql);

        $sql="DELETE from finance.pay_apply where hidden = 1";
        $db->query($sql);
        
        echo "删除完成".PHP_EOL;
		$db->db()->commit();
		$db->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
}catch(Exception $e){
	    echo $sql.json_encode($e);
		$db->db()->rollback();//事务回滚
		$db->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return $e->getMessage();	
}
?>