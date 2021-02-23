<?php


class SaleQuotaItemModel extends Model
{
    function __construct($id=null,$parems=""){
        $this->_objName = 'sale_quota_item';
        $this->_dataObject=array(
           'id'=>'主键',
           'item_id'=>'指标id',
            'pdate'=>'指示日期',
            'plan_value'=>'指标计划值',
            'actual_value'=>'指标实际值',
            'create_time'=>'添加时间',
            'modify_time'=>'修改时间',
            'create_user'=>'创建人',
            'update_user'=>'编制人',
        );
        parent::__construct($id, $parems);
    }
    
    //查询销售计划
    function query($where=array(),$useCache=true){
        $sql="select * from ".$this->table().", app_order.sale_quota  where item_id= sale_quota.id and cate_type1='".$where['cate_type1']."' and  DATE_FORMAT(pdate,'%Y-%m')='".$where['pdateTime']."' ";
      return $this->db()->getAll( $sql);
    }

    function TMPlanCount($start,$end){
       $sql="SELECT COUNT(o.order_sn)  as count FROM base_order_info o,app_order_account a WHERE o.id=a.order_id AND o.order_status=2 AND a.order_amount>0 AND o.department_id=2 AND d.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') AND o.pay_date>='".$start." 00:00:00' AND  o.pay_date<='".$end." 00:00:00'  ";
       return $this->db()->getAll($sql);
    }
    //
    function TMDayMarkAmount($start,$end){
       //$sql="SELECT LEFT(o.pay_date,10) as pay_date,SUM(a.order_amount) as   order_amount ,COUNT(o.order_sn)  as count FROM base_order_info o,app_order_account a WHERE o.id=a.order_id AND o.order_status=2 AND a.order_amount>0 AND o.department_id=2 AND o.pay_date>'".$start." 00:00:00' AND  o.pay_date<'".$end." 00:00:00'   GROUP BY LEFT(o.pay_date,10)";
        $sql="SELECT pay_date,SUM(order_amount) as order_amount ,COUNT(order_sn)  as count FROM (SELECT distinct LEFT(o.pay_date,10) as pay_date,o.order_sn,a.order_amount FROM base_order_info o,app_order_details d,app_order_account a WHERE o.id=a.order_id AND o.id=d.order_id AND o.order_status=2 AND a.order_amount>0 AND o.department_id=2 AND d.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') AND o.pay_date>'".$start." 00:00:00' AND  o.pay_date<'".$end." 00:00:00' ) as t  GROUP BY t.pay_date";
       return $this->db()->getAll($sql);
    }
    //s单
    function TMSDayMargin($start,$end){
      $sql="select left(b.check_time,10) as pay_date,sum(bg.shijia) as earning,sum(bg.shijia-g.yuanshichengbenjia) as margin from warehouse_shipping.warehouse_bill b,app_order.base_order_info o,warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_goods g where b.id=bg.bill_id and bg.goods_id=g.goods_id and b.bill_status=2 and b.bill_type='S' and b.order_sn=o.order_sn and o.department_id=2 AND g.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') and b.check_time>'".$start." 00:00:00' and b.check_time<'".$end." 00:00:00'  group by left(b.check_time,10)";
      return $this->db()->getAll($sql);
    }

    function JDPlanCount($start,$end){
      // $sql="SELECT COUNT(o.order_sn) AS count  FROM app_order.base_order_info o,app_order.app_order_account a WHERE o.id=a.order_id AND o.order_status=2 AND a.order_amount>0 AND o.department_id=71  AND o.customer_source_id in (645,2414) AND o.pay_date>'".$start." 00:00:00' AND o.pay_date<'".$end." 00:00:00' ";
        $sql="SELECT COUNT(order_sn)  as count FROM (SELECT distinct LEFT(o.pay_date,10) as pay_date,o.order_sn,a.order_amount FROM base_order_info o,app_order_details d,app_order_account a WHERE o.id=a.order_id AND o.id=d.order_id AND o.order_status=2 AND a.order_amount>0 AND o.department_id=71 AND o.customer_source_id in (645,2414) AND d.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') AND o.pay_date>'".$start." 00:00:00' AND  o.pay_date<'".$end." 00:00:00' ) as t  ";
      return $this->db()->getAll($sql);
    }

    function JDDayMarkAmount($start,$end){
      //$sql="SELECT LEFT(o.pay_date,10) AS  pay_date,COUNT(o.order_sn) AS count ,SUM(a.order_amount) AS order_amount FROM app_order.base_order_info o,app_order.app_order_account a WHERE o.id=a.order_id AND o.order_status=2 AND a.order_amount>0 AND o.department_id=71  AND o.customer_source_id in (645,2414) AND o.pay_date>'".$start." 00:00:00' AND o.pay_date<'".$end." 00:00:00'  GROUP BY LEFT(o.pay_date,10);";
        $sql="SELECT pay_date,SUM(order_amount) as order_amount ,COUNT(order_sn)  as count FROM (SELECT distinct LEFT(o.pay_date,10) as pay_date,o.order_sn,a.order_amount FROM base_order_info o,app_order_details d,app_order_account a WHERE o.id=a.order_id AND o.id=d.order_id AND o.order_status=2 AND a.order_amount>0 AND o.department_id=71 AND o.customer_source_id in (645,2414) AND d.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') AND o.pay_date>'".$start." 00:00:00' AND  o.pay_date<'".$end." 00:00:00' ) as t  GROUP BY t.pay_date";
        return $this->db()->getAll($sql);
    }

