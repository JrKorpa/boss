<?php
	 /**
	* 销售计划报表管理器
	*/
	class SalePlanTwoController extends CommonController
	{
		private static 	$gainParent='渠道二部';
		//要想下载模板
		protected $whitelist = array('downloadCSV');
		function  index($params){
		  $this->render('sale_plan_two_search_form.html',array('bar'=>Auth::getBar()));
		}

		function search($params){
			$args=array(
			    'mod'	=> _Request::get("mod"),
			    'con'	=> substr(__CLASS__, 0, -10),
			    'act'	=> __FUNCTION__,
			    'export_time_start'=>_Request::get("export_time_start"),
			    'dep'=>_Request::get('dep[]'),
			);
			$sale= new SaleQuotaItemModel(27);
			$count=$sale->queryItemCount(array('time'=>substr($params['export_time_start'],0,7),'channel'=>self::$gainParent));
			if($count[0]['count(*)']<=0){
				exit("搜索月份数据不存在");
			}

			$day=date('t',strtotime($params['export_time_start']));//获取这一个月多少天
			$firstDay= date("Y-m-d", strtotime(substr($params['export_time_start'],0,7)."-01"));//这个月的第一天
			$supFirstDay=date('Y-m-d',strtotime('+1 months',strtotime($firstDay)));//下个月第一天
			$start_week=date('w',strtotime(substr($params['export_time_start'],0,7)."-01"));//获取这个月的第一天是星期几
			$str=substr($params['export_time_start'],0,7);//获取年和月
			$weekDatas=[];
			$weeks=[];
			$arrDays=[];
			$week_arrays=[];
			$rootWeeks=[];
			$month=[];
			//把数据库按照年月、渠道拿出来的数据按顺序拆分成二维数组
			$ParentData=$sale->gainParent(self::$gainParent,"2800");
			$arr=['cate_type1'=>self::$gainParent,'pdateTime'=>$str];
			$itemData=$sale->query($arr);
			$viewData=[];
			for($j=0;$j<count($itemData);$j++){
				$viewData[$itemData[$j]['item_id']][$itemData[$j]['pdate']]=$itemData[$j]['plan_value'];
			}
			$start_week_end=[];//获取每周的开始结束时间
			//这个月的第一天不是星期一也不是星期天 就把1号赋给start，作为这个月的开始
			if($start_week!=1||$start_week!=0){
				$start_week_end['start']=1;
			}
			for($i=1;$i<=$day;$i++){
				$element=trim($str.($i<10?"_0".$i:"_".$i));
				$element2=$str.($i>=10?"-":"-0").$i;
				if (date('w',strtotime($element2))==1){
					$start_week_end['start']=$i;
				}
				if(date('w',strtotime($element2))==0||$i==$day){
					$start_week_end['end']=$i;
					$week_arrays[]=$start_week_end;
					$start_week_end=[];
				}
				$arrDays[]=str_replace('-','_',$element);	
				//毛利额计划
				$viewData['2250'][$element2]=sprintf('%1.2f',($viewData['2100'][$element2])*($viewData['2200'][$element2]/100));
			}
			$array=$this->channelTwo($viewData,$firstDay,$supFirstDay,$sale);
			$practical=$array['reality'];
			$complete=$array['complete'];
		    $gap=$array['gap'];
		    $refund=$array['refund'];
			
			/*$weekDatas的0下标是计划
			 *	$weeks的0下标为实际 1下标为完成率
			 */
			//获取周的数据
			for($i=0;$i<count($ParentData);$i++){
					$childs=$viewData[$ParentData[$i]['id']];
					//新增客单价 计划值
					if ($ParentData[$i]['id']==2400){
					    //新增订单量
					    $resultArray=$this->weekCal($viewData,2500,$week_arrays,$str);
                        for ($j=0;$j<count($week_arrays);$j++){
                            $weekDatas[$ParentData[$i]['id']][]=sprintf('%1.2f',isset($resultArray[0][$j])?($resultArray[0][$j]==0?0:$weekDatas[1900][$j]/$resultArray[0][$j]):0);
                        }
                        $month['sourceDB'][$ParentData[$i]['id']][]=sprintf('%1.2f',  array_sum($resultArray[0])==0?0:$month['sourceDB'][1900][0]/array_sum($resultArray[0]));
                    }else if($ParentData[$i]['id']==2600){ //收入客单价
					    //收入订单量
                        $resultArray=$this->weekCal($viewData,2700,$week_arrays,$str);
                        for ($j=0;$j<count($week_arrays);$j++){
                            $weekDatas[$ParentData[$i]['id']][]=sprintf('%1.2f',isset($resultArray[0][$j])?($resultArray[0][$j]==0?0:$weekDatas[2100][$j]/$resultArray[0][$j]):0);
                        }
                        $month['sourceDB'][$ParentData[$i]['id']][]=sprintf('%1.2f',  array_sum($resultArray[0])==0?0:$month['sourceDB'][2100][0]/array_sum($resultArray[0]));
                    }else if($ParentData[$i]['id']==2200){//毛利率
                        //毛利额
                        $resultArray=$this->weekCal($viewData,2250,$week_arrays,$str);
                        for ($j=0;$j<count($week_arrays);$j++){
                            $weekDatas[$ParentData[$i]['id']][]=sprintf('%1.2f',$weekDatas[2100][$j]==0?0:$resultArray[0][$j]/$weekDatas[2100][$j]*100);
                        }

                        $month['sourceDB'][$ParentData[$i]['id']][]=sprintf('%1.2f',  $month['sourceDB'][2100][0]==0?0:array_sum($resultArray[0])/$month['sourceDB'][2100][0]*100);
                    }else{
                        $resultArray=$this->weekCal($viewData,$ParentData[$i]['id'],$week_arrays,$str);

                        $weekDatas[$ParentData[$i]['id']]=$resultArray[0];
                        $month['sourceDB'][$ParentData[$i]['id']][]=sprintf('%1.2f',empty($resultArray[0])?0:array_sum($resultArray[0]));

                    }

					//获取每周实际的数据
					if($ParentData[$i]['id']==2200){//毛利率
						$weekMaoLi=[];
						/*foreach ($week_arrays as $key => $value) {
							$val=0;
							$date=$sale->channelTwoSDayMargin($str.($value['start']<10?"-0":"-").$value['start'],$str.($value['start']<10?"-0":"-").$value['end']);
							foreach ($date as $k => $v) {
								$val+=($v['earning']-$v['cost']);
							}
							$weekMaoLi[]=$val;
						}*/
						$weekDate=[];
                        $weekMaoLi = $earning=$this->parseWeekData($week_arrays,$practical,2250,$str);
						//收入
						$earning=$this->parseWeekData($week_arrays,$practical,2100,$str);
						for($d=0;$d<count($week_arrays);$d++){
							$weekDate[]=sprintf('%1.2f',$earning[$d]==0?0.00:($weekMaoLi[$d]/$earning[$d]*100));
						}
						$weeks[$ParentData[$i]['id']][]=$weekDate;
					}else if($ParentData[$i]['id']==2400){//新增客单价
						$orderNumber=$this->parseWeekData($week_arrays,$practical,2500,$str);
						$weekDate=[];
						for($d=0;$d<count($week_arrays);$d++){
							$weekDate[]=sprintf('%1.2f',$orderNumber[$d]==0?0.00:($weeks[1900][0][$d]/$orderNumber[$d]));
						}
						$weeks[$ParentData[$i]['id']][]=$weekDate;
					}else if($ParentData[$i]['id']==2600){//收入客单价
						$shouRuOrderNumber=$this->parseWeekData($week_arrays,$practical,2700,$str);
						$weekDate=[];
						for($d=0;$d<count($week_arrays);$d++){
							$weekDate[]=sprintf('%1.2f',$shouRuOrderNumber[$d]==0?0.00:($weeks[2100][0][$d]/$shouRuOrderNumber[$d]));
						}
						$weeks[$ParentData[$i]['id']][]=$weekDate;
					}else{
						$weeks[$ParentData[$i]['id']][]=$this->parseWeekData($week_arrays,$practical,$ParentData[$i]['id'],$str);
					}

					if($ParentData[$i]['id']==2200){
						$weekMaoLi=[];
						/*foreach ($week_arrays as $key => $value) {
							$val=0;
							$date=$sale->channelTwoSDayMargin($str.($value['start']<10?"-0":"-").$value['start'],$str.($value['start']<10?"-0":"-").$value['end']);
							foreach ($date as $k => $v) {
								$val+=$v['margin'];
							}
							$weekMaoLi[]=$val;
						}*/
                        $weekMaoLi = $earning=$this->parseWeekData($week_arrays,$practical,2250,$str);
						$earning=$this->parseWeekData($week_arrays,$practical,2100,$str);
						//月  实际值
						$month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',array_sum($earning)==0?0.00:(array_sum($weekMaoLi)/array_sum($earning)*100));
					}else if($ParentData[$i]['id']==2400){//新增客单价 月
                        $orderNumber=$this->parseWeekData($week_arrays,$practical,2500,$str);
                        $month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',array_sum( $orderNumber)==0?0:$month['practical'][1900][0]/array_sum($orderNumber));
                    }else if($ParentData[$i]['id']==2600){//收入客单价 月
                        $shouRuOrderNumber=$this->parseWeekData($week_arrays,$practical,2700,$str);//收入客单价数量
                        $month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',array_sum($shouRuOrderNumber)==0?0:$month['practical'][2100][0]/array_sum($shouRuOrderNumber));
                    }else{
						//月  实际值
						$month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',array_sum($weeks[$ParentData[$i]['id']][0]));
					}
					//周 完成率
					$index=0;
					if($ParentData[$i]['id']==2050 || $ParentData[$i]['id']==2350){
						$index=	$ParentData[$i]['id']-50;
					}
					if($index!=0){
						$weekComplete=[];
			  	    	for($d=0;$d<count($week_arrays);$d++){
							$weekComplete[]=sprintf('%1.2f',$weekDatas[$index][$d]==0?0:($weekDatas[$ParentData[$i]['id']][$d]/$weekDatas[$index][$d]*100));
						}
						$weeks[$ParentData[$i]['id']][]=$weekComplete;
					}else{
						 //获取每周完成率的数据
						 $weeks[$ParentData[$i]['id']][]=$this->computeCompleteWeek($weekDatas,$weeks,$ParentData[$i]['id'],$week_arrays);
					}

					if($ParentData[$i]['id']==2200){
						//月的完成率
					$month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',$month['sourceDB'][$ParentData[$i]['id']][0]==0?0.00:(($month['practical'][$ParentData[$i]['id']][0]/100)/($month['sourceDB'][$ParentData[$i]['id']][0]/100)*100));
					}else if($index!=0){
						$month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',$month['sourceDB'][$index][0]==0?0.00:($month['sourceDB'][$ParentData[$i]['id']][0]/$month['sourceDB'][$index][0]*100));
					}else{
						//月的完成率
						$month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',$month['sourceDB'][$ParentData[$i]['id']][0]==0?0.00:($month['practical'][$ParentData[$i]['id']][0]/$month['sourceDB'][$ParentData[$i]['id']][0]*100));
					}

					//周的差值
					$diff=[];
					if($ParentData[$i]['id']==2050){
						for($d=0;$d<count($week_arrays);$d++){
							$diff[]=sprintf('%1.2f',$weekDatas[$ParentData[$i]['id']][$d]-$weekDatas[2000][$d]);
						}
					}else if($ParentData[$i]['id']==2350){
						for($d=0;$d<count($week_arrays);$d++){
							$diff[]=sprintf('%1.2f',$weekDatas[$ParentData[$i]['id']][$d]-$weekDatas[2300][$d]);
						}
					}else{
						for($d=0;$d<count($week_arrays);$d++){
							$diff[]=sprintf('%1.2f',$weeks[$ParentData[$i]['id']][0][$d]-$weekDatas[$ParentData[$i]['id']][$d]);
						}
					}
					$weeks[$ParentData[$i]['id']][]=$diff;
					
					if($index!=0){
						$month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',$month['sourceDB'][$ParentData[$i]['id']][0]-$month['sourceDB'][$index][0]);
					}else{
						//月 差距
						$month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',$month['practical'][$ParentData[$i]['id']][0]-$month['sourceDB'][$ParentData[$i]['id']][0]);
					}		
			}
			$weeks['2800'][]=$this->parseWeekData($week_arrays,$refund,'2800',$str);
			$weeks['2850'][]=$this->parseWeekData($week_arrays,$refund,'2850',$str);
			$month['practical'][2800][]=sprintf('%1.2f',array_sum($weeks['2800'][0]));
			$month['practical'][2850][]=sprintf('%1.2f',array_sum($weeks['2850'][0]));

		    $this->render('sale_plan_two_search_list.html',array(
			  	'ParentData'=>$ParentData,
			  	'datetime'=>$params['export_time_start'],
			  	'days'=>$arrDays,
			  	'view'=>$viewData,
			  	'weeks'=>$weekDatas,
			  	'practical'=>$practical,
			  	'week'=>$weeks,
			  	'complete'=>$complete,
			  	'refund'=>$refund,
			  	'day'=>$day,
			  	'array'=>isset($array['channelDataCount'])?$array['channelDataCount']:'',
			  	'rootWeeks'=>$rootWeeks,
			  	'gap'=>$gap,
			  	'month'=>$month,
			));
		}

		//渠道二部
		function channelTwo(array $viewData,$firstDay,$supFirstDay,$sale){
			$practical=[];//实际
			$complete=[];//完成率
			$gap=[];//差值
			$refund=[];//退款
			$days=date('t',strtotime($firstDay));
			for($i=1;$i<=$days;$i++){
				$date=substr($firstDay,0,7).($i<10?("-0".$i):"-".$i);
				//批发新增完成率
				$complete['2050'][$date]=sprintf('%1.2d',$viewData['2000'][$date]==0?0:($viewData['2050'][$date]/$viewData['2000'][$date])*100);
				//批发新增差值
				$gap['2050'][$date]=sprintf('%1.2f',$viewData['2050'][$date]-$viewData['2000'][$date]);
				//批发新增差值
				$gap['2350'][$date]=sprintf('%1.2f',$viewData['2350'][$date]-$viewData['2300'][$date]);

			}
			foreach ($sale->channelTwoDayMargin($firstDay,$supFirstDay) as $key => $value) {
				//零售新增实际
				$practical['1900'][$value['pay_date']]=sprintf('%1.2f',$value['order_amount']);
				//零售新增完成率
				$complete['1900'][$value['pay_date']]=sprintf('%1.2f',$viewData['1900'][$value['pay_date']]==0?0:($practical['1900'][$value['pay_date']]/$viewData['1900'][$value['pay_date']])*100);

				//零售新增差值
				$gap['1900'][$value['pay_date']]=sprintf('%1.2f',$practical['1900'][$value['pay_date']]-$viewData['1900'][$value['pay_date']]);

				//新增客单价实际
				$practical['2400'][$value['pay_date']]=sprintf('%1.2f',$value['count']==0?0:($practical['1900'][$value['pay_date']]/$value['count']));
				//新增客单价完成率
				$complete['2400'][$value['pay_date']]=sprintf('%1.2f',$viewData['2400'][$value['pay_date']]==0?0:($practical['2400'][$value['pay_date']]/$viewData['2400'][$value['pay_date']])*100);
				//新增客单价差值
				$gap['2400'][$value['pay_date']]=sprintf('%1.2f',$practical['2400'][$value['pay_date']]-$viewData['2400'][$value['pay_date']]);

				//新增订单量实际
				$practical['2500'][$value['pay_date']]=sprintf('%1.2f',$value['count']);
				//新增订单量完成率
				$complete['2500'][$value['pay_date']]=sprintf('%1.2f',$viewData['2500'][$value['pay_date']]==0?0:($practical['2500'][$value['pay_date']]/$viewData['2500'][$value['pay_date']])*100);
				//新增订单量差值
				$gap['2500'][$value['pay_date']]=sprintf('%1.2f',$practical['2500'][$value['pay_date']]-$viewData['2500'][$value['pay_date']]);
			}

			foreach ($sale->channelTwoSDayMargin($firstDay,$supFirstDay) as $key => $value) {
				//收入实际
				$practical['2100'][$value['pay_date']]=sprintf('%1.2f',$value['earning']);
				//收入完成率
				$complete['2100'][$value['pay_date']]=sprintf('%1.2f',$viewData['2100'][$value['pay_date']]==0?0:($practical['2100'][$value['pay_date']]/$viewData['2100'][$value['pay_date']])*100);
				//收入差值
				$gap['2100'][$value['pay_date']]=sprintf('%1.2f',$practical['2100'][$value['pay_date']]-$viewData['2100'][$value['pay_date']]);

				//毛利率完成率
				$complete['2200'][$value['pay_date']]=sprintf('%1.2f',$viewData['2200'][$value['pay_date']]==0?0:(($practical['2200'][$value['pay_date']]/100)/($viewData['2200'][$value['pay_date']]/100)*100));
				//毛利率差值
				$gap['2200'][$value['pay_date']]=sprintf('%1.2f',$practical['2200'][$value['pay_date']]-$viewData['2200'][$value['pay_date']]);


				//收入客单价实际
				$practical['2600'][$value['pay_date']]=sprintf('%1.2f',$value['count']==0?0:($practical['2100'][$value['pay_date']]/$value['count']));
				//收入客单价完成率
				$complete['2600'][$value['pay_date']]=sprintf('%1.2f',$viewData['2600'][$value['pay_date']]==0?0:($practical['2600'][$value['pay_date']]/$viewData['2600'][$value['pay_date']]*100));
				//收入客单价差值
				$gap['2600'][$value['pay_date']]=sprintf('%1.2f',$practical['2600'][$value['pay_date']]-$viewData['2600'][$value['pay_date']]);

				//收入订单量实际
				$practical['2700'][$value['pay_date']]=sprintf('%1.2f',($value['count']));
				//收入订单量完成率
				$complete['2700'][$value['pay_date']]=sprintf('%1.2f',$viewData['2700'][$value['pay_date']]==0?0:($practical['2700'][$value['pay_date']]/$viewData['2700'][$value['pay_date']]*100));
				//收入订单量差值
				$gap['2700'][$value['pay_date']]=sprintf('%1.2f',$practical['2700'][$value['pay_date']]-$viewData['2700'][$value['pay_date']]);


				//毛利额实际
				$practical['2250'][$value['pay_date']]=sprintf('%1.2f',$practical['2100'][$value['pay_date']]-$value['cost']);

                //毛利率实际
                $practical['2200'][$value['pay_date']]=sprintf('%1.2f',$value['earning']==0?0:($practical['2250'][$value['pay_date']]/$value['earning']*100));

				//毛利额完成率
				$complete['2250'][$value['pay_date']]=sprintf('%1.2f',$viewData['2250'][$value['pay_date']]==0?0.00:($practical['2250'][$value['pay_date']]/$viewData['2250'][$value['pay_date']]*100));

				//毛利额差值
				$gap['2250'][$value['pay_date']]=sprintf('%1.2f',$practical['2250'][$value['pay_date']]-$viewData['2250'][$value['pay_date']]);

			}
			foreach ($sale->channelTwoRefund($firstDay,$supFirstDay) as $key => $value) {
				//退货单量
				$refund['2800'][$value['pay_date']]=$value['count'];
				//退货金额
				$refund['2850'][$value['pay_date']]=$value['refund'];
			}
			return  array('reality'=>$practical,'complete'=>$complete,'gap'=>$gap,'refund'=>$refund);
		}
		function add($params){
			$sale= new SaleQuotaItemModel(27);
			$day=date('t',strtotime($params['export_time_start']));
			$str=substr($params['export_time_start'],0,7);
			$count=$sale->queryItemCount(array('time'=>$str,'channel'=>self::$gainParent));
			if($count[0]['count(*)']>0){
				exit("这个月已创建报表,不能再创建");
			}
			if(date('y-m',strtotime($str))<=date('y-m')){
	             exit("报表月份小于当前月份(如正是当前月请用导入)");
	        }

			$arrDays=[];
			for($i=1;$i<=$day;$i++){
				$element=trim($str.($i<10?"_0".$i:"_".$i));
				$arrDays[]=str_replace('-','_',$element);
			}

			$ParentData=$sale->gainParent(self::$gainParent,"'700','1500','2800','2250'");
		    $this->render('sale_plan_two_info.html',array(
		  	'ParentData'=>$ParentData,
		  	'datetime'=>$params['export_time_start'],
		  	'days'=>$arrDays,
		  	'method'=>'insert',
			));
		}

		function  insert($params){
			$result = array('success' => 0,'error' =>'','respone'=>'');
			$arr=$this->sort($params,self::$gainParent,'insert',$params['datetime']);
			$model = new  SaleQuotaItemModel(27);
			$returValue=$model->insert($arr);
			if($returValue==true){
				$result['success']=1;
				$result['respone']='添加成功';
			}else{
				$result['error']='添加失败';
			}
			Util::jsonExit($result);		
		}


		function  edit($params){
			$sale= new SaleQuotaItemModel(27);
			$day=date('t',strtotime($params['export_time_start']));
			$str=substr($params['export_time_start'],0,7);
			$count=$sale->queryItemCount(array('time'=>$str,'channel'=>self::$gainParent));
			if($count[0]['count(*)']==0){
				exit("没有添加这个月的数据,请进行添加");
			}

			
			$arrDays=[];
			for($i=1;$i<=$day;$i++){
				$element=trim($str.($i<10?"_0".$i:"_".$i));
				$arrDays[]=str_replace('-','_',$element);
			}

			$ParentData=$sale->gainParent(self::$gainParent,"'700','1500','2800','2250'");
			$arr=['cate_type1'=>self::$gainParent,'pdateTime'=>$str];
			$itemData=$sale->query($arr);
			
			//把数据库按照年月、渠道拿出来的数据按顺序拆分成二维数组
			$viewData=[];
			for($j=0;$j<count($itemData);$j++){
				$viewData[$itemData[$j]['item_id']][$itemData[$j]['pdate']]=$itemData[$j]['plan_value'];
			}
			/*echo '<pre>';
			print_r($viewData);exit;*/
		    $this->render('sale_plan_two_info.html',array(
			  	'ParentData'=>$ParentData,
			  	'datetime'=>$params['export_time_start'],
			  	'days'=>$arrDays,
			  	'view'=>$viewData,
			  	'method'=>'update',
			));
		}

		function update($params){
			$result = array('success' => 0,'error' =>'','respone'=>'');
			$updateArr=$this->sort($params,self::$gainParent,'update',$params['datetime']);
			$model = new  SaleQuotaItemModel(27);
			if(empty($updateArr)){
				$result['error']='修改失败';
				Util::jsonExit($result);
			}

			if(empty($params['datetime'])){
				$result['error']='修改失败';
				Util::jsonExit($result);
			}

			/*echo '<pre>';
			print_r($updateArr); exit;*/
			$check=$model->updateItem($updateArr);
			if($check){
				$result['success']=1;
				$result['respone']='修改成功';
			}else{
				$result['error']='修改失败';
			}
			Util::jsonExit($result);
			
			/*echo '<pre>';
			print_r($params); exit;*/
			
		}
        
		function importIndex($params){
		    $result = array("title"=>"销售计划导入");
		    $result['content'] = $this->fetch("sale_plan_import.html");
		    Util::jsonExit($result);
		}
		
		public function downloadCSV($params){
		    $result = array('success' => 0,'error' =>'');
		    $year_month=substr($params['export_time_start'],0,7);
		    $model = new  SaleQuotaItemModel(27);
		    /*$count=$model->queryItemCount(array('time'=>$year_month,'channel'=>self::$gainParent));
		     if($count[0]['count(*)']>0){
		     //exit("没有添加这个月的数据,请进行添加");
		     }*/
		    $ParentData=$model->gainParent(self::$gainParent,"'700','1500','2800'");
		    $days=date('t',strtotime($params['export_time_start']));
		    $root=array('渠道',' ',' ',' ','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');
		    $fileName = "销售计划报表.csv";
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename=' . $fileName);
		    $diff=31-$days;
		    //1.输出字段标题
		    $str = "";
		    for($i=0;$i<(count($root)-$diff);$i++){
		        $v = @iconv("UTF-8","GBK",$root[$i]);
		        $str .= $v.",";
		    }
		    $str = trim($str,",")."\r\n";
		    echo $str;
		    $child="";
		    $target="";
		    foreach($ParentData as $key=>$value){
		        $quota_name=@iconv("UTF-8","GBK",$value['quota_name']);
		        $cate_type1=@iconv("UTF-8","GBK",$value['cate_type1']);
		        $cate_type2=@iconv("UTF-8","GBK",$value['cate_type2']);
		        $target="计划";
		        if($value['id']=='2050' || $value['id']=='2350'){
		        	$target="实际";
		        }
		        $target=@iconv("UTF-8","GBK",$target);
		        echo $cate_type1.",".$cate_type2.",".$quota_name.",".$target."\r\n";
		    }
		}
		public function importCSV($params){
		    $result = array("error"=>"","success"=>"");
		    $month=substr(trim($params['time_start']),0,7);
		    $days=date("t",strtotime($params['time_start']));
		    $model = new  SaleQuotaItemModel(27);
		    $ParentData=$model->gainParent(self::$gainParent,"'700','1500','2800'");
		    if(empty($_FILES['file']['tmp_name'])){
		        $result['error'] = "请上传文件";
		        Util::jsonExit($result);
		    }else if(Upload::getExt($_FILES['file']['name']) != 'csv'){
		        $result['error'] = "请上传csv格式的文件";
		        Util::jsonExit($result);
		    }
		    if(date('y-m',strtotime($month))<date('y-m')){
		        $result['error'] = "报表月份小于当前月份";
		        Util::jsonExit($result);
		    }
		    
		    $count=$model->queryItemCount(array('time'=>$month,'channel'=>self::$gainParent));
		    if($count[0]['count(*)']>0){
		        exit("这个月已创建报表,不能再创建");
		    }
		    
		    $tmp_name = $_FILES['file']['tmp_name'];
		    $file = fopen($tmp_name, 'r');
		    $datalist = array();
		    $dateArray = array();
		    $i = 0;
		    while ($datav = fgetcsv($file)) {
		        $i++;
		        foreach ($datav as $k=>$v){
		            $datav[$k] = @iconv("GBK","UTF-8",$v);
		        }
		        if($i >= 2){
		            foreach ($datav as $k=>$v){
		                if(!is_numeric(trim($v))&& $k!=0 && $k!=1 && $k!=2 && $k!=3){
		                    if(trim($v)==""){
		                        $datav[$k]="0";
		                    }else{
		                        $result['error'] = "第{$i}行,请输入数字";
		                        Util::jsonExit($result);
		                    }
		                }
		            }
		            unset($datav[0],$datav[1],$datav[2],$datav[3]);
		            $dateArray[ $ParentData[$i-2]['id']]=$datav;
		        }
		    }
		    /* echo '<pre>';
		     var_dump($dateArray); exit;*/
		    if(!(count($ParentData)==count($dateArray))){
		        $result['error'] = "数据行丢失";
		        Util::jsonExit($result);
		    }
		    if($model->importCSV($ParentData,$dateArray,$month)){
		        $result['success']=1;
		        $result['respone']='添加成功';
		    }else{
		        $result['error']="增加失败";
		    }
		    Util::jsonExit($result);
		  
		}
		
		 //计算完成率
  	    private function computeCompleteWeek($plan,$practical,$id,$week_arrays){
  	    	$weekComplete=[];
  	    	if($id==2200){ //毛利率完成率
  	    		for($d=0;$d<count($week_arrays);$d++){
					$weekComplete[]=sprintf('%1.2f',$plan[$id][$d]==0?0:(($practical[$id][0][$d]/100)/($plan[$id][$d]/100)*100));
				}
  	    	}else{
  	    		for($d=0;$d<count($week_arrays);$d++){
					$weekComplete[]=sprintf('%1.2f',$plan[$id][$d]==0?0:($practical[$id][0][$d]/$plan[$id][$d]*100));
				}
  	    	}
			return $weekComplete;
  	    }
		
		private function parseWeekData(array $week,$data,$id,$year_month){
			$weekData=[];
			foreach ($week as $key => $value) {
				$week=0;
				for($j=$value['start'];$j<=$value['end'];$j++){
					$week+=empty($data[$id][$year_month.($j<10?'-0':'-').$j])?0:$data[$id][$year_month.($j<10?'-0':'-').$j];
				}
				$weekData[]=sprintf('%1.2f',$week);
			}
			return $weekData;
		}
        //用于累加 获取每周的数据
        private function weekCal($arrayData,$id,$week_arrays,$year){
            $monthCount=[];
            $weekCount=0;
            $childs=$arrayData[$id];
            $weeks=[];
            foreach ($week_arrays as $key => $value) {
                $week=0.00;
                for($j=$value['start'];$j<=$value['end'];$j++){
                    if($childs[$year.($j<10?'-0':'-').($j==0?1:$j)]>0){
                        $week+=$childs[$year.($j<10?'-0':'-').($j==0?1:$j)];
                        $weekCount++;
                    }
                }
                $monthCount[]=$weekCount;
                $weeks[]=sprintf('%1.2f',$week);
            }
            return array($weeks,$monthCount);
        }
		//根据表头进行筛选
		private function sort($arr,$gainParent,$method,$date){
			$model = new  SaleQuotaItemModel(27);
			$ParentData=$model->gainParent($gainParent,"'700','1500','2800','2250'");
			$data=$arr['datetime'];
			$day=date('t',strtotime($data));
			$str=str_replace('-','_',substr($data,0,7));
			$lement="day_";
			$value=[];
			$index=1;
			//修改要判断数据日期是否小于现在，并且指定数据下标
			/* if($method=='update'){
				$month=date('y-m',strtotime(substr(trim($date),0,7)));
				if($month==date('y-m')){
					$index=date("d");
				}else if($month<date('y-m')){
					$index=date('t',strtotime($month));
				}
			} */
			for($i=0;$i<count($ParentData);$i++){
				for($j=$index;$j<=$day;$j++){

					$value[]=array($ParentData[$i]['id'],$arr[trim($lement.$ParentData[$i]['id'].'_'.$str.($j<10?("_0".$j):("_".$j)))],$str.($j<10?"_0".$j:"_".$j));
				}	
			}
			return $value;
		}
	}
?>