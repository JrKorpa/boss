<?php
class AppPerformanceCountController extends CommonController {
        protected $smartyDebugEnabled = false;
		protected $types = array(0=>"全部",-1=>"其他",1=>"异业联盟",2=>"社区",3=>"BDD相关",4=>"团购",5=>"老顾客",6=>"数据",7=>"网络来源");
		protected $xiangqian = array(1=>'工厂配钻，工厂镶嵌',2=>'需工厂镶嵌',3=>'不需镶嵌',
									 4=>'客户先看钻再返厂镶嵌',5=>'镶嵌4c裸钻',6=>'镶嵌4c裸钻，客户先看钻',7=>'成品',8=>'半成品');
        public $limit_time = '2019-01-01 00:00:00';
        protected $whitelist = array('dow');
        public function index($params) {
            //录单类型
            $referer=array(1=>'婚博会',2=>'非婚博会');
			
            $this->getSourceList();
			$this->getCustomerSources();

            $model = new UserChannelModel(1);
            $data = $model->getChannels($_SESSION['userId'],0);
            if(empty($data)){
                die('请先联系管理员授权渠道!');
            }
            $myDepartment = array();
            foreach($data as $key => $val){
                $myDepartment[]=$val['id'];
            }

            $department = $myDepartment;

            $where['department'] = $department;
            
            if(is_array($where['department'])){
                $where['department']=implode(",",$where['department']);
            }
            $create_user=array();
            $create_user = $model->get_all_channels_by_channel_id($where['department']);
            
            foreach($create_user as $k=>$v){
                $dp_leader_name = explode(",",$v['dp_leader_name']);
                $dp_leader_name = array_filter($dp_leader_name);
                $dp_people_name = explode(",",$v['dp_people_name']);
                $dp_people_name = array_filter($dp_people_name);  
                $dp_name = array_merge($dp_leader_name,$dp_people_name);
                foreach($dp_name as $k=>$v){
                    $dp_leader_people_name[]=$v;
                }
            }
            $this->render('app_performance_count_search_form.html', array('bar' => Auth::getBar(),'referer'=>$referer,'sourcetypes'=>$this->types ));
        }
	
	
	
	//search.1  统计当期发货数量和金额
	public function getsendgoodsmondy($sendgoodsData = array())
	{
		$allsendgoodsmoney= 0 ;
		if(!empty($sendgoodsData))
		{
			foreach($sendgoodsData as $sendv)
			{
				//商品数量
				$gcount = $sendv['goods_count'];
				//商品价格
				$gprice = $sendv['goods_price'];
				//优惠状态，只有为3的时候 价格才需要减去
				$gfavorable_status = $sendv['favorable_status'];
				//优惠价格
				$gfavor_price = $sendv['favorable_price'];
				//判断优惠是否通过
				if($gfavorable_status == 3)
				{
					//优惠通过(价格等于商品价格减去优惠价)
					$plus = bcsub($gprice,$gfavor_price,2);
					$money = bcmul($gcount,$plus,2);
				}else{
					$money = bcmul($gcount,$gprice,2);
				}
				$allsendgoodsmoney = bcadd($allsendgoodsmoney,$money,2);
			}
		}
		return $allsendgoodsmoney;
	}
	
	//search.5 统计满足条件的订单的数据
	public function getorderarr($orderData = array(),$orderDataHuanGou =array())
	{
		
		$orderTongji = array();
		$orderTongji['order_num'] = 0;//订单总数
        $orderTongji['zp_suc_order_num'] = 0;//订单总数（赠品单）
		$orderTongji['order_sum_amount'] = 0;//订单总金额
		$orderTongji['order_goods_sum_amount'] = 0;//订单商品总金额
		$orderTongji['order_sum_paid'] = 0;//已付金额
		$orderTongji['order_sum_unpaid'] = 0;//未付金额
        $orderTongji['order_real_return_price'] = 0; //实退金额
        $orderTongji['goods_amount'] = 0;
		//订单之外的数据定义
		$cusinfo = array(); //定义一个数组用来容纳 同一个手机号 同一天的订单
        $zp_cusinfo = array();  //定义一个数组用来容纳 同一个手机号 同一天的订单(赠品)
		$hbinfo = array(); //定义一个数组用来容纳 需要合并的订单数据
		$orderTongji['suc_order_num'] = 0;//成单数
		$orderTongji['hb_order_num'] = 0 ;//(新增)合并的订单数
		
		$mobilearr = array();   //新增合并订单的手机号数组
		
		//开始循环订单数据
		foreach($orderData as $key => $order){
			$orderTongji['order_num']++;
			$orderTongji['order_sum_amount'] += $order['order_amount'];
            $orderTongji['order_goods_sum_amount'] += $order['goods_amount'] - $order['favorable_price'];
			$orderTongji['order_sum_paid'] += $order['money_paid'];
			$orderTongji['order_sum_unpaid'] += $order['money_unpaid'];
            $orderTongji['order_real_return_price'] += $order['real_return_price'];
			
			//同一个手机号 同月算一个订单 不含赠品单
            $createtime = substr($order['pay_date'],0,7);
            if($order['is_zp'] != '1'){
                $cusinfo[$createtime][] = $order['mobile'];
            }else{
                $zp_cusinfo[$createtime][] = $order['mobile'];
            }
			
			//增加合并订单(不含300一下订单统计) 不含赠品单
            //if($order['is_zp'] != '1' && $order['order_amount']> 300)
			//if($order['is_zp'] != '1' && $order['order_amount']> 300 && !in_array($order['mobile'],$mobilearr))
			//{
				//$orderTongji['hb_order_num']++;
				//array_push($mobilearr,$order['mobile']);
                //$hb_order_info[$createtime][] = $order['mobile'];
			//}
			
		}

        $hb_order_info = array();
        //循环换购订单
        foreach ($orderDataHuanGou as $key => $order) {
            //同一个手机号 同月算一个订单 不含赠品单
            $createtime = substr($order['pay_date'],0,7);
            //增加合并订单(不含300一下订单统计) 不含赠品单
            if($order['is_zp'] != '1')
            {
                $hb_order_info[$createtime][] = $order['mobile'];
            }
        }
        //tongji成交num 同一个手机号 同月算一个订单 不含赠品单
        if(!empty($cusinfo)){
            foreach ($cusinfo as $val) {
                $orderTongji['suc_order_num'] += count(array_flip($val));
            }
        }
        //tongji成交num 同一个手机号 同月算一个订单 赠品单
        if(!empty($zp_cusinfo)){
            foreach ($zp_cusinfo as $val) {
                $orderTongji['zp_suc_order_num'] += count(array_flip($val));
            }
        }
		//1根据搜索条件段内查询结果同一个手机号算一个订单（2）订单金额大于300元（不含300）
        //改1561 :同一个手机号 同月算一个订单
        if(!empty($hb_order_info)){
            foreach ($hb_order_info as $val) {
                $orderTongji['hb_order_num']+= count(array_flip($val));
            }
        }
		//$orderTongji['hb_order_num']+= count(array_flip($hbinfo));
		return $orderTongji;
	}
	
