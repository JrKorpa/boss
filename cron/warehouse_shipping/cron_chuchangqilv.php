<?php
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
require_once('MysqlDB.class.php');
set_time_limit(0);
ini_set('memory_limit','2000M');
$doTest = false;

if($doTest){
    $new_conf = [
        'dsn'=>"mysql:host=192.168.0.131;dbname=kucun_bak",
        'user'=>"root",
        'password'=>"123456",
        'charset' => 'utf8'
    ];
}else{
    $new_conf = [
        'dsn'=>"mysql:host=192.168.1.192;dbname=warehouse_shipping",
        'user'=>"cuteman",
        'password'=>"QW@W#RSS33#E#",
        'charset' => 'utf8'
    ];
}
$day = 1 ;
$t = time()-86400*$day;
$date = date("Y-m-d",$t);

$db = new MysqlDB($new_conf);
prof($db);//临时改的-----校对更新布产单标准出厂时间

$sql= "select dotime from kucun_bak.product_info_chuchang where dotime = '{$date}'";
$dotime = $db->getOne($sql);

if($dotime){
    die("已执行成功");
}

$sql="insert into kucun_bak.product_info_chuchang
(
    a_id,dotime,id,bc_sn,p_id,p_sn,style_sn,status,buchan_fac_opra,num,prc_id,prc_name,opra_uname,
    add_time,esmt_time,rece_time,info,from_type,consignee,edit_time,order_time,
    remark,bc_style,goods_name,xiangqian,factory_opra_status,customer_source_id,channel_id,caigou_info,
    create_user,weixiu_status,is_peishi,buchan_times,is_alone,cc_jiejiari,cc_day,
    to_factory_time,oqc_pass_time,wait_dia_starttime,wait_dia_endtime,diamond_type,qiban_type
)
select 
    NULL,IF(p.oqc_pass_time='0000-00-00 00:00:00',left(p.rece_time,10),left(p.oqc_pass_time,10)),p.id,p.bc_sn,p.p_id,p.p_sn,p.style_sn,p.status,p.buchan_fac_opra,p.num,p.prc_id,p.prc_name,p.opra_uname,
    p.add_time,p.esmt_time,p.rece_time,p.info,p.from_type,p.consignee,p.edit_time,p.order_time,
    p.remark,p.bc_style,p.goods_name,p.xiangqian,p.factory_opra_status,p.customer_source_id,p.channel_id,p.caigou_info,
    p.create_user,p.weixiu_status,p.is_peishi,p.buchan_times,p.is_alone,0,0,
    to_factory_time,oqc_pass_time,wait_dia_starttime,wait_dia_endtime,diamond_type,qiban_type
from kela_supplier.product_info p
   where 
   if(p.oqc_pass_time='0000-00-00 00:00:00',(p.status=9 AND p.rece_time like '{$date}%'),(p.oqc_pass_time like '{$date}%'))";
//die;
$res = $db->query($sql);

//from_type 1:采购单 2:客订单
//order_type : 1:客订单 2:采购单
$sql = "
   SELECT a_id,prc_id,p_id,style_sn,status,order_time,oqc_pass_time,rece_time,wait_dia_starttime,wait_dia_endtime,from_type
   FROM kucun_bak.product_info_chuchang
   WHERE 
        1
        AND dotime = '{$date}'
        AND ((oqc_pass_time like '{$date}%'
        AND oqc_pass_time not like '0000-00-00%' ) OR (status=9 AND rece_time like '{$date}%'))
   ;
