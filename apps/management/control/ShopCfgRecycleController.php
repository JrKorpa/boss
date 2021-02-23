<?php
/**
 *  -------------------------------------------------
 *   @file		: ShopCfgController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-30 10:14:28
 *   @update	:
 *  -------------------------------------------------
 */
class ShopCfgRecycleController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('shop_cfg_recycle_search_form.html',array('bar'=>Auth::getBar()));
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
			'shop_name'=>_Request::get("shop_name"),
			'is_delete'=>1,
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['shop_name']=$args['shop_name'];
		$where['is_delete'] =$args['is_delete'];

		$model = new ShopCfgModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'shop_cfg_search_page';
		$this->render('shop_cfg_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	//恢复公司基本信息
	public function recover ()
	{
		$id = _Post::getInt('id');
		/*		var_dump($_POST);
                exit;*/
		$model = new ShopCfgModel($id,2);
		$do = $model->getDataObject();
		if(!$do['is_delete'])
		{
			$result['error'] = "当前公司没有被删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_delete',0);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "恢复失败";
		}
		Util::jsonExit($result);
	}
}

?>