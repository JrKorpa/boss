<?php
/**
 *  -------------------------------------------------
 *   @file		: AppbespokeinfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 16:51:42
 *   @update	:
 *  -------------------------------------------------
 */
class AppBespokeInfoModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_bespoke_info';
		$this->_prefix ='bespoke';
        $this->_dataObject = array(
"bespoke_id"=>"预约ID",
 "bespoke_sn"=>"预约号",
"department_id"=>"体验店ID",
"mem_id"=>"顾客id",
"customer_source_id"=>"客户来源",
"customer"=>"客户姓名",
"customer_mobile"=>"客户手机",
"customer_email"=>"客户email",
"customer_address"=>"客户地址",
"create_time"=>"预约时间",
"bespoke_inshop_time"=>"预约到店时间",
"real_inshop_time"=>"实际到店时间",
"make_order"=>"制单人",
"accecipt_man"=>"接待人",
"queue_status"=>"预约队列，0首先，1其次，2最后",
"salesstage"=>"销售阶段:[1,了解;2,对比;3,决定]",
"brandimage"=>"品牌印象[1,弱;2,中;3,强]",
"bespoke_status"=>"预约状态 1保存2已经审核3作废",
"re_status"=>"到店状态 1到店 2未到店",
"withuserdo"=>"回访状态 0未回访 1已回访",
"is_delete"=>"取消预约 0未取消 1已取消",
"remark"=>"预约备注");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表 
	 *
	 *	@url MessageController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 = 1 ";
        if(isset($where['bespoke_sn'])&&!empty($where['bespoke_sn'])){
            $sql.=" AND `bespoke_sn` = '".addslashes($where['bespoke_sn'])."'";
        }
        if(isset($where['customer'])&&!empty($where['customer'])){
            $sql.=" AND `customer` like '".addslashes($where['customer'])."%'";
        }
        if (isset($where['todo']) && $where['todo']==1) {
            $is_leader = intval($where['is_dz']);
            $sql.=" and (accecipt_man='{$_SESSION['userName']}' or (accecipt_man='' and {$is_leader}))";
            if (!empty($where['department_id'])) {
                $sql.=" AND `department_id` in (".$where['department_id'].")";
            }
        } else {
            // 客服只能看自己创建或接待的预约
            if (!empty($where['kefu_name'])) {
                $sql.=" AND (`accecipt_man` like '".addslashes($where['kefu_name'])."%' OR `make_order` like '".addslashes($where['kefu_name'])."%')";
            } else {
                /*if(!empty($where['make_order'])){
                    $sql.=" AND `make_order` like '".addslashes($where['make_order'])."%'";
                }*/
                if(!empty($where['make_order'])){//boss_1212
                    $str_n = " AND (";
                    foreach ($where['make_order'] as $make_order) {
                        $str_n.= "`make_order` like '".addslashes($make_order)."%' or ";
                    }
                    $sql .= rtrim($str_n, "or ").") ";
                }
                if(isset($where['accecipt_man'])&&!empty($where['accecipt_man'])){
                    $sql.=" AND `accecipt_man` like '".addslashes($where['accecipt_man'])."%'";
                }
                if(isset($where['fenpei_sn'])&&$where['fenpei_sn']==1){
                    $sql.=" AND (`accecipt_man` != '' OR `accecipt_man` IS NOT NULL) ";
                }elseif(isset($where['fenpei_sn'])&&$where['fenpei_sn']==2){
                    $sql.=" AND (`accecipt_man` = '' OR `accecipt_man` IS NULL) ";
                }
            }
            // 门店搜索
            if(!empty($where['department_id'])){
                $sql.=" AND `department_id` in (".$where['department_id'].")";
            }
        }

        if(!empty($where['customer_source_id'])){
            $sql.=" AND `customer_source_id` in ('".$where['customer_source_id']."')";
        }
        if(!empty($where['customer_mobile'])){
        	if (strlen(trim($where['customer_mobile'])) == 11) {
        		$sql.=" AND `customer_mobile` = '".$where['customer_mobile']."'";
        	} else {
            	$sql.=" AND `customer_mobile` like '".$where['customer_mobile']."%'";
        	}
        }
        if(!empty($where['start_time'])){
            $sql.=" AND `create_time` >= '".$where['start_time']." 00:00:00'";
        }
        if(!empty($where['end_time'])){
            $sql.=" AND `create_time` <= '".$where['end_time']." 23:59:59'";
        }
        if(!empty($where['real_inshop_time_start'])){
            $sql.=" AND `real_inshop_time` >= '".$where['real_inshop_time_start']." 00:00:00'";
        }
        if(!empty($where['real_inshop_time_end'])){
            $sql.=" AND `real_inshop_time` <= '".$where['real_inshop_time_end']." 23:59:59'";
        }
        if(!empty($where['bespoke_inshop_time_start'])){
            $sql.=" AND `bespoke_inshop_time` >= '".$where['bespoke_inshop_time_start']."'";
        }
        if(!empty($where['bespoke_inshop_time_end'])){
            $sql.=" AND `bespoke_inshop_time` <= '".$where['bespoke_inshop_time_end']."'";
        }
        if(!empty($where['recommender_sn'])){
            $sql.=" AND `recommender_sn` = '".$where['recommender_sn']."'";
        }
        if(!empty($where['re_status'])){
            $sql.=" AND `re_status` = ".$where['re_status'];
        }

        if(!empty($where['deal_status'])){
            $sql.=" AND `deal_status` = ".$where['deal_status'];
        }
        if(!empty($where['queue_status'])){
            $sql.=" AND `queue_status` = ".$where['queue_status'];
        }
		if(isset($where['hf_status'])&&($where['hf_status'])!=null){
            $sql.=" AND `withuserdo` = ".$where['hf_status'];
        }
        if(!empty($where['bespoke_status'])){
            if (is_array($where['bespoke_status'])) {
                $str_statuses = implode(',', $where['bespoke_status']);
                $sql.=" AND `bespoke_status` in ({$str_statuses})";
            } else {
                $sql.=" AND `bespoke_status`=". $where['bespoke_status'];
            }
        }
            
		$sql .= " ORDER BY `bespoke_id` DESC";
        //echo $sql;die;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
    
    //接收到店人数
    public function get_bespoke_list_by_accecipt_man_and_queue_status_count($accecipt_man='',$queue_status=1){
		$sql='select count(bespoke_id) from `'.$this->table().'` where accecipt_man = "'.$accecipt_man.'" and queue_status = "'.$queue_status.'"';
        return $this->_db->getOne($sql);
    }

    //通过顾问取预约
    public function get_bespoke_list_by_accecipt_man($accecipt_man='',$queue_status=''){
        if($accecipt_man==''){
            $accecipt_man=$_SESSION['userName'];
        }
        if($queue_status==''){
            $queue_status=2;
        }
		if (strpos($queue_status, ',') === false) {
			$sql='select * from `'.$this->table().'` where re_status=1 and `accecipt_man` = "'.$accecipt_man.'" and `queue_status` = '.$queue_status.' order by bespoke_id desc';
		} else {
			$sql='select * from `'.$this->table().'` where re_status=1 and `accecipt_man` = "'.$accecipt_man.'" and `queue_status` in('.$queue_status.') order by bespoke_id desc';
		}
        return $this->_db->getAll($sql);
    }

    /**
     * 生成预约号
     */
    public function create_besp_sn(){
        return date('ym').str_pad(mt_rand(1,99999),5,'0',STR_PAD_LEFT);
    }

    /**
     * 查询预约号是否存在 
     */
    public function get_bespoke_by_besp_sn($besp_sn,$select='*'){
        $sql="select ".$select." from `".$this->table()."` where `bespoke_sn` = '".$besp_sn."'";
        return $this->_db->getRow($sql);
    }
    
    
    /**
     * 获取所有分配接待人的数据
     * @param type $ids
     * @return type
     */
    public function getInfoByIds($ids) {
        $sql = "SELECT `bespoke_status`,`department_id`,`queue_status`,`bespoke_id`,`re_status` FROM `{$this->table()}` WHERE `bespoke_id` IN ($ids)";
        return $this->db()->getAll($sql);
    }

    /**
     * 查询微信端 到店抽奖码是否存在
     */
    public function get_bespoke_by_re_lot_code($re_lot_code,$select='*'){
        $sql="select ".$select." from `".$this->table()."` where `re_lot_code` = '".$re_lot_code."'";
        return $this->_db->getRow($sql);
    }

    /**
     * 查询重复预约，根据同一手机号 一周内 未到店的 不能重复
     */
    public function get_repeat_bespoke($mobile, $bespoke_inshop_time, $select='*'){
        $serven_days = 7*24*3600;
        $start_date = date('Y-m-d', strtotime($bespoke_inshop_time)-$serven_days);
        $end_date = date('Y-m-d', strtotime($bespoke_inshop_time)+$serven_days);
        $sql="select {$select} from `".$this->table()."` where `customer_mobile` = '{$mobile}' and `bespoke_inshop_time`>='{$start_date}'
        and `bespoke_inshop_time`<='{$end_date}' and bespoke_status<>3 and re_status=2 order by re_status";
        return $this->_db->getRow($sql);
    }


    /**
     * 查询重复预约，根据同一手机号 同一渠道 接待状态(未服务/待服务/服务中) 不能重复
     */
    public function get_repeat_bespoke2($mobile, $channel_id, $select='*'){
        $sql="select {$select} from `".$this->table()."` where `customer_mobile` = '{$mobile}' and department_id='{$channel_id}' and bespoke_status<>3 and queue_status in (1,2,3)";
        return $this->_db->getRow($sql);
    }

    
    public function pauseBespoke($bespoke_sn) {
    	$sql = "update app_bespoke_info set queue_status = 2 where bespoke_sn ='{$bespoke_sn}'";
    	return $this->db()->query($sql);
    }

}

?>