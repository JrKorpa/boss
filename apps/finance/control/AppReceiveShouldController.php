<?php
/**
 *  -------------------------------------------------
 *   @file		: AppReceiveShouldController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-02 09:41:38
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiveShouldController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $model = new CustomerSourcesModel(1);
        $source_list = $model->getSourcesPay();
		$this->render('app_receive_should_search_form.html',array('bar'=>Auth::getBar(),'source_list'=>$source_list));
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
			'should_number' =>_Request::get('should_number'),
			'from_ad' =>_Request::getString('from_ad'),
			'status'=>_Request::getInt('status'),
			'total_status'=>_Request::getInt('total_status'),
            'make_time_start'=>  _Request::getString('make_time_start'),
            'make_time_end'=>  _Request::getString('make_time_end'),
            'check_time_start'=>  _Request::getString('check_time_start'),
            'check_time_end'=>  _Request::getString('check_time_end'),
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
		$where = array();
        $where['should_number'] = $args['should_number'];
        $where['from_ad'] = $args['from_ad'];
        $where['status'] = $args['status'];
        $where['total_status'] = $args['total_status'];
        $where['make_time_start'] = $args['make_time_start'];
        $where['make_time_end'] = $args['make_time_end'];
        $where['check_time_start'] = $args['check_time_start'];
        $where['check_time_end'] = $args['check_time_end'];

		$model = new AppReceiveShouldModel(29);
		$data = $model->pageList($where,$page,10,false);
		$sourceModel = new CustomerSourcesModel(1);
        if($data['data']){
            foreach ($data['data'] as $key => $val){
                $data['data'][$key]['ad_name'] = $sourceModel->getSourceNameById($val['from_ad']);
            }
        }
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_receive_should_search_page';
		$this->render('app_receive_should_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}


	/**
	 *	verifyPrice，渲染确认收款页面
	 */
	public function verifyPrice ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
        $model = new AppReceiveShouldModel($id,29);
        $do = $model->getDataObject();
        if($do['total_status'] == 3){
            $result['content'] = '已完成付款';
            Util::jsonExit($result);
        }
		$total = floatval($do['total_cope'] - $do['total_real']);		//获取剩余金额
        $bankName = $model->getBankName();
		$result['content'] = $this->fetch('app_receive_should_info.html',array(
			'view'=>new AppReceiveShouldView($model),
            'total'=>$total,
            'bankName'=>$bankName
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}


    /**
	* 确认收款
	*/
	public function insert(){
		$result = array('success' => 0,'error' => '');
		if( empty(_Request::getInt('bank_name')) )
		{
			$result['error'] = '请选择收款账号';
			Util::jsonExit($result);
		}
		if( empty(_Request::getString('bank_serial_number')) )
		{
			$result['error'] = '请填写银行交易流水号';
			Util::jsonExit($result);
		}
		if (!preg_match("/^[0-9]*$/",_Request::getString('bank_serial_number')))
		{
			$result['error'] = '银行交易流水号只能为数字';
			Util::jsonExit($result);
		}
		if( empty(_Request::getString('pay_time')) || _Request::getString('pay_time') > date("Y-m-d") ){
			$result['error'] = '收款时间不能为空，并且不能大于今天';
			Util::jsonExit($result);
		}
		$submit_total = _Request::getFloat('total');
		if( empty($submit_total) )
		{
			$result['error'] = '收款金额金额不能为空';
			Util::jsonExit($result);
		}
		$syje = _Request::get('syje');
		if( !empty($syje))
		{
			if($submit_total>_Request::get('syje'))
			{
				$result['error'] = '实际收款金额不能超过实际应收金额';
				Util::jsonExit($result);
			}
			if($syje>0 && $submit_total<0)
			{
				$result['error'] = '应收金额为正数，收款金额不能为小于0';
				Util::jsonExit($result);
			}
		}
		if($should_id = _Request::getInt('should_id'))
		{
            $shouldModel = new AppReceiveShouldModel($should_id,30);
			$sdata = $shouldModel->getDataObject();
			if($sdata['status']  != 2)
			{
				$result['error'] = '应收单未审核，不能进行收款操作';
				Util::jsonExit($result);
			}
			// 应收金额 == 实收金额 即停止 不可操作
			if(floatval($sdata['total_real']) == floatval($sdata['total_cope']))
			{
				$result['error'] = '应收单:'.$sdata['should_number'].' 应收款已结清';
				Util::jsonExit($result);
			}

			// 满足条件 写入数据库
			$data_arr = array(
                'real_number'=>'',
				'from_ad'=>$sdata['from_ad'],
				'should_number'=>$sdata['should_number'],
				'bank_name'=>$shouldModel->getBankName(_Request::getInt('bank_name')),
				'bank_serial_number'=>_Request::get('bank_serial_number'),
				'total'=>$submit_total,
				'pay_tiime'=>_Request::get('pay_time'),
				'maketime'=>date("Y-m-d H:i:s"),
				'makename'=>$_SESSION['userName'],
				);
			//var_dump($sdata);exit;
			$realModel = new AppReceiveRealModel(30);
			if($rdata = $realModel->addReal($data_arr))
			{
				//生成收款记录成功,修改应收单 实收金额 以及 收款状态
				$shouldModel->updateShouldInfo($should_id , $submit_total);

				//更新 销售出入库列表 相应数据的 回款周期/是否回款状态
				if( $sdata['total_status'] == 1 )
				{
					//判断是否是第一次收款 [未付款状态即是第一次]
					if( !$this->updateHuikuan($rdata['should_number'] , $rdata['pay_tiime']) )
					{
						//状态回滚
						$result['error'] = '找不到对应的销售出入库数据';
						Util::jsonExit($result);
					}
				}
				$result['success'] = 1;
				$result['error'] = '收款成功';
				Util::jsonExit($result);
			}
			else
			{
				$result['error'] = '收款失败';
				Util::jsonExit($result);
			}

		}
		else
		{
			$result['error'] ="非法应收单ID";
			Util::jsonExit($result);
		}
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$should_id = intval($params["id"]);
        $model = new AppReceiveShouldDetailModel(29);
		$data = $model->getDetailArr($should_id , '*' , true);
        $appModel = new AppReceiveApplyModel(29);
		foreach($data as $k => $v){
			$w = ' WHERE `apply_number` = \''.$data[$k]['apply_number'].'\'';
			$apply_id_arr = $appModel->getInfo('id','app_receive_apply',$w,true);
            $data[$k]['apply_id'] = $apply_id_arr['id'];
		}
		//VAR_DUMP($data);exit;
		$shouldModel = new AppReceiveShouldModel($should_id,29);
		$should_info = $shouldModel->getDataObject();

		//$ecsAdModel = new EcsAdModel(29);
		//$ecs_sql = 'SELECT `ad_sn`,`ad_name` FROM `ecs_ad` WHERE `ad_sn` = '.$should_info['from_ad'];
		//$info = $ecsAdModel->getnum($ecs_sql);
        $model = new CustomerSourcesModel(1);
        $ad_name = $model->getSourceNameById($should_info['from_ad']);

		$should_info['from_ad'] =  $ad_name;
		$should_info['num'] = count($data);
		$should_info['total'] = 0;
		foreach($data as $k =>$v){
			$should_info['total'] += $v['total_cope'];
		}
		$this->render('app_receive_should_show.html',array(
			'bar'=>Auth::getViewBar(),
			'info'=>$data,
			'should_info'=>$should_info,
		));
	}

	/**
	* 更新 销售出入库列表 相应数据的 回款周期/是否回款状态
	*  @abstract 接收应收单号-->查出相应的应收单ID should_id -->应收申请单单号-->核销单-->S/B单-->修改回款状态+回款周期
	*  @param INT $should_number 应收单号
	*  @param date $pay_tiime 财务收款时间
	*/
	public function updateHuikuan($should_number,$pay_tiime)
	{
        $model = new AppReceiveShouldModel(29);
		$data = $model->getRowNumber($should_number,'should_id');	//获取表 pay_should 应收单ID
		$shouldDetailModel = new AppReceiveShouldDetailModel(30);
		$applydata = $shouldDetailModel->getDetailArr($data['should_id'],'apply_number');	//获取表 pay_should_detail 应收申请单单号集合
		$applyModel = new AppReceiveApplyModel(30);
		$jxcOrderModel = new PayJxcOrderModel(30);
		$bool = true;

		foreach ($applydata as $value)
		{	//获取表 pay_apply 核销单单号
			$condition = 'WHERE `apply_number` = \''.$value.'\'';
			$apply = $applyModel->getInfo('check_sale_number','app_receive_apply',$condition);
			//根据核销单号得出 销售出入库ID
			if (empty($apply))//查不到数据报错 处理
			{
				return false;
			}
			$orderData = $jxcOrderModel->getRow('order_id,checktime',array('hexiao_number'=>$apply[0]['check_sale_number']));
			if(empty($orderData['order_id'])){
				return false;
			}
			//更新 销售出入库pay_jxc_order 中的 回款，回款周期
			$update = array(
				'is_return'=>1,
				'returntime'=>$pay_tiime,
				);
			$bool = $bool && $jxcOrderModel->updateRealValue($orderData['order_id'],$update);
		}
		return $bool;
	}


	/**
	* 生成应收单
	*/
	public function shouldAddSub(){
		$result = array('success' => 0,'error' =>'参数错误');
		$ids = _Post::get('ids');
		if($this->checkShouldCon($ids))//检查数据成功
		{
			//添加数据
			$model = new AppReceiveShouldModel(30);
			$res = $model->addShould($ids);
			if($res['error'])
			{
				$result['error'] = '生成应收单 CWYS'.$res['id'];
				$result['success'] = 1;
				$result['id'] = $res['id'];
			}else{
				$result['error'] = '生成应收单失败';
			}
		}
		Util::jsonExit($result);
	}

	/**
	* 检测是否符合生成应收单条件
	* 检查生成应收单的数据 是否相同订单来源/结算商，是否是“待生成应付单”状态。是否已经存在在其他应付单里面
	* @param $ids String 要合成应收单的 应收申请单ID
	*/
	public function checkShouldCon($ids_str){
		$result = array('success' => 0,'error' =>'');
		$applyModel = new AppReceiveApplyModel(29);
		if(!$applyModel->checkDistinct('from_ad',$ids_str)){
			$result['error'] = '所选单据不是同一个订单来源/结算商，不能提交。';
			Util::jsonExit($result);
		}
		$ids = explode(',', $ids_str);
		foreach ($ids as $k => $v) {
			$gRow = $applyModel->getRow($v,'status,should_number,apply_number');
			if($gRow['status'] != 5){
				$result['error'] = '申请单 '.$gRow['apply_number'].' 状态不对，不能提交。';
				Util::jsonExit($result);
			}
			if($gRow['should_number']!=''){
				$result['error'] = '申请单 '.$gRow['apply_number'].' 已经存在于应收单据 '.$gRow['should_number'].' 中，不能提交。';
				Util::jsonExit($result);
			}
		}
		return true;
	}

	//点击生成应收单，先检查数据的准确定和计算总金额
	public function shouldAddCheck($params)
	{
		$result = array('success' => 0,'error' =>'计算总金额，参数错误');
		$ids = $params['ids'];
		if($this->checkShouldCon($ids))
		{
			$applyModel = new AppReceiveApplyModel(29);
			$total = $applyModel->getTotalOfIds($ids);
			$result['success'] = 1;
			$result['total']	=	$total;
		}
		Util::jsonExit($result);
	}

	//审核应收单
	public function checkCon($params)
	{
		$result = array('success' => 0,'error' =>'操作失败');
		$shouldModel = new AppReceiveShouldModel(29);
		$should_id = $params['id'];
		$info = $shouldModel->getRow($should_id,'makename,total_cope,should_number,status');
		/*如果单据是取消状态，则不能进行审核*/
		if($info['status']==3){
			$result['error'] = '该单据已被取消，不能进行审核操作';
			Util::jsonExit($result);
		}

		if($info['makename'] == $_SESSION['userName']){
			$result = array('success' => 0,'error' =>'自己不能审核自己的单据');
			Util::jsonExit($result);
		}
		$set0 =  " total_status = 3 , status = 2 , checkname = '{$_SESSION['userName']}' ,checktime = '".date('Y-m-d H:i:s')."' " ;
		$set1 =  " total_status = 1 , status = 2 , checkname = '{$_SESSION['userName']}' ,checktime = '".date('Y-m-d H:i:s')."' " ;
		$where = ' should_id ='.$should_id;
		if($info['total_cope'] == 0 )
		{
			$shouldModel->updateCol($set0 , $where);	//应收金额为0元的单据，一经审核通过，付款状态自动更新为‘已付款’
		}else{
			$shouldModel->updateCol($set1 , $where);	//应收款不为0的单据
		}
		$result['success'] = 1;
		$result['error'] = '应收单：'.$info['should_number'].' 审核成功';

		Util::jsonExit($result);
	}

	//取消应收单
	public function delCon($params)
	{
		$result = array('success' => 0,'error' =>'操作失败');
		$should_id = $params['id'];
		$shouldModel = new AppReceiveShouldModel($should_id,29);
		$shouldDetailModel = new AppReceiveShouldDetailModel(29);
		// 制单人和审核人有权限取消

		if($_SESSION['userName'] != $shouldModel->getValue('makename'))
		{
			$result = array('success' => 0,'error' =>'只有制单人和审核人才能取消待审应收单');
			Util::jsonExit($result);
		}
		if($shouldModel->getValue('status') != 1)
		{
			$result = array('success' => 0,'error' =>'待审核状态才能进行审核操作');
			Util::jsonExit($result);
		}
		$res = $shouldModel->updateCol('status = 3',' should_id = '.$should_id);
		//更新对应的pay_apply应收申请单状态
		if($res)
		{
			$apply_numbers = $shouldDetailModel->getDetailArr($should_id , ' apply_number ');
			if(!empty($apply_numbers))
			{
				$applyModel = new AppReceiveApplyModel(30);
				$rows = $applyModel->update2(5,$apply_numbers);
			}
			$result['success'] = 1;
			$result['error'] ='应收单：'._Request::get('should_number').' 取消成功';
		}

		Util::jsonExit($result);
	}

}

?>