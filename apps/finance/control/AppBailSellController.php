<?php
/**
 *  -------------------------------------------------
 *   @file		: GoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-08 15:15:41
 *   @update	:
 *  -------------------------------------------------
 */
class AppBailSellController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('search','downCsv');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
	    $payApplyList = (new PayApplyModel(29))->getCatTypeList();
		$processorModel = new ApiProcessorModel();
		$process_list = $processorModel->GetSupplierList();
		$this->render('app_bail_sell_search_form.html',array(
				'bar'=>Auth::getBar(),
		         'payApplyList'=>$payApplyList,
				'dd' => new DictView(new DictModel(1)),
				'process_list' => $process_list
			)
		);
	}
	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'type' => 1,
		    'download'=>_Request::get("download"),
			'company' => _Request::get('company'),
			'prc_id'	=> _Request::get('prc_id'),
			'prc_num'	=> _Request::get('prc_num'),
// 			'goods_status' => _Request::get('goods_status'),
// 			'goods_status' => 2,//0=初始化，1=库存，2=已销售，3=转仓中，4=盘点中，5=销售中，6=冻结，7=已返厂,8=退货中，9=返厂中, 10=作废, 11=损益中,12=已报损
			'pay_apply_status' => _Request::get('pay_apply_status'),//应付申请状态（1=>待申请，2=>待审核，3=>已驳回，4=>已审核）
