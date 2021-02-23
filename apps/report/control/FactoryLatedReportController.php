<?php
/**
 *  -------------------------------------------------
 *  工厂超期率报表
 *   @file		: FactoryLatedReportController.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  -------------------------------------------------
 */
class FactoryLatedReportController extends Controller
{
	function __construct(){
		parent::__construct();
		set_time_limit(0);
		ini_set('memory_limit','-1');
	}
	//获取传过来的变量
	public function getData()
	{
		if(_Request::get("is_post")){
			if(isset($_POST['from_type'])) $from_type=$_POST['from_type'];
			else $from_type='';
			if(isset($_POST['style_sn'])) $style_sn=$_POST['style_sn'];
			else $style_sn='';
			if(isset($_POST['prc_name'])) $prc_name=$_POST['prc_name'];
			else $prc_name='';
			if(isset($_POST['start_time'])) $start_time=$_POST['start_time'];
			else $start_time='';
			if(isset($_POST['end_time'])) $end_time=$_POST['end_time'];
			else $end_time='';
			$opra_uname='';
		}
		else{
			
			$from_type=_Request::get('from_type');
			$style_sn=_Request::get('style_sn');
			$prc_name=_Request::get('prc_name');
			$start_time=_Request::get('start_time');
			$end_time=_Request::get('end_time');
			$opra_uname=_Request::get("opra_uname");
		}
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
            'from_type'=>$from_type,
			'opra_uname'=>$opra_uname,//第三层列表跟单人分组需要用到
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
        $model = new ProductInfoModel(53);
		$Processor_list   = $model->GetSupplierList();//工厂列表
		//获取跟单人
		$gendanModel = new ProductFactoryOprauserModel(53);
		$gen_list = $gendanModel->select2($fields = ' distinct(`opra_uname`)' , $where = ' 1 ' , $type = 'all');
		$this->render('factory_lated_search_form.html',array(
            'bar'=>Auth::getBar(),
        	'Processor_list'=>$Processor_list,
			'user_list'=>$gen_list,
            )
        );
	}
    /**
     *  取出指定时间数组
     */
    public function get_data_arr($start_time,$end_time){
		$start_time_str=explode("-",$start_time);
		$end_time_str=explode("-",$end_time);
		$data_arr=array();
		while(true){
			if($start_time_str[0].$start_time_str[1].$start_time_str[2]>$end_time_str[0].$end_time_str[1].$end_time_str[2]) break;
			$data_arr[$start_time_str[0]."-".$start_time_str[1]."-".$start_time_str[2]]=$start_time_str[1]."-".$start_time_str[2];
			$start_time_str[2]++;
			$start_time_str=explode("-",date("Y-m-d",mktime(0,0,0,$start_time_str[1],$start_time_str[2],$start_time_str[0])));
		}
        krsort($data_arr);
		return $data_arr;
	}
    /**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args=$this->getData();
		$page = _Request::getInt("page",1);
        $page_num = (_Post::getInt('page_num'))?_Post::getInt('page_num'):30;
		$model = new ProductInfoModel(53);
        $start_time =$args["start_time"];
 	 	$end_time =$args["end_time"];
        if(empty($start_time) || empty($start_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 40){
            die('统计日期不能超过40天!');
        }
        
		$data1 = $model->pageList($args,$page,$page_num,false); 
        $data2 = $model->getDeliveryOrder($args,$page,$page_num,false);
        $data3 = $model->getReceiveOrder($args,$page,$page_num,false);

        $data = array();
        foreach($dateList as $k => $v)
        {
            $ret = array();
            $ret['dotime'] = $k;
            $ret['delivery_num'] = 0;
            $ret['receive_num'] = 0;
            $ret['cnt'] = 0;
            $ret['un_cnt'] = 0;
            $ret['on_cnt'] = 0;
            $ret['lated_percent'] = 0;
            $ret['pass_percent'] = 0;
            $data[$k] = $ret;
        }

        if(isset($data1['data']) && !empty($data1['data'])){
            foreach($data1['data'] as $key => $val){
                $data[$val['dotime']]['cnt'] = $val['cnt'];
                $data[$val['dotime']]['un_cnt'] = $val['un_cnt'];
                $data[$val['dotime']]['on_cnt'] = $val['on_cnt'];
            }
        }
        if(isset($data2['data']) && !empty($data2['data'])){
            foreach($data2['data'] as $key => $val){
                $data[$val['dotime']]['delivery_num'] = $val['delivery_num'];
            }
        }
        if(isset($data3['data']) && !empty($data3['data'])){
            foreach($data3['data'] as $key => $val){
                $data[$val['dotime']]['receive_num'] = $val['receive_num'];
            }
        }
        
        $datacount = array();
        $datacount['delivery_num'] = 0;
        $datacount['receive_num'] = 0;
        $datacount['cnt'] = 0;
        $datacount['un_cnt'] = 0;
        $datacount['on_cnt'] = 0;
        $datacount['lated_percent'] = 0;
        $datacount['pass_percent'] = 0;
		if($data){
            foreach($data as $key => $val){
                $datacount['delivery_num'] += $val['delivery_num'];
                $datacount['receive_num'] += $val['receive_num'];
                $datacount['cnt'] += $val['cnt'];
                $datacount['un_cnt'] += $val['un_cnt'];
                $datacount['on_cnt'] += $val['on_cnt'];
                $data[$key]['lated_percent'] = $val['un_cnt']==0?0:round($val['un_cnt']/$val['cnt'],4)*100;
                $data[$key]['pass_percent'] = $val['on_cnt']==0?0:round($val['on_cnt']/$val['cnt'],4)*100;
            }
            $datacount['lated_percent'] = $datacount['un_cnt']==0?0:round($datacount['un_cnt']/$datacount['cnt'],4)*100;
            $datacount['pass_percent'] = $datacount['on_cnt']==0?0:round($datacount['on_cnt']/$datacount['cnt'],4)*100;
        }
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'factory_lated_search_page'; 
		//汇总
		$alldata=array('data'=>array());
		$this->render('factory_lated_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'args'=>$args,
			'datacount'=>$datacount
				
		));
   }
   /**
    * 详情列表
    */
   	public function detail_list(){
   		$model = new ProductInfoModel(53);
   		$Processor_list   = $model->GetSupplierList();//工厂列表
   		//获取跟单人
   		$gendanModel = new ProductFactoryOprauserModel(53);
   		$gen_list = $gendanModel->select2($fields = ' distinct(`opra_uname`)' , $where = ' 1 ' , $type = 'all');
   		$args=$this->getData();
	   	$this->render('factory_detail_list_search_form.html',array(
	   			'bar'=>Auth::getBar('detail_list'),
	   			'args'=>$args,
	   			'Processor_list'=>$Processor_list,
	   			'user_list'=>$gen_list,
	   	));
   }
   
