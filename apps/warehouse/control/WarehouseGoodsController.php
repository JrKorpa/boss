<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 22:23:15
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseGoodsController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array("search","daochu","printCode","hbdown","bqj_dow");

	/****
	获取公司 列表
	****/
	public function company()
	{
		$model     = new CompanyModel(1);
		$company   = $model->getCompanyTree();//公司列表
		return $company;
	}
	/***
	获取有效的仓库
	***/
	public function warehouse()
	{
		$model_w	= new WarehouseModel(21);
		$warehouse  = $model_w->select(array('is_delete'=>1),array("id","name"));
		return $warehouse;
	}
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//获取主成色列表
		/*$model = new ApiStyleModel(21);
		$zhuchengseList = $model->getZhuchengseList();*/
		//材质（主成色）列表 （需求 ：http://192.168.1.57/browse/NEW-132  按照老系统主成色写死下拉框内容）
		/* $zhuchengseList = array(
			array('material_name' => '18K白金'),
			array('material_name' => '18K玫瑰金'),
			array('material_name' => '18K黄金'),
			array('material_name' => '18K彩金'),
			array('material_name' => 'PT950'),
			array('material_name' => 'PT900'),
			array('material_name' => 'PT990'),
			array('material_name' => '足金'),
			array('material_name' => '千足金'),
			array('material_name' => '9K白金'),
			array('material_name' => '9K玫瑰金'),
			array('material_name' => '9K黄金'),
			array('material_name' => '9K彩金'),
			array('material_name' => '14K金'),
			array('material_name' => 'Pd950'),
			array('material_name' => 'S925'),
			array('material_name' => '无'),
			array('material_name' => '10K白金'),
			array('material_name' => '10K玫瑰金'),
			array('material_name' => '10K黄金'),
			array('material_name' => '10K彩金'),
			array('material_name' => '14k白金'),
			array('material_name' => '14K玫瑰金'),
			array('material_name' => '14K黄金'),
			array('material_name' => '14K彩金'),
			array('material_name' => '18K黄白'),
			array('material_name' => '18K玫瑰白'),
		); */
		$goodsAttrModel = new GoodsAttributeModel(17);
		$caizhi_arr = $goodsAttrModel->getCaizhiList();
		$jinse_arr  = $goodsAttrModel->getJinseList();
                //供应商
		$model_p = new ApiProModel();
		$pro_list = $model_p->GetSupplierList(array('status'=>1));
		
		$model = new WarehouseGoodsModel(21);
		$productTypeArr=$model->getProducts_type();		
		$cutTypeArr=$model->getCat_type();
		//echo '<pre>';
		//print_r($productTypeArr);die();

		 /** 主石颜色/净度 **/
		//$color_arr = array('D','D-E','E','F','F-G','G','H','I','I-J','J','K','K-L','白色','彩钻','蓝','粉','橙','绿','红','香槟','格雷恩','紫','混色','蓝紫色','黑','变色','其他');
		//$clarty_arr= array('FL','IF','VVS', 'VVS1','VVS2','VS', 'VS1','VS2','SI', 'SI1','SI2','I1','P','不分级');
		$color_arr = $goodsAttrModel->getColorList();
		$clarty_arr= $goodsAttrModel->getClarityList();
		//类型/
		//$jintuo_type = array('成品','女戒','空托女戒');
		$companylist = $this->company();
		if(SYS_SCOPE=='zhanting' && $_SESSION['userName']=='欧丽亚'){
			 $companylist =null;
             $companylist[] =array('id'=>58,'company_name'=>'总公司');
		}
		$jinshi_type = array('3D','精工','普通');
		$zhengshuleibie = array('NGDTC','GIA','IGI','NGTC','HRD','AGL','EGL','NGGC','NGSTC','HRD-D','HRD-S','NGSTC');
		$luozuanzhengshu = array('NGDTC','GIA','IGI','NGTC','HRD','AGL','EGL','HRD-D','NGGC','HRD-S','NGSTC');
		$zhushi = array('钻石','彩钻','蓝宝','红宝','珍珠','翡翠','锆石','水晶','珍珠贝','和田玉','砭石','玛瑙','砗磲','淡水珍珠','海水珍珠');
		$chanpinxian = array('其他饰品','黄金等投资产品','素金饰品','黄金饰品及工艺品','钻石饰品','彩钻饰品','珍珠饰品','彩宝饰品','成品钻','翡翠饰品','配件及特殊包装','非珠宝');
		$xilie = $goodsAttrModel->getXilieList();
		$apiStyleModel = new ApiStyleModel();
		$catList = $apiStyleModel->getCatTypeInfo();
		$this->render('warehouse_goods_search_form.html',array(
			'bar'	=> Auth::getBar(),
			'dd'	=> new DictView(new DictModel(1)),
			'caizhi_arr'=>$caizhi_arr,
		    'jinse_arr'=>$jinse_arr,
			'catList' => $catList,
			'pro_list' => $pro_list,
			'warehouselist' => $this->warehouse(),	//仓库列表
			'companylist' => $companylist,		//公司列表
			'color_arr' =>$color_arr,
			'clarty_arr' => $clarty_arr,
			//'jintuo_type' => $jintuo_type,
			'jinshi_type' => $jinshi_type,
			'zhengshuleibie'=> $zhengshuleibie,
                        'luozuanzhengshu' =>$luozuanzhengshu,
			'zhushi' => $zhushi,
			'chanpinxian' => $chanpinxian,
			'xilie' =>$xilie,
			'chanpinxian1' =>$productTypeArr,
			'catList1' => $cutTypeArr
				
		));
	}

	/**
	 *	search，列表
	 */
	public function search($params)
	{
        ini_set('memory_limit','-1');
        set_time_limit(0);
		//echo '<div style="display:none;"><pre>';print_r($params);echo '</pre></div>';
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'down_info' => 	_Request::get('down_info')?_Request::get('down_info'):'',
			'put_in_type'=> _Request::get("put_in_type"),
			'weixiu_status'=> _Request::get("weixiu_status"),
			'is_on_sale'=> _Request::get("is_on_sale"),
			'caizhi'=> _Request::get("caizhi"),
		    'jinse'=> _Request::get("jinse"),
			'goods_id'	=> _Request::get("goods_id"),
                        'pinpai'         =>  _Request::get("pinpai"),
			'style_sn'	=> _Request::get("style_sn"),
			'cat_type'	=> _Request::get("cat_type"),
			'cat_type1'	=> _Request::get("cat_type1"),
			'company_id'	=> _Request::get("company_id"),
			'warehouse_id'	=> _Request::get("warehouse_id"),
			'zhengshuhao'   => _Request::get("zhengshuhao"),
			'order_goods_ids' => _Request::get("order_goods_ids"),//是否绑定
			'shoucun'    => _Request::get("shoucun"),
			'kucunstart' => _Request::get("kucun_start"),
			'kucunend'   => _Request::get("kucun_end"),
			'processor'  => _Request::get("processor"),
			'buchan'     => _Request::get("buchan"),//布产号
			'mohao'      => _Request::get("mohao"),
			'zhushi'     => _Request::get("zhushi"),
			'zhengshuleibie' => _Request::get("zhengshu_type"),
                        'luozuanzhengshu' => _Request::get("luozuanzhengshu"),
                        'xilie_name' => _Request::get("xilie_name"),
			'jinzhong_begin' => _Request::get("jinzhong_begin"),
			'jinzhong_end'   => _Request::get("jinzhong_end"),
			'zs_color'   => _Request::get("zs_color"),
			'zs_clarity' => _Request::get("zs_clarity"),
			'jintuo_type' => _Request::get('jintuo_type'),
			'jinshi_type' => _Request::get("jinshi_type"),
			'jiejia'     => _Request::get("jiejia"),
			'guiwei'     => _Request::get("guiwei"),
			'box_id'     => _Request::get("box_id"),
		
			'chanpinxian' => _Request::get("chanpinxian"),
			'chanpinxian1' => _Request::get("chanpinxian1"),
			'zhushi_begin'=> _Request::get("zhushi_begin"),
			'zhushi_end'  => _Request::get("zhushi_end"),
			'weixiu_company_id'  => _Request::get("weixiu_company_id"),
			'weixiu_warehouse_id'  => _Request::get("weixiu_warehouse_id"),
			'xinyaozhanshi'=>_Request::get("xinyaozhanshi"),
			'hbh'  => _Request::get("hbh"),
			'group_search' => _Request::get("group_search"),
			'orderby'=>_Request::get("__order"),
			'desc_or_asc'=>_Request::get("__desc_or_asc"),
            'company_id_list'=>'',
            'total_jinzhong'   => _Request::get("total_jinzhong"),
            'zhengshuhao2'         =>  _Request::get("zhengshuhao2"),
			
		);

        $is_company_check = Auth::user_is_from_base_company();
        if(!$is_company_check){
            $args['company_id_list'] = $_SESSION['companyId'];
            if (SYS_SCOPE == 'zhanting' && (empty($_SESSION['companyId']) || in_array($_SESSION['companyId'], array('0', '-1')))) {
            	echo '找不到您的归属公司，无法进行下一步操作，请确认您的公司列表';
            	exit;
            }
        }else{
        	if(SYS_SCOPE == 'zhanting' && $_SESSION['userName']=='欧丽亚'){
        		$args['company_id_list']=58;
        	}
        }
        

		$warehouseGoodsModelR = new WarehouseGoodsModel(55);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
        if ($args['xilie_name'] !=''){  
            $sql = "SELECT  id from front.app_style_xilie  WHERE `name` = '".$args['xilie_name']."' ";
            $id = $warehouseGoodsModelR->db()->getOne($sql);
            $sql = "SELECT style_sn from front.base_style_info WHERE   INSTR (xilie, ',".$id.",') > 0";
            $style_sn = $warehouseGoodsModelR->db()->getAll($sql);
            $xilie_val = array();
            foreach ($style_sn as $k => $v){
                $xilie_val[] ="'". $v['style_sn']."'";
            }

            $xilie_val = join(',', $xilie_val);
            $args['xilie_val'] = !empty($xilie_val)?$xilie_val:'NO';
        }else{
            $args['xilie_val'] = '';
        }
		$where = array(
			'put_in_type'	=> $args['put_in_type'],
			'weixiu_status'	=> $args['weixiu_status'],
			'is_on_sale'	=> $args['is_on_sale'],
			'caizhi'	=> $args['caizhi'],
		    'jinse'	=> $args['jinse'],
			'goods_id'		=> $args['goods_id'],
			'style_sn'		=> $args['style_sn'],
			'cat_type'		=> $args['cat_type'],
			'cat_type1'		=> $args['cat_type1'],
			'company_id'	=> $args['company_id'],
			'warehouse_id'	=> $args['warehouse_id'],
			'zhengshuhao'   => $args['zhengshuhao'],
			'order_goods_ids' => $args['order_goods_ids'],
			'shoucun'       => $args['shoucun'],
			'kucunstart'    => $args['kucunstart'],
			'kucunend'      => $args['kucunend'],
			'processor'     => $args['processor'],
			'buchan'        => $args['buchan'],
                        'pinpai'         => $args['pinpai'],
			'mohao'         => $args['mohao'],
			'zhushi'        => $args['zhushi'],
			'zhengshuleibie'=> $args['zhengshuleibie'],
                        'luozuanzhengshu' =>$args['luozuanzhengshu'],
                        'xilie_name' =>$args['xilie_val'],
			'jinzhong_begin'    => $args['jinzhong_begin'],
			'jinzhong_end'      => $args['jinzhong_end'],
			'zs_color'      => $args['zs_color'],
			'zs_clarity'    => $args['zs_clarity'],
			'jintuo_type'   => $args['jintuo_type'],
			'jinshi_type'   => $args['jinshi_type'],
			'jiejia'        => $args['jiejia'],
			'guiwei'        => $args['guiwei'],
			'box_id'        => $args['box_id'],
			
			'chanpinxian'   => $args['chanpinxian'],
			'chanpinxian1'   => $args['chanpinxian1'],
			'zhushi_begin'  => $args['zhushi_begin'],
			'zhushi_end'    => $args['zhushi_end'],
			'weixiu_company_id'    => $args['weixiu_company_id'],
			'weixiu_warehouse_id'    => $args['weixiu_warehouse_id'],
			'xinyaozhanshi'    => $args['xinyaozhanshi'],
			'hbh'    => $args['hbh'],
			'orderby'    => $args['orderby'],
			'desc_or_asc'    => $args['desc_or_asc'],
            'company_id_list' => $args['company_id_list'],
            'total_jinzhong'=> $args['total_jinzhong'],
            'zhengshuhao2'=> $args['zhengshuhao2'],
		);

        if(SYS_SCOPE == 'zhanting'){
            $where['hidden'] = '0';
        }
		//导出功能
		if($args['down_info']=='down_info'){
			/*
			$data = $warehouseGoodsModelR->pageList($where,$page,90000000,false);			
			$data = $this->search_tsyd($data);
			$this->addJiajiaChengben($data);
			$this->download($data);
			exit;
			*/
			$downloadfilename=KELA_ROOT."/public/download/download".time().".csv";
			$downloadfilename_path_dir = dirname($downloadfilename);
			if (!is_dir($downloadfilename_path_dir)) {				
				@mkdir($downloadfilename_path_dir, 0777, true);
			}			
			$countR=$warehouseGoodsModelR->getDownloadDataCount($where);		

			if($countR>=200000){
				$data=null;
				$this->putFile($data,$downloadfilename);
				$this->downloadfile($downloadfilename);
				exit(); 
			}
			set_time_limit(1800);
			for($k=1;$k< $countR/1000+1;$k++){
				$data=null;
				$data['data']=$warehouseGoodsModelR->getDownloadData($where,$k,1000);
				if($countR<3000){
				    $data = $this->search_tsyd($data);
	  		        $this->addJiajiaChengben($data);
	  		    }    
	  		    $this->putFile($data,$downloadfilename);		    
			}
	   
			$this->downloadfile($downloadfilename);
			exit();
		}
		if($args['down_info']=='group_search'){
			$data = $warehouseGoodsModelR->pageGroupList($where,$page,90000000,false);
			$this->groupdownload($data);
			exit;
		}
		if($args['hbh']==1){
            //$dd =new DictModel(1);
			//$data = $model->hbhpageList($where,$page,90000000,false);
			$data = $warehouseGoodsModelR->hbhpageListAll($where);
			//print_r($data);die;
			/*foreach($data['data'] as $k=>$v){
				if($v['bill_no'] !=''){
					if(isset($newdata[$v['goods_id']])){
						if($v['check_time'] > $newdata[$v['goods_id']]['check_time']){
							$newdata[$v['goods_id']]=$v;
						}
					}else{
						$newdata[$v['goods_id']]=$v;
					}
				}else{
					$newdata[$v['goods_id']]=$v;
				}
			}*/
			//var_dump($newdata);die;
            $title = array("货号","款号","款式类型","新款式类型","产品线","新产品线","名称","主成色","主成色重","指圈号","主石形状","主石重","主石净度","主石颜色","主石切工","主石对称","主石抛光","主石规格","荧光","证书号","标签价","金托类型(1:成品2:空托女戒3:空托)","柜位号","调拨单号","单据备注");
            Util::downloadCsv("婚博会专用导出",$title,$data['data']);
			//$this->hbhdownload($data['data']);
			exit;
		}
		  
	    //入库导出功能
	    if($args['down_info']=='down_storage_info'){
	        if(empty($where['goods_id'])){
	            echo '请输入货号导出';
	            exit;
	        }
	        $data = $warehouseGoodsModelR->pageList($where,0,20000,false);
	        $this->downloadStorageExcel($data['data']);
	    }
		//分组搜索
		if($args['group_search']==1){
			$data = $warehouseGoodsModelR->pageGroupList($where,$page,40,false);
			$gallerymodel = new ApiStyleModel();
			//循环插入图片进入数组
			if (!empty($data['data'])){
				foreach ($data['data'] as $k => $v){
					$img = $gallerymodel->getStyleGalleryList($v['goods_sn']);
					if (!empty($img)){
						$data['data'][$k]['img'] = current($img)['thumb_img'];
					}
				}
			
			}
		
			$tongji = $data['tongji'];
			$pageData = $data;
			$pageData['jsFuncs'] = 'warehouse_goods_search_page';
				
			$this->render('warehouse_goods_group_search_list.html',array(
				'pa'=>Util::page($pageData),
				'page_list'=>$data,
				'tongji'=>$tongji,
				'dd' => new DictView(new DictModel(1))
			));
			exit;
		}
		$data = $warehouseGoodsModelR->pageList($where,$page,40,false);
		//统计订单数量 以及订单名义价总金额
		$tongji = $data['tongji'];
		$pageData = $data;

		$data = $this->search_tsyd($data);		//寻找天生一对
		$this->addJiajiaChengben($data);
	    $pageData['jsFuncs'] = 'warehouse_goods_search_page';

		$this->render('warehouse_goods_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'tongji'=>$tongji,
			'dd' => new DictView(new DictModel(1))
		));
	}
	
	private function checkChengbenViewRights($model) {
	    
	    if ($_SESSION['userType']==1) return true;
	    
	    $ids =  preg_split('/,/', $_SESSION['qudao'], -1, PREG_SPLIT_NO_EMPTY);
	    $sale_persons = $model->getSalesPersons($ids);
	    
	    $userId = $_SESSION['userId'];
	    $userName = $_SESSION['userName'];
	    
	    foreach ($sale_persons as $data) {
	        $dp_leader_name = explode(',', $data['dp_leader_name']);
	        if (in_array($userName, $dp_leader_name)) return false;
	        
	        $dp_people_id = explode(',', $data['dp_people']);
	         if (in_array($userId, $dp_people_id)) return false;
	    }
	    
	    return true;
	}
   
	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_goods_info.html',array(
			'view'=>new WarehouseGoodsView(new WarehouseGoodsModel(1))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}



	/**
	 *	edit，渲染修改页面
	 */
	public function build_j ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	    $result['error']   = "智慧门店的相关业务已暂停,请移步到智慧门店系统操作";
                $result['success'] = 0;
                Util::jsonExit($result);      
        } 		
	    $result = array('success' => 0,'error' => '');
        $ids = _Post::getList('_ids');        

        $warehouseGoodsModel = new WarehouseGoodsModel(22);
        $salesModel = new SalesModel(27); 
        $proccesorModel = new SelfProccesorModel(13);
        
        $pdolist[13] = $proccesorModel->db()->db();
        $pdolist[22] = $warehouseGoodsModel->db()->db();
        $pdolist[27] = $salesModel->db()->db();
	    
	    try{
    	    foreach ($pdolist as $pdo){
    	        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
    	        $pdo->beginTransaction(); //开启事务
    	    }
        }catch (Exception $e){
            $error = "操作失败，事物回滚！提示：系统批量开启事物时发生异常！";
            Util::rollbackExit($error,$pdolist);
        }  
        foreach ($ids as $k => $good_id){         
           
            $goods_data = $warehouseGoodsModel->getGoodsByGoods_id($good_id);
            if(empty($goods_data)){
                continue;
            }
                        
            $is_on_sale = $goods_data['is_on_sale'];
            $order_goods_id = $goods_data['order_goods_id'];
            $goods_id = $goods_data['goods_id'];           
            if($is_on_sale != 2)
            {
                $error = "货品{$good_id}不是库存状态，只有库存状态才能解绑";
                Util::rollbackExit($error,$pdolist);
            }     
            
            if(!$order_goods_id)
            {
                $result['error']   = "货品{$good_id}未绑定，无需解绑";
                $result['success'] = 0;
                Util::jsonExit($result);
            }else{
                //仓库货品解绑
                $data = array("order_goods_id"=>'');
                $res = $warehouseGoodsModel->update($data,"goods_id='{$goods_id}'");
                if(!$res){
                    $error = "货品{$good_id}解绑失败,请重新尝试！";
                    Util::rollbackExit($error,$pdolist);
                }
            }

            $order_info = $salesModel->getOrderInfoByDetailId($order_goods_id);
            if(!empty($order_info)){
                $order_sn = $order_info['order_sn'];
                //清空订单商品明细货号
                $data = array('goods_id'=>'');
                $res = $salesModel->updateAppOrderDetail($data,"id={$order_goods_id}");
                if(!$res){
                    $error = "货品{$good_id}解绑失败,请重新尝试！error:".__LINE__;
                    Util::rollbackExit($error,$pdolist);
                }
                //解绑订单日志
                $remark = "商品列表，货号{$good_id}解绑";
                $res = $salesModel->AddOrderLog($order_sn, $remark);
                if(!$res){
                    $error = "货品{$good_id}解绑时,操作日志写入失败";
                    Util::rollbackExit($error,$pdolist);
                }
            }
            //添加布产单日志(如果有布产单)
            if($order_goods_id>0){
                $bc_id = $proccesorModel->selectProductInfo("id","p_id={$order_goods_id}",3);
                if($bc_id >0){
                    $buchanRemark = $remark = "商品列表，货号{$good_id}解绑";
                    $proccesorModel->addBuchanOpraLog($bc_id,$buchanRemark);
                }
           }
           
                        
        }
        //Util::rollbackExit("测试",$pdolist);
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
        
        
    public function BatchJiebang($params) {
            $model = new WarehouseGoodsModel(21);
            $ids = $params['_ids'];
            foreach($ids as $k => $v){
                if($model->jiebang($v))
		{
		    $result['success'] = 1;
		}else{
                    $result['error'] = '操作失败';
                }
		Util::jsonExit($result);
            }
        }
	
	public function getGoodsInfoBygoods_id()
	{
		$goods_id = _Post::get('goods_id');
		$file = _Post::getString('file_name');

		$arr = Util::iniToArray(APP_ROOT.'warehouse/data/'.$file);
		$billModel = new WarehouseBillGoodsModel(21);
		$goods = $billModel->getGoodsInfoByGoodsID($arr,$goods_id);
		$return_json = array();
		foreach ($goods as $v) {
			$return_jso[] = "{$v}";
		}
		print_r($return_json);exit;
	}

	public function show($params){
		if(isset($params['goods_is']) && !empty($params['goods_is'])){
			$model = new WarehouseGoodsModel(21);
			$id = $model->getIdBySN($params['goods_is']);
			if(!$id){echo '该商品不存在';exit;}
		}else{
			$id = intval($params["id"]);
		}
		$this->render('warehouse_goods_show.html',array(
			'view'=>new WarehouseGoodsView(new WarehouseGoodsModel($id,21)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new WarehouseGoodsModel($id,2);
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
	 * 二级联动 根据公司，获取选中公司下的仓库
	 */
	public function getTowarehouseId(){
		$to_company_id = _Request::get('id');
		$model = new WarehouseBillInfoMModel(21);
		$data = $model->getWarehouseByCompany($to_company_id);
		$num = count($data);
		$i = 0;
		foreach ($data as $vo){
		    $i ++;
		    $selected = $i==1 && $num==1 ?' selected':'';
		    echo "<option value='{$vo['id']}'{$selected}>{$vo['code']} | {$vo['name']}</option>";
		}
	}

	/**
	 * 批量复制
	 */
	public function batchCopy(){
		$bill_id = _Post::getInt('bill_id');
		$sql = "SELECT `id`,`goods_id` FROM `warehouse_bill_goods` WHERE `bill_id` = ".$bill_id;
		$data = $this->db()->getAll($sql);
		$data = array_column($data,'goods_id','id');
		$ids='';
		foreach ($data as $v) {
			$ids .= $v."\r\n";
		}
		header("Content-type:text/html;charset=utf-8");
		echo $ids;
	}
	//导出
	public function hbhdownload($newdata) {
		$dd =new DictModel(1);
		if ($newdata) {
			
			$down = $newdata;

			//$xls_content = "货号,入库方式,状态,款号,款式类型,产品线,模号,名称,证书号,主成色,主成色重,指圈大小,主石形状,主石重,主石净度,主石颜色,主石切工,主石对称,主石抛光,主石规格,荧光,成本价,名义成本,手寸,公司,库房,供货商,本库库龄,库龄,国际报价,折扣,柜位,金饰类型,金托类型,最后销售时间,是否绑定订单,是否结价,戒托实际镶口,副石1重量,副石1粒数,副石2重量,副石2粒数,证书号\r\n";
                        $xls_content = "货号,款号,款式类型,新款式类型,产品线,新产品线,名称,主成色,主成色重,指圈号,主石形状,主石重,主石净度,主石颜色,主石切工,主石对称,主石抛光,主石规格,荧光,证书号,标签价,金托类型(1:成品2:空托女戒3:空托),调拨单号,柜台号\r\n";
			$jiejia=array(0=>'未结价',1=>'结价');
			foreach ($down as $key => $val) {
					//$val['jiejia']=isset($jiejia[$val['jiejia']])?$jiejia[$val['jiejia']]:'';
					$xls_content .= $val['goods_id']. ",";
					$xls_content .= $val['goods_sn']. ",";
					$xls_content .= $val['cat_type']. ",";
					$xls_content .= $val['cat_type1']. ",";
					$xls_content .= $val['product_type']. ",";
					$xls_content .= $val['product_type1']. ",";
					$xls_content .= $val['goods_name']. ",";
					$xls_content .= $val['caizhi']. ",";
					$xls_content .= $val['jinzhong']. ",";
					$xls_content .= $val['shoucun']. ",";
					$xls_content .= $val['zhushixingzhuang']. ",";
					$xls_content .= $val['zuanshidaxiao']. ",";
					$xls_content .= $val['zhushijingdu']. ",";
					$xls_content .= $val['zhushiyanse']. ",";
					$xls_content .= $val['zhushiqiegong']. ",";
					$xls_content .= $val['duichen']. ",";
					$xls_content .= $val['paoguang']. ",";
					$xls_content .= $val['zhushiguige']. ",";
					$xls_content .= $val['yingguang']. ",";
					$xls_content .= $val['zhengshuhao']. ",";
					$xls_content .= $val['mingyichengben']. ",";
					$xls_content .= $dd->getEnum('warehouse_goods.tuo_type',$val['tuo_type']). ",";
					$xls_content .= $val['bill_no'] . ",";
					$xls_content .= $val['bill_note'] . "\n";
			}
		} else {
			$xls_content = '没有数据！';
		}
                /*
		header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
		echo iconv("utf-8", "gbk", $xls_content);
                 *
                 */
                header("Content-type:text/csv;charset=gbk");
                header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "婚博会专用导出" . date("Y-m-d")) . ".csv");
                header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
                header('Expires:0');
                header('Pragma:public');
                echo iconv("utf-8", "gbk//IGNORE", $xls_content);

                exit;

	}
	//导出
	public function download($data) {
		$dd =new DictModel(1);
		
		


		if ($data['data']) {

			$down = $data['data'];

	        $status=array();
	        $list=$dd->getEnumArray("warehouse.goods_status");		
			foreach ($list as $k=>$v) {
				$status['warehouse.goods_status'][$v['name']] = $v['label'];
			}		
	        $list=$dd->getEnumArray("warehouse_goods.tuo_type");		
			foreach ($list as $k=>$v) {
				$status['warehouse_goods.tuo_type'][$v['name']] = $v['label'];
			}
	        $list=$dd->getEnumArray("warehouse.put_in_type");		
			foreach ($list as $k=>$v) {
				$status['warehouse.put_in_type'][$v['name']] = $v['label'];
			}
	        $list=$dd->getEnumArray("warehouse.weixiu_status");		
			foreach ($list as $k=>$v) {
				$status['warehouse.weixiu_status'][$v['name']] = $v['label'];
			}			

			//    $xls_content = "产品线,新产品线,款式分类,新款式分类,货号,供应商,入库方式,状态,所在仓库,款号,模号,名称,名义价,原始采购价,最新采购价,材质,金重,手寸,金托类型,主石,主石粒数,主石形状,主石大小,主石颜色,主石净度,主石切工,抛光,对称,荧光,主石规格,副石1,副石1粒数,副石1重,副石2,副石2粒数,副石2重,证书号,证书类型,金饰类型,数量,是否结价,是否绑定,所在公司,戒托实际镶口,维修状态,维修公司,维修仓库,金耗,最后销售时间,本库库龄,库龄,国际报价,折扣,柜位\r\n";
			$show_private_data = SYS_SCOPE == 'boss' || Auth::user_is_from_base_company();
			if ($show_private_data) {
			     $xls_content = "产品线,新产品线,款式分类,新款式分类,货号,供应商,入库方式,状态,所在仓库,款号,模号,名称,名义价,原始采购价,最新采购价,加价成本价,材质,金重,手寸,金托类型,主石,主石粒数,主石形状,主石大小,主石颜色,主石净度,主石切工,抛光,对称,荧光,主石规格,副石1,副石1粒数,副石1重,副石2,副石2粒数,副石2重,证书号,证书类型,星耀证书号,金饰类型,数量,是否结价,是否绑定,所在公司,戒托实际镶口,维修状态,维修公司,维修仓库,金耗,最后销售时间,本库库龄,库龄,国际报价,折扣,品牌,裸钻证书类型,供应商货品条码,系列及款式归属,柜位,星耀钻石,买入工费单价\r\n";
			} else {
			     $xls_content = "产品线,新产品线,款式分类,新款式分类,货号,状态,所在仓库,款号,名称,加价成本价,材质,金重,手寸,金托类型,主石,主石粒数,主石形状,主石大小,主石颜色,主石净度,主石切工,抛光,对称,荧光,主石规格,副石1,副石1粒数,副石1重,副石2,副石2粒数,副石2重,证书号,证书类型,星耀证书号,金饰类型,数量,是否结价,是否绑定,所在公司,戒托实际镶口,最后销售时间,本库库龄,国际报价,折扣,品牌,裸钻证书类型,系列及款式归属,柜位,买入工费单价\r\n";
			}
			$jiejia=array(0=>'未结价',1=>'已结价',2=>'未知');//入库时出现"2"的情况 ,待查源头 marked by zzm 2015-11-18
			foreach ($down as $key => $val) {
				//$val['jiejia']=isset($jiejia[$val['jiejia']])?$jiejia[$val['jiejia']]:'';
				//$val['order_goods_id']=$val['order_goods_id']?'绑定':'未绑定';
				//$val['tuo_type']=$dd->getEnum('warehouse_goods.tuo_type',$val['tuo_type']);
				$val['mingyichengben']=Auth::canRead("warehouse_goods.nominal_price",2,$val['warehouse_id'])?$val['mingyichengben']:'';
				$val['jiajiachengben']=Auth::canRead("warehouse_goods.nominal_price",2,$val['warehouse_id'])?$val['jiajiachengben']:'';
				$val['chengbenjia']=Auth::canRead("warehouse_goods.purchase_price",2,$val['warehouse_id'])?$val['chengbenjia']:'';
				                $xls_content .= $val['product_type']. ",";
				                $xls_content .= $val['product_type1']. ",";
                                $xls_content .= $val['cat_type']. ",";
                                $xls_content .= $val['cat_type1']. ",";
                                $xls_content .= $val['goods_id']. ",";
                                if ($show_private_data) {
								    $xls_content .= $val['prc_id']. ",";//prc_name
                                    $xls_content .= isset($status['warehouse.put_in_type'][$val['put_in_type']]) ? $status['warehouse.put_in_type'][$val['put_in_type']] ."," : $val['put_in_type'] . ",";
								}
                                $xls_content .= isset($status['warehouse.goods_status'][$val['is_on_sale']]) ? $status['warehouse.goods_status'][$val['is_on_sale']]. "," : $val['is_on_sale']  .",";
                                $xls_content .= $val['warehouse']. ",";
                                $xls_content .= $val['goods_sn']. ",";
                                if ($show_private_data) {
                                    $xls_content .= $val['mo_sn']. ",";
                                }
                                $xls_content .= $val['goods_name']. ",";
                                if ($show_private_data) {
                                    $xls_content .= $val['mingyichengben']. ",";
                                    $xls_content .= $val['yuanshichengbenjia']. ",";
                                    $xls_content .= $val['chengbenjia']. ",";
                                }
                                $xls_content .= $val['jiajiachengben']. ",";
                                $xls_content .= $val['caizhi']. ",";
                                $xls_content .= $val['jinzhong']. ",";
                                $xls_content .= $val['shoucun']. ",";
                                $xls_content .= isset($status['warehouse_goods.tuo_type'][$val['tuo_type']]) ? $status['warehouse_goods.tuo_type'][$val['tuo_type']] .","  : $val['tuo_type'] . ",";
                                $xls_content .= $val['zhushi']. ",";
                                $xls_content .= $val['zhushilishu']. ",";
                                $xls_content .= $val['zhushixingzhuang']. ",";
                                $xls_content .= $val['zuanshidaxiao']. ",";
                                $xls_content .= $val['zhushiyanse']. ",";
                                $xls_content .= $val['zhushijingdu']. ",";
                                $xls_content .= $val['zhushiqiegong']. ",";
                                $xls_content .= $val['paoguang']. ",";
                                $xls_content .= $val['duichen']. ",";
                                $xls_content .= $val['yingguang']. ",";
                                $xls_content .= $val['zhushiguige']. ",";
                                $xls_content .= $val['fushi']. ",";
                                $xls_content .= $val['fushilishu']. ",";
                                $xls_content .= $val['fushizhong']. ",";
                                $xls_content .= $val['shi2']. ",";
                                $xls_content .= $val['shi2lishu']. ",";
                                $xls_content .= $val['shi2zhong']. ",";
                                $xls_content .= $val['zhengshuhao']. ",";
                                $xls_content .= $val['zhengshuleibie']. ",";
                                $xls_content .= $val['gemx_zhengshu']. ",";
                                $xls_content .= $val['ziyin']. ",";
                                $xls_content .= $val['num'].",";
                                /*if ($val['jiejia'] == 0) $xls_content .= "未结价". ",";
                                if ($val['jiejia'] == 1) $xls_content .= "已结价". ",";*/
                                $xls_content .= $jiejia[$val['jiejia']]. ",";

                                if($val['order_goods_id'] == 0 || $val['order_goods_id'] == ''){
                                    $xls_content .= "未绑定". ",";
                                }else{
                                    $xls_content .= "绑定". ",";
                                }
                                $xls_content .= $val['company']. ",";
                                $xls_content .= $val['jietuoxiangkou']. ",";
                                if ($show_private_data) {
                                    if ($weixiu_status = isset($status['warehouse.weixiu_status'][$val['weixiu_status']]) ? $status['warehouse.weixiu_status'][$val['weixiu_status']] : $val['weixiu_status'] ){
                                        $xls_content .= $weixiu_status. ",";
                                    }else{
                                        $xls_content .= '--'. ",";
                                    }
                                    $xls_content .= $val['weixiu_company_name']. ",";
                                    $xls_content .= $val['weixiu_warehouse_name']. ",";
                                    $xls_content .= $val['jinhao']. ",";
			                    }
                                $xls_content .= $val['account_time'] . ",";
                                $kuling = ceil((time() - strtotime($val['addtime'])) / (3600 * 24)).'天';
				if (empty($val['change_time']))
				{
                                    $thiskuling = '0';
				}else{
                                    $thiskuling = ceil((time() - strtotime($val['change_time'])) / (3600 * 24)) . '天';
				}
				$xls_content .=$thiskuling. ",";//本库库龄
				if ($show_private_data) {
				    $xls_content .= $kuling . ",";//库龄
				}
				$xls_content .= $val['guojibaojia'] . ",";
				$xls_content .= $val['zuanshizhekou'] . ",";
                $xls_content .= $val['pinpai'] . ",";
                $xls_content .= $val['luozuanzhengshu'] . ",";
                if ($show_private_data) {
                    $xls_content .= $val['supplier_code'] . ",";
                }
                $xls_content .= $val['xilie_name'] . ",";
                 if($val['xinyaozhanshi']==0){
                 	$xinyaozhanshi='未知';
                 }else if($val['xinyaozhanshi']==1){
                 	$xinyaozhanshi='是';
                 }else if($val['xinyaozhanshi']==2){
                 	$xinyaozhanshi='否';
                 }                             
				$xls_content .= $val['box_sn'] . ",";
				if ($show_private_data) {
				    $xls_content .= $xinyaozhanshi . ",";
				}
				$xls_content .= $val['mairugongfeidanjia'] . "\n";


                                /*原来的排列顺序
                                $xls_content .= $val['goods_id'] . ",";
				$xls_content .= $dd->getEnum('warehouse.put_in_type',$val['put_in_type']) . ",";
				$xls_content .= $dd->getEnum('warehouse.goods_status',$val['is_on_sale']). ",";
				$xls_content .= $val['goods_sn'] . ",";
				$xls_content .= $val['cat_type'] . ",";
				$xls_content .= $val['product_type'] . ",";
				$xls_content .= $val['mo_sn'] . ",";
				$xls_content .= $val['goods_name'] . ",";
                                $xls_content .= $val['zhengshuhao'].",";//证书号
				$xls_content .= $val['caizhi'] . ",";//主成色
				$xls_content .= $val['jinzhong'] . ",";//主成色重
				$xls_content .= $val['shoucun'] . ",";//指圈大小
				$xls_content .= $val['zhushixingzhuang'] . ",";//主石形状
				$xls_content .= $val['zuanshidaxiao'] . ",";//主石重
				$xls_content .= $val['zhushijingdu'] . ",";//主石净度
				$xls_content .= $val['zhushiyanse'] . ",";//主石颜色
				$xls_content .= $val['zhushiqiegong'] . ",";//主石切工
				$xls_content .= $val['duichen'] . ",";//主石对称
				$xls_content.=$val['paoguang'].",";//主石抛光
				$xls_content.=$val['zhushiguige'].",";//规格
				$xls_content.=$val['yingguang'].",";
				$xls_content .= $val['chengbenjia'] . ",";
				$xls_content .= $val['mingyichengben'] . ",";
				$xls_content .= $val['shoucun'] . ",";
				$xls_content .= $val['company'] . ",";
				$xls_content .= $val['warehouse'] . ",";
				$xls_content .= $val['prc_name'] . ",";
				$kuling = ceil((time() - strtotime($val['addtime'])) / (3600 * 24)).'天';
				if (empty($val['change_time']))
				{
				$thiskuling = '0';
				}else{
				$thiskuling = ceil((time() - strtotime($val['change_time'])) / (3600 * 24)) . '天';
				}
				$xls_content .=$thiskuling. ",";//本库库龄
				$xls_content .= $kuling . ",";//库龄
				$xls_content .= $val['guojibaojia'] . ",";
				$xls_content .= $val['zuanshizhekou'] . ",";
				$xls_content .= $val['box_sn'] . ",";
				$xls_content .= $val['huopin_type'] . ",";
				$xls_content .= $val['tuo_type'] . ",";
				$xls_content .= $val['account_time'] . ",";
				$xls_content .= $val['order_goods_id'] . ",";
				$xls_content .= $val['jiejia'] . ",";
				$xls_content .= $val['jietuoxiangkou'] . ",";
				$xls_content .= $val['fushizhong'] . ",";
				$xls_content .= $val['fushilishu'] . ",";
				$xls_content .= $val['shi2zhong'] . ",";
				$xls_content .= $val['shi2lishu'] . ",";
				$xls_content .= $val['zhengshuhao'] . "\n";
                                 *
                                 */

			}
		} else {
			$xls_content = '没有数据！';
		}
                /*
		header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
		echo iconv("utf-8", "gbk", $xls_content);
                 *
                 */
                header("Content-type:text/csv;charset=gbk");
                header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "export_" . date("Y-m-d")) . ".csv");
                header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
                header('Expires:0');
                header('Pragma:public');
                echo iconv("utf-8", "gbk//IGNORE", $xls_content);

                exit;

	}
	public function putFile($data,$filename) {
		$dd =new DictModel(1);

		if ($data['data']) {

			$down = $data['data'];

	        $status=array();
	        $list=$dd->getEnumArray("warehouse.goods_status");		
			foreach ($list as $k=>$v) {
				$status['warehouse.goods_status'][$v['name']] = $v['label'];
			}		
	        $list=$dd->getEnumArray("warehouse_goods.tuo_type");		
			foreach ($list as $k=>$v) {
				$status['warehouse_goods.tuo_type'][$v['name']] = $v['label'];
			}
	        $list=$dd->getEnumArray("warehouse.put_in_type");		
			foreach ($list as $k=>$v) {
				$status['warehouse.put_in_type'][$v['name']] = $v['label'];
			}
	        $list=$dd->getEnumArray("warehouse.weixiu_status");		
			foreach ($list as $k=>$v) {
				$status['warehouse.weixiu_status'][$v['name']] = $v['label'];
			}			

			//    $xls_content = "产品线,新产品线,款式分类,新款式分类,货号,供应商,入库方式,状态,所在仓库,款号,模号,名称,名义价,原始采购价,最新采购价,材质,金重,手寸,金托类型,主石,主石粒数,主石形状,主石大小,主石颜色,主石净度,主石切工,抛光,对称,荧光,主石规格,副石1,副石1粒数,副石1重,副石2,副石2粒数,副石2重,证书号,证书类型,金饰类型,数量,是否结价,是否绑定,所在公司,戒托实际镶口,维修状态,维修公司,维修仓库,金耗,最后销售时间,本库库龄,库龄,国际报价,折扣,柜位\r\n";
			$show_private_data = SYS_SCOPE == 'boss' || Auth::user_is_from_base_company();
			if ($show_private_data) {
			     $xls_content = "产品线,新产品线,款式分类,新款式分类,货号,供应商,入库方式,状态,所在仓库,款号,模号,名称,名义价,原始采购价,舟山原始成本价,最新采购价,加价成本价,主成色买入单价,材质,金重,手寸,金托类型,主石,主石粒数,主石形状,主石大小,主石颜色,主石净度,主石切工,抛光,对称,荧光,主石规格,副石1,副石1粒数,副石1重,副石2,副石2粒数,副石2重,副石3,副石3粒数,副石3重,证书号,证书类型,星耀证书号,金饰类型,数量,是否结价,是否绑定,所在公司,戒托实际镶口,维修状态,维修公司,维修仓库,金耗,最后销售时间,本库库龄,库龄,国际报价,折扣,品牌,裸钻证书类型,供应商货品条码,系列及款式归属,柜位,星耀钻石,买入工费单价,经销商批发价,管理费,标签价,主石包号,副石包号,石2包号,石3包号\r\n";
			} else {
			     $xls_content = "产品线,新产品线,款式分类,新款式分类,货号,状态,所在仓库,款号,名称,加价成本价,主成色买入单价,材质,金重,手寸,金托类型,主石,主石粒数,主石形状,主石大小,主石颜色,主石净度,主石切工,抛光,对称,荧光,主石规格,副石1,副石1粒数,副石1重,副石2,副石2粒数,副石2重,副石3,副石3粒数,副石3重,证书号,证书类型,星耀证书号,金饰类型,数量,是否结价,是否绑定,所在公司,戒托实际镶口,最后销售时间,本库库龄,国际报价,折扣,品牌,裸钻证书类型,系列及款式归属,柜位,买入工费单价,经销商批发价,管理费,标签价,主石包号,副石包号,石2包号,石3包号\r\n";
			}
			$jiejia=array(0=>'未结价',1=>'已结价',2=>'未知');//入库时出现"2"的情况 ,待查源头 marked by zzm 2015-11-18
			$xls_content = iconv('utf-8', 'GB18030', $xls_content);
            //file_put_contents($filename,chr(239).chr(187).chr(191),FILE_APPEND); 
            file_put_contents($filename,$xls_content,FILE_APPEND);
            $show_private_data = SYS_SCOPE == 'boss' || Auth::user_is_from_base_company();                
			foreach ($down as $key => $val) {
				$xls_content=null;
				$val['mingyichengben']=Auth::canRead("warehouse_goods.nominal_price",2,$val['warehouse_id'])?$val['mingyichengben']:'--';
				$val['jiajiachengben']=Auth::canRead("warehouse_goods.nominal_price",2,$val['warehouse_id'])?$val['jiajiachengben']:'--';
				$val['chengbenjia']=Auth::canRead("warehouse_goods.purchase_price",2,$val['warehouse_id'])?$val['chengbenjia']:'--';
				if(!empty($val['jingxiaoshangchengbenjia'])){
				    $val['jingxiaoshangchengbenjia'] = (float)$val['jingxiaoshangchengbenjia']-(float)$val['management_fee'];
				}else{
				    $val['jingxiaoshangchengbenjia'] = 0;
				}
				$val['jingxiaoshangchengbenjia']=Auth::canRead("warehouse_goods.jingxiaoshangchengbenjia",2,$val['warehouse_id'])?$val['jingxiaoshangchengbenjia']:'--';
				$val['management_fee']=Auth::canRead("warehouse_goods.management_fee",2,$val['warehouse_id'])?$val['management_fee']:'--';
				                $xls_content .= $val['product_type']. ",";
				                $xls_content .= $val['product_type1']. ",";
                                $xls_content .= $val['cat_type']. ",";
                                $xls_content .= $val['cat_type1']. ",";
                                $xls_content .= $val['goods_id']. ",";
                                if ($show_private_data) {
								    $xls_content .= $val['prc_id']. ",";//prc_name
                                    $xls_content .= isset($status['warehouse.put_in_type'][$val['put_in_type']]) ? $status['warehouse.put_in_type'][$val['put_in_type']] ."," : $val['put_in_type'] . ",";
								}
                                $xls_content .= isset($status['warehouse.goods_status'][$val['is_on_sale']]) ? $status['warehouse.goods_status'][$val['is_on_sale']]. "," : $val['is_on_sale']  .",";
                                $xls_content .= $val['warehouse']. ",";
                                $xls_content .= $val['goods_sn']. ",";
                                if ($show_private_data) {
                                    $xls_content .= $val['mo_sn']. ",";
                                }
                                $xls_content .= $val['goods_name']. ",";
                                if ($show_private_data) {
                                    $xls_content .= $val['mingyichengben']. ",";
                                    $xls_content .= $val['yuanshichengbenjia']. ",";                                    
                                    $xls_content .= $val['yuanshichengbenjia_zs']. ",";
                                    $xls_content .= $val['chengbenjia']. ",";
                                }
                                $xls_content .= $val['jiajiachengben']. ",";
                                $xls_content .= $val['zhuchengsemairudanjia']. ",";
                                $xls_content .= $val['caizhi']. ",";
                                $xls_content .= $val['jinzhong']. ",";
                                $xls_content .= $val['shoucun']. ",";
                                $xls_content .= isset($status['warehouse_goods.tuo_type'][$val['tuo_type']]) ? $status['warehouse_goods.tuo_type'][$val['tuo_type']] .","  : $val['tuo_type'] . ",";
                                $xls_content .= $val['zhushi']. ",";
                                $xls_content .= $val['zhushilishu']. ",";
                                $xls_content .= $val['zhushixingzhuang']. ",";
                                $xls_content .= $val['zuanshidaxiao']. ",";
                                $xls_content .= $val['zhushiyanse']. ",";
                                $xls_content .= $val['zhushijingdu']. ",";
                                $xls_content .= $val['zhushiqiegong']. ",";
                                $xls_content .= $val['paoguang']. ",";
                                $xls_content .= $val['duichen']. ",";
                                $xls_content .= $val['yingguang']. ",";
                                $xls_content .= $val['zhushiguige']. ",";
                                $xls_content .= $val['fushi']. ",";
                                $xls_content .= $val['fushilishu']. ",";
                                $xls_content .= $val['fushizhong']. ",";
                                $xls_content .= $val['shi2']. ",";
                                $xls_content .= $val['shi2lishu']. ",";
                                $xls_content .= $val['shi2zhong']. ",";
                                $xls_content .= $val['shi3']. ",";
                                $xls_content .= $val['shi3lishu']. ",";
                                $xls_content .= $val['shi3zhong']. ",";
                                $xls_content .= $val['zhengshuhao']. ",";
                                $xls_content .= $val['zhengshuleibie']. ",";
                                $xls_content .= $val['gemx_zhengshu']. ",";
                                $xls_content .= $val['ziyin']. ",";
                                $xls_content .= $val['num'].",";
                                /*if ($val['jiejia'] == 0) $xls_content .= "未结价". ",";
                                if ($val['jiejia'] == 1) $xls_content .= "已结价". ",";*/
                                $xls_content .= $jiejia[$val['jiejia']]. ",";

                                if($val['order_goods_id'] == 0 || $val['order_goods_id'] == ''){
                                    $xls_content .= "未绑定". ",";
                                }else{
                                    $xls_content .= "绑定". ",";
                                }
                                $xls_content .= $val['company']. ",";
                                $xls_content .= $val['jietuoxiangkou']. ",";
                                if ($show_private_data) {
                                    if ($weixiu_status = isset($status['warehouse.weixiu_status'][$val['weixiu_status']]) ? $status['warehouse.weixiu_status'][$val['weixiu_status']] : $val['weixiu_status'] ){
                                        $xls_content .= $weixiu_status. ",";
                                    }else{
                                        $xls_content .= '--'. ",";
                                    }
                                    $xls_content .= $val['weixiu_company_name']. ",";
                                    $xls_content .= $val['weixiu_warehouse_name']. ",";
                                    $xls_content .= $val['jinhao']. ",";
			                    }
                                $xls_content .= $val['account_time'] . ",";
                                $kuling = ceil((time() - strtotime($val['addtime'])) / (3600 * 24)).'天';
				if (empty($val['change_time']))
				{
                                    $thiskuling = '0';
				}else{
                                    $thiskuling = ceil((time() - strtotime($val['change_time'])) / (3600 * 24)) . '天';
				}
				$xls_content .=$thiskuling. ",";//本库库龄
				if ($show_private_data) {
				    $xls_content .= $kuling . ",";//库龄
				}
				$xls_content .= $val['guojibaojia'] . ",";
				$xls_content .= $val['zuanshizhekou'] . ",";
                $xls_content .= $val['pinpai'] . ",";
                $xls_content .= $val['luozuanzhengshu'] . ",";
                if ($show_private_data) {
                    $xls_content .= $val['supplier_code'] . ",";
                }
                $xls_content .= $val['xilie_name'] . ",";
                 if($val['xinyaozhanshi']==0){
                 	$xinyaozhanshi='未知';
                 }else if($val['xinyaozhanshi']==1){
                 	$xinyaozhanshi='是';
                 }else if($val['xinyaozhanshi']==2){
                 	$xinyaozhanshi='否';
                 }                             
				$xls_content .= $val['box_sn'] . ",";
				if ($show_private_data) {
				    $xls_content .= $xinyaozhanshi . ",";
				}
				$xls_content .= ($show_private_data?$val['mairugongfeidanjia']:"--"). ",";
				$xls_content .= $val['jingxiaoshangchengbenjia']. ",";
				$xls_content .= $val['management_fee'] . ",";				
				$xls_content .= ($show_private_data?$val['biaoqianjia']:"--"). ",";
				$xls_content .= $val['zhushibaohao'] . ",";
				$xls_content .= $val['fushibaohao'] . ",";
				$xls_content .= $val['shi2baohao'] . ",";
                $xls_content .= $val['shi3baohao']  . "\n";
				$xls_content = iconv('utf-8', 'GB18030', $xls_content);
                file_put_contents($filename,$xls_content,FILE_APPEND);  
			}
		} else {
			$xls_content = '没有数据！或者数据太多请联系技术导出';
			file_put_contents($filename,$xls_content,FILE_APPEND); 
		}
               
	}

    public function downloadfile($file){
                header("Content-type:text/csv;charset=gbk");
    	        //header("Content-type:text/html;charset=utf-8");
    	       //header('Content-type: text/csv; charset=UTF-16LE');
				$fp=fopen($file,"r"); 
				$file_size=filesize($file); 
				//下载文件需要用到的头 
				//Header("Content-type: application/octet-stream"); 
				Header("Accept-Ranges: bytes"); 
				Header("Accept-Length:".$file_size); 
				Header("Content-Disposition: attachment; filename=download".time().".csv"); 
				$buffer=1024; 
				$file_count=0; 
				//echo chr(239).chr(187).chr(191);
				//向浏览器返回数据 
				while(!feof($fp) && $file_count<$file_size){ 
				$file_con=fread($fp,$buffer); 
				$file_count+=$buffer; 			
				echo $file_con; 
				} 
				fclose($fp); 
                exit();
    }
	//导出
	public function groupdownload($data) {

		if ($data['data']) {

            $dir = KELA_ROOT."/apps/warehouse/tmp/";
            $dh=opendir($dir);
            while ($file=readdir($dh)) {
                if($file!="." && $file!="..") {
                    $fullpath=$dir."/".$file;
                    if(!is_dir($fullpath)) {
                        unlink($fullpath);
                    }
                }
            }
            
			$down = $data['data'];
            $path = '/frame/PHPExcel/PHPExcel.php';
            $pathIo = '/frame/PHPExcel/PHPExcel/IOFactory.php';
            include_once(KELA_ROOT.$path);
            include_once(KELA_ROOT.$pathIo);

            // 创建一个处理对象实例
            $objPHPExcel = new PHPExcel();
            // 创建文件格式写入对象实例, uncomment
            $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel); // 用于其他版本格式

            // 创建一个表
            $objPHPExcel->setActiveSheetIndex(0);

            $objPHPExcel->getActiveSheet()->setCellValue('A1', '款号');
            $objPHPExcel->getActiveSheet()->setCellValue('B1', '款式图片');
            $objPHPExcel->getActiveSheet()->setCellValue('C1', '模号');
            $objPHPExcel->getActiveSheet()->setCellValue('D1', '名称');
            $objPHPExcel->getActiveSheet()->setCellValue('E1', '主成色');
            $objPHPExcel->getActiveSheet()->setCellValue('F1', '数量');
            $objPHPExcel->getActiveSheet()->setCellValue('G1', '成本价');

            $i = 1;
            foreach ($down as $v) {

                $i=$i+1;

                $price = "";
                if (Auth::canRead("warehouse_goods.nominal_price",2,$v['warehouse_id'])){
                    $price .=" 名义价：". $v['mingyichengben'];
                }
                if (Auth::canRead("warehouse_goods.cost_price",2,$v['warehouse_id'])){
                    $price .="，" . " 原始采购价：". $v['yuanshichengbenjia'];
                }
                if (Auth::canRead("warehouse_goods.purchase_price",2,$v['warehouse_id'])){
                    $price .="，" . " 最新采购价：". $v['chengbenjia'];
                }

                // 设置高度
                $objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(100);
                $objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $v['goods_sn']);
                $pathfile = KELA_ROOT."/apps/warehouse/tmp/".substr($v['thumb_img'],-10);
                if(file_put_contents($pathfile,file_get_contents($v['thumb_img']))){
                    // 为excel加图片
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setName('Photo');
                    $objDrawing->setDescription('Photo');
                    $objDrawing->setPath($pathfile);
                    $objDrawing->setHeight(110);
                    $objDrawing->setWidth(110);
                    $objDrawing->setCoordinates('B'.$i);
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValue('B'.$i, '无图片');
                }
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $v['mo_sn']);
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $v['goods_name']);
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $v['caizhi']);
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $v['counts']);
                $objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $price);
            }

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(17);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(17);

            // 设置工作表的第一项表
            $objPHPExcel->setActiveSheetIndex(0);

            // 重命名表
            $objPHPExcel->getActiveSheet()->setTitle("分组导出数据");

            $ymd = date("Ymd_His", time()+8*60*60);
            include_once(KELA_ROOT.$pathIo);
            $outputFileName = $ymd.'.xls';
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header('Content-Disposition:inline;filename="'.$outputFileName.'"');
            header("Content-Transfer-Encoding: binary");
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: no-cache");
            $objWriter->save('php://output');
            exit;
			/*$xls_content = "款号,模号,名称,主成色,数量,成本价\r\n";

			foreach ($down as $key => $val) {
				$xls_content .= $val['goods_sn']. ",";
				$xls_content .= $val['mo_sn']. ",";
				$xls_content .= $val['goods_name']. ",";
				$xls_content .= $val['caizhi']. ",";
				$xls_content .= $val['counts']. ",";
				
				$price = "";
				if (Auth::canRead("warehouse_goods.nominal_price",2,$val['warehouse_id'])){
					$price .=" 名义价：". $val['mingyichengben'];
				}
				if (Auth::canRead("warehouse_goods.cost_price",2,$val['warehouse_id'])){
					$price .="，" . " 原始采购价：". $val['yuanshichengbenjia'];
				}
				if (Auth::canRead("warehouse_goods.purchase_price",2,$val['warehouse_id'])){
					$price .="，" . " 最新采购价：". $val['chengbenjia'];
				}
				$xls_content .= $price . "\n";
			}
		} else {
			$xls_content = '没有数据！';
		}
                header("Content-type:text/csv;charset=gbk");
                header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
                header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
                header('Expires:0');
                header('Pragma:public');
                echo iconv("utf-8", "gbk//IGNORE", $xls_content);

                exit;*/
        }
	}
	/**一键倒入柜位信息  2015-4-22**/
	public function DaoBox($params){
		$id = $params['id'];

		$model = new WarehouseGoodsModel($id , 21);
		$gmodel = new GoodsWarehouseModel(21);
		$boxModel = new WarehouseBoxModel(21);

		//判断货品是不是库存状态，不是就不对咯
		$is_on_sale = $model->getValue('is_on_sale');
		if($is_on_sale != 2){
			echo "选中的货品不是 <span style='color:red;'>库存</span> 状态，不能操作";die;
		}

		$goods_id = $model->getValue('goods_id');
		//检测是否有柜位系信息，有返回false
		$xx = $gmodel->select2($fields = " `id` ", $where = " `good_id` = '{$goods_id}' ", $is_all = 3 );		//是否存在
		if($xx === false){
			$warehouse_id = $model->getValue('warehouse_id');
			//获取仓库的默认柜位id
			$box_id = $boxModel->select2($fields = '`id`', $where=" `warehouse_id`={$warehouse_id} AND `box_sn`='0-00-0-0' " , $is_all = 3);

		//检测是否有默认柜位，如果没有那么创建一个
			if(!$box_id){	//没有默认柜位
				$box_id = $gmodel->addbox($warehouse_id);	//获取插入的boxID
			}

			$data = array(
				'good_id' => $goods_id ,
				'warehouse_id' =>  $warehouse_id,
				'box_id' => $box_id ,
				'add_time' => date('Y-m-d H:i:s'),
				'create_time' => date('Y-m-d H:i:s'),
				'create_user' => 'SYSTEM',
			);

		}else{
			echo '已经存在柜位信息';die;
		}
		$res = $gmodel->insertAdd($data);
		if($res['success'] == 1){
			echo '操作成功';die;
		}else{
			echo $res['error'];die;
		}
	}

	public function view_Image($params){
		$id=_Request::get('id');
		$style_sn=_Request::get('style_sn');
		if($id){
			$model = new WarehouseGoodsModel($id,21);
			$style_sn =$model->getValue('goods_sn');
		}
		$gallerymodel = new ApiStyleModel();
		//$gallery_data = $gallerymodel->getProductGallery($style_sn,1);
		$gallery_data=$gallerymodel->getStyleGalleryList($style_sn);
		
// 		echo "<pre>";
// 		print_r($gallery_data);exit;
		
		
		$this->render('show_image.html',array(
				'gallery_data'=>$gallery_data
		));
	}

	public function create_salepolicy_goods($params){
		//var_dump($_REQUEST);exit;
		$id=_Request::get('id');
        $ids = $params['_ids'];
        
        	$goodsAttrModel = new GoodsAttributeModel(17);
		$caizhi_arr = $goodsAttrModel->getCaizhiList();
		$jinse_arr  = $goodsAttrModel->getJinseList();
        $caizhi_keys_arr = array_flip($caizhi_arr);
        $jinse_keys_arr  = array_flip($jinse_arr);
        //只有库存状态未绑定订单才能生成可销售商品             
		$model = new WarehouseGoodsModel($id,21);
        $all_data = array();
        foreach ($ids as $k => $v) {
            $goods_data = $model->getGoodsByGoods_id($v);
            //生成可销售商品需要判断只有库存状态未绑定订单可以操作
            if($goods_data['is_on_sale']!=2){
                $result['error'] = "当前货品：".$v." 不是库存状态,不可以操作！";
                $result['success'] = 0;
                Util::jsonExit($result);
            }
            if (!empty($goods_data['order_goods_id'])){
                $result['error'] = "当前货品:".$v." 已绑定订单,不可以操作！";
                $result['success'] = 0;
                Util::jsonExit($result);
            }
            //把产品线和款式分类转换成ID传入（仓储存的是varchar的）
            if(!$goods_data['product_type']){
                $goods_data['product_type'] = '其他';
            }
            if(!$goods_data['cat_type']){
                $goods_data['cat_type'] = '其他';
            }
            $apimodelStyle = new ApiStyleModel();
            $cat_type = $apimodelStyle->getCatTypeInfo(array('cat_type_name'),array($goods_data['cat_type']));
            $product_type = $apimodelStyle->getProductTypeInfo(array('product_type_name'),array($goods_data['product_type']));
            $cat_type_id	 = count($cat_type)?$cat_type[0]['id']:0;
            $product_type_id = count($product_type)?$product_type[0]['id']:0;

            //准备销售政策传输数据
            $putdatasale['goods_name'] = $goods_data['goods_name'];
            $putdatasale['goods_id'] = $goods_data['goods_id'];
            $putdatasale['chengbenjia'] = $goods_data['chengbenjia'];
            $putdatasale['goods_sn'] = $goods_data['goods_sn'];
            $putdatasale['category']= $cat_type_id;
            $putdatasale['product_type']= $product_type_id;
            $putdatasale['is_sale']= 1;

            //$putdatasale['warehouse_id']= $bill_info['to_warehouse_id'];
            //$putdatasale['company_id']= $bill_info['to_company_id'];
            //$putdatasale['warehouse']= $bill_info['to_warehouse_name'];
            //$putdatasale['company']= $bill_info['to_company_name'];

            $putdatasale['stone'] = $goods_data['jietuoxiangkou'];
            $putdatasale['finger'] = $goods_data['shoucun'];
            $putdatasale['add_time'] = date("Y-m-d H:i:s");

            $caizhi_upper = strtoupper($goods_data['caizhi']);
            if(preg_match('/[0-9a-z]+/i',$caizhi_upper,$caizhi_jinse)){
                $caizhi = strtoupper($caizhi_jinse[0]);
                $jinse  = substr($caizhi_upper,strlen($caizhi_jinse[0]));
                if (isset($caizhi_keys_arr[$caizhi])) {
                    $putdatasale['caizhi'] = $caizhi_keys_arr[$caizhi];
                }
                if(isset($jinse_keys_arr[$jinse])){
                    $putdatasale['yanse'] = $jinse_keys_arr[$jinse];
                }
            }else{
    		    if (isset($caizhi_keys_arr[$caizhi_upper])) {
    		        $putdatasale['caizhi'] = $caizhi_keys_arr[$caizhi_upper];
    		    }
    		    $putdatasale['yanse'] = 0;
    		}

            $all_data[] = $putdatasale;
        }
        
        $baseSalepolicyGoodsModel = new BaseSalepolicyGoodsModel(17);
        $pdo = $baseSalepolicyGoodsModel->db()->db();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
        $pdo->beginTransaction();//开启事务
        $res = $baseSalepolicyGoodsModel->createSalepolicyGoods($all_data);
        if($res !== false){
            $pdo->commit();//事物提交
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            $result['success'] = 1;
        }else{
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            $result['error'] = "操作失败！";
        }       
        Util::jsonExit($result);
	}


	/**
	 * 返回老系统
	 * 改变商品状态为100
	 */
	public function pushOldsys($params)
	{
		$id = $params['id'];
		$model = new WarehouseGoodsModel($id,21);
		$goods_id = $model->getValue('goods_id');
		$is_on_sale = $model->getValue('is_on_sale');
		$company = $model->getValue('company_id');
		$warehouse = $model->getValue('warehouse_id');

		if($is_on_sale != '2'){
			$result['error'] = "非库存商品,不可返回旧系统";
			Util::jsonExit($result);
		}
		$old_conf = [
			'dsn'=>"mysql:host=192.168.1.61;dbname=jxc",
			'user'=>"kela_jxc",
			'password'=>"kela$%jxc",
			/*'dsn'=>"mysql:host=localhost;dbname=test",
			'user'=>"root",
			'password'=>"yangxt",*/
		];
		$db_pdo = new PDO($old_conf['dsn'], $old_conf['user'], $old_conf['password'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));

		$sql = "SELECT `goods_id`,`is_on_sale`,`order_goods_id` FROM `jxc_goods` WHERE `goods_id` = '".$goods_id."'";
		$obj = $db_pdo->query($sql);
		$row = $obj->fetch(PDO::FETCH_ASSOC);

		if(empty($row)){

			/*$result['error'] = "旧系统无此商品";
			Util::jsonExit($result);*/
			/*---------------*/
			//确定是旧系统订单 旧系统又无此货品 制造数据
			$newdo = $model->getDataObject();

			$newdo['storage_mode'] = ($newdo['put_in_type'] -1);unset($newdo['put_in_type']);
			$newdo['shipin_type'] = $newdo['product_type'];unset($newdo['product_type']);
			$newdo['kuanshi_type'] = $newdo['cat_type'];unset($newdo['cat_type']);
			$newdo['warehouse'] = $newdo['warehouse_id'];unset($newdo['warehouse_id']);
			$newdo['company'] = $newdo['company_id'];unset($newdo['company_id']);
			$newdo['tmp_sn'] = $newdo['box_sn'];unset($newdo['box_sn']);
			$newdo['zhuchengsezhong'] = $newdo['jinzhong'];unset($newdo['jinzhong']);
			$newdo['zhuchengse'] = $newdo['caizhi'];unset($newdo['caizhi']);
			$newdo['zhushizhong'] = $newdo['zuanshidaxiao'];unset($newdo['zuanshidaxiao']);
			$newdo['xianzaichengben'] = $newdo['mingyichengben'];unset($newdo['mingyichengben']);
			$newdo['pass_status']= '1';
			$newdo['is_on_sale'] = '1';
			$newdo['order_goods_id'] = null;
			$newdo['status_old'] =0;
			$newdo['order_id_old'] = '';
			$newdo['zuanshizhekou']=floatval($newdo['zuanshizhekou']);
			$newdo['account_time'] = date("Y-m-d H:i:s",$newdo['account_time']);
			$newdo['sale_time'] = date("Y-m-d H:i:s",$newdo['sale_time']);
			$newdo['company_time'] = date("Y-m-d H:i:s",$newdo['company_time']);
			$newdo['chuku_time'] = date("Y-m-d H:i:s",$newdo['chuku_time']);
			$newdo['gene_sn'] = '';
			$newdo['caigou_chengbenjia']= 0;
			$newdo['zhushiyanse'] = $newdo['yanse'];
			unset($newdo['id'],$newdo['oldsys_id'],$newdo['pass_sale'],$newdo['old_set_w'],$newdo['buchan_sn'],$newdo['jiejia'],$newdo['weixiu_status'],$newdo['weixiu_company_id'],$newdo['weixiu_company_name'],$newdo['weixiu_warehouse_id'],$newdo['weixiu_warehouse_name'],$newdo['zhushitiaoma']);

			foreach ($newdo as $k=>$v) {
				$newdo[$k] = "'".$v."'";
			}
			$sql = "INSERT INTO `jxc_goods` (".implode(',',array_keys($newdo)).") VALUES (".implode(',',$newdo).")";
			//exit($sql);
			$res = $db_pdo->exec($sql);
			if(!$res){
				$result['error'] = $sql."旧系统无此商品,创建商品失败";
				Util::jsonExit($result);
			}
			/*--------------*/

		}else{
			/*if(!$row['order_goods_id']){
				$result['error'] = "该商品不是旧系统订单货品,不允许返回旧系统";
				Util::jsonExit($result);
			}*/
			if( $row['is_on_sale'] != '100'){
				$result['error'] = "旧系统货品状态异常，请联系技术人员处理。(is_on_sale = ".$row['is_on_sale'].")";
				Util::jsonExit($result);
			}

		}
		
        $sql = "UPDATE `jxc_goods` SET `is_on_sale`  = '1',`warehouse` = '{$warehouse}',`company` = '{$company}' WHERE `goods_id` = '".$goods_id."'";
		$res2 = $db_pdo->exec($sql);
		if($res2){
			$model->setValue('is_on_sale',100);
			$res1 = $model->save(true);
		}
		if($res1 && $res2){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}

	public function batchPushOld(){
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_goods_batch_pushold.html',array(
		));
		$result['title'] = '批量返回老系统';
		Util::jsonExit($result);
	}

	public function batchPushOldsys(){
		$result = array('success' => 0,'error' => '');
		$goods_sns = _Post::getList('goods_arr');
		$model = new WarehouseGoodsModel(21);
		$res = $model->filterGids($goods_sns);
		//print_r($res);exit;
		$g_ids = $res['success'];//正确,是库存的货号ID
		$fail = $res['error'];
		if(!empty($g_ids)){
			$data = $model->goBackOldSYS($g_ids);
			$fail = array_merge($res['error'],$data['error']);
		}
		//$success = $data['success'];
		if(empty($fail)){
			$result['success'] = 1;
		}else{
			$result['error'] = "以下商品返回失败:<br/>";
			foreach ($fail as $gid) {
				$result['error'] .= $gid.'<br/>';
			}
		}
		Util::jsonExit($result);
	}
	public function warehouseidinfo(){
		$result = array('success' => 0,'error' => '');
		$arr= array('370','372','390','392','397','445','467','545','553','562','570','667');
		$warehouse_id = _Post::getList('warehouse_id');
		$flag = true;
		foreach($warehouse_id as $val){
			if(!in_array($val,$arr)){
				$flag = false;
				break;
			}
		}
		if($flag == true){
			$result['success'] = 1;
		}else{
			$result['error'] = 1;
		}
		//var_dump($result);die;
		Util::jsonExit($result);
	}


	///数据导出
	function daochu ($params)
	{
		$sql = "select
	case when wb.bill_type='S' then '销售单' when wb.bill_type='D' then '销售退货单' end as '单据类型',
	concat(wb.bill_no) as '单号',
	case when wb.bill_status = 1 then '已保存' when wb.bill_status=2 then '已审核' when wb.bill_status=3 then '已取消' end as '状态',
	wb.create_time as '制单时间',
	wb.check_time as '审核时间',
	wg.goods_id as '货号',
	wg.yuanshichengbenjia as '原始采购成本' ,
	wbg.xiaoshoujia as '实价',
		case when wg.is_on_sale=1 then '收货中'
		when wg.is_on_sale=2 then '库存'
		when wg.is_on_sale =3 then '已销售'
		when wg.is_on_sale=4 then '盘库中'
		when wg.is_on_sale=5 then '调拨中'
		 when wg.is_on_sale=6 then '损益中'
		 when wg.is_on_sale=7 then '已报损'
		 when wg.is_on_sale=8 then '返厂中'
		 when wg.is_on_sale=9 then '已返厂'
		 when wg.is_on_sale=10 then '销售中'
		 when wg.is_on_sale=11 then '退货中'
		 when wg.is_on_sale=12 then '作废'
		 when wg.is_on_sale=100 then '在老系统'
		END AS  '货品状态',
	wg.addtime as '入库时间',
	wg.warehouse_id
	from warehouse_bill as wb,warehouse_bill_goods as wbg,warehouse_goods as wg
where
wg.goods_id=wbg.goods_id and wbg.bill_id=wb.id and wb.bill_type in ('S','D') and  wb.create_time <= '2015-05-8 23:59:59' ";
		$model = new WarehouseGoodsModel(22);
		$res = $model->db()->getAll($sql);
		foreach ($res as $key=>$val)
		{
			$goods_id = $val['货号'];
			$sql = "select l.send_goods_sn
		from warehouse_bill as wb,warehouse_bill_goods as wbg,warehouse_goods as wg ,warehouse_bill_info_l as l
where
wg.goods_id=wbg.goods_id and wbg.bill_id=wb.id and wb.bill_type='L' and l.bill_id= wb.id and wb.bill_status=2 and wg.goods_id ='{$goods_id}'";
			$send_goods_sn = $model->db()->getOne($sql);
			if($send_goods_sn)
			{
				$res[$key]["收货单号"] = $send_goods_sn;
			}
			else
			{
				$res[$key]["收货单号"] = '新系统查不到收货单号';
			}
		}
		//var_dump($res);exit;
		header("Content-Disposition: attachment;filename=lyh.csv");
		echo iconv("utf-8", "gbk" , "单据类型,单号,状态,制单时间,审核时间,货号,原始采购成本,实价,状态,入库时间,收货单号") . "\n";
		foreach($res as $k=>$v)
		{
			$v['原始采购成本']=Auth::canRead("warehouse_goods.nominal_price",2,$v['warehouse_id'])?$v['原始采购成本']:'';
			$str=$v['单据类型'].",".$v['单号'].",".$v['状态'].",".$v['制单时间'].",".$v['审核时间'].",".$v['货号'].",".$v['原始采购成本'].",".$v['实价'].",".$v['货品状态'].",".$v['入库时间'].",".$v['收货单号'];
			echo iconv("utf-8","gbk",$str)."\n";
		}
		exit;

	}


	//寻找天生一对
	private function search_tsyd($data){
		/**
		 * 判断是否是货品镶嵌了天生一对的钻石，货号后面显示天生一对钻石的款号。
		 * 1、检测 pinpai 字段不为空
		 * 2、根据pinpai = '证书号', 找到钻，检测钻 zhengshuleibie 是否为 “HRD-D”
		 * 3、是的话，再根据这个钻去找另外一个货
		*/
	    $model = new WarehouseGoodsModel(21);
	    $all_xilie = $model->db()->getAll('select id,name from front.app_style_xilie where `status` = 1');
	    $all_xilie = array_column($all_xilie, 'name', 'id');
	    
        foreach($data['data'] AS $k => $val){
			$sql="select g.*,d.kuan_sn from warehouse_goods g inner join rel_hrd d on g.goods_id=d.tuo_b where d.tuo_a='{$val['goods_id']}' limit 0,1";
			$res = $model->db()->getAll($sql);
			foreach($res as $key=>$val2){
				$data['data'][$k]['tsyd']=$val2;
				$data['data'][$k]['tsyd_goods_sn']= $val2['kuan_sn'];
			}
			
			$name = '';
			if (!empty($val['xilie'])) {
			     $list = Util::eexplode(',', $val['xilie']);
			     foreach ($list as $kk => $v){
			         if (isset($all_xilie[$v])){
			             $name .= $all_xilie[$v].' ';
			         }
			     }
			}
			
			$data['data'][$k]['xilie_name'] = $name;
	    }
	    /*
		foreach($data['data'] as $k => $val){
			$sql="select xilie from front.base_style_info where style_sn='{$val['goods_sn']}' limit 0,1";
			$xilie = $model->db()->getOne($sql);

			$sql = "select name from front.app_style_xilie where  id in(0{$xilie}0)";
			$xilie_name = $model->db()->getAll($sql);

			$name = '';
			if(!empty($xilie_name)) {
				foreach ($xilie_name as $kk => $v){
					$name .= $v['name'].' ';
				}
			}
			$data['data'][$k]['xilie_name'] = $name;
		}*/
	   	return $data;
	}

	// 调拨单、S销售单：加价成本价
	private function addJiajiaChengben(&$data){
	    //TODO: 如果是展厅的经销商用户，则直接返回，不要计算
	    if (SYS_SCOPE =='zhanting' && !Auth::user_is_from_base_company()) return;

	    $model = new WarehouseGoodsModel(21);
		$company_ids = $model->getTydCompanyIds(); // 直营分公司
		foreach($data['data'] AS &$item){
			// 默认等于 原始采购价
			$item['jiajiachengben'] = $item['yuanshichengbenjia'];
			if (isset($company_ids[$item['company_id']])) {
				$jiajialv = $model->getMbillJiajialv($item['goods_id'], $item['company_id']);
				if ($jiajialv) {
					$item['jiajiachengben'] = number_format($item['yuanshichengbenjia'] * (1 + $jiajialv/100),2,null,'');
				}
			}
		}
	}
	
	//婚博会数据导出专用
	public function hbdown()
	{
		$args = $_REQUEST;
		if ($args['company_id'] == '' )
		{
			 echo "未选择公司，不能导出！"; 
			 exit;
		}
		if($args['company_id'] =='58')
		{
			 echo "请导出总公司以外的数据！"; 
			 exit;
		}		
		$warehouseGoodsModelR = new WarehouseGoodsModel(55);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
        if ($args['xilie_name'] !=''){  
            $sql = "SELECT  id from front.app_style_xilie  WHERE `name` = '".$args['xilie_name']."' ";
            $id = $warehouseGoodsModelR->db()->getOne($sql);
            $sql = "SELECT style_sn from front.base_style_info WHERE   INSTR (xilie, ',".$id.",') > 0";
            $style_sn = $warehouseGoodsModelR->db()->getAll($sql);
            $xilie_val = array();
            foreach ($style_sn as $k => $v){
                $xilie_val[] ="'". $v['style_sn']."'";
            }
            $xilie_val = join(',', $xilie_val);
            $args['xilie_val'] = $xilie_val;
        }else{
            $args['xilie_val'] = '';
        }
		$args['zhengshuleibie'] = $args['zhengshu_type'];
		$args['weixiu_warehouse_id'] = isset($args['weixiu_warehouse_id'])?$args['weixiu_warehouse_id']:'';
		$args['kucunstart'] = isset($args['kucunstart']) ? $args['kucunstart'] : '';
		$args['kucunend'] = isset($args['kucunend']) ? $args['kucunend'] : '';
		$data = $warehouseGoodsModelR->hbhexport($args);
		//$title = array("货号","款号","款式类型","新款式类型","产品线","新产品线","名称","主成色","主成色重","指圈号",
		//"主石形状","主石重","主石净度","主石颜色","主石切工","主石对称","主石抛光","主石规格",
		//"荧光","证书号","标签价","金托类型(1:成品2:空托女戒3:空托)","柜位号","调拨单号","单据备注");
		$data =  $data['data'];
		if(!empty($data))
		{
			foreach($data as $k=>$obj)
			{
				$data[$k]['cat_type1'] = empty($obj['cat_type1'])?$obj['cat_type']:$obj['cat_type1'];
				$data[$k]['product_type1'] = empty($obj['product_type1'])?$obj['product_type']:$obj['product_type1'];
				$data[$k]['mingyichengben'] ='';
				$data[$k]['jijiachengben'] ='';
				unset($data[$k]['cat_type']);
				unset($data[$k]['product_type']);
				unset($data[$k]['box_sn']);
			}
			$title = array(
			"货号","款号","款式类型","产品线","名称","主成色","主成色重","指圈号",
			"主石形状","主石重","主石净度","主石颜色","主石切工","主石对称","主石抛光","主石规格",
			"荧光","证书号","成本价","标签价","金托类型(1:成品,2:空托女戒,3:空托)","调拨单号","柜台号"
			);
			Util::downloadCsv("婚博会专用导出",$title,$data);
			exit;
		}else{
			echo "没有满足条件的数据";
			exit;				
		}

	}

    /**
    *批量修改展厅标签价格 boss_1465
    */
    public function batchUpdateBqj($value='')
    {
        $result = array('success' => 0,'error' => '');
        $result['content'] = $this->fetch('batch_update_bqj.html');
        $result['title'] = '批量修改展厅标签价';
        Util::jsonExit($result);
    }

    /**
    *修改展厅标签价
    */
    public function batchUpdateBqjInsert($params)
    {
        $result = array('success' => 0,'error' => '');
        if($_FILES['file_bqj']['name'] == '')
        {
            $result['error'] = "请上传要更新的数据";
            Util::jsonExit($result);
        }
        if(empty($_FILES['file_bqj']['tmp_name']))
        {
            $result['error'] = "上传文件不能为空";
            Util::jsonExit($result);
        }
        $file_array = explode(".",$_FILES['file_bqj']['name']);
        $file_extension = strtolower(array_pop($file_array));
        if($file_extension != 'csv'){
            $result['error'] = "请上传CSV格式的文件";
            Util::jsonExit($result);
        }
        $model = new WarehouseGoodsModel(55);
        $f = fopen($_FILES['file_bqj']['tmp_name'],"r");
        $i = 0;
        while(! feof($f)){
            $con = fgetcsv($f);
            if ($i > 0){
                if (trim($con[0]) == '' && trim($con[1]) == '' ){
                    if($i == 1){
                        $result['error'] = "上传文件数据不能为空";
                        Util::jsonExit($result);
                    }
                }else{
                    $goods_id = strtoupper(trim($con[0])); 
                    $price = strtoupper(trim($con[1])); 
                    if(empty($goods_id) || empty($price)){
                        $result['error'] = "货号和展厅标签价为必填项";
                        Util::jsonExit($result);
                    }

                    $z = "/^(\d+)(\.\d+)?$/";
                    if(!preg_match("/^\d*$/",$goods_id))
                    {
                        $result['error'] = '第'.($i+1).'行货号格式不对，货号只能为数字。';
                        Util::jsonExit($result);
                    }
                    if(!preg_match($z,$price)){
                        $result['error'] = '第'.($i+1).'行展厅标签价只能为数字并且是正数。';
                        Util::jsonExit($result);
                    }
                    $val['goods_id'] = $goods_id;
                    $val['biaoqianjia'] = $price;
                    $data[] = $val;
                }
            }
            $i++;
        }
        $res = $model->upBqjData($data);
        if($res['success'] == 1){
            $result['error'] = "提交成功";
            Util::jsonExit($result);
        }
        $result['error'] = $res['error'];
        Util::jsonExit($result);
    }

    /**
    *下载标签价模板
    */
    public function bqj_dow($value='')
    {
        $title = array('货号','展厅标签价');
        $this->detail_csv('展厅修改标签价',$title,array());
    }

    /**
    *转换编码格式，导出csv数据
    */
    public function detail_csv($name,$title,$content)
    {
        $ymd = date("Ymd_His", time()+8*60*60);
        header("Content-Disposition: attachment;filename=".iconv('utf-8','gbk',$name).".csv");
        $fp = fopen('php://output', 'w');
        $title = eval('return '.iconv('utf-8','gbk',var_export($title,true).';')) ;
        fputcsv($fp, $title);
       foreach($content as $k=>$v)
       {
            fputcsv($fp, $v);
       }
        fclose($fp);exit;
    }
     function downloadStorageExcel($data){
     	$dic=new DictView(new DictModel(1));
        $path = '/frame/PHPExcel/PHPExcel.php';
        $pathIo = '/frame/PHPExcel/PHPExcel/IOFactory.php';
        include_once(KELA_ROOT.$path);
        include_once(KELA_ROOT.$pathIo);
        // 创建一个处理对象实例
        $objPhpExcel = new PHPExcel();
        // 创建文件格式写入对象实例, uncomment
        $objWriter = new PHPExcel_Writer_Excel5($objPhpExcel); // 用于其他版本格式
        // 设置一个当前活动页
        $objPhpExcel->setActiveSheetIndex(0);
        //获取活动页
        $objSheet=$objPhpExcel->getActiveSheet();
        $title=array(
                array('货号','款号','模号','饰品分类','款式分类','主成色','主成色重','金耗',
					'主成重计价','主成色买入单价','主成色买入成本','主成色计价单价','主石','主石颗粒', '主石重','主石重计价',
					'主石颜色','主石净度','主石买入单价','主石买入成本','主石计价单价','主石切工','主石形状','主石包号',
					'主石规格','副石','副石颗粒','副石重','副石重计价单价','副石颜色','副石净度','副石买入单价',
					'副石买入成本','副石计价单价','副石形状','副石包号','副石规格','总重','买入工费单据','买入工费',
					'计价工费','手寸','黄金分类','单件成本','配件成本','其他成本','计价成本','成本价',
					'加价率','最新零售价','品牌','长度','证书号','颜色','净度','配件数量',
					'国际证书','证书类别','名称','订单号','石2','石2粒数','石2重','石2重计价',
					'石2买入单价','石2买入成本','石2计价单价','切工','抛光','对称','荧光','布产号',
					'销售成本','钻石折扣','证书2','裸石国际报价','工厂成本','成品/空托','GEMC证书号','戒指实际镶口',
					'主石条码','（主石）彩钻颜色分级','供应商货品条码','裸钻钻石类别','生产跟单费','证书费','营运费用','配件金重',
					'名义成本价','石2包号','石3','石3粒数','石3重','石3重计价','石3买入单价','石3买入成本','石3计价单价','石3包号'),
            );
      
        $key=1;
        foreach ($data as $k => $value) {
        	$key=$key+1;
        	$title[]=array(
        		    $value['goods_id'],
        			$value['goods_sn'],
        			$value['mo_sn'],
        			$value['product_type'],//饰品分类
        			$value['cat_type'],//款式分类
        			$value['caizhi'],
        			$value['jinzhong'],
        			$value['jinhao'],
        			'1',
        			' ',
        			' ',
        			' ',
        			$value['zhushi'],
        			$value['zhushilishu'],
        			$value['zuanshidaxiao'],
        			'1',//主石计价  固定为1
        			$value['zhushiyanse'],
        			$value['zhushijingdu'],
        			' ',//主石买入单价
        			' ',//主石买入成本
        			' ',////主石计价单价
        			$value['zhushiqiegong'],
        			$value['zhushixingzhuang'],
        			' ',//$value['zhushibaohao'],
        			' ',//$value['zhushiguige'],
        			$value['fushi'],
        			$value['fushilishu'],

        			$value['fushizhong'],
        			'1',
        			$value['fushiyanse'],//副石颜色
        			$value['fushijingdu'],
        			' ',//副石买入单价
        			' ',//副石买入成本
        			' ',//副石买入成本
        			$value['fushixingzhuang'],
        			' ',//$value['fushibaohao'],
        			' ',//$value['fushiguige'],
        			$value['zongzhong'],
        			' ',//$value['mairugongfeidanjia'],
        			' ',//$value['mairugongfei'],
        			' ',//$value['jijiagongfei'],
        			$value['shoucun'],
        			' ',//黄金分类
        			$value['danjianchengben'],//单件成本
        			$value['peijianchengben'],
        			$value['qitachengben'],
        			$value['chengbenjia'],
        			$value['jijiachengben'],//计价成本
        			$value['jiajialv'],//加价率
        			' ',//最新零售价
        			$value['pinpai'],
        			' ',//长度
        			$value['zhengshuhao'],

        			$value['yanse'],
        			$value['jingdu'],
        			$value['peijianshuliang'],
        			$value['guojizhengshu'],
        			$value['zhengshuleibie'],
        			$value['goods_name'],//名称
        			'订单号',
        			$value['shi2'],
        			$value['shi2lishu'],
        			$value['shi2zhong'],
        			' ',//石2重计价
        			' ',//石2买入单价
        			' ',//石2买入成本
        			' ',//石2计价单价
        			$value['qiegong'],
        			$value['paoguang'],
        			' ',//对称
        			$value['yingguang'],//荧光
        			$value['buchan_sn'],
        			' ',//销售成本
        			' ',//钻石成本
        			$value['zhengshuhao2'],
        			$value['guojibaojia'],
        			' ',//工厂成本
        			$dic->getEnum('warehouse_goods.tuo_type',$value['tuo_type']),
        			$value['gemx_zhengshu'],//exmc证书号
        			$value['jietuoxiangkou'],

        			$value['zhushitiaoma'],
        			$value['color_grade'],
        			$value['supplier_code'],
        			$value['luozuanzhengshu'],
        			' ',//生产跟单费
        			' ',//证书费
        			' ',//营运费用
        			$value['peijianjinchong'],
        			$value['mingyichengben'],
					$value['shi2baohao'],
					$value['shi3'],
					$value['shi3lishu'],
					$value['shi3zhong'],
					$value['shi3zhongjijia'],
					$value['shi3mairudanjia'],
					$value['shi3mairuchengben'],
					$value['shi3jijiadanjia'],
					$value['shi3baohao'],
        		);

        }
        $objSheet->fromArray($title);
        $ymd = date("Ymd_His", time()+8*60*60);
        include_once(KELA_ROOT.$pathIo);
        $outputFileName = $ymd.'.xls';
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:inline;filename="'.$outputFileName.'"');
        header("Content-Transfer-Encoding: binary");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: no-cache");
        $objWriter->save('php://output');
        exit;
    }

    //批量选取打印标签
    public function printCode($value='')
    {
        $ids = _Request::getString("_ids");
        if(empty($ids)){
            exit("ID is empty.");
        }
        $where = array();
        $where['goods_id'] = explode(",", $ids);
        $model = new WarehouseGoodsModel(21);
        $res = $model->getprintgoodsinfo($where," `goods_name`,`goods_sn`,`zhengshuhao`,`caizhi`,`jinzhong`,`zhushilishu`,`zuanshidaxiao`,(`fushilishu`+`shi2lishu`+`shi3lishu`) as fushilishu,(`fushizhong`+`shi2zhong`+`shi3zhong`) as fushizhong,`goods_id`,`shoucun` ");
        if(empty($res)){
            exit("NO data.");
        }
        $selfTihCz = $this->tihCzInfo;
        foreach ($res as $key => &$info) {
            if($info['caizhi'] != ''){
                $info['caizhi'] = str_replace(array_keys($selfTihCz), array_values($selfTihCz), $info['caizhi']);
            }
            if($info['goods_name'] != ''){
                $info['goods_name'] = str_replace('锆石','合成立方氧化锆',$info['goods_name']);
                $info['goods_name'] = str_replace(array_keys($selfTihCz), array_values($selfTihCz), $info['goods_name']);
                $info['goods_name'] = str_replace(array_keys($this->tihCtInfo), array_values($this->tihCtInfo), $info['goods_name']);
                $info['goods_name'] = str_replace(array_keys($this->stoneInfo), array_values($this->stoneInfo), $info['goods_name']);
            }
        }
        $this->render('print_goods_info.html',array(
            'datalist'=>$res
          )
        );
    }
}

?>
