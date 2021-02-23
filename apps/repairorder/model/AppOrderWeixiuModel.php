<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderWeixiuModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 23:17:46
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
			"old_goods_id"=>"原货号",
			"goods_id"=>"货号",
			"consignee"=>"客户姓名",
			"repair_act"=>"维修动作",
			"repair_man"=>"维修负责人id",
			"repair_factory"=>"工厂",
			"repair_make_order"=>"维修制单人",
			"remark"=>"备注",
			"status"=>"状态",
			"order_time"=>"下单时间",
			"confirm_time"=>"确认时间",
			"factory_time"=>"下单时间",
			"end_time"=>"预计出厂时间",
			"re_end_time"=>"完成时间",
			"receiving_time"=>"收货时间",
			"frequency"=>"维修次数",
			"after_sale"=>"是否是售后维修 0不是，1是",
			"change_sn"=>"转仓单号",
			"receiving_time"=>"收货时间",
			"qc_status"=>"质检状态，0:未质检，1：质检通过，2：质检未过",
			"qc_times"=>"质检次数",
			"qc_nopass_dt"=>"最新质检未通过时间",
            "order_class"=>"线上线下"
			);
		parent::__construct($id,$strConn);
	}
	



		/**
	pageList ,分页
	**/
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		
		$sql  = "SELECT  m.id,m.order_id,m.order_sn,m.rec_id,m.re_type,m.goods_id,m.consignee,m.repair_act,m.repair_man,m.repair_factory,m.repair_make_order,m.status,m.order_time,m.confirm_time,m.factory_time,m.end_time,m.re_end_time,m.receiving_time,m.qc_status,m.qc_times,m.weixiu_price,m.qc_nopass_dt,if(`sc`.`channel_class` = 1, '线上', '线下') as 'channel_class' FROM `repair_order`.`".$this->table()."` AS m left join `app_order`.`base_order_info` `oi` on `m`.`order_sn` = `oi`.`order_sn` left join `cuteframe`.`sales_channels` `sc` on `oi`.`department_id` = `sc`.`id` ";
		$str = '';
		if(!empty($where['id']))
		{
			//add by zhangruiying 去除用户不小心输入或粘贴的空白字符和中文,号替换
			$where['id']=preg_replace("/[sv]+/",'',$where['id']);
			$where['id']=str_replace(" ",',',$where['id']);
			$where['id']=str_replace("，",',',$where['id']);
			//add end
			$item =explode(",",$where['id']);
			$goodsid = "";
			foreach($item as $key => $val) {
				if ($val != '') {
					if($goodsid){
						$goodsid .= ",'".trim($val)."'";
					}else{
						$goodsid .= "'".trim($val)."'";
					}
				}
			}
			$where['id'] = $goodsid;
			$str .= " `m`.`id` in (".$where['id'].") AND ";
			
			//$str .="`m`.`id`='".$where['id']."' AND ";
		}

        //zt隐藏
        if(SYS_SCOPE == 'zhanting')
        {
            //$str .="`m`.`status` not in(5,6) AND ";
            $str.=" `m`.`hidden` <> 1 AND ";
        }
		
		if(!empty($where['re_type']))
		{
			$str .="`m`.`re_type`='".$where['re_type']."' AND ";
		}
		if(!empty($where['order_sn']))
		{
			$str .="`m`.`order_sn`='".$where['order_sn']."' AND ";
		}
		if(!empty($where['old_goods_id']))
		{
			$str .="`m`.`old_goods_id`='".$where['old_goods_id']."' AND ";
		}
		if(!empty($where['goods_id']))
		{
			$str .="`m`.`goods_id`='".$where['goods_id']."' AND ";
		}
		if(!empty($where['rec_id']))
		{
			$str .="`m`.`rec_id`='".$where['rec_id']."' AND ";
		}
		if(!empty($where['consignee']))
		{
			$str .="`m`.`consignee`='".$where['consignee']."' AND ";
		}
		if(!empty($where['repair_factory']))
		{
			$str .="`m`.`repair_factory`='".$where['repair_factory']."' AND ";
		}
 	 	if(!empty($where['status']))
		{
			$str .="`m`.`status`='".$where['status']."' AND ";
		}
		if(isset($where['status_in']) && $where['status_in'])
		{
			$str .="`m`.`status` in (".$where['status_in'].") AND ";
		}	
		if(isset($where['frequency']) && $where['frequency'])
		{
			$str .="`m`.`frequency`=".$where['frequency']." AND ";
		} 
		if(!empty($where['repair_act']))
		{
			$str .= " `m`.`repair_act` like \"%".addslashes($where['repair_act'])."%\"  AND ";
		}
		if(!empty($where['order_time_s']))
		{
			$str .= "`m`.`order_time` >= '".$where['order_time_s']." 00:00:00' AND ";
		}
		if(!empty($where['order_time_e']))
		{
			$str .= "`m`.`order_time` <= '".$where['order_time_e']." 23:59:59' AND ";
		}

		if(!empty($where['confirm_time_s']))
		{
			$str .= "`m`.`confirm_time` >= '".$where['confirm_time_s']." 00:00:00' AND ";
		}
		if(!empty($where['confirm_time_e']))
		{
			$str .= "`m`.`confirm_time` <= '".$where['confirm_time_e']." 23:59:59' AND ";
		}

		if(!empty($where['factory_time_s']))
		{
			$str .= "`m`.`factory_time` >= '".$where['factory_time_s']." 00:00:00' AND ";
		}
		if(!empty($where['factory_time_e'] ))
		{
			$str .= "`m`.`factory_time` <= '".$where['factory_time_e']." 23:59:59' AND ";
		}

		if(!empty($where['receiving_time_s'] ))
		{
			$str .= "`m`.`receiving_time` >= '".$where['receiving_time_s']." 00:00:00' AND ";
		}
		if(!empty($where['receiving_time_e'] ))
		{
			$str .= "`m`.`receiving_time` <= '".$where['receiving_time_e']." 23:59:59' AND ";
		}

		if(!empty($where['re_end_time_s']))
		{
			$str .= "`m`.`re_end_time` >= '".$where['re_end_time_s']." 00:00:00' AND ";
		}
		if(!empty($where['re_end_time_e']))
		{
			$str .= "`m`.`re_end_time` <= '".$where['re_end_time_e']." 23:59:59' AND ";
		}
		
		// 2015-10-24增加
		if(!empty($where['qc_status']))
		{
			$str .= "`m`.`qc_status` = '".$where['qc_status']."' AND ";
		}
		if(!empty($where['qc_times']))
		{
			$str .= "`m`.`qc_times` = '".$where['qc_times']."' AND ";
		}
		
		if(!empty($where['qc_nopass_dt_s']))
		{
			$str .= "`m`.`qc_nopass_dt` >= '".$where['qc_nopass_dt_s']." 00:00:00' AND ";
		}
		if(!empty($where['qc_nopass_dt_e']))
		{
			$str .= "`m`.`qc_nopass_dt` <= '".$where['qc_nopass_dt_e']." 23:59:59' AND ";
		}
        if(!empty($where['channel_class']))
        {
            $str .= "`sc`.`channel_class` = ".$where['channel_class']." AND ";
        }

		if($str)
		{
			$str = rtrim($str,"AND ");
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY m.id DESC";
		//return $sql;exit;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        //获取目标货号，“维修订单”完毕时间后的第一笔已保存/已审核状态的M/WF单的制单时间，作为【出货时间】并带入调拨单号作为【出货单号】，若已保存的调拨单取消了，返回空值；
        if($data['data']){
            foreach ($data['data'] as $key => $value) {
                $data['data'][$key]['out_goods_time'] = '';//出货时间
                $data['data'][$key]['out_goods_bill'] = '';//出货单号
                $data['data'][$key]['end_time_log'] = '';//质检时间
                $data['data'][$key]['end_user_log'] = '';//质检人
                if(!empty($value['goods_id']) && !empty($value['order_time'])){
                    $goods_id = $value['goods_id'];
                    if($value['re_type'] == '6'){
                        $sql = "select id,create_time from warehouse_shipping.virtual_return_bill where g_id = '{$goods_id}' and bill_status = 2 and bill_type = '无账调拨单' order by create_time asc limit 1";
                        $biinfo = $this->db()->getRow($sql);
                        if(!empty($biinfo)){
                            $data['data'][$key]['out_goods_time'] = $biinfo['create_time'];
                            $data['data'][$key]['out_goods_bill'] = $biinfo['id'];
                        }
                    }else{
                        $sql = "select wb.bill_no,wb.create_time from warehouse_shipping.warehouse_bill wb
    inner join warehouse_shipping.warehouse_bill_goods bg on wb.id = bg.bill_id
    where bg.goods_id = '".$value['goods_id']."' and wb.bill_type in('M','WF') and wb.bill_status in(1,2) and wb.create_time >= '".$value['order_time']."' order by create_time asc limit 1";
                        $biinfo = $this->db()->getRow($sql);
                        if(!empty($biinfo)){
                            $data['data'][$key]['out_goods_time'] = $biinfo['create_time'];
                            $data['data'][$key]['out_goods_bill'] = $biinfo['bill_no'];
                        }
                    }
                }
                $sql = "select date_time,user_name from app_order_weixiu_log where do_id = ".$value['id']." and content like '维修单完毕%' order by id desc limit 1";
                $loginfo = $this->db()->getRow($sql);
                if(!empty($loginfo)){
                    $data['data'][$key]['end_time_log'] = $loginfo['date_time'];
                    $data['data'][$key]['end_user_log'] = $loginfo['user_name'];
                }
                //是否超期
                //【是否超期】：栏位值“超期/未超期”；
                //“超期”：【出货时间】-【制单时间】的天数，再根据是否有周日定为3天/4天期限，如果逾期，视为超期；
                $data['data'][$key]['is_overdue'] = '否';
                $expire_time = $this->getendday($value['order_time'],3);
                //var_dump($data['data'][$key]['out_goods_time']>$expire_time);die;
                if($data['data'][$key]['out_goods_time'] > $expire_time){
                    $data['data'][$key]['is_overdue'] = '是';
                }
                //选择出货时间筛选
                if(!empty($where['out_goods_s']) && !empty($where['out_goods_e'])){
                    //var_dump($where['out_goods_s'],$where['out_goods_e']);die;
                    if(empty($data['data'][$key]['out_goods_time']) || $data['data'][$key]['out_goods_time'] < $where['out_goods_s']."00:00:00" || $data['data'][$key]['out_goods_time'] > $where['out_goods_e']."23:59:59")
                        unset($data['data'][$key]);continue;
                }
                //选择是否超期筛选
                if(!empty($where['is_overdue']) && $data['data'][$key]['is_overdue'] != $where['is_overdue']) unset($data['data'][$key]);continue;
            }
        }
        //var_dump($data);die;
		return $data;
	}
        
	function getWeixiuOrderInfo($id) 
	{
		if (!empty($id))
		{
		    $sql = "select * from `app_order_weixiu` where `id`='{$id}'";
			$data = $this->db()->getRow($sql);
			return $data;
		}
		else 
		{
			return '';
		}
	}

	public function update_qc ()
	{
		// 4.1维修单状态为完毕：如果有操作过质检未通过，质检次数=操作过“质检未通过”的次数+1，质检状态=“质检通过”，最新质检未过时间=最后一次操作质检未通过的时间；
		// 如果没有操作过质检未通过，质检次数=1，质检状态=质检通过，最新质检未过时间为空。
		 
		$sql = "select do_id,count(*) as ts,date_time from  app_order_weixiu_log
		where  content like '%质检未过%' group by do_id ";
		$dataarr = $this->db()->getPageList($sql,array(),1,999999999,false);
		
		$sql0 = "select id from  app_order_weixiu";
		// $data0 = $this->db()->getRow($sql0);
		$data0arr = $this->db()->getPageList($sql0,array(),1,999999999,false);
		
		
		$data0 = $data = $doidarr = array();
		if(!empty($dataarr["data"]))
		{
			$data = $dataarr["data"];
		}
		
		if(!empty($data0arr["data"]))
		{
			$data0 = $data0arr["data"];
		}
		
		foreach($data as $key => $arr)
		{
			$doidarr[] = $arr["do_id"];
		}
			
		// print_r($data);
		// die();
		
		foreach($data0 as $key => $arr)
		{
			$id = $arr["id"];
			if(in_array($id,$doidarr))
			{
				foreach($data as $key => $arr)
				{
					$do_id = $arr["do_id"];
					$date_time = $arr["date_time"];
					$ts = $arr["ts"];
					$sql = "update app_order_weixiu set qc_times = $ts + 1,qc_status = 1,qc_nopass_dt = '$date_time'  where  status = 5 and  id = '$do_id'";
					$ret = $this->db()->query($sql);
					if($ret)
					{
						echo "更新".$do_id."成功！"."<br>";
					}
					else
					{
						echo "<span style = 'color:#F00;'>更新".$do_id."失败！"."</span><br>";
					}
				}
			}
			else
			{
				$date_time = "0000-00-00 00:00:00";
				$sql = "update app_order_weixiu set qc_times = 1,qc_status = 1,qc_nopass_dt = '$date_time'  where  status = 5 and  id = '$id'";
				$ret1 = $this->db()->query($sql);
				if($ret1)
				{
					echo "更新".$id."成功！"."<br>";
				}
				else
				{
					echo "<span style = 'color:#F00;'>更新".$id."失败！"."</span><br>";
				}
			}
				
		}
		
		 
		// 4.2维修单状态为收货：如果有操作过质检未通过，质检次数=操作过“质检未通过”的次数+1，质检状态=“质检通过”，最新质检未过时间=最后一次操作质检未通过的时间；如果没有操作过质检未通过，质检次数=1，质检状态=质检通过，最新质检未过时间为空
	  
		$doidarr = array();
		foreach($data as $key => $arr)
		{
			$doidarr[] = $arr["do_id"];
		}
			
		foreach($data0 as $key => $arr)
		{
			$id = $arr["id"];
			if(in_array($id,$doidarr))
			{
				foreach($data as $key => $arr)
				{
					$do_id = $arr["do_id"];
					$date_time = $arr["date_time"];
					$ts = $arr["ts"];
					$sql = "update app_order_weixiu set qc_times = $ts + 1,qc_status = 1,qc_nopass_dt = '$date_time'  where  status = 6 and  id = '$do_id'";
					$ret = $this->db()->query($sql);
					if($ret)
					{
						echo "更新".$do_id."成功！"."<br>";
					}
					else
					{
						echo "<span style = 'color:#F00;'>更新".$do_id."失败！"."</span><br>";
					}		
				}
			}
			else
			{
				$date_time = "0000-00-00 00:00:00";
				$sql = "update app_order_weixiu set qc_times = 1,qc_status = 1,qc_nopass_dt = '$date_time'  where  status = 6 and  id = '$id'";
				$ret1 = $this->db()->query($sql);
				if($ret1)
				{
					echo "更新".$id."成功！"."<br>";
				}
				else
				{
					echo "<span style = 'color:#F00;'>更新".$id."失败！"."</span><br>";
				}
			}
				
		}
		
		// 4.3维修单状态为非完毕、非收货：如果有操作过质检未通过，质检次数=操作过“质检未通过”的次数，质检状态=“质检未过”，最新质检未过时间=最后一次操作质检未通过的时间；如果没有操作过质检未通过，质检次数=0，质检状态=未质检，最新质检未过时间为空
 
		$doidarr = array();
		foreach($data as $key => $arr)
		{
			$doidarr[] = $arr["do_id"];
		}
			
		foreach($data0 as $key => $arr)
		{
			$id = $arr["id"];
			if(in_array($id,$doidarr))
			{
				foreach($data as $key => $arr)
				{
					$do_id = $arr["do_id"];
					$date_time = $arr["date_time"];
					$ts = $arr["ts"];
					$sql = "update app_order_weixiu set qc_times = $ts,qc_status = 2,qc_nopass_dt = '$date_time'  where  status != 5 and status != 6 and id = '$do_id'";
					$ret = $this->db()->query($sql);
					if($ret)
					{
						echo "更新".$do_id."成功！"."<br>";
					}
					else
					{
						echo "<span style = 'color:#F00;'>更新".$do_id."失败！"."</span><br>";
					}		
				}
			}
			else
			{
				$date_time = "0000-00-00 00:00:00";
				$sql = "update app_order_weixiu set qc_times = 0,qc_status = 3,qc_nopass_dt = '$date_time'  where status != 5 and status != 6 and  id = '$id'";
				$ret1 = $this->db()->query($sql);
				if($ret1)
				{
					echo "更新".$id."成功！"."<br>";
				}
				else
				{
					echo "<span style = 'color:#F00;'>更新".$id."失败！"."</span><br>";
				}
			}
				
		}
		

		
		// 5、历史维修订单完毕时间清洗
		// 只有当维修单状态为收货且完毕时间为空，需要清洗，完毕时间=收货时间
		
			$sql = "update app_order_weixiu set re_end_time = receiving_time  where  status = 6 and (re_end_time = null or re_end_time = '0000-00-00 00:00:00')";
			$ret2 = $this->db()->query($sql);
			if($ret2)
			{
				echo "更新历史维修订单完毕时间成功！"."<br>";
			}
			else
			{
				echo "<span style = 'color:#F00;'>更新历史维修订单完毕时间失败！"."</span><br>";
			}
		
		 
	}

	
	
	function getGoodsAttr($goods_id)
	{
	   
		$ret = ApiModel::warehouse_api(array('goods_id'), array($goods_id), "GetWarehouseGoodsByGoodsid");
		//print_r($ret);exit;
		return $ret['return_msg'];
	}
	/*******************************
	function:CheckBc
	description:检查维修单中是否有重复的布产号（除"5" => "完毕","6" => "收货","7" => "取消"）
	*********************************/
	public function CheckBc ($rec_id) 
	{
		$sql = "select count(1) from app_order_weixiu where rec_id = '{$rec_id}' AND status not in (5,6,7) limit 1";
		//echo $sql;exit;
		return $this->db()->getOne($sql);
	}
	/*************************************************
	function : getInfoByids
	description:通过ids获取维修信息
	**************************************************/
	public function getInfoByids($ids) 
	{
		$sql = "SELECT * FROM ".$this->table()." WHERE id IN ('".join("','", $ids)."')";
		return $this->db()->getAll($sql);
	}
	
	public function EditWeixuStatus($id,$status){
		$sql="UPDATE ".$this->table()." SET status={$status} WHERE id={$id}";
		return $this->db()->query($sql);
	}
	//质检状态更新--2015-10-24
	public function EditWeixuQc($id,$status){
		$sql="UPDATE ".$this->table()." SET qc_status={$status} WHERE id={$id}";
		return $this->db()->query($sql);
	}
	//质检未通过时间更新--2015-10-24
	public function EditQcNopDt($id,$dt){
		$sql="UPDATE ".$this->table()." SET qc_nopass_dt='{$dt}' WHERE id={$id}";
		return $this->db()->query($sql);
	}
	//质检次数更新--2015-10-24
	public function EditQctimes($id,$times){
		$sql="UPDATE ".$this->table()." SET qc_times=qc_times+{$times} WHERE id={$id}";
		return $this->db()->query($sql);
	}
	
	
	//添加维修日志
	public function setWeiXiuLog($arr){
		if(isset($arr['do_id']) && $arr['do_id']==''){
			return false;
		}
		if(isset($arr['user_name']) && $arr['user_name']==''){
			return false;
		}
		if(isset($arr['do_type']) && $arr['do_type']==''){
			return false;
		}
		if(isset($arr['content']) && $arr['content']==''){
			return false;
		}
		$date_time=date("Y-m-d H:i:s",time());
		$sql="INSERT INTO app_order_weixiu_log  (`do_id`,`date_time`,`user_name`,`do_type`,`content`) VALUES(".$arr['do_id'].",'".$date_time."','".$arr['user_name']."','".$arr['do_type']."','".$arr['content']."')";
		return $this->db()->query($sql);
		
	}
	
	public function updateWeixiu($set,$id){
		$sql="UPDATE  ".$this->table()." SET {$set} WHERE id={$id}";
		return $this->db()->query($sql);
	}

    /**
    * 求取从某日起经过一定天数后的日期,
    * 排除周日
    * @param $start       开始日期
    * @param $offset      经过天数
    * @return
    *  examples:输入(2010-06-25,5),得到2010-07-02
    */
    public function getendday( $start='now', $offset=0){
        $starttime = strtotime($start);
        $tmptime = $starttime + 24*3600;
        while( $offset > 0 ){
            $weekday = date('w', $tmptime);
            $tmpday = date('Y-m-d', $tmptime);
            if($weekday != 0){//不是周末
                $offset--;
            }
            $tmptime += 24*3600;
        }
        return $tmpday;
    }
	
	
}

?>