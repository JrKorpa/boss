<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseMemberInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 19:31:15
 *   @update	:
 *  -------------------------------------------------
 */
class BaseMemberInfoModel extends Model
{
    public $_prefix = 'member';
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'base_member_info';
        $this->_dataObject = array("member_id"=>"会员id",
			"country_id"=>"会员所在国家",
			"province_id"=>"会员所在省份",
			"city_id"=>"会员所在城市",
			"region_id"=>"会员所在区域",
			"source_id"=>"会员来源",
			"member_name"=>"会员名称",
			"department_id"=>"部门id",
			"customer_source_id"=>"客户来源",
			"mem_card_sn"=>"会员卡号",
			"member_phone"=>"会员电话",
			"member_age"=>"会员年龄",
			"member_qq"=>"会员QQ",
			"member_email"=>"会员EMAIL",
			"member_aliww"=>"会员旺旺",
			"member_dudget"=>"会员预算",
			"member_maristatus"=>"会员婚姻状况",
			"member_address"=>"会员地址",
			"member_peference"=>"会员喜好",
			"member_type"=>"会员类型：0=无效会员,1=潜在会员,2=意向会员,3=订单会员;默认1",
            "member_truename"=>"真实姓名",
            "member_tel"=>"固定电话",
            "member_msn"=>"会员msn",
            "member_sex"=>"会员性别",
            "member_birthday"=>"会员生日",
            "member_wedding"=>"会员结婚时间",
            "member_question"=>"会员密码提示问题",
            "member_answer"=>"会员密码提示答案",
            "reg_time"=>"会员注册时间",
            "last_login"=>"会员上次登录时间",
            "last_ip"=>"会员上次登录的ip",
            "visit_count"=>"会员登录次数",
            "head_img"=>"会员头像路径",
            "make_order"=>"登记人",
            "email_valid"=>"邮箱是否验证",
            "complete_info"=>"会员信息是否完整",
            "member_password"=>"会员登录密码"
        );
		parent::__construct($id,$strConn);
	}
	/**
	 *	pageList，分页列表
	 *
	 *	@url MessageController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
        if(isset($where['member_name'])&&!empty($where['member_name'])){
            $sql.=" AND `member_name` like '".addslashes($where['member_name'])."%'";
        }
        if(!empty($where['member_phone'])){
            $sql.= " AND `member_phone` LIKE '".addslashes($where['member_phone'])."'";
        }
        if(!empty($where['department_id'])){
            $sql.= " AND `department_id`=".$where['department_id']."";
        }

        if(!empty($where['department_id_in'])){
            $sql.= " AND `department_id` in (".$where['department_id_in'].")";
        }

        if(!empty($where['member_type'])){
            $sql.= " AND `member_type`='".$where['member_type']."'";
        }
		$sql .= " ORDER BY `member_id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

    public function getMemByMobile ($mobile)
    {
        $sql="SELECT * FROM `".$this->table()."` WHERE member_phone='{$mobile}' ";
		$data = $this->db()->getRow($sql);
		return $data;
    }

	public function hasMemberPhone ($mobile)
	{
        $sql="SELECT count(1) FROM `".$this->table()."` WHERE member_phone='{$mobile}' ";
		if($this->pk())
		{
			$sql .=" AND member_id<>".$this->pk();
		}
		return $this->db()->getOne($sql);
	}
    // 更改订单用户
    public function setOrderMember($order_id, $data) {
        $str_field = 'user_id='.$data['user_id'];
        if(isset($data['consignee'])) {
            $str_field .= ",consignee='{$data['consignee']}'";
        }
        if(isset($data['mobile'])) {
            $str_field .= ',mobile='.$data['mobile'];
        }
        $sql = "update app_order.base_order_info set $str_field where id=".$order_id;
        return $this->db()->query($sql);
    }
}
?>