<?php
/**
 *  -------------------------------------------------
 *   @file		: AvgProductTimeController.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  ------	
	-------------------------------------------
 */
class AvgProductTimeController extends CommonController
{
	protected $smartyDebugEnabled = true;
    //add by zhangruiying
    protected $whitelist = array('DownloadCsv','piliang_print_jiagong','printBills','bath_print_bill');
	protected $dd;
	protected $from_type=array(1=>'采购单',2=>'订单');
	protected $is_extended=array('距出厂不足两天','超期未出厂','其它');
	protected $yanse=array('D','D-E','E','E-F','F','F-G','G','G-H','H','H+','H-I','I','I-J','J','J-K','K','K-L','L','M','白色','黑色','金色');
	protected $jingdu=array('FL','IF','VVS','VVS1','VVS2','VS','VS1','VS2','SI','SI1','SI2','I','I1','I2','P P1','无');
	//add by zhangruiying
	public function getDataFarmat()
	{
		 $args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'bc_sn'	=> _Request::get("bc_sn"),
		 	'bc_ids'	=> _Request::get("bc_ids"),
			'p_sn'	=> _Request::get("p_sn"),
			'style_sn'	=> _Request::get("style_sn"),
			'fac_number'=> _Request::get("fac_number"),
			'prc_id'	=> _Request::get("prc_id"),
			'status'	=> _Request::get("status"),
		 	'rece_time'	=> _Request::get("rece_time"),
		 	'prc_name'	=> _Request::get("prc_name"),
            'consignee' => _Request::getString('consignee'),
			'opra_uname'=>_Request::getString('opra_uname'),
			'buchan_fac_opra[]'=>_Request::getList("buchan_fac_opra"),
			'esmt_time_start'=>_Request::get("esmt_time_start"),
			'esmt_time_end'=>_Request::get("esmt_time_end"),
			'order_time_start'=>_Request::get("order_time_start"),
			'order_time_end'=>_Request::get("order_time_end"),
			'channel_id'	=> _Request::get("channel_id"),
			'customer_source_id' => _Request::get("customer_source_id"),
			'xiangqian' => _Request::getString('xiangqian'),
			'from_type'=>_Request::getString('from_type'),
			'is_extended'=>_Request::get("is_extended"),
			'page_size' => _Request::get('page_size')?_Request::get('page_size'):10,
			'orderby'=>_Request::get('__order'),
			'desc_or_asc'=>_Request::get('__desc_or_asc')
			);
		$args['bc_sn']=str_replace('，',' ',$args['bc_sn']);
		$args['bc_sn']=trim(preg_replace('/(\s+|,+)/',' ',$args['bc_sn']));

		$args['p_sn']=str_replace('，',' ',$args['p_sn']);
		$args['p_sn']=trim(preg_replace('/(\s+|,+)/',' ',$args['p_sn']));
		$args['is_peishi'] = _Request::get('is_peishi');
		$args['peishi_status'] = _Request::get('peishi_status');
		return $args;

	}
	//客户来源
    public function getCustomerSources()
	{
        $CustomerSourcesModel = new CustomerSourcesModel(1);
        $CustomerSourcesList = $CustomerSourcesModel->getCustomerSourcesList("`id`,`source_name`");
        $this->assign('customer_source_list', $CustomerSourcesList);
    }
	//渠道
    public function getChannel()
	{
        $SalesChannelsModel = new SalesChannelsModel(1);
        $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
		$this->assign('channellist', $channellist);
    }
	public function __construct()
	{
		parent::__construct();
		$view_dd=new DictView(new DictModel(1));
		$this->assign('dd',$view_dd);
		$this->dd=$view_dd;

	}
	/**
	 *	index，搜索框
	 */
	public function index($params)
	{
//		Util::M('product_opra_log','front',13);	//生成模型后请注释该行
//		Util::V('product_opra_log',13);	//生成视图后请注释该行
		$args=$this->getDataFarmat();
		$this->getChannel();
		$this->getCustomerSources();
		//获取供应商列表
		$facmodel = new AppProcessorInfoModel(13);
		$process = $facmodel->getProList();

		//获取跟单人
		$gendanModel = new ProductFactoryOprauserModel(13);
		$gen_list = $gendanModel->select2($fields = ' distinct(`opra_uname`)' , $where = ' 1 ' , $type = 'all');
		$this->render('avg_product_time_search_form.html',array(
			'bar'=>Auth::getBar(),
			'process' => $process,
			'user_list'=>$gen_list,
			'user_type'=>$this->from_type,
			'is_extended'=>$this->is_extended,
			'args'=>$args,	
		));
	}

	/**
	 *	search，列表
	 */
	public function searchProduct($params)
	{

		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$args=$this->getDataFarmat();
		$args['bc_ids'] && $args['ids']=explode(',',$args['bc_ids']);
		$model = new ProductInfoModel(13);
		$data = $model->pageList($args,$page,$args['page_size'],false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'product_info_search_page';

		$table=new TableView('product_info_search_list','id');
		$table->CheckBox('_ids[]','id');
		$view=new ProductInfoView(new ProductInfoModel(13));
		$table->SetFieldConf($view->getFC());
		$table->SetSort($args['orderby'],$args['desc_or_asc']);
		$table->setTitle('bc_sn');
		$data=$this->FormatData($data);
		echo $table->ShowList($data,$pageData);
		exit;
	}
	function FormatData($data)
	{
		if($data)
		{
			$view=new ProductInfoView(new ProductInfoModel(13));
			foreach($data['data'] as $key=>$v)
			{
				$data['data'][$key]['from_type']=isset($this->from_type[$v['from_type']])?$this->from_type[$v['from_type']].$v['p_sn']:'';
				$data['data'][$key]['online']=$this->dd->getEnum('sales_channels_class',$view->get_channel_class($v['channel_id']));
				$data['data'][$key]['channel_id']=$view->get_channel_name($v['channel_id']);
				$data['data'][$key]['customer_source_id']=$view->get_customer_name($v['customer_source_id']);
				$data['data'][$key]['status']=$this->dd->getEnum('buchan_status',$v['status']);
				$data['data'][$key]['buchan_fac_opra']=$this->dd->getEnum('buchan_fac_opra',$v['buchan_fac_opra']);
				if($v['esmt_time']<date('Y-m-d') and in_array($v['status'],array(4,7)))
				{
					$data['data'][$key]['esmt_time']="<b style=\"color:red;\">{$v['esmt_time']}</b>";
				}
				else if($v['esmt_time']<date('Y-m-d',time()+3*86400) and in_array($v['status'],array(4,7)) and $v['esmt_time']>=date('Y-m-d'))
				{
					$data['data'][$key]['esmt_time']="<b style=\"color:green;\">{$v['esmt_time']}</b>";
	
				}
				if(empty($v['time']))
				{
					$data['data'][$key]['time']=$v['edit_time']?$v['edit_time']:$v['add_time'];
				}
			}
			return $data;
		}
		return array();
	}
}?>