<?php

/**
 *  -------------------------------------------------
 *   @file		: DiamondListModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-14 16:13:09
 *   @update	:
 *  -------------------------------------------------
 */
class ColorDiamondListModel extends Model {

       //颜色(Color) ：黄钻 Yellow   蓝钻 Blue   粉钻 Pink   橙钻 Orange  绿钻 Green  红钻  Red  香槟钻 Champagne  灰钻 Grey  紫钻 Purple  变色龙 Multicolor  蓝紫钻 Royal_purple  白钻 White  黑钻 Black  阿盖尔粉钻 Argyle
    public static $Color_arr = array('Yellow', 'Blue', 'Pink', 'Orange','Green', 'Red', 'Champagne', 'Gray','Purple', 'Multicolor', 'Royal_purple', 'White', 'Black', 'Argyle','Other');
    //形状(Shape) ：圆形 Round   公主方 Princess   祖母绿 Emerald   椭圆 Oval  橄榄 Marquise  雷蒂恩  Radiant  心形 Heart  垫形 Asscher
    public static $Shape_arr = array('Round', 'Princess', 'Emerald', 'Oval','Marquise', 'Radiant', 'Heart', 'Asscher','Pear','Other');
    //净度(Clarity)IF,VVS1,VVS2,VS1,VS2,SI1,SI2,I1,I2						
    public static $Clarity_arr = array('I1', 'IF', 'VVS1', 'VVS2', 'VS1', 'VS2', 'SI1','I2');
    //彩钻颜色分级(Color_grade)：微 Faint   Very Light(很淡)   Light(淡)   Fancy Light(淡彩)  Fancy(中彩)  Fancy Intense(浓彩)  Fancy Dark(暗淡)  Fancy Deep(深彩)   Fancy Vivid(艳彩)
    public static $Color_grade_arr = array('Faint', 'Very Light', 'Light', 'Fancy Light', 'Fancy', 'Fancy Intense', 'Fancy Dark','Fancy Deep','Fancy Vivid');
    //证书类型
    public static $Cert_arr = array('AGL','Egl','GIA','Argyle','IGI');
	//来源
    public static $Fromad_arr = array('kela','leibish');
    //来源
    public static $Fromads_arr = array('1'=>'kela','2'=>'leibish','3'=>'Other');
    //荧光
    public static $Fluorescence_arr = array('Very strong','Strong','Medium','Faint','None');
    //抛光
    public static $Polish_arr = array('Excellent','Very good','Good','Fair','Bad');
    //对称性
    public static $Symmetry_arr = array('Excellent','Very good','Good','Fair','Bad');
    
	/* function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_diamond_color';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"goods_sn"=>" ",
"shape"=>" ",
"carat"=>" ",
"color"=>" ",
"clarity"=>" ",
"cut"=>" ",
"polish"=>" ",
"symmetry"=>" ",
"fluorescence"=>" ",
"table"=>" ",
"depth"=>" ",
"measurements"=>" ",
"cert"=>" ",
"cert_id"=>" ",
"price"=>" ",
"image1"=>" ",
"image2"=>" ",
"image3"=>" ",
"image4"=>" ",
"image5"=>" ",
"source"=>"来源",
"is_delete"=>" ",
"add_time"=>" ");
		parent::__construct($id,$strConn);
	} */

	    /**
     * 	pageList，分页列表
     *
     * 	@url DiamondListController/search
     */
    function pageList($where) {
        if(isset($where['carat_min'])){
            $keys[]='carat_min';
            $vals[]=$where['carat_min'];
        }
        if(isset($where['carat_max'])){
            $keys[]='carat_max';
            $vals[]=$where['carat_max'];
        }
        if(isset($where['cert'])){
        	$keys[]='cert';
        	$vals[]=$where['cert'];
        }
        if(isset($where['clarity'])){
                $keys[]='clarity';
                $vals[]=$where['clarity'];
        }
        if(isset($where['color'])){
        	$keys[]='color';
        	$vals[]=$where['color'];
        }
        
        if(isset($where['color_grade'])){
        	$keys[]='color_grade';
        	$vals[]=$where['color_grade'];
        }
        
        if(isset($where['shape'])){
        	$keys[]='shape';
        	$vals[]=$where['shape'];
        }
        
//         if(isset($where['symmetry'])){
//         	$keys[]='symmetry';
//         	$vals[]=$where['symmetry'];
//         }
//         if(isset($where['polish'])){
//         	$keys[]='polish';
//         	$vals[]=$where['polish'];
//         }
//         if(isset($where['fluorescence'])){
//         	$keys[]='fluorescence';
//         	$vals[]=$where['fluorescence'];
//         }
        if(isset($where['from_ad'])){
        	$keys[]='from_ad';
        	$vals[]=$where['from_ad'];
        }
        if(isset($where['cert_id'])){
        	$keys[]='cert_id';
        	$vals[]=$where['cert_id'];
        }
        if(isset($where['goods_sn'])){
        	$keys[]='goods_sn';
        	$vals[]=$where['goods_sn'];
        }
        if(isset($where['price_min'])){
        	$keys[]='price_min';
        	$vals[]=$where['price_min'];
        }
        if(isset($where['price_max'])){
        	$keys[]='price_max';
        	$vals[]=$where['price_max'];
        }
        
        if(isset($where['page'])){
        	$keys[]='page';
        	$vals[]=$where['page'];
        }
        if(isset($where['pageSize'])){
        	$keys[]='pageSize';
        	$vals[]=$where['pageSize'];
        }
//         echo 11;exit;
        $ret=ApiModel::diamond_api($keys,$vals,'getcolordiamondList');
        return $ret;              
    }
    /**
     * 	getRowById，取一行
     *
     * 	@url DiamondListController/getAllList
     */
    function getRowById($goods_id) {
        $keys=array('goods_id');
        $vals=array($goods_id);
        $ret=ApiModel::diamond_api($keys,$vals,'GetDiamondByiId');
        return $ret; 
    }

