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
class ProductInfoImgModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'rel_img_product_info';
        $this->_dataObject = array("id"=>"ID",
				"g_id"=>"布产ID",
				"code"=>"属性code",
				"name"=>"属性name",
				"value"=>"属性value值");
		parent::__construct($id,$strConn);
	}

	function insert($arr)
	{
		if(is_array($arr) and !empty($arr))
		{
			$sql="insert into $this->_objName (`".implode("`,`",array_keys($arr[0]))."`) values ";

			foreach($arr as $key=>$v)
			{
				$sql.="('".implode("','",array_values($v))."'),";
			}
			$sql=rtrim($sql,',');
			return $this->db()->query($sql);
		}
		else
		{
			return false;
		}

	}
	function getImgList($id)
	{
		$sql="select save_path,save_name from $this->_objName where product_info_id=$id";
		$row=$this->db()->getRow($sql);
		if($row)
		{
		   $row['thumb_img']=$row['save_path'].$row['save_name'];
		   $row['big_img']= $row['thumb_img'];
		}
		return $row;



	}


}

?>