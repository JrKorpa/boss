<?php
/**
 *  -------------------------------------------------
 *   @file		: RelStyleLoversModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-20 17:37:08
 *   @update	:
 *  -------------------------------------------------
 */
class RelStyleLoversModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'rel_style_lovers';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"自增id",
"style_id1"=>"款式id",
"style_id2"=>"款式id",
"style_sn1"=>"款式编号",
"style_sn2"=>"款式编号");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url RelStyleLoversController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
        $sql = "SELECT `sl`.`id` as `love_id`,`sl`.`style_id1`,`sl`.`style_id2`,`sl`.`style_sn1`,`sl`.`style_sn2`,`si`.* FROM `".$this->table()."` as `sl`,`".$this->table()."` as `sa`, `base_style_info` as `si` WHERE `sl`.`style_sn1`=`sa`.`style_sn1` and (`sl`.`style_id1`=`si`.`style_id` or `sa`.`style_id2`=`si`.`style_id`) ";
		//不要用*,修改为具体字段
        if (isset($where['style_sn']) && !empty($where['style_sn'])){
            $sql .= " AND (`sl`.`style_sn1`='{$where['style_sn']}' or `sa`.`style_sn2`='{$where['style_sn']}')";
        }
        if (isset($where['style_sn_in']) && !empty($where['style_sn_in'])) {
            $sql .= " AND (`sl`.`style_sn1` in ({$where['style_sn_in']}) or `sl`.`style_sn2` in ({$where['style_sn_in']}) )";
        }
        if(isset($where['product_type_id']) && !empty($where['product_type_id'])){
            $sql .=" AND `si`.`product_type` in ({$where['product_type_id']}) ";
        }
        if(isset($where['cat_type_id']) && !empty($where['cat_type_id'])){
            $sql .=" AND `si`.`style_type` = '{$where['cat_type_id']}' ";
        }
        if(isset($where['style_sex']) && !empty($where['style_sex'])){
            $sql .=" AND `si`.`style_sex` = '{$where['style_sex']}' ";
        }
        if(isset($where['style_name']) && !empty($where['style_name'])){
            $sql .=" AND `si`.`style_name` like '%" . addslashes($where['style_name']) . "%'";
        }
        if(isset($where['is_made']) && $where['is_made']!=''){
            $sql .=" AND `si`.`is_made` = {$where['is_made']}";
        }
        if(isset($where['check_status']) && !empty($where['check_status'])){
            $sql .=" AND `si`.`check_status` = '{$where['check_status']}' ";
        }
        if(isset($where['xilie']) && !empty($where['xilie'])){
            if(count($where['xilie'])==1){
                 $sql.= " AND `si`.`xilie` like'%,".$where['xilie'][0].",%'";
            }else{
                $str = "";
                foreach ($where['xilie'] as $val){
                     $str.=" `si`.`xilie` like '%,".$val.",%' or ";
                }
                $str = rtrim($str," or");
                $sql .= " AND (".$str.")";
               
            }
        }
        if(isset($where['check_status_zuofei']) && !empty($where['check_status_zuofei'])){
            $sql .=" AND `si`.`check_status` != {$where['check_status_zuofei']}";
        }
        $sql .= " order by `sl`.`id` desc";
     	$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
    
    //判断此款号是否已经存在情侣列表
    public function checkStyleLoverStyleSn($style_sn){
        if(empty($style_sn)){
            return false;
        }
        $sql = "SELECT `id` FROM `" . $this->table() . "` as `a` WHERE `a`.style_sn1='".$style_sn."' or  `a`.style_sn2='".$style_sn."'";
      
        $data = $this->db()->getAll($sql);
        return $data;
    }
}

?>