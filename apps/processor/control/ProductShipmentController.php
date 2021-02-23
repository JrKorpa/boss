<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductShipmentController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 14:23:07
 *   @update	:
 *  工厂出货
 *  -------------------------------------------------
 */
class ProductShipmentController extends Controller
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('product_shipment','front',13);	//生成模型后请注释该行
		//Util::V('product_shipment',13);	//生成视图后请注释该行
		//$this->render('product_shipment_search_form.html');
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();

		$model = new ProductShipmentModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'product_shipment_search_page';
		$this->render('product_shipment_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ($params)
	{
		$id = intval($params["id"]);//布产单ID
		$result = array('success' => 0,'error' => '','title' => '工厂出货');
		$model = new ProductInfoModel($id,14);
		$status = $model->getValue('status');
		$from_type = $model->getValue('from_type');
		$buchan_fac_opra = $model->getValue('buchan_fac_opra');
		if($status != 4  && $status != 7)//布产单只有生产中和部分出厂两个状态下才能进行出货
		{
			$result['content'] = "布产单状态不对，不允许操作。";
			Util::jsonExit($result);
		}
		if($buchan_fac_opra == 5){
		    //oqc质检未过
		    $result['content'] = "OQC质检未通过，不允许操作。";
		    Util::jsonExit($result);
		}
		
		//剩余数量
		$c_num = $this->getRestNum($id);
		if($c_num == 0)
		{
			$result['content'] = "布产单已全部出货完成";
			Util::jsonExit($result);
		}

		//布产单总数量
		$infoModel = new ProductInfoModel($id,13);
		$bc_num = $infoModel->getValue('num');
		
		/** 获取顶级导航列表**/
		$newmodel = new ProductFqcConfModel(13);
		$top_menu = $newmodel->get_top_menu();

		if($c_num > $bc_num)
		{
			$result['content'] = "布产单剩余出厂数量异常，请联系技术人员。";
			Util::jsonExit($result);
		}

		$result['content'] = $this->fetch('product_shipment_info.html',array(
			//'dd' => new DictView(new DictModel(1)),
			'from_type' => $from_type,
			'top_menu'=>$top_menu,
			'id' => $id
		));

		$y_num=0;
		$shmtModel = new ProductShipmentModel(13);
		$bf_num = $shmtModel->getSumBfNum($id);
		if(!$bf_num){
			$bf_num=0;
		}
		//已出货数量
		$y_num = $bc_num-$c_num-$bf_num;
		//var_dump($bf_num);exit;
		$result['title'] = '工厂出货（布产单数量：'.$bc_num.'&nbsp;&nbsp;剩余出货数量：'.$c_num.'&nbsp;&nbsp;已出货数量：'.$y_num.'）';
		if($from_type==1){
			$result['title'] = '工厂出货（布产单数量：'.$bc_num.'&nbsp;&nbsp;剩余出货数量：'.$c_num.'&nbsp;&nbsp;已出货数量：'.$y_num.'&nbsp;&nbsp;已报废数量：'.$bf_num.'）';
		}

		Util::jsonExit($result);
	}


	/**
	 *	add，渲染添加页面
	 */
	public function to_shipment_pl ($params)
	{

		$ids = _Request::getList("_ids");//$params["_ids"];//布产单ID
		$result = array('success' => 0,'error' => '','title' => '批量出厂');
		
		$model = new ProductInfoModel(14);
		#循环判断 布产单只有生产中和部分出厂两个状态下才能进行OQC质检
		 $is_ok = $model->Isfromtype($ids);
		 $num = count($is_ok);

		if($num>1)
		{
			echo "必须选择同一来源的布产单号进行批量操作！";exit;
		}
		$from_type = $is_ok[0]['from_type'];
	
		foreach($ids as $id){
		    $model = new ProductInfoModel($id,14);
		    $status = $model->getValue('status');
		    $from_type = $model->getValue('from_type');
		    $bc_sn = $model->get_bc_sn($id);
		    $buchan_fac_opra = $model->getValue('buchan_fac_opra');
    	   if($status != 4 && $status != 7)//布产单只有生产中和部分出厂两个状态下才能进行出货
			{
				$result['content'] = "布产单".$bc_sn."状态不对。提示：只有布产状态为【生产中】或【部分出厂】才允许出厂";
				Util::jsonExit($result);
			}
			if($buchan_fac_opra == 5){
			    //oqc质检未过
			    $result['content'] = "布产单".$bc_sn."的OQC质检未通过，不允许出厂。";
			    Util::jsonExit($result);
			}
		    
		}
		
		/** 获取顶级导航列表**/
		$newmodel = new ProductFqcConfModel(13);
		$top_menu = $newmodel->get_top_menu();
		//var_dump($id_s);exit;
/* 		$this->render('product_shipment_info_pl.html',array(
				//'dd' => new DictView(new DictModel(1)),
				'id_s' => join(",",$ids),
				'from_type'=>$from_type,
				'top_menu'=>$top_menu,
				'title'=>'工厂出货'
		));  */
        
		$result['content'] = $this->fetch('product_shipment_info_pl.html',array(
				'id_s' => join(",",$ids),
				'from_type'=>$from_type,
				'top_menu'=>$top_menu,		    
		));
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);//布产ID
		$model = new ProductShipmentModel(13);
        $bcmodel = new ProductInfoModel($id,13);
        $from_type = $bcmodel->getValue('from_type');
		$list = $model->pageList(array('bc_id'=>$id));
		foreach ($list as $k=> $v){
		  $list[$k]['oqc_result']=$v['oqc_result']==1?"质检通过"	:"质检未过";
		  $newmodel = new ProductFqcConfModel($v['oqc_no_type'],13);
		  $list[$k]['oqc_no_type']=$newmodel->getValue('cat_name');
		  $newmodel = new ProductFqcConfModel($v['oqc_no_reason'],13);
		  $list[$k]['oqc_no_reason']=$newmodel->getValue('cat_name');
		}

		$result['title'] = "工厂出货明细";
		$result['content'] = $this->fetch('product_shipment_show.html',array(
			'dd'=>new DictView(new DictModel(1)),
            'from_type' => $from_type,
			'list'=> $list
		));
		Util::jsonExit($result);
	}



    //add  luochuanrong 
    public function  bindInsert($pdo,$table,$params){

        $fields = implode('`,`', array_keys($params));
        $value = str_repeat('?,',count($params)-1).'?';
        $valuedata = array_values($params);
        $sql = "INSERT INTO `".$table."` (`" . $fields . "`) VALUES (". $value .")"; 

        $st=$pdo->prepare($sql);
        foreach ($valuedata as $k => &$p) {                       
                $st->bindValue($k + 1, $p, is_int($p) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }

        $res=$st->execute();
        //echo $sql;
        return $res; 
    }

    /**
     * 重新编写布产出厂函数 将API方式改写成数据库事务 luochuanrong
     * 
    */
    public function insert($params)
    {
		$id = intval($params["id"]);//布产单ID
        //		$shipment_number= $params['shipment_number'];
		$num		=isset($params['num'])?intval($params['num']):0 ;
		$info		= $params['info'];
		$oqc_result		= intval($params['oqc_result']);
		$reason_scrapped =isset($params['reason_scrapped'])?$params['reason_scrapped']:'' ;
		$oqc_no_num	=isset($params['oqc_no_num'])?intval($params['oqc_no_num']):0 ;
		$oqc_no_type =isset($params['oqc_no_type'])?intval($params['oqc_no_type']):'' ;
		$oqc_no_reason =isset($params['oqc_no_reason'])?intval($params['oqc_no_reason']):'' ;
		$bf_num =isset($params['bf_num'])?intval($params['bf_num']):0;
        if($id<=0){
        	$result['error'] = "参数错误，不允许操作。";
			Util::jsonExit($result);
        }
		

		
        $model14=DB::cn(14)->db();
        try{
	        $model14->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
	        $model14->beginTransaction(); //开启事务
	        $sql="select * from product_info where id='$id' for update";
	        //$res=$model22->query($sql);
            $bc_num=0;
            $from_type=0;
            $res=$model14->query($sql);
            $row=$res->fetch(PDO::FETCH_ASSOC);
            if($row==false){
            	    $model14->rollback();//事务回滚
			        $model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交                  	
					$result['error'] = "布产单不存在!";
					Util::jsonExit($result);   
            }
            //var_dump($row);
            $bc_num=$row['num'];
            $from_type=$row['from_type'];
            $status=$row['status'];          
            $time = date("Y-m-d H:i:s");
            $purchase_id=0;
            $order_sn=0;
			if($status != 4 && $status != 7)//布产单只有生产中和部分出厂两个状态下才能进行出货
			{
                $model14->rollback();//事务回滚
			    $model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交				
				$result['error'] = "布产单只有生产中和部分出厂两个状态下才能进行出货";
				Util::jsonExit($result);
			}  
			
			
			//质检未过时，2015-11-4 boss571
			if($oqc_result==0 ){
				//出货数量不能输出
				if($num>0){
					$model14->rollback();//事务回滚
					$model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					$result['error'] = "质检未过时，出货数量不能输入";
					Util::jsonExit($result);
				}
				//质检未过类型必填
				if($oqc_no_type==''){
					$model14->rollback();//事务回滚
					$model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					$result['error'] = "质检未过时，质检未过类型必填";
					Util::jsonExit($result);
				}
				
				//质检未过原因必填
				if($oqc_no_reason==''){
					$model14->rollback();//事务回滚
					$model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					$result['error'] = "质检未过时，质检未过原因必填";
					Util::jsonExit($result);
				}
				
			}
			//订单,质检通过 出厂数量必须大于0
			if($from_type==2){
				if($num<=0 && $oqc_result==1)
				{
	                $model14->rollback();//事务回滚
				    $model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交					
					$result['error'] = "出货数量不允许为空且要大于0";
					Util::jsonExit($result);
				}
				$order_sn=$row['p_sn'];
			}
			//若是 采购布产单  报废数量和出货数量必须有一个大于0
			 if($from_type==1){
			 	//2015-11-4 boss571
				if($oqc_result==1 && $num <= 0 && $bf_num <= 0)
				{
	                $model14->rollback();//事务回滚
				    $model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交					
					$result['error'] = "质检通过时，出货数量+报废数量要大于0";
					Util::jsonExit($result);
				}
				
				//2015-11-4 boss571
				if($oqc_result==0 && $bf_num <= 0 && $oqc_no_num <=0){
					$model14->rollback();//事务回滚
					$model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					$result['error'] = "质检未通过，报废数量和质检未过数量必须有一个大于0";
					Util::jsonExit($result);
				}
				
				//报废数大于0，报废原因必填 2015-11-4 boss571
				if($bf_num > 0 && $reason_scrapped==''){
					$model14->rollback();//事务回滚
					$model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					$result['error'] = "报废数大于0，报废原因必填";
					Util::jsonExit($result);
				}
				$purchase_id=substr($row['p_sn'],3);
			}			         
            
			


            //出库数量+报废数量 =总数量
            $other_num=0;
            $sql="select sum(num) as num,sum(bf_num) as bf_num,sum(oqc_no_num) as oqc_no_num from product_shipment where bc_id='$id'";
            foreach ($model14->query($sql) as $row2){
                if( $num+$bf_num+$oqc_no_num > $bc_num-$row2['num']-$row2['bf_num']-$row2['oqc_no_num'] ){
            	    $model14->rollback();//事务回滚
			        $model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交                  	
					$result['error'] = "出厂数量/报废数量/质检未过数量和$num + $bf_num + $oqc_no_num 超过未出厂数量". ($bc_num-$row2['num']-$row2['bf_num']) .",不允许操作。";
					Util::jsonExit($result);       	
                }
                $other_num=$bc_num-$row2['num']-$row2['bf_num'];                
            } 
            $other_num= $other_num-$num-$bf_num;
        
            
			$newdo=array(
				"bc_id"			=> $id,
				"shipment_number"=> '',
				"num"			=> $num,
				"bf_num"		=> $bf_num,
				"reason_scrapped"	=> $reason_scrapped,
				"oqc_result"		=> $oqc_result,
				"oqc_no_num"		=> $oqc_no_num,
				"oqc_no_type"		=> $oqc_no_type,
				"oqc_no_reason"		=> $oqc_no_reason,
				"info"			=> $info,
				"opra_uid"		=> $_SESSION['userId'],
				"opra_uname"	=> $_SESSION['userName'],
				"opra_time"		=> $time
			);
		  
            $res=$this->bindInsert($model14,"product_shipment",$newdo);
            if($res===false){
            	$model14->rollback();//事务回滚
			    $model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交              	
				$result['error'] = "加入出厂数量操作失败!";
				Util::jsonExit($result);            	
            }
             
         
			if($other_num == 0)
			{
				//
				// 工厂出厂
				// 变更布产状态
				// 当全部都出厂后，变更布产状态->已出场/ 同时 生产状态也变更未已出场
				//				
				$status = 9;				
                $sql="update product_info set status='9',buchan_fac_opra='6',rece_time='$time',edit_time='$time' where id='$id' and status not in (8,9,10,11) ";
                $count=$model14->exec($sql);
                if($count<>1){
                	$model14->rollback();//事务回滚
                	$model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                	$result['error'] = "更新布产单状态失败! 可能布产单已经取消 报废 已出厂或者不需布产";
                	Util::jsonExit($result);
                }
			}else if($other_num > 0 && $other_num < $bc_num){
				$status = 7;
				$sql="update product_info set status='7',edit_time='$time' where id='$id' and status not in (8,9,10,11)";				
				$count=$model14->exec($sql);
				if($count<>1){
					$model14->rollback();//事务回滚
					$model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					$result['error'] = "更新布产单状态失败! 可能布产单已经取消 报废 已出厂或者不需布产";
					Util::jsonExit($result);
				}
			}

			
 
            $model28=DB::cn(28)->db();
	        $model28->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
	        $model28->beginTransaction();  
	
	        //通过布产单找到对应订单明细ID          
            $sql = "SELECT  pg.`bc_id`, pg.`goods_id`,p.`status` FROM `product_goods_rel` as pg left join `product_info` as p  on pg.`bc_id`=p.`id` WHERE p.`id` = {$id}";
            $res=$model14->query($sql);            
            $rowrel=$res->fetch(PDO::FETCH_ASSOC);
    

            //通过$rowrel['goods_id'] 查找订单明细
            if($from_type==2 && $rowrel!=false){
	            $sql="select d.buchan_status,d.order_id,o.order_pay_status,o.send_good_status,o.order_status,o.referer from app_order_details d inner join base_order_info o on d.order_id=o.id  where d.id='".$rowrel['goods_id']."'"; 
	            //echo $sql;             
	            $res2=$model28->query($sql);             
	            $row2=$res2->fetch(PDO::FETCH_ASSOC);
            }  
            $orderLog=array(); 

            $remark="";
            $peihuo="";
            if($rowrel!=false && $from_type==2 && $row2!=false){
            	/*
            	  if($oqc_result==1){ 
                    $sql="update app_order_details set buchan_status='$status' where id='".$rowrel['goods_id']."'";
                    //echo$sql;
                    $count=$model28->exec($sql);
                    if($count<>1){
		            	$model14->rollback();//事务回滚 
					    $model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		            	$model28->rollback();//事务回滚
					    $model28->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交                    	
                    	$result['error'] = "更新订单明细货品布产状态失败!";
					    Util::jsonExit($result);	
                    } 
                    
            	  }*/
                    $order_id=$row2['order_id'];
                    $order_pay_status=$row2['order_pay_status'];
                    $orderLog=array(
                           'order_id'=>$row2['order_id'],
                           'order_status'=>$row2['order_status'],
                           'shipping_status'=>$row2['send_good_status'],
                           'pay_status'=>$row2['order_pay_status'],
                           'create_user'=>$_SESSION['userName'],
                           'create_time'=>date('Y-m-d H:i:s'),
                           'remark'=>''
                    );	
                    /*
                    if($row2['referer'] == "天生一对加盟商"){
                        //天生一对加盟商的订单,，出厂后判断是否改变订单明细的配货状态
                        if(in_array($order_pay_status,array(2,3,4))){
                            $sql="update app_order_details set delivery_status=2 where id='".$rowrel['goods_id']."'";
                            $res = $model28->query($sql);
                            if(!$res){
                                $model14->rollback();//事务回滚
                                $model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                                $model28->rollback();//事务回滚
                                $model28->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                                $result['error'] = "更新订单明细配货状态失败!";
                                Util::jsonExit($result);
                            }
                        }
                    }
                    */
                    if($order_id>0){                    	
                    	//查找订单未完成出厂的货号
                        $sql="select count(id) as unbcrows from app_order_details where order_id='$order_id' and buchan_status<>9 and buchan_status<>11 and is_stock_goods=0 and is_return=0 ";
                        $res3=$model28->query($sql);
                        $ress=$res3->fetch(PDO::FETCH_ASSOC);                        
                        
                   
                  /*
                        //检查订单下的所有布产单是否完成 完成则更改订单的配货状态和出产状态
                        if($ress['unbcrows']==0) {  //订单货品全部出厂完成 更改订单布产状态 配货状态                        	    
                                $tsyd_pay_in_status = $row2['referer'] == "天生一对加盟商" && in_array($order_pay_status,array(2,3,4));                   	    
                                if($order_pay_status==3 || $order_pay_status==4 || $tsyd_pay_in_status){
                                    $sql="update base_order_info set delivery_status=2,buchan_status=4 where id='$order_id'";
                        	        $peihuo="允许配货.";
                        	    }else{
                        	    	$sql="update base_order_info set buchan_status=4 where id='$order_id'"; 
                        	    } 
                        	    $result28=$model28->query($sql); 
                        	    //$count=$model28->exec($sql);
                        	    if($result28===false){
					            	$model14->rollback();//事务回滚
								    $model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					            	$model28->rollback();//事务回滚
								    $model28->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交                        	    	
                        	        $result['error'] = "更新订单布产状态/配货状态失败!";
				                    Util::jsonExit($result); 
                        	    } 
                        	    $remark=" 订单: $order_sn 全部出厂完成.";

                        }
                        */
                                              
                    }          
            }
            //更新实际等钻结束时间
            $productmodel = new ProductInfoModel($id,14);
		 	$row7=$productmodel->getBuChanInfoById($id);
            $from_type = $row7['from_type'];
            $now =date('Y-m-d',time());
			//等钻操作后，操作非等钻后等钻完成时间和开始生产的出厂时间比对，大于更新出厂时间(未超期)
            
			//if($row7['wait_dia_endtime'] =='0000-00-00 00:00:00' && $row7['wait_dia_starttime'] !='0000-00-00 00:00:00' && $now <=$row7['esmt_time']){
				$order_type = $from_type==1?2:1;
				$res2 = $this->updateEsmttimeByBc_Id($id,$order_type);
				if($res2 === false){
					$model14->rollback();//事务回滚
				    $model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	            	$model28->rollback();//事务回滚
				    $model28->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交                        	    	
        	        $result['error'] = "更新标准出厂时间失败";
                    Util::jsonExit($result); 
				}
			

			if($row7['wait_dia_endtime'] =='0000-00-00 00:00:00' && $row7['wait_dia_starttime'] !='0000-00-00 00:00:00'){
				$wait_dia_endtime = date('Y-m-d H:i:s',time());
				$res1=$productmodel->updateWait_dia_endtimeById($id,$wait_dia_endtime);
				if($res1===false){
					$model14->rollback();//事务回滚
				    $model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	            	$model28->rollback();//事务回滚
        	        $result['error'] = "更新实际等钻结束时间失败!";
                    Util::jsonExit($result); 
				}
			}


			//更新OQC质检通过时间
			$c_num2 = $this->getRestNum($id);			//剩余出货量
			$total1 = $num + $bf_num;					//已出货和已报废的
			if($from_type ==1 && $total1 == $c_num2){
				//采购单全部出厂更新OQC质检通过时间
				$oqc_pass_time = date('Y-m-d H:i:s',time());
				$res3=$productmodel->updateOqc_pass_timeById($id,$oqc_pass_time);
		    	if($res3===false){
		            	$model14->rollback();//事务回滚
					    $model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		            	$model28->rollback();//事务回滚
					    $model28->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交                        	    	
	        	        $result['error'] = "更新OQC质检通过时间失败!";
	                    Util::jsonExit($result); 
            	    } 
			}
           
            $log=array(
            	'bc_id'=>$id,
            	'status'=>$status,
            	'time'=>$time,
            	'uname'=>$_SESSION['userName'],
            	'uid'=>$_SESSION['userId'],
            	'remark'=>"布产单工厂出货，出货数量：".$num."，报废数量：".$bf_num."，质检未通过数：".$oqc_no_num."，备注：".$info .$remark
            );
            $res=$this->bindInsert($model14,"product_opra_log",$log);            
            if($res==false){
            	$model14->rollback();//事务回滚
			    $model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交			   
	            $model28->rollback();//事务回滚
				$model28->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交				 			    
				$result['error'] = "加入工厂出厂明细日志操作失败!";
				Util::jsonExit($result);            	
            }

            $bc_sn = $productmodel->get_bc_sn($id);
            $orderLog['remark']="布产单".$bc_sn."工厂出货，出货数量：".$num."，报废数量：".$bf_num."，质检未通过数：".$oqc_no_num."，备注：".$info .$remark.$peihuo;
            $res=$this->bindInsert($model28,"app_order_action",$orderLog);
            if($res==false){
            	$model14->rollback();//事务回滚
			    $model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交			   
	            $model28->rollback();//事务回滚
				$model28->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交				 			    
				$result['error'] = "加入订单日志操作失败!";
				Util::jsonExit($result);            	
            }
            
	 		$model14->commit();//如果没有异常，就提交事务
			$model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	 		$model28->commit();//如果没有异常，就提交事务
			$model28->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交	 
			$result['success'] = 1;  
			Util::jsonExit($result);             

        }catch (Exception $e) {              
            	$model14->rollback();//事务回滚
			    $model14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			    if(isset($model28) && !empty($model28)){
	            	$model28->rollback();//事务回滚
				    $model28->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				} 
				
				$result['error'] = "程序异常错误,出厂失败!";
				Util::jsonExit($result);							
        } 
                  

    }


	/**
	 *	insert，信息入库
	 */
	//旧程序 可以废除 
	public function insert_old ($params)
	{
		$id = intval($params["id"]);//布产单ID
//		$shipment_number= $params['shipment_number'];
		$num		= intval($params['num']);
		$info		= $params['info'];
		$bf_num     =isset($params['bf_num'])?$params['bf_num']:0;

		//出库数量+报废数量 =总数量
		$model = new ProductInfoModel($id,14);
		$status = $model->getValue('status');
		$from_type = $model->getValue('from_type');
		if($from_type==2){
			if($num <= 0 )
			{
				$result['error'] = "出货数量必须大于0";
				Util::jsonExit($result);
			}
		}
		//若是 采购布产单  报废数量和出货数量必须有一个大于0
		 if($from_type==1){
			if($num <= 0 && $bf_num <= 0)
			{
				$result['error'] = "出货数量和报废数量必须有一个大于0";
				Util::jsonExit($result);
			}
		}

		if($status != 4 && $status != 7)//布产单只有生产中和部分出厂两个状态下才能进行出货
		{
			$result['error'] = "布产单状态不对，不允许操作。";
			Util::jsonExit($result);
		}

		$spmodel = new ProductShipmentModel(14);
		/*if($spmodel->getExistNumberOfBcid($id,$shipment_number))
		{
			$result['error'] = "此布产单已经存在此出货单号";
			Util::jsonExit($result);
		}*/
		$c_num = $this->getRestNum($id);

		//出货数量 不能大于  剩余出货数量
		if($num+$bf_num > $c_num)
		{
			$result['error'] = "此布产单还差".$c_num."个没出货，输入数量超过实际需要出货数量";
			Util::jsonExit($result);
		}
		$time = date("Y-m-d H:i:s");
		$olddo = array();
		$newdo=array(
			"bc_id"			=> $id,
			"shipment_number"=> '',
			"num"			=> $num,
			"bf_num"		=> $bf_num,
			"info"			=> $info,
			"opra_uid"		=> $_SESSION['userId'],
			"opra_uname"	=> $_SESSION['userName'],
			"opra_time"		=> $time
		);
		$res = $spmodel->saveData($newdo,$olddo);

		if($res !== false){

			$c_num = $this->getRestNum($id);
			if($c_num == 0)
			{
				/**
				* 工厂出厂
				* 变更布产状态
				* 当全部都出厂后，变更布产状态->已出场/ 同时 生产状态也变更未已出场
				*/
				$status = 9;
				$model->setValue('status',9);				//布产单状态  数字字典 buchan_status
				$model->setValue('buchan_fac_opra',6);		//布产单生产状态 数字字典 buchan_fac_opra
				$model->setValue('rece_time',$time);

			}else{
				$status = 7;
				$model->setValue('status',7);				//布产单状态  数字字典 buchan_status
			}
			$model->setValue('edit_time',date('Y-m-d H:i:s'));
			$model->save();

			//判断是布产单是否有关联货品 若关联 更新布产操作状态到 货品详情表 BY linian
			$rec = $model->judgeBcGoodsRel($id);
			if(!empty($rec)){
				$keys =array('update_data');
				$vals =array(array(array('id'=>$rec['goods_id'],'buchan_status'=>$status)));
				$ret = ApiModel::sales_api($keys, $vals, 'UpdateOrderDetailStatus');
			}


			//记录操作日志
			$logModel = new ProductOpraLogModel(14);
			$dd = new DictModel(1);
			$info = $info?$info:'无';
			if($from_type==1){
				//$logModel->addLog($id,$model->getValue('status'),"工厂出货，出货数量：".$num."，报废数量：".$bf_num."，备注：".$info);
				$logModel->addLog($id,"布产单工厂出货，出货数量：".$num."，报废数量：".$bf_num."，备注：".$info);
				//$model->Writeback($id, "工厂出货，数量：".$num."，备注：".$info);	//回写订单操作日志 BY hulichao
			}else{
				//$logModel->addLog($id,$model->getValue('status'),"工厂出货，出货数量：".$num."，备注：".$info);
				$logModel->addLog($id,"布产单工厂出货，出货数量：".$num."，备注：".$info);
				//$model->Writeback($id, "工厂出货，数量：".$num."，备注：".$info);	//回写订单操作日志 BY hulichao
			}

			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}

	//剩余出货数量
	public function getRestNum ($bc_id)
	{
		//布产单总数量
		$infoModel = new ProductInfoModel($bc_id,13);
		$bc_num = $infoModel->getValue('num');

		//布产单总出货数量
		$shmtModel = new ProductShipmentModel(13);
		$shmt_num = $shmtModel->getSumNum($bc_id);

		//布产单报废数量
		$shmtModel = new ProductShipmentModel(13);
		$shmt_bfnum = $shmtModel->getSumBfNum($bc_id);

		//还需出厂的数量 = 布产单总数量 - 总出货数量
		$c_num = $bc_num - $shmt_num-$shmt_bfnum;
		return $c_num;
	}



	/**
	 *	insert，批量工厂出货
	 */
	public function bath_insert ($params)
	{
		//var_dump($_REQUEST);exit;
		$ids = _Request::get('id');
		$from_type = _Request::get('from_type');
		$ids_arr = explode(',', $ids);
		$num= _Request::get('num',0);
		//判断批量布产剩余出厂数量是否全部大于 要出场数量
		foreach($ids_arr as $id){
		    $model = new ProductInfoModel($id,14);
		    $status = $model->getValue('status');
		    $from_type = $model->getValue('from_type');
		    $buchan_fac_opra = $model->getValue('buchan_fac_opra');
		    $bc_sn = $model->get_bc_sn($id);
			//每个布产剩余出厂数量
			$c_num =$this->getRestNum($id);
			//要批量出厂数量
			if($from_type==2){
				if($num>$c_num){
					$result['error'] = "布产单".$bc_sn."：出货数量高于剩余出货数量，不可以操作！";
					Util::jsonExit($result);
				}
			}
			if($buchan_fac_opra == 5){
			    //oqc质检未过
			    $result['content'] = "OQC质检未通过，不允许操作。";
			    Util::jsonExit($result);
			} 
			if($from_type==1){
				$bf_num=  _Request::get('bf_num');
				if($num+$bf_num>$c_num){
					$result['error'] = "布产单".$bc_sn."：出货数量和报废数量之和高于剩余出货数量，不可以操作！";
					Util::jsonExit($result);
				}
			}



		}
		
		
		
		$num		= isset($params['num'])?intval($params['num']):0;
		$info		= $params['info'];
		$bf_num     =isset($params['bf_num'])?intval($params['bf_num']):0;
		$oqc_result		= intval($params['oqc_result']);
		$reason_scrapped =isset($params['reason_scrapped'])?$params['reason_scrapped']:'' ;
		$oqc_no_num	=isset($params['oqc_no_num'])?intval($params['oqc_no_num']):0 ;
		$oqc_no_type =isset($params['oqc_no_type'])?intval($params['oqc_no_type']):'' ;
		$oqc_no_reason =isset($params['oqc_no_reason'])?intval($params['oqc_no_reason']):'' ;
		
		if($bf_num>0 && $reason_scrapped==''){
			$result['error'] = "报废数量大于0时，报废原因必填 ";
			Util::jsonExit($result);
		}
		if($bf_num == 0 && $reason_scrapped !=''){
			$result['error'] = "报废数量等于0时，报废原因不需要填 ";
			Util::jsonExit($result);
		}
		if($oqc_result==0 ){
			//出货数量不能输出
			if($num>0){
				$result['error'] = "质检未过时，出货数量不能输出";
				Util::jsonExit($result);
			}
			//质检未过类型必填
			if($oqc_no_type==''){
				$result['error'] = "质检未过时，质检未过类型必填";
				Util::jsonExit($result);
			}
		
			//质检未过原因必填
			if($oqc_no_reason==''){
				$result['error'] = "质检未过时，质检未过原因必填";
				Util::jsonExit($result);
			}
		
		}
		$logModel = new ProductOpraLogModel(13);
		$spmodel = new ProductShipmentModel(13);
		$salesModel = new SalesModel(27);
		
		$pdolist[14] = $spmodel->db()->db();
		$pdolist[27] = $salesModel->db()->db();
		try{
    		foreach ($pdolist as $pdo){
        		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
        		$pdo->beginTransaction();//开启事务
    		}
		}catch (PDOException $e){
		    $msg = "批量事物开启失败!".$e->getMessage();
		    Util::rollbackExit($msg);
		}
try{
		foreach($ids_arr as $id){
			//出库数量+报废数量 =总数量
			$model = new ProductInfoModel($id,13);
			$bc_num=$model->getValue('num');
			$status = $model->getValue('status');
			$from_type = $model->getValue('from_type');
			if($from_type==2){
				if($num <= 0 && $oqc_result==1)
				{
					$result['error'] = "出货数量必须大于0";
					Util::jsonExit($result);
				}
			}
			//若是 采购布产单  报废数量和出货数量必须有一个大于0
			if($from_type==1){
				if($num <= 0 && $bf_num <= 0 && $oqc_result==1)
				{
					$result['error'] = "出货数量和报废数量必须有一个大于0";
					Util::jsonExit($result);
				}
				
				if($oqc_no_num <= 0 && $bf_num <= 0 && $oqc_result==0)
				{
					$result['error'] = "质检未通过数和报废数量必须有一个大于0";
					Util::jsonExit($result);
				}
				
				
			}

			if($status != 4 && $status != 7)//布产单只有生产中和部分出厂两个状态下才能进行出货
			{
				$result['error'] = "布产单状态不对，不允许操作。提示：只有布产状态为【生产中】或【部分出厂】才允许出厂";
				Util::jsonExit($result);
			}			

			$c_num = $this->getRestNum($id);

			//出货数量 不能大于  剩余出货数量
			if($num+$bf_num+$oqc_no_num > $c_num)
			{
				$result['error'] = "此布产单{$id}还差".$c_num."个没出货，输入数量超过实际需要出货数量";
				Util::jsonExit($result);
			}
			$time = date("Y-m-d H:i:s");
			$olddo = array();
			$newdo=array(
					"bc_id"			=> $id,
					"shipment_number"=> '',
					"num"			=> $num,
					"bf_num"		=> $bf_num,
					"info"			=> $info,
					"reason_scrapped"	=> $reason_scrapped,
					"oqc_result"		=> $oqc_result,
					"oqc_no_num"		=> $oqc_no_num,
					"oqc_no_type"		=> $oqc_no_type,
					"oqc_no_reason"		=> $oqc_no_reason,
					"opra_uid"		=> $_SESSION['userId'],
					"opra_uname"	=> $_SESSION['userName'],
					"opra_time"		=> $time
			);
			$res = $spmodel->saveData($newdo,$olddo);

			if($res !== false){

				$c_num = $this->getRestNum($id);
				if($c_num == 0)
				{
					/**
					 * 工厂出厂
					 * 变更布产状态
					 * 当全部都出厂后，变更布产状态->已出场/ 同时 生产状态也变更未已出场
					 */
					$status = 9;
					$model->setValue('status',9);				//布产单状态  数字字典 buchan_status
					$model->setValue('buchan_fac_opra',6);		//布产单生产状态 数字字典 buchan_fac_opra
					$model->setValue('rece_time',$time);

				}elseif($c_num > 0 && $c_num < $bc_num){
					$status = 7;
					$model->setValue('status',7);				//布产单状态  数字字典 buchan_status
				}
				$model->setValue('edit_time',date('Y-m-d H:i:s'));
				$model->save();
				//订单明细回写
                if($from_type==2){
                    $order_sn = $model->getValue("p_sn");
                    $order_detail_id = $model->getValue("p_id");
                    
				    $order_info = $salesModel->getBaseOrderInfoByOrderSn("id,referer,order_pay_status",$order_sn);
				    if(!empty($order_info)){
				        $order_pay_status = $order_info['order_pay_status'];				        
    				    $data = array('buchan_status'=>$status);
    					if($order_info['referer']=="天生一对加盟商"){    					     
    					     if(in_array($order_pay_status,array(2,3,4))){   					         
    					         $data['delivery_status']=2;//天生一对供应商，订单商品允许配货
    					     }
    					}
    					
    					$ress=$salesModel->updateOrderDetail($data,"id={$order_detail_id}");
    		            if($ress){
    		            	$salesModel->updateOrderInfoStatusByOrderSn($order_sn);
    		            }
                        
				    }
				}

				//记录操作日志
				$info = $info?$info:'无';
				if($from_type==1){
					//$logModel->addLog($id,$model->getValue('status'),"工厂出货，出货数量：".$num."，报废数量：".$bf_num."，备注：".$info);
					$logModel->addLog($id,"布产单工厂出货，出货数量：".$num."，报废数量：".$bf_num."，质检未通过数：".$oqc_no_num."，备注：".$info);
					$model->Writeback($id, "工厂出货，数量：".$num."，备注：".$info);	//回写订单操作日志 BY hulichao
				}else{
					//$logModel->addLog($id,$model->getValue('status'),"工厂出货，出货数量：".$num."，备注：".$info);
					$logModel->addLog($id,"布产单工厂出货，出货数量：".$num."，质检未通过数：".$oqc_no_num."，备注：".$info);

					$model->Writeback($id, "工厂出货，数量：".$num."，备注：".$info);	//回写订单操作日志 BY hulichao
				}

				$result['success'] = 1;
			}else{
				 $msg = "操作失败:Exception:".$e->getMessage();
                 Util::rollbackExit($msg,$pdolist);
			}
		}
}catch (PDOException $e){
    $msg = "操作失败:PDOException:".$e->getMessage();
    Util::rollbackExit($msg,$pdolist);
}catch (Exception $e){
    $msg = "操作失败:Exception:".$e->getMessage();
    Util::rollbackExit($msg,$pdolist);
}    

		try{
		    foreach ($pdolist as $pdo){		        
		        $pdo->commit();
		        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		    }
		}catch (PDOException $e){
		    $msg = "批量事物提交失败!".$e->getMessage();
		    Util::rollbackExit($msg,$pdolist);
		}
		Util::jsonExit($result);
	}

	/*
	*更加供应商ID更新出厂时间(送钻时间比较)
	*
	*/
	public function updateEsmttimeByBc_Id($id,$order_type=1){

		$newmodel = new AppProcessorWorktimeModel(14);
		$productModel = new ProductInfoModel($id,14);
		$stylemodel = new StyleModel(11);
		$purchasemodel = new PurchaseModel(23);
		$proInfos = $productModel->getBuChanInfoById($id);
		$infos = $newmodel->getProcessorInfoByProId($proInfos['prc_id'],$order_type);
		$behind_wait_dia = !empty($infos['behind_wait_dia'])?$infos['behind_wait_dia']:0;
			//更新出厂时间:未出厂 && 出厂时间大于当前时间
				$qiban_exists = $purchasemodel->getQiBanInfosByStyle_Sn($proInfos['style_sn'],$proInfos['p_sn']);
				if($order_type ==1){
					//客订单
					if($proInfos['style_sn'] =='QIBAN' && $qiban_exists){
					//无款起版
						$cycle = $infos['wkqbzq'];
					
					}else{
						//成品:款式库存在,起版列表没有
						if($proInfos['style_sn'] !='QIBAN'){
							//起版列表信息
							$qiban_exists = $purchasemodel->getQiBanInfosByStyle_Sn($proInfos['style_sn'],$proInfos['p_sn']);
							if(empty($qiban_exists)){
								//成品(更新)
								$cycle = $infos['normal_day'];
							}else{
								//有款起版(更新)
								$cycle = $infos['ykqbzq'];
							}
						}	
					}
				}else{
					//备货单
					$is_style = $purchasemodel->getStyleInfoByCgd($proInfos['p_sn']);
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
						
						if(!empty($behind_wait_dia)){
						for($i=1;$i<=$behind_wait_dia;$i++){
							$new_day = date('Y-m-d',strtotime('+'.$i.' day',time()));
								//放假日期
							if(strpos($infos['holiday_time'],$new_day) !== false){
									++$behind_wait_dia;
									continue;
								}
								//暂时只能获得周末休息天数(默认周天休息)
								switch ($infos['is_rest']) {
									case '1':
										break;
									case '2':
										if(date('w',strtotime($new_day))== 0){
											$behind_wait_dia = $behind_wait_dia+1;
										}
										break;
									default:
										if(date('w',strtotime($new_day))== 6 || date('w',strtotime($new_day))== 0){
											$behind_wait_dia = $behind_wait_dia+1;
										}
										break;
								}											
							//周末上班
							if(strpos($infos['is_work'],$new_day) !== false && strpos('60',date('w',strtotime($new_day))) !== false){
									--$behind_wait_dia;				
								}
							}
						}

						$now = date('Y-m-d',time());
						if(isset($day)){
                        //if(isset($day) && $proInfos['esmt_time'] >=$now){
							$esmt_time = max($new_day,$day);
							$res = $productModel->updateEsmttime($id,$esmt_time,1);
							if($res===false){
								return false;
							}
						}
						
						return true;
		}



	
}

?>