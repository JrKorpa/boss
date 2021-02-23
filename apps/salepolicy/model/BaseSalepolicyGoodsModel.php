<?php

/**
 *  -------------------------------------------------
 *   @file		: BaseSalepolicyGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-03 18:25:10
 *   @update	:
 *  -------------------------------------------------
 */
class BaseSalepolicyGoodsModel extends Model {

    //镶口
    public static $stone = array('0.20', '0.30', '0.40', '0.50', '0.60', '0.70', '0.80', '0.90', '1.00', '1.10', '1.20', '1.30', '1.40', '1.50');
    //手寸
    public static $finger = array('9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21');
    //材质
    public static $caizhi = array(
		    '10'=>'9K',
		    '13'=>'10K',
		    '9'=>'14K',		    
		    '1'=>'18K',		    
		    '11'=>'PT900',
		    '2'=>'PT950',
		    '12'=>'PT999',
		    '3'=>'18K&PT950',
		    '4'=>'S990',		    
		    '6'=>'S925',
            '8'=>'足金',
		    '5'=>'千足银',
		    '7'=>'千足金',		    
		    '14'=>'千足金银',
		    '15'=>'裸石',
		    '0'=>'其它'		    
	);
    //颜色
    public static $yanse = array(
            '0'=>'无',
            '1'=>'白',
            '2'=>'黄',
            '9'=>'黄白',
            '3'=>'玫瑰金',
            '4'=>'分色',
            '5'=>'彩金',
            '6'=>'玫瑰黄',
            '7'=>'玫瑰白',
            '8'=>'玫瑰金',            
            '10'=>'按图做'            
   );

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'base_salepolicy_goods';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => "自增id",
            "goods_id" => "货号",
            "goods_sn" => "款号",
            "goods_name" => "商品名称",
            "chengbenjia" => "成本价",
            "stone" => "镶口",
            "finger" => "手寸",
            "caizhi" => "材质",
            "yanse" => "颜色",
            "category" => "分类",
            "product_type" => "产品线",
            "isXianhuo" => "现货状态0是期货1是现货",
            "is_sale" => "上架状态，1上架，0下架",
            "type"=>"销售策略类型，1：普通类型；2：打包类型",
            "together_id"=>"打包策略绑定多件商品id",
            "add_time" => "推送数据的时间");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url BaseSalepolicyGoodsController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        $sql = "SELECT `bsg`.id,`bsg`.goods_id,`bsg`.goods_sn,`bsg`.goods_name,`bsg`.isXianhuo,`bsg`.chengbenjia,`bsg`.stone,`bsg`.finger,`bsg`.caizhi,`bsg`.yanse,`bsg`.category,`bsg`.product_type,`bsg`.product_type,`bsg`.add_time,`bsg`.is_sale,`bsg`.type,`bsg`.is_base_style,`bsg`.xiangkou,`bsg`.is_valid,`bsg`.company,`bsg`.warehouse,`bsg`.`is_policy` FROM `" . $this->table() . "` as bsg";
        $str = '';
		if($where['goods_sn'] != "")
		{
			$str .= "`bsg`.`goods_sn`='".$where['goods_sn']."' AND ";
		}
		if(!empty($where['goods_id']))
		{
            $goods_id = "'".str_replace(' ',"','",$where['goods_id'])."'";
			$str .= "`bsg`.`goods_id` in ({$goods_id}) AND ";
		}
		if(!empty($where['price_start']))
		{
			$str .= "`bsg`.`chengbenjia`>=".$where['price_start']." AND ";
		}
		if(!empty($where['price_end']))
		{
			$str .= "`bsg`.`chengbenjia`<=".$where['price_end']." AND ";
		}
		if(!empty($where['stone']))
		{
			$str .= "`bsg`.`stone`=".$where['stone']." AND ";
		}
		if(!empty($where['finger']))
		{
			$str .= "`bsg`.`finger`=".$where['finger']." AND ";
		}
		if(!empty($where['caizhi']))
		{
			$str .= "`bsg`.`caizhi`=".$where['caizhi']." AND ";
		}
		if(!empty($where['yanse']))
		{
			$str .= "`bsg`.`yanse`=".$where['yanse']." AND ";
		}
		if(isset($where['isXianhuo'])&&$where['isXianhuo']!='')
		{
			$str .= "`bsg`.`isXianhuo`=".$where['isXianhuo']." AND ";
		}
		if(isset($where['is_policy'])&&$where['is_policy']!='')
		{
			$str .= "`bsg`.`is_policy`=".$where['is_policy']." AND ";
		}
		if(isset($where['is_sale'])&&$where['is_sale']!='')
		{
			$str .= "`bsg`.`is_sale`=".$where['is_sale']." AND ";
		}
        if(isset($where['category'])&&$where['category']!='')
        {
            $str .= "`bsg`.`category`=".$where['category']." AND ";
        }
        if(isset($where['type'])&&$where['type']!=''){
            $str .= "`bsg`.`type`=".$where['type']." AND ";
        }
        if(isset($where['zhuchengse'])&&$where['zhuchengse']!='')
        {
            $str .= "`bsg`.`zhuchengse`='".$where['zhuchengse']."' AND ";
        }
        if(isset($where['zhushi'])&&$where['zhushi']!='')
        {
            $str .= "`bsg`.`zhushi`='".$where['zhushi']."' AND ";
        }
        if(isset($where['mohao'])&&$where['mohao']!='')
        {
            $str .= "`bsg`.`mohao`='".$where['mohao']."' AND ";
        }
        if(isset($where['processor'])&&$where['processor']!='')
        {
            $str .= "`bsg`.`processor`='".$where['processor']."' AND ";
        }
        if(isset($where['shoucun'])&&$where['shoucun']!='')
        {
            $str .= "`bsg`.`shoucun`='".$where['shoucun']."' AND ";
        }
        if(isset($where['chanpinxian'])&&$where['chanpinxian']!='')
        {
            $str .= "`bsg`.`product_type`='".$where['chanpinxian']."' AND ";
        }
        if(isset($where['jintuo_type'])&&$where['jintuo_type']!='')
        {
            $str .= "`bsg`.`jintuo_type`='".$where['jintuo_type']."' AND ";
        }
        if(isset($where['jinshi_type'])&&$where['jinshi_type']!='')
        {
            $str .= "`bsg`.`jinshi_type`='".$where['jinshi_type']."' AND ";
        }
        if(isset($where['zs_clarity'])&&$where['zs_clarity']!='')
        {
            $str .= "`bsg`.`zs_clarity`='".$where['zs_clarity']."' AND ";
        }
        if(isset($where['zs_color'])&&$where['zs_color']!='')
        {
            $str .= "`bsg`.`zs_color`='".$where['zs_color']."' AND ";
        }
        if(isset($where['zhengshuleibie'])&&$where['zhengshuleibie']!='')
        {
            $str .= "`bsg`.`zhengshuleibie`='".$where['zhengshuleibie']."' AND ";
        }
        if(isset($where['zhengshuhao'])&&$where['zhengshuhao']!='')
        {
            $str .= "`bsg`.`zhengshuhao`='".$where['zhengshuhao']."' AND ";
        }
        if(isset($where['warehouse_id'])&&$where['warehouse_id']!='')
        {
            $str .= "`bsg`.`warehouse_id`='".$where['warehouse_id']."' AND ";
        }
        if(isset($where['company_id'])&&$where['company_id']!='')
        {
            $str .= "`bsg`.`company_id`='".$where['company_id']."' AND ";
        }
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $sql .= " ORDER BY `bsg`.`id` DESC";

        //echo $sql;
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }


    function isHaveGoodsId($goods_id) {
        if(empty($goods_id)){
            return false;
        }
        $sql = "select `is_sale`,`id`,`chengbenjia` from `{$this->table()}` where `goods_id`='{$goods_id}'";
        return $this->db()->getRow($sql);
    }
    function getChenbenByid($goods_id) {
        if(empty($goods_id)){
            return false;
        }
        $sql = "select `chengbenjia` from `{$this->table()}` where `goods_id`='{$goods_id}'";
       
        return $this->db()->getOne($sql);
    }
    function getMingyiChenbenByid($goods_id) {
        if(empty($goods_id)){
            return false;
        }
        $sql = "select `mingyichengben` from `warehouse_shipping`.`warehouse_goods` where `goods_id`='{$goods_id}'";
     
        return $this->db()->getOne($sql);
    }



    function isHaveGoodsSn($goods_id) {
        if(empty($goods_id)){
            return false;
        }
        $sql = "select `is_sale` from `{$this->table()}` where `goods_id`='{$goods_id}'";
        return $this->db()->getOne($sql);
    }


    function getListByIds($ids) {
        if(empty($ids)){
            return FALSE;
        }
        $sql = "SELECT `goods_id`,`chengbenjia`,`isXianhuo`,`goods_id` from `{$this->table()}` WHERE `id` in ({$ids})";
        return $this->db()->getAll($sql);
    }

    /**
     * 接口获取产品线名称
     * @param type $product_type
     * @return string
     */
    function getProductTypeName($product_type) {
        if(empty($product_type)){
            return '';
        }
        $keys=array('product_type_id');
        $vals=array($product_type);
        $ret = ApiModel::style_api($keys, $vals, "getProductTypeInfo");
        return $ret[0]['name'];
    }

    /**
     * 接口获取款式名称
     * @param type $product_type
     * @return string
     */
    function getCatTypeName($category) {
        if(empty($category)){
            return '';
        }
        $keys=array('cat_type_id');
        $vals=array($category);
        $ret = ApiModel::style_api($keys, $vals, "getCatTypeInfo");
        return $ret[0]['name'];
    }


    public function settype($ids,$type){
    	return true;
        if(empty($ids)||empty($type)){
           return false;
        }
        $ids = implode(',',$ids);
        $sql = "update `base_salepolicy_goods` set `type`=$type WHERE `id` in ($ids)";
        return $this->db()->query($sql);

    }

    public function getSaleStitice($ids){
        if(!empty($ids)){
            $ids = implode(',',$ids);
            $sql = "select `id`,`is_sale`,`is_valid` from `base_salepolicy_goods` where id in ($ids)";
            
            return $this->db()->getAll($sql);
        }else{
            return false;
        }
    }
    
    public function updateSalePolicyStatus($where){
    	return true;
        if(!isset($where['id_in'])){
            return false;
        }
        $ids = $where['id_in'];
        if(!isset($where['is_sale'])){
            return false;
        }
        $is_sale = $where['is_sale'];
        $sql_c = "select `goods_id` from `base_salepolicy_goods` where id in ($ids) ";
        $goods_id = $this->db()->getAll($sql_c);
        if(empty($goods_id)){
            return false;
        }
        $str = '';
        foreach ($goods_id as $k => $v) {
            $str .= "'".$v['goods_id']."',";
        }
        $str_s = rtrim($str,',');
        $sql_s = "update `app_salepolicy_goods` set `status` = 5 where `goods_id` in ($str_s)";
        $res = $this->db()->query($sql_s);
        $sql = "update `base_salepolicy_goods` set `is_sale`=$is_sale where id in ($ids) ";
        
        return $this->db()->query($sql);
        
    }
    
    /**
     * 更改货品已加入销售政策状态
     * @param type $where
     * @return boolean
     */
    public function updateGoodsIsPolicy($goods_ids){      
    	return true;
        $goods_id = implode("','", $goods_ids);
        $sql = "update `base_salepolicy_goods` set `is_policy`=2 where `goods_id` in ('".$goods_id."') ";      
        return $this->db()->query($sql);
    }

    /**
     * 取镶口
     */
    function getStone() {

        return self::$stone;
    }

    /**
     * 取手寸
     */
    function getFinger() {

        return self::$finger;
    }

    /**
     * 取材质
     */
    function getCaizhi() {

        return self::$caizhi;
    }

    /**
     * 取颜色
     */
    function getYanse() {

        return self::$yanse;
    }


    public function getAppInfoList($b_g_id){
        if(empty($b_g_id)){
            return false;
        }
        $sql = "SELECT bg.*,ag.*,bs.policy_name from base_salepolicy_goods as bg LEFT JOIN  app_salepolicy_goods as ag on bg.goods_id = ag.goods_id LEFT JOIN base_salepolicy_info as bs on bs.policy_id=ag.policy_id  WHERE ag.is_delete =1 AND bg.id=".$b_g_id." AND bs.bsi_status=3";
        return $this->db()->getAll($sql);
    }
	
	
	//根据base_salepolicy_goods中的id 获取商品是否已经有绑定了非审核状态的默认销售政策
	public function getSalepolicyinfo($b_g_id)
	{
		if(empty($b_g_id))
		{
			return false;
		}
		$sql = "
select a.goods_name,c.policy_name 
	from  base_salepolicy_goods as a
left join app_salepolicy_goods as b on a.goods_id=b.goods_id
left join base_salepolicy_info as c on b.policy_id=c.policy_id
where a.id=$b_g_id and c.is_default=1 and c.bsi_status !=3 
";
		return $this->db()->getAll($sql);
	}
    public function getKuanPrice($goods_id)
    {
        if(empty($goods_id)){
            return false;
        }
        $sql="select is_kuanprice,kuanprice from `warehouse_shipping`.`warehouse_goods_age` where goods_id = $goods_id ;";
        $row = $this->db()->getRow($sql);
        if($row){
            if($row['is_kuanprice']==1){
                return $row['kuanprice'] ;
            }
        }
        return false;
    }
}

?>