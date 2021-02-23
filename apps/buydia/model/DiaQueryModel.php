<?php
/**
 *  -------------------------------------------------
 *   @file		: DiaQueryModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-03-22 11:54:37
 *   @update	:
 *  -------------------------------------------------
 */
class DiaQueryModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'stone';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"ID",
"dia_package"=>"石包",
"purchase_price"=>"每卡采购价格(元)",
"supplier"=>"供应商",
"specification"=>"规格",
"color"=>"颜色",
"neatness"=>"净度",
"cut"=>"切工",
"symmetry"=>"对称",
"polishing"=>"抛光",
"fluorescence"=>"荧光",
"status"=>"状态",
"lose_efficacy_time"=>"失效时间",
"lose_efficacy_cause"=>"失效原因",
"lose_efficacy_user"=>"失效操作人");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url DiaQueryController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
		if(!empty($where['dia_package']))
		{
            if(count($where['dia_package']) > 1){
                $str .= "`dia_package` in('".implode("','", $where['dia_package'])."') AND ";
            }else{
                $str .= "`dia_package` like \"".addslashes($where['dia_package'][0])."%\" AND ";
            }
		}
		if(!empty($where['status']))
		{
			$str .= "`status`='".$where['status']."' AND ";
		}
        if(!empty($where['sup_id']))
        {
            $str .= "`sup_id`='".$where['sup_id']."' AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
        //echo $sql;die;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
}

?>