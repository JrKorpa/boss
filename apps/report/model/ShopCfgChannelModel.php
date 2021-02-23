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
class ShopCfgChannelModel extends Model
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
		"shop_address"=>"体验店地址",
		"shop_phone"=>"电话",
		"shop_time"=>"营业时间",
		"shop_traffic"=>"交通情况",
		"shop_dec"=>"体验店描述",
		"second_url"=>"体验店二级域名",
		"order"=>"排序",
		"create_user"=>"创建人",
		"create_time"=>"创建时间",
		"is_delete"=>"是否有效 0有效1无效");
		parent::__construct($id,$strConn);
	}

	
	
	//获取所有体验店的信息
	//渠道里面类型为体验店的
	function getallshop($where=[])
	{
		$sql = 'SELECT sc.id,s.shop_type,s.shop_name,s.start_shop_time FROM `sales_channels` as sc inner join';
		$sql .= ' shop_cfg as s on sc.channel_own_id = s.id  WHERE sc.channel_type = 2 and s.is_delete=0';
		if (!empty($where) && isset($where['shop_type'])) {
			$sql .= ' and s.shop_type ='.$where['shop_type'];
		}
		return $this->db()->getAll($sql);
	}
	
	//获取所有体验店的信息
	//渠道里面类型为体验店的
	function getallshop_name()
	{
		$sql = 'SELECT sc.id,s.shop_type,s.shop_name,sc.channel_name FROM `sales_channels` as sc inner join';
		$sql .= ' shop_cfg as s on sc.channel_own_id = s.id  WHERE sc.channel_type = 2 and s.is_delete=0';
		return $this->db()->getAll($sql);
	}

	//根据体验店的id获取渠道的ID
	function getchannelids($shopid)
	{
		$sql = "select id from sales_channels where channel_own_id= $shopid";
		return $this->db()->getAll($sql);
	}
	
	//获取所有体验店的信息
	//渠道里面类型为体验店的
	function getallshopqita()
	{
		$sql = 'SELECT sc.id,sc.channel_name,s.shop_type,s.shop_name FROM `sales_channels` as sc inner join';
		$sql .= ' shop_cfg as s on sc.channel_own_id = s.id  WHERE sc.channel_type = 2 and s.is_delete=0';
		return $this->db()->getAll($sql);
	}	

	//获取 体验店的基本信息
	public function shopname($id){
		$sql ="SELECT channel_name  FROM sales_channels where id=".$id;
		return $this->db()->getRow($sql);
	}
	
	//为了绩效统计的
	function pageListreport($where,$page,$pageSize=10,$useCache=false)
	{
		$sql = 'select * from app_bespoke_info where ';
		$str = '';
		//特色处理是否已经删除
		$isdelete = isset($where['is_delete']) ? $where['is_delete'] : '';
		if($isdelete >=0)
		{
			$sql .= " is_delete = $isdelete and " ;
			unset($where['is_delete']);
		}
		//过滤没有任何值的
		$array = array_filter($where);
		//特殊处理时间
		$begin_time = isset($array['begintime']) ? $array['begintime'] : '';
		$end_time = isset($array['endtime']) ? $array['endtime'] : '';
		if(!empty($begin_time))
		{
			$str .=" create_time > '".$begin_time."' and ";
			unset($array['begintime']);
		}
		if(!empty($end_time))
		{
			$str .=" create_time < '".$end_time."' and ";
			unset($array['endtime']);
		}
		
		//开始拼接sql
		if(!empty($array))
		{
			foreach($array as $k=>$v)
			{
				$str .= $k.'='.$v.' and ';
			}
		}
		$sql .=$str .' 1 order by bespoke_id asc';
		echo $sql;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	
}

?>