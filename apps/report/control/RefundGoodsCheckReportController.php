<?php
/**
 * 退款库管审核明细报表
 *  -------------------------------------------------
 *   @file		: RefundGoodsCheckReportController
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-13 11:05:03
 *   @update	:
 *  -------------------------------------------------
 */
class RefundGoodsCheckReportController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist  = array('exportCsv');
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $this->render('refund_goods_check_report_search_form.html',
            array('view'=>new AppReturnGoodsView(new AppReturnGoodsModel(31)),
                'bar'=>Auth::getBar()                
            ));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
            'mod'   => _Request::get("mod"),
            'con'   => substr(__CLASS__, 0, -10),
            'act'   => __FUNCTION__,
			'goods_status'	=> _Request::getInt("goods_status"),
			'goods_start_time'	=> _Request::getString("goods_start_time"),
			'goods_end_time'	=> _Request::getString("goods_end_time"),
		);
		$page = _Request::getInt("page",1);
		$where = array(
		    'goods_status'=>$args['goods_status'],
            'goods_start_time'=>$args['goods_start_time'],
            'goods_end_time'=>$args['goods_end_time']
        );

		$model = new AppReturnGoodsModel(31);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'refund_goods_check_report_search_page';
		$this->render('refund_goods_check_report_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
		    'view'=>new AppReturnGoodsView($model)
		));
	}
	/**
	 * 导出CSV报表
	 */
	public function exportCsv($params){
	    set_time_limit(0);
	    header('Content-Type: application/vnd.ms-excel');
	    header("Content-Disposition: attachment;filename=退款库管审核明细报表.csv");
	    header('Cache-Control: max-age=0');
	    
	    $args = array(
	        'mod'   => _Request::get("mod"),
	        'con'   => substr(__CLASS__, 0, -10),
	        'act'   => __FUNCTION__,
	        'goods_status'	=> _Request::getInt("goods_status"),
	        'goods_start_time'	=> _Request::getString("goods_start_time"),
	        'goods_end_time'	=> _Request::getString("goods_end_time"),
	    );
	    $page = _Request::getInt("page",1);
	    $where = array(
	        'goods_status'=>$args['goods_status'],
	        'goods_start_time'=>$args['goods_start_time'],
	        'goods_end_time'=>$args['goods_end_time']
	    );	    
	    $model = new AppReturnGoodsModel(31);	   	    
	    $userModel = new UserModel(1);
	    $check_status_arr = array('0'=>'未操作','1'=>'审核通过','2'=>'审核驳回');
	    
	    $titleList = array(
	        "return_id"=>"流水号",
	        "order_sn"=>"订单号",
	        "apply_user_name"=>"申请人",
	        "department"=>"申请部门",
	        "return_type"=>"申请类型",
	        "return_by"=>"退款方式",
	        "consignee"=>"收款人",
	        "return_goods_id"=>"货号",
	        "goods_sn"=>"款号",
	        "apply_time"=>"申请时间",
	        "real_return_amount"=>"实退金额",
	        "leader_status"=>"主管状态",
	        "goods_status"=>"库管状态",
	        "goods_comfirm_id"=>"库管审核人",
	        "goods_time"=>"库管审核时间",
	        "cto_status"=>"事业部状态",
	        "deparment_finance_status"=>"现场财务状态",
	        "finance_status"=>"财务状态",
	    );
	    $_datalist = array();	    
	    foreach ($titleList as $key=>$vo){
	        $titleList[$key] = @iconv('utf-8', 'GB18030', $vo);
	        $_datalist[$key] = "";//初始化默认值为空值 到临时数组 数组
	    }

	    echo implode(",",$titleList),"\r\n";
	    $page = 1;
	    $pageSize=30;
	    $pageCount=1;
	    $recordCount = 0;
	    while($page <= $pageCount){
	    
	        $data = $model->pageList($where,$page,$pageSize,true,$recordCount);
	        $page++;
            $pageCount = $data['pageCount'];
            $recordCount = $data['recordCount'];
        
            if(!is_array($data['data']) || empty($data['data'])){
                continue;
            }
    	    foreach ($data['data'] as $vo){
    	        $datalist = $_datalist;//重置datalist 默认值为空值
    	        
    	        $datalist['return_id'] = $vo['return_id'];
    	        $datalist['order_sn'] = "'".$vo['order_sn'];	        
    	        $datalist['apply_user_name'] = $vo['apply_user_name'];
    	        $datalist['department'] = $vo['department'];	        
    	        $datalist['return_type'] = $this->dd->getEnum("refund.finance_type",$vo['return_type']);
    	        $datalist['return_by'] = $this->dd->getEnum("refund.return_by",$vo['return_by']);
    	        $datalist['consignee'] = $vo['consignee'];
    	        $datalist['return_goods_id'] = $vo['return_goods_id'];
    	        $datalist['goods_sn'] = $vo['goods_sn'];
    	        $datalist['apply_time'] = $vo['apply_time'];
    	        $datalist['real_return_amount'] = $vo['real_return_amount'];
    	        $datalist['leader_status'] = isset($check_status_arr[$vo['leader_status']])?$check_status_arr[$vo['leader_status']]:'未操作';
    	        $datalist['goods_status'] = isset($check_status_arr[$vo['goods_status']])?$check_status_arr[$vo['goods_status']]:'未操作';
    	        if($vo['goods_comfirm_id']>0){
    	           $datalist['goods_comfirm_id'] = $userModel->getAccount($vo['goods_comfirm_id']);
    	        }
    	        $datalist['goods_time'] = $vo['goods_time'];
    	        $datalist['cto_status'] = isset($check_status_arr[$vo['cto_status']])?$check_status_arr[$vo['cto_status']]:'未操作';
    	        $datalist['deparment_finance_status'] = isset($check_status_arr[$vo['deparment_finance_status']])?$check_status_arr[$vo['deparment_finance_status']]:'未操作';
    	        $datalist['finance_status'] = isset($check_status_arr[$vo['finance_status']])?$check_status_arr[$vo['finance_status']]:'未操作';;
    	        foreach ($datalist as $key=>$vo){
   	               $datalist[$key] = @iconv('utf-8', 'GB18030', $vo);
    	        }
    	        echo implode(",",$datalist),"\r\n";;
    	    }//end foreach $data['data']
	     }//end while
	    
	}

	
}
?>
