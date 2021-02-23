<?php
define('UPDATE_FRENQ',3600);//更新频率
$start_time = get_millisecond();
require_once KELA_ROOT.'/frame/vendor/vopsdk/ApiDeliveryService.class.php';
$deliveryService = new ApiDeliveryService();
$model = new DbModel(21);

$is_create_detail = isset($_GET['is_create_detail'])?1:0;

$update_total = 0;
$insert_total = 0;
$update_frenq = 3600;//更新数据周期1小时
$where = array();
//默认只更30天以内的数据
if(!empty($_REQUEST['days'])){
    $where['st_create_time'] = date('Y-m-d H:i:s',strtotime("-{$_REQUEST['days']} day"));
}else{
    $where['st_create_time'] = date('Y-m-d H:i:s',strtotime('-20 day'));
}

if(!empty($_REQUEST['po_no'])){
    $where['st_create_time'] = date('Y-m-d H:i:s',strtotime('-1000 day'));
    $where['po_no'] = $_REQUEST['po_no'];
    $update_frenq = 60;
}
if(!empty($_REQUEST['pick_no'])){
    $where['st_create_time'] = date('Y-m-d H:i:s',strtotime('-1000 day'));
    $where['pick_no'] = $_REQUEST['pick_no'];
    $update_frenq = 60;
}
$page = 1;
$page_size = 50;
try{
    $picklist = $deliveryService->getPickList($where,$page,$page_size);
    $page ++;
    if(is_object($picklist) && !empty($picklist->picks)){
        $total = $picklist->total;
        $page_count = ceil($total/$page_size); 
        //$page_count = 1;//测试
        updatePickList($picklist->picks);
        while($page <= $page_count){
            $page ++;
            $picklist = $deliveryService->getpicklist($where,$page,$page_size);
            if(is_object($picklist) && !empty($picklist->picks)){        
                 updatePickList($picklist->picks);
            }
        } 
    }
}catch (\Osp\Exception\OspException $e){
     echo "error:".$e->getReturnMessage();
     exit;
}
$end_time = get_millisecond();
$time_diff = ($end_time-$start_time)/1000;
echo "vip_picklist同步数据成功！<hr/>";
echo "新增数据：".$insert_total.'<br/>';
echo "更新数据：".$update_total.'<br/>';
echo "更新耗时：".$time_diff.'秒 <br/>';
