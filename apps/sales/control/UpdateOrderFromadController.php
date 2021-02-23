<?php

/**
 *  -------------------------------------------------
 *   @file		: BaseOrderInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com
 *   @date		: 2015-01-28 12:36:56
 *   @update	:
 *  -------------------------------------------------
 */
class UpdateOrderFromadController extends CommonController {
    /**
     * 	index
     */
    public function index($params) {
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }	    	
		$CustomerSourcesModel = new CustomerSourcesModel(1);
		$source_name = $CustomerSourcesModel->getCustomerSourcesList("`id`,`source_name`", '');
		$this->render('update_order_from_ad.html',array('source_name' => $source_name));
    }
    /**
     * 	update，更新信息
     */
    public function update($params) {
        $res = array('error'=>1,'msg'=>'');
		$order_sn = _Post::getString('order_sn');
		$customer_source_id = _Post::getInt('customer_source_id');
        if(empty($order_sn)){
			$res['msg'] ="订单号为空，请重新输入";
			Util::jsonExit($res);
		}
		if(empty($customer_source_id)){
			$res['msg'] ="请选择修改渠道，请重新输入";
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
				if($order_info_ct['order_pay_status'] != 1){
					$order_sn_str .= "'".$val."',";
				}
				if($order_info_ct['customer_source_id'] == $customer_source_id){
					continue;
				}
				$order_sn_list .= "'$val',";
				$order_sn_arr[$k] = $val;
			}
			$order_sn_list = rtrim($order_sn_list,',');
			if($order_sn_str != ''){
                                //业务取消对订单的限制
				//$order_sn_str = rtrim($order_sn_str,',');
				//$res['msg'] ="订单{$order_sn_str}已付款，只有未付款订单才能更改。";
				//Util::jsonExit($res);
			}
		}
		$where = array();
		$source_name=array();
		$where['order_sn'] = $order_sn_list;
		$where['customer_source_id'] = $customer_source_id;
		$ret = $orderModel->updateOrderCustomerSourceBySn($where);
		$CustomerSourcesModel = new CustomerSourcesModel(1);
		$source_name = $CustomerSourcesModel->getCustomerSourcesList("`id`,`source_name`", '');
			foreach ($source_name as $val) {
				$source_name[$val['id']] = $val['source_name'];
				
			}
			
            foreach ($order_sn_arr as $val) {
                $order_info = $orderModel->getOrderInfoBySn($val);
                $logInfo = array(
                    'order_id'=>$order_info['id'],
                    'order_status'=>$order_info['order_status'],
                    'shipping_status'=>$order_info['send_good_status'],
                    'pay_status'=>$order_info['order_pay_status'],
                    'create_user'=>$_SESSION['userName'],
                    'create_time'=>date("Y-m-d H:i:s"),
                    'remark'=>'客户来源 '.$source_name[$order_info_ct['customer_source_id']].'修改为'.$source_name[$customer_source_id],
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
