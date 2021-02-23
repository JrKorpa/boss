<?php
/**
 *  -------------------------------------------------
 *   @file		: ExternalOrderController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-19
 *   @update	:
 *  -------------------------------------------------
 */
class ExternalOrderController extends CommonController
{
    //接口文件数组
    protected $from_arr = array(
        2 => array("ad_name"=> "淘宝B店", "api_path" =>"taobaoOrderApi"),
        71 => array("ad_name"=> "京东SOP", "api_path" =>"jd_jde_php"),
        "taobaoC" => array("ad_name"=> "淘宝C店", "api_path" =>"taobaoOrderApi"),
        "jingdongA" => array("ad_name"=> "京东", "api_path" =>"jd_jdk_php_2"),
        "jingdongB" => array("ad_name"=> "京东/裸钻", "api_path" =>"jd_jda_php"),
        "jingdongC" => array("ad_name"=> "京东/金条", "api_path" =>"jd_jdb_php"),
        "jingdongD" => array("ad_name"=> "京东/名品手表", "api_path" =>"jd_jdc_php"),
        "jingdongE" => array("ad_name"=> "京东/欧若雅", "api_path" =>"jd_jdd_php"),
        "paipai" => array("ad_name"=> "拍拍网店", "api_path" =>"paipaiOrder"),
    );

    //外部订单的仓库
    protected $warehouse_arr=array(
        2=>'线上低值库',
        79=>'深圳珍珠库',
        184=>'黄金网络库',
        386=>'彩宝库',
        482=>'淘宝黄金',
        483=>'京东黄金',
        484=>'淘宝素金',
        485=>'京东素金',
        486=>'线上钻饰库',
        546=>'线上唯品会货品库',
        487=>'线上混合库',
        96=>'总公司后库',
        5=>'半成品库',
        342=>'黄金店面库',
        369=>'主站库',
        521=>'投资金条库',
        546=>'线上唯品会货品库',
        400=>'B2C库',
        399=>'银行库',
        672=>'轻奢库',
        698=>'淘宝B店',
        699=>'京东SOP',
        700=>'B2C渠道二部',
        701=>'产品开发样品库',
        702=>'B2C渠道赠品库',
        705=>'主站金条库',
        323=>'淘宝裸钻库'
    );


