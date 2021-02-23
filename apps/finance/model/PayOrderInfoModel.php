<?php

/**
 *  -------------------------------------------------
 *   @file		: PayOrderInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-26 15:33:31
 *   @update	:
 *  -------------------------------------------------
 */
class PayOrderInfoModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'pay_order_info';
        $this->pk = 'order_id';
        $this->_prefix = '';
        $this->_dataObject = array("order_id" => "销售明细ID",
            "kela_sn" => "BDD订单号",
            "external_sn" => "外部订单号",
            "make_order" => "制单人",
            "order_time" => "下单时间",
            "shipping_time" => "发货时间",
            "pay_id" => "支付方式ID",
            "pay_name" => "支付方式",
            "department" => "来源部门",
            "from_ad" => "订单来源",
            "status" => "1、待申请，2、待审核、3已审核、4、已驳回、5、待提交",
            "apply_number" => "应收申请单",
            "addtime" => "数据创建时间",
            "kela_total_all" => "BDD金额",
            "jxc_total_all" => "销账金额",
            "external_total_all" => "外部金额");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url PayOrderInfoController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        //不要用*,修改为具体字段
        $sql = "SELECT `order_id`,`external_sn`,`make_order`,`order_time`,`from_ad`,`department`,`apply_number`,`shipping_time`,`pay_name`,`status`,`kela_sn`,`kela_total_all`,`jxc_total_all`,`external_total_all` ";
        $sql .= "FROM `pay_order_info`";
        $str = '';
        if (!empty($where['kela_sn'])) {
            $str .= "`kela_sn` = '{$where['kela_sn']}' AND ";
        }
        if ((isset($where['department'])) && $where['department'] != '') {
            $str .= "`department` = {$where['department']} AND ";
        }
        if (!empty($where['pay_name'])) {
            $str .= "`pay_id` = {$where['pay_name']} AND ";
        }
        if (!empty($where['from_ad'])) {
            $str .= "`from_ad` = '{$where['from_ad']}' AND ";
        }
        if (!empty($where['external_sn'])) {
            $str .= "`external_sn` in({$where['external_sn']})  AND ";
        }

        if (!empty($where['apply_number'])) {
            $str .= "`apply_number` = '{$where['apply_number']}'  AND ";
        }
        if (!empty($where['status'])) {
            $str .= "`status` = {$where['status']} AND ";
        }
        if (!empty($where['order_time_start'])) {
            $str .= "`order_time` >= '" . $where['order_time_start'] . " 00:00:00' AND ";
        }
        if (!empty($where['order_time_end'])) {
            $str .= "`order_time` <= '" . $where['order_time_end'] . " 00:00:00' AND ";
        }
        if (!empty($where['shipping_time_start'])) {
            $str .= "`shipping_time` >= '" . $where['shipping_time_start'] . " 00:00:00' AND ";
        }
        if (!empty($where['shipping_time_end'])) {
            $str .= "`shipping_time` <= '" . $where['shipping_time_end'] . " 00:00:00' AND ";
        }

        if (isset($where['storage_mode']) && count($where['storage_mode']) > 0) {
            if (in_array(3, explode(",", $where['storage_mode']))) {
                $str.='(`jxc_total_all-`kela_total_all`)!=0 AND '; //销帐有误差 = 销账金额 减去 BDD金额
            }
            if (in_array(4, explode(",", $where['storage_mode']))) {
                $str.='(`kela_total_all`-`external_total_all`)!=0 AND ';  //4)制单误差：BDD金额 减去 外部金额
            }
        }
        $str.=" `from_ad` in('" . implode("','", $this->getAdList()) . "') AND ";
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }


    public function getInfoList($where){
        //不要用*,修改为具体字段
        $sql = "SELECT `poi`.`order_id`,`poi`.`external_sn`,`poi`.`make_order`,`poi`.`order_time`,`poi`.`from_ad`,`poi`.`apply_number`,`poi`.`shipping_time`,`poi`.`pay_name`,`poi`.`status`,`poi`.`kela_sn`,`poi`.`kela_total_all`,`poi`.`jxc_total_all`,`poi`.`external_total_all`,`edc`.`dep_name`,`ea`.`ad_name` ";
        $sql .= "FROM `pay_order_info` AS `poi`,`ecs_department_channel` AS `edc`,`ecs_ad` AS `ea`";
        $str = '';
        if (!empty($where['kela_sn'])) {
            $str .= "`poi`.`kela_sn` = '{$where['kela_sn']}' AND ";
        }
        if ((isset($where['department'])) && $where['department'] != '') {
            $str .= "`poi`.`department` = {$where['department']} AND ";
        }
        if (!empty($where['pay_name'])) {
            $str .= "`poi`.`pay_id` = {$where['pay_name']} AND ";
        }
        if (!empty($where['from_id'])) {
            $str .= "`poi`.`from_ad` = '{$where['from_id']}' AND ";
        }
        if (!empty($where['external_sn'])) {
            $str .= "`poi`.`external_sn` in({$where['external_sn']})  AND ";
        }

        if (!empty($where['apply_number'])) {
            $str .= "`poi`.`apply_number` = '{$where['apply_number']}'  AND ";
        }
        if (!empty($where['status'])) {
            $str .= "`poi`.`status` = {$where['status']} AND ";
        }
        if (!empty($where['order_time_start'])) {
            $str .= "`poi`.`order_time` >= '" . $where['order_time_start'] . " 00:00:00' AND ";
        }
        if (!empty($where['order_time_end'])) {
            $str .= "`poi`.`order_time` <= '" . $where['order_time_end'] . " 00:00:00' AND ";
        }
        if (!empty($where['shipping_time_start'])) {
            $str .= "`poi`.`shipping_time` >= '" . $where['shipping_time_start'] . " 00:00:00' AND ";
        }
        if (!empty($where['shipping_time_end'])) {
            $str .= "`poi`.`shipping_time` <= '" . $where['shipping_time_end'] . " 00:00:00' AND ";
        }

        if (isset($where['storage_mode']) && count($where['storage_mode']) > 0) {
            if (in_array(3, explode(",", $where['storage_mode']))) {
                $str.='(`poi`.`jxc_total_all-`poi`.`kela_total_all`)!=0 AND '; //销帐有误差 = 销账金额 减去 BDD金额
            }
            if (in_array(4, explode(",", $where['storage_mode']))) {
                $str.='(`poi`.`kela_total_all`-`poi`.`external_total_all`)!=0 AND ';  //4)制单误差：BDD金额 减去 外部金额
            }
        }
        $str.="`poi`.`department`=`edc`.`dc_id` AND ";
        $str.="`poi`.`from_ad`=`ea`.`ad_sn` AND `poi`.`from_ad` in('" . implode("','", $this->getAdList()) . "') AND ";
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $data = $this->db()->getAll($sql);
        return $data;
    }


    public function getAdList() {
        $model = new CustomerSourcesModel(1);
        $source_list = $model->getSourcesPay("id");
		$source_list = array_column($source_list,'id');
        return $source_list;
    }

    /**
     * 根据kela订单号修改
     * @param type $arr
     * @param type $kelaorder
     * @return type
     */
	public function update_pl($arr,$kelaorder)
	{
		$str = "";
		$val ="";
		foreach($arr as $k => $v)
		{
			$val .= ",$k = '$v'";
		}

		foreach ($kelaorder as $key=>$value)
		{
			$str .= ",'".$value."' ";
			$w = $key;
		}
		$str = substr($str,1);
		$sql = "update `".$this->table()."` set ".substr($val,1)." where  `kela_sn`  in (".$str.")";
		//echo $sql;
		return $this->db()->query($sql);
	}


    /*检查订单号和外部订单号是否符号要求
	参数 BDD订单号  外部订单号  订单来源
	1：BDD订单号不存在                                              1 BDD订单号xxxxx订单错误
	2：BDD订单号已经在其他申请单中申请                              2 BDD订单号已经在其他生成申请单
	3：外部订单号为空  不做任何限制
	4：外部订单号不为空：外部申请单号已经申请过  待审核 已审核 已驳回 3：外部订单号非待申请状态
	6: 外部订单号不为空：订单来源与申请单一致                         4：订单来源非指定来源

	只验证在销售收款中的  申请
	*/
	function checkOrderData($kelaSn,$externalSn,$from_ad,$apply_number)
	{
		$sql = "select `order_id`, `kela_sn`, `external_sn`, `make_order`, `order_time`, `shipping_time`, `pay_id`, `pay_name`, `department`, `from_ad`, `status`, `apply_number`, `addtime`, `kela_total_all`, `jxc_total_all`, `external_total_all` from `".$this->table()."` where `kela_sn` = '$kelaSn'"; //可能多条可能一条
		//echo $sql;

		if ($result =  $this->db()->getAll($sql)) //kela存在
		{
			$pay_detail_mode = new AppReceiveApplyDetailModel(29);
			//1、因为一个BDD订单号只能有一个状态 2、待审核、3已审核、4、已驳回 首先检查状态不正确
			if ($result[0]['status'] == 2 || $result[0]['status'] == 3 ||$result[0]['status'] == 4)
			{
				//克兰订单号状态不正确
				if(!$apply_number)//不是编辑
				{
					return 3;
				}else{//编辑的时候 排除自己的单据

					if("YSSQ".$apply_number != $result[0]['apply_number'])
					{
						return 3;
					}
				}
			}
			//2、检查是否已经在其他申请单中申请  根据应单号为空 或者有值是自己的单号 判断
			else if ($this->check_exist($result,$apply_number))
			{
				return 2;//其他单号已经申请
			}
			//3、2014/10/20 订单来源与申请单号相同
			else if ($result[0]['from_ad'] != $from_ad)
			{
				return 4; //订单来源不相同
			}
			//4、外部订单号已经申请过  只能查询申请表中的详细外部订单 排除自己申请单号  5
			else if ($pay_detail_mode->check_externalSn($apply_number,$externalSn)>=1)
			{
				return 5;  //外部单号已经申请
			}
			else
			{
				return 6;//5、验证通过
			}
		}
		else
		{
			return 1; //不存在
		}
	}


    //计算符合BDD订单号的数据条数
	function checkKelaSn($kela_sn,$from_ad=0)
	{
		$sql = "select count(*) from `".$this->table()."` where `kela_sn` = '".$kela_sn."'";
		if($from_ad != 0)
		{
			$sql .= " and `from_ad` = '$from_ad'";
		}
		return $this->db()->getOne($sql);
	}



    /**
     * 检车BDD订单号是否已经在其他申请单中
     * @param type $result
     * @param string $apply_number
     * @return boolean
     */
	function check_exist ($result,$apply_number='')
	{
		if ($apply_number)
		{
			$apply_number = "YSSQ".$apply_number;
		}
		if (is_array($result))
		{
			foreach($result as $value)
			{
				if ($value['apply_number'] == '')//申请单为不为空的其他申请单  修改时不等于本身
				{
					return false;//用于修改添加是
				}
				if ($value['apply_number'] != $apply_number)
				{
					return true;  //用于修改本身
				}
			}
			return false;
		}
	}

	/*跟新金额*/
	public function updateTotal($str,$where){
		$sql ='UPDATE '.$this->table().' SET '.$str.' WHERE '.$where;
		$this->db()->query($sql);
	}

}

?>