	//search.6 统计订单明细中,成品,裸钻的总数,总金额,和占比
	public function getpercent($orderdetails,$alltsydsn=array())
	{
		if(empty($orderdetails))
		{
			return false;
		}
		/*三、区分成品，裸石规则：①工厂配钻，工厂镶嵌；②需工厂镶嵌；③不需镶嵌；④客户先看钻再返厂镶嵌；⑤镶嵌4c裸钻；⑥镶嵌4c裸钻，客户先看钻；⑦成品；⑧半成品
			（1） 镶嵌要求：②④⑤⑥，钻石证书类型为EGL，金额=托+钻的金额，成品数量为1
			（2） 镶嵌要求：③，单独下了一个EGL裸钻，EGL裸钻算一个成品
			（3） 非（1）（2）两点并且主石重>=0.2，镶嵌要求不管是什么，裸石就是裸石，非裸石就是成品
			（4） 非（1）（2）两点并且主石重<0.2，镶嵌要求：②④⑤⑥，金额=托+钻的金额，成品数量为1
			（5） 非（1）（2）两点并且主石重<0.2，镶嵌要求：③，裸钻算成品
			（6） 非以上条件，镶嵌要求：①⑦⑧ 裸石是裸石，非裸石是成品*/
		$wone = array('需工厂镶嵌','客户先看钻再返厂镶嵌','镶嵌4c裸钻','镶嵌4c裸钻，客户先看钻');
		//$wtwo = array('工厂配钻','成品','半成品');
		$wthree = array('工厂配钻，工厂镶嵌','成品','半成品');
		$lznum=0;
		$flznum=0;
		$lzamount=0;
		$flzamount =0;
		$tsydnum = 0;
		$tsydamount = 0;
		$xynum =0;
		$xyamount=0;
		$allnum =0;
		$allamount=0;
		$allzs = array();
		
		//普通证书号
		//天生一对证书号
		$zhengshuhao_w1 = array();
		//条件4的证书号
		$zhengshuhao_w4 = array();
		//天生一对的证书号
		$zhengshuhao_tsyd = array();
		
		
		//条件一的统计
		$cp_w1_num = 0;
		$cp_w1_money=0;
		//条件二的统计
		$cp_w2_num = 0;
		$cp_w2_money=0;
		//条件三的统计
		$cp_w3_num = 0;
		$cp_w3_money=0;
		$lz_w3_num = 0;
		$lz_w3_money=0;
		//条件四的统计
		$cp_w4_num = 0;
		$cp_w4_money=0;
		//条件5的统计
		$cp_w5_num=0;
		$cp_w5_money=0;
		$lz_w5_num =0;
		$lz_w5_money=0;
		//条件6的统计
		$cp_w6_num=0;
		$cp_w6_money=0;
		$lz_w6_num = 0;
		$lz_w6_money=0;
		
		
		
		//天生一对各种条件统计
		$tsyd_w1_num=0;
		$tsyd_w1_money =0;
		$tsyd_w2_num=0;
		$tsyd_w2_money =0;
		$tsyd_w3_num=0;
		$tsyd_w3_money =0;
		//星耀统计
		$xynum =0;
		$xyamount=0;
		
		//print_r($orderdetails);
		//die();
		foreach($orderdetails as $k=>$obj)
		{
			$xq = $obj['xiangqian'];
			$num = $obj['goods_count'];
			$gtype = $obj['goods_type'];
			
			$cart = $obj['cart'];   //石重
			$cert = $obj['cert'];   //证书类型
			$zhengshuhao = trim($obj['zhengshuhao'])=='' ? '空' : $obj['zhengshuhao'];
			$goods_sn = $obj['goods_sn'];
			//是否已退
			$is_return = $obj['is_return'];
			if($is_return)
			{
				//continue;
			}
			
			//商品金额
			if($obj['favorable_status']==3)
			{
				$money = $obj['goods_count']*( $obj['goods_price']- $obj['favorable_price']);
			}else{
				$money = $obj['goods_count'] * $obj['goods_price'];	
			}
			//如果镶嵌要求为2,4,5,6并且证书类型为EGL,则为成品  //金额=托+钻的金额，成品数量为1 (钻石多少数量就多少)
			
			if(in_array($zhengshuhao,$zhengshuhao_w1))
			{
				$cp_w1_money += $money;
			}elseif(in_array($zhengshuhao,$zhengshuhao_w4))
			{
				$cp_w4_money+=$money;
			}elseif(in_array($xq,$wone) && ($cert == 'EGL' || $cert =='AGL'))
			{
				if(!in_array($zhengshuhao,$zhengshuhao_w1))
				{
					$cp_w1_num+= $num;
					$cp_w1_money += $money;
					if($zhengshuhao !='空')
					{
						array_push($zhengshuhao_w1,$zhengshuhao);
					}
				}
			}elseif(( $xq == '不需工厂镶嵌' ||  $xq == '不需镶嵌')  && ($cert == 'EGL' || $cert =='AGL')){
				$cp_w2_num+=$num;
				$cp_w2_money += $money;
			}elseif($cert !='EGL' && $cert != 'AGL' && $cart >= 0.2)
			{
				//条件三
				if( $gtype =='lz'){
					$lz_w3_num+= $num;
					$lz_w3_money += $money;	
				}else{
					$cp_w3_num+= $num;
					$cp_w3_money += $money;		
				}
			}elseif($cert !='EGL' && $cert != 'AGL' && $cart < 0.2){
				//条件四
				if(in_array($xq,$wone)){
					if(!in_array($zhengshuhao,$zhengshuhao_w4))
					{
						if($zhengshuhao !='空'){
							array_push($zhengshuhao_w4,$zhengshuhao);
						}
						$cp_w4_num += $num;
						$cp_w4_money+=$money;
					}
				}elseif($xq=='不需工厂镶嵌' || $xq=='不需镶嵌'){
					//条件五
					/*if($gtype == 'lz')
					{
						$lz_w5_num+=$num;
						$lz_w5_money+=$money;
					}else{
								
					}*/
					$cp_w5_num += $num;
					$cp_w5_money += $money;
				}elseif(in_array($xq,$wthree)){
					//条件六
					if($gtype !='lz')
					{
						$cp_w6_num += $num;
						$cp_w6_money += $money;	
					}else{
						$lz_w6_num += $num;
						$lz_w6_money += $money;	
					}	
				}
			}elseif(in_array($xq,$wthree))
			{
				//条件六
				if($gtype !='lz')
				{
					$cp_w6_num += $num;
					$cp_w6_money += $money;	
				}else{
					$lz_w6_num += $num;
					$lz_w6_money += $money;	
				}
			}
			
			
			//1和4  20分以上
			//2和5  20分一下
			//下面是天生一对的 //和星耀的
			
			
			//判断天生一对的托
			//如果在数组里面了,说明过来的是托  只判断证书号  因为表里面的有些数据证书类型是不对的
			/*if(in_array($zhengshuhao,$zhengshuhao_tsyd))
			{
				if($cart >= 0.2){
					if(in_array($goods_sn,$alltsydsn))
					{
						$tsyd_w1_num += $num;
						$tsyd_w1_money += $money;
					}	
				}else{
					$tsyd_w1_money += $money;
				}
				continue;
			}
			if($cert =='HRD-D')
			{
				if( $cart >= 0.2 )
				{
					if(!in_array($zhengshuhao,$zhengshuhao_tsyd))
					{
						$tsyd_w1_num += $num;
						$tsyd_w1_money += $money;
						if($zhengshuhao !='空')
						{
							array_push($zhengshuhao_tsyd,$zhengshuhao);
						}
					}
				}else{
					//如果是20分以下
					if(!in_array($zhengshuhao,$zhengshuhao_tsyd)){
						$tsyd_w2_num += $num;
						$tsyd_w2_money += $money;
						if($zhengshuhao !='空')
						{
							array_push($zhengshuhao_tsyd,$zhengshuhao);
						}
					}
				}	
			}else{
				//如果是单个的天生一对的托
				if($zhengshuhao == '空' && in_array($goods_sn,$alltsydsn))
				{
					$tsyd_w3_num += $num;
					$tsyd_w3_money += $money;	
				}
			}*/
            /*天生一对销售件数=数量1+数量2
            数量1=销售订单商品证书类型为“HRD-D”,商品类型非“lz”非“zp”并且钻石大小大于0的商品数量
            数量2=销售订单商品系列归属为“天生一对”并且钻石大小为0或者空的商品数量
            天生一对销售金额：金额1+金额2
            金额1=销售订单商品证书类型为“HRD-D”并且钻石大小大于0的商品成交价总和
            金额2=销售订单商品系列归属为“天生一对”并且钻石大小为0或者空的商品成交价总和*/
            //天生一对
            if($cert == 'HRD-D' && !in_array($gtype, array('lz','zp')) && $cart >0){
                $tsyd_w1_num += $num;
            }
            if($cert == 'HRD-D' && $cart >0){
                $tsyd_w1_money += $money;
            }

            //天生一对特殊款（不包含证书类型是HRD-D）
            //款号在销售政策管理-销售商品里的“天生一对特殊款”里存在算天生一对销售
            if($cert != 'HRD-D' && $this->checkTsydSpecial($goods_sn)){
                $tsyd_w3_num += $num;
                if($xq == '需工厂镶嵌' && !empty($zhengshuhao)){
                    $tsyd_w3_money += $money;
                    $tsyd_w3_money += $this->getTsydDiaPrice($zhengshuhao);
                }elseif($xq != '需工厂镶嵌'){
                    $tsyd_w3_money += $money;
                }
            }

            //款式归属为天生一对
            if(in_array($goods_sn,$alltsydsn) && $cart<=0 && !$this->checkTsydSpecial($goods_sn)){
                $tsyd_w2_num += $num;
                $tsyd_w2_money += $money;
            }

	
			//星耀统计
			if($cert == 'HRD-S' && $gtype == 'lz')
			{
				$xynum+=$num;
				$xyamount+=$money;
			}
			//总数

		}
		$lznum = $lz_w3_num+$lz_w5_num+$lz_w6_num;
		$lzamount = $lz_w3_money + $lz_w5_money+$lz_w6_money;
		
		
		$flznum = $cp_w1_num+$cp_w2_num+$cp_w3_num+$cp_w4_num+$cp_w5_num+$cp_w6_num;
		$flzamount = $cp_w1_money+$cp_w2_money+$cp_w3_money+$cp_w4_money+$cp_w5_money+$cp_w6_money;
		
		//$tsydnum = $tsyd_w1_num+$tsyd_w2_num+$tsyd_w3_num;
		//$tsydamount = $tsyd_w1_money+$tsyd_w2_money+$tsyd_w3_money;

        $tsydnum = $tsyd_w1_num+$tsyd_w2_num+$tsyd_w3_num;
        $tsydamount = $tsyd_w1_money+$tsyd_w2_money+$tsyd_w3_money;
		
		$allnum =$lznum+$flznum;
		$allamount = $lzamount+$flzamount;
		
		
		$returndata = array(
			'lsdata'=>array('num'=>$lznum,'amount'=>$lzamount),
			'flsdata'=>array('num'=>$flznum,'amount'=>$flzamount),
			'tsyddata'=>array('num'=>$tsydnum,'amount'=>$tsydamount),
			'xydata'=>array('num'=>$xynum,'amount'=>$xyamount)
		);
		
		//件数占比
		if($allnum > 0 )
		{
			$returndata['lsdata']['allnum'] = sprintf("%.2f",($lznum * 100 / $allnum)) . '%';
			$returndata['flsdata']['allnum'] = sprintf("%.2f",($flznum * 100 /$allnum)) . '%';
		}else{
			$returndata['lsdata']['allnum'] = 0;
			$returndata['flsdata']['allnum'] = 0;
		}
		//金额占比
		if($allamount > 0 )
		{
			$returndata['lsdata']['allmount'] = sprintf("%.2f",($lzamount * 100 / $allamount)) . '%';
			$returndata['flsdata']['allmount'] = sprintf("%.2f",($flzamount * 100 /$allamount)) . '%';
		}else{
			$returndata['lsdata']['allmount'] = 0;
			$returndata['flsdata']['allmount'] = 0;
		}
		return $returndata;
	}

