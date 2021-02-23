<?php
/**
 *  -------------------------------------------------
 *   @file		: OrderBatchFqcController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *
	质检订单号限制：已配货状态 未发货或已到店状态 才能质检
	质检通过：需要验证是否生成了销售单  发货状态改为允许发货
	质检未通过：销售单取消 配货状态改为配货中
 *  -------------------------------------------------
 */
class OrderBatchFqcController extends CommonController
{
	protected $smartyDebugEnabled = true;
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('order_batch_fqc_search_form.html',array(
			'bar'=>Auth::getBar(),
		));
	}
	function UpdateFqcStatus()
	{
		$result=array('error'=>'','success'=>0,'is_refresh'=>1);
		$order_sn=_Post::get('ids');		
		if(!empty($order_sn))
		{
			$ids=preg_split("/\n/",$order_sn);
			$error='';
			$i=0;$num=count($ids);
			if(!empty($ids))
			{
				foreach($ids as $id)
				{

					$re=$this->check_is_fqc($id);
					if($re!==true)
					{
						$error.=$re;
						continue;
					}
					$i++;
				}
				if($error=='')
				{
					$result['success']=1;
				}
				else
				{

					$result['error']='你选择了'.$num.'条记录，其中操作成功'.$i.'条<br />'.$error;
				}
			}
			else
			{
				$result['error']='请输入合法的订单号';
			}
		}
		else
		{
			$result['error']='请输入你要操作的记录';
		}
		Util::jsonExit($result);

	}
	public function check_is_fqc($order_sn)
	{     
        $saleModel = new SalesModel(27);
        $baseOrderInfo = $saleModel->getBaseOrderInfoByOrderSn($order_sn);
        if(empty($baseOrderInfo)){
            return $order_sn.":订单号不存在！<br />";
        }else if($baseOrderInfo['apply_return']==2){
        	return $order_sn.":有未完成的退款申请，不能操作<br />";
        }else if ($baseOrderInfo['apply_close']==1){
        	return $order_sn.":审核关闭状态，不能操作<br />";
        }else if ($baseOrderInfo['delivery_status'] != 5){//已配货 	5
        	return $order_sn.":配货状态错误，需要配货完成才可以质检<br />";
        }else if($baseOrderInfo['send_good_status'] == 4){
           return $order_sn.":订单已质检,不允许重复质检<br />";
        }else if (!($baseOrderInfo['send_good_status'] == 1 || $baseOrderInfo['send_good_status'] == 5)){//未发货 已到店	1
           return $order_sn.":发货状态错误，只有未发货和已到店才可以质检<br />";
        }
        
        $model_s = new WarehouseBillInfoSModel(22);
        $e_res   =$model_s->xiaoshou_exit($order_sn);
        if ($e_res<=0){//销售单不存在
           return $order_sn.":不存在销售单<br />";
        }		
		
		$baseOrderData = array('send_good_status'=>4);
		$res = $saleModel->updateBaseOrderInfo($baseOrderData,"order_sn='{$order_sn}'");
		if($res == false){
		    return $order_sn.":程序异常更新订单发货状态失败请联系开发人员<br />";
		}

		$newmodel =  new OrderFqcModel(22);
		$newdo=array("order_sn"=>$order_sn,'datatime'=>date('Y-m-d H:i:s'),'is_pass'=>1,'admin'=>$_SESSION['userName']);
		$res = $newmodel->saveData($newdo,array());
		if($res !== false)
		{
			$remark = 'FQC质检通过，允许发货';
		    $saleModel->AddOrderLog($order_sn,$remark);
		}
		else
		{
			return $order_sn.':保存质检记录失败！<br />';
		}
		return true;
	}

}

?>