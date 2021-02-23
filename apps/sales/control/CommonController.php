<?php
/**
 *  -------------------------------------------------
 *   @file		: CommonController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-03
 *   @update	:
 *  -------------------------------------------------
 */
class CommonController extends Controller
{
	protected $whitelist = array();
    //记得修改api里的维护数组
    protected $gifts = array(1=>'赠送珍珠耳钉',2=>'赠送S925银链',3=>'赠送黑皮绳',4=>'赠送红玛瑙手链',5=>'赠送白色手提袋',6=>'赠送情人节礼盒',7=>'赠送红绳',8=>'赠送手绳',9=>'赠送砗磲手链',10=>'赠送粉晶手链',11=>'赠送金条红包0.02g',12=>'赠送首饰盒',13=>'耳堵');
	 //protected $gifts=array();
    //财务备案的支付方式一旦选择这些支付方式就是支付状态就是财务备案的
    //protected $_fenter = array(112=>'纽海电子商务(上海)有限公司',253=>'国美在线电子商务有限公司',166=>'苏宁易购',78=>'中国移动积分',259=>'深圳星美汇商贸有限公司',245=>'京东结算京东官网在线支付',246=>'京东渠道自有物流货到付款',243=>'工商银行支付',50=>'
    //广发银行代收款',14=>'民生银行代收款',97=>'平安银行商城代收',129=>'交通银行积分代收款',30=>'建设银行代收款',62=>'招商银行代收款',250=>'深圳东方美宝网络科技有限公司',90=>'卓越代收款',258=>'无锡买卖宝信息技术有限公司',263=>'深圳国银通宝有限公司',264=>'北京陌陌科技有限公司',281=>'上海寺库电子商务有限公司',282=>'深圳市百变美金珠宝有限公司',283=>'上海陆家嘴国际金融资产交易市场股份有限公司',284=>'北京百度网讯科技有限公司',288=>'北京陌陌科技有限公司（淘宝C店）',287=>'上海微盟企业发展有限公司',289=>'上海尊溢商务信息咨询有限公司',290=>'南京钱宝信息传媒有限公司');
    //套装款号
    //1.爱绽放套装：
    //钻戒：KLRW027053
    //吊坠：KLPW026408
    //耳钉：KLDW027054
    //
    //2.怦然心动系列三件套装戒指
    //KLPW026546
    //KLPW026548
    //KLPW026550
    //
    //3.四叶草钻戒套装
    //蝴蝶结：KLRW026690
    //桃心（上）：KLRW026694
    //桃心（下）：KLRW026691
    protected $_coordinates=array('AZF'=>array('KLRW027053','KLPW026408','KLDW027054'),'PRXD'=>array('KLPW026546','KLPW026548','KLPW026550'),'SYC'=>array('KLRW026690','KLRW026694','KLRW026691'));

	protected function init ($act,$c)
	{
		if(!Util::isAjax())
		{
			if(!in_array($act,$this->whitelist))
			{
				Route::setAsAutoLaunch($_SERVER['REQUEST_URI'], _Request::getString("title", "销售管理"));
				header('Location:/index.php');
				die();
			}
		}
		else
		{
			if($c!='Main'   && $c !='AppOrderAddress' && $c!='getDiamandInfoAjax')
			{
				if(!Auth::getMenuAuth($c)){
					die($c.'无权操作');
				}
				if(!Auth::getOperationAuth($c,$act))
				{
					die('没有操作权限');
				}
			}
		}
		$this->dd = new DictView(new DictModel(1));
		$this->assign('dd',$this->dd);//数据字典
	}

    //校验不同渠道的不同权限的方法  给出当前渠道id 校验是否有操作权限
    //有权限返回true 没有权限返回权限名称

    public function checkSession($channelid){
        // print_r($_SESSION['__operation_p']);
        if($_SESSION['userType']==1){
            return true;
        }
        $pre = '/([A-Z]{1})/';
        $res =preg_replace($pre,'_$1',$_GET['con']);
        $con =substr($res,1);
        $act = $_GET['act'];
        $act =preg_replace($pre,'_$1',$act);
        $pricheck =strtoupper($con.'_'.$act.'_O');
        $sql = "SELECT `name` FROM `permission` WHERE code='".$pricheck."'";
        //这里会有找不到权限的情况 所以当你要鉴定权限的时候请先保证这个操作是管控的
        $res = DB::cn(1)->getOne($sql);
        //判断所传渠道是否在本人有权限的渠道内存在。没有，则提示
        $channellist = $this->ChannelListO();
        if(!in_array($channelid,$channellist))
        {
            return $res;
        }
        //判断是否有权限
        $pris = array_flip($_SESSION['__operation_p'][3][$channelid]);
        if(in_array($pricheck,$pris)){
            return true;
        }
        return $res;
    }


