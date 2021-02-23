<?php
/**
 *  -------------------------------------------------
 *   @file		: DiaOrderController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: liyanhong <1536451016@qq.com>
 *   @date		: 2015-03-16 11:12:23
 *   @update	:
 *  -------------------------------------------------
 */
class DiaOrderController extends CommonController
{
	protected $smartyDebugEnabled	= false;
	protected $whitelist			= array('getTemplate','print_shibao','downloads');
	protected $order_type			= array(
		'MS' => '买石单',
		'SS' => '送石单',
		'HS' => '还石单',
		'TS' => '退石单',
		'YS' => '遗失单',
		'SY' => '损益单',
		'TH' => '退货单',
		'AS' => '调整单',
		'RK' => '其他入库单',
		'CK' => '其他出库单',
		'fenbaoru' => '分包入',
		'fenbaochu'=> '分包出'
		);

	/**************************************************************************************************
	 *	index，搜索框
	 **************************************************************************************************/
	public function index ($params)
	{
		//var_dump($params);exit;
		//石包管理页面连接连接需要
		$shibao = isset($params['shibao'])?$params['shibao']:'';
		$type   = isset($params['type'])?$params['type']:'';

		$this->render('dia_order_search_form.html',array(
				'bar'        => Auth::getBar(),
				'order_type' => $this->order_type,
				'pro_list'   => $this->get_pro_list(),
				'shibao'	 => $shibao,
				'type'		 => $type
			));
	}

	
	/**************************************************************************************************
	 *	search，列表
	 **************************************************************************************************/
	public function search ($params)
	{
		//var_dump($params);exit;
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");
			'order_id'			=> _Request::get('order_id'),
			'type'				=> _Request::get('type'),
			'status'			=> _Request::get('status'),
			'send_goods_sn'		=> _Request::get('send_goods_sn'),
			'shibao'			=> _Request::get('shibao'),
			'zhengshuhao'		=> _Request::get('zhengshuhao'),
			'make_order'		=> _Request::get('make_order'),
			'prc_id'			=> _Request::get('prc_id'),
			'in_warehouse_type'	=> _Request::get('in_warehouse_type'),
			'account_type'		=> _Request::get('account_type'),
			'add_time_start'	=> _Request::get('add_time_start'),
			'add_time_end'		=> _Request::get('add_time_end'),
			'check_time_start'	=> _Request::get('check_time_start'),
			'check_time_end'	=> _Request::get('check_time_end'),
			'info'				=> _Request::get('info')
		);
		//var_dump($args);exit;
		$page = _Request::getInt("page",1);
		$where = array(
			'order_id'			=> $args['order_id'],
			'type'				=> $args['type'],
			'status'			=> $args['status'],
			'send_goods_sn'		=> $args['send_goods_sn'],
			'shibao'			=> $args['shibao'],//
			'zhengshuhao'		=> $args['zhengshuhao'],//
			'make_order'		=> $args['make_order'],
			'prc_id'			=> $args['prc_id'],
			'in_warehouse_type' => $args['in_warehouse_type'],
			'account_type'		=> $args['account_type'],
			'add_time_start'	=> $args['add_time_start'],
			'add_time_end'		=> $args['add_time_end'],
			'check_time_start'	=> $args['check_time_start'],
			'check_time_end'	=> $args['check_time_end'],
			'info'				=> $args['info']
			);

