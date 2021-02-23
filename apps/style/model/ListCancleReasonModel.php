<?php
/**
 *  -------------------------------------------------
 *   @file		: ListCancleReasonModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-03 12:27:26
 *   @update	:
 *  -------------------------------------------------
 */
class ListCancleReasonModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'list_cancle_reason';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"自增ID",
"style_id"=>"款id",
"create_user"=>"操作人",
"create_time"=>"操作时间",
"remark"=>"操作备注",
"type"=>"作废原因类型");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ListCancleReasonController/search
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
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
    
    //获取作废原因
    public function getCancleReason($style_id) {
        if(empty($style_id)){
            return false;
        }
       
		$sql = "SELECT `type`,`remark` FROM `".$this->table()."` WHERE `style_id`=".$style_id." ORDER BY id desc LIMIT 1";
		return $this->db()->getRow($sql);
    }
}

?>