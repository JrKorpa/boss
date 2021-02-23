<?php
/**
 * 仓库数据模块的模型（代替WareHouse/Api/api.php）
 *  -------------------------------------------------
 *   @file		: WareHouseModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseModel
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
    
	/**
	 * 生成销售退货单(将warehouse里api的createReturnGoodsBill功能移到此处)
	 *
	 * @param unknown $order_sn
	 * @param unknown $return_id
	 * @param unknown $create_user
	 * @param unknown $warehouse_id
	 * @param unknown $order_goods
	 */
	public function createReturnGoodsBill($order_sn, $return_id, $create_user, $warehouse_id, $order_goods,$m_warehouse_id="")
	{
	    $status = true;
	    $create_user = $_SESSION['userName'];
	    try {
	        // 退货订单号
	        if (empty($order_sn)) {
	            throw new Exception('缺少参数order_sn或者为空!');
	        }
	        // 退货流水ID
	        if (empty($return_id)) {
	            throw new Exception('缺少参数return_id或者为空!');
	        }
	        if (empty($create_user)) {
	            $create_user = 'SYSTEM';
	        }
	        // 退货仓库ID
	        if (empty($warehouse_id)) {
	            throw new Exception('已发货，请选择已生产，转为库存!!');
	        }
	        if (! count($order_goods)) {
	            throw new Exception('缺缺少参数order_goods或者为空');
	        }
	        
	        $sql = "SELECT `a`.`company_name` , `a`.`company_id` , `b`.`name` FROM warehouse_shipping.warehouse_rel AS `a` LEFT JOIN warehouse_shipping.warehouse AS `b` ON `a`.`warehouse_id` = `b`.`id` WHERE `b`.`id` = '{$warehouse_id}'";
	
	        $warehouse_info = $this->db()->getRow($sql);	        
	        
	        $sql = "SELECT `id` FROM warehouse_shipping.warehouse_bill where `order_sn` = '" . $order_sn . "' AND `bill_status` = 2 AND `bill_type` = 'S' order by id desc";
	
	        $bill_id_arr = $this->db()->getAll($sql);
	        if (empty($bill_id_arr)) {
	            throw new Exception('此订单号没有有效的销售单，请检查并联系相关人员');
	        }
	        $bill_id_str=implode(',', array_column($bill_id_arr, 'id'));
	        $chengbenjia = 0; // 总成本价
	        $mingyijia = 0; // 总名义成本价
	        $tuihuojia = 0; // 总退货价
	        $goods_total = 0;//
            $biaoqianjia = 0;

            $is_hidden = 0;
            $goods_id_arr = array();

	        foreach ($order_goods as $key => $val) {
	            $goods_id = $val['goods_id'];
	            $sql = "select g.*,bg.shijia,bg.sale_price from warehouse_shipping.warehouse_goods as g,warehouse_shipping.warehouse_bill_goods as bg where bg.goods_id = g.goods_id and g.order_goods_id = '{$val['detail_id']}' and bg.bill_id in ({$bill_id_str}) and bg.goods_id='{$goods_id}'" ;
	            $goods_arr = $this->db()->getRow($sql);	           
	            if (empty($goods_arr['goods_id'])) { // 没有找到关联的货号
	                throw new Exception("货号 {$goods_id} 不是销售单中的货品");
	            } else {
	                $goods_id = $goods_arr['goods_id'];
	                /* $sql = "select * from warehouse_shipping.warehouse_goods where goods_id = '{$goods_id}'";
	                $arr = $this->db()->getRow($sql); // 取出货品的其他信息
	                if(!empty($arr)){
	                    if ($arr['is_on_sale'] != '3') { // 货品不是已销售状态不能做退货
	                        throw new Exception('货品不是已销售状态，不能退货');
	                    }
	                } */
	               // $sql = "select shijia from warehouse_shipping.warehouse_bill_goods where goods_id ='$goods_id' and bill_id = $bill_id ";
	               // $shijia = $this->db()->getOne($sql);
	                $sale_price = $goods_arr['sale_price'];
	                $shijia = $goods_arr['shijia'];
	                $tuihuojia += $shijia;
	                $biaoqianjia += $goods_arr['biaoqianjia'];
	                
	                $val['shijia'] = $shijia;
	                $val['sale_price'] = $sale_price;//零售价
	                $order_goods[$key] = array_merge($val, $goods_arr);
	                $chengbenjia += $goods_arr['yuanshichengbenjia'];
	                $mingyijia += $goods_arr['mingyichengben'];
	                $goods_total += $sale_price;
	                // $tuihuojia += $val['return_price'];
	               

                    if($goods_arr['hidden'] == 1) $is_hidden = 1;
                    $goods_id_arr[] = $goods_id;
	            }
	        }
	        // 生成单号
	        //$sql = 'SELECT `bill_no` FROM warehouse_shipping.warehouse_bill WHERE `id` = (SELECT max(id) from warehouse_shipping.warehouse_bill)';
	        //$str = $this->db()->getOne($sql);
	        //$no = (substr($str, 1, 8) != date('Ymd', time())) ? 1 : intval(substr($str, 9)) + 1;
	        // $bill_no = 'D' . date('Ymd', time()) . str_pad($no, 5, "0", STR_PAD_LEFT);
	        /*
	         * $to_warehouse_id = 96;
	         * $to_warehouse_name = '总公司后库';
	         * $to_company_id = 58;
	         * $to_company_name = '总公司';
	         */
	        $time = date('Y-m-d H:i:s');
	        $to_warehouse_id = $warehouse_id;
	        $to_warehouse_name = $warehouse_info['name'];
	        $to_company_id = $warehouse_info['company_id'];
	        $to_company_name = $warehouse_info['company_name'];
	        	        
	        if($to_company_id==445 && empty($m_warehouse_id)){
	            throw new Exception('调拨入库仓不能为空');
	        }	        
	        
	        $sql = "INSERT INTO warehouse_shipping.warehouse_bill(`bill_no`, `bill_type`, `bill_status`, `order_sn`, `goods_num`, `to_warehouse_id`, `to_warehouse_name`, `to_company_id`, `to_company_name`, `from_company_id`, `from_company_name`, `bill_note`, `yuanshichengben`, `goods_total`, `shijia`, `check_user`, `check_time`, `create_user`, `create_time`,`hidden`) VALUES ('" . '' . "','D',2,'" . $order_sn . "'," . count($order_goods) . "," . $to_warehouse_id . ",'" . $to_warehouse_name . "'," . $to_company_id . ",'" . $to_company_name . "',0,null,'退款流水：" . $return_id . "'," . $chengbenjia . "," . $goods_total . "," . $tuihuojia . ",'{$create_user}','".$time."','" . $create_user . "','" . $time . "',".$is_hidden.")";
	        // file_put_contents('e:/8.sql', "88\r\n".$sql."\r\n",FILE_APPEND);
	        $this->db()->query($sql);
	
	        $_id = $this->db()->insertId();
	        $d_bill_id = $_id;
	        if(!$_id){
	            throw new Exception('单据插入失败');
	        }
	
	        //$bill_id = substr($_id, - 4);
	        //$bill_no = 'D' . date('Ymd', time()) . rand(100, 999) . str_pad($bill_id, 4, "0", STR_PAD_LEFT);
	        $bill_no = $this->create_bill_no('D',$d_bill_id);
	        $bill_no_dd = '@'.substr($bill_no,1,strlen($bill_no)-1);
	        $sql = "UPDATE warehouse_shipping.warehouse_bill SET `bill_no`='{$bill_no}' WHERE `id`={$d_bill_id}";
	        $res =  $this->db()->query($sql);
	        if(!$res){
	            throw new Exception('单据更新失败');
	        }
	
	        $sql = "INSERT INTO warehouse_shipping.warehouse_bill_info_d(`bill_id`, `return_sn`) VALUES (" . $d_bill_id . "," . $return_id . ")";
	        $res = $this->db()->query($sql);
	        $_id = $this->db()->insertId();
	        if(!$_id){
	            throw new Exception('单据更新失败');
	        }
	        $sql = "INSERT INTO warehouse_shipping.warehouse_bill_status(`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES (" . $d_bill_id . ",'" . $bill_no . "',1,'" . $time . "','SYSTEM','" . Util::getClicentIp() . "')";
	        $this->db()->query($sql);
	        $_id = $this->db()->insertId();
	        if(!$_id){
	            throw new Exception('单据状态更新失败');
	        }      
	        
            $goods_id_str='';
	        foreach ($order_goods as $key => $val) {
	            $sql = "INSERT INTO warehouse_shipping.warehouse_bill_goods(`bill_id`, `bill_no`, `bill_type`, `goods_id`, `goods_sn`, `goods_name`, `num`, `warehouse_id`, `caizhi`, `jinzhong`, `yanse`, `zuanshidaxiao`, `yuanshichengben`, `sale_price`, `shijia`, `in_warehouse_type`, `account`, `addtime`, `pandian_status`, `guiwei`) VALUES (" . $d_bill_id . ",'" . $bill_no . "','D'," . $val['goods_id'] . ",'" . $val['goods_sn'] . "','" . $val['goods_name'] . "',1," . $to_warehouse_id . ",'" . $val['caizhi'] . "'," . $val['jinzhong'] . ",'" . $val['zhushiyanse'] . "'," . $val['zuanshidaxiao'] . "," . $val['yuanshichengbenjia'] . "," . $val['sale_price'] . "," . $val['shijia'] . "," . $val['put_in_type'] . ",0,'" . $time . "',0,null)";
	            // file_put_contents('e:/8.sql', "99\r\n".$sql."\r\n",FILE_APPEND);
	            $this->db()->query($sql);
	            $_id = $this->db()->insertId();
	            if(!$_id){
	                throw new Exception('单据详情添加失败');
	            }
	            $sql = "UPDATE `warehouse_goods` set `is_on_sale` = 2 ,`change_time` = '{$time}',`order_goods_id` = 0 ,`company` = '{$to_company_name}' ,`company_id` = {$to_company_id} ,`warehouse` = '{$to_warehouse_name}' , 
	            `warehouse_id` = {$to_warehouse_id},`box_sn` = '0-00-0-0' WHERE `goods_id` = '".$val['goods_id']."'";
	            $res = $this->db()->query($sql);	            
	            if(!$res){
	                throw new Exception('还原货品库存可销售状态失败');
	            }
	            $sql = "SELECT `id` FROM `warehouse_box` WHERE `warehouse_id` = {$to_warehouse_id} AND `box_sn` = '0-00-0-0' LIMIT 1";
	            $box_id = $this->db->getOne($sql);
	            if(!empty($box_id)){
	                $sql = "UPDATE `goods_warehouse` SET  `add_time` = '{$time}',`warehouse_id` = {$to_warehouse_id}, box_id = {$box_id} WHERE `good_id` = '{$val['goods_id']}'";
	                $res = $this->db()->query($sql);
	                if(!$res){
	                    throw new Exception('更新货品柜位失败');
	                }
	            }
	            $goods_id_str .= $val['goods_id']. ' ';
	        }
	        //AsyncDelegate::dispatch("warehouse", array('event' => 'bill_D_checked', 'bill_id'=>$d_bill_id));
	        //从深圳分公司调拨总部
	        if($to_company_id==445  && $m_warehouse_id>0){  
	            $sql = "SELECT `a`.`company_name` , `a`.`company_id` , `b`.`name` FROM warehouse_shipping.warehouse_rel AS `a` LEFT JOIN warehouse_shipping.warehouse AS `b` ON `a`.`warehouse_id` = `b`.`id` WHERE `b`.`id` = '{$m_warehouse_id}'";
	            $m_warehouse_info = $this->db()->getRow($sql);
	            $m_warehouse_name = $m_warehouse_info['name'];//调拨仓
	            
    	        $sql = "INSERT INTO `warehouse_bill` (
    	        `bill_no`,  `to_company_id`, `to_warehouse_id`,
    	        `from_company_id`,`goods_num`,`goods_total`,
    	        `bill_note`,`to_warehouse_name`,
    	        `to_company_name`,`from_company_name`,
    	        `create_user`,`create_time`,`bill_type`,`order_sn`, `bill_status` , `check_time` , `check_user`
    	        ) VALUES (
    	        '' , 58 , {$m_warehouse_id},
    	        445 , 1 , 0 ,
    	        '库管审核退货，自动生成调拨单(深圳分公司调拨至总公司),退款流水：{$return_id}' , '{$m_warehouse_name}' ,
    	        '总公司' , 'BDD深圳分公司' , '{$_SESSION['userName']}' , '{$time}' , 'M' , '{$order_sn}' , 2 , '{$time}' , '{$_SESSION['userName']}'
    	            )";
    	        $this->db()->query($sql);
    	        $bill_m_id = $this->db()->insertId();
    	        $bill_m_no = $this->create_bill_no('M',$bill_m_id);    	        
    	        //调拨明细写入
    	        $goods_total = 0;
    	        $goods_num = 0;
    	        $goods_total_jiajia=0;
    	        foreach ($order_goods as $key => $goods_info) {
    	            $goods_id = $goods_info['goods_id'];
    	            /*$sql = "SELECT `goods_sn` , `goods_name` , `num` , `jinzhong` , `caizhi` , `yanse` , `jingdu` , `jinhao` , `mingyichengben` , `zhengshuhao`, `cat_type1` FROM `warehouse_goods` WHERE `goods_id` = '{$goods_id}' ";
    	            $goods_info = $this->db()->getRow($sql);
    	            if(empty($goods_info)){
    	                throw new Exception("货品查询失败");
    	            }*/
    	            $goods_total += $goods_info['mingyichengben'];
    	            $goods_num ++;
    	            //给金耗为空的赋值 0
    	            if($goods_info['jinhao'] == ''){
    	                $goods_info['jinhao'] = 0;
    	            }
    	            $jiajiaModel = new WarehouseBillInfoYJiajialvModel(22);
					$jiajialv = $jiajiaModel->getJiajialvByStyleTypeName($goods_info['cat_type1']);
					$jiajialv = $jiajialv/1;
					$goods_total_jiajia += ($goods_info['mingyichengben'] * (1 + $jiajialv/100));

    	            $sql = "INSERT INTO `warehouse_bill_goods` (
    	            `bill_id`, `bill_no`, `bill_type`, `goods_id`,
    	            `goods_sn`, `goods_name`, `num`, `jinzhong`,
    	            `caizhi`, `yanse`,`jingdu`,`jinhao`,
    	            `sale_price`, `zhengshuhao`, `addtime`, `jiajialv`
    	            ) VALUES (
    	            {$bill_m_id}, '{$bill_m_no}', 'M', '{$goods_id}',
    	            '{$goods_info['goods_sn']}', '{$goods_info['goods_name']}', 1, '{$goods_info['jinzhong']}',
    	            '{$goods_info['caizhi']}', '{$goods_info['yanse']}', '{$goods_info['jingdu']}', {$goods_info['jinhao']},
    	            '{$goods_info['mingyichengben']}', '{$goods_info['zhengshuhao']}', '{$time}', {$jiajialv}) ";
    				$this->db()->query($sql);

    	            //变更仓库warehoue_goods 货品的所在 所在公司|仓库 = 总公司 | 调拨单入库仓
    	            $sql = "UPDATE `warehouse_goods` SET `company` = '总公司' , `warehouse` = '{$m_warehouse_name}' , `company_id` = 58, `warehouse_id` ={$m_warehouse_id}  WHERE `goods_id` = '{$goods_id}'";
    	            $this->db()->query($sql);
    	        }

    	        $sql = "UPDATE `warehouse_bill` SET `bill_no`='{$bill_m_no}',goods_num={$goods_num},goods_total={$goods_total},goods_total_jiajia ={$goods_total_jiajia} WHERE `id`={$bill_m_id}";
    	        $this->db()->query($sql);
    	        //调拨单审核记录
    	        $ip = Util::getClicentIp();
    	        $sql = "INSERT INTO `warehouse_bill_status` (`bill_id`, `bill_no`, `status`, `update_time`, `update_user`, `update_ip`) VALUES ({$bill_m_id}, '{$bill_m_no}', 2, '{$time}', '{$_SESSION['userName']}', '{$ip}') ";
    	        $this->db()->query($sql);
    	        
    	        //AsyncDelegate::dispatch("warehouse", array('event' => 'bill_M_checked', 'bill_id'=>$bill_m_id));
	        }


	        //如果货号是隐藏的，则自动生成货品的入库单+自动生成浩鹏到门店的批发单
            if($is_hidden  == 1){
                $create_time = date("Y-m-d H:i:s");
                $check_time = date("Y-m-d H:i:s",time()+50);
                $create_time_p = date("Y-m-d H:i:s",time()+100);
                $check_time_p = date("Y-m-d H:i:s",time()+150);
            //入库单
               //单据信息
                $info = array(
                    'bill_no' => '',
                    'bill_type' => 'L',
                    'goods_num' => count($order_goods),
                    'to_company_id' => $to_company_id,
                    'to_company_name' => $to_company_name,
                    'to_warehouse_id' => $to_warehouse_id,
                    'to_warehouse_name' => $to_warehouse_name,
                    'bill_note' => $bill_no_dd,
                    'goods_total' => $chengbenjia,
                    'yuanshichengben' => $chengbenjia,
                    'mingyichengbenjia'=>$mingyijia,
                    'pifajia'=>$mingyijia,
                    'shijia' => 0,
                    'put_in_type' => 1,
                    'send_goods_sn' => '',
                    'order_sn' => $order_sn,
                    'pro_id' => 592,
                    'pro_name' => '灵动',
                    'jiejia' => 1,
                    'create_time' => $time,
                    'create_user' => $_SESSION['userName'],
                    'create_ip' => Util::getClicentIp(),
                    'production_manager_name'=>'',
                    'label_price' => $biaoqianjia,
                    'company_id_from'=>1
                );

                $sql = "INSERT INTO warehouse_shipping.`warehouse_bill` (
					`id`, `bill_no`, `bill_type`, `bill_status`,
					`goods_num`, `to_warehouse_id`, `to_warehouse_name`,
					`to_company_id`,`to_company_name`, 
					`from_company_id`, `from_company_name`,
					`order_sn`,`bill_note`,
					`check_user`, `check_time`, `create_user`, `create_time`,
					`put_in_type`,`send_goods_sn`,
					 `pro_id`, `pro_name`,`jiejia`,
					 `goods_total`, `yuanshichengben`, `shijia`,
					 `production_manager_name`,`label_price_total`,`company_id_from`
					) VALUES (
					NULL, '0', 'L', 2,
					{$info['goods_num']}, '1873', '浩鹏后库', 
					'58','总公司',
					0, '', 
					'','{$info['bill_note']}', 
					'李丽珍','{$check_time}','王永红', '{$create_time}',
					'1', '20190106999',
					'{$info['pro_id']}', '{$info['pro_name']}', '1',
					'{$info['yuanshichengben']}', '{$info['yuanshichengben']}', '0',
					'','{$info['label_price']}',{$info['company_id_from']})";

                $this->db()->query($sql);
                $_id = $this->db()->insertId();
                $l_bill_id = $_id;


                //$bill_id = substr($_id, - 4);
                //$bill_no = 'D' . date('Ymd', time()) . rand(100, 999) . str_pad($bill_id, 4, "0", STR_PAD_LEFT);
                $bill_no = $this->create_bill_no('L',$l_bill_id);
                $sql = "UPDATE warehouse_shipping.warehouse_bill SET `bill_no`='{$bill_no}' WHERE `id`={$l_bill_id}";
                $res =  $this->db()->query($sql);


                $sql = "insert into warehouse_shipping.warehouse_bill_goods (id,bill_id,bill_no,bill_type,goods_id,goods_sn,goods_name,num,warehouse_id,caizhi,jinzhong,jingdu,yanse,zhengshuhao,zuanshidaxiao,in_warehouse_type,account,addtime,pandian_guiwei,sale_price,yuanshichengben,label_price)
				 select 0,'{$l_bill_id}','{$bill_no}','L',g.goods_id,g.goods_sn,g.goods_name,1,1873,g.caizhi,g.jinzhong,g.jingdu,g.yanse,g.zhengshuhao,g.zuanshidaxiao,1,1,'2019-01-01 00:00:00','0-00-0-0',g.yuanshichengbenjia,g.yuanshichengbenjia,g.biaoqianjia from warehouse_goods g where g.goods_id in (".join(',',$goods_id_arr).")";
                $res = $this->db()->query($sql);

                $sql = "insert into warehouse_shipping.warehouse_bill_pay values (0,'{$l_bill_id}','{$info['pro_id']}','{$info['pro_name']}','3','2','2','{$info['yuanshichengben']}')";
                $res = $this->db()->query($sql);



                $sql = "select wholesale_id from warehouse_shipping.jxc_wholesale j where j.sign_company='{$to_company_id}'";
                $wholesale_id  = $this->db()->getOne($sql);
                if(empty($wholesale_id)) $wholesale_id=0;


                //批发单
                $sql = "INSERT INTO `warehouse_bill` (`bill_no`, `bill_status`, `bill_type`, `goods_num` , `bill_note` , `goods_total`,`shijia` , `pifajia` , `create_user` , `create_time`,`check_user`,`check_time` ,`from_company_id` , `from_company_name`, `to_customer_id`, `to_company_id`, `to_company_name`, `to_warehouse_id`, `to_warehouse_name`, `out_warehouse_type`,`label_price_total`,`p_type`,is_invoice,sign_user,sign_time,company_id_from,jiejia) VALUES ('',2 , 'P' ,  {$info['goods_num']} , '{$info['bill_note']}' , {$info['yuanshichengben']} , {$info['pifajia']} , {$info['mingyichengbenjia']} , '王永红' , '{$create_time_p}','李丽珍','{$check_time_p}' ,'58', '总公司', {$wholesale_id}, {$info['to_company_id']}, '{$info['to_company_name']}', '88888888', '', '1', '{$info['label_price']}', '经销商备货','0','门店','{$check_time_p}',{$info['company_id_from']},1)";
                $res = $this->db()->query($sql);
                $bill_id = $this->db()->insertId();
                $bill_no= $this->create_bill_no('P',$bill_id);
                $sql = "UPDATE warehouse_shipping.`warehouse_bill` SET `bill_no`='{$bill_no}' WHERE `id`={$bill_id}";
                $res = $this->db()->query($sql);
                $sql = "insert into warehouse_shipping.warehouse_bill_goods (id,bill_id,bill_no,bill_type,goods_id,goods_sn,goods_name,num,caizhi,jinzhong,jingdu,yanse,zhengshuhao,zuanshidaxiao,in_warehouse_type,account,addtime,pandian_guiwei,pifajia,sale_price,shijia,dep_settlement_type,settlement_time,management_fee,label_price)
                select 0,'{$bill_id}','{$bill_no}','P',g.goods_id,g.goods_sn,g.goods_name,1,g.caizhi,g.jinzhong,g.jingdu,g.yanse,g.zhengshuhao,g.zuanshidaxiao,g.put_in_type,0,'{$create_time_p}','0-00-0-0',g.mingyichengben,g.yuanshichengbenjia,g.jingxiaoshangchengbenjia-g.management_fee,'2','{$check_time_p}',g.management_fee,g.biaoqianjia  from warehouse_goods g where g.goods_id in (".join(',',$goods_id_arr).")";
                $res = $this->db()->query($sql);


                //库存隐藏改成显示
                $sql ="update warehouse_shipping.warehouse_goods g set g.hidden='0',addtime='{$check_time}' where g.goods_id in (".join(',',$goods_id_arr).")";
                $res = $this->db()->query($sql);


            }


	        $status = true;
	        $msg = "操作成功";
	    } catch (Exception $e) {
	        $status = false;
	        $bill_no = '';
	        $goods_id = '';
	        $bill_m_no = '';
	        $msg = $e->getMessage();
	    }
	    return array(
	        'status' => $status,
	        'msg' => $msg,
	        'bill_no' => $bill_no,
	        'bill_m_no' => isset($bill_m_no)?$bill_m_no:'',
	        'goods_id' => $goods_id_str
	    );
	}
	public function create_bill_no($type, $bill_id = '1')
	{
	    $bill_id = substr($bill_id, -4);
	    $bill_no = $type . date('Ymd', time()) . rand(100, 999) . str_pad($bill_id, 4,
	        "0", STR_PAD_LEFT);
	    return $bill_no;
	}
	/**
	 * 检查是否有可用的S单
	 * @param unknown $where
	 */
	public function checkHasBillByWhere($where){
	    $sql = "select count(*) as total FROM warehouse_bill where 1=1";
	    if(isset($where['order_sn'])){
	        $sql .= " And order_sn='{$where['order_sn']}'";
	    }
	    if(isset($where['bill_type'])){
	        $sql .= " And bill_type='{$where['bill_type']}'";
	    }
	    if(isset($where['bill_status'])){
	        $sql .= " And bill_status={$where['bill_status']}";
	    }
	    return $this->db()->getOne($sql);
	}
	/**
	 * 取消销售单 --- JUAN(将warehouse里api的OprationBillD功能移到此处逻辑未做改动)
	 *
	 * @param unknown $order_sn
	 * @param unknown $detail_id
	 * @param unknown $detail_id
	 * （仅支持订单退货调用，没事儿别动哦~ 因为取消销售单后有订单和货品解绑动作，一般用不到）
	 */
	public function cancelBillS($order_sn, $detail_id)
	{
	    $status = true;
	    $msg = '';
	    $salemodel = new SalesModel(27);
	
	    try {
	        if (empty($order_sn) || empty($detail_id)) {
	            throw new Exception("缺少参数！");
	        }
	
	        $sql = "SELECT COUNT(1) FROM warehouse_shipping.warehouse_goods WHERE `order_goods_id` = " . $detail_id;
	        if (! $this->db()->getOne($sql)) {
	            throw new Exception($detail_id . " 没有绑定的货品，请检查。");
	        }
	
	        //如果发货状态是为发货，需要验证是否有对应的销售单
	        //$send_good_status = $salemodel->getSendGoodStatusByOrderSn($order_sn);
	        if($send_good_status !=1){
	            // 找到对应的销售单
	            $sql = "SELECT id FROM warehouse_shipping.warehouse_bill WHERE `order_sn` = '" . $order_sn . "' AND `bill_status` = 1 AND `bill_type` = 'S'";
	            $bill_id = $this->db()->getOne($sql);
	            if(empty($bill_id)){
	                throw new Exception( "未找到对应销售单。");
	            }
	            // 在此不判断是否有有效的销售单，因为有可能一个订单两件货都在申请，第一个申请通过的时候就取消了销售单了。
	            // 把已保存的相关联订单的销售单置为取消
	            $sql = "UPDATE warehouse_shipping.warehouse_bill SET `bill_status` = 3 WHERE `id` = " . $bill_id;
	            $res = $this->db()->query($sql);
	            if(!res){
	                throw new Exception( "单据状态更新失败");
	            }
	
	            // 把取消的销售单中的货品置为库存状态
	            $sql = "UPDATE warehouse_shipping.warehouse_goods as wg,warehouse_shipping.warehouse_bill_goods as wbg SET wg.`is_on_sale` = 2 WHERE wg.goods_id = wbg.goods_id and wbg.`bill_id` = " . $bill_id;
	            $res = $this->db()->query($sql);
	            if(!res){
	                throw new Exception( "库存状态更新失败");
	            }
	        }
            // 把所传的detail_id绑定的货品解绑,销售单中的货品置为库存状态
            $sql = "UPDATE warehouse_shipping.warehouse_goods SET `order_goods_id` = 0,`is_on_sale` = 2 WHERE `order_goods_id` = '" . $detail_id."'";
            $res = $this->db()->query($sql);
            if(!res){
                throw new Exception( "货品解绑失败");
            }
	        
	         
	    } catch (Exception $e) {
	        $status = false;
	        $msg = $e->getMessage();
	    }
	    return array('status'=>$status,'msg'=>$msg);
	}
	
	/**
	 * 通过货号绑定\解绑货品
	 * @param unknown $bind_type
	 * @param unknown $order_goods_id
	 * @param unknown $goods_id
	 */
	public function bindGoodsInfoByGoodsId($bind_type,$order_goods_id,$goods_id=0){
	    $set = "";
	    $where = "";
	    if(($bind_type != 1 && $bind_type != 2 )|| empty($order_goods_id)){
	        return false;
	    }
	
	    if($bind_type == 1){
	        if(empty($goods_id)){
	            return false;
	        }
	        $set .= " `order_goods_id` = '" . $order_goods_id . "' ";
	        $where .= " `goods_id` = '" . $goods_id . "' ";
	    }else if($bind_type == 2){
	        $set .= " `order_goods_id` = '' ";
	        $where .= " `order_goods_id` = '" . $order_goods_id . "' ";
	    }
	
	    if ($bind_type == 1) {//绑定
	        $sql = "update warehouse_shipping.warehouse_goods set " . $set . "  WHERE " . $where . " and is_on_sale = 2 ";
	    } else {
	        $sql = "update warehouse_shipping.warehouse_goods set " . $set . "  WHERE " . $where;
	    }
	    return $this->db()->query($sql);
	}	
	/**
	 * 查询绑定订单的仓储货品信息
	 * @param unknown $order_goods_id
	 */
	public function getWarehouseGoodsInfo($order_goods_id,$fields="*"){
	    $sql = "SELECT * FROM warehouse_shipping.warehouse_goods WHERE `order_goods_id` = '{$order_goods_id}'"; //暂时用＊号
	    return $this->db()->getRow($sql);
	}
	/**
	 * 更改warehosue表记录
	 * @param array $data
	 * @param string $where
	 */
	public function updateWarehouseGoods($data,$where){
	    $sql = $this->updateSql('warehouse_goods',$data, $where);
	    return $this->db()->query($sql);
	}

    /**
     * 根据订单号查询该订单最新的S单，取出出库公司；
     * @param unknown $order_sn
     */
    public function getFromCompanyIdByOrder_sn($order_sn){

        $sql = "SELECT `from_company_id` FROM warehouse_shipping.warehouse_bill WHERE `bill_type` = 'S' AND `bill_status` = 2 AND `order_sn` = '{$order_sn}' ORDER BY `check_time` DESC LIMIT 1";
        return $this->db()->getOne($sql);
    }
    
    //判断订单是否绑定销售单
    public function getWarehouseBillGoods($order_sn){
    	$sql="SELECT bg.id FROM warehouse_shipping.warehouse_bill AS wb,warehouse_shipping.warehouse_bill_goods AS bg WHERE wb.id=bg.bill_id AND bg.bill_type='S' AND wb.bill_status = 2 AND wb.order_sn='{$order_sn}' LIMIT 1";
    	return $this->db()->getRow($sql);
    }

    /**
     * 根据订单号查询该订单所有的S单；
     * @param unknown $order_sn
     */
    public function getBillSidByOrder_sn($order_sn){

        $sql = "SELECT `id` FROM `warehouse_shipping`.`warehouse_bill` WHERE `bill_type` = 'S' AND `bill_status` = 2 AND `order_sn` = '{$order_sn}'";
        return $this->db()->getAll($sql);
    }

    /**
     * 根据最新的S单取出该S下的所以货号；
     * @param unknown $bill_id
     */
    public function getBillSInfoBy_ids($ids){

        $sql = "SELECT `wg`.`goods_id`,wg.goods_sn,`wg`.`goods_name` FROM `warehouse_shipping`.`warehouse_bill` `wb` INNER JOIN `warehouse_shipping`.`warehouse_bill_goods` `wbg` ON `wb`.`id` = `wbg`.`bill_id` INNER JOIN `warehouse_shipping`.`warehouse_goods` `wg` ON `wbg`.`goods_id` = `wg`.`goods_id` WHERE `wb`.`id` in({$ids}) AND `wg`.`is_on_sale` = 3";
         
        return $this->db()->getAll($sql);
    }
    /**
     * 根据订单号查询绑定的S单货品
     */
    public function  getBillSInfoByOrderSn($order_sn,$detail_id){
        $sql = "SELECT distinct b.goods_id,b.goods_sn,b.goods_name,b.detail_id,b.shijia as return_price FROM warehouse_bill a INNER JOIN warehouse_bill_goods  b on a.id=b.bill_id
        where a.`order_sn` = '{$order_sn}' and b.detail_id='{$detail_id}' AND a.`bill_type` = 'S' and a.bill_status=2";
        return $this->db()->getAll($sql);
    }


    /**
     * 根据订单号明细查询绑定的S单货品
     */
    public function  getBillSInfoByOrderSn_New($order_sn,$detail_id){    	
        $sql = "select bg.goods_id,bg.goods_sn,g.goods_name,g.order_goods_id as detail_id,bg.shijia as return_price  from  warehouse_shipping.warehouse_bill_goods bg,warehouse_shipping.warehouse_bill b,warehouse_shipping.warehouse_goods g where  bg.bill_id=b.id and b.bill_type='S' and b.bill_status=2 and bg.goods_id=g.goods_id and g.order_goods_id='{$detail_id}' and b.order_sn='{$order_sn}' and g.is_on_sale=3 order by bg.id desc limit 1";
        return $this->db()->getAll($sql);
    }


    /**
     * 确认所选的货号未绑定订单货品；
     * @param unknown $bill_id
     */
    public function checkOrderGoodsId_isnull($goods_id){

        $sql = "SELECT `order_goods_id` FROM `warehouse_shipping`.`warehouse_goods` WHERE `goods_id` = {$goods_id}";
        return $this->db()->getOne($sql);
    }

    /**
     * 如果所选的有货品，且未绑定订单货品则将订单的order_goods_id 写入到所选货品里；
     * @param unknown $goods_id，$order_goods_id
     */
    public function updateBangDingOrderGoods_id($goods_id, $order_goods_id){
        if(!$goods_id){
            return false;
        }
        $sql = "UPDATE `warehouse_shipping`.`warehouse_goods` SET `order_goods_id` = '{$order_goods_id}' WHERE `goods_id` = '{$goods_id}'";
        return $this->db()->query($sql);
    }
    
    public function getGoodsPfj($order_id){
        if(!$order_id){
            return 0;
        }
    	$sql="select SUM(g.shijia) as pfj from warehouse_shipping.warehouse_bill_goods as g left join app_order.app_order_details as a on a.id=g.detail_id where a.order_id={$order_id} and a.delivery_status=5";
    	$row=$this->db()->getRow($sql);
    	if(empty($row)){
    		return 0;
    	}else{
    		return $row['pfj'];
    	}
    }

    /**
     * 根据公司id 获取当前公司底下的仓库 (warehouse_rel)有效的仓库
     */
    public function getWarehouseByCompany($company_id){
        $sql = "SELECT `b`.`id`,`b`.`name`,`b`.`code` FROM `warehouse_rel` AS `a` INNER JOIN `warehouse` AS `b` ON `a`.`company_id`={$company_id} AND `a`.`warehouse_id` = `b`.`id` and b.is_delete = 1";
        return $this->db()->getAll($sql);
    }
    //判断仓库是否有效
    public function getWarehouseIsDelete($warehouseCode){
        $sql="SELECT count(a.id) as count FROM `warehouse_shipping`.warehouse AS a WHERE a.id={$warehouseCode} and a.is_delete=1";
        return $this->db()->getAll($sql);
    }
    //判断公司是否有效
    public function getcompanyIsDelete($companyCode){
        $sql="select count(id) as count from `cuteframe`.company where is_deleted=0 and id={$companyCode} ";
        return $this->db()->getAll($sql);
    }
}

?>