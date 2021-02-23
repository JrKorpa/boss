<?php
/**
 *  -------------------------------------------------
 *  工厂平均生产时长报表
 *   @file		: FactoryAvgProductTimeReportController.php
 *   @link		:  www.kela.cn
 *   @update	:
 *  -------------------------------------------------
 */
class FactoryAvgProductTimeReportController extends Controller
{
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        //获取工厂
		$gen_list=array();
		$facmodel = new AppProcessorInfoModel(53);
		$process = $facmodel->getAllProList();
		$dd = new DictView(new DictModel(1));
		
		//获取跟单人
		$gendanModel = new ProductFactoryOprauserModel(53);
		//获取系列
		$xilieArr=$gendanModel->getXilie();
		$gen_list = $gendanModel->select2($fields = ' distinct(`opra_uname`)' , $where = ' 1 ' , $type = 'all');
		$this->render('factory_avgproduct_time_search_form.html',array(
            'bar'=>Auth::getBar(),
			'process' => $process,
			'user_list'=>$gen_list,
			'xilieArr'=>$xilieArr,	
			'dd'=>$dd,
				
            )
        );
	}
	/**
	 *	search，列表
	 */
	public function search ($params)
	{
        $from_type=_Request::get('from_type');
        $style_sn=_Request::get('style_sn');
        $prc_name=_Request::get('prc_name');
        $start_time=_Request::get('start_time');
        $end_time=_Request::get('end_time');
 	 	$where=array(
 	 		'start_time'=>$start_time,
 	 		'end_time'=>$end_time,
 	 		'from_type'=>$from_type,
 	 		'style_sn'=>$style_sn,
 	 		'opra_unames'=>_Request::getList("opra_unames"),
 	 		'prc_ids'=>_Request::getList("prc_ids"),
 	 		'diamond_type'=>_Request::get("diamond_type"),
 	 		'qiban_type'=>_Request::get("qiban_type"),
 	 		'xilie_ids'=>_Request::getList("xilie_ids"),
 	 		'style_type'=>_Request::get("style_type"),
 	 	);
 	 	//print_r($where);exit;
		$page = _Request::getInt("page",1);
        $page_num = (_Post::getInt('page_num'))?_Post::getInt('page_num'):40;
		$model = new FactoryAvgProductTimeReportModel(53);
		$data = $model->pageAvgproducttimelList($where,$page,$page_num,false); 
		if($data['data']){
			foreach ($data['data'] as & $val){
                $val['avg_product_time'] = $val['cnt']>0?round($val['day_sum']/$val['cnt'],4):0;
			}
		}
		$pageData = $data;
        $where['prc_ids[]'] = $where['prc_ids'];
        $where['xilie_ids[]'] = $where['xilie_ids'];
        $where['opra_unames[]'] = $where['opra_unames'];

        $pageData['filter'] = $where;
		$pageData['jsFuncs'] = 'factory_avgproduct_time_search_page'; 
		//汇总
		$data2 = $model->pageAvgproducttimelList_tj($where);
		$tongji = array();
        $tongji = $data2;
        $tongji['avg_product_time'] = $tongji['cnt']>0?round($tongji['day_sum']/$tongji['cnt'],4):0;
		$this->render('factory_avgproduct_time_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'args'=>$where,
			'tongji'=>$tongji
		));
   }
   /**
    * 详情列表
    */
   	public function detail_list(){
   		//获取跟单人
   		$gen_list=array();
   		$facmodel = new AppProcessorInfoModel(53);
   		$process = $facmodel->getProList();
   		//获取跟单人
   		$gendanModel = new ProductFactoryOprauserModel(53);
   		//获取系列
   		$xilieArr=$gendanModel->getXilie();
   		$dd = new DictView(new DictModel(1));
   		$gen_list = $gendanModel->select2($fields = ' distinct(`opra_uname`)' , $where = ' 1 ' , $type = 'all');
	   	$this->render('avgproduct_time_detail_list_search_form.html',array(
	   			'bar'=>Auth::getBar('detail_list'),
	   			'process' => $process,
	   			'user_list'=>$gen_list,
	   			'xilieArr'=>$xilieArr,
	   			'dd'=>$dd,
	   	));
   }
   
   public function detail_list_ajax(){
        $from_type=_Request::get('from_type');
        $style_sn=_Request::get('style_sn');
        $prc_name=_Request::get('prc_name');
        $start_time=_Request::get('start_time');
        $end_time=_Request::get('end_time');
 	 	$where=array(
 	 		'start_time'=>$start_time,
 	 		'end_time'=>$end_time,
 	 		'opra_unames'=>_Request::getList("opra_unames"),
 	 		'prc_ids'=>_Request::getList("prc_ids"),
 	 			'diamond_type'=>_Request::get("diamond_type"),
 	 			'qiban_type'=>_Request::get("qiban_type"),
 	 			'xilie_ids'=>_Request::getList("xilie_ids"),
 	 			'style_type'=>_Request::get("style_type"),
 	 	);
        if($from_type){
            $where['from_type'] = $from_type;
        }
        if($style_sn){
            $where['style_sn'] = $style_sn;
        }
		$page = _Request::getInt("page",1);
        $page_num = (_Post::getInt('page_num'))?_Post::getInt('page_num'):40;
		$model = new FactoryAvgProductTimeReportModel(53);
		$data = $model->pageAvgproducttimelList_2($where,$page,$page_num,false); 
		if($data['data']){
			foreach ($data['data'] as & $val){
                $val['avg_product_time'] = $val['cnt']>0?round($val['day_sum']/$val['cnt'],4):0;
			}
		}
		$pageData = $data;
        $where['opra_unames[]'] = $where['opra_unames'];
        $where['xilie_ids[]'] = $where['xilie_ids'];
        $where['prc_ids[]'] = $where['prc_ids'];
		$pageData['filter'] = $where;
		$pageData['jsFuncs'] = 'factory_avg_detail_search_page'; 
	   	$this->render('avgproduct_time_detail_search_list.html',array(
	   			'pa'=>Util::page($pageData),
	   			'page_list'=>$data,
	   			'args'=>$where
	   	));
   }
   public function last_detail_list(){
   		//获取跟单人
   		$gen_list=array();
   		$facmodel = new AppProcessorInfoModel(53);
   		$process = $facmodel->getProList();
   		//获取跟单人
   		$gendanModel = new ProductFactoryOprauserModel(53);
   		$xilieArr=$gendanModel->getXilie();
   		$dd = new DictView(new DictModel(1));
   		$gen_list = $gendanModel->select2($fields = ' distinct(`opra_uname`)' , $where = ' 1 ' , $type = 'all');
	   	$this->render('factory_last_detail_list_search_form.html',array(
	   			'bar'=>Auth::getBar('last_detail_list'),
	   			'process' => $process,
	   			'user_list'=>$gen_list,
	   			'xilieArr'=>$xilieArr,
	   			'dd'=>$dd,
	   	));
   }
   
   public function last_detail_list_ajax(){
        $from_type=_Request::get('from_type');
        $style_sn=_Request::get('style_sn');
        $prc_name=_Request::get('prc_name');
        $start_time=_Request::get('start_time');
        $end_time=_Request::get('end_time');
 	 	$where=array(
 	 		'start_time'=>$start_time,
 	 		'end_time'=>$end_time,
 	 		'opra_unames'=>_Request::getList("opra_unames"),
 	 		'prc_ids'=>_Request::getList("prc_ids"),
 	 			'diamond_type'=>_Request::get("diamond_type"),
 	 			'qiban_type'=>_Request::get("qiban_type"),
 	 			'xilie_ids'=>_Request::getList("xilie_ids"),
 	 			'style_type'=>_Request::get("style_type"),
 	 	);
        if($from_type){
            $where['from_type'] = $from_type;
        }
        if($style_sn){
            $where['style_sn'] = $style_sn;
        }
		$page = _Request::getInt("page",1);
        $page_num = (_Post::getInt('page_num'))?_Post::getInt('page_num'):10;
		$model = new FactoryAvgProductTimeReportModel(53);
		$data = $model->pageAvgproducttimelList_3($where,$page,$page_num,false); 
		$dd = new DictView(new DictModel(1));
		$qiban_type_arr=$dd->getEnumArray('qiban_type');
		$qibanArr=array();
		foreach ($qiban_type_arr as $v){
			$qibanArr[$v['name']]=$v['label'];
		}
	   	foreach ($data['data'] as $k=>$val){
	   		$data['data'][$k]['qiban_type']=$qibanArr[$val['qiban_type']];
	   		if($val['diamond_type']==2){
	   			$diamond_type="期货钻";
	   		}elseif($val['diamond_type']==1){
	   			$diamond_type="现货钻";
	   		}else{
	   			$diamond_type='默认';
	   		}
	   		if($val['kuan_type']==1){
	   			$kuan_type="简单款";
	   		}elseif($val['kuan_type']==2){
	   			$kuan_type="豪华款";
	   		}else{
	   			$kuan_type='';
	   		}
	   		$data['data'][$k]['diamond_type']=$diamond_type;
	   		$data['data'][$k]['kuan_type']=$kuan_type;
	   	}
	   	//print_r($data);exit;
	   	$pageData = $data;
        $where['opra_unames[]'] = $where['opra_unames'];
        $where['xilie_ids[]'] = $where['xilie_ids'];
        $where['prc_ids[]'] = $where['prc_ids'];
	   	$pageData['filter'] = $where;
	   	$pageData['jsFuncs'] = 'factory_last_detail_search_page';
	   	$this->render('factory_last_detail_search_list.html',array(
	   			'pa'=>Util::page($pageData),
	   			'page_list'=>$data,
	   			'args'=>$where,
	   	));
   }
   /**
    * 导出第一层报表
    */
   public function export_cxv_index(){
        $from_type=_Request::get('from_type');
        $style_sn=_Request::get('style_sn');
        $prc_name=_Request::get('prc_name');
        $start_time=_Request::get('start_time');
        $end_time=_Request::get('end_time');
 	 	$where=array(
 	 		'start_time'=>$start_time,
 	 		'end_time'=>$end_time,
 	 		'from_type'=>$from_type,
 	 		'style_sn'=>$style_sn,
 	 		'opra_unames'=>_Request::getList("opra_unames"),
 	 		'prc_ids'=>_Request::getList("prc_ids"),
 	 			'diamond_type'=>_Request::get("diamond_type"),
 	 			'qiban_type'=>_Request::get("qiban_type"),
 	 			'xilie_ids'=>_Request::getList("xilie_ids"),
 	 			'style_type'=>_Request::get("style_type"),
 	 	);
		$page = _Request::getInt("page",1);
        $page_num = (_Post::getInt('page_num'))?_Post::getInt('page_num'):10000;
		$model = new FactoryAvgProductTimeReportModel(53);
		$data = $model->pageAvgproducttimelList($where,$page,$page_num,false); 
		if($data['data']){
			foreach ($data['data'] as & $val){
                $val['avg_product_time'] = $val['cnt']>0?round($val['day_sum']/$val['cnt'],4):0;
			}
		}
		$pageData = $data;
        $where['prc_ids[]'] = $where['prc_ids'];
        $where['opra_unames[]'] = $where['opra_unames'];
        $where['xilie_ids[]'] = $where['xilie_ids'];
        $pageData['filter'] = $where;
		$pageData['jsFuncs'] = 'factory_avgproduct_time_search_page'; 
		//汇总
		$data2 = $model->pageAvgproducttimelList_tj($where);
		$tongji = array();
        $tongji = $data2;
        $tongji['dotime'] = "总计";
        $tongji['avg_product_time'] = $tongji['cnt']>0?round($tongji['day_sum']/$tongji['cnt'],4):0;
        array_push($data['data'],$tongji);
        $util=new Util();
        $title=array('日期','出货数量','平均生产时长（天）');
        $csv_data=array();

        if($data['data']){
            foreach ($data['data'] as & $val){
                $temp=array();
                $temp[]=$val['dotime'];
                $temp[]=$val['cnt'];
                $temp[]=$val['avg_product_time'];
                $csv_data[] = $temp;    
            }
        }
        $util->downloadCsv('平均生产时长总报表'.date('Y-m-d'),$title,$csv_data);
   }
   /**
    * 导出明细报表
    */
   public function export_cxv_detail(){
        $from_type=_Request::get('from_type');
        $style_sn=_Request::get('style_sn');
        $prc_name=_Request::get('prc_name');
        $start_time=_Request::get('start_time');
        $end_time=_Request::get('end_time');
 	 	$where=array(
 	 		'start_time'=>$start_time,
 	 		'end_time'=>$end_time,
 	 		'from_type'=>$from_type,
 	 		'style_sn'=>$style_sn,
 	 		'opra_unames'=>_Request::getList("opra_unames"),
 	 		'prc_ids'=>_Request::getList("prc_ids"),
 	 			'diamond_type'=>_Request::get("diamond_type"),
 	 			'qiban_type'=>_Request::get("qiban_type"),
 	 			'xilie_ids'=>_Request::getList("xilie_ids"),
 	 			'style_type'=>_Request::get("style_type"),
 	 	);
 	 	
        $args = $where;
        $page = _Request::getInt("page",1);
        $model = new FactoryAvgProductTimeReportModel(53);
        $args['status']=9;
        $data = $model->pageAvgproducttimelList_3($args,$page,99999999999,false);
        $util=new Util();//'销售渠道','客户来源'
        $title=array('布产单号','布产来源','款号','数量','客户姓名','跟单人','工厂名称','分配工厂时间','工厂接单时间','标准出厂时间','质检通过时间','工厂交货时间','布产状态','生产状态','布产类型','镶嵌要求','采购备注','制单人','生产时长','起版类型','钻石类型','款式类型');
        $csv_data=array();
        if($data['data']){
            if(isset($data['data']) && $data['data']){
                foreach($data['data'] as &$val){
                    $temp=array();
                    $temp[]=$val['bc_sn'];
                    $temp[]='订单'.$val['p_sn'];
                    $temp[]=$val['style_sn'];
                    $temp[]=$val['num'];
                    $temp[]=$val['consignee'];
                    $temp[]=$val['opra_uname'];
                    $temp[]=$val['prc_name'];
                    $temp[]=$val['to_factory_time'];
                    $temp[]=$val['order_time'];
                    $temp[]=$val['esmt_time'];
                    $temp[]=$val['oqc_pass_time'];
                    $temp[]=$val['rece_time'];
                    
                    $temp[]='已出厂';//$val['status']
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
                    //$temp[]=$dd->getEnum('buchan_fac_opra',$val['buchan_fac_opra']);
                    $temp[]=$buchan_fac_opra;
                    if($val['from_type']==1)
                        $from_type='采购单';
                    else $from_type='订单';
                    $temp[]=$from_type;
                    $temp[]=$val['xiangqian'];
                    $temp[]=$val['caigou_info'];
                    /* $temp[]=$model->get_channel_name($val['channel_id']);
                    $temp[]=$model->get_source_name($val['customer_source_id']); */
                    $temp[]=$val['create_user'];
                    $temp[]=$val['cc_day'];
                    $dd = new DictView(new DictModel(1));
                    $qiban_type_arr=$dd->getEnumArray('qiban_type');
                    $qibanArr=array();
                    foreach ($qiban_type_arr as $v){
                    	$qibanArr[$v['name']]=$v['label'];
                    }
                    $temp[]=$qibanArr[$val['qiban_type']];
                    if($val['diamond_type']==2){
                    	$diamond_type="期货钻";
                    }elseif($val['diamond_type']==1){
                    	$diamond_type="现货钻";
                    }else{
                    	$diamond_type='默认';
                    }
                    if($val['kuan_type']==1){
                    	$kuan_type="简单款";
                    }elseif($val['kuan_type']==2){
                    	$kuan_type="豪华款";
                    }else{
                    	$kuan_type='';
                    }
                    $temp[]=$diamond_type;
                    $temp[]=$kuan_type;
                    $csv_data[]=$temp;
                }
            }
        }
        $util->downloadCsv('平均生产时长详细报表'.date('Y-m-d'),$title,$csv_data);
   }
}
