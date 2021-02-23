<?php
/**
 *  -------------------------------------------------
 *   @file		: ApiOrderModel.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Zhangyuanyuan <1041522879@qq.com>
 *   @date		: 2015年1月19日
 *   @update	:
 *  -------------------------------------------------
 */
class ApiMemberModel
{
    function getMemberByPhone($where){
        $keys = array('member_phone');
        $vals = array($where['member_phone']);
        
        $ret=ApiModel::sale_member_api($keys,$vals,'GetMemberByPhone');
        return $ret;
    }
    
    function getMemberByName($where){
        $keys = array('member_name');
        $vals = array($where['member_name']);
        
        $ret=ApiModel::sale_member_api($keys,$vals,'GetMemberByName');
        return $ret;
    }

    function GetMemberByMember_id($member_id){
        $keys = array('member_id');
        $vals = array($member_id);

        $ret=ApiModel::sale_member_api($keys,$vals,'GetMemberByMember_id');
//var_dump($ret,$member_id);
        return $ret;
    }
    
    function createMember($where){
        foreach ($where as $k=>$v){
            $keys[] = $k;
            $vals[] = $v;
        }
        $ret=ApiModel::sale_member_api($keys,$vals,'addMemberInfo');
        return $ret;
    }
    
    //插入用户地址
    function AddMemberAddressInfo($where){
        foreach ($where as $k=>$v){
            $keys[] = $k;
            $vals[] = $v;
        }
        
        $ret=ApiModel::sale_member_api($keys,$vals,'AddMemberAddressInfo');
        return $ret;
    }

}

?>
