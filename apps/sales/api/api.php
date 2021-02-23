<?php
/**
 * This contains the Retrieval API .
 *
 */
class api
{
    private  $db = null;
    private $error=0;
    private  $error_msg = '';
    private  $return_msg = '';
    private  $return_sql = '';
    private  $filter = array();
    //sales父级控制器
    protected $gifts = array(1=>'赠送珍珠耳钉',2=>'赠送S925银链',3=>'赠送黑皮绳',4=>'赠送红玛瑙手链',5=>'赠送白色手提袋',6=>'赠送情人节礼盒',7=>'赠送红绳',8=>'赠送手绳',9=>'赠送砗磲手链',10=>'赠送粉晶手链',11=>'赠送金条红包0.02g',12=>'赠送首饰盒',13=>'耳堵');
    public function __construct($_filter)
    {
        global $config;
        $this->db= new KELA_API_DB($config);
		$this->filter = $_filter;
    }
    /*
     * 获取赠品列表
     */
    public function getGiftList()
    {   
        $s_time = microtime();
        $filter = $this->filter;        
        $where = " WHERE 1 ";
        if(isset($filter['sell_type']) && $filter['sell_type'] != ''){
            $where .= " and `sell_type` = {$filter['sell_type']}";
        }
        if(!empty($filter['sale_way'])){
            if(is_array($filter['sale_way'])){
                $where .= " and `sale_way` in ('".implode("','",$filter['sale_way'])."')";
            }else{
                $where .= " and `sale_way` = '{$filter['sale_way']}'";
            }
        }
         
        if(isset($filter['status']) && $filter['status'] != ''){
            $where .= " and `status` = {$filter['status']}";
        }
        
        if(!empty($filter['goods_number'])){
            if(is_array($filter['goods_number'])){
                $where .= " and `goods_number` in ('".implode("','",$filter['goods_number'])."')";
            }else{
                $style_sn = $filter['goods_number'];
                $where .= " and `goods_number` = '{$filter['goods_number']}'";
            }            
        }
        $sql = "SELECT `name` as goods_name,`goods_number` as goods_id,`sell_sprice` as goods_price,is_randring,status,add_time,update_time,is_xz,sale_way FROM app_order.`gift_goods` {$where}  order by name desc;";
        
        //file_put_contents("gift.txt",$sql);
        
        $row = $this->db->getAll($sql);
        if(!empty($filter['extends'])){
            if(!empty($style_sn) && in_array('goods_image',$filter['extends'])){                
                $image_sql = "select middle_img from front.app_style_gallery where style_sn='{$style_sn}'";
                $goods_image = $this->db->getOne($image_sql);
                $row[0]['goods_image'] = $goods_image.'';
            }
        }
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
    
        //返回信息
        if(!$row){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到数据";
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
     * 订单发货时验证是否有库管审核的退款单
     * @param type $order_sn
     * $is_true = 1 可以配货，0不能配货
     */
    public function isHaveGoodsCheck() {
        $s_time = microtime();
		if(isset($this->filter['order_sn']) && !empty($this->filter['order_sn'])){
			$where = " `order_sn` = '{$this->filter['order_sn']}'";
		}else{
			$this -> error = 1;
			$this -> return_msg = "请检查订单号";
			$this->display();
		}
		
// 		$sql = "SELECT count(1) as num,`rg`.`check_status`,`rg`.`return_id` FROM `app_return_goods` as `rg` WHERE `rg`.`order_goods_id` !=0 AND ".$where." order by `rg`.`return_id` desc limit 1";
// 		$isHave = $this->db->getRow($sql);
// 		$is_true = 0;
// 		if($isHave['num']==0){
// 		    $is_true = 1;
// 		}elseif ($isHave['check_status'] == 0) {
// 		    $_sql = "SELECT `leader_status` FROM `app_return_check` WHERE `return_id`={$isHave['return_id']}";
// 		    $leader_status = $this->db->getOne($_sql);
// 		    if($leader_status == 2){
// 		        $is_true = 1;
// 		    }
// 		}elseif ($isHave['check_status'] == 5) {
// 		    $is_true = 1;
// 		}
		
// 		$is_true = 1; //0.表示退款流程中，1表示退款流程没有，或者完结
// 		$sql = "SELECT arg.check_status,arg.return_id,arc.`leader_status` FROM `app_return_goods` AS arg LEFT JOIN  `app_return_check` AS arc ON arg.return_id = arc.return_id WHERE arg.order_sn = '{$this->filter['order_sn']}'";
//         $returnGoodsInfo = $this->db->getAll($sql);
//         if(!empty($returnGoodsInfo)){
//             foreach ($returnGoodsInfo as $returnItem){
//                 //0未操作1主管审核通过2库管审核通过3事业部通过4现场财务通过5财务通过
//                 //判断逻辑是根据上面流程，具体还需业务部门提供
//                 if($returnItem['check_status'] != 5 && $returnItem['check_status'] != 0){
//                     $is_true = 0;
//                 }
                
//                 if($returnItem['check_status'] == 0 && $returnItem['leader_status'] != 2){
//                     $is_true = 0;
//                 }
                
//                 if($is_true == 0){
//                     break;
//                 }   
//             }
//         }

	   $sql = "SELECT apply_return FROM `base_order_info` WHERE {$where} ";
	   $apply_return = $this->db->getOne($sql);
	   $is_true = 1;
	   if($apply_return == 2){
	       $is_true = 0;
	   }
	   
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
		//返回信息

        $this -> error = 0;
        $this -> return_sql = $sql;
        $this -> return_msg = $is_true;
        $this->display();
    }


    /**
     * 获取订单商品信息
     */
    public function getOrderDetailByOrderId() {
        $res = 0;
        if(isset($this->filter['order_id']) && !empty($this->filter['order_id'])){
            //$select = "`id`, `order_id`, `goods_id`, `goods_sn`, `goods_name`, `goods_price`, `goods_count`, `create_time`, `modify_time`, `create_user`, `details_status`, `send_good_status`, `is_stock_goods`, `is_return`, `details_remark`,`goods_type`";
            $select = "*";
            if(isset($this->filter['select']) && !empty($this->filter['select'])){
                $select = $this->filter['select'];
            }

            $sql = "SELECT {$select} FROM `app_order_details` WHERE `order_id`={$this->filter['order_id']} ";
            if(isset($this->filter['goods_id']) && !empty($this->filter['goods_id'])){
                $sql.= " AND `goods_id`='".$this->filter['goods_id']."'";
            }
            if (isset($this->filter['is_return']) && $this->filter['is_return'] !='') {
                $sql .= " and `is_return` = {$this->filter['is_return']}";
            }
               
            $res = $this->db->getAll($sql);
        }

        if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单详情";
			$this -> return_msg = array();
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $res;
			$this->display();
		}
    }

    /**
     * 获取订单商品信息
     */
    public function getOrderDetailByBCId() {
        $res = 0;
        if(isset($this->filter['bc_id']) && !empty($this->filter['bc_id'])){
            $select = "*";
            if(isset($this->filter['select']) && !empty($this->filter['select'])){
                $select = $this->filter['select'];
            }

            $sql = "SELECT {$select} FROM `app_order_details` WHERE `bc_id`={$this->filter['bc_id']}";
            $res = $this->db->getRow($sql);
        }

        if(!$res)
        {
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到此订单详情";
            $this -> return_msg = array();
            $this->display();
        }
        else
        {
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }

    /**
     * 根据goods_id获取款号,和其他自定义字段
     */
    public function getGoodsSnByGoodsId() {
        if(empty($this->filter['goods_id'])){
            $this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "参数不全或不合法！";
			$this -> return_msg = '';
			$this->display();
        }
        $select = "`goods_sn`";
        if(!empty($this->filter['fields'])){
            $select = $this->filter['fields'];
        }
        $sql = "SELECT $select FROM `app_order_details` WHERE `id`={$this->filter['goods_id']}";
        $res = $this->db->getRow($sql);
        if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单详情";
			$this -> return_msg = '';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $res;
			$this->display();
		}
    }
    /**
     * 订单金额数据统计
     */
    public function getOrderPriceInfo() {
        if(empty($this->filter['order_id'])){
            $this -> error = 1;
			$this -> error_msg = "参数不全或不合法！";
			$this->display();
        }
        $sql = "SELECT `oa`.`order_id`,`oa`.`coupon_price`,`oa`.`shipping_fee`,`oi`.`order_sn`,oa.insure_fee,oa.pay_fee,oa.pack_fee,oa.card_fee,oa.real_return_price,SUM(`od`.`goods_price`) AS `goods_amount`,SUM(if(`od`.`favorable_status`=3,`od`.`favorable_price`,0)) AS `favorable_price`,`oa`.`money_paid`,`oa`.`order_amount`,`oa`.`money_unpaid` FROM `base_order_info` AS `oi` , `app_order_details` AS `od` , `app_order_account` AS `oa` WHERE `oi`.`id`=`od`.`order_id` AND `oi`.`id`=`oa`.`order_id` AND `oi`.`id`={$this->filter['order_id']}";
        $res = $this->db->getRow($sql);
        if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单详情";
			$this -> return_msg = '';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $res;
			$this->display();
		}
    }
    /**
     * 订单赠品详情
     */
    public function getOrderGiftInfo() {
        if(empty($this->filter['order_id'])){
            $this -> error = 1;
			$this -> error_msg = "参数不全或不合法！";
			$this->display();
        }
        $sql="SELECT `gift_id`,`remark`,`gift_num` FROM `rel_gift_order` WHERE `order_id`={$this->filter['order_id']}";
        $res = $this->db->getRow($sql);
        if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单详情";
			$this -> return_msg = '';
			$this->display();
		}
		else
		{
            $res['config_arr'] = array(1=>'赠送珍珠耳钉',2=>'赠送S925银链',3=>'赠送黑皮绳',4=>'赠送红玛瑙手链',5=>'赠送白色手提袋',6=>'赠送情人节礼盒',7=>'赠送红绳',8=>'赠送手绳',9=>'赠送砗磲手链',10=>'赠送粉晶手链',11=>'赠送金条红包0.02g',12=>'赠送首饰盒',13=>'耳堵');
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $res;
			$this->display();
		}
    }
    /**
     * 通过order_id查询发票信息
     */
    public function getInvoiceById() {
        if(empty($this->filter['order_id'])){
            $this -> error = 1;
			$this -> error_msg = "参数不全或不合法！";
			$this->display();
        }
        $sql = "SELECT * FROM `app_order_invoice` WHERE `order_id`={$this->filter['order_id']}";
        $res = $this->db->getRow($sql);
        if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单详情";
			$this -> return_msg = '';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $res;
			$this->display();
		}
    }

	/**
     * 取消申请关闭/已付款转为支付订金状态
     */
    public function UpdateOrderInfoModiy() {
        $s_time = microtime();
        $_id = 0;

        if (isset($this->filter) && !empty($this->filter)) {
            $data = $this->filter;
            if(count($data) > 0){
                if(!empty($this->filter['order_sn'])){
                    $sql = "SELECT * FROM `base_order_info` WHERE `order_sn`='{$this->filter['order_sn']}'";
                    $getOrder_sn = $this->db->getRow($sql);
                    if(!$getOrder_sn){
                        $this->error = 0;
                        $this->return_sql = '';
                        $this->error_msg = "订单号不存在";
                        $this->return_msg = 0;
                        $this->display();
                    }
                }
                if(!empty($this->filter['order_sn']) && isset($this->filter['apply_close']) && $this->filter['apply_close']!==''){
                    $data=" `apply_close`='{$this->filter['apply_close']}'";
                    $where=" `order_sn`='{$this->filter['order_sn']}'";
                }elseif(!empty($this->filter['order_sn']) && isset($this->filter['order_pay_status']) && $this->filter['order_pay_status']!=='' && $this->filter['order_pay_status']==2){
                    if($getOrder_sn['order_pay_status']==1){
                        $this->error = 0;
                        $this->return_sql = '';
                        $this->error_msg = "";
                        $this->return_msg = 3;
                        $this->display();
                    }elseif($getOrder_sn['order_pay_status']==2){
                        $this->error = 0;
                        $this->return_sql = '';
                        $this->error_msg = "";
                        $this->return_msg = 2;
                        $this->display();
                    }elseif($getOrder_sn['order_pay_status']==4){
                        $this->error = 0;
                        $this->return_sql = '';
                        $this->error_msg = "";
                        $this->return_msg = 4;
                        $this->display();
                    }elseif($getOrder_sn['order_status']!=2){
                         $this->error = 0;
                        $this->return_sql = '';
                        $this->error_msg = "";
                        $this->return_msg = 5;
                        $this->display();
                    } elseif ($getOrder_sn['delivery_status']==5) {
                        //已配货的订单不能更新已付款转为支付订金状态
                        $this->error = 0;
                        $this->return_sql = '';
                        $this->error_msg = "";
                        $this->return_msg = 6;
                        $this->display();
                    }
                     $data=" `order_pay_status`={$this->filter['order_pay_status']}";
                    $where=" `order_sn`='{$this->filter['order_sn']}'";
                }elseif(!empty($this->filter['order_sn']) && isset($this->filter['order_status']) && $this->filter['order_status']!==''){
                    $data=" `order_status`='{$this->filter['order_status']}'";
                    $where=" `order_sn`='{$this->filter['order_sn']}'";
                }elseif(!empty($this->filter['order_sn']) && isset($this->filter['send_good_status']) && $this->filter['send_good_status']!==''){
                    $data=" `send_good_status`='{$this->filter['send_good_status']}'";
                    $where=" `order_sn`='{$this->filter['order_sn']}'";
                }elseif(!empty($this->filter['order_sn']) && isset($this->filter['order_pay_status']) && $this->filter['order_pay_status']!=='' && $this->filter['order_pay_status']==4){
                     $data=" `order_pay_status`={$this->filter['order_pay_status']}";
                    $where=" `order_sn`='{$this->filter['order_sn']}'";
                }
                $sql="UPDATE `base_order_info` SET {$data} WHERE {$where} limit 1;";

                $res = $this->db->query($sql);
            }else{
                $this->error = 1;
				$this->return_sql = '';
				$this->error_msg = "update_data是个空数组";
				$this->return_msg = 0;
				$this->display();
            }
        }else{
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数update_data";
            $this->return_msg = 0;
            $this->display();
        }
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
		if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "订单不存在";
			$this -> return_msg = 0;
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = '';
			$this -> return_msg = $getOrder_sn;
			$this->display();
		}
    }

    /**
     * 更新订单的商品改为退货状态
     */
    public function updateOrderDetailById() {
        if(isset($this->filter['id']) && !empty($this->filter['id'])){
            $where = "`id` = {$this->filter['id']}";
        }
        if(empty($this->filter['is_return']) || !isset($where)){
            $this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "参数不全或不合法！";
			$this -> return_msg = '';
			$this->display();
        }
        $sql = "UPDATE `app_order_details` SET `is_return` = {$this->filter['is_return']} WHERE {$where}";
        $res = $this->db->query($sql);
        if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "更新失败";
			$this -> return_msg = 'failed';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = 'success';
			$this->display();
		}
    }
    /**
     * 更新订单的商品退货状态改为初始状态
     */
    public function updateOrderDetailByOrderSn() {
        if(!isset($this->filter['order_sn']) || empty($this->filter['order_sn'])){
            $this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "参数不全或不合法！";
			$this -> return_msg = '';
			$this->display();
        }
        $sql = "UPDATE `app_order_details` as `od`,`base_order_info` as `oi` SET `is_return` = 0 WHERE `oi`.`id`=`od`.`order_id` and `oi`.`order_sn`='{$this->filter['order_sn']}'";
        $res = $this->db->query($sql);
        if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "更新失败";
			$this -> return_msg = 'failed';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = 'success';
			$this->display();
		}
    }


    /**
     * 更新订单的快递方式
     */
    public function updateAddressWay() {
        if(!isset($this->filter['order_sn']) || empty($this->filter['order_sn'])){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "修改快递方式接口：错误，缺少参数订单号！";
            $this -> return_msg = '';
            $this->display();
        }
        if(!isset($this->filter['express_id']) || empty($this->filter['express_id'])){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "修改快递方式接口：错误，缺少参数快递方式！";
            $this -> return_msg = '';
            $this->display();
        }
        if ($this->filter['express_id'] != 10) {//上门取货
            if(isset($this->filter['freight_no']) && empty($this->filter['freight_no'])){
                $this -> error = 1;
                $this -> return_sql = '';
                $this -> error_msg = "修改快递方式接口：错误，缺少参数快递单号！";
                $this -> return_msg = '';
                $this->display();
            }
        }

        $_sql = "select count(*) as num from `base_order_info` where `order_sn`='{$this->filter['order_sn']}' and `send_good_status` in (3,5)";
        $count = $this->db->getRow($_sql);
        if(0==$count['num']){
            $setValue = "`oa`.`express_id` = {$this->filter['express_id']}";
            if(isset($this->filter['freight_no']) && !empty($this->filter['freight_no'])){
                $setValue .= ",`oa`.`freight_no`={$this->filter['freight_no']}";
            }
            $sql = "UPDATE `app_order_address` as `oa`,`base_order_info` as `oi` SET $setValue WHERE `oa`.`order_id`=`oi`.`id` and `oi`.`order_sn`='{$this->filter['order_sn']}'";
        }else{
            $this -> error = 1;
			$this -> return_sql = $_sql;
			$this -> error_msg = "修改快递方式接口：发货状态下的订单不允许更改快递方式";
			$this -> return_msg = 'failed';
			$this->display();
        }
        $res = $this->db->query($sql);
        if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "修改快递方式接口：更新快递方式失败";
			$this -> return_msg = 'failed';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = 'success';
			$this->display();
		}
    }



    /**
     * 更新订单的商品申请优惠金额审批成功
     */
    public function updateOrderDetailFieldById() {
        if(isset($this->filter['id']) && !empty($this->filter['id'])){
            $where = " `id` = {$this->filter['id']}";
        }
        if(empty($this->filter['update_fields']) || !isset($where)){
            $this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "参数不全或不合法！";
			$this -> return_msg = '';
			$this->display();
        }

        $res = $this->db->autoExecute('app_order_details',$this->filter['update_fields'],'UPDATE',$where);
        if($this->filter['update_fields']['favorable_status']==3){
            $details_info = $this->db->getRow("select `order_id`,`favorable_price` from `app_order_details` where `id`={$this->filter['id']}");
            if($details_info){
                $money_unpaid = $this->db->getRow("select `money_unpaid`,`favorable_price` from `app_order_account` where `order_id`={$details_info['order_id']}");

                $money_unpaid['money_unpaid'] -= $details_info['favorable_price'];
                $favorable_price = $money_unpaid['favorable_price'] + $details_info['favorable_price'];
                $this->db->query("update `app_order_account` set `order_amount`={$money_unpaid['money_unpaid']},`money_unpaid`={$money_unpaid['money_unpaid']},`favorable_price`=$favorable_price where `order_id`={$details_info['order_id']} limit 1;");

            }
        }
        if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "更新失败";
			$this -> return_msg = 'failed';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = "";
			$this -> return_msg = 'success';
			$this->display();
		}
    }


	/**
	 * 布产修改商品属性 yxt
	 */
	public function EditOrderGoodsInfo(){
		$s_time = microtime();
		if (isset($this->filter['detail_id']) &&!empty($this->filter['detail_id'])) {
			$detail_id = $this->filter['detail_id'];
		}else{
			$this->return_sql = '';
			$this->error_msg = "缺少参数'detail_id'";
			$this->return_msg = 0;
			$this->display();
		}
		if (isset($this->filter['apply_info']) &&!empty($this->filter['apply_info'])) {
			$apply_info = $this->filter['apply_info'];
		}else{
			$this->return_sql = '';
			$this->error_msg = "缺少参数'apply_info'";
			$this->return_msg = 0;
			$this->display();
		}

		$res = $this->db->autoExecute('app_order_details',$apply_info,'UPDATE','`id`='.$detail_id);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$res){
			$this -> error_msg = "更新失败";
			$this -> return_msg = false;
			$this->display();
		}else{
			$this -> return_sql = $res;
			$this -> return_msg = true;
			$this->display();
		}

	}

	/**
	 * 获取订单商品信息@yxt
	 */
	public function GetGoodsInfobyOrderSN(){
		$s_time = microtime();
		if(isset($this->filter['order_sn']) && !empty($this->filter['order_sn'])){
			$where = "(SELECT `id` FROM `base_order_info` WHERE `order_sn` = '{$this->filter['order_sn']}')";
		}else{
			$this -> return_msg = "请检查订单号";
			$this->display();
		}
		$sql = "SELECT `id`,`goods_id`,`goods_sn`,`goods_name`,`goods_price`,`goods_count`,`send_good_status` FROM `app_order_details` WHERE `order_id` = ".$where;
		$row = $this->db->getAll($sql);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
		//返回信息
		if(!$row){
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单相关商品信息";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}

	}
    
    
	/**
	 * 获取订单信息
	 */
	public function GetOrderInfoByDetailsId(){
		$s_time = microtime();
		if(isset($this->filter['details_id']) && !empty($this->filter['details_id'])){
			$sql = "select oi.* from app_order_details as od ,base_order_info as oi where od.order_id=oi.id and od.id={$this->filter['details_id']}";
		}else{
            $this -> error = 1;
			$this -> error_msg = "请检查商品明细id";
			$this->display();
		}
		$row = $this->db->getRow($sql);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
		//返回信息
		if(!$row){
            $this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单相关商品信息";
			$this -> return_msg = array();
			$this->display();
		}else{
            $this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}

	}

        /*
         * 获取外部订单号
         *
         */
        public function GetOutOrderSn() {
            $s_time = microtime();
            $where  = " where 1 ";
            if(isset($this->filter['order_id']) && !empty($this->filter['order_id'])){
                $where .= " and order_id=".$this->filter['order_id'];
            }else{
                $this->return_msg = "没有订单号";
                $this->display();
            }
            $sql = "SELECT `out_order_sn` FROM rel_out_order ".$where;
            $row = $this->db->getRow($sql);
            // 记录日志
            $reponse_time = microtime() - $s_time;
            $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
            //返回信息
            if(!$row){
                $this -> return_sql = $sql;
                $this -> error_msg = "未查询到信息";
                $this -> return_msg = array();
                $this->display();
            }else{
                $this -> return_sql = $sql;
                $this -> return_msg = $row;
                $this->display();
            }

        }


    /**
    * 获取订单order_sn，获取自定义字段
    * @author hlc
    */
    public function GetDeliveryStatus(){
                $s_time = microtime();
        if(empty($this->filter['order_sn'])){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "参数不全或不合法！";
            $this -> return_msg = '';
            $this->display();
        }
        $select = " `id` ";
        if(!empty($this->filter['fields'])){
            $select = $this->filter['fields'];
        }
        $sql = "SELECT $select FROM `base_order_info` WHERE `order_sn`='{$this->filter['order_sn']}'";
        $res = $this->db->getRow($sql);


        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        if(!$res)
        {
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到此订单详情";
            $this -> return_msg = '';
            $this->display();
        }
        else
        {
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }


	/**
	* 获取订单order_sn，获取自定义字段
	* @author hlc
	*/
	public function GetDeliveryStatus2(){
                $s_time = microtime();
		if(empty($this->filter['order_sn'])){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "参数不全或不合法！";
			$this -> return_msg = '';
			$this->display();
		}
		$select = " a.`id` ";
		if(!empty($this->filter['fields'])){
			$select = $this->filter['fields'];
		}

        $has_company = $this->filter['has_company'];
        $results = array_intersect(array(58,445), $has_company);
        $has_company = implode("','", $has_company);
        if(!empty($results)){
            $where =" where a.order_sn=".$this->filter['order_sn']." and (ad.distribution_type=2 OR sc.company_id in('".$has_company."') OR EXISTS(SELECT 1 FROM app_order_address WHERE id=ad.id AND shop_type = 2 AND distribution_type = 1))";

        }else{
             $where = " where a.order_sn=".$this->filter['order_sn']." and ad.distribution_type =1 and sc.company_id in('".$has_company."')";
        }

         $sql = "SELECT $select FROM `base_order_info` a left join app_order_address ad on a.id=ad.order_id left join cuteframe.sales_channels sc on ad.shop_name=sc.channel_own ".$where;

        $res = $this->db->getRow($sql);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单详情";
			$this -> return_msg = '';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $res;
			$this->display();
		}
	}

	/**
	* 获取订单order_id，获取自定义字段
	* @author hlc
	*/
	public function GetOrderAccountRow(){
                $s_time = microtime();
		if(empty($this->filter['order_id'])){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "参数不全或不合法！";
			$this -> return_msg = '';
			$this->display();
		}
		$sql = "SELECT * FROM `app_order_account` WHERE `order_id`={$this->filter['order_id']}";
		$res = $this->db->getRow($sql);


		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单金额";
			$this -> return_msg = '';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $res;
			$this->display();
		}
	}

    /**
     * 更新部分订单状态
     */
    public function updateOrderInfoStatus() {
        $s_time = microtime();
        if(empty($this->filter['order_sn']) || empty($this->filter['send_good_status']) || empty($this->filter['delivery_status'])){
            $this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "订单号order_sn不能为空或更新的状态不能为空";
			$this->display();
        }
        $sql = "UPDATE `base_order_info` SET `send_good_status`={$this->filter['send_good_status']},`delivery_status`={$this->filter['delivery_status']} WHERE `order_sn`='{$this->filter['order_sn']}'";
		$res = $this -> db -> query($sql);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "更新失败";
			$this -> return_msg = 'failed';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = 'success';
			$this->display();
		}
    }


    /**
     * 更新订单金额数据
     * modified by zzm 2015-12-18 boss-982
     */
    public function updateAccountInfo() {
        $s_time = microtime();
        if(empty($this->filter['order_sn'])){
            $this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "订单号order_sn不能为空";
			$this->display();
        }
        $sql = "UPDATE `app_order_account` as `oa`,`base_order_info` as `oi` SET `oi`.`delivery_status`=1,`oi`.`order_pay_status`=1,`oa`.`money_paid`=0,`oa`.`money_unpaid`=`order_amount`,`oi`.`pay_date`=null WHERE `oi`.`id`=`oa`.`order_id` and `oi`.`order_sn`='{$this->filter['order_sn']}'";
        $res = $this -> db -> query($sql);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "更新失败";
			$this -> return_msg = 'failed';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = 'success';
			$this->display();
		}
    }
    /**
     * 更新订单金额数据,不改变配货状态
     * modified by zzm 2015-12-18 boss-982
     */
    public function updateAccountInfo_not_delivery_status() {
    	$s_time = microtime();
    	if(empty($this->filter['order_sn'])){
    		$this -> error = 1;
    		$this -> return_sql = "";
    		$this -> error_msg = "订单号order_sn不能为空";
    		$this->display();
    	}
    	$sql = "UPDATE `app_order_account` as `oa`,`base_order_info` as `oi` SET `oi`.`order_pay_status`=1,`oa`.`money_paid`=0,`oa`.`money_unpaid`=`order_amount`,`oi`.`pay_date`=null WHERE `oi`.`id`=`oa`.`order_id` and `oi`.`order_sn`='{$this->filter['order_sn']}'";
    	$res = $this -> db -> query($sql);
    	// 记录日志
    	$reponse_time = microtime() - $s_time;
    	$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
    
    	//返回信息
    	if(!$res)
    	{
    		$this -> error = 1;
    		$this -> return_sql = $sql;
    		$this -> error_msg = "更新失败";
    		$this -> return_msg = 'failed';
    		$this->display();
    	}
    	else
    	{
    		$this -> error = 0;
    		$this -> return_sql = $sql;
    		$this -> return_msg = 'success';
    		$this->display();
    	}
    }
    
    /**
     * 更新订单实退金额数据
     */
    public function modfiyAccountInfo() {
        $s_time = microtime();
        if(empty($this->filter['order_sn'])){
            $this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "订单号order_sn不能为空";
			$this->display();
        }
        if(empty($this->filter['order_id'])){
            $this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "订单id号order_id不能为空";
			$this->display();
        }
        $_sql = "update app_order.app_order_account ac inner join 
            (	
                SELECT 
                        i.id, i.order_sn, 
                        d.goods_price, 
                        d.favorable_price ,
                        IFNULL(f.deposit, 0) as deposit ,
                        (IFNULL(d.goods_price,0)-IFNULL(d.favorable_price,0)-IFNULL(d.refund_goods_price,0)+IFNULL(d.refund_favor_price,0)-IFNULL(a.coupon_price,0)+IFNULL(a.shipping_fee,0)) as order_amount,
                      (IFNULL(d.goods_price,0)-IFNULL(d.favorable_price,0)-IFNULL(d.refund_goods_price,0)+IFNULL(d.refund_favor_price,0)-IFNULL(a.coupon_price,0)+IFNULL(a.shipping_fee,0)-IFNULL(f.deposit,0)) as real_money_unpaid
                    from app_order.base_order_info i 
                    left JOIN
                    (
                        select order_id, sum(deposit) as deposit from finance.app_order_pay_action
                        where `is_type` = 1 and `status` != 4
                        GROUP BY order_id
                    ) f on f.order_id = i.id
                    left JOIN (
                        SELECT order_id, sum(goods_price) as goods_price, sum(case when is_return = 1 then goods_price else 0 end) as refund_goods_price, sum(case when favorable_status = 3 then favorable_price else 0 end) as favorable_price, sum(case when is_return = 1 and favorable_status = 3 then favorable_price else 0 end) as refund_favor_price from app_order.app_order_details group by order_id
                    ) d on d.order_id = i.id
                    inner JOIN app_order.app_order_account a on a.order_id = i.id
                WHERE i.id = '".$this->filter['order_id']."'
            ) g on g.id = ac.order_id
            set ac.order_amount = ifnull(g.order_amount,0), ac.goods_amount = ifnull(g.goods_price,0), ac.favorable_price = ifnull(g.favorable_price,0), ac.money_paid = ifnull(g.deposit,0), ac.goods_return_price = 0, ac.real_return_price = 0 ,ac.money_unpaid = (case when g.real_money_unpaid <0 then 0 else g.real_money_unpaid end)
            where ac.order_id = ".$this->filter['order_id']." ;";;
		$res = $this -> db -> query($_sql);
		// 记录日志
        
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $_sql;
			$this -> error_msg = "更新失败";
			$this -> return_msg = 'failed';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $_sql;
			$this -> return_msg = 'success';
			$this->display();
		}
    }

    /**
     * 查询是否转单退款
     */
    public function getReturnGoodsInfo() {
        $s_time = microtime();
        if(empty($this->filter['order_sn'])){
            $this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "订单号order_sn不能为空";
			$this->display();
        }
        $sql = "SELECT `rg`.`return_type`,`rg`.`pay_order_sn` FROM `app_return_goods` as `rg`,`app_return_check` as `rc` WHERE `rg`.`order_sn`='{$this->filter['order_sn']}'";
		$res = $this -> db ->getRow($sql);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "查询失败";
			$this -> return_msg = 'failed';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $res;
			$this->display();
		}
    }



    /**
     * 更新订单实退金额
     */
    public function updateOrderAccountRealReturnPrice() {
        $s_time = microtime();
        if(empty($this->filter['order_id']) || empty($this->filter['real_return_price'])){
            $this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "订单号order_sn不能为空或更新的数据不能为空";
			$this->display();
        }
        $_sql = "SELECT `real_return_price`,`money_unpaid`,`money_paid`,`order_amount` FROM `app_order_account` WHERE `order_id`={$this->filter['order_id']}";
        $order_account = $this->db->getRow($_sql);
        if(empty($order_account)){
            $this -> error = 1;
			$this -> return_sql = $_sql;
			$this -> error_msg = "没有该订单";
			$this -> return_msg = 'failed';
			$this->display();
        }

        if($this->filter['zhuandan']){
            $money_paid = $order_account['money_paid']-$this->filter['real_return_price'];
            $order_amount = $order_account['order_amount']-$this->filter['real_return_price'];
            $real_return_price = $order_account['real_return_price']+$this->filter['real_return_price'];
            $set = "`real_return_price`=$real_return_price,`money_paid`=$money_paid,`order_amount`=$order_amount";
        }else{
            $money_paid = $order_account['money_paid']-$this->filter['real_return_price'];
            $money_unpaid = $order_account['money_unpaid']+$this->filter['real_return_price'];
            $real_return_price = $order_account['real_return_price']+$this->filter['real_return_price'];
            $set = "`real_return_price`=$real_return_price,`money_paid`=$money_paid,`money_unpaid`=$money_unpaid";
        }
        
        /*
        $money_paid = $order_account['money_paid']-$this->filter['real_return_price'];
        $order_amount = $order_account['order_amount']-$this->filter['real_return_price'];
        $real_return_price = $order_account['real_return_price']+$this->filter['real_return_price'];
        $set = "`real_return_price`=$real_return_price,`money_paid`=$money_paid,`order_amount`=$order_amount";
        */
        $sql = "UPDATE `app_order_account` SET $set WHERE `order_id`={$this->filter['order_id']}";
		$res = $this -> db -> query($sql);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "更新失败";
			$this -> return_msg = 'failed';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = 'success';
			$this->display();
		}
    }

	/**
	 * 更改订单发货状态
	 * @author : yxt
	 */
	public function updateOrderSendStatus(){
		if(isset($this->filter['order_sn']) && !empty($this->filter['order_sn'])){
			$where = "`order_sn` = '{$this->filter['order_sn']}'";
		}else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "请传入订单号！";
			$this -> return_msg = '';
			$this->display();
		}
		if(isset($this->filter['send_good_status']) && !empty($this->filter['send_good_status'])){
			$set_status = "`send_good_status` = '{$this->filter['send_good_status']}'";
		}else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "请传入发货状态！";
			$this -> return_msg = '';
			$this->display();
		}
		if(isset($this->filter['express_id']) && !empty($this->filter['express_id'])){
			$set_express = "`express_id` = '{$this->filter['express_id']}'";
		}
		//回写快递公司
		//$sql = "UPDATE `app_order_address` SET {$set_express} WHERE `order_id` = (SELECT `id` FROM `base_order_info` WHERE `order_sn` = ".$this->filter['order_sn'].")";
		//$res1 = $this->db->query($sql);
		//回写发货状态
		$sql = "UPDATE `base_order_info` SET {$set_status} WHERE {$where}";
		$res2 = $this->db->query($sql);

		if(!$res2)
		{
			$this -> return_sql = $sql;
			$this -> error_msg = "更新失败";
			$this -> return_msg = 'failed';
			$this->display();
		}
		else
		{
			$this -> return_sql = $sql;
			$this -> return_msg = 'success';
			$this->display();
		}
	}

	/**
	 * 更改订单商品发货状态,回写快递单号(By:order_sn)
	 */
	public function setOrderGoodsSend(){
		if(isset($this->filter['order_sn']) && !empty($this->filter['order_sn']))
		{
			$order_sn = $this->filter['order_sn'];
		}
		else
		{
			$this -> return_sql = '';
			$this -> error_msg = "请传入订单号！";
			$this -> return_msg = '';
			$this->display();
		}
		if(isset($this->filter['freight_no']) && !empty($this->filter['freight_no']))
		{
			$freight_no = $this->filter['freight_no'];
		}
		else
		{
			$freight_no = '';
		}
		$sql = "update base_order_info as a left join app_order_details as b on a.id=b.order_id set a.send_good_status='2',b.send_good_status=2 where a.order_sn='{$order_sn}' ";
		$res= $this->db->query($sql);
		//回写快递号
		$sql1 = "UPDATE `app_order_address` SET freight_no='{$freight_no}' WHERE `order_id` = (SELECT `id` FROM `base_order_info` WHERE `order_sn` = '".$this->filter['order_sn']."')";
		$res1 = $this->db->query($sql1);
		if(!$res && $res1)
		{
			$this -> return_sql = $sql;
			$this -> error_msg =  false;
			$this -> return_msg = false;
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = true;
			$this->display();
		}

	}

    /**
     * 更新订单支付状态为3（已付款）
     */
    public function updateOrderPayStatus() {
        if(isset($this->filter['order_id']) && !empty($this->filter['order_id'])){
            $where = " WHERE `id` = {$this->filter['order_id']}";
        }else{
            $this -> error = 1;
			$this -> error_msg = "参数不全或不合法！";
			$this -> return_msg = $this->filter['deposit'];
			$this->display();
        }
        $sql = "UPDATE `base_order_info` SET `order_pay_status`=3 $where";
        $res = $this->db->query($sql);
        if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "更新失败";
			$this -> return_msg = 'failed';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = 'success';
			$this->display();
		}
    }

    /**
     * 更新订单表中的部分数据
     */
    public function updateOrderInfoByOrderId() {
        if(isset($this->filter['order_id']) && !empty($this->filter['order_id'])){
            $where = " WHERE `order_id` = {$this->filter['order_id']}";
        }
        if(empty($this->filter['deposit']) || !isset($where)){
            $this -> error = 1;
			$this -> error_msg = "参数不全或不合法！";
			$this -> return_msg = isset($this->filter['deposit']) ? $this->filter['deposit'] : "";
			$this->display();
        }
        $order_info = $this->db->getRow("SELECT `oa`.`money_paid`,`oa`.`money_unpaid`,`oi`.`order_pay_status` FROM `app_order_account` as `oa`,`base_order_info` as `oi` where `oa`.`order_id`=`oi`.`id` and `oa`.`order_id`={$this->filter['order_id']}");

        $money_paid = $order_info['money_paid'] + $this->filter['deposit'];
        $money_unpaid = $order_info['money_unpaid'] - $this->filter['deposit'];
        if($order_info['order_pay_status']==1){
            //部分付款
			//第一次点款时间
			if (!empty($this->filter['pay_time'])) {
				$pay_date = $this->filter['pay_time'];
			} else if (!empty($this->filter['pay_date'])) {
				$pay_date = $this->filter['pay_date'];
			} else {
				$pay_date = date("Y-m-d H:i:s");
			}
            $_sql = "UPDATE `base_order_info` SET `order_pay_status`=2,`pay_date`='{$pay_date}' WHERE `id`={$this->filter['order_id']} and (pay_date is null or pay_date ='0000-00-00 00:00:00') limit 1;";
            $this->db->query($_sql);
        }
        $sql = "UPDATE `app_order_account` SET `money_paid`={$money_paid},`money_unpaid`={$money_unpaid} $where limit 1;";
        $res = $this->db->query($sql);
        if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "更新失败";
			$this -> return_msg = 'failed';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = 'success';
			$this->display();
		}
    }


    /**
     * 0元点款接口,更新订单第一次点款时间
     */
    public function updateOrderInfoPayDate() {
        if(!isset($this->filter['order_id']) || empty($this->filter['order_id'])){
            $this -> error = 1;
			$this -> error_msg = "参数不全或不合法";
			$this -> return_msg = 'failed';
			$this->display();
        }

        //第一次点款时间
		if (!empty($this->filter['pay_time'])) {
			$pay_date = $this->filter['pay_time'];
		} else if (!empty($this->filter['pay_date'])) {
			$pay_date = $this->filter['pay_date'];
		} else {
			$pay_date = date("Y-m-d H:i:s");
		}
        $sql = "UPDATE `base_order_info` SET `order_pay_status`=2,`pay_date`='{$pay_date}' WHERE `id`={$this->filter['order_id']} and (pay_date is null or pay_date ='0000-00-00 00:00:00') limit 1;";
        $res = $this->db->query($sql);
        if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "更新失败";
			$this -> return_msg = 'failed';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = 'success';
			$this->display();
		}
    }




    // 更具客户姓名查询订单号，在布产列表需要根据客户姓名查询信息
    public function GetOrderSnByConsignee() {
        $s_time = microtime();
        if (isset($this->filter['consignee']) && !empty($this->filter['consignee'])){
            $consignee = $this->filter['consignee'];
            $sql = "select `order_sn` from `base_order_info` where consignee='".$consignee."'";
            $res = $this->db->getAll($sql);
        }
        if(!$res)
        {
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到此订单";
            $this -> return_msg = array();
            $this->display();
        }
        else
        {
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }


    /**
     * 取得订单配送地址
     */
    public function getAddressById() {
        $s_time = microtime();
        if (!isset($this->filter['order_id']) || empty($this->filter['order_id'])){
            $this -> error = 1;
            $this -> error_msg = "参数不全或不合法";
            $this->display();
        }
        $sql = "SELECT * FROM `app_order_address` WHERE `order_id` ={$this->filter['order_id']}";
        $res = $this->db->getRow($sql);
        if(!$res)
        {
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到此订单";
            $this -> return_msg = array();
            $this->display();
        }
        else
        {
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }

    /**
     * 通过订单号取订单信息
     */
    public function getOrder_infoByOrder_sn() {
        $s_time = microtime();
        if (!isset($this->filter['order_sn']) || empty($this->filter['order_sn'])){
            $this -> error = 1;
            $this -> error_msg = "参数不全或不合法";
            $this->display();
        }
        $sql = "SELECT * FROM `base_order_info` WHERE `order_sn` ='{$this->filter['order_sn']}' limit 1";
        $res = $this->db->getRow($sql);
        if(!$res)
        {
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到此订单";
            $this -> return_msg = array();
            $this->display();
        }
        else
        {
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }

    /**
     * 根据订单号查询
     */
    public function GetOrderInfoBySn() {
        $res = 0;
        $sql = '';
        if(isset($this->filter['order_sn']) && !empty($this->filter['order_sn'])){
            $_order_sn = $this->filter['order_sn'];
            $select = "`oi`.*, `oa`.`order_amount`, `oa`.`money_paid`, `oa`.`money_unpaid`, `oa`.`goods_return_price`, `oa`.`real_return_price`, `oa`.`shipping_fee`, `oa`.`goods_amount`,(select GROUP_CONCAT(DISTINCT rod.out_order_sn) from rel_out_order as rod where rod.order_id=oi.id group by rod.order_id) as out_order_sn ";
            if(isset($this->filter['select']) && !empty($this->filter['select'])){
                $select = $this->filter['select'];
            }
            $sql = "SELECT {$select} FROM `base_order_info` as `oi` LEFT JOIN `app_order_account` as `oa` ON `oi`.`id`=`oa`.`order_id`  WHERE `oi`.`order_sn`='{$this->filter['order_sn']}' and `oi`.`is_delete`= 0";
            $res = $this->db->getRow($sql);
            if ($res && isset($this->filter['with_normal_items']) && !empty($this->filter['with_normal_items'])) {
            	$items = $this->db->getAll("SELECT b.*,s.shoucun,s.company_id,s.company,s.warehouse,s.warehouse_id FROM `app_order_details` AS `b` left join`warehouse_shipping`.`warehouse_goods` s ON b.goods_id=s.goods_id WHERE b.order_id=".$res['id']." and b.is_return=0;");
            	$res = array('order' => $res,'items' => $items);
            }
        }

        if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单";
			$this -> return_msg = array();
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $res;
			$this->display();
		}
    }

    //财务给订单填写发票查询的订单和账户和发票的信息
    public function GetOrderInfoInvoiceBySn() {
        $res = 0;
        if(isset($this->filter['order_sn']) && !empty($this->filter['order_sn'])){
            $_order_sn = $this->filter['order_sn'];
            $select = "`oi`.*, `oa`.`order_amount`, `oa`.`money_paid`, `oa`.`money_unpaid`, `oa`.`goods_return_price`, `oa`.`real_return_price`, `oa`.`shipping_fee`, `oa`.`goods_amount`,`oi`.`department_id`";
            if(isset($this->filter['select']) && !empty($this->filter['select'])){
                $select = $this->filter['select'];
            }
            $sql = "SELECT {$select} FROM `base_order_info` as `oi`,`app_order_account` as `oa` WHERE `oi`.`id`=`oa`.`order_id` and `oi`.`order_sn`='{$this->filter['order_sn']}'";
            $res = $this->db->getRow($sql);

            if ($res) {
                $sql = "SELECT id,is_invoice,invoice_title,invoice_amount,invoice_num,taxpayer_sn,invoice_email FROM app_order_invoice WHERE order_id=$res[id] AND is_invoice=1";
                 $invoice = $this->db->getAll($sql);
            }else{
                $this -> error = 3;
                $this -> return_sql = $sql;
                $this -> error_msg = "未查询到此订单";
                $this -> return_msg = array();
                $this->display();
            }
        }

        if(!$invoice)
        {
            $arr=array('info'=>$res);
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到此订单的发票信息";
            $this -> return_msg =$arr;
            $this->display();
        }
        else
        {
            $arr=array('info'=>$res,'fapiao'=>$invoice);
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $arr;
            $this->display();
        }
    }

    public function updateOrderInfoInvoiceByid(){
        if(isset($this->filter['order_id']) && !empty($this->filter['order_id'])){
			$res = $this -> db -> autoExecute('app_order_invoice',$this->filter['updatedata'],'UPDATE',"order_id=".$this->filter['order_id']);
        }

        if(!$res){
            $this -> error = 0;
            $this -> return_sql = '';
            $this -> error_msg = "未查询到此订单信息";
            $this -> return_msg = false;
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = '';
            $this -> return_msg = true;
            $this->display();
        }

    }

	/**
	 * 获取订单明细ID
	 * @author : yxt
	 */
	public function getOrderDetailsId(){
		$s_time = microtime();
		$where = " 1";
		if(isset($this->filter['order_sn']) && !empty($this -> filter["order_sn"]))
		{
			$where .=" AND `order_sn` ='".$this -> filter["order_sn"]."'";
		}else{
			$this -> error = 0;
                        $this -> return_sql = '';
                        $this -> error_msg = "订单号不能空！";
                        $this -> return_msg = array();
                        $this->display();
		}

		$sql = "SELECT `id` FROM `base_order_info` WHERE ".$where;
		$order_id = $this->db->getOne($sql);
     //`oa`.`coupon_price`,`oa`.`shipping_fee`,`oa`.`insure_fee`,`oa`.`pay_fee`,`oa`.`pack_fee`,`oa`.`card_fee`
		//获取订单基本信息
		$sql = "SELECT oi.`id`, oi.`order_sn`, oi.`user_id`, oi.`department_id`,oi.`order_status`, oi.`order_pay_type`, oi.`order_pay_status`,oi.`send_good_status`,oi.`is_delete`,oi.`is_xianhuo`, `oi`.`order_remark`, `oa`.`order_amount`, `oa`.`money_paid`, `oa`.`money_unpaid`, `oa`.`goods_return_price`, `oa`.`real_return_price`,`oa`.`shipping_fee`,`oa`.`coupon_price`,`oa`.`insure_fee`,`oa`.`pay_fee`,`oa`.`pack_fee`,`oa`.`card_fee`,oi.customer_source_id,oi.apply_return,oi.apply_close FROM `base_order_info` as oi,`app_order_account` as `oa` WHERE `oi`.`id`=`oa`.`order_id` and ".$where;

		$orderInfo = $this->db->getRow($sql);
		//获取订单地址
		$sql = "SELECT `consignee`,`shop_name`,`distribution_type`,`express_id`,`country_id`,`province_id`,`city_id`,`regional_id`,`address`,`tel`,`email`,`zipcode`,`freight_no`,`shop_type` FROM `app_order_address` WHERE `order_id` = '".$order_id."'";
		$order_address = $this->db->getRow($sql);

		//获取订单明细ID
		$sql = "SELECT `id`,`goods_id`,`goods_sn`,`goods_name`,`goods_count` as num, `goods_price`,`cart`,`cut`,`clarity`,`color`,`caizhi`,`jinse`, `jinzhong`,`zhiquan`,`kezi`,`face_work`,`xiangqian`,`favorable_status`,`favorable_price`,`is_return`,`is_zp`,`is_finance` FROM `app_order_details`  WHERE `order_id` = ".$order_id;
		$detail = $this->db->getAll($sql);

		//获取发票信息
		$sql = "SELECT `is_invoice`,`invoice_title`,`invoice_num`,`invoice_amount`,`create_time`  FROM `app_order_invoice`  WHERE `order_id` = '".$order_id."'";
		$order_invoice = $this->db->getRow($sql);

        //外部订单信息
        $sql = "SELECT `out_order_sn` FROM `rel_out_order` WHERE `order_id` = {$order_id}";
        $order_out = $this->db->getOne($sql);

		$data[0] = $orderInfo;
		$data[1] = $order_address;
		$data[2] = $detail;
		$data[3] = $order_invoice;
        $data[4] = $order_out;

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$orderInfo){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单信息";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $data;
			$this->display();
		}
	}

    /**
     * 删除退款记录
     */
    public function deleteReturnGoodsInfo(){
        $s_time = microtime();
        if (isset($this->filter['order_sn']) && $this->filter['order_sn']!='') {
            $sql = "delete `rg`,`rc` FROM `app_return_goods` as `rg`,`app_return_check` as `rc` WHERE `rg`.`return_id`=`rc`.`return_id` and `rg`.`order_sn`='".$this->filter['order_sn']."'";
            $res = $this->db->query($sql);
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "没有order_sn";
            $this -> return_msg = array();
            $this->display();
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        if(!$res){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "没有查到相应的信息";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }


    /**
     * 查询订单列表分页信息（关联支付状态）
     * @param order_sn order_pay_status
     * @return json
     */
	public function GetOrderList()
	{
		$s_time = microtime();
		//$this -> filter["page"] = 3;  //当前页
		$page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
		$page_size = intval($this -> filter["page_size"]) > 0 ? intval($this -> filter["page_size"]) : 15;

        $order_id			=intval(trim($this->filter['order_id']));//订单id
		$order_sn			=trim($this->filter['order_sn']);//订单号
		$consignee			=trim($this->filter['consignee']);//客户姓名
		$order_pay_status	=intval(trim($this->filter['order_pay_status']));//支付状态
		//$no_order_status	=intval(trim($this->filter['no_order_status']));//订单状态
        $no_order_status    =intval(trim($this->filter['order_status']));//订单状态
		$mobile      =trim($this->filter['mobile']);//电话
        $start_time      =trim($this->filter['start_time']);//开始时间
        $end_time      =trim($this->filter['end_time']);//结束时间
        $department_id      =trim($this->filter['order_department']);//销售渠道
        //$department_id      =trim($this->filter['department_id']);//销售渠道
		$ids=isset($this->filter['ids'])?$this->filter['ids']:'';
        $hidden = $this->filter['hidden'];
		$where = " where 1 AND `oi`.`id`=`oa`.`order_id`";
		if(!empty($order_id))
		{
			$where .= " and `oi`.`id` = " . $order_id;
        }
		if(!empty($order_sn))
		{
			$where .= " and `oi`.`order_sn`='".$order_sn."'";
		}
		if(!empty($consignee))
		{
			$where .= " and `oi`.`consignee` LIKE '".$consignee."%'";
		}
		if(!empty($order_pay_status))
		{
			$where .= " and `oi`.`order_pay_status`=".$order_pay_status;
		}
		if(!empty($no_order_status))
		{
			$where .= " and `oi`.`order_status`=".$no_order_status;
		}
        if(!empty($mobile))
        {
            $where .= " and `oi`.`mobile`='".$mobile."'";
        }
        if(!empty($start_time))
        {
            $where .= " and `oi`.`create_time` >= '".$start_time." 00:00:00'";
        }
        if(!empty($end_time))
        {
            $where .= " and `oi`.`create_time` <= '".$end_time." 23:59:59'";
        }
		if(!empty($department_id))
		{
			$where .= " and `oi`.`department_id` in (".$department_id.")";
		}
		if(!empty($ids))
		{
			$where .= " and `oi`.`order_sn` in($ids)";
		}
        if($hidden){
            $where .= " and `oi`.`hidden` <> 1 ";
        }
		$sql   = "SELECT COUNT(*) FROM `base_order_info` as `oi`,`app_order_account` as `oa` ".$where;
		$record_count   =  $this -> db ->getOne($sql);
		$page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

		$sql = "select * from `base_order_info` as oi,`app_order_account` as oa ".$where." ORDER BY `oa`.`id` desc LIMIT " . ($page - 1) * $page_size . ",$page_size";
		$res = $this -> db -> getAll($sql);
		$content = array("page" => $page, "pageSize" => $page_size, "recordCount" => $record_count, "data" => $res, "sql" => $sql,'pageCount'=>$page_count);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		if(!$res)
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单";
			$this -> return_msg = $content;
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
     * 查询订单列表分页信息
     * @param order_sn 订单号
     * @param delivery_status 配送状态
     * @return json
     */
	public function GetOrderListPage()
	{


		$s_time = microtime();
		//$this -> filter["page"] = 3;  //当前页
		$page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
		$page_size = intval($this -> filter["page_size"]) > 0 ? intval($this -> filter["page_size"]) : 15;

                $order_id = isset($this->filter['order_id']) ? intval(trim($this->filter['order_id'])) : 0 ;//订单id
		$order_sn = isset($this->filter['order_sn']) ? trim($this->filter['order_sn']) : '';//订单号
		$delivery_status = isset($this->filter['delivery_status']) ? intval(trim($this->filter['delivery_status'])) : 0;//订单配送状态
		$delivery_status_str = trim($this->filter['delivery_status_str']);//多个订单配送状态
		$order_status = intval(trim($this->filter['order_status']));//订单状态

		$customer_source_id =trim($this->filter['customer_source_id']);//
		$create_user =trim($this->filter['create_user']);//
		$is_print_tihuo =trim($this->filter['is_print_tihuo']);//
		$sales_channels_id =trim($this->filter['sales_channels_id']);//
		$create_time_start =trim($this->filter['create_time_start']);//
		$create_time_end =trim($this->filter['create_time_end']);//
		$delivery_address =trim($this->filter['delivery_address']);// 
        $has_company =$this->filter['has_company'];// 
        $shousuo =trim($this->filter['shousuo']);// 
		//根据款号  批量查询配货订单
		$style_sn=trim($this->filter['style_sn']);//
		if(!empty($style_sn)){
			$sql="select order_id from app_order_details where goods_sn in ({$style_sn})  group by order_id";
			$rec = $this -> db -> getAll($sql);
			if($rec){
				foreach($rec as $val){
					$order_id .= $val['order_id'].',';
				}
				$order_id = rtrim($order_id,',');
			}else{
				//无效订单号
				$order_id=0;
			}

		}


		$where = '';
                $str = '';
		if(!empty($order_id))
		{
			$str .= " `a`.`id` IN (".$order_id.")AND ";
       		 }
		if(!empty($order_sn))
		{
			$str .= " `a`.`order_sn` IN (".$order_sn.") AND ";
		}
		if(!empty($delivery_status))
		{
			$str .= " `a`.`delivery_status`=".$delivery_status." AND ";
		}
		if(!empty($order_status))
		{
			$str .= " `a`.`order_status`=".$order_status." AND ";
		}
		if(!empty($delivery_status_str))
		{
			$str .= " `a`.`delivery_status` IN (".$delivery_status_str.") AND ";
		}

		if(!empty($create_user))
		{
			$str .= " `a`.`create_user`='".$create_user."' AND ";
		}
		if($is_print_tihuo!='')
		{
			$str .= " `a`.`is_print_tihuo`=".$is_print_tihuo." AND ";
		}
		if(!empty($sales_channels_id))
		{
            $str1 = " `a`.`department_id`IN (".$sales_channels_id.") AND ";
			$str .= $str1;
            // $str .= " `a`.`department_id`IN (".$sales_channels_id.") AND ";
		}

        if(!empty($delivery_address))
            {
                //配货地址的
               $str =str_replace($str1, '', $str);
                if($delivery_address == '总部到客户' ){
                     $str .= " `ad`.`distribution_type` =2 AND ";
                }elseif(!empty($delivery_address)){
                    $str .= " `ad`.`shop_name` IN ('".$delivery_address."') AND ";
                }else{
                    $str .=" AND ";
                }
            } 


		if(!empty($customer_source_id))
		{
			$str .= " `a`.`customer_source_id`=".$customer_source_id." AND ";
		}
		 if(!empty($create_time_start))
		{
			$str .= " `a`.`create_time`>'".$create_time_start." 00:00:00' AND ";
		}
		if(!empty($create_time_end))
		{
			$str .= " `a`.`create_time`<'".$create_time_end." 23:59:59' AND ";
		}
		if(isset($this -> filter["apply_close"]))
		{
			$str .= " `a`.`apply_close` ='".$this -> filter["apply_close"]."' AND ";
		}

		if(isset($this -> filter["order_status"]))
		{
			$str .= " `a`.`order_status` ='".$this -> filter["order_status"]."' AND ";
		} 

        //待配货订单里 发货状态是已到店的是否可以配货??
/*        if(isset($this -> filter["send_good_status"]))
        {
            $str .= " `a`.`send_good_status` ='".$this -> filter["send_good_status"]."' AND ";
        }*/

		if($str != ''){
			$str = rtrim($str,"AND ");//这个空格很重要
			$where .=" WHERE ".$str;
		}


        if($shousuo ==''){
            $where =str_replace($str1, '', $where);
            $results = array_intersect(array(58,445), $has_company);
            if(!empty($results)){
                //包含配货地址为总部的
                 $has_company =implode("','", $has_company);
                  $sql ="SELECT count(*) from (SELECT * FROM 
                    (select `a`.*, `b`.`order_amount`,`ad`.shop_name,1 company_id,2 s_name FROM app_order.`base_order_info` AS `a` INNER JOIN app_order.`app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` LEFT JOIN app_order.app_order_address as ad on ad.order_id=a.id $where AND `ad`.`distribution_type` =2) AS aa
                     UNION ALL SELECT * FROM 
                    (select `a`.*, `b`.`order_amount`,`ad`.shop_name FROM app_order.`base_order_info` AS `a` INNER JOIN app_order.`app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` LEFT JOIN app_order.app_order_address as ad on ad.order_id=a.id $where) AS cc
                    LEFT JOIN
                    (SELECT s.company_id,sc.shop_name s_name FROM cuteframe.sales_channels s
                        LEFT JOIN cuteframe.shop_cfg as sc on sc.id=s.channel_own_id) AS bb ON cc.shop_name=bb.s_name where bb.company_id IN ('".$has_company."')) as dd";

                $record_count   =  $this -> db ->getOne($sql);
                $page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

                $sql ="SELECT DISTINCT * from (SELECT * FROM 
                    (select `a`.*, `b`.`order_amount`,`ad`.shop_name,1 company_id,2 s_name FROM app_order.`base_order_info` AS `a` INNER JOIN app_order.`app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` LEFT JOIN app_order.app_order_address as ad on ad.order_id=a.id $where AND `ad`.`distribution_type` =2) AS aa
                     UNION ALL SELECT * FROM 
                    (select `a`.*, `b`.`order_amount`,`ad`.shop_name FROM app_order.`base_order_info` AS `a` INNER JOIN app_order.`app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` LEFT JOIN app_order.app_order_address as ad on ad.order_id=a.id $where) AS cc
                    LEFT JOIN
                    (SELECT s.company_id,sc.shop_name s_name FROM cuteframe.sales_channels s
                        LEFT JOIN cuteframe.shop_cfg as sc on sc.id=s.channel_own_id) AS bb ON cc.shop_name=bb.s_name where bb.company_id IN ('".$has_company."')) AS dd ORDER BY `dd`.`id` DESC LIMIT " . ($page - 1) * $page_size . ",$page_size";

            }else{

                $has_company =implode("','", $has_company);
                  $sql ="SELECT count(*) from (SELECT * FROM 
                    (select `a`.*, `b`.`order_amount`,`ad`.shop_name FROM app_order.`base_order_info` AS `a` INNER JOIN app_order.`app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` LEFT JOIN app_order.app_order_address as ad on ad.order_id=a.id $where) AS cc
                    LEFT JOIN
                    (SELECT s.company_id,sc.shop_name s_name FROM cuteframe.sales_channels s
                        LEFT JOIN cuteframe.shop_cfg as sc on sc.id=s.channel_own_id) AS bb ON cc.shop_name=bb.s_name where bb.company_id IN ('".$has_company."')) as dd";

                $record_count   =  $this -> db ->getOne($sql);
                $page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

                $sql ="SELECT DISTINCT * from (SELECT * FROM 
                    (select `a`.*, `b`.`order_amount`,`ad`.shop_name FROM app_order.`base_order_info` AS `a` INNER JOIN app_order.`app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` LEFT JOIN app_order.app_order_address as ad on ad.order_id=a.id $where) AS cc
                    LEFT JOIN
                    (SELECT s.company_id,sc.shop_name s_name FROM cuteframe.sales_channels s
                        LEFT JOIN cuteframe.shop_cfg as sc on sc.id=s.channel_own_id) AS bb ON cc.shop_name=bb.s_name where bb.company_id IN ('".$has_company."')) AS dd ORDER BY `dd`.`id` DESC LIMIT " . ($page - 1) * $page_size . ",$page_size";

            }
        }else{
           
            if($delivery_address == '总部到客户' ){
                $sql ="SELECT COUNT(*) FROM `base_order_info` AS `a` INNER JOIN `app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` LEFT JOIN app_order_address as ad on ad.order_id=a.id  WHERE  `a`.`order_status`=2 AND  `a`.`delivery_status` IN (2,3) AND  `ad`.`distribution_type` =2 AND  `a`.`apply_close` ='0'";
                 $record_count   =  $this -> db ->getOne($sql);
                 $page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

                  $sql ="SELECT `a`.*, `b`.`order_amount`,`ad`.shop_name FROM `base_order_info` AS `a` INNER JOIN `app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` LEFT JOIN app_order_address as ad on ad.order_id=a.id  $where ORDER BY `a`.`id` DESC LIMIT " . ($page - 1) * $page_size . ",$page_size";

            }else{
                $results = array_intersect(array(58,445), $has_company);
                if(!empty($results)){
                //包含配货地址为总部的
                 $has_company =implode("','", $has_company);
                  $sql ="SELECT count(*) from (SELECT * FROM 
                    (select `a`.*, `b`.`order_amount`,`ad`.shop_name,1 company_id,2 s_name FROM app_order.`base_order_info` AS `a` INNER JOIN app_order.`app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` LEFT JOIN app_order.app_order_address as ad on ad.order_id=a.id $where AND `ad`.`distribution_type` =2) AS aa
                     UNION ALL SELECT * FROM 
                    (select `a`.*, `b`.`order_amount`,`ad`.shop_name FROM app_order.`base_order_info` AS `a` INNER JOIN app_order.`app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` LEFT JOIN app_order.app_order_address as ad on ad.order_id=a.id $where) AS cc
                    LEFT JOIN
                    (SELECT s.company_id,sc.shop_name s_name FROM cuteframe.sales_channels s
                        LEFT JOIN cuteframe.shop_cfg as sc on sc.id=s.channel_own_id) AS bb ON cc.shop_name=bb.s_name where bb.company_id IN ('".$has_company."')) as dd";

                $record_count   =  $this -> db ->getOne($sql);
                $page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

                $sql ="SELECT DISTINCT * from (SELECT * FROM 
                    (select `a`.*, `b`.`order_amount`,`ad`.shop_name,1 company_id,2 s_name FROM app_order.`base_order_info` AS `a` INNER JOIN app_order.`app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` LEFT JOIN app_order.app_order_address as ad on ad.order_id=a.id $where AND `ad`.`distribution_type` =2) AS aa
                     UNION ALL SELECT * FROM 
                    (select `a`.*, `b`.`order_amount`,`ad`.shop_name FROM app_order.`base_order_info` AS `a` INNER JOIN app_order.`app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` LEFT JOIN app_order.app_order_address as ad on ad.order_id=a.id $where) AS cc
                    LEFT JOIN
                    (SELECT s.company_id,sc.shop_name s_name FROM cuteframe.sales_channels s
                        LEFT JOIN cuteframe.shop_cfg as sc on sc.id=s.channel_own_id) AS bb ON cc.shop_name=bb.s_name where bb.company_id IN ('".$has_company."')) AS dd ORDER BY `dd`.`id` DESC LIMIT " . ($page - 1) * $page_size . ",$page_size";

            }else{

                $has_company =implode("','", $has_company);
                  $sql ="SELECT count(*) from (SELECT * FROM 
                    (select `a`.*, `b`.`order_amount`,`ad`.shop_name FROM app_order.`base_order_info` AS `a` INNER JOIN app_order.`app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` LEFT JOIN app_order.app_order_address as ad on ad.order_id=a.id $where) AS cc
                    LEFT JOIN
                    (SELECT s.company_id,sc.shop_name s_name FROM cuteframe.sales_channels s
                        LEFT JOIN cuteframe.shop_cfg as sc on sc.id=s.channel_own_id) AS bb ON cc.shop_name=bb.s_name where bb.company_id IN ('".$has_company."')) as dd";

                $record_count   =  $this -> db ->getOne($sql);
                $page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

                $sql ="SELECT DISTINCT * from (SELECT * FROM 
                    (select `a`.*, `b`.`order_amount`,`ad`.shop_name FROM app_order.`base_order_info` AS `a` INNER JOIN app_order.`app_order_account` AS `b` ON `a`.`id`=`b`.`order_id` LEFT JOIN app_order.app_order_address as ad on ad.order_id=a.id $where) AS cc
                    LEFT JOIN
                    (SELECT s.company_id,sc.shop_name s_name FROM cuteframe.sales_channels s
                        LEFT JOIN cuteframe.shop_cfg as sc on sc.id=s.channel_own_id) AS bb ON cc.shop_name=bb.s_name where bb.company_id IN ('".$has_company."')) AS dd ORDER BY `dd`.`id` DESC LIMIT " . ($page - 1) * $page_size . ",$page_size";

                }
            }

        }

		$res = $this -> db -> getAll($sql);


		foreach($res as $key => $val){
			$order_id = $val['id'];
			$action_sql="select create_time from app_order_action where order_id = {$order_id} order by action_id desc;";
			$last_time = $this->db->getOne($action_sql);
			if(empty($last_time)){
				$last_time = '0000-00-00 00:00:00';
			}
			$res[$key]['last_time'] = $last_time;
		}


		$content = array("page" => $page, "pageSize" => $page_size, "recordCount" => $record_count, "data" => $res, "sql" => $sql,'pageCount'=>$page_count);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		if(!$res)
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单";
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
    //外部订单需要
    public function getRelOn(){
        $s_time = microtime();
        if(isset($this->filter['order_id']) && !empty($this -> filter["order_id"]))
        {
            $sql ="SELECT `out_order_sn` FROM WHERE order_id=".$this->filter['order_id'];
            $res = $this -> db -> getAll($sql);

        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "没有order_id";
            $this -> return_msg = array();
            $this->display();
        }

        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        if(!$res){
            $this -> error =2;
            $this -> return_sql = '';
            $this -> error_msg = "外部订单获取出错";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error =0;
            $this -> return_sql = $sql;
            $this -> error_msg = "成功";
            $this -> return_msg = $res;
            $this->display();
        }

    }


     /**
     * 查询未收货订单
     * @param order_sn 订单号
     * @param delivery_status 配送状态
     * @return json
     */
	public function getNotReceivingOrder()
	{
		$s_time = microtime();
		$page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
		$page_size = (isset($this -> filter["page_size"]) && $this -> filter["page_size"]) ? intval($this -> filter["page_size"]) : 10;

		$str = '`oi`.`id`=`od`.`order_id` ';
        $where = '';

        if(isset($this -> filter['hidden']) && $this -> filter['hidden'] != ''){
            $str .= " AND oi.hidden = ".$this -> filter['hidden']." ";
        }

		if(!empty($this -> filter["goods_status"]))
		{
			$str .=" AND `od`.`details_status` IN ('".join("','",$this -> filter["goods_status"])."')";
		}
        if(!empty($this -> filter["arrival_status"]))
		{
			$str .=" AND `od`.`send_good_status` IN ('".join("','",$this -> filter["arrival_status"])."')";
		}
        if(!empty($this -> filter["order_status"]))
		{
			$str .=" AND `oi`.`order_status` IN ('".join("','",$this -> filter["order_status"])."')";
		}
        if(!empty($this -> filter["shipping_status"]))
		{
			$str .=" AND `oi`.`send_good_status` IN ('".join("','",$this -> filter["shipping_status"])."')";
		}
        if(!empty($this -> filter["order_sn"]))
		{
			$str .=" AND `oi`.`order_sn` like \"%".addslashes($this->filter['order_sn'])."%\"";
		}
        if(!empty($this -> filter["consignee"]))
		{
			$str .=" AND `oi`.`consignee` like \"%".addslashes($this->filter['consignee'])."%\"";
		}

        if(!empty($this -> filter['department_id'])){
            if(is_array($this -> filter['department_id'])){
                $str .=" AND `oi`.`department_id` in (". implode(',', $this -> filter['department_id']).")";
            }
        }


		if($str != ''){
			$str = rtrim($str,"AND ");//这个空格很重要
			$where .=" WHERE ".$str;
		}



		$count_sql   = "SELECT COUNT(*) FROM `base_order_info` AS `oi`, `app_order_details` AS `od` ".$where;
		$record_count   =  $this -> db ->getOne($count_sql);
		$page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

		$sql = "select `oi`.*, `od`.* FROM `base_order_info` AS `oi`, `app_order_details` AS `od` ".$where." order by `oi`.`id` desc LIMIT " . ($page - 1) * $page_size . ",$page_size";
        //file_put_contents('getNotReceivingOrder',$sql);
		$res = $this -> db -> getAll($sql);
		$content = array("page" => $page, "pageSize" => $page_size, "recordCount" => $record_count, "data" => $res, "sql" => $sql,'pageCount'=>$page_count);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		if(!$res)
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单";
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
    * 查询订单信息 (关联支付 app_order_account)
    * @param $order_id
    * @return json
    */
	public function GetOrderInfo()
	{
        //var_dump(__FUNCTION__);
		$s_time = microtime();
		$where = "";

		//$order_id = 60;
		if(isset($this->filter['order_id']) && !empty($this->filter['order_id']))
		{
            $order_id=intval(trim($this->filter['order_id']));
			$where .= " AND a.`id` = " . $order_id;
        }
		if(isset($this->filter['order_sn']) && !empty($this -> filter["order_sn"]))
		{
			$where .=" AND a.`order_sn` ='".$this -> filter["order_sn"]."'";
		}


        //查询商品详情
       //$sql = "select a.*,b.`order_amount`,b.`money_paid`,b.`money_unpaid`,r.out_order_sn from `base_order_info` AS a,  `app_order_account` AS b , rel_out_order AS r WHERE a.`id`=b.`order_id` AND a.`id`=`r`.`order_id`" .$where." ;";
       $sql = "SELECT a . * , b.`order_amount` , b.`money_paid` , b.`money_unpaid` , (select r.out_order_sn from rel_out_order r where r.order_id=a.id limit 1) as out_order_sn FROM `base_order_info` AS a, `app_order_account` AS b WHERE a.`id` = b.`order_id` " .$where." ;";
       $row = $this->db->getRow($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单";
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
    * 查询订单信息
    * @param $order_id
    * @return json
    */
	public function GetOrderInfoRow()
	{
		$s_time = microtime();
                $where = '';
		if(isset($this->filter['order_id']) && !empty($this->filter['order_id']))
		{
			$order_id=intval(trim($this->filter['order_id']));
			$where .= " `a`.`id` = " . $order_id . " AND ";
		}
		if(isset($this->filter['order_sn']) && !empty($this -> filter["order_sn"]))
		{
			$where .=" `a`.`order_sn` ='".$this -> filter["order_sn"]."' AND ";
		}
		if($where != ''){
			$where = rtrim($where, ' AND ');
			$where = ' WHERE '.$where;
		}

        //查询商品详情
       $sql = "SELECT `a`.* , b.`order_amount`,b.`money_paid`,b.`money_unpaid`,ar.`consignee` as shouhuoren,ar.`distribution_type`,b.`coupon_price`,(select GROUP_CONCAT(DISTINCT rod.out_order_sn) from rel_out_order as rod where rod.order_id=a.id group by rod.order_id) as out_order_sn,`sc`.`channel_class` FROM `base_order_info` AS `a` LEFT JOIN `app_order_account` AS `b` ON `a`.`id`= `b`.`order_id` LEFT JOIN `app_order_address` as ar ON `a`.`id`=`ar`.`order_id` left join `cuteframe`.`sales_channels` `sc` on `sc`.`id` = `a`.`department_id`" .$where." limit 1 ;";


       $row = $this->db->getRow($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}


    /*
     * 添加订单操作日志
     */
    public function addOrderAction(){
        $s_time = microtime();
		$where = "";

        if(!isset($this->filter['order_id'])){
            $this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "订单id不能为空";
			$this->display();
        }
        $order_id=intval(trim($this->filter['order_id']));

        $action_field = " `order_id`, `order_status`, `shipping_status`, `pay_status`,`create_time`,`create_user`,`remark`";
        $action_value = "".$order_id." ,".$this->filter['order_status']." ,".$this->filter['shipping_status']." ,".$this->filter['pay_status'].", '".$this->filter['create_time']."' , '".$this->filter['create_user']."', '".$this->filter['remark']."' ";
        $sql = "INSERT INTO `app_order_action` (" . $action_field . ") VALUES (". $action_value .")";
        $res = $this->db->query($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$res){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $res;
			$this->display();
		}
    }

    	/**
    	* 跟新订单的 打印提货单 状态
    	* @param order_sn string  格式：123,456,789
    	* @hlc
    	*/
    	public function updatePrintTihuo(){
		$s_time = microtime();
		$where = "";
		if(!isset($this->filter['order_sn'])){
			$this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "订单号order_sn不能为空";
			$this->display();
		}
		$order_sn_arr = explode(',' , trim($this->filter['order_sn'])) ;
		foreach($order_sn_arr as $order_sn){
			$sql = "UPDATE `base_order_info` SET `is_print_tihuo` = 1 WHERE `order_sn` = '{$order_sn}'";
			$this->db->query($sql);
		}

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		$this -> error = 0;
		$this -> return_sql = $sql;
		$this -> return_msg = '操作成功';
		$this->display();

    	}

	/*------------------------------------------------------ */
    //-- 判断订单号是否有效
    //-- by linian
    /*------------------------------------------------------ */
	public function CheckOrdersn()
	{
                $order_err =$order_arr= array();
		//若 数组存在 遍历查询是否是 有效的订单号
		if(isset($this->filter['order_sn']) && !empty($this -> filter["order_sn"]))
		{
			$order_sn_arr =$this ->filter["order_sn"];
			foreach($order_sn_arr as $v){
				//查询订单号

				$sql = "select order_sn,delivery_status from `base_order_info`  WHERE `order_sn`='{$v}'";

				$row = $this->db->getRow($sql);
				/*				$row=var_export($row,true);
                                Util::L($row);*/

				//把有效的订单号存进数组$order_arr[]
				if(!$row){
					$order_err[]=$v;
					// 无效的数组存进$order_err[]
				}else{

					$order_arr[$row['order_sn']]=$row['delivery_status'];


				}
			}

			//订单为空 返回报错信息
		}else{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg ="订单号不可以为空";
			$this->display();
		}

		$this -> error = 0;
		$this -> return_sql = $sql;
		//返回正确的订单号$order_arr 和 错误的订单号$order_err
		$this -> error_msg = array('order_arr'=>$order_arr,'order_err'=>$order_err);
		$this->display();
	}


    /*------------------------------------------------------ */
		//-- 更改订单配送状态
		//-- by linian
		/*------------------------------------------------------ */
	public function EditOrderdeliveryStatus()
	{

		//若 接收到的数据不为空 则拼接where条件
		if(isset($this->filter['order_sn']) && !empty($this -> filter["order_sn"]))
		{
			$data = $this ->filter["order_sn"];
		}else{
			$this -> error = 1;
			$this -> return_msg = "订单号不能为空";
			$this->display();
		}
		if(isset($this->filter['delivery_status']) && !empty($this -> filter["delivery_status"]))
		{
			$delivery_status = $this -> filter["delivery_status"];
		}else{
			$this -> error = 1;
			$this -> return_msg = "配货状态不能为空";
			$this->display();
		}
		//遍历订单数组更新
		foreach($data as $v){
			//批量更新订单配送状态
	        $sql = "UPDATE `base_order_info` SET `delivery_status`='{$delivery_status}' WHERE `order_sn`='{$v}'";
			$row=$this->db->query($sql);
		}

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> return_msg = "编辑失败";
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = "编辑成功";
			$this->display();
		}
	}

	/**
	* 变更订单的发货状态 （数字字典 order.send_good_status） @BY CaoCao
	*/
	public function UpdateSendGoodStatus(){
		//若 接收到的数据不为空 则拼接where条件
		if(isset($this->filter['order_sn']) && !empty($this -> filter["order_sn"]))
		{
			$data = $this ->filter["order_sn"];
		}else{
			$this -> error = 1;
			$this -> return_msg = "订单号不能为空";
			$this->display();
		}
		if(isset($this->filter['send_good_status']) && !empty($this -> filter["send_good_status"]))
		{
			$send_good_status = $this -> filter["send_good_status"];
		}else{
			$this -> error = 1;
			$this -> return_msg = "发货状态不能为空";
			$this->display();
		}
		//遍历订单数组更新
		foreach($data as $v){
			//批量更新订单配送状态
			$sql = "UPDATE `base_order_info` SET `send_good_status`='{$send_good_status}' WHERE `order_sn`='{$v}'";
			$row=$this->db->query($sql);
		}
		// file_put_contents('c:/a.txt',$sql,FILE_APPEND);
		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> return_msg = "修改订单发货状态失败";
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = "修改订单发货状态成功";
			$this->display();
		}
	}

    public function EditSendGoodsStatus()
    {

        //若 接收到的数据不为空 则拼接where条件
        if(isset($this->filter['order_sn']) && !empty($this -> filter["order_sn"]))
        {
            $data = $this ->filter["order_sn"];
        }else{
            $this -> error = 1;
            $this -> return_msg = "订单号不能为空";
            $this->display();
        }
        if(isset($this->filter['send_good_status']) && !empty($this -> filter["send_good_status"]))
        {
            $send_good_status = $this -> filter["send_good_status"];
        }else{
            $this -> error = 1;
            $this -> return_msg = "发货状态不能为空";
            $this->display();
        }

        //如果传过来的是已到店，现在订单发货状态是：已发货，则不做更改发货状态处理 调拨单

        if ($send_good_status == 5){
            foreach ($data as $order_sn){
                $sql = "select `send_good_status` from `base_order_info` where `order_sn`='$order_sn'";
                $res = $this->db->getRow($sql);
                $send_good_status2 = $res['send_good_status'];
            }
            if($send_good_status2 == 2){//已发货
                $this -> error = 0;
                $this -> return_sql = $sql;
                $this -> return_msg = "编辑成功";
                $this->display();
            }
        }

        //遍历订单数组更新
        foreach($data as $v){
            //批量更新订单送货状态
            $sql = "UPDATE `base_order_info` SET `send_good_status`='{$send_good_status}' WHERE `order_sn`='{$v}'";
            $row=$this->db->query($sql);
        }

        //返回信息
        if(!$row){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> return_msg = "编辑失败";
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = "编辑成功";
            $this->display();
        }
    }

    /**
     * 根据订单号获取订单信息
	 * is_return 必须是数字类型的，不可以传字符串类型的。
     * @return json
     */
    public function GetOrderInfoByOrdersn() {
        $s_time = microtime();
        $where = '';
       // $sql = "SELECT * FROM `base_order_info` AS `a` LEFT JOIN `app_order_details` AS `b` ON `a`.`id` = `b`.`order_id` WHERE 1"; //暂时用＊号
       //查看之前的手寸
        $sql = "SELECT a.*,b.*,s.shoucun,s.company_id,s.company,s.warehouse,s.warehouse_id FROM `base_order_info` AS `a` LEFT JOIN `app_order_details` AS `b` ON `a`.`id` = `b`.`order_id` left join`warehouse_shipping`.`warehouse_goods` s ON b.goods_id=s.goods_id WHERE 1"; //暂时用＊号
        if (!empty($this->filter['order_sn'])) {
            $sql .= " and `order_sn` = '{$this->filter['order_sn']}'";
        }else{
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = "订单号不能为空!";
            $this->return_msg = array();
            $this->display();
        }
        if (isset($this->filter['is_return']) && ($this->filter['is_return'] !='' || $this->filter['is_return'] === 0)) {
            $sql .= " and `b`.`is_return` = {$this->filter['is_return']}";
        }
        $data['data'] = $this->db->getAll($sql);
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if (!$data['data']) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = "未查询到此商品";
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        }
    }

    //根据order_id或者order_sn取出商品明细(带分页)
    public function GetOrderInfoByOrdersnPage() {

        $s_time = microtime();
        $page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
        $page_size = intval($this -> filter["page_size"]) > 0 ? intval($this -> filter["page_size"]) : 2;
        if (empty($this->filter['order_sn'])&&empty($this->filter['order_id'])) {
            $this->error = 1;
            $this->error_msg = "订单号或者订单id不能为空!";
            $this->return_msg = array();
            $this->display();
        }

        if($this->filter['order_sn']){
        $csql   = "SELECT COUNT(*)FROM `base_order_info` AS `a` LEFT JOIN `app_order_details` AS `b` ON `a`.`id` = `b`.`order_id` WHERE a.order_sn=".$this->filter['order_sn'];
        $record_count   =  $this -> db ->getOne($csql);
        $page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

        $sql = "SELECT * FROM `base_order_info` AS `a` INNER JOIN `app_order_details` AS `b` ON `a`.`id` = `b`.`order_id` WHERE a.order_sn=". $this->filter['order_sn']." ORDER BY b.id desc LIMIT " . ($page - 1) * $page_size . ",$page_size";
        $res = $this -> db -> getAll($sql);
        $content = array("page" => $page, "pageSize" => $page_size, "recordCount" =>
          $record_count, "data" => $res, "sql" => $sql,'pageCount'=>$page_count);
        }else{
            $csql   = "SELECT COUNT(*)FROM  `app_order_details` AS `b`  WHERE b.order_id=".$this->filter['order_id'];
            $record_count   =  $this -> db ->getOne($csql);
            $page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

            $sql = "SELECT * FROM `app_order_details` AS `b` WHERE b.order_id=". $this->filter['order_id']." ORDER BY b.id desc LIMIT " . ($page - 1) * $page_size . ",$page_size";
            $res = $this -> db -> getAll($sql);
            $content = array("page" => $page, "pageSize" => $page_size, "recordCount" =>
                $record_count, "data" => $res, "sql" => $sql,'pageCount'=>$page_count);
        }
       // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if (!$content['data']) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = "未查询到此商品";
            $this->return_msg =$content;
            $this->display();
        } else {
           $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $content;
            $this->display();
        }
    }


    /**
     * 更新订单明细表中的状态
     * @param array $update_data 此商品的对的所以状态，指针对货品
     * app_order_details
     */

    //TODO apps\processor\model\ApiSalesModel.php 使用接口的入口

    public function UpdateOrderDetailStatus() {
        $s_time = microtime();
        $arrids = array();
        //布产状态为出厂状态则把这个order_id记录上    然后进行判断改订单状态
        if (isset($this->filter['update_data'])) {
            $data = $this->filter['update_data'];
            if(count($data) > 0){
            	$this->error = 1;
                foreach ($data as $val){
                    $all_ids[] = $val['id'];
                    if($val['buchan_status']==9 || $val['buchan_status']==11){
                        $arrids[]=$val['id'];
                    }
                    $where = " `id` = {$val['id']}";
                    unset($val['id']);
                    $res = $this -> db -> autoExecute('app_order_details',$val,'UPDATE',$where, "SILENT");
                }
                //通过商品id取出order_id 并通过orderid 取不是都出厂的商品  判断有无  如果没有则把已付款订单改为待配货
                if(count($arrids) > 0){
                    $arrids=implode(',',$arrids);
                    $sql1 = "SELECT `order_id` FROM `app_order_details` WHERE `id` IN (".$arrids.")";
                    $res1 = $this->db->getAll($sql1);
                    $order_id_arr = array();
                    foreach($res1 as $k=>$v){
                        $sql2 ="SELECT COUNT(id) FROM `app_order_details` WHERE `order_id`=".$v['order_id']."  AND (`buchan_status`!=9 and `buchan_status`!=11) AND `is_stock_goods`=0";
                        $res2 =$this->db->getOne($sql2);
                        if(!$res2){
                            $sql3 ="UPDATE `base_order_info` SET `delivery_status`=2 WHERE `id`=".$v['order_id']." AND (`order_pay_status`=3 or `order_pay_status`=4)";
                           $res3 =  $this->db->query($sql3);
                        }
                    }
                }

                //更改订单状态：当商品的布产状态都为：生产中，订单状态变成生产中；都已出厂，则订单状态为已出厂；当都为不需要配货，都为不需要配货，其他状态未操作
                if(count($all_ids) > 0){
                    $allids=implode(',',$all_ids);
                    $sql1 = "SELECT `order_id` FROM `app_order_details` WHERE `id` IN (".$allids.")";
                    $res1 = $this->db->getAll($sql1);
                    $order_id_arr = array();
                    foreach($res1 as $k=>$v){
                        $order_id_arr[$v['order_id']] = $v['order_id'];
                    }

                    foreach($order_id_arr as $v){
                        $order_id = $v;
                        $sql3 ="SELECT id,buchan_status FROM `app_order_details` WHERE `order_id`=".$order_id."  AND `is_stock_goods`=0";
                        $data_detail =$this->db->getAll($sql3);
                        $all_detail_num = count($data_detail);

                        $new_data_detail = array();
                        //商品的布产状态都为生产中：4 或者不需要布产；11
                        foreach ($data_detail as $dd_val){
                            $new_data_detail[$dd_val['buchan_status']][]= 1;
                        }
                        //获取订单的状态
                        $sql3= "SELECT `buchan_status` FROM `base_order_info` WHERE `id` = ".$order_id;
                        $data_order =$this->db->getRow($sql3);
                        $order_buchan_status = $data_order['buchan_status'];
                        switch ($order_buchan_status){
                             //订单的状态:允许布产2
                            case 2:
                                //判断订单中期货都为生产中：4，则订单的状态也改为:生产中：3
                                if(isset($new_data_detail[4])){
                                    if($all_detail_num == count($new_data_detail[4])){
                                        $sql5 ="UPDATE `base_order_info` SET `buchan_status`=3 WHERE `id`=".$order_id;
                                        $res5 =  $this->db->query($sql5);
                                    }
                                }
                                 //判断订单中期货都为:不需要布产：11，则订单的状态也改为:不需要布产：5
                                if(isset($new_data_detail[11])){
                                    if($all_detail_num == count($new_data_detail[11])){
                                         $sql5 ="UPDATE `base_order_info` SET `buchan_status`=5  WHERE `id`=".$order_id;
                                        $res5 =  $this->db->query($sql5);
                                    }
                                }

                                break;
                                //订单状态：生产中：3
                            case 3;
                                //判断订单中期货都为:已出厂 9，则订单的状态也改为:不需要布产：4
                                if(isset($new_data_detail[9])){
                                    if($all_detail_num == count($new_data_detail[9])){
                                        $sql5 ="UPDATE `base_order_info` SET `buchan_status`=4 WHERE `id`=".$order_id;
                                        $res5 =  $this->db->query($sql5);
                                    }
                                }
                                break;

                            case 4:
                                break;
                            case 5:
                                break;
						}
					}
				}
             }else{
		        $this->error = 1;
		        $this->return_sql = '';
		        $this->error_msg = "update_data是个空数组";
		        $this->return_msg = 0;
		        $this->display();
            }
        }else{
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数update_data";
            $this->return_msg = 0;
            $this->display();
        }
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
		if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $res;
			$this -> error_msg = "失败";
			$this -> return_msg = array();
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $res;
			$this -> return_msg = '成功';
			$this->display();
		}
    }


	/**
	* 根据订单号 打印提货单,获取提货单基本信息
	* @param $order_sn
	**/
	public function GetPrintBillsInfo()
	{
		$s_time = microtime();
		if( !isset($this->filter['order_sn']) && empty($this->filter['order_sn']))
		{
			$this ->error = 1;
			$this ->error_msg = "订单号不能为空";
			$this ->return_msg = array();
			$this->display();
		}
		$order_sn = $this->filter['order_sn'];
		$sql = "SELECT  `a`.`id`,`a`.`order_sn`, `a`.`user_id`, `a`.`delivery_status`,`a`.`department_id`,`a`.`customer_source_id`,`a`.`send_good_status`, `a`.`order_remark`, `a`.`order_status`, `a`.`order_pay_status`,`a`.`order_pay_type`,`a`.`consignee` order_consignee,`b`.`order_amount`, `b`.`money_paid`, `b`.`money_unpaid`,`b`.`goods_amount`,`b`.`favorable_price`,`b`.`coupon_price`,c.`consignee`, `c`.`distribution_type`, `c`.`express_id`, `c`.`address`,`c`.`shop_type`,`c`.`shop_name`, `d`.`invoice_title`, `d`.`invoice_amount`, `d`.`invoice_address`, `d`.`invoice_num`, `d`.`is_invoice`,`go`.gift_id,`go`.remark,`go`.gift_num FROM  `app_order_account` AS `b`, `app_order_address` AS `c`,`base_order_info` AS `a` LEFT JOIN `app_order_invoice` AS d ON `a`.`id` = `d`.`order_id` LEFT JOIN `rel_gift_order` AS `go` ON `go`.`order_id`=`a`.`id`  WHERE `a`.`order_sn` = '{$order_sn}' AND `a`.`id` = `b`.`order_id` AND `a`.`id` = `c`.`order_id`";
		$row = $this->db->getRow($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单";
			$this -> return_msg = array();
			$this->display();
		}else{
			$giftstr='';
			if(isset($row['gift_id']) && !empty($row['gift_id'])){
				$gifta = explode(',',$row['gift_id']);
				$giftn = explode(',',$row['gift_num']);

				foreach($gifta as $k=>$v){
				   // if(in_array($k,$this->gifts)){
						if(isset($this->gifts[$v]))
						$giftstr.= $this->gifts[$v];
						if(isset($giftn[$k]))
						$giftstr.= $giftn[$k].'个,';
					//}
				}
				$giftstr=trim($giftstr,',');
			 }
			$row['gift']=$giftstr;

			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}

    /**
     * 查询订单列表分页信息（关联支付状态）
     * @param order_sn order_pay_status
     * @return json
     */
    public function GetOrderInvoiceList()
    {
        $s_time = microtime();
        //$this -> filter["page"] = 3;  //当前页
        $page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
        $page_size = intval($this -> filter["page_size"]) > 0 ? intval($this -> filter["page_size"]) : 15;

        $order_sn       =trim($this->filter['order_sn']);//订单号
        $title          =trim($this->filter['title']);//发票抬头
        $invoice_num    =trim($this->filter['invoice_num']);//发票号
        $price_start    =trim($this->filter['price_start']);//金额小
        $price_end      =trim($this->filter['price_end']);//金额大
        $start_time      =trim($this->filter['start_time']);
        $end_time      =trim($this->filter['end_time']);
        $create_user      =trim($this->filter['create_user']);
        $content      =trim($this->filter['content']);
        $status      =trim($this->filter['status']);
        $type      =trim($this->filter['type']);

        $where = " where 1 and `re`.`is_invoice` = 1 ";
        if(!empty($title))
        {
            $where .= " and `re`.`invoice_title` LIKE '%" . $title."%'";
        }
        if(!empty($create_user))
        {
            $where .= " and `re`.`create_user` LIKE '" . $create_user."%'";
        }
        if(!empty($content))
        {
            $where .= " and `re`.`invoice_content` LIKE '" . $content."%'";
        }
        if(!empty($order_sn))
        {
            $where .= " and `oi`.`order_sn`='".$order_sn."'";
        }
        if(!empty($status))
        {
            $where .= " and `re`.`invoice_status`='".$status."'";
        }
        /*if(!empty($type))
        {
            $where .= " and `re`.`type`='".$type."'";
        }*/
        if(!empty($invoice_num))
        {
            $where .= " and `re`.`invoice_num`='".$invoice_num."'";
        }
        if(!empty($price_start))
        {
            $where .= " and `re`.`invoice_amount` >= '".$price_start."'";
        }
        if(!empty($price_end))
        {
            $where .= " and `re`.`invoice_amount` <= '".$price_end."'";
        }
        if(!empty($start_time))
        {
            $where .= " and `re`.`create_time` >= '".$start_time." 00:00:00'";
        }
        if(!empty($end_time))
        {
            $where .= " and `re`.`create_time` <= '".$end_time." 23:59:59'";
        }
        //$this -> return_sql = $where;
        //$this->display();
        $sql   = "SELECT COUNT(*) FROM `app_order_invoice` as `re` LEFT JOIN `base_order_info` as `oi` on `re`.`order_id` = `oi`.id LEFT JOIN `app_order_account` as `ao` on `oi`.`id` = `ao`.id ".$where;
        $record_count   =  $this -> db ->getOne($sql);
        $page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

        $sql = "SELECT `oi`.`id`,`oi`.`order_sn`,`oi`.`delivery_status`,`oi`.`send_good_status`,`oi`.`order_status`,`oi`.`buchan_status`,`oi`.order_pay_status,`re`.`invoice_status`,`re`.`invoice_num`,`ao`.`order_amount`,`ao`.`money_paid`,`ao`.`money_unpaid`,`ao`.`goods_return_price`,`ao`.`real_return_price`,`ao`.`shipping_fee`,`ao`.`card_fee`,`ao`.`pack_fee`,`ao`.`pay_fee`,`ao`.`insure_fee`,`ao`.`goods_amount`,`ao`.`coupon_price`,`ao`.`favorable_price` FROM `app_order_invoice` as `re` LEFT JOIN `base_order_info` as `oi` on `re`.`order_id` = `oi`.id LEFT JOIN `app_order_account` as `ao` on `oi`.`id` = `ao`.id  ".$where." ORDER BY `re`.`id` desc LIMIT " . ($page - 1) * $page_size . ",$page_size";
        $res = $this -> db -> getAll($sql);
        $content = array("page" => $page, "pageSize" => $page_size, "recordCount" => $record_count, "data" => $res, "sql" => $sql,'pageCount'=>$page_count);
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        if(!$res)
        {
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到订单发票信息";
            $this -> return_msg = $content;
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
	 * 订单号获取客户姓名
	 */
	public function GetConsigneeByorder_sn(){
		$s_time = microtime();
		if(!isset($this->filter['order_sn']) || empty($this->filter['order_sn'])){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "缺少参数.order_sn";
			$this -> return_msg = array();
			$this->display();
		}else{
			$order_sn = $this->filter['order_sn'];
		}
		$sql = "SELECT `consignee` FROM `base_order_info` WHERE `order_sn` = '".$order_sn."'";
		$res = $this->db->getOne($sql);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
		if(!$res)
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询客户信息";
			$this -> return_msg = '';
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $res;
			$this->display();
		}

	}


    /**
     * 订单号获取客户姓名
     */
    public function GetConsigneeByorder_sn_copy(){
        $s_time = microtime();
        if(!isset($this->filter['order_sn']) || empty($this->filter['order_sn'])){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "缺少参数.order_sn";
            $this -> return_msg = array();
            $this->display();
        }else{
            $order_sn = $this->filter['order_sn'];
        }
        $sql = "SELECT `oi`.`consignee`,`sc`.`channel_class` FROM `app_order`.`base_order_info` `oi` inner join `cuteframe`.`sales_channels` `sc` on `oi`.`department_id` = `sc`.`id` WHERE `oi`.`order_sn` = '".$order_sn."'";
        $res = $this->db->getRow($sql);
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        if(!$res)
        {
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询客户信息";
            $this -> return_msg = '';
            $this->display();
        }
        else
        {
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }

    }



	/**
	 * 订单日志接口
	 */
	public function mkOrderLog()
	{
		$s_time = microtime();
		if(!isset($this->filter['remark']) || !isset($this->filter['create_user'])){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "缺少参数";
			$this -> return_msg = array();
			$this->display();
		}

		if(!isset($this->filter['order_id']) || isset($this->filter['order_sn'])){
			$sql = "SELECT `id` FROM `base_order_info` WHERE `order_sn` = '".$this->filter['order_sn']."'";
			$newdo['order_id'] = $this->db->getOne($sql);
		}

		if(isset($this->filter['order_id'])){
			$newdo['order_id'] = $this->filter['order_id'];
		}

		$newdo['remark'] = $this->filter['remark'];
		$newdo['create_user'] = $this->filter['create_user'];
		$newdo['create_time'] = date('Y-m-d H:i:s');

		$newdo['order_status'] = (isset($this->filter['order_status']))?$this->filter['order_status']:'0';
		$newdo['shipping_status'] = (isset($this->filter['shipping_status']))?$this->filter['shipping_status']:'0';
		$newdo['pay_status'] = (isset($this->filter['pay_status']))?$this->filter['pay_status']:'0';

		$sql = "INSERT INTO `app_order_action` (";
		$label = '';$val = '';
		foreach ($newdo as $k=>$v) {
			$label .= "`".$k."`,";
			$val .= "'".$v."',";
		}
		$label = rtrim($label,',');$val = rtrim($val,',');
		$sql .= $label.") VALUES (".$val.")";
		$res = $this->db->query($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "失败";
			$this -> return_msg = $res;
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $res;
			$this->display();
		}

	}


    /*
     * 添加订单操作日志
     */
    public function getOrderActionList(){
        $s_time = microtime();
		$where = "";

        if(!isset($this->filter['order_id'])){
            $this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "订单id不能为空";
			$this->display();
        }
        $order_id=intval(trim($this->filter['order_id']));
		$where = " order_id = $order_id ";

        $action_field = " `order_id`, `order_status`, `shipping_status`, `pay_status`,`create_time`,`create_user`,`remark` ";
        $sql = "SELECT $action_field FROM `app_order_action` WHERE $where ORDER BY `action_id` DESC";

        $res = $this->db->getAll($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$res){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $res;
			$this->display();
		}
    }
    
    public function getOrderActionListBySn(){        
        if(!isset($this->filter['order_sn'])){
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "订单sn不能为空";
            $this->display();
        }
        
        $order_sn = trim($this->filter['order_sn']);
        $sql = "select id,hidden from `base_order_info` where `order_sn` = '{$order_sn}'";
        $data = $this->db->getRow($sql);
        if($data['hidden']==1 && SYS_SCOPE == 'zhanting'){
            $where = " ORDER BY `action_id` asc limit 0";
        }else{
            $where = " ORDER BY `action_id` DESC";
        }
        $id = $data['id'];
        $sql = "SELECT `order_id`, `order_status`, `shipping_status`, `pay_status`,`create_time`,`create_user`,`remark` FROM `app_order_action` WHERE 
         `order_id` = {$id} {$where}";
           
        $res = $this->db->getAll($sql);
        //返回信息
        if(!$res){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到此订单";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }

	/**
	 * 获取订单明细ID
	 * @author : yxt
	 */
	public function getRelOrderSnByOrderid(){
		$s_time = microtime();
		$where = " 1";
		if(isset($this->filter['order_id']) && !empty($this -> filter["order_id"]))
		{
			$where .=" AND order_id =".$this -> filter["order_id"]." ";
		}

		$sql = "SELECT `id`,`out_order_sn` FROM `rel_out_order` WHERE ".$where;
		$relOrderSn = $this->db->getAll($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$relOrderSn){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单信息";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $relOrderSn;
			$this->display();
		}
	}
	/**
	 * 获取订单明细ID
	 * @author : yxt
	 */
	public function getOrderAddressByOrderid(){
		$s_time = microtime();
		$where = " 1";
		if(isset($this->filter['order_id']) && !empty($this -> filter["order_id"]))
		{
			$where .=" AND order_id =".$this -> filter["order_id"]." ";
		}

		$sql = "SELECT id,order_id,consignee,distribution_type,express_id,freight_no,country_id,province_id,city_id,regional_id,address,tel,email,zipcode,goods_id FROM `app_order_address` WHERE ".$where;
		$orderAddress = $this->db->getRow($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$orderAddress){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此订单信息";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $orderAddress;
			$this->display();
		}
	}
	/**
	 * 根据获取订单明细信息列表（最初是为4C订单查询的）
	 * 默认搜索参数有 证书号和 配石状态
	 */
    public function getOrderDetailList(){
        $s_time = microtime();
        $sql = "SELECT * FROM `app_order_details` where 1=1";
        if(!empty($this -> filter["cert_id"])){
            $sql .= " and zhengshuhao='".trim($this->filter['cert_id'])."'";
        }
        if(isset($this->filter["is_peishi"])){
            $sql .= " and is_peishi='{$this -> filter["is_peishi"]}'";
        }
        $res = $this->db->getAll($sql);
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));        
        //返回信息
        if(!$res){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到此订单信息";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }
    /**
     * 通过证书号 获取订单信息
     */
    public function getOrderInfoByCate(){
        $s_time = microtime();
        $w = '';
        if(isset($this->filter['zhengshuhao']) && !empty($this -> filter["zhengshuhao"]))
        {
            $zhengshuhao = trim($this->filter['zhengshuhao']);
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "缺少参数";
            $this -> return_msg = array();
            $this->display();
        }
        //取出非本订单的证书号关联的商品（主要是祼钻的戒托的关系）
        if(isset($this->filter['order_id']) && !empty($this -> filter["order_id"])){
            $w .= " AND d.order_id !=".trim($this->filter['order_id'])." ";
			$w .= " AND b.order_status=2 and d.is_return=0 and d.is_peishi=0 ";
        }

        $sql = "SELECT b.*,d.zhengshuhao,d.id as detail_id,d.goods_type,d.goods_sn,d.goods_name,d.goods_id FROM `app_order_details` AS d,`base_order_info` AS b WHERE b.`id` = d.`order_id` AND `zhengshuhao` = '".$zhengshuhao."' $w";
        $res = $this->db->getRow($sql);

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$res){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到此订单信息";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
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
	//add by zhangruiying
	//修改快递信息时判断是否是已发货或收货确认不允许修
	public function checkOrderStatus()
	{
		$order_sn=$this->filter['order_no'];
		$sql="select send_good_status from base_order_info where order_sn='{$order_sn}'";
		$row=$this->db->getRow($sql);
		//返回信息
		if($row)
		{
				if($row['send_good_status']==5 or $row['send_good_status']==3)
				{
					$this -> error = 1;
					$this -> return_sql = $sql;
					$this -> return_msg =false;
				}
				else
				{
					$this -> error = 0;
					$this -> return_sql =$sql;
					$this -> return_msg =true;
				}
		}
		else
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> return_msg =false;

		}
		$this->display();

	}
	public function AddOrderLog()
	{
		$order_sn=$this->filter['order_no'];
		$create_user=$this->filter['create_user'];
		$remark=$this->filter['remark'];
		$sql="select id,order_status,send_good_status,order_pay_status from base_order_info where order_sn='{$order_sn}'";
		$row=$this->db->getRow($sql);

		$sql="insert into app_order_action(`order_id`,`order_status`,`shipping_status`,`pay_status`,`create_user`,`create_time`,`remark`) values ('{$row['id']}','{$row['order_status']}','{$row['send_good_status']}','{$row['order_pay_status']}','{$create_user}','".date('Y-m-d H:i:s')."','{$remark}')";
		$res = $this->db->query($sql);

		//返回信息
		if($res)
		{
			$this -> error = 0;
			$this -> return_sql =$sql;
			$this -> return_msg =true;

		}
		else
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> return_msg =false;

		}
		$this->display();
	}

    public function SearchGoodsZp(){
        if(empty($this->filter['where'])){
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "参数错误";
            $this->return_msg = array();
            $this->display();
        }else{
            $sql = "SELECT ao.`id`,ao.`order_id`,ao.`goods_id`,ao.`goods_sn`,ao.`goods_name`,ao.`goods_price`,ao.`favorable_price`,ao.`goods_count`,ao.`create_time`,ao.`modify_time`,ao.`create_user`,ao.`details_status`,ao.`send_good_status`,ao.`buchan_status`,ao.`is_stock_goods`,ao.`is_return`,ao.`details_remark`,ao.`cart`,ao.`cut`,ao.`clarity`,ao.`color`,ao.`zhengshuhao`,ao.`caizhi`,ao.`jinse`,ao.`jinzhong`,ao.`zhiquan`,ao.`kezi`,ao.`face_work`,ao.`xiangqian`,ao.`goods_type`,ao.`cat_type`,ao.`product_type`,ao.`favorable_status`,ao.`kuan_sn`,bo.`id`,bo.`order_sn`,bo.`user_id`,bo.`consignee`,bo.`mobile`,bo.`order_status`,bo.`order_pay_status`,bo.`order_pay_type`,bo.`delivery_status`,bo.`send_good_status`,bo.`buchan_status`,bo.`customer_source_id`,bo.`department_id`,bo.`create_time`,bo.`create_user`,bo.`check_time`,bo.`check_user`,bo.`genzong`,bo.`modify_time`,bo.`order_remark`,bo.`referer`,bo.`is_delete`,bo.`apply_close`,bo.`is_xianhuo`,bo.`is_print_tihuo`,bo.`is_zp`,bo.`effect_date`,bo.`bespoke_id`,bo.`recommended`,sum(ao.goods_count) as xuqiu  FROM `app_order_details` as `ao` LEFT JOIN `base_order_info` as `bo` on ao.`order_id`=bo.id ";
            $str = "";
            $where=$this->filter['where'];

            if($where['start_time'] != "")
            {
                $str .= "bo.`create_time` >= '".$where['start_time']."' AND ";
            }
            if($where['end_time'] != "")
            {
                $str .= "bo.`create_time` <= '".$where['end_time']."' AND ";
            }
            if($where['channel_id'] != "")
            {
                $str .= "bo.`department_id` = ".$where['channel_id']." AND ";
            }

            if($where['goods_sn'] != "")

            {
                $str .= "ao.`goods_sn` LIKE '%".$where['goods_sn']."%' AND ";
            }

            $str .= "ao.`goods_type` = 'zp' AND bo.order_status=2 AND ";
            if($str)
            {
                $str = rtrim($str,"AND ");
                $sql .=" WHERE ".$str;
            }
            $sql .= " GROUP BY  ao.`goods_sn`,ao.`zhiquan`,ao.`kezi` order by ao.`goods_sn`,ao.`zhiquan`,ao.`kezi`;";
            $data = $this->db->getAll($sql);
        }
        if(empty($data)){
            $this->error = 0;
            $this->return_sql = $sql;
            $this->error_msg = "无数据返回";
            $this->return_msg = array();
            $this->display();
        }else{
            $this->error = 0;
            $this->return_sql = $sql;
            $this->error_msg = "成功";
            $this->return_msg = $data;
            $this->display();
        }
    }

    public function SearchOrderdownLoad(){
        if(empty($this->filter['where'])){
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "参数错误";
            $this->return_msg = array();
            $this->display();
        }else{
            $sql = "SELECT ao.`id`,ao.`order_id`,ao.`goods_id`,ao.`goods_sn`,ao.`goods_name`,ao.`goods_price`,ao.`favorable_price`,ao.`goods_count`,ao.`create_time`,ao.`modify_time`,ao.`create_user`,ao.`details_status`,ao.`send_good_status`,ao.`buchan_status`,ao.`is_stock_goods`,ao.`is_return`,ao.`details_remark`,ao.`cart`,ao.`cut`,ao.`clarity`,ao.`color`,ao.`zhengshuhao`,ao.`caizhi`,ao.`jinse`,ao.`jinzhong`,ao.`zhiquan`,ao.`kezi`,ao.`face_work`,ao.`xiangqian`,ao.`goods_type`,ao.`cat_type`,ao.`product_type`,ao.`favorable_status`,ao.`kuan_sn`,bo.`id`,bo.`order_sn`,bo.`user_id`,bo.`consignee`,bo.`mobile`,bo.`order_status`,bo.`order_pay_status`,bo.`order_pay_type`,bo.`delivery_status`,bo.`send_good_status`,bo.`buchan_status`,bo.`customer_source_id`,bo.`department_id`,bo.`create_time`,bo.`create_user`,bo.`check_time`,bo.`check_user`,bo.`genzong`,bo.`modify_time`,bo.`order_remark`,bo.`referer`,bo.`is_delete`,bo.`apply_close`,bo.`is_xianhuo`,bo.`is_print_tihuo`,bo.`is_zp`,bo.`effect_date`,bo.`bespoke_id`,bo.`recommended`,bo.`apply_return` FROM `app_order_details` as `ao` LEFT JOIN `base_order_info` as `bo` on ao.`order_id`=bo.id ";
            $str = "";
            $where=$this->filter['where'];

            if($where['start_time'] != "")
            {
                $str .= "bo.`create_time` >= '".$where['start_time']."' AND ";
            }
            if($where['end_time'] != "")
            {
                $str .= "bo.`create_time` <= '".$where['end_time']."' AND ";
            }
            if($where['channel_id'] != "")
            {
                $str .= "bo.`department_id` = ".$where['channel_id']." AND ";
            }

            if($where['goods_sn'] != "")

            {
                $str .= "ao.`goods_sn` LIKE '%".$where['goods_sn']."%' AND ";
            }

            $str .= "ao.`goods_type` = 'zp' AND bo.order_status=2 AND ";
            if($str)
            {
                $str = rtrim($str,"AND ");
                $sql .=" WHERE ".$str;
            }
            $data = $this->db->getAll($sql);
        }
        if(empty($data)){
            $this->error = 0;
            $this->return_sql = $sql;
            $this->error_msg = "无数据返回";
            $this->return_msg = array();
            $this->display();
        }else{
            $this->error = 0;
            $this->return_sql = $sql;
            $this->error_msg = "成功";
            $this->return_msg = $data;
            $this->display();
        }
    }

    public function GetOrderInfoBySns() {
        $res = 0;
        $sql = '';
        if(isset($this->filter['order_sn']) && !empty($this->filter['order_sn'])){
            $_order_sn = $this->filter['order_sn'];
            $select = "`oi`.*, `oa`.`order_amount`, `oa`.`money_paid`, `oa`.`money_unpaid`, `oa`.`goods_return_price`, `oa`.`real_return_price`, `oa`.`shipping_fee`, `oa`.`goods_amount`,`oi`.`department_id`,`oi`.`apply_close`,`oi`.`order_status`,`oi`.`order_pay_status` ";
            if(isset($this->filter['select']) && !empty($this->filter['select'])){
                $select = $this->filter['select'];
            }
            $sql = "SELECT {$select} FROM `base_order_info` as `oi` LEFT JOIN `app_order_account` as `oa` ON `oi`.`id`=`oa`.`order_id` WHERE `oi`.`order_sn` in ('{$_order_sn}') AND oi.order_status=2 AND oi.order_pay_status=1";
            $res = $this->db->getAll($sql);
        }

        if(!$res)
        {
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到此订单";
            $this -> return_msg = array();
            $this->display();
        }
        else
        {
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }
    //批量点款后改变订单的状态 如果是现货就允许配货如果是期货就去布产
    public function  UpdataOrderStus(){
        $user =$this->filter['user_name'];
        if(!isset($this->filter['pay_type']) || empty($this->filter['pay_type'])){
            $this -> error = 3;
            $this -> return_sql = '';
            $this -> error_msg = "没有设置支付方式";
            $this -> return_msg = array();
            $this->display();
        }else{
            $pay_type=$this->filter['pay_type'];
        }

        if(isset($this->filter['order_sn']) && !empty($this->filter['order_sn'])){
            $sql = "select bo.`id`,bo.`is_xianhuo`,ao.order_amount,bo.order_status,bo.order_pay_status,bo.delivery_status from base_order_info as bo LEFT JOIN app_order_account as ao ON bo.id=ao.order_id where bo.order_sn in ({$this->filter['order_sn']})";
            $res = $this->db->getAll($sql);
            foreach($res as $ke=>$va){
                if ($va['is_xianhuo']==1) {
                    $xianhuo[]= $va['id'];
                }else{
                    $qihuo[]= $va['id'];
                }
            }

            if(!empty($xianhuo)){
                $xianhuo = implode(',',$xianhuo);
                $sql="UPDATE base_order_info as bo LEFT JOIN app_order_account as ao ON bo.id=ao.order_id SET bo.order_pay_status=3,bo.delivery_status=2,ao.money_paid=ao.order_amount,ao.money_unpaid=0 WHERE bo.id in ($xianhuo)";
                $rea = $this->db->query($sql);
            }
            if(!empty($qihuo)){
                $qihuo = implode(',',$qihuo);
                $sql="UPDATE base_order_info AS bo LEFT JOIN app_order_account as ao ON bo.id=ao.order_id SET bo.order_pay_status=3,ao.money_paid=ao.order_amount,ao.money_unpaid=0 WHERE bo.id in ($qihuo)";
                $rea = $this->db->query($sql);
                $sql="select count(d.id) from  base_order_info o ,app_order_details d where o.id=d.order_id and o.order_sn='".$this->filter['order_sn']."' and d.id is not null and d.is_stock_goods=0 and d.buchan_status<>9 and d.buchan_status<>11 and d.is_return<>1";
                $rescount=$this->db->getOne($sql);
                if($rescount==0){
                    $sql="update base_order_info set delivery_status=2 where order_sn='".$this->filter['order_sn']."'";
                    $this->db->query($sql);
                }    
            }
            //订单日志的生成

            $time = date('Y-m-d H:i:s');
            foreach($res as $k=>$v){
                $remark = "通过批量点款 支付金额 [$v[order_amount]]";
                $sql = "insert into app_order_action (`order_id`,`order_status`,`shipping_status`,`pay_status`,`create_user`,`create_time`,`remark`) VALUES ($v[id],$v[order_status],$v[delivery_status],'3','$user','$time','$remark')";
               $ret = $this->db->query($sql);
            }
        }else{
            $this -> error = 2;
            $this -> return_sql = '';
            $this -> error_msg = "没有穿传递order_sn";
            $this -> return_msg = array();
            $this->display();
        }

        if(!$rea){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "修改失败";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> error_msg = "批量修改成功";
            $this -> return_msg = array('success'=>1);
            $this->display();
        }
    }

    /**
     * 更新订单数据
     * @author liuri
     */
    public function updateOrderArr() {
        $s_time = microtime();
        $where = "";
        if(empty($this->filter['order_id']) && empty($this->filter['order_sn'])){
            $this -> error = 1;
			$this -> return_sql ='';
			$this -> return_msg ='订单id为空或订单号为空';
			$this->display();
        }else{
            if($this->filter['order_sn']){
                $where = " order_sn = '{$this->filter['order_sn']}'";
            }
            if($this->filter['order_id']){
                $where = " id = {$this->filter['order_id']}";
            }
        }
        if(empty($this->filter['update_fileds'])){
            $this -> error = 1;
			$this -> return_sql ='';
			$this -> return_msg ='需要更新的字段为空';
			$this->display();
        }
        $res = $this->db->autoExecute('base_order_info',$this->filter['update_fileds'],'UPDATE',$where);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        if(!$res){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "修改失败";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> error_msg = "批量修改成功";
            $this -> return_msg = array('success'=>1);
            $this->display();
        }
    }
    //根据订单编号和商品编号查询起版Id
    public function getQiBanIdByWhere(){
        if (empty($this->filter['order_sn']) || empty($this->filter['goods_sn'])){
            $this -> error = 1;
            $this -> error_msg = "参数不全或不合法";
            $this->display();
        }else{
            $order_sn = $this->filter['order_sn'];
            $goods_sn = $this->filter['goods_sn'];
        }
        $sql = "SELECT id FROM `base_order_info` WHERE `order_sn` ={$order_sn}";
        $res = $this->db->getRow($sql);
        if(!empty($res['id'])){
             $order_id = $res['id'];
             $sql = "select goods_id from `app_order_details` where order_id='{$order_id}' and goods_sn='{$goods_sn}' and goods_type ='qiban'";
             $res = $this->db->getRow($sql);
             if(!empty($res['goods_id'])){
                 $this -> error = 0;
                 $this -> return_sql = $sql;
                 $this -> error_msg = "起版ID查询成功";
                 $this -> return_msg = $res['goods_id'];
                 $this->display();
             }else{
                 $this -> error = 1;
                 $this -> return_sql = $sql;
                 $this -> error_msg = "起版信息不存在";
                 $this -> return_msg = 0;
                 $this->display();
             }
        }else{
            $this -> error = 1;
            $this -> error_msg = "查询订单Id不存在";
            $this -> return_sql = $sql;
            $this->display();
        }
        
    }
	/*
	public function GetOrderSnByDetail()
	{
		$ids=$this->filter['ids'];
		if(empty($ids))
		{
		    $this -> error = 1;
			$this -> return_sql ='';
			$this -> return_msg ='参数为空';
			$this->display();

		}
		$sql="select b.order_sn,d.goods_id from app_order_details as d,base_order_info as b where b.id=d.order_id and d.goods_id in({$ids})'";
		file_put_contents('c:/a.txt',$sql,FILE_APPEND);
		$row=$this->db->getAll($sql);
		//返回信息
		if(!empty($row))
		{
			$key=array_column($row,'goods_id');
			$value=array_column($row,'order_sn');
			$arr=array_combine($key,$value);
			$this -> error = 0;
			$this -> return_sql =$sql;
			$this -> return_msg =$arr;

		}
		else
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> return_msg =array();

		}

		$this->display();

	}
	*/
    
    /*
     * 根据订单编号获取订单顾客姓名
     */
    public function getConsigneeByOrderSn(){
    	$sql = "select consignee from app_order.base_order_info where order_sn = '".$this->filter['order_sn']."'";
   		$res = $this->db->getOne($sql);
   		if(!$res){
   			$this -> error = 1;
   			$this -> return_msg = array();
   			$this->display();
   		}else{
   			$this -> error = 0;
   			$this -> return_msg = $res;
   			$this->display();
   		}
    }
    
    /**
     * 根据订单号获取订单信息
     * is_return 必须是数字类型的，不可以传字符串类型的。
     * @return json
     */
    public function getDistributionByOrderId() {
    	 
    	$s_time = microtime();
    	$where = '';
    	// $sql = "SELECT * FROM `base_order_info` AS `a` LEFT JOIN `app_order_details` AS `b` ON `a`.`id` = `b`.`order_id` WHERE 1"; //暂时用＊号
    	//查看之前的手寸
    	$sql = "SELECT distribution_type,shop_name FROM app_order_address";
    	if (!empty($this->filter['order_id'])) {
    		$sql .= " where `order_id` = '{$this->filter['order_id']}'";
    	}else{
    		$this->error = 1;
    		$this->return_sql = $sql;
    		$this->error_msg = "订单号不能为空!";
    		$this->return_msg = array();
    		$this->display();
    	}  
       
    	$data['data']= $this->db->getAll($sql);
    	
    	// 记录日志
    	$reponse_time = microtime() - $s_time;
    	$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
    	//
    	//返回信息
    	if (!$data['data']) {
    	$this->error = 1;
    		$this->return_sql = $sql;
    		$this->error_msg = "未查询到此商品";
    		$this->return_msg = array();
    		$this->display();
    	} else {
    	$this->error = 0;
    	$this->return_sql = $sql;
    	$this->return_msg = $data;
    	$this->display();
      }
    }
    
    /*
     * 通过体验店名称获取公司名称
     */
    public function getCompanyInfoByShopName(){
    	$sql ="select company from cuteframe.sales_channels where channel_own = '".$this->filter['channel_own']."'";
    	$data['data'] = $this->db->getAll($sql);
    	//返回信息
    	if (!$data['data']) {
    	$this->error = 1;
    		$this->return_sql = $sql;
    		$this->error_msg = "未查询到此商品";
    		$this->return_msg = array();
    		$this->display();
    	} else {
    	$this->error = 0;
    	$this->return_sql = $sql;
    	$this->return_msg = $data;
    	$this->display();
      } 
    	
    }
    
    public function getShopOrderShip() {
    	$where = " 1";
    	if(isset($this->filter['order_sn']) && !empty($this -> filter["order_sn"]))
    	{
    		$where .=" AND `order_sn` ='".$this -> filter["order_sn"]."'";
    	}else{
    		$this -> error = 1;
    		$this -> return_sql = '';
    		$this -> error_msg = "订单号不能空！";
    		$this -> return_msg = array();
    		$this->display();
    	}
    	
    	$sql = "SELECT * FROM `base_order_info` WHERE ".$where;
    	$order_info = $this->db->getRow($sql);
    	if (!$order_info) {
    		$this -> error = 1;
    		$this -> return_sql = $sql;
    		$this -> error_msg = "未查询到此订单信息";
    		$this -> return_msg = array();
    		$this->display();
    	}
    	
    	$order_id = $order_info['id'];
    	//获取订单地址
    	$sql = "SELECT `consignee`,`shop_name`,`distribution_type`,`express_id`,`country_id`,`province_id`,`city_id`,`regional_id`,`address`,`tel`,`email`,`zipcode`,`freight_no`,`shop_type` FROM `app_order_address` WHERE `order_id` = '".$order_id."'";
    	$order_address = $this->db->getRow($sql);
    	
    	//获取发票信息
    	$sql = "SELECT `is_invoice`,`invoice_title`,`invoice_num`,`invoice_amount`,`create_time`  FROM `app_order_invoice`  WHERE `order_id` = '".$order_id."'";
    	$order_invoice = $this->db->getRow($sql);
    	
    	$data[0] = $order_info;
    	$data[1] = $order_address;
    	$data[2] = $order_invoice;
    	
    	//返回信息
    	$this -> error = 0;
    	$this -> return_sql = $sql;
    	$this -> return_msg = $data;
    	$this->display();
    }


    public function allow_buchan(){  
        $pdo=$this->db->db();
        $from_type = 2;
        //$user_name= !empty($this->filter["user_name"]) ? trim($this->filter["user_name"]) : ''; 
        if(empty($this->filter["order_sn"])){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "订单号不能空！";
            $this -> return_msg = array();
            $this->display();
        }
        $order_sn = $this->filter["order_sn"];
        $date_log = "order_sn:".$order_sn ."--". date("Y-m-d H:i:s") ;
        $sql = "SELECT o.*,(select u.out_order_sn from app_order.rel_out_order u where u.order_id=o.id limit 1) as out_order_sn FROM app_order.base_order_info o WHERE o.order_sn = '{$order_sn}'";
        //echo $sql;
        $order_info = $this->db->getRow($sql);
        
        if(empty($order_info)){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "订单号不存在！";
            $this -> return_msg = array();
            $this->display();           
        }
        if($order_info['order_status']!=2){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "未审核的订单不允许布产-1";
            $this -> return_msg = array();
            $this->display();           
        }
        if($order_info['order_pay_status']==1){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "订单未付款,不可以布产";
            $this -> return_msg = array();
            $this->display();           
        }                
        
        $sql="select d.* from app_order.app_order_details d,app_order.base_order_info o where d.order_id=o.id and o.order_sn='{$order_sn}' and d.is_stock_goods=0 and d.is_return<>1 and (d.bc_id=0 or d.bc_id is null)";
        $order_detail_list=$this->db->getAll($sql);
        if(empty($order_detail_list)){
            $this -> error = 0;
            $this -> return_sql = '';
            $this -> error_msg = "订单货号已全部布产";
            $this -> return_msg = array();
            $this->display();                        
        }

        $attr_names =array('cart'=>'主石单颗重','zhushi_num'=>'主石粒数','clarity'=>'主石净度','color'=>'主石颜色','cert'=>'证书类型','zhengshuhao'=>'证书号','caizhi'=>'材质','jinse'=>'金色','jinzhong'=>'金重','zhiquan'=>'指圈','kezi'=>'刻字','face_work'=>'表面工艺');
        $goods_arr = array();
        foreach($order_detail_list as $key=>$val){
                if($val['is_stock_goods'] == 1 && empty($val['is_peishi'])){
                    continue;
                }
                $detail_id = $val['id'];  
                $sql="SELECT `gr`.* FROM kela_supplier.`product_goods_rel` as `gr`,kela_supplier.`product_info` as `pi` WHERE `gr`.`bc_id`=`pi`.`id` and `gr`.`status` = 0 and `gr`.`goods_id` = '{$detail_id}' and `pi`.`p_sn` = '{$order_sn}'";              
                $buchan_info=$this->db->getRow($sql);
                if(!empty($buchan_info)){
                    continue;
                }
                $new_style_info = array();
                $xmp=array();
                foreach ($attr_names as $a_key=>$a_val){
                    $xmp['code'] = $a_key;
                    $xmp['name'] = $a_val;
                    $xmp['value'] = $val[$a_key];
                    $new_style_info[] = $xmp;
                }
               
                $goods_num = $val['goods_count'];
                $goods_type=0;
                if($val['is_peishi']==1){
                    $zhengshuhao = $val['zhengshuhao'];  

                    $ret=$this->db->getRow("select * from front.`diamond_info` where cert_id='{$zhengshuhao}'");
                    if(empty($ret))
                        $ret=$this->db->getRow("select * from front.`diamond_info` where cert_id='{$zhengshuhao}'");
                    if($ret){
                        $goods_arr[$key]['diamond'] = $ret;
                        $goods_type=$ret['goods_type'];
                    }else{
                        $this -> error = 1;
                        $this -> return_sql = '';
                        $this -> error_msg = "裸钻列表未找到证书号为{$zhengshuhao}的裸钻信";
                        $this -> return_msg = array();                      
                        file_put_contents('allow_buchan_api.log', $date_log ."裸钻列表未找到证书号为".$zhengshuhao."的裸钻信".PHP_EOL,FILE_APPEND);
                        $this->display(); 
                        exit();
                    }
                    
                }
                /*
                $diamodel = new SelfDiamondModel(19);
                $cert_id2 = preg_replace('/[a-zA-Z]{0,10}/', '', $val['zhengshuhao']);
                $goods_type = $diamodel->getGoodsTypeByCertId($val['zhengshuhao'],$cert_id2);

                    if(!empty($cert_id2)){
                        $sql ="select good_type from front.diamond_info where cert_id='".$cert_id."' OR cert_id ='".$cert_id2."'";
                    }else{
                        $sql ="select good_type from front.diamond_info where cert_id='".$cert_id."'";
                    }

                    if(!empty($cert_id2)){
                        $sql ="select good_type from front.diamond_info_all where cert_id='".$cert_id."' OR cert_id ='".$cert_id2."'";
                    }else{
                        $sql ="select good_type from front.diamond_info_all where cert_id='".$cert_id."'";
                    }
                */
                if($val['zhengshuhao'] == ''){
                    $diamond_type = 1;
                }else{
                    if($goods_type ==2){
                        //期货钻
                         $diamond_type =2;
                    }else{
                        $diamond_type =1; 
                    }
                }




                $goods_arr[$key]['origin_dia_type']=$diamond_type;
                $goods_arr[$key]['diamond_type']=$diamond_type;
                $goods_arr[$key]['p_id'] =  $detail_id;
                $goods_arr[$key]['p_sn'] =  $order_sn;
                $goods_arr[$key]['style_sn'] = $val['goods_sn'];
                $goods_arr[$key]['goods_name'] = $val['goods_name'];
                $goods_arr[$key]['bc_style'] = empty($val['bc_style'])?'普通件':$val['bc_style'];
                $goods_arr[$key]['xiangqian'] = $val['xiangqian'];
                $goods_arr[$key]['goods_type'] = $val['goods_type'];
                $goods_arr[$key]['cat_type'] = $val['cat_type'];
                $goods_arr[$key]['product_type'] = $val['product_type'];
                $goods_arr[$key]['num'] = $goods_num;
                $goods_arr[$key]['info'] = empty($val['details_remark']) ? '' : $val['details_remark'];
                $goods_arr[$key]['consignee'] = $order_info['consignee'];
                $goods_arr[$key]['attr'] = $new_style_info;
                $goods_arr[$key]['customer_source_id'] = $order_info['customer_source_id'];
                $goods_arr[$key]['channel_id'] = $order_info['department_id'];

                $goods_arr[$key]['create_user']=$order_info['create_user'];
                $goods_arr[$key]['is_peishi'] = $val['is_peishi'];
                $goods_arr[$key]['is_alone'] = $val['is_alone'];
                $goods_arr[$key]['qiban_type'] = $val['qiban_type'];
                $goods_arr[$key]['out_order_sn'] = $order_info['out_order_sn'];
                $goods_arr[$key]['caigou_info'] = $order_info['order_remark'];
                $goods_arr[$key]['goods_id'] = $val['goods_id'];

              //按照款号+材质+材质颜色+指圈+镶口来判断 是否快速定制
            $is_quick_diy = 0;
            if(!empty($val['goods_sn']) && !empty($val['caizhi']) && !empty($val['jinse']) && !empty($val['zhiquan']) && !empty($val['xiangkou'])){
                $quickdiy_where  = " style_sn = '".$val['goods_sn']."' and caizhi = '".$val['caizhi']."' and caizhiyanse = '".$val['jinse']."' and  zhiquan = ".$val['zhiquan']." and xiangkou = ".$val['xiangkou']." ";
                $sql = "select * from front.app_style_quickdiy  where status =1 AND $quickdiy_where ";
                $ress =  $this->db->getRow($sql);
                if(!empty($ress)){
                    $is_quick_diy = 1;
                }
            }

            $goods_arr[$key]['is_quick_diy']  = $is_quick_diy;


                //end
        }
            //var_dump($goods_arr);exit;
        //    $res = array('data'=>'','error'=>0);
            //添加布产单
        //print_r($goods_arr);
        if(!empty($goods_arr)){
            try{

                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
                    $pdo->beginTransaction();//开启事务
                    $time=date('Y-m-d H:i:s');
                    $arr=array();
                    foreach ($goods_arr as $key => $value){
                        $is_peishi = 0;
                        $cs_id = $value['customer_source_id'];//客户来源
                        $djbh_bc = $cs_id == 2946 ? 'EC' : '';//boss_1246
                        //EDITBY ZHANGRUIYIGN
                        $attrKeyVal = array_column($value['attr'],'value','code');                  
                        
                        $value['caigou_info']=isset($value['caigou_info'])?$value['caigou_info']:'';
                        $value['create_user'] = !empty($value['create_user'])?$value['create_user']:'';
                        $value['prc_id']=isset($value['prc_id'])?$value['prc_id']:0;
                        $value['prc_name']=isset($value['prc_name'])?$value['prc_name']:'';
                        $value['opra_uname']=isset($value['opra_uname'])?$value['opra_uname']:'';
                        $value['is_alone']=isset($value['is_alone'])?$value['is_alone']:0;
                        $value['style_sn']=trim($value['style_sn']);
                        $value['status']=!empty($value['prc_id'])?3:1;
                        $value['qiban_type']=isset($value['qiban_type'])?$value['qiban_type']: 2;
                        $value['diamond_type']=!empty($value['diamond_type'])?$value['diamond_type']:0;
                        $value['origin_dia_type']=!empty($value['origin_dia_type'])?$value['origin_dia_type']:0;
                        $value['to_factory_time']=!empty($value['to_factory_time'])?$value['to_factory_time']:'0000-00-00 00:00:00';
                        $value['is_quick_diy'] = isset($value['is_quick_diy'])?$value['is_quick_diy']:0;
                        //$sql = "INSERT INTO kela_supplier.`product_info`(`bc_sn`, `p_id`, `p_sn`, `style_sn`, `status`, `num`, `prc_id`,`prc_name`, `opra_uname`, `add_time`, `edit_time`, `info`,`from_type`,`consignee`,`bc_style`,`goods_name`,`xiangqian`,`customer_source_id`,`channel_id`,`caigou_info`,`create_user`,`is_alone`,`qiban_type`,`diamond_type`,`origin_dia_type`,`to_factory_time`) VALUES ('',{$value['p_id']},'{$value['p_sn']}','{$value['style_sn']}',{$value['status']},{$value['num']},{$value['prc_id']},'{$value['prc_name']}','{$value['opra_uname']}','{$time}','{$time}','{$value['info']}',{$from_type},'{$value['consignee']}','{$value['bc_style']}','{$value['goods_name']}','{$value['xiangqian']}','{$value['customer_source_id']}','{$value['channel_id']}','{$value['caigou_info']}','{$value['create_user']}','{$value['is_alone']}','{$value['qiban_type']}','{$value['diamond_type']}','{$value['origin_dia_type']}','{$value['to_factory_time']}')";
                        //$this->db->query($sql);
                        //$_id = $this->db->insertId();
                        //EDIT END
                        //file_put_contents("./u223.txt",$sql."\r\n",FILE_APPEND);
                        $val_insert = array($value['p_id'],$value['p_sn'],$value['style_sn'],$value['status'],$value['num'],$value['prc_id'],$value['prc_name'],$value['opra_uname'],$time,$time,$value['info'],$from_type,$value['consignee'],$value['bc_style'],$value['goods_name'],$value['xiangqian'],$value['customer_source_id'],$value['channel_id'],$value['caigou_info'],$value['create_user'],$value['is_alone'],$value['qiban_type'],$value['diamond_type'],$value['origin_dia_type'],$value['to_factory_time'],$value['is_quick_diy']);
                        $sql = "INSERT INTO kela_supplier.product_info (`bc_sn`, `p_id`, `p_sn`, `style_sn`, `status`, `num`, `prc_id`,`prc_name`, `opra_uname`, `add_time`, `edit_time`, `info`,`from_type`,`consignee`,`bc_style`,`goods_name`,`xiangqian`,`customer_source_id`,`channel_id`,`caigou_info`,`create_user`,`is_alone`,`qiban_type`,`diamond_type`,`origin_dia_type`,`to_factory_time`,`is_quick_diy`) VALUES ('',?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                        $stmt = $pdo->prepare($sql);
                        $res=$stmt->execute(array_values($val_insert));                        
                        $_id = $pdo->lastInsertId();
                        
    
                        //订单中只要有4C销售裸钻配石信息入库
                        $is_peishi = isset($value['is_peishi'])?$value['is_peishi']:0;
                        if($is_peishi==1){
                            $is_peishi =1;
                            $d = $value['diamond'];
                            $d['chengben_jia'] = !empty($d['chengben_jia'])?$d['chengben_jia']:0;
                            $d['source_discount'] = !empty($d['source_discount'])?$d['source_discount']:0;
                            $fileds ="`id`,`order_sn`,`zhengshuhao`,`zhengshuhao_org`,`price_org`,`price`,`discount_org`,`discount`,`color`,`carat`,`shape`,`clarity`,`cut`,`peishi_status`";
                            $values ="{$_id},'{$value['p_sn']}','{$d['cert_id']}','{$d['cert_id']}','{$d['chengben_jia']}','{$d['chengben_jia']}','{$d['source_discount']}','{$d['source_discount']}','{$d['color']}','{$d['carat']}','{$d['shape']}','{$d['clarity']}','{$d['cut']}',0";
                            $sql = "INSERT INTO kela_supplier.`product_info_4c`({$fileds}) VALUES ($values)";
                            $this->db->query($sql);
                        }
    
                        $arr[$key]['id'] = $value['p_id'];
                        $arr[$key]['buchan_sn'] = $_id;
                        $arr[$key]['final_bc_sn'] = '';
                        if ($from_type == '2') {
                            $bc_sn = $this->create_bc_sn($value['p_sn'], $_id);
                            $arr[$key]['final_bc_sn'] = $bc_sn;
                            //抓取订单其他为传递的必要属性
                            $sql ="select xiangkou,cert from app_order.app_order_details where id={$value['p_id']}";
                            $orderDetail = $this->db->getRow($sql);
                            if(!empty($orderDetail)){
                                if(!array_key_exists('zhushi_num',$attrKeyVal)){
                                    $value['attr'][] = array('code'=>'zhushi_num','name'=>'主石粒数','value'=>$orderDetail['zhushi_num']);
                                }
                                if(!array_key_exists('xiangkou',$attrKeyVal)){
                                    $value['attr'][] = array('code'=>'xiangkou','name'=>'镶口','value'=>$orderDetail['xiangkou']);
                                }
                                if(!array_key_exists('cert',$attrKeyVal)){
                                    $value['attr'][] = array('code'=>'cert','name'=>'证书类型','value'=>$orderDetail['cert']);
                                }
                            }
                        }

                        $bind_goods_buchan_status =0;
                        if(is_numeric($value['goods_id'])){
                            $sql="select goods_id from warehouse_shipping.warehouse_goods where goods_id='{$value['goods_id']}' and company_id=58 and kela_order_sn='{$order_sn}'";
                            $bingd_goods = $this->db->getRow($sql);
                            if(!empty($bingd_goods)){
                                $bind_goods_buchan_status = 11; //智慧门店下单售卖浩鹏总公司现货订单 布产后布产状态直接改成不需布产
                            }

                        }
                        
                        if($bind_goods_buchan_status===11)
                            $sql = "UPDATE kela_supplier.`product_info` SET bc_sn = '".$bc_sn."',is_peishi=".$is_peishi.",status='11' WHERE id ='{$_id}'";
                        else
                            $sql = "UPDATE kela_supplier.`product_info` SET bc_sn = '".$bc_sn."',is_peishi=".$is_peishi." WHERE id ='{$_id}'";
                         $this->db->query($sql);

                        //获取款式主石，副石相关属性列表
                        $attrExt = $this->getStoneAttrList($value['style_sn'],$value['attr']);
                        if(!empty($attrExt)){
                            $value['attr'] = array_merge($value['attr'],$attrExt);
                        }
                        //$logss =  var_export($value['attr'],true);
                        //file_put_contents('buchan2.txt',$logss);
                        //插入属性
                        $t = "";
                        foreach($value['attr'] as $k => $v)
                        {
                            $sql = "INSERT INTO kela_supplier.`product_info_attr`(`g_id`, `code`, `name`, `value`) VALUES (".$_id.",'".$v['code']."','".$v['name']."','".$v['value']."')";
                            $t .= $sql;
                            $pdo->query($sql);
                        }
                        //file_put_contents("/data/www/cuteframe_boss/apps/processor/logs/u223.txt",$t."\r\n",FILE_APPEND );
                        //插入布产表后增加一条日志
                        if($bind_goods_buchan_status===11)
                            $remark = "系统自动生成布产单：".$bc_sn."，来源单号：".$value['p_sn']." 门店下总部货号".$value['goods_id'].", 布产状态改为不需布产";
                        else
                            $remark = "系统自动生成布产单：".$bc_sn."，来源单号：".$value['p_sn'];
                        $sql = "INSERT INTO kela_supplier.`product_opra_log`(`bc_id`, `status`, `remark`, `uid`, `uname`, `time`) VALUES ({$_id},{$value['status']},'{$remark}',0,'{$value['create_user']}','{$time}')";
                        $this->db->query($sql);
                        //file_put_contents("/data/www/cuteframe_boss/apps/processor/logs/u223.txt",$sql."\r\n",FILE_APPEND );
                        //如果是订单来源的布产单，插入数据到布产和货品关系表中
                        if($from_type == 2){
                            $sql = "INSERT INTO kela_supplier.`product_goods_rel`(`bc_id`,`goods_id`) VALUES (".$_id.",".$value['p_id'].")";
                            $this->db->query($sql);  
                            if($bind_goods_buchan_status===11)
                               $sql = "UPDATE app_order.`app_order_details` SET `bc_id`='{$_id}',buchan_status='11' WHERE `id` = '{$value['p_id']}'";                                                   
                            else
                               $sql = "UPDATE app_order.`app_order_details` SET `bc_id`='{$_id}' WHERE `id` = '{$value['p_id']}'";
                            $this->db->query($sql);
                        }

                    }

                    $sql="update app_order.base_order_info set effect_date=now(),buchan_status=2 where order_sn='{$order_sn}'";
                    $this->db->query($sql);
                    $bc_remark="订单允许布产成功！布产单号为：".implode(",",array_column($arr,'final_bc_sn'));
                    $sql="insert into app_order.app_order_action (order_id,order_status,shipping_status,pay_status,create_user,create_time,remark) values ('{$order_info['id']}','{$order_info['order_status']}','{$order_info['send_good_status']}','{$order_info['order_pay_status']}','{$order_info['create_user']}',now(),'{$bc_remark}')";
                    $this->db->query($sql);
                    $pdo->commit();
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                    $this -> error = 0;
                    $this -> return_sql = '';
                    $this -> error_msg = "订单布产成功";
                    $this -> return_msg = array();
                    $this->display();                     
            }catch(Exception $e){
                    $pdo->rollback();
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                        $this -> error = 1;
                        $this -> return_sql = $sql;
                        $this -> error_msg = json_encode($e);
                        $this -> return_msg = array();
                        //$date_log = "order_sn:".$order_sn ."--". date("Y-m-d H:i:s") ;
                        file_put_contents('allow_buchan_api.log', $date_log . json_encode($e)."--".json_encode($sql).PHP_EOL,FILE_APPEND);
                        $this->display(); 
            }        
        }
    }


    private function getStoneAttrList($style_sn,$attrlist){
        $stoneAttrList = array();//主石，副石 属性    
        $stoneList = $this->getStyleStoneByStyleSn($style_sn);
        $stoneAttrList[11] = array('code'=>'zhushi_cat','name'=>'主石类型','value'=>'');
        $stoneAttrList[12] = array('code'=>'zhushi_shape','name'=>'主石形状','value'=>'');
        $stoneAttrList[21] = array('code'=>'fushi_cat','name'=>'副石类型','value'=>'');
        $stoneAttrList[22] = array('code'=>'fushi_shape','name'=>'副石形状','value'=>'');
        $stoneAttrList[23] = array('code'=>'fushi_yanse','name'=>'副石颜色','value'=>'');
        $stoneAttrList[24] = array('code'=>'fushi_jingdu','name'=>'副石净度','value'=>'');
        $stoneAttrList[25] = array('code'=>'fushi_zhong_total1','name'=>'副石1总重','value'=>'');
        $stoneAttrList[26] = array('code'=>'fushi_num1','name'=>'副石1粒数','value'=>'');
        $stoneAttrList[27] = array('code'=>'fushi_zhong_total2','name'=>'副石2总重','value'=>'');
        $stoneAttrList[28] = array('code'=>'fushi_num2','name'=>'副石2粒数','value'=>'');
        $stoneAttrList[29] = array('code'=>'fushi_zhong_total3','name'=>'副石3总重','value'=>'');
        $stoneAttrList[30] = array('code'=>'fushi_num3','name'=>'副石3粒数','value'=>'');
         
        foreach ($stoneList as $key=>$vo){
            if($key==1){
                $zhushi_shape_arr = array_unique(array_column($vo,'shape'));
                $zhushi_shape = trim(implode('|',$zhushi_shape_arr),'|');
                $zhushi_cat_arr = array_unique(array_column($vo,'stone_cat'));
                $zhushi_cat = trim(implode('|',$zhushi_cat_arr),'|');
                $stoneAttrList[11] = array('code'=>'zhushi_cat','name'=>'主石类型','value'=>$zhushi_cat);
                $stoneAttrList[12] = array('code'=>'zhushi_shape','name'=>'主石形状','value'=>$zhushi_shape);
            }else if($key==2){
                $fushi_cat_arr = array_unique(array_column($vo,'stone_cat'));
                $fushi_cat = trim(implode('|',$fushi_cat_arr),'|');
                $fushi_shape_arr = array_unique(array_column($vo,'shape'));
                $fushi_shape = trim(implode('|',$fushi_shape_arr),'|');
                $fushi_yanse_arr = array_unique(array_column($vo,'color'));
                $fushi_yanse = trim(implode('|',$fushi_yanse_arr),'|');
    
                $fushi_jingdu_arr = array_unique(array_column($vo,'clarity'));
                $fushi_jingdu = trim(implode('|',$fushi_jingdu_arr),'|');
                 
                $stoneAttrList[21] = array('code'=>'fushi_cat','name'=>'副石类型','value'=>$fushi_cat);
                $stoneAttrList[22] = array('code'=>'fushi_shape','name'=>'副石形状','value'=>$fushi_shape);
                $stoneAttrList[23] = array('code'=>'fushi_yanse','name'=>'副石颜色','value'=>$fushi_yanse);
                $stoneAttrList[24] = array('code'=>'fushi_jingdu','name'=>'副石净度','value'=>$fushi_jingdu);
            }
        }

        $zhiquan = $carat = $xiangkou = "";
        foreach ($attrlist as $vo){
            if($vo['code']=="zhiquan"){
                $zhiquan = trim($vo['value']);
            }else if($vo['code']=="cart" || $vo['code']=="zuanshidaxiao"){
                $carat = trim($vo['value']);
            }else if($vo['code']=="xiangkou"){
                $xiangkou = trim($vo['value']);
            }
        }   

        $fushiInfo = $this->getStyleFushi($style_sn, $carat,$xiangkou, $zhiquan);
        if(!empty($fushiInfo)){
            $stoneAttrList[25] = array('code'=>'fushi_zhong_total1','name'=>'副石1总重','value'=>$fushiInfo['fushi_zhong_total1']);
            $stoneAttrList[26] = array('code'=>'fushi_num1','name'=>'副石1粒数','value'=>$fushiInfo['fushi_num1']);

            $stoneAttrList[27] = array('code'=>'fushi_zhong_total2','name'=>'副石2总重','value'=>$fushiInfo['fushi_zhong_total2']);
            $stoneAttrList[28] = array('code'=>'fushi_num2','name'=>'副石2粒数','value'=>$fushiInfo['fushi_num2']);

            $stoneAttrList[29] = array('code'=>'fushi_zhong_total3','name'=>'副石3总重','value'=>$fushiInfo['fushi_zhong_total3']);
            $stoneAttrList[30] = array('code'=>'fushi_num3','name'=>'副石3粒数','value'=>$fushiInfo['fushi_num3']);
        }
        /* 
        else{
            $stoneAttrList[21]['value'] ='';
            $stoneAttrList[22]['value'] ='';
            $stoneAttrList[23]['value'] ='';
            $stoneAttrList[24]['value'] ='';
        }*/

        ksort($stoneAttrList);
        return $stoneAttrList;
    
    }

    private function getStyleStoneByStyleSn($style_sn){

        $shape_arr = array("0"=>"无",1=>"垫形",2=>"公主方",3=>"祖母绿",4=>"心形",5=>"蛋形",6=>"椭圆形",7=>"橄榄形",8=>"三角形",9=>"水滴形",10=>"长方形",11=>"圆形",12=>"梨形",13=>"马眼形");
        $stonecat_arr = array("0"=>"无","1"=>"圆钻","2"=>"异形钻","3"=>"珍珠","4"=>"翡翠","5"=>"红宝石","6"=>"蓝宝石","7"=>"和田玉","8"=>"水晶","9"=>"珍珠贝","10"=>"碧玺","11"=>"玛瑙","12"=>"月光石","13"=>"托帕石","14"=>"石榴石","15"=>"绿松石","16"=>"芙蓉石","17"=>"祖母绿","18"=>"贝壳","19"=>"橄榄石","20"=>"彩钻","21"=>"葡萄石","22"=>"海蓝宝","23"=>"坦桑石","24"=>"粉红宝","25"=>"沙佛莱","26"=>"粉红蓝宝石");
        $color_arr = array("0"=>"无","1" =>"F","2" =>"G","3" =>"H","4" =>"I","8" =>"I-J","5" =>"J","6" =>"K","9" =>"K-L","7" =>"L","10" =>"白","11" =>"M","12" =>"<N","13" =>"N","14" =>"D","15" =>"E");
        $clarity_arr = array("0"=>"无","1"=>"IF","2" => "VVS","3" => "VVS1","4" =>"VVS2","5" =>"VS","6" =>"VS1","7" =>"VS2","8" =>"SI","9" =>"SI1","10" =>"SI2","11" =>"I1","12" =>"I2","13" =>"VSN","14" =>"不分级");

        $sql = "select a.stone_position,a.stone_cat,a.stone_attr from front.rel_style_stone a inner join front.base_style_info b on a.style_id=b.style_id where b.style_sn='{$style_sn}'";
        $data = $this->db->getAll($sql);

        $stoneList = array();
        foreach($data as $vo){
            $stone = array();
            //stone_position = 1主石 2 副石
            $stone_postion = $vo['stone_position'];         
            //stone_cat=1 圆钻 圆形   stone_cat=2 异形钻 对应形状
            $stoneAttr = unserialize($vo['stone_attr']);
            if($vo['stone_cat']==1){
                $shape_name = "圆形"; 
            }else if($vo['stone_cat']==0){
                $stoneAttr = array();
                $shape_name = "无";
                continue;//石头类型为无 记录无效，忽略。
            }else{
                $shape_id = isset($stoneAttr['shape_fushi'])?$stoneAttr['shape_fushi']:'0';
                $shape_id = isset($stoneAttr['shape_zhushi'])?$stoneAttr['shape_zhushi']:$shape_id;
                $shape_name = isset($shape_arr[$shape_id])?$shape_arr[$shape_id]:$shape_id;
            }
            $color_id = isset($stoneAttr['color_fushi'])?$stoneAttr['color_fushi']:'0';
            $color_id = isset($stoneAttr['color_zhushi'])?$stoneAttr['color_zhushi']:$color_id;
            $color = isset($color_arr[$color_id])?$color_arr[$color_id]:$color_id;
             
            $clarity_id = isset($stoneAttr['clarity_fushi'])?$stoneAttr['clarity_fushi']:'0';
            $clarity_id = isset($stoneAttr['clarity_zhushi'])?$stoneAttr['clarity_zhushi']:$clarity_id;
            $clarity = isset($clarity_arr[$clarity_id])?$clarity_arr[$clarity_id]:$clarity_id;
            
            $zhushi_num = isset($stoneAttr['number'])?$stoneAttr['number']:'0';
            if(isset($stonecat_arr[$vo['stone_cat']])){
                $stone_cat = $stonecat_arr[$vo['stone_cat']];
            }else{
                $stone_cat = '无';
            }
            $stone['stone_postion'] = $stone_postion;//石头位置
            $stone['stone_cat'] = $stone_cat;//石头类型
            $stone['shape'] = $shape_name;//石头形状
            $stone['color'] = $color;//石头形状
            $stone['clarity'] = $clarity;//石头形状
            $stone['zhushi_num'] = $zhushi_num;//主石粒数
            $stoneList[$stone_postion][] = $stone;
        }
        return $stoneList;
        
    }

    private function getStyleFushi($style_sn,$stone,$xiangkou,$zhiquan){
        if($stone === '' && $xiangkou ===''){
            return array();
        }
        $zhiquan = round($zhiquan);//四舍五入
        $stone = trim($stone);
        $xiangkou = trim($xiangkou);
        $sql = "select finger as zhiquan,
                     sec_stone_weight as fushi_zhong1,
                     sec_stone_num  as fushi_num1,
                     sec_stone_weight_other as fushi_zhong2,
                     sec_stone_num_other as fushi_num2,
                     sec_stone_weight3 as fushi_zhong3,
                     sec_stone_num3 as fushi_num3 
        from front.app_xiangkou where style_sn='{$style_sn}'";
        if($stone!=='' && is_numeric($stone) && $stone>=0){
            $sql .=" and round(stone*1-0.05,4) <= {$stone} and {$stone}<= round(stone*1+0.04,4) order by abs(stone-{$stone}) asc";
        }else if($stone==='' && is_numeric($xiangkou) && $xiangkou>=0){
            $sql .=" and stone= ".$xiangkou."";
        }else{
            return array();
        }
        //echo $sql;
        $data = $this->db->getAll($sql);
        $fushiInfo = array();
        foreach ($data as $vo){
            $zhiquan_arr = explode('-',$vo['zhiquan']);
            $len = count($zhiquan_arr);
            if($len==2){
                $zhiquan_min = $zhiquan_arr[0];
                $zhiquan_max = $zhiquan_arr[1];
            }else if($len==1){
                $zhiquan_min = $zhiquan_arr[0];
                $zhiquan_max = $zhiquan_arr[0];
            }else {
                continue;
            }
            
            if($zhiquan>=$zhiquan_min && $zhiquan<=$zhiquan_max){
                $fushiInfo['fushi1'] = '';
                $fushiInfo['fushi_num1'] = $vo['fushi_num1'];
                $fushiInfo['fushi_zhong_total1'] = $vo['fushi_zhong1']/1;
                if($vo['fushi_num1']>0 && $vo['fushi_zhong1']>0){
                    $fushiInfo['fushi_zhong1'] = sprintf("%.4f",$vo['fushi_zhong1']/$vo['fushi_num1'])/1;
                    $fushiInfo['fushi1'] = $fushiInfo['fushi_zhong_total1'].'ct/'.$vo['fushi_num1'].'p';
                }else{
                    $fushiInfo['fushi_zhong1']=0;
                }
                
                $fushiInfo['fushi2'] = '';
                $fushiInfo['fushi_num2'] = $vo['fushi_num2'];
                $fushiInfo['fushi_zhong_total2'] = $vo['fushi_zhong2']/1;
                if($vo['fushi_num2']>0 && $vo['fushi_zhong2']>0){
                    $fushiInfo['fushi_zhong2'] = sprintf("%.4f",$vo['fushi_zhong2']/$vo['fushi_num2'])/1;
                    $fushiInfo['fushi2'] = $fushiInfo['fushi_zhong_total2'].'ct/'.$vo['fushi_num2'].'p';
                }else{
                    $fushiInfo['fushi_zhong2']=0;
                }
                
                $fushiInfo['fushi3'] = $vo['fushi_num3'];
                $fushiInfo['fushi_num3'] = $vo['fushi_num3'];
                $fushiInfo['fushi_zhong_total3'] = $vo['fushi_zhong3']/1;
                if($vo['fushi_num3']>0 && $vo['fushi_zhong3']>0){
                        $fushiInfo['fushi_zhong3'] = sprintf("%.4f",$vo['fushi_zhong3']/$vo['fushi_num3'])/1;
                    $fushiInfo['fushi3'] = $fushiInfo['fushi_zhong_total3'].'ct/'.$vo['fushi_num3'].'p';
                }else{
                    $fushiInfo['fushi_zhong3']=0;
                }
                
                break;
            }
        }
        return $fushiInfo;  
    }    

    private function create_bc_sn($order_sn, $bc_id) {
        $bc_sn = BCD_PREFIX.$bc_id;
        if (SYS_SCOPE == 'boss') {
            $sql = "select sc.channel_class from app_order.base_order_info a inner join cuteframe.sales_channels sc on sc.id = a.department_id where a.order_sn = '{$order_sn}'";
            $channel = $this->db->getOne($sql);
            if ($channel == '1') {
                return 'DS'.$bc_sn;
            } else if ($channel == '2') {
                return 'MD'.$bc_sn;
            }
        }
        if(SYS_SCOPE == 'zhanting'){
            $sql = "select c.company_type from app_order.base_order_info a inner join cuteframe.sales_channels sc on sc.id = a.department_id left join cuteframe.company c on sc.company_id=c.id where a.order_sn = '{$order_sn}'";
            $company_type = $this->db->getOne($sql);
            if ($company_type == '2') {
                return BCD_PREFIX.'TGD'.$bc_id;
            } else if ($company_type == '3') {
                return BCD_PREFIX.'JXS'.$bc_id;
            }
        }
        
        return $bc_sn;
    }    
    

    public function getGiftByChannelId(){
        $channel_id = $this->filter["channel_id"];
        //$channel_id =$_REQUEST['channel_id'];
        if(empty($channel_id)){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = '必须传入渠道参数';
            $this -> return_msg = array();
            $this->display();
        }
        $where = "";
        if(isset($this->filter["gift_name"]) && !empty($this->filter["gift_name"])){
            $where.= " AND `name` like '%".$this->filter["gift_name"]."%'";
        }
        if(isset($this->filter["goods_number"]) && !empty($this->filter["goods_number"])){
            $where.= " AND `goods_number` = '".$this->filter["goods_number"]."'";
        }
        $sql = "SELECT * FROM gift_goods WHERE sale_way like concat('%', (SELECT `channel_class` FROM cuteframe.`sales_channels` WHERE id='{$channel_id}' ) ,'%') AND is_zp=1 AND status=1".$where;
        //file_put_contents('diamond.log', $sql);
        $res= $this->db->getAll($sql);
        $this -> error = 0;
        $this -> return_sql = $sql;
        $this -> error_msg = '';
        $this -> return_msg = $res;
        $this->display();        
    }

    //获取生产日志
    public function getBclog_by_zhmd(){

        $order_sn = $this->filter["order_sn"];
        $page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
        $page_size = intval($this -> filter["pageSize"]) > 0 ? intval($this -> filter["pageSize"]) : 10;
        if(empty($order_sn)){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = '订单号不能为空';
            $this -> return_msg = array();
            $this->display();
        }

        $model = Util::get_model("sales\AppOrderActionModel", [27]);
        $where = array();
        $where['order_sn'] = $order_sn;
        $datas = $model->pageList_by_zhmd($where,$page,$page_size,$useCache=true);
        $data = $datas['data'];
        if($data){
            $sups=$model->getAllSupplier();
            foreach ($data as $key => $value) {
                //$data['data'][$key]['remark'] = $this->replaceTsKezi($value['remark']);
                foreach ($sups as $k2 => $v) {
                    $data[$key]['remark']=str_replace($v['name'],'***',$data[$key]['remark']);
                }
                $data[$key]['remark'] =preg_replace("/跟单人由【(.*?)】改为【(.*?)】/is","跟单人由【***】改为【***】", $data[$key]['remark'], 1);
                //$data[$key]['remark'] =preg_replace("/跟单人：(.*?),/is","跟单人：*** ", $data[$key]['remark'], 1);
                $data[$key]['remark'] =preg_replace("/跟单人：((?!\,).)*/is","跟单人：*** ", $data[$key]['remark'], 1);
                
                if(preg_match("/收货单(.*?)审核后货号(.*?)自动绑定订单/is", $data[$key]['remark'])){
                    unset($data[$key]);
                }
            }
        }
        $datas['data'] = $data;
        //返回信息
        if(isset($datas) && !empty($datas)){
            $this -> error = 0;
            $this -> return_sql = "";
            $this -> error_msg = "查询成功";
            $this -> return_msg = $datas;
            $this->display();
        }else{
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "查询失败";
            $this -> return_msg = array();
            $this->display();
        }
    }

    
    /*
     * 物控系统获取渠道送货地址
     */
    public function material_order_get_store_address(){
        $department_id = $this ->filter["department_id"];
        if(empty($store_id)){
            $data['data'] = null;
        }else{
            $sql = "select a.id,a.channel_name,concat(c.accepter_address,' ',c.accepter_name,c.accepter_mobile) as shop_address from cuteframe.sales_channels a left join cuteframe.shop_cfg b on a.channel_own_id=b.id and a.channel_type=2 left join cuteframe.shop_cfg_accepter c on b.id=c.id where a.id ='{$department_id}'";
            $data['data'] = $this->db->getAll($sql);
        }
        //返回信息
        if (!$data['data']) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = "未查询到店铺地址";
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
      }         
    }


    //物控订单主表保存api
    public function material_order_insert(){
        $pdo = $this->db->db();     
        $newdo = array(      
            'bill_no'=>uniqid(), 
            'bill_status' => 1,     
            'department_id'=>$this ->filter["department_id"],                 
            'bill_note'=>$this ->filter["bill_note"],
            'create_user'=>$this ->filter["create_user"],
            'address'=>$this ->filter["address"],           
            'create_time'=>$this ->filter["create_time"],
        );
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务    
            $sql = "insert into warehouse_shipping.material_order (bill_no,bill_status,department_id,bill_note,create_user,address,create_time) values (?,?,?,?,?,?,?)";
            $res= $this->db->query($sql,array_values($newdo));
            $insertid=0;
            if($res===false){
                $error ="保存数据失败";
                throw new Exception("保存数据失败");
            }else{
                $error=$this->db->insertId();
                $bill_id = substr($error, -4);
                $bill_no = "O" . date('Ymd', time()) . rand(100, 999) . str_pad($error, 4,"0", STR_PAD_LEFT);
                $res = $this->db->query("update warehouse_shipping.material_order set bill_no='{$bill_no}' where id='{$error}'");     
            }
            $pdo->commit();//如果没有异常，就提交事务
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        }catch(Exception $e){//捕获异常
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            $error = $e->getMessage();
        }        
        //返回信息
        if (!is_numeric($error)) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = $error;
            $this->return_msg = '';
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $error;
            $this->display();
      }        
    }

    //物控订单主表保存api
    public function material_order_goods_insert(){
        $pdo = $this->db->db();     
        $newdo = array(       
            'bill_id'=>$this ->filter["bill_id"],                  
            'goods_sn'=>$this ->filter["goods_sn"],
            'goods_num'=>$this ->filter["goods_num"],
            'goods_price'=>$this ->filter["goods_price"],           
        );
        $error ='';
        try{
            if(empty($newdo['bill_id'])){
                throw new Exception('添加单据明细必须输入bill_id参数');
            }

            //$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            //$pdo->beginTransaction();//开启事务    
            $sql = "insert into warehouse_shipping.material_order_goods (bill_id,goods_sn,goods_num,goods_price) values (?,?,?,?)";
            $res= $this->db->query($sql,array_values($newdo));
            $insertid=$this->insertId();            
        }catch(Exception $e){//捕获异常         
            $error = $e->getMessage();
        }        
        //返回信息
        if (!empty($error)) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = $error;
            $this->return_msg = '';
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $insertid;
            $this->display();
      }        
    }



    public function get_material_order_row(){
        $where = $this->filter;
        $bill_no =  empty($where['bill_no']) ? 1 : $where['bill_no'] ;
        $bill_id =  empty($where['bill_id']) ? 10 : $where['bill_id'];
        $useCache =  false;
        //$dow_info =  $where['dow_info'];
        $error ="";
        $data =array();
        try{
                if(empty($bill_no)&&empty($bill_id))
                    throw new Exception("必须输入bill_no或者bill_id参数");
                                                   
                //不要用*,修改为具体字段
                $sql = "SELECT b.*,sc.channel_name,(select count(bg.id) from warehouse_shipping.material_order_goods bg,warehouse_shipping.material_goods g  where bg.goods_sn=g.goods_sn and bg.bill_id=b.id and bg.bill_id=b.id) as goods_num,ifnull((select sum(bg.goods_price) from warehouse_shipping.material_order_goods bg,warehouse_shipping.material_goods g  where bg.goods_sn=g.goods_sn and bg.bill_id=b.id and bg.bill_id=b.id and g.goods_type=1),0) as material_amount,ifnull((select sum(bg.goods_price) from warehouse_shipping.material_order_goods bg,warehouse_shipping.material_goods g  where bg.goods_sn=g.goods_sn and bg.bill_id=b.id and bg.bill_id=b.id and g.goods_type=2),0) as gift_amount from warehouse_shipping.material_order b left join cuteframe.sales_channels sc on sc.id = b.department_id";
                
                $str = '';
               
                if(!empty($where['bill_no'])){   
                    if(is_array($where['bill_no'])){
                        $where['bill_no'] = "'".implode("','",$where['bill_no'])."'";
                        $str .= "b.`bill_no` in ({$where['bill_no']}) AND ";
                    }else{
                        $str .= "b.`bill_no` = '{$where['bill_no']}' AND ";
                    }
                }   
                
               
                if(!empty($where['bill_id'])){
                    $str .="b.id='{$where['bill_id']}' AND ";
                }       
               
                if($str)
                {
                    $str = rtrim($str,"AND ");//这个空格很重要
                    $sql .=" WHERE ".$str;
                }
                $sql .= " ORDER BY b.`id` DESC";
                
               
                //echo $sql;  
                //file_put_contents('apitest99.log',json_encode($sql));
                $data = $this->db->getPageListNew($sql,array(),$page, $pageSize,false);
               
        }catch(Exception $e){
            $data =null;
            $error = $e->getMessage();
        }        
        //echo $error;
        //返回信息
        if (!empty($error)) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = $error;
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
      }        
    }        
    

    /**
     *  pageList，物控订单分页列表
     *
     *  @url MaterialBillController/search
     */
    public function material_order_pageList ()
    {
        /*
        ini_set('display_errors','1');
        error_reporting(E_ALL);
        error_reporting(E_ERROR);
        $where =$_REQUEST;
        */
        $where = $this->filter;
        $page =  empty($where['page']) ? 1 : $where['page'] ;
        $pageSize =  empty($where['pageSize']) ? 10 : $where['pageSize'];
        $useCache =  $where['useCache'];
        //$dow_info =  $where['dow_info'];
        $error ="";
        $data['data'] =array();
        try{
                if(empty($where))
                    throw new Exception("参数异常");
                                                   
                //不要用*,修改为具体字段
                $sql = "SELECT DISTINCT b.*,c.channel_name,p.company_name,p.company_type,'/' as goods_sn,'/' as goods_name,'/' as goods_allnum,'/' as goods_allcost,'/' as goods_allshijia from warehouse_shipping.material_order b 
                    LEFT JOIN warehouse_shipping.material_order_goods bg on b.id=bg.bill_id 
                    LEFT JOIN warehouse_shipping.material_goods g on bg.goods_sn=g.goods_sn            
                    LEFT JOIN cuteframe.sales_channels c on b.department_id=c.id 
                    LEFT JOIN cuteframe.company p on c.company_id=p.id ";
                
                $str = '';
                $where_old = $where;
                if(!empty($where['bill_no'])){   
                    if(is_array($where['bill_no'])){
                        $where['bill_no'] = "'".implode("','",$where['bill_no'])."'";
                        $str .= "b.`bill_no` in ({$where['bill_no']}) AND ";
                    }else{
                        $str .= "b.`bill_no` = '{$where['bill_no']}' AND ";
                    }
                }   
                
                if(!empty($where['bill_status'])){
                    $str .= "b.`bill_status` = {$where['bill_status']} AND ";
                }
                
                if(!empty($where['goods_sn'])){
                    $str .="g.goods_sn='{$where['goods_sn']}' AND ";
                }       
                if(!empty($where['style_sn'])){
                    $str .="g.style_sn='{$where['style_sn']}' AND ";
                }
                if(!empty($where['style_name'])){
                    $str .="g.style_name like '%{$where['style_name']}%' AND ";
                }
                if(!empty($where['goods_name'])){
                    $str .="g.goods_name like '%{$where['goods_name']}%' AND ";
                }
                if(!empty($where['catetory1'])){
                    $str .="g.catetory1 = '{$where['catetory1']}' AND ";
                }
                if(!empty($where['catetory2'])){
                    $str .="g.catetory2 = '{$where['catetory2']}' AND ";
                }
                if(!empty($where['goods_spec'])){
                    $str .="g.goods_spec like '%{$where['goods_spec']}%' AND ";
                }
                if(!empty($where['create_user'])){
                    $str .="b.create_user = '{$where['create_user']}' AND ";
                }       
                if (!empty($where['time_start'])) {
                    $str .= " b.create_time>='{$where['time_start']} 00:00:00' AND ";
                }
                if (!empty($where['time_end'])) {
                    $str .= " b.create_time <= '{$where['time_end']} 23:59:59' AND ";
                }
                if (!empty($where['check_time_start'])) {
                    $str .= " b.check_time>='{$where['check_time_start']} 00:00:00' AND ";
                }
                if (!empty($where['check_time_end'])) {
                    $str .= " b.check_time <= '{$where['check_time_end']} 23:59:59' AND ";
                }   

                $where['companyId'] = empty($where['companyId']) ? -1 : $where['companyId'];
                
                if($where['companyId']<>58){
                    if(!empty($where['department_id'])){
                        $str .="b.department_id='{$where['department_id']}' AND ";
                    }else{
                        $str .="b.department_id='0' AND ";
                    }
                }else{
                    if(!empty($where['department_id'])){
                        $str .="b.department_id='{$where['department_id']}' AND ";
                    }                    
                }

                if($str)
                {
                    $str = rtrim($str,"AND ");//这个空格很重要
                    $sql .=" WHERE ".$str;
                }
                $sql .= " ORDER BY b.`id` DESC";
                
                //echo $sql; 
                //$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
                //下载
                /*
                if(isset($dow_info) && $dow_info =="dow_info"){
                    $data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
                    $this->download($data['data']);
                }
                if(!empty($where['show_detail'])){
                    $data = $this->pageList_detail($where_old,$page,$pageSize=10,$useCache=true);
                }else
                */  
                //echo $sql;  
                //file_put_contents('apitest99.log',json_encode($sql));
                $data = $this->db->getPageListNew($sql,array(),$page, $pageSize,false);
                foreach ($data['data'] as $key => $row) {
                    $res1= array('allnum' =>0 ,'allcost' =>0,'allshihia' =>0 );
                    $res2=array();  
                    //$res2=$this->getTotal($row['id']);
                    $sql2="select sum(goods_num) as allnum,sum(goods_price) as allprice from warehouse_shipping.material_order_goods where bill_id='{$row['id']}'";
                    $res2=$this->db->getRow($sql2);
                    if($res2)
                        $res1=$res2;
                    $data['data'][$key]=array_merge($data['data'][$key],$res1);
                }
        }catch(Exception $e){
            $data['data'] =null;
            $error = $e->getMessage();
        }        
        //echo $error;
        //返回信息
        if (!empty($error)) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = $error;
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
      }        
    }

    /**
     *  pageList，物控详情分页列表
     *
     *  @url MaterialBillController/search
     */
    public  function material_order_pageList_detail ()
    {
        //$where,$page,$pageSize=10,$useCache=true
        $where = $this->filter;
        $page =  empty($where['page']) ? 1 : $where['page'];
        $pageSize =  empty($where['pageSize']) ? 10 : $where['pageSize'];
        $useCache =  false;
        //$dow_info =  $where['dow_info'];
        $data['data'] =array();
        $error = "";
        try{
                if(empty($where))
                    throw new Exception("参数异常");
                                    
                //不要用*,修改为具体字段
                //不要用*,修改为具体字段
                $sql = "SELECT b.*,g.goods_sn,g.goods_name,g.goods_spec,g.style_sn,g.style_name,g.catetory1,g.catetory2,g.catetory3,g.goods_sale_price,g.unit,c.channel_name,p.company_name,p.company_type,bg.goods_num,bg.goods_price from material_order b 
                    LEFT JOIN warehouse_shipping.material_order_goods bg on b.id=bg.bill_id 
                    LEFT JOIN warehouse_shipping.material_goods g on bg.goods_sn=g.goods_sn            
                    LEFT JOIN cuteframe.sales_channels c on b.department_id=c.id 
                    LEFT JOIN cuteframe.company p on c.company_id=p.id ";
                    
                
                $str = '';
                if(!empty($where['bill_no'])){   
                    if(is_array($where['bill_no'])){
                        $where['bill_no'] = "'".implode("','",$where['bill_no'])."'";
                        $str .= "b.`bill_no` in ({$where['bill_no']}) AND ";
                    }else{
                        $str .= "b.`bill_no` = '{$where['bill_no']}' AND ";
                    }
                }   
                if(!empty($where['bill_type'])){
                    $str .= "b.`bill_type` = '{$where['bill_type']}' AND ";
                }
                if(!empty($where['bill_status'])){
                    $str .= "b.`bill_status` = {$where['bill_status']} AND ";
                }
                if(!empty($where['in_warehouse_id'])){
                    $str .="bg.in_warehouse_id={$where['in_warehouse_id']} AND ";
                }
                if(!empty($where['out_warehouse_id'])){
                    $str .="bg.out_warehouse_id={$where['out_warehouse_id']} AND ";
                }
                if(!empty($where['supplier_id'])){
                    $str .="bg.supplier_id={$where['supplier_id']} AND ";
                }
                if(!empty($where['goods_sn'])){
                    $str .="g.goods_sn='{$where['goods_sn']}' AND ";
                }       
                if(!empty($where['style_sn'])){
                    $str .="g.style_sn='{$where['style_sn']}' AND ";
                }
                if(!empty($where['style_name'])){
                    $str .="g.style_name like '%{$where['style_name']}%' AND ";
                }
                if(!empty($where['goods_name'])){
                    $str .="g.goods_name like '%{$where['goods_name']}%' AND ";
                }
                if(!empty($where['catetory1'])){
                    $str .="g.catetory1 = '{$where['catetory1']}' AND ";
                }
                if(!empty($where['catetory2'])){
                    $str .="g.catetory2 = '{$where['catetory2']}' AND ";
                }
                if(!empty($where['goods_spec'])){
                    $str .="g.goods_spec like '%{$where['goods_spec']}%' AND ";
                }
                if(!empty($where['create_user'])){
                    $str .="b.create_user = '{$where['create_user']}' AND ";
                }           
                if (!empty($where['time_start'] !== "")) {
                    $str .= " b.create_time>='{$where['time_start']} 00:00:00' AND ";
                }
                if (!empty($where['time_end'])) {
                    $str .= " b.create_time <= '{$where['time_end']} 23:59:59' AND ";
                }
                if (!empty($where['check_time_start'])) {
                    $str .= " b.check_time>='{$where['check_time_start']} 00:00:00' AND ";
                }
                if (!empty($where['check_time_end'])) {
                    $str .= " b.check_time <= '{$where['check_time_end']} 23:59:59' AND ";
                }   
                if(!empty($where['department_id'])){
                    $str .="b.department_id='{$where['department_id']}' AND ";
                }
                /*
                $where['companyId'] = empty($where['companyId']) ? -1 : $where['companyId'];
                if($companyId<>58){
                    $str .="c.company_id='{$companyId}' AND ";
                }*/
                if($str)
                {
                    $str = rtrim($str,"AND ");//这个空格很重要
                    $sql .=" WHERE ".$str;
                }
                $sql .= " group by b.bill_no,bg.goods_sn";

                //echo $sql;
                //exit;
                $data = $this->db->getPageListNew($sql,array(),$page, $pageSize,$useCache);
               
        }catch(Exception $e){       
            $data['data'] =null;
            $error = $e->getMessage();
        }        
        //返回信息
        if (!empty($error)) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = $error;
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        }      
    }
 

    /**
     * 物控订单单据明细查询
     * @param unknown $where
     * @param unknown $page
     * @param number $pageSize
     * @param string $useCache
     */
    public function material_order_billGoodsPageList (){        
        $where = $this->filter;
        $page =  empty($where['page']) ? 1 : $where['page'];
        $pageSize =  empty($where['pageSize']) ? 10 : $where['pageSize'];
        $useCache =  false;
        //$dow_info =  $where['dow_info'];
        $data['data'] =array();
        $error = "";
        try{
                if(empty($where))
                    throw new Exception("参数异常");        
                $sql = "SELECT g.goods_sn,g.goods_name,g.unit,bg.id,bg.goods_num,bg.goods_price,g.style_sn,g.goods_type  
                    FROM warehouse_shipping.material_order_goods bg 
                    LEFT JOIN warehouse_shipping.material_goods g on bg.goods_sn=g.goods_sn ";
                
                $str = '';
                if(!empty($where['bill_id'])){
                    $str .= "bg.`bill_id` = {$where['bill_id']} AND ";
                }
                if(!empty($where['goods_status'])){
                    $str .= "g.`goods_status` = {$where['goods_status']} AND ";
                }
                if(!empty($where['goods_type'])){
                    if(is_array($where['goods_type'])){
                        $str .= "g.`goods_type` in (".implode(",",$where['goods_type']).") AND ";
                    }else{
                        $str .= "g.`goods_type` =".$where['goods_type']." AND ";
                    }
                }       
                if($str)
                {
                    $str = rtrim($str,"AND ");//这个空格很重要
                    $sql .=" WHERE ".$str;
                }
                
                //$sql .= " ORDER BY bg.`goods_sn` ";
                $sql .= " ORDER BY g.goods_type asc,g.goods_sn asc";
                //echo $sql ;
                // exit;
                $data = $this->db->getPageListNew($sql,array(),$page, $pageSize,false);
                
        }catch(Exception $e){       
            $data =null;
            $error = $e->getMessage();
        }        
        //返回信息
        if (!empty($error)) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = $error;
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        } 

    }


    /**
     *  pageList，物控订单明细 添加商品分页列表
     *
     *  @url MaterialGoodsController/search
     */
    public function material_order_goodspageList ()
    {
        $where = $this->filter;
        $page =  empty($where['page']) ? 1 : $where['page'];
        $pageSize =  empty($where['pageSize']) ? 10 : $where['pageSize'];
        $bill_id =  empty($where['bill_id']) ? 0 : $where['bill_id'];

        $useCache =  false;      
        $data =array();
        $error = "";
        try{
                if(empty($where))
                    throw new Exception("参数异常"); 
                if(empty($bill_id))
                    throw new Exception("参数bill_id必填");                               
                $sql = "SELECT mg.*,bsi.jiajialv,bg.goods_num,(select sum(v.inventory_qty) from warehouse_shipping.material_inventory v where v.goods_sn=mg.goods_sn and v.inventory_qty>0) as inventory_qty,(select img.thumb_img from front.app_style_gallery img where img.style_id=bsi.style_id and img.image_place=1 limit 1) as style_img FROM warehouse_shipping.material_goods as mg left join front.base_style_info bsi on mg.style_sn=bsi.style_sn left join warehouse_shipping.material_order_goods bg on mg.goods_sn=bg.goods_sn and bg.bill_id='{$bill_id}' ";
                $str = '';
                if(!empty($where['style_sn'])){
                    $str .=" mg.style_sn = '{$where['style_sn']}'  AND ";
                }
                if(!empty($where['goods_sn'])){
                    $str .=" mg.goods_sn = '{$where['goods_sn']}'  AND ";
                }
                if(!empty($where['goods_status'])){
                    $str .=" mg.goods_status = '{$where['goods_status']}'  AND ";
                } 
                if(!empty($where['goods_type'])){
                    $str .=" mg.goods_type = '{$where['goods_type']}'  AND ";
                }
                if(!empty($where['style_name'])){
                    $str .=" mg.style_name like '%{$where['style_name']}%'  AND ";
                }

                if(!empty($where['goods_name'])){
                    $str .=" mg.goods_name like '%{$where['goods_name']}%'  AND ";
                }

                if(!empty($where['goods_spec'])){
                    $str .=" mg.goods_spec like '%{$where['goods_spec']}%'  AND ";
                }

                if(!empty($where['catetory1'])){
                    $str .=" mg.catetory1 = '%{$where['catetory1']}'  AND ";
                }


                if(!empty($where['catetory2'])){
                    $str .=" catetory2 = '{$where['catetory2']}'  AND ";
                }

                if(!empty($where['catetory3'])){
                    $str .=" catetory3 = '{$where['catetory3']}'  AND ";
                }

                if(!empty($where['cost'])){
                    $str .=" cost = '{$where['cost']}' AND ";
                }
               
                if($str)
                {
                    $str = rtrim($str,"AND ");//这个空格很重要
                    $sql .=" WHERE ".$str;
                }
                if(!empty($where['order_by_field']))
                    $sql .= " ORDER BY mg.{$where['order_by_field']} ";
                else        
                    $sql .= " ORDER BY mg.goods_sn";
                //echo $sql;
                $data = $this->db->getPageListNew($sql,array(),$page, $pageSize,$useCache);
        }catch(Exception $e){       
            $data =null;
            $error = $e->getMessage();
        }        
        //返回信息
        if (!empty($error)) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = $error;
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        } 
    }


    /**
     * 物控订单审核
     * @param unknown $bill_id
     * @param string $transMode
     * @throws Exception
     * @return multitype:number string NULL
     */
    public function material_order_checkOrderPass(){
        $where = $this->filter;       
        $bill_id =  empty($where['bill_id']) ? 0 : $where['bill_id'];
       
        $data =array();
        $error = "";
        try{
                if(empty($where))
                    throw new Exception("参数异常"); 
                if(empty($bill_id))
                    throw new Exception("参数bill_id必填");
                $result = array('success'=>0,'error'=>'');      
                   
                $user = $where['userName'];
                $time = date("Y-m-d H:i:s");
                
                $sql = "select * from warehouse_shipping.material_order where id={$bill_id}";
                $billInfo = $this->db->getRow($sql);
                if($billInfo['bill_status']!=1){
                    throw new Exception("单据不是已保存状态，不能审核！");
                }
               
                        
                $sql = "select * from warehouse_shipping.material_order_goods where bill_id={$bill_id}";
                $billGoodsList = $this->db->getAll($sql);
                if(empty($billGoodsList)){
                    throw new Exception("单据还没有添加明细！");
                } 
            
                $sql = "update warehouse_shipping.material_order set bill_status=2,check_user='{$user}',check_time='{$time}' where id={$bill_id}";
                $this->db->query($sql);
               
                $data = 1;         
        }catch(Exception $e){       
            $data =null;
            $error = $e->getMessage();
        }        
        //返回信息
        if (!empty($error)) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = $error;
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        }    
    }

    /**
     * 物控订单审核
     * @param unknown $bill_id
     * @param string $transMode
     * @throws Exception
     * @return multitype:number string NULL
     */
    function material_order_checkOrderCancel(){
        $where = $this->filter;       
        $bill_id =  empty($where['bill_id']) ? 0 : $where['bill_id'];
       
        $data =array();
        $error = "";
        try{
                if(empty($where))
                    throw new Exception("参数异常"); 
                if(empty($bill_id))
                    throw new Exception("参数bill_id必填");                 
                   
                $user = $where['userName'];
                $time = date("Y-m-d H:i:s");
                
                $sql = "select * from warehouse_shipping.material_order where id={$bill_id}";
                $billInfo = $this->db->getRow($sql);
                if($billInfo['bill_status']!=1){
                    throw new Exception("单据不是已保存状态，不能取消！");
                }
               
                           
                $sql = "update warehouse_shipping.material_order set bill_status=1,check_user='{$user}',check_time='{$time}' where id={$bill_id}";
                $this->db->query($sql);
               
                $data = 1;         
        }catch(Exception $e){       
            $data =null;
            $error = $e->getMessage();
        }        
        //返回信息
        if (!empty($error)) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = $error;
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        }    
    }



    /**
     *  deleteOrderGoods，删除单据明细
     */
    public function deleteOrderGoods(){
        $where = $this->filter;       
        $id =  empty($where['id']) ? 0 : $where['id'];
       
        $data =array();
        $error = "";
        try{
                if(empty($where))
                    throw new Exception("参数异常"); 
                if(empty($id))
                    throw new Exception("参数id必填");
                $result = array('success'=>0,'error'=>'');      
                   
                $user = $where['userName'];
                $time = date("Y-m-d H:i:s");
                
                $sql = "select o.* from warehouse_shipping.material_order o,warehouse_shipping.material_order_goods bg where o.id=bg.bill_id and bg.id='{$id}'";
                $billInfo = $this->db->getRow($sql);
                if($billInfo['bill_status']!=1){
                    throw new Exception("单据不是已保存状态，不能删除！");
                }
               
                          
                $sql = "delete from warehouse_shipping.material_order_goods where where id='{$id}'";
                $this->db->query($sql);
               
                $data = 1;         
        }catch(Exception $e){       
            $data =null;
            $error = $e->getMessage();
        }        
        //返回信息
        if (!empty($error)) {
            $this->error = 1;
            $this->return_sql = $sql;
            $this->error_msg = $error;
            $this->return_msg = array();
            $this->display();
        } else {
            $this->error = 0;
            $this->return_sql = $sql;
            $this->return_msg = $data;
            $this->display();
        }  
    }

}
?>
