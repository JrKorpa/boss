<?php
/**
 *  -------------------------------------------------
 *   @file		: ForumLinksModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-17 15:54:24
 *   @update	:
 *  -------------------------------------------------
 */
class ForumLinksModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'forum_links';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"主键",
"title"=>"显示文字",
"url_img"=>" ",
"url_addr"=>"链接地址",
"display_order"=>"显示顺序");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url ForumLinksController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT `id`,`title`,`url_addr` FROM `".$this->table()."` ";
		$str = '';
		if($where['title'] != "")
		{
			$sstr .= "`title` LIKE \"%".addslashes($where['title'])."%\" AND ";
		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	/**
	 *	getList，列表排序
	 *
	 *	@url ForumLinksController/listAll
	 */
	public function getList () 
	{
		$sql = "SELECT `id`,`title` FROM `".$this->table()."` ORDER BY `display_order` DESC";
		return $this->db()->getAll($sql);
	}

	/**
	 *	sortLink，保存排序
	 *
	 *	@url ForumLinksController/saveSort
	 */
	public function sortLink ($ids) 
	{
		$len = count($ids);
		try{
			for ($i=0;$i<$len;$i++) 
			{
				$sql = "UPDATE `".$this->table()."` SET `display_order`='".($i+1)."' WHERE `id`=".$ids[$i];
				$this->db()->query($sql);
			}
		}
		catch(Exception $e)
		{
			return false;
		}
		return true;
	}
}
?>