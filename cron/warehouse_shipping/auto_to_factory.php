<?php
header("Content-type:text/html;charset=utf-8");
date_default_timezone_set('Asia/Shanghai');
define('SYS_SCOPE', 'boss'); 
file_put_contents('auto_buchan.log',date('Y-m-d H:i:s',time()).PHP_EOL,FILE_APPEND);
require_once('MysqlDB.class.php');
set_time_limit(0);
ini_set('memory_limit','2000M');

$new_conf = [
		'dsn'=>"mysql:host=192.168.1.192;dbname=kela_supplier",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
];

$db = new MysqlDB($new_conf);



$sql = "select * from product_info where status=1 and from_type<>1 limit 30";

$list1 = $db->getAll($sql);
if(empty($list1))
	exit('没有待分配的布产单');


file_put_contents('auto_buchan.log',date('Y-m-d H:i:s',time()).' : 开始分配工厂,总共:'.count($list1).PHP_EOL,FILE_APPEND);


$suc_num =0;
foreach ($list1 as $key => $info) {
	        $id = $info['id'];	        
            $res = _checkPeishiFor4C($info,$db);            
            if($res['res']!==true){

            	continue;
            }   
            $from_type =$info['from_type']; 
            $style_sn = $info['style_sn']; 
            $bc_sn =$info['bc_sn'];
            $p_sn = $info['p_sn'];
            $order_sn = $info['p_sn'];
		    if($from_type==1)
		    {
		        //$result['error'] = "采购类型布产单，不允许点击【分配工厂】，只能在采购列表或者采购布产列表分配工厂！";
		        continue;
		    }
         
            updatejinzhong($id,$db);


        
	        $fac_list = array();  
			if(SYS_SCOPE=='zhanting'  && strtolower($style_sn)=="dia"){
	                $res = checkCompanyType($db,array($id));
					if($res['success'] == 0)
					{
						//$result['title'] = '批量分配工厂';
						//$result['content'] = $res['error'];
						continue;
					}
					if($res['company_type']==3){
	                    //$fac_list = $model->getFactoryUserByID(639); //个体店裸钻布产单指定分配给工厂优星钻石（上海）有限公司
					    $sql  =  " select a.id as factory_id,a.name as factory_name,a.code ,p.opra_uname as gendan,'' as factory_sn from product_factory_oprauser as p,app_processor_info as a where p.prc_id=a.id  and a.id='639'";
						//echo  $sql;exit;
						$fac_list = $db->getAll($sql);
					}else{
					    //$fac_list =	$model->getFactoryUserByID(510);    //个体店裸钻布产单指定分配给批发合作商
					    $sql  =  " select a.id as factory_id,a.name as factory_name,a.code ,p.opra_uname as gendan,'' as factory_sn from product_factory_oprauser as p,app_processor_info as a where p.prc_id=a.id  and a.id='510'";
						//echo  $sql;exit;
						$fac_list = $db->getAll($sql);		        
			        }
			}else{
					//$apiModel = new ApiStyleModel();
					if(strtolower($style_sn)=="dia" || $style_sn ==""){
						$fac_list = array();
						if(strtolower($style_sn)=="dia"){
						    $sql  =  " select a.id as factory_id,a.name as factory_name,a.code ,p.opra_uname as gendan,'' as factory_sn,1 as is_factory from product_factory_oprauser as p,app_processor_info as a where p.prc_id=a.id  and a.id='510'";
							$fac_list = $db->getAll($sql);
						}					    
					}else{
					    //$fac_list = $apiModel->getFactryInfo($style_sn);
					    $xiangkou = 0;
					    $row = $db->getRow("SELECT `id`,`g_id`,`code`,`name`,`value` FROM product_info_attr WHERE code='xiangkou' and  g_id ='{$id}'");
					    if(!empty($row) && !empty($row['value']))
					    	$xiangkou = $row['value'];
					    $sql = "select r.*,f.name as factory_name from front.rel_style_factory r left join kela_supplier.app_processor_info f on r.factory_id=f.id where  r.style_sn ='{$style_sn}' ORDER BY abs({$xiangkou}-r.xiangkou)";//ORDER BY f_id 很重要误删。涉及到布产提示排序
                        $fac_list = $db->getAll($sql);
                        if(empty($fac_list) && $style_sn='QIBAN'){
                        	$sql = "select f.id as factory_id,f.name as factory_name,1 as is_factory from kela_supplier.app_processor_info f where f.id=509";//ORDER BY f_id 很重要误删。涉及到布产提示排序
                            $fac_list = $db->getAll($sql);
                        }
					}	

					if(empty($fac_list)){
						//找不到关联工厂
						//continue;
					}else{
						//【布产类型】是【订单】时 过滤分配工厂数据
						if ($from_type == 2){							
							//查看是否有起版号
							//$sales = new ApiSalesModel();		
							//$qb_id = $sales->getQiBanIdByWhere($model->getValue('p_sn'),$model->getValue('style_sn'));
							$qb_id = 0;
							$qb_row = $db->getRow("select d.goods_id from app_order.app_order_details d,app_order.base_order_info o where o.id=d.order_id and d.goods_sn='{$style_sn}' and d.goods_type ='qiban' and o.order_sn='{$order_sn}'");
							if(!empty($qb_row))
								$qb_id = $qb_row['goods_id'];
							if(!empty($qb_id)){
								//有起版号
								//$purchaseApi = new ApiPurchaseModel();
								//$qiban_info = $purchaseApi->GetQiBianGoodsByQBId($qb_id);
								$sql = "SELECT * FROM purchase.purchase_qiban_goods WHERE addtime='{$qb_id}'";
	                            $qiban_info = $db->getRow($sql);

								if (!empty($qiban_info) && $qiban_info['kuanhao'] != 'QIBAN'){
									foreach($fac_list AS $key => $val){
									//工厂列表只需要显示起版号中的工厂和款式库中的默认工厂
									   if ($qiban_info['gongchang_id'] != $val['factory_id'] && $val['is_factory'] == 0){
										  unset($fac_list[$key]);
									   }
									}
								}
							
							}else{
							    
							    $arr_list = $db->getAll("SELECT `id`,`g_id`,`code`,`name`,`value` FROM product_info_attr WHERE  g_id ='{$id}'");
							    $arr_list =array_column($arr_list,'value','code');
							    if(empty($arr_list)){
	        	                    file_put_contents('auto_buchan.log',date('Y-m-d H:i:s',time()).' : '.$bc_sn.' : 布产单属性为空'.PHP_EOL,FILE_APPEND);
							    	continue;							    	
							    }
							    if(empty($arr_list['face_work']) && strtolower($style_sn)!="dia"){
	        	                    file_put_contents('auto_buchan.log',date('Y-m-d H:i:s',time()).' : '.$bc_sn.' : 表面工艺为空'.PHP_EOL,FILE_APPEND);
							    	continue;
							    }
							    if($info['xiangqian']=='需工厂镶嵌' || $info['xiangqian']=='客户先看钻再返厂镶嵌'){
                                    if(empty($arr_list['zhengshuhao']) || empty($arr_list['cart']) || empty($arr_list['color']) || empty($arr_list['clarity'])){
		        	                    file_put_contents('auto_buchan.log',date('Y-m-d H:i:s',time()).' : '.$bc_sn.' : 当镶嵌方式为[需工厂镶嵌、客户先看钻再返厂镶嵌] 证书号、主石重、主石颜色、主石净度有任一空值 不自动分配工厂'.PHP_EOL,FILE_APPEND);
								    	continue;                                    	
                                    }
							    }
							    if($info['xiangqian']=='镶嵌4C裸钻' || $info['xiangqian']=='工厂配钻工厂镶嵌'){
                                    if(empty($arr_list['cart']) || empty($arr_list['color']) || empty($arr_list['clarity'])){
		        	                    file_put_contents('auto_buchan.log',date('Y-m-d H:i:s',time()).' : '.$bc_sn.' : 当镶嵌方式为[镶嵌4C裸钻、工厂配钻工厂镶嵌] 主石重、主石颜色、主石净度有任一空值 不自动分配工厂'.PHP_EOL,FILE_APPEND);
								    	continue;                                    	
                                    }
							    }
                              
                           
                         
								//非起版号下单，分配的时候只需要显示该款在款式库中的默认工厂即可
								 foreach($fac_list AS $key => $val){
									if ($val['is_factory'] == 0){
										unset($fac_list[$key]);
									} 
								}
							}
						}
					
						
					}		
	        }
	        if(empty($fac_list)){
	        	file_put_contents('auto_buchan.log',date('Y-m-d H:i:s',time()).' : '.$bc_sn.' : 找不到默认工厂'.PHP_EOL,FILE_APPEND);
	        	continue;
	        }
	        /*
	        $new_fac_list = array();
			//分配工厂数据需要过滤重复数据
			foreach($fac_list AS $key => $val){
				$new_fac_list[$val['factory_id']] = $val;
			}
			$fac_list = $new_fac_list;
			*/
            $keys = array_keys($fac_list);
			$factory = $fac_list[$keys[0]];


		    /***** 处理分配工厂逻辑 *****/
		    $disFacData = array(
		        "prc_id" => $factory['factory_id'],
		        "prc_name" => $factory['factory_name'],
		        "bc_id" => $id,
		        "from_type" => $from_type,
		        "bc_sn" => $bc_sn,
		        'p_sn' => $p_sn
		    );
			$db->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$db->db()->beginTransaction();//开启事务
			try{		    
			    $res = DistributionFac($disFacData,$db);
			    if($res['success']!==1){
			    	file_put_contents('auto_buchan.log',date('Y-m-d H:i:s',time()).' : '.$bc_sn.' : 自动分配工厂失败'.$res['error'].PHP_EOL,FILE_APPEND);
					$db->db()->rollback();//事务回滚
					$db->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					continue;
			    }
			    $res = createPeishiList($db,$id,'insert','自动分配工厂');	
			    if($res['success']!==1){
			    	file_put_contents('auto_buchan.log',date('Y-m-d H:i:s',time()).' : '.$bc_sn.' : 创建配石单失败'.PHP_EOL,FILE_APPEND);
					$db->db()->rollback();//事务回滚
					$db->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					continue;			    	
			    }
			    
				$db->db()->commit();
				$db->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交	
                $suc_num++;
			}catch(Exception $e){
				    file_put_contents('auto_buchan.log',date('Y-m-d H:i:s',time()).' : '.$bc_sn.' : '.json_encode($e).PHP_EOL,FILE_APPEND);
				    echo json_encode($e);
					$db->db()->rollback();//事务回滚
					$db->db()->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					return $e->getMessage();	
			}
}

