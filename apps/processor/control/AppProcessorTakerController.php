<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorTakerController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 10:11:14
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorTakerController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('printTaker');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$supplierModel = new AppProcessorInfoModel(13);
		$suppliers = $supplierModel->getList();
		$this->render('app_processor_taker_search_form.html',[
			'bar'=>Auth::getBar(), 'suppliers'=>$suppliers
		]);
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
			'supplier_id'=>_Request::getInt('supplier_id'),
		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['supplier_id'] = $args['supplier_id'];

		$model = new AppProcessorTakerModel(13);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_processor_taker_search_page';
		$this->render('app_processor_taker_search_list.html',array(
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
		$supplier_id = _Post::getInt('supplier_id');
		$model = new AppProcessorTakerModel(13);
		$taker = $model->getTaker($supplier_id);
		$view = new AppProcessorTakerView(new AppProcessorTakerModel(13));
		$view->set_supplier_id($supplier_id);

		$result['content'] = $this->fetch('app_processor_taker_info.html',array(
			'view'=>$view,'supplier_id'=>$supplier_id,'taker'=>$taker
		));
		$result['title'] = '添加委托取货人';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ()
	{
		$result = array('success' => 0,'error' =>'');

		$now = _Post::getList('data');
		$supplier_id = _Post::getInt('supplier_id');
		$model =  new AppProcessorTakerModel(14);
		$old_er = $model->getTakerId($supplier_id);

		$old = array();
		if(!empty($old_er)){
			foreach ($old_er as $v) {$old[] = $v['taker_id'];}
		}

		$del = array_diff($old,$now);		//删除用户
		$pass = array_intersect($old,$now);	//重复用户
		$new = array_diff($now,$old);		//新增用户

		//新增取货人
		if(!empty($new)) {
			foreach ($new as $v) {
				$user[$v] = $model->getUserInfo($v);
			}
			$olddo = array();
			foreach ($user as $k => $v) {
				$newdo=array();
				$newdo['supplier_id'] = $supplier_id;
				$newdo['taker_id'] = $k;
				$newdo['taker_account'] = $v['account'];
				$newdo['taker_name'] = $v['real_name'];
				$newdo['taker_gender'] = $v['gender'];
				$newdo['taker_tel'] = $v['mobile'];
				$newdo['taker_papers'] = $v['icd'];
				$newdo['create_id'] = $_SESSION['userId'];
				$newdo['create_time'] = time();
				$newdo['is_deleted'] = 0;//新增默认
				$res = $model->saveData($newdo,$olddo);
			}
		}
		//删除采购人
		if(!empty($del)) {
			foreach ($del as $v) {
				$sql = 'UPDATE `app_processor_taker` SET	`is_deleted` = 1 WHERE `supplier_id` = '.$supplier_id.' AND `taker_id` = '.$v;
				$res = DB::cn(14)->query($sql);
			}
		}
		//启用重复用户
		if(!empty($pass)) {
			foreach ($pass as $v) {
				$sql = 'UPDATE `app_processor_taker` SET	`is_deleted` = 0 WHERE `supplier_id` = '.$supplier_id.' AND `taker_id` = '.$v;
				$res = DB::cn(14)->query($sql);
			}
		}


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
	 * 打印委托取货人
	 */
	public function printTaker(){
		$supplier_id = _Request::getInt('supplierId');
		$model =  new AppProcessorTakerModel(14);
		$taker = $model->getTaker($supplier_id);
		$supplier = $model->getSupName($supplier_id);

		$this->render('app_processor_taker_print.html',[
			'taker'=>$taker,'supplier'=>$supplier
		]);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppProcessorTakerModel($id,14);
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