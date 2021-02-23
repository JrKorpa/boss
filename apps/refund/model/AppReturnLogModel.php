<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReturnLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-13 11:05:18
 *   @update	:
 *  -------------------------------------------------
 */
class AppReturnLogModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_return_log';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"return_id"=>"退款单id",
"even_time"=>"操作时间",
"even_user"=>"操作人",
"even_content"=>"操作内容");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppReturnLogController/search
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
    
    /**
     * 获取单条申请退款的日志记录
     * @param type $id
     * @return boolean
     */
    function getLogInfoByReturnId($id) {
        if($id<1){
            return false;
        }
        $sql = "SELECT  `even_time`, `even_user`, `even_content` FROM `".$this->table()."` WHERE `return_id`=$id";
        $logInfo = $this->db()->getAll($sql);
        return $logInfo;
    }
    
}

?>