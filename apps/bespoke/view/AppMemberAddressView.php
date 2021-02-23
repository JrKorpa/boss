<?php
/**
 *  -------------------------------------------------
 *   @file		: AppMemberAddressView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 16:08:15
 *   @update	:
 *  -------------------------------------------------
 */
class AppMemberAddressView extends View
{
	protected $_mem_address_id;
	protected $_member_id;
	protected $_customer;
	protected $_mobile;
	protected $_mem_country_id;
	protected $_mem_province_id;
	protected $_mem_city_id;
	protected $_mem_district_id;
	protected $_mem_address;
	protected $_mem_is_def;


	public function get_mem_address_id(){return $this->_mem_address_id;}
	public function get_member_id(){return $this->_member_id;}
	public function get_customer(){return $this->_customer;}
	public function get_mobile(){return $this->_mobile;}
	public function get_mem_country_id(){return $this->_mem_country_id;}
	public function get_mem_province_id(){return $this->_mem_province_id;}
	public function get_mem_city_id(){return $this->_mem_city_id;}
	public function get_mem_district_id(){return $this->_mem_district_id;}
	public function get_mem_address(){return $this->_mem_address;}
	public function get_mem_is_def(){return $this->_mem_is_def;}

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