<?php
/**
 *  -------------------------------------------------
 *   @file		: AppBespokeActionLogModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015-01-12 14:32:07
 *   @update	:
 *  -------------------------------------------------
 */
class AppBespokeOldModel extends Model
{
	function __construct ($id=NULL,$strConn="")
	{
		$this->_objName = 'app_bespoke_info';
		$this->pk='action_id';
		$this->_prefix='';
        $this->_dataObject = array("action_id"=>"自增ID",
"bespoke_id"=>"预约ID",
"create_user"=>"操作人  ",
"create_time"=>"操作时间",
"IP"=>"操作IP",
"bespoke_status"=>"预约状态",
"remark"=>"备注");
		parent::__construct($id,$strConn);
	}

	/**
	 *	pageList，分页列表
	 *
	 *	@url AppBespokeActionLogController/search
	 */
	function pageList ($where,$page,$pagesize=10,$useCache=true)
	{
		if(isset($page))
		{
			$keys[]='page';
            $vals[]=$page;
		}
        if(isset($pagesize)){
            $keys[]='pagesize';
            $vals[]=$pagesize;
        }
        if(isset($where['bespoke_sn'])){
            $keys[]='bespoke_sn';
            $vals[]=$where['bespoke_sn'];
        }
        if(isset($where['bespoke_man'])){
            $keys[]='bespoke_man';
            $vals[]=$where['bespoke_man'];
        }
        if(isset($where['mobile'])){
            $keys[]='mobile';
            $vals[]=$where['mobile'];
        }       
        if(isset($where['department'])){
            $keys[]='department';
            $vals[]=$where['department'];
        }       
        if(isset($where['start_add_time'])){
            $keys[]='start_add_time';
            $vals[]=$where['start_add_time'];
        }       
        if(isset($where['end_add_time'])){
            $keys[]='end_add_time';
            $vals[]=$where['end_add_time'];
        }       
    
        $ret = ApiModel::bossgate_api("getBespokeList",$keys, $vals);
       
        return $ret;  
	}

	/**
	 *	getAllList，下载
	 *
	 *	@url AppBespokeActionLogController/getAllList
	 */
	function getAllList ($where)
	{
        if(isset($where['bespoke_sn'])){
            $keys[]='bespoke_sn';
            $vals[]=$where['bespoke_sn'];
        }
        if(isset($where['bespoke_man'])){
            $keys[]='bespoke_man';
            $vals[]=$where['bespoke_man'];
        }
        if(isset($where['mobile'])){
            $keys[]='mobile';
            $vals[]=$where['mobile'];
        }       
        if(isset($where['department'])){
            $keys[]='department';
            $vals[]=$where['department'];
        }       
        if(isset($where['start_add_time'])){
            $keys[]='start_add_time';
            $vals[]=$where['start_add_time'];
        }       
        if(isset($where['end_add_time'])){
            $keys[]='end_add_time';
            $vals[]=$where['end_add_time'];
        }       
    
        $ret = ApiModel::bossgate_api("getBespokeListDownLoad",$keys, $vals);
       
        return $ret;  
	}

    /**
     * 	预约详情
     *
     * 	@url DiamondListController/search
     */
    function getBespokeByBespoke_sn($where) {

        if(isset($where['bespoke_sn'])){
            $keys[]='bespoke_sn';
            $vals[]=$where['bespoke_sn'];
        }     

        $ret = ApiModel::bossgate_api("getBespokeByBespokeSn",$keys, $vals);
       
        return $ret;              
    }

    /**
     * 	经销商
     *
     * 	@url DiamondListController/getQudaoList
     */
    function getQudaoList($dc_id) {

        if($dc_id!=''){
            $keys[]='dc_id';
            $vals[]=$dc_id;
        }      

        $ret = ApiModel::bossgate_api("getQudaoList",$keys, $vals);
        return $ret;              
    }
}

?>