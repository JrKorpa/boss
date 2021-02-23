<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorTakerModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 10:11:14
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorTakerModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_processor_taker';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"supplier_id"=>"供应商ID",
"taker_id"=>"取货人ID",
"taker_account"=>"取货人账户",
"taker_name"=>"姓名",
"taker_gender"=>"性别",
"taker_tel"=>"联系电话",
"taker_papers"=>"身份证号",
"create_id"=>"添加人",
"create_time"=>"添加时间",
"is_deleted"=>"删除标识");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppProcessorTakerController/search
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
		if(!empty($where['supplier_id']))
		{
			$str .= "`supplier_id`='".$where['supplier_id']."' AND `is_deleted` = 0 AND";
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

	public function getSupName($id){
		$sql = 'SELECT `name` FROM `app_processor_info` WHERE `id` = '.$id;
		return $this->db()->getOne($sql);
	}

	/**
	 * 获取用户信息
	 * @param $id
	 * @return mixed
	 */
	public function getUserInfo($id){
		$sql = 'SELECT `icd`,`gender`,`account`,`code`,`real_name`,`mobile` FROM `user` WHERE `id` = '.$id;
		return DB::cn(1)->getRow($sql);
	}

	/**
	 * 获取委托取货人[未删除]
	 * @param $supplier_id
	 * @return mixed
	 */
	public function getTaker($supplier_id){
		$sql = 'SELECT `id`,`taker_gender`,`taker_id`,`taker_account`,`taker_name`,`taker_tel`,`taker_papers` FROM '.$this->table().' WHERE `supplier_id` = '.$supplier_id.' AND `is_deleted` = 0';
		return DB::cn(13)->getAll($sql);
	}

	/**
	 * 获取全部取货人
	 */
	public function getTakerId($supplier_id){
		$sql = 'SELECT `taker_id` FROM '.$this->table().' WHERE `supplier_id` = '.$supplier_id;
		return DB::cn(13)->getAll($sql);
	}
}

?>