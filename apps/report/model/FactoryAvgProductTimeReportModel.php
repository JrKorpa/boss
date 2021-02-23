<?php
/**
 *  -------------------------------------------------
 *   @file		: FactoryAvgProductTimeReportModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-18 18:17:59
 *   @update	:
 *  -------------------------------------------------
 */
class FactoryAvgProductTimeReportModel extends Model
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
	 * 获取工厂平均生产时长第一层数据
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @return number
	 */
	function pageAvgproducttimelList ($where,$page,$pageSize=10,$useCache=true)
	{
		$exception='';//节假日
		$sql="SELECT 
            SUM(cc_day) day_sum,count(1) cnt,p.dotime
        FROM 
            kucun_bak.`product_info_chuchang` p 
        WHERE 1=1 ";
		$str=" ";
		if(isset($where['start_time']) && !empty($where['start_time'])){
			$str.=" and p.dotime>='{$where['start_time']}' ";
		}
		if(isset($where['end_time']) && !empty($where['end_time'])){
			$str.=" and p.dotime<='{$where['end_time']}' ";
		}
		if(isset($where['style_sn']) && !empty($where['style_sn'])){
			$str.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if(isset($where['prc_ids']) and is_array($where['prc_ids']))
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			if($where['prc_ids'])
			$str .= " AND p.prc_id in ({$where['prc_ids']})";
		}
		if(isset($where['prc_ids_string']) && $where['prc_ids_string'] )
		{
			$where['prc_ids_string']=trim($where['prc_ids_string'],' ');
			$where['prc_ids_string'] && $str .= " AND p.prc_id in ({$where['prc_ids_string']})";
		}
		if(isset($where['from_type']) && !empty($where['from_type'])){
			$str.=" and p.from_type='{$where['from_type']}' ";
		}
		if(isset($where['diamond_type']) && !empty($where['diamond_type'])){
			$str.=" and p.diamond_type='{$where['diamond_type']}' ";
		}
	   if(isset($where['qiban_type']) && $where['qiban_type']!='-1'){
            $str.=" and p.qiban_type='{$where['qiban_type']}' ";
        }
		if(isset($where['style_type']) && !empty($where['style_type'])){
			$str.=" and p.kuan_type='{$where['style_type']}' ";
		}
		if(isset($where['xilie_ids']) && $where['xilie_ids'] && isset($where['xilie_ids'][0]) && $where['xilie_ids'][0]){
			$xilie_ids=' and (';
			foreach ($where['xilie_ids'] as $k=> $val){
				if($k==0){
					$xilie_ids.=" p.xilie like '%{$val},%'";
				}else{
					$xilie_ids.=" or p.xilie like '%{$val},%'";
				}
			}
			$xilie_ids.=") ";
				
			$xilie_ids && $str.=$xilie_ids;
		}
		if(isset($where['opra_unames']) && $where['opra_unames'] && isset($where['opra_unames'][0]) && $where['opra_unames'][0]){
			$opra_unames='';
			foreach ($where['opra_unames'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$opra_unames && $str.=" and p.opra_uname in ({$opra_unames}) ";
		}
		
		$sql=$sql.$str."   GROUP BY p.dotime ORDER BY p.dotime DESC ";	
		//echo $sql;die;
		$product_time_list=array();
		//计算分页
		$data['pageSize']=$pageSize;
		$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
		$data['recordCount'] = $this->db()->getAll($countSql);
		$data['recordCount'] = count($data['recordCount']);
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
	 * 获取工厂平均生产时长第一层数据
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @return number
	 */
	function pageAvgproducttimelList_tj ($where)
	{
		$sql="SELECT 
            SUM(cc_day) day_sum,count(1) cnt
        FROM 
            kucun_bak.`product_info_chuchang` p 
        WHERE 1=1 ";
		$str=" ";
		if(isset($where['start_time']) && !empty($where['start_time'])){
			$str.=" and p.dotime>='{$where['start_time']}' ";
		}
		if(isset($where['end_time']) && !empty($where['end_time'])){
			$str.=" and p.dotime<='{$where['end_time']}' ";
		}
		if(isset($where['style_sn']) && !empty($where['style_sn'])){
			$str.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if(isset($where['prc_ids']) and is_array($where['prc_ids']))
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			if($where['prc_ids'])
			$str .= " AND p.prc_id in ({$where['prc_ids']})";
		}
		if(isset($where['prc_ids_string']) && $where['prc_ids_string'] )
		{
			$where['prc_ids_string']=trim($where['prc_ids_string'],' ');
			$where['prc_ids_string'] && $str .= " AND p.prc_id in ({$where['prc_ids_string']})";
		}
		if(isset($where['from_type']) && !empty($where['from_type'])){
			$str.=" and p.from_type='{$where['from_type']}' ";
		}
		if(isset($where['diamond_type']) && !empty($where['diamond_type'])){
			$str.=" and p.diamond_type='{$where['diamond_type']}' ";
		}
		if(isset($where['qiban_type']) && $where['qiban_type']!='-1'){
			$str.=" and p.qiban_type='{$where['qiban_type']}' ";
		}
		if(isset($where['style_type']) && !empty($where['style_type'])){
			$str.=" and p.kuan_type='{$where['style_type']}' ";
		}
		if(isset($where['xilie_ids']) && $where['xilie_ids'] && isset($where['xilie_ids'][0]) && $where['xilie_ids'][0]){
			$xilie_ids=' and (';
			foreach ($where['xilie_ids'] as $k=> $val){
				if($k==0){
					$xilie_ids.=" p.xilie like '%{$val},%'";
				}else{
					$xilie_ids.=" or p.xilie like '%{$val},%'";
				}
			}
			$xilie_ids.=") ";
		
			$xilie_ids && $str.=$xilie_ids;
		}
		if(isset($where['opra_unames']) && $where['opra_unames'] && isset($where['opra_unames'][0]) && $where['opra_unames'][0]){
			$opra_unames='';
			foreach ($where['opra_unames'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$opra_unames && $str.=" and p.opra_uname in ({$opra_unames}) ";
		}
		$sql=$sql.$str;	
        $data = $this->db()->getRow($sql);
		return $data;
	}
	/**
	 * 获取工厂平均生产时长第一层数据
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @return number
	 */
	function pageAvgproducttimelList_2 ($where,$page,$pageSize=10,$useCache=true)
	{
		$exception='';//节假日
		$sql="SELECT 
            SUM(cc_day) day_sum,count(1) cnt,p.dotime,p.prc_name,p.opra_uname
        FROM 
            kucun_bak.`product_info_chuchang` p 
        WHERE 1=1 ";
		$str=" ";
		if(isset($where['start_time']) && !empty($where['start_time'])){
			$str.=" and p.dotime>='{$where['start_time']}' ";
		}
		if(isset($where['end_time']) && !empty($where['end_time'])){
			$str.=" and p.dotime<='{$where['end_time']}' ";
		}
		if(isset($where['style_sn']) && !empty($where['style_sn'])){
			$str.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if(isset($where['prc_ids']) and is_array($where['prc_ids']))
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			if($where['prc_ids'])
			$str .= " AND p.prc_id in ({$where['prc_ids']})";
		}
		if(isset($where['prc_ids_string']) && $where['prc_ids_string'] )
		{
			$where['prc_ids_string']=trim($where['prc_ids_string'],' ');
			$where['prc_ids_string'] && $str .= " AND p.prc_id in ({$where['prc_ids_string']})";
		}
		if(isset($where['from_type']) && !empty($where['from_type'])){
			$str.=" and p.from_type='{$where['from_type']}' ";
		}
		if(isset($where['diamond_type']) && !empty($where['diamond_type'])){
			$str.=" and p.diamond_type='{$where['diamond_type']}' ";
		}
		if(isset($where['qiban_type']) && $where['qiban_type']!='-1'){
			$str.=" and p.qiban_type='{$where['qiban_type']}' ";
		}
		if(isset($where['style_type']) && !empty($where['style_type'])){
			$str.=" and p.kuan_type='{$where['style_type']}' ";
		}
		if(isset($where['xilie_ids']) && $where['xilie_ids'] && isset($where['xilie_ids'][0]) && $where['xilie_ids'][0]){
			$xilie_ids=' and (';
			foreach ($where['xilie_ids'] as $k=> $val){
				if($k==0){
					$xilie_ids.=" p.xilie like '%{$val},%'";
				}else{
					$xilie_ids.=" or p.xilie like '%{$val},%'";
				}
			}
			$xilie_ids.=") ";
		
			$xilie_ids && $str.=$xilie_ids;
		}
		if(isset($where['opra_unames']) && $where['opra_unames'] && isset($where['opra_unames'][0]) && $where['opra_unames'][0]){
			$opra_unames='';
			foreach ($where['opra_unames'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$opra_unames && $str.=" and p.opra_uname in ({$opra_unames}) ";
		}
		$sql=$sql.$str."   GROUP BY p.dotime,p.prc_name ORDER BY p.dotime DESC,p.prc_name DESC ";	
		$product_time_list=array();
		//计算分页
		$data['pageSize']=$pageSize;
		$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
		$data['recordCount'] = $this->db()->getAll($countSql);
		$data['recordCount'] = count($data['recordCount']);
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
	 * 获取工厂平均生产时长第一层数据
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @return number
	 */
	function pageAvgproducttimelList_3 ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql="SELECT 
             p.*
        FROM 
            kucun_bak.`product_info_chuchang` p 
        WHERE 1=1 ";
		$str=" ";
		if(isset($where['start_time']) && !empty($where['start_time'])){
			$str.=" and p.dotime>='{$where['start_time']}' ";
		}
		if(isset($where['end_time']) && !empty($where['end_time'])){
			$str.=" and p.dotime<='{$where['end_time']}' ";
		}
		if(isset($where['style_sn']) && !empty($where['style_sn'])){
			$str.=" and p.style_sn='{$where['style_sn']}' ";
		}
		if(isset($where['prc_ids']) and is_array($where['prc_ids']))
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			if($where['prc_ids'])
			$str .= " AND p.prc_id in ({$where['prc_ids']})";
		}
		if(isset($where['prc_ids_string']) && $where['prc_ids_string'] )
		{
			$where['prc_ids_string']=trim($where['prc_ids_string'],' ');
			$where['prc_ids_string'] && $str .= " AND p.prc_id in ({$where['prc_ids_string']})";
		}
		if(isset($where['from_type']) && !empty($where['from_type'])){
			$str.=" and p.from_type='{$where['from_type']}' ";
		}
		if(isset($where['diamond_type']) && !empty($where['diamond_type'])){
			$str.=" and p.diamond_type='{$where['diamond_type']}' ";
		}
		if(isset($where['qiban_type']) && $where['qiban_type']!='-1'){
			$str.=" and p.qiban_type='{$where['qiban_type']}' ";
		}
		if(isset($where['style_type']) && !empty($where['style_type'])){
			$str.=" and p.kuan_type='{$where['style_type']}' ";
		}
		if(isset($where['xilie_ids']) && $where['xilie_ids'] && isset($where['xilie_ids'][0]) && $where['xilie_ids'][0]){
			$xilie_ids=' and (';
			foreach ($where['xilie_ids'] as $k=> $val){
				if($k==0){
					$xilie_ids.=" p.xilie like '%{$val},%'";
				}else{
					$xilie_ids.=" or p.xilie like '%{$val},%'";
				}
			}
			$xilie_ids.=") ";
		
			$xilie_ids && $str.=$xilie_ids;
		}
		if(isset($where['opra_unames']) && $where['opra_unames'] && isset($where['opra_unames'][0]) && $where['opra_unames'][0]){
			$opra_unames='';
			foreach ($where['opra_unames'] as $val){
				$opra_unames.="'{$val}',";
			}
			$opra_unames=trim($opra_unames,',');
			$opra_unames && $str.=" and p.opra_uname in ({$opra_unames}) ";
		}
		$sql=$sql.$str." ORDER BY p.dotime DESC,p.prc_name DESC ";	
		//echo $sql;die();
		//计算分页
		$data['pageSize']=$pageSize;
		$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
		$data['recordCount'] = $this->db()->getAll($countSql);
		$data['recordCount'] = count($data['recordCount']);
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

	
}

