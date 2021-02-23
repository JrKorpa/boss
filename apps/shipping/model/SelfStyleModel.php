<?php
/**
 * 款式模块的数据模型（代替Style/Api/api.php）
 *  -------------------------------------------------
 *   @file		: SaleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class SelfStyleModel extends SelfModel
{
    protected $db;
    function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}	
    //app_style_gallery单表查询
	function selectStyleGallery($field="",$where,$type){
	    $this->select($field,$where,$type,"app_style_gallery");
	}

}

?>