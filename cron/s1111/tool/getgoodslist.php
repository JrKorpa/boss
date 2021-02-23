<?php
define('ROOT_PATH',trim(str_replace('\\','/',realpath(dirname(__FILE__))),'tool/'));//定义目录
include('../taobaoapi.php');
//获取自己所有淘宝出售中商品
$count = 0;

	//定义单字母数组
	$array1 = array('h','b','k','d');
	//定义双字母数组
	$array2 = array(
		'hz','hx','hd','hy','hb','hj',
		'bz','bx','bd','br',
		'kz','kx','kd','ke',
		'dz','dd','de','dt');

$other = 0;
echo "<pre>";
for($i=1;$i<=50;$i++)
{
	$goodslist = $apiModel->getgoodsList($i);
	if(empty($goodslist))
	{
		continue;
	}
	foreach($goodslist as $v)
	{	
		
		if($v->num_iid!='9958822894'){
			//continue;
			//print_r($v);
			/*
			$skus = $apiModel->getSkus($v->num_iid);
			//print_r($res);
			foreach ($skus as $key => $sku) {
				print_r($sku);
				$res = $apiModel->getSku(trim($v->num_iid),$sku->sku_id);
			}*/
			//$product = $apiModel->getProduct($v->num_iid);
		}

        
		$newtitle = str_replace(' ','',trim($v->title));
		$type = strtolower(substr($newtitle,-1));
		$type2 = strtolower(substr($newtitle,-2));
		//echo "[".in_array($type,$array1)."]";
		if(in_array($type,$array1) || in_array($type2,$array2))
		{

			//echo $v->title.'<br/>';
			$count++;
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
            $product = $apiModel->getProduct($v->num_iid);	
            //if($v->num_iid=='9958822894'){
                //print_r($product->skus);
                $skus = $product->skus->sku;
	            foreach ($skus as $k => $sk) {
	            	if(empty($sk->outer_id)){
	                    //print_r($sk);
	            		echo $v->title ."不能获取款号信息".$v->num_iid."<br>".PHP_EOL;	
	            	}
	            }
            //}
            /*
            $skus = $product->property_alias;
            if(empty($skus)){
                echo $v->title ."不能获取SKU信息<br>".$v->num_iid.PHP_EOL;	
                continue;  
            }	
            $skus = explode(';', $skus);

   

           
            
            echo $v->outer_id."==".$v->title.$product->property_alias."::".$v->num_iid."<br>";
                      
            foreach ($skus as $sku) { 

	            $smarkname = explode(':', $sku);	
	            if(count($smarkname)<3){
	                echo $v->title ."SKU[".$product->property_alias."]别名有疑问".$v->num_iid."<br>".PHP_EOL;
	                //echo $product->property_alias;	
	                continue;  
                }	
                $smarkname = $smarkname[2];
				$smarkname = str_replace(strrchr($smarkname,'|'),'',$smarkname);
				//$smarkname = str_replace(strrchr($smarkname,'丨'),'',$smarkname);
				//转换中文分号为英文
				$smarkname = str_replace('；',';',$smarkname);
				$smarkname = str_replace('，',',',$smarkname);	
				if( $cate_type==3 ){
							//$data = explode(' ',$smarkname);
							//print_r($data);
							//if(count($data)<>3){		
							//	echo  '单钻类SKU:['.$smarkname.']命名不符合规范[规格:金色材质 石重 颜色 净度](空格隔开3段)'.$v->num_iid.'<br>';
								
							//}
                    //echo $v->title .'K金类SKU['.$smarkname.']'.$v->num_iid.'<br>';
					
					preg_match_all("/约(.*)/", $smarkname, $arr);
					if(empty($arr[1]))
					{
						if($type2=='hy'){
			                //return $where;
						}else{
							//$where['error'] = '黄金铂金类SKU'.$smarkname.'命名不符合规范[约2.1-2.2g..]';
							echo $v->title .'黄金铂金类SKU['.$smarkname.']命名不符合规范[约2.1-2.2g..]'.$v->num_iid.'<br>'.PHP_EOL;	
					    }
					}
				}
		    }
		    */

		}else{
			
			//echo $newtitle.'<br/>';
			//echo $v->title.'<br/>';
			$other++;	
		}
	}
}
echo '满足条件的有'.$count.'个<br/>';
echo '不满足条件的有'.$other.'个<br/>';

?>