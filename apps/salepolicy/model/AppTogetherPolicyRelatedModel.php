<?php
/**
 *  -------------------------------------------------
 *   @file		: AppTogetherPolicyRelatedModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-03-14 18:55:25
 *   @update	:
 *  -------------------------------------------------
 */
class AppTogetherPolicyRelatedModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_together_policey_related';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"自增id",
"policy_id"=>"销售策略id",
"together_id"=>"打包策略id",
"goods_id"=>"商品id");
		parent::__construct($id,$strConn);
	}

}

?>