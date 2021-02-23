<?php
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('Asia/Shanghai');
define('ROOT_PATH',str_replace('\\','/',realpath(dirname(__FILE__))));//定义目录
define('API_ROOT',ROOT_PATH.'/lib/');
include_once(API_ROOT.'PdoModel.php');
include_once(API_ROOT.'GoodsClassModel.php');
$GoodsModel = new GoodsClassModel();
$filenames = 'goods1';
$filename = ROOT_PATH.'/'.$filenames.'.csv';
$file = fopen($filename,'r');
$i=0;
$file_path = ROOT_PATH.'/baseup_'.$filenames.'.txt';
$file_add = ROOT_PATH.'/appadd_'.$filenames.'.txt';
$file_up = ROOT_PATH.'/appup_'.$filenames.'.txt';

$jiajia = '2.2';   	//加价率
$setvalue = '0'; 	//固定值
while ($obj = fgetcsv($file))
{
	if($i==0){
		$i++;
		continue;
	}
	
	$goodsid = $obj[0];//虚拟货号
	$goodssn = $obj[1];//虚拟款号
	$chengbenjia = $obj['4'];//成本
	
	//获取款式信息
	$procatinfo = array('product_type'=>'','category'=>'');
	
	$proandcat = $GoodsModel->getproandcat($goodssn);
	if(!empty($proandcat))
	{
		$procatinfo['product_type'] = $proandcat['product_type'];
		$procatinfo['category'] = $proandcat['style_type'];
	}
	/**********  base_salepolicy_goods  **********/
	//查找是否存在这样的虚拟货号了
	$basedata['goods_id'] = $goodsid;
	$basedata['goods_sn'] = $goodssn;
	$checkres = $GoodsModel->checkgoods($basedata);
	if(empty($checkres))
	{
		//插入到base_salepolicy_goods表中
		echo '插入到base_salepolicy_goods表中<br/>';
		$goodsdata = array();
		$goodsdata['goods_id'] = $goodsid;
		$goodsdata['goods_sn'] = $goodssn;
		$goodsdata['goods_name'] = $goodsid;
		$goodsdata['isXianhuo']= 0;
		$goodsdata['chengbenjia'] = $chengbenjia;
		$goodsdata['category']= $procatinfo['category'];
		$goodsdata['product_type']= $procatinfo['product_type'];
		$goodsdata['add_time']=date('Y-m-d H:i:s');
		$goodsdata['is_sale'] = 1;
		$goodsdata['type'] = 1;
		$goodsdata['is_base_style'] = 1;
		$goodsdata['is_valid']=1;
		$goodsdata['cate_g']=0;
		$goodsdata['is_policy']=2;
		$sql = $GoodsModel->autoinsert('front.base_salepolicy_goods',$goodsdata);
		//$sql .= ";\r\n";
		//file_put_contents($file_path, $sql, FILE_APPEND );
	}else{
		//修改价格和让他上架,并且有销售政策
		if($checkres['chengbenjia'] != $chengbenjia)
		{
			$sql = $GoodsModel->updategoods($goodsid,$chengbenjia);
			//$sql .= ";\r\n";
			//file_put_contents($file_path, $sql, FILE_APPEND );
		}
		//echo '修改base_salepolicy_goods表中的记录<br/>';
	}

	
	/********** app_salepolicy_goods  **********/
	$appdata = array();
	$appdata['goods_id'] = $goodsid;
	$appdata['policy_id'] = 29;
	$checkapp = $GoodsModel->checkapp($appdata);
	if(empty($checkapp))
	{
		//插入到app_salepolicy_goods表中
		$appdata['isXianhuo'] = 0;
		$appdata['sta_value'] = $setvalue;
		$appdata['chengben'] = $chengbenjia;
		$appdata['jiajia'] = $jiajia;
		$appdata['sale_price'] = round($chengbenjia*$jiajia);
		$appdata['create_time'] = date("Y-m-d H:i:s");
		$appdata['create_user'] = 'adminauto';
		$appsql = $GoodsModel->autoinsert('front.app_salepolicy_goods',$appdata);
		//$appsql .= ";\r\n";
		//file_put_contents($file_add, $appsql, FILE_APPEND );
		//echo '插入到app_salepolicy_goods表中<br/>';
	}else{
		//修改app_salepolicy_goods表中的记录
		//echo '修改app_salepolicy_goods表中的记录<br/>';
		if($checkapp['chengben'] != $chengbenjia )
		{
			$appsql = $GoodsModel->updateapp($goodsid,29,$chengbenjia,round($chengbenjia*$jiajia));
			//$appsql .= ";\r\n";
			//file_put_contents($file_up, $appsql, FILE_APPEND );
		}
	}
	$i++;
	echo $i.'  \r\n';
}
//file_put_contents(ROOT_PATH.'/upbase_goods.txt',$centent);
?>