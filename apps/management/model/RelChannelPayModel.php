<?php
/**
 *  -------------------------------------------------
 *   @file		: RelChannelPayModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-05-10 11:50:57
 *   @update	:
 *  -------------------------------------------------
 */
class RelChannelPayModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'rel_channel_pay';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"自增id",
"channel_id"=>"渠道id",
"channel_name"=>"销售渠道",
"pay_id"=>"订购类型id（支付id）",
"pay_name"=>"订购类型名称");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url RelChannelPayController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
		if($where['channel_id'] != "")
		{
			$str .= "`channel_id` = ".$where['channel_id']." AND ";
		}
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
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
     * 检查销售渠道和支付方式是否有关联关系
     * @param type $channel_id
     * @param type $pay_id
     * @return type
     */
    public function check_exists($channel_id,$pay_id,$id=0) {
        $sql = "SELECT `id` FROM `{$this->table()}` WHERE `pay_id`=$pay_id AND `channel_id`=$channel_id";
        if($id){
            $sql .= " AND `id` <> $id";
        }
        return $this->db()->getOne($sql);
    }
    
    /**
     * 获取和该销售渠道关联的支付方式
     * @param type $channel_id
     * @return type
     */
    public function getPayMentList($channel_id){
        $sql = "SELECT `id`,`pay_id`,`pay_name` FROM `{$this->table()}` WHERE `channel_id`=$channel_id";
        return $this->db()->getAll($sql);
    }
    
}

?>