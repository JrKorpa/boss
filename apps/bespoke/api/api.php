<?php
/**
 * This contains the Retrieval API .
 *
 */
class api
{
    private  $db = null;
    private  $error_msg = '';
    private  $return_msg = '';
    private  $return_sql = '';
    private  $filter = array();
    public function __construct($_filter)
    {
        global $config;
        $this->db= new KELA_API_DB($config);
		$this->filter = $_filter;
    }

    /**
     * 查询预约列表分页信息
     * @param order_sn order_pay_status
     * @return json
     */
	public function GetBespokeList()
	{
		$s_time = microtime();
		//$this -> filter["page"] = 3;  //当前页
		$page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
		$page_size = intval($this -> filter["page_size"]) > 0 ? intval($this -> filter["page_size"]) : 15;

        $bespoke_id			=intval(trim($this->filter['bespoke_id']));//预约id
		$department_id			=intval(trim($this->filter['department_id']));//预约体验店
		$customer_mobile	=intval(trim($this->filter['customer_mobile']));//手机
		$customer=trim($this->filter['customer']);//客户姓名
		$customer_email=trim($this->filter['customer_email']);//客户email
		$start_create_time=trim($this->filter['start_create_time']);//预约开始时间
		$end_create_time=trim($this->filter['end_create_time']);//预约结束时间
		$start_bespoke_inshop_time=trim($this->filter['start_bespoke_inshop_time']);//预约到店开始时间
		$end_bespoke_inshop_time=trim($this->filter['end_bespoke_inshop_time']);//预约到店结束时间
		$start_real_inshop_time=trim($this->filter['start_real_inshop_time']);//实际到店开始时间
		$end_real_inshop_time=trim($this->filter['end_real_inshop_time']);//实际到店结束时间
		$make_order=trim($this->filter['make_order']);//制单人
		$bespoke_status=intval(trim($this->filter['bespoke_status']));//预约状态
		$re_status=intval(trim($this->filter['re_status']));//到店状态
		$is_delete=intval(trim($this->filter['is_delete']));//取消预约
        $remark=trim($this->filter['remark']);//取消预约

		$where = " where  1 ";
        if(!empty($remark))
        {
            $where .= " and `remark` = '".$remark."'";
        }
		if(!empty($bespoke_id))
		{
			$where .= " and `bespoke_id` = " . $bespoke_id;
        }
		if(!empty($department_id))
		{
			$where .= " and `department_id`=".$department_id;
		}
		if(!empty($customer_mobile))
		{
			$where .= " and `customer_mobile`=".$customer_mobile;
		}
		if(!empty($customer))
		{
			$where .= " and `customer`='".$customer."'";
		}
		if(!empty($customer_email))
		{
			$where .= " and `customer_email`='".$customer_email."'";
		}
		if(!empty($start_create_time))
		{
			$where .= " and `create_time`>='".$start_create_time."'";
		}
		if(!empty($end_create_time))
		{
			$where .= " and `create_time`<='".$end_create_time."'";
		}
		if(!empty($start_bespoke_inshop_time))
		{
			$where .= " and `bespoke_inshop_time`>='".$start_bespoke_inshop_time."'";
		}
		if(!empty($end_bespoke_inshop_time))
		{
			$where .= " and `bespoke_inshop_time`<='".$end_bespoke_inshop_time."'";
		}
		if(!empty($start_real_inshop_time))
		{
			$where .= " and `real_inshop_time`>='".$start_real_inshop_time."'";
		}
		if(!empty($end_real_inshop_time))
		{
			$where .= " and `real_inshop_time`<='".$end_real_inshop_time."'";
		}
		if(!empty($make_order))
		{
			$where .= " and `make_order`='".$make_order."'";
		}
		if(!empty($bespoke_status))
		{
			$where .= " and `bespoke_status` in (".$bespoke_status.")";
		}
		if(!empty($re_status))
		{
			$where .= " and `re_status` in (".$re_status.")";
		}
		if(!empty($is_delete))
		{
			$where .= " and `is_delete` in (".$is_delete.")";
		}
		$sql   = "SELECT COUNT(*) FROM `app_bespoke_info` ".$where;
		$record_count   =  $this -> db ->getOne($sql);
		$page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

		$sql = "select * from `app_bespoke_info` ".$where." ORDER BY id desc LIMIT " . ($page - 1) * $page_size . ",$page_size";
		$res = $this -> db -> getAll($sql);
		$content = array("page" => $page, "page_size" => $page_size, "record_count" => $record_count, "data" => $res, "sql" => $sql);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
	//	var_dump($content);
		if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此预约";
			$this -> return_msg = array();
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $content;
			$this->display();
		}
	}

	/**
    * 通过预约号查询预约信息
    * @param $bespoke_id
    * @return json
    */
	public function GetBespokeByBespoke_id()
	{
		$s_time = microtime();
        $where='';
        $bespoke_id=intval(trim($this->filter['bespoke_id']));
		//$order_id = 49;
		if(!empty($bespoke_id))
		{
			$where .= " `bespoke_id` = " . $bespoke_id;
        }else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "预约号不能为空";
			$this -> return_msg = array();
			$this->display();
		}

        if(!empty($where)){
            //查询商品详情
            $sql = "select `bespoke_id` from `app_bespoke_info` " .
                   "where ".$where." ;";
            $row = $this->db->getRow($sql);
        }else{
            $row=false;
        }

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此预约";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}
    //获取
    //会员id
    //会员的所有地址信息
    public function  GetMemberAddressInfo(){
        $s_time = microtime();
        $where='';
        $member_id=intval(trim($this->filter['member_id']));
        $mem_address_id=isset($this->filter['mem_address_id']) ? intval(trim($this->filter['mem_address_id'])) : 0 ;

        if(!empty($member_id))
        {
            $where .= " `member_id` = " . $member_id;
        }elseif($mem_address_id){
            $where .= " `mem_address_id` = " . $mem_address_id;
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "会员id或会员地址id不能为空";
            $this -> return_msg = array();
            $this->display();
        }

        if(isset($this->filter['mem_is_def'])){
            $where .= " AND `mem_is_def` = " . $this->filter['mem_is_def'];
        }

        if(!empty($where)){
            $sql = "SELECT * FROM `app_member_address` " .
                "WHERE ".$where;
            $row = $this->db->getAll($sql);
        }else{
            $row=false;
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$row){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到此会员地址信息";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $row;
            $this->display();
        }
    }

    public function  AddMemberAddressInfo(){
        $s_time = microtime();
        $member_id=intval(trim($this->filter['member_id']));
        if(empty($member_id))
        {
            $this -> error = 3;
            $this -> return_sql = '';
            $this -> error_msg = "会员id为空不能添加该地址！";
            $this -> return_msg = array();
            $this->display();
        }
        $mobile=trim($this->filter['mobile']);
        $mem_country_id=trim($this->filter['mem_country_id']);
        $mem_province_id=trim($this->filter['mem_province_id']);
        $mem_city_id=trim($this->filter['mem_city_id']);
        $mem_district_id=intval(trim($this->filter['mem_district_id']));
        $mem_address=trim($this->filter['mem_address']);
        $customer = trim($this->filter['customer']);
        $mem_is_def = trim($this->filter['mem_is_def']);

//        if($mem_is_def==1){
//            $sql = "UPDATE `app_member_address` SET `mem_is_def`=0 WHERE `member_id`=$member_id";
//            $this->db->query($sql);
//        }
            $sql = "INSERT into `app_member_address` (`member_id`,`customer`,`mobile`,`mem_country_id`,`mem_province_id`,`mem_city_id`,`mem_district_id`,`mem_address`,`mem_is_def`) VALUES ('$member_id','$customer','$mobile','$mem_country_id','$mem_province_id','$mem_city_id','$mem_district_id','$mem_address','$mem_is_def')";
            $row = $this->db->query($sql);


        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$row){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "插入失败";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $row;
            $this->display();
        }
    }
    //根据默认地址的选择更改用户的地址是否是默认
    //根据mem_address_id更改默认地址的值
    public function UpdateMemberDefAddressStatus(){
    	$s_time = microtime();
        $where='';
        $mem_address_id = intval(trim($this->filter['mem_address_id']));
       // $member_id      = intval(trim($this->filter['member_id']));
        if(!empty($mem_address_id))
        {
            $where .= " `mem_address_id` = " . $mem_address_id;
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "地址id不能为空不能更改！";
            $this -> return_msg = array();
            $this->display();
        }
        //清空原来的默认地址状态
        $sql = "SELECT member_id from `app_member_address` where mem_address_id='".$mem_address_id."'";
        $info = $this->db->getRow($sql);
        $sql2 = "UPDATE `app_member_address` set `mem_is_def`=0 where member_id='".$info['member_id']."'";
        $this->db->query($sql2);
        if (!empty($where)){  	
        	$sql = "UPDATE `app_member_address` SET `mem_is_def`=1 where ".$where;
        	$row = $this->db->query($sql);
        }else {
        	$row = false;
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$row){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "没有这条地址信息";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $row;
            $this->display();
        }
    }

    //修改
    //用户的使用的地址
    // mem_address_id
    public function  PutMemberAddressInfo(){
        $s_time = microtime();
        $where='';
        $mem_address_id=intval(trim($this->filter['mem_address_id']));
        if(!empty($mem_address_id))
        {
            $where .= " `mem_address_id` = " . $mem_address_id;
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "地址id不能为空不能更改！";
            $this -> return_msg = array();
            $this->display();
        }
        $customer = trim($this->filter['customer']);
        $mobile=trim($this->filter['mobile']);
        $mem_country_id=trim($this->filter['mem_country_id']);
        $mem_province_id=trim($this->filter['mem_province_id']);
        $mem_city_id=trim($this->filter['mem_city_id']);
        $mem_district_id=intval(trim($this->filter['mem_district_id']));
        $mem_address=trim($this->filter['mem_address']);
        $mem_is_def = trim($this->filter['mem_is_def']);



        if(!empty($where)){
            //如果设置了默认地址 要进行的操作
            if($mem_is_def==1){
                $sql = "SELECT `member_id` FROM `app_member_address` WHERE `mem_address_id`='".$mem_address_id."'";
                $info = $this->db->getRow($sql);
                $sql2 = "UPDATE `app_member_address` set `mem_is_def`=0 WHERE `member_id`='".$info['member_id']."'";
                $this->db->query($sql2);
            }

            $sql = "UPDATE `app_member_address` SET `customer`='$customer',`mobile`='$mobile',`mem_country_id`='$mem_country_id',`mem_province_id`='$mem_province_id',`mem_city_id`='$mem_city_id',`mem_district_id`='$mem_district_id',`mem_address`='$mem_address',`mem_is_def`='$mem_is_def' ". " WHERE ".$where;
            $row = $this->db->query($sql);
        }else{
            $row=false;
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$row){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "没有这条地址信息";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $row;
            $this->display();
        }
    }

	/**
    * 通过顾客id查询预约信息
    * @param $mem_id
    * @return json
    */
	public function GetBespokeByMem_id()
	{
		$s_time = microtime();
        $where='';
        $mem_id=intval(trim($this->filter['mem_id']));
		//$order_id = 49;
		if(!empty($mem_id))
		{
			$where.=" `mem_id`=".$mem_id." and `bespoke_status` in (2) and `re_status` in(1) and `is_delete` in(0)";
        }else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "用户id不能为空";
			$this -> return_msg = array();
			$this->display();
		}

        if(!empty($where)){
            //查询商品详情
            $sql="select`bespoke_id`,`department_id`,`mem_id`,`customer`,`customer_mobile`,`customer_email`,`customer_address`, `create_time`,`bespoke_inshop_time`,`real_inshop_time`,`make_order`,`accecipt_man`,`bespoke_status`,`re_status`,`remark`,`is_delete` from `app_bespoke_info` "."where ".$where." ;";
            $row = $this->db->getRow($sql);
        }else{
            $row=false;
        }

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此预约";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}

	/**
    * 添加预约信息
    * @param
    * @return json
    */
	public function addBespokeInfo()
	{
		$s_time = microtime();
        $where='';
        $department_id=intval(trim($this->filter['department_id']));
        $customer=trim($this->filter['customer']);
        $customer_mobile=trim($this->filter['customer_mobile']);
        $customer_email=trim($this->filter['customer_email']);
        $customer_address=trim($this->filter['customer_address']);
        $bespoke_inshop_time=trim($this->filter['bespoke_inshop_time']);
        $remark=trim($this->filter['remark']);
        $create_time=date("Y-m-d H:i:s");
        $make_order=trim($this->filter['make_order']);

		if($department_id==''){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "体验店不能为空";
			$this -> return_msg = array();
			$this->display();
		}

		if($customer_mobile==''){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "客户手机不能为空";
			$this -> return_msg = array();
			$this->display();
		}

		if($bespoke_inshop_time==''){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "预约到店时间不能为空";
			$this -> return_msg = array();
			$this->display();
		}

		if($customer==''){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "顾客姓名不能为空";
			$this -> return_msg = array();
			$this->display();
		}

		if($remark==''){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "备注不能为空";
			$this -> return_msg = array();
			$this->display();
		}

		if($customer_email==''){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "顾客email不能为空";
			$this -> return_msg = array();
			$this->display();
		}

		if($customer_address==''){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "顾客地址不能为空";
			$this -> return_msg = array();
			$this->display();
		}

		//插入信息
		$sql = "INSERT INTO `app_bespoke_info` (`department_id`,`create_time`,`customer_mobile`, `bespoke_inshop_time`, `customer`,`remark`,`customer_email`,`customer_address`,`customer_source_id`,`make_order`) VALUES ({$department_id},'{$create_time}','{$customer_mobile}','{$bespoke_inshop_time}'
		,'{$customer}','{$remark}','{$customer_email}','{$customer_address}',{$customer_source_id},'{$make_order}')";
		if($this->db->query($sql)){
			$row = $this->db->insert_id();
		}


		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "添加失败";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}

    /**
     * 查询会员列表分页信息
     * @param
     * @return json
     */
	public function GetMemberList()
	{
		$s_time = microtime();
		//$this -> filter["page"] = 3;  //当前页
		$page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
		$page_size = intval($this -> filter["page_size"]) > 0 ? intval($this -> filter["page_size"]) : 15;

        $member_id			=intval(trim($this->filter['member_id']));//会员id
		$country_id			=intval(trim($this->filter['country_id']));//会员所在国家
		$province_id=intval(trim($this->filter['province_id']));//会员所在省份
		$city_id=intval(trim($this->filter['city_id']));//会员所在城市
		$region_id=intval(trim($this->filter['region_id']));//会员所在区域
		$source_id=intval(trim($this->filter['source_id']));//会员来源
		$member_name=trim($this->filter['member_name']);//会员名称
		$department_id=intval(trim($this->filter['department_id']));//部门
		$mem_card_sn=trim($this->filter['mem_card_sn']);//会员卡号
		$member_phone=intval(trim($this->filter['member_phone']));//电话
		$member_maristatus=trim($this->filter['member_maristatus']);//婚姻状况
		$member_type=intval(trim($this->filter['member_type']));//会员类型

		$where = " where  1 ";
		if(!empty($member_id))
		{
			$where .= " and `member_id` = " . $member_id;
        }
		if(!empty($country_id))
		{
			$where .= " and `country_id`=".$country_id;
		}
		if(!empty($province_id))
		{
			$where .= " and `province_id`=".$province_id;
		}
		if(!empty($city_id))
		{
			$where .= " and `city_id`=".$city_id;
		}
		if(!empty($region_id))
		{
			$where .= " and `region_id`=".$region_id;
		}
		if(!empty($source_id))
		{
			$where .= " and `source_id`=".$source_id;
		}
		if(!empty($member_name))
		{
			$where .= " and `member_name`='".$member_name."'";
		}
		if(!empty($department_id))
		{
			$where .= " and `department_id`>=".$department_id;
		}
		if(!empty($mem_card_sn))
		{
			$where .= " and `mem_card_sn`='".$mem_card_sn."'";
		}
		if(!empty($member_phone))
		{
			$where .= " and `member_phone`>='".$member_phone."'";
		}
		if(!empty($member_maristatus))
		{
			$where .= " and `member_maristatus`='".$member_maristatus."'";
		}
		if(!empty($member_type))
		{
			$where .= " and `member_type`=".$member_type;
		}

		$sql   = "SELECT COUNT(*) FROM `base_member_info` ".$where;
		$record_count   =  $this -> db ->getOne($sql);
		$page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

		$sql = "select * from `base_member_info` ".$where." ORDER BY id desc LIMIT " . ($page - 1) * $page_size . ",$page_size";
		$res = $this -> db -> getAll($sql);
		$content = array("page" => $page, "page_size" => $page_size, "record_count" => $record_count, "data" => $res, "sql" => $sql);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
	//	var_dump($content);
		if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此会员";
			$this -> return_msg = array();
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $content;
			$this->display();
		}
	}

	/**
    * 通过会员手机号查询会员信息
    * @param $member_phone
    * @return json
    */
	public function GetMemberByPhone()
	{
		$s_time = microtime();
        $where='';
        $member_phone=trim($this->filter['member_phone']);
		//$order_id = 49;
		if(!empty($member_phone))
		{
			$where.= " `member_phone`='".$member_phone."'";
        }else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "手机号不能为空";
			$this -> return_msg = array();
			$this->display();
		}

        if(!empty($where)){
            //查询商品详情
            $sql = "select `member_id`,`country_id`,`province_id`,`city_id`, `region_id`,`customer_source_id` as `source_id`,`member_name`,`department_id`,`mem_card_sn`,`member_phone`,`member_age`,`member_qq`,`member_email`,`member_aliww`,`member_dudget`,`member_maristatus`,`member_address`,`member_peference`,`member_type` from `base_member_info` " .
                   " where".$where." ;";
            $row = $this->db->getRow($sql);
        }else{
            $row=false;
        }

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此会员";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}

	/**
    * 通过用户名查询会员信息
    * @param $member_name
    * @return json
    */
	public function GetMemberByName()
	{
		$s_time = microtime();
        $where='';
        $member_name=trim($this->filter['member_name']);
		//$order_id = 49;
		if(!empty($member_name))
		{
			$where.= " `member_name` like '".$member_name."%'";
        }else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "用户名不能为空";
			$this -> return_msg = array();
			$this->display();
		}

        if(!empty($where)){
            //查询商品详情
            $sql = "select `member_id`,`country_id`,`province_id`,`city_id`, `region_id`,`source_id`,`member_name`,`department_id`,`mem_card_sn`,`member_phone`,`member_age`,`member_qq`,`member_email`,`member_aliww`,`member_dudget`,`member_maristatus`,`member_address`,`member_peference`,`member_type` from `base_member_info` " .
                   " where".$where." LIMIT 10;";
            $row = $this->db->getAll($sql);
        }else{
            $row=false;
        }

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此会员";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}

	/**
    * 通过会员id查询会员信息
    * @param $member_id
    * @return json
    */
	public function GetMemberByMember_id()
	{
		$s_time = microtime();
        $where='';
        $member_id=intval(trim($this->filter['member_id']));
		//$order_id = 49;
		if(empty($member_id)){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "会员id不能为空";
			$this -> return_msg = array();
			$this->display();
		}

        //查询商品详情
        $sql = "select `member_id`,`country_id`,`province_id`,`city_id`, `region_id`,`customer_source_id` as `source_id`,`member_name`,`department_id`,`mem_card_sn`,`member_phone`,`member_age`,`member_qq`,`member_email`,`member_aliww`,`member_dudget`,`member_maristatus`,`member_address`,`member_peference`,`member_type` from `base_member_info` " .
               " where `member_id` = $member_id";
        $row=$this->db->getRow($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此会员";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}

	/**
    * 更新预约信息
    * @param
    * @return json
    */
	public function updateBespokeDealStatus()
	{
		$s_time = microtime();
        $where='';
		if(!isset($this->filter['bespoke_id']) && empty(trim($this->filter['bespoke_id']))){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "预约id不能为空";
			$this -> return_msg = array();
			$this->display();
		}
        $bespoke_id = trim($this->filter['bespoke_id']);

		//更新信息
		$sql = "UPDATE  `app_bespoke_info` SET `deal_status`=1 WHERE `bespoke_id`='{$bespoke_id}'";
		$row=$this->db->query($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if($row==false){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "预约更新失败";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}

	/**
    * 更新预约信息
    * @param
    * @return json
    */
	public function updateBespokeDeal_Status()
	{
		$s_time = microtime();
        $where='';
		if(!isset($this->filter['bespoke_id']) && empty(trim($this->filter['bespoke_id']))){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "预约id不能为空";
			$this -> return_msg = array();
			$this->display();
		}
        $bespoke_id = trim($this->filter['bespoke_id']);

		//更新信息
		$sql = "UPDATE  `app_bespoke_info` SET `deal_status`=2 WHERE `bespoke_id`='{$bespoke_id}'";
		$row=$this->db->query($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if($row==false){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "预约更新失败";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}

	/**
    * 添加会员信息
    * @param
    * @return json
    */
	public function addMemberInfo()
	{
		$s_time = microtime();
        $where='';
		if(!isset($this->filter['member_name']) && empty(trim($this->filter['member_name']))){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "用户名不能为空";
			$this -> return_msg = array();
			$this->display();
		}
        $member_name = trim($this->filter['member_name']);

        if(!isset($this->filter['member_phone']) && empty(trim($this->filter['member_phone']))){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "会员手机号不能为空";
			$this -> return_msg = array();
			$this->display();
		}
        $member_phone = trim($this->filter['member_phone']);
        $member_age = 20;
		if(isset($this->filter['member_age']) && !empty(trim($this->filter['member_age']))){
			$member_age = 	trim($this->filter['member_age']);
		}

        $department_id = 0;
		if(isset($this->filter['department_id']) && !empty(trim($this->filter['department_id']))){
			$department_id = 	trim($this->filter['department_id']);
		}
        $customer_source_id = 0;
		if(isset($this->filter['customer_source_id']) && !empty(trim($this->filter['customer_source_id']))){
			$customer_source_id=trim($this->filter['customer_source_id']);
		}

        $member_type = 1;
		if(isset($this->filter['member_type']) && !empty(trim($this->filter['member_type']))){
			$member_type = 	trim($this->filter['member_type']);
		}
		if(isset($this->filter['reg_time']) && !empty(trim($this->filter['reg_time']))){
			$reg_time = 	trim($this->filter['reg_time']);
		}else{
            $reg_time = 	time();
        }
		if(isset($this->filter['make_order']) && !empty(trim($this->filter['make_order']))){
			$make_order = 	trim($this->filter['make_order']);
		}else{
            $make_order = 	''; 
        }
        if(!empty(trim($this->filter['member_email']))){
            $member_email =  trim($this->filter['member_email']);
        }
        
		//插入信息
		$sql = "INSERT INTO `base_member_info` (`member_name`, `member_phone`, `member_age`, `member_type`,`department_id`,`customer_source_id`,`reg_time`,`make_order`,`member_email`) VALUES ('{$member_name}','{$member_phone}',{$member_age}
		,{$member_type},{$department_id},{$customer_source_id},{$reg_time},'{$make_order}','{$member_email}')";

		if($this->db->query($sql)){
			$row = $this->db->insertId($sql);
		}

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "添加失败";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}

	/**
    * 通过会员id查询会员地址
    * @param $member_id
    * @return json
    */
	public function GetMemberAddressByMember_id()
	{
		$s_time = microtime();
        $where='';
        $member_id=trim($this->filter['member_id']);
		//$order_id = 49;
		if(!empty($member_id))
		{
			$where.= " `member_id` =".$member_id." and `mem_is_def` in(1)";
        }else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "会员id不能为空";
			$this -> return_msg = array();
			$this->display();
		}

        if(!empty($where)){
            //查询商品详情
            $sql="select `mem_address_id`,`member_id`,`customer`,`mobile`,`mem_country_id`,`mem_province_id`,`mem_city_id`, `mem_district_id`,`mem_address`,`mem_is_def` from `app_member_address` " .
            " where".$where." ;";
            $row = $this->db->getRow($sql);
        }else{
            $row=false;
        }

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此会员地址";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}

    /**
    * 专题添加预约信息
    * @param
    * @return json
    */
    public function AddActiveBespokeInfo()
    {
        
        $s_time = microtime();
        $bes['department_id'] = intval(trim($this->filter['department']));//体验店ID
        $bes['mem_id'] = intval(trim($this->filter['mem_id']));//客户ID
        $bes['customer'] = trim($this->filter['bespoke_man']);//客户姓名
        $bes['customer_mobile'] = trim($this->filter['mobile']);//客户手机
        $bes['customer_email'] = trim($this->filter['email']);//客户email
        $bes['customer_address'] = trim($this->filter['address_mes']);//客户地址
        $bes['bespoke_inshop_time'] = trim($this->filter['shop_time']);//预约到店时间
        $bes['remark'] = trim($this->filter['bespok_remark']);//预约备注
        $bes['create_time'] = date("Y-m-d H:i:s");//预约时间
        $bes['customer_source_id'] = trim($this->filter['from_ad']);//预约来源
        do{
            $besp_sn=$this->create_besp_sn();
            if(!$this->get_bespoke_by_besp_sn($besp_sn)){
                break;
            }
        }while(true);
        $bes['bespoke_sn'] = $besp_sn;//唯一预约号

        //是否有预约
        if($bes['customer_mobile'] != '' || $bes['department_id'] != ''){

            $sql = "SELECT `bespoke_id`,`bespoke_sn`,`department_id`,`re_status`,`bespoke_inshop_time` FROM `app_bespoke_info` WHERE `customer_mobile` = '".$bes['customer_mobile']."' AND `department_id` = '".$bes['department_id']."' AND `bespoke_status` !=3 LIMIT 1";
            $bse_user = $this->db->getRow($sql);
        }
        if($bse_user){
            if($bse_user['re_status'] == 2 || $bse_user['re_status'] == 0){

                $this -> error = 1;
                $this -> return_sql = $sql_s;
                $this -> error_msg = "Sorry，该手机号码已经有未处理的预约记录。\n预约号：".$bse_user['bespoke_sn']."，\n到店时间：".$bse_user['bespoke_inshop_time']."；\n请不要重复预约！";
                $this->display();
            }
        }
                
        foreach ( $bes as $k => $v ){
            if($v != ''){
                $tmp .= '`' . $k . '` = \'' . $v . '\',';
            }
        }
        $tmp = rtrim($tmp,',');
        $sql = "INSERT INTO `app_bespoke_info` SET {$tmp}";
        if($this->db->query($sql)){
            $row = $this->db->insertId($sql);
        }
        $bes_list = array();
        $bes_list['bespoke_sn'] = $bes['bespoke_sn'];
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$row){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "添加失败";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $bes_list;
            $this->display();
        }
    }

    /**
    * 手机号查询会员信息
    * @param $member_phone
    * @return json
    */
    public function GetBespokeUserInfo()
    {

        $s_time = microtime();
        $where = '';
        $member_phone = trim($this->filter['mobile']);

        if(!empty($member_phone))
        {
            $where.= " `member_phone`='".$member_phone."'";
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "手机号不能为空";
            $this -> return_msg = array();
            $this->display();
        }

        if(!empty($where)){
            $sql = "select `member_id`,`member_email` from `base_member_info` where ".$where."";
            $row = $this->db->getRow($sql);
        }else{
            $row=false;
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$row){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "此手机号无记录！";
            $this -> return_msg = NULL;
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $row;
            $this->display();
        }
    }

    /**
    * 专题添加预约新会员
    * @param
    * @return json
    */
    public function InsertBespokeUserInfo()
    {

        $s_time = microtime();
        $us['member_name'] = trim($this->filter['bespoke_man']);//客户姓名
        $us['member_phone'] = trim($this->filter['mobile']);//客户手机
        $us['member_email'] = trim($this->filter['email']);//客户email
        $us['member_wedding'] = trim($this->filter['wedding_time']);//客户email
        $us['member_address'] = trim($this->filter['address_mes']);//地址
        $us['city_id'] = trim($this->filter['city']);//城市
        $us['province_id'] = trim($this->filter['province']);//省份
        $us['region_id'] = trim($this->filter['district']);//区县
        $us['reg_time'] = time();//申请时间
        $us['member_type'] = 1;//会员类型 1、普通会员
        $us['department_id'] = trim($this->filter['department']);//部门
        $us['customer_source_id'] = trim($this->filter['from_ad']);//来源

        foreach ( $us as $k => $v ){
            if($v != ''){
                $tmp .= '`' . $k . '` = \'' . $v . '\',';
            }
        }
        $tmp = rtrim($tmp,',');
        $sql = "INSERT INTO `base_member_info` SET {$tmp}";
        if($this->db->query($sql)){
            $row = $this->db->insertId($sql);
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$row){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "添加失败";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $row;
            $this->display();
        }
    }


    /**
    * 专题标识查询预约信息
    * @param $member_phone
    * @return json
    */
    public function GetBespokeLists()
    {
        
        $s_time = microtime();
        $where='';
        $remark=trim($this->filter['remark']);

        if(!empty($remark))
        {
            $where.= " `remark`='".$remark."'";
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "预约标识为空";
            $this -> return_msg = array();
            $this->display();
        }
        if(!empty($where)){
            //查询商品详情
            $sql = "select `department_id`,`customer_mobile` from `app_bespoke_info` where ".$where."";

            $row = $this->db->getAll($sql);
        }else{
            $row=false;
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$row){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到此会员";
            $this -> return_msg = NULL;
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $row;
            $this->display();
        }
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
        $sql="select ".$select." from `app_bespoke_info` where `bespoke_sn` = '".$besp_sn."'";
        return $this->db->getRow($sql);
    }

	/*------------------------------------------------------ */
	//-- 返回内容
	//-- by col
	/*------------------------------------------------------ */
	public function display()
	{
		$res = array("error" => intval($this -> error), "error_msg" => $this -> error_msg, "return_msg" => $this -> return_msg, "return_sql" => $this -> return_sql);
		die (json_encode($res));
	}

	/*------------------------------------------------------ */
	//-- 记录日志信息
	//-- by haibo
	/*------------------------------------------------------ */
	public function recordLog($api, $response_time, $str)
	{
        define('ROOT_LOG_PATH',str_replace('api/api.php', '', str_replace('\\', '/', __FILE__)));
		if (!file_exists(ROOT_LOG_PATH . 'logs/api_logs'))
		{
			mkdir(ROOT_LOG_PATH . 'logs/api_logs', 0777);
			chmod(ROOT_LOG_PATH . 'logs/api_logs', 0777);
		}
		$content = $api."||".$response_time."||".$str."||".date('Y-m-d H:i:s')."\n";
		$file_path =  ROOT_LOG_PATH . 'logs/api_logs/'.date('Y')."_".date('m')."_".date('d')."_api_log.txt";
		file_put_contents($file_path, $content, FILE_APPEND );
	}
}
?>
