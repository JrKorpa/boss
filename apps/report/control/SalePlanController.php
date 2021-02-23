<?php
	 /**
	* 销售计划报表管理器
	*/
	class SalePlanController extends CommonController
	{
		private static 	$gainParent='渠道一部';
		//要想下载模板
		protected $whitelist = array('downloadCSV');
		
		function  index($params){
		  $this->render('sale_plan_search_form.html',array('bar'=>Auth::getBar()));
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
			$param="'0'";
			if(self::$gainParent=='渠道二部'){
				$param="'2800'";
			}
			$ParentData=$sale->gainParent(self::$gainParent,$param);
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
				if(self::$gainParent=='渠道一部'){
					//天猫毛利额计划
					$viewData['700'][$element2]=sprintf('%1.2f',($viewData['600'][$element2]/100)*($viewData['500'][$element2]));
					//京东毛利额计划
					$viewData['1500'][$element2]=sprintf('%1.2f',($viewData['1400'][$element2]/100)*$viewData['1300'][$element2]);
				}
			}

            //渠道一部
            $array=$this->channelOne($viewData,$firstDay,$supFirstDay,$sale);
            $practical=$array['reality'];
            $complete=$array['complete'];
            $orderNum=$array['orderNum'];
            $sel=$array['sel'];//自营的出库金额和出库金额-原始成本金额
            $gap=[];
            $refund=[];


			//获取周的数据
			for($i=0;$i<count($ParentData);$i++){
					$childs=$viewData[$ParentData[$i]['id']];
					/*foreach ($week_arrays as $key => $value) {
						$week=0.00;
						for($j=$value['start'];$j<=$value['end'];$j++){
							$week+=$childs[$str.($j<10?'-0':'-').($j==0?1:$j)];
						}

						$weekDatas[$ParentData[$i]['id']][]=sprintf('%1.2f',$week);
					}*/
                    //京东天猫转化率 计划值计算
                    if($ParentData[$i]['id']==300 || $ParentData[$i]['id']==1100){
                        $resultArray=$this->weekCal($viewData,$ParentData[$i]['id'],$week_arrays,$str);
                        for ($j=0;$j<count($week_arrays);$j++){
                            $weekDatas[$ParentData[$i]['id']][]=sprintf('%1.2f',empty($resultArray[1][$j])?0:($resultArray[1][$j]==0?0:$resultArray[0][$j]/$resultArray[1][$j]));
                        }
                        $month['sourceDB'][$ParentData[$i]['id']][]=sprintf('%1.2f',empty(array_sum($resultArray[1]))?0:(array_sum($resultArray[1])==0?0:array_sum($resultArray[0])/array_sum($resultArray[1])));
                    }else if($ParentData[$i]['id']==350 || $ParentData[$i]['id']==1150){
                        $resultArray=$this->weekCal($viewData,$ParentData[$i]['id'],$week_arrays,$str);
                        for ($j=0;$j<count($week_arrays);$j++){
                            $weekDatas[$ParentData[$i]['id']][]=sprintf('%1.2f',empty($resultArray[1][$j])?0:($resultArray[1][$j]==0?0:$resultArray[0][$j]/$resultArray[1][$j]));
                        }
                        $month['sourceDB'][$ParentData[$i]['id']][]=sprintf('%1.2f',empty(array_sum($resultArray[1]))?0:(array_sum($resultArray[1])==0?0:array_sum($resultArray[0])/array_sum($resultArray[1])));
                    }else if($ParentData[$i]['id']==400 || $ParentData[$i]['id']==1200){   //天猫  京东  新增客单价计算
                        $resultArray=$this->weekCal($viewData,$ParentData[$i]['id'],$week_arrays,$str);
                        //每周的总和
                        $weekData=[];
                        for ($j=0;$j<count($resultArray[0]);$j++){
                            $weekData[]=sprintf('%1.2f',empty($resultArray[1][$j])?0:($resultArray[1][$j]==0?0:$resultArray[0][$j]/$resultArray[1][$j]));
                        }
                        //周数据
                        $weekDatas[$ParentData[$i]['id']]=$weekData;
                        $monthCount=array_sum($resultArray[1]);
                        //获取从数据库读出来的的数据的月计（包括部分实际值）
                        $month['sourceDB'][$ParentData[$i]['id']][]=sprintf('%1.2f',$monthCount==0?0:array_sum($resultArray[0])/$monthCount);
                    }else if($ParentData[$i]['id']==600 || $ParentData[$i]['id']==1400){  //毛利率   京东 天猫毛利率计划周日计算
                        $earning=[];
                        $margin=[];
                        $incaAmount=0;
                        if($ParentData[$i]['id']==600){
                            $earning=$weekDatas[500];
                            $arr=$this->weekCal($viewData,700,$week_arrays,$str);
                            $margin=$arr[0];
                        }else{
                            $earning=$weekDatas[1300];
                            $arr=$this->weekCal($viewData,1500,$week_arrays,$str);
                            $margin=$arr[0];
                        }
                        //毛利率计划  周
                        for ($j=0;$j<count($week_arrays);$j++){
                            $weekDatas[$ParentData[$i]['id']][]=sprintf('%1.2f',empty($earning[$j])?0:($earning[$j]==0?0:$margin[$j]/$earning[$j]*100));
                        }
                        //毛利率计划  月
                        $month['sourceDB'][$ParentData[$i]['id']][]=sprintf('%1.2f',array_sum($earning)==0?0:array_sum($margin)/array_sum($earning)*100);
                    }else if($ParentData[$i]['id']==1800){ //自营毛利率
                        $resultArray=$this->weekCal($viewData,$ParentData[$i]['id'],$week_arrays,$str);
                        for ($j=0;$j<count($week_arrays);$j++){
                            $weekDatas[$ParentData[$i]['id']][]=sprintf('%1.2f',empty($resultArray[1][$j])?0:($resultArray[1][$j]==0?0:$resultArray[0][$j]/$resultArray[1][$j]));
                        }
                        $month['sourceDB'][$ParentData[$i]['id']][]=sprintf('%1.2f',empty(array_sum($resultArray[1]))?0:(array_sum($resultArray[1])==0?0:array_sum($resultArray[0])/array_sum($resultArray[1])));
                    }else{
                        $resultArray=$this->weekCal($viewData,$ParentData[$i]['id'],$week_arrays,$str);
                        $weekDatas[$ParentData[$i]['id']]=$resultArray[0];
                        $month['sourceDB'][$ParentData[$i]['id']][]=sprintf('%1.2f',array_sum($weekDatas[$ParentData[$i]['id']]));
                    }


                     //获取每周实际的数据
                    if($ParentData[$i]['id']==400){//天猫新增客单价 周
                        //计算订单数量
                        $weekOrder=$this->parseWeekData($week_arrays,$orderNum,100,$str,$viewData);
                        $newOrder=[];
                        for($j=0;$j<count($week_arrays);$j++){
                            $newOrder[]= sprintf('%1.2f',$weekOrder[$j]==0?0:($weeks[100][0][$j]/$weekOrder[$j]));
                        }
                        $weeks[$ParentData[$i]['id']][]=$newOrder;
                    }else if($ParentData[$i]['id']==1200){//京东新增客单价 周
                        //计算订单数量
                        $weekOrder=$this->parseWeekData($week_arrays,$orderNum,900,$str,$viewData);
                        $newOrder=[];
                        for($j=0;$j<count($week_arrays);$j++){
                            $newOrder[]= sprintf('%1.2f',$weekOrder[$j]==0?0:($weeks[900][0][$j]/$weekOrder[$j]));
                        }
                        $weeks[$ParentData[$i]['id']][]=$newOrder;
                    }else if($ParentData[$i]['id']==1800){//自营 毛利率实际  周
                        $weekMargin=$this->weekCal($sel['margin'],$ParentData[$i]['id'],$week_arrays,$str);
                        $weekEarning=$this->weekCal($sel['earning'],$ParentData[$i]['id'],$week_arrays,$str);
                        $newOrder=[];
                        for($j=0;$j<count($week_arrays);$j++){
                            $newOrder[]= sprintf('%1.2f',$weekEarning[0][$j]==0?0:$weekMargin[0][$j]/$weekEarning[0][$j]*100);
                        }
                        $weeks[$ParentData[$i]['id']][]=$newOrder;
                    }else{
                        $weeks[$ParentData[$i]['id']][]=$this->parseWeekData($week_arrays,$practical,$ParentData[$i]['id'],$str,$viewData);
                    }

					//获取每月的实际  算法出来的实际 
					if($ParentData[$i]['id']==600 ){
						//利润 一个月的利润
						$profit=array_sum($this->parseWeekData($week_arrays,$practical,700,$str));
						$earning=array_sum($weeks[500][0]);
						$month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',$earning==0?0:($profit/$earning*100));
					}elseif($ParentData[$i]['id']==1400 ){//京东毛利率 实际 月
						$profit=array_sum($this->parseWeekData($week_arrays,$practical,1500,$str));
						$earning=array_sum($weeks[1300][0]);
						$month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',$earning==0?0:($profit/$earning*100));
					}else if($ParentData[$i]['id']==1800 ){//自营毛利率实际 月
                        $weekMargin=$this->weekCal($sel['margin'],$ParentData[$i]['id'],$week_arrays,$str);
                        $weekEarning=$this->weekCal($sel['earning'],$ParentData[$i]['id'],$week_arrays,$str);
                        $month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',array_sum($weekEarning[0])==0?0:(array_sum($weekMargin[0])/array_sum($weekEarning[0])*100));
					}else if($ParentData[$i]['id']==400){//天猫实际  月
                        if(!empty($orderNum[100])){
                            $month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',array_sum($orderNum[100])==0?0:$month['practical'][100][0]/array_sum($orderNum[100]));
                        }else{
                            $month['practical'][$ParentData[$i]['id']][]=0;
                        }
                    }else if($ParentData[$i]['id']==1200){//京东实际  月
                        if(!empty($orderNum[900])){
                            $month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',array_sum($orderNum[900])==0?0:$month['practical'][900][0]/array_sum($orderNum[900]));
                        }else{
                            $month['practical'][$ParentData[$i]['id']][]=0;
                        }
                    }else{
						$month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',array_sum($weeks[$ParentData[$i]['id']][0]));
					}

					$index=0;
					if($ParentData[$i]['id']==250 || $ParentData[$i]['id']==1050 || $ParentData[$i]['id']==1650 || $ParentData[$i]['id']==350 || $ParentData[$i]['id']==1150){
						$index=	$ParentData[$i]['id']-50;
					}
					if($ParentData[$i]['id']==810 ){
						$index=	$ParentData[$i]['id']-10;
					}
					//计算周 完成率
					if($index!=0){
						$weekComplete=[];
						$ride=100;
			  	    	for($d=0;$d<count($week_arrays);$d++){
							$weekComplete[]=sprintf('%1.2f',$weekDatas[$index][$d]==0?0:($weekDatas[$ParentData[$i]['id']][$d]/$weekDatas[$index][$d]*$ride));
						}		
						$weeks[$ParentData[$i]['id']][]=$weekComplete;
						//完成率  月计
						$month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',$month['sourceDB'][$index][0]==0?0:($month['sourceDB'][$ParentData[$i]['id']][0]/$month['sourceDB'][$index][0]*$ride));
					}else{
						  	 $weeks[$ParentData[$i]['id']][]=$this->computeCompleteWeek($weekDatas,$weeks,$ParentData[$i]['id'],$week_arrays);
						  	 //完成率  月计
						  	  if($ParentData[$i]['id']==300 ||$ParentData[$i]['id']==600 || $ParentData[$i]['id']==1800  || $ParentData[$i]['id']==1100){
						  	 	 $month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',$month['sourceDB'][$ParentData[$i]['id']][0]==0?0.00:(($month['practical'][$ParentData[$i]['id']][0]/100)/($month['sourceDB'][$ParentData[$i]['id']][0]/100)*100));

						  	 }else{
						  	 	$month['practical'][$ParentData[$i]['id']][]=sprintf('%1.2f',$month['sourceDB'][$ParentData[$i]['id']][0]==0?0.00:($month['practical'][$ParentData[$i]['id']][0]/$month['sourceDB'][$ParentData[$i]['id']][0]*100));
						  	 }
					}
                    //渠道一部
					if(self::$gainParent=='渠道一部'){

						//计划
						$rootWeeks[$ParentData[$i]['id']][]=$this->parseWeekData($week_arrays,$array['channelDataCount']['planCount'],$ParentData[$i]['id'],$str);

						//实际
						$rootWeeks[$ParentData[$i]['id']][]=$this->parseWeekData($week_arrays,$array['channelDataCount']['practicalCount'],$ParentData[$i]['id'],$str);

						//完成率
						$weekComplet=[];
						for($d=0;$d<count($week_arrays);$d++){
							$weekComplet[]=sprintf('%1.2f',$rootWeeks[$ParentData[$i]['id']][0][$d]==0?0:($rootWeeks[$ParentData[$i]['id']][1][$d]/$rootWeeks[$ParentData[$i]['id']][0][$d]*100));
						}
						$rootWeeks[$ParentData[$i]['id']][]=$weekComplet;

						if($ParentData[$i]['id']==600){
							continue;
						}else{
							//计划 月
							$month['root'][$ParentData[$i]['id']][]=sprintf('%1.2f',array_sum($rootWeeks[$ParentData[$i]['id']][0]));
							//实际
							$month['root'][$ParentData[$i]['id']][]=sprintf('%1.2f',array_sum($rootWeeks[$ParentData[$i]['id']][1]));
							//完成率
							$month['root'][$ParentData[$i]['id']][]=sprintf('%1.2f',$month['root'][$ParentData[$i]['id']][0]==0?0.00:($month['root'][$ParentData[$i]['id']][1]/$month['root'][$ParentData[$i]['id']][0]*100));
						}

					}

			} 
			//渠道一部毛利率 计划   实际  完成率 月
			
			$month['root'][600][]=sprintf('%1.2f',$month['root'][500][0]==0?0.00:($month['root'][700][0]/$month['root'][500][0]*100));
			
			$month['root'][600][]=sprintf('%1.2f',$month['root'][500][1]==0?0.00:$month['root'][700][1]/$month['root'][500][1]*100);

			$month['root'][600][]=sprintf('%1.2f',$month['root'][600][0]==0?0.00:(($month['root'][600][1]/100)/($month['root'][600][0]/100))*100);

		    $this->render('sale_plan_search_list.html',array(
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
			  	'month'=>$month,
			));
		}
		//渠道一部的实际值和完成率
		function channelOne(array $viewData,$firstDay,$supFirstDay,$sale){
			$UVPractical=$viewData['250'];//获取天猫uv实际指标
			$UVPlan=$viewData['200'];//获取天猫uv实际指标
			$practical=[];
			$complete=[];
			$channelDataCount=[];
            $orderNum=[];
            $sel=[];
			//天猫 uv完成率
			foreach ($UVPlan as $key => $value) {
				$complete['250'][$key]=sprintf('%1.2f',$value==0?0:($UVPractical[$key]/$value)*100);
			}
			//费用完成率
			foreach ($viewData['800'] as $key => $value) {
				$complete['810'][$key]=sprintf('%1.2f',$value==0?0:($viewData['810'][$key]/$value)*100);
			}
			//转化率完成率
			foreach ($viewData['300'] as $key => $value) {
				$complete['350'][$key]=sprintf('%1.2f',$value==0?0:($viewData['350'][$key]/$value)*100);
			}

			//京东uv完成率
			foreach ($viewData['1000'] as $key => $value) {
				$complete['1050'][$key]=sprintf('%1.2f',$value==0?0:($viewData[1050][$key]/$value)*100);
			}

			//京东费用完成率
			foreach ($viewData['1600'] as $key => $value) {
				$complete['1650'][$key]=sprintf('%1.2f',$value==0?0:($viewData['1650'][$key]/$value)*100);
			}
			//转化率完成率
			foreach ($viewData['1100'] as $key => $value) {
				$complete['1150'][$key]=sprintf('%1.2f',$value==0?0:($viewData['1150'][$key]/$value)*100);
			}
		
			//获取天猫已审核未关闭状态且订单金额大于0的订单总金额的订单
			$TMMarkAmount=$sale->TMDayMarkAmount($firstDay,$supFirstDay);
			for($i=0;$i<count($TMMarkAmount);$i++){
				//新增实际
				$practical['100'][$TMMarkAmount[$i]['pay_date']]=sprintf('%1.2f',$TMMarkAmount[$i]['order_amount']);
				$orderNum['100'][$TMMarkAmount[$i]['pay_date']]=$TMMarkAmount[$i]['count'];//获取订单量
				//新增完成率
				if($viewData['100'][$TMMarkAmount[$i]['pay_date']]==0){
					$complete['100'][$TMMarkAmount[$i]['pay_date']]=0.00;
				}else{
					$complete['100'][$TMMarkAmount[$i]['pay_date']]=sprintf('%1.2f',$viewData['100'][$TMMarkAmount[$i]['pay_date']]==0?0:(($practical['100'][$TMMarkAmount[$i]['pay_date']]/$viewData['100'][$TMMarkAmount[$i]['pay_date']])*100));
				}

				//新增客单价 实际
				if($TMMarkAmount[$i]['count']==0){
						$practical['400'][$TMMarkAmount[$i]['pay_date']]=0.00;
				}else{
					   $practical['400'][$TMMarkAmount[$i]['pay_date']]=sprintf('%1.2f',$TMMarkAmount[$i]['count']==0?0:($practical['100'][$TMMarkAmount[$i]['pay_date']]/$TMMarkAmount[$i]['count']));
				}
				//新增客单价 完成率
				if($viewData['400'][$TMMarkAmount[$i]['pay_date']]==0){
					$complete['400'][$TMMarkAmount[$i]['pay_date']]=0.00;
				}else{
					 $complete['400'][$TMMarkAmount[$i]['pay_date']]=sprintf('%1.2f',$viewData['400'][$TMMarkAmount[$i]['pay_date']]==0?0:($practical['400'][$TMMarkAmount[$i]['pay_date']]/$viewData['400'][$TMMarkAmount[$i]['pay_date']])*100);
				}
			}

			$TMSDayMargin=$sale->TMSDayMargin($firstDay,$supFirstDay);
			foreach($TMSDayMargin as $key => $value) {
				//收入实际值
				$practical['500'][$value['pay_date']]=sprintf('%1.2f',$value['earning']);
				//收入完成率
				$complete['500'][$value['pay_date']]=sprintf('%1.2f',$viewData['500'][$value['pay_date']]==0?0:($practical['500'][$value['pay_date']]/$viewData['500'][$value['pay_date']])*100);//收入实际值
				//毛利率实际
				$practical['600'][$value['pay_date']]=sprintf('%1.2f',$value['earning']==0?0:($value['margin']/$value['earning'])*100);

				//毛利率完成率
				$complete['600'][$value['pay_date']]=sprintf('%1.2f',$viewData['600'][$value['pay_date']]==0?0:(($practical['600'][$value['pay_date']]/100)/($viewData['600'][$value['pay_date']]/100)*100));
				//毛利额实际
				$practical['700'][$value['pay_date']]=sprintf('%1.2f',$value['margin']);
				//毛利额完成率
				$complete['700'][$value['pay_date']]=sprintf('%1.2f',$viewData['700'][$value['pay_date']]==0?0:($practical['700'][$value['pay_date']]/$viewData['700'][$value['pay_date']])*100);//收入实际值
				
			}

			//京东数据
			$UVPractical=$viewData['1050'];//获取京东uv实际指标
			$JDDayMarkAmount=$sale->JDDayMarkAmount($firstDay,$supFirstDay);
			foreach ($JDDayMarkAmount as $key => $value) {
				//新增实际
				$practical['900'][$value['pay_date']]=sprintf('%1.2f',$value['order_amount']);
                $orderNum['900'][$value['pay_date']]=$value['count'];//获取订单量
				//新增完成率
				$complete['900'][$value['pay_date']]=sprintf('%1.2f',$viewData['900'][$value['pay_date']]==0?0:($practical['900'][$value['pay_date']]/$viewData['900'][$value['pay_date']])*100);
				//转化率实际
				/*$practical['1100'][$value['pay_date']]=sprintf('%1.2f',$value['count']==0?0:($UVPractical[$value['pay_date']]/$value['count']));*/
				//转化完成率
				/*$complete['1100'][$value['pay_date']]=sprintf('%1.2f',$viewData['1100'][$value['pay_date']]==0?0:(($practical['1100'][$value['pay_date']]/100)/($viewData['1100'][$value['pay_date']]/100))*100);*/

				//新增客单价实际
				$practical['1200'][$value['pay_date']]=sprintf('%1.2f',$value['count']==0?0:($value['order_amount']/$value['count']));

				//新增客单价完成率
				$complete['1200'][$value['pay_date']]=sprintf('%1.2f',$viewData['1200'][$value['pay_date']]==0?0:($practical['1200'][$value['pay_date']]/$viewData['1200'][$value['pay_date']])*100);
			}
			$JDSDayMargin=$sale->JDSDayMargin($firstDay,$supFirstDay);
			/* echo '<pre>';
			var_dump($JDSDayMargin);  exit; */
			foreach ($JDSDayMargin as $key => $value) {
				//收入实际值
				$practical['1300'][$value['pay_date']]=sprintf('%1.2f',$value['earning']);
				//收入完成率
				$complete['1300'][$value['pay_date']]=sprintf('%1.2f',$viewData['1300'][$value['pay_date']]==0?0:($practical['1300'][$value['pay_date']]/$viewData['1300'][$value['pay_date']])*100);//收入实际值
				//毛利率实际
				$practical['1400'][$value['pay_date']]=sprintf('%1.2f',$value['earning']==0?0:($value['margin']/$value['earning'])*100);
				//毛利率完成率
				$complete['1400'][$value['pay_date']]=sprintf('%1.2f',$viewData['1400'][$value['pay_date']]==0?0:(($practical['1400'][$value['pay_date']]/100)/($viewData['1400'][$value['pay_date']]/100)*100));
				//毛利额实际
				$practical['1500'][$value['pay_date']]=sprintf('%1.2f',$value['margin']);//毛利额实际
				//毛利额完成率
				$complete['1500'][$value['pay_date']]=sprintf('%1.2f',$viewData['1500'][$value['pay_date']]==0?0:($practical['1500'][$value['pay_date']]/$viewData['1500'][$value['pay_date']])*100);//收入实际值
			}

			//自营
			$aoturophy=$sale->aoturophy($firstDay,$supFirstDay);
			$margin=[];
			$earning=[];
			foreach ($aoturophy as $key => $value) {
				//出库实际金额
				$practical['1700'][$value['pay_date']]=sprintf('%1.2f',$value['earning']);
				//出库金额 完成率
				$complete['1700'][$value['pay_date']]=sprintf('%1.2f',$viewData['1700'][$value['pay_date']]==0?0:($practical['1700'][$value['pay_date']]/$viewData['1700'][$value['pay_date']])*100);//出库实际金额
				//毛利率
				$practical['1800'][$value['pay_date']]=sprintf('%1.2f',$value['earning']==0?0:($value['margin']/$value['earning'])*100);
                $margin[1800][$value['pay_date']]=$value['margin'];
                $earning[1800][$value['pay_date']]=$value['earning'];
			}

			$days=date('t',strtotime($firstDay));
			//渠道一部		
			$practicalCount=[];
			$planCount=[];
			$completeCount=[];

			for($i=1;$i<=$days;$i++){
				$day=substr($firstDay,0,7).($i<10?"-0".$i:"-".$i);
				/*echo '<pre>';
				print_r($practical); exit;*/
				//渠道一部统计新增计划
				$planCount['100'][$day]=sprintf('%1.2f',$viewData['100'][$day]+$viewData['900'][$day]);
				//实际
				$practicalCount['100'][$day]=sprintf('%1.2f',(isset($practical['100'][$day])?$practical['100'][$day]:0)+(isset($practical['900'][$day])?$practical['900'][$day]:0));

				//完成率
				/*$completeCount['100'][$day]=sprintf('%1.2f',empty(isset($planCount['100'][$day])?0:$channelDataCount['100'][$day])?0:((isset($planCount['100'][$day])?$practicalCount['100'][$day]:0)/$planCount['100'][$day]*100));*/
				$completeCount['100'][$day]=sprintf('%1.2f',$planCount['100'][$day]==0?0:($practicalCount['100'][$day]/$planCount['100'][$day]*100));
				//收入计划
				$planCount['500'][$day]=sprintf('%1.2f',$viewData['500'][$day]+$viewData['1300'][$day]);
				/*echo (isset($practical['500'][$day])?$practical['500'][$day]:0)+(isset($practical['1300'][$day])?$practical['1300'][$day]:0);*/
				//实际
				$practicalCount['500'][$day]=sprintf('%1.2f',(isset($practical['500'][$day])?$practical['500'][$day]:0)+(isset($practical['1300'][$day])?$practical['1300'][$day]:0));
				//完成率
				$completeCount['500'][$day]=sprintf('%1.2f',$planCount['500'][$day]==0?0:($practicalCount['500'][$day]/$planCount['500'][$day]*100));

				//毛利额计划
				$planCount['700'][$day]=sprintf('%1.2f',$viewData['700'][$day]+$viewData['1500'][$day]);
				//实际
				$practicalCount['700'][$day]=sprintf('%1.2f',(isset($practical['700'][$day])?$practical['700'][$day]:0)+(isset($practical['1500'][$day])?$practical['1500'][$day]:0));

				//完成率
				$completeCount['700'][$day]=sprintf('%1.2f',$planCount['700'][$day]==0?0:($practicalCount['700'][$day]/$planCount['700'][$day]*100));

				//毛利率计划
				$planCount['600'][$day]=sprintf('%1.2f',$planCount['500'][$day]==0?0:($planCount['700'][$day]/$planCount['500'][$day]*100));
				//实际
				$practicalCount['600'][$day]=sprintf('%1.2f',$practicalCount['500'][$day]==0?0:($practicalCount['700'][$day]/$practicalCount['500'][$day]*100));
				//完成率
				$completeCount['600'][$day]=sprintf('%1.2f',$planCount['600'][$day]==0?0:(($practicalCount['600'][$day]/100)/($planCount['600'][$day]/100)*100));

				//费用计划
				$planCount['800'][$day]=sprintf('%1.2f',$viewData['800'][$day]+$viewData['1600'][$day]);
				//实际
				$practicalCount['800'][$day]=sprintf('%1.2f',$viewData['810'][$day]+$viewData['1650'][$day]);
				//完成率
				$completeCount['800'][$day]=sprintf('%1.2f',$planCount['800'][$day]==0?0:($practicalCount['800'][$day]/$planCount['800'][$day]*100));/**/
			}	

			return  array('reality'=>$practical,'complete'=>$complete,'channelDataCount'=>array('planCount'=>$planCount,'practicalCount'=>$practicalCount,'completeCount'=>$completeCount),'orderNum'=>$orderNum,'sel'=>array('margin'=>$margin,'earning'=>$earning));
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

				//市场费用完成率
				$complete['2350'][$date]=sprintf('%1.2d',$viewData['2300'][$date]==0?0:($viewData['2350'][$date]/$viewData['2300'][$date])*100);
				//差值
				$gap['2350'][$date]=sprintf('%1.2f',$viewData['2350'][$date]-$viewData['2300'][$date]);
			}
			foreach ($sale->channelTwoDayMargin($firstDay,$supFirstDay) as $key => $value) {
				//零售新增实际
				$practical['1900'][$value['pay_date']]=sprintf('%1.2f',$value['order_amount']);
				//零售新增完成率
				$complete['1900'][$value['pay_date']]=sprintf('%1.2f',($practical['1900'][$value['pay_date']]/$viewData['1900'][$view['pay_date']])*100);
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

				//毛利率实际
				$practical['2200'][$value['pay_date']]=sprintf('%1.2f',(($practical['2100'][$value['pay_date']]-$value['margin'])/$practical['2100'][$value['pay_date']])*100);
				//毛利率完成率
				$complete['2200'][$value['pay_date']]=sprintf('%1.2f',$viewData['2200'][$value['pay_date']]==0?0:(($practical['2200'][$value['pay_date']]/100)/($viewData['2200'][$value['pay_date']]/100)*100));
				//毛利率差值
				$gap['2200'][$value['pay_date']]=sprintf('%1.2f',$practical['2200'][$value['pay_date']]-$viewData['2200'][$value['pay_date']]);

				//收入客单价实际
				$practical['2600'][$value['pay_date']]=sprintf('%1.2f',($practical['2100'][$value['pay_date']]/$value('count')));
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

			$ParentData=$sale->gainParent(self::$gainParent,"'700','1500','2800'");
		    $this->render('sale_plan_info.html',array(
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

			$ParentData=$sale->gainParent(self::$gainParent,"'700','1500','2800'");
			$arr=['cate_type1'=>self::$gainParent,'pdateTime'=>$str];
			$itemData=$sale->query($arr);
			
			//把数据库按照年月、渠道拿出来的数据按顺序拆分成二维数组
			$viewData=[];
			for($j=0;$j<count($itemData);$j++){
				$viewData[$itemData[$j]['item_id']][$itemData[$j]['pdate']]=$itemData[$j]['plan_value'];
			}
		    $this->render('sale_plan_info.html',array(
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

			$check=$model->updateItem($updateArr);
			if($check){
				$result['success']=1;
				$result['respone']='修改成功';
			}else{
				$result['error']='修改失败';
			}
			Util::jsonExit($result);
			
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
			$root=array('渠道','科目',' ',' ','1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31');
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
	        	if($value['id']=='250' || $value['id']=='810'|| $value['id']=='1050' || $value['id']=='1650' || $value['id']=='350' || $value['id']=='1150'){
	        		$target="实际";
	        	}
	        	$target=@iconv("UTF-8","GBK",$target);
	        	echo $cate_type1.",".$cate_type2.",".$quota_name.",".$target."\r\n";
	        }
  	    }    
  	    public function importCSV($params){
	    	$result = array("error"=>"","success"=>"");
	    	$month=substr(trim($params['time_start']),0,7);
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
      	  /* echo '<pre>';
      	   var_dump($ParentData); exit;*/
  	    }
  	    //计算完成率
  	    private function computeCompleteWeek(array $plan,array  $practical,$id,$week_arrays){
  	    	$weekComplete=[];
  	    	if($id==600 || $id==1800){
  	    		//毛利率的完成率计算
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
		private function parseWeekData(array $week,$data,$id,$year_month,$plan=null){
			$weekData=[];
			$sale=new SaleQuotaItemModel(27);

			if($id==600 ){
				//天猫毛利率实际 周
				$maoli=self::parseWeekData($week,$data,700,$year_month);
				$shouru=self::parseWeekData($week,$data,500,$year_month);
				for($i=0;$i<count($week);$i++){
					$weekData[]=sprintf('%1.2f',$shouru[$i]==0?0.00:($maoli[$i]/$shouru[$i]*100));
				}
			}else if($id==1400){
				//京东毛利率实际 周
				$maoli=self::parseWeekData($week,$data,1500,$year_month);
				$shouru=self::parseWeekData($week,$data,1300,$year_month);
				for($i=0;$i<count($week);$i++){
					$weekData[]=sprintf('%1.2f',$shouru[$i]==0?0.00:($maoli[$i]/$shouru[$i]*100));
				}
			}else{
				foreach ($week as $key => $value) {
					$week=0;
					for($j=$value['start'];$j<=$value['end'];$j++){
						$week+=empty($data[$id][$year_month.($j<10?'-0':'-').$j])?0:$data[$id][$year_month.($j<10?'-0':'-').$j];
					}
					$weekData[]=sprintf('%1.2f',$week);
				}
			}
			return $weekData;
		}

		//用于累加 获取每周的数据
        private function weekCal($arrayData,$id,$week_arrays,$year){
            $monthCount=[];
            $childs=$arrayData[$id];
            $weeks=[];
            foreach ($week_arrays as $key => $value) {
                $week=0.00;
                $weekCount=0;
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
			$ParentData=$model->gainParent($gainParent,"'700','1500','2800'");
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