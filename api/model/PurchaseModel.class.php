<?php
/**
 *  -------------------------------------------------
 *   @file		: .php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		:
 *   @update	:
 *  -------------------------------------------------
 */
 include_once 'model/CommonModel.class.php';
 class PurchaseModel extends CommonModel
 {
	public function __construct($id = null,$strConn='')
	{
		parent::__construct($id,$strConn);
    }
	public function insertPurchaseReceipt($arr)
	{
		$pdo = $this->db()->db();
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);
		$pdo->beginTransaction();
		$res=$this->addReceipt($arr);
		$res2=false;
		if($res)
		{
			$id=$pdo->lastInsertId();
			$res2=$this->addReceiptDetials($arr['children'],$id);
		}
		if($res and $res2)
		{
			$pdo->commit();
			$status=true;
		}
		else
		{
			$pdo->rollback();
			$status=false;
		}
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
		return $status;
	}
	//采购收货单主表
	public function addReceipt($arr)
	{
		unset($arr['children']);
		$sql="insert into purchase_receipt(`".implode("`,`",array_keys($arr))."`) values('".implode("','",array_values($arr))."')";
		return $this->db()->query($sql);

	}
	//采购收货详情
	public function addReceiptDetials($arr,$id)
	{
		if(!empty($arr))
		{
			$sql='';
			foreach($arr as $key=>$v)
			{
				$v['purchase_receipt_id']=$id;
				$v['purchase_sn']='';
				$v['is_cp_kt']='';
				$sql.="insert into purchase_receipt_detail(`".implode("`,`",array_keys($v))."`) values ('".implode("','",array_values($v))."');";
			}
			file_put_contents('aa.txt',print_r($sql,true),FILE_APPEND);
			return $this->db()->query($sql);
		}
		else
		{
			return false;
		}

	}




 }

?>