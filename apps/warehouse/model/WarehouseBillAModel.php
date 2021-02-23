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
class WarehouseBillAModel extends Model
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
        	
        );
        

        parent::__construct($id, $strConn);
    }

    function pageList($where, $page, $pageSize = 10, $useCache = true,$total_num='')
    {
        if ($where['goods_id'] == '') {

            $lab = ['id` as `b_id', 'bill_no', 'bill_type', 'bill_status', 'goods_num', 'order_sn',
                'send_goods_sn', 'from_company_name', 'to_company_name', 'to_warehouse_name',
                'create_user', 'check_user', 'create_time', 'check_time','company_from','company_id_from','from_bill_id', 'bill_note',
                'goods_total', 'goods_total_jiajia', 'shijia', 'pro_name', 'pro_id'];

            $sel = implode('`,b.`', $lab);
            $sel = "b.`" . $sel . "`";

            //$sql = "SELECT ".$sel.",wg.`pro_id` as prc_id,wg.`pro_name` as prc_name FROM `warehouse_bill` AS `b`,`warehouse_bill_goods` as `g`,CONCAT('warehouse_bill_info_',lower(b.bill_type)) as `wg` where `g`.`bill_id`=`b`.`id` and `b`.`id`=`wg`.`bill_id`";

            //$sql = "SELECT ".$sel.",wg.`prc_id`,wg.`prc_name` FROM `warehouse_bill` AS `b` left join `warehouse_bill_goods` as `g` on `g`.`bill_id`=`b`.`id` left join `warehouse_goods` as `wg` on  `g`.`goods_id`=`wg`.`goods_id` where 1 ";
            if ($where['account_type'] != "") {
                $sql = "SELECT " . $sel .
                 ",wg.*, SUM(wg.`yuanshichengbenjia`) AS total_chengben FROM `warehouse_bill_goods` AS `g` left join `warehouse_bill` as `b` on `g`.`bill_id`=`b`.`id` left join `warehouse_goods` as `wg` on  `g`.`goods_id`=`wg`.`goods_id` left join `warehouse_bill_pay` as wp on g.`bill_id`=wp.`bill_id` where 1 ";
            } else {
                $sql = "SELECT " . $sel .
                ",wg.*,SUM(wg.`yuanshichengbenjia`) AS total_chengben FROM `warehouse_bill_goods` AS `g` left join `warehouse_bill` as `b` on `g`.`bill_id`=`b`.`id` left join `warehouse_goods` as `wg` on  `g`.`goods_id`=`wg`.`goods_id` where 1 ";
            }
           
            //$sql = "select `b`.*,g.`goods_id`,wg.`prc_id`,wg.`prc_name` from `{$this->table()}` as `b`,warehouse_bill_goods as `g`,warehouse_goods as wg where `g`.`bill_id`=`b`.`id` and `g`.`goods_id`=`wg`.`goods_id`";//echo $sql;exit;
        } else {

            //$sql = "select `b`.*,g.`goods_id`,wg.`prc_id`,wg.`prc_name` from `{$this->table()}` as `b`,warehouse_bill_goods as `g`,warehouse_goods as wg where `g`.`bill_id`=`b`.`id` and `g`.`goods_id`=`wg`.`goods_id`";//echo $sql;exit;
            $sql = "select `b`.*,g.`goods_id`,wg.`prc_id`,wg.`prc_name`,wp.`pay_method`,SUM(wg.`yuanshichengbenjia`) AS total_chengben from `{$this->table()}` as `b`,warehouse_bill_goods as `g`,warehouse_goods as wg,`warehouse_bill_pay` as `wp` where `g`.`bill_id`=`b`.`id` and `g`.`bill_id` = `wp`.`bill_id` and `g`.`goods_id`=`wg`.`goods_id` "; //echo $sql;exit;
            //屏蔽发货生成的单据
           
        }
        $str = '';
        if ($where['send_goods_sn'] != "") {
            $str .= " and `b`.send_goods_sn like \"%" . addslashes($where['send_goods_sn']) .
                "%\"";
        }
        if ($where['bill_no'] != "") {
            $str .= " and `b`.bill_no like \"%" . addslashes($where['bill_no']) . "%\"";
        }
        if ($where['create_user'] != "") {
            $str .= " and `b`.create_user = '"   . addslashes($where['create_user'])."'";
        }
        if ($where['bill_type'] !== "") {
            $str .= " and `b`.bill_type = '" . $where['bill_type'] . "' ";
        }
        if ($where['order_sn'] !== "") {
            $str .= " and `b`.order_sn = '" . $where['order_sn'] . "' ";
        }
        if ($where['bill_status'] !== "") {
            $str .= " and `b`.bill_status = " . $where['bill_status'];
        }
        if ($where['from_company_id'] != 0) {
            $str .= " and `b`.from_company_id = " . $where['from_company_id'];
        }
        if ($where['to_company_id'] != 0) {
            $str .= " and `b`.to_company_id = " . $where['to_company_id'] . ' ';
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
        if ($where['bill_note'] != "") {
            $str .= " and `b`.bill_note like \"%" . addslashes($where['bill_note']) . "%\"";
        }
        if ($where['mohao'] != "") {
            $str .= " and wg.`mo_sn`='{$where['mohao']}'";
        }
        if ($where['put_in_type'] != "") {
            $str .= " and wg.`put_in_type`= " . $where['put_in_type'];
        }
        if ($where['account_type'] != "") {
            $str .= " and wp.`pay_method`=" . $where['account_type'];
        }

		if($_SESSION['userName']!="董小华")
            $str .= " and `b`.`bill_no` not in ('B201510125955187','B201510203345215','B201510235555232') " ;
      
        	$sql.=" and ((b.bill_type='L' and b.from_bill_id !='') or (b.bill_type='S' and b.company_id_from !='') )  AND wg.put_in_type != 2";
        	$sql2= $sql.$str . " group by `b`.id ORDER BY `b`.`id`  DESC ";
        	$sql .= $str . " group by `g`.id ORDER BY `b`.`check_time` DESC";
        //echo $sql;exit;
        

       $data = $this->getPageList($sql, array(), $page, $pageSize, $useCache);
      
        if($total_num ==1){
            $sql1 ="SELECT SUM(wb.goods_num) as total_num,SUM(wb.total_chengben) as total_price,SUM(wb.shijia) as total_shijia FROM (".$sql2.") as wb";
            $total = $this->db()->getRow($sql1);
            $data['total_num'] = $total['total_num']?$total['total_num']:0;
            $data['total_price'] = $total['total_price']?$total['total_price']:0.00;
            $data['total_shijia'] = $total['total_shijia']?$total['total_shijia']:0.00;
        }
        return $data;
    }

    public function goodsBillList($where, $page, $pageSize = 10, $useCache = true,$total_num='')
    {
        $sql = "SELECT `g`.`bill_id`,`g`.`bill_no`,`g`.`bill_type`,`b`.`to_warehouse_name`, `b`.`bill_status` ,`b`.`to_company_name`,`b`.`from_company_name`,`b`.`send_goods_sn`,`b`.`create_user`,`b`.`create_time`,`b`.`check_user`,`b`.`check_time`,`b`.`fin_check_time`,`b`.`bill_note`,`b`.`pro_name`,`b`.`order_sn`,`b`.`goods_num`,`b`.`company_from`,`b`.`company_id_from`,`b`.`from_bill_id`,g.sale_price,g.shijia,wg.*  FROM `warehouse_bill_goods` AS `g` LEFT JOIN `warehouse_bill` AS `b` ON `g`.`bill_id` = `b`.`id` LEFT JOIN `warehouse_goods` as wg on wg.goods_id = g.goods_id ";

        if ($where['account_type'] != "") {
            $sql .= " left join `warehouse_bill_pay` as wp on wp.bill_id = b.id WHERE wp.`pay_method`=" .
                $where['account_type'];
        } else {
            $sql .= " where 1 ";
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
        if ($where['order_sn'] !== "") {
            $sql .= " AND `b`.order_sn = '" . $where['order_sn'] . "' ";
        }
        if ($where['bill_status'] !== "") {
            $sql .= " AND `b`.bill_status = " . $where['bill_status'];
        }
        if ($where['from_company_id'] != 0) {
            $sql .= " AND `b`.from_company_id = " . $where['from_company_id'];
        }
        if ($where['to_company_id'] != 0) {
            $sql .= " AND `b`.to_company_id = " . $where['to_company_id'];
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
            $sql .= " and wg.`put_in_type`=" . $where['put_in_type'];
        }
		if($_SESSION['userName']!="董小华")
            $sql .= " and `b`.`bill_no` not in ('B201510125955187','B201510203345215','B201510235555232') " ;
        
      
       	$sql.=" and ((b.bill_type='L' and b.from_bill_id !='') or (b.bill_type='S' and b.company_id_from !='') ) AND wg.put_in_type != 2";
       	$sql .= " ORDER BY `b`.`check_time` DESC";
       
        
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        //print_r($data);exit;
        if($total_num ==1){
             $sql1 ="SELECT SUM(wb.goods_num) as total_num,SUM(wb.yuanshichengbenjia) as total_price,SUM(wb.shijia) as total_shijia FROM (".$sql.") as wb";
            $total = $this->db()->getRow($sql1);
            $data['total_num'] = $total['total_num']?$total['total_num']:0;
            $data['total_price'] = $total['total_price']?$total['total_price']:0.00;
            $data['total_shijia'] = $total['total_shijia']?$total['total_shijia']:0.00;
        }
        return $data;
    }

    public function getPageList($sql, $params = array(), $page = 1, $pageSize = 20,
        $useCache = false)
    {
        if($pageSize>200000)
             $pageSize=200000; 
        try {
            
            $countSql = "SELECT COUNT(*) as count FROM (" . str_replace('ORDER BY `b`.`check_time` DESC', ' ',$sql) . ") AS xxxxxx";
            $data['pageSize'] = (int)$pageSize < 1 ? 20 : (int)$pageSize;
            $data['recordCount'] = $this->db()->getOne($countSql, $params, $useCache);
            $data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
            $data['page'] = $data['pageCount'] == 0 ? 0 : ((int)$page < 1 ? 1 : (int)$page);
            $data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
            $data['isFirst'] = $data['page'] > 1 ? false : true;
            $data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
            $data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] +
                1;
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
        $sql = "SELECT a.`goods_id`,a.`goods_sn`,b.`jinzhong`,b.`zhushilishu`,b.`zuanshidaxiao`,a.`zuanshidaxiao` as size,a.`yuanshichengben`,a.`sale_price`,a.`yanse`,a.`jingdu`,b.`zhengshuhao`,a.`goods_name`,b.`fushilishu`,b.`fushizhong`,b.`shi2lishu`,b.`shi2zhong`,b.`shoucun`,b.`xianzaixiaoshou`,b.`chengbenjia`,b.`zuixinlingshoujia`,b.`cat_type`,b.`mo_sn`,b.`num`,b.`buchan_sn`,a.`shijia`,a.`account`,a.`in_warehouse_type`,	
            a.`jiajialv`,
            b.`prc_id`,b.`prc_name`,b.`zhushibaohao`,b.`fushibaohao`
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
        $sql = "SELECT `pro_name`,`pay_content`,`amount`  FROM `warehouse_bill_pay`  WHERE `bill_id` = '{$bill_id}'";
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
        $data = ApiSalesModel::GetOrderInfoByOrdersn($order_sn);
        if (empty($data['return_msg'])) {
            return false;
        } else {
            return true;
        }
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
        $sql = "SELECT `id` , `is_deleted` FROM `warehouse_box` WHERE `warehouse_id` = {$warehouse_id} LIMIT 1";
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
    }

    function GetBaoxianFei($xiangkou)
    {
        $xiangkou = $xiangkou * 10000;
        $baoxianfei = ApiModel::style_api('', '', "getAllbaoxianfee");
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
}

