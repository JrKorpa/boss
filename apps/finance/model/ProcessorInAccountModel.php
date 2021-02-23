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
class ProcessorInAccountModel extends Model {

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

    function getAdList() {
        $data = array(
            '008100822414', //京东SOP
            '000000261852', //官方微信会员
            '000300142313', //工商银行
            '000300140703', //广发银行
            '000300140534', //民生银行分期POS
            '000300140636', //平安银行
            '000300140714', //交通银行
            '000300140998', //建设银行分期
            '000300140586', //招行信用卡分期商城
            '001000160987', //聚美优品（TTP）
            '000300160566', //中国移动积分
            '001000141530', //联合优至
            '001000162508', //东方美宝
            '001000160607', //卓越商城
            '001000161271', //苏宁易购
            '001000162034', //唯品会B2C
            '001000162548'//大溪地珠宝平台
        );
        return $data;
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


        public function GetJiagongshangCompany($jiagongshang=false) {
            $arr =  array(
                    array('p_id'=>'58','p_name'=>'总公司','p_sn'=>'SZ'),
                    array('p_id'=>'375','p_name'=>'水贝贝雅','p_sn'=>'SBBY'),
                    array('p_id'=>'376','p_name'=>'水贝欧若雅','p_sn'=>'SBORY'),
                    array('p_id'=>'377','p_name'=>'水贝悠米','p_sn'=>'SBAF'),
                    array('p_id'=>'379','p_name'=>'水贝缔诺品牌','p_sn'=>'SBDNPP'),
                    array('p_id'=>'224','p_name'=>'上海中兴路体验店','p_sn'=>'SH3'),
                    array('p_id'=>'414','p_name'=>'南京珠江一号体验店','p_sn'=>'NJ2'),
            );
            if ($jiagongshang)
            {
                unset($arr[0]);
                unset($arr[1]);
                unset($arr[2]);
                unset($arr[3]);
                unset($arr[4]);
            }

            return $arr;
        }

        /*
         * 通过接口获取加工商结算信息
         */

        public  function GetProcessorInfo($args){

            $page = $args['page'];
            $keys = array(
                'company',
                'pro_id',
                'fin_status',
                'account_type',
                'pay_channel',
                'make_time_start',
                'make_time_end',
                'check_time_start',
                'check_time_end',
                'fin_check_time_start',
                'fin_check_time_end',
                'put_in_type',
                'type',
                'page'
                );
            $vals = array(
                $args['company'],
                $args['pro_id'],
                $args['fin_status'],
                $args['account_type'],
                $args['pay_channel'],
                $args['make_time_start'],
                $args['make_time_end'],
                $args['check_time_start'],
                $args['check_time_end'],
                $args['fin_check_time_start'],
                $args['fin_check_time_end'],
                $args['put_in_type'],
                'in',
                $page

            );
            $ret = ApiModel::warehouse_api($keys, $vals, "GetProcessorInAccount");

            return $ret;
        }

        public function checkStatus($bill_no) {
           $ret = ApiModel::warehouse_api(array("bill_no"), array($bill_no), "checkFinCheckStatus");

           return $ret;
        }

}

?>