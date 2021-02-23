<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderInvoiceModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 14:45:45
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderInvoiceModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_order_invoice';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"",
            "order_id"=>"订单id",
            "is_invoice"=>"是否需要发票 1:需要 2：不需要",
            'invoice_status'=>'发票状态:1未开发票2已开发票3发票作废',
            "invoice_title"=>"发票抬头",
            "invoice_content"=>"内容",
            "invoice_amount"=>"发票金额",
            "invoice_address"=>"发票邮寄地址",
            "invoice_num"=>"发票号",
            "create_time"=>"创建时间",
            "open_sn"=>"外部发票流水号",
            "invoice_type"=>"发票类型 1普通发票 2电子发票",
            "taxpayer_sn"=>"纳税人识别号",
        );
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppOrderInvoiceController/search
	 */
	function pageList($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT a.*,b.order_sn,b.order_status,b.buchan_status,b.order_pay_status,b.delivery_status,b.send_good_status FROM `".$this->table()."` a left join base_order_info b on a.order_id=b.id where 1=1 ";
		$str = '';
        if(!empty($where['order_id'])){
            $str .= " AND a.`order_id`=".$where['order_id'];
        }        
        if(SYS_SCOPE == 'zhanting'){
            $str .= " AND b.`hidden`<>1";
        }
        if(!empty($where['order_sn'])){
            $str .= " AND b.`order_sn`='{$where['order_sn']}'";
        }
        //是否开发票1,0
        if(isset($where['is_invoice']) && $where['is_invoice']!=''){
            $str .= " AND a.`is_invoice`=".$where['is_invoice'];
        }
        //发票状态
        if(!empty($where['invoice_status'])){
            $str .= " AND a.`invoice_status`=".$where['invoice_status'];
        }
        //发票类型 1普通发鸟  2电子发票
        if(!empty($where['invoice_type'])){
            $str .= " AND a.`invoice_type`=".$where['invoice_type'];
        }
        //发票号码
        if(!empty($where['invoice_num'])){
            $str .= " AND a.`invoice_num`='".$where['invoice_num']."'";
        }
        //发票抬头
        if(!empty($where['invoice_title'])){
            if(in_array($where['invoice_title'],array('个人','公司'))){
               $str .= " AND a.`invoice_title` ='".$where['invoice_title']."'";
            }else{
                $str .= " AND a.`invoice_title` like '%".$where['invoice_title']."%'";
            }
        }
        if(!empty($where['invoice_content'])){
            $str .= " AND a.`invoice_content` like '%".$where['invoice_content']."%'";
        }
        //创建者
        if(!empty($where['create_user'])){
            $str .= " AND a.`create_user` like '%".$where['create_user']."%'";
        }
        //发票金额范围搜索 最小值
        if(!empty($where['amount_min'])){
            $str .= " AND a.`invoice_amount` >=".$where['amount_min'];
        }
        //发票金额范围搜索 最大值
	    if(!empty($where['amount_max'])){
            $str .= " AND a.`invoice_amount` <=".$where['amount_max'];
        }
        if(!empty($where['create_time_start'])){
            $str .= " AND a.`create_time` >='".$where['create_time_start']."'";
        }
        if(!empty($where['create_time_end'])){
            $str .= " AND a.`create_time` <='".$where['create_time_end']." 23:59:59'";
        }
        
		$sql = $sql.$str." ORDER BY a.`id` DESC";
		//echo $sql;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	//根据订单编号查询 历史发票记录
	public function getOrderInvoice($order_sn){
	    $sql = "SELECT a.order_sn,b.order_amount,c.* FROM base_order_info a left join app_order_account b on a.id=b.order_id left join app_order_invoice c on a.id=c.order_id where a.order_sn='{$order_sn}'";
	    return $this->db()->getAll($sql);	     
	}

    public function setInvoiceNum($data){
        $result = array('success'=>0,'error'=>'');
        $require = array('id','order_id','invoice_num','invoice_amount','open_sn','order_sn');
        foreach ($require as $vo){
            if(!isset($data[$vo])){
                $result['error'] = "参数{$vo}不存在";
                return $result;
            }            
        }
        $id = $data['id'];
        $order_id = $data['order_id'];
        $invoice_num = $data['invoice_num'];
        $open_sn = $data['open_sn'];
        $order_sn = $data['order_sn'];
        $user = $_SESSION['userName'];
        $time = date('Y-m-d H:i:s');
        try{
            $sql = "UPDATE app_order.`app_order_invoice` SET invoice_num='{$invoice_num}',is_invoice=1,invoice_type=2,invoice_status=2,use_user='{$user}',use_time='{$time}',open_sn='{$open_sn}' WHERE id={$id}";
            $this->db()->query($sql);
            $sql = "UPDATE app_order.`app_order_invoice` SET invoice_status=3 WHERE id<>{$id} and order_id={$order_id}";
            $this->db()->query($sql); 
            $sql = "select * from app_order.app_order_invoice where id={$id}";            
            $row = $this->db()->getRow($sql);
            if(!empty($row)){
                $sql = "update finance.base_invoice_info set status=3 where order_sn='{$order_sn}'";
                $this->db()->query($sql);
                $newdo = array(
                    'invoice_num'=>$row['invoice_num'],
                    'price'=>$row['invoice_amount'],
                    'title'=>$row['invoice_title'],
                    'content'=>$row['invoice_content'],
                    'status'=>2,
                    'create_user'=>$row['create_user'],
                    'create_time'=>$row['create_time'],
                    'use_user'=>$row['use_user'],
                    'use_time'=>$row['use_time'],
                    'order_sn'=>$order_sn,
                    'type'=>1                     
                );
                $sql = $this->insertSql($newdo,'finance.base_invoice_info');
                $this->db()->query($sql);
            }  
	        //订单日志 
    	    $sql="select id,order_status,send_good_status,order_pay_status from app_order.base_order_info where order_sn='{$order_sn}'";
    	    $order = $this->db()->getRow($sql);	
    	    $logRemark = "批量导入发票：更新发票状态为已开发票，发票号为：{$invoice_num}"; 
    	    $sql="insert into app_order.app_order_action(`order_id`,`order_status`,`shipping_status`,`pay_status`,`create_user`,`create_time`,`remark`) values ('{$order['id']}','{$order['order_status']}','{$order['send_good_status']}','{$order['order_pay_status']}','{$user}','{$time}','{$logRemark}')";
    	    $this->db()->query($sql);     
        }catch (Exception $e){
            $result['error'] = $sql.$e->getMessage();
            return $result;
        }
        $result['success'] = 1;
        return $result;
    }

    public function updateIprice($price,$order_id){
        if(empty($order_id)){
            return false;
        }
        $sql = "update `app_order_invoice` set `invoice_amount`='$price' WHERE order_id=$order_id";
        return $this->db()->query($sql);
    }



}

?>