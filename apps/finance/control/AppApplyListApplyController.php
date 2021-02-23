<?php
/**
 *  -------------------------------------------------
 *   @file		: AppApplyListApplyController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 19:03:33
 *   @update	:
 *  -------------------------------------------------
 */
class AppApplyListApplyController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('themes','show');

    // 申请单处理状态
    public static $applyOrderDoStatus = array(
        "1" => "新增",
        "2" => "待审核",
        "3" => "已驳回",
        "4" => "已取消",
        "5" => "待生成应付单",
        "6" => "已生成应付单"
    );
    //应付类型
    public static $payType = array(
        1=>'代销借货',
        2=>'成品采购',
        3=>'石包采购',
    );
    //应付类型
    public static $company = array(
           58=>'总公司'
    );

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_apply_list_apply_search_form.html',array('bar'=>Auth::getBar(),
            'view'=>new AppApplyListApplyView(new AppApplyListApplyModel(29)),
            'applyOrderDoStatus'=>self::$applyOrderDoStatus,
            'payType'=>self::$payType,
            'company'=>self::$company,
        ));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");
            'company' => _Request::getInt("company"),
            'status' => _Request::getInt("status"),
            'prc_id' => _Request::getInt("prc_id"),
            'payType' => _Request::getInt("payType"),
            'pay_apply_number' => _Request::getString('pay_apply_number'),
            'pay_number' => _Request::getString('pay_number'),
            'start_make_date' => _Request::getString('start_make_date'),
            'end_make_date' => _Request::getString('end_make_date'),
            'start_check_date' => _Request::getString('start_check_date'),
            'end_check_date' => _Request::getString('end_check_date'),
		);
		$page = _Request::getInt("page",1);
		$where = array(
            'company' => _Request::getInt("company"),
            'status' => _Request::getInt("status"),
            'prc_id' => _Request::getInt("prc_id"),
            'payType' => _Request::getInt("payType"),
            'pay_apply_number' => _Request::getString('pay_apply_number'),
            'pay_number' => _Request::getString('pay_number'),
            'start_make_date' => _Request::getString('start_make_date'),
            'end_make_date' => _Request::getString('end_make_date'),
            'start_check_date' => _Request::getString('start_check_date'),
            'end_check_date' => _Request::getString('end_check_date'),            
        );

		$model = new AppApplyListApplyModel(29);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_apply_list_apply_search_page';
		$this->render('app_apply_list_apply_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
            'applyorderdostatus'=>self::$applyOrderDoStatus,
            'payType'=>self::$payType
		));
	}

	/**
	 *	GoodsSearch，商品列表
	 */
	public function GoodsSearch ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");
            'id' => _Request::getInt("_id"),
		);
		$page = _Request::getInt("page",1);
		$where = array(
            'apply_id' => $args["id"], 
        );

		$model = new AppApplyListApplyModel($where['apply_id'],29);
        $olddo = $model->getDataObject();
		$data = $model->getDataOfApplyId($where,$page,10,false);
		
        $pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_apply_list_apply_search_page';
		$this->render('app_apply_list_apply_search_show_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'olddo'=>$olddo,
            'applyorderdostatus'=>self::$applyOrderDoStatus,
            'payType'=>self::$payType
		));
	}

	/*------------------------------------------------------ */
	//-- 显示修改应付申请单页面
	//-- by Zlj
	/*------------------------------------------------------ */
	public function editApply()
	{
		$id = _Request::get('id');
		if (_Request::get('down'))
		{
			$view = new AppApplyListApplyView(new AppApplyListApplyModel($id,29));
			$data = $view->get_Data();

			$title = array('流水号','货号/单号','系统金额','应付金额','偏差值','偏差说明','驳回原因');
			if (is_array($data))
			{
				foreach($data as $k=>$v)
				{
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

		$this->render("app_apply_list_apply_show_list.html",array(
				'is_full_page'=>isset($_REQUEST['is_full_page'])? 0 : 1,
				'view'=>new AppApplyListApplyView(new AppApplyListApplyModel($id,29)),
				'edit' => 1,
                'dd'=>new DictView(new DictModel(1)),
                'payType'=>self::$payType
			));
	}

    //保存
	public function editApplySub()
	{

		$prc_id = _Post::getInt('prc_id');
		$type = _Post::get('type');
		$apply_id = _Post::get('apply_id');
		$fapiao=_Request::getString('fapiao');
        $id=explode(',',_Request::getString('id'));
        $name=explode(',',_Request::getString('name'));
        $ids=array();
        foreach($id as $k=>$v){
            $ids[$v]=$name[$k];
        }

		$ApplyModel = new AppApplyListApplyModel($apply_id,29);
		if($ApplyModel->getValue('make_name') != $_SESSION['userName'])
		{
			$result['error'] = "只能修改自己制的单。";
		    Util::jsonExit($result);
		}
		$ApplyModel->update(array('fapiao'=>$fapiao),array('apply_id'=>$apply_id));
		$applyGoods = new AppApplyListApplyModel(30);

		if(empty($_FILES['data']['name']))//没有上传文件
		{
			//判断结算商是否相符，在没有上传文件修改的情况下，结算商和应付类型是不可以改变的。
			if($ApplyModel ->getValue('prc_id') != $prc_id)
			{
                $result['error'] = "所选结算商和商品结算商不符，请重新选择。";
				Util::jsonExit($result);
			}
			if($ApplyModel ->getValue('pay_type') != $type)
			{
                $result['error'] = "所选应付类型和商品不符，请重新选择。";
				Util::jsonExit($result);
			}

			if(!empty($ids)){//是否有偏差说明，如果有就修改
				foreach($ids as $k => $v)
				{
					$number = "direc_".$k;
					$direct = trim($number);
					if(empty($direct))
					{
                        $result['error'] = "偏差说明不能为空";
                        Util::jsonExit($result);
					}
                    
					$applyGoods->updategoods(array('dev_direction'=>$v),array('id'=>$k));
				}
			}
		}else{//修改有上传文件
			$data = $this->checkData($_FILES['data'],$type,$prc_id,$ApplyModel ->getValue('pay_apply_number'));//检查上传文件内容
            
			$Jpro = $ApplyModel->getNameList($prc_id);
			$applydata = array(
			'apply_id'		=> $apply_id,
			'prc_id'			=> $prc_id,
			'prc_name'		=> $Jpro[0]['p_name'],
			'pay_type'		=> $type,
			'amount'		=> $data['amount'],
			'total_cope'	=> $data['total_cope'],
			'total_dev'		=> $data['total_dev'],
			'fapiao'		=> $fapiao
			);
            $res = $ApplyModel->saveData($applydata,$data['data']);
		}
		if($ApplyModel->getValue('status') == '2')//已驳回状态再修改需要变回新增。相对应goods表中的状态为待审核。
		{
			$ApplyModel->update(array('status'=>0),array('apply_id'=>$apply_id));
			//对应的商品记录单子的状态
			$value = array('pay_apply_status'=>'2');
			$where = array('pay_apply_number'=>$ApplyModel->getValue('pay_apply_number'));
			$ApplyModel->updategoods($value,$where);
		}
		//$this->apply_log_add($apply_id,'修改申请单');
        $result['success'] = 1;
        Util::jsonExit($result);
	}

//提交申请单
	function subCon()
	{
			$result = array('success' => 0,'error' =>'');
			$apply_id = _Post::get('apply_id');
			$applyModel = new AppApplyListApplyModel($apply_id,30);

			if($applyModel->getValue('make_name') != $_SESSION['userName'])
			{
                $result['error'] = "只能提交自己制的单。";
                Util::jsonExit($result);
			}
			$applyGoods = new AppApplyListApplyModel(30);
			$goods = $applyGoods->getDataOfApplyapply_id($apply_id);
			foreach($goods as $k => $v)
			{
				if($v['total_dev'] != 0 && $v['dev_direction'] == '')
				{
					$result['error'] = '偏差金额不等于0的情况下，偏差说明必填。请检查您所填数据是否保存。';
					Util::jsonExit($result);
				}
			}
            
			//已驳回状态下再提交，驳回原因要清空
			$applyGoods->updategoods(array('overrule_reason'=>''),array('apply_id'=>$apply_id));

			//对应的商品记录单子的状态
			$goods = new AppApplyListApplyModel($apply_id,30);
			$value = array('pay_apply_status'=>'2');
			$where = array('pay_apply_number'=>$applyModel->getValue('pay_apply_number'));

			$applyModel->setValue('status','1');
			if(!($applyModel->save(true) && $goods->update_goods($value,$where)))
			{
				$result['error'] = '提交失败，请重新提交。';
			}else{
				$result['success'] = 1;
				//$this->apply_log_add($apply_id,'提交申请单');
			}

			Util::jsonExit($result);
	}

//取消申请单
	function delCon()
	{
			$result = array('success' => 0,'error' =>'');
			$apply_id = _Post::get('apply_id');
			$applyModel = new AppApplyListApplyModel($apply_id,30);

			if($applyModel->getValue('make_name') != $_SESSION['userName'])
			{
				$result['error'] = '只能取消自己制的单。';
				Util::jsonExit($result);
			}
			//对应的商品记录单子的状态
			$goods = new AppApplyListApplyModel(30);
			$value = array('pay_apply_status'=>'1','pay_apply_number'=>'');
			$where = array('pay_apply_number'=>$applyModel->getValue('pay_apply_number'));

			$applyModel->setValue('status','3');
			if(!($applyModel->save(true) && $goods->update_goods($value,$where)))
			{
				$result['error'] = '取消失败，请重新操作。';
			}else{
				$result['success'] = 1;
				//$this->apply_log_add($apply_id,'取消申请单');
			}
			Util::jsonExit($result);
	}

	/**
	 *	add，渲染添加页面 应付申请单
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_apply_list_apply_info.html',array(
			'view'=>new AppApplyListApplyView(new AppApplyListApplyModel(29))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$tab_id = intval($params["tab_id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_apply_list_apply_info.html',array(
			'view'=>new AppApplyListApplyView(new AppApplyListApplyModel($id,29)),
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
        $where['apply_id']=$id;
        $applyGoods = new AppApplyListApplyModel(29);
        $applyGoodsData = $applyGoods->getDataOfApplyId($where,$page=1,$pageSize=100000000);

        if(isset($_REQUEST['down'])){
            $title = array('流水号','货号/单号','系统金额','应付金额','偏差值','偏差说明');
            $content=array();
            foreach($applyGoodsData['data'] as $k => $v){
                $val = array($v['serial_number'],$v['goods_id'],$v['total'],$v['total_cope'],$v['total_dev'],$v['dev_direction'],$v['overrule_reason']);
                $content[] = $val;
            }
            Util::downloadCsv("数据格式.csv",$title,$content);
            exit;
        }
    
        $this->render('app_apply_list_apply_show.html',array(
			'view'=>new AppApplyListApplyView(new AppApplyListApplyModel($id,29)),
            'applyGoodsData'=>$applyGoodsData,
			'bar'=>Auth::getViewBar()
		));
	}

    public function themes ()
    {
        $title = array('流水号','应付金额');
        Util::downloadCsv("数据格式.csv",$title,false);
        exit;
    }

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$prc_id = _Post::getInt('prc_id');
		$type = _Post::getInt('type');
		$total_cope = _Post::getInt('total_cope');
		$adj_reason = _Post::getString('adj_reason');
		$Jpro = new AppApplyListApplyModel(29);
		$pro_arr = $Jpro->getNameList($prc_id);
        $prc_name =$pro_arr[0]['p_name'];
        $olddo = array();
		$newdo = array(
			'pay_apply_number' => 'T-YFSQ',
			'make_time'	=> date('Y-m-d H:i:s',time()),
			'make_name'	=> $_SESSION['userName'],
			'check_time'	=> '0000-00-00 00:00:00',
			'check_name'	=> '',
			'company'		=> '58',
			'prc_id'			=> $prc_id,
			'prc_name'		=> $pro_arr[0]['p_name'],
			'pay_type'		=> $type,
			'amount'		=> 0,
			'total_cope'	=> $total_cope,
			'total_dev'		=> 0,
			'adj_reason'	=> $adj_reason,
			'record_type'=>'2'
		);
		$model = new AppApplyListApplyModel(30);
		$res = $model->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			//$this->apply_log_add($res['apply_id'],'生成申请单'); 记录日志
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

		$newmodel =  new AppApplyListApplyModel($id,30);

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
		$model = new AppApplyListApplyModel($id,30);
		$do = $model->getDataObject();
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
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
	
		if($file_extension != 'csv'){
		   Util::jsonExit('请上传CSV格式的文件');
		}
		$f = fopen($file['tmp_name'],"r");
		$goodsModel = new GoodsModel(30);
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
                       Util::jsonExit('上传文件数据不能为空');
                    }
				}else{
					$goods_id = strtoupper(trim($con[0])); //流水号
					$price = strtoupper(trim($con[1])); //应付金额

					if(empty($goods_id) || empty($price)){
						Util::jsonExit('流水号和应付金额为必填项');
					}
					
					$z = "/^(\d+)(\.\d+)?$/";
					if(!preg_match("/^\d*$/",$goods_id))   
					{
						Util::jsonExit('第'.($i+1).'行流水号格式不对，流水号只能为数字');
					}
					if(!preg_match($z,$price)){
						Util::jsonExit('第'.($i+1).'行应付金额只能为数字并且是正数。');
					}

					$gRow = $goodsModel->getRow($goods_id);

					//检查是否有此流水号
					if(!count($gRow))
					{
						Util::jsonExit('请检查第'.($i+1).'行数据，没有流水号：'.$goods_id);
					}
					
					//判断上传流水号的供货商是否和表单的供货商相同
					if($gRow['prc_id'] != $prc_id)
					{
						//Util::jsonExit('流水号'.$goods_id.'供货商和所选供货商不同，不能申请。');
					}
					
					if($type == 1 && (!$this->check_goods_status($gRow['goods_status'])))
					{
						//Util::jsonExit('流水号'.$goods_id.'：货品状态为'.C::$goodsStatus[$gRow['goods_status']].'，不属于申请范围。');
					}

					//判断上传流水号的类型是否和表单的应付类型相同
					if($gRow['type'] != $type)
					{
						//Util::jsonExit('流水号'.$goods_id.'入库方式非'.$stora_mode[$type]);
					}

					//判断此项的应付申请状态是否为未申请(添加状态下)
					if($gRow['pay_apply_status'] != 1 && empty($pay_apply_number))
					{
						//Util::jsonExit('流水号'.$goods_id.'已在单据'.$gRow['pay_apply_number'].'中提交过应付申请');
					}
					//判断此项的应付申请状态是否为未申请(修改状态下)
					if($gRow['pay_apply_status'] != 1 && (!empty($pay_apply_number) && $gRow['pay_apply_number'] != $pay_apply_number))
					{
						//Util::jsonExit('流水号'.$goods_id.'已在单据'.$gRow['pay_apply_number'].'中提交过应付申请');
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
			Util::jsonExit('上传文件中流水号有重复值，请检查后再上传。');
		}
		$apply_data['amount'] = count($d);//总数量
		$apply_data['total_cope'] = $total_cope;//总的应付金额
		$apply_data['total_dev'] = $total_dev;//总的偏差金额

		return $apply_data;
	}
	//检查是否为可以申请的货品状态
	function check_goods_status($status)
	{
			//允许申请的货品状态 1=库存，2=已销售，3=转仓中，4=盘点中，5=销售中, 11=损益中,12=已报损
			$goods_status_arr = array('1','2','3','4','5','11','12');
			return in_array($status,$goods_status_arr);
	}

//点击生成应付单，先检查数据的准确定和计算总金额
	public function shouldAddCheck()
	{
		$result = array('success' => 0,'error' =>'');
		$ids = _Post::get('id');
		if($this->checkShouldCon($ids))
		{
			$applyModel = new AppApplyListApplyModel(29);
			$total = $applyModel->getTotalOfIds($ids);
			$result['success'] = 1;
			$result['total']	=	$total;
		}
		Util::jsonExit($result);
	}

//生成应付单提交
	public function shouldAddSub()
	{
		$result = array('success' => 0,'error' =>'');
		$ids = _Post::get('id');

		if($this->checkShouldCon($ids))//检查数据成功
		{
			//添加数据
			$model = new AppApplyListApplyModel(29);
			$res = $model->add($ids);
			if($res['error'])
			{
				$result['error'] = '生成应付单 CWYF'.$res['id'];
				$result['success']		= 1;
				$result['id'] = $res['id'];
			}else{
				$result['error'] = '生成应付单失败';
			}
		}
		Util::jsonExit($result);
	}

/*------------------------------------------------------ */
	//-- 检查生成应付单的数据 是否同一个结算商，同一个类型，是否是“待生成应付单”状态。是否已经存在在其他应付单里面
	// 返回结果
	//-- by Zlj
	/*------------------------------------------------------ */
	function checkShouldCon($ids)
	{
		$result = array('success' => 0,'error' =>'');
		$applyModel = new AppApplyListApplyModel(29);
		if(!$applyModel->checkDistinct('prc_id',$ids))
		{
			$result['error'] = '所选单据不是同一个结算商，不能提交。';
			Util::jsonExit($result);
		}
		if(!$applyModel->checkDistinct('pay_type',$ids))
		{
			$result['error'] = '所选单据应付类型不同，不能提交。';
			Util::jsonExit($result);
		}
		$ids = explode(',',$ids);
		foreach($ids as $k => $v)
		{
			$gRow = $applyModel->getRow($v);
			if($gRow['pay_number'] != '')
			{
				$result['error'] = '申请单 '.$gRow['pay_apply_number'].' 已经存在于应付单据 '.$gRow['pay_number'].' 中，不能提交。';
				Util::jsonExit($result);
			}
			if($gRow['status'] != '4')
			{
				$result['error'] = '申请单 '.$gRow['pay_apply_number'].' 状态不对，不能提交。';
				Util::jsonExit($result);
			}
		}
		return true;
	}
}

?>