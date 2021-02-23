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
class DealData
{
    protected $new_conf;//新配置
   
    protected $new_db;
    protected $old_conf;//旧配置
    protected $old_db;
    function __construct($new_conf,$old_conf)
    {
        $this->new_conf = $new_conf;
        $this->new_db = new PDO($this->new_conf['dsn'], $this->new_conf['user'], $this->new_conf['password'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
        $this->old_conf = $old_conf;
        $this->old_db = new PDO($this->old_conf['dsn'], $this->old_conf['user'], $this->old_conf['password'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
        
       
    }
    //更改货品结价的信息
    public function ModifyGoodsJiejia() {
        var_dump($this->new_db);
         var_dump($this->old_db);
         echo '<br/>';
       $result = $this->new_db->exec("UPDATE `warehouse_goods` set `jiejia`=1 where goods_id='150311501634'");
       var_dump($result);
        
        exit;
        
        $old_data = array();
        $sql = "select `jo`.`order_id`,`jog`.`goods_id` from `jxc_order` as jo,`jxc_order_goods` as jog where jo.`type` in('L') and jo.`status`=2 and jo.`order_id`=jog.`order_id` and jo.`addtime`>='2015-01-01 00:00:00' and jog.goods_id='150311501634'";//   
		//echo $sql;exit;
        $result = $this->old_db->query($sql)->fetchAll();
        foreach($result as $key => $value) {
            $order_id = $value['order_id'];
            $goods_id = $value['goods_id']; 
            $sql2 = "select `account` from `jxc_goods` where `goods_id`='{$goods_id}'";
            $account = $this->old_db->query($sql2)->fetchColumn();
            if ($account == 1) $account = 0;
            if ($account == 2) $account = 1;
            $old_data[] = array('jiejia'=>$account,'goods_id'=>$goods_id);
        }
       // print_r($old_data);exit;
        //同步到新系统
        foreach ($old_data as $k => $v) {
            $jiejia = $v['jiejia'];
            $goods_id = $v['goods_id'];
            //验证新系统是否有此货号
            $sql = "select `goods_id` from `warehouse_goods` where `goods_id`='{$goods_id}'";
			//echo $sql.'<hr>';exit;
            $res = $this->new_db->query($sql)->fetchColumn();
            //var_dump($res);exit;
            if (!empty($res)){
              //  $sql = "UPDATE `warehouse_goods` set `jiejia`={$jiejia} where goods_id='$goods_id'";
                 $sql = "UPDATE `warehouse_goods` set `jiejia`=0 where goods_id='150311501634'";
                echo "sql:".$sql;
               
                $result = $this->new_db->exec("UPDATE `warehouse_goods` set `jiejia`=0 where goods_id='150311501634'");
                var_dump($result);
                
                if ($result) {
		    echo '执行成功！';
                    file_put_contents("./log.txt", "老系统更新到新系统货号：".$goods_id."执行成功\r\n",FILE_APPEND);
                }else {
                    file_put_contents("./log.txt", "老系统更新到新系统货号：".$goods_id."没有更新到;失败sql:".$sql."\r\n",FILE_APPEND);
                }
            }else {
		echo "新系统没有此货号:".$goods_id.'<br/>';
	    }
           
            
        }
       exit;
        //处理新系统的单子
        $sql = "SELECT `id`,`bill_no` from `warehouse_bill` where `bill_type` in ('L','T')";//收货单
        $res = $this->new_db->query($sql)->fetchAll();
       //var_dump($res);exit;
        foreach($res as $k => $v){
            $bill_id = $v['id'];
            $bill_no = $v['bill_no'];
            $sql2 = "select `goods_id` from `warehouse_bill_goods` where `bill_no`='{$bill_no}'";
            $goods_data = $this->new_db->query($sql2)->fetchAll();//获取所有更改结价的货品
            $sql3 = "select `jiejia` from `warehouse_bill_info_l` where `bill_id` = '{$bill_id}'";
            //echo $sql3.'<hr>';
            $jiejia = $this->new_db->query($sql3)->fetchColumn();//获取jiejia的值
            if (is_bool($jiejia) && $jiejia == FALSE) {
                $sql33 = "select `jiejia` from `warehouse_bill_info_t` where `bill_id` = '{$bill_id}'";
                $jiejia = $this->new_db->query($sql33)->fetchColumn();

            }
           
            foreach ($goods_data as $key => $val) {
                $goods_id = $val['goods_id'];
                $sql4 = "UPDATE `warehouse_goods` set `jiejia`={$jiejia} where `goods_id`='{$goods_id}'";
		//echo $sql4.'<hr>';
               
                if($this->result = $this->new_db->exec($sql4)) {
                     file_put_contents("./log.txt", "新系统货号：".$goods_id.'执行成功！<br/>',FILE_APPEND);
                }else {
                     file_put_contents("./log.txt", "新系统货号：".$goods_id.'没有更新到！<br/>',FILE_APPEND);
                }
                
                
            }
           
            
        }
         echo '完毕！';
    }
    
}

