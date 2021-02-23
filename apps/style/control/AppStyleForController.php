<?php
/**
 *  -------------------------------------------------
 *   @file		: AppStyleForController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-19 10:41:52
 *   @update	:
 *  -------------------------------------------------
 */
class AppStyleForController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_style_for_search_form.html',array('bar'=>Auth::getBar()));
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
			$style_id = _Request::getInt("_id")
		);
		$page = _Request::getInt("page",1);
		$where = array(
				'style_id'=>$style_id
			);

		$model = new AppStyleForModel(11);
		$data = $model->pageList($where,$page,10,false);
		foreach($data['data'] as $k=>$v){
			$data['data'][$k]['style_for_who']=trim($v['style_for_who'],",");
			if(!empty($data['data'][$k]['style_for_who'])){
				$data['data'][$k]['style_for_who']=explode(",",$data['data'][$k]['style_for_who']);
				foreach($data['data'][$k]['style_for_who'] as $key=>$val){
					if($val==1){
						$data['data'][$k]['style_for_who'][$key]='宝宝';
					}elseif($val==2){
						$data['data'][$k]['style_for_who'][$key]='青年';					
					}elseif($val==3){
						$data['data'][$k]['style_for_who'][$key]='中年';					
					}elseif($val==4){
						$data['data'][$k]['style_for_who'][$key]='老年';					
					}
				}
				$data['data'][$k]['style_for_who']=implode(",",$data['data'][$k]['style_for_who']);
			}

			$data['data'][$k]['style_for_use']=trim($v['style_for_use'],",");
			if(!empty($data['data'][$k]['style_for_use'])){
				$data['data'][$k]['style_for_use']=explode(",",$data['data'][$k]['style_for_use']);
				foreach($data['data'][$k]['style_for_use'] as $key=>$val){
					if($val==1){
						$data['data'][$k]['style_for_use'][$key]='求婚';
					}elseif($val==2){
						$data['data'][$k]['style_for_use'][$key]='纪念日';					
					}elseif($val==3){
						$data['data'][$k]['style_for_use'][$key]='结婚 ';					
					}
				}
				$data['data'][$k]['style_for_use']=implode(",",$data['data'][$k]['style_for_use']);
			}

			$data['data'][$k]['style_for_when']=trim($v['style_for_when'],",");
			if(!empty($data['data'][$k]['style_for_when'])){
				$data['data'][$k]['style_for_when']=explode(",",$data['data'][$k]['style_for_when']);
				foreach($data['data'][$k]['style_for_when'] as $key=>$val){
					if($val==1){
						$data['data'][$k]['style_for_when'][$key]='投资';
					}elseif($val==2){
						$data['data'][$k]['style_for_when'][$key]='装饰';					
					}
				}
				$data['data'][$k]['style_for_when']=implode(",",$data['data'][$k]['style_for_when']);
			}

			$data['data'][$k]['style_for_designer']=trim($v['style_for_designer'],",");
			if(!empty($data['data'][$k]['style_for_designer'])){
				$data['data'][$k]['style_for_designer']=explode(",",$data['data'][$k]['style_for_designer']);
				foreach($data['data'][$k]['style_for_designer'] as $key=>$val){
					if($val==1){
						$data['data'][$k]['style_for_designer'][$key]='无';
					}elseif($val==2){
						$data['data'][$k]['style_for_designer'][$key]='POLO';					
					}
				}
				$data['data'][$k]['style_for_designer']=implode(",",$data['data'][$k]['style_for_designer']);
			}
		}
		
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_style_for_search_page';
		$this->render('app_style_for_search_list.html',array(
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
		$result['content'] = $this->fetch('app_style_for_info.html',array(
			'view'=>new AppStyleForView(new AppStyleForModel(11)),
			'style_id'=> _Post::getInt('_id'),
			'style_for_who1'=>false,
			'style_for_who2'=>false,
			'style_for_who3'=>false,
			'style_for_who4'=>false,
			'style_for_use1'=>false,
			'style_for_use2'=>false,
			'style_for_use3'=>false,
			'style_for_when1'=>false,
			'style_for_when2'=>false,
			'style_for_designer1'=>false,
			'style_for_designer2'=>false
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
		$tab_id = intval($params["tab_id"]);
		$result = array('success' => 0,'error' => '');
		$where['id']=$id;
		$model = new AppStyleForModel(11);
		$data=$model->get_style_for_by_id($where);
		if(strpos($data['style_for_who'],"1")){
			$style_for_who1=true;
		}else{
			$style_for_who1=false;
		}
		if(strpos($data['style_for_who'],"2")){
			$style_for_who2=true;
		}else{
			$style_for_who2=false;
		}
		if(strpos($data['style_for_who'],"3")){
			$style_for_who3=true;
		}else{
			$style_for_who3=false;
		}
		if(strpos($data['style_for_who'],"4")){
			$style_for_who4=true;
		}else{
			$style_for_who4=false;
		}

		if(strpos($data['style_for_use'],"1")){
			$style_for_use1=true;
		}else{
			$style_for_use1=false;
		}
		if(strpos($data['style_for_use'],"2")){
			$style_for_use2=true;
		}else{
			$style_for_use2=false;
		}
		if(strpos($data['style_for_use'],"3")){
			$style_for_use3=true;
		}else{
			$style_for_use3=false;
		}

		if(strpos($data['style_for_when'],"1")){
			$style_for_when1=true;
		}else{
			$style_for_when1=false;
		}
		if(strpos($data['style_for_when'],"2")){
			$style_for_when2=true;
		}else{
			$style_for_when2=false;
		}

		if(strpos($data['style_for_designer'],"1")){
			$style_for_designer1=true;
		}else{
			$style_for_designer1=false;
		}
		if(strpos($data['style_for_designer'],"2")){
			$style_for_designer2=true;
		}else{
			$style_for_designer2=false;
		}
		$result['content'] = $this->fetch('app_style_for_info.html',array(
			'view'=>new AppStyleForView(new AppStyleForModel($id,11)),
			'tab_id'=>$tab_id,
			'style_id'=>$data['style_id'],
			'style_for_who1'=>$style_for_who1,
			'style_for_who2'=>$style_for_who2,
			'style_for_who3'=>$style_for_who3,
			'style_for_who4'=>$style_for_who4,
			'style_for_use1'=>$style_for_use1,
			'style_for_use2'=>$style_for_use2,
			'style_for_use3'=>$style_for_use3,
			'style_for_when1'=>$style_for_when1,
			'style_for_when2'=>$style_for_when2,
			'style_for_designer1'=>$style_for_designer1,
			'style_for_designer2'=>$style_for_designer2
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
		$this->render('app_style_for_show.html',array(
			'view'=>new AppStyleForView(new AppStyleForModel($id,11)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库 
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		
		$style_for_who = _Request::getList('style_for_who');
		$style_for_use = _Request::getList('style_for_use');
		$style_for_when = _Request::getList('style_for_when');
		$style_for_designer = _Request::getList('style_for_designer');
		$style_id = _Request::getInt('style_id');

		$olddo = array();
		$newdo=array(
				'style_for_who'=>",".join(",",$style_for_who).",",	
				'style_for_use'=>",".join(",",$style_for_use).",",	
				'style_for_when'=>",".join(",",$style_for_when).",",	
				'style_for_designer'=>",".join(",",$style_for_designer).",",
				'style_id'=>$style_id
			);

		$newmodel =  new AppStyleForModel(12);
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

		$id = _Post::getInt('id');
		$style_for_who = _Request::getList('style_for_who');
		$style_for_use = _Request::getList('style_for_use');
		$style_for_when = _Request::getList('style_for_when');
		$style_for_designer = _Request::getList('style_for_designer');

		$newmodel =  new AppStyleForModel($id,12);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
				'id'=>$id,	
				'style_for_who'=>",".join(",",$style_for_who).",",	
				'style_for_use'=>",".join(",",$style_for_use).",",	
				'style_for_when'=>",".join(",",$style_for_when).",",	
				'style_for_designer'=>",".join(",",$style_for_designer).",",
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
		$model = new AppStyleForModel($id,12);
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