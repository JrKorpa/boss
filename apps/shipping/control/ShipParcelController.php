<?php
/**
 *  -------------------------------------------------
 *   @file		: ShipParcelController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-06 10:15:23
 *   @update	:
 *  -------------------------------------------------
 */
class ShipParcelController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('print_baoguo','download');

	/** 获取快递公司列表 **/
	public function getAllExpress(){
		$ex_model = new ExpressModel(1);
		return $ex_model->getAllExpress();
	}

	/** 获取公司列表 **/
	public function getCompanyList(){
		$model = new CompanyModel(1);
		return $model->getCompanyTree();//公司列表
	}

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//获取快递公司
		$getAllExpress = $this->getAllExpress();
		$dd = new DictModel(1);
		$company_list = $this->getCompanyList();
		$this->render('ship_parcel_search_form.html',array(
			'bar'=>Auth::getBar(),
			'express' =>$getAllExpress,
			'company_list' => $company_list,
			'dd'=> new DictView($dd)
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
			'express_id' => _Request::get("express_id"),
			'express_sn' => _Request::get("express_sn"),
			'send_status' => _Request::get("send_status"),
			'is_print' => _Request::get("is_print"),
			'company_id' => _Request::get("company_id"),
			'date_time_s'=> _Request::get("date_time_s"),
			'date_time_e'=> _Request::get("date_time_e"),
			'send_date_time_s' => _Request::get("send_date_time_s"),
			'send_date_time_e' => _Request::get("send_date_time_e"),
			'page_size'=> _Request::get("page_size"),
			'order_sn' => isset($params['order_sn']) ? trim($params['order_sn']) : '',
			'bill_m_no' => isset($params['bill_m_no']) ? trim($params['bill_m_no']) : '',

		);

		//获取快递公司
		$kuaidi = array();
		$AllExpress = $this->getAllExpress();
		foreach($AllExpress as $val){
			$kuaidi[$val['id']] = $val['exp_name'];
		}

		$page = _Request::getInt("page",1);
		$where = array(
			'express_id' =>$args['express_id'],
			'express_sn' =>$args['express_sn'],
			'send_status' =>$args['send_status'],
			'is_print' =>$args['is_print'],
			'company_id' => $args['company_id'],
			'date_time_s'=> $args['date_time_s'],
			'date_time_e'=> $args['date_time_e'],
			'send_date_time_s'=> $args['send_date_time_s'],
			'send_date_time_e'=> $args['send_date_time_e'],
			'page_size'=> $args['page_size']?$args['page_size']:10,
			'order_sn'=> $args['order_sn'],
			'bill_m_no'=> $args['bill_m_no'],
		);

		$model = new ShipParcelModel(43);
		$data = $model->pageList($where,$page,$where['page_size'],false);
		$comModel = new CompanyModel(1);
		foreach($data['data'] as $key => $val)
		{
			$data['data'][$key]['company_name'] = $comModel->getCompanyName($val['company_id']);
		}
		$pageData = $data;
		$pageData['filter'] = $args;
		// echo '<pre>';print_r($data);die;
		$pageData['jsFuncs'] = 'ship_parcel_search_page';
		$this->render('ship_parcel_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'kuaidi' => $kuaidi,
		));
	}

	/**
	 *	add，渲染添加页面 添加包裹单
	 */
	public function add ()
	{
                $tab_id = _Request::getInt('tab_id');
		$getAllExpress = $this->getAllExpress();
		$companyList = $this->getCompanyList();
		$result = array('success' => 0,'error' => '');
                $this->render('ship_parcel_info.html',array(
                        'view'=>new ShipParcelView(new ShipParcelModel(43)),
			'express' => $getAllExpress,
			'companyList'=>$companyList,
                        'tab_id' => $tab_id
                ));
//		$result['content'] = $this->fetch('ship_parcel_info.html',array(
//			'view'=>new ShipParcelView(new ShipParcelModel(43)),
//			'express' => $getAllExpress,
//			'companyList'=>$companyList,
//		));
		//$result['title'] = '添加';
		//Util::jsonExit($result);
	}

	/** 检测单据是否是自己的单据 **/
	public function checkUser($params){
		$result = array('success' => 0,'error' => '');
		$id = $params['id'];
		$model = new ShipParcelModel($id, 43);

		/** 检测是否发货，发货后不能编辑 **/
		if( $model->getValue('send_status') == 2){
			$result['error'] = '快递单已发货，不能编辑';
			Util::jsonExit($result);
		}

		$create_user = $model->select2('create_user', $where = " `id` ={$id} ", $type =1 );
		if($_SESSION['userName'] != $create_user){
			$result['error'] = '只有自己才能编辑自己的单据';
			Util::jsonExit($result);
		}
		$result['success'] = 1;
			Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面 修改包裹单
	 */
	public function edit ($params)
	{
		$result = array('success' => 0,'error' => '');
		//检测是否是自己的单据
		$id = intval($params["id"]);
		$model = new ShipParcelModel(44);
		$getAllExpress = $this->getAllExpress();
		$companyList = $this->getCompanyList();
		$tab_id = _Request::getInt("tab_id");
		$result['content'] = $this->fetch('ship_parcel_info.html',array(
			'view'=>new ShipParcelView(new ShipParcelModel($id,43)),
			'tab_id'=>$tab_id,
			'express' => $getAllExpress,
			'companyList'=>$companyList,
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面 包裹单
	 */
	public function show ($params)
	{
		//获取快递
		$kuaidi = array();
		$arr = $this->getAllExpress();
		foreach($arr as $val){
			$kuaidi[$val['id']] = $val['exp_name'];
		}
		//获取公司
		$company = array();
		$arr = $this->getCompanyList();
		foreach($arr as $val){
			$company[$val['id']] = $val['company_name'];
		}
		$id = intval($params["id"]);
		$this->render('ship_parcel_show.html',array(
			'bar' => Auth::getViewBar(),
			'bar1'=>Auth::getDetailBar("ship_parcel_detail"),
			'view'=>new ShipParcelView(new ShipParcelModel($id,43)),
			'kuaidi' => $kuaidi,
			'company' => $company,
		));
	}

	/**
	 *	insert，信息入库 写入包裹单
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$express_sn = $params['express_sn'];
		$express_id = $params['express_id'];
		$company_id = $params['company_id'];
                $tab_id = _Post::getInt('tab_id');
		$operate_content = '添加包裹单';

		if($express_sn == ''){
			$result['error'] = '快递单号不能为空';
			Util::jsonExit($result);
		}
		if($express_id == ''){
			$result['error'] = '请选择快递公司';
			Util::jsonExit($result);
		}
		if($company_id == ''){
			$result['error'] = '请选择目标展厅';
			Util::jsonExit($result);
		}

		//快递单正则验证
		$express_v =  new ExpressView(new ExpressModel($express_id,1));
		$rule = $express_v->get_freight_rule();
		if($rule && !preg_match($rule,$express_sn)){
			$result['error'] ="快递单号与快递公司不符！";
			Util::jsonExit($result);
		};
		//快递单号不能重复
		$newmodel =  new ShipParcelModel(44);
		$exists = $newmodel->select2(' `id` ' , $where = " `express_sn` = '{$express_sn}' " , $type =1 );
		if($exists){
			$result['error'] = '已存在快递单号：'.$express_sn;
			Util::jsonExit($result);
		}
		$model =  new ShipFreightModel(44);
		//快递单号 在快件列表中是否重复($field,$where,$type=1)
		$exists_kuaijian = $model->select2("freight_no","freight_no ='{$express_sn}' and express_id ={$express_id} and order_no !=''",$type=2);
		
		if($exists_kuaijian){
			$result['error'] = '快件列表中已存在快递单号';
			Util::jsonExit($result);
		}
		$res = $newmodel->insertDate($express_sn, $express_id, $company_id, $operate_content);
		if($res !== false)
		{
			$result['success'] = 1;
                        $result['x_id'] = $res;
                        $result['tab_id'] = $tab_id;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息 包裹单
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');

		if(!$params['express_sn']){
			$result['error'] = '请输入快递单号';
			Util::jsonExit($result);
		}
		if(!$params['express_id']){
			$result['error'] = '请选择快递公司';
			Util::jsonExit($result);
		}
		if(!$params['company_id']){
			$result['error'] = '请选择目标展厅';
			Util::jsonExit($result);
		}

		$newmodel =  new ShipParcelModel($id,44);
		$olddo = $newmodel->getDataObject();

		$company_list =  $this->getCompanyList();//公司列表
		foreach($company_list as $val){
			if($val['id'] == $params['company_id']){
				$shouhuofang = $val['company_name'];
			}
		}

		$newdo=array(
			'id' =>$id,
			'express_id'=>$params['express_id'],
			'express_sn'=>$params['express_sn'],
			'company_id'=>$params['company_id'],
			'shouhuoren' =>$shouhuofang,
		);
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;
			$result['title'] = '修改成功';
			$newmodel->insertLog($id, '修改包裹单');
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
		$model = new ShipParcelModel($id,44);
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

	/** 发货操作 **/
	public function Send($params){
		$result = array('success' => 0,'error' => '发货异常');
		$id = $params['id'];
		$model = new ShipParcelModel($id, 44);
		if( $model->getValue('send_status') ==2 ){
			$result['error'] = '当前包裹单已经发货，不能重复操作';
			Util::jsonExit($result);
		}
		//检测包裹单是否绑定 调拨单。至少绑定一个调拨单才能发货
		$detailModel = new ShipParcelDetailModel(43);
		$exsis = $detailModel->select2($fields = " `id` " , $where = " `parcel_id` = {$id} " , $type = 1);
		if(!$exsis){
			$result['error'] = '请添加 "包裹单详情" 后再进行发货操作';
			Util::jsonExit($result);
		}
		$company_list =  $this->getCompanyList();//公司列表
		foreach($company_list as $val){
			if($val['id'] ==$model->getValue('company_id')){
				$shouhuofang = $val['company_name'];
			}
		}

        /** 添加最新发货时间 START **/
		$details_list = $detailModel->select2($fields = " `zhuancang_sn`,`order_sn` " , $where = " `parcel_id` = {$id} " , $type = 0);
        $details = array();
        $send_order_ids = array();
        if($details_list){
            $zhuancang_sn_List = array();
            $order_List = array();
            foreach($details_list as $key => $val){
                if(empty($val['zhuancang_sn'])){
                    continue;
                }
                $order_List[] = $val['order_sn'];
                $zhuancang_sn_List[] = $val['zhuancang_sn'];
            }
            if($order_List){
                foreach($order_List as $order_sn){
                    $cnt = $detailModel->getWarehouseBillInfoByOrderSn($order_sn);
                    if($cnt){
                        if(!isset($details[$val['order_sn']])){
                            $details[$val['order_sn']]['num']=0;
                        }
                        $details[$val['order_sn']]['num'] += $cnt;
                    }
                }
            }
            if(!empty($zhuancang_sn_List)){
                $list = $detailModel->getWarehouseBillInfo($zhuancang_sn_List);
                if($list){
                    $details_ids = array();
                    foreach($list as $key => $val){
                        if($details_ids && !in_array($val['goods_id'],$details_ids)){
                            continue;
                        }
                        if(!isset($details[$val['order_sn']])){
                            $details[$val['order_sn']]['num']=0;
                        }
                        $details[$val['order_sn']]['num']++;
                    }
                }
                $olist = $detailModel->getOrderInfo($order_List);
                if($olist){
                    $odetails = array();
                    $odetails_ids = array();
                    foreach($olist as $key => $val){
                        $order_sn = $val['order_sn'];
                        $o_num = $val['cnt'];
                        if(isset($details[$order_sn]['num'])){
                            if($details[$order_sn]['num'] >= $o_num)
                            {
                                $send_order_ids[] = $val['id'];
                            }
                        }
                    }
                }
            }
        }

        /** 添加最新发货时间 END **/
		if($model->send($id)){
			$model_d=new ShipFreightModel(44);
			$newdo=array(
				'freight_no'=>$model->getValue('express_sn'),
				'express_id'=>$model->getValue('express_id'),
				'consignee'=>$shouhuofang,
				'order_mount'=>$model->getValue('amount'),
				'remark' => '展厅发货',
				'sender' => '郭伟',
				'department' => '物流部',
				'cons_address'=>$shouhuofang,
				'create_time'=>time(),
				'create_name'=>Auth::$userName
				);
			$res = $model_d->saveData($newdo,array());
            if($send_order_ids){
                $detailModel->updateSendtime($send_order_ids);
            }
			$result = array('success' => 1,'error' => '发货成功');
		}
		Util::jsonExit($result);
	}

	/** 打印包裹单 **/
	public function print_baoguo($params){
		/** 获取快递公司列表 **/
		$kuaidi = $this->getAllExpress();
		/** 获取公司列表 **/
		$company = $this->getCompanyList();
		/** 获取仓库列表 **/
		$arr = ApiWarehouseModel::getWarehouseList();
		$warehouseList = array();
		foreach($arr as $val){
			$warehouseList[$val['id']] = $val['name'];
		}

		$id = $params['id'];
		//获取包裹单信息列表
		$newModel = new ShipParcelModel(44);
		$info = $newModel->select2($fields = ' `express_id`, `express_sn`, `company_id` ' , $where = ' `id` = '.$id , $type= 2);
		foreach($kuaidi as $val){
			if($val['id'] == $info['express_id']){
				$info['express_id'] = $val['exp_name'];
				break;
			}
		}
		foreach($company as $val){
			if($val['id'] == $info['company_id']){
				$info['company_id'] = $val['company_name'];
				break;
			}
		}
		$model = new ShipParcelDetailModel(43);
		$data = $model->select2($fields = ' `id`,`zhuancang_sn`,`shouhuoren`, `goods_name`, `num`, `to_warehouse_id`, `order_sn` ', $where = " `parcel_id` = {$id} " , $type =3 );
		$company_arr = array();
		foreach ($company as $key => $value) {
			$company_arr[$value['id']] = $value['company_name'];

		}
                //获取客户姓名
                foreach($data as $key => $val){
                    $order_sn = $data[$key]['order_sn'];
                    if (!empty($order_sn)) {
                        $consignee = $model->GetConsigneeByOrdersn($order_sn);
                    }else {
                        $consignee = '--';
                    }
                    $data[$key]['consignee'] = $consignee;
                }

		$this->render('print_baoguo.html',array(
			'data' => $data,
			'info' =>$info,
			'time' => date('Y-m-d H:i:s'),
			'company_arr'=>$company_arr,
			'id' =>$id,
			'warehouseList' => $warehouseList,
                        'is_muti'=>0
		));
	}

	/** 更改包裹单打印状态 **/
	public function changePrintStatus($params){
		$result = array('success' => 0,'error' =>'');
		$id = $params['id'];
                $ids=  explode(',', $id);
               foreach($ids as $id)
               {
                    $model = new ShipParcelModel($id, 44);
                    $model->setValue('is_print', 1);
                    $sta = $model->save();
                    if($model->insertLog($id)){
                            $result['success'] = 1;
                    }
               }

		Util::jsonExit($result);
	}
        public function download()
        {
            /** 获取快递公司列表 **/
		$kuaidi = $this->getAllExpress();
		/** 获取公司列表 **/
		$company = $this->getCompanyList();
		/** 获取仓库列表 **/
		$arr = ApiWarehouseModel::getWarehouseList();
		$warehouseList = array();
		foreach($arr as $val){
			$warehouseList[$val['id']] = $val['name'];
		}
                $newModel = new ShipParcelModel(44);
                $model = new ShipParcelDetailModel(43);
                $ids=  _Request::getString('ids');
                $ids=explode(',',$ids);
                $muti=array();
                foreach($ids as $id)
                {
                    $temp=array();
                    $info = $newModel->select2($fields = ' `express_id`, `express_sn`, `company_id` ' , $where = ' `id` = '.$id , $type= 2);
                    foreach($kuaidi as $val){
                            if($val['id'] == $info['express_id']){
                                    $info['express_id'] = $val['exp_name'];
                                    break;
                            }
                    }
                    foreach($company as $val){
                            if($val['id'] == $info['company_id']){
                                    $info['company_id'] = $val['company_name'];
                                    break;
                            }
                    }
                    $data = $model->select2($fields = ' `id`,`zhuancang_sn`,`shouhuoren`, `goods_name`, `num`, `to_warehouse_id`, `order_sn` ', $where = " `parcel_id` = {$id} " , $type =3 );
                    $company_arr = array();
                    foreach ($company as $key => $value) {
                            $company_arr[$value['id']] = $value['company_name'];
                    }
                     //获取客户姓名start
                    foreach($data as $key => $val){
                        $order_sn = $data[$key]['order_sn'];
                        if (!empty($order_sn)) {
                            $consignee = $model->GetConsigneeByOrdersn($order_sn);
                        }else {
                            $consignee = '--';
                        }
                        $data[$key]['consignee'] = $consignee;
                    }
                    //end
                    $temp['data']=$data;
                    $temp['info']=$info;
                    $temp['time']=date('Y-m-d H:i:s');
                    $temp['company_arr']=$company_arr;
                    $temp['warehouseList']=$warehouseList;
                    $muti[]=$temp;
                }
               $ids=implode(',',$ids);
		$this->render('print_baoguo.html',array(
			'muti'=>$muti,
                        'ids'=>$ids,
                        'is_muti'=>1
		));

        }

	public function updateTime($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new ShipParcelModel($id,44);
		$view = new ShipParcelView($model);
		$send_status = $view->get_send_status();
		if($send_status != 1){//非 未发货
			$result['error'] = "改包裹已发货！";
			Util::jsonExit($result);
		}
		$model->setValue('create_time',date('Y-m-d H:i:s'));
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}

}?>