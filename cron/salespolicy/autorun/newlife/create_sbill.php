<?php
/**
 * used:根据base_order_info 和 app_order_details 生成销售单
 * author: lly
 * date:   20161221
**/
include('./lib/PdoModel.php');
include('./lib/OrderClassModel.php');
$orderM = new OrderClassModel();
$order_info = $orderM->getorderList();
if(empty($order_info))
{
	//echo '没有需要生成销售单的订单哟';
	//exit;
}
//定义单据表字段信息
$bill_parames = array(
	'bill_no'=>'', //'单据编号',
	'bill_type'=>'S',
	'bill_status'=>2 , //'数据字典：仓储单据状态（warehouse_in_status）/ 盘点单状态（warehouse.pandian_plan）',
	'order_sn'=>'',      //'订单号',
	'goods_num'=>1,      //'货品总数',
	'put_in_type'=> 1,   //'入库方式 默认1',
	'jiejia'=>0,         //'是否结价,默认0',
	'tuihuoyuanyin'=>0,  //'退货原因 默认为0',
	'send_goods_sn'=>'',  //'送货单号',
	'pro_id'=>'',         //'供应商ID',
	'pro_name'=>'',       //'供应商名称',
	'goods_total'=>0,    //'货总金额',
	'goods_total_jiajia'=>0, //'加价之后的货总金额',
	'shijia'=>0,        //'实际销售价格',
	'to_warehouse_id'=>0, //'入货仓ID (盘点单，该列存盘点的仓库,退货返厂单时，该字段记录出库仓)',
	'to_warehouse_name'=>'',  //'入货仓名称 (盘点单，该列存盘点的仓库)',
	'to_company_id'=>'',      //'入货公司ID',
	'to_company_name'=>'',    //'入货公司名称',
	'from_company_id'=>'',    //'出货公司id',
	'from_company_name'=>'',  //'出货公司名称',
	'bill_note'=>'',          //'备注',
	'yuanshichengben'=>0,     //'原始成本',
	'check_user'=>'admin',        //'审核人',
	'check_time'=>'',        //'审核时间',
	'create_user'=>'admin',       //'制单人',
	'create_time'=>'',       //'制单时间',
	'fin_check_status'=>'', //'财务审核状态:见数据字典',
	'fin_check_time'=>'',   //'财务审核时间',
	'to_customer_id'=>'',   //'配送公司id',
	'pifajia'=>'',          //批发价格（批发单-名义价存放，批发退货-批发价存放）,
	'company_id_from'=>'',  //'业务组织公司ID',
	'company_from'=>'',     //'业务组织公司名称',
	'from_bill_id'=>'',     //'来源单据id',
	'confirm_delivery'=>1,  //'0 未确认;1已确认',
	'is_tsyd'=>'',          //'是否经销商天生一对订单:0 不是;1 是',
	'production_manager_name'=>'', //'生产跟单人',
	'sign_user'=>'',               //'签收人',
	'sign_time'=>''               //'签收时间'
);

