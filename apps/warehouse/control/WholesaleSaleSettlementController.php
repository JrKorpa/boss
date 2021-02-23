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
class WholesaleSaleSettlementController extends CommonController
{
    protected $smartyDebugEnabled = false;


    public function index ($params)
    {
        $this->render('wholesaleSaleSettlement_info.html',array(
            'bar'=>Auth::getBar(),
        ));
    }


    //结算
    public function settlement ($params)
    {

        $result = array('success' => 0,'error' =>'');
        $goods_ids= _Request::get('goods_ids');
        $bill_no_p = _Request::get('bill_no_p');

        if($bill_no_p==''){
            $result['success'] =1;
            $result['error'] = '请填写批发销售单号';
            Util::jsonExit($result);
        }

        if($goods_ids==''){
            $result['success'] =1;
            $result['error'] = '请填写货号';
            Util::jsonExit($result);
        }

        $goods_ids_str = '';
        $args = array();
        $args['goods_ids'] = str_replace(' ',',',$goods_ids);
        $args['goods_ids'] = str_replace('，',',',$goods_ids);
        $args['goods_ids'] = str_replace("\n",',',$goods_ids);
        $tmp = explode(",", $args['goods_ids']);
        foreach($tmp as $val){
            $goods_ids_str .= "'$val',";
        }
        $goods_ids_str = rtrim($goods_ids_str,',');

        //（3）   输入批发销售单，批量结算的货号必须在同一个批发销售单里，并且属于上面输入的批发销售单
        $model = new WarehouseGoodsModel(21);
        $billInfo = $model->getBillInfobyNo($bill_no_p);
        
        if(empty($billInfo)){
            $result['success'] =1;
            $result['error'] = '没有此单据信息';
            Util::jsonExit($result);
        }
        $billInfo = array_column($billInfo,'goods_id');

        //判断是否同一单据货品
        foreach ($tmp as $key => $value) {
            # code...
            if(!in_array($value, $billInfo)){
                $result['success'] =1;
                $result['error'] = '货号‘'.$value.'’不是批发单中的货品';
                Util::jsonExit($result);
            }
        }

        //（4）   点击【结算】，批发销售单里对应货号的门店结算状态变成【已结算】，更新批发销售单结算操作时间
        $res = $model->upSettlementStatus($bill_no_p, $goods_ids_str, 2);
        if($res == false){
            $result['success'] =1;
            $result['error'] = '结算失败！';
            Util::jsonExit($result);
        }else{
            $result['success'] =1;
            $result['error'] = '结算完成！';
            Util::jsonExit($result);
        }
    }

    public function refund($value='')
    {   
        $goods_ids= _Request::get('goods_ids');
        $bill_no_p = _Request::get('bill_no_p');

        if($bill_no_p==''){
            exit('请填写批发销售单号');
        }

        if($goods_ids==''){
            exit('请填写货号');
        }

        $goods_ids_str = '';
        $args = array();
        $args['goods_ids'] = str_replace(' ',',',$goods_ids);
        $args['goods_ids'] = str_replace('，',',',$goods_ids);
        $args['goods_ids'] = str_replace("\n",',',$goods_ids);
        $tmp = explode(",", $args['goods_ids']);
        $model = new WarehouseGoodsModel(21);
        foreach($tmp as $goods_id){
            //验证下此货号是否有【已审核】的【批发退货单】，有才会更新状态已退货，更新批发销售单结算操作时间。
            $is_check_h = $model->checkisH($goods_id);
            if(!$is_check_h){
                exit('货号‘'.$goods_id.'’没有已审核的批发退货单，无法退货！');
            }
            $goods_ids_str .= "'$goods_id',";
        }
        $goods_ids_str = rtrim($goods_ids_str,',');

        //（3）   输入批发销售单，批量结算的货号必须在同一个批发销售单里，并且属于上面输入的批发销售单
        $billInfo = $model->getBillInfobyNo($bill_no_p);
        
        if(empty($billInfo)){
            exit('没有此单据信息');
        }
        $billInfo = array_column($billInfo,'goods_id');

        //判断是否同一单据货品
        foreach ($tmp as $key => $value) {
            # code...
            if(!in_array($value, $billInfo)){
                exit('货号‘'.$value.'’不是批发单中的货品');
            }
        }

        //（5）   点击【退货】，批发销售单里对应货号的门店结算状态变成【已退货】，验证下此货号是否有【已审核】的【批发退货单】，有才会更新状态已退货，更新批发销售单结算操作时间。
        $res = $model->upRefundStatus($bill_no_p, $goods_ids_str, 3);
        if($res == false){
            exit('退货失败！');
        }else{
            exit('退货完成！');
        }
    }

}/** END CLASS**/

?>