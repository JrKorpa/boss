<?php
/**
 *  -------------------------------------------------
 *   @file		: AppApplyBillsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-03 15:28:39
 *   @update	:
 *  -------------------------------------------------
 */
class AppApplyBillsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_apply_bills';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"序号",
"apply_no"=>"申请单号",
"pay_number"=>"应付单号",
"detail_array"=>"对应明细:明细ID用，隔开",
"pay_type"=>"应付类型",
"bills_type"=>"单据状态:1/新增2/待审核3/已驳回4/已取消5/待生成6/已生成",
"supplier_id"=>"结算商ID",
"pay_total"=>"应付金额",		//系统统计
"adjust_total"=>"调整金额",	//调整金额
"apply_total"=>"申请金额",	//系统金额-调整金额
"invoice_no"=>"发票号码",
"create_id"=>"制单人ID",
"create_name"=>"制单人",
"create_time"=>"制单时间",
"check_id"=>"审核人ID",
"check_name"=>"审核人",
"check_time"=>"审核时间");
		parent::__construct($id,$strConn);
	}


	/**
	 *	pageList，分页列表
	 *
	 *	@url AppApplyBillsController/search
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
		if(!empty($where['pay_type'])) {$str .= "`pay_type`='".$where['pay_type']."' AND ";}
		if(!empty($where['bills_type'])) {$str .= "`bills_type`='".$where['bills_type']."' AND ";}
		if(!empty($where['apply_no'])) {$str .= "`apply_no`='".$where['apply_no']."' AND ";}

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
	 * 生成申请单号
	 * @return string
	 */
	public function mkApplyNO(){
		$sql = 'SELECT `apply_no` FROM '.$this->table().' WHERE id = (SELECT max(id) from '.$this->table().')';
		$str = $this->db()->getOne($sql);
		$no = (substr($str,2,8) != date('Ymd',time()))?1:intval(substr($str,11))+1;
		$number = 'SQ'.date('Ymd',time()).'-'.str_pad($no,5,"0",STR_PAD_LEFT);
		return  $number;
	}

	/**
	 * 检查上传数据
	 * @param $file					上传的数据
	 * @param $pay_type				应付类型
	 * @param $supplier_id			供货商ID
	 * @param string $apply_number	申请单号
	 */
	public function checkData($file,$pay_type,$supplier_id,$apply_number=''){
		$file_array = explode(".",$file['name']);
		$file_extension = strtolower(array_pop($file_array));

		if($file_extension != 'csv'){
			Util::jsonExit('请上传CSV格式的文件');
		}
		$f = fopen($file['tmp_name'],"r");

		$detaiModel = new AppDealDetailModel(30);

		$detail = array();//明细ID组
		$total = array();//应付金额组

		$i = 0;

		while(! feof($f)) {
			$con = fgetcsv($f);
			if ($i > 0) {
				if (trim($con[0]) == '' && trim($con[1]) == '') {
					if ($i == 1) {
						Util::jsonExit('上传文件数据不能为空');
					}
				} else {
					$detail_id = strtoupper(trim($con[0])); //流水号
//					$price = strtoupper(trim($con[1]));    //应付金额
//
//					if (empty($detail_id) || empty($price)) {
//						Util::jsonExit('流水号和应付金额为必填项');
//					}

					if (!preg_match("/^[A-Z]{2}\d*$/", $detail_id)) {
						Util::jsonExit('第' . ($i + 1) . '行流水号格式不对，流水号只能为数字');
					}
//					if (!preg_match("/^(\d+)(\.\d+)?$/", $price)) {
//						Util::jsonExit('第' . ($i + 1) . '行应付金额只能为数字并且是正数。');
//					}
					//读取明细
					$gRow = $detaiModel->getRow($detail_id);
					//检查是否有此流水号
					if (!count($gRow)) {
						Util::jsonExit('请检查第' . ($i + 1) . '行数据，没有流水号：' . $detail_id);
					}
					//判断上传流水号的供货商是否和表单的供货商相同
					if ($gRow['supplier_id'] != $supplier_id) {
						Util::jsonExit('流水号' . $detail_id . '供货商和所选供货商不同，不能申请。');
					}
					//判断明细类型是否一致;
					if($gRow['detail_type'] != $pay_type){
						Util::jsonExit('流水号' . $detail_id . '应付类型和所选应付类型不同，不能申请。');
					}

					//判断此项的应付申请状态是否为未申请(添加状态下)
					if ($gRow['apply_status'] != 1 && empty($apply_number)) {
						Util::jsonExit('流水号' . $detail_id . '已在单据' . $gRow['apply_number'] . '中提交过应付申请');
					}
					//判断此项的应付申请状态是否为未申请(修改状态下)
					if ($gRow['apply_status'] != 1 && (!empty($apply_number) && $gRow['apply_number'] != $apply_number)) {
						Util::jsonExit('流水号' . $detail_id . '已在单据' . $gRow['apply_number'] . '中提交过应付申请');
					}
					/*todo
					//如果商品是成品，并且单据类型是退货返厂单或者是其他出库单。金额为负数
					if($pay_type == 2 && ($gRow['pay_type'] == 2 || $gRow['pay_type'] == 3))
					{
						$val['total_cope'] = '-'.$price;//应付金额
						$val['total']			= '-'.$gRow['total'];//系统金额
					}
					//如果商品是石包，并且单据类型是退石单。金额为负数
					if($pay_type == 3 && $gRow['pay_type'] == 2)
					{
						$val['total_cope'] = '-'.$price;//应付金额
						$val['total']			= '-'.$gRow['total'];//系统金额
					}*/

					$detail[] = $gRow['id'];//明细ID组
					$total[] = $gRow['amount_total'];//应付金额
				}
			}
			$i++;
		}

		$unique_arr = array_unique($detail);
		if(count($detail) != count($unique_arr))
		{
			Util::jsonExit('上传文件中流水号有重复值，请检查后再上传。');
		}

		$apply_data['amount'] = count($detail);//总数量
		$apply_data['detail_array'] = implode(',',$detail);//明细ID组
		$apply_data['pay_total'] = array_sum($total) ;//总的应付金额

		return $apply_data;
	}

	public function getDetail($id){
		$sql = 'SELECT `detail_array` FROM '.$this->table().' WHERE `id` ='.$id;
		$str = $this->db()->getOne($sql);
		$arr = explode(',',$str);
		$detail_arr = array();
		foreach ($arr as $v) {
			$sql = 'SELECT `id`,`detail_type`,`serial_number`,`goods_no`,`supplier_name`,`check_time`,`supplier_order`,`amount_total` FROM `app_deal_detail` WHERE `id` ='.$v.' ORDER BY `id` DESC';
			$detail_arr[] = $this->db()->getRow($sql);
		}

		return $detail_arr;
	}

	/**
	 * 获得合计金额
	 * @param $arr
	 * @return number
	 */
	public function sumAmount($arr){
		$amount = array();
		foreach ($arr as $v ) {
			$sql = 'SELECT `pay_total` FROM '.$this->table().' WHERE `id` = '.$v;
			$amount[] = $this->db()->getOne($sql);
		}
		$sum = array_sum($amount);
		return $sum;
	}

	/**
	 * 检查是否一个供应商
	 * @param $arr
	 * @return bool
	 */
	public function checkSupplier($arr){
		foreach ($arr as $v ) {
			$sql = 'SELECT `supplier_id` FROM '.$this->table().' WHERE `id` = '.$v;
			$suppliers[] = $this->db()->getOne($sql);
		}
		$res = array_unique($suppliers);
		$res = count($res);
		return ($res==1)?$suppliers[0]:false;
	}

	/**
	 * 检查明细类型
	 * @param $arr
	 * @return bool
	 */
	public function checkDetailType($arr){
		foreach ($arr as $v ) {
			$sql = 'SELECT `pay_type` FROM '.$this->table().' WHERE `id` = '.$v;
			$pay_type[] = $this->db()->getOne($sql);
		}
		$res = array_unique($pay_type);
		$res = count($res);
		return ($res==1)?$pay_type[0]:false;
	}

	/**
	 * 判断数据创建人是否自己
	 */
	public function checkSelf($id){

		$sql = "SELECT `create_id`,`create_name` FROM ".$this->table()." WHERE `id` = ".$id;
		$res = $this->db()->getRow($sql);

		if($res['create_id'] == $_SESSION['userId'] && $res['create_name'] == $_SESSION['userName']){
			return true;
		}else{
			return false;
		}
	}



}

?>