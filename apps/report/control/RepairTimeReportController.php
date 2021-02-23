<?php
/**
 * 维修时间统计报表
 */
class RepairTimeReportController extends Controller
{
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//渠道
		$model = new ProductInfoModel(53);
		$Processor_list   = $model->GetSupplierList();//公司列表
        $this->assign('pro_list', $Processor_list);
		$this->render('repair_time_search_form.html',array('bar'=>Auth::getBar('index')));
	}
	/**
	 *	search，列表
	 */
	public function search($params) {
        $time_type = _Request::getString('time_type');
        $start_time = _Request::getString('start_time');
        $end_time = _Request::getString('end_time');
        $repair_factory = _Request::getString('repair_factory');
        $frequency = _Request::getString('frequency');
        $re_type = _Request::getString('re_type');//维修类型
        $repair_act =  _Request::getString('repair_act');//维修内容
        
        $where = array();
        if($time_type){
            $where['time_type'] = $time_type;
        }
        if($start_time){
            $where['start_time'] = $start_time;
        }
        if($end_time){
            $where['end_time'] = $end_time;
        }
        if($repair_factory){
            $where['repair_factory'] = $repair_factory;
        }
        if($frequency){
            $where['frequency'] = $frequency;
        }
        if($re_type){
            $where['re_type'] = $re_type;
        }
        if($repair_act){
            $where['repair_act'] = $repair_act;
        }
		$page = _Request::getInt("page",1);
		$pagesize = (_Post::getInt('page_num'))?_Post::getInt('page_num'):10;
		$AppOrderWeixiuModel=new AppOrderWeixiuModel(58);
		$data=$AppOrderWeixiuModel->pageList($where,$page,$pagesize);
		$pageData =$data;
		$pageData['filter'] = $where;
		$pageData['jsFuncs'] = 'repair_time_search_page';
		//汇总
		$tongji = array();
        $tongji['cnt']=0;
        $tongji['5day_receive_num']=0;
        $tongji['5day_receive_time']=0;
        $tongji['allday_receive_num']=0;
        $tongji['allday_receive_time']=0;
        $tongji['5day_percent']=0;
        $tongji['5day_avg']=0;
        $tongji['allday_avg']=0;
        foreach($data['all_data'] as $key => $val){
            $tongji['cnt'] += $val['cnt'];
            $tongji['5day_receive_num'] += $val['5day_receive_num'];
            $tongji['5day_receive_time'] += $val['5day_receive_time'];
            $tongji['allday_receive_num'] += $val['allday_receive_num'];
            $tongji['allday_receive_time'] += $val['allday_receive_time'];
        }
        $tongji['5day_percent'] = $tongji['allday_receive_num']>0 ? round($tongji['5day_receive_num']/$tongji['allday_receive_num'],2)*100:0;
        $tongji['5day_avg'] = $tongji['5day_receive_num']>0?round($tongji['5day_receive_time']/$tongji['5day_receive_num'],2):0;
        $tongji['allday_avg'] = $tongji['allday_receive_num']>0?round($tongji['allday_receive_time']/$tongji['allday_receive_num'],2):0;


		$this->render('repair_time_search_list.html', array(
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
		$this->render('repair_time_search_form_second.html',array('bar'=>Auth::getBar('index')));
	}
	/**
	 *	search，列表
	 */
 	 public function search_second($params) {
        $time_type = _Request::getString('time_type');
        $start_time = _Request::getString('start_time');
        $end_time = _Request::getString('end_time');
        $repair_factory = _Request::getString('repair_factory');
        $frequency = _Request::getString('frequency');
        $re_type = _Request::getString('re_type');//维修类型
        $repair_act =  _Request::getString('repair_act');//维修内容
        
        $where = array();
        if($time_type){
            $where['time_type'] = $time_type;
        }
        if($start_time){
            $where['start_time'] = $start_time;
        }
        if($end_time){
            $where['end_time'] = $end_time;
        }
        if($repair_factory){
            $where['repair_factory'] = $repair_factory;
        }
        if($frequency){
            $where['frequency'] = $frequency;
        }
        if($re_type){
            $where['re_type'] = $re_type;
        }
        if($repair_act){
            $where['repair_act'] = $repair_act;
        }
 	 	$page = _Request::getInt("page",1);
 	 	$pagesize = (_Post::getInt('page_num'))?_Post::getInt('page_num'):100;
        $AppOrderWeixiuModel=new AppOrderWeixiuModel(58);
        $data=$AppOrderWeixiuModel->get_detail($where,$page,$pagesize);
		//汇总
        $tongji = array();
        $tongji['num']=0;
        $tongji['allday_receive_num'] = 0;
        $tongji['0day_receive_num'] = 0;
        $tongji['1day_receive_num'] = 0;
        $tongji['2day_receive_num'] = 0;
        $tongji['3day_receive_num'] = 0;
        $tongji['4day_receive_num'] = 0;
        
        $tongji['5day_receive_num'] = 0;
        $tongji['6day_receive_num'] = 0;
        $tongji['7day_receive_num'] = 0;
        $tongji['8_20day_receive_num'] = 0;
        $tongji['21day_receive_num'] = 0;
        $tongji['0_5day_receive_num'] = 0;
        $tongji['0_5day_receive_percent'] = 0;

        foreach($data['data'] as $key => $val){
            $tongji['num'] += $val['num'];
            $tongji['allday_receive_num'] += $val['allday_receive_num'];
            $tongji['0day_receive_num'] += $val['0day_receive_num'];
            $tongji['1day_receive_num'] += $val['1day_receive_num'];
            $tongji['2day_receive_num'] += $val['2day_receive_num'];
            $tongji['3day_receive_num'] += $val['3day_receive_num'];
            $tongji['4day_receive_num'] += $val['4day_receive_num'];
            
            $tongji['5day_receive_num'] += $val['5day_receive_num'];
            $tongji['6day_receive_num'] += $val['6day_receive_num'];
            $tongji['7day_receive_num'] += $val['7day_receive_num'];
            $tongji['8_20day_receive_num'] += $val['8_20day_receive_num'];
            $tongji['21day_receive_num'] += $val['21day_receive_num'];
            $tongji['0_5day_receive_num'] += $val['0_5day_receive_num'];
            $tongji['0_5day_receive_percent'] += $val['0_5day_receive_percent'];
        }
        $tongji['0_5day_receive_percent'] = $tongji['allday_receive_num']>0 ? round($tongji['0_5day_receive_num']/$tongji['allday_receive_num'],4)*100:0;        
        $pageData =$data;
        $pageData['filter'] = $where;
        $pageData['jsFuncs'] = 'repair_time_search_page_second';
        $this->render('repair_time_search_list_second.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $pageData,
        	'args'=>$where,
            'tongji'=>$tongji
        ));
    }
    public function index_third ($params)
    {
    	//渠道
    	$model = new ProductInfoModel(53);
    	$Processor_list   = $model->GetSupplierList();//公司列表
    	$this->assign('pro_list', $Processor_list);
    	$this->render('repair_time_search_form_third.html',array('bar'=>Auth::getBar('index')));
    }
    /**
     *	search，列表
     */
    public function search_third($params) {
        $time_type = _Request::getString('time_type');
        $start_time = _Request::getString('start_time');
        $end_time = _Request::getString('end_time');
        $repair_factory = _Request::getString('repair_factory');
        $frequency = _Request::getString('frequency');
        $re_type = _Request::getString('re_type');//维修类型
        $repair_act =  _Request::getString('repair_act');//维修内容
        
        $where = array();
        if($time_type){
            $where['time_type'] = $time_type;
        }
        if($start_time){
            $where['start_time'] = $start_time;
        }
        if($end_time){
            $where['end_time'] = $end_time;
        }
        if($repair_factory){
            $where['repair_factory'] = $repair_factory;
        }
        if($frequency){
            $where['frequency'] = $frequency;
        }
        if($re_type){
            $where['re_type'] = $re_type;
        }
        if($repair_act){
            $where['repair_act'] = $repair_act;
        }
        $page = _Request::getInt("page",1);
    	$pagesize = (_Post::getInt('page_num'))?_Post::getInt('page_num'):10000;
    	$AppOrderWeixiuModel=new AppOrderWeixiuModel(58);
    	$data=$AppOrderWeixiuModel->get_detail_third($where,$page,$pagesize);
    	$pageData =$data;
    	$pageData['filter'] = $where;
    	$pageData['jsFuncs'] = 'repair_time_search_page_third';
    	$this->render('repair_time_search_list_third.html', array(
    			'pa' => Util::page($pageData),
    			'page_list' => $pageData,
    			'args'=>$where,
    	));
    }

    public function index_forth ($params)
    {
        $time_type = _Request::getString('time_type');
        $start_time = _Request::getString('start_time');
        $end_time = _Request::getString('end_time');
        $repair_factory = _Request::getString('repair_factory');
        $frequency = _Request::getString('frequency');
        $re_type = _Request::getString('re_type');//维修类型
        $repair_act =  _Request::getString('repair_act');//维修内容
        
        $where = array();
        if($time_type){
            $where['time_type'] = $time_type;
        }
        if($start_time){
            $where['start_time'] = $start_time;
        }
        if($end_time){
            $where['end_time'] = $end_time;
        }
        if($repair_factory){
            $where['repair_factory'] = $repair_factory;
        }
        if($frequency){
            $where['frequency'] = $frequency;
        }
        if($re_type){
            $where['re_type'] = $re_type;
        }
        if($repair_act){
            $where['repair_act'] = $repair_act;
        }
        $page = _Request::getInt("page",1);
    	$pagesize = (_Post::getInt('page_num'))?_Post::getInt('page_num'):100;
    	$AppOrderWeixiuModel=new AppOrderWeixiuModel(58);
    	$data=$AppOrderWeixiuModel->get_detail_forth($where);

		if($where['time_type']=='add'){//日期为下单时间
            $header = "流水号,下单时间,工厂名称,布产号";
		}
		else{//日期为发货时间
            $header = "流水号,发货时间,工厂名称,布产号";
		}
        $this->excel($data,$header,"维修单明细");
    }

    //excel导出功能
    public function excel($data, $header, $name)
    {
        ob_end_clean();
        header("Content-Type: text/html; charset=GBK");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=" . iconv('UTF-8', 'GBK', $name) .".xls");
        header('Cache-Control: max-age=0');

        $header = explode(',',$header);

        $str = "<table border=1 cellspacing=1 cellpadding=1>";
        foreach ($data as $k => $v) {
            if ($k == 0) {
                $str .= "<tr>";
                foreach ($header as $vv) {
                    $str .=  "<td align=right>" . iconv('UTF-8','gbk',$vv) . "</td>";
                }
                $str .= "</tr>";
            }
            $str .= "<tr>";
            foreach ($v as $kk => $vv) {
                $str .= "<td align=right>" . iconv('UTF-8','gbk',$vv) . "</td>";
            }
            $str .= "</tr>";
        }
        $str .= "</table>";
        echo $str;
    }
}