	protected $smartyDebugEnabled = false;
    /**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
        $jumpurl = "index.php?mod=sales&con=BaseOrderInfo&act=index";
        $menuModel = new MenuModel(1);
        $menu = $menuModel->getMenuId($jumpurl);
	    $this->render('external_order_info.html',array());
	}


	/**
	 *	insert，信息入库
	 */
	public function insert ($params)
	{
        $result = array('success' => 0,'error' =>'');
        $tel = _Request::getString('tel');
        $consignee =  _Request::getString('consignee');
        $order_source = _Request::getString('order_source');
        $out_stu =  _Request::getString('out_stu');
        $distribution_type=_Request::getInt('distribution_type');
        $order['order_pay_type']=_Request::getInt('order_pay_type');
        $order['express_id']=_Request::getInt('express_id');
        $is_invoice=_Request::getInt('is_invoice');
        $email = _Request::getString('email');
        if(empty($distribution_type)){
            $result['error']="无详细信息提交失败";
            Util::jsonExit($result);
        }
        $order['order_pay_status'] = 1;
        //商品明细
        $apiModel = new ApiWarehouseModel();
        $apiStyle = new ApiStyleModel();
        $ms= _Request::getList('ms');
		$read_orderModel = new BaseOrderInfoModel(28);
		
        $goods_list = array();
        $goods_price_m=0;
        $favorable_price_m=0;
        $TZ=0;//套装累加
		
		//update by liulinyan for boss-988
		$source_type =  _Request::getInt('order_source');
		if($source_type == 2)
		{
			unset($this->warehouse_arr[483]);
			unset($this->warehouse_arr[485]);
		}elseif($source_type == 71)
		{
			unset($this->warehouse_arr[482]);
			unset($this->warehouse_arr[484]);
		}
        $warehouse_id = implode(",",array_keys($this->warehouse_arr));
        $xianhuof = true;
        //货品处理
        foreach($ms['kezi'] as $key=>$val)
		{
            $val= trim($val);
            if($val){ //验证刻字内容
                $ret = $apiStyle->GetStyleAttribute(array('style_sn'=>$ms['goods_sn'][$key]));
                $attr = $ret['error'] ==1 ?array():$ret['data'];
                //$newdo['kezi']=_Request::get('kezi');
                //刻字验证
                if(isset($val))
				{
                    $keziModel = new KeziModel();
                    $allkezi = $keziModel->getKeziData();
                    //是否欧版戒 92
                    if(isset($attr[92]['value']) && !empty($attr[92]['value']) && trim($attr[92]['value'] == '是'))
					{
                        $str_count = $keziModel->pdKeziData($val,$allkezi,1);
                        if($str_count['str_count']>=50)
						{
                            $result['error'] = "<span style='color:red';>第";
							$result['error'].= ($key+1)."行款号为欧版戒只能刻50位以内的任何字符！<span/>";
                            Util::jsonExit($result);
                        }
                        $ms['kezi'][$key] = $str_count['kezi'];
                    }else{
                        $str_count = $keziModel->pdKeziData($val,$allkezi);
                        if($str_count['str_count']>6){
                            $result['error'] = "<span style='color:red';>第";
							$result['error'].= ($key+1)."款号为非欧版戒只能刻最多6位字符！（一个汉字为一个字符）<span/>";
                            Util::jsonExit($result);
                        }
                        if($str_count['err_bd'] != ''){
                            $result['error'] = "<span style='color:red';>第";
							$result['error'].= ($key+1)."款号为非欧版戒以下标点不可以刻：".$str_count['err_bd']."<span/>";
                            Util::jsonExit($result);
                        }
                        $ms['kezi'][$key] = $str_count['kezi'];
                    }
                }
                /*去掉刻字字符长度限制
				$keziModel = new KeziModel();
                $allkezi = $keziModel->getKeziData();
                $str_count = $keziModel->pdKeziData($val,$allkezi);
                if($str_count>6){
                    $result['error'] = "第".($key+1)."行商品刻字内容不能超过六个字符";
                    Util::jsonExit($result);
                }*/
            }            
        }

        // goods_list 赋值
        $order['is_xianhuo']=1;
        foreach($ms['goods_sn'] as $key=>$val)
		{
            $val= trim($val);
            //套装处理
            if(array_key_exists($val,$this->_coordinates)){
                //生成多件货并拆价格
                $res = $this->MakeCoordinates($val,array('goods_price'=>$ms['goods_price'][$key],'favorable_price'=>$ms['favorable_price'][$key],'remark'=>$ms['remark'][$key]));
                foreach($res as $goodsinfo)
				{
                    $goods_list[$key+$TZ]=$goodsinfo;
                    $TZ+=1;
                }
                $goods_price_m+=$ms['goods_price'][$key];
                $favorable_price_m+=$ms['favorable_price'][$key];
                $xianhuof=false;
                continue;
            }

            //如果不填货号就是走期货填写了需要检查仓库是否有该期货付款就绑定
            //裸钻不验证 add liuri by 20150603 
            if($ms['goods_id'][$key]=='' ){
                $goods_list[$key+$TZ]['is_stock_goods']=0;
                //$xianhuof=false;
                //对款的正确性进行校验
                //裸钻不验证 add liuri by 20150603 
                if($val!='DIA'){
                    $styres = $apiStyle->GetStyleXiangKouByWhere($val);
                    if(empty($styres['data'])){
                        $result['error']='款号'.$val."不存在";
                        Util::jsonExit($result);
                    }


                 //期货或现货转定制，且款号非DIA，【表面工艺】字段必填
                  if(empty($ms['biaomiangongyi'][$key])){
                      $result['error']="表面工艺必填";
                      Util::jsonExit($result);
                  }
                }
				
				//证书号处理 update by liulinyan 2015-11-26 for boss-850
				//期货只需要验证证书号在裸钻列表,彩钻列表,商品列表里面存在即可
				if(trim($ms['zhengshuhao'][$key])!=='')
				{
					$isexitzsh = $read_orderModel->checkZhengshuhao($ms['zhengshuhao'][$key]);
					if(!$isexitzsh)
					{
						$result['error']='证书号'.$ms['zhengshuhao'][$key];
						$result['error'] .="在裸钻列表、彩钻列表、商品列表任意一个地方都不存在，请核实后再来录入！";
                        Util::jsonExit($result);
					}
				}
				
            }else{
                $where=array('goods_id'=>$ms['goods_id'][$key],'warehouse_id'=>$warehouse_id,'is_on_sale'=>2);
                $apiR = $apiModel->getWarehouseGoodsInfo($where);
                if($apiR['error']>0){
					if($source_type == 2)
					{
						$result['error']= '在淘宝可选仓库内 ,没有找到货号为'.$ms['goods_id'][$key]."可销售的现货";
					}elseif($source_type == 71 ){
						$result['error']= '在京东可选仓库内 ,没有找到货号为'.$ms['goods_id'][$key]."可销售的现货";
					}else{
                    	$result['error']= '货号'.$ms['goods_id'][$key]."未查到可销售的现货";
					}
                    Util::jsonExit($result);
                }
                $attr = $apiR['data']['data'];
				
				//如果是裸钻或者是彩钻验证证书号是不是一致
				if($attr['cat_type1'] =='彩钻' || $attr['cat_type'] =='裸石')
				{
					if(trim($attr['zhengshuhao']) != trim($ms['zhengshuhao'][$key]))
					{
						$result['error']='裸钻或彩钻现货'.$ms['goods_id'][$key].'，证书号必须与商品列表一致';
                        Util::jsonExit($result);
					}
				}
                //有货品做属性判断 todo
                //1.对款号进行匹配
                if(($attr['order_goods_id']!='')&&($attr['order_goods_id']!=0)){
                    $result['error']='货号'.$ms['goods_id'][$key]."已经绑定不能添加此货品";
                    Util::jsonExit($result);
                }
                //对款进行属性判断
                $styres = $apiStyle->GetStyleXiangKouByWhere($val);
                $goods_list[$key+$TZ]['is_stock_goods']=1;
                if($val=="DIA"){
                    $goods_list[$key+$TZ]['goods_type']='lz';
                    $xianhuof=false;
                }else{
                    $goods_list[$key+$TZ]['goods_type']='';
                    if($attr['goods_sn']!=$val){
                        $result['error']='货号'.$ms['goods_id'][$key]."与款号".$val."不能匹配";
                        Util::jsonExit($result);
                    }
                }
            }

            //当镶嵌方式选择【需工厂镶嵌、客户先看钻再返厂镶嵌】时，证书号、主石重、主石颜色、主石净度必填
            //工厂镶嵌方式增加一种【镶嵌4C裸钻】。选择镶嵌4C裸钻，证书号字段非必填，主石重、主石颜色、主石净度必填，工厂配钻工厂镶嵌，证书号字段不能编辑
            //当下期货或者现货转定制时，且款号非DIA，【表面工艺】字段必填
            $xiangkou = $ms['jietuoxiangkou'][$key];
            $carat = $ms['stone'][$key];
            $color = $ms['stone_color'][$key];
            $clarity = $ms['stone_clear'][$key];
            $xiangqian = $ms['xiangqian'][$key];
            $zhengshuhao = trim($ms['zhengshuhao'][$key]);
            if(in_array($ms['xiangqian'][$key],["需工厂镶嵌","客户先看钻再返厂镶嵌","工厂配钻，工厂镶嵌","镶嵌4C裸钻"])){
                if($xiangkou>0 && ($carat <=0||empty($color) || empty($clarity))){
                    $result['error']='货号'.$ms['goods_id'][$key]."{$xiangqian}时，主石颜色，净度，石重不能为空";
                    Util::jsonExit($result);
                }
            }

            if(in_array($ms['xiangqian'][$key],["需工厂镶嵌","客户先看钻再返厂镶嵌"]) && empty($zhengshuhao)){
                $result['error']='货号'.$ms['goods_id'][$key]."{$xiangqian}时，证书号不能为空";
                Util::jsonExit($result);
            }

            if(in_array($ms['xiangqian'][$key],["工厂配钻，工厂镶嵌"]) && !empty($zhengshuhao)){
                $result['error']='货号'.$ms['goods_id'][$key]."{$xiangqian}时，证书号不能填";
                Util::jsonExit($result);
            }
           /*-----*/




            if($ms['is_zp'][$key]==1)
            {
                $goods_list[$key+$TZ]['is_stock_goods']=1;
            }
            $is_stock_goods = $goods_list[$key+$TZ]['is_stock_goods'];
            $xiangqian = isset($ms['xiangqian'][$key])?$ms['xiangqian'][$key]:'';
            //主石单颗重验证
            if(!empty($ms['stone'][$key]) && !is_numeric($ms['stone'][$key])){
                $result['error']='货号'.$ms['goods_id'][$key]."主石单颗重不合法，主石单颗重必须为数字!";
                Util::jsonExit($result);
            }else{
                $ms['stone'][$key] = $ms['stone'][$key]/1;                
            } 
            //主石粒数验证
            if(!empty($ms['stone_num'][$key]) && floor($ms['stone_num'][$key])!=$ms['stone_num'][$key]){
                $result['error']='货号'.$ms['goods_id'][$key]."主石粒数不合法，主石粒数必须为正整数!";
                Util::jsonExit($result);
            }else{
                $ms['stone_num'][$key] = $ms['stone_num'][$key]/1;
            }
            //【期货】且镶嵌方式不是 【不需工厂镶嵌】的需要验证主石粒数和主石重的关系
            if($is_stock_goods != 1 && $xiangqian<>'不需工厂镶嵌'){
                if(($ms['stone'][$key]==0 && $ms['stone_num'][$key]>0) ||($ms['stone'][$key]>0 && $ms['stone_num'][$key]==0)){
                    $result['error']='货号'.$ms['goods_id'][$key]." 主石单颗重和主石粒数不合要求，两者要么同时大于0，要么同时为空或0!";
                    Util::jsonExit($result);
                }
            }
            //镶口
            if(!empty($ms['jietuoxiangkou'][$key]) && !is_numeric($ms['jietuoxiangkou'][$key])){
                $result['error']='货号'.$ms['goods_id'][$key]." 镶口不合法，镶口必须为数字!";
                Util::jsonExit($result);
            }else{
                $ms['jietuoxiangkou'][$key] = $ms['jietuoxiangkou'][$key]/1;
            }
            //证书号
            $zhengshuhao = isset($ms['zhengshuhao'][$key])?$ms['zhengshuhao'][$key]:'';
            if($zhengshuhao<>'' && !preg_match("/^[a-z|A-Z|0-9|\|]+$/is",$zhengshuhao)){
                $result['error']='货号'.$ms['goods_id'][$key]." 证书号不合法，证书号只能包含【字母】【数字】【英文竖线】,英文竖线作为多个证书号分隔符。";
                Util::jsonExit($result);
            }
            if($is_stock_goods != 1){
                if($zhengshuhao<>"" && $ms['zhengshuleibie'][$key]==""){
                    $result['error']='货号'.$ms['goods_id'][$key]." 证书类型不能为空，填写了证书号的货品必须填写证书类型。";
                    Util::jsonExit($result);
                }
            }








            //$result['error']='test :".$ms['zhengshuleibie'][$key];
            //Util::jsonExit($result);
            $goods_list[$key+$TZ]['goods_name']=trim($ms['goods_name'][$key]);
            $goods_list[$key+$TZ]['goods_sn']=trim($val);
            $goods_list[$key+$TZ]['ext_goods_sn']=trim($ms['goods_id'][$key]);
            $goods_list[$key+$TZ]['goods_id']=trim($ms['goods_id'][$key]);
            $goods_list[$key+$TZ]['goods_price']=trim($ms['goods_price'][$key]);
            $goods_list[$key+$TZ]['favorable_price']=$ms['favorable_price'][$key];
            $goods_list[$key+$TZ]['favorable_status']=3;
            $goods_list[$key+$TZ]['goods_count']=$ms['goods_number'][$key];
            $goods_list[$key+$TZ]['cart']=$ms['stone'][$key];//主石单颗重
            $goods_list[$key+$TZ]['zhushi_num']=$ms['stone_num'][$key];//主石粒数
            $goods_list[$key+$TZ]['xiangkou']=$ms['jietuoxiangkou'][$key];//镶口
            $goods_list[$key+$TZ]['color']=$ms['stone_color'][$key];
            $goods_list[$key+$TZ]['clarity']=$ms['stone_clear'][$key];
            $goods_list[$key+$TZ]['xiangqian']=$ms['xiangqian'][$key];//镶嵌方式
            $goods_list[$key+$TZ]['cert']= $ms['zhengshuleibie'][$key];
            $goods_list[$key+$TZ]['zhengshuhao']= trim($ms['zhengshuhao'][$key]);
            $goods_list[$key+$TZ]['zhiquan']=$ms['finger'][$key]/1;
            $goods_list[$key+$TZ]['jinzhong']=$ms['gold_weight'][$key];
            $goods_list[$key+$TZ]['caizhi']=isset($ms['gold'][$key])?$ms['gold'][$key]:'';
            $goods_list[$key+$TZ]['details_remark']=$ms['remark'][$key];
            $goods_list[$key+$TZ]['is_zp']=$ms['is_zp'][$key];
            $goods_list[$key+$TZ]['is_finance']=$ms['is_xz'][$key];
            $goods_list[$key+$TZ]['modify_time']=date("Y-m-d H:i:s");
            $goods_list[$key+$TZ]['details_status']=1;
            $goods_list[$key+$TZ]['create_time']=date("Y-m-d H:i:s");
            $goods_list[$key+$TZ]['create_user']=$_SESSION['userName'];
            $goods_list[$key+$TZ]['jinse']=isset($ms['jinse'][$key])?$ms['jinse'][$key]:'';//金色
            $goods_list[$key+$TZ]['kezi']=$ms['kezi'][$key];//刻字
            $goods_list[$key+$TZ]['face_work']=$ms['biaomiangongyi'][$key];//表面工艺
            $goods_list[$key+$TZ]['is_occupation']=$ms['is_occupation'][$key];//占用备货名额
            $goods_price_m+=$ms['goods_price'][$key];

            $favorable_price_m+=$ms['favorable_price'][$key];
            if($val =='DIA' || ( isset($attr['cat_type']) && $attr['cat_type']=='裸石' ) ){
                $goods_list[$key+$TZ]['goods_type']='lz';//裸钻
            }elseif(isset($attr['cat_type']) && $attr['cat_type'] =='彩钻'){
				$goods_list[$key+$TZ]['goods_type'] =='caizuan_goods';	
			}elseif($val == 'QIBAN'){
				$goods_list[$key+$TZ]['goods_type']='qiban';//起板
			}else if($ms['is_zp'][$key]==1) {
                $goods_list[$key+$TZ]['goods_type']='zp';
            }else{
                $goods_list[$key+$TZ]['goods_type']='style_goods';//线上非裸钻
                if($goods_list[$key+$TZ]['is_stock_goods'] < 1)
                {
					$order['is_xianhuo']=0;
				}
            }

            //判断是现货钻 1、期货钻 2 boss_1287
            if($goods_list[$key+$TZ]['goods_type'] == 'qiban' || $goods_list[$key+$TZ]['goods_type'] == 'caizuan_goods'){//起版、彩钻默认是期货
                $goods_list[$key+$TZ]['dia_type'] = 2;
            }else{
                if($goods_list[$key+$TZ]['is_stock_goods'] == 1){//现货
                    $goods_list[$key+$TZ]['dia_type'] = 1;
                }elseif($goods_list[$key+$TZ]['is_stock_goods'] == 0 && $goods_list[$key+$TZ]['zhengshuhao'] == ''){//期货
                    $goods_list[$key+$TZ]['dia_type'] = 1;
                }elseif($goods_list[$key+$TZ]['is_stock_goods'] == 0 && $goods_list[$key+$TZ]['zhengshuhao'] != ''){
                    $diamondModel = new SelfDiamondModel(19);
                    $zhengshuhaot = str_replace(array("GIA", "EGL","AGL"), "", $goods_list[$key+$TZ]['zhengshuhao']);
                    $check_dia = $diamondModel->getDiamondInfoByCertId($zhengshuhaot);
                    if(!empty($check_dia) && isset($check_dia['good_type'])){
                        if($check_dia['good_type'] == 1){
                            $goods_list[$key+$TZ]['dia_type'] = 1;
                        }elseif($check_dia['good_type'] == 2){
                            $goods_list[$key+$TZ]['dia_type'] = 2;
                        }else{
                            $goods_list[$key+$TZ]['dia_type'] = 0;
                        }
                    }else{
                        $goods_list[$key+$TZ]['dia_type'] = 1;
                    }
                }else{
                    $goods_list[$key+$TZ]['dia_type'] = 0;
                }//判断是现货钻 1、期货钻 2
            }
        }
        
        $orderP=$goods_price_m-$favorable_price_m+_Request::getFloat('shop_price');
        $order_amount=_Request::getFloat('order_amount');
        if((string)$orderP!=(string)$order_amount){
            $result['error']="商品总金额-商品优惠总金额不和订单金额相同";
            Util::jsonExit($result);
        }
        //检测是否占用备货
        $goods_list = $this->checkOccupationGoods($goods_list, $source_type);
        $order['out_order_sn'] =_Request::getString('exter_order_num');
        $orderModel = new BaseOrderInfoModel(27);
        //判断该外部单号是否已经录入
        $ex = $orderModel->checkOrderByWhere($order['out_order_sn']);
        if(!empty($ex['order_sn'])){
            $result['error']="该外部订单号已经录入,对应的订单号：".$ex['order_sn'];
            Util::jsonExit($result);
        }
        
        //追加逻辑, 用于直接保存订单和订单商品信息-start
        if(isset($_POST['flag'])&&$_POST['flag']==1){
            $order_sn=_Request::getString('kela_order_sn');
            $orderi['order_price'] =_Request::getString('order_amount');
            $orderi['goods_amount'] =$goods_price_m;
            $orderi['favorable_price']=$favorable_price_m;
            $orderi['order_source'] =$order_source;
            $orderi['out_order_sn'] =$order['out_order_sn'];
            //提交数据和追加订单进行数据对比
            //1.校验内部订单的渠道
            //2.匹配联系人 等待各种限制。。。。
            $address_info=$orderModel->getOrderAddinfoByOrderSn($order_sn);
            if ($address_info['department_id']!=$order_source) {
                $result['error'] = "要追加的订单与订单来源的渠道不符禁止追加！";
                Util::jsonExit($result);
            }
            if($consignee!=$address_info['consignee']){
                $result['error'] = "收货人不相同不能追加！";
                Util::jsonExit($result);
            }
            //订单只有保存状态才让添加
            if($address_info['order_status']!=1){
                $result['error'] = "订单只有待审核状态才能添加！";
                Util::jsonExit($result);
            }
           
            //插入明细表插入关联表并且去修改订单的价格明细表
            $res = $orderModel->originalDetaile($order_sn, $orderi,$goods_list);
            if(!empty($res)){
                //追加绑定下架问题
                $orderModel->Bindxiajia($address_info['order_id']);
                //日志
                $orderActionModel = new AppOrderActionModel(27);
                $ation['order_status'] = 1;
                $ation['order_id'] = $res;
                $ation['shipping_status'] = 1;
                $ation['pay_status'] = 1;
                $ation['create_user'] = $_SESSION['userName'];
                $ation['create_time'] = date("Y-m-d H:i:s");
                $ation['remark'] = "外部订单:[ $order[out_order_sn]]追加到内部订单[$res]";
                $orderActionModel->saveData($ation, array());
                $result['success'] = 1;
                $result['error']=$order_sn;
            }else{
                $result['error'] = "追加外部订单失败！";
            }
            Util::jsonExit($result);
        }
        //用于直接保存订单和订单商品信息-end

        //如果是财务备案的支付方式就把订单支付状态改成财务备案
        if(empty($order['order_pay_type'])){
            $result['error']="未选择支付方式不能提交";
            Util::jsonExit($result);
        }

        if(empty($order['express_id'])){
            $result['error']="未选择配送物流公司不能提交";
            Util::jsonExit($result);
        }

        if(_Request::getString('invoice_title')!='个人' && empty(_Request::getString('taxpayer_sn'))){
            $result['error']="发票抬头是公司的必须录入公司纳税人识别号";
            Util::jsonExit($result);
        }    



        switch($order_source){
            case 2: {
                $order['customer_source_id']=544;
                $order['department_id'] = 2;
                $address_info['express_id']=$order['express_id'];
                $order['delivery_status']=1;
                /*if($out_stu!='WAIT_SELLER_SEND_GOODS'){
                    $result['error'] = "淘宝订单状态不处于等待卖家发货状态不能添加！";
                    Util::jsonExit($result);
                }*/
                break;
            }
            case 71:{
                $order['customer_source_id']=2414;
                $order['department_id']=71;
                $address_info['express_id']=$order['express_id'];//默认京东?
                $order['delivery_status']=1;
                if($out_stu!='WAIT_SELLER_STOCK_OUT' && $out_stu!='POP_ORDER_PAUSE'){
                    $result['error'] = "京东订单状态不处于等待出库状态不能添加！";
                    Util::jsonExit($result);
                }
                break;
            }
        }

        //新建一个用户
        $where = array('member_phone' => $tel);
        $apiModel = new ApiMemberModel();
        $user_info = $apiModel->getMemberByPhone($where);
        //当没有此用户时，重新创建一个用户
        if ($user_info['error']==1) {
            $new_user_data = array(
                'member_name' => $consignee,
                'member_phone' =>$tel,
                'member_email' =>$email,
                'member_age' => 20,
                'member_type' => 1,
                'department_id' => $order['department_id'],
                'customer_source_id' =>  $order['customer_source_id'],
                'order_remark' => _Request::getString('order_remark'),
            );
            $res = $apiModel->createMember($new_user_data);
            if ($res['error'] > 0) {
                $result['error'] = "创建用户失败！";
                Util::jsonExit($result);
            }
            $user_id = $res['data'];
        } else {
            $user_id = $user_info['data']['member_id'];
        }

        //订单信息
        $order_sn = $orderModel->getOrderSn();
        $order['order_sn'] = $order_sn;
        $order['user_id'] = $user_id;//用户id
        $order['consignee'] =$consignee;
        $order['mobile'] = $tel;
        $order['order_status'] = 1;
        $order['create_time'] = date("Y-m-d H:i:s");
        $order['create_user'] = $_SESSION['userName'];
        $order['modify_time'] = date("Y-m-d H:i:s");
        $order['order_remark'] =_Request::getString('order_remark');
        $order['order_price'] =_Request::getString('order_amount');//订单总金额？
        $order['is_delete'] = 0;
        $order['favorable_status'] = 3;
        $order['is_zp'] = 0;
        $order['referer'] = '外部订单';
        $order['is_real_invoice'] = $is_invoice;

        //发票信息
        $invoice['is_invoice'] = $is_invoice;
        $invoice['invoice_amount'] =$order['order_price'];
        $invoice['invoice_title'] = _Request::getString('invoice_title');
        if($invoice['invoice_title']=="个人"){
           $invoice['title_type'] = 1;
           $invoice['taxpayer_sn'] = '';
        }else if(preg_match("/公司/is",$invoice['invoice_title']) || count($invoice['invoice_title'])>12){
           $invoice['title_type'] = 2;
           $invoice['taxpayer_sn'] = _Request::getString('taxpayer_sn');;
        }
        $invoice['invoice_status'] = 1;//未开发票
        $invoice['invoice_address'] = _Request::getString('address');
        $invoice['invoice_email'] = $email;
        $invoice['create_time'] = date("Y-m-d H:i:s");

        //订单金额信息
        $money['order_amount'] =$order['order_price'];
        $money['money_paid'] = 0;
        $money['money_unpaid'] = $order['order_price'];
        $money['shipping_fee'] =_Request::getFloat('shop_price');
        $money['goods_amount'] =$goods_price_m;
        $money['favorable_price']=$favorable_price_m;

        //地址
        $address_info['consignee'] = $consignee;
        $address_info['tel'] = $tel;
        $address_info['distribution_type']=$distribution_type;

        //BOSS-1393 销售管理-订单管理-外部订单录入-物流信息，物流信息是根据规则默认带出来的，根据上述规则这个地方要做对应转换，修改时也要根据上述规则进行判断；平台接口，快递公司自动变更，制单时间为1月16号-2月4号的顾客订单，以前程序自动识别为中通快递的自动变更为顺丰速运，不允许为中通快递；制单时间为1月17号-2月4号的顾客订单，以前程序自动识别为圆通速递的自动变更为顺丰速运，不允许为圆通速递，以下位置管控：
        $newExpressId = _Request::getInt('express_id');
        

/*
        if($order['create_time'] >= '2017-01-17 00:00:00' && $order['create_time'] <='2017-02-04 23:59:59' && $newExpressId == 19){
            $newExpressId = 4;
        }
        if($order['create_time'] >= '2017-01-19 00:00:00' && $order['create_time'] <='2017-02-04 23:59:59' && $newExpressId == 12){
            $newExpressId = 4;
        }
*/
        if($distribution_type==2){
            //到客户
            $address_info['address'] =_Request::getString('address');
            $address_info['country_id']= _Request::getInt('country_id');
            $address_info['province_id']= _Request::getInt('province_id');
            $address_info['city_id']= _Request::getInt('city_id');
            $address_info['regional_id']= _Request::getInt('regional_id');
            $address_info['zipcode']= _Request::getInt('zipcode');
            $address_info['email']= $email;
            $address_info['express_id']=$newExpressId;
            $address_info['goods_id']=0;
            $address_data['freight_no']='';
            $address_info['shop_type']=0;//体验店类型
            $address_info['shop_name']='';//体验店名称
            //这里走接口去查这个用户有没有地址，如果没有就写入一条默认地址
            $this->GetMemberaddress($user_id,$address_info);
        }elseif($distribution_type==1){
            //到店
            $shop_id=_Request::getInt('shop_id');
            $shop_type=_Request::getInt('shop_type');
            if($shop_id=='' || $shop_type=='')
            {
                 $result['error'] = "请选择体验店！";
                Util::jsonExit($result);
            }
            $shopModel = new ShopCfgModel(1);
            $shopinfo = $shopModel->getShopInfoByid($shop_id);
            $address_info['country_id']=$shopinfo['country_id'];
            $address_info['province_id']=$shopinfo['province_id'];
            $address_info['city_id']=$shopinfo['city_id'];
            $address_info['regional_id']=$shopinfo['regional_id'];
            $address_info['address']=$shopinfo['shop_address'];
            $address_info['express_id']=10;//物流问题? 上门取货
            $address_info['shop_type']=$shopinfo['shop_type'];//体验店类型

            $address_info['shop_name']=$shopinfo['shop_name'];//体验店名称
            $address_info['email']='';
            $address_info['goods_id']=0;
            $address_data['freight_no']='';
            $address_info['zipcode']='';
        }

        //赠品处理
        $gift_ids = _Request::getList('gift_id');
        $gift_num = _Request::getList('gift_num');
        $gift_nums ='';
        foreach($gift_ids as $k=>$v){
            if(array_key_exists($v,$gift_num)){
                $gift_nums.=$gift_num[$v].',';
            }
        }
        $gift_nums=trim($gift_nums,',');
        $gift_ids = array(
			'gift_id'=>implode(',',$gift_ids),
			'gift_num'=>$gift_nums,
			'remark'=>_Request::getString('gift_remark')
		);
        //保存所有数据
        $all_data = array(
			'order' => $order,
			'money' => $money,
			'address' => $address_info,
			'invoice' => $invoice,
			'goods_list'=>$goods_list,
			'gift'=>$gift_ids
		);
        $order_id = $orderModel->makeTaobaoOrder($all_data);
        if($order_id){
            $orderActionModel = new AppOrderActionModel(27);
            //操作日志
            $ation['order_status'] = 1;
            $ation['order_id'] = $order_id;
            $ation['shipping_status'] = 1;
            $ation['pay_status'] = 1;
            $ation['create_user'] = $_SESSION['userName'];
            $ation['create_time'] = date("Y-m-d H:i:s");
            $ation['remark'] = "生成订单外部订单号:[".$order['out_order_sn']."]";
            $orderActionModel->saveData($ation, array());
            $result['success'] = 1;
            $result['error']=$order_sn;
            $result['order_id']=$order_id;
        }else{
            $result['error'] = "生成订单失败！";
        }
        Util::jsonExit($result);
	}