// 			'pay_apply_status' => 4,//应付申请状态（1=>待申请，2=>待审核，3=>已驳回，4=>已审核）
			'pay_apply_number' => _Request::get('pay_apply_number'),
			'serial_number' => _Request::get('serial_number'),
			'item_id'	=> _Request::get('item_id'),
			'zhengshuhao'=>_Request::get('zhengshuhao'),
			'make_time_start' => _Request::get('make_time_start'),
			'make_time_end' => _Request::get('make_time_end'),
			'check_time_start' => _Request::get('check_time_start'),
			'check_time_end' => _Request::get('check_time_end'),
			'item_type' => _Request::get('item_type'),
			'storage_mode' => _Request::get('storage_mode')
			);

		$where = $this->getWhere($args);
		$model = new GoodsModel(29);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$dictModel = new DictModel(1);

		//获取所有不需要显示结算商的供应商id
		$filter_product = array(
			382		=> '石料管理',
			383		=> '内部转仓',
			384		=> '拆货管理员',
			378		=> '盘盈入库',
			366		=> '入库成本尾差',
		);
		$filter_product_id = array_keys($filter_product);
		$filter_name = '委外';
		$processorModel = new ApiProcessorModel();
		$weiwai_product_ids = $processorModel->GetSupplierIdsByName(array('name'), array($filter_name));
		$weiwai_product_ids = $this->getSubByKey($weiwai_product_ids, 'id');
		$filter_product_id = array_merge($filter_product_id, $weiwai_product_ids);
		
		$where['filter_product_id'] = $filter_product_id;

        $where['filter_storage_mode'] = [3,4];
        
        if(isset($args['download']) && 'download' == $args['download']){
            $data = $model->pageList($where, 1,9999999999);
            $this->downloadCsv($data['data']);
            exit();
        }
        $warehouseModel = new SelfWarehouseModel(21);
        
		$data = $model->pageList($where,$page,10,false);
		if (is_array($data['data'])){
    		$dictModel = new DictModel(1);
            foreach($data['data'] as $k=>$v)
            {           	
               // $goodsInfo = ApiModel::warehouse_api(array("goods_id"), array($v['item_id']), "GetWarehouseGoodsByGoodsid");
            	$goodsInfo = $warehouseModel->getWarehouseGoodsByGoodsid($v['item_id']);
            	$is_on_sale=$goodsInfo['is_on_sale'];
            	$data['data'][$k]['goods_status_note'] = $dictModel->getEnum('warehouse.goods_status',$is_on_sale);
            }
        }
		//print_r($data);
		
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_bail_sell_detail_search_page';
		$this->render('app_bail_sell_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'dd'=>new DictView($dictModel),
		));
	}

	//按数组键名取值拼成新的数组
	function getSubByKey($arr, $findKey){
		if (is_array($arr) && !empty($arr)){
			$res = array();
			foreach ($arr as $key => $value) {
				if (!empty($value[$findKey])){
					$res[] = $value[$findKey];
				}
			}
			return $res;
		}
	}


	function getWhere($args){
		$where = array();
		if($args['company'])
		{
			$where['company'] = $args['company'];
		}
		if($args['prc_id'])
		{
			$where['prc_id'] = $args['prc_id'];
		}
		if($args['goods_status'])
		{
			$where['goods_status'] = $args['goods_status'];
		}
		if($args['pay_apply_status'])
		{
			$where['pay_apply_status'] = $args['pay_apply_status'];
		}
		if($args['storage_mode'])
		{
			$where['storage_mode'] = $args['storage_mode'];
		}
		if($args['pay_apply_number'])
		{
			$where['pay_apply_number'] = $args['pay_apply_number'];
		}
		if($args['serial_number'])
		{
			$where['serial_number'] = $args['serial_number'];
		}
		if($args['item_id'])
		{
			$where['item_id'] = $args['item_id'];
						
						$where['item_id']=str_replace("，",',',$where['item_id']);
						$where['item_id']=str_replace(" ",',',$where['item_id']);
                        $item =explode(",",$where['item_id']); 
                        $itemnum = "";
                        foreach($item as $key => $val) {
                            if ($val != '') {
                                if($itemnum){
                                    $itemnum .= ",'".trim($val)."'";
                                }else{
                                    $itemnum .= "'".trim($val)."'";
                                }
                            }
                        }
                        $where['item_id'] = null;
                        $where['goods_ids'] = $itemnum;			
		}






		if($args['zhengshuhao'])
		{
			$where['zhengshuhao'] = $args['zhengshuhao'];						
						$where['zhengshuhao']=str_replace("，",',',$where['zhengshuhao']);
						$where['zhengshuhao']=str_replace(" ",',',$where['zhengshuhao']);
                        $item =explode(",",$where['zhengshuhao']); 
                        $itemnum = "";
                        foreach($item as $key => $val) {
                            if ($val != '') {
                                if($itemnum){
                                    $itemnum .= ",'".trim($val)."'";
                                }else{
                                    $itemnum .= "'".trim($val)."'";
                                }
                            }
                        }
                        $where['zhengshuhao'] = $itemnum;
		}
		if($args['make_time_start'])
		{
			$where['make_time_start'] = $args['make_time_start'];
		}
		if($args['make_time_end'])
		{
			$where['make_time_end'] = $args['make_time_end'];
		}
		if($args['check_time_start'])
		{
			$where['check_time_start'] = $args['check_time_start'];
		}
		if($args['check_time_end'])
		{
			$where['check_time_end'] = $args['check_time_end'];
		}
		if($args['prc_num'])
		{
		    $where['prc_num'] = $args['prc_num'];

		}
		if($args['item_type'])
		{
			$where['item_type'] = $args['item_type'];
		}
		$where['type'] = $args['type'];
		return $where;
	}

   


	function downCsv()
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'type' => 1,
			'company' => _Request::get('company'),
			'prc_id'	=> _Request::get('prc_id'),
			'prc_num'	=> _Request::get('prc_num'),
			'goods_status' => _Request::get('goods_status'),
			'pay_apply_status' => _Request::get('pay_apply_status'),
			'pay_apply_number' => _Request::get('pay_apply_number'),
			'serial_number' => _Request::get('serial_number'),
			'item_id'	=> _Request::get('item_id'),
			'zhengshuhao'=>_Request::get('zhengshuhao'),
			'make_time_start' => _Request::get('make_time_start'),
			'make_time_end' => _Request::get('make_time_end'),
			'check_time_start' => _Request::get('check_time_start'),
			'check_time_end' => _Request::get('check_time_end'),
			'item_type' => _Request::get('item_type'),
			'storage_mode' => _Request::get('storage_mode')
			);

		$where = $this->getWhere($args);
		$model = new GoodsModel(29);
		$dictModel = new DictModel(1);
		$warehouseModel = new SelfWarehouseModel(21);
		$data = $model->getPrintAll($where);
		$title = array('流水号','货号','证书号','分类','入库制单时间','入库审核时间','入库方式','货品状态','供货商/结算商','采购成本','应付申请状态','应付申请单号');
		if (is_array($data)){
		   foreach($data as $k=>$v)
		   {
		       //$goodsInfo = ApiModel::warehouse_api(array("goods_id"), array($v['item_id']), "GetWarehouseGoodsByGoodsid");
		       $goodsInfo = $warehouseModel->getWarehouseGoodsByGoodsid($v['item_id']);
		   	   $is_on_sale=$goodsInfo['is_on_sale'];
		       $v['goods_status'] = $dictModel->getEnum('warehouse.goods_status',$is_on_sale);
			   //$v['goods_status'] = $dictModel->getEnum('warehouse.goods_status',$v['goods_status']);
			   $v['pay_apply_status'] = $dictModel->getEnum('app_pay.detail_apply_status',$v['pay_apply_status']);
			   $v['storage_mode'] = $dictModel->getEnum('warehouse.put_in_type',$v['storage_mode']);
				$val = array($v['serial_number'],$v['item_id'],$v['zhengshuhao'],$v['item_type'],$v['make_time'],$v['check_time'],$v['storage_mode'],$v['goods_status'],$v['prc_name'],$v['total'],$v['pay_apply_status'],$v['pay_apply_number']);
				$val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
				$content[] = $val;
		   }
		}
		$model->detail_csv('代销借货明细',$title,$content);
	}
	

	private function changeArrKey($array,$key,$value) {
	    $result = array();
	    foreach ($array as $tmp){
	        $result[$tmp[$key]] = $tmp[$value];
	    }
	    return $result;
	}
	private function downloadCsv($data)
	{
	    $model = new GoodsModel(29);
	    $dictModel = new DictModel(1);
	    $warehouseModel = new SelfWarehouseModel(21);
	    $title = array('流水号','货号','证书号','款式分类','入库制单时间','入库审核时间','入库方式','货品状态','供货商/结算商','采购成本','应付申请状态','应付申请单号','最终销售价');
	    if (is_array($data)){
	        $goods_status = $this->changeArrKey($dictModel->getEnumArray('warehouse.goods_status'), 'name', 'label');
	        $detail_apply_status = $this->changeArrKey($dictModel->getEnumArray('app_pay.detail_apply_status'), 'name', 'label');
	        $put_in_type = $this->changeArrKey($dictModel->getEnumArray('warehouse.put_in_type'), 'name', 'label');
	        foreach ($data as $k=>$v){
	        	$goodsInfo = $warehouseModel->getWarehouseGoodsByGoodsid($v['item_id']);
	        	$is_on_sale=$goodsInfo['is_on_sale'];
                $v['shijia'] = '';
                if($v['storage_mode']=='3')
	        	    $v['shijia'] = $model->getSalePrice($v['item_id']);
	        	$v['goods_status'] = $goods_status[$is_on_sale];
	            $v['pay_apply_status'] = $detail_apply_status[$v['pay_apply_status']];
	            $v['storage_mode'] = $put_in_type[$v['storage_mode']];
	            $val = array($v['serial_number'],$v['item_id'],$v['zhengshuhao'],$v['item_type'],$v['make_time'],$v['check_time'],$v['storage_mode'],$v['goods_status'],$v['prc_name'],$v['total'],$v['pay_apply_status'],$v['pay_apply_number'],$v['shijia']);
	            $val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
	            $content[] = $val;
	        }
	    }
	    $model->detail_csv('代销借货明细',$title,$content);
	}
	
   
    

}

?>