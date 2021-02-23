<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderAddressModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-07-20 17:27:38
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderAddressModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_order_address';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"id",
"order_id"=>"订单id",
"consignee"=>"收货人",
"distribution_type"=>"配送方式",
"express_id"=>"快递公司ID",
"freight_no"=>"快递号",
"country_id"=>"国家id",
"province_id"=>"省份id",
"city_id"=>"城市id",
"regional_id"=>"区域id",
"shop_type"=>"体验店类型",
"shop_name"=>"体验店名称",
"address"=>"详细地址",
"tel"=>"电话",
"email"=>"email",
"zipcode"=>"邮编",
"goods_id"=>"商品id");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppOrderAddressController/search
	 */
		function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT addr.id,bi.order_sn,bi.create_time,dep.channel_name,addr.address FROM app_order.base_order_info bi 
		inner join app_order.app_order_address addr on addr.order_id = bi.id left join cuteframe.sales_channels dep on dep.id = bi.department_id";
		$str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " WHERE bi.department_id in (1,2,3,13,52,71) and bi.order_status = 2
		and (addr.country_id is null or addr.country_id = 0 or addr.province_id is null or addr.province_id = 0 or addr.city_id = 0 or addr.city_id is null or addr.regional_id is null or addr.regional_id = 0) ORDER BY addr.id ASC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	function jxspageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
                if(isset($where['id']) && !empty($where['id'])){
                    $sql .=" AND id = '{$where['id']}' ";
                }
		$sql .= " ORDER BY id DESC";
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
}

?>