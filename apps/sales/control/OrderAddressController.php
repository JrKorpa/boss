<?php
/**
 *  -------------------------------------------------
 *   @file		: OrderAddressController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 11:03:36
 *   @update	:
 *  -------------------------------------------------
 */
class OrderAddressController extends Controller
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{

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


		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();

		$model = new OrderAddressModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'order_address_search_page';
		$this->render('order_address_search_list.html',array(
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

		$result['content'] = $this->fetch('order_address_info.html',array(
			'view'=>new OrderAddressView(new OrderAddressModel(27)),
			'dd'=>new DictView(new DictModel(1)),
			'id'=>_Get::getInt('id'),

		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$order_id = $params['id'];
		$model_address = new OrderAddressModel(27);
		$info = $model_address->select($order_id);
		if ($info)
		{
			$view = new OrderAddressView(new OrderAddressModel($info['id'],27));
		}
		else
		{
			$view = new OrderAddressView(new OrderAddressModel(27));
		}
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('order_address_info.html',array(
			'view'=>$view,
			'dd'=>new DictView(new DictModel(1)),
			'id'=>$order_id,
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
		$this->render('order_address_show.html',array(
			'view'=>new OrderAddressView(new OrderAddressModel($id,1))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$consignee = _Post::get('consignee');
		$tel = _Post::get('tel');
		$distribution_type = _Post::get('distribution_type');
		$country_id  = _Post::get('country_id');
		$province_id = _Post::get('province_id');
		$city_id = _Post::get('city_id');
		$regional_id = _Post::getInt('regional_id');
		$order_id = _Post::getInt('order_id');
		$address = _Post::get('address');
		$zipcode = _Post::get('zipcode');
		$email = _Post::get('email');
		//echo '<pre>';
		//print_r ($_POST);
		//echo '</pre>';
		//exit;
		$olddo = array();
		$newdo=array(
			'tel'=>$tel,
			'email'=>$email,
			'consignee'=>$consignee,
			'distribution_type'=>$distribution_type,
			'country_id'=>$country_id ,
			'province_id'=>$province_id,
			'city_id'=>$city_id,
			'regional_id'=>$regional_id,
			'order_id'=>$order_id,
			'address'=>$address,
			'zipcode'=>$zipcode,
			'goods_id'=>0
		
			);
		//var_dump($newdo);exit;
		$newmodel =  new OrderAddressModel(28);
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
		$id = _Post::getInt('id');
		//echo '<pre>';
		//print_r ($_POST);
		//echo '</pre>';
		//exit;
		$consignee = _Post::get('consignee');
		$tel = _Post::get('tel');
		$distribution_type = _Post::get('distribution_type');
		$country_id  = _Post::get('country_id');
		$province_id = _Post::get('province_id');
		$city_id = _Post::get('city_id');
		$regional_id = _Post::getInt('regional_id');
		//$order_id = _Post::get('order_id');
		$address = _Post::get('address');
		$zipcode = _Post::get('zipcode');
		$email = _Post::get('email');
		$newmodel =  new OrderAddressModel($id,28);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			'tel'=>$tel,
			'email'=>$email,
			'consignee'=>$consignee,
			'distribution_type'=>$distribution_type,
			'country_id'=>$country_id ,
			'province_id'=>$province_id,
			'city_id'=>$city_id,
			'regional_id'=>$regional_id,
			//'order_id'=>$order_id,
			'address'=>$address,
			'zipcode'=>$zipcode,
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
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new OrderAddressModel($id,2);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>