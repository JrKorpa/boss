<?php
/**
 *  -------------------------------------------------
 *   @file		: JitxAppOrderDetailsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2019-06-28 10:36:12
 *   @update	:
 *  -------------------------------------------------
 */
class JitxAppOrderDetailsController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array( 'getGoods','search');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('jitx_app_order_details_search_form.html',array('bar'=>Auth::getBar()));
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


		);
		$page = _Request::getInt("page",1);
		$where = array(
            'order_sn' => _Request::get("order_sn"), 
            'vop_barcode' => _Request::get("vop_barcode"),
		    'bind_status' => _Request::get("bind_status"),
		);
	

		$model = new JitxAppOrderDetailsModel(27);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $where;
		$pageData['jsFuncs'] = 'jitx_app_order_details_search_page';
		$this->render('jitx_app_order_details_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	

	/**
	 *	edit，渲染修改页面
	 */
	public function showBindForm ($params)
	{
		$id = intval($params["id"]);
		$model = new JitxAppOrderDetailsModel(27);
		$data = $model->getOrderDetail($id);

		//exit('11');
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('jitx_app_order_details_info.html',array(
			'view'=>new JitxAppOrderDetailsView(new JitxAppOrderDetailsModel($id,27)),
			'data'=>$data,
			'tab_id'=>$tab_id
		));
		$result['title'] = '搜索绑定货号';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('jitx_app_order_details_show.html',array(
			'view'=>new JitxAppOrderDetailsView(new JitxAppOrderDetailsModel($id,27)),
			'bar'=>Auth::getViewBar()
		));
	}



	/**
	 *	update，更新信息
	 */
	public function bindGoods ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$detail_id = _Post::getInt('id');
		$goods_id = _Post::getInt('goods_id');


		$newmodel =  new JitxAppOrderDetailsModel(27);
        
		$res = $newmodel->bindGoods($detail_id,$goods_id);
		

		
		if($res === 1)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '修改此处为想显示在页签上的字段';
		}
		else
		{
			$result['error'] = $res;
		}
		Util::jsonExit($result);
	}

    //**根据款号搜索未绑定货号
    public function getGoods($params,$warehouse){
    	$result = array('success' => 0,'error' =>'');
    	$goods_sn = trim($params['goods_sn']);
    	$goods_id = trim($params['goods_id']);
    	$warehouse_code = trim($params['warehouse_code']);
    	if(empty($goods_sn) && empty($goods_id)){
    		$result['error'] = '未传入有效款号或者货号';
    		Util::jsonExit($result);
    	}
        
		$model = new JitxAppOrderDetailsModel(27);
		$data = $model->getUnbindGoods($params,$warehouse_code);
		if(!empty($data)){
			$result['success'] = 1;
			$content = $this->fetch("jitx_app_order_details_search_goods_list.html",array('data'=>$data));
			$result['content'] = $content;
		}else{
			$result['error'] = '未查到有效库存';
		}
        Util::jsonExit($result); 

    }
	
}

?>