<?php
/**
 *  -------------------------------------------------
 *   @file		: JxsAreaScopeModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-07-17 01:51:50
 *   @update	:
 *  -------------------------------------------------
 */
class JxsAreaScopeModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'jxs_area_scope';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"jxs_id"=>"经销商id",
"country_id"=>" ",
"province_id"=>"0=全部",
"city_id"=>"0=全部",
"region_id"=>"0=全部",
"create_time"=>" ",
"create_user"=>" ");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url JxsAreaScopeController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
		if(!empty($where['jxs_id']))
		{
			$str .= "`jxs_id`='".$where['jxs_id']."' AND ";
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
    * 验证区域是否添加
    */
    public function checkAreaScope($where)
    {
        $sql = "SELECT count(*) FROM `".$this->table()."`";
        $str = '';
//      if($where['xxx'] != "")
//      {
//          $str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//      }
        if(!empty($where['country_id']))
        {
            $str .= "`country_id`=".$where['country_id']." AND ";
        }
        if(!empty($where['province_id']))
        {
            $str .= "`province_id`=".$where['province_id']." AND ";
        }
        $str .= "(";
        if(!empty($where['city_id']))
        {
            $str .= "`city_id`=".$where['city_id']." or ";
        }
        $str .= "`city_id` = 0) AND ";
        $str .= "(";
        if(!empty($where['region_id']))
        {
            $str .= "`region_id`=".$where['region_id']." or ";
        }
        $str .= "`region_id` = 0) AND ";
        if($str)
        {
            $str = rtrim($str,"AND ");//这个空格很重要
            $sql .=" WHERE ".$str;
        }
        //echo $sql;die;
        return $this->db()->getOne($sql);
    }


    /**
    * 验证同一体验店区域是否添加重复
    */
    public function checkCfAreaScope($where)
    {
        $sql = "SELECT count(*) FROM `".$this->table()."`";
        $str = '';
//      if($where['xxx'] != "")
//      {
//          $str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//      }
        if(!empty($where['jxs_id']))
        {
            $str .= "`jxs_id`=".$where['jxs_id']." AND ";
        }
        if(!empty($where['country_id']))
        {
            $str .= "`country_id`=".$where['country_id']." AND ";
        }
        if(!empty($where['province_id']))
        {
            $str .= "`province_id`=".$where['province_id']." AND ";
        }
        if(!empty($where['city_id']))
        {
            $str .= "`city_id`=".$where['city_id']." AND ";
        }
        if(!empty($where['region_id']))
        {
            $str .= "`region_id`=".$where['region_id']." AND ";
        }
        if($str)
        {
            $str = rtrim($str,"AND ");//这个空格很重要
            $sql .=" WHERE ".$str;
        }
        //echo $sql;die;
        return $this->db()->getOne($sql);
    }

    /**
     * 取单个
     * @return type
     */
    public function getRegionOption($region_ids) {
        $model = new RegionModel(1);
        return $model->getRegionList($region_ids);
    }

    public function getAllJxs(){
        $sql = "SELECT jxs_id,country_id,province_id,city_id,region_id FROM `".$this->table()."`";
        return $this->db()->getAll($sql);
    }
}

