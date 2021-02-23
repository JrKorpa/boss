<?php
/**
 * 款式API数据模型（代替Style/Api/api.php）
 *  -------------------------------------------------
 *   @file      : ProccesorModel.php
 *   @link      :  www.kela.cn
 *   @copyright : 2014-2024 kela Inc
 *   @author    : Laipiyang <462166282@qq.com>
 *   @date      : 2015-02-10 15:34:30
 *   @update    :
 *  -------------------------------------------------
 */
class SelfStyleModel extends SelfModel
{
    protected $db;
    function __construct ($strConn="")
    {
        parent::__construct($strConn);
    }
    /**
     * 根据款号检测是否是三生三世款
     * @param unknown $style_sn
     * @return boolean
     */
    public function isSanShengSanShi($style_sn){
        $sql = "select xilie from front.base_style_info where style_sn='{$style_sn}'";
        $xilie = $this->db()->getOne($sql);
        $xilieArr = explode(",",trim($xilie,","));
        if(in_array(25,$xilieArr)){
            return true;
        }else{
            return false;
        }
        
    }

    /**
     * 根据款号取工费
     * @param unknown $style_sn
     * @return boolean
     */
    public function getStyleWageFee($style_sn){
        $data = array();
        if($style_sn){
            $sql = "select fee_type,price from front.app_style_fee where style_sn='{$style_sn}' and status = 1 and fee_type in(1,4)";
            $fee = $this->db()->getAll($sql);
            if(!empty($fee)){
                $data = array_column($fee, null,'fee_type');
            }
        }
        return $data;
    }
}

?>