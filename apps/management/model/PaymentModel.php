<?php
/**
 *  -------------------------------------------------
 *   @file		: PaymentModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-18 12:03:20
 *   @update	:
 *  -------------------------------------------------
 */
class PaymentModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'payment';
        $this->_dataObject = array("id"=>"主键",
"pay_code"=>"支付方式拼音",
"pay_name"=>"支付方式中文名",
"pay_fee"=>"支付手续费",
"pay_desc"=>"描述",
"pay_order"=>"排序",
"pay_config"=>"配置项",
"is_enabled"=>"是否启用",
"is_cod"=>"是否货到付款",
"is_display"=>"是否显示",
"is_web"=>"是否网络付款可用",
"is_online"=>"支持线上",
"is_offline"=>"支持线下",
"addby_id"=>"创建人",
"add_time"=>"创建时间",
"is_deleted"=>"是否删除",
"is_beian"=>"备案开关 0关/1开"            
        );
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url PaymentController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE `is_deleted` =".$where['is_deleted'];
		if($where['pay_name'] != "")
		{
			$sql .= " AND pay_name like \"%".addslashes($where['pay_name'])."%\"";
		}
        if($where['is_range']==1){
            	$sql .=" AND is_online=1 AND is_offline=0 ";
            
        }
        if($where['is_range']==2){
            	$sql .=" AND is_offline=1 AND is_online=0 ";
            
        }
        if($where['is_range']==3){
            	$sql .=" AND is_offline=1 AND is_online=1 ";
            
        }
        if($where['is_way']==1){
            	$sql .=" AND is_order=1 AND is_balance=0 ";
            
        }
        if($where['is_way']==2){
            	$sql .=" AND is_order=0 AND is_balance=1 ";
            
        }
        if($where['is_way']==3){
            	$sql .=" AND is_order=1 AND is_balance=1 ";
            
        }
        if($where['is_enabled']!=""){
            	$sql .=" AND is_enabled='".$where['is_enabled']."'";
            
        }
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 *	getList，列表排序
	 *
	 *	@url PaymentController/listAll
	 */
	public function getList ()
	{
		$sql = "SELECT id,pay_name,pay_code,is_enabled FROM `".$this->table()."` WHERE `is_deleted`='0' ORDER BY pay_order DESC";
		return $this->db()->getAll($sql);
	}
    public function TgetList ()
    {
        $sql = "SELECT id,pay_name,pay_code,is_enabled FROM `".$this->table()."`  ORDER BY pay_order DESC";
        return $this->db()->getAll($sql);
    }

	public function getEnabled(){
		$sql = "SELECT id,pay_name,pay_code,is_beian FROM `".$this->table()."` WHERE `is_deleted`='0' AND `is_enabled` = '1' ORDER BY pay_order DESC";
		return $this->db()->getAll($sql);
	}

	/**
	 *	sortPayment，保存排序
	 *
	 *	@url PaymentController/saveSort
	 */
	public function sortPayment ($pays)
	{
		$len = count($pays);
		try{
			for ($i=0;$i<$len;$i++)
			{
				$sql = "UPDATE `payment` SET `pay_order`='".($i+1)."' WHERE `id`=".$pays[$i];
				$this->db()->query($sql);
			}
		}
		catch(Exception $e)
		{
			return false;
		}
		return true;
	}

	/**
	 *	getList，列表排序
	 *
	 *	@url PaymentController/listAll
	 */
	public function getAll ()
	{
		$sql = "SELECT id,pay_name,pay_code FROM `".$this->table()."`";
		return $this->db()->getAll($sql);
	}
	/**
	 *	getNameById
	 *
	 *	
	 */
	 public function getNameById($id) 
	 {
		 $sql = "SELECT pay_name FROM `".$this->table()."` where id='{$id}'";
		 return $this->db()->getOne($sql);
	 }
	 
	 //罗湖店去掉京东业务销售数据（去掉订购类型：北京京东世纪贸易有限公司的订单）
	 public function getIdbyName($name)
	 {
		 $sql = "SELECT  id FROM `".$this->table()."` where pay_name='{$name}'";
		 return $this->db()->getOne($sql);
	 }

}

?>