<?php
use vipapis\informal\_RedirectServiceClient;
/**
 *  -------------------------------------------------
 *   @file		: VirtualReturnBillController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-08 15:45:07
 *   @update	:
 *  -------------------------------------------------
 */
class VirtualReturnBillController extends CommonController
{
	protected $smartyDebugEnabled = false;
    public $chanpinxian = array('其他饰品','黄金等投资产品','素金饰品','黄金饰品及工艺品','钻石饰品','彩钻饰品','珍珠饰品','彩宝饰品','成品钻','翡翠饰品','配件及特殊包装','非珠宝');
    protected $whitelist = array('download',"printBill",'downLoadBiaoqian');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
	    $view = new VirtualReturnBillView(new VirtualReturnBillModel(21));
		$this->render('virtual_return_bill_search_form.html',array(
		    'bar'=>Auth::getBar(),
		    'view'=>$view
		));
	}

	/**
	 *	search，列表
	 */
	public function search ($params)
	{
        if(SYS_SCOPE=='zhanting' && Auth::ishop_check_close_company_type()===true){
            die('智慧门店的相关业务已暂停,请移步到智慧门店系统操作');
        }        
        $args = array(
            'mod'   => _Request::get("mod"),
            'con'   => substr(__CLASS__, 0, -10),
            'act'   => __FUNCTION__,            
            'bill_id'   => _Request::get("bill_id"),
            'bill_type'   => _Request::get("bill_type"),
            'order_sn'   => _Request::get("order_sn"),
            'apply_user'   => _Request::get("apply_user"),
            'time_start'   => _Request::get("time_start"),
            'time_end'   => _Request::get("time_end"),
            'business_type'=> _Request::get("business_type"),
            'place_company_id'=> _Request::getInt("place_company_id"),
            'place_warehouse_id'=> _Request::getInt("place_warehouse_id"),
            'no_account_gid' => _Request::get("no_account_gid"),
            'exists_account_gid'=> _Request::get("exists_account_gid"),
            'bill_status'=> _Request::get("bill_status"),
            'from_company_name'=> _Request::get("from_company_name"),
            'from_company_id'=> _Request::get("from_company_id"),
            'guest_name'=> _Request::get("guest_name"),
            'create_user'=>_Request::get("create_user"),
            'check_user'=>_Request::get("check_user"),

            //'参数' = _Request::get("参数");
        );
        $where = $args;
        $page = _Request::getInt("page",1);
		$model = new VirtualReturnBillModel(21);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'virtual_return_bill_search_page';
		$this->render('virtual_return_bill_search_list.html',array(
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
		$result['content'] = $this->fetch('virtual_return_bill_info.html',array(
			'view'=>new VirtualReturnBillView(new VirtualReturnBillModel(21))
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
        $model = new VirtualReturnBillModel($id,21);
        $g_id = $model->getValue('g_id');
        $bill_type = $model->getValue('bill_type');
        $bill_status = $model->getValue('bill_status');
        if($bill_status != 1){
            exit('已保存状态才可以编辑！');
        }
		//$result = array('success' => 0,'error' => '');
        $company=$this->getCompanyList();
        //$model_p = new ApiProModel();
        $goodsAttrModel = new GoodsAttributeModel(17);
        $caizhi_arr = $goodsAttrModel->getCaizhiList();
        $jinse_arr  = $goodsAttrModel->getJinseList();
        $apiStyleModel = new ApiStyleModel();
        $catList = $apiStyleModel->getCatTypeInfo();
        if(in_array($bill_type, array('无账换新退货单','无账维修退货单'))){
            $this->render('virtual_return_bill_info.html',array(
                'view'=>new VirtualReturnBillView($model),
                'gview'=>new VirtualReturnGoodsView(new VirtualReturnGoodsModel($g_id,21)),
                'tab_id'=>$tab_id,
                'dd'=>new DictView(new DictModel(1)),
                'company'=>$company,
                'jinse_arr'=>$jinse_arr,
                'caizhi_arr'=>$caizhi_arr,
                'catList'=>$catList,
                'chanpinxian'=>$this->chanpinxian
            ));
        }else{
            //取出明细
            //$res = $model->getbillgoods($id);
            //$sdata = array_column($res,'virtual_id');
            //$virtual_ids = implode(" ", $sdata);
            $this->render('virtual_return_bill_info_list.html',array(
                'view'=>new VirtualReturnBillView($model),
                'gview'=>new VirtualReturnGoodsView(new VirtualReturnGoodsModel($g_id,21)),
                'tab_id'=>$tab_id,
                'dd'=>new DictView(new DictModel(1)),
                'company'=>$company,
                'jinse_arr'=>$jinse_arr,
                'caizhi_arr'=>$caizhi_arr,
                'catList'=>$catList,
                //'virtual_ids'=>$virtual_ids,
                'chanpinxian'=>$this->chanpinxian
            ));
        }
		
		//$result['title'] = '编辑';
		//Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
        $model = new VirtualReturnBillModel($id,21);
        $g_id = $model->getValue('g_id');
        $id = $model->getValue('id');
        if($g_id != 1){
            $this->render('virtual_return_bill_show.html',array(
                'view'=>new VirtualReturnBillView($model),
                'gview'=>new VirtualReturnGoodsView(new VirtualReturnGoodsModel($g_id,21)),
                'bar'=>Auth::getViewBar()
            ));
        }else{
            $this->render('virtual_bill_goods_show.html',array(
                'view'=>new VirtualReturnBillView($model),
                'bar'=>Auth::getViewBar()
            ));
        }
		
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

		$newmodel =  new VirtualReturnBillModel(22);
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

		$newmodel =  new VirtualReturnBillModel($id,22);

		$olddo = $newmodel->getDataObject();
        $g_id = $olddo['g_id'];
        //var_dump($g_id);die;
        //$gnewmodel =  new VirtualReturnGoodsModel($g_id,22);

        //$golddo = $gnewmodel->getDataObject();

        $time   = date('Y-m-d H:i:s');
        $bill_no = '';
        //$order_sn = trim($params['order_sn']);

        /*if($order_sn != ''){
            //检测是否有提交订单号，有还要检测它的合法性。
            $billModel = new WarehouseBillModel(21);
            $check = $billModel->CheckOrderSn($order_sn);
            if(!$check){
                $result['error'] = "订单号 <span style='color:red;'>{$order_sn}</span> 不存在";
                Util::jsonExit($result);
            }
            //获取订单的明细
            $bing_id = array();     //搜集订单的明细id
            $arr = $billModel->GetDetailByOrderSn($order_sn);
            foreach($arr as $detail){
                $bing_id[] = $detail['goods_id'];
            }
        }*/
        //$company=$this->getCompanyList();
        //var_dump($params);die;
        $from_company_id = explode('|', $params['from_company_id']);
        $from_warehouse_id = explode('|', $params['from_warehouse_id']);
        $out_company_id = explode('|', $params['out_company_id']);
        $out_warehouse_id = explode('|', $params['out_warehouse_id']);
        $place_company_id = explode('|', $params['place_company_id']);
        $place_warehouse_id = explode('|', $params['place_warehouse_id']);
        if(empty($from_company_id[0])){
            $result['error'] = "亲^_^，请选择入库公司";
            Util::jsonExit($result);
        }
        if(empty($from_warehouse_id[0])){
            $result['error'] = "亲^_^，请选择入库仓库";
            Util::jsonExit($result);
        }
        if(empty($params['caizhi'])){
            $result['error'] = "亲^_^，请选择材质";
            Util::jsonExit($result);
        }
        if(empty($params['ingredient_color'])){
            $result['error'] = "亲^_^，请选择材质颜色";
            Util::jsonExit($result);
        }
        if(empty($params['resale_price'])){
            $result['error'] = "亲^_^，请输入零售价";
            Util::jsonExit($result);
        }
        if(empty($params['remark'])){
            $result['error'] = "亲^_^，请输入备注";
            Util::jsonExit($result);
        }
        $c_info = array(
            'id'=>$id,
            'from_company_id'=>isset($from_company_id[0]) && !empty($from_company_id[0])?$from_company_id[0]:'0',
            'from_company_name'=>$from_company_id[1],
            'from_warehouse_id'=>isset($from_warehouse_id[0]) && !empty($from_warehouse_id[0])?$from_warehouse_id[0]:'0',
            'from_warehouse_name'=>trim($from_warehouse_id[1]),
            'out_company_id'=>isset($out_company_id[0]) && !empty($out_company_id[0])?$out_company_id[0]:'0',
            'out_company_name'=>$out_company_id[1],
            'out_warehouse_id'=>isset($out_warehouse_id[0]) && !empty($out_warehouse_id[0])?$out_warehouse_id[0]:'0',
            'out_warehouse_name'=>trim($out_warehouse_id[1]),
            'express_sn'=>trim($params['express_sn']),
            'remark'=>trim($params['remark']),
        );
        //var_dump($c_info);die;
        $data = array(
            'id'=>$g_id,
            //'business_type'=>$params['business_type'],
            'guest_name'=>$params['guest_name'],
            'guest_contact'=>$params['guest_contact'],
            'gold_weight'=>$params['gold_weight'],
            'finger_circle'=>$params['finger_circle'],
            'credential_num'=>$params['credential_num'],
            'main_stone_weight'=>$params['main_stone_weight'],
            'main_stone_num'=>$params['main_stone_num'],
            'deputy_stone_weight'=>$params['deputy_stone_weight'],
            'deputy_stone_num'=>$params['deputy_stone_num'],
            'resale_price'=>$params['resale_price'],
            'out_goods_id'=>!empty($params['out_goods_id'])?$params['out_goods_id']:0,
            'exist_account_gid'=>!empty($params['exist_account_gid'])?$params['exist_account_gid']:0,
            'order_sn'=>$params['order_sn'],
            'style_sn'=>$params['style_sn'],
            'torr_type'=>!empty($params['torr_type'])?$params['torr_type']:0,
            'caizhi'=>$params['caizhi'],
            'ingredient_color'=>$params['ingredient_color'],
            'style_type'=>$params['style_type'],
            'product_line'=>$params['product_line'],
            'place_company_id'=>isset($place_company_id[0]) && !empty($place_company_id[0])?$place_company_id[0]:'0',
            'place_company_name'=>$place_company_id[1],
            'place_warehouse_id'=>isset($place_warehouse_id[0]) && !empty($place_warehouse_id[0])?$place_warehouse_id[0]:'0',
            'place_warehouse_name'=>trim($place_warehouse_id[1]),
            'without_apply_time'=>$time,
            'weixiu_fee'=>$params['weixiu_fee']/1,
            );
        //var_dump($data);die;
        $res = $newmodel->updateVirtualBillInfo($data,$c_info);
		if($res !== false)
		{
			$result['success'] = 1;
			$result['_cls'] = $_cls;
			$result['tab_id'] = $tab_id;	
			$result['title'] = '提示';
		}
		else
		{
			$result['error'] = '明细修改失败';
		}
		Util::jsonExit($result);
	}

    /**
     *  update，更新信息
     */
    public function update_pl ($params)
    {
        $result = array('success' => 0,'error' =>'');
        $_cls = _Post::getInt('_cls');
        $tab_id = _Post::getInt('tab_id');

        $id = _Post::getInt('id');

        $newmodel =  new VirtualReturnBillModel($id,22);

        $olddo = $newmodel->getDataObject();
        $g_id = $olddo['g_id'];
        $bill_type = $olddo['bill_type'];
        //var_dump($g_id);die;
        //$gnewmodel =  new VirtualReturnGoodsModel($g_id,22);

        //$golddo = $gnewmodel->getDataObject();

        $time   = date('Y-m-d H:i:s');
        $bill_no = '';
        //$order_sn = trim($params['order_sn']);

        /*if($order_sn != ''){
            //检测是否有提交订单号，有还要检测它的合法性。
            $billModel = new WarehouseBillModel(21);
            $check = $billModel->CheckOrderSn($order_sn);
            if(!$check){
                $result['error'] = "订单号 <span style='color:red;'>{$order_sn}</span> 不存在";
                Util::jsonExit($result);
            }
            //获取订单的明细
            $bing_id = array();     //搜集订单的明细id
            $arr = $billModel->GetDetailByOrderSn($order_sn);
            foreach($arr as $detail){
                $bing_id[] = $detail['goods_id'];
            }
        }*/
        //$company=$this->getCompanyList();
        //var_dump($params);die;
        $from_company_id = explode('|', $params['from_company_id']);
        $from_warehouse_id = explode('|', $params['from_warehouse_id']);
        $out_company_id = explode('|', $params['out_company_id']);
        $out_warehouse_id = explode('|', $params['out_warehouse_id']);
        //$place_company_id = explode('|', $params['place_company_id']);
        //$place_warehouse_id = explode('|', $params['place_warehouse_id']);
        if(empty($from_company_id[0]) && $bill_type == '无账调拨单'){
            $result['error'] = "亲^_^，请选择入库公司";
            Util::jsonExit($result);
        }
        if(empty($from_warehouse_id[0]) && $bill_type == '无账调拨单'){
            $result['error'] = "亲^_^，请选择入库仓库";
            Util::jsonExit($result);
        }
        if(empty($out_company_id[0])){
            $result['error'] = "亲^_^，请选择出库公司";
            Util::jsonExit($result);
        }
        if(empty($out_warehouse_id[0])){
            $result['error'] = "亲^_^，请选择出库仓库";
            Util::jsonExit($result);
        }
        if(empty($params['remark'])){
            $result['error'] = "亲^_^，请输入备注";
            Util::jsonExit($result);
        }
        $c_info = array(
            'id'=>$id,
            'from_company_id'=>isset($from_company_id[0]) && !empty($from_company_id[0])?$from_company_id[0]:'0',
            'from_company_name'=>$from_company_id[1],
            'from_warehouse_id'=>isset($from_warehouse_id[0]) && !empty($from_warehouse_id[0])?$from_warehouse_id[0]:'0',
            'from_warehouse_name'=>trim($from_warehouse_id[1]),
            'out_company_id'=>isset($out_company_id[0]) && !empty($out_company_id[0])?$out_company_id[0]:'0',
            'out_company_name'=>$out_company_id[1],
            'out_warehouse_id'=>isset($out_warehouse_id[0]) && !empty($out_warehouse_id[0])?$out_warehouse_id[0]:'0',
            'out_warehouse_name'=>trim($out_warehouse_id[1]),
            'express_sn'=>trim($params['express_sn']),
            'remark'=>trim($params['remark']),
        );
        $virtual_id = trim($params['virtual_id']);
        if(empty($virtual_id)){
            $result['error'] = "请输入需要调拨的无帐修退流水号，批量请换行隔开";
            Util::jsonExit($result);
        }
        $model = new NoaccountMoveBillModel(22);
        $virtual_id = str_replace(' ',',',$virtual_id);
        $virtual_id = str_replace('，',',',$virtual_id);
        $virtual_id = str_replace(array("\r\n", "\r", "\n"),',',$virtual_id);
        $virtual_id_arr = explode(",", $virtual_id);
        //检查流水号是否存在
        $goodslist = array();
        foreach ($virtual_id_arr as $key => $virtual_id) {
            $rest = $model->checkVirtualid($virtual_id);
            if(empty($rest)){
                $result['error'] = "流水号".$virtual_id."不存在！请填写正确的流水号";
                Util::jsonExit($result);
            }
            if($rest['return_status'] == 3){
                $result['error'] = "无账修退流水号".$virtual_id."已发货！请填写正确的流水号";
                Util::jsonExit($result);
            }
            if($rest['place_company_id'] != $c_info['out_company_id'] && $bill_type == '无账调拨单'){
                $result['error'] = "无账修退流水号".$virtual_id."所在公司与出库公司不匹配，不能制调拨单！";
                Util::jsonExit($result);
            }
            if($rest['place_warehouse_id'] != $c_info['out_warehouse_id'] && $bill_type == '无账调拨单'){
                $result['error'] = "无账修退流水号".$virtual_id."所在仓库与出库仓不匹配，不能制调拨单！";
                Util::jsonExit($result);
            }
            $chekBill = $model->checkVirtualReturnBill($virtual_id);
            if(!empty($chekBill) && $bill_type == '无账调拨单'){
                $result['error'] = "无账修退流水号".$virtual_id."有未审核的无账修退单，不允许调拨";
                Util::jsonExit($result);
            }
            $goodslist[] = $rest;
        }
        //var_dump($c_info);die;
        /*$data = array(
            'id'=>$g_id,
            'business_type'=>$params['business_type'],
            'guest_name'=>$params['guest_name'],
            'guest_contact'=>$params['guest_contact'],
            'gold_weight'=>$params['gold_weight'],
            'finger_circle'=>$params['finger_circle'],
            'credential_num'=>$params['credential_num'],
            'main_stone_weight'=>$params['main_stone_weight'],
            'main_stone_num'=>$params['main_stone_num'],
            'deputy_stone_weight'=>$params['deputy_stone_weight'],
            'deputy_stone_num'=>$params['deputy_stone_num'],
            'resale_price'=>$params['resale_price'],
            'out_goods_id'=>$params['out_goods_id'],
            'exist_account_gid'=>$params['exist_account_gid'],
            'order_sn'=>$params['order_sn'],
            'style_sn'=>$params['style_sn'],
            'torr_type'=>$params['torr_type'],
            'ingredient_color'=>$params['ingredient_color'],
            'style_type'=>$params['style_type'],
            'product_line'=>$params['product_line'],
            'place_company_id'=>isset($place_company_id[0]) && !empty($place_company_id[0])?$place_company_id[0]:'0',
            'place_company_name'=>$place_company_id[1],
            'place_warehouse_id'=>isset($place_warehouse_id[0]) && !empty($place_warehouse_id[0])?$place_warehouse_id[0]:'0',
            'place_warehouse_name'=>trim($place_warehouse_id[1]),
            'without_apply_time'=>$time,
            );*/
        //var_dump($data);die;
        $res = $newmodel->updateVirtualBillInfoPl($goodslist,$c_info);
        if($res !== false)
        {
            $result['success'] = 1;
            $result['_cls'] = $_cls;
            $result['tab_id'] = $tab_id;    
            $result['title'] = '提示';
        }
        else
        {
            $result['error'] = '明细修改失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 二级联动 根据公司，获取选中公司下的仓库
     */
    public function getTowarehouseId()
    {
        $to_company = _Request::get('id');
        $to_company_arr =explode('|', $to_company);
        $to_company_id = $to_company_arr[0];
        $model = new NoaccountReturnBillModel(21);
        $data = $model->getWarehouseByCompany($to_company_id);
        $this->render('option.html',array(
            'data'=>$data,
            ));
    }

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new VirtualReturnBillModel($id,22);
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

    //piliang审核
    public function pl_check ($params)
    {
        $result = array('success' => 0,'error' => '');
        $ids = $params['_ids'];
        foreach ($ids as $key => $id) {
            $model = new VirtualReturnBillModel($id,22);
            $do = $model->getDataObject();
            $g_id = $do['g_id'];
            $model->setValue('bill_status',2);
            $model->setValue('check_time',date('Y-m-d H:i:s'));
            $model->setValue('check_user',$_SESSION['userName']);
            $res = $model->save(true);
            $gmodel = new VirtualReturnGoodsModel($g_id,22);
            $g_do = $gmodel->getDataObject();
            if(empty($do['from_company_id'])) $do['from_company_id'] = 0;
            if(empty($do['from_warehouse_id'])) $do['from_warehouse_id'] = 0;
            $gmodel->setValue('return_status',2);
            $gmodel->setValue('place_company_id',$do['from_company_id']);
            $gmodel->setValue('place_company_name',$do['from_company_name']);
            $gmodel->setValue('place_warehouse_id',$do['from_warehouse_id']);
            $gmodel->setValue('place_warehouse_name',$do['from_warehouse_name']);
            $res = $gmodel->save(true);
        }
        //联合删除？
        //$res = $model->delete();
        if($res !== false){
            $result['success'] = 1;
        }else{
            $result['error'] = "批量审核失败";
        }
        Util::jsonExit($result);
    }

    //审核
    public function check ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new VirtualReturnBillModel($id,22);
        $do = $model->getDataObject();
        $g_id = $do['g_id'];
        //var_dump($g_id);die;
        $bill_status = $do['bill_status'];
        $bill_type = $do['bill_type'];
        if($bill_status != 1)
        {
            $result['error'] = "已保存状态才可以审核！";
            Util::jsonExit($result);
        }
        $model->setValue('bill_status',2);
        $model->setValue('check_time',date('Y-m-d H:i:s'));
        $model->setValue('check_user',$_SESSION['userName']);
        $res = $model->save(true);
        $status = 0;
            if($bill_type == '无账维修退货单' || $bill_type == '无账换新退货单')
                $status = 1;
                elseif($bill_type == '无账调拨单')
                $status = 2;
                elseif($bill_type == '无账发货单')
                    $status = 3;
        //var_dump($g_id);die;
        //if(in_array($bill_type, array('无账换新退货单','无账维修退货单'))){
            $gmodel = new VirtualReturnGoodsModel($g_id,22);
            $g_do = $gmodel->getDataObject();
            if(empty($do['from_company_id'])) $do['from_company_id'] = 0;
            if(empty($do['from_warehouse_id'])) $do['from_warehouse_id'] = 0;
            $gmodel->setValue('return_status',$status);
            $gmodel->setValue('place_company_id',$do['from_company_id']);
            $gmodel->setValue('place_company_name',$do['from_company_name']);
            $gmodel->setValue('place_warehouse_id',$do['from_warehouse_id']);
            $gmodel->setValue('place_warehouse_name',$do['from_warehouse_name']);
            $res = $gmodel->save(true);
        //}else{
            //$data = $model->getbillgoods($id);
            //$ids = array_column($data,'virtual_id');
            //foreach ($ids as $id) {
                //$gmodel = new VirtualReturnGoodsModel($g_id,22);
                //$gmodel->setValue('return_status',$status);
                //if($bill_type != '无账发货单'){
                    //$gmodel->setValue('place_company_id',$do['from_company_id']);
                    //$gmodel->setValue('place_company_name',$do['from_company_name']);
                    //$gmodel->setValue('place_warehouse_id',$do['from_warehouse_id']);
                    //$gmodel->setValue('place_warehouse_name',$do['from_warehouse_name']);
                //}else{
                    //$gmodel->setValue('place_company_id',0);
                    //$gmodel->setValue('place_company_name','');
                    //$gmodel->setValue('place_warehouse_id',0);
                    //$gmodel->setValue('place_warehouse_name','');
                //}
                //$res = $gmodel->save(true);
            //}
        //}
        //联合删除？
        //$res = $model->delete();
        if($res !== false){
            $result['success'] = 1;
        }else{
            $result['error'] = "审核失败";
        }
        Util::jsonExit($result);
    }

    //取消
    public function cancel ($params)
    {
        $result = array('success' => 0,'error' => '');
        $id = intval($params['id']);
        $model = new VirtualReturnBillModel($id,22);
        $do = $model->getDataObject();
        $bill_status = $do['bill_status'];
        if($bill_status != 1)
        {
            $result['error'] = "已保存状态才可以取消";
            Util::jsonExit($result);
        }
        $model->setValue('bill_status',3);
        $model->setValue('check_time',date('Y-m-d H:i:s'));
        $model->setValue('check_user',$_SESSION['userName']);
        $res = $model->save(true);
        //联合删除？
        //$res = $model->delete();
        if($res !== false){
            $result['success'] = 1;
        }else{
            $result['error'] = "取消失败";
        }
        Util::jsonExit($result);
    }

    /**
     *  edit，渲染修改页面
     */
    public function rotate_account ($params)
    {
        $id = intval($params["id"]);
        $tab_id = _Request::getInt("tab_id");
        $result = array('success' => 0,'error' => '');
        $result['content'] = $this->fetch('virtual_return_bill_info_rotate_account.html',array(
            'view'=>new VirtualReturnBillView(new VirtualReturnBillModel($id,21)),
            'tab_id'=>$tab_id
        ));
        $result['title'] = '转有账';
        Util::jsonExit($result);
    }

    /**
     *  update，更新信息
     */
    public function rotate_account_update ($params)
    {
        $result = array('success' => 0,'error' =>'');
        $_cls = _Post::getInt('_cls');
        $tab_id = _Post::getInt('tab_id');

        $id = _Post::getInt('id');
        $exist_account_gid = _Post::get('exist_account_gid');
        //var_dump($exist_account_gid);die;
        //echo '<pre>';
        //print_r ($_POST);
        //echo '</pre>';
        //exit;

        $newmodel =  new VirtualReturnBillModel($id,22);
        $time = date('Y-m-d H:i:s');
        $olddo = $newmodel->getDataObject();
        $g_id = $olddo['g_id'];
        if($g_id != 1){
            $model = new VirtualReturnGoodsModel($g_id,22);
            $model->setValue('exist_account_gid',$exist_account_gid);
            $model->setValue('exist_account_user',$_SESSION['userName']);
            $model->setValue('exist_account_time',$time);
            $res = $model->save(true);
        }else{
            $result['error'] = '无账修退流水号无效！';
            Util::jsonExit($result);
        }
        //$newdo=array(
        //);

        //$res = $newmodel->saveData($newdo,$olddo);
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

    //无账列表信息导出
    public function download($params)
    {
        //var_dump($params);die;
        set_time_limit(0);
        $where = array(            
            'bill_id'   => _Request::get("bill_id"),
            'bill_type'   => _Request::get("bill_type"),
            'order_sn'   => _Request::get("order_sn"),
            'apply_user'   => _Request::get("apply_user"),
            'time_start'   => _Request::get("time_start"),
            'time_end'   => _Request::get("time_end"),
            'business_type'=> _Request::get("business_type"),
            'place_company_id'=> _Request::getInt("place_company_id"),
            'place_warehouse_id'=> _Request::getInt("place_warehouse_id"),
            'no_account_gid' => _Request::get("no_account_gid"),
            'exists_account_gid'=> _Request::get("exists_account_gid"),
            'bill_status'=> _Request::get("bill_status"),
            'from_company_name'=> _Request::get("from_company_name"),
            'guest_name'=> _Request::get("guest_name"),
            //'参数' = _Request::get("参数");
        );
        
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=导出".date('YmdHi').".csv");
        header('Cache-Control: max-age=0');
        $model = new VirtualReturnBillModel(22);
        $dd =new DictModel(1);
        $title=array('单据编号','无账货号','单据状态','单据类型','业务类型','创建用户','创建时间','入库公司名称','入库仓库名称','出库公司名称','出库仓库名称','审核时间','审核用户','快递单号','转有帐操作人','转有帐时间','有帐货号','备注','订单号','货号','款号','主成色','金重','金托类型','产品线','款式分类','指圈','证书号','主石重','主石粒数','副石重','副石粒数','零售价','维修费');
        foreach ($title as $k => $v) {
            $title[$k]=iconv('utf-8', 'GB18030', $v);
        }
        echo "\"".implode("\",\"",$title)."\"\r\n";
        $page = 1;
        $pageSize=30;
        $pageCount=1;
        $recordCount = 0;
        
        $list_sql=$model->getSql($where);
        while($page <= $pageCount){
            $data = $model->db()->getPageListForExport($list_sql,array(),$page,$pageSize,false,$recordCount);
            $page ++;
            if(!empty($data['data'])){
                $recordCount = $data['recordCount'];
                $pageCount = $data['pageCount'];
                $list = $data['data'];
                if(!is_array($list) || empty($list)){
                    continue;
                }
                foreach($list as $d){

                    $bill_status = $dd->getEnum('noaccount.bill_status',$d['bill_status']);
                    $business_type = $dd->getEnum('warehouse.business_type',$d['business_type']);
                    $torr_type = $dd->getEnum('warehouse_goods.tuo_type',$d['torr_type']);

                    $temp=array();
                    $temp['id']="'".$d['id'];
                    $temp['no_account_gid']=$d['no_account_gid'];
                    $temp['bill_status']=$bill_status;
                    $temp['business_type']=$business_type;
                    $temp['bill_type']=$d['bill_type'];
                    $temp['create_user']=$d['create_user'];
                    $temp['create_time']=$d['create_time'];
                    $temp['from_company_name']=$d['from_company_name'];
                    $temp['from_warehouse_name']=$d['from_warehouse_name'];
                    $temp['out_company_name']=$d['out_company_name'];
                    $temp['out_warehouse_name']=$d['out_warehouse_name'];
                    $temp['check_time']=$d['check_time'];
                    $temp['check_user']=$d['check_user'];
                    $temp['express_sn']=$d['express_sn'];
                    $temp['exist_account_user']=$d['exist_account_user'];
                    $temp['exist_account_time']=$d['exist_account_time'];
                    $temp['exist_account_gid']=$d['exist_account_gid'];
                    $temp['remark']=$d['remark'];

                    $temp['order_sn']=$d['order_sn'];
                    $temp['goods_id']=$d['goods_id'];
                    $temp['style_sn']=$d['style_sn'];
                    $temp['ingredient_color']=$d['ingredient_color'];
                    $temp['gold_weight']=$d['gold_weight'];
                    $temp['torr_type']=$torr_type;
                    $temp['product_line']=$d['product_line'];
                    $temp['style_type']=$d['style_type'];
                    $temp['finger_circle']=$d['finger_circle'];
                    $temp['credential_num']=$d['credential_num'];
                    $temp['main_stone_weight']=$d['main_stone_weight'];
                    $temp['main_stone_num']=$d['main_stone_num'];
                    $temp['deputy_stone_weight']=$d['deputy_stone_weight'];
                    $temp['deputy_stone_num']=$d['deputy_stone_num'];
                    $temp['resale_price']=$d['resale_price'];
                    $temp['weixiu_fee']=$d['weixiu_fee'];
                    foreach ($temp as $k => $v) {
                        $temp[$k] = iconv('utf-8', 'GB18030', $v);
                    }
                    echo "\"".implode("\",\"",$temp)."\"\r\n";
                }
            }
        }
    }
    /**
     * 打印单据明细
     */
    public function printBill($params){
        $id = _Request::getInt("id");
        $model = new VirtualReturnBillModel($id,22);
        $bill_info = $model->getDataObject();
        //print_r($bill_info);exit;
        $bill_info['print_time'] = date("Y-m-d H:i:s");
        $bill_goods_list = $model->getBillGoodsList(array('id'=>$bill_info['g_id']));
        $this->render('print_bill.html',array(
            'bill_info'=>$bill_info,
            'bill_goods_list'=>$bill_goods_list,
        ));
        
    }

    public function downLoadBiaoqian($params)
    {
        //var_dump($params);die;
        set_time_limit(0);
        $where = array(
            'bill_id'   => _Request::get("bill_id"),
            'bill_type'   => _Request::get("bill_type"),
            'order_sn'   => _Request::get("order_sn"),
            'apply_user'   => _Request::get("apply_user"),
            'time_start'   => _Request::get("time_start"),
            'time_end'   => _Request::get("time_end"),
            'business_type'=> _Request::get("business_type"),
            'place_company_id'=> _Request::getInt("place_company_id"),
            'place_warehouse_id'=> _Request::getInt("place_warehouse_id"),
            'no_account_gid' => _Request::get("no_account_gid"),
            'exists_account_gid'=> _Request::get("exists_account_gid"),
            'bill_status'=> _Request::get("bill_status"),
            'from_company_name'=> _Request::get("from_company_name"),
            'guest_name'=> _Request::get("guest_name"),
            //'参数' = _Request::get("参数");
        );
    
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment;filename=导出".date('YmdHi').".csv");
        header('Cache-Control: max-age=0');
        $model = new VirtualReturnBillModel(22);
        $dd =new DictModel(1);
        $title=array('无账货号','款号','材质','材质颜色','订单号','客户姓名','客户联系方式','金重',
            '指圈','证书号','主石重','主石粒数','副石重','副石粒数','零售价','款式分类','产品线','金托类型','备注','单据类型');
        foreach ($title as $k => $v) {
            $title[$k]=iconv('utf-8', 'GB18030', $v);
        }
        echo "\"".implode("\",\"",$title)."\"\r\n";
        $page = 1;
        $pageSize=30;
        $pageCount=1;
        $recordCount = 0;
    
        $list_sql=$model->getSqlForGoods($where);
        while($page <= $pageCount){
            $data = $model->db()->getPageListForExport($list_sql,array(),$page,$pageSize,false,$recordCount);
            $page ++;
            if(!empty($data['data'])){
                $recordCount = $data['recordCount'];
                $pageCount = $data['pageCount'];
                $list = $data['data'];
                if(!is_array($list) || empty($list)){
                    continue;
                }
                foreach($list as $d){
                    $torr_type = $dd->getEnum('warehouse_goods.tuo_type',$d['torr_type']);
    
                    $temp=array();
                    $temp['无帐货号'] = $d['no_account_gid'];
                    $temp['款号'] = $d['style_sn'];
                    $temp['材质'] = $d['caizhi'];
                    $temp['材质颜色'] = $d['ingredient_color'];
                    $temp['订单号'] = $d['order_sn'];
                    $temp['客户姓名']=$d['guest_name'];
                    $temp['客户联系方式']=$d['guest_contact'];
                    $temp['金重']=$d['gold_weight'];
                    $temp['指圈']=$d['finger_circle'];
                    $temp['证书号']=$d['credential_num'];
                    $temp['主石重']=$d['main_stone_weight'];
                    $temp['主石粒数']=$d['main_stone_num'];
                    
                    $temp['副石重'] = $d['deputy_stone_weight'];
                    $temp['副石粒数'] = $d['deputy_stone_num'];
                    $temp['零售价'] = $d['resale_price'];
                    
                    $temp['款式分类']=$d['style_type'];
                    $temp['产品线']=$d['product_line'];
                    $temp['金托类型']=$torr_type;
                    $temp['备注']=$d['remark'];    
                    $temp['单据类型']=$d['bill_type'];                    
                    foreach ($temp as $k => $v) {
                        $temp[$k] = iconv('utf-8', 'GB18030', $v);
                    }
                    echo "\"".implode("\",\"",$temp)."\"\r\n";
                }
            }
        }
    }

}

?>