<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillModel.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillModel extends Model {
	function __construct($id = NULL, $strConn = "") {
		$this->_objName = 'warehouse_bill';
		$this->_dataObject = array (
				"id" => "序号",
				"bill_no" => "单据编号",
				"bill_type" => "单据类型",
				"bill_status" => "数据字典：仓储单据状态（warehouse_in_status）/ 盘点单状态（warehouse.pandian_plan）",
				"order_sn" => "订单号",
				"goods_num" => "货品总数",
				"put_in_type" => "入库方式",
				"jiejia" => "是否结价",
				"send_goods_sn" => "送货单号",
				"pro_id" => "供应商ID",
				"pro_name" => "供应商名称",
				"goods_total" => "货总金额",
				"shijia" => "实际销售价格",
				"to_warehouse_id" => "入货仓ID (盘点单，该列存盘点的仓库,退货返厂单时，该字段记录出库仓)",
				"to_warehouse_name" => "入货仓名称 (盘点单，该列存盘点的仓库)",
				"to_company_id" => "入货公司ID",
				"to_company_name" => "入货公司名称",
				"from_company_id" => "出货公司id",
				"from_company_name" => "出货公司名称",
				"bill_note" => "备注",
				"yuanshichengben" => "原始成本",
				"check_user" => "审核人",
				"check_time" => "审核时间",
				"create_user" => "制单人",
				"create_time" => "制单时间",
				"fin_check_status" => "财务审核状态:见数据字典",
				"fin_check_time" => "财务审核时间",
				"to_customer_id" => "配送公司id" 
		);
		
		parent::__construct ( $id, $strConn );
	}
	function pageList($where, $page, $pageSize = 10, $useCache = true) {
		//print_r($where);exit;
		if($where['bill_type']=='S'){
			$sql = " SELECT left( `o`.`check_time`, 10 ) as add_date,count( * ) as 'acount' 
            from `warehouse_shipping`.`warehouse_bill` o 
            left join `app_order`.`base_order_info` b on b.order_sn=o.order_sn 
            WHERE `o`.`bill_type`='S' AND `o`.`bill_status` in (3,2) 
            AND b.department_id in(13,71,1,122,2,3) ";
			if($where['department_ids']){
				$sql.=' and b.department_id in('.$where['department_ids'].')';
			}
            if($where['start_time'])
                $sql .=" and `o`.`check_time` >='{$where['start_time']} 00:00:00'";
            if($where['end_time'])
                $sql .=" and `o`.`check_time` <='{$where['end_time']} 23:59:59'";
		}
		else{
			$sql = " SELECT left( `o`.`create_time`, 10 ) as add_date,SUM( goods_num) as 'acount' from `warehouse_shipping`.`warehouse_bill` as o  WHERE   `o`.`bill_type`='M' AND `o`.`bill_status` in (1,2)  AND `o`.`from_company_id` =58 AND `o`.`to_company_id` NOT IN ( 58, 218 ,445 )";
			if($where['zt_type']==1){
				 $sql .= " AND `o`.`order_sn` <> ''";
			}
			elseif($where['zt_type']==2){
				$sql .= " AND `o`.`order_sn`=''";
			}
            if($where['start_time'])
                $sql .=" and `o`.`create_time` >='{$where['start_time']} 00:00:00'";
            if($where['end_time'])
                $sql .=" and `o`.`create_time` <='{$where['end_time']} 23:59:59'";
		}
		$sql .=" GROUP BY add_date  order by add_date desc ";
        //echo $sql;
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
	 * 获取发货订单的详情列表
	 * @param unknown $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @return number
	 */
	function pageDetailList($where, $page, $pageSize = 20, $useCache = true){
		if($where['bill_type']=='S'){
			$sql = " SELECT 
            left( `o`.`check_time`, 10 ) as 'add_date',
            c.channel_name department_name ,
            COUNT(`o`.`order_sn`) count 
            from `warehouse_shipping`.`warehouse_bill` o 
            left join `app_order`.`base_order_info` b on b.order_sn=o.order_sn  
            left join cuteframe.`sales_channels` c ON c.id=b.department_id  
            WHERE `o`.`bill_type`='S' AND `o`.`bill_status` in (3,2) 
            AND b.department_id in(13,71,1,122,2,3) ";
			if($where['department_ids']){
				$sql.=' and b.department_id in('.$where['department_ids'].')';
			}
            if($where['department_name']){
                $sql.=" AND c.channel_name = '".$where['department_name']."' ";
            }
		}
		else{
			$sql = " SELECT 
            left( `o`.`check_time`, 10 ) as 'add_date',
            c.channel_name department_name ,
            COUNT(`o`.`order_sn`) count 
            from `warehouse_shipping`.`warehouse_bill` as o  
            left join `app_order`.`base_order_info` b on b.order_sn=o.order_sn  
            left join cuteframe.`sales_channels` c ON c.id=b.department_id  
            WHERE   `o`.`bill_type`='M' AND `o`.`bill_status` in (3,2)  AND `o`.`from_company_id` =58 AND `o`.`to_company_id` NOT IN ( 58, 218 ,445 )";
			if($where['zt_type']==1){
				$sql .= " AND `o`.`order_sn` <> ''";
			}
			elseif($where['zt_type']==2){
				$sql .= " AND `o`.`order_sn`=''";
			}
		}
		if($where['start_time'])
			$sql .=" and `o`.`check_time` >='{$where['start_time']} 00:00:00'";
		if($where['end_time'])
			$sql .=" and `o`.`check_time` <='{$where['end_time']} 23:59:59'";
		$sql .=" group by add_date,`b`.`department_id` desc ";
		$sql .=" order by add_date desc ";
		//echo $sql;
        //exit;
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
	 * 
	 */
	public function get_detail_goods_list($where, $page, $pageSize = 10, $useCache = true){
		$sql="SELECT w.name warehouse_name, g.bill_id  plan_id,g.bill_no,g.chengbenjia price,g.yuanshichengben AS `origin_price`,g.goods_name,g.goods_id,g.goods_sn,g.pandian_status status FROM `warehouse_bill_goods` g left join warehouse w on g.warehouse_id=w.id  WHERE 1";
		if($where['bill_id']){
			$sql.=" and g.bill_id='{$where['bill_id']}'";
		}
		if($where['status']){
			$sql.=" and g.pandian_status='{$where['status']}'";
		}
		if($where['goods_id']){
			$sql.=" and g.goods_id='{$where['goods_id']}'";
		}
		$sql .= " ORDER BY g.`pandian_status` DESC";
		//echo $sql;exit;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	/**
	 * 退货列表
	 */
	public function  pageReturnGoodslist($where, $page, $pageSize = 10, $useCache = true){
		$sql="SELECT 
            COUNT(1) return_count ,
            SUM(IF(s.channel_class=1,1,0)) online_return_count,
            SUM(IF(s.channel_class=2,1,0)) upline_return_count,
            LEFT(b.check_time,10) do_date,
            s.channel_class
        FROM
            warehouse_shipping.warehouse_bill b  
            INNER JOIN app_order.`base_order_info` bo ON b.order_sn=bo.order_sn
            INNER JOIN cuteframe.`sales_channels` s ON s.id=bo.department_id 
        WHERE bo.is_delete=0 ";
		$str="";
        if(isset($where['bill_type']) && $where['bill_type']){
            $str .= " AND b.bill_type = '".$where['bill_type']."' ";
        }else{
            $str .= " AND (b.bill_type = 'O' or b.bill_type = 'D') ";
        }
		if(isset($where['department_id']) && $where['department_id']){
			$str .=" and `s`.`id` ='{$where['department_id']}'";
        }
		if(isset($where['channel_class']) && $where['channel_class']){
			$str .=" and `s`.`channel_class` ='{$where['channel_class']}'";
        }else{
			$str .=" and `s`.`channel_class` in (1,2) ";
        }
		if(isset($where['start_time']) && $where['start_time'])
			$str .=" and `b`.`check_time` >='{$where['start_time']} 00:00:00'";
		if(isset($where['end_time']) && $where['end_time'])
			$str .=" and `b`.`check_time` <='{$where['end_time']} 23:59:59'";
		if($str) $sql.=$str;
		$sql .= " GROUP BY LEFT(b.check_time,10),s.channel_class ORDER BY LEFT(b.check_time,10) DESC ";
		//echo $sql;
        //exit;
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
	 * 发货列表
	 */
	public function  pageSaleGoodsList($where, $page, $pageSize = 10, $useCache = true){
		$sql="SELECT 
            COUNT(1) sale_count ,
            SUM(IF(s.channel_class=1,1,0)) online_sale_count,
            SUM(IF(s.channel_class=2,1,0)) upline_sale_count,
            LEFT(b.check_time,10) do_date,
            b.bill_type  
        FROM
            warehouse_shipping.warehouse_bill b  
            INNER JOIN app_order.`base_order_info` bo ON b.order_sn=bo.order_sn
            INNER JOIN cuteframe.`sales_channels` s ON s.id=bo.department_id 
        WHERE bo.is_delete=0  AND b.bill_type IN ('S')  ";
		$str="";
		if(isset($where['department_id']) && $where['department_id']){
			$str .=" and `s`.`id` ='{$where['department_id']}'";
        }
		if(isset($where['channel_class']) && $where['channel_class']){
			$str .=" and `s`.`channel_class` ='{$where['channel_class']}'";
        }else{
			$str .=" and `s`.`channel_class` in (1,2) ";
        }
		if(isset($where['start_time']) && $where['start_time'])
			$str .=" and `b`.`check_time` >='{$where['start_time']} 00:00:00'";
		if(isset($where['end_time']) && $where['end_time'])
			$str .=" and `b`.`check_time` <='{$where['end_time']} 23:59:59'";
		if($str) $sql.=$str;
		$sql .= " GROUP BY LEFT(b.check_time,10),b.bill_type ORDER BY LEFT(b.check_time,10) DESC ";
		//echo $sql;
        //exit;
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
	 * 第二层列表
	 */
	public function  pageReturnGoodsSecondlist($where, $page, $pageSize = 10, $useCache = true){
		$sql="SELECT 
                COUNT(distinct b.order_sn) return_count ,
                SUM(b.bill_type='O') o_count ,
                SUM(b.bill_type='D') d_count ,
                s.channel_name,
                LEFT(b.check_time,10) acount_date  
            FROM warehouse_shipping.warehouse_bill b
                inner join app_order.`base_order_info` bo ON b.order_sn=bo.order_sn
                inner join cuteframe.`sales_channels` s ON s.id=bo.department_id
                WHERE bo.is_delete=0 ";
		$str="";
		if(isset($where['department_id']) && $where['department_id']){
			$str .=" and `s`.`id` ='{$where['department_id']}'";
        }
		if(isset($where['bill_type']) && !empty($where['bill_type'])){
			$str .=" and `b`.`bill_type` ='{$where['bill_type']}'";
        }else{
			$str .=" and `b`.`bill_type` IN ('O','D') ";
        }
		if(isset($where['channel_class']) && $where['channel_class']){
			$str .=" and `s`.`channel_class` ='{$where['channel_class']}'";
        }else{
        	$str .=" and `s`.`channel_class` in (1,2) ";
        }
		if(isset($where['start_time']) && $where['start_time']){
			$str .=" and `b`.`check_time` >='{$where['start_time']} 00:00:00'";
        }
		if(isset($where['end_time']) && $where['end_time']){
			$str .=" and `b`.`check_time` <='{$where['end_time']} 23:59:59'";
        }
		if($str) $sql.=$str;
		$sql .= " GROUP BY LEFT(b.check_time,10),s.channel_name ORDER BY LEFT(b.check_time,10) DESC ";
		//echo $sql;
        //exit;
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

    public function getSuperviseBill(){
        $data=array();
        $sql="SET SESSION group_concat_max_len = 200000";
        $this->db()->query($sql);
        if(SYS_SCOPE=="boss")
	        $sql="
	            select t2.warehouse,sum(t2.goods_num) as goodsSum,GROUP_CONCAT(t2.bill_no) as orderStr from (
				select b.bill_no,b.bill_type,b.goods_num,b.create_time,if(b.to_company_id=58,b.to_warehouse_name,b.to_company_name) as warehouse,datediff(now(),b.create_time)-(select count(j.id) from cuteframe.holiday_date j where b.create_time<=concat(j.day,' 00:00:00') and now() >=concat(j.day,' 23:59:59')) as days from warehouse_bill b where b.bill_status=1 and b.create_time>'2017-01-01 00:00:00' and b.bill_type in ('L','T','D','H','O')
				union all
				select t1.*,datediff(now(),t1.create_time)-(select count(j.id) from cuteframe.holiday_date j where t1.create_time<=concat(j.day,' 00:00:00') and now() >=concat(j.day,' 23:59:59')) as days from (
	            select b.bill_no,b.bill_type,count(g.goods_id) as goods_num,b.create_time,if(if(b.bill_type in ('WF','R'),g.weixiu_company_id,g.company_id)=58,if(b.bill_type in ('WF','R'),g.weixiu_warehouse_name,g.warehouse) ,if(b.bill_type in ('WF','R'),g.weixiu_company_name,g.company)) as warehouse from warehouse_bill b,warehouse_bill_goods bg,warehouse_goods g where b.id=bg.bill_id and bg.goods_id=g.goods_id and b.bill_status=1 and b.create_time>'2017-01-01 00:00:00' and b.bill_type in ('M','S','P','C','B','E','B','WF','R')
	            group by b.bill_no,b.bill_type,b.create_time,if(if(b.bill_type in ('WF','R'),g.weixiu_company_id,g.company_id)=58,if(b.bill_type in ('WF','R'),g.weixiu_warehouse_name,g.warehouse) ,if(b.bill_type in ('WF','R'),g.weixiu_company_name,g.company))
	            )  as t1 
				)t2 ";
        if(SYS_SCOPE=="zhanting")
            $sql="
                select t2.warehouse,sum(t2.goods_num) as goodsSum,GROUP_CONCAT(t2.bill_no) as orderStr from (
				select b.bill_no,b.bill_type,b.goods_num,b.create_time,if(b.to_company_id=58,b.to_warehouse_name,b.to_company_name) as warehouse,datediff(now(),b.create_time)-(select count(j.id) from cuteframe.holiday_date j where b.create_time<=concat(j.day,' 00:00:00') and now() >=concat(j.day,' 23:59:59')) as days from warehouse_bill b,cuteframe.company c where b.to_company_id=c.id and c.company_type in (1,2) and b.bill_status=1 and b.create_time>'2017-01-01 00:00:00' and b.bill_type in ('L','T','D','H','O')
				union all
				select t1.*,datediff(now(),t1.create_time)-(select count(j.id) from cuteframe.holiday_date j where t1.create_time<=concat(j.day,' 00:00:00') and now() >=concat(j.day,' 23:59:59')) as days from (
	            select b.bill_no,b.bill_type,count(g.goods_id) as goods_num,b.create_time,if(if(b.bill_type in ('WF','R'),g.weixiu_company_id,g.company_id)=58,if(b.bill_type in ('WF','R'),g.weixiu_warehouse_name,g.warehouse) ,if(b.bill_type in ('WF','R'),g.weixiu_company_name,g.company)) as warehouse from warehouse_bill b,warehouse_bill_goods bg,warehouse_goods g,cuteframe.company c where b.id=bg.bill_id and bg.goods_id=g.goods_id and if(b.bill_type in ('WF','R'),g.weixiu_company_id,g.company_id)=c.id and c.company_type in (1,2) and b.bill_status=1 and b.create_time>'2017-01-01 00:00:00' and b.bill_type in ('M','S','P','C','B','E','B','WF','R')
	            group by b.bill_no,b.bill_type,b.create_time,if(if(b.bill_type in ('WF','R'),g.weixiu_company_id,g.company_id)=58,if(b.bill_type in ('WF','R'),g.weixiu_warehouse_name,g.warehouse) ,if(b.bill_type in ('WF','R'),g.weixiu_company_name,g.company))
	            )  as t1 
				)t2";

        //预警条件
        $where=" where 
                t2.days>=
				case t2.bill_type 
				 when 'L' then 2
				 when 'T' then 2
				 when 'D' then 1
				 when 'H' then 1
				 when 'O' then 1
				 when 'M' then 3
				 when 'S' then 2
				 when 'P' then 3
				 when 'C' then 1
				 when 'B' then 3
				 when 'E' then 15
				 when 'WF' then 3
				 when 'R' then 1
				end 
               and t2.days< 
               case t2.bill_type 
               when 'L' then 3
				 when 'T' then 3
				 when 'D' then 2
				 when 'H' then 2
				 when 'O' then 2
				 when 'M' then 7
				 when 'S' then 3
				 when 'P' then 7
				 when 'C' then 3
				 when 'B' then 5
				 when 'E' then 30
				 when 'WF' then 7
				 when 'R' then 2
				 end  group by t2.warehouse";
        $data[]=$this->db()->getAll(trim($sql.$where));
        //超期条件
        $where=" where   t2.days>=
				 case 
				 when t2.bill_type='L' then 3
				 when t2.bill_type='T' then 3
				 when t2.bill_type='D' then 2 
				 when t2.bill_type='H' then 3 
				 when t2.bill_type='O' then 2 
				 when t2.bill_type='M' then 7 
				 when t2.bill_type='S' then 3 
				 when t2.bill_type='P' then 7 
				 when t2.bill_type='C' then 3 
				 when t2.bill_type='B' then 5 
				 when t2.bill_type='E' then 30
				 when t2.bill_type='WF' then 7 
				 when t2.bill_type='R' then 2
				 end  group by t2.warehouse";
        $data[]=$this->db()->getAll(trim($sql.$where));
        return $data;
    }
}
