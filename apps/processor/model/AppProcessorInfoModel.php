<?php

/**
 *  -------------------------------------------------
 *   @file		: AppProcessorInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 17:43:19
 *   @update	:
 *  -------------------------------------------------
 */
class AppProcessorInfoModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_processor_info';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => " ",
            "code" => "供应商编码",
            "name" => "供应商名称",
            "business_scope" => "经营范围：1:黄金,2:K金素金,3:PT素金,4:K金钻石镶嵌品,5:PT钻石镶嵌品,6:成品钻,7:彩宝饰品,8:银饰品,9:其他",
            "is_open" => "是否开通系统:1是0否",
            "password" => "密码",
            "business_license" => "营业执照号码",
            "tax_registry_no" => "税务登记证号",
            "business_license_region" => "营业执照地址:省，市，区",
            "business_license_address" => "营业执照地址",
            "pro_region" => "取货地址:省，市，区",
            "pro_address" => "取货地址",
            "cycle" => "出货周期",
            "pay_type" => "结算方式：1现金,2转账,3支票",
            "tax_invoice" => "增值税发票",
            "tax_point" => "税点",
            "balance_type" => "付款周期：1日结；2月结；3货到付款",
            "purchase_amount" => "采购额度",
            "pro_contact" => "公司联系人",
            "pro_phone" => "公司联系电话",
            "pro_qq" => "公司联系qq",
            "contact" => "BDD紧急联系人",
            "kela_phone" => "BDD紧急联系电话",
            "kela_qq" => "BDD紧急联系qq",
            "bank_name" => "开户银行",
            "account_name" => "户名",
            "account" => "银行账户",
            "is_invoice" => "此供应商是否有能力开发票:1开，0不开",
            "pro_email" => "供货商邮箱",
