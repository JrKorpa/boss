<?php
/*
author: liulinyan
date:   2015-10-24
filename: table_data.php
used: 所涉及表的初始化
*/

/**********  app_order_details表  **********/
$detailsgoods = array(
	'order_id'=>0,
	'goods_id'=>0,
	//款号
	'goods_sn'=>0,
	//原始款号(其实就是原始货号)
	'ext_goods_sn'=>0,
	//商品名称
	'goods_name'=>'',
	//商品价格
	'goods_price'=>0,
	//优惠价格:正数代表减钱，负数代表加钱
	'favorable_price'=>0,
	//商品个数 默认为1,因为如果是多个 我也要进行拆分
	'goods_count'=>1,
	//添加时间
	'create_time'=>date("Y-m-d H:i:s"),
	//修改时间
	'modify_time'=>date("Y-m-d H:i:s"),
	//创建人  备注里面的客服
	'create_user'=>'',
	'details_status'=>'',
	//1未发货2已发货3收货确认4允许发货5已到店    默认为1
	'send_good_status'=> 1,
	//布产状态:1初始化2待分配3已分配4生产中7部分出厂9已出厂10已取消    默认为1
	'buchan_status'=>1,
	//是否是现货：1现货 0期货    默认为1 如果查不到信息就为期货0
	'is_stock_goods'=>1,
	//退货产品 0未退货1已退货
	'is_return'=> 0,
	//备注  淘宝上面的商品标签
	'details_remark'=>'',
	'cart'=> '',
	'cut'=> '',
	'clarity'=> '',
	'color'=> '',
	'cert'=> '',
	'zhengshuhao'=> '',
	'caizhi'=> '',
	'jinse'=> '',
	'jinzhong'=> '',
	'zhiquan'=> '',
	'kezi'=>'',
	//镶嵌要求
	'xiangqian'=>'成品',
	//优惠审核状态1：保存；2：提交申请；3：审核通过；4：审核驳回
	'favorable_status'=> 3,
	//款式分类
	'cat_type'=> '',
	//产品线
	'product_type'=> '',
	'xiangkou'=>'',
	'chengbenjia'=>'',
	//是否赠品0否 1.是
	'is_zp'=>0,    		//默认为0,当添加赠品的时候就是赠品
	//是否销账,2.是  1否
	'is_finance'=> 2
);

//仓库信息
$warehouse_arr=array(
	2=>'线上低值库',
	79=>'深圳珍珠库',
	96=>'总公司后库',
	184=>'黄金网络库',
	386=>'彩宝库',
	482=>'淘宝黄金',
	484=>'淘宝素金',
	486=>'线上钻饰库',
	516=>'物控库',
	672=>'轻奢库',
	673=>'彩钻库'
);
$warehouseid = implode("','",array_keys($warehouse_arr));
?>