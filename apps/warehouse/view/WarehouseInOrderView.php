<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseInOrderView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-09 16:54:53
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseInOrderView extends View
{
	protected $_id;
	protected $_order_no;
	protected $_order_type;
	protected $_put_in_type;
	protected $_send_no;
	protected $_goods_num;
	protected $_cost_price;
	protected $_pay_price;
	protected $_order_status;
	protected $_prc_id;
	protected $_company_id;
	protected $_addby_id;
	protected $_addby_time;
	protected $_addby_ip;
	protected $_check_id;
	protected $_check_time;
	protected $_check_ip;
	protected $_order_note;
	protected $_is_deleted;


	public function get_id(){return $this->_id;}
	public function get_order_no(){return !empty($this->_order_no)?$this->_order_no:$this->create_order_no();}
	public function get_order_type(){return !empty($this->_order_type)?$this->_order_type:1;}
	public function get_put_in_type(){return !empty($this->_put_in_type)?$this->_put_in_type:1;}
	public function get_send_no(){return $this->_send_no;}
	public function get_goods_num(){return $this->_goods_num;}
	public function get_cost_price(){return $this->_cost_price;}
	public function get_pay_price(){return $this->_pay_price;}
	public function get_order_status(){return $this->_order_status;}
	public function get_prc_id(){return $this->_prc_id;}
	public function get_company_id(){return $this->_company_id;}
	public function get_addby_id(){return $this->_addby_id;}
	public function get_addby_time(){return $this->_addby_time;}
	public function get_addby_ip(){return $this->_addby_ip;}
	public function get_check_id(){return $this->_check_id;}
	public function get_check_time(){return $this->_check_time;}
	public function get_check_ip(){return $this->_check_ip;}
	public function get_order_note(){return $this->_order_note;}
	public function get_is_deleted(){return $this->_is_deleted;}

	/**
	 * create_order_no() 生成入库订单
	 */
	public function create_order_no(){

		$sql = 'SELECT order_no FROM warehouse_in_order WHERE id = (SELECT max(id) from warehouse_in_order)';
		$str = DB::cn(21)->getOne($sql);
		$no = (substr($str,2,8) != date('Ymd',time()))?1:intval(substr($str,11))+1;

		return  'RS'.date('Ymd',time()).'-'.str_pad($no,5,"0",STR_PAD_LEFT);
	}

}

?>