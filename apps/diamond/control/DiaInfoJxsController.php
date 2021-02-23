<?php
class DiaInfoJxsController extends CommonController {
	
	protected $smartyDebugEnabled = true;
	protected $code = array();
	protected $warehouse_arrs = array();
    protected $whitelist = array('dow');	
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{

        $kefu_type=3;       
        if($_SESSION['userType']==1 || Auth::user_is_from_base_company())
            $kefu_type=1;
        else{
            $company_model = new CompanyModel(1);
            $companyInfo = $company_model->select2(" `id`,`company_name`,`is_shengdai`,`sd_company_id`, `company_type`" , " id ='".$_SESSION['companyId']."'" , $type = '1');
            if($companyInfo){
                if($companyInfo[0]['company_type']=="1" || $companyInfo[0]['company_type']=="2")
                    $kefu_type=2;
                else
                    $kefu_type=3; 
            }
        }		
		$args = array(
				'carat'=>  _Request::getString('carat'),
				'carat_min'=>_Request::getFloat('carat_min'),
				'carat_max'=>_Request::getFloat('carat_max'),
				'polish'  =>_Request::getList('polish'),
				'symmetry'=>_Request::getList('symmetry'),
				'clarity'=> _Request::getList('clarity'),
				'color'=> _Request::getList('color'),
				'shape'=> _Request::getList('shape'),
				'cut'=> _Request::getList('cut'),
				'cert'=> _Request::getList('cert')
		);
		
		if(is_array($args['cut']) && in_array("3EX",$args['cut'])){
			$args['cut'][]='EX';
			$args['polish'][]='EX';
			$args['symmetry'][]='EX';
		}
		$this->assign('args',$args);
		
		
		$is_logo = 0;
		$param = array('s_start1'=>'','s_end1'=>'','e_start2'=>'','e_end2'=>'');
		if(isset($_REQUEST['start']) && isset($_REQUEST['end']) && _Request::getString('start') && _Request::getString('end')){
			$is_logo = 1;
			$arr_start = explode('-', _Request::getString('start'));
			$arr_end = explode('-', _Request::getString('end'));
			$param = array('s_start1'=>  $arr_start[0],'s_end1'=>  $arr_start[1],'e_start2'=>$arr_end[0],'e_end2'=>$arr_end[1]);
		}

		$this->render('diamond_list_form.html',array(
				'bar'=>Auth::getBar(),
				'dd'=>new DictView(new DictModel(1)),
				'view'=>new DiamondListView(new DiamondListModel(19)),
				'is_logo'=>$is_logo,
				'param'=>$param,
				'kefu_type'=>$kefu_type
		));
	}
	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$pageSize = isset($_REQUEST["pageSize"]) ? intval($_REQUEST["pageSize"]) : 15 ;
		
		$flag = 0;
		
		$companyId = $_SESSION['companyId'];
		$companyModel = new CompanyModel(1);
		$comInfo = $companyModel->select2("*","id={$companyId}",2);
		
		$_warehouse = array();
		$companyList = array();

		//company_type<>1 ：个体店和经销商 只可以看 （自己门店 +总公司）
		 //经销商可以看  所有直营店期货(kgk+enjoy除外) + 自己门店现货 +浩鹏公司现货
		if($comInfo['company_type']<>1){
			$companyList = array($companyId,58); //自己门店现货 +浩鹏公司现货
			$flag = 1;
		} else {
			if (!Auth::user_is_from_base_company()) {
				exit('非总部或经销商店人员不能查看此页面');
			}
			$companyList = array(58); //浩鹏公司现货
			$flag = 1;
		}
		
