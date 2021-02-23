<?php
/**
 * 裸钻模块新APiModel类（代替Diamond/Api/api.php）
 *  -------------------------------------------------
 *   @file		: SelfDiamondModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: gaopeng
 *   @date		: 2015-02-10 15:34:30
 *   @update	:
 *  -------------------------------------------------
 */
class SelfDiamondModel extends SelfModel
{

    function __construct ($strConn="")
	{
		parent::__construct($strConn);
	}
	//查询裸钻通用方法
	public function selectDiamondInfo($field,$where,$type=1){
	    return $this->select($field,$where,$type,'diamond_info');
	}
	//查询裸钻双十一 通用方法
    public function selectDiamondSSY($field,$where,$type=1){
        return $this->select($field,$where,$type,'diamond_ssy_tejia');
    }
    
    	/*
	*通过证书号获取裸钻信息
	*
	*/
    public function getGoodsTypeByCertId($cert_id,$cert_id2=''){
		if(!empty($cert_id2)){
			$sql ="select good_type from front.diamond_info_all where cert_id='".$cert_id."' OR cert_id ='".$cert_id2."'";
		}else{
			$sql ="select good_type from front.diamond_info_all where cert_id='".$cert_id."'";
		}
		$type = $this->db()->getOne($sql);
		if (empty($type)) {
		    if(!empty($cert_id2)){
		        $sql ="select good_type from front.diamond_info where cert_id='".$cert_id."' OR cert_id ='".$cert_id2."'";
		    }else{
		        $sql ="select good_type from front.diamond_info where cert_id='".$cert_id."'";
		    }
		    $type = $this->db()->getOne($sql);
		}
        return $type;
    }  
    
    /**
     * 通过证书号查询裸钻
     * @param *
     * @return json
     */
    public function getDiamondInfoByCertId($cert_id)
    {
 
    	if(!empty($cert_id)){
    		//查询商品详情
    		$sql="select * from `diamond_info` where cert_id='{$cert_id}'";
    		$row = $this->db->getRow($sql);
    		if(empty($row)){
    			$sql="select * from `diamond_info_all` where cert_id='{$cert_id}'";
    			$row = $this->db->getRow($sql);
    			if(!empty($row)){
    				$row['status'] = 2;
    				$row['is_bakdata'] = 1;
    			}
    		}
    	}else{
    		$row = false;
    	}
    
    	return $row;
    }
    
    //验证证书号在裸钻列表,彩钻列表 是否存在
    public function checkZhengshuhao($zhengshuhao)
    {
        $sql="select 1 from front.diamond_info where cert_id = '$zhengshuhao' 
              union
              select 1 from front.app_diamond_color where cert_id = '$zhengshuhao'";
        if($this->db()->getOne($sql)){
            return true;
        }
            
        return false;
    }

    
    public function SalePolicyInfo($where){
    	$policy_id = $where['policy_id'];
    	if(!empty($policy_id)){
    		$sql = "SELECT `policy_id`,`policy_name`,`policy_start_time`,`policy_end_time`,`create_time`,`create_user`,`create_remark`,`check_user`,`check_time`,`zuofei_time`,`check_remark`,`bsi_status`,`is_delete`,`is_together`,`jiajia`,`sta_value`,`is_favourable` FROM `base_salepolicy_info` WHERE `policy_id`=".$policy_id;
    		$data =  $this->db->getRow($sql);
    		if(!empty($data)){
    			return $data;
    		}else{
    			return null;
    		}
    	}else{
    		return null;
    	}
    }
}

?>