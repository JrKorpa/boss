<?php

function boss_on_bill_L_checked($data, $db) {

	if (!isset($data['bill_id']) || empty($data['bill_id'])) return false;
	
	$bill_id = $data['bill_id'];
	if ($bill_id > 0) {
		echo 'start processing L bill:'.$bill_id.PHP_EOL;
		$db->exec(
"insert into goods_io(goods_id,warehouse_id,in_time,birth_time,in_bill_no) 
select wbg.goods_id, b.to_warehouse_id as warehouse_id, b.check_time as in_time, g.addtime as birth_time, b.bill_no as in_bill_no from warehouse_bill_goods wbg 
INNER JOIN warehouse_bill b on b.id = wbg.bill_id and b.bill_type ='L'
inner join warehouse_goods g on g.goods_id = wbg.goods_id
where b.id = {$bill_id};");

        $sql = "select wbg.goods_id,g.cat_type1,b.put_in_type,g.product_type1,g.tuo_type,g.buchan_sn,g.chengbenjia,b.pro_id,b.to_warehouse_id as warehouse_id, b.check_time as in_time, g.addtime as birth_time, b.create_time as b_create from warehouse_bill_goods wbg 
INNER JOIN warehouse_bill b on b.id = wbg.bill_id and b.bill_type ='L'
inner join warehouse_goods g on g.goods_id = wbg.goods_id
where b.id = {$bill_id};";
        $info = $db->getAll($sql);
        if(!empty($info)){
            //1、收货单
            //名义成本计算规则：3、证书费（全部boss）商品产品线是“镶嵌类”并且金托类型是“成品”的证书费=20，产品线为“素金”证书费=5，其他产品线证书费为0
            $xiangqian_type = array('钻石','珍珠','翡翠','宝石','彩钻');
            $sujin_type = array('K金','PT','银饰');
            foreach ($info as $key => $val) {
                if (!empty($val['b_create']) && $val['b_create'] > '2018-01-31 20:00:00') continue;                

                $gendanfei = 0;
                $zhengshufei = 0;
                $mingyichengben = $val['chengbenjia'];
                if($val['put_in_type'] != '5'){
                    //1   成品\2   空托女戒\3   空托
                    if(in_array($val['product_type1'],$xiangqian_type) && !in_array($val['tuo_type'] , array(2,3))){
                        $zhengshufei = 20;
                    }elseif(in_array($val['product_type1'],$sujin_type)){
                        $zhengshufei = 5;
                    }else{
                        $zhengshufei = 0;
                    }
                    
                    //（1）总公司（BDD）收货入库 名义成本=采购成本+证书费+跟单费
                    //（2）总公司（浩鹏）收货入库，供应商如果是【GP商贸有限公司】，名义成本=采购成本*1.17/1.035+证书费
                    //（3）总公司（浩鹏）收货入库，供应商如果不是【GP商贸有限公司】，名义成本=采购成本+证书费
                    //跟单费 直营店，个体店：备货=20，客订单=50
                    //直营店，个体店：备货=20，客订单=50；经销商不收取跟单费。
                    //区分备货、客订单：
                    //通过布产单是否能匹配上具体订单，绑定订单算客订单，未绑定订单算备货单；
                    /*$is_from_type = '';
                    if($val['buchan_sn']){
                        $sql = "select from_type from `kela_supplier`.`product_info` where bc_sn = '".$val['buchan_sn']."';";
                        $is_from_type = $db->getOne($sql);
                    }
                    if($is_from_type == 1){
                        $gendanfei = 20;
                    }elseif($is_from_type == 2){
                        $gendanfei = 50;
                    }else{
                        $gendanfei = 0;
                    }*/
                    //名义成本=采购成本+证书费+跟单费
                    //$mingyichengben = $val['chengbenjia']+$zhengshufei+$gendanfei;
                    //$mingyichengben = $val['chengbenjia']+$zhengshufei;
                    if($val['pro_id'] == '581'){
                        //供应商如果是【GP商贸有限公司】，名义成本=采购成本*1.17/1.035+证书费
                        $mingyichengben = ($val['chengbenjia']*1.17)/1.035;
                    }else{
                        //不是【GP商贸有限公司】裸石和彩钻不计算、名义成本=采购成本+证书费
                        if(in_array($val['cat_type1'], array('裸石','彩钻'))){
                            continue;
                        }
                        $mingyichengben = $val['chengbenjia']+$zhengshufei;
                    }
                }
                //,with_fee = '{$gendanfei}' 跟单费
                $sql = "update warehouse_goods set mingyichengben = '{$mingyichengben}',certificate_fee = '{$zhengshufei}' where goods_id = ".$val['goods_id'];
                //echo $sql."<br/>";
                $db->exec($sql);
                $sql = "update warehouse_bill_goods set mingyijia = '{$mingyichengben}' where bill_id = {$bill_id} and goods_id = ".$val['goods_id'];
                //echo $sql."<br/>";
                $db->exec($sql);
            }
        }
	}
}




function zhanting_on_bill_L_checked($data, $db) {

    if (!isset($data['bill_id']) || empty($data['bill_id'])) return false;
    
    $bill_id = $data['bill_id'];
    if ($bill_id > 0) {
        echo 'start processing L bill:'.$bill_id.PHP_EOL;
        $db->exec(
"insert into goods_io(goods_id,warehouse_id,in_time,birth_time,in_bill_no) 
select wbg.goods_id, b.to_warehouse_id as warehouse_id, b.check_time as in_time, g.addtime as birth_time, b.bill_no as in_bill_no from warehouse_bill_goods wbg 
INNER JOIN warehouse_bill b on b.id = wbg.bill_id and b.bill_type ='L'
inner join warehouse_goods g on g.goods_id = wbg.goods_id
where b.id = {$bill_id};");

        $sql = "select wbg.goods_id,g.cat_type1,b.put_in_type,g.product_type1,g.tuo_type,g.buchan_sn,g.chengbenjia,b.pro_id,b.to_warehouse_id as warehouse_id, b.check_time as in_time, g.addtime as birth_time, b.create_time as b_create from warehouse_bill_goods wbg 
INNER JOIN warehouse_bill b on b.id = wbg.bill_id and b.bill_type ='L'
inner join warehouse_goods g on g.goods_id = wbg.goods_id
where b.id = {$bill_id};";
        $info = $db->getAll($sql);
        if(!empty($info)){
            //1、收货单
            //名义成本计算规则：3、证书费（全部boss）商品产品线是“镶嵌类”并且金托类型是“成品”的证书费=20，产品线为“素金”证书费=5，其他产品线证书费为0
            $xiangqian_type = array('钻石','珍珠','翡翠','宝石','彩钻');
            $sujin_type = array('K金','PT','银饰');
            foreach ($info as $key => $val) {
                if (!empty($val['b_create']) && $val['b_create'] > '2018-01-31 20:00:00') continue;                

                $gendanfei = 0;
                $zhengshufei = 0;
                $mingyichengben = $val['chengbenjia'];
                if($val['put_in_type'] != '5'){
                    //1   成品\2   空托女戒\3   空托
                    if(in_array($val['product_type1'],$xiangqian_type) && !in_array($val['tuo_type'] , array(2,3))){
                        $zhengshufei = 20;
                    }elseif(in_array($val['product_type1'],$sujin_type)){
                        $zhengshufei = 5;
                    }else{
                        $zhengshufei = 0;
                    }
                    
                    //（1）总公司（BDD）收货入库 名义成本=采购成本+证书费+跟单费
                    //（2）总公司（浩鹏）收货入库，供应商如果是【GP商贸有限公司】，名义成本=采购成本*1.17/1.035+证书费
                    //（3）总公司（浩鹏）收货入库，供应商如果不是【GP商贸有限公司】，名义成本=采购成本+证书费
                    //跟单费 直营店，个体店：备货=20，客订单=50
                    //直营店，个体店：备货=20，客订单=50；经销商不收取跟单费。
                    //区分备货、客订单：
                    //通过布产单是否能匹配上具体订单，绑定订单算客订单，未绑定订单算备货单；
                    /*$is_from_type = '';
                    if($val['buchan_sn']){
                        $sql = "select from_type from `kela_supplier`.`product_info` where bc_sn = '".$val['buchan_sn']."';";
                        $is_from_type = $db->getOne($sql);
                    }
                    if($is_from_type == 1){
                        $gendanfei = 20;
                    }elseif($is_from_type == 2){
                        $gendanfei = 50;
                    }else{
                        $gendanfei = 0;
                    }*/
                    //名义成本=采购成本+证书费+跟单费
                    //$mingyichengben = $val['chengbenjia']+$zhengshufei+$gendanfei;
                    //$mingyichengben = $val['chengbenjia']+$zhengshufei;
                    if($val['pro_id'] == '581'){
                        //供应商如果是【GP商贸有限公司】，名义成本=采购成本*1.17/1.035+证书费
                        $mingyichengben = ($val['chengbenjia']*1.17)/1.035;
                    }else{
                        //不是【GP商贸有限公司】裸石和彩钻不计算、名义成本=采购成本+证书费
                        if(in_array($val['cat_type1'], array('裸石','彩钻'))){
                            continue;
                        }
                        $mingyichengben = $val['chengbenjia']+$zhengshufei;
                    }
                }
                //,with_fee = '{$gendanfei}' 跟单费
                $sql = "update warehouse_goods set mingyichengben = '{$mingyichengben}',certificate_fee = '{$zhengshufei}' where goods_id = ".$val['goods_id'];
                //echo $sql."<br/>";
                $db->exec($sql);
                $sql = "update warehouse_bill_goods set mingyijia = '{$mingyichengben}' where bill_id = {$bill_id} and goods_id = ".$val['goods_id'];
                //echo $sql."<br/>";
                $db->exec($sql);
            }
        }

        $sql = "select DISTINCT o.order_sn  from warehouse_bill_goods wbg  INNER JOIN warehouse_bill b on b.id = wbg.bill_id and b.bill_type ='L'
               inner join warehouse_goods g on g.goods_id = wbg.goods_id left join app_order.app_order_details d on g.order_goods_id=d.id
               left join app_order.base_order_info o on d.order_id=o.id where b.id='{$bill_id}' and ifnull(g.order_goods_id,0)>0";
        $orders = $db->getAll($sql);
        if(!empty($orders)){
                require_once __DIR__.'/../Worker.php';
                global $ishop_job_server;      
                $wk = new Worker($ishop_job_server,[],true);      
                foreach ($orders as $key => $order) {              
                    echo 'try to send msg to ishop for pull order.'.PHP_EOL;                    
                    
                    $wk->dispatch('ishop', 'ishop', ['event' => 'pull_order', 'order_id' => 0, 'order_sn' => $order['order_sn'],'source' => 'erp_L_checked' ]);   
                    
                    echo 'msg has been send to ishop!'.PHP_EOL;
                } 
        }

    }
}













?>
