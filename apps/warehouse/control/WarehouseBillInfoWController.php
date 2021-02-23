<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoWController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-18 18:49:32
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoWController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('downCsv', 'downGuiweiCsv');

	/**
	 *	add，渲染添加页面
	 */
	public function add ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
        	die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }		
		//检测是否有最新一个，保存状态的盘点单
		$model = new WarehouseBillInfoWModel(21);
		$row = $model->GetLastPandian();
		if(empty($row)){
			//初始化数据
			$row['id'] = $row['bill_status'] = $row['create_user'] = $row['goods_num'] = $row['chengbenjia'] = $row['bill_note'] = $row['create_time'] = $row['bill_no'] = $row['to_warehouse_name'] = $row['status'] = '';
		}

        
		//获取仓库列表
		$warehouseObj = new WarehouseModel(21);
		$arr = $warehouseObj->select3(array('is_delete'=> 1),array('id', 'name' , 'code'));
		$warehouse = array();
		foreach ($arr as $key => $val) {
			$warehouse[$val['id']] = $val['code']. ' | ' .$val['name'];
		}

		$result = array('success' => 0,'error' => '');
		$this->render('warehouse_bill_info_w_info.html',array(
			'view'=>new WarehouseBillInfoWView(new WarehouseBillInfoWModel(21)),
			'warehouse' => $warehouse,
			'row' => $row,
			'dd' => new DictModel(1),
		));
	}


	/**
	 *	生成盘点单
	 */
	public function CreatePandian ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$warehouse_id = $params['warehouse'];
		$bill_note = $params['bill_note'];
		if(!$warehouse_id){
			$result['error'] = '请选择要盘点的仓库';
			$result['bill_id'] = 0;
			Util::jsonExit($result);
		}

		$newmodel =  new WarehouseBillInfoWModel(22);
		$warehouseModel = new WarehouseModel($warehouse_id , 21);

		/** 判断仓库是否被锁定 **/
		$lock = $warehouseModel->getValue('lock');
		if($lock == 1){
			//获取当前仓库未审核的盘点单
			$bill_id = $newmodel->GetPandianByWarehouse($warehouse_id);

			$result['error'] = '当前仓库已经在盘点';
			$result['bill_id'] = $bill_id;
			Util::jsonExit($result);
		}

		$res = $newmodel->createPandian($warehouse_id, $bill_note);

		if($res['success'] != false)
		{
			$result['success'] = 1;
			$result['error'] = $res['error'];
			$result['bill_id'] = $res['bill_id'];
		}
		else
		{
			$result['error'] = $res['error'];
			$result['bill_id'] = 0;
		}
		Util::jsonExit($result);
	}

	public function edit($params){
		$params['bill_id'] = $params['id'];
		$this->ShowBoxPandian($params);
	}

	/**
	* 总公司盘点页面，带柜位输入框
	*/
	public function ShowBoxPandian($params){
		$bill_id = $params['bill_id'];
		$model = new WarehouseBillInfoWModel(21);
		$bill_info = $model->GetBillWinfo($bill_id);
		$status = $model->GetBillEnd($bill_id);	//获取是否点击的 “盘点完成” 的操作
		//获取当前盘点的货品数 （明细中不是盘亏的）
		$row = $model->GetPaningNum($bill_id);
		//获取当前登录用户盘点的货品数 （明细中不是盘亏的）
		$row1 = $model->GetPaningNumByUser($bill_id,$_SESSION['userName']);
		//获取单据盘点仓所属的公司
		$model = new WarehouseRelModel(21);
		$companyID = $model->GetCompanyByWarehouseId($bill_info['to_warehouse_id']);
		$is_fengonsi = 0;
		if($companyID != 58){
			#如果不是总公司的单据
			$is_fengonsi = 1;
		}

        $groupUser=new GroupUserModel(1);
        //批量盘点组id=8 用户
        $userlist= $groupUser->getGroupUser(8);
        $can_pl_pandian=in_array($_SESSION['userId'], array_column($userlist,'user_id')) ? 'YES':'NO';
        if($_SESSION['userType']==1)
            $can_pl_pandian='YES';

        $this->render('warehouse_bill_info_w_box.html',array(
			'row' => count($row),
			'count1' => count($row1),
			'info'=>$bill_info,
			'dd' => new DictModel(1),
			'status'=>$status,
			'realName'=>$_SESSION['realName'],
			'can_pl_pandian' => $can_pl_pandian,
			'is_fengonsi' => $is_fengonsi, //标示符 ： 区别总公司 与 分公司的盘点单
			'view' => new WarehouseBillView(new WarehouseBillModel($bill_id , 21)),
		));
	}

	/**
	* 提交 盘点
	*/
	public function GetBoxPandian($params){
		$result = array('success' => 0,'error' =>'', 'affirm' =>0);
		$Boxmodel = new WarehouseBoxModel(21);
		$model = new WarehouseBillInfoWModel(22);
		
		$warehouse_id = $params['warehouse_id'];
        $affirm = $params['affirm'];
		//********************** 提交货号盘点 ************************/
		if(isset($params['goods_id'])){
			$bill_id = $params['bill_id'];
			$goods_id = trim($params['goods_id']);
			$box_sn = trim($params['box_sn']);

            $groupUser=new GroupUserModel(1);
            //批量盘点组id=8 用户
            $userlist= $groupUser->getGroupUser(8);
            if(in_array($_SESSION['userId'], array_column($userlist,'user_id')) ||$_SESSION['userType']==1){
					$goods_list = explode(" ",$goods_id);
					if (empty($goods_id) || empty($goods_list) || count($goods_list)==0 )   Util::jsonExit(array('success' => 0, 'error' => '请输入要盘点的货号', 'affirm' => 0));
					if (count($goods_list) > 300) Util::jsonExit(array('success' => 0, 'error' => '批量不能超过300个货号', 'affirm' => 0));
					$error = "";
					$affirm = "";
					foreach ($goods_list as $goods_id) {
						if (empty($goods_id)) continue;
						$res = $model->GetGoodsPandian($goods_id, $bill_id, $box_sn, $affirm);
						$error = $error . $res['error'] . "<br>";
						$affirm = $affirm . $res['affirm'] . "<br>";
					}
					$result['error'] = $error;
					$result['affirm'] = $affirm;
					$result['success'] = 1;
					$result['is_goods'] = 1;
					//获取当前盘点的货品数 （明细中不是盘亏的）
					$row = $model->GetPaningNum($bill_id);
					$result['row'] = count($row);
					$row1 = $model->GetPaningNumByUser($bill_id, $_SESSION['userName']);
					$result['count1'] = count($row1);
			}else{
                $res = $model->GetGoodsPandian($goods_id , $bill_id, $box_sn, $affirm);
                if($res['success'] != false){
                    $result['success'] = 1;
                    $result['error'] = $res['error'];
                    $result['affirm'] = $res['affirm'];
                    $result['is_goods'] = 1;
                    //获取当前盘点的货品数 （明细中不是盘亏的）
                    $row = $model->GetPaningNum($bill_id);
                    $result['row'] = count($row);
                    $row1=$model->GetPaningNumByUser($bill_id,$_SESSION['userName']);
                    $result['count1'] = count($row1);
                }else{
                    $result['error'] = $res['error'];
                    $result['affirm'] = $res['affirm'];
                }
			}



			Util::jsonExit($result);
			exit(); 	//提交货号盘点结束
		}

		/********************** 以下代码是盘点切换柜位代码 **********************/
		$box_sn = trim($params['box_sn']);
		$bill_id = $params['bill_id'];
		if($box_sn == ''){
			$result['error'] = '请输入柜位号';
			Util::jsonExit($result);
		}

		//检测柜位是否是当前仓库的柜位
		$exsis = $model->GetBoxId($box_sn, $bill_id);
		if(!$exsis){
			if($box_sn === '0-00-0-0'){
				$res1 = $model->CreateBox($warehouse_id);
				if(!$res1){
					$result['error'] = '自动生成默认柜位失败...';
					Util::jsonExit($result);
				}
			}else{
				$result['error'] = "您输入的柜位 <san style='color:red;'>{$box_sn}</span> 不属于当前仓库的柜位";
				Util::jsonExit($result);
			}
		}
		//检测提交过来的柜位号是否合法
		$hf_box = $Boxmodel->checkBoxRigth($box_sn);
		if(!$hf_box){
			$result['error'] = '您输入的柜位"不存在" 或 "已被禁用"';
			Util::jsonExit($result);
		}
		$res = $model->LockBox($box_sn , $bill_id, $warehouse_id);
		$billModel = new WarehouseBillModel($bill_id ,21);
		if($res['success'] != false){
			$result['success'] = 1;
			$result['error'] = $res['error'];
			$result['is_goods'] = 0;
			$result['goods_num'] = $billModel->getValue('goods_num');
		}else{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}

	/**
	* 切换柜位
	*/
	public function qieBox($params){
		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['bill_id'];
		$model = new WarehouseBillInfoWModel(22);
		$res = $model->qieBox($bill_id);
		if($res['success'] == 1){
			$result['success'] = 1;
			$result['error'] = '切换成功';
		}else{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}

	/**
	* 盘点完成 按钮
 	*/
 	public function OffPandian($params){
 		$result = array('success' => 0,'error' =>'');
		$bill_id = $params['bill_id'];
		$bill_note = $params['bill_note'];
		$model = new WarehouseBillInfoWModel(22);
		$res = $model->OffPandian($bill_id , $bill_note);
		if($res['success'] == 1){
			$result['success'] = 1;
			$result['error'] = $res['error'];
		}else{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
 	}

 	//盘点单详情
 	public function show($params){
 		$bill_id = $params['id'];
 		$model = new WarehouseBillInfoWModel(21);
 		$bill_info = $model->GetBillWinfo($bill_id);
 		
 		//获取取单据取消时的操作用户和操作时间
 		$WarehouseBillModel = new WarehouseBillModel(21);
 		$billcloseArr=$WarehouseBillModel->get_bill_close_status($bill_id);
 		//print_r($billcloseArr);exit();
 		$this->render('warehouse_bill_info_w_show.html',array(
			'view'=>new WarehouseBillInfoWView(new WarehouseBillInfoWModel(21)),
			'dd' => new DictModel(1),
			'info'=>$bill_info,
 			'billcloseArr'=>$billcloseArr,
		));
 	}

 	//盘点详情
 	public function BillDetail($params){
 		$bill_id = $params['bill_id'];
 		//获取盘点仓库
 		$billModel = new WarehouseBillModel($bill_id , 21);
 		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			'bill_id'	=>$bill_id
		);

		$model = new WarehouseBillGoodsModel(21);

		$where = array('bill_id'=>$bill_id);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'pandian_list';
		
		
		$billModel = new WarehouseBillModel($bill_id , 21);
 		$to_warehouse_id = $billModel->getValue('to_warehouse_id');
		
		if (!empty($data["data"]))
		{
			foreach($data["data"] as $k=>$v)
			{
			// 1221 	正常 	4 		
			// 1220 	盘盈 	3 		
			// 1219 	盘亏 	2 		
			// 1218 	无盘点状态 	1
			//如果货品所在的仓库和正在盘点的仓库是同一个仓库，只是柜位不相同，不用显示盘盈，改为【正常】
			if(($v['warehouse_id'] == $to_warehouse_id) && ($v['pandian_status'] == 3))
				{
					//如果不限制是盘盈状态的，那么盘亏的也会变正常
					$data["data"][$k]['pandian_status'] = 4;
				}
			}
		}

		$this->render('warehouse_bill_goods.html',array(
			'pa' =>Util::page($pageData),
			'dd' => new DictView(new DictModel(1)),
			'data' => $data,
			'to_warehouse_name' =>$billModel->getValue('to_warehouse_name'),
			'view' => new WarehouseBillInfoWView(new WarehouseBillInfoWModel(21)),
		));
 	}

 	//审核
 	public function checkPandian($params){
 		$result = array('success' => 0,'error' =>'');
 		$bill_id = $params['bill_id'];
 		$model = new WarehouseBillInfoWModel(22);
 		$create_user = $model->getValue('create_user');
		if($create_user == $_SESSION['userName']){
		  $result['error'] = '不能审核自己的单据';
		  Util::jsonExit($result);
		}		
		$res = $model->checkBill($bill_id);
		if($res['success'] == 1){
			$result['success'] = 1;
			$result['error'] = $res['error'];
		}else{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
 	}

 	//取消单据
 	public function closePandian($params){
 		$result = array('success' => 0,'error' =>'');
 		$bill_id = $params['bill_id'];
 		$model = new WarehouseBillInfoWModel(21);
 		$res = $model->closePandian($bill_id);
 		if($res['success'] == 1){
			$result['success'] = 1;
			$result['error'] = $res['error'];
		}else{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
 	}

 	/** 切换盘点单 **/
 	public function qiePandian($params){
 		$result = array('success' => 0,'error' =>'');
 		$now = $params['now'];
 		$type = $params['type'];
 		$dd = new DictView(new DictModel(1));
		$model = new WarehouseBillInfoWModel(21);
		$row = $model->GetLastPandian($now, $type);

		if(!empty($row)){
	 		$result['success'] = 1;
			$result['bill_no'] = $row['bill_no'];
			$result['create_time'] = $row['create_time'];
			$result['bill_status'] = $dd->getEnum('warehouse.bill_status' , $row['bill_status']);
			$result['create_user'] = $row['create_user'];
			$result['goods_num'] = $row['goods_num'];
			$result['chengbenjia'] = $row['chengbenjia'];
			$result['to_warehouse_id'] = $row['to_warehouse_id'];
			$result['to_warehouse_name'] = $row['to_warehouse_name'];
			$result['bill_note'] = $row['bill_note'];
			$result['id'] = $row['id'];
			$result['status'] = $row['status'];
		}else{
			$result['success'] =0;
			$result['error'] = '已经是最后一条信息';
		}
 		Util::jsonExit($result);
 	}

 	//导出结果
 	public function downCsv($params){
		set_time_limit(0);
 		$bill_id = $params['bill_id'];
 		$dd = new DictModel(1);

		// $dd =  new DictView(new DictModel(1));
		
 		$billModel = new WarehouseBillModel($bill_id , 21);
 		$create_user = $billModel->getValue('create_user');
 		$to_warehouse_id = $billModel->getValue('to_warehouse_id');


 		$model = new WarehouseBillInfoWModel(21);
 		$title = array('货号','款号','名称','盘点人','盘点时间','成本价','材质','金重','金耗','石重','净度','颜色','证书号','盘点柜位','实际柜位','柜位情况','盘点情况','货品实际仓库','货品状态');
 		$data = $model->PrintInfo($bill_id);
 		// echo '<pre>';print_r($data);echo '</pre>';die;

		if (!empty($data)){
			foreach($data as $k=>$v)
			{
				$guiweiInfo = $v['pandian_status'] == 4 ? '正常' : '错误';
			// 1221 	正常 	4 		
			// 1220 	盘盈 	3 		
			// 1219 	盘亏 	2 		
			// 1218 	无盘点状态 	1
				//如果货品所在的仓库和正在盘点的仓库是同一个仓库，只是柜位不相同，不用显示盘盈，改为【正常】，显示结果如下“货号：150550358476 盘点正常 柜位错误 实际仓库：### 实际柜位：###”，且导出结果的盘点明细中，【盘点情况】改为【正常】，【柜位情况】改为【错误】，盘点明细中增加一列【盘点仓库】
				$pandian_qk = '--';
				if(($v['warehouse_id'] == $to_warehouse_id) && ($v['pandian_status'] == 3)){		//如果不限制是盘盈状态的，那么盘亏的也会变正常
					$pandian_qk = '正常';
				}else{
					$pandian_qk = $dd->getEnum('warehouse.goods_pandian' , $v['pandian_status']);
					
				}
				
				$gid = $v['goods_id'];					
				$WarehouseGoods = new WarehouseGoodsModel(21);
				$ret = $WarehouseGoods->GetGoodsbyGoodid($gid);
				//盘盈的时候成本价重新获取：从商品列表带出-2015-10-28
				if($v['pandian_status'] == 3)
				{
					$v['mingyichengben'] = empty($ret["mingyichengben"])?$ret["chengbenjia"]:$ret["mingyichengben"];
				}
				/* $company_id = $ret['company_id'];
				if(SYS_SCOPE=="zhanting" && !in_array($company_id,array(58,515))){
					//jingxiaoshangchengbenjia 已包含管理费
					$v['mingyichengben'] = $ret['jingxiaoshangchengbenjia'];
				}
 */
				$isos = $v['is_on_sale'];
				$goods_status = $dd->getEnum('warehouse.goods_status',$isos);

					
				$val = array(
					$v['goods_id'],$v['goods_sn'],$v['goods_name'],	//'货号','款号','名称'
					$v['pandian_user'] , $v['addtime'] = ($v['addtime'] !== '0000-00-00 00:00:00') ? $v['addtime']."\t":'' , $v['mingyichengben'],	//'盘点人','盘点时间','成本价'
					$v['caizhi'],$v['jinzhong'],$v['jinhao'],			//'材质','金重','金耗'
                    $v['zuanshidaxiao'],$v['jingdu'], //'石重','净度'
					$v['yanse'], $v['zhengshuhao'], $v['pandian_guiwei'] ,				//'颜色','证书号','盘点柜位'
					$v['guiwei'] , $guiweiInfo , $pandian_qk,	//'实际柜位','柜位情况'，'盘点情况'
					$v['warehouse_name'],		//,'盘盈货品实际仓库'
					$goods_status		//,'货品状态'
				);
				$val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
				$content[] = $val;
			}
		}else{
			$val = array('没有记录');
			$val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
			$content[] = $val;
		}
		$model->detail_csv('盘点明细',$title,$content);
 	}


 	/**导出柜位结果**/
 	public function downGuiweiCsv($params){
 		$bill_id = $params['bill_id'];
 		$model = new WarehouseBillInfoWModel(21);
 		$title = array('盘点柜位','货品数量','盘点数量');
 		$data = $model->PrintInfoToGuiwei($bill_id);
 		// echo '<pre>';print_r($data);echo '</pre>';die;
 		$val = array();
		if (!empty($data)){
			if(!empty($data['guiwei']) && !empty($data['pandian_guiwei'])){
				foreach($data['pandian_guiwei'] as $pgw){
					$pdgw = $pgw['pandian_guiwei'];
					$pdgwc = $pgw['cnt'];
					$pandian_guiwei_array[$pdgw]=$pdgwc;
				}
				foreach($data['guiwei'] as $gw)
				{
					$pgw = $gw['guiwei'];
					$pgwc = $gw['cnt'];
					$val = array(
						$pgw , $pgwc ,  ($pdgw === $pgw) ? $pandian_guiwei_array[$pdgw] : 0
					);
				$val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
				$content[] = $val;
				}
			}else{
				$val = array('没有记录');
				$val = eval('return '.iconv('utf-8','gbk',var_export($val,true).';')) ;
				$content[] = $val;
			}
		}
		$model->detail_csv('盘点柜位明细',$title,$content);
 	}

 	//ajax更改盘点单备注
 	public function InsertBillNote($params){
 		$bill_id = $params['bill_id'];
 		$bill_note = $params['bill_note'];
 		$model = new WarehouseBillModel($bill_id , 22);
 		$model->setValue('bill_note',$bill_note);
 		$model->save();
 	}


}?>
