<?php
/**
 *  -------------------------------------------------
 *   @file		: PurchaseInfoModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: ZhangLijuan <82739364@qq.com>
 *   @date		: 2015-01-08 14:13:19
 *   @update	:
 *  -------------------------------------------------
 */
class PurchaseInfoModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'purchase_info';
        $this->_dataObject = array("id"=>" ",
"p_sn"=>"采购单单号",
"t_id"=>"采购单分类ID",
"is_tofactory"=>"是否去工厂生产",
"is_style"=>"是否有款采购",
"p_sum"=>"采购数量",
"purchase_fee"=>"采购申请费用",
"put_in_type"=>"采购方式（数据字典入库方式）",
//"apply_uname"=>"申请人",
"make_uname"=>"制单人姓名",
"make_time"=>"制单时间",
"check_uname"=>"审核人姓名",
"check_time"=>"审核时间",
"p_status"=>"采购单状态：1=新增，2=待审核，3=审核，4=驳回",
"p_info"=>"采购单备注");
		parent::__construct($id,$strConn);
	}

		/**
	 *	pageList，分页列表
	 *
	 *	@url ApplicationController/search
	 */
	function pageList ($where,$page,$pageSize=10,$useCache=true)
	{
		$sql = "SELECT * FROM `".$this->table()."` WHERE 1 ";
		if(!empty($where['p_sn']))
		{
			//$sql .= " AND p_sn like \"%".addslashes($where['p_sn'])."%\"";
			$where['p_sn']="'".implode("','",$where['p_sn'])."'";
			$sql .= " AND p_sn in ({$where['p_sn']})";
		}
        if(isset($where['hidden']) && $where['hidden'] != ''){
            $sql .= " and hidden = ".$where['hidden'];
        }
		if($where['t_id'] != "")
		{
			$sql .= " AND t_id = ".$where['t_id'];
		}
		if($where['p_status'] !== "")
		{
			$sql .= " AND p_status = ".$where['p_status'];
		}
//		if($where['apply_uname'] != "")
//		{
//			$sql .= " AND apply_uname like \"%".addslashes($where['apply_uname'])."%\"";
//		}
		if($where['make_uname'] != "")
		{
			$sql .= " AND make_uname like \"%".addslashes($where['make_uname'])."%\"";
		}
		if($where['check_uname'] != "")
		{
			$sql .= " AND check_uname like \"%".addslashes($where['check_uname'])."%\"";
		}
		if($where['put_in_type'] !== "")
		{
			$sql .= " AND put_in_type = ".$where['put_in_type'];
		}
		$sql .= " ORDER BY id DESC";
		$data = $this->db()->getPageList($sql,array(),$page, $pageSize,$useCache);
                //接口获取跟单人
                $proApi = new ApiProcessorModel();
                foreach ($data['data'] as $k => $v){
                    $p_sn = $v['p_sn'];
                    $rs = $proApi->GetOpraName(array('order_sn'),array($p_sn));
                    if(isset($rs['opra_uname']) && $rs['opra_uname'] != ''){
                       $opra_uname = $rs['opra_uname']; 
                       $data['data'][$k]['opra_uname'] = $opra_uname;
                    }else{
                        $data['data'][$k]['opra_uname'] = '';
                    }
                    
                    
                }
//                echo '<pre>';
//                print_r($data);
//                echo '</pre>';exit;
		return $data;
	}
        
        

	//根据款式分类取数量
	function getCountForType($t_id)
	{
		$sql = "SELECT count(1) from ".$this->table()." WHERE t_id = ".$t_id;
		$c = $this->db()->getOne($sql);
		return $c;
	}
	//是否存在此采购单
	public function isExistPsn($p_sn)
	{
		$sql = "SELECT COUNT(1) FROM ".$this->table()." WHERE p_sn = '$p_sn'";
		$is_exist = $this->db()->getOne($sql);
		if($is_exist)
		{
			return true;
		}
		return false;
	}

	//根据采购单单号取数据
	public function getRowOfpsn($p_sn)
	{
		$sql = "SELECT `id`, `p_sn`, `t_id`, `is_tofactory`, `is_style`, `p_sum`, `purchase_fee`, `put_in_type`, `make_uname`, `make_time`, `check_uname`, `check_time`, `p_status`, `p_info` FROM ".$this->table()." WHERE p_sn = '$p_sn'";
		return $this->db()->getRow($sql);
	}
	//获取不可以作废的列表add by zhangruiying
	function GetListByAddStatus($ids)
	{
		$sql = "SELECT `id`, `p_sn` FROM ".$this->table()." WHERE id in ($ids) and `p_status`>1";
		$list=$this->db()->getAll($sql);
		$arr=array();
		if(!empty($list))
		{
			foreach($list as $l)
			{
				$arr[$l['id']]=$l['p_sn'];
			}
		}
		return $arr;
	}
	function MutiUpdateStatus($ids)
	{
		$sql = "update `".$this->table()."` set `p_status`=4,check_time='".date('Y-m-d H:i:s')."',check_uname='".$_SESSION['userName']."' where `id` in ($ids) and `p_status`=1";
		return $this->db()->query($sql);

	}
	//add end;
}

?>