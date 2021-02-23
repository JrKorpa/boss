<?php
header('Content-type: text/html; charset=utf-8');
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__))));//定义目录
include(ROOT_PATH.'/lib/PdoModel.php');
include(ROOT_PATH.'/lib/UserClassModel.php');
$userModel = new UserClassModel();
//获取所有需要清洗的数据
$orderlist = $userModel->getorderinfo();
if(empty($orderlist))
{
	return;
}
//定义入库数组
$user_data = array(
	'member_name' => '',
	'member_phone' =>'',
	'member_age' => 20,
	'member_type' => 1,
	'department_id' => 2,
	'customer_source_id' =>  544,
	'reg_time'=>time()
);
foreach($orderlist as $orderinfo)
{
	$orderid = $orderinfo['id'];
	$tel = $orderinfo['mobile'];
	if(!empty($tel))
	{
		$result = $userModel->getinfobymobile($tel);
		$userid = '';
		if(!empty($result))
		{
			$userid = $result['member_id'];
		}else{
			//创建一个用户
			$user_data['member_name'] = $orderinfo['consignee'];
			$user_data['member_phone'] = $tel;
			$addid = $userModel->adduser($user_data);
			if($addid)
			{
				$userid = $addid;
			}
		}
		//如果userid不为空
		if($userid>0)
		{
			//更新订单
			$userModel->updateuserid($orderid,$userid);
		}
	}
}
?>