<?php
/**
 *  -------------------------------------------------
 *   @file		: ShipFreightController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class OrderInvoiceController extends CommonController
{
    protected $smartyDebugEnabled = false;

    /**
     *	index，搜索框
     */
    public function index ($params)
    {
        $this->render('order_invoice_search_form.html',array('bar'=>Auth::getBar()));
    }

    /**
     * 查询订单信息
     */
    public function orderSearch(){
        $order_no = _Post::getString('order_no');
        $apiorder = new ApiOrderModel();
        $res = $apiorder->GetOrderInfoInvoiceBySn($order_no);
        
        $appOrderInvoiceModel = new AppOrderInvoiceModel(27);
        $invoiceList = $appOrderInvoiceModel->getOrderInvoice($order_no);
        $hidden = true;
        $memberInfoApi = new ApiMemberModel();
        if(!empty($res['info'])){
            $hidden = Util::zhantingInfoHidden($res['info']);
            $userInfo = $memberInfoApi->GetMemberByMember_id($res['info']['user_id']);
            if($userInfo['error']!=0){
                $userInfo['data']['member_name']="未查到此会员信息";
            }
        }else{
            $res['info']=array();
            $userInfo['data']['member_name']="未查到此会员信息";
        }
        if(!isset($res['fapiao'])){
            $res['fapiao']=array();
        }else{
            if($res['fapiao'][0]['invoice_num']!=''){
                $invoiceModel = new BaseInvoiceInfoModel(29);
                $status = $invoiceModel->getInvoiceNumEx($res['fapiao'][0]['invoice_num']);
                if($status==3){
                    $res['fapiao'][0]['zuofeitishi']=1;
                }else{
                    $res['fapiao'][0]['zuofeitishi']=0;
                }
            }else{
                $res['fapiao'][0]['zuofeitishi']=0;
            }

        } 
        $is_close = 0;
        if($res['info']['apply_close']==1 || $res['info']['order_status']==4){
            $is_close = 1;
        }

        $this->render('order_invoice_info.html',array(
                'is_close'=>$is_close,'info'=>$res['info'],'fapiaoinfo'=>$res['fapiao'],
                'cmodel'=>new CustomerSourcesModel(1),
                'userInfo'=>$userInfo['data'],
                'smodel'=>new SalesChannelsModel(1),
                'invoiceList'=>$invoiceList,
                'hidden'=>$hidden                
            ));
    }

    public function setInvoiceNum(){
        $result = array('success' => 0,'error' => '');
        $order_id = _Post::getInt('order_id');//订单id
        $order_sn=_Post::getString('order_sn');
        $invoice_num = _Post::get('invoice_num');//要写的发票号

            if(!Util::isNum($invoice_num)){
                $result['error']="发票只能值纯数字";
                Util::jsonExit($result);
            }
        $invoiceModel = new BaseInvoiceInfoModel(29);
         //$res = $invoiceModel->getInvoiceNumEx($invoice_num);
        //不验证发票号是否重复
        $res = 0;

        if($res){
            $result['error']="发票号已经使用过了不能继续使用";
            Util::jsonExit($result);
        }
        $apiorder = new ApiOrderModel();
        $res = $apiorder->updateOrderInfoInvoiceByid($order_id,array('invoice_num'=>$invoice_num,'invoice_status'=>2));
        $orderInfo = $apiorder->getOrderList($order_id);
        $log_remark = "更新发票状态为已开发票，发票号为：".$invoice_num;
        $logInfo = array(
            'order_id'=>$order_id,
            'order_status'=>$orderInfo['order_status'],
            'shipping_status'=>$orderInfo['send_good_status'],
            'pay_status'=>$orderInfo['order_pay_status'],
            'create_user'=>$_SESSION['userName'],
            //'create_time'=>date("Y-m-d H:i:s"),
            'remark'=>$log_remark
        );
        $apiorder->mkOrderInfoLog($logInfo);
        $olddo=array();
        $res1 = $apiorder->GetOrderInfoInvoiceBySn($order_sn);
        $newdo=array(
            'invoice_num'=>$invoice_num,
            'price'=>$res1['fapiao'][0]['invoice_amount'],
            'title'=>$res1['fapiao'][0]['invoice_title'],
            'content'=>'',
            'status'=>2,
            'create_user'=>$res1['info']['create_user'],
            'create_time'=>$res1['info']['create_time'],
            'use_user'=>$_SESSION['userName'],
            'use_time'=>date('Y-m-d H:i:s'),
            'order_sn'=>$order_sn,
            'type'=>1,
        );
        $res = $invoiceModel->saveData($newdo,$olddo);
        if(!$res){
            $result['error']="填写发票号码失败";
            Util::jsonExit($result);
        }else{

            $result['success']=1;
            Util::jsonExit($result);
        }
    }

}

?>