<?php
/**
 *  -------------------------------------------------
 *   @file		: GoodsWarehouseController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 10:55:30
 *   @update	:
 *  -------------------------------------------------
 */
class DistributionController extends CommonController
{
    protected $smartyDebugEnabled = false;


    public function add ($params)
    {
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }        
        $this->render('distribution_info.html',array(
            'bar'=>Auth::getBar(),
        ));
    }


    public function insert ($params)
    {

        $result = array('success' => 0,'error' =>'');
        $orderids= _Request::get('orderids');

        if($orderids==''){
            $result['success'] =1;
            $result['error'] = '您至少需要填写一个订单号';
            Util::jsonExit($result);
        }
        //替换回车
        $orderarr=explode("\n",$orderids);
        //判断是所填订单是否存在
        $res = ApiSalesModel::GetExistOrdersn($orderarr);
        if($res['error']!=0){
            $result['success'] =1;
            $result['error'] = $res['return_msg'];
            Util::jsonExit($result);
        }


        //如果所填所有订单均不存在直接return
        if($res['error_msg']['order_arr']!=array()){
            //开始数据分类
            $blue=array();
            $green=array();
            foreach($res['error_msg']['order_arr'] as $key=>$val){
                if($val!=2){
                    $blue[]=$key;
                }else{
                    $green[]=$key;
                }
            }
            //把符合修改订单的单据修改状态为配货中
            if($green!==array()){
                 $res1 = ApiSalesModel::EditOrderdeliveryStatus($green,3,date("Y-m-d H:i:s"),$_SESSION['userName']);
                if($res1['error']!=0){
                    $result['success'] =1;
                    $result['error'] = $res1['return_msg'];
                    Util::jsonExit($result);
                }
            }

        //数据重组返回前台
            $res['error_msg']['order_err'] = $res['error_msg']['order_err']==null?array():$res['error_msg']['order_err'];
        $arr=implode(',',$green);
        $err=implode(',',$res['error_msg']['order_err']);
        $bluearr=implode(',',$blue);
        $result['content'] = '';
        if($arr!==''){
            $result['content'].='<span style="color:limegreen">'.$arr.'</span>已修改为配货中状态<br/>';
        }
        if($err!==''){
            $result['content'].='<span style="color:red">'.$err.'</span>无此订单请重新核对订单号是否有误<br/>';
        }
        if($bluearr!==''){
            $result['content'].='<span style="color:blue">'.$bluearr.'</span>订单未处于允许配货状态<br/>';
        }

            $result['greend']= implode(',',array_flip(array_intersect($orderarr,$green)));
            $result['red']=implode(',',array_flip(array_intersect($orderarr,$res['error_msg']['order_err'])));
            $result['blue'] =implode(',',array_flip(array_intersect($orderarr,$blue)));
        Util::jsonExit($result);
        }else{
            $result['success'] =1;
            $result['error'] ='所填写的所有订单号均为不存在的编号';
            Util::jsonExit($result);
        }
    }

}/** END CLASS**/

?>