<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseReceiptDetailModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-15 21:24:19
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseReceiptDetailModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'purchase_receipt_detail';
        $this->_dataObject = array("id"=>"id",
"xuhao"=>"序号",
"status"=>"状态（见数据字典，采购收货货品状态）",
"purchase_receipt_id"=>"采购收货id",
"purchase_sn"=>"采购单单号",
"customer_name"=>"客户名",
"bc_sn"=>"布产号（有款布产必填）",
"style_sn"=>"款号（有款必填）",
"factory_sn"=>"模号（无款、有款都需添）",
"ring_mouth"=>"戒托镶口",
"product_type"=>"产品线",
"cat_type"=>"款式分类",
"hand_inch"=>"手寸",
"material"=>"主成色",
"gross_weight"=>"净金重",
"net_gold_weight"=>"主成色重(净金重)",
"gold_loss"=>"金耗",
"gold_price"=>"主成色买入单价(金价)",
"main_stone"=>"主石",
"main_stone_weight"=>"主石重",
"main_stone_num"=>"主石数量",
"work_fee"=>"工费	",
"extra_stone_fee"=>"超石费",
"other_fee"=>"其他费用",
"fittings_cost_fee"=>"配件成本",
"tax_fee"=>"税费",
"customer_info_stone"=>"客来石信息");
		parent::__construct($id,$strConn);
	}


	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT purRe.*,pr.ship_num,pr.prc_name, (select po.opra_info from purchase_iqc_opra as po where  po.rece_detail_id=purRe.id and (po.opra_code = 3 or po.opra_code = 2 ) order by po.id desc limit 1) as opra_info FROM `".$this->table()."` AS purRe LEFT JOIN purchase_receipt AS pr ON purRe.purchase_receipt_id = pr.id  WHERE `pr`.`status` = '2' ";

        if(isset($where['hidden']) && $where['hidden'] != ''){
            $sql .= " AND pr.hidden = ".$where['hidden'];
        }
		if(isset($where['ship_num']) && $where['ship_num'] != "")
		{
			$sql .= " AND pr.ship_num = '".$where['ship_num']."'";
		}
		if(isset($where['prc_id']) && $where['prc_id'] != "")
		{
			$sql .= " AND pr.prc_id = ".$where['prc_id'];
		}
		if(isset($where['purchase_receipt_id']) && $where['purchase_receipt_id'] != "")
		{
			$sql .= " AND purRe.purchase_receipt_id = ".$where['purchase_receipt_id'];
		}
		if(isset($where['purchase_sn']) && $where['purchase_sn'] != "")
		{
			$sql .= " AND purRe.purchase_sn = '".$where['purchase_sn']."'";
		}
		if(isset($where['bc_sn']) && $where['bc_sn'] != "")
		{
			$sql .= " AND purRe.bc_sn = '".$where['bc_sn']."'";
		}
		if(isset($where['style_sn']) && $where['style_sn'] != "")
		{
			$sql .= " AND purRe.style_sn = '".$where['style_sn']."'";
		}
		if(isset($where['customer_name']) && $where['customer_name'] != "")
		{
			$sql .= " AND purRe.customer_name = '".$where['customer_name']."'";
		}
		if(isset($where['status']) && $where['status'] != "")
		{
			$sql .= " AND purRe.status = '".$where['status']."'";
		}

		$sql .= " ORDER BY purRe.id DESC";
		//echo $sql ; exit;
		$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	//根据采购收货单流水号取数据
	function getListForRid($id,$col="*")
	{
		$sql = "SELECT $col FROM ".$this->table()." WHERE purchase_receipt_id = $id";
		$arr = $this->db()->getAll($sql);
		return $arr;
	}

	//删除，删除的同事删除对应的log
	function deleteOfRid($id)
	{
	//	$sql = "DELETE from `".$this->table()."` WHERE purchase_receipt_id = $id";

		$sql = "DELETE a.*,b.* FROM purchase_log as a,".$this->table()." as b WHERE a.rece_detail_id = b.id and b.purchase_receipt_id = ".$id;
		if($this->db()->query($sql))
		{
			return true;
		}
		return false;
	}

	//根据序号取主表的 供应商信息、出货单号
	function getCont($id)
	{
		$sql = "SELECT `prc_id`,`prc_name`,`ship_num` FROM ".$this->table()." AS purRe LEFT JOIN purchase_receipt AS pr ON purRe.purchase_receipt_id = pr.id WHERE purRe.id = ".$id;
		$arr = $this->db()->getRow($sql);
		return $arr;
	}


	/*------------------------------------------------------ */
	//-- 判断传入的ID的某一列的值是不是一样的。
	// true  是一样的。 false 不是一样的。
	//-- by Zlj
	/*------------------------------------------------------ */

	public function checkDistinct($col,$ids)
	{
		$sql = "SELECT count(distinct $col) FROM ".$this->table()." AS purRe LEFT JOIN purchase_receipt AS pr ON purRe.purchase_receipt_id = pr.id WHERE purRe.id in($ids)";
		$count = $this->db()->getOne($sql);
		if($count == '1'){return true;}return false;
	}

	//根据序号取一行内容
	public function getRowOfid($id,$col ='*')
	{
		$sql = "SELECT $col FROM ".$this->table()." WHERE id = ".$id;
		return $this->db()->getRow($sql);
	}

	//修改
	public function update($valueArr,$whereArr)
	{
		$field = '';
		$where = ' 1';
		foreach($valueArr as $k => $v)
		{
			$field .= "$k = '$v',";
		}
		foreach($whereArr as $k => $v)
		{
			$where .= " AND $k = '$v'";
		}
		$field = substr($field,0,-1);
		$sql = "UPDATE ".$this->table()." SET ".$field;
        $sql .= " WHERE ".$where;
		return $this->db()->query($sql,array());
	}

	//通过流水单号获得流水单号是否存在如果存在返回id 和 status
	public function getPurchaseReceiptIdExsist($purchase_receipt_id){
		$sql = "SELECT `id`,`status` FROM `purchase_receipt` WHERE id=".$purchase_receipt_id;
		return 	$this->db()->getRow($sql);
	}

	//根据采购收货流水号取已质检通过的货品
	public function getPurchaseReceiptDetails($purchase_receipt_id,$status = 4){
		$sql = "SELECT `xuhao`,`bc_sn`,`style_sn`,`factory_sn`,`ring_mouth`,`is_cp_kt`,`cat_type`,`hand_inch`,`material`,`gross_weight`,`net_gold_weight`,`gold_loss`, `gold_price`,`main_stone`,`main_stone_weight`,`main_stone_num`,`work_fee`,`extra_stone_fee`,`other_fee`,`fittings_cost_fee`,`tax_fee`,`customer_info_stone`FROM `purchase_receipt_detail` WHERE `status`=".$status." AND `purchase_receipt_id`=".$purchase_receipt_id;

		return $this->db()->getAll($sql);

	}

	//在有效的采购收货单中（除已取消的），是否已经收过此布产号
	public function getCountBcsn($bc_sn)
	{
		$sql = "SELECT purchase_receipt_id FROM ".$this->table()." AS det LEFT JOIN `purchase_receipt` AS pur ON pur.id = det.purchase_receipt_id WHERE pur.status != 3 AND det.bc_sn = '".$bc_sn."'";
		$row=$this->db()->getOne($sql);
	}


}

?>