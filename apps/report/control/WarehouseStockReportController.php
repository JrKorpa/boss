<?php

class WarehouseStockReportController extends Controller
{
	//获取传过来的变量
	public function getData()
	{
		if(_Request::get("is_post")){
			if(isset($_POST['warehouse_id'])) $warehouse_id=$_POST['warehouse_id'];
			else $warehouse_id='';
			if(isset($_POST['company_id'])) $company_id=$_POST['company_id'];
			else $company_id='';
			if(isset($_POST['type'])) $type=$_POST['type'];
			else $type='';
		}
		else{
			$warehouse_id=_Request::getList('warehouse_id');
			$company_id=_Request::getList('company_id');
			$type=_Request::getList('type');
		}
		$args = array(
				'mod'		=> _Request::get("mod"),
				'con'		=> substr(__CLASS__, 0, -10),
				'act'		=> __FUNCTION__,
				'code'      => _Request::get('code'),
				'warehouse_id'      =>$warehouse_id,
				'warehouse_ids_string'=>_Request::get("is_post")?'':trim(_Request::get("warehouse_ids_string")),
				'company_id'=>$company_id,
				'company_ids_string'=>_Request::get("is_post")?'':trim(_Request::get("company_ids_string")),
				'is_delete' => _Request::getString('is_delete'),
				'type' =>$type,
				'types_string'=>_Request::get("is_post")?'':trim(_Request::get("types_string")),
				'start_time'=>_Request::get("is_post")?'':trim(_Request::get("start_time",date("Y-m-d",time()-86400*15))),
				'end_time'=>_Request::get("is_post")?'':trim(_Request::get("end_time",date("Y-m-d"))),
				'dotime'=>_Request::get("is_post")?'':trim(_Request::get("dotime")),
				'dotime_string'=>_Request::get("is_post")?'':trim(_Request::get("dotime_string")),
		);
		$url='/index.php?';
		parse_str($_SERVER["REQUEST_URI"]);
		$args['act']=$act;
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
		if(_Request::get("is_post")){//当是post表单提交数据的时候，以下参数的get数据失效
			$args['warehouse_ids_string']=$args['company_ids_string']=$args['types_string']="";
		}
		if($args['warehouse_id']){
			foreach ($args['warehouse_id'] as $val){
				$args['warehouse_ids_string'].="{$val},";
			}
			$args['warehouse_ids_string']=trim($args['warehouse_ids_string'],',');
		}
		if($args['company_id']){
			foreach ($args['company_id'] as $val){
				$args['company_ids_string'].="{$val},";
			}
			$args['company_ids_string']=trim($args['company_ids_string'],',');
		}
		if($args['type']){
			foreach ($args['type'] as $val){
				$args['types_string'].="{$val},";
			}
			$args['types_string']=trim($args['types_string'],',');
		}
		if($args['dotime_string']){
			$args['dotime_string']=$args['dotime_string'];
			$args['dotime']=$args['dotime_string'];
		}
		return $args;
	
	}
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$company_model = new CompanyModel(59);
		$company_info = $company_model -> getCompanyTree();
		$this->assign('company_info',$company_info);//公司
		$this->dd = new DictView(new DictModel(59));
		$type=$this->dd->getEnumArray('warehouse.type');
		$this->assign('type', $type);
		$model = new WarehouseModel(55);
		$Allhouse=$model->getAllhouse();
		$this->render('warehouse_stock_search_form.html',array('bar'=>Auth::getBar(),'allhouse'=>$Allhouse));
	}

    public function download($data){
        ini_set('memory_limit', '6400M');
        $args = array();
        $args['type'] = array_filter(array_unique(explode(',',$_REQUEST['types_string'])));
        $args['company_ids'] = array_filter(array_unique(explode(',',$_REQUEST['company_ids_string'])));
        $args['warehouse_ids'] = array_filter(array_unique(explode(',',$_REQUEST['warehouse_ids_string'])));
        $args['dotime_string'] = $_REQUEST['dotime_string'];

        $args['types_string'] = $_REQUEST['types_string'];
        $args['company_ids_string'] = $_REQUEST['company_ids_string'];
        $args['warehouse_ids_string'] = $_REQUEST['warehouse_ids_string'];
        $args['dotime_string'] = $_REQUEST['dotime_string'];
        
        if(isset($args['dotime_string']) && empty($args['dotime_string'])){
            exit("时间不能为空!");
        }
		$model = new WarehouseKucunModel(47);
        $statistic_data = $model->get_statistic_data($args);
        $header="产品线,新产品线,款式分类,新款式分类,货号,供应商,入库方式,状态,所在仓库,款号,模号,名称,名义价,原始采购价,最新采购价,材质,金重,手寸,金托类型,主石,主石粒数,主石形状,主石大小,主石颜色,主石净度,主石切工,抛光,对称,荧光,主石规格,主石买入单价,主石买入成本,主石计价单价,副石1,副石1粒数,副石1重,副石1买入单价,副石1买入成本,副石1计价单价,副石2,副石2粒数,副石2重,副石2买入单价,副石2买入成本,副石2计价单价,证书号,证书类型,金饰类型,数量,是否结价,是否绑定,所在公司,戒托实际镶口,维修状态,维修公司,维修仓库,金耗,最后销售时间,本库库龄,库龄,国际报价,折扣,品牌,裸钻证书类型,供应商货品条码,系列及款式归属,柜位,入库时间";

        $this->excel($statistic_data,$header,__FUNCTION__);
    }

    public function downloadReport($data){
        $args = array();
        $model = new WarehouseKucunModel(47);
        
        if(isset($_REQUEST['start_time'])){
            $start_time = $_REQUEST['start_time'];
            $end_time = $_REQUEST['end_time'];
            $time_part = $this->getTimePart($start_time,$end_time);
            $time_part = array_keys($time_part);
            $statistic_info = array();
            if(isset($_REQUEST['company_id'])){
                $company_id = _Request::getList('company_id');
                $args['company_ids_string'] = join(',',$company_id);
            }
            if(isset($_REQUEST['warehouse_id'])){
                $warehouse_id = _Request::getList('warehouse_id');
                $args['warehouse_ids_string'] = join(',',$warehouse_id);
            }
            if(isset($_REQUEST['type'])){
                $type = _Request::getList('type');
                $args['types_string'] = join(',',$type);
            }


            foreach($time_part as $key => $v){
                $args['dotime_string'] = $v;
                $ret = $model->get_statistic_info($args);
                if($ret){
                    unset($ret['warehouse_ids_string']);
                    unset($ret['company_ids_string']);
                    unset($ret['types_string']);
                    $statistic_info[] = $ret;
                }
            }
            $header = "统计时间,库存数量,库存金额";
            $this->excel($statistic_info,$header,__FUNCTION__);
            exit();
        }
        if(isset($_REQUEST['doType']) && $_REQUEST['doType'] == 2){
            $dotime_string = $_REQUEST['dotime_string'];
            if(isset($_REQUEST['company_id'])){
                $company_id = _Request::getList('company_id');
                $args['company_ids_string'] = join(',',$company_id);
            }
            if(isset($_REQUEST['warehouse_id'])){
                $warehouse_id = _Request::getList('warehouse_id');
                $args['warehouse_ids_string'] = join(',',$warehouse_id);
            }
            if(isset($_REQUEST['type'])){
                $type = _Request::getList('type');
                $args['types_string'] = join(',',$type);
            }        
            $args['dotime_string'] = $dotime_string;
            $statistic_data = $model->get_statistic_report($args);
            $header = "统计时间,公司,库存数量,库存金额";
            $this->excel($statistic_data,$header,__FUNCTION__);
            exit();
        }
            


        if(isset($_REQUEST['doType']) && $_REQUEST['doType'] == 3){
            $dotime_string = $_REQUEST['dotime_string'];
            if(isset($_REQUEST['company_id'])){
                $company_id = _Request::getList('company_id');
                $args['company_ids_string'] = join(',',$company_id);
            }
            if(isset($_REQUEST['warehouse_id'])){
                $warehouse_id = _Request::getList('warehouse_id');
                $args['warehouse_ids_string'] = join(',',$warehouse_id);
            }
            if(isset($_REQUEST['type'])){
                $type = _Request::getList('type');
                $args['types_string'] = join(',',$type);
            }        
            $args['dotime_string'] = $dotime_string;
            $statistic_data = $model->get_statistic_report_third($args);
            $header = "统计时间,公司,仓库,库存数量,库存金额";
            $this->excel($statistic_data,$header,__FUNCTION__);
            exit();
        }
    }

    public function getTimePart($start_time,$end_time){
        $start_time_str = explode("-", $start_time);
        $end_time_str = explode("-", $end_time);
        $data_arr = array();
        while(true){                                                                       
            if($start_time_str[0].$start_time_str[1].$start_time_str[2] > $end_time_str[0].$end_time_str[1].$end_time_str[2]) 				break;
            $data_arr[$start_time_str[0]."-".$start_time_str[1]."-".$start_time_str[2]]=$start_time_str[1]."-".$start_time_str[2];
            $start_time_str[2]++;
            $start_time_str = explode("-", date("Y-m-d", mktime(0,0,0,$start_time_str[1],$start_time_str[2],$start_time_str[0])));
        }
        return $data_arr;
    }

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args=$this->getData();
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = $args;
		$model = new WarehouseKucunModel(47);
        $start_time = $args['start_time'];
        $end_time = $args['end_time'];
        
        $time_part = $this->getTimePart($start_time,$end_time);
        $time_part = array_keys($time_part);
        krsort($time_part);
        $statistic_info = array();
        foreach($time_part as $key => $v){
            $args['dotime_string'] = $v;
            $ret = $model->get_statistic_info($args);
            if($ret){
                $statistic_info[] = $ret;
            }
        }
		$model2 = new WarehouseModel(55);
		$Allhouse=$model2->getAllhouse();
		$this->render('warehouse_stock_search_list.html',array(
				'statistic_info'=>$statistic_info,
				'args'=>$args,
				
		));
	}
	/**
	 * 第二层列表
	 */
	public function index_second(){
		$company_model = new CompanyModel(59);
		$company_info = $company_model -> getCompanyTree();
		$this->assign('company_info',$company_info);//公司
		$this->dd = new DictView(new DictModel(59));
		$type=$this->dd->getEnumArray('warehouse.type');
		$this->assign('type', $type);
		$model = new WarehouseModel(55);
		$Allhouse=$model->getAllhouse();
		$args=$this->getData();
		$this->render('warehouse_stock_search_form_second.html',array('bar'=>Auth::getBar(),'allhouse'=>$Allhouse,'args'=>$args,));
	}
	/**
	 * 第二层列表 搜索
	 */
	public function search_second ($params)
	{
        $args = array();

        $args['dotime_string'] = $params['dotime_string'];
        if(isset($params['warehouse_id']) && is_array($params['warehouse_id']) && !empty($params['warehouse_id'])){
            $args['warehouse_ids_string'] = join(',',$params['warehouse_id']);
        }
        if(isset($params['company_id']) && is_array($params['company_id']) && !empty($params['company_id'])){
            $args['company_ids_string'] = join(',',$params['company_id']);
        }
        if(isset($params['type']) && is_array($params['type']) && !empty($params['type'])){
            $args['types_string'] = join(',',$params['type']);
        }
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$model = new WarehouseKucunModel(47);
		$data = $model->pageList($args,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_stock_second_search_page';
		$this->render('warehouse_stock_search_list_second.html',array(
				'pa'=>Util::page($pageData),
				'page_list'=>$data,
				'args'=>$args
		));
	}
	/**
	 * 第三层列表
	 */
	public function index_third(){
		$company_model = new CompanyModel(59);
		$company_info = $company_model -> getCompanyTree();
		$this->assign('company_info',$company_info);//公司
		$this->dd = new DictView(new DictModel(59));
		$type=$this->dd->getEnumArray('warehouse.type');
		$this->assign('type', $type);
		$model = new WarehouseModel(55);
		$Allhouse=$model->getAllhouse();
		$args=$this->getData();
		$this->render('warehouse_stock_search_form_third.html',array('bar'=>Auth::getBar(),'allhouse'=>$Allhouse,'args'=>$args,));
	}
	/**
	 * 第三层列表 搜索
	 */
	public function search_third ($params)
	{
        $args = array();
        $args['dotime_string'] = $params['dotime_string'];
        if(isset($params['warehouse_id']) && is_array($params['warehouse_id']) && !empty($params['warehouse_id'])){
            $args['warehouse_ids_string'] = join(',',$params['warehouse_id']);
        }
        if(isset($params['company_id']) && is_array($params['company_id']) && !empty($params['company_id'])){
            $args['company_ids_string'] = join(',',$params['company_id']);
        }
        if(isset($params['type']) && is_array($params['type']) && !empty($params['type'])){
            $args['types_string'] = join(',',$params['type']);
        }
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$model = new WarehouseKucunModel(47);
		$data = $model->pageListThird($args,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_stock_third_search_page';
		$this->render('warehouse_stock_search_list_third.html',array(
				'pa'=>Util::page($pageData),
				'page_list'=>$data,
		));
	}
	/**
	 * 导出第二层报表
	 */
	public function export_cxv_second(){
		$args=$this->getData();
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$model = new WarehouseKucunModel(47);
		$data = $model->pageList($args,$page,10000000000000,false);
		if($data['data']){
			$util=new Util();
			$title=array('公司','库存数量','库存金额');
			$csv_data=array();
			if(isset($data['data']) && $data['data']){
				foreach($data['data'] as &$val){
					$temp=array();
					$temp['company_name']=$val['company_name'];
					$temp['count']=$val['count'];
					$temp['warehouse_price']=$val['warehouse_price'];
					$csv_data[]=$temp;
				}
			}
			$util->downloadCsv('仓库库存量报表',$title,$csv_data);
		}
	}
	/**
	 * 导出明细报表
	 */
	function export_cxv_detail(){
		$args=$this->getData();
		extract($args);
		if(!$warehouse_ids_string && !$company_ids_string ){
			Util::alert('公司和仓库不能都为空！');
			return;
		}
		$location="index.php?mod=report&con=WarehouseGoodsReport&act=search&down_info=down_info&warehouse_ids_string={$warehouse_ids_string}&company_ids_string={$company_ids_string}&types_string={$types_string}";
		//$this->redirect($action);
		header('location:' . $location);
	//echo $action;
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