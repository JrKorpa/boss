<?php
/**
 *  -------------------------------------------------
 *   @file		: JitxAppOrderDetailsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2019-06-28 10:36:12
 *   @update	:
 *  -------------------------------------------------
 */
class JitxAppOrderDetailsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_order_details';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"主键",
"order_id"=>"订单号",
"goods_id"=>"货号",
"goods_sn"=>" ",
"ext_goods_sn"=>"原始款号",
"goods_name"=>"商品名称",
"goods_price"=>"商品价格",
"favorable_price"=>"优惠价格:正数代表减钱，负数代表加钱",
"goods_count"=>"商品个数",
"create_time"=>"添加时间",
"modify_time"=>"修改时间",
"create_user"=>"创建人",
"details_status"=>" ",
"send_good_status"=>"1未到货2已发货3到货未检验4到货已检验5返厂",
"buchan_status"=>"布产状态:1初始化2待分配3已分配4生产中7部分出厂9已出厂10已取消",
"is_stock_goods"=>"是否是现货：1现货 0期货",
"is_return"=>"退货产品 0未退货1已退货",
"details_remark"=>"备注",
"cart"=>"石重",
"cut"=>"切工",
"clarity"=>"净度",
"color"=>"颜色",
"cert"=>"证书类型",
"zhengshuhao"=>"证书号",
"caizhi"=>"材质",
"jinse"=>"金色",
"jinzhong"=>"金重",
"zhiquan"=>"指圈",
"kezi"=>"刻字",
"face_work"=>"表面工艺",
"xiangqian"=>"镶嵌要求",
"goods_type"=>"商品类型lz:裸钻",
"favorable_status"=>"优惠审核状态；1：保存；2：提交申请；3：审核通过；4：审核驳回",
"cat_type"=>"款式分类",
"product_type"=>"产品线",
"kuan_sn"=>"天生一对的款号",
"xiangkou"=>"镶口",
"chengbenjia"=>"成本价",
"bc_id"=>" ",
"policy_id"=>"销售政策商品",
"is_peishi"=>"是否支持4C配钻，0不支持，1裸钻支持，2空托支持",
"is_zp"=>"是否赠品1.是0.否",
"is_finance"=>"是否销账,2.是.1否",
"weixiu_status"=>" ",
"allow_favorable"=>"是否允许申请优惠",
"qiban_type"=>"起版类型：见数据字典qiban_type",
"delivery_status"=>"1：未配货；2允许配货；5已配货",
"retail_price"=>"原始零售价",
"ds_xiangci"=>"单身-项次（鼎捷）",
"pinhao"=>"品号（鼎捷）",
"dia_type"=>"钻石类型（1、现货钻，2、期货钻）",
"is_cpdz"=>" ",
"tuo_type"=>" ",
"zhushi_num"=>"主石粒数",
"cpdzcode"=>" ",
"discount_point"=>"折扣积分",
"reward_point"=>"奖励积分",
"daijinquan_code"=>"代金券兑换码",
"daijinquan_price"=>"代金券优惠金额",
"daijinquan_addtime"=>"代金券兑换码使用时间",
"jifenma_code"=>"积分码",
"jifenma_point"=>"积分码赠送积分",
"zhuandan_cash"=>" ",
"goods_from"=>" ");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url JitxAppOrderDetailsController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT o.order_sn,o.consignee,o.mobile,d.* FROM  app_order_details d,base_order_info o";
		$str = " d.order_id=o.id  AND o.department_id=13 AND o.create_time>'2019-07-10 00:00:00' AND o.create_user='admin' AND ";
		if($where['order_sn'] != "")
		{
			$str .= " o.order_sn='{$where['order_sn']}' AND ";
		}
		if($where['vop_barcode'] != "")
		{
			$str .= " d.ext_goods_sn='{$where['vop_barcode']}' AND ";
		}


		if($where['bind_status']!="" and $where['bind_status']==0)
		{
			$str .= " d.goods_id ='0' AND o.order_status not in (3,4) AND ";
		}else{
			if(empty($where['bind_status']))
                $str .= " d.goods_id ='0' AND o.order_status not in (3,4) AND ";
			else
			    $str .= " d.goods_id <>'0' AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY d.id DESC";
		//echo $sql;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 *	根据订单明细号获取订单明细等商品信息
	 *
	 *	@url JitxAppOrderDetailsController/getOrderDetail
	 */
	function getOrderDetail($id)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT o.order_sn,o.consignee,o.mobile,d.* FROM  app_order_details d,base_order_info o where d.order_id=o.id  AND o.department_id=13 AND d.id='{$id}'";
		
		//echo $sql;
		$res = $this->db()->getRow($sql);
		return $res;
	}


	/**
	 *	根据订单明细号获取订单明细等商品信息
	 *
	 *	@url JitxAppOrderDetailsController/getOrderDetail
	 */
	function getUnbindGoods($params,$warehouse='')
	{
		//不要用*,修改为具体字段
		$sql = "SELECT g.*  FROM  warehouse_shipping.warehouse_goods g where (ifnull(g.order_goods_id,0)='0' or g.order_goods_id='') AND g.is_on_sale=2 AND g.company_id=58 ";
		
		if($params['goods_sn']<>'')
			$sql .= " AND g.goods_sn='{$params['goods_sn']}' ";

		if($params['goods_id']<>'')
			$sql .= " AND g.goods_id='{$params['goods_id']}' ";

		if($warehouse<>"unlock")
			$sql .=" AND g.warehouse_id in (546,702) ";

		$sql .= " limit 20";
		
		//echo $sql;
		$res = $this->db()->getAll($sql);
		return $res;
	}

	/**
	 *	绑定货号到订单明细
	 *
	 *	@url JitxAppOrderDetailsController/bindGoods
	 */
	function bindGoods($detail_order_id,$goods_id){
		$cancel_order = $this->db()->getRow("select o.order_sn from base_order_info o,app_order_details d where o.id=d.order_id and d.id='{$detail_order_id}' and o.order_status in (3,4)");
		if(!empty($cancel_order))
			return "订单已被取消";
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务 
            $row = $this->db()->getRow("select id,goods_id from app_order_details where id='{$detail_order_id}' for update");
            if(empty($row)){
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//             
				return "订单不存在";
            }
            if(!empty($row['goods_id'])){
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//             
				return "订单已被他人绑定";            	
            }
            $goods = $this->db()->getRow("select goods_id,is_on_sale,order_goods_id,goods_sn,zuanshidaxiao,qiegong,jingdu,yanse,zhengshuleibie,zhengshuhao,caizhi,'jinse',jinzhong,shoucun,'style_goods' as goods_type,(select c.cat_type_id from front.app_cat_type c where c.cat_type_name=g.cat_type1) as cat_type1, (select p.product_type_id from front.app_product_type p where p.product_type_name=g.product_type1) as product_type,jietuoxiangkou as xiangkou ,tuo_type,zhushilishu as zhushi_num from warehouse_shipping.warehouse_goods g where goods_id='{$goods_id}' for update");
            if(empty($goods)){
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//             
				return "货号[{$goods_id}]不存在";
            }
            if($goods['is_on_sale']<>2){
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//             
				return "货号[{$goods_id}]不是库存状态";            	
            }            
            if(!empty($goods['order_goods_id'])){
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//             
				return "货号[{$goods_id}]已被他人绑定";            	
            } 

            unset($goods['goods_id']);
            unset($goods['is_on_sale']);
            unset($goods['order_goods_id']);

            if($goods['cat_type']=='17')
            	$goods['goods_type'] = 'lz';
            if($goods['tuo_type']=='1')
            	$goods['tuo_type'] = '成品';
            else
            	$goods['tuo_type'] = '空托';

            if(strstr($goods['caizhi'],'18K')!=false){
            	$goods['jinse'] =ltrim($goods['caizhi'],'18K');
            	$goods['caizhi'] = '18K';
            }elseif(strstr($goods['caizhi'],'PT950')!=false){
            	$goods['jinse'] =ltrim($goods['caizhi'],'PT950');
            	$goods['caizhi'] = 'PT950';
            }elseif(strstr($goods['caizhi'],'S925')!=false)
            	$goods['caizhi'] = 'S925';
            elseif(strstr($goods['caizhi'],'足金')!=false)
            	$goods['caizhi'] = '足金';   
            if(empty($goods['zhushi_num']))
            	$goods['zhushi_num'] =0;
     			         
            $sql = "update app_order_details set goods_id='{$goods_id}',goods_sn=?,cart=?,cut=?,clarity=?,color=?,cert=?,zhengshuhao=?,caizhi=?,jinse=?,jinzhong=?,zhiquan=?,goods_type=?,cat_type=?,product_type=?,xiangkou=?,tuo_type=?,zhushi_num=? where id='{$detail_order_id}'";
            $stmt = $pdo->prepare($sql);
            $res=$stmt->execute(array_values($goods));             
            //$pdo->exec("update app_order_details set goods_id='{$goods_id}', where id='{$detail_order_id}'");
            $pdo->exec("update warehouse_shipping.warehouse_goods set order_goods_id='{$detail_order_id}' where goods_id='{$goods_id}'");

			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return 1;
		}catch(Exception $e){//捕获异常
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return "事物操作不成功". $e->getMessage();
		}



	}




}

?>