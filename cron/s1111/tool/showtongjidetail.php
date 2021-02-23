<?php
include('../lib/PdoModel.php');
include('../lib/OrderClassModel.php');
$orderclass = new OrderClassModel();

$time['btime'] = isset($_REQUEST['btime']) ? $_REQUEST['btime']:date('Y-m-d');
$time['etime'] = isset($_REQUEST['etime']) ? $_REQUEST['etime']:date('Y-m-d');


//标题出错的
$title_where = " reason like '%标题%' ";
$titledata = $orderclass->getfalsedata($title_where,1,$time);

//标签出错的
$bq_where = "reason like '%标签%' ";
$bqdata = $orderclass->getfalsedata($bq_where,0,$time);

//备注出错的
$bz_where = "reason like '%备注%' ";
$bzdata = $orderclass->getfalsedata($bz_where,1,$time);

//市场推广的
$tg_where = "reason like '%市场推广%' ";
$tgdata = $orderclass->getfalsedata($tg_where,0,$time);


//退款的
$tk_where = "reason like '%退款%' ";
$tkdata = $orderclass->getfalsedata($tk_where,0,$time);

//不用抓取的订单有
$noget_where = "reason like '%定金链接%' ";
$nogetdata = $orderclass->getfalsedata($noget_where,0,$time);

//淘宝关闭的订单有
$taob_where = "order_status='TRADE_CLOSED_BY_TAOBAO' ";
$taobdata = $orderclass->getfalsedata($taob_where,0,$time);


//不是等待卖家发货的有
$no_where = " reason like '%不是等待卖家%' ";
$nodata = $orderclass->getfalsedata($no_where,0,$time);

