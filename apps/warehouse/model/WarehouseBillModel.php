<?php
/**
 *  -------------------------------------------------
 *   @file		: WarehouseBillModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-17 22:41:39
 *   @update	:
 *  -------------------------------------------------
 */
class WarehouseBillModel extends Model
{
    function __construct($id = null, $strConn = "")
    {
        $this->_objName = 'warehouse_bill';
        $this->_dataObject = array(
            "id" => "序号",
            "bill_no" => "单据编号",
            "bill_type" => "单据类型",
            "bill_status" => "数据字典：仓储单据状态（warehouse_in_status）/ 盘点单状态（warehouse.pandian_plan）",
            "order_sn" => "订单号",
            "goods_num" => "货品总数",
            "put_in_type" => "入库方式",
            "jiejia" => "是否结价",
            "send_goods_sn" => "送货单号",
            "pro_id" => "供应商ID",
            "pro_name" => "供应商名称",
            "goods_total" => "货总金额",
            "goods_total_jiajia" => "加价之后的货总金额",
            "shijia" => "实际销售价格",
            "to_warehouse_id" => "入货仓ID (盘点单，该列存盘点的仓库,退货返厂单时，该字段记录出库仓)",
            "to_warehouse_name" => "入货仓名称 (盘点单，该列存盘点的仓库)",
            "to_company_id" => "入货公司ID",
            "to_company_name" => "入货公司名称",
            "from_company_id" => "出货公司id",
            "from_company_name" => "出货公司名称",
            "bill_note" => "备注",
            "yuanshichengben" => "原始成本",
            "check_user" => "审核人",
            "check_time" => "审核时间",
            "create_user" => "制单人",
            "create_time" => "制单时间",
            "fin_check_status" => "财务审核状态:见数据字典",
            "fin_check_time" => "财务审核时间",
            "to_customer_id" => "配送公司id",
        	"confirm_delivery" => "确认发货",
        	"is_tsyd" => "是否经销商天生一对订单",
        	"sign_user" => '签收人',
            "sign_time" => '签收时间'
        );
        

        parent::__construct($id, $strConn);
    }

    function pageList($where, $page, $pageSize = 10, $useCache = true,$total_num='')
    {
        $sasdt = '';
        $sg_y = '';
        if($where['dep_settlement_type'] != '' && $where['bill_type'] == 'P'){
            $sasdt = " inner join warehouse_bill_goods g on g.bill_id = b.id";
            $sg_y = " group by `b`.id ";
        }
        if($where['settlement_time_start'] != '' && $where['bill_type'] == 'P'){
            $sasdt = " inner join warehouse_bill_goods g on g.bill_id = b.id";
            $sg_y = " group by `b`.id ";
        }
        if($where['settlement_time_end'] != '' && $where['bill_type'] == 'P'){
            $sasdt = " inner join warehouse_bill_goods g on g.bill_id = b.id";
            $sg_y = " group by `b`.id ";
        }
        $orderby= "  group by `b`.id ORDER BY `b`.`id`  DESC ";
        if ($where['goods_id'] == '') {

            $lab = ['id', 'bill_no', 'bill_type', 'bill_status', 'goods_num', 'order_sn',
                'send_goods_sn', 'from_company_name', 'to_company_name', 'to_warehouse_name',
                'create_user', 'check_user', 'create_time', 'check_time','company_from','company_id_from','from_bill_id', 'bill_note',
                'goods_total', 'goods_total_jiajia', 'shijia','jiejia', 'pro_name', 'pro_id','is_tsyd','to_customer_id','out_warehouse_type','p_type','fin_check_status','fin_check_user','fin_check_time'];

            $sel = implode('`,b.`', $lab);
            $sel = "b.`" . $sel . "`";

            //$sql = "SELECT ".$sel.",wg.`pro_id` as prc_id,wg.`pro_name` as prc_name FROM `warehouse_bill` AS `b`,`warehouse_bill_goods` as `g`,CONCAT('warehouse_bill_info_',lower(b.bill_type)) as `wg` where `g`.`bill_id`=`b`.`id` and `b`.`id`=`wg`.`bill_id`";

            //$sql = "SELECT ".$sel.",wg.`prc_id`,wg.`prc_name` FROM `warehouse_bill` AS `b` left join `warehouse_bill_goods` as `g` on `g`.`bill_id`=`b`.`id` left join `warehouse_goods` as `wg` on  `g`.`goods_id`=`wg`.`goods_id` where 1 ";
            if ($where['account_type'] != "") {
                $sql = "SELECT " . $sel .
                 ",wg.`prc_id`,wg.`prc_name`,wg.mingyichengben,SUM(wg.`yuanshichengbenjia`) AS total_chengben FROM `warehouse_bill` AS `b` left join `warehouse_bill_goods` as `g` on `g`.`bill_id`=`b`.`id` left join `warehouse_goods` as `wg` on  `g`.`goods_id`=`wg`.`goods_id` left join `warehouse_bill_pay` as wp on g.`bill_id`=wp.`bill_id` where 1 ";
            } else {
                if ($where['mohao'] != "" || $where['account_type'] != ""||$where['p_order_sn']!='' || $where['bc_sn'] !="") {
                    $sql = "SELECT " . $sel .
                    ",wg.`prc_id`,wg.`prc_name`,wg.mingyichengben,SUM(wg.`yuanshichengbenjia`) AS total_chengben FROM `warehouse_bill` AS `b` left join `warehouse_bill_goods` as `g` on `g`.`bill_id`=`b`.`id` left join `warehouse_goods` as `wg` on  `g`.`goods_id`=`wg`.`goods_id` where 1 ";
                }else{
                    $sql = "SELECT " . $sel .
                    //",wg.`prc_id`,wg.`prc_name`,SUM(wg.`yuanshichengbenjia`) AS total_chengben FROM `warehouse_bill` AS `b` left join `warehouse_bill_goods` as `g` on `g`.`bill_id`=`b`.`id` left join `warehouse_goods` as `wg` on  `g`.`goods_id`=`wg`.`goods_id` where 1 ";
                    //",(select sum(g.yuanshichengbenjia)  from warehouse_bill_goods bg,warehouse_goods g where bg.goods_id=g.goods_id and bg.bill_id=b.id ) as  total_chengben from warehouse_bill b where 1 ";
                    ", b.goods_total as  total_chengben from warehouse_bill b {$sasdt} where 1 ";
                    $orderby= " {$sg_y} ORDER BY `b`.`id`  DESC ";
                    if($where['pay_id'] != ""){
                         $sql="select b.id,b.bill_no,b.bill_type,b.bill_status,b.goods_num,b.order_sn,b.send_goods_sn,b.from_company_name,b.to_company_name,b.to_warehouse_name,
                            b.create_user,b.check_user,b.create_time,b.check_time,b.company_from,b.company_id_from,b.from_bill_id,b.bill_note,
                            b.goods_total,b.goods_total_jiajia,b.company_id_from,b.shijia,b.pro_name,b.pro_id,b.is_tsyd,b.to_customer_id,b.out_warehouse_type,b.p_type,b.goods_total as  total_chengben from warehouse_bill b {$sasdt}
                            left join app_order.base_order_info o on b.order_sn=o.order_sn where 1";
                    }
                } 
                
            }
           
            //$sql = "select `b`.*,g.`goods_id`,wg.`prc_id`,wg.`prc_name` from `{$this->table()}` as `b`,warehouse_bill_goods as `g`,warehouse_goods as wg where `g`.`bill_id`=`b`.`id` and `g`.`goods_id`=`wg`.`goods_id`";//echo $sql;exit;
        } else {

            //$sql = "select `b`.*,g.`goods_id`,wg.`prc_id`,wg.`prc_name` from `{$this->table()}` as `b`,warehouse_bill_goods as `g`,warehouse_goods as wg where `g`.`bill_id`=`b`.`id` and `g`.`goods_id`=`wg`.`goods_id`";//echo $sql;exit;
            $sql = "select `b`.*,g.`goods_id`,wg.`prc_id`,wg.`prc_name`,wg.mingyichengben,wp.`pay_method`,SUM(wg.`yuanshichengbenjia`) AS total_chengben from `{$this->table()}` as `b`,warehouse_bill_goods as `g`,warehouse_goods as wg,`warehouse_bill_pay` as `wp` where `g`.`bill_id`=`b`.`id` and `g`.`bill_id` = `wp`.`bill_id` and `g`.`goods_id`=`wg`.`goods_id` "; //echo $sql;exit;
            //屏蔽发货生成的单据
           
        }
        $str = '';

        if(isset($where['hidden']) && $where['hidden'] != ''){
            $str .= " and `b`.hidden = ".$where['hidden'];
        }


        if ($where['send_goods_sn'] != "") {
            $str .= " and `b`.send_goods_sn like \"%" . addslashes($where['send_goods_sn']) .
                "%\"";
        }
        if ($where['bill_no'] != "") {
            $str .= " and `b`.bill_no = '" . addslashes($where['bill_no']) . "'";
        }
        if ($where['create_user'] != "") {
            $str .= " and `b`.create_user = '"   . addslashes($where['create_user'])."'";
        }
        if ($where['bill_type'] !== "") {
            $str .= " and `b`.bill_type = '" . $where['bill_type'] . "' ";
        }
        if ($where['confirm_delivery'] !== "" && $where['bill_type'] == "P") {
        	$str .= " and `b`.confirm_delivery = " . $where['confirm_delivery'];
        }
        if ($where['p_order_sn'] !== "" && $where['bill_type'] == "P") {
        	$str .= " and `g`.order_sn = '" . $where['p_order_sn']."'";
        }
        if ($where['out_warehouse_type'] !== "" && $where['bill_type'] == "P") {
            $str .= " and `b`.out_warehouse_type = '" . $where['out_warehouse_type']."'";
        }
        if ($where['dep_settlement_type'] !== "" && $where['bill_type'] == "P") {
            $str .= " and `g`.dep_settlement_type = '" . $where['dep_settlement_type']."'";
        }
        if ($where['is_tsyd'] !== "" && $where['bill_type'] == "P") {
        	$str .= " and `b`.is_tsyd = '" . $where['is_tsyd']."'";
        }
        if ($where['order_sn'] !== "") {
            $str .= " and `b`.order_sn = '" . $where['order_sn'] . "' ";
        }
        if ($where['bill_status'] !== "") {
            $str .= " and `b`.bill_status = " . $where['bill_status'];
        }
        if ($where['fin_check_status'] !== "") {
            $str .= " and `b`.fin_check_status = " . $where['fin_check_status'];
        } 
        if ($where['jiejia'] !== "") {
            $str .= " and `b`.jiejia = '" . $where['jiejia'] ."'";
        }  
        //布产单号
        if ($where['bc_sn'] !== "") {
            $str .= " and `wg`.buchan_sn = '" . $where['bc_sn']."'";
        }

        if (!empty($where['to_customer_id'])) {
        	$str .= " and `b`.to_customer_id = " . $where['to_customer_id'];
        }      

        if($where['company_id_list'] != ''){
            if($where['from_company_id'] != 0 || $where['to_company_id'] != 0){
                if ($where['from_company_id'] != 0) {
                    $str .= " and `b`.from_company_id = " . $where['company_id_list'];
                }
                if ($where['to_company_id'] != 0) {
                    $str .= " and `b`.to_company_id = " . $where['company_id_list'] . ' ';
                }
            }else{
                $str .= " and (`b`.from_company_id = " . $where['company_id_list']." or `b`.to_company_id = ".$where['company_id_list'].")";
            }
        }else{
            if ($where['from_company_id'] != 0) {
                $str .= " and `b`.from_company_id = " . $where['from_company_id'];
            }
            if ($where['to_company_id'] != 0) {
                $str .= " and `b`.to_company_id = " . $where['to_company_id'] . ' ';
            }
        }
        //二、H单权限管控
        //总公司的能看所有的H单，
        //经销商，个体，直营看出库公司是自己的H单，
        //省代能看出库公司是自己以及下属省代的H单（并且入库公司非总公司），
        ////用户级别1.总公司 、2.经销商，个体，直营 、3.省代 
        if($_SESSION['userType']<>1 && $where['level'] <> 1 && !empty($where['dataCompInfo'])){
            $dataCompInfo = array_pop($where['dataCompInfo']);
            $cp_str = '';
            if(!empty($where['dataCompInfo']) && $where['level'] == 3){
                $cp_str = "(`b`.from_company_id in(".implode(',', $where['dataCompInfo']).") and `b`.`to_company_id` <> '58') or ";
            }
            if($dataCompInfo) $str .=" and if(b.bill_type = 'H',(".$cp_str." `b`.`from_company_id` = ".$dataCompInfo." or b.to_company_id=".$dataCompInfo."),'1')";
        }
        //用户当前所在公司=总公司or浩鹏，则单据列表能看到所有B单,用户当前所在公司=B单的出库公司，则单据列表能看到B单出库公司是自己的,用户当前所在公司≠B单的出库公司，则单据列表看不到B单
        if($_SESSION['userType']<>1 && $where['now_compid'] <> '58'){//58总公司
            $str .= " and if(b.bill_type = 'B', `b`.`from_company_id` = ".$where['now_compid'].",'1')";
        }
        if ($where['to_warehouse_id'] != 0) {
            $str .= " and `b`.to_warehouse_id = " . $where['to_warehouse_id'];
        }
        if ($where['processors'] != "") {
            $str .= " and `b`.`pro_id`={$where['processors']} ";
        }
        if ($where['time_start'] !== "") {
            $str .= " and `b`.`create_time`>='{$where['time_start']} 00:00:00'";
        }
        if ($where['time_end'] !== "") {
            $str .= " and `b`.`create_time` <= '{$where['time_end']} 23:59:59'";
        }
        if ($where['check_time_start'] !== "") {
            $str .= " and `b`.`check_time`>='{$where['check_time_start']} 00:00:00'";
        }
        if ($where['check_time_end'] !== "") {
            $str .= " and `b`.`check_time` <= '{$where['check_time_end']} 23:59:59'";
        }
        if ($where['settlement_time_start'] != "") {
            $str .= " and `g`.`settlement_time`>='{$where['settlement_time_start']} 00:00:00'";
        }
        if ($where['settlement_time_end'] != "") {
            $str .= " and `g`.`settlement_time` <= '{$where['settlement_time_end']} 23:59:59'";
        }
        if ($where['bill_note'] != "") {
            $str .= " and `b`.bill_note like \"%" . addslashes($where['bill_note']) . "%\"";
        }
        if ($where['mohao'] != "") {
            $str .= " and wg.`mo_sn`='{$where['mohao']}'";
        }
        if ($where['put_in_type'] != "") {
            $str .= " and b.`put_in_type`= " . $where['put_in_type'];
        }
        if ($where['chuku_type'] != "") {
            $str .= " and b.`tuihuoyuanyin`= " . $where['chuku_type'];
        }
        if ($where['account_type'] != "") {
            $str .= " and wp.`pay_method`=" . $where['account_type'];
        }
        if(!empty($where['pay_id'])){
            $str .= " and o.order_pay_type='" . $where['pay_id'] ."'";            
        }

        if ($where['fin_check_time_start'] !== "") {
            $str .= " and `b`.`fin_check_time`>='{$where['fin_check_time_start']} 00:00:00'";
        }
        if ($where['fin_check_time_end'] !== "") {
            $str .= " and `b`.`fin_check_time` <= '{$where['fin_check_time_end']} 23:59:59'";
        }
		if($_SESSION['userName']!="董小华")
            $str .= " and `b`.`bill_no` not in ('B201510125955187','B201510203345215','B201510235555232') " ;
		
        if(isset($where['laiyuan'])&&$where['laiyuan']=='a'){
        	$sql.=" and ((b.bill_type='L' and b.from_bill_id !='') or (b.bill_type='S' and b.company_id_from !='') )";
            $sql .= $str;
            $orderby= " group by `b`.id ORDER BY `b`.`check_time` DESC";
        }elseif(isset($where['laiyuan'])&&$where['laiyuan']=='hz'){
            $sql .= $str;
            $orderby= " group by `b`.id ORDER BY `b`.`check_time` DESC";
        }else{
		   if($_SESSION['userName']=="lily") {
			  $sql.=" and b.to_customer_id=63 and (bill_type='P' or bill_type='H') ";
		   }	
          $sql.=" and (b.from_bill_id is null  or b.from_bill_id = 0 ) ";
          $sql .= $str;
        }
        $sql.=$orderby;
        //echo $sql;exit;

        $data = $this->getPageList($sql, array(), $page, $pageSize, $useCache);
        /*
        if($total_num ==1){
            $sql1 ="SELECT SUM(wb.goods_num) as total_num,SUM(wb.total_chengben) as total_price,SUM(wb.shijia) as total_shijia FROM (".$sql.") as wb";
            $total = $this->db()->getRow($sql1);
            $data['total_num'] = $total['total_num']?$total['total_num']:0;
            $data['total_price'] = $total['total_price']?$total['total_price']:0.00;
            $data['total_shijia'] = $total['total_shijia']?$total['total_shijia']:0.00;
        }
        */
        return $data;
    }

