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
class ShopcountInfoModel extends Model
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
        if(isset($where['accecipt_man'])&&!empty($where['accecipt_man'])){
            $sql.=" AND (`accecipt_man` like '".addslashes($where['accecipt_man'])."%')";
        }
        if(!empty($where['department_id']) || $where['is_b']){
            if($where['is_b']){
                $sql.=" AND (`department_id` in (".$where['department_id'].") OR `accecipt_man` like '".addslashes($_SESSION['userName'])."%' OR `make_order` like '".addslashes($_SESSION['userName'])."%')";
            }else{
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
        if(!empty($where['make_order'])){
            $sql.=" AND `make_order` like '".addslashes($where['make_order'])."%'";
        }
        if(isset($where['fenpei_sn'])&&$where['fenpei_sn']==1){
            $sql.=" AND (`accecipt_man` != '' OR `accecipt_man` IS NOT NULL) ";
        }elseif(isset($where['fenpei_sn'])&&$where['fenpei_sn']==2){
            $sql.=" AND (`accecipt_man` = '' OR `accecipt_man` IS NULL) ";
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
            $sql.=" AND `bespoke_status`=". $where['bespoke_status'];
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
    public function get_bespoke_list_by_make_order($make_order='',$queue_status=''){
        if($make_order==''){
            $make_order=$_SESSION['userName'];
        }
        if($queue_status==''){
            $queue_status=2;
        }
        $sql='select * from `'.$this->table().'` where (accecipt_man = "'.$make_order.'" or`make_order` = "'.$make_order.'") and `queue_status` in("'.$queue_status.'")';
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
	
	
	//分开来处理
	//拿取体验店的全部预约
	//默认为拿取全部预约数
	public function getinfocount($where,$cloumn=1,$all='')
	{
		$sql = "select count(*) as count from app_bespoke_info where ";
		if($cloumn==1)
		{
			$sql .= " bespoke_status = 2 and  "; //预约状态已审核
		}elseif($cloumn==2 && empty($all))
		{
			$sql .= " re_status = 1 and ";       //到店状态已到店
		}
		
		$str = $this->combinesql($where,$cloumn);
		
		$sql .= $str .' 1 order by bespoke_id asc';
		$data = $this->_db->getRow($sql);
		return $data['count'];
	}
	
	
	public function combinesql($where,$cloumn=1)
	{
		//这里组装的时间过滤点以下面来区分
		/*
		全部预约，预约到店率，预约成交率：添加预约单时间  1
		实际到店数：预约单到店时间      2
		当前应到数：预约单到店时间      2
		*/
		if($cloumn==2)
		{
			$needtime = 'real_inshop_time';
			//$needtime = 'bespoke_inshop_time';  //预约店时间
		}else{
			$needtime = 'create_time';	
		}
		
		
		
		$str = '';
		//特殊处理是否已经删除
		$isdelete = isset($where['is_delete']) ? $where['is_delete'] : '';
		if($isdelete >=0)
		{
			$str .= " is_delete = $isdelete and " ;
			unset($where['is_delete']);
		}
		//过滤没有任何值的
		$array = array_filter($where);
		//特殊处理时间
		$begin_time = isset($array['begintime']) ? $array['begintime'] : '';
		$end_time = isset($array['endtime']) ? $array['endtime'] : '';

		if(!empty($begin_time))
		{
	            if($cloumn == 2){
			    $str .=" $needtime >= '".$begin_time."' and ";
        	    }else{
        	            $str .=" $needtime >= '".$begin_time." 00:00:00' and ";
        	    }
			unset($array['begintime']);
		}
		if(!empty($end_time))
		{
        	    if($column == 2){
			    $str .=" $needtime <= '".$end_time."' and ";
          	    }else{
                	    $str .=" $needtime <= '".$end_time." 23:59:59' and ";
	            }
			unset($array['endtime']);
		}


		
		//开始拼接sql
		if(!empty($array))
		{
			foreach($array as $k=>$v)
			{
				$v =  is_numeric($v) ? $v : "'".$v."'";
				$str .= $k.'='.$v.' and ';
			}
		}
		return $str;
		
	}
	
	
	
	//为了绩效统计的
	public function pageListreport($where,$page,$pageSize=10,$useCache=false)
	{
		$sql = 'select * from app_bespoke_info where ';
		$str = '';
		//特殊处理是否已经删除
		$isdelete = isset($where['is_delete']) ? $where['is_delete'] : '';
		if($isdelete >=0)
		{
			$sql .= " is_delete = $isdelete and " ;
			unset($where['is_delete']);
		}
		//过滤没有任何值的
		$array = array_filter($where);
		//特殊处理时间
		$begin_time = isset($array['begintime']) ? $array['begintime'] : '';
		$end_time = isset($array['endtime']) ? $array['endtime'] : '';
		if(!empty($begin_time))
		{
			$str .=" create_time > '".$begin_time."' and ";
			unset($array['begintime']);
		}
		if(!empty($end_time))
		{
			$str .=" create_time < '".$end_time."' and ";
			unset($array['endtime']);
		}
		
		//开始拼接sql
		if(!empty($array))
		{
			foreach($array as $k=>$v)
			{
				$v =  is_numeric($v) ? $v : "'".$v."'";
				$str .= $k.'='.$v.' and ';
			}
		}
		$sql .=$str .' 1 order by bespoke_id asc';
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	
	
    
}

?>
