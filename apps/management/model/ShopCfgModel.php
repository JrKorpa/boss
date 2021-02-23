<?php
/**
 *  -------------------------------------------------
 *   @file		: ShopCfgModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-30 10:35:37
 *   @update	:
 *  -------------------------------------------------
 */
class ShopCfgModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'shop_cfg';
        $this->_dataObject = array("id"=>"主键",
"shop_name"=>"名称",
"short_name"=>"简称(拼音)",
"shop_type"=>"门店类型",
"country_id"=>"国家",
"province_id"=>"省",
"city_id"=> "市",
"regional_id"=>"区",
"area"=>"区域",
"shop_address"=>"体验店地址",
"shop_phone"=>"电话",
"shop_time"=>"营业时间",
"start_shop_time"=>"开店时间",
"shop_traffic"=>"交通情况",
"shop_dec"=>"体验店描述",
"second_url"=>"体验店二级域名",
"shop_status"=>"店面状态：0暂停营业，1正常营业，2关闭",
"baidu_maps"=>"百度地图坐标",
"shopowner"=>"店长",
"shopowner_tel"=>"店长联系电话",
"shopowner_mail"=>"店长邮箱",
"order"=>"排序",
"create_user"=>"创建人",
"create_time"=>"创建时间",
"is_delete"=>"是否有效 0有效1无效");
		parent::__construct($id,$strConn);
	}

	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//$sql = "SELECT `id`,`shop_name`,`short_name`,`shop_type`,`country_id`,`province_id`,`city_id`,`regional_id`,`shop_address`,`shop_phone`,`shop_time`,`shop_traffic`,`shop_dec`,`second_url`,`order`,`create_user`,`create_time`, `is_delete`,`oldsys_id` FROM `shop_cfg` as `m` ";
		$sql="select distinct m.*,c.company_name,c.sd_company_id,c2.company_name as sd_company_name from shop_cfg m left join sales_channels s on m.id=s.channel_own_id and s.channel_type=2 and s.is_deleted=0 left join company  c on s.company_id=c.id 
left  join company c2 on c.sd_company_id=c2.id ";
		$str = '';
		if($where['shop_name'] != "")
		{
			$str .= "`m`.`shop_name` like \"%".addslashes($where['shop_name'])."%\" AND ";
		}
		if($where['shop_tel'] != "")
		{
			$str .= "(`m`.`shop_responsible_tel` = '".$where['shop_tel']."' or `m`.`shop_phone` = '".$where['shop_tel']."' or `m`.`shopowner_tel` = '".$where['shop_tel']."') AND ";
		}
        if($where['shop_type'] != "")
        {
            $str .= "`m`.`shop_type` = ".$where['shop_type']." AND ";
        }
        if($where['shop_address'] != "")
        {
            $str .= "`m`.`shop_address` like \"%".addslashes($where['shop_address'])."%\" AND ";
        }
		if(isset($where['is_delete']))
		{
			$str .= "`m`.`is_delete` ='".$where['is_delete']."' AND ";
		}

		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}

		$sql .= " ORDER BY m.order DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function move ($id,$up=true)
	{
		$do = $this->getDataObject();
		if(!$do)
		{

			return 4;
		}
		if($up)
		{
			$sql = "SELECT id,`order` FROM `".$this->table()."` WHERE `order`>'".$do['order']."' ORDER BY `order` ASC LIMIT 1";
		}
		else
		{
			$sql = "SELECT id,`order` FROM `".$this->table()."` WHERE `order`<'".$do['order']."' ORDER BY `order` DESC LIMIT 1";
		}
		$destdo = $this->db()->getRow($sql);
		if(!$destdo)
		{
			return 3;
		}

		$sql = "UPDATE `".$this->table()."` SET `order`='".$do['order']."' WHERE `id`='".$destdo['id']."' ";
		$res = $this->db()->query($sql);
		if(!$res)
		{

			return 5;
		}
		$sql = "UPDATE `".$this->table()."` SET `order`='".$destdo['order']."' WHERE `id`='".$id."' ";
		$res = $this->db()->query($sql);
		if($res)
		{
			return 1;
		}
		else
		{
			return 6;
		}
	}
	//获取 体验店的基本信息
	public function getUinfo($id){
		$sql ="SELECT u.real_name,s.* FROM shop_cfg as s LEFT JOIN user as u ON s.create_user=u.id WHERE s.id=".$id." ORDER BY s.id";
		return $this->db()->getRow($sql);
	}

    public function getAllShopCfg($where=''){
        $sql ="select `id`,`shop_name`,`shop_type` from .".$this->table()." WHERE is_delete=0";
        if(!empty($where['shop_type'])){
            $sql.=" AND  shop_type=".$where['shop_type'];
        }
        return $this->db()->getAll($sql);
    }

    public function getShopInfoByid($id){
        $sql ="select * from .".$this->table()." where `is_delete`=0 and `id`=$id";
        return $this->db()->getRow($sql);
    }
    public function getShopInfoByRid($id)
    {
         $sql ="select * from .".$this->table()." where `regional_id`=$id";
        return $this->db()->getRow($sql);
    }


	//体验店打印获取列表
	/********** update by liulinyan 2015-07-31 for get accepterinfo **********/
	function pageListTyd ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT `m`.`id`,`shop_name`,`short_name`,`shop_type`,`country_id`,`province_id`,`city_id`,`regional_id`,`shop_address`,`shop_phone`,`shop_time`,`shop_traffic`,`shop_dec`,`second_url`,`order`,`create_user`,`create_time`,`is_delete`,`oldsys_id`,`accepter_name`,`accepter_mobile`,`accepter_company`,`accepter_address` FROM `shop_cfg` as `m` left join `shop_cfg_accepter` on `m`.`id` = `shop_cfg_accepter`.`id` ";
		$str = '';
		if($where['shop_name'] != "")
		{
			$str .= "`m`.`shop_name` like \"%".addslashes($where['shop_name'])."%\" AND ";
		}

        if($where['shop_type'] != "")
        {
            $str .= "`m`.`shop_type` = ".$where['shop_type']." AND ";
        }
        if(!empty($where['accepter_company'])){
        	$str .= "`shop_cfg_accepter`.`accepter_company` like \"%".addslashes($where['accepter_company'])."%\" AND ";
        } 
		if(isset($where['is_delete']))
		{
			$str .= "`m`.`is_delete` ='".$where['is_delete']."' AND ";
		}

		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY m.order DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	//获取所有体验店的信息
	public function getStoreInfo($has_company){
		$has_company = implode("','",$has_company);
		$sql ="SELECT s.company_id,sc.shop_name,s.is_deleted FROM cuteframe.sales_channels s
                        LEFT JOIN cuteframe.shop_cfg AS sc ON sc.id=s.channel_own_id WHERE s.channel_type=2 AND s.company_id IN('".$has_company."')";
		return $this->db()->getAll($sql);

	}
	
	//获取 所有体验店的基本信息
	public function getUinfos(){
		$sql ="SELECT u.real_name,s.* FROM shop_cfg as s LEFT JOIN user as u ON s.create_user=u.id WHERE s.is_delete = 0 ORDER BY s.order DESC";
		return $this->db()->getAll($sql);
	}
}

?>