//定义单据货品表字段信息
$bill_goods = array(
	'pinhao'=>'',    //品号（鼎捷字段）',
	'xiangci'=>'',   //'项次(鼎捷字段)',
	'p_sn_out'=>'',  //'外部订单号（鼎捷）',
	'bill_id'=>'单据id',
	'bill_no'=>'单据编号',
	'bill_type'=>'单据类型',
	'goods_id'=>'货号',
	'goods_sn'=>'款号',
	'goods_name'=>'商品名称',
	'num'=>'数量',
	'warehouse_id'=>'',   //  '所属仓库ID(如果是盘点单的明细，则表示：盘点时,盘盈的货品需要记录)',
	'caizhi'=>'',  //'材质',
	'jinzhong'=>'',  //'金重',
	'jingdu'=>'',   //'净度',
	'jinhao'=>0,    //'金耗',
	'yanse'=>'',   //'颜色',
	'zhengshuhao'=>'', //'证书号',
	'zuanshidaxiao'=>'', //'钻石大小',
	'chengbenjia'=>'',  //'成本价',
	'mingyijia'=>'',   //'名义价',
	'xiaoshoujia'=>'', //'销售价（损益单中的退货价） （在退货单中，该字段作为退货价）',
	'in_warehouse_type'=>0, //'入库方式 0、默认无。1.购买。2、委托加工。3、代销。4、借入',
	'account'=>0,  //'是否结价0、默认无。1、未结价。2、已结价',
	'addtime'=>date('Y-m-d H:i:s'), //'添加时间',
	'pandian_status'=>0, //'盘点状态 参考数字字典',
	'guiwei'=>'',   //'货品所在柜位号',
	'detail_id'=>'',   //'销售单和退后单存订单的detail_id所用',
	'pandian_guiwei'=>'0-00-0-0', //'盘点柜位',
	'pandian_user'=>'', //'盘点人',
	'pifajia'=>0,       //批发价格（批发单-名义价存放，批发退货-批发价存放）',
	'sale_price'=>'', //'销售价',
	'shijia'=> 0,       //'实际价格',
	'yuanshichengben'=>0, // '原始采购成本',
	'bill_y_id'=>'',    //'Y单号',
	'jiajialv'=>'',    //'加价率（适用于Y单）',
	'order_sn'=>'',   //'订单号'
);
foreach($order_info as $k=>$obj)
{
	$departmentid = $obj['department_id'];
	$companyid = $orderM->getcompany($departmentid);
	$warehousedata = $orderM->getwarehouse($companyid);
	$wdata = $warehousedata[0];
	
	$billdata = array();
	$billdata = $bill_parames;
	$billno = 'S'.date('mdis').rand(10,10000);
	$billdata['bill_no'] = $billno;
	$billdata['order_sn'] = $obj['order_sn'];
	$billdata['jiejia'] = 0;
	$billdata['tuihuoyuanyin'] = 0;
	$billdata['goods_total'] = 0; //货品总金额
	$billdata['shijia'] = 0;      //
	$billdata['yuanshichengben']=0;  //原始成本
	//$billdata['create_user'] = 'admin'; //$obj['create_user'];
	$billdata['create_time'] = $obj['create_time'];
	$billdata['to_customer_id'] = 0;
	$billdata['pifajia'] = 0;
	$billdata['from_company_id'] = $wdata['company_id'] ;
	$billdata['from_company_name'] = $wdata['company_name'] ;
	
	

	//跟进订单好获取订单明细
	$detailsdata = $orderM->getdetailinfo($obj['order_sn']);    
	if(empty($detailsdata)){
		return ;	
	}
	$goods_total = 0;
	$yuanshichengben = 0;
	$goodsnum =0;
	
	$billgdatas = array();
	foreach($detailsdata as $goodsinfo)
	{
		$billg_data = array();
		$billg_data = $bill_goods;
		$billg_data['bill_id'] = '';
		$billg_data['bill_no'] = $billno;
		$billg_data['bill_type'] = 'S';
		$billg_data['goods_id'] = $goodsinfo['goods_id'] ? $goodsinfo['goods_id'] : 0;
		$billg_data['goods_sn'] = $goodsinfo['goods_sn'];
		$billg_data['goods_name'] = $goodsinfo['goods_name'];
		$billg_data['num'] = $goodsinfo['goods_count'];
		$billg_data['caizhi'] = $goodsinfo['caizhi'];
		$billg_data['jinzhong'] = $goodsinfo['jinzhong'];
		$billg_data['jingdu'] = $goodsinfo['clarity'];
		$billg_data['yanse'] = $goodsinfo['color'];
		$billg_data['zhengshuhao'] = $goodsinfo['zhengshuhao'];
		$billg_data['zuanshidaxiao'] = $goodsinfo['cart'];
		$billg_data['chengbenjia'] =  $goodsinfo['chengbenjia'];
		//名义价 (暂时取这个)
		$billg_data['mingyijia'] =  $goodsinfo['chengbenjia'];
		$billg_data['xiaoshoujia'] =  $goodsinfo['goods_price'];
		//如果优惠审核通过的话  那么销售价就是原价 - 优惠
		if($goodsinfo['favorable_status'] == 3)
		{
			$billg_data['xiaoshoujia'] =  $goodsinfo['goods_price']-$goodsinfo['favorable_price'];
		}
		$billg_data['sale_price'] = $goodsinfo['mingyichengben']; 
		$billg_data['shijia'] = $billg_data['xiaoshoujia'];
		$billg_data['pifajia'] = $goodsinfo['chengbenjia'];
		$billg_data['order_sn'] = $obj['order_sn'];
		$goods_total+= $goodsinfo['goods_price'];
		$goodsnum += $goodsinfo['goods_count'];
		$yuanshichengben += $goodsinfo['chengbenjia'];
		$billg_data = $orderM->filterarr($billg_data);
		array_push($billgdatas,$billg_data);
	}
	$billdata['goods_num'] = $goodsnum;
	$billdata['goods_total'] = $goods_total; //货品总金额
	$billdata['yuanshichengben']=$yuanshichengben;  //原始成本
	$billdata = $orderM->filterarr($billdata);
	$bill_id = $orderM->autoinsert('warehouse_shipping.warehouse_bill',$billdata);
	if($bill_id)
	{
		foreach($billgdatas as $k=>$billginfo)
		{
			$billginfo['bill_id'] = $bill_id;
			$orderM->autoinsert('warehouse_shipping.warehouse_bill_goods',$billginfo);
			unset($billgdatas[$k]);
		}
	}
}
?>