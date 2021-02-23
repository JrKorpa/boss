<?php
/**
 *  -------------------------------------------------
 *   @file		: OfflineChannelGoldReportModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2017-08-23 16:21:03
 *   @update	:
 *  -------------------------------------------------
 */
class OfflineChannelGoldReportModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'base_order_info';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url OfflineChannelGoldReportController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT * FROM `".$this->table()."`";
		$str = '';
//		if($where['xxx'] != "")
//		{
//			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
//		}
//		if(!empty($where['xx']))
//		{
//			$str .= "`xx`='".$where['xx']."' AND ";
//		}
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY `id` DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}

    public function getRealVolOrder($where=array())
    {
        $sql=" select `oi`.`department_id`,`oi`.`id`,b.goods_sn,`b`.`id` as detailsid,`b`.`favorable_price`,
        `b`.`favorable_status`,`b`.`goods_price`,`b`.`goods_type`,`b`.`goods_count` ,
        `b`.`is_return`,`b`.`cart`,`b`.`xiangqian`,g.product_type1,`p`.`product_type_name`,
        if(LENGTH(b.cert) >0,b.cert,g.zhengshuleibie) as cert,`b`.`zhengshuhao`,oi.mobile,
        oi.pay_date,oi.`is_zp` from 
                app_order.base_order_info as oi
                inner join app_order_details b on b.order_id = oi.id
                left join `warehouse_shipping`.`warehouse_goods` as g on `b`.`goods_id`=`g`.`goods_id`
                left join front.base_style_info bi on bi.style_sn = b.goods_sn 
                left join front.`app_product_type` `p` on `p`.`product_type_id` = `bi`.`product_type` where ";
        $sql.=" oi.order_status=2 and oi.order_pay_status in (2,3,4) and oi.is_delete = 0 ";
        if( isset($where['department_id']) && $where['department_id'] > 0 )
        {
            $sql .= " and oi.department_id in(".$where['department_id'].")";
        }
        if(isset($where['orderenter']) && !empty($where['orderenter']))
        {
            if($where['orderenter'] == '婚博会')
            {
                $sql .= " and oi.referer ='婚博会' ";
            }else{
                $sql .= " and oi.referer <> '婚博会' ";
            }
        }
        if(isset($where['begintime']) && !empty($where['begintime']))
        {
            $sql .= " and oi.pay_date >= '".$where['begintime']." 00:00:00'";   
        }
        if(isset($where['endtime']) && !empty($where['endtime']))
        {
            $sql .= " and oi.pay_date <= '".$where['endtime']." 23:59:59'"; 
        }
        //echo $sql;
        return $this->db()->getAll($sql);
    }
}

?>