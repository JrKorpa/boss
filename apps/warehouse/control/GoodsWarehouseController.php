<?php
/**
 *  -------------------------------------------------
 *   @file		: GoodsWarehouseController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 10:55:30
 *   @update	:
 *  -------------------------------------------------
 */
class GoodsWarehouseController extends CommonController
{
	protected $smartyDebugEnabled = false;
	/** 获取公司列表 **/
	public function GetCompanyList(){
		$model     = new CompanyModel(1);
		$company   = $model->getCompanyTree();//公司列表
		return $company;
	}

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('goods_warehouse_search_form.html',array(
			'bar'=>Auth::getBar(),
			'view'=>new GoodsWarehouseView(new GoodsWarehouseModel(21)),
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
			'good_id' => _Request::get('good_id'),
			'warehouse_id[]' => _Request::getList("warehouse_id"),
			'status' => _Request::get('status'),
			'goods_sn' => _Request::get('goods_sn'),
			'box_sn' => _Request::get('box_sn'),
			'close' => _Request::get('close')
		);
                
		$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
		$where = array();
		$where['good_id'] = $args['good_id'];
		$where['warehouse_id[]'] = $args['warehouse_id[]'];
		$where['status'] = $args['status'];
		$where['goods_sn'] = $args['goods_sn'];
		$where['box_sn'] = $args['box_sn'];
		$where['close'] = $args['close'];
        if(SYS_SCOPE == 'zhanting'){
            $where['hidden'] = '0';
            $where['addtime'] = '2019-01-09 22:00:00';
        }
		if(_Request::get('wh_id')){
			array_push($where['warehouse_id[]'], _Request::get('wh_id'));
		}
		$model = new GoodsWarehouseModel(21);
		$data = $model->pageList($where,$page,10,false);
		//获取公司列表
		$arr = $this->GetCompanyList();
		$company = array();
		foreach($arr as $k){
			$company[$k['id']] = $k['company_name'];
		}
		$warehouserelModel = new WarehouseRelModel(21);
		foreach ($data['data'] as $key => &$value) {
			$value['company_id'] = $warehouserelModel->GetCompanyByWarehouseId($value['warehouse_id']);
		}

		$pageData = $data;

// echo '<pre>';print_r($data['data']);echo '</pre>';

		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'goods_warehouse_search_page';
		$this->render('goods_warehouse_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data,
			'view' => new GoodsWarehouseView(new GoodsWarehouseModel(21)),
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ($params)
	{
		$result = array('success' => 0,'error' => '');
		$result['content'] = $this->fetch('goods_warehouse_info.html',array(
			'view'=>new GoodsWarehouseView(new GoodsWarehouseModel(21)),
			'action' => 'add',
		));
		$result['title'] = '货品批量上下架';
		Util::jsonExit($result);
	}

	/**
	 *	edit，渲染修改页面
	 */
	public function edit ($params)
	{
		$id = intval($params["id"]);
		$result = array('success' => 0,'error' => '');
		$model = new GoodsWarehouseModel($id,21);

		//获取柜位列表
		$warehouse_id = $model->getValue('warehouse_id');
		$boxModel = new WarehouseBoxModel(21);
		$boxList = $boxModel->getBoxListByWarehouseID($warehouse_id);

		//根据仓库id获取仓库是否锁定(盘点)
		$this->check_warehouse_lock($warehouse_id);

		$result['content'] = $this->fetch('goods_warehouse_info.html',array(
			'view'=>new GoodsWarehouseView($model),
			'action' => 'edit',
			'boxList'=>$boxList,
		));
		$result['title'] = '货品上下架';
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$newmodel =  new GoodsWarehouseModel(22);
		$get_goods = _Request::get('good_id');

		/** 检测提交的货号是否有已经上架的 **/
		/*$is_shangjia = $newmodel->checkIsShangJia($get_goods);
		if($is_shangjia['shangjia']){
			$result['error'] = $is_shangjia['error'];
			Util::jsonExit($result);
		}*/

		if(!_Request::get('warehouse_id')){
			$result['error'] = '请选择输入货品所在的仓库';
			Util::jsonExit($result);
		}
		if(!_Request::get('box_id')){
			$result['error'] = '请选择上架的柜位';
			Util::jsonExit($result);
		}
        $get_goods=str_replace('，',' ',$get_goods);
		$get_goods=trim(preg_replace('/(\s+|,+)/',' ',$get_goods));
		$strArr = explode(' ', $get_goods);
		$warehouse = _Request::get('warehouse_id');	//要入库的仓库ID
		//检测仓库是否锁定，盘点中则不可以修改
		$this->check_warehouse_lock($warehouse);
		#1/检测提交的货号，是否在同一个仓库
		$ext = $newmodel->checkOnWarehouse($strArr, $warehouse);
		if(!$ext['type']){
			$result['error'] = $ext['error'];	//批量货品中，不是同一个仓库的货物
			Util::jsonExit($result);
		}

		#2/检测货号是否存在库goods_warehouse 中
		$data  = $newmodel->checkRepeat($strArr);
		if(!$data['type']){
			$result['error'] = $data['error'];	//批量货品中，有货品没有入库[既输入的批量货号中，有些货号不存在于表goods_warehouse 中]
			Util::jsonExit($result);
		}


		#3检测上架的柜位是否被禁止
		$box_id = _Request::get('box_id');
		$is_delete = $newmodel->checkBoxToDelete($box_id);	//获取筐位的禁用状态
		if(!$is_delete){
			$result['error'] = '您选择上架的柜位已被禁用，无法完成上架操作!';
			Util::jsonExit($result);
		}

		//上架
		$res = $newmodel->shangjia($strArr, $box_id);

		if($res !== false)
		{
			$result['success'] = 1;
		}
		else
		{
			$result['error'] = '操作失败';
		}
		Util::jsonExit($result);
	}

	/**
	 *	update，更新信息
	 */
	public function update ($params)
	{
		$result = array('success' => 0,'error' =>'');
		if(!_Request::get('box_id')){
			$result['error'] = '请选择上架的柜位';
			Util::jsonExit($result);
		}

		//根据仓库id获取仓库是否锁定(盘点)
		if(isset($params['warehouse_id']))
		{
			$this->check_warehouse_lock($params['warehouse_id']);
		}

		$newmodel =  new GoodsWarehouseModel(22);
		$get_goods = _Request::get('good_id');
                $get_goods=str_replace('，',',',$get_goods);
		$strArr = explode(',', $get_goods);
		$data  = $newmodel->checkRepeat($strArr);
		
		
		if(!$data['type']){
			$result['error'] = $data['error'];
			Util::jsonExit($result);
		}
		$box_id = $params['box_id'];
		
		$data1  = $newmodel->checkGoodsBox($strArr,$box_id);
		
		
		if(!$data1['type']){
			$result['error'] = $data1['error'];
			Util::jsonExit($result);
		}
		
		
		//上架
		$res = $newmodel->shangjia($strArr, $box_id);

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
		$model = new GoodsWarehouseModel($id,22);
		$do = $model->getDataObject();
		$valid = $do['is_system'];
		if($valid)
		{
			$result['error'] = "当前记录为系统内置，禁止删除";
			Util::jsonExit($result);
		}
		$model->setValue('is_deleted',1);
		$res = $model->save(true);
		//$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/** 联动获取柜位列表 **/
	public function getBox(){
		$id = _Request::get('id');
		$model = new WarehouseBoxModel(21);
		$data = $model->getBoxListByWarehouseID($id);
		$this->render('goods_box_option.html',array(
			'data'=>$data,
			));
	}

	//批量下架功能
	public function BatchUndercarriage($params){
		$model = new GoodsWarehouseModel(22);
		//接受货号,批量下架
		if(isset($params['get']) && $params['get'] == 1){
			$result = array('success' => 0,'error' => '');
			$goods_list = explode(',' , trim($params['goods'], ','));
			$goods_list = array_unique($goods_list);
			$res = $model->BatchUndercarriage($goods_list);
			if($res['success']){
				$result['success'] = 1;
				$result['num'] = $res['success_num'];
			}
			$result['error'] = $res['error'];
			Util::jsonExit($result);
		}

		$this->render('batch_under_carriage.html', array(
			'tab_id' => $params['tab_id'],
		));
	}
	//判断仓库是否锁定状态
    public function check_warehouse_lock($warehouse_id)
	{
		$warehouse_model  = new WarehouseModel($warehouse_id,21);
		$lock = $warehouse_model->getValue('lock');
		$name = $warehouse_model->getValue('name');
		if ($lock==1)
		{
			Util::jsonExit($name."正在盘点中，不允许上下架！");
		}
	}
        
    public function PutGoodsOn() {
        $result = array('success' => 0,'error' => '');
        $tab_id = _Request::getInt('tab_id');
        $result['content'] = $this->render('goods_on.html',array(
                'view'=>new GoodsWarehouseView(new GoodsWarehouseModel(21)),
                'action' => 'add',
                'tab_id' => $tab_id
        ));
       
    }
    //批量上架
    public function PiliangPutGoodsOn(){
        $result = array('success' => 0,'error' => '');
        $tab_id = _Request::getInt('tab_id');
        $model = new GoodsWarehouseModel(21);
        
        $result['content'] = $this->render('goods_off.html',array(
                'view'=>new GoodsWarehouseView(new GoodsWarehouseModel(21)),
                'action' => 'add',
                'tab_id' => $tab_id
        ));
       
    }
    //上架
    public function SaveData($params){
		 
		 
        $model = new GoodsWarehouseModel(21);
        $result = array('success' => 0,'error' =>'');
		
        if (isset($params['goods_id'])){
            $goods_id = $params['goods_id'];
        }
		
        if (isset($params['box_id'])){
             $box_sn = $model->GetBoxSn('', $params['box_id']);
        }
		
        $result = $model->SetGoodsOn($box_sn, $goods_id);
       
        Util::jsonExit($result);
    }
    //批量上架
    public function Updata($params){
        //print_r($params);exit;
        $model = new GoodsWarehouseModel(21);
        $box_sn = _Request::getString('box_sn');
        
        $result = array('success' => 0,'error' =>'');
        //print_r($params);exit;
        if (isset($params['goods_id'])){
            $goods_list = explode(',' , trim($params['goods_id'], ','));
	    $goods_list = array_unique($goods_list);
            
        }
        //print_r($params);exit;
        if (isset($params['warehouse'])){
            $warehouse = $params['warehouse'];
        }
       //exit($warehouse);
        $result = $model->SetPiliangGoodsOn($goods_list,$box_sn,$warehouse);
        //var_dump($result);exit;
        Util::jsonExit($result);
        
    }
    public function GetData($params){
        $goods_id = _Request::getString('goods_id');
        $type = _Request::getString('type');
        $html = '';
        $model = new GoodsWarehouseModel(21);
        if ($type == 'on') {
            $info = $model->getSameGoodsSnBoxInfo($goods_id);
            if(empty($info)){
                $html .= "<div class='control-label' style='color:red;'>未找到该货号同款的其他柜位信息</div>"; 
            }
            if(isset($info['error']) && $info['error'] == 1){
                Util::jsonExit($info);
            }else{
               $data = $model->getWarehouseAndBox($goods_id);
                //添加，如果返回的仓库有柜位，则显示下拉列表把柜位号显示出来
               if(isset($data['warehouse_id']) && $data['warehouse_id'] != ''){
                   $box_list = $model->GetBoxSn($data['warehouse_id']);
               }
                foreach ($info as $k => $v){
                    if(empty($v['box_sn'])){//如果没有柜位记录
                        break;
                        //$html .= "<div class='control-label' style='color:red;'>款号：".$v['goods_sn']." 未找到此款上架柜位号</div>";
                    }else {
                        $box_id = $model->GetBoxid($v['box_sn']);
                                                //exit($box_id)
                        $id = $v['box_sn']."|".$box_id;
                        $html .= "<div class='control-label'>款号：".$v['goods_sn']." 柜位：<a onclick='javascript:setBoxSn(this);' style='cursor:pointer;' name='".$v['warehouse']."' id='".$id."' >".$v['box_sn']."</a> 仓库：".$v['warehouse']." 数量：{$v['goods_totals']}</div> ";
                    }
                    
                }
                
            }
            $html .= "<div>&nbsp;</div><div>&nbsp;</div><div>&nbsp;</div><div>&nbsp;</div>";
            //echo $data['warehouse'].','.$data['box_sn'].','.$html; 
            echo $data['warehouse'].','.$box_list.','.$html; 
        }else{
        
            $data = $model->getWarehouseAndBox($goods_id);
            echo $data['warehouse'].','.$data['box_sn'];
        }
        
        
    }
    /**
     *	上下架，搜索框
     */
    public function index_upoffshelf ($params)
    {
    	$this->render('goods_warehouse_upoffshelf_search_form.html',array(
    			'bar'=>Auth::getBar(),
    			'view'=>new GoodsWarehouseView(new GoodsWarehouseModel(21)),
    	));
    }
    
    /**
     *	上下架search，列表
     */
    public function search_upoffshelf ($params)
    {
    	$args = array(
    			'mod'	=> _Request::get("mod"),
    			'con'	=> substr(__CLASS__, 0, -10),
    			'act'	=> __FUNCTION__,
    			'good_id' => _Request::get('good_id'),
    			'warehouse_id[]' => _Request::getList("warehouse_id"),
    			'status' => _Request::get('status'),
    			'goods_sn' => _Request::get('goods_sn'),
    			'box_sn' => _Request::get('box_sn'),
    			'close' => _Request::get('close')
    	);
    
    	$page = isset($_REQUEST["page"]) ? intval($_REQUEST["page"]) : 1 ;
    	$where = array();
    	$where['good_id'] = $args['good_id'];
    	$where['warehouse_id[]'] = $args['warehouse_id[]'];
    	$where['status'] = $args['status'];
    	$where['goods_sn'] = $args['goods_sn'];
    	$where['box_sn'] = $args['box_sn'];
    	$where['close'] = $args['close'];
    	$where['start_time'] = _Request::get('start_time');
    	$where['count_type'] = _Request::get('count_type');
    	if(_Request::get('wh_id')){
    		array_push($where['warehouse_id[]'], _Request::get('wh_id'));
    	}
    	$model = new GoodsWarehouseModel(21);
    	$data = $model->pageList($where,$page,10,false);
    	//获取公司列表
    	$arr = $this->GetCompanyList();
    	$company = array();
    	foreach($arr as $k){
    		$company[$k['id']] = $k['company_name'];
    	}
    	$warehouserelModel = new WarehouseRelModel(21);
    	foreach ($data['data'] as $key => &$value) {
    		$value['company_id'] = $warehouserelModel->GetCompanyByWarehouseId($value['warehouse_id']);
    	}
    
    	$pageData = $data;
    
    	// echo '<pre>';print_r($data['data']);echo '</pre>';
    
    	$pageData['filter'] = $args;
    	$pageData['jsFuncs'] = 'goods_warehouse_upoffshelf_search_page';
    	$this->render('goods_warehouse_upoffshelf_search_list.html',array(
    			'pa'=>Util::page($pageData),
    			'page_list'=>$data,
    			'view' => new GoodsWarehouseView(new GoodsWarehouseModel(21)),
    	));
    }
    

}/** END CLASS**/

?>