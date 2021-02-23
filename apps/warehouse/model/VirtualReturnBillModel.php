<?php
/**
 *  -------------------------------------------------
 *   @file		: VirtualReturnBillModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-09-08 15:45:07
 *   @update	:
 *  -------------------------------------------------
 */
class VirtualReturnBillModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'virtual_return_bill';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"单据编号",
"g_id"=>"无账修退流水号（虚拟货号）",
"bill_status"=>"单据状态",
"bill_type"=>"单据类型",
"create_user"=>"创建用户",
"create_time"=>"创建时间",
"from_company_id"=>"入库公司ID",
"from_company_name"=>"入库公司名称",
"from_warehouse_id"=>"入库仓库ID",
"from_warehouse_name"=>"入库仓库名称",
"out_company_id"=>"出库公司ID",
"out_company_name"=>"出库公司名称",
"out_warehouse_id"=>"出库仓库ID",
"out_warehouse_name"=>"出库仓库名称",
"check_time"=>"审核时间",
"check_user"=>"审核用户",
"express_sn"=>"快递单号",
"remark"=>"备注");
		parent::__construct($id,$strConn);
	}

    //拼接sql
    public function getSql($where = array())
    {
        //不要用*,修改为具体字段
        $sql = "SELECT b.id,g.no_account_gid,g.business_type,b.bill_status,b.bill_type,b.create_user,b.create_time,b.from_company_name,b.from_warehouse_name,b.out_company_name,b.out_warehouse_name,b.check_time,b.check_user,b.express_sn,b.remark,g.exist_account_gid,g.exist_account_user,g.exist_account_time,g.order_sn,g.goods_id,g.style_sn,g.ingredient_color,g.gold_weight,g.torr_type,g.product_line,g.style_type,g.finger_circle,g.credential_num,g.main_stone_weight,g.main_stone_num,g.deputy_stone_weight,g.deputy_stone_num,g.resale_price,g.place_company_name,g.place_warehouse_name,g.return_status,g.weixiu_fee FROM `".$this->table()."` `b` left join `virtual_return_goods` `g` on b.g_id = g.id";
        $str = '';    
        //zt隐藏
        if(SYS_SCOPE == 'zhanting')
        {
            //$str .="`b`.`bill_status` <> 2 AND ";
            $str.=" `b`.`hidden` <> 1 AND ";
        }           
        if(!empty($where['bill_id']))
        {
            $str .= "b.`id`=".$where['bill_id']." AND ";
        }
        if(!empty($where['bill_type']))
        {
            $str .= "b.`bill_type`='".$where['bill_type']."' AND ";
        }
        if(!empty($where['bill_status']))
        {
            $str .= "b.`bill_status`=".$where['bill_status']." AND ";
        }
        if(!empty($where['business_type']))
        {
            $str .= "g.`business_type`='".$where['business_type']."' AND ";
        }
        if(!empty($where['order_sn']))
        {
            $str .= "g.`order_sn`='".$where['order_sn']."' AND ";
        }
        //无帐货号
        if(!empty($where['no_account_gid']))
        {
            $str .= "g.`no_account_gid`='".$where['no_account_gid']."' AND ";
        }
        //货品所在公司
        if(!empty($where['place_company_id'])){
            $str .="g.place_company_id={$where['place_company_id']} AND ";
        }
        //货品所在仓库
        if(!empty($where['place_warehouse_id'])){
            $str .="g.place_warehouse_id={$where['place_warehouse_id']} AND ";
        }
        //有账货号
        if(!empty($where['exist_account_gid'])){
            $str .="g.exist_account_gid='{$where['exist_account_gid']}' AND ";
        }
        //客户姓名
        if(!empty($where['guest_name'])){
            $str .="g.guest_name like '%".addslashes($where['guest_name'])."%' AND ";
        }
        if(!empty($where['apply_user']))
        {
            $str .= "g.`apply_user` like '".addslashes($where['apply_user'])."%' AND ";
        }
        if(!empty($where['create_user']))
        {
            $str .= "b.`create_user` like '".addslashes($where['create_user'])."%' AND ";
        }
        if(!empty($where['check_user']))
        {
            $str .= "b.`check_user` like '".addslashes($where['check_user'])."%' AND ";
        }
        if (!empty($where['time_start'])) {
            $str .= "b.`create_time` >= '{$where['time_start']} 00:00:00' AND ";
        }
        if (!empty($where['time_end'])) {
            $str .= "b.`create_time` <= '{$where['time_end']} 23:59:59' AND ";
        }
        //货品所在仓库
        if(!empty($where['from_company_id'])){
            $str .="b.from_company_id='{$where['from_company_id']}' AND ";
        }

        if($str)
        {
            $str = rtrim($str,"AND ");//这个空格很重要
            $sql .=" WHERE ".$str;
        }
        $sql .= " ORDER BY b.`id` DESC";

        return $sql;
    }
    //拼接sql
    public function getSqlForGoods($where = array())
    {
        //不要用*,修改为具体字段
        $sql = "SELECT b.bill_type,b.remark,g.* FROM `".$this->table()."` `b` left join `virtual_return_goods` `g` on b.g_id = g.id";
        $str = '';
        if(!empty($where['bill_id']))
        {
            $str .= "b.`id`=".$where['bill_id']." AND ";
        }
        if(!empty($where['bill_type']))
        {
            $str .= "b.`bill_type`='".$where['bill_type']."' AND ";
        }
        if(!empty($where['bill_status']))
        {
            $str .= "b.`bill_status`=".$where['bill_status']." AND ";
        }
        if(!empty($where['business_type']))
        {
            $str .= "g.`business_type`='".$where['business_type']."' AND ";
        }
        if(!empty($where['order_sn']))
        {
            $str .= "g.`order_sn`='".$where['order_sn']."' AND ";
        }
        //无帐货号
        if(!empty($where['no_account_gid']))
        {
            $str .= "g.`no_account_gid`='".$where['no_account_gid']."' AND ";
        }
        //货品所在公司
        if(!empty($where['place_company_id'])){
            $str .="g.place_company_id={$where['place_company_id']} AND ";
        }
        //货品所在仓库
        if(!empty($where['place_warehouse_id'])){
            $str .="g.place_warehouse_id={$where['place_warehouse_id']} AND ";
        }
        //有账货号
        if(!empty($where['exist_account_gid'])){
            $str .="g.exist_account_gid='{$where['exist_account_gid']}' AND ";
        }
        //客户姓名
        if(!empty($where['guest_name'])){
            $str .="g.guest_name like '%".addslashes($where['guest_name'])."%' AND ";
        }
        if(!empty($where['apply_user']))
        {
            $str .= "g.`apply_user` like '".addslashes($where['apply_user'])."%' AND ";
        }
        if (!empty($where['time_start'])) {
            $str .= "b.`create_time` >= '{$where['time_start']} 00:00:00' AND ";
        }
        if (!empty($where['time_end'])) {
            $str .= "b.`create_time` <= '{$where['time_end']} 23:59:59' AND ";
        }
        if($str)
        {
            $str = rtrim($str,"AND ");//这个空格很重要
            $sql .=" WHERE ".$str;
        }
        $sql .= " ORDER BY b.`id` DESC";
    
        return $sql;
    }
	/**
	 *	pageList，分页列表
	 *
	 *	@url VirtualReturnBillController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{      
        $sql = $this->getSql($where);
        //echo $sql;die;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

    public function getbillgoods($id='')
    {
        $sql = "select virtual_id from virtual_bill_goods where bill_id = {$id}";
        return $this->db()->getAll($sql);
    }
    public function getBillGoodsList($where){        
        $sql = "select * from virtual_return_goods where 1";
        if(!empty($where['id'])){
            $sql .= " AND id={$where['id']}";
        }
        return $this->db()->getAll($sql);
    }
    //单据编辑
    public function updateVirtualBillInfo($data,$info)
    {
        $time = date('Y-m-d H:i:s');
        $user = $_SESSION['userName'];
        $ip = Util::getClicentIp();
        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            //编辑单据
            $sql = "UPDATE `warehouse_shipping`.`virtual_return_bill` SET `from_company_id`='{$info['from_company_id']}', `from_company_name`='{$info['from_company_name']}', `from_warehouse_id`='{$info['from_warehouse_id']}', `from_warehouse_name`='{$info['from_warehouse_name']}', `out_company_id`='{$info['out_company_id']}', `out_company_name`='{$info['out_company_name']}', `out_warehouse_id`='{$info['out_warehouse_id']}', `out_warehouse_name`='{$info['out_warehouse_name']}', `express_sn`='{$info['express_sn']}', `remark`='{$info['remark']}' WHERE `id`='".$info['id']."'";
            $pdo->query($sql);
            //编辑货品 `business_type`='{$data['business_type']}',
            $sql = "UPDATE `warehouse_shipping`.`virtual_return_goods` SET `order_sn`='{$data['order_sn']}', `style_sn`='{$data['style_sn']}',`caizhi`='{$data['caizhi']}', `ingredient_color`='{$data['ingredient_color']}', `gold_weight`='{$data['gold_weight']}', `torr_type`='{$data['torr_type']}', `product_line`='{$data['product_line']}', `style_type`='{$data['style_type']}', `finger_circle`='{$data['finger_circle']}', `credential_num`='{$data['credential_num']}', `main_stone_weight`='{$data['main_stone_weight']}', `main_stone_num`='{$data['main_stone_num']}', `deputy_stone_weight`='{$data['deputy_stone_weight']}', `deputy_stone_num`='{$data['deputy_stone_num']}', `resale_price`='{$data['resale_price']}', `out_goods_id`='{$data['out_goods_id']}', `place_company_id`='{$data['place_company_id']}', `place_company_name`='{$data['place_company_name']}', `place_warehouse_id`='{$data['place_warehouse_id']}', `place_warehouse_name`='{$data['place_warehouse_name']}', `guest_name`='{$data['guest_name']}', `guest_contact`='{$data['guest_contact']}', `return_remark`='{$data['return_remark']}', `exist_account_gid`='{$data['exist_account_gid']}',weixiu_fee={$data['weixiu_fee']} WHERE `id`='".$data['id']."'";
            $pdo->query($sql);
        }catch(Exception $e){//捕获异常
           // var_dump($e);
               echo $sql;
        $pdo->rollback();//事务回滚
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return false;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return $id;
    }

     //单据编辑
    public function updateVirtualBillInfoPl($data,$info)
    {//var_dump($data,$info);die;
        $time = date('Y-m-d H:i:s');
        $user = $_SESSION['userName'];
        $ip = Util::getClicentIp();
        $pdo = $this->db()->db();//pdo对象
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            //编辑单据
            $sql = "UPDATE `warehouse_shipping`.`virtual_return_bill` SET `from_company_id`='{$info['from_company_id']}', `from_company_name`='{$info['from_company_name']}', `from_warehouse_id`='{$info['from_warehouse_id']}', `from_warehouse_name`='{$info['from_warehouse_name']}', `out_company_id`='{$info['out_company_id']}', `out_company_name`='{$info['out_company_name']}', `out_warehouse_id`='{$info['out_warehouse_id']}', `out_warehouse_name`='{$info['out_warehouse_name']}', `express_sn`='{$info['express_sn']}', `remark`='{$info['remark']}' WHERE `id`='".$info['id']."'";
            $pdo->query($sql);
            $sql = "delete from virtual_bill_goods where bill_id = {$info['id']}";
            $pdo->query($sql);
            //单据明细
            foreach ($data as $key => $value) {
                $goods_id = !empty($value['goods_id'])?$value['goods_id']:0;
                $sql = "INSERT INTO `warehouse_shipping`.`virtual_bill_goods` (`id`, `bill_id`, `virtual_id`, `business_type`, `order_sn`, `goods_id`, `style_sn`, `caizhi`, `ingredient_color`, `gold_weight`, `torr_type`, `product_line`, `style_type`, `finger_circle`, `credential_num`, `main_stone_weight`, `main_stone_num`, `deputy_stone_weight`, `deputy_stone_num`, `resale_price`) VALUES (null, '".$info['id']."','".$value['id']."', '".$value['business_type']."', '".$value['order_sn']."', '".$goods_id."', '".$value['style_sn']."', '".$value['caizhi']."', '".$value['ingredient_color']."', '".$value['gold_weight']."', '".$value['torr_type']."', '".$value['product_line']."', '".$value['style_type']."', '".$value['finger_circle']."', '".$value['credential_num']."', '".$value['main_stone_weight']."', '".$value['main_stone_num']."', '".$value['deputy_stone_weight']."', '".$value['deputy_stone_num']."', '".$value['resale_price']."')";
                $pdo->query($sql);
            }
            //编辑货品
            //$sql = "UPDATE `warehouse_shipping`.`virtual_return_goods` SET `business_type`='{$data['business_type']}', `order_sn`='{$data['order_sn']}', `style_sn`='{$data['style_sn']}', `ingredient_color`='{$data['ingredient_color']}', `gold_weight`='{$data['gold_weight']}', `torr_type`='{$data['torr_type']}', `product_line`='{$data['product_line']}', `style_type`='{$data['style_type']}', `finger_circle`='{$data['finger_circle']}', `credential_num`='{$data['credential_num']}', `main_stone_weight`='{$data['main_stone_weight']}', `main_stone_num`='{$data['main_stone_num']}', `deputy_stone_weight`='{$data['deputy_stone_weight']}', `deputy_stone_num`='{$data['deputy_stone_num']}', `resale_price`='{$data['resale_price']}', `out_goods_id`='{$data['out_goods_id']}', `place_company_id`='{$data['place_company_id']}', `place_company_name`='{$data['place_company_name']}', `place_warehouse_id`='{$data['place_warehouse_id']}', `place_warehouse_name`='{$data['place_warehouse_name']}', `guest_name`='{$data['guest_name']}', `guest_contact`='{$data['guest_contact']}', `return_remark`='{$data['return_remark']}', `exist_account_gid`='{$data['exist_account_gid']}' WHERE `id`='".$data['id']."'";
            //$pdo->query($sql);
            
        }catch(Exception $e){//捕获异常
           // var_dump($e);
               echo $sql;
        $pdo->rollback();//事务回滚
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return false;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return $id;
    }
}

?>