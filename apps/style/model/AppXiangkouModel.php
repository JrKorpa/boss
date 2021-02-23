<?php
/**
 *  -------------------------------------------------
 *   @file		: AppXiangkouModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-22 23:00:31
 *   @update	:
 *  -------------------------------------------------
 */
class AppXiangkouModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_xiangkou';
		$this->pk='x_id';
		$this->_prefix='';
        $this->_dataObject = array("x_id"=>" ",
"style_id"=>"款式编码",
"style_sn"=>"款式编码",
"stone"=>"镶口",
"finger"=>"手寸",
"main_stone_weight"=>"主石重",
"main_stone_num"=>"主石数",
"sec_stone_weight"=>"副石重",
"sec_stone_num"=>"副石重数",
"sec_stone_weight_other"=>"其他副石重",
"sec_stone_num_other"=>"其他副石数",
"g18_weight"=>"18K金重",
"g18_weight_more"=>"18K金重上公差",
"g18_weight_more2"=>"18K金重下公差",
"gpt_weight"=>"pt950金重",
"gpt_weight_more"=>"pt950金重上公差",
"gpt_weight_more2"=>"pt950金重下公差",
"sec_stone_price_other"=>"其他副石成本价");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppXiangkouController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
        if(isset($where['style_id']) && !empty($where['style_id'])){
            $sql .=" AND style_id = '{$where['style_id']}' ";
        }
		$sql .= " ORDER BY x_id DESC";
        //echo $sql;die;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 *	style_sn，取款
	 *
	 *	@url AppXiangkouController/get
	 */
	function getXiangKouByStyle_sn ($where)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
        if(isset($where['style_sn']) && !empty($where['style_sn'])){
            $sql .=" AND `style_sn` = '{$where['style_sn']}' ";
        }
		$sql .= " ORDER BY x_id DESC";
		$data = $this->db()->getAll($sql);
		return $data;
	}

	/**
	 *	style_sn，取款
	 *
	 *	@url AppXiangkouController/get
	 */
	function getXiangKouByStyle_Id($where)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
        if(isset($where['style_id']) && !empty($where['style_id'])){
            $sql .=" AND `style_id` = '{$where['style_id']}' ";
        }
        if(isset($where['stone']) && !empty($where['stone'])){
            $sql .=" AND `stone` = '{$where['stone']}' ";
        }
        if(isset($where['finger']) && !empty($where['finger'])){
            $sql .=" AND `finger` = '{$where['finger']}' ";
        }
		$sql .= " ORDER BY x_id DESC";
		$data = $this->db()->getAll($sql);
		return $data;
	}

	/**
	 *	style_sn，删除
	 *
	 *	@url AppXiangkouController/delete
	 */
	function deleteXiangKouByStyle_sn ($where)
	{
		$sql = "DELETE FROM `".$this->table()."` WHERE `style_sn` = '{$where['style_sn']}' ";
		$this->db()->query($sql); 
	}

    /**
     *  style_sn，删除
     *
     *  @url AppXiangkouController/delete
     */
    function deleteXiangKouByStyle_snInfo ($where)
    {
        if($where['style_sn'] == ''){
            return false;
        }
        if($where['stone'] == ''){
            return false;
        }
        if($where['finger'] == ''){
            return false;
        }

        $sql = "DELETE FROM `".$this->table()."` WHERE `style_sn` = '{$where['style_sn']}' AND `stone` = '{$where['stone']}' AND `finger` = '{$where['finger']}'";
        return $this->db()->query($sql); 
    }

    /**
     *  style_sn，删除
     *
     *  @url AppXiangkouController/delete
     */
    function delStyleXiangkouFinger ($where)
    {

        $sql = "DELETE FROM `".$this->table()."` WHERE `style_sn` = '{$where['style_sn']}' AND `stone` = '{$where['stone']}' AND `finger` = '{$where['finger']}'";
        $this->db()->query($sql); 
    }
}

?>