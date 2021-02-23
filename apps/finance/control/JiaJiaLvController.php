<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoYJiajialvController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-11-26 12:03:08
 *   @update	:
 *  -------------------------------------------------
 */
class JiaJiaLvController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//业务一定要放在财务菜单下，实际上此功能属于warehousr模块下，无法剥离到财务下，所以跳转过去
		$this->redirect("location:/index.php?mod=warehouse&con=WarehouseBillInfoYJiaJiaLv&act=index");
	}

}

?>