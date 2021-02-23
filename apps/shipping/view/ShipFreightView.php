<?php
/**
 *  -------------------------------------------------
 *   @file		: ShipFreightView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class ShipFreightView extends View
{
    public function __construct($obj){
        if(is_array($obj)){
            foreach($obj as $key => $value){
                $key = '_'.$key;
                $this->$key = $value;
            }
        }else{
            return parent::__construct($obj);
        }
    }
	protected $_id;
	protected $_order_no;
	protected $_freight_no;
	protected $_express_id;
	protected $_consignee;
	protected $_cons_address;
	protected $_cons_mobile;
	protected $_cons_tel;
	protected $_note;
	protected $_create_id;
	protected $_create_name;
	protected $_create_time;
	protected $_is_deleted;
	protected $_sender;
	protected $_department;
	protected $_remark;

	public function get_id(){return $this->_id;}
	public function get_order_no(){return $this->_order_no;}
	public function get_freight_no(){return $this->_freight_no;}
	public function get_express_id(){return $this->_express_id;}
	public function get_consignee(){return $this->_consignee;}
	public function get_cons_address(){return $this->_cons_address;}
	public function get_cons_mobile(){return $this->_cons_mobile;}
	public function get_cons_tel(){return $this->_cons_tel;}
	public function get_note(){return $this->_note;}
	public function get_create_id(){return $this->_create_id;}
	public function get_create_name(){return $this->_create_name;}
	public function get_create_time(){return $this->_create_time;}
	public function get_is_deleted(){return $this->_is_deleted;}
	public function get_sender(){return $this->_sender;}
	public function get_department(){return $this->_department;}
	public function get_remark(){return $this->_remark;}

}
?>