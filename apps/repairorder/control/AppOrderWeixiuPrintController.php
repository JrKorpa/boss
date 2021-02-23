<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderWeixiuPrintController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: liyanhong <462166422@qq.com>
 *   @date		: 2015-01-41 17:16:36
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderWeixiuPrintController extends Controller
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('prints');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_order_weixiu_print.html');
	}
	public function prints($params) 
	{
		$ids = $params["ids"];
		$ids = explode(",", $ids);
		$model = new AppOrderWeixiuModel(41);
		$result = $model->getInfoByids($ids);
		$dd = new DictView(new DictModel(1));
		//var_dump($result );exit;
		foreach ($result as $key=>$val)
		{
			$str_msg = explode(',',$val['repair_act']);
			$str = '';
			foreach ($str_msg as $k=>$val)
			{
				if($k) 
				{
					$str .= ','.$dd->getEnum('weixiu.action',$val);
				}
				else
				{
					$str .= $dd->getEnum('weixiu.action',$val);
				}
			}
			//echo $str."</br>";
			$result[$key]['repair_act'] = $str;
			//echo $key."</br>";
		}
		$this->render('app_order_weixiu_ls.html',array('res'=>$result,'num'=>count($result)));
	}
}

?>