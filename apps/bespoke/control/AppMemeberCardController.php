<?php
/**
 *  -------------------------------------------------
 *   @file		: AppMemeberCardController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 12:01:24
 *   @update	:
 *  -------------------------------------------------
 */
class AppMemeberCardController extends CommonController
{
	protected $smartyDebugEnabled = true;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('memeber_card_search_form.html',['bar'=>Auth::getBar()]);
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
			'mem_card_sn'=>_Request::get('mem_card_sn'),
			'mem_card_type'=>_Request::get('mem_card_type'),
			'mem_card_status'=>_Request::get('mem_card_status'),

		);
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['mem_card_sn'] = $args['mem_card_sn'];
		$where['mem_card_type'] = $args['mem_card_type'];
		$where['mem_card_status'] = $args['mem_card_status'];

		$model = new AppMemeberCardModel(17);
		$data = $model->pageList($where,$page,10,false);

		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_memeber_card_search_page';
		$this->render('memeber_card_search_list.html',array(
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
		$result['content'] = $this->fetch('memeber_card_info.html',array(
			'view'=>new AppMemeberCardView(new AppMemeberCardModel(18))
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
                $row = new AppMemeberCardView(new AppMemeberCardModel($id,18));
                //var_dump($row);exit;
		$result['content'] = $this->fetch('memeber_card_info.html',array(
			'view'=>new AppMemeberCardView(new AppMemeberCardModel($id,18))
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 * sales 渲染销售页面
	 */
	public function	sales($params){
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('memeber_sales_card.html',array(
			'view'=>new AppMemeberCardView(new AppMemeberCardModel($id,18))
		));
		$result['title'] = '会员卡销售';
		Util::jsonExit($result);

	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
                      //  print_r($params);exit;
		$result = array('success' => 0,'error' =>'');

		//rules验证
		$vd = new Validator();
		$vd->set_rules('mem_card_sn', '会员卡号',  'require|isNumber');
		$vd->set_rules('mem_card_level', '会员等级',  'require');
		$vd->set_rules('men_card_type', '会员类型',  'require');

		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_errors();
			Util::jsonExit($result);
		}
		//接收数据
		foreach ($_POST as $k => $v) {
			$$k = _Post::get($k);
		}

		$olddo = array();
		$newdo=array(
			'mem_card_sn'=>$mem_card_sn,
			'mem_card_level'=>$mem_card_level,
			'men_card_type'=>$men_card_type,
			'mem_card_status'=>2,//初始值 2为新卡
			'mem_card_uptime'=>time(),
			'addby_id'=>$_SESSION['userId'],
			'add_time'=>time(),
		);
                $model =  new AppMemeberCardModel(18);
                $has   = $model->hasCard($mem_card_sn);
                if ($has){
                    $result['error'] = "该会员卡号已经存在";
                    Util::jsonExit($result);
                }
		$newmodel =  new AppMemeberCardModel(18);
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
		$mem_card_status=_Post::get('mem_card_status');
		//rules验证
		$vd = new Validator();
		$vd->set_rules('mem_card_level', '会员等级',  'require');
		$vd->set_rules('men_card_type', '会员类型',  'require');
		if (!$vd->is_valid($_POST))
		{
			$result['error'] = $vd->get_errors();
			Util::jsonExit($result);
		}

		$emun = ['1','2','3','4'];
		if(!in_array($mem_card_status,$emun)){
			$result['error'] = '会员状态错误';
			Util::jsonExit($result);
		}

		//接收数据
		foreach ($_POST as $k => $v) {
			$$k = _Post::get($k);
		}


		$newmodel =  new AppMemeberCardModel($id,18);

		$olddo = $newmodel->getDataObject();
		$newdo=array(
			'id'=>$id,
			'mem_card_level'=>$mem_card_level,
			'men_card_type'=>$men_card_type,
			'mem_card_status'=>$mem_card_status,
			'mem_card_uptime'=>time(),
		);

//		print_r($newdo);exit;
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
		$model = new AppMemeberCardModel($id,18);

		$view = new AppMemeberCardView($model);
		$card_status = $view->get_mem_card_status();
		if($card_status != 4){
			$result['error'] = "此卡还未注销!";
		}else{
			$model->setValue('is_deleted',1);
			$res = $model->save(true);
			if($res !== false){
				$result['success'] = 1;
			}else{
				$result['error'] = "删除失败";
			}
		}

		Util::jsonExit($result);
	}

	/**
	 * giveMember	授予会员
	 * @param $params
	 * @return boolean
	 */
	public function giveMember($params){
		$id = intval($params['id']);
		$name = $params['member_name'];
		$tel = $params['member_tel'];
        $result = array('success' => 0,'error' => '数据错误');
        if($tel == ''){
            $result['error'] = "电话号码不能为空";
            Util::jsonExit($result);
		}
		$chrnum = '/^13[0-9]{1}[0-9]{8}$|15[012356789]{1}[0-9]{8}$|18[012356789]{1}[0-9]{8}$|14[57]{1}[0-9]$/';
		if(!preg_match($chrnum, $tel)){
			$result['error'] = "请输入正确的手机号码";
            Util::jsonExit($result);
		}
        $model = new AppMemeberCardModel($id,18);
		$view = new  AppMemeberCardView($model);
		$card_no = $view->get_mem_card_sn();

		$sql = 'UPDATE `base_member_info` SET `mem_card_sn` = "'.$card_no.'" WHERE `member_phone` = "'.$tel.'"';
        if($name != ''){
			$sql .= 'member_name ='.$name ;
		}

		$res = DB::cn(18)->db()->query($sql);

		if($res){
			$model->setValue('mem_card_status',1);
            $res = $model->save();
			if($res){
                $result['success'] = 1;
            }
		}
        Util::jsonExit($result);
	}

	/**
	 * cardlost 会员卡挂失
	 */
	public function cardlost($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppMemeberCardModel($id,18);
		$model->setValue('mem_card_status',3);
		$res = $model->save(true);
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "操作失败";
		}
		Util::jsonExit($result);
	}

	/**
	 * cardnolost 取消挂失
	 */
	public function cardnolost($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppMemeberCardModel($id,18);
		$view = new AppMemeberCardView($model);
		$status = $view->get_mem_card_status();
		if($status != 3){
			$result['error'] = "此卡还未挂失!";
		}else{
			$model->setValue('mem_card_status',1);
			$res = $model->save(true);
			if($res !== false){
				$result['success'] = 1;
			}else{
				$result['error'] = "操作失败";
			}
		}

		Util::jsonExit($result);
	}

	/**
	 * cardoff 	会员卡注销
	 */
	public function cardoff($params){
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);

		$model = new AppMemeberCardModel($id,18);
		$view = new AppMemeberCardView($model);
		$card_no = $view->get_mem_card_sn();

		$sql = 'SELECT member_id FROM `base_member_info` WHERE mem_card_sn = '.$card_no;
		$member_id = DB::cn(17)->getOne($sql);


		if($member_id === false){
			$result['error'] = "此卡正在使用,不允许注销!";
		}else{
			$model->setValue('mem_card_status',4);
			$res = $model->save(true);
//			清空会员表里的会员卡号！
//			if($res){
//				$sql = "UPDATE `base_member_info` SET mem_card_sn = '' WHERE member_id = ".$member_id;
//				$res =  DB::cn(18)->db()->exec($sql);
//			}
			if($res !== false){
				$result['success'] = 1;
			}else{
				$result['error'] = "操作失败";
			}
		}

		Util::jsonExit($result);


	}

}

?>