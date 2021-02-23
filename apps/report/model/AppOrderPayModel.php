<?php

/**
 *  -------------------------------------------------
 *   @file		: AppOrderPayActionModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 18:16:49
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderPayModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_order_pay_action';
        $this->pk = 'pay_id';
        $this->_prefix = '';
        $this->_dataObject = array("pay_id" => " ",
            "order_id" => " ",
            "order_sn" => " ",
            "goods_sn" => " ",
            "goods_name" => " ",
            "order_time" => " ",
            "order_amount" => " ",
            "deposit" => " ",
            "balance" => " ",
            "attach_sn" => " ",
            "remark" => " ",
            "pay_time" => " ",
            "pay_type" => " ",
            "pay_channel" => " ",
            "order_consignee" => " ",
            "pay_account" => " ",
            "pay_sn" => " ",
            "proof_sn" => "凭证号",
            "leader" => " ",
            "leader_check" => " ",
            "opter_name" => " ",
            "repay_time" => " ",
            "department" => "销售渠道",
            "status" => "0=未提报，1=已提报，2=已审核，3=有问题",
            "pay_checker" => "财务审核人",
            "pay_check_time" => "财务审核时间",
            "system_flg" => "系统识别,0展厅,1ecshop后台");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url AppOrderPayActionController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE 1 ";
        $sql .= " ORDER BY pay_id DESC";
        $_data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        $data = array();
        $data[0]['order_id'] = 50;
        $data[0]['order_sn'] = '2014112797275';
        $data[0]['user_name'] = 'amdin';
        $data[0]['create_time'] = '2014-11-27 08:20:20';
        $data[0]['order_price'] = 2000;
        $data[0]['pay_price'] = 1500;
        $data[0]['real_return_price'] = 0;
        $data[0]['yingshou_price'] = 500;
        $_data['data'] = $data;
        return $_data;
    }
    function getAllListA ($where,$page,$pageSize=10,$useCache=true){
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE 1 ";
        $str='';
        if(isset($where['order_sn']) && $where['order_sn']){
            $str .= " and `order_sn` = '{$where['order_sn']}'";
        }
        if(isset($where['department']) && $where['department']){
            $str .= " and `department` = {$where['department']}";
        }
        if(isset($where['pay_type']) && $where['pay_type']){
            $str .= " and `pay_type` = '{$where['pay_type']}'";
        }
        if(isset($where['start_time_p']) && $where['start_time_p']){
            $str .= " and `pay_time` >= '{$where['start_time_p']} 00:00:00'";
        }
        if( isset($where['end_time_p']) && $where['end_time_p']){
            $str .= " and `pay_time` <= '{$where['end_time_p']} 59:59:59'";
        }
        if(isset($where['start_time']) && $where['start_time']){
            $str .= " and `order_time` >= '{$where['start_time']} 00:00:00'";
        }
        if( isset($where['end_time']) && $where['end_time']){
            $str .= " and `order_time` <= '{$where['end_time']} 59:59:59'";
        }
        if( isset($where['out_order_sn']) && $where['out_order_sn']){
            $str .= " and `out_order_sn` = '{$where['out_order_sn']}'";
        }
        if( isset($where['attach_sn']) && $where['attach_sn']){
            $str .= " and `attach_sn` = '{$where['attach_sn']}'";
        }
        $sql.=$str;
        $rea = $this->payActionT($str);
        $sql .= " ORDER BY `pay_id` DESC";
       // echo $sql;
        //exit;
        $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        $data['T']=$rea;
        return $data;
    }

    /**
     * 	pageList，分页列表，收银提报列表
     *
     * 	@url AppOrderPayActionListController/search
     */
	function getAllList($where) {
		
		$sql = "SELECT `pay_id`, `order_id`, `order_sn`, `order_time`, `order_amount` AS `order_amount`, `deposit` AS `deposit`, `balance` AS `balance`, `attach_sn`, `remark`, `pay_time`, `pay_type`, `order_consignee`, `pay_account`, `pay_sn`, `proof_sn`, `leader`, `leader_check`, `opter_name`, `department`, `status`, `pay_checker`, `pay_check_time`, `system_flg` FROM `" . $this->table() . "` WHERE 1 AND `is_type`=1 ";
		if(isset($where['status']) && $where['status']){
			$sql .= " and `status` = '{$where['status']}'";
		}
        if(isset($where['department']) && $where['department']){
            $sql .= " and `department` in ({$where['department']})";
        }
        if(isset($where['order_sn']) && $where['order_sn']){
            $sql .= " and `order_sn` = '{$where['order_sn']}'";
        }
        if(isset($where['start_time']) && $where['start_time']){
            $sql .= " and `pay_time` >= '{$where['start_time']} 00:00:00'";
        }
        if( isset($where['end_time']) && $where['end_time']){
        	$sql .= " and `pay_time` <= '{$where['end_time']} 59:59:59'";
        }
        if( isset($where['opter_name']) && $where['opter_name']){
        	$sql .= " and `opter_name`  like \"%".addslashes($where['opter_name'])."%\"";
        }
        $sql .= " ORDER BY `pay_id` DESC";
        $_data = $this->db()->getAll($sql);
		return $_data;
	}    

    /**
     * 检查收银提报状态是否正确
     * @param array $ids
     * @return boolean
     */
    function getStatusList($ids){
        $sql = "SELECT `pay_id` FROM `app_order_pay_action` WHERE `status` != 1";
        if(count($ids) < 1){
            return FALSE;
        }
        $sql .= " and `pay_id` in (".implode(',', $ids).")";
        $data = $this->db()->getAll($sql);
        return $data;
    }
    
    /**
     * 删除提报记录
     * @param type $order_id
     */
    function deleteOrderPayInfo($order_id){
        $sql = "DELETE FROM `app_order_pay_action` WHERE `order_id`=$order_id";
        $data = $this->db()->query($sql);
        return $data;
    }
    
    /**
     * 检查会计审核状态是否正确
     * @param array $ids
     * @return boolean
     */
    function getCheckStatus($ids){
        $sql = "SELECT `pay_id` FROM `app_order_pay_action` WHERE `status` != 2";
        if(count($ids) < 1){
            return FALSE;
        }
        $sql .= " and `pay_id` in (".implode(',', $ids).")";
        $data = $this->db()->getAll($sql);
        return $data;
    }
    
    /**
     * 更新状态值
     * @param array $ids
     * @param int $status
     * @return boolean
     */
    function updateListStatus($ids,$status=2){
        if(count($ids) < 0){
            return FALSE;
        }
        if($status == 2){
            $sql = "update `app_order_pay_action` set `status` = $status,`leader` = '{$_SESSION['userName']}',`leader_check` = '".date("Y-m-d H:i:s")."' WHERE `pay_id` in (".implode(',', $ids).")";
        }else if($status == 3){
            $sql = "update `app_order_pay_action` set `status` = $status,`pay_checker` = '{$_SESSION['userName']}',`pay_check_time` = '".date("Y-m-d H:i:s")."' WHERE `pay_id` in (".implode(',', $ids).")";
        }else{
            $sql = "update `app_order_pay_action` set `status` = $status WHERE `pay_id` in (".implode(',', $ids).")";
        }
        $data = $this->db()->query($sql);
        return $data;
    }
    
    
    /**
     * 	pageList，分页列表，会计审核列表
     *
     * 	@url AppOrderPayActionListController/search
     */
	function checkPageList($where, $page, $pageSize = 10, $useCache = true) {
		$sql = "SELECT `pay_id`, `order_id`, `order_sn`, `order_time`, `order_amount`, `deposit`, `balance`, `attach_sn`, `remark`, `pay_time`, `pay_type`, `order_consignee`, `pay_account`, `pay_sn`, `proof_sn`, `leader`, `leader_check`, `opter_name`, `department`, `status`, `pay_checker`, `pay_check_time`, `system_flg` FROM `" . $this->table() . "` WHERE 1";
		if(isset($where['status']) && $where['status']){
			$sql .= " and `status` = '{$where['status']}'";
		}      
        if(isset($where['department']) && $where['department']){
            $sql .= " and `department` in ({$where['department']})";
        }
       
        if(isset($where['order_sn']) && $where['order_sn']){
            $sql .= " and `order_sn` = '{$where['order_sn']}'";
        }
        if(isset($where['start_time']) && $where['start_time'] ){
            $sql .= " and `pay_time` >= '{$where['start_time']} 00:00:00'";
        }
        
        if(isset($where['end_time']) && $where['end_time']){
        	$sql .= " and `pay_time` <= '{$where['end_time']} 59:59:59'";
        }
        $sql .= " ORDER BY `pay_id` DESC";
        $_data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
		return $_data;
	}
    
    /**
     * 统计
     * @param array $where
     * @return type
     */
    function orderTongJi($where,$status){
        $sql = "SELECT count(*) as order_num,sum(deposit) as shishou_price FROM `app_order_pay_action` WHERE ";
        $sql_where = " `pay_type`<>272 ";
        if(isset($where['department']) && $where['department']){
            $sql_where .= " and `department` = '{$where['department']}'";
        }
         if(isset($where['status']) && $where['status']){
            $sql_where .= " and `status` = '{$where['status']}'";
        } 
        if(isset($where['order_sn']) && $where['order_sn']){
            $sql_where .= " and `order_sn` = '{$where['order_sn']}'";
        }
        if(isset($status) && $status){
        	$sql_where .= " and `status` = {$status}";
        }
        if(isset($where['start_time']) && $where['start_time'] && isset($where['end_time']) && $where['end_time']){
        	$sql_where .= " and `pay_time` >= '{$where['start_time']}' and `pay_time` <= '{$where['end_time']}'";
        }
        $sql .= $sql_where;
    // echo $sql;
        //统计收款单总数 和 实收金额
        $data = $this->db()->getRow($sql);
        return $data;
    }
    /**
     * 统计 已提报 未提报 已审核数量
     * @param array $where
     * @return type
     */
    function orderTongJi_status($where,$status){
    	$sql = "SELECT count(*) FROM `app_order_pay_action` WHERE ";
    	$sql_where = " 1 ";
    	if(isset($where['department']) && $where['department']){
    		$sql_where .= " and `department` = '{$where['department']}'";
    	}
    	/*  if(isset($where['status']) && $where['status']){
    	 $sql_where .= " and `status` = '{$where['status']}'";
    	} */
    	if(isset($where['order_sn']) && $where['order_sn']){
    		$sql_where .= " and `order_sn` = '{$where['order_sn']}'";
    	}
    	if(isset($status) && $status){
    		$sql_where .= " and `status` = {$status}";
    	}
    	if(isset($where['start_time']) && $where['start_time'] && isset($where['end_time']) && $where['end_time']){
    		$sql_where .= " and `pay_time` >= '{$where['start_time']}' and `pay_time` <= '{$where['end_time']}'";
    	}
    	$sql .= $sql_where;
    	// echo $sql;
    	//统计收款单总数 和 实收金额
    	$data = $this->db()->getRow($sql);
    	return $data;
    }
    
    
    //通过会员id查询会员信息
    function GetMemberByMember_id($member_id){
        if(empty($member_id)){
            return false;
        }
        $keys=array('member_id');
        $vals=array($member_id);

        $ret=ApiModel::bespoke_api($keys,$vals,'GetMemberByMember_id');
        return $ret;
    }
    
    //通过预约id更改成交状态
    function updateBespokeDeal_Status($bespoke_id){

        $keys=array('bespoke_id');
        $vals=array($bespoke_id);

        $ret=ApiModel::bespoke_api($keys,$vals,'updateBespokeDealStatus');
        return $ret;
    }

    /**
     *  取出指定时间数组
     */
    public function get_data_arr($start_time,$end_time){
		$start_time_str=explode("-",$start_time);
		$end_time_str=explode("-",$end_time);
		$data_arr=array();
		while(true){
			if($start_time_str[0].$start_time_str[1].$start_time_str[2]>$end_time_str[0].$end_time_str[1].$end_time_str[2]) break;
			$data_arr[$start_time_str[0]."-".$start_time_str[1]."-".$start_time_str[2]]=$start_time_str[1]."-".$start_time_str[2];
			$start_time_str[2]++;
			$start_time_str=explode("-",date("Y-m-d",mktime(0,0,0,$start_time_str[1],$start_time_str[2],$start_time_str[0])));
		}
		return $data_arr;
	}

    public function payActionT($where){
        $sql= "SELECT COUNT(order_sn) FROM ".$this->table()." where 1".$where." group by order_sn";
        $arr['Corder_sn'] = count($this->db()->getAll($sql));
        $sql = "SELECT COUNT(pay_id) as id  FROM ".$this->table()." where 1".$where;
        $rea = $this->db()->getOne($sql);
        $sql = "SELECT sum(deposit) as deposit  FROM ".$this->table()." where 1".$where;
        $res = $this->db()->getOne($sql);
        $arr['Cid']=$rea;
        $arr['Cdep']=$res;
        return $arr;
    }

    public function addPayLog($arr){
        if(empty($arr)){
            return false;
        }
        //事物逻辑
        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            if(isset($arr['Payaction'])){
                $f = array_keys($arr['Payaction']);
                $v = array_values($arr['Payaction']);
                $f = implode('`,`',$f);
                $v = implode("','",$v);
                $sql = "insert into app_order_pay_action (`$f`) VALUE ('$v')";
                $pdo->query($sql);
            }

            if(isset($arr['AppReceiptPay'])){
                $f = array_keys($arr['AppReceiptPay']);
                $v = array_values($arr['AppReceiptPay']);
                $f = implode('`,`',$f);
                $v = implode("','",$v);
                $sql = "insert into app_receipt_pay (`$f`) VALUE ('$v')";
                $pdo->query($sql);
                $appreceiptpayid = $pdo->lastInsertId();
            }

            if(isset($arr['AppReceiptPayLog'])){
                $arr['AppReceiptPayLog']['receipt_id']=$appreceiptpayid;
                $f = array_keys($arr['AppReceiptPayLog']);
                $v = array_values($arr['AppReceiptPayLog']);
                $f = implode('`,`',$f);
                $v = implode("','",$v);
                $sql = "insert into app_receipt_pay_log (`$f`) VALUE ('$v')";
                $pdo->query($sql);
            }

        } catch(Exception $e){//捕获异常
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            $error = var_export($e,true);
            file_put_contents('log.batchM.txt',$error,FILE_APPEND);
            return false;
        }
        //如果没有异常，就提交事务
        $pdo->commit();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return true;


    }

    public function create_receipt($shop_short="DYC",$str='DJ'){
        //$shop_short=$_SESSION['warehouse'];
//        $shop_short='DYC';
        $date = date("Ymd");
        $header=$str.'-'.$shop_short.'-'.$date;

        $receipt_id = rand(0,999);
        $nes = str_pad($receipt_id,4,'0',STR_PAD_LEFT);
        $bonus_code=$header.$nes;
        return $bonus_code;
    }
	
	
	/*
	author: liulinyan
	date:2015-08-15
	used:根据订单编号拿取订单最新的信息
	*/
	public function getpayinfo($order_sn)
	{
		$sql = "select order_amount,deposit,balance from app_order_pay_action where order_sn =$order_sn order by pay_id desc limit 1";
		$data = $this->db()->getRow($sql);
    	return $data;
	}

}

?>