    public function getExternalOrdeInfo()
	{
        $taobao_order_id = trim($_POST["order_id"]);
        $from_type = $_POST["order_source"];
        $res = $this->outApi($taobao_order_id,$from_type);
        $styleMolel = new CStyleModel(11);
        if($res['success']==1)
		{
            $res=$res['error'];
            //获取地理信息联动
            $region = new RegionModel(1);
            $countdata = $region->getRegionType(0);
          $addressid = '"'.$res['order']['province'].'","'.$res['order']['city'].'","'.$res['order']['district'].'"';
            $addressid= str_replace('省','',$addressid);
            $addressid= str_replace('市','',$addressid);
            $ids = $region->GetReginIdByName($addressid);
            //体验店信息
            $shopModel = new ShopCfgModel(1);
            $shops = $shopModel->getAllShopCfg();
            
            //商品属性信息
            $orderDetailModel = new AppOrderDetailsModel(27);
            $goods_attr = $orderDetailModel->getGoodsAttr();
            //这里统一做商品优惠的处理
            //拆出来一共有多少个商品
            $model=new AppOrderDetailsModel(27);
            $gifts=$model->getOutOrdergift();
            $s=0;
            if($from_type==71)
			{
                $countgoods = count($res['goods_list']);
                foreach($res['goods_list'] as $key=>$val)                
				{   
				    $style_sn = $val['goods_sn'];
				    $stoneList = $styleMolel->getStyleStoneByStyleSn($style_sn);
				    $zhushi_num = 0;
				    if(!empty($stoneList[1])){
				        $zhushiList = $stoneList[1];//主石列表
				        foreach ($zhushiList as $zhushi) {
				            $zhushi_num += $zhushi['zhushi_num'];
				        }
				    }
				    $res['goods_list'][$key]['zhushi_num'] = $zhushi_num;
				    
                    if(($key+1)!=$countgoods)
					{  
                        //如果不是最后一个
                        $res['goods_list'][$key]['favorable_price']=round($val['shop_price']/$res['order']['order_total_price'],2)*$res['order']['seller_discount'];
                        $res['goods_list'][$key]['zhenshi']=$val['shop_price']-$res['goods_list'][$key]['favorable_price'];
                        $s+= $res['goods_list'][$key]['favorable_price'];
                    }else{
                        //如果是最后一个
                        $res['goods_list'][$key]['favorable_price']=$res['order']['seller_discount']-$s;
                        $res['goods_list'][$key]['zhenshi']=$val['shop_price']-$res['goods_list'][$key]['favorable_price'];
                    }
                }
            }elseif($from_type==2){
                $res['order']['order_pay_type']=24;
            }
            $res['order']['from_type']=$from_type;

            $this->render('external_order_ext.html',
                array('shops'=>$shops,
                    'order'=>$res['order'],
                    'goods_list'=>$res['goods_list'],
                    'count'=>$countdata,
                    'ids'=>$ids,
                    'gifts'=>$gifts,
                    'paylist'=>$this->GetPaymentInfo(),
                    'exp'=>$this->GetExp(),
                    'goods_attr'=>$goods_attr,
                    )
                );
        }else{
            echo $res['error'];
        }
    }


