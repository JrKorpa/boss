<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductApplyInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-17 16:30:13
 *   @update	:
 *  -------------------------------------------------
 */
class ProductApplyInfoController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
                //获取供应商列表
		$facmodel = new AppProcessorInfoModel(13);
		$process = $facmodel->getProList();
                //$this->getSourceList();
		$this->render('product_apply_info_search_form.html',array(
                    'bar'=>Auth::getBar(),
                    'process' => $process,
                    'dd'=>new DictView(new DictModel(1))
                ));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
                //$this->getSourceList();
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'order_sn'=>_Request::getString('order_sn'),
			'style_sn'=>_Request::getString('style_sn'),
			'bc_sn' => _Request::getString("bc_sn"),
			'processor' => _Request::getString("processor"),
			'time_start' => _Request::getString('time_start'),
			'time_end'   => _Request::getString('time_end'),
			'buchan_fac_opra' => _Request::getInt('buchan_fac_opra'),//订单生产状态
			'factory_status' => _Request::getInt("factory_status"),
			'apply_status' => _Request::getInt('apply_status'),
			'buchan_status' => _Request::getInt('buchan_status')
		);
		$page = _Request::getInt("page",1);
        $model = new ProductApplyInfoModel(13);
		$data = $model->pageList($args,$page,10,false);
		$newdata = $data['data'];
		$channel=$this->getChannelArr();
		if(!empty($data['data']))
		{
			foreach($newdata as $key => $val) {
				$data['data'][$key]['channel_id'] = isset($channel[$val['channel_id']])?$channel[$val['channel_id']]:'';
			}
		}
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'product_apply_info_search_page';
		$this->render('product_apply_info_search_list.html',array(
			'pa'=>Util::page($pageData),'dict'=>new DictView(new DictModel(1)),
			'page_list'=>$data,'view'=>new ProductApplyInfoView(new ProductApplyInfoModel(13)),
		));
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$m = new ProductApplyInfoModel($id,13);
		$view = new ProductApplyInfoView($m);
		$olddo = $view->get_old_info();
		$newdo = $view->get_apply_info();
		if($olddo[0]['code'] == 'id'){unset($olddo[0]);}

		$label = array_column($olddo,'name','code');
		$olddo = array_column($olddo,'value','code');
		$newdo = array_column($newdo,'value','code');
		$kezi=new Kezi();
		$olddo['kezi']=(isset($olddo['kezi']))?($kezi->retWord($olddo['kezi'])?$kezi->retWord($olddo['kezi']):$olddo['kezi']):$olddo['kezi'];
        $olddo['kezi']=$this->replaceTsKezi($olddo['kezi']);
		if(isset($newdo['kezi'])) 
		{
			$newdo['kezi']=(isset($newdo['kezi']))?($kezi->retWord($newdo['kezi']))?$kezi->retWord($newdo['kezi']):$olddo['kezi']:$olddo['kezi'];
            $newdo['kezi']=$this->replaceTsKezi($newdo['kezi']);
		}

		$this->render('product_apply_info_show.html',array(
			'view'=>$view,'dict'=>new DictView(new DictModel(1)),
			'bar'=>Auth::getViewBar(),'olddo'=>$olddo,'newdo'=>$newdo,
			'label'=>$label
		));

	}
	
	/**
	 * 审核通过
	 * 
	 */
	public function checkPass($params){
		$result = array('success' => 0,'error' => '');
		$id = _Request::getInt('id');
		$model = new ProductApplyInfoModel($id,14);		
		
		$salesModel = new CSalesModel(27);
		$opraLogModel = new ProductOpraLogModel(14);
		$peishiModel = new PeishiListModel(14);
		
		$view = new ProductApplyInfoView($model);
		$status = $view->get_apply_status();
		$detail_id = $view->get_detail_id();
		$bc_status = $view->get_goods_status();
		
		if($status!='0'){
			$result['error'] = "该信息已审核";
			Util::jsonExit($result);
		}		
		if($bc_status > 6 ){
			$result['error'] = "该货品已出产";
			Util::jsonExit($result);
		}
		//修改布产属性
		$bc_id = $view->getProInfoId();
		$style_sn = $view->get_style_sn();		
		$newAttr = $view->get_apply_info();//需要修改的布产属性
		$oldAttr = $view->get_attr($bc_id);//原始布产属性列表
		
		$pdolist[14] = $model->db()->db();
		$pdolist[27] = $salesModel->db()->db();
		//开启事物
	    foreach ($pdolist as $pdo){
	        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
	        $pdo->beginTransaction(); //开启事务
	    }
	    
	    $bc_zhengshuhao_changed = false;
	    $diy_dependency_changed = false;
		$res = $model->saveProductAttrData($bc_id,$newAttr,$oldAttr);
		if($res['success']==0){
			$error = "更新布产信息失败! error：".$res['error'];
			Util::rollbackExit($error,$pdolist);
		}else{				
    		$bc_zhengshuhao_changed = $res['data']['bc_zhengshuhao_changed'];
    		$diy_dependency_changed = $res['data']['bc_diy_dependency_changed'];
		}
		//回写订单商品信息 begin
		$attrKeyVal = array_column($newAttr,'value','code');
		if($detail_id >0){
    		$orderDetailsData = array();//订单明细表待回写数据
    		//订单商品允许回写的字段列表
    		$orderDetailsFields =array('cart'=>'主石单颗重','zhushi_num'=>'主石粒数','clarity'=>'主石净度','color'=>'主石颜色','zhengshuhao'=>'证书号','caizhi'=>'材质','jinse'=>'金色','jinzhong'=>'金重','xiangkou'=>'镶口','zhiquan'=>'指圈','kezi'=>'刻字','face_work'=>'表面工艺','xiangqian'=>'镶嵌要求','is_peishi'=>'是否支持4C配钻','cert'=>'证书类型');
    		foreach ($attrKeyVal as $k=>$v){    		    
    		    if(isset($orderDetailsFields[$k])){
    		        $orderDetailsData[$k] = $v;
    		    }
    		}
    		if(!empty($orderDetailsData)){    		    
    			$res = $salesModel->updateAppOrderDetails($orderDetailsData,'id='.$detail_id);
    			if($res === false){
    				$error = "回写订单商品明细失败！".$res;
    				Util::rollbackExit($error,$pdolist);
    			}
    		}
		}
		//回写订单商品信息 end

		
		$model->setValue('apply_status',1);
		$model->setValue('check_id',$_SESSION['userId']);
		$model->setValue('check_name',$_SESSION['realName']);
		$model->setValue('check_time',date('Y-m-d H:i:s'));
		$res = $model->save(true);
		if($res === false){
		    $error = "操作失败！提示：更新审核状态失败";
		    Util::rollbackExit($error,$pdolist);
		}

		//增加修改日志
		$log  = $model->getProductLog();
		$newInfo = $view->get_apply_info();
		$oldInfo = $view->get_old_info();
		$newInfo = array_column($newInfo,'value','name');
		$oldInfo = array_column($oldInfo,'value','name');
		$c_info = '';
		foreach ($newInfo as $key=>$vo){
		    if(isset($oldInfo[$key]) && $vo<>$oldInfo[$key]){
		        $c_info .= "[{$key}]由【{$oldInfo[$key]}】改为【{$vo}】<br/>";
		    }
		}
		$log = ($c_info)?"修改属性：".$c_info:"无修改信息";
		$res = $opraLogModel->addLog($bc_id,"布产单审核通过，".$log);		
		if($res){
			$pro_model = new ProductInfoModel(14);
			$pro_model->Writeback($bc_id, "布产单审核通过:".$log);	//回写订单操作日志 BY hulichao
		}else{
			$error = "写入日志失败";
			Util::rollbackExit($error,$pdolist);
		}
		
		//更新布产主石，副石信息
		$styleModel = new CStyleModel(11);
		$oldAttr2 = $view->get_attr($bc_id);//重新获取布产属性所有属性
		$newAttr2 = $styleModel->getStoneAttrList($style_sn,$oldAttr2);
		$res = $model->saveProductAttrData($bc_id, $newAttr2, $oldAttr2);
		if($res['success']==0){
		    $error = "操作失败:同步主石，副石信息失败。".$res['error'];
		    Util::rollbackExit($error,$pdolist);
		}
		$res = $peishiModel->createPeishiList($bc_id,'update',"布产单修改");
		if($res['success']==0){
		    $error = "操作失败:同步布产单关联的配石单失败。".$res['error'];
		    Util::rollbackExit($error,$pdolist);
		}
		//$error = "test！".$log;
		//Util::rollbackExit($error,$pdolist);
	    try{	
            //批量提交事物
            foreach ($pdolist as $pdo){
                $pdo->commit();
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
            }
            
        }catch (Exception $e){
            $error = "操作失败，事物回滚！";
            Util::rollbackExit($error,$pdolist);
        }      	
    	
    	if ($bc_zhengshuhao_changed !== false) {
    		//AsyncDelegate::dispatch('buchan', array('event' => 'certId_changed', 'bc_id' => $bc_id, 'zhengshuhao' => $bc_zhengshuhao_changed, 'userId' => $_SESSION['userId'], 'userName' => $_SESSION['userName']));
    	}else if($diy_dependency_changed) {
    		if (!empty($detail_id)) {
    			//AsyncDelegate::dispatch('buchan', array('event' => 'order_bcd_upserted', 'bc_infos' => array($bc_id => $detail_id), 'reason' => '订单布产信息修改', 'userId' => $_SESSION['userId'], 'userName' => $_SESSION['userName']));
    		}
    	}
    	$result['success'] = 1;
		Util::jsonExit($result);
	}
		
	public function checkOut($params){

		$id = _Post::getInt('id');
		$model = new ProductApplyInfoModel($id,14);
		$view = new ProductApplyInfoView($model);

		$pro_model = new ProductInfoModel(13);
		$bc_sn = $view->get_bc_sn();
		$bc_id = $pro_model->Select2($fields=' `id` ' , $where=" `bc_sn` = '{$bc_sn}' " , $type = 'one');

		$status = $view->get_apply_status();
		if($status!='0'){echo "该信息已审核";exit;}
		$remark = _Post::getString('remark');
		if(empty($remark)){
			echo '请填写拒绝理由!!!';
		}
		$model->setValue('refuse_remark',$remark);
		$model->setValue('check_id',$_SESSION['userId']);
		$model->setValue('check_name',$_SESSION['realName']);
		$model->setValue('check_time',date('Y-m-d H:i:s'));
		$model->setValue('apply_status',2);
		$res = $model->save(true);

		//$pro_model->Writeback($bc_id, "布产审核驳回:".$remark);	//回写订单操作日志 BY hulichao

		echo ($res)?'1':'0';

	}


	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new ProductApplyInfoModel($id,14);
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

}

?>