	//update  by lly 20161216
    public function search($params) {
		$model = new UserChannelModel(1);
		$data = $model->getChannels($_SESSION['userId'],0);
		//查看当前用户都拥有哪些渠道的权限.
		if(empty($data)){
			die('请先联系管理员授权渠道!');
		}
		$start_time =  _Request::getString("start_time")?_Request::getString("start_time"):date("Y-m-d");
		$end_time = _Request::getString("end_time")?_Request::getString("end_time"):date("Y-m-d");
        if($start_time < $this->limit_time && SYS_SCOPE == 'zhanting'){
            $start_time = $this->limit_time;
        }
		$data_arr=$this->get_data_arr($start_time,$end_time);
        if(count($data_arr)>366){
			die('请查询366天范围内的信息!');
        }
		//获取当前用户选择的体验店
		$department = _Request::getInt('department');
		
		//1定义一个数组用来 容纳当前登录用户所拥有的销售渠道
		$myDepartment = array();
		foreach($data as $key => $val){
			$myDepartment[]=$val['id'];
		}
		
		//如果用户没有选择体验店的
		if(empty($department)){
			$department = $myDepartment;
            $where['department'] = $department;
		}else{
			//如果用户选择了体验店, 则再检查,所选择的销售渠道是否在被授予的权限渠道里面
			if(!in_array($department,$myDepartment)){
				//如果用户选择的销售渠道,而自己没有权限的话,那么则默认你没有选择哟(这种情况可以在源头筛选那里过滤掉)
				$department = $myDepartment;
			}
            $where['department'] = $department;
		}
		if(is_array($where['department'])){
            $where['department'] = implode(",",$where['department']);
        }
		//2定义一个数组用来容纳 该渠道都有哪些销售顾问
		$create_user=array();
        $create_user = $model->get_channels_person_by_channel_id($where['department']);
        $dp_people_name = explode(",",$create_user['dp_people_name']);  //销售顾问名称
        $dp_people_name = array_filter($dp_people_name);
		//如果选择了销售顾问
		$salsename = _Request::getList("salse") ?  _Request::getList("salse") : '';
		
		$usrModel = new UserModel(1);
		if(!empty($salsename))
		{
			$where['salse'] = $salsename;
			$uid = '';
			if(count($salsename)==1)
			{
				$uid = $usrModel->getAccountId($salsename[0]);
			}else{
				foreach($salsename as $v)	
				{
					$uid[] = $usrModel->getAccountId($v);
				}
			}
			//获取顾问的id 用作退款申请人那里
			$where['salseids'] = $uid;
		}
		/*
		段君说如果没有选择销售顾问 就去掉销售顾问的过滤
		else{
			$where['salse'] = $dp_people_name;
		}*/
		
		//3客户来源
		$CustomerSourcesModel = new CustomerSourcesModel(1);
		//获取客户来源分类
		$fenlei = _Request::getInt("fenlei") ? _Request::getInt("fenlei") : '';
		//获取客户来源id
		$from_ad = _Request::getString("from_ad");
		//如果没有选择客户来源,那就判断是否选择了来源分类.如果没有选择分类,则用体验店去筛选
		if(empty($from_ad))
		{
			//获取该分类下的所有客户来源id
			/*$allsourcedata = $CustomerSourcesModel->getalldata($where['department'],$fenlei);
			if(!empty($allsourcedata))
			{
				$from_ad = array_column($allsourcedata,'id');
				$where['from_ad'] = implode(",",$from_ad);
			}*/
			$where['from_ad'] = array();
		}else{
			$where['from_ad'] = $from_ad;
		}
		$is_zp = _Request::getString("is_zp");
		/*
		组装好搜索订单的条件(上面有销售渠道 $where['department'] ; 客户来源 $where['from_ad'] ; 制单人 $where['salse'] )*/
		$where['is_zp'] = $is_zp;
		$where['start_time'] = $start_time;
		$where['end_time'] = $end_time;
		$where['referer'] = _Request::getString("referer");
		//新增需求
		//罗湖店去掉京东业务销售数据（去掉订购类型：北京京东世纪贸易有限公司的订单）
		//罗湖店则默认为深圳地王大厦店  department_id = 9
		$payModel =new PaymentModel(1);
		$payid = $payModel->getIdbyName('北京京东世纪贸易有限公司');
		if( $where['department'] == '9')
		{
			$where['order_pay_type'] = $payid;
		}
		$model = new AppPerformanceCountModel(51);//只读数据库
		//<0>所有的订单信息
		$orderData= $model->GetOrderList($where);
        //不含300元以下商品
        $orderDataHuanGou = $model->GetOrderListHuanGou($where);
		if(empty($orderData)){
			die('未找到满足条件的订单信息!');
		}
		
		//获取所有天生的款号
		$alltsydsn = $model->getalltsydgoodssn();
		if(!empty($alltsydsn))
		{
			$alltsydsn = array_column($alltsydsn,'style_sn');
		}else{
			$alltsydsn=array();	
		}
		
		
		
		$lz_flz_return = array(
			'lsdata'=>array('num'=>0,'amount'=>0),
			'flsdata'=>array('num'=>0,'amount'=>0),
			'tsyddata'=>array('num'=>0,'amount'=>0),
			'xydata'=>array('num'=>0,'amount'=>0)
		);
		$ky_lz_flz_return = $lz_flz_return;
		$ng_lz_flz_return = $lz_flz_return;
		
		
		//<1>当期实退商品金额
		$returngoods = $model->getRetrunGoodsAmount($where);
		//<2>退款不退货总金额
        $returnprice = $model->getReturnPrice($where);
		//<#3>（先退款不退货 然后又退货的商品总金额）;
		
		//先获取return_goods表中的order_goods_id(订单明细自增id)
		$returngoods_orderids = $model->getRetrunGoodsOrderid($where);
		
		
		//跨月金额
		$plus_returnprice= 0 ;
		if(!empty($returngoods_orderids))
		{
			$oids = array_column($returngoods_orderids,'order_goods_id');
			//+跨月的(退款不退货的)总金额
			$plus_returnprice = $model->getReturnPrice($where,$oids);
			
			
			
			//下面是把退货和退款的分别计算在裸石和成品的
			
			//1.1:如果当期有退货的,那么计算出,当期这些退货的商品是属于裸石还是成品
			$returng_details = $model->getDetailsbyid($oids);
			
			//1.2:统计出退货的商品裸钻数量和成品数量以及金额
			$lz_flz_return = $this->checklzorflz($returng_details,$alltsydsn);
			//print_r($lz_flz_return);
			
			
			//3.1:获取当期退货，并且之前有退款不退货的记录明细
			$return_nog_details = $model->getNogoodsReturoids($where,$oids);
			if(!empty($return_nog_details))
			{
				$ngids = array_column($return_nog_details,'order_goods_id');
				$ng_return_details = $model->getDetailsbyid($ngids,2);
				//3.2:统计出退货的商品 之前有退款不退货的商品裸钻数量和成品数量以及金额
				$ky_lz_flz_return = $this->checklzorflz($ng_return_details,$alltsydsn);
				//print_r($ky_lz_flz_return);
			}
			
		}
		
		//2.1 统计当期退款不退货(不退商品的)的订单明细自增id
		$noreturngoods_orderids = $model->getRetrunGoodsOrderid($where,2);
		if(!empty($noreturngoods_orderids))
		{
			$nids = array_column($noreturngoods_orderids,'order_goods_id');
			//print_r($nids);die();
			
			//根据订单明细自增id获取出订单的明细
			$noreturng_details = $model->getDetailsbyid($nids,2);
			//print_r($noreturng_details);die();
			$ng_lz_flz_return = $this->checklzorflz($noreturng_details,$alltsydsn);
			//print_r($ng_lz_flz_return);
			//根据明细,把数据平摊到裸石和成品上面去,数量不做处理
		}
		
		
		//<3>订单商品明细
		$goodsData = $model->pageAllGoodsList($where);
		//<4>当期发货订单商品明细
		$sendgoodsData = $model->getSendgoodsPrice($where);
		
		
		// 4.1  统计当期发货数量和金额
		$allsendgoodsmoney = $this->getsendgoodsmondy($sendgoodsData);
        //BOSS-1708门店业绩统计区分黄金和非黄金
		$gold_info = $this->differentiateWhetherGold($goodsData,$sendgoodsData,$returng_details,$ng_return_details,$noreturng_details,$alltsydsn);
        //var_dump($gold_info);die;
		//5 统计订单的数据
		$orderTongji = $this->getorderarr($orderData,$orderDataHuanGou);
		$orderTongji['order_send_goods_sum_amount'] = $allsendgoodsmoney ; //当期发货订单商品总金额
		
		//总业绩
        $orderTongji['performance_count'] = $orderTongji['order_goods_sum_amount'] - $returngoods - $returnprice + $plus_returnprice;
		
		
		//下面是计算各自的数量,金额和占比
		$lsdata = array(
			'num'=>0,        //件数
			'amount'=>0,     //金额
			'allnum'=>0,	 //件数占比
			'allamount'=>0,  //金额占比
		);
		$flsdata = $lsdata;
		//下面是跟进订单的明细具体的算法咯   20161220
		$percentdata = $this->getpercent($goodsData,$alltsydsn);
		
		//返回裸石,成品的数据
		$percentdata = $this->getlsflsdata($percentdata,$lz_flz_return,$ng_lz_flz_return,$ky_lz_flz_return);
		
		
		
		//这里把裸石和成品的发生退货的也都区分开来
		//当期实退商品总额(裸石还是成品)
		
		
		//当期实退金额(退款不退货)
		
		
		//当期实退商品 在之前发生过退款不退货的记录总和
		
		
		
		
		
		
		/*****  下面继续  *****/
        $args = array(
            'mod' => _Request::get("mod"),
            'con' => substr(__CLASS__, 0, -10),
            'act' => __FUNCTION__,
            'department'=>_Request::getInt('department'),
            'salse' => _Request::getList("salse"),
            'from_ad' => _Request::getString("from_ad"),
            'is_zp' => _Request::getString("is_zp"),
            'start_time' => _Request::getString("start_time")?_Request::getString("start_time"):date("Y-m-d"),
            'end_time' => _Request::getString("end_time")?_Request::getString("end_time"):date("Y-m-d"),
            'referer' => _Request::getString("referer"),
			'fenlei' => _Request::getString("fenlei"),
        );
        $page = _Request::getInt("page", 1);
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'app_performance_count_search_page';
		//裸石销售件数，金额，件数占比（裸石件数/总销售件数），金额占比（裸石销售金额/总销售金额）
		//成品销售件数，金额，件数占比（成品件数/总销售件数），金额占比（成品销售金额/总销售金额）
		//成品含义：非裸钻的空托和成品
        $this->render('app_performance_count_search_list.html',
			array(
				'start_time' => $start_time,
				'end_time' => $end_time,
				'orderTongji' => $orderTongji,
				'orderData' => $orderData,
				'returngoods' => $returngoods,
				'plusreturnprice'=>$plus_returnprice,
				'returnprice' => $returnprice,
				'lsdata'=>$percentdata['lsdata'],
				'flsdata'=>$percentdata['flsdata'],
				'xydata'=>$percentdata['xydata'],
				'tsyddata'=>$percentdata['tsyddata'],
                'gold_info'=>$gold_info
			)
		);
    }

