<?php
include('../lib/PdoModel.php');
include('../lib/OrderClassModel.php');
$orderclass = new OrderClassModel();
/*默认为查看当天*/
$btime = date('Y-m-d');
$etime = date('Y-m-d');
$where = array(
	'btime'=>$btime,
	'etime'=>$etime,
	'isyushou'=>0
);
$showtitle = '常规订单';
$_submit = isset($_REQUEST['timesearch'])?$_REQUEST['timesearch']:'无';
if($_submit == '查看')
{
	$begintime = $_REQUEST['begintime'];
	$endtime = $_REQUEST['endtime'];
	$yushou = $_REQUEST['isyushou'];
	if(!empty($begintime))
	{
		$where['btime'] = $begintime;
	}
	if(!empty($endtime))
	{
		$where['etime'] = $endtime;
	}
	if($endtime < $begintime)
	{
		$where['etime'] = $endtime;
	}
	$where['isyushou'] = $yushou;
}

if($where['isyushou']>0)
{
	$showtitle = '预售订单';
}

$falsedata = json_encode($where);
$falsedata = base64_encode($falsedata);


$tongjinum = $orderclass->countorder($where);
//现货统计
$huopingnum = $orderclass->countxianhuo($where);
$moneydata = $orderclass->countmoney($where);

//发货统计
$fhnum = $orderclass->countfh($where);


$okdata = json_encode($where);
$okdata = base64_encode($okdata);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
<style type="text/css">
	.page{margin:0 auto; width:800px; height:600px; border:1px solid #CCC;}
	.line{
		width:800px;
		height:30px;
		float:left;
		line-height:20px;
		font-size:16px;
		text-align:center;
		display:block;
		background-color:#6CF;
	}
	.box{width:300px; height:200px;line-height:40px; margin:20px 30px 10px 30px; padding:20px; }
	.green{background-color:#39F; float:left;color:#fff;font-weight:bold; }
	.red{background-color:#F00; float:right;}
	.gray{background-color:#CCC; float:left;}
	.yellow{background-color:#FF0; float:right;}
	a{color:#FFF; float:right;}
</style>
<script type="text/javascript" src="../js/jquery-1.10.2.js"></script>
<script type="text/javascript" src="My97DatePicker/WdatePicker.js"></script>
</head>

<body>
<div class="page">
 
	<div class="line">《双十一自动抓单统计》
	<?php 
		$timeshow = $where['btime'].'&nbsp;到&nbsp;'.$where['etime'];
		echo $timeshow.$showtitle;
	?>订单统计
    </div>
    <div class="line">
    	<form action="./showtongji.php" method="post" enctype="multipart/form-data">
        订单类型:
        <select name="isyushou">
        	<option value="0">常规订单</option>
            <option value="1">预售订单</option>
        </select>&nbsp;&nbsp;
    	时间：<input type="text" class="Wdate" name="begintime" 
        onFocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" />&nbsp;&nbsp;to&nbsp;&nbsp;
  		<input type="text" class="Wdate" name="endtime" 
        onFocus="WdatePicker({lang:'zh-cn',dateFmt:'yyyy-MM-dd'})" />&nbsp;&nbsp;
        <input type="submit" value="查看" name="timesearch" />
        </form>
    </div>
        <div class="box gray">
            定制单数：<?php echo $huopingnum['qihuo'];?>
        </div>
        <div class="box yellow">
            现货单数：<?php echo $huopingnum['xianhuo'];?><br/>
            已发货:<?php echo $fhnum['yfh'] ;?><br/>
            未发货:<?php echo $fhnum['wfh'] ;?>
        </div>
        <div class="box green">
            抓单成功：<?php echo $tongjinum['ok'];?>
            <br/>
            订单总额：<?php echo $moneydata['total'];?>元
            <br/>
            已点款额：<?php echo $moneydata['paid'];?>元
            <br/>
            未点款额：<?php echo $moneydata['unpaid'];?>元
            <br/>
            <a href="./oklist.php?timesearch=showtongji&data=<?php echo $okdata;?>" target="_blank">
            	查看详情
            </a>
        </div>
        <div class="box red">
            抓单失败：<?php echo $tongjinum['false'];?>
            		<br/>不是等待卖家发货的有：
                    <?php 
						//不是等待卖家发货的有
						$where['reason'] = '不是等待卖家';
						$nodata = $orderclass->getfalsedata($where);
						echo $nodata->num_rows;
					?>
                    <br/>市场推广的订单总共有:
                    <?php 
						//不是等待卖家发货的有
						$where['reason'] = '市场推广';
						$data = $orderclass->getfalsedata($where);
						echo $data->num_rows;
					?>
                    <br/>规则不用抓取的总共有:
                    <?php 
						//不需要抓取的有
						$where['reason'] = '定金链接';
						$data = $orderclass->getfalsedata($where);
						echo $data->num_rows;
					?>
                    <br />
					<a href="./falselist.php?timesearch=showtongji&data=<?php echo $falsedata; ?>" target="_blank">
                    查看详情
                    </a>
	</div>
</div>
</body>
</html>