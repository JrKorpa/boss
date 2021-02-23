<?php
/**
 * 获取毫秒时间
 * @return number
 */
function get_millisecond() {
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
}
/**
 * 同步更新拣货单
 * @param unknown $picks
 */
function updatePickList($picks){
    global $model,$update_total,$insert_total,$update_frenq;
    $update_frenq = empty($update_frenq)?3600:$update_frenq;

    foreach ($picks as $pick){
        $data = array();
        $pick_no = $pick->pick_no;
        $po_no   = $pick->po_no;
        $data['po_no'] = $pick->po_no;
        $data['pick_no'] = $pick->pick_no;
        $data['co_mode'] = $pick->co_mode;
        $data['sell_site'] = $pick->sell_site;
        $data['order_cate'] = $pick->order_cate;
        $data['pick_num'] = $pick->pick_num;
        $data['create_time'] = $pick->create_time;
        $data['first_export_time'] = $pick->first_export_time;
        $data['export_num'] = $pick->export_num;
        $data['delivery_status'] = $pick->delivery_status;
        $data['store_sn'] = $pick->store_sn;
        $data['delivery_num'] = $pick->delivery_num;
        $data['update_time'] = date('Y-m-d H:i:s');//更新时间
       
        $sql = "select update_time from vip_pick_list where pick_no='{$pick_no}'";
        $exists = $model->db()->getRow($sql);
        try{
            if(!empty($exists)){
                $time_diff = time()-strtotime($exists['update_time']);
                if($time_diff<$update_frenq){
                    continue;
                }
                //距离上次更新时间大于1小时以上，再次更新
                $sql = $model->updateSql("vip_pick_list",$data,"pick_no='{$pick_no}'");
                $update_total++;
            }else{
                //if($pick->delivery_status==1){
                    //continue;//通过其他入口出库的，不再同步
                //}
                $data['boss_pick_status'] = $pick->delivery_status==1?1:0;//捡货状态
                $sql = $model->insertSql($data,'vip_pick_list');
                $insert_total ++;
            }
            $model->db()->query($sql);
        }catch (Exception $e){
            echo "vip_pick_list同步异常：".$sql;
            //exit();
        }
        //是否更新拣货单明细
        updatePickDetail($pick_no,$po_no);
    }

}
/**
 * 同步更新捡货单明细
 * @param unknown $pick_no
 * @param unknown $po_no
 * @return boolean
 */
function updatePickDetail($pick_no,$po_no){
    global $deliveryService,$model,$update_frenq;
    $update_frenq = empty($update_frenq)?3600:$update_frenq;
    
    if(preg_match("/MULPICK/is", $pick_no)){
        updateMultiPoPickDetail($pick_no);
        return;
    }    
    try {
        $pickdetail = $deliveryService->getPickDetail($po_no, $pick_no,1,9999);
    } catch(\Osp\Exception\OspException $e){
        echo "error:".$e->getReturnMessage();
        return false;
    }
    if(is_object($pickdetail) && $pickdetail->pick_product_list){
        foreach ($pickdetail->pick_product_list as $product){
            $data = array();
            $barcode = $product->barcode;
            $data['pick_no'] = $pick_no;            
            $data['po_no'] = $pickdetail->po_no;
            $data['stock'] = $product->stock;
            $data['barcode'] = $product->barcode;
            $data['art_no'] = $product->art_no;
            $data['product_name'] = $product->product_name;
            $data['size'] = $product->size;
            $data['warehouse'] = $pickdetail->warehouse;
            $data['actual_unit_price'] = $product->actual_unit_price/1;
            $data['actual_market_price'] = $product->actual_market_price/1;
            $data['not_delivery_num'] = $product->not_delivery_num/1;
            $data['update_time'] = date('Y-m-d H:i:s');//更新时间           
            $sql = "select update_time,boss_pick_status from vip_pick_details where barcode='{$barcode}' and pick_no='{$pick_no}' and po_no='{$pickdetail->po_no}'";
            try{
                $exists = $model->db()->getRow($sql);
                if(!empty($exists)){
                    $time_diff = time()-strtotime($exists['update_time']);
                    if($time_diff < $update_frenq){
                        continue;
                    }
                    //距离上次更新时间大于1小时以上，再次更新
                    $sql = $model->updateSql("vip_pick_details",$data,"barcode='{$barcode}' and pick_no='{$pick_no}' and po_no='{$pickdetail->po_no}'");
                }else{
                    $data['boss_pick_status'] = $product->not_delivery_num==0?1:0;
                    $sql = $model->insertSql($data,'vip_pick_details');
                }
                //echo $sql;
                $model->db()->query($sql);
            }catch (Exception $e){
                echo "vip_pick_details同步异常：".$sql.'<br/>';
                //exit();
            }

        }
    }else{
        echo "vip_pick_details同步异常，数据为空<br/>";
    }
}
/**
 * 同步更新多PO捡货单明细
 * @param unknown $pick_no
 * @param unknown $po_no
 * @return boolean
 */
