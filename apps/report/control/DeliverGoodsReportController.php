<?php

/**
 * 发货量报表
 *
 * 
 */
class DeliverGoodsReportController extends Controller
{
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('deliver_goods_search_form.html',array('bar'=>Auth::getBar('index')));
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
		return $data_arr;
	}
	/**
	 *	search，列表
	 */
 	 public function search($params) {
 	 	$page =1;
 	 	$pagesize =9999999999;
 	 	$channel_model=new SalesChannelsModel(59);
 	 	$start_time =_Request::get("start_time");
 	 	$end_time =_Request::get("end_time");
 	 	$search_type=_Request::get("search_type");
 	 	$zt_type=_Request::get("zt_type");//展厅类型
 	 	$department_name=$department_ids='';

        if(empty($start_time) || empty($start_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 50){
            die('统计日期不能超过50天!');
        }
 	 	if(!$search_type){
 	 		die('请选择订单类型');
 	 	}
 	 	elseif($search_type=='online_department'){//网络发货
 	 		$bill_type='S';//销售订单
 	 		$department_name=_Request::get("department_name");
 	 		if($department_name){
 	 			$channel_name_arr[]=$department_name;
 	 			$department_ids=$channel_model->getChannelByChannel_Name($channel_name_arr);
 	 		}
 	 		else{
 	 			$channel_name_arr=array('银行销售部','B2C销售部','京东销售部','淘宝销售部','官方网站部','微信平台');
 	 			$department_ids=$channel_model->getChannelByChannel_Name($channel_name_arr);
 	 		}
 	 	}
 	 	elseif($search_type=='zhanting'){//展厅发货
 	 		$bill_type='M';
 	 	}
 	 	$where = array(
 	 			'bill_type'=>$bill_type,
 	 			'department_ids'=>$department_ids,
 	 			'start_time'=>$start_time,
 	 			'end_time'=>$end_time,
 	 			'zt_type'=>$zt_type,
 	 	);
        $WarehouseBillModel=new WarehouseBillModel(55);
        $data=$WarehouseBillModel->pageList($where,$page,$pagesize);
        $acount_all=0;
      	if(isset($data['data']) && $data['data']){
      		foreach($data['data'] as &$val){
      			$acount_all+=$val['acount'];
      		}
      	}
      	$args = array(
      			'mod' => _Request::get("mod"),
      			'con' => substr(__CLASS__, 0, -10),
      			'act' => __FUNCTION__,
      			'page'=>  $page,
      			'bill_type'=>$bill_type,
      			'department_ids'=>$department_ids,
      			'department_name'=>$department_name,
      			'zt_type'=>$zt_type,
      			'start_time'=>$start_time,
      			'end_time'=>$end_time,
      	);
        $pageData =$data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_old_order_search_page';
        $this->render('deliver_goods_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $pageData,
        	'acount_all'=>$acount_all,
        	'args'=>$args,
        ));
    }
    /**
     * 详情列表
     */
    public function detail_list(){
    	$channel_model=new SalesChannelsModel(59);
    	$start_time =_Request::get("start_time");
    	$end_time =_Request::get("end_time")?_Request::get("end_time"):$start_time;
    	$bill_type=_Request::get("bill_type");
    	$zt_type=_Request::get("zt_type");//展厅类型
   		$department_name=_Request::get("department_name");
   		$department_ids='';
   		if($bill_type=='S'){
	 	 	if($department_name){
	 	 			$channel_name_arr[]=$department_name;
	 	 			$department_ids=$channel_model->getChannelByChannel_Name($channel_name_arr);
	 	 	}
	 	 	else{
	 	 			$channel_name_arr=array('银行销售部','B2C销售部','京东销售部','淘宝销售部','官方网站部','微信平台');
	 	 			$department_ids=$channel_model->getChannelByChannel_Name($channel_name_arr);
	 	 	}
   		}
    	$page = _Request::get("page",1);
    	$where = array(
    			'bill_type'=>$bill_type,
    			'department_ids'=>$department_ids,
    			'start_time'=>$start_time,
    			'end_time'=>$end_time,
    			'zt_type'=>$zt_type,
    			'page'=>$page,
    			'department_name'=>$department_name,
    	);
    	$this->render('detail_list_search_form.html',array(
    			'bar'=>Auth::getBar('detail_list'),
    			'where'=>$where,
    	));
    }
    public function detail_list_ajax(){
    	$page = _Request::getInt("page",1);
    	
    	if(_Request::get("is_post")){
    		if(isset($_POST['start_time'])) $start_time=$_POST['start_time'];
    		else $start_time='';
    		if(isset($_POST['end_time'])) $end_time=$_POST['end_time'];
    		else $end_time='';
    		if(isset($_POST['bill_type'])) $bill_type=$_POST['bill_type'];
    		else $bill_type='';
    		if(isset($_POST['zt_type'])) $zt_type=$_POST['zt_type'];
    		else $zt_type='';
    		if(isset($_POST['department_ids'])) $department_ids=$_POST['department_ids'];
    		else $department_ids='';
    		if(isset($_POST['department_name'])) $department_name=$_POST['department_name'];
    		else $department_name='';
    	}
    	else{
    		$start_time =_Request::get("start_time");
	    	$end_time =_Request::get("end_time")?_Request::get("end_time"):$start_time;;
	    	$bill_type=_Request::get("bill_type");
	    	$zt_type=_Request::get("zt_type");//展厅类型
	    	$department_ids=_Request::get("department_ids");
	    	$department_name=_Request::get("department_name");
    	}
    	$args = array(
    			'mod'		=> _Request::get("mod"),
    			'con'		=> substr(__CLASS__, 0, -10),
    			'act'		=> __FUNCTION__,
    			'code'      => _Request::get('code'),
    			'bill_type'=>$bill_type,
    			'department_ids'=>$department_ids,
    			'start_time'=>$start_time,
    			'end_time'=>$end_time,
    			'zt_type'=>$zt_type,
    	);
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
    	$_SERVER["REQUEST_URI"]=$url;//清空REQUEST_URI中get时传递过来的参数，$_SERVER["REQUEST_URI"]在Util.class.php分页page方法中有用到
    	
    	$where = array(
    			'bill_type'=>$bill_type,
    			'department_name'=>$department_name,
    			'department_ids'=>$department_ids,
    			'start_time'=>$start_time,
    			'end_time'=>$end_time,
    			'zt_type'=>$zt_type,
    	);
    	$WarehouseBillModel=new WarehouseBillModel(55);
        
        if(empty($where['bill_type']))
        {
 	 		die('请选择订单类型');
        }
        //var_dump($where);die;

    	$data=$WarehouseBillModel->pageDetailList($where,$page);
    	$pageData = $data;
    	$pageData['filter'] =$args;
    	$pageData['jsFuncs'] = 'delivergoodsreport_detail_search_page';
    	//print_r(Util::page($pageData));exit;
    	$this->render('deliver_detail_list.html',array(
    			'bar'=>Auth::getBar(),
    			'pa'=>Util::page($pageData),
    			'page_list'=>$data,
    	));
    }
    /**
     * 导出第一层报表
     */
    public function export_cxv_index(){
    	$page =1;
 	 	$pagesize =9999999999;
 	 	$channel_model=new SalesChannelsModel(59);
 	 	$start_time =_Request::get("start_time");
 	 	$end_time =_Request::get("end_time");
 	 	$search_type=_Request::get("search_type");
 	 	$zt_type=_Request::get("zt_type");//展厅类型
 	 	$department_name=$department_ids='';
 	 	if(!$search_type){
 	 		die('请选择订单类型');
 	 	}
 	 	elseif($search_type=='online_department'){//网络发货
 	 		$bill_type='S';//销售订单
 	 		$department_name=_Request::get("department_name");
 	 		if($department_name){
 	 			$channel_name_arr[]=$department_name;
 	 			$department_ids=$channel_model->getChannelByChannel_Name($channel_name_arr);
 	 		}
 	 		else{
 	 			$channel_name_arr=array('银行销售部','B2C销售部','京东销售部','淘宝销售部','官方网站部','微信平台');
 	 			$department_ids=$channel_model->getChannelByChannel_Name($channel_name_arr);
 	 		}
 	 	}
 	 	elseif($search_type=='zhanting'){//展厅发货
 	 		$bill_type='M';
 	 	}
 	 	$where = array(
 	 			'bill_type'=>$bill_type,
 	 			'department_ids'=>$department_ids,
 	 			'start_time'=>$start_time,
 	 			'end_time'=>$end_time,
 	 			'zt_type'=>$zt_type,
 	 	);
 	 	//print_r($where);exit;
        $WarehouseBillModel=new WarehouseBillModel(55);
        $data=$WarehouseBillModel->pageList($where,$page,$pagesize);
    	if($data['data']){
    		$util=new Util();
    		$title=array('日期','数量');
    		$csv_data=array();
    		if(isset($data['data']) && $data['data']){
    			foreach($data['data'] as &$val){
    				$temp=array();
    				$temp['add_date']=$val['add_date'];
    				$temp['acount']=$val['acount'];
    					
    				$csv_data[]=$temp;
    			}
    		}
    		$util->downloadCsv(date('Y-m-d').'发货量统计',$title,$csv_data);
    	}
    }
}