    //取销售顾问
    public function getCreateuser(){
        $result = array('success' => 0,'error' => '');
        $department= _Request::getInt('department');
		$model = new UserChannelModel(1);

        $data = $model->get_channels_person_by_channel_id($department);
		
        if($data['dp_people_name']=='' || $data['dp_people_name']==''){
            $data = $model->get_user_channel_by_channel_id($department);
        }else{
            //$dp_people = explode(",",$data['dp_people']);
            $dp_people_name = explode(",",$data['dp_people_name']);
            $dp_people_name = array_filter($dp_people_name);
            $dp_leader_name = explode(",",$data['dp_leader_name']);
            $dp_leader_name = array_filter($dp_leader_name);
            $data=array();
            $dp_people_me = in_array($_SESSION['userName'],$dp_people_name);
			
			if(in_array($_SESSION['userName'],$dp_leader_name))
			{
				$alluser = array_merge($dp_people_name,$dp_leader_name);
				foreach($alluser as $k=>$v)
				{
					$data[]['account']=$v;
				}
			}elseif($dp_people_me){
				$data[0]['account']=$_SESSION['userName'];	
			}
        }
        if(!empty($data)){
            $content='';
            foreach($data as $k=>$v){
                $content.='<option value="'.$v['account'].'">'.$v['account'].'</option>"';
            }
            $result['content']=$content;
        }

        Util::jsonExit($result);
    }

    public function getSourceList() {
        //渠道
		$model = new UserChannelModel(1);
		$data = $model->getChannels($_SESSION['userId'],0);
		if(empty($data)){
			die('请先联系管理员授权渠道!');
		}
		$this->assign('onlySale',0);//count($data)==1);
        $this->assign('sales_channels_idData', $data);
    }

    public function getCustomerSources() {
        //客户来源
        $CustomerSourcesModel = new CustomerSourcesModel(1);
        $CustomerSourcesList = $CustomerSourcesModel->getCustomerSourcesList("`id`,`source_name`");

        $this->assign('customer_source_list', $CustomerSourcesList);
    }

