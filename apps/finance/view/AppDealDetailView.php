<?php
/**
 *  -------------------------------------------------
 *   @file		: AppDealDetailView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-26 16:49:30
 *   @update	:
 *  -------------------------------------------------
 */
class AppDealDetailView extends View
{

	protected $_id;
	protected $_detail_type;
	protected $_serial_number;
	protected $_goods_no;//货号/单据编号
	protected $_goods_type;//货品分类/单据分类
	protected $_certficate_no;//证书号
	protected $_make_time;
	protected $_check_time;
	protected $_put_in_type;//入库方式
	protected $_goods_status;//货品状态
	protected $_supplier_id;//供货商/结算商ID
	protected $_supplier_name;//供货商/结算商名称
	protected $_amount_total;
	protected $_apply_status;
	protected $_apply_number;

	protected $_company_id;		//所属公司
	protected $_supplier_order;	//供货商单号
	protected $_pay_cont;		//支付内容


	public function get_id(){return $this->_id;}
	public function get_detail_type(){return $this->_detail_type;}
	public function get_serial_number(){return $this->_serial_number;}
	public function get_goods_no(){return $this->_goods_no;}
	public function get_certficate_no(){return $this->_certficate_no;}
	public function get_goods_type(){return $this->_goods_type;}
	public function get_make_time(){return $this->_make_time;}
	public function get_check_time(){return $this->_check_time;}
	public function get_put_in_type(){return $this->_put_in_type;}
	public function get_goods_status(){return $this->_goods_status;}
	public function get_supplier_id(){return $this->_supplier_id;}
	public function get_supplier_name(){return $this->_supplier_name;}
	public function get_amount_total(){return $this->_amount_total;}
	public function get_apply_status(){return $this->_apply_status;}
	public function get_apply_number(){return $this->_apply_number;}
	public function get_company_id(){return $this->_company_id;}
	public function get_supplier_order(){return $this->_supplier_order;}
	public function get_pay_cont(){return $this->_pay_cont;}


	public function detail_type(){
		$type = [
			['id'=>1,'label'=>'代销借贷'],
			['id'=>2,'label'=>'成品采购'],
			['id'=>3,'label'=>'石包采购'],
		];
		return	$type;
	}

	public function apply_status($id = false){
		$status = [
			['id'=>1,'label'=>'待申请'],
			['id'=>2,'label'=>'待审核'],
			['id'=>3,'label'=>'已驳回'],
			['id'=>4,'label'=>'已审核']
		];
		return ($id)?$status[($id-1)]['label']:$status;
	}

	public function putin_type($id = false){
		$dict = new DictView(new DictModel(1));
		$status = $dict->getEnumArray('warehouse.put_in_type');
		return ($id)?$status[($id-1)]['label']:$status;
	}

	//单据类型
	public function order_type($id = false){
		$type = [
			['id'=>'L','label'=>'收货单'],
			['id'=>'B','label'=>'返厂单'],
			['id'=>'S','label'=>'买石单'],
			['id'=>'T','label'=>'退货单'],
			['id'=>'O','label'=>'其他'],
//			['id'=>'M','label'=>'调拨单'],
//			['id'=>'E','label'=>'损益单'],
//			['id'=>'C','label'=>'其他出库单'],
//			['id'=>'W','label'=>'盘点单'],
		];
		if($id === false){
			return $type;
		}else{
			foreach ($type as $v) {
				if($v['id'] == $id){return $v['label'];}
			}
		}
		return 'undefined';
	}

	public function company(){
		$company = [
			['id'=>58,'label'=>'总公司'],
		];
		return $company;
	}




}
?>