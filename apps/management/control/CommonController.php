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
				header('Location:/index.php');
				die();
			}
		}
		else
		{
			if($c!='Main')
			{
				if(!Auth::getMenuAuth($c)){
					die('无权操作');
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
    //返回这个操作权限的的去渠道数组
    public function ChannelListO(){
        if($_SESSION['userType']==1){
            return true;
        }
        $pre = '/([A-Z]{1})/';
        $res =preg_replace($pre,'_$1',$_GET['con']);
        $con =substr($res,1);
        $act = $_GET['act'];
        $act =preg_replace($pre,'_$1',$act);
        $pricheck =strtoupper($con.'_'.$act.'_O');

        $pris = $_SESSION['__operation_p'][3];

        $channelarr=array();
        foreach($pris as $key=>$val){
            if(array_key_exists($pricheck,$val)){
                $channelarr[]=$key;
            }
        }
        return $channelarr;

    }
    
    function getchannelinfo(array $channerarray){
        $channelModel = new SalesChannelsModel(1);
        return $channelModel->getSalesChannel($channerarray);
    }    
}
?>