<?php
class ApiManagementModel
{
    function getJxslist($ids = array()) {
        $ret=ApiModel::management_api(array('ids'),array($ids),'GetJxslist');
        return $ret['error'] == 0 ? $ret['data'] : array();
    }
    
    function GetSalesChannelsByIds($dept_ids) {
        if (empty($dept_ids)) return array();
        
        $dept_str = implode(',', array_unique($dept_ids));
        $resp = ApiModel::management_api(array('dept_ids'), array($dept_str), 'GetSalesChannelsByIds');
        
        return $resp['error'] == 0 ? $resp['data'] : array();
    }
	
	function GetChannelClassByIds($ids) {
        if (empty($ids)) return array();
        $resp = ApiModel::management_api(array('ids'), array($ids), 'GetChannelClassByIds');
        return $resp['error'] == 0 ? $resp['data'] : array();
    }
	
}

?>