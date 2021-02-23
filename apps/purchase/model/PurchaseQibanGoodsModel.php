<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseQibanGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-01 15:22:51
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseQibanGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'purchase_qiban_goods';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"info"=>" ",
"price"=>" ",
"addtime"=>" ",
"order_sn"=>" ",
"opt"=>" ",
"customer"=>" ",
"xiangkou"=>"镶口",
"shoucun"=>"手寸",
"specifi"=>"规格",
"fuzhu"=>"辅助版号",
"qibanfei"=>"起版费",
"jinliao"=>"金料",
"jinse"=>"金色",
"gongyi"=>"表面工艺",
"is_shenhe"=>"是否审核2=未审核 1=已审核",
"is_fukuan"=>"是否付款 2未付款 1=已付款",
"gongchang"=>"工厂",
"kuanhao"=>"款号",
"zhengshu"=>"证书号",
"xuqiu"=>"产品需求",
"pic"=>"起版图片",
"status"=>"状态",
"kuan_type"=>"款式类型",
"zhushi_num"=>"主石粒数",
"cert"=>"证书类型"
        );
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url PurchaseQibanGoodsController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
        if(isset($where['hidden']) && $where['hidden'] != ''){
            $str .= " hidden = ".$where['hidden']." AND ";
        }
		if($where['order_sn'] != "")
		{
			$str .= "`order_sn` like \"%".addslashes($where['order_sn'])."%\" AND ";
		}
		if($where['addtime'] != "")
		{
			$str .= "`addtime` like \"%".addslashes($where['addtime'])."%\" AND ";
		}
		if($where['customer'] != "")
		{
			$str .= "`customer` like \"%".addslashes($where['customer'])."%\" AND ";
		}
		if(!empty($where['status']))
		{
			$str .= "`status`='".$where['status']."' AND ";
		}
		if(!empty($where['price_min']))
		{
			$str .= "`price`>='".$where['price_min']."' AND ";
		}
		if(!empty($where['price_max']))
		{
			$str .= "`price`<='".$where['price_max']."' AND ";
		}
		if(!empty($where['xiangkou_min']))
		{
			$str .= "`xiangkou`>=".$where['xiangkou_min']." AND ";
		}
		if(!empty($where['xiangkou_max']))
		{
			$str .= "`xiangkou`<=".$where['xiangkou_max']." AND ";
		}
		if(!empty($where['shoucun_min']))
		{
			$str .= "`shoucun`>=".$where['shoucun_min']." AND ";
		}
		if(!empty($where['shoucun_max']))
		{
			$str .= "`shoucun`<=".$where['shoucun_max']." AND ";
		}
		if(!empty($where['fuzhu']))
		{
			$str .= "`fuzhu`='".$where['fuzhu']."' AND ";
		}
		if(!empty($where['gongchang']))
		{
			$str .= "`gongchang`='".$where['gongchang']."' AND ";
		}
		if(!empty($where['kuanhao']))
		{
			$str .= "`kuanhao`='".$where['kuanhao']."' AND ";
		}
		if(!empty($where['zhengshu']))
		{
			$str .= "`zhengshu`='".$where['zhengshu']."' AND ";
		}
		if(!empty($where['xuqiu']))
		{
			$str .= "`xuqiu`='".$where['xuqiu']."' AND ";
		}
		if(!empty($where['jinliao']))
		{
			$str .= "`jinliao`='".$where['jinliao']."' AND ";
		}
		if(!empty($where['jinse']))
		{
			$str .= "`jinse`='".$where['jinse']."' AND ";
		}
		if(!empty($where['gongyi']))
		{
			$str .= "`gongyi`='".$where['gongyi']."' AND ";
		}
		if(!empty($where['opt']))
		{
			$str .= "`opt`='".$where['opt']."' AND ";
		}
		if(!empty($where['kuan_type']))
		{
			$str .= "`kuan_type`=".$where['kuan_type']." AND ";
		}
        if(!empty($where['qiban_type']))
        {
            $str .= "`qiban_type`='".$where['qiban_type']."' AND ";
        }
        if(isset($where['start_time']) && $where['start_time'] != "")
        {
            $str .= "`addtime` >= ".strtotime($where['start_time']." 00:00:00")." AND ";
        }
        if(isset($where['end_time']) && $where['end_time'] != "")
        {
            $str .= "`addtime` <= ".strtotime($where['end_time']." 23:59:59")." AND ";
        }
        if(!empty($where['info']))
        {
            $str .= "`info`='".$where['info']."' AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";//echo $sql;exit;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	

	
	
}?>