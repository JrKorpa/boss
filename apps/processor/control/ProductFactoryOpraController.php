<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductFactoryOpraController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 10:04:17
 *   @update	:
 *		布产单 工厂操作
 *  -------------------------------------------------
 */
class ProductFactoryOpraController extends Controller
{
	protected $smartyDebugEnabled = true;

	//获取所有工厂操作列表
	public function getActionFactory(){
		$FactoryOpraModel = new FactoryOpraDictModel(13);
		$factory_config = $FactoryOpraModel->GetInfo($fields = '`name` , `dict_value`' , $where = '`status` = 1 ', $type = 'all');
		return $factory_config;
	}
	//根据工厂操作id,获取工厂操作名
	public function getActionName($action_id){
		$arr = $this->getActionFactory();
		foreach ($arr as $key => $value) {
			if($value['dict_value'] == $action_id){
				return $value['name'];
			}
		}
	}
	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		//Util::M('product_factory_opra','front',13);	//生成模型后请注释该行
		//Util::V('product_factory_opra',13);	//生成视图后请注释该行
		//$this->render('product_factory_opra_search_form.html');
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ($params)
	{
		$id = intval($params["id"]);//布产单ID
		$c = _Request::get("c");

		$result = array('success' => 0,'error' => '','title' => '工厂操作');
		$model = new ProductInfoModel($id,14);
		$status = $model->getValue('status');
		if(!in_array($status,array(4,5,6,7)))		//布产单只有生产中状态下才能进行工厂操作
		{
			$result['content'] = "布产单状态不对，不允许操作。";
			Util::jsonExit($result);
		}

		$factory_config=$this->getActionFactory();
		if(in_array($status,array(5,6,7))){
            $factory_config2=array(); 
            foreach ($factory_config as $key => $item) {
            	if($item['name']=='部分回货' || $item['name']=='已回货')
             	    $factory_config2[]= $item;
            }
            $factory_config=$factory_config2;
		}

		$result['content'] = $this->fetch('product_factory_opra_info.html',array(
			'id' => $id,
			'dd'=>new DictView(new DictModel(1)),
			'factory_config' => $factory_config,		//获取工厂操作
			'factory_opra_status' => $this->getActionName( $model->getValue('factory_opra_status') ),
			'factory_opra_status_id' => $model->getValue('factory_opra_status'),
		));

		Util::jsonExit($result);
	}

