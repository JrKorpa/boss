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
		"check_status"=>"0未操作1主管审核通过2库管审核通过3事业部通过4现场财务通过5财务通过");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppReturnGoodsController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true,$recordCount=false)
    {
        //不要用*,修改为具体字段
        $sql = "SELECT `a`.*,`b`.`goods_status`,`b`.`deparment_finance_status`,`b`.`finance_status`,`b`.`leader_status`,`b`.`cto_status`,b.goods_comfirm_id,b.goods_time FROM `".$this->table()."` as a left join `app_return_check` as b on `a`.`return_id` = `b`.`return_id`";
        $str = '';
        if(!empty($where['return_id']))
        {
            $str .= "a.`return_id` = ".intval($where['return_id'])." AND ";
        }
        if(!empty($where['order_sn']))
        {
            $str .= "a.`order_sn`='".$where['order_sn']."' AND ";
        }
        if(!empty($where['return_type']))
        {
            $str .= "a.`return_type`='".$where['return_type']."' AND ";
        }
        //库管  全部|审核通过|审核驳回 条件
        if(!empty($where['goods_status'])){
            $str .= "`b`.`goods_status`={$where['goods_status']} AND ";
        }
        //库管审核时间范围查询
        if(!empty($where['goods_start_time'])){
        
            $str .= "b.`goods_time` >= '".$where['goods_start_time']." 00:00:00' AND ";
        }
        if(!empty($where['goods_end_time'])){
        
            $str .= "b.`goods_time` <= '".$where['goods_end_time']." 23:59:59' AND ";
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
        
        if($str)
        {
            $str = rtrim($str,"AND ");//这个空格很重要
            $sql .=" WHERE ".$str;
        }
        $sql .= " ORDER BY a.`return_id` DESC";
        //echo $sql;
        $data = $this->db()->getPageListForExport($sql,array(),$page, $pageSize,$useCache,$recordCount);
        if($data['data']){
            $userModel = new UserModel(1);
            $departmentModel = new SalesChannelsModel(1);
            foreach ($data['data'] as &$val){
                if($val['apply_user_id']>0){
                    $val['apply_user_name'] = $userModel->getAccount($val['apply_user_id']);
                }else{
                    $val['apply_user_name'] = "";
                }
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
    
}

?>