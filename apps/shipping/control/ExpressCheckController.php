<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoHController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-21 21:31:17
 *   @update	:
 *  -------------------------------------------------
 */
class ExpressCheckController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $whitelist  = array('downloads');

	/**
	 *	index，搜索框
	 */
	 public function index ($params)
	{
		$this->render('express_check_search_form.html',array('bar'=>Auth::getBar()));
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


		);
		$page = _Request::getInt("page",1);
		$where = array();

		$model = new ExpressCheckModel(43);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'express_check_search_page';
		$this->render('express_check_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function uploade ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('express_check_info.html',array(
			'view'=>new ExpressCheckView(new ExpressCheckModel(43))
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
		$result['content'] = $this->fetch('warehouse_bill_info_h_info.html',array(
			'view'=>new WarehouseBillInfoHView(new WarehouseBillInfoHModel($id,21)),
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
		$this->render('warehouse_bill_info_h_show.html',array(
			'view'=>new WarehouseBillInfoHView(new WarehouseBillInfoHModel($id,21)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		//var_dump($_FILES['label']['name']);exit;
		$result = array('success' => 0,'error' =>'');
		if(empty($_FILES)){
			$result['error'] = '请上传文件！';
			Util::jsonExit($result);
		}
		$tmp_name = $_FILES['label'];
		//file_put_contents($fp, $content);
		$uploadObj = new Upload();
		$path = KELA_ROOT.'/apps/shipping/upload/';
		$uploadObj->base_path=$path;
		if(isset($_FILES['label'])){
			$res = $uploadObj->toUP($_FILES['label']);
			//var_dump($res);exit;
			if(!is_array($res)){
				$result['error'] = '文件上传失败';
				Util::jsonExit($result);
			}else{
				//$head_img = $res['url'];

				$olddo = array();
				$newdo=array(
					'path'=>$res['path'],
					"oldname"=>$res['oldname'],
					"name"=>$res['name']
				);

				$newmodel =  new ExpressCheckModel(43);
				$res = $newmodel->saveData($newdo,$olddo);
				if($res !== false)
				{
					$result['success'] = 1;
				}
				else
				{
					$result['error'] = '上传失败';
				}
				Util::jsonExit($result);

			}
		}else{
				$result['error'] = '请上传文件！';
				Util::jsonExit($result);
		}
		Util::jsonExit($result);

	}


	/**
	 *	check，对账操作
	 */
	public function check ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');

		$id = _Post::getInt('id');
		$newmodel =  new ExpressCheckModel($id,43);
		$option=$newmodel->getValue('option');
		$tmp_name=$newmodel->getValue('name');
		$path=$newmodel->getValue('path');
		$file_path = KELA_ROOT."{$path}{$tmp_name}";
		//var_dump($file_path);exit;
		$file=file("{$file_path}");
		$freight_no_str='';
		foreach($file as $value){
			//$res = $newmodel->check_freight_no($value);
			//echo $value."<br>";
			$freight_no_str .="{$value},";

		}
		$freight_no_str = rtrim($freight_no_str,',');
		//var_dump($freight_no_str);exit;
		$olddo = $newmodel->getDataObject();
		$newdo=array(
				'id'=>$id,
				'option'=>1,
				//'freight_no_str'=>$freight_no_str,
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
			$result['error'] = '对账失败';
		}
		Util::jsonExit($result);
	}
	//edit by zhangruiying2015/7/6 对于下载5000条数据等待5分钟优化
	public function downloads($data) {
		$id = _Request::get('id');
		$newmodel =  new ExpressCheckModel($id,43);
		$tmp_name=$newmodel->getValue('name');
		$oldname=$newmodel->getValue('oldname');
		$path=$newmodel->getValue('path');
		$file_path = KELA_ROOT."{$path}{$tmp_name}";
		$arr=pathinfo($file_path);
		if(file_exists($file_path) and isset($arr['extension']) and $arr['extension']=='txt')
		{
			$file=file("{$file_path}");
			$file=implode(',',$file);
			$file=preg_replace('/\s/','',$file);
			$arr=$newmodel->getObjectList(array('ids'=>$file));
			$file=explode(',',$file);
			if(!empty($arr))
			{
				$arr=array_combine(array_column($arr,'freight_no'),$arr);
			}
			$expressModel=new ExpressModel(1);
			$exp_list=$expressModel->getAllExpress();
			$exp_list=array_combine(array_column($exp_list,'id'),array_column($exp_list,'exp_name'));
			//获取所有渠道列表
			$SalesChannelsModel = new SalesChannelsModel(1);
			$getSalesChannelsInfo = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",'');
			$getSalesChannelsInfo=array_combine(array_column($getSalesChannelsInfo,'id'),array_column($getSalesChannelsInfo,'channel_name'));
			$title=array('快递单号','订单号','订单渠道','寄件人','寄件部门','收货地址','快递公司','发货日期','发货缘由');
			$res_arr=array();
			if (!empty($file)) {
				foreach ($file as  $val) {
						$res=array();
						$res['freight_no']=isset($arr[$val]['freight_no'])?$arr[$val]['freight_no']."\t":$val."\t";
						$res['order_no']=isset($arr[$val]['order_no'])?$arr[$val]['order_no']."\t":'';
						$res['channel_id']=isset($arr[$val]['channel_id'])?$arr[$val]['channel_id']:'';
						$res['channel_id'] =isset($getSalesChannelsInfo[$res['channel_id']])?$getSalesChannelsInfo[$res['channel_id']]:'';
						$res['sender']=isset($arr[$val]['sender'])?$arr[$val]['sender']:'';
						$res['department']=isset($arr[$val]['department'])?$arr[$val]['department']:'';
						$res['cons_address']=isset($arr[$val]['cons_address'])?$arr[$val]['cons_address']:'';
						$res['express_id']=isset($arr[$val]['express_id'])?$arr[$val]['express_id']:'';
						$res['express_id'] =isset($exp_list[$res['express_id']])?$exp_list[$res['express_id']]:'';
						$res['create_time'] =isset($arr[$val]['create_time'])?date('Y-m-d H:i:s',intval($arr[$val]['create_time'])):'';
						$res['remark']=isset($arr[$val]['remark'])?$arr[$val]['remark']:'';
						$res_arr[]=$res;
				}
			}
			Util::downloadCsv(rtrim($oldname,'.txt'),$title,$res_arr);
		}
		else
		{
			echo '文件不存在或文件类型错误';
			exit;
		}
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
		$newmodel =  new WarehouseBillInfoHModel($id,22);

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
	public function del ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model =  new ExpressCheckModel($id,43);
		$tmp_name=$model->getValue('name');
		$path=$model->getValue('path');
		@unlink(KELA_ROOT."{$path}{$tmp_name}");
		$do = $model->getDataObject();
		$res = $model->delete();
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>