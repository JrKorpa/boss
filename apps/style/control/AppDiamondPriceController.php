<?php
/**
 *  -------------------------------------------------
 *   @file		: AppDiamondPriceController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 11:11:16
 *   @update	:
 *  -------------------------------------------------
 */
class AppDiamondPriceController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('app_diamond_price','front',11);	//生成模型后请注释该行
		//Util::V('app_diamond_price',11);	//生成视图后请注释该行
		$this->render('app_diamond_price_search_form.html',array('bar'=>Auth::getBar()));
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
			'guige_a'	=> _Request::getString('guige_a'),
			'guige_b'=> _Request::getString('guige_b'),
			'price' => _Request::getString('price'),
			'guige_status' => _Request::getString('guige_status')
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['guige_a'] = _Request::getString('guige_a');
		$where['guige_b'] = _Request::getString('guige_b');
		$where['price'] = _Request::getString('price');
		$where['guige_status'] = _Request::getString('guige_status');
		$model = new AppDiamondPriceModel(19);
		$data = $model->pageList($where,$page,20,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_diamond_price_search_page';
		$this->render('app_diamond_price_search_list.html',array(
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
		$result['content'] = $this->fetch('app_diamond_price_info.html',array(
			'view'=>new AppDiamondPriceView(new AppDiamondPriceModel(19))
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
		$result['content'] = $this->fetch('app_diamond_price_info.html',array(
			'view'=>new AppDiamondPriceView(new AppDiamondPriceModel($id,19))
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
		$this->render('app_diamond_price_show.html',array(
			'view'=>new AppDiamondPriceView(new AppDiamondPriceModel($id,19))
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
			'guige_a' => _Request::getInt('guige_a'),
			'guige_b' => _Request::getInt('guige_b'),
			'price' => _Request::getFloat('price'),			
		);	

		if(empty($newdo['guige_a']) || $newdo['guige_a']<0){
			$result['error'] = '最小值应该大于等于0的整数';
			Util::jsonExit($result);
		}
		if(empty($newdo['guige_b'])  || $newdo['guige_b']<0){
			$result['error'] = '最大值应该大于0的整数';
			Util::jsonExit($result);
		}
		if(empty($newdo['price'])){
			$result['error'] = '价格不能为空';
			Util::jsonExit($result);
		}
		if($newdo['guige_a']>$newdo['guige_b']){
			$result['error'] = '最小值不能大于最大值';
			Util::jsonExit($result);
		}
		
		$newmodel =  new AppDiamondPriceModel(20);
		$get_diamondprice = $newmodel->getAllList();
        $do = true;
       
        foreach ($get_diamondprice as $v) {
           if ($v['guige_a'] <= $newdo['guige_a'] && $newdo['guige_a'] < $v['guige_b']) {
                $do = FALSE;
                break;
            }
            if ($v['guige_a'] <$newdo['guige_b'] && $newdo['guige_b'] < $v['guige_b']) {
                $do = FALSE;
                break;
            }
            if ($newdo['guige_a'] <= $v['guige_a'] && $newdo['guige_b'] >= $v['guige_b']) {
                $do = FALSE;
                break;
            }
        }
		
        if(!$do){
            $result['error'] = '请重新设置最小值和最大值范围！';
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
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$id = _Post::getInt('id');

		$newmodel =  new AppDiamondPriceModel($id,20);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id' => $id,
			'guige_a' => _Request::getInt('guige_a'),
			'guige_b' => _Request::getInt('guige_b'),
			'price' => _Request::getFloat('price'),			
		);
		if(empty($newdo['guige_a'])){
			$result['error'] = '最小值不能为空';
			Util::jsonExit($result);
		}
		if(empty($newdo['guige_b'])){
			$result['error'] = '最大值不能为空';
			Util::jsonExit($result);
		}
		if(empty($newdo['price'])){
			$result['error'] = '价格不能为空';
			Util::jsonExit($result);
		}
		if($newdo['guige_a']>$newdo['guige_b']){
			$result['error'] = '最大值不能大于最大值';
			Util::jsonExit($result);
		}

		$get_diamondprice = $newmodel->getAllList();
        $do = true;
        foreach ($get_diamondprice as $v) {
            if($v['id']==$id){
                continue;
            }
            if ($v['guige_a'] <= $newdo['guige_a'] && $newdo['guige_a'] < $v['guige_b']) {
                $do = FALSE;
                break;
            }
            if ($v['guige_a'] <$newdo['guige_b'] && $newdo['guige_b'] < $v['guige_b']) {
                $do = FALSE;
                break;
            }
            if ($newdo['guige_a'] <= $v['guige_a'] && $newdo['guige_b'] >= $v['guige_b']) {
                $do = FALSE;
                break;
            }
        }
		
        if(!$do){
            $result['error'] = '范围出错';
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
/* 	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppDiamondPriceModel($id,20);
		$do = $model->getDataObject();
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	} */
	/**
	 *	stop停用
	 */
	public function delete($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppDiamondPriceModel($id,20);
		$do = $model->getDataObject();
		$status = $do['guige_status'];
        if($status == 0){
            $result['error'] = "此条数据已经停用";
            Util::jsonExit($result);
        }
		$model->setValue('guige_status',0);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}
	/**
	 *	delete，启用
	 */
	public function enable($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppDiamondPriceModel($id,20);
        $do = $model->getDataObject();
		$status = $do['guige_status'];
        if($status == 1){
            $result['error'] = "此条数据已经启用";
            Util::jsonExit($result);
        }
		$model->setValue('guige_status',1);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}
}

?>