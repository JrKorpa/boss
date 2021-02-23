<?php
/*
$title = '白18K金钻石戒指一克拉钻戒结婚求婚显钻女戒水仙子D B C ';
$title = str_replace(' ','',trim($title));
$type = strtolower(substr($title,-1));
*/

//定义公用的主石颜色数组
$dzcolor = array(
	'白色'=>"'白色',''",
	'DE'=>"'D','E','D-E'",
	'FG'=>"'F','F-G','G'",
	'H'=>"'H'",
	'IJ'=>"'I','I-J','J'",
	'KL'=>"'K','K-L','L','白色',''"
);
//定义公用的主石净度
$dzjingdu = array(
	'FL'=>"'FL'",
	'IF'=>"'IF'",
	'VVS'=>"'VVS','VVS1','VVS2'",
	'VS'=>"'VS','VS1','VS2'",
	'SI'=>"'SI','SI1','SI2'",
	'I'=>"'I','I1','I2','P','P1','白色','不分级'",
	'P'=>"'I','I1','I2','P','P2','白色','不分级'",
	'不分级'=>"'I','I1','I2','P','P1','','不分级'",
	'N'=>"'I','I1','I2','P','P1','','不分级'"
);
function makeruledata($title,$smarkname,$remark)
{
	global $dzcolor;
	global $dzjingdu;	
	//去掉所有标签最后一个竖线后面的信息
	$smarkname = str_replace(strrchr($smarkname,'|'),'',$smarkname);
	//$smarkname = str_replace(strrchr($smarkname,'丨'),'',$smarkname);
	//转换中文分号为英文
	$smarkname = str_replace('；',';',$smarkname);
	//去掉标签里面的规则:这些	
	//$smarkname = str_replace('戒指手寸:','',$smarkname);
	
	$where = array();
	
	//定义单字母数组
	$array1 = array('h','b','k','d');
	//定义双字母数组
	$array2 = array(
		'hz','hx','hd','hy','hb','hj',
		'bz','bx','bd','br',
		'kz','kx','kd','ke',
		'dz','dd','de','dt');

	$newtitle = str_replace(' ','',trim($title));
	$type = strtolower(substr($newtitle,-1));
	$type2 = strtolower(substr($newtitle,-2));
	
	if(!in_array($type,$array1) && !in_array($type2,$array2))
	{
		$where['error'] = '标题结尾:'.$title.'不符合抓单规则';
		return $where;
	}
	
	if(empty($remark)||empty($smarkname))
	{
		$where['error'] = '货品备注或者货品SKU没有填写';
		return $where;
	}

    $except = array('kn','dk','dn'); //多个款号的K金项链  有多个款号的产品（除吊坠） 有多个款号的单钻吊坠 
    if(in_array($type2,$except)){
		$where['error'] = '多个款号的商品不抓取,手动录单';
		return $where;    	
    }

	
	$smarkname = str_replace('，',',',$smarkname);	
	$remark = str_replace('，',',',$remark);
	
	if($type2<>'dz'){
	    $data = explode(',',$remark);
		if(count($data)<>3){
				$where['error'] = '非单钻货品备注'.$remark.'，不符合格式[制单人,赠品款号,备注](3段逗号隔开)';
				return $where;				
		}
	}	

	$cate_type = 0;

	if(substr($type2,0,1)=='h'){
		$cate_type = 1;
	}elseif(substr($type2,0,1)=='b'){
		$cate_type = 2;
	}elseif(substr($type2,0,1)=='k'){
		$cate_type = 3;
	}elseif(substr($type2,0,1)=='d'){
		$cate_type = 4;
	}elseif($type=='h'){
		$cate_type = 1;
	}elseif($type=='b'){
		$cate_type = 2;
	}elseif($type=='k'){
		$cate_type = 3;
	}elseif($type=='d'){
		$cate_type = 4;
	}

    //黄金铂金类 需要获取材质 金重 指圈(戒指类)
    if( $cate_type==1 || $cate_type==2 ){    	
        if(substr($type,0,1)=='h' || substr($type2,0,1)=='h'){
            $where['caizhi'] = '足金,24K,千足金,黄金';
            $where['jinliao'] = '足金';
            $where['jinse'] = ''; 
        }    
        if(substr($type,0,1)=='b' || substr($type2,0,1)=='b'){
        	$where['caizhi'] = 'PT950';
            $where['jinliao'] = 'PT950';
            $where['jinse'] = '白';         	
        }	
        $arr = array();        
		preg_match_all("/约(.*)/", $smarkname, $arr);
		if(empty($arr[1]))
		{
			if($type2=='hy'){
                return $where;
			}else{
				$where['error'] = '黄金铂金类SKU'.$smarkname.'命名不符合规范[约2.1-2.2g..]';
				return $where;
		    }
		}

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
			$where['gold_weight_max'] = trim($gold[1]);
		}
		if(isset($data[1]) && !empty($data[1]))
		{
			//如果是戒指
			if(substr($type2,-1)=='z')
			{
				$where['shoucun'] = str_replace('#','',trim($data[1]));
			}
			//如果是项链
			if(substr($type2,-1)=='x')
			{
				//寸 目前没有啊
				//$where['shoucun'] = str_replace('寸','',$data[1]);
				$where['shoucun'] = '';
			}
		}		
      
    }

    //K金类 需要获取材质  指圈(戒指类)   ***没有金重
    if($cate_type==3){                
		$smarkname = str_replace('；',';',$smarkname);
		$smarkname = str_replace('规格:','',$smarkname);
		$data = explode(';',$smarkname);		
        $where['caizhi'] = '18K'.trim($data[0]);
        $where['jinliao'] = '18K';
        $where['jinse'] = trim($data[0]);        
		//如果是戒指
		if(substr($type2,-1)=='z')
		{
			if(empty($data[1])){
				$where['error'] = 'K金类戒指SKU'.$smarkname.'命名不符合规范[颜色;指圈号]';
				return $where;
			}
			$where['shoucun'] = str_replace('#','',trim($data[1]));
		}
		//如果是项链
		if(substr($type2,-1)=='x')
		{
			//寸 目前没有啊
			//$where['shoucun'] = str_replace('寸','',$data[1]);
			$where['shoucun'] = '';
		}
    }

   

    
    //3单钻类 需要获取材质 石重 主石颜色 净度  指圈(戒指类)
    if($cate_type==4){                
		$smarkname = str_replace('；',';',$smarkname);
		preg_match_all("/规格:(.*)/", $smarkname, $data);
		if(empty($data[1])){
			$where['error'] = '单钻类SKU:'.$smarkname.'命名不符合规范[规格:金色材质 石重 颜色 净度](不含规格：)';
			return $where;
		}
		$data = explode(' ',trim($data[1][0]));
		if(count($data)<>3){		
			$where['error'] = '单钻类SKU:'.$smarkname.'命名不符合规范[规格:金色材质 石重 颜色 净度](空格隔开3段)';
			return $where;
		}
		//材质及颜色	
		if(!empty($data[0])){	
			$pos = strpos(trim($data[0]),'18K');	
			if($pos!==false){	
			    $tmp_jinse = substr(trim($data[0]),0,$pos);
			    if($tmp_jinse=='玫瑰')
			        $tmp_jinse = '玫瑰金';		
				$where['caizhi'] = '18K'.$tmp_jinse;
		        $where['jinliao'] = '18K';
		        $where['jinse'] = substr(trim($data[0]),0,$pos);				
			}
			if(strpos(trim($data[0]),'PT950')!==false){			
				$where['caizhi'] = 'PT950';
		        $where['jinliao'] = 'PT950';
		        $where['jinse'] = '白';				
			}
			$pos = strpos(trim($data[0]),'S925');	
			if($pos!==false){			
				$where['caizhi'] = 'S925'.substr(trim($data[0]),0,$pos);
		        $where['jinliao'] = 'S925';
		        $where['jinse'] = substr(trim($data[0]),0,$pos);				
			}

		
		}	
        //石重
		if(!empty($data[1])){
            $shizhong = explode('/',$data[1]);
            $where['zuanshidaxiao'] = str_replace('分','',trim($shizhong[0])); 
            $where['zuanshidaxiao'] = bcdiv($where['zuanshidaxiao'],100,3);
            if(!empty($shizhong[1])){
                $where['zhushi_num'] = trim(str_replace('颗','',trim($shizhong[1]))); 
                if(is_numeric($where['zhushi_num']) && $where['zhushi_num']>0 && $where['zhushi_num']<100)
                    $where['zuanshidaxiao'] =  bcdiv($where['zuanshidaxiao'],$where['zhushi_num'],3);      	
            }
		}
        //颜色 净度
		if(!empty($data[2])){
            $yansejingdu = explode('/',trim($data[2]));
            //$where['zhushiyanse'] = trim($yansejingdu[0]);
            if(!empty($dzcolor[trim($yansejingdu[0])])){
            	$where['zhushiyanse_search'] = $dzcolor[trim($yansejingdu[0])];
                $where['zhushiyanse'] = trim($yansejingdu[0]);
            }else{
            	$where['zhushiyanse_search'] = "'".trim($yansejingdu[0])."'"; 
                $where['zhushiyanse'] =trim($yansejingdu[0]); 
            } 
            if(!empty($yansejingdu[1])){
            	$yansejingdu[1] = str_replace(strrchr($yansejingdu[1],'丨'),'',$yansejingdu[1]);
	            if(!empty($dzjingdu[trim($yansejingdu[1])])){
	            	$where['zhushijingdu_search'] = $dzjingdu[trim($yansejingdu[1])];
	                $where['zhushijingdu'] = trim($yansejingdu[1]);
	            }else{
	            	$where['zhushijingdu_search'] = "'".trim($yansejingdu[1])."'"; 
	                $where['zhushijingdu'] = trim($yansejingdu[1]); 
	            }           	
            }else{
                $where['zhushijingdu_search'] = "'I','I1','I2','P','P1','','不分级'";                    
            }
            	
		}		
		if(empty($where['caizhi']) || empty($where['zuanshidaxiao']) || empty($where['zhushiyanse']) ){
			$where['error'] = '单钻类SKU'.$smarkname.'命名不符合规范[规格:金色材质 石重 颜色 净度](匹配不到材质及颜色或者是石重 颜色 净度)';
			return $where;
		}       

		//如果是戒指
		if(substr($type2,-1)=='z')
		{
			$data = explode(',',$remark);
			if(count($data)<>5){
				$where['error'] = '单钻戒指备注不符合格式[制单人,指圈,刻字,赠品款号,备注](5段逗号隔开)';
				return $where;				
			}
			if(empty($data[1])){
				$where['error'] = '单钻戒指备注'.$remark.'命名不符合规范(匹配不到戒指指圈)';
				return $where;
			}else{
				$where['shoucun'] = str_replace('#','',trim($data[1]));
				$where['shoucun'] = str_replace('号','',trim($data[1]));
				$where['ziyin'] = trim($data[2]);
			}			
		}
		
    }


    return $where;      
}


?>