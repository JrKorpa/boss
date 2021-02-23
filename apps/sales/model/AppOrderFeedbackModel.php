<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderFeedbackModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2016-01-26 10:30:32
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderFeedbackModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_order_feedback';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID",
"ks_option"=>"客诉选项",
"ks_user"=>"添加人",
"ks_time"=>"添加时间",
"ks_status"=>"状态");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppOrderFeedbackController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
		if($where['ks_user'] != "")
		{
			$str .= "`ks_user` like \"".addslashes($where['ks_user'])."%\" AND ";
		}
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

    /**
     *  get_feedback_list，取出所有的可用客诉内容
     *
     *  @url AppOrderFeedbackController/get_feedback_list
     */
    public function get_feedback_list()
    {
        # code...
        $sql = "select `id`,`ks_option` from `".$this->table()."` where `ks_status` = 1";
        $data = $this->db()->getAll($sql);
        return $data;
    }
}

?>