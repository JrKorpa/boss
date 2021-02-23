<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductApplyInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-17 16:30:13
 *   @update	:
 *  -------------------------------------------------
 */
class ProductApplyInfoView extends View
{
	protected $_id;
	protected $_order_sn;
	protected $_bc_sn;
	protected $_detail_id;
	protected $_style_sn;
	protected $_apply_info;
	protected $_old_info;
	protected $_apply_status;
	protected $_factory_status;
	protected $_factory_time;
	protected $_goods_status;
	protected $_apply_id;
	protected $_apply_name;
	protected $_apply_time;
	protected $_check_id;
	protected $_check_name;
	protected $_check_time;
	protected $_refuse_remark;
	protected $_special;

	public function get_id(){return $this->_id;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_bc_sn(){return $this->_bc_sn;}
	public function get_detail_id(){return $this->_detail_id;}
	public function get_style_sn(){return $this->_style_sn;}
	public function get_apply_status(){return $this->_apply_status;}
	public function get_factory_status(){return $this->_factory_status;}
	public function get_factory_time(){return $this->_factory_time;}
	public function get_apply_id(){return $this->_apply_id;}
	public function get_apply_name(){return $this->_apply_name;}
	public function get_apply_time(){return $this->_apply_time;}
	public function get_check_id(){return $this->_check_id;}
	public function get_check_name(){return $this->_check_name;}
	public function get_check_time(){return $this->_check_time;}
	public function get_refuse_remark(){return $this->_refuse_remark;}
	public function get_special(){return $this->_special;}
	public function get_apply_info(){return unserialize($this->_apply_info);}
	public function get_old_info(){return unserialize($this->_old_info);}

	public function get_goods_status(){
		$sql = "SELECT `status` FROM `product_info` WHERE `bc_sn` = '".$this->_bc_sn."'";
		$model = $this->getModel();
		return $model->db()->getOne($sql);
	}


	public function getApplyStatus($i = false){
		$status = ['未操作','同意','拒绝'];
		return ($i===false)?$status:$status[$i];
	}

	public function getGoodsNum(){
		$info_id = $this->getProInfoId();
		$model = $this->getModel();
		$sql = "SELECT `num` FROM `product_info` WHERE `id`='".$info_id."'";
		return $model->db()->getOne($sql);

	}

	/**
	 * 获取布产ID
	 */
	public function getProInfoId(){
		$sql = "SELECT `bc_id` FROM `product_goods_rel` WHERE `goods_id` = '".$this->_detail_id."' AND `status` = '0'";
		$model = $this->getModel();
		$res = $model->db()->getOne($sql);
		return $res;
	}

	/**
	 * 获取布产属性
	 */
	public function get_attr($g_id){
		$sql = "SELECT `code`,`name`,`value` FROM `product_info_attr` WHERE `g_id` = '".$g_id."'";
		$model = $this->getModel();
		$res = $model->db()->getAll($sql);
		return $res;
	}


}
?>