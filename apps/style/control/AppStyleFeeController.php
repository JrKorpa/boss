<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleFeeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-12 17:02:54
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleFeeController extends CommonController
{
	protected $smartyDebugEnabled = false;
	protected $feeTypeArr = array('1'=>'18K工费','2'=>'超石费用','3'=>'表面工艺','4'=>'PT950工费');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_style_fee_search_form.html',array('bar'=>Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$style_id = _Request::getInt('_id');
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__,
			//'参数' = _Request::get("参数");
			'style_id'=>$style_id,


		);
		$page = _Request::getInt("page",1);
		$where = array(
			'style_id'=>$style_id,
		);
		
		$model = new AppStyleFeeModel(11);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_style_fee_search_page';
		$this->render('app_style_fee_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'style_id'=>$style_id,
            'feeTypes'=>$this->feeTypeArr
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$style_id = _Request::getInt('_id');
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_style_fee_info.html',array(
			'view'=>new AppStyleFeeView(new AppStyleFeeModel(11)),
			'style_id'=>$style_id,
            'feeTypes'=>$this->feeTypeArr
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
		$style_id = _Request::getInt('_id');
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_style_fee_info.html',array(
			'view'=>new AppStyleFeeView(new AppStyleFeeModel($id,11)),
			'tab_id'=>$tab_id,
			'style_id'=>$style_id,
            'feeTypes'=>$this->feeTypeArr
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
		$this->render('app_style_fee_show.html',array(
			'view'=>new AppStyleFeeView(new AppStyleFeeModel($id,11)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$style_id=_Request::getInt('style_id');
        $fee_type = _post::getInt('fee_type');
		if($fee_type<1){
            $result['error'] = "请选择费用类型";
			Util::jsonExit($result);
        }
		$price = _post::getInt('price');
        if($price<0){
            $result['error'] = "费用要大于等于0";
			Util::jsonExit($result); 
        }
		$status = _post::getInt('status');
		$check_user=$_SESSION['userName'];
		$check_time=date("Y-m-d H:i:s");
		$olddo = array();
		$newdo=array(
			'style_id'=>$style_id,
			'fee_type'=>$fee_type,	
			'price'=>$price,
			'status'=>$status,
			'check_user'=>$check_user,
			'check_time'=>$check_time,
		);

		$newmodel =  new AppStyleFeeModel(12);
        $existFeeType = $newmodel->existFeeType($style_id, $fee_type);
        if($existFeeType > 0){
            $result['error'] = "同款同费用类型的记录已经存在";
			Util::jsonExit($result);
        }
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '添加失败';
		}
		$newlog=array(
			'style_id'=>$style_id,
			'create_user'=>$_SESSION['userName'],
            'create_time'=>date("Y-m-d H:i:s"),
			'remark'=>'添加公费类型：'.$this->feeTypeArr[$fee_type]
		);
		$newlogmodel =  new BaseStyleLogModel(12);
		$newlogmodel->saveData($newlog,$olddo);
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		
		$fee_type = _post::getInt('fee_type');
		$price = _post::getInt('price');
        if($price<0){
            $result['error'] = "费用要大于等于0";
			Util::jsonExit($result); 
        }
		$status = _post::getInt('status');
		$_cls = _Post::getInt('_cls');
		$tab_id = _Post::getInt('tab_id');
		
		$id = _Post::getInt('id');
		$style_id = _Post::getInt('style_id');
		$check_user=$_SESSION['userName'];
		$check_time=date("Y-m-d H:i:s");
		

		$newmodel =  new AppStyleFeeModel($id,12);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			'style_id'=>$style_id,
			'fee_type'=>$fee_type,	
			'price'=>$price,
			'status'=>$status,
			'check_user'=>$check_user,
			'check_time'=>$check_time,
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
		$model = new AppStyleFeeModel($id,12);
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
}

?>