    //地理信息联动方法
    public function getProvince(){
        $count_id = _Post::getInt('count');
        $reginModel = new RegionModel(1);
        $provincedata = $reginModel->getRegion($count_id);
        $res = $this->fetch('app_order_address_province_option.html',array('provincedata'=>$provincedata));
        echo $res;

    }

    //获取体验店详细地址的方法
    public function getShopInfo()
	{
		$result = array('success' => 0,'error' =>'');
		$shop_id = _Request::getInt('shop_id');
		if(empty($shop_id)){
			$result['error']  ="没有正确选择一个体验店";
			Util::jsonExit($result);
		}
		$shopModel = new ShopCfgModel($shop_id,1);
		$result['error']=$shopModel->getValue('shop_address');
		$result['success']=1;
		Util::jsonExit($result);
	}

	//对外部接口方法进行封装
	public function outApi($taobao_order_id,$from_type)
	{
		$result=array('success'=>0,'error'=>'');
		if(empty($taobao_order_id))
		{
			$result['error']='<div class="alert alert-info" style="display: block !important;width:100% !important;">';
			$result['error'] .= '外部订单号不能为空！</div>';
			$result['out_sn']=$taobao_order_id;
			return $result;
		}
        if(!Util::isNum($taobao_order_id))
		{
            $result['error']='<div class="alert alert-info" style="display: block !important;width:100% !important;">';
			$result['error'].= '外部订单号只能是纯数字！</div>';
            $result['out_sn']=$taobao_order_id;
            return $result;
        }
        if(empty($from_type)){
            $result['error']='<div class="alert alert-info" style="display: block !important;width:100% !important;">';
			$result['error'] .= '订单来源不能为空</div>';
            $result['out_sn']=$taobao_order_id;
            return $result;
        }
		//接口文件路径
        $file_path = APP_ROOT."sales/modules/".$this->from_arr[$from_type]["api_path"]."/index.php";
        if(!file_exists($file_path))
        {
            $result['error']='<div class="alert alert-info" style="display: block !important;width:100% !important;">';	            $result['error'] .= '很抱歉,接口文件不存在！</div>';
            return $result;
        }
        //引入接口文件
        require_once($file_path);
		
        if($from_type == 'paipai')//拍拍网店下单入口
        {
            $uin = "855000017";
            $appOAuthID = "700092986";
            $appOAuthkey = "1rhjvtkxU5d2wZJh";
            $accessToken = "d87a89bcfcef02b331cbeea0b880dd7f";
            $api_order =  new PaiPaiOpenApiOauth($appOAuthID, $appOAuthkey, $accessToken, $uin);
            //用户使用的提交数据的方法。post 和 get均可；以及字符集
            $api_order->setMethod("get");//post
            $api_order->setCharset("utf-8");//gbk
            //以下部分用于设置用户在调用相关接口时url中"?"之后的各个参数，如上述描述中的a=1&b=2&c=3
            $params = &$api_order->getParams();//注意，这里使用的是引用，故可以直接使用
            $params["sellerUin"] = $uin;
            $params["zhongwen"] = "cn";
            $params["pageSize"] = "10";
            $params["tms_op"] = "admin@855000017";
            $params["tms_opuin"] = $uin;
            $params["tms_skey"] = "@WXOgdqq16";
            $params["dealCode"] = $taobao_order_id;
            if($taobao_order_id=='')
            {
                $result['error']= '<div class="alert alert-info" style="display: block !important;width:100%';
				$result['error'] .=' !important; ">1.很抱歉,未查到该外部订单订单相关信息！</div>';
                $result['out_sn']=$taobao_order_id;
                return $result;
            }
            $params['listItem'] =1;//显示订单商品
            $api_order->setApiPath("/deal/getDealDetail.xhtml");
            $xml = $api_order->invoke();
            $xml = simplexml_load_string($xml);
            //print_r($xml);
            //exit;
            //条件满足增加BDD订单
			$ds_satus = 'DS_WAIT_SELLER_DELIVERY';
            if(trim($xml->errorCode)=='0' && $xml->errorMessage == '' && trim($xml->dealState) == $ds_satus)
            {
                /*print_r($xml);exit;*/
                //组合成数组给order数组
                $orderInfo = array(
                    'taobao_order_id'=>trim($xml->dealCode),
                    'consignee'=>trim($xml->receiverName),
                    'address'=>trim($xml->receiverAddress),
                    'tel'=>trim($xml->receiverPhone),
                    'mobile'=>trim($xml->receiverMobile),
                    'postscript'=>trim($xml->buyerRemark),
                    'post_fee'=>trim($xml->freight)/100,
                    'zipcode' =>trim($xml->receiverPostcode),
                    'country'=>1,
                    'email'=>trim($xml->buyerUin).'@qq.com',
                    'sign_building'=>'',
                    'best_time'=>'',
                    'need_inv'=>trim($xml->dealPayFeeTotal) >= 50000 ? 1 : 0,
                    'shipping_target'=>'个人',
                    'referer'=> "拍拍网店快速入单",
                    'warehouse' => "SZ",
                    'department'=>'2',
                    'from_ad'=>'000200230612',
                    'shipping_id'=>'4',
                    'shipping_name'=>'顺丰速运',
                    'pay_id'=>'80',
                    'pay_name'=>'腾讯拍拍店',
                    'province'=>0,
                    'district'=>0,
                    'city'=>0
                );
                //循环订单商品
                $n=0;
                foreach(($xml->itemList->itemInfo) as $item=>$v)
                {
                    $order_goods[$n]['goods_name'] = trim($v->itemName);
                    $order_goods[$n]['goods_sn'] = trim($v->itemLocalCode);
                    $order_goods[$n]['shop_price'] = trim($v->itemDealPrice)/100;//金额（分）除以100 得到（元）
                    $order_goods[$n]['num'] = trim($v->itemDealCount);
                    $order_goods[$n]['ext_goods_sn'] = trim($v->itemCode);
                    //print_r($v);exit;
                    $n++;
                }
                $res  = array(
                    'order' => $orderInfo,
                    'goods_list' => $order_goods
                );
            }else{
                $result['error']= '<div class="alert alert-info" style="display: block !important;width:100%';
				$result['error'] .=' !important; ">2.很抱歉,未查到该外部订单订单相关信息！</div>';
                $result['out_sn']=$taobao_order_id;
                return $result;
            }
        }else
        {
			//如果不是拍拍
            $apimodel = $this->from_arr[$from_type];
            $api_order = new $apimodel["api_path"]();
            //如果是京东sop走
            if($from_type==71){
                $api_order->new_jd=true;
            }
            if($from_type == 'taobaoC')
            {
                $api_order -> TAOBAO_APP_KEY = "21316845";
                $api_order -> TAOBAO_SECRETKEY = "1169aa8bf28956d2a82d7201666042f8";
                $api_order -> from_ad = "000200230545";
                $api_order -> top_session = "6101e139a3c2de25d07191e01c7d7dfe39c054f2ece214d289919";
            }
			//通过接口返回需要展示的数据
            $res = $api_order->make_order_info($taobao_order_id);
            if($res["is_error"])
            {
                $result['error']= '<div class="alert alert-info" style="display: block !important;width:100%';
				$result['error'] .= ' !important; ">3.' . $res['message']  .'</div>';
                $result['out_sn']=$taobao_order_id;
                return $result;
            }
            $result['success']=1;
            $result['error']=$res;
            return $result;
        }
    }

