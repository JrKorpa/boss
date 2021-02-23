<?php
/**
 *  -------------------------------------------------
 *   @file		: AppApplyBalanceModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-04 12:54:28
 *   @update	:
 *  -------------------------------------------------
 */
class AppApplyBalanceModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_apply_balance';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"balance_no"=>"结算单号",
"apply_array"=>"对应申请单[数组]",
"supplier_id"=>"结算商ID",
"supplier_name"=>"结算商名称",
"total_sys"=>"系统金额",
"total_dev"=>"调整金额",
"total_real"=>"应付金额",
"pay_total"=>"实付金额",
"pay_type"=>"应付类型",
"pay_status"=>"付款状态",
"balance_status"=>"单据状态",
"create_id"=>"制单人ID",
"create_name"=>"制单人",
"create_time"=>"制单时间",
"check_id"=>" ",
"check_name"=>" ",
"check_time"=>" ");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppApplyBalanceController/search
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

		if(!empty($where['supplier_id'])) {$str .= "`supplier_id`='".$where['supplier_id']."' AND ";}
		if(!empty($where['balance_no'])) {$str .= "`balance_no`='".$where['balance_no']."' AND ";}
		if(!empty($where['pay_status'])) {$str .= "`pay_status`='".$where['pay_status']."' AND ";}
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
	 * 生成结算单号
	 */
	public function mkBalanceNO(){
		$sql = 'SELECT `balance_no` FROM '.$this->table().' WHERE id = (SELECT max(id) from '.$this->table().')';
		$str = $this->db()->getOne($sql);
		$no = (substr($str,2,8) != date('Ymd',time()))?1:intval(substr($str,11))+1;
		$number = 'YF'.date('Ymd',time()).'-'.str_pad($no,5,"0",STR_PAD_LEFT);
		return  $number;
	}

	/**
	 * 获取应付申请单
	 */
	public function getBills($arr){
		if(!is_array($arr)){
			return false;
		}else{
			foreach ($arr as $v) {
				$sql = 'SELECT `apply_no`,`pay_total`,`invoice_no`,`id` FROM `app_apply_bills` WHERE `id` = '.$v;
				$bills[] = $this->db()->getRow($sql);
			}
			return $bills;
		}
	}

	/**
	 * 获取供应商支付信息
	 */
	public function getSupplierPay($id){
		if(empty($id)){
			return false;
		}
		$keys=array('id');
		$vals=array($id);
		$ret=ApiModel::supplier_api($keys,$vals,'GetSupplierPay');
		return $ret;
	}

	/**
	 * 判断数据创建人是否自己
	 */
	public function checkSelf($id){

		$sql = "SELECT `create_id`,`create_name` FROM ".$this->table()." WHERE `id` = ".$id;
		$res = $this->db()->getRow($sql);

		if($res['create_id'] == $_SESSION['userId']){
			return true;
		}else{
			return false;
		}
	}

}

?>