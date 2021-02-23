<?php
/**
 *  -------------------------------------------------
 *   @file		: AppSalepolicyGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-17 18:36:47
 *   @update	:
 *  -------------------------------------------------
 */
class AppSalepolicyGoodsController extends CommonController
{
	protected $smartyDebugEnabled = true;
    protected $whitelist = array('downcsv','printcode','downLoads','dow');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_salepolicy_goods_search_form.html',array('bar'=>Auth::getBar()));
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
			'policy_id' => _Request::getInt('_id'),
            'goods_sn'=>_Request::get('goods_sn'),
            'goods_id'=>_Request::get('goods_id'),
            'xianhuo'=>_Request::get('xianhuo'),
            'min_p'=>_Request::get('min_p'),
            'max_p'=>_Request::get('max_p'),
            'is_valid'=>_Request::get('is_valid'),
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where = array(
                'goods_sn'=>$args['goods_sn'],
                'goods_id'=>$args['goods_id'],
				'policy_id'=>$args['policy_id'],
				'xianhuo'=>$args['xianhuo'],
				'min_p'=>$args['min_p'],
				'max_p'=>$args['max_p'],
				'is_valid'=>$args['is_valid'],
			);


		//$model = new AppSalepolicyGoodsModel(17);
		$model = new AppSalepolicyGoodsModel(15);//只读数据库
		$data = $model->pageList($where,$page,40,false);
 
    
        //查看此货品如果已经下架那么需要把此货品的状态改为取消
        // 本来是主动关系 为什么要改变状态
	    //$baseSalepolicyGoodsModel = new BaseSalepolicyGoodsModel(17);
		$baseSalepolicyGoodsModel = new BaseSalepolicyGoodsModel(15);//只读数据库
        foreach ($data['data'] as $key=>$val){
            $info = $baseSalepolicyGoodsModel->isHaveGoodsId($val['goods_id']);
            if($info['is_sale'] == 0){
                $data['data'][$key]['new_status']=1;
            }else{
                $data['data'][$key]['new_status']=0;
            }
        }
        if($data['data']){
            $goods_ids = implode("','",array_column($data['data'],'goods_id'));
            //取仓库的货品状态 
      
            $wapimodel = new ApiWarehouseModel();;
            $is_on_sales = array_column($wapimodel->getWaregoodisonsale($goods_ids),'is_on_sale','goods_id');
            
            foreach ($data['data'] as &$val){
                $val['is_on_sale']=$is_on_sales[$val['goods_id']];
            }
        }

        
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_salepolicy_goods_search_page';
		$this->render('app_salepolicy_goods_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}
    
	/**
	 *	downLoads，导出
	 */
	public function downLoads ($params)
	{
        
        set_time_limit(0);
        ini_set('memory_limit','6000M');
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");
			'policy_id' => _Request::getInt('_id'),
            'goods_sn'=>_Request::get('goods_sn'),
            'goods_id'=>_Request::get('goods_id'),
            'xianhuo'=>_Request::get('xianhuo'),
            'min_p'=>_Request::get('min_p'),
            'max_p'=>_Request::get('max_p'),
            'is_valid'=>_Request::get('is_valid'),
		);

		$where = array(
                'goods_sn'=>$args['goods_sn'],
                'goods_id'=>$args['goods_id'],
				'policy_id'=>$args['policy_id'],
				'xianhuo'=>$args['xianhuo'],
				'min_p'=>$args['min_p'],
				'max_p'=>$args['max_p'],
				'is_valid'=>$args['is_valid'],
			);
        
        $downLoads=date("YmdHis");
        $path="apps/salepolicy/downLoads";
        if (!is_dir($path)){  
            mkdir(iconv("UTF-8", "GBK", $path),0777,true);
            $handle=fopen($path."/".$downLoads.".csv", "w");
        }else{
            if(!file_exists($path."/".$downLoads.".csv")){
                $handle=fopen($path."/".$downLoads.".csv", "w");
            }     
        }

        $title = array(
				'货号',
				'固定值',
				'加价率',
				'销售价',
				'产品线',
				'款式分类',
				'入库方式',
				'状态所在仓库',
				'款号',
				'系列',
				'模号',
				'名称',
				'名义成本',
				'采购成本',
				'定制成本',
				'材质',
				'金重',
				'手寸',
				'金托类型',
				'主石',
				'主石粒数',
				'主石形状',
				'主石大小',
				'主石颜色',
				'主石净度',
                '主石切工',
                '主石规格',
                '副石1',
                '副石1粒数',
                '副石1重',
                '副石2',
                '副石2粒数',
                '副石2重',
                '副石3',
                '副石3粒数',
                '副石3重',
                '证书号',
                '证书类型',
                '金饰类型',
                '结价是否绑定',
                '所在公司',
                '总库龄',
                '本库库龄',
                '柜位',
                '戒托实际镶口'
                );
            foreach($title as $k=>$v){
                $title[$k]=iconv("UTF-8","GBK",$v);
            }
            fputcsv($handle,$title);

            //取款式库
            $Styleapimodel = new ApiStyleModel();
            $product_type=$Styleapimodel->getProductTypeInfo();//产品线
            foreach($product_type as $k=>$v){
                $product_type_id[$v['id']]=$v['name'];
            }
            $CatType=$Styleapimodel->getCatTypeInfo();//款式分类
            foreach($product_type as $k=>$v){
                $CatType_id[$v['id']]=$v['name'];
            }

		$model = new AppSalepolicyGoodsModel(17);
        $page=1;
        $limit=2500;
        while(true)
        {
            $start=($page-1)*$limit;
		$data = $model->getAllList($where,$start,$limit);

        if($data['data']==null)
        {
            break;
            exit();
        }

        $page++;
        $dd =new DictModel(1);
        if($data['data']){
            $goods_ids = implode("','",array_column($data['data'],'goods_id'));
            //取仓库的货品状态
            $wapimodel = new ApiWarehouseModel();
            $WaregoodList =$model->getWaregoodisonsale($goods_ids);
            
            $sta_value=$jiajia=array();
            foreach($data['data'] as $k=>$v){
                $sta_value[$v['goods_id']]['sta_value']=$v['sta_value'];
                $jiajia[$v['goods_id']]['jiajia']=$v['jiajia'];
                $jiajia[$v['goods_id']]['sale_price']=$v['sale_price'];
            }

            $goods_arr=array();
            $datalists=array();
            foreach ($WaregoodList as $key=>$val){
                  if(!empty($val)){
                        foreach($val as $k=>$v){
                            $val[$k]=iconv("utf-8","gbk",$v);
                        }
                        $datalists[$key]['goods_id']=$val['goods_id'];
                        //$datalists[$key]['sta_value']=$v['sta_value'];//固定值
                        $datalists[$key]['sta_value']=$sta_value[$val['goods_id']]['sta_value'];//固定值
                        //$datalists[$key]['jiajia']=$v['jiajia'];//加价率
                        $datalists[$key]['jiajia']=$jiajia[$val['goods_id']]['jiajia'];//加价率
                        $datalists[$key]['sale_price']=$jiajia[$val['goods_id']]['sale_price'];//销售价
                        $datalists[$key]['product_type']=$val['product_type'];
                        $datalists[$key]['cat_type']=$val['cat_type'];
                        $datalists[$key]['put_in_type']=isset($val['put_in_type'])&&!empty($val['put_in_type'])?$dd->getEnum('warehouse.put_in_type',$val['put_in_type']):'';
                        $datalists[$key]['is_on_sale']=isset($val['is_on_sale'])&&!empty($val['is_on_sale'])?$dd->getEnum('warehouse.goods_status',$val['is_on_sale']):'';
                        $datalists[$key]['goods_sn']=$val['goods_sn'];
                        
                        $xilie=$model->getStyleBystyle_sn($val['goods_sn']);//系列 
                        if(isset($xilie['xilie'])&&!empty($xilie['xilie'])){
                            $xilie['xilie']=array_filter(explode(",",$xilie['xilie']));
                            $s='';
                            foreach($xilie['xilie'] as $k=>$v){
                                $s.=$dd->getEnum('style.xilie',$v).",";
                            }
                            $datalists[$key]['xilie']=$s;//系列
                        }else{
                            $datalists[$key]['xilie']='';
                        }
                        
                        $datalists[$key]['mo_sn']=$val['mo_sn'];
                        $datalists[$key]['goods_name']=$val['goods_name'];
                        $datalists[$key]['mingyichengben']=$val['mingyichengben'];
                        $datalists[$key]['chengbenjia']=$val['chengbenjia'];
                        $datalists[$key]['dingzhichengben']='';
                        $datalists[$key]['caizhi']=$val['caizhi'];
                        $datalists[$key]['jinzhong']=$val['jinzhong'];
                        $datalists[$key]['shoucun']=$val['shoucun'];
                        $datalists[$key]['tuo_type']=$val['tuo_type'];
                        $datalists[$key]['zhushi']=$val['zhushi'];
                        $datalists[$key]['zhushilishu']=$val['zhushilishu'];
                        $datalists[$key]['zhushixingzhuang']=$val['zhushixingzhuang'];
                        $datalists[$key]['zuanshidaxiao']=$val['zuanshidaxiao'];
                        $datalists[$key]['zhushiyanse']=$val['zhushiyanse'];
                        $datalists[$key]['zhushijingdu']=$val['zhushijingdu'];
                        $datalists[$key]['zhushiqiegong']=$val['zhushiqiegong'];
                        $datalists[$key]['zhushiguige']=$val['zhushiguige'];
                        $datalists[$key]['fushi']=$val['fushi'];
                        $datalists[$key]['fushilishu']=$val['fushilishu'];
                        $datalists[$key]['fushizhong']=$val['fushizhong'];
                        $datalists[$key]['shi2']=$val['shi2'];
                        $datalists[$key]['shi2lishu']=$val['shi2lishu'];
                        $datalists[$key]['shi2zhong']=$val['shi2zhong'];
                        $datalists[$key]['shi3']='';
                        $datalists[$key]['shi3lishu']='';
                        $datalists[$key]['shi3zhong']='';
                        $datalists[$key]['zhengshuhao']=$val['zhengshuhao'];
                        $datalists[$key]['zhengshuleibie']=$val['zhengshuleibie'];
                        $datalists[$key]['ziyin']=$val['ziyin'];
                        if($val['jiejia']=='0'){
                            $datalists[$key]['jiejia']="未结价";
                        }elseif($val['jiejia']=='1'){
                            $datalists[$key]['jiejia']="已结价";
                        }
                        $datalists[$key]['company']=$val['company'];
                        if(isset($val['allage'])&&!empty($val['allage'])){
                            //$datalists[$key]['allage']=date('Y-m-d H:i:s')-$val['addtime'];
                            $datalists[$key]['allage']=floor((strtotime(date('Y-m-d H:i:s'))-strtotime($val['addtime']))/86400);
                        }else{
                            $datalists[$key]['allage']='未统计';
                        }
                        if(isset($val['thisage'])&&!empty($val['thisage'])){
                            //$datalists[$key]['thisage']=date('Y-m-d H:i:s')-$val['change_time'];
                            $datalists[$key]['thisage']=floor((strtotime(date('Y-m-d H:i:s'))-strtotime($val['change_time']))/86400);
                        }else{
                            $datalists[$key]['thisage']='未统计';
                        }
                        $datalists[$key]['box_sn']=$val['box_sn'];
                        $datalists[$key]['jietuoxiangkou']=$val['jietuoxiangkou'];
                        $goods_arr[]=$val['goods_id'];
                  }      
            }
           
                $goods_ids=str_replace("'","",$goods_ids);
                $goods_ids=explode(",",$goods_ids);
                $st=array();
                foreach($goods_ids as $k=>$v){
                    if(!in_array($v,$goods_arr)){
                        $st[]=$v;
                    }
                }
                
                if($st){
                    $st=implode("','",$st);
                    $StylegoodList =$model->GetStyleGoods($st);
                    foreach($StylegoodList as $key=>$val){
                          if(!empty($val)){
                                foreach($val as $k=>$v){
                                    $val[$k]=iconv("utf-8","gbk",$v);
                                }
                                $Stylegdatalists[$key]['goods_id']=$val['goods_sn'];
                                //$Stylegdatalists[$key]['sta_value']=$v['sta_value'];//固定值
                                $Stylegdatalists[$key]['sta_value']=$sta_value[$val['goods_sn']]['sta_value'];//固定值
                                //$Stylegdatalists[$key]['jiajia']=$v['jiajia'];//加价率 
                                $Stylegdatalists[$key]['jiajia']=$jiajia[$val['goods_sn']]['jiajia'];//加价率 
                                $Stylegdatalists[$key]['sale_price']=$jiajia[$val['goods_sn']]['sale_price'];//销售价 
                                $Stylegdatalists[$key]['product_type_id']=$product_type_id[$val['product_type_id']]; //产品线
                                $Stylegdatalists[$key]['cat_type_id']=$CatType_id[$val['cat_type_id']]; //款式分类
                                $Stylegdatalists[$key]['put_in_type']=''; 
                                $Stylegdatalists[$key]['is_on_sale']=''; 
                                $Stylegdatalists[$key]['style_sn']=$val['style_sn']; 
                                $xilie=$model->getStyleBystyle_sn($val['style_sn']);
                                if(isset($xilie['xilie'])&&!empty($xilie['xilie'])){
                                    $xilie['xilie']=array_filter(explode(",",$xilie['xilie']));
                                    $s='';
                                    foreach($xilie['xilie'] as $k=>$v){
                                        $s.=$dd->getEnum('style.xilie',$v).",";
                                    }
                                    $Stylegdatalists[$key]['xilie']=$s;//系列
                                }else{
                                    $Stylegdatalists[$key]['xilie']='';
                                }
                                $Stylegdatalists[$key]['mo_sn']='';
                                $Stylegdatalists[$key]['style_name']=$val['style_name'];
                                $Stylegdatalists[$key]['mingyichengben']='';
                                $Stylegdatalists[$key]['chengbenjia']='';
                                $Stylegdatalists[$key]['dingzhichengben']=$val['dingzhichengben'];
                                if($val['caizhi']==1){
                                   $Stylegdatalists[$key]['caizhi']='18K'; 
                                }elseif($val['caizhi']==2){
                                   $Stylegdatalists[$key]['caizhi']='PT950'; 
                                }else{
                                   $Stylegdatalists[$key]['caizhi']=''; 
                                }
                                $Stylegdatalists[$key]['weight']=$val['weight'];
                                $Stylegdatalists[$key]['shoucun']=$val['shoucun'];
                                $Stylegdatalists[$key]['tuo_type']='';
                                $Stylegdatalists[$key]['zhushi']='';
                                $Stylegdatalists[$key]['zhushilishu']=$val['zhushi_num'];
                                $Stylegdatalists[$key]['zhushixingzhuang']='';
                                $Stylegdatalists[$key]['zuanshidaxiao']=$val['zhushizhong'];
                                if($val['yanse']==1){
                                    $Stylegdatalists[$key]['zhushiyanse']='白';
                                }elseif($val['yanse']==2){
                                    $Stylegdatalists[$key]['zhushiyanse']='黄';
                                }elseif($val['yanse']==3){
                                    $Stylegdatalists[$key]['zhushiyanse']='玫瑰金';
                                }elseif($val['yanse']==4){
                                    $Stylegdatalists[$key]['zhushiyanse']='分色';                                    
                                }else{
                                    $Stylegdatalists[$key]['zhushiyanse']='';
                                    
                                }
                                $Stylegdatalists[$key]['zhushijingdu']='';
                                $Stylegdatalists[$key]['zhushiqiegong']='';
                                $Stylegdatalists[$key]['zhushiguige']='';
                                $Stylegdatalists[$key]['fushi']='';
                                $Stylegdatalists[$key]['fushilishu']=$val['fushi_num1'];
                                $Stylegdatalists[$key]['fushizhong']=$val['fushizhong1'];
                                $Stylegdatalists[$key]['shi2']='';
                                $Stylegdatalists[$key]['shi2lishu']=$val['fushi_num2'];
                                $Stylegdatalists[$key]['shi2zhong']=$val['fushizhong2'];
                                $Stylegdatalists[$key]['shi3']='';
                                $Stylegdatalists[$key]['shi3lishu']=$val['fushi_num3'];
                                $Stylegdatalists[$key]['shi3zhong']=$val['fushizhong3'];
                                $Stylegdatalists[$key]['zhengshuhao']='';
                                $Stylegdatalists[$key]['zhengshuleibie']='';
                                $Stylegdatalists[$key]['ziyin']='';
                                $Stylegdatalists[$key]['jiejia']="";
                                $Stylegdatalists[$key]['company']='';
                                $Stylegdatalists[$key]['allage']='';
                                $Stylegdatalists[$key]['thisage']='';
                                $Stylegdatalists[$key]['box_sn']='';
                                $Stylegdatalists[$key]['jietuoxiangkou']='';
                                
                                $datalists[]=$Stylegdatalists[$key];
                          }                        
                    }
                    //if($Stylegdatalists){
                        //$datalists=array_merge($datalists,$Stylegdatalists);
                    //}
                }

        }
        
            foreach($datalists as $k=>$v){
                fputcsv($handle,$v);
            }
        }
        header('Content-type: application/csv'); 
        //下载显示的名字 
        header('Content-Disposition: attachment; filename='.$downLoads.'.csv'); 
        readfile($path."/".$downLoads.".csv"); 
        exit(); 
	}  

