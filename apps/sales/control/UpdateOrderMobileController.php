<?php
/**
 *  -------------------------------------------------
 *   @file		: UpdateQudaoBumenController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *
 *  -------------------------------------------------
 */
class UpdateOrderMobileController extends CommonController
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
		$this->render('update_order_moblie.html');
	}
	
	/**
	 *	update_mobile
	 */
	public function update_mobile ($params)
	{
        $res = array('error'=>1,'msg'=>'');
		$order_sn = _Post::getString('order_sn');
		$mobile = _Post::getInt('mobile');
        
        if(empty($order_sn)){
			$res['msg'] ="订单号为空，请重新输入";
			Util::jsonExit($res);
		}
		if(empty($mobile)){
			$res['msg'] ="请输入手机号码！";
			Util::jsonExit($res);
		}
        if(!preg_match('/^1\d{10}$/',$mobile)){
            $res['msg'] ="手机号码不合法！";
            Util::jsonExit($res);
        }
        $orderModel= new BaseOrderInfoModel(27);
        $orderAdModel = new AppOrderAddressModel(28);
        $order_sn_list = '';
        $order_id_list = '';
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
                if($order_info_ct['mobile'] == $mobile){
                    continue;
                }
                $order_sn_list .= "'$val',";
                $order_id_list .= "'{$order_info_ct['id']}',";
                $order_sn_arr[$k] = $val;
            }
            $order_sn_list = rtrim($order_sn_list,',');
            $order_id_list = rtrim($order_id_list,',');
        }
        $where = array();
        $where['order_id'] = $order_id_list;
        $where['order_sn'] = $order_sn_list;
        $where['mobile'] = $mobile;
        $ret = $orderModel->updateOrderMobileBySn($where);
        $orderAdModel->updateOrderAddressInfoBySn($where);
        if(!empty($order_sn_arr)){
            foreach ($order_sn_arr as $val) {
                $order_info = $orderModel->getOrderInfoBySn($val);
                $logInfo = array(
                    'order_id'=>$order_info['id'],
                    'order_status'=>$order_info['order_status'],
                    'shipping_status'=>$order_info['send_good_status'],
                    'pay_status'=>$order_info['order_pay_status'],
                    'create_user'=>$_SESSION['userName'],
                    'create_time'=>date("Y-m-d H:i:s"),
                    'remark'=>'手机号 '.$order_info_ct['mobile'].'修改为'.$mobile,
                );
                //写入订单日志
                $orderModel->addOrderAction($logInfo);
            }
        }
        if($ret){
            $res['msg'] ='修改成功！';
            Util::jsonExit($res);
        }
	}
}

?>