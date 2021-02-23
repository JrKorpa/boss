<?php
/**
 *  -------------------------------------------------
 *   @file		: {MODEL}Model.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: {DATE}
 *   @update	:
 *  -------------------------------------------------
 */
class {MODEL}Model extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = '{TABLE}';
        $this->_dataObject = {DATA};
		parent::__construct($id,$strConn);
	}
}

?>