    //外部订单明细合并
    /*public function mergeOrder(){
        $result=array('success'=>0,'error'=>'');
        $out_sns = _Request::getList('m_order_sns');
        //去重
        $from_type = 'taobaoB';
        $out_sns = array('1002846480549537','1002846480549537','1002846480549537','1002846480549537');
        foreach($out_sns as $key=>$val){
            $res = $this->outApi($val,$from_type);
            //检测是否返回错误
            if($res['success']==1){
                //获取成功
                $resl[] = $res['error']['goods_list'];
            }else{
                //获取失败记录外部单号
                $resl['redarr'][]=$val;
            }

        }
        //检测结果 并返回
        if(!isset($resl['redarr'])){
            $this->fetch('');
        }

    }*/
    //检测输入的BDD单号和外部订单的关系
    public function getRelOutsn()
	{
        $result=array('success'=>0,'error'=>'');
        $order_sn = _Request::getString('ordersn');
        $outordersn= _Request::getString('outordersn');
        $order_source=_Request::getString('order_source');

        $model=new BaseOrderInfoModel(27);
        $res = $model->getRelOutsn($order_sn,$outordersn,$order_source);
        if($res===true)
		{
			//符合可以追加的判断
			$result['success']=1;
        }else{
            if($res==1)
			{
                $result['error']="BDD订单号有误不符合追加条件请检查订单是否存在和订单来源";
            }elseif($res==2){
                $result['error']="外部订单号已经被使用过了不能追加到此订单";
            }
        }
        Util::jsonExit($result);
    }


