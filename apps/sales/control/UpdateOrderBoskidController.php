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
class UpdateOrderBoskidController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('update_order_boskid.html');
	}
	
	/**
	 *	update_mobile
	 */
	public function update_bosksn ($params) {
		$order_sn = _Post::getString('order_sn');
		$bosksn = _Post::getInt('bosksn');
        $res = array('error'=>1,'msg'=>'');
        if(empty($order_sn)){
			$res['msg'] ="订单号为空，请重新输入";
			Util::jsonExit($res);
		}
		if(empty($bosksn)){
			$res['msg'] ="请输入预约单号！";
			Util::jsonExit($res);
		}
        $orderModel= new BaseOrderInfoModel(27);
		$boskinfo = $orderModel->getbosksn($bosksn);
		if(empty($boskinfo)){
			$res['msg'] ="预约单号错误,请重新输入！";
			Util::jsonExit($res);
		}
        if($boskinfo['bespoke_status']!=2) {
            $res['msg'] ="输入的预约单未审核或已作废！";
            Util::jsonExit($res);
        }
		$boskid = $boskinfo['bespoke_id'];
		$department_id =  $boskinfo['department_id'];
		$customermobile =  $boskinfo['customer_mobile'];
		$customer_source_id = $boskinfo['customer_source_id'];

        $order_sn_list = '';
        $order_infos = array();
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
                if($order_info_ct['bespoke_id'] == $boskid){
                    continue;
                }
                if($order_info_ct['department_id'] != $department_id)
                {
                    $res['msg'] = $val."订单号和预约单号".$bosksn."的销售渠道不一样,请重新输入。";
                    Util::jsonExit($res);
                }
                if($order_info_ct['mobile'] != $customermobile) {
                    $res['msg'] = $val."订单号和预约单号".$bosksn."的手机号不一样,请重新输入。";
                    Util::jsonExit($res);
                }
                $order_infos[$order_info_ct['id']] = $order_info_ct;
                $order_sn_list .= "'".$val."',";
            }
        }
        if(empty($order_info_ct)){
            $res['msg'] = "订单号无效 或 订单预约号一致";
            Util::jsonExit($res);
        }
        // 多个订单修改时，订单制单人必须一样
        if (count($order_infos)>1) {
            $create_users = array_unique(array_column($order_infos, 'create_user'));
            if (count($create_users) != 1) {
                $res['msg'] = "多个订单的制单人不是同一个，不能改成同一预约号";
                Util::jsonExit($res);
            }
        }

        //1.写入订单日志
        foreach ($order_infos as $order_info) {
            $bespoke_sn = $orderModel->getBespokeSnByBespokeid($order_info['bespoke_id']);
            $logInfo = array(
                'order_id'=>$order_info['id'],
                'order_status'=>$order_info['order_status'],
                'shipping_status'=>$order_info['send_good_status'],
                'pay_status'=>$order_info['order_pay_status'],
                'create_user'=>$_SESSION['userName'],
                'create_time'=>date("Y-m-d H:i:s"),
                'remark'=>'预约单号'.$bespoke_sn.'修改为'.$bosksn // 数据上改的是预约id
            );
            $orderModel->addOrderAction($logInfo);
        }

        //2.改订单预约号，同时修改来源=预约来源
        $where = array();
        $where['order_sn'] = rtrim($order_sn_list,',');
        $where['bespoke_id'] = $boskid;
		$where['customer_source_id'] = $customer_source_id;
		$ret = $orderModel->updateOrderBespokeBySn($where);

        //3.回写预约到店时间等信息
        $data = $order_infos;
        $orderinfo = array_shift($data);// 取最早的订单作为预约的到店时间
        $data = array(
            'real_inshop_time'=>$orderinfo['create_time'],
            'accecipt_man'=>$orderinfo['create_user'],
            'queue_status'=>4,
            'withuserdo'=>1,
            're_status'=>1,
            'deal_status'=>$this->checkOrdersIsDeal($order_infos) ? 1 : 2
        );
        $orderModel->updateBespokeInfo($boskid, $data);

        //4.订单原来的预约号处理
        foreach ($order_infos as $order_info) {
            $besInfo = array();
            $data = $orderModel->getOrderInfoByBespokeId($order_info['bespoke_id']);
            if (empty($data)) {
                // 4.1 作废：如果没有其他关联的订单，预约单状态变成作废
                $besInfo['bespoke_status'] = 3;
            } else {
                // 4.2 成交：如果订单中有 成交的非赠品单就算预约成交了；其他未成交
                $besInfo['deal_status'] = $this->checkOrdersIsDeal($data) ? 1 : 2;
            }
            $orderModel->updateBespokeInfo($order_info['bespoke_id'], $besInfo);
        }
        if($ret){
            $res['error'] = 0;
            $res['msg'] ='修改成功！';
            Util::jsonExit($res);
        }
	}
    // 检查订单列表中是否存在 非赠品付款单
    private function checkOrdersIsDeal($order_infos=array()) {
        foreach ($order_infos as $item) {
            if ($item['is_zp']==0 && $item['order_pay_status']>1) {
                return true;
            }
        }
        return false;
    }
}

?>