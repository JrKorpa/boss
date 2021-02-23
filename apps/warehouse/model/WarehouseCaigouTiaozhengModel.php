<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseCaigouTiaozhengModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 17:03:50
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseCaigouTiaozhengModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_caigou_tiaozheng';
        $this->_dataObject = array(
			"id"=>" ",
			"goods_id"=>"货号",
			"goods_sn"=>"款号",
			"type"=>"1=>石料，2=>成品，3=>其他，4=>裸石",
			"shizhong"=>"石重",
			"yanse"=>"色级",
			"jingdu"=>"净度",
			"qiegong"=>"切工",
			"yuanshichengbenjia"=>"采购原始成本价",
			"xianzaichengben_old"=>"改前采购成本价",
			"xianzaichengben_new"=>"改后采购成本价",
			"shuoming"=>"调价说明 见数据字典",
			"addname"=>"制单人",
			"addtime"=>"制单时间",
			"checkname"=>"审核人",
			"checktime"=>"审核时间",
			"info"=>"备注",
			"status"=>"状态见数据字典  0=新增，1=已保存，2=已审核，3=已取消"
		);
		parent::__construct($id,$strConn);
	}
	/**
	pageList ,分页
	**/
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{

		$sql  = "SELECT  m.*,g.is_on_sale,g.warehouse FROM `".$this->table()."` AS m left join warehouse_goods as g on m.goods_id = g.goods_id ";
		$str = '';
        if(isset($where['hidden']) && $where['hidden'] != ''){
            $str .= " g.hidden = ".$where['hidden']." AND ";
        }
		if($where['goods_id'])
		{
			$str .="`m`.`goods_id`='".$where['goods_id']."' AND ";
		}
		if($where['type'])
		{
			$str .="`m`.`type`='".$where['type']."' AND ";
		}
 	 	if($where['status'])
		{
			$str .="`m`.`status`='".$where['status']."' AND ";
		}
 	 	if($where['shuoming'])
		{
			$str .="`m`.`shuoming`='".$where['shuoming']."' AND ";
		}
		if($where['addname'])
		{
			$str .="`m`.`addname`='".$where['addname']."' AND ";
		}
		if($where['checkname'])
		{
			$str .="`m`.`checkname`='".$where['checkname']."' AND ";
		}

		if($where['addtime_s'] != '')
		{
			$str .= "`m`.`addtime` >= '".$where['addtime_s']." 00:00:00' AND ";
		}
		if($where['addtime_e'] != '')
		{
			$str .= "`m`.`addtime` <= '".$where['addtime_e']." 23:59:59' AND ";
		}
		if($where['checktime_s'] != '')
		{
			$str .= "`m`.`checktime` >= '".$where['checktime_s']." 00:00:00' AND ";
		}
		if($where['checktime_e'] != '')
		{
			$str .= "`m`.`checktime` <= '".$where['checktime_e']." 23:59:59' AND ";
		}
		if(!empty($where['supplier_id']))
		{
		    $str .= "`m`.`supplier_id` = ".$where['supplier_id'];
		}
		if($str)
		{
			$str = rtrim($str,"AND ");
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY m.id DESC";
		//echo $sql;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	//获取列表
	function getList($where){
	    
        $sql  = "SELECT  m.*,g.is_on_sale,g.warehouse FROM `".$this->table()."` AS m left join warehouse_goods as g on m.goods_id = g.goods_id ";
        $str = '';

        if(isset($where['hidden']) && $where['hidden'] != ''){
            $str .= " g.hidden = ".$where['hidden']." AND ";
        }
        if(!empty($where['goods_id']))
        {
            $str .="`m`.`goods_id`='".$where['goods_id']."' AND ";
        }
        if($where['type'])
        {
            $str .="`m`.`type`='".$where['type']."' AND ";
        }
 	 	if($where['status'])
 	 	{
 	 	    $str .="`m`.`status`='".$where['status']."' AND ";
 	 	}
 	 	if($where['shuoming'])
 	 	{
 	 	    $str .="`m`.`shuoming`='".$where['shuoming']."' AND ";
 	 	}
 	 	if($where['addname'])
 	 	{
 	 	    $str .="`m`.`addname`='".$where['addname']."' AND ";
 	 	}
 	 	if($where['checkname'])
 	 	{
 	 	    $str .="`m`.`checkname`='".$where['checkname']."' AND ";
 	 	}

 	 	if($where['addtime_s'] != '')
 	 	{
 	 	    $str .= "`m`.`addtime` >= '".$where['addtime_s']." 00:00:00' AND ";
 	 	}
 	 	if($where['addtime_e'] != '')
 	 	{
 	 	    $str .= "`m`.`addtime` <= '".$where['addtime_e']." 23:59:59' AND ";
 	 	}
 	 	if($where['checktime_s'] != '')
 	 	{
 	 	    $str .= "`m`.`checktime` >= '".$where['checktime_s']." 00:00:00' AND ";
 	 	}
 	 	if($where['checktime_e'] != '')
 	 	{
 	 	    $str .= "`m`.`checktime` <= '".$where['checktime_e']." 23:59:59' AND ";
 	 	}
 	 	if(!empty($where['supplier_id']))
 	 	{
 	 	    $str .= "`m`.`supplier_id` = ".$where['supplier_id'];
 	 	}
 	 	if($str)
 	 	{
 	 	    $str = rtrim($str,"AND ");
 	 	    $sql .=" WHERE ".$str;
 	 	}
 	 	$sql .= " ORDER BY m.id DESC";
 	 	//echo $sql;exit;
 	 	return $this->db()->getAll($sql);

	}
	//查询货品信息
	function get_goods_info($arr)
	{
		$arr = join("','",$arr);
		$sql = "select goods_id,goods_sn,zuanshidaxiao  as shizhong,yanse,yuanshichengbenjia,chengbenjia,put_in_type  from warehouse_goods where goods_id in ('{$arr}')";
		return $this->db()->getAll($sql);
	}
	//多条信息插入  goods_info :上传信息  info 查询信息
	function insert_num($info,$goods_info,$type,$shuoming)
	{
		//净度 切工 数据表中暂时还没有
		$time    = date("Y-m-d H:i:s");
		$addname = $_SESSION['userName'];
		$info_new = array();
		foreach ($info as $val) 
		{
			$val['xianzaichengben_new'] = $goods_info[$val['goods_id']]['xianzaichengben_new'];
			$val['info'] = iconv('gbk','utf-8',$goods_info[$val['goods_id']]['info']);
			$info_new[] = $val;
		}
		$info = $info_new;
		for ($i=0;$i<count($info);$i++)
		{   
		    $info[$i]['supplier_id'] = empty($info[$i]['supplier_id'])?0:$info[$i]['supplier_id'];
			if ($i == 0)
			{
				$sql = "INSERT INTO `warehouse_caigou_tiaozheng` (`id`, `goods_id`, `goods_sn`, `type`, `shizhong`, `yanse`, `jingdu`, `qiegong`, `yuanshichengbenjia`, `xianzaichengben_old`, `xianzaichengben_new`, `shuoming`, `addname`, `addtime`, `info`, `status`,`supplier_id`) VALUES (NULL, '{$info[$i]['goods_id']}', '{$info[$i]['goods_sn']}', '{$type}', '{$info[$i]['shizhong']}', '{$info[$i]['yanse']}', '', '', '{$info[$i]['yuanshichengbenjia']}', '{$info[$i]['chengbenjia']}', '{$info[$i]['xianzaichengben_new']}', '{$shuoming}','{$addname}', '{$time}', '{$info[$i]['info']}', '1',{$info[$i]['supplier_id']})";
			}
			else
			{
				$sql .= ",(NULL, '{$info[$i]['goods_id']}', '{$info[$i]['goods_sn']}', '{$type}', '{$info[$i]['shizhong']}', '{$info[$i]['yanse']}', '', '', '{$info[$i]['yuanshichengbenjia']}', '{$info[$i]['chengbenjia']}', '{$info[$i]['xianzaichengben_new']}', '{$shuoming}','{$addname}', '{$time}', '{$info[$i]['info']}', '1',{$info[$i]['supplier_id']})";
			}
		}
		return $this->db()->query($sql);
	}
	//修改货品采购成本价格
	function update_chengben($goods_id,$xianzaichengben_new)
	{
		$sql ="update warehouse_goods set chengbenjia= '{$xianzaichengben_new}' where goods_id = '{$goods_id}'";
		//var_dump($sql);exit;
		return $this->db()->query($sql);
	}
	//根据款号查询默认工厂(【order by is_factory desc】 表示如果没有默认工厂，就给出一条非默认工厂)
	function getFactoryByStyleSn($style_sn){
	     $sql = "select * from front.rel_style_factory where style_sn='{$style_sn}' and is_cancel=1 order by is_factory desc";
	     return $this->db()->getRow($sql);
	}

}	

?>