<?php 
/**
 *  -------------------------------------------------
 * 文件说明     数据导入类
 * @file        : ExportData2.class.php
 * @author      : yangxt <yangxiaotong@163.com>
 * @date        : 2015-03-16 10:07:27
 * @version     : 2.1
 *  -------------------------------------------------
*/
date_default_timezone_set('Asia/Shanghai');
class ExportData
{

    protected $old_db;
    protected $new_db;
    protected $n_table;     //新表
    protected $o_table;     //旧表
    protected $n_fields;    //新表字段
    protected $o_fields;    //旧表字段

    protected $_is_pass;    //是否记录导出状态
    protected $_is_old;     //是否保留旧系统主键

    protected $param = [
        'oldsys_id'=>'oldsys_id',       //保留旧主键字段
        'pass_status'=>'pass_status',   //记录导出状态字段
        'start'=>'0',                   //脚本起始值
        'len'=>'0',                     //运行步长
        'filter'=>array(),              //过滤字段
        'dict'=>array(),                //修改数据字典
        'default'=>array(),             //设置默认值
        'cannelField'=>array(),         //去除不用字段
        'def_decimal'=>array()
    ];

    protected $data = [
        'old_data'=>array(),
        'old_pk'=>'',
        'new_pk'=>''
    ];

    function __construct($new_conf,$old_conf,$n_table,$o_table,$pass = true,$old_id = false)
    {
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
        $sql = "DESCRIBE `".$this->o_table."`";
        $obj = $this->old_db->query($sql);
        // print($this->old_db);
        // exit;
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $data = $obj->fetchAll();
        $pk = false;
        foreach ($data as $v) {
            if($v['Key'] == 'PRI'){
                $pk = $v['Field'];
            }
            $this->o_fields[] = $v['Field'];
        }
        $this->data['old_pk'] = $pk;
    }

    public function getNewPk()
    {
        $sql = "DESCRIBE `".$this->n_table."`";
        $obj = $this->new_db->query($sql);
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $data = $obj->fetchAll();
        $pk = false;
        foreach ($data as $v) {
            if($v['Key'] == 'PRI'){
                $pk = $v['Field'];
            }
            $this->n_fields[] = $v['Field'];
        }
        $this->data['new_pk'] = $pk;
    }

    /*获取相同字段*/
    public function getSamefields(){
        $r = array_intersect($this->n_fields,$this->o_fields);
        return $r;
    }
    /*获取不同字段*/
    public function getDiffFields()
    {
        $d = array_diff($this->o_fields,$this->n_fields);
        return $d;
    }
    //选取不同字段
    public function replaceFields(){
        $r_sel = '';
        $arr = $this->param['filter'];
        foreach ($arr as $k => $v) {
            $r_sel .= "`m`.`".$k."` AS `".$v."`,";
        }
        return rtrim($r_sel,',');
    }
    //选取相同字段
    public function getSelFields(){
        $sel = '';
        $arr = $this->cannelField();
        if(empty($arr)){
            return 'no fielld';
        }else{
            foreach ($arr as $v) {
                $sel .= "`m`.`".$v."`,";
            }
        }
        return rtrim($sel,',');
    }

    /*去除不用的字段*/
    public function cannelField()
    {
        $arr = array_unique($this->param['cannelField']);
        $all = array_flip($this->getSamefields());
        if(!empty($arr)){
            foreach ($arr as $v) {
                if(array_key_exists($v,$all)){
                    unset($all[$v]);
                }
            }
        }
        $all = array_flip($all);
        return $all;
    }

