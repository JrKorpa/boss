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
class ColorDiamondListController extends Controller
{
	protected $smartyDebugEnabled = true;
    protected $code = array();
    protected $warehouse_arrs = array();


	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('diamond_list','app_order',27);	//生成模型后请注释该行
		//Util::V('diamond_list',27);	//生成视图后请注释该行
		$this->render('color_diamond_list_form.html',array(
				'bar'=>Auth::getBar(),
                'view'=>new ColorDiamondListView(new ColorDiamondListModel(19)),
			));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
        $pageSize = isset($_REQUEST["pageSize"]) ? intval($_REQUEST["pageSize"]) : 15 ;
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
            'page'=>  $page,
            'pageSize'=>  $pageSize,
			'carat_min'=>  _Request::getFloat('carat_min'),
			'carat_max'=>  _Request::getFloat('carat_max'),
			'cert'=> _Request::getString('cert'),
			'clarity'=> _Request::getString('clarity'),
			'color'=> _Request::getString('color'),
			'color_grade'=> _Request::getString('color_grade'),
			'shape'=> _Request::getString('shape'),
// 			'symmetry'=> _Request::getString('symmetry'),
// 			'polish'=> _Request::getString('polish'),
// 			'fluorescence'=> _Request::getString('fluorescence'),
			'from_ad'=> _Request::getString('from_ad'),
			'cert_id'=> _Request::getString('cert_id'),
			'goods_sn'=> _Request::getString('goods_sn'),
            'price_min'=>  _Request::getFloat('price_min'),
            'price_max'=>  _Request::getFloat('price_max'),
			
		);
		
		$newmodel = new ColorDiamondListModel(19);
		$from_ads = $newmodel->getFromads_arr();
		$from_ads = array_flip($from_ads);
		
		$where = array(
            'page'=>  $page,
            'pageSize'=>  $pageSize,
            'carat_min'=> $args['carat_min'],
            'carat_max'=> $args['carat_max'],
			'cert'=> $args['cert'],
            'clarity'=> $args['clarity'],
            'color'=> $args['color'],
            'color_grade'=> $args['color_grade'],
            'shape'=> $args['shape'],
// 			'symmetry'=> $args['symmetry'],
// 			'polish'=> $args['polish'],
// 			'fluorescence'=> $args['fluorescence'],
			'from_ad'=> $from_ads[$args['from_ad']],
			'cert_id'=> $args['cert_id'],
			'goods_sn'=> $args['goods_sn'],
			'price_min'=> $args['price_min'],
			'price_max'=> $args['price_max'],
		);
		if(empty($where['carat_min'])){
			unset($where['carat_min']);
		}
		if(empty($where['carat_max'])){
			unset($where['carat_max']);
		}
		if(empty($where['price_min'])){
			unset($where['price_min']);
		}
		if(empty($where['price_max'])){
			unset($where['price_max']);
		}
		$model = new ColorDiamondListModel(19);

        $Shape_arr = $model->getShapeName();
		$data = $model->pageList($where);
		
		$pageData = $data['data'];
        if($pageData=='未查询到此彩钻'){
            $datalist['data'] = array();
        }else{
            $datalist=$pageData;
            $_goods_list=array();
            foreach($datalist['data'] as $key => $val){
                $_goods_list[]=$val;
            }
            $datalist['data']=$_goods_list;
        }
        $pageData = $datalist;
        
        $from_ads = $model->getFromads_arr();
        
        foreach($datalist['data'] as $k=>$v){
        	$datalist['data'][$k]['from_ad'] = $from_ads[$v['from_ad']];
        }
		$pageData['filter'] = $args;
		
		$pageData['jsFuncs'] = 'colordiamond_list_search_page';
		$this->render('colordiamond_list_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$datalist,
			'Shape_arr'=>$Shape_arr,
			'warehouse_arrs'=>$this->warehouse_arrs
		));
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$model = new DiamondListModel(19);
		$data = $model->getColorRowById($id);
        if(empty($data['data'])){
			die('数据错误!');
        }
        
		$this->render('diamond_list_show.html',array(
			'data'=>$data['data'][0]
//             'dict'=>new DictView(new DictModel(1)),
		));
	}
	
	
// 	/**
// 	 *	show，渲染查看页面
// 	 */
// 	public function show ($params)
// 	{
// 		$view = new AppDiamondColorView(new AppDiamondColorModel($id,19));
// 		$id = intval($params["id"]);
// 		$this->render('app_diamond_color_show.html',array(
// 				'view'=>new AppDiamondColorView(new AppDiamondColorModel($id,19)),
// 				'bar'=>Auth::getViewBar()
// 		));
// 	}
	

     //加入购物车
    public function addCart(){
    	
        $id = _Request::getInt('id');
        $model = new DiamondListModel(19);
		$data = $model->getColorRowById($id);

        if($data['error']==1){
            $result['error'] = '未查到数据!';
            Util::jsonExit($result);
        }

        $info = $data['data'];
        $select_goods_id = array_column($info, 'id');

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
            $parent["session_id"] = DBSessionHandler::getSessionId();  
            $parent["goods_id"] = $val['goods_sn'];						//商品ID
            $parent["goods_sn"] = "CAIZUAN";				//商品编号
            $parent["goods_type"] ='caizuan_goods';							//商品类型
            $parent["goods_name"] ='彩钻';							//商品名称
            $parent["goods_price"] =empty($val['price'])?'--':$val['price'];							//商品价格
            $parent["favorable_price"] ='100';						//优惠价
            $parent["goods_count"] =empty($val['quantity'])?1:$val['quantity'];							//数量
            $parent["is_stock_goods"] =$val['good_type']==1?1:0;						//是否是现货：1现货 0期货
            $parent["create_time"] =date("Y-m-d H:i:s");			//创建时间
            $parent["modify_time"] =date("Y-m-d H:i:s");
            $parent["cart"] = empty($val['carat'])?'--':$val['carat'];
            $parent["clarity"] =empty($val['clarity'])?'--':$val['clarity'];
            $parent["color"] =empty($val['color'])?'--':$val['color'];
            $parent["zhengshuhao"] =empty($val['cert_id'])?'--':$val['cert_id'];
            $parent["create_user"] =$_SESSION['userName'];
            $parent["product_type"] =0;
            
            $cart_id=$cartModel->add_cart($parent);
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