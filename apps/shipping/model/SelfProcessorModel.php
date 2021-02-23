<?php
/**
 * Processor模块的数据模型（代替Processor/Api/api.php）
 *  -------------------------------------------------
 *   @file		: SaleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class SelfProcessorModel extends SelfModel
{
    protected $db;
    function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}	
    //prodcut_info单表查询
	function selectProdcutInfo($field="*",$where,$type=1){
	    $this->select($field,$where,$type,"product_info");
	}

}

?>