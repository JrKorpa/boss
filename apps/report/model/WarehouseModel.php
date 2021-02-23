<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseModel.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'warehouse';
		$this->_dataObject = array("id"=>" ",
		"name"=>"仓库名称",
		"code"=>" ",
		"remark"=>"备注",
		"create_time"=>" ",
		"create_user"=>" ",
		"lock"=>"锁定状态 0未锁定/1锁定",
		"type"=>"仓库类型",
		"is_delete"=>"是否有效；0为无效，1为有效");
		parent::__construct($id,$strConn);
	}
	/**
	 *	pageList，分页列表
	 *
	 *	@url ApplicationController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
        $doTime = false;
        $tableName = "warehouse_goods";
		$str='';
        if(isset($where['dotime_string']))
        {
            $doTime = str_replace("-","",$where['dotime_string']);
            $tableName .= $doTime;
                $c_sql = "SHOW TABLES from kucun_bak ";
                $c_ret = $this->db(47)->getAll($c_sql);
                $tb_ret = array_column($c_ret,'Tables_in_kucun_bak');
                if(!in_array($tableName,$tb_ret)){
                    return false;
                }
        }
		$str='';
		if(isset($where['warehouse_ids_string'])&& $where['warehouse_ids_string']){
			$warehouse_ids_string=trim($where['warehouse_ids_string'],',');
			$str.=" and wg.warehouse_id in ({$warehouse_ids_string})";
		}else{
            $where['warehouse_ids_string']='';
        }
		if(isset($where['types_string'])&& $where['types_string']){
			$types_string=trim($where['types_string'],',');
			$str.=" and wg.cangkuleixing_id in ({$types_string})";
		}else{
            $where['types_string']='';
        }
		$str_num=$str_price=$str;
		if(isset($where['company_ids_string'])&& $where['company_ids_string']){
			$company_ids_string=trim($where['company_ids_string'],',');
			$str_price.=" and wg.company_id in ({$company_ids_string})";
		}else{
            $where['company_ids_string']='';
        }
		$sql="SELECT SUM(wg.yuanshichengbenjia) warehouse_price,wg.company_id,count(*) count,wg.gongsi company_name   FROM kucun_bak.$tableName wg where 1 {$str_price} ";
		$sql .= " group by `wg`.`company_id` ORDER BY `wg`.`company_id` DESC";
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
        $d = $this->db()->getAll($data['sql']);

        if($d){
            $statistic_info = array();
            foreach($d as $k => $v){
                $statistic_info[]=array(
                    'dotime_string'=>$where['dotime_string'],
                    'company_name'=>$v['company_name'],
                    'count'=>$v['count'],
                    'warehouse_price'=>round($v['warehouse_price'],2),
                    'warehouse_ids_string'=>$where['warehouse_ids_string'],
                    'company_ids_string'=>$where['company_ids_string'],
                    'company_id'=>$v['company_id'],
                    'types_string'=>$where['types_string']
                );
            }
            $data['data'] = $statistic_info;
        }else{
            $data['data'] = false;
        }
		return $data;
	}
	/**
	 *	pageList，分页列表,第三层
	 *
	 *	@url ApplicationController/search
	 */
	function pageListThird ($where,$page,$pageSize=10,$useCache=true)
	{
        $doTime = false;
        $tableName = "warehouse_goods";
		$str='';
        if(isset($where['dotime_string']))
        {
            $doTime = str_replace("-","",$where['dotime_string']);
            $tableName .= $doTime;
                $c_sql = "SHOW TABLES from kucun_bak ";
                $c_ret = $this->db(47)->getAll($c_sql);
                $tb_ret = array_column($c_ret,'Tables_in_kucun_bak');
                if(!in_array($tableName,$tb_ret)){
                    return false;
                }
        }
		$str='';
		if(isset($where['warehouse_ids_string'])&& $where['warehouse_ids_string']){
			$warehouse_ids_string=trim($where['warehouse_ids_string'],',');
			$str.=" and wg.warehouse_id in ({$warehouse_ids_string})";
		}else{
            $where['warehouse_ids_string']='';
        }
		if(isset($where['types_string'])&& $where['types_string']){
			$types_string=trim($where['types_string'],',');
			$str.=" and wg.cangkuleixing_id in ({$types_string})";
		}else{
            $where['types_string']='';
        }
		$str_num=$str_price=$str;
		if(isset($where['company_ids_string'])&& $where['company_ids_string']){
			$company_ids_string=trim($where['company_ids_string'],',');
			$str_price.=" and wg.company_id in ({$company_ids_string})";
		}else{
            $where['company_ids_string']='';
        }
		
		$sql="SELECT SUM(wg.yuanshichengbenjia) warehouse_price,wg.company_id,wg.warehouse_id,wg.gongsi company_name,count(*) count ,wg.cangku warehouse_name  FROM kucun_bak.$tableName wg  where 1 {$str_price} ";
		$sql .= " group by `wg`.`company_id`,wg.warehouse_id ORDER BY `wg`.`company_id` DESC";
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
        $d = $this->db()->getAll($data['sql']);
        if($d){
            $statistic_info = array();
            foreach($d as $k => $v){
                $statistic_info[]=array(
                    'dotime_string'=>$where['dotime_string'],
                    'company_name'=>$v['company_name'],
                    'warehouse_name'=>$v['warehouse_name'],
                    'count'=>$v['count'],
                    'company_id'=>$v['company_id'],
                    'warehouse_id'=>$v['warehouse_id'],
                    'warehouse_price'=>round($v['warehouse_price'],2),
                    'warehouse_ids_string'=>$where['warehouse_ids_string'],
                    'company_ids_string'=>$where['company_ids_string'],
                    'types_string'=>$where['types_string']
                );
            }
            $data['data'] = $statistic_info;
        }else{
            $data['data'] = false;
        }
		return $data;
	}

	public function getAllhouse(){
		$sql = "SELECT `id`,`name`,code FROM `warehouse_shipping`.`".$this->table()."` WHERE `is_delete` = '1'";
		$houses = $this->db()->getAll($sql);
		return $houses;
	}
	
	/*
	*获取最新的采购成本
	*
	*/
	public function getNewestCaigouchengbenByGoodsId($goods_id){
		$sql = "select xianzaichengben_new from warehouse_shipping.warehouse_caigou_tiaozheng where goods_id='".$goods_id."' order by addtime desc limit 1";
		return $this->db()->getOne($sql);
	}
	
	/*
	*获取最新的调拨单
	*
	*/
	public function getNewestDiaoboBillByGoodsId($goods_id){
		$sql = "select bill_no from warehouse_shipping.warehouse_bill_goods where bill_type='M' AND goods_id='".$goods_id."' ORDER BY addtime desc limit 1";
		return $this->db()->getOne($sql);
	}

        public function select($dataArray='',$fieldArray='')
	{
		$fieldString = '';
		if(is_array($fieldArray) && !empty($fieldArray))
		{
			foreach($fieldArray as $key => $value){
				$fieldString .= ", $value";
				$fieldString = ltrim($fieldString,',');
			}
		}else
		{
			$fieldString = '*';
		}
	
		$DataString = '';
		if(is_array($dataArray) && !empty($dataArray)){
			foreach($dataArray as $key => $value){
				//$this->trim($value);
				$DataString .= " AND $key ='$value' ";
			}
		}
		$sql = "SELECT ".$fieldString." FROM ".$this->table()." WHERE 1 ";
		$sql .= $DataString;
		$data = $this->db()->getAll($sql);
		// print_r($data);exit;
		return $data;
	}
}