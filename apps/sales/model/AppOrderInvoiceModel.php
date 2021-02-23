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
            'title_type'=>'抬头类型 1个人2公司',
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
		$sql = "SELECT a.*,b.order_sn FROM `".$this->table()."` a left join base_order_info b on a.order_id=b.id where 1=1";
		$str = '';
        if(!empty($where['order_id'])){
            $str .= " AND a.`order_id`=".$where['order_id'];
        }
        if(!empty($where['order_sn'])){
            $str .= " AND b.`order_sn`='{$where['order_sn']}'";
        }
        if(!empty($where['invoice_status'])){
            $str .= " AND b.`invoice_status`=".$where['invoice_status'];
        }

		$sql = $sql.$str." ORDER BY a.`id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
    public function setInvoiceNum($order_id,$invoice_num){
        $sql = "UPDATE `app_order_invoice` SET invoice_num=$invoice_num WHERE is_invoice=1 AND  order_id=$order_id";
        return $this->db()->query($sql);

    }

    public function updateIprice($price,$order_id){
        if(empty($order_id)){
            return false;
        }
        $sql = "update `app_order_invoice` set `invoice_amount`='$price' WHERE order_id=$order_id";
        return $this->db()->query($sql);
    }
    /**
     * 检查是否添加过 发票信息，1个订单只能有一个发票记录
     * @param unknown $order_id
     */
    public function checkOrderHasInvoivce($order_id){
       $sql = "select count(*) from app_order_invoice where order_id={$order_id}";
       return $this->db()->getOne($sql);
    }
    
    public function getMemberEmailByOrderId($order_id){
        $sql = "select member_email from front.base_member_info a inner join app_order.base_order_info b on a.member_phone = b.mobile where b.id={$order_id}";
        return $this->db()->getOne($sql);
    }


}

?>