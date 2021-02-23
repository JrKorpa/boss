<?php
/**
 *  -------------------------------------------------
 *   @file		: AppAttributeValueController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 12:45:36
 *   @update	:
 *  -------------------------------------------------
 */
class AppAttributeValueController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('app_attribute_value','front',11);	//生成模型后请注释该行
		//Util::V('app_attribute_value',11);	//生成视图后请注释该行
        $id = _Get::getInt('id');
		$this->render('app_attribute_value_search_form.html',array('view'=>new AppAttributeValueView(new AppAttributeValueModel(11)),'attribute_id'=>$id,'bar'=>Auth::getBar()));
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
            'attribute_id' => _Request::getInt('attribute_id'),
            'att_value_name' => _Request::get('att_value_name'),
            'att_value_status' => _Request::get('att_value_status')
		);
                
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
        $where['attribute_id'] = $args['attribute_id'];
        $where['att_value_name'] = $args['att_value_name'];
        $where['att_value_status'] = $args['att_value_status'];
                

		$model = new AppAttributeValueModel(11);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_attribute_value_search_page';
		$this->render('app_attribute_value_search_list.html',array(
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
		$result['content'] = $this->fetch('app_attribute_value_info.html',array(
			'view'=>new AppAttributeValueView(new AppAttributeValueModel(11))
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
                //print_r($_REQUEST);die;
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_attribute_value_info.html',array(
			'view'=>new AppAttributeValueView(new AppAttributeValueModel($id,11))
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
		$this->render('app_attribute_value_show.html',array(
			'view'=>new AppAttributeValueView(new AppAttributeValueModel($id,11))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
                $att_value_name = _Post::getString('att_value_name');
                
                $attribute_id = _Post::getInt('attribute_id');
		if(!$attribute_id)
		{
			$result['error'] ="请选择对应属性！";
			Util::jsonExit($result);
		}
		
		$olddo = array();
		$newdo=array(
                    'attribute_id'=>$attribute_id,
                    'att_value_name'=>$att_value_name,
                    'att_value_status'=>1,
                    'create_time'=>date("Y-m-d H:i:s"),
                    'create_user'=>$_SESSION['userName']
                );
               
		$newmodel =  new AppAttributeValueModel(12);
		
		$ret= $newmodel->getAttributeName($att_value_name,$attribute_id);
		if($ret&&strtolower($ret['att_value_name'])==strtolower($att_value_name)){
			$result['error'] = "属性值名已经存在!";
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
                $att_value_name = _Post::getString('att_value_name');
                $att_value_status = _Post::getInt('att_value_status');
                $attribute_id = _Post::getInt('attribute_id');
                if(!$attribute_id)
		{
			$result['error'] ="请选择对应属性！";
			Util::jsonExit($result);
		}
		$newmodel =  new AppAttributeValueModel($id,12);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
                    'attribute_id'=>$attribute_id,
                    'att_value_id'=>$id,
                    'att_value_name'=>$att_value_name,
                    'att_value_status'=>$att_value_status,
                    'create_time'=>date("Y-m-d H:i:s"),
                    'create_user'=>$_SESSION['userName']
		);

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
		$model = new AppAttributeValueModel($id,12);
        $status = $model->getValue('att_value_status');
        if($status == 0){
            $result['error'] = "此状态已修改";
            Util::jsonExit($result);
        }
		$model->setValue('att_value_status',0);

		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

    public function awaken ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new AppAttributeValueModel($id,12);
        $status = $model->getValue('att_value_status');
        if($status == 1){
            $result['error'] = "此状态已修改";
            Util::jsonExit($result);
        }
        $do = $model->getDataObject();

        $model->setValue('att_value_status',1);
        $res = $model->save(true);


        if($res !== false){
            $result['success'] = 1;
        }else{
            $result['error'] = "启用失败";
        }
        Util::jsonExit($result);
    }
}

?>