<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseCaigouTiaozhengController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 16:57:02
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseCaigouTiaozhengController extends CommonController
{
	protected $smartyDebugEnabled = true;
	protected $whitelist = array('getTemplate','downloadCSV','dow');
	protected $goods_status =array('1'=>'收货中','2'=>'库存','3'=>'已销售','4'=>'盘点中','5'=>'调拨中','6'=>'损益中','7'=>'已报损','8'=>'返厂中','9'=>'已返厂','10'=>'销售中','11'=>'退货中','12'=>'作废');
	protected $goods_type =array('1'=>'裸石','2'=>'成品','3'=>'其他');
	/**
	*货品类型  测试数据
	**/
	public function  huopintype()
	{
		return  array(1=>'裸石',2=>'成品',3=>'其他');
	}

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('warehouse_caigou_tiaozheng','warehouse_shipping',21);	//生成模型后请注释该行
		//Util::V('warehouse_caigou_tiaozheng',21);	//生成视图后请注释该行
		$proModel = new ApiProModel();
		$supplierList = $proModel->GetSupplierList(array('status'=>1));
		$this->render('warehouse_caigou_tiaozheng_search_form.html',array(
			'bar' => Auth::getBar(),
			'huopintype'=>$this->huopintype(),
			'supplierList'=>$supplierList,

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
			'goods_id' => _Request::get("goods_id"),
			'type'	   => _Request::get("type"),
			'shuoming' => _Request::get("shuoming"),
			'status'   => _Request::get("status"),
			'addname'  => _Request::get("addname"),
			'checkname'=> _Request::get("checkname"),
			'addtime_s'=> _Request::get("addtime_s"),
			'addtime_e'=> _Request::get("addtime_e"),
			'checktime_s'=> _Request::get("checktime_s"),
			'checktime_e'=> _Request::get("checktime_e"),
		    'supplier_id'=> _Request::get("supplier_id"),
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array(
			'goods_id'		=> $args['goods_id'],
			'type'		    => $args['type'],
			'shuoming'		=> $args['shuoming'],
			'status'		=> $args['status'],
			'addname'		=> $args['addname'],
			'checkname'		=> $args['checkname'],
			'addtime_s'		=> $args['addtime_s'],
			'addtime_e'		=> $args['addtime_e'],
			'checktime_s'	=> $args['checktime_s'],
			'checktime_e'	=> $args['checktime_e'],
		    'supplier_id'   => $args["supplier_id"],
			);
        if(SYS_SCOPE == 'zhanting'){
            $where['hidden'] = '0';
        }
		
		$proModel = new ApiProModel();
		$supplierList = $proModel->GetSupplierList();
		$supplierList = array_column($supplierList,"name",'id');
		
		$model = new WarehouseCaigouTiaozhengModel(21);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_caigou_tiaozheng_search_page';
		$this->render('warehouse_caigou_tiaozheng_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'huopintype'=>$this->huopintype(),
			'search_condition'=>json_encode($where),
		    'supplierList'=>$supplierList
		));
	}
	//导出excel
    public function downloadCSV(){
        $page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
        $where = array(
            'goods_id' => _Request::get("goods_id"),
            'type'	   => _Request::get("type"),
            'shuoming' => _Request::get("shuoming"),
            'status'   => _Request::get("status"),
            'addname'  => _Request::get("addname"),
            'checkname'=> _Request::get("checkname"),
            'addtime_s'=> _Request::get("addtime_s"),
            'addtime_e'=> _Request::get("addtime_e"),
            'checktime_s'=> _Request::get("checktime_s"),
            'checktime_e'=> _Request::get("checktime_e"),
            'supplier_id'=> _Request::get("supplier_id"),
        );
        if(SYS_SCOPE == 'zhanting'){
            $where['hidden'] = '0';
        }
        $proModel = new ApiProModel();
        $supplierList = $proModel->GetSupplierList();
        $supplierList = array_column($supplierList,"name",'id');
        
        $huopintype = $this->huopintype();
        
        $model = new WarehouseCaigouTiaozhengModel(21);
        $datalist = $model->getList($where);
        $conf=array(
            array('field'=>'goods_id','title'=>'货号'),
            array('field'=>'goods_sn','title'=>'款号'),
            array('field'=>'huopintype','title'=>'货品类型'),
            array('field'=>'shizhong','title'=>'石重'),
            array('field'=>'yanse','title'=>'颜色'),
            array('field'=>'jingdu','title'=>'净度'),
            array('field'=>'qiegong','title'=>'切工'),
            array('field'=>'warehouse','title'=>'货品位置'),
            array('field'=>'supplier','title'=>'供应商'),
            array('field'=>'goods_status','title'=>'货品状态'),
            array('field'=>'yuanshichengbenjia','title'=>'原始采购成本'),
            array('field'=>'xianzaichengben_old','title'=>'改前采购成本'),
            array('field'=>'xianzaichengben_new','title'=>'改后采购成本'),
            array('field'=>'tiaozhengchajia','title'=>'调整差价'),
            array('field'=>'shuoming','title'=>'调价说明'),
            array('field'=>'addname','title'=>'制单人'),
            array('field'=>'addtime','title'=>'制单时间'),
            array('field'=>'checkname','title'=>'审核人'),
            array('field'=>'checktime','title'=>'审核时间'),
            array('field'=>'info','title'=>'备注'),
            array('field'=>'status','title'=>'状态'),
        );
        $data = array();
        $goods_status_list = $this->dd->getEnumArray("warehouse.goods_status");
        $goods_status_list = array_column($goods_status_list,"label",'name');
        $cgtiaozheng_status_list = $this->dd->getEnumArray("cgtiaozheng.status");
        $cgtiaozheng_status_list = array_column($goods_status_list,"label",'name');
        foreach($datalist as $key=>$vo){
            $data[$key]['goods_id'] = $vo['goods_id'];
            $data[$key]['goods_sn'] = $vo['goods_sn'];
            $data[$key]['huopintype'] = isset($huopintype[$vo['type']])?$huopintype[$vo['type']]:'';
            $data[$key]['shizhong'] = $vo['shizhong'];
            $data[$key]['yanse'] = $vo['yanse'];
            $data[$key]['jingdu'] = $vo['jingdu'];
            $data[$key]['qiegong'] = $vo['qiegong'];
            $data[$key]['warehouse'] = $vo['warehouse'];
            $data[$key]['supplier'] = isset($supplierList[$vo['supplier_id']])?$supplierList[$vo['supplier_id']]:'';;
            $data[$key]['goods_status'] = isset($goods_status_list[$vo['is_on_sale']])?$goods_status_list[$vo['is_on_sale']]:'';
            $data[$key]['yuanshichengbenjia'] = $vo['yuanshichengbenjia'];
            $data[$key]['xianzaichengben_old'] = $vo['xianzaichengben_old'];
            $data[$key]['xianzaichengben_new'] = $vo['xianzaichengben_new'];
            if($vo['xianzaichengben_new']-$vo['xianzaichengben_old']>0){
                $data[$key]['tiaozhengchajia'] = "+".($vo['xianzaichengben_new']-$vo['xianzaichengben_old']);
            }else{
                $data[$key]['tiaozhengchajia'] = "-".($vo['xianzaichengben_old']-$vo['xianzaichengben_new']);;
            }            
            $data[$key]['shuoming'] = $vo['shuoming'];
            $data[$key]['addname'] = $vo['addname'];
            $data[$key]['addtime'] = $vo['addtime'];
            $data[$key]['checkname'] = $vo['checkname'];
            $data[$key]['checktime'] = $vo['checktime'];
            $data[$key]['info'] = $vo['info'];
            $data[$key]['status'] = isset($cgtiaozheng_status_list[$vo['status']])?$cgtiaozheng_status_list[$vo['status']]:'';
         }
        unset($datalist);
        ob_clean();
        Util::downloadCsvNew('采购成本调整',$conf,$data);
    }
	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_caigou_tiaozheng_info.html',array(
			'view'=>new WarehouseCaigouTiaozhengView(new WarehouseCaigouTiaozhengModel(21)),
			'type'=>$this->huopintype(),
			'dd'  =>new DictView(new DictModel(1))

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
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_caigou_tiaozheng_info.html',array(
			'view'=>new WarehouseCaigouTiaozhengView(new WarehouseCaigouTiaozhengModel($id,21))
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}


	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result		= array('success' => 0,'error' =>'');
		$shuoming	= $_POST['shuoming'];
		$type		= $_POST['type'];
		//var_dump($_POST);exit;
		$model = new WarehouseCaigouTiaozhengModel(22);

		//判断附件是否上传
		if($_FILES['cgchengben_file']['error'])
		{
			$result['error'] = '上传文件格式为csv';
			Util::jsonExit($result);
		}

		if ($_FILES['cgchengben_file']['error'] == 0)
		{
			$file_array     = explode(".",$_FILES['cgchengben_file']['name']);
			$file_extension = strtolower(array_pop($file_array));
			if($file_extension != 'csv')
			{
				$result['error'] = '上传文件格式为csv';
				Util::jsonExit($result);
			}
			$file   = fopen($_FILES['cgchengben_file']['tmp_name'],"r");
			//检查数据格式是否正确
			$check_res = $this->check_data($file);
			if ($check_res['success'] != 1)
			{
				$result['error'] = $check_res['error'];
				Util::jsonExit($result);
			}
			//根据货号查询货品信息及检查货品状态
			$goods_ids  = $check_res['data']; //所有货号
			$goods_info = $check_res['info'];//取得所有上传信息
			$new_goods = array();
			$res = $model->get_goods_info($goods_ids); //查询的货品信息
			if ($res)
			{   
			    
				foreach ($res as $key=>$val)
				{   
				    
					if(!($val['put_in_type']==3 or $val['put_in_type']==4))
					{
						$result['error'] = $val['goods_id'].'货品状态错误，检查货品入库方式是否为代销、借入';
						Util::jsonExit($result);
					}					
					//查询供应商信息
					$res[$key]['supplier_id'] = 0;
					if(!empty($val['goods_sn'])){
					   $supplier = $model->getFactoryByStyleSn($val['goods_sn']);
					   if(!empty($supplier)){
					       $res[$key]['supplier_id'] = $supplier['factory_id'];
					       $goods_sn_list[$key] = $val['goods_sn'];					       
					   }
					}
					//$new_goods[] = $val['goods_id'];
				}
			}
			//var_dump($res);var_dump($goods_ids);exit;
			//匹对查询的货品数量与上传的货品是否相等
			/*   以前的判断条件 后期加上
			if (!goods_exist($goods_id,$type) )
			{
				notice($goods_id . "不存在， 检查货品类型是否正确", 0);
			}
			//检查货品入库方式必须是代销3、借入4  结价为未结价
			if (ruku_jiejia_check($goods_id,$type) == 2)
			{
				notice($goods_id . "货品状态错误，检查货品入库方式是否为代销、借入", 0);
			}
			if (ruku_jiejia_check($goods_id,$type) == 3)
			{
				notice($goods_id . " 货品状态错误，检查货品是否为未结价", 0);
			}
			*/
			//目前只是全部判断正确错误，未详细说明情况
			if (count($res) != count($goods_ids))
			{
				$result['error'] = '上传货号不存在或状态有误，请检查后上传';
				Util::jsonExit($result);
			}
		}
		//保存数据
		$res = $model->insert_num($res,$goods_info,$type,$shuoming);
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
		$id = _Post::getInt('id');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new WarehouseCaigouTiaozhengModel($id,2);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
		);

		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
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
		$model = new WarehouseCaigouTiaozhengModel($id,2);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
	/**
	*check_data,检查数据是否正确
	**/
	public function check_data($file)
	{
		$goods  = array();
		$i      = 0;
		$time   = date("Y-m-d H:i:s");
		$result = array('success' => 0,'error' =>'');
		$arr    = array();

		$model = new WarehouseCaigouTiaozhengModel(21);
		while(! feof($file))
		{
			$dia = fgetcsv($file);
			if ($i > 0)
			{
				if (trim($dia[0]) == '' && trim($dia[1]) == '' && trim($dia[2]) == '')
				{
					if($i == 1)
					{
						$result['error'] = '上传的附件数据不能为空！';
						Util::jsonExit($result);
					}else{
					    continue;
					}
				}
				else
				{
					$goods_id             = strtoupper(trim($dia[0])); //货号
					$xianzaichengben_new  = strtoupper(trim($dia[1])); //最新采购成本
					$info				  = trim($dia[2]); //备注

					if(empty($goods_id) || empty($xianzaichengben_new))
					{
						$result['error'] = $goods_id.'货号和最新采购成本成本为必填项';						return $result;
					}

					$z = "/^(([0-9]+\.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*\.[0-9]+)|([0-9]*[1-9][0-9]*))$/";
					if(!preg_match($z,$xianzaichengben_new))
					{
						$result['error'] = '采购成本只能为数字且不能为负数';
						return $result;
					}
					$goods_info = array(
						'goods_id'=> $goods_id,
						'xianzaichengben_new' =>$xianzaichengben_new,
						'info'=>$info
						);
					$goods[$goods_id]    = $goods_info;
					$arr[]      = $goods_id;
				}
			}
			$i++;
		}
		//检查货号是否正确
		$unique_arr = array_unique ( $arr );
		if (count($arr) != count($unique_arr))
		{
			$result['error'] = '货号重复，请检查后上传';
			return $result;
		}
		$result['info']     = $goods;
		$result['success']  = 1;
		$result['data']     = $arr;
		return $result;
	}
	/**
	*check,审核
	*/
	public function check()
	{
		$result = array('success' => 0,'error' => '');
		$ids = _Post::getList('_ids');
		$time = date("Y-m-d H:i:s");
		//循环选择操作数据
		$arr_goods = array();
		$arr_val   = array();
		foreach ($ids as $key=>$val)
		{
			//查询该条申请记录信息//修改货品价格
			$model    = new WarehouseCaigouTiaozhengModel($val,21);
			$goods_id = $model->getValue('goods_id'); //查询货号
			$xianzaichengben_new = $model->getValue('xianzaichengben_new');
			$re  = $model->update_chengben($goods_id,$xianzaichengben_new);
			//修改审核状态
			$model->setValue('status',2);
			$model->setValue('checkname',$_SESSION['userName']);
			$model->setValue('checktime',$time);
			$res =$model->save();
			$arr_goods[] = $goods_id;
			$arr_val[]   = $xianzaichengben_new;
		}
		//向销售政策推送数据
		$ree = ApiModel::salepolicy_api('changeCostPrice',array('goods_id','chengben'),array($arr_goods,$arr_val));
		if($ree['error'])
		{
			$result['error'] = "销售政策数据推送失败";
			Util::jsonExit($result);

		}
		//var_dump($arr_goods);
		//var_dump($arr_val);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);

	}
	public function getTemplate()
	{
		$str = "货号(必填)\t,最新采购成本(必填)\t,备注\n";
		header("Content-Disposition: attachment;filename=update_chengben.csv");
		echo iconv("utf-8","gbk", $str);
	}

	/**
	 *	downLoad，下载
	 */
	public function download ()
	{
		$where = json_decode(_Get::get('search_condition'),true);
		set_time_limit(0);
		ini_set('memory_limit','2000M');
		$ids = _Post::getList('_ids');
		$model = new WarehouseCaigouTiaozhengModel(21);
		$datalist = $model->pageList($where,$page,$pageSize=100,$useCache=true);		//获得选中的信息
// 		echo "<pre>";
// 		$lists= array();
// 		$view =new DictView(new DictModel(1));	
// 		for($i=0;$i<20;$i++){
// 			$lists[] = $view->getEnum('warehouse.goods_status',$i);
// 		}
// 		print_r($lists);exit;
		
		$datalists=array();
		foreach($datalist['data'] as $k=>$v){
			$datalists[$k]['id']=$v['id']?$v['id']:'';
			$datalists[$k]['goods_id']=$v['goods_id']?$v['goods_id']:'';
			$datalists[$k]['goods_sn']=$v['goods_sn']?$v['goods_sn']:'';
			$datalists[$k]['type']=$v['type']?$v['type']:'';
			$datalists[$k]['shizhong']=$v['shizhong']?$v['shizhong']:'';
			$datalists[$k]['yanse']=$v['yanse']?$v['yanse']:'';
			$datalists[$k]['jingdu']=$v['jingdu']?$v['jingdu']:'';
			$datalists[$k]['qiegong']=$v['qiegong']?$v['qiegong']:'';
			$datalists[$k]['warehouse']=$v['warehouse']?$v['warehouse']:'';
			$datalists[$k]['is_on_sale']=$v['is_on_sale']?$goods_status[$v['is_on_sale']]:'';
			$datalists[$k]['yuanshichengbenjia']=$v['yuanshichengbenjia']?$v['yuanshichengbenjia']:'';
			$datalists[$k]['xianzaichengben_old']=$v['xianzaichengben_old']?$v['xianzaichengben_old']:'';
			$datalists[$k]['xianzaichengben_new']=$v['xianzaichengben_new']?$v['xianzaichengben_new']:'';
			$datalists[$k]['price_diff']=$v['xianzaichengben_new']-$v['xianzaichengben_old'];
			$datalists[$k]['shuoming']=$v['shuoming']?$v['shuoming']:'';
			$datalists[$k]['addname']=$v['addname']?$v['addname']:'';
			$datalists[$k]['addtime']=$v['addtime']?$v['addtime']:'';
			$datalists[$k]['checkname']=$v['checkname']?$v['checkname']:'';
			$datalists[$k]['checktime']=$v['checktime']?$v['checktime']:'';
			$datalists[$k]['info']=$v['info']?$v['info']:'';
			$datalists[$k]['status']=$v['status']?$v['status']:'';
	
		}
		//}
		$title = array(
				'序号',
				'货号',
				'款号',
				'货品类型',
				'石重',
				'色级',
				'净度',
				'切工',
				'货品位置',
				'货品状态',
				'原始采购成本',
				'改前采购成本',
				'改后采购成本',
				'调整价差',
				'调价说明',
				'制单人',
				'制单时间',
				'审核人',
				'审核时间',
				'备注',
				'状态');
		
		Util::downloadCsv("采购成本列表",$title,$datalists);
		
		}
		
		
}

?>