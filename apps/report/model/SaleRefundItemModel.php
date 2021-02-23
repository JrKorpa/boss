<?php
/**
 *  -------------------------------------------------
 *   @file		: BaseGoodInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-08-09 17:51:11
 *   @update	:
 *  -------------------------------------------------
 */
class SaleRefundItemModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'sale_refund_item';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>"自增ID",
            "sup_id"=>"父类id",
            "grade"=>"等级",
            "value"=>"值",
            "create_time"=>"添加时间",
            "modify_time"=>"修改时间",
            "create_user"=>"创建人",
            "update_user"=>"编辑人",
            "date_number"=>"销售退款报表");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url BaseGoodInfoController/search
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
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

	function insert(array $value){
        if(!empty($value)){
            $pdo=$this->db()->db();
            try{
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
                $pdo->beginTransaction();//开启事务
                $sql="INSERT INTO ".$this->table()."(sup_id,grade,value,create_time,modify_time,create_user,update_user,pdate) VALUES(?,?,?,?,?,?,?,?)";
                $smt=$pdo->prepare($sql);
                foreach ($value as $key=> $val){
                    $smt->execute(array(intval($val[0]),intval($val[3]),$val[1],date("Y-m-d H:i:s"),date("Y-m-d H:i:s"),$_SESSION['userName'],$_SESSION['userName'],str_replace('-','_',$val[2])));
                }
            }catch(Exception $e){
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                return false;
            }
            $pdo->commit();//如果没有异常，就提交事务
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return true;
        }else{
            return  false;
        }
    }
    function query($where){
        $sql="select sup_id,grade,value,create_time,modify_time,pdate from  ".$this->table() ." where  ".$where." ORDER BY sup_id,create_time,grade";
        return $this->db()->getAll($sql);
    }

    function updateItem (array $value){
        $pdo=$this->db()->db();
        try{
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务
            $sql="update ".$this->table()." set value=? ,modify_time=?,update_user=?  where sup_id=? and grade=? and pdate=?";
            $smt=$pdo->prepare($sql);
            foreach ($value as $key=> $val){
                $smt->execute(array($val[1],date("Y-m-d H:i:s"),$_SESSION['userName'],$val[0],$val[3],str_replace('-','_',$val[2])));
            }
        }catch (Exception $e){
            var_dump($e->getMessage()); die;
            $pdo->rollback;
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }
        $pdo->commit();
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);
        return true;
    }

    function importCSV($insertData,$month){
            $pdo=$this->db()->db();
            try{
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
                $pdo->beginTransaction();//开启事务
                $sql="INSERT INTO ".$this->table()."(sup_id,grade,value,create_time,modify_time,create_user,update_user,pdate) VALUES(?,?,?,?,?,?,?,?)";
                $smt=$pdo->prepare($sql);
                foreach ($insertData as $k=> $val){
                    foreach ($val as $key=>$value){
                        $smt->execute(array($k,$key,$value,date("Y-m-d H:i:s"),date("Y-m-d H:i:s"),$_SESSION['userName'],$_SESSION['userName'],"$month-01"));
                    }
                }
            }catch(Exception $e){
                var_dump($e->getMessage()); die;
                $pdo->rollback();//事务回滚
                $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
                return false;
            }
            $pdo->commit();//如果没有异常，就提交事务
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return true;
    }
    //获取退款数据

    /**
     * @param $start
     * @param $end
     * @param null $department
     * @return mixed
     */
    function getRefundData($start, $end, $department=null){
        //$sql="select left(r.apply_time,10) as apply_time , sum(r.real_return_amount) as real_return_amount,ifnull(g.cat_type1,c.cat_type_name) as cat_type from app_return_goods r ,base_order_info o,app_order_details d LEFT JOIN warehouse_shipping.warehouse_goods g ON d.goods_id=g.goods_id LEFT JOIN front.base_style_info s ON d.goods_sn=s.style_sn LEFT JOIN front.app_cat_type c ON s.style_type=c.cat_type_id  where r.order_id=o.id and r.order_goods_id=d.id and r.return_type<>'1' and o.order_status=4 and r.check_status>=4 and o.order_pay_status<>1 and d.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') and r.real_return_amount>0 and r.apply_time>'".$start." 00:00:00' and r.apply_time<'".$end." 00:00:00'";
        $sql="SELECT LEFT(apply_time, 10) AS apply_time,SUM(real_return_amount) AS real_return_amount,IFNULL(cat_type1, cat_type_name) AS cat_type,order_sn FROM (SELECT r.apply_time,r.real_return_amount,g.cat_type1,c.cat_type_name,o.order_sn FROM app_return_goods r,base_order_info o,app_order_details d LEFT JOIN warehouse_shipping.warehouse_goods g ON d.goods_id = g.goods_id LEFT JOIN front.base_style_info s ON d.goods_sn = s.style_sn LEFT JOIN front.app_cat_type c ON s.style_type = c.cat_type_id WHERE r.order_id = o.id AND r.order_goods_id = d.id AND r.return_type <> '1' AND o.order_status = 4 AND r.check_status >= 4 AND o.order_pay_status <> 1 AND d.goods_sn NOT IN ( 'KLUX031507', 'KLUX031508', 'KLUX031509', 'KLUX031510', 'KLUX031511', 'KLUX031512' ) AND r.real_return_amount > 0 AND r.apply_time > '".$start." 00:00:00' AND r.apply_time < '".$end." 00:00:00'  ";
        if($department==2){
            $sql.=" and o.department_id=2";
        }else if($department==71){
            $sql.=" and o.department_id=71 and  o.customer_source_id in (645,2414)";
        }
        $sql.=" ORDER BY r.apply_time  ASC ) AS eb GROUP  BY apply_time";
        //$sql .= ' order by apply_time';
      return $this->db()->getAll($sql);
    }
    //获取退款没有重复的订单
    function getRefundOrder($start,$end,$department=null){
        $sql="select left(r.apply_time,10) as apply_time, r.order_sn from app_return_goods r ,base_order_info o,app_order_details d LEFT JOIN warehouse_shipping.warehouse_goods g ON d.goods_id=g.goods_id LEFT JOIN front.base_style_info s ON d.goods_sn=s.style_sn LEFT JOIN front.app_cat_type c ON s.style_type=c.cat_type_id  where r.order_id=o.id and r.order_goods_id=d.id and r.return_type<>'1' and o.order_status=4 and r.check_status>=4 and o.order_pay_status<>1 and d.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') and r.real_return_amount>0 and r.apply_time>'$start 00:00:00' and r.apply_time<'$end 00:00:00'";
        if($department==2){
            $sql.=" and o.department_id=2";
        }else if($department==71){
            $sql.=" and o.department_id=71 and  o.customer_source_id in (645,2414)";
        }
        $sql.=' group by r.order_sn';
        return $this->db()->getAll($sql);
    }
    //获取退款没有重复的货品
    function getRefundGoods($start,$end,$department=null){
        //$sql="select left(r.apply_time,10) as apply_time, r.order_sn,d.goods_sn,ifnull(g.product_type,apt.product_type_name) as cat_type,sum(r.real_return_amount) as real_return_amount from app_return_goods r ,base_order_info o,app_order_details d LEFT JOIN warehouse_shipping.warehouse_goods g ON d.goods_id=g.goods_id LEFT JOIN front.base_style_info s ON d.goods_sn=s.style_sn  LEFT JOIN front.`app_product_type` apt ON apt.product_type_id=g.`product_type1`  where r.order_id=o.id and r.order_goods_id=d.id and r.return_type<>'1' and o.order_status=4 and r.check_status>=4 and o.order_pay_status<>1 and d.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') and r.real_return_amount>0 and r.apply_time>'$start 00:00:00' and r.apply_time<'$end 00:00:00'";
        $sql="select left(r.apply_time,10) as apply_time, r.order_sn,d.goods_sn,ifnull(g.product_type,apt.product_type_name) as cat_type,r.real_return_amount from app_return_goods r ,base_order_info o,app_order_details d LEFT JOIN warehouse_shipping.warehouse_goods g ON d.goods_id=g.goods_id LEFT JOIN front.base_style_info s ON d.goods_sn=s.style_sn  LEFT JOIN front.`app_product_type` apt ON apt.product_type_id=s.`product_type`  where r.order_id=o.id and r.order_goods_id=d.id and r.return_type<>'1' and o.order_status=4 and r.check_status>=4 and o.order_pay_status<>1 and d.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') and r.real_return_amount>0 and r.apply_time>'$start 00:00:00' and r.apply_time<'$end 00:00:00'";
        if($department==2){
            $sql.=" and o.department_id=2";
        }else if($department==71){
            $sql.=" and o.department_id=71 and  o.customer_source_id in (645,2414)";
        }
        //$sql.=' group by d.goods_sn';
        return $this->db()->getAll($sql);
    }
    //天猫新增订单金额
    function TMDayMarkAmount($start,$end){
        $sql="SELECT pay_date,SUM(order_amount) as order_amount ,COUNT(order_sn)  as count FROM (SELECT distinct LEFT(o.pay_date,10) as pay_date,o.order_sn,a.order_amount FROM base_order_info o,app_order_details d,app_order_account a WHERE o.id=a.order_id AND o.id=d.order_id AND o.order_status=2 AND a.order_amount>0 AND o.department_id=2 AND d.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') AND o.pay_date>'".$start." 00:00:00' AND  o.pay_date<'".$end." 00:00:00' ) as t  GROUP BY t.pay_date";
        return $this->db()->getAll($sql);
    }
    function JDDayMarkAmount($start,$end){
        $sql="SELECT pay_date,SUM(order_amount) as order_amount ,COUNT(order_sn)  as count FROM (SELECT distinct LEFT(o.pay_date,10) as pay_date,o.order_sn,a.order_amount FROM base_order_info o,app_order_details d,app_order_account a WHERE o.id=a.order_id AND o.id=d.order_id AND o.order_status=2 AND a.order_amount>0 AND o.department_id=71 AND o.customer_source_id in (645,2414) AND d.goods_sn not in ('KLUX031507','KLUX031508','KLUX031509','KLUX031510','KLUX031511','KLUX031512') AND o.pay_date>'".$start." 00:00:00' AND  o.pay_date<'".$end." 00:00:00' ) as t  GROUP BY t.pay_date";
        return $this->db()->getAll($sql);
    }

    //未发货记录
    function getUnfilledOrders($status,$department_id=null){
        $sql="  SELECT ifnull(sum(b.order_amount),0) as price,count(a.order_sn) AS count FROM base_order_info AS a LEFT JOIN app_order_account AS b ON a.id=b.order_id WHERE a.send_good_status=1 AND a.order_status=2 AND  a.order_pay_status IN (2,3,4)  ";
        if($department_id==2){
            $sql.=" and a.department_id=2";
        }else if($department_id==71){
            $sql.=" and a.department_id=71 and a.customer_source_id in (645,2414)";
        }
        if($status==1){
            $sql.="  AND LEFT(a.pay_date,10)=LEFT(NOW(),10)";
        }else if($status==20){
            $sql.="  AND DATEDIFF(LEFT(NOW(),10),a.pay_date)>=20";
        }else if($status==30){
            $sql.="  AND DATEDIFF(LEFT(NOW(),10),a.pay_date)>=30";
        }
        return $this->db()->getAll($sql);
    }
}
?>