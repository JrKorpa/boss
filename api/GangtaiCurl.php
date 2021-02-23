<?php
/**
 *  -------------------------------------------------
 *   @file		: .php
 *   @link		:  www.kela.cn
 *   @copyright	: 2017-2054 kela Inc
 *   @author	: luochuanrong
 *   @date		:2017/11/08
 *   @update	:
 *  -------------------------------------------------
 */
//header("Content-Type:text/html;charset=UTF-8");
 include_once '../frame/init.php';
 include_once 'class/ErrorClass.php';
 include_once 'model/GangtaiModel.class.php';
//$data = $_POST;
$data=$_REQUEST;
$date=date('Y-m-d');
file_put_contents("logs/gangtaicurl_{$date}.log", json_encode($data).PHP_EOL, FILE_APPEND);
$res=array('success'=>0,'error'=>'','data'=>array(),'total_page'=>0);
$parmas=array('api_name','start_date','end_date','pagesize','page','sign');
if($data)
{
	
	$data['pagesize']=intval($data['pagesize']);
	$data['page']=intval($data['page']);
	foreach ($parmas as $key => $v) {
	 	if(!isset($data[$v]))
		    $res['error']='参数未设置 '; 
	}	
	if(!in_array($data['api_name'],array('Get_sale_detail','Get_inventory_detail')))
        $res['error'].='api不存在 ';
	if($data['pagesize']>5000)
		$res['error'].='页大小不能超过5000行 ';
    $sign=$data['sign'];
    unset($data['sign']);

	if($sign!=md5(json_encode($data)))
	    $res['error'].='签名不正确 ';   	   
    if(!empty($res['error']))
    	exit(json_encode($res));	
	
 
	$model=new GangtaiModel(11);    
    $api=$data['api_name'];    
	$return=$model->$api($data);
	exit(json_encode(array_merge($res,$return)));
}
?>