    /**
     * 	getRowByGoodSn，取一行
     *
     * 	@url DiamondListController/getRowByGoodSn
     */
    function getRowByGoodSn($goods_sn) {
        $keys=array('goods_sn');
        $vals=array($goods_sn);
        $ret=ApiModel::diamond_api($keys,$vals,'GetDiamondByGoods_sn');
        return $ret; 
    }

    /**
     * 	getRowByGoodSn，取一行
     *
     * 	@url DiamondListController/getRowByGoodSnOrCertId
     */
    function getRowByGoodSnOrCertId($sn) {
        $keys=array('goods_sn_or_certid');
        $vals=array($sn);
        $ret=ApiModel::diamond_api($keys,$vals,'GetDiamondByGoods_snOrCertid');
        return $ret; 
    }

    /**
     * 	getAllList，取所有
     *
     * 	@url DiamondListController/getAllList
     */
    function getAllList($select="*") {
        $sql = "SELECT $select FROM `" . $this->table() . "`";
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /**
     * 	deletebycert_id，删除
     *
     * 	@url DiamondListController/deletebycert_id
     */
    function deletebycert_id($cert,$cert_id) {
        $sql = "DELETE FROM `".$this->table()."` WHERE `cert` = '".$cert."' AND `cert_id`='".$cert_id."'";
		return $this->db()->query($sql);
    }

    //取所有形状
    public static function getShapeName()
    {
           $Shape_arr=self::$Shape_arr;
           return $Shape_arr;
    }
    
    public function checkMimaVaild($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        return ApiModel::diamond_api($keys,$vals,'checkDiscountMima');
    }
    
    //取打折密码通过货号
    public function getDiscountByGoods_id($where){
        foreach ($where as $k=>$v){
            $keys[$k] = $k;
            $vals[$k] = $v;
        }
        return ApiModel::diamond_api($keys,$vals,'checkDiscountid');
    }

    public function get_diamond_by_kuan_sn($kuan_sn){
        if(isset($kuan_sn)){
            $keys[] ='kuan_sn';
            $vals[] =$kuan_sn;             
        }
        return ApiModel::diamond_api($keys,$vals,'GetDiamondByKuan_sn');
    }
    
    public function updateDiscountMima($where) {
        $keys = array('log_data','grant_data');
        $vals = array($where['insert_data'],$where['update_data']);

        return ApiModel::diamond_api($keys,$vals,'updateDiscountMima');
    }
    
    //取消优惠
    public function updateDiscountMimas($where) {
        $keys = array('log_data','grant_data');
        $vals = array($where['insert_data'],$where['update_data']);

        return ApiModel::diamond_api($keys,$vals,'updateDiscountMimas');
    }

    /**
     *
     * 获取库房
     */
    public function get_warehouse_all($type=1)
    {
        $keys[]='pppppp';
        $vals[]='';
        $keys[]='diamond_warehouse';
        $vals[]=$type;
        $ret=ApiModel::warehouse_api($keys,$vals,'GetWarehouseList');
        
        return $ret;  
    }
	    /* 根据钻石大小反推镶口
     * 镶口：不用验证 钻石大小>0 钻石大小<0.10
     * 镶口：0.10 钻石大小>=0.10 钻石大小<=0.15
     * 镶口：0.20 钻石大小>0.15 钻石大小<=0.25
     * 镶口：0.30 钻石大小>0.25 钻石大小<=0.35
     * luna
     */
    public function getXiangKou ($stone){
		if($stone>0 && $stone<0.1){
			return '';
		}
		if($stone>=0.10 && $stone <=0.15){
			$xiangkou = 0.1;
		}
		
		if($stone >0.15 && $stone <1){
			$xiangkou = floatval(substr($stone,0,3));
		}
		if($stone>=1){
			$temp_arr = explode('.',$stone);
			if(count($temp_arr)>=2){
	                        //整数部分
				$zhengshu = floor($stone);;
	                        //小数第一位
				$xiaoshu_part = ($stone-$zhengshu)*10;
				$xiaoshu_part = floor($xiaoshu_part);	
	          		$xiaoshu = $xiaoshu_part*0.1;

            			//小数其他位数
				$xiaoshu2 = floatval($stone) - floatval($zhengshu + $xiaoshu) ; 

				//判断小数其他位数是否小于0.051
				if(floatval($xiaoshu2)<0.051){
					 $xiangkou= $zhengshu + $xiaoshu;
					 return $xiangkou;
				}else{
					$xiangkou = $zhengshu + $xiaoshu+0.1;
					return $xiangkou;
	
				}
			}else{//整数的话直接返回石重即可
				return $xiangkou= $stone;
			}
		}
		return $xiangkou;
    }
    
    //获取来源
    public function getFromads_arr() {
    	return ColorDiamondListModel::$Fromads_arr;
    }
    
    
	}
?>