    public function goodsBillList($where, $page, $pageSize = 10, $useCache = true,$total_num='')
    {
        $sql = "SELECT `g`.`goods_id`,`g`.`goods_sn`,`g`.`goods_name`,`g`.`num`,`g`.`bill_id`,`g`.`bill_no`,`g`.`bill_type`,`b`.`to_warehouse_name`, `b`.`bill_status` ,`b`.`to_company_name`,`b`.`from_company_name`,`b`.`send_goods_sn`,`b`.`create_user`,`b`.`create_time`,`b`.`check_user`,`b`.`check_time`,`b`.`fin_check_time`,`b`.`bill_note`,`b`.`pro_id`,`b`.`pro_name`,`b`.`order_sn`,`b`.`goods_num`,`b`.`company_from`,`b`.`company_id_from`,`b`.`from_bill_id`,b.to_customer_id,b.fin_check_status,b.fin_check_user,g.sale_price,g.shijia,wg.`yuanshichengbenjia`,wg.`mingyichengben` FROM `warehouse_bill_goods` AS `g` LEFT JOIN `warehouse_bill` AS `b` ON `g`.`bill_id` = `b`.`id` LEFT JOIN `warehouse_goods` as wg on wg.goods_id = g.goods_id ";

        if ($where['account_type'] != "") {
            $sql .= " left join `warehouse_bill_pay` as wp on wp.bill_id = b.id WHERE wp.`pay_method`=" .
                $where['account_type'];
        } else {
            $sql .= " where 1 ";
        }

        if(isset($where['hidden']) && $where['hidden'] != ''){
            $sql .= " and `b`.hidden = ".$where['hidden'];
        }


        if ($where['goods_id'] != "") {
            $sql .= " AND `g`.goods_id = '" . $where['goods_id'] . "'";
        }
        if ($where['goods_sn'] != "") {
            $sql .= " AND `g`.goods_sn = '" . $where['goods_sn'] . "'";
        }
        if ($where['send_goods_sn'] != "") {
            $sql .= " AND `b`.send_goods_sn like \"%" . $where['send_goods_sn'] . "%\"";
        }
        if ($where['bill_no'] != "") {
            $sql .= " AND `b`.bill_no like \"%" . addslashes($where['bill_no']) . "%\"";
        }
        if ($where['bill_type'] !== "") {
            $sql .= " AND `b`.bill_type = '" . $where['bill_type'] . "'";
        }
        if ($where['confirm_delivery'] !== "" && $where['bill_type'] == "P") {
        	$sql .= " and `b`.confirm_delivery = " . $where['confirm_delivery'];
        }
        if ($where['order_sn'] !== "") {
            $sql .= " AND `b`.order_sn = '" . $where['order_sn'] . "' ";
        }
        if ($where['out_warehouse_type'] != "") {
            $sql .= " AND `b`.out_warehouse_type = '" . $where['out_warehouse_type'] . "' ";
        }
        if ($where['dep_settlement_type'] != "") {
            $sql .= " AND `g`.dep_settlement_type = '" . $where['dep_settlement_type'] . "' ";
        }
        if ($where['bill_status'] !== "") {
            $sql .= " AND `b`.bill_status = " . $where['bill_status'];
        }
        if ($where['fin_check_status'] !== "") {
            $str .= " and `b`.fin_check_status = " . $where['fin_check_status'];
        }           
        if($where['company_id_list'] != ''){
            if($where['from_company_id'] != 0 || $where['to_company_id'] != 0){
                if ($where['from_company_id'] != 0) {
                    $sql .= " and `b`.from_company_id = " . $where['company_id_list'];
                }
                if ($where['to_company_id'] != 0) {
                    $sql .= " and `b`.to_company_id = " . $where['company_id_list'] . ' ';
                }
            }else{
                $sql .= " and (`b`.from_company_id = " . $where['company_id_list']." or `b`.to_company_id = ".$where['company_id_list'].")";
            }
        }else{
            if ($where['from_company_id'] != 0) {
                $sql .= " and `b`.from_company_id = " . $where['from_company_id'];
            }
            if ($where['to_company_id'] != 0) {
                $sql .= " and `b`.to_company_id = " . $where['to_company_id'] . ' ';
            }
        }
        //二、H单权限管控
        //总公司的能看所有的H单，
        //经销商，个体，直营看出库公司是自己的H单，
        //省代能看出库公司是自己以及下属省代的H单（并且入库公司非总公司），
        ////用户级别1.总公司 、2.经销商，个体，直营 、3.省代 
        if($where['level'] <> 1 && !empty($where['dataCompInfo'])){
            $dataCompInfo = array_pop($where['dataCompInfo']);
            $cp_str = '';
            if(!empty($where['dataCompInfo']) && $where['level'] == 3){
                $cp_str = "(`b`.from_company_id in(".implode(',', $where['dataCompInfo']).") and `b`.`to_company_id` <> '58') or ";
            }
            if($dataCompInfo) $str .=" and if(b.bill_type = 'H',(".$cp_str." `b`.`from_company_id` = ".$dataCompInfo."),'1')";
        }
        
        if ($where['to_warehouse_id'] != 0) {
            $sql .= " AND `b`.to_warehouse_id = " . $where['to_warehouse_id'];
        }
        if ($where['bill_note'] != "") {
            $sql .= " and `b`.bill_note like \"%" . addslashes($where['bill_note']) . "%\"";
        }
        if ($where['time_start'] !== "") {
            $sql .= " and `b`.`create_time`>='{$where['time_start']} 00:00:00'";
        }
        if ($where['time_end'] !== "") {
            $sql .= " and `b`.`create_time` <= '{$where['time_end']} 23:59:59'";
        }
        if ($where['settlement_time_start'] !== "" && $where['bill_type'] == "P") {
            $str .= " and `g`.`settlement_time`>='{$where['settlement_time_start']} 00:00:00'";
        }
        if ($where['settlement_time_end'] !== "" && $where['bill_type'] == "P") {
            $str .= " and `g`.`settlement_time` <= '{$where['settlement_time_end']} 23:59:59'";
        }
        if ($where['check_time_start'] !== "") {
            $sql .= " and `b`.`check_time`>='{$where['check_time_start']} 00:00:00'";
        }
        if ($where['check_time_end'] !== "") {
            $sql .= " and `b`.`check_time` <= '{$where['check_time_end']} 23:59:59'";
        }
        if ($where['mohao'] != "") {
            $sql .= " and  wg.`mo_sn`='{$where['mohao']}'";
        }
        if ($where['put_in_type'] != "") {
            $sql .= " and g.`in_warehouse_type`=" . $where['put_in_type'];
        }

        if ($where['chuku_type'] != "") {
            $str .= " and b.`tuihuoyuanyin`= " . $where['chuku_type'];
        }
        
        if ($where['to_customer_id'] != 0) {
        	$sql .= " AND `b`.to_customer_id = " . $where['to_customer_id'];
        }
		if($_SESSION['userName']!="董小华")
            $sql .= " and `b`.`bill_no` not in ('B201510125955187','B201510203345215','B201510235555232') " ;
        
		
       if(isset($where['laiyuan'])&&$where['laiyuan']=='a'){
			$sql.=" and ((b.bill_type='L' and b.from_bill_id !='') or (b.bill_type='S' and b.company_id_from !='') )";
			$sql .= " ORDER BY `b`.`check_time` DESC";
        }elseif(isset($where['laiyuan'])&&$where['laiyuan']=='hz'){
        	$sql .= " ORDER BY `b`.`check_time` DESC";
        }else{
		  if($_SESSION['userName']=="lily") {
			 $sql.=" and b.to_customer_id=63 and (bill_type='P' or bill_type='H') ";
		  }
          $sql.=" and b.from_bill_id is null ";
          $sql .= " ORDER BY `b`.`create_time` DESC";
        }




        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        if($total_num ==1){
             $sql1 ="SELECT SUM(wb.goods_num) as total_num,SUM(wb.yuanshichengbenjia) as total_price,SUM(wb.shijia) as total_shijia FROM (".$sql.") as wb";
            $total = $this->db()->getRow($sql1);
            $data['total_num'] = $total['total_num']?$total['total_num']:0;
            $data['total_price'] = $total['total_price']?$total['total_price']:0.00;
            $data['total_shijia'] = $total['total_shijia']?$total['total_shijia']:0.00;
        }
        return $data;
    }

