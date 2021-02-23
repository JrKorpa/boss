<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseModel.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseKucunModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'kucunliang';
		$this->_dataObject = array("id"=>" ");
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
	/**
	 * 
	 */
	public function get_statistic_info($where){
        $doTime = false;
        $tableName = "warehouse_goods";
		$str='';
        if(isset($where['dotime_string']))
        {
            $doTime = str_replace("-","",$where['dotime_string']);
            $tableName .= $doTime;
                $c_sql = "SHOW TABLES from kucun_bak ";
                $c_ret = $this->db()->getAll($c_sql);
                $tb_ret = array_column($c_ret,'Tables_in_kucun_bak');
                if(!in_array($tableName,$tb_ret)){
                    return false;
                }
        }
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
		if(isset($where['company_ids_string'])&& $where['company_ids_string']){
			$company_ids_string=trim($where['company_ids_string'],',');
			$str.=" and wg.company_id in ({$company_ids_string})";
		}else{
            $where['company_ids_string']='';
        }

        if(!$doTime){
            return false;
        }
		$sql="SELECT SUM(wg.yuanshichengbenjia) warehouse_price,count(*) count FROM kucun_bak.$tableName wg  where 1 {$str}";
		$data=$this->db()->getRow($sql);
        if($data){
            $statistic_info=array(
                'dotime_string'=>$where['dotime_string'],
                'warehouse_num'=>$data['count'],
                'warehouse_price'=>round($data['warehouse_price'],2),
                'warehouse_ids_string'=>$where['warehouse_ids_string'],
                'company_ids_string'=>$where['company_ids_string'],
                'types_string'=>$where['types_string']
            );
           return $statistic_info;
        }else{
            return false;
        }
	}

	/**
	 * 
	 */
	public function get_statistic_data($where){
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
		if(isset($where['warehouse_ids_string'])&& $where['warehouse_ids_string']){
			$warehouse_ids_string=trim($where['warehouse_ids_string'],',');
			$str.=" and wg.warehouse_id in ({$warehouse_ids_string})";
		}
		if(isset($where['types_string'])&& $where['types_string']){
			$types_string=trim($where['types_string'],',');
			$str.=" and wg.cangkuleixing_id in ({$types_string})";
		}
		$str_num=$str_price=$str;
		if(isset($where['company_ids_string'])&& $where['company_ids_string']){
			$company_ids_string=trim($where['company_ids_string'],',');
			$str_price.=" and wg.company_id in ({$company_ids_string})";
		}
        if(!$doTime){
            return false;
        }
		$sql="SELECT chanpinxian,xinchanpinxian,kuanshileixing,xinkuanshifenlei,huohao,gongyingshang,rukufangshi,zhuangtai,
        cangku,kuanhao,mohao,mingcheng,mingyichengben,yuanshichengbenjia,zuixincaigoujia,zhuchengse,zhuchengsechong,zhijuan,
        jintuoleixing,zhushi,zhushilishu,zhushixingzhuang,zhushichong,zhushiyanse,zhushijingdu,qiegong,
        paoguang,duichen,yingguang,zhushiguige,zhushimairudanjia,zhushimairuchengben,zhushijijiadanjia,
        fushi1,fushi1lishu,fushi1chong,fushimairudanjia,fushimairuchengben,fushijijiadanjia,
        shi2,shi2lishu,shi2chong,shi2mairudanjia,shi2mairuchengben,shi2jijiadanjia,
        zhengshuhao,zhengshuleixing,jinshileixing,num,IF(jiejia=1,'是','否'),IF(order_goods_id>0,'绑定','未绑定'),gongsi,
        jietuoxiangkou,weixiu_status,weixiu_company_name,weixiu_warehouse_name,jinhao,
        zuihouxiaoshoushijian,benkukuling,zongkuling,guojibaojia,zuanshizhekou,pinpai,luozuanzhengshuleixing,
        supplier_code,xiliejikuanshiguishu,box_sn,rukushijian FROM kucun_bak.$tableName wg  where 1 {$str_price}";
		$data=$this->db()->getAll($sql);
        return $data;
	}

    public function get_statistic_report($where){
        $tableName = "warehouse_goods";
        $doTime = str_replace("-","",$where['dotime_string']);
        $tableName .= $doTime;
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
        $d = $this->db()->getAll($sql);
        
        $statistic_info = array();
        foreach($d as $k => $v){
            $statistic_info[]=array(
                'dotime_string'=>$where['dotime_string'],
                'company_name'=>$v['company_name'],
                'count'=>$v['count'],
                'warehouse_price'=>round($v['warehouse_price'],2)
            );
        }
        return $statistic_info;
    }

    public function get_statistic_report_third($where){
        $tableName = "warehouse_goods";
        $doTime = str_replace("-","",$where['dotime_string']);
        $tableName .= $doTime;
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
        $d = $this->db()->getAll($sql);
        
        $statistic_info = array();
        foreach($d as $k => $v){
            $statistic_info[]=array(
                'dotime_string'=>$where['dotime_string'],
                'company_name'=>$v['company_name'],
                'warehouse_name'=>$v['warehouse_name'],
                'count'=>$v['count'],
                'warehouse_price'=>round($v['warehouse_price'],2),
            );
        }
        return $statistic_info;
    }


    public function checkTableExists($doTime)
    {
        $tableName = 'warehouse_goods';
        $doTime = str_replace("-","",$doTime);
        $tableName .= $doTime;
        $c_sql = "SHOW TABLES from kucun_bak ";
        $c_ret = $this->db()->getAll($c_sql);
        $tb_ret = array_column($c_ret,'Tables_in_kucun_bak');
        return in_array($tableName,$tb_ret);
    }

    public function pageGoodsList($cangku,$doTime)
    {
        $tableName = 'warehouse_goods';
        $doTime = str_replace("-","",$doTime);
        $tableName .= $doTime;
        $sql = "SELECT huohao,box_sn,shangjiashijian,rukushijian,ruxinkushijian FROM kucun_bak.$tableName where cangku = '".$cangku."' AND zhuangtai = '库存'";
        return $this->db()->getAll($sql);
    }
}