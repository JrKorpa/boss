<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-07 19:07:53
 *   @update	:
 *  -------------------------------------------------
 */
class ExpressListModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'express_list';
		$this->_dataObject = array("id"=>" ",
		"address"=>"收货地址",
		"express_id"=>'快递公司',		
		"create_time"=>" ",
		"create_user"=>" "
	    );
		parent::__construct($id,$strConn);
	}
	/**
	 *	pageList，分页列表
	 *
	 *	@url ApplicationController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		
		
		//$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		//return $data;
	}
	function updateExpressNO($id,$express_no){
        $this->db()->query("update express_list set express_no='$express_no' where id='$id'");  
	}
    function getRow($order_id){
    	//echo "select * from express_list where id='$order_id'";
        return $this->db()->getRow("select * from express_list where id='$order_id'");
    }
	
}?>