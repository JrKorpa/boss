<?php
/**
 *  -------------------------------------------------
 *   @file		: AppShopConfigController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-18 14:53:19
 *   @update	:
 *  -------------------------------------------------
 */
class AppKaiGuanController extends CommonController
{
	protected $smartyDebugEnabled = true;
    protected $whitelist = array('downLoadExcel');
    protected $from_adList = array(1=>'kela',2=>'fiveonezuan',3=>'venus',4=>'dharam',5=>'diamondbyhk',6=>'diarough',7=>'emd',8=>'gd',9=>'jb',10=>'kapu',11=>'kgk',12=>'hy',13=>'leo',14=>'kiran',15=>'vir',16=>'karp',17=>'enjoy',18=>'changning',19=>'kb');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('app_shop_config','front',17);	//生成模型后请注释该行
		//Util::V('app_shop_config',17);	//生成视图后请注释该行
		$allow = array('罗芳','叶启新','周亮','邹燕华','admin','袁娟');
		if(!in_array($_SESSION['userName'],$allow)){
		    die('操作禁止,只有'. implode("/", $allow). '才可以操作');
		}
		$this->render('app_kai_guan_search_form.html', array('bar' => Auth::getBar()));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
		$args = array(
			'mod'	=> _Request::get("mod"),
			'con'	=> substr(__CLASS__, 0, -10),
			'act'	=> __FUNCTION__
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;

		$model = new AppKaiGuanModel(19);
		$kaiguanList = $model->getAllList();
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_kai_guan_search_page';
		$this->render('app_kai_guan_search_list.html',array(
			'kaiguanList'=>$kaiguanList,
            'from_adList'=>$this->from_adList
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_kai_guan_info.html',array(
			'view'=>new AppShopConfigView(new AppShopConfigModel(17))
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
		$result['content'] = $this->fetch('app_kai_guan_info.html',array(
			'view'=>new AppShopConfigView(new AppShopConfigModel($id,17))
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		die('开发中');
		$id = intval($params["id"]);
		$this->render('app_kai_guan_show.html',array(
			'view'=>new AppShopConfigView(new AppShopConfigModel($id,17))
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
				'name'=>  _Request::getString('name'),
				'code'=>  _Request::getString('code'),
				'value'=>  _Request::getString('value')
			);
		if($newdo['name']==''){
			$result['error'] = '名称不能为空';
			Util::jsonExit($result);
		}

		if($newdo['code']==''){
			$result['error'] = '编码不能为空';
			Util::jsonExit($result);
		}

		if($newdo['value']==''){
			$result['error'] = '值不能为空';
			Util::jsonExit($result);
		}
        $model    = new AppShopConfigModel(18);
        $has      = $model->hasConfig($newdo['name'],$newdo['code']);
        if ($has){
            $result['error'] = "已经存在相同名称或编码,请重新填写";
            Util::jsonExit($result);
        }
		$newmodel =  new AppShopConfigModel(18);
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
		$id = _Post::getInt('id');

		$newmodel =  new AppShopConfigModel($id,18);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
				'id'=>  $id,
				'name'=>  _Request::getString('name'),
				'code'=>  _Request::getString('code'),
				'value'=>  _Request::getString('value')
			);
		if($newdo['name']==''){
			$result['error'] = '名称不能为空';
			Util::jsonExit($result);
		}

		if($newdo['code']==''){
			$result['error'] = '编码不能为空';
			Util::jsonExit($result);
		}

		if($newdo['value']==''){
			$result['error'] = '值不能为空';
			Util::jsonExit($result);
		}
		
        $has      = $newmodel->hasConfig($newdo['name'],$newdo['code']);
        if ($has){
            $result['error'] = "已经存在相同名称或编码,请重新填写";
            Util::jsonExit($result);
        }
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
		$model = new AppShopConfigModel($id,18);
		$do = $model->getDataObject();
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update_dia_switch($params)
	{
		$result = array('success' => 0,'error' =>'');
        
        $from = _Request::getList('from');

		$newmodel =  new  AppKaiGuanModel(20);
		$allList = $newmodel->getAllList();
        $updataKai=array();
        $updataGuan=array();

        foreach($allList as $k => $val)
        {
        	$vid 	  = $val['vendor_id'];
        	$activate = $val['activate'];
        	$isSelected = in_array($vid, $from);

        	if ($isSelected && $activate == 0) {
        		$updataKai[] = $vid; 
        	}
        	if (!$isSelected && $activate == 1) {
        		$updataGuan[] = $vid; 
        	}
        }
        $res = $newmodel->UpdateKaiQi($updataKai,$updataGuan);
		if($res['success'] == 'true')
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '修改失败';
		}

		// 增加记录：
	 	$saveData = array(
            'module'=>"diamond",
            'controller'=>"AppKaiGuan",
            'action'=>"update_dia_switch",
            'request_url'=>"update_dia_switch",
            'remark'=>$res['log'],
            'data'=>json_encode($res),
            'create_user'=>Auth::$userName,
            'ip'=>Util::getClicentIp(),
            'create_time'=>date('Y-m-d H:i:s'),
        );
        $logModel = new UserOperationLogModel(1);
        $logModel->saveData($saveData,array());

		Util::jsonExit($result);
	}

    public function downLoadExcel()
    {
        ini_set('memory_limit','-1');
        set_time_limit(0);
        $from_ad = _Request::getString('from_ad');
        $temexcel_file = 'cron/diamond/'.$from_ad.".xml";
        $file = fopen($temexcel_file, 'r');

        header('Content-type: application/octet-stream');
        header("Accept-Ranges:bytes");
        header("Accept-length:" . filesize($temexcel_file));
        header('Content-Disposition: attachment;filename=' . $from_ad.".xml");
        ob_clean();
        $a = fread($file, filesize($temexcel_file));
        fclose($file);
        echo $a;
    }
}

?>