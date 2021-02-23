<?php
//定义公用的主石颜色数组
$dzcolor = array(
	'D-E'=>"'E','D-E','E'",
	'E-F'=>"'E','E-F','F'",
	'F-G'=>"'F','F-G','G'",
	'G-H'=>"'G','G-H','H','H+'",
	'H-I'=>"'H','H-I','I'",
	'I-J'=>"'I','I-J','J'",
	'J-K'=>"'J','J-K','K'",
	'K-L'=>"'K','K-L','L','白色','空'",
	'M'=>"'M','白色','K','K-L','L','空'"
);
//定义公用的主石净度
$dzjingdu = array(
	'FL'=>"'FL'",
	'IF'=>"'IF'",
	'VVS'=>"'VVS','VVS1','VVS2'",
	'VS'=>"'VS','VS1','VS2'",
	'SI'=>"'SI','SI1','SI2'",
	'I'=>"'I','I1','I2','P','P1','无','空','白色'",
	'P'=>"'P','P1','无','空','白色'"
);
/*仓库信息 */
$warehouse_arr=array(
	2=>'线上低值库',
	79=>'深圳珍珠库',
	184=>'黄金网络库',
	386=>'彩宝库',
	482=>'淘宝黄金',
	483=>'京东黄金',
	484=>'淘宝素金',
	485=>'京东素金',
	486=>'线上钻饰库',
	546=>'线上唯品会货品库',
	487=>'线上混合库',
	96=>'总公司后库',
	5=>'半成品库',
	342=>'黄金店面库',
	369=>'主站库',
	521=>'投资金条库',
	546=>'线上唯品会货品库',
);

//戒指指圈  +- 0.5#
//钻石大小  +0.03ct



/*
author: liulinyan
date  : 2015-00-16
@parames: smarkname 标签命名
@parames: remark    信息备注
@uesd: 你给我一个标签名和备注，我返回一个匹配好了的数组
@return array
*/

/*
eg:
	黄金饰品  和铂金
	$smarkname = '约1.2-2.1g;10#';
	$remark = '辛娟,KLRW00188,无';

	k金
	$smarkname = '黄色;10#';
	$remark = 'k,戒指,辛娟,KLRW00188,无';
	
	dz单钻
	$smarkname = '白 18K金 31分 F-G/SI';
	$remark = '辛娟,KLRW00188,无';

	//qz群钻
	$smarkname = '';
	//$remark = '辛娟,18K,白色,0.2ct,0.1ct/10p,I-J,SI,KLRW00288,无';
	$remark = '辛娟,10#,XJ,18K,白色,0.2ct,0.1ct/10p,I-J,SI,KLRW00288,无';
*/

/*  1111111
$title = ' PT950女款铂金包邮女士项链 铂金项链 满天星/b';
$smarkname = '戒指手寸:约：2.21-2.23g；18寸';
$remark = '没有备注';
***/

//

/**
$title = ' 白18K金钻石戒指一克拉钻戒结婚求婚显钻女戒水仙子/D';
$smarkname = '白 18K金 18分 I-J/SI';
$remark = '刘桥林,10#,lql,无,无';
**/


//先截取倒数第一位
$title = '白18K金钻石戒指一克拉钻戒结婚求婚显钻女戒水仙子D';
$type = substr($title,-1,1);
echo $type;