";
$bcList = $db->getAll($sql);
/*
$sql="SELECT 
    p.id,pwt.order_type,holiday_time 
FROM 
    kela_supplier.`app_processor_info` p
    inner join kela_supplier.`app_processor_worktime` pwt on p.id = pwt.processor_id
where 
    pwt.order_type in (1,2)";
$fangList = $db->getAll($sql);
foreach($fangList as $key => $val){
    $from_type = $val['order_type']==1?2:1;
    $fangjiaList[$val['id']][$from_type]['holiday'] = explode(';',$val['holiday_time']);
}
*/
foreach($bcList as $key => $val){
    
    $a_id = $val['a_id'];
    $prc_id = $val['prc_id'];
    $from_type = $val['from_type'];//from_type 1:采购单 2:客订单
    $order_time = $val['order_time'];//接单时间
    $oqc_pass_time = $val['oqc_pass_time'];//质检通过时间
	$rece_time = $val['rece_time'];//工厂交货时间
	$status = $val['status'];//布产状态
	$wait_dia_start_time = $val['wait_dia_starttime'];
    $wait_dia_end_time = $val['wait_dia_endtime'];
    $wait_dia_start_timestamp = strtotime($val['wait_dia_starttime']);
    $wait_dia_end_timestamp = strtotime($val['wait_dia_endtime']);
    $order_timestamp = strtotime($order_time);
    $oqc_pass_timestamp = strtotime($oqc_pass_time);
	$rece_time_timestamp = strtotime($rece_time);
	$start_time=$order_timestamp;
	if($oqc_pass_time!='0000-00-00 00:00:00'){
		$end_time=$oqc_pass_timestamp;
	}else{
		$end_time=$rece_time_timestamp;
	}
	//根据款号查询系列
	if(!empty($val['style_sn'])){
		$sql="select xilie from front.base_style_info where style_sn='".$val['style_sn']."'";
		$xilie=$db->getOne($sql);
		if(!empty($xilie)){
		  $xilie_sql=",xilie='$xilie'";	
		}else{
		  $xilie_sql="";
		}
	}else{
		$xilie_sql="";
	}
	
	if($val['from_type']==2 and $val['p_id'] !=''){
		//根据订单明细查询起版款式类型
		$sql="select pq.kuan_type from purchase.purchase_qiban_goods as pq,app_order.app_order_details as aod where aod.id=".$val['p_id']." and aod.ext_goods_sn=pq.addtime";
		$kuan_type_arr=$db->getRow($sql);
		if(!empty($kuan_type_arr)){
		  $kuan_type=$kuan_type_arr['kuan_type'];
		  $kuan_type_sql=",kuan_type=$kuan_type";	
		}else{
		  $kuan_type_sql="";
		}
	}else{
		$kuan_type_sql="";
	}
	
	//(工厂质检通过时间/工厂出厂时间-订单开始接单日期)的用时
	if($end_time !=0 && $start_time != 0){
		$time1=($end_time-$start_time)/86400;
	}else{
		$time1=0;
	}
	
	
	//等钻用时
	if($wait_dia_end_timestamp !=0 && $wait_dia_start_timestamp != 0){
		$time2=($wait_dia_end_timestamp-$wait_dia_start_timestamp)/86400;
	}else{
		$time2=0;
	}
	
	
	
	//法定节假日放假时长
    $cc_jiejiari=getJiejiari($prc_id,$from_type,$start_time,$end_time,$db);
	
	$cc_day=$time1-$time2-$cc_jiejiari;
	if($cc_day< 0){
		$cc_day=0;
	}
	
    $cc_day=number_format($cc_day,4);

    
    //echo "<hr>";
    //var_dump($oqc_pass_timestamp,$add_timestamp);
    //var_dump($oqc_pass_time,$add_time);
    
    $sql = "UPDATE kucun_bak.product_info_chuchang SET cc_jiejiari = $cc_jiejiari,cc_day = $cc_day $xilie_sql $kuan_type_sql where a_id = $a_id;";
   // echo $sql;
   // die;
    $db->query($sql);
}

//var_dump($bcList);


if($res){
    die("执行成功");
}else{
    die("执行失败");
}

//ALTER TABLE `product_info_chuchang` ADD `dengzuan_jiejiari` INT(10) NOT NULL COMMENT '等钻中的节假日' ;   
//ALTER TABLE `product_info_chuchang` ADD `wd_day` DOUBLE(6,4) UNSIGNED NOT NULL COMMENT '等钻时长' ;

