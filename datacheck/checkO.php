<?php
/**
 *  -------------------------------------------------
 *   @file		: checkO.php
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
	echo "<option>请选择</option>";
	$app_dir = dirname(__DIR__).'/apps/';
	$files = scandir($app_dir.$mod.'/control');
	foreach ($files as $key => $val )
	{
		if($val=='.' || $val=='..' || $val=='.svn' || in_array(substr($val,0,-14),$white))
		{
			unset($files[$key]);
			continue;
		}
		$option = substr($val,0,-14);
		echo "<option value='$option'>$option</option>";
	}
	exit;
}
else if($act=='getO')
{
	$app_dir = '../apps/';
	$mod = isset($_GET['mod']) ? $_GET['mod'] : '';
	$files = scandir($app_dir.$mod.'/control');
	foreach ($files as $key => $val )
	{
		if($val=='.' || $val=='..' || $val=='.svn' || $val=='CommonController.php' || in_array(substr($val,0,-14),$white))
		{
			unset($files[$key]);
			continue;
		}
		$data[] = substr($val,0,-14);
	}
	require '../frame/class/Controller.class.php';
	if(is_file($app_dir.$mod.'/control/CommonController.php'))
	{
		require_once $app_dir.$mod.'/control/CommonController.php';
	}
	//$datas = array();
	foreach ($data as $val )
	{
		$file = $app_dir.$mod.'/control/'.$val.'Controller.php';
		if(is_file($file))
		{
			require_once $file;
			$class_name = $val.'Controller';
			if(class_exists('CommonController'))
			{
				$_cls = 'CommonController';
			}
			else
			{
				$_cls = 'Controller';
			}
			$class = @array_diff(get_class_methods($class_name),get_class_methods($_cls));
			if(is_array($class))
			{
				if(array_search('index',$class)===false)
				{
					array_unshift($class,'index');
				}
				//$datas[$val] = $class;
				echo "<b>$val</b><br />";
				foreach ($class as $v )
				{
					echo "&nbsp;&nbsp;".$v."<br />";
				}
			}

		}
	}
}
else if($act=='getO1')
{
	$mod = isset($_GET['mod']) ? $_GET['mod'] : '';

	$pdo = new PDO('mysql:host=192.168.0.251;port=3306;dbname=cuteframe','yangfuyou', 'yangfuyou1q2w3e', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';"));
	$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$stmt = $pdo->query("SELECT id FROM `application` WHERE `code`='{$mod}'");
	$res = $stmt->fetch(PDO::FETCH_ASSOC);
	if($res)
	{
		$sql = "SELECT id,code FROM `control` WHERE `application_id`=".$res['id']." ORDER BY code";
		$stmt = $pdo->query($sql);
		$id = $stmt->fetchAll(PDO::FETCH_ASSOC);
		if($id)
		{
			$ids = array_column($id,'id');
			$_ids = array_column($id,'code');
			$datas = array_combine($ids,$_ids);

			$sql = "SELECT id,method_name,c_id FROM `operation` WHERE `c_id` IN (".implode(",",$ids).")";
			$stmt = $pdo->query($sql);
			$res = $stmt->fetchAll(PDO::FETCH_ASSOC);
			foreach ($res as $val )
			{
				$data[$datas[$val['c_id']]][] = $val['id'].'--'.$val['method_name'];
			}
			foreach ($data as $key => $val )
			{
				echo "<b>$key</b><br />";
				foreach ($val as $v )
				{
					echo "&nbsp;&nbsp;".$v."<br />";
				}
			}

		}
		else
		{
			echo '控制器不存在或没有正确关联项目';
		}
		exit;
	}
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
		var m = $('select[name="mms"]').val();
		if (m)
		{
			$('#aa').html(m);
			$('#c1').load('checkO.php?act=getO&mod='+m);
			$('#c2').load('checkO.php?act=getO1&mod='+m);
		}
	}

//-->
</script>
</head>
<body>
使用本文件之前要确定control是正确的 <br />
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

	</select>
	&nbsp;&nbsp; <input type="button" value="查询" onclick="search();"/> <span id="aa"></span>
</form>
<div style="border:1px solid red;margin:0 0 20px 0;padding:5px;">
	<div style="width:50%;float:left;">
		<h2>实体操作</h2>
		<div id="c1">

		</div>

	</div>
	<div style="width:50%;float:left;">
		<h2>注册操作</h2>
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