<?php
/**
 *  -------------------------------------------------
 *   @file		: WeekPandianReportController.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  报表管理-运营报表-盘点报表
 *  -------------------------------------------------
 */


class WeekPandianReportController extends Controller
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
		$this->render('weekpandianreport_search_form.html',array(
				'bar'=>Auth::getBar(),
				'company'=>$company,
			));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$page = _Request::getInt("page",1);
		$where = array(
			'id' => '',
			'type' =>_Request::get('type'),
			'status_in'=> '3',//3是通过审核的
			'opt_admin' => '',
			'verify_admin' => '',
			'create_time_start' => '',
			'create_time_end' => '',
			'start_time_start' => _Request::get('time_start'),
			'start_time_end' => _Request::get('time_end'),
		);
		$model = new WarehousePandianPlanModel(55);
		$data = $model->pageList2($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = array();
		$pageData['jsFuncs'] = 'warehouse_pandian_plan_search_page';
		$this->render('weekpandianreport_search_list.html',array(
				'pa'=>Util::page($pageData),
				'page_list'=>$data,
		));
	}
	/**
	 * 详情列表
	 */
	public function detail_list(){
		$this->render('weekpandianreport_detail_list_search_form.html',array(
				'bar'=>Auth::getBar('detail_list'),
		));
	}
	/**
	 * 详情列表数据异步
	 */
	public function detail_list_ajax(){
		$page = _Request::getInt("page",1);
		$id=_Request::get('id');
		$id=!$id?_Request::get('plan_id'):$id;
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
				'plan_id' =>$id,
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
		$model = new WarehousePandianPlanModel($id,55);
		$plan_data=array(
			'verify_date'=>$model->getValue('verify_date'),
			'type'=>$model->getValue('type'),
			'opt_date'=>$model->getValue('opt_date'),
		);
		$data = $model->get_detail_goods_list($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = array();
		$pageData['jsFuncs'] = 'weekpandianreport_detail_search_page';
		$this->render('weekpandianreport_detail_search_list.html',array(
				'pa'=>Util::page($pageData),
				'page_list'=>$data,
				'page_data'=>$plan_data
		));
	}
	/**
	 * 导出第一层报表
	 */
	public function export_cxv_index(){
		$page = _Request::getInt("page",1);
		$where = array(
			'id' => '',
			'type' =>_Request::get('type'),
			'status_in'=> '3',//3是通过审核的
			'opt_admin' => '',
			'verify_admin' => '',
			'create_time_start' => '',
			'create_time_end' => '',
			'start_time_start' => _Request::get('time_start'),
			'start_time_end' => _Request::get('time_end'),
		);
		$model = new WarehousePandianPlanModel(55);
		$data = $model->pageList2($where,$page,9999999999990,false);
		$util=new Util();
		$csv_data=array();
		$title=array('周盘点单号','审核时间','仓库','应盘','金额','实盘','金额','正常','金额','盘盈	','金额','盘亏','金额','盘点准确率','盘亏占比','盘盈占比');
		if($data['data']){
			if(isset($data['data']) && $data['data']){
				foreach($data['data'] as &$val){
					$temp=array();
					$temp['id']=$val['id'];
					$temp['opt_date']=$val['opt_date'];
					if($val['type']==1)
					$temp['type']='线上库';
					else $temp['type']='线下库';
					$temp['all_num']=$val['all_num'];
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

		$util->downloadCsv(date('Y-m-d').'周盘点率统计',$title,$csv_data);
	}
}
