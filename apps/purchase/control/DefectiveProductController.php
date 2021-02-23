<?php
/**
 *  -------------------------------------------------
 *   @file		: DefectiveProductController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-18 23:00:51
 *   @update	:
 *	 不良品返厂单
 *  -------------------------------------------------
 */
class DefectiveProductController extends Controller
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('downcsv','search');
	

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('defective_product_detail','purchase',23);	//生成模型后请注释该行
		//Util::V('defective_product_detail',23);	//生成视图后请注释该行
		$Processor_Info = new ApiProcessorModel();
		$Processor_list = $Processor_Info->GetSupplierList();
		$this->render('defective_product_search_form.html',array(
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
			'id'			=> _Request::getint("id"),
			'ship_num'		=> _Request::get("ship_num"),
			'prc_id'		=> _Request::getInt("prc_id"),
			'status'		=> _Request::getInt("status"),
			'make_name'		=> _Request::get("make_name"),
			'check_name'	=> _Request::get("check_name"),
			'bc_sn' => _Request::get('bc_sn'),
			'make_time_min' => _Request::get('make_time_min'),
			'make_time_max' => _Request::get('make_time_max'),
			'check_time_min' => _Request::get('check_time_min'),
			'check_time_max' => _Request::get('check_time_max'),
		);
		$is_dowload =  _Request::get('is_dowload');
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array(
			'id'			=> $args['id'],
			"ship_num"		=> $args['ship_num'],
			'prc_id'		=> $args['prc_id'],
			'status'		=> $args['status'],
			'make_name'		=> $args['make_name'],
			'check_name'	=> $args['check_name'],
			'bc_sn'			=> $args['bc_sn'],
			'make_time_min' => $args['make_time_min'],
			'make_time_max' => $args['make_time_max'],
			'check_time_min' => $args['check_time_min'],
			'check_time_max' => $args['check_time_max'],
		);
        if(SYS_SCOPE == 'zhanting'){
            $where['hidden'] = '0';
        }
		$model = new DefectiveProductModel(23);
		//判断是否导出
		if($is_dowload == 1){
			$data = $model->pageList($where,$page,10000,false,false);
			$this->dowload($data );
			exit;
		}
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'defective_product_search_page';
		$this->render('defective_product_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'dd'	=> new DictView(new DictModel(1))
		));
	}
	

	public function dowload ($data)
	{
			$str = "单号,状态,货总数,货总金额,供应商, 出货单号, 出货单对应序号, 工厂模号, 金额,布产号, 客户名, 货品类型,制单人,审核人,制单时间,审核时间,备注（返修原因）,货品备注\n";
			$data = $data['data'];
			$dict_view = new DictView(new DictModel(1));
			$dict = new DictModel(1);
			$bl_status = $dict->getEnumArray('bl_status');
			$keys = array_column($bl_status,'name');
			$values = array_column($bl_status,'label');
			$status = array_combine($keys,$values);
			foreach($data as $key => $val)
			{
				$sta = $status[intval($val['status'])];
				$str .= $val['id'].",".$sta.",".$val['num'].",".$val['total'].",".$val['prc_name'].",".$val['ship_num'].",".$val['xuhao'].",".$val['factory_sn'].",".$val['details_total'].",".$val['bc_sn'].",".$val['customer_name'].",".$val['cat_type'].",".$val['make_name'].",".$val['check_name'].",".$val['make_time'].",".$val['check_time'].",".$val['note'].",".$val['info']."\r\n";
			}
			header("Content-Type: application/force-download");//add by zhangruiying防止浏览器直接打开
			header("Content-Disposition: attachment;filename=buliangpinfanchang.csv");
			echo iconv("utf-8","GB18030", $str);
	}
	/**
	 *	add，渲染添加页面
	 */
	public function add ($params)
	{
		//$id = $params['id'];
		$result = array('success' => 0,'error' => '');
		$Processor_Info = new ApiProcessorModel();
		$Processor_list = $Processor_Info->GetSupplierList();
		$result['content'] = $this->fetch('defective_product_add.html',array(
				'dd'  => new DictView(new DictModel(1)),
				'Processor_list'=>$Processor_list
		));
		$result['title'] = '添加操作';
		Util::jsonExit($result);
	}
	/**
	 *	downcsv，渲染添加页面
	 */
	public function downcsv ($params)
	{
		$xls_content = "出货对应序号,模号,金额,布产号,客户名,货品类型,货品备注\r\n";
		header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "" . rtrim('不良品返厂')) . ".csv");
		echo iconv("utf-8", "gbk", $xls_content);
	}
	
	/**
	 *	downcsv，渲染添加页面
	 */
	public function batinsert ($params)
	{
		//var_dump($_FILES['buliangdata']);exit;
		//上传文件
		//var_dump($_REQUEST);exit;
		$prc =_Request::get('prc_id');
		$note = _Request::get('note');
		if(!$prc){
			$result['error'] = '供应商不能为空！';
			Util::jsonExit($result);
		}
		if(empty($note)){
			$result['error'] = '备注不能为空';
			Util::jsonExit($result);
		}
		$ship_num =_Request::get('ship_num');
		$prc =explode("|", $prc);
		$prc_id=$prc[0];
		$prc_name=$prc[1];
		$buliangdata = isset($_FILES['buliangdata']['tmp_name'])?$_FILES['buliangdata']['tmp_name']:'';
		//var_dump($buliangdata);exit;
		if(!$buliangdata){
			$result['error'] = '上传文件不能为空';
			Util::jsonExit($result);
		}
		//exit;		
		$file = fopen($buliangdata,'r');
		//var_dump($file);exit;
		$datas = array();
		$n = 0;
		$goods_tatol=0;
		while ($data = fgetcsv($file))
		{
		if($n == 0)
		{
				$n++;
				continue;
		}
		
		foreach($data as $k => $v){
		//	var_dump($v);exit;
			$data[$k] = iconv("GBK","UTF-8",$v);
			if($k==2){
				$goods_tatol += $v;
			}
			
		}
		//var_dump($goods_tatol);exit;
			$chuhuo_id = intval($data[0]);
			$mohao = trim($data[1]);
			$price = trim($data[2]);
			$buchan_sn = trim($data[3]);
			$kehu_name = trim($data[4]);
			$huopin_type = trim($data[5]);
			$info = trim($data[6]);
		    if($n){
			   if (empty($chuhuo_id) || empty($mohao) || empty($price) || empty($huopin_type) || empty($info)){
					$result['error'] = '上传文件内容不完整！';
					Util::jsonExit($result);
			   }
		    }
		    $datas[] =$data;
		}
		//array_shift($datas);
		$num = count($datas);
		//取出货单号和供应商信息
		$m = new PurchaseReceiptDetailModel(23);
		if(!empty($datas)){
			$olddo = array();
			$newdo=array(
					'status'	=> 1,
					'prc_id'	=> $prc_id,
					'prc_name'	=> $prc_name,
					'ship_num'	=> $ship_num,
					'num'		=> $num,
					'total'		=> $goods_tatol,
					'make_name'	=> $_SESSION['userName'],
					'make_time' => date('Y-m-d H:i:s'),
					'check_name'=> '',
					'note' 		 =>$note
			);
			$newmodel =  new DefectiveProductModel(24);
			$res = $newmodel->batinsert_shiwu($newdo,$datas);//生成不良品返厂主表insert_shiwu
			if($res !== false)
			{
				$result['success'] = 1;
			}
			else
			{
				$result['error'] = '操作失败';
			}
			
			Util::jsonExit($result);
		}
		
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$model = new DefectiveProductDetailModel(23);
		$this->render('defective_product_info.html',array(
			'view' => new DefectiveProductView(new DefectiveProductModel($id,23)),
			'dd'   => new DictView(new DictModel(1)),
			'list' => $model->getListForInfoid($id),
			'bar' => Auth::getViewBar(),
		));
	}


	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$ids = $_POST['ids'];
		$m = new PurchaseReceiptDetailModel(23);
		$ids_str = implode(',',$ids);
		if(!count($ids_str))
		{
			$result['error'] = '至少选择一个货品生成不良品返厂单';
			Util::jsonExit($result);
		}
		if(!$m->checkDistinct('ship_num',$ids_str))
		{
			$result['error'] = '不是同一个出货单号不允许制单';
			Util::jsonExit($result);
		}

		if(!$m->checkDistinct('prc_id',$ids_str))
		{
			$result['error'] = '不是同一个供应商不允许制单';
			Util::jsonExit($result);
		}

		foreach($ids as $key => $val)
		{
			$row = $m->getRowOfid($val,'status');
			if($row['status'] != 5 && $row['status'] !=2)//如果状态不是IQC未过的不能生成不良品返厂单
			{
				$result['error'] = "序号 ".$val." 状态不符，不能生成不良品返厂单";
				Util::jsonExit($result);
			}
		}

		//取出货单号和供应商信息
		$arr = $m ->getCont($ids[0]);
		$olddo = array();
		$newdo=array(
			'status'	=> 1,
			'prc_id'	=> $arr['prc_id'],
			'prc_name'	=> $arr['prc_name'],
			'ship_num'	=> $arr['ship_num'],
			'num'		=> count($ids),
			'total'		=> 0.00,
			'make_name'	=> $_SESSION['userName'],
			'make_time' => date('Y-m-d H:i:s'),
			'check_name'=> '',
			'note' => '质检列表生成'
		);

		$newmodel =  new DefectiveProductModel(24);

		$res = $newmodel->insert_shiwu($newdo,$ids);//生成不良品返厂主表insert_shiwu
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '操作失败';
		}
		//以下代码改为已事物  BY linian
		/*
		//$res = $newmodel->saveData($newdo,$olddo);//生成不良品返厂主表
		if($res !== false)
		{
			$iqcOpraModel = new PurchaseIqcOpraModel(24);
			$detailModel =  new DefectiveProductDetailModel(24);
			foreach($ids as $key => $val)
			{
				//$arr = $model->getRowOfid($val);
				$model = new PurchaseReceiptDetailModel($val,24);
				$model->getDataObject();
				$iqc = $iqcOpraModel->getOne_iqc_w($val);
				var_dump($iqc);exit;
				$newdo=array(
					'info_id'		=> $res,
					'rece_detail_id'=> $model->getValue('id'),
					'xuhao'			=> $model->getValue('xuhao'),
					'factory_sn'	=> $model->getValue('factory_sn'),
					'bc_sn'			=> $model->getValue('bc_sn'),
					'customer_name'	=> $model->getValue('customer_name'),
					'cat_type'		=> $model->getValue('cat_type'),
					'total'			=> 0.00,
					'info'			=> $iqc['opra_info']
				);
				$detail_res = $detailModel->saveData($newdo,$olddo);//生成不良品返厂明细表信息

				//生成不良品返厂单后设置货品状态为待返厂中
				$model->setValue('status',6);
				$model->save();

				//记录log
				$logModel = new PurchaseLogModel(24);
				$logModel->addlog($model->getValue('id'),$model->getValue('status'),"生成不良品返厂单，单号：".$res."，等待返厂。");
			}
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '操作失败';
		}
		*/
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息 先不做更新操作
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new DefectiveProductModel($id,2);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	//审核不良品返厂单
	public function checkDfpro($params)
	{
		$id = $params['id'];
		$model = new DefectiveProductModel($id,24);
		if($model->getValue('status') != 1)
		{
			$result['error'] = "状态不正确，不能操作审核";
			Util::jsonExit($result);
		}
		$rece_detail_id = $model->getValue('rece_detail_id');
		$model->setValue('status',2);
		$model->setValue('check_name',$_SESSION['userName']);
		$model->setValue('check_time',date('Y-m-d H:i:s'));
		if($model->save())
		{
			//不良品返厂单审核，返厂的货品在收货明细里状态为已返厂
			if($rece_detail_id){
				if($model->editDeceiptStatus($id,7))
				{
					$result['success'] = 1;
				}else{
					$result['error'] = "单据数据问题，审核失败";
				}
			}else{
				$result['success'] = 1;
			}
			
		}else{
			$result['error'] = "操作失败，请重试";
		}
		Util::jsonExit($result);
	}

	//取消不良品返厂单
	public function cancelPro($params)
	{
		$id = $params['id'];
		$result = array('success' => 0,'error' =>'');
		$model = new DefectiveProductModel($id,24);
		if($model->getValue('status') != 1)
		{
			$result['error'] = "状态不正确，不能操作取消";
			Util::jsonExit($result);
		}

		if($model->getValue('make_name') != $_SESSION['userName'])
		{
			$result['error'] = "只有制单人才能取消单据";
			Util::jsonExit($result);
		}
		//$rece_detail_id = $model->getValue('rece_detail_id');
                $rece_detail_id = $model->getDetailId($id);
		$model->setValue('status',3);
		$model->setValue('check_name',$_SESSION['userName']);
		$model->setValue('check_time',date('Y-m-d H:i:s'));
		if($model->save())
		{
			//单据下货品恢复为IQC--5未过或报废--2状态(待返厂上一状态)
			if($rece_detail_id){
				if($model->editDeceiptStatus($id,5))
				{
					$result['success'] = 1;
				}else{
					$result['error'] = "单据数据问题，审核失败";
				}
			}else{
				$result['success'] = 1;
			}
			
		}else{
			$result['error'] = "操作失败，请重试";
		}
		Util::jsonExit($result);

	}

		/**
	 * mkJson 生成Json表单
	 */
	public function mkJson(){
		$id = _Post::getInt('id');
		$arr = Util::iniToArray(APP_ROOT.'purchase/data/defectiovePro_from_table.tab');
		$detailModel = new DefectiveProductDetailModel(23);
		$detail_arr = $detailModel->getListForInfoid($id);
		//print_r($detail_arr);exit;
		$detail = array();
		foreach($detail_arr as $key => $val)
		{
			$detail[$key][]	= $val['rece_detail_id'];
			$detail[$key][]	= $val['factory_sn'];
			$detail[$key][]	= $val['bc_sn'];
			$detail[$key][] = $val['customer_name'];
			$detail[$key][] = $val['cat_type'];
			$detail[$key][] = $val['total'];
			$detail[$key][] = $val['info'];
		}
		$arr['data'] = $detail;
		//print_r($arr);exit;
		$json = json_encode($arr);

		echo $json;
	}
	public function getJson(){
		$result = array('success' => 0,'error' =>'');
		$result['error'] = "开发中";
		Util::jsonExit($result);
	}

	public function printDetail($params)
	{
		$id = $params['id'];
		$detailModel = new DefectiveProductDetailModel(23);
		$order_goods = $detailModel->getListForInfoid($id);
		$this->render('defective_product_print.html',array(
			'view'   => new DefectiveProductView(new DefectiveProductModel($id,23)),
			'order_goods' => $order_goods
		));
	}

	public function printWriter(){
		
	}
}

?>