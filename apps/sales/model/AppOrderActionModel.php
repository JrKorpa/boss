<?php
/**
 *  -------------------------------------------------
 *   @file		: AppOrderActionModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-31 12:17:57
 *   @update	:
 *  -------------------------------------------------
 */
class AppOrderActionModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_order_action';
		$this->pk='action_id';
		$this->_prefix='';
        $this->_dataObject = array("action_id"=>"自增ID",
"order_id"=>"订单id",
"order_status"=>"订单审核状态1无效2已审核3取消4关闭",
"shipping_status"=>"发货状态",
"pay_status"=>"支付状态:1未付款2部分付款3已付款",
"create_user"=>"操作人",
"create_time"=>"操作时间",
"remark"=>"操作备注");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppOrderActionController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$haveold = 0;
		$sql= " ";
        $data = [];
		if(!empty($where['_id']))
		{
			/*
			if($where['_id']<1935211)
			{
				$haveold = 1 ;
				$sql = "select 'order_sn' as type,action_id as id,remark,create_user,order_status,pay_status,shipping_status,create_time from app_order_action_jxc where order_id='".$where['_id']."' ORDER BY action_id DESC";
				
			}else{
			*/	
            if(isset($where['hidden']) && $where['hidden'] == '1' && SYS_SCOPE == 'zhanting'){
                if($where['referer'] == '智慧门店'){
                    $limit = 0;
                }else{
                    $limit = 1;
                }
                $sql = "select 'order_sn' as type,action_id as id,remark,create_user,order_status,pay_status,shipping_status,create_time from app_order_action where order_id='".$where['_id']."' limit {$limit}";
                $data['data'] = $this->db()->getAll($sql);
            }else{
                $sql="select * from (
select 'order_sn' as type,action_id as id,remark,create_user,order_status,pay_status,shipping_status,create_time from app_order_action_jxc where order_id='".$where['_id']."' 
union all                   
select 'order_sn' as type,action_id as id,remark,create_user,order_status,pay_status,shipping_status,create_time from app_order_action where order_id='".$where['_id']."' 
union all
select 'bc_sn' as type,l.id,concat(p.bc_sn,' ',l.remark) as remark,'' as create_user,'' as order_status,'' as pay_status,'' as shipping_status,l.time as create_time from kela_supplier.product_opra_log l,kela_supplier.product_goods_rel r,app_order.app_order_details d,kela_supplier.product_info p  where l.bc_id=r.bc_id and r.goods_id=d.id and l.bc_id=p.id and d.order_id='".$where['_id']."' 
and l.remark not like '%</font>%') as tb order by create_time desc,id desc"; 
                $data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
            }
            
			//}
		}
		return $data;
	}

    /**
     *  pageList_by_zhmd，智慧门店获取生产日志
     *
     */
    function pageList_by_zhmd ($where,$page,$pageSize=10,$useCache=true)
    {
        $order_sn = $where['order_sn'];
        $sql = "select id from app_order.base_order_info where order_sn = '".$order_sn."'";
        $orderinfo = $this->db()->getRow($sql);
        $order_id = isset($orderinfo['id'])&& !empty($orderinfo['id'])?$orderinfo['id']:'';

        $data = array();
        $haveold = 0;
        $sql= " ";
        if(!empty($order_id))
        {
            /*
            if($where['_id']<1935211)
            {
                $haveold = 1 ;
                $sql = "select 'order_sn' as type,action_id as id,remark,create_user,order_status,pay_status,shipping_status,create_time from app_order_action_jxc where order_id='".$order_id."' ORDER BY action_id DESC";
                
            }else{
            */  
                $sql="select * from (
select 'order_sn' as type,action_id as id,remark,create_user,order_status,pay_status,shipping_status,create_time from app_order_action_jxc where order_id='".$order_id."' 
union all 
select 'bc_sn' as type,l.id,concat(p.bc_sn,' ',l.remark) as remark,'' as create_user,'' as order_status,'' as pay_status,'' as shipping_status,l.time as create_time from kela_supplier.product_opra_log l,kela_supplier.product_goods_rel r,app_order.app_order_details d,kela_supplier.product_info p  where l.bc_id=r.bc_id and r.goods_id=d.id and l.bc_id=p.id and d.order_id='".$order_id."' 
 and l.remark not like '%</font>%') as tb order by create_time desc,id desc"; 
       //file_put_contents('diamond.log', $sql);
            //}
            //$data = $this->db()->getAll($sql);
            $data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
        }
        
        //$data = $this->db()->getPageListNew($sql,array(),$page, $pageSize,$useCache);
        return $data;
    }
	
	//如果是老系统的 判断标准是id<1935211
	function pageOldList($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `app_order_action_jxc` where 1 ";
		if(!empty($where['_id']))
		{
			$sql .=" AND order_id = '".$where['_id']."' " ;
			$sql .= " ORDER BY `action_id` DESC";
		}
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
		return $data;
	}
		

	/**
	 *	getAll，取所有
	 *
	 *	@url AppOrderActionController/getAll
	 */
	function getAll ($where)
	{
        if($where['hidden'] == '1' && SYS_SCOPE == 'zhanting'){
            $sql = "select 'order_sn' as type,action_id as id,remark,create_user,order_status,pay_status,shipping_status,create_time from app_order_action where order_id='".$where['_id']."' limit 0";
            $data['data'] = $this->db()->getAll($sql);
        }else{
    		//不要用*,修改为具体字段
    		$sql = "SELECT * FROM `".$this->table()."`";
            
            $sql .=" WHERE 1";
    		if(!empty($where['_id']))
    		{
    			$sql .=" AND `order_id`=".$where['_id'];	
    		}
    //		if($where['xxx'] != "")
    //		{
    //			$str .= "`xxx` like \"%".addslashes($where['xxx'])."%\" AND ";
    //		}
    //		if(!empty($where['xx']))
    //		{
    //			$str .= "`xx`='".$where['xx']."' AND ";
    //		}

    		$sql .= " ORDER BY `action_id` DESC";
        }
		$data = $this->db()->getAll($sql);
		return $data;
	}

    function getAllSupplier(){
        $sql="select * from kela_supplier.app_processor_info where status=1";
        $res=$this->db()->getAll($sql);
        return $res;
    }

}

?>