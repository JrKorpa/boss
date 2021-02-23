<?php 
/**
 *  -------------------------------------------------
 * 文件说明     数据导入类
 * @file        : data_export_class.php
 * @author      : yangxt <yangxiaotong@163.com>
 * @date        : 2015-01-22 10:07:27
 * @version     : 1.0
 *  -------------------------------------------------
*/
class ExportData
{
    protected $new_conf;//新配置
    protected $old_conf;//旧配置
    protected $old_db;
    protected $new_db;
    protected $n_table;//新表
    protected $o_table;//旧表
    protected $oldsys_id = "oldsys_id";//记录旧主键
    protected $pass_status = 'pass_status';//写入更新状态
    protected $start = 0;
    protected $len = 0;
    protected $pass;
    protected $old_id;


    public $old_data = array(); //旧表数据
    public $old_pk = array();   //旧表主键

    public $filter = array();   //过滤字段

    function __construct($new_conf,$old_conf,$n_table,$o_table,$filter,$start = 0,$len = 0,$pass = true,$old_id = true)
    {
        $this->new_conf = $new_conf;
        $this->old_conf = $old_conf;
        $this->n_table = $n_table;
        $this->o_table = $o_table;
        $this->filter = $filter;
        $this->start = $start;
        $this->len = $len;
        $this->pass = $pass;
        $this->old_id = $old_id;


        $this->old_db = new PDO($this->old_conf['dsn'], $this->old_conf['user'], $this->old_conf['password'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
        $this->new_db = new PDO($this->new_conf['dsn'], $this->new_conf['user'], $this->new_conf['password'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));

        $this->getOldPk();
    }


    public function getOldDate($where = false,$pass = false)
    {
        $sql = "desc `".$this->o_table."`";
        $obj = $this->old_db->query($sql);
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $data = $obj->fetchAll();
        foreach ($data as $v) {
            $fields[] = $v['Field'];
        }
        $sel = '';
        if(!empty($this->filter)){
            foreach ($this->filter as $k => $v) {
                $sel .= "`".$k."` AS ".$v.",";
            }
        }else{
            foreach ($fields as $v) {
                $sel .= "`".$v."`,";
            }
        }
        $sel = substr($sel,0,-1);
        $p_str = '';
        if($pass !== false){
            $sel .= ",`".$this->old_pk."`";
            $p_str = " AND `".$this->pass_status."` = '0'";
        }
        $sql = "SELECT ".$sel." FROM ".$this->o_table.' WHERE 1=1';

        if($where !== false){
            $sql .= " AND ".$where;
        }
        $sql .= $p_str;

        if($this->len != '0'){
            $sql .= " limit $this->start,$this->len";
        }

        // print_r($sql);exit;
        $obj = $this->old_db->query($sql);
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $this->old_data = $obj->fetchAll();
        return $this->old_data;
    }

    public function insertData($dict = array(),$save_create = false,$default=array())
    {
        if(empty($this->old_data)){
            echo iconv('UTF-8', 'GBK', '没有数据需要更新');
            exit;
        }
        // print_r($default);exit;
        // print_r($this->old_data[0]);exit;
        //================数据处理======================
            foreach ($this->old_data as $key=> $row) {
                foreach ($row as $k => $v) {
                    //处理旧库表中的主键
                    if($k == $this->old_pk && $this->old_pk =='oldsys_id'){
                        // print_r($v);exit;
                        $this->old_data[$key][$this->oldsys_id] = $v;
                    }
                    //处理数据字典
                    if(!empty($dict)){
                        foreach ($dict as $d_k => $d_v) {
                            if($k == $d_k){
                                $this->old_data[$key][$k] = $d_v[$this->old_data[$key][$k]];
                            }
                        }
                    }
                }
                if($this->old_pk == 'oldsys_id'){
                    unset($this->old_data[$key][$this->old_pk]);
                }
                if(isset($this->old_data[$key][$this->pass_status])){
                    unset($this->old_data[$key][$this->pass_status]);
                }
            }
        //================设置默认值====================
            foreach ($this->old_data as $key => $row) {
                foreach ($row as $k => $v) {
                   if($save_create && !empty($default)){
                        foreach ($default as $k_d => $v) {
                            $this->old_data[$key][$k_d] = $v;
                        }
                    }
                }
            }
        // print_r($this->old_data[0]);exit;
        //=================预处理=======================
            $label = " (";$value = " (";
            foreach ($this->old_data[0] as $k => $v) {
                //不记录`oldsys_id`
                if(!$this->old_id && ($k == $this->oldsys_id) && $this->old_pk == 'oldsys_id'){
                    continue;
                }else{
                    $label .= "`".$k."`,";
                    $value .= ":".$k.","; 
                }
            }
            $label = substr($label,0,-1);$label .= ") ";
            $value = substr($value, 0,-1);$value .= ")";

            $sql = "INSERT INTO `".$this->n_table."`".$label."VALUES ".$value."";
            $sql_test1 = "INSERT INTO `".$this->n_table."`".$label."VALUES ";
            // print_r($sql);exit;
            $new_db = $this->new_db;
            $stmt = $new_db->prepare($sql);
        //=================绑定参数=====================
            foreach ($this->old_data[0] as $key => $value) {
                if(!$this->old_id && ($key == $this->oldsys_id) && $this->old_pk == 'oldsys_id'){
                       continue;
                }else{             
                    $stmt->bindParam(':'.$key.'', $$key, PDO::PARAM_STR);
                }
            }
            $res = false;
            $sql_test2 = " (";
        //=================绑定值=======================    
            foreach ($this->old_data as $kkk=>$row) {
                foreach ($row as $k => $v) {
                    if(!$this->old_id && ($k == $this->oldsys_id) && $this->old_pk == 'oldsys_id'){
                       continue;
                    }else{
                        $$k = $v;
                        $sql_test2 .= "'".$v."',"; 
                    } 
                }
        //=================插入数据=====================
        // print_r($sql);exit;
            $res = $stmt->execute();
            // var_dump($res);var_dump($this->pass);exit;
            $sql_test2 = substr($sql_test2, 0,-1);$sql_test2 .= ")";
            $sql_test = $sql_test1." ".$sql_test2;
        //=============【调试专用】========================         
        //print_r($sql_test);exit;//
            if($res && $this->pass){
                $sql = 'UPDATE `'.$this->o_table.'` SET '.$this->pass_status." = '1' WHERE `".$this->old_pk."` = '".$row[$this->oldsys_id]."'";
                // print_r($this->oldsys_id);exit;
                $res = $this->old_db->query($sql);
                if(!$res){
                     echo iconv('UTF-8', 'GBK', "LOSE UPDATE ".$this->o_table." ".$row[$this->oldsys_id]."\r\n");
                     file_put_contents(__DIR__."/log/update/".date('YmdH')."_sql.log",$sql."\r\n",FILE_APPEND);
                }else{
                    echo iconv('UTF-8', 'GBK', "SUCCESS UPDATE ".$this->o_table." ".$row[$this->oldsys_id]."\r\n");
                } 
            }
            if(!$res){
                echo iconv('UTF-8', 'GBK', "LOSE INSERT ".$row[$this->oldsys_id]." TO ".$this->n_table."\r\n");
                // file_put_contents(__DIR__."/log/insert/".date('YmdH').".log","插入".implode('|#|', $row)."失败\r\n",FILE_APPEND);
                file_put_contents(__DIR__."/log/insert/".date('YmdH')."_sql.log",$sql_test."\r\n",FILE_APPEND);
                $sql_test2=" (";
            }else{
                echo iconv('UTF-8', 'GBK', "SUCCESS INSERT ".$row[$this->oldsys_id]." TO ".$this->n_table."\r\n");
                $sql_test2=" (";
            }
        }
         echo iconv('UTF-8', 'GBK', "---===THE MISSION END===---\r\n[".$this->n_table."]");
         file_put_contents(__DIR__."/log/insert/".date('YmdH')."_sql.log",$sql_test."\r\n---===THE MISSION END===---\r\n\r\n\r\n",FILE_APPEND);
    }

    public function getOldPk()
    {
        $sql = "desc `".$this->o_table."`";
        // print_r($sql);exit;
        $obj = $this->old_db->query($sql);
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $data = $obj->fetchAll();
        $pk = false;
        foreach ($data as $v) {
            if($v['Key'] == 'PRI'){
                $pk = $v['Field'];
            }
        }
        $this->old_pk = $pk;
    }


    public function addPassFiled(){
        $sql = "ALTER TABLE `".$this->o_table."` ADD COLUMN `".$this->pass_status."` TINYINT(1) UNSIGNED NULL DEFAULT '0'";
        $res = $this->old_db->query($sql);
//        if($res){
//             $sql = "UPDATE `".$this->o_table."` SET ".$this->pass_status." = '0'";
//             $res = $this->old_db->query($sql);
//        }
        return $res;
    }

    public function addOldFiled(){
        $sql = "ALTER TABLE `".$this->n_table."` ADD COLUMN `".$this->oldsys_id."` TINYINT(1) UNSIGNED NULL DEFAULT '0'";
        $res = $this->new_db->query($sql);
        return $res;
    }

    public function  __set ( $name ,  $value ) 
    {
        if($name == 'oldsys_id' || $name == 'pass_status'){
            $this->$name=$value ;
        }  
    }


    
}

