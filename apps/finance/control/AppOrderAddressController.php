<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderAddressController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-07-20 17:27:38
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderAddressController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_order_address_search_form.html',array('bar'=>Auth::getBar()));
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
		/*
    $pagenow = isset ( $_REQUEST ['pn'] ) ? intval ( $_REQUEST ['pn'] ) : 1;
    $smarty->assign ( 'pagenow', $pagenow );
    // 每页条数
    $size = 30;
    // 总纪录数
    $arr_count = $db->getOne ( "SELECT count(*) FROM `ecs_order_info` as o,`ecs_department_channel` as d  WHERE  
            d.dc_id = o.department
            AND order_time >='2014-10-01 00:00:00' 
            AND order_status =1 
            AND pay_status !=0 
            AND (province ='' || city ='' || country ='' )
            AND order_type not in (1,4) 
            AND (is_zp = 0 OR (goods_amount + shipping_fee + insure_fee + pay_fee + pack_fee + card_fee + order_goods_attach_price + order_attach_price) > 0) 
            AND department in ('0','2','3','10','55','81')" );
    $page_count = ceil ( $arr_count / $size );
    $pp = $pagenow - 1;
    $pagepre = $pagenext = true;
    if ($pp <= 0) {
        $pagepre = false;
        $pp = 1;
    }
    $pn = $pagenow + 1;
    if ($pagenow >= $page_count) {
        $pagenext = false;
        $pn = $page_count;
    }
    
    $s = ($pagenow - 1) * $size;
    // 线上截至今天 (官方网站部 淘宝销售部 京东销售部 银行渠道部 ) 的 所有订单 订单总金额
    $online_sql = "SELECT order_id,dep_name,order_sn,address,district,province,city,country,order_time FROM  `ecs_order_info` as o,`ecs_department_channel` as d  WHERE  
            d.dc_id = o.department
            AND order_time >='2014-10-01 00:00:00' 
            AND order_status =1 
            AND pay_status !=0 
            AND (province ='' || city ='' || country ='' )
            AND order_type not in (1,4) 
            AND (is_zp = 0 OR (goods_amount + shipping_fee + insure_fee + pay_fee + pack_fee + card_fee + order_goods_attach_price + order_attach_price) > 0) 
            AND department in ('0','2','3','10','55','81')  
            ORDER BY order_time ASC
            limit $s,$size";
    $amount_online = $db->getAll ( $online_sql );
    
    $smarty->assign ( 'orderList', $amount_online );
    $smarty->assign ( 'province_list', getRegionProvice () );
    $smarty->assign ( 'pn', $pn );
    $smarty->assign ( 'pp', $pp );
    $smarty->assign ( 'pagepre', $pagepre );
    $smarty->assign ( 'pagenext', $pagenext );
    $smarty->assign ( 'pagecount', $page_count ); // 总页数
    $smarty->assign ( 'allcount', $arr_count ); // 总记录
    $smarty->display ( 'orderList.htm' );

		*/
		$page = _Request::getInt("page",1);
		$where = array();

		$model = new AppOrderAddressModel(27);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_order_address_search_page';
		$this->render('app_order_address_search_list.html',array(
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
		$result['content'] = $this->fetch('app_order_address_info.html',array(
			'view'=>new AppOrderAddressView(new AppOrderAddressModel(27))
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
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_order_address_info.html',array(
			'view'=>new AppOrderAddressView(new AppOrderAddressModel($id,27)),
			'tab_id'=>$tab_id,'app_address'=>true,'id'=>$id
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
		$this->render('app_order_address_show.html',array(
			'view'=>new AppOrderAddressView(new AppOrderAddressModel($id,27)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;
		$olddo = array();
		$newdo=array();

		$newmodel =  new AppOrderAddressModel(28);
		$res = $newmodel->saveData($newdo,$olddo);
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
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');
		$id = _Post::getInt('id');
		//var_dump($_POST);die;
		$newmodel =  new AppOrderAddressModel($id,28);
		$olddo = $newmodel->getDataObject();
		$newdo = array(
            'id'=>$id,
            'country_id'=>_Request::getInt('mem_country_id'),
            'province_id'=>_Request::getInt('mem_province_id'),
            'city_id'=>_Request::getInt('mem_city_id'),
            'regional_id'=>_Request::getInt('mem_district_id')
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
		$model = new AppOrderAddressModel($id,28);
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
}

?>