    /**
     *  取出指定时间数组
     */
    public function get_data_arr($start_time,$end_time){
		$start_time_str=explode("-",$start_time);
		$end_time_str=explode("-",$end_time);
		$data_arr=array();
		while(true){
			if($start_time_str[0].$start_time_str[1].$start_time_str[2]>$end_time_str[0].$end_time_str[1].$end_time_str[2]) break;
			$data_arr[$start_time_str[0]."-".$start_time_str[1]."-".$start_time_str[2]]=$start_time_str[1]."-".$start_time_str[2];
			$start_time_str[2]++;
			$start_time_str=explode("-",date("Y-m-d",mktime(0,0,0,$start_time_str[1],$start_time_str[2],$start_time_str[0])));
		}
		return $data_arr;
	}
	
	
	//search.6 根据订单明细，区分成品和裸钻 数量 以及金额
	public function checklzorflz($orderdetails,$alltsydsn=array())
	{
		if(empty($orderdetails))
		{
			return array(
				'lsdata'=>array('num'=>0,'amount'=>0),
				'flsdata'=>array('num'=>0,'amount'=>0),
				'tsyddata'=>array('num'=>0,'amount'=>0),
				'xydata'=>array('num'=>0,'amount'=>0)
			);
		}
		/*三、区分成品，裸石规则：①工厂配钻，工厂镶嵌；②需工厂镶嵌；③不需镶嵌；④客户先看钻再返厂镶嵌；⑤镶嵌4c裸钻；⑥镶嵌4c裸钻，客户先看钻；⑦成品；⑧半成品
			（1） 镶嵌要求：②④⑤⑥，钻石证书类型为EGL，金额=托+钻的金额，成品数量为1
			（2） 镶嵌要求：③，单独下了一个EGL裸钻，EGL裸钻算一个成品
			（3） 非（1）（2）两点并且主石重>=0.2，镶嵌要求不管是什么，裸石就是裸石，非裸石就是成品
			（4） 非（1）（2）两点并且主石重<0.2，镶嵌要求：②④⑤⑥，金额=托+钻的金额，成品数量为1
			（5） 非（1）（2）两点并且主石重<0.2，镶嵌要求：③，裸钻算成品
			（6） 非以上条件，镶嵌要求：①⑦⑧ 裸石是裸石，非裸石是成品*/
		$wone = array('需工厂镶嵌','客户先看钻再返厂镶嵌','镶嵌4c裸钻','镶嵌4c裸钻，客户先看钻');
		//$wtwo = array('工厂配钻','成品','半成品');
		$wthree = array('工厂配钻，工厂镶嵌','成品','半成品');
		$lznum=0;
		$flznum=0;
		$lzamount=0;
		$flzamount =0;
		$tsydnum = 0;
		$tsydamount = 0;
		$xynum =0;
		$xyamount=0;
		$allnum =0;
		$allamount=0;
		$allzs = array();
		
		//普通证书号
		//天生一对证书号
		$zhengshuhao_w1 = array();
		//条件4的证书号
		$zhengshuhao_w4 = array();
		//天生一对的证书号
		$zhengshuhao_tsyd = array();
		
		
		//条件一的统计
		$cp_w1_num = 0;
		$cp_w1_money=0;
		//条件二的统计
		$cp_w2_num = 0;
		$cp_w2_money=0;
		//条件三的统计
		$cp_w3_num = 0;
		$cp_w3_money=0;
		$lz_w3_num = 0;
		$lz_w3_money=0;
		//条件四的统计
		$cp_w4_num = 0;
		$cp_w4_money=0;
		//条件5的统计
		$cp_w5_num=0;
		$cp_w5_money=0;
		$lz_w5_num =0;
		$lz_w5_money=0;
		//条件6的统计
		$cp_w6_num=0;
		$cp_w6_money=0;
		$lz_w6_num = 0;
		$lz_w6_money=0;
		
		
		
		//天生一对各种条件统计
		$tsyd_w1_num=0;
		$tsyd_w1_money =0;
		$tsyd_w2_num=0;
		$tsyd_w2_money =0;
		$tsyd_w3_num=0;
		$tsyd_w3_money =0;
		//星耀统计
		$xynum =0;
		$xyamount=0;
		
		//print_r($orderdetails);
		//die();
		foreach($orderdetails as $k=>$obj)
		{
			$xq = $obj['xiangqian'];
			$num = $obj['goods_count'];
			$gtype = $obj['goods_type'];
			
			$cart = $obj['cart'];   //石重
			$cert = $obj['cert'];   //证书类型
			$zhengshuhao = trim($obj['zhengshuhao'])=='' ? '空' : $obj['zhengshuhao'];
			$goods_sn = $obj['goods_sn'];
			//商品金额
			if($obj['favorable_status']==3)
			{
				$money = $obj['goods_count']*( $obj['goods_price']- $obj['favorable_price']);
			}else{
				$money = $obj['goods_count'] * $obj['goods_price'];	
			}
			//如果镶嵌要求为2,4,5,6并且证书类型为EGL,则为成品  //金额=托+钻的金额，成品数量为1 (钻石多少数量就多少)
			
			if(in_array($zhengshuhao,$zhengshuhao_w1))
			{
				$cp_w1_money += $money;
			}elseif(in_array($zhengshuhao,$zhengshuhao_w4))
			{
				$cp_w4_money+=$money;
			}elseif(in_array($xq,$wone) && ($cert == 'EGL' || $cert =='AGL'))
			{
				if(!in_array($zhengshuhao,$zhengshuhao_w1))
				{
					$cp_w1_num+= $num;
					$cp_w1_money += $money;
					if($zhengshuhao != '空')
					{
						array_push($zhengshuhao_w1,$zhengshuhao);
					}
				}
			}elseif(( $xq == '不需工厂镶嵌' ||  $xq == '不需镶嵌')  && ($cert == 'EGL' || $cert =='AGL')){
				$cp_w2_num+=$num;
				$cp_w2_money += $money;
			}elseif($cert !='EGL' && $cert != 'AGL' && $cart >= 0.2)
			{
				//条件三
				if( $gtype =='lz'){
					$lz_w3_num+= $num;
					$lz_w3_money += $money;	
				}else{
					$cp_w3_num+= $num;
					$cp_w3_money += $money;		
				}
			}elseif($cert !='EGL' && $cert != 'AGL' && $cart < 0.2){
				//条件四
				if(in_array($xq,$wone)){
					if(!in_array($zhengshuhao,$zhengshuhao_w4))
					{
						if($zhengshuhao != '空')
						{
							array_push($zhengshuhao_w4,$zhengshuhao);
						}
						$cp_w4_num += $num;
						$cp_w4_money+=$money;
					}
				}elseif($xq=='不需工厂镶嵌' || $xq=='不需镶嵌'){
					//条件五
					/*if($gtype == 'lz')
					{
						$lz_w5_num+=$num;
						$lz_w5_money+=$money;
					}else{
								
					}*/
					$cp_w5_num += $num;
					$cp_w5_money += $money;
				}elseif(in_array($xq,$wthree)){
					//条件六
					if($gtype !='lz')
					{
						$cp_w6_num += $num;
						$cp_w6_money += $money;	
					}else{
						$lz_w6_num += $num;
						$lz_w6_money += $money;	
					}	
				}
			}elseif(in_array($xq,$wthree))
			{
				//条件六
				if($gtype !='lz')
				{
					$cp_w6_num += $num;
					$cp_w6_money += $money;	
				}else{
					$lz_w6_num += $num;
					$lz_w6_money += $money;	
				}
			}
			//1和4  20分以上
			//2和5  20分一下
			//下面是天生一对的 //和星耀的
			
			//判断天生一对的托
			//如果在数组里面了,说明过来的是托  只判断证书号  因为表里面的有些数据证书类型是不对的
			/*if(in_array($zhengshuhao,$zhengshuhao_tsyd))
			{
				if($cart >= 0.2){
					if(in_array($goods_sn,$alltsydsn))
					{
						$tsyd_w1_num += $num;
						$tsyd_w1_money += $money;
					}	
				}else{
					$tsyd_w1_money += $money;
				}
				continue;
			}
			
			if($cert =='HRD-D')
			{
				if( $cart >= 0.2 )
				{
					if(!in_array($zhengshuhao,$zhengshuhao_tsyd))
					{
						$tsyd_w1_num += $num;
						$tsyd_w1_money += $money;
						if($zhengshuhao != '空')
						{
							array_push($zhengshuhao_tsyd,$zhengshuhao);
						}
					}
				}else{
					//如果是20分以下
					if(!in_array($zhengshuhao,$zhengshuhao_tsyd)){
						$tsyd_w2_num += $num;
						$tsyd_w2_money += $money;
						if($zhengshuhao != '空'){
							array_push($zhengshuhao_tsyd,$zhengshuhao);
						}
					}
				}	
			}else{
				//如果是单个的天生一对的托
				if($zhengshuhao == '空' && in_array($goods_sn,$alltsydsn))
				{
					$tsyd_w3_num += $num;
					$tsyd_w3_money += $money;	
				}
			}*/

            /*天生一对销售件数=数量1+数量2
            数量1=销售订单商品证书类型为“HRD-D”,商品类型非“lz”非“zp”并且钻石大小大于0的商品数量
            数量2=销售订单商品系列归属为“天生一对”并且钻石大小为0或者空的商品数量
            天生一对销售金额：金额1+金额2
            金额1=销售订单商品证书类型为“HRD-D”并且钻石大小大于0的商品成交价总和
            金额2=销售订单商品系列归属为“天生一对”并且钻石大小为0或者空的商品成交价总和*/
            //天生一对
            if($cert == 'HRD-D' && !in_array($gtype, array('lz','zp')) && $cart >0){
                $tsyd_w1_num += $num;
            }
            if($cert == 'HRD-D' && $cart >0){
                $tsyd_w1_money += $money;
            }

            //天生一对特殊款（不包含证书类型是HRD-D）
            //款号在销售政策管理-销售商品里的“天生一对特殊款”里存在算天生一对销售
            if($cert != 'HRD-D' && $this->checkTsydSpecial($goods_sn)){
                $tsyd_w3_num += $num;
                if($xq == '需工厂镶嵌' && !empty($zhengshuhao)){
                    $tsyd_w3_money += $money;
                    $tsyd_w3_money += $this->getTsydDiaPrice($zhengshuhao);
                }elseif($xq != '需工厂镶嵌'){
                    $tsyd_w3_money += $money;
                }
            }

            //款式归属为天生一对
            if(in_array($goods_sn,$alltsydsn) && $cart<=0 && !$this->checkTsydSpecial($goods_sn)){
                $tsyd_w2_num += $num;
                $tsyd_w2_money += $money;
            }

            

    
            //星耀统计
            if($cert == 'HRD-S' && $gtype == 'lz')
            {
                $xynum+=$num;
                $xyamount+=$money;
            }
            //总数
	
			//星耀统计
			/*if($cert == 'HRD-S')
			{
				$xynum+=$num;
				$xyamount+=$money;
			}*/
			//总数
		}

		$lznum = $lz_w3_num+$lz_w5_num+$lz_w6_num;
		$lzamount = $lz_w3_money + $lz_w5_money+$lz_w6_money;

		$flznum = $cp_w1_num+$cp_w2_num+$cp_w3_num+$cp_w4_num+$cp_w5_num+$cp_w6_num;
		$flzamount = $cp_w1_money+$cp_w2_money+$cp_w3_money+$cp_w4_money+$cp_w5_money+$cp_w6_money;
		
		//$tsydnum = $tsyd_w1_num+$tsyd_w2_num+$tsyd_w3_num;
		//$tsydamount = $tsyd_w1_money+$tsyd_w2_money+$tsyd_w3_money;

        $tsydnum = $tsyd_w1_num+$tsyd_w2_num+$tsyd_w3_num;
        $tsydamount = $tsyd_w1_money+$tsyd_w2_money+$tsyd_w3_money;
		
		$allnum =$lznum+$flznum;
		$allamount = $lzamount+$flzamount;
		
		$returndata = array(
			'lsdata'=>array('num'=>$lznum,'amount'=>$lzamount),
			'flsdata'=>array('num'=>$flznum,'amount'=>$flzamount),
			'tsyddata'=>array('num'=>$tsydnum,'amount'=>$tsydamount),
			'xydata'=>array('num'=>$xynum,'amount'=>$xyamount)
		);
		return $returndata;
	}
	
