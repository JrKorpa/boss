<?php
/**
 *  -------------------------------------------------
 *   @file		: DiamondFourcInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-02-27 15:27:56
 *   @update	:
 *  -------------------------------------------------
 */
class DiamondFourcInfoModel extends Model
{
    public static $color_arr = array("不分级","N","M","L","K-L","K","J-K","J","I-J","I","H-I","H","H+","G-H","G","F-G","F","E-F","E","D-E","D","黄","蓝","粉","橙","绿","红","香槟","格雷恩","紫","混色","蓝紫色","黑","变色","其他","白色","金色");
    public static $clarity_arr = array("不分级","P","P1","I","I1","I2","SI","SI1","SI2","VS","VS1","VS2","VVS","VVS1","VVS2","IF","FL");
    public static $shape_arr = array(1 => '圆形', 2 => '公主方形', 3 => '祖母绿形', 4 => '橄榄形', 5 => '椭圆形', 6 => '水滴形', 7 => '心形', 8 => '坐垫形', 9 => '辐射形', 10 => '方形辐射形', 11 => '方形祖母绿', 12 => '三角形',13=>'戒指托',14=>'异形',15=>'梨形',16=>'阿斯切',17 => '马眼', 18 => '长方形', 19 => '雷迪恩');
    public static $cert_arr = array('HRD-D','GIA','HRD','IGI','DIA','AGL','EGL','NGTC','NGGC','HRD-S','无');
    
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'diamond_fourc_info';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"shape"=>"形状",
"carat_min"=>"最小石重",
"carat_max"=>"最大石重",
"color"=>"颜色",
"clarity"=>"净度",
"cert"=>"证书类型",
"price"=>"价格",
"status"=>"状态 ：1启用，2禁用");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url DiamondFourcInfoController/search
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
		if(!empty($where['shape']))
		{
			$str .= "`shape`='".$where['shape']."' AND ";
		}
        if(!empty($where['carat_min']))
        {
            $str .= "`carat_min`>='".$where['carat_min']."' AND ";
        }
        if(!empty($where['carat_max']))
        {
            $str .= "`carat_max`<='".$where['carat_max']."' AND ";
        }
        if(!empty($where['color']))
        {
            $str .= "`color`='".$where['color']."' AND ";
        }
        if(!empty($where['clarity']))
        {
            $str .= "`clarity`='".$where['clarity']."' AND ";
        }
        if(!empty($where['cert']))
        {
            $str .= "`cert`='".$where['cert']."' AND ";
        }
        if(!empty($where['status']))
        {
            $str .= "`status`='".$where['status']."' AND ";
        }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
        //echo $sql;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}	
	
	public function getDistinctFields($fields,$where=array()){
	    $whereStr = "";
	    $sql = "select distinct {$fields} from ".$this->table()." where 1=1".$whereStr;
	    $data = $this->db()->getAll($sql);
	    $data = array_column($data, $fields);
	    return $data;
	}

}

?>