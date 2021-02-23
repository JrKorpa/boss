<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouserelController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 18:48:59
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouserelController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('warehouse_rel');	//生成模型后请注释该行
		//Util::V('warehouse_rel');	//生成视图后请注释该行
		$company_model = new CompanyModel(1);
		$company_info = $company_model -> getCompanyTree();
		$this->assign('company_info',$company_info);//公司
		$this->render('warehouse_rel_search_form.html',array(
				'bar'=>Auth::getBar('WAREHOUSEREL_LIST_M')
		));
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
			'name' => _Request::get('name'),
			'code' => _Request::get('code'),
			'is_delete' => _Request::get('is_delete'),
			'company_id' => _Request::get('company_id'),

		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();

		$where['name'] = $args['name'];
		$where['code'] = $args['code'];
		$where['is_delete'] = $args['is_delete'];
		$where['company_id'] = $args['company_id'];
		$model = new WarehouseRelModel(21);
		$data = $model->pageList($where,$page,10,false);

		$company_model = new CompanyModel(1);
		foreach($data['data'] as $k => $row){
			$data['data'][$k]['company_name'] = $company_model -> getCompanyName($row['company_id']);
		}

		$pageData = $data;
		$pageData['filter'] = $args;

		$pageData['jsFuncs'] = 'warehouse_rel_search_page';
		$this->render('warehouse_rel_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}
}

?>