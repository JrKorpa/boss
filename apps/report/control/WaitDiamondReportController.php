<?php
/**
 *  -------------------------------------------------
 *  等钻时长报表
 *   @file		: WaitDiamondReportController.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  -------------------------------------------------
 */
class WaitDiamondReportController extends CommonController
{
	function __construct(){
		parent::__construct();
		set_time_limit(0);
        $dd = new DictModel(1);
        $qiban_type = $dd->getEnumArray("qiban_type");
        $this->assign('qiban_type',$qiban_type);
	}
	
	public function getData()
	{
        $from_type=_Request::get('from_type');
        $diamond_type=_Request::get('diamond_type');
        $qiban_type=_Request::get('qiban_type');
        $style_sn=_Request::get('style_sn');
        $prc_name=_Request::get('prc_name');
        $start_time=_Request::get('start_time');
        $end_time=_Request::get('end_time');
		$args = array(
				'mod'	=> _Request::get("mod"),
				'con'	=> substr(__CLASS__, 0, -10),
				'act'	=> __FUNCTION__,
				'from_type'=>$from_type,
				'diamond_type'=>$diamond_type,
				'qiban_type'=>$qiban_type,
				'opra_unames'=>_Request::getList("opra_unames"),
				'opra_uname_string'=>_Request::get("is_post")?'':trim(_Request::get("opra_uname_string")),
				'style_sn'=>$style_sn,
				'prc_name'=>$prc_name,
				'prc_ids_string'=>_Request::get("is_post")?'':trim(_Request::get("prc_ids_string")),
				'prc_ids'=>_Request::getList("prc_ids"),
				'start_time'=>$start_time,
				'end_time'=>$end_time,
		);
		$url='/index.php?';
		parse_str($_SERVER["REQUEST_URI"]);
		$args['act']=$act;
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
		$_SERVER["REQUEST_URI"]=$url;//清空REQUEST_URI中get时传递过来的参数，$_SERVER["REQUEST_URI"]在Util.class.php分页page方法中有用到
		if($args['opra_unames'] && empty($args['opra_uname_string'])){
			foreach ($args['opra_unames'] as $val){
				$args['opra_uname_string'].="{$val},";
			}
			$args['opra_uname_string']=trim($args['opra_uname_string'],',');
		}
		if($args['prc_ids']  && empty($args['prc_ids_string'])){
			foreach ($args['prc_ids'] as $val){
				$args['prc_ids_string'].="{$val},";
			}
			$args['prc_ids_string']=trim($args['prc_ids_string'],',');
		}
		
		
		
		return $args;
	
	}
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        //获取工厂
		$gen_list=array();
		$facmodel = new AppProcessorInfoModel(53);
	//	$process = $facmodel->getProList();
		$process = $facmodel->getAllProList();
		//获取跟单人
		$gendanModel = new ProductFactoryOprauserModel(53);
		$gen_list = $gendanModel->select2($fields = ' distinct(`opra_uname`)' , $where = ' 1 ' , $type = 'all');
		$this->render('wait_diamond_search_form.html',array(
            'bar'=>Auth::getBar(),
        	'process' => $process,
			'user_list'=>$gen_list,
            )
        );
	}
	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args=$this->getData();
		$page = _Request::getInt("page",1);
        $page_num = (_Post::getInt('page_num'))?_Post::getInt('page_num'):300;
		$model = new ProductInfoModel(53);
		//print_r($args);exit;
		$data = $model->page_Avg_wait_diamond_List($args,$page,$page_num,false);
        $tongji = array();
        $tongji['buchan_num'] = 0;
        $tongji['sum_day'] = 0;
        $tongji['avg_time'] = 0;
        foreach($data as $key => $val){
            $tongji['buchan_num'] += $val['buchan_num'];
            $tongji['sum_day'] += $val['sum_day'];
            $data[$key]['avg_time'] = $val['buchan_num']>0?$val['sum_day']/$val['buchan_num']:0;
        }
        $tongji['avg_time'] = $tongji['buchan_num']>0?$tongji['sum_day']/$tongji['buchan_num']:0;

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'wait_diamond_search_page'; 
		
		$this->render('wait_diamond_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'args'=>$args,
			'tongji'=>$tongji
		));
   }
   /**
    *	index_second，搜索框
    */
   public function index_second ($params)
   {
   		//获取工厂
		$gen_list=array();
		$facmodel = new AppProcessorInfoModel(53);
		//	$process = $facmodel->getProList();
		$process = $facmodel->getAllProList();
		//获取跟单人
		$gendanModel = new ProductFactoryOprauserModel(53);
		$gen_list = $gendanModel->select2($fields = ' distinct(`opra_uname`)' , $where = ' 1 ' , $type = 'all');
		$args=$this->getData();
	   	$this->render('wait_diamond_search_form_second.html',array(
	   			'bar'=>Auth::getBar(),
	   			'args'=>$args,
	   			'process' => $process,
				'user_list'=>$gen_list,
	   	));
   }
   /**
    *	search，列表
    */
   public function search_second ($params)
   {
   	$args=$this->getData();
   	$page = _Request::getInt("page",1);
   	$page_num = (_Post::getInt('page_num'))?_Post::getInt('page_num'):20;
   	$model = new ProductInfoModel(53);
   	//print_r($args);exit;
   	$data = $model->page_Avg_wait_diamond_detail_List($args,$page,$page_num,false);
   	if($data['data']){
        foreach($data['data'] as $key => $val){
            $data['data'][$key]['avg_time'] = $val['buchan_num']>0?$val['sum_day']/$val['buchan_num']:0;
        }
   	}
   	$pageData = $data;
   	$pageData['filter'] = $args;
   	$pageData['jsFuncs'] = 'wait_diamond_search_page_second';
   
   	$this->render('wait_diamond_search_list_second.html',array(
   			'pa'=>Util::page($pageData),
   			'page_list'=>$data,
   			'args'=>$args
   	));
   }
   /**
    *	index_third，搜索框
    */
   public function index_third ($params)
   {
   		//获取工厂
		$gen_list=array();
		$facmodel = new AppProcessorInfoModel(53);
		//	$process = $facmodel->getProList();
		$process = $facmodel->getAllProList();
		//获取跟单人
		$gendanModel = new ProductFactoryOprauserModel(53);
		$gen_list = $gendanModel->select2($fields = ' distinct(`opra_uname`)' , $where = ' 1 ' , $type = 'all');
		$args=$this->getData();
	   	$this->render('wait_diamond_search_form_third.html',array(
	   			'bar'=>Auth::getBar(),
	   			'args'=>$args,
	   			'process' => $process,
				'user_list'=>$gen_list,
	   	));
   }
   /**
    *	search_third，列表
    */
   public function search_third ($params)
   {
   	$args=$this->getData();
   	$page = _Request::getInt("page",1);
   	$page_num = (_Post::getInt('page_num'))?_Post::getInt('page_num'):20;
   	$model = new ProductInfoModel(53);
   	//print_r($args);exit;
   	$data = $model->page_Avg_wait_diamond_detail_third_List($args,$page,$page_num,false);
   	$pageData = $data;
   	$pageData['filter'] = $args;
   	$pageData['jsFuncs'] = 'wait_diamond_search_page_third';
   
   	$this->render('wait_diamond_search_list_third.html',array(
   			'pa'=>Util::page($pageData),
   			'page_list'=>$data,
   			'args'=>$args
   	));
   }
   /**
	 * 导出第一层报表
	 */
	public function export_cxv_index(){
		$args=$this->getData();
		$page = _Request::getInt("page",1);
		$model = new ProductInfoModel(53);
		$data = $model->page_Avg_wait_diamond_List($args,$page,99999999999999,false); 
		if($data['data'] && $data['detail_data']){
			foreach ($data['data'] as & $val){
				$count_date=$val['count_date'];
				if(isset($data['detail_data'][$count_date]) && isset($data['detail_data'][$count_date]['avg_wait_diamond_time']))
					$val['avg_wait_diamond_time']=$data['detail_data'][$count_date]['avg_wait_diamond_time'];
				else $val['avg_wait_diamond_time']=0;
			}
		}
		if($data['data']){
			$util=new Util();
			$title=array('日期','送钻总数量','等钻平均时长');
			$csv_data=array();
			if(isset($data['data']) && $data['data']){
				foreach($data['data'] as &$val){
					$temp=array();
					$temp['count_date']=$val['count_date'];
					$temp['count']=$val['count'];
					$temp['avg_wait_diamond_time']=$val['avg_wait_diamond_time'];
					$csv_data[]=$temp;
				}
			}
			$util->downloadCsv(date('Y-m-d').'等钻平均时长统计',$title,$csv_data);
		}
	}
	/**
	 * 导出明细报表, 与工厂平均生产时长类似
	 */
	public function export_cxv_detail(){
		$args=$this->getData();
		$page = _Request::getInt("page",1);
		$model = new ProductInfoModel(53);
		$data = $model->pageWaitDiamondCsvDetailList($args,$page,99999999999,false);
		$util=new Util();//'销售渠道','客户来源'
		$title=array('布产单号','布产来源','款号','数量','客户姓名','跟单人','工厂名称','分配工厂时间','工厂接单时间','标准出厂时间','工厂交货时间','布产状态','生产状态','布产类型','镶嵌要求','采购备注','制单人','最后操作时间','操作备注');
		$csv_data=array();
		if($data['data']){
			if(isset($data['data']) && $data['data']){
				foreach($data['data'] as &$val){
					$temp=array();
					$temp[]=$val['bc_sn'];
					$temp[]='订单'.$val['p_sn'];
					$temp[]=$val['style_sn'];
					$temp[]=$val['num'];
					$temp[]=$val['consignee'];
					$temp[]=$val['opra_uname'];
					$temp[]=$val['prc_name'];
					$temp[]=$val['factory_time'];
					$temp[]=$val['order_time'];
					$temp[]=$val['esmt_time'];
					$temp[]=$val['rece_time'];
						
					$temp[]='已出厂';//$val['status']
					switch ($val['buchan_fac_opra']){
						case '2':$buchan_fac_opra="开始生产";break;
						case '3':$buchan_fac_opra="送钻";break;
						case '4':$buchan_fac_opra="oqc质检通过";break;
						case '5':$buchan_fac_opra="oqc质检未过";break;
						case '6':$buchan_fac_opra="出厂";break;
						case '7':$buchan_fac_opra="起版";break;
						case '8':$buchan_fac_opra="修版";break;
						case '9':$buchan_fac_opra="倒模";break;
						case '10':$buchan_fac_opra="执摸";break;
						case '11':$buchan_fac_opra="等钻";break;
						case '12':$buchan_fac_opra="镶石";break;
						case '13':$buchan_fac_opra="抛光";break;
						case '14':$buchan_fac_opra="电金";break;
						case '17':$buchan_fac_opra="抛光";break;
						default:$buchan_fac_opra="其它";break;
					}
					//$temp[]=$dd->getEnum('buchan_fac_opra',$val['buchan_fac_opra']);
					$temp[]=$buchan_fac_opra;
					if($val['from_type']==1)
						$from_type='采购单';
					else $from_type='订单';
					$temp[]=$from_type;
					$temp[]=$val['xiangqian'];
					$temp[]=$val['caigou_info'];
					/* $temp[]=$model->get_channel_name($val['channel_id']);
					 $temp[]=$model->get_source_name($val['customer_source_id']); */
					$temp[]=$val['create_user'];
					$temp[]=$val['time'];
					$temp[]=htmlspecialchars($val['opra_remark']);
					$csv_data[]=$temp;
				}
			}
			 
		}
		$util->downloadCsv('等钻平均时长详细报表'.date('Y-m-d'),$title,$csv_data);
	}
	
}

