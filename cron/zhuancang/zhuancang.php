<?PHP
header("Content-type:text/html;charset=utf8;");
error_reporting(E_ALL);
//require_once '../../../frame/class/Util.class.php';
//require_once './ApiModel.php';

$mysqli=new mysqli('192.168.1.63','yangfuyou','yangfuyou1q2w3e','warehouse_shipping') or die("数据库连接失败！") ; 


	date_default_timezone_set("PRC");

	$create_time = '2015/4/24 18:41';//-----------------------------------需要修改- 制单时间
	$check_time = '2015/4/24 18:41';//-----------------------------------需要修改 -审核时间  
	$in_warehouse_type = "";
	$send_goods_sn = "";	
	$kela_order_sn = "20150519246907";  //------------------------------------需要修改-订单号
	$account_type = "";
	$bill_note = '技术代处理，20150519246907退货错误';//备注信息：系统转仓-体验店-仓库
	$times = time() . rand(10001,99999);
	$tuihuoyuanyin = '';
	$from_company_id = '58';//发货公司id
	$from_company = '总公司';//发货公司名
	
	$to_warehouse_id = '503';//深圳分公司
	$to_warehouse = '深圳分公司';//

	$to_company_id = '445';//BDD深圳分公司
	$to_company = 'BDD深圳分公司';//
        
	$chengbenjia = 0;
	$bill_type="M";
    $goods_id_array = array('150429549822');//-----------------------------------需要修改-货号
   
    $bill_info = array(
                    'bill_type'=>$bill_type,
                    'bill_status'=>'2',
                    'in_warehouse_type'=>$in_warehouse_type,
                    'order_sn'=>$kela_order_sn,
                    'prc_id'=>0,
                    'prc_name'=>'',
                    'from_warehouse_id'=>0,
                    'from_warehouse'=>'',
                    'to_warehouse_id'=>$to_warehouse_id,//入库
                    'to_warehouse_name'=>$to_warehouse,
                    'from_company_id'=>$from_company_id,//发货
                    'from_company_name'=>$from_company,
                    'to_company_id'=>$to_company_id,
                    'to_company_name'=>$to_company,
                    'chengbenjia'=>$chengbenjia,
                    'goods_sum'=>count($goods_id_array),
                    'create_user'=>"system",
                    'create_time'=>$create_time , 
                    'check_user'=>'system',
                    'check_time'=>$check_time ,
                    'bill_note'=>$bill_note,
                  
    );
    //单据信息，里面信息可作修改
   // print_r($bill_info);exit;
    $res = CreateBill($bill_info,$goods_id_array);
	
    if ($res == "success"){
        echo 'ok';
    }else {
        echo 'failed >_<';
    }
   

	
	
        
function CreateBill($bill_info, $goods_id_array)
{
    global $mysqli;
    $bill_no  = time() . rand(10001,99999);//临时随便给一个值
    $sql = "INSERT INTO `warehouse_bill` (
            `bill_no`, `to_company_id`, `to_warehouse_id`,
            `from_company_id`,`goods_sum`,`chengbenjia`,
            `bill_note`,`to_warehouse_name`,
            `to_company_name`,`from_company_name`,
            `create_user`,`create_time`,`bill_type`,`order_sn`,`check_user`
           ,`check_time` ) VALUES (
            '{$bill_no}', {$bill_info['to_company_id']}, {$bill_info['to_warehouse_id']},
            {$bill_info['from_company_id']}, {$bill_info['goods_sum']}, {$bill_info['chengbenjia']},
            '{$bill_info['bill_note']}','{$bill_info['to_warehouse_name']}',
            '{$bill_info['to_company_name']}',
            '{$bill_info['from_company_name']}','{$bill_info['create_user']}',
            '{$bill_info['create_time']}', '{$bill_info['bill_type']}','{$bill_info['order_sn']}',
            '{$bill_info['check_user']}','{$bill_info['check_time']}')";
			//echo $sql;exit;
            $mysqli->query("set names utf8");
            $mysqli->query($sql);
            $id = $mysqli->insert_id;
            //获取商品信息
            //$goods_id_array = array('150421542290','150421542291');//商品信息，货号可做添加修改
            $str = implode(",", $goods_id_array);
            $goods_id_str = substr($str, 0);
          // echo $goods_id_str;exit;
            $goods = GetGoodsInfo($goods_id_str);
            
            //创建单据编号
            $bill_no = create_bill_no('M',$id);
            echo $bill_no;
            $sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_no}',`bill_status`=2 WHERE `id`={$id}";
            $mysqli->query($sql);
			$chengben = 0;
            //插入明细（货号是唯一的，所以这里的货品数量是1）
            foreach ($goods as $gk => $gv) 
			{
                    //$gv[10] = $gv[10]?$gv[10]:0;
                    $sql = "INSERT INTO `warehouse_bill_goods` (
                            `bill_id`, `bill_no`, `bill_type`, `goods_id`,
                            `goods_sn`, `goods_name`, `num`, `jinzhong`,
                            `caizhi`, `yanse`,`jingdu`,`jinhao`,
                            `mingyijia`, `zhengshuhao`, `addtime`
                            ) VALUES (
                            {$id}, '{$bill_no}', 'M', '{$gv['goods_id']}',
                            '{$gv['goods_sn']}', '{$gv['goods_name']}', 1, '{$gv['jinzhong']}',
                            '{$gv['caizhi']}', '{$gv['yanse']}', '{$gv['jingdu']}', {$gv['jinhao']},
                            '{$gv['mingyichengben']}', '{$gv['zhengshuhao']}', '{$gv['addtime']}') ";
                           // echo $sql.'<hr>';exit;
                    $mysqli->query("set names utf8");
                    $mysqli->query($sql);
					$chengben += $gv['mingyichengben'];

            }
            $sql = "UPDATE `warehouse_bill` SET mingyijia='{$chengben}'  WHERE `id`={$id}";
			//echo $sql;
            $mysqli->query($sql);
            //插入订单号+快递单号
            $sql = "INSERT INTO `warehouse_bill_info_m` (`bill_id`,`ship_number`) VALUES ({$id}, '')";
            
            $mysqli->query($sql);
            //写入warehouse_bill_status信息
            $update_time = date('Y-m-d H:i:s');
            $ip = '';
            $sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$id}, '{$bill_no}', 2, '{$update_time}', '{$bill_info['create_user']}', '{$ip}') ";
            //echo $sql;exit;
            $mysqli->query("set names utf8");
            $mysqli->query($sql);
            return 'success';
            //结束
}
   

function GetGoodsInfo($goods_id_str)
{
    global $mysqli;
    $sql = "select * from `warehouse_goods` where `goods_id` in ({$goods_id_str})";
    
    $mysqli->query("set names utf8");
    $result = $mysqli->query($sql);
    
    if ($result) 
	{
        if($result->num_rows>0)
		{   
			//判断结果集中行的数目是否大于0
            while($row =$result->fetch_assoc() )
			{  //循环输出结果集中的记录
                $goods_info[] = $row;
            }
        }
    }
	else
	{
		die('no data');
    }
    return $goods_info;
    
}

 function create_bill_no($type,$bill_id = '1')
{
		
    $bill_id = substr($bill_id,-4);
    $bill_no = $type.date('Ymd',time()).rand(100,999).str_pad($bill_id,4,"0",STR_PAD_LEFT);
    return $bill_no;
}

    
   ?>