   public function detail_list_ajax(){
	   	$args=$this->getData();
	   	$page = _Request::getInt("page",1);
	   	$page_num = (_Post::getInt('page_num'))?_Post::getInt('page_num'):30;
	   	$model = new ProductInfoModel(53);
        $start_time =$args["start_time"];
 	 	$end_time =$args["end_time"];
        if(empty($start_time) || empty($start_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 40){
            die('统计日期不能超过40天!');
        }
        
		$data1 = $model->pageDetailList($args,$page,$page_num,false); 
        $data2 = $model->getDetailDeliveryOrder($args,$page,$page_num,false);
        $data3 = $model->getDetailReceiveOrder($args,$page,$page_num,false);

        $data = array();

        if(isset($data1['data']) && !empty($data1['data'])){
            foreach($data1['data'] as $key => $val){
                $data[$val['dotime']][$val['opra_uname']]['cnt'] = $val['cnt'];
                $data[$val['dotime']][$val['opra_uname']]['un_cnt'] = $val['un_cnt'];
                $data[$val['dotime']][$val['opra_uname']]['on_cnt'] = $val['on_cnt'];
            }
        }
        if(isset($data2['data']) && !empty($data2['data'])){
            foreach($data2['data'] as $key => $val){
                $data[$val['dotime']][$val['opra_uname']]['delivery_num'] = $val['delivery_num'];
            }
        }
        if(isset($data3['data']) && !empty($data3['data'])){
            foreach($data3['data'] as $key => $val){
                $data[$val['dotime']][$val['opra_uname']]['receive_num'] = $val['receive_num'];
            }
        }
        
        $newdata = array(); 
        $datacount = array();
        $datacount['delivery_num'] = 0;
        $datacount['receive_num'] = 0;
        $datacount['cnt'] = 0;
        $datacount['un_cnt'] = 0;
        $datacount['on_cnt'] = 0;
        $datacount['lated_percent'] = 0;
        $datacount['pass_percent'] = 0;
		if($data){
            foreach($data as $key => $val){
                foreach($val as $user => $v){

                    if(!isset($v['delivery_num'])){
                        $v['delivery_num']=0;
                        $data[$key][$user]['delivery_num'] = 0;
                    }
                    if(!isset($v['receive_num'])){
                        $v['receive_num']=0;
                        $data[$key][$user]['receive_num'] = 0;
                    }
                    if(!isset($v['cnt'])){
                        $v['cnt']=0;
                        $data[$key][$user]['cnt'] = 0;
                    }
                    if(!isset($v['un_cnt'])){
                        $v['un_cnt']=0;
                        $data[$key][$user]['un_cnt'] = 0;
                    }
                    if(!isset($v['on_cnt'])){
                        $v['on_cnt']=0;
                        $data[$key][$user]['on_cnt'] = 0;
                    }
                    $v['dotime'] = $key;
                    $v['opra_uname'] = $user;
                    $v['lated_percent'] = $v['un_cnt']==0?0:round($v['un_cnt']/$v['cnt'],4)*100;
                    $v['pass_percent'] = $v['on_cnt']==0?0:round($v['on_cnt']/$v['cnt'],4)*100;
                    $newdata[] = $v;
                    $datacount['delivery_num'] += $v['delivery_num'];
                    $datacount['receive_num'] += $v['receive_num'];
                    $datacount['cnt'] += $v['cnt'];
                    $datacount['un_cnt'] += $v['un_cnt'];
                    $datacount['on_cnt'] += $v['on_cnt'];
                
                }
            }
            $datacount['lated_percent'] = $datacount['cnt']==0?0:round($datacount['un_cnt']/$datacount['cnt'],4)*100;
            $datacount['pass_percent'] = $datacount['cnt']==0?0:round($datacount['on_cnt']/$datacount['cnt'],4)*100;
        }

	   	$pageData = $data;
	   	$pageData['filter'] = $args;
	   	$pageData['jsFuncs'] = 'factory_detail_search_page';
	   	//print_r($datacount);exit;
	   	$this->render('factory_detail_search_list.html',array(
	   			'pa'=>Util::page($pageData),
	   			'page_list'=>$newdata,
	   			'args'=>$args,
	   			'datacount'=>$datacount
	   	
	   	));
   }
   public function last_detail_list(){
	   	$model = new ProductInfoModel(53);
	   	$Processor_list   = $model->GetSupplierList();//工厂列表
	   	//获取跟单人
	   	$gendanModel = new ProductFactoryOprauserModel(53);
	   	$gen_list = $gendanModel->select2($fields = ' distinct(`opra_uname`)' , $where = ' 1 ' , $type = 'all');
	   	$args=$this->getData();
	   	$this->render('factory_last_detail_list_search_form.html',array(
	   			'bar'=>Auth::getBar('last_detail_list'),
	   			'args'=>$args,
	   			'Processor_list'=>$Processor_list,
	   			'user_list'=>$gen_list,
	   	));
   }
   
   public function last_detail_list_ajax(){
	   	$args=$this->getData();
	   	$page = _Request::getInt("page",1);
	   	$page_num = (_Post::getInt('page_num'))?_Post::getInt('page_num'):30;
	   	$model = new ProductInfoModel(53);
	   	$data = $model->pageLastDetailList($args,$page,$page_num,false);
	   	$pageData = $data;
	   	$pageData['filter'] = $args;
	   	$pageData['jsFuncs'] = 'factoryLated_last_detail_search_page';
	   	$this->render('factory_last_detail_search_list.html',array(
	   			'pa'=>Util::page($pageData),
	   			'page_list'=>$data,
	   			'args'=>$args,
	   	));
   }
   /**
    * 导出第一层报表
    */
   public function export_cxv_index(){
	   	$args=$this->getData();
	   	$page = _Request::getInt("page",1);
	   	$model = new ProductInfoModel(53);

        $start_time =$args["start_time"];
 	 	$end_time =$args["end_time"];
        if(empty($start_time) || empty($start_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 40){
            die('统计日期不能超过40天!');
        }
        $page_num=10000;
		$data1 = $model->pageList($args,$page,$page_num,false); 
        $data2 = $model->getDeliveryOrder($args,$page,$page_num,false);
        $data3 = $model->getReceiveOrder($args,$page,$page_num,false);

        $data = array();
        foreach($dateList as $k => $v)
        {
            $ret = array();
            $ret['dotime'] = $k;
            $ret['delivery_num'] = 0;
            $ret['receive_num'] = 0;
            $ret['cnt'] = 0;
            $ret['un_cnt'] = 0;
            $ret['on_cnt'] = 0;
            $ret['lated_percent'] = 0;
            $ret['pass_percent'] = 0;
            $data[$k] = $ret;
        }

        if(isset($data1['data']) && !empty($data1['data'])){
            foreach($data1['data'] as $key => $val){
                $data[$val['dotime']]['cnt'] = $val['cnt'];
                $data[$val['dotime']]['un_cnt'] = $val['un_cnt'];
                $data[$val['dotime']]['on_cnt'] = $val['on_cnt'];
            }
        }
        if(isset($data2['data']) && !empty($data2['data'])){
            foreach($data2['data'] as $key => $val){
                $data[$val['dotime']]['delivery_num'] = $val['delivery_num'];
            }
        }
        if(isset($data3['data']) && !empty($data3['data'])){
            foreach($data3['data'] as $key => $val){
                $data[$val['dotime']]['receive_num'] = $val['receive_num'];
            }
        }
        
        $datacount = array();
        $datacount['do_time'] = 'Total';
        $datacount['delivery_num'] = 0;
        $datacount['receive_num'] = 0;
        $datacount['cnt'] = 0;
        $datacount['un_cnt'] = 0;
        $datacount['on_cnt'] = 0;
        $datacount['lated_percent'] = 0;
        $datacount['pass_percent'] = 0;
		if($data){
            foreach($data as $key => $val){
                $datacount['delivery_num'] += $val['delivery_num'];
                $datacount['receive_num'] += $val['receive_num'];
                $datacount['cnt'] += $val['cnt'];
                $datacount['un_cnt'] += $val['un_cnt'];
                $datacount['on_cnt'] += $val['on_cnt'];
                $data[$key]['lated_percent'] = $val['un_cnt']==0?0:(round($val['un_cnt']/$val['cnt'],4)*100)."%";
                $data[$key]['pass_percent'] = $val['on_cnt']==0?0:(round($val['on_cnt']/$val['cnt'],4)*100)."%";
            }
            $datacount['lated_percent'] = $datacount['un_cnt']==0?0:(round($datacount['un_cnt']/$datacount['cnt'],4)*100)."%";
            $datacount['pass_percent'] = $datacount['on_cnt']==0?0:(round($datacount['on_cnt']/$datacount['cnt'],4)*100)."%";
        }
        array_push($data,$datacount);
        $util=new Util();
	   	$title=array('出厂日期','布产数','接单数','当天应出数','当天未出数','实际出货数','超期率','及时率');
	   	$csv_data=array();
	   	$util->downloadCsv('工厂超期统计总报表'.date('Y-m-d'),$title,$data);
   }
   /**
    * 导出明细报表
    */
   public function export_cxv_detail(){
   	$args=$this->getData();
   	$page = _Request::getInt("page",1);
   	$model = new ProductInfoModel(53);
   	$args['status']='';

    $start_time =$args["start_time"];
    $end_time =$args["end_time"];
    if(empty($start_time) || empty($start_time)){
        die('统计日期不能为空');
    }
    $dateList = $this->get_data_arr($start_time,$end_time);
    if(count($dateList) > 40){
        die('统计日期不能超过40天!');
    }

    $data = $model->page_factory_lated_csv($args,$page,99999999999,false);
   	$util=new Util();//'销售渠道','客户来源'
   	$title=array('布产单号','布产来源','款号','数量','客户姓名','跟单人','工厂名称',
        '分配工厂时间','工厂接单时间','标准出厂时间','工厂交货时间',
        '布产状态','生产状态','布产类型','镶嵌要求','采购备注','制单人','超期天数','最后操作时间','操作备注');

   	$csv_data=array();
   	if($data){
        foreach($data as $val){
            $temp=array();
            $temp['bc_sn']=$val['bc_sn'];
            $temp['buchan_source']=$val['from_type']==1?'采购单'.$val['p_sn']:'订单'.$val['p_sn'];
            $temp['style_sn']=$val['style_sn'];
            $temp['num']=$val['num'];
            $temp['consignee']=$val['consignee'];
            $temp['opra_uname']=$val['opra_uname'];
            $temp['prc_name']=$val['prc_name'];

            $temp['factory_time']=$val['order_time'];
            $temp['order_time']=$val['order_time'];
            $temp['esmt_time']=$val['esmt_time'];
            $temp['rece_time']=$val['rece_time'];
            switch ($val['status']){
                case '1':$buchan_status="初始化";break;
                case '3':$buchan_status="已分配";break;
                case '4':$buchan_status="生产中";break;
                case '7':$buchan_status="部分出厂";break;
                case '9':$buchan_status="已出厂";break;
                case '10':$buchan_status="已取消";break;
                case '11':$buchan_status="不需布产";break;
                default:$buchan_status="其它";break;
            }
            $temp['buchan_status']=$buchan_status;
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
            $temp['buchan_fac_opra']=$buchan_fac_opra;
            $temp['from_type']=$val['from_type']==1?'采购单':'订单';
            $temp['xiangqian']=$val['xiangqian'];
            $temp['caigou_info']=$val['caigou_info'];
            $temp['create_user']=$val['create_user'];
            
            $status=$val['status'];
            if($status >9 ){
            	$caoqi_day='';
            }else {
            	$starts_time=$val['esmt_time'];
            	if($status==9){
            		$ends_time=substr($val['rece_time'],0,10);
            	}elseif($status<9){
            		$ends_time=date("Y-m-d",time());
            	}
            	$caoqi_day=(strtotime($ends_time)-strtotime($starts_time))/86400;
            }
            if($caoqi_day< 0 ) $caoqi_day=0;
            $temp['caoqi_day']=$caoqi_day;
           
            
            $temp['time']=$val['last_opra_time'];
            $temp['opra_remark']=htmlspecialchars($val['last_opra_remark']);
            $csv_data[]=$temp;
   		}
   	}
   	$util->downloadCsv('工厂超期率详细报表'.date('Y-m-d'),$title,$csv_data);
   }
}

