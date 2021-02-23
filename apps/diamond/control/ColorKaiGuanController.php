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
class ColorKaiGuanController extends CommonController
{
	protected $smartyDebugEnabled = true;
    protected $from_adList = array(1=>'kela',2=>'leibish');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
// 		Util::M('app_shop_config','front',17);	//生成模型后请注释该行
// 		Util::V('app_shop_config',17);	//生成视图后请注释该行
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

		$model = new ColorKaiGuanModel(19);
		
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
	public function update_kai ($params)
	{
		$result = array('success' => 0,'error' =>'');
        
        $from = _Request::getList('from');

		$newmodel =  new  ColorKaiGuanModel(20);
		$allList = $newmodel->getAllList();
        $updataKai=array();
        $updataGuan=array();
        foreach($allList as $k => $val){
            if(in_array($val['from_ad'],$from)){
                if($val['enabled'] == 0){
                    $updataKai[] = $val['from_ad']; 
                }
            }else{
                if($val['enabled'] == 1){
                    $updataGuan[] = $val['from_ad']; 
                }
            }
        }

        $res = $newmodel->UpdateKaiQi($updataKai,$updataGuan);
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
}

?>