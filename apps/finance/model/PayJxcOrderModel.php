<?php

/**
 *  -------------------------------------------------
 *   @file		: PayJxcOrderModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-26 16:08:05
 *   @update	:
 *  -------------------------------------------------
 */
class PayJxcOrderModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'pay_jxc_order';
        $this->pk = 'order_id';
        $this->_prefix = '';
        $this->_dataObject = array("order_id" => "销售出入库ID",
            "jxc_order" => "进销存单号",
            "kela_sn" => "BDD订单号",
            "type" => "单据类型：S、销售单，B、销售退货单",
            "status" => "状态：1、待核销，2、待审核，3、已审核，4、已驳回",
            "goods_num" => "单据货品数量",
            "chengben" => "成本价",
            "shijia" => "销售价",
            "addtime" => "单据下单时间",
            "checktime" => "单据审核时间",
            "hexiaotime" => "核销时间",
            "hexiao_number" => "核销单号",
            "is_return" => "是否回款：0、否，1、是",
            "returntime" => "回款时间");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url PayJxcOrderController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        //不要用*,修改为具体字段
        $sql = "SELECT pjo.`jxc_order`,pjo.`type`,pjo.`type` as type1,pjo.`checktime`,pjo.`goods_num`,pjo.`chengben`,pjo.`shijia`,pjo.`kela_sn`,pjo.`status`,pjo.`hexiao_number`,pjo.`is_return`,pjo.`hexiaotime`,pjo.`returntime`,poi.from_ad FROM `pay_jxc_order` AS pjo,`pay_order_info` AS poi";
        $str = "pjo.`kela_sn`=poi.`kela_sn` AND poi.from_ad in('".implode("','",  $this->getAdList())."') AND ";
        if (!empty($where['jxc_order'])) {
            $str .= "pjo.`jxc_order` = '{$where['jxc_order']}' AND ";
        }

        if (!empty($where['jxc_orders'])) {
            $str .= "pjo.`jxc_order` in({$where['jxc_orders']}) AND ";
        }

        if (!empty($where['from_ad'])) {
            $str .= "poi.`from_ad` = '{$where['from_ad']}' AND ";
        }

        if (!empty($where['kela_sn'])) {
            $str .= "pjo.`kela_sn` = '{$where['kela_sn']}' AND ";
        }
        if (!empty($where['kela_sn_all'])) {
            $str .= "pjo.`kela_sn` in({$where['kela_sn_all']}) AND ";
        }
        if (!empty($where['type'])) {
            $str .= "pjo.`type` = '{$where['type']}' AND ";
        }
        if (!empty($where['status'])) {
            $str .= "pjo.`status` = '{$where['status']}' AND ";
        }
        if (!empty($where['goods_num'])) {
            $str .= "pjo.`goods_num` in '{$where['goods_num']}' AND ";
        }

        if (!empty($where['hexiao_number'])) {
            $str .= "pjo.`hexiao_number` = '{$where['hexiao_number']}' AND ";
        }

        if (!empty($where['addtime_start'])) {
            $str .= "pjo.`addtime` >= '" . $where['addtime_start'] . " 00:00:00' AND ";
        }
        if (!empty($where['addtime_end'])) {
            $str .= "pjo.`addtime` <= '" . $where['addtime_end'] . " 23:59:59' AND ";
        }
        if (!empty($where['checktime_start'])) {
            $str .= "pjo.`checktime` >= '" . $where['checktime_start'] . " 00:00:00' AND ";
        }
        if (!empty($where['checktime_end'])) {
            $str .= "pjo.`checktime` <= '" . $where['checktime_end'] . " 23:59:59' AND ";
        }
        if (!empty($where['hexiaotime_start'])) {
            $str .= "pjo.`hexiaotime` >= '" . $where['hexiaotime_start'] . " 00:00:00' AND ";
        }
        if (!empty($where['hexiaotime_end'])) {
            $str .= "pjo.`hexiaotime` <= '" . $where['hexiaotime_end'] . " 23:59:59' AND ";
        }
        if (!empty($where['is_return'])) {
            if ($where['is_return'] == '2') {
                $str .= "pjo.`is_return` = '0' AND ";
            } else {
                $str .= "pjo.`is_return` = '{$where['is_return']}' AND ";
            }
        }
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        foreach ($data['data'] as &$value) {
			$value['hexiao_zq'] = $this->getDateGap($value['checktime'],$value['hexiaotime']);
			$value['return_zq'] = $this->getDateGap($value['checktime'],$value['returntime']);
		}
        unset($value);
        return $data;
    }

    /**
	 *说 明：explain 这是计算两个时间相差天数的方法
	* 参 数：@param $startDate (string || date)
	* 参 数：@param $endDate (string || date)
	* 返回值 @return ：gapDate  float
	*/
	public function getDateGap($startDate,$endDate){
		$dateOne = strtotime($startDate);
		$dateTwo = time();
		if($startDate && $endDate != '0000-00-00 00:00:00'){
			$dateTwo = strtotime($endDate);
		}
		return floor(($dateTwo - $dateOne)/86000);
	}

    function getInfoList($where) {
        $sql = "SELECT pjo.`jxc_order`,ea.`ad_name`,pjo.`type`,pjo.`checktime`,pjo.`goods_num`,pjo.`chengben`,pjo.`shijia`,pjo.`kela_sn`,pjo.`status`,pjo.`hexiao_number`,pjo.`is_return`,pjo.`hexiaotime`,pjo.`returntime` FROM `pay_jxc_order` AS pjo,`pay_order_info` AS poi,`ecs_ad` AS ea";
        $str = "pjo.`kela_sn`=poi.`kela_sn` AND poi.`from_ad`=ea.`ad_sn` AND ea.ad_sn in('".implode("','",  $this->getAdList())."') AND ";
        if (!empty($where['jxc_order'])) {
            $str .= "pjo.`jxc_order` = '{$where['jxc_order']}' AND ";
        }

        if (!empty($where['jxc_orders'])) {
            $str .= "pjo.`jxc_order` in({$where['jxc_orders']}) AND ";
        }

        if (!empty($where['from_ad'])) {
            $str .= "poi.`from_ad` = '{$where['from_ad']}' AND ";
        }

        if (!empty($where['kela_sn'])) {
            $str .= "pjo.`kela_sn` = '{$where['kela_sn']}' AND ";
        }
        if (!empty($where['kela_sn_all'])) {
            $str .= "pjo.`kela_sn` in({$where['kela_sn_all']}) AND ";
        }
        if (!empty($where['type'])) {
            $str .= "pjo.`type` = '{$where['type']}' AND ";
        }
        if (!empty($where['status'])) {
            $str .= "pjo.`status` = '{$where['status']}' AND ";
        }
        if (!empty($where['goods_num'])) {
            $str .= "pjo.`goods_num` in '{$where['goods_num']}' AND ";
        }

        if (!empty($where['hexiao_number'])) {
            $str .= "pjo.`hexiao_number` = '{$where['hexiao_number']}' AND ";
        }

        if (!empty($where['addtime_start'])) {
            $str .= "pjo.`addtime` >= '" . $where['addtime_start'] . " 00:00:00' AND ";
        }
        if (!empty($where['addtime_end'])) {
            $str .= "pjo.`addtime` <= '" . $where['addtime_end'] . " 23:59:59' AND ";
        }
        if (!empty($where['checktime_start'])) {
            $str .= "pjo.`checktime` >= '" . $where['checktime_start'] . " 00:00:00' AND ";
        }
        if (!empty($where['checktime_end'])) {
            $str .= "pjo.`checktime` <= '" . $where['checktime_end'] . " 23:59:59' AND ";
        }
        if (!empty($where['hexiaotime_start'])) {
            $str .= "pjo.`hexiaotime` >= '" . $where['hexiaotime_start'] . " 00:00:00' AND ";
        }
        if (!empty($where['hexiaotime_end'])) {
            $str .= "pjo.`hexiaotime` <= '" . $where['hexiaotime_end'] . " 23:59:59' AND ";
        }
        if (!empty($where['is_return'])) {
            if ($where['is_return'] == '2') {
                $str .= "pjo.`is_return` = '0' AND ";
            } else {
                $str .= "pjo.`is_return` = '{$where['is_return']}' AND ";
            }
        }
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $data = $this->db()->getAll($sql);
        foreach ($data as &$value) {
			$value['hexiao_zq'] = $this->getDateGap($value['checktime'],$value['hexiaotime']);
			$value['return_zq'] = $this->getDateGap($value['checktime'],$value['returntime']);
		}
        unset($value);
        return $data;
    }

     public function getAdList() {
        $model = new CustomerSourcesModel(1);
        $source_list = $model->getSourcesPay("id");
		$source_list = array_column($source_list,'id');
        return $source_list;
    }

    /**
	* 获取指定信息
	* @param $col String 要查询的字段
	* @param $where Array 查询条件 格式 array('order_id'=>1000)
	* @return 结果集
	*/
	public function getRow($col='*',$where){
		$sql = ' SELECT '.$col.' FROM `'.$this->table();
		$sql .= '` WHERE 1';
		$condition = '';
		foreach ($where as $key => $value) {
			$condition .= ' AND '.$key.' = \''.$value.'\' ,';
		}
		$sql.=rtrim($condition,',');
		return $this->db()->getRow($sql);
	}

    /**
	* 修改指定字段的值
	* @param $id Int 主键 order_id
	* @param $arr Array 字段=>值
	* @return Bool
	*/
	public function updateRealValue($id,$arr){
		$condition = '';
		$sql = 'UPDATE `'.$this->table().'` SET ';
		foreach ($arr as $key => $value) {
			$condition .= ", {$key} = '{$value}' ";
		}
		$sql = $sql.ltrim($condition,',');
		$sql .= ' WHERE `order_id` = '.$id;

		return $this->db()->query($sql,array());
	}


    /**
     * 批量修改销售出入库单号的状态
     * @param type $arr
     * @param type $jxc_order
     * @return type
     */
    public function update_status($arr,$jxc_order)
	{
		$str = "";
		$val = "";
		foreach($arr as $k => $v)
		{
			$val .= ",$k = '$v'";
		}

		foreach ($jxc_order as $key=>$value)
		{
			$str .= ",'".$value['jxc_order']."' ";
		}
		$str = substr($str,1);
		$sql = "update `".$this->table()."` set ".substr($val,1)." where  `jxc_order`  in (".$str.")";
		//echo $sql;exit;
		return $this->db()->query($sql,array());
	}


    /**
	* 获取相同BDD订单号kela_sn下 所有记录销账金额=S单（销售）- B单（退货）
	* @from ApplyModel.php
	* @return 销账金额
	*/
	public function CountJxcTotalApply($apply_id){
		$sql ='SELECT `pad`.`kela_sn`,`pjo`.`type`,`pjo`.`shijia` FROM `'.$this->table().'` AS `pjo` , `app_receive_apply_detail` AS `pad` WHERE `pjo`.`kela_sn` = `pad`.`kela_sn` AND `pad`.`apply_id` = '.$apply_id;
		$data = $this->db()->getAll($sql);
		$jxc_total = 0;
		if (count($data))
		{
			foreach ($data as $key => $value)
			{
				$jxc_total -= $data[$key]['type'] == 'B' ? $data[$key]['shijia'] : 0 ;
				$jxc_total += $data[$key]['type'] == 'S' ? $data[$key]['shijia'] : 0 ;
			}
		}
		return $jxc_total;
	}


}

?>