<?php

/**
 *  -------------------------------------------------
 *   @file		: DiamondInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 16:13:09
 *   @update	:
 *  -------------------------------------------------
 */
class DiamondInfoModel extends Model {

    //切工(Cut) ：完美 EX   非常好 VG   好 G   一般 Fair
    public static $cut_arr = array('EX', 'VG', 'G', 'Fair');
    //抛光(Polish)	 完美 EX   非常好 VG   好 G   一般 Fair
    public static $polish_arr = array('EX', 'VG', 'G', 'Fair');
    //对称(Symmetry)	 完美 EX   非常好 VG   好 G   一般 Fair
    public static $symmetry_arr = array('EX', 'VG', 'G', 'Fair');
    //荧光(Fluorescence): 无 N   轻微 F   中度 M   强烈 S
    public static $fluorescence_arr = array('N', 'F', 'M', 'S','SLT');
    //颜色(Color): D	完全无色   E 无色   F 几乎无色   G   H   I 接近无色   J
    //public static $color_arr = array('D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'H', 'D-E', 'E-F', 'F-G', 'G-H', 'H-I', 'I-J', 'J-K');
    public static $color_arr = array("N","M","L","K-L","K","J-K","J","I-J","I","H-I","H","H+","G-H","G","F-G","F","E-F","E","D-E","D","黄","蓝","粉","橙","绿","红","香槟","格雷恩","紫","混色","蓝紫色","黑","变色","其他","白色","金色");
    //净度(Clarity) FL 完全洁净  IF 内部洁净  VVS1 极微瑕  VVS2  VS1 微瑕  VS2  SI1 小瑕  SI2
    //public static $clarity_arr = array('FL', 'IF', 'VVS1', 'VVS2', 'VS1', 'VS2', 'SI1', 'SI2');
    public static $clarity_arr = array("不分级","P","P1","I","I1","I2","SI","SI1","SI2","VS","VS1","VS2","VVS","VVS1","VVS2","IF","FL");
    //形状(Shape): 圆形   公主方形   祖母绿形   橄榄形   椭圆形   水滴形   心形  坐垫形   辐射形   方形辐射形   方形祖母绿   三角形
    public static $shape_arr = array(1 => '圆形', 2 => '公主方形', 3 => '祖母绿形', 4 => '橄榄形', 5 => '椭圆形', 6 => '水滴形', 7 => '心形', 8 => '坐垫形', 9 => '辐射形', 10 => '方形辐射形', 11 => '方形祖母绿', 12 => '三角形',13=>'戒指托',14=>'异形',15=>'梨形',16=>'阿斯切',17 => '马眼', 18 => '长方形', 19 => '雷迪恩');
    //证书类型 
    public static $cert_arr = array('HRD-D','GIA','HRD','IGI','DIA','AGL','EGL','NGTC','NGGC','HRD-S','NGSTC');
    public static $fromad_arr = array(1=>'kela',2=>'fiveonezuan',3=>'venus',4=>'dharam',5=>'diamondbyhk',6=>'diarough',7=>'emd',8=>'gd',9=>'jb',10=>'kapu',11=>'kgk',12=>'hy',13=>'leo',14=>'kiran',15=>'vir',16=>'karp',17=>'enjoy',18=>'changning', 19=>'kb',20=>'kg','21'=>'bluestar','22'=>'fulong','23'=>'kbgems','24'=>'sheelgems','25'=>'cdinesh','29'=>'SLK','31'=>'starrays','32'=>'shawn');
    public static $goodType_arr = array(1=>'现货',2=>'期货');
    
    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'diamond_info';
        $this->pk = 'goods_id';
        $this->_prefix = '';
        $this->_dataObject = array(
            "goods_id" => "",
            "goods_sn" => "商品编码",
            "goods_name" => "商品名称",
            "goods_number" => "商品数量",
            "market_price" => "市场价",
            "shop_price" => "BDD价",
            "member_price" => "会员价",
            "chengben_jia" => "成本价",
            "carat" => "石重",
            "clarity" => "净度",
            "cut" => "切工",
            "color" => "颜色",
            "shape" => "形状",
            "depth_lv" => "台深",
            "table_lv" => "台宽",
            "symmetry" => "对称",
            "polish" => "抛光",
            "fluorescence" => "荧光",
            "warehouse" => "库房",
            "cert" => "证书号类型",
            "cert_id" => "证书号",
            "gemx_zhengshu" => "gemx证书号",
            "status" => "状态：1启用 0停用",
            "add_time" => "添加时间",
            "is_active" => "0=默认，1=双11等活动",
            "from_ad" => "1=51钻，2=BDD",
            "good_type" => "1=现货，2=期货",
            "kuan_sn" => "款号"
            );
        parent::__construct($id, $strConn);
    }
    /**
     * 	pageList，分页列表 
     *
     * 	@url DiamondInfoController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true,$select="*",$prevent='') {
		if ($_SESSION['userName'] == '胥国凯') $str = "";
		else $str = " d.from_ad<254 AND ";
        if(isset($where['goods_sn']) && !empty($where['goods_sn'])){
            $str.= " d.`goods_sn` LIKE '".$where['goods_sn']."%' AND ";
        }
        if(isset($where['carat_min']) && !empty($where['carat_min'])){
            $str.= " d.`carat`>=".$where['carat_min']." AND ";
        }
        if(isset($where['carat_max']) && !empty($where['carat_max'])){
            $str.= " d.`carat`<=".$where['carat_max']." AND ";
        }
        if(isset($where['clarity']) && !empty($where['clarity'])){
            if(count($where['clarity'])==1){
                 $str.= "d.`clarity` ='".$where['clarity'][0]."' AND ";
            }else{
                $clarity = implode("','",$where['clarity']);
                $str.= "d.`clarity` in ('".$clarity."') AND ";
            }
        }
       
        if(isset($where['color']) && !empty($where['color'])){
            if(count($where['color'])==1){
                 $str.= "d.`color` ='".$where['color'][0]."' AND ";
            }else{
                $color = implode("','",$where['color']);
                $str.= "d.`color` in ('".$color."') AND ";
            }
        }
        if(isset($where['shape']) && !empty($where['shape'])){
            if(count($where['shape'])==1){
                 $str.= "d.`shape` ='".$where['shape'][0]."' AND ";
            }else{
                $shape = implode("','",$where['shape']);
                $str.= "d.`shape` in ('".$shape."') AND ";
            }
        }
        if(isset($where['cut']) && !empty($where['cut'])){
            if(count($where['cut'])==1){
                 $str.= "d.`cut` ='".$where['cut'][0]."' AND ";
            }else{
                $cut = implode("','",$where['cut']);
                $str.= "d.`cut` in ('".$cut."') AND ";
            }
        } 
        if(isset($where['polish']) && !empty($where['polish'])){
            if(count($where['polish'])==1){
                 $str.= "d.`polish` ='".$where['polish'][0]."' AND ";
            }else{
                $polish = implode("','",$where['polish']);
                $str.= "d.`polish` in ('".$polish."') AND ";
            }
        }
        if(!empty($where['symmetry'])){
            if(count($where['symmetry'])==1){
                 $str.= "d.`symmetry` ='".$where['symmetry'][0]."' AND ";
            }else{
                $symmetry = implode("','",$where['symmetry']);
                $str.= "d.`symmetry` in ('".$symmetry."') AND ";
            }
        }
		$join_table=" ";
        $join_where=" ";
        if(!empty($where['fluorescence'])){
            if(count($where['fluorescence'])==1){
                 $str.= "d.`fluorescence` ='".$where['fluorescence'][0]."' AND ";
            }else{
                $fluorescence = implode("','",$where['fluorescence']);
                $str.= "d.`fluorescence` in ('".$fluorescence."') AND ";
            }
        }
        
        if(!empty($where['cert'])){
            if(count($where['cert'])==1){
                $str.= " d.`cert` ='".$where['cert'][0]."' AND ";
            }else{
                $cert = implode("','",$where['cert']);
                $str.= " d.`cert` in ('".$cert."') AND ";
            }
        }
        //库房
        if(!empty($where['warehouse'])){
            $str.= " d.`warehouse`='".$where['warehouse']."' AND ";
        }
        //供应商
        if(!empty($where['from_ad'])){
            $str.= " d.`from_ad`='".$where['from_ad']."' AND ";
        }
        //货品类型
        if(!empty($where['good_type'])){
            $str.= " d.`good_type`='".$where['good_type']."' AND ";
        }
        if(isset($where['kelan_price_min']) && !empty($where['kelan_price_min'])){
            $str.= " d.`shop_price`>=".$where['kelan_price_min']." AND ";
        }
        if(isset($where['kelan_price_max']) && !empty($where['kelan_price_max'])){
            $str.= " d.`shop_price`<=".$where['kelan_price_max']." AND ";
        }
        if(!empty($where['cert_id'])){
            $str.= " d.`cert_id`='".$where['cert_id']."' AND ";
        }
        //国际证书
        if(!empty($where['gemx_zhengshu'])){
            $str.= " d.`gemx_zhengshu`='".$where['gemx_zhengshu']."' AND ";
        }
        //天生一对钻重
        //if(!empty($where['kuan_sn'])){
        $join_table=" ";
        $join_where=" 1 AND ";
        if(!empty($where['s_carats_tsyd1']) || !empty($where['e_carats_tsyd1']) || !empty($where['s_carats_tsyd2']) || !empty($where['e_carats_tsyd2'])){
            $str.= " d.`kuan_sn`!='' AND d.`cert`='HRD-D' ";
				 if(!isset($where['s_carats_tsyd1']) &&
                    !isset($where['e_carats_tsyd1']) &&
                    !isset($where['s_carats_tsyd2']) &&
                    !isset($where['e_carats_tsyd2']) ){
                }else{
					$join_table=",`diamond_info` as e";
                    $join_where=" d.kuan_sn=e.kuan_sn  AND e.goods_id != d.goods_id AND";
                    $tsyd_sql=$this->getTsydSQL($where);
					//if(!empty($tsyd_sql)){
						$str.=$tsyd_sql;
					//}
                }
        }
        //天生一对
        if(!empty($where['ysyd'])){
            $str.= " d.`kuan_sn`!='' AND ";
        }
        //星耀裸钻
        if(!empty($where['gm'])){
            $str.= " (d.`gemx_zhengshu`!='' or d.cert ='HRD-S') AND ";
        }
        //是否有活动
        if(!empty($where['is_active'])){
            $str.= " d.`is_active`='".$where['is_active']."' AND ";
        }
        //状态
        if(!empty($where['status'])){
            $str.= " d.`status`='".$where['status']."' AND ";
        }

         if($prevent ==1){
            $sql1 = "select cert_id from diamond_info_prevent";
            $cert_ids = $this->db()->getAll($sql1);
            $cert_ids = array_column($cert_ids,'cert_id');
            $str .="cert_id not in (".implode(',',$cert_ids).") AND ";         
        }

        $sql = "SELECT $select FROM `" . $this->table() . "` as d $join_table";
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE $join_where" . $str;
        }

        $sql .= " ORDER BY d.`goods_id` DESC";
		//echo $sql."<br>";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }
    /**
	*
	*/
    public function getTsydSQL($filter){
        $where='';
        $ss=$one=$yi='';
        if(isset($filter['s_carats_tsyd1']) && !empty($filter['s_carats_tsyd1'])){
            $ss = $filter['s_carats_tsyd1'];
            $one.= "d.carat >= '$ss'";
            $yi.= "e.carat >= '$ss'";
        }
        $se=$two=$er='';
        if(isset($filter['e_carats_tsyd1']) && !empty($filter['e_carats_tsyd1'])){
            $se = $filter['e_carats_tsyd1'];
            if($ss){
                $two .= ' AND ';
                $er .= ' AND ';
            }
            $two.= " d.carat <= '$se'";
            $er .= " e.carat <= '$se'";
        }
        $es=$three=$shan='';
        if(isset($filter['s_carats_tsyd2']) && !empty($filter['s_carats_tsyd2'])){
            $es = $filter['s_carats_tsyd2'];
            $three.= "e.carat >= '$es'";
            $shan.= "d.carat >= '$es'";
        }
        $ee=$four=$shi='';
        if(isset($filter['e_carats_tsyd2']) && !empty($filter['e_carats_tsyd2'])){
            $ee = $filter['e_carats_tsyd2'];
            if($es){
                $four .= ' AND ';
                $shi .= ' AND ';
            }
            $four.= " e.carat <= '$ee'";
            $shi.= " d.carat <= '$ee'";
        }
        $and1 =  $and2 ='';
        $lg1 = '';
        $rg1 = '';
        if($one || $two){
            $lg1 = '(';
            $rg1 = ')';
        }
        $lg3 = '';
        $rg3 = '';
        if($yi || $er){
            $lg3 = '(';
            $rg3 = ')';
        }
        $and1 = '';
        $lg2 = '';
        $rg2 = '';
        if($three || $four){
            if($one || $two){
                $and1 = ' AND ';
            }
            $lg2 = '(';
            $rg2 = ')';
        }
        $and2 = '';
        $lg4 = '';
        $rg4 = '';
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
     * 	getDiamond_all，下载
     *
     * 	@url DiamondInfoController/getDiamond_all
     */
    function getDiamond_all($where,$start,$limit,$select="*") {
        $str = '';
        if(isset($where['goods_sn']) && !empty($where['goods_sn'])){
            $str.= " d.`goods_sn` LIKE '".$where['goods_sn']."%' AND ";
        }
        if(isset($where['carat_min']) && !empty($where['carat_min'])){
            $str.= " d.`carat`>=".$where['carat_min']." AND ";
        }
        if(isset($where['carat_max']) && !empty($where['carat_max'])){
            $str.= " d.`carat`<=".$where['carat_max']." AND ";
        }
        if(isset($where['clarity']) && !empty($where['clarity'])){
            if(count($where['clarity'])==1){
                 $str.= "d.`clarity` ='".$where['clarity'][0]."' AND ";
            }else{
                $clarity = implode("','",$where['clarity']);
                $str.= "d.`clarity` in ('".$clarity."') AND ";
            }
        }
       
        if(isset($where['color']) && !empty($where['color'])){
            if(count($where['color'])==1){
                 $str.= "d.`color` ='".$where['color'][0]."' AND ";
            }else{
                $color = implode("','",$where['color']);
                $str.= "d.`color` in ('".$color."') AND ";
            }
        }
        if(isset($where['shape']) && !empty($where['shape'])){
            if(count($where['shape'])==1){
                 $str.= "d.`shape` ='".$where['shape'][0]."' AND ";
            }else{
                $shape = implode("','",$where['shape']);
                $str.= "d.`shape` in ('".$shape."') AND ";
            }
        }
        if(isset($where['cut']) && !empty($where['cut'])){
            if(count($where['cut'])==1){
                 $str.= "d.`cut` ='".$where['cut'][0]."' AND ";
            }else{
                $cut = implode("','",$where['cut']);
                $str.= "d.`cut` in ('".$cut."') AND ";
            }
        } 
        if(isset($where['polish']) && !empty($where['polish'])){
            if(count($where['polish'])==1){
                 $str.= "d.`polish` ='".$where['polish'][0]."' AND ";
            }else{
                $polish = implode("','",$where['polish']);
                $str.= "d.`polish` in ('".$polish."') AND ";
            }
        }
        if(!empty($where['symmetry'])){
            if(count($where['symmetry'])==1){
                 $str.= "d.`symmetry` ='".$where['symmetry'][0]."' AND ";
            }else{
                $symmetry = implode("','",$where['symmetry']);
                $str.= "d.`symmetry` in ('".$symmetry."') AND ";
            }
        }
		$join_table=" ";
        $join_where=" ";
        if(!empty($where['fluorescence'])){
            if(count($where['fluorescence'])==1){
                 $str.= "d.`fluorescence` ='".$where['fluorescence'][0]."' AND ";
            }else{
                $fluorescence = implode("','",$where['fluorescence']);
                $str.= "d.`fluorescence` in ('".$fluorescence."') AND ";
            }
        }
        
        if(!empty($where['cert'])){
            if(count($where['cert'])==1){
                $str.= " d.`cert` ='".$where['cert'][0]."' AND ";
            }else{
                $cert = implode("','",$where['cert']);
                $str.= " d.`cert` in ('".$cert."') AND ";
            }
        }
        //库房
        if(!empty($where['warehouse'])){
            $str.= " d.`warehouse`='".$where['warehouse']."' AND ";
        }
        //供应商
        if(!empty($where['from_ad'])){
            $str.= " d.`from_ad`='".$where['from_ad']."' AND ";
        }
        //货品类型
        if(!empty($where['good_type'])){
            $str.= " d.`good_type`='".$where['good_type']."' AND ";
        }
        if(isset($where['kelan_price_min']) && !empty($where['kelan_price_min'])){
            $str.= " d.`shop_price`>=".$where['kelan_price_min']." AND ";
        }
        if(isset($where['kelan_price_max']) && !empty($where['kelan_price_max'])){
            $str.= " d.`shop_price`<=".$where['kelan_price_max']." AND ";
        }
        if(!empty($where['cert_id'])){
            $str.= " d.`cert_id`='".$where['cert_id']."' AND ";
        }
        //国际证书
        if(!empty($where['gemx_zhengshu'])){
            $str.= " d.`gemx_zhengshu`='".$where['gemx_zhengshu']."' AND ";
        }
        //天生一对钻重
        //if(!empty($where['kuan_sn'])){
        $join_table=" ";
        $join_where=" 1 AND ";
        if(!empty($where['s_carats_tsyd1']) || !empty($where['e_carats_tsyd1']) || !empty($where['s_carats_tsyd2']) || !empty($where['e_carats_tsyd2'])){
            $str.= " d.`kuan_sn`!='' AND d.`cert`='HRD-D' ";
				 if(!isset($where['s_carats_tsyd1']) &&
                    !isset($where['e_carats_tsyd1']) &&
                    !isset($where['s_carats_tsyd2']) &&
                    !isset($where['e_carats_tsyd2']) ){
                }else{
					$join_table=",`diamond_info` as e";
                    $join_where=" d.kuan_sn=e.kuan_sn  AND e.goods_id != d.goods_id AND";
                    $tsyd_sql=$this->getTsydSQL($where);
					//if(!empty($tsyd_sql)){
						$str.=$tsyd_sql;
					//}
                }
        }
        //天生一对
        if(!empty($where['ysyd'])){
            $str.= " d.`kuan_sn`!='' AND ";
        }
        //星耀裸钻
        if(!empty($where['gm'])){
            $str.= " d.`gemx_zhengshu`!='' AND ";
        }
        //是否有活动
        if(!empty($where['is_active'])){
            $str.= " d.`is_active`='".$where['is_active']."' AND ";
        }
        //状态
        if(!empty($where['status'])){
            $str.= " d.`status`='".$where['status']."' AND ";
        }
        $sql = "SELECT $select FROM `" . $this->table() . "` as d $join_table";
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE $join_where" . $str;
        }
        $sql .= " ORDER BY d.`goods_id` DESC LIMIT $start, $limit";
		//echo $sql."<br>";
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
     * 获取有多少条数据
     * @param type $param
     * @return type
     */
    function getDiamondCount() {
        return $this->db()->getOne("select count(*) from `{$this->table()}`");
    }

    /**
     * 	getListdownLoad，下载
     *
     * 	@url DiamondInfoController/getListdownLoad
     */
    function getListdownLoad($where) {
        $str='';
        $sql = "SELECT `goods_id`,`goods_sn`,`goods_name`,`goods_number`,`from_ad`,`good_type`,`market_price`,`shop_price`,`member_price`,`chengben_jia`,`carat`,`clarity`,`cut`,`color`,`shape`,`depth_lv`,`table_lv`,`symmetry`,`polish`,`fluorescence`,`cert`,`cert_id`,`gemx_zhengshu`,`status`,`add_time`,`is_active` FROM `" . $this->table() . "`";
        if(isset($where['goods_sn']) && !empty($where['goods_sn'])){
            $str.= "`goods_sn` LIKE '".$where['goods_sn']."%' AND ";
        }
        if(isset($where['carat_min']) && !empty($where['carat_min'])){
            $str.= " `carat`>=".$where['carat_min']." AND ";
        }
        if(isset($where['carat_max']) && !empty($where['carat_max'])){
            $str.= " `carat`<=".$where['carat_max']." AND ";
        }
        if(isset($where['clarity']) && !empty($where['clarity'])){
            if(count($where['clarity'])==1){
                 $str.= "`clarity` ='".$where['clarity'][0]."' AND ";
            }else{
                $clarity = implode("','",$where['clarity']);
                $str.= "`clarity` in ('".$clarity."') AND ";
            }
        }
       
        if(isset($where['color']) && !empty($where['color'])){
            if(count($where['color'])==1){
                 $str.= "`color` ='".$where['color'][0]."' AND ";
            }else{
                $color = implode("','",$where['color']);
                $str.= "`color` in ('".$color."') AND ";
            }
        }
        if(isset($where['shape']) && !empty($where['shape'])){
            if(count($where['shape'])==1){
                 $str.= "`shape` =".$where['shape'][0]." AND ";
            }else{
                $shape = implode(",",$where['shape']);
                $str.= "`shape` in (".$shape.") AND ";
            }
        }
        
        if(isset($where['cut']) && !empty($where['cut'])){
            if(count($where['cut'])==1){
                 $str.= "`cut` ='".$where['cut'][0]."' AND ";
            }else{
                $cut = implode("','",$where['cut']);
                $str.= "`cut` in ('".$cut."') AND ";
            }
        } 
        if(isset($where['polish']) && !empty($where['polish'])){
            if(count($where['polish'])==1){
                 $str.= "`polish` ='".$where['polish'][0]."' AND ";
            }else{
                $polish = implode("','",$where['polish']);
                $str.= "`polish` in ('".$polish."') AND ";
            }
        }
        if(!empty($where['symmetry'])){
            if(count($where['symmetry'])==1){
                 $str.= "`symmetry` ='".$where['symmetry'][0]."' AND ";
            }else{
                $symmetry = implode("','",$where['symmetry']);
                $str.= "`symmetry` in ('".$symmetry."') AND ";
            }
        }
        if(!empty($where['fluorescence'])){
            if(count($where['fluorescence'])==1){
                 $str.= "`fluorescence` ='".$where['fluorescence'][0]."' AND ";
            }else{
                $fluorescence = implode("','",$where['fluorescence']);
                $str.= "`fluorescence` in ('".$fluorescence."') AND ";
            }
        }
        
        if(!empty($where['cert'])){
            if(count($where['cert'])==1){
                $str.= "`cert` ='".$where['cert'][0]."' AND ";
            }else{
                $cert = implode("','",$where['cert']);
                $str.= "`cert` in ('".$cert."') AND ";
            }
        }

        if(!empty($where['cert_id'])){
            $str.= " `cert_id`='".$where['cert_id']."' AND ";
        }
        //是否有活动
        if(!empty($where['is_active'])){
            $str.= " `is_active`='".$where['is_active']."' AND ";
        }
        //状态
        if(!empty($where['status'])){
            $str.= " `status`='".$where['status']."' AND ";
        }
        
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE 1 AND " . $str;
        }
        
        $sql .= " ORDER BY `goods_id` DESC";
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
     * 	getAllList，取所有现货
     *
     * 	@url DiamondInfoController/getAllList
     */
    function getAllList($select="*") {
        $sql = "SELECT $select FROM `" . $this->table() . "` WHERE `good_type`=1";
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
     * 	getAllList，取所有
     *
     * 	@url DiamondInfoController/getAllList
     */
    function getAlls($select="*") {
        $sql = "SELECT $select FROM `" . $this->table() . "`";
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
     * 	getRowBygoods_sn，取一条
     *
     * 	@url DiamondInfoController/getRowBygoods_sn
     */
    function getRowBygoods_sn($goods_sn) {
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE `goods_sn`='".$goods_sn."'";
        $data = $this->db()->getRow($sql);
        return $data;
    }

    /**
     * 	getRowBycert_id，取一条
     *
     * 	@url DiamondInfoController/getRowBycert_id
     */
    function getRowBycert_id($cert_id) {
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE `cert_id`='".$cert_id."'";
        $data = $this->db()->getRow($sql);
        return $data;
    }

    /**
     * 	deletebycert_id，删除
     *
     * 	@url DiamondInfoController/deletebycert_id
     */
    function deletebycert_id($cert,$cert_id) {
        $sql = "DELETE FROM `".$this->table()."` WHERE `cert` = '".$cert."' AND `cert_id`='".$cert_id."'";
		return $this->db()->query($sql);
    }

    function deletebygoods_sn($goods_sn) {
        $sql = "DELETE FROM `".$this->table()."` WHERE `goods_sn` = '".$goods_sn."'";
        return $this->db()->query($sql);
    }

    function checkDiamond($shape,$cut,$color,$clarity,$polish,$fluorescence,$symmetry,$cert){
        $error=0;
        $error_type=array();
        if(!in_array($shape,self::$shape_arr)){
            $error = 1;
            $error_type[]='形状';
        }
        if($shape!='圆形' && !in_array($cut,self::$cut_arr)){
            $error = 1;
            $error_type[]='切工';
        }
        if(!in_array($color,self::$color_arr)){
            //$error = 1;
            //$error_type[]='颜色';
        }
        if(!in_array($clarity,self::$clarity_arr)){
            //$error = 1;
            //$error_type[]='净度';
        }
        if(!in_array($polish,self::$polish_arr)){
            //$error = 1;
            //$error_type[]='抛光';
        }
        if(!in_array($fluorescence,self::$fluorescence_arr)){
            //$error = 1;
            //$error_type[]='荧光';
        }
        if(!in_array($symmetry,self::$symmetry_arr)){
            //$error = 1;
            //$error_type[]='对称';
        }
        if(!in_array($cert,self::$cert_arr)){
            //$error = 1;
            //$error_type[]='证书';
        }
        return array($error,$error_type);
    }

    public function getShapeId($shape)
    {

        foreach(self::$shape_arr as $key => $val)
        { 
            if($val == $shape){
                return $key;
            }
        }
        return false;
    }
    
    //取所有形状
    public static function getShapeName()
    {
           $Shape_arr=self::$shape_arr;
           return $Shape_arr;
    }

    //取所有来源
    public static function getForm_ad()
    {
           $fromad_arr=self::$fromad_arr;
           return $fromad_arr;
    }   

    //取后台打开显示的供应商来源
    public function getForm_ad_only()
    {
        $sql="select `vendor_id` from `diamond_vendor` where `show`=1 ;";
        $r = $this->db()->getAll($sql);
        $fromad_arr = array();
        $base_arr = self::$fromad_arr;
        foreach ($r as $key => $value) {
            $idx = $value['vendor_id'];
            if (array_key_exists($idx, $base_arr)) {
                $fromad_arr[$idx] = $base_arr[$idx];
            }
        }
        return $fromad_arr;
    }  

    //取一条来源
    public static function getOneForm_ad($form_ad)
    {
        foreach(self::$fromad_arr as $key => $val)
        { 
            if($key == $form_ad){
                return $val;
            }
        }
        return false;
    }    

    //批量删除
	public function delManyDelete ($ids)
	{
		if(count($ids)==0)
		{
			return true;
		}
		$sql = "DELETE FROM `" . $this->table() . "` WHERE `goods_id` IN (".implode(",",$ids).")";
		return $this->db()->query($sql);
	}

    /**
     * 	deleteXh，删除指定库房现货
     *
     * 	@url DiamondInfoController/deleteXh
     */
    function deleteXh($warehouse,$good_type=1) {
        $sql = "DELETE FROM `".$this->table()."` WHERE `warehouse` IN ('".$warehouse."') AND `good_type`='".$good_type."'";
		return $this->db()->query($sql);
    }

    /**
     *
     * 天生一对 款号
     */
    public function get_diamond_by_kuan_sn($filter)
    {
        $sql="select * from `" . $this->table() . "` where 1 AND `kuan_sn`='".$filter."'";
        return $this->db()->getAll($sql);
    }

    /**
     *
     * 获取库房
     */
    public function get_warehouse_all($type=1)
    {
        $keys[]='diamond_warehouse';
        $vals[]=$type;
        
        if (SYS_SCOPE == 'zhanting') {
        	//$keys[]='zy';
        	//$vals[]= "1";
        }
        
        $ret=ApiModel::warehouse_api($keys,$vals,'GetWarehouseList');
        return $ret;  
    }

    public function getDiamondInfo($certList) {
        $result = array();
		if(!empty($certList)){
			$sqlStr = "";
			if(is_array($certList)){
				$sqlStr = implode("','",$certList);
			}else{
				$sqlStr = $certList;
			}
			$sql = "select * from diamond_info where cert_id in ('{$sqlStr}')";
			$result = $this->db()->getAll($sql);
		}
		return $result;
    }

    public function offlineDiamond($type,$certList){
		$result = 0;
		if(!empty($certList) || !in_array($type,array(1,2))){
			$sqlStr = "";
			if(is_array($certList)){
				$sqlStr = implode("','",$certList);
			}else{
				$sqlStr = $certList;
			}
			$sql = "update  diamond_info set status = {$type} where cert_id in ('{$sqlStr}')";
			$result = $this->db()->query($sql);
		}
		return $result;
	}
	
	public function transfer_qihuo($data, $from_ad){
	    if(empty($data)){
	        return '数据为空';
	    }
	    unset($data[0]);
	    $f = array('lot_no', 'shape', 'carat', 'color', 'clarity', 'cut','polish','sym', 'fluor', 'cert_type','cert_no', 'gj_price','zk','us_price', 'ori_price', 'sale_price','gemx_zhengshu');
	    
	    $f = implode('`,`',$f);
	    $pdo = $this->db()->db();//pdo对象
	    try{
	        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
	        $pdo->beginTransaction();//开启事务
	        $i = 0;
	        $sql = "truncate table dia_qihuo_tmp; ";
	        foreach($data as $key=>$val){
	            $val_str = array();
	            foreach ($val as $col) {
	                $val_str[] = "'".$col."'";
	            }
	            
	            $sql .= "insert into dia_qihuo_tmp(`$f`) value(".implode(',', $val_str).");";
	            $i++;
	            
	            if ($i % 10 == 0) {
	                $pdo->query($sql);
	                $sql = "";
	            }
	        }
	        
	        if ($i % 10 > 0) {
	            $pdo->query($sql);
	        }
            	        
            	        //normalize 
            	        $normal_sql = "
            DELETE from dia_qihuo_tmp where LENGTH(ifnull(sale_price,'')) = 0 or cert_no is null;

            update diamond_info inner join dia_qihuo_tmp z on z.cert_no = diamond_info.cert_id 
            set 
            diamond_info.goods_sn = z.lot_no, 
            diamond_info.goods_name = CONCAT(z.carat,'克拉/ct ', z.clarity, '净度 ', z.color, '颜色 ', z.cut, '切工'),
            diamond_info.from_ad = '{$from_ad}',
            diamond_info.market_price = z.sale_price,
            diamond_info.shop_price = z.sale_price,
            diamond_info.member_price = z.sale_price,
            diamond_info.chengben_jia = z.ori_price,
            diamond_info.guojibaojia = z.gj_price,
            diamond_info.us_price_source = z.us_price,
            diamond_info.source_discount = z.zk,
            diamond_info.carat = z.carat,
            diamond_info.clarity = z.clarity,
            diamond_info.cut = z.cut,
            diamond_info.shape = z.shape,
            diamond_info.symmetry = z.sym,
            diamond_info.polish = z.Polish,
            diamond_info.fluorescence = z.fluor,
            diamond_info.cert = z.cert_type,
            diamond_info.status = 1,
            diamond_info.is_active =1,
            diamond_info.pifajia = 0,
            diamond_info.gemx_zhengshu =z.gemx_zhengshu,
            diamond_info.pifajia_mode = ''";
           $pdo->query($normal_sql);
            $normal_sql =" INSERT INTO diamond_info (goods_sn, goods_name, goods_number, from_ad, good_type, market_price, shop_price, member_price, chengben_jia, carat, clarity, cut, color, shape, symmetry, polish,fluorescence, warehouse, guojibaojia, us_price_source, source_discount, cert, cert_id,gemx_zhengshu, status, add_time, is_active )
            SELECT 
            z.lot_no AS goods_sn, 
            CONCAT(z.carat,'克拉/ct ', z.clarity, '净度 ', z.color, '颜色 ', z.cut, '切工') as goods_name,
            1 as goods_number,
            '{$from_ad}' as from_ad,
            2 as good_type,
            z.sale_price as market_price,
            z.sale_price as shop_price,
            z.sale_price as member_price,
            z.ori_price as chengben_jia,
            z.carat as carat,
            z.clarity as clarity,
            z.cut as cut,
            z.color as color,
            z.shape as shape,
            z.sym as symmetry,
            z.polish as polish,
            z.fluor as fluorescence,
            'COM' as warehosue,
            z.gj_price as guojibaojia,
            z.us_price AS us_price_source,
            z.zk as source_discount,
            z.cert_type as cert,
            z.cert_no as cert_id,
            z.gemx_zhengshu,
            1 as status,
            now() as add_time,
            1 as is_active
            from dia_qihuo_tmp z left join diamond_info d on z.Cert_No = d.cert_id
            where d.cert_id is null";
            $pdo->query($normal_sql);
            $normal_sql="delete d from diamond_info d left join dia_qihuo_tmp z on z.cert_no = d.cert_id 
            where z.cert_no is null and d.from_ad in ('{$from_ad}') and d.add_time < '".date('Y-m-d')."'";
            	     //$normal_sql="update sdb set b='' where id=1";   
            	        $pdo->query($normal_sql);
            	
	    }catch(Exception $e){//捕获异常
	        $pdo->rollback();//事务回滚
	        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	        return $e->getMessage();
	    }
	    $pdo->commit();//如果没有异常，就提交事务
	    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
	    return true;
	}


    //总部展厅裸钻下载查询
    public function GetDiamondList_api($filter)
    {
        
        $s_time = microtime();
        //$this -> filter["page"] = 3;  //当前页 
        $page = intval($filter["page"]) <= 0 ? 1 : intval($filter["page"]);
        $pageSize = intval($filter["pageSize"]) > 0 ? intval($filter["pageSize"]) : 15;
        $goods_sn= !empty($filter['goods_sn']) ? $filter['goods_sn'] :'';//货号
        $no_goods_id= !empty($filter['no_goods_id']) ? $filter['no_goods_id'] : '';//排除商品
        $goods_name= !empty($filter['goods_name']) ? $filter['goods_name'] : '';//商品名称
        $from_ad= !empty($filter['from_ad']) ? $filter['from_ad'] : '';//来源
        $good_type= !empty($filter['good_type']) ? $filter['good_type'] : '';//货品类型
        $carat_min= !empty($filter['carat_min']) ? floatval($filter['carat_min']) : '';//最小石重
        $carat_max= !empty($filter['carat_max']) ? floatval($filter['carat_max']) : '';//最大石重
        $price_min= !empty($filter['price_min']) ? floatval($filter['price_min']) : '';//最小价格
        $price_max= !empty($filter['price_max']) ? floatval($filter['price_max']) : '';//最大价格
        $clarity= !empty($filter['clarity']) ? $filter['clarity'] : '';//净度
        $cut= !empty($filter['cut']) ? $filter['cut'] : '';//切工
        $color= !empty($filter['color']) ? $filter['color'] : '';//颜色
        $shape= !empty($filter['shape']) ? $filter['shape'] : '';//形状
        $symmetry= !empty($filter['symmetry']) ? $filter['symmetry'] : '';//对称
        $polish= !empty($filter['polish']) ? $filter['polish'] : '';//抛光
        $fluorescence= !empty($filter['fluorescence']) ? $filter['fluorescence'] : '';//荧光
        $cert=!empty($filter['cert']) ? $filter['cert'] : '';//证书号类型
        $cert_id= !empty($filter['cert_id']) ? $filter['cert_id'] : '';//证书号
        $warehouse= !empty($filter['warehouse']) ? $filter['warehouse'] : '';//库房
        //$hrd_s_warehouse= !empty($filter['hrd_s_warehouse']) ? $filter['hrd_s_warehouse'] : '';//展厅只能看直营店星耀钻
        $no_warehouse= !empty($filter['no_warehouse']) ? $filter['no_warehouse'] : '';//排除库房
        $gm= !empty($filter['gm']) ? $filter['gm'] : '';//星耀证书
        $kuan_sn= !empty($filter['kuan_sn']) ? $filter[''] : '';//天生一对
        $gemx_zhengshu= !empty($filter['gemx_zhengshu']) ? $filter['gemx_zhengshu'] : '';//gexm证书号
        $status= !empty($filter['status']) ? $filter['status'] : '';//状态
        $is_active= !empty($filter['is_active']) ? $filter['is_active'] : '';//活动状态
        $s_carats_tsyd1= !empty($filter['s_carats_tsyd1']) ? $filter['s_carats_tsyd1'] : '';//天生一对钻1重小
        $e_carats_tsyd1= !empty($filter['e_carats_tsyd1']) ? $filter['e_carats_tsyd1'] : '';//天生一对钻1重大
        $s_carats_tsyd2= !empty($filter['s_carats_tsyd2']) ? $filter['s_carats_tsyd2'] : '';//天生一对钻2重小
        $e_carats_tsyd2= !empty($filter['e_carats_tsyd2']) ? $filter['e_carats_tsyd2'] : '';//天生一对钻2重大
        $zdj= !empty($filter['zdj']) ? $filter[''] : '';//价格排序
        $stonesort= !empty($filter['stonesort']) ? $filter['stonesort'] : '';//石重排序
        $yansesort= !empty($filter['yansesort']) ? $filter['yansesort'] : '';//颜色排序
        $jdsort= !empty($filter['jdsort']) ? $filter['jdsort'] : '';//净度排序
        $not_from_ad= !empty($filter['not_from_ad']) ? $filter['not_from_ad'] : '';//来源
        $pf_price_min= !empty($filter['pf_price_min']) ? floatval($filter['pf_price_min']) : '';//最小价格
        $pf_price_max= !empty($filter['pf_price_max']) ? floatval($filter['pf_price_max']) : '';//最大价格
        $include_img= !empty($filter['include_img']) ? $filter['include_img'] : '';//是否包含图片
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
        }*/      
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
                    $tsyd_sql=$this->getTsydSQL($filter);
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
            $where .= " and (d.img like 'http://diamonds.kirangems.com/GemKOnline/DiaSearch/appVideo.jsp%' or d.img like 'https://diamanti.s3.amazonaws.com/images/diamond%')";
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
        if(false && isset($filter['ssy_active']) && (empty($kuan_sn) || $kuan_sn=="no_tsyd")){    
            //双十一特价钻 begin,仅当传参ssy开启双十一特价钻搜索识别  by gaopeng
            if($filter['ssy_active']==1){
                $where .=" and d.cert_id in(select cert_id from diamond_ssy_tejia)";
            }else{
                $where .=" and d.cert_id not in(select cert_id from diamond_ssy_tejia)";
            }
            /*
            $sql   = "SELECT COUNT(*) FROM `diamond_info` as d $join_table where $join_where ".$where;
            $record_count   =  $this -> db() ->getOne($sql);
            $page_count     = $record_count > 0 ? ceil($record_count / $pageSize) : 1;
            $page = $page > $page_count ? $page_count : $page;
            
            $isFirst = $page > 1 ? false : true;
            $isLast = $page < $page_count ? false : true;
            $start = ($page == 0) ? 1 : ($page - 1) * $pageSize + 1;
            */
            $join_table .=" left join `diamond_ssy_tejia` s on s.cert_id=d.cert_id";
            $sql = "select d.`goods_id`,d.`goods_sn`,d.`goods_name`,d.`goods_number`,d.`from_ad`,d.`good_type`,d.`market_price`,d.`shop_price`,s.`special_price`,d.`member_price`,d.`chengben_jia`,d.`carat`,d.`clarity`,d.`cut`,d.`color`,d.`shape`,d.`depth_lv`,d.`table_lv`,d.`symmetry`,d.`polish`,d.`fluorescence`,d.`warehouse`,d.`cert`,d.`cert_id`,d.`gemx_zhengshu`,d.`status`,d.`add_time`,d.`is_active`,d.`kuan_sn`, d.pifajia,d.img from `diamond_info` as d $join_table WHERE $join_where ".$where.$_order_by_str." LIMIT " . ($page - 1) * $pageSize . ",$pageSize";
            //双十一特价钻 end
        }else{          
            //默认搜索
            /*
            $sql   = "SELECT COUNT(*) FROM `diamond_info` as d $join_table where $join_where ".$where;
            $record_count   =  $this -> db() ->getOne($sql);
            $page_count     = $record_count > 0 ? ceil($record_count / $pageSize) : 1;
            $page = $page > $page_count ? $page_count : $page;
            
            $isFirst = $page > 1 ? false : true;
            $isLast = $page < $page_count ? false : true;
            $start = ($page == 0) ? 1 : ($page - 1) * $pageSize + 1;
            //var_dump($start,123);die;
            */
            $sql = "select d.`goods_id`,d.`goods_sn`,d.`goods_name`,d.`goods_number`,d.`from_ad`,d.`good_type`,d.`market_price`,d.`shop_price`,d.`member_price`,d.`chengben_jia`,d.`carat`,d.`clarity`,d.`cut`,d.`color`,d.`shape`,d.`depth_lv`,d.`table_lv`,d.`symmetry`,d.`polish`,d.`fluorescence`,d.`warehouse`,d.`cert`,d.`cert_id`,d.`gemx_zhengshu`,d.`status`,d.`add_time`,d.`is_active`,d.`kuan_sn`,d.pifajia,d.img from `diamond_info` as d $join_table WHERE $join_where ".$where.$_order_by_str." LIMIT " . ($page - 1) * $pageSize . ",$pageSize";
        }
        $res = $this -> db() -> getAll($sql);
        //file_put_contents('diamond.log', $sql);
        //$content = array("page" => $page, "pageSize" => $pageSize, "recordCount" => $record_count, "data" => $res,'pageCount'=>$page_count,'isFirst'=>$isFirst,'isLast'=>$isLast,'start'=>$start,'sql'=>$sql);
        $content = $res;
        return $content;
    }


        public function getCompanyName($code) {           
            $sql = "SELECT `whr`.`company_name` FROM warehouse_shipping.`warehouse` as `wh`,warehouse_shipping.`warehouse_rel` as `whr` WHERE `wh`.`id`=`whr`.`warehouse_id` AND `wh`.`is_delete`=1 AND `wh`.`diamond_warehouse`=1 and `wh`.`code`='{$code}'";
            $arr = $this->db()->getOne($sql);
            return $arr;
        }

        public function get_all_diamond_warehouse_company(){
           $sql="SELECT `wh`.code,`whr`.`company_name` FROM warehouse_shipping.`warehouse` as `wh`,warehouse_shipping.`warehouse_rel` as `whr` WHERE `wh`.`id`=`whr`.`warehouse_id` AND `wh`.`is_delete`=1 AND `wh`.`diamond_warehouse`=1 "; 
           $res=$this->db()->getAll($sql);
           $company=array();
           foreach ($res as $key => $v) {
               $company[$v['code']]=$v['company_name'];
           }
           return $company;
        }

        public function get_diamond_info_all_row($cert_id){
            $sql="SELECT `source_discount` FROM diamond_info_all WHERE cert_id = '{$cert_id}'";
            $row=$this->db()->getRow($sql);
            return $row;
        }


}
?>