function updateMultiPoPickDetail($pick_no){
    global $deliveryService,$model,$update_frenq;
      
    $update_frenq = empty($update_frenq)?3600:$update_frenq;
    try {
        $pickdetail = $deliveryService->getMultiPoPickDetail($pick_no,1,9999);
    } catch(\Osp\Exception\OspException $e){
        echo "error:".$e->getReturnMessage();
        return false;
    }
    
    if(is_object($pickdetail) && $pickdetail->pick_detail_list){
        foreach ($pickdetail->pick_detail_list as $product){
            $data = array();
            $barcode = $product->barcode;
            $data['pick_no'] = $product->pick_no;
            $data['po_no'] = $product->po_no;
            $data['warehouse'] = $product->warehouse;
            $data['stock'] = $product->pick_num;
            $data['barcode'] = $product->barcode;
            $data['art_no'] = $product->sn;
            $data['product_name'] = $product->product_name;
            $data['size'] = $product->size;
            //$data['actual_unit_price'] = 0;
            //$data['actual_market_price'] = $product->actual_market_price/1;
            $data['not_delivery_num'] = $product->not_delivery_num/1;
            $data['update_time'] = date('Y-m-d H:i:s');//更新时间
            $sql = "select update_time,boss_pick_status from vip_pick_details where barcode='{$barcode}' and pick_no='{$pick_no}' and po_no='{$product->po_no}'";
            try{
                $exists = $model->db()->getRow($sql);
                if(!empty($exists)){
                    $time_diff = time()-strtotime($exists['update_time']);
                    if($time_diff < $update_frenq){
                        continue;
                    }
                    //距离上次更新时间大于1小时以上，再次更新
                    $sql = $model->updateSql("vip_pick_details",$data,"barcode='{$barcode}' and pick_no='{$pick_no}' and po_no='{$product->po_no}'");
                }else{
                    $data['boss_pick_status'] = $product->not_delivery_num==0?1:0;
                    $sql = $model->insertSql($data,'vip_pick_details');
                }
                //echo $sql;
                $model->db()->query($sql);
            }catch (Exception $e){
                echo "vip_pick_details同步异常：".$sql.'<br/>';
                //exit();
            }
    
        }
    }else{
        echo "vip_pick_details同步异常，数据为空<br/>";
    }
}
/**
 * 同步更新PO单
 * @param unknown $purchase_order_list
 */
function updatePolist($purchase_order_list){
    global $deliveryService,$model,$update_total,$insert_total;
    foreach ($purchase_order_list as $po){
        $po_no = $po->po_no;
        //更新po单到本地，暂时不需要
        /*
        $data = array();
        $data['po_no'] = $po->po_no;
        $data['co_mode'] = $po->co_mode;
        $data['sell_st_time'] = $po->sell_st_time;
        $data['sell_et_time'] = $po->sell_et_time;
        $data['stock'] = $po->stock;
        $data['sales_volume'] = $po->sales_volume;
        $data['not_pick'] = $po->not_pick;
        $data['trade_mode'] = $po->trade_mode;
        $data['schedule_id'] = $po->schedule_id;
        $data['warehouse'] = $po->warehouse;
        $data['schedule_name'] = $po->schedule_name;
        $data['update_time'] = date('Y-m-d H:i:s');//更新时间       
       
        $sql = "select update_time from vip_po_list where po_no='{$po_no}'";
        $exists = $model->db()->getRow($sql);
        try{
            if(!empty($exists)){
                $time_diff = time()-strtotime($exists['update_time']);
                if($time_diff<UPDATE_FRENQ){
                    continue;
                }
                //距离上次更新时间大于1小时以上，再次更新
                $sql = $model->updateSql("vip_po_list",$data,"po_no='{$po_no}'");
                $update_total++;
            }else{
                $sql = $model->insertSql($data,'vip_po_list');
                $insert_total ++;
            }
            $model->db()->query($sql);
        }catch (Exception $e){
            echo "vip_po_list同步异常：".$sql;
            //exit();
        }
        */
        if($po->not_pick>0){
            createPick($po_no);//创建拣货单
        }
    }
}
/**
 * 生成拣货单
 * @param unknown $po_no
 * @return boolean
 */
function createPick($po_no){
    global $deliveryService;
    try {        
        $pick_no_list = $deliveryService->createPick($po_no);
        if(!empty($pick_no_list)){
            $where = array('po_no'=>$po_no);
            $picklist = $deliveryService->getPickList($where,1,999);
            if(is_object($picklist) && !empty($picklist->picks)){
                updatePickList($picks);
            }
        }
    } catch(\Osp\Exception\OspException $e){
        echo $e->getMessage();
        return false;
    }
    return $pick_no_list;
}
