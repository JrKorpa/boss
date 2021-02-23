<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderFavorableModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-17 17:11:13
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderFavorableModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_order_favorable';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"自增id",
"order_sn"=>"订单号",
"order_id"=>"订单id",
"detail_id"=>"商品自增id",
"goods_id"=>"商品货号",
"goods_sn"=>"款号",
"goods_name"=>"商品名称",
"favorable_price"=>"优惠价格（有符号）",
"create_time"=>"创建时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppOrderFavorableController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = " `check_user_id`={$_SESSION['userId']} AND ";
		if($where['order_sn'] != "")
		{
			$str .= " `order_sn` like \"%".addslashes($where['order_sn'])."%\" AND ";
		}
		if(!empty($where['goods_id']))
		{
			$str .= " `goods_id`='".$where['goods_id']."' AND ";
		}
		if(!empty($where['check_status']))
		{
			$str .= " `check_status`=".$where['check_status']." AND ";
		}
        if(!empty($where['consignee']))
        {
            $str .= " `consignee`='".$where['consignee']."' AND ";
        }
        if(!empty($where['create_user']))
        {
            $str .= " `create_user`='".$where['create_user']."' AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
        //echo '<pre>';
        //print_r($sql);die;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	
	
	function checkPermissions(){
		$SalesChannelsModel = new SalesChannelsModel(1);
		$shopArr=$SalesChannelsModel->getShopCid();
		$userName = $_SESSION['userName'];
		$is_check=1;
		foreach ($shopArr as $k=>$v){
			//取得当前用户id,userName
			//判断当前用户是否为店长
			$dp_leader_name = explode(',', $v['dp_leader_name']);
	
			//销售顾问名称
			$dp_people_name = explode(',', $v['dp_people_name']);
			if(!in_array($userName, $dp_leader_name)&&in_array($userName, $dp_people_name)){
				$is_check=0;
			}
		}
		 
		return $is_check;
	}
}

?>