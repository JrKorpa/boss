<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderAddressModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-30 16:37:26
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderAddressModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_order_address';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"id",
"order_id"=>"订单id",
"consignee"=>"收货人",
"distribution_type"=>"配送方式",
"express_id"=>"快递公司",
"country_id"=>"国家id",
"province_id"=>"省份id",
"city_id"=>"城市id",
"regional_id"=>"区域id",
"address"=>"详细地址",
"tel"=>"电话",
"email"=>"email",
"zipcode"=>"邮编",
"goods_id"=>"商品id");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppOrderAddressController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
        if(!empty($where['order_id'])){
            $str .= " `order_id`=".$where['order_id']." AND ";
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

    public function getOrderAddressid($order_id,$info){
        $sql = "SELECT * FROM `app_order_address` WHERE `order_id`=".$order_id;
        $res = $this->db()->getOne($sql);
        if($res){
            $sql = "UPDATE `app_order_address` set `consignee`='$info[consignee]',`distribution_type`='$info[distribution_type]',`express_id`='$info[express_id]',`country_id`='$info[country_id]',`province_id`='$info[province_id]',`city_id`='$info[city_id]',`regional_id`='$info[regional_id]',`address`='$info[address]',`tel`='$info[tel]',`email`='$info[email]',`zipcode`='$info[zipcode]' WHERE `order_id`=".$order_id;

           return  $this->db()->query($sql);

        }else{
            $sql ="insert into app_order_address (order_id,consignee,distribution_type,express_id,country_id,province_id,city_id,regional_id,address,tel,email,zipcode) VALUES ($order_id,'$info[consignee]','$info[distribution_type]','$info[express_id]','$info[country_id]','$info[province_id]','$info[city_id]','$info[regional_id]','$info[address]','$info[tel]','$info[email]','$info[zipcode]')";
           return   $this->db()->query($sql);

        }
        
    }
    public function getOrderAddressinfo($order_id){
        $sql="SELECT * FROM ".$this->table()." WHERE `order_id`=".$order_id;
        $res = $this->db()->getRow($sql);
        return $res;
    }

    //
    public static function UpdateMemberDefAddress($mem_address_id){
       
        $ret = ApiModel::bespoke_api('UpdateMemberDefAddressStatus',array('mem_address_id'),array($mem_address_id));
       
        return $ret; 
    }
    
    //修改预约信息
    public static function updateBespokeDealStatus($bespoke_id){
       
        $ret = ApiModel::bespoke_api('updateBespokeDeal_Status',array('bespoke_id'),array($bespoke_id));
       
        return $ret; 
    }

    //向用户地址表推送用户的地址---修改 (front.app_order_address)推送:member_id(会员id),mobile(顾客手机号),mem_country_id（会员国家）,mem_province_id（会员省）,mem_city_id（会员城市）,mem_district_id（会员区域）,mem_address（会员详细地址）。
    public static function PutMemberAddressInfo($data){
        return ApiModel::bespoke_api('PutMemberAddressInfo',array('mem_address_id','mobile','mem_country_id','mem_province_id','mem_city_id','mem_district_id','mem_address','customer','mem_is_def'),array($data['member_id'],$data['mobile'],$data['mem_country_id'],$data['mem_province_id'],$data['mem_city_id'],$data['mem_district_id'],$data['mem_address'],$data['customer'],$data['mem_is_def']));
    }

    //向用户地址表发送用户id返回用户所有的信息
    public  static function GetMemberAddressInfo($member_id){
        return ApiModel::bespoke_api('GetMemberAddressInfo',array('member_id','mem_is_def'),array($member_id,'1'));
    }
    //向用户地址表发送地址id返回用户所有的信息
    public  static function GetMemberAddressInfos($mem_address_id){
        return ApiModel::bespoke_api('GetMemberAddressInfo',array('mem_address_id'),array($mem_address_id));
    }

    //向用户地址表推送用户的地址---添加 (front.app_order_address)推送:member_id(会员id),mobile(顾客手机号),mem_country_id（会员国家）,mem_province_id（会员省）,mem_city_id（会员城市）,mem_district_id（会员区域）,mem_address（会员详细地址）。
    public static function AddMemberAddressInfo($data){
        return ApiModel::bespoke_api('AddMemberAddressInfo',array('member_id','mobile','mem_country_id','mem_province_id','mem_city_id','mem_district_id','mem_address','customer','mem_is_def'),array($data['member_id'],$data['mobile'],$data['mem_country_id'],$data['mem_province_id'],$data['mem_city_id'],$data['mem_district_id'],$data['mem_address'],$data['customer'],$data['mem_is_def']));
    }

    function getAddressById($order_id)
    {
        $sql = "SELECT * FROM `".$this->table()."` WHERE order_id = {$order_id}";
        $res = $this->db()->getRow($sql);
        return $res;
    }

    public function deleteOrderAddress($order_id){
        $sql = "delete from ".$this->table()." where order_id=$order_id";
        $res = $this->db()->query($sql);
        return $res;
    }

    public function getAddressByOrderid($order_id){
        $sql = "SELECT * FROM `".$this->table()."` WHERE `order_id`=$order_id";
        $res = $this->db()->getRow($sql);
        return $res;
    }

    public function updateOrderAddressInfoBySn($where){
        if(!isset($where['order_id'])){
            return FALSE;
        }
        if(!isset($where['mobile'])){
            return FALSE;
        }
        $order_id = $where['order_id'];
        $mobile = $where['mobile'];
        $sql = "UPDATE `".$this->table()."` SET `tel` = ".$mobile." WHERE `order_id` in($order_id)";
        $res = $this->db()->query($sql);
        return $res;
    }
	public function updateOrderNameInfoBySn($where){
        if(!isset($where['order_id'])){
            return FALSE;
        }
        if(!isset($where['consignee'])){
            return FALSE;
        }
        $order_id = $where['order_id'];
        $consignee = $where['consignee'];
        $sql = "UPDATE `".$this->table()."` SET `consignee` = '".$consignee."' WHERE `order_id` in($order_id)";
        $res = $this->db()->query($sql);
        return $res;
    }
    
   public function getOrderArr($order_id){
   	$sql="SELECT referer,department_id FROM base_order_info WHERE id=$order_id";
   	return $this->db()->getRow($sql);
   }
}

?>