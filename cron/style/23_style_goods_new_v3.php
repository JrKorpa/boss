<?php
/*

/usr/local/php/bin/php cron/style/23_style_goods_new_v3.php style_goods_new
/usr/local/php/bin/php cron/style/23_style_goods_new_v3.php style_goods_new_v3


*/

if($argv[1] == 'style_goods_new' || $argv[1] == 'style_goods_new_v3'){
	$old_table = $argv[1];
}else{
	die('argv is empty;');
}




// v2,v3 的款属性
error_reporting(E_ALL);
header("Content-type:text/html;charset=utf8;");
date_default_timezone_set("PRC");
// define('ROOT_PATH',str_replace('crate_style_info.php', '', str_replace('\\', '/', __FILE__)));
// require_once(ROOT_PATH . 'config/shell_config.php');
$conOld = mysqli_connect('192.168.1.55','style_zyy','KELAzhangyuanyuan123','kela_style');
$conNew =  mysqli_connect('192.168.1.93','cuteman','QW@W#RSS33#E#','front');

$old_style = $old_table;	
$new_table = "list_style_goods";

//$field = "`goods_id`,`style_id`,`style_cat`,`style_sn`,`style_name`,`goods_sn`,`caizhi`,`yanse`,`last_update`,`zhushizhong`,`zhushi_num`,`fushizhong1`,`fushi_num1`,`fushizhong2`,`fushi_num2`,`fushi_chengbenjia_other`,`weight`,`jincha_shang`,`jincha_xia`,`shoucun`,`xiangkou`,`gongfei`,`baomiangongyi_gongfei`,`fushixiangshifei`,`dingzhichengben`,`is_ok`";

$field = ['goods_id','product_type_id','cat_type_id','style_id','style_sn','style_name','goods_sn','shoucun','xiangkou','caizhi','yanse','zhushizhong','zhushi_num','fushizhong1','fushi_num1','fushizhong2','fushi_num2','fushi_chengbenjia_other','weight','jincha_shang','jincha_xia','dingzhichengben','is_ok','last_update','is_base_style'];

$field_sql = implode('`,`',$field);




//旧的数据
$old_table = $old_table;
$sql = "SELECT count(*) FROM " . $old_table . "  WHERE 1 ";
$t = mysqli_query($conOld,$sql);
$cnt = mysqli_fetch_row($t);
$date_time = date("Y-m-d H:i:s");
$len = $cnt[0];

$num = 1000;

$forsize = ceil($len / $num);

$unsetArr = array('style_cat','gongfei','baomiangongyi_gongfei','fushixiangshifei');
//var_dump($forsize);exit;
for($ii = 1; $ii <= $forsize; $ii ++){
    echo $ii."----------------------------------------------------------"."\n";
    $offset = ($ii - 1) * $num;
    echo $sql = "SELECT * FROM " . $old_style . " limit $offset,$num";
echo "\r\n";
    $res = mysqli_query($conOld,$sql);
    $old_style_data = array();
	$val_sql=array();    
    while ( $row = mysqli_fetch_assoc($res) ){

		foreach($row as $k => $v){
			if(in_array($k,$unsetArr) || is_int($k)){
				unset($row[$k]);
			}
	
		}

		foreach($field as $k => $v){
			if(!isset($row[$v])){
				$row[$v]=0;
			}
		}

		$field_sql = implode("`,`",array_keys($row));
		$val_sql[] = "('".implode("','",$row)."')";
    }

	$sql = "INSERT INTO `".$new_table."` (`" . $field_sql . "`)  VALUES " . implode(',',$val_sql)." ;";

	if(!mysqli_query($conNew,$sql)){
			echo "\n\n\n".$sql."\n\n\n";exit;
	}
}

//同步 产品线 和 分类

$sql = "update base_style_info b,list_style_goods g
set g.product_type_id=b.product_type,g.cat_type_id=b.style_type
where b.style_id=g.style_id";

mysqli_query($conNew,$sql);


echo "\n\n\n\n完成ok！";
die();





