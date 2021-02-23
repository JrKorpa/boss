<?php
/**
 *  -------------------------------------------------
 *   @file		: CommonController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
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
				Route::setAsAutoLaunch($_SERVER['REQUEST_URI'], _Request::getString("title", "采购管理"));
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

    /**
     * 替换刻字特殊字符串
     * @param type $kezi
     * @return string
     */
    public function replaceTsKezi($kezi='')
    {
        if($kezi!=''){

            //替换刻字特殊字符串
            $kezi = str_replace('a01','\\',$kezi);
            $kezi = str_replace('a02','\'',$kezi);
            $kezi = str_replace('a03','"',$kezi);
        }
        return $kezi;
    }
    
	public function index ($params){
		die('forbidden');
	}
}
?>