    function JDSDayMargin($start,$end){
      $sql="SELECT LEFT(b.check_time,10) as pay_date,SUM(bg.shijia) AS earning,SUM(bg.shijia-g.yuanshichengbenjia) AS margin FROM warehouse_shipping.warehouse_bill b,app_order.base_order_info o,warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_goods g WHERE b.id=bg.bill_id AND bg.goods_id=g.goods_id AND b.bill_status=2 AND b.bill_type='S' AND b.order_sn=o.order_sn AND o.department_id=71 AND o.customer_source_id in (645,2414) AND g.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') AND b.check_time>'".$start." 00:00:00'AND b.check_time<'".$end." 00:00:00'  GROUP BY LEFT(b.check_time,10)";
      return $this->db()->getAll($sql);
    }
    //自营的数据
    function aoturophy($start,$end){
      $sql="SELECT LEFT(b.check_time,10) AS pay_date,SUM(bg.shijia) AS earning,SUM(bg.shijia-g.yuanshichengbenjia) AS margin FROM warehouse_shipping.warehouse_bill b,app_order.base_order_info o,warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_goods g WHERE b.id=bg.bill_id AND bg.goods_id=g.goods_id AND b.bill_status=2 AND b.bill_type='S' AND b.order_sn=o.order_sn AND o.customer_source_id=2906 AND g.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') AND  b.check_time>'".$start." 00:00:00' AND b.check_time<'".$end." 00:00:00'  GROUP BY LEFT(b.check_time,10)";
       return $this->db()->getAll($sql);
    }

    //渠道二部
    function channelTwoDayMargin($start,$end){
      //$sql="SELECT LEFT(o.pay_date,10) AS pay_date,COUNT(o.order_sn) AS count,SUM(a.order_amount) AS order_amount FROM app_order.base_order_info o,app_order.app_order_account a WHERE o.id=a.order_id AND o.order_status=2 AND o.department_id IN (3,13) AND o.customer_source_id<>2034 AND a.order_amount>0 AND a.order_amount>0  and o.pay_date>'".$start." 00:00:00' and o.pay_date<'".$end." 00:00:00'  GROUP BY LEFT(o.pay_date,10)";
      $sql="select pay_date,COUNT(order_sn) AS count,SUM(order_amount) AS order_amount from ( SELECT distinct LEFT (o.pay_date, 10) AS pay_date,o.order_sn,a.order_amount FROM app_order.base_order_info o,app_order_details d,app_order.app_order_account a WHERE o.id=a.order_id and o.id=d.order_id and d.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') AND o.order_status=2 AND o.department_id IN (3,13) AND o.customer_source_id<>2034 AND a.order_amount>0 AND a.order_amount>0  and o.pay_date>'".$start." 00:00:00' and o.pay_date<'".$end." 00:00:00' ) as t GROUP BY  t.pay_date";
      return $this->db()->getAll($sql);
    }
    //渠道二部
    function channelTwoSDayMargin($start,$end){
        //去掉对唯品会订单的限制 AND o.customer_source_id<>2034
      //$sql="SELECT  check_time AS pay_date,SUM(shouru ) AS earning,SUM(maoli) AS margin, yuanshichengbenjia as cost ,COUNT(order_sn) AS count FROM (SELECT LEFT(b.check_time,10) check_time,SUM(bg.shijia) AS shouru,g.yuanshichengbenjia,SUM(bg.shijia-g.yuanshichengbenjia) AS maoli,o.order_sn FROM warehouse_shipping.warehouse_bill b,app_order.base_order_info o,warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_goods g WHERE b.id=bg.bill_id AND bg.goods_id=g.goods_id AND b.bill_status=2 AND b.bill_type='S' AND b.order_sn=o.order_sn AND o.department_id IN (3,13) AND o.customer_source_id<>2034 AND b.check_time>='".$start." 00:00:00' AND b.check_time<='".$end." 00:00:00'  GROUP BY LEFT(b.check_time,10),o.order_sn) AS t GROUP BY check_time";
        $sql="SELECT  check_time AS pay_date,SUM(shouru ) AS earning,SUM(maoli) AS margin, sum(yuanshichengbenjia) as cost ,COUNT(order_sn) AS count FROM (SELECT LEFT(b.check_time,10) check_time,SUM(bg.shijia) AS shouru,sum(g.yuanshichengbenjia) as yuanshichengbenjia,SUM(bg.shijia-g.yuanshichengbenjia) AS maoli,o.order_sn FROM warehouse_shipping.warehouse_bill b,app_order.base_order_info o,warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_goods g WHERE b.id=bg.bill_id AND bg.goods_id=g.goods_id AND b.bill_status=2 AND b.bill_type='S' AND b.order_sn=o.order_sn AND o.department_id IN (3,13) AND g.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') AND b.check_time>='".$start." 00:00:00' AND b.check_time<='".$end." 00:00:00'  GROUP BY LEFT(b.check_time,10),o.order_sn) AS t GROUP BY check_time";
        return $this->db()->getAll($sql);
    }
    //渠道二部 退款
    function channelTwoRefund($start,$end){
        //去掉对唯品会订单的限制 AND o.customer_source_id<>2034
      //$sql="SELECT LEFT(b.check_time,10) AS pay_date,SUM(b.shijia) AS refund,COUNT(o.order_sn) AS count FROM warehouse_shipping.warehouse_bill b,app_order.base_order_info o,warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_goods g WHERE b.id=bg.bill_id AND bg.goods_id=g.goods_id AND b.bill_status=2 AND b.bill_type='D' AND b.order_sn=o.order_sn AND o.department_id IN (3,13) AND o.customer_source_id<>2034 AND b.check_time>'".$start." 00:00:00' AND b.check_time<'".$end." 00:00:00'  GROUP BY LEFT(b.check_time,10)";
      $sql="SELECT LEFT(b.check_time,10) AS pay_date,SUM(b.shijia) AS refund,COUNT(o.order_sn) AS count FROM warehouse_shipping.warehouse_bill b,app_order.base_order_info o,warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_goods g WHERE b.id=bg.bill_id AND bg.goods_id=g.goods_id AND b.bill_status=2 AND b.bill_type='D' AND b.order_sn=o.order_sn AND o.department_id IN (3,13)  AND g.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') AND b.check_time>'".$start." 00:00:00' AND b.check_time<'".$end." 00:00:00'  GROUP BY LEFT(b.check_time,10)";
      return $this->db()->getAll($sql);
    }
    //获取头表归属渠道数据
    function  gainParent($channel,$filter){
        $sql="select * from  app_order.sale_quota where id not in (".$filter.") and  cate_type1='".$channel."'";
        return $this->db()->getAll($sql);
    }

