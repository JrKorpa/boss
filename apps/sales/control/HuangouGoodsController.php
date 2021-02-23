<?php
/**
 *  -------------------------------------------------
 *   @file		: HuangouGoodsController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2018-01-05 11:06:19
 *   @update	:
 *  -------------------------------------------------
 */
class HuangouGoodsController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$channel=new SalesChannelsModel(1);    
		$channel_list=$channel->getSalesChannelsInfo("id,channel_name",null);      
		$this->render('huangou_goods_search_form.html',array('bar'=>Auth::getBar(),'view'=>new HuangouGoodsView(new HuangouGoodsModel(27))));
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
            'channel_id' => _Request::get('channel_id'),
            'style_sn' => _Request::get('style_sn'),
            'status' => _Request::get('status')
		);
		
		$page = _Request::getInt("page",1);
		

		$model = new HuangouGoodsModel(27);
		$data = $model->pageList($args,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'huangou_goods_search_page';
		$this->render('huangou_goods_search_list.html',array(
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
		$result['content'] = $this->fetch('huangou_goods_info.html',array(
			'view'=>new HuangouGoodsView(new HuangouGoodsModel(27))
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
		$id =  _Request::getInt("id");
		$tab_id = _Request::getInt("tab_id");
	    //$id = _Request::get('id');
	    if(empty($id)){
	        $result['content'] = '参数错误：id 为空！';
	        Util::jsonExit($result);
	    }
	    
	    $model = new HuangouGoodsModel($id,27);
	    $view = new HuangouGoodsView($model);
	    
	    $olddo = $model->getDataObject();	    
	    if(empty($olddo)){
	        $result['content'] = '编辑对象不存在，可能已被删除！';
	        Util::jsonExit($result);
	    }else if($olddo['status']!=1){
	        $result['content'] = '商品已被停用，不能编辑！';
	        Util::jsonExit($result);
	    }

		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('huangou_goods_info.html',array(
			'view'=>$view,
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
		$this->render('huangou_goods_show.html',array(
			'view'=>new HuangouGoodsView(new HuangouGoodsModel($id,27)),
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
		$style_sn=trim(_Post::get("style_sn"));
        $newdo = array(
             "channel_id"=>_Post::get("channel_id"),
             "style_sn"=>$style_sn,
             "sale_price"=>_Post::get("sale_price"),
             "label_price"=>_Post::get("label_price"),             
             "create_user"=>$_SESSION['userName'],
             "create_time"=>date("Y-m-d H:i:s"),            
             "status"=>1
        );      
		$newmodel =  new HuangouGoodsModel(27);	

	    $style=$newmodel->getStyleInfo($style_sn);
	    if(empty($style)){
	        $result['error'] = '找不到款式'.$style_sn;
	        Util::jsonExit($result);	    	
	    }
	    if($style['check_status']!=3){
	        $result['error'] = '款号'.$style_sn.'不是有效状态';
	        Util::jsonExit($result);
	    }
	    $newdo['goods_name'] = $style['style_name'];
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

		$id =  _Request::getInt("id");	   
	    if(empty($id)){
	        $result['error'] = '参数错误：id 为空！';
	        Util::jsonExit($result);
	    }

        //$style_sn=trim(_Post::getString("style_sn"));
		//$style_sn=trim(_Post::get("style_sn"));
	    $model = new HuangouGoodsModel($id,27);
	    //$view = new HuangouGoodsView($model);
	   /*  $style=$model->getStyleInfo($style_sn);
	    if(empty($style)){
	        $result['error'] = '找不到款式'.$style_sn;
	        Util::jsonExit($result);	    	
	    }
	    if($style['check_status']!=3){
	        $result['error'] = '款号'.$style_sn.'不是有效状态';
	        Util::jsonExit($result);
	    } */
       
	    $olddo = $model->getDataObject();	    
	    if(empty($olddo)){
	        $result['error'] = '编辑对象不存在，可能已被删除！';
	        Util::jsonExit($result);
	    }else if($olddo['status']!=1){
	        $result['error'] = '商品已被停用，不能编辑！';
	        Util::jsonExit($result);
	    }

		
		$newmodel =  new HuangouGoodsModel($id,28);

		$olddo = $newmodel->getDataObject();
        $newdo = array(
        	"id"=>$id,
             "channel_id"=>_Post::get("channel_id"),
             //"style_sn"=>$style_sn,
             //'goods_name'=>$style['style_name'],
             "sale_price"=>_Post::get("sale_price"),
             "label_price"=>_Post::get("label_price"),             
             "update_user"=>$_SESSION['userName'],
             "update_time"=>date("Y-m-d H:i:s")           
             
        );  
		
		$res = $newmodel->saveData($newdo,$olddo);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '操作成功';
		}
		else
		{
			$result['error'] = '修改失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	启用，
	 */
	public function enabled ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new HuangouGoodsModel($id,28);
		$do = $model->getDataObject();
		
		$model->setValue('status',1);
		$res = $model->save(true);
		//联合删除？
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "启用失败";
		}
		Util::jsonExit($result);
	}
	/**
	 *	禁用，
	 */
	public function disabled ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new HuangouGoodsModel($id,28);
		$do = $model->getDataObject();
		
		$model->setValue('status',0);
		$res = $model->save(true);
		
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "禁用失败";
		}
		Util::jsonExit($result);
	}	
}

?>