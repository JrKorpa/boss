<?php
define('IN_ECS', true);
header("Content-type:text/html;charset=utf-8;");
error_reporting(E_ALL);
set_time_limit(0);

$doTest = false;

if($doTest){
    $mysqli=new mysqli('192.168.0.131','root','123456','app_order');
}else{
    $mysqli=new mysqli('192.168.1.59','cuteman','QW@W#RSS33#E#','warehouse_shipping');
}

if($mysqli->connect_error){
    die($mysqli->connect_error);
}
$mysqli->query('set names utf-8');


$sql = "select MAX(order_id) max_order_id from finance.jxs_order";
$res = $mysqli->query($sql);
$row = combinedata($res);
if(!$row){
    die;
}


$id = $row['0']['max_order_id'];
//取订单 

$distanceTime = date("Y-m-d H:i:s",time() - (15*24*60*60));
$startOrderId = $id;
if(!$startOrderId){
	  $startOrderId = 0;
}
//echo $startOrderId;exit;
$sql="SELECT 
        o.department_id,
		ad.country_id,
		ad.province_id,
		ad.city_id,
		ad.regional_id region_id,
        ad.address,
		o.id,
		o.order_status,
        o.create_time,
        aoa.order_amount,

        od.cart,
        od.cut,
        od.clarity,
        od.color,
        od.goods_type,
        od.xiangkou jietuoxiangkou,

        wb.order_sn,
		wb.check_time send_goods_time,
		
		wbg.goods_id,
        wbg.sale_price chengbenjia,
		wbg.shijia xiaoshoujia,

		wg.cat_type1 shipin_type,
		wg.product_type1 kuanshi_type,
		wg.caizhi jinse,
		wg.jinzhong,
		wg.goods_sn,
		wg.zhengshuhao,
		wg.goods_name,
		wg.mingyichengben

        FROM  
         `app_order`.`base_order_info` as o
	 inner join `app_order`.`app_order_address` ad on o.id = ad.order_id
	 inner join `app_order`.`app_order_account` aoa on o.id = aoa.order_id
	 inner join `app_order`.`app_order_details` od on o.id = od.order_id
	 inner join `warehouse_shipping`.`warehouse_bill` wb on wb.order_sn = o.order_sn
	 inner join `warehouse_shipping`.`warehouse_bill_goods` wbg on wb.bill_no = wbg.bill_no
	 inner join `warehouse_shipping`.`warehouse_goods` wg on wbg.goods_id = wg.goods_id
        WHERE  
	1
	AND o.id >$startOrderId
    AND wb.bill_type='S' 
	AND wb.`bill_status`=2 
	AND o.create_time >='2014-10-01 00:00:00' 
	AND wb.check_time <='".$distanceTime."' 
	AND o.order_status =2 
	AND o.order_pay_status > 1  
	AND o.is_zp = 0 
	AND aoa.money_paid > 0 
	AND o.department_id in ('1','2','3','13','52','71')  
	group by wg.goods_id
    ";

//file_put_contents('/home/www/kela_order/includes/shell/logs/jxs_profit.log',str_replace(array("\r\n","\n"),array('',''),$sql),FILE_APPEND);
//echo $sql;die;
$res = $mysqli->query($sql);
$orderDlist = combinedata($res);
$batch_id = time();
//var_dump($orderDlist);die;


//产品线
$res = $mysqli->query("SELECT product_type_id id,product_type_name name FROM `front`.`app_product_type`");
$productData = combinedata($res);
foreach($productData as $key => $val){
    $pD[$val['name']] = $val['id'];
}

//款式分类
$res = $mysqli->query("SELECT cat_type_id id,cat_type_name name FROM `front`.`app_cat_type`");
$styleData = combinedata($res);
foreach($styleData as $key => $val){
    $sD[$val['name']] = $val['id'];
}

//jxsid分类
$res = $mysqli->query("SELECT * FROM `finance`.`jxs_area_scope` ");
$jxsidData = combinedata($res);
foreach($jxsidData as $key => $val){
    $jD[$val['jxs_id']] = $val;
}


//更新经销商
$sql = "select od.id,a.country_id,a.province_id,a.city_id,a.regional_id region_id from
        `finance`.`jxs_order` od
        inner join `app_order`.`app_order_address` a on od.order_id=a.order_id ";


