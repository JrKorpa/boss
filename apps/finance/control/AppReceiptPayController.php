<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReceiptPayController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 22:49:36
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiptPayController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('download', 'printReceipt');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $SalesChannelsModel = new SalesChannelsModel(1);
        if($_SESSION['userType'] == 1){
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        }else{
            $ids = explode(',', $_SESSION['qudao']);
            $channellist = $SalesChannelsModel->getSalesChannel($ids);
        }
		$this->render('app_receipt_pay_search_form.html',array('bar'=>Auth::getBar(),'channellist'=>$channellist));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
        if($_SESSION['userType']==1){
            $department = _Request::getInt('pay_department')?_Request::getInt('pay_department'):0;
        }else{
            if(isset($_REQUEST['pay_department'])){
                $department = _Request::getString('pay_department')?_Request::getString('pay_department'):($_SESSION['qudao']?$_SESSION['qudao']:-1);
            }else{
                $department = _Request::getString('pay_department')?_Request::getString('pay_department'):($_SESSION['qudao']?current(explode(',', $_SESSION['qudao'])):-1);
            }
        }
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
            'order_sn' => _Request::getString('order_sn'),
            'receipt_sn' => _Request::getString('receipt_sn'),
            'status' => _Request::getInt('status'),
            'pay_department' =>$department,
            'pay_start_time' => _Request::getString('pay_start_time'),
            'pay_end_time' => _Request::getString('pay_end_time'),
            'add_start_time' => _Request::getString('add_start_time'),
            'add_end_time' => _Request::getString('add_end_time'),
            'type'=> _Request::getString('type'),
			//'参数' = _Request::get("参数");
		);
        $where = array();
