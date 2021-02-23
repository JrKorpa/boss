<?php
/**
 *  -------------------------------------------------
 *   @file		: ApplicationController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-19
 *   @update	:
 *  -------------------------------------------------
 */
class ExternalOrderPayController extends CommonController
{

	protected $smartyDebugEnabled = false;
    protected $from_arr = array(
2 => array("ad_name"=> "淘宝B店", "api_path" =>"taobaoOrderApi"),
"taobaoC" => array("ad_name"=> "淘宝C店", "api_path" =>"taobaoOrderApi"),
"jingdongA" => array("ad_name"=> "京东", "api_path" =>"jd_jdk_php_2"),
"jingdongB" => array("ad_name"=> "京东/裸钻", "api_path" =>"jd_jda_php"),
"jingdongC" => array("ad_name"=> "京东/金条", "api_path" =>"jd_jdb_php"),
"jingdongD" => array("ad_name"=> "京东/名品手表", "api_path" =>"jd_jdc_php"),
"jingdongE" => array("ad_name"=> "京东/欧若雅", "api_path" =>"jd_jdd_php"),
"jingdongF" => array("ad_name"=> "京东SOP", "api_path" =>"jd_jde_php"),
"paipai" => array("ad_name"=> "拍拍网店", "api_path" =>"paipaiOrder")
);


	public function add ()
	{
        $jumpurl = "index.php?mod=sales&con=BaseOrderInfo&act=index";
        $menuModel = new MenuModel(1);
        $menu = $menuModel->getMenuId($jumpurl);
	    $this->render('external_pay.html',array('menu'=>$menu));

	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
        $result = array('success' => 0,'error' =>'');
        $error=array(1=>'外部订单出错',2=>'淘宝订单状态未处于等待发货状态不能付款',3=>'支付金额不等于实际支付金额',4=>'流水号已经支付过',5=>'获取BDD订单的信息失败',6=>'保存到财务失败',7=>'支付订单更改失败',8=>'BDD订单没有关联外部订单',9=>'外部订单信息不符合不能支付',111=>'第一次付款时间写入失败');
        $taobao_order_sn =_Request::getString('exter_order_num');
        $order_sn =_Request::getString('order_sn');
        $price =_Request::getFloat('exter_order_price');
        //获取BDD订单的信息
        $model = new BaseOrderInfoModel(28);
        $order_info =$model->getAccountInfo($order_sn);
        if(empty($order_info)){
            $result['error']='获取BDD订单的信息失败';
            Util::jsonExit($result);
        }
        if($price>$order_info['money_unpaid']){
            $result['error']='支付金额超过BDD订单未付价格请核对！';
            Util::jsonExit($result);
        }
        if($order_info['order_status']!=2){
            $result['error']='BDD订单 未处于审核状态不允许支付';
            Util::jsonExit($result);
        }
        $from_type=$order_info['department_id'];
        $file_path = APP_ROOT."sales/modules/".$this->from_arr[$from_type]["api_path"]."/index.php";
        if(!file_exists($file_path)){
            $result['error']='接口文件不存在';
            Util::jsonExit($result);
        }
        require_once($file_path);
        $apiM = $this->from_arr[$from_type]["api_path"];
        $api_order = new $apiM();
        /* 支付 */
        //生成一个支付凭据
        $date = date("Ymd");
        $header='DK-KLSZFGS-'.$date;
        $receipt_id = rand(0,999);
        $nes = str_pad($receipt_id,4,'0',STR_PAD_LEFT);
        $bonus_code=$header.$nes;
        $order_info['bonus_code']=$bonus_code;

        $r = $api_order -> outer_order_pay($order_info, $taobao_order_sn, $price);
        if(!is_array($r)){
            $result['error']=$error[$r];
        }else{
            $result['success']=1;
            //写入日志
            $orderActionModel = new AppOrderActionModel(27);
            //操作日志
            $ation['order_status'] = $order_info['order_status'];
            $ation['order_id'] = $r['order_id'];
            $ation['shipping_status'] = $order_info['send_good_status'];
            $ation['pay_status'] = $r['pay_stu'];
            $ation['create_user'] = $_SESSION['userName'];
            $ation['create_time'] = date("Y-m-d H:i:s");
            $ation['remark'] = "外部订单[$taobao_order_sn]，通过外部订单支付了$price 元";
            $orderActionModel->saveData($ation, array());
            $result['success'] = 1;
            $result['error']=$order_sn;
        }
        Util::jsonExit($result);
	}

//获取该外部订单的应支付的金额
public function GetOutPrice(){
    $out_order_sn=_Request::get('exter_order_num');
    //这里只限淘宝2
    $apiarr = $this->from_arr[2];
    $file_path = APP_ROOT."sales/modules/".$apiarr["api_path"]."/index.php";
    if(!file_exists($file_path)){
      return;
    }
    require_once($file_path);
    $apiM = $this->from_arr[2]["api_path"];
    $api_order = new $apiM();
    $info = $api_order->get_order_info($out_order_sn);
    $arr= array();
    if(trim($info -> code)){
        $arr['exter_order_price']='';
    }else{
        $price =(float) $info -> trade -> payment;
        $arr['exter_order_price']=$price;
    }
    $orderM=new BaseOrderInfoModel(27);
    $orderinfo  = $orderM->checkOrderByWhere($out_order_sn);
    if(!empty($orderinfo)){
        $arr['order_sn']=$orderinfo['order_sn'];
    }else{
        $arr['order_sn']='';
    }
    Util::jsonExit($arr);
}


}

?>