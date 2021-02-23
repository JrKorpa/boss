<?php
/**
 *  -------------------------------------------------
 *   @file		: BasesalepolicyinfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 10:48:52
 *   @update	:
 *  -------------------------------------------------
 */
class BaseSalepolicyInfoController extends CommonController
{
    protected $smartyDebugEnabled = true;
    protected $whitelist = array('print_q');
    public $huopintypes = array(0=>'期货',1=>'现货',2=>'全部类型');
	//public $chanpinxian = array('其他饰品','黄金等投资产品','素金饰品','黄金饰品及工艺品','钻石饰品','珍珠饰品','彩宝饰品','成品钻','翡翠饰品','配件及特殊包装','非珠宝','钻石','珍珠','翡翠','宝石');
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('base_salepolicy_info','front',17);	//生成模型后请注释该行
		//Util::V('base_salepolicy_info',17);	//生成视图后请注释该行
		
		//款式分类
		$apiStyleModel = new ApiStyleModel();
		$catList = $apiStyleModel->getCatTypeInfo();
		
		
		//产品线
		$model = new BaseSalepolicyInfoModel(17);
		$allproducttype = $model->getallproductype();
		$chanpinxian = array();
		foreach($allproducttype as $typeinfo)
		{
			$typeid = $typeinfo['product_type_id'];
			$name = $typeinfo['product_type_name'];
			$chanpinxian[$typeid]= $name;
		}
		
		$this->render('base_salepolicy_info_search_form.html',
						array('bar'=>Auth::getBar(),
						'catList' => $catList,
						'chanpinxian' =>$chanpinxian));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		//货品类型
		//$huopintypes = array(0=>'A类',1=>'B类',2=>'C类');
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'policy_name'	=> _Request::getString("policy_name"),
			'policy_status'	=> _Request::getInt("policy_status"),
			'is_delete'	=> _Request::getString("is_delete"),
			'chanpinxian'	=> _Request::getString("chanpinxian"),//产品线
			'jintuo_type'	=> _Request::getString("jintuo_type"),//金托类型
			'huopin_type'	=> _Request::getString("huopin_type"),//货品类型
			'cat_type'	=> _Request::getString("cat_type"),      //款式分类
			'range_begin'	=> _Request::getString("range_begin"), //范围
			'range_end'	=> _Request::getString("range_end"), //范围
  	         'zhushi_begin'	=> _Request::getString("zhushi_begin"), //范围
			'zhushi_end'	=> _Request::getString("zhushi_end"), //范围
			'time_start'	=> _Request::getString("time_start"),
			'time_end'	=> _Request::getString("time_end"),
			'is_kuanprice'	=> _Request::getString("is_kuanprice"),
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['policy_name'] = $args['policy_name'];
		$where['policy_status'] = $args['policy_status'];
		$where['is_delete'] = $args['is_delete'];
		$where['product_type'] = $args['chanpinxian'];
		$where['tuo_type'] = $args['jintuo_type'];
		$where['huopin_type'] = $args['huopin_type'];
		$where['cat_type'] = $args['cat_type'];
		$where['range_begin'] = $args['range_begin'];
		$where['range_end'] = $args['range_end'];
        $where['zhushi_begin'] = $args['zhushi_begin'];
		$where['zhushi_end'] = $args['zhushi_end'];
		$where['time_start'] = $args['time_start'];
		$where['time_end'] = $args['time_end'];
		$where['is_kuanprice'] = $args['is_kuanprice'];
        
        $model = new BaseSalepolicyInfoModel(17);
        $data = $model->pageList($where, $page, 25, false);

