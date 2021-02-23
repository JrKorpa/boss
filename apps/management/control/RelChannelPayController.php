<?php
/**
 *  -------------------------------------------------
 *   @file		: RelChannelPayController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-10 11:50:57
 *   @update	:
 *  -------------------------------------------------
 */
class RelChannelPayController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
        if(Auth::$userType>2)
		{
			die('操作禁止');
		}
		$this->render('rel_channel_pay_search_form.html',array('bar'=>Auth::getBar()));
	}
    
    
    /*
	*	leftList,销售渠道列表
	*/
	public function leftList ()
	{
		$model = new SalesChannelsModel(1);
		$data = $model->getSalesChannelsInfo("`id`,`channel_name`",array('is_deleted'=>0));
		$this->render('rel_channel_pay_left_list.html',array('data'=>$data));
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
            'channel_id'=> _Request::getInt('channel_id')

		);
		$page = _Request::getInt("page",1);
		$where = array();
        $where['channel_id'] = $args['channel_id'];

		$model = new RelChannelPayModel(1);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'rel_channel_pay_search_page';
		$this->render('rel_channel_pay_search_list.html',array(
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
        $channel_id = _Post::getInt('id');
        $paymentModle = new PaymentModel(1);
        $paymentInfo = $paymentModle->getList();
        $result['content'] = $this->fetch('rel_channel_pay_info.html',array(
			'view'=>new RelChannelPayView(new RelChannelPayModel(1)),
            'channel_id'=>$channel_id,
            'paymentInfo'=>$paymentInfo
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
        $model = new RelChannelPayModel($id,1);
        $paymentModle = new PaymentModel(1);
        $paymentInfo = $paymentModle->getList();
		$result['content'] = $this->fetch('rel_channel_pay_info.html',array(
			'view'=>new RelChannelPayView($model),
			'channel_id'=>$model->getValue('channel_id'),
            'paymentInfo'=>$paymentInfo
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
		$this->render('rel_channel_pay_show.html',array(
			'view'=>new RelChannelPayView(new RelChannelPayModel($id,1)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$channel_id = _Post::getInt('channel_id');
        
        $pay_id = _Post::getInt('pay_id');
        if($pay_id < 1){
            $result['error'] = "请选择关联的支付方式";
			Util::jsonExit($result);
        }
        if($channel_id < 1){
            $result['error'] = "请选择选中一条左侧销售渠道";
			Util::jsonExit($result);
        }
		$olddo = array();
		$newdo=array();
        $newdo['channel_id'] = $channel_id;
        $channelModle = new SalesChannelsModel(1);
        $channel_name = $channelModle->getNameByid($channel_id);
        $newdo['channel_name'] = $channel_name;
        $newdo['pay_id'] = $pay_id;
        $payModel = new PaymentModel(1);
        $pay_name = $payModel->getNameById($pay_id);
        $newdo['pay_name'] = $pay_name;

		$newmodel =  new RelChannelPayModel(2);
        $is_check = $newmodel->check_exists($channel_id, $pay_id);
        if($is_check){
            $result['error'] = "该销售渠道和该支付方式已存在关联关系";
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
        $pay_id = _Post::getInt('pay_id');
        if($pay_id < 1){
            $result['error'] = "请选择关联的支付方式";
			Util::jsonExit($result);
        }
		$newmodel =  new RelChannelPayModel($id,2);

		$olddo = $newmodel->getDataObject();
        $newdo=array();
        $newdo['id'] = $id;
        $newdo['pay_id'] = $pay_id;
        $payModel = new PaymentModel(1);
        $pay_name = $payModel->getNameById($pay_id);
        $newdo['pay_name'] = $pay_name;
        $is_check = $newmodel->check_exists($olddo['channel_id'], $pay_id, $id);
        if($is_check){
            $result['error'] = "该销售渠道和该支付方式已存在关联关系";
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
		$model = new RelChannelPayModel($id,2);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$res = $model->delete();
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