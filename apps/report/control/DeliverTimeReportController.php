<?php
/**
 * 发货时间统计报表
 * 
 */
class DeliverTimeReportController extends Controller
{
	public function __construct(){
		set_time_limit(0);
		ini_set('memory_limit','-1');
		parent::__construct();
	}
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//渠道
		$model = new UserChannelModel(59);
		$data = $model->getChannels($_SESSION['userId'],0);
		$this->assign('onlySale',count($data)==1);
        $this->assign('sales_channels_idData', $data);
		$this->render('deliver_time_search_form.html',array('bar'=>Auth::getBar('index')));
	}
	
	/**
	 *	search，列表
	 */
 	 public function search($params) {
 	 	$page = _Request::getInt("page",1);
 	 	$pagesize = (_Post::getInt('page_num'))?_Post::getInt('page_num'):10;
 	 	$start_time =_Request::getString("start_time");
 	 	$end_time =_Request::getString("end_time");
 	 	$time_type=_Request::getString("time_type")?_Request::getString("time_type"):'add';
 	 	$order_type=_Request::getString("order_type");
 	 	if(!$order_type) $order_type=_Request::getString("channel_class");
 	 	$buchan_type=_Request::get("buchan_type");
 	 	$department_id=_Request::get("order_department");
 	 	if(!$department_id) $department_id=_Request::get("department_id");
 	 	$where=array(
 	 			'start_time'=>$start_time,
 	 			'end_time'=>$end_time,
 	 			'channel_class'=>$order_type,
 	 			'time_type'=>$time_type,
 	 			'buchan_type'=>$buchan_type,
 	 			'department_id'=>$department_id,
 	 	);
        if(empty($start_time) || empty($start_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 40){
            die('统计日期不能超过40天!');
        }

        $buchan_status = _Request::getList('buchan_status');
        if($buchan_status){
            $where['buchan_status'] = implode(',',$buchan_status);
        }

        //var_dump($dateList);die;

        //不带分页
        $BaseOrderInfoModel=new BaseOrderInfoModel(51);
        if($time_type == 'add'){
            $list=$BaseOrderInfoModel->pageListCreateTime($where);
            foreach($list as $key => $val){
                $tongji_date = substr($val['create_time'],0,10);
                if($val['order_status'] == 4)
                {
                    $dateList[$tongji_date]['close_order']++;
                }elseif($val['order_status'] == 2){
                    $dateList[$tongji_date]['check_order']++;
                    //----------boss_1285
                    /*结合布产类型（现货、期货）、1、0
                    订单类型（线上订单、线下订单）、1、2
                    布产状态（不需布产、已出厂、未操作、生产中、已布产），1、2(不需布产)
                    规则改为包括以下几种组合：
                    线上订单+现货+所有布产状态、111
                    线上订单+期货+不需布产、102
                    线下订单+期货+不需布产、202
                    线下订单+现货+所有布产状、211*/
                    $buxu_bc = $val['buchan_status'] != 5?1:2;
                    $isXharr = array('111','102','202','211','212','112');//定义现货规则
                    $is_xianghuo = $val['channel_class'].$val['is_xianhuo'].$buxu_bc;
                    if(in_array($is_xianghuo, $isXharr)){
                        $dateList[$tongji_date]['xianhuo_order']++;
                    }
                    //-----end
                    if(in_array($val['order_pay_status'],array(3,4))){
                        $dateList[$tongji_date]['paid_order']++;
                        if($val['send_good_status']!=2){
                            $dateList[$tongji_date]['paid_unsend_order']++;
                        }
                        if($val['delivery_status'] != 2){
                            $dateList[$tongji_date]['paid_undeliver_order']++;
                        }
                    }elseif($val['order_pay_status'] == 2){
                        $dateList[$tongji_date]['pay_part_order']++;
                    }elseif($val['order_pay_status'] == 1){
                        $dateList[$tongji_date]['unpaid_order']++;
                    }

                    $re_end_time = $val['send_time'];
                    $order_time = $val['pay_date'];
                    $re_end_timestamp = strtotime($re_end_time);
                    $order_timestamp = strtotime($order_time);
                    if($re_end_time != '0000-00-00 00:00:00' && $order_time != '0000-00-00 00:00:00' && $re_end_timestamp > $order_timestamp){
                        $dateList[$tongji_date]['send_num']++;
                        $delay_day = round(($re_end_timestamp - $order_timestamp)/86400, 2);
                        $kk = '';
                        if($delay_day<7 && $delay_day>=0){
                            if($delay_day<=1 && $delay_day>=0){
                                $delay_day = 0;
                            }elseif($delay_day<2 && $delay_day>1){
                                $delay_day = 1;
                            }elseif($delay_day<3 && $delay_day>=2){
                                $delay_day = 2;
                            }elseif($delay_day<4 && $delay_day>=3){
                                $delay_day = 3;
                            }elseif($delay_day<5 && $delay_day>=4){
                                $delay_day = 4;
                            }elseif($delay_day<6 && $delay_day>=5){
                                $delay_day = 5;
                            }else{
                                $delay_day = 6;
                            }
                            $kk=$delay_day.'d_num';
                        }
                        elseif($delay_day<=20){
                            $kk='7_20d_num';
                        }
                        else{
                            $kk='21d_num';
                        }
                        $dateList[$tongji_date][$kk]++;
                    }
                }
            }
             
            foreach($dateList as $key => $val){
                $dateList[$key]['order_all'] = $val['check_order']+$val['close_order'];
                $dateList[$key]['xianhuo_order_percent'] = $dateList[$key]['order_all']>0?round($dateList[$key]['xianhuo_order']/$dateList[$key]['order_all'],4)*100:0;
                if($val['send_num']>0){
                    $count_num = $val['paid_order'] + $val['pay_part_order'];
                    $dateList[$key]['0d_percent']=number_format(100*$val['0d_num']/$count_num,2);
                    $dateList[$key]['1d_percent']=number_format(100*$val['1d_num']/$count_num,2);
                    $dateList[$key]['2d_percent']=number_format(100*$val['2d_num']/$count_num,2);
                    $dateList[$key]['3d_percent']=number_format(100*$val['3d_num']/$count_num,2);
                    $dateList[$key]['4d_percent']=number_format(100*$val['4d_num']/$count_num,2);
                    $dateList[$key]['5d_percent']=number_format(100*$val['5d_num']/$count_num,2);
                    $dateList[$key]['6d_percent']=number_format(100*$val['6d_num']/$count_num,2);
                    $dateList[$key]['7_20d_percent']=number_format(100*$val['7_20d_num']/$count_num,2);
                    $dateList[$key]['21d_percent']=number_format(100*$val['21d_num']/$count_num,2);
                }
            }
            $data = $dateList;
            krsort($data);
        }else{
            $list=$BaseOrderInfoModel->pageListSendTime($where);
            foreach($list as $key => $val){
                $tongji_date = substr($val['send_time'],0,10);
                if($val['order_status'] == 4)
                {
                    $dateList[$tongji_date]['close_order']++;
                }elseif($val['order_status'] == 2){
                    $dateList[$tongji_date]['check_order']++;
                    //----------boss_1285
                    /*结合布产类型（现货、期货）、1、0
                    订单类型（线上订单、线下订单）、1、2
                    布产状态（不需布产、已出厂、未操作、生产中、已布产），1、2(不需布产)
                    规则改为包括以下几种组合：
                    线上订单+现货+所有布产状态、111
                    线上订单+期货+不需布产、102
                    线下订单+期货+不需布产、202
                    线下订单+现货+所有布产状、211*/
                    $buxu_bc = $val['buchan_status'] != 5?1:2;
                    $isXharr = array('111','102','202','211','212','112');//定义现货规则
                    $is_xianghuo = $val['channel_class'].$val['is_xianhuo'].$buxu_bc;
                    if(in_array($is_xianghuo, $isXharr)){
                        $dateList[$tongji_date]['xianhuo_order']++;
                    }
                    //-----end
                    if(in_array($val['order_pay_status'],array(3,4))){
                        $dateList[$tongji_date]['paid_order']++;
                        if($val['send_good_status']!=2){
                            $dateList[$tongji_date]['paid_unsend_order']++;
                        }
                        if($val['delivery_status'] != 2){
                            $dateList[$tongji_date]['paid_undeliver_order']++;
                        }
                    }elseif($val['order_pay_status'] == 2){
                        $dateList[$tongji_date]['pay_part_order']++;
                    }elseif($val['order_pay_status'] == 1){
                        $dateList[$tongji_date]['unpaid_order']++;
                    }

                    $re_end_time = $val['send_time'];
                    $order_time = $val['pay_date'];
                    $re_end_timestamp = strtotime($re_end_time);
                    $order_timestamp = strtotime($order_time);
                    if($re_end_time != '0000-00-00 00:00:00' && $order_time != '0000-00-00 00:00:00' && $re_end_timestamp > $order_timestamp){
                        $dateList[$tongji_date]['send_num']++;
                        $delay_day = round(($re_end_timestamp - $order_timestamp)/86400, 2);
                        $kk = '';
                        if($delay_day<7 && $delay_day>=0){
                            if($delay_day<=1 && $delay_day>=0){
                                $delay_day = 0;
                            }elseif($delay_day<2 && $delay_day>1){
                                $delay_day = 1;
                            }elseif($delay_day<3 && $delay_day>=2){
                                $delay_day = 2;
                            }elseif($delay_day<4 && $delay_day>=3){
                                $delay_day = 3;
                            }elseif($delay_day<5 && $delay_day>=4){
                                $delay_day = 4;
                            }elseif($delay_day<6 && $delay_day>=5){
                                $delay_day = 5;
                            }else{
                                $delay_day = 6;
                            }
                            $kk=$delay_day.'d_num';
                        }
                        elseif($delay_day<=20){
                            $kk='7_20d_num';
                        }
                        else{
                            $kk='21d_num';
                        }
                        $dateList[$tongji_date][$kk]++;
                    }
                }
            }
             
            foreach($dateList as $key => $val){
                $dateList[$key]['order_all'] = $val['check_order']+$val['close_order'];
                $dateList[$key]['xianhuo_order_percent'] = $dateList[$key]['order_all']>0?round($dateList[$key]['xianhuo_order']/$dateList[$key]['order_all'],4)*100:0;
                if($val['send_num']>0){
                    $count_num = $val['paid_order'] + $val['pay_part_order'];
                    $dateList[$key]['0d_percent']=number_format(100*$val['0d_num']/$count_num,2);
                    $dateList[$key]['1d_percent']=number_format(100*$val['1d_num']/$count_num,2);
                    $dateList[$key]['2d_percent']=number_format(100*$val['2d_num']/$count_num,2);
                    $dateList[$key]['3d_percent']=number_format(100*$val['3d_num']/$count_num,2);
                    $dateList[$key]['4d_percent']=number_format(100*$val['4d_num']/$count_num,2);
                    $dateList[$key]['5d_percent']=number_format(100*$val['5d_num']/$count_num,2);
                    $dateList[$key]['6d_percent']=number_format(100*$val['6d_num']/$count_num,2);
                    $dateList[$key]['7_20d_percent']=number_format(100*$val['7_20d_num']/$count_num,2);
                    $dateList[$key]['21d_percent']=number_format(100*$val['21d_num']/$count_num,2);
                }
            }
            $data = $dateList;
            krsort($data);
        }
        $pageData = array();
        $pageData['data'] = $data;
        $pageData['filter'] = $where;
        $pageData['jsFuncs'] = 'deliver_time_search_page';
        //汇总
        $tongji=array();
        $tongji['order_all'] = 0;
        $tongji['close_order'] = 0;
        $tongji['check_order'] = 0;
        $tongji['xianhuo_order'] = 0;
        $tongji['xianhuo_order_percent'] = 0;
        $tongji['paid_order'] = 0;
        $tongji['pay_part_order'] = 0;
        $tongji['unpaid_order'] = 0;
        $tongji['paid_unsend_order'] = 0;
        $tongji['paid_undeliver_order'] = 0;

        $tongji['0d_num'] = 0;
        $tongji['1d_num'] = 0;
        $tongji['2d_num'] = 0;
        $tongji['3d_num'] = 0;
        $tongji['4d_num'] = 0;
        $tongji['5d_num'] = 0;
        $tongji['6d_num'] = 0;
        $tongji['7_20d_num'] = 0;
        $tongji['21d_num'] = 0;
        $tongji['send_num'] = 0;
        
        $tongji['0d_percent']=0;
        $tongji['1d_percent']=0;
        $tongji['2d_percent']=0;
        $tongji['3d_percent']=0;
        $tongji['4d_percent']=0;
        $tongji['5d_percent']=0;
        $tongji['6d_percent']=0;
        $tongji['7_20d_percent']=0;
        $tongji['21d_percent']=0;
        
        foreach($data as $key => $val){
            $tongji['order_all'] += $val['order_all'];
            $tongji['close_order'] += $val['close_order'];
            $tongji['check_order'] += $val['check_order'];
            $tongji['xianhuo_order'] += $val['xianhuo_order'];
            $tongji['paid_order']  += $val['paid_order'];
            $tongji['pay_part_order'] += $val['pay_part_order'];
            $tongji['unpaid_order'] += $val['unpaid_order'];
            $tongji['paid_unsend_order'] += $val['paid_unsend_order'];
            $tongji['paid_undeliver_order'] += $val['paid_undeliver_order'];

            $tongji['0d_num'] += $val['0d_num'];
            $tongji['1d_num'] += $val['1d_num'];
            $tongji['2d_num'] += $val['2d_num'];
            $tongji['3d_num'] += $val['3d_num'];
            $tongji['4d_num'] += $val['4d_num'];
            $tongji['5d_num'] += $val['5d_num'];
            $tongji['6d_num'] += $val['6d_num'];
            $tongji['7_20d_num'] += $val['7_20d_num'];
            $tongji['21d_num'] += $val['21d_num'];
            $tongji['send_num']  += $val['send_num'];
        }
        
        $tongji['xianhuo_order_percent'] = $tongji['order_all']>0?round($tongji['xianhuo_order']/$tongji['order_all'],4)*100:0;
        if($tongji['send_num']>0){
            $count_num = $tongji['paid_order'] + $tongji['pay_part_order'];
            $tongji['0d_percent']=number_format(100*$tongji['0d_num']/$count_num,2);
            $tongji['1d_percent']=number_format(100*$tongji['1d_num']/$count_num,2);
            $tongji['2d_percent']=number_format(100*$tongji['2d_num']/$count_num,2);
            $tongji['3d_percent']=number_format(100*$tongji['3d_num']/$count_num,2);
            $tongji['4d_percent']=number_format(100*$tongji['4d_num']/$count_num,2);
            $tongji['5d_percent']=number_format(100*$tongji['5d_num']/$count_num,2);
            $tongji['6d_percent']=number_format(100*$tongji['6d_num']/$count_num,2);
            $tongji['7_20d_percent']=number_format(100*$tongji['7_20d_num']/$count_num,2);
            $tongji['21d_percent']=number_format(100*$tongji['21d_num']/$count_num,2);
        }

        $this->render('deliver_time_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $pageData,
        	'tongji'=>$tongji,
        	'args'=>$where,
        ));
    }
    function  export_csv(){
 	 	$page = _Request::getInt("page",1);
 	 	$pagesize = (_Post::getInt('page_num'))?_Post::getInt('page_num'):10;
 	 	$start_time =_Request::getString("start_time");
 	 	$end_time =_Request::getString("end_time");
 	 	$time_type=_Request::getString("time_type")?_Request::getString("time_type"):'add';
 	 	$order_type=_Request::getString("order_type");
 	 	if(!$order_type) $order_type=_Request::getString("channel_class");
 	 	$buchan_type=_Request::get("buchan_type");
 	 	$department_id=_Request::get("order_department");
 	 	if(!$department_id) $department_id=_Request::get("department_id");
 	 	$where=array(
 	 			'start_time'=>$start_time,
 	 			'end_time'=>$end_time,
 	 			'channel_class'=>$order_type,
 	 			'time_type'=>$time_type,
 	 			'buchan_type'=>$buchan_type,
 	 			'department_id'=>$department_id,
 	 	);
        if(empty($start_time) || empty($start_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 40){
            die('统计日期不能超过40天!');
        }
        $buchan_status = _Request::getList('buchan_status');
        if($buchan_status){
            $where['buchan_status'] = implode(',',$buchan_status);
        }

        //不带分页
        $BaseOrderInfoModel=new BaseOrderInfoModel(51);
        if($time_type == 'add'){
            $list=$BaseOrderInfoModel->pageListCreateTime($where);
            $csvList = array();
            $util=new Util();
            foreach($list as $key => $val){
                $tongji_date = substr($val['create_time'],0,10);
                if($val['order_status'] == 4)
                {
                    $dateList[$tongji_date]['close_order']++;
                }elseif($val['order_status'] == 2){
                    $dateList[$tongji_date]['check_order']++;
                    //----------boss_1285
                    /*结合布产类型（现货、期货）、1、0
                    订单类型（线上订单、线下订单）、1、2
                    布产状态（不需布产、已出厂、未操作、生产中、已布产），1、2(不需布产)
                    规则改为包括以下几种组合：
                    线上订单+现货+所有布产状态、111
                    线上订单+期货+不需布产、102
                    线下订单+期货+不需布产、202
                    线下订单+现货+所有布产状、211*/
                    $buxu_bc = $val['buchan_status'] != 5?1:2;
                    $isXharr = array('111','102','202','211','212','112');//定义现货规则
                    $is_xianghuo = $val['channel_class'].$val['is_xianhuo'].$buxu_bc;
                    if(in_array($is_xianghuo, $isXharr)){
                        $dateList[$tongji_date]['xianhuo_order']++;
                    }
                    //-----end
                    if(in_array($val['order_pay_status'],array(3,4))){
                        $dateList[$tongji_date]['paid_order']++;
                        if($val['send_good_status']!=2){
                            $dateList[$tongji_date]['paid_unsend_order']++;
                        }
                        if($val['delivery_status'] != 2){
                            $dateList[$tongji_date]['paid_undeliver_order']++;
                        }
                    }elseif($val['order_pay_status'] == 2){
                        $dateList[$tongji_date]['pay_part_order']++;
                    }elseif($val['order_pay_status'] == 1){
                        $dateList[$tongji_date]['unpaid_order']++;
                    }

                    $re_end_time = $val['send_time'];
                    $order_time = $val['pay_date'];
                    $re_end_timestamp = strtotime($re_end_time);
                    $order_timestamp = strtotime($order_time);
                    if($re_end_time != '0000-00-00 00:00:00' && $order_time != '0000-00-00 00:00:00' && $re_end_timestamp > $order_timestamp){
                        $dateList[$tongji_date]['send_num']++;
                        $delay_day = round(($re_end_timestamp - $order_timestamp)/86400, 2);
                        $kk = '';
                        if($delay_day<7 && $delay_day>=0){
                            if($delay_day<=1 && $delay_day>=0){
                                $delay_day = 0;
                            }elseif($delay_day<2 && $delay_day>1){
                                $delay_day = 1;
                            }elseif($delay_day<3 && $delay_day>=2){
                                $delay_day = 2;
                            }elseif($delay_day<4 && $delay_day>=3){
                                $delay_day = 3;
                            }elseif($delay_day<5 && $delay_day>=4){
                                $delay_day = 4;
                            }elseif($delay_day<6 && $delay_day>=5){
                                $delay_day = 5;
                            }else{
                                $delay_day = 6;
                            }
                            $kk=$delay_day.'d_num';
                        }
                        elseif($delay_day<=20){
                            $kk='7_20d_num';
                        }
                        else{
                            $kk='21d_num';
                        }
                        $dateList[$tongji_date][$kk]++;
                    }
                }
            }
             
            foreach($dateList as $key => $val){
                unset($dateList[$key]['0d_percent']);
                unset($dateList[$key]['2d_percent']);
                unset($dateList[$key]['1d_percent']);
                unset($dateList[$key]['3d_percent']);
                unset($dateList[$key]['4d_percent']);
                unset($dateList[$key]['5d_percent']);
                unset($dateList[$key]['6d_percent']);
                unset($dateList[$key]['7_20d_percent']);
                unset($dateList[$key]['21d_percent']);

                $dateList[$key]['order_all'] = $val['check_order']+$val['close_order'];
                $dateList[$key]['xianhuo_order'] .=  "(" .($dateList[$key]['order_all']>0?round($dateList[$key]['xianhuo_order']/$dateList[$key]['order_all'],4)*100:0)."%)";
                if($val['send_num']>0){
                    $count_num = $val['paid_order'] + $val['pay_part_order'];
                    $dateList[$key]['0d_num'] .=  "(" .number_format(100*$val['0d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['1d_num'] .=  "(" .number_format(100*$val['1d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['2d_num'] .=  "(" .number_format(100*$val['2d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['3d_num'] .=  "(" .number_format(100*$val['3d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['4d_num'] .=  "(" .number_format(100*$val['4d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['5d_num'] .=  "(" .number_format(100*$val['5d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['6d_num'] .=  "(" .number_format(100*$val['6d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['7_20d_num'] .=  "(" .number_format(100*$val['7_20d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['21d_num'] .=  "(" .number_format(100*$val['21d_num']/$count_num,2)."%)" ;
                }else{
                    $count_num = $val['paid_order'] + $val['pay_part_order'];
                    $dateList[$key]['0d_num'] .=  "(0%)" ;
                    $dateList[$key]['1d_num'] .=  "(0%)" ;
                    $dateList[$key]['2d_num'] .=  "(0%)" ;
                    $dateList[$key]['3d_num'] .=  "(0%)" ;
                    $dateList[$key]['4d_num'] .=  "(0%)" ;
                    $dateList[$key]['5d_num'] .=  "(0%)" ;
                    $dateList[$key]['6d_num'] .=  "(0%)" ;
                    $dateList[$key]['7_20d_num'] .=  "(0%)" ;
                    $dateList[$key]['21d_num'] .=  "(0%)" ;
                }
                unset($dateList[$key]['xianhuo_order_percent']);
                unset($dateList[$key]['send_num']);
                //var_dump($dateList[$key]);die;
            }
            $data = $dateList;
            krsort($data);
            $title = array('日期','总订单量','关闭订单','有效订单','现货订单占比','已付款订单','支付定金订单','未付款订单','已付款未发货','已付款未配货','O天发货','1天发货','2天发货','3天发货','4天发货','5天发货','6天发货','7-20天发货','超20天发货');
        	$util->downloadCsv('发货时间统计报表',$title,$data);
            exit;
        }else{
            $list=$BaseOrderInfoModel->pageListSendTime($where);
            $csvList = array();
            $util=new Util();
            foreach($list as $key => $val){
                $tongji_date = substr($val['send_time'],0,10);
                if($val['order_status'] == 4)
                {
                    $dateList[$tongji_date]['close_order']++;
                }elseif($val['order_status'] == 2){
                    $dateList[$tongji_date]['check_order']++;
                    //----------boss_1285
                    /*结合布产类型（现货、期货）、1、0
                    订单类型（线上订单、线下订单）、1、2
                    布产状态（不需布产、已出厂、未操作、生产中、已布产），1、2(不需布产)
                    规则改为包括以下几种组合：
                    线上订单+现货+所有布产状态、111
                    线上订单+期货+不需布产、102
                    线下订单+期货+不需布产、202
                    线下订单+现货+所有布产状、211*/
                    $buxu_bc = $val['buchan_status'] != 5?1:2;
                    $isXharr = array('111','102','202','211','212','112');//定义现货规则
                    $is_xianghuo = $val['channel_class'].$val['is_xianhuo'].$buxu_bc;
                    if(in_array($is_xianghuo, $isXharr)){
                        $dateList[$tongji_date]['xianhuo_order']++;
                    }
                    //-----end
                    if(in_array($val['order_pay_status'],array(3,4))){
                        $dateList[$tongji_date]['paid_order']++;
                        if($val['send_good_status']!=2){
                            $dateList[$tongji_date]['paid_unsend_order']++;
                        }
                        if($val['delivery_status'] != 2){
                            $dateList[$tongji_date]['paid_undeliver_order']++;
                        }
                    }elseif($val['order_pay_status'] == 2){
                        $dateList[$tongji_date]['pay_part_order']++;
                    }elseif($val['order_pay_status'] == 1){
                        $dateList[$tongji_date]['unpaid_order']++;
                    }

                    $re_end_time = $val['send_time'];
                    $order_time = $val['pay_date'];
                    $re_end_timestamp = strtotime($re_end_time);
                    $order_timestamp = strtotime($order_time);
                    if($re_end_time != '0000-00-00 00:00:00' && $order_time != '0000-00-00 00:00:00' && $re_end_timestamp > $order_timestamp){
                        $dateList[$tongji_date]['send_num']++;
                        $delay_day = round(($re_end_timestamp - $order_timestamp)/86400, 2);
                        $kk = '';
                        if($delay_day<7 && $delay_day>=0){
                            if($delay_day<=1 && $delay_day>=0){
                                $delay_day = 0;
                            }elseif($delay_day<2 && $delay_day>1){
                                $delay_day = 1;
                            }elseif($delay_day<3 && $delay_day>=2){
                                $delay_day = 2;
                            }elseif($delay_day<4 && $delay_day>=3){
                                $delay_day = 3;
                            }elseif($delay_day<5 && $delay_day>=4){
                                $delay_day = 4;
                            }elseif($delay_day<6 && $delay_day>=5){
                                $delay_day = 5;
                            }else{
                                $delay_day = 6;
                            }
                            $kk=$delay_day.'d_num';
                        }
                        elseif($delay_day<=20){
                            $kk='7_20d_num';
                        }
                        else{
                            $kk='21d_num';
                        }
                        $dateList[$tongji_date][$kk]++;
                    }
                }
            }
             
            foreach($dateList as $key => $val){
                unset($dateList[$key]['0d_percent']);
                unset($dateList[$key]['2d_percent']);
                unset($dateList[$key]['1d_percent']);
                unset($dateList[$key]['3d_percent']);
                unset($dateList[$key]['4d_percent']);
                unset($dateList[$key]['5d_percent']);
                unset($dateList[$key]['6d_percent']);
                unset($dateList[$key]['7_20d_percent']);
                unset($dateList[$key]['21d_percent']);

                $dateList[$key]['order_all'] = $val['check_order']+$val['close_order'];
                $dateList[$key]['xianhuo_order'] .=  "(" .($dateList[$key]['order_all']>0?round($dateList[$key]['xianhuo_order']/$dateList[$key]['order_all'],4)*100:0)."%)";
                if($val['send_num']>0){
                    $count_num = $val['paid_order'] + $val['pay_part_order'];
                    $dateList[$key]['0d_num'] .=  "(" .number_format(100*$val['0d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['1d_num'] .=  "(" .number_format(100*$val['1d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['2d_num'] .=  "(" .number_format(100*$val['2d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['3d_num'] .=  "(" .number_format(100*$val['3d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['4d_num'] .=  "(" .number_format(100*$val['4d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['5d_num'] .=  "(" .number_format(100*$val['5d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['6d_num'] .=  "(" .number_format(100*$val['6d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['7_20d_num'] .=  "(" .number_format(100*$val['7_20d_num']/$count_num,2)."%)" ;
                    $dateList[$key]['21d_num'] .=  "(" .number_format(100*$val['21d_num']/$count_num,2)."%)" ;
                }else{
                    $count_num = $val['paid_order'] + $val['pay_part_order'];
                    $dateList[$key]['0d_num'] .=  "(0%)" ;
                    $dateList[$key]['1d_num'] .=  "(0%)" ;
                    $dateList[$key]['2d_num'] .=  "(0%)" ;
                    $dateList[$key]['3d_num'] .=  "(0%)" ;
                    $dateList[$key]['4d_num'] .=  "(0%)" ;
                    $dateList[$key]['5d_num'] .=  "(0%)" ;
                    $dateList[$key]['6d_num'] .=  "(0%)" ;
                    $dateList[$key]['7_20d_num'] .=  "(0%)" ;
                    $dateList[$key]['21d_num'] .=  "(0%)" ;
                }
                unset($dateList[$key]['xianhuo_order_percent']);
                unset($dateList[$key]['send_num']);
                //var_dump($dateList[$key]);die;
            }
            $data = $dateList;
            krsort($data);
            $title = array('日期','总订单量','关闭订单','有效订单','现货订单占比','已付款订单','支付定金订单','未付款订单','已付款未发货','已付款未配货','O天发货','1天发货','2天发货','3天发货','4天发货','5天发货','6天发货','7-20天发货','超20天发货');
        	$util->downloadCsv('发货时间统计报表',$title,$data);
            exit;
        }
    }

    public function get_data_arr($start_time,$end_time){
        $start_time_str = explode("-", $start_time);
        $end_time_str = explode("-", $end_time);
        $data_arr = array();
        while(true){                                                                       
            if($start_time_str[0].$start_time_str[1].$start_time_str[2] > $end_time_str[0].$end_time_str[1].$end_time_str[2]) 				break;

            $list = array();

            $list['dotime'] = $start_time_str[0]."-".$start_time_str[1]."-".$start_time_str[2];
            $list['order_all'] = 0;
            $list['close_order'] = 0;
            $list['check_order'] = 0;
            $list['xianhuo_order'] = 0;
            $list['xianhuo_order_percent'] = 0;
            $list['paid_order'] = 0;
            $list['pay_part_order'] = 0;
            $list['unpaid_order'] = 0;
            $list['paid_unsend_order'] = 0;
            $list['paid_undeliver_order'] = 0;

            $list['0d_num'] = 0;
            $list['1d_num'] = 0;
            $list['2d_num'] = 0;
            $list['3d_num'] = 0;
            $list['4d_num'] = 0;
            $list['5d_num'] = 0;
            $list['6d_num'] = 0;
            $list['7_20d_num'] = 0;
            $list['21d_num'] = 0;
            $list['send_num'] = 0;
            
            $list['0d_percent']=0;
            $list['1d_percent']=0;
            $list['2d_percent']=0;
            $list['3d_percent']=0;
            $list['4d_percent']=0;
            $list['5d_percent']=0;
            $list['6d_percent']=0;
            $list['7_20d_percent']=0;
            $list['21d_percent']=0;

            $data_arr[$start_time_str[0]."-".$start_time_str[1]."-".$start_time_str[2]]=$list;
            $start_time_str[2]++;
            $start_time_str = explode("-", date("Y-m-d", mktime(0,0,0,$start_time_str[1],$start_time_str[2],$start_time_str[0])));
        }
        return $data_arr;
    }

    public function downloadDetail()
    {
 	 	$page = _Request::getInt("page",1);
 	 	$pagesize = (_Post::getInt('page_num'))?_Post::getInt('page_num'):10;
 	 	$start_time =_Request::getString("start_time");
 	 	$end_time =_Request::getString("end_time");
 	 	$time_type=_Request::getString("time_type")?_Request::getString("time_type"):'add';
 	 	$order_type=_Request::getString("order_type");
 	 	if(!$order_type) $order_type=_Request::getString("channel_class");
 	 	$buchan_type=_Request::get("buchan_type");
 	 	$department_id=_Request::get("order_department");
 	 	if(!$department_id) $department_id=_Request::get("department_id");
 	 	$where=array(
 	 			'start_time'=>$start_time,
 	 			'end_time'=>$end_time,
 	 			'channel_class'=>$order_type,
 	 			'time_type'=>$time_type,
 	 			'buchan_type'=>$buchan_type,
 	 			'department_id'=>$department_id,
 	 	);
        if(empty($start_time) || empty($start_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 40){
            die('统计日期不能超过40天!');
        }

        $buchan_status = _Request::getList('buchan_status');
        if($buchan_status){
            $where['buchan_status'] = implode(',',$buchan_status);
        }

        //var_dump($dateList);die;
        $order_status_list = array(1=>'待审核',2=>'已审核',4=>'关闭');
        $order_pay_status_list = array(1=>'未付款',2=>'部分付款',3=>'已付款',4=>'财务备案');
        $delivery_status_list = array(1=>'未配货',2=>'允许配货',3=>'配货中',4=>'配货缺货',5=>'已配货',6=>'无效');
        $send_good_status_list = array(1=>'未发货',2=>'已发货',3=>'收货确认',4=>'允许发货',5=>'已到店');

        //不带分页
        $BaseOrderInfoModel=new BaseOrderInfoModel(51);
        $util=new Util();
        $title=array('订单号','订单类型','订单生成时间','订单审核时间','订单状态','订单支付状态','订单配送状态','订单发货状态','支付时间','运营发货时间','运营发货时长');
        $csvList = array();

        if($time_type == 'add'){
            $list=$BaseOrderInfoModel->pageListCreateTime($where);
    		foreach($list as $key => $val){
                if($val['order_status'] != 2) continue;
                $csvList[$key][] = "`".$val['order_sn'];
                $csvList[$key][] = $val['is_xianhuo']==1?'现货单':'期货单';
                $csvList[$key][] = $val['create_time'];
                $csvList[$key][] = $val['check_time'];
                
                $order_status = isset($order_status_list[$val['order_status']])?$order_status_list[$val['order_status']]:'未知';
                $csvList[$key][] = $order_status;

                $order_pay_status = isset($order_pay_status_list[$val['order_pay_status']])?$order_pay_status_list[$val['order_pay_status']]:'未知';
                $csvList[$key][] = $order_pay_status;

                $delivery_status = isset($delivery_status_list[$val['delivery_status']])?$delivery_status_list[$val['delivery_status']]:'未知';
                $csvList[$key][] = $delivery_status;

                $send_good_status = isset($send_good_status_list[$val['send_good_status']])?$send_good_status_list[$val['send_good_status']]:'未知';
                $csvList[$key][] = $send_good_status;

                $csvList[$key][] = $val['pay_date'];
                $csvList[$key][] = $val['send_time'];
                $csvList[$key][] = $this->diffDate($val['send_time'], $val['pay_date']);
    		}
    	}else{
            $list=$BaseOrderInfoModel->pageListSendTime($where);
    		foreach($list as $key => $val){
                if($val['order_status'] != 2) continue;
                $csvList[$key][] = "`".$val['order_sn'];
                $csvList[$key][] = $val['is_xianhuo']==1?'现货单':'期货单';
                $csvList[$key][] = $val['create_time'];
                $csvList[$key][] = $val['check_time'];
                
                $order_status = isset($order_status_list[$val['order_status']])?$order_status_list[$val['order_status']]:'未知';
                $csvList[$key][] = $order_status;

                $order_pay_status = isset($order_pay_status_list[$val['order_pay_status']])?$order_pay_status_list[$val['order_pay_status']]:'未知';
                $csvList[$key][] = $order_pay_status;

                $delivery_status = isset($delivery_status_list[$val['delivery_status']])?$delivery_status_list[$val['delivery_status']]:'未知';
                $csvList[$key][] = $delivery_status;

                $send_good_status = isset($send_good_status_list[$val['send_good_status']])?$send_good_status_list[$val['send_good_status']]:'未知';
                $csvList[$key][] = $send_good_status;

                $csvList[$key][] = $val['pay_date'];
                $csvList[$key][] = $val['send_time'];
                $csvList[$key][] = $this->diffDate($val['send_time'], $val['pay_date']);
    		}
        }
    	$util->downloadCsv('发货时间统计报表订单详情',$title,$csvList);
    }

    //计算两个时间差
    public function diffDate($send_time, $pay_date)
    {
        $one = strtotime($send_time);
        $tow = strtotime($pay_date);
        $cle = $one - $tow;
        $h = 0;
        if($cle>0){
            
            $h = round($cle/86400, 2);
        }
        return $h;
    }
}
