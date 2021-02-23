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
class AppStonePurchaseController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('downCsv');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$processorModel = new ApiProcessorModel();
		$process_list = $processorModel->GetSupplierList();
		$this->render('app_stone_search_form.html',array(
				'bar'=>Auth::getBar(),
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
			'type' => 3,
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
			'item_type' => _Request::get('print')==1?(isset($_POST['item_type'])?implode(',',$_POST['item_type']):''): _Request::get('item_type'),
			'storage_mode' => _Request::get('print')==1?(isset($_POST['storage_mode'])?implode(',',$_POST['storage_mode']):''): _Request::get('storage_mode')
			);
		$where = $this->getWhere($args);
		$model = new GoodsModel(29);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_stone_detail_search_page';
		$this->render('app_stone_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'dd'=>new DictView(new DictModel(1)),
		));
	}

	function getWhere($args)
	{
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
						//若 单据号中间存在空格 汉字逗号 替换为英文模式逗号
						$where['item_id'] = str_replace(' ',',',$where['item_id']);
						$where['item_id'] = str_replace('，',',',$where['item_id']);
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
                        $where['item_id'] = $itemnum;
		}
		if($args['zhengshuhao'])
		{
			$where['zhengshuhao'] = $args['zhengshuhao'];
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
			'type' => 3,
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
		$data = $model->getPrintAll($where);
		$title = array('流水号','单据编号','单据类型','入库制单时间','入库审核时间','纸质单号','供货商/结算商','单据金额','应付申请状态','应付申请单号');
		if (is_array($data)){
		   foreach($data as $k=>$v)
		   {
				$v['item_type'] = $dictModel->getEnum('PayApply.stone_type',$v['item_type']);
				$v['pay_apply_status'] = $dictModel->getEnum('app_pay.detail_apply_status',$v['pay_apply_status']);
				$val = array($v['serial_number'],$v['item_id'],$v['item_type'],$v['make_time'],$v['check_time'],$v['prc_num'],$v['prc_name'],$v['total'],$v['pay_apply_status'],$v['pay_apply_number']);

				$val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
				$content[] = $val;
		   }
		}
		$model->detail_csv('石包采购明细',$title,$content);
	}

}

?>