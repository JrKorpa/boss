<?php

/**
 *  -------------------------------------------------
 *   @file		: EcsAdModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-27 10:37:56
 *   @update	:
 *  -------------------------------------------------
 */
class EcsAdModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'ecs_ad';
        $this->pk = 'ad_id';
        $this->_prefix = '';
        $this->_dataObject = array("ad_id" => " ",
            "ad_sn" => " ",
            "ad_department" => " ",
            "ad_channel" => " ",
            "area" => " ",
            "media_name" => " ",
            "media_passwd" => " ",
            "position_id" => " ",
            "media_type" => " ",
            "ad_domain" => " ",
            "ad_name" => " ",
            "ad_link" => " ",
            "ad_code" => " ",
            "start_date" => " ",
            "end_date" => " ",
            "link_man" => " ",
            "link_email" => " ",
            "link_phone" => " ",
            "click_count" => " ",
            "enabled" => " ",
            "fenlei" => "所属分类 0:全部、-1:其他、1:异业联盟、2:社区、3:BDD相关、4:团购、5:老顾客、6:数据、7:网络来源",
            "reset" => " ",
            "add_time" => "渠道添加时间",
            "last_update_time" => "渠道启用使用最后更新时间（销售系统使用）",
            "ad_cat" => " ");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url EcsAdController/search
     */
    function pageList($where) {
        //不要用*,修改为具体字段
        $_model = new PayOrderInfoModel(29);
        $adList = $_model->getAdList();
        $from_ads = implode("','", $adList);
        $sql = "SELECT `ad_sn`,`ad_name` FROM `" . $this->table() . "` WHERE `ad_sn` IN ('$from_ads')";

        if (isset($where['company']) && $where['company'] != '') {
            $sql .= ' AND ad_department = ' . $where['company'];
        }
        if (!empty($where['from_ad'])) {
            $sql .= ' AND ad_sn = \'' . $where['from_ad'] . '\' ';
        }

        $data = $this->db()->getAll($sql);
        foreach ($data as $key => $value) {
            $data[$key]['ystotal'] = 0;
            $arr_ys_total = $this->sumCol($value['ad_sn'], $where);
            $data[$key]['ystotal'] = !empty($arr_ys_total['ystotal']) ? $arr_ys_total['ystotal'] : 0; //应收款
            $data[$key]['sytotal'] = !empty($arr_ys_total['sytotal']) ? $arr_ys_total['sytotal'] : 0; //实收款
            $data[$key]['dstotal'] = $data[$key]['ystotal'] - $data[$key]['sytotal']; //待收款
        }
        return $data;
    }

    /**
     * 根据订单来源计算应收总金额 实收总金额
     * @param type $ad_sn
     * @param type $where
     * @return type
     */
    public function sumCol($ad_sn, $where) {
        $sql = 'SELECT sum(total_cope) AS ystotal ,sum(total_real) AS sytotal FROM `app_receive_should` ';
        $sql .= ' WHERE status = 2 AND from_ad = \'' . $ad_sn . '\'';
        if (isset($where['pay_time_s'])) {
            $sql .= ' AND checktime >= \'' . $where['pay_time_s'] . ' 00:00:00\'';
        }
        if (isset($where['pay_time_e'])) {
            $sql .= ' AND checktime <= \'' . $where['pay_time_e'] . ' 23:59:59\'';
        }
        //账期查询
        if (isset($where['pay_tiime_start'])) {
            $sql .= " AND checktime >= '" . $where['pay_tiime_start'] . " 00:00:00'";
        }
        if (isset($where['pay_tiime_end'])) {
            $sql .= " AND checktime <= '" . $where['pay_tiime_end'] . " 23:59:59'";
        }
        return $this->db()->getRow($sql);
    }

    public function getAdList($id = 0,$ad_sn='') {
        $model = new PayJxcOrderModel(29);
        $adList = $model->getAdList();
        $sql = "select `ad_sn`,`ad_name`,`ad_department` from " . $this->table() . " where `enabled`=1  and `ad_sn` in('" . implode("','", $adList) . "')";
        if ($id > 0) {
            $sql.=" AND `ad_department`=" . $id;
        }
        if($ad_sn != ''){
            $sql .= " and `ad_sn` = '{$ad_sn}'";
        }
        return $this->db()->getAll($sql);
    }
    
	public function getnum($sql){
		$data = $this->db()->getAll($sql);
		return $data;
	}

    public function getCompanyList() {
        $data = array(
            '0' => '官方网站部',
            '3' => '银行销售部',
            '10' => 'B2C销售部',
            '81' => '京东销售部',
        );
        return $data;
    }

    public function getJiezhangList() {
        $sql = "select `year` from `app_jiezhang` group by `year` order by `year` desc ";
        return $this->db()->getAll($sql);
    }

    public function getJiezhangInfoList($data) {
        $sql = "select `qihao` from `app_jiezhang` where `start_time`!='0000-00-00' and `end_time`!='0000-00-00' and `year`='" . $data . "' order by `id` asc";
        return $this->db()->getAll($sql);
    }

    public function getJiezhangtimes($where = array()) {
        $sql = "select `start_time` from `app_jiezhang`";
        $str = "";
        if (!empty($where['start_year'])) {
            $str .= "`year` = '{$where['start_year']}' AND ";
        }
        if (!empty($where['start_qihao'])) {
            $str .= "`qihao` = '{$where['start_qihao']}' AND ";
        }
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        return $this->db()->getOne($sql);
    }

    public function getJiezhangtimee($where = array()) {
        $sql = "select `end_time` from `app_jiezhang`";
        $str = "";
        if (!empty($where['end_year'])) {
            $str .= "`year` = '{$where['end_year']}' AND ";
        }
        if (!empty($where['end_qihao'])) {
            $str .= "`qihao` = '{$where['end_qihao']}' AND ";
        }
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        return $this->db()->getOne($sql);
    }

}

?>