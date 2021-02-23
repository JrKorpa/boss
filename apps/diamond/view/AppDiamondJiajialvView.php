<?php
/**
 *  -------------------------------------------------
 *   @file		: DiamondJiajialvView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-15 15:56:34
 *   @update	:
 *  -------------------------------------------------
 */
class AppDiamondJiajialvView extends View
{
	protected $_id;
	protected $_good_type;
	protected $_from_ad;
	protected $_cert;
	protected $_cost_min;
	protected $_cost_max;
	protected $_jiajialv;
	protected $_status;


	public function get_id(){return $this->_id;}
	public function get_good_type(){return $this->_good_type;}
	public function get_from_ad(){return $this->_from_ad;}
	public function get_cert(){return $this->_cert;}
	public function get_cost_min(){return $this->_cost_min;}
	public function get_cost_max(){return $this->_cost_max;}
	public function get_jiajialv(){return $this->_jiajialv;}
	public function get_status(){return $this->_status;}
    public function get_opertion_list(){
        return AppDiamondJiajialvModel::$optertion_list;
    }


}
?>