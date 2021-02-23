<?php
/**
 *  -------------------------------------------------
 *   @file		: GoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-08 15:20:40
 *   @update	:
 *  -------------------------------------------------
 */
class GoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'goods';
        $this->_dataObject = array();
		parent::__construct($id,$strConn);
	}


	/**
	 *	pageList，分页列表
	 *	@url MenuController/index
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
        $inner = "";
        if(SYS_SCOPE == 'zhanting'){
            if($where['type'] == 1){
                $inner = " inner join warehouse_shipping.warehouse_goods wg on wg.goods_id = g.item_id ";
            }else{
                $inner = " inner join warehouse_shipping.warehouse_bill wb on wb.bill_no = g.item_id ";
            }
        }
		$sql = "select g.*,substring(g.item_id, 1, 2) as type,substring(g.item_id,3, 10) as order_id,substring(g.item_id, 1, 1) as type1 from ".$this->table()." g {$inner}";
		$sql .=" WHERE 1";
		if(!empty($where['company']))
		{
			$sql .= " AND g.company = {$where['company']}";
		}
		if(!empty($where['prc_id']))
		{
			$sql .= " AND g.prc_id = {$where['prc_id']}";
		}

		//过滤不需要显示的结算商
		if (!empty($where['filter_product_id'])){
			$sql .= " AND g.prc_id not in (". implode(',', $where['filter_product_id']) .")";
		}

        if (!empty($where['filter_storage_mode'])){
            $sql .= " AND g.storage_mode in (". implode(',', $where['filter_storage_mode']) .")";
        }

		if(!empty($where['type']))
		{
			$sql .= " AND g.type = {$where['type']}";
		}
		if(!empty($where['goods_status']))
		{
			$sql .= " AND g.goods_status = '{$where['goods_status']}'";
		}
		if(!empty($where['pay_apply_status']))
		{
			$sql .= " AND g.pay_apply_status = '{$where['pay_apply_status']}'";
		}
		if(!empty($where['storage_mode']))
		{
			$sql .= " AND g.storage_mode in ({$where['storage_mode']})";
		}
		if(!empty($where['pay_apply_number']))
		{
			$sql .= " AND g.pay_apply_number = '{$where['pay_apply_number']}'";
		}
		if(!empty($where['serial_number']))
		{
			$sql .= " AND g.serial_number = '{$where['serial_number']}'";
		}
		if(!empty($where['item_id']))
		{
			$sql .= " AND g.item_id ='{$where['item_id']}'";
		}
		if(!empty($where['goods_ids']))
		{
			$sql .= " AND g.item_id in({$where['goods_ids']})";
		}
		if(!empty($where['zhengshuhao']))
		{
			$sql .= " AND g.zhengshuhao in ({$where['zhengshuhao']})";
		}
		if(!empty($where['zhengshu_all']))
		{
			$sql .= " AND g.zhengshuhao in({$where['zhengshu_all']})";
		}
		if(!empty($where['make_time_start']))
		{
			$sql .= " AND g.make_time >= '".$where['make_time_start']." 00:00:00'";
		}
		if(!empty($where['make_time_end']))
		{
			$sql .= " AND g.make_time <= '".$where['make_time_end']." 23:59:59'";
		}
		if(!empty($where['check_time_start']))
		{
			$sql .= " AND g.check_time >= '".$where['check_time_start']." 00:00:00'";
		}
		if(!empty($where['check_time_end']))
		{
			$sql .= " AND g.check_time <= '".$where['check_time_end']." 23:59:59'";
		}
		if(!empty($where['prc_num']))
		{                   
			$sql .= " AND g.prc_num in (".$where['prc_num'].")";
		}
		if(!empty($where['prc_num_all']))
		{
			$sql .= " AND g.prc_num in({$where['prc_num_all']})";
		}
		if(!empty($where['item_type']))
		{
			$sql .= " AND g.item_type = '{$where['item_type']}' ";
		}
		if (!empty($where['pay_content']))
		{
		    $sql .= " AND g.pay_content={$where['pay_content']}";
		}
        //zt隐藏
        if (SYS_SCOPE == 'zhanting'){
            if($where['type'] == 1){
                $sql .= " AND wg.hidden<>1 ";
            }else{
                $sql .= " AND wb.hidden<>1 ";
            }
        }
		$sql .= " ORDER BY g.`serial_number` DESC";
        //echo $sql;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	public function getPrintAll($where)
	{
		$sql = "select * from ".$this->table();
		$sql .=" WHERE 1";
		if(!empty($where['company']))
		{
			$sql .= " AND company = {$where['company']}";
		}
		if(!empty($where['prc_id']))
		{
			$sql .= " AND prc_id = {$where['prc_id']}";
		}
		if(!empty($where['type']))
		{
			$sql .= " AND type = {$where['type']}";
		}
		if(!empty($where['goods_status']))
		{
			$sql .= " AND goods_status = '{$where['goods_status']}'";
		}
		if(!empty($where['pay_apply_status']))
		{
			$sql .= " AND pay_apply_status = '{$where['pay_apply_status']}'";
		}
		if(!empty($where['storage_mode']))
		{
			$sql .= " AND storage_mode in ({$where['storage_mode']})";
		}
		if(!empty($where['pay_apply_number']))
		{
			$sql .= " AND pay_apply_number = '{$where['pay_apply_number']}'";
		}
		if(!empty($where['serial_number']))
		{
			$sql .= " AND serial_number = '{$where['serial_number']}'";
		}
		if(!empty($where['item_id']))
		{
			$sql .= " AND item_id = '{$where['item_id']}'";
		}
		if(!empty($where['goods_ids']))
		{
			$sql .= " AND item_id in({$where['goods_ids']})";
		}
		if(!empty($where['zhengshuhao']))
		{
			$sql .= " AND zhengshuhao = '{$where['zhengshuhao']}'";
		}
		if(!empty($where['zhengshu_all']))
		{
			$sql .= " AND zhengshuhao in({$where['zhengshu_all']})";
		}
		if(!empty($where['make_time_start']))
		{
			$sql .= " AND make_time >= '".$where['make_time_start']." 00:00:00'";
		}
		if(!empty($where['make_time_end']))
		{
			$sql .= " AND make_time <= '".$where['make_time_end']." 23:59:59'";
		}
		if(!empty($where['check_time_start']))
		{
			$sql .= " AND check_time >= '".$where['check_time_start']." 00:00:00'";
		}
		if(!empty($where['check_time_end']))
		{
			$sql .= " AND check_time <= '".$where['check_time_end']." 23:59:59'";
		}
		if(!empty($where['prc_num']))
		{
			$sql .= " AND prc_num = '".$where['prc_num']."'";
		}
		if(!empty($where['prc_num_all']))
		{
			$sql .= " AND prc_num in({$where['prc_num_all']})";
		}
		if(!empty($where['item_type']))
		{
			$sql .= " AND item_type = '{$where['item_type']}' ";
		}
		if (!empty($where['pay_content']))
		{
		    $sql .= " AND pay_content={$where['pay_content']}";
		}
        $sql .= " ORDER BY `serial_number` DESC";
		$data = $this->db()->getAll($sql);
		//echo $sql;exit;
		return $data;
	}

	public function getRow($id)
	{
		$sql = "select * from ".$this->table();
		$sql .= " WHERE serial_number = '$id'";
		return $this->db()->getRow($sql);
	}

	public function update($valueArr,$whereArr)
	{
		$field = '';
		$where = ' 1';
		foreach($valueArr as $k => $v)
		{
			$field .= "$k = '$v',";
		}
		foreach($whereArr as $k => $v)
		{
			$where .= " AND $k = '$v'";
		}
		$field = substr($field,0,-1);
		$sql = "UPDATE ".$this->table()." SET ".$field;
        $sql .= " WHERE ".$where;
		return $this->db()->query($sql,array());
	}

	//转换编码格式，导出csv数据
	public function detail_csv($name,$title,$content)
	{
		$ymd = date("Ymd_His", time()+8*60*60);
		header("Content-Disposition: attachment;filename=".iconv('utf-8','gbk',$name).$ymd.".csv");
		$fp = fopen('php://output', 'w');
		$title = eval('return '.iconv('utf-8','gbk',var_export($title,true).';')) ;
		fputcsv($fp, $title);
	   foreach($content as $k=>$v)
	   {
			fputcsv($fp, $v);
	   }
		fclose($fp);


	}


	public function getSalePrice($goods_id){
		$sql="select bg.shijia from warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_bill b where bg.bill_id=b.id and b.bill_status=2 and b.bill_type='S' and bg.goods_id= '{$goods_id}' order by b.check_time desc limit 1";
	    return $this->db()->getOne($sql);
	}
}

?>