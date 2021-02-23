<?php
/**
 *  -------------------------------------------------
 *   @file		: ReturnGoodsReportController.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  退货率报表
 *  -------------------------------------------------
 */


class ReturnGoodsReportController extends Controller
{
	
	function __construct(){
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
		$this->render('return_goods_search_form.html',array(
				'bar'=>Auth::getBar(),
			));
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
		set_time_limit(0);
		$page = _Request::getInt("page",1);
		$order_type=_Request::getString("order_type");
		$bill_type=_Request::getString("bill_type");
		$where = array(
			'start_time' => _Request::get('start_time'),
			'end_time' => _Request::get('end_time'),
			'channel_class'=>$order_type,
			'bill_type'=>$bill_type,
			'department_id' => _Request::get('department_id'),	
		);

 	 	$start_time =_Request::get("start_time");
 	 	$end_time =_Request::get("end_time");
        if(empty($start_time) || empty($start_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 40){
            die('统计日期不能超过40天!');
        }

        $model = new WarehouseBillModel(55);
		$data = $model->pageReturnGoodslist($where,$page,50,false);
		$pageData = $data;
		$pageData['filter'] = $where;
		$pageData['jsFuncs'] = 'return_goods_search_page';

        if($data['data']){
            $temp = $data['data'];
            foreach($temp as $key => $val){
                $_returnData[$val['channel_class']][$val['do_date']] = $val;
                $empty = array('online_return_count'=>0,'upline_return_count'=>0);
                $returnData[$val['do_date']] = $empty;
            }
            foreach($_returnData as $type => $d){
                foreach($d as $k => $v){
                    $returnData[$k]['online_return_count'] += $v['online_return_count']; 
                    $returnData[$k]['upline_return_count'] += $v['upline_return_count'];
                }
            }
        }

        $data2 = $model->pageSaleGoodsList($where,$page,50,false);
        if($data2['data']){
            $temp = $data2['data'];
            foreach($temp as $key => $val){
                $saleData[$val['do_date']] = $val;
            }
        }

        $retData = array();
        $tongji = array();
        $tongji[] = 0;
        $tongji['type'] = $where['bill_type'];
        $tongji['do_date'] = '总计';
        $tongji['online_return_count'] = 0;
        $tongji['upline_return_count'] = 0;
        $tongji['total_return_count'] = 0;
        $tongji['online_sale_count'] = 0;
        $tongji['upline_sale_count'] = 0;
        $tongji['total_sale_count'] = 0;

        foreach($dateList as $d => $v){
            $r = array();
            $r['type'] = $where['bill_type'];
            $r['do_date'] = $d;
            if(isset($returnData[$d])){
                $r['online_return_count'] = $returnData[$d]['online_return_count'];
                $r['upline_return_count'] = $returnData[$d]['upline_return_count'];
                $r['total_return_count'] = $returnData[$d]['upline_return_count']+$returnData[$d]['online_return_count'];

                $tongji['online_return_count'] += $r['online_return_count'];
                $tongji['upline_return_count'] += $r['upline_return_count'];
                $tongji['total_return_count'] += $r['total_return_count'];

            }else{
                $r['online_return_count'] = 0;
                $r['upline_return_count'] = 0;
                $r['total_return_count'] = 0;
            }
            if(isset($saleData[$d])){
                $r['online_sale_count'] = $saleData[$d]['online_sale_count'];
                $r['upline_sale_count'] = $saleData[$d]['upline_sale_count'];
                $r['total_sale_count'] = $saleData[$d]['upline_sale_count']+$saleData[$d]['online_sale_count'];

                $tongji['online_sale_count'] += $r['online_sale_count'];
                $tongji['upline_sale_count'] += $r['upline_sale_count'];
                $tongji['total_sale_count'] += $r['total_sale_count'];
            }else{
                $r['online_sale_count'] = 0;
                $r['upline_sale_count'] = 0;
                $r['total_sale_count'] = 0;
            }

            $r['r_lv'] = $r['total_sale_count']>0?round($r['total_return_count']/$r['total_sale_count'],4)*100:0;
            $r['ron_lv'] = $r['online_sale_count']>0?round($r['online_return_count']/$r['online_sale_count'],4)*100:0;
            $r['rup_lv'] = $r['upline_sale_count']>0?round($r['upline_return_count']/$r['upline_sale_count'],4)*100:0;
            $retData[$d] = $r;
        }
        $tongji['r_lv'] = $tongji['total_sale_count']>0?round($tongji['total_return_count']/$tongji['total_sale_count'],4)*100:0;
        $tongji['ron_lv'] = $tongji['online_sale_count']>0?round($tongji['online_return_count']/$tongji['online_sale_count'],4)*100:0;
        $tongji['rup_lv'] = $tongji['upline_sale_count']>0?round($tongji['upline_return_count']/$tongji['upline_sale_count'],4)*100:0;

        
        $this->render('return_goods_search_list.html',array(
				'pa'=>Util::page($pageData),
				'page_list'=>$retData,
				'tongji'=>$tongji,
				'arg'=>$where
		));
	}
	/**
	 *	第二层搜索框
	 */
	public function index_second ($params)
	{
		//渠道
		$model = new UserChannelModel(59);
		$data = $model->getChannels($_SESSION['userId'],0);
		$this->assign('onlySale',count($data)==1);
		$this->assign('sales_channels_idData', $data);
		$this->render('return_goods_search_second_form.html',array(
				'bar'=>Auth::getBar(),
		));
	}
	/**
	 *	search_second，第二层列表
	 */
	public function search_second ($params)
	{
		$page = _Request::getInt("page",1);
		set_time_limit(0);
		$page = _Request::getInt("page",1);
		$order_type=_Request::getString("order_type");
		$bill_type=_Request::getString("bill_type");
		$where = array(
			'start_time' => _Request::get('start_time'),
			'end_time' => _Request::get('end_time'),
			'channel_class'=>$order_type,
			'bill_type'=>$bill_type,
			'department_id' => _Request::get('department_id'),	
		);

 	 	$start_time =_Request::get("start_time");
 	 	$end_time =_Request::get("end_time");
        if(empty($start_time) || empty($start_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 40){
            die('统计日期不能超过40天!');
        }

		$model = new WarehouseBillModel(55);
		$data = $model->pageReturnGoodsSecondlist($where,$page,30,false);
		$pageData = $data;
		$pageData['filter'] = array();
		$pageData['jsFuncs'] = 'return_goods_search_page';
		$acount_all=array();
		$this->render('return_goods_search_second_list.html',array(
				'pa'=>Util::page($pageData),
				'page_list'=>$data,
				'acount_all'=>$acount_all,
		));
	}
	public function get_all_onoffline_channels(){
		//渠道
		$order_type=_Request::get('order_type');
		$model = new UserChannelModel(59);
		$data = $model->getAllChannels_onoffline($order_type);
		echo json_encode($data);
	}
	/**
	 * 导出第一层报表
	 */
	public function export_cxv_index(){
		$page = _Request::getInt("page",1);
		$where = array(
			'start_time' => _Request::get('start_time'),
			'end_time' => _Request::get('end_time'),
			'channel_class' => _Request::get('order_type'),
			'department_id' => _Request::get('department_id'),	
		);
		$model = new WarehouseBillModel(55);
		$data = $model->pageReturnGoodslist($where,$page,999999999,false);
		if($data['data']){
			$util=new Util();
			$title=array('日期','单据类型','总订单量','总线上订单量','总线下订单量','总退货量','总线上退货量','总线下退货量','总退货占比','总线上退货占比','总线下退货占比');
			$csv_data=array();
			if(isset($data['data']) && $data['data']){
				foreach($data['data'] as &$val){
					$temp=array();
					$temp['acount_date']=$val['acount_date'];
					if($val['bill_type']==0)
						$temp['bill_type']='维修退货单';
					else $temp['bill_type']='销售退货单';
					$temp['COUNT']=$val['COUNT'];
					$temp['online_count']=$val['online_count'];
					$temp['offline_count']=$val['offline_count'];
					$temp['return_count']=$val['return_count'];
					$temp['online_return_count']=$val['online_return_count'];
					$temp['offline_return_count']=$val['offline_return_count'];
					$temp['return_goods_percent']=$val['return_goods_percent'].'%';
					$temp['online_return_goods_percent']=$val['online_return_goods_percent'].'%';
					$temp['offline_return_goods_percent']=$val['offline_return_goods_percent'].'%';
					
					$csv_data[]=$temp;
				}
			}
			$util->downloadCsv(date('Y-m-d').'退货率总报表',$title,$csv_data);
		}
	}
}
