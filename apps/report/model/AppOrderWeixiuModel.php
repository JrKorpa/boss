<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderWeixiuModel.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderWeixiuModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_order_weixiu';
        $this->_dataObject = array("id"=>" ",
			"order_id"=>" ",
			"order_sn"=>" ",
			"rec_id"=>"布产号",
			"re_type"=>"维修单类型",
			"goods_id"=>"货号",
			"consignee"=>"客户姓名",
			"repair_act"=>"维修动作",
			"repair_man"=>"维修负责人id",
			"repair_factory"=>"工厂",
			"repair_make_order"=>"维修制单人",
			"remark"=>"备注",
			"status"=>"状态",
			"order_time"=>"制单时间",
			"confirm_time"=>"确认时间",
			"factory_time"=>"下单时间",
			"end_time"=>"预计出厂时间",
			"re_end_time"=>"完成时间",
			"receiving_time"=>"收货时间",
			"frequency"=>"维修次数",
			"after_sale"=>"是否是售后维修 0不是，1是",
			"change_sn"=>"转仓单号");
		parent::__construct($id,$strConn);
	}
	/**
	pageList ,分页
	**/
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$str='';
		if($where['time_type']=='add'){//日期为下单时间
			$sql  = "SELECT COUNT(*) cnt, LEFT(factory_time,10) dotime FROM repair_order.`app_order_weixiu` m  ";
		}
		else{//日期为发货时间
			$sql  = "SELECT COUNT(*) cnt,LEFT(re_end_time,10) dotime FROM repair_order.`app_order_weixiu` m  ";
			$str.=" re_end_time!='' AND ";
		}
		if($where['time_type']=='add'){
			if(isset($where['start_time']) && $where['start_time'] != '')
			{
				$str .= "`m`.`factory_time` >= '".$where['start_time']." 00:00:00' AND ";
			}
			if(isset($where['end_time']) && $where['end_time'] != '')
			{
				$str .= "`m`.`factory_time` <= '".$where['end_time']." 23:59:59' AND ";
			}
		}
		else{
			if(isset($where['start_time']) && $where['start_time'] != '')
			{
				$str .= "`m`.`re_end_time` >= '".$where['start_time']." 00:00:00' AND ";
			}
			if(isset($where['end_time']) && $where['end_time'] != '')
			{
				$str .= "`m`.`re_end_time` <= '".$where['end_time']." 23:59:59' AND ";
			}
		}
		if(isset($where['re_type']) && $where['re_type'])
		{
			$str .="`m`.`re_type`='".$where['re_type']."' AND ";
		}
		if(isset($where['frequency']) && $where['frequency'])
		{
			$str .="`m`.`frequency`='".$where['frequency']."' AND ";
		}
		
		if(isset($where['repair_factory']) && $where['repair_factory'])
		{
			$str .="`m`.`repair_factory`='".$where['repair_factory']."' AND ";
		}
		if(isset($where['repair_act']) && $where['repair_act'] != "")
		{
			$str .= " `m`.`repair_act` like \"%".addslashes($where['repair_act'])."%\" AND ";
		}
		
		if($str)
		{
			$str = rtrim($str,"AND ");
			$sql .=" WHERE ".$str;
		}
		if($where['time_type']=='add'){
			$sql .= " GROUP BY LEFT(factory_time,10) order by factory_time desc ";
		}
		else{
			$sql .= " GROUP BY LEFT(re_end_time,10) order by re_end_time desc ";
		}
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
		if($data['data']){
			foreach ($data['data'] as & $dval){
                $dotime = $dval['dotime'];

                if($where['time_type']=='add'){//日期为下单时间
					$detail_sql="SELECT m.id,m.re_end_time,
                                m.factory_time 
                            FROM repair_order.`app_order_weixiu` m
                            WHERE m.factory_time like '{$dotime}%' AND $str
                            ";
                }else{
					$detail_sql="SELECT m.id,m.re_end_time,
                                m.factory_time ,m.order_time
                            FROM repair_order.`app_order_weixiu` m
                            WHERE m.re_end_time like '{$dotime}%' AND m.re_end_time!='' AND $str 
                            ";;
                }

                //echo $detail_sql;die;
                $list = $this->db()->getAll($detail_sql);
                $get=array(
                    '5day_receive_num'=>0,
                    '5day_receive_time'=>0,
                    'allday_receive_num'=>0,
                    'allday_receive_time'=>0,
                );
                foreach($list as $key => $val){
                    if($val['re_end_time']!='0000-00-00 00:00:00' && !is_null($val['re_end_time'])){
                        $re_end_time = $val['re_end_time'];
                        $factory_time = $val['order_time'];
                        $re_end_timestamp = strtotime(substr($re_end_time,0,10));
                        $factory_timestamp = strtotime(substr($factory_time,0,10));
                        
                        if($re_end_timestamp - $factory_timestamp < 86400*5){
                            $get['5day_receive_num']++;
                            $get['5day_receive_time'] += round(( strtotime($val['re_end_time']) - strtotime($val['order_time']))/86400,2);
                        }
                        $get['allday_receive_num']++;
                        $get['allday_receive_time'] += round(( strtotime($val['re_end_time']) - strtotime($val['order_time']))/86400,2);
                    }
                }

                $dval['5day_receive_num'] = $get['5day_receive_num'];
                $dval['5day_receive_time'] = $get['5day_receive_time'];
                $dval['allday_receive_num'] = $get['allday_receive_num'];
                $dval['allday_receive_time'] = $get['allday_receive_time'];
                $dval['5day_percent'] = $get['allday_receive_num']>0 ? round($get['5day_receive_num']/$get['allday_receive_num'],2)*100:0;
                $dval['5day_avg'] = $dval['5day_receive_num']>0?round($get['5day_receive_time']/$dval['5day_receive_num'],2):0;
                $dval['allday_avg'] = $dval['allday_receive_num']>0?round($get['allday_receive_time']/$dval['allday_receive_num'],2):0;
			}
		}
		
		
		$data['all_data']=$this->db()->getAll($sql);
		if($data['all_data']){
			foreach ($data['all_data'] as & $dval1){
				$dotime = $dval1['dotime'];
		
				if($where['time_type']=='add'){//日期为下单时间
					$detail_sql="SELECT m.id,m.re_end_time,
					m.factory_time
					FROM repair_order.`app_order_weixiu` m
					WHERE m.factory_time like '{$dotime}%' AND $str
					";
				}else{
				$detail_sql="SELECT m.id,m.re_end_time,
					m.factory_time ,m.order_time
					FROM repair_order.`app_order_weixiu` m
					WHERE m.re_end_time like '{$dotime}%' AND m.re_end_time!='' AND $str
					";;
					}
		
					//echo $detail_sql;die;
					$list = $this->db()->getAll($detail_sql);
					$get=array(
					'5day_receive_num'=>0,
					'5day_receive_time'=>0,
					'allday_receive_num'=>0,
					'allday_receive_time'=>0,
					);
					foreach($list as $key => $val){
					if($val['re_end_time']!='0000-00-00 00:00:00' && !is_null($val['re_end_time'])){
					$re_end_time = $val['re_end_time'];
					$factory_time = $val['order_time'];
					$re_end_timestamp = strtotime(substr($re_end_time,0,10));
					$factory_timestamp = strtotime(substr($factory_time,0,10));
		
							if($re_end_timestamp - $factory_timestamp < 86400*5){
							$get['5day_receive_num']++;
						$get['5day_receive_time'] += round(( strtotime($val['re_end_time']) - strtotime($val['order_time']))/86400,2);
					}
					$get['allday_receive_num']++;
						$get['allday_receive_time'] += round(( strtotime($val['re_end_time']) - strtotime($val['order_time']))/86400,2);
					}
					}
		
					$dval1['5day_receive_num'] = $get['5day_receive_num'];
					$dval1['5day_receive_time'] = $get['5day_receive_time'];
					$dval1['allday_receive_num'] = $get['allday_receive_num'];
					$dval1['allday_receive_time'] = $get['allday_receive_time'];
					$dval1['5day_percent'] = $get['allday_receive_num']>0 ? round($get['5day_receive_num']/$get['allday_receive_num'],2)*100:0;
					$dval1['5day_avg'] = $dval1['5day_receive_num']>0?round($get['5day_receive_time']/$dval1['5day_receive_num'],2):0;
				    $dval1['allday_avg'] = $dval1['allday_receive_num']>0?round($get['allday_receive_time']/$dval1['allday_receive_num'],2):0;
		 }
		}
		
		return $data;
	}

	/**
	 二级页面，获取详细
	 **/
	function get_detail ($where,$page,$pageSize=10,$useCache=true)
	{
		if($where['time_type']=='add'){//日期为下单时间
			$sql  = "SELECT  LEFT(factory_time,10) dotime ,re_end_time,factory_time ,frequency,factory_time  FROM repair_order.`app_order_weixiu` m  ";
		}
		else{//日期为发货时间
			$sql  = "SELECT LEFT(re_end_time,10) dotime,re_end_time,factory_time ,frequency,factory_time  FROM repair_order.`app_order_weixiu` m ";
		}
        $str = '';
		if(isset($where['re_type']) && $where['re_type'])
		{
			$str .="`m`.`re_type`='".$where['re_type']."' AND ";
		}
		if(isset($where['frequency']) && $where['frequency'])
		{
			$str .="`m`.`frequency`='".$where['frequency']."' AND ";
		}
		
		if(isset($where['repair_factory']) && $where['repair_factory'])
		{
			$str .="`m`.`repair_factory`='".$where['repair_factory']."' AND ";
		}
		if(isset($where['repair_act']) && $where['repair_act'] != "")
		{
			$str .= " `m`.`repair_act` like \"%".addslashes($where['repair_act'])."%\" AND ";
		}
	   if($where['time_type']=='add'){
			if(isset($where['start_time']) && $where['start_time'] != '')
			{
				$str .= "`m`.`factory_time` >= '".$where['start_time']." 00:00:00' AND ";
			}
			if(isset($where['end_time']) && $where['end_time'] != '')
			{
				$str .= "`m`.`factory_time` <= '".$where['end_time']." 23:59:59' AND ";
			}
		}
		else{
			if(isset($where['start_time']) && $where['start_time'] != '')
			{
				$str .= "`m`.`re_end_time` >= '".$where['start_time']." 00:00:00' AND ";
			}
			if(isset($where['end_time']) && $where['end_time'] != '')
			{
				$str .= "`m`.`re_end_time` <= '".$where['end_time']." 23:59:59' AND ";
			}
		}
		if($str)
		{
			$str = rtrim($str,"AND ");
			$sql .=" WHERE ".$str.' ';
		}

	    if($where['time_type']=='add'){
			$sql .=" group by LEFT(factory_time,10) ";
            $sql.=" order by  LEFT(factory_time,10) desc ";
		}
		else{
			$sql .=" group by LEFT(`m`.`re_end_time`,10) ";
            $sql.=" order by LEFT(`m`.`re_end_time`,10) desc ";
		}
        
        //echo $sql;
        //exit;
		//计算分页
		$data['pageSize']=$pageSize;
		$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
		$data['recordCount'] = $this->db()->getAll($countSql);
        //echo $countSql;
        //var_dump($data['recordCount']);die;
		$data['recordCount']=count($data['recordCount']);
		$data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
		$data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
		$data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
		$data['isFirst'] = $data['page'] > 1 ? false : true;
		$data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
		$data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
		$data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
		$data['data'] = $this->db()->getAll($data['sql']);
        if($data['data']){
			foreach ($data['data'] as & $dval){
                $dotime = $dval['dotime'];

                if($where['time_type']=='add'){//日期为下单时间
					$detail_sql="SELECT m.id,m.re_end_time,
                                m.factory_time 
                            FROM repair_order.`app_order_weixiu` m
                            WHERE m.factory_time like '{$dotime}%' AND $str
                            ";
                }else{
					$detail_sql="SELECT m.id,m.re_end_time,
                                m.factory_time 
                            FROM repair_order.`app_order_weixiu` m
                            WHERE m.re_end_time like '{$dotime}%' AND m.re_end_time!='' AND $str 
                            ";;
                }

                //echo $detail_sql;die;
                $list = $this->db()->getAll($detail_sql);
                $get=array(
                    'dotime'=>$dotime,
                    'num'=>0,
                    'allday_receive_num'=>0,
                    '0_5day_receive_num'=>0,
                    '0_5day_receive_percent'=>0,
                    '0day_receive_num'=>0,
                    '1day_receive_num'=>0,
                    '2day_receive_num'=>0,
                    '3day_receive_num'=>0,
                    '4day_receive_num'=>0,
                    '5day_receive_num'=>0,
                    '6day_receive_num'=>0,
                    '7day_receive_num'=>0,
                    '8_20day_receive_num'=>0,
                    '21day_receive_num'=>0,
                );

                
                foreach($list as $key => $val){
                    $get['num']++;
                    if($val['re_end_time']!='0000-00-00 00:00:00' && !is_null($val['re_end_time'])){
                        $get['allday_receive_num']++;
                        $re_end_time = $val['re_end_time'];
                        $factory_time = $val['factory_time'];
                        $re_end_timestamp = strtotime(substr($re_end_time,0,10));
                        $factory_timestamp = strtotime(substr($factory_time,0,10));
                        
                        $delay_day = ceil(($re_end_timestamp - $factory_timestamp)/86400);
                        $kk = '';
                        if($delay_day<=7){
						    $kk=$delay_day.'day_receive_num';
                        }
                        elseif($delay_day<=20){
                            $kk='8_20day_receive_num';
                        }
                        else{
                            $kk='21day_receive_num';
                        }
                        $get[$kk]++;

                        //5天内收货
                        if($delay_day<=5){
                            $get['0_5day_receive_num']++;
                        }
                    }
                    $get['0_5day_receive_percent'] = $get['allday_receive_num']>0 ? round($get['0_5day_receive_num']/$get['allday_receive_num'],4)*100:0;
                }
                $dval = $get;
			}
		}
		return $data;
	}

	/**
	 三级页面，获取详细
	 **/
	function get_detail_third ($where,$page,$pageSize=10,$useCache=true)
	{
		if($where['time_type']=='add'){//日期为下单时间
			$sql  = "SELECT  
                LEFT(factory_time,10) dotime ,
                COUNT(*) cnt,
                SUM(IF(m.re_end_time is null,0,1)) recnt,
                repair_factory,
                info.`name`,
                SUM(IF(m.re_end_time is null,0,unix_timestamp(m.re_end_time)-unix_timestamp(m.factory_time))) sum_time  
            FROM repair_order.`app_order_weixiu` m 
            INNER JOIN kela_supplier.`app_processor_info` info ON info.id=m.`repair_factory`
            ";
		}
		else{//日期为发货时间
			$sql  = "SELECT 
                LEFT(re_end_time,10) dotime,
                COUNT(*) cnt,
                COUNT(*) recnt,
                repair_factory,
                info.`name`,
                SUM(IF(m.re_end_time is null,0,unix_timestamp(m.re_end_time)-unix_timestamp(m.factory_time))) sum_time  
            FROM repair_order.`app_order_weixiu` m  
            INNER JOIN kela_supplier.`app_processor_info` info ON info.id=m.`repair_factory` ";
		}
		$str = ' 1 AND ';
		if(isset($where['re_type']) && $where['re_type'])
		{
			$str .="`m`.`re_type`='".$where['re_type']."' AND ";
		}
		if(isset($where['frequency']) && $where['frequency'])
		{
			$str .="`m`.`frequency`='".$where['frequency']."' AND ";
		}
		
		if(isset($where['repair_factory']) && $where['repair_factory'])
		{
			$str .="`m`.`repair_factory`='".$where['repair_factory']."' AND ";
		}
		if(isset($where['repair_act']) && $where['repair_act'] != "")
		{
			$str .= " `m`.`repair_act` like \"%".addslashes($where['repair_act'])."%\" AND ";
		}
	   if($where['time_type']=='add'){
			if(isset($where['start_time']) && $where['start_time'] != '')
			{
				$str .= "`m`.`factory_time` >= '".$where['start_time']." 00:00:00' AND ";
			}
			if(isset($where['end_time']) && $where['end_time'] != '')
			{
				$str .= "`m`.`factory_time` <= '".$where['end_time']." 23:59:59' AND ";
			}
		}
		else{
			if(isset($where['start_time']) && $where['start_time'] != '')
			{
				$str .= "`m`.`re_end_time` >= '".$where['start_time']." 00:00:00' AND ";
			}
			if(isset($where['end_time']) && $where['end_time'] != '')
			{
				$str .= "`m`.`re_end_time` <= '".$where['end_time']." 23:59:59' AND ";
			}
		}
		if($str)
		{
			$str = rtrim($str,"AND ");
			$sql .=" WHERE ".$str;
		}
		if($where['time_type']=='add'){
			$sql .= " GROUP BY LEFT(factory_time,10),repair_factory order by LEFT(factory_time,10) desc ";
		}
		else{
			$sql .= " GROUP BY LEFT(re_end_time,10),repair_factory order by LEFT(re_end_time,10) desc ";
		}
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
        //echo "<pre>";
        //var_dump($data['data']);die;
        //die;
        if($data['data']){
			foreach ($data['data'] as & $dval){
                $dval['sum_time'] = round($dval['sum_time']/86400,2);
                $dval['avg_receive_time'] = $dval['recnt']>0?round($dval['sum_time']/$dval['recnt'],2):0;
			}
		}
		return $data;
	}
	/**
	 四级页面，获取详细
	 **/
	function get_detail_forth ($where)
	{
		if($where['time_type']=='add'){//日期为下单时间
			$sql  = "SELECT  m.id,LEFT(factory_time,10) acount_date,info.`name`,m.rec_id  FROM repair_order.`app_order_weixiu` m JOIN kela_supplier.`app_processor_info` info ON info.id=m.`repair_factory` 
            ";
		}
		else{//日期为发货时间
			$sql  = "SELECT m.id,LEFT(re_end_time,10)  acount_date,info.`name`,m.rec_id  FROM repair_order.`app_order_weixiu` m  JOIN kela_supplier.`app_processor_info` info ON info.id=m.`repair_factory` ";
		}
		$str = ' 1 AND ';
		if(isset($where['re_type']) && $where['re_type'])
		{
			$str .="`m`.`re_type`='".$where['re_type']."' AND ";
		}
		if(isset($where['frequency']) && $where['frequency'])
		{
			$str .="`m`.`frequency`='".$where['frequency']."' AND ";
		}
		
		if(isset($where['repair_factory']) && $where['repair_factory'])
		{
			$str .="`m`.`repair_factory`='".$where['repair_factory']."' AND ";
		}
		if(isset($where['repair_act']) && $where['repair_act'] != "")
		{
			$str .= " `m`.`repair_act` like \"%".addslashes($where['repair_act'])."%\" AND ";
		}
	   if($where['time_type']=='add'){
			if(isset($where['start_time']) && $where['start_time'] != '')
			{
				$str .= "`m`.`factory_time` >= '".$where['start_time']." 00:00:00' AND ";
			}
			if(isset($where['end_time']) && $where['end_time'] != '')
			{
				$str .= "`m`.`factory_time` <= '".$where['end_time']." 23:59:59' AND ";
			}
		}
		else{
			if(isset($where['start_time']) && $where['start_time'] != '')
			{
				$str .= "`m`.`re_end_time` >= '".$where['start_time']." 00:00:00' AND ";
			}
			if(isset($where['end_time']) && $where['end_time'] != '')
			{
				$str .= "`m`.`re_end_time` <= '".$where['end_time']." 23:59:59' AND ";
			}
		}
		if($str)
		{
			$str = rtrim($str,"AND ");
			$sql .=" WHERE ".$str;
		}
		$data = $this->db()->getAll($sql);
		return $data;
	}
	/**
	 pageList ,分页
	 **/
	function pageList_QC ($where,$page,$pageSize=10,$useCache=true)
	{
		$str='';
		if(isset($where['start_time']) && $where['start_time'] != '')
		{
			$str .= " AND `wxl`.`date_time` >= '".$where['start_time']." 00:00:00'";
		}
		if(isset($where['end_time']) && $where['end_time'] != '')
		{
			$str .= " AND `wxl`.`date_time` <= '".$where['end_time']." 23:59:59'";
		}
		if(isset($where['re_type']) && $where['re_type'])
		{
			$str .=" AND `wx`.`re_type`='".$where['re_type']."'";
		}
		if(isset($where['frequency']) && $where['frequency'])
		{
			$str .=" AND `wx`.`frequency`='".$where['frequency']."'  ";
		}
	
		if(isset($where['repair_factory']) && $where['repair_factory'])
		{
			$str .=" AND `wx`.`repair_factory`='".$where['repair_factory']."'  ";
		}
		if(isset($where['repair_act']) && $where['repair_act'] != "")
		{
			$str .= " AND  `wx`.`repair_act` like \"%".addslashes($where['repair_act'])."%\"  ";
		}
        if(isset($where['qc_status']) && !empty($where['qc_status']))
        {
            if($where['qc_status'] == 1){
                $str .= " AND wxl.content like '维修单完毕%' ";
            }elseif($where['qc_status'] == 2){
                $str .= " AND wxl.content like '维修单质检未过%' ";
            }
        }
        $sql = "SELECT left(wxl.`date_time`,10) dotime,COUNT(1) qc_time_sum,SUM(IF(wxl.content like '维修单完毕%',1,0)) cnt,0 not_checked_qc_num
                FROM app_order_weixiu wx
                inner join app_order_weixiu_log wxl on wx.id = wxl.do_id
                WHERE 1 $str
                AND ( wxl.content like '维修单质检未过%' or wxl.content like '维修单完毕%')
                GROUP BY left(wxl.`date_time`,10)
                order by left(wxl.`date_time`,10) DESC
                ";
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
	 pageList ,分页
	 **/
	function pageList_QC2 ($where)
	{
		$str='';
		if(isset($where['start_time']) && $where['start_time'] != '')
		{
			$str .= " AND wxl.`date_time` >= '".$where['start_time']." 00:00:00'";
		}
		if(isset($where['end_time']) && $where['end_time'] != '')
		{
			$str .= " AND wxl.`date_time` <= '".$where['end_time']." 23:59:59'";
		}
		if(isset($where['re_type']) && $where['re_type'])
		{
			$str .=" AND `wx`.`re_type`='".$where['re_type']."'";
		}
		if(isset($where['frequency']) && $where['frequency'])
		{
			$str .=" AND `wx`.`frequency`='".$where['frequency']."'  ";
		}
	
		if(isset($where['repair_factory']) && $where['repair_factory'])
		{
			$str .=" AND `wx`.`repair_factory`='".$where['repair_factory']."'  ";
		}
		if(isset($where['repair_act']) && $where['repair_act'] != "")
		{
			$str .= " AND  `wx`.`repair_act` like \"%".addslashes($where['repair_act'])."%\"  ";
		}
        if(isset($where['qc_status']) && !empty($where['qc_status']))
        {
            if($where['qc_status'] == 1){
                $str .= " AND wxl.content like '维修单完毕%' ";
            }elseif($where['qc_status'] == 2){
                $str .= " AND wxl.content like '维修单质检未过%' ";
            }
        }
        $sql = "SELECT COUNT(1) qc_time_sum,SUM(IF(wxl.content like '维修单完毕%',1,0)) cnt,0 not_checked_qc_num
                FROM app_order_weixiu wx
                inner join app_order_weixiu_log wxl on wx.id = wxl.do_id
                WHERE 1 $str
                AND ( wxl.content like '维修单质检未过%' or wxl.content like '维修单完毕%')
                ";
        $data = $this->db()->getRow($sql);
		return $data;
	}
	/**
	 pagePassedList_second，维修良品率第二层数据
	 **/
	function pagePassedList_second ($where,$page,$pageSize=10,$useCache=true)
	{
		$str='';
		if(isset($where['start_time']) && $where['start_time'] != '')
		{
			$str .= " AND `wxl`.`date_time` >= '".$where['start_time']." 00:00:00'";
		}
		if(isset($where['end_time']) && $where['end_time'] != '')
		{
			$str .= " AND `wxl`.`date_time` <= '".$where['end_time']." 23:59:59'  ";
		}
		if(isset($where['re_type']) && $where['re_type'])
		{
			$str .=" AND `wx`.`re_type`='".$where['re_type']."'  ";
		}
		if(isset($where['frequency']) && $where['frequency'])
		{
			$str .=" AND `wx`.`frequency`='".$where['frequency']."'  ";
		}
	
		if(isset($where['repair_factory']) && $where['repair_factory'])
		{
			$str .=" AND `wx`.`repair_factory`='".$where['repair_factory']."'  ";
		}
		if(isset($where['repair_act']) && $where['repair_act'] != "")
		{
			$str .= " AND  `wx`.`repair_act` like \"%".addslashes($where['repair_act'])."%\"  ";
		}
        if(isset($where['qc_status']) && !empty($where['qc_status']))
        {
            if($where['qc_status'] == 1){
                $str .= " AND wxl.content like '维修单完毕%' ";
            }elseif($where['qc_status'] == 2){
                $str .= " AND wxl.content like '维修单质检未过%' ";
            }
        }
        $sql = "SELECT wx.*,wxl.*
                FROM app_order_weixiu wx
                inner join app_order_weixiu_log wxl on wx.id = wxl.do_id
                WHERE 1 $str
                AND ( wxl.content like '维修单质检未过%' or wxl.content like '维修单完毕%')
                order by wxl.id desc ";
		//重新计算分页
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
	 pagePassedList_second，维修良品率第二层数据
	 **/
	function pagePassedList_second_detail ($where)
	{
		$str='';
		if(isset($where['start_time']) && $where['start_time'] != '')
		{
			$str .= " AND `wxl`.`date_time` >= '".$where['start_time']." 00:00:00'";
		}
		if(isset($where['end_time']) && $where['end_time'] != '')
		{
			$str .= " AND `wxl`.`date_time` <= '".$where['end_time']." 23:59:59'  ";
		}
		if(isset($where['re_type']) && $where['re_type'])
		{
			$str .=" AND `wx`.`re_type`='".$where['re_type']."'  ";
		}
		if(isset($where['frequency']) && $where['frequency'])
		{
			$str .=" AND `wx`.`frequency`='".$where['frequency']."'  ";
		}
	
		if(isset($where['repair_factory']) && $where['repair_factory'])
		{
			$str .=" AND `wx`.`repair_factory`='".$where['repair_factory']."'  ";
		}
		if(isset($where['repair_act']) && $where['repair_act'] != "")
		{
			$str .= " AND  `wx`.`repair_act` like \"%".addslashes($where['repair_act'])."%\"  ";
		}
        if(isset($where['qc_status']) && !empty($where['qc_status']))
        {
            if($where['qc_status'] == 1){
                $str .= " AND wxl.content like '维修单完毕%' ";
            }elseif($where['qc_status'] == 2){
                $str .= " AND wxl.content like '维修单质检未过%' ";
            }
        }
        $sql = "SELECT wxl.*,wx.*,wx.id id
                FROM app_order_weixiu wx
                inner join app_order_weixiu_log wxl on wx.id = wxl.do_id
                WHERE 1 $str
                AND ( wxl.content like '维修单质检未过%' or wxl.content like '维修单完毕%')
                order by wxl.id desc
                ";
		$data = $this->db()->getAll($sql); 
		return $data;
	}
    public function getLastOpration($id)
    {
        $sql="select * from app_order_weixiu_log where do_id = $id order by id desc limit 1";
        return $this->db()->getRow($sql);
    }
}

?>