<?php
/**
 * 用石明细报表
 *  -------------------------------------------------
 *   @file		: StoneUseReportController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2016-02-27 14:29:07
 *   @update	:
 *  -------------------------------------------------
 */
class StoneUseReportController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('export');
   
    /**
     * 用石明细报表
     * @see CommonController::index()
     */
    public function index($param){
        //工厂列表=供应商列表
        $proApiModel = new ApiProModel();
        $pro_list = $proApiModel->GetSupplierList(); 
        $factory_id = _Request::getInt('id');
        
        $this->render('stone_use_report_search_form.html',array('bar'=>Auth::getBar(),
                'pro_list'=>$pro_list,
                'factory_id'=>$factory_id
        ));

    }
    
    public function search ($params)
	{   
	   $args = array(
            'mod'	=> _Request::get("mod"),
            'con'	=> substr(__CLASS__, 0, -10),
            'act'	=> __FUNCTION__,
            'processors_id'	=> _Request::getString("processors_id"),
            'factory_id'	=> _Request::getString("factory_id"),
            'check_time_min'=>_Request::getString("check_time_min"),
            'check_time_max'=>_Request::getString("check_time_max"),
            'create_time_min'=>_Request::getString("create_time_min"),
            'create_time_max'=>_Request::getString("create_time_max"),
            'status'=>_Request::getString("status"),
        );
 
        $page = _Request::getInt("page",1);
        $where = $args;
 
        $model = new ShibaoModel(45);
        $data = $model->pageListStoneUse($where,$page,10,false);
        $pageData = $data;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'stone_use_report_search_page';
        $this->render('stone_use_report_search_list.html', array(
            'pa'=>Util::page($pageData),
            'page_list'=>$data
        ));
	}
	
	/**
	 * 查询报表导出
	 * @param unknown $param
	 */
	public function export($param){
	    set_time_limit(0);
	    $where = array(
	        'processors_id'	=> _Request::getString("processors_id"),
	        'factory_id'	=> _Request::getString("factory_id"),
	        'check_time_min'=>_Request::getString("check_time_min"),
	        'check_time_max'=>_Request::getString("check_time_max"),
	        'create_time_min'=>_Request::getString("create_time_min"),
	        'create_time_max'=>_Request::getString("create_time_max"),
	        'status'=>_Request::getString("status"),
	    );
 
	    header('Content-Type: application/vnd.ms-excel');
	    header("Content-Disposition: attachment;filename=用石明细报表.csv");
	    header('Cache-Control: max-age=0');
	
	    $model = new ShibaoModel(45);

	    $titleArr=array('单号','制单人','制单时间','审核人','审核时间','状态','工厂名称','供应商','石包','纸质单号','总数量/粒','总重量/ct','每卡采购价格(元)','金额','规格','备注');
	    foreach ($titleArr as $k => $v) {
	        $titleArr[$k]=iconv('utf-8', 'GB18030', $v);
	    }
	    echo "\"".implode("\",\"",$titleArr)."\"\r\n";
	    $page = 1;
	    $pageSize=30;
	    $pageCount=1;
	    $recordCount = 0;
	    $list_sql=$model->getListStoneUseSql($where);
	    //无限循环拉取数据
	    while($page <= $pageCount){
	
	        $data = $model->db()->getPageListForExport($list_sql,array(),$page,$pageSize,false,$recordCount);
	        $page ++;
	        if(!empty($data['data'])){
	            $recordCount = $data['recordCount'];
	            $pageCount = $data['pageCount'];
	            $data = $data['data'];
	            if(!is_array($data) || empty($data)){
	                continue;
	            }
	            foreach($data as $d){	                
	                $temp = array();
	                $temp['bill_no'] = $d['bill_no'];
	                $temp['create_user'] = $d['create_user'];
	                $temp['create_time'] = $d['create_time'];
	                $temp['check_user'] = $d['check_user'];
	                $temp['check_time'] = $d['check_time'];
	                $temp['status'] = $this->dd->getEnum("diabill.status",$d['status']);
	                $temp['factory_name'] = $d['factory_name'];
	                $temp['processors_name'] = $d['processors_name'];	                
	                $temp['dia_package'] = $d['dia_package'];
	                $temp['paper_no'] = $d['paper_no'];
	                $temp['num'] = $d['num'];
	                $temp['weight'] = $d['weight'];
	                $temp['purchase_price'] = $d['purchase_price'];
	                $temp['price'] = $d['price'];
	                $temp['specification'] = $d['specification'];
	                $temp['remark'] = $d['remark'];
	                foreach ($temp as $k => $v) {
	                    $temp[$k] = iconv('utf-8', 'GB18030', $v);
	                }
	                echo "\"".implode("\",\"",$temp)."\"\r\n";
	            }
	        }//end if
	    }//end while
	}//end fuantion export
}