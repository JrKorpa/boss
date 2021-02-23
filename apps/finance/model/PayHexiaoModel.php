<?php

/**
 *  -------------------------------------------------
 *   @file		: PayHexiaoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-26 16:39:43
 *   @update	:
 *  -------------------------------------------------
 */
class PayHexiaoModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'pay_hexiao';
        $this->pk = 'id';
        $this->_prefix = '';
        $this->_dataObject = array("id" => "核销ID",
            "check_sale_number" => "核销单单号",
            "status" => "1、新增，2、待审核，3、已审核，4、已驳回，5、已取消",
            "from_ad" => "订单来源",
            "order_num" => "单据数量",
            "goods_num" => "货品总数",
            "chengben" => "成本价",
            "shijia" => "销售价",
            "maketime" => "制单时间",
            "makename" => "制单人",
            "checktime" => "审核时间",
            "checkname" => "审核人",
            "apply_number" => "应收申请单单号",
            "cash_type" => "收款类型：1、销售收款，2、退货退款");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url PayHexiaoController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        //不要用*,修改为具体字段
        $sql = "SELECT ph.`check_sale_number`,ph.`id`,ph.`apply_number`,ph.`cash_type`,ph.`goods_num`,ph.`chengben`,ph.`shijia`,ph.`makename`,ph.`maketime`,ph.`checkname`,ph.`checktime`,ph.`status`,ph.`from_ad` FROM pay_hexiao AS ph LEFT JOIN `app_receive_apply` AS pay ON ph.apply_number = pay.apply_number ";
        $str = '';
        if(!empty($where['check_sale_number']))
		{
			$str .= " ph.check_sale_number = '{$where['check_sale_number']}' AND ";
		}
		if(!empty($where['apply_number']))
		{
			$str .= "ph.apply_number = '{$where['apply_number']}' AND ";
		}
		if(!empty($where['from_ad']))
		{
			$str .= "ph.from_ad = {$where['from_ad']} AND ";
		}
		if(!empty($where['cash_type']))
		{
			$str .= "ph.cash_type = {$where['cash_type']} AND ";
		}
		if(!empty($where['status']))
		{
			$str .= "ph.status = {$where['status']} AND ";
		}
		if(!empty($where['maketime_start']))
		{
			$str .= "ph.maketime >= '".$where['maketime_start']." 00:00:00' AND ";
		}
		if(!empty($where['maketime_end']))
		{
			$str .= "ph.maketime <= '".$where['maketime_end']." 23:59:59' AND ";
		}
		if(!empty($where['checktime_start']))
		{
			$str .= "ph.checktime >= '".$where['checktime_start']." 00:00:00' AND ";
		}
		if(!empty($where['checktime_end']))
		{
			$str .= "ph.checktime <= '".$where['checktime_end']." 23:59:59' AND ";
		}
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $sql .= " ORDER BY ph.`id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    /**
     * 根据上传的单号查询该单号信息
     * @param type $jxc_order
     * @param type $id
     * @param type $from_ad
     * @return string|int
     */
    function get_jxc_order_info($jxc_order,$id,$from_ad)
	{
		/* 判断是否该订单已经生成核销单  */
		if ($id == '')
		{
			$id = '';
		}
		else
		{
			$id = "HX".$id;
		}

        $sql = "select `o`.`from_ad`,`j`.`status`,`j`.`hexiao_number`,`j`.`jxc_order`,`j`.`type`,`j`.`goods_num`,`j`.`chengben`,`j`.`shijia` from `pay_jxc_order` as `j`,`pay_order_info` as `o`  where `j`.`kela_sn`=`o`.`kela_sn` and `j`.`jxc_order` = '{$jxc_order}' ";  //出驳回之外的其他状态
		//echo $sql;exit;
		$res = $this->db()->getRow($sql);
		//print_r($res);exit;
		//判断是否是驳回之后的核销单（驳回之后销售单状态时已驳回）
		if ($res)  //核销单存在  1:能上传 no :重复
		{
			//判断来源是否正确
			if ($res['from_ad'] != $from_ad)
			{
				$res['status']='from_no';
			}
			if ($res['status'] == 1 && $res['hexiao_number'] == '')
			{
				return $res; //待核销 核销单号为空
			}
			else if ($res['status']  ==  1 && $res['hexiao_number'] != $id )   //核销状态 核销单号不等于本身
			{
				$res['status'] = 'no';  //重复核销单
			}
			else if ($res['status']  ==  4 && $res['hexiao_number'] == $id)  // 已驳回状态  能修改本身的核销单
			{
				$res['status'] = 1;  //已经驳回的核销单
			}
		}
		else //核销单错误
		{
			return $res;
		}
		return $res;
	}

    /**
     *
     * @param type $checkdata
     * @param type $data
     * @return boolean
     */
    public function saveDatas($checkdata,$data)
	{
		$pay_hexiao_detail_model = new PayHexiaoDetailModel(29);
		if(empty($checkdata['id']))//添加数据
		{
			$id= $this->saveData($checkdata,array()); //1、保存核销单数据
            $sql = "update `".$this->table() ."` set `check_sale_number` = 'HX".$id."' where `id` = {$id}";
			$this->db()->query($sql,array());//修改核销单
			$pay_hexiao_detail_model->save_vd($data,$id);//2、 保存核销单详细订单记录  在pay_heixao_detail
			/*3、将销售订单改为审核状态*/
			$jxc_order   = $pay_hexiao_detail_model->getDataOfhx_Id($id);
			$payjxcorder_model  = new PayJxcOrderModel(29);
			$payjxcorder_model->update_status(array('hexiao_number'=>'HX'.$id,'status'=>2),$jxc_order);
			return array('result'=>'1','id'=>$id);  //返回核销id
		}
		else  //修改数据
		{
			$payjxcorder_model = new PayJxcOrderModel(29);
			// 1、修改核销单单据信息
			$id = $checkdata['id'];
			$arr = $this->saveData($checkdata,$this->getDataObject());//修改单据内容
			// 2、修改核销单详细单据信息 （删除原有,恢复状态，清空单据信息  增加现在）
			$jxc_order  = $pay_hexiao_detail_model->getDataOfhx_Id($id);
			if(count($jxc_order))
			{
				$payjxcorder_model->update_status(array('status'=>1,'hexiao_number'=>''),$jxc_order);
			}
			$pay_hexiao_detail_model->deleteOfHxId($checkdata['id']);
			$pay_hexiao_detail_model->save_vd($data,$checkdata['id']);
			//修改销售单 所有状态为待核销状态
			$jxc_order  = $pay_hexiao_detail_model->getDataOfhx_Id($id);
			$payjxcorder_model->update_status(array('status'=>2,'hexiao_number'=>'HX'.$id),$jxc_order);
			return true;
		}

	}

    /**
     * 修改单据状态
     * @param type $checkdata
     */
    function update_hx($checkdata)
	{
		$arr = $this->saveData($checkdata,$this->getDataObject());//修改单据内容
	}


    /*查询*/
	function select($whereArr)
	{
		$sql = "select `id`, `check_sale_number`, `status`, `from_ad`, `order_num`, `goods_num`, `chengben`, `shijia`, `maketime`, `makename`, `checktime`, `checkname`, `apply_number`, `cash_type` from `".$this->table()."`";
		$str = "";
        if ($whereArr)
		{
			foreach($whereArr as $k => $v)
			{
				$str .= "$k = '$v' AND ";
			}
		}
        if($str != ''){
            $sql .= " WHERE ".rtrim($str," AND ");
        }
		//echo $sql;
		return $this->db()->getAll($sql);
	}

    /**
     * 更新
     * @param type $valueArr
     * @param type $whereArr
     * @return type
     */
    public function update($valueArr,$whereArr)
	{
		$field = '';
		$where = ' 1';
		foreach($valueArr as $k => $v)
		{
			$field .= "$k = '$v',";
		}
		foreach($whereArr as $k => $v)
		{
			$where .= " AND $k = '$v'";
		}
		$field = substr($field,0,-1);
		$sql = "UPDATE `".$this->table()."` SET ".$field;
        $sql .= " WHERE ".$where;
		return $this->db()->query($sql,array());
	}

}

?>