<?php
/**
 *  -------------------------------------------------
 *   @file		: DiamondInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 19:44:38
 *   @update	:
 *  -------------------------------------------------
 */
class DiamondInfoModel extends Model
{
    function __construct ($id=NULL,$strConn="")
    {
    
        // 		$this->_objName = 'app_order_cart';
        $this->_objName = 'diamond_info';
        $this->pk='id';
        $this->_prefix='';
        $this->_dataObject = array("id"=>"ID",
        );
        parent::__construct($id,$strConn);
    }
    
    //根据证书号获取裸钻基本信息
    public function getDiamondInfoByCertId($cert_id){
        $sql = "select * from diamond_info where cert_id='{$cert_id}'";
        $result = $this->db()->getRow($sql);
        return $result;
    }
}

?>