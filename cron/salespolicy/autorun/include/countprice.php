<?php
function GetBaoxianFei($conn,$xiangkou)
{
  //拿取保险费
  $xiangkou = $xiangkou * 10000;
  $i = 0;
  $j = 0;
  $k = 0;
  $sql = 'SELECT `id`,`min`,`max`,`price`,`status` FROM `app_style_baoxianfee` WHERE 1';
  $baoxianfei = mysqli_query($conn,$sql);
  while($v = mysqli_fetch_assoc($baoxianfei))
  {
	 $max[$i] = $v['max'] * 10000;
	 $min[$j] = $v['min'] * 10000;
	 $fee[$k] = $v['price'];
	 $i++;$j++;$k++; 
  }
  $count = count($max);
  for($i = 0; $i <$count; $i ++) 
  {
	if ($xiangkou >= $min[$i] && $xiangkou <= $max[$i])
	{
		return $fee[$i];
	}
  }
}


//获取保险费的值 金托类型和主石大小镶口值
function getbxfinfo($data,$conn)
{
	//保险费
	$baoxianfei = '';
	if(!empty($data))
	{
		if($data['tuo_type']>1)
		{
			//托类型
			//获取镶口
			$xiankou = $data['jietuoxiangkou'];
			if(!empty($xiankou) && $xiankou > 0)
			{
				$getbxf_data = $xiankou;
			}else{
				$getbxf_data = $data['zuanshidaxiao'];
			}
			$baoxianfei = GetBaoxianFei($conn,$getbxf_data);
		}else{
			$baoxianfei = 0;	
		}
	}
	return $baoxianfei;
}
?>