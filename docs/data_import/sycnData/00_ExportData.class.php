<?php
/**
 *  -------------------------------------------------
 * 文件说明     数据导入类
 * @author      : yangxt <yangxiaotong@163.com>
 * @version     : 2.1
 *  -------------------------------------------------
 */
class ExportData
{

    protected $old_db;
    protected $new_db;
    protected $n_table;     //新表
    protected $o_table;     //旧表
    protected $n_fields;    //新表字段
    protected $o_fields;    //旧表字段     
    protected $csv_file;

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

    /*自动插入*/
    public function autoExec($data,$table,$act='INSERT',$where=array(),$db = 'new'){
        $_fields = $this->n_fields;

        foreach ($_fields as $v) {
            if(array_key_exists($v,$data)){
                $param[$v] = ":".$v;
            }
        }
        if($act == 'INSERT'){
            $sql = "INSERT INTO `".$table."` (".implode(',',array_flip($param)).") VALUES (".implode(',',$param).")";
        }
        if(($act == 'UPDATE') && !empty($where)){
            $set = '';
            $i=0;
            foreach ($param as $k => $v) {
                $i++;
                $set .= "`".$k."` = ".$v.",";
            }
            $set = rtrim($set,',');
            $_where = '';
            foreach ($where as $k=>$v) {
                $i++;
                $_where .= "`".$k."` = :".$k." AND ";
            }
            $_where = rtrim($_where,' AND ');
            $sql = "UPDATE `".$table."` SET ".$set." WHERE ".$_where;

            $data = array_merge($data,$where);
        }

        if($db == 'new'){
            $db = $this->new_db;
        }else{
            $db = $this->old_db;
        }
        $_res = $db->prepare($sql);
        try{
            $db->beginTransaction();//开启事务处理
            $_res->execute($data);
            //$_res->debugDumpParams();exit;
        }catch(PDOException $e){
            $db->rollback();
            $db->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
            return false;
        }
        $db->commit();
        $db->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
        return true;

    }

    /*获取旧项目数据*/
    public function getOldData()
    {
        $sel = '';
        $sel1 = $this->getSelFields();
        $sel2 = $this->replaceFields();
        if($sel1 && !$sel2){$sel.=$sel1;}
        if($sel2 && !$sel1){$sel.=$sel2;}
        if($sel1 && $sel2){$sel .= $sel2.','.$sel1;}
        $sql = "SELECT ";
        $sql .= $sel;
        $sql .= " ,p.p_name AS company,j.wh_name AS warehouse FROM `jxc_goods`AS m,`jxc_processors` AS `p`,`jxc_warehouse` AS j,`tmp_1diff` AS d";
        $sql .= " WHERE `m`.`goods_id` = `d`.`old_gid` AND `m`.`company` = `p`.`p_id` AND `m`.`warehouse` = `j`.`wh_id`";
        //print_r($sql);exit;
        $obj = $this->old_db->query($sql);
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $this->data['old_data'] = $obj->fetchAll();
    }

    public function getNewData()
    {
        $sel = "company_id AS company,warehouse_id AS warehouse,is_on_sale";
        $sql = "SELECT w.goods_id,".$sel." FROM `warehouse_goods` AS w,`tmp_goods` AS t WHERE w.goods_id = t.goods_id";
        $obj = $this->new_db->query($sql);
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $data = $obj->fetchAll();
        return $data;
    }


