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
    protected $gifts = array(1=>'赠送珍珠耳钉',2=>'赠送S925银链',3=>'赠送黑皮绳',4=>'赠送红玛瑙手链',5=>'赠送白色手提袋',6=>'赠送情人节礼盒',7=>'赠送红绳',8=>'赠送手绳',9=>'赠送砗磲手链',10=>'赠送粉晶手链',11=>'赠送金条红包0.02g',12=>'赠送首饰盒',13=>'耳堵');
	protected function init ($act,$c) 
	{
		if(!Util::isAjax())
		{
			if(!in_array($act,$this->whitelist))
			{
				Route::setAsAutoLaunch($_SERVER['REQUEST_URI'], _Request::getString("title", "财务管理"));
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