<?php

/**
 * 平均发货时间统计报表
 *
 * 
 */
class AvgDeliverTimeReportController extends Controller
{
	function __construct(){
		parent::__construct();
		set_time_limit(0);
		ini_set('memory_limit','-1');
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
		$this->render('avg_deliver_time_search_form.html',array('bar'=>Auth::getBar('index')));
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
        $dia_type=_Request::get("dia_type");
        $qiban_type=_Request::get("qiban_type");

 	 	if(!$department_id) $department_id=_Request::get("department_id");
 	 	$where=array(
 	 			'start_time'=>$start_time,
 	 			'end_time'=>$end_time,
 	 			'channel_class'=>$order_type,
 	 			'time_type'=>$time_type,
 	 			'buchan_type'=>$buchan_type,
 	 			'department_id'=>$department_id,
                'dia_type'=>$dia_type,
                'qiban_type'=>$qiban_type
 	 	);
        $buchan_status = _Request::getString('buchan_status');
        if($buchan_status){
            $where['buchan_status'] = implode(',',$buchan_status);
        }
        if(empty($start_time) || empty($start_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 40){
            die('统计日期不能超过40天!');
        }

        $is_k = true;
        //区分现货钻和期货钻
        $order_dia_type = array();
        if(!empty($dia_type)){
            $order_dia_type = $this->quFenDiaType($time_type, $where);
        }
        if(!empty($order_dia_type)){
            $where['order_dia_type'] = $order_dia_type;
        }elseif(empty($order_dia_type) && $dia_type != ''){
            $is_k = false;
        }

        //区分起版
        $order_qiban_type = array();
        if($qiban_type !== ''){
            $order_qiban_type = $this->quFenQubanType($time_type, $where);
        }
        if(!empty($order_qiban_type)){
            $where['order_qiban_type'] = $order_qiban_type;
        }elseif(empty($order_qiban_type) && $qiban_type !== ''){
            $is_k = false;
        }
        

        //var_dump($dateList);die;

        //不带分页
        $BaseOrderInfoModel=new BaseOrderInfoModel(51);
        $wXorderInfo = array();
        if($time_type == 'add'){
            if($is_k == true){
                $list=$BaseOrderInfoModel->pageListCreateTime($where);
                if(!empty($list)){
                    $sn_Arr = array_column($list, 'order_sn');
                    $wXorderInfo = $this->quFenXianhuoQihuoW($sn_Arr);
                }
            }else{
                $list = array();
            }
            foreach($list as $key => $val){
                $tongji_date = substr($val['create_time'],0,10);
                $order_sn = $val['order_sn'];
                if($val['order_status'] == 2){
                    //var_dump($val);
                    //echo "<hr>";
                    $re_end_time = $val['send_time'];
                    $order_time = $val['pay_date'];
                    $re_end_timestamp = strtotime($re_end_time);
                    $order_timestamp = strtotime($order_time);
                    if($re_end_time != '0000-00-00 00:00:00' && $order_time != '0000-00-00 00:00:00' && $re_end_timestamp > $order_timestamp){
                        $timePart = $re_end_timestamp - $order_timestamp;
                        $dateList[$tongji_date]['cnt']++;
                        $dateList[$tongji_date]['dotime'] = $tongji_date;
                        $dateList[$tongji_date]['sum_deliver_time'] += $timePart;
                        //是否不需布产
                        $bx_buchan = $val['buchan_status'] == 5?true:false;
                        if($val['is_xianhuo'] == 1){
                            $dateList[$tongji_date]['xianhuo_num']++;
                            $dateList[$tongji_date]['sum_xianhuo_deliver_time'] += $timePart;
                            if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                $dateList[$tongji_date]['repair_xianhuo_order_num']++;
                                $dateList[$tongji_date]['sum_repair_xianhuo_deliver_time'] += $timePart;
                            }
                            if($val['channel_class'] == 1){
                                $dateList[$tongji_date]['online_order_num']++;
                                $dateList[$tongji_date]['online_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_online_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['sum_online_xianhuo_deliver_time'] += $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['online_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_online_repair_xianhuo_deliver_time'] += $timePart;
                                }
                            }elseif($val['channel_class'] == 2){
                                $dateList[$tongji_date]['offline_order_num']++;
                                $dateList[$tongji_date]['offline_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_offline_deliver_time']+= $timePart;
                                $dateList[$tongji_date]['sum_offline_xianhuo_deliver_time']+= $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['offline_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_offline_repair_xianhuo_deliver_time']+= $timePart;
                                }
                            }
                        }else{
                            if($val['channel_class'] == 1 && $bx_buchan == false){
                                $dateList[$tongji_date]['qihuo_num']++;
                                $dateList[$tongji_date]['sum_qihuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['online_order_num']++;
                                $dateList[$tongji_date]['online_qihuo_num']++;
                                $dateList[$tongji_date]['sum_online_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['sum_online_qihuo_deliver_time'] += $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_qihuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_qihuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['online_repair_qihuo_num']++;
                                    $dateList[$tongji_date]['sum_online_repair_qihuo_deliver_time'] += $timePart;
                                }
                           }elseif($val['channel_class'] == 2 && $bx_buchan == false){
                                $dateList[$tongji_date]['qihuo_num']++;
                                $dateList[$tongji_date]['sum_qihuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['offline_order_num']++;
                                $dateList[$tongji_date]['offline_qihuo_num']++;
                                $dateList[$tongji_date]['sum_offline_deliver_time']+= $timePart;
                                $dateList[$tongji_date]['sum_offline_qihuo_deliver_time']+= $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_qihuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_qihuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['offline_repair_qihuo_num']++;
                                    $dateList[$tongji_date]['sum_offline_repair_qihuo_deliver_time']+= $timePart;
                                }
                            }elseif($val['channel_class'] == 1 && $bx_buchan == true){
                                $dateList[$tongji_date]['xianhuo_num']++;
                                $dateList[$tongji_date]['sum_xianhuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['online_order_num']++;
                                $dateList[$tongji_date]['online_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_online_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['sum_online_xianhuo_deliver_time'] += $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_xianhuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_xianhuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['online_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_online_repair_xianhuo_deliver_time'] += $timePart;
                                }
                            }elseif($val['channel_class'] == 2 && $bx_buchan == true){
                                $dateList[$tongji_date]['xianhuo_num']++;
                                $dateList[$tongji_date]['sum_xianhuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['offline_order_num']++;
                                $dateList[$tongji_date]['offline_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_offline_deliver_time']+= $timePart;
                                $dateList[$tongji_date]['sum_offline_xianhuo_deliver_time']+= $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_xianhuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_xianhuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['offline_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_offline_repair_xianhuo_deliver_time']+= $timePart;
                                }
                            }
                        }
                    }
                }
            }

        }else{
            if($is_k == true){
                $list = $BaseOrderInfoModel->pageListSendTime($where);
                if(!empty($list)){
                    $sn_Arr = array_column($list, 'order_sn');
                    $wXorderInfo = $this->quFenXianhuoQihuoW($sn_Arr);
                }
            }else{
                $list = array();
            }
            foreach($list as $key => $val){
                $tongji_date = substr($val['send_time'],0,10);
                if($val['order_status'] == 2){
                    //var_dump($val);
                    //echo "<hr>";
                    $re_end_time = $val['send_time'];
                    $order_time = $val['pay_date'];
                    $re_end_timestamp = strtotime($re_end_time);
                    $order_timestamp = strtotime($order_time);
                    if($re_end_time != '0000-00-00 00:00:00' && $order_time != '0000-00-00 00:00:00' && $re_end_timestamp > $order_timestamp){
                        $timePart = $re_end_timestamp - $order_timestamp;
                        $dateList[$tongji_date]['cnt']++;
                        $dateList[$tongji_date]['dotime'] = $tongji_date;
                        $dateList[$tongji_date]['sum_deliver_time'] += $timePart;
                        //是否不需布产
                        $bx_buchan = $val['buchan_status'] == 5?true:false;
                        if($val['is_xianhuo'] == 1){
                            $dateList[$tongji_date]['xianhuo_num']++;
                            $dateList[$tongji_date]['sum_xianhuo_deliver_time'] += $timePart;
                            if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                $dateList[$tongji_date]['repair_xianhuo_order_num']++;
                                $dateList[$tongji_date]['sum_repair_xianhuo_deliver_time'] += $timePart;
                            }
                            if($val['channel_class'] == 1){
                                $dateList[$tongji_date]['online_order_num']++;
                                $dateList[$tongji_date]['online_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_online_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['sum_online_xianhuo_deliver_time'] += $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['online_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_online_repair_xianhuo_deliver_time'] += $timePart;
                                }
                            }elseif($val['channel_class'] == 2){
                                $dateList[$tongji_date]['offline_order_num']++;
                                $dateList[$tongji_date]['offline_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_offline_deliver_time']+= $timePart;
                                $dateList[$tongji_date]['sum_offline_xianhuo_deliver_time']+= $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['offline_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_offline_repair_xianhuo_deliver_time']+= $timePart;
                                }
                            }
                        }else{
                            if($val['channel_class'] == 1 && $bx_buchan == false){
                                $dateList[$tongji_date]['qihuo_num']++;
                                $dateList[$tongji_date]['sum_qihuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['online_order_num']++;
                                $dateList[$tongji_date]['online_qihuo_num']++;
                                $dateList[$tongji_date]['sum_online_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['sum_online_qihuo_deliver_time'] += $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_qihuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_qihuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['online_repair_qihuo_num']++;
                                    $dateList[$tongji_date]['sum_online_repair_qihuo_deliver_time'] += $timePart;
                                }
                           }elseif($val['channel_class'] == 2 && $bx_buchan == false){
                                $dateList[$tongji_date]['qihuo_num']++;
                                $dateList[$tongji_date]['sum_qihuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['offline_order_num']++;
                                $dateList[$tongji_date]['offline_qihuo_num']++;
                                $dateList[$tongji_date]['sum_offline_deliver_time']+= $timePart;
                                $dateList[$tongji_date]['sum_offline_qihuo_deliver_time']+= $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_qihuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_qihuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['offline_repair_qihuo_num']++;
                                    $dateList[$tongji_date]['sum_offline_repair_qihuo_deliver_time']+= $timePart;
                                }
                            }elseif($val['channel_class'] == 1 && $bx_buchan == true){
                                $dateList[$tongji_date]['xianhuo_num']++;
                                $dateList[$tongji_date]['sum_xianhuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['online_order_num']++;
                                $dateList[$tongji_date]['online_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_online_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['sum_online_xianhuo_deliver_time'] += $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_xianhuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_xianhuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['online_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_online_repair_xianhuo_deliver_time'] += $timePart;
                                }
                            }elseif($val['channel_class'] == 2 && $bx_buchan == true){
                                $dateList[$tongji_date]['xianhuo_num']++;
                                $dateList[$tongji_date]['sum_xianhuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['offline_order_num']++;
                                $dateList[$tongji_date]['offline_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_offline_deliver_time']+= $timePart;
                                $dateList[$tongji_date]['sum_offline_xianhuo_deliver_time']+= $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_xianhuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_xianhuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['offline_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_offline_repair_xianhuo_deliver_time']+= $timePart;
                                }
                            }
                        }
                    }
                }
            }
        }

        $tongji = array();
        $tongji['cnt'] = 0;
        $tongji['xianhuo_num'] = 0;
        $tongji['qihuo_num'] = 0;
        $tongji['repair_xianhuo_order_num'] = 0;
        $tongji['repair_qihuo_order_num'] = 0;

        $tongji['avg_deliver_time'] = 0;
        $tongji['avg_xianhuo_deliver_time'] = 0;
        $tongji['avg_qihuo_deliver_time'] = 0;
        $tongji['avg_repair_xianhuo_deliver_time'] = 0;
        $tongji['avg_repair_qihuo_deliver_time'] = 0;

        $tongji['online_order_num'] = 0;
        $tongji['online_xianhuo_num'] = 0;
        $tongji['online_qihuo_num'] = 0;
        $tongji['online_repair_xianhuo_num'] = 0;
        $tongji['online_repair_qihuo_num'] = 0;

        $tongji['avg_online_deliver_time'] = 0;
        $tongji['avg_online_xianhuo_deliver_time'] = 0;
        $tongji['avg_online_qihuo_deliver_time'] = 0;
        $tongji['avg_online_repair_xianhuo_deliver_time'] = 0;
        $tongji['avg_online_repair_qihuo_deliver_time'] = 0;

        $tongji['offline_order_num'] = 0;
        $tongji['offline_xianhuo_num'] = 0;
        $tongji['offline_qihuo_num'] = 0;
        $tongji['offline_repair_xianhuo_num'] = 0;
        $tongji['offline_repair_qihuo_num'] = 0;
  
        $tongji['avg_offline_deliver_time']=0;
        $tongji['avg_offline_xianhuo_deliver_time']=0;
        $tongji['avg_offline_qihuo_deliver_time']=0;
        $tongji['avg_offline_repair_xianhuo_deliver_time']=0;
        $tongji['avg_offline_repair_qihuo_deliver_time']=0;


        $tongji['sum_deliver_time'] = 0;
        $tongji['sum_xianhuo_deliver_time'] = 0;
        $tongji['sum_qihuo_deliver_time'] = 0;
        $tongji['sum_repair_xianhuo_deliver_time'] = 0;
        $tongji['sum_repair_qihuo_deliver_time'] = 0;

        $tongji['sum_online_deliver_time'] = 0;
        $tongji['sum_online_xianhuo_deliver_time'] = 0;
        $tongji['sum_online_qihuo_deliver_time'] = 0;
        $tongji['sum_online_repair_xianhuo_deliver_time'] = 0;
        $tongji['sum_online_repair_qihuo_deliver_time'] = 0;
  
        $tongji['sum_offline_deliver_time']=0;
        $tongji['sum_offline_xianhuo_deliver_time']=0;
        $tongji['sum_offline_qihuo_deliver_time']=0;
        $tongji['sum_offline_repair_xianhuo_deliver_time']=0;
        $tongji['sum_offline_repair_qihuo_deliver_time']=0;
        
        foreach($dateList as $key => & $list){
            $list['avg_deliver_time'] = $this->getAvg($list['sum_deliver_time'],$list['cnt']);
            $list['avg_xianhuo_deliver_time'] = $this->getAvg($list['sum_xianhuo_deliver_time'],$list['xianhuo_num']);
            $list['avg_qihuo_deliver_time'] = $this->getAvg($list['sum_qihuo_deliver_time'],$list['qihuo_num']);
            $list['avg_repair_xianhuo_deliver_time'] = $this->getAvg($list['sum_repair_xianhuo_deliver_time'],$list['repair_xianhuo_order_num']);
            $list['avg_repair_qihuo_deliver_time'] = $this->getAvg($list['sum_repair_qihuo_deliver_time'],$list['repair_qihuo_order_num']);
            
            $tongji['cnt'] += $list['cnt'];  
            $tongji['xianhuo_num'] += $list['xianhuo_num'];  
            $tongji['qihuo_num'] += $list['qihuo_num'];  
            $tongji['repair_xianhuo_order_num'] += $list['repair_xianhuo_order_num'];  
            $tongji['repair_qihuo_order_num'] += $list['repair_qihuo_order_num'];  


            $tongji['sum_deliver_time'] += $list['sum_deliver_time'];  
            $tongji['sum_xianhuo_deliver_time'] += $list['sum_xianhuo_deliver_time'];  
            $tongji['sum_qihuo_deliver_time'] += $list['sum_qihuo_deliver_time'];  
            $tongji['sum_repair_xianhuo_deliver_time'] += $list['sum_repair_xianhuo_deliver_time'];  
            $tongji['sum_repair_qihuo_deliver_time'] += $list['sum_repair_qihuo_deliver_time'];  
            unset($list['sum_deliver_time']);
            unset($list['sum_xianhuo_deliver_time']);
            unset($list['sum_qihuo_deliver_time']);
            unset($list['sum_repair_xianhuo_deliver_time']);
            unset($list['sum_repair_qihuo_deliver_time']);


            $list['avg_online_deliver_time'] = $this->getAvg($list['sum_online_deliver_time'],$list['online_order_num']);
            $list['avg_online_xianhuo_deliver_time'] = $this->getAvg($list['sum_online_xianhuo_deliver_time'],$list['online_xianhuo_num']);
            $list['avg_online_qihuo_deliver_time'] = $this->getAvg($list['sum_online_qihuo_deliver_time'],$list['online_qihuo_num']);
            $list['avg_online_repair_xianhuo_deliver_time'] = $this->getAvg($list['sum_online_repair_xianhuo_deliver_time'],$list['online_repair_xianhuo_num']);
            $list['avg_online_repair_qihuo_deliver_time'] = $this->getAvg($list['sum_online_repair_qihuo_deliver_time'],$list['online_repair_qihuo_num']);

            $tongji['online_order_num'] += $list['online_order_num'];  
            $tongji['online_xianhuo_num'] += $list['online_xianhuo_num'];  
            $tongji['online_qihuo_num'] += $list['online_qihuo_num'];  
            $tongji['online_repair_xianhuo_num'] += $list['online_repair_xianhuo_num'];  
            $tongji['online_repair_qihuo_num'] += $list['online_repair_qihuo_num'];  

            $tongji['sum_online_deliver_time'] += $list['sum_online_deliver_time'];  
            $tongji['sum_online_xianhuo_deliver_time'] += $list['sum_online_xianhuo_deliver_time'];  
            $tongji['sum_online_qihuo_deliver_time'] += $list['sum_online_qihuo_deliver_time'];  
            $tongji['sum_online_repair_xianhuo_deliver_time'] += $list['sum_online_repair_xianhuo_deliver_time'];  
            $tongji['sum_online_repair_qihuo_deliver_time'] += $list['sum_online_repair_qihuo_deliver_time'];  
            unset($list['sum_online_deliver_time']);
            unset($list['sum_online_xianhuo_deliver_time']);
            unset($list['sum_online_qihuo_deliver_time']);
            unset($list['sum_online_repair_xianhuo_deliver_time']);
            unset($list['sum_online_repair_qihuo_deliver_time']);

            $list['avg_offline_deliver_time'] = $this->getAvg($list['sum_offline_deliver_time'],$list['offline_order_num']);
            $list['avg_offline_xianhuo_deliver_time'] = $this->getAvg($list['sum_offline_xianhuo_deliver_time'],$list['offline_xianhuo_num']);
            $list['avg_offline_qihuo_deliver_time'] = $this->getAvg($list['sum_offline_qihuo_deliver_time'],$list['offline_qihuo_num']);
            $list['avg_offline_repair_xianhuo_deliver_time'] = $this->getAvg($list['sum_offline_repair_xianhuo_deliver_time'],$list['offline_repair_xianhuo_num']);
            $list['avg_offline_repair_qihuo_deliver_time'] = $this->getAvg($list['sum_offline_repair_qihuo_deliver_time'],$list['offline_repair_qihuo_num']);

            $tongji['offline_order_num'] += $list['offline_order_num'];  
            $tongji['offline_xianhuo_num'] += $list['offline_xianhuo_num'];  
            $tongji['offline_qihuo_num'] += $list['offline_qihuo_num'];  
            $tongji['offline_repair_xianhuo_num'] += $list['offline_repair_xianhuo_num'];  
            $tongji['offline_repair_qihuo_num'] += $list['offline_repair_qihuo_num'];  
            $tongji['sum_offline_deliver_time'] += $list['sum_offline_deliver_time'];  
            $tongji['sum_offline_xianhuo_deliver_time'] += $list['sum_offline_xianhuo_deliver_time'];  
            $tongji['sum_offline_qihuo_deliver_time'] += $list['sum_offline_qihuo_deliver_time'];  
            $tongji['sum_offline_repair_xianhuo_deliver_time'] += $list['sum_offline_repair_xianhuo_deliver_time'];  
            $tongji['sum_offline_repair_qihuo_deliver_time'] += $list['sum_offline_repair_qihuo_deliver_time'];  
            unset($list['sum_offline_deliver_time']);
            unset($list['sum_offline_xianhuo_deliver_time']);
            unset($list['sum_offline_qihuo_deliver_time']);
            unset($list['sum_offline_repair_xianhuo_deliver_time']);
            unset($list['sum_offline_repair_qihuo_deliver_time']);
        }

        $tongji['avg_deliver_time'] = $this->getAvg($tongji['sum_deliver_time'],$tongji['cnt']);
        $tongji['avg_xianhuo_deliver_time'] = $this->getAvg($tongji['sum_xianhuo_deliver_time'],$tongji['xianhuo_num']);
        $tongji['avg_qihuo_deliver_time'] = $this->getAvg($tongji['sum_qihuo_deliver_time'],$tongji['qihuo_num']);
        $tongji['avg_repair_xianhuo_deliver_time'] = $this->getAvg($tongji['sum_repair_xianhuo_deliver_time'],$tongji['repair_xianhuo_order_num']);
        $tongji['avg_repair_qihuo_deliver_time'] = $this->getAvg($tongji['sum_repair_qihuo_deliver_time'],$tongji['repair_qihuo_order_num']);
        $tongji['avg_online_deliver_time'] = $this->getAvg($tongji['sum_online_deliver_time'],$tongji['online_order_num']);
        $tongji['avg_online_xianhuo_deliver_time'] = $this->getAvg($tongji['sum_online_xianhuo_deliver_time'],$tongji['online_xianhuo_num']);
        $tongji['avg_online_qihuo_deliver_time'] = $this->getAvg($tongji['sum_online_qihuo_deliver_time'],$tongji['online_qihuo_num']);
        $tongji['avg_online_repair_xianhuo_deliver_time'] = $this->getAvg($tongji['sum_online_repair_xianhuo_deliver_time'],$tongji['online_repair_xianhuo_num']);
        $tongji['avg_online_repair_qihuo_deliver_time'] = $this->getAvg($tongji['sum_online_repair_qihuo_deliver_time'],$tongji['online_repair_qihuo_num']);
        $tongji['avg_offline_deliver_time'] = $this->getAvg($tongji['sum_offline_deliver_time'],$tongji['offline_order_num']);
        $tongji['avg_offline_xianhuo_deliver_time'] = $this->getAvg($tongji['sum_offline_xianhuo_deliver_time'],$tongji['offline_xianhuo_num']);
        $tongji['avg_offline_qihuo_deliver_time'] = $this->getAvg($tongji['sum_offline_qihuo_deliver_time'],$tongji['offline_qihuo_num']);
        $tongji['avg_offline_repair_xianhuo_deliver_time'] = $this->getAvg($tongji['sum_offline_repair_xianhuo_deliver_time'],$tongji['offline_repair_xianhuo_num']);
        $tongji['avg_offline_repair_qihuo_deliver_time'] = $this->getAvg($tongji['sum_offline_repair_qihuo_deliver_time'],$tongji['offline_repair_qihuo_num']);

        $data = $dateList;
        //echo "<pre>";
        //var_dump($data);die;
        krsort($data);

        $pageData = array();
        $pageData['data'] = $data;
        $pageData['filter'] = $where;
        $pageData['jsFuncs'] = 'deliver_time_search_page';
        $this->render('avg_deliver_time_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $pageData,
        	'tongji'=>$tongji,
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
 	 	$time_type=_Request::getString("time_type")?_Request::getString("time_type"):'add';
 	 	$order_type=_Request::getString("order_type");
 	 	if(!$order_type) $order_type=_Request::getString("channel_class");
 	 	$buchan_type=_Request::get("buchan_type");
 	 	$department_id=_Request::get("order_department");
 	 	if(!$department_id) $department_id=_Request::get("department_id");
        $dia_type=_Request::get("dia_type");
        $qiban_type=_Request::get("qiban_type");
 	 	$where=array(
 	 			'start_time'=>$start_time,
 	 			'end_time'=>$end_time,
 	 			'channel_class'=>$order_type,
 	 			'time_type'=>$time_type,
 	 			'buchan_type'=>$buchan_type,
 	 			'department_id'=>$department_id,
                'dia_type'=>$dia_type,
                'qiban_type'=>$qiban_type
 	 	);
        $buchan_status = _Request::getString('buchan_status');
        if($buchan_status){
            $where['buchan_status'] = implode(',',$buchan_status);
        }
        if(empty($start_time) || empty($start_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 40){
            die('统计日期不能超过40天!');
        }

        $is_k = true;
        //区分现货钻和期货钻
        $order_dia_type = array();
        if(!empty($dia_type)){
            $order_dia_type = $this->quFenDiaType($time_type, $where);
        }
        if(!empty($order_dia_type)){
            $where['order_dia_type'] = $order_dia_type;
        }elseif(empty($order_dia_type) && $dia_type != ''){
            $is_k = false;
        }

        //区分起版
        $order_qiban_type = array();
        if($qiban_type !== ''){
            $order_qiban_type = $this->quFenQubanType($time_type, $where);
        }
        if(!empty($order_qiban_type)){
            $where['order_qiban_type'] = $order_qiban_type;
        }elseif(empty($order_qiban_type) && $qiban_type !== ''){
            $is_k = false;
        }

        //var_dump($dateList);die;

        //不带分页
        $BaseOrderInfoModel=new BaseOrderInfoModel(51);
        $wXorderInfo = array();
        if($time_type == 'add'){
            if($is_k == true){
                $list = $BaseOrderInfoModel->pageListCreateTime($where);
                if(!empty($list)){
                    $sn_Arr = array_column($list, 'order_sn');
                    $wXorderInfo = $this->quFenXianhuoQihuoW($sn_Arr);
                }
            }else{
                $list = array();
            }

            foreach($list as $key => $val){
                $tongji_date = substr($val['create_time'],0,10);
                $order_sn = $val['order_sn'];
                if($val['order_status'] == 2){
                    //var_dump($val);
                    //echo "<hr>";
                    $re_end_time = $val['send_time'];
                    $order_time = $val['pay_date'];
                    $re_end_timestamp = strtotime($re_end_time);
                    $order_timestamp = strtotime($order_time);
                    if($re_end_time != '0000-00-00 00:00:00' && $order_time != '0000-00-00 00:00:00' && $re_end_timestamp > $order_timestamp){
                        $timePart = $re_end_timestamp - $order_timestamp;
                        $dateList[$tongji_date]['cnt']++;
                        $dateList[$tongji_date]['dotime'] = $tongji_date;
                        $dateList[$tongji_date]['sum_deliver_time'] += $timePart;
                        //是否不需布产
                        $bx_buchan = $val['buchan_status'] == 5?true:false;

                        if($val['is_xianhuo'] == 1){
                            $dateList[$tongji_date]['xianhuo_num']++;
                            $dateList[$tongji_date]['sum_xianhuo_deliver_time'] += $timePart;
                            if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                $dateList[$tongji_date]['repair_xianhuo_order_num']++;
                                $dateList[$tongji_date]['sum_repair_xianhuo_deliver_time'] += $timePart;
                            }
                            if($val['channel_class'] == 1){
                                $dateList[$tongji_date]['online_order_num']++;
                                $dateList[$tongji_date]['online_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_online_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['sum_online_xianhuo_deliver_time'] += $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['online_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_online_repair_xianhuo_deliver_time'] += $timePart;
                                }
                            }elseif($val['channel_class'] == 2){
                                $dateList[$tongji_date]['offline_order_num']++;
                                $dateList[$tongji_date]['offline_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_offline_deliver_time']+= $timePart;
                                $dateList[$tongji_date]['sum_offline_xianhuo_deliver_time']+= $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['offline_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_offline_repair_xianhuo_deliver_time']+= $timePart;
                                }
                            }
                        }else{
                            if($val['channel_class'] == 1 && $bx_buchan == false){
                                $dateList[$tongji_date]['qihuo_num']++;
                                $dateList[$tongji_date]['sum_qihuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['online_order_num']++;
                                $dateList[$tongji_date]['online_qihuo_num']++;
                                $dateList[$tongji_date]['sum_online_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['sum_online_qihuo_deliver_time'] += $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_qihuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_qihuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['online_repair_qihuo_num']++;
                                    $dateList[$tongji_date]['sum_online_repair_qihuo_deliver_time'] += $timePart;
                                }
                           }elseif($val['channel_class'] == 2 && $bx_buchan == false){
                                $dateList[$tongji_date]['qihuo_num']++;
                                $dateList[$tongji_date]['sum_qihuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['offline_order_num']++;
                                $dateList[$tongji_date]['offline_qihuo_num']++;
                                $dateList[$tongji_date]['sum_offline_deliver_time']+= $timePart;
                                $dateList[$tongji_date]['sum_offline_qihuo_deliver_time']+= $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_qihuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_qihuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['offline_repair_qihuo_num']++;
                                    $dateList[$tongji_date]['sum_offline_repair_qihuo_deliver_time']+= $timePart;
                                }
                            }elseif($val['channel_class'] == 1 && $bx_buchan == true){
                                $dateList[$tongji_date]['xianhuo_num']++;
                                $dateList[$tongji_date]['sum_xianhuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['online_order_num']++;
                                $dateList[$tongji_date]['online_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_online_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['sum_online_xianhuo_deliver_time'] += $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_xianhuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_xianhuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['online_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_online_repair_xianhuo_deliver_time'] += $timePart;
                                }
                            }elseif($val['channel_class'] == 2 && $bx_buchan == true){
                                $dateList[$tongji_date]['xianhuo_num']++;
                                $dateList[$tongji_date]['sum_xianhuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['offline_order_num']++;
                                $dateList[$tongji_date]['offline_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_offline_deliver_time']+= $timePart;
                                $dateList[$tongji_date]['sum_offline_xianhuo_deliver_time']+= $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_xianhuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_xianhuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['offline_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_offline_repair_xianhuo_deliver_time']+= $timePart;
                                }
                            }
                        }
                    }
                }
            }

        }else{

            if($is_k == true){
                $list = $BaseOrderInfoModel->pageListSendTime($where);
                if(!empty($list)){
                    $sn_Arr = array_column($list, 'order_sn');
                    $wXorderInfo = $this->quFenXianhuoQihuoW($sn_Arr);
                }
            }else{
                $list = array();
            }
            foreach($list as $key => $val){
                $tongji_date = substr($val['send_time'],0,10);
                if($val['order_status'] == 2){
                    //var_dump($val);
                    //echo "<hr>";
                    $re_end_time = $val['send_time'];
                    $order_time = $val['pay_date'];
                    $re_end_timestamp = strtotime($re_end_time);
                    $order_timestamp = strtotime($order_time);
                    if($re_end_time != '0000-00-00 00:00:00' && $order_time != '0000-00-00 00:00:00' && $re_end_timestamp > $order_timestamp){
                        $timePart = $re_end_timestamp - $order_timestamp;
                        $dateList[$tongji_date]['cnt']++;
                        $dateList[$tongji_date]['dotime'] = $tongji_date;
                        $dateList[$tongji_date]['sum_deliver_time'] += $timePart;
                        //是否不需布产
                        $bx_buchan = $val['buchan_status'] == 5?true:false;

                        if($val['is_xianhuo'] == 1){
                            $dateList[$tongji_date]['xianhuo_num']++;
                            $dateList[$tongji_date]['sum_xianhuo_deliver_time'] += $timePart;
                            if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                $dateList[$tongji_date]['repair_xianhuo_order_num']++;
                                $dateList[$tongji_date]['sum_repair_xianhuo_deliver_time'] += $timePart;
                            }
                            if($val['channel_class'] == 1){
                                $dateList[$tongji_date]['online_order_num']++;
                                $dateList[$tongji_date]['online_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_online_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['sum_online_xianhuo_deliver_time'] += $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['online_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_online_repair_xianhuo_deliver_time'] += $timePart;
                                }
                            }elseif($val['channel_class'] == 2){
                                $dateList[$tongji_date]['offline_order_num']++;
                                $dateList[$tongji_date]['offline_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_offline_deliver_time']+= $timePart;
                                $dateList[$tongji_date]['sum_offline_xianhuo_deliver_time']+= $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['offline_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_offline_repair_xianhuo_deliver_time']+= $timePart;
                                }
                            }
                        }else{
                            if($val['channel_class'] == 1 && $bx_buchan == false){
                                $dateList[$tongji_date]['qihuo_num']++;
                                $dateList[$tongji_date]['sum_qihuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['online_order_num']++;
                                $dateList[$tongji_date]['online_qihuo_num']++;
                                $dateList[$tongji_date]['sum_online_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['sum_online_qihuo_deliver_time'] += $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_qihuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_qihuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['online_repair_qihuo_num']++;
                                    $dateList[$tongji_date]['sum_online_repair_qihuo_deliver_time'] += $timePart;
                                }
                           }elseif($val['channel_class'] == 2 && $bx_buchan == false){
                                $dateList[$tongji_date]['qihuo_num']++;
                                $dateList[$tongji_date]['sum_qihuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['offline_order_num']++;
                                $dateList[$tongji_date]['offline_qihuo_num']++;
                                $dateList[$tongji_date]['sum_offline_deliver_time']+= $timePart;
                                $dateList[$tongji_date]['sum_offline_qihuo_deliver_time']+= $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_qihuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_qihuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['offline_repair_qihuo_num']++;
                                    $dateList[$tongji_date]['sum_offline_repair_qihuo_deliver_time']+= $timePart;
                                }
                            }elseif($val['channel_class'] == 1 && $bx_buchan == true){
                                $dateList[$tongji_date]['xianhuo_num']++;
                                $dateList[$tongji_date]['sum_xianhuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['online_order_num']++;
                                $dateList[$tongji_date]['online_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_online_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['sum_online_xianhuo_deliver_time'] += $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_xianhuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_xianhuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['online_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_online_repair_xianhuo_deliver_time'] += $timePart;
                                }
                            }elseif($val['channel_class'] == 2 && $bx_buchan == true){
                                $dateList[$tongji_date]['xianhuo_num']++;
                                $dateList[$tongji_date]['sum_xianhuo_deliver_time'] += $timePart;
                                $dateList[$tongji_date]['offline_order_num']++;
                                $dateList[$tongji_date]['offline_xianhuo_num']++;
                                $dateList[$tongji_date]['sum_offline_deliver_time']+= $timePart;
                                $dateList[$tongji_date]['sum_offline_xianhuo_deliver_time']+= $timePart;
                                if(isset($wXorderInfo[$order_sn]) && $wXorderInfo[$order_sn] == true){
                                    $dateList[$tongji_date]['repair_xianhuo_order_num']++;
                                    $dateList[$tongji_date]['sum_repair_xianhuo_deliver_time'] += $timePart;
                                    $dateList[$tongji_date]['offline_repair_xianhuo_num']++;
                                    $dateList[$tongji_date]['sum_offline_repair_xianhuo_deliver_time']+= $timePart;
                                }
                            }
                        }
                    }
                }
            }
        }

        $tongji = array();
        $tongji['dotime'] = '总计';
        $tongji['cnt'] = 0;
        $tongji['xianhuo_num'] = 0;
        $tongji['qihuo_num'] = 0;
        $tongji['repair_xianhuo_order_num'] = 0;
        $tongji['repair_qihuo_order_num'] = 0;

        $tongji['avg_deliver_time'] = 0;
        $tongji['avg_xianhuo_deliver_time'] = 0;
        $tongji['avg_qihuo_deliver_time'] = 0;
        $tongji['avg_repair_xianhuo_deliver_time'] = 0;
        $tongji['avg_repair_qihuo_deliver_time'] = 0;

        $tongji['online_order_num'] = 0;
        $tongji['online_xianhuo_num'] = 0;
        $tongji['online_qihuo_num'] = 0;
        $tongji['online_repair_xianhuo_num'] = 0;
        $tongji['online_repair_qihuo_num'] = 0;

        $tongji['avg_online_deliver_time'] = 0;
        $tongji['avg_online_xianhuo_deliver_time'] = 0;
        $tongji['avg_online_qihuo_deliver_time'] = 0;
        $tongji['avg_online_repair_xianhuo_deliver_time'] = 0;
        $tongji['avg_online_repair_qihuo_deliver_time'] = 0;

        $tongji['offline_order_num'] = 0;
        $tongji['offline_xianhuo_num'] = 0;
        $tongji['offline_qihuo_num'] = 0;
        $tongji['offline_repair_xianhuo_num'] = 0;
        $tongji['offline_repair_qihuo_num'] = 0;
  
        $tongji['avg_offline_deliver_time']=0;
        $tongji['avg_offline_xianhuo_deliver_time']=0;
        $tongji['avg_offline_qihuo_deliver_time']=0;
        $tongji['avg_offline_repair_xianhuo_deliver_time']=0;
        $tongji['avg_offline_repair_qihuo_deliver_time']=0;


        $tongji['sum_deliver_time'] = 0;
        $tongji['sum_xianhuo_deliver_time'] = 0;
        $tongji['sum_qihuo_deliver_time'] = 0;
        $tongji['sum_repair_xianhuo_deliver_time'] = 0;
        $tongji['sum_repair_qihuo_deliver_time'] = 0;

        $tongji['sum_online_deliver_time'] = 0;
        $tongji['sum_online_xianhuo_deliver_time'] = 0;
        $tongji['sum_online_qihuo_deliver_time'] = 0;
        $tongji['sum_online_repair_xianhuo_deliver_time'] = 0;
        $tongji['sum_online_repair_qihuo_deliver_time'] = 0;
  
        $tongji['sum_offline_deliver_time']=0;
        $tongji['sum_offline_xianhuo_deliver_time']=0;
        $tongji['sum_offline_qihuo_deliver_time']=0;
        $tongji['sum_offline_repair_xianhuo_deliver_time']=0;
        $tongji['sum_offline_repair_qihuo_deliver_time']=0;
        
        foreach($dateList as $key => & $list){
            $list['avg_deliver_time'] = $this->getAvg($list['sum_deliver_time'],$list['cnt']);
            $list['avg_xianhuo_deliver_time'] = $this->getAvg($list['sum_xianhuo_deliver_time'],$list['xianhuo_num']);
            $list['avg_qihuo_deliver_time'] = $this->getAvg($list['sum_qihuo_deliver_time'],$list['qihuo_num']);
            $list['avg_repair_xianhuo_deliver_time'] = $this->getAvg($list['sum_repair_xianhuo_deliver_time'],$list['repair_xianhuo_order_num']);
            $list['avg_repair_qihuo_deliver_time'] = $this->getAvg($list['sum_repair_qihuo_deliver_time'],$list['repair_qihuo_order_num']);
            
            $tongji['cnt'] += $list['cnt'];  
            $tongji['xianhuo_num'] += $list['xianhuo_num'];  
            $tongji['qihuo_num'] += $list['qihuo_num'];  
            $tongji['repair_xianhuo_order_num'] += $list['repair_xianhuo_order_num'];  
            $tongji['repair_qihuo_order_num'] += $list['repair_qihuo_order_num'];  


            $tongji['sum_deliver_time'] += $list['sum_deliver_time'];  
            $tongji['sum_xianhuo_deliver_time'] += $list['sum_xianhuo_deliver_time'];  
            $tongji['sum_qihuo_deliver_time'] += $list['sum_qihuo_deliver_time'];  
            $tongji['sum_repair_xianhuo_deliver_time'] += $list['sum_repair_xianhuo_deliver_time'];  
            $tongji['sum_repair_qihuo_deliver_time'] += $list['sum_repair_qihuo_deliver_time'];  
            unset($list['sum_deliver_time']);
            unset($list['sum_xianhuo_deliver_time']);
            unset($list['sum_qihuo_deliver_time']);
            unset($list['sum_repair_xianhuo_deliver_time']);
            unset($list['sum_repair_qihuo_deliver_time']);


            $list['avg_online_deliver_time'] = $this->getAvg($list['sum_online_deliver_time'],$list['online_order_num']);
            $list['avg_online_xianhuo_deliver_time'] = $this->getAvg($list['sum_online_xianhuo_deliver_time'],$list['online_xianhuo_num']);
            $list['avg_online_qihuo_deliver_time'] = $this->getAvg($list['sum_online_qihuo_deliver_time'],$list['online_qihuo_num']);
            $list['avg_online_repair_xianhuo_deliver_time'] = $this->getAvg($list['sum_online_repair_xianhuo_deliver_time'],$list['online_repair_xianhuo_num']);
            $list['avg_online_repair_qihuo_deliver_time'] = $this->getAvg($list['sum_online_repair_qihuo_deliver_time'],$list['online_repair_qihuo_num']);

            $tongji['online_order_num'] += $list['online_order_num'];  
            $tongji['online_xianhuo_num'] += $list['online_xianhuo_num'];  
            $tongji['online_qihuo_num'] += $list['online_qihuo_num'];  
            $tongji['online_repair_xianhuo_num'] += $list['online_repair_xianhuo_num'];  
            $tongji['online_repair_qihuo_num'] += $list['online_repair_qihuo_num'];  

            $tongji['sum_online_deliver_time'] += $list['sum_online_deliver_time'];  
            $tongji['sum_online_xianhuo_deliver_time'] += $list['sum_online_xianhuo_deliver_time'];  
            $tongji['sum_online_qihuo_deliver_time'] += $list['sum_online_qihuo_deliver_time'];  
            $tongji['sum_online_repair_xianhuo_deliver_time'] += $list['sum_online_repair_xianhuo_deliver_time'];  
            $tongji['sum_online_repair_qihuo_deliver_time'] += $list['sum_online_repair_qihuo_deliver_time'];  
            unset($list['sum_online_deliver_time']);
            unset($list['sum_online_xianhuo_deliver_time']);
            unset($list['sum_online_qihuo_deliver_time']);
            unset($list['sum_online_repair_xianhuo_deliver_time']);
            unset($list['sum_online_repair_qihuo_deliver_time']);

            $list['avg_offline_deliver_time'] = $this->getAvg($list['sum_offline_deliver_time'],$list['offline_order_num']);
            $list['avg_offline_xianhuo_deliver_time'] = $this->getAvg($list['sum_offline_xianhuo_deliver_time'],$list['offline_xianhuo_num']);
            $list['avg_offline_qihuo_deliver_time'] = $this->getAvg($list['sum_offline_qihuo_deliver_time'],$list['offline_qihuo_num']);
            $list['avg_offline_repair_xianhuo_deliver_time'] = $this->getAvg($list['sum_offline_repair_xianhuo_deliver_time'],$list['offline_repair_xianhuo_num']);
            $list['avg_offline_repair_qihuo_deliver_time'] = $this->getAvg($list['sum_offline_repair_qihuo_deliver_time'],$list['offline_repair_qihuo_num']);

            $tongji['offline_order_num'] += $list['offline_order_num'];  
            $tongji['offline_xianhuo_num'] += $list['offline_xianhuo_num'];  
            $tongji['offline_qihuo_num'] += $list['offline_qihuo_num'];  
            $tongji['offline_repair_xianhuo_num'] += $list['offline_repair_xianhuo_num'];  
            $tongji['offline_repair_qihuo_num'] += $list['offline_repair_qihuo_num'];  
            $tongji['sum_offline_deliver_time'] += $list['sum_offline_deliver_time'];  
            $tongji['sum_offline_xianhuo_deliver_time'] += $list['sum_offline_xianhuo_deliver_time'];  
            $tongji['sum_offline_qihuo_deliver_time'] += $list['sum_offline_qihuo_deliver_time'];  
            $tongji['sum_offline_repair_xianhuo_deliver_time'] += $list['sum_offline_repair_xianhuo_deliver_time'];  
            $tongji['sum_offline_repair_qihuo_deliver_time'] += $list['sum_offline_repair_qihuo_deliver_time'];  
            unset($list['sum_offline_deliver_time']);
            unset($list['sum_offline_xianhuo_deliver_time']);
            unset($list['sum_offline_qihuo_deliver_time']);
            unset($list['sum_offline_repair_xianhuo_deliver_time']);
            unset($list['sum_offline_repair_qihuo_deliver_time']);
        }

        $tongji['avg_deliver_time'] = $this->getAvg($tongji['sum_deliver_time'],$tongji['cnt']);
        $tongji['avg_xianhuo_deliver_time'] = $this->getAvg($tongji['sum_xianhuo_deliver_time'],$tongji['xianhuo_num']);
        $tongji['avg_qihuo_deliver_time'] = $this->getAvg($tongji['sum_qihuo_deliver_time'],$tongji['qihuo_num']);
        $tongji['avg_repair_xianhuo_deliver_time'] = $this->getAvg($tongji['sum_repair_xianhuo_deliver_time'],$tongji['repair_xianhuo_order_num']);
        $tongji['avg_repair_qihuo_deliver_time'] = $this->getAvg($tongji['sum_repair_qihuo_deliver_time'],$tongji['repair_qihuo_order_num']);
        $tongji['avg_online_deliver_time'] = $this->getAvg($tongji['sum_online_deliver_time'],$tongji['online_order_num']);
        $tongji['avg_online_xianhuo_deliver_time'] = $this->getAvg($tongji['sum_online_xianhuo_deliver_time'],$tongji['online_xianhuo_num']);
        $tongji['avg_online_qihuo_deliver_time'] = $this->getAvg($tongji['sum_online_qihuo_deliver_time'],$tongji['online_qihuo_num']);
        $tongji['avg_online_repair_xianhuo_deliver_time'] = $this->getAvg($tongji['sum_online_repair_xianhuo_deliver_time'],$tongji['online_repair_xianhuo_num']);
        $tongji['avg_online_repair_qihuo_deliver_time'] = $this->getAvg($tongji['sum_online_repair_qihuo_deliver_time'],$tongji['online_repair_qihuo_num']);
        $tongji['avg_offline_deliver_time'] = $this->getAvg($tongji['sum_offline_deliver_time'],$tongji['offline_order_num']);
        $tongji['avg_offline_xianhuo_deliver_time'] = $this->getAvg($tongji['sum_offline_xianhuo_deliver_time'],$tongji['offline_xianhuo_num']);
        $tongji['avg_offline_qihuo_deliver_time'] = $this->getAvg($tongji['sum_offline_qihuo_deliver_time'],$tongji['offline_qihuo_num']);
        $tongji['avg_offline_repair_xianhuo_deliver_time'] = $this->getAvg($tongji['sum_offline_repair_xianhuo_deliver_time'],$tongji['offline_repair_xianhuo_num']);
        $tongji['avg_offline_repair_qihuo_deliver_time'] = $this->getAvg($tongji['sum_offline_repair_qihuo_deliver_time'],$tongji['offline_repair_qihuo_num']);


        $data = $dateList;
        //echo "<pre>";
        //var_dump($data);die;
        krsort($data);
        array_push($data,$tongji);

        //var_dump($data);die;

    	
    	if($data){
    		$util=new Util();
    		$title=array('日期','总订单量(现货/期货/维修)','总平均用时(现货/期货/维修)','线上订单量（现货/期货/维修）','线上平均用时(现货/期货/维修)','线下订单量（现货/期货/维修）','线下平均用时（现货/期货/维修）');
    		$csv_data=array();
            foreach($data as &$val){
                $temp=array();
                $temp['dotime']=$val['dotime'];
                $temp['cnt']=$val['cnt'].'('.$val['xianhuo_num'].'/'.$val['qihuo_num'].'/'.$val['repair_xianhuo_order_num'].'/'.$val['repair_qihuo_order_num'].')';
                $temp['avg_deliver_time']=$val['avg_deliver_time'].'('.$val['avg_xianhuo_deliver_time'].'/'.$val['avg_qihuo_deliver_time'].'/'.$val['avg_repair_xianhuo_deliver_time'].'/'.$val['avg_repair_qihuo_deliver_time'].')';
                $temp['online_order_num']=$val['online_order_num'].'('.$val['online_xianhuo_num'].'/'.$val['online_qihuo_num'].'/'.$val['online_repair_xianhuo_num'].'/'.$val['online_repair_qihuo_num'].')';
                $temp['avg_online_deliver_time']=$val['avg_online_deliver_time'].'('.$val['avg_online_xianhuo_deliver_time'].'/'.$val['avg_online_qihuo_deliver_time'].'/'.$val['avg_online_repair_xianhuo_deliver_time'].'/'.$val['avg_online_repair_qihuo_deliver_time'].')';
                $temp['offline_order_num']=$val['offline_order_num'].'('.$val['offline_xianhuo_num'].'/'.$val['offline_qihuo_num'].'/'.$val['offline_repair_xianhuo_num'].'/'.$val['offline_repair_qihuo_num'].')';
                $temp['avg_offline_deliver_time']=$val['avg_offline_deliver_time'].'('.$val['avg_offline_xianhuo_deliver_time'].'/'.$val['avg_offline_qihuo_deliver_time'].'/'.$val['avg_offline_repair_xianhuo_deliver_time'].'/'.$val['avg_offline_repair_qihuo_deliver_time'].')';
                $csv_data[]=$temp;
            }
    		$util->downloadCsv(date('Y-m-d').'平均发货时长统计',$title,$csv_data);
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
            $list['cnt'] = 0;
            $list['xianhuo_num'] = 0;
            $list['qihuo_num'] = 0;
            $list['repair_xianhuo_order_num'] = 0;
            $list['repair_qihuo_order_num'] = 0;

            $list['avg_deliver_time'] = 0;
            $list['avg_xianhuo_deliver_time'] = 0;
            $list['avg_qihuo_deliver_time'] = 0;
            $list['avg_repair_xianhuo_deliver_time'] = 0;
            $list['avg_repair_qihuo_deliver_time'] = 0;

            $list['online_order_num'] = 0;
            $list['online_xianhuo_num'] = 0;
            $list['online_qihuo_num'] = 0;
            $list['online_repair_xianhuo_num'] = 0;
            $list['online_repair_qihuo_num'] = 0;

            $list['avg_online_deliver_time'] = 0;
            $list['avg_online_xianhuo_deliver_time'] = 0;
            $list['avg_online_qihuo_deliver_time'] = 0;
            $list['avg_online_repair_xianhuo_deliver_time'] = 0;
            $list['avg_online_repair_qihuo_deliver_time'] = 0;

            $list['offline_order_num'] = 0;
            $list['offline_xianhuo_num'] = 0;
            $list['offline_qihuo_num'] = 0;
            $list['offline_repair_xianhuo_num'] = 0;
            $list['offline_repair_qihuo_num'] = 0;
      
            $list['avg_offline_deliver_time']=0;
            $list['avg_offline_xianhuo_deliver_time']=0;
            $list['avg_offline_qihuo_deliver_time']=0;
            $list['avg_offline_repair_xianhuo_deliver_time']=0;
            $list['avg_offline_repair_qihuo_deliver_time']=0;


            $list['sum_deliver_time'] = 0;
            $list['sum_xianhuo_deliver_time'] = 0;
            $list['sum_qihuo_deliver_time'] = 0;
            $list['sum_repair_xianhuo_deliver_time'] = 0;
            $list['sum_repair_qihuo_deliver_time'] = 0;

            $list['sum_online_deliver_time'] = 0;
            $list['sum_online_xianhuo_deliver_time'] = 0;
            $list['sum_online_qihuo_deliver_time'] = 0;
            $list['sum_online_repair_xianhuo_deliver_time'] = 0;
            $list['sum_online_repair_qihuo_deliver_time'] = 0;
      
            $list['sum_offline_deliver_time']=0;
            $list['sum_offline_xianhuo_deliver_time']=0;
            $list['sum_offline_qihuo_deliver_time']=0;
            $list['sum_offline_repair_xianhuo_deliver_time']=0;
            $list['sum_offline_repair_qihuo_deliver_time']=0;


            $data_arr[$start_time_str[0]."-".$start_time_str[1]."-".$start_time_str[2]]=$list;
            $start_time_str[2]++;
            $start_time_str = explode("-", date("Y-m-d", mktime(0,0,0,$start_time_str[1],$start_time_str[2],$start_time_str[0])));
        }
        return $data_arr;
    }

    public function getAvg($s,$t)
    {
        $s /= 86400;
        if($t==0){
            return 0;
        }else{
            return round($s/$t,2);
        }
    }

    public function avgdownloadDetail()
    {
        $page = _Request::getInt("page",1);
        $pagesize = (_Post::getInt('page_num'))?_Post::getInt('page_num'):10;
        $start_time =_Request::getString("start_time");
        $end_time =_Request::getString("end_time");
        $time_type=_Request::getString("time_type")?_Request::getString("time_type"):'add';
        $order_type=_Request::getString("order_type");
        if(!$order_type) $order_type=_Request::getString("channel_class");
        $buchan_type=_Request::getString("buchan_type");
        $department_id=_Request::get("order_department");
        if(!$department_id) $department_id=_Request::get("department_id");
        $dia_type=_Request::getString("dia_type");
        $qiban_type=_Request::getString("qiban_type");
        $where=array(
                'start_time'=>$start_time,
                'end_time'=>$end_time,
                'channel_class'=>$order_type,
                'time_type'=>$time_type,
                'buchan_type'=>$buchan_type,
                'department_id'=>$department_id,
                'dia_type'=>$dia_type,
                'qiban_type'=>$qiban_type
        );
        if(empty($start_time) || empty($start_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 40){
            die('统计日期不能超过40天!');
        }

        $is_k = true;
        //区分现货钻和期货钻
        $order_dia_type = array();
        if(!empty($dia_type)){
            $order_dia_type = $this->quFenDiaType($time_type, $where);
        }
        if(!empty($order_dia_type)){
            $where['order_dia_type'] = $order_dia_type;
        }elseif(empty($order_dia_type) && $dia_type != ''){
            $is_k = false;
        }

        //区分起版
        $order_qiban_type = array();
        if($qiban_type !== ''){
            $order_qiban_type = $this->quFenQubanType($time_type, $where);
        }
        if(!empty($order_qiban_type)){
            $where['order_qiban_type'] = $order_qiban_type;
        }elseif(empty($order_qiban_type) && $qiban_type !== ''){
            $is_k = false;
        }

        $buchan_status = _Request::getString('buchan_status');
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
            if($is_k == true){
                $list=$BaseOrderInfoModel->pageListCreateTime($where);
            }else{
                $list = array();
            } 
            foreach($list as $key => $val){
                $re_end_time = $val['send_time'];
                $order_time = $val['pay_date'];
                $re_end_timestamp = strtotime($re_end_time);
                $order_timestamp = strtotime($order_time);
                if($val['order_status'] == 2 && $re_end_time != '0000-00-00 00:00:00' && $order_time != '0000-00-00 00:00:00' && $re_end_timestamp > $order_timestamp){
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
        }else{
            if($is_k == true){
                $list = $BaseOrderInfoModel->pageListSendTime($where);
            }else{
                $list = array();
            }
            foreach($list as $key => $val){
                $re_end_time = $val['send_time'];
                $order_time = $val['pay_date'];
                $re_end_timestamp = strtotime($re_end_time);
                $order_timestamp = strtotime($order_time);
                if($val['order_status'] == 2 && $re_end_time != '0000-00-00 00:00:00' && $order_time != '0000-00-00 00:00:00' && $re_end_timestamp > $order_timestamp){
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
        }
        $util->downloadCsv('平均发货时间统计报表订单详情',$title,$csvList);
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

    //现货期货钻钻区分
    public function quFenDiaType($time_type, $where=array())
    {
        $in_diatype = $where['dia_type'];
        $BaseOrderInfoModel=new BaseOrderInfoModel(51);
        if($time_type == 'add'){
            $data = $BaseOrderInfoModel->getCreateOrderDetailsInfo($where);
        }else{
            $data = $BaseOrderInfoModel->getSendOrderDetailsInfo($where);
        }

        $xhInfo = $qhInfo = array();
        if(!empty($data)){
            foreach ($data as $val) {
                $info[$val['order_sn']][] = $val['dia_type'];
            }

            foreach ($info as $o_sn => $val) {
                $num = count(array_unique($val));
                if($num = 1){
                    if($val[0] == 0){
                        continue;
                    }elseif($val[0] == 1){
                        $xhInfo[] = $o_sn;
                    }elseif($val[0] == 2){
                        $qhInfo[] = $o_sn;
                    }
                }else{
                    $is_xhz = $is_qhz = false;
                    foreach ($val as $v) {
                        if($v == 0){
                            continue;
                        }
                        if($v == 1){
                            $is_xhz = true;
                        }
                        if($v == 2){
                            $is_qhz = true;
                        }
                    }
                    if($is_xhz == true && $is_qhz == true){
                        $qhInfo[] = $o_sn;
                    }
                }
            }
        }

        return $in_diatype == 1?$xhInfo:$qhInfo;
    }

    //区分起版
    public function quFenQubanType($time_type, $where=array())
    {
        $in_qiban_type = $where['qiban_type'];
        $BaseOrderInfoModel=new BaseOrderInfoModel(51);
        if($time_type == 'add'){
            $data = $BaseOrderInfoModel->getCreateOrderDetailsInfo($where);
        }else{
            $data = $BaseOrderInfoModel->getSendOrderDetailsInfo($where);
        }

        $wkqbInfo = $ykqbInfo = $wqbInfo = array();
        if(!empty($data)){
            foreach ($data as $val) {
                $info[$val['order_sn']][] = $val['qiban_type'];
            }

            foreach ($info as $o_sn => $val) {
                $num = count(array_unique($val));
                if($num == 1){
                    if($val[0] == 0){
                        $wkqbInfo[] = $o_sn;
                    }elseif($val[0] == 1){
                        $ykqbInfo[] = $o_sn;
                    }elseif($val[0] == 2){
                        $wqbInfo[] = $o_sn;
                    }
                }else{
                    if(in_array(0, $val)){
                        $wkqbInfo[] = $o_sn;
                        continue;
                    }
                    if(in_array(1, $val)){
                        $ykqbInfo[] = $o_sn;
                        continue;
                    }
                    if(in_array(2, $val)){
                        $wqbInfo[] = $o_sn;
                        continue;
                    }
                }
            }
        }
        if($in_qiban_type == 0){
            return $wkqbInfo;
        }
        if($in_qiban_type == 1){
            return $ykqbInfo;
        }
        if($in_qiban_type == 2){
            return $wqbInfo;
        }
    }

    //区分现货维修和期货维修
    public function quFenXianhuoQihuoW($order_sn)
    {
        $model = new BaseOrderInfoModel(51);
        $data = $model->getQuFenXianQiHuoW($order_sn);
        $is_check = array();
        if(!empty($data)){
            foreach ($data as $val) {
                $info[$val['order_sn']][] = $val['weixiu_status'];
            }

            foreach ($info as $o_sn => $val) {
                $is_weixiu = false;
                foreach ($val as $v) {
                    if($v != '' && $v != 0 && $v != 7){
                        $is_weixiu = true;
                        continue;
                    }
                }
                $is_check[$o_sn] = $is_weixiu;
            }
        }
        return $is_check;
    }
}