    /*旧项目临时表*/
    public function create2Tmp()
    {
        $base_path = __DIR__."/goods_log/";
        if(is_file($base_path.'time.txt')){
            $fn = $base_path.'time.txt';
            $fp = fopen($fn, 'r');
            fseek($fp, -1, SEEK_END);
            $time = '';
            while(($c = fgetc($fp)) !== false) {
                if($c == "\n" && $time) break;
                $time = $c . $time;
                fseek($fp, -2, SEEK_CUR);
            }
            fclose($fp);
        }else{
            $time = '2015-04-25 00:00:00';
        }
        //print_r($time);exit;
        $base_name = date('Ymd_His')."_";
        file_put_contents($base_path.'time.txt',date('Y-m-d H:i:s')."\n",FILE_APPEND);

            //临时表 tmp1
                $sql = "DROP TABLE IF EXISTS `tmp1`";
                $res = $this->old_db->query($sql);
                if($res){
                    $sql = "CREATE TABLE tmp1 as SELECT g.goods_id as old_gid FROM jxc_order AS o, jxc_order_goods AS og,jxc_goods as g WHERE o.order_id = og.order_id AND og.goods_id = g.goods_id AND o.status = '2' AND o.type <>'W' AND o.addtime >= '".$time."' GROUP BY g.goods_id";
                    $r1 = $this->old_db->query($sql);
                    if(!$r1){echo "tmp1 IS NOT TO CREATE";exit;}
                }
            //临时表 tmp2
                $sql = "DROP TABLE IF EXISTS `tmp2`";
                $res = $this->old_db->query($sql);
                if($res){
                    $sql = "CREATE TABLE `tmp2` (`new_gid` bigint(30) NOT NULL) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
                    $r2 = $this->old_db->query($sql);
                }else{
                    echo "tmp2 IS NOT TO DROP";exit;
                }

                if($r2){
                    $sql = "SELECT bg.goods_id FROM `warehouse_bill_goods` AS bg,`warehouse_bill` AS b WHERE bg.bill_id = b.id AND bg.`bill_type`<>'W' AND b.bill_status ='2' AND b.create_time >= '".$time."' GROUP BY bg.`goods_id`";
                    $obj = $this->new_db->query($sql);
                    $obj->setFetchMode(PDO::FETCH_ASSOC);
                    $data = $obj->fetchAll();
                    if(!empty($data)){
                        $sql = "INSERT INTO `tmp2` VALUES ";
                        foreach ($data as $row) {
                            $sql .= "('".$row['goods_id']."'),";
                        }
                        $sql = rtrim($sql,',');$sql .=";";
                    }
                    //print_r($sql);exit;
                    $r2 = $this->old_db->query($sql);
                }else{
                    echo "tmp2 IS NOT TO CREATE";exit;
                }
            //交集表 tmp_intersect
                if($r1 && $r2){
                    $sql = "DROP TABLE IF EXISTS `tmp_intersect`";
                    $res = $this->old_db->query($sql);
                    if($res){
                        $sql = "CREATE TABLE `tmp_intersect` AS SELECT old_gid AS goods_id FROM tmp1 WHERE EXISTS (SELECT * FROM tmp2 WHERE tmp1.old_gid=tmp2.new_gid)";
                        $this->old_db->query($sql);
                    }else{
                        echo "tmp_intersect NOT TO CREATE";exit;
                    }

                }else{
                    echo "E_ERROR TABEL tmp1,tmp2";exit;
                }
            //临时表 tmp1_diff
                $sql = "DROP TABLE IF EXISTS `tmp_1diff`";
                $this->old_db->query($sql);
                $sql = "CREATE TABLE `tmp_1diff` AS SELECT old_gid FROM tmp1 WHERE NOT EXISTS (SELECT * FROM tmp2 WHERE tmp1.old_gid=tmp2.new_gid)";
                $this->old_db->query($sql);
            //临时表 tmp2_diff
                $sql = "DROP TABLE IF EXISTS `tmp_2diff`";
                $this->old_db->query($sql);
                $sql = "CREATE TABLE `tmp_2diff` AS SELECT new_gid FROM tmp2 WHERE NOT EXISTS (SELECT * FROM tmp1 WHERE tmp1.old_gid=tmp2.new_gid)";
                $this->old_db->query($sql);

        //生成文件
        $file = $base_path.$base_name."tmp_intersect.csv";
        //$sql = 'SELECT * FROM `tmp_intersect` into outfile "'.$file.'"';
        $sql = 'SELECT * FROM `tmp_intersect`';
        $obj = $this->old_db->query($sql,PDO::FETCH_ASSOC);
        $data = $obj->fetchAll();
        $res1 = $this->writeTmpFile($data,$file);

        $file = $base_path.$base_name."tmp_1diff.csv";
        //$sql = 'SELECT * FROM `tmp_1diff` into outfile "'.$file.'"';
        $sql = 'SELECT * FROM `tmp_1diff`';
        $obj = $this->old_db->query($sql,PDO::FETCH_ASSOC);
        $data = $obj->fetchAll();
        $res2 = $this->writeTmpFile($data,$file);

        //处理相同数据
        $res4 = $this->intersectGoods();
        if(!$res4){return false;}

        $file = $this->csv_file = $base_path.$base_name."tmp_2diff.csv";
        //$sql = 'SELECT * FROM `tmp_2diff` into outfile "'.$file.'"';
        $sql = 'SELECT * FROM `tmp_2diff`';
        $obj = $this->old_db->query($sql,PDO::FETCH_ASSOC);
        $data = $obj->fetchAll();
        $res3 = $this->writeTmpFile($data,$file);

        return ($res1 && $res2 && $res3)?true:false;

    }

