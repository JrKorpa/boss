<?php
/**
 *  -------------------------------------------------
 *   @file		: AppApplyRealPayModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-04 19:20:56
 *   @update	:
 *  -------------------------------------------------
 */
class AppApplyRealPayModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_apply_real_pay';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"real_number"=>"实付单号",
"apply_no"=>"应付单号",
"bank_serial"=>"银行流水号",
"account_name"=>"户名",
"bank_name"=>"银行名称",
"bank_account"=>"收款方账户",
"pay_time"=>"付款时间",
"supplier_id"=>"结算商ID",
"supplier_name"=>"结算商名称",
"pay_total"=>"实付金额",
"create_id"=>"创建人ID",
"create_name"=>"创建人",
"create_time"=>"创建时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppApplyRealPayController/search
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
		if(!empty($where['real_number'])) {$str .= "`real_number`LIKE'%".$where['real_number']."%' AND ";}
		if(!empty($where['supplier_id'])) {$str .= "`supplier_id`='".$where['supplier_id']."' AND ";}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function mkRealNumber(){
		$sql = 'SELECT `real_number` FROM '.$this->table().' WHERE id = (SELECT max(id) from '.$this->table().')';
		$str = $this->db()->getOne($sql);
		$no = (substr($str,2,8) != date('Ymd',time()))?1:intval(substr($str,11))+1;
		$number = 'SF'.date('Ymd',time()).'-'.str_pad($no,5,"0",STR_PAD_LEFT);
		return  $number;
	}
}

?>