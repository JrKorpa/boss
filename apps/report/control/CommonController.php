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
	protected $whitelist = array('download','downloadReport');
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

	/**
	 * 返回自己所属的渠道
	 */
	public function getMyDepartments(){
		//获取体验店的信息
		$model = new ShopCfgChannelModel(1);
		$data = $model->getallshop();
		$shops = array();
		foreach ($data as $key => $value) {
			$shops[$value['id']] = $value;
		}
		$userChannelmodel = new UserChannelModel(1);
		$data_chennel = $userChannelmodel->getChannels($_SESSION['userId'],0);
		if(empty($data_chennel)){
			die('请先联系管理员授权渠道!');
		}
		$myDepartment = array();
		foreach($data_chennel as $key => $val){
			if (!empty($shops[$val['id']])) {
				$myDepartment[$key] = $shops[$val['id']];
			}
		}
		return $myDepartment;
	}
	/**
	 *  返回两个时间段之间的天数
	 */
	public function getDatePeriod($start_time,$end_time){
		$seconds = strtotime($end_time)-strtotime($start_time);
		return $seconds/(24*3600);
	}

	// 取直营店/经销商店list
	public function getShops(){
		$shop_type = _Post::getInt('shop_type');

		//获取体验店的信息
		$model = new ShopCfgChannelModel(59);
		$data = $model->getallshop_name();
		$ret = array();
		foreach($data as $key => $val){
			if($shop_type == 1 && $val['shop_type'] == 1){
				$ret[$val['id']] = $val['shop_name'];
                $ret[163] = '总公司网销';
			}
			if($shop_type == 2 && $val['shop_type'] == 2){
				$ret[$val['id']] = $val['shop_name'];
			}
			if($shop_type == 0){
				$ret[$val['id']] = $val['shop_name'];
                $ret[163] = '总公司网销';
			}
		}

		$userChannelmodel = new UserChannelModel(59);
		$data_chennel = $userChannelmodel->getChannels($_SESSION['userId'],0);
		$myChannel="<option value=''></option>";
		foreach($data_chennel as $key => $val){
			if(!empty($ret) && array_key_exists($val['id'],$ret)){
				//$myChannel[$val['id']] = $val['channel_name'];
				$myChannel .= "<option value='".$val['id']."'>".$val['channel_name']."</option>";
			}
		}
		$result = $myChannel;
		Util::jsonExit($result);
	}
}
?>