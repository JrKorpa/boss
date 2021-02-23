<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseOrderInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-07-12 14:44:01
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseOrderInfoController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected static $buchan_status = array('1'=>'未操作','2'=>'已布产','3'=>'生产中','4'=>'已出厂','5'=>'不需布产');
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $SalesChannelsModel = new SalesChannelsModel(1);
        $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
		$this->render('purchase_order_info_search_form.html',array('bar'=>Auth::getBar(),'channellist' => $channellist,'buchan_status'=>self::$buchan_status));
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
            'order_sn'   => _Request::get("order_sn"),
            'out_order_sn'   => _Request::get("out_order_sn"),
            'create_user'   => _Request::get("create_user"),
            'start_time'   => _Request::get("start_time"),
            'end_time'   => _Request::get("end_time"),
            'order_status'   => _Request::get("order_status"),
            'buchan_status'   => _Request::get("buchan_status"),
            'delivery_status'   => _Request::get("delivery_status"),
            'order_department'   => _Request::get("order_department"),
            'is_zhanyong'   => _Request::get("is_zhanyong")
		);
		$page = _Request::getInt("page",1);
		$where = array(
            'order_sn'   =>$args["order_sn"],
            'out_order_sn'   => $args["out_order_sn"],
            'create_user'   => $args["create_user"],
            'start_time'   => $args["start_time"],
            'end_time'   => $args["end_time"],
            'order_status'   => $args["order_status"],
            'buchan_status'   => $args["buchan_status"],
            'delivery_status'   => $args["delivery_status"],
            'order_department'   => $args["order_department"],
            'is_zhanyong'   => $args["is_zhanyong"]
            );
		$model = new PurchaseOrderInfoModel(27);
		$data = $model->pageList($where,$page,10,false);
        //var_dump($data);die;
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'purchase_order_info_search_page';
		$this->render('purchase_order_info_search_list.html',array(
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
		$result['content'] = $this->fetch('purchase_order_info_info.html',array(
			'view'=>new PurchaseOrderInfoView(new PurchaseOrderInfoModel(27))
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
		$result['content'] = $this->fetch('purchase_order_info_info.html',array(
			'view'=>new PurchaseOrderInfoView(new PurchaseOrderInfoModel($id,27)),
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
		$this->render('purchase_order_info_show.html',array(
			'view'=>new PurchaseOrderInfoView(new PurchaseOrderInfoModel($id,27)),
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

		$newmodel =  new PurchaseOrderInfoModel(28);
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

		$newmodel =  new PurchaseOrderInfoModel($id,28);

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
		$model = new PurchaseOrderInfoModel($id,28);
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