    public function getPageList($sql, $params = array(), $page = 1, $pageSize = 20, $useCache = false)
    {
        try {
            //$countSql = "SELECT COUNT(*) as count FROM (" . $sql . ") AS xxxxxx";
            $countSql = "SELECT COUNT(0) as rows,SUM(wb.goods_num) as total_num,SUM(wb.total_chengben) as total_price,SUM(wb.shijia) as total_shijia FROM (" . $sql . ") AS wb";

            $total = $this->db()->getRow($countSql);
            $data['total_num'] = $total['total_num']?$total['total_num']:0;
            $data['total_price'] = $total['total_price']?$total['total_price']:0.00;
            $data['total_shijia'] = $total['total_shijia']?$total['total_shijia']:0.00;
            
            $data['pageSize'] = (int)$pageSize < 1 ? 20 : (int)$pageSize;
            //$data['recordCount'] = $this->db()->getOne($countSql, $params, $useCache);
            $data['recordCount']=$total['rows']?$total['rows']:0;
            $data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
            $data['page'] = $data['pageCount'] == 0 ? 0 : ((int)$page < 1 ? 1 : (int)$page);
            $data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
            $data['isFirst'] = $data['page'] > 1 ? false : true;
            $data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
            $data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] +   1;
            $data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
            $data['data'] = $this->db()->query($data['sql'], $params, $useCache);
        }
        catch (exception $e) {
            throw $e;
        }
        return $data;
    }
    public function getBillType($type_SN = false)
    {
        $sql = "select `type_name`,`type_SN` from `warehouse_bill_type` WHERE `is_enabled` = '1'";
        $data = $this->db()->getAll($sql);
        $type = array_column($data, 'type_name', 'type_SN');
        return ($type_SN) ? $type[$type_SN] : $type;
    }


    /**
     * create_bill_no() 生成入库订单
     */
    public function create_bill_no($type, $bill_id = '1')
    {
        $bill_id = substr($bill_id, -4);
        $bill_no = $type . date('Ymd', time()) . rand(100, 999) . str_pad($bill_id, 4,
            "0", STR_PAD_LEFT);
        if(defined('IS_ZHOUSHAN_SYS') && IS_ZHOUSHAN_SYS=='YES')
            $bill_no = $type . "6". date('Ymd', time()) . rand(10, 99) . str_pad($bill_id, 4,"0", STR_PAD_LEFT);        
        return $bill_no;
    }

    //根据条件返回数据（不带分页的）---JUAN
    public function getList($where)
    {
        $sql = "SELECT * FROM `" . $this->table() . "` ";
        $str = '';
        if ($where['bill_no'] != "") {
            $str .= "bill_no = '" . $where['bill_no'] . "' AND ";
        }
        if ($where['bill_type'] !== "") {
            $str .= "bill_type = '" . $where['bill_type'] . "' AND ";
        }
        if ($where['order_sn'] !== "") {
            $str .= "order_sn = '" . $where['order_sn'] . "' AND ";
        }
        if ($where['bill_status'] !== "") {
            $str .= "bill_status = " . $where['bill_status'] . ' AND ';
        }
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .= " WHERE " . $str;
        }
        $data = $this->db()->getAll($sql);
        return $data;
    }

    /** 普通查询 **/
    public function GetBillInfoByid($fields, $where, $type = 'getOne')
    {
        $sql = "SELECT {$fields} FROM `warehouse_bill` WHERE {$where} ORDER BY `id`";
        if ($type == 'getOne') {
            return $this->db()->getOne($sql);
        } else
            if ($type == 'getRow') {
                return $this->db()->getRow($sql);
            } else
                if ($type == 'getAll') {
                    return $this->db()->getAll($sql);
                }
    }

    /**
     * 根据当前用户，所拥有权限的仓库，获取所属的公司列表
     * @return $return Array array('UserInfo'=> '拥有权限的仓库+公司 ', 'form_company'  => '公司列表');
     */
    public function getUserWarehouseList()
    {
        $user_warehouse_id = $_SESSION['userWareNow']; //用户当前所在的仓库
        $return = array('UserInfo' => '', 'form_company' => '');
        $UserInfo = array();
        $form_company = array();
        //获取当前用户仓库
        $warehouseModel = new WarehouseModel($user_warehouse_id, 21);
        $UserInfo['warehouse_id'] = $user_warehouse_id;
        $UserInfo['warehouse_name'] = $warehouseModel->getValue('name');
        $warehouse = $this->warehouse();
        $quanxianwarehouseid = $this->WarehouseListO();
        if ($quanxianwarehouseid !== true) {
            foreach ($warehouse as $key => $val) {
                if (!in_array($val['id'], $quanxianwarehouseid)) {
                    unset($warehouse[$key]);
                }
            }
        }

        //取得所拥有这个操作的的仓库
        $quanxianwarehouseid = $this->WarehouseListO();
        //通过仓库id取得相关联的公司
        if ($quanxianwarehouseid !== true) {
            foreach ($quanxianwarehouseid as $key => $val) {
                $form_company[$val] = $this->getCompanyInfo($val);
            }
        } else {
            $model = new CompanyModel(1);
            $form_company = $model->getCompanyTree();
            foreach ($form_company as $key => $val) {
                $form_company[$key]['company_id'] = $val['id'];
            }
        }

        //获取当前用户公司信息
        $WarehouseRelModel = new WarehouseRelModel(21);
        $UserInfo['company_id'] = $WarehouseRelModel->GetCompanyByWarehouseId($user_warehouse_id);
        $companyModel = new CompanyModel(1);
        $UserInfo['company_name'] = $companyModel->getCompanyName($UserInfo['company_id']);

        $return = array('UserInfo' => $UserInfo, 'form_company' => $form_company);
        return $return;
    }

    /****
    获取公司 列表
    ****/
    public function company()
    {
        $model = new CompanyModel(1);
        $company = $model->getCompanyTree(); //公司列表
        return $company;
    }
    /***
    获取有效的仓库
    ***/
    public function warehouse()
    {
        $model_w = new WarehouseModel(21);
        $warehouse = $model_w->select(array('is_delete' => 1), array(
            "id",
            "name",
            'code'));
        return $warehouse;
    }


    /************************************************************************************************
    ************* fun:getIdByBillNo
    ********通过单据号获取id
    ****/
    public function getIdByBillNo($bill_no)
    {
        $sql = "select id from " . $this->table() . " where bill_no='{$bill_no}'";
        return $this->db()->getOne($sql);
    }


    /**打印详情  根据货号  $bill_id，获取货品详细属性 **/
    public function getDetail($bill_id)
    {
        $sql = "SELECT a.`goods_id`,a.`goods_sn`,b.`jinzhong`,b.`zhushilishu`,b.`zuanshidaxiao`,a.`zuanshidaxiao` as size,a.`yuanshichengben`,a.`sale_price`,a.`yanse`,a.`jingdu`,b.`zhengshuhao`,a.`goods_name`,b.`fushilishu`,b.`fushizhong`,b.`shi2lishu`,b.`shi2zhong`,b.`shi3lishu`,b.`shi3zhong`,b.`shoucun`,b.`xianzaixiaoshou`,b.`chengbenjia`,b.`zuixinlingshoujia`,b.`cat_type`,b.`mo_sn`,b.`num`,b.`buchan_sn`,a.`shijia`,a.`account`,a.`in_warehouse_type`,	
            a.`jiajialv`,
            b.`prc_id`,b.`prc_name`,b.`zhushibaohao`,b.`fushibaohao`,b.shi2baohao,b.shi3baohao,`a`.`label_price`,b.yuanshichengbenjia_zs 
		FROM `warehouse_bill_goods` as a ,warehouse_goods as b WHERE a.`bill_id` = '{$bill_id}' AND a.`goods_id` = b.`goods_id`";

        //echo $sql;exit;
        return $this->db()->getAll($sql);
    }

    /**打印汇总  根据货号  $bill_id，获取单据和详情表信息 **/
    public function getBillinfo($bill_id)
    {
        $sql = "select  sum(g.`jinzhong`) as jinzhong , g.caizhi from warehouse_bill_goods as og ,warehouse_goods as g where og.goods_id = g.goods_id and og.bill_id = '$bill_id' group by g.caizhi";
        $zhuchengsedata = $this->db()->getAll($sql);
        $sql = "select sum(g.`zhushilishu`) as zhushilishu , sum(g.`zuanshidaxiao`) as zuanshidaxiao , g.zhushi from warehouse_bill_goods as og , warehouse_goods as g where og.goods_id = g.goods_id and og.bill_id = '$bill_id' group by g.zhushi";
        $zhushidata = $this->db()->getAll($sql);
        $sql = "select sum(g.`fushilishu`) as fushilishu , sum(g.`fushizhong`) as fushizhong , g.fushi from warehouse_bill_goods as og ,warehouse_goods as g where og.goods_id = g.goods_id and og.bill_id = '$bill_id' group by g.fushi";
        $fushidata = $this->db()->getAll($sql);
        $sql = "select sum(g.jijiachengben) as jijiachengben_all,sum(g.zuixinlingshoujia) as lingshoujia_all from warehouse_bill_goods as og , warehouse_goods as g where og.goods_id = g.goods_id and og.bill_id = '$bill_id'";
        $all_price = $this->db()->getRow($sql);
        return array(
            'zhuchengsedata' => $zhuchengsedata,
            'zhushidata' => $zhushidata,
            'fushidata' => $fushidata,
            'all_price' => $all_price);
    }

    /**打印详情  根据 $bill_id，获取加工商信息 **/
    public function getBillPay($bill_id)
    {
        $sql = "SELECT `pro_id`,`pro_name`,`pay_content`,`amount`  FROM `warehouse_bill_pay`  WHERE `bill_id` = '{$bill_id}'";
        //echo $sql;exit;
        return $this->db()->getAll($sql);
    }
    //连表bill_goods w_goods表查出 货品id集合
    public function getGoodsIdinfoByBillId($bill_id)
    {
        $sql = "select  goods_id from `warehouse_shipping`.`warehouse_bill_goods`    where  bill_id = '$bill_id'";
       
        return $this->db()->getAll($sql);
    }
   // public function getGoodsIdinfoByBillId($bill_id)
   // {
    //    $sql = "select  b.goods_id from `warehouse_shipping`.`warehouse_bill_goods`  as g inner join `front`.`app_salepolicy_goods` as b on b.`goods_id`=g.`goods_id`  where  g.bill_id = '$bill_id'";
    //   
    //    return $this->db()->getAll($sql);
   // }
    public function getNotInpolicy($goods_id,$policy_id,$bill_id)
    {
        $sql="select goods_id from `warehouse_bill_goods` where goods_id in ($goods_id) and goods_id not in($policy_id) and `bill_id` = '{$bill_id}'";
        
        return $this->db()->getAll($sql);
    }
    public function getPriceByGoodsid($goods_id)
    {
        $sql="select b.`jiajia` as 'bj',b.`sta_value` as 'bst',a.`jiajia` as 'aj',a.`sta_value` as 'ast' from `front`.`app_salepolicy_goods` as a inner join `front`.`base_salepolicy_info` as b on a.`policy_id`=b.`policy_id`
         where a.`goods_id`='$goods_id'";
         
        return $this->db()->getRow($sql);
    }
  
     


    //如果是入库公司是总公司或者深圳分公司的改变发货状态 已到店
    public function checkBillTocompanyid($bill_no)
    {
        $sql = "select to_company_id from warehouse_bill where bill_no='{$bill_no}'";
        $res = $this->db()->getRow($sql);
        if ($res['to_company_id'] != 58 && $res['to_company_id'] != 445) {
            return true;
        } else {
            return false;
        }
    }

    //制单时，填写，提交订单号，检测订单号是否合法的存在
    public function CheckOrderSn($order_sn)
    {
        /*
        $data = ApiSalesModel::GetOrderInfoByOrdersn($order_sn);
        if (empty($data['return_msg'])) {
            return false;
        } else {
            return true;
        }*/
        $res=$this->getOrderInfoByOrderSn($order_sn);
        if($res['order_id']==false)
            return false;
        else
            return true;
    }

    //制单时检测是否有柜位信息(表：goods_warehouse) 如果没有制动生成默认柜位
    //根据货号的仓库信息，然后获取货品所在仓库的默认柜位
    public function CheckAndCreateBox($goods_id)
    {
        $time = date('Y-m-d H:i:s');
        $user = 'SYSTEM';
        //检测是货品否有柜位信息
        $sql = "SELECT `box_id` FROM `goods_warehouse` WHERE `good_id` = '{$goods_id}' LIMIT 1";
        $exists = $this->db()->getOne($sql);
        if ($exists) {
            return array('success' => 1, 'box_id' => $exists);
        }

        //获取货品所在仓库
        $sql = "SELECT `warehouse_id` FROM `warehouse_goods` WHERE `goods_id` = '{$goods_id}'";
        $warehouse_id = $this->db()->getOne($sql);

        //获取货品所在仓库的默认柜位
        $sql = "SELECT `id` , `is_deleted` FROM `warehouse_box` WHERE `warehouse_id` = {$warehouse_id} and `box_sn`='0-00-0-0' LIMIT 1";
        $box_info = $this->db()->getRow($sql);
        //仓库不存在默认柜位，那么就自动生成一个
        if (empty($box_info)) {
            $sql = "INSERT INTO `warehouse_box` (`warehouse_id` , `box_sn` , `create_name` , `create_time` , `info`) VALUES ({$warehouse_id} , '0-00-0-0' , '{$user}' , '{$time}' , '系统制单自动创建默认柜位')";
            $this->db()->query($sql);
            $box_info['id'] = $this->db()->insertId();
        } else {
            //判断默认柜位是否被禁用
            if ($box_info['is_deleted'] != 1) {
                return array('success' => 0, 'error' => '默认柜位被禁用，货品下架失败,导致制单失败');
            }
        }
        $box_id = $box_info['id'];

        /*$sql = "INSERT INTO `goods_warehouse` (`good_id` , `warehouse_id` , `box_id` , `add_time` , `create_time` , `create_user`) VALUES ('{$goods_id}' , {$warehouse_id} , {$box_id} , '{$time}' , '{$time}' , '{$user}')";
        $this->db()->query($sql);*/
        return array('success' => 1, 'box_id' => $box_id);
    }

    //根据订单号，查询最新一个有效状态的销售单的明细
    public function GetDetailByOrderSn($order_sn)
    {
        $sql = "SELECT `b`.`goods_id` FROM `warehouse_bill` AS `a` LEFT JOIN `warehouse_bill_goods` AS `b` ON `a`.`id` = `b`.`bill_id` INNER JOIN `warehouse_goods` AS `c` ON `b`.`goods_id` = `c`.`goods_id` WHERE `a`.`order_sn` = '{$order_sn}' AND `a`.`bill_status` = 2 AND `c`.`is_on_sale` = 3 AND `a`.`bill_type` = 'S'";
        return $this->db()->getAll($sql);
    }

    public function delBill($id)
    {
        $pdo = $this->db()->db(); //pdo对象
        try {
            $sql = "delete from warehouse_bill where id = '" . $id . "'";
            $pdo->query($sql);
            $sql = "delete from warehouse_bill_goods where bill_id = '" . $id . "'";
            $pdo->query($sql);
            $sql = "delete from warehouse_bill_status where bill_id = '" . $id . "'";
            $pdo->query($sql);
        }
        catch (exception $e) { //捕获异常

            $pdo->rollback(); //事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
            return false;
        }
        $pdo->commit(); //如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1); //开启sql语句自动提交
        return true;
    }

    public function getLastBill($goods_id)
    {
        $sql = "SELECT `b`.`goods_id`,a.bill_type FROM `warehouse_bill` AS `a` LEFT JOIN `warehouse_bill_goods` AS `b` ON `a`.`id` = `b`.`bill_id`  WHERE  `a`.`bill_status` = 2 and b.goods_id = " .
            $goods_id . " order by create_time desc limit 1";
        //echo $sql;exit;
        $arr = $this->db()->getRow($sql);
        return $arr;
    }

    /**
     * 获取相关的明细列表数据
     * $bill_id Int 单据ID
     * return array 明细
     */
    public function getBillGoogsList($bill_id)
    {
        $sql = "SELECT `b`.`goods_id`, `b`.`goods_sn`,  `b`.`goods_name`,  `a`.`goods_num`, `b`.`jinzhong`, `b`.`caizhi`, `b`.`yanse`, `b`.`jingdu`, `b`.`jinhao`, `b`.`sale_price`, `b`.`zhengshuhao` FROM `warehouse_bill` AS `a` LEFT JOIN `warehouse_bill_goods` AS `b` ON `a`.`id` = `b`.`bill_id` WHERE `a`.`id`={$bill_id} ";

        return $this->db()->getAll($sql);
    }
    /** 根据bill_no, 查询 bill_goods表中是否存在货品 **/
    public function getGoodsInfoBybillNo($bill_no)
    {
        $sql = "SELECT goods_id FROM  `warehouse_bill_goods`  WHERE `bill_no`='{$bill_no}'";
        return $this->db()->getRow($sql);
    }

    //发出未审核
    public function getBillByBillstatus()
    {
        $sql = "SELECT `bill_type`,COUNT(*) AS `COUNT` FROM  `warehouse_bill` WHERE `bill_status`=1 AND `create_user`='" .
            $_SESSION['userName'] . "' GROUP BY `bill_type`";
        return $this->db()->getAll($sql);
    }


    //判断仓库是否锁定状态
    public function check_warehouse_lock($warehouse_id)
    {
        $result = array('success' => 0, 'error' => '');
        $warehouse_model = new WarehouseModel($warehouse_id, 21);
        $lock = $warehouse_model->getValue('lock');
        $name = $warehouse_model->getValue('name');
        if ($lock == 1) {
            $result['error'] = $name . "正在盘点中，不允许审核！";
            Util::jsonExit($result);
        }
        if($warehouse_model->getValue('is_delete')=="0"){
            $result['error'] = $name . "仓库已禁用，不允许操作！";
            Util::jsonExit($result);           
        }
    }

    function GetBaoxianFei($xiangkou)
    {
        $xiangkou = $xiangkou * 10000;
        $baoxianfei = ApiModel::style_api(array(),array(), "getAllbaoxianfee");
        $i = 0;
        $j = 0;
        $k = 0;
        foreach ($baoxianfei as $k => $v) {
            $max[$i] = $v['max'] * 10000;
            $min[$j] = $v['min'] * 10000;
            $fee[$k] = $v['price'];
            $i++;
            $j++;
            $k++;
        }
        $count = count($max);
        for ($i = 0; $i < $count; $i++) {
            if ($xiangkou >= $min[$i] && $xiangkou <= $max[$i]) {
                return $fee[$i];
            }
        }
    }
    //根据货号和单据NO 判断该货号是否存在调拨单
    public function check_goods_exis($goods_id, $bill_no)
    {
        $sql = "SELECT `b`.`goods_id`,a.bill_type FROM `warehouse_bill` AS `a` LEFT JOIN `warehouse_bill_goods` AS `b` ON `a`.`id` = `b`.`bill_id`  WHERE  `a`.`bill_status` in(1,2) and b.goods_id = " .
            $goods_id . " and b.bill_no = '" . $bill_no .
            "' order by create_time desc limit 1";
        $arr = $this->db()->getRow($sql);
        return $arr;
    }
    //审核单据号补充尾差 尾差处理公共方法 收货单、其他收货单 id：单据id；chengbenjia_goods：单据成本价
    public function cha_deal($id, $chengbenjia_goods)
    {
        #收货单结算商尾差计算（金额限制需要加上====￥￥￥￥）检查是否有结算商--删除已有的结算商列表中的尾差记录
        //1、计算商品总成本价
        //2、计算结算商总成本
        //3、插入结算商尾差
        //4、修改单据价格总计、支付总计为计算的商品总成本
        $model_pay = new WarehouseBillPayModel(22);
        $model_pay->delete_cha($id);

        $zhifujia_prc = $model_pay->getAmount($id);
        $cha = $chengbenjia_goods - $zhifujia_prc;
        $new_array = array(
            'bill_id' => $id,
            'pro_id' => 366, //暂时是0
            'pro_name' => '入库成本尾差',
            'pay_content' => 6, //差 数据字典6
            'pay_method' => 1, //'记账'
            'tax' => 2, //数据字典 含 2
            'amount' => $cha,
            );

        if ($cha != 0) {
            #需要限制 自动尾差补充金额大小
            $re_p = $model_pay->saveData($new_array, array());
            if (!$re_p) {
                $result['error'] = "结算商尾差计算失败";
                Util::jsonExit($result);
            }
        }
    }
    public function getGoodsidBybillno($bill_no)
    {
        $sql = "SELECT goods_id FROM  `warehouse_bill_goods`  WHERE `bill_no`='{$bill_no}'";
        return $this->db()->getAll($sql);
    }
    //获得默认销售政策信息
    public function getPolicyidBygoodsid($goods_id)
    {
        $sql = "SELECT a.policy_name,a.jiajia,a.sta_value,a.policy_start_time,a.policy_end_time FROM  `front`.`base_salepolicy_info` 
          as a inner join `front`.`app_salepolicy_goods` as b  on a.`policy_id`=b.`policy_id`   WHERE b.`goods_id` in ($goods_id)
          and b.`is_delete`=1";

        return $this->db()->getRow($sql);
    }
    //获得单据对应政策信息
    public function getPolicybillBygoodsid($goods_id)
    {
        $sql = "SELECT a.policy_name,b.jiajia,b.sta_value,a.policy_start_time,a.policy_end_time FROM  `front`.`base_salepolicy_info` 
          as a inner join `front`.`app_salepolicy_goods` as b  on a.`policy_id`=b.`policy_id`   WHERE b.`goods_id` in ($goods_id)
          and b.`is_delete`=1";

        return $this->db()->getRow($sql);
    }
	
	 public function getRealData($where,$page,$pageSize=10,$useCache=true , $is_down = false){
        $sql = "select w.name as channel, count(g.id) as sku, sum(g.chengbenjia) as cost, sum(jinzhong) as gold from warehouse_goods as g, warehouse as w where g.is_on_sale in (2,4,5,6,8,10,11) and g.caizhi in ('千足金', '足金')
and g.warehouse_id=w.id group by warehouse_id";
        $values = $this->db()->getAll($sql);
		 $kucun_count =  0; // 库存
						 $chenben_count =  0; // 库存
						 $jin_count =  0; // 库存
						 if($values){
							 foreach($values as $k => $v){
								 $kucun_count += $v['sku'];
								 $chenben_count += $v['cost'];
								 $jin_count += $v['gold'];
							 } 
						 }
						  
        $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache); 
		$data['kucun_count'] = $kucun_count;
						 $data['chenben_count'] = $chenben_count;
						 $data['jin_count'] = $jin_count; 
					 
        return $data;
    }
	
	
	public function getGoldlData($where,$page,$pageSize=10,$useCache=true , $is_down = false){
           $time = "";
           if ($where['start_time'] !== '')
		{
			$time .= " AND check_time >= '".$where['start_time']." 00:00:00' ";
		}
		if ($where['end_time'] !== '')
		{
			$time .= " AND check_time <= '".$where['end_time']." 23:59:59' ";
		}
             
        $sql = "SELECT left( o.check_time, 10 ) AS check_time, warehouse AS warehouse, g.goods_sn AS goods_sn, count( g.id ) AS num, sum( g.jinzhong ) AS gold, sum( g.chengbenjia ) AS chengbenjia, sum( g.mairugongfei ) AS mairugongfei, sum( og.shijia ) AS shijia, 
CASE WHEN g.jiejia =1
THEN '未结价'
ELSE '已结价'
END AS is_settle
FROM warehouse_goods AS g, warehouse AS w, warehouse_bill_goods AS og, warehouse_bill AS o
WHERE og.bill_id = o.id
AND g.warehouse_id=w.id
AND o.bill_status =2
AND o.bill_type = 'S'
AND g.goods_id = og.goods_id
AND g.is_on_sale =3
AND g.caizhi in ('千足金', '足金')
 {$time}
GROUP BY g.warehouse_id, g.goods_sn, left( o.check_time, 10 ),g.jiejia "; 
 
       $values = $this->db()->getAll($sql);
	   
	    	 $num_count =  0; //  
						 $gold_count =  0; //  
						 $chengbenjia_count =  0; //  
						  $mairugongfei_count =  0; //  
						   $shijia_count =  0; //  
						 if($values){
							 foreach($values as $k => $v){
								 $num_count += $v['num'];
								 $gold_count += $v['gold'];
								 $chengbenjia_count += $v['chengbenjia'];
								  $mairugongfei_count += $v['mairugongfei'];
							    $shijia_count += $v['shijia'];
							 } 
						 }
	    
      $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);  
	  $data['num_count'] = $num_count; 
	  $data['gold_count'] = $gold_count; 
	  $data['chengbenjia_count'] = $chengbenjia_count; 
	  $data['mairugongfei_count'] = $mairugongfei_count;  

      return $data;
    }


    public function getGoldltData($where,$page,$pageSize=10,$useCache=true , $is_down = false){
           $time = "";
           if ($where['start_time'] !== '')
        {
            $time .= " AND check_time >= '".$where['start_time']." 00:00:00' ";
        }
        if ($where['end_time'] !== '')
        {
            $time .= " AND check_time <= '".$where['end_time']." 23:59:59' ";
        }
             
        $sql = "SELECT left( o.check_time, 10 ) AS check_time,o.bill_no as bill_no,o.pro_name as pro_name, count( g.id ) AS num,sum( g.jinzhong ) AS gold,sum( g.chengbenjia ) AS chengbenjia, sum( g.mairugongfei ) AS mairugongfei,to_warehouse_name AS to_warehouse_name, g.goods_sn AS goods_sn
FROM warehouse_goods AS g, warehouse_bill_goods AS og, warehouse_bill AS o
WHERE og.bill_id = o.id
AND g.goods_id = og.goods_id
AND o.bill_status =2
AND o.bill_type in('L', 'T') 
AND g.caizhi in ('千足金', '足金')
 {$time}
GROUP BY g.warehouse_id, g.goods_sn, left( o.check_time, 10 )"; 
        $values = $this->db()->getAll($sql);
        $num_count =  0; //  
        $gold_count =  0; //  
        $chengbenjia_count =  0; //  
        $mairugongfei_count =  0; //  
        $shijia_count =  0; //  
        if($values){
            foreach($values as $k => $v){
                $num_count += $v['num'];
                $gold_count += $v['gold'];
                $chengbenjia_count += $v['chengbenjia'];
                $mairugongfei_count += $v['mairugongfei'];
            } 
        }
      $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);  
      $data['num_count'] = $num_count; 
      $data['gold_count'] = $gold_count; 
      $data['chengbenjia_count'] = $chengbenjia_count; 
      $data['mairugongfei_count'] = $mairugongfei_count;  

      return $data;
    }

    public function getOrderInfoByOrderSn($order_sn)
    {
        if(empty($order_sn)){
            return false;
        }
        $sql="SELECT oi.id,aot.allow_shop_time
        FROM 
        app_order.base_order_info oi 
        left join app_order.app_order_time aot on oi.id=aot.order_id
        WHERE oi.order_sn = '$order_sn' limit 1;
        ";
        $row = $this->db()->getRow($sql);
        if(empty($row)){
            return array('order_id'=>false);
        }else{
            if(!empty($row['allow_shop_time'])){
                return array('order_id'=>$row['id'],'allow_shop_time'=>$row['allow_shop_time']);
            }else{
                return array('order_id'=>$row['id'],'allow_shop_time'=>false);
            }
        }
    }
    
    public function updateSendTimeByOrderid($order_id)
    {
        if(empty($order_id)){
            return false;
        }
        $sql="UPDATE `app_order`.`app_order_time` SET `allow_shop_time` = '0000-00-00 00:00:00' WHERE `app_order_time`.`order_id` = $order_id;";
        $this->db()->query($sql);
    }
    
    
    
    /**************
     decription:根据单据id获取商品仓库
     ***************/
    public function get_bill_warehouse($bill_id)
    {
    	$sql = "SELECT `wg`.`id`,`g`.`warehouse_id`,`g`.`warehouse`,`g`.`weixiu_warehouse_id`,`g`.`weixiu_warehouse_name` FROM `warehouse_bill_goods` AS `wg`  LEFT JOIN  `warehouse_goods` AS `g`  ON `wg`.`goods_id`=`g`.`goods_id`  WHERE `bill_id` = $bill_id ORDER BY `g`.`id` ASC" ;
    	return $this->db()->getAll($sql);
    }
    
    
    /**************
     decription:获取单据取消的时间和人
     ***************/
    public function get_bill_close_status($bill_id)
    {
    	$sql = "SELECT update_time,update_user FROM warehouse_bill_status WHERE status=3 AND bill_id={$bill_id} ORDER BY id DESC LIMIT 1" ;
    	return $this->db()->getRow($sql);
    }

    //批量导出批发销售单
    public function getBillPfInfo($ids)
    {
        $sql = "SELECT
                        `wb`.`bill_no`,
                        `wb`.`bill_type`,
                        if(wb.bill_type='M',wb.to_company_name,`jw`.`wholesale_name`) as wholesale_name,
                        `wb`.`from_company_name`,
                        `bg`.`id`,
                        `bg`.`pinhao`,
                        `bg`.`goods_id`,
                        `bg`.`goods_sn`,
                        `wg`.`buchan_sn`,
                        `wg`.`jinzhong`,
                        `wg`.`zhengshuhao`,
                        `wg`.`zhengshuleibie`,
                        `wg`.`jietuoxiangkou`,
                        `wg`.`shoucun`,
                        `wg`.`caizhi`,
                        '' as caizhi1,
                        '' as yanse,
                        `wg`.`zhushi`,
                        `wg`.`product_type1`,
                        `wg`.`cat_type1`,
                        `wg`.`zuanshidaxiao`,
                        `wg`.`zhushilishu`,
                        `wg`.`zhushijingdu`,
                        `wg`.`zhushiyanse`,
                        `wg`.`zhushiqiegong`,
                        `wg`.`zhushixingzhuang`,
                        `wg`.`yingguang`,
                        `wg`.`paoguang`,
                        `wg`.`duichen`,
                        `wg`.`color_grade`,
                        `wg`.`fushijingdu`,
                        `wg`.`fushi`,
                        `wg`.`zongzhong`, 
                        `wg`.`fushilishu`,
                        `wg`.`fushixingzhuang`,
                        `wg`.`fushiyanse`,
                        `wg`.`fushizhong`,
                        `wg`.`shi2`,
                        `wg`.`shi2lishu`,
                        `wg`.`shi2zhong`,
                        if(wb.bill_type='M',wg.yuanshichengbenjia*(1+bg.jiajialv/100),`bg`.`shijia`) as shijia,
                        `bg`.`goods_name`,
                        `wb`.`order_sn`,
                        `bg`.`xiangci`,
                        `wg`.`tuo_type`,
                        `wg`.`pinpai`,
                        `wg`.`goods_sn` as goods_sn1,
                        `bg`.`p_sn_out`,
                        `wg`.`yuanshichengbenjia`,
                        `wb`.`out_warehouse_type`,
                        `wb`.`p_type`,
                        `bg`.`label_price`,
                        `bg`.`management_fee`
                    FROM 
                    `warehouse_bill` `wb` 
                    inner join `warehouse_bill_goods` `bg` on `wb`.`id` = `bg`.`bill_id` 
                    inner join `warehouse_goods` `wg` on `bg`.`goods_id` = `wg`.`goods_id` 
                    left join `jxc_wholesale` `jw` on `wb`.`to_customer_id` = `jw`.`wholesale_id` 
                    WHERE 
                        `wb`.`id` = {$ids} ";
        //echo $sql;exit;
        return $this->db()->getAll($sql);
    }

    //根据新产品线和新款式分类取出新款号
    public function getNewStyle_sn($where)
    {
        $sql = "select `new_k_sn` from `new_style_info` where `cat_type` = '".$where['cat_type1']."' and `product_type` = '".$where['product_type1']."'";
        return $this->db()->getOne($sql);
    }
    
    function getdetailList($where)
    {
    	$sql="SELECT b.id,g.supplier_code,b.to_customer_id,b.to_customer_id,b.to_company_name,b.to_warehouse_name,b.from_company_name,b.bill_no,b.bill_type,b.bill_status,b.create_user,b.create_time,b.check_user,b.check_time,b.fin_check_user,b.fin_check_time,b.bill_note,b.company_id_from,wg.num,wg.goods_id,wg.goods_sn,wg.goods_name,wg.shijia,g.mingyichengben,g.cat_type1,g.product_type1,g.zongzhong,g.jinzhong,g.zhushi,g.zhushilishu,g.zuanshidaxiao,g.zhushiyanse,g.zhushijingdu,g.fushilishu,g.fushizhong,g.caizhi,g.shoucun,b.pro_id,g.prc_id,g.prc_name,g.yuanshichengbenjia,wg.dep_settlement_type,wg.settlement_time,wg.management_fee,b.out_warehouse_type,b.p_type, wg.label_price,g.buchan_sn,g.zhengshuleibie,g.zhengshuhao,if(ifnull(g.buchan_sn,'')='','',(select concat(p.biaozhun_jinzhong_min,'-',p.biaozhun_jinzhong_max) from kela_supplier.product_info p where p.bc_sn=g.buchan_sn limit 1)) as biaozhun_jinzhong,if(ifnull(g.buchan_sn,'')='','',(select concat(p.lishi_jinzhong_min,'-',p.lishi_jinzhong_max) from kela_supplier.product_info p where p.bc_sn=g.buchan_sn limit 1)) as lishi_jinzhong,g.certificate_fee,g.mo_sn,g.operations_fee,b.order_sn,g.zhushitiaoma,b.put_in_type FROM warehouse_bill_goods AS wg LEFT JOIN warehouse_bill AS b ON b.id=wg.bill_id LEFT JOIN warehouse_goods AS g ON g.goods_id=wg.goods_id ";
    	$left_join='';
    	$str=" WHERE 1 ";

        if(isset($where['hidden']) && $where['hidden'] != ''){
            $str .= " and `b`.hidden = ".$where['hidden'];
        }

    	if (!empty($where['send_goods_sn'])) {
    		$str .= " AND `b`.send_goods_sn LIKE \"%" . addslashes($where['send_goods_sn']) .
    		"%\"";
    	}
    	if ($where['bill_no'] != "") {
    		$str .= " AND `b`.bill_no = '" . addslashes($where['bill_no']) . "'";
    	}
    	if ($where['create_user'] != "") {
    		$str .= " AND `b`.create_user = '"   . addslashes($where['create_user'])."'";
    	}
    	if ($where['bill_type'] !== "") {
    		$str .= " AND `b`.bill_type = '" . $where['bill_type'] . "' ";
    	}
    	if ($where['confirm_delivery'] !== "" && $where['bill_type'] == "P") {
    		$str .= " AND `b`.confirm_delivery = " . $where['confirm_delivery'];
    	}
    	if ($where['p_order_sn'] !== "" && $where['bill_type'] == "P") {
    		$str .= " AND `wg`.order_sn = '" . $where['p_order_sn']."'";
    	}
    	if ($where['is_tsyd'] !== "" && $where['bill_type'] == "P") {
    		$str .= " AND `b`.is_tsyd = '" . $where['is_tsyd']."'";
    	}
    	if ($where['order_sn'] !== "") {
    		$str .= " AND `b`.order_sn = '" . $where['order_sn'] . "' ";
    	}
        if ($where['bc_sn'] !== "") {
            $str .= " AND `g`.buchan_sn = '" . $where['bc_sn']."'";
        }
    	if ($where['bill_status'] !== "") {
    		$str .= " AND `b`.bill_status = " . $where['bill_status'];
    	}
        if ($where['fin_check_status'] !== "") {
            $str .= " AND `b`.fin_check_status = " . $where['fin_check_status'];
        } 
        if ($where['jiejia'] !== "") {
            $str .= " AND `b`.jiejia = '" . $where['jiejia'] ."'";
        }                
    	if (!empty($where['to_customer_id'])) {
    		$str .= " AND `b`.to_customer_id = " . $where['to_customer_id'];
    	}
        if ($where['settlement_time_start'] !== "") {
            $str .= " and `wg`.`settlement_time` >='{$where['settlement_time_start']} 00:00:00'";
        }
        if ($where['settlement_time_end'] !== "") {
            $str .= " and `wg`.`settlement_time` <= '{$where['settlement_time_end']} 23:59:59'";
        }
    	
    	if($where['company_id_list'] != ''){
            if($where['from_company_id'] != 0 || $where['to_company_id'] != 0){
                if ($where['from_company_id'] != 0) {
                    $str .= " and `b`.from_company_id = " . $where['company_id_list'];
                }
                if ($where['to_company_id'] != 0) {
                    $str .= " and `b`.to_company_id = " . $where['company_id_list'] . ' ';
                }
            }else{
                $str .= " and (`b`.from_company_id = " . $where['company_id_list']." or `b`.to_company_id = ".$where['company_id_list'].")";
            }
        }else{
            if ($where['from_company_id'] != 0) {
                $str .= " and `b`.from_company_id = " . $where['from_company_id'];
            }
            if ($where['to_company_id'] != 0) {
                $str .= " and `b`.to_company_id = " . $where['to_company_id'] . ' ';
            }
        }
        //二、H单权限管控
        //总公司的能看所有的H单，
        //经销商，个体，直营看出库公司是自己的H单，
        //省代能看出库公司是自己以及下属省代的H单（并且入库公司非总公司），
        ////用户级别1.总公司 、2.经销商，个体，直营 、3.省代 
        if($where['level'] <> 1 && !empty($where['dataCompInfo'])){
            $dataCompInfo = array_pop($where['dataCompInfo']);
            $cp_str = '';
            if(!empty($where['dataCompInfo']) && $where['level'] == 3){
                $cp_str = "(`b`.from_company_id in(".implode(',', $where['dataCompInfo']).") and `b`.`to_company_id` <> '58') or ";
            }
            if($dataCompInfo) $str .=" and if(b.bill_type = 'H',(".$cp_str." `b`.`from_company_id` = ".$dataCompInfo."),'1')";
        }
        if ($where['dep_settlement_type'] != '') {
            $str .= " AND `wg`.dep_settlement_type = " . $where['dep_settlement_type'];
        }
        if ($where['out_warehouse_type'] != '') {
            $str .= " AND `b`.out_warehouse_type = " . $where['out_warehouse_type'];
        }

    	if ($where['to_warehouse_id'] != 0) {
    		$str .= " AND `b`.to_warehouse_id = " . $where['to_warehouse_id'];
    	}
    	if ($where['processors'] != "") {
    		$str .= " AND `b`.`pro_id`={$where['processors']} ";
    	}
    	if ($where['time_start'] !== "") {
    		$str .= " AND `b`.`create_time`>='{$where['time_start']} 00:00:00'";
    	}
    	if ($where['time_end'] !== "") {
    		$str .= " AND `b`.`create_time` <= '{$where['time_end']} 23:59:59'";
    	}
    	if ($where['check_time_start'] !== "") {
    		$str .= " AND `b`.`check_time`>='{$where['check_time_start']} 00:00:00'";
    	}
    	if ($where['check_time_end'] !== "") {
    		$str .= " AND `b`.`check_time` <= '{$where['check_time_end']} 23:59:59'";
    	}
        if ($where['fin_check_time_start'] !== "") {
            $str .= " AND `b`.`fin_check_time`>='{$where['fin_check_time_start']} 00:00:00'";
        }
        if ($where['fin_check_time_end'] !== "") {
            $str .= " AND `b`.`fin_check_time` <= '{$where['fin_check_time_end']} 23:59:59'";
        }
    	if ($where['bill_note'] != "") {
    		$str .= " AND `b`.bill_note like \"%" . addslashes($where['bill_note']) . "%\"";
    	}
    	if ($where['mohao'] != "") {
    		$str .= " AND g.`mo_sn`='{$where['mohao']}'";
    	}
    	if ($where['put_in_type'] != "") {
    		$str .= " AND b.`put_in_type`= " . $where['put_in_type'];
    	}
        if ($where['chuku_type'] != "") {
            $str .= " AND b.`tuihuoyuanyin`= " . $where['chuku_type'];
        }
    	if ($where['account_type'] != "") {
    		$left_join .=' LEFT JOIN `warehouse_bill_pay` as wp on wg.`bill_id`=wp.`bill_id` ';
    		$str .= " AND wp.`pay_method`=" . $where['account_type'];
    	}
        if ($where['pay_id'] != "") {
            $left_join .=' LEFT JOIN  app_order.base_order_info o on b.order_sn=o.order_sn ';
            $str .= " AND o.order_pay_type='" . $where['pay_id'] ."' ";
        }
    	
    	if($_SESSION['userName']!="董小华")
    		$str .= " and `b`.`bill_no` not in ('B201510125955187','B201510203345215','B201510235555232') " ;
    	
    	if($_SESSION['userName']=="lily") {
    		$sql.=" and b.to_customer_id=63 and (b.bill_type='P' or b.bill_type='H') ";
    	}
    	
    	$sql.=$left_join.$str." ORDER BY `b`.`id`  DESC";
    	//echo $sql;exit;
        $rows=$this->db()->getAll($sql);
    	return $rows;
    }
    
    //根据货号查询销售政策对应商品表里的销售价
    public function getLinshoujia($goods_id)
    {
    	$sql = "select `sale_price` from `front`.`app_salepolicy_goods` where `goods_id` = '".$goods_id."' order by id desc limit 1";
    	return $this->db()->getOne($sql);
    }
    
    //根据货号查询销售政策对应商品表里的销售价
    public function getLinshoujia2($cert_id)
    {
    	$sql = "select `shop_price` from `front`.`diamond_info` where `cert_id` = '".$cert_id."'";
    	//return $this->db()->getOne($sql);
    	$api = new ApiModel();
    	$resp = $api->diamond_api(array('sql', 'mt'), array($sql, 'getOne'), 'remote_exec_sql');
    	return $resp['data'];
    }
    
    
    function getBillIds($where)
    {
    	$orderby= "  group by `b`.id ORDER BY `b`.`id`  DESC ";
    	if ($where['goods_id'] == '') {
    
    		$sql="select b.id from `{$this->table()}` as `b` where 1 ";
    		
    	} else {
    
    		
    		$sql = "select `b`.id from `{$this->table()}` as `b`,warehouse_bill_goods as `g` where `g`.`bill_id`=`b`.`id` "; //echo $sql;exit;
    		//屏蔽发货生成的单据
    		 
    	}
    	$str = '';

        if(isset($where['hidden']) && $where['hidden'] != ''){
            $str .= " and `b`.hidden = ".$where['hidden'];
        }

    	if ($where['send_goods_sn'] != "") {
    		$str .= " and `b`.send_goods_sn like \"%" . addslashes($where['send_goods_sn']) .
    		"%\"";
    	}
    	if ($where['bill_no'] != "") {
    		$str .= " and `b`.bill_no = '" . addslashes($where['bill_no']) . "'";
    	}
    	if ($where['create_user'] != "") {
    		$str .= " and `b`.create_user = '"   . addslashes($where['create_user'])."'";
    	}
    	if ($where['bill_type'] !== "") {
    		$str .= " and `b`.bill_type = '" . $where['bill_type'] . "' ";
    	}
    	if ($where['confirm_delivery'] !== "" && $where['bill_type'] == "P") {
    		$str .= " and `b`.confirm_delivery = " . $where['confirm_delivery'];
    	}
    	if ($where['p_order_sn'] !== "" && $where['bill_type'] == "P") {
    		$str .= " and `g`.order_sn = '" . $where['p_order_sn']."'";
    	}
    	if ($where['is_tsyd'] !== "" && $where['bill_type'] == "P") {
    		$str .= " and `b`.is_tsyd = '" . $where['is_tsyd']."'";
    	}
    	if ($where['order_sn'] !== "") {
    		$str .= " and `b`.order_sn = '" . $where['order_sn'] . "' ";
    	}
    	if ($where['out_warehouse_type'] !== "") {
    		$str .= " and `b`.out_warehouse_type = " . $where['out_warehouse_type'];
    	}
        if ($where['bill_status'] !== "") {
            $str .= " and `b`.bill_status = " . $where['bill_status'];
        }
    	if (!empty($where['to_customer_id'])) {
    		$str .= " and `b`.to_customer_id = " . $where['to_customer_id'];
    	}
    
    	if($where['company_id_list'] != ''){
            if($where['from_company_id'] != 0 || $where['to_company_id'] != 0){
                if ($where['from_company_id'] != 0) {
                    $str .= " and `b`.from_company_id = " . $where['company_id_list'];
                }
                if ($where['to_company_id'] != 0) {
                    $str .= " and `b`.to_company_id = " . $where['company_id_list'] . ' ';
                }
            }else{
                $str .= " and (`b`.from_company_id = " . $where['company_id_list']." or `b`.to_company_id = ".$where['company_id_list'].")";
            }
        }else{
            if ($where['from_company_id'] != 0) {
                $str .= " and `b`.from_company_id = " . $where['from_company_id'];
            }
            if ($where['to_company_id'] != 0) {
                $str .= " and `b`.to_company_id = " . $where['to_company_id'] . ' ';
            }
        }

        //二、H单权限管控
        //总公司的能看所有的H单，
        //经销商，个体，直营看出库公司是自己的H单，
        //省代能看出库公司是自己以及下属省代的H单（并且入库公司非总公司），
        ////用户级别1.总公司 、2.经销商，个体，直营 、3.省代 
        if($where['level'] <> 1 && !empty($where['dataCompInfo'])){
            $dataCompInfo = array_pop($where['dataCompInfo']);
            $cp_str = '';
            if(!empty($where['dataCompInfo']) && $where['level'] == 3){
                $cp_str = "(`b`.from_company_id in(".implode(',', $where['dataCompInfo']).") and `b`.`to_company_id` <> '58') or ";
            }
            if($dataCompInfo) $str .=" and if(b.bill_type = 'H',(".$cp_str." `b`.`from_company_id` = ".$dataCompInfo."),'1')";
        }
        
    	if ($where['to_warehouse_id'] != 0) {
    		$str .= " and `b`.to_warehouse_id = " . $where['to_warehouse_id'];
    	}
    	if ($where['processors'] != "") {
    		$str .= " and `b`.`pro_id`={$where['processors']} ";
    	}
    	if ($where['time_start'] !== "") {
    		$str .= " and `b`.`create_time`>='{$where['time_start']} 00:00:00'";
    	}
    	if ($where['time_end'] !== "") {
    		$str .= " and `b`.`create_time` <= '{$where['time_end']} 23:59:59'";
    	}
    	if ($where['check_time_start'] !== "") {
    		$str .= " and `b`.`check_time`>='{$where['check_time_start']} 00:00:00'";
    	}
    	if ($where['check_time_end'] !== "") {
    		$str .= " and `b`.`check_time` <= '{$where['check_time_end']} 23:59:59'";
    	}
        if ($where['settlement_time_start'] !== "" && $where['bill_type'] == "P") {
            $str .= " and `g`.`settlement_time`>='{$where['settlement_time_start']} 00:00:00'";
        }
        if ($where['settlement_time_end'] !== "" && $where['bill_type'] == "P") {
            $str .= " and `g`.`settlement_time` <= '{$where['settlement_time_end']} 23:59:59'";
        }
    	if ($where['bill_note'] != "") {
    		$str .= " and `b`.bill_note like \"%" . addslashes($where['bill_note']) . "%\"";
    	}
    	if ($where['mohao'] != "") {
    		$str .= " and wg.`mo_sn`='{$where['mohao']}'";
    	}
    	if ($where['put_in_type'] != "") {
    		$str .= " and b.`put_in_type`= " . $where['put_in_type'];
    	}
        if ($where['chuku_type'] != "") {
            $str .= " and b.`tuihuoyuanyin`= " . $where['chuku_type'];
        }
    	if ($where['account_type'] != "") {
    		$str .= " and wp.`pay_method`=" . $where['account_type'];
    	}
    
    	if($_SESSION['userName']!="董小华")
    		$str .= " and `b`.`bill_no` not in ('B201510125955187','B201510203345215','B201510235555232') " ;
    
    	
    		if($_SESSION['userName']=="lily") {
    			$sql.=" and b.to_customer_id=63 and (b.bill_type='P' or b.bill_type='H') ";
    		}
    		$sql.=" and (b.from_bill_id is null  or b.from_bill_id = 0 ) ";
    		$sql .= $str;
    	
    	$sql.=$orderby;
    	//echo $sql;exit;
    
    	$data = $this->db()->getAll($sql);
    	
    	return $data;
    }


    public function sign_p_bill($params) {

        $good_ids = $this->db()->getAll("select goods_id from warehouse_bill_goods where bill_id = {$params['id']}");

        $pdo = $this->db()->db(); //pdo对象
        try {
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,0);//关闭sql语句自动提交
            $pdo->beginTransaction();//开启事务

            $sql = "update warehouse_bill set to_warehouse_id ={$params['to_warehouse_id']}, to_warehouse_name='{$params['to_warehouse_name']}', sign_user='{$params['sign_user']}', sign_time='{$params['sign_time']}' , bill_status='4' where id = {$params['id']}";
            $pdo->query($sql);
            // 归属公司在审核时候已经调整
            if (count($good_ids) == 1) {
                $sql = "update warehouse_goods set `is_on_sale`=2, `warehouse_id`={$params['to_warehouse_id']}, `warehouse`='{$params['to_warehouse_name']}' where goods_id = '".$good_ids[0]['goods_id']."'";
            } else {
                $pieces = array_map(function($v){
                    return "'".$v['goods_id']."'";
                } , $good_ids);
                $good_ids = implode(',', $pieces);
                $sql = "update warehouse_goods set `is_on_sale`=2, `warehouse_id`={$params['to_warehouse_id']}, `warehouse`='{$params['to_warehouse_name']}' where goods_id in ({$good_ids})";
            }

            $pdo->query($sql);
        }
        catch (exception $e) { //捕获异常
            $pdo->rollback();//事务回滚
            $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
            return false;
        }

        $pdo->commit();//如果没有异常，就提交事务
        $pdo->setAttribute(PDO::ATTR_AUTOCOMMIT,1);//开启sql语句自动提交
        return true;
    }

    public function getWarehouseNameByID($warehouse_id){
        $sql="select name from warehouse where id='{$warehouse_id}'";
        return $this->db()->getOne($sql);
    }

    public function getPayList(){
        $sql="select * from cuteframe.payment where is_deleted=0";
        return $this->db()->getAll($sql);
    }


    function get_chengben_detail($where)
    {
        $sql="select b.bill_no,b.send_goods_sn,g.buchan_sn,g.goods_id,g.goods_sn,g.mo_sn,g.product_type1,g.cat_type1,g.caizhi,g.jinzhong,g.jinhao,
        g.zhuchengsezhongjijia,g.zhuchengsemairudanjia,g.zhuchengsemairuchengben,g.zhuchengsejijiadanjia,
        g.zhushi, g.zhushilishu, g.zuanshidaxiao, g.zhushizhongjijia,
         g.zhushiyanse, g.zhushijingdu, g.zhushimairudanjia, 
        g.zhushimairuchengben, g.zhushijijiadanjia, g.zhushiqiegong, g.zhushixingzhuang, g.zhushibaohao, 
        g.zhushiguige, g.fushi, g.fushilishu, g.fushizhong, g.fushizhongjijia, g.fushiyanse, g.fushijingdu, g.fushimairudanjia,
         g.fushimairuchengben, g.fushijijiadanjia, g.fushixingzhuang, g.fushibaohao, g.fushiguige, g.zongzhong, g.mairugongfeidanjia,
         g.mairugongfei, g.jijiagongfei, g.shoucun, g.danjianchengben,
         g.peijianchengben,g.peijianjinchong,g.qitachengben, g.chengbenjia, g.jijiachengben, 
         if(g.tuo_type=1,'成品','空托') as tuo_type,b.pro_name,b.pro_id
         FROM warehouse_bill_goods AS wg LEFT JOIN warehouse_bill AS b ON b.id=wg.bill_id LEFT JOIN warehouse_goods AS g ON g.goods_id=wg.goods_id ";
        $left_join='';
        $str=" WHERE 1 and (b.from_bill_id is null  or b.from_bill_id = 0 ) ";
       
        if ($where['bill_no'] != "") {
            $str .= " AND `b`.bill_no = '" . addslashes($where['bill_no']) . "'";
        }
        
        if ($where['bill_type'] !== "") {
            $str .= " AND `b`.bill_type = '" . $where['bill_type'] . "' ";
        }
       
        if ($where['bill_status'] !== "") {
            $str .= " AND `b`.bill_status = " . $where['bill_status'];
        }


        
        if($where['company_id_list'] != ''){
            if($where['from_company_id'] != 0 || $where['to_company_id'] != 0){
                if ($where['from_company_id'] != 0) {
                    $str .= " and `b`.from_company_id = " . $where['company_id_list'];
                }
                if ($where['to_company_id'] != 0) {
                    $str .= " and `b`.to_company_id = " . $where['company_id_list'] . ' ';
                }
            }else{
                $str .= " and (`b`.from_company_id = " . $where['company_id_list']." or `b`.to_company_id = ".$where['company_id_list'].")";
            }
        }else{
            if ($where['from_company_id'] != 0) {
                $str .= " and `b`.from_company_id = " . $where['from_company_id'];
            }
            if ($where['to_company_id'] != 0) {
                $str .= " and `b`.to_company_id = " . $where['to_company_id'] . ' ';
            }
        }
        
        if ($where['check_time_start'] !== "") {
            $str .= " AND `b`.`check_time`>='{$where['check_time_start']} 00:00:00'";
        }
        if ($where['check_time_end'] !== "") {
            $str .= " AND `b`.`check_time` <= '{$where['check_time_end']} 23:59:59'";
        }

        if(SYS_SCOPE == 'zhanting'){
            $str .= " AND `b`.`hidden` = 0 ";
        }
        $sql.=$left_join.$str." ORDER BY `b`.`id`  DESC";
        //echo $sql;exit;
        $rows=$this->db()->getAll($sql);
        return $rows;
    }


    function get_sale_detail($where)
    {
        $str=" ";
        if(SYS_SCOPE == 'zhanting'){
            $str .= " AND `b`.`hidden` = 0 ";
        }
        if ($where['bill_no'] != "") {
            $str .= " AND `b`.bill_no = '" . addslashes($where['bill_no']) . "'";
        }
        if(!empty($where['company_id'])){
            $company_id = $where['company_id'];
        }else{
            $company_id = 58;
        }

        $bill_type = $where['bill_type'];
        $start_time = $where['check_time_start'];
        $end_time = $where['check_time_end'];

       if($bill_type == 'S'){
           if($company_id != 58){
               $str .= "AND `b`.`from_company_id` = {$company_id} ";
           }
           $sql = "SELECT 
        `b`.`bill_no` as S销售单, 
        concat('\'',`b`.`order_sn`) as 订单号, 
        `wg`.`goods_id` as 货号, 
        `b`.`goods_num` as 总数量, 
        1 as 数量,
        `b`.`from_company_name` as 出库公司, 
        `sc`.`channel_name` as 销售渠道, 
        `oi`.`referer` as 录单来源, 
        `cs`.`source_name` as 客户来源,
        `py`.`pay_name` as 订单订购类型, 
        `b`.`create_user` as 制单人, 
        `b`.`check_user` as 审核人, 
        `b`.`create_time` as 制单时间, 
        `b`.`check_time` as 审核时间, 
        `wg`.`caizhi` as 主成色, 
        `wg`.`zuanshidaxiao` as 主石重, 
        `wg`.`zhushixingzhuang` as 主石形状,	
        `wg`.`zhushiyanse` as 主石颜色,
        `wg`.`zhushijingdu` as 主石净度,
        `wg`.`zhushiqiegong` as 主石切工,	
        `wg`.`fushizhong` as 副石重, 
        `wg`.`jinzhong` as 金重, 
        `wg`.`cat_type1` as 新款式类型, 
        `wg`.`cat_type` as 款式类型,
        `g`.`shijia` as 实际价格, 
         `g`.sale_price as 成本价,
        `wg`.`mingyichengben` as 名义成本价, 
        `wg`.`yuanshichengbenjia` as 原始采购成本, 
        `wg`.`yuanshichengbenjia_zs` as 舟山原始采购成本, 
         if(b.from_company_id in(58,223),wg.yuanshichengbenjia,round(wg.yuanshichengbenjia*(1+IFNULL((select jiajialv from `warehouse_shipping`.`warehouse_bill_goods` bgs join `warehouse_shipping`.`warehouse_bill` bill on bill.id=bgs.bill_id where bill.bill_type='M' and bill.to_company_id<>58 and bill.to_company_id<>223 and bgs.goods_id=g.goods_id and (bill.from_bill_id=0 or bill.from_bill_id is null) and bill.check_time < ifnull(b.check_time,b.create_time) order by bgs.id desc limit 1),0)*0.01),2)) as 加价成本价,
        `wg`.`zhengshuhao` as 证书号,
        `wg`.`zhengshuleibie` as 证书类别,       	
        IF((SELECT is_invoice FROM `app_order`.`app_order_invoice` AS aoi WHERE aoi.order_id=oi.id AND is_invoice= 1 LIMIT 1) is null,'否','是')	AS '是否需要开票',
         wg.prc_name as 供应商名称,
         wg.goods_sn  as  款号,wg.product_type1  as  新产品线,
         wg.product_type  as  老产品线,
         case wg.put_in_type when 1 then '购买' when 2 then '委托加工' when 3 then '代销' when 4 then '借入' when 5 then '自采' else '' END   as  入库方式,							
         (select GROUP_CONCAT(x.name)  from front.app_style_xilie  x where  s.xilie like concat('%,',x.id,',%') group by s.style_sn) as 系列及款式归属 ,
         `b`.`order_sn` as 订单号,
         `oi`.`create_user` as 订单制单人,
         concat(wg.tax_rate,'%') as 税率   
        FROM 
        `warehouse_shipping`.`warehouse_bill_goods` as `g` 
        inner join `warehouse_shipping`.`warehouse_goods` as `wg` on `g`.`goods_id` = `wg`.`goods_id`
        left join front.base_style_info s on `wg`.goods_sn=s.style_sn  
        inner join `warehouse_shipping`.`warehouse_bill` as `b` on`g`.`bill_id` = `b`.`id` 
        left join `app_order`.`base_order_info` `oi` on `oi`.`order_sn` = `b`.`order_sn`
        left join `cuteframe`.`sales_channels` `sc` on `sc`.`id` = `oi`.`department_id`
        left join `cuteframe`.`customer_sources` `cs` on `cs`.`id` = `oi`.`customer_source_id`
        left join `cuteframe`.`payment` `py` on `py`.`id` = `oi`.`order_pay_type`         
        WHERE 
        `b`.`bill_type` = 'S' 
        AND `b`.`bill_status` = 2 ".$str."
        AND `b`.`check_time` >= '".$start_time." 00:00:00'
        AND `b`.`check_time` <= '".$end_time." 23:59:59'
        ORDER BY `b`.`id` DESC";
       }else if($bill_type == 'D'){
           if($company_id != 58){
               $str .= "AND `b`.`to_company_id` = {$company_id} ";
           }
           $sql ="SELECT  
        `b`.`bill_no` as D退货单, 
        `b`.`order_sn` as 订单号, 
        `wg`.`goods_id` as 货号, 
        `g`.`num` as 退货数量, 
        `b`.`to_company_name` as 入库公司, 
        `sc`.`channel_name` as 销售渠道, 
        `oi`.`referer` as 录单来源,
        `cs`.`source_name` as 客户来源, 
        `py`.`pay_name` as 订单订购类型, 
        `b`.`create_user` AS 制单人, 
        `b`.`check_user` AS 审核人, 
        `b`.`create_time` AS 制单时间, 
        `b`.`check_time` AS 审核时间, 
        `wg`.`caizhi` as 主成色, 
        `wg`.`zuanshidaxiao` as 主石重, 
    	`wg`.`zhushixingzhuang` as 主石形状,	
        `wg`.`zhushiyanse` as 主石颜色,
        `wg`.`zhushijingdu` as 主石净度,
        `wg`.`zhushiqiegong` as 主石切工,		
        `wg`.`fushizhong` as 副石重, 
        `wg`.`jinzhong` as 金重, 
        `wg`.`cat_type1` as 新款式类型, 
        `wg`.`cat_type` as 款式类型, 
        `g`.`shijia` AS 实际价格, 
        `g`.sale_price as 成本价,
        `wg`.`mingyichengben` AS 名义成本价, 
        `wg`.`yuanshichengbenjia` AS 原始采购成本,
        `wg`.`yuanshichengbenjia_zs` as 舟山原始采购成本, 
        if(b.to_company_id in(58,223),wg.yuanshichengbenjia,round(wg.yuanshichengbenjia*(1+IFNULL((select jiajialv from `warehouse_shipping`.`warehouse_bill_goods` bgs join `warehouse_shipping`.`warehouse_bill` bill on bill.id=bgs.bill_id where bill.bill_type='M' and bill.to_company_id<>58 and bill.to_company_id<>223 and bgs.goods_id=g.goods_id and (bill.from_bill_id=0 or bill.from_bill_id is null) and bill.check_time < ifnull(b.check_time,b.create_time) order by bgs.id desc limit 1),0)*0.01),2)) as 加价成本价,
        `wg`.`zhengshuhao` as 证书号, 
        `wg`.`zhengshuleibie` as 证书类别,
        wg.prc_name as 供应商名称,
        wg.goods_sn  as  款号,wg.product_type1  as  新产品线,
         wg.product_type  as  老产品线,
         case wg.put_in_type when 1 then '购买' when 2 then '委托加工' when 3 then '代销' when 4 then '借入' when 5 then '自采' else '' END   as  入库方式, 	
        (select GROUP_CONCAT(x.name)  from front.app_style_xilie  x where  s.xilie like concat('%,',x.id,',%') group by s.style_sn) as 系列及款式归属 ,
        concat(wg.tax_rate,'%') as 税率
        FROM 
        `warehouse_shipping`.`warehouse_bill_goods` AS `g` 
        inner join `warehouse_shipping`.`warehouse_goods` AS `wg` on `g`.`goods_id` = `wg`.`goods_id` 
        left join front.base_style_info s on `wg`.goods_sn=s.style_sn
        inner join `warehouse_shipping`.`warehouse_bill` AS `b` on `g`.`bill_id` = `b`.`id` 
        left join `app_order`.`base_order_info` oi on `oi`.`order_sn` = `b`.`order_sn`
        left join `cuteframe`.`customer_sources` cs on `cs`.`id` = `oi`.`customer_source_id` 
        left join `cuteframe`.`sales_channels` sc on `sc`.`id` = `oi`.`department_id`
	    left join `cuteframe`.`payment` `py` on `py`.`id` = `oi`.`order_pay_type` 	    
        WHERE 
        `b`.bill_type = 'D'
        AND `b`.bill_status = 2 ".$str." 
        AND b.check_time >= '".$start_time." 00:00:00'
        AND b.check_time <= '".$end_time." 23:59:59'
        ORDER BY `b`.id DESC";
       }else if($bill_type == "P"){
           $sql = "SELECT  b.`bill_no` AS 单号,b.`check_time` AS 仓储销账审核日期, wg.goods_id as 货号,wg.cat_type as 款式类型,b.`from_company_name` as 出库公司 ,c. wholesale_name AS 批发客户,g.num as 货品数量, wg.caizhi as 材质, wg.zuanshidaxiao as 主石重, wg.fushizhong as 副石重, wg.jinzhong as  金重,wg. mingyichengben as 名义成本,g.shijia as 实际价格,wg.`yuanshichengbenjia` AS 原始成本价, wg.`yuanshichengbenjia_zs` as 舟山原始采购成本,  b.`create_time` AS 制单时间,b.company_id_from as 标识,
            wg.prc_name as 供应商名称,
            wg.goods_sn  as  款号,wg.product_type1  as  新产品线,
         wg.product_type  as  老产品线,
         case wg.put_in_type when 1 then '购买' when 2 then '委托加工' when 3 then '代销' when 4 then '借入' when 5 then '自采' else '' END   as  入库方式,wg.zhengshuleibie as 证书类别,(select GROUP_CONCAT(x.name)  from front.app_style_xilie  x where  s.xilie like concat('%,',x.id,',%') group by s.style_sn) as 系列及款式归属,concat(wg.tax_rate,'%') as 税率 
        FROM `warehouse_shipping`.`warehouse_bill` AS `b` 
        inner join `warehouse_shipping`.`warehouse_bill_goods` AS `g` on  `g`.`bill_id` = `b`.`id`
        inner join `warehouse_shipping`.`warehouse_goods` AS `wg` on `g`.`goods_id` = `wg`.`goods_id`
        inner join `warehouse_shipping`.`jxc_wholesale`  as   c on  b. to_customer_id = c. wholesale_id
        left join front.base_style_info s on `wg`.goods_sn=s.style_sn  
        WHERE `b`.bill_status in (2,4) ".$str." 
        AND `b`.bill_type = 'P'
        AND b.check_time >= '".$start_time." 00:00:00'
        AND b.check_time <= '".$end_time." 23:59:59'
        ORDER BY `b`.id DESC";

       }else if($bill_type == "H"){
           $sql = "SELECT  b.`bill_no` AS 单号,b.`check_time` AS 仓储销账审核日期, wg.goods_id as 货号,wg.cat_type as 款式类型,b.`to_company_name` as 入库公司 ,c. wholesale_name AS 批发客户,g.num as 货品数量, wg.caizhi as 材质, wg.zuanshidaxiao as 主石重, wg.fushizhong as 副石重, wg.jinzhong as  金重,wg. mingyichengben as 名义成本,g.shijia as 实际价格,wg.`yuanshichengbenjia` AS 原始成本价, `wg`.`yuanshichengbenjia_zs` as 舟山原始采购成本,  b.`create_time` AS 制单时间,
            wg.prc_name as 供应商名称,
            wg.goods_sn  as  款号,wg.product_type1  as  新产品线,
         wg.product_type  as  老产品线,
         case wg.put_in_type when 1 then '购买' when 2 then '委托加工' when 3 then '代销' when 4 then '借入' when 5 then '自采' else '' END   as  入库方式,wg.zhengshuleibie as 证书类别,(select GROUP_CONCAT(x.name)  from front.app_style_xilie  x where  s.xilie like concat('%,',x.id,',%') group by s.style_sn) as 系列及款式归属,concat(wg.tax_rate,'%') as 税率 
        FROM `warehouse_shipping`.`warehouse_bill` AS `b` 
        inner join `warehouse_shipping`.`warehouse_bill_goods` AS `g` on `g`.`bill_id` = `b`.`id`
        inner join `warehouse_shipping`.`warehouse_goods` AS `wg` on `g`.`goods_id` = `wg`.`goods_id`
        inner join `warehouse_shipping`.`jxc_wholesale` as `c` on b. to_customer_id = c. wholesale_id
        left join front.base_style_info s on `wg`.goods_sn=s.style_sn  
        WHERE `b`.bill_status in (2,4) ".$str." 
        AND `b`.bill_type = 'H'
        AND b.check_time >= '".$start_time." 00:00:00'
        AND b.check_time <= '".$end_time." 23:59:59'
        ORDER BY `b`.id DESC";
       }


        //echo $sql;exit;
        $rows=$this->db()->getAll($sql);
        if($bill_type == 'S' && !empty($rows)){
            $payInfo = array('252');
            if(SYS_SCOPE == 'zhanting') array_push($payInfo,'321');
            foreach ($rows as $key => $value) {
                $rows[$key]['跨渠道以旧换新'] = '否';
                if($value['订单号']){
                    $info = $this->getdiankuanType($value['订单号']);
                    if($info) $info = array_column($info,'pay_type');
                    foreach ($info as $pay_id) {
                        if(in_array($pay_id, $payInfo)) $rows[$key]['跨渠道以旧换新'] = '是';
                    }
                }
            }
        }

        return $rows;
    }



    //根据订单获取点款方式
    public function getdiankuanType($order_sn)
    {
        $sql = "SELECT `pay_type` FROM finance.`app_order_pay_action` where order_sn = '{$order_sn}'";
        return $this->db()->getAll($sql);
    }


    
}