		$model = new DiaOrderModel(45);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'dia_order_search_page';
		$this->render('dia_order_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**************************************************************************************************
	 *	add，渲染添加页面
	***************************************************************************************************/
	public function add ($type)
	{
		$result = array('success' => 0,'error' => '');
		$result['content']	= $this->fetch('dia_order_info.html',array(
			'view'			=>new DiaOrderView(new DiaOrderModel(45)),
			'pro_list'		=>$this->get_pro_list() ,
			'type'			=>$type
		));
		$order_type = $this->order_type;
		$result['title'] = '添加'.$order_type[$type];
		Util::jsonExit($result);
	}
	/**************************************************************************************************
	 *	add_ss，渲染添加页面----送石单
	 **************************************************************************************************/
	public function add_ss ()
	{
		$this->add("SS");
	}
	/***************************************************************************************************
	 *	add_ss，渲染添加页面----买石单
	 **************************************************************************************************/
	public function add_ms ()
	{
		$this->render('dia_order_info_ms.html',array(
			'view'=>new DiaOrderView(new DiaOrderModel(45)),
			'pro_list'=>$this->get_pro_list(),'type'=>'MS'
		));

	}

	/***************************************************************************************************
	 *	add_rk，渲染添加页面----添加其他入库单
	 **************************************************************************************************/
	public function add_rk ()
	{
		$this->add("RK");
	}
	/***************************************************************************************************
	 *	add_sy，渲染添加页面----添加损益单
	 **************************************************************************************************/
	public function add_sy ()
	{
		$this->add("SY");
	}

	/***************************************************************************************************
	 *	add_ys，渲染添加页面----添加遗失单
	 **************************************************************************************************/
	public function add_ys ()
	{
		$this->add("YS");
	}
	/***************************************************************************************************
	 *	add_ts，渲染添加页面----添加退石单
	 */
	public function add_ts ()
	{
		$this->add("TS");
	}
	/***************************************************************************************************
	 *	add_hs，渲染添加页面----添加还石单
	 */
	public function add_hs ()
	{
		$this->add("HS");
	}
	/***************************************************************************************************
	 *	add_as，渲染添加页面----添加调整单
	 */
	public function add_as ()
	{
		$this->add("AS");
	}
	/***************************************************************************************************
	 *	add_th，渲染添加页面----添加退货单
	 */
	public function add_th ()
	{
		$this->add("TH");
	}
	/***************************************************************************************************
	 *	add_ck，渲染添加页面----添加其他出库单
	 */
	public function add_ck ()
	{
		$this->add("CK");
	}
	/***************************************************************************************************
	 *	edit,编辑单据
	 */
	public function edit ($params)
	{
		$id		=  intval($params["id"]);
		$type	=  $params["type"];
		$tab_id =  _Request::getInt("tab_id");
		$model  =  new DiaOrderGoodsModel(45);
		$data   =  $model->getDetailByOrderId($id);
		$tname  =  $this->order_type;
		$this->render('dia_order_info_edit.html',array(
			'view'     => new DiaOrderView(new DiaOrderModel($id,45)),
			'pro_list' => $this->get_pro_list(),
			'tab_id'   => $tab_id,
			'data'     => $data,
			'type'	   => $type,
			'tname'	   => $tname[$type]
			));
	}
	/***************************************************************************************************
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id		=  intval($params["id"]);
		$type   =  $params["type"];
		$tname  =  $this->order_type;
		$this->render('dia_order_show.html',array(
			'view'	=>new DiaOrderView(new DiaOrderModel($id,45)),
			'bar'	=>Auth::getViewBar(),
			'type'	=>$type,
			'tname'	=> $tname[$type]
		));
	}


	/***************************************************************************************************
	 *	insert，信息入库------添加单据信息
	 */
	public function insert ($params)
	{
		$result	= array('success' => 0,'error' =>'');
		$prc				= trim($params['prc_id']);
		$prcs				= explode('|',$prc); 
		$prc_id				= $prcs[0]; 
		$prc_name			= $prcs[1];
		$in_warehouse_type	= isset($params['in_warehouse_type'])?trim($params['in_warehouse_type']):0;
		$account_type		= isset($params['account_type'])?trim($params['account_type']):0;
		$send_goods_sn		= trim($params['send_goods_sn']);
		$info				= trim($params['info']);
		$type				= trim($params['type']);
		$order_time			= trim($params['order_time']);
		$time				= time() . rand(10000,99999);
		$addtime			= date("Y-m-d H:i:s");
		$shijia				= isset($params['shijia'])?$params['shijia']:0;
		$adjust_type		= isset($params['adjust_type'])?$params['adjust_type']:0;

		#1、单据主表信息整理
		$order_info = array(
			'type'				=>	$type,
			'status'			=>	1,
			'order_time'		=>	$order_time,
			'in_warehouse_type'	=>  $in_warehouse_type,
			'account_type'		=>	$account_type,
			'adjust_type'		=>  $adjust_type,
			'send_goods_sn'		=>	$send_goods_sn,
			'shijia'			=>	$shijia,
			'make_order'		=>	$_SESSION['userName'],
			'prc_id'			=>	$prc_id,
			'prc_name'			=>	$prc_name,
			'addtime'			=>	$addtime,
			'info'				=>	$info,
			'times'				=>	$time
			);
		
		#2、单据主表数据验证------？？待做(根据单据不同做不同的限制)
			if ($prc_id=='')
			{
				$result['error'] = "加工商不能为空！";
				Util::jsonExit($result);
			}
			#买石单和其他入库单、其他出库单验证
			if ($type=="MS" || $type=="RK" || $type=="CK")
			{
				
				if ($in_warehouse_type=='')
				{
					$result['error'] = ($type=="CK"?"出库方式不能为空！":"入库方式不能为空！");
					Util::jsonExit($result);
				}
				if ($account_type=='')
				{
					$result['error'] = "结算方式不能为空！";
					Util::jsonExit($result);
				}
				if ( ($type=="MS" || $type=="RK")&& $shijia=='')
				{
					$result['error'] = "支付总计不能为空！";
					Util::jsonExit($result);
				}	
			}
			
			#调整单验证
			if($type=="AS")
			{
				
				if($adjust_type=='')
				{
					$result['error'] = "调整方式不能为空！";
					Util::jsonExit($result);
				}
			}

		#3、文件验证且取得石包明细信息
		$dia_info = $this->checkOrderGoods($type);//取得明细信息
		$newmodel	=  new DiaOrderModel(46);
		//买石单 校验重量、金额
		if($type == 'MS'){
			$pro_sn = _Post::getString('pro_sn');
			$is_batch = $_POST['is_batch'];
			if($is_batch == ''){
				$result['error'] = "请选择是否分批采购！";
				Util::jsonExit($result);
			}
			$order_info['pro_sn'] = $pro_sn;
			$has = $newmodel->hasProSN($pro_sn);
			if(!$has){//首次采购
				$res = $newmodel->setBatch($is_batch,$pro_sn);
				if(!$res){
					$result['error'] = '操作失败';
					Util::jsonExit($result);
				}
			}

			//获取库存[已买]
			$now = $newmodel->getSumByProSN($pro_sn);
			if (empty($new['weight']))
			{
				$now['weight'] = 0;
			}
			if (empty($new['all_total']))
			{
				$now['all_total'] = 0;
			}
			//获取总数
			$all = $newmodel->getAltProinfo($pro_sn);
			//本次采购金额
			$_weight = array_column($dia_info,'zongzhong');
			$_price = array_column($dia_info,'caigouchengben');
			$_total = 0;
			foreach ($_price as $k=>$p) {
				$_total += $p*$_weight[$k];
			}
			$weight = array_sum($_weight) + $now['weight'];
			$total = $_total + $now['all_total'];
			if($is_batch == 0){//采购完成
				$res = $newmodel->checkProRules($weight,$total,$all['weight'],$all['all_total']);
			}else{//分批采购
				$res = $newmodel->checkProRules2($weight,$total,$all['weight'],$all['all_total']);
			}
			if($res !==  true){
				if($res == 'w_error'){
					$result['error'] = '本次购买重量有误';//.$all['weight'].",采购单据对应买石单总重".$weight;
				}
				if($res == 't_error'){
					$result['error'] = '本次购买金额有误';//.$all['all_total'].",采购单据对应买石单总金额".$total."本单".$_total;
				}
				Util::jsonExit($result);
			}
		}
		//print_r($order_info);exit;
		#4、添加操作
		$res = $newmodel->add_info($order_info,$dia_info);
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

	/***************************************************************************************************
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');
		
		$id					= _Post::getInt('order_id');
		$prc				= trim($params['prc_id']);
		$prcs				= explode('|',$prc); 
		$prc_id				= $prcs[0]; 
		$prc_name			= $prcs[1];
		$in_warehouse_type	= isset($params['in_warehouse_type'])?trim($params['in_warehouse_type']):0;
		$account_type		= isset($params['account_type'])?trim($params['account_type']):0;
		$send_goods_sn		= trim($params['send_goods_sn']);
		$info				= trim($params['info']);
		$type				= trim($params['type']);
		$order_time			= trim($params['order_time']);
		$shijia				= isset($params['shijia'])?$params['shijia']:0;
		$adjust_type		= isset($params['adjust_type'])?$params['adjust_type']:0;

		$newmodel =  new DiaOrderModel($id,46);
		#判断状态是否正确
		if ($newmodel->getValue('status') != 1)
		{
			$result['error'] = '只有保存状态的才可以修改';
			Util::jsonExit($result);
		}
		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'order_id'			=> $id,
			'in_warehouse_type'	=> $in_warehouse_type,
			'account_type'		=> $account_type,
			'send_goods_sn'		=> $send_goods_sn,
			'shijia'			=> $shijia,
			'prc_id'			=> $prc_id,
			'order_time'		=> $order_time,
			'prc_name'			=> $prc_name,
			'info'				=> $info,
			'type'				=> $type,
			'adjust_type'		=> $adjust_type
		);

        #买石单和其他入库单、其他出库单验证
        if ($type=="MS" || $type=="RK" || $type=="CK")
        {

            if ($in_warehouse_type=='')
            {
                $result['error'] = ($type=="CK"?"出库方式不能为空！":"入库方式不能为空！");
                Util::jsonExit($result);
            }
            if ($account_type=='')
            {
                $result['error'] = "结算方式不能为空！";
                Util::jsonExit($result);
            }
            if ($shijia=='')
            {
                $result['error'] = "支付总计不能为空！";
                Util::jsonExit($result);
            }
        }

        #调整单验证
        if($type=="AS")
        {

            if($adjust_type=='')
            {
                $result['error'] = "调整方式不能为空！";
                Util::jsonExit($result);
            }
        }

		//没有上传数据
		if (!isset($_FILES['file']['error']))
		{
			$res = $newmodel->saveData($newdo,$olddo);
		}
		//上传数据
		else
		{
			$dia_info = $this->checkOrderGoods($type);//取得明细信息
			$newmodel	=  new DiaOrderModel(46);
			$res = $newmodel->update_info($newdo,$dia_info);
		}
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $id;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '修改此处为想显示在页签上的字段';
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/***************************************************************************************************
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new DiaOrderModel($id,46);
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
	/***************************************************************************************************
	fun:checkOrderGoods
	description:检查上传文件数据
	***************************************************************************************************/
	public function checkOrderGoods($type)
	{
		$result		 = array('success' => 0,'error' => '');
		if (!isset($_FILES['file']['error']))
		{
			$result['error'] = '请选择上传文件';
			Util::jsonExit($result);
		}
		$model		 = new DiaModel(45);
		$order_goods = array();
		$shibaos     = array();
		if ($_FILES['file']['error'] == 0)
		{
			$file_array = explode(".",$_FILES['file']['name']);
			$file_extension = strtolower(array_pop($file_array)); 
			if($file_extension != 'csv')
			{
				$result['error'] = "请上传csv格式的文件";
				Util::jsonExit($result);
			}
			$file = fopen($_FILES['file']['tmp_name'],"r");
			$i = 0;
			while(! feof($file))
			{
				$ret = fgets($file);
				if ($i > 0)
				{
					$dia = explode(",", $ret);
					if (trim($dia[0]) != '')
					{				
						$og = array(
							'shibao'=>strtoupper(trim($dia[0])),  //A
							'num'=>intval(trim($dia[1])),//B
							'zongzhong'=>trim($dia[2]),//C
							'zhengshuhao'=>isset($dia[3])?strtoupper(trim($dia[3])):'', //D
							'zhong'=>isset($dia[4])?strtoupper(trim($dia[4])):'', //E
							'yanse'=>isset($dia[5])?strtoupper(trim($dia[5])):'', //F
							'jingdu'=>isset($dia[6])?strtoupper(trim($dia[6])):'', //G
							'qiegong'=>isset($dia[7])?strtoupper(trim($dia[7])):'',// H
							'duichen'=>isset($dia[8])?strtoupper(trim($dia[8])):'',//I
							'paoguang'=>isset($dia[9])?strtoupper(trim($dia[9])):'',//J
							'yingguang'=>isset($dia[10])?strtoupper(trim($dia[10])):'',//K
							'caigouchengben'=>isset($dia[11])?trim($dia[11]):0,//L
							'xiaoshouchengben'=>isset($dia[12])?trim($dia[12]):0,//M
						);
						#获取石包信息
						$shibao = $model->getInfoByShibao($og['shibao']);
						//var_dump($shibao);exit;
						if ($type != 'MS' && $type != 'RK')
						{
							if(($type == "TH") && $og['caigouchengben']>0)
							{
								
							}
							else
							{
								$og['caigouchengben'] = isset($shibao['caigouchengben'])?$shibao['caigouchengben']:'';
							}
							$og['xiaoshouchengben']		= isset($shibao['xiaoshouchengben'])?$shibao['xiaoshouchengben']:'';
							$og['zhengshuhao']			= isset($shibao['zhengshuhao'])?$shibao['zhengshuhao']:'';
							$og['zhong']				= isset($shibao['zhong'])?$shibao['zhong']:'';
							$og['yanse']				= isset($shibao['yanse'])?$shibao['yanse']:'';
							$og['jingdu']				= isset($shibao['jingdu'])?$shibao['jingdu']:'';
							$og['qiegong']				= isset($shibao['qiegong'])?$shibao['qiegong']:'';
							$og['duichen']				= isset($shibao['duichen'])?$shibao['duichen']:'';
							$og['paoguang']				= isset($shibao['paoguang'])?$shibao['paoguang']:'';
							$og['yingguang']			= isset($shibao['yingguang'])?$shibao['yingguang']:'';
						}
						if ($type !='MS' && $type != 'RK' && !$model->shibao_exist($og['shibao']))
						{
							$result['error'] = $og['shibao'] . "石包不存在";
							Util::jsonExit($result);
						}
						if (substr($og['shibao'], 0 ,2) != 'KL')
						{
							$result['error'] = $og['shibao'] . "BDD的石包号必须以KL开头";
							Util::jsonExit($result);
						}
						if ($og['num'] <= 0)
						{
							$result['error'] = $og['shibao'] . "石包数量不能小于0";
							Util::jsonExit($result);
						}
						if ($og['zongzhong'] <= 0)
						{
							$result['error'] = $og['shibao'] . "石包重量不能小于0";
							Util::jsonExit($result);
						}
						if ($og['caigouchengben'] <= 0 && ($type =='MS' || $type == 'RK'))
						{
							$result['error'] = $og['shibao'] . "石包每卡采购价格不能小于0";
							Util::jsonExit($result);
						}
						if ($og['xiaoshouchengben'] <= 0 && ($type =='MS' || $type == 'RK'))
						{
							$result['error'] = $og['shibao'] . "石包每卡销售价格不能小于0";
							Util::jsonExit($result);
						}
						#检查单据信息2015/3/16 星期一
						$this->checkOrderCorrect($type, $og, $shibao);
						//检测重复  待定  去掉
						if (in_array($og['shibao'],$shibaos))
						{
							//var_dump($shibaos);exit;
							//$result['error'] = "错误!同一张单据中石包不能重复";
							//Util::jsonExit($result);
						}
						$shibaos[] = $og['shibao'];
						$order_goods[] = $og;
					}
					else
					{
						if($i == 1)
						{
							$result['error'] = "上传的附件数据不能为空！";
							Util::jsonExit($result);
						}                    
					}
				}
				$i++;
			}
			fclose($file);
		}
		else
		{
			$result['error'] = "请选择上传文件";
			Util::jsonExit($result);
		}
		if (count($shibaos) != count($order_goods))
		{
			//$result['error'] = "错误!同一张单据中石包不能重复";
			//Util::jsonExit($result);
		}
		//var_dump($order_goods);exit;
		return $order_goods;
	}

	/*****************************************************************************************************
	fun:checkOrderCorrect
	description:检测单据条件是否符合
	******************************************************************************************************/
	function checkOrderCorrect($type, $og, $shibao)
	{
		if ($type == 'SS') //送石单
		{
			if ($og['num'] > $shibao['kucun_cnt'])
			{
				$result['error'] = $og['shibao'] . "石包数量不足";
				Util::jsonExit($result);
			}
			if ($og['zongzhong'] > $shibao['kucun_zhong'])
			{
				$result['error'] = $og['shibao'] . "石包重量不足";
				Util::jsonExit($result);
			}
		}
		elseif ($type == 'HS') //还石单
		{
			#还石单>送石数量-退石数量
			if ($og['num'] > $shibao['SS_cnt'] - $shibao['TS_cnt'] )
			{
				$result['error'] = $og['shibao'] . "石包数量不足";
				Util::jsonExit($result);
			}
			$zhong = round(($shibao['SS_zhong'] - $shibao['TS_zhong']),3);
			if ($og['zongzhong'] > $zhong)
			{
				$result['error'] = $og['shibao'] . "石包重量不足";
				Util::jsonExit($result);
			}
		}
		elseif ($type == 'TS') //退石单
		{
			//退石单>送石数量-还石数量
			if ($og['num'] > $shibao['SS_cnt'] - $shibao['HS_cnt'] )
			{
				$result['error'] = $og['shibao'] . "石包数量不足";
				Util::jsonExit($result);
			}

			$zong = round(($shibao['SS_zhong'] - $shibao['HS_zhong']),3);
			if ($og['zongzhong'] > $zong)
			{
				$result['error'] = $og['shibao'] . "石包重量不足";
				Util::jsonExit($result);
			}
		}

		//遗失单、损益单、退货单、其他出库
		elseif ($type == 'YS' || $type == 'SY' || $type == 'TH' || $type == 'CK')
		{
			if ($og['num'] > $shibao['kucun_cnt'])
			{
				$result['error'] = $og['shibao'] . "石包数量不足";
				Util::jsonExit($result);
			}
			if ($og['zongzhong'] > $shibao['kucun_zhong'])
			{
				$result['error'] = $og['shibao'] . "石包重量不足";
				Util::jsonExit($result);
			}
		}
		//调整单
		//elseif ($type == 'AS' && $og['order_id']>0)  $og['order_id']不知道从哪里来 条件限制暂时没做
		elseif ($type == 'AS' && $og['order_id']>0)
		{
			$model = new DiaOrderModel(45);
			$info  = $model->getInfoByOrderId($og['order_id']);
			if(!$info)
			{
				$result['error'] =  "调整单错误";
				Util::jsonExit($result);
			}
			if($info['adjust_type'] == 0)
			{
				if ($og['num'] > $shibao['HS_cnt'])
				{
					$result['error'] = $og['shibao'] . "石包调整数量不能大于还石数量";
					Util::jsonExit($result);
				}
				if ($og['zongzhong'] > $shibao['HS_zhong'])
				{
					$result['error'] = $og['shibao'] . "石包调整石重不能大于还石重量";
					Util::jsonExit($result);
				}	
			}
		}
		
	}
	/*******************************************************************************************************
	fun:getTemplate
	decription:下载模板
	********************************************************************************************************/
	public function getTemplate($params)
	{
		$type     = $params['type'];
		$typename = $this->order_type; 
		//$name     = $typename[$type];
		header("Content-Disposition: attachment;filename=".$type.date("Ymd").".csv");
		if ($type == 'MS' || $type == 'RK')
		{
			$str = "石包,总数量(粒),总重量(ct),证书号,重量,颜色,净度,切工,对称,抛光,荧光,每卡采购价格(元),每卡销售价格(元)\n";
		}
		else if ($type == 'CK')
		{
			$str = "石包,总数量(粒),总重量(ct),证书号,重量,颜色,净度,切工,对称,抛光,荧光\n";
		}
		else if ($type == 'TH')
		{
			$str = "石包,总数量(粒),总重量(ct),证书号,重量,颜色,净度,切工,对称,抛光,荧光,每卡采购价格(元)\n";
		}
		else
		{
			$str = "石包,总数量(粒),总重量(ct)\n";
		}
		//取得明细信息
		if (isset($params['id']))
		{
			$id   = $params['id'];
			$model = new DiaOrderGoodsModel(45);
			$res = $model->getDetailByOrderId($id);
			if ($type == 'MS' || $type == 'RK')
			{
				$str = "石包,总数量(粒),总重量(ct),证书号,重量,颜色,净度,切工,对称,抛光,荧光,每卡采购价格(元),每卡销售价格(元)\n";
			}
			else
			{
				$str = "石包,总数量(粒),总重量(ct),每卡采购价格(元),每卡销售价格(元)\n";
			}
			if ($res)
			{ 

				foreach ($res as $val)
				{
					if ($type == 'MS' || $type == 'RK')
					{
						$str .= '"'.$val['shibao'].'",'.
								'"'.$val['num'].'",'.
								'"'.$val['zongzhong'].'",'.
								'"'.$val['zhengshuhao'].'",'.
								'"'.$val['zhong'].'",'.
								'"'.$val['yanse'].'",'.
								'"'.$val['jingdu'].'",'.
								'"'.$val['qiegong'].'",'.
								'"'.$val['duichen'].'",'.
								'"'.$val['paoguang'].'",'.
								'"'.$val['yingguang'].'",'.
								'"'.$val['caigouchengben'].'",'.
								'"'.$val['xiaoshouchengben'].'" '."\n";
					}
					else
					{
						$str .= '"'.$val['shibao'].'",'. 
								'"'.$val['num'] .'",'. 
								'"'.$val['zongzhong'] .'",'. 
								'"'.$val['caigouchengben'].'",'. 
								'"'.$val['xiaoshouchengben'].'" '."\n";
					}
				}

			}
		}
		echo iconv("utf-8","gbk", $str);
	}
	/*****************************************************************************************************
	fun:get_pro_list
	description:获取加工商列表
	*******************************************************************************************************/
	public function get_pro_list()
	{
		//获取加工厂信息
		$pro_info = ApiModel::pro_api('GetSupplierList',array('status'=>1));
		return  $pro_info['return_msg']['data'];
	}

	/*******************************************************************************************************
	fun:check
	description:审核单据
	********************************************************************************************************/
	public	function check($params)
	{
		$result	 = array('success' => 0,'error' => '');
		$id      = $params['id'];
		$type    = $params['type'];
		$shibaos = array();
		#只有已保存状态的才可以审核
		$model_order = new DiaOrderModel($id,46);
		if ($model_order->getValue('status') != 1)
		{
			$result['error'] ="单据状态不正确，只有保存状态才可以！";
			Util::jsonExit($result);
		}
		#取得主表单据信息
		$order_data = $model_order->getDataObject();

		$model   = new DiaModel(45);
		if ($type=='MS' || $type =='RK')
		{
			$fun	 = "check_info_ms_rk";
		}
		else
		{
			$fun	 = "check_info_other";
		}

		#1、查询该买石单明细数据
		$model_dia_goods  =  new DiaOrderGoodsModel(45);
		$data			  =  $model_dia_goods->getDetailByOrderId($id);
		#2、检查重复、核对单据数量(买石单、其他入库单除外)、库存相关信息-----这个保存时已经验证---
		if ($data)
		{
			foreach ($data as $og)
			{
				//检测重复  待定
				if (in_array($og['shibao'],$shibaos))
				{
					//$result['error'] = "错误!同一张单据中石包不能重复";
					//Util::jsonExit($result);
				}
				$shibaos[] = $og['shibao'];
				#单据数量检查 --非买石单和其他入库单
				if (!($type == 'MS' || $type == 'RK'))
				{
					$shibao = $model->getInfoByShibao($og['shibao']);
					if(!$shibao)
					{
						$result['error'] ="石包不存在";
						Util::jsonExit($result);
					}
					$this->checkOrderCorrect($type, $og, $shibao);	
					#调整单审核需要判断数量
					if ($type == 'AS')
					{
						if($shibao['HS_cnt']<$og['num'] || $shibao['HS_zhong']<$og['zongzhong'])
						{
							$result['error'] = "石包" . $og['shibao'] . "还石数量小于调整数量";
							Util::jsonExit($result);
						}

					}
				}
			}
			#调整单需要财务审核
			$res = $model_order->$fun($id,$data,$type,$order_data);
		}
		else
		{
			$result['error'] ="单据明细不能为空";
			Util::jsonExit($result);
		}
		if ($res == true)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "审核失败";
		}
		Util::jsonExit($result);
	}
	/********************************************************************************************************
	fun:cancle
	description:取消单据--所有通用
	*********************************************************************************************************/
	public function cancle($params)
	{
		$result	 = array('success' => 0,'error' => '');
		$order_id	= $params['id'];
		$type		= $params['type'];
		$order_type = $this->order_type;
		$model      = new DiaOrderModel($order_id,46);
		if ($model->getValue('status') !=1 )
		{
			$result['error'] = $order_type[$type]."状态不正确，只有保存状态的才可以取消";
			Util::jsonExit($result);
		}
		$res		= $model->cancle_info($order_id);
		if (!$res)
		{
			$result['error'] = $order_type[$type]."取消失败";
		}
		else
		{
			$result['success'] = 1;
		}
		Util::jsonExit($result);
	}
	/********************************************************************************************************
	fun:print_shibao
	description:打印单据--所有通用
	*********************************************************************************************************/
	public function print_shibao($params)
	{
		$id = $params['id'];
		
		$model_order = new DiaOrderModel($id,45);
		$model_dia   = new DiaOrderGoodsModel(45);
		$order       = $model_order->getInfoByOrderId($id);
		$order_goods = $model_dia->getDetailByOrderId($id);
		$type        = $model_order->getValue('type');
		$tname       = $this->order_type; 
		$title       = $tname[$type];
		$this->render('dia_order_print.htm',array(
			'order_goods' => $order_goods,
			'order'		  => $order,
			'title'		  => $title
		));
	}
	/********************************************************************************************************
	fun:rebulidShibao
	description:重新核算石包信息
	*********************************************************************************************************/
	function rebulidShibao ($params)
	{
		$shibao  = $params['shibao'];
		$model   = new DiaOrderModel(46);
		$res     = $model->checkShibaoInfo($shibao,$this->order_type);
		if (!$res)
		{
			$result['error'] = "核算失败";
		}
		else
		{
			$result['success'] = 1;
		}
		Util::jsonExit($result);
	}

	public function downloads($param) 
	{
		$args = array(
			'order_id'			=> _Request::get('order_id'),
			'type'				=> _Request::get('type'),
			'status'			=> _Request::get('status'),
			'send_goods_sn'		=> _Request::get('send_goods_sn'),
			'shibao'			=> _Request::get('shibao'),
			'zhengshuhao'		=> _Request::get('zhengshuhao'),
			'make_order'		=> _Request::get('make_order'),
			'prc_id'			=> _Request::get('prc_id'),
			'in_warehouse_type'	=> _Request::get('in_warehouse_type'),
			'account_type'		=> _Request::get('account_type'),
			'add_time_start'	=> _Request::get('add_time_start'),
			'add_time_end'		=> _Request::get('add_time_end'),
			'check_time_start'	=> _Request::get('check_time_start'),
			'check_time_end'	=> _Request::get('check_time_end'),
			'info'				=> _Request::get('info')
		);

		//$page = _Request::getInt("page",1);
		$where = array(
			'order_id'			=> $args['order_id'],
			'type'				=> $args['type'],
			'status'			=> $args['status'],
			'send_goods_sn'		=> $args['send_goods_sn'],
			'shibao'			=> $args['shibao'],//
			'zhengshuhao'		=> $args['zhengshuhao'],//
			'make_order'		=> $args['make_order'],
			'prc_id'			=> $args['prc_id'],
			'in_warehouse_type' => $args['in_warehouse_type'],
			'account_type'		=> $args['account_type'],
			'add_time_start'	=> $args['add_time_start'],
			'add_time_end'		=> $args['add_time_end'],
			'check_time_start'	=> $args['check_time_start'],
			'check_time_end'	=> $args['check_time_end'],
			'info'				=> $args['info']
			);
		
		$model = new DiaOrderModel(45);
		$data = $model->pageList($where,1,100000,false);
		if ($data['data']) 
		{
			$down = $data['data'];
			$xls_content = "序号,订单号,加工商,价格总计,石包号,制单人,总数量,总重量,纸质单号,制单时间,审核时间,状态\r\n";
			foreach ($down as $key => $val) 
			{
	
				$xls_content .= $val['order_id'] . ",";
				$xls_content .= $val['type'].$val['order_id'] . ",";
				$xls_content .= $val['prc_name']. ",";
				$xls_content .= $val['goods_total'] . ",";
				$xls_content .= $val['shibao']. ",";
				$xls_content .= $val['make_order'] . ",";
				$xls_content .= $val['goods_num'] . ",";
				$xls_content .= $val['goods_zhong']. ",";
				$xls_content .= $val['send_goods_sn'] . ",";
				$xls_content .= $val['addtime'] . ",";
				$xls_content .= $val['checktime'] . ",";
				$xls_content .= $this->dd->getEnum('dia_order.status',$val['status']) . "\n";		
			}
		} 
		else 
		{
			$xls_content = '没有数据！';
		}
		header("Content-type: text/html; charset=gbk");
		header("Content-type:aplication/vnd.ms-excel");
		header("Content-Disposition:filename=" . iconv("utf-8", "gbk", "shibao" . date("Y-m-d")) . ".csv");
		echo iconv("utf-8", "gbk", $xls_content);
	
	}

	/**
	 * 买石单：校验采购单
	 */
	public function checkProSN()
	{
		$pro_sn = _Post::getString('pro_sn');
		$model = new DiaOrderModel(45);
		$res = $model->checkMSpro($pro_sn);
		$result	 = array('success' => 0,'error' => '');
		if(empty($res)){
			$result['error'] = '未查到改采购单信息';
			Util::jsonExit($result);
		}
		if($res['check_status'] != 3){
			$result['error'] = '不是有效采购单';
			Util::jsonExit($result);
		}
		$has = $model->hasProSN($pro_sn);
		if(($res['is_batch']==0) && $has){
			$result['error'] = '该采购单已完成[非分批采购]';
			Util::jsonExit($result);
		}
		$result['success'] = 1;
		$result['pro'] =$res;

		$DiaModel = new DiaOrderModel(45);
		$now = $DiaModel->getSumByProSN($pro_sn);
		$all = $DiaModel->getAltProinfo($pro_sn);
		$weight = ($now['weight'])?$now['weight']:'0';
		$total = ($now['all_total'])?$now['all_total']:'0';
		$note = "采购单：总重量为{$all['weight']}ct,总金额为{$all['all_total']}元;已购买：重量为{$weight}ct,金额为{$total}元";

		$result['info'] = $this->fetch('dia_order_ms_add.html',[
			'view'=>new DiaOrderView($DiaModel),
			'pro_list'=>$this->get_pro_list(),'type'=>'MS',
			'proinfo'=>$res,'note'=>$note
		]);
		Util::jsonExit($result);
	}

}

?>