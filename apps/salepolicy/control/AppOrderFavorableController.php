<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderFavorableController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-17 17:11:12
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderFavorableController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('app_order_favorable_search_form.html',array('bar'=>Auth::getBar()));
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
			'order_sn' => _Request::getString("order_sn"),
			'goods_id' => _Request::getString("goods_id"),
			'check_status' => _Request::getInt("check_status"),
            'consignee' => _Request::getString("consignee"),
            'create_user' => _Request::getString("create_user")
		);
		$page = _Request::getInt("page",1);
		$where = array(
            "order_sn"=>$args['order_sn'],
            "goods_id"=>$args['goods_id'],
            "check_status"=>$args['check_status'],
            "consignee"=>$args['consignee'],
            "create_user"=>$args['create_user']
        );

		$model = new AppOrderFavorableModel(17);
		$data = $model->pageList($where,$page,10,false);
        //echo '<pre>';
        //print_r($data);die;
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_order_favorable_search_page';
		$this->render('app_order_favorable_search_list.html',array(
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
		$result['content'] = $this->fetch('app_order_favorable_info.html',array(
			'view'=>new AppOrderFavorableView(new AppOrderFavorableModel(17))
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
		$tab_id = _Request::getInt("tab_id");
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('app_order_favorable_info.html',array(
			'view'=>new AppOrderFavorableView(new AppOrderFavorableModel($id,17)),
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
		$this->render('app_order_favorable_show.html',array(
			'view'=>new AppOrderFavorableView(new AppOrderFavorableModel($id,17)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;
		$olddo = array();
		$newdo=array();

		$newmodel =  new AppOrderFavorableModel(18);
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
		echo '<pre>';
		print_r ($_POST);
		echo '</pre>';
		exit;

		$newmodel =  new AppOrderFavorableModel($id,18);

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
	 *	checkTrue，审核通过
	 */
	public function checkTrue ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppOrderFavorableModel($id,18);
		$SalesModel=new SalesModel(28);
		$do = $model->getDataObject();
		if($do['check_status'] !=1)
		{
			$result['error'] = "当前记录已操作";
			Util::jsonExit($result);
		}
        $is_check=$model->checkPermissions();
        if($is_check==0){
        	$result['error'] = "没有权限";
			Util::jsonExit($result);
        }
        //$apiSalesModel = new ApiSalesModel();
        //$goods_type = $apiSalesModel->getDetailsInfo($do['detail_id'],"`goods_type`");
        $res1=$SalesModel->getGoodsSnByGoodsId($do['detail_id']);
        if(!$res1){
        	$result['error'] = "没有此订单明细！";
        	Util::jsonExit($result);
        }
            $OrderAccount=$SalesModel->GetOrderAccountRow($do['order_id']);
           // $OrderAccount = $apiSalesModel->GetOrderAccountRow($do['order_id']);
            if($OrderAccount){
                if($OrderAccount['money_unpaid']<$do['favorable_price']){
                    $result['error'] = "优惠金额超过应付尾款金额，审核无法通过";
                    Util::jsonExit($result);                     
                }
            }else{
                    $result['error'] = "该订单无金额信息！";
                    Util::jsonExit($result);                 
            }
        
        $pdo18=$model->db()->db();
        $pdo28=$SalesModel->db()->db();
      try {  
      	$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
      	$pdo28->beginTransaction(); //开启事务
      	 
      	$pdo18->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
      	$pdo18->beginTransaction(); //开启事务
		$model->setValue('check_status',2);
        $model->setValue('check_time', date("Y-m-d H:i:s"));
		
		$res = $model->save(true);
		if($res !== false){
			

			$res2=$SalesModel->updateupdateOrderDetailFieldById($do['detail_id']);
            //$apiSalesModel->updateDetailsFields(array('id'=>$do['detail_id'],'update_fields'=>array('favorable_status'=>3)));
			if($res2){
				$result['success'] = 1;
			}else{
				
				$pdo18->rollback();//事务回滚
				$pdo18->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				$pdo28->rollback();//事务回滚
				$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				$result['error'] = "审核通过失败:更新订单金额失败";
				Util::jsonExit($result);
			}
			
		}else{
			$pdo18->rollback();//事务回滚
			$pdo18->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$pdo28->rollback();//事务回滚
			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$result['error'] = "审核通过失败";
			Util::jsonExit($result);
		}
		
      }catch(Exception $e){//捕获异常			
			$pdo18->rollback();//事务回滚
			$pdo18->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$pdo28->rollback();//事务回滚
			$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			$result['error'] = "事务异常:".$e;
			Util::jsonExit($result);
		}
		//如果没有异常，就提交事务
		$pdo18->commit();
		$pdo18->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		$pdo28->commit();
		$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		
		$OrderAccount=$SalesModel->GetOrderAccountRow($do['order_id']);
		$SalesModel->updateInvoice($do['order_id'],$OrderAccount['order_amount']);//修改发票价格
		Util::jsonExit($result);
	}
    
    
	/**
	 *	checkStop，审核驳回
	 */
	public function checkStop ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppOrderFavorableModel($id,18);
		$do = $model->getDataObject();
		if($do['check_status'] !=1)
		{
			$result['error'] = "当前记录已操作";
			Util::jsonExit($result);
		}
		$is_check=$model->checkPermissions();
		if($is_check==0){
			$result['error'] = "没有权限";
			Util::jsonExit($result);
		}
		$model->setValue('check_status',3);
        $model->setValue('check_time', date("Y-m-d H:i:s"));
		$res = $model->save(true);
		
		if($res !== false){
            $apiSalesModel = new ApiSalesModel();
            $apiSalesModel->updateDetailsFields(array('id'=>$do['detail_id'],'update_fields'=>array('favorable_status'=>4)));
			$result['success'] = 1;
		}else{
			$result['error'] = "审核驳回失败";
		}
		Util::jsonExit($result);
	}
}

?>