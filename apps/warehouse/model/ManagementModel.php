<?php
/**
 * 销售模块的数据模型（代替Sales/Api/api.php）
 *  -------------------------------------------------
 *   @file      : SaleModel.php
 *   @link      :  www.kela.cn
 *   @copyright : 2014-2024 kela Inc
 *   @author    : Laipiyang <462166282@qq.com>
 *   @date      : 2015-02-10 15:34:30
 *   @update    :
 *  -------------------------------------------------
 */
class ManagementModel
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
  
    /*
    *@params $shop_name 门店名称
    *
    *通过店铺名称获得门店信息
    */
    public function GetShopInfoByShopName($shop_name){
        $sql = "select id from cuteframe.shop_cfg where shop_name in('".$shop_name."')";
        $shop_id = $this->db()->getOne($sql);
        if(!empty($shop_id)){
         $sql = "select s.id,s.company_id,c.company_name from cuteframe.sales_channels s left join cuteframe.company c on c.id = s.company_id where s.is_deleted = 0 and channel_own_id ='$shop_id' limit 1";
         return $this->db()->getRow($sql);
        }
        return false;
    }

    /*
    * 获取所有渠道信息
    *
    */
    public function getSalesChannels(){
        $sql = "select * from cuteframe.sales_channels";
        return $this->db()->getAll($sql);
    }


}

?>