<?php

/**
 *  -------------------------------------------------
 *   @file		: BaseStyleInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-08 13:40:44
 *   @update	:
 *  -------------------------------------------------
 */
class BaseStyleInfoModel extends Model {

    public $_prefix = 'style';

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'base_style_info';
        $this->_dataObject = array("style_id" => "款式ID",
            "style_sn" => "款式编号",
            "style_name" => "款式名称",
            "product_type" => "产品线:0=其他 1=黄金等投资产品 2=素金饰品 3=黄金饰品 4=结婚钻石饰品 5=钻石饰品 6=珍珠饰品 7=彩宝及翡翠饰品 8=裸石",
            "style_type" => "款式分类:1=戒指 2=吊坠 3=项链 4=耳钉 5=耳环 6=耳坠 7=手镯 8=手链 9=脚链 13=其他",
			"sell_type" => "畅销度：1=新款 2=滞销款 3=畅销款 4=平常款",
            "create_time" => "添加时间",
            "modify_time" => "更新时间",
            "check_time" => "审核时间",
            "cancel_time" => "作废时间",
            "check_status" => "是否审核:0=取消作废 1=通过审核 2=提交申请 3=未通过 4=作废",
            "is_sales" => "是否销售，0：否，1：是",
            "is_made" => "是否定制，0：否，1：是",
            "dismantle_status" => "是否拆货:0=正常 1=允许拆货 2=已拆货",
            "style_status" => "记录状态",
            "style_remark" => "记录备注",
            "dapei_goods_sn" => '搭配套系名称',
            "changbei_sn" => '是否常备款;1,是；2,否',
            "is_zp" => '是否是赠品；1否，2是',
            "style_sex" => '款式性别;1:男；2：女；3：中性',
            "xilie" => '系列',
            "bang_type" => '绑定1：需要绑定，2：不需要绑定',
            "market_xifen" => '市场细分',
            "goods_content"=>"商品描述",
        );
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url ApplicationController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        $sql = "SELECT `bsi`.*,`rsf`.`factory_sn` FROM `" . $this->table() . "` as `bsi` LEFT JOIN `rel_style_factory` as `rsf` ON `bsi`.`style_sn` = `rsf`.`style_sn` WHERE 1 ";
        
        if (isset($where['style_id']) && !empty($where['style_id'])){
        	$sql .= " AND `bsi`.`style_id` = '{$where['style_id']}'";
        }
        if (isset($where['style_sn']) && !empty($where['style_sn'])){
            $sql .= " AND `bsi`.`style_sn` = '{$where['style_sn']}'";
        }
        if (isset($where['style_sn_in']) && !empty($where['style_sn_in'])) {
            $sql .= " AND `bsi`.`style_sn` in ({$where['style_sn_in']})";
        }
        if(isset($where['product_type_id']) && !empty($where['product_type_id'])){
            $sql .=" AND `bsi`.`product_type` in ({$where['product_type_id']}) ";
        }
        if(isset($where['cat_type_id']) && !empty($where['cat_type_id'])){
            $sql .=" AND `bsi`.`style_type` = '{$where['cat_type_id']}' ";
        }
        if(isset($where['style_sex']) && !empty($where['style_sex'])){
            $sql .=" AND `bsi`.`style_sex` = '{$where['style_sex']}' ";
        }
        if(isset($where['style_name']) && !empty($where['style_name'])){
            $sql .=" AND `bsi`.`style_name` like '" . addslashes($where['style_name']) . "%'";
        }
        if(isset($where['factory_sn']) && !empty($where['factory_sn'])){
            $sql .=" AND `rsf`.`factory_sn` like '" . addslashes($where['factory_sn']) . "%'";
        }
        if(isset($where['factory_sn_in']) && !empty($where['factory_sn_in'])){
        	$sql .=" AND `rsf`.`factory_sn` in ({$where['factory_sn_in']})";
        }
       /* 用分组判断物控与废物空  
        if($_SESSION['userType']<>1){
            if(isset($where['is_wukong']) && $where['is_wukong'] == true){
                $sql .=" AND `bsi`.`is_wukong` = 1";
            }else{
                $sql .=" AND `bsi`.`is_wukong` <> 1";
            }
        }   */
        if(isset($where['xilie']) && !empty($where['xilie'])){
            if(count($where['xilie'])==1){
                 $sql.= " AND `xilie` like '%,".$where['xilie'][0].",%'";
            }else{
                $str = "";
                foreach ($where['xilie'] as $val){
                     $str.=" `xilie` like '%,".$val.",%' or ";
                }
                $str = rtrim($str," or");
                $sql .= " AND (".$str.")";
               
            }
        }
        if(isset($where['company_type']) && !empty($where['company_type'])){
            if(count($where['company_type'])==1){
                 $sql.= " AND `company_type_id` like '%,".$where['company_type'][0].",%'";
            }else{
                $str = "";
                foreach ($where['company_type'] as $val){
                     $str.=" `company_type_id` like '%,".$val.",%' or ";
                }
                $str = rtrim($str," or");
                $sql .= " AND (".$str.")";
               
            }
        }
        if(isset($where['is_made']) && $where['is_made'] !== ''){// 2015-12-25 zzm boss-1013
            $sql .=" AND `bsi`.`is_made` = {$where['is_made']}";
        }
        if(isset($where['check_status']) && !empty($where['check_status'])){
            $sql .=" AND `bsi`.`check_status` = '{$where['check_status']}' ";
        }
        if(isset($where['check_status_zuofei']) && !empty($where['check_status_zuofei'])){
            $sql .=" AND `bsi`.`check_status` != {$where['check_status_zuofei']}";
        }
        if (isset($where['is_xiaozhang']) && !empty($where['is_xiaozhang'])){
            $sql .= " AND `bsi`.`bang_type`= ".$where['is_xiaozhang'];
        }
        if(isset($where['is_kuanprice'])){
            if($where['is_kuanprice'] == 1){
                $sql .=" AND `bsi`.`style_id` in (SELECT style_id FROM `app_price_by_style`)";
            }else{
                $sql .=" AND `bsi`.`style_id` not in (SELECT style_id FROM `app_price_by_style`)";
            }
        }        
        if(!empty($where['market_xifen'])){
            $sql .= " AND `bsi`.`market_xifen`= '{$where['market_xifen']}'";
        }
        
