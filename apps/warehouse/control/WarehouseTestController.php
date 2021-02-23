<?php
/**
 *  -------------------------------------------------
 *   @file		: TestController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: JUAN <82739364@qq.com>
 *   @date		: 2015-01-14 11:04:15
 *   @update	:
 *  仓储管理-仓储单据-单据查询
 *  -------------------------------------------------
 */


class WarehouseTestController extends CommonController
{
	protected $whitelist = array('import_company','import_warehouse');
	public function index($params)
	{
		$this->render('warehouse_dao_show.html',array());
	}
	//add
	public function import_goods($params)
	{
		try{
		$result = array('success' => 0,'error' =>'');
		$where = array('page' => 0);
		$api = new ImportDataModel(21);
                $ids = array();
		if(!empty($params))
		{
			$ids=preg_split("/\n/",$params['goods_ids']);
		}
		$arr=array();

		foreach($ids as $id)
		{
			$goods_id=preg_replace('/\D/','',$id);
			if(empty($goods_id))
			{
                                continue;
			}
            $where['goods_id']=$goods_id;

			$ret=ApiModel::jxc_api('RequestJxcGoodsPage',$where);
			$goods_list = $ret['return_msg']['list'];
			if(!count($goods_list))
			{
				continue;
			}
			if(empty($arr))
			{
				$arr=$goods_list;
			}
			else
			{
				$arr=array_merge($arr,$goods_list);
			}

		}
		if(empty($arr))
		{
			$result['success'] = '0';
			$result['error'] = '请检查导入的数据是否上架或已在系统中存在!';
		}
		else
		{
			if($api->addGoodsData($arr))
			{
				$result['success'] = '1';
			}
			else
			{
				$result['success'] = '0';
				$result['error'] = '导入失败';
			}
		}
		}catch(Exception $e){
			Util::L($e,'zhangruiying001.txt');
		}
		Util::jsonExit($result);
	}


	public function import_company()
	{
		$result = array('success' => 0,'error' =>'');
		$ret=ApiModel::jxc_api('RequestJxcProList',array());
		$list = $ret['return_msg']['list'];

		$api = new ImportDataModel(1);
		if($api->addCompanyData($list))
		{
			echo '公司信息导入成功';exit;
		}
		echo '公司信息导入失败';exit;
	}

	public function import_warehouse()
	{
		$result = array('success' => 0,'error' =>'');
		$ret=ApiModel::jxc_api('RequestJxcWarehouse',array());
		$list = $ret['return_msg']['list'];

		$api = new ImportDataModel(21);
		if($api->addWarehouseData($list))
		{
			echo '仓库信息导入成功';exit;
		}
		echo '仓库信息导入失败';exit;
	}
}
?>