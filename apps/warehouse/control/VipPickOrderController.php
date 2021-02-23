<?php
use vipapis\informal\_RedirectServiceClient;
/**
 * 唯品会拣货订单管理
*  -------------------------------------------------
*   @file		: VipPickOrderController.php
*   @link		:  www.kela.cn
*   @copyright	: 2014-2024 kela Inc
*   @date		: 2017-06-26
*   @update	:
*  -------------------------------------------------
*/
class VipPickOrderController extends CommonController
{
    protected $smartyDebugEnabled = false;
    protected $whitelist = array('exportSearch','printOrder','printOrderGoods');
    
    public function index($params){
        $this->render('vip_pick_order_search_form.html',array(
            'pickDetailsView'=>new VipPickDetailsView(new VipPickDetailsModel(21)),
            'bar'=>Auth::getBar(),
        ));
    }

    public function search($params){
        $args = array(
            'mod'			=> _Request::get("mod"),
            'con'			=> substr(__CLASS__, 0, -10),
            'act'			=> __FUNCTION__,
            'pick_no'		=> _Request::get("pick_no"),
            'po_no'		=> _Request::get("po_no"),
            'order_sn'		=> _Request::get("order_sn"),
            'storage_no'=>_Request::get("storage_no"),
            'out_flag'=>_Request::get("out_flag"),
            'delivery_no'=>_Request::get("delivery_no"),
            'st_create_time'=>_Request::get("st_create_time"),//订单创建时间
            'et_create_time'=>_Request::get("et_create_time"),
            'warehouse'=>_Request::get("warehouse"),
            'is_create_delivery'=>_Request::get("is_create_delivery"),
            'is_print_tihuo'=>_Request::get("is_print_tihuo"),
            'barcode'=>_Request::get("barcode"),
            'order_status'=>_Request::get("order_status"),
            'send_good_status'=>_Request::get("send_good_status"),
            'is_stock_goods'=>_Request::get("is_stock_goods")
        );
        
        $where = $args;
        if(!empty($where['po_no'])){
            $where['po_no'] = str_replace(',', ' ',$where['po_no']);
            $where['po_no'] = preg_replace('/\s+/is',' ',$where['po_no']);
            $where['po_no'] = explode(' ',$where['po_no']);
            $where['po_no'] = count($where['po_no'])>1?$where['po_no']:$where['po_no'][0];
        }
        if(!empty($where['pick_no'])){
            $where['pick_no'] = str_replace(',', ' ',$where['pick_no']);
            $where['pick_no'] = preg_replace('/\s+/is',' ',$where['pick_no']);
            $where['pick_no'] = explode(' ',$where['pick_no']);
            $where['pick_no'] = count($where['pick_no'])>1?$where['pick_no']:$where['pick_no'][0];
        }
        if(!empty($where['order_sn'])){
            $where['order_sn'] = str_replace(',', ' ',$where['order_sn']);
            $where['order_sn'] = preg_replace('/\s+/is',' ',$where['order_sn']);
            $where['order_sn'] = explode(' ',$where['order_sn']);
            $where['order_sn'] = count($where['order_sn'])>1?$where['order_sn']:$where['order_sn'][0];
        }
        if(!empty($where['barcode'])){
            $where['barcode'] = str_replace(',', ' ',$where['barcode']);
            $where['barcode'] = preg_replace('/\s+/is',' ',$where['barcode']);
            $where['barcode'] = explode(' ',$where['barcode']);
            $where['barcode'] = count($where['barcode'])>1?$where['barcode']:$where['barcode'][0];
        }
        
        $page = _Request::getInt("page",1);
        $pageSize = _Request::getInt('page_size',30);
        
        $model = new VipPickOrderDetailsModel(21);
        $data = $model->pageList($where, $page,$pageSize);
        
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'vip_pick_order_search_page';
        //print_r($pageData);
        $this->render('vip_pick_order_search_list.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$pageData,
            'pickDetailsView'=>new VipPickDetailsView(new VipPickDetailsModel(21)),
        ));
    } 
    //重置拣货单
    public function closeOrder($params){
        $result = array('success'=>0,'error'=>'');
        
        $ids = _Request::getList("_ids");
        $model = new VipPickOrderDetailsModel(21);
        $salesModel = new SalesModel(27);
        $where['order_sn'] = $ids;
        $data = $model->pageList($where,1,2000);
        if(empty($data['data'])){
            $result['error'] = "没有符合条件的订单!";
            Util::jsonExit($result);
        }
        $pdolist[21] = $model->db()->db();//仓库数连接PDO
        $pdolist[27] = $salesModel->db()->db();//仓库数连接PDO
        try{
            foreach ($pdolist as $pdo){
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
                $pdo->beginTransaction(); //开启事务
            }
        }catch (Exception $e){
            $error = "操作失败，事物回滚！提示：系统批量开启事物时发生异常！";
            Util::rollbackExit($error,$pdolist);
        }
        //校验订单
        foreach ($data['data'] as $order){ 
            $order_sn = $order['order_sn']; 
            $pick_no = $order['pick_no']; 
            $barcode = $order['barcode'];
            $goods_id = $order['goods_id'];
            $order_detail_id = $order['order_detail_id'];
            if($order['order_status']!=1){
                $rerror = "订单{$order['order_sn']}不允许关闭，只有待审核的订单才可以关闭!";
                Util::rollbackExit($error,$pdolist);
            }else if($order['delivery_status']==1){
                $rerror = "订单{$order['order_sn']}已出仓，不能关闭!";
                Util::rollbackExit($error,$pdolist);
            }
            try{
                //更改捡货状态,捡货状态
                $model->db()->query("update vip_pick_details set boss_pick_num=boss_pick_num-1,boss_pick_status=0 where pick_no='{$pick_no}' and barcode='{$barcode}'");
                $model->db()->query("update vip_pick_list set boss_pick_num=boss_pick_num-1,boss_pick_status=0 where pick_no='{$pick_no}'");                
                //关闭订单
                $model->db()->query("update vip_pick_order_details set is_delete=1 where order_detail_id={$order_detail_id} and pick_no='{$pick_no}' and barcode='{$barcode}'");
                //更新订单状态  已关闭4
                $salesModel->updateBaseOrderInfo(array("order_status"=>4,'is_delete'=>1),"order_sn='{$order_sn}'");
                //订单日志
                $salesModel->AddOrderLog($order_sn,"关闭唯品会订单，重新捡货！");
                $model->db()->query("update warehouse_shipping.warehouse_goods set order_goods_id=0,is_on_sale=2 where goods_id='{$goods_id}'");
                //$error = "test！";
                //Util::rollbackExit($error,$pdolist);
                //批量提交事物
                foreach ($pdolist as $pdo){
                    $pdo->commit();
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
                }
                $result['success'] = 1;
                Util::jsonExit($result);
                
            }catch (Exception $e){
                $error = "操作失败：".$e->getMessage();
                Util::rollbackExit($error,$pdolist);
            }
        }
    }   
    /**
     * 唯品会打印提货单
     * @param unknown $params
     */
    public function printOrder($params){
        $ids = _Request::get("_ids");
        $url = "index.php?mod=warehouse&con=WaitDistribution&act=printBills&sign=3&_ids={$ids}";
        header("location:{$url}");
    }
    /**
     * 唯品会货品标签打印
     * @param unknown $params
     */
    public function printOrderGoods($params){
        $ids = _Request::get("_ids");
        $where['order_sn'] = array_unique(explode(",",$ids));
        
        $model = new VipPickOrderDetailsModel(21);
        $data = $model->pageList($where,1,2000);
        if(empty($data['data'])){
            exit("打印数据为空");
        }
        $this->render('print_order_goods.html',array(
            'data'=>$data['data']
        ));
    }
    public function exportSearch($params){
        $where = array(
            'pick_no'		=> _Request::get("pick_no"),
            'po_no'		=> _Request::get("po_no"),
            'order_sn'		=> _Request::get("order_sn"),
            'storage_no'=>_Request::get("storage_no"),
            'out_flag'=>_Request::get("out_flag"),
            'delivery_no'=>_Request::get("delivery_no"),
            'st_create_time'=>_Request::get("st_create_time"),//订单创建时间
            'et_create_time'=>_Request::get("et_create_time"),
            'warehouse'=>_Request::get("warehouse"),
            'is_create_delivery'=>_Request::get("is_create_delivery"),
            'is_print_tihuo'=>_Request::get("is_print_tihuo"),
            'barcode'=>_Request::get("barcode"),
            'order_status'=>_Request::get("order_status"),
            'send_good_status'=>_Request::get("send_good_status"),
            'is_stock_goods'=>_Request::get("is_stock_goods"),
        );
        if(!empty($where['po_no'])){
            $where['po_no'] = str_replace(',', ' ',$where['po_no']);
            $where['po_no'] = preg_replace('/\s+/is',' ',$where['po_no']);
            $where['po_no'] = explode(' ',$where['po_no']);
            $where['po_no'] = count($where['po_no'])>1?$where['po_no']:$where['po_no'][0];
        }
        if(!empty($where['pick_no'])){
            $where['pick_no'] = str_replace(',', ' ',$where['pick_no']);
            $where['pick_no'] = preg_replace('/\s+/is',' ',$where['pick_no']);
            $where['pick_no'] = explode(' ',$where['pick_no']);
            $where['pick_no'] = count($where['pick_no'])>1?$where['pick_no']:$where['pick_no'][0];
        }
        if(!empty($where['order_sn'])){
            $where['order_sn'] = str_replace(',', ' ',$where['order_sn']);
            $where['order_sn'] = preg_replace('/\s+/is',' ',$where['order_sn']);
            $where['order_sn'] = explode(' ',$where['order_sn']);
            $where['order_sn'] = count($where['order_sn'])>1?$where['order_sn']:$where['order_sn'][0];
        }   
        if(!empty($where['barcode'])){
            $where['barcode'] = str_replace(',', ' ',$where['barcode']);
            $where['barcode'] = preg_replace('/\s+/is',' ',$where['barcode']);
            $where['barcode'] = explode(' ',$where['order_sn']);
            $where['barcode'] = count($where['barcode'])>1?$where['barcode']:$where['barcode'][0];
        }
             
        $page = _Request::getInt("page",1);
        $pageSize = 20000;
        
        $model = new VipPickOrderDetailsModel(21);
        $data = $model->pageList($where, $page,$pageSize);
 
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312','拣货单订单商品列表').".xls");

        $csv_header="<table>
            <tr>
            <td>VIP商品名称</td>
            <td>BOSS订单号</td>
            <td>拣货单编号</td>
            <td>SKU码</td>
            <td>BOSS货号</td>
            <td>BOSS款号</td>
            <td>BOSS商品名称</td>
            <td>PO单编号</td>
            <td>送货仓库</td>
            <td>送货状态</td>
            <td>出仓单号</td>
            <td>BOSS订单状态</td>
            <td>BOSS发货状态</td>
            <td>创建时间</td>            
            </tr>";
        $csv_body = '';
        if(!empty($data['data'])){
            foreach ($data['data'] as $kv => $info) {
                $info['warehouse'] = VipDeliveryView::getWarehouseName($info['warehouse']);
                $info['out_flag'] = VipDeliveryView::getDeliveryStatusName($info['out_flag']);
                $info['order_status'] = $this->dd->getEnum('order.order_status',$info['order_status']);
                $info['send_good_status'] = $this->dd->getEnum('order.send_good_status',$info['send_good_status']);
                $info['po_no'] = str_replace(",","|",$info['po_no']);
                $csv_body.="<tr>
                <td>{$info['product_name']}</td>
                <td>{$info['order_sn']}</td>
                <td>{$info['pick_no']}</td>
                <td>{$info['barcode']}</td>
                <td>{$info['goods_id']}</td>
                <td>{$info['style_sn']}</td>
                <td>{$info['goods_name']}</td>
                <td>{$info['po_no']}</td>                
                <td>{$info['warehouse']}</td>
                <td>{$info['out_flag']}</td>
                <td>{$info['storage_no']}</td>
                <td>{$info['order_status']}</td>
                <td>{$info['send_good_status']}</td>
                <td>{$info['create_time']}</td>
                </tr>";
            }
        }
        $csv_footer="</table>";
        echo $csv_header.$csv_body.$csv_footer;
    }
    
}