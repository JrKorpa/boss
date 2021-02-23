<?php

/**
 * 销售政策一口价商品  MODEL
 *  -------------------------------------------------
 *   @file		: BaseSalepolicyGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-03 18:25:10
 *   @update	:
 *  -------------------------------------------------
 */
class AppYikoujiaGoodsModel extends Model {

    public static $color_arr = array("N","M","L","K-L","K","J-K","J","I-J","I","H-I","H","H+","G-H","G","F-G","F","E-F","E","D-E","D","黄","蓝","粉","橙","绿","红","香槟","格雷恩","紫","混色","蓝紫色","黑","变色","其他","白色","金色");
    public static $clarity_arr = array("不分级","P","P1","I","I1","I2","SI","SI1","SI2","VS","VS1","VS2","VVS","VVS1","VVS2","IF","FL");
    public static $shape_arr = array(1 => '圆形', 2 => '公主方形', 3 => '祖母绿形', 4 => '橄榄形', 5 => '椭圆形', 6 => '水滴形', 7 => '心形', 8 => '坐垫形', 9 => '辐射形', 10 => '方形辐射形', 11 => '方形祖母绿', 12 => '三角形',13=>'戒指托',14=>'异形',15=>'梨形',16=>'阿斯切',17 => '马眼', 18 => '长方形', 19 => '雷迪恩');
    public static $cert_arr = array('HRD-D','GIA','HRD','IGI','DIA','AGL','EGL','NGTC','NGGC','HRD-S');


    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_yikoujia_goods';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => "自增id",
            "goods_id" => "货号",
            "goods_sn" => "款号",
            "goods_name" => "商品名称",
            "small" => "镶口最小值",
            "sbig" => "镶口最大值",
            "caizhi" => "材质",
            "price" => "价格",
            "policy_id" => "销售政策ID",
			"tuo_type" => "金托类型"
            );
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url BaseSalepolicyGoodsController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        $sql = "SELECT a.*,b.policy_name FROM `" . $this->table() . "` a left join base_salepolicy_info b on a.policy_id=b.policy_id where 1=1";
        
        if(!empty($where['policy_id']) && $where['policy_id'] > 0 ){
            $sql .= " AND a.`policy_id`={$where['policy_id']}";
        }
        
		if(!empty($where['goods_id']))
		{
			$sql .= " AND a.`goods_id`='".$where['goods_id']."'";
		}
		if(!empty($where['goods_sn']))
		{
			$sql .= " AND a.`goods_sn`='".$where['goods_sn']."'";
		}
		//镶口是一定会有默认值的
		$sql .= " AND a.small >={$where['small']} ";
		if($where['sbig']==0)
		{
			$sql .= " AND a.sbig<= 100";
		}else{
			$sql .= " AND a.sbig<= {$where['sbig']} ";	
		}
		//暂时留着 怕后面需要
		if(!empty($where['caizhi']))
		{
			$sql .= " AND a.`caizhi`={$where['caizhi']}";
		}
		if($where['is_delete'] != 'all' && $where['is_delete']!='')
		{
			$sql .= " AND a.`is_delete`={$where['is_delete']}";
		}
		
        $sql .= " ORDER BY a.`id` DESC";
		//echo $sql;
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    public  function deleteYikoujiaGoods($ids){
        if(empty($ids)){
            return false;
        }elseif(is_array($ids)){
            $ids = implode(',',$ids);
        }
		$sql = "update ".$this->table()." set is_delete=1 where id in ({$ids})";
        $ret = $this->db()->query($sql); 
        return true;       
    }
	
	public function getyikoujiainfo($where)
	{
		$sql = 'select * from '.$this->table()." where is_delete=0 ";
		
		if(isset($where['id']) && $where['id'] !='')
		{
			$id= $where['id'];
			$sql .=" and id !='{$id}' "; 
		}
		if(isset($where['goods_id']) && $where['goods_id'] !='')
		{
			$gid= $where['goods_id'];
			$sql .=" and goods_id='{$gid}'";
			//return $this->db()->getAll($sql);
		}
		if(isset($where['goods_sn']) && $where['goods_sn'] !='')
		{
			$gsn= $where['goods_sn'];
			$sql .=" and goods_sn='{$gsn}'"; 
		}
		if(isset($where['caizhi']) && $where['caizhi'] !='')
		{
			$caizhi = $where['caizhi'];
			$sql .=" and caizhi='{$caizhi}'"; 
		}
		if(isset($where['small']) && $where['small'] !='')
		{
			$small= $where['small'];
			$sql .=" and small='{$small}'"; 
		}
		if(isset($where['sbig']) && $where['sbig'] !='')
		{
			$sbig= $where['sbig'];
			$sql .=" and sbig='{$sbig}'"; 
		}
		if(isset($where['policy_id']) && $where['policy_id'] !='')
		{
			$policy_id= $where['policy_id'];
			$sql .=" and policy_id='{$policy_id}'"; 
		}
		if(isset($where['isXianhuo']) && $where['isXianhuo'] !='')
		{
			$isXianhuo= $where['isXianhuo'];
			$sql .=" and isXianhuo='{$isXianhuo}' "; 
		}
		if(isset($where['tuo_type']) && $where['tuo_type'] !='')
		{
		    $tuo_type= $where['tuo_type'];
		    $sql .=" and tuo_type = '{$tuo_type}' ";
		}
		return $this->db()->getAll($sql);			
	}

}

?>