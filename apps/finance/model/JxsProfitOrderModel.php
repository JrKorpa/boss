<?php
/**
 *  -------------------------------------------------
 *   @file		: JxsProfitOrderModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-07-16 23:52:49
 *   @update	:
 *  -------------------------------------------------
 */
class JxsProfitOrderModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'jxs_order';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"jxs_id"=>"经销商",
"batch_id"=>"批次",
"order_id"=>"订单号",
"order_sn"=>"订单编号",
"department_id"=>"订单部门",
"create_time"=>"制单时间",
"send_goods_time"=>"发货时间",
"item_count"=>"订单商品数量",
"order_amount"=>"订单金额",
"order_status"=>"订单审核状态1无效2已审核3取消4关闭",
"address"=>"收货地址",
"country_id"=>"国家id",
"province_id"=>"省份id",
"city_id"=>"城市id",
"region_id"=>"区域id",
"calc_profit"=>"公式计算的让利额",
"real_profit"=>"实际让利额",
"calc_status"=>"订单结算状态,0未结算，1结算",
"calc_date"=>"结算时间",
"profit_id" => "结算单id"
        );
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url JxsOrderController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."` ";
		$str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
        if(!empty($where['jxs_id']))
        {
            $str .= "`jxs_id`='".$where['jxs_id']."' AND ";
        }
        else 
        {
            return array('data'=> array());
        }
        if(isset($where['calc_status']))
        {
            $str .= "`calc_status`='".$where['calc_status']."' AND ";
        }
        if (isset($where['ex_calced']) && !empty($where['ex_calced'])) {
            $str .= "`calc_status`!=1 AND ";
        }
        if(!empty($where['start_time'])) {
            $str.="`send_goods_time` >= '".$where['start_time']." 00:00:00' AND ";
        }
        if(!empty($where['end_time'])) {
            $str.="`send_goods_time` <= '".$where['end_time']." 23:59:59' AND ";
        }
        if (!empty($where['profit_id'])) {
            if (isset($where['calc_status']) && $where['calc_status'] == 1) {
                $str.="`profit_id` = ".$where['profit_id']." AND ";
            }
        }
        if (!empty($where['department_id'])) {
            $str .= "`department_id` = {$where['department_id']} AND ";
        }
        if (!empty($where['start_money'])) {
            $str .= "`real_profit` >= {$where['start_money']} AND ";
        }
        if (!empty($where['end_money'])) {
            $str .= "`real_profit` <= {$where['end_money']} AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `batch_id` DESC, `send_goods_time` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	
	function cancelCalculatedOrder(Array $profit_ids) {
	    $profit_ids_str = implode(',', $profit_ids);
	    $sql = "update `".$this->table()."` set `calc_status`=2,`calc_date`=NULL, `profit_id`=NULL where `profit_id` in ({$profit_ids_str}); ";
	    return $this->db()->query($sql);
	}
	
	function sumProfit(Array $ids) {
	    $ids_str = implode(',', $ids);
	    $sql = "select sum(`real_profit`) as total from ".$this->table()." where id in ({$ids_str}); ";
	    $row = $this->db()->getRow($sql);
	    return empty($row) ? 0 : $row['total'];
	}
	
	function getJxsId(Array $ids) {
	    $ids_str = implode(',', $ids);
	    $sql = "select DISTINCT jxs_id from `".$this->table()."` where `id` in ({$ids_str});";
	    $data = $this->db()->getAll($sql);
	    return $data;
	}

    function getOrderByWhere($where)
    {
        if(isset($where['_ids']) && is_array($where['_ids']) && !empty($where['_ids'])){
            $sql = "select o.id,o.jxs_id,o.order_id,o.order_sn,od.goods_id,od.calc_profit,o.calc_status from `".$this->table()."` o
                inner join jxs_order_detail od on o.order_id=od.order_id
            where o.`id` in (".implode(',',$where['_ids']).");";
            $data = $this->db()->getAll($sql);
            return $data;
        }else{
            return false;
        }
    }

    function getD($order_sn,$goods_id)
    {
        if(empty($order_sn) || empty($goods_id)){
            return false;
        }
        $sql = "SELECT 1
            FROM 
                warehouse_shipping.warehouse_bill wb
                inner join warehouse_shipping.warehouse_bill_goods wbg on wb.bill_no = wbg.bill_no
            WHERE 
                wb.bill_type = 'D' 
                AND wb.bill_status = 2 
                AND wb.order_sn = '$order_sn' 
                AND wbg.goods_id = '$goods_id'
        ";
        $data = $this->db()->getOne($sql);
        return $data;
    }

    function updateCalc($jsList, $profit_id)
    {
        if(empty($jsList)){
            return false;
        }
        foreach($jsList as $key =>$val){
            $id = $key;
            $calc = $val['calc_profit'];
            $date = $val['date'];
            $sql = "update `".$this->table()."` set `calc_status`=1,`calc_date`='$date', `profit_id`={$profit_id},real_profit = $calc where `id` = $id; ";
            if(!$this->db()->query($sql)){
                return false;
            }
        }
        return true;
    }
}
?>