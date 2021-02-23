<?php
/**
 * This contains the Retrieval API .
 *
 */
 error_reporting(E_ALL);
class api
{
    private  $db = null;
    private  $error_msg = '';
    private  $return_msg = '';
    private  $return_sql = '';
    private  $filter = array();
    private static $configList = [
        'carat' =>[
            '0.00~0.10'=>'10分以下',
            '0.10~0.20'=>'10分~20分',
            '0.20~0.30'=>'20分~30分',
            '0.30~0.40'=>'30分~40分',
            '0.40~0.50'=>'40分~50分',
            '0.50~0.60'=>'50分~60分',
            '0.60~0.70'=>'60分~70分',
            '0.70~0.80'=>'70分~80分',
            '0.80~0.90'=>'80分~90分',
            '0.90~1.00'=>'90分~100分',
            '1.00~1.50'=>'100分~150分',
            '1.50~100.00'=>'150分以上',
        ],
        'store_lz_jijialv'=>[
            'HRD-D'=>[2.1 , 2.1 , 2.2 , 1.7 , 1.7 , 1.5 , 1.4 , 1.4 , 1.4 , 1.4, 1.3, 1.18],
            'GIA'=>[2.1 , 2.1 , 2.1 , 1.6 , 1.6 , 1.5 , 1.5 , 1.5 , 1.5 , 1.5 , 1.4, 1.2],
            'HRD'=>[2.1 , 2.1 , 2.2 , 1.7 , 1.7 , 1.5 , 1.4 , 1.4 , 1.4 , 1.4, 1.3, 1.18],
            'IGI'=>[2.1 , 2.1 , 2.1 , 2.1 , 2.1 , 1.643 , 1.546 , 1.546 , 1.546 , 1.546 , 1.457, 1.2],
            'DIA'=>[2.1 , 2.1 , 2.1 , 2.1 , 2.1 , 1.643 , 1.546 , 1.546 , 1.546 , 1.546 , 1.457, 1.2],
            'EGL'=>[2.1 , 2.1 , 2.1 , 2.1 , 2.1 , 1.643 , 1.546 , 1.546 , 1.546 , 1.546 , 1.457, 1.2],
            'NGTC'=>[2.1 , 2.1 , 2.1 , 2.1 , 2.1 , 1.643 , 1.546 , 1.546 , 1.546 , 1.546 , 1.457, 1.2],
            'NGGC'=>[2.1 , 2.1 , 2.1 , 2.1 , 2.1 , 1.643 , 1.546 , 1.546 , 1.546 , 1.546 , 1.457, 1.2],
            'HRD-S'=>[2.1 , 2.1 , 2.2 , 1.7 , 1.7 , 1.5 , 1.4 , 1.4 , 1.4 , 1.4, 1.3, 1.18],
            'NGSTC'=>[2.1 , 2.1 , 2.1 , 2.1 , 2.1 , 1.643 , 1.546 , 1.546 , 1.546 , 1.546 , 1.457, 1.2],
            'default'=>'2.1',
            ],
             
        'store_lz_moren_jijialv'=>[
            'HRD-D'=>[2.5, 2.5, 2.5, 2.2, 1.8, 1.65, 1.65, 1.65, 1.6, 1.6, 1.55, 1.25],
            'GIA'=>[1.95 , 1.95 , 1.95 , 2.2 , 2.2 , 1.8 , 1.8 , 1.8 , 1.8 , 1.8 , 1.6, 1.25],
            'HRD'=>[1.95 , 1.95 , 1.95 , 2 , 2 , 1.7 , 1.6 , 1.6 , 1.6 , 1.6, 1.55, 1.25],
            'IGI'=>[1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95, 1.95],
            'DIA'=>[1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95, 1.95],
            'EGL'=>[1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95, 1.95],
            'NGTC'=>[1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95, 1.95],
            'NGGC'=>[1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95, 1.95],
            'HRD-S'=>[1.95 , 1.95 , 1.95 , 2 , 2 , 1.7 , 1.6 , 1.6 , 1.6 , 1.6, 1.55, 1.25],
            'NGSTC'=>[1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95, 1.95],
            'default'=>'2',
            ],

       'store_lz_qihuo_moren_jijialv'=>[
            'HRD-D'=>[2.5, 2.5, 2.5, 2.2, 1.8, 1.75, 1.8, 1.8, 1.6, 1.6, 1.55, 1.25],
            'GIA'=>[1.95 , 1.95 , 1.95 , 2.2 , 2.2 , 1.8 , 1.8 , 1.8 , 1.8 , 1.8 , 1.6, 1.25],
            'HRD'=>[1.95 , 1.95 , 1.95 , 2 , 2 , 1.7 , 1.6 , 1.6 , 1.6 , 1.6, 1.55, 1.25],
            'IGI'=>[1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95, 1.95],
            'DIA'=>[1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95, 1.95],
            'EGL'=>[1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95, 1.95],
            'NGTC'=>[1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95, 1.95],
            'NGGC'=>[1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95, 1.95],
            'HRD-S'=>[1.95 , 1.95 , 1.95 , 2 , 2 , 1.7 , 1.6 , 1.6 , 1.6 , 1.6, 1.55, 1.25],
            'NGSTC'=>[1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95 , 1.95, 1.95],
            'default'=>'2',
        ]
    ];
    public function __construct($_filter)
    {
        global $config;
        $this->db= new KELA_API_DB($config);
		$this->filter = $_filter;
    }
    /**
     * 切换API数据库连接
     * @param int $strConn
     * @return boolean
     * gaopeng
     */
    protected function changeDB($strConn){
        global $xx;
        $xxx= $xx->GetSection('DbConfig'.$strConn);
        if(!$xxx){
            return false;
        }
        $db_host	=	$xxx['db_host'];
        $db_name	=	$xxx['db_name'];
        $db_user	=	$xxx['db_user'];
        $db_pass	=	$xxx['db_pwd'];
        
        $config = array();
        $config['db_type'] = 'mysql';
        $config['db_host'] = $db_host;
        $config['db_port'] = 3306;
        $config['db_name'] = $db_name;
        $config['db_user'] = $db_user;
        $config['db_pwd'] = $db_pass;
        
        $this->db= new KELA_API_DB($config);
    }

