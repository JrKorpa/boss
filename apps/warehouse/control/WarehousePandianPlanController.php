<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehousePandianPlanController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-16 15:10:46
 *   @update	:
 *  -------------------------------------------------
 */
class WarehousePandianPlanController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('downCsv','download','printBill');
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }		
		$dd = new DictModel(1);
		$status = $dd->getEnumArray ('warehouse.pandian_plan');
		$bill_status = array();
		foreach ($status as $key => $value) {
			$bill_status[$value['name']] = $value['label'];
		}
		$this->render('warehouse_pandian_plan_search_form.html',array(
			'bar'=>Auth::getBar(),
			'bill_status'=> $bill_status,
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
			'id' => _Request::get("id"),
			'type' => _Request::get("type"),
			'status' => _Request::get("status"),
			'opt_admin' => _Request::get("opt_admin"),
			'verify_admin' => _Request::get("verify_admin"),
			'create_time_start' => _Request::get("create_time_start"),
			'create_time_end' => _Request::get("create_time_end"),
			'start_time_start' => _Request::get("start_time_start"),
			'start_time_end' => _Request::get("start_time_end"),
		);

		$page = _Request::getInt("page",1);
		$where = array(
			'id' => $args['id'],
			'type' => $args['type'],
			'status' => $args['status'],
			'opt_admin' => $args['opt_admin'],
			'verify_admin' => $args['verify_admin'],
			'create_time_start' => $args['create_time_start'],
			'create_time_end' => $args['create_time_end'],
			'start_time_start' => $args['start_time_start'],
			'start_time_end' => $args['start_time_end'],
		);

		$model = new WarehousePandianPlanModel(21);
		$data = $model->pandianList($where,$page,10,false);
		foreach ($data['data'] as $k=>$v){
			$pandianActionList=$model->getPandianActionList($v['id']);
			$yuguNum=$model->getYuguNum($v['id']);
			$lock_guiwei='';
			if(!empty($pandianActionList)){
				foreach ($pandianActionList as $r){
					$lock_guiwei.=$r['lock_guiwei']."(".$r['user_name'].")、";
				}
				$lock_guiwei=rtrim($lock_guiwei,' 、');
			}
			$data['data'][$k]['lock_guiwei']=$lock_guiwei;
			$data['data'][$k]['yuguNum']=$yuguNum;
		}
		
		
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_pandian_plan_search_page';
		$this->render('warehouse_pandian_plan_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'dd' => new DictModel(1),
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_pandian_plan_info.html',array(
			'view'=>new WarehousePandianPlanView(new WarehousePandianPlanModel(21))
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	盘点开始
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]); 	//单据ID
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
	    $model=new WarehousePandianPlanModel(21);
	    $num=$model->getPandianNum($id);
	   
	     $re=$model->getPandianActionGuiwei($id);
	      if($re['error']==1){
	      	$is_last=1;
	      }else{
	      	$is_last=0;
	      }
	      $guiwei=$re['guiwei'];
	    $yuguNum=$model->getYuguNum($id);
	   // print_r($yuguNum);
	   
	    
		$this->render('warehouse_pandian_plan_info_start.html',array(
			'view'=>new WarehousePandianPlanView(new WarehousePandianPlanModel($id,21)),
			'tab_id'=>$tab_id,
			'dd' => new DictModel(1),
			'userName'=>$_SESSION['userName'],
			'num' => $num,	
			'guiwei' => $guiwei,	
			'yuguNum'=>$yuguNum	,
		    'is_last'=>$is_last,
		));
	}

	/**
	 *	insert，信息入库 新建盘点单
	 * @type = 抽验仓 ,2 线下是带F开头的柜位号
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$type = $params['type'];
		$info = $params['info'];
		if($type == ''){
			$result['error'] = '请选择抽验仓';
			Util::jsonExit($result);
		}

		$newmodel =  new WarehousePandianPlanModel(22);
		 $res = $newmodel->CreateNewPanWeek($type , $info);
		// $result['error'] = $res;
		// Util::jsonExit($result);
		if($res['success'] != false)
		{
			$result['error'] = '添加周盘点单成功';
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}


	/**
	 *	审核盘点单
	 */
	public function checkPandian ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new WarehousePandianPlanModel(22);
		$res = $model->checkPandian($id);

		if($res['success'] != false){
			$result['success'] = 1;
			$result['error'] = $res['error'];
		}else{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}


	/**
	 *	取消盘点单
	 */
	public function closePandian ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new WarehousePandianPlanModel(22);
		$res = $model->closePandian($id);

		if($res['success'] != false){
			$result['success'] = 1;
			$result['error'] = $res['error'];
		}else{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}

	//盘点货品
	public function CreatePandianGoods($params)
	{
		$result = array('success' => 0, 'error' => '', 'affirm' => 0);
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = $params['id'];		//单据ID
		$goods_id = trim($params['goods_id']);
		$box_sn = $params['box_sn'];
        $affirm = $params['affirm'];
        //var_dump($params);die;
		//检测货号是否存在
		$goodsModel = new WarehouseGoodsModel(21);
		$exsis = $goodsModel->select2(' `id`,`is_on_sale` ', $where = " `goods_id` = '{$goods_id}' " , $is_all = 2);
		if(empty($exsis)){
			$result['error'] = "仓库中没有查到货号：<span style='color:red'>{$goods_id}</span>";
			Util::jsonExit($result);
		}
        if(!in_array($exsis['is_on_sale'],array(2,4)) && $affirm != 1){
            $result['affirm'] = 1;
            $result['error'] = "是否记入盘点明细，否则重新扫描货号（默认否）";
            Util::jsonExit($result);
        }
		$model = new WarehousePandianPlanModel(22);
		$result = $model->PandianGoods($goods_id , $box_sn , $id);
		Util::jsonExit($result);
	}

	//切换柜位
	public function qieBox($params){
		$id = $params['id'];	//单据id
		$box_sn = $params['box_sn']; 	//已经盘完的柜位
		$model = new WarehousePandianPlanModel(22);
		/*
		$status = $model->GetStatusBill($id);
		if($status != 1){
			$result['error'] = '当前单据的状态不是 “盘点中”，切换失败';
			Util::jsonExit($result);
		}
		*/
		$res = $model->qieBox($id, $box_sn);
		if($res['success'])
		{
			$result['success'] = 1;
			$result['error'] = $res['error'];
		}
		else
		{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}

	//周盘点单列表导出结果
 	public function downCsv($params){
 		$id = $params['id'];
 		$dd = new DictModel(1);

 		$model = new WarehousePandianPlanModel(21);
 		$box_model = new WarehouseBoxModel(21);
 		$data = $model->showData($id);
 		// echo '<pre>';print_r($data);echo '</pre>';die;
 		$title = array('柜位', '货号','货品状态', '款号' , '名称' , '制单人' , '制单时间' ,'盘点人','盘点时间', '主成色重' , '主石粒数' , '主石重' , '副石粒数' , '副石重' , '盘点情况' , '现在成本' , '盘盈货品应在柜位' , '盘盈货品实际仓库' , '盘盈货品实际状态' ,'导出时间' );
		if (!empty($data)){
			$DictModel=new DictView(new DictModel(1));
			foreach($data as $k=>$v)
			{
				
				//$v['panyingcang'] = '';
				if(!empty($v['old_guiwei']&&$v['panyingcang']=='')){
					//$v['panyingcang'] = $box_model->getWarehouseInfo($fields = ' name ', $where = " `a`.`box_sn` = '{$v['old_guiwei']}' ");
					$v['panyingcang'] = $box_model->getWarehouseInfoBybox_goods($v['goods_id'],$v['old_guiwei']);
				}
				$val = array(
					$v['guiwei'],$v['goods_id'],$DictModel->getEnum('warehouse.goods_status',$v['goods_status']), $v['goods_sn'] , $v['goods_name'] , $v['opt_admin'] , $v['opt_date'],$v['user_name'],$v['time'],
					$v['jinzhong'], $v['zhushilishu'] , $v['zuanshidaxiao'] , $v['fushilishu'] , $v['fushizhong'] , $dd->getEnum('warehouse.goods_pandian', $v['status']) ,
					$v['mingyichengben'] , $v['old_guiwei'] , $v['panyingcang'] , $v['status']
				);
				if($k == 0){
					array_push($val, date('Y-m-d H:i:s'));
				}
				$val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
				$content[] = $val;
			}
		}else{
			$val = array('没有记录');
			$val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
			$content[] = $val;
		}
		$model->detail_csv('周盘点明细',$title,$content);
 	}



 	/**
	 *	indexType1，查询货品明细列表
	 */
	public function indexType1($params)
	{
		//判断当前查询的是 货品明细 还是 单据
		if(isset($params['search_type'])){
			$_SESSION['search_type'] = $params['search_type'];
		}else{
			$_SESSION['search_type'] = !empty($_SESSION['search_type']) ? $_SESSION['search_type'] :1;
		}
		$dd = new DictModel(1);
		$status = $dd->getEnumArray ('warehouse.pandian_plan');
		$bill_status = array();
		foreach ($status as $key => $value) {
			$bill_status[$value['name']] = $value['label'];
		}
		$this->render('pandian_plan_search_goods_form.html',array(
			'bill_status'=> $bill_status,
			));
	}


 	/**
	 *	search，查询货品明细列表
	 */
	public function searchType1($params)
	{
		//判断当前查询的是 货品明细 还是 单据
		if(isset($params['search_type'])){
			$_SESSION['search_type'] = $params['search_type'];
		}else{
			$_SESSION['search_type'] = !empty($_SESSION['search_type']) ? $_SESSION['search_type'] :1;
		}
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'goods_id' => _Request::get("goods_id"),
			'id' => _Request::get("id"),
			'type' => _Request::get("type"),
			'status' => _Request::get("status"),
			'opt_admin' => _Request::get("opt_admin"),
			'verify_admin' => _Request::get("verify_admin"),
			'create_time_start' => _Request::get("create_time_start"),
			'create_time_end' => _Request::get("create_time_end"),
			'start_time_start' => _Request::get("start_time_start"),
			'start_time_end' => _Request::get("start_time_end"),
		);
		$page = _Request::getInt("page",1);
		$where = array(
			'goods_id' => $args['goods_id'],
			'id' => $args['id'],
			'type' => $args['type'],
			'status' => $args['status'],
			'opt_admin' => $args['opt_admin'],
			'verify_admin' => $args['verify_admin'],
			'create_time_start' => $args['create_time_start'],
			'create_time_end' => $args['create_time_end'],
			'start_time_start' => $args['start_time_start'],
			'start_time_end' => $args['start_time_end'],
		);

		$model = new WarehousePandianPlanModel(21);

		if($_SESSION['search_type'] == 1){
			$data = $model->pageList($where, $page, 10, false);
			$jsFuncs = 'pandian_plan_search_goods_page';
			$html = 'pandian_plan_search_goods_list.html';
		}
		if($_SESSION['search_type'] == 2){
			$data = $model->pageList2($where, $page, 10, false);
			$jsFuncs = 'pandian_plan_search_goods_page';
			$html = 'pandian_plan_search_bill_list.html';
		}

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = $jsFuncs;
		$this->render($html ,array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'dd' => new DictModel(1),
		));
	}

	//周盘点查询 列表导出结果
 	public function download($params){
 		$dd = new DictModel(1);
 		$model = new WarehousePandianPlanModel(21);
 		$box_model = new WarehouseBoxModel(21);

 		$where = array(
 			'goods_id' => $params['goods_id'],
 			'id' => $params['id'],
 			'opt_admin' => $params['opt_admin'],
 			'verify_admin' => $params['verify_admin'],
 			'create_time_start' => $params['create_time_start'],
 			'create_time_end' => $params['create_time_end'],
 			'start_time_start' => $params['start_time_start'],
 			'start_time_end' => $params['start_time_end'],
 			'type' => $params['type'],
 			'status' => $params['status'],
 		);
 		if($params['search_type'] == 1){
 			$data = $model->pageList ($where,$page = 1,$pageSize=1,$useCache=true , $down = true);
 			$title = array('单号', '抽检仓', '货号' , '款号' , '货品名称' , '成本价' , '制单时间' , '审核时间' , '状态');
 			$file_name = '周盘点货品明细'.time();
 		}else if($params['search_type'] == 2){
	 		$arr = $model->pageList2($where,$page = 1,$pageSize=1,$useCache=true , $down = true);
	 		$data = $arr['data'];
 			$title = array('单号', '抽检仓', '应盘数量' , '应盘金额' , '实盘数量' , '实盘金额' , '正常数量' , '正常金额' , '盘盈数量' , '盘盈金额' , '盘亏数量' , '盘亏金额' , '错误率' , '制单人' , '制单时间' , '审核人' ,'审核时间', '状态' );
 			$file_name = '周盘点单据明细'.time();
 		}
		if (!empty($data)){
			foreach($data as $k=>$v)
			{
				if($params['search_type'] == 1)
				{
					$v['type'] = $v['type'] == 1 ? '线上' : '线下' ;
					$v['status'] = $dd->getEnum('warehouse.goods_pandian' , $v['status']);
					$val = array(
						$v['id'],$v['type'], $v['goods_id'] , $v['goods_sn'] , $v['goods_name'] , $v['price'],
						$v['opt_date'], $v['verify_date'] , $v['status']
					);
				}
				else if($params['search_type'] == 2)
				{
					$v['type'] = $v['type'] == 1 ? '线上' : '线下' ;
					$val = array(
						$v['id'],$v['type'], $v['all_num'] , $v['all_price'] , $v['real_num'] , $v['real_price'],
						$v['nomal_num'], $v['nomal_price'] , $v['overage_num'] , $v['overage_price'] , $v['loss_num'] , $v['loss_price'],
						$v['error_rate'] , $v['opt_admin'] , $v['opt_date']  , $v['verify_admin'] , $v['verify_date'] , $dd->getEnum('warehouse.pandian_plan', $v['status'])
					);
				}

				$val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
				$content[] = $val;
			}
		}else{
			$val = array('没有记录');
			$val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
			$content[] = $val;
		}
		$model->detail_csv($file_name,$title,$content);
 	}
 	//打印盘盈盘亏报告单
 	public function printBill(){
 		
 		$id = _Request::get('id');  //盘点单
 		
 		$dd = new DictView(new DictModel(1));
 		$status = $dd->getEnumArray ('warehouse.goods_pandian');
 		$bill_status = array();
 		foreach ($status as $key => $value) {
 			$bill_status[$value['name']] = $value['label'];
 		}
 		$goods_status = $dd->getEnumArray ('warehouse.goods_status');
 		$bill_goods_status = array();
 		foreach ($goods_status as $ks => $v) {
 			$bill_goods_status[$v['name']] = $v['label'];
 		}
 		$model = new WarehousePandianPlanModel($id,21);
 		$view=new WarehousePandianPlanView($model);
 		$pkArr=$model->getGoodsNumAndPrice(array('plan_id'=>$id,'status'=>'2'));		
 		$pyArr=$model->getGoodsNumAndPrice(array('plan_id'=>$id,'status'=>'3'));
 		
 		$goods_list=$model->getGoodsList($id);
 		foreach ($goods_list as $k=>$v){
 			if($v['status']==2){
 				$goods_list[$k]['guiwei']='';
 				$goods_list[$k]['old_guiwei']=$v['guiwei'];
 			}
 			$goods_list[$k]['status']=$bill_status[$v['status']];
 			$goods_list[$k]['goods_status']=$bill_goods_status[$v['goods_status']];
 		}

 		$this->render('bath_print_bill.html', array(			
 			'view'=>$view,
 			'pkArr'=>$pkArr,
 			'pyArr'=>$pyArr,			
 			'goods_list'=>$goods_list		
 				
 		));
 		
 		
 		
 	}
 	
 	
}?>