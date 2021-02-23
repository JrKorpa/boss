<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 14:58:58
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
				"caigou_info"=>"采购备注",
                'xiangqian'=>'镶嵌方式'

            );
		parent::__construct($id,$strConn);
	}
	function getsql($where=array(),$count='')
	{

		if($count==1){

			$sql = "SELECT sum(`main`.`num`) total FROM `".$this->table()."` `main`";
		}else{
            
			$sql = "SELECT `main`.*,(SELECT `o`.`time` FROM `product_opra_log` `o` WHERE `status` = 3 and `o`.`bc_id` = `main`.`id` order by `o`.`time` desc limit 1) factory_time,(SELECT `t`.`remark` FROM `product_opra_log` `t` WHERE `t`.`bc_id` = `main`.`id` order by `t`.`id` desc limit 1) opra_remark,(SELECT `t`.`time` FROM `product_opra_log` `t` WHERE `t`.`bc_id` = `main`.`id` order by `t`.`id` desc limit 1) time,`c`.`peishi_status`,`boi`.`referer`,`sc`.`channel_class`,`ia`.`value` p_sn_out FROM `".$this->table()."` `main`";
		}
		
		$sql.=" LEFT JOIN `product_info_4c` `c` ON `main`.`id`=`c`.`id` LEFT JOIN `app_order`.`base_order_info` `boi` ON `main`.`p_sn`=`boi`.`order_sn` LEFT JOIN `cuteframe`.`sales_channels` `sc` ON `boi`.`department_id` = `sc`.`id` LEFT JOIN `product_info_attr` `ia` ON `main`.`id` = `ia`.`g_id` and `ia`.`code` = 'p_sn_out' WHERE 1=1";
		//4C布产搜索 begin
		if(isset($where['is_peishi']) && $where['is_peishi']!=''){
		    if((int)$where['is_peishi']){
		        $sql .= " and main.is_peishi>0";
		    }else{
		        $sql .= " and (main.is_peishi is null or main.is_peishi=0)";
		    }		    
		    if(isset($where['peishi_status']) && $where['peishi_status']!=''){
		        $sql.=" and c.peishi_status='{$where['peishi_status']}'";
		    }
		}
		//4C布产搜索 end
		
		if(SYS_SCOPE=='zhanting'){
			$sql .= " AND main.hidden <> 1";
		}

		if(isset($where['ids']) and !empty($where['ids']))
		{
			$where['ids']=implode(',',$where['ids']);
			$sql .= " AND main.id in ({$where['ids']})";
		}else{
			$sql .= " AND main.add_time>'2016-04-15 00:00:00'";
		}
       if (isset($where['consignee']) and $where['consignee'] != "")
		{
			   $sql .= " AND main.consignee like \"%".addslashes($where['consignee'])."%\"";
		}
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
        //新增外部单号
        if(isset($where['p_sn_out']) and $where['p_sn_out'] != "")
        {
            $p_sn_out="'".str_replace(' ',"','",$where['p_sn_out'])."'";

            $sql .= " AND `ia`.`value` in ({$p_sn_out})";
        }
		if(isset($where['style_sn']) and $where['style_sn'] != "")
		{
			$sql .= " AND main.style_sn like \"%".addslashes($where['style_sn'])."%\"";
		}
		if(isset($where['status']) and $where['status'] !== "")
		{
			$sql .= " AND main.status = ".$where['status'];
		}
		if(isset($where['prc_id']) and $where['prc_id'] !== "")
		{
			$sql .= " AND main.prc_id = ".$where['prc_id'];
		}
		if(isset($where['prc_name']) and $where['prc_name'] !== "")
		{
			$sql .= " AND main.prc_name ='".$where['prc_name']."'";
		}
		if(isset($where['prc_ids']) and !empty($where['prc_ids']))
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			$sql .= " AND main.prc_id in ({$where['prc_ids']})";
		}
		if(isset($where['opra_uname']) and $where['opra_uname'] != "")
		{
			$sql .= " AND main.opra_uname='".addslashes($where['opra_uname'])."'";
		}
		
		if(isset($where['production_manager_name']) and $where['production_manager_name'] != "")
		{  
			$sql .= " AND main.production_manager_name='".addslashes($where['production_manager_name'])."'";
		}
		if(isset($where['from_type']) and !empty($where['from_type']))
		{
			$sql .= " AND main.from_type='".addslashes($where['from_type'])."'";
		}
		if(isset($where['bc_style']) and $where['bc_style'] != "")
		{
			$sql .= " AND main.bc_style like \"%".addslashes($where['bc_style'])."%\"";
		}
		if(isset($where['xiangqian']) and $where['xiangqian'] != "")
		{
			$sql .= " AND main.xiangqian like \"%".addslashes($where['xiangqian'])."%\"";
		}
		//add by zhangruiying
		if(isset($where['consignee']) and !empty($where['consignee']))
		{
			$sql .= " AND main.consignee like \"%".addslashes($where['consignee'])."%\"";
		}
		 if(isset($where['buchan_fac_opra[]']) and !empty($where['buchan_fac_opra[]']))
		{
			$where['buchan_fac_opra[]']=implode(',',$where['buchan_fac_opra[]']);
			$sql .= "  AND main.`buchan_fac_opra` in (".$where['buchan_fac_opra[]'].")  ";
		}
 		if(isset($where['esmt_time_start']) and $where['esmt_time_start'] != "")
		{
			$sql .= " AND main.esmt_time >= '{$where['esmt_time_start']} 00:00:00'";
		}
 		if(isset($where['esmt_time_end']) and $where['esmt_time_end'] != "")
		{
			$sql .= " AND main.esmt_time <= '{$where['esmt_time_end']} 23:59:59'";
		}
 		if(isset($where['order_time_start']) and $where['order_time_start'] != "")
		{
			$sql .= " AND main.order_time >= '{$where['order_time_start']} 00:00:00'";
		}
 		if(isset($where['order_time_end']) and $where['order_time_end'] != "")
		{
			$sql .= " AND main.order_time <= '{$where['order_time_end']} 23:59:59'";
		}
		if(isset($where['rece_time']) and $where['rece_time'] != "")
		{
			$sql .= " AND main.rece_time >= '{$where['rece_time']} 00:00:00' AND main.rece_time <= '{$where['rece_time']} 23:59:59'";
		}
		if(isset($where['channel_id']) and $where['channel_id'] !== "")
		{
			$sql .= " AND main.channel_id = ".$where['channel_id'];
		}
		if(isset($where['customer_source_id']) and $where['customer_source_id'] !== "")
		{
			$sql .= " AND main.customer_source_id = ".$where['customer_source_id'];
		}
		if(isset($where['from_type']) and !empty($where['from_type']))
		{
			$sql .= " AND main.from_type ={$where['from_type']}";
		}
		
		if(isset($where['is_alone']) && $where['is_alone'] !== '' )
		{
		    $sql .= " AND main.is_alone ={$where['is_alone']}";
		}
		
		if(isset($where['qiban_type']) and $where['qiban_type'] !== "" )
		{
		    $sql .= " AND main.qiban_type = {$where['qiban_type']}";
		}

		//新增搜索条件
		/* --开始-- */

		if(isset($where['diamond_type']) and $where['diamond_type'] !== "" )
		{
		    $sql .= " AND main.diamond_type = {$where['diamond_type']}";
		}
		
		if(isset($where['to_factory_time_start']) and $where['to_factory_time_start'] != "")
		{
			$sql .= " AND main.to_factory_time >= '{$where['to_factory_time_start']} 00:00:00'";
		}
 		if(isset($where['to_factory_time_end']) and $where['to_factory_time_end'] != "")
		{
			$sql .= " AND main.to_factory_time <= '{$where['to_factory_time_end']} 23:59:59'";
		}

		if(isset($where['wait_dia_starttime_start']) and $where['wait_dia_starttime_start'] != "")
		{
			$sql .= " AND main.wait_dia_starttime >= '{$where['wait_dia_starttime_start']} 00:00:00'";
		}
 		if(isset($where['wait_dia_starttime_end']) and $where['wait_dia_starttime_end'] != "")
		{
			$sql .= " AND main.wait_dia_starttime <= '{$where['wait_dia_starttime_end']} 23:59:59'";
		}

		if(isset($where['wait_dia_endtime_start']) and $where['wait_dia_endtime_start'] != "")
		{
			$sql .= " AND main.wait_dia_endtime >= '{$where['wait_dia_endtime_start']} 00:00:00'";
		}
 		if(isset($where['wait_dia_endtime_end']) and $where['wait_dia_endtime_end'] != "")
		{
			$sql .= " AND main.wait_dia_endtime <= '{$where['wait_dia_endtime_end']} 23:59:59'";
		}

		if(isset($where['wait_dia_finishtime_start']) and $where['wait_dia_finishtime_start'] != "")
		{
			$sql .= " AND main.wait_dia_finishtime >= '{$where['wait_dia_finishtime_start']} 00:00:00'";
		}
 		if(isset($where['wait_dia_finishtime_end']) and $where['wait_dia_finishtime_end'] != "")
		{
			$sql .= " AND main.wait_dia_finishtime <= '{$where['wait_dia_finishtime_end']} 23:59:59'";
		}

		if(isset($where['oqc_pass_time_start']) and $where['oqc_pass_time_start'] != "")
		{
			$sql .= " AND main.oqc_pass_time >= '{$where['oqc_pass_time_start']} 00:00:00'";
		}
 		if(isset($where['oqc_pass_time_end']) and $where['oqc_pass_time_end'] != "")
		{
			$sql .= " AND main.oqc_pass_time <= '{$where['oqc_pass_time_end']} 23:59:59'";
		}

		if(isset($where['rece_time_start']) and $where['rece_time_start'] != "")
		{
			$sql .= " AND main.rece_time >= '{$where['rece_time_start']} 00:00:00'";
		}
 		if(isset($where['rece_time_end']) and $where['rece_time_end'] != "")
		{
			$sql .= " AND main.rece_time <= '{$where['rece_time_end']} 23:59:59'";
		}

		if(isset($where['referer']) and $where['referer'] != "")
		{
			$sql .= " AND boi.referer = '".$where['referer']."'";
		}

        if(isset($where['channel_class']) and $where['channel_class'] != "")
        {
            $sql .= " AND `sc`.`channel_class` = '".$where['channel_class']."'";
        }
        if(isset($where['is_quick_diy']) && $where['is_quick_diy']!=""){
            $sql .= " AND main.is_quick_diy = ".$where['is_quick_diy'];
        }
        if(isset($where['is_combine']) && $where['is_combine']!=""){            
            if($where['is_combine']==0){
                $sql .= " AND (main.is_combine is null or main.is_combine = ".$where['is_combine'].")";
            }else{
                $sql .= " AND main.is_combine = ".$where['is_combine'];
            }
        }
        if(!empty($where['combine_goods_id'])){
            $combine_goods_id = "'".str_replace(' ',"','",$where['combine_goods_id'])."'";
            $sql .= " AND main.combine_goods_id in ({$combine_goods_id})";
        }
        if(!empty($where['product_not_ordersn'])){
            if($where['product_not_ordersn'] == '2'){
                $_str_day = '= 2';
            }elseif($where['product_not_ordersn'] == '3'){
                $_str_day = '= 3';
            }elseif($where['product_not_ordersn'] == '4'){
                $_str_day = '> 3';
            }else{
                $_str_day = '< 2';
            }
            $sql .= " AND main.`status` = 3 AND datediff(now(), main.to_factory_time) - (
                SELECT
                    count(j.id)
                FROM
                    cuteframe.holiday_date j
                WHERE
                    main.to_factory_time <= concat(j. DAY, ' 00:00:00')
                AND now() >= concat(j. DAY, ' 23:59:59')) {$_str_day} ";
        }
        //echo $sql;
		/* --新增结束-- */
		if(isset($where['is_extended']) and $where['is_extended']!=='')
        {
					if($where['is_extended']!=2)
					{
							$sql.=" AND main.status in(4,7) and (0";
							if($where['is_extended']==1)
							{
								$sql .= " OR main.`esmt_time`<'".date('Y-m-d')."'";
							}
							if($where['is_extended']==0)
							{
								$sql .= " OR (date_format(main.`esmt_time`,'%Y-%m-%d') BETWEEN '".date('Y-m-d')."' AND '".date('Y-m-d',time()+2*86400)."')";
							}
							$sql.=")";
					}
					else
					{
						$sql .= " AND (main.status not in(4,7) or(main.status in(4,7) and main.`esmt_time`>'".date('Y-m-d',time()+2*86400)."'))";
					}
        }
        if($count!=1){
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
        }

		//add end
	    //echo $sql;
		return $sql;
	}
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql=$this->getsql($where);
		//echo $sql;
		$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	function pageList2 ($where)
	{
		$sql = $this->getsql($where,1);
		return  $this->db()->getOne($sql);
	}

	function getdownload($where)
	{
		$sql=$this->getsql($where);
		$res = $this->db()->getAll($sql);
		$arr=array('cart','color','clarity','zhengshuhao','caizhi','jinse','jinzhong','zhiquan','kezi','face_work');
		//对取到的所有数据进行款式属性补全
		foreach ($res as $key=>$val)
		{
			//石重cart	颜色color	净度clarity	证书号zhengshuhao 材质caizhi	金色jinse	金重jinzhong	指圈zhiquan	刻字kezi	表面工艺face_work
			$sql = "select code,value,name from product_info_attr where g_id='{$val['id']}'";//edit by zhangruiying
			$tmp = $this->db()->getAll($sql);
			foreach ($tmp as $v)
			{
					$val[$v['code']] = $v['value'];
					$res[$key] = $val;
			}
			$res[$key]['attr']=$tmp;

		}
		return $res;
	}

        //获取款号。
        function getStyleSnById($where){
            $sql = "SELECT `id`, `style_sn` ,`from_type`, `p_sn` , `consignee`,`prc_id`,`prc_name` FROM `".$this->table()."` WHERE 1 ";
            if ($where['bc_id'] != "")
            {
                $sql .= " AND id='".$where['bc_id']."'";
            }
            $data = $this->db()->getRow($sql);
            return $data;
        }
        public function getConsignee($order_sn) {

            $ret = ApiModel::sales_api(array("order_sn"), array($order_sn), "GetOrderInfoByOrdersn");
            return $ret;
        }

        function getPsn($consignee) {
            $ret = ApiModel::sales_api(array("consignee"), array($consignee), "GetOrderSnByConsignee");

            return $ret['return_msg'];
        }
    /*
	 * 根据布产ID 返回布产单和订单的关联关系以及布产单状态
	*/
	public function judgeBcGoodsRel($id)
	{
		$sql = "SELECT  pg.`bc_id`, pg.`goods_id`,p.`status` FROM `product_goods_rel` as pg left join `product_info` as p  on pg.`bc_id`=p.`id` WHERE p.`id` = {$id}";
		return $this->db()->getRow($sql);
	}
	
	
	


	//分配跟单人-事物  JUAN ------目测这个方法已经失效
	public function sel_opra_uname($params)
	{
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			$sql = "update ".$this->table()." set status = 2,opra_uname = '".$params['opra_uname']."' where id = ".$params['id'];
			$pdo->query($sql);

			$remark = "布产单分配跟单人：".$params['opra_uname'];
			//如果是从订单来源的布产要推送状态到订单
			if($params['from_type'] == 2)
			{
				$rec = $this->judgeBcGoodsRel($params['id']);
				if(!empty($rec)){//布产单和订单有绑定关系
					$api = new ApiSalesModel();
					$r = $api->GetStyleInfoBySn($rec['goods_id'],2);
					if($r['error'])//推送改变订单布产状态失败回滚
					{
						$pdo->query('');
					}
				}
				//回写操作日志到订单日志
				$rex = $this->Writeback($params['id'], $remark);
				if(!$rex){//推送失败，回滚
					$pdo->query('');
				}
			}

			//记录布产操作日志
			$time = date('Y-m-d H:i:s');
			$sql = "INSERT INTO `product_opra_log`(`bc_id`, `status`, `remark`, `uid`, `uname`, `time`) VALUES ({$params['id']},2,'{$remark}',{$_SESSION['userId']},'{$_SESSION['userName']}','{$time}')";
			$pdo->query($sql);
		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}

	/**
	* 普通查询
	*/
	public function Select2($fields=' * ' , $where=' 1 LIMIT 1 ' , $type = 'one'){
		$sql = "SELECT {$fields} FROM `product_info` WHERE {$where} ORDER BY `id` DESC";
		if($type == 'one'){
			return $this->db()->getOne($sql);
		}else if($type == 'row'){
			return $this->db()->getRow($sql);
		}else if($type == 'all'){
			return $this->db()->getAll($sql);
		}
	}

	// 布产操作，通过接口，回写订单操作日志表
	public function Writeback($buchan_id, $remark){
		//获取订单号
		$fields = " `id`, `p_sn` ";
		$where = " `id`={$buchan_id} ";
		$data = $this->Select2($fields, $where , 'row');
		//根据订单号获取订单相关信息
		$orderInfo = ApiSalesModel::GetDeliveryStatus($data['p_sn'] , " id,order_status,order_pay_status,send_good_status ");
		$order_id = $orderInfo['return_msg']['id'];
		$order_status = $orderInfo['return_msg']['order_status'];	//订单状态
		$order_pay_status = $orderInfo['return_msg']['order_pay_status'];		//支付状态
		$send_good_status = $orderInfo['return_msg']['send_good_status'];		//发货状态
		$create_time = date('Y-m-d H:i:s');
		$create_user = $_SESSION['userName'];
		//回写日志
		$res = ApiSalesModel::addOrderAction($order_id , $order_status , $send_good_status , $order_pay_status , $create_time , $create_user , $remark);
		if($res['error']){
			return false;
		}else{
			return true;
		}
	}
	
	
	#根据批量ids查询状态是否生产中 是 则返回true 否则返回false
	public function IsStatusStart($ids)
	{
		$id_s  = join("','",$ids);
		$sql = "select count(1) from ".$this->table()." where id in ('{$id_s}') and status != 4";
		$count = $this->db()->getOne($sql);
		if ($count>0)
			return false;
		else
			return true;
	}

	#根据批量ids查询状态是已分配状态 是 则返回true 否则返回false
	public function IsStatusFenpei($ids)
	{
		$id_s  = join("','",$ids);
		$sql = "select count(1) from ".$this->table()." where id in ('{$id_s}') and status != 3";
		$count = $this->db()->getOne($sql);
		if ($count>0)
			return false;
		else
			return true;
	}
	#根据批量ids查询状态是否生产中和部分出厂 7  是 则返回true 否则返回false
	public function IsStatusChuchang($ids)
	{
		$id_s  = join("','",$ids);
		$sql = "select count(1) from ".$this->table()." where id in ('{$id_s}') and (status != 4 and status !=7)";
		$count = $this->db()->getOne($sql);
		if ($count>0)
			return false;
		else
			return true;
	}

	#根据批量ids查询状态是已分配3待分配2初始化1
	public function IsStatusFactory($ids)
	{
		$result = array('success'=>0,'error'=>'','style_sn'=>'');
		$id_s  = join("','",$ids);
		$sql = "select count(1) from ".$this->table()." where id in ('{$id_s}') and status in (1,2,3)";
		$count = $this->db()->getOne($sql);
		if($count<count($ids))
		{
			$result['error']="布产单状态不正确，单据已经在生产，不能分配工厂";
			return $result;
		}
		$sql = "select distinct style_sn from ".$this->table()." where id in ('{$id_s}') and status in (1,2,3) ";
		$style_num = $this->db()->getAll($sql);
		if(count($style_num)>1)
		{
			$result['error']="布产单中款式只能选择同款";
			return $result;
		}
		$result['style_sn'] = $style_num[0]['style_sn'];
		$result['success'] = 1;
		return $result;
	}

	#根据批量ids查询是托管店还是经销商布产单
	public function checkCompanyType($ids)
	{
		$result = array('success'=>0,'error'=>'','company_type'=>'');
		$id_s  = join("','",$ids);
		
		$sql = "select distinct c.company_type from product_info p,app_order.app_order_details d,app_order.base_order_info o left join cuteframe.sales_channels s on o.department_id=s.id left join cuteframe.company c on s.company_id=c.id where p.p_id=d.id and d.order_id=o.id and p.id in ('{$id_s}') ";
		$company_type_num = $this->db()->getAll($sql);		
		if(count($company_type_num)>1)
		{
			$result['error']="裸钻布产批量分配工厂只能选择同公司类型的布产单";
			return $result;
		}
		$result['company_type'] = $company_type_num[0]['company_type'];
		$result['success'] = 1;
		return $result;
	}

	#根据批量ids查询状态是否同一种单据
	public function Isfromtype($ids)
	{
		$id_s  = join("','",$ids);
		$sql = "select from_type from ".$this->table()." where id in ('{$id_s}') group by from_type";
		return  $this->db()->getAll($sql);
	}
	#根据批量ids查询状态是否有采购单
	public function IsfromtypeCaigou($ids)
	{
		$id_s  = join("','",$ids);
		$sql = "select count(1) from ".$this->table()." where id in ('{$id_s}') and from_type='1'";
		return  $this->db()->getOne($sql);
	}
	#根据石重匹配镶口规则  @param $cart 石重
	public function GetXiangKou($cart){
		$cart = $cart * 1000;
		$cart = intval($cart);
		if ($cart >= 0 && $cart < 100)
		{
			return "";//0-0.1镶口为空
		}
		if ($cart >= 100 && $cart <= 150)
		{
			return "0.100";
		}
		if ($cart > 10000)
		{//当钻石大小大于10克拉的也为空
			return "";
		}
		$arr = array();
		$j = 0;
		for($i = 150;$i <= 10000; $i = $i+100) {
			$arr [$j]= ($i);
			$arr2[$j] = ($i+100);
			$j ++;
		}
		$count = count($arr);
		for($i = 0; $i <$count; $i ++)
		{
			if ( $cart > $arr[$i] && $cart <= $arr2[$i]){
				$num = $arr2[$i] - 50;
				$num = sprintf("%.3f",$num/1000);
				return $num;
			}
		}
	}

	/**
	 * 过滤布产号
	 */
	public function filterBcsn($bcSN_arr)
	{
		$bcSN_arr = array_filter($bcSN_arr);
		$data = array();
		foreach ($bcSN_arr as $bc_sn) {
			$sql = "SELECT count(*) FROM `product_info` WHERE `bc_sn` = '".$bc_sn."'";
			$res = $this->db()->getOne($sql);
			if($res){$data[]=$bc_sn;}
		}
		return $data;
	}

	/**
	 * 获取送钻信息
	 * @param $bcSN_arr
	 * @return bool
	 */
	public function getSendDrillInfo($bcSN_arr){
		$data = array();
		foreach ($bcSN_arr as $k=>$bc_sn) {
			if(!empty($bc_sn)){
				$sql = "SELECT `id`,`bc_sn`,`consignee` FROM `product_info` WHERE `bc_sn` = '".$bc_sn."'";
				$res = $this->db()->getRow($sql);
				if(!empty($res)){
					$data[$k]['bc_sn'] = $res['bc_sn'];
					$data[$k]['consignee'] = $res['consignee'];
					$sql = "SELECT `code`,`value` FROM `product_info_attr` WHERE `g_id` = '".$res['id']."'";
					$g = $this->db()->getAll($sql);
					$g = array_column($g,'value','code');
					$data[$k]['cart'] = (isset($g['cart']))?$g['cart']:'';//石重
					$data[$k]['color'] = (isset($g['color']))?$g['color']:'';//颜色
					$data[$k]['clarity'] = (isset($g['clarity']))?$g['clarity']:'';//净度
					$data[$k]['zhengshuhao'] = (isset($g['zhengshuhao']))?$g['zhengshuhao']:'';//证书号
				}
			}
		}
		return $data;
	}

	public function createCSV($name,$title,$content){
		$ymd = date("Ymd").'_'.rand(100,999);
		header("Content-Disposition: attachment;filename=".iconv('utf-8','gbk',$name).$ymd.".csv");
		$fp = fopen('php://output', 'w');
		$title = eval('return '.iconv('utf-8','gbk',var_export($title,true).';')) ;
		fputcsv($fp, $title);
		foreach($content as $k=>$v)
		{
			fputcsv($fp, $v,',','"');
		}
		fclose($fp);
	}
	public function getBcTime($id)
	{
		if(intval($id)==0)
		{
			return '';
		}
		$sql="select `time` from `product_opra_log` where bc_id=$id and status=2";
		$res = $this->db()->getRow($sql);
		if($res)
		{
			return $res['time'];
		}
		return '';
	}

	/**
	* 布产分配工厂
	*/
	public function distributionFac($data){
	    $bc_id  = $data['bc_id'];
	    $prc_id = $data['prc_id'];
	    $prc_name = $data['prc_name'];
	    $from_type = $data['from_type'];
	    $bc_sn = $data['bc_sn'];
	    $p_sn  = $data['p_sn'];
	    $order_sn = $p_sn;
		//根据工厂 找默认的跟单人
		$oprauserModel = new ProductFactoryOprauserModel(14);
		$salesModel = new SalesModel(27); 
		$man = $oprauserModel->select2('`opra_user_id`,`opra_uname`,`production_manager_name`'," `prc_id`={$prc_id} " , $type = 'row');
		if(empty($man)){
			return array('success'=>0 , 'error'=> "工厂：<span style='color:red;'>{$prc_name}</span> 没有绑定跟单人");
		}
		if($man['production_manager_name'] != ''){
			$production_manager_name=",生产经理：".$man['production_manager_name'];
			$status = 2;
		}else{
			$status = 3;
			$production_manager_name='';
		}
		$time = date('Y-m-d H:i:s');
		try{
			//记录分配的工厂 , 记录分配的跟单人 , 变更布产状态
			$tip = "记录分配的工厂 ,记录分配的跟单人,变更布产状态";
			$sql = "UPDATE `product_info` SET `prc_id` = {$prc_id} , `prc_name` = '{$prc_name}' , `opra_uname` = '{$man['opra_uname']}',`production_manager_name` = '{$man['production_manager_name']}' , `status` = $status,to_factory_time='{$time}',`edit_time`='{$time}' WHERE `id` = {$bc_id}";
			$this->db()->query($sql);

			//记录日志
			$remark = "分配工厂成功：布产单：".$bc_sn.",分配工厂：".$prc_id.",跟单人：".$production_manager_name;//prc_name//$man['opra_uname']
			//如果是从订单来源的布产要推送状态到订单
			if($from_type == 2)
			{
			    //布产单和订单有绑定关系
				$rec = $this->judgeBcGoodsRel($bc_id);
				if(!empty($rec)){		
				    $tip = "更新订单商品布产状态";
				    $detail_id = $rec['goods_id'];
				    $sql = 'update app_order.app_order_details set buchan_status=3 where id='.$detail_id;
					$this->db()->query($sql);
					//更新订单主表 布产状态
					$tip = "更新订单主表布产状态";
					$salesModel->updateOrderBCStatus($order_sn);	
				}
				//回写操作日志到订单日志
				$tip = "回写操作日志到订单日志";
				$rex = $this->Writeback($bc_id, $remark);
				if(!$rex){//推送失败，回滚
					$this->db()->query('');
				} 
			}
			//记录布产操作日志	
			$tip = "记录布产操作日志";
			$sql = "INSERT INTO `product_opra_log`(`bc_id`, `status`, `remark`, `uid`, `uname`, `time`) VALUES ({$bc_id}, $status,'{$remark}',{$_SESSION['userId']},'{$_SESSION['userName']}','{$time}')";
			$this->db()->query($sql);
			return array('success'=>1,'error'=>'');
		}
		catch(Exception $e){//捕获异常
			return array('success' => 0 , 'error'=>"分配工厂失败。提示：distributionFac函数执行{$tip}失败。<!--{$sql}-->");
		}		
	}
	/**************************************************************************************
	function:to_factory_pl
	description:批量生产操作
	para: array('0'=>id1,'1'=>id2) BY LYH
	***************************************************************************************/
	public function to_factory_pl($ids)
	{
		$pdo = $this->db()->db();//pdo对象
		try{

            //获取允许承接其它工厂布产单的组用户
			$kela_user_list=$this->db()->getAll('select u.user_id from cuteframe.group g,cuteframe.group_user u where g.id=u.group_id and g.id=4');
			$kela_user_list=array_column($kela_user_list,'user_id');			
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			foreach ($ids as $key=>$id)
			{
				$model = new ProductInfoModel($id,14);
				$model_pw = new AppProcessorWorktimeModel(13);
				$prc_id = $model->getValue('prc_id');
				$order_sn = $model->getValue('p_sn');
				$bc_sn = $model->getValue('bc_sn');
				$from_type=$model->getValue("from_type");
				$order_type=$from_type==2?1:2;
				//除去指定组用户(id=4) 外，工厂只能承接分配给本工厂的布产单
                if(!in_array($_SESSION['userId'],$kela_user_list) && $model->getValue('opra_uname') <> $_SESSION['userName']){
					$pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					return array('success' => 0 , 'error'=> '接单失败:不能承接分配给其它工厂的单');
                }

				#1、计算标准出厂时间---修改布产状态、工厂标准出厂时间、接单时间、修改时间
				$gendan_info = $model_pw->getInfoById($prc_id,$order_type);
				if(empty($gendan_info))
				{
					$normal_time = time()+(3600*24);
					$time = date("Y-m-d",$normal_time);
				}
				else
				{
					$time = $model_pw->js_normal_time($gendan_info['normal_day'],$gendan_info['is_rest']);
				}
				$now_time=date("Y-m-d");
				$sql = "update product_info set esmt_time='{$time}',order_time='{$now_time}',edit_time='{$now_time}',status=4,buchan_fac_opra=2 where id={$id}";
				$pdo->query($sql);

				#2、记录操作日志
				$newdo=array(
					'bc_id'		=> $id,
					'status'	=> 4,
					'remark'	=> "布产单".$bc_sn."接单并开始生产，批量操作",
					'uid'		=> $_SESSION['userId']?$_SESSION['userId']:0,
					'uname'		=> $_SESSION['userName']?$_SESSION['userName']:'第三方',
					'time'		=> date('Y-m-d H:i:s')
				);

				$sql = "INSERT INTO `kela_supplier`.`product_opra_log` (`id`, `bc_id`, `status`, `remark`, `uid`, `uname`, `time`) VALUES (NULL, '{$newdo['bc_id']}', '{$newdo['status']}', '{$newdo['remark']}','{$newdo['uid']}', '{$newdo['uname']}', '{$newdo['time']}');";
				$pdo->query($sql);

				#2.1 推送订单日志
				ApiModel::sales_api(array('order_no','create_user','remark'),array($order_sn,$newdo['uname'],$newdo['remark']),'AddOrderLog');

				#3、判断是布产单是否有关联货品 若关联 更新布产操作状态到 货品详情表
				$rec = $model->judgeBcGoodsRel($id);
				if(!empty($rec))
				{
					$keys =array('update_data');
					$vals =array(array(array('id'=>$rec['goods_id'],'buchan_status'=>4)));
					$ret = ApiModel::sales_api($keys, $vals, 'UpdateOrderDetailStatus');
					/*
					if($ret['error'])
					{
						$pdo->rollback();//事务回滚
						$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
						return array('success' => 0 , 'error'=> '事物执行失败，操作不成功----修改订单商品状态接口失败');//接口中缺少开始生产操作日志
					}
					*/
				}
			}

		}
		catch(Exception $e)
		{
			//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error'=> '事物执行失败，操作不成功');
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success' => 1);

	}
		/**
	* 布产分配工厂  lyh
	*/
	public function DistributionFac_pl($bc_ids , $prc_id , $prc_name,$man,$peishi = 0)
	{
		$time = date('Y-m-d H:i:s');
		if($man['production_manager_id']!=''){
			$production_manager_name=",生产经理：".$man['production_manager_name'];
			$status=2;
		}else{
			$production_manager_name='';
			$status=3;
		}
		$remark = "分配工厂".$prc_name.",跟单人："."{$production_manager_name},批量操作";
		$pdo = $this->db()->db();//$man['opra_uname']
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			/*************************1 所有工厂数据库操作 start************************/
			foreach ($bc_ids as $key=>$bc_id)
			{
				#1、记录分配的工厂 , 记录分配的跟单人 , 变更布产状态
				$sql = "UPDATE `product_info` SET `prc_id` = {$prc_id} , `prc_name` = '{$prc_name}' , `opra_uname` = '{$man['opra_uname']}' , `production_manager_name` = '{$man['production_manager_name']}', `status` = $status,`def_factory_sn`='',`def_factory_name`='' WHERE `id` = {$bc_id}";
				$pdo->query($sql);
				#2、记录布产操作日志			
				$time = date('Y-m-d H:i:s');
				$sql = "INSERT INTO `product_opra_log`(`bc_id`, `status`, `remark`, `uid`, `uname`, `time`) VALUES ({$bc_id}, $status,'布产单{$remark}',{$_SESSION['userId']},'{$_SESSION['userName']}','{$time}')";
				$pdo->query($sql);
				#3、是否需要配石 	// 配石保存 /配石日志 /操作日志
				if($peishi)
				{
					//根据布产号查订单号
					$model_pi = new ProductInfoModel($bc_id,13);
					$order_sn = $model_pi->getValue('p_sn');
					$from_type = $model_pi->getValue('from_type');
					$newdo=array(
							"order_sn"			=>	 $order_sn,
							"rec_id"            =>   $bc_id,
							"peishi_status"		=>   0,
							"add_user"			=>	 $_SESSION['userName']
						);
					$sql = "INSERT INTO `kela_supplier`.`peishi_list` (`id`, `order_sn`, `rec_id`, `peishi_status`, `add_time`, `last_time`, `add_user`) VALUES (NULL, '{$newdo['order_sn']}', '{$newdo['rec_id']}', '{$newdo['peishi_status']}', '{$time}', CURRENT_TIMESTAMP, '{$newdo['add_user']}');";
					$pdo->query($sql);
					$id = $pdo->lastInsertId();
					$sql = "INSERT INTO `kela_supplier`.`peishi_list_log` (`id`, `peishi_id`, `add_time`, `action_name`, `remark`) VALUES (NULL, {$id},  '{$time}', '{$newdo['add_user']}', '批量生成配石单')";
					$pdo->query($sql);
					$sql = "INSERT INTO `kela_supplier`.`product_opra_log` (`id`, `bc_id`, `status`, `remark`, `uid`, `uname`, `time`) VALUES (NULL, '{$newdo['rec_id']}', '{$status}', '布产单批量生成配石单{$id}', '{$_SESSION['userId']}', '{$newdo['add_user']}', '{$time}');";
					$pdo->query($sql);
					if($from_type == 2)
					{
						$bc_sn = $model_pi->getValue('bc_sn');
						ApiModel::sales_api(array('order_no','create_user','remark'),array($order_sn,$newdo['add_user'],"布产单{$bc_sn}批量生成配石单{$id}"),'AddOrderLog');
					}

				}
			}
			/*************************所有工厂数据库操作 end***************************/


			/*************************3订单接口处理  start************************/
			#1、查询布产来源为订单来源的布产单，并且能在布产订单明细表中有效的记录（1采购、2订单）
			$ids_str = join ("','",$bc_ids);
			$sql = "SELECT  pg.`goods_id`,p.`p_sn`,p.bc_sn FROM `product_goods_rel` as pg left join `product_info` as p  on pg.`bc_id`=p.`id` WHERE p.`id` in ('{$ids_str}') and pg.status=0 and p.from_type=2";
			$goods_arr = $this->db()->getAll($sql);
			if($goods_arr)
			{
				$goods_arrs = array();
				//组织需要修改订单状态、货品状态的形式
				foreach ($goods_arr as $key=>$val)
				{
					$goods_arrs[$key]['id'] = $val['goods_id'];
					$goods_arrs[$key]['buchan_status'] = 3;
				}
				//var_dump($goods_arrs);exit;
				$ret = ApiModel::sales_api(array('update_data'), array($goods_arrs), 'UpdateOrderDetailStatus');
				//var_dump($ret);exit;
				if($ret['error'] == 1)
				{
					$pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					return array('success' => 0 , 'error'=> '事物执行失败，操作不成功--订单接口');
				}
			}


			#2、加订单日志
			$create_user = $_SESSION['userName'];
			foreach ($goods_arr as $val)
			{
				$orderInfo = ApiModel::sales_api(array('order_no','create_user','remark'),array($val['p_sn'],$create_user,'布产单'.$val['bc_sn'].$remark),'AddOrderLog');
			//var_dump($orderInfo);exit;
			}

		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error'=> '事物执行失败，操作不成功'.$sql);
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success'=> 1 , 'error'=>'操作成功');
	}
	/*************************************************************************
	function: getAllFactoryUser
	decription:获取所有跟单人工厂
	***************************************************************************/
	public function getAllFactoryUser()
	{
		$sql  =  " select a.id as factory_id,a.name as factory_name,a.code ,p.opra_uname as gendan,'' as factory_sn from product_factory_oprauser as p,app_processor_info as a where p.prc_id=a.id ";
		//echo  $sql;exit;
		return $this->db()->getAll($sql);
	}
	/*************************************************************************
	function: getFactoryUserByID
	decription:获取指定工厂
	***************************************************************************/
	public function getFactoryUserByID($factory_id)
	{
		$sql  =  " select a.id as factory_id,a.name as factory_name,a.code ,p.opra_uname as gendan,'' as factory_sn from product_factory_oprauser as p,app_processor_info as a where p.prc_id=a.id  and a.id='{$factory_id}'";
		//echo  $sql;exit;
		return $this->db()->getAll($sql);
	}

	/**
	 * 通过证书号 获取送钻信息
	 */
	public function getInfoByCretificate($cate){
		$data = [
			'bc_info'=>array(),
			'order_info'=>array()
		];
		$sql = "SELECT b.`id`,b.`bc_sn`,b.`p_sn`,b.`style_sn`,b.`status`,b.`buchan_fac_opra`,b.`from_type`,b.`prc_id`,b.`prc_name`,b.`opra_uname`,b.`consignee`,b.`customer_source_id`,b.`channel_id`,b.`bc_style`,b.`xiangqian`";
		$sql .=	" FROM `product_info` AS b,`product_info_attr` AS a WHERE b.`id` = a.`g_id` AND a.`code` = 'zhengshuhao' AND a.`value` = '".$cate."'";
		$bc_info = $this->db()->getRow($sql);
		if(!empty($bc_info)){
			$data['bc_info'] = $bc_info;
		}
		$saleApi = new ApiSalesModel();
		$data['order_info'] = $saleApi->getOrderInfoByCate($cate);
		return $data;
	}
	function getAttrInfoByBcID($id)
	{
			$sql = "select code,value,name from product_info_attr where g_id='{$id}'";
			$tmp = $this->db()->getAll($sql);
			$res=array();
			foreach ($tmp as $v)
			{
					$res[$v['code']] =$v['value'];
			}
			return $res;
	}
	function CheckIsToFactory($ids,$prc_ids)
	{
		if($ids)
		{
			$ids=implode(',',$ids);
			$prc_ids=implode(',',$prc_ids);
			$sql="select count(*) as num from product_info where id in($ids) and prc_id in($prc_ids) and status=3 and from_type=2";
			$num=$this->db()->getOne($sql);
			if($num>0)
			{
				return true;
			}
		}
		return false;
	}

    public function getOrderSnByBcsn($bc_sn) {
        $sql = "select `p_sn`,`id` from ".$this->table()." where `bc_sn` in ($bc_sn)";
        $res = $this->db()->getAll($sql);
        return $res;
    }
    
    public function getOrderSnByIds($bc_id) {
        $sql = "select `p_sn`,`id`, `bc_sn` from ".$this->table()." where `id` in ($bc_id)";
        $res = $this->db()->getAll($sql);
        return $res;
    }

	function getToFactoryList($ids,$prc_ids=array(),$form_type=0)
	{
		$sql=$this->getsql(array('ids'=>$ids,'prc_ids'=>$prc_ids,'status'=>3,'from_type'=>$form_type));
		return $rows=$this->db()->getAll($sql);
	}

	/**重新提交布产操作*/
	public function re_buchan_save($ids,$buchan_type)
	{
		$id_s = join("','",$ids);
		$time = date('Y-m-d H:i:s');
		$userName = $_SESSION['userName'];
		$userId = $_SESSION['userId'];
		$pdo = $this->db()->db();
		//获取布产号对应的所有订单号以方便添加订单日志 、修改订单状态
		$sql = "select id,p_sn,bc_sn from product_info where id in ('{$id_s}') and from_type=2 ";
		$order_sn_arr = $this->db()->getAll($sql);

		try
		{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			if ($buchan_type ==1 )
			{
				#1、布产状态：已分配（status：3）、生产状态：未操作（buchan_fac_opra:1)、order_time 接单时间、esmt_time 标准出厂时间 、rece_time 工厂交货时间 清空;remark (重新布产),edit_time (当前时间) 	buchan_times +1
				$sql = "update product_info set status=3,buchan_fac_opra=1,order_time=NULL,esmt_time='0000-00-00',rece_time='0000-00-00 00:00:00',edit_time='{$time}',remark='重新布产',buchan_times= 	buchan_times+1 where id in ('{$id_s}')";
				//file_put_contents('d:/lyh.txt',$sql."\n",FILE_APPEND);
				$pdo->query($sql);
				#2、布产日志添加 
				$sql = 'INSERT INTO `product_opra_log` (`id`, `bc_id`, `status`, `remark`, `uid`, `uname`, `time`) VALUES ';
				foreach ($ids as $k=>$v)
				{
					$sql .= " (NULL,$v,3,'布产单重新布产','{$userId}','{$userName}','{$time}'),";
					//判断是布产单是否有关联货品 若关联 更新布产操作状态到 货品详情表  liyanhong
					$ProductInfoModel = new ProductInfoModel($v,14);
					$from_type = $ProductInfoModel->getValue('from_type');
					if($from_type==2){
						$rec = $ProductInfoModel->judgeBcGoodsRel($v);
						if(!empty($rec)){
							$keys =array('update_data');
							$vals =array(array(array('id'=>$rec['goods_id'],'buchan_status'=>3)));
							$ret = ApiModel::sales_api($keys, $vals, 'UpdateOrderDetailStatus');
						}
					}
					
				}
				$sql = substr($sql,0,-1);
				//file_put_contents('d:/lyh.txt',$sql."\n",FILE_APPEND);
				$pdo->query($sql);
				#3、删除所有工厂出货明细
				$sql = "delete from product_shipment where bc_id in ('{$id_s }')";
				//file_put_contents('d:/lyh.txt',$sql."\n",FILE_APPEND);
				$pdo->query($sql);
				#4、配石标红   添加配石日志
				$sql = "select id from peishi_list where rec_id in ('{$id_s}') ";
				$peishi_id_arr = $this->db()->getAll($sql);
				foreach ($peishi_id_arr as $v)
				{
					if(!empty($v['id']))
					{
						$sql = "INSERT INTO `peishi_list_log` (`id`, `peishi_id`, `add_time`, `action_name`, `remark`) VALUES(NULL, '{$v['id']}', '{$time}', '{$userName}', '重新布产')";
						$pdo->query($sql);
					}
				}
				$remark = '重新布产';

			}
			else if ($buchan_type ==2)
			{
				#1、还原布产状态（生产中或部分出厂）、如果存在多次出厂，只删除最后一笔工厂出货明细
				$sql1 = 'INSERT INTO `product_opra_log` (`id`, `bc_id`, `status`, `remark`, `uid`, `uname`, `time`) VALUES ';

				foreach ($ids as $id)
				{
					//查询日志  不是已出厂状态的前一状态
					$sql = "select status from  `product_opra_log` where bc_id ='{$id}' and status!=9 order by id desc limit 0,1 ";
					$status = $this->db()->getOne($sql);
					$sql = "update product_info set status='{$status}' where id='{$id}' ";
					$pdo->query($sql);
					$sql = "select MAX(id) from product_shipment where bc_id={$id}";
					$ps_id = $this->db()->getOne($sql);
					$sql ="delete from product_shipment where id = {$ps_id} ";
					$pdo->query($sql);
					$sql1 .= " (NULL,$id,'{$status}','布产单继续生产','{$userId}','{$userName}','{$time}'),";
					
				//判断是布产单是否有关联货品 若关联 更新布产操作状态到 货品详情表
					$ProductInfoModel = new ProductInfoModel($id,14);
					$from_type = $ProductInfoModel->getValue('from_type');
					if($from_type==2){
						$rec = $ProductInfoModel->judgeBcGoodsRel($id);
						if(!empty($rec)){
							$keys =array('update_data');
							$vals =array(array(array('id'=>$rec['goods_id'],'buchan_status'=>$status)));
							$ret = ApiModel::sales_api($keys, $vals, 'UpdateOrderDetailStatus');
						}
					}

				}
				#2、还原生产状态（开始生产）、rece_time 工厂交货时间 清空;remark (继续生产),edit_time (当前时间)
				$sql = "update product_info set buchan_fac_opra=2,rece_time='0000-00-00 00:00:00',remark='继续生产',edit_time='{$time }',buchan_times= 	buchan_times+1 where id in ('{$id_s}') ";
				$pdo->query($sql);
				#3、布产日志添加 
				$sql1 = substr($sql1,0,-1);
				$pdo->query($sql1);
				$remark = '继续生产';
				#4、配石标红  ???  
				$sql = "select id from peishi_list where rec_id in ('{$id_s}') ";
				$peishi_id_arr = $this->db()->getAll($sql);
				foreach ($peishi_id_arr as $v)
				{
					if(!empty($v['id']))
					{
						$sql = "INSERT INTO `peishi_list_log` (`id`, `peishi_id`, `add_time`, `action_name`, `remark`) VALUES(NULL, '{$v['id']}', '{$time}', '{$userName}', '重新布产')";
						$pdo->query($sql);
					}
				}
			}
			else if ($buchan_type == 3)
			{
				#3》缺货转生产
				//a、布产状态初始化、生产状态未操作
				$sql = "update product_info set buchan_fac_opra=1,status=1,remark='缺货转生产',edit_time='{$time }',buchan_times= buchan_times+1 where id in ('{$id_s}') ";
				$pdo->query($sql);
				//file_put_contents('d:/lyh.txt',$sql."\n",FILE_APPEND);
				$sql = "INSERT INTO `kela_supplier`.`product_opra_log` (`id`, `bc_id`, `status`, `remark`, `uid`, `uname`, `time`) VALUES ";
				//布产单添加操作日志
				foreach ($ids as $k=>$v)
				{
					$msg = '';
				//判断是布产单是否有关联货品 若关联 更新布产操作状态到 货品详情表
					$ProductInfoModel = new ProductInfoModel($v,14);
					$from_type = $ProductInfoModel->getValue('from_type');
					$p_sn = $ProductInfoModel->getValue('p_sn');
					$bc_sn = $ProductInfoModel->getValue('bc_sn');
					if($from_type==2){
						$rec = $ProductInfoModel->judgeBcGoodsRel($v);
						if(!empty($rec)){
							$keys =array('update_data');
							$vals =array(array(array('id'=>$rec['goods_id'],'buchan_status'=>1)));
							$ret = ApiModel::sales_api($keys, $vals, 'UpdateOrderDetailStatus');
							//根据订单明细id解绑货品
							$jieguo = ApiModel::warehouse_api(array('order_goods_id'),array($rec['goods_id']),'jiebang');
							//订单日志
							if ($jieguo[0])
							{
							ApiModel::sales_api(array('order_no','create_user','remark'),array($p_sn,$userName,"布产单{$bc_sn}缺货转生产,货号{$jieguo[0]}自动解绑"),'AddOrderLog');
							}
							else
							{
							ApiModel::sales_api(array('order_no','create_user','remark'),array($p_sn,$userName,"布产单{$bc_sn}缺货转生产"),'AddOrderLog');
							}
						}
					}

					$sql .= " (NULL,$v,1,'布产单{$bc_sn}缺货转生产','{$userId}','{$userName}','{$time}'),";
				}
				$sql = substr($sql,0,-1);

				//file_put_contents('d:/lyh.txt',$sql,FILE_APPEND);
				$pdo->query($sql);
				$remark = '缺货转生产';
			
			}
			##、公共操作，批量订单更新日志，备注（布产BCXXX重新生产），配货状态修改为未配货、发货状态改为未发货
			if ($order_sn_arr)
			{
				foreach ($order_sn_arr as $val)
				{
					if ($buchan_type != 3)
					{
						//非要这样 。。。。。。 挪到上面  因备注不同
						$bc_sn = $val['bc_sn'];
						$r1 = ApiModel::sales_api(array('order_no','create_user','remark'),array($val['p_sn'],$userName,'布产单'.$bc_sn.$remark),'AddOrderLog');
					}
					$r2 =ApiModel::sales_api(array('order_sn','send_good_status','delivery_status'),array($val['p_sn'],1,1),'updateOrderInfoStatus');
					
				}
				
			}

		}
		catch(Exception $e)
		{//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return array('success' => 0 , 'error'=> '事物执行失败，操作不成功'.$sql);
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return array('success'=> 1 , 'error'=>'操作成功');
	}
	
	public function channelBC($bc_id){
		if(empty($bc_id)) return false;
		//修改布产单的布产状态
		$sql = "UPDATE `product_info` SET `status` = '11' WHERE `id` = '".$bc_id."'";
		$res = $this->db()->query($sql);
		return $res;
	}
	
	public function getfromtype($bc_id){
		$sql = "SELECT `from_type`,`status` FROM `product_info` WHERE `id`={$bc_id}";
		return  $this->db()->getRow($sql);
	}
	
	
	
	public function addBcGoodsLog($bc_id,$remark){
		$proArr=$this->getfromtype($bc_id);
		$olddo = array();
		$newdo=array(
				'bc_id'		=> $bc_id,
				'status'	=> $proArr['status'],
				'remark'	=> $remark,
				'uid'		=> $_SESSION['userId']?$_SESSION['userId']:0,
				'uname'		=> $_SESSION['userName']?$_SESSION['userName']:'第三方',
				'time'		=> date('Y-m-d H:i:s')
		);
		$logModel = new ProductOpraLogModel(14);
		return $res = $logModel->saveData($newdo,$olddo);
	}
	
	/*
	*通过id更新出厂时间
	*@param $type 1 ID  2 供应商ID
	*
	*/
	public function updateEsmttime($id,$esmt_time,$type=1){
		if($type==1){
			$sql ="update ".$this->table()." set esmt_time='".$esmt_time."' where id=".$id;

		}else{
			$sql ="update ".$this->table()." set esmt_time='".$esmt_time."' where prc_id =".$id;
		}
		return $this->db()->query($sql);
	}

	/*
	*通过布产单ID获取镶嵌信息
	*
	*/
	public function getXiangqianById($id){
		$sql = "select xiangqian from ".$this->table()." where id=".$id;
		return $this->db()->getOne($sql);

	}
	
	/*
	*获取一个供应商的所有布产单信息(出厂时间)
	*
	*/
	public function getBuChanInfoByPrc_id($prc_id,$from_type){
		$sql ="select * from ".$this->table()." where prc_id ='".$prc_id."' AND from_type=".$from_type;
		return $this->db()->getAll($sql);

	}
	
	/*
	 *获取一个供应商的所有布产单信息(出厂时间)
	 *
	 */
	public function getBuChanInfoByPrc_ids($prc_id,$from_type){
		$sql ="select * from ".$this->table()." where prc_id ='".$prc_id."' AND from_type=".$from_type." AND status IN (4,7)";
		return $this->db()->getAll($sql);
	
	}

	/*
	*更新单个布产单的出厂时间
	*
	*/
	public function getBuChanInfoById($id){
		$sql = "select * from kela_supplier.product_info where id=".$id;
		return $this->db()->getRow($sql);

	}
	 /**
     * 取一条布产详情---JUAN
     */
    public function GetProductInfoByOrderID($orderDetailID) {
        
        $sql = "SELECT `id`, `bc_sn`, `p_id`, `p_sn`, `style_sn`, `status`, `num`, `prc_id`, `prc_name`, `opra_uname`, `add_time`, `esmt_time`, `rece_time`, `info`,`from_type`,`consignee`,`is_alone` FROM `product_info` WHERE p_id='{$orderDetailID}'";
      
        $data = $this->db()->getRow($sql);
        return $data;
    }

	/*
	* 更新钻石类型
	*
	*/
	public function updateDiamondTypeById($id,$diamond_type){
		$sql ="UPDATE ".$this->table()."  SET diamond_type=".$diamond_type." WHERE id=".$id;
		return $this->db()->query($sql);
	}
	
	/*
	* 批量更新分配工厂时间
	*
	*/
	public function updateTo_factory_timeById($ids,$to_factory_time){
		$sql ="UPDATE ".$this->table()." SET to_factory_time='".$to_factory_time."' WHERE id IN(".$ids.")";
		return $this->db()->query($sql);

	}

	/*
	* 更新等钻开始时间
	*
	*/
	public function updateWait_dia_starttimeById($id,$wait_dia_starttime){
		$sql ="UPDATE ".$this->table()." SET wait_dia_starttime='".$wait_dia_starttime."' WHERE id =".$id;
		return $this->db()->query($sql);

	}

	/*
	* 更新实际等钻结束时间
	*
	*/
	public function updateWait_dia_endtimeById($id,$wait_dia_endtime){
		$sql ="UPDATE ".$this->table()." SET wait_dia_endtime='".$wait_dia_endtime."' WHERE id =".$id;
		return $this->db()->query($sql);

	}

	/*
	* 更新预计等钻完成时间
	*
	*/
	public function updateWait_dia_finishtimeById($id,$wait_dia_finishtime){
		$sql ="UPDATE ".$this->table()." SET wait_dia_finishtime='".$wait_dia_finishtime."' WHERE id =".$id;
		return $this->db()->query($sql);

	}

	/*
	* 更新预OQC质检通过时间
	*
	*/
	public function updateOqc_pass_timeById($id,$oqc_pass_time){
		$sql ="UPDATE ".$this->table()." SET oqc_pass_time='".$oqc_pass_time."' WHERE id =".$id;
		return $this->db()->query($sql);

	}

	public function getOrderSnsByBcsn($id) {
		$sql = "select `p_sn`,`id` from ".$this->table()." where `id` = '{$id}'";
		$res = $this->db()->getRow($sql);
		return $res;
	}
	
	public function updateStatus($id,$status){
		$sql="UPDATE ".$this->table()." SET status=$status WHERE id=$id";
		return $this->db()->query($sql);
	} 

    public function get_bc_sn($id, $use_cache = true) {
        if ((SYS_SCOPE == 'zhanting' &&  $id <= 9946293) || (SYS_SCOPE == 'boss' &&  $id <= 103047)) {
            return BCD_PREFIX.$id;
        }
        if ($use_cache && $this->_dataObject) {
            $bc_sn = $this->getValue('bc_sn');
            if ($bc_sn) return $bc_sn;
        }
        return $this->db()->getOne("select `bc_sn` from ".$this->table()." where `id` = '{$id}'");
    }
}
?>