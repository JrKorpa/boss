<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseLogController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-20 18:41:46
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseLogController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('purchase_log');	//生成模型后请注释该行
		//Util::V('purchase_log');	//生成视图后请注释该行
		//$this->render('purchase_log_search_form.html');
	}


	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$model = new PurchaseLogModel($id,23);

		$result['content'] = $this->fetch('purchase_log_show.html',array(
			'list' => $model->getLog($id),
			'dd' => new DictView(new DictModel(1))
		));
		$result['title'] = '日志详情';
		Util::jsonExit($result);
	}

	
}

?>