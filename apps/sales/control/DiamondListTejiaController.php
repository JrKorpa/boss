<?php
/**
 * 青岛婚博会特价钻石
 *  -------------------------------------------------
 *   @file		: DiamondListTejiaController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2015-09-15
 *   @update	:
 *  -------------------------------------------------
 */
class DiamondListTejiaController extends CommonController
{
	protected $smartyDebugEnabled = true;
	/**
	 * 4C快捷搜索FORM表单
	 */
	public function index($params){
	    $model = new Diamond4CTejiaModel(19); 
	    $citylist = $model->getCityList();
	    $this->render('diamond_list_tejia_form.html',array(
	        'bar'=>Auth::getBar(),
	        'citylist'=>$citylist,
	    ));
	}
	
	/**
	 * 4C快捷搜索
	 */
    public function search($params){
                        
        $_SESSION['cart_filter_data3'] = array();
        $citylist= array(
            "上海"=>array(59,223),// 公司id
            "郑州"=>array(189),
            "天津"=>array(270),
            "乌鲁木齐"=>array(297),
            "苏州"=>array(365),
            "长沙"=>array(477),
            "青岛"=>array(478),
            "南京"=>array(479),
            "武汉"=>array(487),
            "杭州"=>array(488),
            "合肥"=>array(489),
            "成都"=>array(498),
            "重庆"=>array(499),
            "广州"=>array(500,501),
            "深圳"=>array(502,506),
            "北京"=>array(300),
        );
        $company_id = Auth::$companyId;
        $city = '';        
        if($company_id){
            /*foreach ($citylist as $key=>$vo){
                if(in_array($company_id,$vo)){
                    $city = $key;
                }
            }
            if(empty($city)){
                $js = "<script>util.xalert('您当前所在公司不支持该活动，请在右上角切换用户所在公司至支持当前活动公司！');</script>";
                echo $js;exit;
            }else{
                if(time()>=strtotime('2016-01-03')){
                    $js = "<script>util.xalert('{$city}特价钻活动已结束(结束时间{})');</script>";
                    echo $js;exit;
                }
            }*/
            $city_arr[] = "全国";
            //$city_arr[] = $city;
            
        }else{
            $js = "<script>bootbox.confirm({ 
						buttons: {  
							confirm: {  
								label: '前往登记' 
							},  
							cancel: {  
								label: '下次登记'  
							}  
						},  
						message: '亲，你还没有选择所在公司，请先行登记！', 
						closeButton: false,
						callback: function(result) {  
							if (result == true) {
								$('body').modalmanager('loading');
								setTimeout(function(){
									$.post('index.php?mod=management&con=main&act=changeCompany',function(data){
										$('.modal .modal-title').html(data.title);
										$('.modal .modal-body').html(data.content);
										$('.modal .modal-footer').hide();
										$('.modal').modal('toggle');
									});
									util.retrieveReload();
								}, 200);
							}
						},  
						title: '提示', 
					});</script>";
            echo $js;
            exit;
        }
        $args = array(
            'mod'	=> _Request::get("mod"),
            'con'	=> substr(__CLASS__, 0, -10),
            'act'	=> __FUNCTION__,
            'city'=>  _Request::getString('city')
        );

        $no_goods_id = array();
        $cartModel = new AppOrderCartModel(27);
        $cart_goods = $cartModel->get_cart_goods();
        foreach($cart_goods as $good){
            $no_goods_id[] = $good['goods_id'];            	
        }
        $_warehouse = array();
        if(SYS_SCOPE=="zhanting"){
            $companyId = $_SESSION['companyId'];
            $companyModel = new CompanyModel(1);
            $comInfo = $companyModel->select2("*","id={$companyId}",2);
            //company_type<>1 ：个体店和经销商 只可以看 （自己门店 +总公司）
            if($comInfo['company_type']<>1){
                $companyList = array($companyId,58);
            }else{
                //直营店店的可以看（所有直营店 +总公司）
                $companyList = $companyModel->select2("id","company_type=1",1);
                $companyList = array_column($companyList,'id');
                $companyList[] = 58;
            }
            $wareshou_model = new ApiWarehouseModel();
	        $warehouse = $wareshou_model->get_warehouse_all(1,implode(',', $companyList));
	        if($warehouse['error']==0){
	            $_warehouse = array_column($warehouse['data'],'code');
	        }
            array_push($_warehouse, 'COM');
        }
        $shape_arr = $this->dd->getEnumArray("shape");
        $shape_arr = array_column($shape_arr,"label","name");

        $shape_keys_arr = array_flip($shape_arr);
        $diamondListModel = new DiamondListModel();
        $model = new Diamond4CTejiaModel(19);
        $tejialist = $model->getList(array("city"=>$city_arr));
        $datalist = array();
		if(empty($tejialist)){
			$js = "<script>util.xalert('{$city}特价钻活动已结束或没有相关活动');</script>";
			echo $js;exit;
		}
        foreach ($tejialist as $vo){
            //如果形状缺失，终止运行
            if(empty($shape_keys_arr[$vo['shape']])){
                die("形状配置有问题，请联系技术处理!");
            }
            $title = $vo['title'];			
            $color = explode("/",$vo['color']);//颜色
            $shape = array($shape_keys_arr[$vo['shape']]);//形状           
            $carat_min = $vo['carat_min'];//石重
            $carat_max = $vo['carat_max'];//石重
            $clarity = explode('/',$vo['clarity']);//净度
            $cut = explode('/',$vo['cut']);//切工、
            $symmetry = explode('/',$vo['symmetry']);//对称
            $polish = explode('/',$vo['polish']);//抛光
            $fluorescence = explode('/',$vo['fluorescence']);//抛光
            
            $where = array(
                'page'=>1,
                'pageSize'=>1,
                'color' =>$color,
                'shape' =>$shape,
                'carat_min'=>$carat_min,
                'carat_max'=>$carat_max,
                'clarity' =>$clarity,
                'cut'=>$cut,
                'symmetry'=>$symmetry,
                'polish'=>$polish,
                'fluorescence'=>$fluorescence,
                'status'=> 1,
                'no_goods_id'=> $no_goods_id,
                'is_4c' => 3,
                'data' => $vo,
                //'warehouse'=>$_warehouse
            );
            //如果荧光为空，去除荧光搜索条件
            $show_fluorescence = 1;
            if(empty($vo['fluorescence'])){
                $show_fluorescence=0;
                unset($where['fluorescence']);
            }
            
            $where['pageSize']  = 10;//查询出最低价格的10个裸钻
            $result = $diamondListModel->pageList($where);
            if(!empty($result['data']['data'])){
                $p = $result['data']['data'][0]['shop_price'];
				foreach ($result['data']['data'] as $data){
				    $where['price_min'] = $data['shop_price']+0.001;
				    if($data['shop_price']>$p){
				        break;
				    }
				}				
                //二次查询，最低价格为倒数第3的裸钻
				$where['pageSize']  = 1;
                $result = $diamondListModel->pageList($where);
                if(!empty($result['data']['data'])){
                    $data = $result['data']['data'][0];
                }
                $data['special_price'] = $vo['special_price'];
                $data['city'] = $vo['city'];
                $data['title']=$title;
                //是否显示荧光
                $data['show_fluorescence']=$show_fluorescence;
                $datalist[]=$data;
            }else{
                continue;
            }
            
        }
        $pageData = array(
            'page'=>1, 
            'pageSize'=>count($tejialist),
            'recordCount'=>count($tejialist),
            'pageCount'=>1, 
            'data'=>$datalist,
            'filter'=>$args,
            'jsFuncs'=>'diamond_list_tejia_search_page',       
        );
        
        $this->render('diamond_list_tejia_search_list.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$pageData,
            'city'=>$city,
        ));
        
    } 

    //加入购物车
    public function addCart(){
        $result = array('success'=>0,'error'=>'');
        $id = _Request::getInt('id');
        $model = new DiamondListModel(19);
        if (!empty($_SESSION['cart_filter_data3'][$id])){
            //重新根据4c条件查询
            $data = $model->getRowByKeysAndVals($_SESSION['cart_filter_data3'][$id]['keys'],$_SESSION['cart_filter_data3'][$id]['vals'] );
        }else{
            $data = $model->getRowById($id);
        }
        if($data['error']==1){
            $result['error'] = '未查到数据!';
            Util::jsonExit($result);
        }
    
        $info = $data['data'];
        $select_goods_id = array_column($info, 'goods_sn');

        //裸钻只有一个所以去重
        $cartModel = new AppOrderCartModel(27);
        $cartList = $cartModel->get_cart_goods();
        if($cartList){
            foreach ($cartList as $val){
                if(in_array($val['goods_id'], $select_goods_id)){
                    $result['error'] = '此商品已经添加,请勿重复添加!';
                    Util::jsonExit($result);
                }
            }
        }
    
        foreach ($info as $val){
            $parent["session_id"]=DBSessionHandler::getSessionId();
            $parent["goods_id"]=$val['goods_sn'];
            $parent["goods_sn"]='DIA';
            $parent["goods_price"]=$val['shop_price'];
            $parent["is_stock_goods"]=$val['good_type'];
            $parent["goods_count"]=1;
            $parent["create_time"]=  date("Y-m-d H:i:s");
            $parent["modify_time"]= date("Y-m-d H:i:s");
            $parent["create_user"]=$_SESSION['userName'];
            $parent["cart"]=$val['carat'];
            $parent["cut"]=$val['cut'];
            $parent["clarity"]=$val['clarity'];
            $parent["color"]=$val['color'];
            $parent["tuo_type"]='成品';
            $parent["cert"]=$val["cert"];
            $parent["goods_type"]='lz';
            $parent["kuan_sn"]=$val['kuan_sn'];
            $parent["product_type"]=0;
            $parent["cat_type"]=0;
            $parent["zhengshuhao"]=$val['cert_id'];
            $parent["goods_name"]=$val["carat"]."克拉/ct ".$val["clarity"]."净度 ".$val["color"]."颜色 ".$val["cut"]."切工";
            $parent["is_4c"] = 3;
            //file_put_contents('8y.txt',var_export($_SESSION['cart_filter_data3'],true));
            if(empty($_SESSION['cart_filter_data3'][$id])){                
                $result['error'] = '页面表单提交超时，请重新搜索！';
                Util::jsonExit($result);
            }else{
                $parent["filter_data"]= json_encode($_SESSION['cart_filter_data3'][$id]);
            }
            $cart_id=$cartModel->add_cart($parent);            
            break;
        }
    
        if($cart_id){            
            $result['success'] = 1;
        }else{
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }
}

?>