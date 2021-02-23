<?php
/**
 *  -------------------------------------------------
 *   @file		: GiftGoodsModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-12 18:37:15
 *   @update	:
 *  -------------------------------------------------
 */
class GiftGoodsModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'gift_goods';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"name"=>"名称",
"num"=>"数量",
"min_num"=>"最低数量",
"price"=>"均价",
"sell_sprice"=>"售价",
"status"=>"状态-1删除1正常",
"goods_number"=>"赠品货号",
"sell_type"=>"店面销售 1=开启  2=关闭",
"add_time"=>"添加时间",
"update_time"=>"更新时间");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url GiftGoodsController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT `id`,`name`,`num`,`min_num`,`price`,`sell_sprice`,`status`,`goods_number`,`sell_type`,`add_time`,`is_randring`,`update_time`,`sale_way`,`is_xz`,`is_zp` FROM `".$this->table()."`";
		$str = '';
		if($where['name'] != "")
		{
			$str .= "`name` like \"%".addslashes($where['name'])."%\" AND ";
		}
		if(!empty($where['goods_number']))
		{
			$str .= "`goods_number`='".$where['goods_number']."' AND ";
		}
        if(!empty($where['sell_type']))
        {
            $str .= "`sell_type`='".$where['sell_type']."' AND ";
        }
        if(!empty($where['status']))
        {
            $str .= "`status`='".$where['status']."' AND ";
        }
        if(is_numeric($where['is_check']) )
        {
            $str .= "`is_zp`='".$where['is_check']."' AND ";
        }
        if(!empty($where['is_xz']))
        {
            $str .= "`is_xz`='".$where['is_xz']."' AND ";
        }
         if(!empty($where['sale_way1'])&& !empty($where['sale_way2']) )
        {
            $str .= "`sale_way`='12' AND ";
        }
        else if(!empty($where['sale_way1']) )
        {
            $str .= "`sale_way`='".$where['sale_way1']."' AND ";
        }
        else if(!empty($where['sale_way2']) )
        {
            $str .= "`sale_way`='".$where['sale_way2']."' AND ";
        }
        
        
        if(!empty($where['start_time']))
        {
            $str .= "`add_time`>='".$where['start_time']."' AND ";
        }
        if(!empty($where['end_time']))
        {
            $str .= "`add_time`<='".$where['end_time']."' AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
        
        //echo $sql;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}


    /**
	 *	pageList，赠品操作日志分页列表
	 *
	 *	@url AppOrderActionController/search
	 */
	function pageListlog ($goods_number,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `gift_goods_log` where `zp_sn`='$goods_number'";
        
       
		$sql .= " ORDER BY `create_time` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
    public function CheckName($name){
        if(empty($name)){
            return false;
        }
        $sql = "SELECT `name` FROM ".$this->table()." where `name`='$name'";
        return $this->db()->getOne($sql);
    }
	public function TestCode($goods_number){
        if(empty($goods_number)){
            return false;
        }
        $sql = "SELECT `goods_number` FROM ".$this->table()." where `goods_number`='$goods_number'";
        return $this->db()->getOne($sql);
	}
	
	/*
	 * 通过款号查询是否可以删除
	*/
	public function getXzInfo($sn){
		
		$sql = "select o.order_status,o.send_good_status from base_order_info o left join app_order_details s on s.order_id = o.id where s.goods_sn='".$sn."' and o.order_status in (1,2,3) and o.send_good_status in (1)";

		return $this->db()->getAll($sql);
// 		$keys =array('style_sn');
// 		$vals =array($sn);
// 		$ret = ApiModel::giftman_api($keys,$vals,'getXzInfo');
// 		return $ret;
	}
	
	/*
	 * 通过id取款号
	*
	*/
	function getStyleById($style_id){
		$sql = "SELECT * FROM `" . $this->table() . "` WHERE `id`=".$style_id;
		return $this->db()->getRow($sql);
	}
	
	/*
	 * 通过id删除赠品
	 */
	function delZpById($id){
		if(empty($id)){
			return false;
		}
		$sql = "delete from ".$this->table()." where id =".$id;
		return $this->db()->query($sql);
	}
    /*
	 * 通过id判断订单中是否有该款未做过订单，或者订单是关闭或者已发货状态才允许修改，否则不能修改
	 */
	function getgiftorderstatus($id){
		if(empty($id)){
			return false;
		}
		$sql = "SELECT a.`id` FROM ".$this->table()." as a inner join `app_order_details` as b on a.`goods_number`=b.`goods_sn`
        inner join base_order_info as c on b.`order_id`=c.`id` where c.order_status not in (4) and c.send_good_status not in(2) and a.`id`='$id' ";
		return $this->db()->getOne($sql);
	}
    function setgiftgoodslog($zp_sn,$content,$create_name,$create_time){
        if(empty($zp_sn))
        {
            return false;
        }
        $sql="INSERT INTO gift_goods_log (`zp_sn`,`content`,`create_name`,`create_time`) VALUES ('$zp_sn','$content','$create_name','$create_time')";
       
        return $this->db()->query($sql);
    }
	
	
	
	
	
}

?>