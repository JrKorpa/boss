<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorBuyerModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-29 11:16:57
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorBuyerModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_processor_buyer';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"supplier_id"=>"供应商ID",
"buyer_id"=>"采购人ID",
"buyer_name"=>"姓名",
"buyer_account"=>"采购人账号",
"buyer_tel"=>"联系电话",
"buyer_papers"=>"身份证号",
"create_id"=>"添加人",
"create_time"=>"添加时间",
"is_deleted"=>"删除标识");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppProcessorBuyerController/search
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

	/**
	 * 获取用户信息
	 * @param $id
	 * @return mixed
	 */
	public function getUserInfo($id){
		$sql = 'SELECT `icd`,`account`,`code`,`real_name`,`mobile` FROM `user` WHERE `id` = '.$id;
		return DB::cn(1)->getRow($sql);
	}

	/**
	 * 获取供应商采购人[未删除]
	 * @param $supplier_id
	 * @return mixed
	 */
	public function getBuyer($supplier_id){
		$sql = 'SELECT `id`,`buyer_id`,`buyer_account`,`buyer_name`,`buyer_tel`,`buyer_papers` FROM '.$this->table().' WHERE `supplier_id` = '.$supplier_id.' AND `is_deleted` = 0';
		return DB::cn(13)->getAll($sql);
	}

	/**
	 * 获取全部采购人
	 */
	public function getBuyerId($supplier_id){
		$sql = 'SELECT `buyer_id` FROM '.$this->table().' WHERE `supplier_id` = '.$supplier_id;
		return DB::cn(13)->getAll($sql);
	}

	/**
	 * 删除采购人
	 * @param $del_user	   	(array)删除用户
	 * @param $supplier_id	(int)供应商ID
	 * @return mixed
	 */
	public function delBuyer($del_user,$supplier_id){
		foreach ($del_user as $v) {
			$sql = 'UPDATE `app_processor_buyer` SET	`is_deleted` = 1 WHERE `supplier_id` = '.$supplier_id.' AND `buyer_id` = '.$v;
			$res = DB::cn(14)->query($sql);
		}
		return $res;
	}

	/**
	 * 启用重复用户
	 * @param $users
	 * @param $supplier_id
	 * @return mixed
	 */
	public function EnableUser($users,$supplier_id){
		foreach ($users as $v) {
			$sql = 'UPDATE `app_processor_buyer` SET	`is_deleted` = 0 WHERE `supplier_id` = '.$supplier_id.' AND `buyer_id` = '.$v;
			$res = DB::cn(14)->query($sql);
		}
		return $res;
	}



}

?>