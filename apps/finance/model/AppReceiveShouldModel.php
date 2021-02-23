<?php

/**
 *  -------------------------------------------------
 *   @file		: AppReceiveShouldModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-02-02 09:41:38
 *   @update	:
 *  -------------------------------------------------
 */
class AppReceiveShouldModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'app_receive_should';
        $this->pk = 'should_id';
        $this->_prefix = '';
        $this->_dataObject = array("should_id" => "应收单ID",
            "should_number" => "应收单单号",
            "status" => "应收单状态：1、待审核，2、已审核，3、已取消",
            "total_status" => "收款状态：1、未付款，2、部分付款，3、已付款",
            "from_ad" => "订单来源",
            "total_cope" => "应收金额",
            "total_real" => "实收金额",
            "maketime" => "制单时间",
            "makename" => "制单人",
            "checktime" => "审核时间",
            "checkname" => "审核人");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url AppReceiveShouldController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        //不要用*,修改为具体字段
        $sql = 'SELECT `s`.`should_id`, `s`.`should_number`, `s`.`status`, `s`.`from_ad`,`s`.`total_status`, `s`.`total_cope`, `s`.`total_real`, `s`.`maketime`, `s`.`makename`, `s`.`checktime`, `s`.`checkname` FROM `'.$this->table().'` as `s` ';
		$sql .= 'WHERE  1 ';
		if(isset($where['should_number']) && $where['should_number'] != ''){
            $sql .= " and `s`.`should_number` = '{$where['should_number']}'";
        }
		if(isset($where['from_ad']) && $where['from_ad'] != ''){
            $sql .= " and `s`.`from_ad` = '{$where['from_ad']}'";
        }
		if(isset($where['status']) && $where['status'] != ''){
            $sql .= " and `s`.`status` = {$where['status']}";
        }
		if(isset($where['total_status']) && $where['total_status'] != ''){
            $sql .= " and `s`.`total_status` = {$where['total_status']}";
        }
		if(isset($where['make_time_start']) && $where['make_time_start'] != '' && isset($where['make_time_end']) && $where['make_time_end'] != ''){
            $sql .= " and `s`.`maketime` >= '{$where['make_time_start']}' and `s`.`maketime` <= '{$where['make_time_end']}'";
        }
		if(isset($where['check_time_start']) && $where['check_time_start'] != '' && isset($where['check_time_end']) && $where['check_time_end'] != ''){
            $sql .= " and `s`.`checktime` >= '{$where['check_time_start']}' and `s`.`checktime` <= '{$where['check_time_end']}'";
        }
        $sql .= " ORDER BY `s`.`should_id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }


    /**
	* 更新应收单信息
	* @param $total_real 制单人员提交的收款金额
	* @param $id 当前应收单ID
	*/
	public function updateShouldInfo($id,$submit_total){
		//修改实收金额
		$this->updateTotalReal($id,$submit_total);

		//修改收款状态
		$modle = new AppReceiveShouldModel($id,30);
        $total = $modle->getDataObject();
		if( ($total['total_cope'] - $total['total_real']) == 0){			//应收款 == 实收款 => 已付款
            $modle->setValue('total_status', 3);
		}else if($total['total_real'] == 0){						//未付款状态
            $modle->setValue('total_status', 1);
		}else{											//部分付款
            $modle->setValue('total_status', 2);
		}
        return $modle->save(true);
	}

    /**
	* 更新应收单 实收金额
	* @param $submit_total 制单人员提交的收款金额
	* @param $id 当前应收单ID
	*/
	public function updateTotalReal($id,$submit_total){
		$sql = 'UPDATE `'.$this->table().'` SET `total_real` = `total_real`+'.$submit_total.' WHERE `should_id` = '.$id;
		return $this->db()->query($sql);
	}

    /**
	* 根据应收单号-->获取指定单条数据信息
	* @param $should_number String 应收单号
	* @param $col String 要查询的字段
	*/
	public function getRowNumber($should_number,$col = "*"){
		$sql = 'SELECT '.$col.' FROM `'.$this->table().'` WHERE `should_number` = \''.$should_number.'\'';
		return $this->db()->getRow($sql);
	}

    function getBankName($param=0) {
        $data = array(
			'1'=>'(总公司)招商银行股份有限公司上海淮中支行 1219 0832 6010 801',
			'2'=>'(总公司)民生银行卢湾支行 2210 1417 0004 890',
			'3'=>'(总公司)建行北京东四支行 1100 1007 4000 5300 7798',
			'4'=>'(总公司)广发银行上海福州路支行 1651 3505 1700 04276',
			'5'=>'(总公司)工行北京海淀西区支行营业部 0200 0045 1920 1102 843',
			'6'=>'(分公司)交通银行深圳布吉支行 4430 6641 2018 0101 08262',
			'7'=>'(分公司)上海浦东发展银行北京中关村支行 9105 0154 8000 08147',
		);
        if($param > 0){
            return $data[$param];
        }
        return $data;
    }


	/**
	* 生成应收单
	* @param $dis String 应收申请单id
	*/
	public function addShould($ids)
	{
		//计算总金额
		$applyModel = new AppReceiveApplyModel(30);
		$total = $applyModel->getTotalOfIds($ids);
		//通过checkShouldCon方法已经确定所有单据都是同一个订单来源/结算商，只取ids_arr[0]的数据即可
		$ids_arr = explode(',',$ids);
		$applyarr = $applyModel->getRow($ids_arr[0],'from_ad,make_name,check_name');	//获取申请单的相关信息

		$data = array(
			'from_ad'=>$applyarr['from_ad'],
			'total_cope'=>$total,
			'makename'=>$_SESSION['userName'],
			'maketime'	=> date('Y-m-d H:i:s',time()),
			'checktime'	=> '0000-00-00 00:00:00',
			);
		$should_id = $this->saveData($data,array());
		$should_number = 'CWYS'.$should_id;	//合成应收单号
		$sql = 'UPDATE '.$this->table().' SET should_number = \''.$should_number.'\' WHERE should_id = '.$should_id;
		$this->db()->query($sql,array());

		//pay_should_detail表的数据
		$detailData = array();
		foreach($ids_arr as $k => $v)
		{
			$ar = $applyModel->getRow($v,'apply_number,status,total');
			$dataArr['should_id'] = $should_id;		//应收单对应ID
			$dataArr['apply_number'] = $ar['apply_number'];	//应收申请单单号
			$dataArr['total_cope'] = $ar['total'];	//应收金额

			$detailData[] = $dataArr;
			//修改申请单对应的应收单单号
			$applyModel->updateNoId(array('should_number'=> $should_number,'status'=>'6'),$v);
		}

		$detailModel = new AppReceiveShouldDetailModel(30);
		$resultarr['error'] = false;

		if($detailModel->insertAll($detailData))
		{
			$resultarr['error'] = true;
			$resultarr['id'] = $should_id;
		}
		return $resultarr;
	}

		/**
	* 更新指定字段
	* $col String 查询的指定字段
	* $where String 查询的条件
	*/
	public function updateCol( $col , $where ){
		$sql =' UPDATE '.$this->table().' SET '.$col.' WHERE '.$where;
		$rows = $this->db()->query($sql);
		return $rows->rowCount();
	}


	/**
	* 获取指定单条数据信息
	* @param $id int 主键
	* @param $col String 要查询的字段
	*/
	public function getRow($id,$col = "*"){
		$sql = 'SELECT '.$col.' FROM '.$this->table().' WHERE should_id = '.$id;
		return $this->db()->getRow($sql);
	}
}

?>