//$flag  		商品标题 $flag = $goodsinfo->title;
//$smarkname    SKU的值。$smarkname =$goodsinfo->sku_properties_name;如：机身颜色:黑色;手机套餐:官方标配
//$remark       卖家备注',KLBZ'拆分后的  $remark = $orderinfo->trade->seller_memo (',KLBZ'拆分)
function makeruledata($title,$smarkname,$remark)
{
	$alltype = array(
		'/h','/b','/hz','/hx','/bz/','/bx','/kz','/kx','/k','/d','/dz','/q','/qz','/p'
	);
	$where = array();
	//$where['name'] = $remark;
	//如果标签为空或者备注为空直接就返回了 我什么都不做了
	//if(empty($smarkname) || empty($remark))
	if(empty($remark))
	{
		$where['error'] = '备注没有填写';
		return $where;
	}
	$bz = str_replace('，',',',$remark);
	$bz = explode(',',$bz);
	
	$smarkname = str_replace('，',',',$smarkname);
	$remark = str_replace('，',',',$remark);
	
	$rul_title = strtolower(strrchr($title,'/'));
	
	//如果是黄金饰品,铂金 非戒指，非项链
	if($rul_title=='/h' || $rul_title=='/b')
	{
		$where = getdatatorule('h',$smarkname);
	}
	//如果是黄金饰品,铂金，戒指或者项链
	if($rul_title=='/hz' || $rul_title=='/bz')
	{
		$where = getdatatorule('hz',$smarkname);
	}
	if($rul_title=='/hx' || $rul_title == 'bx')
	{
		$where = getdatatorule('hx',$smarkname);
	}
	
	//如果是K金
	if($rul_title == '/k')
	{
		//颜色直接那标签的
		$where['yanse'] = $smarkname;
	}
	//暂时把戒指和项链写一起，因为项链的寸为长度 没有这个字段
	if($rul_title == '/kz' || $rul_title == '/kx')
	{
		$tmparr = explode(';',$smarkname);
		if(isset($tmparr[0]) && !empty($tmparr[0]))
		{
			$where['yanse'] = $tmparr[0];
		}
		if(isset($tmparr[1]) && !empty($tmparr[1]))
		{
			$where['shoucun']  = str_replace('#','',$tmparr[1]);
		}
	}
	//如果是单钻非戒指
	if($rul_title == '/d')
	{
		$where = dzrule($smarkname);
	}
	if($rul_title == '/dz')
	{
		$where = dzrule($smarkname,$remark);
	}
	//群钻戒指
	if($rul_title == '/qz')
	{
		$where = qzrule('/qz',$remark);
	}
	if($rul_title == '/q')
	{
		$where = qzrule('/q',$remark);
	}
	
	if($rul_title == '/p')
	{
		$where['error'] = '裸钻定制、彩钻、定金链接,不抓取';
	}
	if(!in_array($rul_title,$alltype))
	{
		$where['error'] = '商品标题不符合规则';
	}
	return $where;
}

//$where = makeruledata($smarkname,$remark);
//print_r($where);



//黄金饰品和铂金饰品的规则
function getdatatorule($flag,$smarkname)
{
	preg_match_all("/约(.*)/", $smarkname, $arr);
	if(empty($arr[1]))
	{
		$where['error'] = '标签命名不符合规范';
	}else
	{
		//print_r($arr);
		$data = $arr[1][0];
		$data = str_replace('；',';',$data);
		$data = explode(';',$data);
		$gold = str_replace('g','',$data[0]);
		$gold = explode('-',$gold);
		if(isset($gold[0]))
		{
			$where['gold_weight_min'] = str_replace('：','',$gold[0]);
			$where['gold_weight_max'] = $gold[0];
		}
		if(isset($gold[1]))
		{
			$where['gold_max'] = $gold[1];
		}
		if(isset($data[1]) && !empty($data[1]))
		{
			//如果是戒指
			if($flag == 'hz')
			{
				$where['shoucun'] = str_replace('#','',$data[1]);
			}
			//如果是项链
			if($flag == 'hx')
			{
				//寸 目前没有啊
				$where['shoucun'] = str_replace('寸','',$data[1]);
			}
		}		
	}
	return $where;
}

//单钻的规则
function dzrule($smarkname,$remark='')
{
	global $dzcolor;
	global $dzjingdu;
	//如果是单钻
	$mark = explode(' ',$smarkname);
	$where = array();
	if(count($mark) != 4)
	{
		$where['error'] = '兄弟 你的单钻标签命名没有按规则走啊';
	}else{
		$caizhi = $mark[1].$mark[0];
		$where['caizhi'] = str_replace('金','',$caizhi);
		$where['jinzhong'] = str_replace('分','',$mark[2]);
		//颜色和净度
		$tmpcolor = explode('/',$mark[3]);
		if(isset($tmpcolor[0]) && !empty($tmpcolor[0]))
		{
			$where['zhushiyanse'] = $dzcolor[$tmpcolor[0]];
		}else{
			$where['error'] = '颜色没有按照规则写';
		}
		
		
		if(isset($tmpcolor[1]) && !empty($tmpcolor[1]))
		{
			$where['zhushijingdu'] = $dzjingdu[$tmpcolor[1]];
		}
		else{
			$where['error'] = '净度没有按照规则写';	
		}
		//如果传了备注 那就是戒指了 目的是为了获取指圈
		if(!empty($remark))
		{
			$tmpdata = explode(',',$remark);
			if(isset($tmpdata[1]) && !empty($tmpdata[1]))
			{
				$where['shouchun'] = str_replace('#','',$tmpdata[1]);
			}
		}	
	}
	return $where;
}