    public function MakeCoordinates($goods_sn,$info)
	{
        if(empty($goods_sn)){
            return false;
        }
        //拆价格
        $num=count($this->_coordinates[$goods_sn]);
        $avg_goods_price=round($info['goods_price']/$num,2);
        $avg_favorable_price=round($info['favorable_price']/$num,2);
        $goods_prices=0;
        $favorable_prices=0;
        //var_dump($avg_favorable_price,'-------',$avg_goods_price);
        //exit;
        foreach($this->_coordinates[$goods_sn] as $k=>$v){
            if($num>1){
                $goods_list[$k]['goods_price']=$avg_goods_price;
                $goods_list[$k]['favorable_price']=$avg_favorable_price;
                $goods_prices+=$avg_goods_price;
                $favorable_prices+=$avg_favorable_price;
                $num-=1;
            }else{
                $goods_list[$k]['goods_price']=$info['goods_price']-$goods_prices;
                $goods_list[$k]['favorable_price']=$info['favorable_price']-$favorable_prices;
            }

            $model =new ApiStyleModel();
            $res= $model->getStyleInfo($v);
            $goods_list[$k]['goods_name']='';
            $goods_list[$k]['goods_sn']=$v;
            $goods_list[$k]['goods_id']='';
            $goods_list[$k]['favorable_status']=3;
            $goods_list[$k]['goods_count']=1;
            $goods_list[$k]['cart']='';
            $goods_list[$k]['color']='';
            $goods_list[$k]['clarity']='';
            $goods_list[$k]['xiangqian']='';//镶嵌?
            $goods_list[$k]['zhengshuhao']='';
            $goods_list[$k]['zhiquan']='';
            $goods_list[$k]['jinzhong']='';
            $goods_list[$k]['caizhi']='';
            $goods_list[$k]['details_remark']=$info['remark'];
            $goods_list[$k]['modify_time']=date("Y-m-d H:i:s");
            $goods_list[$k]['details_status']=1;
            $goods_list[$k]['create_time']=date("Y-m-d H:i:s");
            $goods_list[$k]['create_user']=$_SESSION['userName'];
            $goods_list[$k]['jinse']='';//金色
            $goods_list[$k]['kezi']='';//刻字
            $goods_list[$k]['face_work']='';//表面工艺
            $goods_list[$k]['is_stock_goods']=0;//表面工艺
        }

        return $goods_list;
    }
    /*
     * 更具款号带出 主石粒数等属性
     */
    public function getStyleAttrsAjax(){
        $result = array('success'=>0,'error'=>'','data'=>array());
        $style_sn = _Request::getString('style_sn');
        $styleMolel = new CStyleModel(11);
        $exists = $styleMolel->select('count(*)',"style_sn='{$style_sn}'",3,'base_style_info');
        if(!$exists){
            $result['error'] = "款号不存在！";
            Util::jsonExit($result);
        }
        $stoneList = $styleMolel->getStyleStoneByStyleSn($style_sn);
        $zhushi_num = 0;
        if(!empty($stoneList[1])){
            $zhushiList = $stoneList[1];//主石列表
            foreach ($zhushiList as $zhushi) {
                $zhushi_num += $zhushi['zhushi_num'];
            }
        }
        $data['zhushi_num'] = $zhushi_num;
        
        $result['success'] = 1;
        $result['data'] = $data;        
        Util::jsonExit($result);
        
    }
    public function GetGoodsList(){
        $args = array(
            'mod'	=> _Request::get("mod"),
            'con'	=> substr(__CLASS__, 0, -10),
            'act'	=> __FUNCTION__,
            's_style_sn'=> _Request::get("s_style_sn"),
            's_stone'=> _Request::get("s_stone"),
            's_stone_color'=> _Request::get("s_stone_color"),
            's_stone_clear'	=> _Request::get("s_stone_clear"),
            's_zhengshuhao'	=> _Request::get("s_zhengshuhao"),
            's_finger'	=> _Request::get("s_finger"),
            's_jinzhong_begin'	=> _Request::get("s_jinzhong_begin"),
            's_jinzhong_end'	=> _Request::get("s_jinzhong_end"),
            's_zhushi_begin'	=> _Request::get("s_zhushi_begin"),
            's_zhushi_end'	=> _Request::get("s_zhushi_end"),
            's_caizhi'   => _Request::get("s_caizhi"),
            's_jinse' => _Request::get("s_jinse"),//是否绑定
            'page' => _Request::getInt("page", 1),
        );
        $where = array(
            'style_sn'	=> $args['s_style_sn'],
            'stone'	=> $args['s_stone'],
            'stone_color'	=> $args['s_stone_color'],
            'stone_clear'		=> $args['s_stone_clear'],
            'zhengshuhao'		=> $args['s_zhengshuhao'],
            'finger'		=> $args['s_finger'],
            'jinzhong_begin'	=> $args['s_jinzhong_begin'],
            'jinzhong_end'	=> $args['s_jinzhong_end'],
            'zhushi_begin'	=> $args['s_zhushi_begin'],
            'zhushi_end'	=> $args['s_zhushi_end'],
            'caizhi'       => $args['s_caizhi'],
            'jinse'    => $args['s_jinse'],
            'w_id'=>implode(',',array_keys($this->warehouse_arr)),
            'page'=>$args['page'],
        );
        if(!empty($where['jinse'])){
            $where['caizhi']=$where['caizhi'].$where['jinse'];
        }
        $selfDiaModel = new SelfDiamondModel(19);
        $Wapi = new ApiWarehouseModel();
        $res= $Wapi->SearchGoods(array('where'=>$where));
        $res=$res['data'];
        $arr=array();
        foreach($res['data'] as $k=>&$v){
            $v['zhengshuhao'] = str_replace(",","|",$v['zhengshuhao']); 
            $zhengshuhaoArr = explode("|",trim($v['zhengshuhao']));
            $zhengshuhao = $zhengshuhaoArr[0];
            if(count($zhengshuhaoArr)==1 && $zhengshuhao<>''){                
               $diainfo = $selfDiaModel->getDiamondInfoByCertId($zhengshuhao);
               if(!empty($diainfo)){
                   if($v['zhengshuleibie']=="" && $diainfo['cert'] != ''){
                      $v['zhengshuleibie'] = $diainfo['cert'];
                   }else if($v['zhengshuleibie']!="" && $diainfo['cert']!='' && $v['zhengshuleibie']!=$diainfo['cert']){
                      $v['zhengshuleibie'] = "";
                   }
               }
            }
			//取出材质
			$caizi = $this->getgoldandcolor($v['caizhi']);
			$v['gold'] = $caizi['gold'];
			$v['jinse'] = $caizi['color'];
			
			//取出货品类型
			$type = $this->dd->getEnum('warehouse_goods.tuo_type', $v['tuo_type']);
			$v['tuo_type'] = $type;
			if($type == '空托')
			{
				$v['tuo_type'] = '需工厂镶嵌';		
			}
			$arr[$v['goods_id']]=$v;
        }
        $rea = json_encode($arr);
        $pageData=$res;
        $pageData['filter'] = $args;
        $pageData['jsFuncs'] = 'goods_info_search_page_s';
        $this->render('external_order_search.html',array('goods_list'=>$res['data'],'info'=>$rea,'pa'=>Util::page($pageData)));
    }    

