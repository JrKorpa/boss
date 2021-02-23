<?php
/**
 *  -------------------------------------------------
 *   @file		: warehousestatisticModel.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseStatisticModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse_statistic';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array();
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url OrderFqcController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT s.id,s.dt, sum(s.total_num) total_num,sum(s.cab_num) cab_num,sum(s.threeday_cab_num) threeday_cab_num,sum(s.all_price) all_price,sum(s.diff_price) diff_price,sum(s.threeday_diff_price) threeday_diff_price FROM `".$this->table()."` s JOIN `warehouse_rel` r  ON r.`warehouse_id`=s.`wh_id`";
		$str = '';
		if(isset($where['warehouse_string']) && $where['warehouse_string']){
			$str.="s.wh_name in({$where['warehouse_string']}) AND ";
		}
		if(isset($where['company_id']) && $where['company_id']){
			$str.="r.company_id='{$where['company_id']}' AND ";
		}
		if( (isset($where['time_start']) && $where['time_start']) || (isset($where['time_end']) && $where['time_end'])){
			if($where['time_start'])
				$str.="s.dt >='{$where['time_start']} 00:00:00' AND ";
			if($where['time_end'])
				$str.="s.dt <='{$where['time_end']} 23:59:59' AND";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " group by s.dt ORDER BY s.id DESC";
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
	 *	pageList，分页列表
	 *
	 *	@url OrderFqcController/search
	 */
	function pageList2 ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT s.id,dt, wh_name,wh_id, total_num, cab_num, threeday_cab_num, all_price, diff_price,threeday_diff_price FROM `".$this->table()."`  s JOIN `warehouse_rel` r  ON r.`warehouse_id`=s.`wh_id`";
		$str = '';
		if(isset($where['warehouse_string']) && $where['warehouse_string']){
			$where['warehouse_string']=explode(',',$where['warehouse_string']);
			$warehouse_names='';
			foreach($where['warehouse_string'] as $val){
				$warehouse_names.="'{$val}',";
			}
			$warehouse_names=trim($warehouse_names,',');
			$warehouse_names && $str.="wh_name in({$warehouse_names}) AND ";
		}
		
		if(isset($where['company_id']) && $where['company_id']){
			$str.="r.company_id='{$where['company_id']}' AND ";
		}
		if( (isset($where['time_start']) && $where['time_start']) || (isset($where['time_end']) && $where['time_end'])){
			if($where['time_start'])
				$str.="dt >='{$where['time_start']} 00:00:00' AND ";
			if($where['time_end'])
				$str.="dt <='{$where['time_end']} 23:59:59' AND";
		}
		elseif(isset($where['dt']) && $where['dt']){
			$str.="dt='{$where['dt']}' AND";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY s.`id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
  /**
   * 获取所有公司列表
   * */
   public function getCompanyList(){
        $model     = new CompanyModel(1);
		$company   = $model->getCompanyTree();//公司列表
		return $company;
            
   }
}

