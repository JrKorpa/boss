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
class AppOrderPayActionModel extends Model {

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
        
        $innerjoin = "";
        if(SYS_SCOPE == 'zhanting'){
            $innerjoin = " inner join app_order.base_order_info oi on oi.id = pa.order_id ";
        }
        
        $sql = "SELECT pa.* FROM `" . $this->table() . "` pa {$innerjoin} WHERE 1 ";
        $str='';
        if(isset($where['order_sn']) && $where['order_sn']){
            $str .= " and pa.`order_sn` = '{$where['order_sn']}'";
        }
        if(isset($where['department']) && $where['department']){
            $str .= " and pa.`department` = {$where['department']}";
        }
        if(isset($where['pay_type']) && $where['pay_type']){
            $str .= " and pa.`pay_type` = '{$where['pay_type']}'";
        }
        if(isset($where['start_time_p']) && $where['start_time_p']){
            $str .= " and pa.`pay_time` >= '{$where['start_time_p']} 00:00:00'";
        }
        if( isset($where['end_time_p']) && $where['end_time_p']){
            $str .= " and pa.`pay_time` <= '{$where['end_time_p']} 59:59:59'";
        }
        if(isset($where['start_time']) && $where['start_time']){
            $str .= " and pa.`order_time` >= '{$where['start_time']} 00:00:00'";
        }
        if( isset($where['end_time']) && $where['end_time']){
            $str .= " and pa.`order_time` <= '{$where['end_time']} 59:59:59'";
        }
        if( isset($where['out_order_sn']) && $where['out_order_sn']){
            $str .= " and pa.`out_order_sn` = '{$where['out_order_sn']}'";
        }
        if( isset($where['attach_sn']) && $where['attach_sn']){
            $str .= " and pa.`attach_sn` = '{$where['attach_sn']}'";
        }
        //zt隐藏
        if(SYS_SCOPE == 'zhanting')
        {
            $str .= " and oi.`hidden` <> 1";
        }
        $sql.=$str;
        $rea = $this->payActionT($str);
        $sql .= " ORDER BY pa.`pay_id` DESC";
        //echo $sql;
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

		$innerjoin = "";
        if(SYS_SCOPE == 'zhanting'){
            $innerjoin = " inner join app_order.base_order_info oi on oi.id = pa.order_id ";
        }