	public function getlsflsdata($newdata,$stdata,$dqdata,$kydata)
	{
		/*print_r($newdata);
		echo '<br/>';
		print_r($stdata);
		echo '<br/>';
		print_r($dqdata);
		echo '<br/>';
		print_r($kydata);
		echo '<br/>';
		die();*/
		$lastdata = array();
		//裸石数量
		$lastdata['lsdata']['num'] =  $newdata['lsdata']['num'] - $stdata['lsdata']['num'];
		//裸石金额
		$lastdata['lsdata']['amount'] = round($newdata['lsdata']['amount'] - $stdata['lsdata']['amount'] - $dqdata['lsdata']['amount'] + $kydata['lsdata']['amount']);
		
		//成品数量
		$lastdata['flsdata']['num'] =  $newdata['flsdata']['num'] - $stdata['flsdata']['num'];
		//成品金额
		$lastdata['flsdata']['amount'] =  round($newdata['flsdata']['amount'] - $stdata['flsdata']['amount'] - $dqdata['flsdata']['amount'] + $kydata['flsdata']['amount']);
		
		//天生一对数量
		$lastdata['tsyddata']['num'] =  $newdata['tsyddata']['num'] - $stdata['tsyddata']['num'];
		//天生一对金额
		$lastdata['tsyddata']['amount'] =  round($newdata['tsyddata']['amount'] - $stdata['tsyddata']['amount'] - $dqdata['tsyddata']['amount'] + $kydata['tsyddata']['amount']);
		
		//星耀数量
		$lastdata['xydata']['num'] =  $newdata['xydata']['num'] - $stdata['xydata']['num'];
		//星耀金额
		$lastdata['xydata']['amount'] =  round($newdata['xydata']['amount'] - $stdata['xydata']['amount'] - $dqdata['xydata']['amount'] + $kydata['xydata']['amount']);
		
		$allnum  = $lastdata['lsdata']['num']+$lastdata['flsdata']['num'];
		$allamount = round($lastdata['lsdata']['amount']+$lastdata['flsdata']['amount']);
		
		//件数占比
		if($allnum > 0 )
		{
			$lastdata['lsdata']['allnum'] = sprintf("%.2f",($lastdata['lsdata']['num'] * 100 / $allnum)) . '%';
			$lastdata['flsdata']['allnum'] = sprintf("%.2f",($lastdata['flsdata']['num'] * 100 /$allnum)) . '%';
		}else{
			$lastdata['lsdata']['allnum'] = 0;
			$lastdata['flsdata']['allnum'] = 0;
		}
		//金额占比
		if($allamount > 0 )
		{
			$lastdata['lsdata']['allmount'] = sprintf("%.2f",($lastdata['lsdata']['amount'] * 100 / $allamount)) . '%';
			$lastdata['flsdata']['allmount'] = sprintf("%.2f",($lastdata['flsdata']['amount'] * 100 /$allamount)) . '%';
		}else{
			$lastdata['lsdata']['allmount'] = 0;
			$lastdata['flsdata']['allmount'] = 0;
		}
		return $lastdata;
	}
    //3、    业绩统计
    //天生一对销售件数：款号在销售政策管理-销售商品里的“天生一对特殊款”里存在算天生一对销售（不包含证书类型是HRD-D的数据）
    //天生一对销售金额：
    //①   款号在销售政策管理-销售商品里的“天生一对特殊款”里存在，此款的镶嵌要求如果是“需工厂镶嵌”并且证书号列有值，那么找到此钻石的销售记录，金额=托+钻的金额（不包含证书类型是HRD-D的数据）
    //②   款号在销售政策管理-销售商品里的“天生一对特殊款”里存在，此款的镶嵌要求如果非“需工厂镶嵌”，金额=托的金额（不包含证书类型是HRD-D的数据）
    private function checkTsydSpecial($style_sn)
    {
        $model = new AppPerformanceCountModel(51);//只读数据库
        $res = $model->getTsydSpecial($style_sn);
        return $res;
    }

