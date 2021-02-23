<?php
/**
 * 销售模块的数据模型（代替Sales/Api/api.php）
 *  -------------------------------------------------
 *   @file		: SaleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class ProductModel
{
    protected $db;
	function __construct ($strConn="")
	{
		$this->db = DB::cn($strConn);
	}
	public function db(){
	    return $this->db;
	}
	final public static function add_special_char($value)
	{
	    if ('*' == $value || false !== strpos($value, '(') || false !== strpos($value, '.') || false !== strpos($value, '`'))
	    {
	        //不处理包含* 或者 使用了sql方法。
	    }
	    else
	    {
	        $value = '`' . trim($value) . '`';
	    }
	    if (preg_match('/\b(select|insert|update|delete)\b/i', $value))
	    {
	        $value = preg_replace('/\b(select|insert|update|delete)\b/i', '', $value);
	    }
	    return $value;
	}
	
	/*
	*获取所有加工时间的信息
	*/
	public function getAllProcessorWorktime($id){
		$sql = "select * from kela_supplier.app_processor_worktime where pw_id=".$id;
		return  $this->db->getRow($sql);  
	}


	/*
	*更新编辑供应商的所有出厂时间
	* @params $pros 更新前的放假时间
	* @params $holiday_time 更新后的放假时间
	*/
	public function updateEsmttimeById($prc_id,$pros=array()){
		$days= count(explode(';', $pros['holiday_time']));
		$days = $days+intval($pros['normal_day']);
		$esmt_time  = date('Y-m-d',strtotime("+".$days." day"));
		$sql ="update kela_supplier.product_info set esmt_time='".$esmt_time."' where status in(1,2,3,4,5,6,7,8) and prc_id=".$prc_id;
		return $this->db()->query($sql);  

	}


	/*
	*写日志
	*
	*/
	public function addLogNew($bc_id,$remark,$status=false){
		$model=new ProductInfoModel($bc_id,13);
		if ($status == false)
		{
			$status = $model->getValue('status');
		}
		$from_type = $model->getValue('from_type');
		$order_sn = $model->getValue('p_sn');
		$newdo=array(
			'bc_id'		=> $bc_id,
			'status'	=> $status,
			'remark'	=> $remark,
			'uid'		=> $_SESSION['userId']?$_SESSION['userId']:0,
			'uname'		=> $_SESSION['userName']?$_SESSION['userName']:'第三方',
			'time'		=> date('Y-m-d H:i:s')
		);
		if($from_type == 2) 
		{
			//推送订单日志
			$sql="select id,order_status,send_good_status,order_pay_status from app_order.base_order_info where order_sn='{$order_sn}'";
			$row=$this->db->getRow($sql);

			$sql="insert into app_order.app_order_action(`order_id`,`order_status`,`shipping_status`,`pay_status`,`create_user`,`create_time`,`remark`) values ('{$row['id']}','{$row['order_status']}','{$row['send_good_status']}','{$row['order_pay_status']}','{$create_user}','".date('Y-m-d H:i:s')."','{$remark}')";
			$res = $this->db->query($sql);

		}
		return $res?$res:false;

	}

	/*
	*
	*
	*/




}

?>