<?php

/**
 *  -------------------------------------------------
 *   @file		: BaseStyleInfoView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 13:40:44
 *   @update	:
 *  -------------------------------------------------
 */
class BaseStyleInfoView extends View {

    protected $_style_id;
    protected $_style_sn;
    protected $_style_name;
    protected $_product_type;
    protected $_style_type;
    protected $_sell_type;
    protected $_create_time;
    protected $_modify_time;
    protected $_check_time;
    protected $_cancel_time;
    protected $_check_status;
    protected $_is_sales;
    protected $_is_made;
    protected $_dismantle_status;
    protected $_style_status;
    protected $_style_remark;
    protected $_dapei_goods_sn;
    protected $_changbei_sn;
    protected $_is_zp;
    protected $_style_sex;
    protected $_xilie;
    protected $_market_xifen;
    protected $_bang_type;
    protected $_is_xz;
    protected $_sale_way;
    protected $_zp_price;
    protected $_is_allow_favorable;
    protected $_is_gold;
    protected $_is_support_style;
    protected $_company_type_id;
    protected $_jiajialv;
    protected $_is_wukong;
    protected $_goods_content;
    
    public function get_goods_content(){
        return $this->_goods_content;
    }
    public function get_is_wukong(){
        return $this->_is_wukong;
    }
    public function get_company_type_id(){
        return $this->_company_type_id;
    }
    public function get_style_id() {
        return $this->_style_id;
    }

    public function get_style_sn() {
        return $this->_style_sn;
    }

    public function get_style_name() {
        return $this->_style_name;
    }

    public function get_product_type() {
        return $this->_product_type;
    }

    public function get_style_type() {
        return $this->_style_type;
    }
	
	public function get_sell_type() {
        return $this->_sell_type;
    }
	
    public function get_create_time() {
        return $this->_create_time;
    }

    public function get_modify_time() {
        return $this->_modify_time;
    }

    public function get_check_time() {
        return $this->_check_time;
    }

    public function get_cancel_time() {
        return $this->_cancel_time;
    }

    public function get_check_status() {
        return $this->_check_status;
    }

    public function get_is_sales() {
        return $this->_is_sales?1:0;
    }

    public function get_is_made() {
        return $this->_is_made?1:0;
    }

    public function get_dismantle_status() {
        return $this->_dismantle_status;
    }

    public function get_style_status() {
        return $this->_style_status;
    }

    public function get_style_remark() {
        return $this->_style_remark;
    }

    public function get_dapei_goods_sn() {
        return $this->_dapei_goods_sn;
    }

    public function get_changbei_sn() {
        return $this->_changbei_sn;
    }
    
    public function get_is_zp() {
        return $this->_is_zp;
    }

    public function get_style_sex() {
        return $this->_style_sex;
    }

    public function get_xilie() {
        return $this->_xilie;
    }

    public function get_market_xifen() {
        return $this->_market_xifen;
    }
    public function get_is_xz() {
        return $this->_is_xz;
    }
    public function get_sale_way() {
        return $this->_sale_way;
    }
    public function get_zp_price() {
        return $this->_zp_price;
    }
    public function get_jiajialv(){
        return $this->_jiajialv;
    }

    public function get_bang_type() {
        return $this->_bang_type?$this->_bang_type:1;
    }
    public function get_is_allow_favorable(){
        return $this->_is_allow_favorable;
    }
    public function get_is_gold(){
        return $this->_is_gold;
    }
    public function get_is_support_style(){
        return $this->_is_support_style;
    }

    /**
     * 获取产品线列表
     * @return type
     */
    public function getProductTypeList() {
        $model = new BaseStyleInfoModel(11);
        return $model->getProductTypeList();
    }

    /**
     * 获取款式分类列表
     * @return type
     */
    public function getStyleTypeList() {
        $model = new BaseStyleInfoModel(11);
        return $model->getStyleTypeList();
    }
	
	/**
	*获取畅销款
	*/
	public function getSellTypeList() {
        $model = new BaseStyleInfoModel(11);
        return $model->getSellTypeList();
    }
    
    public function getMarketXifenList(){
        return $this->_model->getMarketXifenList();
    }

    //获取公司类型名称
    public function getCompanyTypeName($company_type_id)
    {
        $company_type = array(1=>'直营店',2=>'协作经销商',3=>'经销商');
        $company_type_name = '';
        if(!empty($company_type_id)){
            $arr = explode(',', $company_type_id);
            foreach ($arr as $key => $value) {
                if($value) $company_type_name .= $company_type[$value]."、";
            }
        }
        return rtrim($company_type_name,"、");
    }

}

?>