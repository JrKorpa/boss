<?php
/**
 * 维修良品率统计报表
 */
class RepairPassedReportController extends Controller
{
	public function getData()
	{
		$start_time = _Request::get('start_time');//当是post表单提交数据的时候，参数get数据失效
		$end_time = _Request::get('end_time');
		$qc_status= _Request::get('qc_status');
		$repair_factory= _Request::get('repair_factory');
		$frequency= _Request::get('frequency');
		$re_type=_Request::get('re_type');
		$repair_act= _Request::get('repair_act');
		
		$where=array(
				'mod'	=> _Request::get("mod"),
				'con'	=> substr(__CLASS__, 0, -10),
				'act'	=> __FUNCTION__,
				'start_time'=>$start_time,
				'end_time'=>$end_time,
				'qc_status'=>$qc_status,
				'repair_factory'=>$repair_factory,
				'frequency'=>$frequency,
				're_type'=>$re_type,
				'repair_act'=>$repair_act,
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
		return $where;
	}
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//渠道
		$model = new ProductInfoModel(53);
		$Processor_list   = $model->GetSupplierList();//公司列表
        $this->assign('pro_list', $Processor_list);
		$this->render('repair_passed_search_form.html',array('bar'=>Auth::getBar('index')));
	}
	/**
	 *	search，列表
	 */
	public function search($params) {
		$page = _Request::getInt("page",1);
		$pagesize = (_Post::getInt('page_num'))?_Post::getInt('page_num'):10;
		$where=$this->getData();

		$AppOrderWeixiuModel=new AppOrderWeixiuModel(58);
		$data=$AppOrderWeixiuModel->pageList_QC($where,$page,$pagesize);

       
		if(isset($data['data']) && $data['data']){
			foreach($data['data'] as $key => $val){
                $val['not_checked_qc_num']=$val['qc_time_sum'] - $val['cnt'];
                $data['data'][$key]['not_checked_qc_num'] = $val['not_checked_qc_num'];
				$data['data'][$key]['passed_percent']=$val['qc_time_sum']>0?(round($val['cnt']/$val['qc_time_sum'],4)*100):0;
				$data['data'][$key]['not_passed_percent']=$val['qc_time_sum']>0?(round($val['not_checked_qc_num']/$val['qc_time_sum'],4)*100):0;
 			}
		}
        
        $tongji = array();
        $tongji['qc_time_sum'] =0;
        $tongji['cnt'] =0;
        $tongji['not_checked_qc_num'] =0;
        $tongji['passed_percent'] =0;
        $tongji['not_passed_percent'] =0;
 		$data2=$AppOrderWeixiuModel->pageList_QC2($where);
        if($data2){
            $tongji['qc_time_sum'] = $data2['qc_time_sum'];
            $tongji['cnt'] = $data2['cnt'];
            $tongji['not_checked_qc_num'] = $tongji['qc_time_sum'] - $tongji['cnt'];
            $tongji['passed_percent'] = $tongji['qc_time_sum']>0?(round($tongji['cnt']/$tongji['qc_time_sum'],4)*100):0;
            $tongji['not_passed_percent'] = $tongji['qc_time_sum']>0?(round($tongji['not_checked_qc_num']/$tongji['qc_time_sum'],4)*100):0;
        }

		$pageData =$data;
		$pageData['filter'] = $where;
		$pageData['jsFuncs'] = 'repair_passed_search_page';
		$this->render('repair_passed_search_list.html', array(
				'pa' => Util::page($pageData),
				'page_list' => $pageData,
				'tongji'=>$tongji,
				'args'=>$where,
		));
	}
	public function index_second ($params)
	{
		//渠道
		$model = new ProductInfoModel(53);
		$Processor_list   = $model->GetSupplierList();//公司列表
		$this->assign('pro_list', $Processor_list);
		$where=$this->getData();
		$this->render('repair_passed_search_form_second.html',array('bar'=>Auth::getBar('index'),'args'=>$where));
	}
	/**
	 *	search，列表
	 */
 	 public function search_second($params) {
 	 	$page = _Request::getInt("page",1);
 	 	$pagesize = (_Post::getInt('page_num'))?_Post::getInt('page_num'):10;
 	 	$where=$this->getData();
        $AppOrderWeixiuModel=new AppOrderWeixiuModel(58);
        $data=$AppOrderWeixiuModel->pagePassedList_second($where,$page,$pagesize);

        $model = new ProductInfoModel(53);
		$Processor_list   = $model->GetSupplierList();//公司列表
        foreach($Processor_list as $key => $val){
            $plist[$val['id']] = $val['name'];
        }
        //var_dump($plist);die;
        if($data['data']){
        	foreach ($data['data'] as $key => $val){
                $data['data'][$key]['last_opra_time'] = $val['date_time'];
                $data['data'][$key]['last_opra_user'] = $val['user_name'];
                $data['data'][$key]['content'] = $val['content'];
                $bc_sn = $val['rec_id'];
                if(strlen($bc_sn)>2){
                    $data['data'][$key]['bc_id'] = substr($bc_sn,2);
                }else{
                    $data['data'][$key]['bc_id'] = 0;
                }
                if(array_key_exists($val['repair_factory'],$plist)){
                    $data['data'][$key]['factory_name'] = $plist[$val['repair_factory']];
                }else{
                    $data['data'][$key]['factory_name'] = '';
                }
        	}
        }
        $pageData =$data;
        $pageData['filter'] = $where;
        $pageData['jsFuncs'] = 'repair_passed_search_page_second';
        $this->render('repair_passed_search_list_second.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $pageData,
        	'args'=>$where,
        ));
    }
    /**
     * 导出第一层报表
     */
    public function export_cxv_index(){
    $page = _Request::getInt("page",1);
		$pagesize = (_Post::getInt('page_num'))?_Post::getInt('page_num'):10;
		$start_time =_Request::getString("start_time");
		$end_time =_Request::getString("end_time");
		$qc_status=_Request::getString("qc_status");
		$repair_factory=_Request::get("repair_factory");
		$frequency=_Request::get("frequency");
		$re_type=_Request::get("re_type");
		$repair_act=_Request::getString("repair_act");
		
		$where=array(
				'start_time'=>$start_time,
				'end_time'=>$end_time,
				'qc_status'=>$qc_status,
				'repair_factory'=>$repair_factory,
				'frequency'=>$frequency,
				're_type'=>$re_type,
				'repair_act'=>$repair_act,
		);
		$AppOrderWeixiuModel=new AppOrderWeixiuModel(58);
		$data=$AppOrderWeixiuModel->pageList_QC($where,$page,999999999);
		if(isset($data['data']) && $data['data']){
			foreach($data['data'] as &$val){
				$val['passed_percent']=number_format(100*$val['checked_qc_num']/$val['COUNT'],2);
				$val['not_checked_qc_num']=$val['COUNT']-$val['checked_qc_num'];
				$val['not_passed_percent']=number_format(100*$val['not_checked_qc_num']/$val['COUNT'],2);
			}
		}
    	if($data['data']){
    		$util=new Util();
    		$title=array('日期','总质检数','质检通过数','质检未过数','维修良品率','维修不良品率');
    		$csv_data=array();
    		if(isset($data['data']) && $data['data']){
    			foreach($data['data'] as &$val){
    				$temp=array();
    				$temp['acount_date']=$val['acount_date'];
    				$temp['COUNT']=$val['COUNT'];
    				$temp['checked_qc_num']=$val['checked_qc_num'];
    				$temp['not_checked_qc_num']=$val['not_checked_qc_num'];
    				$temp['passed_percent']=$val['passed_percent'].'%';
    				$temp['not_passed_percent']=$val['not_passed_percent'].'%';
    					
    				$csv_data[]=$temp;
    			}
    		}
    		$util->downloadCsv(date('Y-m-d').'维修良品率统计',$title,$csv_data);
    	}
    }

    public function download()
    {
 	 	$where=$this->getData();
        $AppOrderWeixiuModel=new AppOrderWeixiuModel(58);
        $data=$AppOrderWeixiuModel->pagePassedList_second_detail($where);
        $model = new ProductInfoModel(53);
		$Processor_list   = $model->GetSupplierList();//公司列表
        foreach($Processor_list as $key => $val){
            $plist[$val['id']] = $val['name'];
        }
        //var_dump($plist);die;
        if($data){
        	foreach ($data as $key => $val){
                $data[$key]['last_opra_time'] = $val['date_time'];
                $data[$key]['last_opra_user'] = $val['user_name'];
                $data[$key]['content'] = $val['content'];
                $bc_sn = $val['rec_id'];
                if(strlen($bc_sn)>2){
                    $data[$key]['bc_id'] = substr($bc_sn,2);
                }else{
                    $data[$key]['bc_id'] = 0;
                }
                if(array_key_exists($val['repair_factory'],$plist)){
                    $data[$key]['factory_name'] = $plist[$val['repair_factory']];
                }else{
                    $data[$key]['factory_name'] = '';
                }
        	}
    		$util=new Util();
    		$title=array('维修号','订单号','布产号','工厂','货号','维修类型','当前质检状态','操作人','最后操作时间','备注');
    		$csv_data=array();
            foreach($data as $val){
                $temp=array();
                $temp['id']=$val['do_id'];
                $temp['order_sn']=$val['order_sn'];
                $temp['rec_id']=$val['rec_id'];
                $temp['factory_name']=$val['factory_name'];
                $temp['goods_id']=$val['goods_id'];
                switch ($val['re_type']){
                    case 1:
                        $temp['re_type']='新货维修';
                        break;
                    case 2:
                        $temp['re_type']='售后维修';
                        break;
                    case 3:
                        $temp['re_type']='店面维修';
                        break;
                    case 5:
                        $temp['re_type']='库房维修';
                        break;
                    default:
                        $temp['re_type']='其它';
                        break;
                }
                $temp['qc_status']=$val['qc_status'] == 1?'质检通过':$val['qc_status'] == 2?'质检未过':'未质检';
                $temp['last_opra_user']=$val['last_opra_user'];
                $temp['last_opra_time']=$val['last_opra_time'];
                $temp['content']=$val['content'];
                $csv_data[]=$temp;
    		}
    		$util->downloadCsv(date('Y-m-d').'维修良品率',$title,$csv_data);
    	}
    }
}
