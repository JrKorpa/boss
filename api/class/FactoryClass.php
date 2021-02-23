<?php
/**
 *  -------------------------------------------------
 *   @file		: FactoryClass.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		: 2015/4/21
 *   @update	:
 *  -------------------------------------------------
 */
 include_once '../frame/init.php';
 include_once 'class/ErrorClass.php';
 include_once 'model/ProductInfoModel.class.php';
 class FactoryClass
 {
	 protected $error;
	 protected $conf;
	 protected $productInfoRow;
	 protected $productM;
	 protected $dd;
	 protected $res;

	 function __construct()
	 {
		$error=new Error();
		$this->error=$error;
		$this->dd = new DictModel(1);
		$this->res=new stdClass();

	 }
	 function auth()
	 {
		if(empty($this->conf))
		{
			return $this->error->getErrorMessageByNO(0);
		}
		if($this->conf['sign']!==md5($this->conf['bc_id'].$this->conf['status'].$this->conf['bc_id'].$this->conf['fid']))
		{
			return $this->error->getErrorMessageByNO(1);
		}
		return $this->error->getErrorMessageByNO(200);
	 }
	 //工厂同步布产状态的接口
	 function changeStatus($conf)
	 {
		 $result=array('error_no'=>'404','msg'=>'未知错误');
		 $data=$conf;
		 $conf=array();
		 foreach($data as $key=>$v)
		 {
			$conf[$key]=$v;
		 }
		$this->conf=$conf;
		$result=$this->auth();
		if($result['error_no']==200)
		{
			$this->ensureProductM();
			//根据布产号查询该布产号对应的工厂
			$row=$this->productM->getProductInfo($this->conf['bc_id']);
			$prc_ids=array(452,416,5);
			$prc_id_bak=$row['prc_id'];
			if($row['prc_id']==416)
			{
				$prc_id_bak=452;//416和452为一家工厂
			}
			if(!empty($row) and in_array($this->conf['fid'],$prc_ids) and $prc_id_bak==$this->conf['fid'])
			{
				$status_list=$this->productM->getFactoryOpraList();
				$this->productInfoRow=$row;
				//工厂传过来状态是否在系统工厂操作状态列表中
				if(in_array($this->conf['status'],$status_list))
				{
					//生产中和部分生产的并且生产状态来中质检通过的
					if(in_array($row['status'],array(4,7)) and $row['buchan_fac_opra']!==4)
					{
						$this->conf['id']=$row['id'];
						$result=$this->change();
					}
					else
					{
						$result=$this->error->getErrorMessageByNO(4);
					}
				}
				else
				{
					$result=$this->error->getErrorMessageByNO(3);
				}
			}
			else
			{
				$result=$this->error->getErrorMessageByNO(2);
			}
		}
		$this->res->error_no=$result['error_no'];
		$this->res->msg=$result['msg'];
		return $this->res;
	 }
	 function addOpraLog()
	 {
		 $this->ensureProductM();
		 $opraArr=array(
				"bc_id"	=>$this->conf['id'],
				"opra_action"	=> $this->conf['status'],
				"opra_uid"		=> 0,
				"opra_uname"	=> '第三方',
				"opra_time"		=> date("Y-m-d H:i:s"),
				"opra_info"		=>'第三方接口'
		);
		$sql="insert into product_factory_opra (".implode(',',array_keys($opraArr)).") values ('".implode("','",array_values($opraArr))."')";
		return $this->productM->db()->query($sql);
	 }

	 function saveProductInfo($arr)
	 {
		 $this->ensureProductM();
		 $sql="update product_info set";
		 foreach($arr as $k=>$v)
		 {
		   $sql.=" $k='{$v}' ,";
		 }
		 $sql=rtrim($sql,',');
		 $sql.="where id={$this->conf['id']}";
		return $res=$this->productM->db()->query($sql);
	 }
	 function change()
	 {
		 $this->ensureProductM();
		$id=$this->conf['id'];
		$pdo = $this->productM->db()->db();
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
		$pdo->beginTransaction();//开启事务
		//取得当前布产单的工厂操作最新操作状态
		$action_status =$this->productM->getFactioryOpraStatusById($id);
		//重复操作禁止
		if($this->conf['status']==$action_status){
			return $this->error->getErrorMessageByNO(6);
		}
		$waitFrist=$this->productM->checkWaitDiamond($id,11);//布厂单是否有过等钻操作
		$from_type=$this->productInfoRow['from_type']==1?2:1;
		$time_arr=$this->productM->getDelayTime($this->productInfoRow['prc_id'],$from_type);
		//出厂和送钻不属于工厂操作
		$product_arr=array();
		//第一次等钻 标准出厂时间加时
		if ($this->conf['status'] == 11 && $waitFrist==0)
		{
			$time = $this->productM->js_normal_time($time_arr['wait_dia'],$time_arr['is_rest']);
			$product_arr['wait_dia_starttime']=date('Y-m-d H:i:s',time());
			$product_arr['wait_dia_finishtime']=$time;
		}
		else if ($action_status== 11)//上次操作是等钻需要等钻后加时按工作日加
		{
			$time = $this->productM->js_normal_time($time_arr['behind_wait_dia'],$time_arr['is_rest']);
			$product_arr['wait_dia_endtime']=date('Y-m-d H:i:s',time());
		}
		
		if(isset($time))
		{
			$product_arr['esmt_time']=$time;
		}
		if($this->conf['status'] != 6 && $this->conf['status'] != 3)
		{
			 $res=$this->addOpraLog();
			 $product_arr['factory_opra_status']=$this->conf['status'];
		}
		$product_arr['buchan_fac_opra']=$this->conf['status'];
		$product_arr['edit_time']=date('Y-m-d H:i:s');
		$product_arr['remark']="工厂操作:".$this->dd->getEnum('buchan_fac_opra',$this->conf['status'])."，备注：第三方接口";
		$res1 =$this->saveProductInfo($product_arr);
		//写入布产操作日志
		$res2=$this->addLog();
		//写订单日志调节口
		//$res2=$this->addOrderStatus();
		if($res===false or $res1===false or $res2===false)
		{
			$pdo->rollback();
			return $this->error->getErrorMessageByNO(5);
		}
		$pdo->commit();
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
		return $this->error->getErrorMessageByNO(200);
	 }
	 //添加操作日志
	 function addLog()
	 {
		  $this->ensureProductM();
			  $opraArr=array(
				"bc_id"	=>$this->conf['id'],
				"status"	=>$this->productInfoRow['status'],
				"uid"		=> 0,
				"uname"	=> 'SYSTEM',
				"time"		=> date("Y-m-d H:i:s"),
				"remark"	=>"工厂操作:".$this->dd->getEnum('buchan_fac_opra',$this->conf['status'])."，备注：第三方接口"
			);
			$sql="insert into product_opra_log (".implode(',',array_keys($opraArr)).") values ('".implode("','",array_values($opraArr))."')";
			return $this->productM->db()->query($sql);
	 }
	 //回写订单日志
	 function addOrderStatus()
	 {
			if(2==$this->productInfoRow['from_type'])
			{
					$ori_str=array('order_sn'=>$this->productInfoRow['p_sn'],'fields'=>'id,order_status,order_pay_status,send_good_status');
					ksort($ori_str);
					$ori_str=json_encode($ori_str);
					$data=array("filter"=>$ori_str,"sign"=>md5('sales'.$ori_str.'sales'));
					$domain = Util::getDomain();
					if ($this->from_zhanting()) {
						$domain = str_ireplace('boss','zhanting', $domain);
					}

					$ret=Util::httpCurl($domain.'/api.php?con=sales&act=GetDeliveryStatus',$data);
					$ret=json_decode($ret,true);
					if($ret['error']==0)
					{
						$ori_str=array(
						'order_id'=>$ret['return_msg']['id'],
						'order_status'=>$ret['return_msg']['order_status'],
						'send_good_status'=>$ret['return_msg']['send_good_status'],
						'order_pay_status'=>$ret['return_msg']['order_pay_status'],
						'create_time'=>date('Y-m-d H:i:s'),
						'create_user'=>'接口',
						'remark'=>'接口修改工厂操作状态'
						);
						ksort($ori_str);
						$ori_str=json_encode($ori_str);
						$data=array("filter"=>$ori_str,"sign"=>md5('sales'.$ori_str.'sales'));
					    $ret=Util::httpCurl($domain.'/api.php?con=sales&act=addOrderAction',$data);
						$ret=json_decode($ret,true);
						return true;
					}
					else
					{
						return false;
					}
			}
			return true;
	 }
	 //验证请求是否合法这里需要修改
	 function authRequest($sign)
	 {
		return true;
	 }
	 //获取供应商相关布产列表信息
	 function getListByPrcNo($conf)
	 {
		 if($this->authRequest($conf->sign))
		 {
			$boss_productM = new ProductInfoModel(14);
			$zt_productM = new ProductInfoModel(61);

			$row = $boss_productM->getList($conf->fid,$conf->page,$conf->pagesize); //boss
			$row_num = count($row);

			if ($row_num > 0 && $row_num < $conf->pagesize) {
				// boss数据已取完，从zhanting取
 				$row = array_merge($row, $zt_productM->skipTakeBC($conf->fid,0,$conf->pagesize - $row_num));
			} else if ($row_num == 0) {
				$boss_num = $conf->page <= 1 ? 0 : $boss_productM->getProductCount($conf->fid); //boss
				$zt_num = $zt_productM->getProductCount($conf->fid);  //zhanting
				
				$total_num = $boss_num + $zt_num;
				$skip_offset = $conf->page <= 1 ? 0 : $conf->pagesize*($conf->page-1);
				if ($total_num > $skip_offset) {
					// boss数据已取完 or 没有数据，从zhanting取
					$row = $zt_productM->skipTakeBC($conf->fid, $skip_offset > 0 ? $skip_offset - $boss_num : 0, $conf->pagesize);
				}
			}

			if($row)  //boss
			{
				if (!isset($total_num)) {
					$total_num = $boss_productM->getProductCount($conf->fid); //boss
					$total_num += $zt_productM->getProductCount($conf->fid);  //zhanting
				}
				$arr['total_row']=$total_num;
				$arr['list']=$row;
				$wirteXml=CreateXML::getInstance();
				$xmlString=$wirteXml->toXml($arr);
				$res=$this->error->getErrorMessageByNO(200);
				$res['msg']=$xmlString;
			}
			else
			{
				$res=$this->error->getErrorMessageByNO(7);
			}
		 }
		 else
		 {
			$res=$this->error->getErrorMessageByNO(1);
		 }
		$this->res->error_no=$res['error'];
		$this->res->msg=$res['msg'];
		return $res;
	 }
	 //获取布产信息
	 function getInfoById($conf)
	 {
		 if($this->authRequest($conf->sign))
		 {
			 $this->ensureProductM();
			$row=$this->productM->getProductInfo($conf->bc_id);
			if($row and $row['prc_id']==$conf->fid)
			{
				$attr=$this->productM->getAttr($row['id']);
				$row=array_merge($row,$attr);
				$arr=array();
				foreach($row as $key=>$v)
				{
					$arr['rec_id']=$row['bc_sn'];
					$arr['consignee']=$row['consignee'];
					$arr['goods_name']=$row['goods_name'];
					$arr['goods_sn']=$row['style_sn'];
					$arr['goods_number']=$row['num'];
					$arr['stone']=isset($row['cart'])?$row['cart']:'';
					$arr['stone_color']=isset($row['yanse'])?$row['yanse']:'';
					$arr['stone_clear']=isset($row['clarity'])?$row['clarity']:'';
					$arr['certid']=isset($row['zhengshuhao'])?$row['zhengshuhao']:'';
					$arr['face_work']=isset($row['face_work'])?$row['face_work']:'';
					$arr['chengpin']=isset($row['xiangqian'])?$row['xiangqian']:'';
					$arr['gold']=isset($row['caizhi'])?$row['caizhi']:'';
					$arr['gold_weight']=isset($row['jinzhong'])?$row['jinzhong']:'';
					$arr['gold_color']=isset($row['jinse'])?$row['jinse']:'';
					$arr['finger']=isset($row['zhiquan'])?$row['zhiquan']:'';
					$arr['word']=isset($row['kezi'])?$row['kezi']:'';
					$arr['buchan_img']='';
					$arr['remark']=$row['info'];
					$arr['factory_num']='';//模号
				}
				unset($row);
				$wirteXml=CreateXML::getInstance();
				$xmlString=$wirteXml->toXml($arr);
				//file_put_contents('aa.txt',print_r($xmlString,true),FILE_APPEND);
				$res=$this->error->getErrorMessageByNO(200);
				$res['msg']=$xmlString;
			}
			else
			{
				$res=$this->error->getErrorMessageByNO(2);
			}
		 }
		 else
		 {
			$res=$this->error->getErrorMessageByNO(1);
		 }
		$this->res->error_no=$res['error'];
		$this->res->msg=$res['msg'];
		return $res;
	 }

	 function checkRequest($arr)
	 {
		if(!empty($arr))
		{
			if(empty($arr['prc_id']))
			{
				return $this->error->getErrorMessageByNO(10);
			}
			if(empty($arr['factory_order_sn']))
			{
				return $this->error->getErrorMessageByNO(11);
			}
			return $this->error->getErrorMessageByNO(200);
		}
		else
		{
			return $this->error->getErrorMessageByNO(9);
		}

	 }
	 //出货单同步接口
	 function addDeliveryOrder($conf)
	 {
		if($this->authRequest($conf->sign))
		{
			//解析XML
			$xml =simplexml_load_string($conf->product_info);
			if($xml==false)
			{
				$res=$this->error->getErrorMessageByNO(8);
			}
			else
			{
				$check_arr=array('prc_id'=>$conf->fid,'factory_order_sn'=>$conf->factory_order_sn);
				$res=$this->checkRequest($check_arr);
				if($res['error_no']==200)
				{
					$this->ensureProductM();
					$provider=$this->productM->getProcessorArr(array('status'=>1));
					$arr=array('num'=>0,
						'all_amount'=>0.00,
						'ship_num'=>$conf->factory_order_sn,
						'prc_id'=>$conf->fid,
						'prc_name'=>isset($provider[$conf->fid])?$provider[$conf->fid]:'',
						'status'=>1,
						'remark'=>'接口添加',
						'create_time'=>date('Y-m-d H:i:s'),
						'user_id'=>0,
						'user_name'=>'接口',
						'edit_user_id'=>0,
						'edit_user_name'=>'接口',
						'edit_time'=>date('Y-m-d H:i:s')
						);
					foreach ($xml->goods_info as $key=>$v)
					{
						$v=(array)$v;
						$v['chengbenjia']=(($v['net_gold_weight']*$v['gold_loss']*$v['gold_price'])+($v['main_stone_weight']*$v['zhushidanjia'])+($v['fushizhong']*$v['fushidanjia'])+$v['work_fee']+$v['extra_stone_fee']+$v['other_fee']+$v['fittings_cost_fee']);
						$arr['children'][]=$v;
						$arr['num']++;
						$arr['chengbenjia']+=$v['chengbenjia'];
						$arr['all_amount']+=$v['chengbenjia']+$v['tax_fee'];

					}
					include_once 'model/PurchaseModel.class.php';
					$model=new PurchaseModel(23);
					$res=$model->insertPurchaseReceipt($arr);
					if($res)
					{
						$res=$this->error->getErrorMessageByNO(200);
					}
					else
					{
						$res=$this->error->getErrorMessageByNO(5);
					}
				}

			}
		}
		else
		{
			$res=$this->error->getErrorMessageByNO(1);
		}
		$this->res->error_no=$res['error'];
		$this->res->msg=$res['msg'];
		return $res;

	}
	 
	private function from_zhanting($bc_id) {
		return $this->startsWith($bc_id, 'ZT') || $this->startsWith($bc_id, 'ECZT') || $this->startsWith($bc_id, 'zt') || $this->startsWith($bc_id, 'eczt');
	}

 	private function ensureProductM() {

		if (is_null($this->productM)) {
			$bc_id = null;
			if (is_array($this->conf)) {
				if (! isset($this->conf['bc_id']))
					return;
				$bc_id = $this->conf['bc_id'];
			} else {
				try {
					$bc_id = $this->conf->bc_id;
				} catch(Exception $e) {
					
				}
			}
			
            if (empty($bc_id)) return;
            
            if ($this->from_zhanting($bc_id)) {
                $this->productM = new ProductInfoModel(61);
            } else {
                $this->productM = new ProductInfoModel(14);
            }
        }
	}

	private function startsWith($haystack, $needle) {
		return strncmp($haystack, $needle, strlen($needle)) === 0;
	}

 }

?>
