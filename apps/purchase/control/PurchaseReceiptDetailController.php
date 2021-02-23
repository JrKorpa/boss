<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseReceiptDetailController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-15 21:23:04
 *   @update	:
 *   收货单详情，也是待质检列表详情。
 *  -------------------------------------------------
 */
class PurchaseReceiptDetailController extends Controller
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{

		$Processor_Info = new ApiProcessorModel();
		$Processor_list = $Processor_Info->GetSupplierList();
		$this->render('purchase_receipt_detail_search_form.html',array(
			'bar' => Auth::getBar(),
			'dd'  => new DictView(new DictModel(1)),
			'Processor_list'=>$Processor_list

		));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'purchase_receipt_id' => _Request::get("purchase_receipt_id"),
			'ship_num'		=> _Request::get("ship_num"),
			'prc_id'		=> _Request::get("prc_id"),
			'purchase_sn'	=> _Request::get("purchase_sn"),
			'bc_sn'			=> _Request::get("bc_sn"),
			'style_sn'		=> _Request::get("style_sn"),
			'customer_name' => _Request::get("customer_name"),
			'status'		=> _Request::get("status"),
            'page_size'		=> _Request::get("page_size")
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
        $page_size = isset($_REQUEST["page_size"]) ? intval($_REQUEST["page_size"]) : 10 ;
		$where = array(
			'purchase_receipt_id' => $args['purchase_receipt_id'],
			'ship_num'		=> $args['ship_num'],
			'prc_id'		=> $args['prc_id'],
			'purchase_sn'	=> $args['purchase_sn'],
			'bc_sn'			=> $args['bc_sn'],
			'style_sn'		=> $args['style_sn'],
			'customer_name' => $args['customer_name'],
			'status'		=> $args['status'],
		);
        if(SYS_SCOPE == 'zhanting'){
            $where['hidden'] = '0';
        }

		$model = new PurchaseReceiptDetailModel(23);
		$data = $model->pageList($where,$page,$page_size,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'purchase_receipt_detail_search_page';
		$this->render('purchase_receipt_detail_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'dd'  => new DictView(new DictModel(1))
		));
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$model = new PurchaseReceiptDetailModel($id,23);
        $SalesModel = new SalesModel(27);
        $WarehouseModel = new WarehouseModel(21);
		$info_detail = array();
		$info = array();

		//如果有布产号，调用接口，取布产单详情
		if($model->getValue('bc_sn'))
		{
			//调用布产列表接口
			$apiModel = new ApiProcessorModel();
			$buchan_info = $apiModel->GetProductInfoDatail($model->getValue('bc_sn'));
			$info = isset($buchan_info['info']) ? $buchan_info['info'] : array();
			$info_detail = isset($buchan_info['info_detail']) ? $buchan_info['info_detail'] : array();
			//var_dump($buchan_info);exit;
            //boss-1113主钻副钻自动带出效果
            if(!empty($info) && $info['from_type'] == 2){
                $attrinfo = $SalesModel->getOrderAttrInfoByBc_sn($info);
                if(!empty($attrinfo) && $attrinfo['ext_goods_sn'] != ''){
                    $checkgoods = $WarehouseModel->checkGoodsByGoods_id($attrinfo['ext_goods_sn']);
                    if(!empty($checkgoods)){
                        if($checkgoods['fushizhong']>0.01 || $checkgoods['shi2zhong']>0.01){
                            $fushizhong = !empty($checkgoods['fushizhong'])?$checkgoods['fushizhong']:'0.00';
                            $fushilishu = !empty($checkgoods['fushilishu'])?$checkgoods['fushilishu']:'0';
                            $fushi2zhong = !empty($checkgoods['shi2zhong'])?$checkgoods['shi2zhong']:'0.00';
                            $fushi2lishu = !empty($checkgoods['shi2lishu'])?$checkgoods['shi2lishu']:'0';
                            $attr_string = $fushizhong."*CT/".$fushilishu."*P|".$fushi2zhong."*CT/".$fushi2lishu."*P";
                            $info_detail[] = array('name'=>'副石信息','value'=>$attr_string);
                        }
                    }
                }
            }
            //--------------------end
		}
        //echo '<pre>';
        //print_r($info_detail);die;
		
		// 获取款式图片
		if (!isset($apiModel)) $apiModel = new ApiProcessorModel();
		$image_list = $apiModel->getStyleGalleryList($model->getValue('style_sn'));
		//var_dump($image_list);exit;

		//调用IQC质检情况
		$iqcModel = new PurchaseIqcOpraModel(23);
		$iqc_list = $iqcModel->getiqcList($id);
		$this->render('purchase_receipt_detail_show.html',array(
			'view'		=>new PurchaseReceiptDetailView($model),
			'dd'		=> new DictView(new DictModel(1)),
			'buchan'	=> $info,
			'detail'	=> $info_detail,
			'iqc_list'	=> $iqc_list,
			'iqc_cnt'	=> count($iqc_list),
			'image_list'=> $image_list,
			'bar' => Auth::getViewBar(),
		));
	}


	//通过流水号向excel返回已经通过质检的货品--shuaishuai
	public function outGoods(){
		$res = array("error" => "0", "msg" => "");
		//判断该流水号是否存在
		$purchase_receipt_id = _Get::getInt('purchase_receipt_id');
		$model = new PurchaseReceiptDetailModel(23);
		$rest = $model->getPurchaseReceiptIdExsist($purchase_receipt_id);

		if(!$rest){
			$res = array("error" => "1", "msg" => "没有查到对应的出货单");
			$res =$this->arrtoxml($res);
			echo $res;
			exit;
			}
		if($rest['status']!=2){
			$res = array("error" => "1", "msg" => "出货单未审核");
			$res =$this->arrtoxml($res);
			echo $res;
			exit;
		}

		//去取物品
		$rest = $model->getPurchaseReceiptDetails($purchase_receipt_id,3);
		if(count($rest))
		{
			$res = array("error" => "1", "msg" => "有未质检的货品");
			$res =$this->arrtoxml($res);
			echo $res;
			exit;
		}
		$rest = $model->getPurchaseReceiptDetails($purchase_receipt_id,4);
		if(!$rest){
			$res = array("error" => "1", "msg" => "这个单号不存在质检通过商品");
			$res =$this->arrtoxml($res);
			echo $res;
			exit;
		}


		$res['goods_list'] = $rest;
		$res = $this->arrtoxml($res);
		echo $res;
		exit;

	}


	//对数组进行xml处理 ---shuaishuai
	private function arrtoxml($arr,$dom=0,$item=0){
		if (!$dom){
			$dom = new DOMDocument("1.0");
		}
		if(!$item){
			$item = $dom->createElement("root");
			$dom->appendChild($item);
		}
		foreach ($arr as $key=>$val){
			$itemx = $dom->createElement(is_string($key)?$key:"item");
			$item->appendChild($itemx);
			if (!is_array($val)){
				$text = $dom->createTextNode($val);
				$itemx->appendChild($text);

			}else {
				$this->arrtoxml($val,$dom,$itemx);
			}
		}
		return $dom->saveXML();
	}




	/**
	 *	showLog 质检列表页面查看日志按钮操作方法
	 */
	public function showLog ($params)
	{
		$id = intval($params["id"]);
		$model = new PurchaseReceiptDetailModel($id,23);
		$bc_sn = $model->getValue('bc_sn');
		$bc_log = array();

		//有布产号的要去调用布产时候的log展示在采购收货log之前
		if($bc_sn)
		{
			$apimodel = new ApiProcessorModel();
			$bc_log = $apimodel->getBcLog($bc_sn);
		}
		$logmodel = new PurchaseLogModel($id,23);
		$result['content'] = $this->fetch('purchase_log_show.html',array(
			'bc_log' => $bc_log,
			'list' => $logmodel->getLog($id),
			'dd' => new DictView(new DictModel(1))
		));
		$result['title'] = '日志详情';
		Util::jsonExit($result);
	}



