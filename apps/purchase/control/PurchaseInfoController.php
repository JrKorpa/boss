<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseInfoController.php
 *   @link		: 钻石 www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-08 12:26:47
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseInfoController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$purchaseType = new PurchaseTypeModel(23);
		$type_list = $purchaseType->getList(1);
		$Processor_Info = new ApiProcessorModel();
		$Processor_list = $Processor_Info->GetSupplierList();
		$this->render('purchase_info_search_form.html',
			array(
				'bar'=>Auth::getBar(),
				'dd'=>new DictView(new DictModel(1)),
				'type_list'=>$type_list,
				'Processor_list'=>$Processor_list,
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
			'p_sn'	=> _Request::get("p_sn"),
			't_id'	=> _Request::get("t_id"),
			'p_status'=> _Request::getString("p_status"),
			'make_uname'=> _Request::get("make_uname"),
//			'apply_uname'=> _Request::get("apply_uname"),
			'check_uname'=> _Request::get("check_uname"),
			'put_in_type'=> _Request::get("put_in_type"),
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		/*EDIT BY ZHANGRUIYING无用删除并把WHERE改为ARGS
		*/
		/*
		$where = array(
			'p_sn'			=> $args["p_sn"],
			't_id'			=> $args["t_id"],
			'p_status'		=> $args["p_status"],
//			'apply_uname'	=> $args["apply_uname"],
			'make_uname'	=> $args["make_uname"],
			'check_uname'	=> $args["check_uname"],
			'put_in_type'	=> $args["put_in_type"],
		);
		*/
		/*if($args['p_sn'])
		{
			$args['p_sn']=str_replace('，',' ',$args['p_sn']);
			$args['p_sn']=trim(preg_replace('/(\s+|,+)/',' ',$args['p_sn']));
			$args['p_sn']=explode(' ',$args['p_sn']);
		}*/

        if(SYS_SCOPE == 'zhanting'){
            $args['hidden'] = '0';
        }

		$dict = new DictView(new DictModel(1));
		$model = new PurchaseInfoModel(23);
		$data = $model->pageList($args,$page,10,false);

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'purchase_info_search_page';
		$this->render('purchase_info_search_list.html',array(
			'view'=>new PurchaseInfoView(new PurchaseInfoModel(23)),
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'dd' => $dict
		));
	}

	/**
	 *	add，渲染添加页面
	 *  有款采购页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$purchaseType = new PurchaseTypeModel(23);
		$type_list = $purchaseType->getList(1);
        $channelModel = new UserChannelModel(1);//渠道
        $allshop = $channelModel->getAllChannels();
		$result['content'] = $this->fetch('purchase_info.html',array(
			'view'=>new PurchaseInfoView(new PurchaseInfoModel(23)),
			'dd' => new DictView(new DictModel(1)),
			'type_list'=>$type_list,
			'is_style' => 1,
            'allshop' =>$allshop,
            'channel_ids' => array()
		));
		$result['title'] = '添加有款采购';
		Util::jsonExit($result);
	}


	/**
	 *	add，渲染添加页面
	 *  无款采购页面
	 */
	public function addNo ()
	{
		$result = array('success' => 0,'error' => '');
		$purchaseType = new PurchaseTypeModel(23);
		$type_list = $purchaseType->getList(1);
        $channelModel = new UserChannelModel(1);//渠道
        $allshop = $channelModel->getAllChannels();
		$result['content'] = $this->fetch('purchase_info.html',array(
			'view'=>new PurchaseInfoView(new PurchaseInfoModel(23)),
			'dd' => new DictView(new DictModel(1)),
			'type_list'=>$type_list,
			'is_style' => '0',
            'allshop' =>$allshop,
            'channel_ids' => array()
		));
		$result['title'] = '添加无款采购';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = intval($params["tab_id"]);
		$result = array('success' => 0,'error' => '');

		$pmodel = new PurchaseInfoModel($id,24);
		$p_status = $pmodel->getValue('p_status');
		$is_style = $pmodel->getValue('is_style');//是否有款采购
		if($p_status != 1 and $p_status != 4)//只有新增状态下和驳回的才能编辑
		{
			$result['content'] = "此状态不能编辑（只能在已保存未提交前或已驳回状态下才能编辑）";
			Util::jsonExit($result);
		}

		if($pmodel->getValue('make_uname') != $_SESSION['userName'])
		{
			$result['content'] = "只允许制单人编辑";
			Util::jsonExit($result);
		}
		$purchaseType = new PurchaseTypeModel(23);
		$type_list = $purchaseType->getList(1);
        $channelModel = new UserChannelModel(1);//渠道
        $allshop = $channelModel->getAllChannels();
        $channel_ids = $pmodel->getValue('channel_ids');
		$result['content'] = $this->fetch('purchase_info.html',array(
			'view'=>new PurchaseInfoView(new PurchaseInfoModel($id,23)),
			'dd' => new DictView(new DictModel(1)),
			'type_list'=>$type_list,
			'is_style' => $is_style,
            'allshop'  => $allshop,
            'channel_ids' =>explode(",",$channel_ids)
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);

		$this->render('purchase_info_show.html',array(
			'view'=>new PurchaseInfoView(new PurchaseInfoModel($id,23)),
			'id' => $id,
			'dd'=>new DictView(new DictModel(1)),
			'bar'=>Auth::getViewBar(),
		));
	}

	//采购单详情里的采购单明细
	public function showRecelist($params)
	{
		$id = intval($params["id"]);
		$purchase_sn = $params['purchase_sn'];

		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'id'	=>$id
		);

		$g_model = new PurchaseReceiptDetailModel(23);

		$where = array('purchase_sn'=>$purchase_sn);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$data = $g_model->pageList($where,$page,5,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'purchase_rece_show_page';

		$this->render('purchase_receipt_detail_list.html',array(
			'pa' =>Util::page($pageData),
			'dd' => new DictView(new DictModel(1)),
			'data' => $data,
		));
	}


	public function showlist($params)
	{
		$id = intval($params["id"]);

		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'id'	=>$id
		);

		$model = new PurchaseInfoModel($id,23);
		$is_style = $model->getValue('is_style');
		$html_name = $is_style?'purchase_info_show_list.html':'purchase_info_style_show_list.html';

		$g_model = new PurchaseGoodsModel(23);

		$where = array('pinfo_id'=>$id);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$data = $g_model->pageList($where,$page,10,false);

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'purchase_info_show_page';

		$product_list = array();
		$cat_list = array();
		if(!$is_style)
		{
			$apimodel = new ApiStyleModel();
			$product_type_list = $apimodel->getProductTypeInfo();
			foreach($product_type_list as $key => $val)
			{
				$product_list[$val['id']] = $val['name'];
			}
			$cat_type_list = $apimodel->getCatTypeInfo();
			foreach($cat_type_list as $key => $val)
			{
				$cat_list[$val['id']] = $val['name'];
			}
		}

        if($data['data']){
            foreach ($data['data'] as $key => $value) {
                $kezi = $this->replaceTsKezi($value['kezi']);
                $data['data'][$key]['kezi'] = $g_model->retWord($kezi);

                //备货占用信息
                $bhInfo = $g_model->getOutOrderInfo($value['id']);
                $data['data'][$key]['order_sn_guanl'] = '';
                $data['data'][$key]['dep_name_guanl'] = '';
                $data['data'][$key]['guanl_num'] = '';
                if(!empty($bhInfo)){
                    $data['data'][$key]['order_sn_guanl'] = implode("<br/>", array_column($bhInfo,'order_sn'));
                    $data['data'][$key]['dep_name_guanl'] = implode("<br/>", array_column($bhInfo,'dep_name'));
                    $data['data'][$key]['guanl_num'] = count($bhInfo);
                }
            }
        }
		$this->render($html_name,array(
			'pa'=>Util::page($pageData),
			'dd'=>new DictView(new DictModel(1)),
			'data' => $data,
			'product_list' => $product_list,
			'cat_list' => $cat_list
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$t_id			= _Post::getInt('t_id');
//		$apply_uname	= _Post::get('apply_uname');
		$put_in_type	= _Post::get('put_in_type');
		$p_info			= _Post::get('p_info');
		$is_tofactory	= _Post::get('is_tofactory')?_Post::get('is_tofactory'):0;
        $is_zhanyong   = _Post::get('is_zhanyong')?_Post::get('is_zhanyong'):0;
		$purchase_fee	= _Post::get('purchase_fee');
		$is_style		= _Post::get('is_style');
        $allshop        = _Request::getList('channel_id');
		if($t_id=='')
		{
			$result['error'] ="亲~ 采购分类必选";
			Util::jsonExit($result);
		}
//		if($apply_uname=='')
//		{
//			$result['error'] ="亲~ 申请人不能为空";
//			Util::jsonExit($result);
//		}

		if($put_in_type=='')
		{
			$result['error'] ="亲~ 采购方式必选";
			Util::jsonExit($result);
		}
		if($purchase_fee=='')
		{
			$result['error'] ="亲~ 采购申请费用不能为空";
			Util::jsonExit($result);
		}
		if($purchase_fee <= 0)
		{
			$result['error'] ="亲~ 采购申请费用必须大于0";
			Util::jsonExit($result);
		}

		if(preg_match('/[^\d\.\d]$/u', $purchase_fee))
		{
			$result['error'] ="亲~ 采购申请费用格式不正确";
			Util::jsonExit($result);
		}
        /*if(empty($allshop))
        {
            $result['error'] ="亲~ 请选择销售渠道";
            Util::jsonExit($result);
        }*/
		$olddo = array();
		$newdo=array(
			"p_sn"			=>CGD_PREFIX,
			"t_id"			=>$t_id,
			"p_sum"			=>0,
			"make_uname"	=>$_SESSION['userName'],
			"make_time"		=> date('Y-m-d H:i:s'),
			"check_uname"	=>"",
//			"apply_uname"	=>$apply_uname,
			"put_in_type"	=> $put_in_type,
			"p_info"		=> $p_info,
			"is_tofactory"	=> $is_tofactory,
            "is_zhanyong"  => $is_zhanyong,
			"purchase_fee"	=> $purchase_fee,
			"is_style"		=> $is_style,
            "channel_ids"   => ",".implode(",",$allshop).","
		);

        //未传附件明细
		if (!isset($_FILES['batch_goods_add']['error']) || $_FILES['batch_goods_add']['error'] != 0){
			$newmodel =  new PurchaseInfoModel(24);
			$res = $newmodel->saveData($newdo,$olddo);
			if($res !== false)
			{
				$newmodel =  new PurchaseInfoModel($res,24);
				$olddoo = $newmodel->getDataObject();
				$newdoo = array(
					'p_sn'	=> CGD_PREFIX.$res,
					'id'	=> $res
				);
				$res = $newmodel->saveData($newdoo,$olddoo);
	                        //添加日志
	                        $logModel = new PurchaseLogModel(24);
	                        $logModel->addLog($newdoo['id'], 1, "生成采购单");
				$result['success'] = 1;
			}
			else
			{
				$result['error'] = '数据添加失败，联系技术人员';
			}
		}else{

            
            $model =  new PurchaseGoodsModel(24);
			$file_array = explode(".",$_FILES['batch_goods_add']['name']);
			$file_extension = strtolower(array_pop($file_array));
			if($file_extension != 'csv')
			{
				$result['error'] = "请上传csv格式的文件";
				Util::jsonExit($result);
			}
			$file = $_FILES['batch_goods_add']['tmp_name'];
			$data = Upload::getCSV($file);
			if(empty($data)){
			    $result['error'] = "批量导入内容为空";
			    Util::jsonExit($result);
			}

			//* 款号,* 名称,* 数量,* 镶嵌要求	,(*)表面工艺,* 材质,(K金*)K金可做颜色,(*戒指)指圈,(*)钻石大小,(*)镶口,(特定)证书号,颜色,净度,刻字内容,布产备注
			$label = array('style_sn','goods_name','g_num','consignee','xiangqian','face_work','caizhi','18k_color','zhiquan','zuanshidaxiao','zhushi_num','xiangkou','zhengshuhao','cert', 'yanse', 'jingdu','kezi','note');

			$all_style = $model->getAllStyleSN();
			$caizhi_list1 = array("9K","10K","14K","18K");
			$caizhi_list2 = array("PT900","PT950","PT990","PT999","S925","S990","裸石","其它","千足金","千足金银","千足银","足金","无");
            $errStyleSn = array();
            $fac_list = array();


            //准备数据
			foreach ($data as $k=>$row) {                
                    if(isset($row[18])){
                        unset($row[18]);
                    }if(isset($row[19])){
                        unset($row[19]);
                    }if(isset($row[20])){
                        unset($row[20]);
                    }if(isset($row[21])){
                        unset($row[21]);
                    }if(isset($row[22])){
                        unset($row[22]);
                    }
               
				$data[$k] = array_combine($label,$row);
				$data[$k] = array_map('trim',$data[$k]);//批量过滤空格
				$data[$k]['pinfo_id'] = 0;
				if(empty($data[$k]['style_sn'])){
					$result['error'] = '第'.($k+2)."行,款号必填";
					Util::jsonExit($result);
				}
				if(!in_array(trim($data[$k]['style_sn']),$all_style)){
					$result['error'] = '第'.($k+2)."行,款号不存在或不是已审核状态";
					Util::jsonExit($result);
				}
				if(empty($data[$k]['goods_name'])){
					$result['error'] = '第'.($k+2)."行,商品名称必填";
					Util::jsonExit($result);
				}
				if(empty($data[$k]['g_num'])){
					$result['error'] = '第'.($k+2)."行,商品数量必填";
					Util::jsonExit($result);
				}
				if(!Util::isNum($data[$k]['g_num'])){
					$result['error'] = '第'.($k+2)."行,商品数量必须是数字";
					Util::jsonExit($result);
				}
				if(empty($data[$k]['xiangqian'])){
					$result['error'] = '第'.($k+2)."行,镶嵌要求必填";
					Util::jsonExit($result);
				}
				if(empty($data[$k]['caizhi'])){
					$result['error'] = '第'.($k+2)."行,材质必填";
					Util::jsonExit($result);
				}else{
				    $data[$k]['caizhi'] = strtoupper($data[$k]['caizhi']);
				}
				/*
                if(empty($data[$k]['p_sn_out']) && $is_djbh == '9'){
                    $result['error'] = '第'.($k+2)."行,外部单号必填";
                    Util::jsonExit($result);
                }
                if(empty($data[$k]['ds_xiangci']) && $is_djbh == '9'){
                    $result['error'] = '第'.($k+2)."行,单身-项次必填";
                    Util::jsonExit($result);
                }*/
                
                //检查特定属性字段，是否合法
				$res = $this->checkGoodsData($data[$k]);
				if($res['success']==0){
				     $result['error'] = '第'.($k+2)."行,".$res['error'];
				     Util::jsonExit($result);
				}else{
				     $data[$k] = $res['data'];//获取处理过的合法字段值
				}

                //获取巷口
                $xiangkouList = $this->getXiangkouList($data[$k]['style_sn']);
				//获取所有属性
				$attrlist = $this->getGoodsAttrList();				
				if(!in_array($data[$k]['caizhi'],$attrlist['caizhi_arr'])){
				    $result['error'] = '第'.($k+2)."行,材质【{$data[$k]['caizhi']}】在系统不存在";
				    Util::jsonExit($result);
				}
				if(in_array($data[$k]['caizhi'],$caizhi_list1)){
				    if(empty($data[$k]['18k_color']) || $data[$k]['18k_color']=="无"){
				        $result['error'] = '第'.($k+2)."行,材质为【{$data[$k]['caizhi']}】时，金色不能为空";
				        Util::jsonExit($result);
				    }
				}else if(in_array($data[$k]['caizhi'],$caizhi_list2)){
				    if(!empty($data[$k]['18k_color']) && $data[$k]['18k_color']!="无"){
				        $result['error'] = '第'.($k+2)."行,材质为【{$data[$k]['caizhi']}】时，金色必须为空";
				        Util::jsonExit($result);
				    }
				}
				if(!in_array($data[$k]['caizhi'],$caizhi_list2)){
    				if(!in_array($data[$k]['18k_color'],$attrlist['jinse_arr'])){
    				    $result['error'] = '第'.($k+2)."行,材质颜色【{$data[$k]['18k_color']}】在系统不存在";
    				    Util::jsonExit($result);
    				}
			    }

			    if(!in_array($data[$k]['caizhi'],$caizhi_list2)){
    				if(!in_array($data[$k]['18k_color'],$attrlist['jinse_arr'])){
    				    $result['error'] = '第'.($k+2)."行,材质颜色【{$data[$k]['18k_color']}】在系统不存在";
    				    Util::jsonExit($result);
    				}
			    }
			    //镶嵌方式 数据格式验证
			    if(!in_array($data[$k]['xiangqian'],$attrlist['xiangqian_arr'])){
		            $_xiangqian = '【'.implode("】【",$attrlist['xiangqian_arr']).'】';
			        $result['error'] = '第'.($k+2)."行,镶嵌方式【{$data[$k]['xiangqian']}】在系统不存在，请检文字是否有误。<br/><font style='color:red'>提示：镶嵌方式只能是:{$_xiangqian}</font>";
			        Util::jsonExit($result);
			    }
			    //表面工艺  数据格式验证
			    if(!in_array($data[$k]['face_work'],$attrlist['facework_arr'])){
			        $_facework = '【'.implode("】【",$attrlist['facework_arr']).'】';
			        $result['error'] = '第'.($k+2)."行,表面工艺【{$data[$k]['face_work']}】在系统不存在，请检文字是否有误。<br/><font style='color:red'>提示：表面工艺只能是:{$_facework}</font>";
			        Util::jsonExit($result);
			    }
			    //证书类型  数据格式验证
			    if(!empty($data[$k]['cert']) && !in_array($data[$k]['cert'],$attrlist['cert_arr'])){
			        $_facework = '【'.implode("】【",$attrlist['cert_arr']).'】';
			        $result['error'] = '第'.($k+2)."行,证书类型【{$data[$k]['cert']}】在系统不存在，请检文字是否有误。<br/><font style='color:red'>提示：证书类型只能是:{$_facework}</font>";
			        Util::jsonExit($result);
			    }
			    if(!empty($data[$k]['zhengshuhao']) && !empty($data[$k]['cert'])){
			        $res = $model->checkCertByCertId($data[$k]['zhengshuhao'],$data[$k]['cert']);
			        if($res === false){
			            $result['error'] = '第'.($k+2)."行,证书类型【{$data[$k]['cert']}】与证书号【{$data[$k]['zhengshuhao']}】对应的证书类型不匹配。";
			            Util::jsonExit($result);
			        }
			    }
                if(!empty($data[$k]['kezi'])){ //刻字验证
                    $apiStyle = new ApiStyleModel();
                    $styleAttrInfo = $apiStyle->GetStyleAttributeInfo(array('style_sn'=>$data[$k]['style_sn']));
                    $attrinfo = empty($styleAttrInfo) ? array() : $styleAttrInfo;
                    //刻字验证
                    $keziModel =  new PurchaseGoodsModel(24);
                    $allkezi = $keziModel->getKeziData();
                    //是否欧版戒 92
                    if(isset($attrinfo[92]['value']) && !empty($attrinfo[92]['value']) && trim($attrinfo[92]['value'] == '是')){
                        $str_count = $keziModel->pdKeziData($data[$k]['kezi'],$allkezi,1);
                        if($str_count['str_count']>=50){
                            $result['error'] = "第".($k+2)."行,欧版戒只能刻50位以内的任何字符！";
                            Util::jsonExit($result);
                        }
                        $data[$k]['kezi'] = $str_count['kezi'];
                    }else{
                        $str_count = $keziModel->pdKeziData($data[$k]['kezi'],$allkezi);
                        if($str_count['str_count']>6){
                            $result['error'] = "第".($k+2)."行,非欧版戒只能刻最多6位字符！";
                            Util::jsonExit($result);
                        }
                        if($str_count['err_bd'] != ''){
                            $result['error'] = "第".($k+2)."行,非欧版戒下列字符不可以刻：".$str_count['err_bd'];
                            Util::jsonExit($result);
                        }
                        $data[$k]['kezi'] = $str_count['kezi'];
                    }
                }                
                if(!in_array($data[$k]['xiangkou'], $xiangkouList)){
                    $errStyleSn[$data[$k]['style_sn']][] = $data[$k]['xiangkou'];
                }
                  
                //查找款式默认工厂  
                $factory = $model->getDefaultFactory($data[$k]['style_sn'],$data[$k]['xiangkou']);  
			    if(empty($factory)){
			            $result['error'] = '第'.($k+2)."行,款号【{$data[$k]['style_sn']}】找不到默认工厂。";
			            Util::jsonExit($result);
			    }else{
			    	$fac_list[$factory['factory_id']]['style_sn'][] = $k;
			    	$fac_list[$factory['factory_id']]['factory_name'] = $factory['factory_name']; 
			    }
			}
            $err_str = '保存成功！！！<br/>';
            if(!empty($errStyleSn)){
                foreach ($errStyleSn as $k_sn => $xiangkou) {
                    $err_str.= "提示：款式：".$k_sn."没有维护如下镶口（".implode(",", array_unique($xiangkou))."）<br/>";
                }
                $err_str.="请联系款式库专员核对！";
            }


            //数据入库
            //echo "<pre>";
            //print_r($fac_list);
            $main_data = $newdo;
            $newmodel =  new PurchaseInfoModel(24);




			try{
            $pdo = $newmodel->db()->db();
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务 			           
	            foreach ($fac_list as $key => $fac) {
					//$newmodel =  new PurchaseInfoModel(24);
					$newdo = $main_data;
					$olddo = array();
					//保存主表
					$res = $newmodel->saveData($newdo,$olddo);
					if($res !== false)
					{
						$pur_id = $res;						
						$model =  new PurchaseInfoModel($res,24);
						$olddoo = $model->getDataObject();
						$newdoo = array(
							'p_sn'	=> CGD_PREFIX.$res,
							'id'	=> $res,
							'prc_id' => $key,
							'prc_name' => $fac['factory_name'],
							'to_factory_time' => date('Y-m-d H:i:s',time()),
						);
						$res = $model->saveData($newdoo,$olddoo);
			            //添加日志
			            $logModel = new PurchaseLogModel(24);
			            $logModel->addLog($newdoo['id'], 1, "生成采购单");
			            //保存采购明细表
			            $model =  new PurchaseGoodsModel(24);
			            $items = array();
			            foreach ($fac['style_sn'] as $node_key=> $node) {
			            	$data[$node]['pinfo_id'] = $pur_id;
			            	$items[] = $data[$node];			            	
			            }
						$res = $model->batch_insert_notransaction($items,$pur_id);
						if($res !== false){
							//$result['success'] = 1;
			                //$result['msg'] = $err_str;
						}else{
							$pdo->rollback();//事务回滚
							$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交						
							$result['error'] = '保存采购明细失败，联系技术人员';
						}						
					}else{
						$pdo->rollback();//事务回滚
						$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交						
						$result['error'] = '数据添加失败，联系技术人员';
					}   

	            }
            	$pdo->commit();
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				$result['success'] = 1;
            }catch(Exception $e){
            	//echo json_encode($e);
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				$result['error'] = '数据添加失败，联系技术人员'.json_encode($e);
            }





		}	
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id				= _Post::getInt('id');
		$t_id			= _Post::get('t_id');
		$p_info			= _Post::get('p_info');
//		$apply_uname	= _Post::get('apply_uname');
		$put_in_type	= _Post::get('put_in_type');
		$_cls			= _Post::getInt('_cls');
		$tab_id			= _Post::getInt('tab_id');
		$is_tofactory	= _Post::get('is_tofactory')?_Post::get('is_tofactory'):0;
        $is_zhanyong   = _Post::get('is_zhanyong')?_Post::get('is_zhanyong'):0;
		$purchase_fee	= _Post::get('purchase_fee');
        $allshop        = _Request::getList('channel_id');

		if($t_id=='')
		{
			$result['error'] ="亲~ 采购分类不能为空！";
			Util::jsonExit($result);
		}
        if(empty($allshop))
        {
            $result['error'] ="亲~ 请选择销售渠道";
            Util::jsonExit($result);
        }
		$newmodel =  new PurchaseInfoModel($id,24);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			"id"			=>$id,
			"t_id"			=>$t_id,
			"p_info"		=> $p_info,
//			"apply_uname"	=>$apply_uname,
			"put_in_type"	=> $put_in_type,
			"is_tofactory"	=> $is_tofactory,
            "is_zhanyong"  => $is_zhanyong,
			"purchase_fee"	=> $purchase_fee,
			"p_status"=>1,
			"check_uname"=>'',
			"check_time"=>'0000-00-00 00:00:00',
            "channel_ids"   => ",".implode(",",$allshop).","
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;
			$result['title'] = $olddo['p_sn'];
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delete，作废--单据不能删除只能作废
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new PurchaseInfoModel($id,24);
                $log_model = new PurchaseLogModel(24);
		$p_status = $model->getValue('p_status');
		if($p_status != 2)
		{
			$result['error'] = "只有待审核状态下才能操作";
		}else{
			$model->setValue('p_status',4);
			$model->setValue('check_time',date('Y-m-d H:i:s'));
			$model->setValue('check_uname',$_SESSION['userName']);
			$res = $model->save(true);
			if($res !== false){
				$result['success'] = 1;
				$result['title'] = $model->getValue('p_sn');
                                //add log
                                $log_model->addLog($id, 4, '已驳回');
			}else{
				$result['error'] = "操作失败";
			}
		}
		Util::jsonExit($result);
	}

	/**
	* 提交
	*/
	public function subTj($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
                $log_model = new PurchaseLogModel(24);
		$model = new PurchaseInfoModel($id,24);
		$p_status = $model->getValue('p_status');

		if(!in_array($p_status, array(1,4)))		//采购单如果不是保存状态 或者 驳回状态 不让提交
		{
			$result['error'] = "只有已保存 或 已驳回 的状态下才能提交";
			Util::jsonExit($result);
		}

		if($model->getValue('make_uname') != $_SESSION['userName'])
		{
			$result['error'] = "只允许制单人提交";
			Util::jsonExit($result);
		}
		$pur_goods_model = new PurchaseGoodsModel(23);
		if(!$pur_goods_model->getCountForPid($id))
		{
			$result['error'] = "采购单没有数据信息，不能提交。";
			Util::jsonExit($result);
		}
		$model->setValue('p_status',2);

		//提交的时候，把审核人 一栏清空
		$model->setValue('check_uname' , '');
		$model->setValue('check_time' , '0000-00-00 00:00:00');

		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
			$result['title'] = $model->getValue('p_sn');
                        //写入日志
                        $log_model->addLog($id, 2, '已提交，待审核');//待审核
		}else{
			$result['error'] = "提交失败";
		}
		Util::jsonExit($result);
	}
	/*
	*add by zhangruiying
	*列表批量审核与详细页审核
	*/
	function MutiCheck($params)
	{
		$result = array('success' => 0,'error' => '','is_refresh'=>0);
		$ids =isset($params['_ids'])?$params['_ids']:array(intval($params['id']));
		$error='操作完成共操作{%num}条，成功{%s}条<br />';
                $logmodel = new PurchaseLogModel(24);
		$num=count($ids);
		$i=0;
		if(!empty($ids))
		{
			$model_api = new ApiProcessorModel();
			$CProcessorModel = new CProcessorModel(14);
			$pur_goods_model = new PurchaseGoodsModel(23);
			$attrmodel = new PurchaseGoodsAttrModel(23);
			$diamondmodel = new SelfDiamondModel(20);
			$peishiModel = new PeishiListModel(14);
			foreach($ids as $k=>$id)
			{
				$model = new PurchaseInfoModel($id,24);
				$p_sn=$model->getValue('p_sn');
				$p_status = $model->getValue('p_status');
				if($p_status != 2)
				{
					$error.= $p_sn.":此状态不能审核<br />";
					continue;
				}
				if($model->getValue('make_uname') == $_SESSION['userName'] && $_SESSION['userName']<>'admin')
				{
					$error.= $p_sn.":不能审核自己制作的采购单<br />";
					continue;
				}
				if(!$pur_goods_model->getCountForPid($id))
				{
					$error.= $p_sn.":采购单没有数据信息不能审核<br />";
					continue;
				}
				//获取分配工厂时间
				$to_factory_time = $model->getValue('to_factory_time');


				//如果是去工厂生产则推送数据到布产接口
				if($model->getValue('is_tofactory') == 1)
				{
					$factory_info=$this->getFactorylist(array($model->getValue('prc_id')));
					$opra_uname=isset($factory_info['data'][0]['opra_uname'])?$factory_info['data'][0]['opra_uname']:'';
					$arr = $pur_goods_model->get_data_goods($id);
					$goods_arr = array();
					foreach($arr as $key => $val)
					{
						$attr = $attrmodel->getGoodsAttr($val['id']);
						$goods_arr[$key]['p_id'] =	$val['id'];
						$goods_arr[$key]['p_sn'] =  $model->getValue('p_sn');
                        $goods_arr[$key]['t_id'] =  $model->getValue('t_id');
						$goods_arr[$key]['style_sn'] = $val['style_sn'];
						$goods_arr[$key]['num'] = $val['num'];
						$goods_arr[$key]['consignee']=$val['consignee'];
						$goods_arr[$key]['info'] = $val['info'];
						$goods_arr[$key]['attr'] = $attr;
						$goods_arr[$key]['create_user'] = $model->getValue('make_uname');
						$goods_arr[$key]['bc_style']='普通件';
						$goods_arr[$key]['goods_name']=$val['style_sn'];
						$goods_arr[$key]['xiangqian']=$val['xiangqian'];
						$goods_arr[$key]['customer_source_id'] = 0;
						$goods_arr[$key]['channel_id'] = 0;
						$goods_arr[$key]['caigou_info']=$model->getValue('p_info');
						$goods_arr[$key]['prc_id'] =$model->getValue('prc_id');
						$goods_arr[$key]['prc_name'] =$model->getValue('prc_name');
						$goods_arr[$key]['opra_uname']=$opra_uname;
						$goods_arr[$key]['to_factory_time']=$to_factory_time;

						$cert_num = $pur_goods_model->getDamindTypeById($val['id']);
						$cert_num2 = preg_replace('/[a-zA-Z]{0,10}/', '', $cert_num);
						$good_type = $diamondmodel->getGoodsTypeByCertId($cert_num,$cert_num2);
						if($good_type==2){
							$diamond_type=2;
						}else{
							$diamond_type=1;
						}
						$goods_arr[$key]['origin_dia_type']=$diamond_type;
						$goods_arr[$key]['diamond_type']=$diamond_type;
						$order_sn = $model->getValue('p_sn');
		                $is_style = $pur_goods_model->getStyleInfoByCgd($order_sn);
			            if($is_style ==1){
			            	//有款
							$qiban_type = 2;
						}else{
							$qiban_type = 0;
						}
						$goods_arr[$key]['qiban_type']=$qiban_type;  //起版类型

					}
					//$res =$model_api->AddProductInfo($goods_arr);//调用接口推送布产信息数据
					$res = $CProcessorModel->addProductInfo($goods_arr, $from_type=1);
					if($res['success']==0)//推送失败
					{
						$error.= $p_sn.':推送布产失败，原因'.$res['error']."<br />";
						continue;
					}
                    //add log
                    $buchan_info = $res['returnData'];
                    foreach($buchan_info as $k => $v){
                        $logmodel->addLog($id, 3, "审核/批量审核采购单，系统自动生成布产单：".$v['final_bc_sn']);
                        $bc_id = $v['final_bc_id'];
                        $res = $peishiModel->createPeishiList($bc_id,"insert","采购单审核");
                        if($res['success']==0){
                            $error = date('Y-m-d H:i:s')."--{$bc_id}生成配石单失败:".$res['error']."\r\n";
                            file_put_contents('caigou_createPeishiList.log',$error,FILE_APPEND);
                        }
                    }
                    //end log
				}
				//单据改为审核状态
				$model->setValue('p_status',3);
				$model->setValue('check_time',date('Y-m-d H:i:s'));
				$model->setValue('check_uname',$_SESSION['userName']);
				$model->save();
                //不用布产的生log
                if($model->getValue('is_tofactory') != 1){//不需布产的时候添加日志
                    $logmodel->addLog($id, 3, "审核采购单");
                }
                                //end
				$i++;

				//单个采购单更新分配工厂时间
				$productmodel = new SelfProductInfoModel(13);
				$to_factory_time = date('Y-m-d H:i:s',time());
				$p_sn = $model->getValue('p_sn');
				$res1 = $productmodel->updateTo_factory_timeByP_sn($p_sn,$to_factory_time);
				if($res1 ==false){
					$result['error'] ='布产单分配工厂时间更新失败！';
					Util::jsonExit($result);
				}

			}
		}
		$error=str_replace(array('{%s}','{%num}'),array($i,$num),$error);
		//如果有执行成功的需刷新页面
		if($i>0)
		{
			$result['is_refresh'] =1;
		}
		$result['success'] = 0;
		$result['error'] =$error;
		Util::jsonExit($result);
	}	
	
         //添加采购单的日志
        public function addLogs($param) {
            //var_dump($param);exit;
            $id = $param['id'];
            $this->render('app_purchase_action_info.html', array('id'=>$id,'action_id'=>'',
                //'tab_id' => _Request::getInt('tab_id'),
            ));
        }
        //保存添加的日志
        public function action_insert($param) {
            
            $id = $param['rece_id'];
           
            $logmodel = new PurchaseLogModel(24);
            $model = new PurchaseInfoModel($id,24);
            $status = $model->getValue('p_status');
            $result = array('success' => 0,'error' =>'');
            $logs_content = $param['logs_content'];
            if(empty($logs_content)){
                $result['error'] = "备注不能为空！";
                Util::jsonExit($result);
            }
            $remark = "<font color=red>".$logs_content.'</font>';
            $res = $logmodel->addLog($id, $status, $remark);
            if($res !== false)
            {
                $result['success'] = 1;
            }
            else
            {
                $result['error'] = '添加失败';
            }
            Util::jsonExit($result);
    }
	/*
	*add by zhangruiying
	*批量作废
	*/
	function Disabled($params)
	{
		$result = array('success' => 0,'error' => '','is_refresh'=>1);
		$ids =$params['_ids'];
		$count=count($ids);
		$error='';
		if(!empty($ids))
		{
			$model = new PurchaseInfoModel(23);
			//检查订单状态只有为新建状态的才能作废
			$ids=implode(',',$ids);
			$list=$model->GetListByAddStatus($ids);
			$num=count($list);
			$ids=explode(',',$ids);
			if(!empty($list))
			{
				foreach($ids as $k=>$id)
				{
					if(in_array($id,array_keys($list)))
					{
						unset($ids[$k]);
					}
				}
				$error.=implode(',',array_values($list)).'状态不是新增不允许作废！';

			}
			if(!empty($ids))
			{
				$ids=implode(',',$ids);
				$res=$model->MutiUpdateStatus($ids);
				if($res==false)
				{
					$result['error'] ='操作失败，程序异常请联系开发人员处理';
				}
				else
				{
					if($error!='')
					{
						$error='操作完成共操作{%num}条，成功{%s}条<br />'.$error;
						$error=str_replace(array('{%num}','{%s}'),array($count,($count-$num)),$error);
						$result['error'] =$error;
					}
					else
					{
						$result['success'] =1;
					}

				}
			}
			else
			{
				$result['error'] ='您所选的采购单状态都不允许作废';
			}
		}
		else
		{
			$result['error'] ='请选中要作废的采购单';
		}
		Util::jsonExit($result);

	}
	/*
	*add by zhangruiying
	*分配工厂
	*/
	function SendToFactory($params)
	{
		$id = intval($params["id"]);
		$tab_id = intval($params["tab_id"]);
		$result = array('success' => 0,'error' => '');
		$pmodel = new PurchaseInfoModel($id,24);
		$p_status = $pmodel->getValue('p_status');
		$is_tofactory = $pmodel->getValue('is_tofactory');
		if($p_status==3 or $p_status==4 or $is_tofactory!=1)
		{
			$result['content'] = "采购单状态必须为已保存，已提交并且需要工厂生产的才能分配工厂！";
			Util::jsonExit($result);
		}
		$gmodel=new PurchaseGoodsModel(23);
		$list=$gmodel->get_data_goods($id);
		$ids=array_column($list,'style_sn');
		//接口获取款式库款号对应的工厂这里只是工厂ID因为没有工厂名称所以下边作处理！！！！！
		$factory_list=$this->getFactoryIds(array('ids'=>$ids,'is_cancel'=>1));
		$fac_ids=array_column($factory_list,'factory_id');
//		//供应商库获取供应商名
		$factory_list=$this->getFactorylist($fac_ids);
		if(!isset($factory_list['data']) or empty($factory_list['data']))
		{
			$result['content'] = "该采购单下的所有款号均无对应的工厂请先维护款式库相关信息！";
			Util::jsonExit($result);
		}
		$result['content'] = $this->fetch('send_to_factory.html',array(
			'view'=>new PurchaseInfoView(new PurchaseInfoModel($id,23)),
			'dd' => new DictView(new DictModel(1)),
			'factory_list'=>$factory_list,
			'style_ids'=>implode(',',$ids)
		));
		$result['title'] = '分配工厂';
		Util::jsonExit($result);
	}
	/*
	*add by zhangruiying
	*根据多个款号获取对应的工厂列表
	*/
	function getFactoryIds($arr=array())
	{
		if(!empty($arr))
		{
		    $api = new ApiModel();
		    foreach ($arr as $key => $var) {
		        $keys[] = $key;
		        $vals[] = $var;
		    }

		    $ret = $api->style_api($keys, $vals, 'GetFactryInfo');
		    return $ret;
		}
		return array();

	}
	/*
	*add by zhangruiying
	*根据工厂ID获取工厂相关信息
	*/
	function getFactorylist($ids)
	{
		if(!empty($ids))
		{
			$ids=implode(",",$ids);
			
			$api = new ApiModel();
			return $api->process_api(array('ids'), array($ids), 'GetProcessorName');
		}
		return array();
	}
	/*
	*add by zhangruiying
	*采购单分配工厂
	*/
	function updateRelFactory($params)
	{
		$result = array('success' => 0,'error' => '');
		$factory_id= _Request::get("factory_id");
		$id= _Request::get("id");
		$style_ids= _Request::get("style_ids");
                $log_model = new PurchaseLogModel(24);
		if(empty($factory_id)or empty($style_ids))
		{
			$result['error']='分配工厂为空或该采购单下没有采购明细！';
		}
		else
		{
			
			
			
			$factory_list=explode(':',$factory_id);
			$factory_id=$factory_list[0];
			
			$res=$this->CheckStyleIsInFactory($factory_id,$style_ids);
			
			//判断:工厂是否是采购单所有款号的默认工厂或默认工厂的关联工厂
			$g_model = new PurchaseGoodsModel(23);
			$res2=$g_model->CheckIsInFactory($id,$factory_id);
			//$result['error']=$res2;
			//Util::jsonExit($result);
			if($res2['success']==0){
				$result['error']=$res2['error'];
				Util::jsonExit($result);
			}
			
			if($res['success']==1)
			{
				//更新采购单要分配的工厂
				$model=new PurchaseInfoModel($id,24);
				$olddo = $model->getDataObject();
                                //var_dump($olddo);exit;
				$to_factory_time = date('Y-m-d H:i:s',time());
				$factory_name=is_array($factory_list[1])?implode(':',$factory_list[1]):$factory_list[1];
				$newdo = array(
					'id'	=> $id,
					'prc_id'=>$factory_id,
					'prc_name'=>$factory_name,
					'to_factory_time'=>$to_factory_time
				);
				$res = $model->saveData($newdo,$olddo);
				if($res!=false)
				{
					$result['success']=1;
                                        //add log
                                        $p_sn = $olddo['p_sn'];
                                        $proApi = new ApiProcessorModel();
                                        $rs = $proApi->GetProcessorName($factory_id);
                                        if(isset($rs['opra_uname']) && $rs['opra_uname'] != ''){
                                            $opra_uname = $rs['opra_uname']; 
                                            
                                        }else{
                                            $opra_uname = '';
                                        }
                                        $log_model->addLog($id, $olddo['p_status'], "采购单分配工厂:".$factory_name.",跟单人:");
				}//.$opra_uname
				else
				{
					$result['error']='分配失败请联系开发人员！';
				}
			}
			else
			{
				$result['error']=$res['error'];
			}
		}

		Util::jsonExit($result);
	}
	/*
	*add by zhangruiying
	*当前采购单下的所有款号是否存在于当前选中的工厂
	*取当前工厂中所有款号与采购单款号比较是否有不存在的款
	*/
	function CheckStyleIsInFactory($factory_id=0,$style_ids='')
	{
		$result = array('success' => 0,'error' => '');
		$style_ids=str_replace(array(" ","\n","\r"),'',$style_ids);
		$style_ids=explode(',',$style_ids);
		$style_ids=array_unique($style_ids);
		$factory_ids=$this->getRelFactory($factory_id);//获取关联供应商
		$factory_ids=!empty($factory_ids)?array_column($factory_ids,'id'):array($factory_id);
		$ids=$this->getFactoryIds(array('ids'=>$style_ids,'factory_ids'=>$factory_ids,'is_cancel'=>1));
		//file_put_contents('./ruir20150615.txt',print_r($ids,true),FILE_APPEND);
		$ids=array_column($ids,'style_sn');
		$ids=array_unique($ids);
		$diff=array_diff($style_ids,$ids);
		if(!empty($diff))
		{
			$diff=implode(',',$diff);
			$result['error']='所选工厂和关联相同跟单人工厂均不存在款号'.$diff;
		}
		else
		{
			$result['success']=1;
		}
		return $result;
	}
	//获取工厂的关联工厂 add by zhangruiying
	function getRelFactory($factory_id=0)
	{
		if(!empty($factory_id))
		{
		    /*
			$ori_str=array('id'=>$factory_id,'opra_user_same'=>true);
			ksort($ori_str);
			$ori_str=json_encode($ori_str);
			$data=array("filter"=>$ori_str,"sign"=>md5('processor'.$ori_str.'processor'));
			$ret=Util::httpCurl(Util::getDomain().'/api.php?con=processor&act=GetRelFactoryIds',$data);
			$ret=json_decode($ret,true);
			return $ret['return_msg'];
			*/
			$api = new ApiModel();
			return $api->process_api(array('id','opra_user_same'), array($factory_id, true), 'GetRelFactoryIds');
		}
		return array();

	}
        // added by Linphie
        public function showLoglist($params)
	{
            //file_put_contents('./a.txt', 'aaaaa');
                        //var_dump($params);exit;
            
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'id' => _Request::get("id"),
		);
                //var_dump($args);exit;
		$page = _Request::getInt("page",1);
		$where = array();
		$where['id'] = $args['id'];
                $model = new PurchaseLogModel(24);
                $data = $model->getLog($where['id']);
if($data){
    foreach ($data as $key => $value) {
                    # code...
                    $data[$key]['remark'] = $this->replaceTsKezi($value['remark']);
                }

}
                
		
		$pageData = $data;
                //var_dump($pageData);exit;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'search_log_list';
		$this->render('log.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
                        'view'=>new PurchaseInfoView(new PurchaseInfoModel(23)),
                        'dd'=>new DictView(new DictModel(1)),
		));
	}


	/**
	 * 采购单的属性字段是否合法
	 * @param unknown $data
	 */
	protected function checkGoodsData($args){
	    $result = array('success' => 0,'error' => '');
	    $xiangqian = isset($args['xiangqian'])?$args['xiangqian']:'';
	    //主石单颗重验证	    
	    if(!empty($args['zuanshidaxiao']) && !is_numeric($args['zuanshidaxiao'])){
	        $result['error']="主石单颗重不合法，主石单颗重必须为数字!";
	        return $result;
	    }else if(isset($args['zuanshidaxiao'])){
	        $args['zuanshidaxiao'] = $args['zuanshidaxiao']/1;
	    }
	    //主石粒数验证
	    if(!empty($args['zhushi_num']) && !preg_match("/^\d+$/",$args['zhushi_num'])){
	        $result['error']="主石粒数不合法，主石粒数必须为正整数!";
	        return $result;
	    }else if(isset($args['zhushi_num'])){
	        $args['zhushi_num'] = $args['zhushi_num']/1;
	    }
	    if($xiangqian<>'不需工厂镶嵌'){
    	    if(isset($args['zuanshidaxiao']) && isset($args['zhushi_num'])){
    	        if(($args['zuanshidaxiao']==0 && $args['zhushi_num']>0) ||($args['zuanshidaxiao']>0 && $args['zhushi_num']==0)){
    	            $result['error']="主石单颗重和主石粒数不合要求，两者要么同时大于0，要么同时为空或0";
    	            return $result;
    	        }
    	    }
	    }
	    //镶口
	    if(!empty($args['xiangkou']) && !is_numeric($args['xiangkou'])){
	        $result['error']="镶口不合法，镶口必须为数字!";
	        return $result;
	    }else if(isset($args['xiangkou'])){
	        $args['xiangkou'] = $args['xiangkou']/1;
	        //镶口是否合法
	        if($xiangqian<>'不需工厂镶嵌'){
    	        if(!empty($args['xiangkou']) && isset($args['cart'])){
    	            if(!$this->GetStone((float)$args['xiangkou'],(float)$args['cart'])){
    	                $result['error'] = "镶口和石重不匹配";
    	                return $result;
    	            }
    	        }
	        }
	    }
	     
	    //金重
	    /*
	    if(!empty($args['jinzhong']) && !is_numeric($args['jinzhong'])){
	        $result['error']="金重不合法，金重必须为数字!";
	        return $result;
	    }else if(isset($args['jinzhong'])){
	        $args['jinzhong'] = $args['jinzhong']/1;
	    }*/
	    
	    //证书号
	    if(!empty($args['zhengshuhao']) && !preg_match("/^[\-|a-z|A-Z|0-9|\|]+$/is",$args['zhengshuhao'])){
	        $result['error']="证书号不合法，证书号只能包含【字母】【数字】【英文竖线】,英文竖线作为多个证书号分隔符。";
	        return $result;
	    }
	    //证书类型验证
	    if(!empty($args['zhengshuhao']) && isset($args['cert']) && ($args['cert']=="" ||$args['cert']=="无")){
	        $result['error']="证书类型不能为空或无，填写了证书号必须填写有效的证书类型";
	        return $result;	        
	    }
	    //指圈
	    if(!empty($args['zhiquan']) && !is_numeric($args['zhiquan'])){
	        $result['error']="指圈不合法，指圈必须为数字!";
	        return $result;
	    }else if(isset($args['zhiquan'])){
	        $args['zhiquan'] = $args['zhiquan']/1;
	    }
	    $result['success'] = 1;
	    $result['data'] = $args;
	    return $result;
	}


     //获取镶口信息
    private function getXiangkouList($style_sn='')
    {
        $apiStyle = new ApiStyleModel();
        $attres = $apiStyle->GetStyleAttribute($style_sn);
        $xiangkou_arr = array();
        if(!empty($attres) && is_array($attres)){
            $attr_list = array();
            //格式化属性数组结构，让attribute_code作为键值
            foreach($attres as $key=>$vo){
                 $attrcode = $vo['attribute_code'];                       
                 $attr_list[$attrcode] = $vo;
            }
            //获取材质属性列表,如果不为空覆盖默认材质属性列表
            if(!empty($attr_list['xiangkou']['value'])){
                $xiangkou_arr = $attr_list['xiangkou']['value'];
            }       
        }
        return $xiangkou_arr;
    }

	private function getGoodsAttrList($params=array()){
	    $style_sn = isset($params['style_sn'])?$params['style_sn']:'';
	    $output = isset($params['output'])?$params['output']:'';
	    //$caizhi_arr=array('1'=>'默认','2'=>'无','3'=>'9K','4'=>'10K','5'=>'18K','6'=>'24K','7'=>'PT950','8'=>'PT900','9'=>'S925' );
	    //$jinse_arr=array('1'=>'默认','2'=>'无','3'=>'按图做','4'=>'玫瑰金','5'=>'白','6'=>'黄','7'=>'黄白','8'=>'彩金','9'=>'分色' );
	    //定义默认属性列表
	    $goodsAttrModel = new GoodsAttributeModel(17);
	    $caizhi_arr = $goodsAttrModel->getCaizhiList(false);//false 读取属性数据库维护值 ，true 读取 固定维护值	
	    $jinse_arr  = $goodsAttrModel->getJinseList(false);//false 读取属性数据库维护值 ，true 读取 固定维护值
	    $cert_arr  = $goodsAttrModel->getCertList(false);//false 读取属性数据库维护值 ，true 读取 固定维护值
        $xiangqian_arr = $goodsAttrModel->getXiangqianListNew(false);
        $facework_arr = $goodsAttrModel->getFaceworkList(false);//false 读取属性数据库维护值 ，true 读取 固定维护值
	    if($style_sn !=''){
	        $apiStyle = new ApiStyleModel();
	        $attres = $apiStyle->GetStyleAttribute($style_sn);
	        if(!empty($attres) && is_array($attres)){
	            
	            $attr_list = array();
	            //格式化属性数组结构，让attribute_code作为键值
                foreach($attres as $key=>$vo){
                     $attrcode = $vo['attribute_code'];                       
                     $attr_list[$attrcode] = $vo;
                }
	        
    	        //获取材质属性列表,如果不为空覆盖默认材质属性列表
    	        if(!empty($attr_list['caizhi']['value'])){
   	                $caizhi_arr = $attr_list['caizhi']['value'];
    	        }
    	        //获取材质颜色属性列表,如果不为空覆盖默认材质颜色属性列表
    	        if(!empty($attr_list['caizhiyanse']['value'])){
   	                $jinse_arr = $attr_list['caizhiyanse']['value'];
    	        }
    	        if(!empty($attr_list['zhengshu']['value'])){
    	            $cert_arr = $attr_list['zhengshu']['value'];
    	        }    	        
	        }
	    } 
	    $data = array(
	        'caizhi_arr'=>$caizhi_arr,
	        'jinse_arr' =>$jinse_arr,
	        'cert_arr' =>$cert_arr,
	        'xiangqian_arr'=>$xiangqian_arr,
	        'facework_arr'=>$facework_arr 
	    );
	    if($output=="json"){
	        $result['success'] = 1;
	        $result['content'] = $data;
	        Util::jsonExit($data);	        
	    }else{
	        return $data;
	    }
	}




}

?>