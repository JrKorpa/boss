<?php
/**
 *  -------------------------------------------------
 * 文件说明     数据导入类
 * @file        : ExportData2.class.php
 * @author      : yangxt <yangxiaotong@163.com>
 * @date        : 2015-01-22 10:07:27
 * @version     : 1.2
 *  -------------------------------------------------
 */
date_default_timezone_set('Asia/Shanghai');
class ExportData
{

    protected $old_db;
    protected $new_db;
    protected $n_table;     //新表
    protected $o_table;     //旧表
    protected $_is_pass;    //是否记录导出状态
    protected $_is_old;     //是否保留旧系统主键

    protected $param = [
        'oldsys_id'=>'oldsys_id',       //保留旧主键字段
        'pass_status'=>'pass_status',   //记录导出状态字段
        'start'=>'0',                   //脚本起始值
        'len'=>'0',                     //运行步长
        'filter'=>array(),              //过滤字段
        'dict'=>array(),                //修改数据字典
        'default'=>array()             //设置默认值
    ];

    protected $data = [
        'old_data'=>array(),
        'old_pk'=>'',
        'new_pk'=>''
    ];

    function __construct($new_conf,$old_conf,$n_table,$o_table,$pass = true,$old_id)
    {
        date_default_timezone_set('Asia/Shanghai');
        $this->old_db = new PDO($old_conf['dsn'], $old_conf['user'], $old_conf['password'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
        $this->new_db = new PDO($new_conf['dsn'], $new_conf['user'], $new_conf['password'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
        $this->n_table = $n_table;
        $this->o_table = $o_table;
        $this->_is_pass = $pass;
        $this->_is_old = $old_id;

        $this->getOldPk();$this->getNewPk();
    }


    public function __set($name,$value){
        if(array_key_exists($name, $this->param)){
            $this->param[$name] = $value;
        }else{
            return false;
        }
    }

    public function __get($value)
    {
        if(array_key_exists($value,$this->data)){
            return  $this->data[$value];
        }else{
            return false;
        }
    }

    public function getOldPk()
    {
        $sql = "desc `".$this->o_table."`";
        $obj = $this->old_db->query($sql);
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $data = $obj->fetchAll();
        $pk = false;
        foreach ($data as $v) {
            if($v['Key'] == 'PRI'){
                $pk = $v['Field'];
            }
        }
        $this->data['old_pk'] = $pk;
    }

    public function getNewPk()
    {
        $sql = "desc `".$this->n_table."`";
        $obj = $this->new_db->query($sql);
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $data = $obj->fetchAll();
        $pk = false;
        foreach ($data as $v) {
            if($v['Key'] == 'PRI'){
                $pk = $v['Field'];
            }
        }
        $this->data['new_pk'] = $pk;
    }

    public function getOldfields(){

        $sql = "desc `".$this->o_table."`";
        $obj = $this->old_db->query($sql);
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $data = $obj->fetchAll();
        foreach ($data as $v) {
            $fields[] = $v['Field'];
        }
        return $fields;
    }

    /**
     * 获取旧数据
     * @param bool $where       查询条件
     * @param bool $pass        是否提出主键
     * @param bool $table2      查询关联表
     * @param array $on         on条件
     * @param array $field_arr  关联字段
     */
    public function getOldDate($where=false,$pass=false,$table2=false,$on = array(),$field_arr = array())
    {

        $fields = $this->getOldfields();

        if($pass !== false){
            $sel = "m.`".$this->data['old_pk']."` AS oldsys_id,";
        }else{
            $sel = '';
        }
        if(!empty($this->param['filter'])){
            $filter = $this->param['filter'];
            foreach ($filter as $k => $v) {
                $sel .= "m.`".$k."` AS ".$v.",";
            }
        }else{
            foreach ($fields as $v) {
                $sel .= "m.`".$v."`,";
            }
        }
        if(!empty($on) && $table2){
            if(!empty($field_arr)){
                foreach ($field_arr as $k=>$v) {
                    $sel .= "p.`".$k."` AS ".$v.",";
                }
            }
            $sel = rtrim($sel,',');
            $sql = "SELECT ".$sel." FROM ".$this->o_table." AS `m` LEFT JOIN `".$table2."` AS `p` ON (`m`.$on[0] = `p`.$on[1])";
        }else{
            $sel = rtrim($sel,',');
            $sql = "SELECT ".$sel." FROM ".$this->o_table." AS `m` WHERE 1=1";
        }
        if($this->_is_pass){
            $sql .= " AND (`m`.`".$this->param['pass_status']."` = '0' or `m`.`".$this->param['pass_status']."` IS NULL)";
        }

        if($where !== false){
            $sql .= " AND ".$where." ORDER BY ".$this->data['old_pk']." DESC";
        }
        if($this->param['len'] != '0'){
            $sql .= " limit ".$this->param['start'].",".$this->param['len'];
        }
        // print_r($sql);exit;
        $obj = $this->old_db->query($sql);
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $this->data['old_data'] = $obj->fetchAll();
    }


    public function insertData()
    {
        if(empty($this->data['old_data'])){
            echo iconv('UTF-8', 'GBK', 'NO DATA TO EXPORT');
            exit;
        }
        $old_data = $this->data['old_data'];
        // print_r($old_data[0]);exit;
        //================数据处理======================
        foreach ($old_data as $key => $row) {
            foreach ($row as $k => $v) {
                //处理数据字典
                if(!empty($this->param['dict'])){
                    foreach ($this->param['dict'] as $d_k => $d_v) {
                        if($k == $d_k){
                            if(!$old_data[$key][$d_k]){
                                $old_data[$key][$d_k] = ($d_v[' '])?$d_v[' ']:$d_v['0'];
                            }else{
                                $old_data[$key][$d_k] = $d_v[$old_data[$key][$k]];
                            }
                        }
                    }
                }
            }
            if(isset($data[$key]['pass_status'])){
                unset($data[$key]['pass_status']);
            }
        }
        // print_r($old_data[0]);exit;
        //================设置默认值====================
        if(!empty($this->param['default'])){
            foreach ($old_data as $key => $row) {
                foreach ($this->param['default'] as $k_d => $v) {
                    $old_data[$key][$k_d] = $v;
                }
            }
        }
        // print_r($old_data[0]);exit;
        //=================预处理=======================
        $label = " (";$value = " (";
        foreach ($old_data[0] as $k => $v) {
            //不记录`oldsys_id`
            if(!$this->_is_old  && $k == 'oldsys_id'){
                continue;
            }else{
                $label .= "`".$k."`,";
                $value .= ":".$k.",";
            }
        }

        $label = rtrim($label,',');$label .= ") ";
        $value = rtrim($value, ',');$value .= ")";



        $sql = "INSERT INTO `".$this->n_table."`".$label."VALUES ".$value."";
        $sql_1 = "INSERT INTO `".$this->n_table."`".$label."VALUES ";
        $new_db = $this->new_db;
        $stmt = $new_db->prepare($sql);
        // print_r($sql);exit;
        //=================绑定参数=====================
        foreach ($old_data[0] as $key => $v_bin) {
            if(!$this->_is_old  && $key == 'oldsys_id'){
                continue;
            }else{
                $stmt->bindParam(':'.$key.'', $$key, PDO::PARAM_STR);
            }
        }
        $res = false;
        // print_r($old_data[0]);exit;
        $s = 0; $l = 0;
        //=================绑定值======================= 
        foreach ($old_data as $row) {
            // $i = 0;
            $sql_2 = " (";
            foreach ($row as $k => $v) {
                if(!$this->_is_old && $k == 'oldsys_id'){
                    continue;
                }else{
                    // $i++;
                    $$k = $v;
                    $sql_2 .= "'".$v."',";
                }
            }
            // echo $i;exit;
            $sql_2 = rtrim($sql_2,',');$sql_2 .= ")";
            $sql_test = $sql_1." ".$sql_2;
//                 print_r($sql_test);exit;
            //=================插入数据=====================
            $res = $stmt->execute();
            // $stmt->debugDumpParams();
            // var_dump($res);var_dump($this->_is_pass);exit;
            //回写状态
            if($res){
                $i++;
                if($this->_is_pass){
                    $sql = "UPDATE `".$this->o_table."` SET `".$this->param['pass_status']."` = '1' WHERE `".$this->data['old_pk']."` = '".$row['oldsys_id']."'";
                    // print_r($sql);exit;
                    $res = $this->old_db->query($sql);
                    if(!$res){
                        echo iconv('UTF-8', 'GBK', "LOSE UPDATE ".$this->o_table." ".$row['oldsys_id']."\r\n");
                        file_put_contents(__DIR__."/log/update/".date('Ymd_Hi')."_sql.log",$sql."\r\n",FILE_APPEND);
                    }else{
                        echo iconv('UTF-8', 'GBK', "SUCCESS UPDATE ".$this->o_table." ".$row['oldsys_id']."\r\n");
                    }
                }
                echo iconv('UTF-8', 'GBK', "SUCCESS INSERT ".$row['oldsys_id']." TO ".$this->n_table."\r\n");
            }else{
                $s++;
                echo iconv('UTF-8', 'GBK', "LOSE INSERT ".$row['oldsys_id']." TO ".$this->n_table."\r\n");
                file_put_contents(__DIR__."/log/insert/".date('Ymd_Hi')."_sql.log",$sql_test."\r\n",FILE_APPEND);
            }

        }

        file_put_contents(__DIR__."/log/insert/".date('Ymd_Hi')."_sql.log",$sql_test."\r\n---===THE MISSION END===---\r\n||||||||||||||||||||||||||||||||||||||||||||||\r\n||||||||||||||||||||||||||||||||||||||||||||||\r\n||||||||||||||||||||||||||||||||||||||||||||||\r\n\r\n\r\n",FILE_APPEND);
        //脚本执行完成
        echo iconv('UTF-8', 'GBK', "---===THE MISSION END===---\r\n[".$this->n_table."]");
        echo iconv('UTF-8', 'GBK', "SUCCESS INSERT ".$i." LINE \r\n[".$this->n_table."]");
        echo iconv('UTF-8', 'GBK', "LOSE INSERT ".$s." LINE \r\n[".$this->n_table."]");


    }


    /*
    *  $field 更新字段
    *  $label 条件字段
    */
    public function updateNewItem($field,$label,$table,$len = 10000){
        $sql = "SELECT `".$this->data['new_pk']."`,`".$label."` FROM `".$this->n_table."` WHERE `".$field."` IS NULL LIMIT 0,".$len;
        $obj = $this->new_db->query($sql);
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $data = $obj->fetchAll();
        if(empty($data)){
            echo iconv('UTF-8', 'GBK', 'NO DATA TO EXPORT');
            exit;
        }

        foreach ($data as $row) {
            $sql = "SELECT `".$field."` FROM `".$table."` WHERE `".$label."` = '".$row[$label]."'";
            $res = $this->new_db->query($sql)->fetch();
            if($res){
                $value = $res[$field];
                $sql = "UPDATE `".$this->n_table."` SET `".$field."` = '".$value."' WHERE `".$this->data['new_pk']."` = '".$row[$this->data['new_pk']]."'";
                $res = $this->new_db->query($sql);
                if($res){
                    echo iconv('UTF-8', 'GBK', "SUCCESS UPDATE ".$field." TO ".$this->n_table." ".$row[$this->data['new_pk']]."\r\n");
                }else{
                    echo iconv('UTF-8', 'GBK', "LOSE UPDATE ".$field." TO ".$this->n_table." ".$row[$this->data['new_pk']]."\r\n");
                }
            }
        }
        echo iconv('UTF-8', 'GBK', "---===THE MISSION END===---\r\n[".$this->n_table."]");
    }




    public function insert2Warehouse(){
        $sql = "SELECT `wh_id`,`wh_name`,`wh_sn`,`type` FROM `jxc_warehouse` WHERE `wh_id` NOT IN (SELECT `id` FROM `warehouse`)";

        $obj = $this->new_db->query($sql);
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $data = $obj->fetchAll();

        if(!empty($data)){
            $ss=0;$ll=0;
            foreach ($data as $rows) {
                $time = date('Y-m-d H:i:s');
                $sql = "INSERT INTO `warehouse` (`id`,`name`,`code`,`create_time`,`create_user`,`is_delete`,`lock`,`type`,`diamond_warehouse`) VALUES ('{$rows['wh_id']}','{$rows['wh_name']}','{$rows['wh_sn']}','{$time}','system','1','0','{$rows['type']}','0')";
                //print_r($sql);exit;
                $res = $this->new_db->query($sql);
                if($res){
                    $ss++;
                    echo iconv('UTF-8', 'GBK', "SUCCESS INSERT ".$ss." TO warehouse ".$rows['wh_name']."\r\n");
                }else{
                    $ll++;
                    echo iconv('UTF-8', 'GBK', "LOSE INSERT ".$ll." TO warehouse ".$rows['wh_name']."\r\n");
                    file_put_contents(__DIR__."/log/insert/WAREHOUSE_".date('Ymd').".log",$sql."\r\n",FILE_APPEND);
                }
            }
            //脚本执行完成
            echo iconv('UTF-8', 'GBK', "---===THE MISSION END===---");
            echo iconv('UTF-8', 'GBK', "SUCCESS INSERT ".$ss." LINE \r\n");
            echo iconv('UTF-8', 'GBK', "LOSE INSERT ".$ll." LINE \r\n");
        }else{
            echo iconv('UTF-8', 'GBK', 'NO DATA TO EXPORT');
            exit;
        }

    }


}

