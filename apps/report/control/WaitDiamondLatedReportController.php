<?php
/**
 *  -------------------------------------------------
 *  等钻超期报表
 *   @file		: WaitDiamondLatedReportController.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  -------------------------------------------------
 */
class WaitDiamondLatedReportController extends CommonController
{

    public function __construct()
    {
        parent::__construct();
        $dd = new DictModel(1);
        $qiban_type = $dd->getEnumArray("qiban_type");
        $this->assign('qiban_type',$qiban_type);
    }
	
	//获取传过来的变量
	public function getData()
	{


		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
            'from_type'=>_Request::get("from_type"),
            'opra_unames'=>_Request::getList("opra_unames"),
            'opra_uname'=>_Request::get("opra_uname"),
            'style_sn'=>trim(_Request::get("style_sn")),
            'prc_ids'=>_Request::getList("prc_ids"),
            'prc_name'=>_Request::get("prc_name"),
            'start_time'=>_Request::get("start_time"),
            'end_time'=>_Request::get("end_time"),
            'diamond_type'=>_Request::get("diamond_type"),
            'qiban_type'=>_Request::get("qiban_type"),
		);
        if(is_array($args['opra_unames']) && !empty($args['opra_unames'])){
            $args['opra_unames_string'] = implode("','",$args['opra_unames']);
        }
        if(is_array($args['prc_ids']) && !empty($args['prc_ids'])){
            $args['prc_ids_string'] = implode(",",$args['prc_ids']);
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
		$this->render('wait_diamond_lated_search_form.html',array(
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
		//print_r($args);exit;
 	 	$start_time =_Request::get("start_time");
 	 	$end_time =_Request::get("end_time");
        if(empty($start_time) || empty($start_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 40){
            die('统计日期不能超过40天!');
        }

		$data = $model->wait_diamond_lated_List($args,$page,$page_num,false); 
        //var_dump($data);die;

        $datacount = array();
        $datacount['wd_should_all'] = '-';
        $datacount['wd_should_today'] = 0;
        $datacount['wd_un_today'] = 0;
        $datacount['wd_on'] = 0;
        $datacount['wd_real_today'] = 0;
        $datacount['chaoqilv'] = 0;
        $datacount['jishilv'] = 0;
        foreach($data as $key => $val){
            $data[$key]['chaoqilv'] = $val['wd_should_today']>0?round($val['wd_un_today']/$val['wd_should_today'],4)*100:0;
            $data[$key]['jishilv'] = $val['wd_should_today']>0?round(($val['wd_should_today']-$val['wd_un_today'])/$val['wd_should_today'],4)*100:0;
            $datacount['wd_should_today'] += $val['wd_should_today'];
            $datacount['wd_un_today'] += $val['wd_un_today'];
            $datacount['wd_on'] += $val['wd_on'];
            $datacount['wd_real_today'] += $val['wd_real_today'];
        }
        $datacount['chaoqilv'] = $datacount['wd_should_today']>0?round($datacount['wd_un_today']/$datacount['wd_should_today'],4)*100:0;
        $datacount['jishilv'] = $datacount['wd_should_today']>0?round(($datacount['wd_should_today']-$datacount['wd_un_today'])/$datacount['wd_should_today'],4)*100:0;

        $all_data = $model->wait_diamond_All();
        $datacount['cnt'] = $all_data['cnt'];
        $datacount['un_cnt'] = $all_data['un_cnt'];

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'wait_diamond_lated_search_page'; 
		
		$this->render('wait_diamond_lated_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'args'=>$args,
			'datacount'=>$datacount
		));
   }
   /**
    *	index_second，搜索框
    */
   public function index_second ($params)
   {
        $model = new ProductInfoModel(53);
        $Processor_list   = $model->GetSupplierList();//工厂列表
		//获取跟单人
		$gendanModel = new ProductFactoryOprauserModel(53);
		$gen_list = $gendanModel->select2($fields = ' distinct(`opra_uname`)' , $where = ' 1 ' , $type = 'all');
        $this->render('wait_diamond_lated_search_form_second.html',array(
            'bar'=>Auth::getBar(),
        	'Processor_list'=>$Processor_list,
			'user_list'=>$gen_list,
        )
   	    );
   }
   /**
    *	search，列表
    */
   public function search_second ($params)
   {
        $args=$this->getData();
        $page = _Request::getInt("page",1);
        $page_num = (_Post::getInt('page_num'))?_Post::getInt('page_num'):30;
        $model = new ProductInfoModel(53);
        //print_r($args);exit;
        $start_time =_Request::get("start_time");
        $end_time =_Request::get("end_time");
        if(empty($start_time) || empty($end_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 40){
            die('统计日期不能超过40天!');
        }
        $data = $model->wait_diamond_lated_detail_List($args,$page,$page_num,false);
        foreach($data as $key => $val){
            $data[$key]['chaoqilv'] = $val['wd_should_today']>0?round($val['wd_un_today']/$val['wd_should_today'],4)*100:0;
            $data[$key]['jishilv'] = $val['wd_should_today']>0?round(($val['wd_should_today']-$val['wd_un_today'])/$val['wd_should_today'],4)*100:0;
        }

        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'wait_diamond_lated_search_page_second';

        $this->render('wait_diamond_lated_search_list_second.html',array(
            'pa'=>Util::page($pageData),
            'page_list'=>$data,
            'args'=>$args
        ));
   }

    //下载当天应出但未出的钻
    public function downloadReport()
    {
        $start_time = $_REQUEST['start_time'];
        $end_time = $_REQUEST['end_time'];
 
        $time_part = $this->get_data_arr($start_time,$end_time);
        if(empty($start_time) || empty($end_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 40){
            die('统计日期不能超过40天!');
        }
        $args['start_time'] = $start_time;
        $args['end_time'] = $end_time;
        if(isset($_REQUEST['prc_ids']) && is_array($_REQUEST['prc_ids']) && !empty($_REQUEST['prc_ids'])){
            $prc_ids = _Request::getList('prc_ids');
            $args['prc_ids_string'] = join(',',$prc_ids);
        }
        if(isset($_REQUEST['opra_unames']) && is_array($_REQUEST['opra_unames']) && !empty($_REQUEST['opra_unames'])){
            $opra_unames = _Request::getList('opra_unames');
            $args['opra_unames_string'] = join("','",$opra_unames);
        }

        if(isset($_REQUEST['style_sn']) && !empty($_REQUEST['style_sn'])){
            $style_sn = _Request::getList('style_sn');
            $args['style_sn'] = join(',',$style_sn);
        }
        if(isset($_REQUEST['from_type']) && !empty($_REQUEST['from_type'])){
            $from_type = _Request::get('from_type');
            $args['from_type'] = $from_type;
        }
        if(isset($_REQUEST['diamond_type']) && !empty($_REQUEST['diamond_type'])){
            $diamond_type = _Request::get('diamond_type');
            $args['diamond_type'] = $diamond_type;
        }
        if(isset($_REQUEST['qiban_type'])){
            $qiban_type = _Request::get('qiban_type');
            $args['qiban_type'] = $qiban_type;
        }
        $model = new ProductInfoModel(53);
        $data = $model->wait_diamond_lated_detail_Info($args);
        if(empty($data)){
            echo "无数据";die;
        }
        $header = "统计时间,布产单号,等钻开始时间,实际等钻结束时间,预计等钻完成时间";
        $this->excel($data,$header,__FUNCTION__);
        exit();   
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
                    //$str .=  "<td align=right>" . iconv('utf-8','gbk',$vv) . "</td>";
                    $str .=  "<td align=right>" . $vv . "</td>";
                }
                $str .= "</tr>";
            }
            $str .= "<tr>";
            foreach ($v as $kk => $vv) {
                $str .= "<td align=right>" . iconv('utf-8','gbk',$vv) . "</td>";
            }
            $str .= "</tr>";
        }
        $str .= "</table>";
        echo $str;
    }
}
