<?php
/**
 * used:   按照模板,导入销售政策以及销售政策绑定的销售渠道
 * author: lly
 * date:   20161223
**/
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__))));//定义目录
define('API_ROOT',ROOT_PATH.'/lib/');
include_once(API_ROOT.'PdoModel.php');
include(API_ROOT.'OrderClassModel.php');

$source = getopt('a:');
$cname = '直营店';
if(empty($source))
{
	$name = 'zyd';
}else{
	$name = $source['a'];
	
}
$type = array('zyd','jxs');
if(!in_array($name,$type))
{
	die('you are bad man');
}
$cname = ( $name == 'jxs') ? '经销商' : '直营店';	

$filenames = 'base_salepolicy_info_'.$name;
$filename = ROOT_PATH.'/templates/'.$filenames.'.csv';
$file = fopen($filename,'r');
$i=0;
/*
$file_path = ROOT_PATH.'/goods_to_salepolicy/baseup_'.$filenames.'.txt';
$file_add = ROOT_PATH.'/goods_to_salepolicy/appadd_'.$filenames.'.txt';
$file_up = ROOT_PATH.'/goods_to_salepolicy/appup_'.$filenames.'.txt';
*/
$parames = array(
'policy_name'=>'',   						//销售政策名称
'policy_start_time'=>'',   					//开始时间
'policy_end_time' => '',   					//结束时间
'create_time' => date('Y-m-d H:i:s'),       //记录创建时间
'create_user'=>'admin',         			//记录创建人
'create_remark'=>$cname.'批量导入',	   			//记录创建备注
'check_user'=>'admin',          			//审核人
'check_time'=>date('Y-m-d H:i:s'),          //审核时间
'zuofei_time'=>'',         					//作废时间
'check_remark'=>$cname.'批量导入,自动审核通过',    //记录备注
'bsi_status'=>3,          					//记录状态 1保存,2申请审核,3已审核,4取消
'is_delete'=>0,           					//记录是否有效 0有效1无效
'is_together'=>1,							//策略类型：1，普通；2，打包
'jiajia'=>1,								//加价率
'sta_value'=>0,								//固定值
'is_default'=>1,							//是否为默认政策1为默认2位不是默认
'is_favourable'=>1,							//是否允许优惠
'product_type'=>'',							//产品线
'tuo_type'=>1,								//金托类型
'huopin_type'=>'',							//货品类型
'cat_type'=>'',								//款式分类
'range_begin'=>0,							//镶口开始范围
'range_end'=>0,								//镶口结束范围
'zhushi_begin'=>0,							//主石开始范围
'zhushi_end'=>0,							//主石结束范围
'is_kuanprice'=>0,							//是否按款定价 0不是1是
'update_time'=>'',							//更新时间
);

//销售渠道
$channel = array(
'policy_id'=>'',    //销售策略id
'channel'=>'',      //渠道id
'channel_level'=>1, //等级
'create_time'=>date('Y-m-d H:i:s'),
'create_user'=>'admin',
'check_time'=>date('Y-m-d H:i:s'),
'check_user'=>'admin',
'status'=>3,       //状态:1保存2申请3审核通过4未通过5取消
'is_delete'=>1     //取消 1未删除 2已删除
);

$Model = new OrderClassModel();
while ($obj = fgetcsv($file))
{
	if($i==0){
		$i++;
		continue;
	}
	$i++;
	$data = $parames;
	//$goodsid = $obj[0];//虚拟货号
	//echo $goodsid;
	$data['policy_name'] = iconv('gb2312','utf-8',$obj[0]); 
	$data['policy_start_time'] = iconv('gb2312','utf-8',$obj[1]) ? iconv('gb2312','utf-8',$obj[1]) : date('Y-m-d H:i:s');
	$time = 2020+$i;
	$data['policy_end_time'] = iconv('gb2312','utf-8',$obj[2]) ? iconv('gb2312','utf-8',$obj[2]) : $time.'-12-31 00:00:00';
	$data['jiajia'] = iconv('gb2312','utf-8',$obj[3]);
	$data['sta_value'] = iconv('gb2312','utf-8',$obj[4]);
	
	$data['range_begin'] = $obj[5]?$obj[5]:0;
	$data['range_end'] = $obj[6]?$obj[6]:0;
	$data['zhushi_begin'] = $obj[7]?$obj[7]:0;
	$data['zhushi_end'] = $obj[8]?$obj[8]:0;
	$data['product_type'] = iconv('gb2312','utf-8',$obj[9]) ? iconv('gb2312','utf-8',$obj[9]) : '全部';
	
	$data['tuo_type'] = $obj[10] ? $obj[10] : 0;
	
	//0期货 1 现货  2全部
	if($obj[11]=='')
	{
		$data['huopin_type']=2;
	}else{
		$data['huopin_type'] = $obj[11] ? 1 : 0;
	}
	/*
	if((int)$obj[11]==1)
	{
		$data['huopin_type']=1;
	}elseif((int)$obj[11]==0)
	{
		$data['huopin_type']=0;
	}else{
		$data['huopin_type'] = 2;
	} */
	$data['cat_type'] = iconv('gb2312','utf-8',$obj[12]) ? iconv('gb2312','utf-8',$obj[12]) :  '全部';
	
	$data = $Model->filterarr($data);
	//销售政策入库
	$policy_id = $Model->autoinsert('front.base_salepolicy_info',$data);
	if(!$policy_id){
		print_r($data);	
	}
	//销售渠道:
	$department = iconv('gb2312','utf-8',$obj[13]);
	if(!empty($department))
	{
		$tmps = explode(',',$department);
		$tmps = array_filter($tmps);
		foreach($tmps as $chennelid)
		{
			$tmpparems = $channel;
			$tmpparems['policy_id']= $policy_id;
			$tmpparems['channel'] = $chennelid;
			$tmpparems = $Model->filterarr($tmpparems);
			$Model->autoinsert('front.app_salepolicy_channel',$tmpparems);
			//关联渠道入库
		}
	}	
}


?>