<?php
/**
 * 供应商API数据模型（代替Proccesor/Api/api.php）
 *  -------------------------------------------------
 *   @file      : ProccesorModel.php
 *   @link      :  www.kela.cn
 *   @copyright : 2014-2024 kela Inc
 *   @author    : Laipiyang <462166282@qq.com>
 *   @date      : 2015-02-10 15:34:30
 *   @update    :
 *  -------------------------------------------------
 */
class SelfProccesorModel extends SelfModel
{
    protected $db;
    function __construct ($strConn="")
    {
        parent::__construct($strConn);
    }    
    //添加布产操作日志（product_opra_log表插入记录）
    public function addProductOpraLog($data){
        $sql = $this->insertSql($data,'product_opra_log');
        return $this->db()->query($sql);
    }
    //添加布产操作日志
    public function addBuchanOpraLog($bc_id,$remark){
         if(empty($bc_id)){
             return false;
         }
         $bc_status = $this->db()->getOne("select `status` from product_info where id={$bc_id}");
         $data=array(
            'bc_id'		=> $bc_id,
            'status'	=> $bc_status,//当前布产状态
            'remark'	=> $remark,
            'uid'		=> isset($_SESSION['userId'])?$_SESSION['userId']:0,
            'uname'		=> isset($_SESSION['userName'])?$_SESSION['userName']:'第三方',
            'time'		=> date('Y-m-d H:i:s')
        );
        $sql = $this->insertSql($data,'product_opra_log');
        return $this->db()->query($sql);
    }
    public  function selectProductInfo($field="*",$where,$type=2){
        return $this->select($field, $where, $type,"product_info");
    }   

    public function getFaceworkByBcNo($bc_no){
    	$sql="select b.value from product_info a,product_info_attr b where a.id=b.g_id and a.bc_sn='{$bc_no}' and b.code='face_work'";
    	$face_work=$this->db()->getOne($sql);
    	if(!$face_work) $face_work='';
    	return $face_work; 
    }
    
    function getProInfo($where,$field="*",$type=2){
    	return $this->select($field, $where, $type,"product_factory_oprauser");
    }

}

?>