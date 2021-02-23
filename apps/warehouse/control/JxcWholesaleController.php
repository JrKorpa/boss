<?php
/**
 *  -------------------------------------------------
 *   @file		: JxcWholesaleController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-25 19:01:18
 *   @update	:
 *  -------------------------------------------------
 */
class JxcWholesaleController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('jxc_wholesale_search_form.html',array('bar'=>Auth::getBar()));
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
			'wholesale_sn' => _Request::get("wholesale_sn"),
			'wholesale_name' => _Request::get("wholesale_name"),
			'wholesale_credit' => _Request::get("wholesale_credit"),


		);
		$page = _Request::getInt("page",1);
		$where = array(
				'wholesale_sn' => _Request::get("wholesale_sn"),
				'wholesale_name' => _Request::get("wholesale_name"),
				'wholesale_credit' => _Request::get("wholesale_credit"),
		);

		$model = new JxcWholesaleModel(21);
		$data = $model->pageList($where,$page,10,false);

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'jxc_wholesale_search_page';
		$this->render('jxc_wholesale_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
	    $result = array('success' => 0,'error' => '');
	    
	    $api = new ApiModel();
	    $company = $api->management_api(array(), array(), 'getCompanyList');
	   
		$result['content'] = $this->fetch('jxc_wholesale_info.html',array(
			'view'=>new JxcWholesaleView(new JxcWholesaleModel(21)),
		    'company' => $company
		));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
	    $api = new ApiModel();
	    $company = $api->management_api(array(), array(), 'getCompanyList');
	    
		$id = intval($params["id"]);
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('jxc_wholesale_info.html',array(
			'view'=>new JxcWholesaleView(new JxcWholesaleModel($id,21)),
			'tab_id'=>$tab_id,
		    'company' => $company
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
		$this->render('jxc_wholesale_show.html',array(
			'view'=>new JxcWholesaleView(new JxcWholesaleModel($id,21)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$olddo = array();
		$newdo=array(
			//'wholesale_sn'=>_Request::get('wholesale_sn'),
			'wholesale_name'=>_Request::get('wholesale_name'),
			'wholesale_credit'=>_Request::get('wholesale_credit'),
			'add_name'=>$_SESSION['userName'],
			'add_time'=>date('Y-m-d H:i:s'),
		    'sign_required' => _Request::get('sign_required'),
		    'sign_company' => _Request::get('sign_company')
		);
		
		// 如果不需要签收，则清空
		if ($newdo['sign_required'] != '1') {
            $newdo['sign_required'] = 0;
            $newdo['sign_company'] = 0;
		} else if ($newdo['sign_company'] == '' || $newdo['sign_company'] == '0') {
		    $result['error'] = '签收公司不能为空';
		    Util::jsonExit($result);
		}

		$newdo['wholesale_sn'] = $this->gen_unique_code(6);
	
		$newmodel =  new JxcWholesaleModel(22);
		$res = $newmodel->saveData($newdo,$olddo);
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

		$id = _Post::getInt('wholesale_id');
		
		

		$newmodel =  new JxcWholesaleModel($id,22);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'wholesale_id'=>$id,
			'wholesale_sn'=>_Request::get('wholesale_sn'),
			'wholesale_name'=>_Request::get('wholesale_name'),
			'wholesale_credit'=>_Request::get('wholesale_credit'),
			'add_name'=>$_SESSION['userName'],
			'add_time'=>date('Y-m-d H:i:s'),
		    'sign_required' => _Request::get('sign_required'),
		    'sign_company' => _Request::get('sign_company')
		);
		// 如果不需要签收，则清空
		if ($newdo['sign_required'] != '1') {
            $newdo['sign_required'] = 0;
            $newdo['sign_company'] = 0;
		} else if ($newdo['sign_company'] == '' || $newdo['sign_company'] == '0') {
		    $result['error'] = '签收公司不能为空';
		    Util::jsonExit($result);
		}
		
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
		$model = new JxcWholesaleModel($id,22);
		$do = $model->getDataObject();
		
		$model->setValue('wholesale_status',2);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "开启失败";
		}
		Util::jsonExit($result);
	}
	
	
	/**
	 *	open，kaiqi
	 */
	public function open ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new JxcWholesaleModel($id,22);
		$do = $model->getDataObject();
	
		$model->setValue('wholesale_status',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "开启失败";
		}
		Util::jsonExit($result);
	}

	private function gen_unique_code($code_length = 4) {
        $chars = "23456789ABCDEFGHJKLMNPQRSTUVWXYZ";
        $randomString = "";
        $len = strlen($chars)-1;
        for ($i = 0; $i < $code_length; $i++) {
            $randomString .= $chars[mt_rand(0,$len)];
        }
 
	    return $randomString;
	}
}

?>