    /**
     * 获取旧数据
     * @param bool $where   查询条件
     * @param bool $pass    是否提出主键
     * @param array $table1 查询关联表
     * @param array $table2 查询关联表
     */
    public function getOldDate($where=false,$pass=false,$table1 = array(),$table2 = array())
    {
        
        if($pass !== false){
            $sel = "m.`".$this->data['old_pk']."` AS oldsys_id,";
        }else{
            $sel = '';
        }
        $sel1 = $this->getSelFields();
        $sel2 = $this->replaceFields();
        if($sel1 && !$sel2){$sel.=$sel1;}
        if($sel2 && !$sel1){$sel.=$sel2;} 
        if($sel1 && $sel2){$sel .= $sel2.','.$sel1;} 

        if(!empty($table1) && empty($table2)){
            if(!empty($table1['field'])){
                foreach ($table1['field'] as $k=>$v) {
                    $sel .= ",".$table1['as'].".`".$k."` AS ".$v;
                }
            }
            $sql = "SELECT ".$sel." FROM ".$this->o_table." AS `m`,`".$table1['name']."` AS `".$table1['as']."`";
            $sql .= " WHERE `m`.`".$table1['on'][0]."` = `".$table1['as']."`.`".$table1['on'][1]."`";
        }elseif (!empty($table2) && !empty($table1)) {
            if(!empty($table1['field'])){
                foreach ($table1['field'] as $k=>$v) {
                    $sel .= ",".$table1['as'].".`".$k."` AS ".$v;
                }
            }
            if(!empty($table2['field'])){
                foreach ($table2['field'] as $k=>$v) {
                    $sel .= ",".$table2['as'].".`".$k."` AS ".$v;
                }
            }
            $sql = "SELECT ".$sel." FROM ".$this->o_table." AS `m`,`".$table1['name']."` AS `".$table1['as']."`,`".$table2['name']."` AS `".$table2['as']."`";
            $sql .= " WHERE `m`.`".$table1['on'][0]."` = `".$table1['as']."`.`".$table1['on'][1]."`";
            $sql .= " AND `m`.`".$table2['on'][0]."` = `".$table2['as']."`.`".$table2['on'][1]."`";
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
        //print_r($sql);exit;
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
//         print_r($old_data[0]);exit;
        //================数据处理======================
        foreach ($old_data as $key => $row) {
            foreach ($row as $k => $v) {
                //处理数据字典
                if(!empty($this->param['dict'])){
                    foreach ($this->param['dict'] as $d_k => $d_v) {
                        if($k == $d_k){
                            if($old_data[$key][$d_k] == ' ' || $old_data[$key][$d_k] == ''){
                                $old_data[$key][$d_k] = $d_v['0'];
                            }else{
                               $old_data[$key][$k] = $d_v[$old_data[$key][$k]]; 
                            }
                        }
                    }
                }
                //处理金额
                if(!empty($this->param['def_decimal'])){
                    if(in_array($k, $this->param['def_decimal'])){
                        if(!$old_data[$key][$k]){
                            $old_data[$key][$k] = '0';
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
                    if(isset($row['box_sn']) && empty($row['box_sn'])){
                        $old_data[$key]['box_sn'] = '0-00-0-0';
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
//记录所有货号
//file_put_contents(__DIR__."/log/insert/0_ALL_goods_id_".date('Ymd').".log",'goods_id = '.$row['goods_id']."\r\n",FILE_APPEND);
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
                    $s++;
                    if($this->_is_pass){
                        $sql = "UPDATE `".$this->o_table."` SET `".$this->param['pass_status']."` = '1' WHERE `".$this->data['old_pk']."` = '".$row['oldsys_id']."'";
                        // print_r($sql);exit;
                        $res = $this->old_db->query($sql);
                        if(!$res){
                            echo iconv('UTF-8', 'GBK', "LOSE UPDATE ".$this->o_table." ".$row['oldsys_id']."\r\n");
                            file_put_contents(__DIR__."/log/update/LOSE_".date('Ymd_H')."_sql.log",$sql."\r\n",FILE_APPEND);
                        }else{
                            echo iconv('UTF-8', 'GBK', "SUCCESS UPDATE ".$this->o_table." ".$row['oldsys_id']."\r\n");
                            //file_put_contents(__DIR__."/log/update/SUCCESS_".date('Ymd_H')."_sql.log",$sql."\r\n",FILE_APPEND);
                        }
                    }
                    echo iconv('UTF-8', 'GBK', "SUCCESS INSERT ".$s." TO ".$this->n_table."<br/>\r\n");
                    //file_put_contents(__DIR__."/log/insert/SUCCESS_".date('Ymd_H')."_sql.log",$sql_test."\r\n",FILE_APPEND);
//记录所有旧货号
//file_put_contents(__DIR__."/log/insert/0_SUCCESS_goods_id_".date('Ymd').".log",'goods_id = '.$row['goods_id']."\r\n",FILE_APPEND);
                }else{
                    $l++;
                    echo iconv('UTF-8', 'GBK', "LOSE INSERT ".$l." TO ".$this->n_table."<br/>\r\n");
                    file_put_contents(__DIR__."/log/insert/LOSE_".date('Ymd_H')."_sql.log",$sql_test."\r\n",FILE_APPEND);
                }

            }
    
        //file_put_contents(__DIR__."/log/insert/".date('Ymd_Hi')."_sql.log","\r\n\r\n---===THE MISSION END===---\r\n||||||||||||||||||||||||||||||||||||||||||||||\r\n||||||||||||||||||||||||||||||||||||||||||||||\r\n||||||||||||||||||||||||||||||||||||||||||||||\r\n\r\n\r\n",FILE_APPEND);
            //脚本执行完成
        echo iconv('UTF-8', 'GBK', "---===THE MISSION END===---<br/>\r\n[".$this->n_table."]");
        echo iconv('UTF-8', 'GBK', "SUCCESS INSERT ".$s." LINE <br/>\r\n[".$this->n_table."]");
        echo iconv('UTF-8', 'GBK', "LOSE INSERT ".$l." LINE <br/>\r\n[".$this->n_table."]");

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
            // print_r($sql);exit;
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
            }else{
                continue;
            }
        }
        echo iconv('UTF-8', 'GBK', "---===THE MISSION END===---\r\n[".$this->n_table."]");
    }



    public function insertRecordSupplier()
    {
        $sql = "SELECT `id` AS info_id,`name`,`code`,`is_open`,`balance_type`,`account`,`tax_invoice`,`info` FROM `app_processor_info`";
        $obj = $this->new_db->query($sql);
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $data = $obj->fetchAll();

        // print_r($data[0]);exit;

        $label = " (";$value = " (";
        foreach ($data[0] as $k=>$v) {
            $label .= "`".$k."`,";
            $value .= ":".$k.",";
        }
        $label = rtrim($label,',');$label .= ") ";
        $value = rtrim($value, ',');$value .= ")";

        $sql = "INSERT INTO `app_processor_record`".$label."VALUES ".$value."";
        $sql_1 = "INSERT INTO `app_processor_record`".$label."VALUES ";

        $stmt = $this->new_db->prepare($sql);

        foreach ($data[0] as $key => $v) {
            $stmt->bindParam(':'.$key.'', $$key, PDO::PARAM_STR);
        }
        $i = 0;
        foreach ($data as $row) {
            $sql_2 = " (";
            foreach ($row as $k => $v) {
                $$k = $v;
                $sql_2 .= "'".$v."',";
            }
            $sql_2 = rtrim($sql_2,',');$sql_2 .= ")";
            $sql_test = $sql_1." ".$sql_2;
//            print_r($sql_test);exit;

            $res = $stmt->execute();
            if(!$res){
                echo iconv('UTF-8', 'GBK', "LOSE INSERT \r\n");
            }else{
                $i++;
                echo iconv('UTF-8', 'GBK', "SUCCESS INSERT ".$i."\r\n");
            }
        }
        echo iconv('UTF-8', 'GBK', "---===THE MISSION END===---\r\n");
    }

    
    public function getRepeatData()
    {
        $i = 0;$m = $i+1;
        $sql = "SELECT `goods_id` FROM `".$this->o_table."` WHERE `pass_status` = '1' ORDER BY `id` LIMIT ".$i.",".$m;
        // print($sql);exit;
        $obj = $this->old_db->query($sql,PDO::FETCH_ASSOC);
        // $obj->setFetchMode(PDO::FETCH_ASSOC);
        $res = $obj->fetch();
        print($res);exit;
        $goods_id = $res[0];
        if($goods_id){
            $sql = "SELECT `goods_id` FROM '".$this->n_table."' WHERE `goods_id` = '".$goods_id."'";
            $obj = $this->new_db->query($sql, PDO::FETCH_ASSOC);
            // $obj->setFetchMode(PDO::FETCH_ASSOC);
            $res = $obj->fetch();
            if(!$res){
                echo iconv('UTF-8', 'GBK', "LOSE GOODS_ID =".$goods_id."\r\n");
                file_put_contents(__DIR__."/log/insert/".date('repeat_Ymd')."_sql.log",$goods_id."\r\n",FILE_APPEND);
            }else{
                echo iconv('UTF-8', 'GBK', "SUCCESS GOODS_ID =".$goods_id."\r\n");
            }
            $i += 1;
            $this->getRepeatData();
        }else{
            echo iconv('UTF-8', 'GBK', "---===THE MISSION END===---");
        }

    }


    public function updateCategroy($data)
    {
        if(empty($data)){
            echo iconv('UTF-8', 'GBK', 'NO DATA TO EXPORT');
            exit;
        }
        $old_data = $data;
        // print_r($old_data[0]);exit;
        //================数据处理======================
        foreach ($old_data as $key => $row) {
            foreach ($row as $k => $v) {
                //处理数据字典
                if(!empty($this->param['dict'])){
                    foreach ($this->param['dict'] as $d_k => $d_v) {
                        if($k == $d_k){
                            if($d_k){
                                // $old_data[$key][$d_k] = $d_v[$old_data[$key][$k]];
                                $row[$d_k] = $d_v[$old_data[$key][$k]];
                            }else{
                                // $old_data[$key][$d_k] = '8';
                                $row[$d_k] = '8';
                            }

                        }
                    }
                }
            }
            /*-------------*/
            // print_r($row);exit;
            $sql = "UPDATE `base_salepolicy_goods` SET `category` = '".$row['category']."',`cate_g` = '1' WHERE `goods_id` = '".$row['goods_id']."' AND `cate_g` = '0'"; 
//            print($sql);exit;
            $res = $this->new_db->query($sql);
            if(!$res){
                echo iconv('UTF-8', 'GBK', "LOSE UPDATE goods_id =".$row['goods_id']."\r\n");
                file_put_contents(__DIR__."/log/insert/base_sale_goods_".date('Ymd').".log",$row['goods_id']."\r\n",FILE_APPEND);
            }else{
                echo iconv('UTF-8', 'GBK', "SUCCESS UPDATE goods_id=".$row['goods_id']."\r\n");
            }

        }
        echo iconv('UTF-8', 'GBK', "---===THE MISSION END===---");

    }
    
}

