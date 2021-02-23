<?php
/**
 *  -------------------------------------------------
 *   @file		: .php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		:
 *   @update	:
 *  -------------------------------------------------
 */
class Error
{
	protected $conf=array(
		0=>array('error_no'=>'001','msg'=>'请求非法,参数配置为空!'),
		1=>array('error_no'=>'002','msg'=>'请求非法,请求参数有误!'),
		2=>array('error_no'=>'003','msg'=>'非法操作,没有权限操作该布产单'),
		3=>array('error_no'=>'004','msg'=>'工厂操作状态错误，不在系统范围内'),
		4=>array('error_no'=>'005','msg'=>'布产状态必须为开始生产和部分生产的才能操作'),
		5=>array('error_no'=>'006','msg'=>'操作失败'),
		6=>array('error_no'=>'007','msg'=>'操作重复'),
		7=>array('error_no'=>'008','msg'=>'查询结果为空'),
		8=>array('error_no'=>'009','msg'=>'XML解析失败请检查您传送的格式是否正确'),
		9=>array('error_no'=>'010','msg'=>'工厂和出货单号不能为空'),
		10=>array('error_no'=>'011','msg'=>'工厂不能为空'),
		11=>array('error_no'=>'012','msg'=>'出货单号不能为空'),
		200=>array('error_no'=>'200','msg'=>'操作成功'),
		404=>array('error_no'=>'000','msg'=>'未定义的错误类型'),
		);
	function getErrorMessageByNO($no)
	{
		if(empty($this->conf[$no]))
		{
			return $this->conf[404];
		}
		return $this->conf[$no];
	}
}
?>