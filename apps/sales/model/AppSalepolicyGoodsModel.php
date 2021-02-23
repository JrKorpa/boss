<?php
/**
 *  -------------------------------------------------
 *   @file		: AppSalepolicyGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2015-08-28
 *   @update	:
 *  -------------------------------------------------
 */
class AppSalepolicyGoodsModel extends Model
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
    /**
     * 可销售商品分页查询
     * @param $where
     * @param $page
     * @param $pageSize
     * @param $useCache
     * @return $data
     */
    function pageList ($where,$page,$pageSize=15,$useCache=true)
	{   
	    $where['is_sale'] = 1;//上架
	    $where['is_delete'] = 1;//可用（未删除）
	    $where['status'] = 3;//通过审核
	    $where['is_policy'] = 2;//有销售政策
	    if(!empty($where['goods_id'])){
	        $sql_where = "(sg.goods_id ='{$where['goods_id']}' or sg.goods_sn = '{$where['goods_id']}')";
	    }else{
	        $sql_where = "ag.goods_id <>''";
	    }
	    if(isset($where['isXianhuo']) && $where['isXianhuo'] !=''){
	        $is_xianhuo = (int)$where['isXianhuo']?1:0;
	        $sql_where .= " AND sg.isXianhuo={$is_xianhuo}";
	    }
	    $inner_where = '';
	    if(isset($where['channel']) && $where['channel'] !=''){
	        $inner_where = "where channel = '{$where['channel']}'";
	        $sql_where .= " AND sc.channel = '{$where['channel']}'";
	    }
	    if(isset($where['finger']) && $where['finger'] !=''){
	        $sql_where .= " AND sg.finger={$where['finger']}";
	    }
	    if(isset($where['caizhi']) && $where['caizhi'] !=''){
	        $sql_where .= " AND sg.caizhi={$where['caizhi']}";
	    }
	    if(isset($where['yanse']) && $where['yanse'] !=''){
	        $sql_where .= " AND sg.yanse={$where['yanse']}";
	    }
	    if(isset($where['type']) && $where['type'] !=''){	        
	        $sql_where .= " AND sg.type={$where['type']}";
	    }
	    if(isset($where['is_base_style']) && $where['is_base_style'] !=''){
	        $sql_where .= " AND sg.is_base_style='{$where['is_base_style']}'";
	    }
	    if(isset($where['xiangkou']) && $where['xiangkou'] !=''){
	        $sql_where .= " AND sg.stone='{$where['xiangkou']}'";
	    }

	    if(isset($where['policy_name']) && $where['policy_name'] !=''){
	        $sql_where .= " AND si.policy_name like '%{$where['policy_name']}%'";
	    }
	    if(isset($where['update_start_time']) && $where['update_start_time'] !=''){
	        $sql_where .= " and ag.`update_time` >= '{$where['update_start_time']} 00:00:00'";
	    }
	    if(isset($where['update_end_time']) && $where['update_end_time'] !=''){
	        $sql_where .= " AND ag.`update_time` <= '{$where['update_start_time']} 59:59:59'";
	    }
	    if(isset($where['is_delete']) && $where['is_delete'] !=''){
	        $sql_where .= " AND ag.is_delete={$where['is_delete']} AND si.is_delete=0";
	    }
	    if(isset($where['is_sale']) && $where['is_sale'] !=''){
	        $sql_where .= " AND sg.is_sale={$where['is_delete']} AND sg.is_valid =1";
	    }
	    if(isset($where['status']) && $where['status'] !=''){
	        //$sql_where .= " AND ag.`status`='{$where['status']}' AND si.bsi_status=3";
	        $sql_where .= " AND si.bsi_status=3";
	    }
	    if(isset($where['is_policy']) && $where['is_policy'] !=''){
	        $sql_where .= " AND sg.`is_policy`={$where['is_policy']}";
	    } 
	    
	    $sql = "SELECT ag.goods_id,ag.id,ag.policy_id,ag.chengben, ag.sale_price, ag.jiajia, ag.sta_value, ag.chengben_compare, ag.isXianhuo, ag.create_time, ag.update_time,ag.create_user,ag.check_time,ag.check_user,sg.`is_policy`,ag.status,ag.is_delete,sg.xiangkou,sg.stone,sg.finger,sg.caizhi,sg.yanse,sg.type,sg.is_base_style,sg.goods_sn,sg.warehouse,si.policy_name,sc.channel,GROUP_CONCAT(si.policy_name) as policy_name_split,GROUP_CONCAT(ag.sale_price) as sale_price_split 
FROM `base_salepolicy_goods` as sg
LEFT JOIN `app_salepolicy_goods` as ag on  sg.goods_id=ag.goods_id 
LEFT JOIN `base_salepolicy_info` as si on ag.policy_id=si.policy_id
LEFT JOIN (SELECT policy_id,channel from app_salepolicy_channel {$inner_where} group by policy_id) as sc on  sc.policy_id=ag.policy_id
where {$sql_where} group by sg.goods_id order by sg.id desc";
		//echo $sql;
       //echo $sql;
		$data = $this->db()->getPageListNew($sql,array(),$page,$pageSize,$useCache);
		
		if(!empty($data['data']) && is_array($data['data'])){
		    foreach($data['data'] as $key=>$val){
		        if ($val['goods_sn'] == '仅售现货') {
		            $val['thumb_img'] = '';
		        }else{
    		        $sql ="SELECT `thumb_img` FROM `app_style_gallery` WHERE `style_sn`='{$val['goods_sn']}' AND `image_place` = 1";
    		        $thumb_img = $this->db()->getOne($sql);		        
    		        $val['thumb_img'] = $thumb_img;
		        }
		        $policy_name_split = explode(",",$val['policy_name_split']);
		        $sale_price_split = explode(",",$val['sale_price_split']);
		        foreach($policy_name_split as $_key=>$_val){
		            if(!isset($sale_price_split[$_key])){
		                continue;
		            }		            
		            $val['sprice'][]= array("policy_name"=>$_val,'sale_price'=>$sale_price_split[$_key]);
		        }		        
		        $data['data'][$key] = $val;		        
		    }
		}else{
		    $data['data']   = array();
		}
		return $data;
	}
	
	/**
     * 可销售商品分页查询(功能同pageList，暂时不用)
     * @param $where
     * @param $page
     * @param $pageSize
     * @param $useCache
     * @return $data
     */
    function _pageList ($where,$page,$pageSize=15,$useCache=true)
	{   
	    $where['is_sale'] = 1;//上架
	    $where['is_delete'] = 1;//可用（未删除）
	    $where['status'] = 3;//通过审核
	    $where['is_policy'] = 2;//有销售政策
	    if(!empty($where['goods_id'])){
	        $sql_where = "(sg.goods_id ='{$where['goods_id']}' or sg.goods_sn = '{$where['goods_id']}')";
	    }else{
	        $sql_where = "ag.goods_id <>''";
	    }
	    if(isset($where['isXianhuo']) && $where['isXianhuo'] !=''){
	        $is_xianhuo = (int)$where['isXianhuo']?1:0;
	        $sql_where .= " AND sg.isXianhuo={$is_xianhuo}";
	    }
	    if(isset($where['channel']) && $where['channel'] !=''){
	        $sql_where .= " AND sc.channel_ids like '%,{$where['channel']}|%'";
	    }
	    if(isset($where['finger']) && $where['finger'] !=''){
	        $sql_where .= " AND sg.finger='{$where['finger']}'";
	    }
	    if(isset($where['caizhi']) && $where['caizhi'] !=''){
	        $sql_where .= " AND sg.caizhi='{$where['caizhi']}'";
	    }
	    if(isset($where['yanse']) && $where['yanse'] !=''){
	        $sql_where .= " AND sg.yanse='{$where['yanse']}'";
	    }
	    if(isset($where['type']) && $where['type'] !=''){	        
	        $sql_where .= " AND sg.yanse='{$where['type']}'";
	    }
	    if(isset($where['is_base_style']) && $where['is_base_style'] !=''){
	        $sql_where .= " AND sg.is_base_style='{$where['is_base_style']}'";
	    }
	    if(isset($where['xiangkou']) && $where['xiangkou'] !=''){
	        $sql_where .= " AND sg.xiangkou='{$where['xiangkou']}'";
	    }

	    if(isset($where['policy_name']) && $where['policy_name'] !=''){
	        $sql_where .= " AND si.policy_name like '%{$where['policy_name']}%'";
	    }
	    if(isset($where['update_start_time']) && $where['update_start_time'] !=''){
	        $sql_where .= " and ag.`update_time` >= '{$where['update_start_time']} 00:00:00'";
	    }
	    if(isset($where['update_end_time']) && $where['update_end_time'] !=''){
	        $sql_where .= " AND ag.`update_time` <= '{$where['update_start_time']} 59:59:59'";
	    }
	    if(isset($where['is_delete']) && $where['is_delete'] !=''){
	        $sql_where .= " AND ag.is_delete={$where['is_delete']} AND si.is_delete=0";
	    }
	    if(isset($where['is_sale']) && $where['is_sale'] !=''){
	        $sql_where .= " AND sg.is_sale={$where['is_delete']} AND sg.is_valid =1";
	    }
	    if(isset($where['status']) && $where['status'] !=''){
	        //$sql_where .= " AND ag.`status`='{$where['status']}' AND si.bsi_status=3";
	        $sql_where .= "  AND si.bsi_status=3";
	    }
	    if(isset($where['is_policy']) && $where['is_policy'] !=''){
	        $sql_where .= " AND sg.`is_policy`='{$where['is_policy']}'";
	    } 
	    
	    $sql = "SELECT ag.goods_id,ag.id,ag.policy_id,ag.chengben, ag.sale_price, ag.jiajia, ag.sta_value, ag.chengben_compare, ag.isXianhuo, ag.create_time, ag.update_time,ag.create_user,ag.check_time,ag.check_user,sg.`is_policy`,ag.status,ag.is_delete,sg.xiangkou,sg.stone,sg.finger,sg.caizhi,sg.yanse,sg.type,sg.is_base_style,sg.goods_sn,sg.warehouse,si.policy_name,sc.channel_ids,GROUP_CONCAT(si.policy_name) as policy_name_split,GROUP_CONCAT(ag.sale_price) as sale_price_split 
FROM `base_salepolicy_goods` as sg
LEFT JOIN `app_salepolicy_goods` as ag on  sg.goods_id=ag.goods_id 
LEFT JOIN `base_salepolicy_info` as si on ag.policy_id=si.policy_id
LEFT JOIN (SELECT policy_id,concat(',',group_concat(channel,'|')) as channel_ids from app_salepolicy_channel GROUP BY policy_id) as sc on  sc.policy_id=ag.policy_id
where {$sql_where} group by sg.goods_id order by sg.id desc";
       //echo $sql;
		$data = $this->db()->getPageListNew($sql,array(),$page,$pageSize,$useCache);
		
		if(!empty($data['data']) && is_array($data['data'])){
		    foreach($data['data'] as $key=>$val){
		        
		        $channel_ids = explode(",",str_replace("|","",trim($val['channel_ids'],",")));
		        if(!empty($where['channel']) && in_array($where['channel'],$channel_ids)){
		            $val['channel'] = $where['channel'];		            
		        }else{
		            $val['channel'] = current($channel_ids);
		        }
		        
		        if ($val['goods_sn'] == '仅售现货') {
		            $val['thumb_img'] = '';
		        }else{
    		        $sql ="SELECT `thumb_img` FROM `app_style_gallery` WHERE `style_sn`='{$val['goods_sn']}' AND `image_place` = 1";
    		        $thumb_img = $this->db()->getOne($sql);		        
    		        $val['thumb_img'] = $thumb_img;
		        }
		        
		        $data['data'][$key] = $val;
		    }
		    	
		    $policy_name_split = explode(",",$val['policy_name_split']);
	        $sale_price_split = explode(",",$val['sale_price_split']);
	        foreach($policy_name_split as $_key=>$_val){
	            if(!isset($sale_price_split[$_key])){
	                continue;
	            }		            
	            $val['sprice'][]= array("policy_name"=>$_val,'sale_price'=>$sale_price_split[$_key]);
	        }		        
	        $data['data'][$key] = $val;
		}else{
		    $data['data']   = array();
		}
		return $data;
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
		$sql =" select sg.goods_id as id,sg.goods_sn as goods_id,sg.style_sn as goods_sn,sg.style_name as goods_name,
			sg.xiangkou,sg.shoucun as finger,sg.caizhi,sg.yanse,0 as isXianhuo,
			sg.product_type_id as product_type,bsi.company_type_id,
			sg.cat_type_id as category,sg.zhushizhong as stone,case WHEN sg.xiangkou<>0 and pt.parent_id = 3 then 3 else 1 end AS tuo_type,
			sg.dingzhichengben as mingyichengben,sg.last_update as update_time,'非仓库货品' as warehouse,case WHEN (pt.parent_id = 3 AND sg.xiangkou = 0) OR pt.parent_id <> 3 then 1 else 0 end as is_chengpin  
			from list_style_goods sg inner join front.app_product_type pt on sg.product_type_id = pt.product_type_id left join front.base_style_info as bsi on sg.style_sn=bsi.style_sn
			where sg.is_ok=1 and sg.product_type_id>0 ";
        $channelid = $where['channel']; 
		if(isset($where['goods_id_in']) && $where['goods_id_in']!='')
		{
			$sql .= " and sg.goods_sn in('".$where['goods_id_in']."') ";
		}else{
			$sql_where = '';
			//只要是虚拟商品表上架的就ok了,其他的不管了,如果款式不用了,这些找相关部门把货品下架即可
			if(isset($where['goods_id']))
			{
				if(strpos($where['goods_id'],'-') !== false ){
					//所有的期货货号都带有-  符合,否则那就是款号
					$sql_where = " and sg.goods_sn ='{$where['goods_id']}' ";
				}else{
					$sql_where = " and sg.style_sn ='{$where['goods_id']}' ";
				}
			}
            
            /*
            if($_SESSION['companyId']<>58){
	            $compan_type= $this->userByCompany();
	            $sql_where .=" AND (( bsi.company_type_id LIKE '%,".$compan_type.",%' ) OR bsi.company_type_id IS NULL OR bsi.company_type_id='') ";
	            $sql_where .=" AND (( sg.xiangkou_company_type LIKE '%,".$compan_type.",%' ) OR sg.xiangkou_company_type IS NULL OR sg.xiangkou_company_type='') ";
            }*/
            //echo $channelid;
            if(!empty($channelid)){
            	$company_type=$this->userCompanyTypeByChannelId($channelid);            	
                if(!empty($company_type)){
                	//echo $compan_type;
		            $sql_where .=" AND (( bsi.company_type_id LIKE '%,".$company_type.",%' ) OR bsi.company_type_id IS NULL OR bsi.company_type_id='') ";
		            $sql_where .=" AND (( sg.xiangkou_company_type LIKE '%,".$company_type.",%' ) OR sg.xiangkou_company_type IS NULL OR sg.xiangkou_company_type='' OR sg.xiangkou_company_type='0') ";
                }
            }
            
            
			if(isset($where['finger'])){
				$sql_where .= " and sg.shoucun={$where['finger']}";
			}								
			if(isset($where['caizhi'])){
				$sql_where .= " and sg.caizhi={$where['caizhi']}";
			}
			if(isset($where['yanse'])){
				$sql_where .= " and sg.yanse={$where['yanse']}";
			}
			if(isset($where['xiangkou'])){
				$sql_where .= " and sg.xiangkou= " .$where['xiangkou'] ;
			}
			if(isset($where['is_quick_diy'])){
			    $sql_where .= " and is_quick_diy= ".$where['is_quick_diy'] ;
			}			
            if(isset($where['tuo_type']) && $where['tuo_type'] !=1){
                //空托搜索
               $sql_where .= " and sg.xiangkou<>0 and pt.parent_id = 3 ";
            }
            $sql .= $sql_where;
		}
		//echo $sql;
		$data = $this->db()->getPageListNew($sql,array(),$page,$pageSize,$useCache);
		
		$policyid = 0;
		if(isset($where['policy_id'])){
			$policyid = $where['policy_id'];	
		}
	
		//成品定制
		if(!empty($data['data'])){
		    if(isset($where['tuo_type']) && $where['tuo_type'] == 1){
		        $where['ginfos'] = $data['data'];
		        $result = $this->getChenpingdingzhiList($where);
		        if($result['error']){
		           $data['error'] = $result['error'];
		           $data['data']  = $result['data'];
		        }
		        $data['data'] =$result['data'];
		    }else{		        
			    $data['data'] = $this->getpolicygoods($data['data'],$channelid,$policyid);
		    }
		}else{
		    $data['error'] = "没有找到支持定制的虚拟商品！";
		    $data['data']  = array();
		}		
		//print_r($data);exit;
		return $data;
	}
	//成品定制
	
	public function getChenpingdingzhiList($where){
	    return $this->getCpdzList($where);
	    
	    $result = array('error'=>false,'data'=>array());
	    $goods_id = isset($where['goods_id'])?$where['goods_id']:'';
	    $xiangkou = isset($where['xiangkou'])?$where['xiangkou']:'';
	    $clarity = isset($where['clarity'])?$where['clarity']:'';
	    $color = isset($where['color'])?$where['color']:"";
	    $shape = isset($where['shape'])?$where['shape']:'';
	    $channel_id = isset($where['channel'])?$where['channel']:'';
	    $policy_id = isset($where['policy_id'])?$where['policy_id']:0;
        
	    $cert = isset($where['cert'])?$where['cert']:'';
	    $tuo_type = isset($where['tuo_type'])?$where['tuo_type']:'';
	    $goods_key = isset($where['goods_key'])?$where['goods_key']:'';
	    $attrModel = new GoodsAttributeModel(17);
	    $shapeNameArr = $attrModel->getShapeList();
	    $shapeIdArr  = array_flip($shapeNameArr);//成品定制  形状名称=>形状ID 映射关系 列表
	    if(empty($where['ginfos'])){
	        $sql =" select sg.goods_id as id,sg.goods_sn as goods_id,sg.style_sn as goods_sn,sg.style_name as goods_name,
	        sg.xiangkou,sg.shoucun as finger,sg.caizhi,sg.yanse,0 as isXianhuo,
	        sg.product_type_id as product_type,
	        sg.cat_type_id as category,sg.zhushizhong as stone,case WHEN sg.xiangkou<>0 and pt.parent_id = 3 then 3 else 1 end AS tuo_type,
	        sg.dingzhichengben as mingyichengben,sg.last_update as update_time,'非仓库货品' as warehouse,IFNULL((pt.parent_id=3 and sg.xiangkou=0) or pt.parent_id<>3,0) as is_chengpin
	        from list_style_goods sg inner join front.app_product_type pt on sg.product_type_id = pt.product_type_id
	        where sg.is_ok=1 and sg.product_type_id>0 and sg.goods_sn='{$goods_id}'";
	        $ginfos = $this->db()->getAll($sql);	        
	    }else{
	        $ginfos = $where['ginfos'];
	    }
	    if(empty($ginfos)){
	        $result['error'] = "虚拟货号不存在";
	        return $result;
	    }else if(count($ginfos)==1 && $xiangkou==""){
	        $xiangkou = $ginfos[0]['xiangkou'];
	    }
         
    	foreach ($ginfos as $k=>$ginfo){
        	$ginfo['clarity'] = $clarity;
        	$ginfo['color']  = $color;
        	$ginfo['shape'] = $shape;
        	$ginfo['zhengshuleibie'] = $cert;
        	$ginfo['tuo_type'] = 1;
        	$ginfos[$k] = $ginfo;
    	}
    	$datalist = $this->getpolicygoods($ginfos,$channel_id,$policy_id);
    	$sprice_tongji=0;//不存在销售政策的统计有多少个
    	foreach ($datalist as $key=>$data){
            if($data['sprice']){
            	foreach ($data['sprice'] as $price_key => $pricearr) {
            		if($pricearr['tuo_type']=='2' || $pricearr['tuo_type']=='3' ){
            			unset($datalist[$key]['sprice'][$price_key]);
            		}
            	}
            }                		
    	    if(empty($data['sprice'])){
    	        unset($datalist[$key]);
    	    }
    	}
    	if(empty($datalist)){
    	    $result['error'] = "没有符合条件的成品定制商品。提示：搜索商品找不到销售政策！";
    	    return $result;
    	}
    	
    	//print_r($datalist);
    	//此 形状枚键值举仅针对 款式石头有效，不可公用.
    	$shape_arr = array(1=>"垫形",2=>"公主方形",3=>"祖母绿形",4=>"心形",5=>"蛋形",6=>"椭圆形",7=>"橄榄形",8=>"三角形",9=>"水滴形",10=>"长方形",11=>"圆形",12=>"梨形",13=>"马眼形");
    	foreach ($datalist as $key=>$data){
    	   $spriceList =array();
    	   $spriceListError =array();
    	   foreach ($data['sprice'] as $k=>$v){
    	        $policy_id = $v['id'];
    	        $sale_price = $v['sale_price'];    	       
            	$chengben = $v['chengben'];
            	$jiajia = isset($v['jiajia'])?$v['jiajia']:1;//一口价，无jiajia
            	$sta_value = isset($v['sta_value'])?$v['sta_value']:0;//一口价，无sta_value
        	    $xiangkou = $data['xiangkou'];
        	    $goods_sn = $v['goods_sn'];
        	    $v['policy_id'] = $policy_id;
        	    $v['calc_tip'] = "货号：{$v['goods_id']}<br/>";
        	    $v['calc_tip'] .= "销售政策：{$v['policy_name']}<br/>";
        	    if($v['is_chengpin']==1){
        	        $v['clarity'] = "";//主石净度
        	        $v['cert'] = "";//证书类型
        	        $v['stone_price'] = "";//石头价格
        	        $v['carat_min'] = "";//主石最小值
        	        $v['carat_max'] = "";//主石最大值
        	        $v['stone_id'] = 0;//主石ID
        	        $v['calc_tip'] .= "成品销售价(无需配钻)=空托成本价*加价率+固定值=({$chengben}*{$jiajia}+{$sta_value}=<b style='color:red'>{$sale_price}</b>";
        	        $v['goods_key'] = md5($v['goods_id']."&".$v['id']."&0");//货品ID&销售政策ID&石头ID 的MD5
        	        if(!empty($goods_key) && $goods_key<>$v['goods_key']){
        	            $v['goods_key2'] = $goods_key;
        	            unset($datalist[$key]['sprice'][$k]);
        	            continue;
        	        }
        	        $spriceList[] = $v;
        	        continue;//重要
        	    }
        	    //接下来都是需要空托+配石组合成品 
        	    /*
        	     * 形状校验
        	     * */
        	    $sql = "select a.stone_cat,a.stone_attr from front.rel_style_stone a inner join front.base_style_info b on a.style_id=b.style_id where b.style_sn='{$goods_sn}' and a.stone_cat in(1,2) and stone_position=1 limit 1";
        	    $stoneInfoList = $this->db()->getAll($sql);
        	    $stonePriceList = array();
        	    $shapeList = array();
        	    foreach ($stoneInfoList as  $stoneInfo){
            	    if(empty($stoneInfo)){
            	        $style_shape = "";
            	    }else{
            	        if($stoneInfo['stone_cat']==1){
            	            $style_shape = "圆形";
            	        }else{
            	            $stoneAttr = unserialize($stoneInfo['stone_attr']);
            	            $shape_id = isset($stoneAttr['shape_zhushi'])?$stoneAttr['shape_zhushi']:'';
        	                $style_shape = isset($shape_arr[$shape_id])?$shape_arr[$shape_id]:$shape_id;
            	        }
            	        $shape = isset($shapeIdArr[$style_shape])?$shapeIdArr[$style_shape]:"";
            	        //echo $shape.'-'.$style_shape.'=';
            	        if($shape<>''){
            	            //获取货号的款号中的形状后，需要重新获取石头列表
            	            //避免前面查询的石头初步满足条件，但形状不符被过滤，导致没有符合的石头，但此时有满足镶口+1的石头的（很特殊的情况）
                	        $_xiangkou = $xiangkou * 100;
                	        $sql_str = "";
                	        $sql = "select * from front.diamond_fourc_info where `status`=1 and carat_min<={$_xiangkou} and carat_max>={$_xiangkou}";
                	        if($shape != ""){
                	            $sql_str .=" AND shape={$shape}";
                	        }
                	        if($color != ""){
                	            $sql_str .=" AND color='{$color}'";
                	        }
                	        if($clarity !=""){
                	            $sql_str .=" AND clarity='{$clarity}'";
                	        }
                	        if($cert !=""){
                	            $sql_str .=" AND cert='{$cert}'";
                	        }                	        
                	        $_stonePriceList = $this->db()->getAll($sql.$sql_str);
                	        if(empty($_stonePriceList)){
                	            $_xiangkou = $_xiangkou+1;
                	            $sql = "select * from front.diamond_fourc_info where `status`=1 and carat_min<={$_xiangkou} and carat_max>={$_xiangkou}";
                	            $_stonePriceList = $this->db()->getAll($sql.$sql_str);
                	        }
                	        $v['calc_tip'] .="形状【{$style_shape}】{$shape},颜色【{$color}】,净度【{$clarity}】,证书类型【{$cert}】匹配石头价格成功！<br/>";
                	        if(!empty($_stonePriceList) && !in_array($shape,$shapeList)){
                	            foreach ($_stonePriceList as $_stone){
                	                if(!isset($stonePriceList[$_stone['id']])){
                	                    $_stone['shapeName'] = $style_shape;
                	                    $stonePriceList[$_stone['id']] = $_stone;
                	                }
                	            }
                	            $shapeList[] = $shape;
                	        }
            	        }else{
            	            $v['error_tip']="货号{$goods_id}所属的款号{$goods_sn}没有主石形状";
            	            $datalist[$key]['errors'][]=$v;	  
            	            unset($datalist[$key]);
            	            continue;
            	        }
            	        
            	    }
        	    }        	     
    	        if($xiangkou>0 && empty($stonePriceList)){
    	            $v['sale_price'] ='';
    	            $v['stone_price']='';        	             
    	            $v['calc_tip'] = "没有符合条件的成品定制商品。提示：成品定制没有符合条件的钻石！匹配条件:形状【{$style_shape}】{$shape},颜色【{$color}】,净度【{$clarity}】,证书类型【{$cert}】";;
    	            $datalist[$key]['errors'][] = $v; //调试专用
    	            unset($datalist[$key]['sprice'][$k]);
    	            continue;
    	        }  
                
        	    foreach ($stonePriceList as $k2=>$v2){
        	        $spriceRow = $v;// 用 $spriceRow 替代$v 非常重要
        	        $spriceRow['key2'] = $k2;
        	        $stone_price = $v2['price'];
        	        //销售政策 颜色与石头配置颜色 校对
        	        $color_arr = explode(',',$spriceRow['color']);
        	        if($spriceRow['color']=="全部" || in_array($v2['color'],$color_arr)){
        	            $spriceRow['calc_tip'] .="主石颜色校对:销售政策【{$spriceRow['color']}】 与 石头【{$v2['color']}】 正确！<br/>";
        	            $spriceRow['color'] = $v2['color'];//主石颜色
        	        }else{
        	            $spriceRow['error_tip']="销售政策主石颜色【{$spriceRow['color']}】与石头配置颜色【{$v2['color']}】不符";
        	            $datalist[$key]['errors'][]=$spriceRow;
        	            //unset($datalist[$key]['sprice'][$k]);
        	            continue;
        	        }
        	        //销售政策 主石净度与石头配置主石净度 校对
        	        $clarity_arr = explode(',',$spriceRow['clarity']);
        	        if($spriceRow['clarity']=="全部" || in_array($v2['clarity'],$clarity_arr)){
        	            $spriceRow['calc_tip'] .="主石净度校对:销售政策【{$spriceRow['clarity']}】 与 石头【{$v2['clarity']}】 正确！<br/>";
        	            $spriceRow['clarity'] = $v2['clarity'];//主石净度
        	        }else{
        	            $spriceRow['error_tip']="销售政策主石净度【{$spriceRow['clarity']}】与石头配置净度【{$v2['clarity']}】不符";
        	            $datalist[$key]['errors'][]= array($spriceRow,$v2);
        	            //unset($datalist[$key]['sprice'][$k]);
        	            continue;
        	        }
        	        //销售政策证书类型与石头配置证书类型 校对
        	        $cert_arr = explode(',',$spriceRow['cert']);
        	        if($spriceRow['cert']=="" || $spriceRow['cert']=="全部" || $spriceRow['cert']=="全部类型" || in_array($v2['cert'],$cert_arr)){
        	            $spriceRow['calc_tip'] .="证书类型校对:销售政策【{$spriceRow['cert']}】 与 石头【{$v2['cert']}】 正确！<br/>";
        	            $spriceRow['cert'] = $v2['cert'];//证书类型
        	        }else{
        	            $spriceRow['stone_id'] = $v2['id'];//证书类型
        	            $spriceRow['error_tip']="销售政策证书类型【{$spriceRow['cert']}】与石头配置证书类型【{$v2['cert']}】不符";
        	            $datalist[$key]['errors'][]=$spriceRow;
        	            //unset($datalist[$key]['sprice'][$k]);
        	            continue;
        	        }
        	        
        	        $spriceRow['shape_id'] = $v2['shape'];
        	        $spriceRow['shape'] = isset($shapeNameArr[$v2['shape']])?$shapeNameArr[$v2['shape']]:$v2['shape'];//主石形状
        	        	
        	        if($v2['shape']==''){
        	            $spriceRow['sale_price'] = "";
        	            $spriceRow['error_tip'] = "匹配到钻石形状【{$spriceRow['shape']}】与款号{$goods_sn}的主石形状【{$style_shape}】不一致";
        	            $datalist[$key]['errors'][]=$spriceRow;
        	            //unset($datalist[$key]['sprice'][$k]);
        	            continue;
        	        }else{
        	            $spriceRow['calc_tip'] .="主石形状校对:款号形状与 石头形状 正确！<br/>";
        	        }
        	        	
        	        if($spriceRow['is_yikoujia']==0){
        	            $sale_price = ($stone_price*$xiangkou+$chengben)*$jiajia+$sta_value;
        	            $sale_price = sprintf("%.0f", $sale_price);
        	            $spriceRow['sale_price'] = $sale_price;
        	            $spriceRow['stone_price'] = $stone_price;
        	            $spriceRow['calc_tip'] .= "销售价=(石头价格*镶口+戒托成本)*加价率+固定值=({$stone_price}*{$xiangkou}+{$chengben})*{$jiajia}+{$sta_value}=<b style='color:red'>{$sale_price}</b>";
        	        }else{
        	            $spriceRow['stone_price']='';
        	            $spriceRow['calc_tip'] .= "销售价=一口价=<b style='color:red'>{$sale_price}</b>";
        	        }
        	        $spriceRow['clarity'] = $v2['clarity'];//主石净度
        	        $spriceRow['cert'] = $v2['cert'];//证书类型
        	        $spriceRow['stone_price'] = $stone_price;//石头价格
        	        $spriceRow['carat_min'] = $v2['carat_min'];//主石最小值
        	        $spriceRow['carat_max'] = $v2['carat_max'];//主石最大值
        	        $spriceRow['stone_id'] = $v2['id'];//主石ID
        	        $spriceRow['goods_key'] = md5($spriceRow['goods_id']."&".$spriceRow['id']."&".$spriceRow['stone_id']);//货品ID&销售政策ID&石头ID 的MD5
        	        if(!empty($goods_key) && $goods_key<>$spriceRow['goods_key']){
        	            $spriceRow['goods_key2'] = $goods_key;
        	            //unset($datalist[$key]['sprice'][$k]);
        	            continue;
        	        }
        	        $spriceList[] = $spriceRow;
        	    }  //end foreach $stonePriceList 
        	         	    
    	    }//end foreach $data['sprice']
    	    //file_put_contents('1.txt',var_export($datalist,true)) ;
    	    	
    	    /* echo "<pre>";
    	    print_r($datalist);
    	    echo "<pre/>"; */
    	    if(!empty($spriceList)){    	        
    	       $datalist[$key]['sprice'] = $spriceList;
    	       $datalist[$key]['sale_price'] = $datalist[$key]['sprice'][0]['sale_price'];
    	       $datalist[$key]['policy_name'] = $datalist[$key]['sprice'][0]['policy_name'];
    	    }else{
    	       $datalist[$key]['sale_price'] = '';
    	       unset($datalist[$key]);
    	       /*
     	       $datalist[$key]['sprice'] = $spriceList;
    	       $datalist[$key]['sale_price'] ='';
    	       $datalist[$key]['policy_name'] = ''; 
    	       */  	       
    	    }

    	} 
    	
        //if($_SESSION['userName']=='admin'){
        //    echo '<pre>';
        //    print_r($datalist);
        //} 
    	//is_more_line=1 每个组合的成品定制都  拆分成行   	
    	if(!empty($where['is_more_line'])){
    	    $_datalist = array();
    	    foreach ($datalist as $key=>$vo){
	            foreach ($vo['sprice'] as $k=>$v){
	                $row = array_merge($vo,$v);
	                $_datalist[] = $row;
	            }
    	    }
    	    $datalist = $_datalist;
    	}
    	if($xiangkou>0 && empty($datalist)){
    	    $result['error'] = "没有符合条件的成品定制商品。提示：石头价格匹配成功，但没有对应的销售政策！";
    	    return $result;
    	}
    	//print_r($datalist);
        //超级管理员        
    	$result['data'] = $datalist;
	    return $result;
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
			$is_chengpin = $ginfo['is_chengpin'];
			$xiangkou = $ginfo['xiangkou'];
			
			//如果是现货 首先算出保险费
			if($ginfo['isXianhuo'] == 1){
				//如果是经销商（只要大于0就ok）
				if(SYS_SCOPE == 'zhanting' && $ginfo['jingxiaoshangchengbenjia']>0 )
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
					$baoxianfei = $this->getbaoxianfei($product,$baoxian_xiankou);
				}
			}
			
			$chenben = $ginfo['mingyichengben'] + $baoxianfei;
			
			//获取图片
			if ($ginfo['goods_sn'] == '仅售现货') {
				$ginfo['thumb_img'] = '';
			}else{
				$sql ="SELECT `thumb_img` FROM `app_style_gallery` WHERE `style_sn`='{$ginfo['goods_sn']}' AND `image_place` = 1";
				$thumb_img = $this->db()->getOne($sql);		        
				//$data[$k]['thumb_img'] = $thumb_img;
				$ginfo['thumb_img'] = $thumb_img;
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
				$ginfo['yanse'] = $this->getyanseid($caizhi,$yanse,$ginfo['caizhi']);
				$ginfo['caizhi'] = $this->getcaizhiid($caizhi,$ginfo['caizhi']);
				$ginfo['product_type'] = $this->getproductid($ginfo['product_type']);
				$ginfo['category'] = $this->getcatid($ginfo['category']);
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
		select a.policy_id,a.policy_name,a.jiajia,a.sta_value,a.range_begin,a.range_end,a.cert,a.color,a.clarity,a.tuo_type 
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
			if(isset($ginfo['tuo_type']) && $ginfo['tuo_type']==1){
			    $sql .=" and a.tuo_type in(0,1)";
			}else{
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
			    $sql .=" and a.tuo_type in(0,1) ";
			}else{
			    $sql .=" and a.tuo_type in(0,2,3) ";
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
    		$xilie = $this->getxilie($ginfo['goods_sn']);
    		$sql.=" and ( a.xilie='全部系列' or a.xilie regexp '{$xilie}' ) ";
		}
		//echo $sql;die();
		
		
		//告诉我们只取活动的 否则的话获取全部的(默认的和非默认的)
		if($ginfo['isXianhuo']==1 || $ginfo['tuo_type'] !=1 ){
		    //非成品定制，默认销售政策查询
    		if($isactive>0)
    		{
    			$sql .=" and a.is_default != 1";
    		}else{
                $sql .=" and a.is_default = 1";
    		}
		}
		if($policyid>0)
		{
			$sql .= " and a.policy_id = $policyid ";
		}
		$sql .=" and b.channel= $channelid group by a.policy_id order by a.is_default asc,a.policy_id desc ";

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
	
	//一口价商品列表 New ，兼容成品定制
	public function getYikoujiaNew($ginfo,$caizhi,$policyid=0,$channelid=0)
	{
	    $goods_data = $ginfo;
		$sql = " select a.policy_id,a.price,b.policy_name,b.jiajia,b.sta_value,a.cert,a.color,a.clarity,a.tuo_type   
			from app_yikoujia_goods as a 
			inner join base_salepolicy_info as b on a.policy_id=b.policy_id 
			inner join app_salepolicy_channel as d on a.policy_id=d.policy_id 
			where b.is_kuanprice=1 and b.is_delete=0 and b.bsi_status=3 and a.is_delete=0 and ";
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
		if(isset($ginfo['goods_sn']) && $ginfo['goods_sn'] !='')
		{
		    //要排除掉 指定了货号的一口价
		    $sql .=" a.goods_sn='{$ginfo['goods_sn']}' and ";
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
		if(isset($ginfo['color']) && $ginfo['color'] !=''){
		    $sql .=" a.color in ('全部','{$ginfo['color']}')  and ";
		}
		if(isset($ginfo['clarity']) && $ginfo['clarity'] !=''){
		    $sql .=" a.clarity in('全部','{$ginfo['clarity']}')  and ";
		}
		if(isset($ginfo['cert']) && $ginfo['cert'] !=''){
		    $sql .=" a.cert in('{$ginfo['cert']}','全部') and ";
		}
		//金托类型
		$sql .=" a.tuo_type in (0,1) and b.tuo_type in (0,1) and ";

		if(isset($ginfo['goods_id']) && $ginfo['goods_id'] !='')
		{
			//$sql = $sql." a.goods_id='{$ginfo['goods_id']}' and ";			
		}

		$sql .= " 1 ";
		//echo $sql;exit;
		$data = $this->db()->getAll($sql);
		if(!empty($data)){
			$goods_data['yikoujia'] = $data;
		}else{
		    $goods_data['yikoujia'] = array();

		}
		return $goods_data;
	}
	
	

	//判断是否为空,在调用前判断
	public function getyikoujia($ginfo,$caizhi,$policyid=0,$channelid=0)
	{
	    $goods_data = $ginfo;
	    //成品定制一口价
	    if(isset($ginfo['tuo_type']) && $ginfo['tuo_type']==1 && isset($ginfo['isXianhuo']) && $ginfo['isXianhuo']==0){
	        return $this->getYikoujiaNew($ginfo, $caizhi,$policyid,$channelid);
	    } 
	    $sql = " select a.policy_id,a.price,b.policy_name,b.jiajia,b.sta_value,a.cert,a.color,a.clarity,a.tuo_type
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
	        //$sql .= " a.goods_id='{$ginfo['goods_id']}' and ";
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
	        $sql .=" a.goods_sn='{$ginfo['goods_sn']}' and a.goods_id ='' and ";
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
		if(isset($ginfo['tuo_type'])){
	    		if($ginfo['tuo_type']==1){
	    		    $sql .=" a.tuo_type in(0,1) and ";
	    		}else{
	    		    $sql .=" a.tuo_type in(0,2,3) and ";
	    		}
		}	
	    $sql .= " 1 ";

	    $data = $this->db()->getAll($sql);
	    if(!empty($data))
	    {
	        $goods_data['yikoujia'] = $data;
	    }else{
	        $goods_data['yikoujia'] = array();
	    }
	    return $goods_data;
	}
	
	//改造可销售商品的分页查询
	
	function pageXianhuoList($where,$page,$pageSize=10,$caizhi,$yanse,$useCache=true,$dia_supported = 0)
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
				$parent['color']=$val['color'];拷贝的上面的,忘记注释掉了*/
		$sql =" select a.id,a.goods_id,a.goods_sn,a.goods_name,a.zuanshidaxiao,
			a.jietuoxiangkou as xiangkou,a.shoucun as finger,a.caizhi,a.yanse,1 as isXianhuo,
			a.product_type1 as product_type,
			a.zhengshuleibie,
			a.cat_type1 as category,
			a.mingyichengben,a.update_time,a.warehouse,a.put_in_type,a.jingxiaoshangchengbenjia,
			a.zhengshuhao,a.jinzhong,a.zuanshidaxiao as cart,a.qiegong as cut,a.jingdu as clarity,a.yanse as color,a.tuo_type,'0' as is_quick_diy,'1' as is_chengpin,mairugongfei 
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
						a.product_type1,a.cat_type1,a.warehouse,a.put_in_type,a.jingxiaoshangchengbenjia 
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
						$bound_order_sn = $this->db()->getOne("select order_sn from app_order.base_order_info i inner join app_order.app_order_details d on d.order_id = i.id where d.id = {$ginfo['order_goods_id']}");
						$result['content'] = '货品已经绑定订单了, 订单号为 '.$bound_order_sn;
					} else if (!$dia_supported) {
						if($ginfo['is_default'] != '1'){
							$result['error'] = 1;
							$result['content'] = '货品所在的仓库为'.$ginfo['warehouse'].' 该仓库为非默认上架仓库';
						}elseif($ginfo['product_type1'] == '彩钻' && $ginfo['cat_type1'] =='裸石'){
							$result['error'] = 1;
							$result['content'] = '货品的产品线是彩钻, 不走销售政策, 请选择彩钻下单, 或找产品部核对商品信息';
						}elseif($ginfo['product_type1'] == '钻石' && $ginfo['cat_type1'] =='裸石'){
							$result['error'] = 1;
							$result['content'] = '货品的产品线是钻石, 款式分类为裸石, 这类货品不走销售政策, 请找产品部核对该货品信息';
						}elseif($ginfo['product_type1'] == '钻石' && $ginfo['cat_type1'] =='彩钻'){
							$result['error'] = 1;
							$result['content'] = '货品的产品线是钻石, 款式分类为彩钻, 这类货品不走销售政策, 请找产品部核对该货品信息';
						}elseif($ginfo['product_type1'] == '彩钻' && $ginfo['cat_type1'] =='钻石'){
							$result['error'] = 1;
							$result['content'] = '货品的产品线是彩钻, 款式分类为钻石, 这类货品不走销售政策, 请找产品部核对该货品信息';
						}
					}
					if( SYS_SCOPE == 'zhanting' && $ginfo['put_in_type'] != 5){
						//经销商非自采的 增加经销商名义成本价的过滤
						if( $ginfo['jingxiaoshangchengbenjia'] < 1){
							//$result['error'] = 1;
							//$result['content'] = '非自采的商品, 成本价为0, 请找产品部核对该货品信息';
						}
					}
				}
			}
			if($result['error']>0)
			{
				return $result;
			}
			$sql .= $sql_where;
			if (!$dia_supported) {
				$sql .= " and (	( a.product_type1 !='钻石' and  a.cat_type1 not in('裸石','彩钻')) or ( a.product_type1 !='彩钻' and a.cat_type1 not in('钻石','彩钻','裸石')))";
			}			
		}
        //增加展厅名以成本价过滤条件
	    if(SYS_SCOPE == 'zhanting')
	    {
	        $sql .= " and a.jingxiaoshangchengbenjia > 0 ";
	    }		

		//echo $sql;die();
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
	/**
	 * 根据虚拟货号验证是否是成品定制
	 * @param unknown $goods_sn
	 * 如果虚拟货号对应的[产品线为镶嵌类,镶口为0 ]或[产品线为非镶嵌类金托类型] 的是成品，产线为镶嵌类，镶口不为0，金托类型可选择成品或空托
	 */
	public function checkIsChengpindingzhi($goods_sn){
	    $sql = "select count(*) from list_style_goods sg inner join front.app_product_type pt on sg.product_type_id = pt.product_type_id	
where ((pt.parent_id=3 and sg.xiangkou=0) or pt.parent_id<>3) and goods_sn='{$goods_sn}'";
	    return $this->db()->getOne($sql);
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
			$sql = 'SELECT `id`,`min`,`max`,`price`,`status` FROM `app_style_baoxianfee` WHERE 1';
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


    //根据款号获取主石行走4
    public function getStyleStone($style_sn)
    {
        # code...
        $sql = "select ss.stone_cat,ss.stone_attr from base_style_info si inner join rel_style_stone ss on ss.style_id = si.style_id where si.style_sn ='".$style_sn."' and ss.stone_position = 1";
        return $this->db()->getRow($sql);
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

    //获取当前用户公司compan_type资料
	function userByCompany(){
	    $sql="select company_type from cuteframe.company where id={$_SESSION['companyId']}";
	    return $this->db()->getOne($sql);
    }

    //获取当前用户公司compan_type资料
	function userCompanyTypeByChannelId($channel_id){
	    $sql="select company_type from cuteframe.company c,cuteframe.sales_channels s where c.id=s.company_id and s.id='{$channel_id}'";
	    //echo $sql;
	    return $this->db()->getOne($sql);
    } 
      
    /**
     * 新版成品定制列表
     * @param unknown $where
     * @return multitype:boolean multitype: string |multitype:boolean multitype: string Ambigous <string, unknown, multitype:, multitype:multitype: >
     */
    public function getCpdzList($where){
	    $result = array('error'=>false,'data'=>array());
	    $goods_id = isset($where['goods_id'])?$where['goods_id']:'';
	    $xiangkou = isset($where['xiangkou'])?$where['xiangkou']:'';
	    $clarity = isset($where['clarity'])?$where['clarity']:'';
	    $color = isset($where['color'])?$where['color']:"";
	    $shape = isset($where['shape'])?$where['shape']:'';
	    $channel_id = isset($where['channel'])?$where['channel']:'';
	    $policy_id = isset($where['policy_id'])?$where['policy_id']:0;
	    $carat = isset($where['carat'])?$where['carat']:0;//主石大小
	    $carat = !empty($xiangkou)?$xiangkou:$carat;
	    
	    $cert = isset($where['cert'])?$where['cert']:'';
	    $tuo_type = isset($where['tuo_type'])?$where['tuo_type']:'';
	    $goods_key = isset($where['goods_key'])?$where['goods_key']:'';
	    $attrModel = new GoodsAttributeModel(17);
	    $shapeNameArr = $attrModel->getShapeList();
	    $shapeIdArr  = array_flip($shapeNameArr);//成品定制  形状名称=>形状ID 映射关系 列表
	    if(empty($where['ginfos'])){
	        $sql =" select sg.goods_id as id,sg.goods_sn as goods_id,sg.style_sn as goods_sn,sg.style_name as goods_name,
	        sg.xiangkou,sg.shoucun as finger,sg.caizhi,sg.yanse,0 as isXianhuo,
	        sg.product_type_id as product_type,
	        sg.cat_type_id as category,sg.zhushizhong as stone,case WHEN sg.xiangkou<>0 and pt.parent_id = 3 then 3 else 1 end AS tuo_type,
	        sg.dingzhichengben as mingyichengben,sg.last_update as update_time,'非仓库货品' as warehouse,IFNULL((pt.parent_id=3 and sg.xiangkou=0) or pt.parent_id<>3,0) as is_chengpin
	        from list_style_goods sg inner join front.app_product_type pt on sg.product_type_id = pt.product_type_id
	        where sg.is_ok=1 and sg.product_type_id>0 and sg.goods_sn='{$goods_id}'";
	        $ginfos = $this->db()->getAll($sql);	        
	    }else{
	        $ginfos = $where['ginfos'];
	    }
	    if(empty($ginfos)){
	        $result['error'] = "虚拟货号不存在";
	        return $result;
	    }else if(count($ginfos)==1 && $xiangkou==""){
	        $xiangkou = $ginfos[0]['xiangkou'];
	    }
         
    	foreach ($ginfos as $k=>$ginfo){
        	$ginfo['clarity'] = $clarity;
        	$ginfo['color']  = $color;
        	$ginfo['shape'] = $shape;
        	$ginfo['zhengshuleibie'] = $cert;
        	$ginfo['tuo_type'] = 1;
        	$ginfos[$k] = $ginfo;
    	}
    	$datalist = $this->getpolicygoods($ginfos,$channel_id,$policy_id);
    	$sprice_tongji=0;//不存在销售政策的统计有多少个
    	foreach ($datalist as $key=>$data){
            if($data['sprice']){
            	foreach ($data['sprice'] as $price_key => $pricearr) {
            		if($pricearr['tuo_type']=='2' || $pricearr['tuo_type']=='3' ){
            			unset($datalist[$key]['sprice'][$price_key]);
            		}
            	}
            }                		
    	    if(empty($data['sprice'])){
    	        unset($datalist[$key]);
    	    }
    	}
    	if(empty($datalist)){
    	    $result['error'] = "没有符合条件的成品定制商品。提示：搜索商品找不到销售政策！";
    	    return $result;
    	}
    	
    	//print_r($datalist);
    	//此 形状枚键值举仅针对 款式石头有效，不可公用.
    	$shape_arr = array(1=>"垫形",2=>"公主方形",3=>"祖母绿形",4=>"心形",5=>"蛋形",6=>"椭圆形",7=>"橄榄形",8=>"三角形",9=>"水滴形",10=>"长方形",11=>"圆形",12=>"梨形",13=>"马眼形");
    	$goodsYikoujiaList = array();
    	foreach ($datalist as $key=>$data){
    	   $spriceList = array();
    	   $spriceListError = array();    	   
    	   foreach ($data['sprice'] as $k=>$v){
    	        $goods_id = $v['goods_id'];
    	        $policy_id = $v['id'];
    	        $sale_price = $v['sale_price'];    	       
            	$chengben = $v['chengben'];
            	$jiajia = isset($v['jiajia'])?$v['jiajia']:1;//一口价，无jiajia
            	$sta_value = isset($v['sta_value'])?$v['sta_value']:0;//一口价，无sta_value
        	    $xiangkou = $data['xiangkou'];
        	    $goods_sn = $v['goods_sn'];
        	    $v['policy_id'] = $policy_id;
        	    $v['calc_tip'] = "货号：{$v['goods_id']}<br/>";
        	    $v['calc_tip'] .= "销售政策：{$v['policy_name']}<br/>";
        	    if($v['is_chengpin']==1){
        	        $v['clarity'] = "";//主石净度
        	        $v['cert'] = "";//证书类型
        	        $v['stone_price'] = "";//石头价格
        	        $v['carat_min'] = "";//主石最小值
        	        $v['carat_max'] = "";//主石最大值
        	        $v['stone_id'] = 0;//主石ID
        	        $v['calc_tip'] .= "成品销售价(无需配钻)=空托成本价*加价率+固定值=({$chengben}*{$jiajia}+{$sta_value}=<b style='color:red'>{$sale_price}</b>";
        	        $v['goods_key'] = md5($v['goods_id']."&".$v['id']."&0");//货品ID&销售政策ID&石头ID 的MD5
        	        if(!empty($goods_key) && $goods_key<>$v['goods_key']){
        	            $v['goods_key2'] = $goods_key;
        	            unset($datalist[$key]['sprice'][$k]);
        	            continue;
        	        }
        	        $spriceList[] = $v;
        	        continue;//重要
        	    }       	     
        	    //接下来都是需要空托+配石组合成品 
        	    /*
        	     * 形状校验
        	     * */
        	    $sql = "select a.stone_cat,a.stone_attr from front.rel_style_stone a inner join front.base_style_info b on a.style_id=b.style_id where b.style_sn='{$goods_sn}' and a.stone_cat in(1,2) and stone_position=1";
        	    $stoneInfoList = $this->db()->getAll($sql);
        	    $stonePriceList = array();
        	    $shapeList = array();
        	    $stoneNum = 0;
        	    foreach ($stoneInfoList as  $stoneInfo){
        	        $stoneAttr = @unserialize($stoneInfo['stone_attr']);
        	        if($stoneInfo['stone_cat']==1){
        	            $style_shape = "圆形";
        	        }else{        	            
        	            $shape_id = isset($stoneAttr['shape_zhushi'])?$stoneAttr['shape_zhushi']:'';
    	                $style_shape = isset($shape_arr[$shape_id])?$shape_arr[$shape_id]:$shape_id;
           	        }
        	        $zhushi_num = isset($stoneAttr['number'])?$stoneAttr['number']:1;
        	        $stoneNum += $zhushi_num;
        	        $shape = isset($shapeIdArr[$style_shape])?$shapeIdArr[$style_shape]:"";
        	        //echo $shape.'-'.$style_shape.'=';
        	        if($shape<>''){
        	            //获取货号的款号中的形状后，需要重新获取石头列表
        	            //避免前面查询的石头初步满足条件，但形状不符被过滤，导致没有符合的石头，但此时有满足镶口+1的石头的（很特殊的情况）
            	        $_carat = $carat * 100;
            	        $sql_str = "";
            	        $sql = "select * from front.diamond_fourc_info where `status`=1 and carat_min<={$_carat} and carat_max>={$_carat}";
            	        if($shape != ""){
            	            $sql_str .=" AND shape={$shape}";
            	        }
            	        if($color != ""){
            	            $sql_str .=" AND color='{$color}'";
            	        }
            	        if($clarity !=""){
            	            $sql_str .=" AND clarity='{$clarity}'";
            	        }
            	        if($cert !=""){
            	            $sql_str .=" AND cert='{$cert}'";
            	        }         	        
            	        $_stonePriceList = $this->db()->getAll($sql.$sql_str);
            	        /*if(empty($_stonePriceList)){
            	            $_xiangkou = $_xiangkou+1;
            	            $sql = "select * from front.diamond_fourc_info where `status`=1 and carat_min<={$_xiangkou} and carat_max>={$_xiangkou}";
            	            $_stonePriceList = $this->db()->getAll($sql.$sql_str);
            	        }*/
            	        $v['calc_tip'] .="形状【{$style_shape}】{$shape},颜色【{$color}】,净度【{$clarity}】,证书类型【{$cert}】匹配石头价格成功！<br/>";
            	        if(!empty($_stonePriceList) && !in_array($shape,$shapeList)){
            	            foreach ($_stonePriceList as $_stone){
            	                if(!isset($stonePriceList[$_stone['id']])){
            	                    $_stone['shapeName'] = $style_shape;
            	                    $stonePriceList[$_stone['id']] = $_stone;
            	                }
            	            }
            	            $shapeList[] = $shape;
            	        }
        	        }else{
        	            $shapeList[] = '';
        	            $v['error_tip']="货号{$goods_id}所属的款号{$goods_sn}没有主石形状";
        	            $datalist[$key]['errors'][]=$v;	  
        	            unset($datalist[$key]);
        	            continue;
        	        }            	        

        	    }
        	    //$stoneNum = 2;
        	    if(count(array_unique($shapeList))>1) {
        	        $result['error'] = "{$goods_sn}款号有多颗主石，且主石形状不一致，不支持成品定制！";
        	        return $result;
        	    }   
    	        if($xiangkou>0 && empty($stonePriceList)){
    	            /*$v['sale_price'] ='';
    	            $v['stone_price']='';        	             
    	            $v['calc_tip'] = "没有符合条件的成品定制商品。提示：成品定制没有符合条件的钻石！匹配条件:形状【{$style_shape}】{$shape},颜色【{$color}】,净度【{$clarity}】,证书类型【{$cert}】";;
    	            $datalist[$key]['errors'][] = $v; //调试专用
    	            unset($datalist[$key]['sprice'][$k]);
    	            continue;*/
    	            $result['error'] = "没有符合条件的成品定制商品。提示：没有匹配到石头价格信息！";
    	            return $result;
    	        }  
        	    foreach ($stonePriceList as $k2=>$v2){
        	        $spriceRow = $v;// 用 $spriceRow 替代$v 非常重要
        	        $spriceRow['key2'] = $k2;
        	        $stone_price = $v2['price']*$stoneNum;
        	        //销售政策 颜色与石头配置颜色 校对
        	        $color_arr = explode(',',$spriceRow['color']);
        	        if($spriceRow['color']=="全部" || in_array($v2['color'],$color_arr)){
        	            $spriceRow['calc_tip'] .="主石颜色校对:销售政策【{$spriceRow['color']}】 与 石头【{$v2['color']}】 正确！<br/>";
        	            $spriceRow['color'] = $v2['color'];//主石颜色
        	        }else{
        	            $spriceRow['error_tip']="销售政策主石颜色【{$spriceRow['color']}】与石头配置颜色【{$v2['color']}】不符";
        	            $datalist[$key]['errors'][]=$spriceRow;
        	            //unset($datalist[$key]['sprice'][$k]);
        	            continue;
        	        }
        	        //销售政策 主石净度与石头配置主石净度 校对
        	        $clarity_arr = explode(',',$spriceRow['clarity']);
        	        if($spriceRow['clarity']=="全部" || in_array($v2['clarity'],$clarity_arr)){
        	            $spriceRow['calc_tip'] .="主石净度校对:销售政策【{$spriceRow['clarity']}】 与 石头【{$v2['clarity']}】 正确！<br/>";
        	            $spriceRow['clarity'] = $v2['clarity'];//主石净度
        	        }else{
        	            $spriceRow['error_tip']="销售政策主石净度【{$spriceRow['clarity']}】与石头配置净度【{$v2['clarity']}】不符";
        	            $datalist[$key]['errors'][]= array($spriceRow,$v2);
        	            //unset($datalist[$key]['sprice'][$k]);
        	            continue;
        	        }
        	        //销售政策证书类型与石头配置证书类型 校对
        	        $cert_arr = explode(',',$spriceRow['cert']);
        	        if($spriceRow['cert']=="" || $spriceRow['cert']=="全部" || $spriceRow['cert']=="全部类型" || in_array($v2['cert'],$cert_arr)){
        	            $spriceRow['calc_tip'] .="证书类型校对:销售政策【{$spriceRow['cert']}】 与 石头【{$v2['cert']}】 正确！<br/>";
        	            $spriceRow['cert'] = $v2['cert'];//证书类型
        	        }else{
        	            $spriceRow['stone_id'] = $v2['id'];//证书类型
        	            $spriceRow['error_tip']="销售政策证书类型【{$spriceRow['cert']}】与石头配置证书类型【{$v2['cert']}】不符";
        	            $datalist[$key]['errors'][]=$spriceRow;
        	            //unset($datalist[$key]['sprice'][$k]);
        	            continue;
        	        }
        	        
        	        $spriceRow['shape_id'] = $v2['shape'];
        	        $spriceRow['shape'] = isset($shapeNameArr[$v2['shape']])?$shapeNameArr[$v2['shape']]:$v2['shape'];//主石形状
        	        	
        	        if($v2['shape']==''){
        	            $spriceRow['sale_price'] = "";
        	            $spriceRow['error_tip'] = "匹配到钻石形状【{$spriceRow['shape']}】与款号{$goods_sn}的主石形状【{$style_shape}】不一致";
        	            $datalist[$key]['errors'][]=$spriceRow;
        	            //unset($datalist[$key]['sprice'][$k]);
        	            continue;
        	        }else{
        	            $spriceRow['calc_tip'] .="主石形状校对:款号形状与 石头形状 正确！<br/>";
        	        }
        	        //货品ID&销售政策ID&石头ID 的MD5
        	        $spriceRow['clarity'] = $v2['clarity'];//主石净度
        	        $spriceRow['cert'] = $v2['cert'];//证书类型
        	        $spriceRow['stone_price'] = $stone_price;//石头价格
        	        $spriceRow['carat_min'] = $v2['carat_min'];//主石最小值
        	        $spriceRow['carat_max'] = $v2['carat_max'];//主石最大值
        	        $spriceRow['stone_id'] = $v2['id'];//主石ID

        	        $spriceRow['goods_key'] = md5($spriceRow['goods_id']."&".$spriceRow['id']."&".$spriceRow['stone_id']);
        	        if($spriceRow['is_yikoujia']==0){
        	            $sale_price = ($stone_price*$xiangkou+$chengben)*$jiajia+$sta_value;
        	            $sale_price = sprintf("%.0f", $sale_price);
        	            $spriceRow['sale_price'] = $sale_price;
        	            $spriceRow['stone_price'] = $stone_price;
        	            $spriceRow['calc_tip'] .= "销售价=(石头价格*镶口+戒托成本)*加价率+固定值=({$stone_price}*{$xiangkou}+{$chengben})*{$jiajia}+{$sta_value}=<b style='color:red'>{$sale_price}</b>";
        	        }else{ 
        	            $goods_stone_key = $spriceRow['goods_id']."&".$spriceRow['stone_id'];      	            
        	            if(!in_array($goods_stone_key,$goodsYikoujiaList)){
        	                $goodsYikoujiaList[] = $goods_stone_key;
        	            }        	            
        	            $spriceRow['stone_price']='';
        	            $spriceRow['calc_tip'] .= "销售价=一口价=<b style='color:red'>{$sale_price}</b>";
        	        }        	               	        
        	        if(!empty($goods_key) && $goods_key<>$spriceRow['goods_key']){
        	            $spriceRow['goods_key2'] = $goods_key;
        	            //unset($datalist[$key]['sprice'][$k]);
        	            continue;
        	        }
        	        $spriceList[] = $spriceRow;
        	    }  //end foreach $stonePriceList 
        	         	    
    	    }//end foreach $data['sprice']
    	    //file_put_contents('1.txt',var_export($datalist,true)) ;
    	    	
    	    /* echo "<pre>";
    	    print_r($datalist);
    	    echo "<pre/>"; */
    	    if(!empty($spriceList)){    	        
    	       $datalist[$key]['sprice'] = $spriceList;
    	       $datalist[$key]['sale_price'] = $datalist[$key]['sprice'][0]['sale_price'];
    	       $datalist[$key]['policy_name'] = $datalist[$key]['sprice'][0]['policy_name'];
    	    }else{
    	       $datalist[$key]['sale_price'] = '';
    	       unset($datalist[$key]);
    	       /*
     	       $datalist[$key]['sprice'] = $spriceList;
    	       $datalist[$key]['sale_price'] ='';
    	       $datalist[$key]['policy_name'] = ''; 
    	       */  	       
    	    }

    	}
    	//一口价处理
    	foreach ($datalist as $key=>$data){
    	    foreach ($data['sprice'] as $k=>$v){
    	        //如果存在一口价，删除掉非一口价
    	        $goods_stone_key = $v['goods_id']."&".$v['stone_id'];
    	        if(in_array($goods_stone_key,$goodsYikoujiaList) && $v['is_yikoujia']==0){
    	            unset($data['sprice'][$k]);
    	        }
    	    }
    	    $datalist[$key] = $data;
    	}
        //if($_SESSION['userName']=='admin'){
        //    echo '<pre>';
        //    print_r($datalist);
        //} 
    	//is_more_line=1 每个组合的成品定制都  拆分成行   	
    	if(!empty($where['is_more_line'])){
    	    $_datalist = array();
    	    foreach ($datalist as $key=>$vo){
	            foreach ($vo['sprice'] as $k=>$v){
	                $row = array_merge($vo,$v);
	                $_datalist[] = $row;
	            }
    	    }
    	    $datalist = $_datalist;
    	}
    	if($xiangkou>0 && empty($datalist)){
    	    $result['error'] = "没有符合条件的成品定制商品。提示：石头价格匹配成功，但没有对应的销售政策！";
    	    return $result;
    	}
    	//print_r($datalist);
        //超级管理员        
    	$result['data'] = $datalist;
	    return $result;
     } 	

}
    


?>