		if($companyList){
			/*
			$wareshou_model = new ApiWarehouseModel();
			$warehouse = $wareshou_model->get_warehouse_all(1,  implode(',', $companyList));
			if($warehouse['error']==0){
				$_warehouse = array_column($warehouse['data'],'code');
			}*/
			$where = array('diamond_warehouse' =>1 , 'company_id' =>implode(',', $companyList));
			$warehouse = $companyModel->getWarehouse_Where($where);			        
			if(!empty($warehouse)){
			    $_warehouse = array_column($warehouse,'code');
			}			
			if($flag){
				array_push($_warehouse, 'COM'); //直营店期货
			}
		}
		$not_from_ad=array();
		if(SYS_SCOPE=='zhanting')
		    $not_from_ad = array('11','17');	//直营店期货(kgk+enjoy除外)	


        $not_from_ad = array();  
	    if(SYS_SCOPE=='zhanting'){
            $not_from_ad = array('11','17');  //展厅过滤凯吉凯和一加一的期货
	    }
		$args = array(
				'mod'	=> _Request::get("mod"),
				'con'	=> substr(__CLASS__, 0, -10),
				'act'	=> __FUNCTION__,
				'page'=>  $page,
				'pageSize'=>  $pageSize,
				//'goods_sn'=>  _Request::getString('goods_sn'),
				'carat_min'=>  _Request::getFloat('carat_min'),
				'carat_max'=>  _Request::getFloat('carat_max'),
				'price_min'=>  _Request::getFloat('price_min'),
				'price_max'=>  _Request::getFloat('price_max'),
				'clarity[]'=> _Request::getList('clarity'),
				'color[]'=> _Request::getList('color'),
				'shape[]'=> _Request::getList('shape'),
				'cut[]'=> _Request::getList('cut'),
				'polish[]'=> _Request::getList('polish'),
				'symmetry[]'=> _Request::getList('symmetry'),
				'fluorescence[]'=> _Request::getList('fluorescence'),
				'cert[]'=> _Request::getList('cert'),
				'cert_id'=> _Request::getInt('cert_id'),
				'is_active'=> _Request::getInt('is_active'),
				'status'=> _Request::getInt('status'),
				//'goods_name'=> _Request::getString('goods_name'),
				//'from_ad'=> _Request::getInt('from_ad'),
				'not_from_ad'=> $not_from_ad,
				'gm'=> _Request::getInt('gm'),
				'zdj'=> _Request::getString('zdj'),
				'stonesort'=> _Request::getString('stonesort'),
				'yansesort'=> _Request::getString('yansesort'),
				'jdsort'=> _Request::getString('jdsort'),
				'good_type'=> _Request::getInt('good_type'),
				's_carats_tsyd1'=> _Request::getFloat('s_carats_tsyd1'),
				'e_carats_tsyd1'=> _Request::getFloat('e_carats_tsyd1'),
				's_carats_tsyd2'=> _Request::getFloat('s_carats_tsyd2'),
				'e_carats_tsyd2'=> _Request::getFloat('e_carats_tsyd2'),
				'gemx_zhengshu'=> _Request::getString('gemx_zhengshu'),
				'ysyd'=> _Request::getInt('ysyd'),
				'company[]'=> $companyList,
				'ssy_active'=> _Request::getInt('ssy_active',0),//双十一
				'pf_price_min' => _Request::getFloat('pf_price_min'),
				'pf_price_max' => _Request::getFloat('pf_price_max'),
				'not_from_ad'   => $not_from_ad,
		);
		$where = array(
				'page'=>  $page,
				'pageSize'=>  $pageSize,
				'goods_sn'=>  _Request::getString('goods_sn'),
				'carat_min'=>  _Request::getFloat('carat_min'),
				'carat_max'=>  _Request::getFloat('carat_max'),
				'price_min'=>  _Request::getFloat('price_min'),
				'price_max'=>  _Request::getFloat('price_max'),
				'clarity'=> _Request::getList('clarity'),
				'color'=> _Request::getList('color'),
				'shape'=> _Request::getList('shape'),
				'cut'=> _Request::getList('cut'),
				'polish'=> _Request::getList('polish'),
				'symmetry'=> _Request::getList('symmetry'),
				'fluorescence'=> _Request::getList('fluorescence'),
				'cert'=> _Request::getList('cert'),
				'cert_id'=> _Request::getString('cert_id'),
				'is_active'=> _Request::getInt('is_active'),
				'status'=> 1,//_Request::getInt('status'),
				'goods_name'=> _Request::getString('goods_name'),
				'from_ad'=> _Request::getInt('from_ad'),
				'not_from_ad'=> $not_from_ad,
				'gm'=> _Request::getInt('gm'),
				'zdj'=> _Request::getString('zdj'),
				'stonesort'=> _Request::getString('stonesort'),
				'yansesort'=> _Request::getString('yansesort'),
				'jdsort'=> _Request::getString('jdsort'),
				'good_type'=> _Request::getInt('good_type'),
				's_carats_tsyd1'=> $args['s_carats_tsyd1'],
				'e_carats_tsyd1'=> $args['e_carats_tsyd1'],
				's_carats_tsyd2'=> $args['s_carats_tsyd2'],
				'e_carats_tsyd2'=> $args['e_carats_tsyd2'],
				'gemx_zhengshu'=> _Request::getString('gemx_zhengshu'),
				'ysyd'=> $args['ysyd'],
				'warehouse'=> $_warehouse,
				'pf_price_min' => _Request::getFloat('pf_price_min'),
				'pf_price_max' => _Request::getFloat('pf_price_max'),
				'not_from_ad'   => $not_from_ad,
		);
		$model = new DiamondListModel(19);
		$data = $model->pageList($where);
		$pageData = $data['data'];
		//print_r($data);
		$datalist=$pageData;
		$_goods_list=array();
		$api_model = new ApiWarehouseModel();
		foreach($datalist['data'] as $key => $val){
			if($val['good_type']==1){
				$company_name = $api_model->get_company_name($val['warehouse']);
				if($company_name['error']==0){
					$val['company_name']=$company_name['data'];
                    if(SYS_SCOPE=='zhanting' && $val['company_name']=='总公司')
                    	$val['company_name']='浩鹏';					
				}else{
					$val['company_name']='总公司';
				}
			}else{
				$val['company_name'] = '预定';
			}
			if($val['cert']=='HRD-D' && $val['kuan_sn']!=''){
				$dia_kuan=array();
				$dia_kuan=$model->get_diamond_by_kuan_sn($val['kuan_sn']);
				if($dia_kuan){
					foreach($dia_kuan['data'] as $k => &$v){
						if($v['goods_sn']!=$val['goods_sn']){
							if($v['good_type']==1){
								$company_name = $api_model->get_company_name($val['warehouse']);
								if($company_name['error']==0){
									$v['company_name']=$company_name['data'];
				                    if(SYS_SCOPE=='zhanting' && $val['company_name']=='总公司')
				                    	$val['company_name']='浩鹏';									
								}else{
									$v['company_name']='总公司';
								}
							}else{
								$v['company_name'] = '预定';
							}
							$val['add']=$v;
							break;
						}
					}
				}
			}
			$_goods_list[]=$val;
			
			unset($val);
			unset($v);
			$datalist['data']=$_goods_list;
			
			$kuan_sn=array();
			foreach($datalist['data'] as $key=>$val)
			{
				if($val['kuan_sn']!=''){
					if(!in_array($val['kuan_sn'],$kuan_sn)){
						$kuan_sn[]=$val['kuan_sn'];
					}else{
						unset($datalist['data'][$key]);
					}
				}
			}
		}
		