		$sql = "SELECT pa.`pay_id`, pa.`order_id`, pa.`order_sn`, pa.`order_time`, pa.`order_amount` AS `order_amount`, pa.`deposit` AS `deposit`, pa.`balance` AS `balance`, pa.`attach_sn`, pa.`remark`, pa.`pay_time`, pa.`pay_type`, pa.`order_consignee`, pa.`pay_account`, pa.`pay_sn`, pa.`proof_sn`, pa.`leader`, pa.`leader_check`, pa.`opter_name`, pa.`department`, pa.`status`, pa.`pay_checker`, pa.`pay_check_time`, pa.`system_flg` FROM `" . $this->table() . "` pa {$innerjoin} WHERE 1 AND pa.`is_type`=1 ";
		if(isset($where['status']) && $where['status']){
			$sql .= " and pa.`status` = '{$where['status']}'";
		}
        if(isset($where['department']) && $where['department']){
            $sql .= " and pa.`department` in ({$where['department']})";
        }
        if(isset($where['order_sn']) && $where['order_sn']){
            $sql .= " and pa.`order_sn` = '{$where['order_sn']}'";
        }
        if(isset($where['start_time']) && $where['start_time']){
            $sql .= " and pa.`pay_time` >= '{$where['start_time']} 00:00:00'";
        }
        if( isset($where['end_time']) && $where['end_time']){
        	$sql .= " and pa.`pay_time` <= '{$where['end_time']} 59:59:59'";
        }
        if( isset($where['opter_name']) && $where['opter_name']){
        	$sql .= " and pa.`opter_name`  like \"%".addslashes($where['opter_name'])."%\"";
        }
        //zt隐藏
        if(SYS_SCOPE == 'zhanting')
        {
            $sql .= " and oi.`hidden` <> 1";
        }
        $sql .= " ORDER BY pa.`pay_id` DESC";
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
        $sql= "SELECT COUNT(pa.order_sn) FROM ".$this->table()." pa inner join app_order.base_order_info oi on oi.id = pa.order_id where 1".$where." group by pa.order_sn";
        $arr['Corder_sn'] = count($this->db()->getAll($sql));
        $sql = "SELECT COUNT(pa.pay_id) as id  FROM ".$this->table()." pa inner join app_order.base_order_info oi on oi.id = pa.order_id where 1".$where;
        $rea = $this->db()->getOne($sql);
        $sql = "SELECT sum(pa.deposit) as deposit  FROM ".$this->table()." pa inner join app_order.base_order_info oi on oi.id = pa.order_id where 1".$where;
        $res = $this->db()->getOne($sql);
        $arr['Cid']=$rea;
        $arr['Cdep']=$res;
        return $arr;
    }

    public function addPayLog($arr){

        if(empty($arr)){
            return array('error'=>1,'msg'=>'addPayLog方法参数为空');
        }
        //事物逻辑
        $pdo = $this->db()->db();//pdo对象
        try{
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
            return array('error'=>0,'msg'=>'操作成功');
        } catch(Exception $e){//捕获异常
            return array('error'=>1,'msg'=>'操作失败:SQL'.$sql);
        }

    }

    public function create_receipt($shop_short="DYC",$str='DJ'){
        //$shop_short=$_SESSION['warehouse'];
//        $shop_short='DYC';
        $date = date("Ymd");
        $header=$str.'-'.$shop_short.'-'.$date;

        $receipt_id = rand(0,999999);
        $nes = str_pad($receipt_id,6,'0',STR_PAD_LEFT);
        $bonus_code=$header.$nes;
        return $bonus_code;
    }
	
	
	
    //裸钻
    function getRowByGoodSn($goods_sn) {
        $keys=array('goods_sn');
        $vals=array($goods_sn);
        $ret=ApiModel::diamond_api($keys,$vals,'GetDiamondByGoods_sn');
        return $ret; 
    }
	
	//彩钻
    function getRowByGoodSnOrCertId($id) {
        $keys=array('id');
        $vals=array($id);
        $ret=ApiModel::diamond_api($keys,$vals,'GetColorDiamondByiId');
        return $ret; 
    }

    //获取点款已使用过的转单流水记录
    function getZhuandan_sn_deposit($zhuandan_sn){
        $sql = "select sum(deposit) as deposit_sum from app_order_pay_action where zhuandan_sn='{$zhuandan_sn}'";
        return $this->db()->getOne($sql);
    }

    /**
     * 判断
     * 1 没有点款记录
     * 2 点款赠品
     * 3 不是点款赠品
     */
    function isTouchGifts($order_id){
        $sql = "SELECT * FROM ".$this->table()." WHERE  order_id='{$order_id}'";
        $data = $this->db()->getRow($sql);
        if(count($data) == 0){
            return 1;
        }
        foreach($data as $key => $value){
            if($value['pay_type'] == 224){
                return 2;
            }
        }
        return 3;
    }


    function updatePayType($id,$pay_type){

        $sql = "select order_sn,status,deposit,pay_time from app_order_pay_action where pay_id={$id}";
        $row = $this->db()->getRow($sql);
        if(empty($row) || $row['status'] != 1){
            return false;
        }
        $sql = "update app_order_pay_action set pay_type = {$pay_type} WHERE  pay_id={$id}";
        $res = $this->db()->query($sql);
        if($res){
            $pay_time = date('Y-m-d',strtotime($row['pay_time']));
            $sql = "update app_receipt_pay set pay_type = {$pay_type} WHERE  order_sn={$row['order_sn']} and pay_fee = {$row['deposit']} and pay_time = '{$pay_time}'";
            $res = $this->db()->query($sql);
        }
        return $res;
    }

}

?>