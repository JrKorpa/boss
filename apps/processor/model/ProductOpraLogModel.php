<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductOpraLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-28 16:54:09
 *   @update	:
 *  -------------------------------------------------
 */
class ProductOpraLogModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'product_opra_log';
        $this->_dataObject = array("id"=>" ",
"bc_id"=>"布产号ID",
"status"=>"当前状态",
"remark"=>"备注",
"uid"=>"操作人ID",
"uname"=>"操作人姓名",
"time"=>"操作时间");
		parent::__construct($id,$strConn);
	}

	/**
	* 增加操作日志；订单布产则需要推送到订单日志
	* 为了保证布产日志和订单日志统一，所有布产日志全部调用
	* bc_id 布产号ID
	* status  货品当前状态
	* remark 备注
	* status 默认是去当前状态，考虑到事务，如果传值则用该状态
	**/
	function addLog($bc_id,$remark,$status=false)
	{
		$olddo = array();
		//根据布产号查布产状态和布产类型
		$model=new ProductInfoModel($bc_id,14);
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
			 //ApiModel::sales_api(array("order_no","create_user","remark"), array($order_sn,$newdo['uname'],$remark), "AddOrderLog");
		}
		$res = $this->saveData($newdo,$olddo);
		return $res;
	}
	/*
	function addLog($bc_id,$status,$remark)
	{
		$olddo = array();
		$newdo=array(
			'bc_id'		=> $bc_id,
			'status'	=> $status,
			'remark'	=> $remark,
			'uid'		=> $_SESSION['userId']?$_SESSION['userId']:0,
			'uname'		=> $_SESSION['userName']?$_SESSION['userName']:'第三方',
			'time'		=> date('Y-m-d H:i:s')
		);

		$res = $this->saveData($newdo,$olddo);
		return $res;
	}
	*/
	function getLog($rece_id)
	{
		$sql = "SELECT `id`,`rece_detail_id`,`status`,`remark`,`uid`,`uname`,`time` FROM ".$this->table()." WHERE `rece_detail_id` = ".$rece_id." ORDER BY id DESC";
		return $this->db()->getAll($sql);
	}

	/*
	*写日志
	*
	*/
	public function addLogNew($bc_id,$remark,$status=false){
		$model=new ProductInfoModel($bc_id,13);
		$olddo =$model->getDataObject();
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
			//推送订单日志
		$sql ="insert into `".$this->table()."` (bc_id,status,remark,uid,uname,time) values ('".$newdo['bc_id']."','".$newdo['status']."','".$newdo['remark']."','".$newdo['uid']."','".$newdo['uname']."','".$newdo['time']."');";

		return $this->db()->query($sql);

	}
}

?>