	/**************************************************************************************************
	fun:add_pl
	description:批量添加工厂操作
	***************************************************************************************************/
	public function add_pl($params)
	{
		$ids = $params['_ids'];
		$result = array('success' => 0,'error' => '','title' => '工厂操作');
		$model = new ProductInfoModel(13);
		#循环判断只有生产中的才能进行工厂操作
		$is_ok = $model->IsStatusStart($ids);
		if($is_ok == false)//布产单只有生产中状态下才能进行工厂操作
		{
		    $result['content'] = "布产单状态不对，不允许操作。";
			Util::jsonExit($result);
		}
		$id_s  = join(",",$ids);
		//获取工厂操作列表
		$caozuoModel = new FactoryOpraDictModel(13);
		$act_arr = $caozuoModel->GetInfo($fields = '`name`,`dict_value`' , $where = '`status` = 1 ', $type = 'all');
		$this->render('product_factory_opra_info_pl.html',array(
			'dd'=>new DictView(new DictModel(1)),
			'ids' => $id_s,
			'act_arr'=>$act_arr,
		));

	}
	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);//布产ID
		$model = new ProductFactoryOpraModel(13);
		$list = $model->pageList(array('bc_id'=>$id));

		$result['title'] = "工厂操作流水";
		$result['content'] = $this->fetch('product_factory_opra_show.html',array(
			'dd'=>new DictView(new DictModel(1)),
			'list'=> $list
		));
		Util::jsonExit($result);
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' => '');
		$model_fac = new ProductFactoryOpraModel(14);
		$salesmodel = new SalesModel(27);
		$WarehouseModel = new WarehouseModel(21);
		$model_pw = new AppProcessorWorktimeModel(13);
		$attrmodel = new ProductInfoAttrModel(13);
		$diamodel = new DiamondModel(20);
		$ids = $params["id"];//布产单ID
		$ids = explode(',', $ids);

		foreach($ids as $key =>$id)
		{
			//判断布产单的状态
			$model = new ProductInfoModel($id,13);
			
			$status = $model->getValue('status');
			$mark = 0;
			$time = $model->getValue('esmt_time');
			//等钻操作
			if($params['fac_opra'] == 11){
				$order_sn = $model->getValue('p_sn');
				$style_sn = $model->getValue('style_sn');
				//更新钻石类型
				$goodsinfos = $salesmodel->getStockGoodsByOrderSn($order_sn,$style_sn);
				$cert_id2 = preg_replace('/[a-zA-Z]{0,10}/', '', $goodsinfos['zhengshuhao']);
                $goods_type = $diamodel->getGoodsTypeByCertId($goodsinfos['zhengshuhao'],$cert_id2);
                if($goods_type ==2){
                            //期货钻
                       	$diamond_type = 2;
	                }else{
	                    //现货钻
                     	$diamond_type = 1;
	               }

	             /* // 逻辑修改，先注释
                $is_exists = $WarehouseModel->isExistsByGoodsId($goodsinfos['goods_id']);
                if(empty($goodsinfos['goods_id']) && empty($goodsinfos['zhengshuhao'])){
                    //货号和证书号都为空，就是现货
                    $diamond_type = 1;
                }else{
                    if($is_exists){
                    //现货钻
                       $diamond_type = 1;
                    }else{
                        //货号没找到，通过证书号去裸钻列表查找判断是期货还是现货
                        $cert_id2 = preg_replace('/[a-zA-Z]{0,10}/', '', $goodsinfos['zhengshuhao']);
                        $goods_type = $diamodel->getGoodsTypeByCertId($goodsinfos['zhengshuhao'],$cert_id2);
                        if($goods_type ==2){
                            //期货钻
                             $diamond_type = 2;
                        }else{
                            //现货钻
                             $diamond_type = 1;
                           }
                        }
                    }			*/

                    $old_diamond_type = $model->getValue('diamond_type');
                    if($diamond_type !=$old_diamond_type){
                    	$res =$model->updateDiamondTypeById($id,$diamond_type);
	                    if($res===false){
	                    	$result['error'] = "钻石类型更新失败!";
							Util::jsonExit($result);
	                    }
                    }
               
				//镶嵌方式”为“工厂配钻工厂镶嵌、需工厂镶嵌、客户先看钻再返厂镶嵌、成品才允许等钻
				$xiangqian = $model->getXiangqianById($id);
				$xiangqian_valid_arr = array('工厂配钻，工厂镶嵌','需工厂镶嵌','客户先看钻再返厂镶嵌','成品','镶嵌4C裸钻','镶嵌4C裸钻，客户先看钻');
				if(in_array($xiangqian,$xiangqian_valid_arr)){
				    					
				}else{
					$result['error'] = "镶嵌方式为【工厂配钻，工厂镶嵌】、【需工厂镶嵌】、【客户先看钻再返厂镶嵌】、【成品】、【镶嵌4C裸钻】、【镶嵌4C裸钻，客户先看钻】才允许等钻操作!";
					Util::jsonExit($result);
				}
				/* //*2016-06-30开始
				//根据货号进行更新，未出厂,出厂时间大于当前时间 订单 from_type
				$pass_status =array(1,2,3,4,5,6);
				$now =date('Y-m-d',time());
				// && $time >= $now
				//if(in_array($status, $pass_status) && $time >= $now){
				if(in_array($status, $pass_status)){
						$pro_id = $model->getValue('prc_id');
						$order_sn = $model->getValue('p_sn');
						$style_sn = $model->getValue('style_sn');
						$from_type = $model->getValue('from_type');
						$order_type=($from_type==1)?2:1;	//数据表存储不一样
						$goodsinfos = $salesmodel->getStockGoodsByOrderSn($order_sn,$style_sn);
							//通过货号在商品列表中找到即为现货
							$is_exists = $WarehouseModel->isExistsByGoodsId($goodsinfos['goods_id']);
							$cert_id =$attrmodel->getCertNumById($id);
							if(empty($goodsinfos['goods_id']) && empty($cert_id)){
								//货号和证书号都为空，就是现货
								$order_time = $model->getValue('order_time');
								$infos = $model_pw->getProcessorInfoByTypeAndId($pro_id,$order_type);
									//获得现货等钻加时、假期、周末上班天数、周末休息时间
								$cycle = intval($infos['now_wait_dia']);

							}else{
								if($is_exists){
								//现货：现货等钻加时更新出厂时间
									$infos = $model_pw->getProcessorInfoByTypeAndId($pro_id,$order_type);
									//获得现货等钻加时、假期、周末上班天数、周末休息时间
									$cycle = intval($infos['now_wait_dia']);

								}else{
									//货号没找到，通过证书号去裸钻列表查找判断是期货还是现货
									$cert_id2 = preg_replace('/[a-zA-Z]{0,10}/', '', $cert_id);
									$goods_type = $diamodel->getGoodsTypeByCertId($cert_id,$cert_id2);
									if($goods_type==2){
										//期货
										$pro_id = $model->getValue('prc_id');
										$infos = $model_pw->getProcessorInfoByTypeAndId($pro_id,$order_type);
										//获得现货等钻加时、假期、周末上班天数、周末休息时间
										$cycle = intval($infos['wait_dia']);
									}else{
										//现货
										$pro_id = $model->getValue('prc_id');
										$infos = $model_pw->getProcessorInfoByTypeAndId($pro_id,$order_type);
										//获得现货等钻加时、假期、周末上班天数、周末休息时间
										$cycle = intval($infos['now_wait_dia']);
									   }
									}
								}
								//遇到放假出厂时间往后延(周末时间做不到,默认单休为周六)
								if(!empty($cycle)){
									for($i=1;$i<=$cycle;$i++){
										$day = date('Y-m-d',strtotime('+'.$i.' day',time()));
											//放假休息
												if(strpos($infos['holiday_time'],$day) !== false){
														//有假期就延后一天
														++$cycle;
														continue;

												}
												//暂时只能获得周末休息天数
												switch ($infos['is_rest']) {
													case '1':
														break;
													case '2':
														//有周末就后延后一天
														if(date('w',strtotime($day))== 0){
							                                    $cycle = $cycle+1;
							                                }
														break;
													default:
														if(date('w',strtotime($day))== 6 || date('w',strtotime($day))== 0){
							                                    $cycle = $cycle+1;
							                                }
														break;
												}											
											//周末上班
											if(strpos($infos['is_work'],$day) !== false && strpos('60',date('w',strtotime($day))) !== false){
													--$cycle;
											}
									
									 	}
									 	if(strtotime(date('Y-m-d',strtotime($day))) > strtotime($time)){
											$res = $model->updateEsmttime($id,$day,1);
											if($res ===false){
													$result['error'] = "布产单出厂时间更新失败!";
													Util::jsonExit($result);
											}
									 	}
										//更新预计等钻完成时间
										$wait_dia_finishtime = date('Y-m-d H:i:s',strtotime($day));
										$res3 = $model->updateWait_dia_finishtimeById($id,$day);	//预计等钻完成时间	
										if($res3 ===false){
											$result['error'] = "布产单预计等钻完成时间更新失败!";
											Util::jsonExit($result);
										}

									}
								
								$wait_dia_starttime = date('Y-m-d H:i:s',time());
								$res2 = $model->updateWait_dia_starttimeById($id,$wait_dia_starttime);	//等钻开始时间
								if($res2 ===false){
									$result['error'] = "布产单等钻开始时间更新失败!";
									Util::jsonExit($result);
								}

						
						}else{
							//钻石类型变化，等钻超期需要更新等钻开始时间
							$origin_dia_type = $model->getValue('origin_dia_type');
							if($origin_dia_type != $diamond_type){
								$wait_dia_starttime = date('Y-m-d H:i:s',time());
								$res2 = $model->updateWait_dia_starttimeById($id,$wait_dia_starttime);	//等钻开始时间
								if($res2 ===false){
									$result['error'] = "布产单等钻开始时间更新失败!";
									Util::jsonExit($result);
								}
							}
						}
						*/ ////*2016-06-30结束
					}else{
                       /*2016-06-30开始
						//等钻操作后，操作非等钻后等钻完成时间和开始生产的出厂时间比对，大于更新出厂时间
						$wait_dia_starttime = $model->getValue('wait_dia_starttime');
						$wait_dia_endtime = $model->getValue('wait_dia_endtime');
						$esmt_time = $model->getValue('esmt_time');
						$now = date('Y-m-d',time());

						
                        //if($wait_dia_endtime =='0000-00-00 00:00:00' && $wait_dia_starttime !='0000-00-00 00:00:00' && $now <= $esmt_time){
						if($wait_dia_endtime =='0000-00-00 00:00:00' && $wait_dia_starttime !='0000-00-00 00:00:00'){
							$from_type = $model->getValue('from_type');
							$order_type =$from_type ==1?2:1;
							$res2 = $this->updateEsmttimeById($id,$order_type);
							if($res2 === false){
								$result['error'] = "更新标准出厂时间失败!";
								Util::jsonExit($result);
							}

						}

						//工厂操作(非等钻)更新实际等钻结束时间
						if($wait_dia_endtime =='0000-00-00 00:00:00' && $wait_dia_starttime !='0000-00-00 00:00:00'){
							$wait_dia_endtime = date('Y-m-d H:i:s',time());
							$res1 = $model->updateWait_dia_endtimeById($id,$wait_dia_endtime);
							if($res === false){
								$result['error'] = "更新实际等钻结束时间失败!";
								Util::jsonExit($result);
							}

						}
						*/ //*2016-06-30结束

				}
				
				
			if(isset($params['is_songzuan']) && $params['is_songzuan'] == true){		//布产单的 送钻 可以在分配跟单人之后进行操作,不用等到生产中的状态~~~ BY caocao
				//等钻操作后，操作送钻，等钻完成时间和开始生产的出厂时间比对，大于更新出厂时间
						/*
				        $wait_dia_starttime = $model->getValue('wait_dia_starttime');
						$wait_dia_endtime = $model->getValue('wait_dia_endtime');
						$esmt_time = $model->getValue('esmt_time');
						$now = date('Y-m-d',time());
                        if($wait_dia_endtime =='0000-00-00 00:00:00' && $wait_dia_starttime !='0000-00-00 00:00:00'){
						//if($wait_dia_endtime =='0000-00-00 00:00:00' && $wait_dia_starttime !='0000-00-00 00:00:00' && $now <= $esmt_time){
							$from_type = $model->getValue('from_type');
							$order_type =$from_type ==1?2:1;
							$res2 = $this->updateEsmttimeById($id,$order_type);
							if($res2 === false){
								$result['error'] = "更新标准出厂时间失败!";
								Util::jsonExit($result);
							}
						}*/

				if($status < 3)		//送钻前，检测是否分配了工厂 跟单人
				{
					#只要布产的单据都能送钻（未分配工厂、生产之前） lyh
					//$result['error'] = "布产单:<span style='color:red'>{$model->getValue('bc_sn')}</span>还没有分配工厂，不允许操作。";
					//Util::jsonExit($result);
				}
			}else{
				if(!in_array($status,array(4,5,6,7)))//布产单只有生产中状态下才能进行工厂操作
				{
					$result['error'] = "布产单:<span style='color:red'>{$model->getValue('bc_sn')}</span>状态不对，不允许操作。";
					Util::jsonExit($result);
				}
			}

			//检测是否选择了工厂操作
			$fac_opra = $params['fac_opra'];
			if($fac_opra == "")
			{
				$result['error'] = "请选择一项操作";
				Util::jsonExit($result);
			}

			//如果工厂操作倒回去操作，备注是必填项
			$opra_info = $params['opra_info'];
			/*if(($fac_opra < $model->getValue('buchan_fac_opra')) && $opra_info == '')
			{
				$result['error'] = "请填写备注";
				Util::jsonExit($result);
			}*/
			if($this->checkSort( $fac_opra , $model->getValue('factory_opra_status'))  && trim($opra_info) == '')
			{
				$result['error'] = "请填写备注";
				Util::jsonExit($result);
			}
		}

		$res = $model_fac->addinsert($ids , $params['fac_opra'] , $params['opra_info']);
		if($res['success'] == 1){
			$result['success'] = 1;
			$result['error'] = $res['error'];
		}else{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}

	/**
	* 检测工厂操作的排序问题
	* @param $fac_opra 提交过来的工厂操作
	* @param $buchan_fac_opra 布产单现在的工厂操作状态
	* @param $opra_info 当前提交的操作备注
	*/
	public function checkSort( $fac_opra , $buchan_fac_opra){
		$model = new FactoryOpraDictModel(13);
		$res = $model->checkSort($fac_opra , $buchan_fac_opra);
		if($res){
			return true;
		}else{
			return false;
		}
	}

	/**
	 *	insert_pl，工厂批量操作保存记录
	 */
	public function insert_pl ($params)
	{
		$id_s = $params["ids"];//布产单ID
		$result = array('success' => 0,'error' => '');

		$model = new ProductInfoModel(14);
		$ids = explode(',',$id_s);
		$is_ok = $model->IsStatusStart($ids);
		if($is_ok == false)//布产单只有生产中状态下才能进行工厂操作
		{
			$result['content'] = "布产单状态不对，不允许操作。";
			Util::jsonExit($result);
		}

		if (!isset( $params['fac_opra']))
		{
			$result['content'] = "请选择一项操作";
			Util::jsonExit($result);
		}
		$fac_opra = $params['fac_opra'];
		$opra_info = $params['opra_info'];

		$model_fac = new ProductFactoryOpraModel(14);
		$res = $model_fac->addinsert($ids , $params['fac_opra'] , $params['opra_info']);
		if($res['success'] == 1){
			$result['success'] = 1;
			$result['error'] = $res['error'];
		}else{
			$result['error'] = $res['error'];
		}
		Util::jsonExit($result);
	}


	/**
	* 送钻操作
	*/
	public function songZuan($params){
		//获取 等钻 数字字典的key枚举值
		$dd = new DictModel(1);
		$arr = $dd->getEnumArray('buchan_fac_opra');
		foreach($arr as $val){
			if($val['label'] === '送钻'){
				$params['fac_opra'] = $val['name'];
				break;
			}
		}
		$params['opra_info'] = '送钻操作';
		$params['act'] = 'insert';

		//送钻必须开始生产的限制去掉 2015/4/28 Bycaocao
		$params['is_songzuan'] = true;

		//判断当前操作时单个布产送钻 还是 批量送钻

		if(is_array($params['id'])){
			$params['id'] = implode(',', $params['id']);
			$this->insert($params);//批量送钻
		}else{
			$this->insert($params);		//单个布产送钻
		}
	}

	/*
	*更加供应商ID更新出厂时间(送钻时间比较)
	*
	*/
	public function updateEsmttimeById($id,$order_type=1){

		$newmodel = new AppProcessorWorktimeModel(14);
		$productModel = new ProductInfoModel($id,14);
		$stylemodel = new StyleModel(11);
		$purchasemodel = new PurchaseModel(23);
		$proInfos = $productModel->getBuChanInfoById($id);
		$infos = $newmodel->getProcessorInfoByProId($proInfos['prc_id'],$order_type);
		$behind_wait_dia = !empty($infos['behind_wait_dia'])?$infos['behind_wait_dia']:0;
			//更新出厂时间:未出厂 && 出厂时间大于当前时间
				$qiban_exists = $purchasemodel->getQiBanInfosByStyle_Sn($proInfos['style_sn'],$proInfos['p_sn']);
				if($order_type ==1){
					//客订单
					/*
					if($proInfos['style_sn'] =='QIBAN' && $qiban_exists){
					//无款起版
						$cycle = $infos['wkqbzq'];
					
					}else{
						//成品:款式库存在,起版列表没有
						if($proInfos['style_sn'] !='QIBAN'){
							//起版列表信息
							$qiban_exists = $purchasemodel->getQiBanInfosByStyle_Sn($proInfos['style_sn'],$proInfos['p_sn']);
							if(empty($qiban_exists)){
								//成品(更新)
								$cycle = $infos['normal_day'];
							}else{
								//有款起版(更新)
								$cycle = $infos['ykqbzq'];
							}
						}	
					}
					*/
					if($proInfos['qiban_type']==0){
						$cycle = $infos['wkqbzq'];
					}elseif($proInfos['qiban_type']==1){
						$cycle = $infos['ykqbzq'];
					}else{
						$cycle = $infos['normal_day'];
					}
				}else{
					//备货单
					$is_style = $purchasemodel->getStyleInfoByCgd($proInfos['p_sn']);
					if($is_style ==1){
						//采购列表  --有款采购
						$cycle = $infos['ykqbzq'];
					}elseif($is_style ==0){
						//采购列表  --无款采购
						$cycle = $infos['wkqbzq'];
					}else{
						//采购列表  --标准采购
						$cycle = $infos['normal_day'];
					}
				}


					if(!empty($cycle)){
						$order_time = strtotime($proInfos['order_time']);
						for($i=1;$i<=$cycle;$i++){
							$day = date('Y-m-d',strtotime('+'.$i.' day',$order_time));
								//放假日期
							if(strpos($infos['holiday_time'],$day) !== false){
									++$cycle;
									continue;
								}
								//暂时只能获得周末休息天数(默认周天休息)
								switch ($infos['is_rest']) {
									case '1':
										break;
									case '2':
										if(date('w',strtotime($day))== 0){
											$cycle = $cycle+1;
										}
										break;
									default:
										if(date('w',strtotime($day))== 6 || date('w',strtotime($day))== 0){
											$cycle = $cycle+1;
										}
										break;
								}											
							//周末上班
							if(strpos($infos['is_work'],$day) !== false && strpos('60',date('w',strtotime($day))) !== false){
									--$cycle;				
								}
							}
						}
						
						if(!empty($behind_wait_dia)){
						for($i=1;$i<=$behind_wait_dia;$i++){
							$new_day = date('Y-m-d',strtotime('+'.$i.' day',time()));
								//放假日期
							if(strpos($infos['holiday_time'],$new_day) !== false){
									++$behind_wait_dia;
									continue;
								}
								//暂时只能获得周末休息天数(默认周天休息)
								switch ($infos['is_rest']) {
									case '1':
										break;
									case '2':
										if(date('w',strtotime($new_day))== 0){
											$behind_wait_dia = $behind_wait_dia+1;
										}
										break;
									default:
										if(date('w',strtotime($new_day))== 6 || date('w',strtotime($new_day))== 0){
											$behind_wait_dia = $behind_wait_dia+1;
										}
										break;
								}											
							//周末上班
							if(strpos($infos['is_work'],$new_day) !== false && strpos('60',date('w',strtotime($new_day))) !== false){
									--$behind_wait_dia;				
								}
							}
						}
						$now = date('Y-m-d',time());
                        if(isset($day)){
						//if(isset($day) && $proInfos['esmt_time'] >=$now){
							$esmt_time = max($new_day,$day);
							$res = $productModel->updateEsmttime($id,$esmt_time,1);
							if($res===false){
								return false;
							}
						}
						
						return true;
		}
}?>