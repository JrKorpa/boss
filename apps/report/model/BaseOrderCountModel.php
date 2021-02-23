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
class BaseOrderCountModel extends Model
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
    /**	pageList，分页列表 
	 *
	 *	@url ApplicationController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{

		$sql  = "SELECT `b`.`order_amount`,`b`.`money_paid`,`b`.`money_unpaid`,`b`.`goods_return_price`,`b`.`real_return_price`,`b`.`shipping_fee`,`b`.`goods_amount`,`b`.`coupon_price`,`b`.`favorable_price`,`b`.`card_fee`,`b`.`pack_fee`,`b`.`pay_fee`,`b`.`insure_fee`,`a`.* FROM `".$this->table()."` as a,`app_order_account` as b";

		$sql .= " where `a`.`id`=`b`.`order_id`";
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
            $sql .= " AND `a`.`mobile` = ".addslashes($where['mobile']);
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
			$sql .= " AND `a`.`order_pay_status` = ".addslashes($where['order_pay_status']);
		}
		if(isset($where['pay_type']) && $where['pay_type'] != "")
		{
			$sql .= " AND `a`.`order_pay_type` = ".$where['pay_type'];
		}
		/*if($where['order_department'] != "")
		{
			$sql .= " AND `a`.`order_department` = ".addslashes($where['order_department']);
		}*/
		if(isset($where['department_id'])&&$where['department_id'] != "")
		{
			$sql .= " AND `a`.`department_id` in(".addslashes($where['department_id']).")";
		}
        if(!empty($where['start_time'])){
            $sql.=" AND `create_time` >= '".$where['start_time']." 00:00:00'";
        }
        if(!empty($where['end_time'])){
            $sql.=" AND `create_time` <= '".$where['end_time']." 23:59:59'";
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
        if(isset($where['referer']) && $where['referer']!= ""){
            $sql .= " AND `a`.`referer` = ".addslashes($where['referer']);
        }
		if(!empty($where['hbh_referer']))
		{
			if($where['hbh_referer'] == '1'){
				$sql .= " AND `a`.`referer` = '婚博会' ";
			}else{
				$sql .= " AND `a`.`referer` != '婚博会' ";
			}
		}

		if(isset($where['is_delete']))
		{
           if ($where['is_delete']==0 || $where['is_delete']==1){
               $sql .= " AND `a`.`is_delete` = ".$where['is_delete'];
          }
		}
		// add by zhangruiying添加判断变量是否存在避免报错isset($where['is_zp'])
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
        if(isset($where['close_order']))
        {
			if(empty($where['close_order'])){
	            $sql .= " AND `a`.`order_status` not in (3,4) ";
			}
        }

		if(isset($where['order_type']) && $where['order_type'] != '')
        {
            $sql .= " AND `a`.`is_xianhuo`  = ".$where['order_type']."";
        }
		$sql .= " ORDER BY `a`.`id` DESC";//echo $sql;exit;
		
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
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
        /*
        $order_sn = date('Ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),5,'1',STR_PAD_LEFT);
        return $order_sn;
        */
        switch (SYS_SCOPE){
            case 'boss':
                return date('Ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),5,'1',STR_PAD_LEFT);
            case "zhanting":
                return date('ymd').mt_rand(3,9).str_pad(mt_rand(1, 99999),5,'1',STR_PAD_LEFT);
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
        $sql = "SELECT * FROM `".$this->table()."` WHERE `id`=".$id." limit 1;";
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


            $invoice_field = " `order_id`, `is_invoice`, `invoice_amount`,`create_time`";
            $invoice_value = "".$order_id." ,".$invoice_data['is_invoice']." ,".$invoice_data['invoice_amount']." , '".$invoice_data['create_time']."' ";
            $sql_invoice = "INSERT INTO `app_order_invoice` (" . $invoice_field . ") VALUES (". $invoice_value .")";
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

			$order_data['send_good_status']=1;
			$order_data['buchan_status']=1;

			foreach($order_field as $key => $val){
				$order_value_arr[$val] = isset($order_data[$val])?str_replace("'","",$order_data[$val]):''; 
			}
			
            $sql_order = "INSERT INTO `base_order_info` (`" . implode('`,`',array_keys($order_value_arr)) . "`) VALUES ('". implode("','",array_values($order_value_arr)) ."')";


$order_field = " `order_sn`,`bespoke_id`,`consignee`,`recommended`,`mobile`,`user_id`, `order_status`, `order_pay_status`, `order_pay_type`,  `customer_source_id`, `department_id`, `create_time`, `create_user`,  `modify_time`, `order_remark`,  `is_delete`,`send_good_status`,`buchan_status`,`is_xianhuo`,`is_zp`,`delivery_status`,`referer`";
	//var_dump($order_data);die;
            $order_value = "'".$order_data['order_sn']."' ,'".$order_data['bespoke_id']."' ,'".$order_data['consignee']."' ,'".$order_data['recommended']."' ,'".$order_data['mobile']."' ,".$order_data['user_id']." ,".$order_data['order_status']." ,".$order_data['order_pay_status'].",".$order_data['order_pay_type'].",".$order_data['customer_source_id']." ,".$order_data['department_id']." , '".$order_data['create_time']."' ,'".$order_data['create_user']."', '".$order_data['modify_time']."', '".$order_data['order_remark']."', '".$order_data['is_delete']."',1,1,".$order_data['is_xianhuo'].",".$order_data['is_zp'].",".$order_data['delivery_status'].",'".$order_data['referer']."'";

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
                $address_field = " `order_id`, `consignee`,`tel`,`address`,`distribution_type`,`express_id`,`freight_no`,`country_id`,`province_id`,`city_id`,`regional_id`,`shop_type`,`shop_name`,`email`,`zipcode`,`goods_id`";
                $address_data['regional_id'] = empty($address_data['regional_id'])?0:intval($address_data['regional_id']);
		$address_value = "".$order_id." ,'".$address_data['consignee']."' ,'".$address_data['tel']."' , '".$address_data['address']."','".$address_data['distribution_type']."','".$address_data['express_id']."','".$address_data['freight_no']."' ,'".$address_data['country_id']."','".$address_data['province_id']."','".$address_data['city_id']."','".$address_data['regional_id']."','".$address_data['shop_type']."','".$address_data['shop_name']."','".$address_data['email']."','".$address_data['zipcode']."','".$address_data['goods_id']."'";
                
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
				$val['buchan_status'] = 1;
		if($val['goods_type']=='zp'){
			$val['cat_type']=0;
			$val['product_type']=0;
			$val['kuan_sn']='';
		}	
                $val_list = array("order_id","goods_id","xiangkou","goods_sn","ext_goods_sn","goods_name","goods_price","goods_count","create_time","create_user","modify_time","details_status","is_stock_goods","details_remark","cut","cart","clarity","color","zhengshuhao","caizhi","jinse","jinzhong","zhiquan","kezi","face_work","xiangqian","send_good_status","buchan_status","goods_type","cat_type","product_type","kuan_sn","favorable_price","favorable_status","policy_id");
				$keys=array();
				$vals=array();
				foreach($val_list as $k => $v){
                    $val['policy_id']= $val['policy_id']?$val['policy_id']:0;
					$keys[] = $v;
					$vals[] = isset($val[$v])?str_replace("'","",$val[$v]):'';
				}
                $sql_goods = "INSERT INTO `app_order_details` (`" . implode('`,`',$keys) . "`) VALUES ('". implode("','",$vals) ."')";

                $pdo->query($sql_goods);
                $pdo->lastInsertId();
            }

			//总的价格
            $money_data = $all_data['money'];
            if(empty($money_data)){
                return false;
            }
			if(isset($money_data['favorable_price'])){
		        $sql_money = "UPDATE `app_order_account` SET `order_amount`=".$money_data['order_amount'].",  `money_unpaid`=".$money_data['money_unpaid'].",`goods_amount`=".$money_data['goods_amount'].",`favorable_price`=".$money_data['favorable_price']." WHERE `order_id`=$order_id";
			}else{
		        $sql_money = "UPDATE `app_order_account` SET `order_amount`=".$money_data['order_amount'].",  `money_unpaid`=".$money_data['money_unpaid'].",`goods_amount`=".$money_data['goods_amount']." WHERE `order_id`=$order_id";
			}
            $pdo->query($sql_money);
            //修改发票金额
            $sql_n ="update `app_order_invoice` set `invoice_amount`='".$money_data['order_amount']."' where `order_id`=".$order_id;
            $pdo->query($sql_n);
		}
		catch(Exception $e){//捕获异常
            echo '<pre>';
            print_r($e);
            echo '</pre>';
            exit;
            
			file_put_contents('makeemptyordergoods',serialize($e));
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
    }

    //已支付和未支付
    public function getOrderAccount($order_id){
        $sql = "SELECT  * FROM `app_order_account` WHERE  `order_id`=".$order_id;
        return $this->db()->getRow($sql);
    }

    //插入订单日志
    public function addOrderAction($action_data) {
        if(empty($action_data)){
            return false;
        }
        //$action_field = " `order_id`, `order_status`, `shipping_status`, `pay_status`,`create_time`,`create_user`,`remark`";
        //$action_value = "".$order_id." ,".$action_data['order_status']." ,".$action_data['shipping_status']." ,".$action_data['pay_status'].", '".$action_data['create_time']."' , '".$action_data['create_user']."', '".$action_data['remark']."' ";
       // $sql_action = "INSERT INTO `app_order_action` (" . $action_field . ") VALUES (". $action_value .")";
        $fields = array_keys($action_data);
        $valuedata = array_values($action_data);
		//var_dump($fields,$valuedata);die;
		$field = implode('`,`', $fields);
        $value = str_repeat('?,',count($fields)-1).'?';

        $sql = "INSERT INTO `app_order_action` (`" . $field . "`) VALUES (". $value .")";
        return $this->db()->query($sql,$valuedata);
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
    public function updateOrderGenDanAction($action_data) {
        if(empty($action_data)){
            return false;
        }
        $sql = "UPDATE `base_order_info` SET `genzong`='{$action_data['genzong']}' WHERE `id`={$action_data['id']}";
        return $this->db()->query($sql);
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

   public function getorderAddresinfo($order_id){
       $sql = "SELECT * FROM app_order_address WHERE order_id=".$order_id;
       return $this->db()->getRow($sql);
   }

     /*
    * 通过order_id查询地址
   */
   public function getAddressByid($order_id){

    $sql = "SELECT `id`,`address` FROM `app_order_address` ";
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
    public function makeTaobaoOrder($all_data){
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
if(isset($order_data['user_id']) && !empty($order_data['user_id'])){
	$order_data['user_id']=intval($order_data['user_id']);
}else{
	$order_data['user_id']=0;
}
            $order_field = " `order_sn`,`consignee`,`mobile`, `user_id`, `order_status`, `order_pay_status`,  `customer_source_id`, `department_id`, `create_time`, `create_user`,  `modify_time`, `order_remark`,  `is_delete`,`send_good_status`,`buchan_status`,`is_zp`,`is_xianhuo`,`delivery_status`,`order_pay_type`,`referer`";
            $order_value = "'".$order_data['order_sn']."' ,'".$order_data['consignee']."' ,'".$order_data['mobile']."' ,".$order_data['user_id']." ,".$order_data['order_status']." ,".$order_data['order_pay_status'].",".$order_data['customer_source_id']." ,".$order_data['department_id']." , '".$order_data['create_time']."' , '".$order_data['create_user']."', '".$order_data['modify_time']."', '".$order_data['order_remark']."', '".$order_data['is_delete']."',1,1,".$order_data['is_zp'].",".$order_data['is_xianhuo'].",".$order_data['delivery_status'].",".$order_data['order_pay_type'].",'外部订单'";
            $sql_order = "INSERT INTO `base_order_info` (" . $order_field . ") VALUES (". $order_value .")";
            $pdo->query($sql_order);
            $order_id = $pdo->lastInsertId();
            $money_field = " `order_id`, `order_amount`, `money_paid`, `money_unpaid`,`favorable_price`,`goods_amount`,`shipping_fee`";
            $money_value = "".$order_id." ,".$money_data['order_amount']." ,".$money_data['money_paid']." ,".$money_data['money_unpaid']." ,".$money_data['favorable_price']." ,".$money_data['goods_amount']." ,".$money_data['shipping_fee'];
            $sql_money = "INSERT INTO `app_order_account` (" . $money_field . ") VALUES (". $money_value .")";
            $pdo->query($sql_money);
            //echo $sql_money;


            $invoice_field = " `order_id`, `is_invoice`, `invoice_amount`,`create_time`,`invoice_title`";
            $invoice_value = "".$order_id." ,".$invoice_data['is_invoice']." ,".$invoice_data['invoice_amount']." , '".$invoice_data['create_time']."','".$invoice_data['invoice_title']."'";
            $sql_invoice = "INSERT INTO `app_order_invoice` (" . $invoice_field . ") VALUES (". $invoice_value .")";
            $pdo->query($sql_invoice);


            $address_field = " `order_id`, `consignee`,`tel`,`address`,`distribution_type`,`express_id`,`freight_no`,`country_id`,`province_id`,`city_id`,`regional_id`,`shop_type`,`shop_name`,`email`,`zipcode`,`goods_id`";
            $address_value = "".$order_id." ,'".$address_data['consignee']."' ,'".$address_data['tel']."' , '".$address_data['address']."','".$address_data['distribution_type']."','".$address_data['express_id']."','".$address_data['freight_no']."' ,'".$address_data['country_id']."','".$address_data['province_id']."','".$address_data['city_id']."','".$address_data['regional_id']."','".$address_data['shop_type']."','".$address_data['shop_name']."','".$address_data['email']."','".$address_data['zipcode']."','".$address_data['goods_id']."'";
            $sql_address = "INSERT INTO `app_order_address` (" . $address_field . ") VALUES (". $address_value .")";
            $pdo->query($sql_address);

            //订单明细
            $goods_field = "`order_id`,`goods_id`,`goods_sn`,`ext_goods_sn`,`goods_name`,`goods_price`,`goods_count`,`create_time`,`create_user`,`modify_time`,`details_status`,`is_stock_goods`,`details_remark`,`cart`,`clarity`,`color`,`zhengshuhao`,`caizhi`,`jinse`,`jinzhong`,`zhiquan`,`kezi`,`face_work`,`xiangqian`,`send_good_status`,`buchan_status`,`goods_type`,`favorable_price`,`favorable_status`";
            $goods_value='';
            foreach($goods_data as $key=>$goods){
                $goods_value.= " (".$order_id." ,'".$goods['goods_id']."' ,'".$goods['goods_sn']."' ,'".$goods['ext_goods_sn']."' ,'".$goods['goods_name']."' ,".$goods['goods_price']." ,".$goods['goods_count']." , '".$goods['create_time']."' , '".$goods['create_user']."', '".$goods['modify_time']."', ".$goods['details_status'].", ".$goods['is_stock_goods'].", '".$goods['details_remark']."','".$goods['cart']."','".$goods['clarity']."','".$goods['color']."','".$goods['zhengshuhao']."','".$goods['caizhi']."','".$goods['jinse']."','".$goods['jinzhong']."','".$goods['zhiquan']."','".$goods['kezi']."','".$goods['face_work']."','".$goods['xiangqian']."',1,1,'".$goods['goods_type']."',".$goods['favorable_price'].",".$goods['favorable_status']."),"; }
            $sql_goods =rtrim("INSERT INTO `app_order_details` (" . $goods_field . ") VALUES $goods_value",',');
            $pdo->query($sql_goods);
            //echo $sql_goods;
            //赠品处理
            if($all_data['gift']){
                $all_data['gift']['order_id']=$order_id;
                $field = implode('`,`',array_keys($all_data['gift']));
                $valuse = implode("','",array_values($all_data['gift']));
                $sql = "insert into `rel_gift_order` (`$field`) VALUES('$valuse')";
                $pdo->query($sql);
            }
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
        $sql = "SELECT `a`.`id`,`a`.`order_sn` FROM ".$this->table()." AS `a`, `rel_out_order` AS `b` WHERE `a`.`id`=`b`.`order_id` AND b.out_order_sn ='".$out_order_sn."' AND a.order_status IN (1,2)";
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

    //修改批量发票金额问题
    public function updateBatchOrderInvoice($data) {
        $order_id = $data['order_id'];
        $sql= "UPDATE `app_order_invoice` SET `is_invoice`=".$data['is_invoice'].",  `invoice_amount`=".$data['invoice_amount'].", `is_invoice`=".$data['is_invoice'].",`invoice_title`='".$data['invoice_title']."' WHERE `order_id`=$order_id";
        return $this->db()->query($sql);
    }

    public function addOderDetail($goods){
        $order_id = $goods['order_id'];
        $field = "`order_id`,`goods_id`,`goods_sn`,`ext_goods_sn`,`goods_name`,`goods_price`,`goods_count`,`create_time`,`create_user`,`modify_time`,`details_status`,`is_stock_goods`,`details_remark`,`cut`,`cart`,`clarity`,`color`,`zhengshuhao`,`caizhi`,`jinse`,`jinzhong`,`zhiquan`,`kezi`,`face_work`,`xiangqian`,`send_good_status`,`buchan_status`,`goods_type`";
        $value = " ".$order_id." ,'".$goods['goods_id']."' ,'".$goods['goods_sn']."' ,'".$goods['ext_goods_sn']."' ,'".$goods['goods_name']."' ,".$goods['goods_price']." ,".$goods['goods_count']." , '".$goods['create_time']."' , '".$goods['create_user']."', '".$goods['modify_time']."', ".$goods['details_status'].", ".$goods['is_stock_goods'].", '".$goods['details_remark']."','".$goods['cut']."','".$goods['cart']."','".$goods['clarity']."','".$goods['color']."','".$goods['zhengshuhao']."','".$goods['caizhi']."','".$goods['jinse']."','".$goods['jinzhong']."','".$goods['zhiquan']."','".$goods['kezi']."','".$goods['face_work']."','".$goods['xiangqian']."',1,1,'".$goods['goods_type']."'";
        $sql = "INSERT INTO `app_order_details` (" . $field . ") VALUES (". $value .")";
          $this->db()->query($sql);
        return $this->db()->db()->lastInsertId();
    }

    //添加商品
    public function addNewOrderDetail($order_id,$data){
        foreach ($data as $goods){
        	!$goods['policy_id'] && $goods['policy_id']=0;
            $field = "`order_id`,`goods_id`,`goods_sn`,`ext_goods_sn`,`goods_name`,`goods_price`,`goods_count`,`create_time`,`create_user`,`modify_time`,`is_stock_goods`,`cut`,`cart`,`clarity`,`color`,`zhengshuhao`,`caizhi`,`jinse`,`jinzhong`,`zhiquan`,`kezi`,`face_work`,`xiangqian`,`send_good_status`,`buchan_status`,`goods_type`,`cat_type`,`product_type`,`kuan_sn`,`xiangkou`,`policy_id`,`is_4c`";
            $value = " ".$order_id." ,'".$goods['goods_id']."' ,'".$goods['goods_sn']."' ,'".$goods['ext_goods_sn']."' ,'".$goods['goods_name']."' ,".$goods['goods_price']." ,".$goods['goods_count']." , '".$goods['create_time']."' , '".$goods['create_user']."', '".$goods['modify_time']."',  ".$goods['is_stock_goods'].",'".$goods['cut']."','".$goods['cart']."','".$goods['clarity']."','".$goods['color']."','".$goods['zhengshuhao']."','".$goods['caizhi']."','".$goods['jinse']."','".$goods['jinzhong']."','".$goods['zhiquan']."','".$goods['kezi']."','".$goods['face_work']."','".$goods['xiangqian']."',1,1,'".$goods['goods_type']."',".$goods['cat_type'].",".$goods['product_type'].",'".$goods['kuan_sn']."','".$goods['xiangkou']."',".$goods['policy_id'].",'".$goods['is_4c']."'";
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
            $dsql = "SELECT COUNT(*) FROM `app_order_details` WHERE `order_id`=$order_id AND `buchan_status`<>9 AND is_stock_goods=0";
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
    public function getOrdersnByOutsn($out_order_sn){
        if(empty($out_order_sn)){
            return false;
        }
        $sql = "SELECT `boi`.`id` FROM `rel_out_order` as `ror` LEFT JOIN `base_order_info` as `boi` on ror.order_id=boi.id WHERE `ror`.`out_order_sn`='".$out_order_sn."'";
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

    //批量修改渠道部门
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
		echo $sql;
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
        $sql = "SELECT COUNT(*) FROM `".$this->table()."` WHERE `order_sn`='{$order_sn}' AND `department_id`=$order_source";
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
        $sql = "SELECT `id`,`order_pay_status` FROM `".$this->table()."` WHERE `order_sn`='$order_sn'";
        $res = $this->db()->getRow($sql);
        if(empty($res)){
            return false;
        }

        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
        //插入明细
            $goods_field = "`order_id`,`goods_id`,`goods_sn`,`goods_name`,`goods_price`,`goods_count`,`create_time`,`create_user`,`modify_time`,`details_status`,`is_stock_goods`,`details_remark`,`cart`,`clarity`,`color`,`zhengshuhao`,`caizhi`,`jinse`,`jinzhong`,`zhiquan`,`kezi`,`face_work`,`xiangqian`,`send_good_status`,`buchan_status`,`goods_type`,`favorable_price`,`favorable_status`";
        $price=0;
        foreach($goods_list as $key=>$goods){
            $goods_value= " (".$res['id']." ,'".$goods['goods_id']."' ,'".$goods['goods_sn']."' ,'".$goods['goods_name']."' ,".$goods['goods_price']." ,".$goods['goods_count']." , '".$goods['create_time']."' , '".$goods['create_user']."', '".$goods['modify_time']."', ".$goods['details_status'].", ".$goods['is_stock_goods'].", '".$goods['details_remark']."','".$goods['cart']."','".$goods['clarity']."','".$goods['color']."','".$goods['zhengshuhao']."','".$goods['caizhi']."','".$goods['jinse']."','".$goods['jinzhong']."','".$goods['zhiquan']."','".$goods['kezi']."','".$goods['face_work']."','".$goods['xiangqian']."',1,1,'"."',".$goods['favorable_price'].",".$goods['favorable_status'].")";
            $sql_goods ="INSERT INTO `app_order_details` (" . $goods_field . ") VALUES $goods_value";
            $pdo->query($sql_goods);
            //插入关联表
            $id = $pdo->lastInsertId();
            $rel_field="`order_id`,`out_order_sn`,`goods_detail_id`";
           $sql_rel ="INSERT INTO `rel_out_order` (" . $rel_field . ") VALUES ('".$res['id']."','".$orderi['out_order_sn']."',".$id.")";
           $this->db()->query($sql_rel);
          $price=$price+$goods['goods_price']*$goods['goods_count'];
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
        $sql = "SELECT `ao`.*,`bo`.* FROM `base_order_info` as `bo` LEFT JOIN `app_order_address` AS `ao` ON `bo`.`id`=`ao`.`order_id` WHERE `bo`.`order_sn`='{$order_sn}'";
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
        $sql="SELECT `gift_id`,`remark`,`gift_num` FROM `rel_gift_order` WHERE `order_id`=$id";
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
        $goodsModel = new AppOrderDetailsModel(28);
        $warehouseModel = new ApiWarehouseModel();
        $salepolicyM = new ApiSalePolicyModel();
        $res =  $goodsModel->getGoodsByOrderId(array('order_id'=>$order_id,'is_stock_goods'=>1));
        foreach($res as $ke=>$va){
            //绑定
            $reat = $warehouseModel->BindGoodsInfoByGoodsId(array('order_goods_id'=>$va['id'],'goods_id'=>$va['goods_id'],'bind_type'=>$data['bind_type']));
            //var_dump($reat);
            $xianhuo_detail[$ke]['is_sale'] = $data['is_sale'];
            $xianhuo_detail[$ke]['is_valid'] = 2;
            $xianhuo_detail[$ke]['goods_id'] = $va['goods_id'];
        }
        //上架or下架
        if(!empty($xianhuo_detail)){
            $salepolicyM->UpdateAppPayDetail($xianhuo_detail);
           // var_dump($reat);
        }
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
            $order_infos=$order_info;
            unset($order_infos['id']);
            unset($order_infos['check_time']);
            unset($order_infos['effect_date']);
            unset($order_infos['pay_date']);
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
            unset($address_info['id']);
            unset($address_info['goods_id']);
            $af= array_keys($address_info);
            $av = array_values($address_info);
            $af = implode("`,`",$af);
            $av = implode("','",$av);
            $sql = "insert into app_order_address(`$af`) value('$av')";
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
            $str .=" AND `order_sn` = ".$where['order_sn'];
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
                    $str .= " AND `order_remark` = '现货未取走'";
                }
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
		$sql = "SELECT oi.id as order_id,oi.order_sn,og.id,og.goods_id,og.details_remark,og.goods_price,oi.order_pay_status,oi.order_status,oi.customer_source_id from `app_order_details` as og, base_order_info as oi WHERE og.order_id = oi.id  AND oi.`order_sn` in ('".implode("','",$order_sns)."')";
		return $this->db()->getAll($sql);
	}
	
	/*
	author:zhangyuanyuan
	date:2015-10-14
	used:获取所有的销售顾问
	*/
	public function getxsgw($shopid,$begintime,$endtime)
	{
		$sql = "select distinct create_user from `app_order`.base_order_info where department_id=$shopid and order_status=2 and order_pay_status in (2,3,4) AND pay_date>='$begintime 00:00:00' AND pay_date<='$endtime 23:59:59' ;";
		$userData1 = $this->db()->getAll($sql);
        if($userData1){
            $userData1 = array_column($userData1,'create_user');
        }
        $sql = "SELECT distinct accecipt_man FROM `front`.`app_bespoke_info` WHERE create_time>='$begintime 00:00:00' and create_time<='$endtime 23:59:59' and department_id=$shopid and bespoke_status=2";
		$userData2 = $this->db()->getAll($sql);
        if($userData2){
            $userData2 = array_column($userData2,'accecipt_man');
        }

        $sql = "SELECT distinct accecipt_man FROM `front`.`app_bespoke_info` WHERE real_inshop_time>='$begintime 00:00:00' and real_inshop_time<='$endtime 23:59:59' and department_id=$shopid and bespoke_status=2";
		$userData3 = $this->db()->getAll($sql);
        if($userData3){
            $userData3 = array_column($userData3,'accecipt_man');
        }

        $sql = "SELECT distinct accecipt_man FROM `front`.`app_bespoke_info` WHERE bespoke_inshop_time>='$begintime' and bespoke_inshop_time<='$endtime' and department_id=$shopid and bespoke_status=2 ";
		$userData4 = $this->db()->getAll($sql);
        if($userData4){
            $userData4 = array_column($userData4,'accecipt_man');
        }

        $userData = array_merge($userData1,$userData2,$userData3,$userData4);
        $userData = array_unique(array_filter($userData));
        return $userData;
    }	
	
	/*
	author:zhangyuanyuan
	date:2015-10-14
	*/
	//获取实际成单数信息
	public function getrelorder($where,$timearr)
	{
		$where = array_filter($where);
		$timearr = array_filter($timearr);
		$sql = "select order_sn,is_zp from base_order_info where order_status=2 and order_pay_status > 1 and ";
		$str = $this->combinetj($where);
		$timestr = $this->combinetm($timearr);
		$sql .= $str;
		$sql .= $timestr .' 1 ';
		$data = $this->db()->getAll($sql);
		return $data;
		//实际成单数，订单总金额，已付款金额，未付款金额，赠品单数：第一次付款时间（订单状态：已审核并且支付定金，财务备案和已付款的订单）
	}
	
	//拼接相等的sql
	public function combinetj($where)
	{
		if(empty($where) || !is_array($where))
		{
			return '';
		}
		$str ='';
		foreach($where as $k=>$v)
		{
			if($v =='非婚博会')
			{
				$str .= $k ."<> '婚博会' and ";
			}else{
				$v =  is_numeric($v) ? $v : "'".$v."'";
				$str .= $k.'='.$v.' and ';
			}
		}
		return $str;
	}
	public function combinetm($timearr,$field='pay_date')
	{
		if(empty($timearr) || !is_array($timearr))
		{
			return '';
		}
		$str = '';
		/*
		if($field == 'pay_date')
		{
			$field = " date_format($field,'%y-%m-%d')";
		}*/		
		//特殊处理时间
		$array = array_filter($timearr);
		$begin_time = isset($array['begintime']) ? $array['begintime'] : '';
		$end_time = isset($array['endtime']) ? $array['endtime'] : '';
		if(!empty($begin_time))
		{
			$str .=" $field >= '".$begin_time." 00:00:00' and ";
		}
		if(!empty($end_time))
		{
			$str .=" $field <= '".$end_time." 23:59:59' and ";
		}
		return $str;
	}

    
    
   
    
}


?>
