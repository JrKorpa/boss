<?php
/**
 *  -------------------------------------------------
 *   @file		: JxsAreaScopeView.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-07-17 01:51:50
 *   @update	:
 *  -------------------------------------------------
 */
class JxsAreaScopeView extends View
{
	protected $_id;
	protected $_jxs_id;
	protected $_country_id;
	protected $_province_id;
	protected $_city_id;
	protected $_region_id;
	protected $_create_time;
	protected $_create_user;


	public function get_id(){return $this->_id;}
	public function get_jxs_id(){return $this->_jxs_id;}
	public function get_country_id(){return $this->_country_id;}
	public function get_province_id(){return $this->_province_id;}
	public function get_city_id(){return $this->_city_id;}
	public function get_region_id(){return $this->_region_id;}
	public function get_create_time(){return $this->_create_time;}
	public function get_create_use(){return $this->_create_user;}

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