    //根据证书号查询证书号商品的成交价
    private function getTsydDiaPrice($zhengshuhao){
        if(empty($zhengshuhao) || $zhengshuhao == '空') return 0;
        $model = new AppPerformanceCountModel(51);//只读数据库
        $res = $model->getTsydDiaPrice($zhengshuhao);
        if($res) return $res;
        return 0;
    }
    //根据当期订单明细统计订单总业绩
    //$returng_details 退商品
    //$ng_return_details 获取当期退货，并且之前有退款不退货的记录明细
    //$noreturng_details 退款不退货
    public function differentiateWhetherGold($goodsData,$sendgoodsData,$returng_details,$ng_return_details,$noreturng_details,$alltsydsn)
    {
        //var_dump($goodsData);die;
        //黄金商品总金额
        //非黄金商品总金额
        $gold_data = array(
            'gold_performance'=>0,
            'no_gold_performance'=>0,
            'gold_num'=>0,
            'no_gold_num'=>0,
            'gold_num_hg'=>0,
            'no_gold_num_hg'=>0,
            'gold_allsendgoodsmoney'=>0,
            'no_gold_allsendgoodsmoney'=>0,
            'gold_performance_total'=>0,
            'no_gold_performance_total'=>0,
            'gold_now_return_goods_total'=>0,
            'no_gold_now_return_goods_total'=>0,
            'gold_now_return_real_total'=>0,
            'no_gold_now_return_real_total'=>0,
            'no_gold_sale_cp_num'=>0,
            'no_gold_num_percentage'=>0
            );
        $no_gold_goods_info = array();//当期所有非黄金商品信息
        $gold_allsendgoodsmoney = 0;//黄金发货金额
        $no_gold_allsendgoodsmoney = 0;//非黄金发货金额
        $gold_info_num = array();//黄金成交客户数
        $no_gold_info_num = array();//非黄金成交客户数
        $gold_info_num_hg = array();//黄金成交客户数（不包含换购）
        $no_gold_info_num_hg = array();//非黄金成交客户数（不包含换购）
        $gold = array('普通黄金','定价黄金');
        if(!empty($goodsData)){
            foreach ($goodsData as $k => $goods) {
                $product_type1 = $goods['product_type1'];
                $product_type_name = $goods['product_type_name'];
                //商品金额
                if($goods['favorable_status']==3)
                {
                    //$money = $goods['goods_count']*( $goods['goods_price']- $goods['favorable_price']);
                    $money = bcmul($goods['goods_count'],bcsub($goods['goods_price'],$goods['favorable_price'],2),2);
                }else{
                    $money = bcmul($goods['goods_count'],$goods['goods_price'],2); 
                }

                //同一个手机号 同月算一个订单 不含赠品单
                $createtime = substr($goods['pay_date'],0,7);
                $is_hg = bccomp(300,$money,2);
                //var_dump($money,$is_hg);die;
                if(in_array($product_type1, $gold) || in_array($product_type_name, $gold)){
                    $gold_data['gold_performance'] += $money;

                    if($goods['is_zp'] != '1'){
                        $gold_info_num[$createtime][] = $goods['mobile'];
                    }

                    if($goods['is_zp'] != '1' && $is_hg != 1){
                        $gold_info_num_hg[$createtime][] = $goods['mobile'];
                    }
                }else{
                    $gold_data['no_gold_performance'] += $money;

                    if($goods['is_zp'] != '1'){
                        $no_gold_info_num[$createtime][] = $goods['mobile'];
                    }

                    if($goods['is_zp'] != '1' && $is_hg != 1){
                        $no_gold_info_num_hg[$createtime][] = $goods['mobile'];
                    }
                    //非黄金商品
                    $no_gold_goods_info[] = $goods;
                }
            }
            if(!empty($gold_info_num)){
                foreach ($gold_info_num as $val) {
                    $gold_data['gold_num'] += count(array_flip($val));
                }
            }
            if(!empty($no_gold_info_num)){
                foreach ($no_gold_info_num as $val) {
                    $gold_data['no_gold_num'] += count(array_flip($val));
                }
            }
            if(!empty($gold_info_num_hg)){
                foreach ($gold_info_num_hg as $val) {
                    $gold_data['gold_num_hg'] += count(array_flip($val));
                }
            }
            if(!empty($no_gold_info_num_hg)){
                foreach ($no_gold_info_num_hg as $val) {
                    $gold_data['no_gold_num_hg'] += count(array_flip($val));
                }
            }
        }
        //统计退货金额 分黄金和非黄金
        $allsendgoodsmoney_info = $this->checkGoldType($sendgoodsData,$gold);
        $gold_data['gold_allsendgoodsmoney'] = $allsendgoodsmoney_info['gold_return_price'];//黄金当期发货订单商品总金额(回款)
        $gold_data['no_gold_allsendgoodsmoney'] = $allsendgoodsmoney_info['no_gold_return_price'];//非黄金当期发货订单商品总金额(回款)
        //统计退货金额 分黄金和非黄金
        $return_goods_price = $this->checkGoldType($returng_details,$gold);
        //获取当期退货，并且之前有退款不退货的记录明细 分黄金和非黄金
        $ng_return_goods_price = $this->checkGoldType($ng_return_details,$gold);
        //退款不退货 分黄金和非黄金
        $noreturn_goods_price = $this->checkGoldType($noreturng_details,$gold);
        //黄金退款金额总计
        $return_data_total = bcadd(bcadd($return_goods_price['gold_return_price'],$ng_return_goods_price['gold_return_price'],2),$noreturn_goods_price['gold_return_price'],2);
        //非黄金退款金额总计
        $no_return_data_total = bcadd(bcadd($return_goods_price['no_gold_return_price'],$ng_return_goods_price['no_gold_return_price'],2),$noreturn_goods_price['no_gold_return_price'],2);
        //黄金总业绩
        $gold_data['gold_performance_total'] = bcsub($gold_data['gold_performance'],$return_data_total,2);
        //非黄金总业绩
        $gold_data['no_gold_performance_total'] = bcsub($gold_data['no_gold_performance'],$no_return_data_total,2);
        //黄金当期实退货商品总金额
        $gold_data['gold_now_return_goods_total'] = bcadd($return_goods_price['gold_return_price'],$ng_return_goods_price['gold_return_price'],2);
        //非黄金当期实退货商品总金额
        $gold_data['no_gold_now_return_goods_total'] = bcadd($return_goods_price['no_gold_return_price'],$ng_return_goods_price['no_gold_return_price'],2);
        //黄金当期实退金额（退款不退货）
        $gold_data['gold_now_return_real_total'] = $noreturn_goods_price['gold_return_price'];
        //非黄金当期实退金额（退款不退货）
        $gold_data['no_gold_now_return_real_total'] = $noreturn_goods_price['no_gold_return_price'];

        //非黄金成品销售件数
        $no_gold_goods_info_num = $this->getpercent($no_gold_goods_info,$alltsydsn);
        //非黄金成品销售件数 退款
        //var_dump($return_goods_price['no_gold_goods']);die;
        $no_gold_goods_info_return_num = $this->checklzorflz($return_goods_price['no_gold_goods'],$alltsydsn);
        //var_dump($no_gold_goods_info_return_num);die;
        //var_dump($no_gold_goods_info_num,$no_gold_goods_info_return_num);die;
        //非黄金成品销售件数
        $gold_data['no_gold_sale_cp_num'] = bcsub($no_gold_goods_info_num['flsdata']['num'],$no_gold_goods_info_return_num['flsdata']['num']);
        //非黄金裸石销售件数
        $no_gold_sale_ls_num = bcsub($no_gold_goods_info_num['lsdata']['num'],$no_gold_goods_info_return_num['lsdata']['num']);
        //非黄金成品销售件数+裸石销售件数
        $no_gold_cp_ls_sale_num = bcadd($gold_data['no_gold_sale_cp_num'],$no_gold_sale_ls_num,2);
        //非黄金件数占比（非黄金件数占比=非黄金成品销售件数/（非黄金成品销售件数+裸石销售件数））
        //$gold_data['no_gold_num_percentage'] = ($gold_data['no_gold_sale_cp_num']/$no_gold_cp_ls_sale_num)*100;
        $gold_data['no_gold_num_percentage'] = bcmul(bcdiv($gold_data['no_gold_sale_cp_num'],$no_gold_cp_ls_sale_num,4),100,2);
        return $gold_data;
    }

    //区分是否黄金和非黄金 的退款金额
    public function checkGoldType($details_return,$gold)
    {
        $return_data = array();
        $return_data['gold_return_price'] = 0;
        $return_data['no_gold_return_price'] = 0;
        $return_data['no_gold_goods'] = array();//非黄金退款货品
        if(!empty($details_return)){
            foreach ($details_return as $goods) {
                //商品金额
                if($goods['favorable_status']==3)
                {
                    //$money = $goods['goods_count']*( $goods['goods_price']- $goods['favorable_price']);
                    $money = bcmul($goods['goods_count'],bcsub($goods['goods_price'],$goods['favorable_price'],2),2);
                }else{
                    $money = bcmul($goods['goods_count'],$goods['goods_price'],2); 
                }

                if(in_array($goods['product_type1'], $gold) || in_array($goods['product_type_name'], $gold)){
                    $return_data['gold_return_price'] = bcadd($return_data['gold_return_price'],$money,2);
                }else{
                    $return_data['no_gold_return_price'] = bcadd($return_data['no_gold_return_price'],$money,2);
                    $return_data['no_gold_goods'][] = $goods;
                }
            }
        }
        return $return_data;
    }

