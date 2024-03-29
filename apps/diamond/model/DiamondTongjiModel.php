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
class DiamondTongjiModel extends Model {

    //切工(Cut) ：完美 EX   非常好 VG   好 G   一般 Fair
    public static $Cut_arr = array('EX', 'VG', 'G', 'Fair');
    //抛光(Polish)	 完美 EX   非常好 VG   好 G   一般 Fair
    public static $Polish_arr = array('EX', 'VG', 'G', 'Fair');
    //对称(Symmetry)	 完美 EX   非常好 VG   好 G   一般 Fair
    public static $Symmetry_arr = array('EX', 'VG', 'G', 'Fair');
    //荧光(Fluorescence): 无 N   轻微 F   中度 M   强烈 S
    public static $Fluorescence_arr = array('N', 'F', 'M', 'S');
    //颜色(Color): D	完全无色   E 无色   F 几乎无色   G   H   I 接近无色   J
    public static $Color_arr = array('D', 'E', 'F', 'G', 'H', 'I', 'J');
    //净度(Clarity) FL 完全洁净  IF 内部洁净  VVS1 极微瑕  VVS2  VS1 微瑕  VS2  SI1 小瑕  SI2
    public static $Clarity_arr = array('FL', 'IF', 'VVS1', 'VVS2', 'VS1', 'VS2', 'SI1', 'SI2');
    //形状(Shape): 圆形   公主方形   祖母绿形   橄榄形   椭圆形   水滴形   心形  坐垫形   辐射形   方形辐射形   方形祖母绿   三角形
    public static $Shape_arr = array(1 => '圆形', 2 => '公主方形', 3 => '祖母绿形', 4 => '橄榄形', 5 => '椭圆形', 6 => '水滴形', 7 => '心形', 8 => '坐垫形', 9 => '辐射形', 10 => '方形辐射形', 11 => '方形祖母绿', 12 => '三角形',13=>'戒指托',14=>'异形',15=>'梨形',16=>'阿斯切',17 => '马眼', 18 => '长方形', 19 => '雷迪恩');
    //证书类型
    public static $Cert_arr = array('HRD-D','GIA','HRD','IGI','HRD-S');
    
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
            "cert" => "证书号类型",
            "cert_id" => "证书号",
            "gemx_zhengshu" => "gemx证书号",
            "status" => "状态：1启用 0停用",
            "add_time" => "添加时间",
            "is_active" => "0=默认，1=双11等活动",
            "from_ad" => "1=51钻，2=BDD",
            "good_type" => "1=现货，2=期货"
            );
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url DiamondTongjiController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true,$select="*") {
        $sql = "SELECT $select FROM `" . $this->table() . "` WHERE 1 ";
        if(isset($where['goods_sn']) && !empty($where['goods_sn'])){
            $sql.= " AND `goods_sn` LIKE '".$where['goods_sn']."%'";
        }
        if(isset($where['carat_min']) && !empty($where['carat_min'])){
            $sql.= " AND  `carat`>=".$where['carat_min'];
        }
        if(isset($where['carat_max']) && !empty($where['carat_max'])){
            $sql.= " AND  `carat`<=".$where['carat_max'];
        }
        if(isset($where['clarity']) && !empty($where['clarity'])){
            if(count($where['clarity'])==1){
                 $sql.= " AND `clarity` ='".$where['clarity'][0]."'";
            }else{
                $clarity = implode("','",$where['clarity']);
                $sql.= " AND `clarity` in ('".$clarity."')";
            }
        }
       
        if(isset($where['color']) && !empty($where['color'])){
            if(count($where['color'])==1){
                 $sql.= " AND `color` ='".$where['color'][0]."'";
            }else{
                $color = implode("','",$where['color']);
                $sql.= " AND `color` in ('".$color."')";
            }
        }
        if(isset($where['shape']) && !empty($where['shape'])){
            if(count($where['shape'])==1){
                 $sql.= " AND `shape` =".$where['shape'][0];
            }else{
                $shape = implode(",",$where['shape']);
                $sql.= " AND `shape` in (".$shape.")";
            }
        }
        
        if(isset($where['cut']) && !empty($where['cut'])){
            if(count($where['cut'])==1){
                 $sql.= " AND `cut` ='".$where['cut'][0]."'";
            }else{
                $cut = implode("','",$where['cut']);
                $sql.= " AND `cut` in ('".$cut."')";
            }
        } 
        if(isset($where['polish']) && !empty($where['polish'])){
            if(count($where['polish'])==1){
                 $sql.= " AND `polish` ='".$where['polish'][0]."'";
            }else{
                $polish = implode("','",$where['polish']);
                $sql.= " AND `polish` in ('".$polish."')";
            }
        }
        if(!empty($where['symmetry'])){
            if(count($where['symmetry'])==1){
                 $sql.= " AND `symmetry` ='".$where['symmetry'][0]."'";
            }else{
                $symmetry = implode("','",$where['symmetry']);
                $sql.= " AND `symmetry` in ('".$symmetry."')";
            }
        }
        if(!empty($where['fluorescence'])){
            if(count($where['fluorescence'])==1){
                 $sql.= " AND `fluorescence` ='".$where['fluorescence'][0]."'";
            }else{
                $fluorescence = implode("','",$where['fluorescence']);
                $sql.= " AND `fluorescence` in ('".$fluorescence."')";
            }
        }
        
        if(!empty($where['cert'])){
            if(count($where['cert'])==1){
                $sql.= " AND `cert` ='".$where['cert'][0]."'";
            }else{
                $cert = implode("','",$where['cert']);
                $sql.= " AND `cert` in ('".$cert."')";
            }
        }

        if(!empty($where['cert_id'])){
            $sql.= " AND  `cert_id`='".$where['cert_id']."'";
        }
        //是否有活动
        if(!empty($where['is_active'])){
            $sql.= " AND  `is_active`='".$where['is_active']."'";
        }
        //状态
        if(!empty($where['status'])){
            $sql.= " AND  `status`='".$where['status']."'";
        }
        
        
        
        $sql .= " ORDER BY `goods_id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    /**
     * 	getCert，按证书类型取
     *
     * 	@url DiamondTongjiController/getCert
     */
    function getCert() {
        $sql = "SELECT `cert`,count(*) as c FROM `" . $this->table() . "` WHERE `cert`!='' "; 
        $sql .= " GROUP BY `cert`";
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
     * 	getfrom_ad，按来源取
     *
     * 	@url DiamondTongjiController/getCert
     */
    function getFrom_ad() {
        $sql = "SELECT from_ad,count(*) as c FROM `" . $this->table() . "` WHERE 1 "; 
        $sql .= " GROUP BY from_ad";
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
     * 	getshape，按形状取
     *
     * 	@url DiamondTongjiController/getshape
     */
    function getShape() {
        $sql = "SELECT shape,count(*) as c FROM `" . $this->table() . "` WHERE 1 "; 
        $sql .= " GROUP BY `shape`";
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
     * 	getshape，按库房取
     *
     * 	@url DiamondTongjiController/getshape
     */
    function getWarehouse() {
        $sql = "SELECT warehouse,count(*) as c FROM `" . $this->table() . "` WHERE 1 "; 
        $sql .= " GROUP BY `warehouse`";
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
     * 	del，按形状删除
     */
    function del($where) {
        $sql = "DELETE FROM `" . $this->table() . "` WHERE 1".$where;
        return $this->db()->query($sql);
    }
}

?>