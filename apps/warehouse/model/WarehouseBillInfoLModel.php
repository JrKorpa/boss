<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillInfoLModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-21 17:15:30
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillInfoLModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = '';
	    $this->_dataObject = array();
		parent::__construct($id,$strConn);
	}

	//添加
	public function add_info ($data,$info)
	{
		$time = date('Y-m-d H:i:s');
		$billModel  = new WarehouseBillModel(21);
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务

			$bill_no= $billModel->create_bill_no('L');
			$label_price_total = 0;
			if(SYS_SCOPE=="zhanting"){
			    $label_price_total = array_sum(array_column($data,"biaoqianjia"));
			}			
			//1、添加Bill主表
			$sql = "INSERT INTO `warehouse_bill` (
				`id`, `bill_no`, `bill_type`, `bill_status`,
				`goods_num`, `to_warehouse_id`, `to_warehouse_name`, `to_company_id`,
				`to_company_name`, `from_company_id`, `from_company_name`, `order_sn`,
				`bill_note`,`check_user`, `check_time`, `create_user`, `create_time`,`put_in_type`,
				`send_goods_sn`, `pro_id`, `pro_name`, `jiejia`,`goods_total`, `yuanshichengben`, `shijia`,`production_manager_name`,`label_price_total`
				) VALUES (
				NULL, '{$bill_no}', 'L', 1,
				{$info['goods_num']}, {$info['to_warehouse_id']}, '{$info['to_warehouse_name']}', {$info['to_company_id']},
				'{$info['to_company_name']}', 0, '', '{$info['order_sn']}',
				'{$info['bill_note']}', NULL, NULL, '{$info['create_user']}', '{$info['create_time']}', '{$info['put_in_type']}', '{$info['send_goods_sn']}', '{$info['pro_id']}', '{$info['pro_name']}', '{$info['jiejia']}', '{$info['goods_total']}', '{$info['yuanshichengben']}', '{$info['shijia']}','{$info['production_manager_name']}',{$label_price_total}
				);";

			$pdo->query($sql);

			$id = $pdo->lastInsertId();

			$bill_no= $billModel->create_bill_no('L',$id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$id}";
			$pdo->query($sql);

			//3、添加状态表
			$sql = "INSERT INTO `warehouse_bill_status` (`id`, `bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES (NULL, '{$id}', '{$bill_no}', '1', '{$info['create_time']}', '{$info['create_user']}', '{$info['create_ip']}');";
			$pdo->query($sql);

			$model = new WarehouseGoodsModel(21);
			$goods_id = $model->_getGoodsId();
			//4、明细添加
			//var_dump($data);exit;
			foreach (array_reverse($data) as $key=>$value)
			{	
			    		    
				floatval($goods_id);
				$goods_id++;
				$goods_id=number_format($goods_id,0,"","");
				if(empty($value['with_fee'])) $value['with_fee']=0;
				if(empty($value['certificate_fee'])) $value['certificate_fee']=0;
				if(empty($value['operations_fee'])) $value['operations_fee']=0;
				if (SYS_SCOPE == 'zhanting') {
				    $value['goods_id'] = $value['goods_id']> 0 ? $value['goods_id'] : $goods_id;
				} else if (SYS_SCOPE == 'boss') {
				    $value['goods_id'] = $goods_id;
				}
				$value['put_in_type'] = $info['put_in_type'];
				$value['prc_id'] = $info['pro_id'];
				$value['prc_name'] = $info['pro_name'];
				$value['company'] = $info['to_company_name'];
				$value['warehouse'] = $info['to_warehouse_name'];
				$value['company_id'] = $info['to_company_id'];
				$value['warehouse_id'] = $info['to_warehouse_id'];
				$value['jiejia'] = $info['jiejia'];
				$value['account'] = $info['jiejia'];//数据字段重复
				$value['addtime']   = '0000-00-00 00:00:00';//收货单保存记录时间，审核通过后写入收货单审核时间
				
				if (SYS_SCOPE == 'boss'){
	                if(!empty($value['zhushitiaoma'])){
	                    $zhushitiaom_array = explode('#',$value['zhushitiaoma']);
	                    $sql = "select sum(if(ifnull(yuanshichengbenjia_zs,0)>0,yuanshichengbenjia_zs,yuanshichengbenjia)) as zhushi_yuanshichengbenjia_zs,sum(yuanshichengbenjia) as zhushi_yuanshichengbenjia_kela,count(goods_id) as goods_rows from warehouse_goods where goods_id in ('". implode("','", $zhushitiaom_array) ."')";
	                    $res_zs = $this->db()->getRow($sql);
	                    if($res_zs){
	                        if($res_zs['goods_rows'] == count($zhushitiaom_array)){
	                            if(bccomp($res_zs['zhushi_yuanshichengbenjia_kela'], $value['chengbenjia'],2)==1){
	                            	return "录入主石条码". $value['zhushitiaoma'] ."有误：主石条码成本大于当前货品成本";
	                            }
                                $peijian_chengben = $value['chengbenjia'] - $res_zs['zhushi_yuanshichengbenjia_kela'] ; //配件成本
	                            $value['yuanshichengbenjia_zs'] = round( $res_zs['zhushi_yuanshichengbenjia_zs'] + $peijian_chengben ,2); //货品真正的采购成本 舟山货品的舟山原始成本+BDD货品的BDD原始成本
	                        }else{
	                            return "主石条码". $value['zhushitiaoma'] ."不存在";
	                        }
	                    }else{
	                    	return "主石条码". $value['zhushitiaoma'] ."不存在"; 
	                    }
	                } 
                }

				$sql = $this->insertSql($value,'warehouse_goods');
				$pdo->query($sql);

				//插入单据明细表
				$label_price = 0;
				if(SYS_SCOPE=="zhanting" && !empty($value['biaoqianjia'])){
				    $label_price =(float)$value['biaoqianjia'];
				}				
				$sql = "INSERT INTO `warehouse_bill_goods` (`id`, `bill_id`, `bill_no`, `bill_type`, `goods_id`, `goods_sn`, `goods_name`, `num`, `caizhi`, `jinzhong`, `yanse`, `zuanshidaxiao`, `yuanshichengben`, `sale_price`, `shijia`, `in_warehouse_type`, `account`, `addtime`,`warehouse_id`,`label_price`) VALUES (NULL, '{$id}', '{$bill_no}', 'L', '{$value['goods_id']}', '{$value['goods_sn']}', '{$value['goods_name']}', '1', '{$value['caizhi']}', '{$value['jinzhong']}', '{$value['yanse']}', '{$value['zuanshidaxiao']}', '{$value['chengbenjia']}', '{$value['chengbenjia']}', '0', '{$info['put_in_type']}', '{$info['jiejia']}', '{$time}','{$info['to_warehouse_id']}',{$label_price})";
				$pdo->query($sql);
	
			}
			
			//保存结算商
			//$billPayArr=$_SESSION['bill_pay'];
			$olddo_str=$_COOKIE['bill_pay'];
			$billPayArr=unserialize($olddo_str);
			if(!empty($billPayArr)){
			  foreach ($billPayArr as $row){
				  $sql = "INSERT INTO `warehouse_bill_pay` (`id`, `bill_id`, `pro_id`, `pro_name`, `pay_content`, `pay_method`, `tax`, `amount`) VALUES (NULL, '{$id}', '{$row['pro_id']}', '{$row['pro_name']}','{$row['pay_content']}','{$row['pay_method']}','{$row['pay_tax']}','{$row['amount']}' )";
				  $pdo->query($sql);
			  }
			}
		  
		}
		catch(Exception $e){//捕获异常
			//echo '<pre>';print_r($e);echo '</pre>';die;
			//print_r($data);exit;
			$pdo->rollback();//事务回滚
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
			return $e->getMessage();
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		//清除结算商session数组
		setcookie('bill_pay','',time()-10);
		return array('id'=>$id, 'bill_no'=>$bill_no);
		// return $id;
	}


	public function  up_info($data,$info){
	    $result = array('error'=>"","success"=>0);
		$pdo = $this->db()->db();//pdo对象
		$time = date('Y-m-d H:i:s');
		//这里查询入库单据的商品goods_id

		$extgoodss = "SELECT `bg`.* FROM `warehouse_bill_goods` AS `bg` LEFT JOIN `warehouse_goods` AS `g` ON `bg`.`goods_id` = `g`.`goods_id` WHERE `bg`.`bill_id` ='{$info['id']}' order by `bg`.`id` asc";

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
			$label_price_total = 0;
			if(SYS_SCOPE=="zhanting"){
			    $label_price_total = array_sum(array_column($data,"biaoqianjia"));
			}
			//1、更新Bill主表

			$sql ="UPDATE warehouse_bill SET `goods_num`='{$info['goods_num']}',`bill_note`='{$info['bill_note']}', `pro_id`='{$info['pro_id']}',`pro_name`='{$info['pro_name']}',`yuanshichengben`='{$info['yuanshichengben']}', `goods_total`='{$info['goods_total']}', `shijia`='{$info['shijia']}', `to_warehouse_id`='{$info['to_warehouse_id']}',`to_warehouse_name`='{$info['to_warehouse_name']}',`to_company_id`='{$info['to_company_id']}',`to_company_name`='{$info['to_company_name']}' ,`jiejia`='{$info['jiejia']}',`send_goods_sn`='{$info['send_goods_sn']}',put_in_type='{$info['put_in_type']}',production_manager_name='{$info['production_manager_name']}',label_price_total={$label_price_total}   WHERE `id`='{$info['id']}'";
			// echo $sql;exit;

			$pdo->query($sql);


			//更新其他表表
			$sql ="UPDATE `warehouse_bill_goods` as a,`warehouse_goods` as b SET a.`in_warehouse_type`='{$info['put_in_type']}', a.`account`='{$info['jiejia']}', b.`put_in_type`='{$info['put_in_type']}', b.`account`='{$info['jiejia']}',b.`jiejia` = '{$info['jiejia']}'    WHERE a.`bill_id`='{$info['id']}' and a.goods_id = b.goods_id";
			$pdo->query($sql);
			//更新货品入库仓
			$sql ="UPDATE warehouse_goods SET `warehouse_id`='{$info['to_warehouse_id']}',`warehouse`='{$info['to_warehouse_name']}',`company_id`='{$info['to_company_id']}',`company`='{$info['to_company_name']}'   WHERE `goods_id` in ({$goods_id_all})";
			//echo $sql;exit;
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
						if(empty($val['with_fee'])) $val['with_fee']=0;
						if(empty($val['certificate_fee'])) $val['certificate_fee']=0;
						if(empty($val['operations_fee'])) $val['operations_fee']=0;
						$val['goods_id'] = $goods_id;
						$val['put_in_type'] = $info['put_in_type'];
						$val['prc_id'] = $info['prc_id'];
						$val['prc_name'] = $info['prc_name'];
						$val['company'] = $info['to_company_name'];
						$val['warehouse'] = $info['to_warehouse_name'];
						$val['company_id'] = $info['to_company_id'];
						$val['warehouse_id'] = $info['to_warehouse_id'];
						$val['addtime'] = $time;

                        //根据主石条码计算舟山成本价 
						if (SYS_SCOPE == 'boss'){
			                if(!empty($val['zhushitiaoma'])){
			                    $zhushitiaom_array = explode('#',$val['zhushitiaoma']);
			                    $sql = "select sum(if(ifnull(yuanshichengbenjia_zs,0)>0,yuanshichengbenjia_zs,yuanshichengbenjia)) as zhushi_yuanshichengbenjia_zs,sum(yuanshichengbenjia) as zhushi_yuanshichengbenjia_kela,count(goods_id) as goods_rows from warehouse_goods where goods_id in ('". implode("','", $zhushitiaom_array) ."')";
			                    $res_zs = $this->db()->getRow($sql);
			                    if($res_zs){
			                        if($res_zs['goods_rows'] == count($zhushitiaom_array)){
			                            if(bccomp($res_zs['zhushi_yuanshichengbenjia_kela'], $val['chengbenjia'],2)==1){
												$pdo->rollback();
												$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);			                        		                        	
					                            $result['error'] = "录入主石条码". $val['zhushitiaoma'] ."有误：主石条码成本大于当前货品成本";
					                            return 	$result;                            	
			                            }
		                                $peijian_chengben = $val['chengbenjia'] - $res_zs['zhushi_yuanshichengbenjia_kela'] ; //配件成本
			                            $val['yuanshichengbenjia_zs'] = round( $res_zs['zhushi_yuanshichengbenjia_zs'] + $peijian_chengben ,2); //货品真正的采购成本 舟山货品的舟山原始成本+BDD货品的BDD原始成本
			                        }else{
										$pdo->rollback();
										$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);			                        		                        	
			                            $result['error'] = "主石条码". $val['zhushitiaoma'] ."不存在";
			                            return 	$result;
			                        }
			                    }else{
										$pdo->rollback();
										$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);			                        		                        	
			                            $result['error'] = "主石条码". $val['zhushitiaoma'] ."不存在";
			                            return 	$result; 
			                    }
			                } 
		                }                

						

						$sql = $this->insertSql($val,'warehouse_goods');
						$pdo->query($sql);

						//插入单据明细表
						$label_price = 0;
						if(SYS_SCOPE=="zhanting" && !empty($val['biaoqianjia'])){
						    $label_price =(float)$val['biaoqianjia'];
						}
						$sql = "INSERT INTO `warehouse_bill_goods` (`id`, `bill_id`, `bill_no`, `bill_type`, `goods_id`, `goods_sn`, `goods_name`, `num`, `caizhi`, `jinzhong`, `yanse`, `zuanshidaxiao`, `yuanshichengben`, `sale_price`, `shijia`, `in_warehouse_type`, `account`, `addtime`,`label_price`) VALUES (NULL, '{$info['id']}', '{$info['bill_no']}', 'L', '{$goods_id}', '{$val['goods_sn']}', '{$val['goods_name']}', '1', '{$val['caizhi']}', '{$val['jinzhong']}', '{$val['yanse']}', '{$val['zuanshidaxiao']}', '{$val['chengbenjia']}', '{$val['chengbenjia']}', 0, '{$info['put_in_type']}', '{$info['jiejia']}', '{$time}',{$label_price})";
						//echo $sql;
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
				foreach ($data as $value)
				{  
					if($value['goods_id']!=0){						
                        //根据主石条码计算舟山成本价 
						if (SYS_SCOPE == 'boss'){
			                if(!empty($value['zhushitiaoma'])){
			                    $zhushitiaom_array = explode('#',$value['zhushitiaoma']);
			                    $sql = "select sum(if(ifnull(yuanshichengbenjia_zs,0)>0,yuanshichengbenjia_zs,yuanshichengbenjia)) as zhushi_yuanshichengbenjia_zs,sum(yuanshichengbenjia) as zhushi_yuanshichengbenjia_kela,count(goods_id) as goods_rows from warehouse_goods where goods_id in ('". implode("','", $zhushitiaom_array) ."')";
			                    $res_zs = $this->db()->getRow($sql);
			                    if($res_zs){
			                        if($res_zs['goods_rows'] == count($zhushitiaom_array)){
			                            if(bccomp($res_zs['zhushi_yuanshichengbenjia_kela'], $value['chengbenjia'],2)==1){
												$pdo->rollback();
												$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);			                        		                        	
					                            $result['error'] = "录入主石条码". $value['zhushitiaoma'] ."有误：主石条码成本大于当前货品成本";
					                            return 	$result;                            	
			                            }
		                                $peijian_chengben = $value['chengbenjia'] - $res_zs['zhushi_yuanshichengbenjia_kela'] ; //配件成本
			                            $value['yuanshichengbenjia_zs'] = round( $res_zs['zhushi_yuanshichengbenjia_zs'] + $peijian_chengben ,2); //货品真正的采购成本 舟山货品的舟山原始成本+BDD货品的BDD原始成本
			                        }else{
										$pdo->rollback();
										$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);			                        		                        	
			                            $result['error'] = "主石条码". $value['zhushitiaoma'] ."不存在";
			                            return 	$result;
			                        }
			                    }else{
										$pdo->rollback();
										$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);			                        		                        	
			                            $result['error'] = "主石条码". $value['zhushitiaoma'] ."不存在";
			                            return 	$result; 
			                    }
			                } 
		                }
		                					
						$sql = $this->update($value,array('goods_id'=>$value['goods_id']),'warehouse_goods');
						//echo $sql;
						//修改货品表
						$pdo->query($sql);

						//修改单据明细表
						$sql = "UPDATE `warehouse_bill_goods` SET `goods_name`='{$value['goods_name']}', `num`='1', `caizhi`= '{$value['caizhi']}', `jinzhong`='{$value['jinzhong']}', `yanse`='{$value['yanse']}', `zuanshidaxiao`= '{$value['zuanshidaxiao']}', `yuanshichengben`='{$value['chengbenjia']}', `sale_price`='{$value['chengbenjia']}',`in_warehouse_type`='{$info['put_in_type']}', `account`='{$info['jiejia']}' WHERE `goods_id`='".$value['goods_id']."' and bill_id = {$info['id']}";
						$pdo->query($sql);
					}
				}
			}
			//有明细修改================================end========================
			$pdo->commit();//如果没有异常，就提交事务
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
			//return true;	
			$result['success'] = 1;
			return 	$result;	
		}
		catch(Exception $e){			
			$pdo->rollback();
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
			$result['error'] = "事物执行失败，请联系技术人员处理:error:".$sql;
			return $result;
		}
		
	}