    //导出排行榜数据
    public function dow($params){
 
        set_time_limit(0);
        header("Content-Type: text/html; charset=gb2312");
        header("Content-type:aplication/vnd.ms-excel");
        header("Content-Disposition:filename=".iconv('utf-8','gb2312','导出销售排行榜').time().".xls");

        $userModel1 = new UserModel(1);
        $userModel111 = new UserModel(111);
        $date_start = $params['start_time'];
        $date_end = $params['end_time'];
        if(empty($date_start) || empty($date_end))
        	exit('起始时间和结束时间不能为空');

        //天生一对有销售记录名单
        $tsyd_saler_list=array();
        $xy_saler_list=array();
        $xxbl_saler_list=array();
        $sys1 = '';
        $sys111='';
        if(SYS_SCOPE=='boss'){
	        $sys1 = 'boss';
	        $sys111='zhanting';             
        }
        if(SYS_SCOPE=='zhanting'){
	        $sys1 = 'zhanting';
	        $sys111='boss';             
        }

        //获取各店销售业绩第一名
        $res1 = $userModel1->get_sale_goods_amount_top($date_start,$date_end,$sys1);
        $res111 = $userModel111->get_sale_goods_amount_top($date_start,$date_end,$sys111);
        $goods_amount_list=array_merge($res1,$res111);
        $key_list=array();
        //echo "<pre>";print_r($res1);
        foreach ($goods_amount_list as $key => $v) {
        	$key_list[$key] = $v['goods_amount'];
        }
        array_multisort($key_list,SORT_DESC,$goods_amount_list);

        //统计天生一对排行
        $res1   = $userModel1->get_sale_goods_tsyd($date_start,$date_end,$sys1);
        $res111 = $userModel111->get_sale_goods_tsyd($date_start,$date_end,$sys111);
        $res_list = array_merge($res1,$res111);
        $res_list = array_filter($res_list, function($item){ 
                 return $item['allnum'] >= 2; 
            });        
        $key_list = array();
        //echo "<pre>";print_r($res1);
        $limit_list=array();
        $tsyd_saler_list=array();
        if(!empty($res_list)){
	        foreach ($res_list as $key => $v) {
	        	$key_list[$key] = $v['allnum'];
	        }
	        array_multisort($key_list,SORT_DESC,$res_list);
	        $limit_list = array();
	        for($i=0;$i<count($res_list);$i++) {
	        	if($i<9){
	        		$limit_list[] = $res_list[$i];
	        	}else{
	        		if($i==9){
	        			$limit_list[] = $res_list[$i];
	        			$tmp = $res_list[$i]['allnum'];
	        		}else{        			
	                    if($res_list[$i]['allnum']==$tmp)
	                    	$limit_list[] = $res_list[$i];
	        		}
	        	}
	        	if($res_list[$i]['allnum']>0)
	        		$tsyd_saler_list[]=$res_list[$i]['create_user'];
	        }
        }  
        $tsyd_list = $limit_list;
 

        //统计星耀排行
        $res1   = $userModel1->get_sale_goods_xy($date_start,$date_end,$sys1);
        $res111 = $userModel111->get_sale_goods_xy($date_start,$date_end,$sys111);
        $res_list = array_merge($res1,$res111);
        $res_list = array_filter($res_list, function($item){ 
                 return $item['allnum'] >= 2; 
            });        
        $key_list = array();
        //echo "<pre>";print_r($res1);
        $limit_list = array();
        $xy_saler_list=array();
        if(!empty($res_list)){
	        foreach ($res_list as $key => $v) {
	        	$key_list[$key] = $v['allnum'];
	        }
	        array_multisort($key_list,SORT_DESC,$res_list);
	        $limit_list = array();
	        for($i=0;$i<count($res_list);$i++) {
	        	if($i<9){
	        		$limit_list[] = $res_list[$i];
	        	}else{
	        		if($i==9){
	        			$limit_list[] = $res_list[$i];
	        			$tmp = $res_list[$i]['allnum'];
	        		}else{
	                    if($res_list[$i]['allnum']==$tmp)
	                    	$limit_list[] = $res_list[$i];
	        		}
	        	}
	        	if($res_list[$i]['allnum']>0)
	        		$xy_saler_list[]=$res_list[$i]['create_user'];        	
	        }
	    }    
        $xy_list = $limit_list;        


        //统计香榭巴黎排行
        $res1   = $userModel1->get_sale_goods_xxbl($date_start,$date_end,$sys1);
        $res111 = $userModel111->get_sale_goods_xxbl($date_start,$date_end,$sys111);
        $res_list = array_merge($res1,$res111);
        $res_list = array_filter($res_list, function($item){ 
                 return $item['allnum'] >= 2; 
            });        
        $key_list = array();
        //echo "<pre>";print_r($res1);
        $limit_list = array();
        $xybl_saler_list=array();
        if(!empty($res_list)){
	        foreach ($res_list as $key => $v) {
	        	$key_list[$key] = $v['allnum'];
	        }
	        array_multisort($key_list,SORT_DESC,$res_list);	        
	        for($i=0;$i<count($res_list);$i++) {
	        	if($i<9){
	        		$limit_list[] = $res_list[$i];
	        	}else{
	        		if($i==9){
	        			$limit_list[] = $res_list[$i];
	        			$tmp = $res_list[$i]['allnum'];
	        		}else{
	                    if($res_list[$i]['allnum']==$tmp)
	                    	$limit_list[] = $res_list[$i];
	        		}
	        	}
	        	if($res_list[$i]['allnum']>0)
	        		$xybl_saler_list[]=$res_list[$i]['create_user'];        	
	        }
	    }    
        $xxbl_list = $limit_list;


        //统计销售黑榜数据
        $res1 = $userModel1->get_sale_goods_amount($date_start,$date_end,$sys1);
        $res111 = $userModel111->get_sale_goods_amount($date_start,$date_end,$sys111);
        $amount_list = array_merge($res1,$res111);
        $amount_saler_num = count($amount_list);
        $amount_sum = array_sum(array_column($amount_list,'goods_amount')); 
        $amount_avg=0;
        if($amount_saler_num>0)
            $amount_avg = round($amount_sum/$amount_saler_num,2);
        $lower_avg_saler=array();
        //echo $amount_avg;
        //print_r($amount_list);
        if(!empty($amount_list)){
	        foreach ($amount_list as $key => $v) {
	        	if($v['goods_amount'] < $amount_avg && !in_array($v['create_user'],$tsyd_saler_list) && !in_array($v['create_user'],$xy_saler_list) && !in_array($v['create_user'],$xxbl_saler_list)){
	        		$v['amount_avg']=$amount_avg;
	        		$lower_avg_saler[]=$v;
	        	}
	        }
        }

        if(!empty($lower_avg_saler)){
	        $key_list = array();
	        foreach ($lower_avg_saler as $key => $v) {
	        	$key_list[$key] = $v['goods_amount'];
	        }
	        array_multisort($key_list,SORT_ASC,$lower_avg_saler);        
        } 

        $content='';
        $res=array('goods_amount_list' => $goods_amount_list,'tsyd_list'=>$tsyd_list,'xy_list'=>$xy_list,'xxbl_list'=>$xxbl_list,'lower_avg_saler'=>$lower_avg_saler);        	
        


        $csv_body = '<table>';
        
        $csv_body .="<tr align='center'><th colspan='4' style='text-align:center;'>红榜一. 各店销冠</th></tr>
					<tr>    
					    <th>排名</th>       
						<th>销售门店</th>
						<th>销售顾问</th>
						<th>销售业绩</th>			
					</tr>";
        if(!empty($goods_amount_list)){        
                foreach ($goods_amount_list as $kv => $info) {
                    $csv_body.="<tr><td>".($kv+1)."</td><td>". $info['channel_name'] ."</td><td>".$info['create_user']."</td><td>".$info['goods_amount']."</td></tr>";
                }
        }    



        $csv_body .="<tr><td colspan='4'></td></tr><tr><td colspan='4'></td></tr>"; 
        $csv_body .="<tr align='center'><th colspan='4' style='text-align:center;'>红榜二. 天生一对销售件数</th></tr>
					<tr>    
					    <th>排名</th>       
						<th>销售门店</th>
						<th>销售顾问</th>
						<th>销售业绩</th>			
					</tr>";
        if(!empty($tsyd_list)){        
                foreach ($tsyd_list as $kv => $info) {
                    $csv_body.="<tr><td>".($kv+1)."</td><td>". $info['channel_name'] ."</td><td>".$info['create_user']."</td><td>".$info['allnum']."</td></tr>";
                }
        }


        $csv_body .="<tr><td colspan='4'></td></tr><tr><td colspan='4'></td></tr>"; 
        $csv_body .="<tr align='center'><th colspan='4' style='text-align:center;'>红榜三. 星耀销售件数</th></tr>
					<tr>    
					    <th>排名</th>       
						<th>销售门店</th>
						<th>销售顾问</th>
						<th>销售业绩</th>			
					</tr>";
        if(!empty($xy_list)){        
                foreach ($xy_list as $kv => $info) {
                    $csv_body.="<tr><td>".($kv+1)."</td><td>". $info['channel_name'] ."</td><td>".$info['create_user']."</td><td>".$info['allnum']."</td></tr>";
                }
        }


        $csv_body .="<tr><td colspan='4'></td></tr><tr><td colspan='4'></td></tr>"; 
        $csv_body .="<tr align='center'><th colspan='4' style='text-align:center;'>红榜四. 香邂巴黎销售件数</th></tr>
					<tr>    
					    <th>排名</th>       
						<th>销售门店</th>
						<th>销售顾问</th>
						<th>销售业绩</th>			
					</tr>";
        if(!empty($xxbl_list)){        
                foreach ($xxbl_list as $kv => $info) {
                    $csv_body.="<tr><td>".($kv+1)."</td><td>". $info['channel_name'] ."</td><td>".$info['create_user']."</td><td>".$info['allnum']."</td></tr>";
                }
        }        


        $csv_body .="<tr><td colspan='4'></td></tr><tr><td colspan='4'></td></tr>"; 
        $csv_body .="<tr align='center'><th colspan='4' style='text-align:center;'>销售黑榜</th></tr>
					<tr>    
					    <th>排名</th>       
						<th>销售门店</th>
						<th>销售顾问</th>
						<th>全国平均</th>
						<th>销售业绩</th>			
					</tr>";
        if(!empty($lower_avg_saler)){        
                foreach ($lower_avg_saler as $kv => $info) {
                    $csv_body.="<tr><td>".($kv+1)."</td><td>". $info['channel_name'] ."</td><td>".$info['create_user']."</td><td>".$info['amount_avg']."</td><td>".$info['goods_amount']."</td></tr>";
                }
        }  
         
        $csv_body .="</table>";
        echo $csv_body;
    }


}
