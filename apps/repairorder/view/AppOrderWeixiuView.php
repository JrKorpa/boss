<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderWeixiuView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 17:23:54
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderWeixiuView extends View
{
	protected $_id;
	protected $_order_id;
	protected $_order_sn;
	protected $_rec_id;
	protected $_re_type;
	protected $_goods_id;
	protected $_consignee;
	protected $_repair_act;
	protected $_repair_man;
	protected $_repair_factory;
	protected $_repair_make_order;
	protected $_remark;
	protected $_status;
	protected $_order_time;
	protected $_confirm_time;
	protected $_factory_time;
	protected $_end_time;
	protected $_re_end_time;
	protected $_receiving_time;
	protected $_frequency;
	protected $_after_sale;
	protected $_change_sn;
	
	protected $_qc_status;
	protected $_qc_times;
	protected $_qc_nopass_dt;

    protected $_order_class;

    protected $_weixiu_price;

	public function get_qc_status(){return $this->_qc_status;}
	public function get_qc_times(){return empty($this->_qc_times)?0:$this->_qc_times;}
	public function get_qc_nopass_dt(){return $this->_qc_nopass_dt;}
	
	public function get_id(){return $this->_id;}
	public function get_order_id(){return $this->_order_id;}
	public function get_order_sn(){return $this->_order_sn;}
	public function get_rec_id(){return $this->_rec_id;}
	public function get_re_type(){return $this->_re_type;}
	public function get_goods_id(){return $this->_goods_id;}
	public function get_consignee(){return $this->_consignee;}
	public function get_repair_act()
	{
		return $this->_repair_act;
	}
	public function get_repair_man(){return $this->_repair_man;}
	public function get_repair_factory(){return $this->_repair_factory;}
	public function get_repair_make_order(){return $this->_repair_make_order;}
	public function get_remark(){return $this->_remark;}
	public function get_status(){return $this->_status;}
	public function get_order_time(){return $this->_order_time;}
	public function get_confirm_time(){return $this->_confirm_time;}
	public function get_factory_time(){return $this->_factory_time;}
	public function get_end_time(){return $this->_end_time;}
	public function get_re_end_time(){return $this->_re_end_time;}
	public function get_receiving_time(){return $this->_receiving_time;}
	public function get_frequency(){return $this->_frequency;}
	public function get_after_sale(){return $this->_after_sale;}
	public function get_change_sn(){return $this->_change_sn;}
    public function get_order_class(){return $this->_order_class;}
	public function get_user_name($id) 
	{
		$model = new UserModel(1);
		return $model->getAccount($id);
	}
    public function get_weixiu_price(){return $this->_weixiu_price;}
	//根据id获取供应商名称
	public function get_pro_name($id)
	{
		$model = new ApiDataModel();
		$arr = array('id'=>$id);
	    $res = $model->GetProcessorName($arr);
		return $res;
	}
	//根据维修内容正路数据显示
	public function get_repair_act_con($str)
	{
		$arr = explode(',',$str);
		$dic = new DictView(new DictModel(1));
		$str = array();
		foreach ($arr as $key=>$val)
		{
			$str[] = $dic->getEnum('weixiu.action',$val);
		}
		return join(',',$str);
	}

}
?>