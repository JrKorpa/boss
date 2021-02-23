<?php
/**
 *  -------------------------------------------------
 *   @file		: AppDiamondColorModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-02 15:07:13
 *   @update	:
 *  -------------------------------------------------
 */
class AppDiamondColorModel extends Model
{
    //颜色(Color) ：黄钻 Yellow   蓝钻 Blue   粉钻 Pink   橙钻 Orange  绿钻 Green  红钻  Red  香槟钻 Champagne  灰钻 Grey  紫钻 Purple  变色龙 Multicolor  蓝紫钻 Royal_purple  白钻 White  黑钻 Black  阿盖尔粉钻 Argyle
    public static $Color_arr = array('Yellow', 'Blue', 'Pink', 'Orange','Green', 'Red', 'Champagne', 'Gray','Purple', 'Multicolor', 'Royal_purple', 'Black', 'chameleon', 'Other');
    //形状(Shape) ：圆形 Round   公主方 Princess   祖母绿 Emerald   椭圆 Oval  橄榄 Marquise  雷蒂恩  Radiant  心形 Heart  垫形 Asscher
    public static $Shape_arr = array('Round', 'Princess', 'Emerald', 'Oval','Marquise', 'Radiant', 'Heart', 'Asscher','Cushion','Pear','Other');
    //形状(Shape)$shape_arr
    public static $Shape_arr2 = array('Round'=>'圆形', 'Princess'=>'公主方', 'Emerald'=>'祖母绿', 'Oval'=>'椭圆','Marquise'=>'橄榄', 'Radiant'=>'雷蒂恩 ', 'Heart'=>'心形 ', 'Asscher'=>'垫形','Other'=>'其他');
    //净度(Clarity)
    public static $Clarity_arr = array('I1', 'IF', 'VVS1', 'VVS2', 'VS1', 'VS2', 'SI1','I2');
    //彩钻颜色分级(Color_grade)：微 Faint   Very Light(很淡)   Light(淡)   Fancy Light(淡彩)  Fancy(中彩)  Fancy Intense(浓彩)  Fancy Dark(暗淡)  Fancy Deep(深彩)   Fancy Vivid(艳彩)
    public static $Color_grade_arr = array('Faint', 'Very Light', 'Light', 'Fancy Light', 'Fancy', 'Fancy Intense', 'Fancy Dark','Fancy Deep','Fancy Vivid');
    //证书类型
    public static $Cert_arr = array('AGL','Egl','GIA','Argyle','IGI','HRD-S');
    //供应商
    public static $from_ad = array('1' =>'kela','2'=>'leibish');
    //库房
    public static $warehouse = array('kela','leibish');
    //库房
    public static $warehouse2 = array('1'=>'kela','2'=>'leibish');
    //货品类型good_type  1 现货  2 期货
    public static $goods_type = array('1'=>'现货','2'=>'期货');
    //货品状态
    public static $status = array('1'=>'上架','0'=>'下架');
    //对称性
    public static $symmetry = array('Excellent','Very good','Good','Fair','Bad');
    //抛光
    public static $polish = array('Excellent','Very good','Good','Fair','Bad');
    //荧光
    public static $fluorescence = array('Very strong','Strong','Medium','Faint','None');
    
    
	function __construct ($id=NULL,$strConn="")
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
									"polish"=>" ",
									"symmetry"=>" ",
									"fluorescence"=>" ",
									"measurements"=>" ",
									"cert"=>" ",
									"cert_id"=>" ",
									"price"=>" ",
									"image1"=>" ",
									"image2"=>" ",
									"image3"=>" ",
									"image4"=>" ",
									"image5"=>" ",
									"from_ad"=>"",
					        		"add_time"=>"",
					        		"color_grade"=>"",
					        		"warehouse"=>"",
					        		"cost_price"=>"",
					        		"good_type"=>"",
					        		"mo_sn"=>"",
									"status"=>" ",
									"quantity"=>" ");
        
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppDiamondColorController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		
		$str = '';
		// if($where['goods_sn'] != "")
		// {
		// 	$str .= " `goods_sn` like \"%".addslashes($where['goods_sn'])."%\" AND ";
		// }
		if($where['goods_id'] != "")
		{
			$str .= "`goods_id`='".$where['goods_id']."' AND ";
		}

		if(!empty($where['carat_min']))
		{
			$str .= "`carat`>=".$where['carat_min']." AND ";
		}
		if(!empty($where['carat_max']))
		{
			$str .= "`carat`<=".$where['carat_max']." AND ";
		}
		
		if(!empty($where['price_min']))
		{
			$str .= "`price`>=".$where['price_min']." AND ";
		}
		if(!empty($where['price_max']))
		{
			$str .= "`price`<=".$where['price_max']." AND ";
		}
		 if(!empty($where['clarity']))
	        {
	        	$str .= "`clarity` IN('".$where['clarity']."') AND ";
	        }
		 if(!empty($where['color']))
	        {
	        	$str .= "`color` IN('".$where['color']."') AND ";
	        }
	       
        if(!empty($where['color_grade']))
        {
        	$str .= "`color_grade` IN('".$where['color_grade']."') AND ";
        }
	        
		 if(!empty($where['shape']))
	        {
	        	$str .= "`shape` IN('".$where['shape']."') AND ";
	        }
		 if(!empty($where['cert']))
	        {
	        	$str .= "`cert` IN('".$where['cert']."') AND ";
	        }
        if($where['cert_id'] != "")
        {
            $str .= " `cert_id` like \"%".addslashes($where['cert_id'])."%\" AND ";
        }
        
        if(!empty($where['from_ad']))
        {
        	$str .= "`from_ad` IN('".$where['from_ad']."') AND ";
        }
        
        if($where['status'] !== '')
        {
        	$str .= "status= '".$where['status']."' AND ";
        }
        
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	
		public function getShapeList(){
			
			return self::$Shape_arr2;
			
		}
		
		public function getWarehouse(){
			
			return self::$warehouse2;
		}
		
		//批量删除
		public function delManyDelete ($ids)
		{
			if(count($ids)==0)
			{
				return true;
			}
			$sql = "DELETE FROM `" . $this->table() . "` WHERE `id` IN (".implode(",",$ids).")";
			
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


		function getRowBygoods_id($goods_id) {
			$sql = "SELECT * FROM `" . $this->table() . "` WHERE `goods_id`='".$goods_id."'";
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
// 			echo $sql;exit;
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
		
		function checkDiamond($shape,$color,$clarity,$polish,$fluorescence,$symmetry,$cert){
			
			$error=0;
			$error_type=array();
			if(!in_array($shape,self::$Shape_arr)){
				$error_arr=self::$Shape_arr;
				$error = 1;
				$error_type[]='形状';
				return array($error,$error_type,$error_arr);
			}
			if(!in_array($color,self::$Color_arr)){
				$error_arr=self::$Color_arr;
				$error = 1;
				$error_type[]='颜色';
				return array($error,$error_type,$error_arr);
			}
			if(!in_array($clarity,self::$Clarity_arr)){
				$error_arr=self::$Clarity_arr;
				$error = 1;
				$error_type[]='净度';
				return array($error,$error_type,$error_arr);
			}
			if(!in_array($polish,self::$polish)){
				$error_arr=self::$polish;
				$error = 1;
				$error_type[]='抛光';
				return array($error,$error_type,$error_arr);
			}
			if(!in_array($fluorescence,self::$fluorescence)){
				$error_arr=self::$fluorescence;
				$error = 1;
				$error_type[]='荧光';
				return array($error,$error_type,$error_arr);
			}
			if(!in_array($symmetry,self::$symmetry)){
				$error_arr=self::$symmetry;
				$error = 1;
				$error_type[]='对称性';
				return array($error,$error_type,$error_arr);
			}
			if(!in_array($cert,self::$Cert_arr)){
				$error_arr=self::$Cert_arr;
				$error = 1;
				$error_type[]='证书';
				return array($error,$error_type,$error_arr);
			}
// 			return array($error,$error_type,$error_arr);
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
			$Shape_arr=self::$Shape_arr;
			return $Shape_arr;
		}
		
		//取所有来源
		public static function getForm_ad()
		{
			$fromad_arr=self::$from_ad;
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
		
	
		/**
		 *
		 * 获取库房
		 */
		public function get_warehouse_all($type=1)
		{
			$keys[]='diamond_warehouse';
			$vals[]=$type;
			$ret=ApiModel::warehouse_api($keys,$vals,'GetWarehouseList');
			return $ret;
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
			if(isset($where['shape']) && !empty($where['shape'][0])){
// 				echo count($where['shape']);
				if(count($where['shape'])==1){
					$str.= "d.`shape` ='".$where['shape'][0]."' AND ";
				}else{
					$shape = implode("','",$where['shape']);
					$str.= "d.`shape` in ('".$shape."') AND ";
				}
			}
			if(isset($where['carat_min']) && !empty($where['carat_min'])){
				$str.= " d.`carat`>=".$where['carat_min']." AND ";
			}
			if(isset($where['carat_max']) && !empty($where['carat_max'])){
				$str.= " d.`carat`<=".$where['carat_max']." AND ";
			}
			if(isset($where['color']) && !empty($where['color'][0])){
				if(count($where['color'])==1){
					$str.= "d.`color` ='".$where['color'][0]."' AND ";
				}else{
					$color = implode("','",$where['color']);
					$str.= "d.`color` in ('".$color."') AND ";
				}
			}
			if(isset($where['clarity']) && !empty($where['clarity'][0])){
				if(count($where['clarity'])==1){
					$str.= "d.`clarity` ='".$where['clarity'][0]."' AND ";
				}else{
					$clarity = implode("','",$where['clarity']);
					$str.= "d.`clarity` in ('".$clarity."') AND ";
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
			
			if(!empty($where['cert'][0])){
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
			//状态
			if(!empty($where['status'])){
				$str.= " d.`status`='".$where['status']."' AND ";
			}
			$sql = "SELECT $select FROM `" . $this->table() . "` as d $join_table";
			if ($str) {
				$str = rtrim($str, "AND "); //这个空格很重要
				$sql .=" WHERE $join_where" . $str;
			}
			$sql .= " ORDER BY d.`id` DESC LIMIT $start, $limit";
		
// 			echo $sql;exit;
			$data = $this->db()->getAll($sql);
			return $data;
		}
		
		public function getMeasures($id){
			$sql = "select * from ".$this->table()." where id=".$id;
// 			echo $sql;exit;
			$data = $this->db()->getAll($sql);
			return $data;			
			
		}
		
		/*
		 *根据证书类型，来源，货品状态获取成本价 
		 */
// 		public function getCostPrice($cert,$from_ad,$good_type){
// 			$sql ='select * from '.$this->table().' where cert="'.$cert.'" and from_ad="'.$from_ad.'" and good_type="'.$good_type.'"';
// 			echo $sql;exit;
			
			
// 		}
		
		/*
		 * 通过货品id获取商品的所有图片（5张）
		 * getImagesById
		 */
		public function getImagesById($id){
			$sql = "select image1,image2,image3,image4,image5 from ".$this->table()." where id = ".$id;
// 			echo $sql;exit;
			$data = $this->db()->getAll($sql);
			return $data;
		}
		
}

?>