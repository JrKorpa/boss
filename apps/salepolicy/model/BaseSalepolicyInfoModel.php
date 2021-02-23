<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseSalepolicyInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-11 10:54:58
 *   @update	:
 *  -------------------------------------------------
 */
class BaseSalepolicyInfoModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'base_salepolicy_info';
		$this->_prefix ='policy';
        $this->_dataObject = array("policy_id"=>"自增ID",
"policy_name"=>"销售策略名称",
"policy_start_time"=>"销售策略开始时间",
"policy_end_time"=>"销售策略结束时间",
"create_time"=>"记录创建时间",
"create_user"=>"记录创建人",
"create_remark"=>"记录创建备注",
"check_user"=>"审核人",
"check_time"=>"审核时间",
"zuofei_time"=>"作废时间",
"check_remark"=>"记录备注",
"bsi_status"=>"记录状态 0保存,1申请审核,2已审核,3作废",
"is_together"=>"策略类型：1，普通；2，打包",
"sta_value"=>"固定值",
"jiajia"=>"加价率",
"is_delete"=>"记录是否有效 0有效1效",
"is_favourable"=>"销售政策是否可以申请优惠1：可申请 2:不可以",
"is_default"=>"销售政策是否为默认销售政策1：是 2:不是",
"is_kuanprice"=>"是否按款定价",
"kuanprice"=>"按款定价",
"xilie"=>"系列及归属",
"cert"=>"裸钻证书类型",
        );
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url BaseSalepolicyInfoController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
        if(isset($where['policy_name'])&&!empty($where['policy_name'])){
            $sql.=" AND `policy_name` like '".addslashes($where['policy_name'])."%'";
        }
        if(isset($where['policy_status'])&&!empty($where['policy_status'])){
            $sql.=" AND `bsi_status` = ".$where['policy_status'];
        }
        if(isset($where['is_delete'])&&$where['is_delete']!=''){
            $sql.=" AND `is_delete` = ".$where['is_delete'];
        }
	//产品线
	if(isset($where['product_type'])&&$where['product_type']!=''){
            $sql.=" AND `product_type` = '".addslashes($where['product_type'])."'";
        }
	//金托类型
	if(isset($where['tuo_type'])&&$where['tuo_type']!=''){
            $sql.=" AND `tuo_type` = ".$where['tuo_type'];
        }
	//货品类型
	if(isset($where['huopin_type'])&&$where['huopin_type']!=''){
            $sql.=" AND `huopin_type` = ".$where['huopin_type'];
        }
	//款式分类
	if(isset($where['cat_type'])&&$where['cat_type']!=''){
            $sql.=" AND `cat_type` = '".addslashes($where['cat_type'])."'";
        }
	//镶口范围
	if(isset($where['range_begin'])&&$where['range_begin']!=''){
            $sql.=" AND `range_begin` = '".addslashes($where['range_begin'])."'";
        }
	//镶口范围
	if(isset($where['range_end'])&&$where['range_end']!=''){
            $sql.=" AND `range_end` = '".addslashes($where['range_end'])."'";
        }
        //主石范围
	if(isset($where['zhushi_begin'])&&$where['zhushi_begin']!=''){
            $sql.=" AND `zhushi_begin` = '".addslashes($where['zhushi_begin'])."'";
        }
	//主石范围
	if(isset($where['zhushi_end'])&&$where['zhushi_end']!=''){
            $sql.=" AND `zhushi_end` = '".addslashes($where['zhushi_end'])."'";
        }

        if(isset($where['time_start'])&&!empty($where['time_start'])){
            $sql.=" AND `policy_start_time` >= '".$where['time_start']."'";
        }
        if(isset($where['time_end'])&&!empty($where['time_end'])){
            $sql.=" AND `policy_end_time` <= '".$where['time_end']."'";
        }
        if(isset($where['is_kuanprice'])&&$where['is_kuanprice']!=''){
            $sql.=" AND `is_kuanprice` = '".addslashes($where['is_kuanprice'])."'";
        }
		$sql .= " ORDER BY `policy_id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

    //！！！！！！！！！！！0竟然是有效
    function getPolicyList($policy_id=0,$is_together=1) {
        $newtime  = date('Y-m-d');
        $sql = "SELECT `policy_id`,`policy_name` FROM `".$this->table()."` WHERE  policy_start_time<='$newtime' AND policy_end_time>='$newtime' and is_delete=0";
        if($policy_id > 0){
            $sql .= " AND `policy_id` = {$policy_id}";
        }
        if($is_together > 0){
            $sql .= " AND `is_together` = {$is_together}";
        }
        $policy_list = $this->db()->getAll($sql);
        return $policy_list;
    }

    //单据审核时要判断的app_salepolicy_goods base_salepolicy_info是否存在可用的关联信息
    public function relData($salepolicy_id){
        $sql = "SELECT COUNT(*) FROM `app_salepolicy_goods` WHERE `is_delete`<>2 and `policy_id`=".$salepolicy_id;
        $goods_num = $this->db()->getOne($sql);
        if($goods_num==0){
            return 3;
        }
        $sql = "SELECT COUNT(*) FROM `app_salepolicy_channel` WHERE `is_delete`<>2 and `policy_id`=".$salepolicy_id;
        $goods_num = $this->db()->getOne($sql);

        if($goods_num==0){
            return 2;
        }
        return 1;

    }
    
    /**
     * 验证打包策略下是否商品
     * @param type $policy_id
     * @return type
     */
    public function validateGoods($policy_id) {
        $sql = "SELECT count(`atg`.`goods_id`) FROM `app_together_policey_related` as `atp`,`app_together_goods_related` as `atg`,`app_salepolicy_together_goods` as `ast` "
                . "WHERE `atp`.`id`=`atg`.`together_id` AND `ast`.`id`=`atp`.`together_id` AND `atp`.`policy_id`=$policy_id";
        return $this->db()->getOne($sql);
    }

    //查询销售政策名称 验证是否重复政策
    function getPolicyName($policy_name) {

    	$sql = "SELECT `policy_id`,`policy_name` FROM `".$this->table()."` WHERE  1 ";
    	if(isset($policy_name)&&!empty($policy_name)){
    		$sql .= " AND `policy_name` = '{$policy_name}'";
    	}
    	$policy_name = $this->db()->getRow($sql);
    	return $policy_name;
    }
    function getPolicyidMax($policy_name) {

    	$sql = "SELECT max(`policy_id`)+1 FROM `".$this->table()."` WHERE  1 ";
    	return $this->db()->getOne($sql);
    }


    public function getPchannellist($p_id){
        if(empty($p_id)){
            return false;
        }
        $sql = "SELECT channel FROM app_salepolicy_channel WHERE is_delete=1 AND policy_id=".$p_id;
        return  $this->db()->getAll($sql);
    }
    public function getBillType(){
		$sql = "select `type_name`,`type_SN` from `warehouse_shipping`.`warehouse_bill_type` WHERE `is_enabled` = '1'";
		return $this->db()->getAll($sql);
	}
    public function getGoodsidBybillno($bill_type,$bill_no){
        $sql = "select `goods_id` from `warehouse_shipping`.`warehouse_bill_goods` WHERE bill_type='$bill_type' and bill_no='$bill_no'";
		return $this->db()->getAll($sql);
    }
	
	//add by liulinyan 2015-08-27
	//获取所有启用的产品线
	public function getallproductype()
	{
		$sql = "select product_type_id,product_type_name from front.app_product_type where product_type_status=1 order by display_order asc";
		return $this->db()->getAll($sql);
	}
    function getInfoByid($id)
    {
        $sql="SELECT * FROM `".$this->table()."`  WHERE policy_id='$id'";
        $res=$this->db()->getRow($sql);
        return $res;
    }
     
    public  function checkKuanpriceExists(){
        $sql = "select policy_name from ".$this->table()." where is_kuanprice=1 limit 1";
        $res=$this->db()->getOne($sql);
        return $res;
    }
}

?>