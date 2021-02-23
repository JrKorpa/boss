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
		parent::__construct($id,$strConn);
	}

    public function getGoodsAttr($is_xianhuo = true) {
		$clarity = $this->getClarityList();
		$color = $this->getColorList();
		$caizhi = $this->getCaizhiList();
		$jinse = $this->getJinse($is_xianhuo);
		$xiangqian = $this->getXiangqianList();
		$face_work = $this->getFaceworkList();
        return array(
            'clarity'=>$clarity,
            'color'=>$color,
            'caizhi'=>$caizhi,
            'jinse'=>$jinse,
            'xiangqian'=>$xiangqian,
            'face_work'=>$face_work            
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
    
    public function getClarityList() {
        return array(
            'FL'=>'FL',
            'IF'=>'IF',
            'VVS'=>'VVS','VVS1'=>'VVS1','VVS2'=>'VVS2',
            'VS'=>'VS','VS1'=>'VS1','VS2'=>'VS2',
            'SI'=>'SI','SI1'=>'SI1','SI2'=>'SI2',
            'I1'=>'I1','I2'=>'I2',
            'P P1'=>'P P1','无'=>'无');
    }
    
    public function getColorList() {
        return array(
            'D'=>'D','D-E'=>'D-E',
            'E'=>'E','E-F'=>'E-F',
            'F'=>'F','F-G'=>'F-G',
            'G'=>'G','G-H'=>'G-H',
            'H'=>'H','H+'=>'H+','H-I'=>'H-I',
            'I'=>'I','I-J'=>'I-J',
            'J'=>'J','J-K'=>'J-K',
            'K'=>'K','K-L'=>'K-L',
            'L'=>'L','M'=>'M',
            '白色'=>'白色','黑色'=>'黑色','金色'=>'金色');
    }
    public function getShapeList(){
        return array(1 => '圆形', 2 => '公主方形', 3 => '祖母绿形', 4 => '橄榄形', 5 => '椭圆形', 6 => '水滴形', 7 => '心形', 8 => '坐垫形', 9 => '辐射形', 10 => '方形辐射形', 11 => '方形祖母绿', 12 => '三角形',13=>'戒指托',14=>'异形');
    }
    //证书类型列表
    public function getCertTypeList(){
        return array('GIA' => 'GIA','AGL' => 'AGL', 'EGL' => 'EGL', 'HRD' => 'HRD', 'HRD-D' => 'HRD-D', 'HRD-S' => 'HRD-S','IGI' => 'IGI', 'NGTC' => 'NGTC', '其他'=>'其他');
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
         $Cert_arr = array('HRD-D','GIA','HRD','IGI','HRD-S');
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
        if(isset($where['is_stock_goods']) && $where['is_stock_goods']!=''){
            $str .=" `is_stock_goods`='".$where['is_stock_goods']."' AND"; 
        }
        if(isset($where['goods_type_no']) && $where['goods_type_no']!=''){
            $str .=" `goods_type`!='".$where['goods_type_no']."' AND"; 
        }
        if(isset($where['kuan_sn']) && $where['kuan_sn']!=''){
            $str .=" `kuan_sn`!='".$where['kuan_sn']."' AND"; 
        }
        if(isset($where['buchan_status']) && $where['buchan_status']!=''){
            $str .=" `buchan_status`!='".$where['buchan_status']."' AND";
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
        $sql = "SELECT `id` FROM `".$this->table()."` where `order_id` =".$order_id."  AND bc_id=0 AND (is_stock_goods=0 or is_peishi>0)";
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
        $sql = "select `od`.`favorable_status`,`od`.`favorable_price`,`od`.`order_id`,`oi`.`order_sn`,`od`.`goods_id`,`od`.`id` as `detail_id`,`od`.`goods_sn`,`od`.`goods_price`,`od`.`goods_name` from `base_order_info` as `oi`,`app_order_details` as `od` where `oi`.`id`=`od`.`order_id` and `od`.`id`=$details_id";
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
            $sql = "UPDATE `app_order_details` SET `goods_price`=`goods_price`+'".$data['xzprice']."' WHERE `id`=".$data['detail_id'];
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
    
     //根据部门id订单销售渠道线上or线下
    public function getOrdergiftchannelbydepid($id){
       
        $sql = "SELECT `channel_class` FROM `cuteframe`.`sales_channels`    WHERE `id`=$id";
   
        return $this->db()->getOne($sql);
    }

    //根据订单ID获取订单明细信息
    public function getDetailsInfoByOrderSn($order_id)
    {
        # code.
        $sql = "select `goods_sn` from `app_order`.`app_order_details` where `order_id` = $order_id";
        $info = $this->db()->getAll($sql);

        $sql_num = "select sum(`goods_count`) from `app_order`.`app_order_details` where `order_id` = $order_id";
        $num = $this->db()->getOne($sql_num);

        return array('info'=>$info, 'num'=>$num);
    }
    
}	

?>