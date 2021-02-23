<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseMingyiTiaozhengController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-16 16:02:18
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseMingyiTiaozhengController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist = array('getTemplate');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('warehouse_mingyi_tiaozheng_search_form.html',array(
                    'bar'=>Auth::getBar(),
                    'huopintype' => $this->huopintype(),
                    'dd' => new DictView(new DictModel(1)),
                        ));
	}
    public function  huopintype()
	{
		return  array(1=>'裸石',2=>'成品',3=>'其他');
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
			'checktime_e'=> _Request::get("checktime_e")
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
		$where = array();
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
			'checktime_e'	=> $args['checktime_e']
		);
        if(SYS_SCOPE == 'zhanting'){
            $where['hidden'] = '0';
        }
		$model = new WarehouseMingyiTiaozhengModel(21);
		$data = $model->pageList($where,$page,10,false);
                //var_dump($data);exit;
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'warehouse_mingyi_tiaozheng_search_page';
		$this->render('warehouse_mingyi_tiaozheng_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
                        'huopintype' => $this->huopintype()
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_mingyi_tiaozheng_info.html',array(
			'view'=>new WarehouseMingyiTiaozhengView(new WarehouseMingyiTiaozhengModel(21)),
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
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('warehouse_mingyi_tiaozheng_info.html',array(
			'view'=>new WarehouseMingyiTiaozhengView(new WarehouseMingyiTiaozhengModel($id,21)),
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
		$this->render('warehouse_mingyi_tiaozheng_show.html',array(
			'view'=>new WarehouseMingyiTiaozhengView(new WarehouseMingyiTiaozhengModel($id,21)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$shuoming	= $_POST['shuoming'];
		$type		= $_POST['type'];
		$model =  new WarehouseMingyiTiaozhengModel(22);

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
				foreach ($res as $val)
				{
					$new_goods[] = $val['goods_id'];
				}
			}

			//目前只是全部判断正确错误，未详细说明情况
			if (count($res) != count($goods_ids))
			{
				$result['error'] = '上传货号不存在，请检查后上传';
				Util::jsonExit($result);
			}
		}
		//echo "<pre>";print_r($res);exit;
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
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new WarehouseMingyiTiaozhengModel($id,22);

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
		$model = new WarehouseMingyiTiaozhengModel($id,22);
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
	public function getTemplate()
	{
		$str = "货号(必填),最新名义成本(必填),备注\n";
		header("Content-Disposition: attachment;filename=mingyichengben.csv");
		echo iconv("utf-8","gbk", $str);
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

		$model = new WarehouseMingyiTiaozhengModel(21);
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
					//$z="/^\\d+$/";
					if(!preg_match($z,$xianzaichengben_new))
					{
						$result['error'] = '名义成本只能为数字且不能为负数';
						return $result;
					} 
					$goods_info = array(
						'goods_id'=> $goods_id,
						'xianzaimingyi_new' =>$xianzaichengben_new,
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
			$model    = new WarehouseMingyiTiaozhengModel($val,21);
			$goods_id = $model->getValue('goods_id'); //查询货号
			$xianzaimingyi_new = $model->getValue('xianzaimingyi_new');
			$re  = $model->update_chengben($goods_id,$xianzaimingyi_new);
			//修改审核状态
			$model->setValue('status',2);
			$model->setValue('checkname',$_SESSION['userName']);
			$model->setValue('checktime',$time);
			$res =$model->save();
			$arr_goods[] = $goods_id;
			$arr_val[]   = $xianzaimingyi_new;
		}
		//向销售政策推送数据
	/* 	$ree = ApiModel::salepolicy_api('changeCostPrice',array('goods_id','chengben'),array($arr_goods,$arr_val));
		if($ree['error'])
		{
			$result['error'] = "销售政策数据推送失败";
			Util::jsonExit($result);
	
		} */
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
}

?>