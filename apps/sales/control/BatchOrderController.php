<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseOrderInfoController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-20 16:56:01
 *   @update	:
 *  -------------------------------------------------
 */
class BatchOrderController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist = array('downcsv');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$this->render('batch_order_search_form.html',array('bar'=>Auth::getBar('BATCH_ORDER_M',array(),true),'dd' => new DictView(new DictModel(1)),));
	}

	/**
	 *	add，渲染添加页面
	 */
	public function add ()
	{
		$result = array('success' => 0,'error' => '');
        //支付方式
        $paymentModel = new PaymentModel(1);
		$paymentList = $paymentModel->getEnabled();
        //客户来源
        $this->getCustomerSources();
        //渠道部门
        $res= $this->ChannelListO();
        if($res===true){
            //获取全部的有效的销售渠道
            $SalesChannelsModel = new SalesChannelsModel(1);
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`",array('is_deleted'=>0));
        }else{
            $channellist = $this->getchannelinfo($res);
        }
        //全部的物流
        $express = new ExpressView(new ExpressModel(1));
        $express = $express->getAllexp();
        $express =  array_column($express,'exp_name','id');
        
        //国家列表
        $Smodel = new ShopCfgModel(1);
        $shop = $Smodel->getAllShopCfg();
		$result['content'] = $this->fetch('batch_order_info.html',array(
			'view'=>new BaseOrderInfoView(new BaseOrderInfoModel(27)),
            'channellist'=>$channellist,
            'paymentList'=>$paymentList,
            'express'=>$express,
			'shop'=>$shop,			
		));
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
		$result['content'] = $this->fetch('batch_order_info.html',array(
			'view'=>new BaseOrderInfoView(new BaseOrderInfoModel($id,27)),
			'tab_id'=>$tab_id
		));
		$result['title'] = '编辑';
		Util::jsonExit($result);
	}

	/**
	 *	show，渲染查看页面
	 */
	public function show ($params)
	{
		$id = intval($params["id"]);
		$this->render('batch_order_show.html',array(
			'view'=>new BaseOrderInfoView(new BaseOrderInfoModel($id,27)),
			'bar'=>Auth::getViewBar()
		));
	}
	
    /**
     *	insert，信息入库
     */
    public function insert ($params){
        $result = array('success' => 0,'error' =>'');
        $order_status = _Request::getInt('order_status');
        $order_department = _Request::getInt('order_department');
        $customer_source = _Request::getInt('customer_source');
        $pay_type = _Request::getInt('pay_type');
        $express_id = _Request::getInt('express_id');
        //$by_customer = _Request::getString('by_customer'); // 默认选中
        $by_out_order_no = _Request::getString('by_out_order_no');
        $distribution_type = _Request::getInt('distribution_type');
        if($distribution_type==1){
        	$shop_id=_Request::getInt('shop_id');
        	$shopModel = new ShopCfgModel(1);
        	$shopinfo = $shopModel->getShopInfoByid($shop_id);
        	if(empty($shopinfo['country_id'])||empty($shopinfo['country_id'])||empty($shopinfo['country_id'])||empty($shopinfo['country_id'])){
        		$result['error'] = '体验店地址不全请先在管理中心通用将体验店地址信息补全';
        		Util::jsonExit($result);
        	}
        	
        	$regional_id=$shopinfo['regional_id'];
        	//$address=$shopinfo['shop_name'].'|'.$shopinfo['shop_address'];
        	$shop_name=$shopinfo['shop_name'];
        	$shop_type=$shopinfo['shop_type'];
        	$express_id=10;
        }else{
        	$regional_id=0;
        	$shop_name='';
        	$shop_type='';
        }

        if(empty($express_id)){
            $result['error'] = '请选择快递物流';
            Util::jsonExit($result);
        } 
        
        if(empty($pay_type)){
            $result['error'] = '请选择支付方式';
            Util::jsonExit($result);
        }

        //获取材质和材质颜色，列表
        $goodsAttrModel = new GoodsAttributeModel(17);
        $caizhi_arr = $goodsAttrModel->getCaizhiList(false);
        $jinse_arr = $goodsAttrModel->getJinseList(false);
        $facework_arr = $goodsAttrModel->getFaceworkList(false);
        $cert_arr = $goodsAttrModel->getCertList(false);
        $caizhi_list1 = array("9K","10K","14K","18K"); // 颜色不能为空的彩钻
        $caizhi_list2 = array("PT900","PT950","PT990","PT999","S990","裸石","其它","千足金","千足金银","千足银","足金","无");// 没有颜色的材质

        //渠道属于线上还是线下
        $newmodeldetail=new AppOrderDetailsModel(27);
        $is_online=$newmodeldetail->getOrdergiftchannelbydepid($order_department);

        if(!isset($_FILES['file_order'])){
            $result['error'] = '请上传文件';
            Util::jsonExit($result);
        }
        $upload_name = $_FILES['file_order'];

        if(empty($order_department)){
            $result['error'] = '请选择部门';
            Util::jsonExit($result);
        }
        if(empty($order_status)){
            $result['error'] = '请选择订单状态';
            Util::jsonExit($result);
        }
        if(empty($express_id)){
            $result['error'] = '请选择快递物流';
            Util::jsonExit($result);
        }
        if(empty($customer_source)){
            $result['error'] = '请选择来源';
            Util::jsonExit($result);
        }
        //屏蔽 begin  BOSS-1394 所有渠道允许使用圆通速递 by gaopeng	 2017-01-13
        // 1、销售管理-订单管理-批量导单，保存的时候需要增加一个判断条件:当部门选择【B2C销售部】且来源选择【中国移动积分】或者部门选择【银行销售部】且来源选择【交通银行】，快递物流允许选择【圆通速递】，其他的情况不允许保存，需提示“订单部门和来源不支持圆通速递，请选择其他的快递物流”
        // 2015-11-02   fix by lyy boss629
        // [12] => 圆通速递
        /* if(($order_department == 13 and $customer_source == 566 ) or ($order_department == 3 and $customer_source == 714 )) {
            ;//允许选择圆通也允许选择其他，什么都不做
        } else {
            if($express_id == 12)
            {
                $result['error'] = '订单部门和来源不支持圆通速递，请选择其他的快递物流';
                Util::jsonExit($result);
            }
        } */
        //屏蔽 end
        
        if (Upload::getExt($upload_name['name']) != 'csv') {
            $result['error'] = '请上传csv格式文件';
            Util::jsonExit($result);
        }
        $tmp_name = $upload_name['tmp_name'];
        if (!$tmp_name) {
            $result['error'] = '文件不能为空';
            Util::jsonExit($result);
        }
        $newmodel =  new BaseOrderInfoModel(28);
        $orderModel = new BaseOrderInfoModel(27);
        $orderActionModel = new AppOrderActionModel(27);
        $apiPurchModel = new ApiPurchaseModel();
        $apiModel = new ApiWarehouseModel();
        $apiStyle = new ApiStyleModel();
        $keziModel = new KeziModel();

        $goodsToMerge = array();
        $file = fopen($tmp_name, 'r');
        $i=0;

        //循环验证
        //1.对字段类型的验证  1电话 2 省市地区 3邮编验证 4产品id 5钻石大小6金重 7产品单价 8产品数量 9订单金额 10配送类型
        //2.匹配省市县存在的问题
        //3.判断外部订单状态
        $error['flag']=true;//鉴定标示
        $is_xianhuof=array();//鉴定期货
        $out_order_sns=array();//所有的外部单号

        //起版信息
        $qibanList = array();

        /* $attres = $apiStyle->GetStyleAttribute(array('style_sn'=>"KLRW028368"));
        $style_attr = array();
        if(is_array($attres['data']) && !empty($attres['data'])){
            foreach($attres['data'] as $vo){
                $style_attr[$vo['attribute_code']] = $vo;
            }
            unset($attres);
        } */
        // goods_ids,goods_sns,goods_names,goods_nums,postscripts,zhiquans,danjias,zuandaxiaos,zuan_jindus,zuan_yanses,jinliaos,jinzhongs,kezis,zhengshuhaos
        // "货号id，编号sn，名称，数量，备注，指圈，单价，重量，净度，颜色，金料，金料颜色，金重，刻字,证书号"
        // zp_ids,zp_s,zp_nums,zp_remark_s,zp_zhiquan
        // "赠品货号，赠品款号，赠品数量，赠品备注，赠品指圈"
        // 31列怎么多了个货号，7列已经有货号了；33列 SKU编码 是不是没用了
        $datalist = array();
        $goods_id_array=array();
        while ($datav = fgetcsv($file)) {
            if($i==0){
                $i++;
                continue;
            }
            if(count($datav)<42){
                $result['error'] = '模板不正确，请下载最新模板！';
                Util::jsonExit($result);
            }
            $datav = array_map("trimIconv",$datav);
            $row = array();
            // user info
            $customer = $row['customer'] =  $datav[0];//电话
            $row['mobile'] = $mobile     = $datav[1];//电话
            $row['provi'] = $provi      = $datav[2];//省
            $row['city'] = $city       = $datav[3];//市
            $row['region'] = $region     = $datav[4];//地区
            $row['address'] = $address    = $datav[5];//地址
            $row['code'] = $code       = $datav[6];//邮编

            // goods infos
            $row['goods_ids'] = $goods_ids  = $datav[7];//产品id
            $goods_sns = $row['goods_sns'] = $datav[8];//款号
            $row['goods_names'] = $goods_names  = $datav[9];//产品名称
            $zuandaxiaos = $row['zuandaxiaos'] = $datav[10];//主石单颗重
            $row['zhushi_nums'] = $zhushi_nums  = $datav[11];//主石粒数
            $row['zuan_yanses'] = $zuan_yanses  = $datav[12];//钻石颜色(大写英文,无,D,E,F,G,H,I,I-J,J,K,K-L,M)
            $row['zuan_jindus'] = $zuan_jindus  = $datav[13];//钻石净度(大写英文,无,FL,IF,VVS,VVS1,VVS2,VS,VS1,VS2,SI,SI1,SI2)
            $row['jinzhongs'] = $jinzhongs  = $datav[14];//金重
            $row['jinliaos'] = $jinliaos  = $datav[15];//金料
            $row['jinses'] = $jinses  = $datav[16];//金料颜色
            $row['danjias'] = $danjias  = $datav[17];//产品单价
            $row['goods_nums'] = $goods_nums  = $datav[18];//产品数量
            $row['zhiquans'] = $zhiquans  = $datav[19];//指圈号

            // order info
            $row['peisongfees'] = $peisongfees  = $datav[20];//配送费
            $row['order_amounts'] = $order_amounts  = $datav[21];//订单金额
            $row['biaomiangongyis'] = $biaomiangongyis  = $datav[22];//表面工艺
            $row['specials'] = $specials  = $datav[23];//是否特殊处理，商品备注
            $row['postscripts'] = $postscripts  = $datav[24];//订单备注
            $row['out_order_sn'] = $out_order_sn  = $datav[25];//外部订单号(50左右)

            // 赠品信息
            $zp_s  = $row['zp_s'] =  $datav[26];//赠品款号
            $zp_nums  = $row['zp_nums'] =  $datav[27];//赠品数量
            $zp_remark_s  = $row['zp_remark_s'] =  $datav[28];//赠品备注

            // 其他补充
            $row['kezis'] = $kezis  = $datav[29];//刻字内容
            $row['invoice_title'] = $invoice_title  = $datav[30];//发票抬头
            $row['taxpayer_sn'] = $taxpayer_sn  = $datav[31];//纳税人识别号
            $row['invoice_amount'] = $invoice_amount  = $datav[32];//订单发票金额
            $row['order_type'] = $order_type  = $datav[33];//订单类型
            $row['order_goods_id'] = $order_goods_id  = $datav[34];//货号
            $row['ismultistyle'] = $ismultistyle  =$datav[35];//是否一款多货（只限期货 1为是0为不是）
            $row['sku_code'] = $sku_code = $datav[36];//SKU编码
            $zp_ids = $row['zp_ids'] = $datav[37];//赠品id
            $zp_zhiquan = $row['zp_zhiquan'] = $datav[38];//赠品指圈
            $row['zhengshuhaos'] = $zhengshuhaos = $datav[39];//证书号
            $row['zhengshuleibies'] = $zhengshuleibies = $datav[40];//证书类别
            $row['xiangkou'] = $xiangkou = $datav[41];//镶口
            $row['ds_xiangci'] = $ds_xiangci = $datav[42];//单身-项次            
            $row['pinhao'] = $pinhao = $datav[43];//品号

            if(!empty($invoice_amount) && empty($invoice_title)){
                $error[ $i ][] = "<span style=color:red>发票抬头</span>：不能为空";
                $error['flag'] = FALSE;
            }
            if(!empty($invoice_title) && $invoice_title<>'个人' && empty($taxpayer_sn)){
                $error[ $i ][] = "<span style=color:red>发票抬头非个人的纳税人识别号</span>：不能为空";
                $error['flag'] = FALSE;
            }

            //省市外部订单号校验
            if(empty($provi)){
                $error[ $i ][] = "<span style=color:red>省</span>：不能为空";
                $error['flag'] = FALSE;
            }
            if(empty($city)){
                $error[ $i ][] = "<span style=color:red>市</span>：不能为空";
                $error['flag'] = FALSE;
            }
            if($customer_source == 2946 && empty($ds_xiangci)){//2946 鼎捷经销商订单 客户来源
                $error[ $i ][] = "<span style=color:red>鼎捷经销商订单，单身-项次</span>：不能为空";
                $error['flag'] = FALSE;
            }
            if($customer_source != 2946 && !empty($ds_xiangci)){//2946 鼎捷经销商订单 客户来源
                $error[ $i ][] = "<span style=color:red>不是鼎捷经销商订单，单身-项次</span>：不需填写";
                $error['flag'] = FALSE;
            }
            //首先判断各种值的数目是否匹配
            $style_attr_list = array();
            if(!empty($goods_sns)){
                //以款号的数量为准如果不能和款号的数量相匹配则不能提交
                $goods_sn_list = explode("|", $goods_sns);//款号
                $goods_id_list = explode("|", $goods_ids);//货号
                $zuandaxiaos_list = explode("|", $zuandaxiaos);//主石单颗重
                $zhushinum_list = explode("|", $zhushi_nums);//主石粒数
                $zhengshuhaos_list = explode("|", $zhengshuhaos);//证书号
                $zhengshuleibie_list = explode("|", $zhengshuleibies);//证书类型
                $caizhi_list = explode('|',$jinliaos);//金料
                $biaomiangongyilist = explode('|',$biaomiangongyis);//表面工艺
                $jinse_list = explode('|',$jinses);//金色
                $kezis_list = explode('|',$kezis);//刻字
                //查款号是否
                //对款进行属性判断
                foreach($goods_sn_list as $gsl => & $gslv){
                    if (!empty($gslv)) {
                        $goods_id = trim($goods_id_list[$gsl]);
                        $zuandaxiao = isset($zuandaxiaos_list[$gsl])?$zuandaxiaos_list[$gsl]:0;
                        $zhushi_num = isset($zhushinum_list[$gsl])?$zhushinum_list[$gsl]:0;
                        $zhengshuleibie = isset($zhengshuleibie_list[$gsl])?$zhengshuleibie_list[$gsl]:"";
                        $zhengshuhao = isset($zhengshuhaos_list[$gsl])?$zhengshuhaos_list[$gsl]:"";
                        $biaomiangongyi = isset($biaomiangongyilist[$gsl])?$biaomiangongyilist[$gsl]:'';
                        $caizhi = isset($caizhi_list[$gsl])?$caizhi_list[$gsl]:'';
                        $jinse = isset($jinse_list[$gsl])?$jinse_list[$gsl]:'';
                        $kezi = isset($kezis[$gsl])?$kezis[$gsl]:'';


                        $gslv = trim($gslv);
                        if(!is_numeric($zuandaxiao) && !empty($zuandaxiao)){
                            $error[ $i ][] = "<span style=color:red>主石单颗重【{$zuandaxiao}】不合法</span>：只能填写数字类型";
                            $error['flag'] = FALSE;
                        }
                        if(!empty($zhushi_num) && !preg_match("/^\d+$/",$zhushi_num)){
                            $error[ $i ][] = "<span style=color:red>主石粒数【{$zhushi_num}】不合法</span>：只能填写数字类型";
                            $error['flag'] = FALSE;
                        }
                        if(($zhushi_num<=0 && $zuandaxiao>0) || ($zhushi_num>0 && $zuandaxiao<=0)){
                            $error[ $i ][] = "<span style=color:red>主石单颗重和主石粒数不合法</span>：二者必须同时大于0或同时为空或等于0";
                            $error['flag'] = FALSE;
                        }
                        #gaopeng123
                        if(!empty($zhengshuleibie)){
                            if(!in_array($zhengshuleibie,$cert_arr)){
                                $error[ $i ][] = "<span style=color:red>证书类型</span>：不合法。";
                                $error['flag'] = FALSE;
                            }
                        }
                        if(!empty($zhengshuhao))
                        {                           
                            //证书号
                            if(!empty($zhengshuhao) && !preg_match("/^[a-z|A-Z|0-9|\|]+$/is",$zhengshuhao)){
                                $error[ $i ][] = "<span style=color:red>证书号</span>：不合法，证书号只能包含字母/数字/英文竖线";
                                $error['flag'] = FALSE;
                            }
                            if(empty($zhengshuleibie) || $zhengshuleibie=='无'){                                
                                $error[ $i ][] = "<span style=color:red>证书类型</span>： 不能为空或无，填写了证书号必须填写有效的证书类型";
                                $error['flag'] = FALSE;
                            }else{
                                $ret = $newmodel->checkCertByCertId($zhengshuhao,$zhengshuleibie);
                                if(!$ret){
                                    $error[ $i ][] = "<span style=color:red>证书类型</span>：与证书号{$zhengshuhao}对应的证书类型不匹配";
                                    $error['flag'] = FALSE;
                                }
                            }
                        }
                        
                        $goodsinfo=array();
                        if(!empty($goods_id)){
                            $goodsinfo=$orderModel->GetWarehouseGoodsByGoodsid($goods_id);
                            if(empty($goodsinfo)){
                                    $error[$i][]="<span style=color:red>未找到产品id</span>：{$goods_id} ";
                                    $error['flag']=false;
                                    continue;
                            }
                        }                            
                        //现货:通过产品ID获得是否现货,现货
                        if($gslv == 'DIA' || $gslv == 'CAIZUAN'){
                            //废除API获取货号 $goodsinfo = $apiModel->GetWarehouseGoodsByGoodsid(array('goods_id'=>$goods_id));
                            if(empty($zhengshuhao)){
                                //if(isset($goodsinfo['data']) && isset($goodsinfo['data']['data']) && !empty($goodsinfo['data']['data']['zhengshuhao'])){
                                if(isset($goodsinfo['zhengshuhao']) && !empty($goodsinfo['zhengshuhao'])){
                                    $error[ $i ][] = "裸钻的证书号必填".$goods_id;
                                    $error['flag'] = FALSE;
                                    continue;
                                }
                            }else{
                                //if(isset($goodsinfo['data']) && isset($goodsinfo['data']['data']) && isset($goodsinfo['data']['data']['zhengshuhao'])){
                                    if($zhengshuhao != $goodsinfo['zhengshuhao']){
                                        $error[ $i ][] = "裸钻的证书号与货号不匹配".$goodsinfo['zhengshuhao'];
                                        $error['flag'] = FALSE;
                                        continue;
                                    }
                                //}
                            }
                            //continue;
                        }
                        //$qibaninfo = $apiPurchModel->GetQiBianGoodsByQBId($gslv);
                        $qibaninfo = $orderModel->GetQiBianGoodsByQBId($gslv);
                        if(!empty($qibaninfo)){
                            if($qibaninfo['status'] != 1){
                                $error[ $i ][] = "<span style=color:red>起版号 $gslv</span>：状态出错";
                                $error['flag'] = FALSE;
                            }
                            if($qibaninfo['order_sn'] != ''){
                                $error[ $i ][] = "<span style=color:red>起版号 $gslv</span>：已在订单{$qibaninfo['order_sn']}使用";
                                $error['flag'] = FALSE;
                            }
                            $qibanList[$gslv] = $qibaninfo;
                            continue;
                        }

                        if(!empty($goods_id)){
                            if(!in_array($goods_id,$goods_id_array))
                                $goods_id_array[]=$goods_id;
                            else{
                                $error[ $i ][] = "货号【{$goods_id}】重复，不允许保存";
                                $error['flag'] = FALSE;                                
                            }    
                            //
                            //$goodsinfo = $apiModel->GetWarehouseGoodsByGoodsid(array('goods_id'=>$goods_id));
                            
                            // 开始与现货对比 BOSS-1021
                            //$warehouseGoods = $goodsinfo['data']['data'];
                            $warehouseGoods = $goodsinfo;
                            if($warehouseGoods['goods_sn'] != $gslv) {
                                $error[ $i ][] = "货号【{$goods_id}】对应的款号【{$gslv}】跟库存中的款号不一致，不允许保存";
                                $error['flag'] = FALSE;
                            } else {
                                $goods_caizhi_arr = $goodsAttrModel->explodeZhuchengseToStr($warehouseGoods['caizhi']);
                                $kucun_caizhi = $goods_caizhi_arr['caizhi'];
                                $kucun_jinse  = $goods_caizhi_arr['jinse'];
                                // 与库存现货材质 对比
                                if(!empty($caizhi) && strtoupper($caizhi) != strtoupper($kucun_caizhi)){
                                    $error[ $i ][] = "<span style=color:red>产品id</span>：$goods_id 录入的金料【{$caizhi}】与现货【{$kucun_caizhi}】不一致，不允许保存";
                                    $error['flag'] = FALSE;
                                }
                                //与库存现货材质颜色(金色) 对比
                                if(!empty($jinse) &&$jinse != '无' && $jinse != $kucun_jinse){
                                    $error[ $i ][] = "<span style=color:red>产品id</span>：$goods_id 录入的金料颜色【{$jinse}】与现货【{$kucun_jinse}】不一致，不允许保存";
                                    $error['flag'] = FALSE;
                                }

                                if($warehouseGoods['is_on_sale'] != 2) {
                                    $error[$i][]="<span style=color:red>产品id</span>：$goods_id 没有匹配的货品不能添加";
                                    $error['flag']=false;
                                }
                                if($warehouseGoods['order_goods_id'] != '' && $warehouseGoods['order_goods_id']!=0){
                                    $error[$i][]="<span style=color:red>产品id</span>：$goods_id 已被绑定不能绑定这个货";
                                    $error['flag']=false;
                                }
                                if(in_array($warehouseGoods['cat_type1'],array('裸石','彩钻')) && $warehouseGoods['zhengshuhao'] != $zhengshuhao){
                                    $error[$i][]="<span style=color:red>产品id</span>：$goods_id 证书号不是 ".$warehouseGoods['zhengshuhao'];
                                    $error['flag']=false;
                                }
                            }
                        }else{
                            $style_attr_list[$gsl]['goods_sn'] = $gslv;
                            $style_attr_list[$gsl]['attr'] = array();

                            //$styres = $apiStyle->GetStyleXiangKouByWhere($gslv);
                            $styres=$orderModel->GetStyleXiangKouByWhere($gslv);
                            if (empty($styres) || !is_array($styres)) {
                                $error[ $i ][] = "<span style=color:red>产品编号$gslv</span>：没有工厂";
                                $error['flag'] = FALSE;
                            }
                        }
                        // 不管期货还是现货的材质、颜色检查
                        if(!empty($caizhi)){
                            //金料(材质)是否在系统存在
                            if(!in_array(strtoupper($caizhi),$caizhi_arr)){
                                $error[$i][]="产品编号{$gslv}：金料【{$caizhi}】系统不存在";
                                $error['flag']=false;
                            }
                            /*
                            if(empty($goods_id)){
                                $goodsAttrModel = new GoodsAttributeModel(17);
                                $style_caizhi=$goodsAttrModel->getStyleCaizhi($gslv);
                                if(!in_array(strtoupper($caizhi),$style_caizhi)){
                                    $error[$i][]="产品编号{$gslv}：金料【{$caizhi}】系统不存在";
                                    $error['flag']=false;
                                }
                            }*/    

                            //金料(材质)颜色是否在系统存在
                            if(!in_array(strtoupper($caizhi),$caizhi_list2)  && $caizhi !='S925'){
                                if(!in_array(strtoupper($jinse),$jinse_arr)){
                                    $error[$i][] = "产品编号{$gslv}：金料颜色【{$jinse}】系统不存在";
                                    $error['flag'] = false;
                                }
                            }

                            //材质为9K,10K,14K,18K时，需判断材质颜色不能为空
                            if(in_array(strtoupper($caizhi),$caizhi_list1)){
                                if($jinse=="无" || $jinse==""){
                                    $error[$i][]="产品编号{$gslv}：金料为【{$caizhi}】时，金料颜色不能为空";
                                    $error['flag']=false;
                                }
                            }else if(in_array(strtoupper($caizhi),$caizhi_list2)){
                                //如果材质输入“PT900、PT950、PT999、S925、S990、裸石、其它、千足金、千足金银、千足银、无”，材质颜色必须为空，否则报错
                                if($jinse!="无" && $jinse!=""){
                                    $error[$i][]="产品编号{$gslv}：金料为【{$caizhi}】时，金料颜色必须为空";
                                    $error['flag']=false;
                                }
                            }
                        } else {
                            // 材质为空，材质颜色不为空时 不允许录入
                            if(!empty($jinse)){
                                $error[ $i ][] = "产品编号{$gslv}：材质为空，材质颜色【{$jinse}】必须为空才允许导入，请检查";
                                $error['flag'] = FALSE;
                            }
                        }
                        //验证刻字内容
                        if(!empty($kezi)){
                            $styleAttrInfo = $apiStyle->GetStyleAttribute(array('style_sn'=>$goods_sns));
                            $attrinfo = $styleAttrInfo['error'] == 1 ? array() : $styleAttrInfo['data'];
                            //刻字验证
                            $allkezi = $keziModel->getKeziData();
                            //是否欧版戒 92
                            if(isset($attrinfo[92]['value']) && !empty($attrinfo[92]['value']) && trim($attrinfo[92]['value'] == '是')){
                                $str_count = $keziModel->pdKeziData($datav['kezis'],$allkezi,1);
                                if($str_count['str_count']>=50){
                                    $error[$i][]="<span style=color:red>刻字内容</span>欧版戒只能刻50位以内的任何字符";
                                    $error['flag']=false;
                                }
                            }else{
                                $str_count = $keziModel->pdKeziData($kezis,$allkezi);
                                if($str_count['str_count']>6){
                                    $error[$i][]="<span style=color:red>刻字内容</span>非欧版戒只能刻最多6位字符";
                                    $error['flag']=false;
                                }
                                if($str_count['err_bd'] != ''){
                                    $error[$i][]="<span style=color:red>刻字内容</span>非欧版戒下列字符不可以刻：".$str_count['err_bd']."";
                                    $error['flag']=false;
                                }
                            }
                        }
                        //验证表面工艺
                        if(!empty($biaomiangongyi)){
                            if(!in_array($biaomiangongyi, $facework_arr)){
                                $error[ $i ][] = "产品编号{$gslv}：表面工艺【{$biaomiangongyi}】不存在，请检查！<br>
                                已有表面工艺【".implode("|", $facework_arr)."】";
                                $error['flag'] = FALSE;
                            }
                        }







                    }else{
                        $error[ $i ][] = "<span style=color:red>产品编号$gslv</span>：款号有空值请检查";
                        $error['flag'] = FALSE;
                    }
                }
                $snc= count($goods_sn_list);
            }else{
                $error[$i][]   = "<span style=color:red>产品编号</span>：不允许为空";
                $error['flag'] = false;
            }

            if(!empty($goods_ids)){
                $goods_id_unique_s = array();
                $goods_id_list = explode("|", $goods_ids);
                foreach($goods_id_list as $k=>$v){
                    if($v==''){
                        $is_xianhuof[]=$i;
                        continue;
                    }
                    //这里判断货号重复的情况
                    if(!empty($goods_id_unique_s) && in_array($v,$goods_id_unique_s)){
                        $error[$i][]="<span style=color:red>产品id$v</span>：货号重复请检查货号";
                        $error['flag']=false;
                    }
                    $goods_id_unique_s[]=$v;
                    //款号和货号的匹配
                }
                if(count($goods_id_list)!=$snc){
                    $error[$i][]="<span style=color:red>产品id</span>：与商品数量不相同请检查";
                    $error['flag']=false;
                }
            }else{
                //全是期货
                $is_xianhuof[]=$i;
            }
            if(!empty($goods_names)){
                $goods_name_list = explode("|", $goods_names);
                if(count($goods_name_list)!=$snc){
                    $error[$i][]="<span style=color:red>商品名称</span>：与商品数量不相同请检查";
                    $error['flag']=false;
                }
            }
            if(!empty($goods_nums)){
                $goods_num_list = explode("|", $goods_nums);
                $count=in_array('1',array_map(array($this,'goodsnum'),$goods_num_list));
                if(count($goods_num_list)!=$snc){
                    $error[$i][]="<span style=color:red>产品数量</span>：与商品数量不相同请检查";
                    $error['flag']=false;
                }
                if($count){
                    $error[$i][]="<span style=color:red>产品数量</span>类型出错";
                    $error['flag']=false;
                }
            }else{
                $error[$i][]="<span style=color:red>产品数量</span>不能为空";
                $error['flag']=false;
            }
            if($zhiquans != ''){
                $finger_list = explode("|", $zhiquans);
                $zhiquan = in_array('1',array_map(array($this,'finger'),$finger_list));
                if($zhiquan){
                    $error[$i][]="<span style=color:red>指圈数据</span>类型出错";
                    $error['flag']=false;
                }
                if(count($finger_list)!=$snc){
                    $error[$i][]="<span style=color:red>指圈数据</span>：与商品数量不相同请检查";
                    $error['flag']=false;
                }
            }else{
                $error[$i][]="<span style=color:red>指圈数据</span>不能为空";
                $error['flag']=false;
            }
            if(!empty($danjias)){
                $goods_price_list = explode("|", $danjias);
                $price=in_array('1',array_map(array($this,'price'),$goods_price_list));
                if(count($goods_price_list)!=$snc){
                    $error[$i][]="<span style=color:red>产品单价</span>：与商品数量不相同请检查";
                    $error['flag']=false;
                }
                if($price){
                    $error[$i][]="<span style=color:red>产品单价</span>类型（数字最高两位小数）出错";
                    $error['flag']=false;
                }
            }else{
                $error[$i][]="<span style=color:red>产品单价</span>不能为空";
                $error['flag']=false;
            }
            $shop_price = $peisongfees;
            if(!Util::isNum($shop_price)||$shop_price<0){
                $error[$i][]="<span style=color:red>配送费</span>类型出错";
                $error['flag']=false;
            }
            $total=explode("|", $danjias);
            $tnum =explode("|", $goods_nums);
            $totaloderP=0;
            foreach($total as $a=>$b){
                $totaloderP+=$b*$tnum[$a];
            }
            if(!empty($invoice_title) && empty($invoice_amount)){
                $error[$i][]="<span style=color:red>发票有抬头 发票金额</span>不能为空";
                $error['flag']=false;
            }

            if(bccomp($invoice_amount,$totaloderP,2)==1){
                $error[$i][]="<span style=color:red>发票金额{$invoice_amount}</span>大于商品金额{$totaloderP}";
                $error['flag']=false;
            }
            //判断订单状态
            //外部单号是否存在
            $info = $orderModel->checkOrderByWhere($out_order_sn);
            if($info){
                $error[$i][]="<span style=color:red>外部订单号</span>：{$out_order_sn}已经存在";
                $error['flag']=false;
            }

            $province_id = $this->getRegionId($provi,1);
            if(empty($province_id)){
                $error[$i][]="<span style=color:red>省</span>:输入错误";
                $error['flag']=false;
            }

            $city_id = $this->getRegionId($city,2);
            if(empty($city_id)){
                $error[$i][]="<span style=color:red>市</span>:输入错误";
                $error['flag']=false;
            }

            //赠品判断
            if(!empty($zp_s)){
                $gifts_sn=explode('|',$zp_s);
                $gifts_amounts=explode('|',$zp_nums);
                $gifts_ids = explode('|',$zp_ids);
                $gifts_remark_s = explode('|',$zp_remark_s);
                $gifts_zhiquan = explode('|',$zp_zhiquan);
                $gift_count= count($gifts_sn);
                
                if(!empty($zp_ids) && count($gifts_ids) <> $gift_count){
                    $error[$i][]="<span style=color:red>赠品ID</span>:赠品商品ID数量与赠品数量不一致";
                    $error['flag']=false;
                }else if(empty($zp_ids)){
                    $zp_ids = $row['zp_ids'] = str_repeat('|',$gift_count-1);
                }
                if(!empty($zp_remark_s) && count($gifts_remark_s) <> $gift_count){
                    $error[$i][]="<span style=color:red>赠品备注</span>:赠品备注数量与赠品数量不一致";
                    $error['flag']=false;
                }else if(empty($zp_remark_s)){
                    $zp_remark_s = $row['zp_remark_s'] = str_repeat('|',$gift_count-1);
                }
                if(!empty($zp_zhiquan) && count($gifts_zhiquan) <> $gift_count){
                    $error[$i][]="<span style=color:red>赠品指圈</span>:赠品指圈数量与赠品数量不一致";
                    $error['flag']=false;
                }else if(empty($zp_zhiquan)){
                    $zp_zhiquan = $row['zp_zhiquan'] = str_repeat('|',$gift_count-1);
                }
                foreach ($gifts_sn as $key=>$val){
                    $gifts_info=$orderModel->selectzpinfo($gifts_sn[$key]);

                    if(empty($gifts_info)) {
                        $error[$i][]="<span style=color:red>赠品款号</span>:$gifts_sn[$key]赠品款号不存在";
                        $error['flag']=false;
                        continue;
                    }
                    if($gifts_info['status']!=1) {
                        $error[$i][]="<span style=color:red>赠品款号</span>:$gifts_sn[$key]赠品款号不可用";
                        $error['flag']=false;
                    }
                    if($gifts_info['sale_way']!=$is_online && $gifts_info['sale_way']!=12 ) {
                        $error[$i][]="<span style=color:red>赠品款号</span>:$gifts_sn[$key]赠品款号不能用于此渠道";
                        $error['flag']=false;
                    }
                    // 赠品对应的赠品数量为空 或者 不是数字
                    if (empty($gifts_amounts[$key]) || !is_numeric($gifts_amounts[$key])) {
                        $error[$i][]="<span style=color:red>赠品款号</span>:$gifts_sn[$key]赠品数量为空或类型不对";
                        $error['flag']=false;
                    }
                    if (!empty($gifts_zhiquan[$key]) && !is_numeric($gifts_zhiquan[$key])) {
                        $error[$i][]="<span style=color:red>赠品指圈</span>:$gifts_zhiquan[$key]类型不对，必须为数字";
                        $error['flag']=false;
                    }
                }
                
            } else {
                // 如果赠品为空款号【24】为空，数量【25】和备注【26】不允许输入
                if ($zp_nums!='' || $zp_remark_s!='') {
                    $error[$i][]="<span style=color:red>赠品款号为空</span>，不允许录入赠品数量和赠品备注";
                    $error['flag']=false;
                }
            }
            // 外部订单号一致，但是姓名或者电话不一致，导入提示“第1、2行外部订单号一致，姓名或电话不一致，不允许导入”
            if ($out_order_sn) {
                if(isset($out_order_sns[$out_order_sn])) {
                    $temp_item = $out_order_sns[$out_order_sn];
                    if ($temp_item['customer']!=$customer || $temp_item['mobile']!=$mobile) {
                        $row = $temp_item['row'] + 1;
                        $error[$i][]="与第{$row}行的外部订单号【<span style=color:red>$out_order_sn</span>】一致，姓名或电话不一致，不允许导入";
                        $error['flag']=false;
                    }
                } else {
                    // 只存最初的，跟后面的对比
                    $out_order_sns[$out_order_sn] = array('row'=>$i, 'customer'=>$customer, 'mobile'=>$mobile);
                }
            }
            // 有效数据合并保存
            $merged = false;
            if ($by_out_order_no=='on' && $out_order_sn) {
                // 姓名+手机号+外部单号 合并
                if (!empty($goodsToMerge[$out_order_sn.$customer.$mobile])) {
                    $idx = $goodsToMerge[$out_order_sn.$customer.$mobile];
                    $this->mergeGoods($datalist[$idx], $row);
                    $merged = true;
                } else {
                    $goodsToMerge[$out_order_sn.$customer.$mobile] = $i;
                }
            } else {
                // 姓名+手机号 合并
                if (!empty($goodsToMerge[$customer.$mobile])) {
                    $idx = $goodsToMerge[$customer.$mobile];
                    $this->mergeGoods($datalist[$idx], $row);
                    $merged = true;
                } else {
                    $goodsToMerge[$customer.$mobile] = $i;
                }
               
            }
            if (!$merged) {
                if(empty($row['goods_ids']))
                    $row['goods_ids']='|0';
                if(empty($row['specials']))
                    $row['specials']='|-';
                if(empty($row['zhiquans']))
                    $row['zhiquans']='| ';
                if(empty($row['zuandaxiaos']))
                    $row['zuandaxiaos']='|0';
                if(empty($row['zhushi_nums']))
                    $row['zhushi_nums']='|0';
                if(empty($row['zuan_jindus']))
                    $row['zuan_jindus']='| ';
                if(empty($row['zuan_yanses']))
                    $row['zuan_yanses']='| ';
                if(empty($row['jinliaos']))
                    $row['jinliaos']='| ';  
                if(empty($row['jinses']))
                    $row['jinses']='| ';
                if(empty($row['jinzhongs']))
                    $row['jinzhongs']='| ';
                if(empty($row['kezis']))
                    $row['kezis']='| '; 
                if(empty($row['zhengshuhaos']))
                    $row['zhengshuhaos']='| '; 
                if(empty($row['zhengshuleibies']))
                    $row['zhengshuleibies']='| ';
                if(empty($row['xiangkou']))
                    $row['xiangkou']='| ';
                if(empty($row['biaomiangongyis']))
                    $row['biaomiangongyis']='| ';
                 $datalist[$i] = $row;
            }
            $i++;
            //print_r($datalist);
        }
        //print_r($error);
//exit;
        if(!$error['flag']){
            //发生错误
            unset($error['flag']);
            $str = '';
            foreach($error as $k=>$v){
                $s = "【".implode('】,【',$v).'】';
                $str.='第'.($k+1).'行'.$s.'<hr>';
            }
            $result['error'] = $str;
            Util::jsonExit($result);
        }
        $order_sns=array();
        //如果支付方式是财务备案将支付状态改成财务备案，但如果是京东部门且含有彩钻、裸钻商品，则为部分付款（支付定价）
        $payment_list = $this->getPaymentsBeiAn();
        foreach($datalist as $j=>$data) {
            $data = array_map('trim',$data);
            $data['kezis'] = str_replace('\\','a01',$data['kezis']);
            $data['kezis'] = str_replace('\'','a02',$data['kezis']);
            $data['kezis'] = str_replace('"','a03',$data['kezis']);

            $order_sn = $orderModel->getOrderSn();
            $order_sns[$j]=$order_sn;

            $user_info = array();
            $user_info['member_name'] = $data['customer'];
            $user_info['member_phone'] = $data['mobile'];
            $user_info['department_id'] = $order_department;
            $user_info['customer_source_id'] = $customer_source;

            // 地址
            $user_address = array();
            $province_id = $this->getRegionId($data['provi'],1);
            $city_id = $this->getRegionId($data['city'],2);
            if ($data['region']!='') {
                $district_id = $this->getRegionId($data['region'], 3);
            }else{
                $district_id=$regional_id;
            }
            $user_address['consignee'] = $data['customer'];
            $user_address['tel'] = $data['mobile'];
            $user_address['country_id'] = 1;
            $user_address['province_id'] = $province_id;
            $user_address['city_id'] = $city_id;
            $user_address['regional_id'] = $district_id;
            $user_address['address'] = $data['address'];

            // 订单
            $order = array();
            $order['order_sn'] = $order_sn;
            $order['consignee']=$data['customer'];
            $order['mobile']=$data['mobile'];
            $order['order_status'] = $order_status;
            $order['order_pay_type'] = $pay_type;
            $order['department_id'] = $order_department;
            $order['customer_source_id'] = $customer_source;
            $order['create_time'] = date("Y-m-d H:i:s");
            $order['create_user'] = $_SESSION['userName'];
            $order['modify_time'] = date("Y-m-d H:i:s");
            $order['order_remark'] = $data['postscripts'];
            $order['is_delete'] = 0;
            $order['is_xianhuo'] = in_array($j,$is_xianhuof)?0:1;
            $order['is_zp']=0;//是否是赠品单
            $order['referer'] = "批量导单";
            $order['delivery_status']=1;
            $order['order_pay_status'] = $order_pay_status = 1;
            $order['is_real_invoice'] = 1;
            if ($order_status==2 && !empty($payment_list[$pay_type])) {
                $order['order_pay_status'] = $order_pay_status = 4;
                $order['pay_date']=date("Y-m-d H:i:s",time());//第一次付款时间
                if ($order['is_xianhuo'] == 1) {
                    $order['delivery_status'] = 2;
                }
            }

            //发票
            $title = $data['invoice_title'];
            $invoice=array();
            $invoice['invoice_amount'] = 0;
            $invoice['is_invoice'] = '0';
            $invoice['create_time'] = date("Y-m-d H:i:s");

            //BOSS-1393 手工录入订单及手工维护快递公司，制单时间为1月16号-2月4号的顾客订单，不允许选择中通快递，制单时间为1月17号-2月4号的顾客订单，不允许选择圆通速递，快递公司自动变更，制单时间为1月16号-2月4号的顾客订单，以前程序自动识别为中通快递的自动变更为顺丰速运，不允许为中通快递；制单时间为1月17号-2月4号的顾客订单，以前程序自动识别为圆通速递的自动变更为顺丰速运，不允许为圆通速递，以下位置管控：
            if($order['create_time'] >= '2019-01-26 00:00:00' && $order['create_time'] <='2019-02-12 23:59:59' && $express_id == 19){
                $express_id = 4;
            }/*
            if($order['create_time'] >= '2017-01-19 00:00:00' && $order['create_time'] <='2017-02-04 23:59:59' && $express_id == 12){
                $express_id = 4;
            }
*/
            //配送地址
            $peisong = array();
            $peisong['consignee'] = $data['customer'];
            $peisong['express_id'] = $express_id;//快递公司id
            $peisong['country_id'] = 1;
            $peisong['province_id'] = $province_id;
            $peisong['city_id'] = $city_id;
            $peisong['regional_id'] = $district_id;
            $peisong['address'] =  $data['address'];
            $peisong['tel'] = $data['mobile'];
            $peisong['zipcode'] = $data['code'];
            $peisong['email']='';//?邮箱
            $peisong['freight_no']='';//? 快递单号
            $peisong['shop_type']=$shop_type;//?体验店类型
            $peisong['shop_name']=$shop_name;//?体验店名称
            $peisong['goods_id']=0;//?商品id
            $peisong['distribution_type']=$distribution_type;//总公司到客户
            foreach($peisong as $k => $v){
                $peisong[$k] = str_replace("'"," ",$v);
            }

            //插入用户
            $user_id = $this->getMemberId($user_info);
            //插入用户地址
            $this->GetMemberaddress($user_id,$user_address);

            //插入订单
            $order['user_id'] = $user_id;
            $all_data = array('order' => $order, 'invoice'=>$invoice,'address'=>$peisong);
            $order_id = $orderModel->makeEmptyOrder($all_data);
            $peisong_fee = $data['peisongfees'];

            // 赠品保存：根据数量拆分多个插入
            $zpdata = $this->insertGifts($data, $order_id);
            $zp_goods_amount = $zpdata['zp_goods_amount'];
            $zp_favorable_price = $zpdata['zp_favorable_price'];

            // 商品保存：根据数量拆分多个插入
            $total_goods_price =  $this->insertManyGoods($data, $order_id, $qibanList, $order_sn);
            $res = $total_goods_price>0;

            if($order_id){
                //订单金额
                $order_amout = $total_goods_price + $peisong_fee;
                $update_money = array();
                $update_money['order_id'] = $order_id;
                $update_money['order_amount'] = $order_amout;
                $update_money['money_paid'] =0;
                $update_money['money_unpaid'] =$order_amout;
                $update_money['shipping_fee'] = $peisong_fee;
                $update_money['goods_amount'] = $total_goods_price+$zp_goods_amount;
                $update_money['favorable_price'] = $zp_favorable_price;
                $orderModel->UpdateOrderAccount($update_money);
                // 发票：改订单id等信息
                if($title){
                    $invoice = array();
                    $invoice['order_id'] =$order_id;
                    $invoice['is_invoice'] = 1;
                    $invoice['invoice_title'] = $data['invoice_title'];
                    $invoice['taxpayer_sn'] = $data['taxpayer_sn'];
                    $invoice['invoice_amount'] = $data['invoice_amount'];
                    $orderModel->updateBatchOrderInvoice($invoice);
                }
            }
            if($order_id){
                $t='';
                $payment_list = $this->getPaymentsBeiAn();
                if($order_status==2 && $order['is_xianhuo']==0 && !empty($payment_list[$pay_type])){
                    $t=$this->allow_buchan(array('id'=>$order_id));
                    if($t){
                        $order_status=2;
                        $order_pay_status=4;
                    }
                }

                //操作日志
                $ation['order_status'] = $order_status;
                $ation['order_id'] = $order_id;
                $ation['shipping_status'] = 1;
                $ation['pay_status'] =$order_pay_status;
                $ation['create_user'] = $_SESSION['userName'];
                $ation['create_time'] = date("Y-m-d H:i:s");
                $ation['remark'] = "批量导入的单据";
                $orderActionModel->saveData($ation, array());
                if($t){
                    //操作日志
                    $ation['order_status'] = $order_status;
                    $ation['order_id'] = $order_id;
                    $ation['shipping_status'] = 1;
                    $ation['pay_status'] = $order_pay_status;
                    $ation['create_user'] = $_SESSION['userName'];
                    $ation['create_time'] = date("Y-m-d H:i:s");
                    $ation['remark'] = $order_sn."订单允许布产成功！布产单号为：".$t;
                    $orderActionModel->saveData($ation, array());
                }
            }
            //绑定下架
            $orderModel->Bindxiajia($order_id);
        }

        $str='';
        foreach($order_sns as $ks=>$vs){
            $str.=$vs.'<br/>';
        }
        $result['content'] = $str;
        if($res !== false) {
            $result['success'] = 1;
        } else {
            $result['error'] = '添加失败';
        }
        Util::jsonExit($result);
    }

    /**
     * 多行订单合并，字段不要随便改动，这里用的静态地址
     * 商品合并字段：货号id，编号sn，名称，数量，备注，指圈，单价，重量，净度，颜色，金料，金料颜色，金重，刻字,证书号
     * goods_ids,goods_sns,goods_names,goods_nums,postscripts,zhiquans,danjias,zuandaxiaos,zuan_jindus,zuan_yanses,jinliaos,jinzhongs,kezis,zhengshuhaos
     * 赠品合并字段：赠品货号，赠品款号，赠品数量，赠品备注，赠品指圈
     * zp_ids,zp_s,zp_nums,zp_remark_s,zp_zhiquan
     * 订单合并字段：发票金额
     * */
    private function mergeGoods(&$data, $row) {
        // 订单商品合并
        //$data['goods_ids'] = trim($data['goods_ids'], '|').'|'.trim($row['goods_ids'], '|');
        if(!empty($row['goods_ids'])){
            $data['goods_ids'] = trim($data['goods_ids'], '|').'|'.trim($row['goods_ids'], '|');
        }else{
            $data['goods_ids'] = trim($data['goods_ids'], '|').'|0';         
        }
        $data['goods_sns'] = trim($data['goods_sns'], '|').'|'.trim($row['goods_sns'], '|');
        $data['goods_names'] = trim($data['goods_names'], '|').'|'.trim($row['goods_names'], '|');
        $data['goods_nums'] = trim($data['goods_nums'], '|').'|'.trim($row['goods_nums'], '|');
        if(!empty($row['specials'])) {
            $data['specials'] = trim($data['specials'], '|').'|'.trim($row['specials'], '|'); // 商品备注
        }else{
            $data['specials'] = trim($data['specials'], '|').'| '; 
        }
        if(!empty($row['zhiquans'])){     
            $data['zhiquans'] = trim($data['zhiquans'], '|').'|'.trim($row['zhiquans'], '|');
        }else{
            $data['zhiquans'] = trim($data['zhiquans'], '|').'| '; 
        }
        $data['danjias'] = trim($data['danjias'], '|').'|'.trim($row['danjias'], '|');
        //主石单颗重
        if(!empty($row['zuandaxiaos'])) {
           $data['zuandaxiaos'] = trim($data['zuandaxiaos'], '|').'|'.trim($row['zuandaxiaos'], '|');
        }else{
            $data['zuandaxiaos'] = trim($data['zuandaxiaos'], '|').'|0'; 
        }
        //主石粒数
        if(!empty($row['zhushi_nums'])) {
            $data['zhushi_nums'] = trim($data['zhushi_nums'], '|').'|'.trim($row['zhushi_nums'], '|');
        }else{
            $data['zhushi_nums'] = trim($data['zhushi_nums'], '|').'|0';
        }
        if(!empty($row['zuan_jindus'])) { 
            $data['zuan_jindus'] = trim($data['zuan_jindus'], '|').'|'.trim($row['zuan_jindus'], '|');
        }else{
            $data['zuan_jindus'] = trim($data['zuan_jindus'], '|').'| ';  
        }
        if(!empty($row['zuan_yanses'])) {
            $data['zuan_yanses'] = trim($data['zuan_yanses'], '|').'|'.trim($row['zuan_yanses'], '|');
        }else{
            $data['zuan_yanses'] = trim($data['zuan_yanses'], '|').'| '; 
        }
        if(!empty($row['jinliaos'])) {
            $data['jinliaos'] = trim($data['jinliaos'], '|').'|'.trim($row['jinliaos'], '|');
        }else{
            $data['jinliaos'] = trim($data['jinliaos'], '|').'| '; 
        }
        if(!empty($row['jinses'])) {
            $data['jinses'] = trim($data['jinses'], '|').'|'.trim($row['jinses'], '|');
        }else{
            $data['jinses'] = trim($data['jinses'], '|').'| '; 
        }
        if(!empty($row['jinzhongs'])) {
            $data['jinzhongs'] = trim($data['jinzhongs'], '|').'|'.trim($row['jinzhongs'], '|');
        }else{
            $data['jinzhongs'] = trim($data['jinzhongs'], '|').'| '; 
        }
        if(!empty($row['kezis'])) {
            $data['kezis'] = trim($data['kezis'], '|').'|'.trim($row['kezis'], '|');
        }else{
            $data['kezis'] = trim($data['kezis'], '|').'| '; 
        } 
        if(!empty($row['zhengshuhaos']))  {
            $data['zhengshuhaos'] = trim($data['zhengshuhaos'], '|').'|'.trim($row['zhengshuhaos'], '|');
        }else{
            $data['zhengshuhaos'] = trim($data['zhengshuhaos'], '|').'| ';
        }
        if(!empty($row['zhengshuleibies'])){
            $data['zhengshuleibies'] = trim($data['zhengshuleibies'], '|').'|'.trim($row['zhengshuleibies'], '|');
        }else{
            $data['zhengshuleibies'] = trim($data['zhengshuleibies'], '|').'| ';
        }
        if(!empty($row['biaomiangongyis'])){
            $data['biaomiangongyis'] = trim($data['biaomiangongyis'], '|').'|'.trim($row['biaomiangongyis'], '|');
        }else{
            $data['biaomiangongyis'] = trim($data['biaomiangongyis'], '|').'| ';
        }
        if(!empty($row['xiangkou'])){
            $data['xiangkou'] = trim($data['xiangkou'], '|').'|'.trim($row['xiangkou'], '|');
        }else{
            $data['xiangkou'] = trim($data['xiangkou'], '|').'| ';
        }
        // 赠品合并
        if(!empty($row['zp_s'])){
           $data['zp_s'] = $data['zp_s'].'|'.$row['zp_s'];
           $data['zp_nums'] = $data['zp_nums'].'|'.$row['zp_nums'];
           $data['zp_ids'] = $data['zp_ids'].'|'.$row['zp_ids'];
           $data['zp_remark_s'] = $data['zp_remark_s'].'|'.$row['zp_remark_s'];
           $data['zp_zhiquan'] = $data['zp_zhiquan'].'|'.$row['zp_zhiquan'];
           
        }
        
        // 发票累加
        $data['invoice_amount'] += $row['invoice_amount'];
        // 外部单号合并
        $data['out_order_sn'] = trim($data['out_order_sn'], '|').'|'.trim($row['out_order_sn'], '|');
    }

    public function getCustomerSources() {
        //客户来源
        $CustomerSourcesModel = new CustomerSourcesModel(1);
        $CustomerSourcesList = $CustomerSourcesModel->getCustomerSourcesList("`id`,`source_name`");
        $this->assign('customer_source_list', $CustomerSourcesList);
    }

    //获取地址
    public function getRegionId($name,$type){
        $region = new RegionModel(1);
        return $region->getRegionByName($name,$type);
    }
    
    //检查款号是否存在
    public function checkStyleSn($data){
        $error = 1;
        $error_info = array();
        $styleModel = new ApiStyleModel();
        foreach ($data as $val){
            $style_sn = $val['style_sn'];
            $res = $styleModel->getStyleInfo($style_sn);
            if($res['data']['error']==0){
                $error_info[] = $val;
                $error = 0;
            }else{
                $style_data= $res['data']; 
            }
        }
        return array('error'=>$error,'data'=>$error_info,'style'=>$style_data);
    }
    //用户
    public function getMemberId($data) {
         //获取会员id,调接口
        $user_id = 0;
        $mobile = $data['member_phone'];
        $user_name = $data['member_name'];
        $where = array('member_phone' => $mobile);
        $apiModel = new ApiMemberModel();
        $user_info = $apiModel->getMemberByPhone($where);
        //当没有此用户时，重新创建一个用户
        if ($user_info['error']==1) {
            $new_user_data = array(
                'member_name' => $user_name,
                'member_phone' => $mobile,
                'member_age' => 20,
                'member_type' => 1,
                'department_id' => $data['department_id'],
                'customer_source_id' => $data['customer_source_id'],
            );
            $res = $apiModel->createMember($new_user_data);
            if ($res['error'] > 0) {
                 $user_id = 0;
            }else{
                $user_id = $res['data'];
            }
        } else {
            $user_id = $user_info['data']['member_id'];
        }
        return $user_id;
    }
    
    //插入用户地址:写接口
    public function addUserAddress($data) {
        //AddMemberAddressInfo
        $memberModel = new ApiMemberModel();
        $memberModel->AddMemberAddressInfo($data);
    }

    public function downcsv(){
        $title = array(
            '姓名（*）',
            '电话',
            '省（*）',
            '市（*）',
            '地区',
            '地址',
            '邮编',
            '产品id',
            '产品编号（*）',
            '产品名称',
            '主石单颗重',
            '主石粒数',
            '主石颜色(大写英文,无,D,E,F,G,H,I,I-J,J,K,K-L,M)',
            '主石净度(大写英文,无,FL,IF,VVS,VVS1,VVS2,VS,VS1,VS2,SI,SI1,SI2)',
            '金重',
            '金料',
            '金料颜色',
            '产品单价（*）',
            '产品数量（*）',
            '指圈号（*）',
            '配送费（*）',
            '订单金额',
            '表面工艺',
            '是否特殊处理',
            '订单备注',
            '外部订单号(50左右)（*）',
            '赠品款号',
            '赠品数量',
            '赠品备注',
            '刻字内容（特殊符号按照标准填写：[&符号] 、[间隔号]、 [空心]、 [实心]、 [小数点]、 [心心相印]、 [一箭穿心]）',
            '发票抬头',
            '纳税人识别号',
            '订单发票金额',
            '订单类型',
            '货号',
            '是否一款多货（只限期货 1为是0为不是）',
			'SKU编码',
            '赠品id',
            '赠品指圈',
            '证书号',
            '证书类型',
            '镶口',
            '单身-项次（数字+符号）',
            '品号',
        );

        Util::downloadCsv("批量导单".time(),$title,'');
    }

    public function getSource(){
        $channelid =_Post::getInt('order_department');
        $model = new CustomerSourcesModel(1);
        $res = $model->getSourceBychanelId($channelid);
        if(!empty($res)){
            $res= array_column($res,'source_name','id');
        }else{
            $res = array_column($model->getSources(),'source_name','id');
        }
        $this->render('source_option.html',array('res'=>$res));

    }

//指圈
    protected  function finger($val){
        if(!is_numeric($val)||$val<0){
            return 1;
        }
        return 2;
    }
//数量
    protected  function goodsnum($val){
        if(!is_numeric($val)||$val<0){
            return 1;
        }
        return 2;
    }
//价格
    protected  function price($val){
        if(!Util::isNum($val)||$val<0){
           return 1;
        }

        if(strpos($val,'.')!=false){
            //如果有小数点就鉴定他小数点几位
            $res = explode('.',$val);
            if(strlen($res[1])>2){
                return 1;
            };
        }
        return 2;
    }
//钻石大小
    protected  function stone($val){
        if(!Util::isNum($val)){
           return 1;
        }
        return 2;
    }
    //赠品校验
    protected  function gift($val){
        if(!array_key_exists((int)$val,$this->gifts)){
            return 1;
        }
        return 2;
    }

    // 插入商品-根据数量拆成多条记录
    private function insertManyGoods($data, $order_id, $qibanList, $order_sn){
        $apiPurchModel = new ApiPurchaseModel();
        $orderModel = new BaseOrderInfoModel(27);

        if(empty($data['goods_ids'])&&($data['ismultistyle']==1)) {
            $goods_id_list = array();
        } else {
            $goods_id_list = explode("|", $data['goods_ids']);
        }
        // goods info
        $goods_sn_list = explode("|", $data['goods_sns']);
        $goods_name_list = explode("|", $data['goods_names']);
        $goods_num_list = explode("|", $data['goods_nums']);
        $finger_list = explode("|", $data['zhiquans']);
        $goods_price_list = explode("|", $data['danjias']);
        $cart_list = explode("|", $data['zuandaxiaos']);
        $zhushinum_list = explode("|", $data['zhushi_nums']);
        $clarity_list = explode("|", $data['zuan_jindus']);
        $color_list = explode("|", $data['zuan_yanses']);
        $caizhi_list = explode("|", $data['jinliaos']);
        $jinse_list = explode("|", $data['jinses']);
        $jinzhong_list = explode("|", $data['jinzhongs']);
        $kezi_list = explode("|", $data['kezis']);
        $special_list = explode("|", $data['specials']);
        $zhengshuleibie_list = explode("|", $data['zhengshuleibies']);
        $zhengshuhao_list = explode("|", $data['zhengshuhaos']);
        $biaomiangongyi_list = explode("|", $data['biaomiangongyis']);
        // order info
        $out_order_sn_list = explode("|", $data['out_order_sn']);
        $xiangkou_list = explode("|", $data['xiangkou']);
        $ds_xiangci_list = explode("|", $data['ds_xiangci']);
        $pinhao_list = explode("|", $data['pinhao']);
        
        $zhengshuleibie_list = explode("|", $data['zhengshuleibies']);
        $zhushinum_list = explode("|", $data['zhushi_nums']);
        
        $consignee = $data['customer'];
        $datetime = date("Y-m-d H:i:s");

        $total_goods_price = 0;
        foreach($goods_sn_list as $g_key=>$goods_sn){
            $goods_tal = intval($goods_num_list[$g_key]);
            if($goods_tal <=0) $goods_tal = 1;

            for($h=1; $h<=$goods_tal; $h++){
                $goods = array();
                if(!empty($goods_id_list[$g_key])){
                    $goods['is_stock_goods'] = 1;
                    $goods['goods_id'] = $goods_id_list[$g_key];
                }else{
                    $goods['is_stock_goods'] = 0;
                    $goods['goods_id'] ='';
                }
                if($goods_sn == 'DIA'){
                    $goods['goods_type'] = 'lz';
                }elseif($goods_sn == 'CAIZUAN'){
                    $goods['goods_type'] = 'caizuan_goods';
                }elseif(preg_match('/^\d*$/',$goods_sn)){
                    $goods['goods_type'] = 'qiban';
                }else{
                    $goods['goods_type'] = 'style_goods';
                }

                $goods_sn_mat = isset($goods_sn_list[$g_key]) ? $goods_sn_list[$g_key] : '';

                $goods['order_id'] =$order_id;
                $goods['goods_sn'] = isset($qibanList[$goods_sn_list[$g_key]]) ? $qibanList[$goods_sn_list[$g_key]]['kuanhao'] : $goods_sn_mat;
                $goods['ext_goods_sn'] = $goods_sn_mat;
                $goods['goods_name'] = isset($goods_name_list[$g_key]) ? $goods_name_list[$g_key] : '';
                $goods['cart'] = !empty($cart_list[$g_key]) ? trim($cart_list[$g_key]) : '0';
                $goods['zhushi_num'] = !empty($zhushinum_list[$g_key]) ? intval(trim($zhushinum_list[$g_key])) : '0';
                $goods['color'] = isset($color_list[$g_key])?trim($color_list[$g_key]):'';
                $goods['clarity'] = isset($clarity_list[$g_key])?trim($clarity_list[$g_key]):'';
                $goods['cert'] = isset($zhengshuleibie_list[$g_key])?trim($zhengshuleibie_list[$g_key]):'';
                $goods['zhengshuhao'] = isset($zhengshuhao_list[$g_key])?trim($zhengshuhao_list[$g_key]):'';
                $goods['jinzhong'] = isset($jinzhong_list[$g_key]) ? $jinzhong_list[$g_key] : '';
                $goods['caizhi'] = isset($caizhi_list[$g_key]) ? trim($caizhi_list[$g_key]) : '';
                $goods['jinse'] = isset($jinse_list[$g_key]) ? ($jinse_list[$g_key]=='无'?'':$jinse_list[$g_key]) : '';
                $goods['goods_price'] = isset($goods_price_list[$g_key]) ? $goods_price_list[$g_key] : '';
                $goods['face_work'] = isset($biaomiangongyi_list[$g_key]) ? $biaomiangongyi_list[$g_key] : '';
                $goods['zhiquan'] = isset($finger_list[$g_key]) ? trim($finger_list[$g_key]) : '';
                $goods['kezi'] = isset($kezi_list[$g_key]) ? trim($kezi_list[$g_key]) : '';
                $goods['xiangkou'] = isset($xiangkou_list[$g_key]) ? trim($xiangkou_list[$g_key]) : '';
                $goods['ds_xiangci'] = isset($ds_xiangci_list[$g_key]) ? $ds_xiangci_list[$g_key] : '';
                $goods['pinhao'] = isset($pinhao_list[$g_key]) ? $pinhao_list[$g_key] : '';
                if(!empty($special_list[$g_key])){
                    $goods['details_remark'] = str_replace(array(',',"'"),array('',''),$special_list[$g_key]);
                } else {
                    $goods['details_remark'] = '';
                }

                $goods['goods_count'] = 1;
                $goods['xiangqian'] = "工厂配钻，工厂镶嵌";
                $goods['create_time'] = $datetime;
                $goods['modify_time'] = $datetime;
                $goods['create_user'] = $_SESSION['userName'];
                $goods['cat_type'] = '0';
                $goods['product_type'] = '0';
                $goods['cut'] = '';
                $goods['details_status'] = 1;
                $goods['favorable_price'] =0;
                $goods['favorable_status'] = 1;
                $goods['is_zp']=0;
                $goods['is_finance']=2;
                $qiban_type_name = '';
                if(isset($qibanList[$goods_sn_list[$g_key]])){

                    $qiban_type_name = $qibanList[$goods_sn_list[$g_key]]['qiban_type'];
                }
                if($qiban_type_name == '有款起版'){
                    $qiban_type_mat = 1;
                }else if($qiban_type_name == '无款起版'){
                    $qiban_type_mat = 0;
                }else{
                    $qiban_type_mat = 2;
                }
                $goods['qiban_type']=$qiban_type_mat;

                //判断是现货钻 1、期货钻 2 boss_1287
                if($goods['goods_type'] == 'qiban' || $goods['goods_type'] == 'caizuan_goods'){//起版、彩钻默认是期货
                    $goods['dia_type'] = 2;
                }else{
                    if($goods['is_stock_goods'] == 1){//现货
                        $goods['dia_type'] = 1;
                    }elseif($goods['is_stock_goods'] == 0 && $goods['zhengshuhao'] == ''){//期货
                        $goods['dia_type'] = 1;
                    }elseif($goods['is_stock_goods'] == 0 && $goods['zhengshuhao'] != ''){
                        $diamondModel = new SelfDiamondModel(19);
                        $zhengshuhaot = str_replace(array("GIA", "EGL","AGL"), "", $goods['zhengshuhao']);
                        $check_dia = $diamondModel->getDiamondInfoByCertId($zhengshuhaot);
                        if(!empty($check_dia) && isset($check_dia['good_type'])){
                            if($check_dia['good_type'] == 1){
                                $goods['dia_type'] = 1;
                            }elseif($check_dia['good_type'] == 2){
                                $goods['dia_type'] = 2;
                            }else{
                                $goods['dia_type'] = 0;
                            }
                        }else{
                            $goods['dia_type'] = 1;
                        }
                    }else{
                        $goods['dia_type'] = 0;
                    }//判断是现货钻 1、期货钻 2
                }

                //print_r($goods);exit;
                $goods = array_map("trim",$goods);
                $res = $orderModel->addOderDetail($goods);
                //这里插入关联表
                foreach (array_unique($out_order_sn_list) as $item) {
                    if (!empty($item)) {
                        $orderModel->addRelOutOrder(array('order_id'=>$order_id,'out_order_sn'=>$item,'goods_detail_id'=>$res));
                    }
                }
                // 根据addtime更新起版信息
                if($goods['goods_type'] == 'qiban' && !empty($goods['goods_id'])){//&& empty($goods['goods_id']) 谭碧玉说去掉就必须去掉
                    //foreach($qibanList as $key => $val) {
                        $apiPurchModel->updatePurchaseGoodsInfo(
                            array('addtime'=>$goods['goods_id'],'opt'=>$_SESSION['userName'],'order_sn'=>$order_sn,'customer'=>$consignee)
                        );
                    //}
                }
                $total_goods_price += $goods_price_list[$g_key];
            }
        }
        return $total_goods_price;
    }
    // 插入赠品
    private function insertGifts($data, $order_id) {
        $zp_goods_amount = 0;
        $zp_favorable_price = 0;
        if(!empty($data['zp_s'])) {
            $gifts_id_list = explode("|", $data['zp_ids']);
            $gifts_sn_list = explode("|", $data['zp_s']);
            $gifts_num_list = explode("|", $data['zp_nums']);
            $gifts_remark_list = explode("|", $data['zp_remark_s']);
            $finger_list = explode("|", $data['zp_zhiquan']);
            $out_order_sn_list = explode("|", $data['out_order_sn']);
            $datetime = date("Y-m-d H:i:s");

            $orderModel = new BaseOrderInfoModel(27);
            foreach ($gifts_sn_list as $key=>$val){
                $goods['order_id'] = $order_id;
                $goods['goods_id'] = isset($gifts_id_list[$key]) ? $gifts_id_list[$key] : '';
                $goods['goods_sn'] = isset($gifts_sn_list[$key]) ? $gifts_sn_list[$key] : '';
                $goods['zhiquan'] = isset($finger_list[$key]) ? $finger_list[$key] : 0;
                $goods['details_remark'] = isset($gifts_remark_list[$key]) ? $gifts_remark_list[$key] : '';

                $gifts_info=$orderModel->selectzpinfo($gifts_sn_list[$key]);
                $goods['goods_name'] = isset($gifts_info['name']) ? $gifts_info['name'] : '';
                $goods['goods_price'] = isset($gifts_info['sell_sprice']) ? $gifts_info['sell_sprice'] : 0;
                $goods['favorable_price'] = isset($gifts_info['sell_sprice']) ? $gifts_info['sell_sprice'] : 0;
                $goods['is_finance'] = isset($gifts_info['is_xz']) ? $gifts_info['is_xz'] : '';

                $goods['create_time'] = $datetime;
                $goods['modify_time'] = $datetime;
                $goods['create_user'] = $_SESSION['userName'];
                $goods['ext_goods_sn'] = '';
                $goods['cart'] ='';
                $goods['xiangkou'] ='';
                $goods['zhushi_num'] ='0';
                $goods['color'] ='' ;
                $goods['clarity'] = '';
                $goods['zhengshuhao'] = '';
                $goods['cert'] = '';
                $goods['jinzhong'] ='';
                $goods['caizhi'] = '';
                $goods['jinse'] = '';
                $goods['is_stock_goods'] = 1;
                $goods['favorable_status'] = 3;
                $goods['kezi'] ='';
                $goods['goods_type'] = 'zp';
                $goods['cat_type'] = '0';
                $goods['product_type'] = '0';
                $goods['face_work'] = '';
                $goods['xiangqian'] = "无";
                $goods['cut'] = '';
                $goods['details_status'] = 1;
                $goods['is_zp']=1;
                $goods['qiban_type']=2;
                $goods['dia_type']=1;//boss_1287
                $goods['pinhao']='';//boss_1287
                $goods['ds_xiangci']='';//boss_1287
                // 赠品根据数量分多条数据插入订单商品表
                $goods_count = $gifts_num_list[$key];
                for ($i=1; $i<=$goods_count; $i++) {
                    $goods['goods_count'] = 1;
                    $zp_goods_amount += $gifts_info['sell_sprice'];
                    $zp_favorable_price += $gifts_info['sell_sprice'];
                    $res = $orderModel->addOderDetail($goods);
                    //这里插入关联表
                    foreach ($out_order_sn_list as $item) {
                        if (!empty($item)) {
                            $orderModel->addRelOutOrder(array('order_id'=>$order_id, 'out_order_sn'=>$item, 'goods_detail_id'=>$res));
                        }
                    }
                }
            }
        }
        return array('zp_goods_amount'=>$zp_goods_amount, 'zp_favorable_price'=>$zp_favorable_price);
    }

    public function allow_buchan($param) {
        $order_id = $param['id'];
        $model = new BaseOrderInfoModel($order_id, 27);
        $orderInfo = $model->getDataObject();
        $detailModel = new AppOrderDetailsModel(27);
        $order_detail_data = $detailModel->getGoodsByOrderId(array('order_id'=>$order_id,'is_stock_goods'=>0));
        $res=$this->AddBuchanDan($orderInfo,$order_detail_data);
        $_model = new BaseOrderInfoModel($order_id, 28);
        $_model->setValue('effect_date', date("Y-m-d H:i:s"));
        $_model->setValue('buchan_status', 2);//变成允许布产
        $_model->save();
        if($res['data']){
        	/*
            $detailModel->updateOrderDetailsBcidByOrder_id($order_id,$res['data'][0]['buchan_sn']);
            return $res['data'][0]['buchan_sn'];
            */
        	$a = $res['data'];
        	$detailsModel = new AppOrderDetailsModel(28);
        	$pdo28 = $detailsModel->db()->db();
        	try{
        		$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT, 0); //关闭sql语句自动提交
        		$pdo28->beginTransaction(); //开启事务
        		foreach($a as $va){
        			$res1=$detailsModel->updateOrderDetailsBcidById($va['id'], $va['buchan_sn']);
        			if(!$res1){
        				$pdo28->rollback(); //事务回滚
        				$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
        				$result['error'] = "商品明细Id".$va['id']."：布产操作失败";
        				return $result;
        			}
        		}
        	
        		$pdo28->commit(); //事务提交
        		$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动提交
        	}
        	catch(Exception $e){//捕获异常
        		//  print_r($e);exit;
        		$pdo28->rollback(); //事务回滚
        		$pdo28->setAttribute(PDO::ATTR_AUTOCOMMIT,1); //开启sql语句自动回滚
        		$row['error']=1;
        		$row['data']= "数据异常，布产操作失败。".$e;
        	
        	
        	}
        	
        	$buchan_sn = implode(",",array_column($a,'final_bc_sn'));
        	return $buchan_sn; 
        }
    }
    public function AddBuchanDan($orderinfo,$detail_goods){
        $order_id           = $orderinfo['id'];
        $order_sn			= $orderinfo['order_sn'];
        $consignee			= $orderinfo['consignee'];
        $customer_source_id = $orderinfo['customer_source_id'];
        $department_id		= $orderinfo['department_id'];
        $order_remark = $orderinfo['order_remark'];

        $out_order_sn = '';//boss_1246
        if($customer_source_id == 2946){
            $model = new BaseOrderInfoModel(27);
            $ret = $model->getOurOrderSn($order_id);
            if(!empty($ret)){
                $out_order_sn = $ret[0]['out_order_sn'];
            }
        }
        
        //判断是否独立销售 is_alone
        if($orderinfo['is_xianhuo'] == 0){
            foreach ($detail_goods as $k=>$v){
                $detail_goods[$k]['is_alone'] = 0;
                if($v['goods_type'] == 'lz'){//单独售卖
                    $detail_goods[$k]['is_alone'] = 1;
                }elseif( empty($v['zhengshuhao']))  {//成品售卖
        
                }elseif( strripos($v['zhengshuhao'],'K') !== false || strripos($v['zhengshuhao'],'C') !== false)  {//成品售卖
        
                }else{
                    $orderDetailModel = new AppOrderDetailsModel(27);
                    $goods_info = $orderDetailModel->getGoodsInfoByZhengshuhao($v['zhengshuhao'],$v['id']);
                    if($goods_info){//单独售卖
                        $detail_goods[$k]['is_alone'] = 1;
                    }
                }
            }
        }
        
        //$processorApiModel = new ApiProcessorModel();
        $SelfProductInfoModel=new SelfProductInfoModel(13);
        $ProductInfoModel=new SelfProductInfoModel(14);
        //找到此订单是否已经存在布产的单
        $attr_names =array('cart'=>'主石单颗重','zhushi_num'=>'主石粒数','clarity'=>'净度','color'=>'颜色','cert'=>'证书类型','zhengshuhao'=>'证书号','caizhi'=>'材质','jinse'=>'金色','jinzhong'=>'金重','zhiquan'=>'指圈','kezi'=>'刻字','face_work'=>'表面工艺');
        if(!empty($detail_goods)){
            $goods_arr = array();
            foreach($detail_goods as $key=>$val){
                if($val['is_stock_goods'] == 1){
                    continue;
                }
                $detail_id = $val['id'];
                //查看此商品是否已经开始布产
                
                $buchan_info=$SelfProductInfoModel->GetGoodsRelInfo($detail_id);
                if(!empty($buchan_info)){
                	continue;
                }
                /*
                $buchan_info = $processorApiModel->GetGoodsRelInfo($detail_id);

                if(!empty($buchan_info['data'])){
                    continue;
                }*/
                $new_style_info = array();
                foreach ($attr_names as $a_key=>$a_val){
                    $xmp['code'] = $a_key;
                    $xmp['name'] = $a_val;
                    $xmp['value'] = $val[$a_key];
                    $new_style_info[]= $xmp;
                }
                //boss_1246
                if($customer_source_id == 2946){//鼎捷需要

                    $new_style_info[] = array('code'=>'p_sn_out', 'name'=>'外部单号', 'value'=>$out_order_sn);
                }

                $diamodel11 = new SelfDiamondModel(19);
                $cert_id2 = preg_replace('/[a-zA-Z]{0,10}/', '', $val['zhengshuhao']);
                $goods_type = $diamodel11->getGoodsTypeByCertId($val['zhengshuhao'],$cert_id2);
                if($val['zhengshuhao'] == ''){
                    $diamond_type = 1;
                }else{
                    if($goods_type ==2){
                        //期货钻
                         $diamond_type =2;
                    }else{
                        $diamond_type =1; 
                    }
                }
                
                $goods_num = $val['goods_count'];

                $goods_arr[$key]['origin_dia_type']=$diamond_type;
                $goods_arr[$key]['diamond_type']=$diamond_type;
                $goods_arr[$key]['p_id'] =	$detail_id;
                $goods_arr[$key]['p_sn'] =  $order_sn;
                $goods_arr[$key]['style_sn'] = $val['goods_sn'];
                $goods_arr[$key]['goods_name'] = $val['goods_name'];
                $goods_arr[$key]['bc_style'] = empty($val['bc_style'])?'普通件':$val['bc_style'];
                $goods_arr[$key]['xiangqian'] = $val['xiangqian'];
                $goods_arr[$key]['goods_type'] = $val['goods_type'];
                $goods_arr[$key]['cat_type'] = $val['cat_type'];
                $goods_arr[$key]['product_type'] = $val['product_type'];
                $goods_arr[$key]['num'] = $goods_num;
                $goods_arr[$key]['info'] = $val['details_remark'];
                $goods_arr[$key]['consignee'] = $consignee;
                $goods_arr[$key]['attr'] = $new_style_info;
                $goods_arr[$key]['customer_source_id'] = $customer_source_id;
                $goods_arr[$key]['channel_id'] = $department_id;
                $goods_arr[$key]['is_alone'] = $val['is_alone'];
				$goods_arr[$key]['caigou_info'] = $order_remark;
				$goods_arr[$key]['create_user']=$_SESSION['userName'];
                //$goods_arr[$key]['diamond_type'] = '0';
                //$goods_arr[$key]['qiban_type'] = '2';//默认
                //$goods_arr[$key]['origin_dia_type'] = '0';
            }
            //var_dump($goods_arr);exit;
            $res = array('data'=>'','error'=>0);
            //添加布产单
            if(!empty($goods_arr)){
            	$res = $ProductInfoModel->AddProductInfo($goods_arr);
               // $res = $processorApiModel->AddProductInfo($goods_arr);
            }
            return $res;
        }
    }

    
}
function trimIconv($v)
{
    return trim(iconv('gbk','utf-8',$v));
}