/**
	 *	add，渲染添加页面
	 */
	public function addIqc ($params)
	{
		$id = $params['id'];
		$result = array('success' => 0,'error' => '');
		$model = new PurchaseReceiptDetailModel($id,23);
		//若 状态不是IQC未过 判断当前状态
	
		if($model->getValue('status') != 3 && $model->getValue('status') != 5)
		{
			$result['content'] = "非待质检状态，不能操作。";
			Util::jsonExit($result);
		}
			
		
		
		$result['content'] = $this->fetch('purchase_iqc_opra_info.html',array(
			'dd'  => new DictView(new DictModel(1)),
			'rece_detail_id' => $id
		));
		$result['title'] = '质检操作';
		Util::jsonExit($result);
	}


	/**
	 *	insert，信息入库
	 */
	public function insertIqc ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$opra_code	= _Post::getInt('opra_code');
		$opra_info	= _Post::get('opra_info');
		$rece_detail_id = _Post::get('rece_detail_id');

		if(!$opra_code)
		{

			$result['error'] = '质检操作必选';
			Util::jsonExit($result);
		}

		$olddo = array();
		$newdo=array(
			'rece_detail_id'	=> $rece_detail_id,
			'opra_code'	=> $opra_code,
			'opra_info'	=> $opra_info,
			'opra_uname'=> $_SESSION['userName'],
			'opra_time' => date('Y-m-d H:i:s')
		);
		$newmodel =  new PurchaseIqcOpraModel(24);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			//质检操作id => 对应的货品状态
			$s = array(
				'1' => '4',
				'2' => '2',
				'3' => '5'
			);
			$model = new PurchaseReceiptDetailModel($rece_detail_id,24);
			$model->setValue('status',$s[$opra_code]);
			if($model->save() !==false)
			{
				$result['success'] = 1;
				//记录流水日志
				$logModel = new PurchaseLogModel(24);
				$dd = new DictModel(1);
				$remark = $opra_info?$opra_info:"无";
				$opra_act = $dd->getEnum('iqc_opra',$opra_code);
				$logModel->addLog($rece_detail_id,$s[$opra_code],"IQC质检操作：".$opra_act."，备注：".$remark);
			}else{
				$result['error'] = '操作失败';
			}
		}
		else
		{
			$result['error'] = '操作失败';
		}
		Util::jsonExit($result);
	}
	
	
	/**
	 *	bacth_addIqc，渲染添加页面
	 */
	public function bacth_addIqc ($params)
	{
		$ids_arr = $params['ids'];
		$ids_str =implode(',', $ids_arr);
		$result = array('success' => 0,'error' => '');
		/* $model = new PurchaseReceiptDetailModel($id,23);
	
		if($model->getValue('status') != 3 && $model->getValue('status') != 5)
		{
			$result['content'] = "非待质检状态，不能操作。";
			Util::jsonExit($result);
		} */
			
	
	
		$result['content'] = $this->fetch('purchase_batch_iqc_opra_info.html',array(
				'dd'  => new DictView(new DictModel(1)),
				'ids' => $ids_str
		));
		$result['title'] = '质检操作';
		Util::jsonExit($result);
	}
	//批量iqc质检操作
	public function batch_insertIqc ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$opra_code	= _Post::getInt('opra_code');
		$opra_info	= _Post::get('opra_info');
		$rece_detail_id = _Post::get('rece_detail_id');
		$rece_detail_id_arr =explode(',', $rece_detail_id);
		//判断状态是否符合条件  都是带质检状态才可以批量操作
		foreach($rece_detail_id_arr as $val){
				$model = new PurchaseReceiptDetailModel($val,23);
				if($model->getValue('status') != 3 && $model->getValue('status') != 5)
				{
					$result['error'] = "流水号".$val."非待质检状态，不能批量操作。";
					Util::jsonExit($result);
				}
		}
		foreach($rece_detail_id_arr as $val){
			
			
			if(!$opra_code)
			{			
				$result['error'] = '质检操作必选';
				Util::jsonExit($result);
			}
			$olddo = array();
			$newdo=array(
					'rece_detail_id'	=> $val,
					'opra_code'	=> $opra_code,
					'opra_info'	=> $opra_info,
					'opra_uname'=> $_SESSION['userName'],
					'opra_time' => date('Y-m-d H:i:s')
			);
			$newmodel =  new PurchaseIqcOpraModel(24);
			$res = $newmodel->saveData($newdo,$olddo);
			if($res !== false)
			{
				//质检操作id => 对应的货品状态
				$s = array(
						'1' => '4',
						'2' => '2',
						'3' => '5'
				);
				$model = new PurchaseReceiptDetailModel($val,24);
				$model->setValue('status',$s[$opra_code]);
				if($model->save())
				{
					$result['success'] = 1;
					//记录流水日志
					$logModel = new PurchaseLogModel(24);
					$dd = new DictModel(1);
					$remark = $opra_info?$opra_info:"无";
					$opra_act = $dd->getEnum('iqc_opra',$opra_code);
					$logModel->addLog($val,$s[$opra_code],"IQC质检操作：".$opra_act."，备注：".$remark);
				}else{
					$result['error'] = '操作失败';
				}
			}
			else
			{
				$result['error'] = '操作失败';
			}
			
		}
		
	
		
		Util::jsonExit($result);
	}


}

?>