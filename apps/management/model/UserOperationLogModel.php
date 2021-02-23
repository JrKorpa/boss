<?php
/**
 *  -------------------------------------------------
 *   @file		: RolePermissionModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2014-12-28 12:12:07
 *   @update	:
 *  -------------------------------------------------
 */
class UserOperationLogModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'user_operation_log';
        $this->_dataObject = array('id'=>'主键Id',
            'title'=>'日志标题',
            'module'=>'模块名称(mod值)',
            'controller'=>'控制器(con值)',
            'action'=>'操作方法(act值)',
            'request_url'=>'请求地址',
            'data'=>'备份数据',
            'remark'=>'日志内容',
            'create_user'=>'操作人',
            'ip'=>'ip地址',
            'create_time'=>'记录时间'            
        );
		parent::__construct($id,$strConn);
	}	
	/**
	 * 日志分页列表
	 * @param array $where
	 * @param unknown $page
	 * @param number $pageSize
	 * @param string $useCache
	 * @return unknown
	 */
	function pageList(array $where,$page,$pageSize=10,$useCache=true){
	    
	    $where_str = '1=1';
	    if(!empty($where['module']))
	    {
	        $where_str .= " AND l.module='{$where['module']}'";
	    }
	    if(!empty($where['controller']))
	    {
	        $where_str .= " AND (l.controller='{$where['controller']}' or t.c_name like '%{$where['controller']}%')";
	    }
	    if(!empty($where['action']))
	    {
	        $where_str .= " AND l.action='{$where['action']}'";
	    }
	    if(!empty($where['remark']))
	    {
	        $where_str .= " AND l.remark like '%{$where['remark']}%'";
	    }
	    if(!empty($where['start_date']))
	    {
	        $start_date = $where['start_date'];
	        $where_str .= " AND l.create_time>'{$start_date}'";
	    }	    
	    if(!empty($where['end_date']))
	    {
	        $end_date =$where['end_date']." 23:59:59";
	        $where_str .= " AND l.create_time<='{$end_date}'";
	    }
	    if(!empty($where['create_user']))
	    {
	        $where_str .= " AND l.create_user='{$where['create_user']}'";
	    }
	    $field = "l.*,t.a_name,t.c_name,t.m_name";	    
	    $joinTable ="SELECT m.label as m_name,c.label as c_name,o.label as a_name,m.`code` as m_code,c.`code` as c_code,o.method_name as a_code
FROM operation o LEFT JOIN control c on o.c_id=c.id LEFT JOIN application m on c.application_id=m.id";
	    $joinOn = "l.module=t.m_code AND l.controller=t.c_code AND (l.action=t.a_code or t.a_code ='')";
	    $orderby="l.create_time desc";
	    $sql = "SELECT {$field} from ".$this->table()." l left join ($joinTable) t on {$joinOn} where {$where_str} order by {$orderby}";
	    if(!$pageSize) {
	        $pageSize = 10;
	    }
	    //echo $sql;	    
	    $data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
	    return $data;
	}
     
}

?>