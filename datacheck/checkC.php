<?php
/**
 *  -------------------------------------------------
 *   @file		: checkC.php
 *   @link		:  www.kela.cn
 *   @copyright	: 2014-2024 kela Inc
 *   @author	: Laipiyang <462166282@qq.com>
 *   @date		: 2015/3/10
 *   @update	:
 *  -------------------------------------------------
 */
//header('Location:/index.php');exit;
error_reporting(E_ALL);//错误报告设置
ini_set('display_errors',1);//是否显示PHP错误信息，1显，0不显;
$act=isset($_GET['act']) ? $_GET['act']:"";
$white = array(
'WarehouseBillGoods',
'WarehouseBillPay',
'WarehouseBillInfoD',
'WarehouseBillInfoS',
'AppBespokeActionLog',
'AppPayApply',
'AppPayShould',
'AppPayReal',
'RelStyleStone',
'RelStyleFactory',
'ApiDemo',
'Login',
'Main',
'Static',
'PayApplyDetail',
'PayHexiaoDetail',
'AppXiangkou',
'AppOrderAddress'
);
if ($act=='getC')
{
	$mod = isset($_GET['mod']) ? $_GET['mod'] : '';
	$app_dir = dirname(__DIR__).'/apps/';
	$files = scandir($app_dir.$mod.'/control');
	foreach ($files as $key => $val )
	{
		if($val=='.' || $val=='..' || $val=='.svn' || $val=='CommonController.php'  || in_array(substr($val,0,-14),$white))
		{
			unset($files[$key]);
			continue;
		}
		echo substr($val,0,-14).'<br />';
	}
	exit;
}
else if ($act=='getC1')
{
	$mod = isset($_GET['mod']) ? $_GET['mod'] : '';
	$pdo = new PDO('mysql:host=192.168.0.251;port=3306;dbname=cuteframe','yangfuyou', 'yangfuyou1q2w3e', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
	$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$stmt = $pdo->query("SELECT id FROM `application` WHERE `code`='{$mod}'");
	$res = $stmt->fetch(PDO::FETCH_ASSOC);
	if($res)
	{
		$sql = "SELECT * FROM `control` WHERE `application_id`=".$res['id']." ORDER BY code";
		$stmt = $pdo->query($sql);
		$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
		foreach ($data as $val )
		{
			if($val['type']==1){
				echo '独立对象';
			}
			else if ($val['type']==2)
			{
				echo '主对象';
			}
			else if ($val['type']==3)
			{
				echo '明细对象';
			}
			echo $val['id'],'--',$val['code'].'--'.$val['label'].'<br />';
		}
	}
	exit;

}
else
{
	function getMod ()
	{
		$app_dir = dirname(__DIR__).'/apps/';
		$dirs = scandir($app_dir);
		foreach ($dirs as $key => $val )
		{
			if($val=='.' || $val=='..' || $val=='.svn')
			{
				unset($dirs[$key]);
				continue;
			}
			if(!is_dir($app_dir.$val))
			{
				unset($dirs[$key]);
			}
		}
		return $dirs;
	}
	$mods = getMod();
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="utf-8"/>
<title>控制器检查</title>
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript">
<!--
	function search()
	{

		var mod = $('select[name="mms"]').val();
		if (mod)
		{
			$('#aa').html(mod);
			$('#c1').load('checkC.php?act=getC&mod='+mod);
			$('#c2').load('checkC.php?act=getC1&mod='+mod);
		}
	}

//-->
</script>
</head>
<body>
<form method="post" action="#">
	模块：
	<select name="mms">
		<option value="" selected="selected">请选择</option>
		<?php
			foreach ($mods as $val )
			{
				echo '<option value="'.$val.'">'.$val.'</option>';
			}
		?>

	</select>&nbsp;&nbsp; <input type="button" value="查询" onclick="search();"/> <span id="aa"></span>
</form>
<div style="border:1px solid red;margin:0 0 20px 0;padding:5px;">
	<div style="width:50%;float:left;">
		<h2>实体控制器</h2>
		<div id="c1">

		</div>

	</div>
	<div style="width:50%;float:left;">
		<h2>注册控制器</h2>
		<div id="c2">

		</div>
	</div>
	<div style="clear:both;">

	</div>
</div>
<br />
<br />
<br />
</body>
</html>


<?php
 }
?>