$res = $mysqli->query($sql);
$list = combinedata($res);
foreach ($list as $v){
	$jxs_id = getJxsId($v);

	if(empty($jxs_id)){
		$jxs_id=0;
	}

	$ssql = 'update `finance`.`jxs_order` set jxs_id='.$jxs_id.' where id ='.$v['id'];
	$mysqli->query($ssql);

}
//更新经销商结束


foreach($orderDlist as $key => $val){
    $order_list[$val['order_sn']]['info'] = $val;
    $order_list[$val['order_sn']]['detail'][] = $val;
}
//var_dump($order_list);die;

//$mysqli->query("truncate finance.jxs_order_detail_zyy");
//$mysqli->query("truncate finance.jxs_order_zyy");


foreach($order_list as $order_info){
    //echo "<pre>";
    //var_dump($order_info);
    //die;
    //禁止自动提交
    $mysqli->autocommit(false);
    
    $id = $order_info['info']['id'];
    $order_sn = $order_info['info']['order_sn'];
    $department_id = $order_info['info']['department_id'];
    $create_time = $order_info['info']['create_time'];
    $send_goods_time = $order_info['info']['send_goods_time'];

    $goods_list = getD($order_sn,$send_goods_time);
    //var_dump($ret);

    $item_count = count($order_info['detail']);
    $order_status = $order_info['info']['order_status'];

    $jxs_id = getJxsId($order_info['info']);
    if(empty($jxs_id)){
        continue;
    }

    $order_amount = 0;

    $address = $order_info['info']['address'];
    $country_id = $order_info['info']['country_id'];
    $province_id = $order_info['info']['province_id'];
    $city_id = $order_info['info']['city_id'];
    $region_id = $order_info['info']['region_id'];

    $og_base_sql = "
        insert into finance.jxs_order_detail(
            id,batch_id,order_id,goods_id,
            trading_price,cost_price,
            cart,cut,clarity,color,
            jinzhong,jinse,caizhi,
            goods_type,cat_type,product_type,xiangkou,
            profit_type,calc_profit
        ) values 
    ";
    $og_part_sql = array();
    foreach($order_info['detail'] as $og){
        $goods_id = $og['goods_id'];
        $chengbenjia = $og['chengbenjia'];
        $xiaoshoujia = $og['xiaoshoujia'];

        if(!empty($goods_list) && in_array($goods_id,$goods_list)){
            continue;
        }
        $order_amount += $xiaoshoujia;


        $cart = $og['cart'];
        $cut = $og['cut'];
        $clarity = $og['clarity'];
        $color = $og['color'];

        $jinzhong = $og['jinzhong'];
        $jinse = $og['jinse'];
        $caizhi = '';
        if(preg_match('/18/',$jinse)){
            $caizhi = '18K';
            $jinse = str_replace('18K','',strtoupper($jinse));
        }elseif( strtoupper($jinse) == 'PT950' || strtoupper($jinse) == 'S925'){
            $caizhi = strtoupper($jinse);
            $jinse = '白';
        }

        $goods_type = $og['goods_type'];
        $product_type = isset($pD[$og['kuanshi_type']])?$pD[$og['kuanshi_type']]:0;
        $cat_type = isset($sD[$og['shipin_type']])?$sD[$og['shipin_type']]:0;

        $xiangkou = $og['jietuoxiangkou'];

        $og_part_sql[] = "(
            
            NULL,$batch_id,$id,$goods_id,
            $xiaoshoujia,$chengbenjia,
            '$cart','$cut','$clarity','$color',
            '$jinzhong','$jinse','$caizhi',
            '$goods_type',$cat_type,$product_type,'$xiangkou',
            NULL,0.00
            
            )";
    }

    if(empty($og_part_sql)){
        continue;
    }

    $og_sql = $og_base_sql.implode(',',$og_part_sql);

    $insert_order = "insert into finance.jxs_order(
            id,jxs_id,batch_id,order_id,order_sn,department_id,create_time,send_goods_time,
            item_count,order_amount,order_status,
            address,country_id,province_id,city_id,region_id,
            calc_profit,real_profit,calc_status,calc_date,profit_id
        ) values(
            NULL,$jxs_id,$batch_id,$id,'$order_sn',$department_id,'$create_time','$send_goods_time',
            $item_count,$order_amount,$order_status,
            '$address',$country_id,$province_id,$city_id,$region_id,
            0.00,0.00,0,'0000-00-00 00:00:00',0
        ) ";



    //var_dump($insert_order,$og_sql);
    //echo "<hr>";
    $b1=$mysqli->query($insert_order);
    $b2=$mysqli->query($og_sql);
    if(!$b1||!$b2){
        //如果两个操作中存在一个出现异常，则数据库数据恢复到原始
        $mysqli->rollback();
        echo $order_sn."is wrong <hr>";
    }else{
        //如果两个操作都成功，则真正提交，修改数据库数据
        $mysqli->commit();
        echo $order_sn."is ok <hr>";
    }
}
$mysqli->autocommit(true);

    //更新记录类型
