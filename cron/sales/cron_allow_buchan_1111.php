<?php
/**
1.每10分钟定时跑脚本，双11订单自动布产。
*/

	header("Content-type:text/html;charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
	require_once('MysqlDB.class.php');
	require_once('MysqlDB.class.php');
	set_time_limit(0);
	ini_set('memory_limit','2000M');

//$new_mysqli=new mysqli('192.168.1.93','cuteman','QW@W#RSS33#E#','warehouse_shipping') or die("数据库连接失败！") ;

$new_conf = [
		'dsn'=>"mysql:host=192.168.0.95;dbname=app_order",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
];
$db = new MysqlDB($new_conf);
$pdo=$db->db();
$sql = "select distinct b.order_sn from s11_order_info as a 
		inner join base_order_info as b on a.order_id=b.id 
		where a.res=1 and b.order_status=2 and b.effect_date is null and b.buchan_status=1 
		and b.order_pay_status in(2,3) and b.referer in('双11抓单')";
$res = $db->getAll($sql);
$from_type = 2;
if(empty($res)){
	exit('没有需要布产的订单');
}		

foreach ($res as $key => $o) {
	    $order_sn = $o['order_sn'];
        $sql = "SELECT o.*,(select u.out_order_sn from app_order.rel_out_order u where u.order_id=o.id limit 1) as out_order_sn FROM app_order.base_order_info o WHERE o.order_sn = '{$order_sn}'";
        //echo $sql;
        $order_info = $db->getRow($sql);
        if(empty($order_info)){
        	echo "订单号".$order_sn."不存在！<br>\n";
        	continue;
        }	    
	    $sql="select d.* from app_order.app_order_details d,app_order.base_order_info o where d.order_id=o.id and o.order_sn='{$order_sn}' and d.is_stock_goods=0 and d.is_return<>1 and (d.bc_id=0 or d.bc_id is null)";
	    $order_detail_list=$db->getAll($sql);
	    if(empty($order_detail_list)){
	        echo "订单".$o['order_sn']."货号已全部布产<br>\n";
	        continue;                     
	    }	


        $attr_names =array('cart'=>'主石单颗重','zhushi_num'=>'主石粒数','clarity'=>'主石净度','color'=>'主石颜色','cert'=>'证书类型','zhengshuhao'=>'证书号','caizhi'=>'材质','jinse'=>'金色','jinzhong'=>'金重','zhiquan'=>'指圈','kezi'=>'刻字','face_work'=>'表面工艺');
        $goods_arr = array();
        foreach($order_detail_list as $key=>$val){
                if($val['is_stock_goods'] == 1){
                    continue;
                }
                $detail_id = $val['id'];  
                $sql="SELECT `gr`.* FROM kela_supplier.`product_goods_rel` as `gr`,kela_supplier.`product_info` as `pi` WHERE `gr`.`bc_id`=`pi`.`id` and `gr`.`status` = 0 and `gr`.`goods_id` = '{$detail_id}' and `pi`.`p_sn` = '{$order_sn}'";              
                $buchan_info=$db->getRow($sql);
                if(!empty($buchan_info)){
                    continue;
                }
                $new_style_info = array();
                $xmp=array();
                foreach ($attr_names as $a_key=>$a_val){
                    $xmp['code'] = $a_key;
                    $xmp['name'] = $a_val;
                    $xmp['value'] = $val[$a_key];
                    $new_style_info[] = $xmp;
                }
               
                $goods_num = $val['goods_count'];
                $goods_type=0;
                /*
                if($val['is_peishi']==1){
                    $zhengshuhao = $val['zhengshuhao'];  

                    $ret=$this->db->getRow("select * from front.`diamond_info` where cert_id='{$zhengshuhao}'");
                    if(empty($ret))
                        $ret=$this->db->getRow("select * from front.`diamond_info` where cert_id='{$zhengshuhao}'");
                    if($ret){
                        $goods_arr[$key]['diamond'] = $ret;
                        $goods_type=$ret['goods_type'];
                    }else{
                        $this -> error = 1;
                        $this -> return_sql = '';
                        $this -> error_msg = "裸钻列表未找到证书号为{$zhengshuhao}的裸钻信";
                        $this -> return_msg = array();                      
                        file_put_contents('allow_buchan_api.log', $date_log ."裸钻列表未找到证书号为".$zhengshuhao."的裸钻信".PHP_EOL,FILE_APPEND);
                        $this->display(); 
                        exit();
                    }
                    
                }*/
                
                /*
                if($val['zhengshuhao'] == ''){
                    $diamond_type = 1;
                }else{
                    if($goods_type ==2){
                        //期货钻
                         $diamond_type =2;
                    }else{
                        $diamond_type =1; 
                    }
                }*/
                
                $diamond_type =1;

                $goods_arr[$key]['origin_dia_type']=$diamond_type;
                $goods_arr[$key]['diamond_type']=$diamond_type;
                $goods_arr[$key]['p_id'] =  $detail_id;
                $goods_arr[$key]['p_sn'] =  $order_sn;
                $goods_arr[$key]['style_sn'] = $val['goods_sn'];
                $goods_arr[$key]['goods_name'] = $val['goods_name'];
                $goods_arr[$key]['bc_style'] = empty($val['bc_style'])?'普通件':$val['bc_style'];
                $goods_arr[$key]['xiangqian'] = $val['xiangqian'];
                $goods_arr[$key]['goods_type'] = $val['goods_type'];
                $goods_arr[$key]['cat_type'] = $val['cat_type'];
                $goods_arr[$key]['product_type'] = $val['product_type'];
                $goods_arr[$key]['num'] = $goods_num;
                $goods_arr[$key]['info'] = empty($val['details_remark']) ? '' : $val['details_remark'];
                $goods_arr[$key]['consignee'] = $order_info['consignee'];
                $goods_arr[$key]['attr'] = $new_style_info;
                $goods_arr[$key]['customer_source_id'] = $order_info['customer_source_id'];
                $goods_arr[$key]['channel_id'] = $order_info['department_id'];

                $goods_arr[$key]['create_user']=$order_info['create_user'];
                $goods_arr[$key]['is_peishi'] = $val['is_peishi'];
                //$goods_arr[$key]['is_alone'] = $val['is_alone'];
                $goods_arr[$key]['qiban_type'] = $val['qiban_type'];
                $goods_arr[$key]['out_order_sn'] = $order_info['out_order_sn'];
                $goods_arr[$key]['caigou_info'] = $order_info['order_remark'];
                $goods_arr[$key]['goods_id'] = $val['goods_id'];
                $is_quick_diy = 0;
                if(!empty($val['goods_sn']) && !empty($val['caizhi']) && !empty($val['jinse']) && !empty($val['zhiquan']) && !empty($val['xiangkou'])){
                    $quickdiy_where  = " style_sn = '".$val['goods_sn']."' and caizhi = '".$val['caizhi']."' and caizhiyanse = '".$val['jinse']."' and  zhiquan = ".$val['zhiquan']." and xiangkou = ".$val['xiangkou']." ";
                    $sql = "select * from front.app_style_quickdiy  where status =1 AND $quickdiy_where ";
                    $ress =  $$db->getRow($sql);
                    if(!empty($ress)){
                        $is_quick_diy = 1;
                    }
                }

            $goods_arr[$key]['is_quick_diy']  = $is_quick_diy;

                //end
        }
            //var_dump($goods_arr);exit;
        //    $res = array('data'=>'','error'=>0);
            //添加布产单
        //print_r($goods_arr);
        if(!empty($goods_arr)){
            try{

                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
                    $pdo->beginTransaction();//开启事务
                    $time=date('Y-m-d H:i:s');
                    $arr=array();
                    foreach ($goods_arr as $key => $value){
                        $is_peishi = 0;
                        $cs_id = $value['customer_source_id'];//客户来源
                        $djbh_bc = $cs_id == 2946 ? 'EC' : '';//boss_1246
                        //EDITBY ZHANGRUIYIGN
                        $attrKeyVal = array_column($value['attr'],'value','code');                  
                        
                        $value['caigou_info']=isset($value['caigou_info'])?$value['caigou_info']:'';
                        $value['create_user'] = !empty($value['create_user'])?$value['create_user']:'';
                        $value['prc_id']=isset($value['prc_id'])?$value['prc_id']:0;
                        $value['prc_name']=isset($value['prc_name'])?$value['prc_name']:'';
                        $value['opra_uname']=isset($value['opra_uname'])?$value['opra_uname']:'';
                        $value['is_alone']=isset($value['is_alone'])?$value['is_alone']:0;
                        $value['style_sn']=trim($value['style_sn']);
                        $value['status']=!empty($value['prc_id'])?3:1;
                        $value['qiban_type']=isset($value['qiban_type'])?$value['qiban_type']: 2;
                        $value['diamond_type']=!empty($value['diamond_type'])?$value['diamond_type']:0;
                        $value['origin_dia_type']=!empty($value['origin_dia_type'])?$value['origin_dia_type']:0;
                        $value['to_factory_time']=!empty($value['to_factory_time'])?$value['to_factory_time']:'0000-00-00 00:00:00';
                        $value['is_quick_diy'] = isset($value['is_quick_diy'])?$value['is_quick_diy']:0;
                       

                        $val_insert = array($value['p_id'],$value['p_sn'],$value['style_sn'],$value['status'],$value['num'],$value['prc_id'],$value['prc_name'],$value['opra_uname'],$time,$time,$value['info'],$from_type,$value['consignee'],$value['bc_style'],$value['goods_name'],$value['xiangqian'],$value['customer_source_id'],$value['channel_id'],$value['caigou_info'],$value['create_user'],$value['is_alone'],$value['qiban_type'],$value['diamond_type'],$value['origin_dia_type'],$value['to_factory_time'],$value['is_quick_diy'] );
                        $sql = "INSERT INTO kela_supplier.product_info (`bc_sn`, `p_id`, `p_sn`, `style_sn`, `status`, `num`, `prc_id`,`prc_name`, `opra_uname`, `add_time`, `edit_time`, `info`,`from_type`,`consignee`,`bc_style`,`goods_name`,`xiangqian`,`customer_source_id`,`channel_id`,`caigou_info`,`create_user`,`is_alone`,`qiban_type`,`diamond_type`,`origin_dia_type`,`to_factory_time`,`is_quick_diy`) VALUES ('',?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                        $stmt = $pdo->prepare($sql);
                        $res=$stmt->execute(array_values($val_insert));                        
                        $_id = $pdo->lastInsertId();
                        
        
                        $arr[$key]['id'] = $value['p_id'];
                        $arr[$key]['buchan_sn'] = $_id;
                        $arr[$key]['final_bc_sn'] = '';
                        if ($from_type == '2') {
                            $bc_sn = create_bc_sn($db,$value['p_sn'], $_id);
                            $arr[$key]['final_bc_sn'] = $bc_sn;
                            //抓取订单其他为传递的必要属性
                            $sql ="select xiangkou,cert from app_order.app_order_details where id={$value['p_id']}";
                            $orderDetail = $db->getRow($sql);
                            if(!empty($orderDetail)){
                                if(!array_key_exists('zhushi_num',$attrKeyVal)){
                                    $value['attr'][] = array('code'=>'zhushi_num','name'=>'主石粒数','value'=>$orderDetail['zhushi_num']);
                                }
                                if(!array_key_exists('xiangkou',$attrKeyVal)){
                                    $value['attr'][] = array('code'=>'xiangkou','name'=>'镶口','value'=>$orderDetail['xiangkou']);
                                }
                                if(!array_key_exists('cert',$attrKeyVal)){
                                    $value['attr'][] = array('code'=>'cert','name'=>'证书类型','value'=>$orderDetail['cert']);
                                }
                            }
                        }


                
                         $sql = "UPDATE kela_supplier.`product_info` SET bc_sn = '".$bc_sn."',is_peishi=".$is_peishi." WHERE id ='{$_id}'";
                         $db->query($sql);

                        //获取款式主石，副石相关属性列表
                        $attrExt = getStoneAttrList($db,$value['style_sn'],$value['attr']);
                        if(!empty($attrExt)){
                            $value['attr'] = array_merge($value['attr'],$attrExt);
                        }
                        //$logss =  var_export($value['attr'],true);
                        //file_put_contents('buchan2.txt',$logss);
                        //插入属性
                        $t = "";
                        foreach($value['attr'] as $k => $v)
                        {
                            $sql = "INSERT INTO kela_supplier.`product_info_attr`(`g_id`, `code`, `name`, `value`) VALUES (".$_id.",'".$v['code']."','".$v['name']."','".$v['value']."')";
                            $t .= $sql;
                            $pdo->query($sql);
                        }
                        //file_put_contents("/data/www/cuteframe_boss/apps/processor/logs/u223.txt",$t."\r\n",FILE_APPEND );
                        //插入布产表后增加一条日志
                        $remark = "系统自动生成布产单：".$bc_sn."，来源单号：".$value['p_sn'];
                        $sql = "INSERT INTO kela_supplier.`product_opra_log`(`bc_id`, `status`, `remark`, `uid`, `uname`, `time`) VALUES ({$_id},{$value['status']},'{$remark}',0,'{$value['create_user']}','{$time}')";
                        $db->query($sql);
                        //file_put_contents("/data/www/cuteframe_boss/apps/processor/logs/u223.txt",$sql."\r\n",FILE_APPEND );
                        //如果是订单来源的布产单，插入数据到布产和货品关系表中
                        if($from_type == 2){
                            $sql = "INSERT INTO kela_supplier.`product_goods_rel`(`bc_id`,`goods_id`) VALUES (".$_id.",".$value['p_id'].")";
                            $db->query($sql);  
                            $sql = "UPDATE app_order.`app_order_details` SET `bc_id`='{$_id}' WHERE `id` = '{$value['p_id']}'";
                            $db->query($sql);
                        }

                    }

                    $sql="update app_order.base_order_info set effect_date=now(),buchan_status=2 where order_sn='{$order_sn}'";
                    $db->query($sql);
                    $bc_remark="订单允许布产成功！布产单号为：".implode(",",array_column($arr,'final_bc_sn'));
                    $sql="insert into app_order.app_order_action (order_id,order_status,shipping_status,pay_status,create_user,create_time,remark) values ('{$order_info['id']}','{$order_info['order_status']}','{$order_info['send_good_status']}','{$order_info['order_pay_status']}','{$order_info['create_user']}',now(),'{$bc_remark}')";
                    //echo $sql;
                    $db->query($sql);
                    $pdo->commit();
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                    echo "订单".$order_sn."布产成功<br>\n";                              
            }catch(Exception $e){
                    $pdo->rollback();
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                    
                    $date_log = "order_sn:".$order_sn ."--". date("Y-m-d H:i:s") ;
                    file_put_contents('allow_buchan_api_11.log', $date_log . json_encode($e)."--".json_encode($sql).PHP_EOL,FILE_APPEND);
                    
            }  
        }
}





    function create_bc_sn($db,$order_sn, $bc_id) {
        $bc_sn = 'BC'.$bc_id;
        //if (SYS_SCOPE == 'boss') {
            $sql = "select sc.channel_class from app_order.base_order_info a inner join cuteframe.sales_channels sc on sc.id = a.department_id where a.order_sn = '{$order_sn}'";
            $channel = $db->getOne($sql);
            if ($channel == '1') {
                return 'DS'.$bc_sn;
            } else if ($channel == '2') {
                return 'MD'.$bc_sn;
            }
        //}
    }   
    function getStoneAttrList($db,$style_sn,$attrlist){
        $stoneAttrList = array();//主石，副石 属性    
        $stoneList = getStyleStoneByStyleSn($db,$style_sn);
        $stoneAttrList[11] = array('code'=>'zhushi_cat','name'=>'主石类型','value'=>'');
        $stoneAttrList[12] = array('code'=>'zhushi_shape','name'=>'主石形状','value'=>'');
        $stoneAttrList[21] = array('code'=>'fushi_cat','name'=>'副石类型','value'=>'');
        $stoneAttrList[22] = array('code'=>'fushi_shape','name'=>'副石形状','value'=>'');
        $stoneAttrList[23] = array('code'=>'fushi_yanse','name'=>'副石颜色','value'=>'');
        $stoneAttrList[24] = array('code'=>'fushi_jingdu','name'=>'副石净度','value'=>'');
        $stoneAttrList[25] = array('code'=>'fushi_zhong_total1','name'=>'副石1总重','value'=>'');
        $stoneAttrList[26] = array('code'=>'fushi_num1','name'=>'副石1粒数','value'=>'');
        $stoneAttrList[27] = array('code'=>'fushi_zhong_total2','name'=>'副石2总重','value'=>'');
        $stoneAttrList[28] = array('code'=>'fushi_num2','name'=>'副石2粒数','value'=>'');
        $stoneAttrList[29] = array('code'=>'fushi_zhong_total3','name'=>'副石3总重','value'=>'');
        $stoneAttrList[30] = array('code'=>'fushi_num3','name'=>'副石3粒数','value'=>'');
         
        foreach ($stoneList as $key=>$vo){
            if($key==1){
                $zhushi_shape_arr = array_unique(array_column($vo,'shape'));
                $zhushi_shape = trim(implode('|',$zhushi_shape_arr),'|');
                $zhushi_cat_arr = array_unique(array_column($vo,'stone_cat'));
                $zhushi_cat = trim(implode('|',$zhushi_cat_arr),'|');
                $stoneAttrList[11] = array('code'=>'zhushi_cat','name'=>'主石类型','value'=>$zhushi_cat);
                $stoneAttrList[12] = array('code'=>'zhushi_shape','name'=>'主石形状','value'=>$zhushi_shape);
            }else if($key==2){
                $fushi_cat_arr = array_unique(array_column($vo,'stone_cat'));
                $fushi_cat = trim(implode('|',$fushi_cat_arr),'|');
                $fushi_shape_arr = array_unique(array_column($vo,'shape'));
                $fushi_shape = trim(implode('|',$fushi_shape_arr),'|');
                $fushi_yanse_arr = array_unique(array_column($vo,'color'));
                $fushi_yanse = trim(implode('|',$fushi_yanse_arr),'|');
    
                $fushi_jingdu_arr = array_unique(array_column($vo,'clarity'));
                $fushi_jingdu = trim(implode('|',$fushi_jingdu_arr),'|');
                 
                $stoneAttrList[21] = array('code'=>'fushi_cat','name'=>'副石类型','value'=>$fushi_cat);
                $stoneAttrList[22] = array('code'=>'fushi_shape','name'=>'副石形状','value'=>$fushi_shape);
                $stoneAttrList[23] = array('code'=>'fushi_yanse','name'=>'副石颜色','value'=>$fushi_yanse);
                $stoneAttrList[24] = array('code'=>'fushi_jingdu','name'=>'副石净度','value'=>$fushi_jingdu);
            }
        }

        $zhiquan = $carat = $xiangkou = "";
        foreach ($attrlist as $vo){
            if($vo['code']=="zhiquan"){
                $zhiquan = trim($vo['value']);
            }else if($vo['code']=="cart" || $vo['code']=="zuanshidaxiao"){
                $carat = trim($vo['value']);
            }else if($vo['code']=="xiangkou"){
                $xiangkou = trim($vo['value']);
            }
        }   

        $fushiInfo = getStyleFushi($db,$style_sn, $carat,$xiangkou, $zhiquan);
        if(!empty($fushiInfo)){
            $stoneAttrList[25] = array('code'=>'fushi_zhong_total1','name'=>'副石1总重','value'=>$fushiInfo['fushi_zhong_total1']);
            $stoneAttrList[26] = array('code'=>'fushi_num1','name'=>'副石1粒数','value'=>$fushiInfo['fushi_num1']);

            $stoneAttrList[27] = array('code'=>'fushi_zhong_total2','name'=>'副石2总重','value'=>$fushiInfo['fushi_zhong_total2']);
            $stoneAttrList[28] = array('code'=>'fushi_num2','name'=>'副石2粒数','value'=>$fushiInfo['fushi_num2']);

            $stoneAttrList[29] = array('code'=>'fushi_zhong_total3','name'=>'副石3总重','value'=>$fushiInfo['fushi_zhong_total3']);
            $stoneAttrList[30] = array('code'=>'fushi_num3','name'=>'副石3粒数','value'=>$fushiInfo['fushi_num3']);
        }
        /* 
        else{
            $stoneAttrList[21]['value'] ='';
            $stoneAttrList[22]['value'] ='';
            $stoneAttrList[23]['value'] ='';
            $stoneAttrList[24]['value'] ='';
        }*/

        ksort($stoneAttrList);
        return $stoneAttrList;
    
    }

    function getStyleStoneByStyleSn($db,$style_sn){

        $shape_arr = array("0"=>"无",1=>"垫形",2=>"公主方",3=>"祖母绿",4=>"心形",5=>"蛋形",6=>"椭圆形",7=>"橄榄形",8=>"三角形",9=>"水滴形",10=>"长方形",11=>"圆形",12=>"梨形",13=>"马眼形");
        $stonecat_arr = array("0"=>"无","1"=>"圆钻","2"=>"异形钻","3"=>"珍珠","4"=>"翡翠","5"=>"红宝石","6"=>"蓝宝石","7"=>"和田玉","8"=>"水晶","9"=>"珍珠贝","10"=>"碧玺","11"=>"玛瑙","12"=>"月光石","13"=>"托帕石","14"=>"石榴石","15"=>"绿松石","16"=>"芙蓉石","17"=>"祖母绿","18"=>"贝壳","19"=>"橄榄石","20"=>"彩钻","21"=>"葡萄石","22"=>"海蓝宝","23"=>"坦桑石","24"=>"粉红宝","25"=>"沙佛莱","26"=>"粉红蓝宝石");
        $color_arr = array("0"=>"无","1" =>"F","2" =>"G","3" =>"H","4" =>"I","8" =>"I-J","5" =>"J","6" =>"K","9" =>"K-L","7" =>"L","10" =>"白","11" =>"M","12" =>"<N","13" =>"N","14" =>"D","15" =>"E");
        $clarity_arr = array("0"=>"无","1"=>"IF","2" => "VVS","3" => "VVS1","4" =>"VVS2","5" =>"VS","6" =>"VS1","7" =>"VS2","8" =>"SI","9" =>"SI1","10" =>"SI2","11" =>"I1","12" =>"I2","13" =>"VSN","14" =>"不分级");

        $sql = "select a.stone_position,a.stone_cat,a.stone_attr from front.rel_style_stone a inner join front.base_style_info b on a.style_id=b.style_id where b.style_sn='{$style_sn}'";
        $data = $db->getAll($sql);

        $stoneList = array();
        foreach($data as $vo){
            $stone = array();
            //stone_position = 1主石 2 副石
            $stone_postion = $vo['stone_position'];         
            //stone_cat=1 圆钻 圆形   stone_cat=2 异形钻 对应形状
            $stoneAttr = unserialize($vo['stone_attr']);
            if($vo['stone_cat']==1){
                $shape_name = "圆形"; 
            }else if($vo['stone_cat']==0){
                $stoneAttr = array();
                $shape_name = "无";
                continue;//石头类型为无 记录无效，忽略。
            }else{
                $shape_id = isset($stoneAttr['shape_fushi'])?$stoneAttr['shape_fushi']:'0';
                $shape_id = isset($stoneAttr['shape_zhushi'])?$stoneAttr['shape_zhushi']:$shape_id;
                $shape_name = isset($shape_arr[$shape_id])?$shape_arr[$shape_id]:$shape_id;
            }
            $color_id = isset($stoneAttr['color_fushi'])?$stoneAttr['color_fushi']:'0';
            $color_id = isset($stoneAttr['color_zhushi'])?$stoneAttr['color_zhushi']:$color_id;
            $color = isset($color_arr[$color_id])?$color_arr[$color_id]:$color_id;
             
            $clarity_id = isset($stoneAttr['clarity_fushi'])?$stoneAttr['clarity_fushi']:'0';
            $clarity_id = isset($stoneAttr['clarity_zhushi'])?$stoneAttr['clarity_zhushi']:$clarity_id;
            $clarity = isset($clarity_arr[$clarity_id])?$clarity_arr[$clarity_id]:$clarity_id;
            
            $zhushi_num = isset($stoneAttr['number'])?$stoneAttr['number']:'0';
            if(isset($stonecat_arr[$vo['stone_cat']])){
                $stone_cat = $stonecat_arr[$vo['stone_cat']];
            }else{
                $stone_cat = '无';
            }
            $stone['stone_postion'] = $stone_postion;//石头位置
            $stone['stone_cat'] = $stone_cat;//石头类型
            $stone['shape'] = $shape_name;//石头形状
            $stone['color'] = $color;//石头形状
            $stone['clarity'] = $clarity;//石头形状
            $stone['zhushi_num'] = $zhushi_num;//主石粒数
            $stoneList[$stone_postion][] = $stone;
        }
        return $stoneList;
        
    }

    function getStyleFushi($db,$style_sn,$stone,$xiangkou,$zhiquan){
        if($stone === '' && $xiangkou ===''){
            return array();
        }
        $zhiquan = round($zhiquan);//四舍五入
        $stone = trim($stone);
        $xiangkou = trim($xiangkou);
        $sql = "select finger as zhiquan,
                     sec_stone_weight as fushi_zhong1,
                     sec_stone_num  as fushi_num1,
                     sec_stone_weight_other as fushi_zhong2,
                     sec_stone_num_other as fushi_num2,
                     sec_stone_weight3 as fushi_zhong3,
                     sec_stone_num3 as fushi_num3 
        from front.app_xiangkou where style_sn='{$style_sn}'";
        if($stone!=='' && is_numeric($stone) && $stone>=0){
            $sql .=" and round(stone*1-0.05,4) <= {$stone} and {$stone}<= round(stone*1+0.04,4) order by abs(stone-{$stone}) asc";
        }else if($stone==='' && is_numeric($xiangkou) && $xiangkou>=0){
            $sql .=" and stone= ".$xiangkou."";
        }else{
            return array();
        }
        //echo $sql;
        $data = $db->getAll($sql);
        $fushiInfo = array();
        foreach ($data as $vo){
            $zhiquan_arr = explode('-',$vo['zhiquan']);
            $len = count($zhiquan_arr);
            if($len==2){
                $zhiquan_min = $zhiquan_arr[0];
                $zhiquan_max = $zhiquan_arr[1];
            }else if($len==1){
                $zhiquan_min = $zhiquan_arr[0];
                $zhiquan_max = $zhiquan_arr[0];
            }else {
                continue;
            }
            
            if($zhiquan>=$zhiquan_min && $zhiquan<=$zhiquan_max){
                $fushiInfo['fushi1'] = '';
                $fushiInfo['fushi_num1'] = $vo['fushi_num1'];
                $fushiInfo['fushi_zhong_total1'] = $vo['fushi_zhong1']/1;
                if($vo['fushi_num1']>0 && $vo['fushi_zhong1']>0){
                    $fushiInfo['fushi_zhong1'] = sprintf("%.4f",$vo['fushi_zhong1']/$vo['fushi_num1'])/1;
                    $fushiInfo['fushi1'] = $fushiInfo['fushi_zhong_total1'].'ct/'.$vo['fushi_num1'].'p';
                }else{
                    $fushiInfo['fushi_zhong1']=0;
                }
                
                $fushiInfo['fushi2'] = '';
                $fushiInfo['fushi_num2'] = $vo['fushi_num2'];
                $fushiInfo['fushi_zhong_total2'] = $vo['fushi_zhong2']/1;
                if($vo['fushi_num2']>0 && $vo['fushi_zhong2']>0){
                    $fushiInfo['fushi_zhong2'] = sprintf("%.4f",$vo['fushi_zhong2']/$vo['fushi_num2'])/1;
                    $fushiInfo['fushi2'] = $fushiInfo['fushi_zhong_total2'].'ct/'.$vo['fushi_num2'].'p';
                }else{
                    $fushiInfo['fushi_zhong2']=0;
                }
                
                $fushiInfo['fushi3'] = $vo['fushi_num3'];
                $fushiInfo['fushi_num3'] = $vo['fushi_num3'];
                $fushiInfo['fushi_zhong_total3'] = $vo['fushi_zhong3']/1;
                if($vo['fushi_num3']>0 && $vo['fushi_zhong3']>0){
                        $fushiInfo['fushi_zhong3'] = sprintf("%.4f",$vo['fushi_zhong3']/$vo['fushi_num3'])/1;
                    $fushiInfo['fushi3'] = $fushiInfo['fushi_zhong_total3'].'ct/'.$vo['fushi_num3'].'p';
                }else{
                    $fushiInfo['fushi_zhong3']=0;
                }
                
                break;
            }
        }
        return $fushiInfo;  
    }   

?>