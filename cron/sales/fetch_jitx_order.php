<?php
define('VOP_JITX_ZHEKOU', 0.7);
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once('MysqlDB.class.php');
error_reporting(E_ERROR | E_PARSE);
set_time_limit(0);
ini_set('memory_limit','2000M');

$new_conf = [
		'dsn'=>"mysql:host=192.168.1.192;dbname=app_order",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
];

$db = new MysqlDB($new_conf);

if (PHP_SAPI == 'cli'){
	chdir(dirname(__FILE__));
}

require_once '../../frame/common/define.php';
require_once '../../frame/jitx/vipapis/jitx/JitXServiceClient.php';
        try {
                $service=\vipapis\jitx\JitXServiceClient::getService();
                $ctx=\Osp\Context\InvocationContextFactory::getInstance();                   
             
              
                $ctx->setAppKey(VOP_APP_KEY);
                $ctx->setAppSecret(VOP_APP_SECRET);
                $ctx->setAppURL("https://gw.vipapis.com");
                //$ctx->setAppKey("a876c4cc");
                //$ctx->setAppSecret("77780A5819EC3CFBE648436DB9F95492");
                //$ctx->setAppURL("http://sandbox.vipapis.com");
                 
                $start_time = time()-3600*20;//strtotime("2019-07-08 08:10:00");
                //echo $start_time;
                $to_time = time(); //strtotime("2019-07-08 13:30:00"); 
                $res_list = array();
                $i=0;
               
                while(true){
	                if( $start_time  > $to_time)
                		break;                	
                	$i++;
                	//echo $i.PHP_EOL;
	                $req = new \vipapis\jitx\GetOrdersRequest(
	                   ['vendor_id'=>VOP_VENDOR_ID,
	                    'start_time'=>$start_time,
	                    'end_time'=> $start_time + 1750,
	                    //'order_status'=>['22','23','97_22','97_23'],
	                    'page'=>1,
	                    'limit'=>200,
	                   ]
	                );
	                $res = $service->getOrders($req);
	                //var_dump($res);
	                if($res->orders)
	                	$res_list = array_merge($res_list,$res->orders);
	                $start_time = $start_time + 1750;
	                //echo date("Y-m-d H:i:s",$start_time).PHP_EOL;

	            }

	            //echo "<pre>";
	            //print_r($res_list);    
	            //exit('end');
                $create_time = date('Y-m-d H:i:s');
                if(!empty($res_list)){

                	foreach ($res_list as $key => $out_order) {
                		$out_order_sn = $out_order->order_sn;
                		if($out_order->order_status=='97_10'){
                               cancel_order($db,$out_order_sn);
                               continue; 
                		}
                        if($out_order->order_status!='10')
                            continue;                		
                		$res = get_out_order_sn($db,$out_order_sn);
                		if(!empty($res)){
                			file_put_contents('jitx.log',date('Y-m-d H:i:s',time())." : 外部订单[{$out_order_sn}]已经存在，不必重复抓取".PHP_EOL,FILE_APPEND);
                			echo "外部订单{$out_order_sn}重复".PHP_EOL;
                            continue; 
                		}

 
                        $db->db()->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
                		try{
		                		$db->db()->beginTransaction();
		                        $base_order_info = array();
		                        $order_sn = date('Ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),5,'1',STR_PAD_LEFT);
		                        $base_order_info['order_sn'] = $order_sn;
		                        $base_order_info['user_id'] = '0';
		                        $base_order_info['consignee'] = $out_order->buyer;
		                        $base_order_info['mobile'] = $out_order->buyer_mobile;
		                        $base_order_info['order_status'] = 1;
		                        $base_order_info['order_pay_status'] = 1;
		                        $base_order_info['order_pay_type'] = 170;
		                        $base_order_info['delivery_status'] = 1;
		                        $base_order_info['send_good_status'] = 1;
		                        $base_order_info['customer_source_id'] = 2034;
		                        $base_order_info['department_id'] = 13;
		                        $base_order_info['create_time'] = $create_time;
		                        $base_order_info['modify_time'] = $create_time;
		                        $base_order_info['create_user'] = 'admin';
		                        $base_order_info['order_remark'] = $out_order->remark;
		                        $base_order_info['referer'] = '系统抓单';
		                        $base_order_info['is_xianhuo'] = 1;              
		                        $base_order_info['is_zp'] = 0;
		                        $order_id = saveData($db,'base_order_info',$base_order_info);
		                        echo $order_id.PHP_EOL;
                                
                                //外部订单关联表入库
                                $rel_out_order = array();
                                $rel_out_order['order_id'] = $order_id;
                                $rel_out_order['out_order_sn'] = $out_order_sn; 
                                saveData($db,'rel_out_order',$rel_out_order); 

                                //订单日志入库
                                $app_order_action = array(); 
                                $app_order_action['order_id'] = $order_id; 
                                $app_order_action['order_status'] = 1;
                                $app_order_action['shipping_status'] = 1;
                                $app_order_action['pay_status'] = 1;
                                $app_order_action['create_user'] = 'admin';
                                $app_order_action['create_time'] = $create_time;
                                $app_order_action['remark'] = '唯品会自动抓单';
                                saveData($db,'app_order_action',$app_order_action);
                          

		                        $details_list = array();
		                        $out_order_goods_list = $out_order->order_goods;
		                        $total_goods_price = 0;


		                        foreach ($out_order_goods_list as $key_goods => $out_goods) {
		                        	$goods_price = round(VOP_JITX_ZHEKOU*$out_goods->price/$out_goods->quantity,2);
		                            
		                        	for($i=0; $i< $out_goods->quantity; $i++ ){
		                        		$app_order_details = array();
			                        	$total_goods_price = $total_goods_price + $goods_price; 
			                            $app_order_details['order_id'] = $order_id;
				                        $app_order_details['goods_id'] = '0';//$out_goods->barcode;
				                        $app_order_details['goods_sn'] = $out_goods->sn;
				                        $app_order_details['ext_goods_sn'] = $out_goods->barcode;
				                        $app_order_details['goods_name'] = $out_goods->product_name;
				                        $app_order_details['goods_price'] = $goods_price;
				                        $app_order_details['favorable_price'] = 0;
				                        $app_order_details['goods_count'] = 1;
				                        $app_order_details['create_time'] = $create_time;
				                        $app_order_details['modify_time'] = $create_time;
				                        $app_order_details['create_user'] = 'admin';
				                        $app_order_details['details_status'] = 1;
				                        $app_order_details['send_good_status'] = 1;
				                        $app_order_details['buchan_status'] = 1;
				                        $app_order_details['is_stock_goods'] = 1;
				                        $app_order_details['is_return'] = 0;
				                        $app_order_details['favorable_status'] = 3;
				                        $app_order_details['details_remark'] = $out_goods->size;
				                        $app_order_details['face_work'] = '光面';
				                        $app_order_details['xiangqian'] = '不需要工厂镶嵌';
				                        $details_list[] = $app_order_details;
				                    }

		                        }

		                        if(empty($details_list)){
		                        	$db->db()->rollback();
		                        	$db->db()->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
		                        	echo "{$out_order_sn}订单明细为空".PHP_EOL;
		                            break;
		                        }

		                        //订单明细入库
		                        for($i=0;$i<count($details_list);$i++){
		                            saveData($db,'app_order_details',$details_list[$i]);
		                        }

		                     

		                        //订单金额入库
		                        $app_order_account = array();
		                        $app_order_account['order_id'] = $order_id;
		                        $app_order_account['order_amount'] = $total_goods_price;
		                        $app_order_account['money_unpaid'] = $total_goods_price;
		                        saveData($db,'app_order_account',$app_order_account);


		                        //订单发票表入库
		                        $app_order_invoice = array();
		                        $app_order_invoice['order_id'] = $order_id;
		                        $app_order_invoice['is_invoice'] = 0;
		                        $app_order_invoice['invoice_title'] = '';
		                        $app_order_invoice['invoice_content'] = '';
		                        $app_order_invoice['invoice_status'] = 1;
		                        $app_order_invoice['invoice_amount'] = $total_goods_price;
		                        $app_order_invoice['create_user'] = 'system';
		                        $app_order_invoice['create_time'] = $create_time;
		                        saveData($db,'app_order_invoice',$app_order_invoice);

                                
		                        //订单地址入库
		                        $address_ids = explode(".", $out_order->buyer_address);

		                        $app_order_address = array();
		                        $app_order_address['order_id'] = $order_id;
		                        $app_order_address['consignee'] = $out_order->buyer;
		                        $app_order_address['distribution_type'] = 2;
		                        $app_order_address['express_id'] = 41;   
		                        $app_order_address['country_id'] = 1;

		                        $app_order_address['province_id'] = 0;
		                        $reg_id = $db->getOne("select region_id from cuteframe.region where region_name='{$address_ids[0]}' and region_type='1'");
		                        if(!empty($reg_id))
		                            $app_order_address['province_id'] = $reg_id;

		                        $app_order_address['city_id'] = 0;
		                        $reg_id = $db->getOne("select region_id from cuteframe.region where region_name='{$address_ids[1]}' and region_type='2'");
		                        if(!empty($reg_id))
		                            $app_order_address['city_id'] = $reg_id;		                        
		                        $app_order_address['regional_id'] = 0;
		                        $reg_id = $db->getOne("select region_id from cuteframe.region where region_name='{$address_ids[2]}' and region_type='3'");
		                        if(!empty($reg_id))
		                            $app_order_address['regional_id'] = $reg_id;		                        
		                        $app_order_address['address'] = $out_order->buyer_address;
		                        $app_order_address['tel'] = $out_order->buyer_mobile;
		                        saveData($db,'app_order_address',$app_order_address);
		                        echo $order_sn.PHP_EOL;
		                        $db->db()->commit();
		                        $db->db()->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
                        }catch(Exception $ex){
                        	$db->db()->rollback();
                        	$db->db()->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
                        	echo $ex->getMessage().PHP_EOL;
                        }

                	}                     

                } 
 

        } catch(\Osp\Exception\OspException $e){
            exit($e->getReturnMessage());
        }    
					  













	function get_out_order_sn($db,$out_order_sn)
	{
			//为了兼容boss所以去之前的手动录单那里做一次查询
			$sql = "select a.*  
			from rel_out_order as a 
			left join base_order_info as b on a.order_id=b.id 
			where a.out_order_sn='".$out_order_sn."' and b.order_status < 3"; 
			//echo $sql;
			//待审核的和已经审核的就不能再次录入了
			$result = $db->getAll($sql);
			return $result;
	}

    function saveData($db,$table,$data){
            $sql = "INSERT INTO {$table} (".implode(",",array_keys($data)).") VALUES (". str_pad("?",count($data)*2-1,",?",STR_PAD_RIGHT) .")";
            //echo $sql;
            $stmt = $db->prepare($sql);
            $res=$stmt->execute(array_values($data));                        
            $id = $db->insertId();
            return $id;            

    }

    function cancel_order($db,$out_order_sn){
    	    $sql = "select o.order_sn from app_order.rel_out_order r,app_order.base_order_info o where r.order_id=o.id and r.out_order_sn='{$out_order_sn}' and o.order_status=1 limit 1";
            $order_sn = $db->getOne($sql);
            if($order_sn){
            	$db->exec("update app_order.base_order_info set order_status=3 where order_sn='{$order_sn}' and order_status=1");
            }
    }

?>