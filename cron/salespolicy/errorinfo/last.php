<?php
header('Content-type: text/html; charset=utf-8');
define('ROOT_LOG_PATH',str_replace('goods_to_salepolicy_goods.php', '', str_replace('\\', '/', __FILE__))); 
$conn=mysqli_connect('192.168.1.59','cuteman','QW@W#RSS33#E#','front') or die("数据库链接失败");
$conn -> set_charset ("utf8" );

include('./include/page.class.php');
//分页的
$pageModel = new Page();
$pagenow = isset($_POST['page'])?(int)$_POST['page']:1;
$pagesize = 30;

//偏移量
$offset = $pageModel->offset($pagenow,$pagesize);
//定义所有分类

//第一步获取传过来的参数
//商品货号id：
$goods_id = isset($_POST['goods_id']) ? $_POST['goods_id'] : '';

//时间段  这里是日期格式如:  2015-07-23 18:08:00 如果不是的话 就进行转换下
$begintime = isset($_POST['begintime']) ? $_POST['begintime'] : '' ;
$endtime = isset($_POST['endtime']) ? $_POST['endtime'] : '' ;
if($endtime < $begintime )
{
	exit('<tr><td colspan="10" class="line">结束时间必须大于开始时间</td></tr>');
}
if(empty($begintime) || empty($endtime))
{
	exit('<tr><td colspan="10" class="line">请选择时间段</td></tr>');
}
//产品线：
$product_type = isset($_POST['product_type']) ? $_POST['product_type'] : '';
//发生动作
$action_name = isset($_POST['action_name']) ? $_POST['action_name'] : '';


//拼接sql
$sql = " select * from front.auto_run_goods where ";
if(isset($_POST['goods_id']) && $_POST['goods_id'] !='')
{
	$sql .= " goods_id = '".$_POST['goods_id']."' and ";
}
if(isset($_POST['product_type']) && $_POST['product_type'] !='')
{
	$sql .= " product_type = '".$_POST['product_type']."' and ";
}
if(isset($_POST['action_name']) && $_POST['action_name'] !='')
{
	$sql .= " action_name = '".$_POST['action_name']."' and ";
}
if(isset($_POST['begintime']) && $_POST['begintime'] !='')
{
	$sql .= "action_time >= '".$_POST['begintime']."' and ";
}
if(isset($_POST['endtime']) && $_POST['endtime'] !='')
{
	$sql .= "action_time <= '".$_POST['endtime']."' and ";
}
$sqlcount = $sql." 1 order by id asc ";
$sql .= "1 order by id asc limit $offset, $pagesize";

$result  = mysqli_query($conn,$sql);
$rows = $result->num_rows;


//计算所有的数量
$allresult = mysqli_query($conn,$sqlcount);
$allcount = $allresult->num_rows;


?>
<tr>
    <th>货号</th>
    <th>产品线</th>
    <th>款式分类</th>
    <th>状态</th>
    <th>所在仓库</th>
    <th>款号</th>
    <th>名称</th>
    <th>名义价</th>
    <th>金托类型</th>
    <th>主石</th>
    <th>主石粒数</th>
    <th>主石大小</th>
    <th>证书号</th>
    <th>是否绑定</th>
    <th>柜位</th>
    <th>单据类型</th>
    <th>单据编号</th>
    <th>发生动作</th>
    <th>发生时间</th>
</tr>
<?php
//第一步,查找所有的产品线的名称
$sql = "SELECT distinct product_type from auto_run_goods";
$data = mysqli_query($conn,$sql);
$row = $data->num_rows;
//定义一个变量$alltype来存储所有的产品线类型
$alltype = '';
if($row < 1 )
{
	$alltype['null'] = '暂时没有产品线'; 
}else{
	while($obj = mysqli_fetch_assoc($data))
	{
		$type = $obj['product_type'];
		$alltype[$type] = $type;
	}
}

if($rows<1)
{
	echo <<< HTML
	<tr>
		<td colspan="19" class="line">暂时没有找到数据哟</td>
	</tr>
HTML;
}else{
	while($obj = mysqli_fetch_assoc($result))
	{
		echo <<< HTML
	<tr>
		<td>{$obj['goods_id']}</td>
		<td>{$obj['product_type']}</td>
		<td>{$obj['cat_type']}</td>
		<td>{$obj['is_on_sale']}</td>
		<td>{$obj['warehouse']}</td>
		<td>{$obj['goods_sn']}</td>
		<td>{$obj['goods_name']}</td>
		<td>{$obj['mingyichengben']}</td>
		<td>{$obj['tuo_type']}</td>
		<td>{$obj['zhushi']}</td>
		<td>{$obj['zhushilishu']}</td>
		<td>{$obj['zuanshidaxiao']}</td>
		<td>{$obj['zhengshuhao']}</td>
		<td>{$obj['order_goods_id']}</td>
		<td>{$obj['box_sn']}</td>
		<td>{$obj['bill_type']}</td>
		<td>{$obj['bill_no']}</td>
		<td>{$obj['action_name']}</td>
		<td>{$obj['action_time']}</td>
	</tr>
HTML;
	}
	
	$pages = $pageModel->pages($pagesize,$allcount);
	
	echo <<< HTML
	<tr>
		<td colspan="19">
HTML;
	$pagelink = $pageModel->pagelink($pages,$pagenow);
	echo <<< HTML
		总数为:$allcount
		</td>
	</tr>
	<tr>
		<td colspan="19" class="line">
		<form action="view.php" method="post" enctype="multipart/form-data"  target="_blank">
			<input type="hidden" name="goods_id" value="$goods_id">
			<input type="hidden" name="product_type" value="$product_type">
			<input type="hidden" name="action_name" value="$action_name">
			<input type="hidden" name="begintime" value="$begintime">
			<input type="hidden" name="endtime" value="$endtime">
			<input type="submit" value="导出excel">
		</form>
		</td>
	</tr>
HTML;
}
?>