<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseOrderInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-08-15 16:06:49
 *   @update	:
 *  -------------------------------------------------
 */
class SaleUnfilledOrdersController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('sale_unfilled_orders_search_form.html',array('bar'=>Auth::getBar()));
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
		$where = array();
        $data=[];
		$model = new SaleRefundItemModel(27);
		//天猫数据
        $data['tMSameDate']=$model->getUnfilledOrders(1,2);
        $data['tMTwentyDate']=$model->getUnfilledOrders(20,2);
        $data['tMThirtyDate']=$model->getUnfilledOrders(30,2);
        //京东数据
        $data['jDSameDate']=$model->getUnfilledOrders(1,71);
        $data['jDTwentyDate']=$model->getUnfilledOrders(20,71);
        $data['jDThirtyDate']=$model->getUnfilledOrders(30,71);
        //和 数据
        $data['sumSameDate'][]=sprintf('%1.2f',$data['tMSameDate'][0]['price']+$data['jDSameDate'][0]['price']);
        $data['sumSameDate'][]=sprintf('%1.2f',$data['tMSameDate'][0]['count']+$data['jDSameDate'][0]['count']);
        $data['sumTwentyDate'][]=sprintf('%1.2f',$data['tMTwentyDate'][0]['price']+$data['jDTwentyDate'][0]['price']);
        $data['sumTwentyDate'][]=sprintf('%1.2f',$data['tMTwentyDate'][0]['count']+$data['jDTwentyDate'][0]['count']);
        $data['sumThirtyDate'][]=sprintf('%1.2f',$data['tMThirtyDate'][0]['price']+$data['jDThirtyDate'][0]['price']);
        $data['sumThirtyDate'][]=sprintf('%1.2f',$data['tMThirtyDate'][0]['count']+$data['jDThirtyDate'][0]['count']);
		/*$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'base_order_info_search_page';*/
		$this->render('sale_unfilled_orders_search_list.html',array(
			//'pa'=>Util::page($pageData),
			'data'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('base_order_info_info.html',array(
			'view'=>new BaseOrderInfoView(new BaseOrderInfoModel(27))
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
		$result['content'] = $this->fetch('base_order_info_info.html',array(
			'view'=>new BaseOrderInfoView(new BaseOrderInfoModel($id,27)),
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
		$id = intval($params["id"]);
		$this->render('sale_unfilled_orders_show.html',array(
			'view'=>new BaseOrderInfoView(new BaseOrderInfoModel($id,27)),
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

		$newmodel =  new BaseOrderInfoModel(28);
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
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new BaseOrderInfoModel($id,28);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
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
		$model = new BaseOrderInfoModel($id,28);
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