<?php
/**
 *  -------------------------------------------------
 *   @file		: DealerCustomerManageModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-12-15 11:51:21
 *   @update	:
 *  -------------------------------------------------
 */
class DealerCustomerManageModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'dealer_customer_manage';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID号",
"customer_name"=>"准客户姓名",
"status"=>"状态(文本)",
"source"=>"来源类型",
"source_channel"=>"来源渠道",
"tel"=>"联系电话",
"email"=>"联系邮箱",
"province"=>"省",
"city"=>"市",
"district"=>"县区",
"shop_nums"=>"意向开店数",
"investment_amount"=>"投资金额",
"info"=>"其他信息",
"follow_upper_id"=>"跟进员ID,逗号隔开",
"created_time"=>"创建时间",
"modified_time"=>"修改时间",
"text_item"=>"项目");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url DealerCustomerManageController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$user_model = new RoleUserModel(1);
		$operator = array_column($user_model->getRoleUserList(32),'uid');
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
		if($where['customer_name'] != "")
		{
			$str .= "`customer_name` like \"%".addslashes($where['customer_name'])."%\" AND ";
		}
		if($where['tel'] != "")
		{
			$str .= "`tel` like \"%".addslashes($where['tel'])."%\" AND ";
		}
		if($where['email'] != "")
		{
			$str .= "`email` like \"%".addslashes($where['email'])."%\" AND ";
		}
        if($where['district'] != "")
        {
            $str .= "`district` like \"%".addslashes($where['district'])."%\" AND ";
        }
        if($where['province'] != "")
        {
            $str .= "`province` like \"%".addslashes($where['province'])."%\" AND ";
        }
		if($where['city'] != "")
		{
			$str .= "`city` like \"%".addslashes($where['city'])."%\" AND ";
		}
		if($where['follower'] != "")
		{
			$str .= "FIND_IN_SET((SELECT id FROM `cuteframe`.`user` WHERE `account` = '".addslashes($where['follower'])."'),follow_upper_id) AND ";
		}
        if(isset($where['source_channel']) && !empty($where['source_channel'])){
            if(count($where['source_channel'])==1){
                 $str.= " `source_channel` like '%,".$where['source_channel'][0].",%' AND ";
            }else{
                $str_p = "";
                foreach ($where['source_channel'] as $val){
                     $str_p.=" `source_channel` like '%,".$val.",%' or ";
                }
                $str_p = rtrim($str_p," or");
                $str.= " (".$str_p.") AND ";
               
            }
        }
		if($where['status'] != "")
		{
			$str .= "`status` like \"%".addslashes($where['status'])."%\" AND ";
		}
        if(isset($where['source']) && !empty($where['source'])){
            if(count($where['source'])==1){
                 $str.= " `source` like '%,".$where['source'][0].",%' AND ";
            }else{
                $str_c = "";
                foreach ($where['source'] as $val){
                     $str_c.=" `source` like '%,".$val.",%' or ";
                }
                $str_c = rtrim($str_c," or");
                $str.= " (".$str_c.") AND ";
               
            }
        }
        if(isset($where['spread_id']) && !empty($where['spread_id'])){
            if(count($where['spread_id'])==1){
                 $str.= " `spread_id` like '%,".$where['spread_id'][0].",%' AND ";
            }else{
                $str_t = "";
                foreach ($where['spread_id'] as $val){
                     $str_t.=" `spread_id` like '%,".$val.",%' or ";
                }
                $str_t = rtrim($str_t," or");
                $str.= " (".$str_t.") AND ";
               
            }
        }
        if(isset($where['text_item']) && !empty($where['text_item'])){
            if(count($where['text_item'])==1){
                 $str.= " `text_item` like '%,".$where['text_item'][0].",%' AND ";
            }else{
                $str_t = "";
                foreach ($where['text_item'] as $val){
                     $str_t.=" `text_item` like '%,".$val.",%' or ";
                }
                $str_t = rtrim($str_t," or");
                $str.= " (".$str_t.") AND ";
               
            }
        }
		if(!empty($where['start_time'])) {
            $str.="`created_time` >= '".$where['start_time']." 00:00:00' AND ";
        }
        if(!empty($where['end_time'])) {
            $str.="`created_time` <= '".$where['end_time']." 23:59:59' AND ";
        }
		/*if(!empty($where['xx']))
		{
			$str .= "`xx`='".$where['xx']."' AND ";
		}*/
		if($_SESSION['userId'] != 1 && !in_array($_SESSION['userId'],$operator)){
			$str.="(FIND_IN_SET(".$_SESSION['userId'].",follow_upper_id) or FIND_IN_SET(".$_SESSION['userId'].",spread_id)) AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
        //echo $sql;die;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
	
	/**
	 *	saveFollow,添加跟进情况
	 *
	 */
	function saveFollow ($insertData)
	{
		if($insertData['did'] && $insertData['content']){
			$sql = " INSERT INTO front.dealer_customer_follow(`did`,`content`,`created_time`,`modified_time`,`follow_name`)VALUES(".$insertData['did'].",'".$insertData['content']."',NOW(),NOW(),'".$insertData['follow_name']."')";
			$res = $this->db()->query($sql);
		}	
		return $res?$res:false;
	}
	
	/**
	 *	selectFollow,查询跟进情况
	 *
	 */
	function selectFollow ($id,$if_one=null)
	{
		$where = $id ? " WHERE `did` = ".intval($id) : "";
		$field = $if_one ? "content,created_time,follow_name" : "*";
		$sql = " SELECT ".$field." FROM front.dealer_customer_follow ".$where." ORDER BY created_time DESC";
		if($if_one) $sql .= " LIMIT 1";
		return !$if_one ? $this->db()->getAll($sql) : $this->db()->getRow($sql);
	}
	
	/**
	 *	deleteFollow,删除跟进情况
	 *
	 */
	function deleteFollow ($did)
	{
		if($did){
			$sql = " DELETE FROM front.dealer_customer_follow WHERE did = ".$did;
			$this->db()->query($sql);
		}
	}
	
	/**
	 *	updateFollow,修改跟进情况
	 *
	 */
	function updateFollow ($content,$fid)
	{
		if($fid){
			$sql = " UPDATE front.dealer_customer_follow SET content = '".$content."',modified_time = NOW() WHERE id = ".$fid;
			$res = $this->db()->query($sql);
		}
		return $res?$res:false;
	}
	
	/**
	 *	checkTel,检查手机号有无重复
	 *
	 */
	function checkTel ($tel)
	{
		$sql = " SELECT COUNT(*) FROM ".$this->table()." WHERE tel ='".htmlspecialchars($tel)."'";
		return $this->db()->getOne($sql);
	}

	/**
     *  getHistorySource,取出历史来源类型
     *
     */
    function getHistorySource ()
    {
        $sql = " SELECT `source` FROM ".$this->table()." where `source` not like ',%,%,' group by `source`";
        return $this->db()->getAll($sql);
    }

    /**
     *  getHistorySourceChannel,取出历史来源类型
     *
     */
    function getHistorySourceChannel ()
    {
        $sql = " SELECT `source_channel` FROM ".$this->table()." where `source_channel` not like ',%,%,' group by `source_channel`";
        return $this->db()->getAll($sql);
    }

    /**
     *  getHistorySourceChannel,取出历史项目
     *
     */
    function getTextItem ()
    {
        $sql = " SELECT `text_item` FROM ".$this->table()." where `text_item` not like ',%,%,' group by `text_item`";
        return $this->db()->getAll($sql);
    }

    /**
     *  getHistorySourceChannel,取出历史项目
     *
     */
    function getTelAll ()
    {
        $sql = " SELECT `tel` FROM ".$this->table()."";
        return $this->db()->getAll($sql);
    }
	
}

?>