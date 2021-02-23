<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseHuodongPrintCodeController.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-06-04 16:42:28
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseGoodsPrintCodeController extends CommonController
{
	protected $smartyDebugEnabled = false;
    protected $whitelist =array('printCode', 'uploadzdfile', 'downCode_zhubao', 'downCode_sujin', 'downCode_luoshi', 'error_csv','dow','zddow','uploadbzhfile');

	/**
	 *	index，搜索框
	 */
	public function index ($params)
	{
		$res = $this->ChannelListO();
        if ($res === true) {
            //获取全部的有效的销售渠道
            $SalesChannelsModel = new SalesChannelsModel(1);
            $channellist = $SalesChannelsModel->getSalesChannelsInfo("`id`,`channel_name`", '');
        } else {
            $channellist = $this->getchannelinfo($res);
        }
		
		if(SYS_SCOPE=='zhanting')
		{
			$zdyms = '货品最后一个已审核的P单的批发价 + 管理费 + 镶嵌保险费';
		}else{
			$zdyms = '货品的名义成本价 + 镶嵌保险费 ';
		}
		//print_r($channellist);
		$this->render('warehouse_goods_form.html',array(
			'bar'=>Auth::getBar(),
			'zdyms'=>$zdyms,
			'channelList'=>$channellist));
	}
	
	
	/**
	 * used:根据货品信息,计算价格  或者计算出保险费来
	 * parames: needbj 是否需要比价 1 (默认需要比价) 0 不需要比价
	 * return goodsinfo
	**/
	public function calc_goods_price($goodsinfo,$needbj=1,$jiajialv=1,$addnum=0,$templatedata=array())
	{
		$data = array();
		if(empty($goodsinfo))
		{
			return $data;
		}
		$model = new GoodsSalepolicyPriceModel(15);
		$result = array();
		$bjids = array();
		//如果不需要比价
		foreach($goodsinfo as $k=>$obj)
		{
			$baoxianfei = 0;
			$goods_id = $obj['goods_id'];
			
			//如果是非模板打标
			if(!isset($templatedata[$goods_id]))
			{
				$product = $obj['product_type1'];
				$xiankou = $obj['jietuoxiangkou'];
				$bx_xiankou = 0;
				if($xiankou>0){
					$bx_xiankou = $xiankou;	
				}else{
					$bx_xiankou = $obj['zuanshidaxiao'];
				}
				if($obj['tuo_type'] != 1){
					$baoxianfei= $model->getbaoxianfei($product,$bx_xiankou);
				}
				$obj['baoxianfei'] = $baoxianfei;
				$obj['jiajialv'] = $jiajialv;
				$obj['addnum'] = $addnum;
				$tmp_cb = $obj['mingyichengben'];
				if(SYS_SCOPE =='zhanting'){
					$tmp_cb = $obj['jingxiaoshangchengbenjia'];	
				}
				$obj['dabiao_price'] = round( ($tmp_cb + $baoxianfei) * $jiajialv + $addnum );	
			}else{
				$obj['baoxianfei'] = $baoxianfei;
				$obj['jiajialv'] = $jiajialv;
				$obj['addnum'] = $addnum;
				$obj['dabiao_price'] = round($templatedata[$goods_id]['buyout_price']);	
			}
			
			if($needbj>0)
			{
				//再去找价格对比吧,哥只能帮你到这里了
				//把需要比价的货品信息放在一个数组里面
				//array_push($bjids,$goods_id);
				$bjids[$goods_id] = $obj;
			}
			$result[$goods_id] = $obj;
			unset($goodsinfo[$k]);
		}
		if($needbj>0){
			$result['20170218bj'] = $bjids;
		}
		return $result;
	}
	
	
	/**
	 * used:根据货品信息,计算出保险费来
	 * return goodsinfo
	**/
	public function addbxftogoodsinfo($goodsinfo)
	{
		$data = array();
		if(empty($goodsinfo))
		{
			return $data;
		}
		$model = new GoodsSalepolicyPriceModel(15);
		$result = array();
		//如果不需要比价
		foreach($goodsinfo as $k=>$obj)
		{
			$baoxianfei = 0;
			$goods_id = $obj['goods_id'];
			$product = $obj['product_type1'];
			$xiankou = $obj['jietuoxiangkou'];
			$bx_xiankou = 0;
			if($xiankou>0){
				$bx_xiankou = $xiankou;	
			}else{
				$bx_xiankou = $obj['zuanshidaxiao'];
			}
			if($obj['tuo_type'] != 1){
				$baoxianfei= $model->getbaoxianfei($product,$bx_xiankou);
			}
			$obj['baoxianfei'] = $baoxianfei;
			$result[$k] = $obj;
			unset($goodsinfo[$k]);
		}
		return $result;
	}
	
	
	
		
	
	/**
	 * used ：根据货品信息获取满足条件的销售政策
	 * parames: goodsinfo  货品信息
	 * parames: channel    销售渠道id
	 * return : array      
	**/
	public function getpolicyginfo($g_infos,$channel=1,$policy_id=null)
	{
		$data = array();
		if(empty($g_infos)){
			return $data;
		}
		$g_infos = $this->addbxftogoodsinfo($g_infos);
		$no_price = array();
		//材质
        $goodsAttrModel = new GoodsAttributeModel(17);
        $caizhi = $goodsAttrModel->getCaizhiList();
        $yanse  = $goodsAttrModel->getJinseList();
		
		$GpriceModel = new GoodsSalepolicyPriceModel(15);
		$result_info = array();
		
		
		$low_ids = array();
		foreach($g_infos as $k=>$obj)
		{
			$where = array();
			if( SYS_SCOPE =='zhanting' ) {
				$chengben = $obj['jingxiaoshangchengbenjia']+$obj['baoxianfei'];	
			}else{
				$chengben = $obj['mingyichengben']+$obj['baoxianfei'];	
			}

			if($policy_id != null){
			    $where['policy_id'] = $policy_id;
            }
			
			$where['goods_id'] = $obj['goods_id'];
			$where['goods_sn'] = $obj['goods_sn'];
			$where['product_type'] = $obj['product_type1'];
			$where['cat_type'] = $obj['cat_type1'];
			$where['jietuoxiangkou'] = $obj['jietuoxiangkou'];
			$where['zuanshidaxiao'] = $obj['zuanshidaxiao'];
			$where['tuo_type'] = $obj['tuo_type'];
			$where['zhengshuleibie'] = $obj['zhengshuleibie'];
			$gid = $obj['goods_id'];
			//货品材质
			$caizhiname = $obj['caizhi'];
			$where['caizhi'] = $GpriceModel->getcaizhiid($caizhi,$caizhiname);
			$where['isXianhuo'] = 1;
			//获取一口价的销售政策
			$yikojiainfo = $GpriceModel->getyikoujiainfo($where,$channel);
			//如果有一口价了那么就不能再取默认的了
			if(!empty($yikojiainfo))
			{
				$isactive = 1;
				$salepolicyinfo = $GpriceModel->getsalepolicyinfo($where,$channel,$isactive);
			}else{
				//否则就取默认的销售政策
				$salepolicyinfo = $GpriceModel->getsalepolicyinfo($where,$channel);
			}
			
			//如果一口价没找到,销售政策也没找到,则提示这些货品没有找到销售政策
			if(empty($yikojiainfo) && empty($salepolicyinfo))
			{
				$no_price[$gid] = $obj;
				continue ;
				//array_push($no_price,$obj['goods_id']);
			}elseif(!empty($yikojiainfo) && !empty($salepolicyinfo))
			{
				$price = 0;
				//如果同时存在一口价和销售政策价,那么拿取最大的
				$yikoujia_p  = $yikojiainfo[0]['price'];
				$salepolicy_p = round(($chengben * $salepolicyinfo[0]['jiajia'])+ $salepolicyinfo[0]['sta_value']);
				if($yikoujia_p >= $salepolicy_p)
				{
					$obj['price'] = $yikoujia_p;
					$obj['policy_name'] = '一口价政策:'.$yikojiainfo[0]['policy_name'];
					$obj['jiajia'] = 1;
					$obj['sta_value'] = 0;
				}else{
					$obj['price'] = $salepolicy_p;
					$obj['policy_name'] = $salepolicyinfo[0]['policy_name'];
					$obj['jiajia'] = $salepolicyinfo[0]['jiajia'];
					$obj['sta_value'] = $salepolicyinfo[0]['sta_value'];
				}	
			}elseif(!empty($yikojiainfo) && empty($salepolicyinfo)){
				$obj['price'] = $yikojiainfo[0]['price'];
				$obj['policy_name'] = '一口价政策:'.$yikojiainfo[0]['policy_name'];
				$obj['jiajia'] = 1;
				$obj['sta_value'] = 0;	
			}elseif(empty($yikojiainfo) && !empty($salepolicyinfo)){
				$salepolicy_p = round(($chengben * $salepolicyinfo[0]['jiajia'])+ $salepolicyinfo[0]['sta_value']);
				$obj['price'] = $salepolicy_p;
				$obj['policy_name'] = $salepolicyinfo[0]['policy_name'];
				$obj['jiajia'] = $salepolicyinfo[0]['jiajia'];
				$obj['sta_value'] = $salepolicyinfo[0]['sta_value'];	
			}
			
			
			//最后 如果是非婚博会过来的 也就是需要比较的,存在直接计算的dabiao_price的  那就在和price进行对比
			
			//如果低于销售政策自动算价,则抛出错误
			if(isset($obj['dabiao_price'])){
				if($obj['price'] > $obj['dabiao_price'])
				{
					$tmp = '';
					//自动算价格出错,自己填写的加价率和固定值偏低了
					$tmp['gid'] =  $gid;
					$tmp['dabiaop'] = $obj['dabiao_price'];
					$tmp['price'] = $obj['price'];
					$tmp['policy_name'] = $obj['policy_name'];
					$low_ids[$gid] = $tmp;
				}
			}
			$result_info[$gid] = $obj;
			
			unset($g_infos[$k]);
			//print_r($yikojiainfo);
			//print_r($salepolicyinfo);
		}
		//价格出现低的数据
		$result_info['low_ginfo'] = $low_ids;
		$result_info['no_policy'] = $no_price;
		return $result_info;
	}
	
	/**
	 * 输出错误信息 
	**/
	public function printlowinfo($sale_g_infos)
	{
		$str= '';
		if(empty($sale_g_infos))
		{
			return $str;
		}
		foreach($sale_g_infos as $k=>$v)
		{
			$str .= '商品编号：'.$v['gid'].'根据填写的加价率算出来的价格是'.$v['dabiaop'].'低于销售政策'.$v['policy_name'].'计算出来的价格'.$v['price'].'<br/>';
		}
		return $str;
	}
	
	public function print_nopolicyinfo($g_infos)
	{
		$str= '';
		if(empty($g_infos))
		{
			return $str;
		}
		foreach($g_infos as $k=>$v)
		{
			$str .= '商品编号：'.$v['goods_id'].'没有找到产品线是'.$v['product_type1'].',款式分类是'.$v['cat_type1'].'的销售政策<br/>';
		}
		return $str;
	}
	
	

	/**
	 *	printCode,根据货号自动去拿取所需要的打标信息
	*/
    public function printCode($params)
	{
		$args = array(
            'mod'	=> _Request::get("mod"),
            'con'	=> substr(__CLASS__, 0, -10),
            'act'	=> __FUNCTION__,
            'down_info' => _Request::get("down_info"),
			'channel' => _Request::get("departmentid"),
            'goods_id'=> _Request::get("goods_id"),
			'jiajialv' => _Request::get("jiajialv"),
            'jiajianum' => _Request::get("jiajianum"),
            'daying_type'=>_Request::get("daying_type"),
            'type_t'=>_Request::get("type_t"),
            'policy_id'=>_Request::get("policy_id"),
			'isactive'=>_Request::get("isactive")
        );
		//这里只需要货号
		$gids = str_replace(' ','',$args['goods_id']);
		$gids = str_replace('，',',',$args['goods_id']);
		//如果操作人员正规填写的话 $where['goods_id_in'] 可以直接等于$gids,
		//就怕出现,,这样的  用数组过滤一遍 不至于sql出问题 
		$gids_arr = explode(',',$gids);
		$gids_arr = array_filter($gids_arr);
		$gids_arr = array_unique($gids_arr);
		
		if( !empty($gids_arr)){
			if(count($gids_arr) > 1 ){
				$where['goods_id'] = $gids_arr;
			}else{
				$where['goods_id'] = $gids_arr[0];
			}
		}
		/*(1) 当选择自定义打标时：
		不根据销售政策按照加价率打标，根据货号、加价率和累计系数计算建议零售价，
		备注：如果货品所在仓库是【婚博会备货库】，直接计算建议零售价。
      	如果货品所在仓库不是【婚博会备货库】，货品必须有对应的销售政策，打印价格不能低于销售政策里的价格*/
		
		//eg ： 150311501611 婚博会备货库
		$model = new WarehouseGoodsModel(21);
		//首先把婚博会备货库里面的货号拿出来,直接计算出价格
		
		//1.1首先需要检查是否所有货号都在商品列表里面存在
		$data_one = $model->getprintgoodsinfo($where);
		if(empty($data_one))
		{
			$err_txt = '在商品列表里面找不到货号为 "' .implode(',',$gids_arr). '" 的货品信息,请检查货号是否正确';
			$this->error_txt($err_txt,'找不到货号打标失败');
			die('在商品列表里面找不到货号为 "' .implode(',',$gids_arr). '" 的货品信息,请检查货号是否正确');
		}
		
		//获取搜索出来的货品id
		$data_ids = array_column($data_one,'goods_id');
		
		//搜索的goods_id  和 搜索结果的商品id 求差集  ，如果有差集说明这些货品没有在仓库里面找到货品，则提示
		$cj_data = array_diff($gids_arr,$data_ids);
		if(!empty($cj_data))
		{
			$err_txt = '在商品列表里面找不到货号为 "' .implode(',',$cj_data). '" 的货品信息,请检查货号是否正确';
			$this->error_txt($err_txt,'商品列表找不到货号打标失败');
			die('在商品列表里面找不到货号为 "' .implode(',',$cj_data). '" 的货品信息,请检查货号是否正确');
		}
		//如果都能找到,咱们再继续哈哈
		
		$type_t = $args['type_t'];
		$channel = $args['channel'];
		$policy_id = $args['policy_id'];

        //（2）   增加【展厅标签价】
        //（3） 根据输入货号打印商品列表里的展厅标签价，如果货号的展厅标签价为空或为0，系统提示货号“*** 没有展厅标签价无法打印”，所有的货号都需要一次性显示；

        if($type_t == 3 && SYS_SCOPE == 'zhanting'){
            $goods_chek = '';
            foreach ($gids_arr as $god_id) {
                $god_id = trim($god_id);
                if ($god_id == ""){
                  continue;
                }
                $chek_goods = $model->getGoodsByGoods_id($god_id);
                if($chek_goods['biaoqianjia'] == 0){
                    $goods_chek.= $god_id."|";
                }
            }

            if($goods_chek != ''){//货号“*** 没有展厅标签价无法打印”
                $error = "货号：".$goods_chek."没有展厅标签价无法打印";
                $this->error_txt($error, '货号没有标签价，打标失败');
                die('货号没有标签价，打标失败');
            }
        }
		
		//是否活动价99
		$ishuodongprice = $args['isactive']>0 ? $args['isactive']:0;
		
		/*
		goods_id,goods_sn,shoucun,changdu,jietuoxiangkou,zhushilishu,zuanshidaxiao,fushilishu,fushizhong,jinzhong,zongzhong,zhushijingdu,zhushiyanse,

zhengshuhao,caizhi,goods_name,pinpai
		*/
		
		//把货品所有的需要的信息都拿出来
		$needfields = 'goods_id,goods_sn,shoucun,changdu,zhushilishu,if(fushilishu>0,fushilishu,0)+if(shi2lishu>0,shi2lishu,0)+if(shi3lishu>0,shi3lishu,0) as fushilishu,fushizhong+shi2zhong+shi3zhong as fushizhong,jinzhong,zongzhong,zhushijingdu,zhushiyanse,
		zhengshuhao,zhengshuleibie,goods_name,pinpai,warehouse,mingyichengben,jingxiaoshangchengbenjia,tuo_type,caizhi,biaoqianjia,1 as isXianhuo';
		//拿取保险费需要的字段
		$needfields .=',product_type1,zuanshidaxiao,jietuoxiangkou';
		//匹配销售政策的时候要用
		$needfields .= ',cat_type1';
		if($type_t==0){
			$dbtitle = '自定义打标模式';
			//加快速度,把婚博会备货库的数据先拿出来,直接计算出价格
			$hbh_where = $where;;
			$hbh_where['ishbhwarehouse'] = 1;
			$hbh_infos =  $model->getprintgoodsinfo($hbh_where,$needfields);
			
			/********************  计算价格咯  ********************/
			$needbj = 0;//自定义打标,婚博会备货库不需要比价,直接计算
			$data_hbh_goods = $this->calc_goods_price($hbh_infos,$needbj,$args['jiajialv'],$args['jiajianum']);
			//print_r($data_hbh_goods);
			//echo '以上是婚博会的数据<br/>';
			//echo '********************************************<br/>';

			//非婚博会备货库的货品
			$hbh_where['ishbhwarehouse'] = 0;
			$fhbh_infos = $model->getprintgoodsinfo($hbh_where,$needfields);
			
			$needbj = 1;//自定义打标,非婚博会备货库需要比价
			$data_fhbh_goods = $this->calc_goods_price($fhbh_infos,$needbj,$args['jiajialv'],$args['jiajianum']);
			
			if(!empty($data_hbh_goods) && !empty($data_fhbh_goods))
			{
				//$allinfos = array_merge($data_hbh_goods,$data_fhbh_goods);
				$allinfos = $data_hbh_goods+$data_fhbh_goods;
			}elseif(!empty($data_hbh_goods)){
				$allinfos = $data_hbh_goods;
			}elseif(!empty($data_fhbh_goods)){
				$allinfos = $data_fhbh_goods;
			}
			if(isset($allinfos['20170218bj'])){
				//把需要比价的货品去查找对应的销售政策,然后把价格反回来
				$duibi_data = $this->getpolicyginfo($allinfos['20170218bj'],$channel,$policy_id);
				
				//如果存在货品没有销售政策的则体示打标失败
				if(!empty($duibi_data['no_policy'])){
					$nopic_str = $this->print_nopolicyinfo($duibi_data['no_policy']);
					if($nopic_str !='')
					{
						$this->error_txt($nopic_str,'找不到销售政策打标失败');
						die($nopic_str.'打标失败');
					}	
				}
				//有了政策我们再接着往下走
				if(!empty($duibi_data['low_ginfo'])){
					$err_str = $this->printlowinfo($duibi_data['low_ginfo']);
					if($err_str !='')
					{
						$this->error_txt($err_str,'价格低于自动算价打标失败');
						die($nopic_str.'打标失败');
					}
				}
				//走到这里说明比价通过
				unset($allinfos['20170218bj']);
			}
         
		}

		/*(2) 当选择根据销售政策打标时，根据货号和销售渠道找到对应的销售政策匹配建议零售价*/
		if($type_t==1)
		{
			$dbtitle = '自动算价打标模式';
			$g_infos = $model->getprintgoodsinfo($where,$needfields);
			$allinfos = $this->getpolicyginfo($g_infos,$channel,$policy_id);
			//如果存在货品没有销售政策的则提示打标失败
			if(!empty($allinfos['no_policy'])){
				$nopic_str = $this->print_nopolicyinfo($allinfos['no_policy']);
				if($nopic_str !='')
				{
					$this->error_txt($nopic_str,'找不到销售政策打标失败');
					die($nopic_str.'打标失败');
				}	
			}
		}
		
		//die();
		/*(3) 当选择时指定价打标时，不根据销售政策按照一口价打标 
		如下图在导入指定价打标数据下载模板，根据模板格式维护好数据，
		点击浏览上传，点击提交。点击打印条码打印
		备注：如果货品所在仓库是【婚博会备货库】，直接计算建议零售价。
      	如果货品所在仓库不是【婚博会备货库】，货品必须有对应的销售政策，打印价格不能低于销售政策里的价格*/
		if($type_t==2)
		{
			$dbtitle = '指定价打标模式';
			//2.1首先需要检查是否所有货号都在商品模板标签表里面存在
			$data_three = $model->getzdjdbgoodsinfo($where);
			if(empty($data_three))
			{
				$err_info ='在商品模板列表里面找不到货号为 "' .implode(',',$gids_arr). '" 的货品信息,请检查货号是否正确';
				$this->error_txt($err_info,'在商品模板列表里面找不到货号,打标失败');
				die('在商品模板列表里面找不到货号为 "' .implode(',',$gids_arr). '" 的货品信息,请检查货号是否正确');
			}
			//获取搜索出来的货品id
			$datathree_ids = array_keys($data_three);
			
			//搜索的goods_id  和 搜索结果的商品id 求差集  ，如果有差集说明这些货品没有在仓库里面找到货品，则提示
			$chaji_data = array_diff($gids_arr,$datathree_ids);
			
			if(!empty($chaji_data))
			{
				$err_info ='在模板标签列表里面找不到货号为 "' .implode(',',$chaji_data). '" 的货品信息,请核实后再打印';
				$this->error_txt($err_info,'在模板标签列表里面找不到货号,打标失败');
				die('在模板标签列表里面找不到货号为 "' .implode(',',$chaji_data). '" 的货品信息,请核实后再打印');
			}
			//如果都能找到,咱们再继续哈哈
			
			//其实到了这里以后我们只需要按照自定义标签的逻辑走即可，只不过价格不是算出来的而是取出来的
			
			//加快速度,把婚博会备货库的数据先拿出来,直接计算出价格
			$hbh_where = $where;;
			$hbh_where['ishbhwarehouse'] = 1;
			$hbh_infos =  $model->getprintgoodsinfo($hbh_where,$needfields);
			//print_r($hbh_infos);
			/********************  计算价格咯  ********************/
			$needbj = 0;//按照模板打标,婚博会备货库不需要比价,直接取warehouse_biaoqian里面的价格
			$data_hbh_goods = $this->calc_goods_price($hbh_infos,$needbj,$args['jiajialv'],$args['jiajianum'],$data_three);
			
			//非婚博会备货库的货品
			$hbh_where['ishbhwarehouse'] = 0;
			$fhbh_infos = $model->getprintgoodsinfo($hbh_where,$needfields);
			
			$needbj = 1;//按照模板打标,非婚博会备货库需要比价
			$data_fhbh_goods = $this->calc_goods_price($fhbh_infos,$needbj,1,0,$data_three);
			if(!empty($data_hbh_goods) && !empty($data_fhbh_goods))
			{
				$allinfos = array_merge($data_hbh_goods,$data_fhbh_goods);
			}elseif(!empty($data_hbh_goods)){
				$allinfos = $data_hbh_goods;
			}elseif(!empty($data_fhbh_goods)){
				$allinfos = $data_fhbh_goods;
			}
			
			if(isset($allinfos['20170218bj'])){
				//把需要比价的货品去查找对应的销售政策,然后把价格反回来
				$duibi_data = $this->getpolicyginfo($allinfos['20170218bj'],$channel,$policy_id);
				//如果存在货品没有销售政策的则体示打标失败
				if(!empty($duibi_data['no_policy'])){
					$nopic_str = $this->print_nopolicyinfo($duibi_data['no_policy']);
					if($nopic_str !='')
					{
						$this->error_txt($nopic_str,'找不到销售政策,打标失败');
						die($nopic_str.'打标失败');
					}	
				}
				//有了政策我们再接着往下走
				if(!empty($duibi_data['low_ginfo'])){
					$err_str = $this->printlowinfo($duibi_data['low_ginfo']);
					if($err_str !='')
					{
						$this->error_txt($err_str,'货品价格低于系统自动算价,打标失败');
						die($err_str.'打标失败');
					}
				}
				//走到这里说明比价通过
				unset($allinfos['20170218bj']);
			}	
		}


        if($type_t == 3){
            $allinfos_arr =  $model->getprintgoodsinfo($where,$needfields);
            foreach ($allinfos_arr as $jr) {
                $allinfos[$jr['goods_id']] = $jr;
            }
        }
		/*
			数组按照以下顺序拼装
			货号	款号	手寸	长度	镶口	主石粒数	主石重 副石粒数 副石重 主成色重 总重 净度 颜色 直营店钻石净度 证书号 主成色 名称 品牌 最新零售价 定制价
		*/
		$printstr = '';
		foreach($gids_arr as $v)
		{
			$obj = $allinfos[$v];
			$line = $this->printfunc($obj,$args['daying_type'],$ishuodongprice,$type_t);
			$printstr .= $line;
		}
		//上面是拼装的数据

		if($args['daying_type']==1)
		{
            $content = "货号,款号,手寸,长度,镶口,主石粒数,主石重,副石粒数,副石重,主成色重,总重,净度,颜色,直营店钻石净度,最新零售价,主石重与主石粒数,副石重与副石粒数,颜色与直营店净度,证书号,主成色,名称,品牌,定制价,展厅标签价,证书类型,是否支持定制\r\n";
			$content .= $printstr;
			header("Content-type:text/csv;charset=gbk");
			header("Content-Disposition:filename=" . iconv("utf-8", "gbk",date("Y-m-d")).$dbtitle."tiaoma.csv");
			header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
			header('Expires:0');
			header('Pragma:public');
			echo iconv("utf-8", "gbk//IGNORE", $content);
			exit;
		}
		
		if($args['daying_type']==2)
		{
			ini_set('memory_limit', '-1');
            set_time_limit(0);
            header("Content-Type: text/html; charset=gb2312");
            header("Content-type:aplication/vnd.ms-excel");
            header("Content-Disposition:filename=" . iconv('utf-8', 'gb2312', date("Y-m-d")).$dbtitle."tiaoma.xls");
            $exl_body = '
			<table border="1">
				<tr>
					<td style="text-align: center;">货号</td>
					<td style="text-align: center;">款号</td>
					<td style="text-align: center;">手寸</td>
					<td style="text-align: center;">长度</td>
					<td style="text-align: center;">镶口</td>
					<td style="text-align: center;">主石粒数</td>
					<td style="text-align: center;">主石重</td>
					<td style="text-align: center;">副石粒数</td>
					<td style="text-align: center;">副石重</td>
					<td style="text-align: center;">主成色重</td>
					<td style="text-align: center;">总重</td>
					<td style="text-align: center;">净度</td>
					<td style="text-align: center;">颜色</td>
					<td style="text-align: center;">直营店钻石净度</td>
					<td style="text-align: center;">最新零售价</td>
					<td style="text-align: center;">主石重与主石粒数</td>
					<td style="text-align: center;">副石重与副石粒数</td>
					<td style="text-align: center;">颜色与直营店净度</td>
					<td style="text-align: center;">证书号</td>
					<td style="text-align: center;">主成色</td>
					<td style="text-align: center;">名称</td>
					<td style="text-align: center;">品牌</td>
					<td style="text-align: center;">定制价</td>
                    <td style="text-align: center;">展厅标签价</td>
                    <td style="text-align: center;">证书类型</td>
                    <td style="text-align: center;">是否支持定制</td>
                </tr>';
			//连接主体信息
			$exl_body .= $printstr;
			//添加结尾标识
			$exl_body .= "</table>";
        	echo $exl_body;exit;
		}
		
		
		
		
		echo $str;
		die();
		print_r($allinfos);
		die();
		
		
		//根据提供的货号去商品列表查找 货号,如果货号不存在的话则踢出来
		$model = new WarehouseGoodsModel(21);
		$warehouse_ids = $model->getallgoodsid($gids_arr);
		
		
		
		
		
		
		
		//导出的数据格式为
		//货号 款号 手寸 长度 主石 主石粒数 主石重 副石 副石粒数 副石重 总重 净度 颜色 证书号 主石切工 主成色 名称 主成色重 总重 副成色 副成色重 品牌 一口价 建议零售价
		
		
		if(empty($gids_arr)){
			echo '货号请按照要求填写,填写谢谢';
		}
		
		
		
		$where['goods_id_in'] = implode("','",$gids_arr);
		$where['channel'] = $args['channel'];
		
		//打标目前只针对现货
		$model = new GoodsSalepolicyPriceModel(15);
		
		
		
		
		
		//($where,$page,$pageSize,$caizhi,$yanse);
		$data = $model->pageXianhuoList($where,1,count($gids_arr),$caizhi,$yanse);
		
		
		
		
		
		
		print_r($data);die();
		if(empty($data['data'])){
			print_r();	
		}
		foreach($data['data'] as $obj)
		{
			echo '货号：'.$obj['goods_id'].'_&nbsp;&nbsp;_';
			echo '款号：'.$obj['goods_sn'].'_&nbsp;&nbsp;_';
			echo '手寸：'.$obj['finger'].'_&nbsp;&nbsp;_';
			echo '长度：'.$obj['changdu'].'_&nbsp;&nbsp;_';
			echo '主石：'.$obj['zhushi'].'_&nbsp;&nbsp;_';
			echo '主石粒数：'.$obj['zhushilishu'].'_&nbsp;&nbsp;_';
			echo '主石重：'.$obj['zuanshidaxiao'].'_&nbsp;&nbsp;_';
			echo '副石：'.$obj['fushi'].'_&nbsp;&nbsp;_';
			echo '副石粒数：'.$obj['fushilishu'].'_&nbsp;&nbsp;_';
			echo '总重：'.$obj['zongzhong'].'_&nbsp;&nbsp;_';
			echo '净度：'.$obj['clarity'].'_&nbsp;&nbsp;_';
			echo '颜色：'.$obj['color'].'_&nbsp;&nbsp;_';
			echo '证书号：'.$obj['zhengshuhao'].'_&nbsp;&nbsp;_';
			echo '主石切工：'.$obj['cut'].'_&nbsp;&nbsp;_';
			echo '主成色：'.$obj['dbcaizhi'].'_&nbsp;&nbsp;_';
			echo '主成色重：'.'lly'.'_&nbsp;&nbsp;_';
			echo '总重：'.'lly'.'_&nbsp;&nbsp;_';
			echo '副成色：'.'lly'.'_&nbsp;&nbsp;_';
			echo '副成色重：'.'lly'.'_&nbsp;&nbsp;_';
			echo '品牌：'.$obj['pinpai'].'_&nbsp;&nbsp;_';
			echo '建议零售价：'.$obj['sale_price'].'<br/>';
			
		}
    }
	//有了数据之后开始达标
	public function printfunc($datainfo,$iscsv=1,$ishuodongprice=0,$label_price='')
	{
		$str = '';
		if(empty($datainfo)){
			return $str;	
		}
		$this->filerLabel($datainfo);

		/*
		货号	款号	手寸	长度	镶口	主石粒数	主石重 副石粒数 副石重 主成色重
		总重 净度 颜色 直营店钻石净度 证书号 主成色 名称 品牌 最新零售价 定制价
		*/
		
		//直营店净度转换
		$zyd_jindu = $datainfo['zhushijingdu'];
		if(strpos($zyd_jindu,'SI') !== false)
		{
			$zyd_jindu = 'SI';
		}
		//价格显示
		if(isset($datainfo['dabiao_price']))
		{
			$price = $datainfo['dabiao_price'];
		}elseif(isset($datainfo['price'])){
			$price = $datainfo['price'];
		}
		if($ishuodongprice>0)
		{
			if($price<=99){
				$price = 99;
			}else{
				//说明价格大于99
				$tmpprice = substr($price,0,-2);	
				$price = $tmpprice.'99';
			}
		}
		
		//定制成本显示
		//如果商品材质是18K,那么线上PT定制价   金重*400+销售价+500   
		//如果商品材质是PT950,那么显示18K金定制价   销售价-(金重*250)
		$caizhi = strtoupper($datainfo['caizhi']);
		$dingzhi_price = 0;
		if(strpos($caizhi,'18K') !== false){
			//说明是18K
			$dingzhi_price = 'PT定制价￥'. ceil($datainfo['jinzhong']*400+$price+500);
		}elseif(strpos($caizhi,'铂')!==false){
			$dingzhi_price = '18K金定制价￥'. ceil($price-($datainfo['jinzhong'] * 250));	
		}
		
		//新加的主石重与粒数
		$zsdx_new  = $datainfo['zuanshidaxiao'];
		$zsls_new = $datainfo['zhushilishu'];
		$zszyulishu = '';
		if($zsdx_new>0 && $zsls_new>0){
			$zszyulishu = $zsdx_new.'ct/'.$zsls_new.'p';	
		}elseif($zsdx_new>0 && empty($zsls_new)){
            $zszyulishu = $zsdx_new.'ct';
        }elseif(empty($zsdx_new) && $zsls_new>0){
            $zszyulishu = $zsls_new.'p';    
        }else{
            $zszyulishu = '';
        }
		//新加的副石重于副石粒数
		$fszyulishu='';
		if($datainfo['fushilishu']>0 && $datainfo['fushizhong']>0)
		{
			$fszyulishu = $datainfo['fushizhong'].'ct/'.$datainfo['fushilishu'].'p';
		}elseif($datainfo['fushilishu']>0 && empty($datainfo['fushizhong'])){
            $fszyulishu = $datainfo['fushilishu'].'p';
        }elseif(empty($datainfo['fushilishu']) && $datainfo['fushizhong']>0){
            
            $fszyulishu = $datainfo['fushizhong'].'ct';
        }else{
            $fszyulishu='';
        }
		//新加的颜色与直营店净度
		if($datainfo['zhushiyanse']=='' && $zyd_jindu=='' )
		{
			$ysyuzydjd = '';
		}elseif($datainfo['zhushiyanse'] !='' && $zyd_jindu==''){
			$ysyuzydjd = $datainfo['zhushiyanse'];
		}elseif($datainfo['zhushiyanse'] =='' && $zyd_jindu!=''){
            $ysyuzydjd = $zyd_jindu;
        }else{
            $ysyuzydjd = $datainfo['zhushiyanse'].'/'.$zyd_jindu;
        }
        $jinzhong = !empty($datainfo['jinzhong'])?$datainfo['jinzhong']."g":"";
        $zongzhong = !empty($datainfo['zongzhong'])?$datainfo['zongzhong']."g":"";
		$biaoqianjia = SYS_SCOPE == 'zhanting' && $label_price == 3 ? $datainfo['biaoqianjia']:'——';
		$shoucun = !empty($datainfo['shoucun'])?$datainfo['shoucun']."#":"";
        $jietuoxiangkou = bcmul(100, $datainfo['jietuoxiangkou'], 2);
        $price = !empty($price)?'￥'.$price:"￥0";
		$lineinfo= '';
		$lineinfo .= 'Start'.$datainfo['goods_id'].'End';
		$lineinfo .= 'Start'.$datainfo['goods_sn'].'End';
		$lineinfo .= 'Start'.$shoucun.'End';
		$lineinfo .= 'Start'.$datainfo['changdu'].'End';
		$lineinfo .= 'Start'.$jietuoxiangkou.'End';
		$lineinfo .= 'Start'.$datainfo['zhushilishu'].'End';
		$lineinfo .= 'Start'.$datainfo['zuanshidaxiao'].'End';
		$lineinfo .= 'Start'.$datainfo['fushilishu'].'End';
		$lineinfo .= 'Start'.$datainfo['fushizhong'].'End';
		$lineinfo .= 'Start'.$jinzhong.'End';
		$lineinfo .= 'Start'.$zongzhong.'End';
		$lineinfo .= 'Start'.$datainfo['zhushijingdu'].'End';
		$lineinfo .= 'Start'.$datainfo['zhushiyanse'].'End';
		$lineinfo .= 'Start'.$zyd_jindu.'End';
		//新加的
		$lineinfo .= 'Start'.$price.'End';
		$lineinfo .= 'Start'.$zszyulishu.'End';
		$lineinfo .= 'Start'.$fszyulishu.'End';
		$lineinfo .= 'Start'.$ysyuzydjd.'End';
		//新加的end
		$lineinfo .= 'Start'.$datainfo['zhengshuhao'].'End';
		$lineinfo .= 'Start'.$datainfo['caizhi'].'End';
		$lineinfo .= 'Start'.$datainfo['goods_name'].'End';
		$lineinfo .= 'Start'.$datainfo['pinpai'].'End';
		//$lineinfo .= 'Start'.$price.'End';
		$lineinfo .= 'Start'.$dingzhi_price.'End';
        $lineinfo .= 'Start'.$biaoqianjia.'End';
        $lineinfo .= 'Start'.$datainfo['zhengshuleibie'].'End';
        $lineinfo .= 'Start'.$datainfo['is_made'];//最后一个去掉,因为csv最后一个和其他的不一样.'End';
		//1:csv
		if($iscsv==2){
			$tmp_line = str_replace('Start','<td>',$lineinfo);
			$tmp_line = str_replace('End','</td>',$tmp_line);
			$str .='<tr>'.$tmp_line.'</td></tr>';
			/*$str.="<tr>";
			$str .="<td>" . $datainfo['goods_id'] . "</td>";
			$str .="<td>" . $datainfo['goods_sn'] . "</td>";
			$str .="<td>" . $datainfo['shoucun'] . "</td>";
			$str .="<td>" . $datainfo['changdu'] . "</td>";
			$str .="<td>" . 100 * $datainfo['jietuoxiangkou']."</td>";
			$str .="<td>" . $datainfo['zhushilishu'] . "</td>";
			$str .="<td>" . $datainfo['zuanshidaxiao'] . "</td>";
			$str .="<td>" . $datainfo['fushilishu'] . "</td>";
			$str .="<td>" . $datainfo['fushizhong'] . "</td>";
			$str .="<td>" . $datainfo['jinzhong'] . "</td>";
			$str .="<td>" . $datainfo['zongzhong'] . "</td>";
			$str .="<td>" . $datainfo['zhushijingdu'] . "</td>";
			$str .="<td>" .  $datainfo['zhushiyanse'] . "</td>";
			$str .="<td>" . $zyd_jindu . "</td>";
			$str .="<td>" . $datainfo['zhengshuhao'] . "</td>";
			$str .="<td>" . $datainfo['caizhi'] . "</td>";
			$str .="<td>" . $datainfo['goods_name'] . "</td>";
			$str .="<td>" . $datainfo['pinpai']. "</td>";
			$str .="<td>" . $price . "</td>";
			$str .="<td>" . $dingzhi_price . "</td>";
			$str .="</tr>";//总重g*/	
		}else{
			$tmp_line = str_replace('Start','"',$lineinfo);
			$tmp_line = str_replace('End','",',$tmp_line);
			$str .= $tmp_line."\"\r\n";
		/*$str .=
			"\"" . $datainfo['goods_id'] . "\"," .
			"\"" . $datainfo['goods_sn'] . "\"," .
			"\"" . $datainfo['shoucun'] . "\"," .
			"\"" . $datainfo['changdu'] . "\"," .
			"\"" . 100 * $datainfo['jietuoxiangkou'] . "\"," .
			"\"" . $datainfo['zhushilishu'] . "\"," .
			"\"" . $datainfo['zuanshidaxiao'] . "\"," .
			"\"" . $datainfo['fushilishu'] . "\"," .
			"\"" . $datainfo['fushizhong'] . "\"," .
			"\"" . $datainfo['jinzhong'] . "\"," .
			"\"" . $datainfo['zongzhong'] . "\"," .
			"\"" . $datainfo['zhushijingdu'] . "\"," .
			"\"" .  $datainfo['zhushiyanse'] . '"' . "," .
			"\"" . $zyd_jindu . "\"," .
			"\"" . $datainfo['zhengshuhao'] . "\"," .
			"\"" . $datainfo['caizhi'] . "\"," .
			"\"" . $datainfo['goods_name'] . "\"," .
			"\"" . $datainfo['pinpai']. "\"," .
			"\"" . $price . "\"," .
			"\"".$dingzhi_price."\"\r\n";    //定制成本价*/
		}
		return $str;
	}
    public function filerLabel(&$goods){
        $sql = "select *, att2 as kuanshi_type from warehouse_goods as g where g.goods_id = '".$goods['goods_id']."' limit 0, 1";
          //echo $sql;exit;
        $model = new WarehouseGoodsModel(21);
        $line = $model->db()->getRow($sql);
        $goods_id=$line['goods_id'];
        if (empty($line)){
              //echo "没有查到该货号：".$goods_id."信息,请核实后再做打印";exit;
            die("没有查到该货号：".$goods_id."信息，请核实后再做打印！");  
        }
        if($line['caizhi'] != '' && $line['caizhi'] != "无" && $line['caizhi'] != "其它" && $line['caizhi'] != "裸石	"){
          	if(strstr($line['goods_name'], $line['caizhi']) === false){
          	 	die("该货号：".$goods_id."商品名称没有包含该商品材质".$line['caizhi']);
          	}
        }
          
        if($line['zhushi']=='钻石' && $line['cat_type1']!='裸石' && strstr($line['goods_name'],$line['zhushi'])===false && $line['tuo_type']=='1'){
          	die('该货号：'.$goods_id.'商品名称必须包含'.$line['zhushi']);
          	
        }
        if($line['zhushi']=='' && $line['fushi']=='钻石' && $line['cat_type1']!='裸石' && strstr($line['goods_name'],$line['fushi'])===false && $line['tuo_type']=='1'){
          	die('该货号：'.$goods_id.'商品名称必须包含'.$line['fushi']);
          	
        }
       

     
          //$line['zhuchengse'] = $zhuchengse_list[$zhuchengse];
          //$line['goods_name'] = str_replace($zhuchengse, $line['caizhi'], $line['goods_name']);
          $line['goods_name'] = str_replace(array('女戒','情侣戒','CNC情侣戒','男戒','戒托'), array('戒指','戒指','戒指','戒指','戒指'), $line['goods_name']);
          $line['goods_name'] = str_replace(array('海水海水','淡水白珠','淡水圆珠','淡水',"大溪地", "南洋金珠"), array('海水','珍珠','珍珠','',"海水","海水珍珠"), $line['goods_name']);

          $tihCz = array(
            '无' => '',
            '其它' => '',
            '其他' => '',
            '24K' =>'足金',
            '千足金银'    =>'足金银',
            'S990'    =>'足银',
            '千足银' =>'足银',            
            '千足金' =>'足金',
            'PT900'   =>'铂900',
            'PT999'   =>'铂999',
            'PT990'   =>'铂990',
            'PT950'   =>'铂950',
            '18K玫瑰黄'=>'18K金',
            '18K玫瑰白'=>'18K金',
            '18K玫瑰金'=>'18K金',
            '18K黄金'=>'18K金',
            '18K白金'=>'18K金',
            '18K黑金'=>'18K金',
            '18K彩金'=>'18K金',
            '18K红'=>'18K金',
            '18K黄白'=>'18K金',
            '18K分色'=>'18K金',
            '18K黄'=>'18K金',
            '18K白'=>'18K金',
            '9K玫瑰黄'=>'9K金',
            '9K玫瑰白'=>'9K金',
            '9K玫瑰金'=>'9K金',
            '9K黄金'=>'9K金',
            '9K白金'=>'9K金',
            '9K黑金'=>'9K金',
            '9K彩金'=>'9K金',
            '9K红'=>'9K金',
            '9K黄白'=>'9K金',
            '9K分色'=>'9K金',
            '9K黄'=>'9K金',
            '9K白'=>'9K金',
            '10K玫瑰黄'=>'10K金',
            '10K玫瑰白'=>'10K金',
            '10K玫瑰金'=>'10K金',
            '10K黄金'=>'10K金',
            '10K白金'=>'10K金',
            '10K黑金'=>'10K金',
            '10K彩金'=>'10K金',
            '10K红'=>'10K金',
            '10K黄白'=>'10K金',
            '10K分色'=>'10K金',
            '10K黄'=>'10K金',
            '10K白'=>'10K金', 
            '14K玫瑰黄'=>'14K金',
            '14K玫瑰白'=>'14K金',
            '14K玫瑰金'=>'14K金',
            '14K黄金'=>'14K金',
            '14K白金'=>'14K金',
            '14K黑金'=>'14K金',
            '14K彩金'=>'14K金',
            '14K红'=>'14K金',
            '14K黄白'=>'14K金',
            '14K分色'=>'14K金',
            '14K黄'=>'14K金',
            '14K白'=>'14K金',
            '19K黄'=>'19K金',
            '19K白'=>'19K金',
            '19K玫瑰黄'=>'19K金',
            '19K玫瑰白'=>'19K金',
            '19K玫瑰金'=>'19K金',
            '19K黄金'=>'19K金',
            '19K白金'=>'19K金',
            '19K黑金'=>'19K金',
            '19K彩金'=>'19K金',
            '19K红'=>'19K金',
            '19K黄白'=>'19K金',
            '19K分色'=>'19K金',
            '20K黄'=>'20K金',
            '20K白'=>'20K金',
            '20K玫瑰黄'=>'20K金',
            '20K玫瑰白'=>'20K金',
            '20K玫瑰金'=>'20K金',
            '20K黄金'=>'20K金',
            '20K白金'=>'20K金',
            '20K黑金'=>'20K金',
            '20K彩金'=>'20K金',
            '20K红'=>'20K金',
            '20K黄白'=>'20K金',
            '20K分色'=>'20K金',
            '21K黄'=>'21K金',
            '21K白'=>'21K金',
            '21K玫瑰黄'=>'21K金',
            '21K玫瑰白'=>'21K金',
            '21K玫瑰金'=>'21K金',
            '21K黄金'=>'21K金',
            '21K白金'=>'21K金',
            '21K黑金'=>'21K金',
            '21K彩金'=>'21K金',
            '21K红'=>'21K金',
            '21K黄白'=>'21K金',
            '21K分色'=>'21K金',
            '22K黄'=>'22K金',
            '22K白'=>'22K金',
            '22K玫瑰黄'=>'22K金',
            '22K玫瑰白'=>'22K金',
            '22K玫瑰金'=>'22K金',
            '22K黄金'=>'22K金',
            '22K白金'=>'22K金',
            '22K黑金'=>'22K金',
            '22K彩金'=>'22K金',
            '22K红'=>'22K金',
            '22K黄白'=>'22K金',
            '22K分色'=>'22K金',
            '23K黄'=>'23K金',
            '23K白'=>'23K金',
            '23K玫瑰黄'=>'23K金',
            '23K玫瑰白'=>'23K金',
            '23K玫瑰金'=>'23K金',
            '23K黄金'=>'23K金',
            '23K白金'=>'23K金',
            '23K黑金'=>'23K金',
            '23K彩金'=>'23K金',
            '23K红'=>'23K金',
            '23K黄白'=>'23K金',
            '23K分色'=>'23K金',
            'S925黄'=>'S925',
            'S925白'=>'S925',
            'S925玫瑰黄'=>'S925',
            'S925玫瑰白'=>'S925',
            'S925玫瑰金'=>'S925',
            'S925黄金'=>'S925',
            'S925白金'=>'S925',
            'S925黑金'=>'S925',
            'S925彩金'=>'S925',
            'S925红'=>'S925',
            'S925黄白'=>'S925',
            'S925分色'=>'S925',
            'S925'    =>'银925'
            );

            $stone_arr = array('红宝'=>'红宝石',
            '珍珠贝'=>'贝壳',
            '白水晶'=>'水晶',
            '粉晶'=>'水晶',
            '茶晶'=>'水晶',
            '紫晶'=>'水晶',
            '紫水晶'=>'水晶',
            '黄水晶'=>'水晶',
            '彩兰宝'=>'蓝宝石',
            '彩色蓝宝'=>'蓝宝石',
            '蓝晶'=>'水晶',
            '黄晶'=>'水晶',
            '柠檬晶'=>'水晶',
            '红玛瑙'=>'玛瑙',
            '黑玛瑙'=>'玛瑙',
            '奥泊'=>'宝石',
            '黑钻'=>'钻石',
            '琥铂'=>'琥铂',
            '虎晴石'=>'宝石',
            '大溪地珍珠'=>'珍珠',
            '大溪地黑珍珠'=>'珍珠',
            '淡水白珠'=>'珍珠',
            '淡水珍珠'=>'珍珠',
            '南洋白珠'=>'珍珠',
            '南洋金珠'=>'珍珠',
            '海水香槟珠'=>'珍珠',
            '混搭珍珠'=>'珍珠',
            '蓝宝'=>'蓝宝石',
        	'宝石石'=>'宝石',
            '黄钻'=>'钻石');

            $stone_arr_s = array('红玛瑙'=>'玛瑙',
                    '和田玉'=>'和田玉',
                    '星光石'=>'星光石',
                    '莹石'=>'莹石',
                    '捷克陨石'=>'捷克陨石',
                    '绿松石'=>'绿松石',
                    '欧泊'=>'欧泊',
                    '砗磲'=>'砗磲',
                    '芙蓉石'=>'芙蓉石',
                    '坦桑石'=>'坦桑石',
                    '南洋白珠'=>'珍珠',
                    '大溪地珍珠'=>'珍珠',
                    '南洋金珠'=>'珍珠',
                    '无'=>'',
                    '黑玛瑙'=>'玛瑙',
                    '托帕石'=>'托帕石',
                    '橄榄石'=>'橄榄石',
                    '红纹石'=>'红纹石',
                    '蓝宝石'=>'蓝宝石',
                    '祖母绿'=>'祖母绿',
                    '黄水晶'=>'水晶',
                    '玉髓'=>'玉髓',
                    '异形钻'=>'钻石',
                    '粉红宝'=>'粉红宝',
                    '彩钻'=>'钻石',
                    '尖晶石'=>'尖晶石',
                    '石榴石'=>'石榴石',
                    '贝壳'=>'贝壳',
                    '珍珠贝'=>'贝壳',
                    '圆钻'=>'钻石',
                    '碧玺'=>'碧玺',
                    '葡萄石'=>'葡萄石',
                    '拉长石（月光石）'=>'拉长石（月光石）',
                    '舒俱来石'=>'舒俱来石',
                    '琥珀'=>'琥珀',
                    '黑钻'=>'钻石',
                    '混搭珍珠'=>'珍珠',
                    '碧玉'=>'',
                    '紫龙晶'=>'紫龙晶',
                    '玛瑙'=>'玛瑙',
                    '青金石'=>'青金石',
                    '虎睛石（木变石）'=>'虎睛石（木变石）',
                    '黑曜石'=>'黑曜石',
                    '珍珠'=>'珍珠',
                    '红宝石'=>'红宝石',
                    '其它'=>'',
                    '海蓝宝'=>'海蓝宝石',
                    '水晶'=>'水晶',
                    '翡翠'=>'翡翠',
                    '孔雀石'=>'孔雀石',
                    '东陵玉'=>'东陵玉',
                    '锂辉石'=>'锂辉石',
                    '珊瑚'=>'珊瑚',
                    '海水香槟珠'=>'珍珠',
                    '淡水白珠'=>'珍珠',
                    '锆石'=>'合成立方氧化锆',
                    '月光石'=>'月光石');

            $tihCt = array('耳环'=>'耳饰',
                    '吊坠'=>'吊坠',
                    '裸石（镶嵌物）'=>'裸石（镶嵌物）',
                    '女戒'=>'戒指',
                    '套装'=>'饰品',
                    '纪念币'=>'纪念币',
                    '素料类'=>'素料类',
                    '彩宝'=>'彩宝',
                    '彩钻'=>'彩钻',
                    '男戒'=>'戒指',
                    '赠品'=>'饰品',
                    '胸针'=>'饰品',
                    '脚链'=>'脚链',
                    '情侣戒'=>'戒指',
                    '金条'=>'金条',
                    '其它'=>'饰品',
                    '摆件'=>'摆件',
                    '项链'=>'项链',
                    '多功能款'=>'饰品',
                    '手链'=>'手链',
                    '耳钩'=>'耳饰',
                    '手表'=>'手表',
                    '固定资产'=>'固定资产',
                    '长链'=>'饰品',
                    '裸石（统包货）'=>'裸石（统包货）',
                    '裸石（珍珠）'=>'裸石（珍珠）',
                    '套戒'=>'戒指',
                    '领带夹'=>'领带夹',
                    '手镯'=>'手镯',
                    '原材料'=>'原材料',
                    '袖口钮'=>'饰品',
                    '耳钉'=>'耳饰',
                    '物料'=>'物料',
                    '其他'=>'饰品',
                    '耳饰'=>'耳饰');



	            if($line['caizhi'] != ''){
	                $goods['caizhi'] = str_replace(array_keys($tihCz), array_values($tihCz), $line['caizhi']);
	            }
	            if($line['cat_type1'] != ''){
	                $goods['cat_type1'] = str_replace(array_keys($tihCt), array_values($tihCt), $line['cat_type1']);
	            }
	            if($line['goods_name'] != ''){
		            //$line['goods_name'] = str_replace("千", "", $line['goods_name']);
		            $goods['goods_name'] = str_replace('锆石','合成立方氧化锆',$line['goods_name']);
		            $goods['goods_name'] = str_replace(array_keys($tihCz), array_values($tihCz), $goods['goods_name']);
		            $goods['goods_name'] = str_replace(array_keys($tihCt), array_values($tihCt), $goods['goods_name']);
		            $goods['goods_name'] = str_replace(array_keys($stone_arr_s), array_values($stone_arr_s), $goods['goods_name']);
	            }

      
                if($line['zhushi'] != ''){
                    $goods['zhushi'] = str_replace(array_keys($stone_arr_s), array_values($stone_arr_s),$line['zhushi']);
                }
                if($line['fushi'] != ''){
                    $goods['fushi'] = str_replace(array_keys($stone_arr_s), array_values($stone_arr_s),$line['fushi']);
                }

                if($goods['zhushi'] != ''){
                    $shitou = $goods['zhushi'];
                }else{
                    $shitou = $goods['fushi'];
                }
                
                $goods['goods_name'] = $goods['caizhi'].$shitou.$goods['cat_type1'];
                
                //$line['zongzhong'] = !empty($line['zongzhong'])?$line['zongzhong'].'g':'';

                $sql = "select is_made from front.base_style_info where style_sn='{$goods['goods_sn']}'";
                $is_made = $model->db()->getOne($sql);
                $goods['is_made'] = $is_made ? '定':'';

            
    }

    public function uploadzdfile()
    {
        ini_set("memory_limit","-1");
        set_time_limit(0);//设置上传允许超时提交（数据量大时有用）
        //标红提示；
        $error = '';
        //$result['error'] = "提示：批量上传成功，<span style='color:red;'>请核查！</span>";
        //Util::jsonExit($result);
        $fileInfo = $_FILES['zhidingcode'];//读取文件信息；

        $tmp_name = $fileInfo['tmp_name'];
        //是否选择文件；
        if ($tmp_name == '') 
        {
            $error = "请选择上传文件！";
            $this->error_csv($error);
        }

        //是否csv文件；
        $file_name = $fileInfo['name'];
        $ext=Upload::getExt($file_name);
        if ($ext != 'xlsx' && $ext != 'xls' && $ext != 'csv') 
        {

            $error = "请上传.xls或.xls为后缀的文件！";
            $this->error_csv($error);
        }
        
        if($ext=='xlsx' || $ext=='xls'){        
           //上传.xlsx或者.xls文件
	        $path = '/frame/PHPExcel/PHPExcel.php';
	        $pathIo = '/frame/PHPExcel/PHPExcel/IOFactory.php';
	        $Excel5 = '/frame/PHPExcel/PHPExcel/Reader/Excel5.php';
	        
	        include_once(KELA_ROOT.$path);
	        include_once(KELA_ROOT.$pathIo);
	        include_once(KELA_ROOT.$Excel5);
	        $uploadfile=KELA_ROOT.'/frame/dabiao.'.$ext;
	        $result=move_uploaded_file($tmp_name,$uploadfile);
	        if($result){
	        	if($ext=='xlsx'){
		          $objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format
	        	}else{
	        		$objReader = PHPExcel_IOFactory::createReader('Excel5'); 
	        	}
		        $objPHPExcel = $objReader->load($uploadfile);
		        
		        $objWorksheet = $objPHPExcel->getActiveSheet();
		        $highestRow = $objWorksheet->getHighestRow();
		        
		        
		        $highestColumn = $objWorksheet->getHighestColumn();
		        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数
		        
		        $data=array();
		        for ($i = 2;$i <= $highestRow;$i++)
		        {
		        	 $row=array();
		        	 $goods_id= $objPHPExcel->getActiveSheet()->getCell("F{$i}")->getValue();	        	 
		        	 $price= $objPHPExcel->getActiveSheet()->getCell("BE{$i}")->getValue();  
		        	 
		        	  
		              if(!is_numeric($goods_id)) continue;
		              if( $price ===null) continue;
		              if(!is_numeric($price)){
		              	$error = "BE{$i}行‘价格’是用公式计算的价格，请改成数值。";
		              	$this->error_csv($error);
		              }
		              if($price<0 || $goods_id <= 0) continue;
		        	  $row['buyout_price']=round($price, 0);
		        	  $row['goods_id']=$goods_id;
		        	  $row['activity_price']=0;
		        	  $data[]=$row;	
		        	  //$error .=$goods_id.",".$price."\r\n"; 
		        	     
		        }
	        
		     }else{
		        $error = "上传失败";
		        $this->error_csv($error);
		     }
         }elseif($ext=='csv'){
         	//上传csv格式
         	
         	//打开文件资源
         	$fileData = fopen($tmp_name, 'r');
         	while ($data = fgetcsv($fileData))
         	{
         		$codeInfo[] = $data;
         	}
         	
         	//是否填写数据
         	if (count($codeInfo) == 1)
         	{
         	
         		$error = "未检测到数据，请填写后上传！";
         		$this->error_csv($error);
         	}
         	
         	$hgt = 1;//行数；
         	array_shift($codeInfo);//去除首行文字；
         	foreach ($codeInfo as $key => $value) {
         		$hgt++;
         		
         		$fields = array('goods_id','buyout_price','activity_price');
         		$LineInfo=array();
         		//去除用户录入不规范的内容
         		for ($i=0; $i < 3 ; $i++)
         		{
         		$LineInfo[$fields[$i]] = $this->trimall($value[$i]);
         		}
         	
         		if($LineInfo['goods_id'] == '')
         		{
         		$error = "文件第".$hgt."行货号不能为空！";
         				$this->error_csv($error);
         		}
         	
         			if($LineInfo['buyout_price'] == '')
         			{
         			$error = "文件第".$hgt."行指定价不能为空！";
         			$this->error_csv($error);
         	}
         	
         	
         	if($LineInfo['activity_price'] == '')
         	{
         	///$error = "文件第".$hgt."行活动价不能为空！";
         	//$this->error_csv($error);
         	$LineInfo['activity_price']='0';
         	}
         	
         	$data[] = $LineInfo;
         	}
         }else{
         	$error = "上传文件格式错误";
         	$this->error_csv($error);
         }  
         
	      if(empty($data)){
	       	  $error = "上传文件是空文件";
	       	  $this->error_csv($error);
	      }
	        $model = new WarehouseGoodsModel(21);
	        $model1 = new WarehouseGoodsModel(22);
	        $res = true;
	        $word="操作人：".$_SESSION['userName']."\t操作时间：".date('Y-m-d H:i:s',time())."\r\n\r\n";
	        $str='';
	        foreach ($data as $key => $value) {
	        	$re = $model->getBiaoqian($value['goods_id']);
	        	if(empty($re)){
	        		$r = $model1->saveBiaoQianData($value);
	        		if($r){
	        			$str.="货号".$value['goods_id']."插入价格：".$value['buyout_price']."\r\n";
	        		}
	        	}else{
	        		$bq_id=$re['id'];
	        		$buyout_price=$re['buyout_price'];	        		
	        		$r = $model1->updateBiaoQianData($bq_id,$value);
	        		if($r){
	        			$str.="货号".$value['goods_id']."更新价格：{$buyout_price}---->".$value['buyout_price']."\r\n";
	        		}
	        	}
	        	if($r == false) $res = false;
	        }
	        if($res == true){
	        	$file=KELA_ROOT."/frame/dabiao_log.txt";
	        	if(!file_exists($file)){
	        		file_put_contents($file,'');
	        	}
	        	$fh = fopen($file, "a");
	        	$word .=$str."\r\n\r\n\r\n";
	        	fwrite($fh, $word);
	        	
	        	$this->error_txt($str,'导入成功信息');
	        }else{
	        	$error = "提交导入失败！";
	        	$this->error_csv($error);
	        }
    }

    public function uploadbzhfile(){
    	ini_set("memory_limit","-1");
    	set_time_limit(0);//设置上传允许超时提交（数据量大时有用）
    	//标红提示；
    	$error = '';
    	//$result['error'] = "提示：批量上传成功，<span style='color:red;'>请核查！</span>";
    	//Util::jsonExit($result);
    	$fileInfo = $_FILES['zhidingcode'];//读取文件信息；
    	 
    	$tmp_name = $fileInfo['tmp_name'];
    	//是否选择文件；
    	if ($tmp_name == '')
    	{
    		$error = "请选择上传文件！";
    		$this->error_csv($error);
    	}
    	 
    	//是否csv文件；
    	$file_name = $fileInfo['name'];
    	$ext=Upload::getExt($file_name);
    	if ($ext != 'xlsx' && $ext != 'xls')
    	{
    		 
    		$error = "请上传.xls或.xls为后缀的文件！";
    		$this->error_csv($error);
    	}
    	 
    	 
    	//上传.xlsx或者.xls文件
    	$path = '/frame/PHPExcel/PHPExcel.php';
    	$pathIo = '/frame/PHPExcel/PHPExcel/IOFactory.php';
    	$Excel5 = '/frame/PHPExcel/PHPExcel/Reader/Excel5.php';
    	 
    	include_once(KELA_ROOT.$path);
    	include_once(KELA_ROOT.$pathIo);
    	include_once(KELA_ROOT.$Excel5);
    	$uploadfile=KELA_ROOT.'/frame/baizhihui.'.$ext;
    	$result=move_uploaded_file($tmp_name,$uploadfile);
    	if($result){
    		 
    		if($ext=='xlsx'){
    			$objReader = PHPExcel_IOFactory::createReader('Excel2007');//use excel2007 for 2007 format
    		}else{
    			$objReader = PHPExcel_IOFactory::createReader('Excel5');
    		}
    		$objPHPExcel = $objReader->load($uploadfile);
    		 
    		$objWorksheet = $objPHPExcel->getActiveSheet();
    		$highestRow = $objWorksheet->getHighestRow();
    		 
    		 
    		$highestColumn = $objWorksheet->getHighestColumn();
    		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数
    		 
    		$data=array();
    		for ($i = 2;$i <= $highestRow;$i++)
    		{
    		$row=array();
    		$row['goods_id']= $objPHPExcel->getActiveSheet()->getCell("F{$i}")->getValue();
    		$row['goods_sn']= $objPHPExcel->getActiveSheet()->getCell("G{$i}")->getValue();
    		$row['goods_name']= $objPHPExcel->getActiveSheet()->getCell("BK{$i}")->getValue();
    		$row['shoucun']= $objPHPExcel->getActiveSheet()->getCell("M{$i}")->getValue();
    		$row['zhengshuhao']= $objPHPExcel->getActiveSheet()->getCell("I{$i}")->getValue();
    		$row['chengbenjia']= $objPHPExcel->getActiveSheet()->getCell("BA{$i}")->getValue();
    		$row['jinzhong']= $objPHPExcel->getActiveSheet()->getCell("H{$i}")->getValue();
    				$row['zhushishu']= $objPHPExcel->getActiveSheet()->getCell("S{$i}")->getValue();
    						$row['zhushizhong']= $objPHPExcel->getActiveSheet()->getCell("R{$i}")->getValue();
    	    			$row['fushishu']= $objPHPExcel->getActiveSheet()->getCell("AL{$i}")->getValue();
    	    			$row['fushizhong']= $objPHPExcel->getActiveSheet()->getCell("AO{$i}")->getValue();
    	    			$row['xiangkou']= $objPHPExcel->getActiveSheet()->getCell("L{$i}")->getValue();
    	    			$row['xiaoshoujia']= $objPHPExcel->getActiveSheet()->getCell("BE{$i}")->getValue();
    	    					$row['zhuchengse']= $objPHPExcel->getActiveSheet()->getCell("N{$i}")->getValue();
    	    							$row['zhushijiebie']= $objPHPExcel->getActiveSheet()->getCell("T{$i}")->getValue();
    	    							$row['zhushijingdu']= $objPHPExcel->getActiveSheet()->getCell("T{$i}")->getValue();
    	    							$row['zhushisecai']= $objPHPExcel->getActiveSheet()->getCell("U{$i}")->getValue();
    	    							$row['zhushiyanse']= $objPHPExcel->getActiveSheet()->getCell("U{$i}")->getValue();
    	    							$row['ygoods_id']= $objPHPExcel->getActiveSheet()->getCell("BR{$i}")->getValue();
    
    
    
    
    	    							//if(empty($row['goods_id'])) continue;
    		if(empty($row['goods_sn'])) continue;
    		if(empty($row['goods_name'])) continue;
    		if(empty($row['chengbenjia']) && $row['chengbenjia'] != 0) continue;
    		if(empty($row['xiaoshoujia']) && $row['xiaoshoujia'] != 0) continue;
    		if(!is_numeric($row['chengbenjia'])){
    				$error = "BA{$i}行‘价格’是用公式计算的价格，请改成数值。";
    				$this->error_csv($error);
    		}
    						if(!is_numeric($row['xiaoshoujia'])){
    						$error = "BE{$i}行‘价格’是用公式计算的价格，请改成数值。";
    						$this->error_csv($error);
    		}
    		$data[]=$row;
    				//$error .=$goods_id.",".$price."\r\n";
    				 
    						}
    
    						}else{
    						$error = "上传失败";
    						$this->error_csv($error);
    }
    
     
     
    $model = new WarehouseGoodsModel(21);
    $model1 = new WarehouseGoodsModel(22);
    	$res = true;
    	$word="操作人：".$_SESSION['userName']."\t操作时间：".date('Y-m-d H:i:s',time())."\r\n\r\n";
    	$str='';
    	foreach ($data as $key => $value) {
		    	$re = $model->getBaizhihui($value['goods_id']);
		    	if(empty($re)){
		    			$r = $model1->insertTableData('warehouse_goods_baizhihui',$value);
		    			$id=$model1->db()->insertId();
		    			if(empty($value['goods_id'])){//货号为空时，id赋值给货号
		    			$d['goods_id']=$id;
		    	$model1->updateTableData('warehouse_goods_baizhihui',$d,'id='.$id);
		    			}
		    			if($r){
		    			   $str.="货号".$value['goods_id']."录入数据库成功\r\n";
		    	       }
		    	}else{
			    	$id=$re['id'];
			    			$goods_id=$value['goods_id'];
			    		unset($value['goods_id']);
			    		$r = $model1->updateTableData('warehouse_goods_baizhihui',$value,'id='.$id);
			    	if($r){
			    	$str.="货号".$goods_id."更新信息成功\r\n";
			    	}
		    	}
    		  if($r == false) $res = false;
    	}
    		if($res == true){
    	//echo ("<script>alert('".$str."');window.history.go(-1);<script>");
    	 
    	$file=KELA_ROOT."/frame/baizhihui_log.txt";
    		if(!file_exists($file)){
    	file_put_contents($file,'');
    	}
    	$fh = fopen($file, "a");
    	$word .=$str."\r\n\r\n\r\n";
    	fwrite($fh, $word);
    	 
    $this->error_txt($str,'导入成功信息');
    }else{
    $error = "提交导入失败！";
    	$this->error_csv($error);
    }
     
    }
    
    
    /**
     *  downCode_zhubao，下载标签
     */
    public function downCode_zhubao()
    {
        ini_set("memory_limit","-1");
        set_time_limit(0);//设置上传允许超时提交（数据量大时有用）
        //标红提示；
        $error = '';
        //$result['error'] = "提示：批量上传成功，<span style='color:red;'>请核查！</span>";
        //Util::jsonExit($result);
        $fileInfo = $_FILES['file_code'];//读取文件信息；

        $tmp_name = $fileInfo['tmp_name'];
        //是否选择文件；
        if ($tmp_name == '') 
        {
            $error = "请选择上传文件！";
            $this->error_csv($error);
        }

        //是否csv文件；
        $file_name = $fileInfo['name'];
        if (Upload::getExt($file_name) != 'csv') 
        {

            $error = "请上传.csv为后缀的文件！";
            $this->error_csv($error);
        }

        //打开文件资源
        $fileData = fopen($tmp_name, 'r');
        while ($data = fgetcsv($fileData))
        {
            $codeInfo[] = $data;
        }

        //是否填写数据
        if (count($codeInfo) == 1)
        {

            $error = "未检测到数据，请填写后上传！";
            $this->error_csv($error);
        }

        //限制上传数据量，限制行数为小于等于150行数据
        /*if (count($codeInfo) >= 151)
        {

            $error = "上传数据过大会导致提交超时，不能超过150行信息！";
            $this->error_csv($error);
        }*/
        $hgt = 1;//行数；
        array_shift($codeInfo);//去除首行文字；
        foreach ($codeInfo as $key => $value) {
            $hgt++;
            //是否为16列信息；
            if (count($value) != 54)
            {

                $error = "文件第".$hgt."行请上传13列信息！";
                $this->error_csv($error);
            }

            $fields = array('riqi','danhao','gongyingshang','gongyingshangname','goods_id','goods_sn','goods_name','shoucun','cat_type','shipin_type','zhengshuhao','shijichengben','zhengshufei','xiulifei','qibanfei','rukuchengbenjia','rukuxiaoshoujia','num','huozhong','jinzhong','peijianzhong','zhushi','zhushishu','zhushizhong','fushi','fushishu','fushizhong','gongyingshangtype','shizhong','xilie','xiangkou','xiaoshoujia','xingbie','xuhao','yanse','zhuchengse','zhushiduicheng','zhushiguige','zhushidanlizhong','zhushijibie','zhushijingdu','zhushimingcheng','zhushimohao','zhushipaoguang','zhushiquegong','zhushisecai','zhushiyanse','zhushizhonglei','zhushiquduanzhong','shuxing','beizhu','biaomian','cangku','changkuname');

            //去除用户录入不规范的内容
            for ($i=0; $i < 54 ; $i++) 
            {
                $LineInfo[$fields[$i]] = $this->trimall($value[$i]);
            }

            /*if($LineInfo['customer_name'] == '')
            {
                echo "文件第".$hgt."行请填写准客户姓名！";die;
            }

            if($LineInfo['tel'] == '')
            {
                echo "文件第".$hgt."行请填写电话号码！";die;
            }

            if(in_array($LineInfo['tel'], $telAll))
            {
                echo "文件第".$hgt."行电话号码在系统已存在！";die;
            }*/

            $data[] = $LineInfo;
        }
        $model = new WarehouseGoodsModel(21);
        $model->downCodeInfo($data);
        exit();
    }

    /**
     *  downCode_zhubao，下载标签
     */
    public function downCode_sujin()
    {
        ini_set("memory_limit","-1");
        set_time_limit(0);//设置上传允许超时提交（数据量大时有用）
        //标红提示；
        $error = '';
        //$result['error'] = "提示：批量上传成功，<span style='color:red;'>请核查！</span>";
        //Util::jsonExit($result);
        $fileInfo = $_FILES['file_code'];//读取文件信息；

        $tmp_name = $fileInfo['tmp_name'];
        //是否选择文件；
        if ($tmp_name == '') 
        {
            $error = "请选择上传文件！";
            $this->error_csv($error);
        }

        //是否csv文件；
        $file_name = $fileInfo['name'];
        if (Upload::getExt($file_name) != 'csv') 
        {

            $error = "请上传.csv为后缀的文件！";
            $this->error_csv($error);
        }

        //打开文件资源
        $fileData = fopen($tmp_name, 'r');
        while ($data = fgetcsv($fileData))
        {
            $codeInfo[] = $data;
        }

        //是否填写数据
        if (count($codeInfo) == 1)
        {

            $error = "未检测到数据，请填写后上传！";
            $this->error_csv($error);
        }

        //限制上传数据量，限制行数为小于等于150行数据
        /*if (count($codeInfo) >= 151)
        {

            $error = "上传数据过大会导致提交超时，不能超过150行信息！";
            $this->error_csv($error);
        }*/
        $hgt = 1;//行数；
        array_shift($codeInfo);//去除首行文字；
        foreach ($codeInfo as $key => $value) {
            $hgt++;
            //是否为16列信息；
            if (count($value) != 54)
            {

                $error = "文件第".$hgt."行请上传13列信息！";
                $this->error_csv($error);
            }

            $fields = array('riqi','danhao','gongyingshang','gongyingshangname','goods_id','goods_sn','goods_name','shoucun','cat_type','shipin_type','zhengshuhao','shijichengben','zhengshufei','xiulifei','qibanfei','rukuchengbenjia','rukuxiaoshoujia','num','huozhong','jinzhong','peijianzhong','zhushi','zhushishu','zhushizhong','fushi','fushishu','fushizhong','gongyingshangtype','shizhong','xilie','xiangkou','xiaoshoujia','xingbie','xuhao','yanse','zhuchengse','zhushiduicheng','zhushiguige','zhushidanlizhong','zhushijibie','zhushijingdu','zhushimingcheng','zhushimohao','zhushipaoguang','zhushiquegong','zhushisecai','zhushiyanse','zhushizhonglei','zhushiquduanzhong','shuxing','beizhu','biaomian','cangku','changkuname');

            //去除用户录入不规范的内容
            for ($i=0; $i < 54 ; $i++) 
            {
                $LineInfo[$fields[$i]] = $this->trimall($value[$i]);
            }

            /*if($LineInfo['customer_name'] == '')
            {
                echo "文件第".$hgt."行请填写准客户姓名！";die;
            }

            if($LineInfo['tel'] == '')
            {
                echo "文件第".$hgt."行请填写电话号码！";die;
            }

            if(in_array($LineInfo['tel'], $telAll))
            {
                echo "文件第".$hgt."行电话号码在系统已存在！";die;
            }*/

            $data[] = $LineInfo;
        }
        $model = new WarehouseGoodsModel(21);
        $model->downCodeInfo($data);
        exit();
    }

    /**
     *  downCode_zhubao，下载标签
     */
    public function downCode_luoshi()
    {
        ini_set("memory_limit","-1");
        set_time_limit(0);//设置上传允许超时提交（数据量大时有用）
        //标红提示；
        $error = '';
        //$result['error'] = "提示：批量上传成功，<span style='color:red;'>请核查！</span>";
        //Util::jsonExit($result);
        $fileInfo = $_FILES['file_code'];//读取文件信息；

        $tmp_name = $fileInfo['tmp_name'];
        //是否选择文件；
        if ($tmp_name == '') 
        {
            $error = "请选择上传文件！";
            $this->error_csv($error);
        }

        //是否csv文件；
        $file_name = $fileInfo['name'];
        if (Upload::getExt($file_name) != 'csv') 
        {

            $error = "请上传.csv为后缀的文件！";
            $this->error_csv($error);
        }

        //打开文件资源
        $fileData = fopen($tmp_name, 'r');
        while ($data = fgetcsv($fileData))
        {
            $codeInfo[] = $data;
        }

        //是否填写数据
        if (count($codeInfo) == 1)
        {

            $error = "未检测到数据，请填写后上传！";
            $this->error_csv($error);
        }

        //限制上传数据量，限制行数为小于等于150行数据
        /*if (count($codeInfo) >= 151)
        {

            $error = "上传数据过大会导致提交超时，不能超过150行信息！";
            $this->error_csv($error);
        }*/
        $hgt = 1;//行数；
        array_shift($codeInfo);//去除首行文字；
        foreach ($codeInfo as $key => $value) {
            $hgt++;
            //是否为16列信息；
            if (count($value) != 54)
            {

                $error = "文件第".$hgt."行请上传13列信息！";
                $this->error_csv($error);
            }

            $fields = array('riqi','danhao','gongyingshang','gongyingshangname','goods_id','goods_sn','goods_name','shoucun','cat_type','shipin_type','zhengshuhao','shijichengben','zhengshufei','xiulifei','qibanfei','rukuchengbenjia','rukuxiaoshoujia','num','huozhong','jinzhong','peijianzhong','zhushi','zhushishu','zhushizhong','fushi','fushishu','fushizhong','gongyingshangtype','shizhong','xilie','xiangkou','xiaoshoujia','xingbie','xuhao','yanse','zhuchengse','zhushiduicheng','zhushiguige','zhushidanlizhong','zhushijibie','zhushijingdu','zhushimingcheng','zhushimohao','zhushipaoguang','zhushiquegong','zhushisecai','zhushiyanse','zhushizhonglei','zhushiquduanzhong','shuxing','beizhu','biaomian','cangku','changkuname');

            //去除用户录入不规范的内容
            for ($i=0; $i < 54 ; $i++) 
            {
                $LineInfo[$fields[$i]] = $this->trimall($value[$i]);
            }

            /*if($LineInfo['customer_name'] == '')
            {
                echo "文件第".$hgt."行请填写准客户姓名！";die;
            }

            if($LineInfo['tel'] == '')
            {
                echo "文件第".$hgt."行请填写电话号码！";die;
            }

            if(in_array($LineInfo['tel'], $telAll))
            {
                echo "文件第".$hgt."行电话号码在系统已存在！";die;
            }*/

            $data[] = $LineInfo;
        }
        $model = new WarehouseGoodsModel(21);
        $model->downCodeInfo($data);
        exit();
    }
	
    //下载
    public function dow($value='')
    {
        $title = array(
                '准客户姓名',
                '状态',
                '项目',
                '来源类型',
                '来源渠道',
                '联系电话',
                '邮箱',
                '省',
                '市',
                '区',
                '意向开店数',
                '投资金额',
                '其他信息'
                );
        $data[0]['name']="张三";
        $data[0]['status']="待跟进";
        $data[0]['xiangmu']="kelan";
        $data[0]['laiyuan']="A";
        $data[0]['qudao']="中国加盟网";
        $data[0]['dianhua']="13888888882";
        $data[0]['eml']="123@kela.cn";
        $data[0]['sheng']="广东";
        $data[0]['shi']="深圳";
        $data[0]['qu']="龙岗";
        $data[0]['yix']="1";
        $data[0]['jine']="1000000万";
        $data[0]['qita']="备注";
            
        Util::downloadCsv("masterplate".time(),$title,$data);
    }

    //下载
    public function zddow($value='')
    {
    	$type= _Request::get("type");
    	if($type==1){
	        $title = array(
	                '货号',
	                '打标价',
	                '活动价'
	                );
	        $data[0]['name']="28872219223";
	        $data[0]['status']="998";
	        $data[0]['xiangmu']="998";
	            
	        Util::downloadCsv("zhiding_masterplate".time(),$title,$data);
    	}elseif($type==2){
    		$temexcel_file = 'apps/warehouse/exceltemp/dabiao.xls';
    		//$filedir = "apps/warehouse/exceltemp/";
    		$user_file = 'dabiao_' . time() . ".xls";
    		$file = fopen($temexcel_file, 'r');
    		
    		header('Content-type: application/octet-stream');
    		header("Accept-Ranges:bytes");
    		header("Accept-length:" . filesize($temexcel_file));
    		header('Content-Disposition: attachment;filename=' . $user_file);
    		ob_clean();
    		$a = fread($file, filesize($temexcel_file));
    		fclose($file);
    		echo $a;
    	}elseif($type==3){
    		
    		$temexcel_file = 'apps/warehouse/exceltemp/dabiao.xlsx';
    		//$filedir = "apps/warehouse/exceltemp/";
    		$user_file = 'dabiao_' . time() . ".xlsx";
    		$file = fopen($temexcel_file, 'r');
    		
    		header('Content-type: application/octet-stream');
    		header("Accept-Ranges:bytes");
    		header("Accept-length:" . filesize($temexcel_file));
    		header('Content-Disposition: attachment;filename=' . $user_file);
    		ob_clean();
    		$a = fread($file, filesize($temexcel_file));
    		fclose($file);
    		echo $a;
    	}elseif($type==4){
    	
    		$temexcel_file = 'apps/warehouse/exceltemp/baizhihui.xlsx';
    		//$filedir = "apps/warehouse/exceltemp/";
    		$user_file = 'baizhihui_' . time() . ".xlsx";
    		$file = fopen($temexcel_file, 'r');
    	
    		header('Content-type: application/octet-stream');
    		header("Accept-Ranges:bytes");
    		header("Accept-length:" . filesize($temexcel_file));
    		header('Content-Disposition: attachment;filename=' . $user_file);
    		ob_clean();
    		$a = fread($file, filesize($temexcel_file));
    		fclose($file);
    		echo $a;
    	}
    }


    /**
     *  trimall，删除空格
     */
    public function trimall($str)
    {

        //字符类型转换；
        $str = iconv('gbk','utf-8',$str);
        //数字不能为负数；
        if(is_numeric($str)){

            $str = abs($str);
        }
        //过滤字符串中用户不小心录入的的空格、换行、等特殊字符；
        $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");

        return str_replace($qian,$hou,$str);
    }

    /**
     *  错误输出
     */
    public function error_csv($content,$filename='')
    {
    	if($filename=='') $filename=$content;
        header("Content-type:text/csv;charset=gbk");
        header("Content-Disposition:filename=" . iconv("utf-8", "gbk","error:".$filename) . ".csv");
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
        echo iconv("utf-8", "gbk//IGNORE", $content);
        exit;
    }
    
    public function error_txt($content,$filename='')
    {
    	if($filename=='') $filename=$content;
    	header("Content-type:text/txt;charset=gbk");
    	header("Content-Type: application/octet-stream");
    	Header( "Accept-Ranges:bytes ");
    	header('Content-Disposition: attachment; filename="' . iconv("utf-8", "gbk","error:".$filename) . '.txt"');
    	header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
    	header('Expires:0');
    	header('Pragma:public');
    	echo iconv("utf-8", "gbk//IGNORE", $content);
    	exit;
    }
}

?>