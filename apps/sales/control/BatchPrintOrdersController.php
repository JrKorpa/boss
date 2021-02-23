<?php
/**
 *  -------------------------------------------------
 *   @file		: BatchPrintOrdersController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: zhangruiying
 *   @date		: 2015/4/30
 *   @update	:
 *  仓储管理-仓储单据-单据查询
 *  -------------------------------------------------
 */


class BatchPrintOrdersController extends CommonController
{
	protected $whitelist = array('exportOrder');
	public function index($params)
	{
		$this->render('BatchPrintOrders_show.html',array());
	}
	//add
	public function exportOrder($params)
	{
		$model= new BaseOrderInfoModel(27);
		$ids = array();
		if(isset($params['ids']) and !empty($params['ids']))
		{
			$ids=explode(',',$params['ids']);
		}
		foreach($ids as $key=>$id)
		{
			if(preg_match('/\D/',$id) or empty($id))
			{
				unset($ids[$key]);

			}
		}
		$ids_bat=$ids;
		$list=array();
		if(!empty($ids))
		{
			$ids="'".implode("','",$ids)."'";
			$list=$model->getExportDownload($ids);
			$detail_list=$model->getDetialByOrderId($ids);
			$region = new RegionModel(1);
			foreach($list as $key=>$v)
			{
				$list[$key]['country_name']=$region->getRegionName($v['country_id']);
				$list[$key]['provice_name']=$region->getRegionName($v['province_id']);
				$list[$key]['city_name']=$region->getRegionName($v['city_id']);
				$list[$key]['goods_detail']=isset($detail_list[$v['id']])?$detail_list[$v['id']]:array();
			}
		}
		$this->assign('list',$list);
		$this->assign('ids',json_encode($ids_bat,true));
		$this->render('order_print.html',array());
	}
	function changeOrderStatus()
	{
		$result = array('success' => 0, 'error' => '');
		$ids=_Request::get('ids');
		$ids=json_decode($ids,true);
		if(empty($ids))
		{
			$result['error']='订单号为空';
			 Util::jsonExit($result);
		}
		$ids="'".implode("','",$ids)."'";
		$model= new BaseOrderInfoModel(28);

		$res=$model->changeOrderStatus($ids);
		if($res!==false)
		{
			//添加操作日志
			$result['success']=1;
		}
		else
		{
			$result['error']='操作失败';
		}
		Util::jsonExit($result);

	}
}
?>