    /**
     * 查询裸钻列表分页信息
     * @param *
     * @return json
     */
	public function GetDiamondList()
	{
	    $this->changeDB(52);//切换到52号连接(只读服务器)
	    
		$s_time = microtime();
		//$this -> filter["page"] = 3;  //当前页 
		$page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
		$pageSize = intval($this -> filter["pageSize"]) > 0 ? intval($this -> filter["pageSize"]) : 15;
        $goods_sn=$this->filter['goods_sn'];//货号
		$no_goods_id=$this->filter['no_goods_id'];//排除商品
		$goods_name=$this->filter['goods_name'];//商品名称
		$from_ad=$this->filter['from_ad'];//来源
		$good_type=$this->filter['good_type'];//货品类型
		$carat_min=floatval($this->filter['carat_min']);//最小石重
		$carat_max=floatval($this->filter['carat_max']);//最大石重
		$price_min=floatval($this->filter['price_min']);//最小价格
		$price_max=floatval($this->filter['price_max']);//最大价格
		$clarity=$this->filter['clarity'];//净度
		$cut=$this->filter['cut'];//切工
		$color=$this->filter['color'];//颜色
		$shape=$this->filter['shape'];//形状
		$symmetry=$this->filter['symmetry'];//对称
		$polish=$this->filter['polish'];//抛光
		$fluorescence=$this->filter['fluorescence'];//荧光
		$cert=$this->filter['cert'];//证书号类型
		$cert_id=$this->filter['cert_id'];//证书号
		$warehouse=$this->filter['warehouse'];//库房
		$hrd_s_warehouse=$this->filter['hrd_s_warehouse'];//展厅只能看直营店星耀钻
		$no_warehouse=$this->filter['no_warehouse'];//排除库房
		$gm=$this->filter['gm'];//星耀证书
		$kuan_sn=$this->filter['kuan_sn'];//天生一对
		$gemx_zhengshu=$this->filter['gemx_zhengshu'];//gexm证书号
		$status=$this->filter['status'];//状态
		$is_active=$this->filter['is_active'];//活动状态
		$s_carats_tsyd1=$this->filter['s_carats_tsyd1'];//天生一对钻1重小
		$e_carats_tsyd1=$this->filter['e_carats_tsyd1'];//天生一对钻1重大
		$s_carats_tsyd2=$this->filter['s_carats_tsyd2'];//天生一对钻2重小
		$e_carats_tsyd2=$this->filter['e_carats_tsyd2'];//天生一对钻2重大
		$zdj=$this->filter['zdj'];//价格排序
		$stonesort=$this->filter['stonesort'];//石重排序
		$yansesort=$this->filter['yansesort'];//颜色排序
		$jdsort=$this->filter['jdsort'];//净度排序
		$not_from_ad=$this->filter['not_from_ad'];//来源
		$pf_price_min=floatval($this->filter['pf_price_min']);//最小价格
		$pf_price_max=floatval($this->filter['pf_price_max']);//最大价格
		$include_img=$this->filter['include_img'];//是否包含图片
		$where = " ";
		if(!empty($goods_sn))
		{
			$where .= " and d.`goods_sn`='".$goods_sn."'";
        }
		if(!empty($goods_name))
		{
			$where .= " and d.`goods_name`='".$goods_name."'";
		}
		if(!empty($from_ad))
		{
			$where .= " and d.`from_ad`=".$from_ad;
		}
		if(!empty($not_from_ad)){
		    if(is_array($not_from_ad)){
		        $where .= " and d.`from_ad` not in(".implode(',',$not_from_ad).")";
		    }else{
		        $where .= " and d.`from_ad`<>".$not_from_ad;
		    }
		}
		if(!empty($good_type))
		{
			$where .= " and d.`good_type`=".$good_type;
		}
		if(!empty($carat_min))
		{
			$where .= " and d.`carat`>=".$carat_min;
		}
		if(!empty($carat_max))
		{
			$where .= " and d.`carat`<=".$carat_max;
		}
		if(!empty($price_min))
		{
			$where .= " and d.`shop_price`>=".$price_min;
		}
		if(!empty($price_max))
		{
			$where .= " and d.`shop_price`<=".$price_max;
		}
		if(!empty($pf_price_min))
		{
			$where .= " and d.`pifajia`>=".$pf_price_min;
		}
		if(!empty($pf_price_max))
		{
			$where .= " and d.`pifajia`<=".$pf_price_max;
		}
		if(!empty($clarity))
		{
			$where .= " and d.`clarity` in('".implode ("','",$clarity)."')";
		}
		if(!empty($cut))
		{
			$where .= " and d.`cut` in('".implode ("','",$cut)."')";
		}
		if(!empty($color))
		{
			$where .= " and d.`color` in('".implode ("','",$color)."')";
		}
		if(!empty($shape))
		{
			$where .= " and d.`shape` in('".implode ("','",$shape)."')";
		}
		if(!empty($symmetry))
		{
			$where .= " and d.`symmetry` in('".implode ("','",$symmetry)."')";
		}
		if(!empty($polish))
		{
			$where .= " and d.`polish` in('".implode ("','",$polish)."')";
		}
		if(!empty($fluorescence))
		{
			$where .= " and d.`fluorescence` in('".implode ("','",$fluorescence)."')";
		}
		if(!empty($cert))
		{
			$where .= " and d.`cert` in('".implode ("','",$cert)."')";
		}
		$join_table=" ";
        $join_where=" 1 ";
		if(!empty($cert_id))
		{   
		    if(is_array($cert_id)){
		        $cert_id = implode ("','",$cert_id);
		    }
			$where .= " and d.`cert_id` in('".$cert_id."')";
		}
		if(!empty($warehouse))
		{
			$where .= " and d.`warehouse` in('".implode("','",$warehouse)."')";
		}
		/*
		if(!empty($hrd_s_warehouse))
		{
			$where .= " and if(d.`warehouse` in('".implode("','",$hrd_s_warehouse)."'), d.cert, 'HRD-S') = 'HRD-S'";
		}	
		*/	
		if(!empty($no_warehouse))
		{
			$where .= " and d.`warehouse` not in('".implode("','",$no_warehouse)."')";
		}
		if(!empty($no_goods_id))
		{
			$where .= " and d.`goods_sn` not in('".implode("','",$no_goods_id)."')";
		}
		if(!empty($gm))
		{
			$where .= " and (d.`gemx_zhengshu` !='' or d.cert ='HRD-S')";
		}
		if(!empty($kuan_sn))
		{
		    if($kuan_sn=="no_tsyd"){
			    $where .= " and (d.`kuan_sn` ='' or d.`kuan_sn` is null)";
		    }else{
		        $where .= " and d.`kuan_sn` !=''";
		    }
		}
        //天生一对
        //if(!empty($kuan_sn)){
        $join_table=" ";
        $join_where=" 1 ";
        if(!empty($s_carats_tsyd1) || !empty($e_carats_tsyd1) || !empty($s_carats_tsyd2) || !empty($e_carats_tsyd2)){
            $where.= " and d.`kuan_sn`!='' and d.`cert`='HRD-D'";
                //$where.="AND d.kuan_sn!='' ";
                if(!isset($s_carats_tsyd1) &&
                    !isset($e_carats_tsyd1) &&
                    !isset($s_carats_tsyd2) &&
                    !isset($e_carats_tsyd2) ){

                }else{
					$join_table=" ,`diamond_info` as e";
                    $join_where=" d.kuan_sn=e.kuan_sn  AND e.goods_id != d.goods_id ";
                    $tsyd_sql=$this->getTsydSQL($this->filter);
					//if(!empty($tsyd_sql)){
						$where.=$tsyd_sql;
					//}
                }
        }
		if(!empty($gemx_zhengshu))
		{
			$where .= " and d.`gemx_zhengshu` in('".$gemx_zhengshu."')";
		}
		if(!empty($status))
		{
			$where .= " and d.`status` in(".$status.")";
		}
		if(!empty($is_active))
		{
			$where .= " and d.`is_active` in(".$is_active.")";
		}

		if(!empty($include_img) && $include_img==1)
		{
			$where .= " and (d.img like 'https://diamonds.kirangems.com/GemKOnline/DiaSearch/appVideo.jsp%')";
		}

		
		$_order_by_str = "";
		if($zdj){
			$_order_by_str .= "d.shop_price ".$zdj.",";
		}
		if($stonesort){
			$_order_by_str .= "d.carat ".$stonesort.",";
		}
		if($yansesort){
			$_order_by_str .= "d.color ".$yansesort.",";
		}
		if($jdsort){
			$_order_by_str .= "d.clarity ".$jdsort.",";
		}
		if($_order_by_str){
			$_order_by_str = " ORDER BY ".rtrim($_order_by_str,',')." ";
		} else if (!empty($pf_price_min) || !empty($pf_price_max))  {
			$_order_by_str = " ORDER BY pifajia asc ";
		}
		
		//关闭双十一活动，加false;
		if(false && isset($this->filter['ssy_active']) && (empty($kuan_sn) || $kuan_sn=="no_tsyd")){	
		    //双十一特价钻 begin,仅当传参ssy开启双十一特价钻搜索识别  by gaopeng
		    if($this->filter['ssy_active']==1){
		        $where .=" and d.cert_id in(select cert_id from diamond_ssy_tejia)";
		    }else{
		        $where .=" and d.cert_id not in(select cert_id from diamond_ssy_tejia)";
		    }
		    $sql   = "SELECT COUNT(*) FROM `diamond_info` as d $join_table where $join_where ".$where;
		    $record_count   =  $this -> db ->getOne($sql);
		    $page_count     = $record_count > 0 ? ceil($record_count / $pageSize) : 1;
		    $page = $page > $page_count ? $page_count : $page;
		    
		    $isFirst = $page > 1 ? false : true;
		    $isLast = $page < $page_count ? false : true;
		    $start = ($page == 0) ? 1 : ($page - 1) * $pageSize + 1;
    		$join_table .=" left join `diamond_ssy_tejia` s on s.cert_id=d.cert_id";
    		$sql = "select d.`goods_id`,d.`goods_sn`,d.`goods_name`,d.`goods_number`,d.`from_ad`,d.`good_type`,d.`market_price`,d.`shop_price`,s.`special_price`,d.`member_price`,d.`chengben_jia`,d.`carat`,d.`clarity`,d.`cut`,d.`color`,d.`shape`,d.`depth_lv`,d.`table_lv`,d.`symmetry`,d.`polish`,d.`fluorescence`,d.`warehouse`,d.`cert`,d.`cert_id`,d.`gemx_zhengshu`,d.`status`,d.`add_time`,d.`is_active`,d.`kuan_sn`, d.pifajia,d.img from `diamond_info` as d $join_table WHERE $join_where ".$where.$_order_by_str." LIMIT " . ($page - 1) * $pageSize . ",$pageSize";
    		//双十一特价钻 end
		}else{		    
		    //默认搜索
		    $sql   = "SELECT COUNT(*) FROM `diamond_info` as d $join_table where $join_where ".$where;
		    $record_count   =  $this -> db ->getOne($sql);
		    $page_count     = $record_count > 0 ? ceil($record_count / $pageSize) : 1;
		    $page = $page > $page_count ? $page_count : $page;
		    
		    $isFirst = $page > 1 ? false : true;
		    $isLast = $page < $page_count ? false : true;
		    $start = ($page == 0) ? 1 : ($page - 1) * $pageSize + 1;
		    //var_dump($start,123);die;
		    $sql = "select d.`goods_id`,d.`goods_sn`,d.`goods_name`,d.`goods_number`,d.`from_ad`,d.`good_type`,d.`market_price`,d.`shop_price`,d.`member_price`,d.`chengben_jia`,d.`carat`,d.`clarity`,d.`cut`,d.`color`,d.`shape`,d.`depth_lv`,d.`table_lv`,d.`symmetry`,d.`polish`,d.`fluorescence`,d.`warehouse`,d.`cert`,d.`cert_id`,d.`gemx_zhengshu`,d.`status`,d.`add_time`,d.`is_active`,d.`kuan_sn`,d.pifajia,d.img from `diamond_info` as d $join_table WHERE $join_where ".$where.$_order_by_str." LIMIT " . ($page - 1) * $pageSize . ",$pageSize";
		}
		$res = $this -> db -> getAll($sql);
		//file_put_contents('diamond.log', $sql);
		$content = array("page" => $page, "pageSize" => $pageSize, "recordCount" => $record_count, "data" => $res,'pageCount'=>$page_count,'isFirst'=>$isFirst,'isLast'=>$isLast,'start'=>$start,'sql'=>$sql);
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
		if(!$res)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此裸钻";
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
     * 获取裸钻检索条件参数
     * @param *
     * @return json
     */
    public function getdiamondindex()
    {
        $keys = isset($this -> filter['keys']) && !empty($this -> filter['keys'])?$this -> filter['keys']:'';//属性
        //切工(Cut) ：完美 EX   非常好 VG   好 G   一般 Fair
        $cut_arr = array('EX', 'VG', 'G');
        //$cut_arr = array('EX', 'VG', 'G', 'Fair');
        //抛光(Polish)     完美 EX   非常好 VG   好 G   一般 Fair
        $polish_arr = array('EX', 'VG', 'G');
        //$polish_arr = array('EX', 'VG', 'G', 'Fair');
        //对称(Symmetry)   完美 EX   非常好 VG   好 G   一般 Fair
        $symmetry_arr = array('EX', 'VG', 'G');
        //$symmetry_arr = array('EX', 'VG', 'G', 'Fair');
        //荧光(Fluorescence): 无 N   轻微 F   中度 M   强烈 S
        $fluorescence_arr = array('N', 'F', 'M', 'S','SLT');
        //颜色(Color): D  完全无色   E 无色   F 几乎无色   G   H   I 接近无色   J
        $color_arr = array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N');
        //$color_arr = array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'H', 'D-E', 'E-F', 'F-G', 'G-H', 'H-I', 'I-J', 'J-K');
        //$color_arr = array("N","M","L","K-L","K","J-K","J","I-J","I","H-I","H","H+","G-H","G","F-G","F","E-F","E","D-E","D","黄","蓝","粉","橙","绿","红","香槟","格雷恩","紫","混色","蓝紫色","黑","变色","其他","白色","金色");
        //净度(Clarity) FL 完全洁净  IF 内部洁净  VVS1 极微瑕  VVS2  VS1 微瑕  VS2  SI1 小瑕  SI2
        $clarity_arr = array('IF', 'VVS1', 'VVS2', 'VS1', 'VS2', 'SI1', 'SI2');
        //$clarity_arr = array('FL', 'IF', 'VVS1', 'VVS2', 'VS1', 'VS2', 'SI1', 'SI2');
        //$clarity_arr = array("不分级","P","P1","I","I1","I2","SI","SI1","SI2","VS","VS1","VS2","VVS","VVS1","VVS2","IF","FL");
        //形状(Shape): 圆形   公主方形   祖母绿形   橄榄形   椭圆形   水滴形   心形  坐垫形   辐射形   方形辐射形   方形祖母绿   三角形
        $shape_all = array(1 => '圆形', 2 => '公主方形', 3 => '祖母绿形', 4 => '橄榄形', 5 => '椭圆形', 6 => '水滴形', 7 => '心形', 8 => '坐垫形', 9 => '辐射形', 10 => '方形辐射形', 11 => '方形祖母绿', 12 => '三角形',13=>'戒指托',14=>'异形',15=>'梨形',16=>'阿斯切',17 => '马眼', 18 => '长方形', 19 => '雷迪恩');
        $shape_arr = array(1 => '圆形', 2 => '公主方形', 3 => '祖母绿形', 4 => '橄榄形', 5 => '椭圆形',  7 => '心形', 8 => '坐垫形',15=>'梨形', 19 => '雷迪恩');
        $shape_val = array('圆形' => 1,'公主方形' => 2,'祖母绿形' => 3,'橄榄形' => 4,'椭圆形' => 5,'心形' => 7,'坐垫形' => 8,'梨形' => 15, '雷迪恩' => 19);
        //证书类型 
        $cert_arr = array('GIA','HRD-D','HRD-S');
        $cert_jiajialv = array('GIA','HRD-S');//裸钻加价率添加裸钻类型
        //array('HRD-D','GIA','HRD','IGI','DIA','EGL','NGTC','NGGC','HRD-S','NGSTC');
        $fromad_arr = array(1=>'kela',2=>'fiveonezuan',3=>'venus',4=>'dharam',5=>'diamondbyhk',6=>'diarough',7=>'emd',8=>'gd',9=>'jb',10=>'kapu',11=>'kgk',12=>'hy',13=>'leo',14=>'kiran',15=>'vir',16=>'karp',17=>'enjoy',18=>'changning', 19=>'kb',20=>'kg','21'=>'bluestar','22'=>'fulong','23'=>'kbgems','24'=>'sheelgems','25'=>'cdinesh','29'=>'SLK');
        $xilie_arr = array('星耀钻石', 'GIA钻石', '天生一对');
        $xilie_val = array('星耀钻石' => 'gemx' ,'GIA钻石'=>'gia', '天生一对'=>'tsyd');
        $data = array('cut' => $cut_arr,'polish' => $polish_arr,'symmetry' => $symmetry_arr,'fluorescence' => $fluorescence_arr,'color' => $color_arr,'clarity' => $clarity_arr,'shape' => $shape_arr, 'shape_val'=>$shape_val, 'cert' => $cert_arr,'cert_jiajialv'=>$cert_jiajialv, 'fromad' => $fromad_arr, 'xilie' => $xilie_arr, 'xilie_val'=> $xilie_val, 'shape_all'=>$shape_all);
        if(!empty($keys)){
            $key_data = array();
            foreach ($keys as $key) 
                {
                    $key_data[$key] = $data[$key];
                }
            $this -> error = 0;
            $this -> return_sql = "";
            $this -> return_msg = $key_data;
            $this->display();
        }else{
            $this -> error = 0;
            $this -> return_sql = "";
            $this -> return_msg = $data;
            $this->display();
        }
    }

    /**
     * 查询裸钻列表分页信息
     * @param *
     * @return json
     */
    public function getwitdiamondlist()
    {
        $this->changeDB(52);
        $page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
        $pageSize = intval($this -> filter["pageSize"]) > 0 ? intval($this -> filter["pageSize"]) : 10;
        $useCache = !empty($this -> filter['useCache'])?$this -> filter['useCache']:false;
        $recordCount = !empty($this -> filter['recordCount'])?$this -> filter['recordCount']:false;
        $company_id = !empty($this -> filter['company_id'])?$this -> filter['company_id']:0;
        $goods_sn = $this -> filter['goods_sn'];//货号
        $no_goods_id = $this -> filter['no_goods_id'];//排除商品
        $no_cert_id = $this-> filter['no_cert_id'];//排除裸钻
        $goods_name = $this -> filter['goods_name'];//商品名称
        $from_ad = $this -> filter['from_ad'];//来源
        $good_type = $this -> filter['good_type'];//货品类型
        $carat_min = floatval($this -> filter['carat_min']);//最小石重
        $carat_max = floatval($this -> filter['carat_max']);//最大石重
        //file_put_contents('diamond.log', $carat_max);
        $price_min = floatval($this -> filter['price_min']);//最小价格
        $price_max = floatval($this -> filter['price_max']);//最大价格
        $clarity = $this -> filter['clarity'];//净度
        $cut = $this -> filter['cut'];//切工
        $color = $this -> filter['color'];//颜色
        $shape = $this -> filter['shape'];//形状
        $symmetry = $this -> filter['symmetry'];//对称
        $polish = $this -> filter['polish'];//抛光
        $fluorescence = $this -> filter['fluorescence'];//荧光
        $cert = $this -> filter['cert'];//证书号类型
        $cert_id = $this -> filter['cert_id'];//证书号
        $warehouse = $this -> filter['warehouse'];//库房
        $hrd_s_warehouse = $this -> filter['hrd_s_warehouse'];//展厅只能看直营店星耀钻
        $no_warehouse = $this -> filter['no_warehouse'];//排除库房
        $xilie = $this -> filter['xilie'];//系列
        $gm = $this -> filter['gm'];//星耀证书
        $kuan_sn = $this -> filter['kuan_sn'];//天生一对
        $gemx_zhengshu = $this -> filter['gemx_zhengshu'];//gexm证书号
        $status = $this -> filter['status'];//状态
        $is_hot = $this -> filter['is_hot'];//热销
        $is_active = $this -> filter['is_active'];//活动状态
        $pricesort = $this -> filter['pricesort'];//价格排序
        $stonesort = $this -> filter['stonesort'];//石重排序
        $yansesort = $this -> filter['yansesort'];//颜色排序
        $claritysort = $this -> filter['claritysort'];//净度排序
        $pf_price_min = floatval($this -> filter['pf_price_min']);//最小批发价
        $pf_price_max = floatval($this -> filter['pf_price_max']);//最大批发价
        $s_carats_tsyd1 = $this -> filter['tsyd1_stone_min'];//天生一对钻1重小
        $e_carats_tsyd1 = $this -> filter['tsyd1_stone_max'];//天生一对钻1重大
        $s_carats_tsyd2 = $this -> filter['tsyd2_stone_min'];//天生一对钻2重小
        $e_carats_tsyd2 = $this -> filter['tsyd2_stone_max'];//天生一对钻2重大
        $not_from_ad=$this -> filter['not_from_ad'];//来源
        $include_img=$this -> filter['is_3dimage'];//是否包含图片
        $carats_tsyd2 = $this -> filter['carats_tsyd2'];//天生一对钻2
        $is_tsyd = $this -> filter['is_tsyd'];//对戒且为天生一对款
        //file_put_contents('diamond.log', $is_tsyd);
        $where = " AND d.`pifajia` <> 0";
        if(!empty($goods_sn))
        {
            $where .= " AND d.`goods_sn`='".$goods_sn."'";
        }
        if(!empty($goods_name))
        {
            $where .= " AND d.`goods_name`='".$goods_name."'";
        }
        if(!empty($from_ad))
        {
            $where .= " AND d.`from_ad`=".$from_ad;
        }
        if(!empty($not_from_ad)){
            if(is_array($not_from_ad)){
                $where .= " AND d.`from_ad` not in(".implode(',',$not_from_ad).")";
            }else{
                $where .= " AND d.`from_ad`<>".$not_from_ad;
            }
        }
        if(!empty($good_type))
        {
            //$where .= " AND d.`good_type`=".$good_type;
            $where .= " AND d.`good_type` in('".implode ("','",$good_type)."')";
        }
        if(!empty($carat_min) && empty($carats_tsyd2))
        //if(bccomp($carat_min, 0, 3) != -1)
        {
            $where .= " AND d.`carat`>=".$carat_min;
        }
        if(!empty($carat_max) && empty($carats_tsyd2))
        //if(bccomp($carat_max, 0, 3) != -1)
        {
            $where .= " AND d.`carat`<=".$carat_max;
        }
        if(!empty($price_min))
        {
            $where .= " AND d.`shop_price`>=".$price_min;
        }
        if(!empty($price_max))
        {
            $where .= " AND d.`shop_price`<=".$price_max;
        }
        if(!empty($pf_price_min))
        {
            $where .= " AND d.`pifajia`>=".$pf_price_min;
        }
        if(!empty($pf_price_max))
        {
            $where .= " AND d.`pifajia`<=".$pf_price_max;
        }
        if(!empty($clarity))
        {
            $where .= " AND d.`clarity` in('".implode ("','",$clarity)."')";
        }
        if(!empty($cut))
        {
            $where .= " AND d.`cut` in('".implode ("','",$cut)."')";
        }
        if(!empty($color))
        {
            $where .= " AND d.`color` in('".implode ("','",$color)."')";
        }
        if(!empty($shape))
        {
            $where .= " AND d.`shape` in('".implode ("','",$shape)."')";
        }
        if(!empty($symmetry))
        {
            $where .= " AND d.`symmetry` in('".implode ("','",$symmetry)."')";
        }
        if(!empty($polish))
        {
            $where .= " AND d.`polish` in('".implode ("','",$polish)."')";
        }
        if(!empty($fluorescence))
        {
            $where .= " AND d.`fluorescence` in('".implode ("','",$fluorescence)."')";
        }
        //天生一对
        $join_table=" ";
        $join_where=" 1 ";
        if(!empty($cert))
        {   
            $is_diamk = true;
            foreach ($cert as $certid) {
                if(!in_array($certid, array('GIA','HRD-D','HRD-S'))){
                    $is_diamk = false;
                }
            }
            if($is_diamk){
                $where .= " AND d.`cert` in('".implode ("','",$cert)."')";
                if(!empty($carats_tsyd2) && in_array('HRD-D', $cert)){
                    //$_where .= " OR (d.`kuan_sn`!='' AND d.`cert`='HRD-D')";
                    $where .=" AND d.`kuan_sn`!='' ";
                    $join_table=" ,`diamond_info` as e";
                    $join_where=" d.kuan_sn=e.kuan_sn  AND e.goods_id != d.goods_id ";

                    $tsyd2_sql=$this->getTsydCartSQL($this->filter);
                    $where.=$tsyd2_sql;
                }
            }else{
                $where .= " AND d.`cert` in('GIA','HRD-D','HRD-S')";
            }
        }else{
            if($is_tsyd === false){
                $where .= " AND d.`cert` in('GIA','HRD-S')";
            }else{
                $where .= " AND d.`cert` in('GIA','HRD-D','HRD-S')";
            }
        }

        if(!empty($cert_id))
        {   
            if(is_array($cert_id)){
                $cert_id = implode ("','",$cert_id);
            }
            $where .= " AND d.`cert_id` in('".$cert_id."')";
        }
        if(!empty($warehouse))
        {
            $where .= " AND d.`warehouse` in('".implode("','",$warehouse)."')";
        }
        if(!empty($hrd_s_warehouse))
        {
            $where .= " AND if(d.`warehouse` in('".implode("','",$hrd_s_warehouse)."'), d.cert, 'HRD-S') = 'HRD-S'";
        }       
        if(!empty($no_warehouse))
        {
            $where .= " AND d.`warehouse` not in('".implode("','",$no_warehouse)."')";
        }
        if(!empty($no_goods_id))
        {
            $where .= " AND d.`goods_sn` not in('".implode("','",$no_goods_id)."')";
        }
        if(!empty($no_cert_id))
        {
            $where .= " AND d.`cert_id` not in('".implode("','",$no_cert_id)."')";
        }
        if(!empty($include_img)){
            if(in_array(1,$include_img) && !in_array(2,$include_img))
            {
                $where .= " and (d.img like 'https://diamonds.kirangems.com/GemKOnline/DiaSearch/appVideo.jsp%' or d.img like 'https://diamanti.s3.amazonaws.com/images/diamond%')";
            }elseif(in_array(2,$include_img) && !in_array(1,$include_img))
            {
                $where .= " and d.img not like 'https://diamonds.kirangems.com/GemKOnline/DiaSearch/appVideo.jsp%' and d.img not like 'https://diamanti.s3.amazonaws.com/images/diamond%'";
            }else{}
        }
        //天生一对
        /*$join_table=" ";
        $join_where=" 1 ";
        if(!empty($xilie)){
            $_where = "";
            if(in_array('tsyd',$xilie)){
                if($kuan_sn=="no_tsyd"){
                    $_where .= " OR (d.`kuan_sn` ='' OR d.`kuan_sn` is null)";
                }else{
                    $_where .= " OR (d.`kuan_sn`!='' AND d.`cert`='HRD-D')";
                    $join_table=" ,`diamond_info` as e";
                    $join_where=" d.kuan_sn=e.kuan_sn  AND e.goods_id != d.goods_id ";
                    /*if(!empty($s_carats_tsyd1) || !empty($e_carats_tsyd1) || !empty($s_carats_tsyd2) || !empty($e_carats_tsyd2)){
                        if(!isset($s_carats_tsyd1) &&
                            !isset($e_carats_tsyd1) &&
                            !isset($s_carats_tsyd2) &&
                            !isset($e_carats_tsyd2) ){

                        }else{
                            $tsyd_sql=$this->getTsydSQL($this->filter);
                            file_put_contents('diamond.log', $tsyd_sql);
                            if(!empty($tsyd_sql)){
                                $where.=$tsyd_sql;
                            }
                        }
                    }
                    if(!empty($carats_tsyd2)){
                        $tsyd2_sql=$this->getTsydCartSQL($this->filter);
                        $where.=$tsyd2_sql;
                    }
                }
            }else{
                foreach ($xilie as $xl) {
                    if($xl == 'gemx')
                    {
                        $_where .= " OR (d.`gemx_zhengshu` !='' OR d.cert ='HRD-S')";
                    }

                    if($xl == 'gia')
                    {
                        $_where .= " OR (d.cert ='GIA')";
                    }
                    
                    //if($xl == 'tsyd')
                    //{
                    //    if($kuan_sn=="no_tsyd"){
                    //        $_where .= " OR (d.`kuan_sn` ='' OR d.`kuan_sn` is null)";
                    //    }else{
                    //        $_where .= " OR (d.`kuan_sn`!='' AND d.`cert`='HRD-D')";
                    //        $join_table=" ,`diamond_info` as e";
                    //        $join_where=" d.kuan_sn=e.kuan_sn  AND e.goods_id != d.goods_id ";
                    //    }
                    //}
                }
            }
            if($_where) $where .= " AND (".ltrim($_where, " OR ").")";
        }*/
        //天生一对
        //$join_table=" ";
        //$join_where=" 1 ";
        //if(!empty($tsyd1_stone_min) || !empty($tsyd1_stone_max) || !empty($tsyd2_stone_min) || !empty($tsyd2_stone_max)){
        //    $where.= " AND d.`kuan_sn`!='' AND d.`cert`='HRD-D'";
        //    if(!isset($tsyd1_stone_min) &&
        //        !isset($tsyd1_stone_max) &&
        //        !isset($tsyd2_stone_min) &&
        //        !isset($tsyd2_stone_max) ){
        //    }else{
        //        $join_table=" ,`diamond_info` as e";
        //        $join_where=" d.kuan_sn=e.kuan_sn  AND e.goods_id != d.goods_id ";
        //        $where.= $this->getTsydSQL($this->filter);
        //    }
        //}
        if(!empty($gemx_zhengshu))
        {
            $where .= " AND d.`gemx_zhengshu` in('".$gemx_zhengshu."')";
        }
        if(!empty($status))
        {
            $where .= " AND d.`status` in(".$status.")";
        }
        //if(!empty($is_hot))
        //{
        //    $where .= " AND d.`is_hot` in(".$is_hot.")";
        //}
        if(!empty($is_active))
        {
            $where .= " AND d.`is_active` in(".$is_active.")";
        }
        $_order_by = "";
        if($pricesort) $_order_by .= "d.shop_price ".$pricesort.",";
        if($stonesort) $_order_by .= "d.carat ".$stonesort.",";
        if($yansesort) $_order_by .= "d.color ".$yansesort.",";
        if($jdsort) $_order_by .= "d.clarity ".$jdsort.",";
        if($_order_by)
        {
            $_order_by = " ORDER BY ".rtrim($_order_by,',')." ";

        } 
        else if (!empty($pf_price_min) || !empty($pf_price_max))
        {
            $_order_by = " ORDER BY d.pifajia asc ";
        }else{
            $_order_by = " ORDER BY d.`shape`,d.`add_time` desc ";
        }
        if($is_hot){
            $sql = "select d.`goods_id`,d.`goods_sn`,d.`goods_name`,d.`goods_number`,d.`from_ad`,d.`good_type`,d.`market_price`,d.`shop_price`,d.`member_price`,d.`chengben_jia`,d.`carat`,d.`clarity`,d.`cut`,d.`color`,d.`shape`,d.`depth_lv`,d.`table_lv`,d.`symmetry`,d.`polish`,d.`fluorescence`,d.`warehouse`,d.`cert`,d.`cert_id`,d.`gemx_zhengshu`,d.`status`,d.`add_time`,d.`is_active`,d.`kuan_sn`,d.pifajia,d.img from `diamond_info` as d $join_table WHERE $join_where ".$where.$_order_by." limit 3,10";//".$page.",".$pageSize
            $data = $this->db->getAll($sql);
        }else{
            $sql = "select d.`goods_id`,d.`goods_sn`,d.`goods_name`,d.`goods_number`,d.`from_ad`,d.`good_type`,d.`market_price`,d.`shop_price`,d.`member_price`,d.`chengben_jia`,d.`carat`,d.`clarity`,d.`cut`,d.`color`,d.`shape`,d.`depth_lv`,d.`table_lv`,d.`symmetry`,d.`polish`,d.`fluorescence`,d.`warehouse`,d.`cert`,d.`cert_id`,d.`gemx_zhengshu`,d.`status`,d.`add_time`,d.`is_active`,d.`kuan_sn`,d.pifajia,d.img from `diamond_info` as d $join_table WHERE $join_where ".$where.$_order_by;
            $data = $this->db->getPageList($sql, array(), $page, $pageSize, $useCache,$recordCount);
        }
        //file_put_contents('diamond.log', $sql);
        //$this->calc_dia_channel_price($data['data'], $company_id);
        //记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        if(!$res)
        {
            $this -> error = 0;
            $this -> error_msg = "查询成功";
        }
        else
        {
            $this -> error = 1;
            $this -> error_msg = "查询失败";
        }
        $this -> return_sql = $sql;
        $this -> return_msg = $data;
        $this->display();
    }



    /**
     * 查询裸钻列表分页信息
     * @param *
     * @return json
     */
    public function get_dealer_diamondlist()
    {
        $this->changeDB(52);
        $page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
        $pageSize = intval($this -> filter["pageSize"]) > 0 ? intval($this -> filter["pageSize"]) : 10;
        if($pageSize > 1000) $pageSize = 1000;
        $cert_id = $this -> filter['cert_id'];//证书号

        $where = " `pifajia` <> 0 ";//AND `good_type` = 2 
        if(!empty($cert_id))
        {
            if(is_array($cert_id)){
                $cert_id = implode ("','",$cert_id);
            }
            $where .= " AND `cert_id` in('".$cert_id."')";
        }

        $sql = "select `cert_id`,`shop_price`,`chengben_jia`,pifajia from `diamond_info`  WHERE  ".$where;
        $data = $this->db->getPageList($sql, array(), $page, $pageSize);
        unset($data['sql']);
        unset($data['countSql']);



        //file_put_contents('diamond.log', $sql);
        //$this->calc_dia_channel_price($data['data'], $company_id);
        //记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        if($data)
        {
            $this -> error = 0;
            $this -> error_msg = "查询成功";
        }
        else
        {
            $this -> error = 1;
            $this -> error_msg = "查询失败";
        }
       // $this -> return_sql = $sql;
        $this -> return_msg = $data;
        $this->display();
    }






    //裸钻推荐查询
    public function getTuiJianList()
    {
        $carat_min = floatval($this -> filter['carat_min']);//最小石重
        $carat_max = floatval($this -> filter['carat_max']);//最大石重
        $cert = $this -> filter['cert'];//证书号类型
        $color = $this -> filter['color'];//颜色
        $clarity = $this -> filter['clarity'];//净度
        $cut = $this -> filter['cut'];//切工
        $from_ad = $this -> filter['from_ad'];//来源
        $not_from_ad=$this -> filter['not_from_ad'];//来源
        $no_goods_id = $this -> filter['no_goods_id'];//排除商品
        $no_cert_id = $this-> filter['no_cert_id'];//排除裸钻
        $warehouse = $this -> filter['warehouse'];//库房
        $pricesort = $this -> filter['pricesort'];//价格排序
        $status = $this -> filter['status'];//状态
        if(!empty($carat_min))
        {
            $where .= " AND d.`carat`>=".$carat_min;
        }
        if(!empty($carat_max))
        {
            $where .= " AND d.`carat`<=".$carat_max;
        }
        if(!empty($from_ad))
        {
            $where .= " AND d.`from_ad`=".$from_ad;
        }
        if(!empty($not_from_ad)){
            if(is_array($not_from_ad)){
                $where .= " AND d.`from_ad` not in(".implode(',',$not_from_ad).")";
            }else{
                $where .= " AND d.`from_ad`<>".$not_from_ad;
            }
        }
        if(!empty($clarity))
        {
            $where .= " AND d.`clarity` in('".implode ("','",$clarity)."')";
        }
        if(!empty($cut))
        {
            $where .= " AND d.`cut` in('".implode ("','",$cut)."')";
        }
        if(!empty($color))
        {
            $where .= " AND d.`color` in('".implode ("','",$color)."')";
        }
        if(!empty($no_goods_id))
        {
            $where .= " AND d.`goods_sn` not in('".implode("','",$no_goods_id)."')";
        }
        if(!empty($no_cert_id))
        {
            $where .= " AND d.`cert_id` not in('".implode("','",$no_cert_id)."')";
        }
        if(!empty($warehouse))
        {
            $where .= " AND d.`warehouse` in('".implode("','",$warehouse)."')";
        }
        if(!empty($status))
        {
            $where .= " and d.`status` in(".$status.")";
        }
        $_order_by = "";
        if($pricesort) $_order_by .= "d.shop_price ".$pricesort.",";
        if($_order_by)
        {
            $_order_by = " ORDER BY ".rtrim($_order_by,',')." ";

        } 
        $join_table="";
        $join_where=" 1 ";
        $sql = "select d.`goods_id`,d.`goods_sn`,d.`goods_name`,d.`goods_number`,d.`from_ad`,d.`good_type`,d.`market_price`,d.`shop_price`,d.`member_price`,d.`chengben_jia`,d.`carat`,d.`clarity`,d.`cut`,d.`color`,d.`shape`,d.`depth_lv`,d.`table_lv`,d.`symmetry`,d.`polish`,d.`fluorescence`,d.`warehouse`,d.`cert`,d.`cert_id`,d.`gemx_zhengshu`,d.`status`,d.`add_time`,d.`is_active`,d.`kuan_sn`,d.pifajia,d.img from `diamond_info` as d $join_table WHERE $join_where ".$where.$_order_by." limit 3,10";
        //file_put_contents('diamond.log', $sql);
        $data = $this->db->getAll($sql);
        //记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        if(!$res)
        {
            $this -> error = 0;
            $this -> error_msg = "查询成功";
        }
        else
        {
            $this -> error = 1;
            $this -> error_msg = "查询失败";
        }
        $this -> return_sql = $sql;
        $this -> return_msg = $data;
        $this->display();
    }

    
	/**
	 *彩钻搜索
	 **/
	function getcolordiamondList ()
	 {
		$page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
		$pageSize = intval($this -> filter["pageSize"]) > 0 ? intval($this -> filter["pageSize"]) : 15;
		$carat_min=floatval($this->filter['carat_min']);//最小石重
		$carat_max=floatval($this->filter['carat_max']);//最大石重
		$cert=$this->filter['cert'];//证书类型
		$clarity=$this->filter['clarity'];//净度
		$color=$this->filter['color'];//颜色
		$color_grade=$this->filter['color_grade'];//颜色分级
		$shape=$this->filter['shape'];//颜色形状
		$symmetry=$this->filter['symmetry'];//对称性
		$polish=$this->filter['polish'];//抛光
		$fluorescence=$this->filter['fluorescence'];//荧光
		$from_ad=$this->filter['from_ad'];//来源
		$cert_id=$this->filter['cert_id'];//证书号
		$goods_sn=$this->filter['goods_sn'];//货品编号
		$price_min=$this->filter['price_min'];//价格
		$price_max=$this->filter['price_max'];//价格
		
		//不要用*,修改为具体字段
		$str = '';
		if(!empty($carat_min))
		{
			$str .= "`carat`>=".$carat_min." AND ";
		}
		if(!empty($carat_max))
		{
			$str .= "`carat`<=".$carat_max." AND ";
		}
		if(!empty($price_min))
		{
			$str .= "`price`>=".$price_min." AND ";
		}
		if(!empty($price_max))
		{
			$str .= "`price`<=".$price_max." AND ";
		}
		if(!empty($cert))
		{
			$str .= "`cert` ='".$cert."' AND ";		
		}
		if(!empty($clarity))
		{
			$str .= "`clarity` ='".$clarity."' AND ";		
		}
		if(!empty($color))
		{
			$str .= "`color` ='".$color."' AND ";		
		}
		
		if(!empty($color_grade))
		{
			$str .= "`color_grade` ='".$color_grade."' AND ";		
		}
		
		if(!empty($shape))
		{
			$str .= "`shape` ='".$shape."' AND ";		
		}
		
// 		if(!empty($symmetry))
// 		{
// 			$str .= "`symmetry` ='".$symmetry."' AND ";		
// 		}
		
// 		if(!empty($polish))
// 		{
// 			$str .= "`polish` ='".$polish."' AND ";		
// 		}
// 		if(!empty($fluorescence))
// 		{
// 			$str .= "`fluorescence` ='".$fluorescence."' AND ";		
// 		}
		
		if(!empty($from_ad))
		{
			$str .= "`from_ad` ='".$from_ad."' AND ";		
		}
		
		if(!empty($cert_id))
		{	
			$str .= "`cert_id` ='".$cert_id."' AND ";		
		}
// 		if(!empty($cert_id))
// 		{
// 			$str .= "`cert_id` IN('".implode("','",$cert_id)."') AND ";
// 		}
		if(!empty($goods_sn))
		{
			//$str .= "`goods_sn` like '%{$goods_sn}%' AND ";
			$str .= "`goods_sn`='".$goods_sn."' AND ";
		}

		$sql   = "SELECT COUNT(*) FROM `app_diamond_color` where status =1 and ".$str.'1';
		
		$record_count   =  $this -> db ->getOne($sql);
		$page_count     = $record_count > 0 ? ceil($record_count / $pageSize) : 1;
		$page = $page > $page_count ? $page_count : $page;
    	$isFirst = $page > 1 ? false : true;
    	$isLast = $page < $page_count ? false : true;
    	$start = ($page == 0) ? 1 : ($page - 1) * $pageSize + 1;
		$sql = "SELECT * FROM `app_diamond_color`";
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE status=1 AND ".$str;

		}else{
			$sql .=" WHERE status=1 ";
		}
		$sql .= " ORDER BY `id` DESC";
		$sql .= " limit ".($start - 1).",$pageSize;";
// 		file_put_contents('e:/8.sql',$sql);
		$data = $this -> db -> getAll($sql);
		//var_dump($data);die;
		$content = array("page" => $page, "pageSize" => $pageSize, "recordCount" => $record_count, "data" => $data,'pageCount'=>$page_count,'isFirst'=>$isFirst,'isLast'=>$isLast,'start'=>$start,'sql'=>$sql);
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
		if(!$data)
		{
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "未查询到此彩钻";
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

    public function getCaiZuanInfo(){
    	
        $goods_sn = $this->filter['goods_sn'];
        if(empty($goods_sn)){
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "没有传款号";
            $this -> return_msg = array();
            $this->display();
        }else{
        	$sql = "SELECT * FROM app_diamond_color WHERE `status` =0 and  (`cert_id`='$goods_sn' or goods_sn='$goods_sn' or id='$goods_sn')";
        	$data =  $this->db->getRow($sql);
        	if($data){
        		$this -> error = 1;
        		$this -> return_sql = '';
        		$this -> error_msg = "under";
        		$this -> return_msg = array();
        		$this->display();
        	}
            $sql = "SELECT * FROM app_diamond_color WHERE `status` =1 and  (`cert_id`='$goods_sn' or goods_sn='$goods_sn' or id='$goods_sn')";
            $data =  $this->db->getRow($sql);
            
        }

        if(!$data)
        {
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到此彩钻";
            $this -> return_msg = array();
            $this->display();
        }
        else
        {
            $this -> error = 0;
            $this -> return_sql = $sql;
            $this -> return_msg = $data;
            $this->display();
        }

    }

    /**
     * 查询裸钻详情
     */
    public function getDiamondInfo()
    {
        $s_time = microtime();
        $where='';
        //$order_id = 49;
        if(!empty($this->filter['cert_id'])){
            $cert_id = trim($this->filter['cert_id']);
            $where .= " (`cert_id`='".$cert_id."' or goods_sn='{$cert_id}')";
        }else{
            $this -> error = 1;
            $this -> error_msg = "cert_id参数不能为空";
            $this -> return_msg = array();
            $this->display();
        }
        if(!empty($this->filter['status'])){
            $status = trim($this->filter['status']);
            $where .= " AND `status` = '".$status."'";
        }
        $attrModel = new GoodsAttributeModel(11);
        $shapeArr = $attrModel->getShapeList();
        //查询商品详情
        $sql="select * from `diamond_info` where ".$where;        
        $row = $this->db->getRow($sql);        
        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        if(empty($row)){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "未查询到此裸钻";
            $this -> return_msg = array();
            $this->display();
        }
        $row['shape_name'] = isset($shapeArr[$row['shape']])?$shapeArr[$row['shape']]:$row['shape'];
        $this -> error = 0;
        $this -> return_sql = $sql;
        $this -> return_msg = $row;
        $this->display();
    }

	/**
	*  天生一对按钻重搜索
	*/
    public function getTsydSQL($filter){
        $where='';
        $one = "";
        $yi = "";
        if(isset($filter['s_carats_tsyd1']) && !empty($filter['s_carats_tsyd1'])){
            $ss = $filter['s_carats_tsyd1'];
            $one.= "d.carat >= '$ss'";
            $yi.= "e.carat >= '$ss'";
        }
        $two = $er ='';
        if(isset($filter['e_carats_tsyd1']) && !empty($filter['e_carats_tsyd1'])){
            $se = $filter['e_carats_tsyd1'];
            
            if($ss){
                $two .= ' AND ';
                $er .= ' AND ';
            }
            $two.= " d.carat <= '$se'";
            $er .= " e.carat <= '$se'";
        }
        $three=$shan='';
        if(isset($filter['s_carats_tsyd2']) && !empty($filter['s_carats_tsyd2'])){
            $es = $filter['s_carats_tsyd2'];
            $three.= "e.carat >= '$es'";
            $shan.= "d.carat >= '$es'";
        }
        if(isset($filter['e_carats_tsyd2']) && !empty($filter['e_carats_tsyd2'])){
            $ee = $filter['e_carats_tsyd2'];
            $four = $shi ='';
            if($es){
                $four .= ' AND ';
                $shi .= ' AND ';
            }
            $four.= " e.carat <= '$ee'";
            $shi.= " d.carat <= '$ee'";
        }
        $and1 =  $and2 ='';

        if($one || $two){
            $lg1 = '(';
            $rg1 = ')';
        }
        if($yi || $er){
            $lg3 = '(';
            $rg3 = ')';
        }

        if($three || $four){
            if($one || $two){
                $and1 = ' AND ';
            }
            $lg2 = '(';
            $rg2 = ')';
        }
        if($shan ||  $shi){
            if($one || $two){
                $and2 = ' AND ';
            }
            $lg4 = '(';
            $rg4 = ')';
        }

        $where = " AND
                (
                    (
                        $lg1 $one  $two $rg1
                        $and1
                        $lg2 $three  $four $rg2
                    )
                    OR
                    (
                        $lg3 $yi  $er $rg3
                        $and2
                        $lg4 $shan  $shi $rg4
                    )
                )
                ";
        return $where;
    }

    /**
    *  天生一对按钻重搜索
    */
    public function getTsydCartSQL($filter){
        $where='';
        $one = "";
        $yi = "";
        if(isset($filter['carat_min']) && !empty($filter['carat_min'])){
            $ss = $filter['carat_min'];
            $one.= "d.carat >= '$ss'";
            $yi.= "e.carat >= '$ss'";
        }
        $two = $er ='';
        if(isset($filter['carat_max']) && !empty($filter['carat_max'])){
            $se = $filter['carat_max'];
            
            if($ss){
                $two .= ' AND ';
                $er .= ' AND ';
            }
            $two.= " d.carat <= '$se'";
            $er .= " e.carat <= '$se'";
        }
        $three=$shan='';
        if(isset($filter['carats_tsyd2']) && !empty($filter['carats_tsyd2'])){
            $cartarr = $filter['carats_tsyd2'];
            $three ="";
            foreach ($cartarr as $stonearr) {
                $stone1 = $stonearr[0];
                $stone2 = $stonearr[1];
                $three .= "(e.carat >= ".$stone1." and e.carat <= ".$stone2.") OR ";
                $shan .= "(d.carat >= ".$stone1." and d.carat <= ".$stone2.") OR ";
            }
            if($three) $three = rtrim($three,"OR ");
            if($shan) $shan = rtrim($shan,"OR ");
            //$three.= "e.carat in('".implode("','", $es)."')";
            //$shan.= "d.carat in('".implode("','", $es)."')";
        }
        /*if(isset($filter['e_carats_tsyd2']) && !empty($filter['e_carats_tsyd2'])){
            $ee = $filter['e_carats_tsyd2'];
            $four = $shi ='';
            if($es){
                $four .= ' AND ';
                $shi .= ' AND ';
            }
            $four.= " e.carat <= '$ee'";
            $shi.= " d.carat <= '$ee'";
        }*/
        $and1 =  $and2 ='';

        if($one || $two){
            $lg1 = '(';
            $rg1 = ')';
        }
        if($yi || $er){
            $lg3 = '(';
            $rg3 = ')';
        }

        if($three){
            if($one || $two){
                $and1 = ' AND ';
            }
            $lg2 = '(';
            $rg2 = ')';
        }
        if($shan){
            if($one || $two){
                $and2 = ' AND ';
            }
            $lg4 = '(';
            $rg4 = ')';
        }

        $where = " AND
                (
                    (
                        $lg1 $one  $two $rg1
                        $and1
                        $lg2 $three  $rg2
                    )
                    OR
                    (
                        $lg3 $yi  $er $rg3
                        $and2
                        $lg4 $shan  $rg4
                    )
                )
                ";
                //file_put_contents('diamond.log', $where);
        return $where;
    }


	/**
    * 添加裸钻
    * @param *
    * @return json
    */
	public function AddDiamondInfo()
	{
		$s_time = microtime();
        $where='';
        $goods_sn=trim($this->filter['goods_sn']);
        $goods_name=trim($this->filter['goods_name']);
        $goods_number=intval(trim($this->filter['goods_number']));
        $from_ad=intval(trim($this->filter['from_ad']));
        $good_type=intval(trim($this->filter['good_type']));
        $market_price=floatval(trim($this->filter['market_price']));
        $chengben_jia=floatval(trim($this->filter['chengben_jia']));
        $carat=floatval(trim($this->filter['carat']));
        $clarity=trim($this->filter['clarity']);
        $cut=trim($this->filter['cut']);
        $color=trim($this->filter['color']);
        $shape=intval(trim($this->filter['shape']));
        $depth_lv=trim($this->filter['depth_lv']);
        $table_lv=trim($this->filter['table_lv']);
        $symmetry=trim($this->filter['symmetry']);
        $polish=trim($this->filter['polish']);
        $fluorescence=trim($this->filter['fluorescence']);
        $cert=trim($this->filter['cert']);
        $cert_id=trim($this->filter['cert_id']);
        $gemx_zhengshu=trim($this->filter['gemx_zhengshu']);
        $status=trim($this->filter['status']);
        $add_time=trim($this->filter['add_time']);
        $is_active=trim($this->filter['is_active']);

		//添加信息
		$sql = "INSERT INTO `diamond_info`(`goods_sn`, `goods_name`, `goods_number`, `from_ad`, `good_type`, `market_price`, `shop_price`, `member_price`, `chengben_jia`, `carat`, `clarity`, `cut`, `color`, `shape`, `depth_lv`, `table_lv`, `symmetry`, `polish`, `fluorescence`, `cert`, `cert_id`, `gemx_zhengshu`, `status`, `add_time`, `is_active`) VALUES ('{$goods_sn}','{$goods_name}',{$goods_number},{$from_ad},{$good_type},{$market_price},{$chengben_jia},{$carat},'{$clarity}','{$cut}','{$color}',{$shape},'{$depth_lv}','{$table_lv}','{$symmetry}','{$polish}','{$fluorescence}','{$cert}','{$cert_id}','{$gemx_zhengshu}',{$status},'{$add_time}',{$is_active})";
		if($this->db->query($sql)){
			$row = $this->db->insert_id();
		}
		
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "添加失败";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = "添加成功";
			$this->display();
		}
	}

	/**
    * 编辑裸钻
    * @param *
    * @return json
    */
	public function EditDiamondInfo()
	{
		$s_time = microtime();
        $where='';
        $goods_sn=trim($this->filter['goods_sn']);
        $goods_name=trim($this->filter['goods_name']);
        $goods_number=intval(trim($this->filter['goods_number']));
        $from_ad=intval(trim($this->filter['from_ad']));
        $good_type=intval(trim($this->filter['good_type']));
        $market_price=floatval(trim($this->filter['market_price']));
        $chengben_jia=floatval(trim($this->filter['chengben_jia']));
        $carat=floatval(trim($this->filter['carat']));
        $clarity=trim($this->filter['clarity']);
        $cut=trim($this->filter['cut']);
        $color=trim($this->filter['color']);
        $shape=intval(trim($this->filter['shape']));
        $depth_lv=trim($this->filter['depth_lv']);
        $table_lv=trim($this->filter['table_lv']);
        $symmetry=trim($this->filter['symmetry']);
        $polish=trim($this->filter['polish']);
        $fluorescence=trim($this->filter['fluorescence']);
        $cert=trim($this->filter['cert']);
        $cert_id=trim($this->filter['cert_id']);
        $gemx_zhengshu=trim($this->filter['gemx_zhengshu']);
        $status=trim($this->filter['status']);
        $is_active=trim($this->filter['is_active']);
		if($goods_sn==''){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "编辑失败，货号不能为空";
			$this -> return_msg = array();
			$this->display();			
		}
		//编辑信息
		$sql = "UPDATE `diamond_info` SET `goods_name`='{$goods_name}',`goods_number`={$goods_number},`from_ad`={$from_ad},`good_type`={$good_type},`market_price`={$market_price},`chengben_jia`={$chengben_jia},`carat`={$carat},`clarity`='{$clarity}',`cut`='{$cut}',`color`='{$color}',`shape`={$shape},`depth_lv`='{$depth_lv}',`table_lv`='{$table_lv}',`symmetry`='{$symmetry}',`polish`='{$polish}',`fluorescence`='{$fluorescence}',`cert`='{$cert}',`cert_id`='{$cert_id}',`gemx_zhengshu`='{$gemx_zhengshu}',`status`={$status},`is_active`={$is_active} WHERE `goods_sn`='{$goods_sn}'";
		$row=$this->db->query($sql);
		
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "编辑失败";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = "编辑成功";
			$this->display();
		}
	}