function get_data_arr($start_time,$end_time){
    $start_time_str=explode("-",$start_time);
    $end_time_str=explode("-",$end_time);

    $data_arr=array();
    while(true){
        if($start_time_str[0].$start_time_str[1].$start_time_str[2]>$end_time_str[0].$end_time_str[1].$end_time_str[2]) break;
        $data_arr[$start_time_str[0]."-".$start_time_str[1]."-".$start_time_str[2]]=$start_time_str[0]."-".$start_time_str[1]."-".$start_time_str[2];
        $start_time_str[2]++;
        $start_time_str=explode("-",date("Y-m-d",mktime(0,0,0,$start_time_str[1],$start_time_str[2],$start_time_str[0])));
    }
    krsort($data_arr);
    return $data_arr;
}
function getJiejiari($processor_id,$order_type,$start_time,$end_time,$db){
		$sql="select is_rest,is_work,holiday_time from kela_supplier.app_processor_worktime where processor_id=$processor_id and order_type=$order_type";
		$row=$db->getRow($sql);
		//如果没有工厂时间，就为0
		if(empty($row)){
			$time_num=0;
			return $time_num;
		}
		$is_rest=$row['is_rest'];
		$is_work=$row['is_work'];
		$holiday_time=$row['holiday_time'];
		$work_arr=explode(",", $is_work);
		$holiday_time_arr=explode(',', $holiday_time);
		if($is_rest==1){			
			$time_num=count($holiday_time_arr);			
		}else{
		  if($is_rest==2){
			$xinxi_arr=array();//保存供应商周末休日期
			for($t=$start_time;$t<=$end_time;$t=$t+86400){
				$w=date("w",$t);
				if($w==0){
					$xinxi_arr[]=date("Y-m-d",$t);			
				}
			}
			 
		  }elseif($is_rest==3){
			for($t=$start_time;$t<=$end_time;$t=$t+86400){
				$w=date("w",$t);
				if($w==0||$w==6){
					$xinxi_arr[]=date("Y-m-d",$t);
				}
			}
			
		 }
		    //供应商周末休息日期与放假日期合集	
            if(empty($xinxi_arr)){
				$bin_arr=$holiday_time_arr;
			}elseif(empty($holiday_time_arr)){
				$bin_arr=$xinxi_arr;
			}else{
				$bin_arr=array_unique(array_merge($xinxi_arr,$holiday_time_arr));
			}			
		    
            
		    //供应商周末休息日期与周末上班日期交集
			if(!empty($xinxi_arr) && !empty($work_arr)){
				$jiao_arr=array_intersect($xinxi_arr,$work_arr);
			}else{
				$jiao_arr=array();
			}
			
			$time_num=count($bin_arr)-count($jiao_arr);
	  }
		return $time_num;
		
	}
	
	
	function prof($db){
		$sql="select id,bc_sn,order_time,esmt_time,rece_time from kela_supplier.product_info where order_time > '2016-05-01' AND status in (4,9)";
		$rows=$db->getAll($sql);
		foreach($rows as $k=>$v){
			$rows[$k]['dd']=$esmt_time=getElestime($v['id'],$db);
			$sql="update kela_supplier.product_info set esmt_time='{$esmt_time}' where id= ".$v['id'];
		    $db->query($sql);
			//echo $k;
			//if($k==100) exit;
		}
		//print_r($rows);
	}
	function getElestime($id,$db){
		
		$proInfos = getBuChanInfoById($id,$db);
		$order_type=$proInfos['from_type']==1?2:1;
		$infos = getProcessorInfoByProId($proInfos['prc_id'],$order_type,$db);
		$behind_wait_dia = !empty($infos['behind_wait_dia'])?$infos['behind_wait_dia']:0;
			//更新出厂时间:未出厂 && 出厂时间大于当前时间
				
				if($order_type ==1){
					//客订单
					
					if($proInfos['qiban_type']==0){
						$cycle = $infos['wkqbzq'];
					}elseif($proInfos['qiban_type']==1){
						$cycle = $infos['ykqbzq'];
					}else{
						$cycle = $infos['normal_day'];
					}
				}else{
					//备货单
					$is_style = getStyleInfoByCgd($proInfos['p_sn'],$db);
					if($is_style ==1){
						//采购列表  --有款采购
						$cycle = $infos['ykqbzq'];
					}elseif($is_style ==0){
						//采购列表  --无款采购
						$cycle = $infos['wkqbzq'];
					}else{
						//采购列表  --标准采购
						$cycle = $infos['normal_day'];
					}
				}

                    $day='0000-00-00';
					if(!empty($cycle)){
						$order_time = strtotime($proInfos['order_time']);
						for($i=1;$i<=$cycle;$i++){
							$day = date('Y-m-d',strtotime('+'.$i.' day',$order_time));
								//放假日期
							if(strpos($infos['holiday_time'],$day) !== false){
									++$cycle;
									continue;
								}
								//暂时只能获得周末休息天数(默认周天休息)
								switch ($infos['is_rest']) {
									case '1':
										break;
									case '2':
										if(date('w',strtotime($day))== 0){
											$cycle = $cycle+1;
										}
										break;
									default:
										if(date('w',strtotime($day))== 6 || date('w',strtotime($day))== 0){
											$cycle = $cycle+1;
										}
										break;
								}											
							//周末上班
							if(strpos($infos['is_work'],$day) !== false && strpos('60',date('w',strtotime($day))) !== false){
									--$cycle;				
								}
							}
						}
						
						
					$pro_id = $proInfos['prc_id'];
					$order_sn =$proInfos['p_sn'];
					$style_sn =$proInfos['style_sn'];
					$from_type = $proInfos['from_type'];
					$order_type=($from_type==1)?2:1;	//数据表存储不一样
					$goodsinfos = getStockGoodsByOrderSn($order_sn,$style_sn,$db);
					//通过货号在商品列表中找到即为现货
					$is_exists = isExistsByGoodsId($goodsinfos['goods_id'],$db);
					$cert_id =getCertNumById($id,$db);
					$infos = getProcessorInfoByTypeAndId($pro_id,$order_type,$db);
						
				    if(empty($goodsinfos['goods_id']) && empty($cert_id)){
						//货号和证书号都为空，就是现货
		
						//获得现货等钻加时、假期、周末上班天数、周末休息时间
						$cycle1 = intval($infos['now_wait_dia']);
					
					}else{
						if($is_exists){
							//现货：现货等钻加时更新出厂时间
							
							//获得现货等钻加时、假期、周末上班天数、周末休息时间
							$cycle1 = intval($infos['now_wait_dia']);
					
						}else{
							//货号没找到，通过证书号去裸钻列表查找判断是期货还是现货
							$cert_id2 = preg_replace('/[a-zA-Z]{0,10}/', '', $cert_id);
							$goods_type = getGoodsTypeByCertId($cert_id,$cert_id2,$db);
							if($goods_type==2){
								//期货
																
						      //获得现货等钻加时、假期、周末上班天数、周末休息时间
								$cycle1 = intval($infos['wait_dia']);
							}else{
								//现货
								
							
								//获得现货等钻加时、假期、周末上班天数、周末休息时间
								$cycle1 = intval($infos['now_wait_dia']);
							}
						}
					}
					$wait_dia_starttime=$proInfos['wait_dia_starttime'];			
					$wait_dia_endtime=$proInfos['wait_dia_endtime'];
					$behind_wait_dia=$infos['behind_wait_dia'];
					$cycle2='';
					if($wait_dia_starttime != '0000-00-00 00:00:00'){
						if($wait_dia_endtime=='0000-00-00 00:00:00'){
							$time1=$wait_dia_starttime;
							$cycle2=$cycle1;
						}else{
							$cycle2=$behind_wait_dia;
							$time1=$wait_dia_endtime;
						}
					}
					
					
					
					$new_day="0000-00-00";
					if(!empty($cycle2)){
						for($i=1;$i<=$cycle2;$i++){
							$new_day = date('Y-m-d',strtotime('+'.$i.' day',strtotime($time1)));
							//放假日期
							if(strpos($infos['holiday_time'],$new_day) !== false){
								++$cycle2;
								continue;
							}
							//暂时只能获得周末休息天数(默认周天休息)
							switch ($infos['is_rest']) {
								case '1':
									break;
								case '2':
									if(date('w',strtotime($new_day))== 0){
										$cycle2 = $cycle2+1;
									}
									break;
								default:
									if(date('w',strtotime($new_day))== 6 || date('w',strtotime($new_day))== 0){
										$cycle2 = $cycle2+1;
									}
									break;
							}
							//周末上班
							if(strpos($infos['is_work'],$new_day) !== false && strpos('60',date('w',strtotime($new_day))) !== false){
								--$cycle2;
							}
						}
					}
						
                        $esmt_time = max($new_day,$day);
						return $esmt_time;
	}
	
	 function getBuChanInfoById($id,$db){
		$sql = "select * from kela_supplier.product_info where id=".$id;
		return $db->getRow($sql);

	}
	
	 function getProcessorInfoByProId($pro_id,$order_type='',$db){
		if($order_type ==''){
			$sql ="select * from kela_supplier.app_processor_worktime where processor_id='".$pro_id."';";
		}else{
			$sql ="select * from kela_supplier.app_processor_worktime where processor_id='".$pro_id."' AND order_type='".$order_type."'";
		}
		return $db->getRow($sql);
	}
	
	 function getStyleInfoByCgd($p_sn,$db){
		$sql = "select is_style from purchase.purchase_info where p_sn='".$p_sn."'";
		return $db->getOne($sql);
	}
	
	/*
	*通过订单获取钻石货号类型(现货或期货)
	* @author lrj
	*/
 	 function getStockGoodsByOrderSn($order_sn,$style_sn,$db){
 		$sql = "select d.is_stock_goods,d.goods_id,d.zhengshuhao from app_order.base_order_info b left join app_order.app_order_details d on b.id=d.order_id where b.order_sn='".$order_sn."' AND d.goods_sn='".$style_sn."'";
 		$res = $db->getRow($sql);
 		return $res;
 	}
	
	  /*
    *通过货品ID判断货品是不是现货
    *
    */
     function isExistsByGoodsId($goods_id,$db){
    	$sql ="select id from warehouse_shipping.warehouse_goods where goods_id='".$goods_id."'";
    	return $db->getOne($sql);

    }
	
	/*
	*通过布产单id获取证书号
	*/
	 function getCertNumById($id,$db){
		$sql ="select value from kela_supplier.product_info_attr where code='zhengshuhao' AND g_id='".$id."'";
		return $db->getOne($sql);
	}
	
	/*
	*通过供应商ID和订单类型取加工信息
	*
	*/

	 function getProcessorInfoByTypeAndId($prc_id,$order_type=1,$db){
		$sql = "select * from kela_supplier.app_processor_worktime where processor_id=".$prc_id." AND order_type=".$order_type;
		return $db->getRow($sql);
	}
	
	/*
	*通过证书号获取裸钻信息
	*
	*/
	 function getGoodsTypeByCertId($cert_id,$cert_id2='',$db){
		if(!empty($cert_id2)){
			$sql ="select good_type from front.diamond_info_all where cert_id='".$cert_id."' OR cert_id ='".$cert_id2."'";
		}else{
			$sql ="select good_type from front.diamond_info_all where cert_id='".$cert_id."'";
		}
		return $db->getOne($sql);

	}