<?php
/**
 *  -------------------------------------------------
 *   @file		: AppLzDiscountGrantController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-19 18:32:50
 *   @update	:
 *  -------------------------------------------------
 */
class AppLzDiscountGrantController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        $discountView = new AppLzDiscountGrantView(new AppLzDiscountGrantModel(99));
        $type = $discountView->get_diamond_type();
		$this->render('app_lz_discount_grant_search_form.html',array('bar'=>Auth::getBar(),
                'type'=>$type));
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
			'username' => _Request::getString("username"),
            'type' => _Request::getInt("type"),
            'createtime' => _Request::getString("createtime"),
            'endtime' => _Request::getString("endtime"),
            'start_time' => _Request::getString("start_time"),
            'end_time' => _Request::getString("end_time"),
            'user_id' => $_SESSION['userId']
		);
		$page = _Request::getInt("page",1);
        $userModel = new UserModel(1);
        $user_id = $userModel->getAccountId($args['username']);
		$where = array(
            'user_id' => $user_id,
            'type' => $args['type'],
            'createtime' => $args['createtime'],
            'endtime' => $args['endtime'],
            'start_time' => $args['start_time'],
            'end_time' => $args['end_time'],
            'user_id' => $args['user_id']
        );
        
        $userModel = new UserModel(1);
        
		$model = new AppLzDiscountGrantModel(99);
		$data = $model->PageList($where,$page,10,false);
        $baseview = new BaseLzDiscountConfigView(new BaseLzDiscountConfigModel(99));
        $dimond_type = $baseview->get_diamond_type();
        foreach ($data['data'] as $key=>$val){
            $user_id = $val['user_id'];
            $name = $userModel->getAccount($user_id);
            $data['data'][$key]['user_name']= $name;
            $data['data'][$key]['zhekou']= $val['zhekou']*100;
            
            $type = $val['type'];
            $data['data'][$key]['type_name'] = $dimond_type[$type];
        }
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_lz_discount_grant_search_page';
		$this->render('app_lz_discount_grant_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
            'view_sale'=>$baseview,
		));
	}
    
	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
        $username = $_SESSION['userName'];
		$result['content'] = $this->fetch('app_lz_discount_grant_info.html',array(
			'view'=>new AppLzDiscountGrantView(new AppLzDiscountGrantModel(99)),
            'view_sale'=>new BaseLzDiscountConfigView(new BaseLzDiscountConfigModel(99)),
            'username'=>$username
		));
		$result['title'] = '授权';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
        $a = new BaseLzDiscountConfigView(new BaseLzDiscountConfigModel(99));
        
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
        $newmodel = new BaseLzDiscountConfigModel(99);
        $discount_data = $newmodel->getDiscountByWhere(array('user_id'=>$id));
        
		$result['content'] = $this->fetch('app_lz_discount_grant_info.html',array(
			//'view'=>new AppLzDiscountGrantView(new AppLzDiscountGrantModel(19)),
			'tab_id'=>$tab_id,
            'user_id'=>$id,
            'view_sale'=>new BaseLzDiscountConfigView(new BaseLzDiscountConfigModel(99)),
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
		$this->render('app_lz_discount_grant_show.html',array(
			'view'=>new AppLzDiscountGrantView(new AppLzDiscountGrantModel($id,99)),
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
        $type = _Request::getInt('type');
        $mima = _Request::getString('mima');
        $zhekou = _Request::getFloat('zhekou')/100;
        $now_time = time();
        $over_time = $now_time + 10800;//30分钟过期
       
        $create_time = date("Y-m-d H:i:s",$now_time);
        $end_time = date("Y-m-d H:i:s",$over_time);
        if($mima == ''){
            $result['error'] = '请生成折扣密码！';
            Util::jsonExit($result);
        }
        $username = $_SESSION['userName'];
        if($user_name != $username){
            $result['error'] = '只能给自己授权！';
            Util::jsonExit($result);die;
        }
        $userModel = new UserModel(1);
        $user_id = $userModel->getAccountId($user_name);
        
        $dicountModel = new AppLzDiscountGrantModel(99);
        //先判断此用户是否已经存在密码
        $where = array('user_id'=>$user_id,'type'=>$type,'zhekou'=>$zhekou,'status'=>1,'time'=>date("Y-m-d H:i:s"),'mima'=>$mima);
        $info = $dicountModel->checkCode($where);
        if($info){
            $result['error'] = '此密码仍然可用,不需要保存！';
            Util::jsonExit($result);die;
        }
		$olddo = array();
		$newdo=array(
            'user_id'=>$user_id,
            'type'=>$type,
            'zhekou'=>$zhekou,
            'mima'=>$mima,
            'create_user_id'=>$_SESSION['userId'],
            'create_user'=>$_SESSION['userName'],
            'createtime'=>$create_time,
            'endtime'=>$end_time,
        );
        
        //判断密码是否已经存在
		$newmodel =  new AppLzDiscountGrantModel(99);
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
        
		$newmodel =  new AppLzDiscountGrantModel($id,99);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
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
		$model = new AppLzDiscountGrantModel($id,99);
		$do = $model->getDataObject();
		$status = $do['status'];
		if($status == 4)
		{
			$result['error'] = "此数据已经作废";
			Util::jsonExit($result);
		}
		if($status == 2)
		{
			$result['error'] = "此数据已经使用";
			Util::jsonExit($result);
		}
		
		$model->setValue('status',4);
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
    
    /*
     * 生成code
     */
    public function createCode(){
        $result = array('success' => 0,'error' => '');
        $type = _Request::getInt('type');
        $user_name = _Request::getString('user_name');
        $zhekou = _Request::getFloat('zhekou');
        $newmodel = new BaseLzDiscountConfigModel(99);
        $userModel = new UserModel(1);
        if(empty($user_name)){
            $result['error'] = "销售顾问不能为空";
            Util::jsonExit($result);
        }
        
        if(empty($zhekou)){
            $result['error'] = "折扣不能为空";
            Util::jsonExit($result);
        }
        
        $user_id = $userModel->getAccountId($user_name);
        if(empty($user_id)){
            $result['error'] = "此销售顾问不能存在";
            Util::jsonExit($result);
        }
        
        $discount_data = $newmodel->getDiscountByWhere(array('user_id'=>$user_id,'type'=>$type,'enabled'=>1));
      
        if(empty($discount_data)){
            $result['error'] = "此销售顾问还没有开放权限";
            Util::jsonExit($result);
        }
        $user_zhekou = $discount_data[0]['zhekou']*100;
        if($zhekou < $user_zhekou){
            $result['error'] = "您只能输入：".$user_zhekou."以上的折扣";
            Util::jsonExit($result);
        }
        
        $dicountModel = new AppLzDiscountGrantModel(99);
        //先判断此用户是否已经存在密码
        $where = array('user_id'=>$user_id,'type'=>$type,'zhekou'=>$zhekou,'status'=>1,'time'=>date("Y-m-d H:i:s"));
        $info = $dicountModel->checkCode($where);
        //var_dump($info);die;
        if(empty($info)){
            $mima = $dicountModel->createCode();
            //判断此密码是否已经产生过
           // $where = array('user_id'=>$user_id,'type'=>$type,'status'=>1,'time'=>date("Y-m-d H:i:s"),'mima'=>$mima);
            //$info1 = $dicountModel->checkCode($where);
            
        }else{
            $mima=$info[0]['mima'];
        }
        
        $result['success'] = 1;
        $result['error'] = $mima;
        Util::jsonExit($result);
        
    }
}

?>