//        $channerids = '';
//        $ChannelM = new SalesChannelsModel(1);
//        if($args['pay_department']!=''){
//            $channeridarr =  $ChannelM->getOwns($args['type'],$args['pay_department']);
//            if(!empty($channeridarr)){
//                foreach($channeridarr as $key=>$val){
//                    $channerids.=$val['id'].',';
//                }
//                $where['channerids']=rtrim($channerids,',');
//            }else{
//                $where['channerids']=0;
//            }
//
//        }else{
//            $where['channerids']=false;
//        }

		$page = _Request::getInt("page",1);
        $where['order_sn'] = $args['order_sn'];
        $where['receipt_sn'] = $args['receipt_sn'];
        $where['status'] = $args['status'];
        $where['pay_start_time'] = $args['pay_start_time'];
        $where['pay_end_time'] = $args['pay_end_time'];
        $where['pay_department'] = $args['pay_department'];
        $where['add_start_time'] = $args['add_start_time'];
        $where['add_end_time'] = $args['add_end_time'];
        
        //收款方式
		$Paymentmodel = new PaymentModel(1);
		$Paymentm = $Paymentmodel->getAll();
        $Paymentms=array();
        foreach($Paymentm as $k=>$v){
            $Paymentms[$v['id']]=$v['pay_name'];
        }

		$model = new AppReceiptPayModel(29);
		$data = $model->pageList($where,$page,10,false);

        //获取全部的有效的销售渠道
        $SalesChannelsModel = new SalesChannelsModel(1);
        $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
        //获取所有数据
        $allSalesChannelsData = array();
        foreach ($getSalesChannelsInfo as $val){
            $allSalesChannelsData[$val['id']] = $val['channel_name'];
        }

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_receipt_pay_search_page';
		$this->render('app_receipt_pay_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'Paymentms'=>$Paymentms,
            'allSalesChannelsData'=>$allSalesChannelsData,
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_receipt_pay_info.html',array(
			'view'=>new AppReceiptPayView(new AppReceiptPayModel(29))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}
	
	/**
     * 	showLog，查看日志页面
     */
    public function showLog($params) {
        $id = intval($params["id"]);
        $result = array('success' => 0, 'error' => '');
		$model = new AppReceiptPayLogModel(29);
		$where['receipt_id'] = $id;
		$page = _Request::getInt("page", 1);
		$data = $model->pageList($where,$page,10,0);
        $result['content'] = $this->fetch('app_receipt_pay_show.html', array(
            'view' => $data
        ));
        $result['title'] = '查看日志';
        Util::jsonExit($result);
    }

    
    public function download($param) {
        if($_SESSION['userType']==1){
            $department = _Request::getInt('pay_department')?_Request::getInt('pay_department'):0;
        }else{
            if(isset($_REQUEST['pay_department'])){
                $department = _Request::getString('pay_department')?_Request::getString('pay_department'):($_SESSION['qudao']?$_SESSION['qudao']:-1);
            }else{
                $department = _Request::getString('pay_department')?_Request::getString('pay_department'):($_SESSION['qudao']?substr($_SESSION['qudao'], 0,1):-1);
            }
        }
        $args = array(
            'order_sn' => _Request::getString('order_sn'),
            'receipt_sn' => _Request::getString('receipt_sn'),
            'status' => _Request::getInt('status'),
            'pay_department' => $department,
            'pay_start_time' => _Request::getString('pay_start_time'),
            'pay_end_time' => _Request::getString('pay_end_time'),
            'add_start_time' => _Request::getString('add_start_time'),
            'add_end_time' => _Request::getString('add_end_time')
                //'参数' = _Request::get("参数");
        );
        $model = new AppReceiptPayModel(29);

        $data = $model->pageList($args, 1, 10000000000000000, false);
        
        if ($data['data']) {
            //获取全部的有效的销售渠道
            $SalesChannelsModel = new SalesChannelsModel(1);
            $getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
            //获取所有数据
            $allSalesChannelsData = array();
            foreach ($getSalesChannelsInfo as $val){
                $allSalesChannelsData[$val['id']] = $val['channel_name'];
            }
            foreach ($data['data'] as &$val) {
                $val['status'] = $model->getStatusList($val['status']);
                $val['department'] = $allSalesChannelsData[$val['department']];
            }
            unset($val);
            $down = $data['data'];


            $xls_content = "点款收据号码,状态,收款金额,收款方式,收款时间,收款人,收款方,订单号,客户姓名,收据日期,操作人\r\n";
            foreach ($down as $key => $val) {
                $xls_content .= $val['receipt_sn'] . ",";
                $xls_content .= $val['status'] . ",";
                $xls_content .= $val['pay_fee'] . ",";
                $xls_content .= $this->getPayTypeName($val['pay_type']) . ",";
                $xls_content .= $val['pay_time'] . ",";
                $xls_content .= $val['pay_user'] . ",";
                $xls_content .= $val['department'] . ",";
                $xls_content .= $val['order_sn'] . ",";
                $xls_content .= $val['customer'] . ",";
                $xls_content .= $val['add_time'] . ",";
                $xls_content .= $val['add_user'] . "\n";
            }
        } else {
            $xls_content = '没有数据！';
        }
        header("Content-type: text/html; charset=gbk");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
        echo iconv("utf-8", "gbk", $xls_content);
        exit;
    }
    
    public function printCount() {
        $id = _Request::getInt('id');
        $model = new AppReceiptPayModel($id, 30);
        $info = $model->getDataObject();
        $num = $info['print_num'] + 1;
        $model->setValue('print_num', $num);
        $res = $model->save(true);
        if ($res) {
            //插入定金日志
            $_model = new AppReceiptPayLogModel(30);
            $receiptlogdata ['receipt_id'] = $id;
            $receiptlogdata ['receipt_action'] = '定金收据打印';
            $receiptlogdata ['add_time'] = date("Y-m-d H:i:s");
            $receiptlogdata ['add_user'] = $_SESSION['userName'];
            $_model->saveData($receiptlogdata, array());
        }
    }
    
    public function printReceipt() {
        $model = new AppReceiptPayModel(29);
        $receipt_sn = _Request::getString('receipt_sn');
        $print = $model->getRowList($receipt_sn);
        //获取大写数字
        $money = array("0" => "零", "1" => "壹", "2" => "贰", "3" => "叁", "4" => "肆", "5" => "伍", "6" => "陆", "7" => "柒", "8" => "捌", "9" => "玖", "10" => "拾");


        $print['money'] = strrev($print['pay_fee']);
        $payView = new PaymentView(new PaymentModel($print['pay_type'],1));
        $print['pay_type'] = $payView->get_pay_name();
        $this->render('receipt_print.html', array('bigmoney' => $print['money'], 'money' => $money, 'ret_list' => $print));
    }

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = intval($params["tab_id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_receipt_pay_info.html',array(
			'view'=>new AppReceiptPayView(new AppReceiptPayModel($id,29)),
			'tab_id'=>$tab_id
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('app_receipt_pay_show.html',array(
			'view'=>new AppReceiptPayView(new AppReceiptPayModel($id,29)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;
		$olddo = array();
		$newdo=array();

		$newmodel =  new AppReceiptPayModel(30);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new AppReceiptPayModel($id,30);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '修改此处为想显示在页签上的字段';
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppReceiptPayModel($id,30);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

    public function getPayTypeName($id) {
        $payView = new PaymentView(new PaymentModel($id,1));
        return $payView->get_pay_name();
    }

    public function getTree(){
        $type = _Request::get('type');
        $model = new CompanyModel(1);
        $res = $model->getAllDCS();

        switch($type){
            case '1':{
               echo   $this->fetch('app_receipt_pay_list_option.html',array(
                    'list'=>$res[1],
                ));
            }
            case '2':{
                echo  $this->fetch('app_receipt_pay_list_option.html',array(
                    'list'=>$res[2],
                ));
            }
            case '3':{
                echo  $this->fetch('app_receipt_pay_list_option.html',array(
                    'list'=>$res[3],
                ));
            }
            default:{
                return false;
            }
        }

    }


}

?>