    function getchannelinfo(array $channerarray){
        $channelModel = new SalesChannelsModel(1);
        return $channelModel->getSalesChannel($channerarray);
    }
    
    //返回这个操作权限的的去渠道数组
    public function ChannelListO(){
        if($_SESSION['userType']==1 || strpos($_SESSION['qudao'], '163')!==false){//boss_1212 163表示总公司网销
            return true;
        }
        /*
         $pre = '/([A-Z]{1})/';
         $res =preg_replace($pre,'_$1',$_GET['con']);
         $con =substr($res,1);
         $act = $_GET['act'];
         $act =preg_replace($pre,'_$1',$act);
         $pricheck =strtoupper($con.'_'.$act.'_O');
    
         $pris = isset($_SESSION['__operation_p'][3]) ? $_SESSION['__operation_p'][3] : array();
    
         $channelarr=array();
         foreach($pris as $key=>$val){
         if(array_key_exists($pricheck,$val)){
         $channelarr[]=$key;
         }
         }
         return $channelarr;
         */
        $sql  = "select channel_id from cuteframe.user_channel where user_id = {$_SESSION['userId']} ";
        $channel_ids = DB::cn(1)->getAll($sql);
        return array_column($channel_ids, 'channel_id');
    }

    //校验属性的方法
    public function compareGoods($type,$data,$goods_id){

    }

    //判断有无地址信息有就不推数据没有就推一条默认地址
    public function GetMemberaddress($member_id,$data){
        $datam['member_id']=$member_id;
        $MemberApi = new AppOrderAddressModel(27);
        $res = $MemberApi->GetMemberAddressInfo($member_id);
       if($res['error']>0){
            //如果没查到向会员地址表插入一条默认地址
           $datam['mobile']=$data['tel'];
           $datam['mem_country_id']=$data['country_id'];
           $datam['mem_province_id']=$data['province_id'];
           $datam['mem_city_id']=$data['city_id'];
           $datam['mem_district_id']=$data['regional_id'];
           $datam['mem_address']=$data['address'];
           $datam['customer']=$data['consignee'];
           $datam['mem_is_def']=1;
           return  $MemberApi->AddMemberAddressInfo($datam);
       }
        return false;
    }
    //支付方式
    public function GetPaymentInfo(){
        $payModel=new PaymentModel(1);
        return  $res = array_column($payModel->getEnabled(),'pay_name','id');
    }
    //物流方式
    public function GetExp(){
        $exModel = new ExpressModel(1);
        return $res = array_column($exModel->getAllExpress(),'exp_name','id');
    }



	public function index ($params){
		die('forbidden');
	}
    
    
    //获取所有直营店 add liuri by 20150510
    public function getAllShop() {
        $shopModel = new ShopCfgModel(1);
        $res = $shopModel->getAllShopCfg(array('shop_type'=>1));
        return $res;
    }

    //取各支付方式的备案情况，财务备案的支付方式一旦选择这些支付方式就是支付状态就是财务备案的
    public function getPaymentsBeiAn() {
        //支付方式
        $paymentModel = new PaymentModel(1);
        $paymentList = $paymentModel->getEnabled();
        return array_column($paymentList,'is_beian','id'); // is_beian=1 or 0
    }

    /**
     * 替换刻字特殊字符串
     * @param type $kezi
     * @return string
     */
    public function replaceTsKezi($kezi='')
    {
        if($kezi!=''){

            //替换刻字特殊字符串
            $kezi = str_replace('a01','\\',$kezi);
            $kezi = str_replace('a02','\'',$kezi);
            $kezi = str_replace('a03','"',$kezi);
        }
        return $kezi;
    }
    
