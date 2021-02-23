<?php
/**
 *  -------------------------------------------------
 *   @file		: DiamondListController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 21:06:55
 *   @update	:
 *  -------------------------------------------------
 */
class DiamondListController extends Controller
{
	protected $smartyDebugEnabled = true;
    protected $code = array();
    protected $warehouse_arrs = array();
    protected $whitelist = array('showImg');


    public function __construct() {
        parent::__construct();

        $model = new ApiWarehouseModel();
        $company_arr = $model->get_company_all();
        $arr = array();
        if($company_arr['error']==0){
            $arr = $company_arr['data'];
        }
        $this->assign("company",$arr);
    }
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{   
	     $args = array(
	        'carat'=>  _Request::getString('carat'),
	        'carat_min'=>_Request::getFloat('carat_min'),
	        'carat_max'=>_Request::getFloat('carat_max'),
	        'polish'  =>_Request::getList('polish'),
	        'symmetry'=>_Request::getList('symmetry'),
	        'clarity'=> _Request::getList('clarity'),
	        'color'=> _Request::getList('color'),
	        'shape'=> _Request::getList('shape'),
	        'cut'=> _Request::getList('cut'),
	        'cert'=> _Request::getList('cert')
	     );	
	         
         if(is_array($args['cut']) && in_array("3EX",$args['cut'])){
             $args['cut'][]='EX';
             $args['polish'][]='EX';
             $args['symmetry'][]='EX';
         }     
         $this->assign('args',$args);
	        
	   
        $is_logo = 0;
        $param = array('s_start1'=>'','s_end1'=>'','e_start2'=>'','e_end2'=>'');
        if(isset($_REQUEST['start']) && isset($_REQUEST['end']) && _Request::getString('start') && _Request::getString('end')){
            $is_logo = 1;
            $arr_start = explode('-', _Request::getString('start'));
            $arr_end = explode('-', _Request::getString('end'));
            $param = array('s_start1'=>  $arr_start[0],'s_end1'=>  $arr_start[1],'e_start2'=>$arr_end[0],'e_end2'=>$arr_end[1]);
        }
		//Util::M('diamond_list','app_order',27);	//生成模型后请注释该行
		//Util::V('diamond_list',27);	//生成视图后请注释该行
		$this->render('diamond_list_form.html',array(
				'bar'=>Auth::getBar(),
				'dd'=>new DictView(new DictModel(1)),
                'view'=>new DiamondListView(new DiamondListModel(19)),
                'is_logo'=>$is_logo,
                'param'=>$param
			));
	}
	/**
	 *	search，列表
	 */
	public function search ($params)
	{
	    $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
	    $pageSize = isset($_REQUEST["pageSize"]) ? intval($_REQUEST["pageSize"]) : 15 ;
	    $companyList = _Request::getList('company');
	    $headCompanyId = 58;
	    
	    $flag = 0;
	    if(in_array($headCompanyId, $companyList)){
	        $flag = 1;
	    }
	    
	    $companyId = $_SESSION['companyId'];
	    $companyModel = new CompanyModel(1);
	    $comInfo = $companyModel->select2("*","id={$companyId}",2);
	    
	    $_warehouse = array();

	    //以下注释为旧的裸钻搜索规则
	    /*
	    $_hrd_s_warehouse=array();
	    if (empty($companyList)) {
	        //company_type<>1 ：个体店和经销商 只可以看 （自己门店 +总公司+直营店星耀钻）
	        if($comInfo['company_type']<>1){
	            //直营店店的可以看（所有直营店 +总公司）
	            $_companyList = $companyModel->select2("id","company_type=1",1);
	            $_companyList = array_column($_companyList,'id');
	            $_companyList[] = 58;
	            $_companyList[] =$companyId;	        	
	            //$_companyList = array($companyId,58);
	            $flag = 1;
	            $_hrd_s_warehouse=$companyModel->getWarehouse(1,1,58);
	            $_hrd_s_warehouse=array_column($_hrd_s_warehouse,'code');
	        }else{
	            //直营店店的可以看（所有直营店 +总公司）
	            $_companyList = $companyModel->select2("id","company_type=1",1);
	            $_companyList = array_column($_companyList,'id');
	            $_companyList[] = 58;
	            $flag = 1;
	        }
	        if(!empty($companyList)){
	            $companyList = array_intersect($companyList, $_companyList);
	        }else{
	            $companyList = $_companyList;
	        }
	    } else {
	    	if($comInfo['company_type'] == 1) {
	    		// 只能所选公司里的直营公司和总公司
	    		$_companyList = array_column($companyModel->select2("id","company_type=1",1), 'id');
	    	} else{
	    		// 只能看自己公司和总公司
	    		$_companyList = array_column($companyModel->select2("id","company_type=1",1), 'id');
	    		$_companyList[] = $companyId;
                $_hrd_s_warehouse=$companyModel->getWarehouse(1,1,58);
                $_hrd_s_warehouse=array_column($_hrd_s_warehouse,'code');
	    	}
	    	
	    	$filter_company = array();
	    	foreach ($companyList as $cid) {
	    		if ($cid == '58') {
	    			$filter_company[] = $cid;
	    		} else if (in_array($cid, $_companyList)) {
	    			$filter_company[] = $cid;
	    		}
	    	}
	    	$companyList = $filter_company;
	    }

	    if($companyList){
	        $wareshou_model = new ApiWarehouseModel();
	        $warehouse = $wareshou_model->get_warehouse_all(1,  implode(',', $companyList));
	        if($warehouse['error']==0){
	            $_warehouse = array_column($warehouse['data'],'code');
	        }
	        if($flag){
	            array_push($_warehouse, 'COM');
	        }
	    }	
	    */

        $not_from_ad = '';
	    if(SYS_SCOPE=='boss'){
                //直营店店的可以看（所有直营店 +总公司）的(期货+现货)
		        $_companyList = $companyModel->select2("id","company_type=1",1);
		        $_companyList = array_column($_companyList,'id');
		        $_companyList[] = 58; 	    	
	    	    if(!empty($companyList)){	    	    	
			    	$filter_company = array();
			    	foreach ($companyList as $cid) {
			    		if ($cid == '58') {
			    			$filter_company[] = $cid;
			    		} else if (in_array($cid, $_companyList)) {
			    			$filter_company[] = $cid;
			    		}
			    	}
			    	$companyList = $filter_company;  
			    }else{
                    $companyList = $_companyList;
                    $flag = 1;
                }

			    $where = array('diamond_warehouse' =>1 , 'company_id' =>implode(',', $companyList));
			    $warehouse = $companyModel->getWarehouse_Where($where);			        
			    if(!empty($warehouse)){
			        $_warehouse = array_column($warehouse,'code');
			    }
		        if($flag){
		            array_push($_warehouse, 'COM');
		        }
	    }

        if(SYS_SCOPE=='zhanting')	    {
                //经销商可以看  所有直营店期货(kgk+enjoy除外) + 自己门店现货 +浩鹏公司现货
	            $_companyList[] = 58; 
	            $_companyList[] = $companyId;
			    $where = array('diamond_warehouse' =>1 , 'company_id' =>implode(',', $_companyList));
			    $warehouse = $companyModel->getWarehouse_Where($where);			        
			    if(!empty($warehouse)){
			        $_warehouse = array_column($warehouse,'code');
			    }
			    array_push($_warehouse, 'COM');  //直营店期货
			    //$not_from_ad = array('11');	//直营店期货(kgk+enjoy除外)		       
			   	
	    }


	    /*begin--------排除掉购物车中的商品----------begin*/
	    $no_goods_id = array();
	    $cartModel = new AppOrderCartModel(27);
	    $cart_goods = $cartModel->get_cart_goods();
	    foreach($cart_goods as $good){
	        $no_goods_id[] = $good['goods_id'];
	        	
	    }

        $not_from_ad = array();  
	    if(SYS_SCOPE=='zhanting'){
            $not_from_ad = array('11'); 
	    }
	    /*end----------排除掉购物车中的商品----------end*/
	    $args = array(
	        'mod'	=> _Request::get("mod"),
	        'con'	=> substr(__CLASS__, 0, -10),
	        'act'	=> __FUNCTION__,
	        'page'=>  $page,
	        'pageSize'=>  $pageSize,
	        'goods_sn'=>  _Request::getString('goods_sn'),
	        'carat_min'=>  _Request::getFloat('carat_min'),
	        'carat_max'=>  _Request::getFloat('carat_max'),
	        'price_min'=>  _Request::getFloat('price_min'),
	        'price_max'=>  _Request::getFloat('price_max'),
	        'clarity[]'=> _Request::getList('clarity'),
	        'color[]'=> _Request::getList('color'),
	        'shape[]'=> _Request::getList('shape'),
	        'cut[]'=> _Request::getList('cut'),
	        'polish[]'=> _Request::getList('polish'),
	        'symmetry[]'=> _Request::getList('symmetry'),
	        'fluorescence[]'=> _Request::getList('fluorescence'),
	        'cert[]'=> _Request::getList('cert'),
	        'cert_id'=> _Request::getInt('cert_id'),
	        'is_active'=> _Request::getInt('is_active'),
	        'status'=> _Request::getInt('status'),
	        'goods_name'=> _Request::getString('goods_name'),
	        'from_ad'=> _Request::getInt('from_ad'),
	        'not_from_ad'=> $not_from_ad,
	        'gm'=> _Request::getInt('gm'),
	        'zdj'=> _Request::getString('zdj'),
	        'stonesort'=> _Request::getString('stonesort'),
	        'yansesort'=> _Request::getString('yansesort'),
	        'jdsort'=> _Request::getString('jdsort'),
	        'good_type'=> _Request::getInt('good_type'),
	        's_carats_tsyd1'=> _Request::getFloat('s_carats_tsyd1'),
	        'e_carats_tsyd1'=> _Request::getFloat('e_carats_tsyd1'),
	        's_carats_tsyd2'=> _Request::getFloat('s_carats_tsyd2'),
	        'e_carats_tsyd2'=> _Request::getFloat('e_carats_tsyd2'),
	        'gemx_zhengshu'=> _Request::getString('gemx_zhengshu'),
	        'ysyd'=> _Request::getInt('ysyd'),	        
	        'company[]'=> $companyList,
	        'ssy_active'=> _Request::getInt('ssy_active',0),//双十一
	    	'pf_price_min' => _Request::getFloat('pf_price_min'),
	    	'pf_price_max' => _Request::getFloat('pf_price_max'),
	    	'include_img'  => _Request::getString('include_img'),
	    	'not_from_ad'   => $not_from_ad,
	    );
	    $where = array(
	        'page'=>  $page,
	        'pageSize'=>  $pageSize,
	        'goods_sn'=>  _Request::getString('goods_sn'),
	        'carat_min'=>  _Request::getFloat('carat_min'),
	        'carat_max'=>  _Request::getFloat('carat_max'),
	        'price_min'=>  _Request::getFloat('price_min'),
	        'price_max'=>  _Request::getFloat('price_max'),
	        'clarity'=> _Request::getList('clarity'),
	        'color'=> _Request::getList('color'),
	        'shape'=> _Request::getList('shape'),
	        'cut'=> _Request::getList('cut'),
	        'polish'=> _Request::getList('polish'),
	        'symmetry'=> _Request::getList('symmetry'),
	        'fluorescence'=> _Request::getList('fluorescence'),
	        'cert'=> _Request::getList('cert'),
	        'cert_id'=> _Request::getString('cert_id'),
	        'is_active'=> _Request::getInt('is_active'),
	        'status'=> 1,//_Request::getInt('status'),
	        'goods_name'=> _Request::getString('goods_name'),
	        'from_ad'=> _Request::getInt('from_ad'),
	        'not_from_ad'=> $not_from_ad,
	        'gm'=> _Request::getInt('gm'),
	        'zdj'=> _Request::getString('zdj'),
	        'stonesort'=> _Request::getString('stonesort'),
	        'yansesort'=> _Request::getString('yansesort'),
	        'jdsort'=> _Request::getString('jdsort'),
	        'good_type'=> _Request::getInt('good_type'),
	        's_carats_tsyd1'=> $args['s_carats_tsyd1'],
	        'e_carats_tsyd1'=> $args['e_carats_tsyd1'],
	        's_carats_tsyd2'=> $args['s_carats_tsyd2'],
	        'e_carats_tsyd2'=> $args['e_carats_tsyd2'],
	        'gemx_zhengshu'=> _Request::getString('gemx_zhengshu'),
	        'ysyd'=> $args['ysyd'],
	        'warehouse'=> $_warehouse,
	        //'hrd_s_warehouse'=>$_hrd_s_warehouse,
	        'no_goods_id'=> $no_goods_id,
	        /*'ssy_active'=> $args['ssy_active'],*///双十一活动
	    	'pf_price_min' => _Request::getFloat('pf_price_min'),
	    	'pf_price_max' => _Request::getFloat('pf_price_max'),
	    	'include_img'  => _Request::getString('include_img'),
	    	'not_from_ad'   => $not_from_ad,
	    );
	    $model = new DiamondListModel(19);	    
	    $data = $model->pageList($where);
	    $pageData = $data['data'];
	    //print_r($data);   
        $datalist=$pageData;
        $_goods_list=array();
        $api_model = new ApiWarehouseModel();
        foreach($datalist['data'] as $key => $val){
            if($val['good_type']==1){
                $company_name = $api_model->get_company_name($val['warehouse']);
                if($company_name['error']==0){
                    $val['company_name']=$company_name['data'];
                    if(SYS_SCOPE=='zhanting' && $val['company_name']=='总公司')
                    	$val['company_name']='浩鹏';
                }else{
                    $val['company_name']='总公司';
                }
            }else{
                $val['company_name'] = '预定';
            }
            if($val['cert']=='HRD-D' && $val['kuan_sn']!=''){
                $dia_kuan=array();
                $dia_kuan=$model->get_diamond_by_kuan_sn($val['kuan_sn']);
                if($dia_kuan){
                    foreach($dia_kuan['data'] as $k => &$v){
                        if($v['goods_sn']!=$val['goods_sn']){
                            if($v['good_type']==1){
                                $company_name = $api_model->get_company_name($val['warehouse']);
                                if($company_name['error']==0){
                                    $v['company_name']=$company_name['data'];
				                    if(SYS_SCOPE=='zhanting' && $val['company_name']=='总公司')
				                    	$val['company_name']='浩鹏';                                    
                                }else{
                                    $v['company_name']='总公司';
                                }
                            }else{
                                $v['company_name'] = '预定';
                            }
							$this->calc_dia_channel_price($v);
                            $val['add']=$v;
                            break;
                        }
                    }
                }
            }
            $_goods_list[]=$val;
	        
	        unset($val);
	        unset($v);
	        $datalist['data']=$_goods_list;
	
	        $kuan_sn=array();
	        foreach($datalist['data'] as $key=>$val)
	        {
	            if($val['kuan_sn']!=''){
	                if(!in_array($val['kuan_sn'],$kuan_sn)){
	                    $kuan_sn[]=$val['kuan_sn'];
	                }else{
	                    unset($datalist['data'][$key]);
	                }
	            }
	        }
	    }
	    
	    $this->calc_dia_channel_price($datalist['data']);
		if (SYS_SCOPE == 'zhanting' && !empty($where['zdj'])) {
			if ($where['zdj'] == 'asc') {
				usort($datalist['data'], function($a, $b) {
					$ap = floatval($a['shop_price']);
					$bp = floatval($b['shop_price']);

					if ($ap == $bp) return 0;
					return $ap < $bp ? - 1 : 1;
				});
			} else {
				usort($datalist['data'], function($a, $b) {
					$ap = floatval($a['shop_price']);
					$bp = floatval($b['shop_price']);

					if ($ap == $bp) return 0;
					return $ap < $bp ? 1 : -1;
				});
			}
		}
	     
	    $pageData = $datalist;
	    $pageData['filter'] = $args;
	    $pageData['jsFuncs'] = 'diamond_list_search_page';
	    $this->render('diamond_list_search_list.html',array(
	        'pa'=>Util::page($pageData),
	        'dd'=>new DictView(new DictModel(1)),
	        'page_list'=>$datalist,
	    ));
	}	
	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$model = new DiamondListModel(19);
		$data = $model->getRowById($id);
        if(empty($data['data'])){
			die('数据错误!');
        }
		$this->render('diamond_list_show.html',array(
			'd'=>$data['data'],
            'dd'=>new DictView(new DictModel(1)),
		));
	}

	/**
	 *	showimg，渲染查看页面
	 */
	public function showImg ($params)
	{
		//print_r($params);
		//echo "kkk";
		$url = urldecode(_Request::getString("url"));
		$url_type = intval($params["url_type"]);		
		if($url_type==1){
            $result['content'] ="<iframe height='1000' width='1000' src='".$url."' id='iframepage' scrolling='no' frameborder='0' onload='changeFrameHeight()'></iframe><script>function changeFrameHeight(){
		       //$('.modal').width(1000),height(1000);		    
		       }
		       </script>";	
        } 
		if($url_type==2)
			$result['content'] ="<img src='".$url."' id='show_diamond_img' onload='changeImgHeight()'><script>function changeImgHeight(){
	            var img_height=$('#show_diamond_img').height()+50;
	            var img_width =$('#show_diamond_img').width()+50;
	            //if(img_height>1000)
	            //	img_height=600;
	            //if(img_width>1000)
	            //	img_width=1000;
	            //$('.modal').height(img_height).width(img_width); 
            }
            </script>";
		$result['title'] = '';

		Util::jsonExit($result);
	}
     //加入购物车
    public function addCart(){
        $id = _Request::getInt('id');
        $diamondModel = new SelfDiamondModel(19);
        $info = $diamondModel->selectDiamondInfo("*","goods_id={$id}",2);

        if(empty($info)){
            $result['error'] = '未查到数据!';
            Util::jsonExit($result);
        }       
        $select_goods_id = array($info['goods_sn']);
             
        $cartModel = new AppOrderCartModel(27);
        //裸钻只有一个所以去重
        $cartList = $cartModel->get_cart_goods();      
        if($cartList){
            foreach ($cartList as $val){
                if(in_array($val['goods_id'], $select_goods_id)){
                     $result['error'] = '此商品已经添加,请勿重复添加!';
                     Util::jsonExit($result);
                }
            }
        }
        
        $this->calc_dia_channel_price($info);
        
        $parent["session_id"]=DBSessionHandler::getSessionId();
        $parent["goods_id"]=$info['goods_sn'];
        $parent["goods_sn"]='DIA';
        $parent["goods_price"]=$info['shop_price'];
        $parent["is_stock_goods"]=$info['good_type']==1?1:0;
        $parent["goods_count"]=1;
        $parent["create_time"]=  date("Y-m-d H:i:s");
        $parent["modify_time"]= date("Y-m-d H:i:s");
        $parent["create_user"]=$_SESSION['userName'];
        $parent["cart"]=$info['carat'];
        $parent["cut"]=$info['cut'];
        $parent["clarity"]=$info['clarity'];
        $parent["color"]=$info['color'];
        $parent["goods_type"]='lz';
        $parent["kuan_sn"]=$info['kuan_sn'];
        $parent["product_type"]=0;
        $parent["cat_type"]=0;
        $parent["zhengshuhao"]=$info['cert_id'];
        $parent["goods_name"]=$info["carat"]."克拉/ct ".$info["clarity"]."净度 ".$info["color"]."颜色 ".$info["cut"]."切工";
		$parent["is_4c"] = 1;
        $parent['xiangkou'] = '0';
        $parent['zhiquan'] = '0';
        $parent['xiangqian'] = '不需工厂镶嵌';
        $cart_id=$cartModel->add_cart($parent);

        if($cart_id){
            $result['success'] = 1;
        }else{
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }
    
    protected function calc_dia_channel_price(&$diamond_list) {
    	if (empty($diamond_list)) return;
    	
    	if ($_SESSION['companyId'] == '666' || $_SESSION['companyId'] == '488' || $_SESSION['companyId'] == '623' || $_SESSION['companyId'] == '760') {
    		
    		$calc_func = function(&$d) {
    			if ($d['cert'] == 'HRD-S') {
					$x = 1.1;
					/*
					if ($_SESSION['companyId'] == '623') {
						if ($d['carat'] >= 0.5) {
							$x = 1.15;
						} else {
							$x = 1.35;
						}
					}*/ 
                    if ($_SESSION['companyId'] == '623' || $_SESSION['companyId'] == '760'){
                        $x = 1.05;
                    }
					
					$d['shop_price'] = round($d['shop_price'] * $x);
    			}
    		};
    		
    		if (count($diamond_list) == count($diamond_list, 1)) {
    			$calc_func($diamond_list);
    		} else {
    			foreach ($diamond_list as &$d) {
    				$calc_func($d);
    			}
    		}
    		
    		return;
    	}
    	
        if (SYS_SCOPE != 'zhanting') return;

    	$companyModel = new CompanyModel(1);
    	$company_type = $companyModel->select2("company_type","id={$_SESSION['companyId']}",3);
    	if ($company_type != '3') {
    		return;
    	}
    	
    	$sql = "select channel_id, s.channel_name from cuteframe.user_channel uc
    	inner join cuteframe.sales_channels s on s.id = uc.channel_id
    	inner join cuteframe.company c on s.company_id = c.id
    	where user_id = {$_SESSION['userId']} and c.id = {$_SESSION['companyId']}";
    	//echo $sql;die();
    	$channel_list = DB::cn(1)->getAll($sql);
    	if (empty($channel_list)) {
    		//exit("找不到销售渠道，无法计算");
    		if (count($diamond_list) == count($diamond_list, 1)) {
    			$diamond_list['shop_price_recalc'] = 0;
    		} else {
    			foreach ($diamond_list as &$d) {
    				$d['shop_price_recalc'] = 0;
    			}
    		}
    		
    		return;
    	}
    	
    	// TODO:  默认一个公司的所有渠道都是相同加价率
    	$channel_id = $channel_list[0]['channel_id'];
    	
    	$sql = "select * from front.diamond_channel_jiajialv where channel_id={$channel_id} and status = 1";
    	$channel_price_configs = DB::cn(99)->getAll($sql);
    	
    	$calc_func = function(&$d) use($channel_price_configs) {
    		if ($d['pifajia'] == 0) {
    			$d['shop_price_recalc'] = 0;
    			$d['shop_price'] = '--';
    			return;
    		}
    		
    		foreach ($channel_price_configs as $cfg) {
    			if ($cfg['cert'] == $d['cert'] && $d['good_type'] == $cfg['good_type'] && $cfg['carat_min'] <= $d['carat'] && $d['carat'] < $cfg['carat_max']) {
    				$d['shop_price'] = round($d['pifajia'] * $cfg['jiajialv']);
    				$d['shop_price_recalc'] = 1;
    				break;
    			}
    		}
    		
    		if (!isset($d['shop_price_recalc'])) {
    		    $lv =  $d['good_type'] == 1 ? 1.95 : 1.95;
    		    
    		    /**
    		     * 针对星耀： 如果没有设置加价率，按以下逻辑
    		     * 30-49分最低2.1；50-59分最低1.643；60-99分最低1.546；100-149分最低1.457；150分以上最低1.2
    		     */
    		    if ($d['cert'] == 'HRD-S') {
    		        if ($d['carat'] >= 1.5) {
    		            $lv = 1.2;
    		        } else if ($d['carat'] >= 1) {
    		            $lv = 1.457;
    		        } else if ($d['carat'] >= 0.6) {
    		            $lv = 1.546;
    		        } else if ($d['carat'] >= 0.5) {
    		            $lv = 1.643;
    		        } else if ($d['carat'] >= 0.3) {
    		            $lv = 2.1;
    		        }
    		    }
    		    
    			$d['shop_price'] = round($d['pifajia'] * $lv); //避免将成本价显示出来
    			$d['shop_price_recalc'] = 0;
    		}
    	};
    	
    	if (count($diamond_list) == count($diamond_list, 1)) {
    		$calc_func($diamond_list);
    	} else {
	    	foreach ($diamond_list as &$d) {
	    		$calc_func($d);
	    	}
    	}
    }
}

?>
