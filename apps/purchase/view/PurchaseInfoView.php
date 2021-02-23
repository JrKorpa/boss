<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-08 14:13:19
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseInfoView extends View
{
	protected $_id;
	protected $_p_sn;
	protected $_t_id;
	protected $_is_tofactory;
	protected $_is_style;
	protected $_p_sum;
	protected $_purchase_fee;
	protected $_put_in_type;
//	protected $_apply_uname;
	protected $_make_uname;
	protected $_make_time;
	protected $_check_uname;
	protected $_check_time;
	protected $_p_status;
	protected $_p_info;
	protected $_prc_name;
    protected $_channel_ids;
    protected $_is_zhanyong;



	public function get_id(){return $this->_id;}
	public function get_p_sn(){return $this->_p_sn;}
	public function get_t_id(){return $this->_t_id;}
	public function get_is_tofactory(){return $this->_is_tofactory;}
	public function get_is_style(){return $this->_is_style;}
	public function get_p_sum(){return $this->_p_sum;}
	public function get_purchase_fee(){return $this->_purchase_fee;}
	public function get_put_in_type(){return $this->_put_in_type;}
//	public function get_apply_uname(){return $this->_apply_uname;}
	public function get_make_uname(){return $this->_make_uname;}
	public function get_make_time(){return $this->_make_time;}
	public function get_check_uname(){return $this->_check_uname;}
	public function get_check_time(){return $this->_check_time;}
	public function get_p_status(){return $this->_p_status;}
	public function get_p_info(){return $this->_p_info;}
	public function get_prc_name(){return $this->_prc_name;}
    public function get_channel_ids(){return $this->_channel_ids;}
    public function get_is_zhanyong(){return $this->_is_zhanyong;}

	public function get_t_name($t_id)
	{
		$model = new PurchaseTypeModel(23);
		return $model->getTname($t_id);
	}

    public function get_dep_name()
    {
        $ids = $this->get_channel_ids();
        $model = new SalesChannelsModel(1);
        $name = '';
        if($ids){
            $idsArr =array_filter(explode(',', $ids));
            foreach ($idsArr as $val) {
                $name.= $model->getNameByid($val).",";
            }
        }
        return trim($name,',');
        
    }

}
?>