	/**
	 *	searchother，列表
	 */
	public function searchother ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");
			'policy_id' => _Request::getInt('id')
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where = array(
				'policy_id'=>$args['policy_id']
			);
	
		$model = new AppSalepolicyGoodsModel(17);
		$data = $model->pageTogetherList($where,$page,5,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_salepolicy_goods_search_page';
		$this->render('app_salepolicy_goods_search_listother.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
 		$id=_Request::getInt('id');

		$result['content'] = $this->fetch('app_salepolicy_goods_info.html',array(
			'view'=>new AppSalepolicyGoodsView(new AppSalepolicyGoodsModel(17)),
			'policy_id'=>_Request::getInt('id'),
			'baseInfo'=>new BaseSalepolicyInfoView(new BaseSalepolicyInfoModel($id,17))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

    public function batadd ()
    {
        $result = array('success' => 0,'error' => '');
        $id=_Request::getInt('id');
        $result['content'] = $this->fetch('bat_app_salepolicy_goods_info.html',array(
            'view'=>new AppSalepolicyGoodsView(new AppSalepolicyGoodsModel(17)),
            'policy_id'=>_Request::getInt('id'),
            'baseInfo'=>new BaseSalepolicyInfoView(new BaseSalepolicyInfoModel($id,17))
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }

    public function batinsert(){
        $bigen=time();
        $result=array('success'=>0,'error'=>'');
        $upload_name = $_FILES['file_csv'];
        $tmp_name = $upload_name['tmp_name'];
        if (!$tmp_name) {
            $result['error'] = '文件不能为空';
            Util::jsonExit($result);
        }
        if (Upload::getExt($upload_name['name']) != 'csv') {
            $result['error'] = '请上传csv格式文件';
            Util::jsonExit($result);
        }
        $goods_ids = array();//批量去重数组
        $error=array();//错误信息
        $error['flag']=true;//错误标示
        $file = fopen($tmp_name, 'r');
        $i=0;
        $policy_id = _Request::getInt("policy_id");
        $baseSalepolicyInfoModel =  new BaseSalepolicyInfoModel($policy_id,18);
        $baseSalepolicyInfo = $baseSalepolicyInfoModel->getDataObject();
		$bsi_status = $baseSalepolicyInfo['bsi_status'];
        $is_kuanprice = $baseSalepolicyInfo['is_kuanprice'];
        $product_type = $baseSalepolicyInfo['product_type'];
        $cat_type = $baseSalepolicyInfo['cat_type'];
        $tuo_type = $baseSalepolicyInfo['tuo_type'];
        $zhushi_begin = $baseSalepolicyInfo['zhushi_begin'];
        $zhushi_end = $baseSalepolicyInfo['zhushi_end'];
        if(empty($zhushi_begin)){
            $zhushi_begin = 0;
        }
        if(empty($zhushi_end)){
            $zhushi_end = 99999;
        }
        $bsmodel=new BaseSalepolicyGoodsModel(17);
        $model =  new AppSalepolicyGoodsModel(18);

        $apiStyleModel = new ApiStyleModel();
		$catList = $apiStyleModel->getCatTypeInfo();
		$allproducttype = $baseSalepolicyInfoModel->getallproductype();
        $info = array();
        $j=0;

        $hidden_goods_list = array();

        while ($datav = fgetcsv($file)) {
            if($i==0){
                $i++;
                continue;
            }
            //货号的判断机制
            $goods_id = trim(iconv('gbk','utf-8',$datav[0]));
            if($goods_id==''){
                $error['flag']=false;
                $error[$i][]="货号不能为空";
            }else{
                $res =  $this->checkSGoods($goods_id);
                if(in_array($goods_id,$goods_ids)){
                    $error['flag']=false;
                    $error[$i][]="文件中出现重复 货号".$goods_id;
                }elseif(!$this->checkGoods($policy_id,$goods_id)){
                    $error['flag']=false;
                    $error[$i][] ='货品已经存在该销售政策中，不能重复添加 货号'.$goods_id;
                } elseif($res){
                    if($res==1){
                        $error['flag']=false;
                        $error[$i][] ='没有该货品，不能添加 货号'.$goods_id."请检查货号是否正确";
                    }else{
                        $error['flag']=false;
                        $error[$i][] ='货品状态错误，不能添加 货号'.$goods_id."请检查货号是否正确";
                    }
                }
                $dmodel = new AppSalepolicyGoodsModel(18);
                $dmodel->deleteGoods($goods_id);

                $goods_infos = $model->getWaregoodisonsale($goods_id);
                if($goods_infos)
                {
                    $goods_info = $goods_infos[0];
                }
                $goods_age_info = $model->getWaregoodisAgeonsale($goods_id);
                $goods_product_type = $goods_info['product_type1'];
                $goods_cat_type1 = $goods_info['cat_type1'];
                $goods_tuo_type = $goods_info['tuo_type'];
                $goods_zhushidaxiao = $goods_info['zuanshidaxiao'];

                if(isset($goods_age_info['is_kuanprice']) && $goods_age_info['is_kuanprice'] != $is_kuanprice){
                    if($is_kuanprice == 1){
                        $error['flag']=false;
                        $error[$i][]="按款定价销售政策只能添加按款定价的商品";
                    }else{
                        $error['flag']=false;
                        $error[$i][]="按货定价销售政策只能添加按货定价的商品";
                    }
                }
                
                if($tuo_type != 0){
                    if($tuo_type != $goods_tuo_type){
                        $error['flag']=false;
                        $error[$i][]="托类型不匹配，不能添加到销售政策里";
                    }
                }
                if($goods_zhushidaxiao >= $zhushi_begin && $goods_zhushidaxiao <= $zhushi_end){
                }else{
                    $error['flag']=false;
                    $error[$i][]="主石大小不匹配，不能添加到销售政策里";
                }
                //款式分类
                $supportCats = $this->supportCats($catList,$goods_cat_type1,$cat_type) ;
                if(!$supportCats)
                {
                    $error['flag']=false;
                    $error[$i][]="款式分类不匹配，不能添加到销售政策里";
                }

                //产品线
                $supportProductLine = $this->supportProductLine($allproducttype,$goods_product_type,$product_type);
                if(!$supportProductLine)
                {
                    $error['flag']=false;
                    $error[$i][]="产品线不匹配，不能添加到销售政策里";
                }

                $chengben = $bsmodel->getMingyiChenbenByid($goods_id);
                $sale_price = 0;
                if(!(bool)preg_match('/^[0-9\.]*$/i',$chengben)){
                    $error['flag']=false;
                    $error[$i][]="成本价只能是数字";
                }elseif(strlen($chengben)>8){
                    $error['flag']=false;
                    $error[$i][]="成本价长度超出系统限制请检查(总长度不应超过8)";
                }

                if($is_kuanprice == 1){
                    $sta_value=0;
                    $jiajia=0;
                    $sale_price = $goods_age_info['kuanprice'];
                }else{
                    $sta_value = trim(iconv('gbk','utf-8',$datav[1]));
                    $jiajia = trim(iconv('gbk','utf-8',$datav[2]));
                    if($sta_value===''){
                        $error['flag']=false;
                        $error[$i][]="固定值不能为空！";
                    }else{
                        if(!(bool)preg_match('/^[0-9\.-]*$/i',$sta_value)){
                            $error['flag']=false;
                            $error[$i][]="固定值只能是数字！";
                        }elseif(strlen($sta_value)>8){
                            $error['flag']=false;
                            $error[$i][]="固定值长度超出系统限制请检查(总长度不应超过8)";
                        }
                    }

                    if($jiajia===''){
                        $error['flag']=false;
                        $error[$i][]="加价率不能为空！";
                    }else{
                        if(!(bool)preg_match('/^[0-9\.]*$/i',$jiajia)){
                            $error['flag']=false;
                            $error[$i][]="固定值只能是数字！";
                        }elseif(strlen($jiajia)>8){
                            $error['flag']=false;
                            $error[$i][]="加价率长度超出系统限制请检查(总长度不应超过8)";
                        }
                    }

                    if($sta_value==0&&$jiajia==0){
                        $error['flag']=false;
                        $error[$i][]="加价率和固定值不能同时为0";
                    }else{
                        //0.根据政策id 获取自己的所有渠道id
                        $newmodel =  new AppSalepolicyChannelModel(17);//$policy_id
                        $xiangkou=$model->getxiankouBygoods_id($goods_id);
                        $new2model=new AppSalepolicyGoodsModel(18);    
                        if(!empty($xiangkou)){
                        $xiangkou1 = $xiangkou['jietuoxiangkou'];
                            if(!empty($xiangkou1) && $xiangkou1 > 0)
                            {
                                $getbxf_data = $xiangkou1;
                            }else{
                                $getbxf_data = $xiangkou['zuanshidaxiao'];
                            }
                            $baoxianfei = $new2model->GetBaoxianFei($getbxf_data);
                     
                        }
                        else {
                            $baoxianfei=0;
                        }
                          
                        //计算销售价格
                        $sale_price = round(($chengben+$baoxianfei) * $jiajia + $sta_value );
                        if($sale_price<0){
                            $error['flag']=false;
                            $error[$i][]="销售价不能小于0";
                        }
                        $price_a = (string) $sale_price;
                        if(strlen($price_a)>12){
                            $error['flag']=false;
                            $error[$i][]="货品成本价 X 加价率 + 固定值 超过了系统最大限度 请调整";
                        }                    
                    }
                }

                $info[$j]['sta_value'] =$sta_value ;
                $info[$j]['jiajia']  =$jiajia;
                $info[$j]['create_user']  =$_SESSION['userName'];
                $info[$j]['check_user']  =$_SESSION['userName'];
                $info[$j]['create_time']  =date("Y-m-d H:i:s");
                $info[$j]['check_time']  = $info[$j]['create_time'];
                $info[$j]['status']  =3;
                $info[$j]['policy_id']  =$policy_id;
                $info[$j]['goods_id']  =$goods_id;
                $info[$j]['chengben']  = $chengben;
                $info[$j]['sale_price'] = $sale_price;
                if(!preg_match("/^\d*$/", $goods_id)){
                    $info[$j]['isXianhuo'] = 0;
                }else{
                    $info[$j]['isXianhuo'] = 1;
                }
                $j++;
            }

            $i++;
        }
        if(!$error['flag']){
            //发生错误
            unset($error['flag']);
            $str = '';
            $ka=1;
            foreach($error as $k=>$v){
                $s = implode(',',$v);
                $ka=$k+1;
                $str.='第'.$ka.'行'.$s.'<br/>';
            }
            $result['error'] = $str;
            Util::jsonExit($result);
        }
        
        //保存到数据库
        $model = new AppSalepolicyGoodsModel(18);
        $res = $model->saveAllG($info);
 
        if(!$res){
            $result['error'] = '批量添加失败 编号001';
            Util::jsonExit($result);
        }
        $result['success'] =1;
        Util::jsonExit($result);

    }


    public function batedit ()
    {
        $result = array('success' => 0,'error' => '');
        $id=_Request::getInt('id');
        $baseSalepolicyInfoModel = new BaseSalepolicyInfoModel(18);
        $policyInfo = $baseSalepolicyInfoModel->getInfoByid($id);
        if($policyInfo['is_kuanprice']==1){
            $result['content'] = "按款定价的销售政策不支持编辑!";
            $result['title'] = '编辑';
            Util::jsonExit($result);
        }

        
        $result['content'] = $this->fetch('batedit_app_salepolicy_goods_info.html',array(
            'view'=>new AppSalepolicyGoodsView(new AppSalepolicyGoodsModel(17)),
            'policy_id'=>_Request::getInt('id'),
            'baseInfo'=>new BaseSalepolicyInfoView(new BaseSalepolicyInfoModel($id,17))
        ));
        $result['title'] = '添加';
        Util::jsonExit($result);
    }


    public function batupdate(){
        set_time_limit(0);
        ini_set('memory_limit','6000M');
        $bigen=time();
        $result=array('success'=>0,'error'=>'');
        $result['e']='';
        $result['err']='';
        $upload_name = $_FILES['batedit_app'];
        $tmp_name = $upload_name['tmp_name'];
        if (!$tmp_name) {
            $result['error'] = '文件不能为空';
            $result['e'] = 1;
            Util::jsonExit($result);
        }
        if (Upload::getExt($upload_name['name']) != 'csv') {
            $result['error'] = '请上传csv格式文件';
            $result['e'] = 1;
            Util::jsonExit($result);
        }
        $goods_ids = array();//批量去重数组
        $error=array();//错误信息
        $error['flag']=true;//错误标示
        $file = fopen($tmp_name, 'r');
        $i=0;
        $policy_id = _Request::getInt("policy_id");
        $bsmodel=new BaseSalepolicyGoodsModel(17);
        $asmodel=new AppSalepolicyGoodsModel(18);
        $baseSalepolicyInfoModel =  new BaseSalepolicyInfoModel($policy_id,18);
        $baseSalepolicyInfo = $baseSalepolicyInfoModel->getDataObject();
		$bsi_status = $baseSalepolicyInfo['bsi_status'];
        $is_kuanprice = $baseSalepolicyInfo['is_kuanprice'];
        if($is_kuanprice ==1 ){
            $result['error'] = '按款定价销售策略不支持修改价格';
            Util::jsonExit($result);
        }
        while ($datav = fgetcsv($file)) {
            if($i==0){
                $i++;
                continue;
            }
            //货号的判断机制
            $goods_id = trim(iconv('gbk','utf-8',$datav[0]));
            $chengbenjial = $bsmodel->getMingyiChenbenByid($goods_id);
            $guding = trim(iconv('gbk','utf-8',$datav[1]));
            $jiajial = trim(iconv('gbk','utf-8',$datav[2]));
            $oldRow = $asmodel->getInfoByGoodsIdAndPolicyIs_delete($goods_id,$policy_id);
            $ByGoodsId=$bsmodel->isHaveGoodsId($goods_id);
            if(!$ByGoodsId){
                $error['flag']=false;
                $error[$i][] = '货号"'.$goods_id.'"不存在可销售商品！';
                $result['err'][$i]['goods_id'] = $goods_id;
                $result['err'][$i]['sta_value'] = $guding;
                $result['err'][$i]['jiajia'] = $jiajial;                
            }
            if($goods_id==''){
                $error['flag']=false;
                $error[$i][]="货号不能为空";
                $result['err'][$i]['goods_id'] = $goods_id;
                $result['err'][$i]['sta_value'] = $guding;
                $result['err'][$i]['jiajia'] = $jiajial;
            }elseif($this->checkGoods($policy_id,$goods_id)){
                $policy_name=$asmodel->getPolicyNameByGoodsId($goods_id);
                $str='';
                 for ($p= 0;$p< count($policy_name); $p++){
                $str.= $policy_name[$p]['policy_name']." ";
                }
               
                if($policy_name){

                } 
                else{

                }
               
                if(in_array($goods_id,$goods_ids)){

                } 
                //elseif($res){
                    //if($res==1){
                        //$error['flag']=false;
                        //$error[$i][] ='没有该货品，不能添加 货号'.$goods_id."请检查货号是否正确";
                    //}else{
                        //$error['flag']=false;
                        //$error[$i][] ='货品状态错误，不能添加 货号'.$goods_id."请检查货号是否正确";
                    //}
                //}
                $goods_ids[]=$goods_id;
            }
            //固定值错误
            if(!(bool)preg_match('/^[0-9\.]*$/i',$guding)){
                $error['flag']=false;
                $error[$i][]="固定值只能是数字";
                $result['err'][$i]['goods_id'] = $goods_id;
                $result['err'][$i]['sta_value'] = $guding;
                $result['err'][$i]['jiajia'] = $jiajial;
            }elseif(strlen($guding)>8){
                $error['flag']=false;
                $error[$i][]="固定值长度超出系统限制请检查(总长度不应超过8)";
                $result['err'][$i]['goods_id'] = $goods_id;
                $result['err'][$i]['sta_value'] = $guding;
                $result['err'][$i]['jiajia'] = $jiajial;
            }
            //加价率
            if(!(bool)preg_match('/^[0-9\.]*$/i',$jiajial)){
                $error['flag']=false;
                $error[$i][]="加价率只能是数字";
                $result['err'][$i]['goods_id'] = $goods_id;
                $result['err'][$i]['sta_value'] = $guding;
                $result['err'][$i]['jiajia'] = $jiajial;
            }elseif(strlen($jiajial)>8){
                $error['flag']=false;
                $error[$i][]="加价率长度超出系统限制请检查(总长度不应超过8)";
                $result['err'][$i]['goods_id'] = $goods_id;
                $result['err'][$i]['sta_value'] = $guding;
                $result['err'][$i]['jiajia'] = $jiajial;
            }
            if(empty($guding) && empty($jiajial)){
                $error['flag']=false;
                $error[$i][]="固定值和加价率不能同时为空";
                $result['err'][$i]['goods_id'] = $goods_id;
                $result['err'][$i]['sta_value'] = $guding;
                $result['err'][$i]['jiajia'] = $jiajial;
            }
            //成本价
        
                if(!(bool)preg_match('/^[0-9\.]*$/i',$chengbenjial)){
                    $error['flag']=false;
                    $error[$i][]="成本价只能是数字";
                    $result['err'][$i]['goods_id'] = $goods_id;
                    $result['err'][$i]['sta_value'] = $guding;
                    $result['err'][$i]['jiajia'] = $jiajial;
                }elseif(strlen($chengbenjial)>8){
                    $error['flag']=false;
                    $error[$i][]="成本价长度超出系统限制请检查(总长度不应超过8)";
                    $result['err'][$i]['goods_id'] = $goods_id;
                    $result['err'][$i]['sta_value'] = $guding;
                    $result['err'][$i]['jiajia'] = $jiajial;
                }
          
            if($chengbenjial!=''&&$jiajial!=''&&$guding!=''){
                $price_a = (string) round( $chengbenjial * $jiajial + $guding);
                if(strlen($price_a)>12){
                    $error['flag']=false;
                    $error[$i][]="货品成本价 X 加价率 + 固定值 超过了系统最大限度 请调整";
                    $result['err'][$i]['goods_id'] = $goods_id;
                    $result['err'][$i]['sta_value'] = $guding;
                    $result['err'][$i]['jiajia'] = $jiajial;
                }

            }
            //成本价
            //if(iconv('gbk','utf-8',$datav[1])==''){
            //    $error['flag']=false;
            //    $error[$i][] ="成本价不能为空";
            //}
            $i++;
        }
        if(!$error['flag']){
            //发生错误 res
            unset($error['flag']);
            $str = '';
            $ka=1;
            foreach($error as $k=>$v){
                $s = implode(',',$v);
                $ka=$k+1;
                $str.='第'.$ka.'行'.$s.'<br/>';
            }
            $result['error'] = $str;
            Util::jsonExit($result);
        }
        error_reporting(E_ALL);
        rewind($file);
        $info = array();
        $j=0;
        
        //保存到数据库 
        $model = new AppSalepolicyGoodsModel(18);
        while ($data = fgetcsv($file)) {
            if($j==0){
                $j++;
                continue;
            }

            $goods_id=trim(iconv('gbk','utf-8',$data[0]));
            $chengben=$bsmodel->getMingyiChenbenByid($goods_id);
            $sta_value=trim(iconv('gbk','utf-8',$data[1]));
            $jiajia=trim(iconv('gbk','utf-8',$data[2]));


            $newmodel =  new AppSalepolicyGoodsModel(18);
            $oldRow = $newmodel->getInfoByGoodsIdAndPolicyIs_delete($goods_id,$policy_id);
            
            if(!$oldRow){
                $olddo = array();
                $info = array();
                //$newdo['goods_id'] = $goods_id;
                //$newdo['sta_value'] = $sta_value;
                //$newdo['jiajia'] = $jiajia;
                $info['sta_value'] =$sta_value ;
                $info['jiajia']  =$jiajia;
                $info['create_user']  =$_SESSION['userName'];
                $info['check_user']  =$_SESSION['userName'];
                $info['create_time']  =date("Y-m-d H:i:s");
                $info['check_time']  = $info['create_time'];
                $info['status']  =3;
                $info['policy_id']  =$policy_id;
                $info['goods_id']  =$goods_id;
                $info['chengben']  = $chengben;
                $info['sale_price'] = round($info['chengben']  * $info['jiajia'] + $info['sta_value']);
                if(!preg_match("/^\d*$/", $info['goods_id'])){
                    $info['isXianhuo'] = 0;
                }else{
                    $info['isXianhuo'] = 1;
                }
                $model->saveData($info, $olddo);
            }else{
            
                $sale_price = round($chengben * $jiajia + $sta_value);
                if($sta_value!=$oldRow['sta_value'] || $jiajia!=$oldRow['jiajia'] || $chengben!=$oldRow['chengben'] || $sale_price!=$oldRow['sale_price']){

                    /*$PolicyGoods=$model->getInfoByGoodsIdAndPolicyIdIs_delete($goods_id,$policy_id);
                    if($PolicyGoods){
                        $model->deleteInfoByGoodsIdAndPolicyId($goods_id,$policy_id);
                    }*/

                    $updatedata = array();
                    $updatedata['sta_value'] = $sta_value;
                    $updatedata['jiajia'] = $jiajia;
                    $updatedata['chengben'] = $chengben;
                    $updatedata['sale_price'] = $sale_price;
                    $newmodel->updateAppSalepolicyGoodsById($oldRow['id'],$updatedata);
                    $contentArr = array();
                    
                    if($sta_value!=$oldRow['sta_value']){
                        $contentArr[]="将固定值由{$oldRow['sta_value']}修改为{$sta_value} ";
                    }
                    if($jiajia!=$oldRow['jiajia']){
                        $contentArr[]="将加价率由{$oldRow['jiajia']}修改为{$jiajia} ";
                    }
                    if($chengben!=$oldRow['chengben']){
                        $contentArr[]="将成本由{$oldRow['chengben']}修改为{$chengben} ";
                    }
                    if($sale_price!=$oldRow['sale_price']){
                        $contentArr[]="将销售价由{$oldRow['sale_price']}修改为{$sale_price} ";
                    }
                    
                    $logmodel =  new AppSalepolicyChannelLogModel(18);
                    $bespokeActionLog=array();
                    $bespokeActionLog['policy_id']=$policy_id;
                    $bespokeActionLog['create_user']=$_SESSION['userName'];
                    $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
                    $bespokeActionLog['IP']=Util::getClicentIp();
                    $bespokeActionLog['status']=1;
                    $bespokeActionLog['remark']=implode(',',$contentArr);
                    $logmodel->saveData($bespokeActionLog,array());
                }
            }
        }
        $result['success'] =1;
        Util::jsonExit($result);
    }

    /*public function batupdate(){
        $bigen=time();
        $result=array('success'=>0,'error'=>'');
        $upload_name = $_FILES['batedit_app'];
        $tmp_name = $upload_name['tmp_name'];
        if (!$tmp_name) {
            $result['error'] = '文件不能为空';
            Util::jsonExit($result);
        }
        if (Upload::getExt($upload_name['name']) != 'csv') {
            $result['error'] = '请上传csv格式文件';
            Util::jsonExit($result);
        }
        $goods_ids = array();//批量去重数组
        $error=array();//错误信息
        $error['flag']=true;//错误标示
        $file = fopen($tmp_name, 'r');
        $i=0;
        $policy_id = _Request::getInt("policy_id");
           $bsmodel=new BaseSalepolicyGoodsModel(17);
        while ($datav = fgetcsv($file)) {
            if($i==0){
                $i++;
                continue;
            }
            //货号的判断机制
            $goods_id = trim(iconv('gbk','utf-8',$datav[0]));
            $chengbenjial = $bsmodel->getChenbenByid($goods_id);
            $guding = trim(iconv('gbk','utf-8',$datav[1]));
            $jiajial = trim(iconv('gbk','utf-8',$datav[2]));
            if($goods_id==''){
                $error['flag']=false;
                $error[$i][]="货号不能为空";
            }elseif($this->checkGoods($policy_id,$goods_id)){
                $asmodel=new AppSalepolicyGoodsModel(18);
                $policy_name=$asmodel->getPolicyNameByGoodsId($goods_id);
                $str='';
                 for ($p= 0;$p< count($policy_name); $p++){
                $str.= $policy_name[$p]['policy_name']." ";
                }
               
                if($policy_name){
                    $error['flag']=false;
                    $error[$i][] ='货品不存在本销售政策中,该货品存在'.$str."销售政策中";
                } 
                else{
                    $error['flag']=false;
                    $error[$i][] ='该货品不在销售政策中';
                }
               
                if(in_array($goods_id,$goods_ids)){
                    $error['flag']=false;
                    $error[$i][]="文件中出现重复 货号".$goods_id;
                } elseif($res){
                    if($res==1){
                        $error['flag']=false;
                        $error[$i][] ='没有该货品，不能添加 货号'.$goods_id."请检查货号是否正确";
                    }else{
                        $error['flag']=false;
                        $error[$i][] ='货品状态错误，不能添加 货号'.$goods_id."请检查货号是否正确";
                    }
                }
                $goods_ids[]=$goods_id;
            }
            //固定值错误
            if(!(bool)preg_match('/^[0-9\.]*$/i',$guding)){
                $error['flag']=false;
                $error[$i][]="固定值只能是数字";
            }elseif(strlen($guding)>8){
                $error['flag']=false;
                $error[$i][]="固定值长度超出系统限制请检查(总长度不应超过8)";
            }
            //加价率
            if(!(bool)preg_match('/^[0-9\.]*$/i',$jiajial)){
                $error['flag']=false;
                $error[$i][]="加价率只能是数字";
            }elseif(strlen($jiajial)>8){
                $error['flag']=false;
                $error[$i][]="加价率长度超出系统限制请检查(总长度不应超过8)";
            }
            if(empty($guding) && empty($jiajial)){
                $error['flag']=false;
                $error[$i][]="固定值和加价率不能同时为空";
            }
            //成本价
        
                if(!(bool)preg_match('/^[0-9\.]*$/i',$chengbenjial)){
                    $error['flag']=false;
                    $error[$i][]="成本价只能是数字";
                }elseif(strlen($chengbenjial)>8){
                    $error['flag']=false;
                    $error[$i][]="成本价长度超出系统限制请检查(总长度不应超过8)";
                }
          
            if($chengbenjial!=''&&$jiajial!=''&&$guding!=''){
                $price_a = (string) round( $chengbenjial * $jiajial + $guding);
                if(strlen($price_a)>12){
                    $error['flag']=false;
                    $error[$i][]="货品成本价 X 加价率 + 固定值 超过了系统最大限度 请调整";
                }

            }
            //成本价
            //if(iconv('gbk','utf-8',$datav[1])==''){
            //    $error['flag']=false;
            //    $error[$i][] ="成本价不能为空";
            //}
            $i++;
        }
        if(!$error['flag']){
            //发生错误
            unset($error['flag']);
            $str = '';
            $ka=1;
            foreach($error as $k=>$v){
                $s = implode(',',$v);
                $ka=$k+1;
                $str.='第'.$ka.'行'.$s.'<br/>';
            }
            $result['error'] = $str;
            Util::jsonExit($result);
        }
        error_reporting(E_ALL);
        rewind($file);
        $info = array();
        $j=0;
     
        //保存到数据库
        while ($data = fgetcsv($file)) {
            if($j==0){
                $j++;
                continue;
            }

            $goods_id=trim(iconv('gbk','utf-8',$data[0]));
            $chengben=$bsmodel->getChenbenByid($goods_id);
            $sta_value=trim(iconv('gbk','utf-8',$data[1]));
            $jiajia=trim(iconv('gbk','utf-8',$data[2]));
            
  
       
            $newmodel =  new AppSalepolicyGoodsModel($id,18);
            $oldRow = $newmodel->getInfoByGoodsIdAndPolicyId($goods_id,$policy_id);
                        $model=new AppSalepolicyGoodsModel(18);   
        $new2model=new AppSalepolicyGoodsModel(18);   
        $xiangkou=$model->getxiankouBygoods_id($goods_id); 
        if(!empty($xiangkou)){
                 
        $xiangkou1 = $xiangkou['jietuoxiangkou'];
			if(!empty($xiangkou1) && $xiangkou1 > 0)
			{
				$getbxf_data = $xiangkou;
			}else{
		
				$getbxf_data = $xiangkou['zuanshidaxiao'];
			}
			$baoxianfei = $new2model->GetBaoxianFei($getbxf_data);
     
         }
         else {
            $baoxianfei=0;
         }
    
            $sale_price = round(($chengben+$baoxianfei) * $jiajia + $sta_value);
            if($sta_value!=$oldRow['sta_value'] || $jiajia!=$oldRow['jiajia'] || $chengben!=$oldRow['chengben'] || $sale_price!=$oldRow['sale_price']){

                $updatedata = array();
                $updatedata['sta_value'] = $sta_value;
                $updatedata['jiajia'] = $jiajia;
                $updatedata['chengben'] = $chengben;
                $updatedata['sale_price'] = $sale_price;
                $newmodel->updateAppSalepolicyGoodsById($oldRow['id'],$updatedata);
              
                $contentArr = array();
                
                if($sta_value!=$oldRow['sta_value']){
                    $contentArr[]="将固定值由{$oldRow['sta_value']}修改为{$sta_value} ";
                }
                if($jiajia!=$oldRow['jiajia']){
                    $contentArr[]="将加价率由{$oldRow['jiajia']}修改为{$jiajia} ";
                }
                if($chengben!=$oldRow['chengben']){
                    $contentArr[]="将成本由{$oldRow['chengben']}修改为{$chengben} ";
                }
                if($sale_price!=$oldRow['sale_price']){
                    $contentArr[]="将销售价由{$oldRow['sale_price']}修改为{$sale_price} ";
                }
                
                $logmodel =  new AppSalepolicyChannelLogModel(18);
                $bespokeActionLog=array();
                $bespokeActionLog['policy_id']=$policy_id;
                $bespokeActionLog['create_user']=$_SESSION['userName'];
                $bespokeActionLog['create_time']=date("Y-m-d H:i:s");
                $bespokeActionLog['IP']=Util::getClicentIp();
                $bespokeActionLog['status']=1;
                $bespokeActionLog['remark']=implode(',',$contentArr);
                $logmodel->saveData($bespokeActionLog,array());
            }
        }
        $result['success'] =1;
        Util::jsonExit($result);
    }*/

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
 
        $model =  new AppSalepolicyGoodsModel(18);
        $row=$model->getInfoByid($id);
        $goods_id = $row['goods_id'];
        $policy_id = $row['policy_id'];

        $baseSalepolicyInfoModel = new BaseSalepolicyInfoModel(18);
        $policyInfo = $baseSalepolicyInfoModel->getInfoByid($policy_id);
        if($policyInfo['is_kuanprice']==1){
            $result['content'] = "按款定价的销售政策不支持编辑!";
            $result['title'] = '编辑';
            Util::jsonExit($result);
        }
      
        $new2model=new AppSalepolicyGoodsModel(18);    
        $xiangkou=$model->getxiankouBygoods_id($goods_id);
 
        if(!empty($xiangkou)){
                 
        $xiangkou1 = $xiangkou['jietuoxiangkou'];
			if(!empty($xiangkou1) && $xiangkou1 > 0)
			{
				$getbxf_data = $xiangkou1;
			}else{
		
				$getbxf_data = $xiangkou['zuanshidaxiao'];
			}
			$baoxianfei = $new2model->GetBaoxianFei($getbxf_data);
     
         }
         else {
            $baoxianfei=0;
         }
		//$tab_id = intval($params["tab_id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_salepolicy_goods_info.html',array(
			'view'=>new AppSalepolicyGoodsView(new AppSalepolicyGoodsModel($id,17)),
			'id'=>$id,
            'baoxianfee'=>$baoxianfei,
			'policy_id'=>_Request::getInt('policy_id')
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
		$this->render('app_salepolicy_goods_show.html',array(
			'view'=>new AppSalepolicyGoodsView(new AppSalepolicyGoodsModel($id,17)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$olddo = array();
		if(_Request::getString('goods_id')==''){
			$result['error'] = '货号不能为空！';
			Util::jsonExit($result);
		}

        $policy_id = _Request::getInt("policy_id");
        $baseSalepolicyInfoModel =  new BaseSalepolicyInfoModel($policy_id,18);
        $baseSalepolicyInfo = $baseSalepolicyInfoModel->getDataObject();
		$bsi_status = $baseSalepolicyInfo['bsi_status'];
        $is_kuanprice = $baseSalepolicyInfo['is_kuanprice'];
        $product_type = $baseSalepolicyInfo['product_type'];
        $cat_type = $baseSalepolicyInfo['cat_type'];
        $tuo_type = $baseSalepolicyInfo['tuo_type'];
        $zhushi_begin = $baseSalepolicyInfo['zhushi_begin'];
        $zhushi_end = $baseSalepolicyInfo['zhushi_end'];
        if(empty($zhushi_begin)){
            $zhushi_begin = 0;
        }
        if(empty($zhushi_end)){
            $zhushi_end = 99999;
        }

		$goods_id = _Request::getString('goods_id');
        $model =  new AppSalepolicyGoodsModel(18);
        $goods_infos = $model->getWaregoodisonsale($goods_id);
        if($goods_infos)
        {
            $goods_info = $goods_infos[0];
        }
        $goods_age_info = $model->getWaregoodisAgeonsale($goods_id);
        $goods_product_type = $goods_info['product_type1'];
        $goods_cat_type1 = $goods_info['cat_type1'];
        $goods_tuo_type = $goods_info['tuo_type'];
        $goods_zhushidaxiao = $goods_info['zuanshidaxiao'];

        if(isset($goods_age_info['is_kuanprice']) && $goods_age_info['is_kuanprice'] != $is_kuanprice){
            if($is_kuanprice == 1){
                $result['error'] = '按款定价销售政策只能添加按款定价的商品!';
                Util::jsonExit($result);
            }else{
                $result['error'] = '按货定价销售政策只能添加按货定价的商品!';
                Util::jsonExit($result);
            }
        }
        
        if($tuo_type != 0){
            if($tuo_type != $goods_tuo_type){
                $result['error'] = '托类型不匹配，不能添加到销售政策里!';
                Util::jsonExit($result);
            }
        }
        if($goods_zhushidaxiao >= $zhushi_begin && $goods_zhushidaxiao <= $zhushi_end){
        }else{
            $result['error'] = '主石大小不匹配，不能添加到销售政策里!';
            Util::jsonExit($result);
        }
		//款式分类
		$apiStyleModel = new ApiStyleModel();
		$catList = $apiStyleModel->getCatTypeInfo();
        $supportCats = $this->supportCats($catList,$goods_cat_type1,$cat_type) ;

		//产品线
		$allproducttype = $baseSalepolicyInfoModel->getallproductype();
        $supportProductLine = $this->supportProductLine($allproducttype,$goods_product_type,$product_type);
        
        if(!$supportCats)
        {
            $result['error'] = '款式分类不匹配，不能添加到销售政策里!';
            Util::jsonExit($result);
        }
        if(!$supportProductLine)
        {
            $result['error'] = '产品线不匹配，不能添加到销售政策里!';
            Util::jsonExit($result);
        }

		$model =  new AppSalepolicyGoodsModel(18);
		$where['goods_id']=$goods_id;
		$where['policy_id']=$policy_id;
		$goods_data = $model->getInfoByGoodsId($where);
		if(!empty($goods_data)){
			$result['error'] = '货品已经存在该销售政策中，不能重复添加!';
			Util::jsonExit($result);
		}
        
        $hidden_goods_list = array();
		$model =  new AppSalepolicyGoodsModel(18);
		$where['goods_id']=$goods_id;
		$where['policy_id']=$policy_id;
		$hidden_goods_data = $model->getDeleteInfoByGoodsId($where);
		if(!empty($hidden_goods_data)){
            $hidden_goods_list[] = $hidden_goods_data['goods_id'];
        }

        $sta_value = _Request::get('sta_value');
        $jiajia=_Request::get('jiajia');
        $chengben = _Request::getFloat('chengben');
        if($is_kuanprice == 1){
            if($sta_value!=0 || $jiajia!=0){
                $result['error'] = '按款定价的销售政策 加价率和固定值必须同时为0';
                Util::jsonExit($result);
            }
            $sta_value=0;
            $jiajia=0;
            $sale_price = $goods_age_info['kuanprice'];
        }else{
            if($sta_value===''){
                $result['error'] = '固定值不能为空！';
                Util::jsonExit($result);
            }else{
                if(!(bool)preg_match('/^[0-9\.-]*$/i',$sta_value)){
                    $result['error'] = '固定值只能是数字！';
                    Util::jsonExit($result);
                }elseif(strlen($sta_value)>8){
                    $result['error'] = "固定值长度超出系统限制请检查(总长度不应超过8)";
                    Util::jsonExit($result);
                }
            }

            if($jiajia===''){
                $result['error'] = '加价率不能为空！';
                Util::jsonExit($result);
            }else{
                if(!(bool)preg_match('/^[0-9\.]*$/i',$jiajia)){
                    $result['error'] = '加价率只能是正数字！';
                    Util::jsonExit($result);
                }elseif(strlen($jiajia)>8){
                    $result['error'] = "加价率长度超出系统限制请检查(总长度不应超过8)";
                    Util::jsonExit($result);
                }
            }

            if($sta_value==0&&$jiajia==0){
                $result['error'] = '加价率和固定值不能同时为0';
                Util::jsonExit($result);
            }

            //0.根据政策id 获取自己的所有渠道id
            $newmodel =  new AppSalepolicyChannelModel(17);//$policy_id
            $xiangkou=$model->getxiankouBygoods_id($goods_id);
            $new2model=new AppSalepolicyGoodsModel(18);    
            if(!empty($xiangkou)){
            $xiangkou1 = $xiangkou['jietuoxiangkou'];
                if(!empty($xiangkou1) && $xiangkou1 > 0)
                {
                    $getbxf_data = $xiangkou1;
                }else{
                    $getbxf_data = $xiangkou['zuanshidaxiao'];
                }
                $baoxianfei = $new2model->GetBaoxianFei($getbxf_data);
         
            }
            else {
                $baoxianfei=0;
            }
              
            //计算销售价格
            $sale_price = round(($chengben+$baoxianfei) * $jiajia + $sta_value );
            if($sale_price<0){
                $result['error'] = '销售价不能小于0!';
                Util::jsonExit($result);
            }
        }

        $_newModel = new BaseSalepolicyGoodsModel(17);
        if(!(bool)preg_match('/[^0-9]/i',$goods_id)){
            $is_have = $_newModel->isHaveGoodsId($goods_id);
            if(!$is_have){
                $result['error'] = '没有该货品，不能添加';
				Util::jsonExit($result);
            }
            if($is_have['is_sale'] != 1){
                $result['error'] = '货品状态错误，不能添加';
                Util::jsonExit($result);
            }
        }else{
            $is_have = $_newModel->isHaveGoodsSn($goods_id);
            if(empty($is_have)){
                $result['error'] = '没有该货品，不能添加';
                Util::jsonExit($result);
            }
            if($is_have != 1){
                $result['error'] = '货品状态错误，不能添加';
                Util::jsonExit($result);
            }
        }

        if(!preg_match("/^\d*$/",$goods_id)){
            $isXianhuo = 0;
        }else{
            $isXianhuo = 1;
        }
        if(empty($hidden_goods_list)){
            $id = $hidden_goods_list[0];
    		$newmodel =  new AppSalepolicyGoodsModel($id,18);
		    $olddo = $newmodel->getDataObject();
        }
        $newdo=array(
            'policy_id'=>$policy_id,
            'goods_id'=>$goods_id,
            'isXianhuo'=>$isXianhuo,
            'sta_value'=>$sta_value,
            'chengben'=>$chengben,
            'jiajia'=>$jiajia,
            'sale_price'=>$sale_price,
            'create_time'=>date("Y-m-d H:i:s"),
            'create_user'=>$_SESSION['userName'],
		);

		$newmodel =  new AppSalepolicyGoodsModel(18);
		$res = $newmodel->saveData($newdo,$olddo);
        $_model = new BaseSalepolicyGoodsModel(18);
        $_model->updateGoodsIsPolicy(array($goods_id));
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

	/**
	 *	update，更新信息 sale_price
	 */
	public function update ($params)
	{
	    $goods_id = _Request::getString('goods_id');
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');
       
		if(_Request::getString('goods_id')==''){
			$result['error'] = '货号不能为空！';
			Util::jsonExit($result);
		}

		if(_Request::getFloat('chengben')===''){
			$result['error'] = '成本价不能为空！';
			Util::jsonExit($result);
		}


		$newmodel =  new AppSalepolicyGoodsModel($id,18);
		$olddo = $newmodel->getDataObject();

        $policy_id = $olddo["policy_id"];
        $baseSalepolicyInfoModel =  new BaseSalepolicyInfoModel($policy_id,18);
        $baseSalepolicyInfo = $baseSalepolicyInfoModel->getDataObject();
		$bsi_status = $baseSalepolicyInfo['bsi_status'];
        $is_kuanprice = $baseSalepolicyInfo['is_kuanprice'];
        if($is_kuanprice ==1 ){
            $result['error'] = '按款定价销售策略不支持修改价格';
            Util::jsonExit($result);
        }
        $product_type = $baseSalepolicyInfo['product_type'];
        $cat_type = $baseSalepolicyInfo['cat_type'];
        $tuo_type = $baseSalepolicyInfo['tuo_type'];
        $zhushi_begin = $baseSalepolicyInfo['zhushi_begin'];
        $zhushi_end = $baseSalepolicyInfo['zhushi_end'];
        if(empty($zhushi_begin)){
            $zhushi_begin = 0;
        }
        if(empty($zhushi_end)){
            $zhushi_end = 99999;
        }
        

        $sta_value = _Request::getFloat('sta_value');
		$chengben = _Request::getFloat('chengben');
		$jiajia = _Request::getFloat('jiajia');

        if($sta_value===''){
            $result['error'] = '固定值不能为空！';
            Util::jsonExit($result);
        }else{
            if(!(bool)preg_match('/^[0-9\.-]*$/i',$sta_value)){
                $result['error'] = '固定值只能是数字！';
                Util::jsonExit($result);
            }elseif(strlen($sta_value)>8){
                $result['error'] = "固定值长度超出系统限制请检查(总长度不应超过8)";
                Util::jsonExit($result);
            }
        }


        if($jiajia===''){
            $result['error'] = '加价率不能为空！';
            Util::jsonExit($result);
        }else{
            if(!(bool)preg_match('/^[0-9\.]*$/i',$jiajia)){
                $result['error'] = '加价率只能是正数字！';
                Util::jsonExit($result);
            }elseif(strlen($jiajia)>8){
                $result['error'] = "加价率长度超出系统限制请检查(总长度不应超过8)";
                Util::jsonExit($result);
            }
        }

        if($sta_value==0&&$jiajia==0){
            $result['error'] = '加价率和固定值不能同时为0';
            Util::jsonExit($result);
        }
        $model =  new AppSalepolicyGoodsModel(18);
        $new2model=new AppSalepolicyGoodsModel(18);    
        $xiangkou=$model->getxiankouBygoods_id($goods_id);
 
        if(!empty($xiangkou)){
                 
        $xiangkou1 = $xiangkou['jietuoxiangkou'];
			if(!empty($xiangkou1) && $xiangkou1 > 0)
			{
				$getbxf_data = $xiangkou1;
			}else{
		
				$getbxf_data = $xiangkou['zuanshidaxiao'];
			}
			$baoxianfei = $new2model->GetBaoxianFei($getbxf_data);
     
         }
         else {
            $baoxianfei=0;
         }
             
    
        //计算销售价格
		$sale_price = round(($chengben+$baoxianfei) * $jiajia + $sta_value );
        if($sale_price<0){
            $result['error'] = '销售价不能小于0!';
            Util::jsonExit($result);
        }
        
        if(!preg_match("/^\d*$/",$goods_id)){
            $isXianhuo = 0;
        }else{
            $isXianhuo = 1;
        }
        
		$newdo=array(
				'id'=>$id,
				'goods_id'=>_Request::getString('goods_id'),
				//'isXianhuo'=>_Request::getInt('isXianhuo'),
		        'isXianhuo'=>$isXianhuo,
				'sta_value'=>$sta_value,
				'chengben'=>$chengben,
				'jiajia'=>$jiajia,
				'sale_price'=>$sale_price,
				'update_time'=>date("Y-m-d H:i:s" ),
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '修改此处为想显示在页签上的字段';
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppSalepolicyGoodsModel($id,18);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
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

	/**
	 *	showsed，渲染查看页面
	 */
	public function showsed ($params)
	{
		$id = intval($params["id"]);
		$result['content'] = $this->fetch('app_salepolicy_goods_show_show.html',array(
			'view'=>new AppSalepolicyGoodsView(new AppSalepolicyGoodsModel($id,17)),
			'dd'=>new DictView(new DictModel(1))
		));
		$result['title'] = '商品详情';
		Util::jsonExit($result);

	}

	/**
	 *	deletesed，删除
	 */
	public function deletesed ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');

		$newmodel =  new AppSalepolicyGoodsModel($id,17);

		$olddo = $newmodel->getDataObject();	

		$newdo=array(
			'id'	=> $id,		
			'is_delete'	=> 2	
		);

		$res = $newmodel->saveData($newdo,$olddo);
        //先查一遍这个货有几个销售政策如果只有这一个就把货品状态变为否
        $rea = $newmodel->GoodsRev($olddo['goods_id']);

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

	/**
	 *	bsi_statust，申请
	 */
	public function bsi_statust ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');

		$newmodel =  new AppSalepolicyGoodsModel($id,17);

		$olddo = $newmodel->getDataObject();
		if($olddo['status']!=1){
			$result['error'] = '状态错误，不能申请！';
			Util::jsonExit($result);		
		}
		if($olddo['is_delete']==2){
			$result['error'] = '该状态已删除，不能申请！';
			Util::jsonExit($result);			
		}
		$newdo=array(
			'id'	=> $id,		
			'status'	=> 2,
			'check_time'	=> date("Y-m-d H:i:s"),	
			'check_user'	=> $_SESSION['userName']			
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

	/**
	 *	bsi_statusts，通过 
	 */
	public function bsi_statusts ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');

		$newmodel =  new AppSalepolicyGoodsModel($id,17);

		$olddo = $newmodel->getDataObject();
		if($olddo['status']!=2){
			$result['error'] = '状态错误，不能通过！';
			Util::jsonExit($result);		
		}
		if($olddo['is_delete']==2){
			$result['error'] = '该状态已删除，不能通过！';
			Util::jsonExit($result);			
		}
		$newdo=array(
			'id'	=> $id,		
			'status'	=> 3,
			'check_time'	=> date("Y-m-d H:i:s"),	
			'check_user'	=> $_SESSION['userName']			
		);

		if(!(bool)preg_match('/[^0-9]/i',$olddo['goods_id'])){
			$goods_id_arr = $newmodel->getWarehouseBygoods_id($olddo['goods_id']);
			if($goods_id_arr['error']>0){
				$result['error'] = $goods_id_arr['error_msg'];
				Util::jsonExit($result);
			}
			if($goods_id_arr['return_msg']['data']['is_on_sale']!=2){
				$result['error'] = '货品状态错误，不能审核通过';
				Util::jsonExit($result);		
			}
		}else{
			$goods_sn_arr = $newmodel->getStyleBygoods_sn($olddo['goods_id']);
			if($goods_sn_arr['error']>0){
				$result['error'] = $goods_sn_arr['error_msg'];
				Util::jsonExit($result);
			}
			if(empty($goods_sn_arr['return_msg'])){
				$result['error'] = $goods_sn_arr['error_msg'];
				Util::jsonExit($result);
			}			
			if($goods_sn_arr['return_msg']['is_sales']!=1){
				$result['error'] = '该款不能销售,不能审核通过';
				Util::jsonExit($result);		
			}						
		}

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

	/**
	 *	not_bsi_statusts，未通过
	 */
	public function not_bsi_statusts ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');

		$newmodel =  new AppSalepolicyGoodsModel($id,17);
		$olddo = $newmodel->getDataObject();
		if($olddo['status']!=2){
			$result['error'] = '状态错误，不能驳回！';
			Util::jsonExit($result);		
		}		
		if($olddo['is_delete']==2){
			$result['error'] = '该状态已删除，不能驳回！';
			Util::jsonExit($result);			
		}
		$newdo=array(
			'id'	=> $id,		
			'status'	=> 4,
			'check_time'	=> date("Y-m-d H:i:s"),	
			'check_user'	=> $_SESSION['userName']	
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

	/**
	 *	bsi_statusted，取消
	 */
	public function bsi_statusted ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');

		$newmodel =  new AppSalepolicyGoodsModel($id,17);

		$olddo = $newmodel->getDataObject();
		if($olddo['status']!=1){
			$result['error'] = '状态错误，不能取消！';
			Util::jsonExit($result);		
		}	
		if($olddo['is_delete']==2){
			$result['error'] = '该状态已删除，不能取消！';
			Util::jsonExit($result);			
		}		
		$newdo=array(
			'id'	=> $id,		
			'status'	=> 5,
			'check_time'	=> date("Y-m-d H:i:s"),	
			'check_user'	=> $_SESSION['userName']			
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


    public function getChengBenJia(){
        $result = array('success' => 0,'error' =>'');
        $goods_id=_Request::getString('goods_id');
        $model=new AppSalepolicyGoodsModel(18);   
        $new2model=new AppSalepolicyGoodsModel(18);    
        $xiangkou=$model->getxiankouBygoods_id($goods_id);
 

        if(!empty($xiangkou)){
                 
        $xiangkou1 = $xiangkou['jietuoxiangkou'];
			if(!empty($xiangkou1) && $xiangkou1 > 0)
			{
				$getbxf_data = $xiangkou1;
			}else{
		
				$getbxf_data = $xiangkou['zuanshidaxiao'];
			}
			$baoxianfei = $new2model->GetBaoxianFei($getbxf_data);
     
         }
         else {
            $baoxianfei=0;
         }
        $bmodel = new BaseSalepolicyGoodsModel(17);
        $chengben=$bmodel->getMingyiChenbenByid($goods_id);
        $res = $bmodel->isHaveGoodsId($goods_id);
        if(empty($res)){
            $result['error'] = 1;
			$result['msg'] = '没有该货品，不能添加';
            Util::jsonExit($result);
        }
	//取仓库的货品状态
	$status = $this->getstatus($goods_id);
	if($status == 0)
	{
		$result['error'] = 1;
		$result['msg'] = '没有该货品，不能添加';
    		Util::jsonExit($result);	
	}elseif($status=='error')
	{
		$result['error'] = 1;
		$result['msg'] = '货品状态不满足添加要求';
		Util::jsonExit($result);
	}

        $p = $bmodel->getKuanPrice($goods_id);
        if($p){
            $shijia = $p;
        }else{
            $shijia = 0;
        }

        $result['success']=1;
        $result['msg'] =$chengben;
        $result['msg1'] = $baoxianfei;
        $result['msg_shijia'] = $shijia;
        Util::jsonExit($result);


    }

    public function checkChannel($policy_id,$goods_id){
        $newmodel =  new AppSalepolicyChannelModel(17);//$policy_id
        $channel = $newmodel->getSalepolicyChannelByPolicyId($policy_id);
        foreach($channel as $val){
            //获取不同渠道具有相同政策的id
            $policy_id_arr = $newmodel->getSalepolicyChannelByChannel($val['channel']);
            //遍历政策ID 查出该政策下的商品是否 有当前添加商品ID
            //var_dump($policy_id_arr);exit;
            foreach($policy_id_arr as $v){
                //var_dump($v);exit;
                if($v['policy_id']!=$policy_id){
                    $model = new AppSalepolicyGoodsModel(17);
                    $goods_id_arr = $model->getGoodsById($v['policy_id']);
                    foreach($goods_id_arr as $vv){
                        if($vv['goods_id']==$goods_id){
                           return false;
                        }
                    }
                }

            }
        }
        return true;

    }

    public function downcsv(){
        $title = array(
            '货号',
            '固定值',
            '加价率',
        );

        Util::downloadCsv("批量导入销售政策商品",$title,'');
    }

    public function dow(){
        $result = array('success' => 0,'error' =>'');
        $dow=_Request::getString('d');

        $dw=array();
        if($dow){
            $dow=array_filter(explode("||",$dow));
            if($dow){
                foreach($dow as $k=>$v){
                    $str=explode("!",$v);
                        $dw[$k]['goods_id']=$str[0];
                        $dw[$k]['sta_value']=$str[1];
                        $dw[$k]['jiajia']=$str[2];
                }
            }
        }
        $title = array(
            '货号',
            '固定值',
            '加价率',
        );

        Util::downloadCsv("批量导入销售政策商品错误信息",$title,$dw);
    }

    public function checkGoods($policy_id,$goods_id){
        $model =  new AppSalepolicyGoodsModel(18);
        //var_dump($goods_id);exit;
        $where['goods_id']=$goods_id;
        $where['policy_id']=$policy_id;
        $goods_data = $model->getInfoByGoodsId($where);
        if(!empty($goods_data)){
           return false;
        }else{
            return true;
        }

    }

    public function checkSgoods($goods_id){
        $_newModel = new BaseSalepolicyGoodsModel(17);
        if(!(bool)preg_match('/[^0-9]/i',$goods_id)){
            $is_have = $_newModel->isHaveGoodsId($goods_id);
            if(!$is_have){
               return 1;
            }
	    $status = $this->getstatus($goods_id);
	    if($status == 0 )
	    {
		return 1;
	    }elseif($status == 'error')
	    {
		return 2;	
	    }
	    /*update bu liulinyan
            if($is_have['is_sale'] != 1){
               return 2;
            }*/
        }else{
            $is_have = $_newModel->isHaveGoodsSn($goods_id);
            if(empty($is_have)){
               return 1;
            }
	    /*update bu liulinyan
            if($is_have != 1){
                return 2;
            }*/
	    $status = $this->getstatus($goods_id);
	    if($status == 0 )
	    {
		return 1;
	    }elseif($status == 'error')
	    {
		return 2;	
	    }
        }
        return 0;
    }

    public function getchengbenprice($goods_id){
        $bmodel = new BaseSalepolicyGoodsModel(17);
        return $bmodel->isHaveGoodsId($goods_id);
    }
    
	//取仓库的货品状态
	public function getstatus($goods_id)
	{
		//取仓库的货品状态
		$wapimodel = new ApiWarehouseModel();
		$is_on_sales = array_column($wapimodel->getWaregoodisonsale($goods_id),'is_on_sale','goods_id');
		if(empty($is_on_sales))
		{
			/*
			$result['error'] = 1;
			$result['msg'] = '没有该货品，不能添加';
			Util::jsonExit($result);*/
			return 0;
		}
		//$status = array('1'=>'收货中','2'=>'库存','4'=>'盘点中','5'=>'调拨中');
		$status = array(1,2,4,5);
		foreach($is_on_sales as $v)
		{
			if(!in_array($v,$status))
			{
				/*
				$result['error'] = 1;
				$result['msg'] = '货品状态不满足添加要求';
				Util::jsonExit($result);
				*/
				return 'error';
				break;
			}
		}
		return 1;
	}

	/*
	 * 批量删除多个商品
	 */    
	public function delMany ($params)
	{
		$result = array('success' => 0,'error' => '');
		$goods_id = _Request::getList('_ids');
		$model =  new AppSalepolicyGoodsModel(17);
		$res = $model->delManyDelete($goods_id);		//不进行物理删除
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
    /******
    fun:print;打印条码
    ******/
    public function printcode($params)
    {
        
        $id = intval($params['id']);
        $model = new AppSalepolicyGoodsModel(17);
        $result = $model->getGoodsById($id);
        $arr = array(); //货品id 所有 字符串形式
        $new_arr = array();
        header("Content-Disposition: attachment;filename=huopin.csv");
        $str = "号,款号,商品名称,销售价,加价率,款式分类,产品线,货品状态,所在公司,所在仓库,入库方式,材质,金重,金耗,颜色,净度,手寸,证书号,钻石大小,戒托镶口,成本价,名义成本\n";
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
                        
                          if($val['put_in_type']==1 || $val['put_in_type']==2)
                        {
                            $put_in_type='GM';
                        }
                        if($val['put_in_type']==3 || $val['put_in_type']==4)
                        {
                            $put_in_type='DX';
                        }
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

    function supportCats($catList,$goods_cat_type1,$cat_type)
    {
        if($cat_type == '全部')
        {
            return true;
        }
        return $cat_type == $goods_cat_type1;
    }

    function supportProductLine($allproducttype,$goods_product_type,$product_type)
    {
        if($product_type == '全部')
        {
            return true;
        }
        return $goods_product_type == $product_type;
    }

}

