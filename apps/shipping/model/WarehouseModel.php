<?php
/**
 * 仓库数据模块的模型（代替WareHouse/Api/api.php）
 *  -------------------------------------------------
 *   @file		: WareHouseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseModel extends  SelfModel
{
    protected $db;
	function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}
	/**
	 * 查询该订单号对应的【销售单】（已保存状态）的所有货号(替换WareHouse/Api/api->GetGoodsIdsByOrderSN方法)
	 */
	public function getGoodsIdsByOrderSn($order_sn){
        if(empty($order_sn)){
            return false;
        }	    
	    $sql = "select bg.goods_id from `warehouse_bill` as b,`warehouse_bill_goods` as bg  where b.id=bg.bill_id and b.order_sn = '{$order_sn}' and b.bill_type='S' and b.bill_status =1";
	    $result = $this->db()->getAll($sql);
        return $result;	    
	    
	}
	/**
	 * 检测货品状态是否为销售中(替换WareHouse/Api/api->check_goods_status方法)
	 */
	public function checkGoodsIsSale($goods_ids){
	    if(empty($goods_ids)){
	        return false;
	    }
	    if(is_array($goods_ids)){
	        $goods_ids = implode("','",$goods_ids);
	    }	    
	    //判断货品是否是销售中10
	    $sql = "select count(1) from `warehouse_goods` where goods_id in ('" . $goods_ids . "') and is_on_sale = 10";
	    $result = $this->db()->getOne($sql);
	    return $result;
	}
	/**
	 * 审核销售单接口（替换WareHouse/Api/api->checkXiaoshou方法）
	 */
	public function confirmSale($data){
	    // 审核销售单接口 根据order_sn
        if(!empty($data["order_sn"])) {
            $order_sn = $data['order_sn'];
        } else {
           return false;
        }

        if(!empty($data["goods_ids"])) {
            //$data['goods_ids'] = is_array($data['goods_ids'])?implode("','",$data['goods_ids']):array($data['goods_ids']);
        } else {
            return false;
        }
        if(!empty($data["user"])) {
            $update_user = $data['user'];
        } else {
            $update_user = $_SESSION['userName'];
        }
        if (!empty($data["ip"])) {
            $update_ip = $data['ip'];
        } else {
            $update_ip = Util::getClicentIp();
        }    
        if (!empty($data["time"])) {
            $time = $data["time"];
        } else {
            $time = date("Y-m-d H:i:s");
        }        
        try{
            $sql = "select id,bill_no from `warehouse_bill`  where order_sn ='{$order_sn}' and bill_type='S' and bill_status=1 ";
            $row = $this->db()->getRow($sql);
            #1变更仓储货品状态，变为 货品状态未变为已销售   
            if(is_array($data['goods_ids'])){
            	foreach ($data['goods_ids'] as $goods_key => $goods_id) {
		            $sql = "UPDATE `warehouse_goods`  SET `is_on_sale` =3,`chuku_time`= '{$time}' WHERE goods_id ='{$goods_id}' and is_on_sale =10 ";
		            $res_update=$this->db()->db()->exec($sql);
		            if($res_update<>1)
		            	return false;
            	}
            }else{
            	return false;
            }                     
            #2自动审核销售单
            if($row){
	            $sql = "update `warehouse_bill` set bill_status=2,check_time= '{$time}',check_user='{$update_user}'  where id='{$row['id']}' ";
		        $this->db()->query($sql);  
		    }else{
		    	return false;
		    } 
            #3仓储货品下架
            if(is_array($data['goods_ids'])){
            	foreach ($data['goods_ids'] as $goods_key => $goods_id) {
		            $sql = "UPDATE `goods_warehouse` SET `box_id` = '0', `create_time` = '0000-00-00 00:00:00', `create_user` = '' WHERE `good_id`='{$goods_id}'";
		            $this->db()->query($sql);
            	}
            }else{
            	return false;
            }             
            
            //写入日志
            $date = date("Y-m-d H:i:s");
            $sql = "INSERT INTO `warehouse_bill_status` (`id`, `bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES (NULL, '{$row['id']}', '{$row['bill_no']}', '2', '{$date}', '{$update_user}', '{$update_ip}');";
            $this->db()->query($sql);
            #记录出库时间，写入库龄表 warehouse_goods_age；
            //1、货品已销售，记录出库时间endtime（货品库龄已结束，之后不再统计）；
            if(is_array($data['goods_ids'])){
            	foreach ($data['goods_ids'] as $goods_key => $goods_id) {
		            $sql = "UPDATE `warehouse_goods_age` SET `endtime` = '{$date}' WHERE `goods_id`='{$goods_id}'";
		            $this->db()->query($sql);
            	}
            }else{
            	return false;
            }

                         
            /*if(!is_array($goods_ids)){
                $goods_dis = array($goods_ids);
            }
            foreach ($goods_ids as $goods_id) {
                # code...
                $sql = "select `id`,`goods_id`,`addtime`,`change_time` from `warehouse_goods` where `goods_id` = {$goods_id}";
                $goods_info = $this->db()->getRow($sql);
                $sql = "select `id` from `warehouse_goods_age` where `goods_id` = {$goods_id}";
                $goods_age = $this->db()->getRow($sql);
                $newtime = date("Y-m-d H:i:s",time());
                $total_age = intval(((strtotime($newtime)-strtotime($goods_info['addtime']))/86400));
                $self_age = intval(((strtotime($newtime)-strtotime($goods_info['change_time']))/86400));
                if(!$goods_age){
                    $sql = "insert into `warehouse_goods_age` (`warehouse_id`,`goods_id`,`endtime`,`total_age`)values(".$goods_info['id'].",".$goods_info['goods_id'].",'".$newtime."','".$total_age."','".$self_age."')";
                }else{
                    $sql = "update `warehouse_goods_age` set `endtime` = '".$newtime."' `total_age` = '".$total_age."' `self_age` = '".$self_age."' where `id` = ".$goods_age['id']."";
                }
                $this->db()->query($sql);
            }*/
        } catch (Exception $e) {//捕获异常
        	//echo json_encode($e).$sql;
            return false;
        }
        return true; 

	}
	
	/** 根据order_sn 检测是否含有对应的销售单 **/
	public function checkSbillByOrderSn($order_sn){
	    $sql = "SELECT `id`,`bill_status` FROM `warehouse_bill` WHERE `order_sn` = '{$order_sn}'";
	    return $this->db()->getRow($sql);
	}
	
	
	public function updateWarehouseGoods($order_sn,$out_company,$companyName){
		$sql = "update `warehouse_bill` set company_id_from={$out_company},company_from='{$companyName}'  where order_sn ='{$order_sn}' and bill_type='S'";
		return $this->db()->query($sql);
	}

	public function addWarehouseBill($order_sn,$out_company,$companyName){
		$sql="select * from  `warehouse_bill` where order_sn ='{$order_sn}' and bill_type='S'";
		$info=$this->db()->getRow($sql);
		$id=$info['id'];
		unset($info['id']);
		foreach ($info as $k=>$v){
			if($v==''){
				unset($info[$k]);
			}
		}
		$info['bill_no']='';
		
		$info['create_user']=$info['check_user']=$_SESSION['userName'];
		//$info['create_time']=$info['check_time']=$info['create_time'];
		
		$warehouseGoodsArr=$this->getWarehouseGoodsPrcArr($id);
		$sql=$this->insertSql($info,'warehouse_bill');
		
		if($out_company){
			$warehouseArr=$this->getMasterWarehouse($out_company);
			$to_warehouse_id=$warehouseArr['id']?$warehouseArr['id']:'null';
			$to_warehouse_name=$warehouseArr['name']?$warehouseArr['name']:'';
		}else{
			$to_warehouse_id='null';
			$to_warehouse_name='';
		}	
		
		 //根据货号来生成对应的单据
		 $goodsArr=$this->getGoodsByBillId($id); 
		 if(!empty($goodsArr)){ 
		 foreach ($goodsArr as $goods){   
				$otherArr=array(
						'out_company'=>$out_company,
						'companyName'=>$companyName,						
						'bill_id'=>$id,
						'to_warehouse_id'=>$to_warehouse_id,
						'to_warehouse_name'=>$to_warehouse_name,
						'goods'=>$goods
						
						
				);
				
				//生成M单据
				
				$this->db()->query($sql);
				$bill_id=$this->db()->insertId();
				$res=$this->addBillAndGoods($bill_id, "M", $otherArr);
				if(!$res){
					return false;
				}
				
				
				//生成B单据
				
				$this->db()->query($sql);
				$bill_id=$this->db()->insertId();
				$res=$this->addBillAndGoods($bill_id, "B", $otherArr);
				if(!$res){
					return false;
				}
				
				//生成L单据
				$this->db()->query($sql);
				$bill_id=$this->db()->insertId();
				$res=$this->addBillAndGoods($bill_id, "L", $otherArr);
				if(!$res){
					return false;
				}
			}
		}else{
			return false;
		}
		
		return true;
		
	}
	
	
	
	/**
	 * create_bill_no() 生成入库订单
	 */
	public function create_bill_no($type, $bill_id = '1')
	{
		$bill_id = substr($bill_id, -4);
		$bill_no = $type . date('Ymd', time()) . rand(100, 999) . str_pad($bill_id, 4,
				"0", STR_PAD_LEFT);
		return $bill_no;
	}

	/**
	 * create_bill_no() 生成单据和其单据明细
	 */
	public function addBillAndGoods($bill_id,$bill_type,$otherArr){
	     try{
			//生成M单据
			if(!$bill_id){
				return false;
			}
				
			//创建M单据编号
			$bill_no = $this->create_bill_no($bill_type,$bill_id);
			
			    $goodsArr=$otherArr['goods'];
			    $prc_id=$goodsArr['prc_id'];
			    $prc_name=$goodsArr['prc_name'];
				$chengbenjia = $goodsArr['chengbenjia'];
				$put_in_type=$goodsArr['put_in_type'];
				unset($goodsArr['id']);
				unset($goodsArr['prc_id']);
				unset($goodsArr['prc_name']);
				unset($goodsArr['chengbenjia']);
				unset($goodsArr['put_in_type']);
				foreach ($goodsArr as $m=>$v1){
					if($v1==''){
						unset($goodsArr[$m]);
					}
				}
				$goodsArr['bill_id']=$bill_id;
				$goodsArr['bill_no']=$bill_no;
				$goodsArr['bill_type']=$bill_type;
				$sql=$this->insertSql($goodsArr,'warehouse_bill_goods');
				$res=$this->db()->query($sql);
				if(!$res){
					return false;
				}
			
					
			
			
			
			if($bill_type=='M'){
			  $sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}',`bill_type`='M',`bill_status`=2,`to_company_id`=58,`to_company_name`='总公司',`to_warehouse_id` = 96,`to_warehouse_name` = '总公司后库',`from_bill_id`={$otherArr['bill_id']},`check_time`='".date("Y-m-d H:i:s",time()-60)."',goods_num=1 WHERE `id`={$bill_id}";
			}elseif($bill_type=='B'){
				$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}',`bill_type`='B',`bill_status`=2,`from_company_id`=58,`from_company_name`= '总公司',`to_warehouse_id` = 96,`to_warehouse_name` = '总公司后库',`pro_id`={$prc_id},`pro_name`='{$prc_name}',`from_bill_id`={$otherArr['bill_id']},`check_time`='".date("Y-m-d H:i:s",time()-40)."',goods_total={$chengbenjia},goods_num=1 WHERE `id`={$bill_id}";
			}elseif($bill_type=='L'){
				$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}',`bill_type`='L',`bill_status`=2,`put_in_type`={$put_in_type},`to_company_id`={$otherArr['out_company']},`to_company_name`='".$otherArr['companyName']."',`from_company_id` = null,`from_company_name`='',`pro_id`={$prc_id},`pro_name`='".$prc_name."',`from_bill_id`={$otherArr['bill_id']},`to_warehouse_name`='".$otherArr['to_warehouse_name']."',`to_warehouse_id`={$otherArr['to_warehouse_id']},`check_time`='".date("Y-m-d H:i:s",time()-20)."',goods_total={$chengbenjia},shijia=0,goods_num=1 WHERE `id`={$bill_id}";
			}else{
				return false;
			}
			
			$res=$this->db()->query($sql);
			if(!$res){
				return false;
			}
			//写入warehouse_bill_status信息
			$update_time = date('Y-m-d H:i:s');
			$ip = Util::getClicentIp();
			$sql1 = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 2, '{$update_time}', '{$_SESSION['userName']}', '{$ip}') ";
			$res=$this->db()->query($sql1);
			if(!$res){
				return false;
			}
			
			   
			
			  
			}catch (Exception $e){
				echo $e;exit;
				return false;
			}
			
			return true;
		}
	
	
	
	public function getWarehouseGoodsPrcArr($bill_id){
		$sql="SELECT g.prc_id,g.prc_name FROM warehouse_bill_goods AS bg , warehouse_goods AS g WHERE bg.goods_id=g.goods_id AND bg.bill_id={$bill_id} AND bg.bill_type = 'S'";
		return $this->db()->getAll($sql);
	}
	
	public function getWarehouseGoodsArr($bill_id,$prc_id){
	    $sql="SELECT bg.*,g.chengbenjia FROM warehouse_bill_goods AS bg , warehouse_goods AS g WHERE bg.goods_id=g.goods_id AND bg.bill_id={$bill_id} AND bg.bill_type = 'S' AND g.prc_id={$prc_id}";
		return $this->db()->getAll($sql);
	}
	
	
	public function getGoodsByBillId($bill_id){
		$sql="SELECT bg.*,g.prc_id,g.prc_name,g.chengbenjia,g.put_in_type FROM warehouse_bill_goods AS bg , warehouse_goods AS g WHERE bg.goods_id=g.goods_id AND bg.bill_id={$bill_id} AND bg.bill_type = 'S'";
		return $this->db()->getAll($sql);
	}
	
	/**
	 * 获取某公司的仓库
	 */
	public function getMasterWarehouse($company_id)
	{
		$sql = "select w.id,w.name,w.code from warehouse_shipping.warehouse as w ,`warehouse_rel` AS `r` where w.is_delete=1
 			AND `w`.`id` = `r`.`warehouse_id` and company_id= '".$company_id."' order by w.create_time desc LIMIT 1";
	
		return $this->db()->getRow($sql);
	}

    /**
     * 获取天生一对经销商批发销售单
     */
    function getTsydJxsAllBill_on ($where,$page,$pageSize=10,$useCache=true)
    {
        //不要用*,修改为具体字段
        $sql = "SELECT wb.*,jw.*,wp.create_user as print_user,wp.create_time as print_time FROM `warehouse_shipping`.`warehouse_bill` `wb` INNER JOIN `warehouse_shipping`.`jxc_wholesale` `jw` ON `wb`.`to_customer_id` = `jw`.`wholesale_id` left join warehouse_bill_print wp on wb.id=wp.bill_id";
        $str = '';
        if($where['to_customer_id'] != "")
        {
          $str .= "`jw`.`wholesale_name` like \"".addslashes($where['to_customer_id'])."%\" AND ";
        }
        if(!empty($where['bill_no']))
        {
            $bill_no_split = implode("','",$where['bill_no']);
            $str .= "`wb`.`bill_no` in('{$bill_no_split}') AND ";
        }
        if(!empty($where['create_time_start']))
        {
          $str .= "`wb`.`create_time`>='".$where['create_time_start']." 00:00:00' AND ";
        }
        if(!empty($where['create_time_end']))
        {
          $str .= "`wb`.`create_time`<='".$where['create_time_end']." 23:59:59' AND ";
        }
        if(!empty($where['create_user'])){
            $str .= "wb.create_user='{$where['create_user']}' AND ";
        }
        //是否打印
        if(!empty($where['is_print'])){
            if($where['is_print']==2){
                $str .= "wp.create_time is null AND ";
            }else{
                $str .= "wp.create_time <>'' AND ";
            }
        }
        //打印人
        if(!empty($where['print_user'])){
            $str .= "wp.create_user='{$where['print_user']}' AND ";
        }
        //打印时间范围
        if(!empty($where['print_time_start'])){
            $str .= "wp.create_time>='{$where['print_time_start']}' AND ";
        }
        if(!empty($where['print_time_end'])){
            $str .= "wp.create_time<='{$where['print_time_start']} 23:59:59' AND ";
        }
        $str .= "`wb`.`bill_type` = 'P' AND `wb`.`bill_status` = 1 AND `wb`.`confirm_delivery` = 1 and wb.is_tsyd=1";

        if($str)
        {
            $str = rtrim($str,"AND ");//这个空格很重要
        }
        $order_by = "`wb`.`id` DESC";
        if(!empty($where['sort_wholesale'])){
            $order_by = "jw.wholesale_name {$where['sort_wholesale']}";
        }
        if(!empty($where['sort_create_time'])){
            $order_by = "wb.create_time {$where['sort_create_time']}";
        }
        $sql .=" WHERE ".$str;
        $sql .= " ORDER BY {$order_by}";
        $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        return $data;
    }

    // 通用表查询
    public function select2($field,$where,$type=1) {
        $sql = "SELECT {$field} FROM `warehouse_shipping`.`warehouse_bill` where {$where}";
        $sql .= " ORDER BY `id` DESC";
        if($type==1){
            return $this->db()->getAll($sql);
        }elseif($type==2){
            return $this->db()->getRow($sql);
        }elseif($type==3){
            return $this->db()->getOne($sql);
        }
    }
    //天生一对加盟商单据信息
    public function getTsydWarehouseBill($bill_id){
        $sql = "select wb.*,w.wholesale_name from warehouse_bill wb left join jxc_wholesale w on wb.to_customer_id=w.wholesale_id where wb.id={$bill_id}";
        return $this->db()->getRow($sql);
    }
    //warehouse_bill表通用查询
    public function selectWarehouseBill($field="*",$where,$type=1){
        return $this->select($field,$where,$type,"warehouse_bill");
    }
    //warehouse_bill_goods表通用查询
    public function selectWarehouseBillGoods($field="*",$where,$type=1){
        return $this->select($field,$where,$type,"warehouse_bill_goods");
    }
    public function getTsydOrderDetailList($bill_id){
        $sql = "select d.* from warehouse_shipping.warehouse_bill_goods wg inner join app_order.app_order_details d on wg.detail_id=d.id where wg.bill_id={$bill_id}";
        return $this->db()->getAll($sql);
    }
    //goods_warehouse表通用查询
    public function selectGoodsWarehouse($field="*",$where,$type=1){
        return $this->select($field,$where,$type,"goods_warehouse");
    }
    //warehouse_goods表通用查询
    public function selectWarehouseGoods($field="*",$where,$type=1){
        return $this->select($field,$where,$type,"warehouse_goods");
    }
    //warehouse_box表通用查询
    public function selectWarehouseBox($field="*",$where,$type=1){
        return $this->select($field,$where,$type,"warehouse_box");
    }
    //jxc_wholesale表通用查询
    public function selectJxcWholesale($field="*",$where,$type=1){
        return $this->select($field,$where,$type,"jxc_wholesale");
    }
    //warehouse_bill_print表通用查询
    public function selectWarehouseBillPrint($field="*",$where,$type=1){
        return $this->select($field,$where,$type,"warehouse_bill_print");
    }
    // 更新单据状态
    public function updateBillStatusByBillno($bill_no)
    {
        # code...
        $sql = "update `warehouse_shipping`.`warehouse_bill` set `bill_status` = 2 where `bill_no` in('{$bill_no}')";
        //echo $sql;die;
        return $this->db()->query($sql);
    }

	function getWholesaleName($wholesale_id){
		$sql="select wholesale_name from jxc_wholesale where wholesale_id={$wholesale_id}";
		return $this->db()->getOne($sql);
	}
	
	function getWarehouseBill($bill_no){
		$sql="select to_customer_id from warehouse_bill where bill_no='{$bill_no}'";
		$to_customer_id= $this->db()->getOne($sql);
		$sql="select wb.order_sn,wb.goods_name,b.consignee from warehouse_bill_goods as wb left join app_order.base_order_info as b on wb.order_sn=b.order_sn where wb.bill_no='{$bill_no}'";
		$rows=$this->db()->getAll($sql);
		$order_sn_str='';
		$goods_name_str='';
		$consignee_str='';
		foreach ($rows as $v){
			$order_sn_str.=$v['order_sn'].',';
			$goods_name_str.=$v['goods_name'].',';
			$consignee_str.=$v['consignee'].',';
		}
		$row['to_customer_id']=$to_customer_id;
		$row['order_sn_str']=rtrim($order_sn_str,',');
		$row['goods_name_str']=rtrim($goods_name_str,',');
		$row['consignee_str']=rtrim($consignee_str,',');
		$row['num']=count($rows);
		return $row;
				
	}
}

?>