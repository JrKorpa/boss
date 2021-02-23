<?php
	 /**
	* 销售计划报表管理器
	*/
	class SaleRefundController extends CommonController
	{
        private static $title=array(
            '200'=>array('渠道一部','退款金额','计划新增退款率'),
            '1200'=>array(  '渠道一部','新增退款单量','计划新增退单率'),
            '2200'=>array('天猫','退款金额','计划新增退款率'),
            '3200'=>array(  '天猫','新增退款单量','计划新增退单率'),
            '4200'=>array( '京东','退款金额','计划新增退款率'),
            '5200'=>array(  '京东','新增退款单量','计划新增退单率'),
        );
		//要想下载模板
		protected $whitelist = array('downloadCSV');

		function  index($params){
		  $this->render('sale_refund_search_form.html',array('bar'=>Auth::getBar()));
		}

		function search($params){
			$args=array(
			    'mod'	=> _Request::get("mod"),
			    'con'	=> substr(__CLASS__, 0, -10),
			    'act'	=> __FUNCTION__,
			    'export_time_start'=>_Request::get("export_time_start"),
			    'dep'=>_Request::get('dep[]'),
			);
            $month=substr($params['export_time_start'],0,7);
			$model= new SaleRefundItemModel(27);
            $firstDay= date("Y-m-d", strtotime($month."-01"));//这个月的第一天
            $supFirstDay=date('Y-m-d',strtotime('+1 months',strtotime($firstDay)));//下个月第一天
            $itemData=$model->query(" pdate='$firstDay'");
            if(count($itemData)==0){
                exit('搜索月份数据不存在');
            }
            $selTitle=array(
                '渠道一部'=>array(
                    '退款金额'=>array('100'=>'新增退款金额','200'=>'计划新增退款率','300'=>'实际新增退款率'),
                    '退款金额产品线占比'=>array('400'=>'黄金','500'=>'铂金','600'=>'K金','700'=>'钻石','800'=>'金条','900'=>'彩宝','1000'=>'其他'),
                    '新增退款单量'=>array('1100'=>'新增退款单量','1200'=>'计划新增退单率','1300'=>'实际新增退单率'),
                    '退款单量产品线占比'=>array('1400'=>'黄金','1500'=>'铂金','1600'=>'K金','1700'=>'钻石','1800'=>'金条','1900'=>'彩宝','2000'=>'其他')
                ),
                '天猫'=>array(
                    '退款金额'=>array('2100'=>'新增退款金额','2200'=>'计划新增退款率','2300'=>'实际新增退款率'),
                    '退款金额产品线占比'=>array('2400'=>'黄金','2500'=>'铂金','2600'=>'K金','2700'=>'钻石','2800'=>'金条','2900'=>'彩宝','3000'=>'其他'),
                    '新增退款单量'=>array('3100'=>'新增退款单量','3200'=>'计划新增退单率','3300'=>'实际新增退单率'),
                    '退款单量产品线占比'=>array('3400'=>'黄金','3500'=>'铂金','3600'=>'K金','3700'=>'钻石','3800'=>'金条','3900'=>'彩宝','4000'=>'其他')
                ),
                '京东'=>array(
                    '退款金额'=>array('4100'=>'新增退款金额','4200'=>'计划新增退款率','4300'=>'实际新增退款率'),
                    '退款金额产品线占比'=>array('4400'=>'黄金','4500'=>'铂金','4600'=>'K金','4700'=>'钻石','4800'=>'金条','4900'=>'彩宝','5000'=>'其他'),
                    '新增退款单量'=>array('5100'=>'新增退款单量','5200'=>'计划新增退单率','5300'=>'实际新增退单率'),
                    '退款单量产品线占比'=>array('5400'=>'黄金','5500'=>'铂金','5600'=>'K金','5700'=>'钻石','5800'=>'金条','5900'=>'彩宝','6000'=>'其他')
                ),
            );

            //获取周
            $array_week=$this->returnWeek($month);
            //计算退款数据
            $refundDate=$this->refundRecord($array_week,$firstDay,$supFirstDay,$itemData,$month,$model,$selTitle);
            $itemData=$this->refundDataSort($itemData,array(200,1200,2200,3200,4200,5200));
		    $this->render('sale_refund_search_list.html',array(
			    'title'=>$selTitle,
                'array_week'=>$array_week,
                'data'=>$refundDate,
                'itemdata'=>$itemData,
			));
		}

		function add($params){
		    $model = new SaleRefundItemModel(27);
            $month=substr($params['export_time_start'],0,7);
            $itemData=$model->query(" pdate='$month-01'");
            if(count($itemData)>0){
                exit('这个月的数据已添加,不能继续添加');
            }
            /*if(date('y-m',strtotime($month))<=date('y-m')){
                exit("报表月份小于当前月份(如正是当前月请用导入)");
            }*/
            //获取这个的周
            $week_array=$this->returnWeek($params['export_time_start']);
            $this->render('sale_refund_info.html',array(
                'title'=>self::$title,
                'week_array'=>$week_array,
                'date'=>str_replace('-','_',substr($params['export_time_start'],0,7)),

                'method'=>'insert',
            ));
		}

		function  insert($params){
			$result = array('success' => 0,'error' =>'','respone'=>'');
			$insertData=$this->sort($params,'insert',$params['datetime'],array_keys(self::$title));
            $model = new SaleRefundItemModel(27);
            $returnStatus=$model->insert($insertData);
			if($returnStatus){
				$result['success']=1;
				$result['respone']='添加成功';
			}else{
				$result['error']='添加失败';
			}
			Util::jsonExit($result);
		}


		function  edit($params){
            $model = new SaleRefundItemModel(27);
            $month=substr($params['export_time_start'],0,7);
            $itemData=$model->query(" pdate='$month-01'");
            $sortArray=[];
            if(count($itemData)==0){
                exit("没有添加这个月的数据,请进行添加");
            }
           /* if(date('y-m',strtotime($month))<date('y-m')){
                exit("修改数据小于的日期小于当前日期，不允许修改");
            }*/
            $week_array = $this->returnWeek($month);
            foreach (array_keys(self::$title) as $k => $v){
                foreach ($itemData as $key=>$value){
                   if($value['sup_id']==$v){
                       $sortArray[$v][$value['grade']]=$value;
                   }
                }
            }
		    $this->render('sale_refund_info.html',array(
                'title'=>self::$title,
                'week_array'=>$week_array,
                'date'=>str_replace('-','_',substr($params['export_time_start'],0,7)),
                'itemData'=>$sortArray,
                'method'=>'update',
			));
		}

		function update($params){
            $result = array('success' => 0,'error' =>'','respone'=>'');
            $insertData=$this->sort($params,'update',$params['datetime'],array_keys(self::$title));
            $model = new SaleRefundItemModel(27);
            $returnStatus=$model->updateItem($insertData);
			if($returnStatus){
				$result['success']=1;
				$result['respone']='修改成功';
			}else{
				$result['error']='修改失败';
			}
			Util::jsonExit($result);
		}
        
		function importIndex($params){
		    $result = array("title"=>"计划导入");
		    $result['content'] = $this->fetch("sale_refund_import.html");
		    Util::jsonExit($result);
		}
		//模板下载
		public function downloadCSV($params){
		    $root=array('渠道',' ','科目','月','1周','2周','3周','4周','5周','6周');
		    $fileName = "退款计划报表.csv";
		    header('Content-Type: application/octet-stream');
		    header('Content-Disposition: attachment; filename=' . $fileName);
		    $week_array=$this->returnWeek($params['export_time_start']);
		    $diff=6-count($week_array);
		    //1.输出字段标题
		    $str = "";
		    for($i=0;$i<(count($root)-$diff);$i++){
		        $v = @iconv("UTF-8","GBK",$root[$i]);
		        $str .= $v.",";
		    }
		    $str = trim($str,",")."\r\n";
		    echo $str;
		    foreach(self::$title as $key => $value){
                $section=@iconv("UTF-8","GBK",$value[0]);
                $clas=@iconv('UTF-8','GBK',$value[1]);
                $subj=@iconv('UTF-8','GBK',$value[2]);
                echo $section.",".$clas." ,".$subj."  "."\r\n";
            }
		}
		public function importCSV($params){
		    $result = array("error"=>"","success"=>"");
		    $month=substr(trim($params['time_start']),0,7);
		    $days=date("t",strtotime($params['time_start']));
		    $model= new SaleRefundItemModel(27);
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
		    $itemArray=$model->query(" pdate= '$month-01'");
		    if(count($itemArray)>0){
		        exit("这个月已创建报表,不能再创建");
		    }
            $parentData=array_keys(self::$title);
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
                    $array=array();
		            foreach ($datav as $k=>$v){
		                if(!is_numeric(trim($v))&& $k!=0 && $k!=1 && $k!=2){
		                    if(trim($v)==""){
                                //$datav[$k]="0";
                                $array[]="0";
                            }else{
                                $result['error'] = "第{$i}行{$k},请{$v}输入数字";
                                Util::jsonExit($result);
                            }
		                }else if($k!=0 && $k!=1 && $k!=2){
                            $array[] = $datav[$k];
                        }
		            }
		            $dateArray[$parentData[$i-2]]=$array;
		        }
		    }
		    if(!(count($parentData)==count($dateArray))){
		        $result['error'] = "数据行丢失";
		        Util::jsonExit($result);
		    }
		    if($model->importCSV($dateArray,$month)){
		        $result['success']=1;
		        $result['respone']='添加成功';
		    }else{
		        $result['error']="添加失败";
		    }
		    Util::jsonExit($result);
		  
		}

  	   //退款记录算法
        /**
         * @param $refundData
         * @param array $array_week
         * @param $viewData
         * @param $month
         */
        private function refundRecord(array $array_week,$start,$end, $viewData, $month,$model,$selTitle){
            $returnData=[];
            $TM=array_merge(array_keys($selTitle['天猫']['退款金额']),array_keys($selTitle['天猫']['退款金额产品线占比']),array_keys($selTitle['天猫']['新增退款单量']),array_keys($selTitle['天猫']['退款单量产品线占比']));
            $JD=array_merge(array_keys($selTitle['京东']['退款金额']),array_keys($selTitle['京东']['退款金额产品线占比']),array_keys($selTitle['京东']['新增退款单量']),array_keys($selTitle['京东']['退款单量产品线占比']));

            //天猫
            $tMArrayAmount=$this->datesort($model->TMDayMarkAmount($start,$end),'pay_date'); //新增
            $tMDepartmentTypes=$model->getRefundData($start,$end,2);//退款金额
            $tMOrderQuantity=$model->getRefundOrder($start,$end,2); //订单量
            $tMGoodsQuantity=$this->refundDateSort($model->getRefundGoods($start,$end,2),$array_week,$month); //同一产平线没有重复的货号
            $tMData=$this->channelData($array_week,$start,$end, $viewData, $month,$TM,$tMDepartmentTypes,$tMArrayAmount,$tMOrderQuantity,$tMGoodsQuantity);
            //京东
            $jDArrayAmount=$this->datesort($model->JDDayMarkAmount($start,$end),'pay_date');
            $jDDepartmentTypes=$model->getRefundData($start,$end,71);
            $jDOrderQuantity=$model->getRefundOrder($start,$end,71);
            $jDGoodsQuantity=$this->refundDateSort($model->getRefundGoods($start,$end,71),$array_week,$month);
            $jDData=$this->channelData($array_week,$start,$end, $viewData, $month,$JD,$jDDepartmentTypes,$jDArrayAmount,$jDOrderQuantity,$jDGoodsQuantity);
            //渠道一部
            $sumData=[];
            for($i=0;$i<count($array_week);$i++){
                //退款金额
                $sumData['week']['100'][]=sprintf('%1.2f',$tMData['week']['2100'][$i]+$jDData['week']['4100'][$i]);
                //实际新增退款率
                $order_amount=$tMData['order_amount'][$i]+$jDData['order_amount'][$i];
                $sumData['week']['300'][]=sprintf('%1.2f',$order_amount==0?0:$sumData['week']['100'][$i]/$order_amount*100);
                //产品
                $sumData['week']['400'][]=sprintf('%1.2f',$sumData['week']['100'][$i]==0?0:($tMGoodsQuantity[0]['wj'][$i]+$jDGoodsQuantity[0]['wj'][$i])/$sumData['week']['100'][$i]*100);
                $sumData['week']['500'][]=sprintf('%1.2f',$sumData['week']['100'][$i]==0?0:($tMGoodsQuantity[0]['bj'][$i]+$jDGoodsQuantity[0]['bj'][$i])/$sumData['week']['100'][$i]*100);
                $sumData['week']['600'][]=sprintf('%1.2f',$sumData['week']['100'][$i]==0?0:($tMGoodsQuantity[0]['kj'][$i]+$jDGoodsQuantity[0]['kj'][$i])/$sumData['week']['100'][$i]*100);
                $sumData['week']['700'][]=sprintf('%1.2f',$sumData['week']['100'][$i]==0?0:($tMGoodsQuantity[0]['zs'][$i]+$jDGoodsQuantity[0]['zs'][$i])/$sumData['week']['100'][$i]*100);
                $sumData['week']['800'][]=sprintf('%1.2f',$sumData['week']['100'][$i]==0?0:($tMGoodsQuantity[0]['jt'][$i]+$jDGoodsQuantity[0]['jt'][$i])/$sumData['week']['100'][$i]*100);
                $sumData['week']['900'][]=sprintf('%1.2f',$sumData['week']['100'][$i]==0?0:($tMGoodsQuantity[0]['cb'][$i]+$jDGoodsQuantity[0]['cb'][$i])/$sumData['week']['100'][$i]*100);
                $sumData['week']['1000'][]=sprintf('%1.2f',$sumData['week']['100'][$i]==0?0:($tMGoodsQuantity[0]['qt'][$i]+$jDGoodsQuantity[0]['qt'][$i])/$sumData['week']['100'][$i]*100);
                //退款单量
                $sumData['week']['1100'][]=$tMData['week']['3100'][$i]+$jDData['week']['5100'][$i];
                // 实际退款单率
                $sumCount=$tMData['count'][$i]+$jDData['count'][$i];
                $sumData['week']['1300'][]=sprintf('%1.2f',$sumCount==0?0:$sumData['week']['1100'][$i]/$sumCount*100);
                //产品 单量
                $sumData['week']['1400'][]=sprintf('%1.2f',$sumData['week']['1100'][$i]==0?0:($tMGoodsQuantity[1]['wj'][$i]+$jDGoodsQuantity[1]['wj'][$i])/$sumData['week']['1100'][$i]*100);
                $sumData['week']['1500'][]=sprintf('%1.2f',$sumData['week']['1100'][$i]==0?0:($tMGoodsQuantity[1]['bj'][$i]+$jDGoodsQuantity[1]['bj'][$i])/$sumData['week']['1100'][$i]*100);
                $sumData['week']['1600'][]=sprintf('%1.2f',$sumData['week']['1100'][$i]==0?0:($tMGoodsQuantity[1]['kj'][$i]+$jDGoodsQuantity[1]['kj'][$i])/$sumData['week']['1100'][$i]*100);
                $sumData['week']['1700'][]=sprintf('%1.2f',$sumData['week']['1100'][$i]==0?0:($tMGoodsQuantity[1]['zs'][$i]+$jDGoodsQuantity[1]['zs'][$i])/$sumData['week']['1100'][$i]*100);
                $sumData['week']['1800'][]=sprintf('%1.2f',$sumData['week']['1100'][$i]==0?0:($tMGoodsQuantity[1]['jt'][$i]+$jDGoodsQuantity[1]['jt'][$i])/$sumData['week']['1100'][$i]*100);
                $sumData['week']['1900'][]=sprintf('%1.2f',$sumData['week']['1100'][$i]==0?0:($tMGoodsQuantity[1]['cb'][$i]+$jDGoodsQuantity[1]['cb'][$i])/$sumData['week']['1100'][$i]*100);
                $sumData['week']['2000'][]=sprintf('%1.2f',$sumData['week']['1100'][$i]==0?0:($tMGoodsQuantity[1]['qt'][$i]+$jDGoodsQuantity[1]['qt'][$i])/$sumData['week']['1100'][$i]*100);
            }
            //月计算
            //退款金额
            $sumData['month']['100']=sprintf('%1.2f',$tMData['month']['2100']+$jDData['month']['4100']);
            //实际新增退款率
            $order_month_amount=array_sum($tMData['order_amount'])+array_sum($jDData['order_amount']);
            $sumData['month']['300']=sprintf('%1.2f',$order_month_amount==0?0:$sumData['month']['100']/$order_month_amount*100);
            //产品
            $sumData['month']['400']=sprintf('%1.2f',$sumData['month']['100']==0?0:(array_sum($tMGoodsQuantity[0]['wj'])+array_sum($jDGoodsQuantity[0]['wj']))/$sumData['month']['100']*100);
            $sumData['month']['500']=sprintf('%1.2f',$sumData['month']['100']==0?0:(array_sum($tMGoodsQuantity[0]['bj'])+array_sum($jDGoodsQuantity[0]['bj']))/$sumData['month']['100']*100);
            $sumData['month']['600']=sprintf('%1.2f',$sumData['month']['100']==0?0:(array_sum($tMGoodsQuantity[0]['kj'])+array_sum($jDGoodsQuantity[0]['kj']))/$sumData['month']['100']*100);
            $sumData['month']['700']=sprintf('%1.2f',$sumData['month']['100']==0?0:(array_sum($tMGoodsQuantity[0]['zs'])+array_sum($jDGoodsQuantity[0]['zs']))/$sumData['month']['100']*100);
            $sumData['month']['800']=sprintf('%1.2f',$sumData['month']['100']==0?0:(array_sum($tMGoodsQuantity[0]['jt'])+array_sum($jDGoodsQuantity[0]['jt']))/$sumData['month']['100']*100);
            $sumData['month']['900']=sprintf('%1.2f',$sumData['month']['100']==0?0:(array_sum($tMGoodsQuantity[0]['cb'])+array_sum($jDGoodsQuantity[0]['cb']))/$sumData['month']['100']*100);
            $sumData['month']['1000']=sprintf('%1.2f',$sumData['month']['100']==0?0:(array_sum($tMGoodsQuantity[0]['qt'])+array_sum($jDGoodsQuantity[0]['qt']))/$sumData['month']['100']*100);
            //退款单量
            $sumData['month']['1100']=$tMData['month']['3100']+$jDData['month']['5100'];
            // 实际退款单率
            $order_month_count=array_sum($tMData['count'])+array_sum($jDData['count']);
            $sumData['month']['1300']=sprintf('%1.2f',$order_month_count==0?0:$sumData['month']['1100']/$order_month_count*100);
            //产品 单量
            $sumData['month']['1400']=sprintf('%1.2f',$sumData['month']['1100']==0?0:(array_sum($tMGoodsQuantity[1]['wj'])+array_sum($jDGoodsQuantity[1]['wj']))/$sumData['month']['1100']*100);
            $sumData['month']['1500']=sprintf('%1.2f',$sumData['month']['1100']==0?0:(array_sum($tMGoodsQuantity[1]['bj'])+array_sum($jDGoodsQuantity[1]['bj']))/$sumData['month']['1100']*100);
            $sumData['month']['1600']=sprintf('%1.2f',$sumData['month']['1100']==0?0:(array_sum($tMGoodsQuantity[1]['kj'])+array_sum($jDGoodsQuantity[1]['kj']))/$sumData['month']['1100']*100);
            $sumData['month']['1700']=sprintf('%1.2f',$sumData['month']['1100']==0?0:(array_sum($tMGoodsQuantity[1]['zs'])+array_sum($jDGoodsQuantity[1]['zs']))/$sumData['month']['1100']*100);
            $sumData['month']['1800']=sprintf('%1.2f',$sumData['month']['1100']==0?0:(array_sum($tMGoodsQuantity[1]['jt'])+array_sum($jDGoodsQuantity[1]['jt']))/$sumData['month']['1100']*100);
            $sumData['month']['1900']=sprintf('%1.2f',$sumData['month']['1100']==0?0:(array_sum($tMGoodsQuantity[1]['cb'])+array_sum($jDGoodsQuantity[1]['cb']))/$sumData['month']['1100']*100);
            $sumData['month']['2000']=sprintf('%1.2f',$sumData['month']['1100']==0?0:(array_sum($tMGoodsQuantity[1]['qt'])+array_sum($jDGoodsQuantity[1]['qt']))/$sumData['month']['1100']*100);
            //给从数据库拿出来的数据进行排序
            foreach($viewData as $key => $value){
                if($value['grade']==0){
                    $sumData['month'][$value['sup_id']]=$value['value'];
                }else{
                    $sumData['week'][$value['sup_id']][]=$value['value'];
                }
            }
            return array('month'=>array($sumData['month'],$tMData['month'],$jDData['month']),'week'=>array($sumData['week'],$tMData['week'],$jDData['week']));
        }

        private function channelData(array $array_week,$start,$end, $viewData, $month,$itemId,$departmentTypes,$ArrayAmount,$orderQuantity,$goodsQuantity){
            $returnData=[];
            $channelAmount=[];
            foreach ($array_week as $key => $value){
                $weekOrder=0;
                $index=0;
                $amount=0.00;
                $count=0;
                $weekRefund=0.00;
                for ($i=$value['start'];$i<=$value['end'];$i++){
                    $date=$month.($i<10?'-0'.$i:'-'.$i);
                    //每周的退款金额
                    for($j=0;$j<count($departmentTypes);$j++){
                        if($departmentTypes[$j]['apply_time']==$date){
                            $weekRefund+=$departmentTypes[$j]['real_return_amount'];
                        }
                    }
                    //找出周的订单量
                    foreach ($orderQuantity as $k=>$v){
                        if($date==$v['apply_time']){
                            //计算退款的订单量
                            ++$index;
                        }
                    }
                    //天猫或京东新增金额
                    $amount+=empty($ArrayAmount[$date][0]['order_amount'])?0:$ArrayAmount[$date][0]['order_amount'];
                    //天猫或京东新增单量
                    $count+=empty($ArrayAmount[$date][0]['count'])?0:$ArrayAmount[$date][0]['count'];
                }
                //退款总金额
                $returnData['week'][$itemId[0]][]=$weekRefund;
                //订单量  周
                $returnData['week'][$itemId[10]][]=$index;
                //天猫 京东
                $channelAmount['order_amount'][]=$amount;
                //天猫 京东
                $channelAmount['count'][]=$count;
            }
            //$itemId[1] 为计划新增退款率
            //实际新增退款率 周
            for ($i=0;$i<count($array_week);$i++){
                $returnData['week'][$itemId[2]][]=sprintf('%1.2f',$channelAmount['order_amount'][$i]==0?0:$returnData['week'][$itemId[0]][$i]/$channelAmount['order_amount'][$i]*100);
            }

            //退款金额产品线占比 计算
            foreach($goodsQuantity[0] as $key=>$value){
                $amount=0.00;
                for($i=0;$i<count($array_week);$i++){
                    $amount=sprintf('%1.2f',$returnData['week'][$itemId[0]][$i]==0?0:$value[$i]/$returnData['week'][$itemId[0]][$i]*100);
                    if($key=='wj'){
                        $returnData['week'][$itemId[3]][]=$amount;
                    }else if($key=='bj'){
                        $returnData['week'][$itemId[4]][]=$amount;
                    }else if($key=='kj'){
                        $returnData['week'][$itemId[5]][]=$amount;
                    }else if($key=='zs'){
                        $returnData['week'][$itemId[6]][]=$amount;
                    }else if($key=='jt'){
                        $returnData['week'][$itemId[7]][]=$amount;
                    }else if($key=='cb'){
                        $returnData['week'][$itemId[8]][]=$amount;
                    }else if($key='qt'){
                        $returnData['week'][$itemId[9]][]=$amount;
                    }
                }
            }

            //  天猫、京东新增退款单量/天猫、京东新增订单量*100
            for($i=0;$i<count($array_week);$i++){
                $returnData['week'][$itemId[12]][]=sprintf('%1.2f',$channelAmount['count'][$i]==0?0:$returnData['week'][$itemId[10]][$i]/$channelAmount['count'][$i]*100);
            }
            //退款单量产品线占比
            foreach ($goodsQuantity[1] as $key =>$value){
                $amount=0.00;
                for($i=0;$i<count($array_week);$i++){
                    $amount=sprintf('%1.2f',$returnData['week'][$itemId[10]][$i]==0?0:$value[$i]/$returnData['week'][$itemId[10]][$i]*100);
                    if($key=='wj'){
                        $returnData['week'][$itemId[13]][]=$amount;
                    }else if($key=='bj'){
                        $returnData['week'][$itemId[14]][]=$amount;
                    }else if($key=='kj'){
                        $returnData['week'][$itemId[15]][]=$amount;
                    }else if($key=='zs'){
                        $returnData['week'][$itemId[16]][]=$amount;
                    }else if($key=='jt'){
                        $returnData['week'][$itemId[17]][]=$amount;
                    }else if($key=='cb'){
                        $returnData['week'][$itemId[18]][]=$amount;
                    }else if($key='qt'){
                        $returnData['week'][$itemId[19]][]=$amount;
                    }
                }
            }
            //月数据
            //计算退款金额的和
            $returnData['month'][$itemId[0]]=sprintf('%1.2f',array_sum($returnData['week'][$itemId[0]]));
            $returnMonthData=sprintf('%1.2f',array_sum($returnData['week'][$itemId[0]]));
            $monthChanelAmount=array_sum($channelAmount['order_amount']);
            //实际新增退款率
            $returnData['month'][$itemId[2]]=sprintf('%1.2f',$monthChanelAmount==0?0:$returnMonthData/$monthChanelAmount*100);
            //产品
            $index=3;
            foreach ($goodsQuantity[0] as $key =>$value){
                $returnData['month'][$itemId[$index]]=sprintf('%1.2f',$returnMonthData==0?0:array_sum($value)/$returnMonthData*100);
                $index++;
            }
            //新增退款单量  月
            $returnData['month'][$itemId[10]]=sprintf('%1.2f',array_sum($returnData['week'][$itemId[10]]));
            //实际新增退单率
            $returnData['month'][$itemId[12]]=sprintf('%1.2f', array_sum($channelAmount['count'])==0?0:$returnData['month'][$itemId[10]]/array_sum($channelAmount['count'])*100);
            //单量产品
            $index=13;
            foreach ($goodsQuantity[1] as $key =>$value){
                $returnData['month'][$itemId[$index]]=sprintf('%1.2f',$returnData['month'][$itemId[10]]==0?0:array_sum($value)/$returnData['month'][$itemId[10]]*100);
                $index++;
            }
            $returnData['order_amount']=$channelAmount['order_amount'];
            $returnData['count']=$channelAmount['count'];
            return $returnData;
        }

		//根据表头进行筛选
		private function sort($arr,$method,$date,$parentData){
			$data=$arr['datetime'];
			$day=date('t',strtotime($data));
			$str=str_replace('-','_',substr($data,0,7));
			$element="day_";
			$value=[];
			$index=1;
			$weeks=$this->returnWeek(str_replace('_','-',$data));
			for($i=0;$i<count($parentData);$i++){
			    //加1是因为有月的数据var_dump()
                for($j=$index;$j<=count($weeks)+1;$j++){
                    $value[]=array($parentData[$i],is_numeric($arr[trim($element.$str.'_'.$parentData[$i]."_".($j-1))])?$arr[trim($element.$str.'_'.$parentData[$i]."_".($j-1))]:0,$str."_01",$j-1);
                }
            }
            return $value;
		}

		//获取这个月的日期  return 这个月有几个星期
		protected function returnWeek($export_time_start){
            $firstDay= date("Y-m-d", strtotime(substr($export_time_start,0,7)."-01"));//这个月的第一天
            $supFirstDay=date('Y-m-d',strtotime('+1 months',strtotime($firstDay)));//下个月第一天
            $start_week=date('w',strtotime(substr($export_time_start,0,7)."-01"));//获取这个月的第一天是星期几
            $str=substr($export_time_start,0,7);//获取年和月
            $day=date('t',strtotime($export_time_start));//获取这一个月多少天
            $week_arrays=[];
            $start_week_end=[];//获取每周的开始结束时间
            //这个月的第一天不是星期一 就把1号赋给start，作为这个月的开始
            if($start_week!=1){
                $start_week_end['start']=1;
            }
            for($i=1;$i<=$day;$i++){
                $element2=$str.($i>=10?"-":"-0").$i;
                if (date('w',strtotime($element2))==1){
                    $start_week_end['start']=$i;
                }
                if(date('w',strtotime($element2))==0||$i==$day){
                    $start_week_end['end']=$i;
                    $week_arrays[]=$start_week_end;
                    $start_week_end=[];
                }
            }
            return $week_arrays;
        }
        //y用于数据库查询出来的数据进行排序
        private function datesort(array $date,$index){
		    $array=[];
		    foreach ($date as $key=>$value){
		        $array[$value[$index]][]=$value;
		    }
		    return $array;
        }
        //用于退款数据进行用品线分类

        /**
         * @param $data
         * @param $array_week
         * @param $month
         */
        private function refundDateSort($data, $array_week, $month){
            $array=[];
            $channelOrder=array('wj'=>array(),'bj'=>array(),'kj'=>array(),'zs'=>array(),'jt'=>array(),'cb'=>array(),'qt'=>array());
            foreach($array_week as $k=>$v){
                $weekAmount=['wj'=>0.00,'bj'=>0.00,'kj'=>0.00,'zs'=>0.00,'jt'=>0.00,'cb'=>0.00,'qt'=>0.00];
                $weekOrders=['wj'=>0.00,'bj'=>0.00,'kj'=>0.00,'zs'=>0.00,'jt'=>0.00,'cb'=>0.00,'qt'=>0.00];
                for($i=$v['start'];$i<=$v['end'];$i++){
                    foreach ($data as $key => $value){
                        $time=date('Y-m-d',strtotime($month.($i<10?'-0'.$i:'-'.$i)));
                        if($time==$value['apply_time']){
                            if($value['cat_type']=='定价黄金' || $value['cat_type']=='普通黄金'){
                                $weekAmount['wj']+=$value['real_return_amount'];
                                  if(!in_array($value['order_sn'],$channelOrder['wj'])){
                                      $weekOrders['wj']+=1;
                                      $channelOrder['wj'][]=$value['order_sn'];
                                  }
                            }elseif($value['cat_type']=='PT'){
                                $weekAmount['bj']+=$value['real_return_amount'];
                                    if(!in_array($value['oder_sn'],$channelOrder['bj'])){
                                        $weekOrders['bj']+=1;
                                        $channelOrder['bj'][]=$value['order_sn'];
                                    }
                            }else if($value['cat_type']=='K金'){
                                $weekAmount['kj']+=$value['real_return_amount'];
                                    if(!in_array($value['order_sn'],$channelOrder['kj'])){
                                        $weekOrders['kj']+=1;
                                        $channelOrder['kj'][]=$value['order_sn'];
                                    }
                            }else if($value['cat_type']=='钻石'){
                                $weekAmount['zs']+=$value['real_return_amount'];
                                    if(!in_array($value['order_sn'],$channelOrder['zs'])){
                                        $weekOrders['zs']+=1;
                                        $channelOrder['zs'][]=$value['order_sn'];
                                    }
                            }else if($value['cat_type']=='投资黄金'){
                                $weekAmount['jt']+=$value['real_return_amount'];
                                    if(!in_array($value['order_sn'],$channelOrder['jt'])){
                                        $weekOrders['jt']+=1;
                                        $channelOrder['jt'][]=$value['order_sn'];
                                    }
                            }else if($value['cat_type']=='宝石'){
                                $weekAmount['cb']+=$value['real_return_amount'];
                                    if(!in_array($value['order_sn'],$channelOrder['cb'])){
                                        $weekOrders['cb']+=1;
                                        $channelOrder['cb'][]=$value['order_sn'];
                                    }
                            }else{//其它产品类型
                                $weekAmount['qt']+=$value['real_return_amount'];
                                    if(!in_array($value['order_sn'],$channelOrder['qt'])){
                                        $weekOrders['qt']+=1;
                                        $channelOrder['qt'][]=$value['order_sn'];
                                    }
                            }
                        }
                    }
                }
                foreach ($weekAmount as $index => $item){
                    $array['amount'][$index][]=$item;
                }
                foreach ($weekOrders as $index => $item){
                    $array['order'][$index][]=$item;
                }
            }
            unset($channelOrder);
            return array( $array['amount'],$array['order']);
        }

        /**
         * @param array $array
         * 用于数据库查询退款报表进行规范排序
         */
        private function refundDataSort(array $array,array $sort){
            $returnValue=[];
            foreach($sort as $key =>$value){
                foreach ($array as $arIndex =>$arValue){
                    if($value==$arValue['sup_id']){
                       $returnValue[$value][$arValue['grade']]=$arValue['value'];
                    }
                }
            }
            return $returnValue;
        }
	}
?>