//            "balance_day" => "结算日期",
            "status" => "状态：1启用2停用",
            "create_id" => "创建人ID",
            "create_time" => "创建时间",
            "create_user" => "创建人",
            "pact_doc" => "合同附件",
            "license_jpg" => "营业执照附件",
            "tax_jpg" => "税务登记证附件",
            "info" => "备注");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url ControlController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        if (isset($where['opra_uname']) && $where['opra_uname'] != "") {
            $sql = "SELECT `app_processor_info`.*,`product_factory_oprauser`.`opra_uname` FROM `" . $this->table() . "`, `product_factory_oprauser` WHERE `app_processor_info`.`id` = `product_factory_oprauser`.`prc_id` and app_processor_info.is_A_company = 'N'";
           // $sql.= " and `product_factory_oprauser`.`opra_uname` like '%{$where['opra_uname']}%' group by `app_processor_info`.id";
        }else {
            $sql = "SELECT * FROM `" . $this->table() . "` WHERE 1 and app_processor_info.is_A_company = 'N'";
        }
        if(isset($where['name']) && $where['name'] != ''){
            $sql .= " and name like '%{$where['name']}%'";
        }
        if(isset($where['id']) && $where['id'] != ''){
            $sql .= " and `app_processor_info`.`id` = {$where['id']}";
        }
        if(isset($where['status']) && $where['status'] != ''){
            $sql .= " and status = {$where['status']}";
        }
        if(isset($where['pro_contact']) && $where['pro_contact'] != ''){
            $sql .= " and `pro_contact` LIKE '%{$where['pro_contact']}%'";
        }
        if(isset($where['business_scope']) && $where['business_scope'] != ''){
            $sql .= " and business_scope LIKE '%{$where['business_scope']}%'";
        }
        if(isset($where['start_time']) && isset($where['end_time']) && $where['start_time'] != '' && $where['end_time'] != ''){
            $sql .= " and create_time >= '{$where['start_time']}' and create_time <= '{$where['end_time']}'";
        }

       if (isset($where['opra_uname']) && $where['opra_uname'] != "") {
            $sql.= " and `product_factory_oprauser`.`opra_uname` like '%{$where['opra_uname']}%' group by `app_processor_info`.id";
        }
		else
		{
        $sql .= " ORDER BY `app_processor_info`.id DESC";
		}
         //echo $sql;
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    function getStatusList($param='') {
        $data = array(
            '1'=>'启用',
            '2'=>'停用'
        );
        if($param){
            return $data[$param];
        }
        return $data;
    }

	//获取开启的供应商列表--ZLJ
	function getProList()
	{
		$sql = "SELECT a.`id`,a.`code`,a.`name`,b.opra_uname as gendan,b.production_manager_id,b.production_manager_name FROM ".$this->table()." a left join product_factory_oprauser b on a.id=b.prc_id WHERE a.`status` = 1";
		return $this->db()->getAll($sql);
	}

    /**
     * getList 获取通过供应商
     */
    public function getList ()
    {
        $sql = "SELECT `id`,`name`,`code` FROM `".$this->table()."` WHERE `status`='1' ORDER BY id DESC";
        return $this->db()->getAll($sql);
    }

    /**
     * 获取供应商名称
     * @param type $id
     */
    public function getProcessorName($id) {
        if($id<1){
            return '';
        }
        $sql = "SELECT `name` FROM `".$this->table()."` WHERE `id`=$id";
        return $this->db()->getOne($sql);
    }

    /**
     * 查询是否已有分组
     */
    public function hasGroup($supplier_id){
        $sql = 'SELECT `group_id` FROM `app_processor_group` WHERE `supplier_id` ='.$supplier_id;
        return $this->db()->getOne($sql);
    }

    /**
     * 生成分组ID
     */
    public function mkGroupId(){
        $sql = "SELECT IFNULL(max(`group_id`)+1,1) AS `group_id`  FROM `app_processor_group`";
        return $this->db()->getOne($sql);

    }

    /**
     * 加入关联供应商[已去重]
     */
    public function intoGroup($supplier_id,$group_id){
        $sql = 'SELECT `id` FROM `app_processor_group` WHERE `supplier_id` ='.$supplier_id.' AND `group_id` = '.$group_id;
        $res = $this->db()->getOne($sql);
        if($res == false ){
            $sql = 'INSERT INTO `app_processor_group` (`supplier_id`,`group_id`) VALUES ('.$supplier_id.','.$group_id.')';
            $res = $this->db()->query($sql);
        }
        return $res;
    }

    /**
     * 删除关联供应商
     */
    public function delGroup($supplier_id,$group_id){
        $sql = 'DELETE FROM `app_processor_group` WHERE `supplier_id` ='.$supplier_id.' AND `group_id` = '.$group_id;
        $res = $this->db()->query($sql);
        return $res;
    }

    /**
     * 获取关联供应商ID
     */
    public function getGroup($group_id){
        $sql = 'select 473 as supplier_id union SELECT `supplier_id` FROM `app_processor_group` WHERE `group_id` ='.$group_id ;
        return DB::cn(14)->getAll($sql);
    }
	
	
	//获取开启的关联供应商列表
	public function getGroupProList($supplier_id)
	{
		$sql = "SELECT `id`, `name` FROM `app_processor_info` WHERE status = 1 AND  `id` in (select 473 as supplier_id union SELECT `supplier_id` FROM `app_processor_group` WHERE `group_id` = (SELECT `group_id` FROM `app_processor_group` WHERE `supplier_id` = $supplier_id))";
		$datas = $this->db()->getAll($sql);
		if(!empty($datas)){
			foreach($datas as $data){
				$suppliers[$data['id']] = $data['name'];
			}
			return $suppliers;
		}else{
			return false;
		}
	}


    public function getAllWarehouse(){
    	/*
        $w_model = new ApiWarehouseModel();
        $ret = $w_model::getAllWarehouse();
        return ($ret)?$ret:false;
        */
    	
    	$inst = localCache::getInstance();
    	$key = '__all_warehouse__';
    	$data = $inst->get($key);
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$w_model = new ApiWarehouseModel();
    	$data = $w_model::getAllWarehouse();
    	if ($data) {
    		$inst->set($key, $data);
    	}
    	
    	return $data ? $data : false;
    }

    public function getGoodsAttrs($attr){
    	/*
        $w_model = new ApiWarehouseModel();
        $ret = $w_model::getGoodsAttrs($attr);
        return ($ret)?array_filter($ret):false;
        */
    	$inst = localCache::getInstance();
    	$key = '__goods_attrs__';
    	$data = $inst->get($key);
    	if (!empty($data)) {
    		return $data;
    	}
    	
    	$w_model = new ApiWarehouseModel();
    	$ret = $w_model::getGoodsAttrs($attr);
    	$data = $ret ? array_filter($ret) : false;
    	if ($data) {
    		$inst->set($key, $data);
    	}
    	
    	return $data;
    }

    public function checkSupplierName($name){
        $sql = "SELECT count(*) FROM ".$this->table()." WHERE `name` ='".$name."' AND `status` = 1";
        $res = $this->db()->getOne($sql);
        return $res;
    }

    public function checkSupplierCode($code){
        $sql = "SELECT count(*) FROM ".$this->table()." WHERE `name` ='".$code."' AND `status` = 1";
        $res = $this->db()->getOne($sql);
        return $res;
    }

    /**
     * 现货取消布产,绑定商品,并下架
     */
    public function cannelBC($param){
        //根据布产ID，获取订单明细的主键ID
        $sql = "SELECT * FROM `product_goods_rel` WHERE `bc_id` = {$param['bc_id']}";
        $row = $this->db()->getRow($sql);

        $arr = ['bc_id','goods_id','order_gs_id'];
        foreach ($arr as $v) {
            if(!array_key_exists($v,$param)){
                return false;
            }
        }

        //绑定商品
        $w_model = new ApiWarehouseModel();
        $goods = $param['goods_id'];
        $order_gs_id = $param['order_gs_id'];
        $res = $w_model->BindGoodsInfoByGoodsId($goods,$order_gs_id,1);
        if(!$res){return false;}
        //商品下架
        $s_model = new ApiSalepolicyModel();
        $res = $s_model->EditIsSaleStatus(array($goods),'0','2');//销售下架
        if(!$res){
            //取消绑定商品
            $w_model->BindGoodsInfoByGoodsId($goods,$order_gs_id,2);
            return false;
        }
        //修改布产单的布产状态
        $sql = "UPDATE `product_info` SET `status` = '11' WHERE `id` = '".$param['bc_id']."'";
        $res = $this->db()->query($sql);

        //修改订单的明细的 布产状态
        //判断是布产单是否有关联货品 若关联 更新布产操作状态到 货品详情表 BY CAOCAO
        $rec = $this->judgeBcGoodsRel($param['bc_id']);
        if(!empty($rec)){
            $keys =array('update_data');
            $vals =array(array(array('id'=>$param['order_gs_id'] , 'buchan_status'=>11)));
            $ret = ApiModel::sales_api($keys, $vals, 'UpdateOrderDetailStatus');
        }

        //推送到订单 的日志
        $order_detail_arr = ApiModel::sales_api($keys = array('order_sn') , $vals = array($param['order_sn']) , 'GetGoodsInfobyOrderSN');
        $order_detail_id = 0;
        foreach ($order_detail_arr['return_msg'] as $key => $value) {
                if($value['id'] == $param['order_gs_id']){
                      $order_detail_id = $value['goods_id'];
                }
        }
        $ProductInfomodel = new ProductInfoModel(13);
        $ProductInfomodel->Writeback($param['bc_id'], $remark = "货号：{$order_detail_id} 不需要布产");

	//添加 不需布产日志
	$logModel = new ProductOpraLogModel(14);
	//$logModel->addLog($param['bc_id'],11,"布产单BC".$param['bc_id']."不需布产；绑定现货".$goods);
	$logModel->addLog($param['bc_id'],"布产单不需布产；绑定现货".$goods);
        return $res;

    }

    ////根据布产ID 返回布产单和订单的关联关系以及布产单状态
    public function judgeBcGoodsRel($id)
    {
        $sql = "SELECT  pg.`bc_id`, pg.`goods_id`,p.`status` FROM `product_goods_rel` as pg left join `product_info` as p  on pg.`bc_id`=p.`id` WHERE p.`id` = {$id}";
        return $this->db()->getRow($sql);
    }
    
    
   

}

?>