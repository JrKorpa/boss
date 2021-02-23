<?php
/**
 *  -------------------------------------------------
 *   @file		: AppCouponTypeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-11 16:52:55
 *   @update	:
 *  -------------------------------------------------
 */
class AppCouponExchangeController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_coupon_exchange_search_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	exchange_verify 兑换
	 */
	public function exchange_verify ($params)
	{
		$res = array('error'=>1,'msg'=>'');
		$coupon_code = _Request::getString('coupon_code');
        $coupon_code = trim($coupon_code);
        if($coupon_code == ''){
            $res['msg'] ="请输入兑换码！";
            Util::jsonExit($res);
        }
        $model = new BaseCouponModel(18);
        $log_model = new AppCouponLogModel(18);
        $log_data = array();
        $log_data['exchange_coupon'] = $coupon_code;
        $log_data['exchange_status'] = 2;
        $log_data['exchange_name'] = $_SESSION['userName'];
        $log_data['exchange_time'] = date("Y-m-d H:i:s",time());
        $id = $model->checkCode($coupon_code);
        if($id == ''){
            $log_data['exchange_remark'] = '优惠码不存在';
            $log_model->saveData($log_data,array());
            $res['msg'] ="优惠码不存在或无法识别！";
            Util::jsonExit($res);
        }
        $check_status = $model->checkCodeStatus($id);
        if($check_status['coupon_status'] == 2){
            $log_data['exchange_remark'] = '优惠码已使用';
            $log_model->saveData($log_data,array());
            $res['msg'] ="此优惠码已使用，不可再用，请确认！";
            Util::jsonExit($res);
        }
        if($check_status['coupon_status'] == 3){
            $log_data['exchange_remark'] = '优惠码已作废';
            $log_model->saveData($log_data,array());
            $res['msg'] ="此优惠码已作废，不可再用，请确认！";
            Util::jsonExit($res);
        }
        $policy_id = $check_status['coupon_policy'];
        $app_model = new AppCouponPolicyModel(18);
        $app_policy = $app_model->getCouponPolicyRow($policy_id);
        //print_r($check_status);die;
        if($app_policy['policy_status'] == 6){
            $log_data['exchange_remark'] = '优惠码已过期';
            $log_model->saveData($log_data,array());
            $res['msg'] ="此优惠码已过期，不可再用，请确认！";
            Util::jsonExit($res);
        }
        $r = false;
        if($check_status['coupon_status'] == 1){
            $data = array();
            $data['id'] = $id;
            $data['use_time'] = date("Y-m-d H:i:s",time());
            $data['exchange_user'] = $_SESSION['userName'];
            //print_r($data);die;
            $r = $model->updateCouponStatus($data);
            //print_r($r);die;
        }
        if($r !== false){
            $log_data['exchange_remark'] = '';
            $log_data['exchange_status'] = 1;
            $res['msg'] ="此优惠码兑换成功，请在当天下单，过期作废！";
        }else{
            $log_data['exchange_remark'] = '系统错误';
            $res['msg'] ="兑换失败！";
        }
        $log_model->saveData($log_data,array());
        Util::jsonExit($res);
	}
}

?>