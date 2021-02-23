<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductOqcOpraController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 12:05:59
 *   @update	:
 *  -------------------------------------------------
 */
class ProductOqcOpraController extends Controller
{
	protected $smartyDebugEnabled = false;

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('product_oqc_opra_search_form.html',array('bar'=>Auth::getBar()));
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
			//'参数' = _Request::get("参数");


		);
		$page = _Request::getInt("page",1);
		$where = array();

		$model = new ProductOqcOpraModel(13);
		$data = $model->pageList($where,$page,10,false);
		$pageData = $data;
		$pageData['filter'] = $args;
		$pageData['jsFuncs'] = 'product_oqc_opra_search_page';
		$this->render('product_oqc_opra_search_list.html',array(
			'pa'=>Util::page($pageData),
			'page_list'=>$data
		));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add($params)
	{
		$id = intval($params["id"]);//布产单ID

		$result = array('success' => 0,'error' => '','title' => 'OQC质检');
		$model = new ProductInfoModel($id,14);
		$from_type=$model->getValue('from_type');
		/*if($from_type==1 && SYS_SCOPE=='boss'){
			$result['content'] = "采购单类型的布产单不能OQC质检";
			Util::jsonExit($result);
		}*/
		$status = $model->getValue('status');

                /** 获取顶级导航列表**/
                $newmodel = new ProductFqcConfModel(13);
                $top_menu = $newmodel->get_top_menu();
		if($status != 4  && $status != 7)//布产单只有生产中和部分出厂两个状态下才能进行工厂操作
		{
			$result['content'] = "只有 \"生产中\" 和 \"部分出厂\" 两个状态下才能进行OQC质检";
			Util::jsonExit($result);
		}

		$result['content'] = $this->fetch('product_oqc_opra_info.html',array(
			'dd' => new DictView(new DictModel(1)),
			'id' => $id,
            'from_type'=>$from_type,
            'top_menu'=>$top_menu
		));
		$result['title'] = 'OQC质检';
		Util::jsonExit($result);
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add_pl($params)
	{
		$ids = $params["_ids"];//布产单ID
		//var_dump($ids);exit;
		$result = array('success' => 0,'error' => '','title' => 'OQC质检');
		$model = new ProductInfoModel(14);
		#循环判断 布产单只有生产中和部分出厂两个状态下才能进行OQC质检
		$is_ok = $model->IsStatusChuchang($ids);
		if($is_ok == false)//布产单只有生产中和部分出厂两个状态下才能进行OQC质检
		{
			echo "只有 \"生产中\" 和 \"部分出厂\" 两个状态下才能进行OQC质检";exit;
		}

		/** 获取顶级导航列表**/
		$newmodel = new ProductFqcConfModel(13);
		$top_menu = $newmodel->get_top_menu();
		$id_s  = join(",",$ids);
		$this->render('product_oqc_opra_info_pl.html',array(
			'dd' => new DictView(new DictModel(1)),
			'id' => $id_s,
            'top_menu'=>$top_menu,
			'title'=>'OQC质检'
		));
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);//布产ID
		$model = new ProductOqcOpraModel(13);
		$list = $model->pageList(array('bc_id'=>$id));

		$result['title'] = "OQC质检详情";
		$result['content'] = $this->fetch('product_oqc_opra_show.html',array(
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
		// echo 88;exit;
		$bc_ids = $params['ids'];
        $oqc_num =isset($params['oqc_num'])?intval($params['oqc_num']):0;//质检数量
        $oqc_bf_num =isset($params['oqc_bf_num'])?intval($params['oqc_bf_num']):0;//报废数量
        $reason_scrapped =isset($params['reason_scrapped'])?$params['reason_scrapped']:'';//报废原因
        $oqc_no_num =isset($params['oqc_no_num'])?intval($params['oqc_no_num']):0;//质检未过数量
        $oqc_result =isset($params['oqc_result'])?$params['oqc_result']:'';//质检结果
		$oqc_data = array(
            'oqc_num' => $oqc_num,
            'oqc_bf_num' => $oqc_bf_num,
            'oqc_no_num' => $oqc_no_num,
            'reason_scrapped' => $reason_scrapped,
			'oqc_result' => $oqc_result,		//质检结果
			'oqc_reason' => isset($params['oqc_reason'])?$params['oqc_reason']:'',	//质检未过类型
			'oqc_info'=>$params['oqc_info'],			//质检备注
			'oqc_problem'=>$params['oqc_problem'],		//质检未过原因
		);
		$bc_id_arr = explode(',', $bc_ids);

		// echo "<pre>";
		// print_r($params);
		// exit;
        $model = new ProductOqcOpraModel(14);
		foreach($bc_id_arr AS $bc_id){
			$pro_model = new ProductInfoModel($bc_id,14);
			$model_pw = new AppProcessorWorktimeModel(13);
			$status = $pro_model->getValue('status');
            $from_type = $pro_model->getValue('from_type');
            $bc_num = $pro_model->getValue('num');
			
            //if(SYS_SCOPE=='boss'){
				if($status != 4  && $status != 7)		//布产单只有生产中和部分出厂两个状态下才能进行oqc质检
				{
					$result['error'] = "只有 \"生产中\" 和 \"部分出厂\" 两个状态下才能进行oqc质检";
					Util::jsonExit($result);
				}
		    //}
            /*if(SYS_SCOPE=='zhanting'){
				if($status != 4 )		//布产单只有生产中和部分出厂两个状态下才能进行oqc质检
				{
					$result['error'] = "只有 \"生产中\" 状态下才能进行oqc质检";
					Util::jsonExit($result);
				}
		    }*/
			if($oqc_data['oqc_result'] !=1 && ($oqc_data['oqc_reason'] == '' || $oqc_data['oqc_problem'] == ''))//质检未过，未过原因为必选.
			{
				$result['error'] = "OQC未过原因必选";
				Util::jsonExit($result);//oqc_reason oqc_problem
			}

            //若是 采购布产单  报废数量和出货数量必须有一个大于0
            if($from_type==1){
                //2015-11-4 boss571
                if($oqc_result==1 && $oqc_num <= 0 && $oqc_bf_num <= 0)
                {
                    $result['error'] = "质检通过时，出货数量+报废数量要大于0";
                    Util::jsonExit($result);
                }
                
                //2015-11-4 boss571
                if($oqc_result==0 && $oqc_bf_num <= 0 && $oqc_no_num <=0)
                {
                    $result['error'] = "质检未通过，报废数量和质检未过数量必须有一个大于0";
                    Util::jsonExit($result);
                }
                
                //报废数大于0，报废原因必填 2015-11-4 boss571
                if($oqc_bf_num > 0 && $reason_scrapped=='')
                {
                    $result['error'] = "报废数大于0，报废原因必填";
                    Util::jsonExit($result);
                }

                //出库数量+报废数量 =总数量
                $other_num=0;
                $sql="select sum(oqc_num) as num,sum(oqc_bf_num) as bf_num,sum(oqc_no_num) as oqc_no_num from product_oqc_opra where bc_id='$bc_id'";
                //echo $sql;
                foreach ($model->db()->query($sql) as $row2){
                	$other_num=$bc_num-$row2['num']-$row2['bf_num']; 
                    if( $oqc_num+$oqc_bf_num > $other_num )
                    {
                        $result['error'] = "质检通过数量:".$oqc_num ."+报废数量:". $oqc_bf_num ." 超过剩余质检数量:". ($bc_num-$row2['num']-$row2['bf_num']) .",不允许操作。";
                        Util::jsonExit($result);
                    }
                                   
                } 
                $other_num= $other_num-$oqc_num-$oqc_bf_num;
                //var_dump($other_num);die;
            }                    

			//更新实际等钻结束时间
			$wait_dia_starttime = $pro_model->getValue('wait_dia_starttime');
			$wait_dia_endtime = $pro_model->getValue('wait_dia_endtime');
			if($wait_dia_endtime =='0000-00-00 00:00:00' && $wait_dia_starttime !='0000-00-00 00:00:00'){
				$wait_dia_endtime = date('Y-m-d H:i:s',time());
				$res1= $pro_model->updateWait_dia_endtimeById($bc_id,$wait_dia_endtime);
				if($res1===false){
					$result['error'] = '更新实际等钻结束时间失败！';
					Util::jsonExit($result);
				}
			}

			//等钻操作后，操作非等钻后等钻完成时间和开始生产的出厂时间比对，大于更新出厂时间
			$wait_dia_starttime = $pro_model->getValue('wait_dia_starttime');
			$wait_dia_endtime = $pro_model->getValue('wait_dia_endtime');
			$esmt_time = $pro_model->getValue('esmt_time');
			$now =date('Y-m-d',time());
            
			//if($wait_dia_endtime =='0000-00-00 00:00:00' && $wait_dia_starttime !='0000-00-00 00:00:00' && $now <= $esmt_time){
			if($wait_dia_endtime =='0000-00-00 00:00:00' && $wait_dia_starttime !='0000-00-00 00:00:00'){
				$from_type = $pro_model->getValue('from_type');
				$order_type =$from_type ==1?2:1;
				$res2 = $this->updateEsmttimeByBc_Id($bc_id,$order_type);
				if($res2 === false){
					$result['error'] = "更新标准出厂时间失败!";
					Util::jsonExit($result);
					
	
				}
			}

			//质检未过->订单问题更新出厂时间
			$esmt_time = $pro_model->getValue('esmt_time');
			$now = date('Y-m-d',time());
			if($oqc_data['oqc_result'] ==0 && $oqc_data['oqc_reason'] ==41 && $now <= $esmt_time){
				//质检订单问题加时
				$from_type = $pro_model->getValue('from_type');
				$order_type = $from_type==2?1:2;
				$res = $this->updateEsmttimeById($bc_id,$order_type);
				if($res ===false){
					$result['error'] = "订单问题更新出厂时间失败!";
					Util::jsonExit($result);
				}
			}

			//更新OQC质检通过时间(销售单)
			$from_type = $pro_model->getValue('from_type');
			if($oqc_data['oqc_result']==1){
				$oqc_pass_time = date('Y-m-d H:i:s',time());
				$res2= $pro_model->updateOqc_pass_timeById($bc_id,$oqc_pass_time);
				if($res2===false){
					$result['error'] = '更新OQC质检通过时间失败！';
					Util::jsonExit($result);
				}
			}
			
		}

		$res = $model->AddOqcAction($bc_id_arr , $oqc_data);
		if($res['success'] == 1){
			$result['success'] = 1;
		}
		$result['error'] = $res['error'];

		Util::jsonExit($result);
	}

         //获取二级分类
	public function get_protype ()
	{
		$where= $_REQUEST['id'];
		/** 获取对应二级导航列表**/
		$model = new ProductFqcConfModel(13);
		$second_menu = $model->get_second_menu($where);
		$html ="";
                $html .= "<option value=''>请选择</option>";
		foreach ($second_menu as $key=>$val)
		{
			$html .= "<option value='{$val['id']}'>{$val['cat_name']}</option>";
		}

		Util::jsonExit($html);
	}
	public function jisuan_gongqi($normal_day,$is_rest,$esmt_time)
	{
		$ziranri = 1;
		$gongzuori = 0;
		$liushuiri =$esmt_time;
		while($gongzuori < $normal_day)
		{
			$liushuiri = $esmt_time+3600*24*$ziranri;//流水日期
			$ziranri++;
			if(date("w",$liushuiri) == 6)
			{
				if($is_rest ==2)
				{//单休
					$gongzuori++;
				}
				elseif($is_rest ==1)
				{//不休
					$gongzuori++;
				}
			}
			elseif(date("w",$liushuiri) == 0)
			{
				if($is_rest ==1)
				{//不休
					$gongzuori++;
				}
			}
			else
			{
				$gongzuori++;
			}
		}
		return $liushuiri;
	}
	/**
	 *	insert，信息入库
	 */
	public function insert_pl ($params)
	{
		echo '<pre>';print_r($params);echo '</pre>';die;
		$ids = $params["ids"];//布产单ID
		$ids = explode(',',$ids);
		//var_dump($params);exit;
		$oqc_result		= $params['oqc_result'];
		$oqc_reason		= isset($params['oqc_reason'])?$params['oqc_reason']:'';
		$oqc_info		= $params['oqc_info'];
		$oqc_problem	= $params['oqc_problem'];
		$pro_model = new ProductInfoModel(14);
		#循环判断 布产单只有生产中和部分出厂两个状态下才能进行工厂操作
		$is_ok = $pro_model->IsStatusChuchang($ids);
		if($is_ok == false)//布产单只有生产中状态下才能进行工厂操作
		{
			$result['error'] = "布产单状态不对，不允许操作。";
			Util::jsonExit($result);
		}

		if($oqc_result!=1 && ($oqc_reason == '' || $oqc_problem == ''))//质检未过，未过原因为必选.
		{
			$result['error'] = "OQC未过原因必选";
			Util::jsonExit($result);//oqc_reason oqc_problem
		}
		$model = new ProductOqcOpraModel(14);

		//批量操作质检
		foreach ($ids as $id)
		{
                        $dd = new DictModel(1);
			$pro_model = new ProductInfoModel($id,14);
			#质检未通过 如果是订单加时则需修改标准出厂时间，在当前时间小于标准出厂时间 4订单问题
			//OQC未过，订单问题。在未超期的情况下加时(订单问题加时)。如果已经超期了，就不加时了。
			$esmt_time = $pro_model->getValue('esmt_time');

			//重新赋值保证数据一致
			$oqc_result		= $params['oqc_result'];
			$oqc_reason		= isset($params['oqc_reason'])?$params['oqc_reason']:'';
			$oqc_info		= $params['oqc_info'];
			$oqc_problem	= $params['oqc_problem'];

			if ($oqc_result!=1 &&  $oqc_reason==4 && date('Y-m-d') <=  $esmt_time)
			{
					//1：问题订单(计算方式 标准出厂时间加上订单加时时间)（计算工作日）5
					//2、等钻加时 当前时间加上等钻加时时间 （不计算工作日）25 ；
					//3、等钻后加时 当前时间加上等钻后加时时间（计算工作日）5
					//4、开始生产设置标准出厂时间，根据默认设置标准出厂时间（7+1天23:59:59）或者提示设置 date("Y-m-d 23:59:59",time()+24*3600*8); 有则按工作日计算天数
					#工厂加时信息获取
					$model_pw = new AppProcessorWorktimeModel(13);
					$prc_id = $pro_model->getValue('prc_id');
					$order_problem = 0;
					$is_rest       = 1;//不休
					$row   = $model_pw->getInfoById($prc_id);
					if ($row)
					{
						$order_problem = $row['order_problem'];
						$is_rest	   = $row['is_rest'];
					}
					$str_time = strtotime($esmt_time);
					$time = $model_pw->js_normal_time($order_problem,$is_rest,$str_time);
					$pro_model->setValue('esmt_time',$time);

                                        //add by zhangruiying
                                        $pro_model->setValue('edit_time',date('Y-m-d H:i:s'));
                                        $pro_model->setValue('remark',"OQC操作：".$oqc_result." ".$dd->getEnum('OQC_reason',$oqc_reason)."，备注：".$oqc_info);

                                        //add end

					$pro_model->save();
			}
                        else
                        {
                             $pro_model->setValue('edit_time',date('Y-m-d H:i:s'));
                                       $pro_model->setValue('remark',"OQC操作：".$oqc_result." ".$dd->getEnum('OQC_reason',$oqc_reason)."，备注：".$oqc_info);
                                        //add end

					$pro_model->save();
                        }
			//exit;

			$oqc_reason = $oqc_result==1?0:$oqc_reason;
			$olddo = array();
			$newdo=array(
				"bc_id"			=> $id,
				"oqc_result"	=> $oqc_result,
				"oqc_reason"	=> $oqc_reason,
				"oqc_info"		=> $oqc_info,
				"opra_uid"		=> $_SESSION['userId'],
				"opra_uname"	=> $_SESSION['userName'],
				"opra_time"		=> date("Y-m-d H:i:s")
			);
			$res = $model->saveData($newdo,$olddo);
			//var_dump($res);//exit;
			if($res !== false)
			{
				//记录操作日志
				$logModel = new ProductOpraLogModel(14);

				$oqc_result = $oqc_result?"质检通过":"质检未过";
				$oqc_info = $oqc_info?$oqc_info:'无';
				//$logModel->addLog($id,4,"OQC操作：".$oqc_result." ".$dd->getEnum('OQC_reason',$oqc_reason)."，备注：".$oqc_info);
				$logModel->addLog($id,"OQC操作：".$oqc_result." ".$dd->getEnum('OQC_reason',$oqc_reason)."，备注：".$oqc_info);
				//$pro_model->Writeback($id, "OQC操作：".$oqc_result." ".$dd->getEnum('OQC_reason',$oqc_reason)."，备注：".$oqc_info);	//回写订单操作日志 BY hulichao
				$result['success'] = 1;
			}
			else
			{
				$result['error'] = "操作失败";
			}
		}

		$result['success'] = 1;
		Util::jsonExit($result);
	}


	/*
	*订单问题根据供应商ID更新出厂时间
	*
	*/
	public function updateEsmttimeById($id,$order_type=1){
		$newmodel = new AppProcessorWorktimeModel(14);
		$productModel = new ProductInfoModel($id,14);
		$prc_id =$productModel->getValue('prc_id');
		$esmt_time =$productModel->getValue('esmt_time');
		$infos = $newmodel->getProcessorInfoByProId($prc_id,$order_type);
				$cycle = intval($infos['order_problem']);
				$time = strtotime($esmt_time);
				for($i=0;$i<=$cycle;$i++){
					$day = date('Y-m-d',strtotime('+'.$i.' day',$time));
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
				$res = $productModel->updateEsmttime($id,$day,1);
				if(!$res){
					return false;
				}
				return true;
		}

	/*
	*更加供应商ID更新出厂时间(送钻时间比较)
	*
	*/
	public function updateEsmttimeByBc_Id($id,$order_type=1){

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


}

?>