<?php
/**
 *  -------------------------------------------------
 *   @file		: CommonController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-03
 *   @update	:
 *  -------------------------------------------------
 */
class CommonController extends Controller 
{
	protected $whitelist = array();
	protected function init ($act,$c) 
	{
		if(!Util::isAjax())
		{
			if(!in_array($act,$this->whitelist))
			{
				Route::setAsAutoLaunch($_SERVER['REQUEST_URI'], _Request::getString("title", "赠品管理"));
				header('Location:/index.php');
				die();
			}
		}
		else
		{
			if($c!='Main')
			{
				if(!Auth::getMenuAuth($c)){
					die('没有菜单权限');
				}
				if(!Auth::getOperationAuth($c,$act))
				{
					die('没有操作权限');
				}
			}
		}
		$this->dd = new DictView(new DictModel(1));
		$this->assign('dd',$this->dd);//数据字典
                
	}
	public function index ($params){
		die('forbidden');
	}
}
?>