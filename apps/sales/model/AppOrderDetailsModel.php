<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderDetailsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-10 21:17:07
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderDetailsModel extends Model
{
	
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_order_details';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"主键",
            "order_id"=>"订单号",
            "goods_id"=>"货号",
            "ext_goods_sn"=>"原始货号",
            "goods_sn"=>"款号",
            "goods_name"=>"商品名称",
            "goods_price"=>"商品价格",
            "favorable_price"=>"优惠价格:正数代表减钱，负数代表加钱",
            "goods_count"=>"商品个数",
            "create_time"=>"添加时间",
            "modify_time"=>" ",
            "create_user"=>"创建人",
            "details_status"=>" ",
            "send_good_status"=>"1未发货2已发货3收货确认4允许发货5已到店",
            "buchan_status"=>"布产状态:1初始化2待分配3已分配4生产中7部分出厂9已出厂10已取消",
            "is_stock_goods"=>"是否是现货：1现货 0期货",
            "is_return"=>"退货产品 0未退货1已退货",
            "details_remark"=>"备注",
            "cart"=>"石重",
            "cut"=>"切工",
            "clarity"=>"净度",
            "color"=>"颜色",
            "zhengshuhao"=>"证书号",
            "zhengshuhao_org"=>"原始证书号",
            "caizhi"=>"材质",
            "jinse"=>"金色",
            "jinzhong"=>"金重",
            "zhiquan"=>"指圈",
            "kezi"=>"刻字",
            "face_work"=>"表面工艺",
            "xiangqian"=>"镶嵌要求",
            "goods_type"=>"商品类型lz:裸钻",
            "cat_type"=>"款式分类：-1裸钻",
            "product_type"=>"产品线：-1是裸钻",
            "bc_id"=>"期货布产id",
            "favorable_status"=>"优惠审核状态；1：保存；2：提交申请；3：审核通过；4：审核驳回",
            'is_4c'=>"是否是4C订单"
        );
        // 配货专用仓库
        $this->warehouse_arr = array(
            2=>'线上低值库',
            79=>'深圳珍珠库',
            96=>'总公司后库',
            184=>'黄金网络库',
            386=>'彩宝库',
            482=>'淘宝黄金',
            484=>'淘宝素金',
            486=>'线上钻饰库',
            516=>'物控库',
            672=>'轻奢库',
            673=>'彩钻库'
        );
        // 配货 颜色区间
        $this->dzcolor = array(
            '白色'=>"'白色'",
            'DE'=>"'D','E','D-E'",
            'D-E'=>"'D','E','D-E'",
            'FG'=>"'F','F-G','G'",
            'F-G'=>"'F','F-G','G'",
            'H'=>"'H'",
            'IJ'=>"'I','I-J','J'",
            'I-J'=>"'I','I-J','J'",
            'KL'=>"'K','K-L','L','白色',''",
            'K-L'=>"'K','K-L','L','白色',''"
        );
        // 配货 净度区间
        $this->dzjingdu = array(
            'FL'=>"'FL'",
            'IF'=>"'IF'",
            'VVS'=>"'VVS','VVS1','VVS2'",
            'VS'=>"'VS','VS1','VS2'",
            'SI'=>"'SI','SI1','SI2'",
            'I'=>"'I','I1','I2','P','P1','白色','不分级'",
            'P'=>"'I','I1','I2','P','P2','白色','不分级'",
            '不分级净度'=>"'I','I1','I2','P','P1','','不分级'"
        );
        // 配货 没有颜色的材质
        $this->dzcaizhi = array(
            'PT990','PT950','PT900','足金','千足银','千足金银','S925','S990','裸石','其他','无'
        );
		parent::__construct($id,$strConn);
	}

    public function getGoodsAttr($is_xianhuo = true) {
		$clarity = $this->getClarityList();
		$color = $this->getColorList();
		$caizhi = $this->getCaizhiList();
		$jinse = $this->getJinse($is_xianhuo);
		$xiangqian = $this->getXiangqianList();
		$face_work = $this->getFaceworkList();
        $cert = $this->getCertList();
		$cert_type = $this->getCertTypeList();
        return array(
            'clarity'=>$clarity,
            'color'=>$color,
            'caizhi'=>$caizhi,
            'jinse'=>$jinse,
            'xiangqian'=>$xiangqian,
            'face_work'=>$face_work,
            'cert'=>$cert,          
            'cert_type'=>$cert_type            
        );
    }
    /**
     * 材质颜色（金色）
     * @param string $is_xianhuo
     * @return array $attr_list
     */
    public function getJinse($is_xianhuo = true) {

        $goodsAttrModel = new GoodsAttributeModel(17);
        $data = $goodsAttrModel->getAttributeValues("caizhiyanse");
        $attr_list = array_column($data,"attribute_value","attribute_value");
        return $attr_list;
    }
    
	
	 function get_retrun_goods($where,$page,$pageSize=10,$useCache=true){
                 $sql = "select * from `app_order`.`app_return_goods` where order_id = " . $where['order_id'];
		 
		 $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
                  
		return $data['data'];
        }
	
    public function getClarityList() {
        return array(
            "不分级"=>"不分级",
            'FL'=>'FL',
            'IF'=>'IF',
            'VVS'=>'VVS','VVS1'=>'VVS1','VVS2'=>'VVS2',
            'VS'=>'VS','VS1'=>'VS1','VS2'=>'VS2',
            'SI'=>'SI','SI1'=>'SI1','SI2'=>'SI2',
            'I1'=>'I1','I2'=>'I2','P'=>'P',
            'P1'=>'P1','无'=>'无');
    }
    
    public function getColorList() {
        return array("不分级"=>"不分级",
            'D'=>'D','D-E'=>'D-E',
            'E'=>'E','E-F'=>'E-F',
            'F'=>'F','F-G'=>'F-G',
            'G'=>'G','G-H'=>'G-H',
            'H'=>'H','H+'=>'H+','H-I'=>'H-I',
            'I'=>'I','I-J'=>'I-J',
            'J'=>'J','J-K'=>'J-K',
            'K'=>'K','K-L'=>'K-L',
            'L'=>'L','M'=>'M',
            '白色'=>'白色','黑色'=>'黑色','金色'=>'金色','无'=>'无');
    }

    public function getCertList() {
        return array('HRD-D','GIA','HRD','IGI','DIA','AGL','EGL','NGTC','NGGC','HRD-S','无');
    }
    public function getShapeList(){
        return array(1 => '圆形', 2 => '公主方形', 3 => '祖母绿形', 4 => '橄榄形', 5 => '椭圆形', 6 => '水滴形', 7 => '心形', 8 => '坐垫形', 9 => '辐射形', 10 => '方形辐射形', 11 => '方形祖母绿', 12 => '三角形',13=>'戒指托',14=>'异形',15=>'梨形',16=>'阿斯切',17 => '马眼', 18 => '长方形', 19 => '雷迪恩');
    }
    //证书类型列表
    public function getCertTypeList(){
        return array('GIA' => 'GIA','AGL' => 'AGL', 'EGL' => 'EGL', 'HRD' => 'HRD', 'HRD-D' => 'HRD-D', 'HRD-S' => 'HRD-S','IGI' => 'IGI', 'NGTC' => 'NGTC','NGSTC'=>'NGSTC', '其他'=>'其他','无'=>'无');
    }
    /**
     * 材质
     * @return unknown
     */
    public function getCaizhiList() {
        $goodsAttrModel = new GoodsAttributeModel(17);
        $data = $goodsAttrModel->getAttributeValues("caizhi");
        $attr_list = array_column($data,"attribute_value","attribute_value");
        return $attr_list;
    }
    
    public function getXiangqianList($numeral_key = true) {
        return $numeral_key ? 
            array('1'=>'工厂配钻，工厂镶嵌','2'=>'不需工厂镶嵌','7'=>"镶嵌4C裸钻",'8'=>'镶嵌4C裸钻，客户先看钻','3'=>'需工厂镶嵌','4'=>'客户先看钻再返厂镶嵌','5'=>'成品','6'=>'半成品') :
            array('工厂配钻，工厂镶嵌'=>'工厂配钻，工厂镶嵌','不需工厂镶嵌'=>'不需工厂镶嵌','镶嵌4C裸钻'=>'镶嵌4C裸钻','镶嵌4C裸钻，客户先看钻'=>'镶嵌4C裸钻，客户先看钻','需工厂镶嵌'=>'需工厂镶嵌','客户先看钻再返厂镶嵌'=>'客户先看钻再返厂镶嵌','成品'=>'成品','半成品'=>'半成品');
    }
    /**
     * 表面工艺
     * @return unknown
     */
    public function getFaceworkList() {
        $goodsAttrModel = new GoodsAttributeModel(17);
        $data = $goodsAttrModel->getAttributeValues("biaomiangongyi");
        $attr_list = array_column($data,"attribute_value","attribute_value");
        return $attr_list;
     }
    
    public function getBuchanList() {
        return array('0'=>'普通件','1'=>'加急件','2'=>'特急件');
    }

    public function getCaiZuanAttr(){
        //颜色(Color) ：黄钻 Yellow   蓝钻 Blue   粉钻 Pink   橙钻 Orange  绿钻 Green  红钻  Red  香槟钻 Champagne  灰钻 Grey  紫钻 Purple  变色龙 Multicolor  蓝紫钻 Royal_purple  白钻 White  黑钻 Black  阿盖尔粉钻 Argyle
        $Color_arr = array('Yellow', 'Blue', 'Pink', 'Orange','Green', 'Red', 'Champagne', 'Grey','Purple', 'Multicolor', 'Royal_purple', 'White', 'Black', 'Argyle');
        //形状(Shape) ：圆形 Round   公主方 Princess   祖母绿 Emerald   椭圆 Oval  橄榄 Marquise  雷蒂恩  Radiant  心形 Heart  垫形 Asscher
         $Shape_arr = array('Round', 'Princess', 'Emerald', 'Oval','Marquise', 'Radiant', 'Heart', 'Asscher');
        //净度(Clarity)
         $Clarity_arr = array('I1', 'IF', 'VVS1', 'VVS2', 'VS1', 'VS2', 'SI1');
        //彩钻颜色分级(Color_grade)：微 Faint   Very Light(很淡)   Light(淡)   Fancy Light(淡彩)  Fancy(中彩)  Fancy Intense(浓彩)  Fancy Dark(暗淡)  Fancy Deep(深彩)   Fancy Vivid(艳彩)
         $Color_grade_arr = array('Faint', 'Very,Light', 'Light', 'Fancy,Light', 'Fancy', 'Fancy,Intense', 'Fancy,Dark','Fancy,Deep','Fancy,Vivid');
        //证书类型
         $Cert_arr = array('HRD-D','GIA','HRD','IGI','HRD-S','NGSTC');
        return array('clarity'=>$Clarity_arr,'color'=>$Color_arr,'shape'=>$Shape_arr,'olor_grade'=>$Color_grade_arr,'cert_arr'=>$Cert_arr);
    }
    
    public function getGoodsAttr2() {
        $xiangqian = array('1'=>'工厂配钻，工厂镶嵌','2'=>'不需工厂镶嵌','3'=>'需工厂镶嵌','4'=>'客户先看钻再返厂镶嵌','5'=>'成品','6'=>'半成品','7'=>'镶嵌4C裸钻');
        $caizhi = array('1'=>'默认','2'=>'无','3'=>'9K','4'=>'10K','5'=>'18K','6'=>'24K','7'=>'PT950','8'=>'PT900','9'=>'S925' );
        $jinse = array('1'=>'默认','2'=>'无','3'=>'按图做','4'=>'玫瑰金','5'=>'白','6'=>'黄','7'=>'黄白','8'=>'彩金','9'=>'分色' );
        $face_work = array('1'=>'无','2'=>'拉沙','3'=>'光面','4'=>'磨砂','5'=>'磨砂+光面','6'=>'拉沙+光面' );
        return array('face_work'=>$face_work,'caizhi'=>$caizhi,'jinse'=>$jinse,'xiangqian'=>$xiangqian);
    }
	
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{

		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
        if(!empty($where['order_id'])){
            $str .= " `order_id`= ".$where['order_id']." AND ";
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
    
	/**
	 * 查询订单商品，并查找之前的手寸
	 */
	function get_order_goods ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT aod.*,s.shoucun FROM `app_order`.`app_order_details` AS aod left JOIN `warehouse_shipping`.`warehouse_goods` s ON aod.goods_id=s.goods_id";
		$str = '';
		if(!empty($where['order_id'])){
			$str .= " aod.`order_id`= ".$where['order_id']." AND ";
		}
		if(isset($where['id']) && !empty($where['id'])){
			$str .= " aod.`id`= ".$where['id']." AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY aod.`id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		foreach ($data['data'] as &$vo){
		    $vo['style_channel'] = '';
		    if($vo['cpdzcode']!=''){
		        $sql = "select style_channel from front.base_cpdz_code where `code`='{$vo['cpdzcode']}'";
		        $row = $this->db()->getRow($sql);
		        if(!empty($row)){
		            $vo['style_channel'] = $row['style_channel'];
		        }
		    }
		}
		return $data;
	}
    /*
     * 核算总价
     */
	public function goods_all_money($id)
	{
		$sql = "select  sum(`goods_price`) from `".$this->table()."` where 	`order_id` ='$id' and `details_status`=1 ";	
		return $this->db()->getOne($sql);
	}
    
    /*
     * 判断此订单的商品是否已经存在
     */
    public function getGoodsByOrderId($where){
        $sql = "SELECT * FROM `".$this->table()."` ";
        $str = '';
        if(isset($where['order_id']) && !empty($where['order_id'])){
            $str .=" `order_id`=".$where['order_id']." AND"; 
        }
        if(isset($where['goods_sn']) && !empty($where['goods_sn'])){
            $str .=" `goods_sn`=".$where['goods_sn']." AND"; 
        }
        if(isset($where['goods_id']) && !empty($where['goods_id'])){
            $str .=" `goods_id`='".$where['goods_id']."' AND"; 
        }
        if(isset($where['is_stock_goods'])){
            $str .=" `is_stock_goods`='".$where['is_stock_goods']."' AND"; 
        }
        if(isset($where['goods_type_no']) && $where['goods_type_no']!=''){
            $str .=" `goods_type`!='".$where['goods_type_no']."' AND"; 
        }
        if(isset($where['kuan_sn']) && $where['kuan_sn']!=''){
            $str .=" `kuan_sn`='".$where['kuan_sn']."' AND";
        }
        if(isset($where['buchan_status'])){
            $str .=" `buchan_status`='".$where['buchan_status']."' AND";
        }
        // 订单商品：1已布产 2未布产
        if(isset($where['is_buchan'])){
            if($where['is_buchan']==1) {
                $str .=" bc_id>0 AND";
            } else {
                $str .=" (bc_id='' or bc_id=0 or bc_id is null) AND";
            }
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
        
		$sql .= " ORDER BY `id` DESC";
        $data = $this->db()->getAll($sql);
		return $data;
    }
    
    /*
     * 通过order_id查询货品
    */
    public function getGoodsById($order_id){
    	
    	$sql = "SELECT `goods_id` FROM `".$this->table()."` ";
    	$str = '';
    	if(isset($order_id) && !empty($order_id)){
    		$str .=" `order_id`=".$order_id." AND";
    	}   
    	if($str)
    	{
    		$str = rtrim($str,"AND ");//这个空格很重要
    		$sql .=" WHERE ".$str;
    	}
    
    	$sql .= " ORDER BY `id` DESC";
   // echo $sql;exit;
    	$data = $this->db()->getAll($sql);
    	return $data;
    }
    public function getGoodsBcStatus($order_id)
    {
        $sql = "SELECT `id` FROM `".$this->table()."` where `order_id` =".$order_id."  AND (bc_id=0 or bc_id is null) AND is_return<>1 AND (is_stock_goods=0 or is_peishi>0)";
        $data = $this->db()->getAll($sql);
    	return $data;
    }
    
    /*
     * 通过order_id查询货品信息
    */
    public function getGoodsInfoById($order_id){
        
        $sql = "SELECT * FROM `".$this->table()."` ";
        $str = '';
        if(isset($order_id) && !empty($order_id)){
            $str .=" `order_id`=".$order_id." AND";
        }   
        if($str)
        {
            $str = rtrim($str,"AND ");//这个空格很重要
            $sql .=" WHERE ".$str;
        }
    
        $sql .= " ORDER BY `id` DESC";
   // echo $sql;exit;
        $data = $this->db()->getAll($sql);
        return $data;
    }
    
    /*
     * 通过$zhengshuhao查询货品信息
    */
    public function getGoodsInfoByZhengshuhao($zhengshuhao,$order_id=''){
    
        $sql = "SELECT * FROM `".$this->table()."` ";
    	$str = '';
    	if($order_id){
    	    $str .=" `order_id` != '".$order_id."' AND";
    	}
    	if(!empty($zhengshuhao)){
    		$str .=" `zhengshuhao`='".$zhengshuhao."' AND";
    	}
    	if($str)
    	{
    		$str = rtrim($str,"AND ");//这个空格很重要
    		$sql .=" WHERE ".$str;
    		$sql .="  AND is_return = 0 ";
    	}
    
    	$sql .= " ORDER BY `id` DESC";
    	$data = $this->db()->getRow($sql);
    	return $data;
    }
    
    
    /*
     * 通过Details_Id查询货品信息
    */
    public function getGoodsInfoByDetailsId($where){
    
    	$sql = "SELECT * FROM `".$this->table()."` ";
    	$str = '';
    	if(isset($where['id']) && !empty($where['id'])){
    		$str .=" `id`=".$where['id']." AND";
    	}
    	if($str)
    	{
    		$str = rtrim($str,"AND ");//这个空格很重要
    		$sql .=" WHERE ".$str;
    	}
    
    	$sql .= " ORDER BY `id` DESC";
    	$data = $this->db()->getRow($sql);
    	return $data;
    }
    
    
    /**
     * 获取订单和商品信息
     * @param type $details_id
     */
    public function getOrderInfo($details_id) {
        if($details_id < 1){
            return FALSE;
        }
        $sql = "select `od`.`favorable_status`,`od`.`favorable_price`,`od`.`order_id`,`oi`.`order_sn`,`od`.`goods_id`,`od`.`id` as `detail_id`,`od`.`goods_sn`,`od`.`goods_price`,`od`.`goods_name`,`oi`.`department_id`,`oi`.`mobile`,`oi`.`bespoke_id` from `base_order_info` as `oi`,`app_order_details` as `od` where `oi`.`id`=`od`.`order_id` and `od`.`id`=$details_id";
        return $this->db()->getRow($sql);
    }
    
    /**
     * 获取订单优惠券价格
     * @param type $order_id
     * @return type
     */
    public function getOrderCouponPrice($order_id) {
        $sql = "select `coupon_price` from `app_order_account` where `order_id`=$order_id";
        return $this->db()->getOne($sql);
    }
    //获取这个订单的明细总数量
    public function GetCountDe($order_id){
        $sql="SELECT COUNT(*) FROM ".$this->table()." WHERE `order_id`=".$order_id;
        return  $this->db()->getOne($sql);
    }
    
    /**
     * 使用优惠券更新订单优惠金额
     * @param type $data
     * @return boolean
     */
    public function updateAccountInfo($data) {
        if(!isset($data['order_id']) || $data['order_id']<1){
            return false;
        }
        if(!isset($data['coupon_price'])){
            return false;
        }
        $_sql = "select `order_amount`,`money_unpaid` from `app_order_account` where `order_id`={$data['order_id']}";
        $order_account = $this->db()->getRow($_sql);
		if($data['coupon_price']>=$order_account['order_amount']){
			$orderAmount = 0;
			$money_unpaid = 0;
			$data['coupon_price'] = $order_account['order_amount'];
		}else{			
			$orderAmount = $order_account['order_amount'] - $data['coupon_price'];
			$money_unpaid = $order_account['money_unpaid'] - $data['coupon_price'];
		}
        $sql = "update `app_order_account` set `coupon_price`={$data['coupon_price']},`order_amount`=$orderAmount,`money_unpaid`=$money_unpaid where `order_id`={$data['order_id']}";
        return $this->db()->query($sql);
    }
    

    /**
     * 使用代金券惠券更新订单优惠金额
     * @param type $data
     * @return boolean
     */
    public function updateDaijinquanAccount($data) {
        if(!isset($data['detail_id']) || empty($data['detail_id'])){
            return "订单参数不能为空";
        }
        if(!isset($data['daijinquan_price'])){
            return "优惠金额不能为空";
        }
        if(!isset($data['daijinquan_code'])){
            return "代金券兑换码不能为空";
        }

        $pdo = $this->db()->db();
        try{
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
                $pdo->beginTransaction();//开启事务 
                $sql="select id,order_id,goods_price,favorable_price,favorable_status,daijinquan_code,daijinquan_price,is_zp from app_order_details where id='{$data['detail_id']}'";
                $order_detail = $this->db()->getRow($sql);
                if(empty($order_detail)){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交
                    return "订单不存在";
                }
                 
                if(!empty($order_detail['daijinquan_code']) && $order_detail['daijinquan_price']>0){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                    
                    return "订单已用过代金券优惠,不可多次使用";
                }
                if($order_detail['is_zp']==1){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                    
                    return "订单赠品不可以申请代金券优惠";
                }
                if($order_detail['favorable_status']<>3 && $order_detail['favorable_price']>0){
                     $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                   
                    return "订单优惠未审核通过,不可以和优惠码叠加使用代金券优惠";
                }
             
                //一个自然月一个人只能用一张代金券 以订单第一次点款时间做
                $sql = "select o.id,o.mobile,o.department_id,a.order_amount,a.money_unpaid,o.order_pay_status from base_order_info o,app_order_account a where o.id=a.order_id and o.id='{$order_detail['order_id']}'";
                $order_info =$this->db()->getRow($sql);
                if(empty($order_info)){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                    
                    return "订单不存在";
                }
                if(!in_array($order_info['order_pay_status'],array(1,2))){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                    
                    return "未付款订单才能使用代金券优惠";
                }
                if($order_info['money_unpaid']==0){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                    
                    return "订单未付金额为0的场景不适合使用代金券优惠";
                }
                
                $month_time = date("Y-m")."-01 00:00:00"; 
                $sql = "select o.order_sn from base_order_info o,app_order_details d where o.id=d.order_id and o.mobile='{$order_info['mobile']}' and ifnull(d.daijinquan_code,'')<>'' and d.daijinquan_addtime>='{$month_time}'";
                $old_order_info =$this->db()->getRow($sql); 
                if(!empty($old_order_info)){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                    
                    return "客户手机号在本自然月内已使用代金券兑换码,订单号:{$old_order_info['order_sn']}";
                }

                if(bccomp($data['daijinquan_price'],$order_info['money_unpaid'],2)==1){
                    $data['daijinquan_price'] =  $order_info['money_unpaid'];
                }
                $sql ="update app_order_details set favorable_price=favorable_price+{$data['daijinquan_price']},daijinquan_code='{$data['daijinquan_code']}',daijinquan_price='{$data['daijinquan_price']}',favorable_status=3,daijinquan_addtime=now() where id='{$data['detail_id']}'";
                $pdo->exec($sql);
                $sql = "update app_order_account a set favorable_price=(select sum(d.favorable_price) from app_order_details d where d.order_id=a.order_id),a.order_amount=a.order_amount-{$data['daijinquan_price']},a.money_unpaid=a.money_unpaid-{$data['daijinquan_price']} where a.order_id='{$order_detail['order_id']}'";
                $pdo->exec($sql);
        }catch(Exception $ex){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交
                return json_encode($ex); 
        } 
        $pdo->commit(); // 如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交   
        return true;    
    }
    

    /**
     * 使用积分码 保存
     * @param type $data
     * @return boolean
     */
    public function updateJifenma($data) {
        if(!isset($data['detail_id']) || empty($data['detail_id'])){
            return "订单参数不能为空";
        }
        if(!isset($data['jifenma_point'])){
            return "赠送积分不能为空";
        }
        if(!isset($data['jifenma_code'])){
            return "积分码不能为空";
        }

        $pdo = $this->db()->db();
        try{
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
                $pdo->beginTransaction();//开启事务 
                
                $sql ="select * from cuteframe.point_code where point_code='{$data['jifenma_code']}'"; 
                $jifenma_info =$this->db()->getRow($sql);
                if(empty($jifenma_info)){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                    
                    return "积分码不存在";
                }
                if($jifenma_info['status']<>0){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                    
                    return "积分码已被使用";
                }


                $sql="select d.id,d.order_id,d.goods_price,d.favorable_price,d.favorable_status,d.jifenma_code,d.jifenma_point,d.is_zp,o.order_sn,o.order_status,o.send_good_status,o.department_id from app_order_details d,base_order_info o where d.order_id=o.id and d.id='{$data['detail_id']}'";
                $order_detail = $this->db()->getRow($sql);
                if(empty($order_detail)){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                    
                    return "订单不存在";
                }
                 
                if(!empty($order_detail['jifenma_code']) && $order_detail['jifenma_point']>0){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                    
                    return "订单货号已用过积分码,不可重复使用";
                }
                if($order_detail['is_zp']==1){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                    
                    return "订单赠品不可以使用积分码";
                }
                if($order_detail['order_status']<>2){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                    
                    return "订单未审核不可以使用积分码";
                }

                if(!in_array($order_detail['send_good_status'],array(1))){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                    
                    return "未发货订单才能使用积分码";
                }   

                if($jifenma_info['channel_id']<>$order_detail['department_id']){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                    
                    return "当前订单渠道和积分码渠道不相同";
                }

                if(bccomp($data['jifenma_point'],$order_detail['goods_price']*$jifenma_info['use_proportion']/100,2)==1){
                    $pdo->rollback();//事务回滚
                    $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交                    
                    return "赠送积分不能高于商品成交价的".$jifenma_info['use_proportion']."%";                   
                }             

                             
                $sql = "update app_order_details set jifenma_code='{$data['jifenma_code']}',jifenma_point='{$data['jifenma_point']}' where id='{$data['detail_id']}'";
                $pdo->exec($sql);
                $sql = "update cuteframe.point_code set status='1',order_sn='{$order_detail['order_sn']}',use_people_name='{$_SESSION['userName']}' where point_code='{$data['jifenma_code']}'";
                $pdo->exec($sql);
                $remark = "使用积分码赠送".$data['jifenma_point']."积分";
                $now = date('Y-m-d H:i:s');
                $sql = "insert into app_order.app_order_action select 0,o.id,o.order_status,o.send_good_status,o.order_pay_status,'{$_SESSION['userName']}','{$now}','{$remark}' from app_order.base_order_info o,app_order.app_order_details d where o.id=d.order_id and d.id='{$data['detail_id']}'"; 
                $pdo->exec($sql);

        }catch(Exception $ex){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交
                return json_encode($ex); 
        } 
        $pdo->commit(); // 如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); // 开启sql语句自动提交   
        return true;    
    }

    /*
     * 判断订单中的现货只能有一个，裸钻的期货只能有一个
     */
    public function checkOrderGoodsOnly($where) {
        if(!isset($where['goods_id'])){
            return false;
        }
        
        if(!isset($where['order_id'])){
            return false;
        }
        $goods_id = $where['goods_id'];
        $order_id = $where['order_id'];
        $sql = "select `id` from `app_order_details` where `goods_id` ='".$goods_id."' and `order_id`=".$order_id;
        return $this->db()->getRow($sql);
    }
    
    //删除天生一对商品
    public function deleteByWhere($where) {
        $str = '';
        if(isset($where['kuan_sn'])){
            $str .= " AND `kuan_sn`='".$where['kuan_sn']."'";
        }
       
        if(empty($str)){
            return false;
        }
        $sql = "DELETE FROM `app_order_details` WHERE 1 ".$str;
       
        return $this->db()->query($sql);
    }
    //获取天生一对的成本积分、折扣积分、奖励积分
    public function getPoint($where){
        $str = '';
        if(isset($where['kuan_sn'])){
            $str .= " AND `kuan_sn`='".$where['kuan_sn']."'";
        }

        if(empty($str)){
            return false;
        }
        $sql = "SELECT sum(original_point)as original,sum(discount_point) as discount,sum(reward_point) as reward as gross FROM `app_order_details` WHERE 1 ".$str;

        return $this->db()->query($sql);
    }
    public function ValenceDelete($updata,$id){

        
        if(empty($id)){
            return false;
        }
        if(empty($updata)){
            return false;
        }
        //事务处理
        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            //先删除所传明细id
            $sqld= "delete from app_order_details where id=".$id;
            $pdo->query($sqld);
            foreach($updata as $k=>$v){
                if($v['id']==$id){
                    continue;
                }
                $set='';
                foreach($v as $ke=>$va){
                    $set.=$ke."='".$va."',";
                }
                $set=trim($set,',');
                $sql = "UPDATE  `app_order_details` set ".$set." WHERE id=".$v['id'];
                //echo $sql;
                $pdo->query($sql);
            }
        }catch(Exception $e){//捕获异常
            $e = var_export($e);
            file_put_contents('binggai.wangshuai.txt',$e);
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                return false;
            }
            $pdo->commit();//如果没有异常，就提交事务
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return true;

    }

    public function XzGoodsPrice($data){
        if(empty($data['order_id'])){
            return false;
        }
        if(empty($data['detail_id'])){
            return false;
        }
        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            //修改明细表
			/*
            $sql = "UPDATE `app_order_details` SET `goods_price`=`goods_price`+'".$data['xzprice']."' WHERE `id`=".$data['detail_id'];*/
			//update by liulinyan 20151009 for boss-327
			$sql = "UPDATE `app_order_details` SET `goods_price`=`goods_price`+'".$data['xzprice']."',";
			$sql .= " is_zp=0,is_finance=2 WHERE `id`=".$data['detail_id'];
            $pdo->query($sql);
			
			//只要有一个商品为非赠品那就是非赠品单
			//修改订单为非赠品单不管现在是什么单
			$sql = "update base_order_info set is_zp=0 where id = '".$data['order_id']."'";
			$pdo->query($sql);
			
            //修改订单金额表
            $sql="UPDATE `app_order_account` SET `order_amount`=`order_amount`+'".$data['xzprice']."',`money_unpaid`=`money_unpaid`+'".$data['xzprice']."',`goods_amount`=`goods_amount`+'".$data['xzprice']."' WHERE `order_id`=".$data['order_id'];
            $pdo->query($sql);
            //增加发票的金额
            $sql="UPDATE `app_order_invoice` SET `invoice_amount`=`invoice_amount`+'".$data['xzprice']."' WHERE `order_id`=".$data['order_id'];
            $pdo->query($sql);
        }catch(Exception $e){//捕获异常
            $e = var_export($e);
            file_put_contents('binggai.wangshuai.txt',$e);
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return true;

    }
    
    //获取订单商品的钱
    public function getOrderGoodsMoney($order_id) {
        if(empty($order_id)){
            return FALSE;
        }
        $sql = "SELECT `id`,`goods_price`,`favorable_price`,`favorable_status` FROM `".$this->table()."` WHERE `order_id`=".$order_id;
        return $this->db()->getAll($sql);
    }
    
    //获取订单金额
    public function getOrderAccount($order_id){
        if(empty($order_id)){
            return FALSE;
        }
        
        $sql = "SELECT * FROM `app_order_account` WHERE `order_id` = ".$order_id;
        return $this->db()->getRow($sql);
    }
    
    //处理订单金额问题
    public function calculateOrderMoney($order_id) {
        if(empty($order_id)){
            return FALSE;
        }
        
        //获取此订单的所有商品的钱detail
        $all_goods_data = $this->getOrderGoodsMoney($order_id);
        //获取订单金额数据account
        $order_account = $this->getOrderAccount($order_id);
       
       
        //重新计算订单中所有商品的的价格和优惠
        $all_goods_price = 0;//商品的所有金额
        $all_goods_favorable_price = 0;//商品的所有优惠金额
        foreach ($all_goods_data as $val){
            $goods_favorable_price = 0;//默认是给0的，因为优惠是要读取审核通过的才能算做优惠
            if($val['favorable_status']==3){
                $goods_favorable_price = $val['favorable_price'];
            }
            $all_goods_price +=$val['goods_price'];
            $all_goods_favorable_price += $goods_favorable_price;
        }
        
        //订单的金额中，除了商品金额，和优惠重新计算，其他的金额都保持不变：重新计算订单金额
        //订单金额 = 商品总金额- 商品优惠 - 订单优惠： - 退款 + 配送费用 + 保价费用 + 支付费用 + 包装费用 + 贺卡费用
        $coupon_price = $order_account['coupon_price'];//订单优惠
        $real_return_price = $order_account['real_return_price'];//退款
        $shipping_fee = $order_account['shipping_fee'];//配送费用
        $insure_fee = $order_account['insure_fee'];//保价费用
        $pay_fee = $order_account['pay_fee'];//支付费用
        $pack_fee = $order_account['pack_fee'];//包装费用
        $card_fee = $order_account['card_fee'];//贺卡费用
        
        $money_paid = $order_account['money_paid'];//已付款
        
        //重新计算订单金额
        $order_amount = $all_goods_price - $all_goods_favorable_price - $coupon_price - $real_return_price + $shipping_fee + $insure_fee + $pay_fee + $pack_fee + $card_fee;
        //重新计算未付
        $new_money_unpaid = $order_amount - $money_paid;
        
        //修改订单account中：商品金额，未付，订单金额，优惠金额
        $sql = "update app_order_account set order_amount=".$order_amount." ,money_unpaid=".$new_money_unpaid." ,goods_amount=".$all_goods_price." ,favorable_price=".$all_goods_favorable_price." where order_id =".$order_id;
        $res =  $this->db()->query($sql);
        return $res;
    }
    //检查4C销售中，空托绑定的证书号是否可用，可用返回被绑定的货号和订单号，不可用false
    public function checkCertIdHasBindFor4C($zhengshuaho,$detail_id){
       $sql = "select d.goods_id,o.order_sn from ".$this->table()." d left join base_order_info o on d.order_id=o.id where d.zhengshuhao='{$zhengshuaho}' and d.is_peishi=2 and d.id<>'{$detail_id}'";
       $res =  $this->db()->getRow($sql);
       if(!empty($res)){
           return $res;
       }else{
           return false;
       }
    }//获取外部订单赠品
    public function getOutOrdergift(){
       
        $sql = "SELECT * FROM gift_goods WHERE sale_way like '%1%' and is_zp=1 and status=1";
        return $this->db()->getAll($sql);
    }
    //获取外部订单赠品详情
    public function getOutOrdergiftinfo($id){
       
        $sql = "SELECT * FROM gift_goods WHERE id='$id' ";
        return $this->db()->getRow($sql);
    }
    //根据赠品id订单销售渠道线上or线下
    public function getOrdergiftchannel($id){
       
        $sql = "SELECT `channel_class` FROM `app_order`.`base_order_info` as a inner join `cuteframe`.`sales_channels` as b on
        a.`department_id`=b.`id`  WHERE a.`id`=$id";
   
        return $this->db()->getOne($sql);
    }
    public function getGiftByChannelId($id){
        $sql = "SELECT * FROM gift_goods WHERE sale_way like concat('%', (SELECT `channel_class` FROM `cuteframe`.`sales_channels` WHERE id=$id ) ,'%') AND is_zp=1 AND status=1";
        return $this->db()->getAll($sql);
    }
     //根据部门id订单销售渠道线上or线下
    public function getOrdergiftchannelbydepid($id){
       
        $sql = "SELECT `channel_class` FROM `cuteframe`.`sales_channels`    WHERE `id`=$id";
   
        return $this->db()->getOne($sql);
    }
    //获取款式审核状态
    public function getCheckStatus($goods_sn){
    	$sql = "SELECT check_status FROM `front`.`base_style_info` WHERE style_sn='$goods_sn'";
    	 return $this->db()->getOne($sql);
    }
    
    
    public function getOrderArr($where){
    	$sql = "SELECT aod.*,s.shoucun FROM `app_order`.`app_order_details` AS aod left JOIN `warehouse_shipping`.`warehouse_goods` s ON aod.goods_id=s.goods_id  where 1 ".$where;
    	$sql .= " ORDER BY aod.`id` DESC";
    	return $this->db()->getAll($sql);
    }
    // 现货匹配，条件：款号、石重、颜色、净度、切工、证书号、材质+金色、金重、指圈、镶口
    //order_detail: goods_sn,cart,color,cut,clarity,zhengshuhao,caizhi+jinse,jinzhong,zhiquan,xiangkou
    //warehouse_goods: goods_sn,zuanshidaxiao,zhushiyanse,zhushiqiegong,zhushijingdu,zhengshuhao,caizhi,jinzhong,shoucun,jietuoxiangkou
    public function getOrderWarehouseGoods($order_item=array()){
        if (empty($order_item['goods_sn'])) return false;

        $warehouse_ids = implode(',', array_keys($this->warehouse_arr));
        $where = " warehouse_id in ({$warehouse_ids}) and order_goods_id<1 and is_on_sale=2 ";
        // 款号
        $where .= " and goods_sn = '{$order_item['goods_sn']}'";
        // 石重
        if (!empty($order_item['cart'])) {
            $where .= " and zuanshidaxiao = '{$order_item['cart']}'";
        } else {
            $where .= " and (zuanshidaxiao = '' or zuanshidaxiao is null)";
        }
        // 颜色
        if (!empty($order_item['color'])) {
            if (isset($this->dzcolor[$order_item['color']])) {
                $where .= " and zhushiyanse in ({$this->dzcolor[$order_item['color']]})";
            } else {
                $where .= " and zhushiyanse = '{$order_item['color']}'";
            }
        } else {
            $where .= " and (zhushiyanse = '' or zhushiyanse is null)";
        }
        // 净度
        if (!empty($order_item['clarity']) && $order_item['clarity']!='无') {
            if (isset($this->dzjingdu[$order_item['clarity']])) {
                $where .= " and zhushijingdu in ({$this->dzjingdu[$order_item['clarity']]})";
            } else {
                $where .= " and zhushijingdu = '{$order_item['clarity']}'";
            }
        } else {
            $where .= " and (zhushijingdu = '' or zhushijingdu is null)";
        }
        // 切工
        if (!empty($order_item['cut'])) {
            $where .= " and zhushiqiegong = '{$order_item['cut']}'";
        } else {
            $where .= " and (zhushiqiegong = '' or zhushiqiegong is null)";
        }
        // 证书号
        if (!empty($order_item['zhengshuhao']) && $order_item['zhengshuhao']!='-') {
            $where .= " and zhengshuhao = '{$order_item['zhengshuhao']}'";
        } else {
            $where .= " and (zhengshuhao = '' or zhengshuhao is null or zhengshuhao='-')";
        }
        // 材质+金色
        if (!empty($order_item['caizhi'])) {
            // 特殊材质不匹配颜色
            if (in_array($order_item['caizhi'], $this->dzcaizhi)) {
                $where .= " and caizhi = '{$order_item['caizhi']}'";
            } elseif (!empty($order_item['jinse'])) {
                $cz = $order_item['caizhi'].$order_item['jinse'];
                $where .= " and caizhi = '{$cz}'";
            }
        } else {
            $where .= " and (caizhi = '' or caizhi is null)";
        }
        // 金重
        if (!empty($order_item['jinzhong'])) {
            $where .= " and jinzhong = '{$order_item['jinzhong']}'";
        } else {
            $where .= " and (jinzhong = '' or jinzhong is null)";
        }
        // 指圈
        if (!empty($order_item['zhiquan'])) {
            $where .= " and shoucun = '{$order_item['zhiquan']}'";
        } else {
            $where .= " and (shoucun = '' or shoucun is null)";
        }
        // 镶口
        if (!empty($order_item['xiangkou'])) {
            $where .= " and jietuoxiangkou = '{$order_item['xiangkou']}'";
        } else {
            $where .= " and (jietuoxiangkou = '' or jietuoxiangkou is null)";
        }
        $sql = "select id,goods_id from `warehouse_shipping`.`warehouse_goods` s  where {$where} ORDER BY mingyichengben asc limit 1";
        return $this->db()->getRow($sql);
    }
    // 库存商品绑定到订单
    public function bindOrderWarehouseGoods($order_detail_id, $id) {
        $sql = "update `warehouse_shipping`.`warehouse_goods` set order_goods_id={$order_detail_id} where id=".$id;
        return $this->db()->query($sql);
    }

    //获取部门id
    public function getOrderDetailArr($id){
    	$sql="SELECT boi.department_id AS department_id,aod.goods_sn AS goods_sn,aod.is_stock_goods AS  is_stock_goods,aod.bc_id AS bc_id,aod.zhengshuhao,aod.delivery_status,aod.goods_type as goods_type FROM base_order_info AS boi LEFT JOIN app_order_details AS aod ON aod.order_id = boi.id  WHERE aod.id=$id";
    	return $this->db()->getRow($sql);
    }
    
    //在订单审核后和布产之前直接修改商品信息
    public function updateOrderDetailsGoodsById($data,$id)
    {
        # code...
        if(!$id){
            return false;
        }
        $tmp = '';
        $set = '';
        foreach ( $data as $k => $v ){
            $value = is_array($v) ? $v['value'] : $v;
            $tmp .= '`' . $k . '` = \'' . $value . '\',';
        }
        $set = rtrim($tmp,',');
        $sql = "UPDATE `app_order_details` SET {$set} WHERE `id` = {$id}";
        //echo $sql;die;
        return $this->db()->query($sql);
    }
   
    //bc_id
    public function updateOrderDetailsBcidByOrder_id($order_id,$bc_id)
    {
        $sql = "UPDATE `app_order_details` SET `bc_id`='$bc_id' WHERE `order_id` = '$order_id'";
        return $this->db()->query($sql);
    }    
    
    //bc_id
    public function updateOrderDetailsBcidById($id,$bc_id)
    {
    	$sql = "UPDATE `app_order_details` SET `bc_id`='$bc_id' WHERE `id` = $id";
    	return $this->db()->query($sql);
    }
    
    /**
     *
     * 获取退款商品总金额
     * @param int $order_goods_id
     * @param number $return_by
     */
    function getReturnGoodsPrice($order_id,$order_goods_id=0,$return_by=0){
        $sql = "select sum(real_return_amount) from app_return_goods a left join app_return_check b on a.return_id=b.return_id where a.order_id={$order_id} and b.leader_status<>2 and check_status>=4";
        if($order_goods_id>0){
            $sql .=" AND a.order_goods_id={$order_goods_id}";
        }
        if($return_by>0){
            $sql.=" AND a.return_by={$return_by}";
        }
        return (float)$this->db()->getOne($sql);
    }
    /*
     * 获取销售政策对应的商品
     */
    public function getStyleAttribute($good_id,$style_info){
        $sale_attribute_arr = array('goods_sn','cart','zhushi_num','clarity','color','zhengshuhao','caizhi','jinse','jinzhong','zhiquan','xiangkou','shape');
        //款式库货号组成：款号+材质+颜色+镶口+指圈
        $goods_id_arr = explode("-", $good_id);
        $goods_sn = $goods_id_arr[0];
        $caizhi = $goods_id_arr[1];
        $color = $goods_id_arr[2];
        $xiangkou = $goods_id_arr[3]/100;
        $zhiquan = $goods_id_arr[4];
        //从货号上可以知道是属性     
        $dd = new DictView(new DictModel(1));
        $color_arr = $dd->getEnumArray('style.color');
        $color_arr = array_column($color_arr,'label','note');
        //$color_arr = array('W'=>"白",'Y'=>"黄",'R'=>"玫瑰金",'C'=>'分色','RW'=>'玫瑰白','RY'=>'玫瑰黄','YW'=>'黄白','H'=>'彩金');
        $have_attribute = array(
            'goods_sn'=>$goods_sn,
            'caizhi'=>$caizhi,
            'jinse'=>$color_arr[$color],
            'cart'=>$xiangkou,            
            'color'=>$color,
            'zhiquan'=>$zhiquan,
            'xiangkou'=>$xiangkou
        );
        $new_attribute_arr = array();
        foreach ($style_info as $key=>$val){
            $attribute_code = isset($val['attribute_code'])?$val['attribute_code']:'';
            $attribute_value =isset($val['value'])?$val['value']:'';
            if(in_array($attribute_code, $sale_attribute_arr)){
                $new_attribute_arr[$attribute_code] = $attribute_value;
            }
        }
        //把没有是属性设成空
        foreach ($sale_attribute_arr as $val){
            if(!array_key_exists($val, $new_attribute_arr)){
                $new_attribute_arr[$val]="";
            }
            if(array_key_exists($val, $have_attribute)){
                $new_attribute_arr[$val] = $have_attribute[$val];
            }
        }
        $sql = "select a.stone_cat,a.stone_attr from front.rel_style_stone a inner join front.base_style_info b on a.style_id=b.style_id where b.style_sn='{$goods_sn}' and stone_position=1";
        $stoneInfoList = $this->db()->getAll($sql);
        //此 形状枚举仅针对 款式石头有效，不可公用.
        $shape_arr = array(1=>"垫形",2=>"公主方",3=>"祖母绿",4=>"心形",5=>"蛋形",6=>"椭圆形",7=>"橄榄形",8=>"三角形",9=>"水滴形",10=>"长方形",11=>"圆形",12=>"梨形",13=>"马眼形");
        $shape_name = "";
        $zhushi_num = 0;
        foreach ($stoneInfoList as $stoneInfo){
            $stoneAttr = unserialize($stoneInfo['stone_attr']);
            if(!empty($stoneAttr['number'])){
                $zhushi_num += (int)$stoneAttr['number'];
            }
            if(empty($stoneInfo)){
                $new_attribute_arr['shape'] = "";
            }else{
                if($stoneInfo['stone_cat']==1){
                    $shape_name .= "|圆形";
                }else{
                    $shape_id = isset($stoneAttr['shape_zhushi'])?$stoneAttr['shape_zhushi']:'';
                    if($shape_id==''){
                        $shape_id = isset($stoneAttr['shape_fushi'])?$stoneAttr['shape_fushi']:'';
                    }
                    $shape_name .= isset($shape_arr[$shape_id])?'|'.$shape_arr[$shape_id]:'|'.$shape_id;
                }
            }
        }
        $new_attribute_arr['shape'] = trim($shape_name,'|');
        $new_attribute_arr['zhushi_num'] = $zhushi_num;
        return $new_attribute_arr;
    }


    public function  getZhushiNum($goods_sn){
        $sql = "select a.stone_cat,a.stone_attr from front.rel_style_stone a inner join front.base_style_info b on a.style_id=b.style_id where b.style_sn='{$goods_sn}' and stone_position=1";
        $stoneInfoList = $this->db()->getAll($sql);
        //此 形状枚举仅针对 款式石头有效，不可公用.
        $zhushi_num = 0;
        foreach ($stoneInfoList as $stoneInfo){
            $stoneAttr = unserialize($stoneInfo['stone_attr']);
            if(!empty($stoneAttr['number'])){
                $zhushi_num += (int)$stoneAttr['number'];
            }

        }
        return $zhushi_num;
    }


    public function updateDetailPrice($price,$id)
    {
        $sql = "update app_order_details set `favorable_price` = '".$price."',`favorable_status` = 3 where `id` = ".$id;
        $res = $this->db()->query($sql);
        return $res;
    }
    //根据明细ID取绑定备货信息
    public function getOutOrderInfo($detail_id)
    {
        $sql = "select poi.order_sn,
    poi.dep_name,
    pi.p_sn from `app_order`.`purchase_order_info` poi inner join purchase.purchase_goods pg on pg.id = poi.purchase_id inner join purchase.purchase_info pi on pi.id = pg.pinfo_id where `poi`.detail_id = '".$detail_id."'";
        return $this->db()->getAll($sql);
    }

    //根据订单号查询是否有占用备货
    public function selectBhInfo($order_sn='')
    {
        $sql = "select * from app_order.purchase_order_info where order_sn = '".$order_sn."'";
        return $this->db()->getAll($sql);
    }

    //取消占用备货
    public function countermandOccupy($detail_id){
        if(empty($detail_id)){
            return false;
        }
        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            //解除商品与采购单的对应关系
            $sql = "DELETE FROM `app_order`.`purchase_order_info` WHERE `detail_id` = {$detail_id}";
            $pdo->query($sql);

            //解除商品与收货单的对应关系
            $sql = "update warehouse_shipping.warehouse_goods set order_goods_id = '' where order_goods_id = '".$detail_id."'";
            $pdo->query($sql);

            //$pdo->query('');//暂时报错
        }catch(Exception $e){//捕获异常
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return true;
    }
    //查询成品定制码
    public function getBaseCpdzCodeInfo($cpdzcode){
        $sql = "select * from front.base_cpdz_code where `code`='{$cpdzcode}'";
        return $this->db()->getRow($sql);
    }
    //修改
    public function updateCpdzCode($do,$where){
        $sql = $this->updateSqlNew($do,"front.base_cpdz_code",$where);
        //echo $sql;
        return $this->db()->query($sql);
    }


    //删除订单商品 如果使用了积分码 则修改对应的积分码为未使用
    public function update_jifenma_status($jifenma_code){
        $sql = "update cuteframe.point_code set status='0',order_sn=null,use_people_name=null where point_code='{$jifenma_code}'";
        //echo $sql;
        return $this->db()->query($sql);
    }

    public function getWarehouseGoodsInfo($goods_id){
        $sql = "select * from warehouse_shipping.warehouse_goods where goods_id = '{$goods_id}'";
        return $this->db()->getRow($sql);
    }

    public function getIdBespokeInfo($bespoke_id){
        $sql = "select * from front.app_bespoke_info where bespoke_id={$bespoke_id}";
        return $this->db()->getRow($sql);
    }
}	

?>