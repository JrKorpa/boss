<?php
/**
 *  -------------------------------------------------
 *   @file		: AppMemberAddressModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 16:08:15
 *   @update	:
 *  -------------------------------------------------
 */
class AppMemberAddressModel extends Model
{
    public $_prefix = 'mem_address';
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_member_address';
        $this->_dataObject = array("mem_address_id"=>"地址id",
"member_id"=>"会员id",
"customer"=>"顾客名",
"mobile"=>"手机号",
"mem_country_id"=>"会员国家",
"mem_province_id"=>"会员省",
"mem_city_id"=>"会员城市",
"mem_district_id"=>"会员区",
"mem_address"=>"会员详细地址",
"mem_is_def"=>"是否默认 1是默认0不是默认");
		parent::__construct($id,$strConn);
	}
	/**
	 *	pageList，分页列表
	 *
	 *	@url MessageController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
                if(isset($where['member_id']) && !empty($where['member_id'])){
                    $sql .=" AND member_id = '{$where['member_id']}' ";
                }
		$sql .= " ORDER BY mem_address_id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

    /**
     * 取国家
     * @return type
     */
    public function getCountryOption() {
        $model = new RegionModel(1);
        return $model->getRegion(0);
    }

    /**
     * 取单个
     * @return type
     */
    public function getRegionOption($region_ids) {
        $model = new RegionModel(1);
        return $model->getRegionList($region_ids);
    }

    /**
     * 更新
     * @return type
     */
    public function updateMemberAddress($member_id) {
        $sql="UPDATE `".$this->table()."` SET `mem_is_def`=0 WHERE `member_id`=".$member_id;
        return $this->db()->query($sql);
    }

    /**
     * 更新
     * @return type
     */
    public function getRow($member_id) {
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
        if(isset($member_id) && !empty($member_id)){
            $sql .=" AND `member_id`=".$member_id;
        }
		$data = $this->db()->getRow($sql);
		return $data;
    }
}

?>