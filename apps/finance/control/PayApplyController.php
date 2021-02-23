<?php
/**
 *  -------------------------------------------------
 *   @file		: PayApplyController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-08 15:16:15
 *   @update	:
 *  -------------------------------------------------
 */
class PayApplyController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('search','downData','downloadDemo');
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$proList = $this->getProcessList();
		//1、应付申请单；2、应付调整单
        $recordTypeList = array(1=>'应付申请单',2=>'应付调整单');
        //$payApplyList = (new PayApplyModel(29))->getCatTypeList();

		$apiStyleModel = new ApiStyleModel();
		$catList = $apiStyleModel->getCatTypeInfo();
        if($catList){
            $catList=$catList[0];
        }else{
            $catList=array();
        }

		$this->render('pay_apply_search_form.html',array(
			'bar'=>Auth::getBar(),
			'view'=>new PayApplyView(new PayApplyModel(29)),
			'proList' => $proList,
		    'catList'=>$catList,
		    'recordTypeList'=>$recordTypeList
		    //'payApplyList'=>$payApplyList
		));
	}

	public function getProcessList()
	{
		$processorModel = new ApiProcessorModel();
		$process_list = $processorModel->GetSupplierList();
		return $process_list;
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$page_action = array(
			'mod'	=> $_REQUEST["mod"],
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
		   'down_info' => 	_Request::get('down_info')?_Request::get('down_info'):'',
			'sort' => 	_Request::get('sort'),
			'order' => 	_Request::get('order'),
			"company" => _Request::get('company'),
			"applyorderdostatus" => _Request::get('applyorderdostatus'),
			"process_list" => _Request::get('process_list'),
			"pay_apply_number" => _Request::get('pay_apply_number'),
			"totalpaytype" => _Request::get('totalpaytype'),
			"pay_number" => _Request::get('pay_number'),
			"start_make_date" => _Request::get('start_make_date'),
			"end_make_date" => _Request::get('end_make_date'),
			"start_check_date" => _Request::get('start_check_date'),
			"end_check_date" => _Request::get('end_check_date'),
			"fapiao"=>_Request::get('fapiao'),
		    "record_type"=>_Request::get('record_type'),
		    "cat_type"=>_Request::get('cat_type'),
		    //"style_type"=>_Request::get('style_type')
			);
		$filter = array();

		if(!empty($page_action["sort"]))
		{
			$filter["sort"] = $page_action["sort"];
		}
		if(!empty($page_action["order"]))
		{
			$filter["order"] = $page_action["order"];
		}
		if(!empty($page_action["company"]))
		{
			$filter["company"] = $page_action["company"];
		}
		if($page_action["applyorderdostatus"] != '')
		{
			$filter["status"] = $page_action["applyorderdostatus"];
		}
		if(!empty($page_action["process_list"]))
		{
			$filter["prc_id"] = $page_action["process_list"];
		}
		if(!empty($page_action["pay_apply_number"]))
		{
			$filter["pay_apply_number"] = $page_action["pay_apply_number"];
		}
		if(!empty($page_action["totalpaytype"]))
		{
			$filter["pay_type"] = $page_action["totalpaytype"];
		}
		if(!empty($page_action["pay_number"]))
		{
			$filter["pay_number"] = $page_action["pay_number"];
		}
		if(!empty($page_action["start_make_date"]))
		{
			$filter["start_make_time"] = $page_action["start_make_date"];
		}
		if(!empty($page_action["end_make_date"]))
		{
			$filter["end_make_time"] = $page_action["end_make_date"];
		}
		if(!empty($page_action["start_check_date"]))
		{
			$filter["start_check_time"] = $page_action["start_check_date"];
		}
		if(!empty($page_action["end_check_date"]))
		{
			$filter["end_check_time"] = $page_action["end_check_date"];
		}
		if(!empty($page_action["fapiao"]))
		{
			$filter["fapiao"] = $page_action["fapiao"];
		}
		if(!empty($page_action['record_type'])){
		    $filter["record_type"] = $page_action["record_type"];
		}
		if(!empty($page_action['cat_type'])){
		    $filter["cat_type"] = $page_action["cat_type"];
		}
// 		if(!empty($page_action['style_type'])){
// 		    $filter["style_type"] = $page_action["style_type"];
// 		}

		$apiStyleModel = new ApiStyleModel();
		$catList = $apiStyleModel->getCatTypeInfo();
        if($catList){
            foreach($catList[0] as $k=>$v){
                $catLists[$v['id']]=$v['name'];
            }
        }else{
            $catLists=array();
        }        

		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$Applymodel = new PayApplyModel(29);

        if(SYS_SCOPE == 'zhanting'){
            $filter['hidden'] = '0';
        }
	
		if($page_action['down_info']=='down_info'){
		    $data = $Applymodel->pageList($filter,$page,90000000);
		    $this->download($data);
		    exit;
		}
		
		$data = $Applymodel->pageList($filter,$page,15);
		$pageData = $data;
		$pageData['filter'] = $page_action;
		$pageData['jsFuncs'] = 'app_apply_bills_search_page';
		$this->render('pay_apply_search_list.html',array(
			'pa'=>Util::page($pageData),
			'catLists'=>$catLists,
			'page_list'=>$pageData
		));
	}

	/**
	 *	add, 应付申请单添加页面
	 */
	public function add ()
	{
		$proList = $this->getProcessList();
		$this->render('pay_apply_info.html',array(
			'view'=>new PayApplyView(new PayApplyModel(29)),
			'proList' => $proList
		));
	}

	/**
	 *	addTz, 应付调整单添加页面
	 */
	public function addTz ()
	{
		$proList = $this->getProcessList();
		$apiStyleModel = new ApiStyleModel();
		$catList = $apiStyleModel->getCatTypeInfo();
        if($catList){
            $catList=$catList[0];
        }else{
            $catList=array();
        }
		$this->render('pay_apply_tz_info.html',array(
			'view'=>new PayApplyView(new PayApplyModel(29)),
			'proList' => $proList,
            'catList' => $catList
		));
	}

	public function insertTz($params)
	{
		$result = array('success' => 0,'error' => '');
		$type	= $params['type'];
		$total_cope	= $params['total_cope'];
		$adj_reason	= $params['adj_reason'];
        $cat_type	= $params['cat_type'];

		$prc = explode("|",$params['prc_id']);
		$prc_id = $prc[0];
		$prc_name = $prc[1];

		if($prc_id=='')
		{
			$result['content'] = "请选择结算商";
			Util::jsonExit($result);
		}
		if($type=='')
		{
			$result['content'] = "请选择应付类型";
			Util::jsonExit($result);
		}
		if($total_cope=='')
		{
			$result['content'] = "应付金额不能为空";
			Util::jsonExit($result);
		}
		if($adj_reason=='')
		{
			$result['content'] = "调整原因不能为空";
			Util::jsonExit($result);
		}
		if($cat_type=='')
		{
			$result['content'] = "款式分类不能为空";
			Util::jsonExit($result);
		}

		$applydata = array(
			'pay_apply_number' => 'T-YFSQ',
			'make_time'	=> date('Y-m-d H:i:s',time()),
			'make_name'	=> $_SESSION['userName'],
			'check_time'	=> '0000-00-00 00:00:00',
			'check_name'	=> '',
			'company'		=> '58',
			'status'		=> 1,
			'prc_id'		=> $prc_id,
			'prc_name'		=> $prc_name,
			'pay_type'		=> $type,
			'amount'		=> 0,
			'total_cope'	=> $total_cope,
			'total_dev'		=> 0,
			'adj_reason'	=> $adj_reason,
            'style_type'	=> $cat_type,
			'record_type'=>'2'
		);
		$model = new PayApplyModel(29);
		$res = $model->savaAdjData($applydata);
		if($res['result']){
			$result['success'] = 1;
			$result['pay_apply_number'] = 'T-YFSQ'.$res['apply_id'];
		}
		Util::jsonExit($result);

	}


	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' => '');
		$prc = explode("|",$params['prc_id']);
		$prc_id = $prc[0];
		$prc_name = $prc[1];
		$type = $params['type'];

		if($prc_id=='')
		{
			$result['content'] = "请选择结算商";
			Util::jsonExit($result);
		}
		if($type=='')
		{
			$result['content'] = "请选择应付类型";
			Util::jsonExit($result);
		}

		if($_FILES['data']['name'] == '')
		{
			$result['content'] = "请上传要申请的数据";
			Util::jsonExit($result);
		}
		if(empty($_FILES['data']['tmp_name']))
		{
			$result['content'] = "上传文件不能为空";
			Util::jsonExit($result);
		}

		$data = $this->checkData($_FILES['data'],$type,$prc_id);//检查上传文件内容

		$applydata = array(
			'pay_apply_number' => 'YFSQ',
			'make_time'	=> date('Y-m-d H:i:s',time()),
			'make_name'	=> $_SESSION['userName'],
			'check_time'	=> '0000-00-00 00:00:00',
			'check_name'	=> '',
			'company'		=> '58',
			'status'		=> 1,
			'prc_id'		=> $prc_id,
			'prc_name'		=> $prc_name,
			'pay_type'		=> $type,
			'amount'		=> $data['amount'],
			'total_cope'	=> $data['total_cope'],
			'total_dev'		=> $data['total_dev'],
			'fapiao'		=> $_POST['fapiao']

		);
		$model = new PayApplyModel(29);
		$res = $model->saveDatas($applydata,$data['data']);
		if($res['result']){
			$result['success'] = 1;
			$result['pay_apply_number'] = 'YFSQ'.$res['apply_id'];
                        $result['id'] = $res['apply_id'];
		}
		Util::jsonExit($result);
	}

	/*====================
	** 检查上传的csv数据，并返回最终符合的数据
	** file上传的数据
	** type 此次申请的应付类型(来判断数据是否和申请的类型相同)
	** prc_id 此次申请的供货商ID
	** pay_apply_number 应付申请单号（修改的时候判断所用）
	====================*/
	public function checkData($file,$type,$prc_id,$pay_apply_number='')
	{
		$file_array = explode(".",$file['name']);
		$file_extension = strtolower(array_pop($file_array));
		$dict = new DictModel(1);
		if($file_extension != 'csv'){
		   echo '请上传CSV格式的文件';exit;
		}
		$f = fopen($file['tmp_name'],"r");
		$goodsModel = new GoodsModel(29);
		$goods = array();
		$stora_mode = array('1'=>'代销或借入','2'=>'购买或委托加工','3'=>'石包');
		$i = 0;
		$total_cope = 0;
		$total_dev = 0;
		while(! feof($f)){
			$con = fgetcsv($f);
			if ($i > 0){
				if (trim($con[0]) == '' && trim($con[1]) == '' ){
                    if($i == 1){
                       echo '上传文件数据不能为空';exit;
                    }
				}else{
					$goods_id = strtoupper(trim($con[0])); //流水号
					$price = strtoupper(trim($con[1])); //应付金额

					if(empty($goods_id) || empty($price)){
						echo '流水号和应付金额为必填项';exit;
					}

					$z = "/^(\d+)(\.\d+)?$/";
					if(!preg_match("/^\d*$/",$goods_id))
					{
						echo '第'.($i+1).'行流水号格式不对，流水号只能为数字';exit;
					}
					if(!preg_match($z,$price)){
						echo '第'.($i+1).'行应付金额只能为数字并且是正数。';exit;
					}

					$gRow = $goodsModel->getRow($goods_id);

					//检查是否有此流水号
					if(!count($gRow))
					{
						echo '请检查第'.($i+1).'行数据，没有流水号：'.$goods_id;exit;
					}

					//判断上传流水号的供货商是否和表单的供货商相同
					if($gRow['prc_id'] != $prc_id)
					{
						echo '流水号'.$goods_id.'供货商和所选供货商不同，不能申请。';exit;
					}


					//判断上传流水号的类型是否和表单的应付类型相同
					if($gRow['type'] != $type)
					{
						echo '流水号'.$goods_id.'入库方式非'.$stora_mode[$type];exit;
					}

					//如果单据类型是代销借入，货品的入库方式必须是代销和借入
					if($type == 1 && $gRow['storage_mode'] != 3 && $gRow['storage_mode'] != 4)
					{
						echo '流水号'.$goods_id.'数据错误，入库方式非'.$stora_mode[$type];exit;
					}
					//如果单据类型是成品采购，货品的入库方式必须是购买和加工
					if($type == 2 && $gRow['storage_mode'] != 1 && $gRow['storage_mode'] != 2)
					{
						echo '流水号'.$goods_id.'数据错误，入库方式非'.$stora_mode[$type];exit;
					}

					if($type == 1 && (!$this->check_goods_status($gRow['goods_status'])))
					{
						echo '流水号'.$goods_id.'：货品状态为'.$dict->getEnum('warehouse.goods_status',$gRow['goods_status']).'，不属于申请范围。';exit;
					}

					//判断此项的应付申请状态是否为未申请(添加状态下)
					if($gRow['pay_apply_status'] != 1 && empty($pay_apply_number))
					{
						echo '流水号'.$goods_id.'已在单据'.$gRow['pay_apply_number'].'中提交过应付申请';exit;
					}
					//判断此项的应付申请状态是否为未申请(修改状态下)
					if($gRow['pay_apply_status'] != 1 && (!empty($pay_apply_number) && $gRow['pay_apply_number'] != $pay_apply_number))
					{
						echo '流水号'.$goods_id.'已在单据'.$gRow['pay_apply_number'].'中提交过应付申请';exit;
					}

					$val['serial_number'] = $gRow['serial_number'];//流水号
					$val['goods_id'] = $gRow['item_id'];//货号/单号
					$val['total_cope'] = $price;//应付金额
					$val['total']			= $gRow['total'];//系统金额

					//如果商品是成品，并且单据类型是退货返厂单或者是其他出库单。金额为负数
					if($type == 2 && ($gRow['item_type'] == 2 || $gRow['item_type'] == 3))
					{
						$val['total_cope'] = '-'.$price;//应付金额
						$val['total']			= '-'.$gRow['total'];//系统金额
					}
					//如果商品是石包，并且单据类型是退石单。金额为负数
					if($type == 3 && $gRow['item_type'] == 2)
					{
						$val['total_cope'] = '-'.$price;//应付金额
						$val['total']			= '-'.$gRow['total'];//系统金额
					}
					//	偏差＝应付金额－系统金额
					$val['total_dev'] = $val['total_cope'] - $val['total'];
					$apply_data['data'][] = $val;

					$total_cope += $val['total_cope'];
					$total_dev += $val['total_dev'];

					$d[] = $val['serial_number'];
				}
			}
			$i++;
		}
		$unique_arr = array_unique ( $d );
		if(count($d) != count($unique_arr))
		{
			echo '上传文件中流水号有重复值，请检查后再上传。';exit;
		}
		$apply_data['amount'] = count($d);//总数量
		$apply_data['total_cope'] = $total_cope;//总的应付金额
		$apply_data['total_dev'] = $total_dev;//总的偏差金额

		return $apply_data;
	}
	//详情页
	function show($params)
	{
		$id = $params['id'];
		$model = new PayApplyModel(29);
		$row = $model->getRow($id);

		$apiStyleModel = new ApiStyleModel();
		$catList = $apiStyleModel->getCatTypeInfo();
        if($catList){
            foreach($catList[0] as $k=>$v){
                $catLists[$v['id']]=$v['name'];
            }
        }else{
            $catLists=array();
        }

		if($row['record_type'] == '1')//申请单
		{
			$html = "pay_apply_show.html";
		}else{//调整单

			$html = "pay_apply_tz_show.html";
		}
		$this->render($html,array(
				'info' => $row,
				'catLists' => $catLists,
				'show' => 1
			));
	}


	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = $params['id'];
		$model = new PayApplyModel(29);
		$row = $model->getRow($id,'*');

		$proList = $this->getProcessList();

		if($row['record_type'] == '2')//调整单
		{
            $apiStyleModel = new ApiStyleModel();
            $catList = $apiStyleModel->getCatTypeInfo();
            if($catList){
                $catList=$catList[0];
            }else{
                $catList=array();
            }
			$this->render("pay_apply_tz_edit_info.html",array(
				'info' => $row,
				'proList' => $proList,
				'catList' => $catList,
				'bar'=>Auth::getViewBar(),
			));
		}else{//申请单

			$this->render("pay_apply_edit_info.html",array(
				'info' => $row,
				'dd' => new DictView(new DictModel(1)),
				'bar'=>Auth::getViewBar(),
				'proList' => $proList,
				'apply_status' => $row['status'],
				'show' => 0 //编辑页
			));
		}

	}

	public function downData($params)
	{
		$id = $params['id'];
		$model = new PayApplyGoodsModel(29);
		$data = $model->getDataOfApplyId($id);
		$dict = new DictModel(1);

		$title = array('流水号','货号/单号','系统金额','应付金额','偏差值','偏差说明','驳回原因');
		if (is_array($data))
		{
			foreach($data as $k=>$v)
			{
				$v['dev_direction'] = $dict->getEnum('pay_apply.dev_direction',$v['dev_direction']);
				if ($v['overrule_reason'] == '')
				{
					$val = array($v['serial_number'],$v['goods_id'],$v['total'],$v['total_cope'],$v['total_dev'],$v['dev_direction']);
					$val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
					$content[] = $val;
					$title = array('流水号','货号/单号','系统金额','应付金额','偏差值','偏差说明');
				}
				else
				{
					$val = array($v['serial_number'],$v['goods_id'],$v['total'],$v['total_cope'],$v['total_dev'],$v['dev_direction'],$v['overrule_reason']);
					$val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
					$content[] = $val;
				}
			 }
		}
		$name = date("Ymd_His");
		$this->detail_csv('申请明细列表'.$name,$title,$content);
	}

	//转换编码格式，导出csv数据
	public function detail_csv($name,$title,$content)
	{
		$ymd = date("Ymd_His", time()+8*60*60);
		header("Content-Disposition: attachment;filename=".iconv('utf-8','gbk',$name).".csv");
		$fp = fopen('php://output', 'w');
		$title = eval('return '.iconv('utf-8','gbk',var_export($title,true).';')) ;
		fputcsv($fp, $title);
	   foreach($content as $k=>$v)
	   {
			fputcsv($fp, $v);
	   }
		fclose($fp);exit;
	}

	public function showDetaillist($params)
	{
		$id = intval($params["id"]);
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'id'	=>$id
		);

		$g_model = new PayApplyGoodsModel(29);
		$where = array('apply_id'=>$id);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$data = $g_model->pageList($where,$page,5,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'pay_apply_show_list_page';

		$html = "pay_apply_info_show_list";
		/*if($show)
		{
			$html = "pay_apply_info_show_list";
		}else{
			$html = "pay_apply_edit_info_list";
		}*/
		$this->render($html.'.html',array(
			'pa' =>Util::page($pageData),
			'data' => $data,
			'apply_status' => $params['apply_status'],
			'show' => $params['show']
		));
	}

	//检查是否为可以申请的货品状态
	function check_goods_status($status)
	{
			//允许申请的货品状态 2=库存，3=已销售，4=盘点中，5=调拨中， 6=损益中,7=已报损，10=销售中,1=收货中（浩鹏系统在p单签收前；L单审核前不会到这来来）
			$goods_status_arr = array('1','2','3','4','5','6','7','10');
			return in_array($status,$goods_status_arr);
	}

	function update($params)
	{
		$result = array('success' => 0,'error' => '');
		$prc = explode("|",$params['prc_id']);
		$prc_id = $prc[0];
		$prc_name = $prc[1];
		$type = $params['type'];
		$apply_id = $params['apply_id'];
		$fapiao = $params['fapiao'];

		$ApplyModel = new PayApplyModel(29);
		$row = $ApplyModel->getRow($apply_id,"*");

		if($row['status'] != 1 && $row['status'] != 3)
		{
			$result['error'] = "不是已保存或者驳回状态不能修改";
			Util::jsonExit($result);
		}

		if($row['make_name'] != $_SESSION['userName'])
		{
			$result['error'] = "只能修改自己制的单。";
			Util::jsonExit($result);
		}
		if($prc_id=='')
		{
			$result['error'] = "请选择结算商";
			Util::jsonExit($result);
		}
		if($type=='')
		{
			$result['error'] = "请选择应付类型";
			Util::jsonExit($result);
		}


		$ApplyModel->update(array('fapiao'=>$fapiao),array('apply_id'=>$apply_id));
		$applyGoods = new PayApplyGoodsModel(29);

		if(empty($_FILES['data']['name']))//没有上传文件
		{
			//判断结算商是否相符，在没有上传文件修改的情况下，结算商和应付类型是不可以改变的。
			if($row['prc_id'] != $prc_id)
			{
				$result['error'] = "所选结算商和商品结算商不符，请重新选择。";
				Util::jsonExit($result);
			}
			if($row['pay_type'] != $type)
			{
				$result['error'] = "所选应付类型和商品不符，请重新选择。";
				Util::jsonExit($result);
			}


			if(isset($_POST['ids'])){//是否有偏差说明，如果有就修改
				$ids = $_POST['ids'];
				foreach($ids as $k => $v)
				{
					$number = "direc_".$v;
					$direct = trim($_POST[$number]);
					if(empty($direct))
					{
						$result['error'] = "偏差说明不能为空";
						Util::jsonExit($result);
					}
					$applyGoods->update(array('dev_direction'=>$direct),array('id'=>$v));
				}
			}
		}else{//修改有上传文件
			$data = $this->checkData($_FILES['data'],$type,$prc_id,$row['pay_apply_number']);//检查上传文件内容

			$applydata = array(
			'apply_id'		=> $apply_id,
			'pay_apply_number' => $row['pay_apply_number'],
			'prc_id'		=> $prc_id,
			'prc_name'		=> $prc_name,
			'pay_type'		=> $type,
			'amount'		=> $data['amount'],
			'total_cope'	=> $data['total_cope'],
			'total_dev'		=> $data['total_dev'],
			'fapiao'		=> $fapiao
			);
			$res = $ApplyModel->saveDatas($applydata,$data['data']);
		}
		if($row['status'] == '3')//已驳回状态再修改需要变回新增。相对应goods表中的状态为待审核。
		{
			$ApplyModel->update(array('status'=>1),array('apply_id'=>$apply_id));
			//对应的商品记录单子的状态
			$goods = new GoodsModel(29);
			$value = array('pay_apply_status'=>'2');
			$where = array('pay_apply_number'=>$row['pay_apply_number']);
			$goods->update($value,$where);
		}
		//$this->apply_log_add($apply_id,'修改申请单');
		$result['success'] = 1;
		Util::jsonExit($result);
	}

	function updateTz($params)
	{
		$prc = explode("|",$params['prc_id']);
		$prc_id = $prc[0];
		$prc_name = $prc[1];

		$type = $params['type'];
		$total_cope = $params['total_cope'];
		$adj_reason = $params['adj_reason'];
		$apply_id = $params['id'];
		$cat_type = $params['cat_type'];

		$ApplyModel = new PayApplyModel(29);
		$row = $ApplyModel->getRow($apply_id,"*");

		if($row['status'] != 1 && $row['status'] != 3)
		{
			$result['error'] = "不是已保存或驳回状态不能修改";
			Util::jsonExit($result);
		}

		if($row['make_name'] != $_SESSION['userName'])
		{
			$result['error'] = "只能修改自己制的单。";
			Util::jsonExit($result);
		}

		if($prc_id=='')
		{
			$result['content'] = "请选择结算商";
			Util::jsonExit($result);
		}
		if($type=='')
		{
			$result['content'] = "请选择应付类型";
			Util::jsonExit($result);
		}
		if($total_cope=='')
		{
			$result['content'] = "应付金额不能为空";
			Util::jsonExit($result);
		}
		if($adj_reason=='')
		{
			$result['content'] = "调整原因不能为空";
			Util::jsonExit($result);
		}
		if($cat_type=='')
		{
			$result['content'] = "款式分类不能为空";
			Util::jsonExit($result);
		}


		$applydata = array(
			'apply_id'		=> $apply_id,
			'prc_id'		=> $prc_id,
			'prc_name'		=> $prc_name,
			'pay_type'		=> $type,
			'total_cope'	=> $total_cope,
			'adj_reason'	=> $adj_reason,
			'style_type'	=> $cat_type
		);
		$res = $ApplyModel->savaAdjData($applydata);

		if($res['result']){
			$result['success'] = 1;
		}
		Util::jsonExit($result);
	}

	//提交申请单
	function subCon($params)
	{
		$result = array('success' => 0,'error' =>'');
		$apply_id = $params['id'];

		$applyModel = new PayApplyModel(29);
		$row = $applyModel->getRow($apply_id,"*");

		//$row['make_name']='admin';

		//$_SESSION['userName']='杨福友';

		if($row['make_name'] != $_SESSION['userName'])
		{
			$result['error'] = '只能提交自己制的单。';
			Util::jsonExit($result);
		}

		if($row['status'] != 1 && $row['status'] != 3)
		{
			$result['error'] = '只有已保存或者驳回状态才能提交';
			Util::jsonExit($result);
		}


		$applyGoods = new PayApplyGoodsModel(29);
		$goods = $applyGoods->getDataOfApplyId($apply_id);
		foreach($goods as $k => $v)
		{
			if($v['total_dev'] != 0 && $v['dev_direction'] == '')
			{
				$result['error'] = '偏差金额不等于0的情况下，偏差说明必填。请检查您所填数据是否保存。';
				Util::jsonExit($result);
			}
		}
		//已驳回状态下再提交，单据和明细的驳回原因都要清空
		$applyModel->update(array('overrule_reason'=>''),array('apply_id'=>$apply_id));
		$applyGoods->update(array('overrule_reason'=>''),array('apply_id'=>$apply_id));

		//对应的商品记录单子的状态
		$goods = new GoodsModel(29);
		$value = array('pay_apply_status'=>'2');
		$where = array('pay_apply_number'=>$row['pay_apply_number']);

		$applyRet = $applyModel->update(array('status'=>2),array('apply_id'=>$apply_id));
		$goodsRet = $goods->update($value,$where);

		if(!($applyRet && $goodsRet))
		{
			$result['error'] = '提交失败，请重新提交。';
		}else{
			$result['success'] = 1;
			//$this->apply_log_add($apply_id,'提交申请单');
		}

		Util::jsonExit($result);
	}

	//取消申请单
	function delCon($params)
	{
		$result = array('success' => 0,'error' =>'');

		$apply_id = $params['id'];
		$applyModel = new PayApplyModel(29);
		$row = $applyModel->getRow($apply_id,"*");

		if($row['make_name'] != $_SESSION['userName'])
		{

			$result['error'] = '只能取消自己制的单';
			Util::jsonExit($result);
		}


		if($row['status'] != 1 && $row['status'] != 3)
		{
			$result['error'] = '只有已保存和驳回状态才能取消';
			Util::jsonExit($result);
		}

		//对应的商品记录单子的状态
		$goods = new GoodsModel(29);
		$value = array('pay_apply_status'=>'1','pay_apply_number'=>'');
		$where = array('pay_apply_number'=>$row['pay_apply_number']);

		$applyRet = $applyModel->update(array('status'=>4),array('apply_id'=>$apply_id));
		$goodsRet = $goods->update($value,$where);

		if(!($applyRet && $goodsRet))
		{
			$result['error'] = '取消失败，请重新操作。';
		}else{
			$result['success'] = 1;
			//$this->apply_log_add($apply_id,'取消申请单');
		}
		Util::jsonExit($result);
	}

	//审核申请单
	function checkCon($params)
	{
		//不能审核自己制的单
		$result = array('success' => 0,'error' =>'');

		$apply_id = $params['id'];
		$applyModel = new PayApplyModel(29);
		$applyrow = $applyModel->getRow($apply_id,"*");

		if($applyrow['make_name'] == $_SESSION['userName'])
		{
			$result['error'] = '不能审核自己制的单';
			Util::jsonExit($result);
		}

		if($applyrow['status'] != 2)
		{
			$result['error'] = '只有待审核状态才能审核';
			Util::jsonExit($result);
		}

		$goods = new GoodsModel(29);
		$applyGoods = new PayApplyGoodsModel(29);
		
		//审核时，如果是代销借货的申请单，则要再次核对商品状态
		if($applyrow['pay_type'] == 1)
		{
			$dict = new DictModel(1);
			$apply_goods = $applyGoods->getDataOfApplyId($apply_id);
			foreach($apply_goods as $key => $val)
			{
				$row = $goods->getRow($val['serial_number']);
				if(!count($row))
				{
					$result['error'] = "流水号".$val['serial_number']."：原始数据不存在，不能审核";
					Util::jsonExit($result);
				}
				if(!$this->check_goods_status($row['goods_status']))
				{
					$result['error'] = "流水号".$val['serial_number']."：货品状态为".$dict->getEnum('warehouse.goods_status',$row['goods_status'])."，不属于申请范围。";
					Util::jsonExit($result);
				}
				//修改对应的货号的结价状态变为己结价状态。
				$warehouseModel = new ApiWarehouseModel();
				$warehouseModel->UpdateJiejiaByGoodsId($val['goods_id']);
		
			}
		}else if($applyrow['pay_type'] == 2){	
			//如果单据类型是成品采购单 则根据单号获取货号 修改对应的货号的结价状态变为己结价状态。
			$apply_goods = $applyGoods->getDataOfApplyId($apply_id);
			$goods_ids = array();
			foreach($apply_goods as $key => $val)
			{
				$warehouseModel = new ApiWarehouseModel();			
				$ret = $warehouseModel->getWarehouseBillGoods(array('bill_no' => $val['goods_id'], 'bill_type' => 'L')); //取的单只是L单（收货单）不包括B单（退货返厂单）
				if (!$ret['error']){
					foreach($ret['data'] as $val){
						//修改对应的货号的结价状态变为己结价状态。
						$goods_ids[] = $val['goods_id'];
					}
				}
			}
			$warehouseModel->UpdateJiejiaByGoodsId($goods_ids);
		}
		
		//对应的商品记录单子的状态
		$value = array('pay_apply_status'=>'4');
		$where = array('pay_apply_number'=>$applyrow['pay_apply_number']);
		$goodsRet = $goods->update($value,$where);

		//审核通过，可生成应付单，记录审核人和审核时间
		$value = array('status'=>'5','check_name'=>$_SESSION['userName'],'check_time'=>date('Y-m-d H:i:s'));
		$where = array('apply_id'=>$applyrow['apply_id']);
		$applyRet  = $applyModel->update($value,$where);

		if(!($applyRet && $goodsRet))
		{
			$result['error'] = '审核失败，请重新提交。';
		}else{
			$result['success'] = 1;
			//$this->apply_log_add($apply_id,'审核申请单');
		}
		Util::jsonExit($result);
	}

		//驳回
	function reCon($params)
	{
		$result = array('success' => 0,'error' =>'');

		$apply_id	= $params['apply_id'];
		$ids		= $params['ids'];
		$reasons	= $params['reasons'];

		$applyModel = new PayApplyModel(29);
		$applyrow = $applyModel->getRow($apply_id,"*");

		if($applyrow['make_name'] == $_SESSION['userName'])
		{
			$result['error'] = '不能驳回自己制的单';
			Util::jsonExit($result);
		}

		if($applyrow['status'] != 2)
		{
			$result['error'] = '只有待审核状态才能驳回';
			Util::jsonExit($result);
		}

		if(empty($ids))
		{
			$result['error'] = '驳回原因至少填写一项';
			Util::jsonExit($result);
		}

		//记录驳回原因
		$ids = explode(',',$ids);
		$reasons = explode('#',$reasons);
		$applygoods = new PayApplyGoodsModel(29);
		foreach($ids as $k => $v)
		{
			$applygoods->update(array('overrule_reason'=>$reasons[$k]),array('serial_number'=>$v,'apply_id'=>$apply_id));
		}

		//对应的商品记录单子的状态
		$goods = new GoodsModel(29);
		$value = array('pay_apply_status'=>'3');
		$where = array('pay_apply_number'=>$applyrow['pay_apply_number']);
		$goodsRet = $goods->update($value,$where);

		//驳回，记录操作人和时间
		$value = array('status'=>'3','check_name'=>$_SESSION['userName'],'check_time'=>date('Y-m-d H:i:s',time()));
		$where = array('apply_id'=>$apply_id);
		$applyRet = $applyModel->update($value,$where);

		if(!($applyRet && $goodsRet))
		{
			$result['error'] = '驳回失败，请重新提交。';
		}else{
			$result['success'] = 1;
			//$this->apply_log_add($apply_id,'驳回申请单');
		}
		Util::jsonExit($result);
	}

	//调整单审核、驳回
	function AdjCheckCon($params)
	{
		$result = array('success' => 0,'error' =>'');
		$apply_id	= $params['id'];
		$type		= isset($params['type'])?$params['type']:'';

		$applyModel = new PayApplyModel(29);
		$applyrow = $applyModel->getRow($apply_id,"*");

		if($applyrow['make_name'] == $_SESSION['userName'])
		{
			$result['error'] = '不能操作自己制的单';
			Util::jsonExit($result);
		}

		if($applyrow['status'] != 2)
		{
			$result['error'] = '只有待审核状态才能操作';
			Util::jsonExit($result);
		}

		$value['check_name'] = $_SESSION['userName'];
		$value['check_time'] = date('Y-m-d H:i:s');

		//记录审核人和审核时间
		if($type == 'overrule')//驳回
		{
			$reason = $params['reason'];
			$value['status'] = 3;
			$value['overrule_reason'] = $reason;
		}else{//审核
			$value['status'] = 5;
		}
		if($applyModel->update($value,array('apply_id'=>$apply_id)))
		{
			$result['success'] = 1;
		}

		Util::jsonExit($result);
	}

	public function downloadDemo()
	{
		$title = array('流水号','付款金额');
		$this->detail_csv('应付申请单V1.0',$title,array());
	}
	
	//导出
	private function download($data) {
	    if ($data['data']) {
	        $down = $data['data'];
	        $dd =new DictModel(1);
	        $tmp = $dd->getEnumArray('app_pay_should.pay_type');
	        $payTypeList = array();
	        foreach ($tmp as $key=>$val){
	            $payTypeList[$val['name']] = $val['label'];
	        }
	        $statusListTmp = $dd->getEnumArray('app_pay_apply.status');
	        $statusList = array();
	        foreach ($statusListTmp as $key=>$val){
	            $statusList[$val['name']] = $val['label'];
	        }
	     	//$xls_content = "应付申请单号,发票号,应付类型,应付金额,结算商,款式分类,制单时间,制单人,审核时间,审核人,申请状态,应付单号\r\n";
	     	$xls_content = "应付申请单号,发票号,应付类型,应付金额,结算商,制单时间,制单人,审核时间,审核人,申请状态,应付单号\r\n";
	     	foreach ($down as $key => $val) {
	     	    $xls_content .= $val['pay_apply_number']. ",";
	            $xls_content .= $val['fapiao']. ",";
	            $xls_content .= $payTypeList[$val['pay_type']]. ",";
	            $xls_content .= $val['total_cope']. ",";
	            $xls_content .= $val['prc_name']. ",";
	            //$xls_content .= $val['cat_type_name']. ",";
	            $xls_content .= $val['make_time']. ",";
	            $xls_content .= $val['make_name']. ",";
	            $xls_content .= $val['check_time']. ",";
	            $xls_content .= $val['check_name']. ",";
	            $xls_content .= $statusList[$val['status']]. ",";
	            $xls_content .= $val['pay_number'] . "\n";
	     	}
	    } else {
	        $xls_content = '没有数据！';
	    }
	    header("Content-type:text/csv;charset=gbk");
	    header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "导出" . date("Y-m-d")) . ".csv");
	    header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
	    header('Expires:0');
	    header('Pragma:public');
	    echo iconv("utf-8", "gbk//IGNORE", $xls_content);
	    exit;
	}
}

?>