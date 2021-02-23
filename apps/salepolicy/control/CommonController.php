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
				Route::setAsAutoLaunch($_SERVER['REQUEST_URI'], _Request::getString("title", "销售政策管理"));
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

    //校验不同渠道的不同权限的方法  给出当前渠道id 校验是否有操作权限
//有权限返回true 没有权限返回权限名称

    public function checkSession($channelid){
        // print_r($_SESSION['__operation_p']);
        if($_SESSION['userType']==1){
            return true;
        }
        $pre = '/([A-Z]{1})/';
        $res =preg_replace($pre,'_$1',$_GET['con']);
        $con =substr($res,1);
        $act = $_GET['act'];
        $act =preg_replace($pre,'_$1',$act);
        $pricheck =strtoupper($con.'_'.$act.'_O');
        $sql = "SELECT `name` FROM `permission` WHERE code='".$pricheck."'";
        //这里会有找不到权限的情况 所以当你要鉴定权限的时候请先保证这个操作是管控的
        $res = DB::cn(1)->getOne($sql);

        //判断所传渠道是否在本人有权限的渠道内存在。没有，则提示
        $channellist = $this->ChannelListO();
        if(!in_array($channelid,$channellist))
        {
            return $res;
        }
        //判断是否有权限
        $pris = array_flip($_SESSION['__operation_p'][3][$channelid]);
        if(in_array($pricheck,$pris)){
            return true;
        }
        return $res;
    }


    function getchannelinfo(array $channerarray){
        $channelModel = new SalesChannelsModel(1);
        return $channelModel->getSalesChannel($channerarray);
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

	public function index ($params){
		die('forbidden');
	}
}
?>