<?php
/**
 * 批量快递单文件明细管理Model
 *  -------------------------------------------------
 *   @file		: ExpressFileDetailModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2015-09-28 
 *   @update	:
 *  -------------------------------------------------
 */
class ExpressFileDetailModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'express_file_detail';
		$this->pk='id';
		$this->_prefix='';
		$this->_dataObject = array(
		    "id"=>"主键ID",
		    "file_id"=>"文件ID",
		    "freight_no"=>"快递单号",
		    "sender"=>"发件人",
		    "department"=>"发件部门",
		    "remark"=>"备注(发件缘由)",
		    "consignee"=>"收件人",
		    "cons_address"=>"收件人地址",
		    "cons_tel"=>"收件人电话",		    		    		    
		);
		parent::__construct($id,$strConn);
	}
	/**
	 * 根据文件id删除快递单记录
	 * @param unknown $id
	 */
	public function deleteByFileId($file_id){
	    $sql = "delete from ".$this->table()." where file_id = {$file_id}";
	    return $this->db()->query($sql);
	}
	/**
	 * 获取快递单文件明细-快递信息列表
	 */
	public function getList($file_id)
	{   
	    $sql = "select * from ".$this->table()." where file_id={$file_id} limit 0,500";//临时限制最大输出500行	    
	    return $this->db()->getAll($sql);
	}
	
	public function getRow($fileds="*",$where){
	    $sql = "select {$fileds} from ".$this->table()." where {$where}";
	    return $this->db()->getRow($sql);
	}

	
}

?>