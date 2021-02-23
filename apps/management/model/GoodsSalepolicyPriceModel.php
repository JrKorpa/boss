<?php
/**
 *  -------------------------------------------------
 *   @file		: GoodsSalepolicyPriceModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2016-2024 kela Inc
 *   @author	: Liulinyan
 *   @date		: 2016-02-16
 *  -------------------------------------------------
 */
class GoodsSalepolicyPriceModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_salepolicy_goods';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
			"policy_id"=>"销售策略id",
			"goods_id"=>"货号或款号",
			"chengben"=>"成本价",
			"sale_price"=>"销售价",
			"jiajia"=>"加价率",
			"sta_value"=>"固定值",
			"isXianhuo"=>"现货状态0是期货1是现货",
			"create_time"=>"创建时间",
			"create_user"=>"创建人",
			"check_time"=>"审核时间",
			"check_user"=>"审核",
			"status"=>"状态:1保存2申请3审核通过4未通过5取消",
			"is_delete"=>"删除 1未删除 2已删除");
		parent::__construct($id,$strConn);
	}
    
	public function getSalepolicyArr($goods_id,$channel){
		$sql = "SELECT ag.id
		FROM `base_salepolicy_goods` as sg
		LEFT JOIN `app_salepolicy_goods` as ag on  sg.goods_id=ag.goods_id
		LEFT JOIN `base_salepolicy_info` as si on ag.policy_id=si.policy_id
		LEFT JOIN (SELECT policy_id,channel from app_salepolicy_channel where channel='{$channel}' group by policy_id) as sc on  sc.policy_id=ag.policy_id
		where  sc.channel = '{$channel}' and ag.goods_id='{$goods_id}' group by sg.goods_id order by sg.id desc";
		//return $sql;
		return $data=$this->db()->getAll($sql);
	}
	
	
	//废除api直接跨库查询只针对goodsid
	public function getgoodsinfo($goodsid)
	{
		if(empty($goodsid))
		{
			return false;
		}
		$sql = "select a.order_goods_id,a.warehouse,a.warehouse_id,
		a.product_type1,a.cat_type1,b.is_default  
		from warehouse_shipping.warehouse_goods as a 
		inner join warehouse_shipping.warehouse as b on a.warehouse_id=b.id ";
		$sql .= " where a.goods_id='".$goodsid."' ";
		return $data=$this->db()->getAll($sql);
	}
	
	
	//废除期货商品查询,不需要关联app_salepolicy_goods表的逻辑
	//更新,直接走虚拟商品表   list_style_goods表  updata 20170114
	/**
     * 可销售商品分页查询
     * @param $where
     * @param $page
     * @param $pageSize
     * @param $useCache
     * @return $data
     */
    function pageQihuoList ($where,$page,$pageSize=10,$useCache=true)
	{   
		$where = array_filter($where);
		$sql =" select goods_id as id,goods_sn as goods_id,style_sn as goods_sn,style_name as goods_name,
			xiangkou,shoucun as finger,caizhi,yanse,0 as isXianhuo,
			product_type_id as product_type,
			cat_type_id as category,zhushizhong as stone,
			dingzhichengben as mingyichengben,last_update as update_time,'非仓库货品' as warehouse 
			from list_style_goods 
			where is_ok=1 and product_type_id>0 ";
		if(isset($where['goods_id_in']) && $where['goods_id_in']!='')
		{
			$sql .= " and goods_sn in('".$where['goods_id_in']."') ";
		}else{
			$sql_where = '';
			//只要是虚拟商品表上架的就ok了,其他的不管了,如果款式不用了,这些找相关部门把货品下架即可
			if(isset($where['goods_id']))
			{
				if(strpos($where['goods_id'],'-') !== false ){
					//所有的期货货号都带有-  符合,否则那就是款号
					$sql_where = " and goods_sn ='{$where['goods_id']}' ";
				}else{
					$sql_where = " and style_sn ='{$where['goods_id']}' ";
				}
			}
			if(isset($where['finger'])){
				$sql_where .= " and shoucun={$where['finger']}";
			}								
			if(isset($where['caizhi'])){
				$sql_where .= " and caizhi={$where['caizhi']}";
			}
			if(isset($where['yanse'])){
				$sql_where .= " and yanse={$where['yanse']}";
			}
			if(isset($where['xiangkou'])){
				$sql_where .= " and xiangkou= " .$where['xiangkou'] ;
			}
			$sql .= $sql_where;
		}
		$data = $this->db()->getPageListNew($sql,array(),$page,$pageSize,$useCache);
		$channelid = $where['channel'];
		$policyid = 0;
		if(isset($where['policy_id'])){
			$policyid = $where['policy_id'];	
		}
		
		if(!empty($data['data']))
		{
			$data['data'] = $this->getpolicygoods($data['data'],$channelid,$policyid);
		}
		return $data;
	}
		
	//定义一个方法,你给我一个商品数据列表,我把匹配到的销售政策给你
	//如果找到了商品,那么根据商品的属性去寻找销售政策
		
	public function getpolicygoods($data,$channelid,$policyid=0,$caizhi=array(),$yanse=array())
	{	
		foreach($data as $k=>$ginfo)	
		{
			//拿出产品线和款式分类去找销售政策
			$baoxianfei = 0;
			$product = $ginfo['product_type'];
			$cat = $ginfo['category'];
			//如果是现货 首先算出保险费
			if($ginfo['isXianhuo'] == 1){
				//如果是经销商
				if($ginfo['jingxiaoshangchengbenjia']>0 && $ginfo['jingxiaoshangchengbenjia'] > $ginfo['mingyichengben'] )
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
				$baoxianfei = $this->getbaoxianfei($product,$baoxian_xiankou);
			}
			$chenben = $ginfo['mingyichengben'] + $baoxianfei;
			
			//获取图片
			if ($ginfo['goods_sn'] == '仅售现货') {
				$ginfo['thumb_img'] = '';
			}else{
				$sql ="SELECT `thumb_img` FROM `app_style_gallery` WHERE `style_sn`='{$ginfo['goods_sn']}' AND `image_place` = 1";
				$thumb_img = $this->db()->getOne($sql);		        
				$data[$k]['thumb_img'] = $thumb_img;
			}
			
			
			//先去找根据货号,款号去找是否存在一口价的价格
			//一口价的不管渠道（优化以后也需要根据渠道去取 一口价,有可能一个货在不同的渠道销售不同的一口价）
			$ginfo = $this->getyikoujia($ginfo,$caizhi,$policyid,$channelid);
			$tmpobj = array();
			$policynames = array();
			$saleprices = array();
			$policyids = array();
			if(isset($ginfo['yikoujia']) && !empty($ginfo['yikoujia']))
			{
				
				foreach($ginfo['yikoujia'] as $obj)
				{
					$tmp['goods_id'] = $ginfo['goods_id'];
					$tmp['id'] = $obj['policy_id'];
					$tmp['policy_name'] = $obj['policy_name'];
					$tmp['sale_price'] = $obj['price'];
					array_push($policynames,$obj['policy_name']);
					array_push($saleprices,$tmp['sale_price']);
					array_push($policyids,$obj['policy_id']);
					array_push($tmpobj,$tmp);
				}
				unset($ginfo['yikoujia']);
				//如果找到了一口价,那么接着找满足条件的活动政策
				$ginfo = $this->getcombypolicy($ginfo,$channelid,1,$policyid);
				//不需要走下去了, 意味着这个商品是按款定价的东西,可以不用管了
			}else{
				//如果不是按款定价的,那么我们就走正常的销售政策(是否按款定价为否)
				$ginfo = $this->getcombypolicy($ginfo,$channelid,0,$policyid);
			}
			if(isset($ginfo['putong_data']) && !empty($ginfo['putong_data']))
			{
				foreach($ginfo['putong_data'] as $policy)
				{
					$tmp['goods_id'] = $ginfo['goods_id'];
					$tmp['id'] = $policy['policy_id'];
					$tmp['policy_name'] = $policy['policy_name'];
					$tmp['sale_price'] = round($chenben * $policy['jiajia']) + $policy['sta_value'];
					array_push($policynames,$policy['policy_name']);
					array_push($saleprices,$tmp['sale_price']);
					array_push($policyids,$policy['policy_id']);
					array_push($tmpobj,$tmp);
				}
				unset($ginfo['putong_data']);
			}
			
			if($ginfo['isXianhuo'] == 1)
			{
				//将18K白 转换为材质为18K对应的id  颜色为白对应的id
				$ginfo['yanse'] = $this->getyanseid($caizhi,$yanse,$ginfo['caizhi']);
				$ginfo['caizhi'] = $this->getcaizhiid($caizhi,$ginfo['caizhi']);
				$ginfo['product_type'] = $this->getproductid($ginfo['product_type']);
				$ginfo['category'] = $this->getcatid($ginfo['category']);
			}
			
			$data[$k] = $ginfo;
			$data[$k]['sprice']= $tmpobj;
			$data[$k]['policy_name_split'] = implode(',',$policynames);
			$data[$k]['sale_price_split'] = implode(',',$saleprices);
			$data[$k]['policy_name'] = $policynames[0];
			$data[$k]['sale_price'] = $saleprices[0];
			$data[$k]['policy_id_split'] = $policyids;
			$data[$k]['channel'] = $channelid;
			if(empty($tmpobj)){
				unset($data[$k]['sprice']);
			}
		}
		return $data;
	}
	
	
	
	//根据货品属性拿取销售政策(活动的销售政策,和默认的销售政策,非按款定价的政策)
	//$ginfo 是否为空,在调用方法之前去做
	//告诉我们是否只取活动的  非默认的
	public function getcombypolicy($ginfo,$channelid,$isactive=0,$policyid=0)
	{
		//is_kuanprice          0不是  1是
		//is_default            是否为默认政策1为默认2位不是默认
		//is_detete             记录是否有效 0有效1无效
		//policy_start_time     销售策略开始时间
		//policy_end_time      销售策略结束时间
		$goods_data = $ginfo;
		 
		$time = date('Y-m-d');
		$sql = " 
		select a.policy_id,a.policy_name,a.jiajia,a.sta_value,a.range_begin,a.range_end 
		from base_salepolicy_info as a 
		left join app_salepolicy_channel as b on a.policy_id=b.policy_id   
		where a.is_kuanprice=0 and a.is_delete=0 and a.bsi_status=3 and  
		a.policy_start_time <= '".$time."' and a.policy_end_time >= '".$time."' ";
		//如果是满足了按款定价的之后,那么只需要找出活动的销售政策即可
		if($ginfo['isXianhuo'] < 1)
		{
			if(isset($ginfo['product_type'])&& $ginfo['product_type'] != '')
			{
				//产品线id
				$sql .= " and a.product_type_id in(0,{$ginfo['product_type']}) ";
			}
			if(isset($ginfo['category'])&& $ginfo['category'] != '')
			{
				//款式分类id
				$sql .= " and a.cat_type_id in(0,{$ginfo['category']}) ";
			}
			
			if(isset($ginfo['xiangkou']) && $ginfo['xiangkou'] !='')
			{
				//镶口范围
				$xiangkou = $ginfo['xiangkou'];
				$sql .= " and $xiangkou >= a.range_begin and $xiangkou <= a.range_end ";
			}
			//期货目前只针对空托和空托女戒,政策货品类型为期货或者全部
			$sql .=" and a.tuo_type in(2,3) and a.huopin_type in(0,2) ";
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

			if(isset($ginfo['tuo_type'])){
	    		if($ginfo['tuo_type']==1){
	    		    $sql .=" and a.tuo_type in(0,1)";
	    		}else{
	    		    $sql .=" and a.tuo_type in(0,2,3)";
	    		}
			}			
				//现货   政策货品类型为现货或者全部
			$sql .=" and a.huopin_type in(1,2) ";
		}
		
		//告诉我们只取活动的 否则的话获取全部的(默认的和非默认的)
		if($isactive>0)
		{
			$sql .=" and a.is_default=0";
		}
		if($policyid>0)
		{
			$sql .= " and a.policy_id = $policyid ";
		}
		$sql .=" and b.channel= $channelid group by a.policy_id order by a.jiajia desc, a.is_default asc,a.policy_id desc limit 1";
		//echo $sql;
		$data = $this->db()->getAll($sql);
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
	
	
	
	
	//跟进货品属性拿取一口价数据
	//判断是否为空,在调用前判断
	public function getyikoujia($ginfo,$caizhi,$policyid=0,$channelid=0)
	{
		$goods_data = $ginfo;
		$sql = " select a.policy_id,a.price,b.policy_name,b.jiajia,b.sta_value 
			from app_yikoujia_goods as a 
			inner join base_salepolicy_info as b on a.policy_id=b.policy_id 
			inner join app_salepolicy_channel as d on a.policy_id=d.policy_id 
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
			$sql_one = $sql." a.goods_id='{$ginfo['goods_id']}' ";
			$data = $this->db()->getAll($sql_one);
			if(!empty($data))
			{
				$goods_data['yikoujia'] = $data;
				return $goods_data;
			}
		}
		if(isset($ginfo['goods_sn']) && $ginfo['goods_sn'] !='')
		{
			//要排除掉 指定了货号的一口价
			$sql .=" a.goods_sn='{$ginfo['goods_sn']}' and a.goods_id < 1 and ";
		}
		if(isset($ginfo['caizhi']) && $ginfo['caizhi'] !='')
		{
			$caizhiid = $ginfo['caizhi'];
			if($ginfo['isXianhuo']==1)
			{
				$caizhiid = $this->getcaizhiid($caizhi,$ginfo['caizhi']);
			}
			$sql .=" a.caizhi='{$caizhiid}' and ";
		}
		if(isset($ginfo['xiangkou']) && $ginfo['xiangkou'] !='')
		{
			$sql .=" a.small <= {$ginfo['xiangkou']} and a.sbig >= {$ginfo['xiangkou']}  and ";
		}
		//如果是现货的,再追加销售政策的空托类型
		if($ginfo['isXianhuo']==1)
		{
			//金托类型
			if(isset($ginfo['tuo_type'])){
	    		if($ginfo['tuo_type']==1){
	    		    $sql .=" a.tuo_type in(0,1) and ";
	    		}else{
	    		    $sql .=" a.tuo_type in(0,2,3) and ";
	    		}
			}
		}
		$sql .= " 1 ";
		//echo $sql.'print';
		$data = $this->db()->getAll($sql);
		if(!empty($data))
		{
			$goods_data['yikoujia'] = $data;
		}
		return $goods_data;
	}
	
	
	//改造可销售商品的分页查询
	
	function pageXianhuoList($where,$page,$pageSize=10,$caizhi,$yanse,$useCache=true)
	{   
		$result = array('error'=>0,'content'=>'');		
		//如果要过滤仓库的话,就根据当前登录的人,看他是属于哪个公司的,然后找出仓库来
		//$where['warehouse_id'] = array();
		$where = array_filter($where);
		$issinger = 0; 
		//只要是商品表里面有的就ok了,其他的不管了(1:产品线为彩钻的不管,2:产品线为钻石并且款式分类为裸石的不管)
		/*$parent['zhengshuhao']=$val['zhengshuhao'];
		$parent['jinzhong']=$val['jinzhong'];
		$parent['cart'] = $val['cart'];
		$parent['cut'] = $val['cut'];
		$parent['clarity']=$val['clarity'];
		$parent['color']=$val['color'];*/
		
		//新加字段数据,专为打标而生
		$dabiao_field = "a.changdu,a.zhushi,a.zhushilishu,a.fushi,a.fushilishu,a.fushizhong,a.zongzhong,a.pinpai,a.caizhi as dbcaizhi";
		
		$sql =" select a.id,a.goods_id,a.goods_sn,a.goods_name,a.zuanshidaxiao,
			a.jietuoxiangkou as xiangkou,a.shoucun as finger,a.caizhi,a.yanse,1 as isXianhuo,
			a.product_type1 as product_type,
			a.cat_type1 as category,
			a.mingyichengben,a.update_time,a.warehouse,a.jingxiaoshangchengbenjia,
			zhengshuhao,jinzhong,zuanshidaxiao as cart,qiegong as cut,jingdu as clarity,yanse as color,tuo_type, $dabiao_field
			from warehouse_shipping.warehouse_goods as a 
			inner join warehouse_shipping.warehouse as b on a.warehouse_id=b.id 
			where a.is_on_sale=2 and a.order_goods_id < 1 and b.is_default=1 ";
		
		/*如果是购物车*/
		if(isset($where['goods_id_in']) && $where['goods_id_in']!='')
		{
			$sql .= " and a.goods_id in('".$where['goods_id_in']."') ";
		}else{
			$sql_where = '';
			if(isset($where['goods_id'])){
				if(is_numeric($where['goods_id'])){
					$issinger = 1;
					//如果是纯数字,那么肯定是货号,否则那就是款号
					$sql_where = " and a.goods_id ='{$where['goods_id']}' ";
				}else{
					$sql_where = " and a.goods_sn ='{$where['goods_id']}' ";
				}
			}
			if(isset($where['finger'])){
				$sql_where .= " and a.shoucun={$where['finger']}";
			}
			if(isset($where['xiangkou'])){
				$sql_where .= " and a.jietuoxiangkou= " .$where['xiangkou'] ;
			}
			if(isset($where['caizhi']) && isset($where['yanse'])){
				$caizhiname = $caizhi[$where['caizhi']];
				$yansename = $yanse[$where['yanse']];
				$sql_where .= " and a.caizhi='{$caizhiname}{$yansename}'";
			}elseif(isset($where['caizhi']) && !isset($where['yanse'])){
				$caizhiname = $caizhi[$where['caizhi']];
				$sql_where .= " and a.caizhi like '%{$caizhiname}%'";
			}elseif(!isset($where['caizhi']) && isset($where['yanse'])){
				$yansename = $yanse[$where['yanse']];
				$sql_where .= " and a.caizhi like '%{$yansename}%'";
			}
			
			//如果是经销商
			if(isset($where['company_id_list'])){
				$sql_where .= " and a.company_id = '{$where['company_id_list']}'";	
			}
			
			if($issinger>0)
			{
				//说明填写的是货号
				$sqlone = "select b.is_default,a.is_on_sale,a.order_goods_id,
						a.product_type1,a.cat_type1,a.warehouse
						from warehouse_shipping.warehouse_goods as a 
						inner join warehouse_shipping.warehouse as b on a.warehouse_id=b.id  
						where 1 ".$sql_where ." limit 1";
				$data = $this->db()->getAll($sqlone);
				if(empty($data))
				{
					$result['error'] = 1;
					$result['content'] = '仓库里面没有货号';
				}else{
					$ginfo = $data[0];
					if($ginfo['is_on_sale'] != 2)
					{
						$result['error'] = 1;
						$result['content'] = '货号目前不是库存状态';
					}elseif($ginfo['order_goods_id']>0){
						$result['error'] = 1;
						$result['content'] = '货品已经绑定订单了,订单明细的id为'.$ginfo['order_goods_id'];
					}elseif($ginfo['is_default'] != '1'){
						$result['error'] = 1;
						$result['content'] = '货品所在的仓库为'.$ginfo['warehouse'].' 该仓库为非默认上架仓库';
					}elseif($ginfo['product_type1'] == '彩钻'){
						$result['error'] = 1;
						$result['content'] = '货品的产品线是彩钻,不走销售政策,请选择彩钻下单,或找产品部核对商品信息';
					}elseif($ginfo['product_type1'] == '钻石' && $ginfo['cat_type1'] =='裸石'){
						$result['error'] = 1;
						$result['content'] = '货品的产品线是钻石,款式分类为裸石,这类货品不走销售政策,请找产品部核对该货品信息';
					}
				}
			}
			if($result['error']>0)
			{
				return $result;
			}
			$sql .= $sql_where ." and ( a.product_type1 !='彩钻' or ( a.product_type1 !='钻石' and a.cat_type1 !='裸石') ) ";
		}
		$data = $this->db()->getPageListNew($sql,array(),$page,$pageSize,$useCache);
		$channelid = $where['channel'];
		if( isset($where['policy_id']) && $where['policy_id'] > 0 )
		{
			//主要是购物车那里
			$policy_id = $where['policy_id'];
		}else{
			$policy_id = 0;	
		}
		if(!empty($data['data']))
		{
			$data['data'] = $this->getpolicygoods($data['data'],$channelid,$policy_id,$caizhi,$yanse);
		}
		return $data;
	}
	
	//过滤掉空的数组里面的值(非0)
	public function arrayfilter($data)
	{
		if(empty($data))
		{
			return array();
		}
		foreach($data as $k=>$v)
		{
			if($v === '')
			{
				unset($data[$k]);
			}
		}
		return $data;
	}
	
	//定义一个函数用来返回材质id
	public function getcaizhiid($caizhi,$caizhiname)
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
	public function getyanseid($caizhi,$yanse,$caizhiname)
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
	public function getproductid($pname='')
	{
		if($pname=='')
		{
			return 0;
		}
		$sql = "select product_type_id from app_product_type where product_type_status=1 and product_type_name='{$pname}'";
		$pid = $this->db()->getOne($sql);
		return $pid;
	}
	public function getcatid($cname='')
	{
		if($cname=='')
		{
			return 0;
		}
		$sql = "select cat_type_id from  app_cat_type where cat_type_status=1 and cat_type_name='{$cname}'";
		$cid = $this->db()->getOne($sql);
		return $cid;
	}
	
	
	
	
	
	//拿取保险费
	public function getbaoxianfei($producttype,$xiangkou)
	{
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
			$data = $this->db()->getAll($sql);
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
	
	
	/**
	 * used : 根据条件获取一口价的金额
	**/
	public function getyikoujiainfo($where,$channel=1)
	{
		
		/*
		b.bsi_status=3  销售状态已经审核
		b.is_delete=0   销售状态是否已经删除(0未删除,1已经删除)
		b.is_kuanprice=1  是否按款定价(用作一口价)
		a.is_delete = 0 是否已经删除(0未删除,1已经删除)
		*/
		
		$sql = " select a.goods_id,a.goods_sn,a.price,b.policy_id,b.policy_name,b.jiajia,b.sta_value 
		from app_yikoujia_goods as a 
		inner join base_salepolicy_info as b on a.policy_id=b.policy_id  
		inner join app_salepolicy_channel as c on a.policy_id=c.policy_id  and c.channel=$channel
		where b.bsi_status=3 and b.is_delete=0 and b.is_kuanprice=1 and a.is_delete=0 ";  
		if(isset($where['isXianhuo']))
		{
			$isxianhuo = $where['isXianhuo'];
			//获取类型包含全部和他自身
			$sql .= " and a.isXianhuo= $isxianhuo and b.huopin_type in($isxianhuo,2)";
		}

        if(isset($where['policy_id']) && !empty($where['policy_id'])){
            $sql .=" and b.policy_id = ".$where['policy_id'];
        }
		//如果有货号 ,则不必要过滤其他的属性咯
		if(isset($where['goods_id']))
		{
			$gid = $where['goods_id'];
			$sql_one = $sql." and a.goods_id = '{$gid}' order by a.price desc limit 1";
			//echo $sql_one;
			$data = $this->db()->getAll($sql_one);
			if(!empty($data)){
				return $data;
			}
		}

		//款号匹配  并且货号为空
		if(isset($where['goods_sn']))
		{
			$gsn = $where['goods_sn'];
			$sql .= " and a.goods_sn = '{$gsn}' and a.goods_id='' ";
		}
		//镶口匹配(如果没有镶口 则拿石头去匹配)
		if(isset($where['jietuoxiangkou']))
		{
			$xiankou = $where['jietuoxiangkou'];
			if( $xiankou > 0 ){
				$sql .= " and a.small <= $xiankou and a.sbig >= $xiankou ";	
			}else{
				$cart = $where['zuanshidaxiao'];
				$sql .= " and a.small <= $cart and a.sbig >= $cart ";
			}
		}
		//材质匹配
		if(isset($where['caizhi']))
		{
			$caizhiid = empty($where['caizhi']) ? 1 : $where['caizhi'];
			$sql .= " and a.caizhi = $caizhiid ";	
		}
		
		//如果是现货的,再追加销售政策的空托类型
		if($where['isXianhuo']==1)
		{
			//金托类型
			if(isset($where['tuo_type']))
			{
				$tuotype = $where['tuo_type'];
				$sql .=" and a.tuo_type in (0,$tuotype) ";
			}
		}
		
		//线扣
		$sql .=" order by a.price desc limit 1";
		//echo $sql;
		return $this->db()->getAll($sql);
		
	}
	
	
	/**
	 * used : 根据条件筛选出价格和加价率
	 * parames: where 赛选的条件
	 * channel: 渠道
	**/
	public function getsalepolicyinfo($where,$channel,$isactive=0)
	{
		$time = date('Y-m-d');
		$sql = " 
		select a.policy_id,a.policy_name,a.jiajia,a.sta_value,a.range_begin,a.range_end,a.cert,a.color,a.clarity,a.tuo_type 
		from front.base_salepolicy_info as a 
		left join front.app_salepolicy_channel as b on a.policy_id=b.policy_id   
		where a.is_kuanprice=0 and a.is_delete=0 and a.bsi_status=3 and b.channel='".$channel.
		"' and a.policy_start_time <= '".$time."' and a.policy_end_time >= '".$time."' ";
		
		if($isactive>0)
		{
			$sql .=" and a.is_default != 1 ";
		}else{
			$sql .=" and a.is_default = 1 ";	
		}

		//销售政策
        if(isset($where['policy_id']) && !empty($where['policy_id'])){
            $sql .=" and a.policy_id = ".$where['policy_id'];
        }
		//产品线
		if(isset($where['product_type']))
		{
			$pt = $where['product_type'];
			$sql .=" and a.product_type in('全部','','{$pt}')";
		}
		//款式分类
		if(isset($where['cat_type']))
		{
			$ct = $where['cat_type'];
			$sql .=" and a.cat_type in('全部','','{$ct}')";
		}
		//货品类型(现货还是期货)
		if(isset($where['isXianhuo']))
		{
			$isxianhuo = $where['isXianhuo'];
			$sql .=" and a.huopin_type in(2,$isxianhuo)";
		}
		//金托类型
		if(isset($where['tuo_type']))
		{
			
			if($where['tuo_type']==1)
			        $sql .=" and a.tuo_type in(0,1)";
			else
			        $sql .=" and a.tuo_type in(0,2,3)";			       
		}
		//主石
		if(isset($where['zuanshidaxiao']))
		{
			$zsdx = $where['zuanshidaxiao'];
			$sql .=" and a.zhushi_begin <= $zsdx and a.zhushi_end >= $zsdx ";
		}
		//镶口大小
		if(isset($where['jietuoxiangkou']))
		{
			$xk = $where['jietuoxiangkou'];
			$zdx = $where['zuanshidaxiao'];
			if($xk > 0)
			{
				$sql .=" and a.range_begin <= $xk and a.range_end >= $xk ";
			}else{
				$sql .=" and a.range_begin <= $zdx and a.range_end >= $zdx ";	
			}
		}
		
		//现货再追加一个证书类型
		if(isset($where['zhengshuleibie']))
		{
			if(empty($where['zhengshuleibie']))
			{
				$zslb = '无';
			}else{
				$zslb = $where['zhengshuleibie'];	
			}
			$sql .=" and (a.cert='全部类型' or a.cert regexp '{$zslb}' ) ";
		}
		
		
		//把系列加上
		if(isset($where['goods_sn']))
		{
			$xilie = $this->getxilie($where['goods_sn']);
			$sql.=" and ( a.xilie='全部系列' or a.xilie regexp '{$xilie}' ) ";
		}
		$sql .= " order by a.jiajia desc,a.sta_value desc  limit 1";
		//echo $sql;die();
		return $this->db()->getAll($sql);
	}
	
	
	//给我一个款号,我帮你把他所属的系列拿出来
	public function getxilie($gsn='')
	{
		if($gsn=='')
		{
			return '';
		}
		$sql = "select xilie from front.base_style_info where check_status=3 and style_sn='{$gsn}'";
		$xilieid = $this->db()->getOne($sql);
		if(empty($xilieid))
		{
			return '空白';
		}else{
			$allid = array_filter(explode(',',$xilieid));
			$xilieids = implode(',',$allid);
			$sqlone = "select name from app_style_xilie where id in({$xilieids})";
			$allxilie = $this->db()->getAll($sqlone);
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
	
	
}
    


?>