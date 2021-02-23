<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseOrderInfoModel.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  -------------------------------------------------
 */
class BaseOrderInfoModel extends Model
{
		function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'base_order_info';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
			"order_sn"=>"订单编号",
            "user_id"=>"会员id",
            "consignee"=>"名字",
            "mobile"=>"手机号",
            "order_status"=>"订单审核状态1无效2已审核3取消4关闭",
            "order_pay_status"=>"支付状态:1未付款2部分付款3已付款",
            "order_pay_type"=>"支付类型",
            "delivery_status"=>"[参考数字字典：配送状态(sales.delivery_status)]",
            "send_good_status"=>"1未发货2已发货3收货确认4允许发货5已到店",
            "buchan_status"=>"布产状态:0未操作, 1 已布产,2 已出厂,8待审核",
            "customer_source_id"=>"客户来源",
            "department_id"=>"订单部门",
            "create_time"=>"制单时间",
            "create_user"=>"制单人",
            "recommended"=>"推荐人",
            "check_time"=>"审核时间",
            "check_user"=>"审核人",
            "modify_time"=>"修改时间",
            "order_remark"=>"备注信息",
            "referer"=>"录入来源",
            "is_delete"=>"订单状态0有效1删除",
            "apply_close"=>"申请关闭:0=未申请，1=申请关闭",
            "is_xianhuo"=>"是否是现货：1现货 0定制 2未添加商品",
            "is_print_tihuo"=>"是否打印提货单（数字字典confirm）",
            "is_zp"=>"是否为赠品单1为不是2为是",
            "effect_date"=>"订单生效时间(确定布产)",
            'apply_return'=>'申请退款',
        );
		parent::__construct($id,$strConn);
	}
	function pageListCreateTime ($where)
	{
        $sql="SELECT 
                    a.order_sn,
                    a.create_time,
                    IF(a.check_time is null ,'0000-00-00 00:00:00',a.check_time) check_time,
                    a.order_status,
                    a.order_pay_status,
                    a.delivery_status,
                    a.send_good_status,
                    a.is_xianhuo,
                    `s`.`channel_class`,
                    `a`.`buchan_status`,
                    IF(a.pay_date is null ,'0000-00-00 00:00:00',a.pay_date) pay_date,
                    IF(at.allow_shop_time is null,IF(a.shipfreight_time is null , '0000-00-00 00:00:00',a.shipfreight_time),at.allow_shop_time) send_time
                FROM 
                    app_order.base_order_info a 
                    inner join app_order.app_order_account oa on oa.order_id=a.id 
                    inner JOIN  cuteframe.`sales_channels` s ON s.id=a.department_id  
                    left join app_order.app_order_time at on a.id = at.order_id
                WHERE 1=1 AND a.is_delete=0 ";
		if(isset($where['department_id'])&& $where['department_id'])
		{
			$sql .= " AND `a`.`department_id` in(".addslashes($where['department_id']).")";
		}
		if(isset($where['channel_class']) && $where['channel_class']){
			$sql .=" and `s`.`channel_class` ='{$where['channel_class']}'";
		}else{
            $sql .=" and `s`.`channel_class` in (1,2) ";
        }
		if(isset($where['buchan_type']) )
		{
			if($where['buchan_type']== 'xianhuo'){
				$sql .= " AND `a`.`is_xianhuo`  = '1'";
            }
			elseif($where['buchan_type']== 'qihuo'){
    			$sql .= " AND `a`.`is_xianhuo`  = '0'";
            }
		}
		if(!empty($where['buchan_status'])){
            $sql.=" AND `a`.`buchan_status` in (".$where['buchan_status'].") ";
		}
		if(!empty($where['start_time'])){
            $sql.=" AND `a`.`create_time` >= '".$where['start_time']." 00:00:00'";
		}
		if(!empty($where['end_time'])){
            $sql.=" AND `a`.`create_time` <= '".$where['end_time']." 23:59:59'";
		}
        if(!empty($where['order_dia_type'])){
            $sql.="AND `a`.`order_sn` in(".implode(',', $where['order_dia_type']).")";
        }
        if(!empty($where['order_qiban_type'])){
            $sql.=" AND `a`.`order_sn` in(".implode(",", $where['order_qiban_type']).")";
        }
        $sql .= " order by a.id desc ";
        //echo $sql;die;
        $data = $this->db()->getAll($sql);
        return $data;
    }

	function pageListSendTime ($where)
	{
        $sql="SELECT 
                    a.order_sn,
                    a.create_time,
                    IF(a.check_time is null ,'0000-00-00 00:00:00',a.check_time) check_time,
                    a.order_status,
                    a.order_pay_status,
                    a.delivery_status,
                    a.send_good_status,
                    a.is_xianhuo,
                    `s`.`channel_class`,
                    `a`.`buchan_status`,
                    IF(a.pay_date is null ,'0000-00-00 00:00:00',a.pay_date) pay_date,
                    IF(at.allow_shop_time is null,IF(a.shipfreight_time is null , '0000-00-00 00:00:00',a.shipfreight_time),at.allow_shop_time) send_time
                FROM 
                    app_order.base_order_info a 
                    inner join app_order.app_order_account oa on oa.order_id=a.id 
                    inner JOIN  cuteframe.`sales_channels` s ON s.id=a.department_id  
                    left join app_order.app_order_time at on a.id = at.order_id
                WHERE 1=1 AND a.is_delete=0 ";
		if(isset($where['department_id'])&& $where['department_id'])
		{
			$sql .= " AND `a`.`department_id` in(".addslashes($where['department_id']).")";
		}
		if(isset($where['channel_class']) && $where['channel_class']){
			$sql .=" and `s`.`channel_class` ='{$where['channel_class']}'";
		}else{
            $sql .=" and `s`.`channel_class` in (1,2) ";
        }
		if(isset($where['buchan_type']) )
		{
			if($where['buchan_type']== 'xianhuo'){
				$sql .= " AND `a`.`is_xianhuo`  = '1'";
            }
			elseif($where['buchan_type']== 'qihuo'){
    			$sql .= " AND `a`.`is_xianhuo`  = '0'";
            }
		}
		if(!empty($where['buchan_status'])){
            $sql.=" AND `a`.`buchan_status` in (".$where['buchan_status'].") ";
		}
		if(!empty($where['start_time'])){
             $sql .= " AND ((`s`.`channel_class`=2 AND at.allow_shop_time >='".$where['start_time']." 00:00:00') or (`s`.`channel_class`=1 AND a.shipfreight_time >='".$where['start_time']." 00:00:00'))";
		}
		if(!empty($where['end_time'])){
             $sql .= " AND ((`s`.`channel_class`=2 AND at.allow_shop_time <='".$where['end_time']." 23:59:59') or (`s`.`channel_class`=1 AND a.shipfreight_time <='".$where['end_time']." 23:59:59'))";
		}
        if(!empty($where['order_dia_type'])){
            $sql.="AND `a`.`order_sn` in(".implode(',', $where['order_dia_type']).")";
        }
        if(!empty($where['order_qiban_type'])){
            $sql.=" AND `a`.`order_sn` in(".implode(",", $order_qiban_type).")";
        }
        $sql .= " order by a.id desc ";
        //echo $sql;die;
        $data = $this->db()->getAll($sql);
        return $data;
    }
	
    /**	pageList，分页列表,获取平均发货时长
     *
     *	@url ApplicationController/search
     */
    function pageList2 ($where,$page,$pageSize=10,$useCache=true)
    {
    	if($where['time_type']=='add'){//日期为下单时间
    		$sql="SELECT  LEFT(create_time,10)  acount_date,COUNT(*) COUNT,SUM(is_xianhuo=1) xianhuo_num FROM base_order_info a left JOIN  cuteframe.`sales_channels` s ON s.id=a.department_id where 1=1 ";
    	}
    	else{
    		$sql="SELECT  b.create_time send_goods_time, LEFT(b.create_time,10) acount_date,COUNT(*) COUNT,SUM(is_xianhuo=1) xianhuo_num 
FROM base_order_info a 
left JOIN  cuteframe.`sales_channels` s ON s.id=a.department_id
LEFT JOIN (
SELECT temp1.create_time ,temp1.`order_id`,temp1.action_id FROM app_order.`app_order_action` temp1 INNER JOIN (SELECT MIN(action_id) action_id FROM app_order.`app_order_action` WHERE  shipping_status=2  GROUP BY order_id  ) temp2 ON temp1.`action_id`=temp2.action_id
)  b ON b.`order_id`=a.id 
WHERE 1=1  ";
    	}
    	$str='';
    	if(!empty($where['order_status']))
    	{
    		$str .= " AND `a`.`order_status` = ".addslashes($where['order_status']);
    	}
    	if(!empty($where['order_check_status']))
    	{
    		$str .= " AND `a`.`order_check_status` = ".addslashes($where['order_check_status']);
    	}
    	if(!empty($where['order_pay_status']))
    	{
    		$str .= " AND `a`.`order_pay_status` = ".addslashes($where['order_pay_status']);
    	}
    	if(isset($where['pay_type']) && $where['pay_type'] != "")
    	{
    		$str .= " AND `a`.`order_pay_type` = ".$where['pay_type'];
    	}
    	if(isset($where['department_id'])&&$where['department_id'] != "")
    	{
    		$str .= " AND `a`.`department_id` in(".addslashes($where['department_id']).")";
    	}
    	if(isset($where['channel_class']) && $where['channel_class']){
    		$str .=" and `s`.`channel_class` ='{$where['channel_class']}'";
    	}
    	if(!empty($where['delivery_status'])){
    		$str .= " AND `a`.`delivery_status` = ".addslashes($where['delivery_status']);
    	}
    	if(!empty($where['buchan_status'])){
    		$str .= " AND `a`.`buchan_status` = ".addslashes($where['buchan_status']);
    	}
    	if(isset($where['order_type']) && $where['order_type'] != '')
    	{
    		//$sql .= " AND `a`.`is_xianhuo`  = ".$where['order_type']."";
    	}
    	$detail_str=$str;
    	if($where['time_type']=='add'){//日期为下单时间
    		if(!empty($where['start_time'])){
    			$str.=" AND `create_time` >= '".$where['start_time']." 00:00:00'";
    		}
    		if(!empty($where['end_time'])){
    			$str.=" AND `create_time` <= '".$where['end_time']." 23:59:59'";
    		}
    		$sql=$sql.$str." GROUP BY LEFT(create_time,10) ORDER BY a.create_time DESC";
    	}
    	else{
    		if(!empty($where['start_time'])){
    			$str.=" AND b.create_time >= '".$where['start_time']." 00:00:00'";
    		}
    		if(!empty($where['end_time'])){
    			$str.=" AND b.create_time <= '".$where['end_time']." 23:59:59'";
    		}
    		$sql=$sql.$str."  GROUP BY LEFT(b.create_time,10) ORDER BY b.create_time DESC";
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
	//	echo $data['sql'].'<br><br>';//exit;
		$data['data'] = $this->db()->getAll($data['sql']);
		
		$detail_data=array();
    	$start_key=count($data['data'])-1;
    	if($start_key>=0){
			$detail_start_time=$data['data'][$start_key]['acount_date'];
			$detail_end_time=$data['data'][0]['acount_date'];
			$detail_ids_sql='select id from base_order_info a where create_time>="'.$detail_start_time.'" and create_time<="'.$detail_end_time.' 23:59:59"';
		//	$str=trim($str,' AND');
			$detail_ids_sql.=$detail_str;
	    	$detail_sql="SELECT s.`channel_name`,s.channel_class,o.`department_id` , o.id,o.order_sn, o.create_time,a.create_time send_goods_time,o.buchan_status,o.send_good_status,o.delivery_status,o.order_pay_status,o.is_xianhuo,o.weixiu_status ,
	a.`create_time` pay_time,weixiu.order_time weixiu_start_time ,weixiu.re_end_time ,weixiu.end_time weixiu_end_time,weixiu.is_repair_order,p.order_time buchan_time
		FROM app_order.`base_order_info` o
	    left JOIN  cuteframe.`sales_channels` s ON s.id=o.department_id
		LEFT JOIN (
		SELECT temp1.create_time ,temp1.`order_id`,temp1.action_id FROM app_order.`app_order_action` temp1 INNER JOIN (SELECT MIN(action_id) action_id FROM app_order.`app_order_action` WHERE  pay_status IN(2,3,4)  GROUP BY order_id  ) temp2 ON temp1.`action_id`=temp2.action_id
		 )  a ON a.`order_id`=o.id 
		 LEFT JOIN (SELECT order_time ,confirm_time,re_end_time,end_time ,order_id,order_sn,1 is_repair_order FROM repair_order.app_order_weixiu temp1 INNER JOIN (SELECT MAX(id) id FROM repair_order.app_order_weixiu WHERE (re_end_time!='' OR end_time!='') GROUP BY order_sn) temp2 ON temp1.id=temp2.id ) weixiu
		 ON weixiu.order_sn=o.`order_sn` 
		 LEFT JOIN kela_supplier.`product_info` p ON p.p_sn=o.`order_sn` ";
	    	if($detail_ids_sql){
	    		$detail_sql.=' where o.id in('.$detail_ids_sql.')';
	    	}
	    	$detail_data=$this->db()->getAll($detail_sql);
    	}
    	//echo $detail_sql;
    	$data['detail_data']=array();
    	if($detail_data){
    		foreach ($detail_data as & $val){
    			if($where['time_type']=='add')//日期为下单时间
    				$key=substr($val['create_time'],0,10);
    			else
    				$key=substr($val['send_goods_time'],0,10);
    			if($val['is_repair_order']){//总维修订单
    				if(isset($data['detail_data'][$key]['repair_order_num'])) $data['detail_data'][$key]['repair_order_num']++;
    				else $data['detail_data'][$key]['repair_order_num']=1;
    			}
    			if($val['channel_class']==1){//线上订单
    				if(isset($data['detail_data'][$key]['online_order_num'])) $data['detail_data'][$key]['online_order_num']++;
    				else $data['detail_data'][$key]['online_order_num']=1;
    				if($val['is_xianhuo']==1){//线上现货订单
    					if(isset($data['detail_data'][$key]['online_xianhuo_num'])) $data['detail_data'][$key]['online_xianhuo_num']++;
    					else $data['detail_data'][$key]['online_xianhuo_num']=1;
    					//发货时长
    					if($val['send_goods_time'] && $val['pay_time']){
	    					$diff_day=number_format((strtotime($val['send_goods_time'])-strtotime($val['pay_time']))/(3600*24),2);
	    					if($diff_day>0){
	    						$data['detail_data'][$key]['avg_online_xianhuo_deliver_time'][]=$diff_day;
	    					}
    					}
    				}
    				else{//线上期货订单
    					if(isset($data['detail_data'][$key]['online_qihuo_num'])) $data['detail_data'][$key]['online_qihuo_num']++;
    					else $data['detail_data'][$key]['online_qihuo_num']=1;
    					//发货时长
    					if($val['send_goods_time'] && $val['buchan_time']){
	    					$diff_day=number_format((strtotime($val['send_goods_time'])-strtotime($val['buchan_time']))/(3600*24),2);
	    					if($diff_day>0){
	    						$data['detail_data'][$key]['avg_online_qihuo_deliver_time'][]=$diff_day;
	    					}
    					}
    				}
    				if($val['is_repair_order']==1){//线上维修单
    					if(isset($data['detail_data'][$key]['online_repair_num'])) $data['detail_data'][$key]['online_repair_num']++;
    					else $data['detail_data'][$key]['online_repair_num']=1;
    					//发货时长
    					$weixiu_start_time=$val['weixiu_start_time'];
    					$weixiu_end_time=$val['re_end_time']?$val['re_end_time']:$val['weixiu_end_time'];
    					if($weixiu_start_time && $weixiu_end_time){
    						$diff_day=number_format((strtotime($weixiu_end_time)-strtotime($weixiu_start_time))/(3600*24),2);
    						if($diff_day>0){
    							$data['detail_data'][$key]['avg_online_repair_deliver_time'][]=$diff_day;
    						}
    					}
    				}
    			}
    			else{//线下订单
    				if(isset($data['detail_data'][$key]['offline_order_num'])) $data['detail_data'][$key]['offline_order_num']++;
    				else $data['detail_data'][$key]['offline_order_num']=1;
    				if($val['is_xianhuo']==1){//线下现货订单
    					if(isset($data['detail_data'][$key]['offline_xianhuo_num'])) $data['detail_data'][$key]['offline_xianhuo_num']++;
    					else $data['detail_data'][$key]['offline_xianhuo_num']=1;
    					//发货时长
    					if($val['send_goods_time'] && $val['pay_time']){
    						$diff_day=number_format((strtotime($val['send_goods_time'])-strtotime($val['pay_time']))/(3600*24),2);
    						if($diff_day>0){
    							$data['detail_data'][$key]['avg_offline_xianhuo_deliver_time'][]=$diff_day;
    						}
    					}
    				}
    				else{//线下期货订单
    					if(isset($data['detail_data'][$key]['offline_qihuo_num'])) $data['detail_data'][$key]['offline_qihuo_num']++;
    					else $data['detail_data'][$key]['offline_qihuo_num']=1;
    					//发货时长
    					if($val['send_goods_time'] && $val['buchan_time']){
    						$diff_day=number_format((strtotime($val['send_goods_time'])-strtotime($val['buchan_time']))/(3600*24),2);
    						if($diff_day>0){
    							$data['detail_data'][$key]['avg_offline_xianhuo_deliver_time'][]=$diff_day;
    						}
    					}
    				}
    				if($val['is_repair_order']==1){//线下维修单
    					if(isset($data['detail_data'][$key]['offline_repair_num'])) $data['detail_data'][$key]['offline_repair_num']++;
    					else $data['detail_data'][$key]['offline_repair_num']=1;
    					//发货时长
    					//$weixiu_start_time=$val['weixiu_start_time'];
    					$weixiu_start_time=$val['pay_time'];
    					$weixiu_end_time=$val['re_end_time']?$val['re_end_time']:$val['weixiu_end_time'];
    					if($weixiu_start_time && $weixiu_end_time){
    						$diff_day=number_format((strtotime($weixiu_end_time)-strtotime($weixiu_start_time))/(3600*24),2);
    						if($diff_day>0){
    							$data['detail_data'][$key]['avg_online_repair_deliver_time'][]=$diff_day;
    						}
    					}
    				}
    			}
    		}
    	}
    	foreach ($data['detail_data'] as &$value){
    		$online_acount=$offline_acount=0;
    		if(!isset($value['repair_order_num'])) $value['repair_order_num']=0;
    		if(!isset($value['online_order_num'])) $value['online_order_num']=0;
    		if(!isset($value['online_xianhuo_num'])) $value['online_xianhuo_num']=0;
    		//计算线上现货平均时长
    		if(!isset($value['avg_online_xianhuo_deliver_time'])) $value['avg_online_xianhuo_deliver_time']=0;
    		else {
    			$online_acount++;
    			$value['avg_online_xianhuo_deliver_time']=number_format(array_sum($value['avg_online_xianhuo_deliver_time'])/count($value['avg_online_xianhuo_deliver_time']),2);
    		}
    		if(!isset($value['online_qihuo_num'])) $value['online_qihuo_num']=0;
    		//计算线上期货平均时长
    		if(!isset($value['avg_online_qihuo_deliver_time'])) $value['avg_online_qihuo_deliver_time']=0;
    		else {
    			$online_acount++;
    			$value['avg_online_qihuo_deliver_time']=number_format(array_sum($value['avg_online_qihuo_deliver_time'])/count($value['avg_online_qihuo_deliver_time']),2);
    		}
    		if(!isset($value['online_repair_num'])) $value['online_repair_num']=0;
    		//计算线上维修平均时长
    		if(!isset($value['avg_online_repair_deliver_time'])) $value['avg_online_repair_deliver_time']=0;
    		else {
    			$online_acount++;
    			$value['avg_online_repair_deliver_time']=number_format(array_sum($value['avg_online_repair_deliver_time'])/count($value['avg_online_qihuo_deliver_time']),2);
    		}
    		if($online_acount){
    			$value['avg_online_deliver_time']=($value['avg_online_xianhuo_deliver_time']+$value['avg_online_qihuo_deliver_time']+$value['avg_online_repair_deliver_time'])/$online_acount;
    		}
    		else $value['avg_online_deliver_time']=0;
    		if(!isset($value['offline_order_num'])) $value['offline_order_num']=0;
    		if(!isset($value['offline_xianhuo_num'])) $value['offline_xianhuo_num']=0;
    		//计算线下现货平均时长
    		if(!isset($value['avg_offline_xianhuo_deliver_time'])) $value['avg_offline_xianhuo_deliver_time']=0;
    		else {
    			$offline_acount++;
    			$value['avg_offline_xianhuo_deliver_time']=number_format(array_sum($value['avg_offline_xianhuo_deliver_time'])/count($value['avg_offline_xianhuo_deliver_time']),2);
    		}
    		if(!isset($value['offline_qihuo_num'])) $value['offline_qihuo_num']=0;
    		//计算线下期货平均时长
    		if(!isset($value['avg_offline_qihuo_deliver_time'])) $value['avg_offline_qihuo_deliver_time']=0;
    		else {
    			$offline_acount++;
    			$value['avg_offline_qihuo_deliver_time']=number_format(array_sum($value['avg_offline_qihuo_deliver_time'])/count($value['avg_offline_qihuo_deliver_time']),2);
    		}
    		if(!isset($value['offline_repair_num'])) $value['offline_repair_num']=0;
    		//计算线下维修平均时长
    		if(!isset($value['avg_offline_repair_deliver_time'])) $value['avg_offline_repair_deliver_time']=0;
    		else {
    			$offline_acount++;
    			$value['avg_offline_repair_deliver_time']=number_format(array_sum($value['avg_offline_repair_deliver_time'])/count($value['avg_offline_qihuo_deliver_time']),2);
    		}
    		if($offline_acount){
    			$value['avg_offline_deliver_time']=($value['avg_offline_xianhuo_deliver_time']+$value['avg_offline_qihuo_deliver_time']+$value['avg_offline_repair_deliver_time'])/$offline_acount;
    		}
    		else $value['avg_offline_deliver_time']=0;
    	}
    	return $data;
	}


    //区分期货钻和现货钻
    public function getCreateOrderDetailsInfo($where)
    {
        $sql="SELECT 
                    `a`.`order_sn`,
                    `od`.`qiban_type`,
                    `od`.`dia_type`
                FROM 
                    `app_order`.`base_order_info` `a` 
                    inner join `app_order`.`app_order_details` `od` on `od`.`order_id` = `a`.`id` 
                    inner join  `cuteframe`.`sales_channels` `s` ON `s`.`id`=`a`.`department_id` 
                WHERE 1=1 AND `a`.`is_delete` = 0 ";
        if(isset($where['department_id'])&& $where['department_id'])
        {
            $sql .= " AND `a`.`department_id` in(".addslashes($where['department_id']).")";
        }
        if(isset($where['channel_class']) && $where['channel_class']){
            $sql .=" and `s`.`channel_class` ='{$where['channel_class']}'";
        }else{
            $sql .=" and `s`.`channel_class` in (1,2) ";
        }
        if(isset($where['buchan_type']) )
        {
            if($where['buchan_type']== 'xianhuo'){
                $sql .= " AND `a`.`is_xianhuo`  = '1'";
            }
            elseif($where['buchan_type']== 'qihuo'){
                $sql .= " AND `a`.`is_xianhuo`  = '0'";
            }
        }
        if(!empty($where['buchan_status'])){
            $sql.=" AND `a`.`buchan_status` in (".$where['buchan_status'].") ";
        }
        if(!empty($where['start_time'])){
            $sql.=" AND `a`.`create_time` >= '".$where['start_time']." 00:00:00'";
        }
        if(!empty($where['end_time'])){
            $sql.=" AND `a`.`create_time` <= '".$where['end_time']." 23:59:59'";
        }
        $sql .= " order by a.id desc ";
        //echo $sql;die;
        $data = $this->db()->getAll($sql);
        return $data;
    }

    //区分起版类型
    function getSendOrderDetailsInfo ($where)
    {
        $sql="SELECT 
                    a.order_sn,
                    `od`.`qiban_type`,
                    `od`.`dia_type`
                FROM 
                    app_order.base_order_info a 
                    inner join `app_order`.`app_order_details` `od` on `od`.`order_id` = `a`.`id` 
                    inner JOIN  cuteframe.`sales_channels` s ON s.id=a.department_id  
                    left join app_order.app_order_time at on a.id = at.order_id
                WHERE 1=1 AND a.is_delete=0 ";
        if(isset($where['department_id'])&& $where['department_id'])
        {
            $sql .= " AND `a`.`department_id` in(".addslashes($where['department_id']).")";
        }
        if(isset($where['channel_class']) && $where['channel_class']){
            $sql .=" and `s`.`channel_class` ='{$where['channel_class']}'";
        }else{
            $sql .=" and `s`.`channel_class` in (1,2) ";
        }
        if(isset($where['buchan_type']) )
        {
            if($where['buchan_type']== 'xianhuo'){
                $sql .= " AND `a`.`is_xianhuo`  = '1'";
            }
            elseif($where['buchan_type']== 'qihuo'){
                $sql .= " AND `a`.`is_xianhuo`  = '0'";
            }
        }
        if(!empty($where['buchan_status'])){
            $sql.=" AND `a`.`buchan_status` in (".$where['buchan_status'].") ";
        }
        if(!empty($where['start_time'])){
             $sql .= " AND ((`s`.`channel_class`=2 AND at.allow_shop_time >='".$where['start_time']." 00:00:00') or (`s`.`channel_class`=1 AND a.shipfreight_time >='".$where['start_time']." 00:00:00'))";
        }
        if(!empty($where['end_time'])){
             $sql .= " AND ((`s`.`channel_class`=2 AND at.allow_shop_time <='".$where['end_time']." 23:59:59') or (`s`.`channel_class`=1 AND a.shipfreight_time <='".$where['end_time']." 23:59:59'))";
        }
        $sql .= " order by a.id desc ";
        //echo $sql;die;
        $data = $this->db()->getAll($sql);
        return $data;
    }

    //根据订单号获取符合条件的所有订单明细
    public function getQuFenXianQiHuoW($order_sn)
    {
        $sql = "select `s`.`order_sn`,`d`.`weixiu_status` from `base_order_info` `s` inner join `app_order_details` `d` on `s`.`id` = `d`.`order_id` where `order_sn` in('".implode("','", $order_sn)."')";
        $data = $this->db()->getAll($sql);
        return $data;
    }

}
