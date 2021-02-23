<?php
/**
 *  -------------------------------------------------
 *   @file		: AppProcessorWorktimeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-01 10:14:48
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorWorktimeController extends CommonController
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		die('我是明细，没有菜单列表');
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
			'_id' => _Request::get("_id"),
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
		$where = array();
		$where['_id'] = $args['_id'];

		$model = new AppProcessorWorktimeModel(13);
		$data = $model->pageList($where,$page,10,false);
        if($data['data']){
            $view = new AppProcessorInfoView(new AppProcessorInfoModel($where['_id'],13));
            $name = $view->get_name();
            foreach ($data['data'] as &$val){
                $val['processor_id'] = $name;
            }
            unset($val);
        }
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'app_processor_worktime_search_page';
		$this->render('app_processor_worktime_search_list.html',array(
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
        $model = new AppProcessorWorktimeModel(13);
        // $is_have = $model->isHave(_Post::getInt('_id'));
        $order = $model->isExists(_Post::getInt('_id'));

        if($order>2){
            $result['content'] = "一个供应商只能有一条客订单和一条备货单加工时间";
        }else{
            $result['content'] = $this->fetch('app_processor_worktime_info.html',array(
                'view'=>new AppProcessorWorktimeView($model),
                '_id'=>_Post::getInt('_id')
            ));
        }
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
		$v =new AppProcessorWorktimeView(new AppProcessorWorktimeModel($id,13));
		$result['content'] = $this->fetch('app_processor_worktime_info.html',array(
			'view'=>$v,
			'tab_id'=>$tab_id,
			'_id'=>$v->get_processor_id()
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		die('开发中');
		$id = intval($params["id"]);
		$this->render('app_processor_worktime_show.html',array(
			'view'=>new AppProcessorWorktimeView(new AppProcessorWorktimeModel($id,13)),
			'bar'=>Auth::getViewBar()
		));
	}

	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
		$result = array('success' => 0,'error' =>'');
		$_id = _Post::getInt('_id');//主表主键
        $normal_day = _Post::getInt('normal_day');
        $wait_dia = _Post::getInt('wait_dia');
        $behind_wait_dia = _Post::getInt('behind_wait_dia');
        $ykqbzq = _Post::getInt('ykqbzq');
        $is_rest = _Post::getInt('is_rest');
        $order_problem = _Post::getInt('order_problem');
        $order_type = _Post::getInt('order_type');
        $is_work = _Post::getString('is_works');
        $now_wait_dia = _Post::getInt('now_wait_dia');
        $wkqbzq = _Post::getInt('wkqbzq');
        $holiday_time = _Post::getString('holiday_times');

        $newmodel =  new AppProcessorWorktimeModel(14);
        $order1 = $newmodel->isExistsOrder1($_id);
        $order2 = $newmodel->isExistsOrder2($_id);
        if($order_type ==1 && $order1 >0){
        	$result['error'] = "一个供应商只能有一条客订单加工时间";
        	Util::jsonExit($result);
        }

        if($order_type ==2 && $order2 >0){
        	$result['error'] = "一个供应商只能有一条备货单加工时间";
            Util::jsonExit($result);
        }

        if(!($normal_day>0 && $wait_dia>0 && $behind_wait_dia>0 && $ykqbzq>0 && $order_problem>=0 && $is_rest>0)){
            $result['error'] = '订单问题加时必须大于等于0，其他天数必须大于0。';
            Util::jsonExit($result);
        }
        if($normal_day > 127 || $wait_dia > 127 || $behind_wait_dia > 127 || $ykqbzq > 127 || $order_problem > 127 || $is_rest > 9){
            $result['error'] = '提交的数据中有的数值过大';
            Util::jsonExit($result);
        }
		$olddo = array();
		$newdo=array(
			'processor_id'=>$_id,
			'normal_day'=>$normal_day,
			'wait_dia'=>$wait_dia,
			'behind_wait_dia'=>$behind_wait_dia,
			'ykqbzq'=>$ykqbzq,
			'is_rest'=>$is_rest,
			'order_problem'=>$order_problem,
			'order_type'=>$order_type,
			'is_work'=>$is_work,
          	'now_wait_dia'=>$now_wait_dia,
           	'wkqbzq'=>$wkqbzq,
    		'holiday_time'=>$holiday_time
		);
		
		$stylemodel = new StyleModel(11);
		$purchasemodel = new PurchaseModel(23);

		$pdo14 = $newmodel->db()->db();
		$pdo11 = $stylemodel->db()->db();
		$pdo23 = $purchasemodel->db()->db();


		$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);	//关闭自动提交
		$pdo14->beginTransaction();

		$pdo11->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);	//关闭自动提交
		$pdo11->beginTransaction();

		$pdo23->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);	//关闭自动提交
		$pdo23->beginTransaction();

		try{
			if($order_type == 'all'){
				//如果选择删除之前添加的
				$res1 = $newmodel->delAllInfoById($_id);
				if($res1 ==false){
						throw new Exception("删除失败!");
				}
				for($i=1;$i<3;$i++){
					$newdo['order_type'] =$i;
					$res = $newmodel->saveData($newdo,$olddo);
					if($res ==false){
						throw new Exception("修改失败");
					}
					$res1 = $this->updateEsmttimeByOrigin($_id,$i,$newdo);
					if($res1 ==false){
						throw new Exception("出厂时间更新失败!");
					}
				}
			}else{
				$res = $newmodel->saveData($newdo,$olddo);
				if($res ==false){
					throw new Exception("修改失败");
				}

				$res1 = $this->updateEsmttimeByOrigin($_id,$order_type,$newdo);
				if($res1 ==false){
					throw new Exception("出厂时间更新失败!");
				}
			}

		}catch(Exception $e){
			$pdo14->rollback();
			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
            $result['error'] = $e->getMessage();
            Util::jsonExit($result);
		}
		
		 $pdo14->commit(); //事务提交
         $pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
     	$result['success'] = 1;
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

		$id = _Post::getInt('pw_id');
		$prc_id = _Post::getInt('_id');
        $normal_day = _Post::getInt('normal_day');
        $wait_dia = _Post::getInt('wait_dia');
        $behind_wait_dia = _Post::getInt('behind_wait_dia');
        $ykqbzq = _Post::getInt('ykqbzq');
        $is_rest = _Post::getInt('is_rest');
        $order_problem = _Post::getInt('order_problem');
        $order_type = _Post::getInt('order_type');
        $is_work = _Post::getString('is_works');
        $now_wait_dia = _Post::getInt('now_wait_dia');
        $wkqbzq = _Post::getInt('wkqbzq');
        $holiday_time = _Post::getString('holiday_times');

		if(!($normal_day>0 && $wait_dia>0 && $behind_wait_dia>0 && $ykqbzq>0 && $order_problem>=0 && $is_rest>0)){
            $result['error'] = '订单问题加时必须大于等于0，其他天数必须大于0。';
            Util::jsonExit($result);
        }
        if($normal_day > 127 || $wait_dia > 127 || $behind_wait_dia > 127 || $ykqbzq > 127 || $order_problem > 127 || $is_rest > 9){
            $result['error'] = '提交的数据中有的数值过大';
            Util::jsonExit($result);
        }

        if($order_type =='all'){
			$result['error'] = "一个供应商不允许同时编辑两条记录!";
        	Util::jsonExit($result);
		}
		$newmodel =  new AppProcessorWorktimeModel($id,14);
		$is_exists = $newmodel->getProcessorInfoByTypeAndId($prc_id,$order_type);
		$old_order_type = $newmodel->getValue('order_type');
		if(!empty($is_exists['pw_id']) && $order_type !=$old_order_type){
			$result['error'] = "一个供应商只能有一条客订单和一条备货单加工时间";
        	Util::jsonExit($result);
		}

		$olddo = $newmodel->getDataObject();
		$newdo=array(
            'pw_id'=>$id,
            'normal_day'=>$normal_day,
            'wait_dia'=>$wait_dia,
            'behind_wait_dia'=>$behind_wait_dia,
            'ykqbzq'=>$ykqbzq,
            'is_rest'=>$is_rest,
            'order_problem'=>$order_problem,
            'order_type'=>$order_type,
         	'is_work'=>$is_work,
          	'now_wait_dia'=>$now_wait_dia,
           	'wkqbzq'=>$wkqbzq,
    		'holiday_time'=>$holiday_time,
		);

		//更新标准出厂时间=标准出货时间 + 放假日期 - 周末上班；
		$productModel = new ProductInfoModel(14);
		$stylemodel = new StyleModel(11);
		$purchasemodel = new PurchaseModel(23);
		//开启事务`

		$pdo14 = $newmodel->db()->db();
		$pdo11 = $stylemodel->db()->db();
		$pdo23 = $purchasemodel->db()->db();


		$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);	//关闭自动提交
		$pdo14->beginTransaction();

		$pdo11->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);	//关闭自动提交
		$pdo11->beginTransaction();

		$pdo23->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);	//关闭自动提交
		$pdo23->beginTransaction();


		try{
			$prc_id = $newmodel->getValue('processor_id');
			$order_type = $newmodel->getValue('order_type');
			$res = $newmodel->saveData($newdo,$olddo);
			if($res ===false){
				throw new Exception("修改失败");
				
			}
			//更新出厂时间(在原有的基础上增加或减少出厂时间)
			$res1 = $this->updateEsmttimeByOrigin($prc_id,$order_type,$olddo);
			if($res1 ===false){
				throw new Exception("出厂时间更新失败");
			}

		}catch(Exception $e){
			$pdo14->rollback();
			$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);  //开启自动提交
			$result['error'] = $e->getMessage();
			Util::jsonExit($result);

		}
		$pdo14->commit();
		$pdo14->setAttribute(PDO::ATTR_AUTOCOMMIT,1);

		$result['success'] = 1;
		$result['tab_id'] = $tab_id;

		Util::jsonExit($result);
		
	}

	/**
	 *	delete，删除
	 */
	public function delete ($params)
	{
		$result = array('success' => 0,'error' => '');
		$id = intval($params['id']);
		$model = new AppProcessorWorktimeModel($id,14);
		$res = $model->delete();
		if($res !== false){
			$result['success'] = 1;
		}else{
			$result['error'] = "删除失败";
		}
		Util::jsonExit($result);
	}

	/*
	*更加供应商ID更新出厂时间
	*
	*/
	public function updateEsmttimeByPrc_id($prc_id,$order_type=1,$infos){
		
		$newmodel = new AppProcessorWorktimeModel(14);
		$productModel = new ProductInfoModel(14);
		$stylemodel = new StyleModel(11);
		$purchasemodel = new PurchaseModel(23);
		$from_type = $order_type==1?2:1;
		$proInfos = $productModel->getBuChanInfoByPrc_id($prc_id,$from_type);
		$now = date('Y-m-d',time());
		foreach($proInfos as $k=>$v){
			//更新出厂时间:未出厂 && 出厂时间大于当前时间
			//if(in_array($v['status'], array('1','2','3','4','5','6')) && $v['esmt_time'] >= $now){
				// $infos = $newmodel->getProcessorInfoByTypeAndId($prc_id,$order_type);
            $is_fj = false;//标准出厂时间是否小于一个供应商放假日期、周末上班日期boss_1324
            //更新出厂时间:未出厂 && 出厂时间大于当前时间
            //if(in_array($v['status'], array('1','2','3','4','5','6')) && $v['esmt_time'] >= $now){
            $esmt_time = $v['esmt_time'];
            if($infos['holiday_time']){
                $fangjia = explode(';', $infos['holiday_time']);
            }
            if($infos['is_work']){
                $zmfangjia = explode(';', $infos['is_work']);
            }
            $fjData = array();
            if(empty($fangjia)){
                $fjData = $zmfangjia;
            }
            if(empty($zmfangjia)){
                $fjData = $fangjia;
            }
            if(!empty($fangjia) && !empty($zmfangjia)){
                $fjData = array_merge($fangjia, $zmfangjia);
            }
            foreach ($fjData as $key => $value) {
                if($esmt_time >= $value){
                    $is_fj = true;//有大于等于一个供应商放假日期、周末上班日期
                }
            }
            if(in_array($v['status'], array('4','7')) && $is_fj == true){//4、生成中，7部分出厂
				if($order_type==1){
					//查找客订单是否有起版号
					$qiban_exists = $purchasemodel->getQiBanInfosByStyle_Sn($v['style_sn'],$v['p_sn']);
					if($v['style_sn'] =='QIBAN' && $qiban_exists){
					//无款起版
					$cycle = $infos['wkqbzq'];

				}else{
					//成品:款式库存在,起版列表没有
						//起版列表信息
					if($v['style_sn'] !='QIBAN'){
						if(empty($qiban_exists)){
							//成品(更新)
							$cycle = $infos['normal_day'];
						}else{
							//有款起版(更新)
							$cycle = $infos['ykqbzq'];

						}
					}
				}

			}else{
				$is_style = $purchasemodel->getStyleInfoByCgd($v['p_sn']);
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
				$add_days = strtotime($v['order_time']) +intval($cycle)*3600*24;
				$order_time = strtotime($v['order_time']);
				for($i=0;$i<=$cycle;$i++){
					$day = date('Y-m-d',strtotime('+'.$i.' day',$order_time));
						//放假日期
						if(strpos($infos['holiday_time'],$day) !== false){
								// $add_days +=3600*24;
								$cycle = $cycle+1;
								continue;
						}
						//暂时只能获得周末休息天数(默认周天休息)
						switch ($infos['is_rest']) {
							case '1':
								break;
							case '2':
								//单休有周末就后延后一天
								if(date('w',strtotime($day))== 0){
									// $add_days +=3600*24;
									$cycle = $cycle+1;
								}
								
								break;
							default:
								//双休遇到周末就延后两天
								if(date('w',strtotime($day))== 6 || date('w',strtotime($day))== 0){
									// $add_days +=3600*24;
									$cycle = $cycle+1;
								}
								break;
						}
						
					//周末上班
					if(strpos($infos['is_work'],$day) !== false && strpos('60',date('w',strtotime($day))) !== false){
							// $add_days =$add_days-3600*24;
							$cycle = $cycle-1;
					}
			
				}

					// $esmt_time =date('Y-m-d',$add_days);
					$res = $productModel->updateEsmttime($v['id'],$day,1);
					if(!$res){
						return false;
					}
			}
			
		}
		return true;
	}


	/*
	* 在原有出厂时间上更新出厂时间
	*
	*/
	public function updateEsmttimeByOrigin($prc_id,$order_type,$olddo){
			//获取老的总时间
			$apkmodel = new AppProcessorWorktimeModel(53);
			$productModel = new ProductInfoModel(14);
			$salesmodel = new SalesModel(27);
			$purchasemodel = new PurchaseModel(54);
			$WarehouseModel = new WarehouseModel(21);
			$attrmodel = new ProductInfoAttrModel(13);	
			$diamodel = new DiamondModel(20);
			$from_type = $order_type==1?2:1;
			$proInfos = $productModel->getBuChanInfoByPrc_ids($prc_id,$from_type);
			$infos = $apkmodel->getProcessorInfoByTypeAndId($prc_id,$order_type);	
			$now = date('Y-m-d',time());
			foreach ($proInfos as $k => $v) {
				//更新出厂时间条件
				//if(in_array($v['status'], array('1','2','3','4','5','6')) && $v['esmt_time'] >= $now){
                $is_fj = false;//标准出厂时间是否小于一个供应商放假日期、周末上班日期boss_1324
            //更新出厂时间:未出厂 && 出厂时间大于当前时间
            //if(in_array($v['status'], array('1','2','3','4','5','6')) && $v['esmt_time'] >= $now){
                //$esmt_time = $v['esmt_time'];
                if($infos['holiday_time']){
                    $fangjia = explode(';', $infos['holiday_time']);
                }
                if($infos['is_work']){
                    $zmfangjia = explode(';', $infos['is_work']);
                }
                /*
                $fjData = array();
                if(empty($fangjia)){
                    $fjData = $zmfangjia;
                }
                if(empty($zmfangjia)){
                    $fjData = $fangjia;
                }
                if(!empty($fangjia) && !empty($zmfangjia)){
                    $fjData = array_merge($fangjia, $zmfangjia);
                }
                foreach ($fjData as $key => $value) {
                    if($esmt_time >= $value){
                        $is_fj = true;//有大于等于一个供应商放假日期、周末上班日期
                    }
                }
                */
                //if(in_array($v['status'], array('4','7')) && $is_fj == true){//4、生成中，7部分出厂
              if(in_array($v['status'], array('4','7'))){//4、生成中，7部分出厂
                	$cycle=0;
                if($from_type==1){
                	if($proInfos['qiban_type']==0){
                		$cycle = $infos['wkqbzq'];
                	}elseif($proInfos['qiban_type']==1){
                		$cycle = $infos['ykqbzq'];
                	}else{
                		$cycle = $infos['normal_day'];
                	}	

				}else{
					$is_style = $purchasemodel->getStyleInfoByCgd($v['p_sn']);
					if($is_style ==1){
						//采购列表  --有款采购
						$cycle = $infos['ykqbzq'];
						$old_cycle = $olddo['ykqbzq'];
					}elseif($is_style ===0){
						//采购列表  --无款采购
						$cycle = $infos['wkqbzq'];
						$old_cycle = $olddo['wkqbzq'];
					}else{
						//采购列表  --标准采购
						$cycle = $infos['normal_day'];
						$old_cycle = $olddo['normal_day'];
					}
				}
				/*
				//时间比较(绝对值)
					$esmt_time = $v['esmt_time'];
					$order_time = $v['order_time'];
					//放假时间差
					$old_holiday_times = explode(";", $olddo['holiday_time']);
					$holiday_times = explode(";", $infos['holiday_time']);

					foreach ($old_holiday_times as $key1 => $val1) {
						if($val1 >= $order_time && $val1 <= $esmt_time){
								++$old_cycle;
						}
					}
					foreach ($holiday_times as $key2 => $val2) {
						if($val2 >= $order_time && $val2 <= $esmt_time){
								++$cycle;
						}
					}
					//上班时间差
					$old_is_work = explode(";", $olddo['is_work']);
					$is_work = explode(";", $infos['is_work']);
					foreach ($old_is_work as $key2 => $val2) {
						if($val2 >= $order_time && $val2 <= $esmt_time){
								--$old_cycle;
						}
					}
					foreach ($is_work as $key2 => $val2) {
						if($val2 >= $order_time && $val2 <= $esmt_time){
								--$cycle;
						}
					}
					$diff_set = abs($cycle-$old_cycle);

					$esmt_time = strtotime($esmt_time);
					
					for($i=0;$i<=$diff_set;$i++){
						if($old_cycle < $cycle){
							$day = date('Y-m-d',strtotime('+'.$i.' day',$esmt_time));	
						}else{
							$day = date('Y-m-d',strtotime('-'.$i.' day',$esmt_time));	
						}
							//放假日期
						if(strpos($infos['holiday_time'],$day) !== false){
								// $add_days +=3600*24; 
								++$diff_set;
								continue;
							}
							//暂时只能获得周末休息天数(默认周天休息)
							switch ($infos['is_rest']) {
								case '1':
									break;
								case '2':
									//有周末就后延后一天
									if(date('w',strtotime($day))== 0){
										// $add_days +=3600*24;
										$diff_set = $diff_set+1;
									}
									break;
								default:
									if(date('w',strtotime($day))== 6 || date('w',strtotime($day))== 0){
										// $add_days +=3600*24;
										$diff_set = $diff_set+1;
									}
									break;
							}											
						//周末上班
						if(strpos($infos['is_work'],$day) !== false && strpos('60',date('w',strtotime($day))) !== false){
								--$diff_set;				
							}
				
						}
						
						$res = $productModel->updateEsmttime($v['id'],$day,1);
						if($res ===false){
							return false;
						}
						*/
					
				  // if($v['bc_sn']=='BC92492'){echo $cycle;exit;}
					if(!empty($cycle)){
						$order_time = strtotime($v['order_time']);
						for($i=1;$i<=$cycle;$i++){
							$day = date('Y-m-d',strtotime('+'.$i.' day',$order_time));					
							//放假日期
							if(strpos($infos['holiday_time'],$day) !== false){
								$cycle = $cycle+1;
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
					
					
					
					$pro_id = $v['prc_id'];
					$order_sn =$v['p_sn'];
					$style_sn =$v['style_sn'];
					
					
					$goodsinfos = $salesmodel->getStockGoodsByOrderSn($order_sn,$style_sn);
					//通过货号在商品列表中找到即为现货
					$is_exists = $WarehouseModel->isExistsByGoodsId($goodsinfos['goods_id']);
					$cert_id =$attrmodel->getCertNumById($id);
					$infos = $apkmodel->getProcessorInfoByTypeAndId($pro_id,$order_type);
					if(empty($goodsinfos['goods_id']) && empty($cert_id)){
						//货号和证书号都为空，就是现货
		
						//获得现货等钻加时、假期、周末上班天数、周末休息时间
						$cycle1 = intval($infos['now_wait_dia']);
					
					}else{
						if($is_exists){
							//现货：现货等钻加时更新出厂时间
							
							//获得现货等钻加时、假期、周末上班天数、周末休息时间
							$cycle1 = intval($infos['now_wait_dia']);
					
						}else{
							//货号没找到，通过证书号去裸钻列表查找判断是期货还是现货
							$cert_id2 = preg_replace('/[a-zA-Z]{0,10}/', '', $cert_id);
							$goods_type = $diamodel->getGoodsTypeByCertId($cert_id,$cert_id2);
							if($goods_type==2){
								//期货
							
								
								//获得现货等钻加时、假期、周末上班天数、周末休息时间
								$cycle1 = intval($infos['wait_dia']);
							}else{
								//现货
								
							
								//获得现货等钻加时、假期、周末上班天数、周末休息时间
								$cycle1 = intval($infos['now_wait_dia']);
							}
						}
					}
					$wait_dia_starttime=$v['wait_dia_starttime'];			
					$wait_dia_endtime=$v['wait_dia_endtime'];
					$behind_wait_dia=$infos['behind_wait_dia'];
					$cycle2='';
					if($wait_dia_starttime != '0000-00-00 00:00:00'){
						if($wait_dia_endtime=='0000-00-00 00:00:00'){
							$time1=$wait_dia_starttime;
							$cycle2=$cycle1;
						}else{
							$cycle2=$behind_wait_dia;
							$time1=$wait_dia_endtime;
						}
					}
					
					
					
					
					if(!empty($cycle2)){
						for($i=1;$i<=$cycle2;$i++){
							$new_day = date('Y-m-d',strtotime('+'.$i.' day',strtotime($time1)));
							//放假日期
							if(strpos($infos['holiday_time'],$new_day) !== false){
								++$cycle2;
								continue;
							}
							//暂时只能获得周末休息天数(默认周天休息)
							switch ($infos['is_rest']) {
								case '1':
									break;
								case '2':
									if(date('w',strtotime($new_day))== 0){
										$cycle2 = $cycle2+1;
									}
									break;
								default:
									if(date('w',strtotime($new_day))== 6 || date('w',strtotime($new_day))== 0){
										$cycle2 = $cycle2+1;
									}
									break;
							}
							//周末上班
							if(strpos($infos['is_work'],$new_day) !== false && strpos('60',date('w',strtotime($new_day))) !== false){
								--$cycle2;
							}
						}
					}
					//if($v['bc_sn']=='BC89556'){echo $new_day;exit;}				
					if(isset($day)){
						//if(isset($day) && $proInfos['esmt_time'] >=$now){
						$esmt_time = max($new_day,$day);
						
						    $res = $productModel->updateEsmttime($v['id'],$esmt_time,1);
						    if($res ===false){
							    return false;
						    }
						
					}else{
						return false;
					}
					
					
						
				
			}

		}
			return true;
	}


}

?>