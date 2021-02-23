<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductInfomodel.class.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		:
 *   @update	:
 *	 @description:供应商管理相关
 *  -------------------------------------------------
 */
 include_once 'model/CommonModel.class.php';
 class ProductInfoModel extends CommonModel
 {
	public function __construct($id = null,$strConn='')
	{
		parent::__construct($id,$strConn);
    }
	//获取供应商列表ARRAY('供应商ID'=>'供应商名称')
	function getProcessorArr($where=array())
	{
		$row=$this->getProcessorList($where);
		$arr=array();
		if($row)
		{
			foreach($row as $v)
			{
				$arr[$v['id']]=$v['name'];
			}
		}
		return $arr;

	}
	function getProcessorList($where=array())
	{
		$sql="select main.id,main.name from app_processor_info as main";
		$str="";
		if(isset($where['status']) and $where['status']!=='')
		{
			$str.=" and main.status={$where['status']}";
		}
		if(isset($where['open']) and $where['open']!=='')
		{
			$str.=" and main.is_open={$where['open']}";
		}
		if($str)
		{
			$str=ltrim($str,' and');
			$sql.=" where ".$str;
		}
		return $row=$this->db()->getAll($sql);

	}
	//工厂操作数据字典
	function getFactoryOpraList()
	{
		$sql = "SELECT dict_value FROM factory_opra_dict where status=1";
		$row=$this->db()->getAll($sql);
		$arr=array();
		if(!empty($row))
		{
			foreach($row as $r)
			{
				$arr[]=$r['dict_value'];
			}
		}
		return $arr;
	 }
	/*
	*根据布产ID取布产信息
	*BC_ID 布产编号
	*/
	function getProductInfo($bc_id)
	{
		$sql = "SELECT bc_sn,consignee,goods_name,style_sn,num,prc_id,status,buchan_fac_opra,id,p_sn,from_type,info,xiangqian from product_info where bc_sn='{$bc_id}'";
		$row=$this->db()->getRow($sql);
		return $row;
	}
	/*
	*取当前布产单工厂最新操作状态
	*BC_ID 布产编号
	*/
	function getFactioryOpraStatusById($id)
	{
		if(empty($id))
		{
			return false;
		}
		$sql = "select opra_action from product_factory_opra where bc_id={$id} order by id desc limit 0,1";
		return $this->db()->getOne($sql);
	}
	 //取工厂操作是否第一次等钻操作
	 function checkWaitDiamond($bc_id,$opra_id)
	 {
		$sql = "select count(id) as num from product_factory_opra where bc_id={$bc_id} and opra_action={$opra_id}";
		return $this->db()->getOne($sql);
	 }
	/*
	*取加时信息
	*$id 工厂ID
	*/
	function getDelayTime($id,$order_type=1)
	{
		$sql = "select pw_id,processor_id,normal_day,wait_dia,behind_wait_dia,ykqbzq,order_problem,is_rest from `app_processor_worktime` where `processor_id`=$id and order_type={$order_type} order by pw_id desc";
		$row=$this->db()->getRow($sql);
		$arr=array();
		if ($row)
		{
			$arr['wait_dia']= $row['wait_dia']?$row['wait_dia']:25;
			$arr['behind_wait_dia']= $row['behind_wait_dia']?$row['behind_wait_dia']:5;
			$arr['is_rest']= $row['is_rest']?$row['is_rest']:1;
			
		}
		return $arr;
	}
	/*
	*获取工厂的布产信息
	*$id 工厂ID
	*/
	 function getList($fid=0,$page,$pagesize)
	 {
		$page=$page<1?1:$page;
		$pagesize=$pagesize<1?10:$pagesize;
		$start=($page-1)*$pagesize;
		$sql="select bc_sn as rec_id,p_sn as order_sn,consignee,style_sn,goods_name from product_info where prc_id={$fid} and (status=4 or status=7) and buchan_fac_opra=2 order by id desc limit {$start},{$pagesize}";
		$row=$this->db()->getAll($sql);
		return $row;
	 }
	 function getProductCount($fid=0)
	 {
		$sql="select count(id) as num from product_info where prc_id={$fid} and (status=4 or status=7) and buchan_fac_opra=2";
		$num=$this->db()->getOne($sql);
		return $num;
	 }
	 function getAttr($bc_id)
	 {
		$sql = "select code,value,name from product_info_attr where g_id='{$bc_id}'";//edit by zhangruiying
		$tmp = $this->db()->getAll($sql);
		$arr=array();
		if($tmp)
		{
			foreach ($tmp as $v)
			{
				$arr[$v['code']] = $v['value'];
			}
		}
		return $arr;
	 }
	 
     function js_normal_time($normal_day,$is_rest,$esmt_time='')
	 {
		$ziranri = 1;
		$gongzuori = 0;
		if(empty($esmt_time)){$esmt_time = time();}
		$normal_time = $esmt_time;
		while($gongzuori < $normal_day){
			$normal_time = $esmt_time+3600*24*$ziranri;//流水日期
			$ziranri++;
			if(date("w",$normal_time) == 6){
				if($is_rest ==2){//单休
					$gongzuori++;
				}elseif($is_rest ==1){//不休
					$gongzuori++;
				}
			}elseif(date("w",$normal_time) == 0){
				if($is_rest ==1){//双休
					$gongzuori++;
				}
			}else{
				$gongzuori++;
			}
		}
		$time = date("Y-m-d",$normal_time);
		return $time;
	 }

	 function skipTakeBC($fid=0,$skip_num,$take_num)
	 {
		$sql="select bc_sn as rec_id,p_sn as order_sn,consignee,style_sn,goods_name from product_info where prc_id={$fid} and (status=4 or status=7) and buchan_fac_opra=2 order by id desc limit {$skip_num},{$take_num}";
		$row=$this->db()->getAll($sql);
		return $row;
	 }




 }

?>