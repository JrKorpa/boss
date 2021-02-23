<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillPayModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 18:25:39
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillPayModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_bill_pay';
        $this->_dataObject = array("id"=>"id主键",
"bill_id"=>"单据id",
"pro_id"=>"加工商id",
"pro_name"=>"加工商名称",
"pay_content"=>"支付内容（数据字典）",
"pay_method"=>"结算方式（数据字典）",
"tax"=>"是否含税金",
"amount"=>"金额");
		parent::__construct($id,$strConn);
	}

	function getList($where = array())
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";

		if(isset($where['bill_id']) && $where['bill_id'] != "")
		{
			$sql .= " AND bill_id = ".$where['bill_id'];
		}
		$sql .= " ORDER BY id DESC";
		// file_put_contents('e:/8.sql', "\r\n".$sql."\r\n",FILE_APPEND);
		return $this->db()->getAll($sql);
	}

	//一个单据的结算总金额
	function getAmount($bill_id)
	{
		$sql = "SELECT sum(amount) FROM ".$this->table()." WHERE bill_id = ".$bill_id;
		$amount = $this->db()->getOne($sql);
		$amount = $amount?$amount:0.00;
		return $amount;
	}
	
	//一个单据的所有返厂货品总成本金额 
	function getGoodsAmount($bill_id)
	{
		$sql = "SELECT sum(chengbenjia) FROM  warehouse_bill_goods WHERE bill_id = ".$bill_id;
		$amount = $this->db()->getOne($sql);
		$amount = $amount?$amount:0.00;
		return $amount;
	}
	
	//一个单据的所有返厂货品总成本金额 
	function getBillType($bill_id)
	{
		$sql = "SELECT bill_type FROM  warehouse_bill_goods WHERE bill_id = ".$bill_id;
		$bill_type = $this->db()->getOne($sql);
		return $bill_type;
	}


	public function getProArr($bill_id){
		$sql = "SELECT pro_id FROM ".$this->table()." WHERE bill_id=".$bill_id;
		$res = $this->db()->getAll($sql);
		if(!$res){
			return false;
		}
		$arr =array();
		foreach($res as $key=>$val){
			$arr[]=$val['pro_id'];
		}
		return $arr;
	}
	
	public function delete_cha($id)
	{
		$sql = 'delete from '.$this->table().' where bill_id = '.$id. ' and pro_name ="入库成本尾差"';
		//echo $sql;exit;
		return $this->db()->query($sql);
	}

}

?>