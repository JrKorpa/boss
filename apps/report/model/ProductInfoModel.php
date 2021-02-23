<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductInfoModel.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  -------------------------------------------------
 */
class ProductInfoModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
            $this->_objName = 'product_info';
            $this->_dataObject = array(
                "id"=>"ID",
                "bc_sn"=>"布产号",
                "p_id"=>"采购单明细ID/订单商品ID",
                "p_sn"=>"采购单号/订单号",
                "style_sn"=>"款号",
                "status"=>"状态",
                "num"=>"数量",
                "prc_id"=>"工厂ID",
                "prc_name"=>"工厂名称",
                "opra_uname"=>"跟单人",
                "add_time"=>"单据添加时间",
                "esmt_time"=>"标准出厂时间",
                "rece_time"=>"工厂交货时间",
                "info"=>"备注",
                "from_type"=>"来源类型：1=>采购单 2=>订单",
				"caigou_info"=>"采购备注"

            );
		parent::__construct($id,$strConn);
	}
	/**
	 * 获取工厂超期报表第一层数据
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @return number
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		/*
        $sql="select dotime,COUNT(1) cnt,SUM(IF(rece_time = '0000-00-00 00:00:00',1,0)) un_cnt,SUM(IF(rece_time = '0000-00-00 00:00:00',0,1)) on_cnt
            from kucun_bak.product_info_gongchang p
            where p.esmt_time = p.dotime
             ";
             */
		$sql="select esmt_time as dotime,COUNT(1) cnt,SUM(IF(rece_time = '0000-00-00 00:00:00',1,0)) un_cnt,SUM(IF(rece_time = '0000-00-00 00:00:00',0,1)) on_cnt
            from kela_supplier.product_info p
            where 1
             ";
		if($where['start_time']){
			$sql.=" and p.esmt_time>='{$where['start_time']} 00:00:00' ";
		}
		if($where['end_time']){
			$sql.=" and p.esmt_time<='{$where['end_time']} 23:59:59' ";
		}
		if($where['style_sn']){
			$sql.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if($where['prc_name']){
			$sql.=" and p.prc_name like '%{$where['prc_name']}%' ";
		}
		if($where['from_type']){//1采购 2客订单
			$sql.=" and p.from_type='{$where['from_type']}' ";
		}
		if(isset($where['opra_uname']) && $where['opra_uname']){//跟单人
			$sql.=" and p.opra_uname='{$where['opra_uname']}' ";
		}
		if(isset($where['prc_ids']) and is_array($where['prc_ids']))
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			if($where['prc_ids'])
			$sql .= " AND p.prc_id in ({$where['prc_ids']})";
		}
		if(isset($where['prc_ids_string']))
		{
			$where['prc_ids_string']=trim($where['prc_ids_string'],',');
			if($where['prc_ids_string'])
				$sql .= " AND p.prc_id in ({$where['prc_ids_string']})";
		}
		if($where['opra_unames'] && isset($where['opra_unames'][0]) && $where['opra_unames'][0]){
			$opra_unames='';
			foreach ($where['opra_unames'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$opra_unames && $sql.=" and p.opra_uname in ({$opra_unames}) ";
		}
		$sql.=" 
            group by esmt_time
            order by esmt_time ";
        //echo $sql;exit;
		//计算分页
		$data['pageSize']=$pageSize;
		$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
		$data['recordCount'] = $this->db()->getAll($countSql);
		$data['recordCount']=count($data['recordCount']);
		$data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
		$data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
		$data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
		$data['isFirst'] = $data['page'] > 1 ? false : true;
		$data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
		$data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
		$data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
        $data['data'] = $this->db()->getAll($data['sql']);
		return $data;
	}

    public function getDeliveryOrder($where,$page,$pageSize=10,$useCache=true)
    {
    	/*
        $sql="select dotime,COUNT(1) delivery_num
            from kucun_bak.product_info_gongchang p
            where left(p.add_time,10) = p.dotime
             ";
             */
    	$sql="select left(p.add_time,10) as dotime,COUNT(1) delivery_num
            from kela_supplier.product_info p
            where 1
             ";
		if($where['start_time']){
			$sql.=" and p.add_time>='{$where['start_time']} 00:00:00' ";
		}
		if($where['end_time']){
			$sql.=" and p.add_time<='{$where['end_time']} 23:59:59' ";
		}
		if($where['style_sn']){
			$sql.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if($where['prc_name']){
			$sql.=" and p.prc_name like '%{$where['prc_name']}%' ";
		}
		if($where['from_type']){//1采购 2客订单
			$sql.=" and p.from_type='{$where['from_type']}' ";
		}
		if(isset($where['opra_uname']) && $where['opra_uname']){//跟单人
			$sql.=" and p.opra_uname='{$where['opra_uname']}' ";
		}
		if(isset($where['prc_ids']) and is_array($where['prc_ids']))
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			if($where['prc_ids'])
			$sql .= " AND p.prc_id in ({$where['prc_ids']})";
		}
		if(isset($where['prc_ids_string']))
		{
			$where['prc_ids_string']=trim($where['prc_ids_string'],',');
			if($where['prc_ids_string'])
				$sql .= " AND p.prc_id in ({$where['prc_ids_string']})";
		}
		if($where['opra_unames'] && isset($where['opra_unames'][0]) && $where['opra_unames'][0]){
			$opra_unames='';
			foreach ($where['opra_unames'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$opra_unames && $sql.=" and p.opra_uname in ({$opra_unames}) ";
		}
		$sql.=" 
            group by left(p.add_time,10)
            order by left(p.add_time,10) ";
        //echo $sql;exit;
		//计算分页
		$data['pageSize']=$pageSize;
		$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
		$data['recordCount'] = $this->db()->getAll($countSql);
		$data['recordCount']=count($data['recordCount']);
		$data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
		$data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
		$data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
		$data['isFirst'] = $data['page'] > 1 ? false : true;
		$data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
		$data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
		$data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
        $data['data'] = $this->db()->getAll($data['sql']);
		return $data;
    }



    public function getDetailDeliveryOrder($where,$page,$pageSize=10,$useCache=true)
    {
    	/*
        $sql="select p.dotime,p.opra_uname,COUNT(1) delivery_num
            from kucun_bak.product_info_gongchang p
            where left(p.add_time,10) = p.dotime
             ";
             */
    	$sql="select left(p.add_time,10) as dotime,p.opra_uname,COUNT(1) delivery_num
            from kela_supplier.product_info p
            where 1
             ";
		if($where['start_time']){
			$sql.=" and p.add_time>='{$where['start_time']} 00:00:00' ";
		}
		if($where['end_time']){
			$sql.=" and p.add_time<='{$where['end_time']} 23:59:59' ";
		}
		if($where['style_sn']){
			$sql.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if($where['prc_name']){
			$sql.=" and p.prc_name like '%{$where['prc_name']}%' ";
		}
		if($where['from_type']){//1采购 2客订单
			$sql.=" and p.from_type='{$where['from_type']}' ";
		}
		if(isset($where['opra_uname']) && $where['opra_uname']){//跟单人
			$sql.=" and p.opra_uname='{$where['opra_uname']}' ";
		}
		if(isset($where['prc_ids']) and is_array($where['prc_ids']))
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			if($where['prc_ids'])
			$sql .= " AND p.prc_id in ({$where['prc_ids']})";
		}
		if(isset($where['prc_ids_string']))
		{
			$where['prc_ids_string']=trim($where['prc_ids_string'],',');
			if($where['prc_ids_string'])
				$sql .= " AND p.prc_id in ({$where['prc_ids_string']})";
		}
		if($where['opra_unames'] && isset($where['opra_unames'][0]) && $where['opra_unames'][0]){
			$opra_unames='';
			foreach ($where['opra_unames'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$opra_unames && $sql.=" and p.opra_uname in ({$opra_unames}) ";
		}
		$sql.=" 
            group by left(p.add_time,10)
            order by left(p.add_time,10) ";
        //echo $sql;exit;
		//计算分页
		$data['pageSize']=$pageSize;
		$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
		$data['recordCount'] = $this->db()->getAll($countSql);
		$data['recordCount']=count($data['recordCount']);
		$data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
		$data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
		$data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
		$data['isFirst'] = $data['page'] > 1 ? false : true;
		$data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
		$data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
		$data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
        $data['data'] = $this->db()->getAll($data['sql']);
		return $data;
    }

    public function getReceiveOrder($where,$page,$pageSize=10,$useCache=true)
    {
    	/*
        $sql="select dotime,COUNT(1) receive_num
            from kucun_bak.product_info_gongchang p
            where left(p.order_time,10) = p.dotime
             ";
             */
    	$sql="select left(p.order_time,10) as dotime,COUNT(1) receive_num
            from kela_supplier.product_info p
            where 1
             ";
		if($where['start_time']){
			$sql.=" and p.order_time>='{$where['start_time']} 00:00:00' ";
		}
		if($where['end_time']){
			$sql.=" and p.order_time<='{$where['end_time']} 23:59:59' ";
		}
		if($where['style_sn']){
			$sql.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if($where['prc_name']){
			$sql.=" and p.prc_name like '%{$where['prc_name']}%' ";
		}
		if($where['from_type']){//1采购 2客订单
			$sql.=" and p.from_type='{$where['from_type']}' ";
		}
		if(isset($where['opra_uname']) && $where['opra_uname']){//跟单人
			$sql.=" and p.opra_uname='{$where['opra_uname']}' ";
		}
		if(isset($where['prc_ids']) and is_array($where['prc_ids']))
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			if($where['prc_ids'])
			$sql .= " AND p.prc_id in ({$where['prc_ids']})";
		}
		if(isset($where['prc_ids_string']))
		{
			$where['prc_ids_string']=trim($where['prc_ids_string'],',');
			if($where['prc_ids_string'])
				$sql .= " AND p.prc_id in ({$where['prc_ids_string']})";
		}
		if($where['opra_unames'] && isset($where['opra_unames'][0]) && $where['opra_unames'][0]){
			$opra_unames='';
			foreach ($where['opra_unames'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$opra_unames && $sql.=" and p.opra_uname in ({$opra_unames}) ";
		}
		$sql.=" 
            group by left(p.order_time,10)
            order by left(p.order_time,10) ";
        //echo $sql;exit;
		//计算分页
		$data['pageSize']=$pageSize;
		$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
		$data['recordCount'] = $this->db()->getAll($countSql);
		$data['recordCount']=count($data['recordCount']);
		$data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
		$data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
		$data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
		$data['isFirst'] = $data['page'] > 1 ? false : true;
		$data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
		$data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
		$data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
        $data['data'] = $this->db()->getAll($data['sql']);
		return $data;
    }

    public function getDetailReceiveOrder($where,$page,$pageSize=10,$useCache=true)
    {
    	/*
        $sql="select dotime,p.opra_uname,COUNT(1) receive_num
            from kucun_bak.product_info_gongchang p
            where left(p.order_time,10) = p.dotime
             ";
             */
    	$sql="select left(p.order_time,10) as dotime,p.opra_uname,COUNT(1) receive_num
            from kela_supplier.product_info p
            where 1
             ";
		if($where['start_time']){
			$sql.=" and p.order_time>='{$where['start_time']} 00:00;00' ";
		}
		if($where['end_time']){
			$sql.=" and p.order_time<='{$where['end_time']} 23:59:59' ";
		}
		if($where['style_sn']){
			$sql.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if($where['prc_name']){
			$sql.=" and p.prc_name like '%{$where['prc_name']}%' ";
		}
		if($where['from_type']){//1采购 2客订单
			$sql.=" and p.from_type='{$where['from_type']}' ";
		}
		if(isset($where['opra_uname']) && $where['opra_uname']){//跟单人
			$sql.=" and p.opra_uname='{$where['opra_uname']}' ";
		}
		if(isset($where['prc_ids']) and is_array($where['prc_ids']))
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			if($where['prc_ids'])
			$sql .= " AND p.prc_id in ({$where['prc_ids']})";
		}
		if(isset($where['prc_ids_string']))
		{
			$where['prc_ids_string']=trim($where['prc_ids_string'],',');
			if($where['prc_ids_string'])
				$sql .= " AND p.prc_id in ({$where['prc_ids_string']})";
		}
		if($where['opra_unames'] && isset($where['opra_unames'][0]) && $where['opra_unames'][0]){
			$opra_unames='';
			foreach ($where['opra_unames'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$opra_unames && $sql.=" and p.opra_uname in ({$opra_unames}) ";
		}
		$sql.=" 
            group by left(p.order_time,10),p.opra_uname ";
        //echo $sql;exit;
		//计算分页
		$data['pageSize']=$pageSize;
		$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
		$data['recordCount'] = $this->db()->getAll($countSql);
		$data['recordCount']=count($data['recordCount']);
		$data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
		$data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
		$data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
		$data['isFirst'] = $data['page'] > 1 ? false : true;
		$data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
		$data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
		$data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
        $data['data'] = $this->db()->getAll($data['sql']);
		return $data;
    }

    /**
	 * 获取工厂超期报表第二层数据
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @return number
	 */
	function pageDetailList ($where,$page,$pageSize=10,$useCache=true)
	{
		/*
        $sql="select dotime,p.opra_uname,COUNT(1) cnt,SUM(IF(rece_time = '0000-00-00 00:00:00',1,0)) un_cnt,SUM(IF(rece_time = '0000-00-00 00:00:00',0,1)) on_cnt
            from kucun_bak.product_info_gongchang p
            where p.esmt_time = p.dotime ";
        */
		$sql="select p.esmt_time as dotime,p.opra_uname,COUNT(1) cnt,SUM(IF(rece_time = '0000-00-00 00:00:00',1,0)) un_cnt,SUM(IF(rece_time = '0000-00-00 00:00:00',0,1)) on_cnt
            from kela_supplier.product_info p
            where 1 ";
		if($where['start_time']){
			$sql.=" and p.esmt_time>='{$where['start_time']} 00:00:00' ";
		}
		if($where['end_time']){
			$sql.=" and p.esmt_time<='{$where['end_time']} 23:59:59' ";
		}
		if($where['style_sn']){
			$sql.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if($where['from_type']){
			$sql.=" and p.from_type='{$where['from_type']}' ";
		}
		if(isset($where['prc_ids']) && !empty($where['prc_ids'])  && isset($where['prc_ids'][0]) && $where['prc_ids'][0])
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			$where['prc_ids'] && $sql .= " AND p.prc_id in ({$where['prc_ids']})";
		}
		elseif(isset($where['prc_ids_string']) and !empty($where['prc_ids_string']))
		{
			$sql .= " AND p.prc_id in ({$where['prc_ids_string']})";
		}
		if($where['opra_unames'] && isset($where['opra_unames'][0]) && $where['opra_unames'][0]){
			$opra_unames='';
			foreach ($where['opra_unames'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$opra_unames && $sql.=" and p.opra_uname in ({$opra_unames}) ";
		}
		elseif($where['opra_uname_string']){
			$where['opra_uname_string']=explode(',',$where['opra_uname_string']);
			$opra_unames='';
			foreach($where['opra_uname_string'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$sql.=" and p.opra_uname in ({$opra_unames}) ";
		}
		$sql.=" GROUP BY p.esmt_time,p.opra_uname ORDER BY p.esmt_time DESC,p.opra_uname desc ";
		//echo $sql;exit;
		//计算分页
		$data['pageSize']=$pageSize;
		$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
		$data['recordCount'] = $this->db()->getAll($countSql);
		$data['recordCount']=count($data['recordCount']);
		$data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
		$data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
		$data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
		$data['isFirst'] = $data['page'] > 1 ? false : true;
		$data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
		$data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
		$data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
        $data['data'] = $this->db()->getAll($data['sql']);
		return $data;
	}
	/**
	 * 获取工厂超期第三层数据
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @return unknown
	 */
	function pageLastDetailList ($where,$page,$pageSize=10,$useCache=true)
	{
		/*
		$sql="SELECT p.* 
            from kucun_bak.product_info_gongchang p
            where p.esmt_time = p.dotime ";
        */
		$sql="SELECT p.*,p.esmt_time as dotime
            from kela_supplier.product_info p
            where 1 ";
        //$sql.=" AND p.esmt_time!='0000-00-00' AND left(p.esmt_time,10) = p.dotime  ";
		if($where['start_time']){
			$sql.=" and p.esmt_time>='{$where['start_time']} 00:00:00' ";
		}
		if($where['end_time']){
			$sql.=" and p.esmt_time<='{$where['end_time']} 23:59:59' ";
		}
		if($where['style_sn']){
			$sql.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if(isset($where['prc_name'])&& $where['prc_name']){
			$sql.=" and p.prc_name like '%{$where['prc_name']}%' ";
		}
		if($where['from_type']){
			$sql.=" and p.from_type='{$where['from_type']}' ";
		}
		if(isset($where['opra_uname']) && $where['opra_uname']){
			$sql.=" and p.opra_uname='{$where['opra_uname']}' ";
		}
		if(isset($where['opra_uname_string']) && $where['opra_uname_string']){
			$sql.=" and p.opra_uname='{$where['opra_uname_string']}' ";
		}
		if(isset($where['prc_ids_string']) and !empty($where['prc_ids_string']))
		{
			$sql .= " AND p.prc_id in ({$where['prc_ids_string']})";
		}
		$sql.="  order BY left(p.esmt_time,10) desc,p.id desc ";
		//exit($sql);
		$data['pageSize']=$pageSize;
		$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
		$data['recordCount'] = $this->db()->getAll($countSql);
		$data['recordCount']=count($data['recordCount']);
		$data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
		$data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
		$data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
		$data['isFirst'] = $data['page'] > 1 ? false : true;
		$data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
		$data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
		$data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
        $data['data'] = $this->db()->getAll($data['sql']);
		return $data;
	}
	/**
	 * 获取工厂超期详细报表数据
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @return unknown
	 */
	function page_factory_lated_csv ($where,$page,$pageSize=10,$useCache=true)
	{
		/*
		$sql="SELECT p.* 
            from kucun_bak.product_info_gongchang p
            where p.esmt_time = p.dotime ";
        */
		$sql="SELECT p.*
            from kela_supplier.product_info p
            where 1 ";
        //$sql.=" AND p.esmt_time!='0000-00-00' AND left(p.esmt_time,10) = p.dotime  ";
		if($where['start_time']){
			$sql.=" and p.esmt_time>='{$where['start_time']} 00:00:00' ";
		}
		if($where['end_time']){
			$sql.=" and p.esmt_time<='{$where['end_time']} 23:59:59' ";
		}
		if($where['style_sn']){
			$sql.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if(isset($where['prc_name'])&& $where['prc_name']){
			$sql.=" and p.prc_name like '%{$where['prc_name']}%' ";
		}
		if($where['from_type']){
			$sql.=" and p.from_type='{$where['from_type']}' ";
		}
		if(isset($where['opra_uname']) && $where['opra_uname']){
			$sql.=" and p.opra_uname='{$where['opra_uname']}' ";
		}
		if(isset($where['prc_ids_string']) and !empty($where['prc_ids_string']))
		{
			$sql .= " AND p.prc_id in ({$where['prc_ids_string']})";
		}
		$sql.="  order BY p.esmt_time desc,p.id desc ";
        $data = $this->db()->getAll($sql);
		return $data;
	}

	/**
	 * 获取工厂平均生产时长第二层列表数据
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @return number
	 */
	function page_Avg_producttime_opra_List ($where,$page,$pageSize=10,$useCache=true)
	{
		$exception='';//节假日
		$sql="SELECT  GROUP_CONCAT(p.id) bc_ids, LEFT(p.rece_time,10) rece_date,LEFT(o.opra_time,10) oqc_date ,COUNT(*) count,p.prc_name,p.opra_uname FROM `product_info` p
LEFT JOIN  `app_processor_worktime` w ON w.processor_id=p.`prc_id`
LEFT JOIN (SELECT MAX(opra_time) opra_time,bc_id,oqc_result FROM  `product_oqc_opra` GROUP BY bc_id) o ON o.bc_id=p.id
WHERE (p.`status`=9 AND p.`order_time` )";
		if($where['start_time']){
			$sql.=" and p.rece_time>='{$where['start_time']}' ";
		}
		if($where['end_time']){
			$sql.=" and p.rece_time<='{$where['end_time']} 23:59:59' ";
		}
		if($where['style_sn']){
			$sql.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if($where['from_type']){
			$sql.=" and p.from_type='{$where['from_type']}' ";
		}
		if(isset($where['prc_ids']) && !empty($where['prc_ids'])  && isset($where['prc_ids'][0]) && $where['prc_ids'][0])
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			$where['prc_ids'] && $sql .= " AND p.prc_id in ({$where['prc_ids']})";
		}
		elseif(isset($where['prc_ids_string']) && $where['prc_ids_string'] )
		{
			$where['prc_ids_string']=trim($where['prc_ids_string'],' ');
			$where['prc_ids_string'] && $sql .= " AND p.prc_id in ({$where['prc_ids_string']})";
		}
		if($where['opra_unames'] && isset($where['opra_unames'][0]) && $where['opra_unames'][0]){
			$opra_unames='';
			foreach ($where['opra_unames'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$opra_unames && $sql.=" and p.opra_uname in ({$opra_unames}) ";
		}
		elseif($where['opra_uname_string']){
			$where['opra_uname_string']=explode(',',$where['opra_uname_string']);
			$opra_unames='';
			foreach($where['opra_uname_string'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim(trim($opra_unames,','),' ');
			$opra_unames && $sql.=" and p.opra_uname in ({$opra_unames}) ";
		}
		$sql.="   GROUP BY p.opra_uname,p.prc_name,LEFT(p.rece_time,10)  ORDER BY p.rece_time DESC ,o.opra_time DESC";
	//		echo $sql;exit;
		//计算分页
		$data['pageSize']=$pageSize;
		$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
		$data['recordCount'] = $this->db()->getAll($countSql);
		$data['recordCount']=count($data['recordCount']);
		$data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
		$data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
		$data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
		$data['isFirst'] = $data['page'] > 1 ? false : true;
		$data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
		$data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
		$data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
        $data['data'] = $this->db()->getAll($data['sql']);
		//计算平均生产时长
		if($data['data']){
			$ids='';//布产ids
			foreach ($data['data'] as &$value){
				$value['avg_product_time']=0;
				//出厂时间如果不等于0000-00-00，则取质检通过的时间
				$value['count_date']=($value['rece_date']!='0000-00-00')?$value['rece_date']:$value['oqc_date'];
				$ids.=$value['bc_ids'].',';
			
			$ids=trim($value['bc_ids'],',');
			$product_sql="SELECT p.id, p.`prc_id`,p.`prc_name`,p.bc_sn,p.`status`,p.order_time,p.rece_time,o.oqc_result,o.opra_time oqc_time,w.normal_day,w.wait_dia,w.behind_wait_dia,w.is_rest,w.order_problem  FROM `product_info` p
LEFT JOIN  `app_processor_worktime` w ON w.processor_id=p.`prc_id`
LEFT JOIN (SELECT MAX(opra_time) opra_time,bc_id,oqc_result FROM  `product_oqc_opra` GROUP BY bc_id) o ON o.bc_id=p.id
WHERE ";
			$product_sql.="  p.id in({$ids})";
			$data_list=$this->db()->getAll($product_sql);
			$product_time_list=array();
			if($data_list){
				foreach ($data_list as &$val){
					//求单个生产时长=(订单操作出厂时间-订单开始接单日期)的用时-等钻用时-法定节假日放假时长
					if($val['status']==9){
						$val['count_time']=$val['rece_time'];//如果是已出厂，则统计时间取出厂时间，否则取质检通过时间
					}
					else{
						$val['count_time']=$val['oqc_time'];
					}
					$word_day=7;
					if($val['is_rest']){//计算每周工作的天数
						$word_day=8-$val['is_rest'];
					}
					$val['product_time']=$this->get_diff_hour($val['order_time'],$val['count_time'],$word_day,$exception);
					$val['product_time']=$val['product_time']>0?$val['product_time']:0;
					$val['product_time']=($val['product_time']-$val['wait_dia']-$val['behind_wait_dia']>0)?$val['product_time']-$val['wait_dia']-$val['behind_wait_dia']:$val['product_time'];//减去等钻时间
					$product_time_list[]=$val['product_time'];
					
				}
				$value['avg_product_time']=number_format(array_sum($product_time_list)/count($product_time_list),2);
			}
			else $value['avg_product_time']=0;
		}
	}
		//	print_r($product_time_list);exit;
		$data['product_time_list']=$product_time_list;
		return $data;
	}
	/**
	 * 工厂平均生产时长详细报表sql
	 * @param unknown $where
	 * @return string
	 */
	function getCsvDetailSql($where=array(),$time_type=1)
	{
	
		$sql = "SELECT main.*,o.max_time as factory_time,r.remark as opra_remark,r.time,c.peishi_status FROM `".$this->table()."` as main left join (select bc_id,MAX(time) as max_time from product_opra_log where status=3 group by bc_id) as o on o.bc_id=main.id left join (SELECT t.remark,t.bc_id,t.time FROM product_opra_log t INNER JOIN (SELECT bc_id, MAX(id) AS max FROM product_opra_log GROUP BY bc_id ) as tm ON t.bc_id = tm.bc_id AND t.id=tm.max) as r on r.bc_id=main.id";
		$sql.=" left join product_info_4c c on main.id=c.id where 1=1";
		
		if(isset($where['bc_sn']) and $where['bc_sn'] != "")
		{
			$bc_sn="'".str_replace(' ',"','",$where['bc_sn'])."'";
	
			$sql .= " AND main.bc_sn in ({$bc_sn})";
		}
		if(isset($where['p_sn']) and $where['p_sn'] != "")
		{
			//$sql .= " AND p_sn like \"%".addslashes($where['p_sn'])."%\"";
			$p_sn="'".str_replace(' ',"','",$where['p_sn'])."'";
			$sql .= " AND main.p_sn in ({$p_sn})";
		}
		if(isset($where['style_sn']) and $where['style_sn'] != "")
		{
			$sql .= " AND main.style_sn like \"%".addslashes($where['style_sn'])."%\"";
		}
		if(isset($where['status']) and $where['status'] !== "")
		{
			$sql .= " AND main.status = ".$where['status'];
		}
		
		if(isset($where['from_type']) and !empty($where['from_type']))
		{
			$sql .= " AND main.from_type='".addslashes($where['from_type'])."'";
		}
		if(isset($where['start_time']) and $where['start_time'] != "")
		{
			if($time_type==1)
			$sql .= " AND main.rece_time >= '{$where['start_time']} 00:00:00'";
			elseif($time_type==2)
			$sql .= " AND main.esmt_time >= '{$where['start_time']} 00:00:00'";
		}
		if(isset($where['end_time']) and $where['end_time'] != "")
		{
			if($time_type==1)
			$sql .= " AND main.rece_time <= '{$where['end_time']} 23:59:59'";
			elseif($time_type==2)
			$sql .= " AND main.esmt_time <= '{$where['end_time']} 23:59:59'";
		}
		if(isset($where['prc_ids']) && $where['prc_ids'])
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			$where['prc_ids'] && $sql .= " AND main.prc_id in ({$where['prc_ids']})";
		}
		if(isset($where['prc_ids_string']) and !empty($where['prc_ids_string']))
		{
			$sql .= " AND main.prc_id in ({$where['prc_ids_string']})";
		}
		if($where['opra_uname_string']){
			$where['opra_uname_string']=explode(',',$where['opra_uname_string']);
			$opra_unames='';
			foreach($where['opra_uname_string'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$opra_unames && $sql.=" and main.opra_uname in ({$opra_unames}) ";
		}
		
		if(isset($where['orderby']) and $where['orderby'] !== "" and isset($where['desc_or_asc']) and $where['desc_or_asc'] !== "")
		{
			$sql.= " ORDER BY {$where['orderby']} {$where['desc_or_asc']}";
		}
		else if(isset($where['orderby']) and $where['orderby'] !== "" )
		{
			$sql.= " ORDER BY {$where['orderby']}";
		}
		else
		{
			$sql .= " ORDER BY main.id DESC";
		}
	
		//add end
		//echo $sql;exit;
		return $sql;
	}
	/**
	 * 工厂平均生产时长详细报表数据
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @return unknown
	 */
	function pageCsvDetailList ($where,$page,$pageSize=10,$useCache=true,$time_type=1)
	{
		$sql=$this->getCsvDetailSql($where,$time_type);
		$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	/**
	 * 获取两个日期之间的小时差，除去节假日或周末休息日
	 * @param string $start 开始日期 datetime 
	 * @param string $end 结束日期 datetime 
	 * @param string $exception 节假日
	 * @param string $word_day 一周工作的天数
	 * @return Ambigous <number, string>
	 */
	function get_diff_hour( $start='now', $end='tomorrow',$word_day='5' , $exception=''){
		$starttime = strtotime($start);
		$endtime= strtotime($end);
		$diff_hour=number_format(($endtime-$starttime)/3600,2);//时间差时
		$startdate=date("Y-m-d",$starttime);
		$endtdate=date("Y-m-d",$endtime);
		$diff_date=(strtotime($endtdate)-strtotime($startdate))/(3600*24);
		while($diff_date>1){//中间间隔不少于1天,则要考虑减去节假日或周末休息日
			$tmptime= $starttime + 24*3600*($diff_date-1);//从enddate的前一天开始算起，判断是否是节假日
			$weekday = date('N', $tmptime);
			$tmpday = date('Y-m-d', $tmptime);
			$if_holiday = false;//是否节假日
			if(is_array($exception)){
				$if_holiday = in_array($tmpday,$exception);
			}else{
				$if_holiday = ($exception==$tmpday);
			}
			if( $weekday>$word_day || $if_holiday){//是周末或者节假日，则减去24小时
				$diff_hour=$diff_hour-24;
			}
			$diff_date--;
		}
		$return_date=number_format($diff_hour/24,2);
		return $return_date;
	}
	/**默认取有效的供应商
	 *
	*/
	function GetSupplierList()
	{
		$sql = "SELECT `id`,`name`,`status` FROM `app_processor_info` WHERE `status`=1";
		$data = $this->db()->getAll($sql);
		return $data;
	}
	/**
	 * 获取等钻平均时长列表
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @return number
	 */
	function page_Avg_wait_diamond_List ($where,$page,$pageSize=10,$useCache=true)
	{
		$str='';
		if(isset($where['start_time']) && $where['start_time']){
			$str.=" and p.dotime>='{$where['start_time']}' ";
		}
		if(isset($where['end_time']) && $where['end_time']){
			$str.=" and p.dotime<='{$where['end_time']}' ";
		}
		if(isset($where['style_sn']) && $where['style_sn']){
			$str.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if(isset($where['prc_name']) && $where['prc_name']){
			$str.=" and p.prc_name like '%{$where['prc_name']}%' ";
		}
		if(isset($where['from_type']) && $where['from_type']){
			$str.=" and p.from_type='{$where['from_type']}' ";
		}
        if(isset($where['diamond_type']) && !empty($where['diamond_type'])){
            $str.=" and p.diamond_type='{$where['diamond_type']}' ";
        }
        if(isset($where['qiban_type']) && $where['qiban_type']!='-1'){
            $str.=" and p.qiban_type='{$where['qiban_type']}' ";
        }
		if(isset($where['prc_ids']) && is_array($where['prc_ids']) && !empty($where['prc_ids'])){
			$prc_ids='';
			foreach ($where['prc_ids'] as $val){
				$prc_ids.="'{$val}',";
			}
			$prc_ids=trim($prc_ids,',');
			$str.=" and p.prc_id in ({$prc_ids}) ";
		}
		if(isset($where['opra_unames']) && is_array($where['opra_unames']) && !empty($where['opra_unames'])){
			$opra_unames='';
			foreach ($where['opra_unames'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$str.=" and p.opra_uname in ({$opra_unames}) ";
		}
		$sql="SELECT dotime,SUM(wd_day) sum_day,COUNT(1) buchan_num
            FROM `kucun_bak`.`product_info_wait_diamond` p where dotime = left(wait_dia_endtime,10) ".$str;
        $sql.=" GROUP by dotime ORDER BY dotime DESC";
        return $data = $this->db()->getAll($sql);
	}
	/**
	 * 获取等钻平均时长详细列表，根据工厂、采购者
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @return number
	 */
	function page_Avg_wait_diamond_detail_List ($where,$page,$pageSize=10,$useCache=true)
	{
		$str='';
		if(isset($where['start_time']) && $where['start_time']){
			$str.=" and p.dotime>='{$where['start_time']}' ";
		}
		if(isset($where['end_time']) && $where['end_time']){
			$str.=" and p.dotime<='{$where['end_time']}' ";
		}
		if(isset($where['style_sn']) && $where['style_sn']){
			$str.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if(isset($where['prc_name']) && $where['prc_name']){
			$str.=" and p.prc_name like '%{$where['prc_name']}%' ";
		}
		if(isset($where['from_type']) && $where['from_type']){
			$str.=" and p.from_type='{$where['from_type']}' ";
		}
        if(isset($where['diamond_type']) && !empty($where['diamond_type'])){
            $str.=" and p.diamond_type='{$where['diamond_type']}' ";
        }
        if(isset($where['qiban_type']) && $where['qiban_type']!='-1'){
            $str.=" and p.qiban_type='{$where['qiban_type']}' ";
        }
		if(isset($where['prc_ids']) && is_array($where['prc_ids']) && !empty($where['prc_ids'])){
			$prc_ids='';
			foreach ($where['prc_ids'] as $val){
				$prc_ids.="'{$val}',";
			}
			$prc_ids=trim($prc_ids,',');
			$str.=" and p.prc_id in ({$prc_ids}) ";
		}
		if(isset($where['opra_unames']) && is_array($where['opra_unames']) && !empty($where['opra_unames'])){
			$opra_unames='';
			foreach ($where['opra_unames'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$str.=" and p.opra_uname in ({$opra_unames}) ";
		}
		$sql="SELECT dotime,prc_name,opra_uname,SUM(wd_day) sum_day,COUNT(1) buchan_num
            FROM `kucun_bak`.`product_info_wait_diamond` p where dotime = left(wait_dia_endtime,10)  ".$str;
        $sql.=" GROUP by dotime,opra_uname ORDER BY dotime DESC,opra_uname DESC";
		//计算分页
		$data['pageSize']=$pageSize;
		$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
		$data['recordCount'] = $this->db()->getAll($countSql);
		$data['recordCount']=count($data['recordCount']);
		$data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
		$data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
		$data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
		$data['isFirst'] = $data['page'] > 1 ? false : true;
		$data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
		$data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
		$data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
        $data['data'] = $this->db()->getAll($data['sql']);
		return $data;
    }
		
	/**
	 * 获取等钻平均时长详细列表，根据工厂、采购者
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @return number
	 */
	function page_Avg_wait_diamond_detail_third_List ($where,$page,$pageSize=10,$useCache=true)
	{
		$str='';
		if(isset($where['start_time']) && $where['start_time']){
			$str.=" and p.dotime>='{$where['start_time']}' ";
		}
		if(isset($where['end_time']) && $where['end_time']){
			$str.=" and p.dotime<='{$where['end_time']}' ";
		}
		if(isset($where['style_sn']) && $where['style_sn']){
			$str.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if(isset($where['prc_name']) && $where['prc_name']){
			$str.=" and p.prc_name like '%{$where['prc_name']}%' ";
		}
		if(isset($where['from_type']) && $where['from_type']){
			$str.=" and p.from_type='{$where['from_type']}' ";
		}
        if(isset($where['diamond_type']) && !empty($where['diamond_type'])){
            $str.=" and p.diamond_type='{$where['diamond_type']}' ";
        }
        if(isset($where['qiban_type']) && $where['qiban_type']!='-1'){
            $str.=" and p.qiban_type='{$where['qiban_type']}' ";
        }
		if(isset($where['prc_ids']) && is_array($where['prc_ids']) && !empty($where['prc_ids'])){
			$prc_ids='';
			foreach ($where['prc_ids'] as $val){
				$prc_ids.="'{$val}',";
			}
			$prc_ids=trim($prc_ids,',');
			$str.=" and p.prc_id in ({$prc_ids}) ";
		}
		if(isset($where['opra_unames']) && is_array($where['opra_unames']) && !empty($where['opra_unames'])){
			$opra_unames='';
			foreach ($where['opra_unames'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$str.=" and p.opra_uname in ({$opra_unames}) ";
		}
		$sql="SELECT *
            FROM `kucun_bak`.`product_info_wait_diamond` p where dotime = left(wait_dia_endtime,10) ".$str;
        $sql.=" ORDER BY dotime DESC,opra_uname DESC";
		//计算分页
		$data['pageSize']=$pageSize;
		$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
		$data['recordCount'] = $this->db()->getAll($countSql);
		$data['recordCount']=count($data['recordCount']);
		$data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
		$data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
		$data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
		$data['isFirst'] = $data['page'] > 1 ? false : true;
		$data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
		$data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
		$data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
        $data['data'] = $this->db()->getAll($data['sql']);
		return $data;
    }
		/**
		 * 获取等钻超期列表
		 * @param unknown $where
		 * @param unknown $page
		 * @param number $pageSize
		 * @param string $useCache
		 * @return number
		 */
		function page_Avg_wait_diamond_lated_List ($where,$page,$pageSize=10,$useCache=true)
		{
			$exception='';//节假日
			$str='';
			if($where['start_time']){
				$str.=" and p.add_time>='{$where['start_time']}' ";
			}
			if($where['end_time']){
				$str.=" and p.add_time<='{$where['end_time']} 23:59:59' ";
			}
			if($where['style_sn']){
				$str.=" and p.style_sn='{$where['style_sn']}' ";
			}
			if($where['prc_name']){
				$str.=" and p.prc_name like '%{$where['prc_name']}%' ";
			}
			if($where['from_type']){
				$str.=" and p.from_type='{$where['from_type']}' ";
			}
            if(isset($where['diamond_type']) && !empty($where['diamond_type'])){
                $str.=" and p.diamond_type='{$where['diamond_type']}' ";
            }
            if(isset($where['qiban_type']) && $where['qiban_type']!='-1'){
                $str.=" and p.qiban_type='{$where['qiban_type']}' ";
            }
			if($where['opra_uname']){
				$str.=" and p.opra_uname='{$where['opra_uname']}' ";
			}
			$sql="SELECT add_time, 0 as avg_wait_diamond_time, LEFT(add_time,10) count_date, COUNT(*) count FROM (
			SELECT p.add_time, l.bc_id,l.time next_time,temp.time wait_time,l.remark next_remark,temp.remark  FROM `product_opra_log`  l JOIN  (
			SELECT * FROM `product_opra_log`  WHERE remark LIKE '%等钻%'
			) temp ON  temp.bc_id=l.bc_id
			JOIN product_info p ON p.id=l.bc_id
			WHERE  l.time>temp.time {$str}  GROUP BY l.bc_id ORDER BY l.time ASC
			) wait_diamond GROUP BY LEFT(add_time,10) ORDER BY wait_time DESC";
		
			//	echo $sql;exit;
			$product_time_list=array();
			//计算分页
			$data['pageSize']=$pageSize;
			$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
			$data['recordCount'] = $this->db()->getAll($countSql);
			$data['recordCount']=count($data['recordCount']);
			$data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
			$data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
			$data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
			$data['isFirst'] = $data['page'] > 1 ? false : true;
			$data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
			$data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
			$data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
	        $data['data'] = $this->db()->getAll($data['sql']);
		//计算平均等钻时长，等钻时长=(订单操作等钻下一步动作的时间-订单操作等钻日期)的用时 -法定节假日放假时长
			$start_key=count($data['data'])-1;
			$detail_start_time=$data['data'][$start_key]['count_date'];
			$detail_end_time=$data['data'][0]['count_date'];
			$str.=' and p.add_time>="'.$detail_start_time.'" and p.add_time<="'.$detail_end_time.' 23:59:59"';
			$detail_sql="SELECT l.bc_id,l.time next_time,temp.time wait_time,l.remark next_remark,temp.remark  FROM `product_opra_log`  l JOIN  (
			SELECT * FROM `product_opra_log`  WHERE remark LIKE '%等钻%'
			) temp ON  temp.bc_id=l.bc_id
			JOIN product_info p ON p.id=l.bc_id
			WHERE  l.time>temp.time {$str}  GROUP BY l.bc_id ORDER BY l.time ASC";
			$detail_data=$this->db()->getAll($detail_sql);
			$data['detail_data']=array();
			foreach($detail_data as $value){
			$key=substr($value['wait_time'],0,10);
			$data['detail_data'][$key][]=$this->get_diff_hour($value['wait_time'],$value['next_time'],7,$exception='');
			}
			if($data['detail_data']){
			foreach ($data['detail_data'] as & $val){
			$val['avg_wait_diamond_time']=number_format(array_sum($val)/count($val),2);
			}
			}
			return $data;
			}
		public function get_channel_name($id)
		{
			$SalesChannelsModel = new SalesChannelsModel(1);
			if($id == 0)
			{
				return '';
			}
			$channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", array('id'=>$id));
			if($channellist)
			{
				return $channellist[0]['channel_name'];
			}
		}
		//获取来源名称
		public function get_source_name($source_id)
		{
			$sql = "SELECT `source_name` FROM `customer_sources` WHERE `id` = '".$source_id."'";
			$name = DB::cn(1)->getOne($sql);
			return $name;
		}
		/**
		 * 获取等钻平均时长报表明细数据
		 * @param unknown $where
		 * @param unknown $page
		 * @param number $pageSize
		 * @param string $useCache
		 * @param number $time_type
		 * @return unknown
		 */
		public function pageWaitDiamondCsvDetailList($where,$page,$pageSize=10,$useCache=true,$time_type=1){
			$sql=$this->getWaitDiamondCsvDetailSql($where,$time_type);
			$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
			return $data;
		}
		/**
		 * 等钻平均时长报表明细报表sql
		 * @param unknown $where
		 * @return string
		 */
		function getWaitDiamondCsvDetailSql($where=array(),$time_type=1)
		{
			$exception='';//节假日
			$str='';
			if($where['start_time']){
				$str.=" and p.add_time>='{$where['start_time']}' ";
			}
			if($where['end_time']){
				$str.=" and p.add_time<='{$where['end_time']} 23:59:59' ";
			}
			if($where['style_sn']){
				$str.=" and p.style_sn='{$where['style_sn']}' ";
			}
			if($where['prc_name']){
				$str.=" and p.prc_name like '%{$where['prc_name']}%' ";
			}
			if($where['from_type']){
				$str.=" and p.from_type='{$where['from_type']}' ";
			}
            if(isset($where['diamond_type']) && !empty($where['diamond_type'])){
                $str.=" and p.diamond_type='{$where['diamond_type']}' ";
            }
            if(isset($where['qiban_type']) && $where['qiban_type']!='-1'){
                $str.=" and p.qiban_type='{$where['qiban_type']}' ";
            }
			if(isset($where['prc_ids']) and !empty($where['prc_ids']))
			{
				$where['prc_ids']=implode(',',$where['prc_ids']);
				$where['prc_ids'] && $str .= " AND p.prc_id in ({$where['prc_ids']})";
			}
			elseif(isset($where['prc_ids_string']) and !empty($where['prc_ids_string']))
			{
				$str .= " AND p.prc_id in ({$where['prc_ids_string']})";
			}
			if($where['opra_unames'] && isset($where['opra_unames'][0]) && $where['opra_unames'][0]){
				$opra_unames='';
				foreach ($where['opra_unames'] as $val){
					$opra_unames.="'{$val}',";
				}
				$opra_unames=trim($opra_unames,',');
				$opra_unames && $str.=" and p.opra_uname in ({$opra_unames}) ";
			}
			elseif($where['opra_uname_string']){
				$where['opra_uname_string']=explode(',',$where['opra_uname_string']);
				$opra_unames='';
				foreach($where['opra_uname_string'] as $val){
					$opra_unames.="'{$val}',";
				}
				$opra_unames=trim($opra_unames,',');
				$str.=" and p.opra_uname in ({$opra_unames}) ";
			}
			$bc_id_sql="SELECT bc_id FROM (
			SELECT p.add_time, l.bc_id,l.time next_time,temp.time wait_time,l.remark next_remark,temp.remark  FROM `product_opra_log`  l JOIN  (
			SELECT * FROM `product_opra_log`  WHERE remark LIKE '%等钻%'
			) temp ON  temp.bc_id=l.bc_id
			JOIN product_info p ON p.id=l.bc_id
			WHERE  l.time>temp.time {$str}  GROUP BY l.bc_id ORDER BY l.time ASC
			) wait_diamond ";
			$sql = "SELECT main.*,o.max_time as factory_time,r.remark as opra_remark,r.time,c.peishi_status FROM `".$this->table()."` as main left join (select bc_id,MAX(time) as max_time from product_opra_log where status=3 group by bc_id) as o on o.bc_id=main.id left join (SELECT t.remark,t.bc_id,t.time FROM product_opra_log t INNER JOIN (SELECT bc_id, MAX(id) AS max FROM product_opra_log GROUP BY bc_id ) as tm ON t.bc_id = tm.bc_id AND t.id=tm.max) as r on r.bc_id=main.id";
			$sql.=" left join product_info_4c c on main.id=c.id where 1=1";
		
			$sql .= " AND main.id in ({$bc_id_sql})";
		
			//add end
			//echo $sql;exit;
			return $sql;
		}



        
	/**
	 * 获取等钻平均时长详细列表，根据工厂、采购者
	 */
	function wait_diamond_lated_detail_List ($where,$page,$pageSize=10,$useCache=true)
	{
        $exception='';//节假日
        $str='';
        if($where['start_time']){
            $str.=" and p.dotime>='{$where['start_time']}' ";
        }
        if($where['end_time']){
            $str.=" and p.dotime<='{$where['end_time']}' ";
        }
        if($where['style_sn']){
            $str.=" and p.style_sn='{$where['style_sn']}' ";
        }
        if($where['prc_name']){
            $str.=" and p.prc_name like '%{$where['prc_name']}%' ";
        }
        if($where['from_type']){
            $str.=" and p.from_type='{$where['from_type']}' ";
        }
        if(isset($where['diamond_type']) && !empty($where['diamond_type'])){
            $str.=" and p.diamond_type='{$where['diamond_type']}' ";
        }
        if(isset($where['qiban_type']) && $where['qiban_type']!='-1'){
            $str.=" and p.qiban_type='{$where['qiban_type']}' ";
        }
        if($where['opra_uname']){
            $str.=" and p.opra_uname='{$where['opra_uname']}' ";
        }
        if(isset($where['opra_unames_string']) && !empty($where['opra_unames_string'])){
            $str.=" and p.opra_uname in ('".$where['opra_unames_string']."') ";
        }
        if(isset($where['prc_ids_string']) && !empty($where['prc_ids_string'])){
            $str.=" and p.prc_id in (".$where['prc_ids_string'].") ";
        }
        $sql="
        SELECT 
            dotime,
            prc_name,
            opra_uname,
            SUM(IF(left(wait_dia_finishtime,10) = dotime,1,0)) as  wd_should_today,
            SUM(IF(left(wait_dia_finishtime,10) = dotime AND wait_dia_endtime='0000-00-00 00:00:00',1,0)) as wd_un_today,
            SUM(IF(left(wait_dia_finishtime,10) = dotime AND wait_dia_endtime!='0000-00-00 00:00:00',1,0)) as wd_on,
            SUM(IF(left(wait_dia_endtime,10) = dotime ,1,0)) as wd_real_today
        FROM 
            kucun_bak.product_info_wait_diamond p
        WHERE p.wait_dia_starttime != '0000-00-00 00:00:00' ".$str;
        $sql .= " group by dotime,opra_uname 
                  having wd_should_today>0
                  order by dotime desc,opra_uname desc;";
        //echo $sql;
        $data = $this->db()->getAll($sql);
        return $data;
    }
    /**
     * 获取等钻超期列表
     */
    function wait_diamond_lated_List ($where,$page,$pageSize=10,$useCache=true)
    {
        $exception='';//节假日
        $str='';
        if($where['start_time']){
            $str.=" and p.dotime>='{$where['start_time']}' ";
        }
        if($where['end_time']){
            $str.=" and p.dotime<='{$where['end_time']}' ";
        }
        if($where['style_sn']){
            $str.=" and p.style_sn='{$where['style_sn']}' ";
        }
        if($where['prc_name']){
            $str.=" and p.prc_name like '%{$where['prc_name']}%' ";
        }
        if($where['from_type']){
            $str.=" and p.from_type='{$where['from_type']}' ";
        }
        if(isset($where['diamond_type']) && !empty($where['diamond_type'])){
            $str.=" and p.diamond_type='{$where['diamond_type']}' ";
        }
        if(isset($where['qiban_type']) && $where['qiban_type']!='-1'){
            $str.=" and p.qiban_type='{$where['qiban_type']}' ";
        }
        if($where['opra_uname']){
            $str.=" and p.opra_uname='{$where['opra_uname']}' ";
        }
        if(isset($where['opra_unames_string']) && !empty($where['opra_unames_string'])){
            $str.=" and p.opra_uname in ('".$where['opra_unames_string']."') ";
        }
        if(isset($where['prc_ids_string']) && !empty($where['prc_ids_string'])){
            $str.=" and p.prc_id in (".$where['prc_ids_string'].") ";
        }
        $sql="
        SELECT 
            dotime,
        	SUM(IF(wait_dia_endtime = '0000-00-00 00:00:00',1,0)) as wd_should_all, 
            SUM(IF(left(wait_dia_finishtime,10) = dotime,1,0)) as  wd_should_today,
            SUM(IF(left(wait_dia_finishtime,10) = dotime AND wait_dia_endtime='0000-00-00 00:00:00',1,0)) as wd_un_today,
            SUM(IF(left(wait_dia_finishtime,10) = dotime AND wait_dia_endtime!='0000-00-00 00:00:00',1,0)) as wd_on,
            SUM(IF(left(wait_dia_endtime,10) = dotime AND wait_dia_endtime!='0000-00-00 00:00:00',1,0)) as wd_real_today
        FROM kucun_bak.product_info_wait_diamond p
        WHERE p.wait_dia_starttime != '0000-00-00 00:00:00' ".$str;
        $sql .= " group by dotime order by dotime desc;";
        //echo $sql;
        $data = $this->db()->getAll($sql);
        return $data;
    }
	/**
	 * 获取等钻平均时长详细列表，根据工厂、采购者
	 */
	function wait_diamond_lated_detail_Info ($where)
	{
        $exception='';//节假日
        $str='';
        if(isset($where['start_time']) && $where['start_time']){
            $str.=" and p.dotime>='{$where['start_time']}' ";
        }
        if(isset($where['end_time']) && $where['end_time']){
            $str.=" and p.dotime<='{$where['end_time']}' ";
        }
        if(isset($where['style_sn']) && $where['style_sn']){
            $str.=" and p.style_sn='{$where['style_sn']}' ";
        }
        if(isset($where['prc_name']) && $where['prc_name']){
            $str.=" and p.prc_name like '%{$where['prc_name']}%' ";
        }
        if(isset($where['from_type']) && $where['from_type']){
            $str.=" and p.from_type='{$where['from_type']}' ";
        }
        if(isset($where['diamond_type']) && !empty($where['diamond_type'])){
            $str.=" and p.diamond_type='{$where['diamond_type']}' ";
        }
        if(isset($where['qiban_type']) && $where['qiban_type']!='-1'){
            $str.=" and p.qiban_type='{$where['qiban_type']}' ";
        }
        if(isset($where['opra_uname']) && $where['opra_uname']){
            $str.=" and p.opra_uname='{$where['opra_uname']}' ";
        }
        if(isset($where['opra_unames_string']) && !empty($where['opra_unames_string'])){
            $str.=" and p.opra_uname in ('".$where['opra_unames_string']."') ";
        }
        if(isset($where['prc_ids_string']) && !empty($where['prc_ids_string'])){
            $str.=" and p.prc_id in (".$where['prc_ids_string'].") ";
        }
        $sql="SELECT 
            p.dotime,
            p.bc_sn,
            p.wait_dia_starttime,
            p.wait_dia_endtime,
            p.wait_dia_finishtime
        FROM 
            kucun_bak.product_info_wait_diamond p
        WHERE 
            p.wait_dia_starttime != '0000-00-00 00:00:00' 
            AND p.wait_dia_endtime = '0000-00-00 00:00:00' ".$str;
        $sql .= " order by dotime desc,opra_uname desc;";
        //echo $sql;die;
        $data = $this->db()->getAll($sql);
        return $data;
    }

    public function wait_diamond_All()
    {
        $sql="SELECT COUNT(1) cnt,SUM(wait_dia_endtime='0000-00-00 00:00:00') un_cnt FROM kela_supplier.product_info 
            WHERE wait_dia_starttime!='0000-00-00 00:00:00';
        ";
        return $this->db()->getRow($sql);
    }

    //根据订单取出布产单信息
    public function getBcSnByOrderSn($order_sn)
    {
        # code...
        $sql = "select `bc_sn`,`bc_style`,`prc_name`,`esmt_time`,`order_time` from `kela_supplier`.`product_info` where `p_sn` = '$order_sn'";
        //echo $sql;die;
        return $this->db()->getAll($sql);
    }
}