file_put_contents('auto_buchan.log',date('Y-m-d H:i:s',time()).' : 总共分配成功:'.$suc_num.PHP_EOL,FILE_APPEND);


		


	#根据批量ids查询是托管店还是经销商布产单
	function checkCompanyType($db,$ids)
	{
		$result = array('success'=>0,'error'=>'','company_type'=>'');
		$id_s  = join("','",$ids);
		
		$sql = "select distinct c.company_type from product_info p,app_order.app_order_details d,app_order.base_order_info o left join cuteframe.sales_channels s on o.department_id=s.id left join cuteframe.company c on s.company_id=c.id where p.p_id=d.id and d.order_id=o.id and p.id in ('{$id_s}') ";
		$company_type_num = $db->getAll($sql);		
		if(count($company_type_num)>1)
		{
			$result['error']="裸钻布产批量分配工厂只能选择同公司类型的布产单";
			return $result;
		}
		$result['company_type'] = $company_type_num[0]['company_type'];
		$result['success'] = 1;
		return $result;
	}
;
    function _checkPeishiFor4C($bcInfo,$db)
    {
        $result = array('content'=>'','res'=>false);
        if(empty($bcInfo)){
            $result['content'] = "布产信息为空,error：".__LINE__; 
            return $result;
        }

        $id = $bcInfo['id'];
        $bc_sn = $bcInfo['bc_sn'];
        $is_peishi = $bcInfo['is_peishi'];
        $bucan_status = $bcInfo['status'];
        $order_detail_id = $bcInfo['p_id'];//app_order_details 的主键ID值
        $order_sn = $bcInfo['p_sn'];
        $consignee= $bcInfo['consignee'];
        //裸钻4C布产单
        if($is_peishi==1){           
            $data = $db->getRow("select * from product_info_4c where id='$id'");
            if(empty($data)){
                $result['content'] = "4C裸钻布产记录查询失败,error：".__LINE__;
                return $result;
            }else if($data['peishi_status']==0){
                $result['content'] = "当前裸石还未完成4C配钻,请先配钻！提示：需要等待钉钻部对该裸石进行4C配钻后，才可以分配工厂";
                return $result;
            }
        
        }
        else if($is_peishi==2){
            $zhengshuhao = '';//查询当前空托的证书号
            $sql = "SELECT `id`,`g_id`,`code`,`name`,`value` FROM product_info_attr WHERE g_id = '{$id}'";
		    $dataAttr = $db->getAll($sql);
            //print_r($dataAttr);            
            if(!empty($dataAttr)){
	            foreach($dataAttr as $val){
	                if($val['code']=="zhengshuhao"){
	                    $zhengshuhao = $val['value'];
	                }
	            }
            }
            if($zhengshuhao==''){
                $result['content'] = "当前空托的证书号没有填写！";
                return $result;
            }
            
            //检查空托是否配石
            $checkHasPeishi = $db->getRow("select * from product_info_4c where kt_order_detail_id='{$order_detail_id}'");
            if(empty($checkHasPeishi)){
                $result['content'] = "当前空托未配石,请配石!";
                return $result;
            }
            
            //检查证书号对应裸钻是否已经下单            
            $salesModel = new SalesModel(27);
            $diamond_info = array();
            //匹配裸钻 begin
            $sql = "select a.*,b.order_sn,b.consignee from app_order.app_order_details a left join app_order.base_order_info b on a.order_id=b.id where a.zhengshuhao='{$zhengshuhao}' and a.goods_type ='lz' and a.buchan_status<=2";
			//echo $sql;
			$order_detail_list= $db->getAll($sql); 
			if(empty($order_detail_list)){
                $result['content'] = "当前布产单{$bc_sn}对应的订单不存在!";
                return $result;				
			}       
            foreach($order_detail_list as $vo){
                if($vo['consignee'] ==$consignee){
                    $diamond_info = $vo;
                }
                if($order_sn ==$vo['order_sn']){
                    //与空托订单号一样的裸钻，优先匹配
                    break;
                }
            }

            if(empty($diamond_info)){
                $result['content'] = "当前空拖关联的裸钻【证书号：<font color='red'>{$zhengshuhao}</font>】还未下单。";
                return $result;
            }else if($diamond_info['is_peishi']<1){
                $result['content'] = "当前空拖关联的裸钻【证书号：<font color='red'>{$zhengshuhao}</font>】 不支持4C配钻，此空托镶嵌方式可能有误！当下解决方案：将空托镶嵌方式改为“非镶嵌4C裸钻”方式，订单按非4C流程走";
                return $result;
            }
            //匹配裸钻 end 
                   
            if(empty($checkHasPeishi)){
                $result['content'] = "当前空拖关联的裸石还未完成4C配钻，请先配钻！。提示：需要等待钉钻部对此空托镶嵌的裸石【证书号：<font color='red'>{$zhengshuhao}</font>】进行4C配钻后，才可以分配工厂";
                return $result;
            }        	  
        }
        $result['res'] = true;
        return $result;
    }



	/**
	* 布产分配工厂
	*/
	function distributionFac($data,$db){
	    $bc_id  = $data['bc_id'];
	    $prc_id = $data['prc_id'];
	    $prc_name = $data['prc_name'];
	    $from_type = $data['from_type'];
	    $bc_sn = $data['bc_sn'];
	    $p_sn  = $data['p_sn'];
	    $order_sn = $p_sn;
		//根据工厂 找默认的跟单人
		//$oprauserModel = new ProductFactoryOprauserModel(14);
		//$salesModel = new SalesModel(27); 
		//$man = $oprauserModel->select2('`opra_user_id`,`opra_uname`,`production_manager_name`'," `prc_id`={$prc_id} " , $type = 'row');
		$man = $db->getRow("select `opra_user_id`,`opra_uname`,`production_manager_name` from product_factory_oprauser where prc_id='{$prc_id}'");
		if(empty($man)){
			return array('success'=>0 , 'error'=> "工厂：<span style='color:red;'>{$prc_name}</span> 没有绑定跟单人");
		}
		if($man['production_manager_name'] != ''){
			$production_manager_name=",生产经理：".$man['production_manager_name'];
			$status = 3;
		}else{
			$status = 3;
			$production_manager_name='';
		}
		$time = date('Y-m-d H:i:s');
		try{
			//记录分配的工厂 , 记录分配的跟单人 , 变更布产状态
			$tip = "记录分配的工厂 ,记录分配的跟单人,变更布产状态";
			$sql = "UPDATE `product_info` SET `prc_id` = {$prc_id} , `prc_name` = '{$prc_name}' , `opra_uname` = '{$man['opra_uname']}',`production_manager_name` = '{$man['production_manager_name']}' , `status` = $status,to_factory_time='{$time}',`edit_time`='{$time}' WHERE `id` = {$bc_id}";
			//echo $sql;
			$db->query($sql);

			//记录日志
			$remark = "自动分配工厂成功：布产单：".$bc_sn.",分配工厂：".$prc_id.",跟单人：".$production_manager_name;//prc_name//$man['opra_uname']
			//如果是从订单来源的布产要推送状态到订单
			if($from_type == 2)
			{
			    //布产单和订单有绑定关系
				//$rec = $this->judgeBcGoodsRel($bc_id);
				$sql = "SELECT  pg.`bc_id`, pg.`goods_id`,p.`status` FROM `product_goods_rel` as pg left join `product_info` as p  on pg.`bc_id`=p.`id` WHERE p.`id` = {$bc_id}";
		        $rec = $db->getRow($sql);
				if(!empty($rec)){		
				    $tip = "更新订单商品布产状态";
				    $detail_id = $rec['goods_id'];
				    $sql = 'update app_order.app_order_details set buchan_status=3 where id='.$detail_id;
					$db->query($sql);
					//更新订单主表 布产状态
					$tip = "更新订单主表布产状态";
					$sql="select min(d.buchan_status) as buchan_status from app_order.base_order_info o,app_order.app_order_details d where o.id=d.order_id and d.is_stock_goods=0 and d.is_return=0 and o.order_sn='{$order_sn}'";
					$rowbc=$db->getRow($sql);					
					$buchan_status=5;
					if($rowbc!=false){
						$min_buchan_status=$rowbc['buchan_status'];
						if($min_buchan_status==10 || $min_buchan_status==11)
							$buchan_status=5;
						if($min_buchan_status==9)
							$buchan_status=4;
						if(in_array($min_buchan_status,array(4,5,6,7,8)))
								$buchan_status=3;
						if($min_buchan_status==3 || $min_buchan_status==2)
							$buchan_status=2;
						if($min_buchan_status==1)
							$buchan_status=1;
					}
					$sql = "UPDATE app_order.base_order_info SET  `buchan_status`={$buchan_status} WHERE `order_sn`='{$order_sn}'";
					//echo $sql;
					$db->query($sql);	
				}
				//回写操作日志到订单日志
				$tip = "回写操作日志到订单日志";
				$order_remark = "布产单{$bc_sn}自动分配工厂:{$prc_id}";
				$sql = "insert into app_order.app_order_action select 0,o.id,o.order_status,o.send_good_status,o.order_pay_status,'admin',now(),'{$order_remark}' from app_order.base_order_info o where o.order_sn='{$order_sn}'";
	            $db->query($sql);
			}
			//记录布产操作日志	
			$tip = "记录布产操作日志";
			$sql = "INSERT INTO `product_opra_log`(`bc_id`, `status`, `remark`, `uid`, `uname`, `time`) VALUES ({$bc_id}, $status,'{$remark}',1,'admin','{$time}')";
			$db->query($sql);
			return array('success'=>1,'error'=>'');
		}catch(Exception $e){//捕获异常
			file_put_contents('auto_buchan.log',date('Y-m-d H:i:s',time()).' : '.$bc_sn. $sql .PHP_EOL,FILE_APPEND);
			throw $e;
			return array('success' => 0 , 'error'=>"自动分配工厂失败。提示：distributionFac函数执行失败。<!--{$sql}-->");
		}		
	}





    /**
     * 生成配石单
     * @param unknown $id
     * @return multitype:number string
     */
    function createPeishiList($db,$id,$act="insert",$type="自动分配工厂"){
    
        $result = array('success' => 0,'error' => '');        
        $olddo = $db->getRow("select * from product_info where id='{$id}'");
        $bc_sn = $olddo['bc_sn'];
        $p_sn = $olddo['p_sn'];
        $bc_status = $olddo['status'];
        $xiangqian = $olddo['xiangqian'];
        $style_sn = $olddo['style_sn'];
        $factory_id = $olddo['prc_id'];
        //布产列表，采购类型的布产单，不允许点击【分配工厂】，只能在采购列表或者采购布产列表分配工厂
        $from_type = $olddo['from_type'];
        //初始化、不需布产、已出厂、已取消，作废,配石单不需要做任何判断
        //不需布产11，已取消10，已出厂9，作废8，部分出厂7，质检完成6，质检中5，生产中4，已分配3，待分配2,初始化1

        /***** 处理是否生成 配石单逻辑  开始*****/
        //查询封装配石信息
        //$attrList = $attrModel->getGoodsAttr($id);
        $sql = "SELECT `id`,`g_id`,`code`,`name`,`value` FROM product_info_attr WHERE g_id = '{$id}'";
        $attrList =$db->getAll($sql);
        $attrList = array_column($attrList,'value','code');
    
        $zhushi_carat = isset($attrList['cart'])?$attrList['cart']:'';//主石重
        $zhushi_carat = isset($attrList['carat'])?$attrList['carat']:$zhushi_carat;//主石重
        $zhushi_carat = isset($attrList['zuanshidaxiao'])?$attrList['zuanshidaxiao']:$zhushi_carat;//主石重
               
        $zhushi_num = isset($attrList['zhushi_num'])?$attrList['zhushi_num']:'0';//主石粒数
        $zhushi_num = $zhushi_carat>0 && $zhushi_num<=0?1:$zhushi_num;
        //特殊主石重拆分处理
        if(!is_numeric(trim($zhushi_carat))){
            $zhushi_carat = str_replace(" ", '', $zhushi_carat);
            if(preg_match("/(\d+(\.\d+)?)ct/is", $zhushi_carat,$arr)) {
                $zhushi_carat = $arr[1]/1;
            }
            if(preg_match("/(\d+?)p/is", $zhushi_carat,$arr)){
                $zhushi_num = $arr[1]/1;
            }            
        }
        
        $zhushi_cat = isset($attrList['zhushi_cat'])?$attrList['zhushi_cat']:'';//主石类型
        $zhushi_yanse = isset($attrList['color'])?$attrList['color']:'';//主石颜色
        $zhushi_yanse = isset($attrList['yanse'])?$attrList['yanse']:$zhushi_yanse;//主石颜色
    
        $zhushi_jingdu = isset($attrList['clarity'])?$attrList['clarity']:'';//主石净度
        $zhushi_jingdu = isset($attrList['jingdu'])?$attrList['jingdu']:$zhushi_jingdu;//主石净度
         
        $zhushi_shape = isset($attrList['zhushi_shape'])?$attrList['zhushi_shape']:'';//主石形状
        $zhushi_cert = isset($attrList['cert'])?$attrList['cert']:'';//主石证书类型
        $zhushi_zhengshuhao = isset($attrList['zhengshuhao'])?$attrList['zhengshuhao']:'';//主石证书类型
        $zhushi_zhengshuhao = isset($attrList['zhengshu'])?$attrList['zhengshu']:$zhushi_zhengshuhao;//主石证书类型
    
        $fushi_zhong1 = isset($attrList['fushi_zhong_total1'])?$attrList['fushi_zhong_total1']:'';//副石1重
        $fushi_num1 = isset($attrList['fushi_num1'])?$attrList['fushi_num1']:'';//副石1粒数
        $fushi_zhong2 = isset($attrList['fushi_zhong_total2'])?$attrList['fushi_zhong_total2']:'';//副石2重
        $fushi_num2 = isset($attrList['fushi_num2'])?$attrList['fushi_num2']:'';//副石2粒数
        $fushi_zhong3 = isset($attrList['fushi_zhong_total3'])?$attrList['fushi_zhong_total3']:'';//副石3重
        $fushi_num3 = isset($attrList['fushi_num3'])?$attrList['fushi_num3']:'';//副石3粒数
        $fushi_cat = isset($attrList['fushi_cat'])?$attrList['fushi_cat']:'';//副石类型
        $fushi_yanse = isset($attrList['fushi_yanse'])?$attrList['fushi_yanse']:'';//副石类型
        $fushi_jingdu = isset($attrList['fushi_jingdu'])?$attrList['fushi_jingdu']:'';//副石类型
        $fushi_shape = isset($attrList['fushi_shape'])?$attrList['fushi_shape']:'';//副石类型
    
        $xiangqianArr = array("工厂配钻，工厂镶嵌");
        $stone_position = array(0=>'主石',1=>'副石1',2=>'副石2',3=>'副石3');
        $stoneList = array();
        $isPeishiList = array();
        foreach ($stone_position as $p=>$posName){
            $stoneList[$p] = array("order_sn"=>$p_sn,
                "rec_id"=>$id,
                "peishi_status"=>0,
                "add_time"=>date('Y-m-d H:i:s'),
                "last_time"=>date('Y-m-d H:i:s'),
                "add_user"=>'admin',
                "color"=>"",
                "clarity"=>"",
                "shape"=>"",
                "cert"=>"",
                "zhengshuhao"=>"",
                "carat"=>"",
                "stone_num"=>"",
                "stone_cat"=>"",
                "stone_position"=>$p
            );
            $isPeishiList[$p] = true;
            if($p == 0){
    
                $stoneList[$p]['color'] = $zhushi_yanse;
                $stoneList[$p]['clarity'] = $zhushi_jingdu;
                $stoneList[$p]['shape'] = $zhushi_shape;
                $stoneList[$p]['cert'] = $zhushi_cert;
                $stoneList[$p]['zhengshuhao'] = $zhushi_zhengshuhao;
                $stoneList[$p]['carat'] = $zhushi_carat;
                $stoneList[$p]['stone_num'] = $zhushi_num;
                $stoneList[$p]['stone_cat'] = $zhushi_cat;
               if(in_array($xiangqian,$xiangqianArr)){
                    if($zhushi_carat<=0 || $zhushi_num<=0 || strtoupper($style_sn)=='DIA')
                    {
                        $isPeishiList[$p] = false;
                        continue;
                    }
                }else{
                    $isPeishiList[$p] = false;
                    continue;
                }
    
    
            }else{
    
                if($p==1){
                    if($fushi_num1<=0){
                        $isPeishiList[$p] = false;
                        $stoneList[$p]['carat'] = 0;
                        $stoneList[$p]['stone_num'] = 0;
                    }else{
                        $stoneList[$p]['carat'] = sprintf("%.4f",$fushi_zhong1/$fushi_num1)/1;
                        $stoneList[$p]['stone_num'] = $fushi_num1;
                    }
                }else if($p==2){
                    if($fushi_num2<=0){
                        $isPeishiList[$p] = false;
                        $stoneList[$p]['carat'] = 0;
                        $stoneList[$p]['stone_num'] = 0;
                    }else{
                        $stoneList[$p]['carat'] = sprintf("%.4f",$fushi_zhong2/$fushi_num2)/1;
                        $stoneList[$p]['stone_num'] = $fushi_num2;
                    }
    
                }else if($p==3){
                    if($fushi_num3<=0){
                        $isPeishiList[$p] = false;
                        $stoneList[$p]['carat'] = 0;
                        $stoneList[$p]['stone_num'] = 0;
                    }else{
                        $stoneList[$p]['carat'] = sprintf("%.4f",$fushi_zhong3/$fushi_num3)/1;
                        $stoneList[$p]['stone_num'] = $fushi_num3;
                    }
                }
                $stoneList[$p]['color'] = $fushi_yanse;
                $stoneList[$p]['clarity'] = $fushi_jingdu;
                $stoneList[$p]['shape'] = $fushi_shape;
                $stoneList[$p]['zhengshuhao'] = "";
                $stoneList[$p]['cert'] = "";
                $stoneList[$p]['stone_cat'] = $fushi_cat;
            }
    
        }
        //提交保存配石信息
        try{
            foreach ($isPeishiList as $p=>$is_peishi){
                $peishiData = $stoneList[$p];
                //7.1布产单分配工厂，产生配石单时，配石状态根据以下规则自动更新：
                $peishiData['peishi_status'] = getNewPeishiStatus($db,$factory_id,$peishiData);
                $stoneTypeName = isset($stone_position[$p])?$stone_position[$p]:'';
                $exists = $db->getRow("select * from peishi_list where rec_id='{$id}' and stone_position='{$p}'");
                if(!empty($exists)){
                    $exists['peishi_status'] = (int)$exists['peishi_status'];
                    //a .由【有】到【无】
                    if($is_peishi==false){
                        //a 1、配石单的原有状态是不需配石1\已送生产部4，则新的配石状态不变
                        if(in_array($exists['peishi_status'],array(1,4))){
                            $peishiData['peishi_status'] = $exists['peishi_status'];
                        }
                        //a 2、配石单的原有状态是未操作0\配石中2\厂配石3\备用钻6\采购中5，则新的配石状态更新为不需配石1
                        if(in_array($exists['peishi_status'],array(0,2,3,5,6))){
                            $peishiData['peishi_status'] = 1;
                        }
                    }else{
                        //b 由【有】到【有】                                               
                        //b 2 配石单的原有状态是已送生产部4，则新的配石状态不变
                        if(in_array($exists['peishi_status'],array(4))){
                            $peishiData['peishi_status'] = $exists['peishi_status'];
                        }else{
                            //b 1.1如果判断后的状态应该是未操作0，再分情况更新
                            if($peishiData['peishi_status']==0){
                                //b 1.1.1如果原状态是未操作0、配石中2、采购中5，则状态不变
                                if(in_array($exists['peishi_status'],array(0,2,5))){
                                    $peishiData['peishi_status'] = $exists['peishi_status'];
                                }
                                //b 1.1.2如果原状态是不需配石1\厂配石3\备用钻6,则状态更新为未操作0
                                if(in_array($exists['peishi_status'],array(1,3,6))){
                                    $peishiData['peishi_status'] = 0;//未操作
                                }
                                //b 1.2如果判断后的状态应该是厂配石，直接把状态更新为厂配石
                                //b 1.3如果判断后的状态应该是不需配石，直接把状态更新为不需配石                                
                            }
                            
                        }

                    }//end if($is_peishi==false){
                    
                    $peishiRemark = '';
                    $peishi_id = $exists['id'];                    
                    $peishi_array = $db->getAll("select i.* from cuteframe.dict_item i,cuteframe.dict d where i.dict_id=d.id and d.name='peishi_status'");
                    
                    //$peishi_status_old_name = $dd->getEnum('peishi_status',$exists['peishi_status']);
                    $peishi_array = array_column($peishi_array,'label','name');
                    $peishi_status_old_name =  $peishi_array[$exists['peishi_status']];
                    $peishi_status_old_name = $peishi_status_old_name?$peishi_status_old_name:'未操作';

                    //$peishi_status_new_name = $dd->getEnum('peishi_status',$peishiData['peishi_status']);
                    $peishi_status_new_name =  $peishi_array[$peishiData['peishi_status']];
                    $peishi_status_new_name = $peishi_status_new_name?$peishi_status_new_name:'未操作';
                                       
                    $fielterFields = array('add_time','last_time','peishi_status');
                    $changeDataLog = getDataChangeLog($peishiData,$exists,$fielterFields);
                          
                    $db->update2('peishi_list',$peishiData,"id={$peishi_id}");
                    if($exists['peishi_status']==$peishiData['peishi_status']){
                        $peishiRemark = "{$type}：{$stoneTypeName}配石单{$peishi_id}已存在<br/>更新数据：".$changeDataLog;
                        if($changeDataLog==''){
                            $peishiRemark = "";
                        }    
                    }else{
                        //$peishiListModel->update($peishiData,"id={$peishi_id}");
                        $db->update2('peishi_list',$peishiData,"id={$peishi_id}");
                        $peishiRemark = "{$type}：{$stoneTypeName}配石单{$peishi_id}已存在，更新配石单：配石状态由【{$peishi_status_old_name}】改为【{$peishi_status_new_name}】";
                        if($changeDataLog){
                            $peishiRemark .= ",".$changeDataLog;
                        }
                    }
    
                }else {
                    //c 由【无】到【有】，重新生成配石单，状态判断操作7.1
                    if($is_peishi==false){
                        continue;
                    }
                    $peishi_array = $db->getAll("select i.* from cuteframe.dict_item i,cuteframe.dict d where i.dict_id=d.id and d.name='peishi_status'");
                    $peishi_array = array_column($peishi_array,'label','name');
                    $peishi_status_new_name = !empty($peishiData['peishi_status']) ? $peishi_array[$peishiData['peishi_status']] : '';
                    $peishi_status_new_name = $peishi_status_new_name?$peishi_status_new_name:'未操作';
                    
                    //$peishi_id = $peishiListModel->saveData($peishiData,array());
                    $db->insert2('peishi_list',$peishiData);
                    $peishi_id = $db->insertId();
                    $peishiRemark= "{$type}：布产单{$bc_sn}生成{$stoneTypeName}配石单{$peishi_id},默认状态【{$peishi_status_new_name}】";
                }
    
                if($peishiRemark!=''){
		        	$data = array(
			        'peishi_id'=>$peishi_id,
			        'remark'=>$peishiRemark,
			        'add_time'=>date('Y-m-d H:i:s'),
			        'action_name'=>'admin',
			        );	        
                    $db->insert2('peishi_list_log',$data);
                    //布产记录操作日志
		            $newdo=array(
					'bc_id'		=> $id,
					'status'	=> $bc_status,
					'remark'	=> $peishiRemark,
					'uid'		=> 1,
					'uname'		=> 'admin',
					'time'		=> date('Y-m-d H:i:s')
				    );
                    $db->insert2('product_opra_log',$newdo);
                }
    
            }
            $result['success'] = 1;
            return $result;
        }catch (Exception $e){
            $result['error'] = $e->getMessage();
            throw $e;
            return $result;
        }
    }    


    function getDataChangeLog($newdo,$olddo,$filterFields){
        
        $fields =  array(
            "id"=>"id主键",
            "order_sn"=>"订单号",
            "rec_id"=>"布产id",
            "peishi_status"=>"配石状态",
            "add_time"=>"添加时间",
            "last_time"=>"最后修改时间",
            "add_user"=>"添加人",
            "color"=>"钻石颜色",
            "clarity"=>"钻石净度",
            "shape"=>"钻石形状",
            "cert"=>"证书类型",
            "zhengshuhao"=>"证书号",
            "carat"=>"钻石大小",
            "stone_num"=>"钻石数量",
            "stone_cat"=>"钻石类型",
            "stone_position"=>"石头位置(0:主石 1:副石1 2:副石2 3:副石3)",
            'caigou_time'=>'采购时间记录最新一次采购时间）' ,
            'songshi_time'=>'已送生产部时间(已送生产部的最新一次时间)',
            'peishi_time'=>'配石中时间（操作配石中的最新时间）' ,
            'caigou_user'=>'采购人（操作采购中的人员）' ,
            'songshi_user'=>'送石人（已送生产部操作人员）' ,
            'peishi_user'=>'配石人（配石中操作人员）'             
        );
        $remark = '';
        foreach($newdo as $key=>$vo){
            if(in_array($key,$filterFields)){
                continue;
            }        
            if(isset($olddo[$key]) && $vo != $olddo[$key]){                 
                if(isset($fields[$key]) && count($fields[$key])<20){
                    $field_name = $fields[$key];
                }else{
                    $field_name = $key;
                }                 
                $remark.="[".$field_name."]由【".$olddo[$key]."】改为【".$vo."】,";
            }
        }
        if($remark==''){
            $remark ="";
        }else{
            $remark ="".trim($remark,',')."";
        }
        return $remark;
    }

    /**
     * 自动识别配石状态
     * @param unknown $data
     * @return boolean|number
     * 1.证书号不为空，生成的配石单的状态默认为'不需配石'
     * 2. 证书号为空，根据布产单上的钻石大小、工厂、证书类型去裸石供料类型配置表判断更新，如果匹配到多个值，取优先级高（数字越小越优先）的供料类型：
        1）	如果匹配到的值是厂配钻，生成的配石单的状态默认为“厂配石”
        2）	如果匹配到的值是BDD配钻，生成的配石单的状态默认为“未操作”
        3）	如果匹配不到任何值，生成的配石单的状态默认为'未操作'
     */
    function getNewPeishiStatus($db,$factory_id,$data){
        
        $cert = isset($data['cert'])?$data['cert']:'';
        $carat = isset($data['carat'])?(float)$data['carat']:0;
        $zhengshuhao = isset($data['zhengshuhao'])?$data['zhengshuhao']:""; 
        $color = isset($data['color'])?$data['color']:"";
        $clarity = isset($data['clarity'])?$data['clarity']:"";
        if(!empty($zhengshuhao)){
            return 1;//不需配石
        }
        $sql = "select feed_type from stone_feed_config where is_enable=1 and factory_id={$factory_id} and carat_min<={$carat} and carat_max>={$carat} and (cert='{$cert}' or cert='ALL') and (color='{$color}' or color='ALL') and (clarity='{$clarity}' or clarity='ALL') order by prority_sort asc";
       // echo $sql.';';
        $data = $db->getRow($sql);
        if(!empty($data)){
            $feed_type = $data['feed_type'];//1BDD配钻 2厂配钻
            if($feed_type == 2){
                return 3;//厂配石
            }else if ($feed_type == 1){
                return 0;//未操作
            }
        }
        return 0;//未操作        
    }

   function updatejinzhong($bc_id,$db){
            $sql="select p.bc_sn,s.style_sn,s.check_status,s.style_id,ifnull(sz.value,'') as stone,ifnull(xk.value,'') as xiangkou,ifnull(cz.value,'') as caizhi,ifnull(zq.value,'') as zhiquan,p.qiban_type 
     from kela_supplier.product_info p left join front.base_style_info s on p.style_sn=s.style_sn left join kela_supplier.product_info_attr sz on p.id=sz.g_id and sz.code='cart' left join kela_supplier.product_info_attr xk on p.id=xk.g_id and xk.code='xiangkou' left join kela_supplier.product_info_attr cz on p.id=cz.g_id and cz.code='caizhi' left join kela_supplier.product_info_attr zq on p.id=zq.g_id and zq.code='zhiquan'
     where s.check_status=3 and if(ifnull(sz.value,'')<>'',sz.value,ifnull(xk.value,''))<>'' and ifnull(zq.value,'')<>'' and ifnull(cz.value,'')<>'' and p.id='".$bc_id."'";
            //echo $sql;
            $res=$db->getRow($sql);
            $row=null;
            $jingzhongmax='';
            $jingzhongmin='';
            if($res && in_array(trim($res['caizhi']),array('18K','PT950')) && is_numeric(trim($res['zhiquan']))){
                if($res['stone']<>''  && is_numeric(trim($res['stone'])) && $res['stone']>0){
                    $sql="select * from front.app_xiangkou where round(stone*1-0.05,3) <= ".trim($res['stone'])." and ".trim($res['stone'])."<= round(stone*1+0.04,3) and substring_index(finger,'-',1)*1 <= ".trim($res['zhiquan'])."  and ".trim($res['zhiquan'])." <= substring_index(finger,'-',-1)*1 and style_id='".$res['style_id']."' order by abs(stone-".trim($res['stone']).") limit 1";
                    //echo $sql;
                    $row=$db->getRow($sql);
                    if(empty($row)){
                        $sql="select * from front.app_xiangkou where round(stone*1-0.05,3) <= ".trim($res['stone'])." and ".trim($res['stone'])."<= round(stone*1+0.04,3) and substring_index(finger,'-',1)*1 <= ".round(trim($res['zhiquan']),0)."  and ".round(trim($res['zhiquan']),0)." <= substring_index(finger,'-',-1)*1 and style_id='".$res['style_id']."' order by abs(stone-".trim($res['stone']).") limit 1";
                        //echo $sql;
                        $row=$db->getRow($sql);
                    }
                    $sql="select att_value_name, round(att_value_name*1-0.05,3) as stonemin,round(att_value_name*1+0.04,3) as stonemax  from front.app_attribute_value where attribute_id=1 and att_value_status=1 and  round(att_value_name*1-0.05,3) <= ".trim($res['stone'])." and ".trim($res['stone'])." <= round(att_value_name*1+0.04,3) order by abs(".trim($res['stone'])."-att_value_name*1) limit 1";
                    $rowstone=$db->getRow($sql);
                    if($rowstone)
                        $sql="select max(jinzhong) as lishi_jinzhong_max,min(jinzhong) as lishi_jinzhong_min from warehouse_shipping.warehouse_goods g where g.is_on_sale in (1,2,3,4,5,6,8,10,11) and goods_sn='".trim($res['style_sn'])."' and caizhi like '".trim($res['caizhi'])."%' and  shoucun='".trim($res['zhiquan'])."' and ((zuanshidaxiao >= ".$rowstone['stonemin']." and zuanshidaxiao <= ".$rowstone['stonemax'].")  or (jietuoxiangkou-0.05<= ".trim($res['stone'])." and jietuoxiangkou+0.04 >= ".trim($res['stone'])."))";
                    else
                        $sql="select max(jinzhong) as lishi_jinzhong_max,min(jinzhong) as lishi_jinzhong_min from warehouse_shipping.warehouse_goods g where g.is_on_sale in (1,2,3,4,5,6,8,10,11) and goods_sn='".trim($res['style_sn'])."' and caizhi like '".trim($res['caizhi'])."%' and  shoucun='".trim($res['zhiquan'])."' and jietuoxiangkou-0.05<= ".trim($res['stone'])." and jietuoxiangkou+0.04 >= ".trim($res['stone']);
                    //echo $sql;
                    $row_lishi=$db->getRow($sql);
                }elseif($res['xiangkou']<>'' && is_numeric(trim($res['xiangkou']))){
                    $sql="select * from front.app_xiangkou where stone*1 = '".trim($res['xiangkou'])."'  and substring_index(finger,'-',1)*1 <= ".trim($res['zhiquan'])."  and ".trim($res['zhiquan'])." <= substring_index(finger,'-',-1)*1 and style_id='".$res['style_id']."'";
                    //echo $sql;
                    $row=$db->getRow($sql);
                    if(empty($row)){
                        $sql="select * from front.app_xiangkou where stone*1 = '".trim($res['xiangkou'])."'  and substring_index(finger,'-',1)*1 <= ".round(trim($res['zhiquan']),0)."  and ".round(trim($res['zhiquan']),0)." <= substring_index(finger,'-',-1)*1 and style_id='".$res['style_id']."'";
                        //echo $sql;
                        $row=$db->getRow($sql);
                    }
                    $sql="select max(jinzhong) as lishi_jinzhong_max,min(jinzhong) as lishi_jinzhong_min from warehouse_shipping.warehouse_goods g where g.is_on_sale in (1,2,3,4,5,6,8,10,11) and goods_sn='".trim($res['style_sn'])."' and caizhi like '".trim($res['caizhi'])."%' and  shoucun='".trim($res['zhiquan'])."' and ((zuanshidaxiao>= ".round(trim($res['xiangkou'])-0.05,3) ." and zuanshidaxiao <= ".round(trim($res['xiangkou'])+0.04,3) .") or jietuoxiangkou='".trim($res['xiangkou'])."')" ;
                    //echo $sql;
                    $row_lishi=$db->getRow($sql);
                }
                if($row){
                    if(trim($res['caizhi'])=='18K'){
                        $jingzhongmax=$row['g18_weight']+$row['g18_weight_more'];
                        $jingzhongmin=$row['g18_weight']-$row['g18_weight_more2'];
                    }
                    if(trim($res['caizhi'])=='PT950'){
                        $jingzhongmax=$row['gpt_weight']+$row['gpt_weight_more'];
                        $jingzhongmin=$row['gpt_weight']-$row['gpt_weight_more2'];
                    }
                    $sql="update kela_supplier.product_info set biaozhun_jinzhong_max='".$jingzhongmax."',biaozhun_jinzhong_min='".$jingzhongmin."' where id='".$bc_id."'";
                    //echo $sql;
                    $db->query($sql);
                }else{
                    $sql="update kela_supplier.product_info set biaozhun_jinzhong_max=null,biaozhun_jinzhong_min=null where id='".$bc_id."'";
                    //echo $sql;
                    $db->query($sql);
                }
                if($row_lishi['lishi_jinzhong_max'] && $row_lishi['lishi_jinzhong_min']){
                    $lishi_jinzhong_max=$row_lishi['lishi_jinzhong_max'];
                    $lishi_jinzhong_min=$row_lishi['lishi_jinzhong_min'];
                    $sql="update kela_supplier.product_info set lishi_jinzhong_max='".$lishi_jinzhong_max."',lishi_jinzhong_min='".$lishi_jinzhong_min."' where id='".$bc_id."'";
                    //echo $sql;
                    $db->query($sql);
                }else{
                    $sql="update kela_supplier.product_info set lishi_jinzhong_max=null,lishi_jinzhong_min=null where id='".$bc_id."'";
                    //echo $sql;
                    $db->query($sql);
                }
            }else{
                $sql="update kela_supplier.product_info set biaozhun_jinzhong_max=null,biaozhun_jinzhong_min=null where id='".$bc_id."'";
                //echo $sql;
                $db->query($sql);
                $sql="update kela_supplier.product_info set lishi_jinzhong_max=null,lishi_jinzhong_min=null where id='".$bc_id."'";
                //echo $sql;
                $db->query($sql);
            }

            if($res && in_array($res['qiban_type'], array('0','1'))){
                $sql="select q.jinzhong_min,q.jinzhong_max from kela_supplier.product_info p,kela_supplier.product_goods_rel r,app_order.app_order_details d,purchase.purchase_qiban_goods q 
    where p.id=r.bc_id and r.goods_id=d.id and d.ext_goods_sn=q.addtime and p.id='{$bc_id}'";
                $qiban_res=$db->getRow($sql);
                if(!empty($qiban_res)){
                    $sql="update kela_supplier.product_info set biaozhun_jinzhong_max='".$qiban_res['jinzhong_max']."',biaozhun_jinzhong_min='".$qiban_res['jinzhong_min']."' where id='".$bc_id."'";
                    //echo $sql;
                    $db->query($sql);                   
                } 

            }

   }

?>