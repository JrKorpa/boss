<?php
/**
 * 新 ApiModel 基类
 *  -------------------------------------------------
 *   @file		: SelfModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class SelfModel
{
    /**
     * @var KDB
     */
    protected $db;
	function __construct ($strConn="")
	{
		$this->db = DB::cn($strConn);
	}
	public function db(){
	    return $this->db;
	}
	final public static function add_special_char($value)
	{
	    if ('*' == $value || false !== strpos($value, '(') || false !== strpos($value, '.') || false !== strpos($value, '`'))
	    {
	        //不处理包含* 或者 使用了sql方法。
	    }
	    else
	    {
	        $value = '`' . trim($value) . '`';
	    }
	    if (preg_match('/\b(select|insert|update|delete)\b/i', $value))
	    {
	        $value = preg_replace('/\b(select|insert|update|delete)\b/i', '', $value);
	    }
	    return $value;
	}
	/*
	 * updateSql,生成更新语句
	 */
	public function updateSql ($table,$do,$where)
	{
	    $field = '';
	    $fields = array();
	    foreach ($do as $key=>$val)
	    {
	        switch (substr($val, 0, 2))
	        {
	            case '+=':
	                $val = substr($val,2);
	                if (is_numeric($val)) {
	                    $fields[] = self::add_special_char($key) . '=' . self::add_special_char($key) . '+' . $val;
	                }
	                else
	                {
	                    continue;
	                }
	                break;
	            case '-=':
	                $val = substr($val, 2);
	                if (is_numeric($val))
	                {
	                    $fields[] = self::add_special_char($val) . '=' . self::add_special_char($key) . '-' . $val;
	                }
	                else
	                {
	                    continue;
	                }
	                break;
	            default:
	                if(is_numeric($val))
	                {
	                    $fields[] = self::add_special_char($key) . '=' . $val;
	                }
	                else
	                {
	                    $fields[] = self::add_special_char($key) . '="' . $val.'"';
	                }
	        }
	    }
	    $field = implode(',', $fields);
	    $sql = "UPDATE ".$table." SET ".$field;
	    $sql .= " WHERE {$where}";
	    return $sql;
	}
	public function insertSql ($do,$tableName = "")
	{	    
	    $fields = array_keys($do);
	    $valuedata = array_values($do);
	    array_walk($fields, array($this, 'add_special_char'));
	    foreach ($valuedata as $k=>$v){
	        $valuedata[$k] = $this->db()->db()->quote($v);
	    }
	    $field = implode('`,`', $fields);
	    $value = implode(",",$valuedata);
	    return "INSERT INTO ".$tableName." (`" . $field . "`) VALUES (".$value.")";
	}
	//单表查询
    public function select($field,$where,$type=1,$table) {
	    $sql = "SELECT {$field} FROM ".$table." where {$where}";
	    if($type==1){
	        return $this->db()->getAll($sql);
	    }elseif($type==2){
	        return $this->db()->getRow($sql);
	    }elseif($type==3){
	        return $this->db()->getOne($sql);
	    }
	}
	
	/**
	 * 更新订单总积分 = 明细商品积分之和。注：判断是否有转单积分，如果有需要重新计算积分
	 */
	public function update_order_point($order_id) {
	    $res = 0;
	    if(empty($order_id))
	    {
	        return true;
	    }
	    
	    //$zsql = "SELECT sum(deposit) as zhuandanCash FROM finance.app_order_pay_action AS p WHERE  p.`status` <> 4  AND p.is_type = 1  AND p.zhuandan_sn > 0 AND  p.order_id = '{$order_id}'";

        //添加配置 为了适配展厅和boss不同的环境
        $pay_type = '252';
        if ( SYS_SCOPE == 'zhanting' )
        {
            $pay_type = '321';
        }
        /**
         * 转单金额不送积分，分两种情况
         * 1、发货后转单，转单金额不送积分
         * 2、支付方式为跨渠道协作收款、经销商渠道协作收款的点进来的钱不送积分
         */
        $zsql = "SELECT sum(pa.deposit) as zhuandanCash FROM finance.app_order_pay_action pa  left join app_order.app_return_goods rg on pa.zhuandan_sn = rg.return_id  left join app_order.base_order_info boi on rg.order_sn = boi.order_sn  where pa.`status` <> 4  AND pa.is_type = 1 and pa.order_id = '{$order_id}'  and ( (pa.zhuandan_sn >0 and boi.send_good_status = 2)  or pa.pay_type = '{$pay_type}')";
        $orderZhuandanCash = $this->db()->getOne($zsql);
	    //查询订单
	    $osql = "select department_id, mobile,pay_date,is_zp from app_order.base_order_info where id = '{$order_id}'";
	    $order_info = $this->db()->getRow($osql);
	    if(empty($order_info)) {
	        return true;
	    }
	    try {
	        $pointRules = Util::point_api_get_config($order_info['department_id'], $order_info['mobile'], strtotime($order_info['pay_date']));
	    }
	    catch (Exception $e) {
	        //无法确认积分规则，则暂时不更新，由最终赠送时再在处理
	        return true;
	    }
        /**
         * 赠品单不送积分
         */
        if($order_info['is_zp'] == 1){
            $this->giftsDealWith($order_id);
            $orderZhuandanCash = 0;
        } else {
            if($orderZhuandanCash && $orderZhuandanCash > 0) {
                $dsql = "select * from app_order.app_order_details where order_id = '{$order_id}'";
                $details = $this->db()->getAll($dsql);
                if(empty($details)) {
                    return true;
                }

                $totalPayCash = 0;
                // 计算明细支付比例
                foreach ($details as $item) {
                    $totalPayCash += ($item['goods_price'] - $item['favorable_price']);
                }
                //更新明细积分
                foreach ($details as $item) {
                    $goodsPayPrice = $item['goods_price'] - $item['favorable_price'];
                    $zhuandanCash = round($orderZhuandanCash * ($goodsPayPrice / $totalPayCash), 2);

                    if (!$pointRules['is_enable_point']) {
                        $sql = "update app_order.app_order_details set zhuandan_cash = '{$zhuandanCash}' where id='{$item['id']}'";
                        //echo $sql;
                        $this->db()->query($sql);
                        return true;
                    }
                    else {
                        $this->update_orderdetail_point($item, $pointRules, $zhuandanCash);
                    }
                }
            } else {
                $orderZhuandanCash = 0;
            }
        }
	    $sql = "update app_order.base_order_info o  set o.discount_point=(select sum(d.discount_point) from app_order.app_order_details d where d.order_id=o.id),
	    o.reward_point=(select sum(d.reward_point) from app_order.app_order_details d where d.order_id=o.id),
	    o.jifenma_point=(select sum(d.jifenma_point) from app_order.app_order_details d where d.order_id=o.id),
	    o.zhuandan_cash='{$orderZhuandanCash}'
	    where o.id= '{$order_id}'";
	    $res = $this->db()->query($sql);
	    if(!$res) {
	        return false;
	    }
	    
	    $now = date('Y-m-d H:i:s');
	    $sql = "insert into app_order.app_order_action select 0,o.id,o.order_status,o.send_good_status,o.order_pay_status,'{$_SESSION['userName']}','{$now}',(select concat('订单发货,赠送',round(sum(ifnull(d.discount_point,0)+ifnull(d.reward_point,0)+ifnull(d.jifenma_point,0)),0),'总积分') from app_order.app_order_details d where d.order_id=o.id) from app_order.base_order_info o where o.id='{$order_id}'";
	    $res = $this->db()->query($sql);
	    if(!$res) {
	        return false;
	    } 
		return true;
	}

    /**
     * @param $order_id
     * 点款成赠品的订单处理 赠品订单不送积分
     */
	public function giftsDealWith($order_id){
        $sql = "update app_order.app_order_details set discount_point='0',reward_point='0', zhuandan_cash = '0' where order_id='{$order_id}'";
        $this->db()->query($sql);
    }

	/**
	 * 计算并更新订单货号积分
	 * @param integer|array $detailIdOrData
	 * @param array $pontRules
	 * @param float $zhuandanCash
	 * @return boolean
	 */
	public function update_orderdetail_point($detailIdOrData, array $pointRules = null, $zhuandanCash = 0.00){
	    $discount_point =0; //折扣积分
	    $reward_point =0;  //额外奖励积分
	    
	    if(empty($detailIdOrData)){
	        return false;
	    }
	    
	    if(is_numeric($detailIdOrData)) {
	        $sql = "select o.order_sn,o.department_id, o.mobile, d.* from app_order_details d,base_order_info o where o.id=d.order_id and d.id='{$detailIdOrData}'";
	        $order_detail_info = $this->db()->getRow($sql);
	        if(empty($order_detail_info)){
	            return false;
	        }
	    }
	    else {
	        $order_detail_info = $detailIdOrData;
		}
		
		/**
		 * 黄金产品（材质是足金，产品线为普通黄金，定价黄金）没有积分
		 */
		$caizhi = $order_detail_info['caizhi'];
		$product_type = $order_detail_info['product_type'];
		if($product_type == 7 || $product_type == 13 || strpos($caizhi, '足金') !== false) {
			return false;
		}
	    /**
		 * 黄金产品（材质是足金，产品线为普通黄金，定价黄金）没有积分
		 */
	    
	    if(empty($pointRules)) {
	        try {
	            $pointRules = Util::point_api_get_config($order_detail_info['department_id'], $order_detail_info['mobile'], strtotime($order_detail_info['pay_date']));
	            if (!$pointRules['is_enable_point']) {
	                return false;
	            }
	        }
	        catch (Exception $e) {
	            return false;
	        }
		}
		
	    $goodsPrice=  $order_detail_info['goods_price'];
        $carat =  (float)$order_detail_info['cart'];
	    $styleSn=  $order_detail_info['goods_sn'];
	    $certType =  $order_detail_info['cert'];
	    $favorable_status = $order_detail_info['favorable_status'];
	    $favorablePrice = $favorable_status == 3 ? (float)$order_detail_info['favorable_price'] : 0;
	    $daijinquanPrice= 0; //代金券的金额已经自动累加到favorable_price中，故不用再计算 (float)$order_detail_info['daijinquan_price'];
	    $isStock = $order_detail_info['is_stock_goods'];
	    $goodsType = $order_detail_info['goods_type'];
	    $xiangqianType = $order_detail_info['xiangqian'];
	    $jietuoType = $order_detail_info['tuo_type'];
	    $xiangkou = $order_detail_info['xiangkou'];
	    list($discount_point, $reward_point,$activityRate,$activityName,$rewardRateId) = Util::point_api_calculate_order_detail_point(
	            $pointRules, $goodsPrice, $favorablePrice, $daijinquanPrice, $zhuandanCash,
	            $certType, $carat, $styleSn, $goodsType, $isStock, $xiangqianType, $jietuoType, $xiangkou);
	    if($discount_point <= 0 || !is_numeric($discount_point)) {
	        $discount_point = 0;
	    }
	    if($reward_point <= 0 || !is_numeric($reward_point)) {
	        $reward_point= 0;
	    }
	    $sql = "update app_order.app_order_details set discount_point='{$discount_point}',reward_point='{$reward_point}', zhuandan_cash = '{$zhuandanCash}' where id='{$order_detail_info['id']}'";
	    //echo $sql;
        $this->db()->query($sql);
        if(!empty($rewardRateId))
        {
            $now = date('Y-m-d H:i:s');
            $sql = "insert into app_order.app_order_action select 0,o.id,o.order_status,o.send_good_status,o.order_pay_status,'{$_SESSION['userName']}','{$now}','应用上积分规则{$rewardRateId}' from app_order.base_order_info o where o.id='{$order_detail_info['order_id']}'";
            $res = $this->db()->query($sql);
        }
	}
}

?>