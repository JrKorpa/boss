<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseOrderInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-13 21:02:11
 *   @update	:
 *  -------------------------------------------------
 */
class BaseOrderInfoModel extends Model
{
    // 布产单状态 =》 订单布产状态 转换关系
    public $bcd_status_2_order_bc_status = array(
        '10' => '1',
        '2' => '2',
        '3' => '2',
        '4' => '3',
        '7' => '3',
        '9' => '4',
        '11' => '5'
    );

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
            'is_real_invoice'=>'是否需要开发票',
            'hidden'=>'是否需要隐藏'
        );
		parent::__construct($id,$strConn);
	}
    /**	pageList，分页列表 
	 *
	 *	@url ApplicationController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
	    if(isset($where['is_netsale']) && in_array($where['is_netsale'], array(1, 2))) {
		  $sql  = "SELECT `b`.`order_amount`,`b`.`money_paid`,`b`.`money_unpaid`,`b`.`goods_return_price`,`b`.`real_return_price`,`b`.`shipping_fee`,`b`.`goods_amount`,`b`.`coupon_price`,`b`.`favorable_price`,`b`.`card_fee`,`b`.`pack_fee`,`b`.`pay_fee`,`b`.`insure_fee`,`sc`.`channel_class`,
		  (SELECT 1 FROM `cuteframe`.`sales_channels_person` WHERE `dp_is_netsale` LIKE CONCAT('%',(SELECT make_order FROM `front`.`app_bespoke_info` WHERE `bespoke_id` = a.`bespoke_id`),'%') LIMIT 1) person,`a`.* FROM `".$this->table()."` as a,`app_order_account` as b,`cuteframe`.`sales_channels` `sc` ";
	    } else {
	        $sql  = "SELECT `b`.`order_amount`,`b`.`money_paid`,`b`.`money_unpaid`,`b`.`goods_return_price`,`b`.`real_return_price`,`b`.`shipping_fee`,`b`.`goods_amount`,`b`.`coupon_price`,`b`.`favorable_price`,`b`.`card_fee`,`b`.`pack_fee`,`b`.`pay_fee`,`b`.`insure_fee`,`sc`.`channel_class`,
		  `a`.* FROM `".$this->table()."` as a,`app_order_account` as b,`cuteframe`.`sales_channels` `sc` ";
	    }
		//$sql .= " where `a`.`id`=`b`.`order_id` and ((a.referer = '天生一对加盟商' and a.order_status > 1) or a.referer != '天生一对加盟商')";
		$sql .= " where `a`.`id`=`b`.`order_id` AND `a`.`department_id` = `sc`.`id` ";
		if(!empty($where['order_sn']))
		{
			$sql .= " AND `a`.`order_sn` in ('".$where['order_sn']."')";
		}
        if(!empty($where['create_user']))
        {
            $sql .= " AND `a`.`create_user` like \"%".addslashes($where['create_user'])."%\"";
        }
        if(isset($where['genzong']) && $where['genzong'] != "")
        {
            $sql .= " AND `a`.`genzong` like \"%".addslashes($where['genzong'])."%\"";
        }
        if(!empty($where['consignee']))
        {
            $sql .= " AND `a`.`consignee` like \"%".addslashes($where['consignee'])."%\"";
        }
        if(!empty($where['mobile']))
        {
            $sql .= " AND `a`.`mobile` = '".addslashes($where['mobile'])."'";
        }
		if(!empty($where['order_status']))
		{
			$sql .= " AND `a`.`order_status` = ".addslashes($where['order_status']);
		}
		if(!empty($where['order_check_status']))
		{
			$sql .= " AND `a`.`order_check_status` = ".addslashes($where['order_check_status']);
		}
		if(!empty($where['order_pay_status']))
		{
            if ($where['order_pay_status']=='234') {
                $sql .= " AND `a`.`order_pay_status` > 1";
            } else {
                $sql .= " AND `a`.`order_pay_status` = ".addslashes($where['order_pay_status']);
            }
		}
		if(isset($where['pay_type']) && $where['pay_type'] != "")
		{
			$sql .= " AND `a`.`order_pay_type` = ".$where['pay_type'];
		}
		if(isset($where['department_id'])&&$where['department_id'] != "")
		{
			//$sql .= " AND `a`.`department_id` in(".addslashes($where['department_id']).")";
            //if(isset($where['sale']) && $where['sale']=='sale'){
                //$sql .=" AND (`a`.`create_user`='".$_SESSION['userName']."' or `a`.`genzong`='".$_SESSION['userName']."') ";
            //} 
            if($where['is_user_super'] === true){
                $sql .= " AND `a`.`department_id` in(".addslashes($where['department_id']).")";
            }else{
                $sql .=$this->jointDepList($where['department_id'],$where['sale']); 
            }
		}
        if(!empty($where['start_time'])){
            $sql.=" AND `create_time` >= '".$where['start_time']." 00:00:00'";
        }
        if(!empty($where['end_time'])){
            $sql.=" AND `create_time` <= '".$where['end_time']." 23:59:59'";
        }
        if(!empty($where['pay_date_start_time'])){
            $sql.=" AND `pay_date` >= '".$where['pay_date_start_time']." 00:00:00'";
        }
        if(!empty($where['pay_date_end_time'])){
            $sql.=" AND `pay_date` <= '".$where['pay_date_end_time']." 23:59:59'";
        }
        if(!empty($where['send_good_status'])){
        $sql .= " AND `a`.`send_good_status` = ".addslashes($where['send_good_status']);
         }
        if(!empty($where['delivery_status'])){
            $sql .= " AND `a`.`delivery_status` = ".addslashes($where['delivery_status']);
        }
        if(!empty($where['buchan_status'])){
            $sql .= " AND `a`.`buchan_status` = ".addslashes($where['buchan_status']);
        }
        if(!empty($where['customer_source'])){
            $sql .= " AND `a`.`customer_source_id` = ".addslashes($where['customer_source']);
        }
		if(!empty($where['referer']))
		{
			if($where['referer'] == '非婚博会'){
                $sql .= " AND `a`.`referer` != '婚博会' ";
			}else{
                $sql .= " AND `a`.`referer` = '{$where['referer']}'";
			}
		}

		if(isset($where['is_delete']))
		{
           if ($where['is_delete']==0 || $where['is_delete']==1){
               $sql .= " AND `a`.`is_delete` = ".$where['is_delete'];
          }
		}
        if(isset($where['is_zp']) and $where['is_zp']!='')
        {
            $sql .= " AND `a`.`is_zp` = ".$where['is_zp'];
        }

        if(isset($where['order_ids']))
        {
            if(!empty($where['order_ids'])){
                $sql .= " AND `a`.`id` in(".$where['order_ids'].")";
            }else{
                $sql .= " AND `a`.`id`=0";
            }
        }
        if(isset($where['close_order']) && $where['close_order']=='1')
        {
			$sql .= " AND `a`.`order_status` not in (3,4) ";			
        }
        
        if(isset($where['bespoke_id']))
        {
			if(!empty($where['bespoke_id'])){
	            $sql .= " AND `a`.`bespoke_id`  = '".$where['bespoke_id']."' ";
			}
        }
        if(isset($where['recommender_sn']))
        {
            if(!empty($where['recommender_sn'])){
                $sql .= " AND `a`.`recommender_sn`  = '".$where['recommender_sn']."' ";
            }
        }

		if(isset($where['order_type']) && $where['order_type'] != '')
        {
            $sql .= " AND `a`.`is_xianhuo`  = ".$where['order_type']."";
        }

        if(isset($where['channel_class']) && $where['channel_class'] != '')
        {
            $sql .= " AND `sc`.`channel_class`  = ".$where['channel_class']."";
        }
        
		if(isset($where['is_netsale']) && $where['is_netsale'] == 2)
        {
            $sql .= " AND EXISTS(SELECT 1 FROM cuteframe.`sales_channels_person` WHERE dp_is_netsale LIKE CONCAT('%',
      				(SELECT make_order FROM front.app_bespoke_info WHERE bespoke_id = a.`bespoke_id` AND `make_order` != '' GROUP BY `make_order`),'%')) ";
        }
        else if(isset($where['is_netsale']) && $where['is_netsale'] == 1)
        {
            $sql .= " AND NOT EXISTS(SELECT 1 FROM cuteframe.`sales_channels_person` WHERE dp_is_netsale LIKE CONCAT('%',
      				(SELECT make_order FROM front.app_bespoke_info WHERE bespoke_id = a.`bespoke_id` AND `make_order` != '' GROUP BY `make_order`),'%')) ";
        }

		

		$sql .= " ORDER BY `a`.`id` DESC";
        //echo $sql;exit();       
		$data = $this->db()->getPageListOpt($sql,array(),$page, $pageSize,$useCache,'FROM `base_order_info` as a','ORDER BY `a`.`id` DESC');
        //统计问题
       // $cdata=$this->db()->getAll($sql);
        //$where['sql']=$sql;
        //$where = var_export($where,true);
        //file_put_contents(APP_ROOT."sales/logs/order_search".date('Y-m-d').'.txt',$where.PHP_EOL,FILE_APPEND);
        $all_price = 0;
        //if(!empty($cdata)){
        //    $all_price= array_sum(array_column($cdata,'order_amount'));
        //}

		return array('data'=>$data,'all_price'=>$all_price);
	}

    public function jointDepList($depList,$sale)
    {
        $depMen = array();
        $str = '';
        $str_stong = '';
        $str_ltong = '';
        $user_stong = $_SESSION['userName'];
        $depInfo = explode(",", $depList);
        foreach ($depInfo as $did) {
            if(in_array($did, $sale)){
                $depMen[] = $did;
            }else{
                $str_stong.= " (`a`.`department_id` = ".$did." and (a.create_user = '".$user_stong."' or a.genzong = '".$user_stong."')) or ";
            }
        }
        if(!empty($depMen)){
            if(count($depMen) == 1){
                $str_ltong = " (`a`.`department_id` = ".$depMen[0].") or ";
            }else{
                $str_ltong = " (`a`.`department_id` in(".implode(",", $depMen).")) or ";
            }
        }
        $str = " AND (".rtrim($str_ltong.$str_stong,"or ").")";
        return $str;
    }
		//2015/1/9 星期五
	public function select($dataArray='',$fieldArray='')
	{
		$fieldString = '';
		if(is_array($fieldArray) && !empty($fieldArray))
		{
			foreach($fieldArray as $key => $value){
				$fieldString .= ", $value";
				$fieldString = ltrim($fieldString,',');
			}
		}else
		{
			$fieldString = '*';
		}
		$DataString = '';
		if(is_array($dataArray) && !empty($dataArray)){
			foreach($dataArray as $key => $value){
				$DataString .= " AND $key ='$value' ";
			}
		}
		$sql = "SELECT ".$fieldString." FROM ".$this->table()." WHERE 1 ";
		$sql .= $DataString;

		$data = $this->db()->getAll($sql);
		//var_dump($sql);exit;
		return $data;
	}

    /*
     * 生成单号
     */
    public function getOrderSn(){
        switch (SYS_SCOPE){
            case 'boss':
                return date('Ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),5,'1',STR_PAD_LEFT);
            case "zhanting":
                return '9'.date('Ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),5,'1',STR_PAD_LEFT);
            default:
                die();
        }
        //return date('Ymd') . str_pad(mt_rand(1, 999999), 6, '1', STR_PAD_LEFT);
    }

    /*
     * 根据订单id获取订单数据
     */
    public function getOrderInfoById($id) {
        if(empty($id)){
            return false;
        }
        $sql = "SELECT o.*,s.company_id FROM `".$this->table()."` o left join cuteframe.sales_channels s on o.department_id=s.id  WHERE o.`id`=".$id." limit 1;";
        return $this->db()->getRow($sql);
    }

    /*
     * 根据订单号获取订单数据
     */
    public function getOrderInfoBySn($order_sn) {
        if(empty($order_sn)){
            return false;
        }
        $sql = "SELECT * FROM `".$this->table()."` WHERE `order_sn`='".$order_sn."' limit 1;";
        return $this->db()->getRow($sql);
    }
    /*
     * 根据预约单号获取订单数据
     */
    public function getOrderInfoByBespokeId($bespokeId) {
        if(empty($bespokeId)){
            return false;
        }
        $sql = "SELECT * FROM `".$this->table()."` WHERE `bespoke_id`='".$bespokeId."'";
        return $this->db()->getAll($sql);
    }

    /*
     * 添加订单
     */
    public function makeOrder($all_data){
        //添加订单信息
        $order_data = $all_data['order'];
        if(empty($order_data)){
            return false;
        }

        //总的价格
        $money_data = $all_data['money'];
        if(empty($money_data)){
            return false;
        }

        //发票
        $invoice_data = $all_data['invoice'];
        if(empty($invoice_data)){
            return false;
        }
        $address_data = $all_data['address'];
        if(empty($address_data)){
            return false;
        }

        $pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

            $order_field = " `order_sn`, `user_id`, `order_status`, `order_pay_status`,  `customer_source_id`, `department_id`, `create_time`, `create_user`,  `modify_time`, `order_remark`,  `is_delete`,`delivery_status`,`send_good_status`,`buchan_status`,`referer`";
            $order_value = "'".$order_data['order_sn']."' ,".$order_data['user_id']." ,".$order_data['order_status']." ,".$order_data['order_pay_status'].",".$order_data['customer_source_id']." ,".$order_data['department_id']." , '".$order_data['create_time']."' , '".$order_data['create_user']."', '".$order_data['modify_time']."', '".$order_data['order_remark']."', '".$order_data['is_delete']."',1,1,1,'".$order_data['referer']."'";
            $sql_order = "INSERT INTO `base_order_info` (" . $order_field . ") VALUES (". $order_value .")";
            $pdo->query($sql_order);
			$order_id = $pdo->lastInsertId();

            $money_field = " `order_id`, `order_amount`, `money_paid`, `money_unpaid`";
            $money_value = "".$order_id." ,".$money_data['order_amount']." ,".$money_data['money_paid']." ,".$money_data['money_unpaid']." ";
            $sql_money = "INSERT INTO `app_order_account` (" . $money_field . ") VALUES (". $money_value .")";
			$pdo->query($sql_money);

//            $action_field = " `order_id`, `order_status`, `shipping_status`, `pay_status`,`create_time`,`create_user`,`remark`";
//            $action_value = "".$order_id." ,".$action_data['order_status']." ,".$action_data['shipping_status']." ,".$action_data['pay_status'].", '".$action_data['create_time']."' , '".$action_data['create_user']."', '".$action_data['remark']."' ";
//            $sql_action = "INSERT INTO `app_order_action` (" . $action_field . ") VALUES (". $action_value .")";
//			$pdo->query($sql_action);


            //$invoice_field = " `order_id`, `is_invoice`, `invoice_amount`,`create_time`";
            //$invoice_value = "".$order_id." ,".$invoice_data['is_invoice']." ,".$invoice_data['invoice_amount']." , '".$invoice_data['create_time']."' ";
            //$sql_invoice = "INSERT INTO `app_order_invoice` (" . $invoice_field . ") VALUES (". $invoice_value .")";
            $invoice_fields = array('order_id', 'is_invoice', 'invoice_amount','create_time','invoice_title','invoice_type','invoice_status','title_type','invoice_address','invoice_email','taxpayer_sn');
            foreach ($invoice_data as $key=>$vo){
                if(!in_array($key,$invoice_fields)){
                    unset($invoice_data[$key]);
                }
            }
            $invoice_data['order_id'] = empty($invoice_data['order_id'])?$order_id:$invoice_data['order_id'];
            $sql_invoice = $this->insertSql($invoice_data,"app_order_invoice");
            $pdo->query($sql_invoice);
            //发货地址

           /* $address_field = " `order_id`, `consignee`, `tel`,`address`";
            $address_value = "".$order_id." ,'".$address_data['consignee']."' ,'".$address_data['tel']."' , '".$address_data['address']."' ";
            $sql_address = "INSERT INTO `app_order_address` (" . $address_field . ") VALUES (". $address_value .")";
            $pdo->query($sql_address);*/

		}
		catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return $order_id;
    }

    /*
     * 添加订单
     */
    public function makeEmptyOrder($all_data){
        //添加订单信息
        $order_data = $all_data['order'];
        if(empty($order_data)){
            return false;
        }

        //发票
        $invoice_data = $all_data['invoice'];
        if(empty($invoice_data)){
            return false;
        }
        if(isset($all_data['address'])){
            $address_data = $all_data['address'];
            if(empty($address_data)){
                return false;
            }
        }


        $pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			
			if(!isset($order_data['referer']) || empty($order_data['referer'])){
				$order_data['referer'] = '未知';
			}
			
			if(!isset($order_data['recommended']) || empty($order_data['recommended'])){
				$order_data['recommended'] = '';
			}
                          if(!isset($order_data['user_id']) || empty($order_data['user_id'])){
                                  $order_data['user_id'] = 0;
                          }else{
				  $order_data['user_id']  = intval($order_data['user_id']);
			}
            $order_field = array("order_sn","bespoke_id","consignee","recommended","mobile","user_id","order_status","order_pay_status","order_pay_type","customer_source_id","department_id","create_time","create_user","modify_time","order_remark","is_delete","is_xianhuo","is_zp","delivery_status","referer","send_good_status","buchan_status");
			$bespoke_id = isset($order_data['bespoke_id'])?intval($order_data['bespoke_id']):0;
			$order_data['bespoke_id'] = $bespoke_id;
			$order_data['send_good_status']=1;
			$order_data['buchan_status']=1;
			$order_data['out_company']=0;
			
            if(empty($order_data['is_real_invoice']) && !empty($order_data['department_id'])){                
                $sql = "SELECT channel_class FROM cuteframe.`sales_channels` WHERE id={$order_data['department_id']}";
                $sales_channels = $this->db()->getOne($sql);
                if($sales_channels == 2){
                    $order_data['is_real_invoice']=0;
                    $sql = "select id from cuteframe.company where company_sn='5A'";
                    $order_data['out_company'] = $this->db()->getOne($sql);
                }
            }

			foreach($order_field as $key => $val){
				$order_value_arr[$val] = isset($order_data[$val])?str_replace("'","",$order_data[$val]):''; 
			}
			
            //$sql_order = "INSERT INTO `base_order_info` (`" . implode('`,`',array_keys($order_value_arr)) . "`) VALUES ('". implode("','",array_values($order_value_arr)) ."')";
         
            $order_data['pay_date']=isset($order_data['pay_date'])?$order_data['pay_date']:"0000-00-00 00:00:00";
            $order_field = " `order_sn`,`bespoke_id`,`consignee`,`recommended`,`recommender_sn`,`mobile`,`user_id`, `order_status`, `order_pay_status`, `order_pay_type`,  `customer_source_id`, `department_id`, `create_time`, `create_user`,  `modify_time`, `order_remark`,  `is_delete`,`send_good_status`,`buchan_status`,`is_xianhuo`,`is_zp`,`delivery_status`,`referer`,`is_real_invoice`,`out_company`,`pay_date`";
	//var_dump($order_data);die;
            $order_value = "'".$order_data['order_sn']."' ,'".$order_data['bespoke_id']."' ,'".$order_data['consignee']."' ,'".$order_data['recommended']."','".$order_data['recommender_sn']."'  ,'".$order_data['mobile']."' ,".$order_data['user_id']." ,".$order_data['order_status']." ,".$order_data['order_pay_status'].",".$order_data['order_pay_type'].",".$order_data['customer_source_id']." ,".$order_data['department_id']." , '".$order_data['create_time']."' ,'".$order_data['create_user']."', '".$order_data['modify_time']."', '".$order_data['order_remark']."', '".$order_data['is_delete']."',1,1,".$order_data['is_xianhuo'].",".$order_data['is_zp'].",".$order_data['delivery_status'].",'".$order_data['referer']."','".$order_data['is_real_invoice']."',{$order_data['out_company']},'".$order_data['pay_date']."'";
            if($order_data['order_status']==2||$order_data['order_status']==5){
            	$order_field.=",`check_time`,`check_user`";
            	$order_value.=", '".$order_data['create_time']."' ,'".$order_data['create_user']."'";
            }
            $sql_order = "INSERT INTO `base_order_info` (" . $order_field . ") VALUES (". $order_value .")";
            $pdo->query($sql_order);
			$order_id = $pdo->lastInsertId();
			//$details_field = " `order_id`, `goods_name`, `goods_count` ,`create_time`,`modify_time` ,`goods_price`, `favorable_status`, `favorable_price`,`is_stock_goods`,`xiangqian`";
			//$details_value = "".$order_id." ,0 ,0 ,0,0 ,0 ,0,0 ,0 ,0";
			//$sql_details = "INSERT INTO `app_order_details` (" . $details_field . ") VALUES (". $details_value .")";
			//$pdo->query($sql_details);
            $money_field = " `order_id`, `order_amount`, `money_paid`, `money_unpaid`,`coupon_price`,`shipping_fee`,`insure_fee`,`pay_fee`,`pack_fee`,`card_fee`,`real_return_price`,`goods_amount`,`favorable_price`";
            $money_value = "".$order_id." ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0 ,0";
            $sql_money = "INSERT INTO `app_order_account` (" . $money_field . ") VALUES (". $money_value .")";
            $pdo->query($sql_money);
            $invoice_field = " `order_id`, `is_invoice`, `invoice_amount`,`create_time`";
            $invoice_value = "".$order_id." ,".$invoice_data['is_invoice']." ,0, '".$invoice_data['create_time']."' ";
            $sql_invoice = "INSERT INTO `app_order_invoice` (" . $invoice_field . ") VALUES (". $invoice_value .")";
            $pdo->query($sql_invoice);
            //用户地址信息入库
            if(isset($address_data)&&!empty($address_data)){
		
		if(empty($address_data['shop_type'])){
			$address_data['shop_type']=0;
		}


                $address_field = " `order_id`, `consignee`,`tel`,`address`,`distribution_type`,`express_id`,`freight_no`,`country_id`,`province_id`,`city_id`,`regional_id`,`shop_type`,`shop_name`,`email`,`zipcode`,`goods_id`";
                $address_data['regional_id'] = empty($address_data['regional_id'])?0:intval($address_data['regional_id']);
		$address_value = "".$order_id." ,'".$address_data['consignee']."' ,'".$address_data['tel']."' , '".$address_data['address']."','".$address_data['distribution_type']."','".$address_data['express_id']."','".$address_data['freight_no']."' ,'".$address_data['country_id']."','".$address_data['province_id']."','".$address_data['city_id']."','".$address_data['regional_id']."',".$address_data['shop_type'].",'".$address_data['shop_name']."','".$address_data['email']."','".$address_data['zipcode']."',0";
                
		$sql_address = "INSERT INTO `app_order_address` (" . $address_field . ") VALUES (". $address_value .")";
       
                $pdo->query($sql_address);
            }
		}
		catch(Exception $e){//捕获异常
            $error = var_export($e,true);
            file_put_contents('makeorder.txt',$error,FILE_APPEND);
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return $order_id;
    }

    /*
     * 添加订单
     */
    public function makeOrderGoods($all_data){
        //订单信息
        $order_data = $all_data['order'];
        if(empty($order_data)){
            return false;
        }
       $order_id = $order_data['order_id'];
       if(empty($order_id)){
           return false;
       }

        //商品详细
        $goods = $all_data['goods'];
        if(empty($goods)){
            return false;
        }
        $pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

            if($order_data['is_edit'] == 1){
               $sql_order = "UPDATE `base_order_info` SET `is_xianhuo` = ".$order_data['is_xianhuo']." WHERE `id`=".$order_id;
               $pdo->query($sql_order);
            }

            $goods_field = "`order_id`,`goods_id`,`goods_sn`,`goods_name`,`goods_price`,`goods_count`,`create_time`,`create_user`,`modify_time`,`details_status`,`is_stock_goods`,`details_remark`,`cut`,`cart`,`clarity`,`color`,`zhengshuhao`,`caizhi`,`jinse`,`jinzhong`,`zhiquan`,`kezi`,`face_work`,`xiangqian`,`send_good_status`,`buchan_status`,`goods_type`,`cat_type`,`product_type`";
            $goods_value = " ".$order_id." ,'".$goods['goods_id']."' ,'".$goods['goods_sn']."' ,'".$goods['goods_name']."' ,".$goods['goods_price']." ,".$goods['goods_count']." , '".$goods['create_time']."' , '".$goods['create_user']."', '".$goods['modify_time']."', ".$goods['details_status'].", ".$goods['is_stock_goods'].", '".$goods['details_remark']."','".$goods['cut']."','".$goods['cart']."','".$goods['clarity']."','".$goods['color']."','".$goods['zhengshuhao']."','".$goods['caizhi']."','".$goods['jinse']."','".$goods['jinzhong']."','".$goods['zhiquan']."','".$goods['kezi']."','".$goods['face_work']."','".$goods['xiangqian']."',1,1,'".$goods['goods_type']."',".$goods['cat_type'].",".$goods['product_type']."";
            $sql_goods = "INSERT INTO `app_order_details` (" . $goods_field . ") VALUES (". $goods_value .")";
            $pdo->query($sql_goods);
            $pdo->lastInsertId();

			//总的价格
            $money_data = $all_data['money'];
            if(empty($money_data)){
                return false;
            }

            $sql_money = "UPDATE `app_order_account` SET `order_amount`=".$money_data['order_amount'].",  `money_unpaid`=".$money_data['money_unpaid'].",`goods_amount`=".$money_data['goods_amount']." WHERE `order_id`=$order_id";
            $pdo->query($sql_money);
			$pdo->lastInsertId();

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

    /*
     * 添加订单添加商品为二位数组
     * 2015-04-07  luna
     */
    public function makeNewOrderGoods($all_data){
        //订单信息
        $order_data = $all_data['order'];
        if(empty($order_data)){
            return false;
        }
        $order_id = $order_data['order_id'];
        if(empty($order_id)){
            return false;
        }
    
        //商品详细
        $goods = $all_data['goods'];
        if(empty($goods)){
            return false;
        }
/*echo '<pre>';
print_r($goods); exit;*/
        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            if($order_data['is_edit'] == 1){
                $sql_order = "UPDATE `base_order_info` SET `is_xianhuo` = ".$order_data['is_xianhuo']." WHERE `id`=".$order_id;
                $pdo->query($sql_order);
            }
            foreach($goods as $val){
                if(!isset($val['favorable_price']) || empty($val['favorable_price'])){
                    $val['favorable_price'] = 0;
                    $val['favorable_status'] = 1;
                }else{
                    $val['favorable_status'] = 3;
                }
                if(!isset($val['xiangkou']) || empty($val['xiangkou'])){
                    $val['xiangkou'] = 0;
                }
    
                $val['order_id'] = $order_id;
                $val['send_good_status'] = 1;
                //add by liuri 注释$val['is_stock_goods'] = 1;
                //$val['is_stock_goods'] = 1;
                if($val['is_stock_goods']===''){
                    $val['is_stock_goods'] = 1;
                }
                if(isset($val['is_xianhuo'])){
                    $val['is_stock_goods'] = $val['is_xianhuo'];
                    unset($val['is_xianhuo']);
                }
                $val['buchan_status'] = 1;
                $val['is_finance'] = 2;
                if($val['goods_type']==='zp'){
                    $val['cat_type']=0;
                    $val['product_type']=0;
                    $val['kuan_sn']='';
                    $val['is_finance']=$this->selectisfinance($val['goods_sn']);
                }
                // fix qiban_type
                if (!isset($val['qiban_type'])) {
                    $val['qiban_type'] = 2;
                }
    
                if(empty($val['is_peishi'])){
                    $val['is_peishi'] =0;
                }
                if(empty($val['goods_price'])){
                    $val['goods_price'] = 0;
                }
                if(empty($val['is_finance'])){
                    $val['is_finance'] =2;
                }
                if(empty($val['is_zp'])){
                    $val['is_zp'] =0;
                }
                if(empty($val['is_cpdz'])){
                    $val['is_cpdz'] =0;
                }
                $cpdzcode = "";
                if(!empty($val['cpdzcode'])){
                    $cpdzcode = $val['cpdzcode'];
                }

                if($val['is_zp'] ==1){
                    $val['favorable_status'] = 3;
                }
                //$val_list = array('is_finance','is_zp','is_peishi',"order_id","goods_id","xiangkou","goods_sn","ext_goods_sn","goods_name","goods_price","goods_count","create_time","create_user","modify_time","details_status","is_stock_goods","details_remark","cut","cart","clarity","color","cert","zhengshuhao","caizhi","jinse","jinzhong","zhiquan","kezi","face_work","xiangqian","send_good_status","buchan_status","goods_type","cat_type","product_type","kuan_sn","favorable_price","favorable_status","policy_id",'qiban_type','dia_type','tuo_type','is_cpdz');
                /* $keys=array();
                 $vals=array();
                 foreach($val_list as $k => $v){
                 $val['policy_id']= isset($val['policy_id'])?$val['policy_id']:0;
                 $keys[] = $v;
                 $vals[] = isset($val[$v])?str_replace("'","",$val[$v]):'';
                 }
                 $sql_goods = "INSERT INTO `app_order_details` (`" . implode('`,`',$keys) . "`) VALUES ('". implode("','",$vals) ."')"; */
                $sql = $this->insertSql($val,'app_order_details');
                $pdo->query($sql);
                $order_detail_id = $pdo->lastInsertId();
                //绑定使用成品定制码
                if($order_detail_id>0 && !empty($cpdzcode)){
                    $sql = "UPDATE front.`base_cpdz_code` SET `order_detail_id`={$order_detail_id},use_status=2 WHERE `code`='{$cpdzcode}'";
                    $pdo->query($sql);                    
                }

                //积分--------------------------------------------------------------------
                /*
                $poing = array();
                if(!empty($val['original_point'])){
                  $selSql = "SELECT * FROM app_order.app_order_account  where order_id={$order_id}";
                  $orderAccount = $this->db()->getRow($selSql);
               

                 //持有积分+商品折扣后的积分+商品奖励积分
                  $orderOriginalPoint = ceil($orderAccount['current_point']+$val['discount_point']+$val['reward_point']);
                  $sql = "UPDATE app_order_account SET current_point={$orderOriginalPoint} WHERE order_id={$order_id}";
                  $pdo->query($sql);
                }*/
            }

            //总的价格
            $money_data = $all_data['money'];
            if(empty($money_data)){
                return false;
            }
            if(isset($money_data['favorable_price'])){
                $sql = "UPDATE `app_order_account` SET `order_amount`=".$money_data['order_amount'].",  `money_unpaid`=".$money_data['money_unpaid'].",`goods_amount`=".$money_data['goods_amount'].",`favorable_price`=".$money_data['favorable_price']." WHERE `order_id`=$order_id";
            }else{
                $sql = "UPDATE `app_order_account` SET `order_amount`=".$money_data['order_amount'].",  `money_unpaid`=".$money_data['money_unpaid'].",`goods_amount`=".$money_data['goods_amount']." WHERE `order_id`=$order_id";
            }
            $pdo->query($sql);
            if(isset($val['is_zp']) && !$val['is_zp'])//添加的商品不是赠品时，修改发票金额
            {
                $sql ="update `app_order_invoice` set `invoice_amount`='".$money_data['order_amount']."' where `order_id`=".$order_id;
                $pdo->query($sql);
            }
    
            if(!empty($all_data['action'])){
                $this->addOrderAction($all_data['action']);
            }
    
            $pdo->commit();//如果没有异常，就提交事务
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        }
        catch(Exception $e){
            //echo $sql;
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        $this->Bindxiajia($order_id);
        return $order_detail_id;
    }

    //已支付和未支付
    public function getOrderAccount($order_id){
        $sql = "SELECT  * FROM `app_order_account` WHERE  `order_id`=".$order_id;
        return $this->db()->getRow($sql);
    }
	
	public function getGoodsPrice($order_id){
          $sql = "SELECT * FROM app_order_details WHERE id IN (SELECT order_goods_id FROM app_return_goods WHERE `order_id` = {$order_id} and return_by=1 AND check_status >=4)";
     
       $detail = $this->db()->getAll($sql);
      
        return $detail;  
    }

     public function check($order_id){ 
      $sql = "SELECT 1 FROM app_order_details WHERE id IN (SELECT order_goods_id FROM app_return_goods WHERE `order_id` = {$order_id} AND check_status >=4) and is_return = 1
	  union all
	  SELECT 1 FROM app_return_goods WHERE `order_id` = {$order_id} AND check_status >=4 and order_goods_id = 0";
      	$data = $this->db()->getAll($sql);
        
        if(empty($data)){
            return 0;
        }  else {
           return 1; 
        }
     
    }
    
    public function getReturnGoodsfavor($order_id){
         $sql = "SELECT SUM(favorable_price) AS t_favorable_price FROM app_order_details WHERE id IN (SELECT order_goods_id FROM app_return_goods WHERE `order_id` = {$order_id} AND check_status >=4) AND favorable_status =3";
         return $this->db()->getOne($sql);
    }

    //插入订单日志
    public function addOrderAction($newdo) {
        if(empty($newdo)){
            return false;
        }
        $sql = $this->insertSql($newdo,"app_order_action");
        return $this->db()->query($sql);
    }

    //更新订单日志
    public function updateOrderAction($action_data) {
        if(empty($action_data)){
            return false;
        }
        $sql = "UPDATE `app_order_action` SET `remark`='{$action_data['remark']}' WHERE `action_id`={$action_data['action_id']}";
        return $this->db()->query($sql);
    }

    //更新跟单人
    public function updateOrderGenDanAction($ids,$genzong) {
        if(empty($ids)){
            return false;
        }
        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            foreach ($ids as $key => $id) {
                $sql = "UPDATE `base_order_info` SET `genzong`='$genzong' WHERE `id`=$id";
                $pdo->query($sql);
            }
            //$pdo->query('');//暂时报错
        }catch(Exception $e){//捕获异常
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return true;
    }


    public function getOrderActionById($id) {
        $sql = "select * from `app_order_action` where `action_id`=$id";
        return $this->db()->getRow($sql);
    }

    //取用户
    public function getMember_Info_userId($user_id){
        if(!empty($user_id)){
            $keys[] ='member_id';
            $vals[] =$user_id;
        }else{
            return false;
        }

        $ret = ApiModel::sale_member_api($keys, $vals, 'GetMemberByMember_id');
        return $ret;
    }
    /**
     * 查询预约客户
     * @param unknown $member_id
     */
    public function getMemberByMemberId($member_id){
        $sql = "select * from front.base_member_info where member_id={$member_id}";
        return $this->db()->getRow($sql);
    }

   public function getorderAddresinfo($order_id){
       $sql = "SELECT * FROM app_order_address WHERE order_id=".$order_id;
       return $this->db()->getRow($sql);
   }

     /*
    * 通过order_id查询地址
   */
   public function getAddressByid($order_id){

    $sql = "SELECT `id`,`address`,`express_id` FROM `app_order_address` ";
    $str = '';
    if(isset($order_id) && !empty($order_id)){
        $str .=" `order_id`=".$order_id." AND";
    }
    if($str)
    {
        $str = rtrim($str,"AND ");//这个空格很重要
        $sql .=" WHERE ".$str;
    }

    $sql .= " ORDER BY `id` DESC";
    // echo $sql;exit;
    $data = $this->db()->getAll($sql);
    return $data;
   }
   
   
   public function getAddressInfo($id) {
       $sql = "select `address` from `app_order_address` where `id`=$id";
       return $this->db()->getOne($sql);
   }

    /*
    * 通过order_id查询发票信息
   */
   public function getInvoiceByid($order_id){

    $sql = "SELECT * FROM `app_order_invoice` ";
    $str = '';
    if(isset($order_id) && !empty($order_id)){
        $str .=" `order_id`=".$order_id." AND";
    }
    if($str)
    {
        $str = rtrim($str,"AND ");//这个空格很重要
        $sql .=" WHERE ".$str;
    }

    $sql .= " ORDER BY `id` DESC";
    // echo $sql;exit;
    $data = $this->db()->getRow($sql);
    return $data;
   }

    public function getOrderInfoAccount($order_sn){
        $sql = "SELECT bi.*,aoc.order_amount,aoc.money_paid,aoc.money_unpaid,aoc.shipping_fee,aoc.goods_amount FROM `base_order_info` as `bi` LEFT JOIN `app_order_account` as `aoc` ON `bi`.`id`=`aoc`.`order_id` WHERE `bi`.`order_sn`='$order_sn'";
        /*echo $sql;
        exit;*/
        return $this->db()->getRow($sql);
    }

    public function getOrderInvoiceByOrderSn($order_sn){
            $sql = "SELECT aoi.* FROM `base_order_info` as boi, `app_order_invoice` as aoi WHERE boi.id=aoi.order_id AND aoi.is_invoice=1 and boi.order_sn='$order_sn'";
                return $this->db()->getAll($sql);
        }

    //淘宝订单入库
    public function makeTaobaoOrder($all_data)
	{
        //添加订单信息
        $order_data = $all_data['order'];
        if(empty($order_data)){
            return false;
        }

        //总的价格
        $money_data = $all_data['money'];
        if(empty($money_data)){
            return false;
        }

        //发票
        $invoice_data = $all_data['invoice'];
        if(empty($invoice_data)){
            return false;
        }
        $address_data = $all_data['address'];
        if(empty($address_data)){
            return false;
        }
        //商品列表
        $goods_data = $all_data['goods_list'];
        if(empty($goods_data)){
            return false;
        }

        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
			if(isset($order_data['user_id']) && !empty($order_data['user_id']))
			{
				$order_data['user_id']=intval($order_data['user_id']);
			}else{
				$order_data['user_id']=0;
			}
			$order_data['out_company'] = 0;
            if(empty($order_data['is_real_invoice']) && !empty($order_data['department_id'])){                
                $sql = "SELECT channel_class FROM cuteframe.`sales_channels` WHERE id={$order_data['department_id']}";
                $sales_channels = $this->db()->getOne($sql);
                if($sales_channels == 2){
                    $order_data['is_real_invoice']=0;
                    $sql = "select id from cuteframe.company where company_sn='5A'";
                    $order_data['out_company'] = $this->db()->getOne($sql);
                }
            }
            $order_field = " `order_sn`,`consignee`,`mobile`, `user_id`, `order_status`, `order_pay_status`,  `customer_source_id`, `department_id`, `create_time`, `create_user`,  `modify_time`, `order_remark`,  `is_delete`,`send_good_status`,`buchan_status`,`is_zp`,`is_xianhuo`,`delivery_status`,`order_pay_type`,`referer`,`is_real_invoice`,`out_company`";
            $order_value = "'".$order_data['order_sn']."' ,'".$order_data['consignee']."' ,'".$order_data['mobile']."' ,".$order_data['user_id']." ,".$order_data['order_status']." ,".$order_data['order_pay_status'].",".$order_data['customer_source_id']." ,".$order_data['department_id']." , '".$order_data['create_time']."' , '".$order_data['create_user']."', '".$order_data['modify_time']."', '".$order_data['order_remark']."', '".$order_data['is_delete']."',1,1,".$order_data['is_zp'].",".$order_data['is_xianhuo'].",".$order_data['delivery_status'].",".$order_data['order_pay_type'].",'外部订单','".$order_data['is_real_invoice']."',{$order_data['out_company']}";
            $sql_order = "INSERT INTO `base_order_info` (" . $order_field . ") VALUES (". $order_value .")";
            $pdo->query($sql_order);
            $order_id = $pdo->lastInsertId();
            $money_field = " `order_id`, `order_amount`, `money_paid`,`money_unpaid`,`favorable_price`,`goods_amount`,`shipping_fee`";
            $money_value = "".$order_id." ,".$money_data['order_amount']." ,".$money_data['money_paid']." ,".$money_data['money_unpaid']." ,".$money_data['favorable_price']." ,".$money_data['goods_amount']." ,".$money_data['shipping_fee'];
            $sql_money = "INSERT INTO `app_order_account` (" . $money_field . ") VALUES (". $money_value .")";
            $pdo->query($sql_money);
            //echo $sql_money;

            
            //$invoice_field = " `order_id`, `is_invoice`, `invoice_amount`,`create_time`,`invoice_title`,`invoice_type`,`invoice_status`,`title_type`";
            //$invoice_value = "".$order_id." ,".$invoice_data['is_invoice']." ,".$invoice_data['invoice_amount']." , '".$invoice_data['create_time']."','".$invoice_data['invoice_title']."','";
            //$sql_invoice = "INSERT INTO `app_order_invoice` (" . $invoice_field . ") VALUES (". $invoice_value .")";
            $invoice_fields = array('order_id', 'is_invoice', 'invoice_amount','create_time','invoice_title','invoice_type','invoice_status','title_type','invoice_address','invoice_email','taxpayer_sn');
            foreach ($invoice_data as $key=>$vo){
                if(!in_array($key,$invoice_fields)){
                    unset($invoice_data[$key]);
                }
            }
            $invoice_data['order_id'] = empty($invoice_data['order_id'])?$order_id:$invoice_data['order_id'];
            $sql_invoice = $this->insertSql($invoice_data,"app_order_invoice");
            $pdo->query($sql_invoice);

            $address_data['freight_no'] = isset($address_data['freight_no'])?$address_data['freight_no']:'';
            $address_field = " `order_id`, `consignee`,`tel`,`address`,`distribution_type`,`express_id`,`freight_no`,`country_id`,`province_id`,`city_id`,`regional_id`,`shop_type`,`shop_name`,`email`,`zipcode`,`goods_id`";
            $address_value = "".$order_id." ,'".$address_data['consignee']."' ,'".$address_data['tel']."' , '".$address_data['address']."','".$address_data['distribution_type']."','".$address_data['express_id']."','".$address_data['freight_no']."' ,'".$address_data['country_id']."','".$address_data['province_id']."','".$address_data['city_id']."','".$address_data['regional_id']."','".$address_data['shop_type']."','".$address_data['shop_name']."','".$address_data['email']."','".$address_data['zipcode']."','".$address_data['goods_id']."'";
            $sql_address = "INSERT INTO `app_order_address` (" . $address_field . ") VALUES (". $address_value .")";
            $pdo->query($sql_address);
            $dep_name = $order_data['department_id']==2?'淘宝销售部':'京东销售部';
            //订单明细
            $goods_field = "`order_id`,`goods_id`,`goods_sn`,`ext_goods_sn`,`goods_name`,`goods_price`,`goods_count`,`create_time`,`create_user`,`modify_time`,`details_status`,`is_stock_goods`,`details_remark`,`cart`,`clarity`,`color`,`zhengshuhao`,`caizhi`,`jinse`,`jinzhong`,`zhiquan`,`kezi`,`face_work`,`xiangqian`,`send_good_status`,`buchan_status`,`goods_type`,`favorable_price`,`favorable_status`,`is_zp`,`is_finance`,`zhushi_num`,`cert`,`xiangkou`";
            //$goods_value='';
            foreach($goods_data as $key=>$goods){
                $goods_value= " (".$order_id." ,'".$goods['goods_id']."' ,'".$goods['goods_sn']."' ,'".$goods['ext_goods_sn']."' ,'".$goods['goods_name']."' ,".$goods['goods_price']." ,".$goods['goods_count']." , '".$goods['create_time']."' , '".$goods['create_user']."', '".$goods['modify_time']."', ".$goods['details_status'].", ".$goods['is_stock_goods'].", '".$goods['details_remark']."','".$goods['cart']."','".$goods['clarity']."','".$goods['color']."','".$goods['zhengshuhao']."','".$goods['caizhi']."','".$goods['jinse']."','".$goods['jinzhong']."','".$goods['zhiquan']."','".$goods['kezi']."','".$goods['face_work']."','".$goods['xiangqian']."',1,1,'".$goods['goods_type']."',".$goods['favorable_price'].",".$goods['favorable_status'].",".$goods['is_zp'].",".$goods['is_finance'].",{$goods['zhushi_num']},'{$goods['cert']}','{$goods['xiangkou']}')"; 

                $sql_goods ="INSERT INTO `app_order_details` (" . $goods_field . ") VALUES $goods_value";
                $pdo->query($sql_goods);

                $id = $pdo->lastInsertId();

                //写入采购订单关联表
                if($goods['purchase_id'] != '' && $goods['already_num']>0){
                    $str_value = '';
                    for ($i=0; $i <$goods['already_num'] ; $i++) { 
                        $str_value .="('".$goods['purchase_id']."','".$id."','".$order_data['order_sn']."','".$dep_name."'),"; 
                    }
                    $sql = trim("insert into purchase_order_info (`purchase_id`,`detail_id`,`order_sn`,`dep_name`) VALUES $str_value",',');
                    //echo $sql;die;
                    $pdo->query($sql);
                    
                }
            }
            //$sql_goods =rtrim("INSERT INTO `app_order_details` (" . $goods_field . ") VALUES $goods_value",',');
            //$pdo->query($sql_goods);
            //echo $sql_goods;
            /*
            //赠品处理
            if($all_data['gift']){
                $all_data['gift']['order_id']=$order_id;
                $field = implode('`,`',array_keys($all_data['gift']));
                $valuse = implode("','",array_values($all_data['gift']));
                $sql = "insert into `rel_gift_order` (`$field`) VALUES('$valuse')";
                $pdo->query($sql);
            }*/
        }
        catch(Exception $e){//捕获异常
            $error = var_export($e,true);
            file_put_contents('taobao.wangshuai.txt',$error,FILE_APPEND);
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        //如果没有异常，就提交事务
        $pdo->commit();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        $sql="SELECT `id` FROM `app_order_details` WHERE `order_id`=$order_id";
        $rd = $this->db()->getAll($sql);
        $rd = array_column($rd,'id');
        $rel_field="`order_id`,`out_order_sn`,`goods_detail_id`";
        foreach($rd as $k=>$v){
            $sql_rel ="INSERT INTO `rel_out_order` (" . $rel_field . ") VALUES ('".$order_id."','".$order_data['out_order_sn']."',".$v.")";
            $this->db()->query($sql_rel);
        }
        //绑定和下架
       $res = $this->Bindxiajia($order_id);
       return $order_id;
    }

    //检查外部单号是否存在
    public function checkOrderByWhere($out_order_sn) {
        $sql = "SELECT `a`.`id`,`a`.`order_sn` FROM ".$this->table()." AS `a`, `rel_out_order` AS `b` WHERE `a`.`id`=`b`.`order_id` AND b.out_order_sn ='".$out_order_sn."'  AND a.order_status IN (1,2,5)";
         return $this->db()->getRow($sql);
    }

    //插入外部单号
    public function addRelOutOrder($data) {
        $field = " `order_id`, `out_order_sn`,`goods_detail_id`";
        $value = "".$data['order_id']." ,'".$data['out_order_sn']."','".$data['goods_detail_id']."'";
        $sql = "INSERT INTO `rel_out_order` (" . $field . ") VALUES (". $value .")";
        return $this->db()->query($sql);
    }

    //订单金额信息
    public function updateOrderAccount($data){
        if(!isset($data['order_id'])){
            return false;
        }
        $order_id = $data['order_id'];
        unset($data['order_id']);
        $sqlstr='';
        foreach($data as $key=>$val){
            $sqlstr.= $key."='".$val."',";
        }
        $sqlstr=trim($sqlstr,',');
        $sql= "UPDATE `app_order_account` SET ".$sqlstr." WHERE `order_id`=$order_id";
        return $this->db()->query($sql);
    }

    //更新发票
    public function updateOrderInvoice($data) {
        $order_id = $data['order_id'];
        $sql= "UPDATE `app_order_invoice` SET `is_invoice`=".$data['is_invoice'].",  `invoice_amount`=".$data['invoice_amount']." WHERE `order_id`=$order_id";
        return $this->db()->query($sql);
    }

    //更新发票
    public function updateOrderInvoiceByOrder($order_id,$inv) {
        $out_company = 0;
        if(empty($inv)){
            $sql = "select department_id from `base_order_info` where id={$order_id}";
            $department_id = $this->db()->getOne($sql);
            if(!empty($department_id)){
                $sql = "SELECT channel_class FROM `cuteframe`.`sales_channels` WHERE id={$department_id}";
                $sales_channels = $this->db()->getOne($sql);
                if($sales_channels == 2){
                    $sql = "select id from cuteframe.company where company_sn='5A'";
                    $out_company = $this->db()->getOne($sql);
                }
            }
        }
        $sql= "UPDATE `base_order_info` SET `is_real_invoice` = {$inv},out_company={$out_company}  WHERE `id`={$order_id}";
        return $this->db()->query($sql);
    }
    //修改批量发票金额问题
    public function updateBatchOrderInvoice($data) {
        $order_id = $data['order_id'];
        $sql= "UPDATE `app_order_invoice` SET `is_invoice`=".$data['is_invoice'].",  `invoice_amount`=".$data['invoice_amount'].", `is_invoice`=".$data['is_invoice'].",`invoice_title`='".$data['invoice_title']."', `taxpayer_sn`='".$data['taxpayer_sn']."' WHERE `order_id`=$order_id";
        return $this->db()->query($sql);
    }

    public function addOderDetail($goods){
        $order_id = $goods['order_id'];
        $field = "`order_id`,`goods_id`,`goods_sn`,`ext_goods_sn`,`goods_name`,`goods_price`,`goods_count`,`create_time`,`create_user`,`modify_time`,`details_status`,`is_stock_goods`,`details_remark`,`cut`,`cart`,`clarity`,`color`,`zhengshuhao`,`caizhi`,`jinse`,`jinzhong`,`zhiquan`,`kezi`,`face_work`,`xiangqian`,`send_good_status`,`buchan_status`,`goods_type`,`cat_type`,`product_type`,`is_zp`,`is_finance`,`favorable_price`,`favorable_status`,`qiban_type`,`xiangkou`,`ds_xiangci`,`pinhao`,`dia_type`,`zhushi_num`,`cert`";
        $value = " ".$order_id." ,'".$goods['goods_id']."' ,'".$goods['goods_sn']."' ,'".$goods['ext_goods_sn']."' ,'".$goods['goods_name']."' ,".$goods['goods_price']." ,".$goods['goods_count']." , '".$goods['create_time']."' , '".$goods['create_user']."', '".$goods['modify_time']."', ".$goods['details_status'].", ".$goods['is_stock_goods'].", '".$goods['details_remark']."','".$goods['cut']."','".$goods['cart']."','".$goods['clarity']."','".$goods['color']."','".$goods['zhengshuhao']."','".$goods['caizhi']."','".$goods['jinse']."','".$goods['jinzhong']."','".$goods['zhiquan']."','".$goods['kezi']."','".$goods['face_work']."','".$goods['xiangqian']."',1,1,'".$goods['goods_type']."','".$goods['cat_type']."','".$goods['product_type']."','".$goods['is_zp']."','".$goods['is_finance']."','".$goods['favorable_price']."','".$goods['favorable_status']."','".$goods['qiban_type']."','".$goods['xiangkou']."','".$goods['ds_xiangci']."','".$goods['pinhao']."','".$goods['dia_type']."','".$goods['zhushi_num']."','".$goods['cert']."'";
        $sql = "INSERT INTO `app_order_details` (" . $field . ") VALUES (". $value .")";
        $this->db()->query($sql);
        return $this->db()->db()->lastInsertId();
    }

    //添加商品
    public function addNewOrderDetail($order_id,$data){
        foreach ($data as $goods){
        	$goods['policy_id']      = isset($goods['policy_id'])?$goods['policy_id']:0;
        	$goods['is_peishi']      = isset($goods['is_peishi'])?$goods['is_peishi']:0;
        	$goods['details_remark'] = isset($goods['details_remark'])?$goods['details_remark']:'';
			$goods['qiban_type']     = isset($goods['qiban_type'])?$goods['qiban_type']: 2 ;
			$goods['cert']           = isset($goods['cert'])?$goods['cert']: '' ;
			$goods['tuo_type']       = isset($goods['tuo_type'])?$goods['tuo_type']: '' ;
			$goods['is_cpdz']       = isset($goods['is_cpdz'])?$goods['is_cpdz']: 0 ;
            $field = "`order_id`,`goods_id`,`goods_sn`,`ext_goods_sn`,`goods_name`,`goods_price`,`favorable_price`,`goods_count`,`create_time`,`create_user`,`modify_time`,`is_stock_goods`,`cut`,`cart`,`zhushi_num`,`clarity`,`color`,`cert`,`zhengshuhao`,`caizhi`,`jinse`,`jinzhong`,`zhiquan`,`kezi`,`face_work`,`xiangqian`,`send_good_status`,`buchan_status`,`goods_type`,`cat_type`,`product_type`,`kuan_sn`,`xiangkou`,`policy_id`,`is_peishi`,`is_finance`,`details_remark`,`qiban_type`,`dia_type`,tuo_type,is_cpdz";
            $value = " ".$order_id." ,'".$goods['goods_id']."' ,'".$goods['goods_sn']."' ,'".$goods['ext_goods_sn']."' ,'".$goods['goods_name']."' ,".$goods['goods_price']."  ,0 ,".$goods['goods_count']." , '".$goods['create_time']."' , '".$goods['create_user']."', '".$goods['modify_time']."',  ".$goods['is_stock_goods'].",'".$goods['cut']."','".$goods['cart']."','".$goods['zhushi_num']."','".$goods['clarity']."','".$goods['color']."','".$goods['cert']."','".$goods['zhengshuhao']."','".$goods['caizhi']."','".$goods['jinse']."','".$goods['jinzhong']."','".$goods['zhiquan']."','".$goods['kezi']."','".$goods['face_work']."','".$goods['xiangqian']."',1,1,'".$goods['goods_type']."',".$goods['cat_type'].",".$goods['product_type'].",'".$goods['kuan_sn']."','".$goods['xiangkou']."',".$goods['policy_id'].",'".$goods['is_peishi']."','2','".$goods['details_remark']."','".$goods['qiban_type']."',".$goods['dia_type'].",'{$goods['tuo_type']}',{$goods['is_cpdz']}";
            $sql = "INSERT INTO `app_order_details` (" . $field . ") VALUES (". $value .")";
            $res = $this->db()->query($sql);
        }
        return $res;
    }

	/**
     * 订单金额数据统计
     * @param type $order_id
     * @return type
     */
    public function getOrderPriceInfo($order_id) {
        $sql = "SELECT `oa`.`order_id`,`oa`.`coupon_price`,`oa`.`shipping_fee`,`oi`.`order_sn`,oa.insure_fee,oa.pay_fee,oa.pack_fee,oa.card_fee,oa.real_return_price,SUM(`od`.`goods_price`) AS `goods_amount`,SUM(if(`od`.`favorable_status`=3,`od`.`favorable_price`,0)) AS `favorable_price`,`oa`.`money_paid`,`oa`.`order_amount`,`oa`.`money_unpaid` FROM `base_order_info` AS `oi` , `app_order_details` AS `od` , `app_order_account` AS `oa` WHERE `oi`.`id`=`od`.`order_id` AND `oi`.`id`=`oa`.`order_id` AND `oi`.`id`=$order_id";
        return $this->db()->getRow($sql);
    }

    /**
     * 未审核订单金额数据统计
     * @param type $order_id
     * @return type
     */
    public function getDetailPriceInfo($order_id) {
        //$sql = "SELECT SUM(`od`.`favorable_price`) AS `favorable_price`,`oa`.`order_amount`,`oa`.`goods_amount` FROM `app_order_details` AS `od` , `app_order_account` AS `oa` WHERE `od`.`order_id`=`oa`.`order_id` AND `od`.`order_id`=$order_id AND `od`.`favorable_status` = 3";
        //$sql = "SELECT SUM(`od`.favorable_price) AS `favorable_price`,`oa`.`order_amount`,`oa`.`goods_amount` FROM `app_order_account` AS `oa` LEFT JOIN `app_order_details` AS `od` ON `oa`.`order_id` = `od`.`order_id` AND `od`.`favorable_status` = 3 AND `oa`.`order_id` = $order_id";
        $sql = "SELECT SUM(`favorable_price`) AS `favorable_price` FROM `app_order_details` WHERE `favorable_status` = 3 AND `order_id` = $order_id";
        return $this->db()->getRow($sql);
    }

    //通过接口查询查看凭据是否存在
    public function getPaySnExt($attach_sn){
        $ret = ApiFinanceModel::getPaySnExt($attach_sn);
        return $ret;
    }

    //获取订单金额参数和订单基本信息
    public function getAccountInfo($order_sn){
        if(empty($order_sn)){
            return false;
        }
        $sql = "select `boi`.*,aoa.* from `app_order_account` as `aoa` LEFT JOIN `base_order_info` as `boi` ON `boi`.`id`=`aoa`.`order_id` WHERE `boi`.`order_sn`='".$order_sn."'";
        $res = $this->db()->getRow($sql);
        return $res;
    }


    public function getAccountInfoT($order_sn){
        if(empty($order_sn)){
            return false;
        }
        $sql = "select `boi`.*,aoa.* from `app_order_account` as `aoa` LEFT JOIN `base_order_info` as `boi` ON `boi`.`id`=`aoa`.`order_id` WHERE `boi`.`order_sn`='".$order_sn."'";
        $res = $this->db()->getRow($sql);
        //调取会员接口取出该会员的名称
        $mem = new ApiMemberModel();
        $ret = $mem->GetMemberByMember_id($res['user_id']);
        if($ret['error']>0){
            return false;
        }
        $res['user_name']=$ret['data']['member_name'];
        return $res;
    }

    public function cerateOrderPayAction($info){
        $res = ApiFinanceModel::cerateOrderPayAction($info);
        return $res;
    }

    public function updateOutOrder($real_payment,$order_id){
        if(empty($order_id)){
            return false;
        }
        $sql = "UPDATE `app_order_account` SET `money_paid`=`money_paid`+$real_payment, `money_unpaid` = `money_unpaid` - $real_payment WHERE `order_id` = '".$order_id."'";
        $res = $this->db()->query($sql);
        //$nsql = "UPDATE `app_order_invoice` set `invoice_amount`=`invoice_amount`+$real_payment WHERE `order_id`='".$order_id."'";
        //$ra = $this->db()->query($nsql);
        if(empty($res)){
            return false;
        }
       return $this->changgestu($order_id);

    }

    //如果已付全款就把订单状态改成已付款付了一部分款就变成部分付款
    public function changgestu($order_id){
        $sql = "SELECT `order_amount`,`money_paid` FROM `app_order_account` WHERE `order_id`=$order_id";
        $res = $this->db()->getRow($sql);
        if($res['order_amount']<=$res['money_paid']){
            $dsql = "SELECT COUNT(*) FROM `app_order_details` WHERE `order_id`=$order_id AND `buchan_status`<>9 AND `buchan_status`<>11 AND is_stock_goods=0 and is_return<>1";
            $res = $this->db()->getOne($dsql);
            if($res==0){
                //如果没查到则可以吧order_pay_status=3,send_good_status=4,
                $sql = "UPDATE base_order_info SET `order_pay_status`=3,`delivery_status`=2 WHERE id=$order_id";
            }else{
                $sql = "UPDATE base_order_info SET `order_pay_status`=3 WHERE id=$order_id";
            }
            $this->db()->query($sql);
            return 3;
        }
        if(($res['order_amount']>$res['money_paid'])&&($res['money_paid']>0)){
            $sql = "UPDATE base_order_info SET `order_pay_status`=2 WHERE id=$order_id";
             $this->db()->query($sql);
            return 2;
        }
        return true;
    }
    //给仓储查看订单详情页
    public function getOrderidBysn($order_sn){
        $sql = "select `id` from ".$this->table()." where `order_sn`='".$order_sn."'";
        return $this->db()->getOne($sql);
    }

    //获取外部订单号
    public function getOurOrderSn($order_id){
        if(empty($order_id)){
            return false;
        }
        $sql = "SELECT DISTINCT`out_order_sn` FROM rel_out_order WHERE `order_id`=$order_id";
        return $this->db()->getAll($sql);
    }
    public function getOrdersnByOutsn($out_order_sn_str){                                  
        if(empty($out_order_sn_str)){
            return false;
        }
        $sql = "SELECT `boi`.`id` FROM `rel_out_order` as `ror` right JOIN `base_order_info` as `boi` on ror.order_id=boi.id WHERE `ror`.`out_order_sn` in ('".$out_order_sn_str."')";
        //echo $sql;
        return $this->db()->getAll($sql);
    }

    //
    public function changeOrderIsxianhuo($where) {

        if(!isset($where['order_id'])){
            return FALSE;
        }
        if(!isset($where['is_xianhuo'])){
            return FALSE;
        }
        $order_id = $where['order_id'];
        $is_xianhuo = $where['is_xianhuo'];
        $sql = "UPDATE `base_order_info` SET `is_xianhuo` = ".$is_xianhuo." WHERE `id`=".$order_id;

        return $this->db()->query($sql);
    }
    
    public function changeOrderIsXianhuoNew($where) {
    
        if(!isset($where['order_id'])){
            return FALSE;
        }
        
        $order_id = $where['order_id'];
        $sql = "update `base_order_info` i inner JOIN (select order_id, min(is_stock_goods) as is_xianhuo from `app_order_details` where is_return = 0 and order_id={$order_id} group by order_id) d on d.order_id = i.id
set i.is_xianhuo = d.is_xianhuo where id = ".$order_id;
    
        return $this->db()->query($sql);
    }

    //批量修改渠道部门
    public function updateOrderChannelBySn($where) {

        if(!isset($where['order_sn'])){
            return FALSE;
        }
        if(!isset($where['channel'])){
            return FALSE;
        }
        $order_sn = $where['order_sn'];
        $channel = $where['channel'];
        $sql = "UPDATE `".$this->table()."` SET `department_id` = ".$channel." WHERE `order_sn` in($order_sn)";
        
        return $this->db()->query($sql);
    }

    //批量修改订购类型
    public function updateOrderPayTypeBySn($where) {

        if(!isset($where['order_sn'])){
            return FALSE;
        }
        if(!isset($where['dinggou'])){
            return FALSE;
        }
        $order_sn = $where['order_sn'];
        $dinggou = $where['dinggou'];
        $sql = "UPDATE `".$this->table()."` SET `order_pay_type` = ".$dinggou." WHERE `order_sn` in($order_sn)";
        
        return $this->db()->query($sql);
    }

    //批量修改顾客电话
    public function updateOrderMobileBySn($where) {

        if(!isset($where['order_sn'])){
            return FALSE;
        }
        if(!isset($where['mobile'])){
            return FALSE;
        }
        $order_sn = $where['order_sn'];
        $mobile = $where['mobile'];
        $sql = "UPDATE `".$this->table()."` SET `mobile` = ".$mobile." WHERE `order_sn` in($order_sn)";
        
        return $this->db()->query($sql);
    }
	
		//批量修改客户姓名
    public function updateOrderconsigneeBySn($where) {
        if(!isset($where['order_sn'])){
            return FALSE;
        }
        if(!isset($where['consignee'])){
            return FALSE;
        }
        $order_sn = $where['order_sn'];
        $consignee = $where['consignee'];
        $sql = "UPDATE `".$this->table()."` SET `consignee` = '".$consignee."' WHERE `order_sn` in($order_sn)";
        return $this->db()->query($sql);
    }
		//批量修改顾问修改顾问姓名
	    public function updateOrderAdvisorBySn($where) {
        if(!isset($where['order_sn'])){
            return FALSE;
        }
        if(!isset($where['create_user'])){
            return FALSE;
        }
        $order_sn = $where['order_sn'];
        $create_user = $where['create_user'];
        $sql = "UPDATE `".$this->table()."` SET `create_user` = '".$create_user."' WHERE `order_sn` in($order_sn)";
		//echo $sql;
        return $this->db()->query($sql);
    }
		//批量修改客户来源
    public function updateOrderCustomerSourceBySn($where) {
        if(!isset($where['order_sn'])){
            return FALSE;
        }
        if(!isset($where['customer_source_id'])){
            return FALSE;
        }
        $order_sn = $where['order_sn'];
        $customer_source_id = $where['customer_source_id'];
        $sql = "UPDATE `".$this->table()."` SET `customer_source_id` = ".$customer_source_id." WHERE `order_sn` in($order_sn)";
        return $this->db()->query($sql);
    }
	
    public function getRelOutsn($order_sn,$outordersn,$order_source){
        //追加限制 ？
        $sql = "SELECT COUNT(*) FROM `".$this->table()."` WHERE `order_sn`='$order_sn' AND `department_id`=$order_source";
        $res = $this->db()->getOne($sql);
        if(empty($res)){
            return 1;
        }else{
            $sql = "SELECT COUNT(*) FROM  `rel_out_order` where `out_order_sn`=$outordersn";
            $res = $this->db()->getOne($sql);
            if(!empty($res)){
                return 2;
            }else{
                return true;
            }
        }
    }

    public function originalDetaile($order_sn, $orderi,$goods_list){
        $sql = "SELECT `id`,`order_pay_status`,`department_id` FROM `".$this->table()."` WHERE `order_sn`='$order_sn'";
        $res = $this->db()->getRow($sql);
        if(empty($res)){
            return false;
        }

        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
        //插入明细
            $goods_field = "`order_id`,`goods_id`,`goods_sn`,`goods_name`,`goods_price`,`goods_count`,`create_time`,`create_user`,`modify_time`,`details_status`,`is_stock_goods`,`details_remark`,`cart`,`clarity`,`color`,`zhengshuhao`,`caizhi`,`jinse`,`jinzhong`,`zhiquan`,`kezi`,`face_work`,`xiangqian`,`send_good_status`,`buchan_status`,`goods_type`,`favorable_price`,`favorable_status`,`is_zp`,`is_finance`,`dia_type`,`zhushi_num`,cert";
        $price=0;
        $dep_name = $res['department_id']==2?'淘宝销售部':'京东销售部';
        foreach($goods_list as $key=>$goods){
            $goods_value= " (".$res['id']." ,'".$goods['goods_id']."' ,'".$goods['goods_sn']."' ,'".$goods['goods_name']."' ,".$goods['goods_price']." ,".$goods['goods_count']." , '".$goods['create_time']."' , '".$goods['create_user']."', '".$goods['modify_time']."', ".$goods['details_status'].", ".$goods['is_stock_goods'].", '".$goods['details_remark']."','".$goods['cart']."','".$goods['clarity']."','".$goods['color']."','".$goods['zhengshuhao']."','".$goods['caizhi']."','".$goods['jinse']."','".$goods['jinzhong']."','".$goods['zhiquan']."','".$goods['kezi']."','".$goods['face_work']."','".$goods['xiangqian']."',1,1,'"."',".$goods['favorable_price'].",".$goods['favorable_status'].",".$goods['is_zp'].",".$goods['is_finance'].",".$goods['dia_type'].",{$goods['zhushi_num']},'{$goods['cert']}')";
            $sql_goods ="INSERT INTO `app_order_details` (" . $goods_field . ") VALUES $goods_value";
            $pdo->query($sql_goods);
            //插入关联表
            $id = $pdo->lastInsertId();
            $rel_field="`order_id`,`out_order_sn`,`goods_detail_id`";
           $sql_rel ="INSERT INTO `rel_out_order` (" . $rel_field . ") VALUES ('".$res['id']."','".$orderi['out_order_sn']."',".$id.")";
           $this->db()->query($sql_rel);
          $price=$price+$goods['goods_price']*$goods['goods_count'];

            //写入采购订单关联表
            if($goods['purchase_id'] != '' && $goods['already_num']>0){
                $str_value = '';
                for ($i=0; $i <$goods['already_num'] ; $i++) { 
                    $str_value .="(".$goods['purchase_id'].",".$id.",".$order_sn.",'".$dep_name."'),"; 
                }
                $sql = trim("insert into purchase_order_info (`purchase_id`,`detail_id`,`order_sn`,`dep_name`) VALUES $str_value",',');
                //echo $sql;die;
                $pdo->query($sql);
                
            }
        }

        //更新商品价格表
            $sql = "UPDATE `app_order_account` SET `order_amount`=`order_amount`+$orderi[order_price],`money_unpaid`=`money_unpaid`+$orderi[order_price], `favorable_price`=`favorable_price`+$orderi[favorable_price],`goods_amount`=`goods_amount`+$orderi[goods_amount] WHERE order_id=$res[id]";
            $pdo->query($sql);
            $sql="SELECT money_paid FROM app_order_account WHERE order_id=$res[id]";
            $rest =$this->db()->getOne($sql);
            //财务备案单据的处理
            if($res['order_pay_status']!=4){
                if($rest>0){
                    $sql = "UPDATE base_order_info SET `order_pay_status`=2 WHERE id=".$res['id'];
                }else{
                    $sql = "UPDATE base_order_info SET `order_pay_status`=1 WHERE id=".$res['id'];
                }
            }
            $pdo->query($sql);
            //更新发票金额
            $sql = "UPDATE `app_order_invoice` SET `invoice_amount`=`invoice_amount`+$orderi[order_price] WHERE `order_id`=".$res['id'];
            $pdo->query($sql);
    } catch(Exception $e){//捕获异常
        $pdo->rollback();//事务回滚
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return false;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return $res['id'];
    }

    public function getOrderAddinfoByOrderSn($order_sn){
        if(empty($order_sn)){
            return false;
        }
        $sql = "SELECT `ao`.*,`bo`.* FROM `base_order_info` as `bo` LEFT JOIN `app_order_address` AS `ao` ON `bo`.`id`=`ao`.`order_id` WHERE `bo`.`order_sn`='$order_sn'";
       return  $this->db()->getRow($sql);
    }

     //删除商品时更新金额信息
    public function updateOrderAccountNew($data){
        $order_id = $data['order_id'];
        $sql= "UPDATE `app_order_account` SET `order_amount`=".$data['order_amount'].",  `money_unpaid`=".$data['money_unpaid'].",`goods_amount`=".$data['goods_amount'].",`coupon_price`=".$data['coupon_price'].",`favorable_price`=".$data['favorable_price']." WHERE `order_id`=$order_id";

        return $this->db()->query($sql);
    }

	/**
	 *	通过订单id 更新订单信息
	 */
	public function updateOrderByOrderId($order_id,$updatedata){
		if(empty($order_id)){
			return false;
		}
		foreach($updatedata as $key => $data){
			$set[]=" $key = '{$data}'";
		}
		$sql="UPDATE base_order_info set ".implode(',',$set)." WHERE id=$order_id;";
		return $this->db()->query($sql);
	}

    public function getGifts($id){
        if(empty($id)){
            return false;
        }
        $sql="SELECT `gift_id`,`remark`,`gift_num` FROM `rel_gift_order` AS `rg` LEFT JOIN `base_order_info` AS `b` ON `rg`.`order_id`=`b`.`id`  WHERE `rg`.`order_id`=$id AND `b`.`create_time` < '2015-10-23 00:00:00' ";
        return  $this->db()->getRow($sql);
    }

    public function updateGifts($order_id,$udata){
        if(empty($order_id)){
            return false;
        }
        $sql="SELECT `gift_id` FROM `rel_gift_order` WHERE `order_id`=$order_id";
        $res = $this->db()->getRow($sql);

        if(empty($res)){
            $field = implode('`,`',array_keys($udata));
            $value = implode("','",array_values($udata));
            $sql = "INSERT INTO `rel_gift_order` (`$field`) VALUE ('".$value."')";
           return $this->db()->query($sql);
        }else{
            $sqlstr='';
            foreach($udata as $key=>$val){
                $sqlstr.= $key."='".$val."',";
            }
            $sqlstr = trim($sqlstr,',');
            $sql = "UPDATE `rel_gift_order` SET ".$sqlstr." WHERE order_id =$order_id";
            return $this->db()->query($sql);
        }
    }
    /*对绑定下架功能进行封装*/
    public function Bindxiajia($order_id,$data=array('bind_type'=>1,'is_sale'=>0)){
        if(empty($order_id)){
            return false;
        }

        /*
        $goodsModel = new AppOrderDetailsModel(28);
        $warehouseModel = new ApiWarehouseModel();
        $salepolicyM = new ApiSalePolicyModel();
        //$res =  $goodsModel->getGoodsByOrderId(array('order_id'=>$order_id,'is_stock_goods'=>1));
        $res =  $goodsModel->getGoodsByOrderId(array('order_id'=>$order_id));
        foreach($res as $ke=>$va){
            //绑定
            $reat = $warehouseModel->BindGoodsInfoByGoodsId(array('order_goods_id'=>$va['id'],'goods_id'=>$va['goods_id'],'bind_type'=>$data['bind_type']));
            //var_dump($reat);
            $xianhuo_detail[$ke]['is_sale'] = $data['is_sale'];
            $xianhuo_detail[$ke]['is_valid'] = 2;
			if($data['is_sale'] == 1)
			{
				$xianhuo_detail[$ke]['is_valid'] = 1;
			}
            $xianhuo_detail[$ke]['goods_id'] = $va['goods_id'];
        }*/
        if($data['bind_type']==1)
            $sql="update app_order_details d,warehouse_shipping.warehouse_goods g set g.order_goods_id=d.id where d.goods_id=g.goods_id and d.order_id='{$order_id}' and (g.order_goods_id ='' or g.order_goods_id='0')";  
        if($data['bind_type']==2)
            $sql="update app_order_details d,warehouse_shipping.warehouse_goods g set g.order_goods_id='' where convert(d.id,CHAR)=g.order_goods_id and d.order_id='{$order_id}'";  

        return $this->db()->query($sql);

        //上架or下架   新的销售政策已经不用表base_salepolicy_goods
        //if(!empty($xianhuo_detail)){
        //    $salepolicyM->UpdateAppPayDetail($xianhuo_detail);
           // var_dump($reat);
        //}
    }
	//add by zhangruiying
	public function getExportDownload($ids)
	{
		$sql="select b.id,b.order_sn,b.order_remark,a.goods_amount,a.order_amount,a.shipping_fee,a.coupon_price,a.favorable_price,a.money_paid,a.money_unpaid,address.consignee,address.address,address.country_id,address.province_id,address.city_id,address.tel,i.invoice_title,i.invoice_address from base_order_info as b left join app_order_account as a on b.id=a.order_id left join app_order_address as address on address.order_id=b.id left join app_order_invoice as i on i.order_id=b.id where b.order_sn in($ids)";
		$res = $this->db()->getAll($sql);
		return $res;

	}
	public function getDetialByOrderId($ids)
	{
		//`id`, `order_id`, `goods_id`, `goods_sn`, `goods_name`, `goods_price`, `favorable_price`, `goods_count`, `create_time`, `modify_time`, `create_user`, `details_status`, `send_good_status`, `buchan_status`, `is_stock_goods`, `is_return`, `details_remark`, `cart`, `cut`, `clarity`, `color`, `zhengshuhao`, `caizhi`, `jinse`, `jinzhong`, `zhiquan`, `kezi`, `face_work`, `xiangqian`, `goods_type`, `favorable_status`, `cat_type`, `product_type`, `kuan_sn`) VALUES

		$sql="select goods_id,goods_name,goods_price,favorable_price,goods_count,details_remark,cart,cut,zhiquan,clarity,`color`,`zhengshuhao`,`caizhi`,`jinse`,`jinzhong`,`kezi`,`face_work`,`xiangqian`,cat_type,goods_type,product_type,kuan_sn,order_id,is_return from app_order_details where order_id in(select id from base_order_info where order_sn in($ids))";
		$res = $this->db()->getAll($sql);
		$arr=array();
		if($res)
		{
			foreach($res as $key=>$v)
			{
				$v['attr']['cart']=$v['cart'];
				$v['attr']['cut']=$v['cut'];
				$v['attr']['clarity']=$v['clarity'];
				$v['attr']['color']=$v['color'];
				$v['attr']['zhengshuhao']=$v['zhengshuhao'];
				$v['attr']['caizhi']=$v['caizhi'];
				$v['attr']['jinse']=$v['jinse'];
				$v['attr']['jinzhong']=$v['jinzhong'];
				$v['attr']['kezi']=$v['kezi'];
				$v['attr']['face_work']=$v['face_work'];
				$v['attr']['xiangqian']=$v['xiangqian'];
				$v['attr']['cat_type']=$v['cat_type'];
				$v['attr']['goods_type']=$v['goods_type'];
				$v['attr']['product_type']=$v['product_type'];
				$v['attr']['kuan_sn']=$v['kuan_sn'];
				$v['total']=($v['goods_price']-$v['favorable_price']) * $v['goods_count'];
				$arr[$v['order_id']][]=$v;
			}

		}
		//var_dump($arr);
		//exit;


		return $arr;

	}
	function changeOrderStatus($ids)
	{
		$sql="update base_order_info set is_print_tihuo=1 where order_sn in($ids)";
		$res = $this->db()->query($sql);
		if($res!=false)
		{
			//添加操作日志
			$sql="select id,order_status,order_pay_status,send_good_status from base_order_info where order_sn in($ids)";
			$list = $this->db()->getAll($sql);
			if($list)
			{
				foreach($list as $r)
				{
					$array=array(
						'order_id'=>$r['id'],
						'order_status'=>$r['order_status'],
						'shipping_status'=>$r['send_good_status'],
						'pay_status'=>$r['order_pay_status'],
						'create_time'=>date('Y-m-d H:i:s'),
						'create_user'=>Auth::$userName,
						'remark'=>'打印订单'
					);
					$this->addOrderAction($array);
				}
			}

		}
		return $res;

	}
	//add end
    
    /**
     * 统计订单类型和订单商品不统一
     * @param type $page
     * @return type
     */
    public function getTongjiInfo($page) {
        $sql = "SELECT distinct `oi`.`id`,`oi`.`order_sn`,`oi`.`is_xianhuo` FROM `base_order_info` as `oi`,`app_order_details` as `od` WHERE `oi`.`id`=`od`.`order_id` AND ((`oi`.`is_xianhuo`=1 AND `od`.`is_stock_goods`=0) OR (`oi`.`is_xianhuo`=0 AND `od`.`is_stock_goods`=1))";
        $data = $this->db()->getPageList($sql,array(),$page, 10,false);
		return $data;
    }

    public function GetOut($order_id){
        $sql="select out_order_sn from rel_out_order WHERE order_id=".$order_id;
        return  $this->db()->getOne($sql);
    }

    public function InsertOutSn($out_sn,$order_id){
        $sql="select id,order_id,out_order_sn,goods_detail_id from rel_out_order where out_order_sn='".$out_sn."'";
        $rea = $this->db()->getRow($sql);
        if(!empty($rea)){
            return true;
        }
        $sql="select id,order_id,out_order_sn,goods_detail_id  from rel_out_order WHERE order_id=".$order_id;
        $rea = $this->db()->getRow($sql);
        $sql="insert into rel_out_order (order_id,out_order_sn,goods_detail_id) VALUE ($order_id,'$out_sn',$rea[goods_detail_id])";
        return $this->db()->query($sql);
    }

    public function CopyOrderInfo($order_ido){
        if(empty($order_ido)){
            return false;
        }
        $order_sn =$this->getOrderSn();
        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            $sql="select * from ".$this->table()." where id=".$order_ido;
            $order_info = $this->db()->getRow($sql);
            
			$asql = "select * from app_order_address WHERE order_id=".$order_ido;
            $address_info = $this->db()->getRow($asql);

            $order_info['apply_return']='1';
            $order_infos=$order_info;
            unset($order_infos['id']);
            unset($order_infos['check_time']);
            unset($order_infos['effect_date']);
            unset($order_infos['pay_date']);
			unset($order_infos['shipfreight_time']);
            $order_infos['order_sn']=$order_sn;
            $order_infos['order_status']=1;
            $order_infos['order_pay_status']=1;
            $order_infos['delivery_status']=1;
            $order_infos['send_good_status']=1;
            $order_infos['buchan_status']=1;
            $order_infos['create_time']=date('Y-m-d H:i:s');
            $order_infos['create_user']=$_SESSION['userName'];
            $order_infos['check_user']='';
            $order_infos['genzong']='';
            $order_infos['order_remark']='';
            $order_infos['referer']='补发订单';
            $order_infos['is_xianhuo']=1;
            $order_infos['is_print_tihuo']=0;
            $order_infos['recommended']='';
            $f= array_keys($order_infos);
            $v = array_values($order_infos);
            $f = implode("`,`",$f);
            $v = implode("','",$v);
            //复制代码
            $sql = "insert into ".$this->table()."(`$f`) value('$v')";
            $this->db()->query($sql);
            $order_id = $pdo->lastInsertId();
            $address_info['order_id']=$order_id;
            $address_info['freight_no']='';
            $address_info['express_id'] = 19;
            unset($address_info['id']);
            unset($address_info['goods_id']);
            unset($address_info['wholesale_id']);
            $af= array_keys($address_info);
            $av = array_values($address_info);
            $af = implode("`,`",$af);
            $av = implode("','",$av);
            $sql = "insert into app_order_address(`$af`,`wholesale_id`) value('$av',null)";
            $this->db()->query($sql);
            //金额问题
            $account = array(
                'order_id'=>$order_id,
                'order_amount' => 0,
                'money_paid' =>0,
                'money_unpaid' => 0,
                'shipping_fee' => 0,
                'goods_amount' => 0,
            );

            $afc= array_keys($account);
            $avc = array_values($account);
            $afc = implode("`,`",$afc);
            $avc = implode("','",$avc);
            $sql = "insert into app_order_account(`$afc`) value('$avc')";
            $this->db()->query($sql);
			
			//发票信息
			$invoice = array(
				'order_id'=>$order_id,
				'is_invoice'=>0,
				'invoice_title'=>'不需要发票',
				'invoice_status'=>1,
				'invoice_amount'=>0,
				'invoice_address'=>$address_info['address'],
				'create_time'=>date("Y-m-d H:i:s")
			);
			$vkey = array_keys($invoice);
			$vval = array_values($invoice);
			$vfileds = implode("`,`",$vkey);
            $vvalues = implode("','",$vval);
            $sql = "insert into app_order_invoice(`$vfileds`) value('$vvalues')";
            $this->db()->query($sql);
			
        }
        catch(Exception $e){//捕获异常
            $error = var_export($e,true);
            file_put_contents('bufa.txt',$error,FILE_APPEND);
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        //如果没有异常，就提交事务
        $pdo->commit();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交

        $orderActionModel = new AppOrderActionModel(27);

        //操作日志
        $ation['order_status'] = $order_info['order_status'];
        $ation['order_id'] = $order_info['id'];
        $ation['shipping_status'] = $order_info['delivery_status'];
        $ation['pay_status'] = $order_info['order_pay_status'];
        $ation['create_user'] = $_SESSION['userName'];
        $ation['create_time'] = date("Y-m-d H:i:s");
        $ation['remark'] = "该订单生成补发订单:[".$order_sn."]";
        $orderActionModel->saveData($ation, array());
        $ation['order_status'] =1;
        $ation['order_id'] = $order_id;
        $ation['shipping_status'] =1;
        $ation['pay_status'] = 1;
        $ation['create_user'] = $_SESSION['userName'];
        $ation['create_time'] = date("Y-m-d H:i:s");
        $ation['remark'] = "补发订单通过:[".$order_info['order_sn']."]生成该订单";
        $orderActionModel->saveData($ation, array());
        return $order_sn;
    }

    public function getOrderInfoaByid($order_id){
        if(empty($order_id)){
            return false;
        }
        $sql = "select `boi`.*,aoa.* from `app_order_account` as `aoa` LEFT JOIN `base_order_info` as `boi` ON `boi`.`id`=`aoa`.`order_id` WHERE `boi`.`id`='".$order_id."'";
        $res = $this->db()->getRow($sql);
        return $res;
    }

    //查看订单信息
    public function getOrderInfoNewBysn($where){
        $str = " where 1 ";
        if(!empty($where['order_sn'])){
            $str .=" AND `order_sn` = '{$where['order_sn']}'";
        }
        if(!empty($where['create_time'])){
            $str.=" AND `create_time` >= '".$where['create_time']." 00:00:00'";
        }
        if(!empty($where['create_time'])){
            $str.=" AND `create_time` <= '".$where['create_time']." 23:59:59'";
        }
        $sql = "select `id` from ".$this->table()."".$str." limit 1";
        return $this->db()->getRow($sql);
    }
    
    
    /**
     * 婚博会订单
     * @param type $where
     * @param type $page
     * @param type $pageSize
     * @param type $useCache
     * @return type
     */
    public function getHbhOrderList($where,$page,$pageSize=10,$useCache=true) {
        $str = " WHERE `referer` = '婚博会'";
        if($where['order_sn']){
            $str .= " AND `order_sn` = '{$where['order_sn']}'";
        }else{
            if($where['department']){
                $str .= " AND `department_id` = {$where['department']}";
            }
            if($where['type']){
                if($where['type']==1){
                    $str .= " AND `genzong` = ''";
                }
                if($where['type']==2){
                    $str .= " AND `genzong` != ''";
                }
            }
            if($where['goods_type']){
                if($where['goods_type']==1){
                    $str .= " AND `is_xianhuo` = 1";
                }else if($where['goods_type']==2){
                    $str .= " AND `is_xianhuo` = 0";
                }else if($where['goods_type']==4){
                    $str .= " AND `order_remark` = '%现货未取走%'";
                }
            }
            if(isset($where['create_user']) && $where['create_user']){
                $str .= " AND `create_user` = '{$where['create_user']}'";
            }
            if(isset($where['genzong']) && $where['genzong']){
                $str .= " AND `genzong` = '{$where['genzong']}'";
            }
            if(isset($where['start_time']) && $where['start_time']){
                $str .= " AND `create_time` >= '{$where['start_time']} 00:00:00'";
            }
            if(isset($where['end_time']) && $where['end_time']){
                $str .= " AND `create_time` <= '{$where['end_time']} 23:59:59'";
            }
        }
        $sql = "SELECT `id`,`order_sn`,`create_user`,`genzong` FROM `{$this->table()}` $str";
        $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        return $data;
    }
    /**
     * 导出婚博会订单
     */
    public function getHbhOrderListdownLoad($where) {
        $str = " WHERE `referer` = '婚博会'";
        if($where['order_sn']){
            $str .= " AND `order_sn` = '{$where['order_sn']}'";
        }else{
            if($where['department']){
                $str .= " AND `department_id` = {$where['department']}";
            }
            if($where['type']){
                if($where['type']==1){
                    $str .= " AND `genzong` = ''";
                }
                if($where['type']==2){
                    $str .= " AND `genzong` != ''";
                }
            }
            if($where['goods_type']){
                if($where['goods_type']==1){
                    $str .= " AND `is_xianhuo` = 1";
                }else if($where['goods_type']==2){
                    $str .= " AND `is_xianhuo` = 0";
                }else if($where['goods_type']==4){
                    $str .= " AND `order_remark` = '%现货未取走%'";
                }
            }
            if(isset($where['create_user']) && $where['create_user']){
                $str .= " AND `create_user` = '{$where['create_user']}'";
            }
            if(isset($where['genzong']) && $where['genzong']){
                $str .= " AND `genzong` = '{$where['genzong']}'";
            }
            if(isset($where['start_time']) && $where['start_time']){
                $str .= " AND `create_time` >= '{$where['start_time']} 00:00:00'";
            }
            if(isset($where['end_time']) && $where['end_time']){
                $str .= " AND `create_time` <= '{$where['end_time']} 23:59:59'";
            }
        }
        $sql = "SELECT `order_sn`,`create_user`,`genzong` FROM `{$this->table()}` $str";
        $data['data'] = $this->db()->getAll($sql);
        return $data;
    }
	/*修改唯品会订单*/
	public function UpdatewphOrderprice($new) {
        if(empty($new)){
           return false;
        }
		$sql = "UPDATE `app_order_details` SET `goods_price` = ".$new['goods_price']." WHERE id = ".$new['id']."";
		//var_dump($sql);die;
		return $this->db()->query($sql);
		
    }	
	/*删除唯品会订单商品*/
	public function DeletewphOrdergoods($id) {
        if(empty($id)){
           return false;
        }
		$sql = "DELETE FROM app_order_details WHERE id=".$id."";
		$this->db()->query($sql);
    }

	/*验证唯品会订单来源*/
	public function GetwphOrderinfo($order_sns){
		if(empty($order_sns) || !is_array($order_sns)){
			return false;
		}
		$sql = "SELECT oi.id as order_id,oi.order_sn,og.id,og.goods_id,og.details_remark,og.goods_price,oi.order_pay_status,oi.order_status,oi.customer_source_id,oi.send_good_status,oi.delivery_status from `app_order_details` as og, base_order_info as oi WHERE og.order_id = oi.id  AND oi.`order_sn` in ('".implode("','",$order_sns)."')";
		return $this->db()->getAll($sql);
	}
	
	/**/
	public function SelectwphOgprice($order_id){
		if(empty($order_id)){
			return false;
		}
		$sql = "SELECT SUM(goods_price) as goods_price from `app_order_details` WHERE order_id = ".$order_id."";
		return $this->db()->getOne($sql);
	}
	public function UpdatewphOrderAccount($info){
		if(empty($info)){
			return false;
		}
		$sql = "update app_order_account set order_amount = ".$info['order_price'].",money_unpaid =".$info['order_price'].",goods_amount = ".$info['order_price']." where order_id = '".$info['order_id']."'";
		
		return $this->db()->query($sql);
	}

    /**
     * 根据外部订单号查询BDD订单号；
     * @param type $where
     * @return array
     */
    public function selectKelaOrderByOutOrder($where)
    {
        $out_order_sn = $where['out_order_sn'];
        $sql = "select `s`.`order_sn`,`t`.`out_order_sn` from `".$this->table()."` `s` inner join `rel_out_order` `t` on `s`.`id` = `t`.`order_id` where `t`.`out_order_sn` in({$out_order_sn}) group by `s`.`order_sn`";
        $data = $this->db()->getAll($sql);
        return $data;
    }
    
    /**
     * 获取订单的退款信息
     * @param type $return_id
     * @return type
     */
    public function getReturnGoodsInfo($return_id) {
        $sql = "SELECT order_id,apply_return_amount,order_goods_id FROM `app_return_goods` WHERE `return_id`=$return_id";
        return $this->db()->getRow($sql);
    }
    
    /**
     * 删除订单的退款记录
     * @param type $order_sn
     * @return type
     */
    public function deleteReturnGoodsInfo($return_id) {
        $sql = "delete app_return_goods,app_return_check from app_return_goods inner join app_return_check on app_return_check.return_id=app_return_goods.return_id where app_return_goods.return_id=$return_id";
        return $this->db()->query($sql);
    }
    
    /**
     * 更新订单的商品退货状态改为初始状态
     * @param type $order_sn
     * @return type
     */
    public function updateOrderDetailByOrderSn($details_id) {
        $sql = "UPDATE `app_order_details` SET `is_return` = 0 WHERE `id`=$details_id";
        return $this->db()->query($sql);
    }
    
    /**
     * 更新订单实退金额
     * @param type $order_sn
     * @return type
     */
    public function modfiyAccountInfo($order_id,$price) {
        $_sql = "select * from app_order_account where order_id=$order_id";
        $return_price = $this->db()->getRow($_sql);
        $money_paid = $return_price['money_paid'] + $price;
        $order_amount = $return_price['order_amount'] + $price;
        $real_return_price = $return_price['real_return_price'] - $price;
        $goods_return_price = $return_price['goods_return_price'] - $price;
        $base_sql = "UPDATE `base_order_info` SET `apply_return`=1 WHERE `id`={$order_id}";
        $this->db()->query($base_sql);
        $sql = "UPDATE `app_order_account` as `oa` SET `oa`.`goods_return_price`=$goods_return_price,`oa`.`real_return_price`=$real_return_price,`oa`.`money_paid`={$money_paid},`oa`.`order_amount`={$order_amount} WHERE `oa`.`order_id`={$order_id}";
        return $this->db()->query($sql);
    }

    /**
     * 裸钻加入购物车，裸钻下架
     * @param type $order_sn
     * @return type
     */
    public function diaSoldOut($dia_sn)
    {
        # code...
        if($dia_sn == ''){
            return false;
        }
        $apimodel = new ApiDiamondModel();
        return $apimodel->diaSoldOut($dia_sn);
    }
    
    /*
     * 通过订单号获取用户姓名
     */
    public function getConsigneeByOrderSn($orderSn){
    	$sql ="select consignee from ".$this->table()." where order_sn = '".$orderSn."'";
    	//file_put_contents('e:/8.sql',$sql);
    	return $this->db()->getOne($sql);
    }
    
    public function selectisfinance($goods_sn) {
        $sql = "select is_xz from gift_goods where goods_number ='$goods_sn' ";
      
        return $this->db()->getOne($sql);
    }
    
    public function selectzpinfo($goods_sn) {
        $sql = "select * from gift_goods where goods_number ='$goods_sn' ";
        return $this->db()->getRow($sql);
    }
    
	
	//根据订单id获取是否有非赠品商品，如果有 订单为非赠品单,如果没有 则为赠品单
	public function updateorderiszp($orderid)
	{
		//只要有一个是非赠品 就ok
		$sql ="select * from app_order_details where order_id='".$orderid."' and is_zp=0 and is_return=0";
		$row = $this->db()->getRow($sql);
		if(empty($row))
		{
			$sql ="select * from app_order_details where order_id='".$orderid."' and is_zp=1 and is_return=0";
			//还得有一个赠品才是赠品单
			$row = $this->db()->getRow($sql);
			if(!empty($row))
			{
				//是赠品单
				$sql = "update base_order_info set is_zp = 1 where id='".$orderid."'";
			}else{
				$sql = "update base_order_info set is_zp = 0 where id='".$orderid."'";
			}
		}else{
			//如果不为空则不是赠品单
			$sql = "update base_order_info set is_zp = 0 where id='".$orderid."'";
		}
		return $this->db()->query($sql);
	}

    public function getBespokeidByBespokeSn($bespoke_sn){
        if(!empty($bespoke_sn)){
            $sql="select bespoke_id from `front`.`app_bespoke_info` where bespoke_sn = '".$bespoke_sn."';";
            return $this->db()->getOne($sql);
        }else{
            return 0;
        }
    }
    
    public function getBespokeSnByBespokeid($bespoke_id){
        if(!empty($bespoke_id)){
            $sql="select bespoke_sn from `front`.`app_bespoke_info` where bespoke_id = '".$bespoke_id."';";
            $ret = $this->db()->getOne($sql);
            if($ret){
                return $ret;
            }else{
                return '-';
            }
        }else{
            return '-';
        }
    }

    public function updatePayDateByOrderid($date = '',$order_id){
        if(empty($date) || empty($order_id)){
            return false;
        }else{
            $sql="UPDATE base_order_info set pay_date='".$date."' where id='".$order_id."' and (pay_date is null
            or pay_date = '') limit 1;";
            return $this->db()->query($sql);
        }
    }
    //检查订单商品是否已全部退货
    public function checkOrderGoodsReturnAll($order_id){
    
        //只要有一个没退货，就返回false(不能关闭)
        $sql = "select count(*) as c from app_order_details where is_return=0 and order_id={$order_id} and is_finance=2";
        $row1 = $this->db()->getRow($sql);
        if(isset($row1['c']) && $row1['c']==0){
            return true;
        }
        
        //只要有一条申请退款记录是转单（退款方式是退商品，退款类型是转单）就返回true（可以关闭）
        $sql="select count(*) as c from app_return_goods where order_id ={$order_id} and return_by=1 and return_type=1";
        $row2=$this->db()->getRow($sql);
        if(isset($row2['c']) && $row2['c']>0){
            return true;
        }
        return false;       
        /*
    	$sql = "select order_id,is_return from app_order_details where order_id={$order_id} and is_zp=0";
    	 $rows = $this->db()->getAll($sql);
    	if(!empty($rows)){
    		$order_str='(';
    		foreach ($rows as $k=>$v){
    			//只要有一个没退货，就返回false(不能关闭)
    			if($v['is_return']==0){
    				return false;
    			}
    			if($k==0){
    				$order_str.=$v['order_id'];
    			}else{
    				$order_str.=','.$v['order_id'];
    			}
    		}
    		$order_str.=')';
    		//只要有一条申请退款记录是转单（退款方式是退商品，退款类型是转单）就返回true（可以关闭）
    	    $sql="select return_id from app_return_goods where order_id in {$order_str} and return_by=1 and return_type=1";
    		$r=$this->db()->getAll($sql);
    		
    		if(!empty($r)){
    			return true;
    		}
    		
    		
    		
    	}
    	
    	
        $sql = "select sum(is_return) as 'returns',count(*) as 'total' from app_order_details where order_id={$order_id}";
        $row = $this->db()->getRow($sql);
        if(!empty($row)){
            $isReturnAll = $row['returns']!=$row['total']?false:true;
        }else{
            $isReturnAll = true;
        }
        return $isReturnAll;
        */
    }

    /**
        获取常用的时间    
    */
    public function getOrderTime($order_id)
    {
        if(empty($order_id)){
            return false;
        }
        $sql="SELECT * FROM `app_order_time` where order_id = $order_id ;";
        return $this->db()->getRow($sql);
    }
	
	//修改预约单号,增加获取预约单号
	public function getbosksn($bosksn)
	{
		$data = array();
		if(empty($bosksn))
		{
			return $data;
		}
		$sql = "select bespoke_id,bespoke_sn,department_id,customer_source_id,customer_mobile,bespoke_status, customer,remark
		from front.app_bespoke_info where bespoke_sn='".$bosksn."'";
		return $this->db()->getRow($sql);	
	}	
    // 修改预约信息
    public function updateBespokeInfo($bespoke_id, $bespokeInfo=array()){
        if (empty($bespoke_id) || empty($bespokeInfo)) return false;
        $fields = '';
        foreach ($bespokeInfo as $k=>$v) {
            if (is_numeric($v)) {
                $fields .= $k.'='.$v.',';
            } else {
                $fields .= "$k='$v',"; // 字符窜加引号
            }
        }
        $fields = rtrim($fields, ',');
        $sql = "update front.app_bespoke_info set {$fields} where bespoke_id=".$bespoke_id;
        return $this->_db->query($sql);
    }
	//批量修改预约单号
    public function updateOrderBespokeBySn($where) {

        if(!isset($where['order_sn'])){
            return FALSE;
        }
        if(!isset($where['bespoke_id'])){
            return FALSE;
        }
		if(!isset($where['customer_source_id'])){
			return FALSE;
		}
        $order_sn = $where['order_sn'];
        $bosk_id = $where['bespoke_id'];
		$customersourceid = $where['customer_source_id'];
        $sql = "UPDATE `".$this->table()."` SET `bespoke_id` = '".$bosk_id."',
		customer_source_id = '".$customersourceid."' WHERE `order_sn` in($order_sn)";
        return $this->db()->query($sql);
    }

    public function updateExpressById($order_id,$express_id)
    {
        if(empty($order_id) || empty($express_id)){
            return false;
        }
        $sql= "update app_order_address set express_id = $express_id where order_id = $order_id ";
        return $this->db()->query($sql);
    }

    public function getNameByid($id)
	{
		$sql = "select channel_name from cuteframe.sales_channels where id ={$id}";
		return $this->db()->getOne($sql);
	}
	
	//验证证书号在裸钻列表,彩钻列表,商品列表里面是否存在
	public function checkZhengshuhao($zhengshuhao)
    {
        $selfDiaModel = new SelfDiamondModel(19);
        if ($selfDiaModel->checkZhengshuhao($zhengshuhao)) {
            return true;
        }
        
        $sql="select 1 from warehouse_shipping.warehouse_goods where zhengshuhao = '$zhengshuhao'";
        if($this->db()->getOne($sql)){
            return true;
        }
        return false;
    }
    /**
     * 检查证书号与证书类型是否匹配,证书号有效 ，判断是否
     * @param unknown $zhengshuhao
     * @param unknown $cert
     */
    public function checkCertByCertId($zhengshuhao,$cert){
        $sql="select cert from front.diamond_info where cert_id = '{$zhengshuhao}'  
              union
              select cert from front.app_diamond_color where cert_id = '{$zhengshuhao}'";
        $row = $this->db()->getRow($sql);
        if(!empty($row)){
            if($row['cert']!=trim($cert)){
                return false;
            }
        }else{        
            $sql="select zhengshuleibie from warehouse_shipping.warehouse_goods where zhengshuhao = '{$zhengshuhao}'";
            $row = $this->db()->getRow($sql);
            if(!empty($row)){
                if($row['zhengshuleibie']<>$cert){
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * 取现有订单中所有录单类型
     */
    public function getReferers() {
        $sql = 'select referer from base_order_info group by referer';
        $list =  $this->db()->getAll($sql);
        return array_column($list, 'referer');
    }

    /**
     * 插入订单客诉信息；
     */
    public function ins_complaint_list($info)
    {
        # code...
        foreach ( $info as $k => $v ){
            if($v != ''){
                $str .= '`' . $k . '` = \'' . $v . '\',';
            }
        }
        $str = rtrim($str,',');
        $sql = "INSERT INTO `app_order`.`app_order_complaint` SET {$str}";
        return $this->db()->query($sql);
    }
    
    public function getGoodsStock($company_id,$goods_id){
        $sql="select goods_id,`order_goods_id` from warehouse_shipping.warehouse_goods where company_id='$company_id' and goods_id='$goods_id'";
        return $this->db()->getRow($sql);
    }

    public function getGoodsStockBygoodsid($goods_id){
        $sql="select goods_id,`order_goods_id`,`company_id` from warehouse_shipping.warehouse_goods where goods_id='$goods_id'";
        return $this->db()->getRow($sql);
    }

    public function getGoodsStockCheck($goods_id){
        $sql="select `order_goods_id` from warehouse_shipping.warehouse_goods where goods_id='$goods_id'";
        return $this->db()->getOne($sql);
    }
    
    public function serveBespoke($bespoke_sn, $username) {
    	// 将之前正在服务的预约单设置为待服务状态
    	$sqls = array(
 			"update front.app_bespoke_info set queue_status = 2 where re_status = 1 and accecipt_man = '{$username}' and queue_status = 3;",
 			"update front.app_bespoke_info set queue_status = 3, bespoke_status = 2, re_status = 1, accecipt_man = '{$username}' where bespoke_sn = '{$bespoke_sn}'",
    	);
    	return $this->db()->commit($sqls);
    }

    //获取订单商品信息
    public function getOrderInfoByOrdersn($order_sn)
    {
        $sql = "select oa.id as acc_id,oa.order_amount,oa.money_unpaid,od.id,od.order_id,od.favorable_status,od.goods_price,od.favorable_price from base_order_info oi 
inner join app_order_details od on od.order_id = oi.id 
inner join app_order_account oa on oa.order_id = oi.id
where oi.order_sn = '".$order_sn."' and od.is_zp <> 1;";
        return $this->db()->getAll($sql);
    }

    /**
     * 订单金额数据统计
     * @param type $order_id
     * @return type
     */
    public function getOrderPriceInfoByorder_sn($order_sn) {
        $sql = "SELECT `oa`.`order_id`,`oa`.`coupon_price`,`oa`.`shipping_fee`,`oi`.`order_sn`,oa.insure_fee,oa.pay_fee,oa.pack_fee,oa.card_fee,oa.real_return_price,SUM(`od`.`goods_price`) AS `goods_amount`,SUM(if(`od`.`favorable_status`=3,`od`.`favorable_price`,0)) AS `favorable_price`,`oa`.`money_paid`,`oa`.`order_amount`,`oa`.`money_unpaid` FROM `base_order_info` AS `oi` , `app_order_details` AS `od` , `app_order_account` AS `oa` WHERE `oi`.`id`=`od`.`order_id` AND `oi`.`id`=`oa`.`order_id` AND `oi`.`order_sn`='{$order_sn}'";
        return $this->db()->getRow($sql);
    }

    //修改订单金额
    public function updateOrderPrice($data, $id)
    {
        $sql = "update `app_order_account` set `order_amount` = '".$data['order_amount']."',`favorable_price` = '".$data['favorable_price']."',`money_unpaid` = '".$data['money_unpaid']."' where `id` = ".$id;
        $res = $this->db()->query($sql);
        return $res;
    }

    public function getUserBespokeRole($username){
        $sql="select dp_leader_name from cuteframe.sales_channels_person";       
        $res=$this->db()->getAll($sql);        
        if($res){
            $res=array_column($res,'dp_leader_name');
            $res=explode(',',implode(',', $res));
            if(in_array($username, $res)) 
                return '';
            else
                return 'sale';
        }else
            return 'sale';
    }

    public function getUserBespokeRoleByDepId($username,$dep_id){
        $check_info = array();
        if(!empty($dep_id)){
            $sql="select id,dp_leader_name from cuteframe.sales_channels_person where id in(".$dep_id.")";  
            $res=$this->db()->getAll($sql);
            if(!empty($res)){
                foreach ($res as $key => $val) {
                    $check_ext = false;
                    $check_user = explode(",", $val['dp_leader_name']);
                    if(!empty($check_user)) $check_ext = in_array($username, $check_user)?true:false;
                    if($check_ext) $check_info[] = $val['id'];
                }
            }
        }
        return $check_info;
    }

    //查询是否有渠道和制单人的单
    public function getCheckChannelCreateUser($data)
    {
        $sql = "select count(*) from app_order.base_order_info where department_id = ".$data['department_id']." and create_user = '".$data['create_user']."'";
        return $this->db()->getOne($sql);
    }

    //更新跟单人
    public function BatchGenZongInfo($data){
        $sql = "update app_order.base_order_info set genzong = '".$data['genzong']."' where department_id = ".$data['department_id']." and create_user = '".$data['create_user']."'";
        return $this->db()->query($sql);
    }

    public function exctrWphOrderInfo($data)
    {
        set_time_limit(0);
        $rest = array('error'=>1,'msg'=>'');
        if(empty($data)){
            return false;
        }
        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            foreach($data as $key => $val)
            {   

                $order_sn = trim($val[0]);
                $favorable_price = trim($val[1]);
                $favorable_price = str_replace(',', '', $favorable_price);
                $orderInfo = $this->getOrderInfoByOrdersn($order_sn);
                if(count($orderInfo) != '1'){
                    $error = '操作失败,事物回滚!提示：订单'.$order_sn.'中【是否赠品】为否的商品只有一件才能批量修改，如果有多件非赠品的商品，不允许上传，可以到订单界面单独修改金额';
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                    $rest['msg'] = $error;
                    return $rest;
                }
                $orderData = $orderInfo[0];
                $old_favorable_price = $orderData['favorable_status'] == 3 ?$orderData['favorable_price']:0;
                $price = $orderData['goods_price'] - $old_favorable_price;
                if($favorable_price >= $price){
                    $error = '操作失败,事物回滚!提示：订单'.$order_sn.'中商品优惠金额必须＜订单的商品价格之和';
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                    $rest['msg'] = $error;
                    return $rest;
                }
                //重新计算订单金额
                $favorable_price_par = $old_favorable_price+$favorable_price;
                //保存优惠金额
                $sql = "update app_order_details set `favorable_price` = '".$favorable_price_par."',`favorable_status` = 3 where `id` = ".$orderData['id'];
                $pdo->query($sql);
                //订单金额 = 订单金额-这次的优惠
                $orderamount = $orderData['order_amount']-$favorable_price ;
                $order_unpaid = $orderData['money_unpaid']-$favorable_price;
                $infoPrice = $this->getOrderPriceInfoByorder_sn($order_sn);
                //$favorable_price_new = $infoPrice['favorable_price'] + $favorable_price;//商品优惠
                $sql = "update `app_order_account` set `order_amount` = '".$orderamount."',`favorable_price` = '".$favorable_price_par."',`money_unpaid` = '".$order_unpaid."' where `id` = ".$orderData['acc_id'];
                $pdo->query($sql);
                $order_info = $this->getOrderInfoBySn($order_sn);
                $time = date("Y-m-d H:i:s");
                $sql = "insert into app_order_action(order_id,order_status,shipping_status,pay_status,create_user,create_time,remark) values('".$order_info['id']."','".$order_info['order_status']."','".$order_info['send_good_status']."','".$order_info['order_pay_status']."','".$_SESSION['userName']."','".$time."','唯品会订单批量修改优惠金额:￥".$favorable_price."')";
                $pdo->query($sql);
            }
        }catch(Exception $e){//捕获异常
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            $rest['msg'] = "操作失败,事物回滚!".$e->getMessage().'-'.$sql;
            return $rest;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        $rest['error'] = 0;
        return $rest;
    }
    //''''''''获取订单所有明细............//
    function getOrderAllDetails($order_sn){
        $sql="select o.id,o.order_sn,h.channel_name,o.create_user,o.create_time,o.check_user,o.check_time,case when o.order_status=1 then '待审核' when o.order_status=2 then '已审核' when o.order_status=3 then '取消' when o.order_status=4 then '关闭' when o.order_status=5 then '审核未通过' end as order_status,a.order_amount,a.money_paid,a.money_unpaid,d.goods_id,d.goods_sn,d.goods_name,d.cart,d.zhushi_num,d.color,d.clarity,d.cut,d.cert,d.zhengshuhao,d.zhiquan,d.is_stock_goods from base_order_info o left join cuteframe.sales_channels h on o.department_id=h.id left join cuteframe.customer_sources s on o.customer_source_id=s.id,app_order_details d,app_order_account a where o.id=d.order_id and o.id=a.order_id and o.order_sn='$order_sn'";
        //echo $sql;
        //exit();
        return $this->db()->getAll($sql); 
    }

    public function GetWarehouseGoodsByGoodsid($goods_id){
        $sql="select * from warehouse_shipping.warehouse_goods where goods_id='{$goods_id}'";
        return $this->db()->getRow($sql);
    }

    public function GetQiBianGoodsByQBId($qb_id){       
            $sql = "SELECT * FROM purchase.purchase_qiban_goods WHERE addtime='{$qb_id}'";
            return  $this->db()->getRow($sql);    
    }
    
    public function GetStyleXiangKouByWhere($style_sn){
        $sql="select * from front.rel_style_factory where style_sn='{$style_sn}'"; 
        //echo $sql;
        return  $this->db()->getAll($sql);
    }     

    //''''''''获取订单所有明细............//
    function getOrderAllDetailsForAuth($where){
        if(empty($where))
            return null;
        $where_str = "";
        if(!empty($where['order_sn'])){
            $where['order_sn'] = preg_replace("/[sv]+/",'',$where['order_sn']);
            $where['order_sn'] = str_replace(" ",',',$where['order_sn']);
            $where['order_sn'] = str_replace("，",',',$where['order_sn']);
            $item = explode(",",$where['order_sn']);
            if(is_array($item) && count($item)==1)
                $where_str .= " and o.order_sn='{$where['order_sn']}' ";
            if(is_array($item) && count($item)>1)
                $where_str .= " and o.order_sn in ('". implode("','",$item)  ."') ";
        }

        if(!empty($where['cert_id'])){
            $where['cert_id'] = preg_replace("/[sv]+/",'',$where['cert_id']);
            $where['cert_id'] = str_replace(" ",',',$where['cert_id']);
            $where['cert_id'] = str_replace("，",',',$where['cert_id']);
            $item = array_filter(explode(",",$where['cert_id']));
            if(is_array($item) && count($item)==1)
                $where_str .= " and d.zhengshuhao='{$where['cert_id']}' ";
            if(is_array($item) && count($item)>1)
                $where_str .= " and d.zhengshuhao in ('". implode("','",$item)  ."') ";
        }        
        $sql = "select o.id,o.order_sn,h.channel_name,o.create_user,o.create_time,o.check_user,o.check_time,case when o.order_status=1 then '待审核' when o.order_status=2 then '已审核' when o.order_status=3 then '取消' when o.order_status=4 then '关闭' when o.order_status=5 then '审核未通过' end as order_status,a.order_amount,a.money_paid,a.money_unpaid,d.goods_id,d.goods_sn,d.goods_name,d.cart,d.zhushi_num,d.color,d.clarity,d.cut,d.cert,d.zhengshuhao,d.zhiquan,d.is_stock_goods,o.hidden from base_order_info o left join cuteframe.sales_channels h on o.department_id=h.id left join cuteframe.customer_sources s on o.customer_source_id=s.id,app_order_details d,app_order_account a where o.id=d.order_id and o.id=a.order_id ";
        if(!empty($where_str))
            $sql .= $where_str ;
        else
            return null;
        //echo $sql;
        //exit();
        return $this->db()->getAll($sql); 
    }
}

