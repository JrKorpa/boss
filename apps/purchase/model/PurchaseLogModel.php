<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-20 16:44:03
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseLogModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'purchase_log';
        $this->_dataObject = array("id"=>"ID",
"rece_detail_id"=>"关联序号ID",
"status"=>"当前状态",
"remark"=>"备注",
"uid"=>"操作人ID",
"uname"=>"操作人姓名",
"time"=>"操作时间");
		parent::__construct($id,$strConn);
	}

	/**
	* 操作增加日志
	* rece_id 收货货品序号（操作的内容ID）
	* status  货品当前状态
	* remark 备注
	**/
	function addLog($rece_id,$status,$remark)
	{
//		$olddo = array();
//		$newdo=array(
//			'rece_detail_id'=> $rece_id,
//			'status'	=> $status,
//			'remark'	=> $remark,
//			'uid'		=> $_SESSION['userId'],
//			'uname'		=> $_SESSION['userName'],
//			'time'		=> date('Y-m-d H:i:s')
//		);
                $sql = "insert into ".$this->table()."(`rece_detail_id`,`status`,`remark`,`uid`,`uname`,`time`) values ($rece_id,$status,'".$remark."',".$_SESSION['userId'].",'".$_SESSION['userName']."','".date('Y-m-d H:i:s')."')";
                
                $this->db()->query($sql);
                $lastid = $this->db()->insertId();
		//$res = $this->saveData($newdo,$olddo);
		return $lastid;
	}

	function getLog($rece_id)
	{
		$sql = "SELECT `id`,`rece_detail_id`,`status`,`remark`,`uid`,`uname`,`time` FROM ".$this->table()." WHERE `rece_detail_id` = ".$rece_id." ORDER BY id DESC";
		return $this->db()->getAll($sql);
	}

	//根据序号删除log
	function delLog($rece_id)
	{
		$sql = "DELETE FROM ".$this->table()." WHERE rece_detail_id = ".$rece_id;echo $sql;exit;
		return $this->db()->query($sql);
	}
	//根据收货流水号删除log
	function delLog_purReid($purchase_receipt_id)
	{
		$sql = "DELETE a.* FROM ".$this->table()." as a,purchase_receipt_detail as b WHERE a.rece_detail_id = b.id and b.purchase_receipt_id = ".$purchase_receipt_id;
		return $this->db()->query($sql);
	}
}

?>