    public function intersectGoods(){
        $sql = "SELECT `goods_id` FROM `tmp_intersect`";
        $obj = $this->old_db->query($sql);
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $ids = $obj->fetchAll();
        $o_ids = array();$n_ids = array();
        foreach ($ids as $k=>$g) {
            $sql = "SELECT max(o.checktime) AS o_time  FROM `jxc_order_goods` AS g,`jxc_order` AS o where o.order_id = g.order_id AND o.status = '2' AND g.goods_id = ".$g['goods_id'];
            $obj = $this->old_db->query($sql);
            $obj->setFetchMode(PDO::FETCH_ASSOC);
            $res = $obj->fetch();
            $o_time = $res['o_time'];

            $sql = "SELECT max(b.`check_time`) AS `n_time` FROM `warehouse_bill_goods` AS g,warehouse_bill AS b WHERE g.bill_id = b.id AND b.`bill_status` = 2 AND g.`goods_id` = ".$g['goods_id'];
            $obj = $this->new_db->query($sql);
            $obj->setFetchMode(PDO::FETCH_ASSOC);
            $res = $obj->fetch();
            $n_time = $res['n_time'];

            if($o_time > $n_time){
                $o_ids[] = $g['goods_id'];
            }else{
                $n_ids[]=$g['goods_id'];
            }
        }
        if(!empty($o_ids)){
            $sql = "INSERT INTO `tmp_1diff` VALUES ";
            foreach ($o_ids as $v) {
                $sql .= "(".$v."),";
            }
            $sql = rtrim($sql,',');
            $res1 = $this->old_db->query($sql);
    }

        if(!empty($n_ids)){
            $sql = "INSERT INTO `tmp_2diff` VALUES ";
            foreach ($n_ids as $v) {
                $sql .= "(".$v."),";
            }
            $sql = rtrim($sql,',');
            $res2 = $this->old_db->query($sql);
        }

        return ($res1 && $res2)?true:false;
    }

    /*新项目临时表*/
    public function createGoodsTmp($len = 0)
    {
        if(!is_file($this->csv_file)){return false;}
        //$this->csv_file = __DIR__."/goods_log/20150425_124157_tmp_2diff.csv";

        //读取CVS文件
        $spl_object = new SplFileObject($this->csv_file, 'rb');
        $spl_object->seek(filesize($this->csv_file));
        $start = 0;$len = ($len)?$len:$spl_object->key();
        $spl_object->seek($start);
        while ($len && !$spl_object->eof()) {
            $data[] = $spl_object->fgetcsv();
            $spl_object->next();
        }
        $data = array_column($data,0);
        if(!end($data)){array_pop($data);}

        //临时表
        $sql = "DROP TABLE IF EXISTS `tmp_goods`";
        $this->new_db->query($sql);
        $sql = "CREATE TABLE `tmp_goods` (`goods_id` bigint(30) NOT NULL) ENGINE=MyISAM  DEFAULT CHARSET=utf8";
        $res = $this->new_db->query($sql);
        if(!$res){
            echo "tmp_goods IS NOT TO CREATE";exit;
        }
        if(!empty($data)){
            $sql = "INSERT INTO `tmp_goods` VALUES ";
            foreach ($data as $g) {
                $sql .= "('".$g."'),";
            }
            $sql = rtrim($sql,',');$sql .=";";
            return $res = $this->new_db->query($sql);
        }else{
            return false;
        }
    }

