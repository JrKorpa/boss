<?php
/**
 * This contains the Retrieval API .
 *
 */
define("STYLE_GOODS_JIAJIALV",1.5);
class api
{
    private  $db = null;
    private $error=0;
    private  $error_msg = '';
    private  $return_msg = '';
    private  $return_sql = '';
    private  $filter = array();
    public function __construct($_filter)
    {
        global $config;
        $this->db= new KELA_API_DB($config);
		$this->filter = $_filter;
    }

    /**
     * 根据款号获取款式列表
     * @param $style_sn
     * @return json
     */
	public function GetStyleList()
	{
		$s_time = microtime();
		//$this -> filter["page"] = 3;  //当前页

		$page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
		$page_size = intval($this -> filter["page_size"]) > 0 ? intval($this -> filter["page_size"]) : 15;

        $where = " WHERE 1";
        $style_sn=trim($this->filter['style_sn']);
        if(!empty($style_sn)){
            $where.= " AND `style_sn` ='".$style_sn."' ";
        }

		$sql   = "SELECT COUNT(*) FROM ` base_style_info` ".$where;

		$record_count   =  $this -> db ->getOne($sql);
		$page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

		$sql = "select * from `base_style_info` ".$where." ORDER BY `style_id` desc LIMIT " . ($page - 1) * $page_size . ",$page_size";
		$res = $this -> db -> getAll($sql);
		$content = array("page" => $page, "page_size" => $page_size, "record_count" => $record_count, "data" => $res, "sql" => $sql);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
	//	var_dump($content);
		if(!$res)
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $content;
			$this->display();
		}
	}

    /**
     * 根据查询条件获取款式列表     款式图片专用
     * @param $style_sn
     * @param product_type_id
     * @param cat_type_id
     * @param check_status
     * @param dismantle_status
     * @return json
     */
	public function GetStyleInfoList()
	{
		$s_time = microtime();
		//$this -> filter["page"] = 3;  //当前页
		$page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
		$page_size = intval($this -> filter["page_size"]) > 0 ? intval($this -> filter["page_size"]) : 15;

        $where = " WHERE 1";
        $style_sn=trim($this->filter['style_sn']);
        if(!empty($style_sn)){
            $where.= " AND `style_sn` ='".$style_sn."' ";
        }
        if(isset($this->filter['product_type_id'])&&$this->filter['product_type_id']!=''){
            $where.= " AND `product_type` =".$this->filter['product_type_id'];
        }
        if(isset($this->filter['cat_type_id'])&&$this->filter['cat_type_id']!=''){
            $where.= " AND `style_type` =".$this->filter['cat_type_id'];
        }
        if(isset($this->filter['check_status'])&&$this->filter['check_status']!=''){
            $where.= " AND `check_status` =".$this->filter['check_status'];
        }
        if(isset($this->filter['dismantle_status'])&&$this->filter['dismantle_status']!=''){
            $where.= " AND `dismantle_status` =".$this->filter['dismantle_status'];
        }


		$sql   = "SELECT COUNT(*) FROM `base_style_info` ".$where;

		$record_count   =  $this -> db ->getOne($sql);
		$page_count     = $record_count > 0 ? ceil($record_count / $page_size) : 1;

		$sql = "select * from `base_style_info` ".$where." ORDER BY `style_id` desc LIMIT " . ($page - 1) * $page_size . ",$page_size";
		$res = $this -> db -> getAll($sql);
        foreach ($res as &$val){
            $val['product_type'] = $this->db->getOne("select `product_type_name` from `app_product_type` where `product_type_id`={$val['product_type']}");
            $val['style_type'] = $this->db->getOne("select `cat_type_name` from `app_cat_type` where `cat_type_id`={$val['style_type']}");
        }
		$content = array("page" => $page, "page_count" => $page_count, "record_count" => $record_count, "data" => $res, "sql" => $sql);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
	//	var_dump($content);
		if(!$res)
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}
		else
		{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $content;
			$this->display();
		}
	}

	/**
    * 查询款式信息
    * @param $style_sn
    * @return json
    */
	public function GetStyleInfo()
	{
		$s_time = microtime();
        $where='';
        $style_sn=trim($this->filter['style_sn']);
		//$order_id = 49;
		if(!empty($style_sn))
		{
			$where .= " `style_sn` = '".$style_sn."'";
        }

        if(!empty($where)){
            //查询商品详情
            $sql = "select `style_id`, `style_sn`,`bang_type`, `style_name`, `product_type`, `style_type`, `create_time`, `modify_time`, `check_time`, `cancel_time`, `check_status`,`xilie`, `is_sales`, `is_made`, `dismantle_status`, `style_status`, `style_remark`,`goods_content` from `base_style_info` " .
                   "where ".$where." ;";
            $row = $this->db->getRow($sql);
        }else{
            $this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "款号不能为空";
			$this->display();
        }

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}

	/**
    * 查询款式信息
    * @param $style_sn
    * @return json
    */
	public function GetStyleInfoAll()
	{
		$s_time = microtime();
        $where='';
        $style_sn=trim($this->filter['style_sn']);
		//$order_id = 49;
		if(!empty($style_sn))
		{
			$where .= " `style_sn` = '".$style_sn."'";
        }

        if(!empty($where)){
            //查询商品详情
            $sql = "select * from `base_style_info` " .
                   "where ".$where." ;";
            $row = $this->db->getRow($sql);
        }else{
            $this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "款号不能为空";
			$this->display();
        }

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}
	/**
	 * 款式商品分页查询
	 * gaopeng
	 * 2018-03-22
	 */
    public function getStyleGoodsList(){
        
        $filter = $this->filter;
        
        $page = !empty($filter['page'])?$filter['page']:1;
        $pageSize = !empty($filter['pageSize'])?$filter['pageSize']:10;
        $useCache = !empty($filter['useCache'])?$filter['useCache']:false;
        $recordCount = !empty($filter['recordCount'])?$filter['recordCount']:false;
        $jiajialv = 1.5;
        $sql = "where s.is_made=1";
        //状态搜索
        if(isset($filter['is_ok']) && $filter['is_ok']!=''){
            $sql .= " AND g.is_ok =".$filter['is_ok'];
        }
        //货号搜索
        if(!empty($filter['goods_sn'])){
            if(is_array($filter['goods_sn'])){
                $sql .= " AND g.goods_sn in('".implode("','",$filter['goods_sn'])."')";
            }else{
                $sql .= " AND g.goods_sn ='".$filter['goods_sn']."'";
            }
        }
        //款号搜索
        if(!empty($filter['style_sn'])){
            if(is_array($filter['style_sn'])){
                $sql .= " AND g.style_sn in('".implode("','",$filter['style_sn'])."')";
            }else{
                $sql .= " AND g.style_sn ='".$filter['style_sn']."'";
            }
        }
        //名称模糊搜索
        if(!empty($filter['style_name'])){
            $sql .= " AND (g.style_sn='".$filter['style_name']."' or g.style_name like '%".$filter['style_name']."%')";
        }        
        //款式分类
        if(!empty($filter['cat_type_id'])){
            if(is_array($filter['cat_type_id'])){
                $sql .= " AND g.cat_type_id in(".implode(",",$filter['cat_type_id']).")";                
            }else{
                $sql .= " AND g.cat_type_id =".$filter['cat_type_id'];
                
            }
        }
        //产品线
        if(!empty($filter['product_type_id'])){
            if(is_array($filter['product_type_id'])){
                $sql .= " AND g.product_type_id in(".implode(",",$filter['product_type_id']).")";
            }else{
                $sql .= " AND g.product_type_id =".$filter['product_type_id'];
            }            
        }
        //材质
        $caizhiArr = array("18K"=>1,"PT950"=>2);
        if(!empty($filter['caizhi'])){
            if(is_array($filter['caizhi'])){
                foreach ($filter['caizhi'] as $k=>$v){
                    $filter['caizhi'][$k] = !empty($caizhiArr[$v])?$caizhiArr[$v]:$v; 
                }
                $sql .= " AND g.caizhi in(".implode(",",$filter['caizhi']).")";
            }else{
                $filter['caizhi'] = !empty($caizhiArr[$filter['caizhi']])?$caizhiArr[$filter['caizhi']]:$filter['caizhi'];
                $sql .= " AND g.caizhi =".$filter['caizhi'];
            }
            
        }
        //材质颜色
        $dd = new DictView(new DictModel(1));
        $yanseArr = $dd->getEnumArray("style.color");
        $yanseArr = array_column($yanseArr,'name','label');
        if(!empty($filter['yanse'])){                        
            if(is_array($filter['yanse'])){
                foreach ($filter['yanse'] as $k=>$v){
                    $filter['yanse'][$k] = !empty($caizhiArr[$v])?$caizhiArr[$v]:intval($v);
                }
                $sql .= " AND g.yanse in(".implode(",",$filter['yanse']).")";
            }else{
                $filter['yanse'] = !empty($yanseArr[$filter['yanse']])?$yanseArr[$filter['yanse']]:$filter['yanse'];
                $sql .= " AND g.yanse =".intval($filter['yanse']);
            }
        
        }
        //手寸
        if(!empty($filter['shoucun'])){            
            if(is_array($filter['shoucun'])){
                $sql .= " AND g.shoucun in(".implode(",",$filter['shoucun']).")";
            }else{
                $sql .= " AND g.shoucun =".$filter['shoucun'];
            }        
        }
        if(!empty($filter['shoucun_min'])){
            $sql .= " AND g.shoucun >=".$filter['shoucun_min'];
        }
        if(!empty($filter['shoucun_max'])){
            $sql .= " AND g.shoucun <=".$filter['shoucun_max'];
        }
        //镶口
        if(!empty($filter['xiangkou'])){
            if(is_array($filter['xiangkou'])){
                $sql .= " AND g.xiangkou in(".implode(",",$filter['xiangkou']).")";
            }else{
                $sql .= " AND g.xiangkou =".$filter['xiangkou'];
            }
        }
        //主石重搜索
        if(!empty($filter['carat_min'])){
            $sql .= " AND g.zhushizhong >=".$filter['carat_min'];
        }
        if(!empty($filter['carat_max'])){
            $sql .= " AND g.zhushizhong <=".$filter['carat_max'];
        }
        if(!empty($filter['shape'])){
            $tempSql = "select distinct style_id from front.rel_style_stone where stone_position=1 and stone_cat in(1,2)";
            if(is_array($filter['shape'])){
                $tempSql .= " AND shape in(".implode(",",$filter['shape']).")";
            }else{
                $tempSql .= " AND shape =".$filter['shape'];
            }
            $sql .=" AND s.style_id in({$tempSql})";
        }
        //系列搜索
        if(!empty($filter['xilie'])){
            if(is_array($filter['xilie'])){
                $str = "";
                foreach ($filter['xilie'] as $vo){
                    $str .= "s.xilie like '%,".$vo.",%' OR ";
                }
                $sql .= " AND (".trim($str," OR ").")";
            }else{
               $sql .= " AND s.xilie like '%,".$filter['xilie'].",%'";
            }
        }
        
        //商品价格搜索
        if(!empty($filter['price_min'])){
            $filter['price_min'] = sprintf("%.2f",$filter['price_min']/$jiajialv);
            $sql .= " AND g.dingzhichengben >= {$filter['price_min']}";
        }
        if(!empty($filter['price_max'])){
            $filter['price_max'] = sprintf("%.2f",$filter['price_max']/$jiajialv);
            $sql .= " AND g.dingzhichengben <= {$filter['price_max']}";
        }
        if(isset($filter['is_recommend'])){
            $sql .= " AND s.is_recommend = {$filter['is_recommend']}"; 
        }
        if(isset($filter['is_tsyd'])){
            $sql .= " AND s.style_sn in(select style_sn1 from rel_style_lovers UNION select style_sn2 from rel_style_lovers)";
        }
        $orderby_list = array(
            '1|1'=>"",
            '1|2'=>"",
            '2|1'=>"s.goods_click asc",
            '2|2'=>"s.goods_click desc",
            '3|1'=>"s.style_id asc",
            '3|2'=>"s.style_id desc",
            '4|1'=>"s.goods_salenum asc",
            '4|2'=>"s.goods_salenum desc",
            '5|1'=>"g.dingzhichengben asc",
            '5|2'=>"g.dingzhichengben desc",
        );
        
        //排序
        $order_by = "";
        if(!empty($filter['order_by']) && !empty($orderby_list[$filter['order_by']])){
            $order_by = $orderby_list[$filter['order_by']];
            $sql .=" order by {$order_by}";  
        }
        
        //$fields = "g.goods_id,'' as goods_image,g.product_type_id,g.cat_type_id,g.style_sn,g.style_name,g.goods_sn,g.shoucun,g.xiangkou,g.caizhi,g.yanse,g.zhushizhong,g.zhushi_num,g.fushizhong1,g.fushi_num1,g.fushizhong2,g.fushi_num2,g.fushizhong3,g.fushi_num3,g.fushi_chengbenjia_other,g.weight as jinzhong,g.jincha_shang,g.jincha_xia,g.dingzhichengben,g.dingzhichengben*{$jiajialv} as goods_price";
        $fields = "g.goods_id,'' as goods_image,g.product_type_id,g.cat_type_id,g.style_sn,g.style_name,g.goods_sn,g.shoucun,g.xiangkou,g.caizhi,g.yanse,g.dingzhichengben,g.dingzhichengben*{$jiajialv} as goods_price";
        if(!empty($filter['group_by'])){ 
             $sql = "select g.*,s.goods_salenum,s.goods_click from front.list_style_goods g inner join front.base_style_info s on g.style_sn=s.style_sn ".$sql;
             $sql = "select {$fields},g.goods_salenum,g.goods_click from ({$sql})g group by g.style_sn";
             if(preg_match("/g\./is",$order_by)){
                 $sql .=" order by {$order_by}";
             }
        }else{
             $sql = "select {$fields},s.goods_salenum,s.goods_click from front.list_style_goods g inner join front.base_style_info s on g.style_sn=s.style_sn ".$sql;
        }
        $data = $this->db->getPageList($sql, array(), $page, $pageSize, $useCache,$recordCount);
              
        if(is_array($data)){
            $style_images = array();
            $caizhiArr = array_flip($caizhiArr);
            $yanseArr  = array_flip($yanseArr);
            foreach ($data['data'] as $key=>$val){
                $style_sn = $val['style_sn'];
                if(!isset($images[$style_sn])){
                    $sql2 = "select middle_img from front.app_style_gallery where style_sn='{$style_sn}'";
                    $goods_image = $this->db->getOne($sql2);
                    $style_images[$style_sn] = $goods_image;
                }
                $val['goods_price'] = (int)$val['goods_price'];
                //$val['caizhi_id'] = $val['caizhi'];
                $val['caizhi'] = isset($caizhiArr[$val['caizhi']])?$caizhiArr[$val['caizhi']]:$val['caizhi'];
                //$val['yanse_id'] = $val['yanse'];
                $val['yanse'] = isset($yanseArr[$val['yanse']])?$yanseArr[$val['yanse']]:$val['yanse'];
                $val['goods_image'] = $style_images[$style_sn];
                $data['data'][$key] = $val;
            }           
            $this ->error = 0;
            $this ->error_msg = "查询成功";
        }else{
            $this ->error = 1;
            $this ->error_msg = "查询失败";
        }        
        $this ->return_sql = $sql;        
        $this ->return_msg = $data;
        $this->display();
       
    }
    /**
     * 获取款式商品销售价格
     */
    public function getStyleGoodsPrice(){
        $filter = $this->filter;
        if(empty($filter['style_sn'])){
            $this ->error = 1;
            $this ->error_msg = "参数错误，style_sn参数不能为空";
            $this->display();
        }else if(!isset($filter['shoucun'])){
            $this ->error = 1;
            $this ->error_msg = "参数错误，shoucun参数不能为空";
            $this->display();
        }else if(!isset($filter['xiangkou'])){
            $this ->error = 1;
            $this ->error_msg = "参数错误，xiangkou参数不能为空";
            $this->display();
        }else if(empty($filter['caizhi'])){
            $this ->error = 1;
            $this ->error_msg = "参数错误，caizhi参数不能为空";
            $this->display();
        }else if(empty($filter['yanse'])){
            $this ->error = 1;
            $this ->error_msg = "参数错误，yanse参数不能为空";
            $this->display();
        }else if(empty($filter['channel_id'])){
            $this ->error = 1;
            $this ->error_msg = "参数错误，channel_id参数不能为空";
            $this->display();
        }
        
        $style_sn = $filter['style_sn'];
        $shoucun  = floatval($filter['shoucun']);
        $xiangkou = floatval($filter['xiangkou']);
        $caizhi   = $filter['caizhi'];
        $yanse    = $filter['yanse'];
        $channel_id  = $filter['channel_id'];
        
        $sql = "select * from front.list_style_goods where style_sn='{$style_sn}' and shoucun={$shoucun} and xiangkou={$xiangkou} and caizhi={$caizhi} and yanse={$yanse} and is_ok=1";
        $goods = $this->db->getRow($sql);
        if(empty($goods)){
            $this ->error = 1;
            $this ->error_msg = "不支持定制：未匹配到虚拟商品";
            $this ->return_sql = $sql;
            $this->display();
        }
        
        $goods_sn = $goods['goods_sn'];
        $product_type_id = $goods['product_type_id'];
        $cat_type_id = $goods['cat_type_id'];        
        $xiangkou    = $goods['xiangkou']*100;
        $dingzhichengben = $goods['dingzhichengben'];
        
        $sql = "select a.goods_id,a.policy_id,b.policy_name,a.price
			from app_yikoujia_goods as a 
			inner join base_salepolicy_info as b on a.policy_id=b.policy_id 
			inner join app_salepolicy_channel as d on a.policy_id=d.policy_id 
			where a.goods_id='{$goods_sn}' and b.is_kuanprice=1 and b.is_delete=0 and b.bsi_status=3 and a.is_delete=0 and d.channel={$channel_id} 
and a.small<={$xiangkou} and a.sbig>={$xiangkou} and caizhi={$caizhi} and a.tuo_type in(0,2,3)";
        $yikoujia = $this->db->getRow($sql);

        if(!empty($yikoujia)){
            $is_yikoujia = 1;
            $goods_price = $yikoujia['price'];
        }else{
            $sql = "select a.jiajia,a.sta_value from base_salepolicy_info a inner join app_salepolicy_channel b on a.policy_id=b.policy_id   
    		where a.is_kuanprice=0 and a.is_delete=0 and a.bsi_status=3 and a.tuo_type in(0,2,3)
    AND a.product_type_id={$product_type_id} and a.cat_type_id={$cat_type_id} and b.channel={$channel_id} and a.range_begin<{$xiangkou} and a.range_end>={$xiangkou} LIMIT 1";
            $salepolicy = $this->db->getRow($sql);
            if(empty($salepolicy)){
                $this->error = 1;            
                $this->error_msg = "找不到销售政策";
                $this->return_sql = $sql;
                $this->display();
            }
            $is_yikoujia = 0;
            $jiajialv  = $salepolicy['jiajia'];
            $sta_value = $salepolicy['sta_value'];
            $goods_price = $jiajialv*$dingzhichengben + $sta_value;
        }
        $data = array(
            'goods_sn'=>$goods['goods_sn'],
            'style_sn'=>$goods['style_sn'],
            'xiangkou'=>$goods['xiangkou'],
            'shoucun'=>$goods['shoucun'],
            'caizhi'=>$goods['caizhi'],
            'yanse'=>$goods['yanse'],
            'is_yikoujia'=>$is_yikoujia,
            'goods_price'=>round($goods_price),
        ); 
        
        $this -> error = 0;
        $this -> error_msg  = "查询成功";
        $this -> return_sql = $sql;
        $this -> return_msg = $data;
        $this->display();
    }

    //查询蕈状
    public function getStyleParamsBySn()
    {
        $filter = $this->filter;
        $shapearr = array();
        $sql = "select style_id from front.base_style_info where style_sn ='".$filter['style_sn']."'";
        $style_id = $this->db->getOne($sql);
        $tempSql = "";
        if(!empty($style_id)){
            $tempSql = "select shape from front.rel_style_stone where stone_position=1 and stone_cat in(1,2) AND style_id =".$style_id;
            //file_put_contents('diamond.log', $tempSql);
            $shapearr = $this->db->getAll($tempSql);
        }
        if(!empty($shapearr)){
            $this ->error = 0;
            $this ->error_msg = "查询成功";
        }else{
            $this ->error = 1;
            $this ->error_msg = "查询失败";
        }        
        $this ->return_sql = $tempSql;        
        $this ->return_msg = $shapearr;
        $this->display();
    }
    /**
     * 搜索表单
     */
    public function getStyleGoodsIndex()
    {   
        if(empty($this->filter['keys']) || !is_array($this->filter['keys'])){
            $this -> error = 1;
            $this -> error_msg = "参数错误:keys不能为空";
            $this -> return_msg = array();
            $this->display();
        }
        $keys = $this->filter['keys'];
        $data = array();
        foreach ($keys as $key){
            if($key == 'xilie'){

                $data['xilie'] = array(1 => '天鹅湖', 2 => '天使之吻', 3 => '怦然心动',5=>'天使之翼',8=>'天生一对');
                                
            }else if($key == "caizhi"){

                $data['caizhi'] = array(1 => '18K', 2 => 'PT950');
                
            }else if($key =="shape"){

                $data['shape'] = array(1 => '圆形', 2 => '公主方形', 3 => '祖母绿形', 4 => '橄榄形', 5 => '椭圆形', 6 => '水滴形', 7 => '心形', 8 => '坐垫形', 9 => '辐射形');
                
            }else if($key =="cat_type"){

                $data['cat_type'] = array(2 => '钻戒', 11 => '对戒', 10 => '男戒', 3 => '吊坠', 4 => '项链', 5 => '耳饰', 7 => '手链', 15 => '黄金');
            }else if($key =="cart"){

                $data['cart'] = array('无钻'=>array(0, 0), '30分以下'=>array(0, 0.29), '30-50分'=>array(0.3,0.5), '50-70分'=>array(0.5,0.7), '50-70分'=>array(0.7,1), '1克拉以上'=>array(1.01, 10));
            }else if($key =="price"){

                $data['price'] = array('3000以下'=>array(0,2999), '3000-5000' =>array(3000,5000), '5000-10000'=>array(5000, 10000), '10000-15000'=>array(10000, 15000), '15000-30000'=>array(15000, 30000), '30000以上'=>array(30000,10000000));

            }else if($key == "caizhi"){

                $data['caizhi'] = array('18K', 'PT950');
            }else if($key == "jud_type"){

                $data['jud_type'] = array('吊坠/项链', '手链/手镯', '耳饰');
            }else if($key == "pick_xilie"){

                $data['pick_xilie'] = array(24=>'香榭巴黎', 6=>'星耀美钻', 20=>'心之吻', 8=>'天生一对', 5=>'天使之翼');
            }else if($key == "is_xianhuo"){
                $data['is_xianhuo'] = array(0=>"期货",1=>"现货");
            }
        }
        $this -> error = 0;
        $this -> return_msg = $data;
        $this->display();

    } 
    /**
     * 更多定制属性表单列表
     */
    public function getStyleGoodsDiyIndex(){
        if(empty($this->filter['keys']) || !is_array($this->filter['keys'])){
            $this -> error = 1;
            $this -> error_msg = "参数错误:keys不能为空";
            $this -> return_msg = array();
            $this->display();
        }
        $keys = $this->filter['keys'];
        $data = array();
        $attrModel = new GoodsAttributeModel(11);
        $CStyleModel = new CStyleModel(19);
        foreach ($keys as $key){
            if($key == 'tuo_type'){
                $data['tuo_type'] = array(1 => '成品', 2 => '空托');        
            }else if($key == "xiangqian"){
                $data['xiangqian'] =  array('1'=>'工厂配钻，工厂镶嵌','2'=>'不需工厂镶嵌','3'=>'需工厂镶嵌','4'=>'客户先看钻再返厂镶嵌','5'=>'成品','6'=>'镶嵌4C裸钻');
            }else if($key =="zhushi_num"){
                if(empty($this->filter['style_sn'])){
                    $this -> error = 1;
                    $this -> error_msg = "参数错误:style_sn不能为空";
                    $this -> return_msg = array();
                    $this->display();
                }
                $style_sn = $this->filter['style_sn'];                
                $stoneList = $CStyleModel->getStyleStoneByStyleSn($style_sn);
                $zhushi_num = 0;
                if(!empty($stoneList[1])){
                    $zhushiList = $stoneList[1];//主石列表
                    foreach ($zhushiList as $zhushi) {
                        $zhushi_num += $zhushi['zhushi_num'];
                    }
                }   
                $data['zhushi_num'] = $zhushi_num;
            }else if($key == "cert"){
                $data['cert'] = $attrModel->getCertList();//整数类型
            }else if($key == "color"){
                $data['color'] = $attrModel->getColorList();//颜色
            }else if($key == "confirm"){
                $data['confirm'] = array(1=>"是",0=>"否");//是否
            }else if($key == "clarity"){
                $data['clarity'] = $attrModel->getClarityList();//净度
            }else if($key == "cut"){
                $data['cut'] = $attrModel->getCutList();//净度
            }else if($key == "facework"){
                //$data['facework'] = $attrModel->getFaceworkList();
                if(empty($this->filter['style_sn'])){
                    $this -> error = 1;
                    $this -> error_msg = "参数错误:style_sn不能为空";
                    $this -> return_msg = array();
                    $this->display();
                }
                $style_sn = $this->filter['style_sn'];
                $res = $attrModel->getAttrValListBySN($style_sn,array('biaomiangongyi'));
                if(!empty($res['biaomiangongyi'])){
                   $data['facework'] = $res['biaomiangongyi'];
                }else{
                    $data['facework'] = $attrModel->getFaceworkList();
                }
            }else if($key=="style_info"){
                if(empty($this->filter['style_sn'])){
                    $this -> error = 1;
                    $this -> error_msg = "参数错误:style_sn不能为空";
                    $this -> return_msg = array();
                    $this->display();
                }
                $style_sn = $this->filter['style_sn'];
                $sql = "select * from front.base_style_info where style_sn='{$style_sn}'";
                $data['style_info'] = $this->db->getRow($sql);
            }else if($key == "kezi"){                
                $kezi = array(
                    "[&符号]"=>"1.png",
                    "[间隔号]"=>"2.png",
                    "[空心]"=>"3.png",
                    //"[实心]"=>"4.png",
                    "[小数点]"=>"5.png",
                    "[心心相印]"=>"6.png",
                    "[一箭穿心]"=>"7.png",
                );
                
                if(!empty($this->filter['style_sn'])){
                    $sql = "select style_type from front.base_style_info where style_sn='{$this->filter['style_sn']}'";
                    $style_type = $this->db->getOne($sql);
                    if($style_type ==2){
                        $kezi["[红宝石]"] = "8.png";
                    }
                }
                $data['kezi'] = $kezi;
           }
        }
        $this -> error = 0;
        $this -> return_msg = $data;
        $this->display();
    }
    /**
     * 根据款式商品聚合属性列表
     */
    public function getStyleGoodsAttr(){
        $filter = $this->filter;
        if(empty($filter['style_sn'])){
            $this ->error = 1;
            $this ->error_msg = "参数错误，款号不能为空";
            $this->display();
        }
        
        $sql = "select caizhi,yanse,shoucun,xiangkou from front.list_style_goods where style_sn='{$filter['style_sn']}' and is_ok=1";
        if(isset($filter['xiangkou']) && $filter['xiangkou']!=''){
            $sql .= " AND xiangkou=".floatval($filter['xiangkou']);
        }
        $data = $this->db->getAll($sql);
        
        if(!empty($data)){
            $caizhiArr = array(1=>'18K',2=>'PT950');            
            $dd = new DictView(new DictModel(1));
            $yanseArr = $dd->getEnumArray("style.color");
            $yanseArr = array_column($yanseArr,'label','name'); 
            $datalist = array();
            foreach ($data as $key=>$vo){                
                $caizhi = !empty($caizhiArr[$vo['caizhi']])?$caizhiArr[$vo['caizhi']]:$vo['caizhi'];
                $yanse  = !empty($yanseArr[$vo['yanse']])?$yanseArr[$vo['yanse']]:$vo['yanse'];                
                $vo['jinse_key']   = $vo['caizhi'].'|'.$vo['yanse'];
                $vo['jinse_name']  = $caizhi.$yanse;                
                $data[$key] = $vo;
            }
            $jinseArr = array_unique(array_column($data,'jinse_name','jinse_key'));
            $shoucunArr = array_unique(array_column($data,'shoucun'));
            $xiangkouArr = array_unique(array_column($data,'xiangkou'));
            sort($shoucunArr);
            $data = array(
                'jinse'=>$jinseArr,
                'shoucun'=>$shoucunArr,
                'xiangkou'=>$xiangkouArr,
            );
            $this -> error = 0;
            $this -> error_msg  = "查询成功";
            $this -> return_sql = $sql;            
            $this -> return_msg = $data;
            $this->display();
        }else{
            $this -> error = 1;
            $this -> error_msg = "未查询到数据";
            $this -> return_sql = $sql;
            $this -> return_msg = array();
            $this->display();
        }
        
    }
    /**
     * 款式商品详情
     */
    public function getStyleGoodsInfo(){
        $filter = $this->filter;
        if(empty($filter['goods_sn'])){
            $this -> error = 1;
            $this -> error_msg = "参数错误，goods_sn不能为空";
            $this -> return_msg = array();
            $this->display();
        }         
        $goods_sn = $this->filter['goods_sn'];
        $sql = "select g.*,s.goods_content,s.style_id from front.list_style_goods g inner join front.base_style_info s on g.style_sn=s.style_sn where g.goods_sn='{$goods_sn}'";
        if(!empty($filter['status'])){
            $sql .=" AND status={$filter['status']}";
        }
        $data = $this->db->getRow($sql);
        if(empty($data)){
             $this -> error = 1;
             $this -> error_msg  = "查询失败";
             $this -> return_sql = $sql;
             $this -> return_msg = array();
             $this->display();
        }
        $caizhiArr = array(1=>'18K',2=>'PT950');
        $data['caizhi_name'] = isset($caizhiArr[$data['caizhi']])?$caizhiArr[$data['caizhi']]:$data['caizhi'];

        $dd = new DictView(new DictModel(1));
        $data['yanse_name'] = $dd->getEnum("style.color",$data['yanse']);

        if(!empty($filter['extends'])){
             $style_sn = $data['style_sn'];
             $style_id = $data['style_id'];
             if(in_array("goods_image",$filter['extends'])){
                 $image_sql = "select middle_img from front.app_style_gallery where style_sn='{$style_sn}'";
                 $goods_image = $this->db->getOne($image_sql);
                 $data['goods_image'] = $goods_image;
             }
            
             if(in_array("goods_shape",$filter['extends'])){
                 $attrModel = new GoodsAttributeModel(11);
                 $shapeArr = $attrModel->getShapeList();
                 
                 $shape_sql = "select distinct shape from front.rel_style_stone where style_id='{$style_id}' and stone_position=1 and shape<>0";
                 $shape_list = $this->db->getAll($shape_sql);
                 foreach ($shape_list as $vo){
                     $shape[] = $vo['shape'];
                     $shape_name[] = isset($shapeArr[$vo['shape']])?$shapeArr[$vo['shape']]:$vo['shape'];
                 }
                 $data['shape'] = implode("|", $shape);
                 $data['shape_name'] = implode("|", $shape_name); 
             }
             //商品价格计算
             if(in_array("goods_price",$filter['extends'])){
                 
                 if(empty($filter['channel_id'])){
                     $this->error = 1;
                     $this->error_msg = "参数错误，channel_id不能为空";
                     $this->return_msg = array();
                     $this->display();
                 } 
                 
                 $channel_id  = $filter['channel_id'];
                 $shoucun  = $data['shoucun'];
                 $xiangkou = $data['xiangkou'];
                 $caizhi   = $data['caizhi'];
                 $yanse    = $data['yanse'];
                 $product_type_id = $data['product_type_id'];
                 $cat_type_id = $data['cat_type_id'];
                 $xiangkou    = $data['xiangkou']*100;
                 $dingzhichengben = $data['dingzhichengben'];
                      
                 $sql = "select a.goods_id,a.policy_id,b.policy_name,a.price
                 from app_yikoujia_goods as a
                 inner join base_salepolicy_info as b on a.policy_id=b.policy_id
                 inner join app_salepolicy_channel as d on a.policy_id=d.policy_id
                 where a.goods_id='{$goods_sn}' and b.is_kuanprice=1 and b.is_delete=0 and b.bsi_status=3 and a.is_delete=0 and d.channel={$channel_id}
                 and a.small<={$xiangkou} and a.sbig>={$xiangkou} and caizhi={$caizhi} and a.tuo_type in(0,2,3)";
                 $yikoujia = $this->db->getRow($sql);
                 $goods_price = 0;
                 if(!empty($yikoujia)){
                     $is_yikoujia = 1;
                     $goods_price = $yikoujia['price'];
                 }else{
                    $sql = "select a.jiajia,a.sta_value from base_salepolicy_info a inner join app_salepolicy_channel b on a.policy_id=b.policy_id
                    where a.is_kuanprice=0 and a.is_delete=0 and a.bsi_status=3 and a.tuo_type in(0,2,3)
                    AND a.product_type_id={$product_type_id} and a.cat_type_id={$cat_type_id} and b.channel={$channel_id} and a.range_begin<{$xiangkou} and a.range_end>={$xiangkou} LIMIT 1";
                    $salepolicy = $this->db->getRow($sql);
                    $is_yikoujia = 0;
                    if(!empty($salepolicy)){
                        $jiajialv  = $salepolicy['jiajia'];
                        $sta_value = $salepolicy['sta_value'];
                        $goods_price = $jiajialv*$dingzhichengben + $sta_value;
                    }
                 }
                 $data['goods_price'] = (int)$goods_price;
                 $data['is_yikoujia'] = $is_yikoujia;
             }
        }

        $this -> error = 0;
        $this -> error_msg  = "查询成功";
        $this -> return_sql = $sql;
        $this -> return_msg = $data;
        $this->display();

    }
    /**
     * 成品定制石头价格列表
     */
    public function getCpdzPriceList(){
        $filter = $this->filter;
        $require_params = array('style_sn','caizhi','yanse','xiangkou','shoucun','carat','channel_id');
        foreach ($require_params as $vo){
            if(!isset($filter[$vo]) || $filter[$vo]==""){
                $this ->error = 1;
                $this ->error_msg = "参数错误：{$vo}不能为空";
                $this->display();
            }
        }
        $style_sn = $filter['style_sn'];
        $shoucun  = $filter['shoucun'];
        $xiangkou = $filter['xiangkou'];
        $caizhi   = $filter['caizhi'];
        $yanse    = $filter['yanse'];
        $carat    = $filter['carat'];
        $channel_id  = $filter['channel_id'];
        if(!empty($filter['policy_id'])){
            $policy_id = $filter['policy_id'];
        }else{
            $policy_id = 0;
        }        
        $sql = "select * from front.list_style_goods where style_sn='{$style_sn}' and shoucun={$shoucun} and xiangkou={$xiangkou} and caizhi={$caizhi} and yanse={$yanse} and is_ok=1";
        $goods = $this->db->getRow($sql);
        if(empty($goods)){
            $this ->error = 1;
            $this ->error_msg = "未匹配到商品";
            $this ->return_sql = $sql;
            $this->display();
        }
        $goods_id = $goods['goods_sn'];
        $model = Util::get_model('sales\AppSalepolicyGoodsModel', [15]);
        
        $where = array(
            'goods_id'=>$goods_id,
            'tuo_type'=>1,//成品
            'style_sn'=>$style_sn,
            'xiangkou'=>$xiangkou,
            'carat'=>$carat,
            'shoucun'=>$shoucun,
            'caizhi'=>$caizhi,
            'yanse'=>$yanse,
            'channel'=>$channel_id,
            'policy_id'=>$policy_id,
        );
        $result = $model->getCpdzList($where);
        if($result['error']===false){
            $this->error = 0;
            $this->error_msg = "查询成功";
            $this->return_msg = $result['data'][0]['sprice'];
            $this->display();
        }else{
            $this->error = 1;
            $this->error_msg = $result['error'];
            $this->return_msg = $result['data'];
            $this->display();
        }
    }
    /**
     * 成品定制价格
     */
    public function getCpdzPrice(){
        $filter = $this->filter;
        $require_params = array('style_sn','caizhi','yanse','xiangkou','shoucun','carat','channel_id','cert','color','clarity','policy_id');
        foreach ($require_params as $vo){
            if(!isset($filter[$vo]) || $filter[$vo]==""){
                $this ->error = 1;
                $this ->error_msg = "参数错误：{$vo}不能为空";
                $this->display();
            }
        }
        
        /*
        if(empty($filter['shape'])){
            $this->error = 1;
            $this->error_msg = "参数错误，shape不能为空";
            $this->return_msg = array();
            $this->display();
        }*/      
 
        $tuo_type = $filter['tuo_type'];
        $style_sn = $filter['style_sn'];
        $shoucun  = $filter['shoucun'];
        $xiangkou = $filter['xiangkou'];
        $carat    = $filter['carat'];
        $caizhi   = $filter['caizhi'];
        $yanse    = $filter['yanse'];
        $channel_id  = $filter['channel_id'];
        $cert  = $filter['cert'];
        $cert_id  = $filter['cert_id'];
        $color    = $filter['color'];
        $clarity  = $filter['clarity'];
        $policy_id = $filter['policy_id'];
        /* if(empty($shape)){
            $sql = "select rss.shape from fron.rel_style_stone rss inner join front.base_style_info bsi on rss.style_id=bsi.style_id where bsi.style_sn='{$style_sn}'";
            $row = $this->db->getRow($sql);
            $shape = !empty($row['shape'])?$row['shape']:"";
        } */
        $sql = "select * from front.list_style_goods where style_sn='{$style_sn}' and shoucun={$shoucun} and xiangkou={$xiangkou} and caizhi={$caizhi} and yanse={$yanse} and is_ok=1";
        $goods = $this->db->getRow($sql);
        if(empty($goods)){
            $this ->error = 1;
            $this ->error_msg = "未匹配到商品";
            $this ->return_sql = $sql;
            $this->display();
        }
        $goods_id = $goods['goods_sn'];        
        $model = Util::get_model('sales\AppSalepolicyGoodsModel', [15]);
        
        $where = array(
            'goods_id'=>$goods_id,
            'tuo_type'=>$tuo_type,
            'style_sn'=>$style_sn,
            'xiangkou'=>$xiangkou,
            'carat'=>$carat,
            'shoucun'=>$shoucun,
            'caizhi'=>$caizhi,
            'yanse'=>$yanse,
            'cert'=>$cert,
            'color'=>$color,
            'clarity'=>$clarity,
            'channel'=>$channel_id, 
            'policy_id'=>$policy_id           
        );
        $result = $model->getCpdzList($where);
        if($result['error']===false){        
            $this->error = 0;
            $this->error_msg = "查询成功";
            $this->return_msg = $result['data'][0];
            $this->display();
        }else{
            $this->error = 1;
            $this->error_msg = $result['error'];
            $this->return_msg = $result['data'];
            $this->display();
        }
    }
    /**
     * 根据 款号 材质，材质颜色，指圈，镶口 获取有效虚拟货号
     */
    public function getStyleGoodsSn(){
        $filter = $this->filter;
        if(empty($filter['style_sn'])){
            $this ->error = 1;
            $this ->error_msg = "参数错误，style_sn参数不能为空";
            $this->display();
        }else if(empty($filter['shoucun'])){
            $this ->error = 1;
            $this ->error_msg = "参数错误，shoucun参数不能为空";
            $this->display();
        }else if(empty($filter['xiangkou'])){
            $this ->error = 1;
            $this ->error_msg = "参数错误，xiangkou参数不能为空";
            $this->display();
        }else if(empty($filter['caizhi'])){
            $this ->error = 1;
            $this ->error_msg = "参数错误，caizhi参数不能为空";
            $this->display();
        }else if(empty($filter['yanse'])){
            $this ->error = 1;
            $this ->error_msg = "参数错误，yanse参数不能为空";
            $this->display();
        }
        
        $style_sn = $filter['style_sn'];
        $shoucun  = $filter['shoucun'];
        $xiangkou = $filter['xiangkou'];
        $caizhi   = $filter['caizhi'];
        $yanse    = $filter['yanse'];
        
        $sql = "select goods_sn as goods_id,0 as goods_price from front.list_style_goods where style_sn='{$style_sn}' and shoucun={$shoucun} and xiangkou={$xiangkou} and caizhi={$caizhi} and yanse={$yanse} and is_ok=1";
        $row = $this->db->getRow($sql);
        if(empty($row)){
            $this ->error = 1;
            $this ->error_msg = "未匹配到商品";
            $this ->return_sql = $sql;
            $this->display();
        }
        $this->error = 0;
        $this->error_msg = "查询成功";
        $this->return_msg = $row;
        $this->display();
    }
	/**
    * 查询商品信息
    * @param $goods_sn
    * @return json
    */
	public function GetStyleGoods()
	{
		$s_time = microtime();
        $where='';
        $goods_sn=trim($this->filter['goods_sn']);
		//$order_id = 49;
		if(!empty($goods_sn))
		{
			$where .= " `goods_sn` in ('".$goods_sn."')";
        }

        if(!empty($where)){
            //查询商品详情
            $sql = "select * from `list_style_goods` " .
                   "where ".$where." ;";
            $row = $this->db->getAll($sql);
        }else{
            $this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "商品编号不能为空";
			$this->display();
        }

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}

	/**
    * 查询款号
    * @param $style_id
    * @return json
    */
	public function getStyleInfoByStyleId()
	{
		$s_time = microtime();
        $where='';
        $style_id=trim($this->filter['style_id']);
		//$order_id = 49;
		if(!empty($style_id))
		{
			$where .= " `style_id` = ".$style_id;
        }

        if(!empty($where)){
            //查询商品详情
            $sql = "select * from `base_style_info` where ".$where;
            $row = $this->db->getRow($sql);

            $row['product_type'] = $this->db->getOne("select `product_type_name` from `app_product_type` where `product_type_id`={$row['product_type']}");
            $row['style_type'] = $this->db->getOne("select `cat_type_name` from `app_cat_type` where `cat_type_id`={$row['style_type']}");
        }else{
            $this -> error = 1;
			$this -> return_sql = "";
			$this -> error_msg = "款id不能为空";
			$this->display();
        }

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}



    /**
    * 查询款式图片信息
    * @param $style_sn 款号
    * @param $image_place 位置
    * @return json
    */
	public function GetStyleGalleryInfo()
	{
		$s_time = microtime();
		$sql = "select `sg`.`g_id`, `sg`.`style_id`, `sg`.`image_place`, `sg`.`img_sort`, `sg`.`img_ori`, `sg`.`thumb_img`, `sg`.`middle_img`, `sg`.`big_img` from `base_style_info` as `si`,`app_style_gallery` as `sg`" .
		    " where `si`.`style_id`=`sg`.`style_id`";

		if(!empty($this->filter['style_sn'])){
			$sql .= " and `si`.`style_sn` = '".$this->filter['style_sn']."'";
        }else{
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "款号不能为空";
            $this->display();
        }
        if(isset($this->filter['image_place'])){
            $image_place = (int)$this->filter['image_place']?(int)$this->filter['image_place']:1;
            $sql .= " and `sg`.`image_place`={$this->filter['image_place']}";
        }
        $sql .= " order by `sg`.`image_place` asc";
        $data = $this->db->getAll($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$data){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $data;
			$this->display();
		}
	}


	/**
    * 查询款式信息
    * @param $style_sn
    * @return json
    */
	public function GetStyleAttribute()
	{
		$s_time = microtime();
        $where='';
        $style_sn=trim($this->filter['style_sn']);
		//$order_id = 49;
        if($style_sn == ''){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "相应的款号不能为空！";
            $this -> return_msg = array();
            $this->display();
        }
        $where = " WHERE 1";
		if(!empty($style_sn))
		{
			$where .= " AND a.`style_sn` = '".$style_sn."'";
        }

        if(!empty($where)){
            //判断此款是否已经审核
            $sql = "select a.`check_status`,a.`product_type` as `product_type_id`,a.`style_type` as `cat_type_id` from `base_style_info` as a ".$where;
            $style_data = $this->db->getRow($sql);
            if(empty($style_data)){
                $this -> error = 1;
                $this -> return_sql = $sql;
                $this -> error_msg = "款式库中没有此款号信息";
                $this -> return_msg = array();
                $this->display();
            }

            //只有已经审核的数据才有效
            if($style_data['check_status']!=3){
                $this -> error = 1;
                $this -> return_sql = $sql;
                $this -> error_msg = "款式库中此款号不是审核状态，数据无效！";
                $this -> return_msg = array();
                $this->display();
            }

            //获取此款的产品线和分类，来确定一下属性的是否必填
            $product_type_id = $style_data['product_type_id'];
            $cat_type_id = $style_data['cat_type_id'];

            //$sql_1 = " SELECT `is_require`,`attribute_id`,`attr_type` FROM `rel_cat_attribute` WHERE `product_type_id`=$product_type_id and `cat_type_id`=$cat_type_id AND `attr_type`=2";
            $sql_1 = " SELECT `is_require`,`attribute_id`,`attr_type` FROM `rel_cat_attribute` WHERE `product_type_id`=$product_type_id and `cat_type_id`=$cat_type_id ";
            $cat_attribute_arr = $this->db->getAll($sql_1);
            if(empty($cat_attribute_arr)){
                $this -> error = 1;
                $this -> return_sql = $sql_1;
                $this -> error_msg = "款式库中此款号对应的产品线或款式分类没有设置属性！";
                $this -> return_msg = array();
                $this->display();
            }
            //获取属性的是否必填和销售属性
            $new_cat_attribute = array();
			$new_cat_attribute_attr_type = array();
            foreach ($cat_attribute_arr as $val){
                $is_require = $val['is_require'];
                $attr_id = $val['attribute_id'];
                $attr_type = $val['attr_type'];
                $new_cat_attribute[$attr_id] = $is_require;
                $new_cat_attribute_attr_type[$attr_id] = $attr_type;
            }

            //获取指圈的属性id,因为指圈需要由6-8切分6，7，8
            $zhiquan_attr_id =0;
            $sql = "SELECT `attribute_id` FROM `app_attribute` WHERE `attribute_name` ='指圈'";
            $zq_row = $this->db->getRow($sql);
            if($zq_row){
                $zhiquan_attr_id = $zq_row['attribute_id'];
            }

            //此款的所有属性和属性值
            $sql = "SELECT a.product_type_id,a.cat_type_id,a.`attribute_id`,a.`attribute_value`,b.`attribute_code`,b.`show_type`,b.`attribute_name`,c.`att_value_id`,c.`att_value_name` FROM `rel_style_attribute` as a inner join `app_attribute` as b on a.`attribute_id` = b.`attribute_id` left join `app_attribute_value` as c on b.`attribute_id` = c.`attribute_id`  ".$where." ;";
            $row = $this->db->getAll($sql);
            if(empty($row)){
                $this -> error = 0;
                $this -> return_sql = $sql;
                $this -> error_msg = "款式库没有设置此款的属性";
                $this -> return_msg = array();
                $this->display();
            }
            //过滤一下
            $style_attr_arr = array();
            foreach ($row as $val){
                $a_id = $val['attribute_id'] ;
                $show_type = $val['show_type'];
                $style_value = $val['attribute_value'];
                if($show_type == 3){//多选
                    if(empty($style_value)) {
                       continue;
                    }
                    $tmp_value = rtrim($style_value,",");
                    $style_attr_arr[$a_id] = explode(",", $tmp_value);
                }else{
                    $style_attr_arr[$a_id] =$style_value;
                }
            }


            //获取此款的产品线和分类，来确定一下属性的是否必填
            $product_type_id = $row[0]['product_type_id'];
            $cat_type_id = $row[0]['cat_type_id'];

            $sql_1 = " SELECT * FROM `rel_cat_attribute` WHERE `product_type_id`=$product_type_id and `cat_type_id`=$cat_type_id";
            $cat_attribute_arr = $this->db->getAll($sql_1);
            $new_cat_attribute = array();
			$new_cat_attribute_attr_type = array();

            foreach ($cat_attribute_arr as $val){
                $is_require = $val['is_require'];
                $attr_id = $val['attribute_id'];
                $attr_type = $val['attr_type'];
                $new_cat_attribute[$attr_id] = $is_require;
                $new_cat_attribute_attr_type[$attr_id] = $attr_type;
            }

            $new_attribute_data = array();
            foreach ($row as $val) {
                //匹配一下属性值存在
                $value_id = $val['att_value_id'];
                $attribute_id = $val['attribute_id'];
                if(isset($style_attr_arr[$attribute_id]) && is_array($style_attr_arr[$attribute_id])){
                    if(!in_array($value_id, $style_attr_arr[$attribute_id])){
                        continue;
                    }
                }
                /*else{
                    if(isset($style_attr_arr[$attribute_id]) && $value_id != $style_attr_arr[$attribute_id]){
                        continue;
                    }
                }*/

                //只取销售属性
                /*if(!array_key_exists($attribute_id, $new_cat_attribute_attr_type)){
                    continue;
                }*/

                $attribute_value = $val['attribute_value'];
                $show_type = $val['show_type'];

                $att_value_name = $val['att_value_name'];
                $new_attribute_data[$attribute_id]['attribute_id'] = $val['attribute_id'];
                $new_attribute_data[$attribute_id]['attribute_name'] = $val['attribute_name'];
                $new_attribute_data[$attribute_id]['attribute_code'] = $val['attribute_code'];
                $new_attribute_data[$attribute_id]['show_type'] = $val['show_type'];
                $new_attribute_data[$attribute_id]['is_require'] = $new_cat_attribute[$attribute_id];
                $new_attribute_data[$attribute_id]['attr_type'] = $new_cat_attribute_attr_type[$attribute_id];

                switch ($show_type){
                    case 1:
                         //文本框
                         $new_attribute_data[$attribute_id]['value'] = $attribute_value;
                        break;
                    case 2:
                        //2单选
                        if($attribute_value == $value_id){
                             $new_attribute_data[$attribute_id]['value'] = $att_value_name;
                        }
                        break;
                    case 3:
                        //3多选
                        if(!isset($new_attribute_data[$attribute_id]['value'])){
                            $new_attribute_data[$attribute_id]['value'] = '';

                        }
                        //指圈需要切割6-8变成6,7,8
                        if($attribute_id == $zhiquan_attr_id){
                            $zhiquan_arr = $this->cutFingerInfo(array($att_value_name));
                            $zhiquan_str = implode(",", $zhiquan_arr[0]);
                            $new_attribute_data[$attribute_id]['value'] .= $zhiquan_str.',';
                            $new_attribute_data[$attribute_id]['valstr'] .= $att_value_name.',';
                        }else{
                            $new_attribute_data[$attribute_id]['value'] .= $att_value_name.',';
                        }
                        break;
                    case 4:
                        //4下拉列表
                        if($attribute_value == $value_id){
                            $new_attribute_data[$attribute_id]['value'] = $att_value_name;
                        }

                        break;
                }

            }
        }else{
            $row=false;
        }

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();;
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $new_attribute_data;
			$this->display();
		}
	}
	/**
    * 查询产品线和款式分类的属性
    * @param $product_id ,$cat_type_id
    * @return json
    */
	public function GetCatAttribute()
	{
		$s_time = microtime();
		//$order_id = 49;
        //$where = " WHERE a.`attr_type`=2 ";
        $where = " WHERE 1  ";
		if(isset($this->filter['product_type_id']) && !empty(trim($this->filter['product_type_id'])))
		{
            $product_type = trim($this->filter['product_type_id']);
			$where .= " AND a.`product_type_id` = '".$product_type."'";
        }

		if(isset($this->filter['cat_type_id']) && !empty(trim($this->filter['cat_type_id'])))
		{
            $cat_type = trim($this->filter['cat_type_id']);
			$where .= " AND a.`cat_type_id` = '".$cat_type."'";
        }

         //获取指圈的属性id,因为指圈需要由6-8切分6，7，8
        $zhiquan_attr_id =0;
        $sql = "SELECT `attribute_id` FROM `app_attribute` WHERE `attribute_name` ='指圈'";
        $zq_row = $this->db->getRow($sql);
        if($zq_row){
            $zhiquan_attr_id = $zq_row['attribute_id'];
        }


        if(!empty($where)){
            //查询商品详情
            //$sql = "SELECT a.*,b.`show_type`,b.`attribute_name`,c.`att_value_id`,c.`att_value_name` FROM `rel_style_attribute` as a inner join `app_attribute` as b on a.`attribute_id` = b.`attribute_id` left join `app_attribute_value` as c on b.`attribute_id` = c.`attribute_id` where a.style_sn='A001'  ".$where." ;";
            $sql = "SELECT a.`attribute_id`,a.`is_require`,a.`attr_type`,b.`show_type`,b.`attribute_name`,b.`attribute_code`,c.`att_value_id`,c.`att_value_name` FROM `rel_cat_attribute` as a inner join `app_attribute` as b on a.`attribute_id` = b.`attribute_id` left join `app_attribute_value` as c on b.`attribute_id` = c.`attribute_id`  ".$where." GROUP BY att_value_id;";
            $row = $this->db->getAll($sql);
            foreach ($row as $val) {
                $attribute_id = $val['attribute_id'];
                //$attribute_value = $val['attribute_value'];
                $show_type = $val['show_type'];
                $value_id = $val['att_value_id'];
                $att_value_name = $val['att_value_name'];
                $new_attribute_data[$attribute_id]['attribute_id'] = $val['attribute_id'];
                $new_attribute_data[$attribute_id]['attribute_name'] = $val['attribute_name'];
                $new_attribute_data[$attribute_id]['attribute_code'] = $val['attribute_code'];
                $new_attribute_data[$attribute_id]['show_type'] = $val['show_type'];
                $new_attribute_data[$attribute_id]['is_require'] = $val['is_require'];
                $new_attribute_data[$attribute_id]['attr_type'] = $val['attr_type'];
                switch ($show_type){
                    case 1:
                         //文本框
                         $new_attribute_data[$attribute_id]['value'] = '';
                        break;
                    case 2:
					case 3:
					case 4:
                        //2单选,3多选,4下拉列表
                        if(!isset($new_attribute_data[$attribute_id]['value'])){
                            $new_attribute_data[$attribute_id]['value'] = '';

                        }
			//指圈的id
                        if($attribute_id == $zhiquan_attr_id){
                            $zhiquan_arr = $this->cutFingerInfo(array($att_value_name));
                            $zhiquan_str = implode(",", $zhiquan_arr[0]);
                            $new_attribute_data[$attribute_id]['value'] .= $zhiquan_str.',';
                        }else{
                            $new_attribute_data[$attribute_id]['value'] .= $att_value_name.',';
                        }

                        break;
                }

            }
        }else{
            $row=false;
        }

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $new_attribute_data;
			$this->display();
		}
	}

	/**
    * 查询工厂信息
    * @param $style_sn ,$factory_sn
    * @return json
    */
	public function GetFactryInfo()
	{
		$s_time = microtime();

        $where = " WHERE 1";
        if(isset($this->filter['style_sn']) && !empty($this->filter['style_sn'])){
             $style_sn=trim($this->filter['style_sn']);
             $where .= " AND `style_sn` = '".$style_sn."'";
        }
        if(isset($this->filter['factory_sn']) && !empty($this->filter['factory_sn'])){
             $factory_sn=trim($this->filter['factory_sn']);
             $where .= " AND `factory_sn` = '".$factory_sn."'";
        }
		if(isset($this->filter['factory_id']) && !empty($this->filter['factory_id'])){
             $factory_id=trim($this->filter['factory_id']);
             $where .= " AND `factory_id` = '".$factory_id."'";
        }
		if(isset($this->filter['is_default']) && !empty($this->filter['is_default'])){
			 $is_default=trim($this->filter['is_default']);
             $where .= " AND `is_def` =$is_default";
        }
		if(isset($this->filter['ids']) && !empty($this->filter['ids'])){
			  $this->filter['ids']="'".implode("','",$this->filter['ids'])."'";
              $where .= " AND `style_sn` in ({$this->filter['ids']})";
        }
		if(isset($this->filter['factory_ids']) && !empty($this->filter['factory_ids'])){
			  $this->filter['factory_ids']=implode(",",$this->filter['factory_ids']);
              $where .= " AND `factory_id` in ({$this->filter['factory_ids']})";
        }
		if(isset($this->filter['is_cancel']) && !empty($this->filter['is_cancel'])){
              $where .= " AND `is_cancel`={$this->filter['is_cancel']}";
        }
        if(!empty($where)){
            //查询商品详情
            $sql = "select * from `rel_style_factory` " .$where." ORDER BY f_id DESC;";//ORDER BY f_id 很重要误删。涉及到布产提示排序
			file_put_contents('./ruir20150615.txt',print_r($sql,true),FILE_APPEND);
            $row = $this->db->getAll($sql);
        }else{
            $row=false;
        }

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}

    /*
     * 获取产品线
     */
    public function getProductTypeInfo()
    {
        $s_time = microtime();
        $sql = "SELECT 	product_type_id as id,product_type_name as name,parent_id,concat(tree_path,'-',product_type_id) AS abspath FROM `app_product_type` WHERE product_type_status=1";

        if(isset($this->filter['product_type_id']) && !empty($this->filter['product_type_id'])){
             $product_type_id=trim($this->filter['product_type_id']);
             $sql .= " AND `product_type_id` = '".$product_type_id."'";
        }

		if(isset($this->filter['product_type_name']) && !empty($this->filter['product_type_name'])){
             $product_type_name=trim($this->filter['product_type_name']);
             $sql .= " AND `product_type_name` = '".$product_type_name."'";
        }

		$sql .=" ORDER BY abspath ASC,display_order DESC";
        $row = $this->db->getAll($sql);

        // 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
    }
