<?php
/**
 *  -------------------------------------------------
 *   @file		: ButtonModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-11-24 11:44:08
 *   @update	:
 *  -------------------------------------------------
 */
class ButtonModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'button';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"按钮id",
"label"=>"显示名",
"class_id"=>"按钮样式id",
"function_id"=>"按钮图标id",
"cust_function"=>"自定义处理函数",
"icon_id"=>"按钮事件id",
"data_url"=>"按钮请求地址",
"type"=>"按钮类型；1为列表页面,2为查看页面",
"tips"=>"按钮提示",
"is_system"=>"是否系统内置",
"is_deleted"=>"是否删除",
"data_title"=>"页签标题",
"a_id"=>"所属模块",
"c_id"=>"所属文件",
"o_id"=>"请求方法",
"display_order"=>"排序");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ButtonController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT m.*,`c`.`classname`,`i`.`name` AS `iconname`,`bf`.`name` AS `functionname`,`ct`.`label` AS `control_name`,`ct`.`code` AS `control_code` FROM `".$this->table()."` AS `m` LEFT JOIN `button_class` AS `c` ON `m`.`class_id`=`c`.`id` LEFT JOIN `button_icon` AS `i` ON `m`.`icon_id`=`i`.`id` LEFT JOIN `button_function` AS `bf` ON `m`.`function_id`=`bf`.`id` LEFT JOIN `control` AS `ct` ON `m`.`c_id`=`ct`.`id` ";

		$str = '';
		if($where['c_id'])
		{
			$str .= "`m`.`c_id`='".$where['c_id']."' AND ";
		}
		if(isset($where['is_deleted']))
		{
			$str .=" `m`.`is_deleted`='".$where['is_deleted']."' AND ";
		}
		if($where['label'] != "")
		{
			$str .= "`m`.`label` LIKE \"%".addslashes($where['label'])."%\" AND ";
		}
		if($where['tips'] != "")
		{
			$str.= "`m`.`tips` LIKE \"%".addslashes($where['tips'])."%\" AND ";
		}
		
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}		
		
		$sql .= " ORDER BY `m`.`id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/*
	*  -------------------------------------------------
	*   
	*  -------------------------------------------------
	*/
	public function has ($do) 
	{
		$sql = "SELECT count(1) FROM `".$this->table()."` WHERE `a_id`='{$do['a_id']}' AND `c_id`='{$do['c_id']}' AND id<>'{$this->pk()}' AND `o_id`='{$do['o_id']}' AND `function_id`={$do['function_id']} AND `data_url`='".$do['data_url']."'";
		return $this->db()->getOne($sql);
	}

	public function getControls ($app_id) 
	{
		$sql = "SELECT `id`,`label`,`code` FROM `control` WHERE `application_id`='{$app_id}' AND `is_deleted`='0'";
		return $this->db()->getAll($sql);
	}

	public function getOperations ($c_id) 
	{
		$sql = "SELECT `id`,`label`,`method_name` FROM `operation` WHERE `c_id`='{$c_id}' AND `is_deleted`='0'";
		return $this->db()->getAll($sql);
	}

	/**
	 *	ButtonContolller/ajax
	 */
	public function listAlls ($type)
	{
		$sql = "(SELECT `id`,`name`,`label`,`tips` FROM `button_function` WHERE `type`='".$type."' AND `is_deleted`='0') ";
		if($type!=3)
		{
			$sql .= "UNION (SELECT `id`,`name`,`label`,`tips` FROM `button_function` WHERE `type`='3' AND `is_deleted`='0') ";
		}
		$sql .=" ORDER BY `id` DESC ";
		return $this->db()->getAll($sql);
	}
}

?>