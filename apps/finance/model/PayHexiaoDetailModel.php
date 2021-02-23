<?php

/**
 *  -------------------------------------------------
 *   @file		: PayHexiaoDetailModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-28 14:50:44
 *   @update	:
 *  -------------------------------------------------
 */
class PayHexiaoDetailModel extends Model {

    function __construct($id = NULL, $strConn = "") {
        $this->_objName = 'pay_hexiao_detail';
        $this->pk = 'detail_id';
        $this->_prefix = '';
        $this->_dataObject = array("detail_id" => " ",
            "hx_id" => "核销单ID",
            "jxc_order" => " ",
            "type" => "单据类型：S、销售单，B、销售退货单",
            "goods_num" => "货品总数",
            "chengben" => "成本价",
            "shijia" => "销售价",
            "overrule_reason" => "驳回原因 ");
        parent::__construct($id, $strConn);
    }

    /**
     * 	pageList，分页列表
     *
     * 	@url PayHexiaoDetailController/search
     */
    function pageList($where, $page, $pageSize = 10, $useCache = true) {
        //不要用*,修改为具体字段
        $sql = "SELECT * FROM `" . $this->table() . "`";
        $str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
        if ($str) {
            $str = rtrim($str, "AND "); //这个空格很重要
            $sql .=" WHERE " . $str;
        }
        $sql .= " ORDER BY `detail_id` DESC";
        $data = $this->db()->getPageList($sql, array(), $page, $pageSize, $useCache);
        return $data;
    }

    /**
     * 保存核销单详细订单的信息
     * @param type $arr
     * @param type $hx_id
     * @return type
     */
    function save_vd($arr,$hx_id)
	{
		//核销单详细入库
		/** 去掉status字段    **/
		foreach ($arr as $value)
		{
			array_shift($value);
			array_shift($value);
			array_shift($value);
			$value['hx_id'] = $hx_id;
			$new_data[] = $value;
		}
		return $this->insertAll($new_data);
	}

    /**
     * 通过核销单号获取所有详细订单
     * @param type $hx_id
     * @return type
     */
    function getDataOfhx_Id($hx_id)
	{
		$sql = "select `detail_id`, `hx_id`, `jxc_order`, `type`, `goods_num`, `chengben`, `shijia`, `overrule_reason` from `".$this->table();
		$sql .= "` where `hx_id` = $hx_id order by `detail_id` asc";
		return $this->db()->getAll($sql);
	}

    /**
     * 通过订单号删除
     * @param type $hx_id
     * @return type
     */
    public function deleteOfHxId($hx_id)
	{
		$sql = "delete from `".$this->table();
		$sql .= "` where `hx_id` = ".$hx_id;
		return $this->db()->query($sql,array());
	}

    /**
     * 批量修改核销单详细列表
     * @param type $valueArr
     * @param type $whereArr
     * @return type
     */
    public function updateByrea($valueArr,$whereArr)
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