        foreach ($data['data'] as $key => $val) {
            $data['data'][$key]['policy_start_time'] = $val['policy_start_time'] .
                ' 00:00:00';
            $data['data'][$key]['policy_end_time'] = $val['policy_end_time'] . ' 23:59:59';
            $data['data'][$key]['now_time'] = date("Y-m-d H:i:s");
        }
        //var_dump($data);
        $pageData = $data;

		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'base_salepolicy_info_search_page';
		$this->render('base_salepolicy_info_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'huopintypes'=>$this->huopintypes
		));
	}

    /**
     *	add，渲染添加页面
     */
    public function add()
    {

         $model1 = new BaseSalepolicyInfoModel(17);
        $bill_type = $model1->getBillType();
        $act=__function__;
		$result = array('success' => 0,'error' => '');
		//款式分类
		$apiStyleModel = new ApiStyleModel();
		$catArr = $apiStyleModel->getCatTypeInfo();
		
		//产品线
		$allproducttype = $model1->getallproductype();
		$chanpinxian = array();
		foreach($allproducttype as $typeinfo)
		{
			$typeid = $typeinfo['product_type_id'];
			$name = $typeinfo['product_type_name'];
			$chanpinxian[$typeid]= $name;
		}
		//获取有效的系列
		$dd = new DictView(new DictModel(1));
		$xiliearr = $dd->getEnumArray("style.xilie");
		
		$kongbai = array('name'=>0,'label'=>'空白','note'=>'空白','dict_name'=>'style.xilie');
		$allxilie = array('name'=>0,'label'=>'全部系列','note'=>'全部系列','dict_name'=>'style.xilie');
		array_unshift($xiliearr,$kongbai);
		array_unshift($xiliearr,$allxilie);
		
		//获取证书类型
		$goodsAttrModel = new GoodsAttributeModel(17);
 		$certArr = $goodsAttrModel->getCertList();
 		$colorArr = $goodsAttrModel->getColorList();
 		$clarityArr = $goodsAttrModel->getClarityList();
 		array_unshift($certArr,'全部类型');
 		array_unshift($colorArr,'全部');
 		array_unshift($clarityArr,'全部');
 		
		$result['content'] = $this->fetch('base_salepolicy_info_info.html',
		array(
			'view'=>new BaseSalepolicyInfoView(new BaseSalepolicyInfoModel(17)),
			'edit'=>false,'bill_type' => $bill_type,'act'=>$act,
			'catArr' => $catArr,
			'xilieArr'=>$xiliearr,
			'xilie'=>array(),
			'chanpinxian' => $chanpinxian,
			'certArr'=>$certArr,
			'cert'=>array(),
		    'colorArr'=>$colorArr,
		    'clarityArr'=>$clarityArr,
			'huopintypes' => $this->huopintypes
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

    /**
     *	edit，渲染修改页面
     */
    public function edit($params)
    {
        
        $id = intval($params["id"]);
        $result = array('success' => 0, 'error' => '');
        $model = new BaseSalepolicyInfoModel($id, 17);
		//注释掉已经审核的销售政策也是可以编辑的
		/*
        if ($model->getValue('bsi_status') == 3) {
            $result['content'] = '已审核的政策不能编辑';
            Util::jsonExit($result);
        }*/
			
		//款式分类
		$apiStyleModel = new ApiStyleModel();
        //产品线
		$allproducttype = $model->getallproductype();
        $chanpinxian = array();
        foreach($allproducttype as $typeinfo)
		{
			$typeid = $typeinfo['product_type_id'];
			$name = $typeinfo['product_type_name'];
			$chanpinxian[$typeid]= $name;
		}
		//获取有效的系列
		$dd = new DictView(new DictModel(1));
		$xiliearr = $dd->getEnumArray("style.xilie");
		$kongbai = array('name'=>0,'label'=>'空白','note'=>'空白','dict_name'=>'style.xilie');
		$allxilie = array('name'=>0,'label'=>'全部系列','note'=>'全部系列','dict_name'=>'style.xilie');
		array_unshift($xiliearr,$kongbai);
		array_unshift($xiliearr,$allxilie);
		//获取证书类型
		$goodsAttrModel = new GoodsAttributeModel(17);
 		$certArr = $goodsAttrModel->getCertList();
 		$colorArr = $goodsAttrModel->getColorList();
 		$clarityArr = $goodsAttrModel->getClarityList();
		array_unshift($certArr,'全部类型');
		array_unshift($colorArr,'全部');
		array_unshift($clarityArr,'全部');
		
		$baseSalepolicyview = new BaseSalepolicyInfoView($model);
		$catArr = $apiStyleModel->getCatTypeInfo();
		$result['content'] = $this->fetch('base_salepolicy_info_info.html',array(
			'view'=>$baseSalepolicyview,
			'edit'=>true,
		    'catArr'=>$catArr,
			'xilieArr'=>$xiliearr,
			'certArr'=>$certArr,
		    'certArr'=>$certArr,
		    'colorArr'=>$colorArr,
			'clarityArr' => $clarityArr,
			'chanpinxian' => $chanpinxian,
			'huopintypes' => $this->huopintypes
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}


    /**
     *	show，渲染查看页面
     */
    public function show($params)
    {
        $id = intval($params["id"]);
        $policyChannelStatus = array(
            '1' => '保存',
            '2' => '审核',
            '3' => '已经审核',
            '4' => '取消');
        $appsalepolicychannelmodel = new AppSalepolicyChannelModel(17);
        $channelList = $appsalepolicychannelmodel->getSalepolicyChannelByPolicyId($id);

        $model = new BaseSalepolicyInfoModel($id, 17);
        $do = $model->getDataObject();

		//产品线
		$allproducttype = $model->getallproductype();
		$chanpinxian = array();
		foreach($allproducttype as $typeinfo)
		{
			$typeid = $typeinfo['product_type_id'];
			$name = $typeinfo['product_type_name'];
			$chanpinxian[$typeid]= $name;
		}
		
        $html = "base_salepolicy_info_show.html";
        if ($do['is_together'] == 2) {
            $html = "base_salepolicy_info_showother.html";
        }
        $time = date("Y-m-d");
	
// 		echo "<pre>";
// 		print_r(new BaseSalepolicyInfoView($model));
// 		print_r($channelList);
// 		print_r($policyChannelStatus);
// 		exit;
		
		$this->render($html,array(
			'view'=>new BaseSalepolicyInfoView($model),
			'channelList'=>$channelList,
			'policyChannelStatus'=>$policyChannelStatus,
			//'bar_v'=>Auth::getViewBar(),
			'now_time' => $time,
			'chanpinxian' => $chanpinxian,
		));
	}



    /**
     *	shows，销售政策详情
     */
    public function shows($params)
    {
        $id = intval($params["id"]);
        $result['content'] = $this->fetch('base_salepolicy_info_show_list_show.html',
            array('view' => new BaseSalepolicyInfoView(new BaseSalepolicyInfoModel($id, 17))));
        $result['title'] = '销售政策详情';
        Util::jsonExit($result);

    }

    /**
     *	insert，信息入库
     */
    public function insert($params)
    {
        $result = array('success' => 0, 'error' => '');
        $msg = '';
        $bill_no = _Request::getString("bill_no");
        $bill_type = _Request::getString("bill_type");
        
        $cat_type_str = _Request::getString('cat_type');
        $cat_type_arr = explode('|',$cat_type_str);
        $cat_type_id = !empty($cat_type_arr[0])?$cat_type_arr[0]:"0";
        $cat_type_name = isset($cat_type_arr[1])?$cat_type_arr[1]:"";
        
        $product_type_str = _Request::getString('chanpinxian');
        $product_type_arr = explode('|',$product_type_str);
        $product_type_id = !empty($product_type_arr[0])?$product_type_arr[0]:"0";
        $product_type_name = isset($product_type_arr[1])?$product_type_arr[1]:"";
        
        $olddo = array();
		$newdo=array(
			'policy_name'	=> _Request::getString("policy_name"),
			'policy_start_time'	=> _Request::getString("policy_start_time"),
			'policy_end_time'	=> _Request::getString("policy_end_time"),
			'create_time'	=> date("Y-m-d H:i:s"),
			'is_together'	=> _Request::getInt("is_together"),
			'bsi_status'	=> 1,
			'jiajia'	=> _Request::getFloat('jiajialv'),
			
			//update by liulinyan 2015-08-11 不对是否存在变量做改动 依照以前的写法
			'range_begin'	=> _Request::getString('range_begin'),
			'range_end'	=> _Request::getString('range_end'),
            'zhushi_begin'	=> _Request::getString("zhushi_begin"), //范围
			'zhushi_end'	=> _Request::getString("zhushi_end"), //范围
			'product_type_id'	=> $product_type_id,
		    'product_type'	=> $product_type_name,
			'tuo_type'	=> _Request::getInt('jintuo_type'),
			'huopin_type'	=> _Request::getInt('huopin_type'),
			'cat_type_id'	=> $cat_type_id,
		    'cat_type'	=> $cat_type_name,
			//end
			
			'sta_value'	=> _Request::getFloat('sta_value'),
			'is_delete'	=> 0,
			'check_remark'	=> _Request::getString("check_remark"),
			'create_user'	=> $_SESSION['userName'],
			'check_user'	=> $_SESSION['userName'],
			'is_favourable'	=> _Request::get('is_favourable')?1:2,
			'is_default'	=> _Request::get('is_default')?1:2,
            'is_kuanprice'	=> _Request::get('is_kuanprice')?1:0,
			'xilie'	=> _Request::getList('xilie'),
			'cert'	=> _Request::getList('cert'),
		    'clarity'=>_Request::getList('clarity'),
		    'color'=>_Request::getList('color'),
		);

		
		//如果系列选择了全部,那么就用all代替,并且去掉其他的
		if(!empty($newdo['xilie']))
		{
			if(in_array('全部系列',$newdo['xilie'])){
				$newdo['xilie']='全部系列';
			}else{
				$newdo['xilie'] = implode(',',$newdo['xilie']);	
			}
		}else{
			$newdo['xilie']='全部系列';	
		}
		//如果证书类型选择了全部,那么就用all代替,并且去掉其他的
		if(!empty($newdo['cert']))
		{
			if(in_array('全部类型',$newdo['cert'])){
				$newdo['cert']='全部类型';	
			}else{
				$newdo['cert'] = implode(',',$newdo['cert']);	
			}
		}else{
			$newdo['cert']='全部类型';	
		}
		//主石颜色
		if(!empty($newdo['color']))
		{
		    if(in_array('全部',$newdo['color'])){
		        $newdo['color']='全部';
		    }else{
		        $newdo['color'] = implode(',',$newdo['color']);
		    }
		}else{
		    $newdo['color']='全部';
		}
		//主石净度
		if(!empty($newdo['clarity']))
		{
		    if(in_array('全部',$newdo['clarity'])){
		        $newdo['clarity']='全部';
		    }else{
		        $newdo['clarity'] = implode(',',$newdo['clarity']);
		    }
		}else{
		    $newdo['clarity']='全部类型';
		}
        if (empty($newdo['policy_name'])) {
            $result['error'] = '销售策略名称不能为空';
            Util::jsonExit($result);
        }
        if (empty($newdo['policy_start_time'])) {
            $result['error'] = '销售策略开始时间不能为空';
            Util::jsonExit($result);
        }
        if (empty($newdo['policy_end_time'])) {
            $result['error'] = '销售策略结束时间不能为空';
            Util::jsonExit($result);
        }
        
        $newmodel = new BaseSalepolicyInfoModel(17);
        $goodsmodel=new BaseSalepolicyGoodsModel(17);
        //去掉按款定价(也就是现在用作一口价标识的过滤);update by lly 2017-03-07 
		/*
        if($newdo['is_kuanprice'] == 1){
            //必须现货
            if($newdo['huopin_type'] == 0){
                $result['error'] = '按款定价的销售政策，货品类型必须现货';
                Util::jsonExit($result);
            }
            if ($newdo['sta_value'] != 0 || $newdo['jiajia'] != 0) {
                $result['error'] = '按款定价的销售政策，加价率和固定值必须同时为0';
                Util::jsonExit($result);
            }
            $checkKuanpriceExists = $newmodel->checkKuanpriceExists();
            if($checkKuanpriceExists){
                $result['error'] = "已存在按款定价的销售政策({$checkKuanpriceExists}) ！ 提示：只能存在一个按款定价的销售政策！";
                Util::jsonExit($result);
            }
        }else{*/
		if ($newdo['sta_value'] === '') {
			$result['error'] = '固定值不能为空！';
			Util::jsonExit($result);
		} else {
			if (!(bool)preg_match('/^[0-9\.-]*$/i', $newdo['sta_value'])) {
				$result['error'] = '固定值只能是数字！';
				Util::jsonExit($result);
			} elseif (strlen($newdo['sta_value']) > 8) {
				$result['error'] = "固定值长度超出系统限制请检查(总长度不应超过8)";
				Util::jsonExit($result);
			}
		}
		if ($newdo['jiajia'] === '') {
			$result['error'] = '加价率不能为空！';
			Util::jsonExit($result);
		} else {
			if (!(bool)preg_match('/^[0-9\.]*$/i', $newdo['jiajia'])) {
				$result['error'] = '加价率只能是正数字！';
				Util::jsonExit($result);
			} elseif (strlen($newdo['jiajia']) > 8) {
				$result['error'] = "加价率长度超出系统限制请检查(总长度不应超过8)";
				Util::jsonExit($result);
			}
		}

		if ($newdo['sta_value'] == 0 && $newdo['jiajia'] == 0) {
			$result['error'] = '加价率和固定值不能同时为0';
			Util::jsonExit($result);
		}
        if ($newdo['policy_start_time'] > $newdo['policy_end_time']) {
            $result['error'] = '销售策略开始日期不能大于日期结束时间';
            Util::jsonExit($result);
        }
        $ret = $newmodel->getPolicyName($newdo['policy_name']);
        if ($ret) {
            $result['error'] = '销售策略名称不能重复!';
            Util::jsonExit($result);
        }
        if ($newdo['is_kuanprice'] != 1 && !empty($bill_no) && !empty($bill_type)) {
            $goods_id = $newmodel->getGoodsidBybillno($bill_type, $bill_no);
            foreach ($goods_id as $id) {
                foreach ($id as $k => $i) {
                    $model = new AppSalepolicyGoodsModel(18);
                    $xiangkou = $model->getxiankouBygoods_id($i);
                    $is_sale =$goodsmodel->isHaveGoodsSn($i);
                    if($is_sale=='1'){
                    if (!empty($xiangkou)) {

                        $xiangkou1 = $xiangkou['jietuoxiangkou'];
                        if (!empty($xiangkou1) && $xiangkou1 > 0) {
                            $getbxf_data = $xiangkou1;
                        } else {

                            $getbxf_data = $xiangkou['zuanshidaxiao'];
                        }
                        $baoxianfei = $new2model->GetBaoxianFei($getbxf_data);

                    } else {
                        $baoxianfei = 0;
                    }
                    $info = array();
                    $chengben = $model->getChengbenBygoods_id($i);
                    $policy_id=$newmodel->getPolicyidMax();
                    $info[$k]['sta_value'] = $newdo['sta_value'];
                    $info[$k]['create_user'] = $_SESSION['userName'];
                    $info[$k]['check_user'] = $_SESSION['userName'];
                    $info[$k]['jiajia'] = $newdo['jiajia'];
                    $info[$k]['chengben'] = $chengben;
                    $info[$k]['goods_id'] = $i;
                    $info[$k]['status'] = 3;
                    $info[$k]['policy_id'] = $policy_id;
                    $info[$k]['is_delete']=1;
                    $info[$k]['create_time'] = date("Y-m-d H:i:s");
                     $info[$k]['check_time'] = date("Y-m-d H:i:s");
                    $info[$k]['sale_price'] = round(($info[$k]['chengben'] + $baoxianfei) * $info[$k]['jiajia'] + $info[$k]['sta_value']);
                    if (!preg_match("/^\d*$/", $info[$k]['goods_id'])) {
                        $info[$k]['isXianhuo'] = 0;
                    } else {
                        $info[$k]['isXianhuo'] = 1;
                    }
                    
                    $res = $model->saveAllG($info);
                    
                }
                else{
                    $goods_ids=array();
                    $goods_ids[$k]=$i;
                    $msg.=$goods_ids[$k]." |";
                }
                }
            }
            $msg=rtrim($msg,' |');
    
            if(!$res){
                $result['error'] = "添加失败,单据号中 $msg 不是可销售商品or未上架!";
                Util::jsonExit($result);
            }
            
        }
       
          
        
        $newmodel = new BaseSalepolicyInfoModel(18);
        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
            $result['msg']='';
            if($msg!=''){
                $result['msg']=" $msg 货号未在可销售商品里or未上架";
            }
            
        } else {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    /**
     *	update，更新信息
     */
    public function update($params)
    {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('policy_id');
		$newmodel =  new BaseSalepolicyInfoModel($id,18);
		$olddo = $newmodel->getDataObject();
		/*
        if(!in_array($olddo['bsi_status'],array(1,2))){
            $result['error'] = '销售策略保存或申请中才可以修改';
            Util::jsonExit($result);
        }*/
        $cat_type_str = _Request::getString('cat_type');
        $cat_type_arr = explode('|',$cat_type_str);
        $cat_type_id = !empty($cat_type_arr[0])?$cat_type_arr[0]:"0";
        $cat_type_name = isset($cat_type_arr[1])?$cat_type_arr[1]:"";
        
        $product_type_str = _Request::getString('chanpinxian');
        $product_type_arr = explode('|',$product_type_str);
        $product_type_id = !empty($product_type_arr[0])?$product_type_arr[0]:"0";
        $product_type_name = isset($product_type_arr[1])?$product_type_arr[1]:"";
        
		$newdo=array(
			'policy_id'	=> $id,
			'policy_name'	=> _Request::getString("policy_name"),
			'policy_start_time'	=> _Request::getString("policy_start_time"),
			'policy_end_time'	=> _Request::getString("policy_end_time"),
			'is_together'	=> _Request::getString("is_together"),
			'is_delete'	=> _Request::getInt("is_delete"),
			'jiajia'	=> _Request::getFloat('jiajialv'),
			
			//update by liulinyan 2015-08-11 不对是否存在变量做改动 依照以前的写法
			'range_begin'	=> _Request::getString('range_begin'),
			'range_end'	=> _Request::getString('range_end'),
            'zhushi_begin'	=> _Request::getString("zhushi_begin"), //范围
			'zhushi_end'	=> _Request::getString("zhushi_end"), //范围
			'product_type_id'	=> $product_type_id,
		    'product_type'	=> $product_type_name,
			'tuo_type'	=> _Request::getInt('jintuo_type'),
			'huopin_type'	=> _Request::getInt('huopin_type'),
			'cat_type_id'	=> $cat_type_id,
		    'cat_type'	=> $cat_type_name,
			//end
			
			'sta_value'	=> _Request::getFloat('sta_value'),
			'check_remark'	=> _Request::getString("check_remark"),
            'is_favourable'	=> _Request::get('is_favourable')?1:2,
            'is_default'	=> _Request::get('is_default')?1:2,
			'is_kuanprice'	=> _Request::get('is_kuanprice')?1:0,
			'xilie'	=> _Request::getList('xilie'),
			'cert'	=> _Request::getList('cert'),
		    'clarity'=>_Request::getList('clarity'),
		    'color'=>_Request::getList('color'),
		);
      	//如果系列选择了全部,那么就用all代替,并且去掉其他的
		if(!empty($newdo['xilie']))
		{
			if(in_array('全部系列',$newdo['xilie'])){
				$newdo['xilie']='全部系列';
			}else{
				$newdo['xilie'] = implode(',',$newdo['xilie']);	
			}
		}else{
			$newdo['xilie']='全部系列';
		}
		//如果证书类型选择了全部,那么就用all代替,并且去掉其他的
		if(!empty($newdo['cert']))
		{
			if(in_array('全部类型',$newdo['cert'])){
				$newdo['cert']='全部类型';	
			}else{
				$newdo['cert'] = implode(',',$newdo['cert']);	
			}
		}else{
			$newdo['cert']='全部类型';	
		}
		//主石颜色
		if(!empty($newdo['color']))
		{
		    if(in_array('全部',$newdo['color'])){
		        $newdo['color']='全部';
		    }else{
		        $newdo['color'] = implode(',',$newdo['color']);
		    }
		}else{
		    $newdo['color']='全部';
		}
		//主石净度
		if(!empty($newdo['clarity']))
		{
		    if(in_array('全部',$newdo['clarity'])){
		        $newdo['clarity']='全部';
		    }else{
		        $newdo['clarity'] = implode(',',$newdo['clarity']);
		    }
		}else{
		    $newdo['clarity']='全部';
		}
		//end
        if (empty($newdo['policy_name'])) {
            $result['error'] = '销售策略名称不能为空';
            Util::jsonExit($result);
        }
        if (empty($newdo['policy_start_time'])) {
            $result['error'] = '销售策略开始时间不能为空';
            Util::jsonExit($result);
        }
        if (empty($newdo['policy_end_time'])) {
            $result['error'] = '销售策略结束时间不能为空';
            Util::jsonExit($result);
        }
        if (empty($newdo['check_remark'])) {
            $result['error'] = '记录备注不能为空';
            Util::jsonExit($result);
        }
        if ($newdo['policy_start_time'] > $newdo['policy_end_time']) {
            $result['error'] = '销售策略开始日期不能大于日期结束时间';
            Util::jsonExit($result);
        }

        /*if($newdo['is_kuanprice'] == 1){
            if($newdo['huopin_type'] == 0){
                $result['error'] = '按款定价的销售政策，货品类型必须现货';
                Util::jsonExit($result);
            }
            //必须现货
            if ($newdo['sta_value'] != 0 || $newdo['jiajia'] != 0) {
                $result['error'] = '按款定价的销售政策，加价率和固定值必须同时为0';
                Util::jsonExit($result);
            }
            $checkKuanpriceExists = $newmodel->checkKuanpriceExists();
            if($checkKuanpriceExists){
                $result['error'] = "已存在按款定价的销售政策({$checkKuanpriceExists}) ！ 提示：只能存在一个按款定价的销售政策！";
                Util::jsonExit($result);
            }
        }else{*/
            if ($newdo['sta_value'] === '') {
                $result['error'] = '固定值不能为空！';
                Util::jsonExit($result);
            } else {
                if (!(bool)preg_match('/^[0-9\.-]*$/i', $newdo['sta_value'])) {
                    $result['error'] = '固定值只能是数字！';
                    Util::jsonExit($result);
                } elseif (strlen($newdo['sta_value']) > 8) {
                    $result['error'] = "固定值长度超出系统限制请检查(总长度不应超过8)";
                    Util::jsonExit($result);
                }
            }
            if ($newdo['jiajia'] === '') {
                $result['error'] = '加价率不能为空！';
                Util::jsonExit($result);
            } else {
                if (!(bool)preg_match('/^[0-9\.]*$/i', $newdo['jiajia'])) {
                    $result['error'] = '加价率只能是正数字！';
                    Util::jsonExit($result);
                } elseif (strlen($newdo['jiajia']) > 8) {
                    $result['error'] = "加价率长度超出系统限制请检查(总长度不应超过8)";
                    Util::jsonExit($result);
                }
            }

            if ($newdo['sta_value'] == 0 && $newdo['jiajia'] == 0) {
                $result['error'] = '加价率和固定值不能同时为0';
                Util::jsonExit($result);
            }
        //}

        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }

    /**
     *	delete，删除
     */
    public function delete($params)
    {
        $result = array('success' => 0, 'error' => '');
        $id = intval($params['id']);
        $model = new BaseSalepolicyInfoModel($id, 2);
        $do = $model->getDataObject();
        $valid = $do['is_system'];
        if ($valid) {
            $result['error'] = "当前记录为系统内置，禁止删除";
            Util::jsonExit($result);
        }
        $model->setValue('is_deleted', 1);
        $res = $model->save(true);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = "删除失败";
        }
        Util::jsonExit($result);
    }

    /**
     *	bsi_statust，申请审核
     */
    public function bsi_statust($params)
    {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');

        $newmodel = new BaseSalepolicyInfoModel($id, 17);

        $olddo = $newmodel->getDataObject();

        $res = $newmodel->relData($id);
        /*
        if ($olddo['is_together'] == 1) {
            if ($res == 3) {
                $result['error'] = '销售政策中没有商品，不能申请';
                $result['error'] = '销售政策中没有商品，不能申请';
                Util::jsonExit($result);
            }
        } else {
            $goods_num = $newmodel->validateGoods($id);
            if (!$goods_num) {
                $result['error'] = '销售政策中没有商品，不能申请';
                Util::jsonExit($result);
            }
        }*/


        if ($res == 2) {
            $result['error'] = '销售政策中没有销售渠道，不能申请';
            Util::jsonExit($result);
        }

        if ($olddo['bsi_status'] != 1) {
            $result['error'] = '记录状态错误，不能申请';
            Util::jsonExit($result);
        }
        if ($olddo['is_delete'] == 1) {
            $result['error'] = '该状态已无效，不能申请';
            Util::jsonExit($result);
        }
        //检测app_salepolicy_goods base_salepolicy_info是否存可用的关联数据如果都有才能通过审核

        $newdo = array(
            'policy_id' => $id,
            'bsi_status' => 2,
            );

        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }

    /**
     *	bsi_statusts，通过
     */
    public function bsi_statusts($params)
    {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');

        $newmodel = new BaseSalepolicyInfoModel($id, 17);
        $olddo = $newmodel->getDataObject();

        if ($olddo['bsi_status'] != 2) {
            $result['error'] = '记录状态错误，不能通过';
            Util::jsonExit($result);
        }
        if ($olddo['is_delete'] == 1) {
            $result['error'] = '该状态已无效，不能通过';
            Util::jsonExit($result);
        }

        $newdo = array(
            'policy_id' => $id,
            'bsi_status' => 3,
            );

        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }

    /**
     *	not_bsi_statusts，驳回
     */
    public function not_bsi_statusts($params)
    {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');
        if ($id == '') {
            $result['error'] = 'ID错误！';
            Util::jsonExit($result);
        }
        $newmodel = new BaseSalepolicyInfoModel($id, 17);
        $olddo = $newmodel->getDataObject();

        if ($olddo['bsi_status'] != 2) {
            $result['error'] = '记录状态错误，无法驳回！';
            Util::jsonExit($result);
        }
        if ($olddo['is_delete'] == 1) {
            $result['error'] = '状态无效，无法驳回！';
            Util::jsonExit($result);
        }

        $newdo = array(
            'policy_id' => $id,
            'bsi_status' => 4,
            );

        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }

    /**
     *	bsi_statust，无效
     */
    public function is_deletet($params)
    {
        $result = array('success' => 0, 'error' => '');
        $id = _Post::getInt('id');

        $newmodel = new BaseSalepolicyInfoModel($id, 17);

        $olddo = $newmodel->getDataObject();
		/*
        if ($olddo['bsi_status'] == '3') {
            $result['error'] = '记录状态已审核通过，不能无效';
            Util::jsonExit($result);
        }
        if ($olddo['bsi_status'] == '2') {
            $result['error'] = '记录状态已申请审核，不能无效';
            Util::jsonExit($result);
        }
        if ($olddo['bsi_status'] == '4') {
            $result['error'] = '记录状态已驳回，不能无效';
            Util::jsonExit($result);
        }*/

        $newdo = array('policy_id' => $id, 'is_delete' => 1);

        $where = array();
        $where['policy_id'] = $id;

        $res = $newmodel->saveData($newdo, $olddo);
        if ($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '修改失败';
        }
        Util::jsonExit($result);
    }

    //销售政策详情 查看详情
    public function showlist($params)
    {
        $id = intval($params["id"]);

        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__class__, 0, -10),
            'act' => __function__,
            'id' => $id);

        $g_model = new BaseSalepolicyInfoModel(17);

        $where = array('policy_id' => $id);
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1;
        $data = $g_model->pageList($where, $page, 5, false);

        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'purchase_info_show_page';
        $this->render('base_salepolicy_info_show_list_list.html', array(
            'pa' => Util::page($pageData),
            'dd' => new DictView(new DictModel(17)),
            'data' => $data));
    }
    /******
    fun:print;打印标签
    ******/
    public function print_q($params)
    {
        $id = intval($params['id']);
     
        $model = new AppSalepolicyGoodsModel(17);
        $result = $model->getGoodsById($id);
        $arr = array(); //货品id 所有 字符串形式
        $new_arr = array();
        header("Content-Disposition: attachment;filename=huopin.csv");
        $str = "货号,款号,商品名称,销售价,加价率,款式分类,产品线,货品状态,所在公司,所在仓库,入库方式,材质,金重,金耗,颜色,净度,手寸,证书号,钻石大小,戒托镶口,成本价,名义成本\n";
        if ($result) {
            foreach ($result as $k => $v) {
                $arr[] = $v['goods_id'];
                $new_arr[$v['goods_id']] = $v;
            }
            $str_goods_ids = join($arr, "','");
            //调用接口查询货号的信息 仓储中
            $res = ApiModel::warehouse_api(array('goods_id'), array($str_goods_ids),
                'GetGoodsInfoByGoods');
            if ($res['return_msg']) {
                $data = $res['return_msg'];
                if ($data) {
                    //将data、new_arr数据组合
                    foreach ($data as $k => $v) {
                        $v['jiajia'] = $new_arr[$v['goods_id']]['jiajia'];
                        $v['price'] = $new_arr[$v['goods_id']]['sale_price'];
                        $data[$k] = $v;
                    }
                    //打印数据
                    //款式分类	产品线	货品状态 入库方式
                    $dd = new DictView(new DictModel(1));
                    foreach ($data as $val) {
                        //调用接口查询产品线、款式分类
                        $rrr = ApiModel::style_api(array('product_type_id'), array($val['product_type']),
                            'getProductTypeInfo');
                        if ($rrr) {
                            $val['product_type'] = $rrr[0]['name'];
                        }
                        $qqq = ApiModel::style_api(array('cat_type_id'), array($val['cat_type']),
                            'getCatTypeInfo');
                        if ($qqq) {
                            $val['cat_type'] = $qqq[0]['name'];
                        }
                        $is_on_sale = $dd->getEnum('warehouse.goods_status', $val['is_on_sale']);
                        $put_in_type = $dd->getEnum('warehouse.put_in_type', $val['put_in_type']);
                        $str .= iconv("utf-8", "utf-8", $val['goods_id'] . "," . $val['goods_sn'] . "," .
                            $val['goods_name'] . "," . $val['price'] . "," . $val['jiajia'] . "," . $val['cat_type'] .
                            "," . $val['product_type'] . "," . $is_on_sale . "," . $val['company'] . "," . $val['warehouse'] .
                            "," . $put_in_type . "," . $val['caizhi'] . "," . $val['jinzhong'] . "," . $val['jinhao'] .
                            "," . $val['yanse'] . "," . $val['jingdu'] . "," . $val['shoucun'] . "," . $val['zhengshuhao'] .
                            "," . $val['zuanshidaxiao'] . "," . $val['jietuoxiangkou'] . "," . $val['chengbenjia'] .
                            "," . $val['mingyichengben'] . "\n");
                    }
                }
            }
        }
        echo iconv("utf-8", "gbk", $str);
        exit;
    }
}

?>