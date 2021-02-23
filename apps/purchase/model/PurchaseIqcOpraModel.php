<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseIqcOpraModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-18 18:17:59
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseIqcOpraModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'purchase_iqc_opra';
        $this->_dataObject = array("id"=>"ID",
"rece_detail_id"=>"操作的序号",
"opra_code"=>"1=质检通过 2=报废 3=IQC未过",
"opra_uname"=>"操作人",
"opra_time"=>"操作时间",
"opra_info"=>"备注");
		parent::__construct($id,$strConn);
	}

	//取最后一条OQC未过备注
	public function getOne_iqc_w($rece_detail_id)
	{
		$sql = "SELECT * FROM ".$this->table()." WHERE rece_detail_id = ".$rece_detail_id." AND opra_code = 3 order by id desc limit 1";
		return $this->db()->getRow($sql);
	}

	public function getiqcList($id)
	{
		$sql = "SELECT * FROM ".$this->table()." WHERE rece_detail_id = ".$id;
		return $this->db()->getAll($sql);
	}
}

?>