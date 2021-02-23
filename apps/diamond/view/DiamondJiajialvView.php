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
class DiamondJiajialvView extends View
{
	protected $_id;
	protected $_good_type;
	protected $_from_ad;
	protected $_cert;
	protected $_carat_min;
	protected $_carat_max;
	protected $_jiajialv;
	protected $_status;


	public function get_id(){return $this->_id;}
	public function get_good_type(){return $this->_good_type;}
	public function get_from_ad(){return $this->_from_ad;}
	public function get_cert(){return $this->_cert;}
	public function get_carat_min(){return $this->_carat_min;}
	public function get_carat_max(){return $this->_carat_max;}
	public function get_jiajialv(){return $this->_jiajialv;}
	public function get_status(){return $this->_status;}
    public function get_opertion_list(){
        return DiamondJiajialvModel::$optertion_list;
    }


}
?>