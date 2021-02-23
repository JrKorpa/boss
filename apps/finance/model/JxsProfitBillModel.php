<?php
/**
 *  -------------------------------------------------
 *   @file		: JxsProfitBillModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-07-17 00:49:38
 *   @update	:
 *  -------------------------------------------------
 */
class JxsProfitBillModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'jxs_profit';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"jxs_id"=>"经销商",
"created_date"=>"申请时间",
"created_by"=>"申请人",
"calc_profit"=>"结算金额",
"calc_date"=>"结算时间",
"status"=>"状态,0未结算，1结算, 2取消");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url JxsProfitBillController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
		if(!empty($where['jxs_id']))
		{
			$str .= "`jxs_id`='".$where['jxs_id']."' AND ";
		}
        if(!empty($where['status']))
        {
            $str .= "`status`='".$where['status']."' AND ";
        }
        if(!empty($where['start_time'])){
            $str.="`created_date` >= '".$where['start_time']." 00:00:00' AND ";
        }
        if(!empty($where['end_time'])){
            $str.="`created_date` <= '".$where['end_time']." 23:59:59' AND ";
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
	
	function cancelBill($bill_ids) {
	    $profit_ids_str = implode(',', $bill_ids);
	    $sql = "update `".$this->table()."` set `status`=2 where id in ({$profit_ids_str}); ";
	    return $this->db()->query($sql);
	}
	
}

?>