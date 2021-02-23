<?php
/**
 * 工厂良品率报表
 *
 * 
 */
class FactoryPassedReportController extends Controller
{
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
		$this->render('factory_passed_search_form.html',array(
				'bar'=>Auth::getBar('index'),
				'Processor_list'=>$Processor_list,
				'user_list'=>$gen_list,
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
			$data_arr[$start_time_str[0]."-".$start_time_str[1]."-".$start_time_str[2]]=$start_time_str[0]."-".$start_time_str[1]."-".$start_time_str[2];
			$start_time_str[2]++;
			$start_time_str=explode("-",date("Y-m-d",mktime(0,0,0,$start_time_str[1],$start_time_str[2],$start_time_str[0])));
		}
        krsort($data_arr);
		return $data_arr;
	}
    
    /**
	 *	search，列表
	 */
 	 public function search($params) {
 	 	$page = _Request::getInt("page",1);
        $page_num = (_Post::getInt('page_num'))?_Post::getInt('page_num'):10;
 	 	$qc_type=_Request::get("qc_type")?_Request::get("qc_type"):0;
 	 	$start_time =_Request::get("start_time");
 	 	$end_time =_Request::get("end_time");
 	 	$style_sn =_Request::get("style_sn");
 	 	$result =_Request::get("result");

 	 	$where=array(
 	 		'start_time'=>$start_time,
 	 		'end_time'=>$end_time,
 	 		'qc_type'=>$qc_type,
 	 		'style_sn'=>$style_sn,
 	 		'result'=>$result,
 	 		'opra_uname'=>_Request::getList("opra_uname"),
 	 		'prc_ids'=>_Request::getList("prc_ids"),
 	 	);
 	 	$start_time =_Request::get("start_time");
 	 	$end_time =_Request::get("end_time");
        if(empty($start_time) || empty($start_time)){
            die('统计日期不能为空');
        }
        $dateList = $this->get_data_arr($start_time,$end_time);
        if(count($dateList) > 140){
            die('统计日期不能超过140天!');
        }

        $iqcmodel =  new PurchaseIqcOpraModel(54);
 	 	$data=$iqcmodel->get_qc_list($where, $page, $page_num);
 	 	if($data['data']){
 	 		foreach ($data['data'] as & $val){
 	 			$val['fail_num']=$val['count']-$val['pass_num'];
 	 			$val['pass_percent']=number_format($val['pass_num']*100/$val['count'],2);
 	 			$val['fail_percent']=number_format($val['fail_num']*100/$val['count'],2);
 	 		}
 	 	}
        $pageData =$data;
        $where['opra_uname[]'] = $where['opra_uname'];
        $where['prc_ids[]'] = $where['prc_ids'];
        $pageData['filter'] = $where;
        $pageData['jsFuncs'] = 'factory_passed_search_page';
        //汇总
        $alldata= $iqcmodel->get_qc_list($where,$page,999999999,false);
        $datacount=array(
        		'count'=>0,
        		'fail_num'=>0,
        		'pass_num'  =>0,
        		'pass_percent'	=>0,
        		'fail_percent'	=>0,
        );
        if($alldata['data']){
        	foreach ($alldata['data'] as $value){
        		$datacount['count']+=$value['count'];
        		$datacount['pass_num']+=$value['pass_num'];
        	}
        	if($datacount['count']){
        		$datacount['fail_num']=$datacount['count']-$datacount['pass_num'];
        		$datacount['pass_percent']=number_format($datacount['pass_num']*100/$datacount['count'],2);
        		$datacount['fail_percent']=number_format($datacount['fail_num']*100/$datacount['count'],2);
        	}
        }
        $this->render('factory_passed_search_list.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $pageData,
        	'datacount'=>$datacount,
        	'args'=>$where,
        ));
    }
    /**
     * 详情列表
     */
    public function index_second(){
		$model = new ProductInfoModel(53);
		$Processor_list   = $model->GetSupplierList();//工厂列表
		//获取跟单人
		$gendanModel = new ProductFactoryOprauserModel(53);
		$gen_list = $gendanModel->select2($fields = ' distinct(`opra_uname`)' , $where = ' 1 ' , $type = 'all');
		$this->render('factory_passed_search_form_second.html',array(
				'bar'=>Auth::getBar('index'),
				'Processor_list'=>$Processor_list,
				'user_list'=>$gen_list,
		));
    }
    public function search_second($params){
 	 	$page = _Request::getInt("page",1);
        $page_num = (_Post::getInt('page_num'))?_Post::getInt('page_num'):10;
 	 	$qc_type=_Request::get("qc_type")?_Request::get("qc_type"):0;
 	 	$start_time =_Request::get("start_time");
 	 	$end_time =_Request::get("end_time");
 	 	$style_sn =_Request::get("style_sn");
 	 	$result =_Request::get("result");

 	 	$where=array(
 	 		'start_time'=>$start_time,
 	 		'end_time'=>$end_time,
 	 		'qc_type'=>$qc_type,
 	 		'style_sn'=>$style_sn,
 	 		'result'=>$result,
 	 		'opra_uname'=>_Request::getList("opra_uname"),
 	 		'prc_ids'=>_Request::getList("prc_ids"),
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
        
        $iqcmodel =  new PurchaseIqcOpraModel(54);
 	 	$data=$iqcmodel->get_qc_list_second($where, $page, $page_num);
 	 	if($data['data']){
 	 		foreach ($data['data'] as & $val){
                $val['result'] = $val['result']==1?'通过':'未通过';
                $bc_sn = $val['bc_sn'];
                if(strlen($bc_sn)>2){
                    $val['bc_id'] = substr($bc_sn,2);
                }else{
                    $val['bc_id'] = 0;
                }
                /*
                if($val['qc_type'] == 'OQC'){
                    switch($val['reason']){
                        case 1:
                            $val['reason'] = '指圈不对';
                            break;
                        case 2:
                            $val['reason'] = '镶钻不稳';
                            break;
                        default:
                            $val['reason'] = '';
                            break;
                    }
                }else{
                    $val['reason'] = '';
                }*/
 	 		}
 	 	}

        $pageData =$data;
        $where['opra_uname[]'] = $where['opra_uname'];
        $where['prc_ids[]'] = $where['prc_ids'];
        $pageData['filter'] = $where;
        $pageData['jsFuncs'] = 'factory_passed_search_page_second';
        $this->render('factory_passed_search_list_second.html', array(
            'pa' => Util::page($pageData),
            'page_list' => $pageData,
        	'args'=>$where,
        ));
    }

    public function download()
    {

 	 	$page = _Request::getInt("page",1);
        $page_num = (_Post::getInt('page_num'))?_Post::getInt('page_num'):10;
 	 	$qc_type=_Request::get("qc_type")?_Request::get("qc_type"):0;
 	 	$start_time =_Request::get("start_time");
 	 	$end_time =_Request::get("end_time");
 	 	$style_sn =_Request::get("style_sn");
 	 	$result =_Request::get("result");

 	 	$where=array(
 	 		'start_time'=>$start_time,
 	 		'end_time'=>$end_time,
 	 		'qc_type'=>$qc_type,
 	 		'style_sn'=>$style_sn,
 	 		'result'=>$result,
 	 		'opra_uname'=>_Request::getList("opra_uname"),
 	 		'prc_ids'=>_Request::getList("prc_ids"),
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
        
        $iqcmodel =  new PurchaseIqcOpraModel(54);
 	 	$data=$iqcmodel->get_qc_list_second_details($where);
 	 	if($data){
 	 		foreach ($data as $key => $val){
 	 			/*
                if($val['qc_type'] == 'OQC'){
                    switch($val['reason']){
                        case 1:
                            $data[$key]['reason'] = '指圈不对';
                            break;
                        case 2:
                            $data[$key]['reason'] = '镶钻不稳';
                            break;
                        default:
                            $data[$key]['reason'] = '';
                            break;
                    }
                }else{
                    $data[$key]['reason'] = '';
                }
                */
                $data[$key]['result'] = $val['result']==1?'通过':'未通过';
                unset($data[$key]['opra_date']);
 	 		}
 	 	}
        $header="操作时间,布产号,类型,款号,公司ID,公司名称,跟单人,质检结果,原因,备注";
        $name = "质检明细";
        $this->excel($data, $header, $name);
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
                    $str .=  "<td align=right>" . iconv('utf-8','gbk',$vv) . "</td>";
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
