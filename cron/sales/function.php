<?php
function getPayType($name)
{
    global $conNew,$conOld;
    $sql="select id from cuteframe.payment where pay_name = '$name'";
	$row = $conNew->getRow($sql);
    if($row){
        return $row['id'];
    }else{
        return 0;
    }
}
function getCustomerSource($from_ad)
{
    global $conNew,$conOld;
    $sql="select id from cuteframe.customer_sources where source_code = '$from_ad'";
	$row = $conNew->getRow($sql);
    if($row){
        return $row['id'];
    }else{
        return 0;
    }
}
function getOldDepartmentName($id)
{
    global $conNew,$conOld;
    $sql="select dep_name from kela_order_part.ecs_department_channel where dc_id = '$id'";
	$row = $conOld->getRow($sql);
    if($row){
        return $row['dep_name'];
    }else{
        return '';
    }
}
function getNewDepartmentId($department_name)
{
    if(empty($department_name)){
        return 0;
    }
    global $conNew,$conOld;
    $sql="select id from cuteframe.sales_channels where channel_name = '$department_name'";
	$row = $conNew->getRow($sql);
    if($row){
        return $row['id'];
    }else{
        return 0;
    }
}
function getReturn($order_id)
{
    global $conNew,$conOld;
    $sql="select * from kela_order_part.ecs_return_goods where order_id = $order_id AND (leader_status=0 OR (leader_status=1 AND (order_goods_id=0 AND deparment_finance_status=0) OR (order_goods_id>0 AND goods_status=0)));";
	$res = $conOld->getOne($sql);
    return $res?1:0;
}
function getCompany($is_real_invoice)
{
    if($is_real_invoice){
        return 0;
    }
    return 0;
    $sql="select id from cuteframe.company where company_sn='5A'";
    global $conNew,$conOld;
	$row = $conNew->getRow($sql);
    if($row){
        return $row['id'];
    }else{
        return 0;
    }
}
function getMobile($orderInfo)
{
    $mobile = base64_decode($orderInfo['mobile']);
    if(preg_match('/1\d{10}/',$mobile)){
        return $mobile;
    }
    $tel = base64_decode($orderInfo['tel']);
    if(preg_match('/1\d{10}/',$tel)){
        return $tel;
    }
    if(!empty($orderInfo['tel'])){
        return $orderInfo['tel'];
    }
    if(!empty($orderInfo['mobile'])){
        return $orderInfo['mobile'];
    }
    return '';
}
function getGoodsInfo($goods_id)
{
    if(empty($goods_id)){
        $ret = array();
        $ret['caizhi'] = '';
        $ret['jinzhong'] = '';
        $ret['jingdu'] = '';
        $ret['jinhao'] = '';
        $ret['yanse'] = '';
        $ret['zhengshuhao'] = '';
        $ret['zuanshidaxiao'] = '';
        $ret['in_warehouse_type'] = '';
        $ret['account'] = '';
        $ret['yuanshichengben'] = '';
    }
    $sql="select * from jxc.jxc_goods where goods_id='$goods_id'";
    global $conNew,$conOld;
	$jxc_info = $conNew->getRow($sql);
    if($jxc_info){
        $put_in_type = array('1','2','3','4');

        $ret = array();
        $ret['caizhi'] = $jxc_info['zhuchengse'];
        $ret['jinzhong'] = $jxc_info['zhuchengsezhong'];
        $ret['jingdu'] = $jxc_info['zhushijingdu'];
        $ret['jinhao'] = $jxc_info['num'];
        $ret['yanse'] = $jxc_info['zhushiyanse'];
        $ret['zhengshuhao'] = $jxc_info['zhengshuhao'];
        $ret['zuanshidaxiao'] = $jxc_info['zhushizhong'];
        $ret['in_warehouse_type'] = isset($put_in_type[$jxc_info['storage_mode']])?$put_in_type[$jxc_info['storage_mode']]:0;
        $ret['account'] = $jxc_info['account'];//是否结价0、默认无。1、未结价。2、已结价
        $ret['yuanshichengben'] = $jxc_info['yuanshichengbenjia'];
        return $ret;
    }else{
        $ret = array();
        $ret['caizhi'] = '';
        $ret['jinzhong'] = '';
        $ret['jingdu'] = '';
        $ret['jinhao'] = '';
        $ret['yanse'] = '';
        $ret['zhengshuhao'] = '';
        $ret['zuanshidaxiao'] = '';
        $ret['in_warehouse_type'] = '';
        $ret['account'] = '';
        $ret['yuanshichengben'] = '';
        return $ret;
    }
}
function getCertInfo($cert){
    if(preg_match('/^GIA/',$cert)){
        $certInfo = array();
        $certInfo['cert'] = 'GIA';
        $certInfo['certid'] = str_replace('GIA','',$cert);
        return $certInfo;
    }
    if(preg_match('/^EGL/',$cert)){
        $certInfo = array();
        $certInfo['cert'] = 'EGL';
        $certInfo['certid'] = str_replace('EGL','',$cert);
        return $certInfo;
    }
    if(preg_match('/^AGL/',$cert)){
        $certInfo = array();
        $certInfo['cert'] = 'AGL';
        $certInfo['certid'] = str_replace('AGL','',$cert);
        return $certInfo;
    }    
    $certInfo['cert'] = '';
    $certInfo['certid'] = $cert;
    return $certInfo;
}

function getProvince($p_id)
{
    if(empty($p_id)){
        return '';
    }
    global $conNew,$conOld;
	$sql="select region_name from kela_order_part.ecs_region where region_id = $p_id AND region_type=1;";
    $region_name = $conOld->getOne($sql);
    $region_name = str_replace('省','',$region_name);
    $region_name = str_replace('自治区','',$region_name);
    $region_name = str_replace('壮族自治区','',$region_name);
    return $region_name;
}
function getCity($p_id)
{
    if(empty($p_id)){
        return '';
    }
    global $conNew,$conOld;
	$sql="select region_name from kela_order_part.ecs_region where region_id = $p_id AND region_type=2;";
    $region_name = $conOld->getOne($sql);
    $region_name = str_replace('市','',$region_name);
    return $region_name;
}
function getDistrict($p_id)
{
    if(empty($p_id)){
        return '';
    }
    global $conNew,$conOld;
	$sql="select region_name from kela_order_part.ecs_region where region_id = $p_id AND region_type=3;";
    $region_name = $conOld->getOne($sql);
    $region_name = str_replace('区','',$region_name);
    return $region_name;
}

function getProvinceId($name,$id)
{
    global $conNew,$conOld;
	$sql="select region_id from cuteframe.region where region_name = '$name' AND parent_id=1;";
    $region_id = $conNew->getOne($sql);
    if(empty($region_id)){
        $region_id=0;
    }
    return $region_id;
}

function getCityId($name,$id)
{
    if(empty($id)){
        return 0;
    }
    global $conNew,$conOld;
	$sql="select region_id from cuteframe.region where region_name = '$name' AND parent_id=$id;";
    $region_id = $conNew->getOne($sql);
    if(empty($region_id)){
        $region_id=0;
    }
    return $region_id;
}

function getDistrictId($name,$id)
{
    if(empty($id)){
        return 0;
    }
    global $conNew,$conOld;
	$sql="select region_id from cuteframe.region where region_name = '$name' AND parent_id=$id;";
    $region_id = $conNew->getOne($sql);
    if(empty($region_id)){
        $region_id=0;
    }
    return $region_id;
}

