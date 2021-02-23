<?php
/**
 *  -------------------------------------------------
 *   @file		: SaleProfitController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-04-17 14:49:15
 *   @update	:
 *  -------------------------------------------------
 */
class SaleProfitController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('dow');
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('sale_profit_search_form.html',array('bar'=>Auth::getBar(),'year'=>date("Y")));
	}

    public function _search($params)
    {
        $year = date("Y");
        $args = array(
            'mod'   => _Request::get("mod"),
            'con'   => substr(__CLASS__, 0, -10),
            'act'   => __FUNCTION__,
            'way' => _Request::get("statistics_way",1),
            'year' => _Request::get("statistics_year",$year),
            'report_type' => _Request::get("report_type",1)
        );

        $where = array(
            'way' => $args['way'],
            'year' => $args['year'],
            'report_type' => $args['report_type']
            );

        return $where;
    }

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
        $where = $this->_search($params);
        
        $way = $where['way'];
        $year = $where['year'];
        $report_type = $where['report_type'];
		//$report_type = _Request::get("report_type",1);
		
		$model = new SaleProfitModel(21);
		$data = $model->pageList($where);
        $dia_type=array('其他','GIA','HRD-D','HRD-S');
        $date = date("Y-m-d");
        $type_tilie = array();
        if($way == 1){
            $type_tilie = $this->get_week($year,$date);
        }elseif($way == 2){
            $type_tilie = $this->get_month($year,$date);
        }else{

        }
        //echo "<pre>";print_r($type_tilie);
        //if($args['down_infos'] == 'downs'){
        //    $this->Download($data,$weekInfo,$week_tilie);exit();
        //}
        //echo "<pre>";
        //print_r($data);
		$this->render('sale_profit_search_list.html',array(
			'page_list'=>$data,            
            'type_tilie'=>$type_tilie,
            'SYS_SCOPE'=>SYS_SCOPE,
            'way'=>$way,
            'report_type' =>$report_type
		));		
	}

    function get_week($year,$nowtime){ 
        $year_start = $year . "-01-01"; 
        $year_end = $nowtime; 
        if($year != date('Y')) $year_end = $year . "-12-31";
        $startday = strtotime($year_start); 
        if (intval(date('N', $startday)) != '1') { 
            $startday = strtotime("next monday", strtotime($year_start)); //获取年第一周的日期 
        } 
        if(date('N', strtotime($year_start)) != 1){
            $startday = strtotime ("-1 week", $startday);
        }
        $year_mondy = date("Y-m-d", $startday); //获取年第一周的日期 
        $endday = strtotime($year_end); 
        if (intval(date('W', $endday)) == '7') { 
            $endday = strtotime("last sunday", strtotime($year_end)); 
        } 
        $endday = strtotime ("-1 day", $endday);
        $num = intval(date('W', $endday)+1); 
        for ($i = 1; $i <= $num; $i++) { 
            $j = $i -1; 
            $start_date = date("Y-m-d", strtotime("$year_mondy $j week ")); 
            $end_day = date("Y-m-d", strtotime("$start_date +6 day")); 
            $week_array[$i] =$start_date.'<br/>'.$end_day;
        } 
        return $week_array; 
    } 

    function get_month($year,$nowtime){
        if($year != date('Y')) $nowtime = $year . "-12-31";
        $year_start = $year . "-01-01";
        $start    = new \DateTime($year_start);
        $end      = new \DateTime($nowtime);
        // 时间间距 这里设置的是一个月
        $interval = \DateInterval::createFromDateString('1 month');
        $period   = new \DatePeriod($start, $interval, $end);
        $montharr = array();
        $i = 1;
        foreach ($period as $dt) {
            $begin = date("Y-m-d", mktime(0,0,0,$i,1,$year));
            $end   = date("Y-m-d", mktime(23,59,59,($i+1),0,$year));
            $montharr[$i] = $begin."<br/>".$end;
            $i++;
        }
        return $montharr;
    } 

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('sale_profit_show.html',array(
			'view'=>new SaleProfitView(new SaleProfitModel($id,21)),
			'bar'=>Auth::getViewBar()
		));
	}

    //导出
    public function dow($params){

        $where = $this->_search($params);
        set_time_limit(0);
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312','导出').time().".xls");

        $model = new SaleProfitModel(21);
        $data = $model->pageList($where);
        $year = $where['year'];
        $way  = $where['way'];
        $report_type = $where['report_type'];
        $date = date("Y-m-d");
        $type_tilie = array();
        if($way == 1){
            $type_tilie = $this->get_week($year,$date);
        }elseif($way == 2){
            $type_tilie = $this->get_month($year,$date);
        }else{

        }
        $week_s = '';
        if(!empty($type_tilie)){
            foreach ($type_tilie as $k => $v) {
                if($way == 1){
                    $week_s .= "<td>第".$k."周<br/>".$v."</td>";
                }elseif($way == 2){
                    $week_s .= "<td>第".$k."月<br/>".$v."</td>";
                }else{

                }
            }
        }
        $is_boss = SYS_SCOPE == 'boss';
        if($is_boss){
            $csv_header="<table><tr><td>证书类型</td><td></td><td>年累计</td>".$week_s."</tr>";
        }else{
            if($report_type==1)
               $csv_header="<table><tr><td>证书类型</td><td></td><td>年累计</td>".$week_s."</tr>";
            else    
               $csv_header="<table><tr><td>证书类型</td><td>经销商客户</td><td></td><td>年累计</td>".$week_s."</tr>";
        }
        $csv_body = '';
        if(!empty($data)){
            if($is_boss){
                foreach ($data as $kv => $info) {
                    $str_l= $str_m = $str_t = $str_r = '';
                    foreach ($info as $k => $v) {
                        if($k != '0'){
                            $str_l.= "<td>".$v['lishu']."</td>";
                            $str_m.= "<td>".$v['shijia']."</td>";
                            $str_t.= "<td>".($v['shijia']-$v['chengbenjia'])."</td>";
                            $str_r.= "<td>".$v['lirun']."</td>";
                        }
                    }
                    $csv_body.="<tr><td>".$kv."</td><td>销售粒数</td><td>".$info['0']['lishu']."</td>".$str_l."</tr>";
                    $csv_body.="<tr><td>".$kv."</td><td>销售额</td><td>".$info['0']['shijia']."</td>".$str_m."</tr>";
                    $csv_body.="<tr><td>".$kv."</td><td>毛利额</td><td>".($info['0']['shijia']-$info['0']['chengbenjia'])."</td>".$str_t."</tr>";
                    $csv_body.="<tr><td>".$kv."</td><td>毛利率</td><td>".$info['0']['lirun']."</td>".$str_r."</tr>";
                }
            }else{

                if($report_type==1){
                        foreach ($data as $kv => $info) {
                            $str_l= $str_m = $str_t = $str_r = '';
                            foreach ($info as $k => $v) {
                                if($k != '0'){
                                    $str_l.= "<td>".$v['lishu']."</td>";
                                    $str_m.= "<td>".$v['shijia']."</td>";
                                    $str_t.= "<td>".($v['shijia']-$v['chengbenjia'])."</td>";
                                    $str_r.= "<td>".$v['lirun']."</td>";
                                }
                            }
                            $csv_body.="<tr><td>".$kv."</td><td>销售粒数</td><td>".$info['0']['lishu']."</td>".$str_l."</tr>";
                            $csv_body.="<tr><td>".$kv."</td><td>销售额</td><td>".$info['0']['shijia']."</td>".$str_m."</tr>";
                            $csv_body.="<tr><td>".$kv."</td><td>毛利额</td><td>".($info['0']['shijia']-$info['0']['chengbenjia'])."</td>".$str_t."</tr>";
                            $csv_body.="<tr><td>".$kv."</td><td>毛利率</td><td>".$info['0']['lirun']."</td>".$str_r."</tr>";
                        }
                }
                if($report_type==2){
                        foreach ($data as $kv => $info) {
                            foreach ($info as $kt => $val) {
                                $str_l = $str_m = $str_t = $str_r = '';
                                foreach ($val as $k => $v) {
                                    if($k != '0'){
                                        $str_l.= "<td>".$v['lishu']."</td>";
                                        $str_m.= "<td>".$v['shijia']."</td>";
                                        $str_t.= "<td>".($v['shijia']-$v['chengbenjia'])."</td>";
                                        $str_r.= "<td>".$v['lirun']."</td>";
                                    }
                                }
                                $csv_body.="<tr><td>".$kv."</td><td>".$kt."</td><td>销售粒数</td><td>".$val['0']['lishu']."</td>".$str_l."</tr>";
                                $csv_body.="<tr><td>".$kv."</td><td>".$kt."</td><td>销售额</td><td>".$val['0']['shijia']."</td>".$str_m."</tr>";
                                $csv_body.="<tr><td>".$kv."</td><td>".$kt."</td><td>毛利额</td><td>".($val['0']['shijia']-$val['0']['chengbenjia'])."</td>".$str_t."</tr>";
                                $csv_body.="<tr><td>".$kv."</td><td>".$kt."</td><td>毛利率</td><td>".$val['0']['lirun']."</td>".$str_r."</tr>";
                            }
                        }
                }        
            }
            
        }
        $csv_footer="</table>";
        echo $csv_header.$csv_body.$csv_footer;
    }
}

?>