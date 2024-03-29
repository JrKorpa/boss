<?php
/**
 *  -------------------------------------------------
 *   @file		: UpdateOrderDingGouController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *
 *  -------------------------------------------------
 */
class UpdateOrderDingGouController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }           
		$paylist=$this->GetPaymentInfo();
		$this->render('update_order_dinggou_info.html',array('pay_type'=>$paylist));
	}
	
	/**
	 *	update_dinggou，修改订购类型
	 */
	public function update_dinggou ($params)
	{
        $res = array('error'=>1,'msg'=>'');
		$order_sn = _Post::getString('order_sn');
		$dinggou = _Post::getInt('dinggou');
        if(empty($order_sn)){
            $res['msg'] ="订单号为空，请重新输入";
            Util::jsonExit($res);
        }
        if(empty($dinggou)){
            $res['msg'] ="请选择订购类型！";
            Util::jsonExit($res);
        }
        $orderModel= new BaseOrderInfoModel(27);
        $order_sn_list = '';
        $order_sn_str = '';
        $order_sn_arr = array();
        if($order_sn){
            //若 订单号中间存在空格 汉字逗号 替换为英文模式逗号
            $order_sn = str_replace(' ',',',$order_sn);
            $order_sn = str_replace('，',',',$order_sn);
            $order_sn = str_replace(array("\r\n", "\r", "\n"),',',$order_sn);
            $tmp = explode(",", $order_sn);
            foreach($tmp as $k => $val){
                if($val == ''){
                    continue;
                }
                $order_info_ct = $orderModel->getOrderInfoBySn($val);
                if(!$order_info_ct){
                    $res['msg'] = "没有 “".$val."” 订单号，请重新输入。";
                    Util::jsonExit($res);
                }
                if($order_info_ct['order_pay_status'] != 1 && $order_info_ct['order_pay_status'] != 4){
                    $order_sn_str .= "'".$val."',";
                }
                if($order_info_ct['order_pay_type'] == $dinggou){
                    continue;
                }
                $order_sn_list .= "'$val',";
                $order_sn_arr[$k] = $val;
            }
            $order_sn_list = rtrim($order_sn_list,',');
            if($order_sn_str != ''){
                $order_sn_str = rtrim($order_sn_str,',');
                $res['msg'] ="订单{$order_sn_str}已付款，只有未付款订单才能更改。";
                Util::jsonExit($res);
            }
        }
        $where = array();
        $where['order_sn'] = $order_sn_list;
        $where['dinggou'] = $dinggou;
        $ret = $orderModel->updateOrderPayTypeBySn($where);
        $paylist=$this->GetPaymentInfo();
        foreach ($order_sn_arr as $val) {
            $order_info = $orderModel->getOrderInfoBySn($val);
            $logInfo = array(
                'order_id'=>$order_info['id'],
                'order_status'=>$order_info['order_status'],
                'shipping_status'=>$order_info['send_good_status'],
                'pay_status'=>$order_info['order_pay_status'],
                'create_user'=>$_SESSION['userName'],
                'create_time'=>date("Y-m-d H:i:s"),
                'remark'=>'订购类型['.$paylist[$order_info_ct['order_pay_type']].']改为['.$paylist[$dinggou]."]",
            );
            //写入订单日志
            $orderModel->addOrderAction($logInfo);
        }
        if($ret){
            $res['msg'] ='修改成功！';
            Util::jsonExit($res);
        }
	}
}

?>