<?php
/**
 *  -------------------------------------------------
 *   @file		: BatchGenZongController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *
 *  -------------------------------------------------
 */
class BatchGenZongController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//$SalesChannelsModel = new SalesChannelsModel(1);
        $model = new UserChannelModel(1);
        $channellist = $model->getChannels($_SESSION['userId'],0);
        //var_dump($channellist);die;
		//$channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
		$this->render('batch_gen_zong_info.html',array('channellist' => $channellist));
	}
	
	/**
	 *	update_genzong，修改跟单人
	 */
	public function update_genzong ($params)
	{
        $res = array('error'=>1,'msg'=>'');
		$create_user = _Post::getString('create_user');
        $genzong = _Post::getString('genzong');
		$channel = _Post::getInt('channel');
        if(empty($channel)){
            $res['msg'] ="请选择渠道部门！";
            Util::jsonExit($res);
        }
        if(empty($create_user)){
            $res['msg'] ="制单人为空，请重新输入";
            Util::jsonExit($res);
        }
        if(empty($genzong)){
            $res['msg'] ="跟单人为空，请重新输入";
            Util::jsonExit($res);
        }

        //验证制单人，跟单人是否存在
        $userModel = new UserModel(1);
        $check_create_user  = $userModel->hasAccount($create_user);
        if(!$check_create_user){
            //$res['msg'] ="亲~ 制单人不存在，请核实填写是否正确！";
            //Util::jsonExit($res);
        }
        $check_genzong  = $userModel->hasAccount($genzong);
        if(!$check_genzong){
            $res['msg'] ="亲~ 跟单人不存在，请核实填写是否正确！";
            Util::jsonExit($res);
        }
        //查询该渠道下的制单人是否有单据
        $orderModel= new BaseOrderInfoModel(27);
        $data = array(
            'department_id'=>$channel,
            'create_user' =>$create_user,
            'genzong' => $genzong
            );
        $ret1 = $orderModel->getCheckChannelCreateUser($data);
        if(!$ret1){
            $res['msg'] ="亲~ 该渠道和制单人没有数据！";
            Util::jsonExit($res);
        }
        $ret2 = $orderModel->BatchGenZongInfo($data);
        if($ret2 !== false){
            $res['error'] = 0;
            Util::jsonExit($res);
        }
        $res['msg'] = '更新失败！';
        Util::jsonExit($res);
	}
}

?>