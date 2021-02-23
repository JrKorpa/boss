<?php
/**
 *  -------------------------------------------------
 *   @file		: ProductInfoAttrModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-29 17:36:30
 *   @update	:
 *  -------------------------------------------------
 */
class ProductInfoAttrModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'product_info_attr';
        $this->_dataObject = array("id"=>"ID",
"g_id"=>"布产ID",
"code"=>"属性code",
"name"=>"属性name",
"value"=>"属性value值");
		parent::__construct($id,$strConn);
	}

	function getGoodsAttr($gid)
	{
		$sql = "SELECT `id`,`g_id`,`code`,`name`,`value` FROM ".$this->table()." WHERE g_id = {$gid}";
		return $this->db()->getAll($sql);
	}
	/**
	 * 修改布产商品属性值
	 * @param unknown $value
	 * @param unknown $g_id 布产ID(product_info_attr g_id)
	 * @param unknown $code
	 */
    function editGoodsAttr($value,$g_id,$code){
       $sql = "UPDATE ".$this->table()." SET `value`='".$value."' WHERE `g_id`='".$g_id."' AND `code` = '".$code."'";
       return $this->db()->query($sql);
    }
	function delGoodsAttr($gid)
	{
		$sql = "DELETE FROM ".$this->table()." WHERE g_id = ".$gid;
		return $this->db()->query($sql);
	}

	/**
	* 普通查询
	* @param $type one 查询单个字段， row查询一条记录 all 查询多条记录
	*/
	public function select2($fields = ' * ' , $where = " 1 " , $type = 'one'){
		$sql = "SELECT {$fields} FROM `product_info_attr` WHERE {$where}";
		if($type == 'one'){
			$res = $this->db()->getOne($sql);
		}else if($type == 'row'){
			$res = $this->db()->getRow($sql);
		}else if($type == 'all'){
			$res = $this->db()->getAll($sql);
		}
		return $res;
	}

	/*
	*通过布产单id获取证书号
	*/
	public function getCertNumById($id){
		$sql ="select value from ".$this->table()." where code='zhengshuhao' AND g_id='".$id."'";
		return $this->db()->getOne($sql);
	}



}

?>