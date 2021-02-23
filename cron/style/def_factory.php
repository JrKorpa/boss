<?php
header("Content-type:text/html;charset=utf8;");
set_time_limit(0);
error_reporting(E_ALL);
$act = isset($_REQUEST['act']) ? $_REQUEST['act'] : 'select';
$style_sn = isset($_REQUEST['style_sn']) ? $_REQUEST['style_sn'] : '';
$limit =  isset($_REQUEST['limit']) ? $_REQUEST['limit'] : 20;
$offset = isset($_REQUEST['offset']) ? $_REQUEST['offset'] : 0;
def();
switch ($act){
	case 'update':
		update();
		break;
	case 'select':
	default:
		select();
		break;
		
}
//====================Search Function==================================
function def(){
	global $act,$style_sn;
	if($act == 'select') $label = '查询';
	else if($act == 'update') $label = '洗数据';
	
	echo <<<HTML
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<p>
分页提示，在url的querystring中加入：
<li>offset: 表示从第几个开始</li>
<li>limit：表示查询多少个</li>
如：查询从第一个开始的10条数据，offset=1&limit=10
<br/>
在url上加上act=update即执行数据清洗操作
</p>
		<form method="post">
		<center><div style="padding-top:100px;font-size:24px;">
款号：<input type="input" name="style_sn" value="$style_sn">
<input type="submit"  value="$label">
		</div></center>
</form>

<center><div style="width:90%">
</div></center>
</body>
</html>
HTML;
}

function select(){
	global $style_sn,$limit, $offset;
	$sql = 'SELECT f.* from rel_style_factory f inner JOIN(
	select style_sn, factory_id from rel_style_factory where is_cancel = 1 and is_factory = 1 and is_def = 1
) g on g.style_sn = f.style_sn and g.factory_id = f.factory_id
where f.is_cancel = 1 and f.is_factory = 0 ';
	if (!empty($style_sn)) {
		$sql .= " and f.style_sn = '{$style_sn}'";
	}
	$sql .= " limit {$offset},{$limit}";
	$db1 = mysqli_connect('203.130.44.199', 'cuteman', 'QW@W#RSS33#E#', 'front');
	$arr = mysqli_query($db1, $sql);
	$data= array();
	while($w=mysqli_fetch_assoc($arr)){
		$data[] = $w;
	}
	make($data,__FUNCTION__);
}

function update() {
	global $style_sn;
	$sql = "update rel_style_factory f inner JOIN(
	select style_sn, factory_id from rel_style_factory where is_cancel = 1 and is_factory = 1 and is_def = 1
) g on g.style_sn = f.style_sn and g.factory_id = f.factory_id
set f.is_factory = 1
where f.is_cancel = 1 and f.is_factory = 0 and f.style_sn not in ('KLRW028185','KLNW028270','W121_001','W1985','KLRW022614')";
	if (!empty($style_sn)) {
		$sql .= " and f.style_sn = '{$style_sn}'";
	}
	
	$db1 = mysqli_connect('203.130.44.199', 'cuteman', 'QW@W#RSS33#E#', 'front');
	$resp = mysqli_query($db1, $sql);
	var_dump($resp);
}


function make($data,$name='file'){

echo "<table border=1 cellspacing=1 cellpadding=1>";
foreach($data as $k => $v){
	if($k == 0){
		echo "<tr>";
		foreach($v as $kk => $vv){
			echo "<td align=right>".$kk."</td>";
		}
		echo "</tr>";
	}
	echo "<tr>";
	foreach($v as $kk => $vv){
		echo "<td align=right>".$vv."</td>";
	}
	echo "</tr>";
}
echo "</table>";
}