/*
	function getRow_Bill_id($bill_id)
	{
		$sql = "SELECT `id` as detail_id,`bill_id`,`put_in_type`,`send_goods_sn`,`prc_id`,`prc_name`,`jiejia` FROM ".$this->table()." WHERE `bill_id` = ".$bill_id;
		$pr = "SELECT `warehouse_bill_goods`.`sale_price`,`warehouse_bill_goods`.`shijia` FROM `warehouse_bill_goods` WHERE `bill_id`=".$bill_id;
		$arr = $this->db()->getAll($pr);
		$xiaoshoujia ='0';
		$chengbenjia='0';
		foreach($arr as $key=>$val){
			$chengbenjia+=$val['chengbenjia'];
		}
		$res= $this->db()->getRow($sql);
		$res['xiaoshoujiazongji']= $xiaoshoujia;
		$res['chengbenjiazongji']= $chengbenjia;
		return $res;
	}*/

	//编辑收货单excel需要的数据，所取字段，前后顺序不要颠倒，不要乱改乱动这个方法，你们改了我会不开心的/(ㄒoㄒ)/~~--------JUAN
	public function get_Bill_goods($billno)
	{
		$sql = "select g.id,g.goods_id, g.goods_sn, g.mo_sn,g.product_type, g.cat_type,  g.caizhi, g.jinzhong, g.jinhao, g.zhuchengsezhongjijia, g.zhuchengsemairudanjia, g.zhuchengsemairuchengben, g.zhuchengsejijiadanjia, g.zhushi, g.zhushilishu, g.zuanshidaxiao, g.zhushizhongjijia, g.zhushiyanse, g.zhushijingdu, g.zhushimairudanjia, g.zhushimairuchengben, g.zhushijijiadanjia, g.zhushiqiegong, g.zhushixingzhuang, g.zhushibaohao, g.zhushiguige, g.fushi, g.fushilishu, g.fushizhong, g.fushizhongjijia, g.fushiyanse, g.fushijingdu, g.fushimairudanjia, g.fushimairuchengben, g.fushijijiadanjia, g.fushixingzhuang, g.fushibaohao, g.fushiguige, g.zongzhong, g.mairugongfeidanjia, g.mairugongfei, g.jijiagongfei, g.shoucun, g.ziyin, g.danjianchengben, g.peijianchengben, g.qitachengben,g.yuanshichengbenjia as chengbenjia, g.mingyichengben as jijiachengben, g.jiajialv, g.zuixinlingshoujia, g.pinpai, g.changdu, g.zhengshuhao, g.yanse, g.jingdu, g.peijianshuliang, g.guojizhengshu, g.zhengshuleibie, g.goods_name, g.kela_order_sn, g.shi2, g.shi2lishu, g.shi2zhong, g.shi2zhongjijia, g.shi2mairudanjia, g.shi2mairuchengben, g.shi2jijiadanjia, g.qiegong, g.paoguang, g.duichen, g.yingguang, g.buchan_sn, g.mingyichengben, g.zuanshizhekou, g.zhengshuhao2, g.guojibaojia, g.gongchangchengben,g.tuo_type, g.gemx_zhengshu, g.jietuoxiangkou,g.zhushitiaoma,g.color_grade,`g`.`supplier_code`,g.luozuanzhengshu,g.with_fee,g.certificate_fee,g.operations_fee,g.peijianjinchong,g.shi2baohao,g.shi3,g.shi3lishu,g.shi3zhong,g.shi3zhongjijia,g.shi3mairudanjia,g.shi3mairuchengben,g.shi3jijiadanjia,g.shi3baohao,g.guojian_wgt from warehouse_bill_goods as og , warehouse_goods as g where og.goods_id = g.goods_id  and og.bill_no = '" . $billno . "' order by og.id asc ";

		return $this->db()->getAll($sql);
	}

	//取全部的详细信息
	public function get_data($arr = array())
	{
		$sql = "SELECT `bg`.`id`,`bg`.`goods_id`,`bg`.`goods_sn`,`bg`.`goods_name`,`bg`.`num`,`bg`.`caizhi`,`bg`.`jinzhong`,`bg`.`yanse`,`bg`.`zuanshidaxiao`,`bg`.`sale_price`,`bg`.`shijia`,
		`g`.*
		 FROM `warehouse_bill_goods` AS `bg` LEFT JOIN `warehouse_goods` AS `g`  ON `bg`.`goods_id` = `g`.`goods_id`";
		$str = "";
		if(isset($arr['bill_id']) && $arr['bill_id'] != "")
		{
			$str .= "`bg`.`bill_id` = ".$arr['bill_id']." AND " ;
		}

		if( isset($arr['bill_no']) && $arr['bill_no'] != "")
		{
			$str .= "`bg`.`bill_no` = '".$arr['bill_no']."' AND " ;
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " order by `bg`.`id` asc";
		// echo $sql;die;
		return $this->db()->getAll($sql);
	}

	//取全部的明细信息---分页 JUAN
	public function getPagetList($arr = array(),$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT `bg`.`id`,`bg`.`goods_id`,`bg`.`goods_sn`,`bg`.`goods_name`,`bg`.`num`,`bg`.`caizhi`,`bg`.`jinzhong`,`bg`.`yanse`,`bg`.`zuanshidaxiao`,`bg`.`sale_price`,`bg`.`shijia`,`g`.* FROM `warehouse_bill_goods` AS `bg` LEFT JOIN `warehouse_goods` AS `g`  ON `bg`.`goods_id` = `g`.`goods_id`";
		$str = "";
		if(isset($arr['bill_id']) && $arr['bill_id'] != "")
		{
			$str .= "bg.`bill_id` = ".$arr['bill_id']." AND " ;
		}

		if(isset($arr['bill_no']) && $arr['bill_no'] != "")
		{
			$str .= "bg.`bill_no` = '".$arr['bill_no']."' AND " ;
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY bg.id asc";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}


	//这里调用接口 传给excel type 的信息
	public function  getStyle($goods_sn){

		$styleModel = new ApiStyleModel();
		//$info  = $styleModel->GetStyleInfoBySn($goods_sn);
        $where = " `style_sn` = '".$goods_sn."'";
        $sql = "select `style_id`, `style_sn`,`bang_type`, `style_name`, `product_type`, `style_type`, `create_time`, `modify_time`, `check_time`, `cancel_time`, `check_status`,`xilie`, `is_sales`, `is_made`, `dismantle_status`, `style_status`, `style_remark` from  `front`.`base_style_info` " .
            "where ".$where." ;";
        $info =  $this->db()->getRow($sql);

		if($info==array()){
			return false;
		}

		$product_type=$info['product_type'];
		$CatType = $info['style_type'];
		$ProductType=$styleModel->GetProductType($product_type);
		$CatType = $styleModel->GetCatType($CatType);

		$info['cat_type_name']= $CatType[0]['name'];
		$info['product_type_name']=$ProductType[0]['name'];

		$str='';
		foreach ($info as $k=>$v) {
			$str.=$v.';';
		}
		$str = rtrim($str,';');
		return $str;
	}

	public function checkBillL($bill_id,$pdo){
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
	        $prc_id=$bill_info['pro_id'];
	        $prc_name=$bill_info['pro_name'];

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
					$sql = "update `goods_warehouse` set `add_time` = '{$time}' where `good_id` = '{$v['goods_id']}'";
    	            $pdo->query($sql);
				}
	            $goods_id_str .= ',\'' . $v ['goods_id'] . '\'';
	        }
	        $goods_id_str = ltrim($goods_id_str, ',');
	        // 改变货品状态 warehouse_goods，审核入库添加时间、最后一次转仓时间
	        $tax_rate = 0;
	        if(date("Y-m-d")>='2018-05-01'){
	            $tax_rate = 13;
	        }
	        $sql = "UPDATE `warehouse_goods` SET `is_on_sale`=2,`addtime`='{$time}',`change_time`='{$time}',prc_id='{$prc_id}',prc_name='{$prc_name}',tax_rate={$tax_rate} WHERE `goods_id` IN ({$goods_id_str}) and `is_on_sale`=1";
	        //$pdo->query($sql);
	        $changed=$pdo->exec($sql);
	        if($changed<>count(explode(",",$goods_id_str))) {
	            $pdo->rollback();//事务回滚
	            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	            //return array('success'=> 0 , 'error'=>'货品状态不是收货中'.$goods_id_str);
	            return false;
	        }
	 
	        //boss 舟山入库的石头拿去镶嵌 在BOSS入库后名义成本虚高 需要减去舟山货号卖给BDD的加价部分
            if(SYS_SCOPE=='boss'){
            	$sql = "update warehouse_goods set mingyichengben = mingyichengben - yuanshichengbenjia + yuanshichengbenjia_zs where goods_id IN ({$goods_id_str}) and yuanshichengbenjia_zs>0 and zhushitiaoma<>'' and cat_type1 not in ('裸石','彩钻')";
                $pdo->query($sql);
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
	            //echo $sql;
	            //echo $e->getMessage();
	            //exit();
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
     *舟山系统审核L单后自动生成P单视为自动销售给BDD
     * P单取价规则根据款式分类配置
     *
    */ 
    public function autoMake_BillInfoP($bill_id,$pdo){
	    try{	
	        /**
	         * 获取单据下的商品明细 *
	         */

	        $goods_id_str = '';
	        $sql = "SELECT bg.goods_name,bg.`goods_id`,g.product_type,g.cat_type,bg.sale_price,bg.goods_sn,g.zhengshuhao, g.jietuoxiangkou, g.shoucun , g.caizhi , g.yanse FROM `warehouse_bill_goods` AS `bg` LEFT JOIN `warehouse_goods` AS `g` ON `bg`.`goods_id` = `g`.`goods_id` WHERE `bill_id`={$bill_id}";
	        $bill_goods = $this->db()->getAll($sql);
            
	        if(empty($bill_goods))
	        	return false;
            $goods_all_count = count($bill_goods);

            //生成单据主表记录
	        $sql = "INSERT INTO `warehouse_bill` (`bill_no`, `bill_type`,`bill_status`,`goods_num` , `bill_note`,send_goods_sn, `goods_total`,`shijia` , `pifajia` , `create_user` , `create_time`,`check_user`,`check_time`,`from_company_id` , `from_company_name`, `to_customer_id`, `to_company_id`, `to_company_name`, `to_warehouse_id`, `to_warehouse_name`, `out_warehouse_type`,`label_price_total`,`p_type`)
                    select '','P',2,count(bg.id),b.bill_no,b.bill_no,sum(bg.sale_price),sum(bg.shijia),sum(bg.pifajia),b.create_user,b.check_time,'{$_SESSION['userName']}',b.check_time,b.to_company_id,b.to_company_name,'1',null,'',0,'',b.put_in_type,0,'' from warehouse_bill b,warehouse_bill_goods bg where b.id=bg.bill_id and b.id='{$bill_id}' group by b.id";
	        $pdo->query($sql);
	        $id = $pdo->lastInsertId();
            $warehouse_bill_model = new WarehouseBillModel(22);
            $warehouse_bill_model->create_bill_no();  
			$bill_no = $warehouse_bill_model->create_bill_no("P",$id);
			$sql = "UPDATE `warehouse_bill` SET `bill_no` = '{$bill_no}' WHERE `id` = {$id}";			
			$pdo->query($sql);

            $sql = "INSERT INTO `warehouse_bill_goods` (
					`bill_id` , `bill_no` , `bill_type` , `goods_id` , `goods_sn` ,
					`goods_name` , `num` , `jinzhong` , `jingdu` , `yanse` , `zhengshuhao` ,
					`zuanshidaxiao` ,`sale_price`, `shijia` , `pifajia` ,  `addtime`,`management_fee`,`label_price`
					)
			         select '{$id}','{$bill_no}','P',bg.goods_id,bg.goods_sn,bg.goods_name,1,g.jinzhong,g.jingdu,g.yanse,g.zhengshuhao,g.zuanshidaxiao,g.yuanshichengbenjia,round(g.yuanshichengbenjia*c.jiajialv,2),round(g.yuanshichengbenjia*c.jiajialv,2),now(),0,0 
			         from warehouse_bill b,warehouse_bill_goods bg,warehouse_goods g left join front.app_cat_type c on g.cat_type1=c.cat_type_name where bg.bill_id=b.id and bg.goods_id=g.goods_id and b.id='{$bill_id}'";

            $pdo->query($sql);

            $sql = "update warehouse_bill b set shijia=(select sum(bg.shijia) from warehouse_bill_goods bg where bg.bill_id=b.id ) where id='{$id}'";
            $pdo->query($sql); 
	       
            $sql = "update warehouse_goods g,warehouse_bill_goods bg set g.is_on_sale=3 where g.goods_id=bg.goods_id and bg.bill_id='{$id}' and g.is_on_sale=2 "; 
	        //$pdo->query($sql);
	        $changed=$pdo->exec($sql);
	        if($changed<>$goods_all_count) {
	            $pdo->rollback();//事务回滚
	            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	            //return array('success'=> 0 , 'error'=>'货品状态不是收货中'.$goods_id_str);	            
	            return false;
	        }
	
	    } catch(Exception $e){ // 捕获异常
	        $pdo->rollback();//事务回滚
	        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交
	        if($_SESSION['userName'] == 'admin')
	        {
	            echo $sql;
	            echo $e->getMessage();
	            //exit();
	        }
	        return false;
	    }
	   
	    return true;         
    } 

    /*
    *获取需要同步的收货单及货号信息
    */
    public function getData_For_Tongbu($bill_id){
    	$bill_info = array();    	
    	$goods_id_array = array();

    	$sql= "select g.*,c.cat_type_id as cid,c.jiajialv as cjiajialv from warehouse_goods g left join front.app_cat_type c on g.cat_type1=c.cat_type_name,warehouse_bill_goods bg where g.goods_id=bg.goods_id and bg.bill_id='{$bill_id}'";
    	$goods_id_array = $this->db()->getAll($sql);
    	if(!empty($goods_id_array)){
    		foreach ($goods_id_array as $key => $goods) {
    			if(empty($goods['cid']) || empty($goods['cjiajialv'])){
                    return null;
    			}else{
    				//同步到BDD系统后原始成本价= 舟山原始成本价* 加价率
    				$goods['yuanshichengbenjia_zs'] = $goods['yuanshichengbenjia'];
                    $goods['yuanshichengbenjia'] = round($goods['yuanshichengbenjia']*$goods['cjiajialv'],2);
                    if(empty($goods['yuanshichengbenjia'])){
                    	return null;
                    }
                    $goods['chengbenjia'] =    $goods['yuanshichengbenjia'];
                    $goods['jijiachengben'] =  $goods['yuanshichengbenjia'];
                    $goods['oldsys_id'] = $goods['prc_id'];
                    $goods['att1'] = $goods['prc_name'];
                    $goods['prc_id'] = 641;
                    $goods['prc_name'] = '舟山市BDD商贸有限公司';
                    $goods['put_in_type'] = 3;//舟山销售给BDD入库方式全部为代销
                    $goods['is_on_sale'] =2;                  
                    unset($goods['cid']);
                    unset($goods['cjiajialv']);
                    $bill_info['goods_id'][] = $goods;
    			}
    		}    		
    	}else{
    		return null;
    	} 


    	//获取需要同步的单据明细
    	$sql= "select bg.*,c.cat_type_id as cid,c.jiajialv as cjiajialv,g.yuanshichengbenjia from warehouse_goods g left join front.app_cat_type c on g.cat_type1=c.cat_type_name,warehouse_bill_goods bg where g.goods_id=bg.goods_id and bg.bill_id='{$bill_id}' order by bg.goods_id";
    	$bill_goods_array = $this->db()->getAll($sql);
    	$goods_total = 0;
    	if(!empty($bill_goods_array)){
            foreach ($bill_goods_array as $key => $bill_goods) {
    			if(empty($bill_goods['cid'])){
                    return null;
    			}else{
    				//同步到BDD系统后原始成本价= 舟山原始成本价* 加价率
                    $bill_goods['sale_price'] = round($bill_goods['yuanshichengbenjia']*$bill_goods['cjiajialv'],2);
                    $bill_goods['yuanshichengben'] =  $bill_goods['sale_price']; 
                    $bill_goods['chengbenjia'] =  $bill_goods['sale_price']; 
                    $bill_goods['mingyijia'] =  $bill_goods['sale_price']; 
                    $goods_total = $goods_total + $bill_goods['yuanshichengben'];                 
                    unset($bill_goods['cid']);
                    unset($bill_goods['cjiajialv']);
                    unset($bill_goods['yuanshichengbenjia']);
                    $bill_info['bill_goods'][] = $bill_goods;
    			}
            }
        }else{
        	return null;
        }		
    	
    	//获取单据主记录
    	$sql = "select *from warehouse_bill where id='{$bill_id}' and bill_status=2";
    	$bill_info['bill'] = $this->db()->getAll($sql);
        if(empty($bill_info['bill']))
        	return null;
        $bill_info['bill'][0]['goods_total'] = $goods_total;
        $bill_info['bill'][0]['pro_id'] = 641;
        $bill_info['bill'][0]['pro_name'] = '舟山市BDD商贸有限公司';
        $bill_info['bill'][0]['put_in_type'] = 3; //舟山销售给BDD入库方式全部为代销

    	return $bill_info;
    }

    /*
     *舟山系统审核L单后自动生成P单视为自动销售给BDD再自动生成BDDL单
     * P单取价规则根据款式分类配置
     *
    */ 
    public function auto_CopyBillL($bill_info){
	    if(empty($bill_info))
	    		return false;
	    if(empty($bill_info['goods_id']))
	    		return false;
	    if(empty($bill_info['bill']))
	    		return false;
	    if(empty($bill_info['bill_goods']))
	    		return false;  

		//echo "<pre>";
		//print_r($bill_info);

    	$pdo = $this->db()->db();
	    try{
	    	$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务	    			    			    	
	        
	        //保存货号
            foreach ($bill_info['goods_id'] as $key => $goods) {
            	$goods['id'] = 0 ;
            	$warehouse_goods_key = array_keys($goods);
            	$insert_value_str = '';
            	foreach ($goods as $k => $v) {
            		$insert_value_str .=",?";
            	}
            	$insert_value_str = ltrim($insert_value_str,',');
            	$sql ="insert into warehouse_goods (".implode(",",$warehouse_goods_key).") values ({$insert_value_str})";
            	$stmt = $pdo->prepare($sql);
            	$res=$stmt->execute(array_values($goods));	            
            }

            //保存单据主表
            $bill_id = 0;
            $to_warehouse_id = 0;
            $pro_id = 0;
            $pro_name = '';
            $goods_total = 0;
            $bill_put_in_type ='';            
            foreach ($bill_info['bill'] as $key => $bill) {            	
            	$bill['id'] = 0 ;
            	$to_warehouse_id = $bill['to_warehouse_id'];
            	$pro_id = $bill['pro_id'];
            	$pro_name = $bill['pro_name'];
            	$goods_total = $bill['goods_total'];
            	$bill_put_in_type =$bill['put_in_type'];
                $warehouse_bill_key = array_keys($bill);  
                $insert_value_str = '';          	
            	foreach ($bill as $k => $v) {
            		$insert_value_str .=",?";
            	}
            	$insert_value_str = ltrim($insert_value_str,',');            	
            	$sql ="insert into warehouse_bill (".implode(",",$warehouse_bill_key).") values ({$insert_value_str})";
            	$stmt = $pdo->prepare($sql);
            	$res=$stmt->execute(array_values($bill));	
	            $bill_id = $pdo->lastInsertId();
            }

            //保存单据明细
            foreach ($bill_info['bill_goods'] as $key => $bill_goods) {
            	$bill_goods['id'] = 0 ;
            	$bill_goods['bill_id'] = $bill_id ;
            	$warehouse_bill_goods_key = array_keys($bill_goods); 
            	$insert_value_str = '';           	
            	foreach ($bill_goods as $k => $v) {
            		$insert_value_str .=",?";
            	}
            	$insert_value_str = ltrim($insert_value_str,',');              	
            	$sql ="insert into warehouse_bill_goods (".implode(",",$warehouse_bill_goods_key).") values ({$insert_value_str})";
             	$stmt = $pdo->prepare($sql);
            	$res=$stmt->execute(array_values($bill_goods));	            
            }

            //保存单据结算内容
            $sql="insert into warehouse_bill_pay (bill_id,pro_id,pro_name,pay_content,pay_method,tax,amount) values ('{$bill_id}','{$pro_id}','{$pro_name}','3','2','0','{$goods_total}')";
            $pdo->query($sql);


            //推送财务结算数据 舟山销售给BDD入库方式全部为代销
            /*
            if($bill_put_in_type==1 || $bill_put_in_type==2){
                $sql = "INSERT INTO finance.goods (item_id, order_id, zhengshuhao, goods_status, item_type, company, prc_id, prc_name, prc_num, type, pay_content, storage_mode, make_time, check_time, total, pay_apply_status, pay_apply_number, add_time)  (select b.bill_no,0,'',2,1,58,b.pro_id,b.pro_name,b.send_goods_sn,2,p.pay_content,b.put_in_type,b.create_time,b.check_time,p.amount,'1','',now() from warehouse_shipping.warehouse_bill b,warehouse_shipping.warehouse_bill_pay p where b.id=p.bill_id and b.id='{$bill_id}') ";
                $pdo->query($sql);
            }*/
            //if($bill_put_in_type==3 || $bill_put_in_type==4){            
                $sql = "INSERT INTO finance.goods (item_id, order_id, zhengshuhao, goods_status, item_type, company, prc_id, prc_name, prc_num, type, pay_content, storage_mode, make_time, check_time, total, add_time)   (select g.goods_id,0,g.zhengshuhao,g.is_on_sale,g.cat_type1,58,g.prc_id,g.prc_name,'',1,'',g.put_in_type,g.addtime,g.addtime,bg.sale_price,now() from warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_goods g where bg.goods_id=g.goods_id and bg.bill_id='{$bill_id}') ";
                $pdo->query($sql);
            //}

	        //货品上架 获取入库仓的默认柜位ID 
	       	$sql = "select id from warehouse_box where warehouse_id='{$to_warehouse_id}' AND `box_sn` = '0-00-0-0' AND `is_deleted` = 1";
	        $default_box = $this->db()->getOne($sql);
	        $default_box_id  = $default_box ? $default_box : 0 ;	//入库仓默认柜位
	       	
	        //货品上架 添加货品和仓储关系表 (goods_warehouse ) [获取当前入库仓的默认柜位，把每个货品放置在该默认柜位上]
	        foreach($bill_info['goods_id'] as $v){
	            $sql = "select count(*) from goods_warehouse where good_id='{$v['goods_id']}'";
	            $checkExist = $this->db()->getOne($sql);
	            if(!$checkExist){
    	            $sql = "INSERT INTO `goods_warehouse`(`good_id`, `warehouse_id`, `box_id` , `add_time`,`create_user`) VALUES ( '{$v['goods_id']}' , {$to_warehouse_id} ,  {$default_box_id} ,now(),'SYSTEM')";
    	            $pdo->query($sql);	
	            }
				else//已经存在就更新入库时间为当前仓库最新的审核时间 boss717
				{
					$sql = "update `goods_warehouse` set `add_time` = now() where `good_id` = '{$v['goods_id']}'";
    	            $pdo->query($sql);
				}	            
	        }



            $stmt=null;  	
	    } catch(Exception $e){ // 捕获异常
	        $pdo->rollback();//事务回滚
	        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交
	        if($_SESSION['userName'] == 'admin')
	        {
	            //echo $sql;
	            //echo $e->getMessage();
	            //exit();
	        }
	        return false;
	    }
	   
	    return $pdo;         
    } 

    //复核收货单
	public function checkBillL_repeat($bill_id){
        $now=date('Y-m-d H:i:s');
        $sql="update warehouse_bill set fin_check_status=2,fin_check_time='{$now}',fin_check_user='{$_SESSION['userName']}' where id='".$bill_id."'";
        return $this->db()->db()->exec($sql);
	}	

	
	/*
	 * 审核单据
	 * 单据ID WAREHOUSE_BILL 的主键
	 * 1/改变单据状态 warehouse_bill
	 * 2/改变货品状态 warehouse_goods
	 * 3/记录单据记录 warehouse_bill_status
	 */
	public function checkBillL_BAK($bill_id){
		$time = date('Y-m-d H:i:s');
		$ip = Util::getClicentIp();
		$pdo = $this->db()->db(); // pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); // 关闭sql语句自动提交
			$pdo->beginTransaction(); // 开启事务

			// 改变单据状态 warehouse_bill
			$sql = "UPDATE `warehouse_bill` SET `bill_status`=2,`check_time`='{$time}',`check_user`='{$_SESSION['userName']}' WHERE `id`={$bill_id}";
			//$pdo->query($sql);
			$changed=$pdo->exec($sql);  
			if($changed<>count(explode(",",$goods_id_str))) {  
				$pdo->rollback();//事务回滚
				$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
				return array('success'=> 0 , 'error'=>'货品状态不是库存中'.$goods_id_str);
			}

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
			$sql = "SELECT bg.goods_name,bg.`goods_id`,`g`.`id`,g.product_type,g.cat_type,bg.sale_price,bg.goods_sn,g.zhengshuhao, g.jietuoxiangkou, g.shoucun , g.caizhi , g.yanse FROM `warehouse_bill_goods` AS `bg` LEFT JOIN `warehouse_goods` AS `g` ON `bg`.`goods_id` = `g`.`goods_id` WHERE `bill_id`={$bill_id}";
			$bill_goods = $this->db()->getAll($sql);

			// 添加货品和仓储关系表 (goods_warehouse ) [获取当前入库仓的默认柜位，把每个货品放置在该默认柜位上]
			foreach($bill_goods as $v){
				$sql = "INSERT INTO `goods_warehouse`(`good_id`, `warehouse_id`, `box_id` , `add_time`,`create_user`) VALUES ( '{$v['goods_id']}' , {$to_warehouse_id} ,  {$default_box_id} , '{$time}','SYSTEM')";
				$pdo->query($sql);

                //将货号写入库龄表，以便记录库龄；
                $sql = "INSERT INTO `warehouse_goods_age` (`warehouse_id`, `goods_id`, `endtime`, `self_age`, `total_age`) VALUES ( {$v['id']} , '{$v['goods_id']}' , '0000-00-00 00:00:00' , '1' , '1')";
                $pdo->query($sql);

				$goods_id_str .= ',\'' . $v ['goods_id'] . '\'';
			}
			$goods_id_str = ltrim($goods_id_str, ',');
			$times = date('Y-m-d H:i:s',time());
			// 改变货品状态 warehouse_goods
			$sql = "UPDATE `warehouse_goods` SET `is_on_sale`=2,`change_time`='{$times}' WHERE `goods_id` IN ({$goods_id_str}) and `is_on_sale`=1";
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
		$pdo->commit(); // 如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交


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

	public function warehouseTree($company_id){
		$sql="SELECT `w`.`id`,`w`.`name` FROM `warehouse_rel` as wr LEFT JOIN `warehouse` as w ON `w`.`id`=`wr`.`warehouse_id` WHERE `w`.`is_delete`=1 AND `wr`.`company_id`=$company_id";
		return $this->db()->getAll($sql);
	}

    public function getCompanyInfo($warehouseid){
        $sql = "SELECT `wr`.`company_name`,`wr`.`company_id`,`w`.`name` FROM `warehouse` as `w` LEFT JOIN `warehouse_rel` as `wr` ON `w`.`id`=`wr`.`warehouse_id` WHERE `w`.`id`=".$warehouseid;
        return $this->db()->getRow($sql);
    }
	public function getBuChanSn($id)
	{
		$sql = "SELECT bg.goods_id,g.zhengshuhao,g.tuo_type,g.cat_type,g.id,g.goods_sn,g.buchan_sn,g.order_goods_id FROM `warehouse_bill_goods` AS `bg` LEFT JOIN `warehouse_goods` AS `g`  ON `bg`.`goods_id` = `g`.`goods_id` where `bg`.`bill_id`={$id} and buchan_sn != ''";
		return $this->db()->getAll($sql);
	}

	//整理收货单数据
	public function arrangementData($grid, $pro_id=0,$put_in_type=0)
	{
		$dataArr = array();
		$chengbenzongjia = 0;
		foreach($grid as $key=>$val){
			if(empty($val['with_fee'])) $val['with_fee']=0;
			//if(empty($val['certificate_fee'])) $val['certificate_fee']=0;
			if(empty($val['operations_fee'])) $val['operations_fee']=0;
			$dataArr[$key]['goods_id'] = $val['goods_id']===''?'0':$val['goods_id'];
			$dataArr[$key]['goods_sn'] = $val['goods_sn'];
			$dataArr[$key]['mo_sn'] = $val['mo_sn'];
            $product_type = mb_substr($val['product_type'], 0, 20);
			$dataArr[$key]['product_type'] = $product_type;
			$dataArr[$key]['product_type1'] = $product_type;
			$dataArr[$key]['cat_type'] = $val['cat_type'];
			$dataArr[$key]['cat_type1'] = $val['cat_type'];
			$dataArr[$key]['is_on_sale'] = 1;
			$dataArr[$key]['att2'] = $product_type;
			$dataArr[$key]['caizhi'] = $val['caizhi'];
			$dataArr[$key]['jinzhong'] = $val['jinzhong'];
			$dataArr[$key]['jinhao'] = $val['jinhao'];
			$dataArr[$key]['zhuchengsezhongjijia'] = ($val['zhuchengsezhongjijia'] != '') ? $val['zhuchengsezhongjijia'] : 0;
			$dataArr[$key]['zhuchengsemairudanjia'] = $val['zhuchengsemairudanjia'];
			$dataArr[$key]['zhuchengsemairuchengben'] = $val['zhuchengsemairuchengben'];
            $dataArr[$key]['luozuanzhengshu'] = $val['luozuanzhengshu'];
            $dataArr[$key]['supplier_code'] = $val['supplier_code'];
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
            $chengbenjia = $val['chengbenjia'];	     
			$dataArr[$key]['yuanshichengbenjia'] = $chengbenjia;		
			//$dataArr[$key]['mingyichengben'] = ($val['xiaoshouchengben'] == '')?$val['chengbenjia']:$val['xiaoshouchengben'];
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
			$dataArr[$key]['gongchangchengben'] = $val['gongchangchengben'];
            $tuo_type = $val['tuo_type'];
			$dataArr[$key]['tuo_type'] = $tuo_type;
			$dataArr[$key]['gemx_zhengshu'] = $val['gemx_zhengshu'];
			$dataArr[$key]['jietuoxiangkou'] = empty($val['jietuoxiangkou'])?0.000:$val['jietuoxiangkou'];
			$dataArr[$key]['zhushitiaoma'] = $val['zhushitiaoma'];
			// $dataArr[$key]['mingyichengben'] = $val['xiaoshouchengben'];	
			$dataArr[$key]['color_grade'] = $val['color_grade'];
			$dataArr[$key]['with_fee'] = $val['with_fee'];
			//$dataArr[$key]['certificate_fee'] = $val['certificate_fee'];
			$dataArr[$key]['operations_fee'] = $val['operations_fee'];
			$dataArr[$key]['peijianjinchong'] = $val['peijianjinchong'];
            $dataArr[$key]['shi2baohao'] = $val['shi2baohao'];
            $dataArr[$key]['shi3'] = $val['shi3'];
            $dataArr[$key]['shi3lishu'] = $val['shi3lishu'];
            $dataArr[$key]['shi3zhong'] = $val['shi3zhong'];
            $dataArr[$key]['shi3zhongjijia'] = $val['shi3zhongjijia'];
            $dataArr[$key]['shi3mairudanjia'] = $val['shi3mairudanjia'];
            $dataArr[$key]['shi3mairuchengben'] = $val['shi3mairuchengben'];
            $dataArr[$key]['shi3jijiadanjia'] = $val['shi3jijiadanjia'];
            $dataArr[$key]['shi3baohao'] = $val['shi3baohao'];
            $dataArr[$key]['guojian_wgt'] = (float)$val['guojian_wgt'];
            //计算名义成本价
            /*如果【销售成本】不为空：名义成本=【销售成本】，
            *如果【销售成本】为空：名义成本=【成本价】+【证书费用】
            *【证书费用】：
            *如果供应商为【GP商贸有限公司】【证书费用】=0；
            *如果供应商非【GP商贸有限公司】
            *如果【证书费用】为空：商品产品线是“镶嵌类”并且金托类型是“成品”的证书费=20，产品线为“素金”证书费=5，其他产品线证书费*为0
            *如果【证书费用】不为空：取收货单里的【证书费用】
            *（区分“0”与“空”，如果是“0”就是0元，如果是“空”按照上面规则取值）*/
            $mingyichengben = $val['xiaoshouchengben'];
            $zhengshu_fee   = $val['certificate_fee'];
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
            if($mingyichengben === '') $mingyichengben = bcadd($chengbenjia, $zhengshu_fee, 3);
            if($val['cat_type']<>'裸石' && $val['cat_type']<>'彩钻'){
                if(bccomp($mingyichengben,bcadd($chengbenjia, $zhengshu_fee,3) ,3)==-1)
                	$mingyichengben=bcadd($chengbenjia, $zhengshu_fee,3);
            }

            $dataArr[$key]['certificate_fee'] = $zhengshu_fee;
            $dataArr[$key]['mingyichengben'] = $mingyichengben;
            
            //计算标签价格 gaopeng
            $label_price = 0;
            if(SYS_SCOPE == 'zhanting' && !in_array($put_in_type,array(5)) && ($val['cat_type']<>'裸石'|| ($val['cat_type']=='裸石' && $val['zhengshuhao']==""))){
                $sql = "select count(*) from front.app_style_jxs where style_sn='{$val['goods_sn']}'";
                $is_jxs_style = $this->db()->getOne($sql)?true:false;
                //$label_jiajialv = $is_jxs_style?1.21:1.17;  
                if($is_jxs_style){
                    $label_jiajialv = 1.21;
                }else{
                    if($product_type=='K金'){
                        $label_jiajialv = 1.1;
                    }else{
                        $label_jiajialv = 1.17;
                    }
                }
                if($val['with_fee']==0)  {
                    if($val['cat_type']=='裸石' && $val['zhengshuhao']!=""){
                        $gendan_fee = 0;
                    }else{
                        $gendan_fee = 20;
                    }
                }else{
                    $gendan_fee = (float)$val['with_fee'];
                }
                if($val['goods_sn'] == "QIBAN"){
                    $qiban_fee = 300;
                }else{
                    $qiban_fee = 0;
                }
                //展厅标签价=（名义成本*1.1X+跟单费+起版费）*4
                $label_price = round(($mingyichengben*$label_jiajialv+$gendan_fee+$qiban_fee)*4,0);
                //$label_price = sprintf("%.2f",$label_price);
            }
            $dataArr[$key]['biaoqianjia'] = $label_price;
			//计算总成本价
			$chengbenzongjia = $chengbenzongjia + $val['chengbenjia'];
		}
		return array('dataArr' => $dataArr,'chengbenzongjia' => $chengbenzongjia);
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



	/**打印详情  根据 $bill_id，获取加工商信息 **/
	public function getBillPay($bill_id){
		$sql = "SELECT `pro_id`,`pro_name`,`pay_content`,`amount`  FROM `warehouse_bill_pay`  WHERE `bill_id` = '{$bill_id}'";
		//echo $sql;exit;
		return $this->db()->getAll($sql);
	}

	/**打印详情  根据货号  $bill_id，获取单据和详情表信息 **/
	public function getBillgoods($bill_id){
		//$sql = "SELECT b.`prc_id`,b.`prc_name`,b.`put_in_type`,b.`send_goods_sn`,a.`to_warehouse_name`,a.`check_user`,a.`bill_no`,a.`create_user`,a.`order_sn` FROM `warehouse_bill` as a ,warehouse_bill_info_l as b WHERE a.`id` = '{$bill_id}' AND a.`id` = b.`bill_id`";
                $sql = "SELECT a.`pro_id`,a.`pro_name`,a.`put_in_type`,a.`send_goods_sn`,a.`to_warehouse_name`,a.`check_user`,a.`bill_no`,a.`create_user`,a.`order_sn` FROM `warehouse_bill` as a  WHERE a.`id` = '{$bill_id}'";
		//echo $sql;exit;
		return $this->db()->getRow($sql);
	}



	/** 取消单据 **/
	public function closeBillInfoL($bill_id,$bill_no){
		$pdo = $this->db()->db();//pdo对象
		try{
			$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
			$pdo->beginTransaction();//开启事务
			//将货品状态还原为库存
			$sql = "update warehouse_goods as g,warehouse_bill_goods as bg set g.is_on_sale = 12 where g.goods_id = bg.goods_id and bg.bill_id = {$bill_id} and g.is_on_sale=1";
			//$pdo->query($sql);
			$changed=$pdo->exec($sql);
			if($changed==0) {  
					$pdo->rollback();//事务回滚
					$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
					//return array('success'=> 0 , 'error'=>'货品状态不是退货中'.$goods_id_str);
					return false;
			}


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
		$pdo->rollback();//事务回滚
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return false;
		}
		$pdo->commit();//如果没有异常，就提交事务
		$pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
		return true;
	}
	
     //获取关联工厂ID
    public function getFactoryIdAll($factory_id,$goods_sn){
    	$sql = "SELECT sf.* FROM  front.rel_style_factory as sf WHERE sf.style_sn = '".$goods_sn."' and sf.factory_id={$factory_id} and sf.is_cancel = 1";
    	$arr= $this->db()->getAll($sql);
    	if(!empty($arr)){
    		return $arr;
    	}else{

    		$sql="SELECT sf.* FROM  front.rel_style_factory as sf WHERE sf.style_sn = '".$goods_sn."' and sf.is_cancel = 1 and sf.factory_id IN (select 473 as supplier_id union SELECT apg1.supplier_id FROM kela_supplier.app_processor_group AS apg1 WHERE  apg1.group_id  =  (SELECT apg.group_id FROM   kela_supplier.app_processor_group AS apg   WHERE apg.supplier_id={$factory_id}))";
    		$arr= $this->db()->getAll($sql);
    		if(!empty($arr)){
    			return $arr;
    		}else{
    			return Array();
    		}
    		
    	}
    	
    	
    }
    
    
    public function getsStyle($goods_sn){
    	$sql="select style_id from front.base_style_info  where style_sn='$goods_sn'";
    	return $this->db()->getOne($sql);
    	
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
   
    public function checkStone($bill_id){
        $sql="select distinct dia_package from (select s.dia_package from warehouse_bill_goods bg,warehouse_goods g,warehouse_bill b,shibao.stone s  where bg.goods_id=g.goods_id and bg.bill_id=b.id and g.zhushibaohao=s.dia_package and b.bill_type='L' and b.bill_status=2 and  g.zhushibaohao is not null and g.zhushibaohao<>'' and b.id='$bill_id' and s.status<>1 union all select distinct s.dia_package from warehouse_bill_goods bg,warehouse_goods g,warehouse_bill b,shibao.stone s  where bg.goods_id=g.goods_id and bg.bill_id=b.id and g.fushibaohao=s.dia_package and b.bill_type='L' and b.bill_status=2 and  g.fushibaohao is not null and g.fushibaohao<>''  and b.id='$bill_id' and s.status<>1 ) as tb";
        return $this->db()->getOne($sql);
    }

    public function getSups($bill_id){
        //$sql="select distinct s.sup_id,s.sup_name from warehouse_bill_goods bg,warehouse_goods g,warehouse_bill b,shibao.stone s  where bg.goods_id=g.goods_id and bg.bill_id=b.id and g.zhushibaohao=s.dia_package and b.bill_type='L' and b.bill_status=2 and  g.zhushibaohao is not null and g.zhushibaohao<>'' and b.id='$bill_id'";
        $sql="select distinct sup_id,sup_name from (select distinct s.sup_id,s.sup_name from warehouse_bill_goods bg,warehouse_goods g,warehouse_bill b,shibao.stone s  where bg.goods_id=g.goods_id and bg.bill_id=b.id and g.zhushibaohao=s.dia_package and b.bill_type='L' and b.bill_status<>3 and  g.zhushibaohao is not null and g.zhushibaohao<>'' and b.id='$bill_id' and s.status='1' union all select distinct s.sup_id,s.sup_name from warehouse_bill_goods bg,warehouse_goods g,warehouse_bill b,shibao.stone s  where bg.goods_id=g.goods_id and bg.bill_id=b.id and g.fushibaohao=s.dia_package and b.bill_type='L' and b.bill_status<>3 and  g.fushibaohao is not null and g.fushibaohao<>''  and b.id='$bill_id' and s.status='1' union all select distinct s.sup_id,s.sup_name from warehouse_bill_goods bg,warehouse_goods g,warehouse_bill b,shibao.stone s  where bg.goods_id=g.goods_id and bg.bill_id=b.id and g.shi2baohao=s.dia_package and b.bill_type='L' and b.bill_status<>3 and  g.shi2baohao is not null and g.shi2baohao<>''  and b.id='$bill_id' and s.status='1' union all select distinct s.sup_id,s.sup_name from warehouse_bill_goods bg,warehouse_goods g,warehouse_bill b,shibao.stone s  where bg.goods_id=g.goods_id and bg.bill_id=b.id and g.shi3baohao=s.dia_package and b.bill_type='L' and b.bill_status<>3 and  g.shi3baohao is not null and g.shi3baohao<>''  and b.id='$bill_id' and s.status='1' ) as tb";
        return $this->db()->getAll($sql);
    }
    public function getZhushibaos($bill_id,$sup_id){
        //$sql="select g.zhushibaohao,sum(g.zhushilishu)+sum(g.fushilishu)+sum(g.shi2lishu) as num,sum(g.zuanshidaxiao)+sum(g.fushizhong)+sum(g.shi2zhong) as weight,s.*  from warehouse_bill_goods bg,warehouse_goods g,warehouse_bill b,shibao.stone s  where bg.goods_id=g.goods_id and bg.bill_id=b.id and g.zhushibaohao=s.dia_package and b.bill_type='L' and b.bill_status=2 and  g.zhushibaohao is not null and g.zhushibaohao<>'' and b.id='$bill_id' and s.sup_id='$sup_id'";
        $sql="select sum(num) as num,sum(weight) as weight,dia_package,purchase_price,specification,color,neatness,cut,symmetry,polishing,fluorescence from (select g.zhushibaohao,sum(g.zhushilishu) as num,sum(g.zuanshidaxiao) as weight,s.*  from warehouse_bill_goods bg,warehouse_goods g,warehouse_bill b,shibao.stone s  where bg.goods_id=g.goods_id and bg.bill_id=b.id and g.zhushibaohao=s.dia_package and b.bill_type='L' and b.bill_status<>3 and  g.zhushibaohao is not null and g.zhushibaohao<>''  and s.status='1' and b.id='$bill_id' and s.sup_id='$sup_id' group by s.dia_package
              union all  select g.fushibaohao,sum(g.fushilishu) as num,sum(g.fushizhong) as weight,s.* from warehouse_bill_goods bg,warehouse_goods g,warehouse_bill b,shibao.stone s  where bg.goods_id=g.goods_id and bg.bill_id=b.id and g.fushibaohao=s.dia_package and b.bill_type='L' and b.bill_status<>3 and  g.fushibaohao is not null and g.fushibaohao<>''  and s.status='1' and b.id='$bill_id' and s.sup_id='$sup_id' group by s.dia_package union all  select g.shi2baohao,sum(g.shi2lishu) as num,sum(g.shi2zhong) as weight,s.* from warehouse_bill_goods bg,warehouse_goods g,warehouse_bill b,shibao.stone s  where bg.goods_id=g.goods_id and bg.bill_id=b.id and g.shi2baohao=s.dia_package and b.bill_type='L' and b.bill_status<>3 and  g.shi2baohao is not null and g.shi2baohao<>''  and s.status='1' and b.id='$bill_id' and s.sup_id='$sup_id' group by s.dia_package union all  select g.shi3baohao,sum(g.shi3lishu) as num,sum(g.shi3zhong) as weight,s.* from warehouse_bill_goods bg,warehouse_goods g,warehouse_bill b,shibao.stone s  where bg.goods_id=g.goods_id and bg.bill_id=b.id and g.shi3baohao=s.dia_package and b.bill_type='L' and b.bill_status<>3 and  g.shi3baohao is not null and g.shi3baohao<>''  and s.status='1' and b.id='$bill_id' and s.sup_id='$sup_id' group by s.dia_package ) as tb group by dia_package"; 
        return $this->db()->getAll($sql);
    }
    /**
     * 获取单据明细 列表（）
     * @param unknown $bill_id
     */
    public function getBillGoodsGridByBillId($bill_id)
    {
        $sql = "SELECT `g`.*,g.mingyichengben as xiaoshouchengben,g.buchan_sn as buchanhao FROM `warehouse_bill_goods` AS `wg`  LEFT JOIN  `warehouse_goods` AS `g`  ON `wg`.`goods_id`=`g`.`goods_id`  WHERE `bill_id` = $bill_id ORDER BY `g`.`id` ASC" ;
        return $this->db()->getAll($sql);
    }

}

