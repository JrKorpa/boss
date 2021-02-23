<?php
//add by zhangruiying
//2015/4/25
class ReconciliationStatementController extends CommonController {
        protected $smartyDebugEnabled = false;
		protected $whitelist = array('download','downloadSearchList');
		protected $allSalesChannelsData=array();
		public function __construct()
		{
			parent::__construct();
			$this->allSalesChannelsData=$this->getSourceList();
		}
        public function index($params) {
            $this->getSourceList();
            $this->render('reconciliation_statement_search_form.html', array('bar' => Auth::getBar(), 'sales_channels_idData' => $this->allSalesChannelsData));
        }
	public function getData()
	{
		$args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
			'channel_id'=>_Request::get("channel_id"),
			'ids'=>_Request::get("order_sn")

        );

		if(!empty($args['ids']))
		{
			$args['ids']=str_replace('，',' ',$args['ids']);
			$args['ids']=trim(preg_replace('/(\s+|,+)/',' ',$args['ids']));
			$args['ids']="'".str_replace("'',",'',str_replace(' ',"','",$args['ids']))."'";
		}
		return $args;

	}
	public function getExpressList()
	{
		$expressModel=new ExpressModel(1);
		$exp_list=$expressModel->getAllExpress();
		$arr=array();
		foreach($exp_list as $v)
		{
			$arr[$v['id']]=$v['exp_name'];
		}
		return $arr;
	}
	public function dataFormatList($data)
	{
		if(isset($data['data']) and !empty($data['data']))
		{
			$ids=array_column($data['data'],'order_sn');
			//调取对应的快件信息
			$rows=$this->getExpressDelivery($ids);
			//快递公司
			$exp_list=$this->getExpressList();
			foreach($data['data'] as $key=>$val)
			{
					$data['data'][$key]['channel']=isset($this->allSalesChannelsData[$val['department_id']])?$this->allSalesChannelsData[$val['department_id']]:'';
					$data['data'][$key]['express_id']=isset($exp_list[$val['express_id']])?$exp_list[$val['express_id']]:'';
					if(empty($data['data'][$key]['express_id']))
					{
						$val['express_id']=isset($row['express_id'][$val['order_sn']])?$row['express_id'][$val['order_sn']]:$val['express_id'];
						$data['data'][$key]['express_id']=isset($exp_list[$val['express_id']])?$exp_list[$val['express_id']]:'';
					}
					$data['data'][$key]['freight_no']=isset($row['freight_no'][$val['order_sn']])?$row['freight_no'][$val['order_sn']]:$val['freight_no'];
			}
		}
		return $data;
	}
    public function search($params) {
        $args=$this->getData();
        $page = _Request::getInt("page", 1);
        $Model=new ReconciliationStatementModel(27);
		$data =$Model->pageList($args,$page,10,false);
		$data=$this->dataFormatList($data);
		$pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'reconciliation_statement_search_page';

		$temp=$this->allSalesChannelsData;
        $this->render('reconciliation_statement_list.html', array(
            'pa' => Util::page($pageData),
            'allSalesChannelsData' =>$temp,
            'page_list' => $data
        ));
    }
	public function downloadSearchList()
	{
		$args=$this->getData();
		$Model=new ReconciliationStatementModel(27);
		$data =$Model->getDownload($args);
		$data=$this->dataFormatList(array('data'=>$data));
		$title=array('channel'=>'渠道','out_order_sn'=>'外部订单号','order_sn'=>'BDD订单号','consignee'=>'顾客姓名','goods_name'=>'产品信息','order_amount'=>'售价','express_id'=>'快递公司','freight_no'=>'快递单号');
		$arr=array();
		if(isset($data['data'][0])and !empty($data['data'][0]))
		{
			$show_fileds=array_keys($title);
			//清除下载时不需要显示的元素
			foreach($data['data'] as $key=>$row)
			{
				foreach($title as $k=>$v)
				{
					$arr[$key][$v]=$row[$k];
				}
			}
		}
		Util::downloadCsv('外部订单对应BDD订单信息',$title,$arr);

	}
    public function getSourceList() {
        //渠道
        $SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val) {
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }
        return $allSalesChannelsData;
    }
	//上传
	public function add()
	{
		$result['content'] = $this->fetch('reconciliation_statement_info.html',array(
		));
		$result['title'] = '上传';
		Util::jsonExit($result);
	}
	public function insert()
	{
        $result = array('success' => 1,'error' =>'');
		$file=$_FILES['order_sn'];
		$upload=new Upload(0.5,array('txt'));
		$upload->save_path='./public/upload/ReconciliationStatement/';
		$upload->save_name='out_order_sn.txt';
		$upload_resault=$upload->uploadfile($file);
		if(is_string($upload_resault)){

			$result['success'] =0;
			$result['error']=$upload_resault;
		}
		Util::jsonExit($result);
	}
	public function getExpressDelivery($ids)
	{
		if(!is_array($ids) or empty($ids))
		{
			return false;
		}
		$ids="'".implode("','",$ids)."'";
		/*
		$temp=array('order_sn'=>$ids);
		ksort($temp);
		$ori_str=json_encode($temp);
		$data=array("filter"=>$ori_str,"sign"=>md5('shipping'.$ori_str.'shipping'));
		$ret=Util::httpCurl(Util::getDomain().'/api.php?con=shipping&act=getExpressDelivery',$data,false,true,30);
		$ret=json_decode($ret);
		if($ret['error']==1)
		{
			return array();
		}
		return $ret['return_msg'];
		*/
		$api = new ApiModel();
		$ret = $api->shipping_api(array('order_sn'), array($ids), 'getExpressDelivery');
		return $ret['data'];
	}
	public function checkFile()
	{
		 $result = array('success' => 1,'error' =>'');
		$file_path='./public/upload/ReconciliationStatement/out_order_sn.txt';
		if(!file_exists($file_path))
		{
			$result['success'] =0;
			$result['error']='请选上传你要下载的外部订单号文本';
		}
		Util::jsonExit($result);

	}
	public function download()
	{
		$file_path='./public/upload/ReconciliationStatement/out_order_sn.txt';
		$fp=fopen($file_path,"r");
		$data=array();
		while(!feof($fp))
		{
			$temp=fgets($fp,4096);
			$temp=preg_replace('/\s/','',$temp);
			if(!empty($temp))
			{

				$data[]=$temp;
			}
		}
		$title=array('渠道','外部订单号','BDD订单号','顾客姓名','产品信息','售价','快递公司','快递单号');
		$arr=array();
		if(!empty($data))
		{
			$data_ids=$data;
			$rows=$this->getExpressDelivery($data);
			$ids="'".implode("','",$data)."'";
			$Model=new ReconciliationStatementModel(27);
			$data=$Model->getDownload(array('ids'=>$ids,'order_sn'=>''));
			$exp_list=$this->getExpressList();
			//外层只是为了让外部订单号系统不存在的也按EXCEL显示
			$arr=array();
			foreach($data as $key=>$v)
			{
				$temp=array();
				$temp['channel']=isset($this->allSalesChannelsData[$v['department_id']])?$this->allSalesChannelsData[$v['department_id']]:'';
				$temp['out_order_sn']="\t".$v['out_order_sn']."\t";
				$temp['order_sn']="\t".$v['order_sn']."\t";
				$temp['consignee']=$v['consignee'];
				$temp['goods_name']=$v['goods_name'];
				$temp['order_amount']=$v['order_amount'];
				$temp['express_id']=isset($row['express_id'][$v['order_sn']])?$row['express_id'][$v['order_sn']]:$v['express_id'];
				$temp['express_id']=isset($exp_list[$v['express_id']])?$exp_list[$v['express_id']]:'';
				$temp['freight_no']=isset($row['freight_no'][$v['order_sn']])?$row['freight_no'][$v['order_sn']]:$v['freight_no'];
				$arr[$v['out_order_sn']]=$temp;
			}
			$ret=array();
			foreach($data_ids as $id)
			{

				if(isset($arr[$id]))
				{
					$ret[]=$arr[$id];

				}
				else
				{
					$temp=array();
					$temp['channel']='';
					$temp['out_order_sn']="\t".$id."\t";
					$temp['order_sn']='';
					$temp['consignee']='';
					$temp['goods_name']='';
					$temp['order_amount']='';
					$temp['express_id']='';
					$temp['freight_no']='';
					$ret[]=$temp;
				}

			}


		}
			Util::downloadCsv('外部订单对应BDD订单信息',$title,$ret);
	}


}