    protected function calc_dia_channel_price(&$diamond_list) {
        if (empty($diamond_list)) return;
        
    	if ($_SESSION['companyId'] == '666' || $_SESSION['companyId'] == '488' || $_SESSION['companyId'] == '623' || $_SESSION['companyId'] == '760') {
    		
    		$calc_func = function(&$d) {
    			if ($d['cert'] == 'HRD-S') {
					$x = 1.1;
                    /*
					if ($_SESSION['companyId'] == '623') {
						if ($d['carat'] >= 0.5) {
							$x = 1.15;
						} else {
							$x = 1.35;
						}
					}*/
					
                    if ($_SESSION['companyId'] == '623' || $_SESSION['companyId'] == '760'){
                        $x = 1.05;
                    }
					$d['shop_price'] = round($d['shop_price'] * $x);
    			}
    		};
    		
    		if (count($diamond_list) == count($diamond_list, 1)) {
    			$calc_func($diamond_list);
    		} else {
    			foreach ($diamond_list as &$d) {
    				$calc_func($d);
    			}
    		}
    		
    		return;
    	}
    	
        if (SYS_SCOPE != 'zhanting') return;  

    	$companyModel = new CompanyModel(1);
    	$company_type = $companyModel->select2("company_type","id={$_SESSION['companyId']}",3);
    	if ($company_type != '3') {
    		return;
    	}
    	
    	$sql = "select channel_id, s.channel_name from cuteframe.user_channel uc
    	inner join cuteframe.sales_channels s on s.id = uc.channel_id
    	inner join cuteframe.company c on s.company_id = c.id
    	where user_id = {$_SESSION['userId']} and c.id = {$_SESSION['companyId']}";
    	//echo $sql;die();
    	$channel_list = DB::cn(1)->getAll($sql);
    	if (empty($channel_list)) {
    		//exit("找不到销售渠道，无法计算");
    		if (count($diamond_list) == count($diamond_list, 1)) {
    			$diamond_list['shop_price_recalc'] = 0;
    		} else {
    			foreach ($diamond_list as &$d) {
    				$d['shop_price_recalc'] = 0;
    			}
    		}
    		
    		return;
    	}
    	
    	// TODO:  默认一个公司的所有渠道都是相同加价率
    	$channel_id = $channel_list[0]['channel_id'];
    	
    	$sql = "select * from front.diamond_channel_jiajialv where channel_id={$channel_id} and status = 1";
    	$channel_price_configs = DB::cn(99)->getAll($sql);
    	
    	$calc_func = function(&$d) use($channel_price_configs) {
    		if ($d['pifajia'] == 0) {
    			$d['shop_price_recalc'] = 0;
    			$d['shop_price'] = '--';
    			return;
    		}
    		
    		foreach ($channel_price_configs as $cfg) {
    			if ($cfg['cert'] == $d['cert'] && $d['good_type'] == $cfg['good_type'] && $cfg['carat_min'] <= $d['carat'] && $d['carat'] < $cfg['carat_max']) {
    				$d['shop_price'] = round($d['pifajia'] * $cfg['jiajialv']);
    				$d['shop_price_recalc'] = 1;
    				break;
    			}
    		}
    		
    		if (!isset($d['shop_price_recalc'])) {
    		    
    		    $lv =  $d['good_type'] == 1 ? 1.95 : 1.95;
    		    
    		    /**
    		     * 针对星耀： 如果没有设置加价率，按以下逻辑
    		     * 30-49分最低2.1；50-59分最低1.643；60-99分最低1.546；100-149分最低1.457；150分以上最低1.2
    		     */
    		    if ($d['cert'] == 'HRD-S') {
    		        if ($d['carat'] >= 1.5) {
    		            $lv = 1.2;
    		        } else if ($d['carat'] >= 1) {
    		            $lv = 1.457;
    		        } else if ($d['carat'] >= 0.6) {
    		            $lv = 1.546;
    		        } else if ($d['carat'] >= 0.5) {
    		            $lv = 1.643;
    		        } else if ($d['carat'] >= 0.3) {
    		            $lv = 2.1;
    		        }
    		    }
    		    
    			$d['shop_price'] = round($d['pifajia'] * $lv); //避免将成本价显示出来
    			$d['shop_price_recalc'] = 0;
    		}
    	};
    	
    	if (count($diamond_list) == count($diamond_list, 1)) {
    		$calc_func($diamond_list);
    	} else {
    		foreach ($diamond_list as &$d) {
    			$calc_func($d);
    		}
    	}
    }

    /**
    * 订单性质修改
    * 裸钻：总公司和下单门店和其他门店的现货都是现货单，其他情况的都是期货单
    *（订单最好单独一列标记是门店现货还是非门店还是总公司现货方便后期数据分析用）
    * 非裸钻：本门店的才是现货单，其他门店、总公司、需要生产的都是期货单
    */
    public function orderTypeEditor($goodsinfo, $self_company, $goods_company, $type='')
    {
        $goods_from = 0;//货品来源 1、本门店 2、总公司 3、其他门店
        if($type=='lz')
        {
            $goods_type = $goodsinfo['good_type'];
            if($goods_type == 1){//lz=>good_type 1现货，2期货
                if($self_company == $goods_company){
                    //下单门店现货
                    $goods_from = 1;
                }elseif($goods_company==58){
                    //总公司现货
                    $goods_from = 2;
                }else{
                    //其他门店现货
                    $goods_from = 3;
                }
            }
        }
        else//非裸钻
        {
            if($self_company == $goods_company){
                //下单门店现货
                $goods_from = 1;
            }elseif($goods_company==58){
                //总公司现货
                $goods_from = 2;
            }else{
                //其他门店现货
                $goods_from = 3;
            }
        }
        
        return $goods_from;
    }
}
?>