//群钻规则
function qzrule($flag,$remark)
{
	global $dzcolor;
	global $dzjingdu;
	$where = array();
	$arr = explode(',',$remark);
	if($flag == '/qz')
	{
		if(count($arr) != 11)
		{
			$where['error'] = '群钻戒指订单备注没有按照规则走';
		}else{
			$where['shoucun'] = str_replace('#','',$arr[1]);
			$where['ziyin'] = $arr[2];  //刻字内容   表字段字印
			$where['caizhi'] = $arr[3];
			$where['yanse'] = $arr[4];  //材质颜色
			$where['zuanshidaxiao'] = str_replace('ct','',$arr[5]) ;  //主石大小   
			//副石大小没找到啊
			//$where['fushizhong'] = $bz[8];   //那个p是什么东西
			if(isset($dzcolor[$arr[7]]))
			{
				$where['zhushiyanse'] = $dzcolor[$arr[7]];
			}else{
				$where['error'] = '颜色没有按照规则写';
			}
			
			if(isset($dzcolor[$arr[8]]))
			{
				$where['zhushijingdu'] = $dzjingdu[$arr[8]];
			}else{
				$where['error'] = '净度没有按照规则写';
			}
		}
	}
	
	if($flag == '/q')
	{
		if(count($arr) != 9)
		{
			$where['error'] = '群钻非戒指订单备注没有按照规则走';
		}else{
			//这里是群钻非戒指
			$where['caizhi'] = $arr[1];
			$where['yanse'] = $arr[2];  //材质颜色
			$where['zuanshidaxiao'] = str_replace('ct','',$arr[3]) ;  //主石大小   
			//副石大小没找到啊
			//$where['fushizhong'] = $bz[6];   //那个p是什么东西			
			if(isset($dzcolor[$arr[5]]))
			{
				$where['zhushiyanse'] = $dzcolor[$arr[5]];
			}else{
				$where['error'] = '颜色没有按照规则写';
			}
			
			if(isset($dzcolor[$arr[6]]))
			{
				$where['zhushijingdu'] = $dzjingdu[$arr[6]];
			}else{
				$where['error'] = '净度没有按照规则写';
			}			
		}
	}
	return $where;
}

//规则测试
$ruilearr = array(
//黄金饰品
	'/hz'=>array('flag'=>'约2.1-2.2g;10#','beizhu'=>'辛娟，KLRW00188，无'),
	//没有指圈号的
	//'/hz'=>array('flag'=>'约2.1g','beizhu'=>'辛娟，KLRW00188，无'),
	'/hx'=>array('flag'=>'约2.1-2.2g;16寸','beizhu'=>'辛娟，KLRW00188，无'),
	'/hx'=>array('flag'=>'约2.1g;16寸','beizhu'=>'辛娟，KLRW00188，无'),
	'/h'=>array('flag'=>'约2.1-2.2g','beizhu'=>'辛娟，KLRW00188，无'),
	
//铂金
	'/bz'=>array('flag'=>'约2.1-2.2g;10#','beizhu'=>'辛娟，KLRW00188，无'),
	//没有指圈号的
	'/bz'=>array('flag'=>'约2.1g','beizhu'=>'辛娟，KLRW00188，无'),
	'/bx'=>array('flag'=>'约2.1-2.2g;16寸','beizhu'=>'辛娟，KLRW00188，无'),
	'/bx'=>array('flag'=>'约2.1g;16寸','beizhu'=>'辛娟，KLRW00188，无'),
	'/b'=>array('flag'=>'约2.1-2.2g','beizhu'=>'辛娟，KLRW00188，无'),
//K金
	'/kz'=>array('flag'=>'黄色;10#','beizhu'=>'辛娟，KLRW00188，无'),
	//没有指圈号的
	'/kx'=>array('flag'=>'黄色;16寸','beizhu'=>'辛娟，KLRW00188，无'),
	'/kx'=>array('flag'=>'黄色','beizhu'=>'辛娟，KLRW00188，无'),
	'/k'=>array('flag'=>'黄色','beizhu'=>'辛娟，KLRW00188，无'),

//单钻
	'/dz'=>array(
		'flag'=>'金色 18k金 20分 F-G/SI',
		'beizhu'=>'辛娟,10#,XJ,KLRW00188,无'
	),
	'/d'=>array(
		'flag'=>'金色 18k金 20分 F-G/SI',
		'beizhu'=>'辛娟,KLRW00188,无'
	),
//群钻
	'/qz'=>array(
		'flag'=>'',
		'beizhu'=>'辛娟,10#,XJ,18K,白色,0.2ct,0.1ct/10p,I-J,SI,KLRW00288,无',
	),
	'/q'=>array(
		'flag'=>'',
		'beizhu'=>'辛娟,18K，白色,0.2ct,0.1ct/10p,I-J,SI,KLRW00288,无'
	)
);

/*
foreach($ruilearr as $k=>$v)
{
	echo $k.'**';
	if($k == '/bx')
	{
		$k = '/hx';
	}
	if($k == '/bz')
	{
		$k = '/hz';
	}
	$where = makeruledata($k,$v['flag'],$v['beizhu']);
	print_r($where);
	echo '<br/>';
}
*/


?>