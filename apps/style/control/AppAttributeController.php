<?php
/**
 *  -------------------------------------------------
 *   @file		: AppAttributeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 10:23:29
 *   @update	:
 *  -------------------------------------------------
 */
class AppAttributeController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('app_attribute','front',11);	//生成模型后请注释该行
		//Util::V('app_attribute',11);    //生成视图后请注释该行
               
		$this->render('app_attribute_search_form.html',array('bar'=>Auth::getBar()));
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
                        'attribute_name'=>  _Request::get('attribute_name'),
                        'attribute_status'=>  _Request::get('attribute_status'),
		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array(
                     'attribute_name'=> $args['attribute_name'],
                    'attribute_status'=> $args['attribute_status']
                );

                $appAttributeModel = new AppAttributeModel(11);
                $show_type = $appAttributeModel->_show_type_arr;
               // var_dump($show_type);die;
		$model = new AppAttributeModel(11);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_attribute_search_page';
		$this->render('app_attribute_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
                        'show_type_arr'=>$show_type,
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
                $appAttributeModel = new AppAttributeModel(11);
                $show_type = $appAttributeModel->_show_type_arr;
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_attribute_info.html',array(
			'view'=>new AppAttributeView(new AppAttributeModel(11)),'show_type'=>$show_type	));
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
		
		$model = new AppAttributeModel($id,11);
		$attrExtData = $model->getAttributeExt($id);
		
		$result['content'] = $this->fetch('app_attribute_info.html',array(
			'view'=>new AppAttributeView($model),
		    'show_type'=>$model->_show_type_arr,
		    'attrExtData'=>$attrExtData,	
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
		$this->render('app_attribute_show.html',array(
			'view'=>new AppAttributeView(new AppAttributeModel($id,11))
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$attribute_name = _Post::getString('attribute_name');
        $show_type = _Post::getInt('show_type');
        $attribute_code = _Post::getString('attribute_code');
        $attribute_status = _Request::getString('attribute_status');
        $attr_show_name = _Post::getString('attr_show_name');
        $is_diamond_attr = _Post::getInt('is_diamond_attr');
        $require_confirm = _Post::getInt('require_confirm');
        $is_price_conbined = _Post::getInt('is_price_conbined');
        $attribute_unit  = _Post::getString('attribute_unit');
        $attribute_sort  = _Post::getInt('attribute_sort');
		$olddo = array();
		$newdo=array(
                    'attribute_name'=>$attribute_name,
                    'attribute_code'=>$attribute_code,
                    'create_time'=>date("Y-m-d H:i:s"),
                    'attribute_status'=>$attribute_status,
                    'create_user'=>$_SESSION['userName'],
                    'show_type'=>$show_type,
                );

		$newmodel =  new AppAttributeModel(12);
		$pdo = $newmodel->db()->db();
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
		$pdo->beginTransaction();//开启事务
		
		$ret= $newmodel->getAttributeName($attribute_name,$attribute_code);
		if($ret&&strtolower($ret['attribute_name'])==strtolower($attribute_name)){
			$result['error'] = "属性名已经存在!";
			Util::jsonExit($result);
		}
		 if($ret&&strtolower($ret['attribute_code'])==strtolower($attribute_code)){
			$result['error'] = "属性编码已经存在!";
			Util::jsonExit($result);
		}
		$extModel = new AppAttributeExtModel(12);
		try{
    		$attr_id = $newmodel->saveData($newdo,$olddo);
    		$newdoExt  = array(
    		    'attribute_id'=> $attr_id,
    		    'attr_show_name' =>$attr_show_name,
    		    'is_diamond_attr'=>$is_diamond_attr,
    		    'require_confirm'=>$require_confirm,
    		    'is_price_conbined'=>$is_price_conbined,
    		    'attribute_unit'=>$attribute_unit,
    			'attribute_sort'=>$attribute_sort,
    		);		
    		$extModel->insert($newdoExt);
    		$result['success'] = 1;
    		$pdo->commit();
    		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
    	}catch (Exception $e){
		    $result['error'] = '添加失败';
		    $pdo->rollback();//事务回滚
		    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
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
		$attribute_name = _Post::getString('attribute_name');
        $attribute_code = _Post::getString('attribute_code');
        $attribute_status = _Post::getInt('attribute_status');
        $show_type = _Post::getInt('show_type');
        $attr_show_name = _Post::getString('attr_show_name');
        $is_diamond_attr = _Post::getInt('is_diamond_attr');
        $require_confirm = _Post::getInt('require_confirm');
        $is_price_conbined = _Post::getInt('is_price_conbined');
        $attribute_unit  = _Post::getString('attribute_unit');
        $attribute_sort  = _Post::getInt('attribute_sort');
        
		$newmodel =  new AppAttributeModel($id,12);
		
		$pdo = $newmodel->db()->db();
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
		$pdo->beginTransaction();//开启事务
		
		$olddo = $newmodel->getDataObject();
		$newdo=array(
                    'attribute_id'=>$id,
                    'attribute_name'=>$attribute_name,
                    'attribute_code'=>$attribute_code,
                    'attribute_status'=>$attribute_status,
                    'show_type'=>$show_type,
		);
		
		$newExtmodel =  new AppAttributeExtModel($id,12);
		$olddoExt = $newExtmodel->getDataObject();
		$newdoExt  = array(
    		    'attribute_id'=> $id,
    		    'attr_show_name' =>$attr_show_name,
    		    'is_diamond_attr'=>$is_diamond_attr,
    		    'require_confirm'=>$require_confirm,
		        'is_price_conbined'=>$is_price_conbined,
		        'attribute_unit'=>$attribute_unit,
				'attribute_sort'=>$attribute_sort,
    	);
        try{
            $newmodel->saveData($newdo,$olddo);
            $newExtmodel->saveData($newdoExt, $olddoExt);
            $result['success'] = 1;
            $pdo->commit();
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        }catch (Exception $e){
            $result['error'] = '修改失败';
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        }	
		
		Util::jsonExit($result);
	}

//停用
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppAttributeModel($id,12);
        $status = $model->getValue('attribute_status');
        if($status == 0){
            $result['error'] = "此状态已修改";
            Util::jsonExit($result);
        }
		$do = $model->getDataObject();
		
		$model->setValue('attribute_status',0);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "停用成功";
		}
		Util::jsonExit($result);
	}

//启用
    public function awaken ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new AppAttributeModel($id,12);
        $status = $model->getValue('attribute_status');
        if($status == 1){
            $result['error'] = "此状态已修改";
            Util::jsonExit($result);
        }
        $do = $model->getDataObject();

        $model->setValue('attribute_status',1);
        $res = $model->save(true);
        if($res !== false){
            $result['success'] = 1;
        }else{
            $result['error'] = "启用失败";
        }
        Util::jsonExit($result);
    }
    
      /**
         * 转换编码
         */
        public function createCode() {
               $value = _Request::getString('value');
               if(empty($value)){
                   exit('');
               }
               $code = Pinyin::getQianpin($value);
               echo $code;
        }
}

?>