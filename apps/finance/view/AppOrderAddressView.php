<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderAddressView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-07-20 17:27:38
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderAddressView extends View
{
	protected $_id;
	protected $_order_id;
	protected $_consignee;
	protected $_distribution_type;
	protected $_express_id;
	protected $_freight_no;
	protected $_country_id;
	protected $_province_id;
	protected $_city_id;
	protected $_regional_id;
	protected $_shop_type;
	protected $_shop_name;
	protected $_address;
	protected $_tel;
	protected $_email;
	protected $_zipcode;
	protected $_goods_id;


	public function get_id(){return $this->_id;}
	public function get_order_id(){return $this->_order_id;}
	public function get_consignee(){return $this->_consignee;}
	public function get_distribution_type(){return $this->_distribution_type;}
	public function get_express_id(){return $this->_express_id;}
	public function get_freight_no(){return $this->_freight_no;}
	public function get_country_id(){return $this->_country_id;}
	public function get_province_id(){return $this->_province_id;}
	public function get_city_id(){return $this->_city_id;}
	public function get_regional_id(){return $this->_regional_id;}
	public function get_shop_type(){return $this->_shop_type;}
	public function get_shop_name(){return $this->_shop_name;}
	public function get_address(){return $this->_address;}
	public function get_tel(){return $this->_tel;}
	public function get_email(){return $this->_email;}
	public function get_zipcode(){return $this->_zipcode;}
	public function get_goods_id(){return $this->_goods_id;}
	  /**
     * 取国家
     * @return type
     */
    public function getCountryOption() {
        $model = new RegionModel(1);
        return $model->getRegionType(0);
    }

    /**
     * 获取省市区联动数据
     * @return type
     */
    public function getProvinceOption() {
        $model = new RegionModel(1);
        return $model->getRegionType(1);
    }

    /**
     * 获取会员城市联动数据
     * @return type
     */
    public function getCityOption() {
        $model = new RegionModel(1);
        return $model->getRegionType(2);
    }

    /**
     * 获取会员区联动数据
     * @return type
     */
    public function getDistrictOption() {
        $model = new RegionModel(1);
        return $model->getRegionType(3);
    }

}
?>