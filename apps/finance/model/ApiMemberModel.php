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
    function GetMemberByMember_id($member_id){
        $keys = array('member_id');
        $vals = array($member_id);
        $ret=ApiModel::sale_member_api($keys,$vals,'GetMemberByMember_id');
        return $ret;
    }

}

?>