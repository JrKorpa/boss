<?php
/**
 * 订单批量换货控制器
 *  -------------------------------------------------
 *   @file		: WarehouseBillController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2015-10-19
 *   @update	:
 *
	质检订单号限制：已配货状态 未发货状态 才能质检
	质检通过：需要验证是否生成了销售单  发货状态改为允许发货
	质检未通过：销售单取消 配货状态改为配货中
 *  -------------------------------------------------
 */
class BatchExchangeGoodsController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }		
		$this->render('batch_exchange_goods_search_form.html',array(
			'bar'=>Auth::getBar(),
		));
	}
	
	/**
	 * 过滤提交的批量订单号
	 */
	private function _getOrderSnList($params){
	    
	    $result = array('success'=>'','error'=>'','search_logs'=>'','content'=>'');
 	    if(empty($params['order_sn'])){
	        $result['search_logs'] = "批量订单ID为空";
	        Util::jsonExit($result);
	    }
	
	    $order_sn_split = explode("\n",$params['order_sn']);
	     
	    //获取最终合法订单编号
	    $order_sn_list = array();
	    foreach($order_sn_split as $vo){
	        //获取不为空和不重复的订单id
	        if(trim($vo)!='' && !in_array($vo,$order_sn_list)){
	            $order_sn_list[]=trim($vo);
	        }
	    }
	    if(empty($order_sn_list)){
	        $result['search_logs'] = "批量合法订单ID为空";
	        Util::jsonExit($result);
	    }
	    return $order_sn_list;
	}
	/**
	 * 搜索
	 */
	public function search($params){
	    
	    $result = array('success'=>'','error'=>'','search_logs'=>'','content'=>'');	    
	    $order_sn_list = $this->_getOrderSnList($params);
	    
	    $salesModel = new SalesModel(27);
	    $warehouseGoodsModel = new WarehouseGoodsModel(21);
	    $errors     = array();
	    $goods_list = array();
	    foreach($order_sn_list as $key=>$order_sn){
	        $error = false;
	        $baseOrderInfo = $salesModel->getBaseOrderInfoByOrderSn($order_sn);
	        if(empty($baseOrderInfo)){
	            $logs[$order_sn][] = array('msg'=>"订单不存在",'type'=>'error');
	            continue;
	        }
            //符合换货条件的订单：未关闭、未申请关闭、没有进行中的退款流程、非已配货、非已发货，
	        $order_id        = $baseOrderInfo['id'];
            $delivery_status = $baseOrderInfo['delivery_status'];
            $apply_close     = $baseOrderInfo['apply_close'];
            $apply_return    = $baseOrderInfo['apply_return'];
            $order_status    = $baseOrderInfo['order_status'];
            //0未操作,1正在退款已申请关闭
            if(!empty($apply_close)){
                $error = true;
                $logs[$order_sn][] = array('msg'=>"订单已申请关闭",'type'=>'error');
            }
            //订单已关闭
            if($order_status==4){
                $error = true;
                $logs[$order_sn][] = array('msg'=>"订单已关闭",'type'=>'error');
            }
            //1未操作,2正在退款
            if($apply_return!=1){
                $error = true;
                $logs[$order_sn][] = array('msg'=>"订单正在退款",'type'=>'error');
            }
            //无效6,已配货5,配货缺货4,配货中3,允许配货2,未配货1
            if($delivery_status==5){
                $error = true;
                $logs[$order_sn][] = array('msg'=>"订单已配货",'type'=>'error');
            }
            if($error==true){
                continue;
            }else{
                $logs[$order_sn][] = array('msg'=>"查询成功!",'type'=>'success');
            }
            $data = $salesModel->getAppOrderDetailsByOrderId($order_id);
            foreach($data as $k=>$v){
                $bind_goods_id = $warehouseGoodsModel->select2('goods_id',"order_goods_id='{$v['id']}'");
                $v['order_sn']  = $order_sn;
                $v['bind_goods_id']  = $bind_goods_id;
                $goods_list[] = $v;                
            }
            
	    }
        $search_logs = "查询成功!";
	    $search_logs = "订单总计:".count($logs)."<hr/>";
	    $i = 0;
        foreach($logs as $key=>$vo1){
             $i++;
             $search_logs.="{$i}.订单{$key}：";
             foreach ($vo1 as $vo2){
                 if($vo2['type']=="error"){
                     $search_logs .='【<font color="red">'.$vo2['msg'].'</font>】,';
                 }else{
                     $search_logs .= $vo2['msg'];
                 }
             }
             $search_logs =trim($search_logs,',')."<hr/>";
        }
        $result['success'] = 1;
        $result['search_logs'] = $search_logs;
	    $result['content'] = $this->fetch('batch_exchange_goods_search_list.html',array(
            'goods_list' =>$goods_list,
        ));	    
	    Util::jsonExit($result);
	}
	/**
	 * 批量换货处理
	 */
	public function exchangeGoods($params){
	    $result = array('success'=>'','error'=>'');
	    $reason_arr       = _Request::getList('reason_arr');
	    $new_goods_id_arr = _Request::getList('new_goods_id_arr');

	    $warehouseGoodsModel = new WarehouseGoodsModel(21);
	    $salesModel = new SalesModel(27);
	    $proccesorModel = new SelfProccesorModel(13);
	    $apiSalepolicyModel = new ApiSalepolicyModel(27);
	    
	    $pdolist[13] = $proccesorModel->db()->db();
	    $pdolist[27] = $salesModel->db()->db();
	    $pdolist[21] = $warehouseGoodsModel->db()->db();
        try{
    	    foreach ($pdolist as $pdo){
    	        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
    	        $pdo->beginTransaction(); //开启事务
    	    }
        }catch (Exception $e){
            $error = "操作失败，事物回滚！提示：系统批量开启事物时发生异常！";
            Util::rollbackExit($error,$pdolist);
        }  
        $i = 0;
	    foreach ($new_goods_id_arr as $details_id=>$new_goods_id){
	        $i ++;
	        //货号为空的，略过
	        if($new_goods_id==""){
	            continue;
	        }
	        $detail_data = $salesModel->getAppOrderDetailsById($details_id);
	        $order_data  = $salesModel->getOrderInfoByDetailId($details_id);
	        
	        $error_line  = "序号为【{$i}】的行";
	        if(empty($detail_data) || empty($order_data)){
	            $error = $error_line.',原始货号查询失败！';
	            Util::rollbackExit($error,$pdolist);
	        }else{
	            $goods_sn   = $detail_data['goods_sn'];
	            $goods_id   = $detail_data['goods_id'];
	            $zhengshuhao= strtoupper(preg_replace('/\s/is','',$detail_data['zhengshuhao']));
	            $bc_id      = $detail_data['bc_id'];
	            $order_sn   = $order_data['order_sn'];	            
	        }
	        //新货号与原始货号相同的，不用再换货
	        /* if($goods_id == $new_goods_id){
	            continue;
	        } */
	        $goods_data  = $warehouseGoodsModel->getGoodsByGoods_id($new_goods_id);
	        if(empty($goods_data)){
	           $error = $error_line.',新输入的货号【'.$new_goods_id.'】库存中不存在！';
	           Util::rollbackExit($error,$pdolist);
	        }
	        //证书号去空格回车
	        $goods_data['zhengshuhao'] = strtoupper(preg_replace('/\s/is','',$goods_data['zhengshuhao']));
	        //新输入的货号必须是有效的库存货号
	        if(!empty($goods_data['order_goods_id'])){
	            //已经绑定过的且绑定货号没有发生改变的，不用换货
	            if($details_id == $goods_data['order_goods_id']){
	                continue;
	            }
	            $error = $error_line.',新输入的货号已经被其他订单绑定,不可以换货！';
	            Util::rollbackExit($error,$pdolist);
	        }
	        //新输入	的货号必须是有效的库存货号
	        if($goods_data['is_on_sale']!=2){
	            $error = $error_line.',新输入的货号不是库存状态,不可以换货！';
	            Util::rollbackExit($error,$pdolist);
	        }
	        //输入货号必须是所选的货品同一个款的，不是同一个不让换
	        if($goods_data['cat_type1'] == "裸石" || $goods_data['cat_type1'] == "彩钻" || $goods_data['cat_type'] == "裸石" || $goods_data['cat_type'] == "彩钻")
	        {   
	            if(!(strpos($zhengshuhao,$goods_data['zhengshuhao']) || $goods_data['zhengshuhao'] == $zhengshuhao))
	            {
	                $error = $error_line.',新输入的货号证书号和所下订单证书号不一致，不可以换货！';
	                Util::rollbackExit($error,$pdolist);
	            }
	        }else{
	            if(strtoupper(trim($goods_data['goods_sn']))!= strtoupper(trim($goods_sn))){
	                $error = $error_line.',新输入的货号与原货品不是同一款式,不可以换货！';
	                Util::rollbackExit($error,$pdolist);
	            }
	        }
	        //原货号解绑（$goods_id）,先查询原货号是否绑定订单，如果有绑定，先解绑
	        $unBindRemark = "";
	        if(!empty($goods_id)){
    	        $res = $warehouseGoodsModel->select2('id,goods_id,order_goods_id',"goods_id='{$goods_id}'" ,2);
       	        if(!empty($res['order_goods_id'])){
        	        $data = array('order_goods_id'=>'');
        	        $res = $warehouseGoodsModel->update($data,"goods_id='{$goods_id}'");
        	        if(!$res){
        	            $error = $error_line.',原货号【{$goods_id}】解绑失败！';
        	            Util::rollbackExit($error,$pdolist);
        	        }
        	        $unBindRemark = ",原货号{$goods_id}自动解绑";
    	        }
	        }
	        
	        //绑定新货号($new_goods_id)
	        $data = array('order_goods_id'=>$details_id);
	        $res = $warehouseGoodsModel->update($data,"goods_id='{$new_goods_id}'");
    		if(!$res){
    		    $error = $error_line.',绑定新货号失败！';
    		    Util::rollbackExit($error,$pdolist);
    		}
    		
    		//同步信息到订单商品表
    		$data = array(
    		    'goods_id'=>$new_goods_id,//更新新货号   
    		);
    		$res = $salesModel->updateAppOrderDetail($data,"id='{$details_id}'");
    		if(!$res){
    		    $error = $error_line.',同步新货号更新失败！';
    		    Util::rollbackExit($error,$pdolist);
    		}
    		//添加订单操作日志
    		$bindRemark = '批量换货操作：货品【'.$goods_id.'】已换货成'.$new_goods_id;
    		$orderLogRemark = $bindRemark.$unBindRemark;
            $res = $salesModel->AddOrderLog($order_sn,$orderLogRemark);
            if(!$res){
                $error = $error_line.',订单日志写入失败！';
                Util::rollbackExit($error,$pdolist);
            }
            //添加布产单日志(如果有布产单)
            if($bc_id>0){
                $buchanRemark = $orderLogRemark;//默认跟订单日志相同
                $proccesorModel->addBuchanOpraLog($bc_id,$buchanRemark);
            }
    		//改变可销售商品上架状态 参数 货号 状态(0:下架 1:上架) 商品是否有效
    		$res = $apiSalepolicyModel->EditIsSaleStatus(array($new_goods_id) , 0 , 2);
	        if(!$res){
	            $error = $error_line.',还原商品上架状态失败！';
	            Util::rollbackExit($error,$pdolist);
	        }
	    }
	    
	    if($i==0){
	        $error = '数据表单为空！';
	        Util::rollbackExit($error,$pdolist);
	    }	    
	    try{	
            //批量提交事物
            foreach ($pdolist as $pdo){
                $pdo->commit();
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
            }
            $result['success'] = 1;
            Util::jsonExit($result);
            
        }catch (Exception $e){
            $error = "操作失败，事物回滚！提示：系统批量提交事物时发生异常！";
            Util::rollbackExit($error,$pdolist);
        }     
	    
	    
	}
	
}

?>