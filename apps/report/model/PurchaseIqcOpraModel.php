<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseIqcOpraModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-18 18:17:59
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseIqcOpraModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'purchase_iqc_opra';
        $this->_dataObject = array("id"=>"ID",
"rece_detail_id"=>"操作的序号",
"opra_code"=>"1=质检通过 2=报废 3=IQC未过",
"opra_uname"=>"操作人",
"opra_time"=>"操作时间",
"opra_info"=>"备注");
		parent::__construct($id,$strConn);
	}

	//取最后一条OQC未过备注
	public function getOne_iqc_w($rece_detail_id)
	{
		$sql = "SELECT * FROM ".$this->table()." WHERE rece_detail_id = ".$rece_detail_id." AND opra_code = 3 order by id desc limit 1";
		return $this->db()->getRow($sql);
	}

	public function getiqcList($id)
	{
		$sql = "SELECT * FROM ".$this->table()." WHERE rece_detail_id = ".$id;
		return $this->db()->getAll($sql);
	}
	/**
	 * 
	 */
	public function get_qc_list($where, $page, $pageSize = 10, $useCache = true) {
		$str='';
	    $str0='';
		if(isset($where['start_time']) && !empty($where['start_time'])){
			$str.=" and qc.opra_time>='{$where['start_time']}' ";
			$str0.=" and qm.opra_time>='{$where['start_time']}' ";
		}
		if(isset($where['end_time']) && !empty($where['end_time'])){
			$str.=" and qc.opra_time<='{$where['end_time']} 23:59:59' ";
			$str0.=" and qm.opra_time<='{$where['end_time']} 23:59:59' ";
		}
		if(isset($where['prc_ids']) and !empty($where['prc_ids']))
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			$str .= " AND r.prc_id in ({$where['prc_ids']})";
			$str0 .= " AND r.prc_id in ({$where['prc_ids']})";
		}
		$str1=$str2=$str;
		if(isset($where['opra_uname']) && !empty($where['opra_uname'])){
			$opra_uname='';
			foreach ($where['opra_uname'] as $val){
				$opra_uname.="'{$val}',";
			}
			$opra_uname=trim($opra_uname,',');
			$str1=$str." and r.user_name in ({$opra_uname}) ";
			$str2=$str." and r.opra_uname in ({$opra_uname}) ";
			$str0 .= " and r.opra_uname in ({$opra_uname}) ";
		}
		if(isset($where['style_sn']) and !empty($where['style_sn']))
		{
			$str1 .= " AND d.style_sn in ('{$where['style_sn']}')";
			$str2 .= " AND r.style_sn in ('{$where['style_sn']}')";
			$str0 .= " AND r.style_sn in ('{$where['style_sn']}')";
		}
        $sql_where = '';
        if(isset($where['qc_type']) && !empty($where['qc_type'])){
            $sql_where.= " AND qc_type = '".$where['qc_type']."' ";
        }
        if(isset($where['result']) && !empty($where['result'])){
            $sql_where.= " AND result = '".$where['result']."' ";
        }
	$sql="SELECT opra_time,LEFT(opra_time,10) opra_date, STATUS ,bc_sn,qc_type,style_sn,prc_id,prc_name,COUNT(*) count,SUM(STATUS=1) pass_num  FROM 
    
    (
	SELECT qc.opra_time,qc.opra_code STATUS ,d.`bc_sn`,1 AS qc_type,d.style_sn,r.prc_id,r.`prc_name`,r.user_name opra_uname,IF(qc.opra_code=1,1,2) result FROM purchase.purchase_iqc_opra qc  
	 INNER JOIN purchase.purchase_receipt_detail d ON d.id=qc.rece_detail_id 
	 INNER JOIN purchase.purchase_receipt r ON d.purchase_receipt_id=r.id 
	 WHERE d.`bc_sn`!='' {$str1}
    UNION  ALL 
	  SELECT qc.opra_time,qc.oqc_result STATUS,r.bc_sn ,2 AS qc_type,r.style_sn,r.prc_id,r.prc_name,r.opra_uname,IF(qc.oqc_result=1,1,2) result FROM kela_supplier.product_oqc_opra qc  
	  INNER JOIN kela_supplier.product_info r ON r.id=qc.bc_id
	  where 1  {$str2}
    
    UNION  ALL 
	  SELECT qm.opra_time,qm.oqc_result STATUS,r.bc_sn ,1 AS qc_type,r.style_sn,r.prc_id,r.prc_name,r.opra_uname,IF(qm.oqc_result=1,1,2) result FROM kela_supplier.product_shipment qm  
	  INNER JOIN kela_supplier.product_info r ON r.id=qm.bc_id
	  where 1  {$str0}
  ) qc_log 
  where 1 $sql_where
  GROUP BY LEFT(opra_time,10) order by opra_time desc";
        //echo $sql;
        //exit;
		//$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		$data['pageSize']=$pageSize;
		$countSql = $sql;
		$data['recordCount'] = $this->db()->getAll($countSql);
		$data['recordCount'] = count($data['recordCount']);
		$data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
		$data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
		$data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
		$data['isFirst'] = $data['page'] > 1 ? false : true;
		$data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
		$data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
		$data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
		$data['data'] = $this->db()->getAll($data['sql']);
		return $data;
	}


	/**
	 * 
	 */
	public function get_qc_list_second($where, $page, $pageSize = 10, $useCache = true) {
		$str='';
		$str0='';
		if(isset($where['start_time']) && !empty($where['start_time'])){
			$str.=" and qc.opra_time>='{$where['start_time']}' ";
			$str0.=" and qm.opra_time>='{$where['start_time']}' ";
		}
		if(isset($where['end_time']) && !empty($where['end_time'])){
			$str.=" and qc.opra_time<='{$where['end_time']} 23:59:59' ";
			$str0.=" and qm.opra_time<='{$where['end_time']} 23:59:59' ";
		}
		if(isset($where['prc_ids']) and !empty($where['prc_ids']))
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			$str .= " AND r.prc_id in ({$where['prc_ids']})";
			$str0 .= " AND r.prc_id in ({$where['prc_ids']})";
		}
		$str1=$str2=$str;
		if(isset($where['opra_uname']) && !empty($where['opra_uname'])){
			$opra_uname='';
			foreach ($where['opra_uname'] as $val){
				$opra_uname.="'{$val}',";
			}
			$opra_uname=trim($opra_uname,',');
			$str1=$str." and r.user_name in ({$opra_uname}) ";
			$str2=$str." and r.opra_uname in ({$opra_uname}) ";
			$str0 .= " and r.opra_uname in ({$opra_uname}) ";
		}
		if(isset($where['style_sn']) and !empty($where['style_sn']))
		{
			$str1 .= " AND d.style_sn in ('{$where['style_sn']}')";
			$str2 .= " AND r.style_sn in ('{$where['style_sn']}')";
			$str0 .= " AND r.style_sn in ('{$where['style_sn']}')";
		}
        $sql_where = '';
        if(isset($where['qc_type']) && !empty($where['qc_type'])){
            $sql_where.= " AND qc_type = '".$where['qc_type']."' ";
        }
        if(isset($where['result']) && !empty($where['result'])){
            $sql_where.= " AND result = '".$where['result']."' ";
        }
	$sql="SELECT opra_time,LEFT(opra_time,10) opra_date, reason ,bc_sn,IF(qc_type=1,'IQC','OQC') qc_type,style_sn,prc_id,prc_name,opra_uname,result,content  FROM 
    
    (
	SELECT qc.opra_time,'' reason,d.`bc_sn`,1 AS qc_type,d.style_sn,r.prc_id,r.`prc_name`,r.user_name opra_uname,IF(qc.opra_code=1,1,2) result,
      opra_info content
    FROM purchase.purchase_iqc_opra qc  
	 INNER JOIN purchase.purchase_receipt_detail d ON d.id=qc.rece_detail_id 
	 INNER JOIN purchase.purchase_receipt r ON d.purchase_receipt_id=r.id 
	 WHERE d.`bc_sn`!='' {$str1}
    UNION  ALL 
	  SELECT qc.opra_time,c.cat_name as reason,r.bc_sn ,2 AS qc_type,r.style_sn,r.prc_id,r.prc_name,qc.opra_uname,
      IF(qc.oqc_result=1,1,2) result,
      qc.oqc_info content
      FROM kela_supplier.product_oqc_opra qc  
	  INNER JOIN kela_supplier.product_info r ON r.id=qc.bc_id
	  LEFT JOIN kela_supplier.product_fqc_conf c ON qc.oqc_reason=c.id
	  where 1  {$str2} 
	    
    UNION  ALL 
	  SELECT qm.opra_time,concat((select cat_name from kela_supplier.product_fqc_conf where id=c.parent_id) ,' - ', c.cat_name ) as reason,r.bc_sn ,1 AS qc_type,r.style_sn,r.prc_id,r.prc_name,qm.opra_uname,
      IF(qm.oqc_result=1,1,2) result,
      qm.info content
      FROM kela_supplier.product_shipment qm  
	  INNER JOIN kela_supplier.product_info r ON r.id=qm.bc_id
	  LEFT JOIN kela_supplier.product_fqc_conf c ON qm.oqc_no_reason=c.id
	  where 1  {$str0}
  ) qc_log 
  where 1 $sql_where  order by opra_time desc ";
	    //echo $sql;
		$data['pageSize']=$pageSize;
		$countSql = preg_replace('/^(SELECT.+?\bFROM\b)/i', 'SELECT COUNT(*) count FROM', $sql, 1);
		$data['recordCount'] = $this->db()->getOne($countSql);
		$data['pageCount'] = ceil($data['recordCount'] / $data['pageSize']);
		$data['page'] = $data['pageCount'] == 0 ? 0 : ((int) $page < 1 ? 1 : (int) $page);
		$data['page'] = $data['page'] > $data['pageCount'] ? $data['pageCount'] : $data['page'];
		$data['isFirst'] = $data['page'] > 1 ? false : true;
		$data['isLast'] = $data['page'] < $data['pageCount'] ? false : true;
		$data['start'] = ($data['page'] == 0) ? 1 : ($data['page'] - 1) * $data['pageSize'] + 1;
		$data['sql'] = $sql . ' LIMIT ' . ($data['start'] - 1) . ',' . $data['pageSize'];
		$data['data'] = $this->db()->getAll($data['sql']);
		return $data;
	}


	/**
	 * 
	 */
	public function get_qc_list_second_details($where) {
	$str='';
		$str0='';
		if(isset($where['start_time']) && !empty($where['start_time'])){
			$str.=" and qc.opra_time>='{$where['start_time']}' ";
			$str0.=" and qm.opra_time>='{$where['start_time']}' ";
		}
		if(isset($where['end_time']) && !empty($where['end_time'])){
			$str.=" and qc.opra_time<='{$where['end_time']} 23:59:59' ";
			$str0.=" and qm.opra_time<='{$where['end_time']} 23:59:59' ";
		}
		if(isset($where['prc_ids']) and !empty($where['prc_ids']))
		{
			$where['prc_ids']=implode(',',$where['prc_ids']);
			$str .= " AND r.prc_id in ({$where['prc_ids']})";
			$str0 .= " AND r.prc_id in ({$where['prc_ids']})";
		}
		$str1=$str2=$str;
		if(isset($where['opra_uname']) && !empty($where['opra_uname'])){
			$opra_uname='';
			foreach ($where['opra_uname'] as $val){
				$opra_uname.="'{$val}',";
			}
			$opra_uname=trim($opra_uname,',');
			$str1=$str." and r.user_name in ({$opra_uname}) ";
			$str2=$str." and r.opra_uname in ({$opra_uname}) ";
			$str0 .= " and r.opra_uname in ({$opra_uname}) ";
		}
        $sql_where = '';
        if(isset($where['qc_type']) && !empty($where['qc_type'])){
            $sql_where.= " AND qc_type = '".$where['qc_type']."' ";
        }
        if(isset($where['result']) && !empty($where['result'])){
            $sql_where.= " AND result = '".$where['result']."' ";
        }
	$sql="SELECT opra_time,LEFT(opra_time,10) opra_date ,bc_sn,IF(qc_type=1,'IQC','OQC') qc_type,style_sn,prc_id,prc_name,opra_uname,result, reason ,content  FROM 
    
    (
	SELECT qc.opra_time,'' reason,d.`bc_sn`,1 AS qc_type,d.style_sn,r.prc_id,r.`prc_name`,r.user_name opra_uname,IF(qc.opra_code=1,1,2) result,
      opra_info content
    FROM purchase.purchase_iqc_opra qc  
	 INNER JOIN purchase.purchase_receipt_detail d ON d.id=qc.rece_detail_id 
	 INNER JOIN purchase.purchase_receipt r ON d.purchase_receipt_id=r.id 
	 WHERE d.`bc_sn`!='' {$str1}
    UNION  ALL 
	  SELECT qc.opra_time, c.cat_name as reason,r.bc_sn ,2 AS qc_type,r.style_sn,r.prc_id,r.prc_name,qc.opra_uname,
      IF(qc.oqc_result=1,1,2) result,
      qc.oqc_info content
      FROM kela_supplier.product_oqc_opra qc  
	  INNER JOIN kela_supplier.product_info r ON r.id=qc.bc_id
	  LEFT JOIN kela_supplier.product_fqc_conf c ON qc.oqc_reason=c.id
	  where 1  {$str2}
    
     UNION  ALL 
	  SELECT qm.opra_time,concat((select cat_name from kela_supplier.product_fqc_conf where id=c.parent_id) ,' - ', c.cat_name ) as reason,r.bc_sn ,1 AS qc_type,r.style_sn,r.prc_id,r.prc_name,qm.opra_uname,
      IF(qm.oqc_result=1,1,2) result,
      qm.info content
      FROM kela_supplier.product_shipment qm  
	  INNER JOIN kela_supplier.product_info r ON r.id=qm.bc_id
	  LEFT JOIN kela_supplier.product_fqc_conf c ON qm.oqc_no_reason=c.id
	  where 1  {$str0}
  ) qc_log 
  where 1 $sql_where  order by opra_time desc ";
	
		$data = $this->db()->getAll($sql);
		return $data;
	}
}

