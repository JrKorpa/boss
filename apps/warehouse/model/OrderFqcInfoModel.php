<?php
/**
 *  -------------------------------------------------
 *   @file		: OrderFqcModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-04-08 18:17:23
 *   @update	:
 *  -------------------------------------------------
 */
class OrderFqcInfoModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'order_fqc';
		$this->pk='id';
		$this->_prefix='';
        $this->_dataObject = array("id"=>" ",
"order_sn"=>"订单号",
"problem_type"=>"问题类型（工厂，订单，仓库）",
"problem"=>"问题（刻字、）",
"datatime"=>" ",
"remark"=>"备注",
"is_pass"=>"是否质检通过",
"admin"=>" ");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url OrderFqcController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		//不要用*,修改为具体字段
		$sql = "SELECT a.`id`,a.`order_sn`,a.`problem_type`,a.`problem`,a.`datatime`,a.`is_pass`,a.`problem_type`,a.`remark`,a.`admin` FROM `".$this->table()."` as a left join app_order.base_order_info as b on b.order_sn = a.order_sn";
		$str = '';
        if(isset($where['hidden']) && $where['hidden'] != ''){
            $str .= " b.hidden = ".$where['hidden']." AND ";
        }
                if (isset($where['time_start']) && $where['time_start'] != '') {
                    $str .= "a.`datatime` >='".$where['time_start']." 00:00:00' AND ";
                    
                }
                
                if (isset($where['time_end']) && $where['time_end'] != '') {
                    $str .= "a.`datatime` <='".$where['time_end']." 24:59:59' AND ";
                }
                if (isset($where['is_pass']) && $where['is_pass'] != '') {
                    $str .= "a.`is_pass`=".$where['is_pass']." AND ";
                }
                if (isset($where['qc_type']) && $where['qc_type'] != '') {
                    $str .= " a.`problem_type` = ".$where['qc_type']." AND ";
                }
                if (isset($where['qc2']) && $where['qc2'] != '') {
                    $str .= " a.`problem` = ".$where['qc2']." AND ";
                }
                if (isset($where['order_sn']) && $where['order_sn'] != '') {
                    $str .= " a.`order_sn` = '".$where['order_sn']."' AND ";
                }
                if (isset($where['operator']) && $where['operator'] != '') {
                    $str .= "a.`admin` like \"%".addslashes($where['operator'])."%\" AND ";
                }
		if(isset($where['consignee']) && $where['consignee'] != '') {
                    //根据客户姓名返回订单号，再根据订单号查询
                    $consignee= $where['consignee'];
                    $new_data = $this->getOrderSnBydata(array('consignee' => $consignee));
                    $p_sn = '';
                    if (!empty($new_data)) {
                        foreach($new_data as $key => $val) {
                            $problem_bye = $val['qc_type'];//返回具体的问题类型
                            $p_sn .= "'".$val['p_sn']."',";
                            $data['data'][$key]['consignee'] = $consignee;
                            
                        }
                        $p_sn = substr($p_sn, 0,-1);
                        $str .= " a.`order_sn` in ($p_sn) AND ";
                    }else {
                        $str .= " a.`order_sn` in ('') AND ";
                    }
                   
                    
                   
                }
                if (isset($where['bc_sn']) && $where['bc_sn'] != ''){
                    //根据布产号查询订单号，根据订单号查询
                    $bc_sn = $where['bc_sn'];
                    $new_data = $this->getOrderSnBydata(array('bc_sn' => $where['bc_sn']));
                    //var_dump($new_data);exit;
                    $p_sn = '';
                    if (!empty($new_data)) {
                        $p_sn .= "'".$new_data['p_sn']."',";
                        $p_sn = substr($p_sn, 0,-1);
                        $str .= " a.`order_sn` in ($p_sn) AND ";
                        
                    }else {
                        $str .= " a.`order_sn` in ('') AND ";
                    }
                   
                    
                }
		if($str)
		{
			$str = rtrim($str,"AND ");//这个空格很重要
			$sql .=" WHERE ".$str;
		}
		$sql .= " ORDER BY a.`id` DESC";
                //echo $sql;exit;
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
                
               
                $i = 0;
                foreach($data['data'] as $key => $val){
                    $order_sn = $val['order_sn'];
                    $problem_type = $val['problem_type'];
                    $problem_info = $this->getProblemType($problem_type);
                    $ret = $this->getConsignee($order_sn);
                    if (isset($problem_info['cat_name']) && !empty($problem_info['cat_name'])){
                        $data['data'][$key]['problem_type'] = $problem_info['cat_name'];
                    }else {
                        $data['data'][$key]['problem_type'] = "";
                    }
                    if(isset($ret['return_msg']['consignee'])) {
                        $consignee = $ret['return_msg']['consignee'];
                        $data['data'][$key]['consignee'] = $consignee;
                        
                    }  else {

                        $data['data'][$key]['consignee'] = "";
                    }
                    if (isset($ret['return_msg']['consignee'])) {
                        $bc_sn = $ret['return_msg']['bc_sn'];
                        $data['data'][$key]['bc_sn'] = $bc_sn;
                    }else {
                        $data['data'][$key]['bc_sn'] = "";
                    }

                }
                //var_dump($data);
		return $data;
	}
        function getConsignee($order_sn){
                       // exit('ddd');
            $data = ApiModel::pro_api("GetProductInfo", array('order_sn'=>$order_sn));
            //var_dump($data);exit;
            return $data;
        }
        function getProblemType($problemtype){
            if(!empty($problemtype)){
                $sql = "select `cat_name` from `order_fqc_conf` where `is_deleted` = 0 and `id`=$problemtype";
                //exit($sql);
                $data = $this->db()->getRow($sql);
                return $data;
            }else {
                return '';
            }
        }
        function getOrderSnBydata($data) {
            if (isset($data['bc_sn']) && $data['bc_sn'] != '') {
                $bc_sn = $data['bc_sn'];
            }else {
                $bc_sn = '';
            }
            if (isset($data['consignee']) && $data['consignee'] != '') {
                $consignee = $data['consignee'];
            }else{
                $consignee = '';
            }
            $keys = array('bc_sn','consignee');
            $vals = array($bc_sn,$consignee);
            $ret = ApiModel::pro_api( "GetProductInfo",$data);
            return $ret['return_msg'];
        }
        function get_problem_type() {
            $sql = "SELECT `ID`,`cat_name` from `order_fqc_conf` where `parent_id`=0 and `is_deleted`=0"; 
            
            $data = $this->db()->getAll($sql);
            return $data;
        }
        function get_op2() {//二级分类问题
            $sql = "SELECT `ID`,`cat_name` from `order_fqc_conf` where `parent_id`!=0 and `is_deleted`=0"; 
            $data = $this->db()->getAll($sql);
            return $data;
        }
}

?>