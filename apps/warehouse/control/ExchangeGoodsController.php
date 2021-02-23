<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *
	质检订单号限制：已配货状态 未发货状态 才能质检
	质检通过：需要验证是否生成了销售单  发货状态改为允许发货
	质检未通过：销售单取消 配货状态改为配货中
 *  -------------------------------------------------
 */
class ExchangeGoodsController extends CommonController
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
		$this->render('exchange_goods_search_form.html',array(
			'bar'=>Auth::getBar(),
		));
	}

	/**
	 *	search，列表 显示订单信息
	 */
	public function search ($params)
	{
		$order_sn = _Post::getString('order_sn');
		$keys=array('order_sn');
		$vals=array($order_sn);
		$orderInfo=ApiModel::sales_api('GetOrderInfo',$keys,$vals);
		if(empty($orderInfo['return_msg'])){
			$html ="<div class='alert alert-info'>订单号".$order_sn."不存在，请重新输入</div>";
			Util::jsonExit($html);
		}
		$goodsInfo=ApiModel::sales_api('GetOrderInfoByOrdersn',$keys,$vals);
		$data = $orderInfo['return_msg'];
		$goods_data = $goodsInfo['return_msg']['data'];

		#订单的配货状态是允许配货和配货中的
		if (empty($data))
		{
			$html ="<div class='alert alert-info'>订单号".$order_sn."不存在，请重新输入</div>";
			Util::jsonExit($html);
		}
		//已配货 	5  暂时先把未配货的放开 ---JUAN 因为老系统入的货 输入的布产号没办法读
		if ($data['delivery_status'] != 1 && $data['delivery_status'] != 2 && $data['delivery_status'] != 3)
		{
			$html ="<div class='alert alert-info'>订单号".$order_sn."配货状态错误，允许配货和配货中的货品才可在此处换货！</div>";
			Util::jsonExit($html);
		}

		$departmentModel = new DepartmentModel(1);
		$departmentname = $departmentModel->getNameById($data['department_id']);
		$data['order_source'] = $departmentname ;
		$this->render('exchange_goods_search_list.html',array(
				'data'=>$data,
				'goods_data' => $goods_data,
				'dd'=>new DictView(new DictModel(1)),
				'bar'=>Auth::getBar()
			));
	}

	//changegoods 换货
	public function changegoods ()
	{
		$result = array('success' => 0,'error' =>'');
		// var_dump($_REQUEST);exit;
		$details_info =_Request::get('details_info');
		$details_info = explode('|', $details_info);
		//获取货品  订单详情ID 和 款号
		$details_id = $details_info[0];
		//获取新货号
		$change_goods_id =_Request::get('change_goods_id');
		//获取换货原因
		$change_reason =_Request::get('change_reason');
		//获取订单sn
		$order_sn =_Request::get('order_sn');

		$warehouseGoodsModel = new WarehouseGoodsModel(21);
		$salesModel = new SalesModel(27);
		$proccesorModel = new SelfProccesorModel(13);		
		
		//查询订单明细商品基本信息
		$order_detail = $salesModel->getAppOrderDetailsById($details_id);
		if(empty($order_detail)){
		    $result['error'] = '查询原订单商品信息失败！';
		    Util::jsonExit($result);
		}else{
		    $goods_sn = $order_detail['goods_sn'];
		    $zhengshuhao= strtoupper(preg_replace('/\s/is','',$order_detail['zhengshuhao']));
		    $bc_id  = $order_detail['bc_id'];
		}
		
		$goods_data = $warehouseGoodsModel->getGoodsByGoods_id($change_goods_id);
		if(empty($goods_data)){
			$result['error'] = '新输入的货号不存在！';
			Util::jsonExit($result);
		}
		//证书号去空格回车
		$goods_data['zhengshuhao'] = strtoupper(preg_replace('/\s/is','',$goods_data['zhengshuhao']));
		//新输入的货号必须是有效的库存货号
		if($goods_data['is_on_sale']!=2){
			$result['error'] = '新输入的货号不是库存状态,不可以换货！';
			Util::jsonExit($result);
		}
		//新输入的货号已经绑定
		if($goods_data['order_goods_id']){
		    if($goods_data['order_goods_id']!=$details_id){
		        $result['error'] = '新输入的货号已经被其他订单绑定,不可以换货！';
		        Util::jsonExit($result);
		    }else{
		        $result['error'] = '订单已经绑定过此货号,请勿重复绑定！';
		        Util::jsonExit($result);
		    }
		}
		
		//输入货号必须是所选的货品同一个款的，不是同一个不让换。
		if($goods_data['cat_type1'] == '裸石' || $goods_data['cat_type'] == '裸石' || $goods_data['cat_type1'] == '彩钻'  || $goods_data['cat_type'] == '彩钻')
		{

			if(preg_replace('/[A-Za-z.-]/','',$zhengshuhao) <> preg_replace('/[A-Za-z.-]/','',preg_replace('/\s/is','',$goods_data['zhengshuhao'])))
			{
				$result['error'] = '新输入的货号证书号和所下订单证书号不一致，不可以换货！';
				Util::jsonExit($result);
			}
		}else{
			if(strtoupper(trim($goods_data['goods_sn']))!= strtoupper(trim($goods_sn))){
				if(strtoupper($order_detail['goods_sn'])=="QIBAN" && $order_detail['bc_id']<>""){
				    /*TODO: 对于同一个系统而言，bc_id是唯一的，因布产单前缀字母不定，仅比较数字*/
					if($order_detail['bc_id'] != preg_replace('/^[a-zA-Z]+/', '', $goods_data['buchan_sn'])) {
						$result['error'] = '新输入的货号与原货品不是同一款式,不可以换货！';
				        Util::jsonExit($result);	
					}
				}else{
				    $result['error'] = '新输入的货号与原货品不是同一款式,不可以换货！';
				    Util::jsonExit($result);
				}    
			}
		}
		
		//开始事物
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
		//如果订单原来已经绑定过，就解绑
		$unBindRemark = "";	
	    $bind_goods = $warehouseGoodsModel->select2('id,goods_id,order_goods_id',"order_goods_id ='{$details_id}'",2);	
	    if(!empty($bind_goods['order_goods_id'])){
	        
	        $unBindRemark = ",原货号{$bind_goods['goods_id']}自动解绑";
	        
	        $data = array('order_goods_id'=>'');
	        $res = $warehouseGoodsModel->update($data,"order_goods_id='{$details_id}'");
	        if(!$res){
	            $error ="操作失败,事物回滚!提示：解绑原货号失败";
	            Util::rollbackExit($error,$pdolist);
	        }
	        
	    }
        
	    //绑定新货号
	    $data = array('order_goods_id'=>$details_id);
	    $res = $warehouseGoodsModel->update($data,"goods_id='{$change_goods_id}'");
		if(!$res){
		    $error ="操作失败,事物回滚!提示：绑定新货号失败";
		    Util::rollbackExit($error,$pdolist);
	    }
	    //更新新货号到订单商品
	    $data = array('goods_id'=>$change_goods_id);
	    $res = $salesModel->updateAppOrderDetail($data,"id='{$details_id}'");
	    if(!$res){
	        $error ="操作失败,事物回滚!提示：同步新货号到订单商品失败";
	        Util::rollbackExit($error,$pdolist);
	    }
	    //订单日志
	    $orderLogRemark = '货品【'.$order_detail['goods_id'].'】已换货成【'.$change_goods_id.'】'.$unBindRemark;
        $res = $salesModel->AddOrderLog($order_sn,$orderLogRemark); 
        if(!$res){
            $error ="操作失败,事物回滚!提示：订单日志写入失败";
            Util::rollbackExit($error,$pdolist);
        }        
	    //添加布产单日志(如果有布产单)
        if($bc_id>0){
            $buchanRemark = $orderLogRemark;//默认跟订单日志内容相同
            $proccesorModel->addBuchanOpraLog($bc_id,$buchanRemark);
        }
        
        //Util::rollbackExit("测试",$pdolist);
        
		//改变可销售商品上架状态 参数 货号 状态(0:下架 1:上架) 商品是否有效
		$policyModel = new ApiSalepolicyModel();
		$policyModel->EditIsSaleStatus(array($change_goods_id) , 0 , 2);
	
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