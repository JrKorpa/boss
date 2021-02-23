<?php
/**
*  -------------------------------------------------
*   @file		: NotReceivingController.php
*   @link		:  www.kela.cn
*   @copyright	: 2014-2024 kela Inc
*   @author		: Laipiyang <462166282@qq.com>
*   @date		: 2015-01-11 10:55:30
*   @update		:
*   @未收货列表
*  -------------------------------------------------
*/
class NotReceivingController extends CommonController
{
    protected $smartyDebugEnabled = false;
    /**
     *  index，搜索框
     */
    public function index ($params)
    {
        $this->render('not_receiving_search_form.html',array(
            'bar'=>Auth::getBar(),
        ));
    }

    public function search($params){
        $args = array(
            'mod'   => _Request::get("mod"),
            'con'   => substr(__CLASS__, 0, -10),
            'act'   => __FUNCTION__,
            'type'   => _Request::getInt('type'),
            'order_sn'   => _Request::getString('order_sn'),
            'consignee'   => _Request::getString('consignee'),
//            'order_status'   => array(1),
//            'shipping_status'   => array(1,4),
//            'page'   => _Request::getInt("page",1),
        );

        
        $where = array();
        $arrival_status = array(1,2,3,5);
        if($args['type']==1){
            $arrival_status = array(1,2,3);
        }else if($args['type']==2){
            $arrival_status = array(3);
        }else if($args['type']==3){
            $arrival_status = array(5);
        }
		$where['goods_status']=array(1,2,3);
        $where['arrival_status']=$arrival_status;
		$where['order_status']=array(2);
		$where['shipping_status']=array(1,4);
		$where['order_sn']=$args['order_sn'];
		$where['consignee']=$args['consignee'];
        $where['page']=_Request::getInt("page",1);
        if($_SESSION['userType']>1){
            $usr_channel_model=new UserChannelModel(1);
            $res=$usr_channel_model->getChannels($_SESSION['userId'],0);
            if($res){
                $res = array_column($res,'id');
                $where['department_id']=$res;
            } 
        }
        if(SYS_SCOPE == 'zhanting'){
            $where['hidden'] = '0';
        }
        $result = ApiSalesModel::getNotReceivingOrder($where);
        //print_r($result);
        if(!empty($result)){
            foreach($result['data'] as $key=>$row){ //布产天数
                $result['data'][$key]['fate']=number_format((time()-strtotime($row['effect_date']))/(3600*24),0);
                $order_goods_info=ApiSalesModel::GetDetailByOrderID($row['order_id']);
                if($order_goods_info['error']>0){
                    $result['data'][$key]['goods_number'] = 0;
                    $result['data'][$key]['yidao'] = 0;
                }else{
                    $goods_status=array();
                    $goods_status['yidao']=$goods_status['weidao']=0;
                    foreach($order_goods_info['return_msg'] as $gs){
                        if($gs['send_good_status']==5 || $gs['send_good_status']==3){
                            $goods_status['yidao']++;
                        }else{
                            $goods_status['weidao']++;
                        }
                    }

                    $result['data'][$key]['goods_number'] = $goods_status['yidao'] + $goods_status['weidao'];
                    $result['data'][$key]['yidao'] = $goods_status['yidao'];
                }
            }
        }
        $pageData = $result;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'not_receiving_search_page';
        $this->render('not_receiving_search_list.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$result
        ));
    }
    
    /**
     * 收货
     * @param type $param
     */
    public function receiving($param) {
        $result = array('success' => 0,'error' => '');
		$id = intval($param['id']);
        if($id>0){
			$order_goods=ApiSalesModel::getOrderDetailsById(array('goods_id'=>$id,'fields'=>"`details_status`,`buchan_status`,`order_id`,`send_good_status`,`goods_sn`,`goods_name`"));
			if(empty($order_goods)){
				$result['error']='此订单商品不存在!';
				Util::jsonExit($result);
			}
		}else{
			$result['error']='此订单商品号不存在!';
			Util::jsonExit($result);
		}
        
//        if($order_goods['buchan_status']!=9){
//            $result['error']='此产品未出厂!';
//			Util::jsonExit($result);
//        }
        if($order_goods['send_good_status']==3){
            $result['success'] = 1;
            Util::jsonExit($result);
        }
        $updatedata=array();
		$updatedata['send_good_status']=3;
        $order_info = ApiSalesModel::GetOrderInfoByOrderId($order_goods['order_id']);
		$res = ApiSalesModel::updateOrderDetailsById(array('detail_id'=>$id,'apply_info'=>$updatedata));
        if($res)
		{
            $order_info = $order_info['return_msg'];
            $remark = $order_goods['goods_name'].'已收货未检验';
            $create_user = $_SESSION ['userName'];
            ApiSalesModel::addOrderAction($order_info ['order_sn'], $create_user, $remark);
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
    }
    
    
    /**
     * 返厂
     * @param type $param
     */
    public function palautus($param) {
        $result = array('success' => 0,'error' => '');
		$id = intval($param['id']);
        if($id>0){
			$order_goods=ApiSalesModel::getOrderDetailsById(array('goods_id'=>$id,'fields'=>"`details_status`,`order_id`,`send_good_status`,`goods_sn`,`goods_name`"));
			if(empty($order_goods)){
				$result['error']='此订单商品不存在!';
				Util::jsonExit($result);
			}
		}else{
			$result['error']='此订单商品号不存在!';
			Util::jsonExit($result);
		}
        
        if($order_goods['send_good_status']==5){
            $result['success'] = 1;
            Util::jsonExit($result);
        }
        $updatedata=array();
		$updatedata['send_good_status']=5;
        $order_info = ApiSalesModel::GetOrderInfoByOrderId($order_goods['order_id']);
		$res = ApiSalesModel::updateOrderDetailsById(array('detail_id'=>$id,'apply_info'=>$updatedata));
        if($res)
		{
            $order_info = $order_info['return_msg'];
            $remark = $order_goods['goods_name'].'返厂成功';
            $create_user = $_SESSION ['userName'];
            ApiSalesModel::addOrderAction($order_info ['order_sn'],$create_user, $remark);
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
    }
    

    /**
     * 检验合格
     * @param type $param
     */
    public function check_succ($param) {
        $result = array('success' => 0,'error' => '');
		$id = intval($param['id']);
        if($id>0){
			$order_goods=ApiSalesModel::getOrderDetailsById(array('goods_id'=>$id,'fields'=>"`details_status`,`order_id`,`send_good_status`,`goods_sn`,`goods_name`"));
			if(empty($order_goods)){
				$result['error']='此订单商品不存在!';
				Util::jsonExit($result);
			}
		}else{
			$result['error']='此订单商品号不存在!';
			Util::jsonExit($result);
		}
        
//        if($order_goods['details_status']!=3&&$order_goods['details_status']!=4&&$order_goods['details_status']!=0){
//            $result['error']='此产品未出厂!';
//			Util::jsonExit($result);
//        }
        if($order_goods['send_good_status']!=3){
            $result['error'] = "该订单货品发货状态不正确";
            Util::jsonExit($result);
        }
        $updatedata=array();
		$updatedata['send_good_status']=4;
        $order_info = ApiSalesModel::GetOrderInfoByOrderId($order_goods['order_id']);
		$res = ApiSalesModel::updateOrderDetailsById(array('detail_id'=>$id,'apply_info'=>$updatedata));
        if($res)
		{
            $order_info = $order_info['return_msg'];
            $remark = $order_goods['goods_name'].'收货成功';
            $create_user = $_SESSION ['userName'];
            ApiSalesModel::addOrderAction($order_info['order_sn'],$create_user, $remark);
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "检验合格失败";
		}
		Util::jsonExit($result);
    }
    
}/** END CLASS**/

?>
