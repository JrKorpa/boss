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
class ConfigVisitAllowController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('config_visit_allow_search_form.html',array('bar'=>Auth::getBar()));
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
			'user_name'=> _Request::getString('user_name'),
		);
		$page = _Request::getInt("page",1);
		$where = array(
			'user_name'=>$args['user_name']
		);
        $xilie = array();
        $xilie_arr = array();
        $dd = new DictModel(59);
        $xilie = $dd->getEnumArray("style.xilie");
        foreach ($xilie as $key => $value) {
            # code...
            $xilie_arr[$value['name']] = $value['label'];
        }
		$model = new ConfigVisitAllowModel(17);
		$data = $model->pageList($where,$page,10,false);
        if(!empty($data)){
            foreach ($data['data'] as $key => $value) {
                # code...
                $str = '';
                $xilie_array = array();
                $xilie_array = explode(",",$value['xilie']);
                $xilie_array = array_filter($xilie_array);
                foreach ($xilie_array as $k => $v) {
                    # code...
                    $str .= $xilie_arr[$v].",";
                }
                $data['data'][$key]['xilie'] = $str;
            }
        }
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'config_visit_allow_search_page';
		$this->render('config_visit_allow_search_list.html',array(
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
		$id = _Request::getInt('_id');
		$result['content'] = $this->fetch('config_visit_allow_info.html',array(
            'view'=>new ConfigVisitAllowView(new ConfigVisitAllowModel(17))
            ));
		$result['title'] = '添加';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
        $result = array('success' => 0,'error' => '');
		$id = intval($params["id"]);
        //print_r(new ConfigVisitAllowView(new ConfigVisitAllowModel($id,52)));die;
        $model=new ConfigVisitAllowModel($id,52);
        $xilie =$model->getValue('xilie');
        $xilie=explode(',',$xilie);
		$result['content'] = $this->fetch('config_visit_allow_info.html',array(
			'view'=>new ConfigVisitAllowView($model),'xilie'=>$xilie
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
        $user_name = _Request::getString('user_name');
        $xilie_list[] = _Request::getList('xilie');
        //echo '<pre>';
        //print_r($_POST);die;
        $newmodel = new ConfigVisitAllowModel(16);
		if($user_name == ''){
            $result['error'] = "请输入用户名！";
			Util::jsonExit($result);
        }
        if(empty($xilie_list)){
            $result['error'] = "请选择系列！";
			Util::jsonExit($result);
        }
        $userModel = new UserModel(1);
        $user_id = $userModel->getAccountId($user_name);
        if(!$user_id){
            $result['error'] = "系统没有该用户！";
            Util::jsonExit($result); 
        }

        //验证是否有仓储商品列表权限
        $check_user = $newmodel->getCheckListUser($user_id);
        //print_r($check_user);die;
        if(!$check_user){
            $result['error'] = "该用户没有仓储商品列表权限！";
            Util::jsonExit($result);
        }
        $arr_user = array();
        $arr_user['user_name'] = $user_name;
        $check_user = $newmodel->getAllUserConfig($arr_user);
        //print_r($check_user);die;
        if(!empty($check_user)){
            $result['error'] = "该用户已添加！";
            Util::jsonExit($result);
        }
        $xilie = ',';
        foreach ($xilie_list[0] as $key => $value) {
            # code...
            $xilie .= $value.",";
        }
        //$xilie = rtrim($xilie,",");
		$olddo = array();
		$newdo=array(
			'user_name'=>$user_name,
			'xilie'=>$xilie
		);
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
		$id = intval($params["id"]);
		$user_name = _Request::getString('user_name_user');
        $xilie_list[] = _Request::getList('xilie');
        if($user_name == ''){
            $result['error'] = "请输入用户名！";
            Util::jsonExit($result);
        }
        if(empty($xilie_list)){
            $result['error'] = "请选择系列！";
            Util::jsonExit($result);
        }
		
        $newmodel = new ConfigVisitAllowModel($id,16);
		$olddo = $newmodel->getDataObject();
		$xilie = ',';
        foreach ($xilie_list[0] as $key => $value) {
            # code...
            $xilie .= $value.",";
        }
        //$xilie = rtrim($xilie,",");
        $newdo=array(
            'id'=>$id,
            'user_name'=>$user_name,
            'xilie'=>$xilie
        );
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['title'] = '提示信息';
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
		$model = new ConfigVisitAllowModel($id,16);
		$do = $model->getDataObject();
		//联合删除？
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
}

?>