		$pageData = $datalist;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'zt_diamond_list_search_page';
		$this->render('diamond_list_search_list.html',array(
				'pa'=>Util::page($pageData),
				'dd'=>new DictView(new DictModel(1)),
				'page_list'=>$datalist,
		));
	}
	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$model = new DiamondListModel(19);
		$data = $model->getRowById($id);
		if(empty($data['data'])){
			die('数据错误!');
		}
		$this->render('diamond_list_show.html',array(
				'd'=>$data['data'],
				'dd'=>new DictView(new DictModel(1)),
		));
	}
	

    /**
     *  show，渲染查看页面
     */
    public function showall() {
        $this->render('dia_info_jxs_showall.html');
    }

    public function dow(){
        set_time_limit(0);
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312','导出').time().".xls");

		$flag = 0;
		
		$companyId = $_SESSION['companyId'];
		$companyModel = new CompanyModel(1);
		$comInfo = $companyModel->select2("*","id={$companyId}",2);
		$dd = new DictView(new DictModel(1));
		$_warehouse = array();
		$companyList = array();

		//company_type<>1 ：个体店和经销商 只可以看 （自己门店 +总公司）
		 //经销商可以看  所有直营店期货(kgk+enjoy除外) + 自己门店现货 +浩鹏公司现货
		if($comInfo['company_type']<>1){
			$companyList = array($companyId,58); //自己门店现货 +浩鹏公司现货
			$flag = 1;
		} else {
			if (!Auth::user_is_from_base_company()) {
				exit('非总部或经销商店人员不能查看此页面');
			}
			$companyList = array(58); //浩鹏公司现货
			$flag = 1;
		}
		
		if($companyList){
			/*
			$wareshou_model = new ApiWarehouseModel();
			$warehouse = $wareshou_model->get_warehouse_all(1,  implode(',', $companyList));
			if($warehouse['error']==0){
				$_warehouse = array_column($warehouse['data'],'code');
			}*/
			$where = array('diamond_warehouse' =>1 , 'company_id' =>implode(',', $companyList));
			$warehouse = $companyModel->getWarehouse_Where($where);			        
			if(!empty($warehouse)){
			    $_warehouse = array_column($warehouse,'code');
			}			
			if($flag){
				array_push($_warehouse, 'COM'); //直营店期货
			}
		}
		$not_from_ad=array();
		if(SYS_SCOPE=='zhanting')
		    $not_from_ad = array('11','17');	//直营店期货(kgk+enjoy除外)	

        $page=1;

        $pageSize =5000;
		$args = array(
				'mod'	=> _Request::get("mod"),
				'con'	=> substr(__CLASS__, 0, -10),
				'act'	=> __FUNCTION__,
				'page'=>  $page,
				'pageSize'=>  $pageSize,
				//'goods_sn'=>  _Request::getString('goods_sn'),
				'carat_min'=>  _Request::getFloat('carat_min'),
				'carat_max'=>  _Request::getFloat('carat_max'),
				'price_min'=>  _Request::getFloat('price_min'),
				'price_max'=>  _Request::getFloat('price_max'),
				'clarity[]'=> _Request::getList('clarity'),
				'color[]'=> _Request::getList('color'),
				'shape[]'=> _Request::getList('shape'),
				'cut[]'=> _Request::getList('cut'),
				'polish[]'=> _Request::getList('polish'),
				'symmetry[]'=> _Request::getList('symmetry'),
				'fluorescence[]'=> _Request::getList('fluorescence'),
				'cert[]'=> _Request::getList('cert'),
				'cert_id'=> _Request::getInt('cert_id'),
				'is_active'=> _Request::getInt('is_active'),
				'status'=> _Request::getInt('status'),
				//'goods_name'=> _Request::getString('goods_name'),
				//'from_ad'=> _Request::getInt('from_ad'),
				'gm'=> _Request::getInt('gm'),
				'zdj'=> _Request::getString('zdj'),
				'stonesort'=> _Request::getString('stonesort'),
				'yansesort'=> _Request::getString('yansesort'),
				'jdsort'=> _Request::getString('jdsort'),
				'good_type'=> _Request::getInt('good_type'),
				's_carats_tsyd1'=> _Request::getFloat('s_carats_tsyd1'),
				'e_carats_tsyd1'=> _Request::getFloat('e_carats_tsyd1'),
				's_carats_tsyd2'=> _Request::getFloat('s_carats_tsyd2'),
				'e_carats_tsyd2'=> _Request::getFloat('e_carats_tsyd2'),
				'gemx_zhengshu'=> _Request::getString('gemx_zhengshu'),
				'ysyd'=> _Request::getInt('ysyd'),
				'company[]'=> $companyList,
				'ssy_active'=> _Request::getInt('ssy_active',0),//双十一
				'pf_price_min' => _Request::getFloat('pf_price_min'),
				'pf_price_max' => _Request::getFloat('pf_price_max'),
				'not_from_ad'   => $not_from_ad,
		);
		$where = array(
				'page'=>  $page,
				'pageSize'=>  $pageSize,
				'goods_sn'=>  _Request::getString('goods_sn'),
				'carat_min'=>  _Request::getFloat('carat_min'),
				'carat_max'=>  _Request::getFloat('carat_max'),
				'price_min'=>  _Request::getFloat('price_min'),
				'price_max'=>  _Request::getFloat('price_max'),
				'clarity'=> _Request::getList('clarity'),
				'color'=> _Request::getList('color'),
				'shape'=> _Request::getList('shape'),
				'cut'=> _Request::getList('cut'),
				'polish'=> _Request::getList('polish'),
				'symmetry'=> _Request::getList('symmetry'),
				'fluorescence'=> _Request::getList('fluorescence'),
				'cert'=> _Request::getList('cert'),
				'cert_id'=> _Request::getString('cert_id'),
				'is_active'=> _Request::getInt('is_active'),
				'status'=> 1,//_Request::getInt('status'),
				'goods_name'=> _Request::getString('goods_name'),
				'from_ad'=> _Request::getInt('from_ad'),
				'gm'=> _Request::getInt('gm'),
				'zdj'=> _Request::getString('zdj'),
				'stonesort'=> _Request::getString('stonesort'),
				'yansesort'=> _Request::getString('yansesort'),
				'jdsort'=> _Request::getString('jdsort'),
				'good_type'=> _Request::getInt('good_type'),
				's_carats_tsyd1'=> $args['s_carats_tsyd1'],
				'e_carats_tsyd1'=> $args['e_carats_tsyd1'],
				's_carats_tsyd2'=> $args['s_carats_tsyd2'],
				'e_carats_tsyd2'=> $args['e_carats_tsyd2'],
				'gemx_zhengshu'=> _Request::getString('gemx_zhengshu'),
				'ysyd'=> $args['ysyd'],
				'warehouse'=> $_warehouse,
				'pf_price_min' => _Request::getFloat('pf_price_min'),
				'pf_price_max' => _Request::getFloat('pf_price_max'),
				'not_from_ad'   => $not_from_ad,
		);
		//旧的Api接口查询太慢
		//$model = new DiamondListModel(19);
        $model = new DiamondInfoModel(52); 
        $getShapeName=$model->getShapeName();
        $company_array=$model->get_all_diamond_warehouse_company();  
        $download_flag = 1;
        $result_list=array();
        $csv = "<table><tr><td>商品编码</td><td>所在地</td><td>形状</td><td>石重</td><td>颜色</td><td>净度</td><td>切工</td><td>对称</td><td>抛光</td><td>荧光</td><td>批发价</td><td>证书类型</td><td>证书号</td><td>货品类型</td><td>是否活动</td></tr>";
        
		while($download_flag){
				//$data = $model->pageList($where);
			    $data=$model->GetDiamondList_api($where);
				$where['page'] = $where['page'] +1 ;
				
				$_goods_list=array();
				//echo $where['page'].'=';
				//echo "<pre>";
				//print_r($datalist['data']);
				if(!empty($data)){
					foreach($data as $key => $val){
						if($val['good_type']==1){
							if(!empty($company_array[$val['warehouse']])){
								$data[$key]['company_name']=$company_array[$val['warehouse']];
							}else{
								$data[$key]['company_name']='总公司';
							}
						}else{
							$data[$key]['company_name'] = '预定';
						}
						/*
						if($val['cert']=='HRD-D' && $val['kuan_sn']!=''){
							$dia_kuan=array();
							$dia_kuan=$model->get_diamond_by_kuan_sn($val['kuan_sn']);
							if($dia_kuan){
								foreach($dia_kuan as $k => &$v){
									if($v['goods_sn']!=$val['goods_sn']){
										if($v['good_type']==1){
											//$company_name = $model->getCompanyName($val['warehouse']);
											if(!empty($company_array[$val['warehouse']])){
												$v['company_name']=$company_array[$val['warehouse']];
											}else{
												$v['company_name']='总公司';
											}
										}else{
											$v['company_name'] = '预定';
										}
										$val['add']=$v;
										break;
									}
								}
							}
						}
						$_goods_list[]=$val;
						
						unset($val);
						unset($v);
						$datalist['data']=$_goods_list;
						
						$kuan_sn=array();
						foreach($datalist['data'] as $key=>$val)
						{
							if($val['kuan_sn']!=''){
								if(!in_array($val['kuan_sn'],$kuan_sn)){
									$kuan_sn[]=$val['kuan_sn'];
								}else{
									unset($datalist['data'][$key]);
								}
							}
						}*/
					}
				}else{
					$download_flag=false;
				}

				//$result_list =array_merge($result_list, $datalist['data']);
		        foreach ($data as $key => $v) {		        	 
		          	$csv .= "<tr><td>".(!empty($v['goods_sn']) ? $v['goods_sn'] : $v['kuan_sn'])."</td><td>".(!empty($v['company_name']) ? $v['company_name'] : '')."</td><td>".(isset($getShapeName[$v['shape']])?$getShapeName[$v['shape']]:'')."</td><td>".$v['carat']."</td><td>".$v['color']."</td><td>".$v['clarity']."</td><td>".$v['cut']."</td><td>".$v['symmetry']."</td><td>".$v['polish']."</td><td>".$v['fluorescence']."</td><td>".( $v['pifajia']>0 ? $v['pifajia'] : '--' )."</td><td>".$v['cert']."</td><td>".$v['cert_id']."</td><td>".($v['good_type']==1 ? '现货':'预订')."</td><td>".($v['is_active']==1?"正常":"活动")."</td></tr>";
		        }

		}
          
        //$csv = "<table><tr><td>商品编码</td><td>所在地</td><td>形状</td><td>石重</td><td>颜色</td><td>净度</td><td>切工</td><td>对称</td><td>抛光</td><td>荧光</td><td>批发价</td><td>证书类型</td><td>证书号</td><td>货品类型</td><td>是否活动</td></tr>";
        //foreach ($result_list as $key => $v) {
        	 //$csv .="<tr><td>".$v['goods_sn']."</td></tr>"
        //  	$csv .= "<tr><td>".(!empty($v['goods_sn']) ? $v['goods_sn'] : $v['kuan_sn'])."</td><td>".$v['company_name']."</td><td>".(isset($getShapeName[$v['shape']])?$getShapeName[$v['shape']]:'')."</td><td>".$v['carat']."</td><td>".$v['color']."</td><td>".$v['clarity']."</td><td>".$v['cut']."</td><td>".$v['symmetry']."</td><td>".$v['polish']."</td><td>".$v['fluorescence']."</td><td>".( $v['pifajia']>0 ? $v['pifajia'] : '--' )."</td><td>".$v['cert']."</td><td>".$v['cert_id']."</td><td>".($v['good_type']==1 ? '现货':'预订')."</td><td>".($v['is_active']==1?"正常":"活动")."</td></tr>";
        //}

        $csv .="</table>";
        echo $csv;
    }
}