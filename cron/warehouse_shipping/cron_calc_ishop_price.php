<?php
/**
脚本要求：
名词解释：

1.每天晚上定时跑脚本，计算智慧门店各渠道总部现货零售价。
*/

	//header("Content-type:text/html;charset=utf-8");
	date_default_timezone_set('Asia/Shanghai');
	require_once('MysqlDB.class.php');
	ini_set('memory_limit', '512M');
    set_time_limit(0);
	$new_conf = [
		'dsn'=>"mysql:host=192.168.1.132;dbname=warehouse_shipping",
		'user'=>"cuteman",
		'password'=>"QW@W#RSS33#E#",
		'charset' => 'utf8'
   ]; 

   $db = new MysqlDB($new_conf);

        //获取所有需要计算价格的渠道
        $param_channel_id = !empty($_GET['channel_id']) ? $_GET['channel_id'] : 0;
		$param_goods_id = !empty($_GET['goods_id']) ? $_GET['goods_id'] : 0;
		$param_output = !empty($_GET['output']) ? $_GET['output'] : 0;
		$param_debug = !empty($_GET['debug']) ? $_GET['debug'] : 0;
		if (PHP_SAPI == 'cli') {
		    $param_channel_id = empty($_SERVER['argv'][1]) ? 0 : $_SERVER['argv'][1];
		    $param_goods_id = empty($_SERVER['argv'][2]) ? 0 : $_SERVER['argv'][2];
		    $param_output = empty($_SERVER['argv'][3]) ? 0 : $_SERVER['argv'][3];
		    $param_debug = empty($_SERVER['argv'][4]) ? 0 : $_SERVER['argv'][4];
		}		
		//if(empty($param_channel_id))
		//    return false;   


    $caizhi = getCaizhiList();
    $yanse  = getJinseList();


    $sql ="select DISTINCT s.id,s.channel_name from front.base_salepolicy_info a,front.app_salepolicy_channel b left join cuteframe.sales_channels s on b.channel=s.id  where a.policy_id=b.policy_id and a.is_delete=0 and a.bsi_status=3 and s.channel_name not like '%已闭店%' ";
    if(!empty($param_channel_id)){
        $sql .=" and s.id='{$param_channel_id}'";
    }   
    $channel_list = $db->getAll($sql);
  
    $sql_goods_list ="
			SELECT
			 g.id,g.goods_id,g.goods_sn,g.goods_name,g.zuanshidaxiao,
			g.jietuoxiangkou as xiangkou,g.shoucun as finger,g.caizhi,g.yanse,1 as isXianhuo,
			g.product_type1 as product_type,
			g.zhengshuleibie,
			g.cat_type1 as category,
			g.mingyichengben,g.update_time,g.warehouse,g.put_in_type,g.jingxiaoshangchengbenjia,
			g.zhengshuhao,g.jinzhong,g.zuanshidaxiao as cart,g.qiegong as cut,g.jingdu as clarity,g.yanse as color,g.tuo_type,'0' as is_quick_diy,'1' as is_chengpin,g.mairugongfei,
			if(ifnull(g.biaoqianjia,0)>0,
			   g.biaoqianjia*1*0.25,
			   g.mingyichengben*1.00*
			   if( (select count(1) from front.app_style_jxs j where j.style_sn=g.goods_sn)>0,
			       1.21,
			       1.17
			   )
			   +
			   if(g.goods_sn='QIBAN',
			       300,
			       0
			   )
			   +
			   if(ifnull(g.with_fee,0)>0,
			      g.with_fee,
			      20
			   )
			) as pifajia
			FROM
				warehouse_shipping.warehouse_goods g
			INNER JOIN `warehouse_shipping`.`warehouse` w ON g.warehouse_id = w.id
			AND w.is_delete = 1
			AND w.is_default = 0
			WHERE
			  g.is_on_sale=2 
			  and g.cat_type1<>'裸石'
			  and g.put_in_type in (1,2,3,4)
			  and g.box_sn IN (
					'10-A-2',
					'10-B-1',
					'10-B-2',
					'10-C-1',
					'10-C-2',
					'10-C-3',
					'10-C-4',
					'10-D-1',
					'10-D-2',
					'10-K-1',
					'10-K-10',
					'10-K-2',
					'10-K-5',
					'10-K-6',
					'10-K-7',
					'10-K-8',
					'10-K-9',
					'10-S-1',
					'S-1',
					'S-2',
					'S-K-1',
					'S-K-2',
					'S-K-3',
					'S-K-4',
					'S-K-5',
					'S-K-6'
				) "; 
    if(!empty($param_goods_id))
        $sql_goods_list .= " and g.goods_id='{$param_goods_id}'"; 
    //$xianhuo_goods_list = $db->getAll($sql);

    //echo "<pre>";
    //print_r($xianhuo_goods_list);exit();
    $date = date("Y-m-d H:i:s");
   	//$note_string = $date."--可供智慧门店销售的总部现货数量:".count($xianhuo_goods_list)."\r\n<br>";
	//echo $note_string;
    //file_put_contents('cron_calculate_ishop_sale_price.log',$note_string,FILE_APPEND);
    $k2=0;
    $pagesize = 500;
   foreach ($channel_list as $key1 => $channel_row) {
   	   echo "start ....".$channel_row['channel_name']."<br>";
   	   $sql_insert = "";
   	   $k =0;


        $flag = 1;
        $page = 1;
        while($flag){				   
		        $start =0;
		        if($page<=0){
                    $start = 1;
                }else{
                    $start = ($page - 1) * $pagesize + 1;
                }     
		        $sql_goods_list_limit = $sql_goods_list ." limit ". ($start-1) .",". $pagesize; 
		        //echo $sql_goods_list_limit ;
				$xianhuo_goods_list =$db->getAll($sql_goods_list_limit);
				if(empty($xianhuo_goods_list))
				    $flag = false;
				$page = $page+1 ;   	   
		        foreach ($xianhuo_goods_list as $key2 => $goods) {
		       	    $goods['jingxiaoshangchengbenjia'] = round($goods['pifajia'],2); 
		       	    $data=array();
		       	    $data[] = $goods;
		            $res = getpolicygoods($data,$channel_row['id'],0,$caizhi,$yanse);
		            //print_r($res);        	    
		       	    if(!empty($res)){
		                $res_insert_row = $res[0];
		                if(!empty($res_insert_row['sale_price_split'])){
		                	$goods_sale_price = max(explode(',',$res_insert_row['sale_price_split']));
		                    $sql_insert .= " (0,'{$goods['goods_id']}','{$channel_row['id']}','{$goods['pifajia']}','{$goods_sale_price}','{$date}'),";
		       	            $k++;
		       	        }
		       	    }
		       	    $k2++;
		       	    echo $channel_row['channel_name'].$k2."\r\n<br>";
		       }
		}
		       
	   if(!empty($sql_insert)){
	   	    $sql_delete = "delete from warehouse_shipping.warehouse_goods_ishop_price where channel_id='{$channel_row['id']}' ";
		   	if(!empty($param_goods_id))
			   	    $sql_delete .=" and goods_id='{$param_goods_id}'";	   	    
	   	    $db->query($sql_delete);
	   	    $sql_insert = "insert into warehouse_shipping.warehouse_goods_ishop_price values ".rtrim($sql_insert,',');
	   	    //echo $sql;
	   	    $db->query($sql_insert);
	   	    //$note_string = $date.'--'.$channel_row['channel_name']."计算出销售价的商品共:".$k."\r\n<br>";
	   	    //echo $note_string;
	   	    //file_put_contents('cron_calculate_ishop_sale_price.log',$note_string,FILE_APPEND);
	   }       
   }
   
   echo "<br>end";

  




	function getpolicygoods($data,$channelid,$policyid=0,$caizhi=array(),$yanse=array())
	{	
		global $db;
		foreach($data as $k=>$ginfo)	
		{
			//拿出产品线和款式分类去找销售政策
			$baoxianfei = 0;
			$product = $ginfo['product_type'];
			$cat = $ginfo['category'];
			$is_chengpin = $ginfo['is_chengpin'];
			$xiangkou = $ginfo['xiangkou'];
			
			//如果是现货 首先算出保险费
			if($ginfo['isXianhuo'] == 1){
				//如果是经销商（只要大于0就ok）
				if( $ginfo['jingxiaoshangchengbenjia']>0 )
				{
					$ginfo['mingyichengben'] = $ginfo['jingxiaoshangchengbenjia'];
				}
				if(!empty($ginfo['xiangkou']) && $ginfo['xiangkou'] >0 )
				{
					$baoxian_xiankou = $ginfo['xiangkou'];
				}else{
					$baoxian_xiankou = $ginfo['zuanshidaxiao'];
					//优化2 销售商品搜索和下单时，当镶口有值时，用商品的镶口与销售政策镶口区间匹配；当镶口为0时，用商品的主石大小与销售政策的镶口区间匹配，有符合条件的销售政策时，根据对应销售政策规则计算建议零售价。
					$ginfo['xiangkou'] = $ginfo['zuanshidaxiao'];
				}
				if($ginfo['tuo_type'] != 1){
					$baoxianfei = getbaoxianfei($product,$baoxian_xiankou);
				}
			}
			
			$chenben = $ginfo['mingyichengben'] + $baoxianfei;
			
			//获取图片
			if ($ginfo['goods_sn'] == '仅售现货') {
				$ginfo['thumb_img'] = '';
			}else{
				$sql ="SELECT `thumb_img` FROM front.`app_style_gallery` WHERE `style_sn`='{$ginfo['goods_sn']}' AND `image_place` = 1";
				$thumb_img = $db->getOne($sql);		        
				//$data[$k]['thumb_img'] = $thumb_img;
				$ginfo['thumb_img'] = $thumb_img;
			}
			
			
			//先去找根据货号,款号去找是否存在一口价的价格
			//一口价的不管渠道（优化以后也需要根据渠道去取 一口价,有可能一个货在不同的渠道销售不同的一口价）
			$ginfo = getyikoujia($ginfo,$caizhi,$policyid,$channelid);
			$tmpobj = array();
			$policynames = array();
			$saleprices = array();
			$policyids = array();
			if(isset($ginfo['yikoujia']) && !empty($ginfo['yikoujia']))
			{
				foreach($ginfo['yikoujia'] as $obj)
				{
					$tmp['goods_id'] = $ginfo['goods_id'];
					$tmp['is_chengpin'] = $ginfo['is_chengpin'];
					$tmp['goods_sn'] = $ginfo['goods_sn'];
					$tmp['id'] = $obj['policy_id'];
					$tmp['policy_name'] = $obj['policy_name'];
					$tmp['chengben'] = $chenben;
					$tmp['sale_price'] = $obj['price'];
					$tmp['cert'] = $obj['cert'];  
					$tmp['color'] = $obj['color'];
					$tmp['clarity'] = $obj['clarity'];
					$tmp['tuo_type'] = $obj['tuo_type'];
					$tmp['is_yikoujia'] = 1;
					array_push($policynames,$obj['policy_name']);
					array_push($saleprices,$tmp['sale_price']);
					array_push($policyids,$obj['policy_id']);
					array_push($tmpobj,$tmp);
				}
				unset($ginfo['yikoujia']);
				//如果找到了一口价,那么接着找满足条件的活动政策
				$ginfo = getcombypolicy($ginfo,$channelid,1,$policyid);
				//不需要走下去了, 意味着这个商品是按款定价的东西,可以不用管了
			}else{
				//如果不是按款定价的,那么我们就走正常的销售政策(是否按款定价为否)
				$ginfo = getcombypolicy($ginfo,$channelid,0,$policyid);
			}
			if(isset($ginfo['putong_data']) && !empty($ginfo['putong_data']))
			{
				foreach($ginfo['putong_data'] as $policy)
				{
					$tmp['goods_id'] = $ginfo['goods_id'];
					$tmp['is_chengpin'] = $ginfo['is_chengpin'];
					$tmp['goods_sn'] = $ginfo['goods_sn'];
					$tmp['id'] = $policy['policy_id'];
					$tmp['policy_name'] = $policy['policy_name'];
					$tmp['sale_price'] = round($chenben * $policy['jiajia']) + $policy['sta_value'];
					$tmp['cert'] = $policy['cert'];//a.cert,a.color,a.clarity,a.tuo_type
					$tmp['color'] = $policy['color'];
					$tmp['clarity'] = $policy['clarity'];
					$tmp['tuo_type'] = $policy['tuo_type'];
					$tmp['is_yikoujia'] = 0;
					$tmp['chengben'] = $chenben;
					$tmp['jiajia'] = $policy['jiajia'];
					$tmp['sta_value'] = $policy['sta_value'];
					array_push($policynames,$policy['policy_name']);
					array_push($saleprices,$tmp['sale_price']);
					array_push($policyids,$policy['policy_id']);
					array_push($tmpobj,$tmp);
				}
				unset($ginfo['putong_data']);
			}else{
                //产品线为“普通黄金”的现货货品如果找不到一口价销售政策则按新的定价规则（销售价=当日金价*金重+工费*加价率）
                //如果有一口价销售政策 则按销售政策（即按目前方法定价）
                //产品线为“普通黄金”的现货货品如果找不到一口价销售政策则按新的定
                //价规则（销售价=当日金价*金重+工费*加价率）
                //当日金价：取黄金价格最后一条记录的价格，
                //金重：取商品列表金重，
                //工费：取商品列表买入工费（mairugongfei),
                //加价率：全国统一 取黄金价格最后一条记录的计价率
                //var_dump($ginfo);die;
                if($ginfo['product_type'] == '普通黄金' && empty($tmpobj)){
                    $gold_price = 0;
                    $gold_jiajialv = 0;
                    //销售价=当日金价*金重+工费*加价率
                    //当日金价and加价率
                    $sql_gold = "select gold_price,jiajialv from app_order.app_gold_jiajialv where is_usable = 1 order by id desc limit 1";
                    $gold_price_info = $this->db()->getRow($sql_gold);
                    //var_dump($gold_price_info);die;
                    if(!empty($gold_price_info)){
                        $gold_price = $gold_price_info['gold_price'];
                        $gold_jiajialv = $gold_price_info['jiajialv'];
                    }
                    $tmp['goods_id'] = $ginfo['goods_id'];
                    $tmp['is_chengpin'] = $ginfo['is_chengpin'];
                    $tmp['goods_sn'] = $ginfo['goods_sn'];
                    $tmp['id'] = 0;
                    $tmp['policy_name'] = '普通黄金定价';
                    //$tmp['sale_price'] = round($chenben * $policy['jiajia']) + $policy['sta_value'];
                    $tmp['sale_price'] = bcadd(bcmul($gold_price,$ginfo['jinzhong'],3),bcmul($ginfo['mairugongfei'],$gold_jiajialv,3),2);
                    //var_dump($tmp['sale_price']);die;
                    $tmp['cert'] = '';//a.cert,a.color,a.clarity,a.tuo_type
                    $tmp['color'] = $ginfo['color'];
                    $tmp['clarity'] = $ginfo['clarity'];
                    $tmp['tuo_type'] = $ginfo['tuo_type'];
                    $tmp['is_yikoujia'] = 0;
                    $tmp['chengben'] = $gold_price;
                    $tmp['jiajia'] = $gold_jiajialv;
                    $tmp['sta_value'] = 0;
                    array_push($policynames,'普通黄金定价');
                    array_push($saleprices,$tmp['sale_price']);
                    array_push($policyids,0);
                    array_push($tmpobj,$tmp);
                }
            }

			if($ginfo['isXianhuo'] == 1)
			{
				//将18K白 转换为材质为18K对应的id  颜色为白对应的id
				$ginfo['yanse'] = getyanseid($caizhi,$yanse,$ginfo['caizhi']);
				$ginfo['caizhi'] = getcaizhiid($caizhi,$ginfo['caizhi']);
				$ginfo['product_type'] = getproductid($ginfo['product_type']);
				$ginfo['category'] = getcatid($ginfo['category']);
			}
			
			$data[$k] = $ginfo;
			$data[$k]['channel'] = $channelid;
			if(!empty($tmpobj))
			{
				$data[$k]['sprice']= $tmpobj;
				$data[$k]['policy_name_split'] = implode(',',$policynames);
				$data[$k]['sale_price_split'] = implode(',',$saleprices);
				$data[$k]['policy_name'] = isset($policynames[0])?$policynames[0]:'';
				$data[$k]['sale_price'] = isset($saleprices[0])?$saleprices[0]:'';
				$data[$k]['policy_id_split'] = $policyids;
			}else{
				$data[$k]['sprice']= array();
				$data[$k]['policy_name_split'] = '';
				$data[$k]['sale_price_split'] = '';
				$data[$k]['policy_name'] = '';
				$data[$k]['sale_price'] = '';
				$data[$k]['policy_id_split'] = '';
			}
		}
		return $data;
	}



	//判断是否为空,在调用前判断
	function getyikoujia($ginfo,$caizhi,$policyid=0,$channelid=0)
	{
		global $db;
	    $goods_data = $ginfo;
	    //成品定制一口价
	    //if(isset($ginfo['tuo_type']) && $ginfo['tuo_type']==1 && isset($ginfo['isXianhuo']) && $ginfo['isXianhuo']==0){
	    //    return $this->getYikoujiaNew($ginfo, $caizhi,$policyid,$channelid);
	    //} 
	    $sql = " select a.policy_id,a.price,b.policy_name,b.jiajia,b.sta_value,a.cert,a.color,a.clarity,a.tuo_type
			from front.app_yikoujia_goods as a
			inner join front.base_salepolicy_info as b on a.policy_id=b.policy_id
			inner join front.app_salepolicy_channel as d on a.policy_id=d.policy_id
			where b.is_kuanprice=1 and b.is_delete=0 and b.bsi_status=3 and a.is_delete=0 and ";
	    //b.is_kuanprice=0 and b.is_delete=0 and b.bsi_status=3 增加销售政策的管控
	    //一口价也增加销售渠道的管控
	    if($channelid>0)
	    {
	        $sql .=" d.channel = $channelid and ";
	    }
	    if(isset($ginfo['isXianhuo']))
	    {
	        $sql .=" a.isXianhuo ={$ginfo['isXianhuo']} and ";
	    }
	    if($policyid>0)
	    {
	        $sql .=" a.policy_id = $policyid and ";
	    }
	    if(isset($ginfo['goods_id']) && $ginfo['goods_id'] !='')
	    {
	        //$sql .= " a.goods_id='{$ginfo['goods_id']}' and ";
	        $sql_one = $sql." a.goods_id='{$ginfo['goods_id']}' ";
	        //echo $sql_one;
	        $data = $db->getAll($sql_one);
	         if(!empty($data))
	         {
    	         $goods_data['yikoujia'] = $data;
    	         return $goods_data;
	         } 
	    }
	    if(isset($ginfo['goods_sn']) && $ginfo['goods_sn'] !='')
	    {
	        //要排除掉 指定了货号的一口价
	        $sql .=" a.goods_sn='{$ginfo['goods_sn']}' and a.goods_id< 1 and ";
	    }
	    if(isset($ginfo['caizhi']) && $ginfo['caizhi'] !='')
	    {
	        $caizhiid = $ginfo['caizhi'];
	        if($ginfo['isXianhuo']==1)
	        {
	            $caizhiid = getcaizhiid($caizhi,$ginfo['caizhi']);
	        }
	        $sql .=" a.caizhi='{$caizhiid}' and ";
	    }
	    if(isset($ginfo['xiangkou']) && $ginfo['xiangkou'] !='')
	    {
	        $sql .=" a.small <= {$ginfo['xiangkou']} and a.sbig >= {$ginfo['xiangkou']}  and ";
	    }
	    if(isset($ginfo['color']) && $ginfo['color'] !=''){
	        $sql .=" a.color in ('全部','{$ginfo['color']}')  and ";
	    }
	    if(isset($ginfo['clarity']) && $ginfo['clarity'] !=''){
	        $sql .=" a.clarity in('全部','{$ginfo['clarity']}')  and ";
	    }
	    if(isset($ginfo['cert']) && $ginfo['cert'] !=''){
	        $sql .=" a.cert in('{$ginfo['cert']}','全部') and ";
	    }
	    if(isset($ginfo['shape']) && $ginfo['shape'] !=''){
	        //$sql .=" a.shape = '{$ginfo['shape']}'  and ";
	    }
	    //金托类型
	    if(isset($ginfo['tuo_type']) && $ginfo['tuo_type']==1){  
	            $sql .=" a.tuo_type in (0,1) and ";
	    }else{
	            $sql .=" a.tuo_type in (0,2,3) and ";
	    }	
	    $sql .= " 1 ";

	    $data = $db->getAll($sql);
	    if(!empty($data))
	    {
	        $goods_data['yikoujia'] = $data;
	    }else{
	        $goods_data['yikoujia'] = array();
	    }
	    return $goods_data;
	}