    /*写入数据至新项目*/
    public function insertData()
    {

        if(empty($this->data['old_data'])){
            echo iconv('UTF-8', 'GBK', 'NO DATA TO EXPORT');
            exit;
        }
        $old_data = $this->data['old_data'];
        //print_r($old_data[0]);exit;
        //================数据处理======================
        foreach ($old_data as $key => $row) {
            foreach ($row as $k => $v) {
                //处理数据字典
                if(!empty($this->param['dict'])){
                    foreach ($this->param['dict'] as $d_k => $d_v) {
                        if($k == $d_k){
                            //print_r($old_data[$key][$d_k]);echo "\r\n";print_r($d_v[$old_data[$key][$k]]);echo "\r\n-----\r\n";
                            $old_data[$key][$d_k] = $d_v[$old_data[$key][$k]];
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
            }
        }
        //print_r($old_data[0]);exit;
        $u = 0; $i = 0;
        foreach ($old_data as $key=>$rows) {
            $goods_id = $rows['goods_id'];
            $sql = "SELECT COUNT(*) AS has FROM `warehouse_goods` WHERE `goods_id` = '".$goods_id."'";

            $obj = $this->new_db->query($sql);
            $obj->setFetchMode(PDO::FETCH_ASSOC);
            $res = $obj->fetch();

            //print_r($res);exit;
            if($res['has'] > 0){//UPDATE
                $where = ['goods_id'=>$rows['goods_id']];
                unset($rows['goods_id']);
                $res = $this->autoExec($rows,'warehouse_goods','UPDATE',$where);
                if($res){
                    $u++;
                    echo iconv('UTF-8', 'GBK', "SUCCESS UPDATE ".$where['goods_id']." TO [".$this->n_table."]\r\n");
                }else{
                    echo iconv('UTF-8', 'GBK', "LOSE UPDATE TO [".$this->n_table."]\r\n");
                    file_put_contents(__DIR__."/log/update/LOSE_UPDATE_o2n".date('Ymd').".log",$where['goods_id']."\r\n",FILE_APPEND);
                }

            }else{//INSERT
                $res = $this->autoExec($rows,'warehouse_goods');
                if($res){
                    $i++;
                    echo iconv('UTF-8', 'GBK', "SUCCESS INSER ".$rows['goods_id']." TO [".$this->n_table."]\r\n");
                }else{
                    echo iconv('UTF-8', 'GBK', "LOSE INSER TO [".$this->n_table."]\r\n");
                    file_put_contents(__DIR__."/log/update/LOSE_INSERT_o2n ".date('Ymd').".log",$rows['goods_id']."\r\n",FILE_APPEND);
                }
            }

        }
        //file_put_contents(__DIR__."/log/insert/".date('Ymd_Hi')."_sql.log","\r\n\r\n---===THE MISSION END===---\r\n||||||||||||||||||||||||||||||||||||||||||||||\r\n||||||||||||||||||||||||||||||||||||||||||||||\r\n||||||||||||||||||||||||||||||||||||||||||||||\r\n\r\n\r\n",FILE_APPEND);
        echo iconv('UTF-8', 'GBK', "---===THE MISSION END===---\r\n");
        echo iconv('UTF-8', 'GBK', " INSERT ".$i." LINE \r\n");
        echo iconv('UTF-8', 'GBK', " UPDATE ".$u." LINE \r\n");

    }
    /*写入数据至旧项目*/
    public function insertData2Old($data)
    {
        if(!isset($data[0]['goods_id'])){
            echo "DATA IS ERROR !";exit;
        }
        //[old]0=初始化，1=库存，2=已销售，3=转仓中，4=盘点中，5=销售中，6=冻结，7=已返厂,8=退货中，9=返厂中, 10=作废, 11=损益中,12=已报损
        $dict = [
            'is_on_sale'=>[
                '1'=>'0','2'=>'1','3'=>'2','4'=>'4','5'=>'3','6'=>'11','7'=>'12','8'=>'9','9'=>'7','10'=>'5','11'=>'9','12'=>'10'
            ]
        ];
        //处理数据字典
        foreach ($data as $key=>$row) {
            foreach ($dict as $lab=>$val) {
                if(array_key_exists($lab,$row)){
                    $data[$key][$lab] = $val[$data[$key][$lab]];
                }
            }
        }
        $i = 0;
        foreach ($data as $row) {
            $where = ['goods_id'=>array_shift($row)];
            $res = $this->autoExec($row,'jxc_goods','UPDATE',$where,'old');
            if($res){
                $i++;
                echo iconv('UTF-8', 'GBK', "SUCCESS UPDATE ".$where['goods_id']." TO [".$this->o_table."] ".$i." line\r\n");
            }else{
                echo iconv('UTF-8', 'GBK', "LOSE INSER TO [".$this->o_table."]\r\n");
                file_put_contents(__DIR__."/log/update/LOSE_UPDATE_n2o".date('Ymd').".log",$where['goods_id']."\r\n",FILE_APPEND);
            }
        }
        echo iconv('UTF-8', 'GBK', "---===THE MISSION END===---".$i."\r\n");
    }

    public function writeTmpFile($data,$file){
        if( empty($data) ||!is_array($data[0])){return false;}
        $str = '';$res = false;
        foreach ($data as $rows) {
            foreach ($rows as $v) {
                $str .= $v.',';
            }
            $str = rtrim($str,',');
            $str .= "\r\n";
        }
        $str = rtrim($str,"\r\n");
        if($str){
            $res = file_put_contents($file,$str);
        }
        return $res;
    }

}

