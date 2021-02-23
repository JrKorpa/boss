<?php
define('UPDATE_FRENQ',3600);//更新频率
$start_time = get_millisecond();
require_once KELA_ROOT.'/frame/vendor/vopsdk/ApiDeliveryService.class.php';
$deliveryService = new ApiDeliveryService();
$model = new DbModel(21);

$update_total = 0;
$insert_total = 0;

$where = array();
$where['st_sell_st_time'] = date('Y-m-d',strtotime('-720 day'));

$page = 1;
$page_size = 50;
$polist = $deliveryService->getPoList($where,$page,$page_size);
$page ++;
if(is_object($polist) && !empty($polist->purchase_order_list)){
    
    $total = $polist->total;
    $purchase_order_list = $polist->purchase_order_list;    
    $page_count = ceil($total/$page_size); 
    //$page_count = 1;//测试
    updatePolist($purchase_order_list);
    while($page <= $page_count){
        $page ++;
        $polist = $deliveryService->getPoList($where,$page,$page_size);
        if(is_object($polist) && !empty($polist->purchase_order_list)){
             $purchase_order_list = $polist->purchase_order_list;             
             updatePolist($purchase_order_list);
        }
    }
}
$end_time = get_millisecond();
$time_diff = ($end_time-$start_time)/1000;
echo "vip_polist表同步数据成功！<hr/>";
echo "新增数据：".$insert_total.'<br/>';
echo "更新数据：".$update_total.'<br/>';
echo "更新耗时：".$time_diff.'秒 <br/>';