/*
 * 获取工厂
 */
        public function getFactoryIdByStyleSn() {
            $s_time = microtime();
            $sql = "select sf.factory_id from rel_style_factory sf,base_style_info si where si.style_id=sf.style_id"
                    . " and sf.is_factory=1 ";
            if (isset($this->filter['style_sn'])) {
                $sql.= " and si.style_sn='".$this->filter['style_sn']."'";
            }
            $row = $this->db->getAll($sql);
            // 记录日志
            $reponse_time = microtime() - $s_time;
            $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
            //返回信息
            if(!$row){
                    $this -> error = 1;
                    $this -> return_sql = $sql;
                    $this -> error_msg = "未查询到数据";
                    $this -> return_msg = array();
                    $this->display();
            }else{
                    $this -> error = 0;
                    $this -> return_sql = $sql;
                    $this -> return_msg = $row;
                    $this->display();
            }
        }
	/**
	* 查询材质列表 (或名：主成色)
	* TABLE : app_material_info
	*/
	public function getMaterialInfo(){
		$s_time = microtime();
		$sql = "SELECT `material_id`,  `material_name` FROM `app_material_info` ";
		$str = '';
		if( isset($this->filter['material_name']) && !empty($this->filter['material_name']) )
		{
			$material_name = trim($this->filter['material_name']);
			$str .= "`material_name` = ".$material_name.' AND ';
		}
		if( isset($this->filter['material_status']) && !empty($this->filter['material_status']) )
		{
			$material_status = trim($this->filter['material_status']);
			$str .= "`material_status` = ".$material_status.' AND ';
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `material_id` DESC";
		$row = $this->db->getAll($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
	}


    /*
     * 获取款式分类
     */
    public function getCatTypeInfo()
    {
        $s_time = microtime();
        $sql = "SELECT 	cat_type_id as id,cat_type_name as name,parent_id,concat(tree_path,'-',cat_type_id) AS abspath FROM `app_cat_type` WHERE cat_type_status=1";

        if(isset($this->filter['cat_type_id']) && !empty($this->filter['cat_type_id'])){
             $cat_type_id=trim($this->filter['cat_type_id']);
             $sql .= " AND `cat_type_id` = '".$cat_type_id."'";
        }

		if(isset($this->filter['cat_type_name']) && !empty($this->filter['cat_type_name'])){
             $cat_type_name=trim($this->filter['cat_type_name']);
             $sql .= " AND `cat_type_name` = '".$cat_type_name."'";
        }

        $sql .=" ORDER BY abspath ASC,display_order DESC";
//         file_put_contents('e:/8.sql',$sql);
        $row = $this->db->getAll($sql);


        // 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到数据";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $row;
			$this->display();
		}
    }

    /*
     * 获取款式分类
     */
    public function getCategoryType()
    {
        $s_time = microtime();
        $sql = "SELECT `cat_type_id`,`cat_type_name` FROM `app_cat_type`";
        $row = $this->db->getAll($sql);
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$row){
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到数据";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $row;
            $this->display();
        }
    }

    /**
     * 获取所有款号
     */
    public function getAllStyleSN(){
        $s_time = microtime();

        if (isset($this->filter['All_style']) && $this->filter['All_style'] == 'style') {
            $pass = $this->filter['All_style'];
        }else{
            $this -> error = 1;
            $this->return_sql = '';
            $this->error_msg = "参数有误";
            $this->return_msg = 0;
            $this->display();
        }

        if($pass){
            $sql = 'SELECT `style_id`,`style_sn` FROM `base_style_info` GROUP BY `style_sn`';
            $res = $this->db->getAll($sql);
            $res = array_column($res,'style_sn','style_id');
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(empty($res)){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "未查询到数据";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }

     /**
     * 获取所有已审核款号
     */
    public function getAllStyleSnByCaigou(){
        $s_time = microtime();

        if (isset($this->filter['All_style']) && $this->filter['All_style'] == 'style') {
            $pass = $this->filter['All_style'];
        }else{
            $this -> error = 1;
            $this->return_sql = '';
            $this->error_msg = "参数有误";
            $this->return_msg = 0;
            $this->display();
        }

        if($pass){
            $sql = 'SELECT `style_id`,`style_sn` FROM `base_style_info` WHERE check_status = 3 GROUP BY `style_sn`';
            $res = $this->db->getAll($sql);
            $res = array_column($res,'style_sn','style_id');
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(empty($res)){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "未查询到数据";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }

    /**
     * 获取所有保险费
     */
    public function getAllbaoxianfee(){
        $s_time = microtime();


        $sql = 'SELECT `id`,`min`,`max`,`price`,`status` FROM `app_style_baoxianfee` WHERE 1';

        if(isset($this->filter['min']) && !empty($this->filter['min'])){
             $sql .= " AND `min` >= '".$this->filter['min']."'";
        }

		if(isset($this->filter['max']) && !empty($this->filter['max'])){
             $sql .= " AND `max` <= '".$this->filter['max']."'";
        }

        $res = $this->db->getAll($sql);

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(empty($res)){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "未查询到数据";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $res;
            $this->display();
        }
    }

    public function GetStyleGallery()
    {


        $s_time = microtime();
        $style_sn=trim($this->filter['style_sn']);
        $image_place = isset($this->filter['image_place']) ? intval($this->filter['image_place']) : 1;
        $image_place = $image_place?$image_place:1;
        $where = '';
        if(!empty($style_sn))
        {
            $where .= " AND `style_sn` = '".$style_sn."'";
        }
        if(!empty($where)){
            $sql = "SELECT `style_sn`, `img_ori`,`thumb_img`,`middle_img`,`big_img` FROM `app_style_gallery` WHERE `image_place` = {$image_place}".$where;
            $row = $this->db->getRow($sql);

        }else{
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "款号不能为空";
            $this->display();
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$row){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到数据";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $row;
            $this->display();
        }
    }

    public function GetStyleGalleryByStyle_sn()
    {


        $s_time = microtime();
        $style_sn=trim($this->filter['style_sn']);
        $where = '';
        if(!empty($style_sn))
        {
            $where .= " AND `style_sn` = '".$style_sn."'";
        }
        if(!empty($where)){
            $sql = "SELECT `style_sn`, `img_ori`,`thumb_img`,`middle_img`,`big_img` FROM `app_style_gallery` WHERE 1".$where;
            $row = $this->db->getRow($sql);

        }else{
            $this -> error = 1;
            $this -> return_sql = "";
            $this -> error_msg = "款号不能为空";
            $this->display();
        }

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

        //返回信息
        if(!$row){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到数据";
            $this -> return_msg = array();
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $row;
            $this->display();
        }
    }

    /**
	 * 更新相册地址
	 */
	public function updateGalleryInfo(){
		$s_time = microtime();
		if (isset($this->filter['style_id']) &&!empty($this->filter['style_id'])) {
			$style_id = $this->filter['style_id'];
		}else{
			$this->return_sql = '';
			$this->error_msg = "缺少参数'style_id'";
			$this->return_msg = 0;
			$this->display();
		}
		if (isset($this->filter['image_place']) &&!empty($this->filter['image_place'])) {
			$image_place = $this->filter['image_place'];
		}else{
			$this->return_sql = '';
			$this->error_msg = "缺少参数'image_place'";
			$this->return_msg = 0;
			$this->display();
		}
		if (isset($this->filter['update_info']) &&!empty($this->filter['update_info'])) {
		}else{
			$this->return_sql = '';
			$this->error_msg = "缺少参数'update_info'";
			$this->return_msg = 0;
			$this->display();
		}
		$res = $this->db->autoExecute('app_style_gallery',  $this->filter['update_info'],'UPDATE','`style_id`='.$style_id." and `image_place`=".$image_place);
        // 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$res){
			$this -> error_msg = "更新失败";
			$this -> return_msg = false;
			$this->display();
		}else{
			$this -> return_sql = $res;
			$this -> return_msg = true;
			$this->display();
		}

	}

    /**
	 * 新增相册地址
	 */
	public function addGalleryInfo(){
		$s_time = microtime();
		$res = $this->db->autoExecute('app_style_gallery',  $this->filter['add_info'],'INSERT');

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$res){
			$this -> error_msg = "更新失败";
			$this -> return_msg = false;
			$this->display();
		}else{
			$this -> return_sql = $res;
			$this -> return_msg = $this->db->insertId();
			$this->display();
		}

	}
    /**
	 * 删除相册地址
	 */
	public function delGalleryInfo(){
		$s_time = microtime();
        if(empty($this->filter['g_id'])){
            $this -> error = 1;
			$this -> error_msg = "参数不全";
			$this -> return_msg = array();
			$this->display();
        }
        $sql = "delete from `app_style_gallery` where `g_id` in ({$this->filter['g_id']})";
		$res = $this->db->query($sql);

		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$res){
            $this -> error = 1;
			$this -> error_msg = "删除失败";
			$this -> return_msg = false;
			$this->display();
		}else{
            $this -> error = 0;
			$this -> return_sql = $res;
			$this -> return_msg = 1;
			$this->display();
		}

	}

    	/**
    * 查询工厂信息
    * @param $style_sn ,$xiangkou
    * @return json
    */
	public function GetStyleXiangKouByWhere()
	{
		$s_time = microtime();

        $where = " WHERE 1";
        if(isset($this->filter['style_sn']) && !empty($this->filter['style_sn'])){
             $style_sn=trim($this->filter['style_sn']);
             $where .= " AND `style_sn` = '".$style_sn."'";
        }else{
            $this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "请填写款号";
			$this -> return_msg = array();
			$this->display();
        }
        $xiangkou = '';
        if(isset($this->filter['xiangkou']) && !empty($this->filter['xiangkou'])){
             $xiangkou=trim($this->filter['xiangkou']);
             //$where .= " AND `xiangkou` = '".$xiangkou."'";
        }

        $res_data = array();
        //查询商品详情
        $sql = "select * from `rel_style_factory` " .$where." ;";
        $row = $this->db->getAll($sql);
        if(empty($row)){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "此款没有设置工厂数据";
            $this -> return_msg = array();
            $this->display();
        }
        $is_mark = false;
        //找对应镶口的
        foreach ($row as $val){
            if($val['xiangkou'] == $xiangkou){
                $res_data = $val;
                $is_mark = true;
                break;
            }
        }

        //没有对应镶口的找默认工厂的
        if(!$is_mark){
             foreach ($row as $val){
                if($val['is_def'] ==1){
                    $res_data = $val;
                    $is_mark = true;
                    break;
                }
            }
        }


		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$res_data){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "此款没有设置工厂数据";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = $res_data;
			$this->display();
		}
	}

	function cutFingerInfo($data){
        foreach ($data as $key=>$val){
            if(empty($val)){
                continue;
            }

            $is_search = $this->checkString('-', $val);
            $new_arr = array();
            if($is_search){
                $tmp = explode('-', $val);
                $min = intval($tmp[0]);
                $max = intval($tmp[1]);
                if($min == $max) {
                     $new_arr[] = $min;
                     $new_arr[] = $min+0.5;
                }else{
                    for($i=$min;$i<=$max;$i++){
                        $new_arr[] = $i;
                        $new_arr[] = $i+0.5;
                    }
                }
            }else{
                 $new_arr[] = $val;
            }
            $data[$key]=$new_arr;
        }

//		//如 6-9 原来是 6 7 8 9  现在要 6 6.5 7 7.5 8 8.5 9 9.5
//		$new_data = array();
//		foreach($data[0] as $val){
//			$new_data[]= $val;
//			$new_data[]= intval($val)+0.5;
//		}

        return $data;
    }
	function getStyleNameListByStyleSn()
	{
		$ids=isset($this->filter['ids'])?trim($this->filter['ids']):'';
		$where='';
		if($ids)
		{
			$ids="'".implode("','",$ids);
			$where.=" style_sn in($ids) and";
		}
        $sql = "select style_sn,style_name from base_style_info";
		//file_put_contents('./ruir.txt',$sql."\n",FILE_APPEND);
		if($where)
		{
			$where=rtrim($where,'and');
			$sql.=$where;
		}
		$row = $this->db->getAll($sql);
		if($row){
            $this -> error = 0;
			$this -> return_msg = $row;
			$this->display();
		}else{
            $this -> error = 1;
			$this -> return_sql = $sql;
			$this -> return_msg =array();
			$this->display();
		}

	}

    //天生一对款
	function getStyleTsydPriceList()
	{

        $sql = "select `id`, `style_sn`, `style_name`, `work`, `xiangkou_min`, `xiangkou_max`, `k_weight`, `pt_weight`, `carat`, `k_price`, `pt_price`,`jumpto`, `pic`, `group_sn` from `app_style_tsyd_price`";

		$row = $this->db->getAll($sql);
		if($row){
            $this -> error = 0;
			$this -> return_msg = $row;
			$this->display();
		}else{
            $this -> error = 1;
			$this -> return_sql = $sql;
			$this -> return_msg =array();
			$this->display();
		}

	}