        $sql .= " ORDER BY `bsi`.`style_id` DESC";
        //echo $sql;die;
        $data = $this->db()->getPageListNew($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    /**
     *  pageList，zuofei分页列表
     *
     *  @url ApplicationController/search
     */
    function zuofei_pageList($where, $page, $pageSize = 10, $useCache = true) {
        $sql = "SELECT `bsi`.`style_id`,`bsi`.`style_sn`,`bsi`.`style_name`,`bsi`.`style_type`,`bsi`.`product_type`,`bsi`.`create_time`,`bsi`.`cancel_time` FROM `" . $this->table() . "` as `bsi` WHERE 1 ";

        if (isset($where['style_sn']) && !empty($where['style_sn'])){
            $sql .= " AND `bsi`.`style_sn` = '{$where['style_sn']}'";
        }
        if(isset($where['product_type_id']) && !empty($where['product_type_id'])){
            $sql .=" AND `bsi`.`product_type` in ({$where['product_type_id']}) ";
        }
        if(isset($where['cat_type_id']) && !empty($where['cat_type_id'])){
            $sql .=" AND `bsi`.`style_type` = '{$where['cat_type_id']}' ";
        }
        if(isset($where['check_status']) && !empty($where['check_status'])){
            $sql .=" AND `bsi`.`check_status` = '{$where['check_status']}' ";
        }
        $sql .= " GROUP BY `bsi`.`style_id` ORDER BY `bsi`.`style_id` DESC";
        $data = $this->db()->getPageListNew($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    /**
     * 	pageList_other，分页列表
     *
     * 	@url ApplicationController/search
     */
    function pageList_other($where, $page, $pageSize = 10, $useCache = true) {
        $sql = "SELECT `bsi`.* FROM `" . $this->table() . "` as `bsi` WHERE 1 ";
        if (isset($where['style_sn']) && !empty($where['style_sn'])){
            $sql .= " AND `bsi`.`style_sn` = '{$where['style_sn']}'";
        }
        if (isset($where['style_sn_in']) && !empty($where['style_sn_in'])) {
            $sql .= " AND `bsi`.`style_sn` in ({$where['style_sn_in']})";
        }
        if(isset($where['product_type_id']) && !empty($where['product_type_id'])){
            $sql .=" AND `bsi`.`product_type` in ({$where['product_type_id']}) ";
        }
        if(isset($where['cat_type_id']) && !empty($where['cat_type_id'])){
            $sql .=" AND `bsi`.`style_type` = '{$where['cat_type_id']}' ";
        }
        if(isset($where['style_sex']) && !empty($where['style_sex'])){
            $sql .=" AND `bsi`.`style_sex` = '{$where['style_sex']}' ";
        }
        if(isset($where['style_name']) && !empty($where['style_name'])){
            $sql .=" AND `bsi`.`style_name` like '" . addslashes($where['style_name']) . "%'";
        }

       /*用分组判断物控与废物空  
       if($_SESSION['userType']<>1){
            if(isset($where['is_wukong']) && $where['is_wukong'] == true){
                $sql .=" AND `bsi`.`is_wukong` = 1";
            }else{
                $sql .=" AND `bsi`.`is_wukong` <> 1";
            }
        }     */
        if(isset($where['xilie']) && !empty($where['xilie'])){
            if(count($where['xilie'])==1){
                 $sql.= " AND `xilie` like '%,".$where['xilie'][0].",%'";
            }else{
                $str = "";
                foreach ($where['xilie'] as $val){
                     $str.=" `xilie` like '%,".$val.",%' or ";
                }
                $str = rtrim($str," or");
                $sql .= " AND (".$str.")";
               
            }
        }
        if(isset($where['company_type']) && !empty($where['company_type'])){
            if(count($where['company_type'])==1){
                 $sql.= " AND `company_type_id` like '%,".$where['company_type'][0].",%'";
            }else{
                $str = "";
                foreach ($where['company_type'] as $val){
                     $str.=" `company_type_id` like '%,".$val.",%' or ";
                }
                $str = rtrim($str," or");
                $sql .= " AND (".$str.")";
               
            }
        }
        if(isset($where['is_made']) && $where['is_made'] !== ''){// 2015-12-25 zzm boss-1013
            $sql .=" AND `bsi`.`is_made` = {$where['is_made']}";
        }
        if(isset($where['check_status']) && !empty($where['check_status'])){
            $sql .=" AND `bsi`.`check_status` = '{$where['check_status']}' ";
        }
        if(isset($where['check_status_zuofei']) && !empty($where['check_status_zuofei'])){
            $sql .=" AND `bsi`.`check_status` != {$where['check_status_zuofei']}";
        }
        if (isset($where['is_xiaozhang']) && !empty($where['is_xiaozhang'])){
            $sql .= " AND `bsi`.`bang_type`= ".$where['is_xiaozhang'];
        }
        if(isset($where['is_kuanprice'])){
            if($where['is_kuanprice'] == 1){
                $sql .=" AND `bsi`.`style_id` in (SELECT style_id FROM `app_price_by_style`)";
            }else{
                $sql .=" AND `bsi`.`style_id` not in (SELECT style_id FROM `app_price_by_style`)";
            }
        }
        if(!empty($where['market_xifen'])){
            $sql .= " AND `bsi`.`market_xifen`= '{$where['market_xifen']}'";
        }
        $sql .= " ORDER BY `bsi`.`style_id` DESC";
        //echo $sql;die;
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    /**
     * 获取产品线列表
     * @return type
     */
    public function getProductTypeList($param=0) {
        //$data = array(array('id'=>1,'name'=>'其他'),array('id'=>2,'name'=>'黄金'),array('id'=>3,'name'=>'素金饰品'),array('id'=>4,'name'=>'黄金饰品'),array('id'=>5,'name'=>'结婚钻石饰品'));
        $model = new AppProductTypeModel(11);
        $data = $model->getCtlList($param);
        return $data;
    }
	/**
	*获取畅销度
	*
	*/
	public function getSellTypeList($param=0) {
        $model = new AppCatTypeModel(11);
        $data = $model->getCtlList($param);
        return $data;
    }
    /**
     * 获取款式分类列表
     * @return type
     */
    public function getStyleTypeList($param=0) {
        //$data = array(array('id'=>1,'name'=>'戒指'),array('id'=>2,'name'=>'吊坠'),array('id'=>3,'name'=>'项链'),array('id'=>4,'name'=>'耳钉'),array('id'=>5,'name'=>'耳环'));
        $model = new AppCatTypeModel(11);
        $data = $model->getCtlList($param);
        return $data;
    }

    public function checkStyleId($style_id) {
        $sql = "SELECT * FROM `" . $this->table() . "`  WHERE style_id = '{$style_id}'";
        return $this->db()->getOne($sql);
    }

    /**
     * 	style_sn，取款
     *
     * 	@url ApplicationController/search
     */
    function getStyleStyleByStyle_sn($where) {
        $sql = "SELECT * FROM `" . $this->table() . "`  WHERE 1 ";

        if ($where['style_sn'] != "") {
            $sql .= " AND `style_sn` = '" . addslashes($where['style_sn']) . "'";
        }
       
        if(isset($where['check_status']) && $where['check_status']!=""){
            $sql .= " AND `check_status` = '" . $where['check_status'] . "'";
        }
        $sql .= "ORDER BY `style_id` DESC LIMIT 0,1 ";
       
        $data = $this->db()->getAll($sql);
        return $data;
    }
    
    /*
     * 取具体的款数据
     * 
     */
    function getStyleById($style_id){
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE `style_id`=".$style_id;
        return $this->db()->getRow($sql);
    }
    function getStyleBySn($style_sn){
        $sql = "SELECT * FROM `" . $this->table() . "` WHERE `style_sn`='{$style_sn}'";
        return $this->db()->getRow($sql);
    }

    /**
     * 	style_sn，取款
     *
     * 	@url ApplicationController/search
     */
    function getStyleByStyle_sn($where) {
        $sql = "SELECT * FROM `" . $this->table() . "`  WHERE 1 ";

        if ($where['style_sn'] != "") {
            $sql .= " AND style_sn = '" . addslashes($where['style_sn']) . "'";
        }
        $sql .= "ORDER BY style_id DESC LIMIT 0,1 ";
        $data = $this->db()->getAll($sql);
        return $data;
    }
    
     //添加日志
    public function addBaseStyleLog($data) {
        $baseStyleLogModel = new BaseStyleLogModel(12);
        $olddo = array();
        $newdo = array(
            'style_id'=>$data['style_id'],
            'create_user'=>$_SESSION['userName'],
            'create_time'=>date("Y-m-d H:i:s"),
            'remark'=>$data['remark'],
        );
        return $baseStyleLogModel->saveData($newdo, $olddo);
    }
    
    //添加作废原因
    public function addCancleReason($data) {
        $listCancleModel = new ListCancleReasonModel(12);
        $olddo = array();
        $newdo = array(
            'style_id'=>$data['style_id'],
            'create_user'=>$_SESSION['userName'],
            'create_time'=>date("Y-m-d H:i:s"),
            'type'=>$data['type'],
            'remark'=>$data['remark'],
        );
        return $listCancleModel->saveData($newdo, $olddo);
    }
    
    //获取此产品线，款式分类的款的个数
    public function getStyleCountByWhere($where) {
        $str = "";
        if(isset($where['style_sex'])){
            $str .= " AND `style_sex`=".$where['style_sex'];
        }
        if(isset($where['style_type'])){
            $str .= " AND `style_type`=".$where['style_type'];
        }
        $sql = "SELECT style_sn FROM `" . $this->table() . "`  WHERE 1 ".$str." ORDER BY `style_id` desc limit 1";
        
        return $this->db()->getRow($sql);
    }
    
    /*
     * 获取符合条件的最新创建的一条款式数据
     */
    public function getLatestStyleSnByWhere($where){
    	if (!isset($where['style_sn_prefix']) || empty($where['style_sn_prefix'])) {
    		throw new ObjectException('style_sn_prefix can not be null or empty.');
    	}
    	
    	$str = "";
    	if(isset($where['style_sex'])){
    		$str .= " AND `style_sex`=".$where['style_sex'];
    	}
    	if(isset($where['style_type'])){
    		$str .= " AND `style_type`=".$where['style_type'];
    	}

    	$sql = "SELECT style_sn FROM `" . $this->table() . "`  WHERE 1 ".$str." ORDER BY CONVERT(SUBSTRING(style_sn, ".(strlen($where['style_sn_prefix']) +1)."),UNSIGNED INTEGER) desc limit 1";
    	return $this->db()->getRow($sql);
    }
    public function updateXiLieByStyleSn($where_sn)
    {
     $sql = "update `".$this->table()."` set `xilie`=CONCAT('{$where_sn['xilieid']}',IFNULL(`xilie`,'')),`modify_time`=NOW() where `style_sn` ='{$where_sn['style_sn']}'";
       return $this->db()->query($sql);
    }
     public function getStyleXiLieByStyleSn($style_sn,$xilie) {
     
        $sql = "SELECT `xilie` FROM `" . $this->table() . "`  WHERE `style_sn`='$style_sn' and xilie like '%,".$xilie.",%'";
        
        return $this->db()->getRow($sql);
    }
    public function getStyleXiLieBySn($style_sn) {
     
        $sql = "SELECT `xilie` FROM `" . $this->table() . "`  WHERE `style_sn`='$style_sn' ";
        
        return $this->db()->getOne($sql);
    }
    public function getStyleStatus($style_sn)
    {
        $sql="SELECT CASE WHEN `check_status`=4 THEN '无效'
        WHEN `check_status`=5 THEN '作废中'
        WHEN `check_status`=6 THEN '作废已驳回'
        WHEN `check_status`=7 THEN '已作废' END FROM `".$this->table()."` WHERE `style_sn`='$style_sn' AND `check_status` in(4,5,6,7) ";
        
        return $this->db()->getOne($sql);
    }
    public function getXiLieIdByName($name)
    {
        $sql="SELECT id FROM `front`.`app_style_xilie` where name='$name' and status=1 ";
        return $this->db()->getOne($sql);
    }
    public function getXilieById($id)
    {
        $sql="SELECT `xilie` FROM `" . $this->table() . "` where style_id='$id'  ";
        return $this->db()->getOne($sql);
    }
    public function getXilieNameByid($xilie)
    {
        $sql="SELECT name FROM `front`.`app_style_xilie` where id in(0{$xilie}0)  ";
       
        return $this->db()->getAll($sql);
    }

    public function updateStylesn($id,$new_style_sn)
    {
        if(empty($id) || empty($new_style_sn)){
            return false;
        }

        $sql = "UPDATE `".$this->table()."` SET `style_sn` = '{$new_style_sn}' WHERE `style_id` = {$id} LIMIT 1";
        $this->db()->query($sql);
    }


    public function updateStylesnByStyleId($id,$new_style_sn)
    {
        if(empty($id) || empty($new_style_sn)){
            return false;
        }

        $sql = "UPDATE `".$this->table()."` SET `style_sn` = '{$new_style_sn}' WHERE `style_id` = {$id} LIMIT 1";
        $this->db()->query($sql);

        $sql = "UPDATE `rel_style_attribute` SET `style_sn` =  '{$new_style_sn}' WHERE `style_id` = {$id}";
        $this->db()->query($sql);

        $sql = "UPDATE `app_xiangkou` SET `style_sn` = '{$new_style_sn}' WHERE `style_id` = {$id}";
        $this->db()->query($sql);

        $sql = "UPDATE `rel_style_factory` SET `style_sn` = '{$new_style_sn}' WHERE `style_id` = {$id}";
        $this->db()->query($sql);

        $sql = "UPDATE `app_style_gallery` SET `style_sn` = '{$new_style_sn}' WHERE `style_id` = {$id}";
        $this->db()->query($sql);

        $sql = "UPDATE `app_style_fee` SET `style_sn` = '{$new_style_sn}' WHERE `style_id` = {$id}";
        $this->db()->query($sql);

        $sql = "UPDATE `app_factory_apply` SET `style_sn` = '{$new_style_sn}' WHERE `style_id` = {$id}";
        $this->db()->query($sql);
    }
    
    /*
     * 插入到赠品管理表中giftgoods
     */
    public function addZp($data){
    	$keys =array_keys($data);
    	$vals =array_values($data);
     	$ret=ApiModel::giftman_api($keys,$vals,'addZpFromStyle');
        return $ret;  
    }
    
    /*
     * 判断已审核的赠品是否处于开启状态
     */
    public function getZpStatusByStyle_sn($sn){
    	$keys = array('style_sn');
    	$vals = array($sn);
    	$ret = ApiModel::giftman_api($keys,$vals,'getZpStatusByStyle_sn');
    	return $ret;
    }
    
    /*
     * 更新赠品信息
    */
    public function updateZpStatusByStyle_sn($data){
    	$keys =array_keys($data);
    	$vals =array_values($data);
       
    	$ret = ApiModel::giftman_api($keys,$vals,'updateZpStatusByStyle_sn');
    
    	return $ret;
    }
    
    /*
     * 通过款号查询是否可以销账
     */
    public function getXzInfo($sn){
    	$keys =array('style_sn');
    	$vals =array($sn);
    	$ret = ApiModel::giftman_api($keys,$vals,'getXzInfo');
    	return $ret;
    }
    
    /*
     * 通过id更改审核状态
     */
    public function updateStatusById($id){
    	
    	$sql ="update ".$this->table()." set check_status =5 where style_id =".$id;
    	
    	$res = $this->db()->query($sql);
    	
    	return $res;
    	
    }
    
    function setgiftgoodslog($zp_sn,$content,$create_name,$create_time){
        if(empty($zp_sn))
        {
            return false;
        }
        $sql="INSERT INTO `app_order`.gift_goods_log (`zp_sn`,`content`,`create_name`,`create_time`) VALUES ('$zp_sn','$content','$create_name','$create_time')";

        return $this->db()->query($sql);
    } 
    
    public function  updateStyleSnNew($id,$new_style_sn)
    {
        if($id ==  ''){
            return false;
        }
        # code...
        $sql = "UPDATE `".$this->table()."` SET `style_sn` = '{$new_style_sn}' WHERE `style_id` = {$id} LIMIT 1";
        $this->db()->query($sql);
    }     

    /**
     *   修改款名称信息
    */
    public function updateStyleNameByStyleSn($style_sn,$style_name)
    {
        $sql = "UPDATE `base_style_info` SET `style_name` = '{$style_name}' WHERE `style_sn` = '{$style_sn}'";
        $this->db()->query($sql);
        $sql = "UPDATE `list_style_goods` SET `style_name` = '{$style_name}' WHERE `style_sn` = '{$style_sn}'";
        $this->db()->query($sql);
        //$sql = "UPDATE `base_salepolicy_goods` SET `goods_name` = '{$style_name}' WHERE `goods_sn` = '{$style_sn}' AND isXianhuo=0 ;";
        //$this->db()->query($sql);
    }

    //获取款式信息
    public function getStyleInfoByid($ids='')
    {
        $sql = "select `si`.`style_sn`,
        `si`.`style_name`,
        `t`.`cat_type_name`,
        `p`.`product_type_name`, 
        `si`.`is_sales`,
        `si`.`is_made`,
        `si`.`dismantle_status`,
        `si`.`style_remark`,
        `si`.`dapei_goods_sn`,
        `si`.`changbei_sn`,
        `si`.`style_sex`,
        `si`.`market_xifen`,
        `si`.`is_zp`,
        `si`.`sell_type`,
        `si`.`bang_type`,
        `si`.`sale_way`,
        `si`.`is_xz`,
        `si`.`zp_price`,
        `si`.`is_allow_favorable`,
        `si`.`is_gold`,
        `si`.`is_support_style`,
        `si`.`company_type_id`,
        `si`.`is_auto`
        from `base_style_info` `si` 
        inner join `app_cat_type` `t` on `t`.`cat_type_id` = `si`.`style_type` 
        inner join `app_product_type` `p` on `p`.`product_type_id` = `si`.`product_type`
        where `si`.`style_id` in(".$ids.")";
        //echo $sql;die;
        return $this->db()->getAll($sql);
    }
    
    public function getMarketXifenList(){
        $xifenList = array();
        $data = $this->db()->getAll("select distinct market_xifen from base_style_info where check_status<>7");
        foreach ($data as $vo){
            if($vo['market_xifen']!=""){
                $xifenList[$vo['market_xifen']] = $vo['market_xifen'];
            }
        }
        return $xifenList;
    }

    public function getCompany_type_id($style_sn){

        $sql="select `company_type_id` FROM ".$this->table()." where style_sn='".$style_sn."'";
        return $this->db()->getOne($sql);
    }

    public function setCompany_type_log($style_sn,$newdo,$olddo){

        $sql=" INSERT INTO base_style_company_log(newdo,olddo,create_time,modifi_user,style_sn) VALUE('".$newdo."','".$olddo."',now(),'".$_SESSION['userName']."','".$style_sn."')";
        return  $this->db()->query($sql);
    }

    public function getStyleAttr($attr_code,$style_sn){
        $sql="select r.attribute_value from rel_style_attribute r,app_attribute a where r.attribute_id=a.attribute_id and a.attribute_code='{$attr_code}' and r.style_sn='{$style_sn}'";
        $attr_ids=$this->db()->getOne($sql);
        $attr_list=array();
        if($attr_ids){
            $attr_id_list=explode(',',$attr_ids);
            if($attr_id_list){
                foreach ($attr_id_list as $key => $v) {
                    $sql="select att_value_name from app_attribute_value where att_value_status=1  and att_value_id ='{$v}'";
                    $attr=$this->db()->getOne($sql);
                    if($attr)
                       $attr_list[]= $attr; 
                }
            }                
        }
        return $attr_list;
    }
}