$sql = "select * from `finance`.`jxs_order_detail` where profit_type is NULL ";
$res = $mysqli->query($sql);
$list = combinedata($res);

foreach ($list as $k=>$v){
    $goodsInfo = getRowByGoodsID($v['goods_id']);
    if(!$goodsInfo)
    {
        continue;
    }
    $pl = $goodsInfo['cat_type1'];//品类
    $cpx = $goodsInfo['product_type1'];//产品线
    $zcs = $goodsInfo['caizhi'];//主成色

    if($cpx == '投资黄金'){
        $profit_type = 1;
    }elseif ($cpx == '普通黄金'){
        $profit_type = 2;
    }elseif ($cpx == 'PT'){
        $profit_type = 3;
    }elseif ($cpx == '成品钻' && (strpos($pl,'裸石') !==false || strpos($pl,'祼石') !==false) ){
        $profit_type = 4;
    }elseif ($zcs == '其它' || $zcs == '其他'){
        $profit_type = 5;
    }else{
        $profit_type = 5;
    }
    $mysqli->query('update `finance`.`jxs_order_detail` set profit_type='.$profit_type.' where id='.$v['id']);
}
//echo "SUCC";
//exit;
//step03****************************************************************************
    $sql = "select od.id,o.order_id,o.department_id,profit_type,jinzhong,trading_price,cost_price,department_id from 
        `finance`.`jxs_order_detail` od
        inner join `finance`.`jxs_order` o on od.order_id=o.order_id
        where od.profit_type in (1,2,3,4,5) AND od.calc_profit = 0.00  ";


$res = $mysqli->query($sql);
$list = combinedata($res);

foreach ($list as $k=>$v){
   $pt = $v['profit_type'];
   $zcsz = $v['jinzhong'];
   
   if($pt == 1){
       //不区分渠道  所有渠道都是 经销商利润 = 商品克重 * 1元/克
       $settlement_amount = $zcsz * 1;
       $settlement_role = '经销商利润 = 商品克重'.$zcsz.' * 1元/克';
   }elseif ($pt == 2){
       //不区分渠道  所有渠道都是 经销商利润 = 商品克重 * 5元/克
       $settlement_amount = $zcsz * 5;
       $settlement_role = '经销商利润 = 商品克重'.$zcsz.' * 5元/克';
   }elseif ($pt == 3){
       //不区分渠道  所有渠道都是 经销商利润 = 商品克重 * 10元/克
       $settlement_amount = $zcsz * 10;
       $settlement_role = '经销商利润 = 商品克重'.$zcsz.' * 10元/克';
   }
   /*
    *           天猫	京东	银行	主站							
        平台费	5%	8%	9%	-							
        管理费	5%	5%	5%	5%							
        增值税	5%	5%	5%	5%							
        消费税	6%	6%	6%	6%							
        返点率	21%	24%	25%	16%	
        最终	26%	29%	30%	21%
   * ('1','2','3','13','52','71')  
    */
   elseif ($pt == 4 || $pt == 5){
       /*
        *  经销商利润 = 总销售金额 - 货品成本 - （总销售金额 * 返点率）
        *  0	官方网站部
           2	淘宝销售部
           3	银行销售部
           10	B2C销售部
           55	QQ网购部
           81	京东销售部
       */
       $rate = array(1=>'0.21',2=>'0.26',3=>'0.30',71=>'0.29',13=>'0.30',52=>'0.30');
       if($v['trading_price'] <= $v['cost_price']){
           $settlement_amount=0.00;
           $settlement_role =  '售价'.$v['trading_price'].'上于成本'.$v['cost_price'].' 不计利润！';
       }else{
           $settlement_amount = $v['trading_price'] - $v['cost_price'] - ($v['trading_price'] * $rate[$v['department_id']]);
           $settlement_role = '经销商利润 = 总销售金额'.$v['trading_price'].' - 货品成本'.$v['cost_price'] .' - （总销售金额'.$v['trading_price'].' * 返点率'.$rate[$v['department_id']].'）';
       }
   }else{
       continue;
   }
   $usql = 'update `finance`.`jxs_order_detail` set calc_profit='.$settlement_amount.' where id='.$v['id'];
   $mysqli->query($usql);

   $ssql = 'update 
`finance`.`jxs_order` o
inner join (select o.order_id,SUM(od.calc_profit) s from `finance`.`jxs_order` o inner join `finance`.`jxs_order_detail` od on o.order_id = od.order_id where o.order_id = '.$v['order_id'].'
group by o.order_id having s > 0 ) t on o.order_id = t.order_id
set 
o.calc_profit=t.s
where o.order_id like '.$v['order_id'].' ;';
   $mysqli->query($ssql);
}



