<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseReceiptController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-15 11:16:58
 *   @update	:采购收货单
 *  -------------------------------------------------
 */
class PurchaseReceiptController extends Controller
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('download');
	//add by zhangruiying获取搜索条件
	function getRquestArg()
	{
		$args = array(
			'mod'			=> _Request::get("mod"),
			'con'			=> substr(__CLASS__, 0, -10),
			'act'			=> __FUNCTION__,
			'ship_num'		=> _Request::get("ship_num"),
			'id'			=> _Request::getInt("id"),
			'prc_id'		=> _Request::getInt("prc_id"),
			'user_name'		=> _Request::get("user_name"),
			'status'		=> _Request::get("status"),
			'start_time'=>_Request::get("start_time"),
			'end_time'=>_Request::get("end_time"),
			'is_all'=>_Request::get("is_all")
		);
        if(SYS_SCOPE == 'zhanting'){
            $args['hidden'] = '0';
        }

		return $args;
	}
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$model_p = new ApiProcessorModel();
		$pro_list = $model_p->GetSupplierList();//调用加工商接口
		//add by zhangruiying 如果是权限列表择显示相关供应商下拉选项
		$is_all=_Request::get("is_all");
		$prc_ids=array();
		if($is_all!=1 and $_SESSION['userType']==3)
		{
			$prc_ids=$this->getPrcIdsByUserId($_SESSION['userId']);
		}
		foreach($pro_list as $key=>$v)
		{
			if($is_all!=1 and $_SESSION['userType']==3)
			{
				if(!in_array($v['id'],$prc_ids))
				{
					unset($pro_list[$key]);
				}
			}
		}
		//add end
		$this->render('purchase_receipt_search_form.html',array(
			'bar'=>Auth::getBar(),
			'dd' => new DictView(new DictModel(1)),
			'pro_list'=>$pro_list,
			'is_all'=>$is_all

		));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		//edit by zhangruiying删除了$WHERE不需要整两个变理直接用ARGS
		$args=$this->getRquestArg();
		//edit end
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$model = new PurchaseReceiptModel(23);
		//add by zhangruiying 根据当前登陆用户查询他关联的供应商
		$prc_ids=$this->getPrcIdsByUserId($_SESSION['userId']);
		$args['prc_ids']=$prc_ids;
		//add end
		$data = $model->pageList($args,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		//edit by zhangruiying JS回调方法名全部列表和权限列表
		$pageData['jsFuncs'] =!empty($args['is_all'])?'purchase_receipt_all_search_page':'purchase_receipt_search_page';
		//edit end
		$this->render('purchase_receipt_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'dd' => new DictView(new DictModel(1)),
			'is_all'=>$args['is_all']
		));
	}
	/**
	 *	add by zhangruiying
	 *	下载采购单列表
	 */
	function  download($params)
	{
		$args=$this->getRquestArg();
		$prc_ids=$this->getPrcIdsByUserId($_SESSION['userId']);
		$args['prc_ids']=$prc_ids;
		$model = new PurchaseReceiptModel(23);
		$data = $model->getList($args);
		$conf=array(
			array('field'=>'status','title'=>'采购单收货单状态'),
			array('field'=>'prc_name','title'=>'供应商'),
			array('field'=>'ship_num','title'=>'出货单号'),
			array('field'=>'num','title'=>'总数量'),
			array('field'=>'all_amount','title'=>'总金额'),
			array('field'=>'user_name','title'=>'制单人'),
			array('field'=>'create_time','title'=>'制单时间'),
			array('field'=>'edit_user_name','title'=>'最后操作人'),
			array('field'=>'edit_time','title'=>'最后操作时间')
			);
		$dd=new DictModel(1);

		foreach($data as $k=>$v)
		{
			$data[$k]['status']=$dd->getEnum('DefectiveProductStatus',$v['status']);
		}
		ob_clean();
		Util::downloadCsvNew('purchase_receipt',$conf,$data);
		exit;
	}
	/**
	 *	add by zhangruiying
	 *	根据用户ID查找相关的供应商
	 */
	function getPrcIdsByUserId($id)
	{
	    /*
		$ori_str=array('id'=>$id);
		ksort($ori_str);
		$ori_str=json_encode($ori_str);
		$data=array("filter"=>$ori_str,"sign"=>md5('processor'.$ori_str.'processor'));
		$ret=Util::httpCurl(Util::getDomain().'/api.php?con=processor&act=getPrcIdsByUserId',$data);
		$ret=json_decode($ret,true);
		return $ret['return_msg'];
		*/
	    $api = new ApiModel();
	    return $api->process_api(array('id'), array($id), 'getPrcIdsByUserId');
	}
	
	/**
	 *	add by dom
	 *	根据用户buchanId查找 app_order_detail 信息  goods_type
	 */
	function getAppOrderDetailsByBcid(array $ids)
	{
	    if(!empty($ids)){
		    $api_res  = ApiSalesModel::getOrderDetailByBCId(implode(",",$ids));
		    $data=[];
		    if(!$api_res['error']){
		        foreach ($api_res['return_msg'] as $k=>$v){
		            $data[$v['bc_id']] = $v;
		        }
		    }
		    return $data;
		}
		return array();
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ($params)
	{
		$model_p = new ApiProcessorModel();
		$pro_list = $model_p->GetSupplierList();//调用加工商接口
		$this->render('purchase_receipt_info_add.html',array(
			'tab_id'	=> $params['tab_id'],
			'pro_list'	=>$pro_list
		));
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$model = new PurchaseReceiptModel($id,23);

		$model_p = new ApiProcessorModel();
		$pro_list = $model_p->GetSupplierList();//调用加工商接口

		$this->render('purchase_receipt_info_edit.html',array(
			'view'=>new PurchaseReceiptView($model),
			'dd' => new DictView(new DictModel(1)),
			'pro_list'	=>$pro_list,
			'bar'=>Auth::getViewBar(),
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$ar = explode("|",_Post::get("prc_id"));
		$prc_id		= $ar[0];
		$prc_name	= $ar[1];
		$ship_num	= _Post::get('ship_num');
		$remark		= _Post::get('remark');
		$tab_id		= _Post::getInt('tab_id');
		$newmodel =  new PurchaseReceiptModel(24);
		$c = $newmodel->getCount(array('ship_num'=>$ship_num));
		if($c)
		{
			$result['error'] ="出货单号已经存在，不能重复。";
			Util::jsonExit($result);
		}

		if($ship_num=='')
		{
			$result['error'] ="出货单号不能为空！";
			Util::jsonExit($result);
		}
		if($prc_id=='')
		{
			$result['error'] ="供应商不能为空！";
			Util::jsonExit($result);
		}

		//整理上传数据
		$dataArr = array();
		$xuhao_arr= array();
		$zong_chengbenjia = 0;
		if(isset($_FILES['out_goods_list']['tmp_name']) && $_FILES['out_goods_list']['tmp_name'] !="")
		{
			$handle = fopen ($_FILES['out_goods_list']['tmp_name'],"r");
			$line_num = 1;
			$all_price = 0;
			$bc_sn_ids=array();
			while ($data = fgetcsv ($handle))
			{
				if($line_num ==1)//略过第一行
				{
					$line_num++;
					continue;
				}
				foreach($data as $k => $v)
				{
					$line[$k] = iconv("GBK", 'UTF-8', $v);
				}
				$have_data = false;
				foreach($line as $d)
				{
					$d = trim($d);
					if(!empty($d))
					{
						$have_data = true;
						break;
					}
				}
				if(!$have_data) continue;
				$receiptDetail = array(
					"xuhao" => trim($line[$this->col("a")]),
					"customer_name" => trim($line[$this->col("b")]),
					"purchase_sn" => trim($line[$this->col("c")]),
					"bc_sn" => trim($line[$this->col("d")]),
					"style_sn" => trim($line[$this->col("e")])?trim($line[$this->col("e")]):'',
					"factory_sn" => trim($line[$this->col("f")]),
					"ring_mouth" => trim($line[$this->col("g")])?trim($line[$this->col("g")]):0.00,
					"cat_type" => trim($line[$this->col("h")])?trim($line[$this->col("h")]):'',
					"is_cp_kt" => trim($line[$this->col("i")])?trim($line[$this->col("i")]):'',
					"hand_inch" => trim($line[$this->col("j")])?trim($line[$this->col("j")]):0,
					"material" => trim($line[$this->col("k")])?trim($line[$this->col("k")]):'',
					"gross_weight" => trim($line[$this->col("l")])?trim($line[$this->col("l")]):0,
					"net_gold_weight" => trim($line[$this->col("m")])?trim($line[$this->col("m")]):0.00,//金重
					"gold_loss" => trim($line[$this->col("n")])?trim($line[$this->col("n")]):0.00,//金耗
					"gold_price" => trim($line[$this->col("o")])?trim($line[$this->col("o")]):0.00,//金价
					"main_stone" => trim($line[$this->col("p")])?trim($line[$this->col("p")]):'',
					"main_stone_num" => trim($line[$this->col("q")])?trim($line[$this->col("q")]):0,
					"main_stone_weight" => trim($line[$this->col("r")])?trim($line[$this->col("r")]):0.000,//主石总重

					"zhushiyanse" => trim($line[$this->col("s")])?trim($line[$this->col("s")]):'',//主石颜色
					"zhushijingdu" => trim($line[$this->col("t")])?trim($line[$this->col("t")]):'',//主石净度
					"zhushidanjia" => trim($line[$this->col("u")])?trim($line[$this->col("u")]):0.00,//主石单价
					"fushi" => trim($line[$this->col("v")])?trim($line[$this->col("v")]):'',//副石
					"fushilishu" => trim($line[$this->col("w")])?trim($line[$this->col("w")]):0,//副石粒数
					"fushizhong" => trim($line[$this->col("x")])?trim($line[$this->col("x")]):0.000,//副石重
					"fushidanjia" => trim($line[$this->col("y")])?trim($line[$this->col("y")]):0.00,//副石单价

					"work_fee" => trim($line[$this->col("z")])?trim($line[$this->col("z")]):0.00,
					"extra_stone_fee" => trim($line[$this->col("aa")])?trim($line[$this->col("aa")]):0.00,
					"other_fee" => trim($line[$this->col("ab")])?trim($line[$this->col("ab")]):0.00,
					"fittings_cost_fee" => trim($line[$this->col("ac")])?trim($line[$this->col("ac")]):0.00,
					"tax_fee" => trim($line[$this->col("ad")])?trim($line[$this->col("ad")]):0.00,
					"customer_info_stone" => trim($line[$this->col("ae")])?trim($line[$this->col("ae")]):'',


					"zhengshuhao" => trim($line[$this->col("af")])?trim($line[$this->col("af")]):'',
					"shi2" => trim($line[$this->col("ag")])?trim($line[$this->col("ag")]):'',
					"shi2lishu" => trim($line[$this->col("ah")])?trim($line[$this->col("ah")]):0,
					"shi2zhong" => trim($line[$this->col("ai")])?trim($line[$this->col("ai")]):0.000,
					"shi2danjia" => trim($line[$this->col("aj")])?trim($line[$this->col("aj")]):0.00,

					"shi3" => trim($line[$this->col("ak")])?trim($line[$this->col("ak")]):'',
					"shi3lishu" => trim($line[$this->col("al")])?trim($line[$this->col("al")]):0,
					"shi3zhong" => trim($line[$this->col("am")])?trim($line[$this->col("am")]):0.000,
					"shi3danjia" => trim($line[$this->col("an")])?trim($line[$this->col("an")]):0.00,

				);//print_r($receiptDetail);exit;
				//成本    =净金重*金耗*金价+主石总重*主石单价+副石总重*副石单价+工费+超石费+其他工费+配件成本
				//含税成本=净金重*金耗*金价+主石总重*单价+副石总重*副石单价单价+工费+超石费+其他工费+配件成本+税费 +$receiptDetail['tax_fee']
				$receiptDetail['chengbenjia'] =  $this->getPrice($receiptDetail);

				$zong_chengbenjia += $receiptDetail['chengbenjia'];
				if(empty($receiptDetail['xuhao'])){
					$result['error'] = "序号必填";
					Util::jsonExit($result);
				}
				$all_price += $receiptDetail['chengbenjia'] + $receiptDetail['tax_fee'];
				if(in_array($receiptDetail['xuhao'],$xuhao_arr))
				{
					$result['error'] = "序号重复，请修改";
					Util::jsonExit($result);
				}
				$xuhao_arr = array($receiptDetail['xuhao']);
				//add by zhangruiying
				if(empty($receiptDetail['style_sn']))
				{
					if(!empty($receiptDetail['bc_sn']))
					{
						$bc_sn_ids[]=$receiptDetail['bc_sn'];
					}
					else
					{
						if(!empty($receiptDetail['purchase_sn']))
						{

							$purchase_sn[]=$receiptDetail['purchase_sn'];
						}
					}


				}

				//add end
				//判断数据问题
				$re = $this->checkData($receiptDetail,$prc_id);
				if($re['error'])//检测数据，输出错误
				{
					$result['error'] = $re['content'];
					Util::jsonExit($result);
				}
				$dataArr[] = $receiptDetail;
			}
			if(!empty($bc_sn_ids))
			{
			    /*
				$ori_str=array('ids'=>$bc_sn_ids);
				ksort($ori_str);
				$ori_str=json_encode($ori_str);
				$data=array("filter"=>$ori_str,"sign"=>md5('processor'.$ori_str.'processor'));
				$ret=Util::httpCurl(Util::getDomain().'/api.php?con=processor&act=getProductInfoByIDS',$data);
				$ret=json_decode($ret,true);
				$bc_sn_list=array();
				if($ret['error']==0)
				{
					$bc_sn_list=$ret['return_msg'];
				}
				unset($ret,$bc_sn_ids);
				*/
			    $api = new ApiModel();
			    $bc_sn_list = $api->process_api(array('ids'),array($bc_sn_ids), 'getProductInfoByIDS');
			}
			if(!empty($purchase_sn))
			{
				$purchase_sn_list=$newmodel->GetPurchaseList("'".implode("','",$purchase_sn)."'");
			}
		}
		if(!count($dataArr))
		{
			$result['error'] = "上传内容不能为空";
			Util::jsonExit($result);
		}

		foreach($dataArr as $key=>$v)
		{
			if(empty($v['style_sn']) and !empty($v['bc_sn']))
			{
				$dataArr[$key]['style_sn']=isset($bc_sn_list[$v['bc_sn']])?$bc_sn_list[$v['bc_sn']]:'';
			}
			if(empty($dataArr[$key]['style_sn']) and !empty($v['purchase_sn']))
			{
				$dataArr[$key]['style_sn']=isset($purchase_sn_list[$v['purchase_sn']])?$purchase_sn_list[$v['purchase_sn']]:'';
			}

		}

		$olddo = array();
		$newdo=array(
			'prc_id'	=> $prc_id,
			'prc_name'	=> $prc_name,
			'ship_num'	=> $ship_num,
			'remark'	=> $remark,
			'num'		=> count($dataArr),
			'all_amount'	=> $all_price,
			'user_id'	=> $_SESSION['userId'],
			'user_name'	=> $_SESSION['userName'],
			'create_time'=> date('Y-m-d H:i:s'),
			'chengbenjia' => $zong_chengbenjia
		);
		//var_dump($newdo);var_dump($dataArr);exit;
		$res = $newmodel->add_caigou_info($newdo,$dataArr);
		if ($res['success'] == true)
		{
			$result['success'] = 1;
			$result['x_id']	   = $res['id'];
			$result['tab_id'] = $tab_id;
		}
		else
		{
			$result['error'] = '添加失败';
		}

		Util::jsonExit($result);
	}


	//对上传的每一条数据进行判断
	private function checkData($data,$prc_id='')
	{
		$result = array("error" => 1,"content" => "数据错误");
		if($data['factory_sn'] == "")
		{
			$result['content'] = "请检查数据，工厂模号都不能为空";
			return $result;
		}
		//add by zhangruiying
		if(empty($data['purchase_sn']) and empty($data['bc_sn']))
		{
			$result['content'] = "布产号和采购单号必填其一！";
			return $result;
		}
		//add end
		//填写了采购单 时 验证
		if($data['purchase_sn']!=''){

			//判断采购单号是否存在
			$purModel = new PurchaseInfoModel(23);
			if(!$purModel->isExistPsn($data['purchase_sn']))
			{
				$result['content'] = "采购单号".$data['purchase_sn']."不存在";
				return $result;
			}

			//根据采购单号取单据信息
			$row = $purModel->getRowOfpsn($data['purchase_sn']);

			if($row['p_status'] != 3)
			{
				$result['content'] = "采购单号".$data['purchase_sn']."不是已审核状态，不能进行收货。";
				return $result;
			}
			/*
			if($row['is_style'] && $data['style_sn'] == "")
			{
				$result['content'] = "采购单号".$data['purchase_sn']."为有款采购，必须输入款号";
				return $result;
			}
			*/
			if($row['is_tofactory'] && $data['bc_sn'] == "")
			{
				$result['content'] = "采购单号".$data['purchase_sn']."去工厂生产，必须输入布产号";
				return $result;
			}


		}
              
        
		//如果款号不为空，则一定是个有效的款号
		if($data['style_sn'] != "")
		{
			//调用接口，查看款号是否存在
			$apimodel = new ApiStyleModel();
			$ret = $apimodel->GetStyleInfoBySn($data['style_sn']);
			if(!count($ret))
			{
				$result['content'] = "款号".$data['style_sn']." 不存在";
				return $result;
			}

		}
		if(!in_array($data['is_cp_kt'],['成品','空托']))
		{
			$result['content'] = "'成品/空托' 列输入不规范，只能输入成品或者空托";
			return $result;
		}

		/*如果布产号不为空，判断有效的采购收货单中是不是已经收过此布产号的货品
		* 如果有则提示，
		* 如果没有，则调用接口，判断布产号是否存在，采购单号和布产号是否是正确的对应关系。
		* 布产的款号和录入的款号也必须对应
		*/
		if($data['bc_sn'] != "")
		{
			//调用接口
			$bc_sn = $data['bc_sn'];
			$model = new PurchaseReceiptDetailModel(23);
//			$purchase_receipt_id = $model->getCountBcsn($bc_sn);
//			if($purchase_receipt_id && ($data['purchase_receipt_id'] != $purchase_receipt_id))
//			{
//				//阿布说不需要限制，去掉---JUAN
//				//$result['content'] = "流水号".$purchase_receipt_id." 已经收过布产号".$bc_sn." 请检查。";
//				//return $result;
//			}
			//检查供应商是否一致 begin
			$processorModel = new SelfProcessorModel(13);
			$bc_sn = strtoupper(trim($data['bc_sn']));
			$bc_id = preg_replace('/^[a-zA-Z]+/', '', $bc_sn);
			if(empty($bc_id)){
			    $result['content'] = "布产号".$bc_sn."不合法";
			    return $result;
			}
			$product_info = $processorModel->selectProductInfo("*","id={$bc_id}",2);
			if(empty($product_info)){
			    $result['content'] = "布产号".$bc_sn."系统不存在";
			    return $result;
			}
			$new_prc_id = $product_info['prc_id'];
			$res = $processorModel->checkSupplierConnected($prc_id,$new_prc_id);
			if(!$res){
			    $result['content'] = "布产单".$bc_sn."对应供应商与所选供应商不一致";
			    return $result;
			}
			//检查供应商是否一致 end
			
			if(isset($product_info['style_sn']) and ($product_info['style_sn'] != $data['style_sn']) and !empty($data['style_sn']))
			{
				$result['content'] = "布产单".$bc_sn."所采购款号和此次收货款号".$data['style_sn']." 不一致，请检查。";
				return $result;
			}


		}
		$result['error'] = 0;
		return $result;
	}


	public function col($str)
	{
		$index = 0;
		for($i=0; $i<=strlen($str)-1; $i++)
		{
			$v = ord(strtoupper($str[$i])) -65;
			$index += pow(26, $i)+$v;
		}
		return $index-1;
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new PurchaseReceiptModel($id,2);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/**
	 * mkJson 生成Json表单
	 */
	public function mkJson(){
		$id = _Post::getInt('id');
		$arr = Util::iniToArray(APP_ROOT.'purchase/data/from_table.tab');
		$detailModel = new PurchaseReceiptDetailModel(23);
		$detail_arr = $detailModel->getListForRid($id);
		$detail = array();
		foreach($detail_arr as $key => $val)
		{
			$detail[$key][]	= $val['xuhao'];
			$detail[$key][]	= $val['customer_name'];
			$detail[$key][]	= $val['purchase_sn'];
			$detail[$key][] = $val['bc_sn'];
			$detail[$key][] = $val['style_sn'];
			$detail[$key][] = $val['factory_sn'];
			$detail[$key][] = $val['ring_mouth'];
			$detail[$key][] = $val['cat_type'];
			$detail[$key][]	= $val['is_cp_kt'];
			$detail[$key][] = $val['hand_inch'];
			$detail[$key][] = $val['material'];
			$detail[$key][]	= $val['gross_weight'];
			$detail[$key][] = $val['net_gold_weight'];

			$detail[$key][] = $val['gold_loss'];
			$detail[$key][] = $val['gold_price'];
			$detail[$key][] = $val['main_stone'];
			$detail[$key][] = $val['main_stone_num'];
			$detail[$key][] =$val['main_stone_weight'];


			$detail[$key][] = $val['zhushiyanse'];
			$detail[$key][] = $val['zhushijingdu'];
			$detail[$key][]	= $val['zhushidanjia'];
			$detail[$key][] = $val['fushi'];
			$detail[$key][] = $val['fushilishu'];
			$detail[$key][]	= $val['fushizhong'];
			$detail[$key][] = $val['fushidanjia'];


			$detail[$key][] = $val['work_fee'];
			$detail[$key][] = $val['extra_stone_fee'];
			$detail[$key][] = $val['other_fee'];
			$detail[$key][] = $val['fittings_cost_fee'];
			$detail[$key][] = $val['tax_fee'];
			$detail[$key][] = $val['customer_info_stone'];

			$detail[$key][] = $val['zhengshuhao'];
			$detail[$key][] = $val['shi2'];
			$detail[$key][] = $val['shi2lishu'];
			$detail[$key][] = $val['shi2zhong'];
			$detail[$key][] = $val['shi2danjia'];
			$detail[$key][] = $val['shi3'];
			$detail[$key][] = $val['shi3lishu'];
			$detail[$key][] = $val['shi3zhong'];
			$detail[$key][] = $val['shi3danjia'];
			$detail[$key][] = $val['chengbenjia'];
			$detail[$key][] = $val['chengbenjia']+$val['tax_fee'];
			$detail[$key][] = $val['main_stone_weight']+$val['fushizhong']+$val['shi2zhong']+$val['shi3zhong'];
		}
		$arr['data'] = $detail;
		//print_r($arr);exit;
		$json = json_encode($arr);

		echo $json;
	}

	/**
	 * getJson；修改收货单
	 */
	public function getJson($params){
		$result = array('success' => 0,'error' => '');

		$id   = intval($params['id']);
		$data = isset($params['data'])?$params['data']:array();
		$ar = explode("|",$params['prc_id']);
		$ship_num	= $params['ship_num'];
		$prc_id		= $ar[0];
		$prc_name	= $ar[1];
		$remark		= $params['remark'];
		//$detailModel = new PurchaseReceiptDetailModel(24); //将此段代码加入事务 删除了
		$model = new PurchaseReceiptModel($id,24);
		//验证单据基本信息
		if(!count($data))
		{
			$result['error'] = "明细内容为空，不能编辑";
			Util::jsonExit($result);
		}
		if($ship_num=='')
		{
			$result['error'] ="出货单号不能为空！";
			Util::jsonExit($result);
		}
		if($model->getValue('ship_num') != $ship_num)
		{
			$c = $model->getCount(array('ship_num'=>$ship_num));
			if($c)
			{
				$result['error'] ="出货单号已经存在，不能重复。";
				Util::jsonExit($result);
			}
		}
		if($prc_id=='')
		{
			$result['error'] ="供应商不能为空！";
			Util::jsonExit($result);
		}

		if(!in_array($model->getValue('status'),array(1,4)))
		{
			$result['error'] = "不是已保存状态不允许编辑";
			Util::jsonExit($result);
		}
		if($model->getValue('user_id') != $_SESSION['userId'])
		{
			$result['error'] = "不是制单人不能编辑";
			Util::jsonExit($result);
		}

		//组织数据库数据
		$data_info = array();
		$all_price = 0;
		$zong_chengbenjia = 0;
		foreach($data as $key => $val)
		{
			//剔除啥也没填的数据
			if( ($val[0] == '') && ($val[1] == '') && ($val[2] == '') && ($val[3] == '') && ($val[4] == '') && ($val[5] == '') && ($val[6] == '') && ($val[7] == '') && ($val[8] == '') && ($val[9] == '') && ($val[10] == '') && ($val[11] == '') && ($val[12] == '') && ($val[13] == '') && ($val[14] == '') && ($val[15] == '') && ($val[16] == '') && ($val[17] == '') && ($val[18] == '') && ($val[19] == '') && ($val[20] == '') && ($val[21] == '') && ($val[22] == '') && ($val[23] == '') )
			{
				continue;
			}

			$newdo = array(
				"xuhao" => $val["0"],
				"purchase_receipt_id" => $id,
				"customer_name" => $val["1"],
				"purchase_sn" => $val["2"],
				"bc_sn" => $val["3"],
				"style_sn" => $val["4"],
				"factory_sn" => $val["5"],
				"ring_mouth" => $val["6"]?$val["6"]:0.00,
				"cat_type" => $val["7"],
				"is_cp_kt" => $val["8"],
				"hand_inch" => $val["9"]?$val["9"]:0,
				"material" => $val["10"],
				"gross_weight" => $val["11"]?$val["11"]:0,
				"net_gold_weight" => $val["12"]?$val["12"]:0,
				"gold_loss" => $val["13"]?$val["13"]:0,
				"gold_price" => $val["14"],
				"main_stone" => $val["15"],
				"main_stone_weight" => $val["17"]?$val["17"]:0.000,
				"main_stone_num" => $val["16"],

				"zhushiyanse" => $val['18'],
				"zhushijingdu"=> $val['19'],
				"zhushidanjia"=> $val['20']?$val['20']:0.00,
				"fushi"=> $val['21'],
				"fushilishu"=> $val['22']?$val['22']:0,
				"fushizhong"=> $val['23']?$val['23']:0.000,
				"fushidanjia"=> $val['24']?$val['24']:0,

				"work_fee" => $val["25"],
				"extra_stone_fee" => $val["26"]?$val["26"]:0.00,
				"other_fee" => $val["27"]?$val["27"]:0.00,
				"fittings_cost_fee" => $val["28"]?$val["28"]:0.00,
				"tax_fee" => $val["29"]?$val["29"]:0.00,
				"customer_info_stone" => $val["30"],

				"zhengshuhao" => $val['31'],
				"shi2"=> $val['32'],
				"shi2lishu"=> $val['33']?$val['33']:0,
				"shi2zhong"=> $val['34']?$val['34']:0.000,
				"shi2danjia"=> $val['35']?$val['35']:0.00,
				"shi3"=> $val['36'],
				"shi3lishu"=> $val['37']?$val['37']:0,
				"shi3zhong"=> $val['38']?$val['38']:0,
				"shi3danjia"=> $val['39']?$val['39']:0,

			);
			$re = $this->checkData($newdo,$prc_id);
			if($re['error'])//检测数据，输出错误
			{
				$result['error'] = $re['content'];
				Util::jsonExit($result);
			}
			//计算成本价总和
			$newdo['chengbenjia'] =  $this->getPrice($newdo);
			$data_info[] = $newdo;

			$zong_chengbenjia += $newdo['chengbenjia'];
			$all_price += $newdo['chengbenjia'] + $newdo['tax_fee'];
		}
		//var_dump($data_info);exit;
		$info = array(
			'id'=>$id,
			'ship_num'=>$ship_num,
			'prc_id'=>$prc_id,
			'prc_name'=>$prc_name,
			'remark'=>$remark,
			'num'=>count($data_info),
			'chengbenjia' => $zong_chengbenjia,
			'all_amount'=>$all_price
			);
		$res = $model->update_caigou_info($info,$data_info);
		if ($res != true)
		{
			$result['error'] = "数据保存错误";
			Util::jsonExit($result);
		}
		else
		{
			$result['success'] = true;
			Util::jsonExit($result);
		}
	}

	//采购收货单计算成本公式
	function getPrice($newdo){
		//成本价=净金重*金损耗*金价+主石重*主石价+副石重*副石买入单价+副石重2*副石2买入单价+副石重3*副石3买入单价+工费+超石费+配件成费+其他工费  ；
		//含税成本价=净金重*金损耗*金价+主石重*主石价+副石重*副石买入单价+副石重2*副石2买入单价+副石重3*副石3买入单价+工费+超石费+配件成费+其他工费  +税费
		$price = round(    ($newdo['net_gold_weight']*$newdo['gold_loss']*$newdo['gold_price'])+($newdo['main_stone_weight']*$newdo['zhushidanjia'])+($newdo['fushizhong']*$newdo['fushidanjia'])+$newdo['work_fee']+$newdo['extra_stone_fee']+$newdo['other_fee']+$newdo['fittings_cost_fee'] + $newdo["shi2zhong"]*$newdo["shi2danjia"]+ $newdo["shi3zhong"]*$newdo["shi3danjia"] ,2);
		return $price;
	}
	/**
	 *	update，更新信息
	 */
	public function check_info ()
	{
		$result = array('success' => 0,'error' =>'');
		$id = $_REQUEST['id'];
		$ar = explode("|",$_REQUEST['prc_id']);
		$ship_num	= $_REQUEST['ship_num'];
		$prc_id		= $ar[0];
		$prc_name	= $ar[1];
		$remark		= $_REQUEST['remark'];

		$newmodel =  new PurchaseReceiptModel($id,24);

		if($ship_num=='')
		{
			$result['error'] ="出货单号不能为空！";
			Util::jsonExit($result);
		}

		if($newmodel->getValue('ship_num') != $ship_num)
		{
			$c = $newmodel->getCount(array('ship_num'=>$ship_num));
			if($c)
			{
				$result['error'] ="出货单号已经存在，不能重复。";
				Util::jsonExit($result);
			}
		}

		if($prc_id=='')
		{
			$result['error'] ="供应商不能为空！";
			Util::jsonExit($result);
		}

		if($newmodel->getValue('status') != 1)
		{
			$result['error'] = "不是已保存状态不允许编辑";
			Util::jsonExit($result);
		}
		if($newmodel->getValue('user_id') != $_SESSION['userId'])
		{
			$result['error'] = "不是制单人不能编辑";
			Util::jsonExit($result);
		}
	}

	//采购收货单模板
	public function getTemplate()
	{
		//$str = "序号*,客户名,采购单号*,布产号,款号, 模号*, 戒托实际镶口, 款式分类(品名), 成品/空托*, 手寸, 主成色,净金重,主成色重(净金重),金耗,主成色买入单价(金价),主石(主石类别),主石粒数,主石重,工费(买入工费)*,超石费*,其它工费*,配件成本*,税费*,客来石信息,成本价*\n";
		$str = "序号*,客户名,采购单号*,布产号,款号, 模号*, 戒托实际镶口, 款式分类(品名), 成品/空托*, 手寸, 主成色,净金重,主成色重(净金重),金耗,主成色买入单价(金价),主石(主石类别),主石粒数,主石重,主石颜色,主石净度,主石买入单价,副石(副石类别),副石粒数,副石重,副石买入单价,工费(买入工费)*,超石费*,其它工费*,配件成本*,税费*,客来石信息,证书号,石2(石2类别),石2粒数,石2重,石2买入单价,石3(石3类别),石3粒数,石3重,石3买入单价\n";
		header("Content-Type: application/force-download");//add by zhangruiying防止浏览器直接打开
		header("Content-Disposition: attachment;filename=caigoushouhuo.csv");
		echo iconv("utf-8","gbk", $str);
	}

	//审核单据
	public function checkInfo($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = $params['id'];//采购收货单ID
		$model = new PurchaseReceiptModel($id,24);
		if($model->getValue('status') != 1)
		{
			$result['error'] = "只有已保存状态的采购收货单才能审核！";
			Util::jsonExit($result);
		}
		$res = $model->check_caigou_info($id);
		if (!$res)
		{
			$result['error'] = "审核失败";
		}
		else
		{
			$result['success'] = true;
		}
		Util::jsonExit($result);
	}
	//取消单据
	public function cancelInfo($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = $params['id'];
		$model = new PurchaseReceiptModel($id,24);
		if($model->getValue('status') != 1)
		{
			$result['error'] = "单据状态错误，不能进行此操作";
			Util::jsonExit($result);
		}
		$res = $model->cancle_caigou_info($id,3,8);
		if (!$res)
		{
			$result['error'] = "操作失败";
		}
		else
		{
			$result['success'] = true;
		}
		Util::jsonExit($result);
	}
	//作废
	public function cancel($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = $params['id'];
		$model = new PurchaseReceiptModel($id,24);
		if($model->getValue('status') == 4)
		{
			$result['error'] = "单据已作废不能重复操作！";
			Util::jsonExit($result);
		}
		if($model->getValue('user_id') !== $_SESSION['userId'])
		{
			$result['error'] = "没有操作权限，无权作废他人单据！";
			Util::jsonExit($result);
		}
		$res = $model->cancle_caigou_info($id,4,8);
		if (!$res)
		{
			$result['error'] = "操作失败";
		}
		else
		{
			$result['success'] = true;
		}
		Util::jsonExit($result);
	}


	//采购收货单打印
	public function printDetail($params)
	{
		$id = $params['id'];
		$detailModel = new PurchaseReceiptDetailModel(23);
		$order_goods = $detailModel->getListForRid($id);
		$heji = array(
			"hanshuijia_heji" => 0,
			'zuanshixinxi'=>array(),
			'feiyongtongji'=>array("基本工费"=>0,"超石费"=>0,"其他工费"=>0,'税费'=>0,"配件成本"=>0),
			'jinliaoxinxi'=>array()

		);
        
		$bc_sn_ids=array();
		$apimodel = new ApiProcessorModel();
		foreach($order_goods as $key => $item)
		{
			// 明细统计
			//$order_goods[$key]["fushizhong"] = $item["fushizhong"]+$item["shi2zhong"]+$item['shi3zhong'];
			//$order_goods[$key]["fushishu"] = $item["fushilishu"]+$item["shi2lishu"]+$item['shi3lishu'];
			$order_goods[$key]["chengben_no_roudn"] = $item["net_gold_weight"]*$item["gold_loss"]*$item["gold_price"] + $item["main_stone_weight"]*$item["zhushidanjia"] + $item["fushizhong"]*$item["fushidanjia"] + $item["shi2zhong"]*$item["shi2danjia"]+ $item["shi3zhong"]*$item["shi3danjia"] + $item["work_fee"] + $item["extra_stone_fee"] + $item["other_fee"] +$item["fittings_cost_fee"];
			$order_goods[$key]["chengben"] = round($order_goods[$key]["chengben_no_roudn"], 2);

			$order_goods[$key]["hanshuijia_no_round"] = $order_goods[$key]["chengben_no_roudn"] + $item["tax_fee"];
			$order_goods[$key]["hanshuijia"] = round($order_goods[$key]["hanshuijia_no_round"], 2);
			$heji["hanshuijia_heji"] += $order_goods[$key]["hanshuijia_no_round"];
			$order_goods[$key]["shuifei_round"] = round($item["tax_fee"],2);
			$bc_sn_ids[$item["id"]] = preg_replace('/^[a-zA-Z]+/', '', $item["bc_sn"]);//str_replace("BC","",$item["bc_sn"]);
			
			
			$buchanInfo = $apimodel->GetProductInfo($item["bc_sn"]);
			$order_goods[$key]['is_alone'] = 0;
			if(isset($buchanInfo['is_alone']) && $buchanInfo['is_alone']){
			    $order_goods[$key]['is_alone'] = $buchanInfo['is_alone'];
			}
			
			//$if_kelaishi = "";
			// 配石统计
			if($item["main_stone"])
			{
				if(!isset($heji["zuanshixinxi"][$item["main_stone"]]))
					$heji["zuanshixinxi"][$item["main_stone"]] = array();

				if (!isset($heji["zuanshixinxi"][$item["main_stone"]][$item["zhushidanjia"]]))
					$heji["zuanshixinxi"][$item["main_stone"]][$item["zhushidanjia"]] = array('shuliang'=>0,'shizhong'=>0);

				$heji["zuanshixinxi"][$item["main_stone"]][$item["zhushidanjia"]]["shuliang"] += $item["main_stone_num"];
				//$heji["zuanshixinxi"][$item["main_stone"]] += $item["main_stone_num"];
				$heji["zuanshixinxi"][$item["main_stone"]][$item["zhushidanjia"]]["shizhong"] += $item["main_stone_weight"];
			}

			if($item["fushi"])
			{
				if(!isset($heji["zuanshixinxi"][$item["fushi"]]))
					$heji["zuanshixinxi"][$item["fushi"]] = array();

				if (!isset($heji["zuanshixinxi"][$item["fushi"]][$item["fushidanjia"]]))
					$heji["zuanshixinxi"][$item["fushi"]][$item["fushidanjia"]] = array('shuliang'=>0,'shizhong'=>0);

				$heji["zuanshixinxi"][$item["fushi"]][$item["fushidanjia"]]["shuliang"]+=$item["fushilishu"];
				$heji["zuanshixinxi"][$item["fushi"]][$item["fushidanjia"]]["shizhong"] += $item["fushizhong"];
			}
			if($item["shi2"])
			{
				if(!isset($heji["zuanshixinxi"][$item["shi2"]]))
					$heji["zuanshixinxi"][$item["shi2"]] = array();

				if (!isset($heji["zuanshixinxi"][$item["shi2"]][$item["shi2danjia"]]))
					$heji["zuanshixinxi"][$item["shi2"]][$item["shi2danjia"]] = array('shuliang'=>0,'shizhong'=>0);

				$heji["zuanshixinxi"][$item["shi2"]][$item["shi2danjia"]]["shuliang"]+= $item["shi2lishu"];
				$heji["zuanshixinxi"][$item["shi2"]][$item["shi2danjia"]]["shizhong"] += $item["shi2zhong"];
			}

			if($item['shi3'])
			{
				if(!isset($heji["zuanshixinxi"][$item["shi3"]]))
					$heji["zuanshixinxi"][$item["shi3"]] = array();

				if (!isset($heji["zuanshixinxi"][$item["shi3"]][$item["shi3danjia"]]))
					$heji["zuanshixinxi"][$item["shi3"]][$item["shi3danjia"]] = array('shuliang'=>0,'shizhong'=>0);

				$heji["zuanshixinxi"][$item["shi3"]][$item["shi3danjia"]]["shuliang"]+= $item["shi3lishu"];
				$heji["zuanshixinxi"][$item["shi3"]][$item["shi3maidanjia"]]["shizhong"] += $item["shi3zhong"];
			}

			if(!isset($heji["jinliaoxinxi"][$item["material"]]))
			{
				$heji["jinliaoxinxi"][$item["material"]] = array('zhongliang'=>0,'jinzhi'=>0);
			}
			// 金料统计
			$heji["jinliaoxinxi"][$item["material"]]["zhongliang"] += $item["net_gold_weight"];
			$heji["jinliaoxinxi"][$item["material"]]["jinzhi"] += $item["net_gold_weight"]*$item["gold_loss"]*$item["gold_price"];
			// 费用统计

			$heji["feiyongtongji"]["基本工费"] += $item["work_fee"];
			$heji["feiyongtongji"]["超石费"] += $item["extra_stone_fee"];
			$heji["feiyongtongji"]["其他工费"] += $item["other_fee"];
			$heji["feiyongtongji"]["税费"] += $item["tax_fee"];
			$heji["feiyongtongji"]["配件成本"] += $item["fittings_cost_fee"];
		}
		
		
		//根据布产号取出 app_order_details 里的  goods_type

		$heji["hanshuijia_heji"] = round($heji["hanshuijia_heji"], 2);
		$heji["feiyongtongji"]["税费"] = round($heji["feiyongtongji"]["税费"], 2);
		foreach($heji["jinliaoxinxi"] as $key => $item)
		{
			$heji["jinliaoxinxi"][$key]["pingjunjinjia"] = round($item["jinzhi"]/$item["zhongliang"], 2);
			$heji["jinliaoxinxi"][$key]["jinzhi"] = round($item["jinzhi"], 2);
		}

		$heji["goods_cnt"] = count($order_goods);
		$this->render('purchase_receipt_print.html',array(
			'view'   => new PurchaseReceiptView(new PurchaseReceiptModel($id,23)),
			'order_goods' => $order_goods,
	        'dd' => new DictView(new DictModel(1)),
			'heji' => $heji,
		));
	}

	//采购收货单导出
	public function exportData($params)
	{
		$id = $params['id'];
		$detailModel = new PurchaseReceiptDetailModel(23);
		$order_goods = $detailModel->getListForRid($id);

		$str = "序号,客户名,采购单号,布产号,款号, 模号, 戒托实际镶口, 款式分类(品名), 成品/空托, 手寸, 材质,净金重,主成色重(净金重),金耗,主成色买入单价(金价),主石(主石类别),主石粒数,主石重,主石颜色,主石净度,主石买入单价,副石(副石类别),副石粒数,副石重,副石买入单价,工费(买入工费),超石费,其它工费,配件成本,税费,客来石信息,证书号,石2(石2类别),石2粒数,石2重,石2买入单价,石3(石3类别),石3粒数,石3重,石3买入单价\n";

		foreach($order_goods as $key => $val)
		{
			$str .= $val['id'].",".$val['customer_name'].",".$val['purchase_sn'].",".$val['bc_sn'].",".$val['style_sn'].",".$val['factory_sn'].",".$val['ring_mouth'].",".$val['cat_type'].",".$val['is_cp_kt'].",".$val['hand_inch'].",".$val['material'].",".$val['gross_weight'].",".$val['net_gold_weight'].",".$val['gold_loss'].",".$val['gold_price'].",".$val['main_stone'].",".$val['main_stone_num'].",".$val['main_stone_weight'].",".$val['zhushiyanse'].",".$val['zhushijingdu'].",".$val['zhushidanjia'].",".$val['fushi'].",".$val['fushilishu'].",".$val['fushizhong'].",".$val['fushidanjia'].",".$val['work_fee'].",".$val['extra_stone_fee'].",".$val['other_fee'].",".$val['fittings_cost_fee'].",".$val['tax_fee'].",".$val['customer_info_stone'].",".$val['zhengshuhao'].",".$val['shi2'].",".$val['shi2lishu'].",".$val['shi2zhong'].",".$val['shi2danjia'].",".$val['shi3'].",".$val['shi3lishu'].",".$val['shi3zhong'].",".$val['shi3danjia']."\n";
		}
		header("Content-Type: application/force-download");//add by zhangruiying防止浏览器直接打开
		header("Content-Disposition: attachment;filename=caigoushouhuo.csv");
		echo iconv("utf-8","gbk", $str);
	}
}

?>