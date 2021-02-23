<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseLzDiscountConfigController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-19 15:37:36
 *   @update	:
 *  -------------------------------------------------
 */
class BaseLzDiscountConfigController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $discountView = new BaseLzDiscountConfigView(new BaseLzDiscountConfigModel(99));
        $type = $discountView->get_diamond_type();
        $userModel = new UserChannelModel(1);
        $userInfo = $userModel->getUsers();
		$this->render('base_lz_discount_config_search_form.html',array('bar'=>Auth::getBar(),
                'type'=>$type,
                'userinfo'=>$userInfo
            ));
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
            'user_id[]' => _Request::getList("user_id"),
            'type' => _Request::getInt("type"),
            'enabled' => _Request::getString("enabled"),
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
        $where = array(
            'user_id' => _Request::getList("user_id"),
            'type' => $args['type'],
            'enabled' => $args['enabled']
        );
        $userModel = new UserModel(1);
		$model = new BaseLzDiscountConfigModel(99);
        $discountView = new BaseLzDiscountConfigView($model);
		$data = $model->pageList($where,$page,10,false);
        
        foreach ($data['data'] as $key=>$val){
            $user_id = $val['user_id'];
            $name = $userModel->getAccount($user_id);
            $data['data'][$key]['name']= $name;
        }
        
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'base_lz_discount_config_search_page';
		$this->render('base_lz_discount_config_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
            'type' => $discountView->get_diamond_type()
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
        $userModel = new UserChannelModel(1);
        $userInfo = $userModel->getUsers();
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('base_lz_discount_config_info.html',array(
			'view'=>new BaseLzDiscountConfigView(new BaseLzDiscountConfigModel(99)),
            'userinfo'=>$userInfo
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
        $userModel = new UserChannelModel(1);
        $userInfo = $userModel->getUsers();
        $newmodel = new BaseLzDiscountConfigModel(99);
        $discount_info = $newmodel->getDiscountByWhere(array('id'=>$id));
        $user_id = $discount_info[0]['user_id'];
        $userModel = new UserModel(1);
        $name = $userModel->getAccount($user_id);
        
        $discount_data = $newmodel->getDiscountByWhere(array('user_id'=>$user_id));
        $new_discount_data = array();
        foreach($discount_data as $val){
            $new_discount_data[$val['type']] = $val['zhekou'];
            $new_discount_data['name'] = $name;
        }
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('base_lz_discount_config_info.html',array(
			'view'=>new BaseLzDiscountConfigView(new BaseLzDiscountConfigModel($id,99)),
			'tab_id'=>$tab_id,
            'data'=>$new_discount_data,
            'userinfo'=>$userInfo
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
		$this->render('base_lz_discount_config_show.html',array(
			'view'=>new BaseLzDiscountConfigView(new BaseLzDiscountConfigModel($id,99)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$user_id = _Request::getList('user_id');
        $zhekou_1  = _Request::getFloat('zhekou_1');
        $zhekou_2  = _Request::getFloat('zhekou_2');
        $zhekou_3  = _Request::getFloat('zhekou_3');
        $zhekou_4  = _Request::getFloat('zhekou_4');
        $zhekou_5  = _Request::getFloat('zhekou_5');
        $zhekou_6  = _Request::getFloat('zhekou_6');
        $zhekou_7  = _Request::getFloat('zhekou_7');
        $zhekou_8  = _Request::getFloat('zhekou_8');
        $zhekou_9  = _Request::getFloat('zhekou_9');
        $zhekou_10 = _Request::getFloat('zhekou_10');
        $zhekou_11 = _Request::getFloat('zhekou_11');
        $zhekou_12 = _Request::getFloat('zhekou_12');
        $zhekou_13 = _Request::getFloat('zhekou_13');
        $zhekou_14 = _Request::getFloat('zhekou_14');
        $zhekou_15 = _Request::getFloat('zhekou_15');
        $zhekou_16 = _Request::getFloat('zhekou_16');
        $zhekou_17 = _Request::getFloat('zhekou_17');
		$olddo = array();
        
        $newmodel =  new BaseLzDiscountConfigModel(99);
        //先判断用户是否存在
        /*$userModel = new UserModel(1);
        $ishave = $userModel->getAccountId($user_name);
        if(!$ishave){
            $result['error'] = '用户不存在';
            Util::jsonExit($result);
        }*/
        $userId = array();
        if(!empty($user_id)) {
            foreach ($user_id as $key => $val) {
                $r = explode('|', $val);
                $userId[$r[0]] = $r[1];
            }
        }else{
            $result['error'] = '请选择用户！';
            Util::jsonExit($result);
        }
        for($i=1;$i<18;$i++){
            $tmp_str = "zhekou_".$i;
            $nn = $$tmp_str;
            if($nn<0.01 || $nn >1){
                $result['error'] = '折扣范围在0.01到1之间';
                Util::jsonExit($result);
            }
        }
        $pdo=$newmodel->db()->db();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
        $pdo->beginTransaction(); //开启事务       
        foreach ($userId as $id => $name) {
            for($i=1;$i<18;$i++){
                $tmp_str = "zhekou_".$i;
                $nn = $$tmp_str;
                $where = array('user_id'=>$id,'type'=>$i);
                $discount_info = $newmodel->getDiscountByWhere($where);
                if($discount_info){
                    /*
                    $result['error'] = '已存在‘'.$name.'’裸钻折扣。';
                    $pdo->rollback(); //事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
                    Util::jsonExit($result);
                    */
                    //$id = $discount_info[0]['id'];
                    //$model = new BaseLzDiscountConfigModel($id,20);
                    //$model->setValue('zhekou',$nn);
                    //$res = $model->save(true);
                    
                }else{
                    $newdo = array('user_id'=>$id,'type'=>$i,'zhekou'=>$nn, 'enabled'=>1);
                    $res = $newmodel->saveData($newdo,$olddo);
                }
            }    
        }
		
		if($res !== false)
		{
			$result['success'] = 1;
            $pdo->commit(); //事务提交
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交 
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
        $user_name = _Request::getString('user_name');
        $zhekou_1  = _Request::getFloat('zhekou_1');
        $zhekou_2  = _Request::getFloat('zhekou_2');
        $zhekou_3  = _Request::getFloat('zhekou_3');
        $zhekou_4  = _Request::getFloat('zhekou_4');
        $zhekou_5  = _Request::getFloat('zhekou_5');
        $zhekou_6  = _Request::getFloat('zhekou_6');
        $zhekou_7  = _Request::getFloat('zhekou_7');
        $zhekou_8  = _Request::getFloat('zhekou_8');
        $zhekou_9  = _Request::getFloat('zhekou_9');
        $zhekou_10 = _Request::getFloat('zhekou_10');
        $zhekou_11 = _Request::getFloat('zhekou_11');
        $zhekou_12 = _Request::getFloat('zhekou_12');
        $zhekou_13 = _Request::getFloat('zhekou_13');
        $zhekou_14 = _Request::getFloat('zhekou_14');
        $zhekou_15 = _Request::getFloat('zhekou_15');
        $zhekou_16 = _Request::getFloat('zhekou_16');
        $zhekou_17 = _Request::getFloat('zhekou_17');

		$id = _Post::getInt('id');

        $userModel = new UserModel(1);
        $user_id = $userModel->getAccountId($user_name);
        if($user_id == ''){
            $result['error'] = '无法查找到用户ID！';
            Util::jsonExit($result);
        }
         for($i=1;$i<18;$i++){
            $tmp_str = "zhekou_".$i;
            $nn = $$tmp_str;
            if($nn<0.01 || $nn >1){
                $result['error'] = '折扣范围在0.01到1之间';
                Util::jsonExit($result);
            }
        }

         for($i=1;$i<18;$i++){
            $tmp_str = "zhekou_".$i;
            $nn = $$tmp_str;

            $where = array('user_id'=>$user_id,'type'=>$i);

            $newmodel = new BaseLzDiscountConfigModel(99);
            $discount_info = $newmodel->getDiscountByWhere($where);
            if($discount_info){
                $id = $discount_info[0]['id'];
                $newmodel = new BaseLzDiscountConfigModel($id,99);
                $olddo = $newmodel->getDataObject();
                $newdo = array('id'=>$id,'user_id'=>$user_id,'type'=>$i,'zhekou'=>$nn);
                $res = $newmodel->saveData($newdo,$olddo);
            }else{
                $newdo = array('user_id'=>$user_id,'type'=>$i,'zhekou'=>$nn, 'enabled'=>1);
                $res = $newmodel->saveData($newdo,$olddo);
            }
        }

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
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
        $ids = _Request::getList('_ids');
        foreach ($ids as $key => $id) {
            $model = new BaseLzDiscountConfigModel($id,99);
            $do = $model->getDataObject();
            $enabled = $do['enabled'];
            $model->setValue('enabled',0);
            $res = $model->save(true);
        }
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
    
    
    /**
	 *	recover，启用
	 */
	public function recover ($params)
	{
		$result = array('success' => 0,'error' => '');
		$ids = _Request::getList('_ids');
        foreach ($ids as $key => $id) {
            $model = new BaseLzDiscountConfigModel($id,99);
            $do = $model->getDataObject();
            $enabled = $do['enabled'];
            $model->setValue('enabled',1);
            $res = $model->save(true);
        }
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}
    
}

?>