 //体验店类型动态的取体验店列表
    public function getShopList(){
        $shop_type = _Request::getInt('shop_type');
        $shmodel = new ShopCfgModel(1);
        $list = array_column($shmodel->getAllShopCfg(array('shop_type'=>$shop_type)),'shop_name','id');
        $this->render('shop_option.html',array('list'=>$list));
    }
    //动态获取赠品数据
    public function getGiftList(){
        $gift_id = _Request::getInt('val');
        $model=new AppOrderDetailsModel(27);
        $gift_infos=$model->getOutOrdergiftinfo($gift_id);
        
        $result['name']=$gift_infos['name'];
        $result['goods_number']=$gift_infos['goods_number'];
        $result['sell_sprice']=$gift_infos['sell_sprice'];
        $result['is_zp']=$gift_infos['is_zp'];
        $result['is_xz']=$gift_infos['is_xz'];
     
        Util::jsonExit($result);
    }

	/*
		author:Liulinyan
		data: 2015-08-3
		used:把材质打散为金料和颜色
	*/
	public function getgoldandcolor($caizhi)
	{
		$returndata = array('gold'=>'无','color'=>'无');
		if(empty($caizhi) || $caizhi=='无')
		{
			return $returndata;
		}
		//转换为大写
		$checkinfo = strtoupper($caizhi);
		if($checkinfo == 'PT950')
		{
			$returndata['gold'] = 'PT950';
			$returndata['color']='白';
			return $returndata;
		}
		if($checkinfo == 'S925')
		{
			$returndata['gold'] = 'S925';
			return $returndata;
		}
		if($checkinfo == '千足金' || $checkinfo=='足金')
		{
			//$returndata['gold'] = '24K';
			//$returndata['color']='黄';
			$returndata['gold'] = '足金';
			$returndata['color']='';
			return $returndata;
		}
		
		//定义两种情况
		$goldkind = array('PT900','K');
		//默认为K
		$goldtxt = 'K';
		foreach($goldkind as $v)
		{
			if(strpos($checkinfo,$v) !== false)
			{
				//金料的值
				$goldtxt = $v;
				continue;
			}
		}
		
		//将材质用获取到的goldtxt打散
		$caizhi_arr = explode($goldtxt,$checkinfo);
		$returndata['gold'] = $caizhi_arr[0].$goldtxt;
		$returndata['color'] = $caizhi_arr[1];
		if($returndata['color'] == '黄金')
		{
			$returndata['color']='黄';
		}
		if($returndata['color'] == '白金')
		{
			$returndata['color']='白';
		}
		return $returndata;
	}

