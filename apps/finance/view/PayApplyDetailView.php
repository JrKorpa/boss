<?php
/**
 *  -------------------------------------------------
 *   @file		: PayApplyDetailView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 18:01:26
 *   @update	:
 *  -------------------------------------------------
 */
class PayApplyDetailView extends View
{
	protected $_detail_id;
	protected $_apply_id;
	protected $_replytime;
	protected $_external_sn;
	protected $_kela_sn;
	protected $_external_total;
	protected $_pay_xj;
	protected $_pay_jf;
	protected $_pay_pt_yhq;
	protected $_pay_kela_yhq;
	protected $_f_koudian;
	protected $_f_yongjin;
	protected $_f_jingdong;
	protected $_f_yunfei;
	protected $_f_peifu;
	protected $_f_chajia;
	protected $_f_youhui;
	protected $_f_weiyue;
	protected $_f_qita;
	protected $_sy_fanyou;
	protected $_sy_qita;
	protected $_total;
	protected $_reoverrule_reason;


	public function get_detail_id(){return $this->_detail_id;}
	public function get_apply_id(){return $this->_apply_id;}
	public function get_replytime(){return $this->_replytime;}
	public function get_external_sn(){return $this->_external_sn;}
	public function get_kela_sn(){return $this->_kela_sn;}
	public function get_external_total(){return $this->_external_total;}
	public function get_pay_xj(){return $this->_pay_xj;}
	public function get_pay_jf(){return $this->_pay_jf;}
	public function get_pay_pt_yhq(){return $this->_pay_pt_yhq;}
	public function get_pay_kela_yhq(){return $this->_pay_kela_yhq;}
	public function get_f_koudian(){return $this->_f_koudian;}
	public function get_f_yongjin(){return $this->_f_yongjin;}
	public function get_f_jingdong(){return $this->_f_jingdong;}
	public function get_f_yunfei(){return $this->_f_yunfei;}
	public function get_f_peifu(){return $this->_f_peifu;}
	public function get_f_chajia(){return $this->_f_chajia;}
	public function get_f_youhui(){return $this->_f_youhui;}
	public function get_f_weiyue(){return $this->_f_weiyue;}
	public function get_f_qita(){return $this->_f_qita;}
	public function get_sy_fanyou(){return $this->_sy_fanyou;}
	public function get_sy_qita(){return $this->_sy_qita;}
	public function get_total(){return $this->_total;}
	public function get_reoverrule_reason(){return $this->_reoverrule_reason;}
    
    public function adList() {
        $model = new EcsAdModel(29);
        return $model->getAdList();
    }

    public function companyList() {
        $model = new EcsAdModel(29);
        return $model->getCompanyList();
    }
    
    public function yearList() {
        $model = new EcsAdModel(29);
        $jizhang_list = $model->getJiezhangList();
        $new_year = array();
		foreach($jizhang_list as $k=>$v)
		{
			$new_year[$k] = $v['year'];
		}
        return $new_year;
    }

}
?>