//找不到赠品回滚的有
$zp_where = " reason like '%赠品%' ";
$zpdata = $orderclass->getfalsedata($zp_where,0,$time);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>自动抓单失败列表</title>
<style type="text/css">
	.page{margin:0 auto; width:800px; height:auto; border:none;}
	.line{
		width:800px;
		height:auto;
		float:left;
		line-height:30px;
		text-align:center;
		display:block;
		background-color:#F00;
	}
	

	.box{width:758px; height:auto;line-height:30px;padding:20px; font-size:14px; color:#099; border:1px solid #F00;}
	.green{background-color:#FFF; float:left;font-weight:bold; }
	.red{background-color:#FFF; float:right;}
	.gray{background-color:#FFF; float:left;}
	.yellow{background-color:#FF0; float:right;}
	
	a{color:#6CF;}
		
	.titleerror{float:left; width:100%; display:none;}
	.biaoqianerror{float:left; width:100%;display:none;}
	.bzerror{float:left; width:100%; display:none;}
	.tgerror{float:left; width:100%; display:none;}
	.tkerror{float:left; width:100%; display:none;}
	.nogeterror{float:left; width:100%; display:none;}
	.noerror{float:left; width:100%; display:none;}
	.zperror{float:left;width:100%; display:none;}
</style>
<script type="text/javascript" src="../js/jquery-1.10.2.js"></script>
<script type="text/javascript">
	function showtitle()
	{
		$("#titleerror").css('display','block');
		$("#biaoqianerror").css('display','none');
		$("#bzerror").css('display','none');
		$("#tgerror").css('display','none');
		$("#tkerror").css('display','none');
		$("#nogeterror").css('display','none');
		$("#noerror").css('display','none');
		$("#zperror").css('display','none');
	}
	function showbiaoqian()
	{
		$("#biaoqianerror").css('display','block');
		$("#titleerror").css('display','none');
		$("#bzerror").css('display','none');
		$("#tkerror").css('display','none');
		$("#tgerror").css('display','none');
		$("#nogeterror").css('display','none');
		$("#noerror").css('display','none');
		$("#zperror").css('display','none');
	}
	function showbz()
	{
		$("#bzerror").css('display','block');
		$("#biaoqianerror").css('display','none');
		$("#titleerror").css('display','none');
		$("#tgerror").css('display','none');
		$("#tkerror").css('display','none');
		$("#nogeterror").css('display','none');
		$("#noerror").css('display','none');
		$("#zperror").css('display','none');
		
	}
	function showtg()
	{
		$("#tgerror").css('display','block');
		$("#biaoqianerror").css('display','none');
		$("#titleerror").css('display','none');
		$("#bzerror").css('display','none');
		$("#tkerror").css('display','none');
		$("#nogeterror").css('display','none');
		$("#noerror").css('display','none');
		$("#zperror").css('display','none');
	}
	
	function showtk()
	{
		$("#tkerror").css('display','block');
		$("#biaoqianerror").css('display','none');
		$("#titleerror").css('display','none');
		$("#bzerror").css('display','none');
		$("#tgerror").css('display','none');
		$("#nogeterror").css('display','none');
		$("#noerror").css('display','none');
		$("#zperror").css('display','none');
	}
	function shownoget()
	{
		$("#nogeterror").css('display','block');
		$("#biaoqianerror").css('display','none');
		$("#titleerror").css('display','none');
		$("#bzerror").css('display','none');
		$("#tgerror").css('display','none');
		$("#tkerror").css('display','none');
		$("#noerror").css('display','none');
		$("#zperror").css('display','none');
	}
	function showno()
	{
		$("#tkerror").css('display','none');
		$("#biaoqianerror").css('display','none');
		$("#titleerror").css('display','none');
		$("#bzerror").css('display','none');
		$("#tgerror").css('display','none');
		$("#nogeterror").css('display','none');
		$("#noerror").css('display','block');
		$("#zperror").css('display','none');
	}
	function show(zp)
	{
		$("#tkerror").css('display','none');
		$("#biaoqianerror").css('display','none');
		$("#titleerror").css('display','none');
		$("#bzerror").css('display','none');
		$("#tgerror").css('display','none');
		$("#nogeterror").css('display','none');
		$("#noerror").css('display','none');
		$("#zperror").css('display','none');
		$("#"+zp).css('display','block');
	}
	
</script>
</head>

<body>
<?php

?>
<div class="page">
	<div class="line">自动抓单失败列表信息<?php echo $time['btime'].'to'.$time['etime'];?></div>
        <div class="box green">
        	因为标题错误导致失败总共有：<?php echo $titledata->num_rows;?>
            <a href="javascript:;" onclick="showtitle()">点击查看</a><br/>
            <div class="titleerror" id="titleerror">
			<?php 
				if($titledata && $titledata->num_rows > 0 )
				{
					while($obj = $titledata->fetch_assoc())
					{
						echo '<a href="../tool/looktaobaoinfo.php?orderid=';
						echo $obj['out_order_sn'];
						echo '" target="_blank">';
						echo $obj['reason'].'</a><br/>';
					}
				}
			?>
            </div>
        </div>
        <div class="box gray">
        	因为标签错误导致失败总共有：<?php echo $bqdata->num_rows;?>
            <a href="javascript:;" onclick="showbiaoqian()">点击查看</a><br/>
            <div class="biaoqianerror" id="biaoqianerror">
			<?php 
				if($bqdata && $bqdata->num_rows > 0 )
				{
					while($obj = $bqdata->fetch_assoc())
					{
						echo '<a href="../tool/looktaobaoinfo.php?orderid=';
						echo $obj['out_order_sn'];
						echo '" target="_blank">';
						echo $obj['reason'].'</a><br/>';
					}
				}
			?>
            </div>
        </div>
        <div class="box red">
           因为备注信息错误导致失败的总共有：<?php echo $bzdata->num_rows;?>
           <a href="javascript:;" onclick="showbz()">点击查看</a><br/>
            <div class="bzerror" id="bzerror">
			<?php 
				if($bzdata && $bzdata->num_rows > 0 )
				{
					while($obj = $bzdata->fetch_assoc())
					{
						echo '<a href="../tool/looktaobaoinfo.php?orderid=';
						echo $obj['out_order_sn'];
						echo '" target="_blank">';
						echo $obj['reason'].'</a><br/>';
					}
				}
			?>
            </div>
        </div>
        
        <div class="box gray">
        	因为订单中有商品申请了退款导致失败总共有：<?php echo $tkdata->num_rows;?>
            <a href="javascript:;" onclick="showtk()">点击查看</a><br/>
            <div class="tkerror" id="tkerror">
			<?php 
				if($tkdata && $tkdata->num_rows > 0 )
				{
					while($obj = $tkdata->fetch_assoc())
					{
						echo '<a href="../tool/looktaobaoinfo.php?orderid=';
						echo $obj['out_order_sn'];
						echo '" target="_blank">';
						echo $obj['reason'].'</a><br/>';
					}
				}
			?>
            </div>
        </div>
        <div class="box green">
        	标签不需要抓取的总共有：<?php echo $nogetdata->num_rows;?>
            <a href="javascript:;" onclick="shownoget()">点击查看</a><br/>
            <div class="nogeterror" id="nogeterror">
			<?php 
				if($nogetdata && $nogetdata->num_rows > 0 )
				{
					while($obj = $nogetdata->fetch_assoc())
					{
						echo '<a href="../tool/looktaobaoinfo.php?orderid=';
						echo $obj['out_order_sn'];
						echo '" target="_blank">';
						echo $obj['reason'].'</a><br/>';
					}
				}
			?>
            </div>
        </div>
        <div class="box yellow">
        	市场推广的订单不需要抓取的总共有：<?php echo $tgdata->num_rows;?>
           <a href="javascript:;" onclick="showtg()">点击查看</a><br/>
            <div class="tgerror" id="tgerror">
			<?php 
				if($tgdata && $tgdata->num_rows > 0 )
				{
					while($obj = $tgdata->fetch_assoc())
					{
						
						echo '<a href="../tool/looktaobaoinfo.php?orderid=';
						echo $obj['out_order_sn'];
						echo '" target="_blank">';
						echo $obj['reason'].'</a><br/>';
					}
				}
			?>
            </div>
        </div>
        <div class="box gray">
        	不是等待卖家发货的订单总共有：<?php echo $nodata->num_rows;?>
            <a href="javascript:;" onclick="showno()">点击查看</a><br/>
            <div class="noerror" id="noerror">
			<?php 
				if($nodata && $nodata->num_rows > 0 )
				{
					while($obj = $nodata->fetch_assoc())
					{
						echo '<a href="../tool/looktaobaoinfo.php?orderid=';
						echo $obj['out_order_sn'];
						echo '" target="_blank">';
						echo $obj['reason'].'</a><br/>';
					}
				}
			?>
            </div>
        </div>
        <div class="box gray">
        	赠品出问题的单总共有：<?php echo $zpdata->num_rows;?>
            <a href="javascript:;" onclick="show('zperror')">点击查看</a><br/>
            <div class="zperror" id="zperror">
			<?php 
				if($zpdata && $zpdata->num_rows > 0 )
				{
					while($obj = $zpdata->fetch_assoc())
					{
						$obj['reason'] = strip_tags($obj['reason']);
						$obj['reason'] = strip_tags($obj['reason']);
						echo '<a href="../tool/looktaobaoinfo.php?orderid=';
						echo $obj['out_order_sn'];
						echo '" target="_blank">';
						echo $obj['reason'].'</a><br/>';
					}
				}
			?>
            </div>
        </div>
</div>
</body>
</html>