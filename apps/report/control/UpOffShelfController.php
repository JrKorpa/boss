<?php
/**
 *  -------------------------------------------------
 *   @file		: UpOffShelfController.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  仓储管理-仓储单据-上下架
 *  -------------------------------------------------
 */


class UpOffShelfController extends Controller
{
	
	//线上库
	private $online_warehouse=array(
			'线上低值库',
			'线上钻饰库',
			'线上混合库',
			'黄金网络库',
			'主站库',
			'淘宝黄金',
			'淘宝素金',
			'京东黄金',
			'京东素金',
			'彩宝库',
			'深圳珍珠库',
			'银行库',
			'B2C库'
	);
	//线下库
	private $offline_warehouse=array(
			'婚博会备货库',
			'总公司后库',
			'总公司店面配货库',
			'黄金店面库',
	);
	//需要正常上架的库位
	private $up_shelf_warehouse=array();
	function __construct(){
		parent::__construct();
		$this->up_shelf_warehouse=array_merge($this->online_warehouse,$this->offline_warehouse);
	}
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$model = new WarehouseStatisticModel(55);
		$company   = $model->getCompanyList();//公司列表
		$this->render('warehouse_upoffshelf_search_form.html',array(
				'bar'=>Auth::getBar('index'),
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
				'warehouse_type' => _Request::get("warehouse_type"),
				'time_start'=> _Request::get("time_start"),
				'time_end'=> _Request::get("time_end"),
				'company_id'=> _Request::get("company_id"),
				'warehouse'=> _Request::getList("warehouse"),
				'warehouse_string'=>'',
				
		);
		$where = array(
				'time_start'=> $args['time_start'],
				'time_end'=>$args['time_end'],
				'company_id'=>$args['company_id'],
		);
		$page = _Request::getInt("page",1);
		$warehouse_type=$args['warehouse_type'];	
		$warehouse_in='';
		if($args['warehouse'])	{
			$where['warehouse_string']=$args['warehouse_string']='';
			foreach($args['warehouse'] as $val ){
				$args['warehouse_string'].="'{$val}',";
			}
			$where['warehouse_string']=$args['warehouse_string']=trim($args['warehouse_string'],',');
		}
		elseif($warehouse_type ){
			if($warehouse_type==1){
				$warehouse_in_array=$this->online_warehouse;
            }
			else{
                $warehouse_in_array=$this->offline_warehouse;
            }
			foreach($warehouse_in_array as $val){
				$args['warehouse_string'].="'{$val}',";
			}
			$where['warehouse_string']=$args['warehouse_string']=trim($args['warehouse_string'],',');
		}
		$model = new WarehouseStatisticModel(55);
		$data = $model->pageList($where,$page,20,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_pandian_plan_search_page';
		$this->render('warehouse_upoffshelf_search_list.html',array(
				'pa'=>Util::page($pageData),
				'page_list'=>$data,
				'args'=>$args
		));
	}
	public function index_second ($params)
	{
		$args = array(
				'mod'	=> _Request::get("mod"),
				'con'	=> substr(__CLASS__, 0, -10),
				'act'	=> __FUNCTION__,
				'warehouse_type' => _Request::get("warehouse_type"),
				'time_start'=> _Request::get("time_start"),
				'time_end'=> _Request::get("time_end"),
				'company_id'=> _Request::get("company_id"),
				'warehouse_string'=> _Request::get("warehouse_string")
		
		);
		$model = new WarehouseStatisticModel(55);
		$company   = $model->getCompanyList();//公司列表
		$this->render('warehouse_upoffshelf_search_second_form.html',array(
				'bar'=>Auth::getBar('warehouse_detail_list'),
				'dt'=>_Request::get("id"),
				'args'=>$args,
				'company'=>$company,
		));
	}
	/**
	 * 库房的明细列表
	 */
	public function warehouse_detail_list(){
		if(_Request::get("is_post")){
			if(isset($_POST['company_id'])) $company_id=$_POST['company_id'];
			else $company_id='';
			if(isset($_POST['time_start'])) $time_start=$_POST['time_start'];
			else $time_start='';
			if(isset($_POST['time_end'])) $time_end=$_POST['time_end']; 
			else $time_end='';
			if(isset($_POST['warehouse'])) $warehouse=$_POST['warehouse'];
			else $warehouse=array();
			if(isset($_POST['warehouse_type'])) $warehouse_type=$_POST['warehouse_type'];
			else $warehouse_type='';
			$warehouse_string='';
		}
		else{
			$company_id=_Request::get('company_id');
			$time_start=_Request::get('time_start');
			$time_end=_Request::get('time_end');
			$warehouse=_Request::getList('warehouse');
			$warehouse_type=_Request::get('warehouse_type');
			$warehouse_string=_Request::get("warehouse_string");
		}
		$args = array(
				'mod'	=> _Request::get("mod"),
				'con'	=> substr(__CLASS__, 0, -10),
				'act'	=> __FUNCTION__,
				'dt' => _Request::get("is_post")?'':_Request::get("dt"),
				'company_id'=>$company_id,
				'time_start'=>$time_start,
				'time_end'=>$time_end,
				'warehouse'=> $warehouse,
				'warehouse_string'=>$warehouse_string,
				'warehouse_type'=>$warehouse_type,
		);
		$page = _Request::getInt("page",1);
		$where = array(
				'dt' =>_Request::get("is_post")?'':_Request::get("dt"),
				'time_start'=> $time_start,
				'time_end'=>$time_end,
				'company_id'=>$company_id,
				'warehouse_string'=>$warehouse_string,
				'warehouse_type' => $warehouse_type,
		);
		$warehouse_type=_Request::get("warehouse_type");	
		$warehouse_in='';
		if($args['warehouse'])	{
			$where['warehouse_string']=$args['warehouse_string']='';
			foreach($args['warehouse'] as $val ){
				$val && $args['warehouse_string'].="{$val},";
			}
			$where['warehouse_string']=$args['warehouse_string']=trim($args['warehouse_string'],',');
		}
		elseif($warehouse_type && !$where['warehouse_string']){
			if($warehouse_type==1)
				$warehouse_in_array=$this->online_warehouse;
			else $warehouse_in_array=$this->offline_warehouse;
			foreach($warehouse_in_array as $val){
				$val && $args['warehouse_string'].="{$val},";
			}
			$where['warehouse_string']=$args['warehouse_string']=trim($args['warehouse_string'],',');
		}
		$url='/index.php?';
    	foreach($args as $key=> $value){
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
		$model = new WarehouseStatisticModel(55);
		$data = $model->pageList2($where,$page,20,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_pandian_plan_search_page';
		$this->render('warehouse_detail_list.html',array(
				'pa'=>Util::page($pageData),
				'page_list'=>$data,
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
				'warehouse' => _Request::get("warehouse"),
				'time_start'=> _Request::get("time_start"),
				'time_end'=> _Request::get("time_end"),
				'company_id'=> _Request::get("company_id"),
				'warehouse_type' => _Request::get("warehouse_type"),
		);
		$where = array(
				'time_start'=> $args['time_start'],
				'time_end'=>$args['time_end'],
				'company_id'=>$args['company_id'],
				'warehouse_type'=>$args['warehouse_type'],
		);
		$page = _Request::getInt("page",1);
		$warehouse_type=$args['warehouse_type'];		
		if($warehouse_type){
			$warehouse_in='';
			if($warehouse_type==1)
				$warehouse_in_array=$this->online_warehouse;
			else $warehouse_in_array=$this->offline_warehouse;
			foreach($warehouse_in_array as $val){
				$warehouse_in.="'{$val}',";
			}
			$where['warehouse_in']=trim($warehouse_in,',');
		}
		$model = new WarehouseStatisticModel(55);
		$data = $model->pageList($where,$page,999999999990,false);
		if($data['data']){
			$util=new Util();
			$title=array('日期','总库存数','上柜位数','差','超3天未上柜','总价','差价','超3天差价');
			$csv_data=array();
			if(isset($data['data']) && $data['data']){
				foreach($data['data'] as &$val){
					$temp=array();
					$temp['dt']=$val['dt'];
					$temp['total_num']=$val['total_num'];
					$temp['upshelf_num']=$val['total_num']-$val['cab_num'];
					$temp['cab_num']=$val['cab_num'];
					$temp['threeday_cab_num']=$val['threeday_cab_num'];
					$temp['all_price']=$val['all_price'];
					$temp['diff_price']=$val['diff_price'];
					$temp['threeday_diff_price']=$val['threeday_diff_price'];
						
					$csv_data[]=$temp;
				}
			}
			$util->downloadCsv(date('Y-m-d').'商品上下架统计',$title,$csv_data);
		}
	}


    public function export_cxv_details($params)
    {
        $cangku = _Request::getString('cangku');
        $dotime_string = _Request::getString('dotime_string');

        if(empty($cangku) || empty($dotime_string)){
            exit("仓库或时间参数为空!");
        }
		$model = new WarehouseKucunModel(47);
        $exists = $model->checkTableExists($dotime_string);
        if($exists){
            $util=new Util();
    		$data = $model->pageGoodsList($cangku,$dotime_string);
            $title=array('货号','柜位号','上架时间','入库时间','新入库时间');
			$util->downloadCsv(date('Y-m-d').'商品上下架统计',$title,$data);
        }else{
            exit("当天数据未备份");
        }
    }
}