//检查字符串是否存在
 function checkString($search,$string) {
    $pos = strpos($string, $search);
    if($pos == FALSE){
        return FALSE;
    }else{
        return TRUE;
    }
}
    /*------------------------------------------------------ */
	//-- 返回内容
	//-- by col
	/*------------------------------------------------------ */
	public function display()
	{
		$res = array("error" => intval($this -> error), "error_msg" => $this -> error_msg, "return_msg" => $this -> return_msg, "return_sql" => $this -> return_sql);
		die (json_encode($res));
	}

	/*------------------------------------------------------ */
	//-- 记录日志信息
	//-- by haibo
	/*------------------------------------------------------ */
	public function recordLog($api, $response_time, $str)
	{
        define('ROOT_LOG_PATH',str_replace('api/api.php', '', str_replace('\\', '/', __FILE__)));
		if (!file_exists(ROOT_LOG_PATH . 'logs/api_logs'))
		{
			mkdir(ROOT_LOG_PATH . 'logs/api_logs', 0777);
			chmod(ROOT_LOG_PATH . 'logs/api_logs', 0777);
		}
		$content = $api."||".$response_time."||".$str."||".date('Y-m-d H:i:s')."\n";
		$file_path =  ROOT_LOG_PATH . 'logs/api_logs/'.date('Y')."_".date('m')."_".date('d')."_api_log.txt";
		file_put_contents($file_path, $content, FILE_APPEND );
	}

	public function getStyleGalleryList() {
		$sql = "SELECT * FROM `app_style_gallery` WHERE 1 ";

		if (isset($this->filter['style_id'])) {
			$sql .= " AND style_id = {$this->filter['style_id']}";
		}

		if (isset($this->filter['style_sn'])) {
			$style_sn = trim($this->filter['style_sn']);
			$sql .= " AND style_sn = '{$style_sn}'";
		}

		//$sql .= " ORDER BY (case when image_place is null or image_place = 0 then 999 else image_place end) ASC, img_sort ASC";
// 		file_put_contents('e:/8.sql',$sql);
		$data = $this->db->getAll($sql);

        $this->error = 0;
		$this->return_msg = $data ? $data : array();
		$this->display();
	}

	public function getStyleAndFactories() {
		if (!isset($this->filter['style_sn'])) {
			$this->error = 1;
			$this->return_msg = array();
			$this->display();
		}

		$style_sn = trim($this->filter['style_sn']);
		if (empty($style_sn)) {
			$this->error = 1;
			$this->return_msg = array();
			$this->display();
		}

		$sql = "SELECT s.style_sn, f.factory_id, f.factory_sn FROM `base_style_info` s left join `rel_style_factory` f on f.style_sn = s.style_sn and f.is_cancel = 1
		where s.style_sn = '{$style_sn}' and s.check_status = 3 order by f.is_factory desc";
		$data = $this->db->getAll($sql);

        $factory_id_list = implode(',',array_column($data, 'factory_id'));
        if($data){
            $relation_factory_sql = "select 473 as id union SELECT f.id FROM `app_processor_info` f INNER JOIN `app_processor_group` g on g.supplier_id = f.id
                where g.group_id in (SELECT group_id FROM app_processor_group where supplier_id in ({$factory_id_list})) AND f.id NOT IN({$factory_id_list})";

            $relation_factory_data = DB::cn(14)->db()->query($relation_factory_sql)->fetchAll();

            if($relation_factory_data){
                foreach($relation_factory_data as $relation_factory)
                    $data[] = [
                        'style_sn' => $style_sn,
                        'factory_id' => $relation_factory['id'],
                        'factory_sn' => ''
                    ];
            }
        }


		$this->error = 0;
		$this->return_msg = $data ? $data : array();
		$this->display();
	}

	public function getValidStyleFactoryList() {
		$sql = 'select style_sn, xiangkou from `rel_style_factory` where `is_cancel` = 1 ';
		if(isset($this->filter['factory_id'])) {
			$sql .= " AND `factory_id` = {$this->filter['factory_id']} ";
		}
		if(isset($this->filter['factory_sn'])) {
			$sql .= " AND `factory_sn` = '{$this->filter['factory_sn']}' ";
		}
		// ensure base_style_info is valid.
		$sql .= ' AND exists(select 1 from `base_style_info` where style_sn = `rel_style_factory`.style_sn and check_status in (1,2,3))';
		$sql .= ' ORDER BY `is_factory` DESC';

		$data = $this->db->getAll($sql);

		$this->error = 0;
		$this->return_msg = $data ? $data : array();
		$this->display();
	}
	
	public function GetCatTypes() {
	    $sql = 'select cat_type_id, cat_type_name from `app_cat_type`';
	    $data = $this->db->getAll($sql);
	    
	    $this->error = 0;
	    $this->return_msg = $data ? $data : array();
	    $this->display();
	}
	
	public function GetProductTypes() {
		$sql = 'select product_type_id, product_type_name from `app_product_type`';
	    $data = $this->db->getAll($sql);
	    
	    $this->error = 0;
	    $this->return_msg = $data ? $data : array();
	    $this->display();
	}
	
	public function updateStyleInfoById(){
	    if(empty($this->filter['data'])){
	        $this->error = 1;
	        $this->error_msg = "参数错误，data不能为空";
	        $this->display();
	    }else {
	        $data = $this->filter['data']; 
	    }
	    if(empty($this->filter['style_id']) && empty($this->filter['style_sn'])){
	        $this->error = 1;
	        $this->error_msg = "参数错误，style_id或style_sn至少填写一个";
	        $this->display();
	    }else {
	        if(!empty($this->filter['style_id'])){
	             if(is_array($this->filter['style_id'])){
	                 $where = "style_id in(".implode(',',$this->filter['style_id']).")";
	             }else{
	                 $where = "style_id =".$this->filter['style_id'];	                 
	             }
	        }else{
	            if(is_array($this->filter['style_sn'])){
	                $where = "style_sn in('".implode("','",$this->filter['style_sn'])."')";
	            }else{
	                $where = "style_sn ='".$this->filter['style_sn']."'";
	            }
	        }
	    }
	    $res = $this->db->autoExecute('base_style_info', $data, 'UPDATE', $where);
	     
	    if($res !== false){
	        $this -> error = 1;
	        $this -> return_sql = $res;
	        $this -> error_msg = "操作成功";
	        $this -> return_msg = array();
	        $this->display();
	    }else{
	        $this -> error = 1;
	        $this -> return_sql = $res;
	        $this -> error_msg = "操作失败";
	        $this->display();
	    }
	    
	}
}
?>