    //占用备货
    public function checkOccupationGoods($goodslist=array(), $source_type)
    {
        //$result = array('success' => 0,'error' =>'');
        $model = new SelfPurchaseModel(23);
        $alreadyZy = array();
        foreach ($goodslist as $k => &$goods) {
            $goods['purchase_id'] = '';
            $goods['already_num'] = 0;
            if($goods['is_occupation'] == 1){
                $diff_val = array();
                $diff_val['goods_sn'] = $goods['goods_sn'];//款号
                $diff_val['cart']     = $goods['cart'];//石重
                $diff_val['color']    = $goods['color'];//颜色
                $diff_val['clarity']  = $goods['clarity'];//净度
                $diff_val['caizhi']   = $goods['caizhi'];//材质
                $diff_val['zhiquan']  = $goods['zhiquan'];//指圈
                $diff_val['num']  = $goods['goods_count'];//数量
                $diff_val['jinse']  = $goods['jinse'];//指圈
                $res = $model->checkOutOrderIsGoods($diff_val, $source_type, $alreadyZy);
                if($res !== false){
                    $alreadyZy = array_merge($alreadyZy,$res['ret']);
                    $goods['purchase_id'] = $res['purchase_id'];//绑定采购明细ID
                    $goods['already_num'] = count($res['ret']);//占用数量
                }
                else{
                    $result['error']  ="第".($k+1)."行货品，没有满足条件的备货名额！请将占用备货名额改为否。";
                    Util::jsonExit($result);
                }
            }
        }
        return $goodslist;
    }
}


?>
