<?php
/**
 *  -------------------------------------------------
 *   @file		: ShipParcelDetailModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-06 18:27:56
 *   @update	:
 *  -------------------------------------------------
 */
class ShipParcelDetailModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'ship_parcel_detail';
		$this->pk='id';
		$this->_prefix='';
		$this->_dataObject = array("id"=>" ",
		"parcel_id"=>"包裹id 关联ship_parcel 主键ID",
		"zhuancang_sn"=>"转仓单号",
		"from_place_id"=>"出货地ID",
		"to_warehouse_id"=>"入货仓ID",
		"shouhuoren"=>"收货人",
		"num"=>"货品数量",
		"amount"=>"货品金额",
		"goods_sn"=>"全部款号",
		"goods_name"=>"全部商品名称",
		"create_user"=>"添加人",
		"create_time"=>"添加时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ShipParcelDetailController/search
	 */
	function pageList ($where,$page = 1,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";

		$str = '';
		if(!empty($where['_id']))
		{
			$str .="`parcel_id`=".$where['_id']." AND ";
		}
		if(!empty($where['zhuancang_sn']))
		{
			$str .="`zhuancang_sn`='".$where['zhuancang_sn']."' AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}

		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
        public function GetConsigneeByOrdersn($order_sn) {
            $consignee = ApiModel::sales_api(array("order_sn"), array($order_sn), "GetConsigneeByorder_sn");

            return $consignee;

        }
	/**
	 * 普通查询
	 * @param $fields string 查询的字段
	 * @param $where string 条件
	 * @param $type 1:查询单个字段； 2：查询一条记录；3：查询全部记录
	 **/
	public function select2($fields, $where , $type = 1){
		$sql = "SELECT {$fields} FROM `ship_parcel_detail` WHERE {$where} ORDER BY `id` DESC";
		if($type == 1){
			return $this->db()->getOne($sql);
		}else if( $type == 2 ){
			return $this->db()->getRow($sql);
		}else{
			return $this->db()->getAll($sql);
		}
	}

	/**
	 * 添加调拨单明细
	 * @param  $detail 调拨单明细信息
	 * @param $amount 包裹总金额
	 * @param $id 包裹单ID
	 * @param $bill_id 调拨单单据ID
	 * @param $express_sn 快递单号
	 */
	public function insertDetail($detail, $amount , $id , $bill_id ,$express_sn){
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//业务逻辑开始
			$sql = "UPDATE `ship_parcel` SET `amount` = {$amount}, `num` = `num` + {$detail['num']}  WHERE `id` = {$id}";
			$pdo->query($sql);
			$sql = "INSERT INTO `ship_parcel_detail`
					( `parcel_id`,`zhuancang_sn`, `from_place_id`, `to_warehouse_id` , `shouhuoren` , `num`, `amount`, `goods_sn`, `goods_name`, `create_user`, `create_time`, `order_sn` )
					VALUES ( {$detail['parcel_id']}, '{$detail['zhuancang_sn']}', {$detail['from_place_id']},
						{$detail['to_warehouse_id']}, '{$detail['shouhuoren']}', {$detail['num']},
						{$detail['amount']}, '{$detail['goods_sn']}', '{$detail['goods_name']}',
						'{$detail['create_user']}', '{$detail['create_time']}', '{$detail['order_sn']}' )";
			$pdo->query($sql);

			//回写调拨单的快递单号
			$ret = ApiWarehouseModel::SetShipNumber(array('ship_number'=> $express_sn , 'bill_id'=> $bill_id) , $type='add');
			if(!$ret){
				//回写失败，制造错误回滚
				$pdo->query('');
			}
			//业务逻辑结束
		}
		catch(Exception $e){//捕获异常
		$pdo->rollback();//事务回滚
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return true;
	}

	/**
	* 删除明细
	* @param $detail_id 要删除的明细的ID
	* @param $num 当前要删除的调拨单的货品数量
	* @param $amount 当前要删除的调拨单的货品金额
	* @param $baoguo_id 当前包裹单的ID
	* @param $bill_no 要删除的调拨单号
	*/
	public function deleteDetail($detail_id, $num,$amount, $baoguo_id, $bill_no){
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
                        //回写订单日志删除的时候 start
                        $sql = "select `order_sn` from `ship_parcel_detail` where `id`={$detail_id}";
                        $order_data = $this->db()->getRow($sql);

                        $order_sn = $order_data['order_sn'];
                        $time = date('Y-m-d H:i:s');
                        $user = $_SESSION['userName'];
                        $remark = "调拨单号：".$bill_no."在包裹列表详情中删除！";
                        ApiModel::sales_api(array("order_no","create_user","remark"), array($order_sn,$user,$remark), "AddOrderLog");

                        //add log end
			$sql = "UPDATE `ship_parcel` SET `num` = `num` - {$num} , `amount` = `amount` - {$amount} WHERE `id` = {$baoguo_id}";
			$pdo->query($sql);

			$sql = "DELETE FROM `ship_parcel_detail` WHERE `id` = {$detail_id}";
			$pdo->query($sql);

			//判断删的是否是调拨单，是要调用接口，回写调拨单
			if($bill_no != false && substr($bill_no,0,1) == 'M'){
				//回写调拨单的快递单号
				$ret = ApiWarehouseModel::SetShipNumber(array('bill_no'=> $bill_no) , $type = 'del');
				if(!$ret){
					//回写失败，制造错误回滚
					$pdo->query('');
				}
			}

		}
		catch(Exception $e){//捕获异常
			//print_r($e);exit;
		$pdo->rollback();//事务回滚
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}
    //获取快递单号
    public function getExpressSn($parcel_id) {
        $sql = "select `express_id`,`express_sn` from `ship_parcel`,`ship_parcel_detail` "
                . "where `ship_parcel`.`id`=`ship_parcel_detail`.`parcel_id` and `ship_parcel`.`id`='{$parcel_id}'";
        $data = $this->db()->getRow($sql);
        return $data;
    }


    public function getWarehouseBillInfo($bill_nos){
        
        if(!is_array($bill_nos) || empty($bill_nos))
        {
            return false;
        }
        $bill_no_str = implode("','",$bill_nos);
        $sql="SELECT 
                wb.bill_status,wb.order_sn,wbg.goods_id 
            FROM 
                warehouse_shipping.warehouse_bill wb
                inner join warehouse_shipping.warehouse_bill_goods wbg on wb.bill_no = wbg.bill_no
            WHERE 
                wb.bill_no in ('$bill_no_str') 
                AND wb.bill_status in (1,2)
                ;
        ";
        //echo $sql;
        return $this->db()->getAll($sql);
    }

    public function getWarehouseBillInfoByOrderSn($order_sn)
    {
        $sql="SELECT 
                COUNT(distinct wbg.goods_id ) goods_num
            FROM 
                warehouse_shipping.warehouse_bill wb
                inner join warehouse_shipping.warehouse_bill_goods wbg on wb.bill_no = wbg.bill_no
                inner join `cuteframe`.`company` comin on comin.id = wb.from_company_id
                inner join `shipping`.`ship_parcel_detail` spd on spd.zhuancang_sn = wb.bill_no
                inner join `shipping`.`ship_parcel` sp on sp.id = spd.parcel_id
            WHERE 
                wb.order_sn = '$order_sn'
                AND wb.bill_type = 'M'
                AND wb.bill_status in (1,2)
                AND comin.`company_name` in ('总公司','BDD深圳分公司')
                AND sp.send_status=2
            GROUP BY wb.order_sn
        ";
        return $this->db()->getOne($sql);
    }

    public function getOrderInfo($order_nos){
        
        if(!is_array($order_nos) || empty($order_nos))
        {
            return false;
        }
        $order_nos_str = implode("','",$order_nos);
        $sql="SELECT 
                oi.id,oi.order_sn,COUNT(od.is_finance=2) cnt
            FROM 
                app_order.base_order_info oi
                inner join app_order.app_order_details od on oi.id=od.order_id
            WHERE 
                oi.order_sn in ('$order_nos_str') 
                AND oi.order_status = 2
            GROUP BY oi.id
                ;
        ";
        //echo $sql;
        return $this->db()->getAll($sql);
    }

    public function updateSendtime($send_order_ids)
    {
        $date = date("Y-m-d H:i:s");
        foreach($send_order_ids as $order_id){
            $sql="SELECT * FROM `app_order`.`app_order_time` where order_id = $order_id ;";
            $ret = $this->db()->getRow($sql);
            if(!$ret){
                $sql="INSERT INTO `app_order`.`app_order_time` (`id`, `order_id`, `allow_shop_time`) VALUES (NULL, '$order_id', '$date');";
                $this->db()->query($sql);
            }
        }
    }


}/** END CLASS **/

?>