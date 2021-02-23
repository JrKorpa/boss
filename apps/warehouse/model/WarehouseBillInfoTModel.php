<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoTModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-20 15:29:32
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoTModel extends Model{
	function __construct($id = NULL, $strConn = ""){
		$this->_objName = '';
		$this->_dataObject = array ();
		parent::__construct($id, $strConn);
	}



	/**
	 * 添加单据信息，含货品明细，单据状态
	 */
	public function saveBillAllInfo($data,$info){
		$time = date('Y-m-d H:i:s');
		$billModel  = new WarehouseBillModel(21);
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//1、添加单据主信息
			$bill['bill_no'] = $billModel->create_bill_no('T');
			$bill['bill_type'] = 'T';
			$bill['bill_status'] = '1';
			$bill['goods_num'] = $info['goods_num'];
			$bill['to_warehouse_id'] = $info['to_warehouse_id'];
			$bill['to_warehouse_name'] = $info['to_warehouse_name'];
			$bill['to_company_id'] = $info['to_company_id'];
			$bill['to_company_name'] = $info['to_company_name'];
			$bill['yuanshichengben'] = $info['yuanshichengben'];
			$bill['goods_total'] = $info['goods_total'];
			$bill['shijia'] = $info['shijia'];
			$bill['create_user'] = $info['create_user'];
			$bill['create_time'] = $info['create_time'];
			$bill['order_sn'] = $info['order_sn'];
			$bill['bill_note'] = $info['bill_note'];
			$bill['tuihuoyuanyin'] = $info['tuihuoyuanyin'];
			$bill['put_in_type'] = $info['put_in_type'];
			$bill['send_goods_sn'] = $info['send_goods_sn'];
			$bill['pro_id'] = $info['pro_id'];
			$bill['pro_name'] = $info['pro_name'];
			$bill['jiejia'] = $info['jiejia'];
			$sql = $this->insertSql($bill,'warehouse_bill');
			$pdo->query($sql);
			$id = $pdo->lastInsertId();

			$bill_no = $billModel->create_bill_no('T',$id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no` = '{$bill_no}' WHERE `id` = {$id}";
			$pdo->query($sql);

			//3、添加状态表
			$sql = "INSERT INTO `warehouse_bill_status` (`id`, `bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES (NULL, '{$id}', '{$bill_no}', '1', '{$info['create_time']}', '{$info['create_user']}', '{$info['create_ip']}');";
			$pdo->query($sql);

			//4、明细添加
			$model = new WarehouseGoodsModel(21);
			$goods_id = $model->_getGoodsId();
			foreach (array_reverse($data) as $value)
			{
				//插入货品表
				floatval($goods_id);
				$goods_id++;
				$goods_id=number_format($goods_id,0,"","");
				$value['goods_id'] = $goods_id;
				$value['put_in_type'] = $info['put_in_type'];
				$value['prc_id'] = $info['pro_id'];
				$value['prc_name'] = $info['pro_name'];
				$value['company'] = $info['to_company_name'];
				$value['warehouse'] = $info['to_warehouse_name'];
				$value['company_id'] = $info['to_company_id'];
				$value['warehouse_id'] = $info['to_warehouse_id'];
				$value['account'] = $info['jiejia'];
				$value['jiejia'] = $info['jiejia'];
				$value['addtime'] ='0000-00-00 00:00:00';//收货单保存记录时间，审核通过后写入收货单审核时间

				$sql = $this->insertSql($value,'warehouse_goods');
				$pdo->query($sql);
				//插入单据明细表

				$sql = "INSERT INTO `warehouse_bill_goods` (`id`, `bill_id`, `bill_no`, `bill_type`, `goods_id`, `goods_sn`, `goods_name`, `num`, `caizhi`, `jinzhong`, `yanse`, `zuanshidaxiao`, `yuanshichengben`, `sale_price`, `shijia`, `in_warehouse_type`, `account`, `addtime`,`warehouse_id`) VALUES (NULL, '{$id}', '{$bill_no}', 'T', '{$goods_id}', '{$value['goods_sn']}', '{$value['goods_name']}', '1', '{$value['caizhi']}', '{$value['jinzhong']}', '{$value['yanse']}', '{$value['zuanshidaxiao']}', '{$value['chengbenjia']}', '{$value['chengbenjia']}', '0', '{$info['put_in_type']}', '{$info['jiejia']}', '{$time}','{$info['to_warehouse_id']}')";

				$pdo->query($sql);
			}
			
			//保存结算商
			//$billPayArr=$_SESSION['bill_pay'];
			$olddo_str=$_COOKIE['bill_pay'];
			$billPayArr=unserialize($olddo_str);
			foreach ($billPayArr as $row){
				$sql = "INSERT INTO `warehouse_bill_pay` (`id`, `bill_id`, `pro_id`, `pro_name`, `pay_content`, `pay_method`, `tax`, `amount`) VALUES (NULL, '{$id}', '{$row['pro_id']}', '{$row['pro_name']}','{$row['pay_content']}','{$row['pay_method']}','{$row['pay_tax']}','{$row['amount']}' )";
				$pdo->query($sql);
			}
			
		}
		catch(Exception $e){//捕获异常
			//echo $sql;die;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		//清除结算商session数组
		//unset($_SESSION['bill_pay']);
		setcookie('bill_pay','',time()-10);
		return array('id'=>$id, 'bill_no'=>$bill_no);
	}

	//整理收货单数据
	public function arrangementData($grid, $pro_id=0)
	{
		$dataArr = array();
		$chengbenzongjia = 0;
		foreach($grid as $key=>$val){
            $tuo_type = $val['tuo_type'];
			$dataArr[$key]['goods_id'] = $val['goods_id']===''?'0':$val['goods_id'];
			$dataArr[$key]['goods_sn'] = $val['goods_sn'];
			$dataArr[$key]['mo_sn'] = $val['mo_sn'];
			$dataArr[$key]['product_type'] = mb_substr($val['product_type'], 0, 20);
			$dataArr[$key]['product_type1'] = mb_substr($val['product_type'], 0, 20);
			$dataArr[$key]['cat_type'] = $val['cat_type'];
			$dataArr[$key]['cat_type1'] = $val['cat_type'];
			$dataArr[$key]['is_on_sale'] = 1;
			$dataArr[$key]['att2'] = mb_substr($val['product_type'], 0, 20);
			$dataArr[$key]['caizhi'] = $val['caizhi'];
			$dataArr[$key]['jinzhong'] = $val['jinzhong'];
			$dataArr[$key]['jinhao'] = $val['jinhao'];
			$dataArr[$key]['zhuchengsezhongjijia'] = empty( $val['zhuchengsezhongjijia'])?0.00:$val['zhuchengsezhongjijia'];
			$dataArr[$key]['zhuchengsemairudanjia'] = $val['zhuchengsemairudanjia'];
			$dataArr[$key]['zhuchengsemairuchengben'] = $val['zhuchengsemairuchengben'];
			$dataArr[$key]['zhuchengsejijiadanjia'] = $val['zhuchengsejijiadanjia'];
			$dataArr[$key]['zhushi'] = $val['zhushi'];
			$dataArr[$key]['zhushilishu'] = $val['zhushilishu'];
			$dataArr[$key]['zuanshidaxiao'] = $val['zuanshidaxiao'];
			$dataArr[$key]['zhushizhongjijia'] = $val['zhushizhongjijia'];
			$dataArr[$key]['zhushiyanse'] = $val['zhushiyanse'];
			$dataArr[$key]['zhushijingdu'] = $val['zhushijingdu'];
			$dataArr[$key]['zhushimairudanjia'] = $val['zhushimairudanjia'];
			$dataArr[$key]['zhushimairuchengben'] = $val['zhushimairuchengben'];
			$dataArr[$key]['zhushijijiadanjia'] = $val['zhushijijiadanjia'];
			$dataArr[$key]['zhushiqiegong'] = $val['zhushiqiegong'];
			$dataArr[$key]['zhushixingzhuang'] = $val['zhushixingzhuang'];
			$dataArr[$key]['zhushibaohao'] = $val['zhushibaohao'];
			$dataArr[$key]['zhushiguige'] = $val['zhushiguige'];
			$dataArr[$key]['fushi'] = $val['fushi'];
			$dataArr[$key]['fushilishu'] = $val['fushilishu'];
			$dataArr[$key]['fushizhong'] = $val['fushizhong'];
			$dataArr[$key]['fushizhongjijia'] = $val['fushizhongjijia'];
			$dataArr[$key]['fushiyanse'] = $val['fushiyanse'];
			$dataArr[$key]['fushijingdu'] = $val['fushijingdu'];
			$dataArr[$key]['fushimairuchengben'] = $val['fushimairuchengben'];
			$dataArr[$key]['fushimairudanjia'] = $val['fushimairudanjia'];
			$dataArr[$key]['fushijijiadanjia'] = $val['fushijijiadanjia'];
			$dataArr[$key]['fushixingzhuang'] = $val['fushixingzhuang'];
			$dataArr[$key]['fushibaohao'] = $val['fushibaohao'];
			$dataArr[$key]['fushiguige'] = $val['fushiguige'];
			$dataArr[$key]['zongzhong'] = $val['zongzhong'];
			$dataArr[$key]['mairugongfeidanjia'] = $val['mairugongfeidanjia'];
			$dataArr[$key]['mairugongfei'] = $val['mairugongfei'];
			$dataArr[$key]['jijiagongfei'] = $val['jijiagongfei'];
			$dataArr[$key]['shoucun'] = $val['shoucun'];
			$dataArr[$key]['ziyin'] = $val['ziyin'];
			$dataArr[$key]['danjianchengben'] = $val['danjianchengben'];
			$dataArr[$key]['peijianchengben'] = $val['peijianchengben'];
			$dataArr[$key]['qitachengben'] = $val['qitachengben'];
			$dataArr[$key]['chengbenjia'] = $val['chengbenjia'];
			$dataArr[$key]['mingyichengben'] = (floatval($val['xiaoshouchengben']) >0)?$val['xiaoshouchengben']:$val['chengbenjia'];
			$dataArr[$key]['gongchangchengben'] = $val['gongchangchengben'];
			$dataArr[$key]['yuanshichengbenjia'] = $val['chengbenjia'];
			$dataArr[$key]['jijiachengben'] = $val['jijiachengben'];
			$dataArr[$key]['jiajialv'] = $val['jiajialv'];
			$dataArr[$key]['zuixinlingshoujia'] = $val['zuixinlingshoujia'];
			$dataArr[$key]['pinpai'] = $val['pinpai'];
			$dataArr[$key]['changdu'] = $val['changdu'];
			$dataArr[$key]['zhengshuhao'] = trim($val['zhengshuhao']);
			$dataArr[$key]['yanse'] = $dataArr[$key]['zhushiyanse'];
			$dataArr[$key]['jingdu'] = $dataArr[$key]['zhushijingdu'];
			$dia_sn_one = 88;
			$dia_sn_two = 88;
			$dataArr[$key]['dia_sn'] = $dia_sn_one.$dia_sn_two;
			$dataArr[$key]['peijianshuliang'] = $val['peijianshuliang'];
			$dataArr[$key]['guojizhengshu'] = $val['guojizhengshu'];
			$dataArr[$key]['zhengshuleibie'] = $val['zhengshuleibie'];
			$dataArr[$key]['goods_name'] = $val['goods_name'];
			$dataArr[$key]['kela_order_sn'] = $val['kela_order_sn'];
			$dataArr[$key]['shi2'] = $val['shi2'];
			$dataArr[$key]['shi2lishu'] = $val['shi2lishu'];
			$dataArr[$key]['shi2zhong'] = $val['shi2zhong'];
			$dataArr[$key]['shi2zhongjijia'] = $val['shi2zhongjijia'];
			$dataArr[$key]['shi2mairudanjia'] = $val['shi2mairudanjia'];
			$dataArr[$key]['shi2mairuchengben'] = $val['shi2mairuchengben'];
			$dataArr[$key]['shi2jijiadanjia'] = $val['shi2jijiadanjia'];
			$dataArr[$key]['qiegong'] = $val['qiegong'];
			$dataArr[$key]['paoguang'] = $val['paoguang'];
			$dataArr[$key]['duichen'] = $val['duichen'];
			$dataArr[$key]['yingguang'] = $val['yingguang'];
			$dataArr[$key]['buchan_sn'] = $val['buchanhao']?$val['buchanhao']:'';//默认没有绑定订单
			$dataArr[$key]['order_goods_id'] = 0; //默认没有绑定订单
			$dataArr[$key]['zuanshizhekou'] = $val['zuanshizhekou'];
			$dataArr[$key]['zhengshuhao2']  = trim($val['zhengshuhao2']);
			$dataArr[$key]['guojibaojia']   = $val['guojibaojia'];
			$dataArr[$key]['tuo_type'] = $tuo_type;
			$dataArr[$key]['gemx_zhengshu'] = $val['gemx_zhengshu'];
			$dataArr[$key]['jietuoxiangkou'] = empty($val['jietuoxiangkou'])?0.000:$val['jietuoxiangkou'];
			$dataArr[$key]['zhushitiaoma'] = $val['zhushitiaoma'];
			$dataArr[$key]['color_grade'] = $val['color_grade'];			
			$dataArr[$key]['supplier_code'] = $val['supplier_code'];
			$dataArr[$key]['luozuanzhengshu'] = $val['luozuanzhengshu'];
			$dataArr[$key]['peijianjinchong'] = $val['peijianjinchong'];
            $dataArr[$key]['operations_fee'] = !empty($val['operations_fee'])?$val['operations_fee']:0;
			//计算总成本价
			$chengbenzongjia = $chengbenzongjia + $val['chengbenjia'];

            $zhengshu_fee   = $val['certificate_fee'];
            $product_type = mb_substr($val['product_type'], 0, 20);
            if($pro_id == 581){
                $zhengshu_fee = 0;
            }else{
                if($zhengshu_fee === ''){
                    if(in_array($product_type,array('钻石','珍珠','翡翠','宝石','彩钻')) && $tuo_type == 1){
                        $zhengshu_fee = 20;
                    }elseif(in_array($product_type,array('K金','PT','银饰'))){
                        $zhengshu_fee = 5;
                    }else{
                        $zhengshu_fee = 0;
                    }
                }
            }
            $mingyichengben = $val['xiaoshouchengben'];
            if($mingyichengben === '') $mingyichengben = bcadd($dataArr[$key]['chengbenjia'], $zhengshu_fee, 3);

            $dataArr[$key]['certificate_fee'] = $zhengshu_fee;			
            if($dataArr[$key]['cat_type1']<>'裸石' && $dataArr[$key]['cat_type1']<>'彩钻'){
                if(bccomp($mingyichengben,bcadd($dataArr[$key]['chengbenjia'], $zhengshu_fee,3) ,3)==-1)
                	$mingyichengben=bcadd($dataArr[$key]['chengbenjia'], $zhengshu_fee,3);
            }
            $dataArr[$key]['mingyichengben'] = $mingyichengben;

		}
		return array('dataArr' => $dataArr,'chengbenzongjia' => $chengbenzongjia);
	}

	public function  up_info($data,$info){
		$pdo = $this->db()->db();//pdo对象
		$time = date('Y-m-d H:i:s');
		//这里查询入库单据的商品goods_id

		$extgoodss = "SELECT `bg`.`goods_id`,`bg`.`goods_sn` FROM `warehouse_bill_goods` AS `bg` LEFT JOIN `warehouse_goods` AS `g` ON `bg`.`goods_id` = `g`.`goods_id` WHERE `bg`.`bill_id` ='{$info['id']}' order by `bg`.`id` asc";

		$extgoods =$this->db()->getAll($extgoodss);
		$goods_eids = array();
		$goods_id_all='';
		foreach ($extgoods as $key => $valg) {
			$goods_eids[] = $valg['goods_id'];
			$goods_id_all .= $valg['goods_id'].",";
		}
		$goods_id_all = rtrim($goods_id_all,',');

		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			//1、更新Bill主表

			$sql ="UPDATE warehouse_bill SET `goods_num`='{$info['goods_num']}',`bill_note`='{$info['bill_note']}',`pro_id`='{$info['pro_id']}',`pro_name`='{$info['pro_name']}', `yuanshichengben`='{$info['yuanshichengben']}', `goods_total`='{$info['goods_total']}', `shijia`='{$info['shijia']}' , `order_sn` = '{$info['order_sn']}',   `jiejia`='{$info['jiejia']}',`to_warehouse_id`='{$info['to_warehouse_id']}',`to_warehouse_name`='{$info['to_warehouse_name']}',`to_company_id`='{$info['to_company_id']}',`to_company_name`='{$info['to_company_name']}',`send_goods_sn`='{$info['send_goods_sn']}',put_in_type='{$info['put_in_type']}',tuihuoyuanyin='{$info['tuihuoyuanyin']}'  WHERE `id`='{$info['id']}'";
			$pdo->query($sql);

			//更新其他表表
			$sql ="UPDATE `warehouse_bill_goods` as a,`warehouse_goods` as b SET a.`in_warehouse_type`='{$info['put_in_type']}', a.`account`='{$info['jiejia']}', b.`put_in_type`='{$info['put_in_type']}', b.`account`='{$info['jiejia']}',b.`jiejia` = '{$info['jiejia']}'    WHERE a.`bill_id`='{$info['id']}' and a.goods_id = b.goods_id";
			$pdo->query($sql);

			//更新货品入库仓
			$sql ="UPDATE warehouse_goods SET `warehouse_id`='{$info['to_warehouse_id']}',`warehouse`='{$info['to_warehouse_name']}',`company_id`='{$info['to_company_id']}',`company`='{$info['to_company_name']}'   WHERE `goods_id` in ({$goods_id_all})";
			$pdo->query($sql);
			//有明细修改================================start========================
			if(count($data))
			{
				$model = new WarehouseGoodsModel(21);
				$goods_ids =array();
				$goods_id = $model->_getGoodsId();
				//如果goods_id是0的话走新增
				foreach (array_reverse($data) as $key => $val) {
					if($val['goods_id']=='0'){
						//这里开始新增明细和仓库列表

						floatval($goods_id);
						$goods_id++;
						$goods_id=number_format($goods_id,0,"","");
						$val['goods_id'] = $goods_id;
						$val['put_in_type'] = $info['put_in_type'];
						$val['prc_id'] = $info['pro_id'];
						$val['prc_name'] = $info['pro_name'];
						$val['company'] = $info['to_company_name'];
						$val['warehouse'] = $info['to_warehouse_name'];
						$val['company_id'] = $info['to_company_id'];
						$val['warehouse_id'] = $info['to_warehouse_id'];
						$val['addtime'] = $time;

						$sql = $this->insertSql($val,'warehouse_goods');
						$pdo->query($sql);
						//file_put_contents('d://rr.txt',$sql."<br>",FILE_APPEND);

						//插入单据明细表
						$sql = "INSERT INTO `warehouse_bill_goods` (`id`, `bill_id`, `bill_no`, `bill_type`, `goods_id`, `goods_sn`, `goods_name`, `num`, `caizhi`, `jinzhong`, `yanse`, `zuanshidaxiao`, `yuanshichengben`, `sale_price`, `shijia`, `in_warehouse_type`, `account`, `addtime`) VALUES (NULL, '{$info['id']}', '{$info['bill_no']}', 'T', '{$goods_id}', '{$val['goods_sn']}', '{$val['goods_name']}', '1', '{$val['caizhi']}', '{$val['jinzhong']}', '{$val['yanse']}', '{$val['zuanshidaxiao']}', '{$val['chengbenjia']}', '{$val['chengbenjia']}', 0, '{$info['put_in_type']}', '{$info['jiejia']}', '{$time}')";
						//file_put_contents('d://rr.txt',$sql."<br>",FILE_APPEND);
						$pdo->query($sql);
						unset($data[$key]);
					}else{
						$goods_ids[$key]=$val['goods_id'];
					}
				}

				//比较旧的值 和  新传过开的值少了那些货号  并且关联删除
				$resarr = array_diff($goods_eids,$goods_ids);

				//如果$resarr是空值 则 不需要走删除程序
				if(count($resarr)){
					foreach ($resarr as $key=>$val) {
						$sql = "DELETE FROM `warehouse_bill_goods` WHERE goods_id = '".$val."'";
						$pdo->query($sql);
						$sql = "DELETE FROM `warehouse_goods` WHERE goods_id = '".$val."'";
						$pdo->query($sql);
					}

				}

				//其他的全部走修改
				foreach (array_reverse($data) as $value)
				{
					if($value['goods_id']!=0){
						$sql = $this->update($value,array('goods_id'=>$value['goods_id']),'warehouse_goods');
						//修改货品表
						$pdo->query($sql);

						//修改单据明细表
						$sql = "UPDATE `warehouse_bill_goods` SET `goods_name`='{$value['goods_name']}', `num`='1', `caizhi`= '{$value['caizhi']}', `jinzhong`='{$value['jinzhong']}', `yanse`='{$value['yanse']}', `zuanshidaxiao`= '{$value['zuanshidaxiao']}', `yuanshichengben`='{$value['chengbenjia']}', `sale_price`='{$value['chengbenjia']}',`in_warehouse_type`='{$info['put_in_type']}', `account`='{$info['jiejia']}' WHERE `goods_id`='".$value['goods_id']."' and bill_id = {$info['id']}";
						$pdo->query($sql);
					}
				}
			}
			//有明细修改================================end========================
		}
		catch(Exception $e){
			print_r($e);
			$pdo->rollback();
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
		return true;
	}
	/*
	 * 审核单据
	 * 单据ID WAREHOUSE_BILL 的主键
	 * 1/改变单据状态 warehouse_bill
	 * 2/改变货品状态 warehouse_goods
	 * 3/记录单据记录 warehouse_bill_status
	 */
	public function checkBillT($bill_id,$pdo){
	    $time = date('Y-m-d H:i:s');
	    $ip = Util::getClicentIp();
	    try{
	        // 改变单据状态 warehouse_bill
	        $sql = "UPDATE `warehouse_bill` SET `bill_status`=2,`check_time`='{$time}',`check_user`='{$_SESSION['userName']}' WHERE `id`={$bill_id} and `bill_status`!=2";
	        $num = $pdo->exec($sql);
	        if($num !=1){
	            $pdo->rollback();//事务回滚
	            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	            //return array('success'=> 0 , 'error'=>'货品状态不是收货中'.$goods_id_str);
	            return false;
	        }
	        /* $changed=$pdo->exec($sql);
	         if($changed<>count(explode(",",$goods_id_str))) {
	         $pdo->rollback();//事务回滚
	         $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	         return array('success'=> 0 , 'error'=>'货品状态不是库存中'.$goods_id_str);
	         } */
	
	        // 根据bill_id 获取信息(表:warehouse_bill)
	        $sql = "SELECT `bill_no`,`to_warehouse_id`, `to_warehouse_name` , `to_company_name` , `to_company_id` , `put_in_type`,`create_time`,`check_time`,`send_goods_sn`,`pro_id`,`pro_name`,`goods_total` FROM `warehouse_bill` AS b  WHERE `b`.`id`={$bill_id}";
	        $bill_info = $this->db()->getRow($sql);
	        $bill_no = $bill_info['bill_no'];
	        $to_warehouse_id = $bill_info['to_warehouse_id'];
	        //获取入库仓的默认柜位ID
	        $boxModel = new WarehouseBoxModel(21);
	        $default_box = $boxModel->select2(' `id` ' , $where = " warehouse_id = {$to_warehouse_id} AND `box_sn` = '0-00-0-0' AND `is_deleted` = 1 " , $is_all = 3);
	        $default_box_id  = $default_box ? $default_box : 0 ;	//入库仓默认柜位
	
	        /**
	         * 获取单据下的商品明细 *
	         */
	        $goods_id_str = '';
	        $sql = "SELECT bg.goods_name,bg.`goods_id`,g.product_type,g.cat_type,bg.sale_price,bg.goods_sn,g.zhengshuhao, g.jietuoxiangkou, g.shoucun , g.caizhi , g.yanse FROM `warehouse_bill_goods` AS `bg` LEFT JOIN `warehouse_goods` AS `g` ON `bg`.`goods_id` = `g`.`goods_id` WHERE `bill_id`={$bill_id}";
	        $bill_goods = $this->db()->getAll($sql);
	
	        // 添加货品和仓储关系表 (goods_warehouse ) [获取当前入库仓的默认柜位，把每个货品放置在该默认柜位上]
	        foreach($bill_goods as $v){
	            $sql = "select count(*) from goods_warehouse where good_id='{$v['goods_id']}'";
	            $checkExist = $this->db()->getOne($sql);
	            if(!$checkExist){
	                $sql = "INSERT INTO `goods_warehouse`(`good_id`, `warehouse_id`, `box_id` , `add_time`,`create_user`) VALUES ( '{$v['goods_id']}' , {$to_warehouse_id} ,  {$default_box_id} , '{$time}','SYSTEM')";
	                $pdo->query($sql);
	            }
				else//已经存在就更新入库时间为当前仓库最新的审核时间 boss717
				{
					$sql = "update `goods_warehouse` set `add_time` = {$time} where `good_id` = '{$v['goods_id']}'";
    	            $pdo->query($sql);
				}
				
	            $goods_id_str .= ',\'' . $v ['goods_id'] . '\'';
	        }
	        $goods_id_str = ltrim($goods_id_str, ',');
	        $tax_rate = 0;
	        if(date("Y-m-d")>='2018-05-01'){
	            $tax_rate = 13;
	        }
	        // 改变货品状态 warehouse_goods，审核入库添加时间、最后一次转仓时间
	        $sql = "UPDATE `warehouse_goods` SET `is_on_sale`=2,`addtime`='{$time}',`change_time`='{$time}',tax_rate={$tax_rate} WHERE `goods_id` IN ({$goods_id_str}) and `is_on_sale`=1";
	        //$pdo->query($sql);
	        $changed=$pdo->exec($sql);
	        if($changed<>count(explode(",",$goods_id_str))) {
	            $pdo->rollback();//事务回滚
	            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	            //return array('success'=> 0 , 'error'=>'货品状态不是收货中'.$goods_id_str);
	            return false;
	        }
	
	        // 记录单据记录 warehouse_bill_status
	        $sql = "INSERT INTO `warehouse_bill_status` (`bill_id`,`bill_no`,`status`,`update_time`,`update_user`,`update_ip`) VALUES ({$bill_id},'{$bill_no}',2,'{$time}','{$_SESSION['userName']}','{$ip}')";
	        $pdo->query($sql);
	
	        $sql = "SELECT pro_id,pro_name,pay_content,amount FROM `warehouse_bill_pay` WHERE bill_id = ".$bill_id;
	        $pay_arr = $this->db()->getAll($sql);
	        //推送财务结算数据
	
	        //代销接货的推送货品明细，购买和委托加工推送结算明细
	        if($bill_info['put_in_type'] == 1 || $bill_info['put_in_type'] == 2) //购买、加工  推送结算明细
	        {
	
	            $data = array();
	            foreach($pay_arr as $k => $v)
	            {
	                if($v['pay_content'] == 4 || $v['pay_content'] == 6 || $v['pay_content'] == 8){ //支付内容为证书费或差的，不需要推送到财务模块的成品采购明细中
	                    continue;
	                }
	                if($v['pro_id'] == $bill_info['pro_id']){
	                    $arr = array(
	                        'item_id'	=> $bill_no,
	                        'order_id'	=> 0,
	                        'zhengshuhao' => '',
	                        'goods_status' => 2,
	                        'item_type'	=> '1',
	                        'company'=> 58,
	                        'prc_id'	=> $v['pro_id'],
	                        'prc_name'	=> $v['pro_name'],
	                        'prc_num'	=> $bill_info['send_goods_sn'],
	                        'type'=> 2,
	                        'pay_content' => $v['pay_content'],
	                        'storage_mode'=> $bill_info['put_in_type'],
	                        'make_time'	=> $bill_info['create_time'],
	                        'check_time'=> $time,
	                        'total'		=> $v['amount']
	                    );
	                    $data[] = $arr;
	                }else{
	                    continue;
	                }
	            }
	        }elseif($bill_info['put_in_type'] == 3 || $bill_info['put_in_type'] == 4)//代销、借入  推送货品明细
	        {
	            $pro_ids =array();
	            foreach($pay_arr as $k=>$v){
	                $pro_ids[]= $v['pro_id'];
	            }
	            foreach($bill_goods as $v){
	                if(in_array($bill_info['pro_id'],$pro_ids)){
	                    $arr = array(
	                        'item_id'	=> $v['goods_id'],
	                        'order_id'	=> 0,
	                        'zhengshuhao' => $v['zhengshuhao'],
	                        'goods_status' => 2,
	                        'item_type'	=> $v['cat_type']?$v['cat_type']:'',
	                        'company'=> 58,
	                        'prc_id'	=> $bill_info['pro_id'],
	                        'prc_name'	=> $bill_info['pro_name'],
	                        'prc_num'	=> $bill_info['send_goods_sn'],
	                        'type'=> 1,
	                        'pay_content' => '',
	                        'storage_mode'=> $bill_info['put_in_type'],
	                        'make_time'	=> $bill_info['create_time'],
	                        'check_time'=> $time,
	                        'total'		=> $v['sale_price']
	                    );
	                    $data[] = $arr;
	                }else{
	                    continue;
	                }
	            }
	        }
	
	
	        $no_policy = array();
	        //收货时，如果收回来的布产单与订单有绑定关系，生成的新货号需要被绑定
	        /* if ($up_data)
	         {
	         foreach ($up_data as $val)
	         {
	         $sql = "UPDATE `warehouse_goods` SET `order_goods_id`='{$val['order_goods_id']}' WHERE `id`={$val['id']}";
	         //echo $sql;exit;
	         $pdo->query($sql);
	
	         //这些货号被绑定，不需要推送到销售政策。
	         $no_policy[] = $val['goods_id'];
	         }
	        } */
	
	    } catch(Exception $e){ // 捕获异常
	        $pdo->rollback();//事务回滚
	        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交
	        if($_SESSION['userName'] == 'admin')
	        {
	            echo $sql;
	            echo $e->getMessage();
	            exit();
	        }
	        return false;
	    }
	    //$pdo->commit(); // 如果没有异常，就提交事务
	    //$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交
	
	
	    //这里对销售政策推数据的整理
	    $putdatasale=array();
	    $apimodel = new ApiFinanceModel();
	    $apimodelStyle = new ApiStyleModel();
	    $warehouseRel = new WarehouseRelModel(21);
	
	    foreach ($bill_goods as $key=>$val) {
	        //no_policy是不需要推送过去的，如果不在这个数组内的要推送过去
	        if(!in_array($val['goods_id'],$no_policy))
	        {
	            //把产品线和款式分类转换成ID传入（仓储存的是varchar的）
	            if(!$val['product_type']){$val['product_type'] = '其他';}
	            if(!$val['cat_type']){$val['cat_type'] = '其他';}
	            $cat_type = $apimodelStyle->getCatTypeInfo(array('cat_type_name'),array($val['cat_type']));
	            $product_type = $apimodelStyle->getProductTypeInfo(array('product_type_name'),array($val['product_type']));
	            $cat_type_id	 = count($cat_type)?$cat_type[0]['id']:0;
	            $product_type_id = count($product_type)?$product_type[0]['id']:0;
	
	            //准备销售政策传输数据
	            $putdatasale[$key]['goods_name'] = $val['goods_name'];
	            $putdatasale[$key]['goods_id'] = $val['goods_id'];
	            $putdatasale[$key]['chengbenjia'] = $val['sale_price'];
	            $putdatasale[$key]['goods_sn'] = $val['goods_sn'];
	            $putdatasale[$key]['category']= $cat_type_id;
	            $putdatasale[$key]['product_type']= $product_type_id;
	
	            $putdatasale[$key]['warehouse_id']= $bill_info['to_warehouse_id'];
	            $putdatasale[$key]['company_id']= $bill_info['to_company_id'];
	            $putdatasale[$key]['warehouse']= $bill_info['to_warehouse_name'];
	            $putdatasale[$key]['company']= $bill_info['to_company_name'];
	
	            //获取款式库的属性信息
	            /*$caizhi_arr = array();
	             $style_info_caizhi = $apimodelStyle->getZhuchengseList();
	             foreach($style_info_caizhi as $vals){
	             $caizhi_arr[$vals['material_id']] = $vals['material_name'];
	             }
	             $putdatasale[$key]['caizhi']= array_search($val['caizhi'] , $caizhi_arr);
	
	             $putdatasale[$key]['yanse']= $val['yanse'];
	            */
	
	            $putdatasale[$key]['stone']= $val['jietuoxiangkou'];
	            $putdatasale[$key]['finger']= $val['shoucun'];
	
	        }
	    }
	    //如果失败，多次推送数据到销售政策
	    $salepolicyModel = new ApiSalepolicyModel();
	    $time=date('H:i:s');
	    for($j=1;$j<4;$j++){
	        $putres = $salepolicyModel->AddAppPayDetail($putdatasale);
	        if($putres['error']==0){
	            break;
	        }
	        $filename = date('Y_m_d').'_error_log.txt';
	        Util::rmkdir(APP_ROOT.'warehouse/logs/api_logs/');
	        file_put_contents(APP_ROOT.'warehouse/logs/api_logs/'.$filename,'单号为'.$bill_no.'失败接口为salepolicy_api时间为'.$time.PHP_EOL,FILE_APPEND );
	    }
	
	    //这里对fin推数据
	    for($i=1;$i<4;$i++){
	        $putres=$apimodel->AddAppPayDetail($data);
	        if($putres['error']==0){
	            break;
	        }
	        $filename = date('Y_m_d').'_error_log.txt';
	        Util::rmkdir(APP_ROOT.'warehouse/logs/api_logs/');
	        file_put_contents(APP_ROOT.'warehouse/logs/api_logs/'.$filename,'单号为'.$bill_no.'失败接口为fin_api时间为'.$time=date('H:i:s').PHP_EOL,FILE_APPEND);
	    }
	
	    return true;
	}
	/*
	 * 审核单据
	 * 单据ID WAREHOUSE_BILL 的主键
	 * 1/改变单据状态 warehouse_bill
	 * 2/改变货品状态 warehouse_goods
	 * 3/记录单据记录 warehouse_bill_status
	 */
	public function checkBillT_BAK($bill_id,$pdo){
		$time = date('Y-m-d H:i:s');
		$ip = Util::getClicentIp();
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); // 关闭sql语句自动提交
			$pdo->beginTransaction(); // 开启事务

			// 改变单据状态 warehouse_bill
			$sql = "UPDATE `warehouse_bill` SET `bill_status`=2,`check_time`='{$time}',`check_user`='{$_SESSION['userName']}' WHERE `id`={$bill_id}";
			$pdo->query($sql);

			// 根据bill_id 获取信息(表:warehouse_bill)
			$sql = "SELECT `bill_no`,`to_warehouse_id`,`put_in_type`,`create_time`,`check_time`,`send_goods_sn`,`pro_id`,`pro_name`,`goods_total`,`to_company_id` , `to_warehouse_name` , `to_company_name` FROM `warehouse_bill` AS `b`  WHERE `b`.`id`={$bill_id}";
			$bill_info = $this->db()->getRow($sql);

			$bill_no = $bill_info['bill_no'];
			$to_warehouse_id = $bill_info['to_warehouse_id'];

			//获取入库仓的默认柜位ID
			$boxModel = new WarehouseBoxModel(21);
			$default_box = $boxModel->select2(' `id` ' , $where = " warehouse_id = {$to_warehouse_id} AND `box_sn` = '0-00-0-0' AND `is_deleted` = 1 " , $is_all = 3);
			$default_box_id  = $default_box ? $default_box : 0 ;	//入库仓默认柜位

			/**
			 * 获取单据下的商品明细 *
			 */
			$goods_id_str = '';
			$sql = "SELECT `bg`.`goods_name` , `bg`.`goods_id` , `g`.`id` , `g`.`product_type` , `g`.`cat_type` , `bg`.`sale_price` , `bg`.`goods_sn` ,  `g`.`jietuoxiangkou` , `g`.`shoucun` FROM `warehouse_bill_goods` AS `bg` LEFT JOIN `warehouse_goods` AS `g` ON `bg`.`goods_id` = `g`.`goods_id` WHERE `bill_id`={$bill_id}";
			$bill_goods = $this->db()->getAll($sql);

			// 添加货品和仓储关系表 (goods_warehouse ) [获取当前入库仓的默认柜位，把每个货品放置在该默认柜位上]
			foreach($bill_goods as $v){
				$sql = "INSERT INTO `goods_warehouse`(`good_id`, `warehouse_id`, `box_id` , `add_time`,`create_user`) VALUES ( '{$v['goods_id']}' , {$to_warehouse_id} ,  {$default_box_id} , '{$time}','SYSTEM')";
				$pdo->query($sql);

                //将货号写入库龄表，以便记录库龄；
                $sql = "INSERT INTO `warehouse_goods_age` (`warehouse_id`, `goods_id`, `endtime`, `self_age`, `total_age`) VALUES ( {$v['id']} , '{$v['goods_id']}' , '0000-00-00 00:00:00' , '1' , '1')";
                $pdo->query($sql);

				$goods_id_str .= ',' . $v ['goods_id'];
			}
			$goods_id_str = ltrim($goods_id_str, ',');

			// 改变货品状态 warehouse_goods
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale`=2,`change_time`='{$time}' WHERE `goods_id` IN ({$goods_id_str}) and `is_on_sale`=1";
			//$pdo->query($sql);
			$changed=$pdo->exec($sql);  
			if($changed<>count(explode(",",$goods_id_str))) {  
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				//return array('success'=> 0 , 'error'=>'货品状态不是收货中');
				return false;
			} 


			// 记录单据记录 warehouse_bill_status
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`,`bill_no`,`status`,`update_time`,`update_user`,`update_ip`) VALUES ({$bill_id},'{$bill_no}',2,'{$time}','{$_SESSION['userName']}','{$ip}')";
			$pdo->query($sql);

			//推送货品明细
			foreach($bill_goods as $v){
				$arr = array(
					'item_id'	=> $v['goods_id'],
					'order_id'	=> 0,
					'zhengshuhao' => '',//现在还没录入证书号
					'goods_status' => 2,
					'item_type'	=> $v['cat_type']?$v['cat_type']:'',
					'company'=> 58,
					'prc_id'	=> $bill_info['pro_id'],
					'prc_name'	=> $bill_info['pro_name'],
					'prc_num'	=> $bill_info['send_goods_sn'],
					'type'=> 1,
					'pay_content' => '',
					'storage_mode'=> $bill_info['put_in_type'],
					'make_time'	=> $bill_info['create_time'],
					'check_time'=> $time,
					'total'		=> $v['sale_price']
				);
				$data[] = $arr;
			}



			/* $no_policy = array();
			//收货时，如果收回来的布产单与订单有绑定关系，生成的新货号需要被绑定
			if ($up_data)
			{
				foreach ($up_data as $val)
				{
					$sql = "UPDATE `warehouse_goods` SET `order_goods_id`='{$val['order_goods_id']}' WHERE `id`={$val['id']}";
					//echo $sql;exit;
					$pdo->query($sql);

					//这些货号被绑定，不需要推送到销售政策。
					$no_policy[] = $val['goods_id'];
				}
			} */

		} catch(Exception $e){ // 捕获异常
			var_dump($sql);exit;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交
			return false;
		}
		$pdo->commit(); // 如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交


		//这里对销售政策推数据的整理
		$putdatasale=array();
		$apimodel = new ApiFinanceModel();
		$apimodelStyle = new ApiStyleModel();
		foreach ($bill_goods as $key=>$val) {

			//no_policy是不需要推送过去的，如果不在这个数组内的要推送过去
			if(!in_array($val['goods_id'],$no_policy))
			{
				//把产品线和款式分类转换成ID传入（仓储存的是varchar的）
				if(!$val['product_type']){$val['product_type'] = '其他';}
				if(!$val['cat_type']){$val['cat_type'] = '其他';}
				$cat_type = $apimodelStyle->getCatTypeInfo(array('cat_type_name'),array($val['cat_type']));
				$product_type = $apimodelStyle->getProductTypeInfo(array('product_type_name'),array($val['product_type']));
				$cat_type_id	 = count($cat_type)?$cat_type[0]['id']:0;
				$product_type_id = count($product_type)?$product_type[0]['id']:0;

				//准备销售政策传输数据
				$putdatasale[$key]['goods_name'] = $val['goods_name'];
				$putdatasale[$key]['goods_id'] = $val['goods_id'];
				$putdatasale[$key]['chengbenjia'] = $val['sale_price'];
				$putdatasale[$key]['goods_sn'] = $val['goods_sn'];
				$putdatasale[$key]['category']= $cat_type_id;
				$putdatasale[$key]['product_type']= $product_type_id;

				$putdatasale[$key]['warehouse_id']= $bill_info['to_warehouse_id'];
				$putdatasale[$key]['company_id']= $bill_info['to_company_id'];
				$putdatasale[$key]['warehouse']= $bill_info['to_warehouse_name'];
				$putdatasale[$key]['company']= $bill_info['to_company_name'];

				$putdatasale[$key]['stone']= $val['jietuoxiangkou'];
				$putdatasale[$key]['finger']= $val['shoucun'];
			}
		}
		//如果失败，多次推送数据到销售政策
		$salepolicyModel = new ApiSalepolicyModel();
		$time=date('H:i:s');
		for($j=1;$j<4;$j++){
			$putres = $salepolicyModel->AddAppPayDetail($putdatasale);
			if($putres['error']==0){
				break;
			}
			$filename = date('Y_m_d').'_error_log.txt';
			Util::rmkdir(APP_ROOT.'warehouse/logs/api_logs/');
			file_put_contents(APP_ROOT.'warehouse/logs/api_logs/'.$filename,'单号为'.$bill_no.'失败接口为salepolicy_api时间为'.$time.PHP_EOL,FILE_APPEND );
		}

		//这里对fin推数据
		for($i=1;$i<4;$i++){
			$putres=$apimodel->AddAppPayDetail($data);
			if($putres['error']==0){
				break;
			}
			$filename = date('Y_m_d').'_error_log.txt';
			Util::rmkdir(APP_ROOT.'warehouse/logs/api_logs/');
			file_put_contents(APP_ROOT.'warehouse/logs/api_logs/'.$filename,'单号为'.$bill_no.'失败接口为fin_api时间为'.$time=date('H:i:s').PHP_EOL,FILE_APPEND);
		}

		return true;
	}


	/** 取消单据 **/
	public function closeBillInfoT($bill_id,$bill_no){
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//将货品状态还原为库存
			$sql = "update warehouse_goods as g,warehouse_bill_goods as bg set g.is_on_sale = 12 where g.goods_id = bg.goods_id and bg.bill_id = {$bill_id}";
			$pdo->query($sql);
			//var_dump($sql);exit;
			#更改主表状态 warehouse_bill 的 bill_status 改为1
			$time = date('Y-m-d H:i:s');
			$user = $_SESSION['userName'];
			$sql = "UPDATE `warehouse_bill` SET `bill_status` =3, `check_user`='{$user}', `check_time`= '{$time}' WHERE id={$bill_id}";
			$pdo->query($sql);
			#写入warehouse_bill_status 表
			$ip = Util::getClicentIp();
			$sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_id}, '{$bill_no}', 3, '{$time}'
			, '{$user}', '{$ip}')";
			$pdo->query($sql);
		}
		catch(Exception $e){//捕获异常
			echo $sql;exit;
			print_r($e);exit;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}
	public function update($valueArr,$whereArr,$tableName = '')
	{
		$field = '';
		$where = ' 1';
		foreach($valueArr as $k => $v)
		{
			$field .= "$k = '$v',";
		}
		foreach($whereArr as $k => $v)
		{
			$where .= " AND $k = '$v'";
		}
		if(empty($tableName))
		{
			$tableName = $this->table();
		}
		$field = substr($field,0,-1);
		$sql = "UPDATE ".$tableName." SET ".$field;
        $sql .= " WHERE ".$where;
		return $sql;
	}


    public function updateStylePrice($lid)
    {
        $sql = "SELECT wg.id,wbg.goods_id,wg.goods_sn,wba.goods_id wba_goods_id,
                wg.caizhi,wg.tuo_type,wg.luozuanzhengshu,wg.zhushixingzhuang,wg.zuanshidaxiao,wg.zhushi,
                wg.zhushiyanse,wg.zhushijingdu
            FROM warehouse_shipping.warehouse_bill_goods wbg 
                inner join warehouse_shipping.warehouse_goods wg on wbg.goods_id = wg.goods_id
                left join warehouse_shipping.warehouse_goods_age wba on wbg.goods_id = wba.goods_id
            where wbg.bill_id = $lid ";
        $stylePriceGoods = $this->db()->getAll($sql);
        if($stylePriceGoods){
            foreach($stylePriceGoods as $key => $val){
                $wba_goods_id = $val['wba_goods_id'];
                $is_kuanprice = 0;
                $price = 0;
                $style_kuanprice_id=0;

                $id = $val['id'];
                $goods_id = $val['goods_id'];
                $goods_sn = $val['goods_sn'];
                $caizhi = $val['caizhi'];
                $tuo_type = $val['tuo_type'];
                $luozuanzhengshu = $val['luozuanzhengshu'];
                $zhushixingzhuang = $val['zhushixingzhuang'];
                $zuanshidaxiao = $val['zuanshidaxiao'];

                $zhushi = $val['zhushi'];
                $zhushiyanse = $val['zhushiyanse'];
                $zhushijingdu = $val['zhushijingdu'];

                $appPriceByStyleModel = new AppPriceByStyleModel(17);
                $zhushixingzhuang_num = $appPriceByStyleModel->getXingzhuang($zhushixingzhuang);
                $stone_cat = $zhushixingzhuang == '圆形'?2:3;
                $caizhi_enum = 0;
                if(strpos(strtoupper($caizhi),'18K') !== false ){
                    $caizhi_enum = 1;
                }elseif($caizhi == 'PT950'){
                    $caizhi_enum = 2;
                }
                // 1 18K  2 PT950

                $check = true;
                //var_dump($val);
                if($zhushi != '钻石'){
                    $check = false;
                }elseif($caizhi_enum == 0){
                    $check = false;
                }elseif($zhushixingzhuang_num == 0 ){
                    $check = false;
                }elseif($zhushiyanse == '' ){
                    $check = false;
                }elseif($zhushijingdu == '' ){
                    $check = false;
                }
            
                if($check)
                {
                    $sel_sql = "select id,zuan_yanse_min,zuan_yanse_max,zuan_jindu_min,zuan_jindu_max,price
                        from 
                            front.base_style_info bsi 
                            inner join front.app_price_by_style apbs on bsi.style_id = apbs.style_id
                        where
                            bsi.style_sn = '$goods_sn'
                            AND caizhi = $caizhi_enum
                            AND stone_position = 1
                            AND tuo_type = $tuo_type
                            AND zuan_min <= $zuanshidaxiao  
                            AND zuan_max >= $zuanshidaxiao 
                            AND cert = '$luozuanzhengshu'
                            AND zuan_shape = '$zhushixingzhuang'
                            AND is_delete = 0
                    ";
                    //echo $sel_sql;
                    $priceList = $this->db()->getAll($sel_sql);
                    foreach($priceList as $k => $v){
                        $yanseIn = $this->YanseIn($v['zuan_yanse_min'],$v['zuan_yanse_max'],$zhushiyanse);
                        $jingduIn = $this->JingduIn($v['zuan_jindu_min'],$v['zuan_jindu_max'],$zhushijingdu);
                        $certIn = $this->CertIn($v['cert'],$luozuanzhengshu);
                        if($yanseIn && $jingduIn && $certIn){
                            $is_kuanprice = 1;
                            $price = $v['price'];
                            $style_kuanprice_id = $v['id'];
                            break;
                        }
                    }
                }

                if(is_null($wba_goods_id)){
                    $sql = "INSERT INTO `warehouse_goods_age` (`warehouse_id`, `goods_id`, `endtime`, `self_age`, `total_age`,`is_kuanprice`,`kuanprice`,`style_kuanprice_id`) VALUES ( {$id} , '{$goods_id}' , '0000-00-00 00:00:00' , '1' , '1' ,$is_kuanprice,$price,$style_kuanprice_id)";
                    $this->db()->query($sql);
                }
            }
        }
    }
    public function CertIn($oldcert,$newcert)
    {
        if($oldcert == '全部'){
            return true;
        }
        if($oldcert == '空值'){
            $oldcert = '';
        }
        if($oldcert == $newcert){
            return true;
        }else{
            return false;
        }
    }

    public function YanseIn($oldS,$oldE,$new)
    {
        if($new == ''){
            $new = '空值';
        }
        $appPriceByStyleModel = new AppPriceByStyleModel(17);
        $zuan_yanse = $appPriceByStyleModel->getYanseAll();
        
        $y = array_flip($zuan_yanse);
        $oldS = $y[$oldS];
        $oldE = $y[$oldE];
        $new = $y[$new];

        if($oldS <= $new && $new <= $oldE){
            return true;
        }else{
            return false;
        }
    }

    public function JingduIn($oldS,$oldE,$new)
    {
        if($new == ''){
            $new = '空值';
        }

        $appPriceByStyleModel = new AppPriceByStyleModel(17);
        $zuan_jingdu = $appPriceByStyleModel->getJingduAll();

        $j = array_flip($zuan_jingdu);
        $oldS = $j[$oldS];
        $oldE = $j[$oldE];
        $new = $j[$new];
        
        if($oldS <= $new && $new <= $oldE){
            return true;
        }else{
            return false;
        }
    }
}
?>