function getcombypolicy($ginfo,$channelid,$isactive=0,$policyid=0)
	{
		//is_kuanprice          0不是  1是
		//is_default            是否为默认政策1为默认2位不是默认
		//is_detete             记录是否有效 0有效1无效
		//policy_start_time     销售策略开始时间
		//policy_end_time      销售策略结束时间
		global $db,$param_debug;

		$goods_data = $ginfo;
		$time = date('Y-m-d');
		$sql = " 
		select a.policy_id,a.policy_name,a.jiajia,a.sta_value,a.range_begin,a.range_end,a.cert,a.color,a.clarity,a.tuo_type 
		from front.base_salepolicy_info as a 
		left join front.app_salepolicy_channel as b on a.policy_id=b.policy_id   
		where a.is_kuanprice=0 and a.is_delete=0 and a.bsi_status=3 and  
		a.policy_start_time <= '".$time."' and a.policy_end_time >= '".$time."' ";
		//如果是满足了按款定价的之后,那么只需要找出活动的销售政策即可
		if($ginfo['isXianhuo'] < 1)
		{
			if(isset($ginfo['product_type'])&& $ginfo['product_type'] != '')
			{
				//产品线id
				$sql .= " and a.product_type_id in(0,1,{$ginfo['product_type']}) ";
			}
			if(isset($ginfo['category'])&& $ginfo['category'] != '')
			{
				//款式分类id
				$sql .= " and a.cat_type_id in(0,1,{$ginfo['category']}) ";
			}
			
			if(isset($ginfo['xiangkou']) && $ginfo['xiangkou'] !='')
			{
				//镶口范围
				$xiangkou = $ginfo['xiangkou'];
				$sql .= " and $xiangkou >= a.range_begin and $xiangkou <= a.range_end ";
			}
			//证书类型
			/* if(isset($ginfo['zhengshuleibie']))
			{
			    if(empty($ginfo['zhengshuleibie']))
			    {
			        $zslb = '无';
			    }else{
			        $zslb = $ginfo['zhengshuleibie'];
			    }
			    $sql .=" and (a.cert='全部类型' or a.cert regexp '{$zslb}' ) ";
			} */
		    if(!empty($ginfo['zhengshuleibie']))
		    {
		        $zslb = $ginfo['zhengshuleibie'];
		        $sql .=" and (a.cert='全部类型' or a.cert regexp '{$zslb}' ) ";
		    }			   
				
			if(empty($ginfo['is_chengpin']) && !empty($ginfo['color'])){
			    $sql .=" and (a.color='全部' or a.color regexp '{$ginfo['color']}')";
			}
			if(empty($ginfo['is_chengpin']) && !empty($ginfo['clarity'])){
			    $sql .=" and (a.clarity='全部' or a.clarity regexp '{$ginfo['clarity']}')";
			} 
   			//期货目前只针对空托和空托女戒,政策货品类型为期货或者全部
    		//$sql .=" and a.tuo_type in(0,2,3) and a.huopin_type in(0,2) ";
			if(isset($ginfo['tuo_type'])){
				if($ginfo['tuo_type']==1)
			        $sql .=" and a.tuo_type in(0,1)";
			    else
			        $sql .=" and a.tuo_type in(0,2,3)";
			}
			$sql .=" and a.huopin_type in(0,2) ";
		}else{
			//现货
			if(isset($ginfo['product_type'])&& $ginfo['product_type'] != '')
			{
				//产品线
				$sql .= " and a.product_type in('全部','','{$ginfo['product_type']}') ";
			}
			if(isset($ginfo['category'])&& $ginfo['category'] != '')
			{
				//款式分类
				$sql .= " and a.cat_type in('全部','','{$ginfo['category']}') ";
			}
			
			if(isset($ginfo['xiangkou']) && $ginfo['xiangkou'] !=='')
			{
				//镶口范围
				$xiangkou = $ginfo['xiangkou'];
				$sql .= " and $xiangkou >= a.range_begin and $xiangkou <= a.range_end ";
			}
			if(isset($ginfo['zuanshidaxiao']) && $ginfo['zuanshidaxiao'] !=='')
			{
				$zuanshidaxiao = $ginfo['zuanshidaxiao'];
				$sql .= " and $zuanshidaxiao >= a.zhushi_begin and $zuanshidaxiao <= a.zhushi_end ";
			}
			//金托类型
		    if(isset($ginfo['tuo_type']) && $ginfo['tuo_type']==1){  
		            $sql .=" and a.tuo_type in (0,1) ";
		    }else{
		            $sql .=" and a.tuo_type in (0,2,3) ";
		    }
			//现货   政策货品类型为现货或者全部
			$sql .=" and a.huopin_type in(1,2) ";
			
			//现货再追加一个证书类型
			if(isset($ginfo['zhengshuleibie']))
			{
				if(empty($ginfo['zhengshuleibie']))
				{
					$zslb = '无';
				}else{
					$zslb = $ginfo['zhengshuleibie'];
				}
				$sql .=" and (a.cert='全部类型' or a.cert regexp '{$zslb}' ) ";
			}
		}
		
		//追加一个根据款而定的系列
		if(!empty($ginfo['goods_sn'])){
    		$xilie = getxilie($ginfo['goods_sn']);    		
    		$sql.=" and ( a.xilie='全部系列' or a.xilie regexp '{$xilie}' ) ";
		}
		//echo $sql;die();
		
		
		//告诉我们只取活动的 否则的话获取全部的(默认的和非默认的)
		if($isactive>0)
		{
			$sql .=" and a.is_default != 1 ";
		}else{
			$sql .=" and a.is_default = 1 ";	
		}

		if($policyid>0)
		{
			$sql .= " and a.policy_id = $policyid ";
		}
		$sql .=" and b.channel= $channelid order by a.jiajia desc,a.sta_value desc  limit 1";
        if(!empty($param_debug))
            echo $sql;		
		$data = $db->getAll($sql);
		if(!empty($data))
		{
			$goods_data['putong_data'] = $data;
		}else{
			//否则的话 就没有找到销售政策
			$goods_data['putong_data'] = array();
		}
		return $goods_data;
		//如果没有满足按款定价的话,那么需要找出活动的销售政策和默认的销售政策
	}


	//定义一个函数用来返回材质id
	function getcaizhiid($caizhi,$caizhiname)
	{
		if(empty($caizhi) || $caizhiname ==''){
			return 0;	
		}
		
		foreach($caizhi as $k=>$v)
		{
			if(strpos($caizhiname,$v) !== false)
			{
				return $k;
			}
		}
	}
	//定义一个函数用来返回颜色id
	function getyanseid($caizhi,$yanse,$caizhiname)
	{
		if(empty($caizhi) || empty($yanse) || $caizhiname ==''){
			return 0;	
		}
		foreach($caizhi as $k=>$v)
		{
			if(strpos($caizhiname,$v) !== false)
			{
				$caizhiname = str_replace($v,'',$caizhiname);
				break;
			}
		}
		foreach($yanse as $k=>$v)
		{
			if(trim($caizhiname) === trim($v))
			{
				return $k;
			}
		}
	}
	
	
	//为了下单那里现货用的是产品线的id，款式分类也是用的id
	function getproductid($pname='')
	{
		global $db;
		if($pname=='')
		{
			return 0;
		}
		$sql = "select product_type_id from front.app_product_type where product_type_status=1 and product_type_name='{$pname}'";
		$pid = $db->getOne($sql);
		return $pid;
	}
	function getcatid($cname='')
	{
		global $db;
		if($cname=='')
		{
			return 0;
		}
		$sql = "select cat_type_id from  front.app_cat_type where cat_type_status=1 and cat_type_name='{$cname}'";
		$cid = $db->getOne($sql);
		return $cid;
	}
	//拿取保险费
    function getbaoxianfei($producttype,$xiangkou)
	{
		 global $db;  
		//定义所有需要加保险费用的产品线
		$allproducttype = array('钻石','珍珠','珍珠饰品','翡翠','翡翠饰品','宝石','宝石饰品','钻石饰品','宝石饰品','宝石');
		//定义保险费默认值
		$baoxianfei = 0;
		//判断是否需要拿取保险费 (镶嵌类的现货,拖类型)
		if(in_array($producttype,$allproducttype))
		{
			//拿取保险费
			$xiangkou = $xiangkou * 10000;
			$i = 0;
			$j = 0;
			$k = 0;
			$sql = 'SELECT `id`,`min`,`max`,`price`,`status` FROM front.`app_style_baoxianfee` WHERE 1';
			$data = $db->getAll($sql);
			foreach($data as $v)
			{
				$max[$i] = $v['max'] * 10000;
				$min[$j] = $v['min'] * 10000;
				$fee[$k] = $v['price'];
				$i++;$j++;$k++; 
			}
			$count = count($max);
			for($i = 0; $i <$count; $i ++) 
			{
				if ($xiangkou >= $min[$i] && $xiangkou <= $max[$i])
				{
					return $fee[$i];
				}
			}
		}
		return $baoxianfei;
	}

    function getAttributeValues($attribute_code)
    {
    	global $db;   
        if(empty($attribute_code)){
            return array();
        }
        $sql = "SELECT a.`attribute_name`,a.`attribute_code`,b.`att_value_name` as attribute_value FROM front.`app_attribute` AS a,front.`app_attribute_value` AS b WHERE 
a.`attribute_id`=b.`attribute_id` AND b.att_value_status=1 AND a.attribute_code='{$attribute_code}'";
        return $db->getAll($sql);
    }

    function getCaizhiList($all = true)
    {
        $data= array(
            '10'=>'9K',
            '13'=>'10K',
            '9'=>'14K',
            '1'=>'18K',
            '11'=>'PT900',
            '2'=>'PT950',
            '17'=>'PT990',
            '12'=>'PT999',            
            '3'=>'18K&PT950',
            '4'=>'S990',
            '6'=>'S925',
            '8'=>'足金',
            '5'=>'千足银',
            '7'=>'千足金',
            '14'=>'千足金银',
            '15'=>'裸石',
            '16'=>'无',
            '0'=>'其它',  
            '18'=>'S999'      
        );  
        if($all !== true){
             $res = getAttributeValues("caizhi");
             $data = array_column($res,"attribute_value");
             asort($data);
        }
        return $data;      
    }	

   function getJinseList($all = true)
    {
        $data= array(
            '0'=>'无',
            '1'=>'白',
            '2'=>'黄',           
            '3'=>'玫瑰金',
            '4'=>'分色',
            '5'=>'彩金',
            '6'=>'玫瑰黄',
            '7'=>'玫瑰白',
            '8'=>'黄白',
            '10'=>'按图做'
        );
        if($all !== true){
            $res = getAttributeValues("caizhiyanse");
            $data = array_column($res,"attribute_value");
        }
        return $data;
    }

 function getxilie($gsn='')
	{
		global $db;    
		if($gsn=='')
		{
			return '';
		}
		$sql = "select xilie from front.base_style_info where check_status=3 and style_sn='{$gsn}'";
		$xilieid = $db->getOne($sql);
		if(empty($xilieid))
		{
			return '空白';
		}else{
			$allid = array_filter(explode(',',$xilieid));
			$xilieids = implode(',',$allid);
			$sqlone = "select name from front.app_style_xilie where id in({$xilieids})";
			$allxilie = $db->getAll($sqlone);
			if(!empty($allxilie))
			{
				$xiliename = array_column($allxilie,'name');
				if(count($xiliename)>1)
				{
					$xilie_name = implode('|',$xiliename);
				}else{
					$xilie_name = $xiliename[0];	
				}
				return $xilie_name;
			}else{
				return '空白';	
			}
		}
	}

?>