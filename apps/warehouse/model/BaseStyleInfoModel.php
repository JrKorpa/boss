<?php
/**
 * 销售模块的数据模型（代替Sales/Api/api.php）
 *  -------------------------------------------------
 *   @file		: SaleModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class BaseStyleInfoModel
{
    protected $db;
	function __construct ($strConn="")
	{
		$this->db = DB::cn($strConn);
	}
	public function db(){
	    return $this->db;
	}
	final public static function add_special_char($value)
	{
	    if ('*' == $value || false !== strpos($value, '(') || false !== strpos($value, '.') || false !== strpos($value, '`'))
	    {
	        //不处理包含* 或者 使用了sql方法。
	    }
	    else
	    {
	        $value = '`' . trim($value) . '`';
	    }
	    if (preg_match('/\b(select|insert|update|delete)\b/i', $value))
	    {
	        $value = preg_replace('/\b(select|insert|update|delete)\b/i', '', $value);
	    }
	    return $value;
	}
	/*
	 * updateSql,生成更新语句
	 */
	protected function updateSql ($table,$do,$where)
	{
	    $field = '';
	    $fields = array();
	    foreach ($do as $key=>$val)
	    {
	        switch (substr($val, 0, 2))
	        {
	            case '+=':
	                $val = substr($val,2);
	                if (is_numeric($val)) {
	                    $fields[] = self::add_special_char($key) . '=' . self::add_special_char($key) . '+' . $val;
	                }
	                else
	                {
	                    continue;
	                }
	                break;
	            case '-=':
	                $val = substr($val, 2);
	                if (is_numeric($val))
	                {
	                    $fields[] = self::add_special_char($val) . '=' . self::add_special_char($key) . '-' . $val;
	                }
	                else
	                {
	                    continue;
	                }
	                break;
	            default:
	                if(is_numeric($val))
	                {
	                    $fields[] = self::add_special_char($key) . '=' . $val;
	                }
	                else
	                {
	                    $fields[] = self::add_special_char($key) . '="' . $val.'"';
	                }
	        }
	    }
	    $field = implode(',', $fields);
	    $sql = "UPDATE `".$table."` SET ".$field;
	    $sql .= " WHERE {$where}";
	    return $sql;
	}
	protected function insertSql ($do,$tableName = "")
	{  
	    $fields = array_keys($do);
	    $valuedata = array_values($do);
	    array_walk($fields, array($this, 'add_special_char'));
	    $field = implode('`,`', $fields);
	    $value = implode('","',$valuedata);
	    
	    return "INSERT INTO `".$tableName."` (`" . $field . "`) VALUES (\"". $value ."\")";
	}
	
	
	
	
  //查询是否有满足条件的款号
	public  function isBaseStyle($goods_sn){
		$sql="SELECT style_id  FROM base_style_info WHERE style_sn = '$goods_sn' AND check_status = 3 ";
		return $this->db->getRow($sql);
	}

	public  function isBaseStyleFromT100($goods_sn){
		$sql="SELECT imca001  FROM _imca_t WHERE imca001 = '$goods_sn' ";
		return $this->db->getRow($sql);
	}
	
    /**
    * 查询款式图片信息 替换API方式 打印提货单用
    * @param $style_sn 款号
    * @param $image_place 位置
    
    */
	public function GetStyleGalleryInfo($style_sn,$image_place)
	{
	    $style_sn = trim($style_sn);
		$sql = "select `sg`.`g_id`, `sg`.`style_id`, `sg`.`image_place`, `sg`.`img_sort`, `sg`.`img_ori`, `sg`.`thumb_img`, `sg`.`middle_img`, `sg`.`big_img`,`ct`.`cat_type_name` from `base_style_info` as `si` left join `app_cat_type` as `ct` on (`si`.`style_type`=`ct`.`cat_type_id`) left join `app_style_gallery` as `sg` on `si`.`style_id`=`sg`.`style_id`" .
		    " where `si`.`style_sn` = '{$style_sn}' and (`sg`.`image_place`='{$image_place}' or `sg`.`image_place` is null)";
        //$sql .= " order by `sg`.`image_place` asc";
        $data = $this->db->getAll($sql);
        return $data;
    }

    //获取款式库所有款信息
    public function getstyleAllInfo()
    {
        $sql = "select `style_sn` from `base_style_info` where `check_status` not in(1, 2, 4)";
        return $this->db->getAll($sql);
    }

    public function getStyleInfo($field,$where){
        $sql = "select {$field} from front.base_style_info where 1";
        $str = "";
        if(!empty($where['style_sn'])){
               $str .= " AND style_sn='".addslashes(trim($where['style_sn']))."'";
        }
        $sql .= $str;
        return $this->db->getRow($sql);
    }
}

?>