    function importCSV($date,$array,$pdate){
      $sql="INSERT INTO ".$this->table()."(item_id,pdate,plan_value,actual_value,create_time,modify_time,create_user) VALUES(?,?,?,?,?,?,?)";
      $pdo = $this->db()->db();//pdo对象
      try{
          $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
          $pdo->beginTransaction();//开启事务
          $stmt=$pdo->prepare($sql);
          $index=1;
          foreach ($array as $key => $value) {
            foreach ($value as $k => $val) {
              $day=$k-3;
              $stmt->execute(array($key,($pdate.($day<10?"-0".$day:"-".$day)),$val,0,date("Y-m-d H:i:s"),date("Y-m-d H:i:s"),$_SESSION['userName']));
              $index++;
            }
           
          }

      }catch(Exception $e){//捕获异常
          var_dump($e->getMessage());die;
          $pdo->rollback();//事务回滚
          $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
          return false;
      }
      $pdo->commit();//如果没有异常，就提交事务
      $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
      return true;
      
    }
    function  insert(array $value){
        if(!empty($value)){
            $pdo = $this->db()->db();//pdo对象
            try{
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
                $pdo->beginTransaction();//开启事务
                $check=false;
                $sql="INSERT INTO ".$this->table()."(item_id,pdate,plan_value,actual_value,create_time,modify_time,create_user) VALUES(?,?,?,?,?,?,?)";
                $stmt=$pdo->prepare($sql);
                for($i=0;$i<count($value);$i++){
                     $stmt->execute(array(intval($value[$i][0]),str_replace('_','-',$value[$i][2]),(empty($value[$i][1])?0:$value[$i][1]),0,date("Y-m-d H:i:s"),date("Y-m-d H:i:s"),$_SESSION['userName']));
                }
             }catch(Exception $e){//捕获异常
                var_dump($e->getMessage());die;
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                return false;
             }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return true;
        }
    }


    function queryItemCount(array  $where){
        $sql="select  count(*) from ".$this->table()." left join app_order.sale_quota on sale_quota.id=sale_quota_item.item_id where  DATE_FORMAT(pdate,'%Y-%m')='".$where['time']."' and sale_quota.cate_type1='".$where['channel']."'";
        return $this->db()->getAll($sql);
    }

    function updateItem(array $value){
       $pdo = $this->db()->db();//pdo对象
       try{
          $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
          $pdo->beginTransaction();//开启事务
          $sql="UPDATE ".$this->table()." SET plan_value=?,update_user=?,modify_time=? where item_id=? and pdate=?";
          $stmt=$pdo->prepare($sql);
          for($i=0;$i<count($value);$i++){
               $stmt->execute(array($value[$i][1],$_SESSION['userName'],date('y-m-d H:i:s'),intval($value[$i][0]),str_replace('_','-',$value[$i][2])));
          }
        }catch(Exception $e){//捕获异常
          var_dump($e->getMessage());die;
          $pdo->rollback();//事务回滚
          $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
          return false;
        }
        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return true;
    }

   
}

