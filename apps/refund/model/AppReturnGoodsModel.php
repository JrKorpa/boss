<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReturnGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-13 14:27:42
 *   @update	:
 *  -------------------------------------------------
 */
class AppReturnGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_return_goods';
		$this->pk='return_id';
		$this->_prefix='';
        $this->_dataObject = array("return_id"=>"退款单id",
		"department"=>"所属部门",
		"apply_user_id"=>"申请人",
		"order_id"=>"订单id",
		"order_sn"=>"订单编号",
		"order_goods_id"=>" ",
		"should_return_amount"=>"应退金额",
		"apply_return_amount"=>"申请金额",
		"real_return_amount"=>"实退金额",
		"confirm_price"=>"审核金额",
		"return_res"=>"退款原因",
		"return_type"=>"退款类型,1转单,2打卡,3现金",
		"return_card"=>"退款账户",
		"consignee"=>"退款人",
		"mobile"=>"联系电话",
		"bank_name"=>"开户银行",
		"apply_time"=>"申请时间",
		"pay_id"=>"实际付款人",
		"pay_res"=>"付款备注",
		"pay_status"=>"支付状态",
		"pay_attach"=>"付款附件",
		"pay_order_sn"=>"支付的订单号",
		"jxc_order"=>"进销存退货单",
		"zhuandan_amount"=>" ",
		"check_status"=>"0未操作1主管审核通过2库管审核通过3事业部通过4现场财务通过5财务通过",
        "return_goods_id"=>"退款货号，多个用逗号隔开"
            
        );
		parent::__construct($id,$strConn);
	}
	/**
	 *	pageList，分页列表
	 *
	 *	@url AppReturnGoodsController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
    {
        $leftjoin = "";
        /*if(SYS_SCOPE == 'zhanting'){
            $leftjoin = " left join app_order.base_order_info oi on oi.id = a.order_id ";
        }*/
        //不要用*,修改为具体字段
        $sql = "SELECT `a`.*,`b`.`goods_status`,`b`.`deparment_finance_status`,`b`.`finance_status`,`b`.`leader_status`,`b`.`cto_status`,u.account as apply_user_name  FROM `".$this->table()."` as a left join `app_return_check` as b on `a`.`return_id` = `b`.`return_id` {$leftjoin} left join cuteframe.user  u on a.apply_user_id=u.id ";
        $str = '';
        if(!empty($where['return_id']))
        {
            $str .= "a.`return_id` = ".intval($where['return_id'])." AND ";
        }
        if(!empty($where['order_sn']))
        {
            if(strpos($where['order_sn'],',')){
                $str .= "a.`order_sn`in (".$where['order_sn'].") AND ";
            }else{
                $str .= "a.`order_sn`=".$where['order_sn']." AND ";
            }
        }
        if(!empty($where['return_type']))
        {
            $str .= "a.`return_type`='".$where['return_type']."' AND ";
        }
        if(!empty($where['check_status']) || (isset($where['check_status']) && $where['check_status'] == 0)){
            $str .= "`a`.`check_status` = ".($where['check_status']-1)." AND ";
            if($where['check_status'] == 1){
                $str .= "(`b`.`leader_status` IS NULL or `b`.`leader_status`=0) AND ";
            }
        }
        if(!empty($where['start_time'])){

            $str .= "a.`apply_time` >= '".$where['start_time']." 00:00:00' AND ";
        }
        if(!empty($where['end_time'])){

            $str .= "a.`apply_time` <= '".$where['end_time']." 23:59:59' AND ";
        }
        if(!empty($where['finance_start_time'])){

            $str .= "b.`deparment_finance_time` >= '".$where['finance_start_time']." 00:00:00' AND ";
        }
        if(!empty($where['finance_end_time'])){

            $str .= "b.`deparment_finance_time` <= '".$where['finance_end_time']." 23:59:59' AND ";
        }        
        if(!empty($where['department'])){
        	$SalesModel=new SalesModel(27);
        	//录单来源：B2C订单，制单人是：system_api 的订单，有官方网站部销售渠道权限的管理员才能操作
        	$orderIdInStr=$SalesModel->orderIdInStr();  
        	$departmentArr=explode(',', $where['department']);
        	if(in_array(1, $departmentArr)){
        		$str .= "(a.`department` in (".$where['department'].") ";
        		 if(!empty($orderIdInStr)){
        		 	$str .=" OR a.order_id in ({$orderIdInStr})) AND ";
        		 }else{
        		 	$str .=" ) AND ";
        		 }
        	}else{
        		
        		$str .= "a.`department` in (".$where['department'].") ";
        		if(!empty($orderIdInStr)){
        			$str .=" AND a.order_id not in ({$orderIdInStr}) AND ";
        		}else{
        			$str .="  AND ";
        		}
        	 }
           // $str .= "a.`department` in (".$where['department'].") AND ";
        }

        if(!empty($where['apply_user']))
        {
            $str .= " u.account='".$where['apply_user']."' AND ";
        }        

        //zt隐藏
        /*if(SYS_SCOPE == 'zhanting'){
            $str .=" oi.hidden <> 1";
        }*/

        if(SYS_SCOPE == 'zhanting' 
            && !empty($where['finance_status']) 
            && $where['finance_status'] == 1){
            //$str .= " (b.`finance_status`<>1 or b.return_id is null) AND ";
            $str.=" a.check_status < 5 AND ";
        }

        //退款商品ID搜索
        if(!empty($where['return_goods_id'])){
            $return_goods_ids = implode("','",$where['return_goods_id']);
            $str .=" a.order_goods_id in(select order_goods_id from warehouse_shipping.warehouse_goods where order_goods_id>0 and goods_id in('{$return_goods_ids}'))";
        }
        if($str)
        {
            $str = rtrim($str,"AND ");//这个空格很重要
            $sql .=" WHERE ".$str;
        }
        $sql .= " ORDER BY a.`return_id` DESC";
        //echo $sql; exit;
        $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
        if($data['data']){
            $userModel = new UserModel(1);
            $departmentModel = new SalesChannelsModel(1);
            foreach ($data['data'] as &$val){
                //$val['apply_user_name'] = $val['apply_user_name'] = $userModel->getAccount($val['apply_user_id']);
                $department_name = $departmentModel->getSalesChannelsInfo("`channel_name`",array('id'=>$val['department']));
                if(!empty($department_name[0])){
                   $val['department'] = $department_name[0]['channel_name'];
                }else{
                    $val['department'] = '';
                }
                $val['goods_sn'] = '';
                if($val['order_goods_id']!=0){
                    $sql = "select `goods_sn` from `app_order_details` where `id`={$val['order_goods_id']}";
                    $val['goods_sn'] = $this->db()->getOne($sql);
                }
            }
            unset($val);
        }
        return $data;
    }
    function getBillSGoodsId($order_goods_id,$order_sn){
        $sql = "select bg.goods_id  from  warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_bill b,warehouse_shipping.warehouse_goods g where  bg.bill_id=b.id and b.bill_type='S' and b.bill_status=2 and bg.goods_id=g.goods_id and g.order_goods_id='{$order_goods_id}' and b.order_sn='{$order_sn}' and g.is_on_sale=3";
        return $this->db()->getAll($sql);        
    }
    function savedates($id){
        $sql = "UPDATE app_return_goods SET check_status = 0 WHERE return_id=$id";
        return $this->db()->query($sql);
    }
    
	 function setLeaderStatus($id){
          $sql = "DELETE FROM app_return_check WHERE return_id =$id";
         
        return $this->db()->query($sql); 
    }
	
	
	/**
	 *	get_order_info_by_order_sn，通过订单号取数据
	 *
	 *	@url AppReturnGoodsController/get_order_info_by_order_sn
	 */
	function get_order_info_by_order_sn ($order_sn)
	{
		$sql = "SELECT * FROM `base_order_info` as `oi`,`app_order_account` as `oa` ";
		$str = '';
		if($order_sn != "")
		{
			$str .= " AND `oi`.`order_sn` = '".addslashes($order_sn)."' ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE `oi`.`id`=`oa`.`order_id` ".$str;
		}
		$data = $this->db()->getRow($sql);
		return $data;
	}

	public function get_order_details_by_order_id_and_is_return($order_id,$select='*'){
			$sql='Select '.$select.' from `app_order_details` where `order_id`='.$order_id.' and is_return =0 and is_finance = 2';
			return $this->db()->getAll($sql);
	}

        public function get_return_goods_is_return($order_id){
            $sql = 'SELECT order_goods_id FROM app_return_goods WHERE `order_id`='.$order_id;
            return $this->db()->getAll($sql);
        }

                public function get_return_goods_by_order_goods_id($order_goods_id,$select='*'){
        if(empty($order_goods_id)){
            return false;
        }
        $sql='SELECT '.$select.' FROM `app_return_goods` where `order_goods_id`="'.$order_goods_id.'"';
        return $this->db()->getAll($sql);
	}

	public function get_return_check_by_return_id($return_id,$select='*'){
        if(empty($return_id)){
            return false;
        }
        $sql='SELECT '.$select.' FROM `app_return_check` where `return_id`="'.$return_id.'"';
        return $this->db()->getRow($sql);
	}
	
	//根据退款单中 订单id  查到订单总金额
	public function get_monery_by_return_id($order_id,$select='*'){
		if(empty($order_id)){
			return false;
		}
		$sql='SELECT '.$select.' FROM `app_order_account` where `order_id`='.$order_id.'';
		//echo $sql;exit;
		return $this->db()->getRow($sql);
	}

    /**
     *  getGoodsCheckStatus,获取goods_check_status;
     *
     *  @url AppReturnCheckController/get
     */
    function getNewCheckStatus($id){

        $sql = "SELECT `check_status` FROM `".$this->table()."` WHERE `return_id` = ".$id;
        $id = $this->db()->getOne($sql);
        return $id;
    }
    
    /**
     * 获取一条退款申请数据
     * @param type $id
     * @return boolean
     */
    public function getInfoById($id) {
        if($id<1){
            return false;
        }
        $select = "`rg`.`return_id`,`rg`.`apply_user_id`,`rg`.`return_card`,`rg`.`bank_name`,`rg`.`mobile`,`rg`.`consignee`,`rg`.`order_sn`,`rg`.`apply_time`,`rg`.`department`,`rg`.`order_goods_id`,`rg`.`order_id`,`rg`.`apply_return_amount`,`rg`.`return_type`";
        $select .= ",`rg`.`return_res`,`rc`.`leader_id`, `rc`.`leader_res`, `rc`.`leader_status`, `rc`.`leader_time`, `rc`.`goods_comfirm_id`, `rc`.`goods_res`, `rc`.`goods_status`, `rc`.`goods_time`, `rc`.`cto_id`, `rc`.`cto_res`, `rc`.`cto_status`, `rc`.`cto_time`, `rc`.`deparment_finance_id`, `rc`.`deparment_finance_status`, `rc`.`deparment_finance_res`, `rc`.`deparment_finance_time`, `rc`.`finance_id`, `rc`.`bak_fee`, `rc`.`finance_res`, `rc`.`finance_status`, `rc`.`finance_time`";
        $select .= ",`rc`.`pay_res`,`rc`.`pay_attach`";
        $sql = "SELECT $select FROM `".$this->table()."` as `rg` left join `app_return_check` as `rc` on `rg`.`return_id` = `rc`.`return_id` WHERE `rg`.`return_id`=$id";
        return $this->db()->getRow($sql);
    }
    
    
    public function existOrderSn($order_sn,$detail_id) {
        //  $detail_id = join(',', $detail_id);
      $sql = "select `rg`.`order_goods_id` from `{$this->table()}` as `rg`,`app_return_check` as `rc` where `rg`.`return_id`=`rc`.`return_id` AND `rc`.`leader_status` <> 2 AND `rg`.`order_sn`='{$order_sn}' and `order_goods_id`=$detail_id";
     //   $sql = "select `rg`.`order_goods_id` from `{$this->table()}` as `rg`,`app_return_check` as `rc` where `rg`.`return_id`=`rc`.`return_id` AND `rc`.`leader_status` <> 2 AND `rg`.`order_sn`='{$order_sn}' and `order_goods_id` IN ($detail_id)";
 
        return $this->db()->getAll($sql);
    }
    /**
     * 获取一个订单未退货的商品集合
     * @param type $order_id
     * @return type
     */
    public function getGoodsList($order_id) {
        $sql = "select `id`,`goods_price`,`favorable_price` from `app_order_details` where `order_id`=$order_id and `is_return`=0";
        return $this->db()->getAll($sql);
    }
    /*
     * 划红线的退款金额
     */
    public function getGoodsPrice($order_id){
       $sql = "SELECT * FROM app_order_details WHERE id IN (SELECT order_goods_id FROM app_return_goods WHERE `order_id` = {$order_id} AND return_by=1 AND check_status >=4)";
     
       $detail = $this->db()->getAll($sql);
      
        return $detail;  
    }
    
       public function getReturnGoodsfavor($order_id){
         $sql = "SELECT SUM(favorable_price) AS t_favorable_price FROM app_order_details WHERE id IN (SELECT order_goods_id FROM app_return_goods WHERE `order_id` = {$order_id} AND check_status >=4) AND favorable_status =3";
         return $this->db()->getOne($sql);
    }
    
    public function getNewReturn($ids,$field){
		 $ids = array($ids);
         $detail_id = join(',', $ids);
         $where = '';
         if($field =='goods_price'){
             $where = " id IN ({$detail_id}) ";
         }  else {
             $where = " id IN ({$detail_id}) AND favorable_status =3 ";
         }
          $sql = "SELECT SUM($field) AS favorable_price FROM app_order_details WHERE $where";
         return $this->db()->getOne($sql);
    }

        /**
	 *设置退货状态
	 */
    public function setreturn($order_id) {
        $sql = "UPDATE `base_order_info` SET `apply_return` = 2 where `id`=$order_id";
        return $this->db()->query($sql);
    }
	/**
	 *还原退货状态
	 */
    public function returnapply($order_id) {
        $sql = "UPDATE `base_order_info` SET `apply_return` = 1 where `id`=$order_id";
        return $this->db()->query($sql);
    }
    /**
     * 获取用户id
     * @param type $order_id
     * @return type
     */
    public function getUserId($order_id) {
        $sql = "select `user_id` from `base_order_info` where `id`=$order_id";
        return $this->db()->getOne($sql);
    }

     /**
     * //判断退款商品是否是赠品且未绑定现货
     * @param type $order_id
     * @return type
     */
    public function getzengpinId($order_id) {
        $sql = "select `id` from `app_order_details` where `id`=$order_id  and is_finance=1 and is_zp=1 and goods_id=''";
        return $this->db()->getOne($sql);
    }

    /**
     * 获取货号
     * @param type $order_id
     * @return type
     */
    public function getGoodsIdByOrder($order_goods_id) {
    	$sql = "select `goods_id` from `app_order_details` where `id`=$order_goods_id";
    	return $this->db()->getOne($sql);
    }
    
    
   
    
    /**
     * 审核对应销售退货单(将warehouse里api的OprationBillD功能移到此处)
     *
     * @param unknown $param
     */
    public function OprationBillD($order_sn, $opra_uname, $bill_no, $type)
    {

        $status = true;
        $msg = '';
        try {
            if (empty($order_sn) || empty($opra_uname) || empty($bill_no) || empty($type)) {
                throw new Exception('参数不全！！');
            }
            if ($type != 1 && $type != 2) {
                throw new Exception('参数type不正确！');
            }
    
            $sql = "SELECT `id`, `bill_status`, `to_warehouse_id` , `to_warehouse_name` , `to_company_id` , `to_company_name` FROM warehouse_shipping.warehouse_bill WHERE `bill_no` = '" . $bill_no . "' AND `order_sn` = '" . $order_sn . "'";
            $bill_info = $this->db()->getRow($sql);
    
            // 传入的订单号和销售退货单号不对应或者错误造成不存在
            if (! count($bill_info)) {
                throw new Exception("订单" . $order_sn . "相关的销售退货单" . $bill_no . "不存在，请检查。");
            }
            // 单据只有在保存状态下才能进行审核或者取消
            if ($bill_info['bill_status'] != 1) {
                throw new Exception("单据" . $bill_no . "不是已保存状态，不允许操作。");
            }
    
            // 盘点中的仓库不能退货2015/6/27 星期六
            if (! empty($bill_info['to_warehouse_id'])) {
                if ($type == 1) {
                    $sql = "select `lock` from warehouse_shipping.warehouse where id = '{$bill_info['to_warehouse_id']}'";
                    $lock = $this->db()->getOne($sql);
                    if ($lock == 1) {
                        throw new Exception("退款仓库正在盘点中，不允许审核！");
                    }
                }
            }
    
            $sql = "SELECT goods_id FROM warehouse_shipping.warehouse_bill_goods WHERE bill_id = '" . $bill_info['id'] . "'";
            $goods_id_arr = $this->db()->getAll($sql);
    
            // 审核通过操作
            // 1、修改单据状态为已审核
            // 2、修改货品状态为库存
            // 3、解绑和订单的关系(退货了就不需要和这个订单有绑定关系了)
            $time = date('Y-m-d H:i:s');
            if ($type == 1) {
                $bill_status = 2; // 已审核
                // 2、修改货品状态为库存
                // 3、解绑和订单的关系(退货了就不需要和这个订单有绑定关系了)
                // 4、变更货品的所在地
                // 5、变更货品的柜位信息
                $sql = "SELECT `id` FROM warehouse_shipping.warehouse_box WHERE `warehouse_id` = {$bill_info['to_warehouse_id']} AND `box_sn` = '0-00-0-0' LIMIT 1";
                $box_id = $this->db()->getOne($sql);
                foreach ($goods_id_arr as $key => $val) {
                    $sql = "UPDATE warehouse_shipping.warehouse_goods set `is_on_sale` = 2 , `change_time` = '{$time}',`order_goods_id` = 0 , `company` = '{$bill_info['to_company_name']}' , `company_id` = {$bill_info['to_company_id']} , `warehouse` = '{$bill_info['to_warehouse_name']}' , `warehouse_id` = {$bill_info['to_warehouse_id']}, `box_sn` = '0-00-0-0' WHERE `goods_id` = " . $val['goods_id'];
                    // file_put_contents('e:/8.sql', "\r\n".$sql."\r\n",FILE_APPEND);

                    $res = $this->db()->query($sql);
                    if(!$res){
                         throw new Exception("更新货品状态失败！");
                    }
                    $sql = "UPDATE warehouse_shipping.goods_warehouse SET `warehouse_id` = {$bill_info['to_warehouse_id']}, box_id = {$box_id} WHERE `good_id` = '{$val['goods_id']}'";

                    $res1 = $this->db()->query($sql);
                     if(!$res1){
                        throw new Exception("修改货品失败！");
                    }
                }
            }
            // 取消操作
            // 1、修改单据状态为已取消
            // 2、修改货品状态为已销售
            if ($type == 2) {
                $bill_status = 3; // 已取消
                // 修改货品状态为已销售
                foreach ($goods_id_arr as $key => $val) {
                    $sql = "UPDATE warehouse_shipping.warehouse_goods set is_on_sale = 3 where goods_id = " . $val['goods_id'];

                    $res3 = $this->db()->query($sql);
                    if(!$res3){
                        throw new Exception("修改货品状态失败");
                         // $pdo->rollback();
                         //  return array('status'=>false,'msg'=>'修改货品状态失败');   
                    }
                }
            }
            // 修改单据状态
            $sql = "UPDATE warehouse_shipping.warehouse_bill SET `bill_status`= " . $bill_status . ",`check_user` = '" . $opra_uname . "',`check_time` = '" . $time . "'  WHERE id = " . $bill_info['id'];

            $res4 = $this->db()->query($sql);
            if(!$res4){
                throw new Exception("修改单据状态失败");
                // $pdo->rollback();
                // return array('status'=>false,'msg'=>'修改单据状态失败');
            }

            $sql = "INSERT INTO warehouse_shipping.warehouse_bill_status(`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES (" . $bill_info['id'] . ",'" . $bill_no . "'," . $bill_status . ",'" . $time . "','SYSTEM','" . Util::getClicentIp() . "')";
            // file_put_contents('e:/8.sql', "&&&\r\n".$sql."\r\n",FILE_APPEND);

            $res5 = $this->db()->query($sql);
            if(!$res5){
                throw new Exception("状态更新失败");
                // $pdo->rollback();
                // return array('status'=>false,'msg'=>'状态更新失败');
            }
        }catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }
            return array('status'=>$status,'msg'=>$msg);
    }
    
    /**
     * 记录订单操作日志
     * @param unknown $data
     */
    public function addOrderActionInfo($data)
    {
        $action_field = " `order_id`, `order_status`, `shipping_status`, `pay_status`,`create_time`,`create_user`,`remark`";
        $action_value = "" . $data['order_id'] . " ," . $data['order_status'] . " ," . $data['shipping_status'] . " ," . $data['pay_status'] . ", '" . $data['create_time'] . "' , '" . $data['create_user'] . "', '" . $data['remark'] . "' ";
        $sql = "INSERT INTO app_order_action (" . $action_field . ") VALUES (" . $action_value . ")";
        // file_put_contents('e:/8.sql', "77\r\n".$sql."\r\n",FILE_APPEND);

        $res = $this->db()->query($sql);
    }
    
    /**
     * 取消销售单 --- JUAN(将warehouse里api的OprationBillD功能移到此处逻辑未做改动)
     *
     * @param unknown $order_sn
     * @param unknown $detail_id
     * （仅支持订单退货调用，没事儿别动哦~ 因为取消销售单后有订单和货品解绑动作，一般用不到）
     */
    public function cancelBillS($order_sn, $detail_id)
    {
        $status = true;
        $msg = '';
        $salemodel = new SalesModel(27);

        try {
            if (empty($order_sn) || empty($detail_id)) {
                throw new Exception("缺少参数！");
            }
    
            $sql = "SELECT COUNT(1) FROM warehouse_shipping.warehouse_goods WHERE `order_goods_id` = " . $detail_id;
            if (! $this->db()->getOne($sql)) {
                throw new Exception($detail_id . " 没有绑定的货品，请检查。");
            }
            
            //如果发货状态是为发货，需要验证是否有对应的销售单
            $send_good_status = $salemodel->getSendGoodStatusByOrderSn($order_sn);
            if($send_good_status !=1){
                    // 找到对应的销售单
                $sql = "SELECT id FROM warehouse_shipping.warehouse_bill WHERE `order_sn` = '" . $order_sn . "' AND `bill_status` = 1 AND `bill_type` = 'S'";
                $bill_id = $this->db()->getOne($sql);
                if(empty($bill_id)){
                    throw new Exception( " 未找到对应销售单。");
                }
                // 在此不判断是否有有效的销售单，因为有可能一个订单两件货都在申请，第一个申请通过的时候就取消了销售单了。
                // 把已保存的相关联订单的销售单置为取消
                $sql = "UPDATE warehouse_shipping.warehouse_bill SET `bill_status` = 3 WHERE `id` = " . $bill_id;
                $res = $this->db()->query($sql);
                if(!res){
                    throw new Exception( "单据状态更新失败");
                }

                // 把取消的销售单中的货品置为库存状态
                $sql = "UPDATE warehouse_shipping.warehouse_goods as wg,warehouse_shipping.warehouse_bill_goods as wbg SET wg.`is_on_sale` = 2 WHERE wg.goods_id = wbg.goods_id and wbg.`bill_id` = " . $bill_id;
               $res = $this->db()->query($sql);
                if(!res){
                    throw new Exception( "库存状态更新失败");
                }
                // 把所传的detail_id绑定的货品解绑
                $sql = "UPDATE warehouse_shipping.warehouse_goods SET `order_goods_id` = 0 WHERE `order_goods_id` = '". $detail_id."'";
               $res = $this->db()->query($sql);
               if(!res){
                    throw new Exception( "货品解绑失败");
                }
            }else{
                // 把所传的detail_id绑定的货品解绑,销售单中的货品置为库存状态
                $sql = "UPDATE warehouse_shipping.warehouse_goods SET `order_goods_id` = 0,`is_on_sale` = 2 WHERE `order_goods_id` = '" . $detail_id."'";
               $res = $this->db()->query($sql);
               if(!res){
                    throw new Exception( "货品解绑失败");
                }
            }
     
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }
        return array('status'=>$status,'msg'=>$msg);
    }
    
    /**
     * 更新部分订单状态
     * @param unknown $order_sn
     * @param unknown $send_good_status
     * @param unknown $delivery_status
     * @return Ambigous <boolean, PDOStatement>
     */
    public function updateOrderInfoStatus($order_sn,$send_good_status,$delivery_status){
        $sql = "UPDATE base_order_info SET `send_good_status`={$send_good_status},`delivery_status`={$delivery_status} WHERE `order_sn`='{$order_sn}'";
        return $this->db()->query($sql);
    }
    
    /**
     * 查询绑定订单的仓储货品信息
     * @param unknown $order_goods_id
     */
    public function getWarehouseGoodsInfo($order_goods_id){
        $sql = "SELECT * FROM warehouse_shipping.warehouse_goods WHERE `order_goods_id` = '{$order_goods_id}'"; //暂时用＊号
        return $this->db()->getRow($sql);
    }
    

    /**
     * 通过货号绑定\解绑货品
     * @param unknown $bind_type
     * @param unknown $order_goods_id
     * @param unknown $goods_id
     */
    public function bindGoodsInfoByGoodsId($bind_type,$order_goods_id,$goods_id=0){
        $set = "";
        $where = "";
        if(($bind_type != 1 && $bind_type != 2 )|| empty($order_goods_id)){
            return false;
        }
    
        if($bind_type == 1){
            if(empty($goods_id)){
                return false;
            }
            $set .= " `order_goods_id` = '" . $order_goods_id . "' ";
            $where .= " `goods_id` = '" . $goods_id . "' ";
        }else if($bind_type == 2){
            $set .= " `order_goods_id` = '' ";
            $where .= " `order_goods_id` = '" . $order_goods_id . "' ";
        }
    
        if ($bind_type == 1) {//绑定
            $sql = "update warehouse_shipping.warehouse_goods set " . $set . "  WHERE " . $where . " and is_on_sale = 2 ";
        } else {
            $sql = "update warehouse_shipping.warehouse_goods set " . $set . "  WHERE " . $where;
        }
        return $this->db()->query($sql);
    }
    
    /**
     * 和供应商解除关系
     * @param unknown $arr
     */
    public function relieveProduct($arr){
        $status = true;
        $msg = '';
        try {
            if(!empty($arr) && count($arr) > 0){
                foreach ($arr as $key=>$value){
                    $sql = "SELECT  pg.`bc_id`, pg.`goods_id`,p.`status` FROM kela_supplier.product_goods_rel as pg left join kela_supplier.product_info as p on pg.`bc_id`=p.`id` WHERE 1";
                    if (!empty($value)) {
                        $sql .= " and pg.`goods_id` = '{$value}'";
                    }
                    $res = $this->db()->getRow($sql);
                    if($res){
                        //判断布产状态 若 已经生产
                        if($res['status']<=3){
                            //更新布产表中 生产状态为停止生产
                            $sql = "UPDATE kela_supplier.product_info SET `status` = 10  WHERE `id` =".$res['bc_id'];
                            $this->db()->query($sql);
                        }
                        //更新关系表中状态为无效
                        $sql = "UPDATE kela_supplier.product_goods_rel SET `status` = 1 WHERE `bc_id` =".$res['bc_id'];
                        $this->db()->query($sql);
                    }
                }
            }
        } catch (Exception $e) {
            $status = false;
            $msg = $e->getMessage();
        }       
        return array('status'=>$status,'msg'=>$msg);
    }
    
    
    /**
     * 更新审核表对应的审核阶段的字段
     * @param type $return_id
     * @param type $fileds 更新的数据
     * @return boolean
     */
    function modfiyCheckStatus($return_id,$fileds){
        if(intval($return_id) < 1 || empty($fileds)){
            return false;
        }
        $param = '';
        foreach ($fileds as $key=>$val){
            $param .= "`{$key}`='{$val}',";
        }
        if(empty($param)){
            return false;
        }
        $param = rtrim($param,',');
        $sql = "UPDATE `app_return_check` SET $param WHERE `return_id` = $return_id";
        return $this->db()->query($sql);
    }
    
    
    //检查是否还有退款流程的货(申请状态<4且不是驳回)
    function  getReturnGoodsByWhere($order_id){
    	$sql="select return_id,check_status from ".$this->table()." where order_id={$order_id} and check_status < 4  ";
    	$res=$this->db()->getAll($sql);
    	//查询是否还有此订单的退款
    	if($res){ 
	    	foreach($res as $k=> $r){ 
	    		//不是主管审核驳回和无操作的的返回true
	    		if($r['check_status']!=0){
	    			return true;
	    		}else{
	    			$sql="select id from app_return_check where return_id={$r['return_id']} and leader_status=2";
	    			$res1=$this->db()->getAll($sql);
	    			//如果不是主管审核驳回，返回true
	    			if(!$res1){
	    			  return true;
	    			}
	    		}
	
	    	}
    	
    	} 
    	
    	return false;
    }
    
    //检查是否还有退款流程的货
    function  getReturnGoodsByWhere1($order_id,$id){
        $sql="select * from ".$this->table()."   where  order_id=$order_id and return_id <> {$id} and check_status < 4 ";
    	$res=$this->db()->getAll($sql);
    	//查询是否还有此订单的退款
    	if($res){ 
	    	foreach($res as $k=> $r){ 
	    		//不是主管审核驳回和无操作的的返回true
	    		if($r['check_status']!=0){
	    			return true;
	    		}else{
	    			$sql="select id from app_return_check where return_id={$r['return_id']} and leader_status=2";
	    			$res1=$this->db()->getAll($sql);
	    			//如果不是主管审核驳回，返回true
	    			if(!$res1){
	    			  return true;
	    			}
	    		}
	
	    	}
    	
    	} 
    	
    	return false;
    }
    //获取订单支付信息
    function GetOrderInfoBySn($order_sn){
        $sql = "SELECT `oi`.*, `oa`.`order_amount`, `oa`.`money_paid`, `oa`.`money_unpaid`, `oa`.`goods_return_price`, `oa`.`real_return_price`, `oa`.`shipping_fee`, `oa`.`goods_amount`,`oi`.`department_id`,`oi`.`apply_close`,`oi`.`order_status`,`oi`.`order_pay_status`,(select GROUP_CONCAT(DISTINCT rod.out_order_sn) from rel_out_order as rod where rod.order_id=`oi`.id group by rod.order_id) as out_order_sn  FROM `base_order_info` as `oi` LEFT JOIN `app_order_account` as `oa` ON `oi`.`id`=`oa`.`order_id` WHERE `oi`.`order_sn`='{$order_sn}'";
        return $this->db()->getRow($sql);
    }
    /**
    * 获取订单的单据维修发货状态
    * @param unknown $order_sn
    * return true 维修发货中，false 维修结束 
    */
    function checkOrderWeixiuStatus($order_sn,$order_goods_id){
        $sql = "select weixiu_status from warehouse_shipping.warehouse_goods where order_goods_id={$order_goods_id}";
        $ret = $this->db()->getAll($sql);
        if(!empty($ret)){
            foreach ($ret as $vo){
                //非维修完成和维修取消
                if(!empty($vo['weixiu_status']) && !in_array($vo['weixiu_status'],array(1,4))){
                    return true;
                }
            }
        }else{
            //查询是否有 未审核 的维修发货单
            $sql = "select count(1) as c from warehouse_shipping.warehouse_bill where order_sn='{$order_sn}' and bill_type='R' and bill_status=1";
            $ret1 = $this->db()->getOne($sql);
            if($ret1){
                return true;
            }else{
                //查询是否有未取消（ 未审核+已审核）的维修退货或维修调拨单
                $sql = "select count(1) as c from warehouse_shipping.warehouse_bill where order_sn='{$order_sn}' and bill_type in('O','WF') and bill_status=1";
                $ret2= $this->db()->getOne($sql);
                if($ret2) {
                    return true;
                }
            } 
        }       
        return false;        
    }
    /**
     * 
     * 获取退款商品总金额
     * @param int $order_goods_id
     * @param number $return_by
     */
    function getReturnGoodsPrice($order_sn,$order_goods_id=0,$return_by=0){
        $sql = "select sum(real_return_amount) from app_return_goods a left join app_return_check b on a.return_id=b.return_id where a.order_sn='{$order_sn}' and (b.leader_status<>2 or b.leader_status is null)";
        if($order_goods_id>0){
            $sql .=" AND a.order_goods_id={$order_goods_id}";
        }
        if($return_by>0){
            $sql.=" AND a.return_by={$return_by}";
        }
        return (float) $this->db()->getOne($sql);
    }
    
    public function checkReturnGoods($order_sn,$order_goods_id,$return_by=1){
        $sql = "select count(*) from app_return_goods a left join app_return_check b on a.return_id=b.return_id where a.order_sn='{$order_sn}' and a.order_goods_id={$order_goods_id} and (b.leader_status<>2 or b.leader_status is null)";
        if($return_by>0){
            $sql.=" AND a.return_by={$return_by}";
        }
        return $this->db()->getOne($sql);
    }

    public function getReturnAccountUncheck($order_sn,$order_goods_id=0){
        $sql = "select sum(apply_return_amount) as apply_return_amount,sum(real_return_amount) as real_return_amount  from app_return_goods a left join app_return_check b on a.return_id=b.return_id where a.order_sn='{$order_sn}' and (b.leader_status<>2 or b.leader_status is null) and a.check_status<4";
        if(!empty($order_goods_id) && $order_goods_id>0)
            $sql .= " and a.order_goods_id='".$order_goods_id."'";             
        return $this->db()->getRow($sql);
    }

    public function getReturnAccountCheck($order_sn,$order_goods_id=0){
        $sql = "select sum(apply_return_amount) as apply_return_amount,sum(real_return_amount) as real_return_amount  from app_return_goods a left join app_return_check b on a.return_id=b.return_id where a.order_sn='{$order_sn}' and a.check_status>=4";
        if(!empty($order_goods_id) && $order_goods_id>0)
            $sql .= " and a.order_goods_id='".$order_goods_id."'";  
        return $this->db()->getRow($sql);
    }

    public function getReturnGoodsAccountUncheck($order_sn){
        $sql = "select sum(if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price)) as apply_return_goods_amount  from app_order_details d,app_return_goods a left join app_return_check b on a.return_id=b.return_id where d.id=a.order_goods_id and a.order_sn='{$order_sn}' and a.return_by=1 and (b.leader_status<>2 or b.leader_status is null) and a.check_status<4";

        return $this->db()->getRow($sql);
    }

    public function isPayZero($order_id){
        $sql="select * from app_order_action where order_id='".$order_id."' and remark ='点款成功:0元。'";
        return empty($this->db()->getRow($sql)) ? false : true;  
    }

    public function getGoodsPaid($order_sn){
        $sql="select o.order_sn,d.id,d.is_return,0 as goods_paid,if(d.favorable_status=3,d.goods_price-d.favorable_price,d.goods_price) as goods_price,a.order_amount,a.money_paid,a.real_return_price,(select sum(r.apply_return_amount) from app_return_goods r where r.order_goods_id=d.id and r.check_status>=4) as return_amount from app_order_details d,base_order_info o,app_order_account a where d.order_id=o.id and o.id=a.order_id and o.order_sn='".$order_sn."'";
        $data=$this->db()->getAll($sql);
        if(empty($data)){
            return null;
        }
        $return_paid1=0; 
        $goods_total1=0;
        $moreAmount=false; 
        $money_paid_total = 0;  //订单剩余已付金额    
        foreach ($data as $key => $v){
            $money_paid_total = $v['money_paid']-$v['real_return_price'];
            if($v['is_return']==1){
                $return_paid1+=$v['return_amount'];
            }else{
                $goods_total1+=$v['goods_price'];
            }
            if(($v['money_paid']-$v['real_return_price'])*1000>$v['order_amount']*1000){
                $moreAmount=true; 
            }
        }

        if($moreAmount==false){
                $return_paid2=0;
                $goods_total2=0; 
                foreach ($data as $key => &$v) {
                    if($v['is_return']==0){
                        if((round($v['goods_price']/$goods_total1*($v['money_paid']-$return_paid1),2)-$v['return_amount'])*1000 > $v['goods_price']*1000) {
                            $v['goods_paid']=$v['goods_price'];
                            $return_paid2+=$v['goods_price'];
                        }else{
                            $goods_total2+=$v['goods_price'];
                        }
                    }    
                }        
                foreach ($data as $key => &$v) {
                    if($v['is_return']==0 && $v['goods_paid']==0){                
                        $v['goods_paid']=round($v['goods_price']/$goods_total2*($v['money_paid']-$return_paid1-$return_paid2),2)-$v['return_amount'];
                        if($v['goods_paid']<>0){
                            if(abs($v['goods_paid']-$v['goods_price'])<1)
                                 $v['goods_paid']=$v['goods_price'];
                        }
                    }    
                }
        }else{                
                foreach ($data as $key => &$v) {
                    if($v['is_return']==0){
                        $v['goods_paid']=round($v['goods_price']/$goods_total1*($v['money_paid']-$return_paid1),2)-$v['return_amount'];
                        if($v['goods_paid']<>0){
                            if(abs($v['goods_paid']-$v['goods_price'])<1)
                                 $v['goods_paid']=$v['goods_price']; 
                        }                              
                    }    
                } 

        }
        $res=array(); 
        $goods_num = count($data);
        $goods_paid_toal = 0;//除最后一件货品的所有【剩余已付金额】
        foreach ($data as $key => &$v)  {            
            if($key+1==$goods_num && $money_paid_total<>$goods_paid_toal+$v['goods_paid']){
                $v['goods_paid'] = round($money_paid_total-$goods_paid_toal,2);
            }
            $goods_paid_toal += $v['goods_paid'];
            $res[$v['id']]=$v;
        }
        return $res; 
    }
    
}

?>