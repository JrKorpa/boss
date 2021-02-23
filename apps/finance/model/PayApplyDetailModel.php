<?php

/**
 *  -------------------------------------------------
 *   @file		: PayApplyDetailModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 18:01:26
 *   @update	:
 *  -------------------------------------------------
 */
class PayApplyDetailModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'pay_apply_detail';
        $this->pk = 'detail_id';
        $this->_prefix = '';
        $this->_dataObject = array("detail_id" => " ",
            "apply_id" => "申请单ID",
            "replytime" => "对账日期",
            "external_sn" => "外部订单号",
            "kela_sn" => "BDD订单号",
            "external_total" => "订单金额/退款金额(外部金额)",
            "pay_xj" => "客户支付信息--现金支付",
            "pay_jf" => "客户支付信息--平台积分支付",
            "pay_pt_yhq" => "客户支付信息--平台优惠券支付",
            "pay_kela_yhq" => "客户支付信息--BDD优惠券支付",
            "f_koudian" => "费用--扣点",
            "f_yongjin" => "费用--佣金",
            "f_jingdong" => "费用--京豆、京券",
            "f_yunfei" => "费用--运费",
            "f_peifu" => "费用--卖家赔付",
            "f_chajia" => "费用--退差价",
            "f_youhui" => "费用--活动优惠",
            "f_weiyue" => "费用--违约罚款",
            "f_qita" => "费用--其他",
            "sy_fanyou" => "收益--反邮",
            "sy_qita" => "收益--其他",
            "total" => "应收现金",
            "reoverrule_reason" => "驳回原因");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url PayApplyDetailController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        $model = new PayJxcOrderModel(29);
        $from_ad_list = $model->getAdList();
        $from_ads = implode("','", $from_ad_list);
        $sql = "SELECT `ad_sn`,`ad_name` FROM `ecs_ad` WHERE `ad_sn` IN ('$from_ads')";
        if (isset($where['company']) && $where['company'] != '') {
            $sql .= ' AND `ad_department` = ' . $where['company'];
        }
        if (!empty($where['from_ad'])) {
            $sql .= ' AND `ad_sn` = \'' . $where['from_ad'] . '\' ';
        }
        $data = $this->db()->getAll($sql);
        foreach ($data as $k => $v) {
            //初始化数据列表
            $data[$k]['xse'] = $data[$k]['cb'] = $data[$k]['fy'] = $data[$k]['sy'] = $data[$k]['lr'] = $data[$k]['xsje'] = $data[$k]['xsfy'] = $data[$k]['xssy'] = $data[$k]['xscb'] = $data[$k]['xslr'] = $data[$k]['thje'] = $data[$k]['thfy'] = $data[$k]['thsy'] = $data[$k]['thcb'] = 0;
            $pass_hx_id = $this->checkPassHxID($data[$k]['ad_sn']);
            $pass_app_id = $this->checkPassAppID($data[$k]['ad_sn']);
            // echo '<pre>';print_r($pass_app_id);echo '</pre>';
            if (count($pass_hx_id) || count($pass_app_id)) {
                $count_total = $this->countTotal($pass_hx_id, $pass_app_id, $where);
                $data[$k]['xse'] = $count_total['xse'] ? $count_total['xse'] : 0;
                $data[$k]['cb'] = $count_total['cb'] ? $count_total['cb'] : 0;
                $data[$k]['fy'] = $count_total['fy'] ? $count_total['fy'] : 0;
                $data[$k]['sy'] = $count_total['sy'] ? $count_total['sy'] : 0;
                $data[$k]['lr'] = $count_total['xse'] - $count_total['cb'] - $count_total['fy'] + $count_total['sy']; //利润
                $data[$k]['xsje'] = $count_total['xsje'] ? $count_total['xsje'] : 0;
                $data[$k]['xsfy'] = $count_total['xsfy'] ? $count_total['xsfy'] : 0;
                $data[$k]['xssy'] = $count_total['xssy'] ? $count_total['xssy'] : 0;
                $data[$k]['xscb'] = $count_total['xscb'] ? $count_total['xscb'] : 0;
                $data[$k]['xslr'] = $count_total['xsje'] - $count_total['xsfy'] - $count_total['xscb'] + $count_total['xssy']; //销售利润
                $data[$k]['thje'] = $count_total['thje'] ? $count_total['thje'] : 0;
                $data[$k]['thfy'] = $count_total['thfy'] ? $count_total['thfy'] : 0;
                $data[$k]['thsy'] = $count_total['thsy'] ? $count_total['thsy'] : 0;
                $data[$k]['thcb'] = $count_total['thcb'] ? $count_total['thcb'] : 0;
            }
        }
        // echo '<pre>';print_r($data);echo '</pre>';
        return $data;
    }

    /**
     * 根据订单来源获取->核销ID(hx_id)集合, 检测 过滤 未审核的核销单
     * @param string $ad_sn
     * @return array
     */
    public function checkPassHxID($ad_sn) {
        $data = array();
        $sql = "SELECT `hx_id`,`status` FROM `pay_hexiao` WHERE `status` = 3 AND `from_ad` = '{$ad_sn}'";
        $info = $this->db()->getAll($sql);
        foreach ($info as $k => $v) {
            $data[] = $v['hx_id'];
        }
        return $data;
    }

    /**
     * 根据订单来源 -> 获取 申请单ID(apply_id) 集合, 检测 过滤 未审核的申请单
     * @param string $ad_sn
     * @return array
     */
    public function checkPassAppID($ad_sn) {
        $data = array();
        $sql = "SELECT `apply_id` FROM `pay_apply` WHERE `status` in(5,6) AND `from_ad` = '{$ad_sn}'";
        $info = $this->db()->getAll($sql);
        foreach ($info as $k => $v) {
            $data[] = $v['apply_id'];
        }
        return $data;
    }

    /**
     * 计算统计表各个信息
     * @param int $pass_hx_id
     * @param int $pass_app_id
     * @param array $where
     * @return array
     */
    public function countTotal($pass_hx_id, $pass_app_id, $where) {
        //初始化各种数据
        $count_total = array(
            'xse' => 0,
            'cb' => 0,
            'fy' => 0,
            'sy' => 0,
            'xsje' => 0,
            'xsfy' => 0,
            'xssy' => 0,
            'xscb' => 0,
            'thje' => 0,
            'thfy' => 0,
            'thsy' => 0,
            'thcb' => 0,
        );
        foreach ($pass_hx_id as $hx_id) {  //操作核销单表 pay_hexiao
            $sql = 'SELECT `shijia`,`cash_type`,`chengben` FROM `pay_hexiao` WHERE `hx_id` = ' . $hx_id;
            if (isset($where['pay_time_s'])) {
                $sql .= ' AND `checktime` >= \'' . $where['pay_time_s'] . ' 00:00:00\'';
            }
            if (isset($where['pay_time_e'])) {
                $sql .= ' AND `checktime` <= \'' . $where['pay_time_e'] . ' 23:59:59\'';
            }
            //账期查询
            if (isset($where['pay_tiime_start'])) {
                $sql .= " AND `checktime` >= '" . $where['pay_tiime_start'] . " 00:00:00'";
            }
            if (isset($where['pay_tiime_end'])) {
                $sql .= " AND `checktime` <= '" . $where['pay_tiime_end'] . " 23:59:59'";
            }

            $data1 = $this->db()->getRow($sql);
            if (isset($data1['cash_type'])) {
                if ($data1['cash_type'] == 1) {
                    $count_total['xse'] += $data1['shijia'];  //销售收款
                    $count_total['xscb'] = $count_total['cb'] += $data1['chengben']; //销售成本
                }
                if ($data1['cash_type'] == 2) {
                    $count_total['xse'] -= $data1['shijia'];  //退货退款
                    $count_total['thcb'] = $count_total['cb'] -= $data1['chengben']; //退货成本
                }
            }
        }
        foreach ($pass_app_id as $app_id) {  //操作应收单表 pay_apply pay_apply_detail
            /* 计算 费用 、收益 */
            $sql1 = 'SELECT `pad`.`detail_id`,`pay`.`cash_type`,`pad`.`f_koudian`, `pad`.`f_koudian`, `pad`.`f_yongjin`, `pad`.`f_jingdong`, `pad`.`f_yunfei`, `pad`.`f_peifu`, `pad`.`f_chajia`, `pad`.`f_youhui`, `pad`.`f_weiyue`,`pad`.`sy_fanyou`,`pad`.`sy_qita`,`pad`.`external_total` FROM `pay_apply` AS `pay` , `pay_apply_detail` AS `pad` WHERE `pay`.`apply_id` = `pad`.`apply_id` AND `pay`.`apply_id` = ' . $app_id;
            if (isset($where['pay_time_s'])) {
                $sql1 .= ' AND `pay`.`check_time` >= \'' . $where['pay_time_s'] . ' 00:00:00\'';
            }
            if (isset($where['pay_time_e'])) {
                $sql1 .= ' AND `pay`.`check_time` <= \'' . $where['pay_time_e'] . ' 23:59:59\'';
            }
            //账期查询
            if (isset($where['pay_tiime_start'])) {
                $sql1 .= " AND `pay`.`check_time` >= '" . $where['pay_tiime_start'] . " 00:00:00'";
            }
            if (isset($where['pay_tiime_end'])) {
                $sql1 .= " AND `pay`.`check_time` <= '" . $where['pay_tiime_end'] . " 23:59:59'";
            }

            $data = $this->db()->getRow($sql1);
            //销售费用 、收益	销售金额 	销售费用 	销售收益 	销售成本 	销售利润 	退货金额 	退货费用 	退货收益 	退货成本
            if (isset($data['cash_type'])) {
                // echo '<pre>';print_r($data);echo '</pre>';
                //处理销售
                if ($data['cash_type'] == 1) {
                    $count_total['xsfy'] = $count_total['fy'] += ($data['f_koudian'] + $data['f_yongjin'] + $data['f_jingdong'] + $data['f_yunfei'] + $data['f_peifu'] + $data['f_chajia'] + $data['f_youhui'] + $data['f_weiyue']);     //销售费用
                    $count_total['xssy'] = $count_total['sy'] +=($data['sy_fanyou'] + $data['sy_qita']);  //销售收益
                    $count_total['xsje'] +=$data['external_total'];  //销售金额
                }
                //处理退货
                if ($data['cash_type'] == 2) {
                    $count_total['thfy'] = $count_total['fy'] -= ($data['f_koudian'] + $data['f_yongjin'] + $data['f_jingdong'] + $data['f_yunfei'] + $data['f_peifu'] + $data['f_chajia'] + $data['f_youhui'] + $data['f_weiyue']); //退货费用
                    $count_total['thsy'] = $count_total['sy'] -=($data['sy_fanyou'] + $data['sy_qita']);  //退货收益
                    $count_total['thje'] +=$data['external_total'];  //退货金额
                }
            }
        }
        return $count_total;
    }

}

?>