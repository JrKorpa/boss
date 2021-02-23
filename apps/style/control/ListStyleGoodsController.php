<?php
/**
 *  -------------------------------------------------
 *   @file		: ListStyleGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-13 17:03:57
 *   @update	:
 *  -------------------------------------------------
 */
class ListStyleGoodsController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('downloads');

    public function __construct() {

           parent::__construct();

           //产品线
           $new_product_data= array();
           $productModel = new AppProductTypeModel(11);
           $product_data = $productModel->getCtlList();
           foreach ($product_data as $val){
               $new_product_data[$val['product_type_id']]=$val['product_type_name'];
           }
           
           //获取分类名称
            $new_cat_data= array();
            $appCatModel = new AppCatTypeModel(11);
            $cat_data = $appCatModel->getCtlList();
            foreach ($cat_data as $val){
                $new_cat_data[$val['cat_type_id']]=$val['cat_type_name'];
            }
           
            $this->assign('cat_data',$new_cat_data);//数据字典
            $this->assign('product_data',$new_product_data);//数据字典
    }

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
	    $model= new AppXilieModel(11);
        $xilie=$model->getAllXilieName();
		$this->render('list_style_goods_search_form.html',array('bar'=>Auth::getBar(),'xilie'=>$xilie));
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
			//'参数' = _Request::get("参数");
			'style_sn'	=> _Request::getString("style_sn"),
			'caizhi'	=> _Request::getInt('caizhi'),
            'yanse'    => _Request::getInt('yanse'),
			'xiangkou1'	=> _Request::getString("xiangkou1"),
			'xiangkou2'	=> _Request::getString("xiangkou2"),
			'finger1'	=> _Request::getInt("finger1"),
			'finger2'	=> _Request::getInt("finger2"),
			'status'	=> _Request::getInt("status"),
            'xilie[]' => _Request::getList('xilie'),
		    'is_quick_diy' => _Request::get('is_quick_diy'),
		);
		
		$page = _Request::getInt("page",1);
		$where = array(
			'style_sn'	=> $args["style_sn"],
			'caizhi'	=> $args["caizhi"],
            'yanse'     => $args["yanse"],
			'xiangkou1'	=> $args["xiangkou1"],
			'xiangkou2'	=> $args["xiangkou2"],
			'finger1'	=> $args["finger1"],
			'finger2'	=> $args["finger2"],
			'status'	=> $args["status"],
			'xilie'	    => $args["xilie[]"],
		    'is_quick_diy'	    => $args["is_quick_diy"]
			);
        
        //echo '<pre>';
        //print_r($where);die;
        $model = new ListStyleGoodsModel(11);
        $data = $model->pageList($where,$page,300,false);

        //取出费用
        $str_style_ids = implode("','", array_unique(array_column($data['data'],'style_id')) );
        $feeModel = new AppStyleFeeModel(11);
        $res = $feeModel->getStylesFees($str_style_ids);
        $style_fees = array();
        if (!empty($res)) {
            foreach ($res as $item) {
                $style_id = $item['style_id'];
                $fee_type = $item['fee_type'];
                $style_fees[$style_id][$fee_type] = $item['price'];
            }
        }
        //取出颜色
        $this->dd = new DictView(new DictModel(1));
        $color = array();
        $yanSeAll = $this->dd->getEnumArray("style.color");
        if($yanSeAll){
            foreach ($yanSeAll as $key => $value) {
                # code...
                $color[$value['name']] = $value['label'];
            }
        }
        foreach($data['data'] as $k=>$v){
            // 材质
            $data['data'][$k]['caizhitype'] = $v['caizhi']=='1' ? '18K' : 'PT950';
            // 颜色
            $data['data'][$k]['pcolor'] = isset($color[$v['yanse']])?$color[$v['yanse']]:'';
            // $v
            $fees = isset($style_fees[$v['style_id']]) ? $style_fees[$v['style_id']] : array();
            // 1：18k工费，4：PT950工费
            if ($v['caizhi']=='1') {
                $data['data'][$k]['caizhifee'] = isset($fees[1]) ? $fees[1] : '----';
            } else {
                $data['data'][$k]['caizhifee'] = isset($fees[4]) ? $fees[4] : '----';
            }
            $data['data'][$k]['chaoshifee'] = isset($fees[2]) ? $fees[2] : '----';
            $data['data'][$k]['biaomiangongyifee'] = isset($fees[3]) ? $fees[3] : '----';

            $data['data'][$k]['pcolor'] = isset($color[$v['yanse']])?$color[$v['yanse']]:'';
            if(!empty($data['data'][$k]['xiangkou_company_type'])){
                $data['data'][$k]['xiangkou_company_type']=str_replace('1','直营店',$data['data'][$k]['xiangkou_company_type']);
                $data['data'][$k]['xiangkou_company_type']=str_replace('2','托管店',$data['data'][$k]['xiangkou_company_type']);
                $data['data'][$k]['xiangkou_company_type']=str_replace('3','经销商',$data['data'][$k]['xiangkou_company_type']);
                $data['data'][$k]['xiangkou_company_type']=trim($data['data'][$k]['xiangkou_company_type'],',');
            }
        }

        //echo '<pre>';
        //print_r($color);die;
        //颜色正则取颜色
        /*$color_arr = array('W' => "白", 'Y' => "黄", 'R' => "玫瑰金", 'C' => "分色");
        $patt = '/-([A-Z])-/';
        foreach($data['data'] as $k=>$v){
            preg_match($patt,$v['goods_sn'],$m);
            if(empty($m)){
                $data['data'][$k]['pcolor']='--';
                continue;
            }
            if(array_key_exists($m[1],$color_arr)){
                $data['data'][$k]['pcolor']=$color_arr[$m[1]];
            }else{
                $data['data'][$k]['pcolor']='--';
            }
        }*/

        $pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'list_style_goods_search_page';
		$this->render('list_style_goods_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	downloads，下载
	 */
	public function downloads ($params)
	{
        ini_set('memory_limit','6000M');
        set_time_limit(0);
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");
			'style_sn'	=> _Request::getString("style_sn"),
			'caizhi'	=> _Request::getInt('caizhi'),
			'xiangkou1'	=> _Request::getFloat("xiangkou1"),
			'xiangkou2'	=> _Request::getFloat("xiangkou2"),
			'finger1'	=> _Request::getInt("finger1"),
			'finger2'	=> _Request::getInt("finger2"),
			'status'	=> _Request::getInt("status"),
            'xilie[]' => _Request::getList('xilie')
		);
		
		$page = _Request::getInt("page",1);
		$where = array(
				'style_sn'	=> $args["style_sn"],
				'caizhi'	=> $args["caizhi"],
				'xiangkou1'	=> $args["xiangkou1"],
				'xiangkou2'	=> $args["xiangkou2"],
				'finger1'	=> $args["finger1"],
				'finger2'	=> $args["finger2"],
				'status'	=> $args["status"],
                'xilie'	=> implode(",",$args["xilie[]"])
			);
        $model = new ListStyleGoodsModel(11);
        $data['data'] = $model->pageListAll($where);

        if($data['data']!=''){
            //取出费用
            $str_style_ids = implode("','", array_unique(array_column($data['data'],'style_id')) );
            $feeModel = new AppStyleFeeModel();
            $res = $feeModel->getStyleFee($str_style_ids);
            $style_fees = array();
            if (!empty($res)) {
                foreach ($res as $item) {
                    $style_id = $item['style_id'];
                    $fee_type = $item['fee_type'];
                    $style_fees[$style_id][$fee_type] = $item['price'];
                }
            }

            //颜色正则取颜色
            $color_arr = array('W' => "白色", 'Y' => "黄色", 'R' => "玫瑰色", 'C' => "分色");
            $patt = '/-([A-Z])-/';
            foreach($data['data'] as $k=>$v){
                preg_match($patt,$v['goods_sn'],$m);
                if(empty($m)){
                    $data['data'][$k]['pcolor']='--';
                    continue;
                }
                if(array_key_exists($m[1],$color_arr)){
                    $data['data'][$k]['pcolor']=$color_arr[$m[1]];
                }else{
                    $data['data'][$k]['pcolor']='--';
                }
            }
        
            foreach($data['data'] as $k=>$v){
                $datalists[$k]['style_sn']=$v['style_sn'];
                $datalists[$k]['goods_sn']=$v['goods_sn'];
                $datalists[$k]['zhushizhong']=$v['zhushizhong'];
                $datalists[$k]['zhushi_num']=$v['zhushi_num'];
                $datalists[$k]['fushizhong1']=$v['fushizhong1'];
                $datalists[$k]['fushi_num1']=$v['fushi_num1'];
                $datalists[$k]['fushizhong2']=$v['fushizhong2'];
                $datalists[$k]['fushi_num2']=$v['fushi_num2'];
                $datalists[$k]['fushizhong3']=$v['fushizhong3'];
                $datalists[$k]['fushi_num3']=$v['fushi_num3'];
                $datalists[$k]['fushi_chengbenjia_other']=$v['fushi_chengbenjia_other'];

                $data['data'][$k]['caizhi'] = $v['caizhi']=='1' ? '18K' : 'PT950';
                $datalists[$k]['pcolor']=$v['pcolor'];
                $datalists[$k]['weight']=$v['weight'];
                $datalists[$k]['jincha_shang']=$v['jincha_shang'];
                $datalists[$k]['jincha_xia']=$v['jincha_xia'];
                $datalists[$k]['xiangkou']=$v['xiangkou'];
                $datalists[$k]['shoucun']=$v['shoucun'];

                $fees = isset($style_fees[$v['style_id']]) ? $style_fees[$v['style_id']] : array();
                // 1：18k工费，4：PT950工费
                if ($v['caizhi']=='1') {
                    $data['data'][$k]['caizhifee'] = isset($fees[1]) ? $fees[1] : '--';
                } else {
                    $data['data'][$k]['caizhifee'] = isset($fees[4]) ? $fees[4] : '--';
                }
                $data['data'][$k]['chaoshifee'] = isset($fees[2]) ? $fees[2] : '--';
                $data['data'][$k]['biaomiangongyifee'] = isset($fees[3]) ? $fees[3] : '--';
                $datalists[$k]['dingzhichengben']=$v['dingzhichengben'];

                if($v['is_ok']=='1'){
                    $datalists[$k]['is_ok']='上架';
                }else{
                    $datalists[$k]['is_ok']='下架';
                }
            }
        }else{
            $datalists = array();
        } 
        $title = array(
				'款号',
                '商品编号',
                '主石重',
                '主石数',
                '副石重1',
                '副石数1',
                '副石重2',
                '副石数2',
                '副石重3',
                '副石数3',
                '其他副石成本价',
                '材质',
                '颜色',
                '材质金重',
                '金重上公差',
				'金重下公差',
				'镶口',
                '手寸',
                '材质费',
                '超石工费',
                '表面工艺费',
                '定制成本',
                '状态');
            
            Util::downloadCsv("款式库商品信息",$title,$datalists);        
        /*$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'list_style_goods_search_page';
		$this->render('list_style_goods_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));*/
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('list_style_goods_info.html',array(
			'view'=>new ListStyleGoodsView(new ListStyleGoodsModel(11))
		));
		$result['title'] = '添加';
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
		$result['content'] = $this->fetch('list_style_goods_info.html',array(
			'view'=>new ListStyleGoodsView(new ListStyleGoodsModel($id,11)),
			'tab_id'=>$tab_id
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		die('开发中');
		$id = intval($params["id"]);
		$this->render('list_style_goods_show.html',array(
			'view'=>new ListStyleGoodsView(new ListStyleGoodsModel($id,11)),
			'bar'=>Auth::getViewBar($id)
		));
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new ListStyleGoodsModel($id,12);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	edx_status，上架
	 */
	public function edx_status ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$ids = _Request::getList('_ids');
        
        if(empty($ids)){
            $result['error'] = "没有选中数据";
            Util::jsonExit($result);
        }
        //判断一下选中的商品是否都下架，
        $newmodel =  new ListStyleGoodsModel(12);
        $id_str = implode(",", $ids);
        //查看选中数据，如果没有下架的，则说明都已经上架了
        $where = array('id_in'=>$id_str,'is_ok'=>0);
        $up_info = $newmodel->getStyleGoods($where);
        if(empty($up_info)){
            $result['error'] = "您选中的数据都已经上架了";
            Util::jsonExit($result);
        }
        
        //获取下架的id
        $up_ids = array();
        $up_goods_sn = array();
        foreach ($up_info as $val){
            $up_ids[] = $val['goods_id'];
            $up_goods_sn[$val['goods_id']] = $val['goods_sn'];
        }
        //修改成上架
        $up_id_in = implode(",", $up_ids);
        $up_where = array('id_in'=>$up_id_in,'is_ok'=>1);
        $res = $newmodel->updateStyleGoodsStupdate($up_where);
       
		if($res){
            //推送至销售政策
			$apiSalePolicyModel = new ApiSalePolicyModel();
            foreach ($up_goods_sn as $val){
                $salepolicy_data[] = array('goods_id'=>$val,'is_sale'=>'1','is_valid'=>1,'isXianhuo'=>0);
            }
			$apiSalePolicyModel->UpdateSalepolicygoodIsSale(array('update_data'=>$salepolicy_data));
		}
		
		if($res !== false){
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '操作成功';
		}else{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	eds_status，下架
	 */
	public function eds_status ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');
        
        $ids = _Request::getList('_ids');
        
        if(empty($ids)){
            $result['error'] = "没有选中数据";
            Util::jsonExit($result);
        }
        //判断一下选中的商品是否都下架，
        $newmodel =  new ListStyleGoodsModel(12);
        $id_str = implode(",", $ids);
        //查看选中数据，如果没有上架的，则说明都已经下架了
        $where = array('id_in'=>$id_str,'is_ok'=>1);
        $up_info = $newmodel->getStyleGoods($where);
        if(empty($up_info)){
            $result['error'] = "您选中的数据都已经下架了";
            Util::jsonExit($result);
        }
        
        //获取上架的id
        $up_ids = array();
        $up_goods_sn = array();
        foreach ($up_info as $val){
            $up_ids[] = $val['goods_id'];
            $up_goods_sn[$val['goods_id']] = $val['goods_sn'];
        }
        
        $up_id_in = implode(",", $up_ids);
        $up_where = array('id_in'=>$up_id_in,'is_ok'=>0);
        $res = $newmodel->updateStyleGoodsStupdate($up_where);
       
		if($res){
            //推送至销售政策
			$apiSalePolicyModel = new ApiSalePolicyModel();
            foreach ($up_goods_sn as $val){
                $salepolicy_data[] = array('goods_id'=>$val,'is_sale'=>'0','is_valid'=>3,'isXianhuo'=>0);
            }
			
			$apiSalePolicyModel->UpdateSalepolicygoodIsSale(array('update_data'=>$salepolicy_data));
		}
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '操作成功';
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}
	
	
	/*------------------------------------------------------ */
	//-- 更新商品成本价格
	//-- BY linian
	/*------------------------------------------------------ */
	public function update_price($params) {
	
		$result = array('success' => 0, 'error' => '');
		//$style_id = _Post::getInt('id');
	
		$ids = _Request::getList('_ids');
        if(empty($ids)){
            $result['error'] = "没有选中数据";
            Util::jsonExit($result);
        }
        $id_str = implode(",", $ids);
        $model = new ListStyleGoodsModel(11);
        //1,获取商品表中所有商品
        $data = $model->getAllGoodsinfoByids($id_str);
		//var_dump($data);exit;
		//遍历所有商品数据 没遍历一条更新都更新商品成本价格
		foreach($data as $key=>$val){

			//2,每次获取一条基本数据
			$goods_id = $val['goods_id'];
			$style_id = $val['style_id'];
			$style_sn = $val['style_sn'];
			$yanse = $val['yanse'];
			$fushi_1 = $val['fushizhong1'];
			$fushi_num_1 = $val['fushi_num1'];
			$fushi_2 = $val['fushizhong2'];
			$fushi_num_2 = $val['fushi_num2'];
            $fushi_3 = $val['fushizhong3'];
            $fushi_num_3 = $val['fushi_num3'];
			$caizhi = $val['caizhi'];
			$weight = $val['weight'];
            $xiangkou = $val['xiangkou'];
			$jincha_shang = $val['jincha_shang'];
            $product_type_id = $val['product_type_id'];

            $goods_sn[]= $val['goods_sn'];
				
			
			//工费信息  基础工费 表面工艺费 超石费
			$newmodel =  new AppStyleFeeModel(11);
			if(!empty($style_id)){
				//获取三种工费
				$gongfei='';
                $baoxianfei = '';
				$baomiangongyi_gongfei='';
				$chaoshifee='';
				$gongfei_data = $newmodel->getStyleFee($style_id);
				foreach($gongfei_data as $val){
					if($val['fee_type']==1 && $caizhi==1){
						$gongfei = empty($val['price'])?'0':$val['price'];
					}elseif($val['fee_type']==2){
						$chaoshifee = empty($val['price'])?'0':$val['price'];
                        $chaoshifee = $chaoshifee * ($fushi_num_1+$fushi_num_2+$fushi_num_3);
					}elseif($val['fee_type']==3){
						$baomiangongyi_gongfei = empty($val['price'])?'0':$val['price'];
					}elseif($val['fee_type']==4 && $caizhi==2){
                        $gongfei = empty($val['price'])?'0':$val['price'];
                    }
				}

                $productTypeModel = new AppProductTypeModel(11);
                $parent_id = $productTypeModel->getParentIdById($product_type_id);
                if($parent_id == 3 && $xiangkou != ''){
                    $baoxianfeeModel = new AppStyleBaoxianfeeModel(11);
                    $baoxianfei = $baoxianfeeModel->getPriceByXiangkou($xiangkou);
                }
				
			}
			
			//4,计算各种工费数据
            //var_dump($gongfei,$baomiangongyi_gongfei,$chaoshifee,$baoxianfei);die;
			$tal_gongfei = $gongfei+$baomiangongyi_gongfei+$chaoshifee+$baoxianfei;
			//$gongfei = empty($val['gongfei'])?'':$val['gongfei'];
			//$baomiangongyi_gongfei = empty($val['baomiangongyi_gongfei'])?'':$val['baomiangongyi_gongfei'];
			//$chaoshifee = empty($val['chaoshifee'])?'':$val['chaoshifee'];
	
			//金损率:price_type:1男戒2女戒3情侣男戒4情侣女戒;
			//3,判断款号是什么什么戒指，来获取对应的金损
			$model = new AppJinsunModel(11);
			if(!empty($caizhi)){
				//材质
				$where['material_id']=$caizhi;
				//2 女戒
				$where['price_type']=2;
				$jinsundata = $model->pageList($where,10);
				if($jinsundata['data']){
					$jinsunlv = $jinsundata['data'][0]['lv'];
					
				}
			}

			//5,获取所有钻石规格单价数据
			//(副石1重/副石1数量)的对应单价*副石1重+（副石2重/副石2数量）的对应单价*副石2重
			$newmodel =  new AppDiamondPriceModel(19);
			if($fushi_num_1){
				$where['guige'] = 100 * $fushi_1 / $fushi_num_1;
				//获取副石1价格
				$diamondprice = $newmodel->getDanPrice($where);
				$fushi_price_1=$diamondprice['price']*$fushi_1;
			}else{
                $fushi_price_1='';
            }
			if($fushi_num_2){
				$where['guige'] = 100 * $fushi_2 / $fushi_num_2;
				//获取副石2价格
				$diamondprice = $newmodel->getDanPrice($where);
				$fushi_price_2=$diamondprice['price']*$fushi_2;
			}else{
                $fushi_price_2='';
            }
            if($fushi_num_3){
                $where['guige'] = 100 * $fushi_3 / $fushi_num_3;
                //获取副石3价格
                $diamondprice = $newmodel->getDanPrice($where);
                $fushi_price_3=$diamondprice['price']*$fushi_3;
            }else{
                $fushi_price_3='';
            }
            //var_dump($fushi_price_1,$fushi_price_2,$fushi_price_3);die;
			//6,(材质金重+向上公差）*金损率* 对应材质单价
			//材质单价:price_type :1=》18K；2=>PT950; price:价格; type = 2
			$model = new AppMaterialInfoModel(11);
			if(!empty($caizhi)){
				if($caizhi ==1){
					$material_name ='18K';
				}elseif($caizhi ==2){
					$material_name ='PT950';
				}
				//材质
				$where['material_name']=$material_name;
				$where['material_status']=1;
				//获取对应的材质单价
				$caizhidata = $model->pageList($where,10);
				$caizhi_price = $caizhidata['data'][0]['price'];
                $shuidian = $caizhidata['data'][0]['tax_point'];
			}
			//7,金损率 等于1+金损率
			$jinsun_price = $jinsunlv+1;
			//8,计算金损价格
            //var_dump($weight,$jincha_shang,$jinsun_price,$caizhi_price);die;
			$tal_jinsun = ($weight + $jincha_shang) * $jinsun_price * $caizhi_price;
			//9,计算定制成本价格
			$model = new ListStyleGoodsModel(12);
            //var_dump($fushi_price_1 , $fushi_price_2 , $fushi_price_3 , $tal_jinsun , $tal_gongfei, $shuidian);die;
			$dingzhichengben = ($fushi_price_1 + $fushi_price_2 + $fushi_price_3 + $tal_jinsun + $tal_gongfei) * (1 + $shuidian);
			//var_dump($dingzhichengben,88);die;
			$where['chengbenjia'] = round($dingzhichengben,2);
			//var_dump($where['chengbenjia'],99);
			$where['goods_id'] =$goods_id;
			$res = $model->updateChengbenPrice($where);

            $chenbenjia[] =$where['chengbenjia'];
        }

        //更新定制成本
        $salepolicyModel = new SalepolicyModel(18);
        $res = false;
        if(!empty($goods_sn) && !empty($chenbenjia)){
            $res = $salepolicyModel->UpdateSalepolicyChengben($goods_sn,$chenbenjia);
        }
		if ($res !== false) {
			$result['success'] = 1;
		} else {
			$result['error'] = '更新价格失败';
		}
		Util::jsonExit($result);
	
	}
    
    //推送至可销售政策商品列表
    function createSalepolicyGoods(){
        $result = array('success' => 0,'error' => '');
        $ids = _Request::getList('_ids');
        if(empty($ids)){
            $result['error'] = "没有选中数据";
            Util::jsonExit($result);
        }

        //判断有款是否有效：不审核的都不生成
        $no_style_valid = array();
        //获取选中的商品信息
        $id_in = implode(",", $ids);
        $listStyleModel = new ListStyleGoodsModel(11);
        $where = array('id_in'=>$id_in);
        $data = $listStyleModel->getStyleGoods($where);
       
        $style_arr = array();
        foreach($data as $val){
            $style_sn = $val['style_sn'];
            if($val['check_status']!=3){
                $no_style_valid[$style_sn]=$val['check_status'];
            }
            $style_arr[$style_sn]=$val['check_status'];
        }
       
        //判断选中的数据是否都是一个款，并且此款的状态不是审核的
        $is_flag = false;
        foreach ($style_arr as $val){
            if($val == 3){
                $is_flag = true;
            }
        }
        
        if(!$is_flag){
            $result['error'] = '此订单的状态不是审核状态';
            Util::jsonExit($result);
        }
       
        $salepolicy_data = array();
        foreach ($data as $val){
            if(!array_key_exists($val['style_sn'], $no_style_valid)){
                $salepolicy_data[] = array('goods_id'=>$val['goods_sn'],'goods_sn'=>$val['style_sn'],'goods_name'=>$val['style_name'],'chengbenjia'=>$val['dingzhichengben'],'xiangkou'=>$val['xiangkou'],'caizhi'=>$val['caizhi'],'finger'=>$val['shoucun'],'yanse'=>$val['yanse'],'stone'=>$val['xiangkou'],'category'=>$val['cat_type_id'],'product_type'=>$val['product_type_id'],'isXianhuo'=>0,'is_base_style'=>$val['is_base_style']);
            }
        }
       
        $res = $this->addSoplicyGoods($salepolicy_data);
        if ($res !== false) {
			$result['success'] = 1;
		} else {
			$result['error'] = '操作失败';
		}
		Util::jsonExit($result);
    }
    
    public function addSoplicyGoods($data){
        //把商品数据推送到销售政策可销售商品
        $apiSalePolicyModel = new ApiSalePolicyModel();
        return $apiSalePolicyModel->AddAppPayDetail(array('insert_data'=>$data));
    }
}

?>