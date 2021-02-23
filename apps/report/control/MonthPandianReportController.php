<?php
/**
 *  -------------------------------------------------
 *   @file		: MonthPandianReportController.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  报表管理-运营报表-盘点报表
 *  -------------------------------------------------
 */


class MonthPandianReportController extends Controller
{
	
	function __construct(){
		parent::__construct();
	}
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$model = new WarehouseStatisticModel(55);
		$company   = $model->getCompanyList();//公司列表
		$this->render('monthpandianreport_search_form.html',array(
				'bar'=>Auth::getBar(),
				'company'=>$company,
			));
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
				'warehouse_id' => _Request::get("warehouse"),
				'time_start'=> _Request::get("time_start"),
				'time_end'=> _Request::get("time_end"),
				'to_company_id'=> _Request::get("to_company_id"),
		);
	
		$page = _Request::getInt("page",1);
		$where = array(
				'warehouse_id' => $args['warehouse_id'],
				'status_in'=> '2,3',//3是通过审核的
				'start_time'=> $args['time_start'],
				'end_time'=>$args['time_end'],
				'to_company_id'=>$args['to_company_id'],
		);
		$model = new WarehouseBillInfoWModel(55);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_pandian_plan_search_page';
		$this->render('monthpandianreport_search_list.html',array(
				'pa'=>Util::page($pageData),
				'page_list'=>$data,
		));
	}
    /**
	 * 详情列表
	 */
	public function detail_list(){
		$this->render('monthpandianreport_detail_list_search_form.html',array(
				'bar'=>Auth::getBar('detail_list'),
		));
	}
	/**
	 * 详情列表数据异步
	 */
	public function detail_list_ajax(){
		$page = _Request::getInt("page",1);
		$id=_Request::get('id');
		$id=!$id?_Request::get('bill_id'):$id;
		if(_Request::get("is_post")){
			if(isset($_POST['status'])) $status=$_POST['status'];
			else $status='';
			if(isset($_POST['goods_id'])) $goods_id=$_POST['goods_id'];
			else $goods_id='';
		}
		else{
			$status=_Request::get('status');
			$goods_id=_Request::get('goods_id');
		}
		$where = array(
				'bill_id' =>$id,
				'goods_id'=>$goods_id,
				'status'=>$status,
				'mod'		=> _Request::get("mod"),
				'con'		=> substr(__CLASS__, 0, -10),
				'act'		=> __FUNCTION__,
		);
		$url='/index.php?';
		parse_str($_SERVER["REQUEST_URI"]);
		$where['act']=$act;
		foreach($where as $key=> $value){
			if (is_array($value)) {
				foreach ($value AS $v) {
					$url .= $key . '=' . $v . '&';
				}
			} else {
				$url .= $key . '=' . $value . '&';
			}
		}
		$url=trim($url,'&');
		$_SERVER["REQUEST_URI"]=$url;
		//print_r($where);exit;
		$model = new WarehouseBillModel($id,55);
		$plan_data=array(
			'verify_date'=>$model->getValue('check_time'),
			'type'=>$model->getValue('bill_type'),
			'opt_date'=>$model->getValue('create_time'),
		);
		$data = $model->get_detail_goods_list($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = array();
		$pageData['jsFuncs'] = 'weekpandianreport_detail_search_page';
		$this->render('monthpandianreport_detail_search_list.html',array(
				'pa'=>Util::page($pageData),
				'page_list'=>$data,
				'page_data'=>$plan_data
		));
	}
	/**
	 * 导出第一层报表
	 */
	public function export_cxv_index(){
		$args = array(
				'mod'	=> _Request::get("mod"),
				'con'	=> substr(__CLASS__, 0, -10),
				'act'	=> __FUNCTION__,
				'warehouse_id' => _Request::get("warehouse"),
				'time_start'=> _Request::get("time_start"),
				'time_end'=> _Request::get("time_end"),
				'to_company_id'=> _Request::get("to_company_id"),
		);
	
		$page = _Request::getInt("page",1);
		$where = array(
				'warehouse_id' => $args['warehouse_id'],
				'status_in'=> '2,3',//3是通过审核的
				'start_time'=> $args['time_start'],
				'end_time'=>$args['time_end'],
				'to_company_id'=>$args['to_company_id'],
		);
		$model = new WarehouseBillInfoWModel(55);
		$data = $model->pageList($where,$page,1000000000,false);
		$util=new Util();
		$title=array('月盘点单号','审核时间','仓库','应盘','金额','实盘','金额	','正常','金额','盘盈','金额','盘亏','金额','盘点准确率','盘亏占比','盘盈占比');
		$csv_data=array();
		if($data['data']){
			if(isset($data['data']) && $data['data']){
				foreach($data['data'] as &$val){
					$temp=array();
					$temp['bill_no']=$val['bill_no'];
					$temp['opt_date']=$val['opt_date'];
					$temp['to_warehouse_name']=$val['to_warehouse_name'];
					$temp['all_num']=$val['all_num'];
					$val['all_price']=isset($val['all_price']) && $val['all_price']?$val['all_price']:0;
					$temp['all_price']=$val['all_price'];
					$temp['real_num']=$val['real_num'];
					$temp['real_price']=$val['real_price'];
					$temp['nomal_num']=$val['nomal_num'];
					$temp['nomal_price']=$val['nomal_price'];
					$temp['overage_num']=$val['overage_num'];
					$temp['overage_price']=$val['overage_price'];
					$temp['loss_num']=$val['loss_num'];
					$temp['loss_price']=$val['loss_price'];
					$temp['accurate_rate']=$val['accurate_rate']."%";
					$temp['loss_rate']=$val['loss_rate']."%";
					$temp['overage_rate']=$val['overage_rate']."%";
					$csv_data[]=$temp;
				}
			}
		}
		$util->downloadCsv(date('Y-m-d').'月盘点统计',$title,$csv_data);
	}
}