function combinedata($result)
{
	$goods_ids = array();
	if(!$result)
	{
		return $goods_ids;
	}
	
	while($row = mysqli_fetch_assoc($result))
	{
		array_push($goods_ids,$row);
	}
	return $goods_ids;
}

function getRowByGoodsID($goods_id)
{
    global $mysqli;
    $sql = "select * from `warehouse_shipping`.`warehouse_goods` where goods_id = $goods_id ";
    $res = $mysqli->query($sql);
    if($res){
        $row = mysqli_fetch_assoc($res);
        return $row;
    }
    return false;
}

function getJxsId($info)
{
    global $jD;
    $jxs_id = 0;
    $country_id = $info['country_id'];
    $province_id = $info['province_id'];
    $city_id = $info['city_id'];
    $region_id = $info['region_id'];

    foreach($jD as $c => $val){
        //一级代理
        if($val['country_id'] == 0){
            return $val['jxs_id'];
        }else{
            //二级代理
            if($val['country_id'] == $country_id ){
                if($val['province_id'] == 0){
                    return $val['jxs_id'];
                }else{
                    //三级代理
                    if($val['province_id'] == $province_id){
                        if($val['city_id'] == 0){
                            return $val['jxs_id'];  
                        }else{
                            //四级代理
                            if($val['city_id'] == $city_id){
                                if($val['region_id'] == 0){
                                    return $val['jxs_id'];
                                }else{
                                    if($val['region_id'] == $region_id){
                                        return $val['jxs_id'];
                                    }else{
                                        continue;
                                    }
                                }
                            }else{
                                continue;
                            }
                        }
                    }else{
                        continue;
                    }
                }
            }else{
                continue;
            }
        }
        continue;
    }
    return 0;
}


function getD($order_sn,$send_goods_time)
{
    global $mysqli;
    $t = strtotime($send_goods_time);
    $endtime = $t + 86400*15;
    $enddate = date("Y-m-d H:i:s",$endtime);
    $sql = "SELECT wbg.goods_id 
    FROM 
        `warehouse_shipping`.`warehouse_bill` wb
        inner join `warehouse_shipping`.`warehouse_bill_goods` wbg on wb.bill_no = wbg.bill_no
    WHERE wb.bill_type = 'D' AND wb.bill_status = 2 AND wb.check_time < '{$enddate}' AND wb.order_sn = '$order_sn';
    ";
    $goodsList=array();
    $res = $mysqli->query($sql);
    if($res){
        $row = combinedata($res);
        if($row){
            foreach($row as $key => $val){
                $goodsList[] = $val['goods_id'];
            }
        }
    }
    return $goodsList;
}

/**


GOODS_ID;
1211035867

ORDER_ID;
2043392

BILL_NO;
D201511028918980









*/