	/**
    * 删除裸钻
    * @param *
    * @return json
    */
	public function DeleteDiamondInfo()
	{
		$s_time = microtime();
        $where='';
        $goods_sn=trim($this->filter['goods_sn']);
		if($goods_sn==''){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "删除失败，货号不能为空";
			$this -> return_msg = array();
			$this->display();			
		}
		//删除信息
		$sql = "DELETE FROM `diamond_info` WHERE goods_sn='{$goods_sn}'";
		$row=$this->db->query($sql);
		
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg = "删除失败";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $sql;
			$this -> return_msg = "删除成功";
			$this->display();
		}
	}

	/**
    * 通过货号查询裸钻
    * @param *
    * @return json
    */
	public function GetDiamondByGoods_sn()
	{
		
		$s_time = microtime();
        $where='';
        $goods_sn=trim($this->filter['goods_sn']);
		//$order_id = 49;
		if(!empty($goods_sn))
		{
			$where .= " `goods_sn`='".$goods_sn."'";
        }else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "货号不能为空";
			$this -> return_msg = array();
			$this->display();		
		}
        if(!empty($where)){
            //查询商品详情
            $sql="select `goods_id`,`goods_sn`,`goods_name`,`goods_number`,`from_ad`,`good_type`,`market_price`,`shop_price`,`member_price`,`chengben_jia`,`carat`,`clarity`,`cut`,`color`,`shape`,`depth_lv`,`table_lv`,`symmetry`,`polish`,`fluorescence`,`cert`,`cert_id`,`gemx_zhengshu`,`status`,`add_time`,`is_active`,`kuan_sn`, `pifajia` from `diamond_info` " .
            "where ".$where." ;";
            $row = $this->db->getAll($sql);
           
            //如果判断是天生一对，则返回一对裸钻
            if(!empty($row[0]['kuan_sn']) && $row[0]['cert']=='HRD-D'){
                $kuan_sn = $row[0]['kuan_sn'];
                $sql="select `goods_id`,`goods_sn`,`goods_name`,`goods_number`,`from_ad`,`good_type`,`market_price`,`shop_price`,`member_price`,`chengben_jia`,`carat`,`clarity`,`cut`,`color`,`shape`,`depth_lv`,`table_lv`,`symmetry`,`polish`,`fluorescence`,`cert`,`cert_id`,`gemx_zhengshu`,`status`,`add_time`,`is_active`,`kuan_sn`, `pifajia` from `diamond_info` WHERE `kuan_sn`='".$kuan_sn."'" ;
                $row = $this->db->getAll($sql);
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
			$this -> error_msg = "未查询到此裸钻";
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
    * 通过商品编码查询彩钻
    * @param *
    * @return json
    */
	public function GetDiamondByGoods_snOrCertid2()
	{
		$s_time = microtime();
        $where='';
        $goods_sn=trim($this->filter['goods_sn_or_certid']);
		//$order_id = 49;
		if(!empty($goods_sn))
		{
			$where .= " `goods_sn`='".$goods_sn."' or `cert_id` = '".$goods_sn."'";
        }else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "货号不能为空";
			$this -> return_msg = array();
			$this->display();		
		}

        if(!empty($where)){
            //查询商品详情
            $sql="select `goods_id`,`goods_sn`,`goods_name`,`goods_number`,`from_ad`,`good_type`,`market_price`,`shop_price`,`member_price`,`chengben_jia`,`carat`,`clarity`,`cut`,`color`,`shape`,`depth_lv`,`table_lv`,`symmetry`,`polish`,`fluorescence`,`cert`,`cert_id`,`gemx_zhengshu`,`status`,`add_time`,`is_active`,`kuan_sn`, `pifajia` from `diamond_info` " .
            "where ".$where." ;";
            $row = $this->db->getAll($sql);
           
            //如果判断是天生一对，则返回一对裸钻
            if(!empty($row[0]['kuan_sn']) && $row[0]['cert']=='HRD-D'){
                $kuan_sn = $row[0]['kuan_sn'];
                $sql="select `goods_id`,`goods_sn`,`goods_name`,`goods_number`,`from_ad`,`good_type`,`market_price`,`shop_price`,`member_price`,`chengben_jia`,`carat`,`clarity`,`cut`,`color`,`shape`,`depth_lv`,`table_lv`,`symmetry`,`polish`,`fluorescence`,`cert`,`cert_id`,`gemx_zhengshu`,`status`,`add_time`,`is_active`,`kuan_sn`, `pifajia` from `diamond_info` WHERE `kuan_sn`='".$kuan_sn."'" ;
                $row = $this->db->getAll($sql);
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
			$this -> error_msg = "未查询到此裸钻";
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
    * 通过货号查询裸钻
    * @param *
    * @return json
    */
	public function GetDiamondByGoods_snOrCertid()
	{
		$s_time = microtime();
        $where='';
        $goods_sn=trim($this->filter['goods_sn_or_certid']);
		//$order_id = 49;
		if(!empty($goods_sn))
		{
			$where .= " `goods_sn`='".$goods_sn."' or `cert_id` = '".$goods_sn."'";
        }else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "货号不能为空";
			$this -> return_msg = array();
			$this->display();		
		}

        if(!empty($where)){
            //查询商品详情
            $sql="select `goods_id`,`goods_sn`,`goods_name`,`goods_number`,`from_ad`,`good_type`,`market_price`,`shop_price`,`member_price`,`chengben_jia`,`carat`,`clarity`,`cut`,`color`,`shape`,`depth_lv`,`table_lv`,`symmetry`,`polish`,`fluorescence`,`cert`,`cert_id`,`gemx_zhengshu`,`status`,`add_time`,`is_active`,`kuan_sn`, `pifajia` from `diamond_info` " .
            "where ".$where." ;";
            $row = $this->db->getAll($sql);
           
            //如果判断是天生一对，则返回一对裸钻
            if(!empty($row[0]['kuan_sn']) && $row[0]['cert']=='HRD-D'){
                $kuan_sn = $row[0]['kuan_sn'];
                $sql="select `goods_id`,`goods_sn`,`goods_name`,`goods_number`,`from_ad`,`good_type`,`market_price`,`shop_price`,`member_price`,`chengben_jia`,`carat`,`clarity`,`cut`,`color`,`shape`,`depth_lv`,`table_lv`,`symmetry`,`polish`,`fluorescence`,`cert`,`cert_id`,`gemx_zhengshu`,`status`,`add_time`,`is_active`,`kuan_sn`, `pifajia` from `diamond_info` WHERE `kuan_sn`='".$kuan_sn."'" ;
                $row = $this->db->getAll($sql);
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
			$this -> error_msg = "未查询到此裸钻";
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
    * 通过证书号查询裸钻
    * @param *
    * @return json
    */
	public function GetDiamondByCert_id()
	{
		$s_time = microtime();
        $where='';
        $cert_id=trim($this->filter['cert_id']);
		//$order_id = 49;
		if(!empty($cert_id))
		{
			$where .= " `cert_id`='".$cert_id."'";
        }else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "证书号不能为空";
			$this -> return_msg = array();
			$this->display();		
		}

        if(!empty($where)){
            //查询商品详情
            $sql="select * from `diamond_info` where ".$where;
            $row = $this->db->getRow($sql);
            if(empty($row)){
                $sql="select * from `diamond_info_all` where ".$where;
                $row = $this->db->getRow($sql);
                if(!empty($row)){
                    $row['status'] = 2;
                    $row['is_bakdata'] = 1;
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
			$this -> error_msg = "未查询到此裸钻";
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
    * 通过天生一对查询裸钻
    * @param *
    * @return json
    */
	public function GetDiamondByKuan_sn()
	{
		$s_time = microtime();
        $where='';
        $kuan_sn=trim($this->filter['kuan_sn']);
		//$order_id = 49;
		if(!empty($kuan_sn))
		{
			$where .= " `kuan_sn`='".$kuan_sn."'";
        }else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "天生一对不存在";
			$this -> return_msg = array();
			$this->display();		
		}

        if(!empty($where)){
            //查询商品详情
            $sql="select `goods_id`,`goods_sn`,`goods_name`,`goods_number`,`from_ad`,`good_type`,`market_price`,`shop_price`,`member_price`,`chengben_jia`,`carat`,`clarity`,`cut`,`color`,`shape`,`depth_lv`,`table_lv`,`symmetry`,`polish`,`fluorescence`,`warehouse`,`cert`,`cert_id`,`gemx_zhengshu`,`status`,`add_time`,`is_active`,`kuan_sn`, `pifajia`, `img` from `diamond_info` " .
            "where ".$where." ;";
            $row = $this->db->getAll($sql);
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
			$this -> error_msg = "未查询到此裸钻";
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
    * 通过id查询裸钻
    * @param *
    * @return json
    */
	public function GetDiamondByiId()
	{
		$s_time = microtime();
        $where='';
        $goods_id=trim($this->filter['goods_id']);
		if(!empty($goods_id))
		{
			$where .= " `goods_id`=".$goods_id;
        }else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "id不能为空";
			$this -> return_msg = array();
			$this->display();		
		}

        if(!empty($where)){
            //查询商品详情
            $sql="select `goods_id`,`goods_sn`,`goods_name`,`goods_number`,`from_ad`,`good_type`,`market_price`,`shop_price`,`member_price`,`chengben_jia`,`carat`,`clarity`,`cut`,`color`,`shape`,`depth_lv`,`table_lv`,`symmetry`,`polish`,`fluorescence`,`cert`,`cert_id`,`gemx_zhengshu`,`status`,`add_time`,`is_active`,`kuan_sn`,`pifajia` from `diamond_info` " .
            "where ".$where." ;";
//             file_put_contents('e:/8.sql',$sql);
            $row = $this->db->getAll($sql);
            
             //如果判断是天生一对，则返回一对裸钻
            if(!empty($row[0]['kuan_sn']) && $row[0]['cert']=='HRD-D'){
                $kuan_sn = $row[0]['kuan_sn'];
                $sql="select `goods_id`,`goods_sn`,`goods_name`,`goods_number`,`from_ad`,`good_type`,`market_price`,`shop_price`,`member_price`,`chengben_jia`,`carat`,`clarity`,`cut`,`color`,`shape`,`depth_lv`,`table_lv`,`symmetry`,`polish`,`fluorescence`,`cert`,`cert_id`,`gemx_zhengshu`,`status`,`add_time`,`is_active`,`kuan_sn`,`pifajia` from `diamond_info` WHERE `kuan_sn`='".$kuan_sn."'" ;
                $row = $this->db->getAll($sql);
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
			$this -> error_msg = "未查询到此裸钻";
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
	 * 通过id查询裸钻
	 * @param *
	 * @return json
	 */
	public function GetColorDiamondByiId()
	{
		$s_time = microtime();
		$where='';
		$id=trim($this->filter['id']);
		if(!empty($id))
		{
			$where .= " `id`=".$id;
		}else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "id不能为空";
			$this -> return_msg = array();
			$this->display();
		}
	
		if(!empty($where)){
			//查询商品详情
			$sql="select `id`,`goods_sn`,`quantity`,`carat`,`measurements`,`symmetry`,`polish`,`fluorescence`,`shape`,`color`,`color_grade`,`clarity`,`cert`,`cert_id`,`price`,`from_ad`,`warehouse`,`cost_price`,`good_type`,`status` from `app_diamond_color` " .
					"where ".$where." ;";
// 			file_put_contents('e:/8.sql',$sql);
			$row = $this->db->getAll($sql);

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
			$this -> error_msg = "未查询到此裸钻";
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
    * 判断此密码是否有效
    * @param user_id
    * @param type
    * @param mima
    * @return json
    */
	public function checkDiscountMima()
	{
	    $this->changeDB(99);
	    
		$s_time = microtime();
        $where='';
        //判断此用户是否有折扣权限
        $user_id = trim($this->filter['user_id']);
        $mima = trim($this->filter['mima']);
        $type = trim($this->filter['type']);
		if(!empty($user_id))
		{
			//$where .= "  AND `user_id`=".$user_id;
        }else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "用户id不能为空";
			$this -> return_msg = array();
			$this->display();		
		}
		if(!empty($type))
		{
			$where .= "  AND `type`=".$type;
        }
        
        $sql ="SELECT `id` FROM `base_lz_discount_config` WHERE `enabled`=1  ".$where;
        $row1 = $this->db->getRow($sql);
        if(empty($row1)){
            $this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "您请去折扣管理添加折扣权限,并开启";
			$this -> return_msg = array();
			$this->display();	 
        }
        
         if(!empty($mima))
		{
			$where .= " AND `mima`='".$mima."'";
        }else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = '提示：<span style="color:red">超出折扣权限，请输入折扣密码！</span>';
			$this -> return_msg = array();
			$this->display();		
		}
        //判断此密码是否还有效
        if(!empty($where)){
            //查询商品详情
            $date_time = date("Y-m-d H:i:s");
            $sql = "SELECT `id`,`zhekou` FROM `app_lz_discount_grant` WHERE `status` =1 AND `createtime` <= '".$date_time."'  AND `endtime` >='".$date_time."' ".$where." order by `createtime` desc limit 1";
            $row = $this->db->getRow($sql);
        }else{
            $row=false;
        }
       $_diamond_type =array('1'=>'普通（现货）<0.5克拉','2'=>'普通（现货）0.5（含）~1.0克拉','3'=>'普通（现货）1.0（含）~1.5克拉','4'=>'普通（现货）1.5（含）克拉以上','5'=>'星耀<0.5克拉','6'=>'星耀0.5（含）~1.0克拉','7'=>'星耀1.0（含）~1.5克拉','8'=>'星耀1.5（含）克拉以上','9'=>'天生一对裸石','10'=>'天生一对成品','11'=>'成品','12'=>'普通（期货）<0.5克拉','13'=>'普通（期货）0.5（含）~1.0克拉','14'=>'普通（期货）1.0（含）~1.5克拉','15'=>'普通（期货）1.5（含）克拉以上','16'=>'香榭巴黎','17'=>'皇室公主');
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$row){
			$this -> error = 1;
			$this -> return_sql = $sql;
			$this -> error_msg ='提示：<span style="color:red">请确认申请优惠码类型是否是"'.$_diamond_type[$type].'",再确认是否密码错误或者过期！如果是请重申折扣密码。';
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
    * 判断此用户输入金额是否为超出折扣权限
    * @param user_id
    * @param type
    * @param mima
    * @return json
    */
    public function checkDiscountMimaPrice()
    {
        $this->changeDB(99);
        
        $s_time = microtime();
        $where='';
        //判断此用户是否有折扣权限
        $user_id = trim($this->filter['user_id']);
        $mima = trim($this->filter['mima']);
        $type = trim($this->filter['type']);
        if(!empty($user_id))
        {
            $where .= "  AND `user_id`=".$user_id;
        }else{
            $this -> error = 1;
            $this -> return_sql = '';
            $this -> error_msg = "用户id不能为空";
            $this -> return_msg = array();
            $this->display();       
        }
        if(!empty($type))
        {
            $where .= "  AND `type`=".$type;
        }

        $_diamond_type =array('1'=>'普通（现货）<0.5克拉','2'=>'普通（现货）0.5（含）~1.0克拉','3'=>'普通（现货）1.0（含）~1.5克拉','4'=>'普通（现货）1.5（含）克拉以上','5'=>'星耀<0.5克拉','6'=>'星耀0.5（含）~1.0克拉','7'=>'星耀1.0（含）~1.5克拉','8'=>'星耀1.5（含）克拉以上','9'=>'天生一对裸石','10'=>'天生一对成品','11'=>'成品','12'=>'普通（期货）<0.5克拉','13'=>'普通（期货）0.5（含）~1.0克拉','14'=>'普通（期货）1.0（含）~1.5克拉','15'=>'普通（期货）1.5（含）克拉以上','16'=>'香榭巴黎','17'=>'皇室公主');

        // 记录日志
        $reponse_time = microtime() - $s_time;
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        
        $sql ="SELECT `id`,`zhekou` FROM `base_lz_discount_config` WHERE `enabled`=1  ".$where;
        $row = $this->db->getRow($sql);
        if(empty($row)){
            $this -> error = 1;
            $this -> return_sql = $sql;
            $this -> error_msg = "您没有“<span style='color:red'>".$_diamond_type[$type]."</span>”商品的折扣权限，请申请设置并开启！";
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
    * 通过货号取用户id
    * @param user_id
    * @param type
    * @param order_goods_id
    * @return json
    */
	public function checkDiscountid()
	{
	    $this->changeDB(99);
	    
		$s_time = microtime();
        $where='';
        //判断此用户是否有折扣权限
        $user_id = trim($this->filter['user_id']);
        $order_goods_id = trim($this->filter['order_goods_id']);
        $type = trim($this->filter['type']);
		if(!empty($order_goods_id))
		{
			//$where .= "  AND `user_id`=".$user_id;
        }else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "货品id不能为空";
			$this -> return_msg = array();
			$this->display();		
		}
		if(!empty($type))
		{
			$where .= "  AND `type`=".$type;
        }
        
         if(!empty($order_goods_id))
		{
			$where .= " AND `order_goods_id`='".$order_goods_id."'";
        }else{
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "货品不能为空";
			$this -> return_msg = array();
			$this->display();		
		}
        //判断此密码是否还有效
        if(!empty($where)){
            //查询商品详情
            $date_time = date("Y-m-d H:i:s");
            $sql = "SELECT `id`, `user_id`, `type`, `zhekou`, `mima`, `create_user_id`, `create_user`, `createtime`, `endtime`, `use_user_id`, `use_user`, `usetime`, `order_goods_id`, `status` FROM `app_lz_discount_grant` WHERE `status` =2 ".$where;
            $row = $this->db->getRow($sql);
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
			$this -> error_msg ="你还没有设置折扣";
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
    * 密码已经使用更新状态
    * 并加操作日志 
    * @param user_id
    * @param type
    * @param mima
    * @return json
    */
	public function updateDiscountMima()
	{
	    $this->changeDB(99);
	    
		$s_time = microtime();
        $where='';
      
        if(isset($this->filter['log_data'])){
            $log_data = $this->filter['log_data'];
            if(count($log_data)<1){
                $this->error = 1;
				$this->return_sql = '';
				$this->error_msg = "insert_data是个空数组";
				$this->return_msg = 0;
				$this->display();
            }
        }
        
        if (isset($this->filter['grant_data'])) {
            $data = $this->filter['grant_data'];
            if(count($data) > 0){
    
				foreach($data as $k =>$v){
					if(is_null($v)){
						$data[$k]='';
					}
				}
                //更新密码使用状态
                $where = " `id` = '{$data['grant_id']}'";
                unset($data['grant_id']);
                $res = $this -> db -> autoExecute('app_lz_discount_grant',$data,'UPDATE',$where);
            }else{
                $this->error = 1;
				$this->return_sql = '';
				$this->error_msg = "update_data是个空数组";
				$this->return_msg = 0;
				$this->display();
            }
        }else{
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数update_data";
            $this->return_msg = 0;
            $this->display();
        }
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if(!$res){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "操作失败";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = '';
			$this -> return_msg = "操作成功";
			$this->display();
		}
	}
    
 	/**
    * 密码已经使用更新状态
    * 并加操作日志 
    * @param user_id
    * @param type
    * @param order_goods_id
    * @return json
    */
	public function updateDiscountMimas()
	{
	    $this->changeDB(99);
	    
		$s_time = microtime();
        $where='';
      
        if(isset($this->filter['log_data'])){
            $log_data = $this->filter['log_data'];
            if(count($log_data)<1){
                $this->error = 1;
				$this->return_sql = '';
				$this->error_msg = "insert_data是个空数组";
				$this->return_msg = 0;
				$this->display();
            }
        }
        
        if (isset($this->filter['grant_data'])) {
            $data = $this->filter['grant_data'];
            if(count($data) > 0){
               
                //更新密码使用状态
                $where = " `id` = '{$data['grant_id']}'";
                
                unset($data['grant_id']);
                //编辑信息
                $sql = "UPDATE `app_lz_discount_grant` SET `order_goods_id`='{$data['order_goods_id']}',`goods_sn`='{$data['goods_sn']}',`goods_price`='{$data['goods_price']}',`real_price`='{$data['real_price']}',`cert`='{$data['cert']}',`cert_id`='{$data['cert_id']}',`use_user_id`='{$data['use_user_id']}',`use_user`='{$data['use_user']}',`usetime`='{$data['usetime']}',`status`='{$data['status']}' WHERE".$where;
                $res=$this->db->query($sql);
                //插入使用日志
                //$res = $this -> db -> autoExecute('app_lz_discount_log',$log_data,'INSERT',$where, "SILENT");
               
            }else{
                $this->error = 1;
				$this->return_sql = '';
				$this->error_msg = "update_data是个空数组";
				$this->return_msg = 0;
				$this->display();
            }
        }else{
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少参数update_data";
            $this->return_msg = 0;
            $this->display();
        }
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if($res==false){
			$this -> error = 1;
			$this -> return_sql = '';
			$this -> error_msg = "操作失败";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = '';
			$this -> return_msg = "操作成功";
			$this->display();
		}
	}   
    
	/**
	 * 批量更新裸钻基本信息（根据证书号）
	 * @param goods_id
	 * @return json
	 */
	public function editDiamondInfoMulti()
	{
        $this->changeDB(52);
	    $s_time = microtime();
	    if(!is_array($this->filter['data'])){
	        $this->error = 1;
	        $this->return_sql = '';
	        $this->error_msg = "缺少goods_id参数";
	        $this->display();
	    }
	    $data = $this->filter['data'];
	    foreach($data as $cert_id=>$field_values){
	        $where = "cert_id='{$cert_id}'";
	        $res = $this->db->autoExecute('diamond_info', $field_values, 'UPDATE', $where);
	    }
	    // 记录日志
	    $reponse_time = microtime() - $s_time;
	    $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
	
	    //返回信息
	    if($res===false){
	        $this -> error = 1;
	        $this -> return_sql = $res;
	        $this -> error_msg = "操作失败";
	        $this -> return_msg = array();
	        $this->display();
	    }else{
	        $this -> error = 0;
	        $this -> return_sql = $res;
	        $this -> return_msg = "操作成功";
	        $this->display();
	    }
	}
    
 	/**
     * 更新裸钻下架信息
    * @param goods_id
    * @return json
    */
	public function updateDiamondInfo()
	{
		$s_time = microtime();      
        if(!isset($this->filter['goods_id']) || $this->filter['goods_id']==''){
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少goods_id参数";
            $this->display();
        }
        $goods_id = $this->filter['goods_id'];
        foreach ($goods_id as $key => $value) {
            $field_values['status'] = 2;
            $where = "goods_sn='{$value['goods_id']}'";
            $res = $this->db->autoExecute('diamond_info', $field_values, 'UPDATE', $where);
        }
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if($res==false){
			$this -> error = 1;
			$this -> return_sql = $res;
			$this -> error_msg = "操作失败";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $res;
			$this -> return_msg = "操作成功";
			$this->display();
		}
	}   
    
    
	
	/**
     * 更新彩钻下架信息
    * @param goods_id
    * @return json
    */
	public function updateDiamondColorInfo()
	{
		$s_time = microtime();      
        if(!isset($this->filter['id']) || $this->filter['id']==''){
            $this->error = 1;
            $this->return_sql = '';
            $this->error_msg = "缺少id参数";
            $this->display();
        }
        $goods_id = $this->filter['id'];
        foreach ($goods_id as $key => $value) {
            $field_values['status'] = 2;
            $where = "id='{$value['id']}'";
            $res = $this->db->autoExecute('app_diamond_color', $field_values, 'UPDATE', $where);
        }
		// 记录日志
		$reponse_time = microtime() - $s_time;
		$this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));

		//返回信息
		if($res==false){
			$this -> error = 1;
			$this -> return_sql = $res;
			$this -> error_msg = "操作失败";
			$this -> return_msg = array();
			$this->display();
		}else{
			$this -> error = 0;
			$this -> return_sql = $res;
			$this -> return_msg = "操作成功";
			$this->display();
		}
	}
	
	public function remote_exec_sql() {
	    if( isset($this->filter['sql']) && !empty($this->filter['sql']) && isset($this->filter['mt']) && !empty($this->filter['mt'])) { 
	        $method = $this->filter['mt'];
	        if (method_exists($this->db, $method)) {
	            $resp = $this->db->$method($this->filter['sql']);
	            
	            $this -> error = 0;
	            $this -> return_sql = '';
	            $this -> return_msg = $resp;
	            $this->display();
	        } else {
	            $this -> error = 1;
	            $this -> return_sql = '';
			    $this -> error_msg = "操作失败";
			    $this -> return_msg = array();
	            $this->display();
	        }
	    }
	} 
	private function getConfig($key,$value=''){	    
	   $items = isset(self::$configList[$key]) ? self::$configList[$key] : [];
	   $str="";
	   foreach ($items as $hiddenValue=>$displayText) {
	        if($value==$hiddenValue){
	            return $displayText;
	        }
	   }
	   return $str;
	}   
	/**
	 * 裸钻价格计算
	 */
    public function calcprice(){
        
        if(!isset($this->filter['channel_id']) || empty($this->filter['channel_id'])){
            $this->error = 1;
            $this->error_msg = "没有渠道参数";
            $this->display();
        } else if(!isset($this->filter['cert_ids']) || empty($this->filter['cert_ids'])){
            $this->error = 1;
            $this->error_msg = "没有证书号参数";
            $this->display();
        }         
        $channel_id = $this->filter['channel_id'];
        $cert_ids = $this->filter['cert_ids'];
        $cert_ids = Util::eexplode(",", $cert_ids);
        $cert_ids = "'".implode("','", $cert_ids)."'";
        $diamondinfo = DB::cn(19)->getAll("select good_type, cert, cert_id, carat, pifajia, shop_price from front.diamond_info where cert_id in ({$cert_ids});");
        if(empty($diamondinfo)){
            $this->error = 1;
            $this->error_msg = "证书号不存在";
            $this->display();
        }
        
        $sql = "select company_id from cuteframe.sales_channels where id={$channel_id}";
        $company_id = DB::cn(99)->getOne($sql);        
        if ($company_id == '666'
            || $company_id == '488'
            || $company_id == '623'
            || $company_id == '760') {
                $calc_func = function(&$d, $company_id='') {
                    if ($d['cert'] == 'HRD-S') {
                        $x = 1.1;
                        /*
                        if ($company_id == '623') {
                            if ($d['carat'] >= 0.5) {
                                $x = 1.15;
                            } else {
                                $x = 1.35;
                            }
                        }*/
                        if ($company_id == '623' || $company_id == '760'){
                            $x = 1.05;
                        }
                        $d['shop_price'] = round($d['shop_price'] * $x);
                        $d['jiajialv'] = $x;
                    }
                };
        
                if (count($diamondinfo) == count($diamondinfo, 1)) {
                    $calc_func($diamondinfo, $company_id);
                } else {
                    foreach ($diamondinfo as &$d) {
                        $calc_func($d, $company_id);
                    }
                }
 
            }else{
                
                $sql = "select * from diamond_jiajialv where channel_id={$channel_id} and status = 1";
                $jiajialv = DB::cn(250)->getAll($sql);
                //print_r($jiajialv);
                $calc_func = function(&$d) use($jiajialv) {
                    if ($d['pifajia'] == 0) {
                        $d['shop_price_recalc'] = 0;
                        return;
                    }
                    //echo $d['cert'],'--',$d['carat'],'--',$d['good_type'];
                    foreach ($jiajialv as $cfg) {
                        if ($cfg['cert'] == $d['cert']
                            && $d['good_type'] == $cfg['good_type']
                            && $cfg['carat_min'] <= $d['carat']
                            && $d['carat'] < $cfg['carat_max']) {
                                $d['shop_price'] = round($d['pifajia'] * $cfg['jiajialv']);
                                $d['jiajialv'] = $cfg['jiajialv'];
                                $d['shop_price_recalc'] = 1;
                                break;
                            }
                    }
                    if (!isset($d['shop_price_recalc'])) {     
                        /**
                         * 针对星耀： 如果没有设置加价率，按以下逻辑
                         * 30-49分最低2.1；50-59分最低1.643；60-99分最低1.546；100-149分最低1.457；150分以上最低1.2
                         */        
                        //获取对应证书类型的默认加价率数组
                        if($d['good_type'] == 1){//货品类型：1现货2期货
                            $default_jiajialv = "store_lz_moren_jijialv";
                        }else{
                            $default_jiajialv = "store_lz_qihuo_moren_jijialv";
                        }
                        $store_lz_jijialv_arr = $this->getConfig($default_jiajialv,$d['cert']);
                        if(empty($store_lz_jijialv_arr)){
                            //获取默认加价率
                            $lv = $this->getConfig($default_jiajialv,'default');
                        }else{
                            //获取定义石重数组的健名
                            $carat_arr = $this->getConfig("carat");
                            $carat_key_arr = array_keys($carat_arr);
                            //组成新的默认加价率关联数组
                            $carat_min_arr = array_combine($carat_key_arr,$store_lz_jijialv_arr);
            
                            $carat = "0";
                            //获取对应的钻重范围
                            foreach ($carat_key_arr as $v){
                                $carat_arr = explode('~',$v);
                                if(isset($carat_arr[1])){
                                    if($d['carat'] >= $carat_arr[0] && $d['carat'] < $carat_arr[1]){
                                        $carat = $v;
                                        break;
                                    }
                                }else{
                                    if($d['carat'] >= $carat_arr[0]){
                                        $carat = $v;
                                        break;
                                    }
                                }
                            }
                            //获取对应钻重的默认加价率
                            if(isset($carat_min_arr[$carat])){
                                $lv = $carat_min_arr[$carat];
                            }else{
                                $lv = $this->getConfig($default_jiajialv,'default');
                            }
                        }        
            
                        $d['shop_price'] = round($d['pifajia'] * $lv);
                        $d['shop_price_recalc'] = 0;
                        $d['jiajialv'] = $lv;
                    }
                };
                if (count($diamondinfo) == count($diamondinfo, 1)) {
                    $calc_func($diamondinfo);
                } else {
                    foreach ($diamondinfo as &$d) {
                        $calc_func($d);
                    }
                }
        }
        $resp = array();
        foreach ($diamondinfo as $vo){
            $resp[$vo['cert_id']] = $vo['shop_price'];
        }
        unset($diamondinfo);
        $this -> error = 0;
        $this -> return_msg = $resp;
        $this->display();
    }
	/*
	public function calcprice() {
	    if(!isset($this->filter['channel_id']) || empty($this->filter['channel_id'])){
	        $this->error = 1;
	        $this->error_msg = "没有渠道参数";
	        $this->display();
	    } else if(!isset($this->filter['cert_ids']) || empty($this->filter['cert_ids'])){
	        $this->error = 1;
	        $this->error_msg = "没有证书号参数";
	        $this->display();
	    }
	    
	    $channel_id = $this->filter['channel_id'];
	    $cert_ids = $this->filter['cert_ids'];
	    
	    $sql = "select s.id from cuteframe.sales_channels s,cuteframe.company c where s.company_id=c.id and c.company_type=2 and c.is_deleted=0";
	    $tuoguan_channels = DB::cn(99)->getAll($sql);  
	    $tuoguan_channels = array_column($tuoguan_channels, 'id');
	    
	    if (in_array($channel_id, $tuoguan_channels)) {
	        $sql = "select * from front.diamond_channel_jiajialv where channel_id={$channel_id} and status = 1";
	        $channel_price_configs = DB::cn(99)->getAll($sql);     
	    } else {
	        $sql = "select * from diamond_jiajialv where channel_id={$channel_id} and status = 1";
	        $channel_price_configs = DB::cn(250)->getAll($sql);
	        if (empty($channel_price_configs)) {
				$sql = "select * from diamond_jiajialv_default where status = 1";
				$channel_price_configs = DB::cn(250)->getAll($sql);
			}
	    }
	    
	    $cert_ids = Util::eexplode(",", $cert_ids);
	    $cert_ids = "'".implode("','", $cert_ids)."'";
	    $diamond_list = DB::cn(19)->getAll("select good_type, cert, cert_id, carat, pifajia, shop_price from front.diamond_info where cert_id in ({$cert_ids});");

		$resp = array();

        if ($channel_id == '292' || $channel_id == '30' || $channel_id == '242') {
		        foreach ($diamond_list as $d) {		    		
		    			if ($d['cert'] == 'HRD-S') {
							$x = 1.1;
							if ($channel_id == '242') {
								if ($d['carat'] >= 0.5) {
									$x = 1.15;
								} else {
									$x = 1.35;
								}
							}
							
							$d['shop_price'] = round($d['shop_price'] * $x);
		    			}
		    		
		    		    $resp[$d['cert_id']] =  $d['shop_price'];    				           
		        }    		


        	    $this -> error = 0;
			    $this -> return_msg = $resp;
			    $this->display();     
        }

        foreach ($diamond_list as $d) {
			$resp[$d['cert_id']] =  $d['shop_price'];
            if ($d['pifajia'] > 0) {
				$matched = false;
				foreach ($channel_price_configs as $cfg) {
					if ($cfg['cert'] == $d['cert'] && $d['good_type'] == $cfg['good_type'] && $cfg['carat_min'] <= $d['carat'] && $d['carat'] < $cfg['carat_max']) {
						$resp[$d['cert_id']] = round($d['pifajia'] * $cfg['jiajialv']);
						$matched = true;
						break;
					}
				}
				
				if (!$matched) {
					$lv =  $d['good_type'] == 1 ? 1.95 : 1.95;
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
					$resp[$d['cert_id']] = round($d['pifajia'] * $lv); //避免将成本价显示出来
				}
			}
        }
	    
	    $this -> error = 0;
	    $this -> return_msg = $resp;
	    $this->display();
	}
    */
    public function NumToStr($num){
        if (stripos($num,'e')===false) return $num;
        $num = trim(preg_replace('/[=\'"]/','',$num,1),'"');
        $result = "";
        while ($num > 0){
            $v = $num - floor($num / 10)*10;
            $num = floor($num / 10);
            $result = $v . $result;
        }
        return $result; 
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

	/**
    *Carats.io – Diamonds API
    */
    public function get_diamond_list()
    {
        $s_time = microtime();
        date_default_timezone_set('UTC');
        $this->changeDB(52);
        $act    = $this->filter['act'];// all,add,edit,delete
        $page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
        $pageSize = intval($this -> filter["pageSize"]) > 0 ? intval($this -> filter["pageSize"]) : 15;
        $StartDate  = $this->filter['StartDate'];//开始生成UTC时间
        $EndDate    = $this->filter['EndDate'];//结束生成UTC时间

        //$StartDate = '636689251439869028';
        //$EndDate  = '636690116886151147';

        $where = " d.`status` = 1 ";
        if(!empty($act) 
            && in_array($act, array('add','edit','delete'))){
           if(empty($StartDate) || empty($EndDate))
            {
                $this -> error = 1;
                $this -> error_msg = "DATE IS EMPTY.";
                $this -> return_msg = array();
                $this->display();
            }else{
                $StartDate = date('Y-m-d H:i:s', ($StartDate-621355968000000000)/10000000);
                $EndDate = date('Y-m-d H:i:s', ($EndDate-621355968000000000)/10000000);
            }
        }

        //if(!empty($StartDate))
        //{
        //    $where .= " and d.`add_time` >= '{$StartDate} 00:00:00'";
        //}

        //if(!empty($EndDate))
        //{
        //    $where .= " and d.`add_time` <= '{$EndDate} 00:00:00'";
        //}

        $fields = " d.`cert` as GradingLab, 
                d.`cert_id` as Certificate,
                d.`carat` as Carats,
                d.`goods_number` as Num,
                d.`cts` as PPC,
                d.`chengben_jia` as PurchasePrice,
                d.`shop_price` as SellPrice,
                d.`add_time` ";

        if($act == 'add')
        {
            $sql = "SELECT {$fields} from diamond_info_all da left join diamond_info d on da.cert_id=d.cert_id  where da.add_time>'{$StartDate}' and  d.cert_id is not null and da.add_time<'{$EndDate}'";
        }
        elseif($act == 'edit')
        {
            $sql = "SELECT {$fields} from diamond_info_all da left join diamond_info d on da.cert_id=d.cert_id  where da.add_time<'{$EndDate}' and  d.cert_id is not null and d.add_time>'{$StartDate}' and d.add_time<'{$EndDate}'";
        }
        elseif($act == 'delete')
        {
            //$sql = "SELECT {$fields} from `diamond_info` as d where {$where} ORDER BY d.`add_time` desc";
        }
        elseif($act == 'getall')
        {
            $sql = "SELECT {$fields} from `diamond_info` as d where {$where} ORDER BY d.`add_time` desc";
        }
        else
        {
            $this -> error = 1;
            $this -> error_msg = "ERROR : act is empty.";
            $this -> return_msg = array();
            $this->display();
        }

        $data = $this->db->getPageListNew($sql, array(), $page, $pageSize);
        //file_put_contents('diamond.log', $sql);
        $time = gmdate('d.m.Y H:i', time());
        if(!empty($data['data'])){
            $info = array();
            foreach ($data['data'] as $key => $val) {
                $resut = array();
                $add_time = $val['add_time'];
                $ReportDate = "";
                if($add_time){
                    $ReportDate = $this->NumToStr((strtotime($add_time)*10000000)+621355968000000000);
                }
                //$resut['StartDate']         = $StartDate;                
                //$resut['EndDate']           = $EndDate;
                //$resut['ReportDate']        = $time;                //响应UTC时间
                $info[$key]['ReportDate'] = $ReportDate;
                $resut['GradingLab']        = $val['GradingLab'];   //证书类型
                $resut['Certificate']       = $val['Certificate'];  //证书号
                $resut['Carats']            = (float) $val['Carats'];       //石重
                $resut['PPC']               = (float) $val['PPC'];   //美元每克拉价
                $resut['PurchasePrice']     = (float) round($val['PurchasePrice']/THE_EXCHANGE_RATE, 2);   //采购价
                $resut['SellPrice']         = (float) round($val['SellPrice']/THE_EXCHANGE_RATE, 2);   //售价
                $resut['CertificateURL']    = "";   //证书文件URL
                $resut['InventoryStatus']   = $act;   //库存状态
                $info[$key]['KelaRawWrappers'] = $resut;
            }
            $data['data'] = $info;
        }
        $reponse_time = microtime() - $s_time;// 记录日志
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        if(empty($data['data']))
        {
            $this -> error = 1;
            //$this -> return_sql = $sql;
            $this -> error_msg = "is empty data.";
            $this -> return_msg = array();
            $this->display();
        }
        else
        {
            unset($data['countSql'],
            $data['sql'],
            $data['isFirst'],
            $data['isLast'],
            $data['start']);//去掉返回的sql
            
            $this -> error = 0;
            //$this -> return_sql = $sql;
            $this -> return_msg = $data;
            $this->display();
        }
    }

    /**
    *lz列表接口
    */
    public function getdiamondlists()
    {
        $s_time = microtime();
        $this->changeDB(52);
       
        $page = intval($this -> filter["page"]) <= 0 ? 1 : intval($this -> filter["page"]);
        $pageSize = intval($this -> filter["pageSize"]) > 0 ? intval($this -> filter["pageSize"]) : 15;

        $goods_sn=$this->filter['goods_sn'];//货号
        $goods_name=$this->filter['goods_name'];//商品名称
        $good_type=$this->filter['good_type'];//货品类型
        $carat_min=floatval($this->filter['carat_min']);//最小石重
        $carat_max=floatval($this->filter['carat_max']);//最大石重
        $price_min=floatval($this->filter['price_min']);//最小价格
        $price_max=floatval($this->filter['price_max']);//最大价格
        $clarity=$this->filter['clarity'];//净度
        $cut=$this->filter['cut'];//切工
        $color=$this->filter['color'];//颜色
        $shape=$this->filter['shape'];//形状
        $symmetry=$this->filter['symmetry'];//对称
        $polish=$this->filter['polish'];//抛光
        $fluorescence=$this->filter['fluorescence'];//荧光
        $cert=$this->filter['cert'];//证书号类型
        $cert_id=$this->filter['cert_id'];//证书号
        $img = $this->filter['img'];//图片
        $pf_price_min=floatval($this->filter['pf_price_min']);//最小价格
        $pf_price_max=floatval($this->filter['pf_price_max']);//最大价格

        $where = " d.`status` = 1 and ((d.good_type = 1 and d.warehouse in ('SZZSLZ','TBLZK','SHZJSLZK','CGBZZK','SZTSYDLZK','SHZJSJHK','COM')) or d.good_type = 2)";

        if(!empty($goods_sn))
        {
            $where .= " and d.`goods_sn`='".$goods_sn."'";
        }
        if(!empty($goods_name))
        {
            $where .= " and d.`goods_name`='".$goods_name."'";
        }
        if(!empty($good_type))
        {
            $where .= " and d.`good_type`=".$good_type;
        }
        if(!empty($carat_min))
        {
            $where .= " and d.`carat`>=".$carat_min;
        }
        if(!empty($carat_max))
        {
            $where .= " and d.`carat`<=".$carat_max;
        }
        if(!empty($price_min))
        {
            $where .= " and d.`shop_price`>=".$price_min;
        }
        if(!empty($price_max))
        {
            $where .= " and d.`shop_price`<=".$price_max;
        }
        if(!empty($pf_price_min))
        {
            $where .= " and d.`pifajia`>=".$pf_price_min;
        }
        if(!empty($pf_price_max))
        {
            $where .= " and d.`pifajia`<=".$pf_price_max;
        }
        if(!empty($clarity))
        {
            $where .= " and d.`clarity` in('".implode ("','",$clarity)."')";
        }
        if(!empty($cut))
        {
            $where .= " and d.`cut` in('".implode ("','",$cut)."')";
        }
        if(!empty($color))
        {
            $where .= " and d.`color` in('".implode ("','",$color)."')";
        }
        if(!empty($shape))
        {
            $where .= " and d.`shape` in('".implode ("','",$shape)."')";
        }
        if(!empty($symmetry))
        {
            $where .= " and d.`symmetry` in('".implode ("','",$symmetry)."')";
        }
        if(!empty($polish))
        {
            $where .= " and d.`polish` in('".implode ("','",$polish)."')";
        }
        if(!empty($fluorescence))
        {
            $where .= " and d.`fluorescence` in('".implode ("','",$fluorescence)."')";
        }
        if(!empty($cert))
        {
            $where .= " and d.`cert` in('".implode ("','",$cert)."')";
        }
        if(!empty($cert_id))
        {   
            if(is_array($cert_id)){
                $cert_id = implode ("','",$cert_id);
            }
            $where .= " and d.`cert_id` in('".$cert_id."')";
        }
        if(isset($img) && !empty($img))
        {
            $where.=" and left(d.img,40) in ('https://diamonds.kirangems.com/GemKOnlin','https://www.sheetalgroup.com/Details/Sto')";
        }

        $fields = " d.goods_sn,
                d.`goods_name`,
                d.`good_type`,
                d.shop_price,
                d.`carat`,
                d.`clarity`,
                d.`cut`,
                d.`color`,
                d.`shape`,
                d.`depth_lv`,
                d.`table_lv`,
                d.`symmetry`,
                d.`polish`,
                d.`fluorescence`,
                d.`guojibaojia`,
                d.`cts`,
                d.`us_price_source`,
                d.`source_discount`,
                d.`cert`,
                d.`cert_id`,
                d.`pifajia`";

        if(isset($img) && !empty($img))
        {
            $fields.= ",case 
              when left(d.img,40)='https://diamonds.kirangems.com/GemKOnlin' then  concat('http://boss.kela.cn/diamondView/Vision360.html?d=',d.cert_id) 
              when left(d.img,40)='https://www.sheetalgroup.com/Details/Sto' then concat('http://boss.kela.cn/diamondView/diamondView.php?goods_sn=',d.goods_sn)              
              else '' end as img";
        }

        $sql = "SELECT {$fields} from `diamond_info` as d where {$where} ORDER BY d.`add_time` desc";

        $data = $this->db->getPageListNew($sql, array(), $page, $pageSize);

        $reponse_time = microtime() - $s_time;// 记录日志
        $this->recordLog(__FUNCTION__, $reponse_time, json_encode($this->filter));
        if(empty($data['data']))
        {
            $this -> error = 1;
            //$this -> return_sql = $sql;
            $this -> error_msg = "is empty data.";
            $this -> return_msg = array();
            $this->display();
        }
        else
        {
            unset($data['countSql'],
            $data['sql'],
            $data['isFirst'],
            $data['isLast'],
            $data['start']);//去掉返回的sql
            
            $this -> error = 0;
            //$this -> return_sql = $sql;
            $this -> return_msg = $data;
            $this->display();
        }
    }
}
?>
